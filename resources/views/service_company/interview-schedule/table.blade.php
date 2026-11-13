{{--
  JERO ATS — Service Company Interview Tracker (Rule 20 Modern Card + Sub-Card Design System)
  Intent: Provides an enterprise-grade, page-isolated Interview Tracker powered by Elasticsearch
          read projections (0% server table HTML overhead), 3-tier RAM/IndexedDB/Redis caching,
          and real-time Reverb synchronization.
  Tenancy & Security: Strictly scoped by auth()->user()->company_id across IndexedDB, Redis, and Reverb.
  Screen Fit: 100% fluid fit on 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow.
--}}
@extends('layouts.service_company_layout')

@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
/* ==========================================================================
   Interview Tracker — Page-Isolated Rule 20 Modern Card + Sub-Card Design
   ========================================================================== */
:root {
    --it-primary: #009ef7;
    --it-primary-deep: #2563eb;
    --it-primary-soft: #eff6ff;
    --it-success: #10b981;
    --it-success-soft: #ecfdf5;
    --it-warning: #f59e0b;
    --it-warning-soft: #fffbeb;
    --it-danger: #ef4444;
    --it-danger-soft: #fef2f2;
    --it-purple: #7c3aed;
    --it-purple-soft: #f5f3ff;
    --it-text-main: #0f172a;
    --it-text-mid: #475569;
    --it-text-muted: #64748b;
    --it-text-light: #94a3b8;
    --it-border: #e2e8f0;
    --it-border-soft: #f1f5f9;
    --it-bg-subcard: #f8fafc;
}

/* Remove legacy toolbar gap & guarantee 0% horizontal page scroll */
#kt_toolbar { display: none !important; }
#kt_content { margin-top: 0 !important; }
#kt_post { margin-top: 0 !important; }
#kt_content_container {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

/* Apply Poppins across UI text while NEVER overriding icon fonts (Font Awesome / Tabler / Bootstrap Icons) */
.it-dashboard,
.it-dashboard *:not(i):not(.fa):not(.fas):not(.far):not(.fab):not(.fal):not(.fad):not(.fa-solid):not(.fa-regular):not(.fa-brands):not([class*="fa-"]):not(.ti):not([class*="ti-"]):not(.bi):not([class*="bi-"]) {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

.it-dashboard i,
.it-dashboard [class*="fa-"],
.it-dashboard [class*="ti-"],
.it-dashboard [class*="bi-"] {
    box-sizing: border-box !important;
}

.it-dashboard .fa,
.it-dashboard .fas,
.it-dashboard .fa-solid {
    font-family: "Font Awesome 6 Free", "FontAwesome" !important;
    font-weight: 900 !important;
    font-style: normal !important;
    -webkit-font-smoothing: antialiased;
}

.it-dashboard .far,
.it-dashboard .fa-regular {
    font-family: "Font Awesome 6 Free", "FontAwesome" !important;
    font-weight: 400 !important;
    font-style: normal !important;
    -webkit-font-smoothing: antialiased;
}

.it-dashboard .fab,
.it-dashboard .fa-brands {
    font-family: "Font Awesome 6 Brands" !important;
    font-weight: 400 !important;
    font-style: normal !important;
}

.it-dashboard {
    width: 100% !important;
    max-width: 100% !important;
    color: var(--it-text-main);
    -webkit-font-smoothing: antialiased;
}

.it-dashboard ::-webkit-scrollbar { width: 5px; height: 5px; }
.it-dashboard ::-webkit-scrollbar-track { background: transparent; }
.it-dashboard ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.it-dashboard ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* Legacy drawer sizing kept intact */
@media only screen and (min-width: 1024px) and (max-width: 1440px) {
    #client_stage_sub_stage, #kt_client_activitiesApplication { width: 1025px; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px) {
    #client_stage_sub_stage, #kt_client_activitiesApplication { width: 1200px; }
}

/* ==========================================================================
   TIER 1: Pure White Top Header Toolbar Card (Client Tracker Design)
   ========================================================================== */
.it-header-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 14px 20px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 16px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.it-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.it-header-icon {
    width: 44px !important;
    height: 44px !important;
    border-radius: 10px !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #2563eb !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.06) !important;
}

.it-header-title-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}

.it-header-title {
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
    letter-spacing: -0.015em !important;
    margin: 0 !important;
}

.it-header-count-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 2px 9px !important;
    border-radius: 999px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    background: var(--it-primary-soft) !important;
    color: var(--it-primary-deep) !important;
    border: 1px solid #bfdbfe !important;
    font-variant-numeric: tabular-nums !important;
    line-height: 1.3 !important;
}

.it-header-sub {
    font-size: 12px !important;
    color: #64748b !important;
    margin-top: 2px !important;
    line-height: 1.25 !important;
}

.it-header-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

.it-refresh-btn {
    height: 38px !important;
    padding: 0 15px !important;
    border-radius: 9px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #0f172a !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 7px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
}

.it-refresh-btn:hover {
    background: #f8fafc !important;
    border-color: var(--it-primary) !important;
    color: var(--it-primary) !important;
}

.it-refresh-btn.is-syncing svg {
    animation: itSpin 0.8s linear infinite;
}

@keyframes itSpin {
    to { transform: rotate(360deg); }
}

/* ==========================================================================
   TIER 2: Executive KPI Metric Strip (Copied from Client Tracker .cl-stat-grid)
   ========================================================================== */
.it-stat-grid {
    display: grid !important;
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    gap: 8px !important;
    margin-bottom: 14px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

.it-stat-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 11px !important;
    padding: 11px 13px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease !important;
    min-width: 0 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    text-align: left !important;
    outline: none !important;
}

.it-stat-card:hover {
    transform: translateY(-1px) !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06) !important;
    text-decoration: none !important;
}

.it-stat-top {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    margin-bottom: 6px !important;
    min-width: 0 !important;
    width: 100% !important;
}

.it-stat-icon {
    width: 36px !important;
    height: 36px !important;
    min-width: 36px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    transition: all 0.15s ease !important;
}

.it-stat-icon svg {
    width: 18px !important;
    height: 18px !important;
}

.it-stat-icon.blue   { background: #eff6ff !important; color: #2563eb !important; }
.it-stat-icon.green  { background: #ecfdf5 !important; color: #16a34a !important; }
.it-stat-icon.purple { background: #faf5ff !important; color: #9333ea !important; }
.it-stat-icon.pink   { background: #fdf2f8 !important; color: #db2777 !important; }
.it-stat-icon.red    { background: #fef2f2 !important; color: #dc2626 !important; }
.it-stat-icon.amber  { background: #fef3c7 !important; color: #d97706 !important; }

.it-stat-meta {
    min-width: 0 !important;
    flex: 1 !important;
    overflow: hidden !important;
}

.it-stat-num-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 6px !important;
}

.it-stat-num {
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.1 !important;
    white-space: nowrap !important;
    font-variant-numeric: tabular-nums !important;
}

.it-stat-lbl {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

.it-stat-sub {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10.5px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-top: 2px !important;
    width: 100% !important;
}

.it-stat-sub.blue   { color: #3b82f6 !important; }
.it-stat-sub.green  { color: #16a34a !important; }
.it-stat-sub.purple { color: #9333ea !important; }
.it-stat-sub.pink   { color: #db2777 !important; }
.it-stat-sub.red    { color: #dc2626 !important; }
.it-stat-sub.amber  { color: #d97706 !important; }
.it-stat-sub svg    { flex-shrink: 0 !important; width: 12px !important; height: 12px !important; }

.it-kpi-pill-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 2px 7px !important;
    background: #fef2f2 !important;
    border: 1px solid #fecaca !important;
    border-radius: 9999px !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #dc2626 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
}

.it-kpi-dot {
    width: 5px !important;
    height: 5px !important;
    border-radius: 50% !important;
    background: #ef4444 !important;
    display: inline-block !important;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.18) !important;
}

/* KPI Selected States (Color-matched exactly like Client Tracker .cl-card.active) */
.it-stat-card.it-kpi-active[data-kpi-filter="all"],
.it-stat-card.it-kpi-active[data-kpi-tab="all"] {
    background: #eff6ff !important;
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 1.5px #2563eb, 0 4px 14px rgba(37, 99, 235, 0.12) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="all"] .it-stat-icon,
.it-stat-card.it-kpi-active[data-kpi-tab="all"] .it-stat-icon {
    background: #ffffff !important;
    color: #2563eb !important;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.15) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="all"] .it-stat-num,
.it-stat-card.it-kpi-active[data-kpi-tab="all"] .it-stat-num { color: #1d4ed8 !important; }
.it-stat-card.it-kpi-active[data-kpi-filter="all"] .it-stat-lbl,
.it-stat-card.it-kpi-active[data-kpi-tab="all"] .it-stat-lbl { color: #1e40af !important; font-weight: 700 !important; }

.it-stat-card.it-kpi-active[data-kpi-filter="this_month"],
.it-stat-card.it-kpi-active[data-kpi-tab="this_month"] {
    background: #eff6ff !important;
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 1.5px #2563eb, 0 4px 14px rgba(37, 99, 235, 0.12) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="this_month"] .it-stat-icon,
.it-stat-card.it-kpi-active[data-kpi-tab="this_month"] .it-stat-icon {
    background: #ffffff !important;
    color: #2563eb !important;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.15) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="this_month"] .it-stat-num,
.it-stat-card.it-kpi-active[data-kpi-tab="this_month"] .it-stat-num { color: #1d4ed8 !important; }
.it-stat-card.it-kpi-active[data-kpi-filter="this_month"] .it-stat-lbl,
.it-stat-card.it-kpi-active[data-kpi-tab="this_month"] .it-stat-lbl { color: #1e40af !important; font-weight: 700 !important; }

.it-stat-card.it-kpi-active[data-kpi-filter="today"],
.it-stat-card.it-kpi-active[data-kpi-tab="today"] {
    background: #ecfdf5 !important;
    border-color: #34d399 !important;
    box-shadow: 0 0 0 1.5px #059669, 0 4px 14px rgba(5, 150, 105, 0.12) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="today"] .it-stat-icon,
.it-stat-card.it-kpi-active[data-kpi-tab="today"] .it-stat-icon {
    background: #ffffff !important;
    color: #059669 !important;
    box-shadow: 0 1px 3px rgba(5, 150, 105, 0.15) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="today"] .it-stat-num,
.it-stat-card.it-kpi-active[data-kpi-tab="today"] .it-stat-num { color: #065f46 !important; }
.it-stat-card.it-kpi-active[data-kpi-filter="today"] .it-stat-lbl,
.it-stat-card.it-kpi-active[data-kpi-tab="today"] .it-stat-lbl { color: #047857 !important; font-weight: 700 !important; }

.it-stat-card.it-kpi-active[data-kpi-filter="upcoming"],
.it-stat-card.it-kpi-active[data-kpi-tab="upcoming"] {
    background: #faf5ff !important;
    border-color: #c084fc !important;
    box-shadow: 0 0 0 1.5px #9333ea, 0 4px 14px rgba(147, 51, 234, 0.12) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="upcoming"] .it-stat-icon,
.it-stat-card.it-kpi-active[data-kpi-tab="upcoming"] .it-stat-icon {
    background: #ffffff !important;
    color: #9333ea !important;
    box-shadow: 0 1px 3px rgba(147, 51, 234, 0.15) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="upcoming"] .it-stat-num,
.it-stat-card.it-kpi-active[data-kpi-tab="upcoming"] .it-stat-num { color: #6b21a8 !important; }
.it-stat-card.it-kpi-active[data-kpi-filter="upcoming"] .it-stat-lbl,
.it-stat-card.it-kpi-active[data-kpi-tab="upcoming"] .it-stat-lbl { color: #7e22ce !important; font-weight: 700 !important; }

.it-stat-card.it-kpi-active[data-kpi-filter="pending_feedback"],
.it-stat-card.it-kpi-active[data-kpi-tab="pending_feedback"] {
    background: #fef2f2 !important;
    border-color: #f87171 !important;
    box-shadow: 0 0 0 1.5px #dc2626, 0 4px 14px rgba(220, 38, 38, 0.14) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="pending_feedback"] .it-stat-icon,
.it-stat-card.it-kpi-active[data-kpi-tab="pending_feedback"] .it-stat-icon {
    background: #ffffff !important;
    color: #dc2626 !important;
    box-shadow: 0 1px 3px rgba(220, 38, 38, 0.16) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="pending_feedback"] .it-stat-num,
.it-stat-card.it-kpi-active[data-kpi-tab="pending_feedback"] .it-stat-num { color: #991b1b !important; }
.it-stat-card.it-kpi-active[data-kpi-filter="pending_feedback"] .it-stat-lbl,
.it-stat-card.it-kpi-active[data-kpi-tab="pending_feedback"] .it-stat-lbl { color: #b91c1c !important; font-weight: 700 !important; }

/* Standardized Organization Metric Active States */
.it-stat-card.it-kpi-active[data-kpi-filter="direct_client"] {
    background: #ecfdf5 !important;
    border-color: #34d399 !important;
    box-shadow: 0 0 0 1.5px #059669, 0 4px 14px rgba(5, 150, 105, 0.12) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="direct_client"] .it-stat-icon {
    background: #ffffff !important;
    color: #059669 !important;
    box-shadow: 0 1px 3px rgba(5, 150, 105, 0.15) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="direct_client"] .it-stat-num { color: #065f46 !important; }
.it-stat-card.it-kpi-active[data-kpi-filter="direct_client"] .it-stat-lbl { color: #047857 !important; font-weight: 700 !important; }

.it-stat-card.it-kpi-active[data-kpi-filter="internal_client"] {
    background: #eff6ff !important;
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 1.5px #2563eb, 0 4px 14px rgba(37, 99, 235, 0.12) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="internal_client"] .it-stat-icon {
    background: #ffffff !important;
    color: #2563eb !important;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.15) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="internal_client"] .it-stat-num { color: #1d4ed8 !important; }
.it-stat-card.it-kpi-active[data-kpi-filter="internal_client"] .it-stat-lbl { color: #1e40af !important; font-weight: 700 !important; }

.it-stat-card.it-kpi-active[data-kpi-filter="my_organization"] {
    background: #fffbeb !important;
    border-color: #fbbf24 !important;
    box-shadow: 0 0 0 1.5px #d97706, 0 4px 14px rgba(217, 119, 6, 0.14) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="my_organization"] .it-stat-icon {
    background: #ffffff !important;
    color: #d97706 !important;
    box-shadow: 0 1px 3px rgba(217, 119, 6, 0.16) !important;
}
.it-stat-card.it-kpi-active[data-kpi-filter="my_organization"] .it-stat-num { color: #78350f !important; }
.it-stat-card.it-kpi-active[data-kpi-filter="my_organization"] .it-stat-lbl { color: #92400e !important; font-weight: 700 !important; }

/* ==========================================================================
   TIER 3: Unified Single White Card — Search, Mode, Sort & Reset (Client Tracker UX)
   ========================================================================== */
.it-toolbar-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 10px 14px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    width: 100% !important;
    overflow: visible !important;
}

.it-filter-bar {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

/* Enterprise Micro-Search Box (Copied from Client Tracker .cl-search-box) */
.it-search-box {
    position: relative !important;
    flex: 1 1 280px !important;
    min-width: 240px !important;
}

.it-search-box svg.it-search-icon {
    position: absolute !important;
    left: 12px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 15px !important;
    height: 15px !important;
    color: #94a3b8 !important;
    pointer-events: none !important;
}

.it-search-inp {
    width: 100% !important;
    height: 42px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 0 82px 0 38px !important;
    font-size: 12.5px !important;
    font-weight: 400 !important;
    color: #0f172a !important;
    background: #ffffff !important;
    outline: none !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}

.it-search-inp:focus {
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 2.5px rgba(0, 158, 247, 0.15) !important;
}

.it-search-inp::placeholder {
    color: #94a3b8 !important;
}

.it-search-right-affix {
    position: absolute !important;
    right: 10px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}

.it-search-count-pill {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    background: #eff6ff !important;
    color: #009ef7 !important;
    border: 1px solid #dbeafe !important;
    padding: 2px 7px !important;
    border-radius: 6px !important;
    white-space: nowrap !important;
    line-height: 1.2 !important;
    font-variant-numeric: tabular-nums !important;
}


.it-search-kbd-hint {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    color: #94a3b8 !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 4px !important;
    padding: 2px 5px !important;
    line-height: 1 !important;
    user-select: none !important;
}

/* Shared Combobox Pill Trigger (Copied from Client Tracker .cl-act / .cl-sort-combobox-btn) */
.it-company-type-wrapper,
.it-mode-dropdown-wrapper,
.it-sort-dropdown-wrapper {
    position: relative !important;
    display: inline-block !important;
    flex-shrink: 0 !important;
}

.it-combobox-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    height: 42px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 0 12px !important;
    min-width: 155px !important;
    max-width: 240px !important;
    cursor: pointer !important;
    user-select: none !important;
    transition: all 0.15s ease !important;
    position: relative !important;
    text-align: left !important;
    outline: none !important;
}

.it-combobox-btn:focus-visible {
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.15) !important;
}

.it-combobox-btn:hover,
.it-combobox-btn.active {
    border-color: #cbd5e1 !important;
    background: #f8fafc !important;
}

.it-combobox-btn.is-active-filter {
    border-color: #009ef7 !important;
    background: #f0f9ff !important;
}

.it-combobox-btn.is-active-filter .it-pill-val,
.it-combobox-btn.is-active-filter .it-pill-icon {
    color: #009ef7 !important;
    font-weight: 600 !important;
}

.it-combobox-btn.is-custom-sorted {
    border-color: #cbd5e1 !important;
    background: #f8fafc !important;
}

.it-combobox-btn.is-custom-sorted .it-pill-val {
    color: #0f172a !important;
    font-weight: 600 !important;
}

.it-pill-icon {
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}

.it-pill-texts {
    display: flex !important;
    flex-direction: column !important;
    line-height: 1.15 !important;
    flex: 1 !important;
    min-width: 0 !important;
}

.it-pill-lbl {
    font-size: 10px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    text-transform: capitalize !important;
}

.it-pill-val {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.it-pill-clear {
    display: none !important;
}

.it-pill-chevron {
    color: #94a3b8 !important;
    display: flex !important;
    align-items: center !important;
    flex-shrink: 0 !important;
    margin-left: 2px !important;
}

/* Enterprise WAI-ARIA Popover Panels (Copied from Client Tracker .cl-act / .cl-sort-dropdown-panel) */
.it-dropdown-panel {
    position: absolute !important;
    top: calc(100% + 6px) !important;
    right: 0 !important;
    width: 275px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06) !important;
    z-index: 1055 !important;
    display: none !important;
    flex-direction: column !important;
    overflow: hidden !important;
    padding: 6px 0 !important;
    animation: itPopoverFadeIn 0.15s ease-out !important;
}

.it-sort-dropdown-panel {
    width: 290px !important;
}

@keyframes itPopoverFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

.it-dropdown-panel.show {
    display: flex !important;
}

.it-pop-section-hdr {
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    color: #94a3b8 !important;
    padding: 6px 14px 4px 14px !important;
    user-select: none !important;
}

.it-pop-section-hdr:not(:first-child) {
    border-top: 1px solid #f1f5f9 !important;
    margin-top: 4px !important;
    padding-top: 8px !important;
}

.it-pop-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 8px 14px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    cursor: pointer !important;
    transition: all 0.12s ease !important;
    user-select: none !important;
    outline: none !important;
    gap: 8px !important;
}

.it-pop-item:hover,
.it-pop-item:focus {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}

.it-pop-item.selected {
    background: #eff6ff !important;
    color: #009ef7 !important;
    font-weight: 600 !important;
}

.it-pop-item-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
    flex: 1 !important;
}

.it-pop-item-icon {
    color: #64748b !important;
    flex-shrink: 0 !important;
}

.it-pop-item:hover .it-pop-item-icon,
.it-pop-item.selected .it-pop-item-icon {
    color: #009ef7 !important;
}

.it-pop-item-title {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.it-pop-item-right {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
}

.it-pop-badge {
    font-size: 10px !important;
    font-weight: 700 !important;
    padding: 1.5px 6.5px !important;
    border-radius: 10px !important;
    background: #f1f5f9 !important;
    color: #64748b !important;
}

.it-pop-item.selected .it-pop-badge {
    background: #dbeafe !important;
    color: #0284c7 !important;
}

.it-pop-check {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #009ef7 !important;
    display: inline-flex !important;
    align-items: center !important;
    width: 14px !important;
    justify-content: center !important;
}

.it-dot {
    width: 7px !important;
    height: 7px !important;
    border-radius: 50% !important;
    display: inline-block !important;
    flex-shrink: 0 !important;
}
.it-dot-all      { background: #64748b !important; }
.it-dot-blue     { background: #3b82f6 !important; }
.it-dot-purple   { background: #8b5cf6 !important; }
.it-dot-green    { background: #10b981 !important; }
.it-dot-direct   { background: #059669 !important; box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.2) !important; }
.it-dot-internal { background: #2563eb !important; box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2) !important; }
.it-dot-myorg    { background: #0891b2 !important; box-shadow: 0 0 0 2px rgba(8, 145, 178, 0.2) !important; }

.it-pop-badge.direct-badge {
    background: #ecfdf5 !important;
    color: #059669 !important;
    border: 1px solid #a7f3d0 !important;
}
.it-pop-badge.internal-badge {
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
}
.it-pop-badge.myorg-badge {
    background: #ecfeff !important;
    color: #0891b2 !important;
    border: 1px solid #a5f3fc !important;
}

/* Reset Button (Unified with 42px Toolbar Pills) */
.it-reset-btn {
    height: 42px !important;
    padding: 0 14px !important;
    border-radius: 10px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #475569 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}

.it-reset-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

.it-reset-btn.has-active-filters {
    border-color: #bfdbfe !important;
    background: #eff6ff !important;
    color: #2563eb !important;
}

.it-reset-count-badge {
    font-size: 10px !important;
    font-weight: 700 !important;
    padding: 1px 6px !important;
    border-radius: 999px !important;
    background: #2563eb !important;
    color: #ffffff !important;
    line-height: 1.3 !important;
}


/* ==========================================================================
   TIER 4: Master Primary Interview Cards & Discrete Sub-Cards Stream
   ========================================================================== */
.it-stream-wrapper {
    position: relative !important;
    width: 100% !important;
    min-height: 160px !important;
}

.it-cards-stream {
    width: 100% !important;
    max-width: 100% !important;
}

.it-card {
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
    border-radius: 10px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: visible !important;
}

.it-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
}

/* Upper Discrete Sub-Cards Grid (6 Responsive Fractional Columns) */
.it-card-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.9fr) minmax(0, 1.32fr) minmax(0, 1.2fr) minmax(0, 1.5fr) minmax(0, 1.36fr) !important;
    gap: 8px !important;
    padding: 10px 12px !important;
    width: 100% !important;
    align-items: stretch !important;
}

.it-subcard {
    background: var(--it-bg-subcard) !important;
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

.it-subcard-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 4px !important;
    min-height: 18px !important;
}

.it-subcard-label {
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.35px !important;
    color: var(--it-text-muted) !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
    line-height: 1.3 !important;
}

.it-subcard-label i {
    color: var(--it-text-light) !important;
    font-size: 9.5px !important;
    flex-shrink: 0 !important;
}

/* Sub-Card 1: Candidate + Follow-up */
.it-cand-body {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    min-width: 0 !important;
}

.it-avatar {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    text-transform: uppercase !important;
    border: 1px solid rgba(15, 23, 42, 0.06) !important;
}

.it-avatar-red    { background: #fee2e2 !important; color: #dc2626 !important; }
.it-avatar-orange { background: #fef3c7 !important; color: #d97706 !important; }
.it-avatar-blue   { background: #dbeafe !important; color: #2563eb !important; }
.it-avatar-green  { background: #dcfce7 !important; color: #16a34a !important; }
.it-avatar-purple { background: #f3e8ff !important; color: #7c3aed !important; }
.it-avatar-gray   { background: #f1f5f9 !important; color: #475569 !important; }

.it-cand-text {
    flex: 1 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.it-cand-name {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: var(--it-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
    line-height: 1.3 !important;
    text-decoration: none !important;
    cursor: pointer !important;
}

.it-cand-name:hover {
    color: var(--it-primary) !important;
}

.it-cand-role {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: var(--it-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
    margin-top: 1px !important;
    cursor: pointer !important;
}

.it-cand-submeta {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10.5px !important;
    color: var(--it-text-muted) !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.it-cand-submeta i {
    font-size: 9.5px !important;
    color: var(--it-text-light) !important;
    flex-shrink: 0 !important;
}

.it-cand-submeta span {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Follow-up Pill Dropdown */
.it-followup-dropdown {
    position: relative !important;
    display: inline-block !important;
    flex-shrink: 0 !important;
}

.it-followup-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 2px 7px !important;
    border-radius: 5px !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    border: 1px solid var(--it-border) !important;
    background: #ffffff !important;
    color: var(--it-text-mid) !important;
    cursor: pointer !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    transition: all 0.15s ease !important;
}

.it-followup-btn:hover {
    border-color: var(--it-primary) !important;
    color: var(--it-primary) !important;
}

.it-followup-btn.is-confirmed {
    color: #15803d !important;
    border-color: #bbf7d0 !important;
    background: #f0fdf4 !important;
}

.it-followup-btn.is-no-response {
    color: #dc2626 !important;
    border-color: #fecaca !important;
    background: #fef2f2 !important;
}

.it-followup-btn.is-rescheduled {
    color: #2563eb !important;
    border-color: #bfdbfe !important;
    background: #eff6ff !important;
}

.it-fu-caret {
    font-size: 8px !important;
    opacity: 0.75 !important;
}

.it-followup-menu {
    position: absolute !important;
    top: calc(100% + 4px) !important;
    left: 0 !important;
    right: auto !important;
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15) !important;
    z-index: 1050 !important;
    display: none !important;
    min-width: 145px !important;
    padding: 4px !important;
}

.it-followup-menu.open {
    display: block !important;
}

.it-followup-menu.it-menu-up {
    top: auto !important;
    bottom: calc(100% + 4px) !important;
}

.it-followup-item {
    padding: 6px 9px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    gap: 7px !important;
    color: var(--it-text-main) !important;
}

.it-followup-item:hover {
    background: #f1f5f9 !important;
}

/* Sub-Card 2: Requisition */
.it-scope-badge {
    font-size: 8px !important;
    font-weight: 700 !important;
    padding: 1.5px 5.5px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 2.5px !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
    line-height: 1.3 !important;
}

/* Standardized AppTracker Organization Classification Colors */
.it-scope-myorg {
    background: #fef3c7 !important;
    color: #92400e !important;
    border: 1px solid #fcd34d !important;
}

.it-scope-internal {
    background: #dbeafe !important;
    color: #1e40af !important;
    border: 1px solid #93c5fd !important;
}

.it-scope-direct {
    background: #d1fae5 !important;
    color: #166534 !important;
    border: 1px solid #6ee7b7 !important;
}

.it-job-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
    min-width: 0 !important;
}

.it-job-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: var(--it-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
    line-height: 1.3 !important;
    text-decoration: none !important;
}

.it-job-title:hover {
    color: var(--it-primary) !important;
}

.it-job-meta {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 10.5px !important;
    color: var(--it-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    min-width: 0 !important;
}

.it-job-id {
    font-weight: 700 !important;
    color: #0284c7 !important;
    text-decoration: none !important;
    flex-shrink: 0 !important;
    font-variant-numeric: tabular-nums !important;
}

.it-job-id:hover {
    text-decoration: underline !important;
}

.it-job-client {
    font-weight: 600 !important;
    color: var(--it-text-mid) !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    text-transform: uppercase !important;
    font-size: 10px !important;
}

.it-job-pills {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    margin-top: 2px !important;
    flex-wrap: wrap !important;
}

.it-emp-pill {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    border-radius: 5px !important;
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
    color: var(--it-text-mid) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}

.it-emp-pill i {
    font-size: 9px !important;
    color: var(--it-text-muted) !important;
}

.it-resched-tag {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #2563eb !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    white-space: nowrap !important;
}

.it-dot-sep {
    color: #cbd5e1 !important;
    font-size: 9px !important;
    flex-shrink: 0 !important;
}

/* Sub-Card 3: Schedule & Link */
.it-mode-pill {
    font-size: 9px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}

.it-mode-video   { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.it-mode-telecon { background: #f5f3ff !important; color: #7c3aed !important; border: 1px solid #ddd6fe !important; }
.it-mode-f2f     { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
.it-mode-other   { background: #f1f5f9 !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; }

.it-sched-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 3px !important;
    min-width: 0 !important;
}

.it-sched-date-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 6px !important;
}

.it-sched-date {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: var(--it-text-main) !important;
    font-variant-numeric: tabular-nums !important;
    white-space: nowrap !important;
}

.it-sched-date i {
    color: var(--it-primary) !important;
    font-size: 11px !important;
}


.it-sched-time-row {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    color: var(--it-text-mid) !important;
    font-variant-numeric: tabular-nums !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.it-sched-time-row i {
    color: var(--it-text-light) !important;
    font-size: 11px !important;
}

.it-link-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #0284c7 !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2.5px 8px !important;
    border-radius: 5px !important;
    text-decoration: none !important;
    width: fit-content !important;
    margin-top: 2px !important;
    transition: all 0.15s ease !important;
    cursor: pointer !important;
}

.it-link-pill:hover {
    background: var(--it-primary) !important;
    border-color: var(--it-primary) !important;
    color: #ffffff !important;
}

.it-no-link-note {
    font-size: 10px !important;
    color: var(--it-text-light) !important;
    margin-top: 2px !important;
}

/* Sub-Card 4: Interview Panel */
.it-panel-count {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
    white-space: nowrap !important;
}

.it-panel-count.is-empty {
    background: #f1f5f9 !important;
    color: var(--it-text-light) !important;
    border-color: var(--it-border) !important;
}

.it-panel-empty {
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    gap: 1px !important;
    min-height: 44px !important;
}

.it-empty-dash {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: var(--it-text-light) !important;
    line-height: 1.1 !important;
}

.it-empty-sub {
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: var(--it-text-light) !important;
}

.it-panel-single {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
}

.it-panel-avatar {
    width: 30px !important;
    height: 30px !important;
    min-width: 30px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    border: 1.5px solid #ffffff !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08) !important;
}

.it-panel-multi {
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
    min-width: 0 !important;
    position: relative !important;
    cursor: pointer !important;
}

.it-panel-stack {
    display: flex !important;
    align-items: center !important;
}

.it-panel-avatar-sm {
    width: 26px !important;
    height: 26px !important;
    min-width: 26px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    border: 2px solid #ffffff !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1) !important;
    margin-left: -7px !important;
}

.it-panel-avatar-sm:first-child { margin-left: 0 !important; }
.it-panel-avatar-extra { background: #dc2626 !important; color: #ffffff !important; font-size: 9.5px !important; }

.it-panel-meta {
    min-width: 0 !important;
    flex: 1 !important;
    overflow: hidden !important;
}

.it-panel-name {
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: var(--it-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.it-panel-more {
    color: #0284c7 !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
}

.it-panel-company {
    font-size: 10px !important;
    font-weight: 500 !important;
    color: var(--it-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-top: 1px !important;
}

.it-panel-popover {
    display: none !important;
    position: absolute !important;
    bottom: calc(100% + 8px) !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    background: #0f172a !important;
    color: #ffffff !important;
    border-radius: 8px !important;
    padding: 10px 12px !important;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.35) !important;
    z-index: 90 !important;
    min-width: 200px !important;
    max-width: 260px !important;
    pointer-events: none !important;
}

.it-panel-multi:hover .it-panel-popover {
    display: block !important;
}

.it-panel-popover-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    padding-bottom: 5px !important;
    margin-bottom: 5px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.14) !important;
}

.it-panel-popover-row {
    display: flex !important;
    align-items: center !important;
    gap: 7px !important;
    padding: 3px 0 !important;
    font-size: 10.5px !important;
}

.it-panel-pop-dot {
    width: 20px !important;
    height: 20px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    flex-shrink: 0 !important;
}

.it-panel-pop-name {
    flex: 1 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.it-panel-lead-tag {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1px 5px !important;
    border-radius: 4px !important;
    background: rgba(56, 189, 248, 0.2) !important;
    color: #38bdf8 !important;
}

/* Sub-Card 5: Ownership & Recruiter (Matches AppTracker .aptk-subcard-owner) */
.it-owner-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
    min-width: 0 !important;
}

.it-owner-name {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: var(--it-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.it-owner-name span {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.it-owner-name i {
    color: var(--it-primary) !important;
    font-size: 11.5px !important;
    flex-shrink: 0 !important;
}

.it-owner-company {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: var(--it-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
    margin-top: 1px !important;
}

.it-owner-company span {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.it-owner-company i {
    font-size: 9.5px !important;
    color: var(--it-text-light) !important;
    flex-shrink: 0 !important;
}

.it-owner-submitted {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 10px !important;
    color: var(--it-text-light) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
    margin-top: 1px !important;
}

.it-owner-submitted span {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.it-owner-submitted i {
    font-size: 9.5px !important;
    color: var(--it-text-light) !important;
    flex-shrink: 0 !important;
}

/* Sub-Card 6: Evaluation & Feedback */
.it-eval-pill {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}

.it-eval-attended    { background: #eff6ff !important; color: #0284c7 !important; border: 1px solid #bae6fd !important; }
.it-eval-scheduled   { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
.it-eval-rescheduled { background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fde68a !important; }
.it-eval-noshow      { background: #fee2e2 !important; color: #dc2626 !important; border: 1px solid #fecaca !important; }
.it-eval-pending     { background: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a !important; }

.it-feedback-cell {
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
    min-width: 0 !important;
}

.it-fb-status-row {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;
}

.it-fb-badges-row {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-wrap: wrap !important;
}

.it-rec-pill {
    display: inline-flex !important;
    align-items: center !important;
    padding: 1.5px 7px !important;
    border-radius: 999px !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    white-space: nowrap !important;
}

.it-star-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    color: #d97706 !important;
    background: #fffbeb !important;
    border: 1px solid #fde68a !important;
    padding: 1px 6px !important;
    border-radius: 999px !important;
    font-variant-numeric: tabular-nums !important;
}

.it-feedback-btn {
    width: fit-content !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    transition: all 0.15s ease !important;
    border: 1px solid transparent !important;
    margin-top: 2px !important;
}

.it-fb-btn-add {
    background: var(--it-primary-deep) !important;
    color: #ffffff !important;
    border-color: var(--it-primary-deep) !important;
}

.it-fb-btn-add i,
.it-fb-btn-add svg,
.it-fb-btn-add .fa-plus,
.it-fb-btn-add [class*="fa-"] {
    color: #ffffff !important;
    fill: #ffffff !important;
}

.it-fb-btn-add:hover {
    background: #1d4ed8 !important;
}

.it-fb-btn-add:hover i,
.it-fb-btn-add:hover svg,
.it-fb-btn-add:hover .fa-plus {
    color: #ffffff !important;
}

.it-fb-btn-view {
    background: #ffffff !important;
    color: #059669 !important;
    border-color: #a7f3d0 !important;
}

.it-fb-btn-view:hover {
    background: #ecfdf5 !important;
}

.it-fb-btn-neutral {
    background: #ffffff !important;
    color: var(--it-text-mid) !important;
    border-color: var(--it-border) !important;
}

.it-fb-btn-neutral:hover {
    background: #f1f5f9 !important;
}

.it-fb-locked-text {
    font-size: 10px !important;
    color: var(--it-text-light) !important;
    font-weight: 500 !important;
}

/* ==========================================================================
   Sub-Card 6: Bottom Activity & Pipeline Strip
   ========================================================================== */
.it-card-bottom {
    background: #ffffff !important;
    border-top: 1px solid var(--it-border-soft) !important;
    padding: 7px 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
    border-bottom-left-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
}

.it-bottom-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    min-width: 0 !important;
    font-size: 10.5px !important;
    color: var(--it-text-muted) !important;
}

.it-bottom-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    min-width: 0 !important;
}

.it-bottom-lbl {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.25px !important;
    color: var(--it-text-light) !important;
}

.it-bottom-strong {
    color: var(--it-text-main) !important;
    font-weight: 600 !important;
}

.it-bottom-muted {
    color: var(--it-text-muted) !important;
    font-weight: 500 !important;
}

/* Canonical Pipeline Stage Badges */
.it-stage-badge {
    display: inline-flex !important;
    align-items: center !important;
    padding: 1.5px 8px !important;
    border-radius: 4px !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    white-space: nowrap !important;
}

.it-stage-inw { background: #fef9c3 !important; color: #854d0e !important; border: 1px solid #fde047 !important; }
.it-stage-sou { background: #f1f5f9 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important; }
.it-stage-scr { background: #f3e8ff !important; color: #6b21a8 !important; border: 1px solid #d8b4fe !important; }
.it-stage-eva { background: #eff6ff !important; color: #1e40af !important; border: 1px solid #bfdbfe !important; }
.it-stage-neg { background: #fce7f3 !important; color: #9d174d !important; border: 1px solid #fbcfe8 !important; }
.it-stage-off { background: #ffedd5 !important; color: #9a3412 !important; border: 1px solid #fed7aa !important; }
.it-stage-joi { background: #dcfce7 !important; color: #15803d !important; border: 1px solid #bbf7d0 !important; }
.it-stage-rej { background: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fecaca !important; }

.it-substage-pill {
    display: inline-flex !important;
    align-items: center !important;
    padding: 1.5px 7px !important;
    border-radius: 4px !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    background: #f8fafc !important;
    color: var(--it-text-mid) !important;
    border: 1px solid var(--it-border) !important;
    white-space: nowrap !important;
}

.it-bottom-right {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-shrink: 0 !important;
}

.it-action-ghost {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    background: #ffffff !important;
    color: var(--it-text-mid) !important;
    border: 1px solid var(--it-border) !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.it-action-ghost:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: var(--it-text-main) !important;
}

.it-action-primary {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 4px 11px !important;
    border-radius: 6px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    background: #eff6ff !important;
    color: var(--it-primary-deep) !important;
    border: 1px solid #bfdbfe !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.it-action-primary:hover {
    background: var(--it-primary) !important;
    border-color: var(--it-primary) !important;
    color: #ffffff !important;
}

/* ==========================================================================
   Empty / Loading / Footer Pagination Cards
   ========================================================================== */
.it-empty-card {
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
    border-radius: 10px !important;
    padding: 48px 20px !important;
    text-align: center !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
}

.it-empty-icon {
    width: 48px !important;
    height: 48px !important;
    margin: 0 auto 12px !important;
    border-radius: 12px !important;
    background: var(--it-primary-soft) !important;
    color: var(--it-primary-deep) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 20px !important;
}

.it-empty-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: var(--it-text-main) !important;
}

.it-empty-sub {
    font-size: 12px !important;
    color: var(--it-text-muted) !important;
    margin-top: 4px !important;
}

.it-loading {
    display: none;
    background: #ffffff !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 28px 16px !important;
    text-align: center !important;
    color: var(--it-text-muted) !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    margin-bottom: 12px !important;
}
.it-loading.show { display: block !important; }

.it-footer-card {
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
    border-radius: 10px !important;
    padding: 8px 16px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    flex-wrap: wrap !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
}

.it-showing-text {
    font-size: 11.5px !important;
    font-weight: 500 !important;
    color: var(--it-text-muted) !important;
    font-variant-numeric: tabular-nums !important;
}

#pagination .pagi-bar {
    padding: 0 !important;
    border-top: none !important;
    background: transparent !important;
}
#pagination .pagi-info { display: none !important; }

/* ==========================================================================
   15.6" Screen Fit & Small-Screen Responsive Breakpoints
   ========================================================================== */
@media (max-width: 1520px) {
    .it-stat-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }
}

@media (max-width: 1440px) {
    .it-stat-card {
        padding: 9px 11px !important;
    }
    .it-stat-icon {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        border-radius: 7px !important;
    }
    .it-stat-icon svg {
        width: 16px !important;
        height: 16px !important;
    }
    .it-stat-num {
        font-size: 18px !important;
    }
    .it-stat-lbl {
        font-size: 10.5px !important;
    }
    .it-stat-sub {
        font-size: 9.5px !important;
    }
    .it-filter-bar {
        gap: 8px !important;
    }
    .it-combobox-btn,
    .it-reset-btn {
        height: 40px !important;
    }
    .it-combobox-btn {
        min-width: 138px !important;
        padding: 0 10px !important;
    }
    .it-search-inp {
        height: 40px !important;
        padding: 0 65px 0 36px !important;
    }
    .it-search-kbd-hint {
        display: none !important;
    }
    .it-card-grid {
        grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.9fr) minmax(0, 1.32fr) minmax(0, 1.2fr) minmax(0, 1.5fr) minmax(0, 1.36fr) !important;
        gap: 7px !important;
        padding: 9px 10px !important;
    }
    .it-subcard { padding: 7px 9px !important; }
}

@media (max-width: 1200px) {
    .it-stat-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
    .it-card-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }
}

@media (max-width: 768px) {
    .it-header-card { padding: 12px 14px !important; }
    .it-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 7px !important; }
    .it-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .it-search-box { min-width: 100% !important; }
}

@media (max-width: 520px) {
    .it-stat-grid { grid-template-columns: minmax(0, 1fr) !important; }
    .it-card-grid { grid-template-columns: minmax(0, 1fr) !important; }
}
</style>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
<input type="hidden" name="inputSearch" id="inputSearch" />

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_client" id="kt_content" style="margin-top:0!important;padding-top:16px!important;">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post" style="margin-top:0!important;">
    <div id="kt_content_container" class="container-fluid px-3" style="margin-top: -58px;">

@if(in_array("view_schedule", $userPermissions))

      <div class="it-dashboard" id="interviewTrackerDashboard">

        <!-- ══════════════════════════════════════════════════════════════
             TIER 1: TOP HEADER CARD (Title + Count Badge + Live Sync)
             ══════════════════════════════════════════════════════════════ -->
        <div class="it-header-card">
          <div class="it-header-left">
            <div class="it-header-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
                <path d="m9 16 2 2 4-4"/>
              </svg>
            </div>
            <div>
              <div class="it-header-title-row">
                <h1 class="it-header-title">Interview Tracker</h1>
                <span class="it-header-count-badge" id="itHeaderTotalBadge">0</span>
              </div>
              <div class="it-header-sub">Track, schedule, evaluate feedback, and manage all candidate interviews in one place.</div>
            </div>
          </div>

          <div class="it-header-right">
            <button type="button" class="it-refresh-btn" id="itRefreshBtn" onclick="forceSyncInterviewTracker()" title="Sync live interviews">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="23 4 23 10 17 10"/>
                <polyline points="1 20 1 14 7 14"/>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
              </svg>
              <span>Sync</span>
            </button>
          </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
             TIER 2: EXECUTIVE 5-KPI METRIC STRIP (Compact & Responsive Single-Row Layout)
             ══════════════════════════════════════════════════════════════ -->
        <div class="it-stat-grid" id="statsGrid">
          {{-- KPI 1: All Interviews --}}
          <button type="button" class="it-stat-card it-stat-filter it-kpi-active" data-kpi-filter="all" onclick="filterByKpi('all')">
            <div class="it-stat-top">
              <div class="it-stat-icon blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="3" width="7" height="7"/>
                  <rect x="14" y="3" width="7" height="7"/>
                  <rect x="14" y="14" width="7" height="7"/>
                  <rect x="3" y="14" width="7" height="7"/>
                </svg>
              </div>
              <div class="it-stat-meta">
                <div class="it-stat-num-row">
                  <div class="it-stat-num" id="stat-all-interviews">0</div>
                </div>
                <div class="it-stat-lbl">All Interviews</div>
              </div>
            </div>
            <div class="it-stat-sub blue">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
              <span>All scheduled</span>
            </div>
          </button>

          {{-- KPI 2: Interviews This Month --}}
          <button type="button" class="it-stat-card it-stat-filter" data-kpi-filter="this_month" onclick="filterByKpi('this_month')">
            <div class="it-stat-top">
              <div class="it-stat-icon blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
              </div>
              <div class="it-stat-meta">
                <div class="it-stat-num-row">
                  <div class="it-stat-num" id="stat-interview-month">0</div>
                </div>
                <div class="it-stat-lbl">Interviews This Month</div>
              </div>
            </div>
            <div class="it-stat-sub blue">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
              </svg>
              <span>{{ now()->format('M Y') }} volume</span>
            </div>
          </button>

          {{-- KPI 3: Today's Interviews --}}
          <button type="button" class="it-stat-card it-stat-filter" data-kpi-filter="today" onclick="filterByKpi('today')">
            <div class="it-stat-top">
              <div class="it-stat-icon green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                  <circle cx="12" cy="15" r="2"/>
                </svg>
              </div>
              <div class="it-stat-meta">
                <div class="it-stat-num-row">
                  <div class="it-stat-num" id="stat-today">0</div>
                </div>
                <div class="it-stat-lbl">Today's Interviews</div>
              </div>
            </div>
            <div class="it-stat-sub green">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
              <span>{{ now()->format('d M') }} schedule</span>
            </div>
          </button>

          {{-- KPI 4: Upcoming Interviews --}}
          <button type="button" class="it-stat-card it-stat-filter" data-kpi-filter="upcoming" onclick="filterByKpi('upcoming')">
            <div class="it-stat-top">
              <div class="it-stat-icon purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
              </div>
              <div class="it-stat-meta">
                <div class="it-stat-num-row">
                  <div class="it-stat-num" id="stat-upcoming">0</div>
                </div>
                <div class="it-stat-lbl">Upcoming Interviews</div>
              </div>
            </div>
            <div class="it-stat-sub purple">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14"/>
                <path d="m12 5 7 7-7 7"/>
              </svg>
              <span>Scheduled ahead</span>
            </div>
          </button>

          {{-- KPI 5: Feedback Pending --}}
          <button type="button" class="it-stat-card it-stat-filter" data-kpi-filter="pending_feedback" onclick="filterByKpi('pending_feedback')">
            <div class="it-stat-top">
              <div class="it-stat-icon red">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                  <line x1="12" y1="8" x2="12" y2="12"/>
                  <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
              </div>
              <div class="it-stat-meta">
                <div class="it-stat-num-row">
                  <div class="it-stat-num" id="stat-feedback-pending">0</div>
                  <span class="it-kpi-pill-badge" id="badge_itPendingAction" style="display:none;">
                    <span class="it-kpi-dot"></span>
                    <span>Action due</span>
                  </span>
                </div>
                <div class="it-stat-lbl">Feedback Pending</div>
              </div>
            </div>
            <div class="it-stat-sub red">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
              <span id="span_itPendingSub">Evaluation awaiting review</span>
            </div>
          </button>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
             TIER 3: UNIFIED SEARCH, MODE, SORT & RESET CARD (Copied from Client Tracker)
             ══════════════════════════════════════════════════════════════ -->
        <div class="it-toolbar-card">
          <div class="it-filter-bar">
            {{-- Enterprise Micro-Search Box --}}
            <div class="it-search-box" id="itSearchBoxWrapper">
              <svg class="it-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
              <input
                type="text"
                class="it-search-inp"
                name="inputJobSearch"
                value=""
                placeholder="Search interviews by candidate, job, client, recruiter, interviewer..."
                id="kt_tagify_2"
                autocomplete="off"
                spellcheck="false"
                aria-label="Search interview listing"
                role="searchbox"
              />
              <div class="it-search-right-affix">
                <span class="it-search-count-pill" id="itSearchMatchCount" style="display:none;" title="Matching interviews count">0 matches</span>
                <kbd class="it-search-kbd-hint" id="itSearchKbdHint" title="Press Ctrl+K to search">Ctrl+K</kbd>
                <span id="loader_search" class="loader_search hidden_search" style="display:none;"></span>
              </div>
            </div>

            {{-- Filter 1: Job Type Combobox Popover (Direct Client, Internal Client, My Organization) --}}
            <div class="it-company-type-wrapper" id="itCompanyTypeDropdownWrapper">
              <button type="button" class="it-combobox-btn it-company-type-combobox-btn" id="itCompanyTypeBtn" onclick="toggleItCompanyTypeDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="itCompanyTypeDropdownPanel" title="Filter by Job Type">
                <div class="it-pill-icon">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 21h18"/>
                    <path d="M5 21V7l8-4v18"/>
                    <path d="M19 21V11l-6-4"/>
                    <line x1="9" y1="9" x2="9" y2="9.01"/>
                    <line x1="9" y1="13" x2="9" y2="13.01"/>
                    <line x1="9" y1="17" x2="9" y2="17.01"/>
                  </svg>
                </div>
                <div class="it-pill-texts">
                  <span class="it-pill-lbl">Job Type</span>
                  <span class="it-pill-val" id="itCompanyTypeDisplay">All Job Types</span>
                </div>
                <div class="it-pill-chevron">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
              </button>

              <div class="it-dropdown-panel it-company-type-dropdown-panel" id="itCompanyTypeDropdownPanel" role="listbox" aria-labelledby="itCompanyTypeBtn" tabindex="-1">
                <div class="it-pop-section-hdr">Job Type</div>
                <div class="it-pop-item selected" id="type-opt-All" data-type-val="All" onclick="selectItCompanyTypeOption('All', 'All Job Types', event)" role="option" aria-selected="true" tabindex="0">
                  <div class="it-pop-item-left">
                    <span class="it-dot it-dot-all"></span>
                    <span class="it-pop-item-title">All Job Types</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-check">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="type-opt-direct_client" data-type-val="direct_client" onclick="selectItCompanyTypeOption('direct_client', 'Direct Client', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <span class="it-dot it-dot-direct"></span>
                    <span class="it-pop-item-title">Direct Client</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge direct-badge">Marketplace</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="type-opt-internal_client" data-type-val="internal_client" onclick="selectItCompanyTypeOption('internal_client', 'Internal Client', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <span class="it-dot it-dot-internal"></span>
                    <span class="it-pop-item-title">Internal Client</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge internal-badge">Managed</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="type-opt-my_organization" data-type-val="my_organization" onclick="selectItCompanyTypeOption('my_organization', 'My Organization', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <span class="it-dot it-dot-myorg"></span>
                    <span class="it-pop-item-title">My Organization</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge myorg-badge">Own Company</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
              </div>

              <input type="hidden" id="itCompanyTypeFilter" name="company_type" value="All" />
            </div>

            {{-- Filter 2: Interview Mode Combobox Popover (Client Tracker UX) --}}
            <div class="it-mode-dropdown-wrapper" id="itModeDropdownWrapper">
              <button type="button" class="it-combobox-btn it-mode-combobox-btn" id="itModeBtn" onclick="toggleItModeDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="itModeDropdownPanel" title="Filter by Interview Mode">
                <div class="it-pill-icon">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polygon points="23 7 16 12 23 17 23 7"/>
                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                  </svg>
                </div>
                <div class="it-pill-texts">
                  <span class="it-pill-lbl">Mode</span>
                  <span class="it-pill-val" id="itModeDisplay">All Modes</span>
                </div>
                <div class="it-pill-chevron">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
              </button>

              <div class="it-dropdown-panel it-mode-dropdown-panel" id="itModeDropdownPanel" role="listbox" aria-labelledby="itModeBtn" tabindex="-1">
                <div class="it-pop-section-hdr">Interview Mode</div>
                <div class="it-pop-item selected" id="mode-opt-All" data-mode-val="All" onclick="selectItModeOption('All', 'All Modes', event)" role="option" aria-selected="true" tabindex="0">
                  <div class="it-pop-item-left">
                    <span class="it-dot it-dot-all"></span>
                    <span class="it-pop-item-title">All Modes</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-check">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="mode-opt-2" data-mode-val="2" onclick="selectItModeOption('2', 'Video Call', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <span class="it-dot it-dot-blue"></span>
                    <span class="it-pop-item-title">Video Call</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">Online</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="mode-opt-1" data-mode-val="1" onclick="selectItModeOption('1', 'Telecon', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <span class="it-dot it-dot-purple"></span>
                    <span class="it-pop-item-title">Telecon (Phone)</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">Voice</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="mode-opt-3" data-mode-val="3" onclick="selectItModeOption('3', 'Face to Face', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <span class="it-dot it-dot-green"></span>
                    <span class="it-pop-item-title">Face to Face</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">In-Person</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
              </div>

              <input type="hidden" id="itModeFilter" name="mode_filter" value="All" />
            </div>

            {{-- Filter 2: Enterprise Metronic Sort Combobox Popover (Client Tracker UX) --}}
            <div class="it-sort-dropdown-wrapper" id="itSortDropdownWrapper">
              <button type="button" class="it-combobox-btn it-sort-combobox-btn" id="itSortBtn" onclick="toggleItSortDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="itSortDropdownPanel" title="Sort Interview Listing">
                <div class="it-pill-icon">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="7" y1="4" x2="7" y2="20"/>
                    <polyline points="3 8 7 4 11 8"/>
                    <line x1="17" y1="4" x2="17" y2="20"/>
                    <polyline points="13 16 17 20 21 16"/>
                  </svg>
                </div>
                <div class="it-pill-texts">
                  <span class="it-pill-lbl">Sort</span>
                  <span class="it-pill-val" id="itSortDisplay">Newest Interviews</span>
                </div>
                <div class="it-pill-chevron">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
              </button>

              <div class="it-dropdown-panel it-sort-dropdown-panel" id="itSortDropdownPanel" role="listbox" aria-labelledby="itSortBtn" tabindex="-1">
                <div class="it-pop-section-hdr">Chronological</div>
                <div class="it-pop-item selected" id="sort-opt-default" data-sort-val="" onclick="selectItSortOption('', 'Newest Interviews', event)" role="option" aria-selected="true" tabindex="0">
                  <div class="it-pop-item-left">
                    <svg class="it-pop-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span class="it-pop-item-title">Newest Interviews</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">Newest &#8595;</span>
                    <span class="it-pop-check">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="sort-opt-4" data-sort-val="4" onclick="selectItSortOption('4', 'Soonest Scheduled', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <svg class="it-pop-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span class="it-pop-item-title">Soonest Scheduled</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">Soonest &#8593;</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="sort-opt-3" data-sort-val="3" onclick="selectItSortOption('3', 'Latest Scheduled', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <svg class="it-pop-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span class="it-pop-item-title">Latest Scheduled</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">Latest &#8595;</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="sort-opt-2" data-sort-val="2" onclick="selectItSortOption('2', 'Oldest Created', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <svg class="it-pop-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span class="it-pop-item-title">Oldest Created</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">Oldest &#8593;</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>

                <div class="it-pop-section-hdr">Action &amp; Schedule Focus</div>
                <div class="it-pop-item" id="sort-opt-1" data-sort-val="1" onclick="selectItSortOption('1', 'Today\'s Schedule', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <svg class="it-pop-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><path d="M12 14h.01"/></svg>
                    <span class="it-pop-item-title">Today's Interviews ({{ now()->format('d M Y') }})</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">Today</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="sort-opt-fb" data-sort-val="Feedback Pending" onclick="selectItSortOption('Feedback Pending', 'Feedback Pending', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <svg class="it-pop-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span class="it-pop-item-title">Feedback Pending</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">Action</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
                <div class="it-pop-item" id="sort-opt-up" data-sort-val="Interview Pending" onclick="selectItSortOption('Interview Pending', 'Upcoming Interviews', event)" role="option" aria-selected="false" tabindex="0">
                  <div class="it-pop-item-left">
                    <svg class="it-pop-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    <span class="it-pop-item-title">Upcoming Interviews</span>
                  </div>
                  <div class="it-pop-item-right">
                    <span class="it-pop-badge">Upcoming</span>
                    <span class="it-pop-check" style="display:none;">&#10003;</span>
                  </div>
                </div>
              </div>

              <input type="hidden" id="sortingfilter" name="sortingfilter" value="" />
            </div>

            {{-- Filter Reset Button --}}
            <button type="button" class="it-reset-btn" id="itResetFiltersBtn" onclick="resetFilters()" title="Reset search, tabs, mode, and sort filters">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="1 4 1 10 7 10"/>
                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
              </svg>
              <span>Reset</span>
              <span class="it-reset-count-badge" id="itActiveFilterCount" style="display:none;">0</span>
            </button>
          </div>
        </div>


        <!-- ══════════════════════════════════════════════════════════════
             TIER 4: PRIMARY INTERVIEW CARD STREAM & PAGINATION CARD
             ══════════════════════════════════════════════════════════════ -->
        <div class="it-stream-wrapper">
          <div class="it-loading" id="tableLoading">
            <i class="fas fa-spinner fa-spin me-2 text-primary"></i> Loading interviews...
          </div>
          <div id="Interview-trackers" class="it-cards-stream"></div>
        </div>

        <div class="it-footer-card">
          <div id="showingText" class="it-showing-text">Loading...</div>
          <div id="pagination" class="it-pagination"></div>
        </div>

      </div>

@else
      <div class="it-empty-card" style="margin-top:20px;">
        <div class="it-empty-icon" style="background:#fef2f2!important;color:#dc2626!important;">
          <i class="fas fa-lock"></i>
        </div>
        <div class="it-empty-title">Access Denied</div>
        <div class="it-empty-sub">You do not have permission to view Interview Tracker. Contact your administrator.</div>
      </div>
@endif

    </div>
  </div>
</div>
<!--end::Content-->

<!--begin::Modal - Interview Feedback-->
<div href="javascript:void(0);" id="intervied_feedback_toggle" class="text-dark fw-bolder text-hover-primary fs-6" data-bs-toggle="modal" data-bs-target="#interview_feedback"></div>
<div class="modal fade" id="interview_feedback" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content custmbg">
      <div class="modal-header flex-stack">
        <h2>Interview Feedback</h2>
        <div class="btn btn-sm btn-icon btn-active-light-danger data-bs-dismiss" data-bs-dismiss="modal">
          <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
            </svg>
          </span>
        </div>
      </div>
      <div id="load_interview_feedback">&nbsp;</div>
    </div>
  </div>
</div>

<!--begin::Interview Link Popup (custom overlay)-->
<style>
  #ilOverlay { position:fixed; inset:0; background:rgba(15,23,42,.45); z-index:1090; display:none; align-items:center; justify-content:center; padding:20px; }
  #ilOverlay.show { display:flex; }
  #ilOverlay .il-box { background:#fff; border-radius:14px; width:100%; max-width:440px; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); font-family:'Poppins',sans-serif; }
  #ilOverlay .il-head { display:flex; justify-content:space-between; align-items:center; padding:18px 22px 0; }
  #ilOverlay .il-head span { font-size:15px; font-weight:600; color:#1e293b; }
  #ilOverlay .il-close-btn { border:none; background:transparent; font-size:24px; line-height:1; color:#94a3b8; cursor:pointer; padding:0 4px; }
  #ilOverlay .il-close-btn:hover { color:#334155; }
  #ilOverlay .il-body { padding:14px 22px 22px; }
  #ilOverlay .il-cand { font-size:12px; color:#64748b; margin-bottom:8px; }
  #ilOverlay #ilLink { display:block; word-break:break-all; font-size:13px; color:#2563eb; background:#f1f5f9; border-radius:8px; padding:10px 12px; text-decoration:none; }
  #ilOverlay .il-actions { display:flex; gap:10px; margin-top:16px; }
  #ilOverlay .il-note { font-size:11px; color:#94a3b8; margin-top:10px; }
</style>
<div id="ilOverlay">
  <div class="il-box">
    <div class="il-head">
      <span id="ilTitle">Interview Link</span>
      <button type="button" class="il-close-btn" onclick="closeInterviewLink()">&times;</button>
    </div>
    <div class="il-body">
      <div id="ilCandidate" class="il-cand"></div>
      <a id="ilLink" href="#" target="_blank" rel="noopener"></a>
      <div class="il-actions">
        <button type="button" id="ilOpenBtn" class="btn btn-primary btn-sm" style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          <span id="ilOpenLabel">Open Link</span>
        </button>
        <button type="button" id="ilEmailBtn" class="btn btn-light btn-sm" style="flex:1;border:1px solid #e2e8f0;display:inline-flex;align-items:center;justify-content:center;gap:6px;">
          <span class="il-email-label" style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Send via Email</span>
          <span class="il-email-loading d-none">Sending...</span>
        </button>
      </div>
      <div class="il-note" style="display:inline-flex;align-items:center;gap:5px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg> Email is sent to the candidate and assigned interviewers.</div>
    </div>
  </div>
</div>
<!--end::Interview Link Popup-->

<!--begin::JD / Resume overlay panel (self-contained fixed overlay) -->
<style>
  #jdPanelOverlay { position: fixed; inset: 0; z-index: 1200; display: none; }
  #jdPanelOverlay.show { display: block; }
  #jdPanelOverlay .jdp-backdrop { position: absolute; inset: 0; background: rgba(15,23,42,0.5); }
  #jdPanel {
    position: absolute; top: 0; right: 0; height: 100%; width: min(1040px, 94vw);
    background: #ffffff; box-shadow: -8px 0 28px rgba(15,23,42,0.16);
    transform: translateX(100%); transition: transform .28s ease;
    display: flex; flex-direction: column; font-family: "Poppins", sans-serif;
  }
  #jdPanelOverlay.show #jdPanel { transform: translateX(0); }
  .jdp-head { display: flex; align-items: center; justify-content: space-between; padding: 15px 22px; border-bottom: 1px solid #e9ecef; flex: 0 0 auto; }
  .jdp-head h3 { margin: 0; font-size: 16px; font-weight: 700; color: #1e293b; }
  .jdp-close { border: none; background: transparent; color: #dc2626; cursor: pointer; padding: 4px; display: inline-flex; border-radius: 6px; }
  .jdp-close:hover { background: #fef2f2; }
  #jdPanelBody { flex: 1 1 auto; overflow-y: auto; min-height: 0; }
  #jdPanelBody .top-actions, #jdPanelBody .page-close, #jdPanelBody #kt_activitiesJob_close { display: none !important; }
</style>
<div id="jdPanelOverlay">
  <div class="jdp-backdrop" onclick="closeJdPanel()"></div>
  <div id="jdPanel">
    <div class="jdp-head">
      <h3 id="jdPanelTitle">Job Description</h3>
      <button type="button" class="jdp-close" onclick="closeJdPanel()" aria-label="Close">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div id="jdPanelBody"></div>
  </div>
</div>
<script>
  function openJdPanel(title, url) {
    document.getElementById('jdPanelTitle').textContent = title || 'Details';
    document.getElementById('jdPanelBody').innerHTML = '<div style="padding:48px;text-align:center;color:#64748b;font-size:13px;font-family:Poppins,sans-serif;">Loading…</div>';
    document.getElementById('jdPanelOverlay').classList.add('show');
    if (window.jQuery) {
      jQuery.ajax({ type: 'GET', url: url, success: function (r) { document.getElementById('jdPanelBody').innerHTML = (r && r.view) ? r.view : r; } });
    }
  }
  function closeJdPanel() { document.getElementById('jdPanelOverlay').classList.remove('show'); }
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' || e.keyCode === 27) closeJdPanel(); });
</script>
<!--end::JD / Resume overlay panel-->

@endsection

@push('footer-script')

<script>
// ── Enterprise Search, Mode & Sort Combobox Engine (Client Tracker UX Parity) ──
var itSearchDebounceTimer = null;
var itLastTotalCount = 0;
var itStatsByType = {};
var itLastAppliedSort = '';

function updateItToolbarIndicators() {
    var count = 0;
    var q = ($('#kt_tagify_2').val() || '').trim();
    var c = $('#itCompanyTypeFilter').val() || 'All';
    var m = $('#itModeFilter').val() || 'All';
    var s = $('#sortingfilter').val() || '';
    if (q.length > 0) count++;
    if (c !== 'All') count++;
    if (m !== 'All') count++;
    if (s !== '') count++;
    if (typeof activeKpiFilter !== 'undefined' && activeKpiFilter !== 'all') count++;

    var $btn = $('#itResetFiltersBtn');
    var $badge = $('#itActiveFilterCount');
    if (count > 0) {
        $btn.addClass('has-active-filters');
        $badge.text(count).show();
    } else {
        $btn.removeClass('has-active-filters');
        $badge.hide();
    }
}

function closeItCompanyTypeDropdown() {
    $('#itCompanyTypeDropdownPanel').removeClass('show');
    $('#itCompanyTypeBtn').removeClass('active').attr('aria-expanded', 'false');
}

function toggleItCompanyTypeDropdown(e) {
    if (e) { e.stopPropagation(); e.preventDefault(); }
    var $panel = $('#itCompanyTypeDropdownPanel');
    var $btn = $('#itCompanyTypeBtn');
    var isVisible = $panel.hasClass('show');

    if (isVisible) {
        closeItCompanyTypeDropdown();
    } else {
        closeItModeDropdown();
        closeItSortDropdown();
        $panel.addClass('show');
        $btn.addClass('active').attr('aria-expanded', 'true');
        var $selected = $panel.find('.it-pop-item.selected');
        if ($selected.length) {
            $selected.focus();
        } else {
            $panel.find('.it-pop-item').first().focus();
        }
    }
}
window.toggleItCompanyTypeDropdown = toggleItCompanyTypeDropdown;

function applyCompanyTypeStatsLocally(typeKey) {
    var k = (typeKey || 'all').toLowerCase();
    if (itStatsByType && itStatsByType[k]) {
        var group = itStatsByType[k];
        $('#stat-all-interviews').text(group.total ?? 0);
        $('#stat-interview-month').text(group.interview_month ?? 0);
        $('#stat-today').text(group.today ?? 0);
        $('#stat-upcoming').text(group.upcoming ?? 0);
        var pendingCnt = parseInt(group.feedback_pending ?? 0, 10) || 0;
        $('#stat-feedback-pending').text(pendingCnt);
        if (pendingCnt > 0) {
            $('#badge_itPendingAction').show();
            $('#span_itPendingSub').text(pendingCnt + (pendingCnt === 1 ? ' interview awaits feedback' : ' interviews await feedback'));
        } else {
            $('#badge_itPendingAction').hide();
            $('#span_itPendingSub').text("You're all caught up");
        }
    }
}

function selectItCompanyTypeOption(val, text, e, skipFetch) {
    if (e && e.stopPropagation) { e.stopPropagation(); }
    closeItCompanyTypeDropdown();

    var canonical = String(val || 'All');
    var typeLabels = {
        'All': 'All Job Types',
        'direct_client': 'Direct Client',
        'internal_client': 'Internal Client',
        'my_organization': 'My Organization'
    };
    var displayText = text || typeLabels[canonical] || 'All Job Types';
    if (canonical === 'All' && displayText === 'All Types') {
        displayText = 'All Job Types';
    }

    $('#itCompanyTypeDisplay').text(displayText);
    $('#itCompanyTypeFilter').val(canonical);
    if (typeof filterValues !== 'undefined') {
        filterValues.company_type = canonical;
    }

    $('#itCompanyTypeDropdownPanel .it-pop-item').removeClass('selected').attr('aria-selected', 'false')
        .find('.it-pop-check').hide();
    $('#itCompanyTypeDropdownPanel .it-pop-item[data-type-val="' + canonical + '"]')
        .addClass('selected').attr('aria-selected', 'true').find('.it-pop-check').show();

    if (canonical !== 'All') {
        $('#itCompanyTypeBtn').addClass('is-active-filter');
    } else {
        $('#itCompanyTypeBtn').removeClass('is-active-filter');
    }

    applyCompanyTypeStatsLocally(canonical);
    updateItToolbarIndicators();
    if (skipFetch) return;

    itCurrentPage = 1;
    fetchCandidates(1, $('#kt_tagify_2').val());
}
window.selectItCompanyTypeOption = selectItCompanyTypeOption;

function clearItCompanyTypeFilter(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    selectItCompanyTypeOption('All', 'All Job Types', null, false);
}
window.clearItCompanyTypeFilter = clearItCompanyTypeFilter;

function closeItModeDropdown() {
    $('#itModeDropdownPanel').removeClass('show');
    $('#itModeBtn').removeClass('active').attr('aria-expanded', 'false');
}

function closeItSortDropdown() {
    $('#itSortDropdownPanel').removeClass('show');
    $('#itSortBtn').removeClass('active').attr('aria-expanded', 'false');
}

function toggleItModeDropdown(e) {
    if (e) { e.stopPropagation(); e.preventDefault(); }
    var $panel = $('#itModeDropdownPanel');
    var $btn = $('#itModeBtn');
    var isVisible = $panel.hasClass('show');

    if (isVisible) {
        closeItModeDropdown();
    } else {
        closeItCompanyTypeDropdown();
        closeItSortDropdown();
        $panel.addClass('show');
        $btn.addClass('active').attr('aria-expanded', 'true');
        var $selected = $panel.find('.it-pop-item.selected');
        if ($selected.length) {
            $selected.focus();
        } else {
            $panel.find('.it-pop-item').first().focus();
        }
    }
}
window.toggleItModeDropdown = toggleItModeDropdown;

function selectItModeOption(val, text, e, skipFetch) {
    if (e && e.stopPropagation) { e.stopPropagation(); }
    closeItModeDropdown();

    var canonical = String(val || 'All');
    var modeLabels = { 'All': 'All Modes', '2': 'Video Call', '1': 'Telecon', '3': 'Face to Face' };
    var displayText = text || modeLabels[canonical] || 'All Modes';

    $('#itModeDisplay').text(displayText);
    $('#itModeFilter').val(canonical);
    filterValues.mode = canonical;

    $('#itModeDropdownPanel .it-pop-item').removeClass('selected').attr('aria-selected', 'false')
        .find('.it-pop-check').hide();
    $('#itModeDropdownPanel .it-pop-item[data-mode-val="' + canonical + '"]')
        .addClass('selected').attr('aria-selected', 'true').find('.it-pop-check').show();

    if (canonical !== 'All') {
        $('#itModeBtn').addClass('is-active-filter');
    } else {
        $('#itModeBtn').removeClass('is-active-filter');
    }

    updateItToolbarIndicators();
    if (skipFetch) return;

    itCurrentPage = 1;
    fetchCandidates(1, $('#kt_tagify_2').val());
}
window.selectItModeOption = selectItModeOption;

function clearItModeFilter(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    selectItModeOption('All', 'All Modes', null, false);
}
window.clearItModeFilter = clearItModeFilter;

function toggleItSortDropdown(e) {
    if (e) { e.stopPropagation(); e.preventDefault(); }
    var $panel = $('#itSortDropdownPanel');
    var $btn = $('#itSortBtn');
    var isVisible = $panel.hasClass('show');

    if (isVisible) {
        closeItSortDropdown();
    } else {
        closeItCompanyTypeDropdown();
        closeItModeDropdown();
        $panel.addClass('show');
        $btn.addClass('active').attr('aria-expanded', 'true');
        var $selected = $panel.find('.it-pop-item.selected');
        if ($selected.length) {
            $selected.focus();
        } else {
            $panel.find('.it-pop-item').first().focus();
        }
    }
}
window.toggleItSortDropdown = toggleItSortDropdown;

function sortDomInterviewsLocally(sortVal) {
    // Only pure chronological sorts can reorder in-place; filter-based sorts ("1", "Feedback Pending", "Interview Pending") need ES query
    if (sortVal === '1' || sortVal === 'Feedback Pending' || sortVal === 'Interview Pending') {
        return false;
    }
    var $container = $('#Interview-trackers');
    var $cards = $container.children('.it-card').get();
    if (!$cards || $cards.length <= 1) return false;
    if (itLastTotalCount > itCurrentSize || itLastTotalCount > $cards.length) {
        return false; // Multi-page dataset: defer to Elasticsearch query
    }

    $cards.sort(function(a, b) {
        var idA = parseInt($(a).attr('data-interview-id') || '0', 10);
        var idB = parseInt($(b).attr('data-interview-id') || '0', 10);
        var schedA = parseInt($(a).attr('data-schedule-ts') || '0', 10);
        var schedB = parseInt($(b).attr('data-schedule-ts') || '0', 10);
        var createdA = parseInt($(a).attr('data-created-ts') || '0', 10);
        var createdB = parseInt($(b).attr('data-created-ts') || '0', 10);

        if (sortVal === '4') {
            return schedA !== schedB ? (schedA - schedB) : (idA - idB);
        } else if (sortVal === '3') {
            return schedB !== schedA ? (schedB - schedA) : (idB - idA);
        } else if (sortVal === '2') {
            return createdA !== createdB ? (createdA - createdB) : (idA - idB);
        } else {
            return schedB !== schedA ? (schedB - schedA) : (idB - idA);
        }
    });

    requestAnimationFrame(function() {
        $.each($cards, function(idx, el) {
            $container.append(el);
        });
    });
    return true;
}

function selectItSortOption(val, text, e, skipFetch) {
    if (e && e.stopPropagation) { e.stopPropagation(); }
    closeItSortDropdown();

    var canonical = String(val ?? '');
    var sortLabels = {
        '': 'Newest Interviews',
        '4': 'Soonest Scheduled',
        '3': 'Latest Scheduled',
        '2': 'Oldest Created',
        '1': "Today's Schedule",
        'Feedback Pending': 'Feedback Pending',
        'Interview Pending': 'Upcoming Interviews'
    };
    var displayText = text || sortLabels[canonical] || 'Newest Interviews';

    $('#itSortDisplay').text(displayText);
    $('#sortingfilter').val(canonical);

    $('#itSortDropdownPanel .it-pop-item').removeClass('selected').attr('aria-selected', 'false')
        .find('.it-pop-check').hide();
    $('#itSortDropdownPanel .it-pop-item[data-sort-val="' + canonical + '"]')
        .addClass('selected').attr('aria-selected', 'true').find('.it-pop-check').show();

    if (canonical !== '') {
        $('#itSortBtn').addClass('is-custom-sorted');
    } else {
        $('#itSortBtn').removeClass('is-custom-sorted');
    }

    var prevSort = itLastAppliedSort;
    itLastAppliedSort = canonical;

    updateItToolbarIndicators();
    if (skipFetch) return;

    var wasFilterSort = (prevSort === '1' || prevSort === 'Feedback Pending' || prevSort === 'Interview Pending');
    var isFilterSort = (canonical === '1' || canonical === 'Feedback Pending' || canonical === 'Interview Pending');

    if (!wasFilterSort && !isFilterSort && sortDomInterviewsLocally(canonical)) {
        return;
    }

    itCurrentPage = 1;
    fetchCandidates(1, $('#kt_tagify_2').val());
}
window.selectItSortOption = selectItSortOption;

$(document).ready(function() {
    var $searchInp = $('#kt_tagify_2');

    $searchInp.on('input', function() {
        var queryVal = ($(this).val() || '').trim();
        if (queryVal.length > 0) {
            $('#itSearchKbdHint').hide();
        } else {
            $('#itSearchMatchCount').hide();
            $('#itSearchKbdHint').show();
        }
        updateItToolbarIndicators();

        clearTimeout(itSearchDebounceTimer);
        itSearchDebounceTimer = setTimeout(function() {
            itCurrentPage = 1;
            fetchCandidates(1);
        }, 180);
    });

    $searchInp.on('keydown', function(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            $(this).blur();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(itSearchDebounceTimer);
            itCurrentPage = 1;
            fetchCandidates(1);
        }
    });

    // Global keyboard shortcut: Ctrl+K / Cmd+K to focus search (Client Tracker parity)
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key && e.key.toLowerCase() === 'k') {
            var activeEl = document.activeElement;
            var isInput = activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.isContentEditable);
            if (!isInput || activeEl.id === 'kt_tagify_2') {
                e.preventDefault();
                $searchInp.focus().select();
            }
        } else if (e.key === 'Escape') {
            if ($('#itCompanyTypeDropdownPanel').hasClass('show')) {
                closeItCompanyTypeDropdown();
                $('#itCompanyTypeBtn').focus();
            }
            if ($('#itModeDropdownPanel').hasClass('show')) {
                closeItModeDropdown();
                $('#itModeBtn').focus();
            }
            if ($('#itSortDropdownPanel').hasClass('show')) {
                closeItSortDropdown();
                $('#itSortBtn').focus();
            }
        }
    });

    // Keyboard navigation inside WAI-ARIA combobox panels
    $('#itCompanyTypeDropdownPanel, #itModeDropdownPanel, #itSortDropdownPanel').on('keydown', '.it-pop-item', function(e) {
        var $items = $(this).closest('.it-dropdown-panel').find('.it-pop-item');
        var idx = $items.index(this);
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            $items.eq((idx + 1) % $items.length).focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            $items.eq((idx - 1 + $items.length) % $items.length).focus();
        } else if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

    // Close popovers on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#itCompanyTypeDropdownWrapper').length) {
            closeItCompanyTypeDropdown();
        }
        if (!$(e.target).closest('#itModeDropdownWrapper').length) {
            closeItModeDropdown();
        }
        if (!$(e.target).closest('#itSortDropdownWrapper').length) {
            closeItSortDropdown();
        }
    });
});

// Open Job Description slide-over panel from Requisition sub-card
$(document).on('click', '.js-it-open-jd', function(e) {
    e.preventDefault();
    var jobId = $(this).data('job-id');
    if (!jobId) return;
    var isDirect = String($(this).data('direct')) === '1';
    var url = "{{ route('service_company.jobs.viewmore', ':id') }}".replace(':id', jobId);
    url += isDirect ? '?viewmore=clientviewmore' : '?viewmore=viewmore';
    if (typeof openJdPanel === 'function') {
        openJdPanel('Job Description #' + jobId, url);
    }
});

// Quick Reschedule button on Interview Card bottom strip
$(document).on('click', '.js-it-reschedule-btn', function(e) {
    e.preventDefault();
    var wiseId = $(this).data('wise-id');
    var intId = $(this).data('interview-id') || '';
    if (!wiseId) return;
    var url = "{{ route('service_company.job-applications.create-schedule', ':id') }}".replace(':id', wiseId);
    url += '?reschedule=1' + (intId ? '&interview_id=' + intId : '');
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    (function(){
        var _el = document.getElementById('interview_schedule_drawer');
        if (!_el) return;
        if (!_el.classList.contains('drawer')) _el.classList.add('drawer', 'drawer-end');
        _el.classList.add('drawer-on');
        _el.style.transform = 'none';
    })();
    if (typeof window.isdShellSetStep === 'function') window.isdShellSetStep('main');
    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function() {
            $('#app_interview_schedule_content').html('<img src="' + baseurl + '" height="35px" width="100%">');
        },
        success: function(response) {
            $('#app_interview_schedule_content').html(response.view || response);
        }
    });
});

$(document).on('click', '.notAllow-feedback', function () {
    Swal.fire({
        text: "Only users with Administrator or Job Captain roles can add feedback",
        title: "Access Denied",
        icon: "warning",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        customClass: { confirmButton: "btn btn-primary" }
    });
});

$(document).on('click', '.show-feedback', async function () {
    const connectedCandidateShowCustomCheck = await isInternetAvailablecheck();
    if (!connectedCandidateShowCustomCheck) {
        Swal.fire({
            title: "No Internet",
            text: "You are currently offline or the server is unreachable.",
            icon: "warning",
            confirmButtonText: "Ok, got it!",
            customClass: { confirmButton: "btn btn-warning" }
        });
        return;
    }

    $('#intervied_feedback_toggle').trigger('click');
    var id = $(this).data('row-id');

    var urlchange = "{{ route('service_company.interview-schedule.skillshowfeedbackdownload',':id') }}";
    urlchange = urlchange.replace(':id', id);
    $('.urlchange').attr('href', 'javascript:;');

    var url = "{{ route('service_company.interview-schedule.showfeedback',':id') }}";
    url = url.replace(':id', id);
    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function() {
            $('#load_interview_feedback').html('<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:50px 20px;gap:12px;"><div style="width:36px;height:36px;border:3px solid #e2e8f0;border-top-color:#2563eb;border-radius:50%;animation:fbFastSpin .5s linear infinite;"></div><div style="color:#64748b;font-size:13px;font-weight:500;">Loading feedback…</div></div><style>@keyframes fbFastSpin{to{transform:rotate(360deg)}}</style>');
        },
        success: function (response) {
            if (response.status == "success") {
                $('#load_interview_feedback').html(response.view);
                if (response.download_show >= 1) {
                    $('.urlchange').removeClass('d-none');
                }
            }
        }
    });
});

// Reset the feedback modal on close so it always re-loads fresh next time
$(document).on('hidden.bs.modal', '#interview_feedback', function () {
    $('#load_interview_feedback').html('&nbsp;');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
});

$(document).on('click', '#app_interview_schedule_reschedule', function(){
    $("#interview_feedback .close").click();
    var id = $(this).attr('rel');
    var intId = $(this).data('interview-id') || $(this).attr('data-interview-id') || '';
    var url = "{{ route('service_company.job-applications.create-schedule',':id') }}?reschedule=1" + (intId ? '&interview_id=' + intId : '');
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    (function(){ var _el=document.getElementById('interview_schedule_drawer'); if(!_el)return; if(!_el.classList.contains('drawer'))_el.classList.add('drawer','drawer-end'); _el.classList.add('drawer-on'); _el.style.transform='none'; })();
    if (typeof window.isdShellSetStep === 'function') window.isdShellSetStep('main');
    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function() {
            $('#app_interview_schedule_content').html('<img src="'+baseurl+'" height="35px" width="100%">');
        },
        success: function (response) {
            $('#app_interview_schedule_content').html(response.view || response);
        }
    });
});

$(document).on('click', '.interview_mention_link', function(){
    Swal.fire({
        text: "The interview link either does not exist or has expired..",
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        customClass: { confirmButton: "btn btn-primary" }
    });
});

// ── Pagination & UI Preference State ───────────────────────────────────────
var itCurrentPage = 1;
var itCurrentSize = 10;

try {
    var itSavedSize = parseInt(localStorage.getItem('it_pref_page_size'), 10);
    if (itSavedSize && [10, 20, 50, 100].indexOf(itSavedSize) !== -1) {
        itCurrentSize = itSavedSize;
    }
} catch (e) {
    itCurrentSize = 10;
}

$(document).ready(function() {
    var isReload = false;
    try {
        var navEntries = performance.getEntriesByType('navigation');
        if (navEntries && navEntries.length > 0) {
            isReload = navEntries[0].type === 'reload';
        } else if (performance.navigation) {
            isReload = performance.navigation.type === 1;
        }
    } catch (e) {}

    fetchCandidates(1, '', isReload);

    $(document).on('click', '#pagination [data-page]', function(e) {
        e.preventDefault();
        var page = parseInt($(this).data('page'), 10);
        if (!page || page < 1 || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
        itCurrentPage = page;
        fetchCandidates(itCurrentPage);
    });

    $(document).on('change', '#pagination [data-perpage]', function() {
        itCurrentSize = parseInt($(this).val(), 10) || 10;
        try {
            localStorage.setItem('it_pref_page_size', itCurrentSize);
        } catch (e) {}
        itCurrentPage = 1;
        fetchCandidates(itCurrentPage);
    });
});

async function isInternetAvailablecheck() {
    try {
        await fetch("https://www.gstatic.com/generate_204", {
            method: "GET",
            cache: "no-cache",
            mode: "no-cors"
        });
        return true;
    } catch (error) {
        return false;
    }
}

// ── KPI & Filter Controllers (Client Tracker Parity — Zero Subtabs) ─────────
let activeKpiFilter = 'all';
let filterValues = { company_type: 'All', mode: 'All', sort: '', search: '', kpi_filter: 'all' };

function syncActiveKpiCards(filterName) {
    document.querySelectorAll('.it-stat-card').forEach(function(card) {
        var f = card.getAttribute('data-kpi-filter') || card.getAttribute('data-kpi-tab');
        card.classList.toggle('it-kpi-active', f === filterName);
    });
    updateItToolbarIndicators();
}

function filterByKpi(filterName) {
    activeKpiFilter = (activeKpiFilter === filterName) ? 'all' : (filterName || 'all');
    filterValues.kpi_filter = activeKpiFilter;
    syncActiveKpiCards(activeKpiFilter);
    itCurrentPage = 1;
    fetchCandidates(1, $('#kt_tagify_2').val());
}
window.filterByKpi = filterByKpi;

function resetFilters() {
    itRamCache.clear();
    if (window.JeroInterviewScheduleLocalStore) {
        window.JeroInterviewScheduleLocalStore.purge();
    }
    itLastAppliedSort = '';
    selectItSortOption('', 'Newest Interviews', null, true);
    selectItCompanyTypeOption('All', 'All Job Types', null, true);
    selectItModeOption('All', 'All Modes', null, true);
    activeKpiFilter = 'all';
    filterValues.kpi_filter = 'all';
    filterValues.company_type = 'All';
    filterValues.mode = 'All';
    filterValues.sort = '';
    filterValues.search = '';
    syncActiveKpiCards('all');
    $('#kt_tagify_2').val('');
    $('#inputSearch').val('');
    $('#itSearchMatchCount').hide();
    $('#itSearchKbdHint').show();
    updateItToolbarIndicators();
    itCurrentPage = 1;
    fetchCandidates(1, '', true);
}
window.resetFilters = resetFilters;

function forceSyncInterviewTracker() {
    var $btn = $('#itRefreshBtn');
    $btn.addClass('is-syncing');
    fetchCandidates(itCurrentPage, $('#kt_tagify_2').val(), true).finally(function() {
        setTimeout(function() { $btn.removeClass('is-syncing'); }, 350);
    });
}

function toggleFollowUp(btn) {
    const menu = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');
    document.querySelectorAll('.it-followup-menu.open').forEach(m => m.classList.remove('open'));
    if (!isOpen) {
        menu.classList.remove('it-menu-up');
        menu.classList.add('open');
        var rect = menu.getBoundingClientRect();
        var vh = window.innerHeight || document.documentElement.clientHeight;
        if (rect.bottom > vh && rect.top > rect.height) {
            menu.classList.add('it-menu-up');
        }
    }
}

// ── Browser-RAM LRU Cache ──────────────────────────────────────────────────
var itRamCache = new Map();
var IT_MAX_RAM_CACHE = 25;
var itCurrentXhr = null;

// ── JeroInterviewScheduleLocalStore — Native Browser IndexedDB Engine ────────
const JeroInterviewScheduleLocalStore = (function() {
    const DB_NAME = 'jero_interview_schedule_db';
    const DB_VERSION = 7; // Bumped to v7 with onblocked, auto-timeout, and connection close guards
    const CURRENT_TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
    const CURRENT_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
    const CACHE_TTL_MS = 15 * 60 * 1000;

    let dbPromise = null;

    function getDB() {
        if (!window.indexedDB) return Promise.resolve(null);
        if (dbPromise) return dbPromise;

        dbPromise = new Promise((resolve) => {
            let settled = false;
            const timer = setTimeout(function() {
                if (!settled) {
                    settled = true;
                    console.warn('JeroInterviewScheduleLocalStore: DB open timed out, falling back to network');
                    resolve(null);
                }
            }, 500);

            try {
                const req = window.indexedDB.open(DB_NAME, DB_VERSION);
                req.onblocked = function() {
                    if (!settled) {
                        settled = true;
                        clearTimeout(timer);
                        console.warn('JeroInterviewScheduleLocalStore: DB open blocked, falling back to network');
                        resolve(null);
                    }
                };
                req.onupgradeneeded = function(e) {
                    try {
                        const db = e.target.result;
                        if (!db.objectStoreNames.contains('schedule_cache')) {
                            db.createObjectStore('schedule_cache', { keyPath: 'cache_key' });
                        } else {
                            try { e.target.transaction.objectStore('schedule_cache').clear(); } catch (err) {}
                        }
                        if (!db.objectStoreNames.contains('meta')) {
                            db.createObjectStore('meta', { keyPath: 'id' });
                        }
                    } catch (err) {
                        console.warn('JeroInterviewScheduleLocalStore upgrade error:', err);
                    }
                };
                req.onsuccess = function(e) {
                    if (settled) return;
                    settled = true;
                    clearTimeout(timer);
                    const db = e.target.result;
                    db.onversionchange = function() {
                        try { db.close(); } catch (err) {}
                        dbPromise = null;
                    };
                    try {
                        const tx = db.transaction(['meta'], 'readonly');
                        const metaStore = tx.objectStore('meta');
                        const metaReq = metaStore.get('current_context');
                        metaReq.onsuccess = function() {
                            const meta = metaReq.result;
                            if (meta && (meta.tenant_id !== CURRENT_TENANT_ID || meta.user_id !== CURRENT_USER_ID)) {
                                const wipeTx = db.transaction(['schedule_cache', 'meta'], 'readwrite');
                                wipeTx.objectStore('schedule_cache').clear();
                                wipeTx.objectStore('meta').put({ id: 'current_context', tenant_id: CURRENT_TENANT_ID, user_id: CURRENT_USER_ID, updated_at: Date.now() });
                            } else if (!meta) {
                                const setTx = db.transaction(['meta'], 'readwrite');
                                setTx.objectStore('meta').put({ id: 'current_context', tenant_id: CURRENT_TENANT_ID, user_id: CURRENT_USER_ID, updated_at: Date.now() });
                            }
                        };
                    } catch (err) {
                        console.warn('JeroInterviewScheduleLocalStore tenancy check warning:', err);
                    }
                    resolve(db);
                };
                req.onerror = function() {
                    if (!settled) {
                        settled = true;
                        clearTimeout(timer);
                        resolve(null);
                    }
                };
            } catch (err) {
                if (!settled) {
                    settled = true;
                    clearTimeout(timer);
                    resolve(null);
                }
            }
        });

        return dbPromise;
    }

    return {
        get: async function(key) {
            try {
                const db = await getDB();
                if (!db || !key) return null;
                return new Promise((resolve) => {
                    const timer = setTimeout(function() { resolve(null); }, 350);
                    try {
                        const tx = db.transaction(['schedule_cache'], 'readonly');
                        tx.onerror = function() { clearTimeout(timer); resolve(null); };
                        tx.onabort = function() { clearTimeout(timer); resolve(null); };
                        const store = tx.objectStore('schedule_cache');
                        const req = store.get(key);
                        req.onsuccess = function() {
                            clearTimeout(timer);
                            const res = req.result;
                            const lastMut = parseInt(localStorage.getItem('jero_it_last_mutation') || '0', 10);
                            if (res && res.payload && typeof res.payload === 'object' && typeof res.payload.html === 'string' && (Date.now() - res.timestamp < CACHE_TTL_MS) && (!lastMut || res.timestamp > lastMut)) {
                                resolve(res.payload);
                            } else {
                                resolve(null);
                            }
                        };
                        req.onerror = function() { clearTimeout(timer); resolve(null); };
                    } catch (e) {
                        clearTimeout(timer);
                        resolve(null);
                    }
                });
            } catch (err) {
                return null;
            }
        },
        set: async function(key, payload) {
            if (!key || !payload || typeof payload !== 'object' || typeof payload.html !== 'string') return;
            try {
                const db = await getDB();
                if (!db) return;
                const tx = db.transaction(['schedule_cache'], 'readwrite');
                const store = tx.objectStore('schedule_cache');
                store.put({
                    cache_key: key,
                    payload: payload,
                    timestamp: Date.now()
                });
            } catch (e) {}
        },
        purge: async function() {
            try {
                const db = await getDB();
                if (!db) return;
                const tx = db.transaction(['schedule_cache'], 'readwrite');
                tx.objectStore('schedule_cache').clear();
            } catch (e) {}
        }
    };
})();
window.JeroInterviewScheduleLocalStore = JeroInterviewScheduleLocalStore;

// ── Realtime Delta DOM Patcher (Targeted Card Update at 0ms) ───────────────
function patchInterviewRowDelta(interviewId, delta) {
    if (!interviewId || !delta) return false;
    var $card = $('.it-card[data-interview-id="' + interviewId + '"]');
    if (!$card.length) return false;

    // 1. Follow-up status delta patch
    if (delta.followup_status) {
        var $trigger = $card.find('.it-followup-trigger');
        var labels = { 'confirmed': 'Confirmed', 'no-response': 'No Response', 'rescheduled': 'Rescheduled' };
        if ($trigger.length) {
            $trigger.find('.fu-label').text(labels[delta.followup_status] || 'Follow Up');
            $trigger.removeClass('is-confirmed is-no-response is-rescheduled').addClass('is-' + delta.followup_status);
            $trigger.find('.fu-cta-icon').remove();
        }
    }

    // 2. Stage delta patch
    if (delta.stage) {
        var $stageBadge = $card.find('.it-stage-cell .it-stage-badge');
        if ($stageBadge.length) {
            $stageBadge.text(delta.stage);
        }
    }

    // 3. Feedback delta patch (Immediate 0ms card update upon feedback submission)
    if (delta.feedback) {
        var fb = delta.feedback;
        var attId = parseInt(fb.available_status, 10) || 0;
        var $fbCell = $card.find('.it-feedback-cell');
        var $evalPill = $card.find('.it-subcard-feedback .it-eval-pill');

        if ($fbCell.length) {
            var attLabelMap = {
                1: 'Attended',
                2: 'No Show',
                3: 'Rescheduled',
                4: 'Cancelled by Company',
                6: 'Other Reason',
                7: 'Cancelled by Candidate'
            };
            var resultLabels = {
                'selected': 'Selected',
                'hold': 'Hold',
                'rejected': 'Rejected',
                'fake_proxy': 'Fake / Proxy',
                'feedback_pending': 'Feedback Pending'
            };
            var recLabels = {
                'strong_hire': 'Strong Hire',
                'hire': 'Hire',
                'consider': 'Consider',
                'no_hire': 'No Hire',
                'strong_no_hire': 'Strong No Hire'
            };
            var recStyles = {
                'strong_hire': { bg: '#dcfce7', color: '#15803d' },
                'hire': { bg: '#dcfce7', color: '#15803d' },
                'consider': { bg: '#fef3c7', color: '#b45309' },
                'no_hire': { bg: '#fee2e2', color: '#b91c1c' },
                'strong_no_hire': { bg: '#fee2e2', color: '#b91c1c' }
            };

            var html = '';
            if (attId === 1) {
                var resKey = fb.interview_result || '';
                var resText = resultLabels[resKey] || (resKey ? resKey : 'Feedback Pending');
                var resColor = '#10b981';
                var accentColor = '#10b981';
                if (resKey === 'rejected' || resKey === 'fake_proxy') {
                    resColor = '#ef4444';
                    accentColor = '#ef4444';
                } else if (resKey === 'hold' || resKey === 'feedback_pending') {
                    resColor = '#d97706';
                    accentColor = '#f59e0b';
                }
                $card.css('border-left', '3.5px solid ' + accentColor);
                if ($evalPill.length) {
                    $evalPill.removeClass('it-eval-scheduled it-eval-pending it-eval-noshow it-eval-rescheduled').addClass('it-eval-attended').text('Attended');
                }

                html += '<div class="it-fb-status-row" style="color: ' + resColor + ';">' +
                    '<i class="fas fa-check-circle"></i> ' +
                    '<span>' + $('<div>').text(resText).html() + '</span>' +
                    '</div>';

                var recKey = fb.overall_recommendation || '';
                if (recKey && recLabels[recKey]) {
                    var rStyle = recStyles[recKey] || { bg: '#eff6ff', color: '#2563eb' };
                    html += '<div class="it-fb-badges-row"><span class="it-rec-pill" style="background:' + rStyle.bg + ';color:' + rStyle.color + ';">' +
                        $('<div>').text(recLabels[recKey]).html() +
                        '</span></div>';
                }

                html += '<button type="button" class="it-feedback-btn it-fb-btn-view show-feedback" data-row-id="' + interviewId + '">' +
                    '<i class="far fa-eye"></i> View Feedback' +
                    '</button>';
                $card.find('.js-it-reschedule-btn').remove();
            } else if (attId > 1) {
                var attText = attLabelMap[attId] || 'Not Attended';
                $card.css('border-left', '3.5px solid ' + (attId === 4 || attId === 7 ? '#ef4444' : '#f59e0b'));
                if ($evalPill.length) {
                    $evalPill.removeClass('it-eval-scheduled it-eval-pending it-eval-attended')
                        .addClass(attId === 3 ? 'it-eval-rescheduled' : 'it-eval-noshow')
                        .text(attId === 3 ? 'Rescheduled' : (attId === 2 ? 'No Show' : 'Not Attended'));
                }
                html += '<div class="it-fb-status-row" style="color: #d97706;">' +
                    '<i class="fas fa-exclamation-triangle"></i> ' +
                    '<span>' + $('<div>').text(attText).html() + '</span>' +
                    '</div>';
                html += '<button type="button" class="it-feedback-btn it-fb-btn-neutral show-feedback" data-row-id="' + interviewId + '">' +
                    '<i class="far fa-file-alt"></i> No Feedback' +
                    '</button>';
            }

            if (html) {
                $fbCell.html(html);
            }
        }
    }

    return true;
}
window.patchInterviewRowDelta = patchInterviewRowDelta;

function renderInterviewData(response) {
    $('#tableLoading').removeClass('show');
    $('.loadingMessage').css({ display: 'none' });
    $('.loader_search').css({ display: 'none' });
    $('.page-loader').css('display', 'none');

    if (!response || typeof response !== 'object') {
        $('#showingText').text('0 interviews');
        $('#Interview-trackers').html(
            '<div class="it-empty-card">' +
            '  <div class="it-empty-icon"><i class="fas fa-calendar-times"></i></div>' +
            '  <div class="it-empty-title">No Interviews Scheduled</div>' +
            '  <div class="it-empty-sub">No candidate interviews match your selected criteria.</div>' +
            '</div>'
        );
        return;
    }

    var htmlContent = (typeof response.html === 'string' && response.html.trim() !== '')
        ? response.html
        : '<div class="it-empty-card">' +
          '  <div class="it-empty-icon"><i class="fas fa-calendar-times"></i></div>' +
          '  <div class="it-empty-title">No Interviews Scheduled</div>' +
          '  <div class="it-empty-sub">No candidate interviews match your selected criteria.</div>' +
          '</div>';

    $('#Interview-trackers').html(htmlContent);
    $('#pagination').html(response.pagination || '');

    itLastTotalCount = parseInt(response.total ?? 0, 10) || 0;

    if (itLastTotalCount === 0) {
        $('#showingText').text('0 interviews');
    } else if (response.from !== undefined && response.to !== undefined) {
        $('#showingText').text(
            'Showing ' + response.from + ' to ' + response.to + ' of ' + response.total + ' interviews'
        );
    } else if (response.total !== undefined) {
        $('#showingText').text('Showing ' + response.total + ' interviews');
    } else {
        $('#showingText').text('0 interviews');
    }

    var trimmedQuery = ($('#kt_tagify_2').val() || '').trim();
    if (trimmedQuery.length > 0) {
        var matchCount = itLastTotalCount;
        $('#itSearchMatchCount').text(matchCount + (matchCount === 1 ? ' match' : ' matches')).show();
        $('#itSearchKbdHint').hide();
    } else {
        $('#itSearchMatchCount').hide();
        $('#itSearchKbdHint').show();
    }

    if (response.stats) {
        if (response.stats.stats_by_type) {
            itStatsByType = response.stats.stats_by_type;
        }
        $('#stat-all-interviews').text(response.stats.total ?? 0);
        $('#stat-interview-month').text(response.stats.interview_month ?? 0);
        $('#stat-today').text(response.stats.today ?? 0);
        $('#stat-upcoming').text(response.stats.upcoming ?? 0);
        var pendingCnt = parseInt(response.stats.feedback_pending ?? 0, 10) || 0;
        $('#stat-feedback-pending').text(pendingCnt);
        if (pendingCnt > 0) {
            $('#badge_itPendingAction').show();
            $('#span_itPendingSub').text(pendingCnt + (pendingCnt === 1 ? ' interview awaits feedback' : ' interviews await feedback'));
        } else {
            $('#badge_itPendingAction').hide();
            $('#span_itPendingSub').text("You're all caught up");
        }
    }

    var totalAll = response.stats && response.stats.total !== undefined ? response.stats.total : (response.total ?? 0);
    $('#itHeaderTotalBadge').text(totalAll);
    updateItToolbarIndicators();
}

// ── Failure Matrix & Concurrency Safety ────────────────────────────────────
var itPendingFollowUps = new Set();

function itHandleAjaxFailure(xhr, defaultMsg) {
    var status = xhr ? xhr.status : 0;
    if (status === 401) {
        Swal.fire({
            title: "Session Expired",
            text: "Your session has timed out. Please log in again.",
            icon: "warning",
            confirmButtonText: "Log In",
            customClass: { confirmButton: "btn btn-primary" }
        }).then(function() { window.location.reload(); });
        return;
    }
    if (status === 419) {
        Swal.fire({
            title: "Security Token Expired",
            text: "Your session token has expired. Page will refresh to renew security credentials.",
            icon: "info",
            confirmButtonText: "Refresh Now",
            customClass: { confirmButton: "btn btn-primary" }
        }).then(function() { window.location.reload(); });
        return;
    }
    if (status === 403) {
        Swal.fire({
            title: "Access Denied",
            text: "You do not have permission to perform this action.",
            icon: "error",
            confirmButtonText: "Ok",
            customClass: { confirmButton: "btn btn-primary" }
        });
        return;
    }
    if (status === 429) {
        Swal.fire({
            title: "Too Many Requests",
            text: "Please wait a few seconds before trying again.",
            icon: "warning",
            confirmButtonText: "Ok",
            customClass: { confirmButton: "btn btn-primary" }
        });
        return;
    }
    var msg = (xhr && xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : (defaultMsg || "An unexpected error occurred. Please try again.");
    Swal.fire({
        text: msg,
        icon: "error",
        confirmButtonText: "Ok",
        customClass: { confirmButton: "btn btn-primary" }
    });
}

function setFollowUpStatus(el, id, status) {
    document.querySelectorAll('.it-followup-menu.open').forEach(m => m.classList.remove('open'));

    if (itPendingFollowUps.has(id)) {
        return;
    }
    itPendingFollowUps.add(id);

    $.ajax({
        url: '{!! route('service_company.interview-schedule.followup_update') !!}',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', id: id, status: status },
        success: function (resp) {
            itPendingFollowUps.delete(id);
            if (resp && resp.status === 'success') {
                itRamCache.clear();
                JeroInterviewScheduleLocalStore.purge();
                patchInterviewRowDelta(id, { followup_status: status });
            } else {
                Swal.fire({ text: (resp && resp.msg) ? resp.msg : 'Could not update follow-up status.', icon: 'error', confirmButtonText: 'Ok' });
            }
        },
        error: function (xhr) {
            itPendingFollowUps.delete(id);
            itHandleAjaxFailure(xhr, 'Could not update follow-up status.');
        }
    });
}

// ── 3-Layer Caching Fetch Pipeline: RAM -> IndexedDB -> Server ─────────────
async function fetchCandidates(page, search, forceRefresh) {
    if (page !== undefined) { itCurrentPage = page; }

    var currentSearch = ($('#kt_tagify_2').val() || '').trim();
    var payload = {
        page:               itCurrentPage,
        size:               itCurrentSize,
        custom_status:      $('#status').val() || '',
        search:             currentSearch,
        custom_hiringtype:  $('#sortingfilter').val() || '',
        custom_search:      currentSearch,
        tab:                activeKpiFilter,
        kpi_filter:         activeKpiFilter,
        company_type:       (filterValues.company_type || $('#itCompanyTypeFilter').val() || 'All'),
        mode_filter:        filterValues.mode || 'All'
    };

    var cacheKey = JSON.stringify(payload);

    if (forceRefresh) {
        itRamCache.clear();
        if (window.JeroInterviewScheduleLocalStore && window.JeroInterviewScheduleLocalStore.purge) {
            await window.JeroInterviewScheduleLocalStore.purge();
        }
    } else {
        var lastMut = parseInt(localStorage.getItem('jero_it_last_mutation') || '0', 10);
        if (window._jero_it_ram_mounted_at && lastMut > window._jero_it_ram_mounted_at) {
            itRamCache.clear();
            window._jero_it_ram_mounted_at = Date.now();
        } else if (!window._jero_it_ram_mounted_at) {
            window._jero_it_ram_mounted_at = Date.now();
        }

        if (itRamCache.has(cacheKey)) {
            var rCached = itRamCache.get(cacheKey);
            if (rCached && typeof rCached === 'object' && typeof rCached.html === 'string') {
                renderInterviewData(rCached);
                return;
            } else {
                itRamCache.delete(cacheKey);
            }
        }

        try {
            var idbCached = await JeroInterviewScheduleLocalStore.get(cacheKey);
            if (idbCached && typeof idbCached === 'object' && typeof idbCached.html === 'string') {
                itRamCache.set(cacheKey, idbCached);
                renderInterviewData(idbCached);
                return;
            }
        } catch (idbErr) {
            console.warn('[JeroInterviewScheduleLocalStore] read error:', idbErr);
        }
    }

    if (itCurrentXhr && itCurrentXhr.readyState !== 4) {
        itCurrentXhr.abort();
    }

    if ($('#kt_tagify_2').val() !== '') {
        $('.loader_search').css({ display: 'inline-block' });
    } else {
        $('#tableLoading').addClass('show');
    }

    itCurrentXhr = $.ajax({
        url: '{!! route('service_company.interview-schedule.data_ajax') !!}',
        type: 'GET',
        data: payload,
        success: function(response) {
            if (response && typeof response === 'object' && typeof response.html === 'string') {
                if (itRamCache.size >= IT_MAX_RAM_CACHE) {
                    var oldestKey = itRamCache.keys().next().value;
                    itRamCache.delete(oldestKey);
                }
                itRamCache.set(cacheKey, response);
                JeroInterviewScheduleLocalStore.set(cacheKey, response);
            }
            renderInterviewData(response);
        },
        error: function(xhr, status, errorThrown) {
            if (status === 'abort') {
                return;
            }
            $('#tableLoading').removeClass('show');
            $('.loadingMessage').css({ display: 'none' });
            $('.loader_search').css({ display: 'none' });
            $('.page-loader').css('display', 'none');
            $('#showingText').text('0 interviews');

            if (xhr.status === 419) {
                itHandleAjaxFailure(xhr);
            } else if (xhr.status === 403) {
                $('#Interview-trackers').html(
                    '<div class="it-empty-card">' +
                    '  <div class="it-empty-icon" style="background:#fee2e2!important;color:#dc2626!important;"><i class="fas fa-lock"></i></div>' +
                    '  <div class="it-empty-title">Access Restricted</div>' +
                    '  <div class="it-empty-sub">You do not have permission to view interview schedules. Please contact your administrator.</div>' +
                    '</div>'
                );
            } else if (!navigator.onLine || xhr.status === 0) {
                $('#Interview-trackers').html(
                    '<div class="it-empty-card">' +
                    '  <div class="it-empty-icon" style="background:#fef2f2!important;color:#dc2626!important;"><i class="fas fa-wifi"></i></div>' +
                    '  <div class="it-empty-title">Network Connection Lost</div>' +
                    '  <div class="it-empty-sub">You are currently offline. Check your internet connection and click Sync.</div>' +
                    '</div>'
                );
            } else {
                var errDetail = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Please try syncing again or refreshing the page.';
                $('#Interview-trackers').html(
                    '<div class="it-empty-card">' +
                    '  <div class="it-empty-icon" style="background:#fee2e2!important;color:#dc2626!important;"><i class="fas fa-exclamation-triangle"></i></div>' +
                    '  <div class="it-empty-title">Unable to Load Interviews</div>' +
                    '  <div class="it-empty-sub">' + $('<div>').text(errDetail).html() + '</div>' +
                    '  <div style="margin-top:14px;"><button type="button" class="btn btn-sm btn-light-primary" onclick="fetchCandidates(1, \'\', true)"><i class="fas fa-sync-alt me-1"></i> Retry Sync</button></div>' +
                    '</div>'
                );
            }
        }
    });
}
window.fetchCandidates = fetchCandidates;
window.itRamCache = itRamCache;

// Close followup menus on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.it-followup-dropdown')) {
        document.querySelectorAll('.it-followup-menu.open').forEach(m => m.classList.remove('open'));
    }
});

// ===== Interview Link / Address popup =====
var ilCurrent = { id: null, link: '', type: 'link' };

function closeInterviewLink() {
    document.getElementById('ilOverlay').classList.remove('show');
}

$(document).on('click', '.open-interview-link', function () {
    ilCurrent.id = $(this).data('id');
    ilCurrent.link = ($(this).data('link') || '').toString();
    ilCurrent.type = $(this).data('type') || 'link';
    var name = $(this).data('name') || '';
    var isAddress = (ilCurrent.type === 'address');

    $('#ilTitle').text(isAddress ? 'Interview Address' : 'Interview Link');
    $('#ilCandidate').text(name ? ('Candidate: ' + name) : '');
    $('#ilLink').text(ilCurrent.link).attr('href', isAddress ? 'javascript:void(0);' : (ilCurrent.link || '#'));
    $('#ilOpenLabel').text(isAddress ? 'Open in Maps' : 'Open Link');

    $('#ilEmailBtn').prop('disabled', false);
    $('.il-email-label').removeClass('d-none');
    $('.il-email-loading').addClass('d-none');

    document.getElementById('ilOverlay').classList.add('show');
});

$(document).on('click', '#ilOpenBtn', function () {
    if (!ilCurrent.link) {
        Swal.fire({ text: 'Nothing to open.', icon: 'warning', confirmButtonText: 'Ok' });
        return;
    }
    var url = (ilCurrent.type === 'address')
        ? ('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(ilCurrent.link))
        : ilCurrent.link;
    window.open(url, '_blank', 'noopener');
});

$(document).on('click', '#ilEmailBtn', function () {
    if (!ilCurrent.id) return;
    var $btn = $(this);
    if ($btn.prop('disabled')) return;
    $btn.prop('disabled', true);
    $('.il-email-label').addClass('d-none');
    $('.il-email-loading').removeClass('d-none');
    $.ajax({
        url: '{!! route('service_company.interview-schedule.send_link') !!}',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', id: ilCurrent.id, link: ilCurrent.link, type: ilCurrent.type },
        success: function (resp) {
            $btn.prop('disabled', false);
            $('.il-email-label').removeClass('d-none');
            $('.il-email-loading').addClass('d-none');
            if (resp && resp.status === 'success') {
                itRamCache.clear();
                if (window.JeroInterviewScheduleLocalStore) {
                    window.JeroInterviewScheduleLocalStore.purge();
                }
                Swal.fire({ text: 'Emailed to ' + (resp.count || 0) + ' recipient(s).', icon: 'success', confirmButtonText: 'Ok' });
            } else {
                Swal.fire({ text: (resp && resp.msg) ? resp.msg : 'Could not send the email.', icon: 'error', confirmButtonText: 'Ok' });
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false);
            $('.il-email-label').removeClass('d-none');
            $('.il-email-loading').addClass('d-none');
            itHandleAjaxFailure(xhr, 'Could not send the email.');
        }
    });
});

$(document).on('click', '#ilOverlay', function (e) { if (e.target === this) closeInterviewLink(); });
$(document).on('keydown', function (e) { if (e.key === 'Escape') closeInterviewLink(); });

// ── Network Connectivity Watchers ──────────────────────────────────────────
window.addEventListener('offline', function() {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'warning',
        title: 'You are currently offline',
        showConfirmButton: false,
        timer: 4000
    });
});

window.addEventListener('online', function() {
    itRamCache.clear();
    if (window.JeroInterviewScheduleLocalStore) {
        window.JeroInterviewScheduleLocalStore.purge();
    }
    fetchCandidates(itCurrentPage);
});

// ── Real-time Reverb / Echo Synchronization & Reconnect Reconciliation ─────
@if(auth()->check() && auth()->user()->company_id)
$(document).ready(function() {
    if (typeof window.Echo !== 'undefined') {
        var companyChannel = 'company.{{ (int) auth()->user()->company_id }}';

        window.Echo.private(companyChannel)
            .listen('.interview.updated', function(e) {
                itRamCache.clear();
                if (window.JeroInterviewScheduleLocalStore) {
                    window.JeroInterviewScheduleLocalStore.purge();
                }
                if (e && e.interview_id) {
                    var patched = patchInterviewRowDelta(e.interview_id, e);
                    if (!patched) {
                        fetchCandidates(itCurrentPage);
                    }
                } else {
                    fetchCandidates(itCurrentPage);
                }
            })
            .listen('InterviewScheduled', function(e) {
                itRamCache.clear();
                if (window.JeroInterviewScheduleLocalStore) {
                    window.JeroInterviewScheduleLocalStore.purge();
                }
                var curUpcoming = parseInt($('#stat-upcoming').text(), 10) || 0;
                $('#stat-upcoming').text(curUpcoming + 1);
                fetchCandidates(itCurrentPage, null, true);
            })
            .listen('.candidate.drawer.updated', function(e) {
                itRamCache.clear();
                if (window.JeroInterviewScheduleLocalStore) {
                    window.JeroInterviewScheduleLocalStore.purge();
                }
                fetchCandidates(itCurrentPage, null, true);
            });

        try {
            var connector = window.Echo.connector;
            if (connector && connector.pusher && connector.pusher.connection) {
                connector.pusher.connection.bind('state_change', function(states) {
                    if (states.current === 'connected' && (states.previous === 'disconnected' || states.previous === 'unavailable')) {
                        itRamCache.clear();
                        if (window.JeroInterviewScheduleLocalStore) {
                            window.JeroInterviewScheduleLocalStore.purge();
                        }
                        fetchCandidates(itCurrentPage, null, true);
                    }
                });
            }
        } catch (err) {}
    }
});
@endif
</script>

@endpush
