{{--
  JERO ATS — Service Company Roles & Permissions (Rule 20 Modern Card + Sub-Card Design System)
  Intent: Enterprise-grade, page-isolated RBAC governance dashboard featuring Client-Tracker-inspired
          interactive 6-card KPI strip, Rule 20 Tier 1 Search/Filter Toolbar & Tier 2 Sub-Tabs,
          Tier 3 Primary Role Card + 5 Discrete Sub-Cards + Audit Strip, Client-First CSV/PDF export,
          Quick Overview Drawer, and Full-Screen Permissions Matrix Drawer.
  Tenancy & Security: Strictly scoped by auth()->user()->company_id in RolePermissionService.
                      Zero database queries executed inside this Blade template.
  Screen Fit: 100% fluid fit on 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow.
--}}
@extends('layouts.service_company_layout')

@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/icheck/skins/all.css') }}<?php echo TM_VERSION; ?>">
<link href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<style>
/* ==========================================================================
   Roles & Permissions — Page-Isolated Enterprise Card + Sub-Card System
   Font: Poppins strictly throughout; tabular-nums for all metrics
   ========================================================================== */
:root {
    --rp-primary: #009ef7;
    --rp-primary-deep: #2563eb;
    --rp-primary-soft: #eff6ff;
    --rp-success: #16a34a;
    --rp-success-soft: #ecfdf5;
    --rp-warning: #d97706;
    --rp-warning-soft: #fffbeb;
    --rp-danger: #dc2626;
    --rp-danger-soft: #fef2f2;
    --rp-indigo: #4f46e5;
    --rp-indigo-soft: #eef2ff;
    --rp-purple: #9333ea;
    --rp-purple-soft: #faf5ff;
    --rp-rose: #db2777;
    --rp-rose-soft: #fdf2f8;
    --rp-text-main: #0f172a;
    --rp-text-mid: #475569;
    --rp-text-muted: #64748b;
    --rp-text-light: #94a3b8;
    --rp-border: #e2e8f0;
    --rp-border-soft: #f1f5f9;
    --rp-bg-subcard: #f8fafc;
    --rpf-aside-w: 0px;
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
#kt_content, .content { margin-top: 0 !important; padding-top: 12px !important; padding-bottom: 0 !important; }
#kt_post, .post { margin-top: 0 !important; padding: 0 !important; }
#kt_content_container, body { overflow-x: hidden !important; }

.rp-page,
.rp-page *,
.rp-drawer,
.rp-drawer *,
.rpf-drawer,
.rpf-drawer * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
}

.rp-page {
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 14px 28px !important;
    color: var(--rp-text-main);
    -webkit-font-smoothing: antialiased;
}

.rp-num {
    font-variant-numeric: tabular-nums !important;
}

/* ==========================================================================
   HEADER TOOLBAR CARD (Client Tracker Inspired)
   ========================================================================== */
.rp-header-card {
    background: #ffffff !important;
    border: 1px solid var(--rp-border) !important;
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

.rp-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.rp-header-icon-box {
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

.rp-header-title-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}

.rp-header-title {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: var(--rp-text-main) !important;
    letter-spacing: -0.015em !important;
    margin: 0 !important;
    line-height: 1.25 !important;
}

.rp-verified-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #16a34a !important;
}

.rp-header-count-pill {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 2px 9px !important;
    border-radius: 999px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    background: var(--rp-primary-soft) !important;
    color: var(--rp-primary-deep) !important;
    border: 1px solid #bfdbfe !important;
    line-height: 1.3 !important;
}

.rp-header-subtitle {
    font-size: 12px !important;
    font-weight: 400 !important;
    color: var(--rp-text-muted) !important;
    margin-top: 2px !important;
    line-height: 1.35 !important;
}

.rp-header-actions {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
    flex-wrap: wrap !important;
}

.rp-hdr-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 36px !important;
    background: #ffffff !important;
    border: 1px solid var(--rp-border) !important;
    border-radius: 8px !important;
    padding: 0 13px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: var(--rp-text-main) !important;
    cursor: pointer !important;
    text-decoration: none !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.rp-hdr-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: var(--rp-primary-deep) !important;
}

.btn-rp-primary {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 36px !important;
    background: var(--rp-primary) !important;
    border: 1px solid var(--rp-primary) !important;
    border-radius: 8px !important;
    padding: 0 16px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #ffffff !important;
    cursor: pointer !important;
    text-decoration: none !important;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.22) !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.btn-rp-primary:hover {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
    transform: translateY(-0.5px);
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.28) !important;
}

.rp-dropdown-menu {
    min-width: 248px !important;
    padding: 6px !important;
    border-radius: 10px !important;
    border: 1px solid var(--rp-border) !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06) !important;
    background: #ffffff !important;
    margin-top: 6px !important;
    z-index: 1050 !important;
}

.rp-dropdown-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 8px 10px !important;
    border-radius: 7px !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: background 0.12s ease !important;
}

.rp-dropdown-item:hover { background: #f1f5f9 !important; }

.rp-drop-ico {
    width: 30px !important;
    height: 30px !important;
    border-radius: 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    font-size: 13px !important;
}
.rp-drop-ico.csv { background: #eff6ff; color: #2563eb; }
.rp-drop-ico.pdf { background: #fef2f2; color: #dc2626; }
.rp-drop-ico.rst { background: #fffbeb; color: #d97706; }

.rp-drop-title {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    display: block !important;
    line-height: 1.25 !important;
}
.rp-drop-sub {
    font-size: 10.5px !important;
    color: #64748b !important;
    display: block !important;
    margin-top: 1px !important;
}

/* ==========================================================================
   KPI STAT CARDS — Client Tracker Inspired (6 Cards in 1 Horizontal Row)
   ========================================================================== */
.rp-kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    gap: 9px !important;
    margin-bottom: 12px !important;
    width: 100% !important;
}

.rp-kpi-card {
    background: #ffffff !important;
    border: 1px solid var(--rp-border) !important;
    border-radius: 11px !important;
    padding: 10px 11px !important;
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

.rp-kpi-card:hover {
    transform: translateY(-1px) !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
}

.rp-kpi-top {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 6px !important;
    min-width: 0 !important;
    width: 100% !important;
}

.rp-kpi-icon {
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
.rp-kpi-icon svg { width: 17px !important; height: 17px !important; }
.rp-kpi-icon.blue   { background: #eff6ff !important; color: #2563eb !important; }
.rp-kpi-icon.green  { background: #ecfdf5 !important; color: #16a34a !important; }
.rp-kpi-icon.amber  { background: #fffbeb !important; color: #d97706 !important; }
.rp-kpi-icon.rose   { background: #fdf2f8 !important; color: #db2777 !important; }
.rp-kpi-icon.indigo { background: #eef2ff !important; color: #4f46e5 !important; }
.rp-kpi-icon.purple { background: #faf5ff !important; color: #9333ea !important; }

.rp-kpi-meta {
    min-width: 0 !important;
    flex: 1 !important;
    overflow: hidden !important;
}

.rp-kpi-num-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 4px !important;
}

.rp-kpi-num {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: var(--rp-text-main) !important;
    line-height: 1.1 !important;
    white-space: nowrap !important;
    font-variant-numeric: tabular-nums !important;
}

.rp-kpi-lbl {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: var(--rp-text-muted) !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

.rp-kpi-pill-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 1.5px 6px !important;
    background: #fef3c7 !important;
    border: 1px solid #fde68a !important;
    border-radius: 999px !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    color: #b45309 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
}

.rp-kpi-dot-pulse {
    width: 5px !important;
    height: 5px !important;
    border-radius: 50% !important;
    background: #d97706 !important;
    display: inline-block !important;
    animation: rpPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes rpPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.45; transform: scale(1.2); }
}

.rp-kpi-sub {
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
    padding-top: 4px !important;
    border-top: 1px dashed #f1f5f9 !important;
}
.rp-kpi-sub svg { width: 11px !important; height: 11px !important; flex-shrink: 0 !important; }
.rp-kpi-sub.slate  { color: #64748b !important; }
.rp-kpi-sub.green  { color: #16a34a !important; }
.rp-kpi-sub.amber  { color: #d97706 !important; }
.rp-kpi-sub.rose   { color: #db2777 !important; }
.rp-kpi-sub.indigo { color: #4f46e5 !important; }
.rp-kpi-sub.purple { color: #9333ea !important; }

/* ── Client Tracker Style Active KPI Highlight States ── */
.rp-kpi-card.active[data-kpi-filter="all"] {
    background: #eff6ff !important;
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 1.5px #2563eb, 0 4px 14px rgba(37, 99, 235, 0.12) !important;
}
.rp-kpi-card.active[data-kpi-filter="all"] .rp-kpi-icon { background: #ffffff !important; color: #2563eb !important; box-shadow: 0 1px 3px rgba(37, 99, 235, 0.16) !important; }
.rp-kpi-card.active[data-kpi-filter="all"] .rp-kpi-num  { color: #1d4ed8 !important; }
.rp-kpi-card.active[data-kpi-filter="all"] .rp-kpi-lbl  { color: #1e40af !important; font-weight: 700 !important; }

.rp-kpi-card.active[data-kpi-filter="system"] {
    background: #ecfdf5 !important;
    border-color: #34d399 !important;
    box-shadow: 0 0 0 1.5px #16a34a, 0 4px 14px rgba(22, 163, 74, 0.13) !important;
}
.rp-kpi-card.active[data-kpi-filter="system"] .rp-kpi-icon { background: #ffffff !important; color: #16a34a !important; box-shadow: 0 1px 3px rgba(22, 163, 74, 0.16) !important; }
.rp-kpi-card.active[data-kpi-filter="system"] .rp-kpi-num  { color: #15803d !important; }
.rp-kpi-card.active[data-kpi-filter="system"] .rp-kpi-lbl  { color: #166534 !important; font-weight: 700 !important; }

.rp-kpi-card.active[data-kpi-filter="custom"] {
    background: #fffbeb !important;
    border-color: #fbbf24 !important;
    box-shadow: 0 0 0 1.5px #d97706, 0 4px 14px rgba(217, 119, 6, 0.13) !important;
}
.rp-kpi-card.active[data-kpi-filter="custom"] .rp-kpi-icon { background: #ffffff !important; color: #d97706 !important; box-shadow: 0 1px 3px rgba(217, 119, 6, 0.18) !important; }
.rp-kpi-card.active[data-kpi-filter="custom"] .rp-kpi-num  { color: #b45309 !important; }
.rp-kpi-card.active[data-kpi-filter="custom"] .rp-kpi-lbl  { color: #92400e !important; font-weight: 700 !important; }

.rp-kpi-card.active[data-kpi-filter="high_access"] {
    background: #fdf2f8 !important;
    border-color: #f472b6 !important;
    box-shadow: 0 0 0 1.5px #db2777, 0 4px 14px rgba(219, 39, 119, 0.14) !important;
}
.rp-kpi-card.active[data-kpi-filter="high_access"] .rp-kpi-icon { background: #ffffff !important; color: #db2777 !important; box-shadow: 0 1px 3px rgba(219, 39, 119, 0.16) !important; }
.rp-kpi-card.active[data-kpi-filter="high_access"] .rp-kpi-num  { color: #9d174d !important; }
.rp-kpi-card.active[data-kpi-filter="high_access"] .rp-kpi-lbl  { color: #831843 !important; font-weight: 700 !important; }

.rp-kpi-card.active[data-kpi-filter="staffed"] {
    background: #eef2ff !important;
    border-color: #818cf8 !important;
    box-shadow: 0 0 0 1.5px #4f46e5, 0 4px 14px rgba(79, 70, 229, 0.12) !important;
}
.rp-kpi-card.active[data-kpi-filter="staffed"] .rp-kpi-icon { background: #ffffff !important; color: #4f46e5 !important; box-shadow: 0 1px 3px rgba(79, 70, 229, 0.16) !important; }
.rp-kpi-card.active[data-kpi-filter="staffed"] .rp-kpi-num  { color: #3730a3 !important; }
.rp-kpi-card.active[data-kpi-filter="staffed"] .rp-kpi-lbl  { color: #4338ca !important; font-weight: 700 !important; }

.rp-kpi-card.active[data-kpi-filter="unassigned"] {
    background: #faf5ff !important;
    border-color: #c084fc !important;
    box-shadow: 0 0 0 1.5px #9333ea, 0 4px 14px rgba(147, 51, 234, 0.12) !important;
}
.rp-kpi-card.active[data-kpi-filter="unassigned"] .rp-kpi-icon { background: #ffffff !important; color: #9333ea !important; box-shadow: 0 1px 3px rgba(147, 51, 234, 0.15) !important; }
.rp-kpi-card.active[data-kpi-filter="unassigned"] .rp-kpi-num  { color: #6b21a8 !important; }
.rp-kpi-card.active[data-kpi-filter="unassigned"] .rp-kpi-lbl  { color: #7e22ce !important; font-weight: 700 !important; }

/* ==========================================================================
   RULE 20 TIER 1 & TIER 2: TOOLBAR & DEDICATED WHITE SUB-TABS CARDS
   ========================================================================== */
.rp-toolbar-card {
    background: #ffffff !important;
    border: 1px solid var(--rp-border) !important;
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

.rp-search-subcard {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex: 1 1 280px !important;
    min-width: 220px !important;
    height: 38px !important;
    background: #f8fafc !important;
    border: 1px solid var(--rp-border) !important;
    border-radius: 8px !important;
    padding: 0 10px 0 12px !important;
    transition: all 0.15s ease !important;
}
.rp-search-subcard:hover { background: #ffffff !important; border-color: #cbd5e1 !important; }
.rp-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: var(--rp-primary) !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}
.rp-search-subcard i.fa-search { color: var(--rp-text-light) !important; font-size: 13px !important; flex-shrink: 0 !important; }
.rp-search-subcard input {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12.5px !important;
    font-weight: 500 !important;
    color: var(--rp-text-main) !important;
    width: 100% !important;
    height: 34px !important;
    padding: 0 !important;
}
.rp-search-subcard input::placeholder { color: var(--rp-text-light) !important; font-size: 12px !important; font-weight: 400 !important; }

.rp-search-clear-btn {
    border: none !important;
    background: #e2e8f0 !important;
    color: #64748b !important;
    width: 19px !important;
    height: 19px !important;
    border-radius: 50% !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    padding: 0 !important;
    transition: all 0.15s ease !important;
}
.rp-search-clear-btn:hover { background: #cbd5e1 !important; color: #0f172a !important; }

.rp-search-kbd {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    color: #94a3b8 !important;
    background: #ffffff !important;
    border: 1px solid var(--rp-border) !important;
    border-radius: 4px !important;
    padding: 2px 5px !important;
    line-height: 1 !important;
    flex-shrink: 0 !important;
    user-select: none !important;
}

.rp-inline-filters {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    flex-shrink: 0 !important;
}

.rp-filter-pill {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 38px !important;
    background: #f8fafc !important;
    border: 1px solid var(--rp-border) !important;
    border-radius: 8px !important;
    padding: 0 10px !important;
    transition: all 0.15s ease !important;
}
.rp-filter-pill:hover { background: #ffffff !important; border-color: #cbd5e1 !important; }
.rp-filter-pill:focus-within {
    background: #ffffff !important;
    border-color: var(--rp-primary) !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}
.rp-filter-pill.is-active {
    background: #f1faff !important;
    border-color: var(--rp-primary) !important;
}
.rp-filter-pill-lbl {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: var(--rp-text-muted) !important;
    white-space: nowrap !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}
.rp-filter-select {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: var(--rp-text-main) !important;
    height: 34px !important;
    padding-right: 4px !important;
    cursor: pointer !important;
}

.rp-reset-btn {
    height: 38px !important;
    padding: 0 12px !important;
    border-radius: 8px !important;
    border: 1px solid var(--rp-border) !important;
    background: #ffffff !important;
    color: var(--rp-primary-deep) !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}
.rp-reset-btn:hover { background: var(--rp-primary-soft) !important; border-color: #bfdbfe !important; }

/* Tier 2: Dedicated White Sub-Tabs Card */
.rp-tabs-card {
    background: #ffffff !important;
    border: 1px solid var(--rp-border) !important;
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

.rp-tabs-group {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
    min-width: 0 !important;
}

.rp-tab-btn {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 6px 13px !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: var(--rp-text-mid) !important;
    border: 1px solid var(--rp-border) !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
}
.rp-tab-btn:hover { background: #f8fafc !important; border-color: #cbd5e1 !important; color: var(--rp-text-main) !important; }
.rp-tab-btn.active {
    background: var(--rp-primary) !important;
    color: #ffffff !important;
    border-color: var(--rp-primary) !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.22) !important;
}

.rp-tab-badge {
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
    color: var(--rp-text-mid) !important;
    line-height: 1 !important;
}
.rp-tab-btn.active .rp-tab-badge { background: rgba(255, 255, 255, 0.28) !important; color: #ffffff !important; }
.rp-tab-btn:not(.active) .rp-tab-badge.badge-system     { background: #dcfce7 !important; color: #15803d !important; }
.rp-tab-btn:not(.active) .rp-tab-badge.badge-custom     { background: #fef3c7 !important; color: #b45309 !important; }
.rp-tab-btn:not(.active) .rp-tab-badge.badge-high       { background: #fee2e2 !important; color: #b91c1c !important; }
.rp-tab-btn:not(.active) .rp-tab-badge.badge-staffed    { background: #eef2ff !important; color: #4338ca !important; }
.rp-tab-btn:not(.active) .rp-tab-badge.badge-unassigned { background: #f1f5f9 !important; color: #475569 !important; }

.rp-tabs-right-meta {
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    font-size: 11px !important;
    color: var(--rp-text-muted) !important;
    font-weight: 500 !important;
    flex-shrink: 0 !important;
}

.rp-live-chip {
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
.rp-live-dot {
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    background: #16a34a !important;
    animation: rpPulse 2s infinite;
}

/* ==========================================================================
   RULE 20 TIER 3: PRIMARY ROLE CARD & 5 DISCRETE SUB-CARDS STREAM
   ========================================================================== */
.rp-cards-stream {
    width: 100% !important;
    max-width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
}

.rp-primary-card {
    background: #ffffff !important;
    border: 1px solid var(--rp-border) !important;
    border-left: 3.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
    cursor: pointer !important;
}
.rp-primary-card.is-staffed {
    border-left-color: #10b981 !important;
}
.rp-primary-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
    transform: translateY(-1px);
}
.rp-primary-card.is-staffed:hover {
    border-left-color: #059669 !important;
}
.rp-primary-card.selected {
    border-color: var(--rp-primary) !important;
    box-shadow: 0 0 0 1.5px rgba(0, 158, 247, 0.25), 0 4px 14px rgba(0, 158, 247, 0.1) !important;
}
.rp-primary-card.tab-hidden {
    display: none !important;
}

.rp-entity-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.6fr) minmax(0, 1.55fr) minmax(0, 1.4fr) minmax(0, 1.25fr) !important;
    gap: 8px !important;
    padding: 10px 12px !important;
    width: 100% !important;
    align-items: stretch !important;
}

.rp-subcard {
    background: var(--rp-bg-subcard) !important;
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

.rp-subcard-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 5px !important;
    min-height: 18px !important;
}

.rp-subcard-label {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.35px !important;
    color: var(--rp-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.rp-subcard-badges {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-shrink: 0 !important;
}

.rp-micro-pill {
    font-size: 9px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}
.rp-pill-neutral { background: #ffffff !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; }
.rp-pill-blue    { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.rp-pill-emerald { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
.rp-pill-amber   { background: #fffbeb !important; color: #b45309 !important; border: 1px solid #fde68a !important; }

/* Sub-Card 1: Role Identity */
.rp-scope-pill {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}
.scope-system { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.scope-custom { background: #faf5ff !important; color: #7c3aed !important; border: 1px solid #ddd6fe !important; }

.rp-access-pill,
.access-badge {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}
.badge-high   { background: #fee2e2 !important; color: #b91c1c !important; border: 1px solid #fecaca !important; }
.badge-medium { background: #eff6ff !important; color: #1d4ed8 !important; border: 1px solid #bfdbfe !important; }
.badge-low    { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }

.rp-identity-body {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    min-width: 0 !important;
}

.rp-role-icon-box {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    border-radius: 8px !important;
    border: 1px solid transparent !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}
.rp-role-icon-box svg { width: 19px !important; height: 19px !important; }

.rp-identity-text {
    flex: 1 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}
.rp-role-name {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: var(--rp-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
    line-height: 1.3 !important;
}
.rp-primary-card:hover .rp-role-name { color: var(--rp-primary-deep) !important; }

.rp-role-meta-line {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 10.5px !important;
    color: var(--rp-text-muted) !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
}
.rp-role-code { font-weight: 700 !important; color: #0284c7 !important; flex-shrink: 0 !important; }
.rp-meta-dot { color: #cbd5e1 !important; font-size: 9px !important; flex-shrink: 0 !important; }
.rp-role-kind { overflow: hidden !important; text-overflow: ellipsis !important; }

/* Sub-Card 2: Domain Coverage */
.rp-cov-list {
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
}
.rp-cov-row {
    display: grid !important;
    grid-template-columns: 74px minmax(0, 1fr) 34px !important;
    align-items: center !important;
    gap: 6px !important;
}
.rp-cov-name {
    font-size: 10px !important;
    font-weight: 500 !important;
    color: var(--rp-text-mid) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.rp-cov-track {
    height: 5px !important;
    border-radius: 99px !important;
    background: #e2e8f0 !important;
    overflow: hidden !important;
}
.rp-cov-fill {
    height: 100% !important;
    border-radius: 99px !important;
    transition: width 0.25s ease !important;
}
.rp-cov-frac {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    color: var(--rp-text-muted) !important;
    text-align: right !important;
    white-space: nowrap !important;
}

/* Sub-Card 3: Permission Groups */
.rp-modules-body {
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    min-width: 0 !important;
}
.rp-perm-tags {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 4px !important;
}
.rp-perm-tag {
    font-size: 10px !important;
    font-weight: 600 !important;
    background: #ffffff !important;
    color: var(--rp-text-mid) !important;
    border: 1px solid var(--rp-border) !important;
    padding: 2px 7px !important;
    border-radius: 5px !important;
    white-space: nowrap !important;
    max-width: 100% !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.35 !important;
}
.rp-perm-more {
    background: #eff6ff !important;
    color: #2563eb !important;
    border-color: #bfdbfe !important;
    cursor: pointer !important;
    transition: all 0.12s ease !important;
}
.rp-perm-more:hover {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
}

/* Sub-Card 4: Assigned Users */
.rp-members-body {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
    padding: 2px 0 !important;
    cursor: pointer !important;
}
.rp-avatars-stack {
    display: flex !important;
    align-items: center !important;
    flex-shrink: 0 !important;
}
.rp-mini-av {
    width: 26px !important;
    height: 26px !important;
    border-radius: 50% !important;
    border: 2px solid #ffffff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    margin-left: -7px !important;
    flex-shrink: 0 !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08) !important;
}
.rp-mini-av:first-child { margin-left: 0 !important; }
.rp-mini-av-more {
    background: #e2e8f0 !important;
    color: #334155 !important;
    font-size: 9px !important;
}

.rp-member-names {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    overflow: hidden !important;
}
.rp-member-primary {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: var(--rp-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}
.rp-member-extra {
    font-size: 10px !important;
    color: var(--rp-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.rp-empty-subtext {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: var(--rp-text-light) !important;
}

/* Sub-Card 5: Governance & Actions */
.rp-gov-actions {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    flex-wrap: wrap !important;
    margin-top: 2px !important;
}

.rp-act-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    height: 30px !important;
    padding: 0 9px !important;
    border-radius: 7px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
    border: 1px solid var(--rp-border) !important;
    background: #ffffff !important;
    color: var(--rp-text-mid) !important;
}
.rp-act-btn:hover { background: #f1f5f9 !important; color: var(--rp-text-main) !important; border-color: #cbd5e1 !important; }

.rp-act-edit {
    background: #eff6ff !important;
    border-color: #bfdbfe !important;
    color: #2563eb !important;
}
.rp-act-edit:hover {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
}

.rp-act-del {
    padding: 0 8px !important;
    color: #dc2626 !important;
    border-color: #fecaca !important;
    background: #fef2f2 !important;
}
.rp-act-del:hover {
    background: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #ffffff !important;
}

/* Sub-Card 6: Bottom Audit & Quick-Inspect Strip */
.rp-entity-bottom {
    background: #ffffff !important;
    border-top: 1px solid var(--rp-border-soft) !important;
    padding: 7px 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

.rp-bottom-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    min-width: 0 !important;
    font-size: 10.5px !important;
    color: var(--rp-text-muted) !important;
}

.rp-bottom-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
}
.rp-bottom-item i { color: var(--rp-text-light) !important; font-size: 10.5px !important; }
.rp-bottom-strong { color: var(--rp-text-main) !important; font-weight: 600 !important; }
.rp-dot-sep { color: #cbd5e1 !important; font-size: 9px !important; }

.rp-bottom-audit-warn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 1.5px 7px !important;
    border-radius: 999px !important;
    background: #fef2f2 !important;
    color: #b91c1c !important;
    border: 1px solid #fecaca !important;
    font-size: 10px !important;
    font-weight: 600 !important;
}

.rp-bottom-right {
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-shrink: 0 !important;
}

.rp-bottom-link {
    background: transparent !important;
    border: none !important;
    padding: 2px 6px !important;
    border-radius: 5px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: var(--rp-primary-deep) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    cursor: pointer !important;
    transition: background 0.12s ease !important;
}
.rp-bottom-link:hover { background: var(--rp-primary-soft) !important; }
.rp-bottom-link i { font-size: 9px !important; }

/* ── Add Custom Role Bottom Banner Card ── */
.rp-add-banner {
    background: #ffffff !important;
    border: 1.5px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 13px 18px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    margin-top: 2px !important;
}
.rp-add-banner:hover {
    border-color: var(--rp-primary) !important;
    background: #f8fbff !important;
}
.rp-add-banner-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}
.rp-add-plus {
    width: 36px !important;
    height: 36px !important;
    border-radius: 9px !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #2563eb !important;
    flex-shrink: 0 !important;
}
.rp-add-plus svg { width: 16px !important; height: 16px !important; }
.rp-add-title { font-size: 13px !important; font-weight: 700 !important; color: var(--rp-text-main) !important; }
.rp-add-sub { font-size: 11.5px !important; color: var(--rp-text-muted) !important; margin-top: 1px !important; }
.rp-add-cta-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 7px 14px !important;
    border-radius: 8px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    white-space: nowrap !important;
}
.rp-add-banner:hover .rp-add-cta-pill {
    background: var(--rp-primary) !important;
    color: #ffffff !important;
}

/* ── Empty State ── */
.rp-empty-state {
    display: none;
    background: #ffffff !important;
    border: 1px solid var(--rp-border) !important;
    border-radius: 10px !important;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 46px 16px;
    text-align: center;
}
.rp-empty-state.show { display: flex !important; }
.rp-empty-icon {
    width: 48px; height: 48px; border-radius: 50%; background: #eff6ff;
    display: flex; align-items: center; justify-content: center; color: #2563eb;
}
.rp-empty-icon svg { width: 22px; height: 22px; }
.rp-empty-title { font-size: 14px; font-weight: 700; color: var(--rp-text-main); }
.rp-empty-sub { font-size: 12px; color: var(--rp-text-muted); }

/* ==========================================================================
   ROLE DETAIL QUICK-VIEW DRAWER (#roleDrawer)
   ========================================================================== */
.rp-drawer-backdrop {
    position: fixed; inset: 0; background: rgba(15, 23, 42, .45);
    z-index: 1050; opacity: 0; visibility: hidden; transition: opacity .25s ease;
}
.rp-drawer-backdrop.open { opacity: 1; visibility: visible; }

.rp-drawer {
    position: fixed; top: 0; right: -460px; width: 440px; max-width: 94vw; height: 100vh;
    background: #fff; z-index: 1060; box-shadow: -10px 0 32px rgba(15, 23, 42, .16);
    transition: right .3s ease; display: flex; flex-direction: column;
}
.rp-drawer.open { right: 0; }

.rp-drawer-head { padding: 18px 20px 0; flex-shrink: 0; border-bottom: 1px solid var(--rp-border-soft); }
.rp-drawer-head-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
.rp-drawer-title-wrap { display: flex; align-items: center; gap: 10px; min-width: 0; }
.rp-drawer-icon {
    width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.rp-drawer-icon svg { width: 20px; height: 20px; }
.rp-drawer-title-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.rp-drawer-title { font-size: 14px; font-weight: 700; color: var(--rp-text-main); margin: 0; }
.rp-drawer-meta { font-size: 11px; color: var(--rp-text-muted); margin-top: 3px; }
.rp-drawer-head-actions { display: flex; gap: 4px; flex-shrink: 0; position: relative; }
.rp-drawer-icon-btn {
    width: 28px; height: 28px; border-radius: 7px; display: flex;
    align-items: center; justify-content: center; color: var(--rp-text-muted);
    cursor: pointer; background: transparent; border: none; flex-shrink: 0;
}
.rp-drawer-icon-btn:hover { background: #f1f5f9; color: var(--rp-text-main); }
.rp-drawer-icon-btn svg { width: 15px; height: 15px; }

.rp-drawer-menu {
    position: absolute; top: 34px; right: 0; background: #fff; border: 1px solid var(--rp-border);
    border-radius: 8px; box-shadow: 0 8px 24px rgba(15, 23, 42, .12); min-width: 155px;
    padding: 6px; display: none; z-index: 5;
}
.rp-drawer-menu.open { display: block; }
.rp-drawer-menu-item {
    display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 6px;
    font-size: 12px; color: var(--rp-text-mid); cursor: pointer; white-space: nowrap;
}
.rp-drawer-menu-item svg { width: 13px; height: 13px; flex-shrink: 0; }
.rp-drawer-menu-item:hover { background: #f1f5f9; }
.rp-drawer-menu-item.danger { color: #dc2626; }
.rp-drawer-menu-item.danger:hover { background: #fef2f2; }

.rp-drawer-tabs { display: flex; gap: 16px; padding: 14px 0 0; flex-shrink: 0; }
.rp-drawer-tab {
    padding: 0 0 10px; font-size: 12px; font-weight: 500; color: var(--rp-text-muted); cursor: pointer;
    border-bottom: 2px solid transparent; margin-bottom: -1px; user-select: none;
}
.rp-drawer-tab.active { color: var(--rp-primary-deep); font-weight: 600; border-bottom-color: var(--rp-primary); }

.rp-drawer-body { flex: 1; overflow-y: auto; padding: 18px 20px; }
.rp-drawer-pane { display: none; }
.rp-drawer-pane.active { display: block; }

.rp-drawer-footer {
    padding: 14px 20px; border-top: 1px solid var(--rp-border-soft); display: flex; gap: 8px; flex-shrink: 0;
}
.rp-drawer-footer .rp-dbtn { flex: 1; justify-content: center; }

.rp-dbtn {
    display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;
    padding: 9px 12px; font-size: 12px; font-weight: 600; cursor: pointer;
    border: 1px solid var(--rp-border); background: #fff; color: var(--rp-text-mid);
    transition: border-color .15s, background .15s;
}
.rp-dbtn svg { width: 13px; height: 13px; }
.rp-dbtn:hover { border-color: #cbd5e1; background: #f8fafc; color: var(--rp-text-main); }
.rp-dbtn.primary { background: var(--rp-primary); border-color: var(--rp-primary); color: #fff; }
.rp-dbtn.primary:hover { background: #0284c7; border-color: #0284c7; color: #fff; }
.rp-dbtn.danger { color: #dc2626; border-color: #fecaca; }
.rp-dbtn.danger:hover { background: #fef2f2; border-color: #dc2626; }

/* Drawer metric mini-cards */
.rp-dmetrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 16px; }
.rp-dmetric { background: #f8fafc; border: 1px solid #edf2f7; border-radius: 10px; padding: 12px 8px; text-align: center; }
.rp-dmetric-icon {
    width: 28px; height: 28px; border-radius: 7px; margin: 0 auto 6px;
    display: flex; align-items: center; justify-content: center;
}
.rp-dmetric-icon svg { width: 14px; height: 14px; }
.rp-dmetric-val { font-size: 14px; font-weight: 700; color: var(--rp-text-main); line-height: 1.2; font-variant-numeric: tabular-nums; }
.rp-dmetric-lbl { font-size: 10px; color: var(--rp-text-muted); margin-top: 2px; }

.rp-dcard { background: #f8fafc; border: 1px solid #edf2f7; border-radius: 10px; padding: 14px; margin-bottom: 14px; }
.rp-dcard-title { font-size: 12px; font-weight: 700; color: var(--rp-text-main); margin: 0 0 12px; }
.rp-dinfo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 14px; }
.rp-dinfo-lbl { font-size: 10px; color: var(--rp-text-muted); }
.rp-dinfo-val { font-size: 12px; font-weight: 600; color: var(--rp-text-main); margin-top: 3px; }

.rp-dcov-row { margin-bottom: 12px; }
.rp-dcov-row:last-child { margin-bottom: 0; }
.rp-dcov-top { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px; }
.rp-dcov-name { font-size: 11px; color: var(--rp-text-mid); font-weight: 500; }
.rp-dcov-frac { font-size: 10.5px; color: var(--rp-text-muted); font-weight: 600; font-variant-numeric: tabular-nums; }
.rp-dcov-track { height: 6px; border-radius: 4px; background: #e2e8f0; overflow: hidden; }
.rp-dcov-fill { height: 100%; border-radius: 4px; }

.rp-view-all-link {
    display: inline-flex; align-items: center; gap: 4px; font-size: 11.5px;
    color: var(--rp-primary-deep); font-weight: 600; cursor: pointer; margin-bottom: 14px;
}
.rp-view-all-link svg { width: 12px; height: 12px; }

.rp-dwarning {
    background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
    padding: 12px 14px; display: flex; gap: 10px; align-items: flex-start;
}
.rp-dwarning svg { color: #d97706; flex-shrink: 0; margin-top: 1px; width: 16px; height: 16px; }
.rp-dwarning-title { font-size: 12px; font-weight: 700; color: #92400e; margin: 0 0 3px; }
.rp-dwarning-text { font-size: 11px; color: #b45309; line-height: 1.5; margin: 0; }

.rp-dperm-group { margin-bottom: 16px; }
.rp-dperm-group:last-child { margin-bottom: 0; }
.rp-dperm-group-title {
    font-size: 10.5px; font-weight: 700; color: var(--rp-text-muted); text-transform: uppercase;
    letter-spacing: .3px; margin: 0 0 8px;
}
.rp-dperm-item {
    display: flex; align-items: center; gap: 8px; padding: 7px 0;
    border-bottom: 1px solid #f1f5f9; font-size: 12px; color: var(--rp-text-main);
}
.rp-dperm-item:last-child { border-bottom: none; }
.rp-dperm-item svg { width: 13px; height: 13px; color: #16a34a; flex-shrink: 0; }

.rp-duser-item { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid #f1f5f9; }
.rp-duser-item:last-child { border-bottom: none; }
.rp-duser-av {
    width: 32px; height: 32px; border-radius: 8px; display: flex;
    align-items: center; justify-content: center; color: #fff; font-weight: 700;
    font-size: 11px; flex-shrink: 0;
}
.rp-duser-name { font-size: 12px; font-weight: 600; color: var(--rp-text-main); }
.rp-duser-email { font-size: 10.5px; color: var(--rp-text-muted); }
.rp-dempty { font-size: 11.5px; color: var(--rp-text-light); text-align: center; padding: 24px 8px; }

/* ==========================================================================
   ADD / EDIT ROLE FORM DRAWER (.rpf-*) — Harmonized Enterprise Styling
   ========================================================================== */
#roleFormBody, #roleFormBody * { box-sizing: border-box; }
.rpf-backdrop {
    position: fixed; top: 0; right: 0; bottom: 0; left: var(--rpf-aside-w);
    background: rgba(15, 23, 42, .45);
    z-index: 1070; opacity: 0; visibility: hidden; transition: opacity .25s ease, left .2s ease;
}
.rpf-backdrop.open { opacity: 1; visibility: visible; }

.rpf-drawer {
    position: fixed; top: 0; right: 0; bottom: 0; left: var(--rpf-aside-w); height: 100vh;
    background: #f8fafc; z-index: 1080; box-shadow: -12px 0 40px rgba(15, 23, 42, .20);
    transform: translateX(100%);
    transition: transform .32s cubic-bezier(.4,0,.2,1), left .2s ease;
    display: flex; flex-direction: column; overflow: hidden;
}
.rpf-drawer.open { transform: translateX(0); }

.rpf-container { max-width: 1600px; margin: 0 auto; width: 100%; box-sizing: border-box; }
.rpf-loading { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; color: var(--rp-text-muted); font-size: 12px; }
.rpf-spinner { width: 26px; height: 26px; border: 3px solid #e2e8f0; border-top-color: var(--rp-primary); border-radius: 50%; animation: rpf-spin .7s linear infinite; }
@keyframes rpf-spin { to { transform: rotate(360deg); } }

.rpf-form-slot { flex: 1; display: flex; flex-direction: column; min-height: 0; width: 100%; }
.rpf-form { display: flex; flex-direction: column; flex: 1; min-height: 0; width: 100%; }

.rpf-head { background: #fff; padding: 14px 0; border-bottom: 1px solid var(--rp-border); flex-shrink: 0; }
.rpf-head-inner { display: flex; align-items: center; gap: 12px; padding: 0 24px; }
.rpf-head-icon { width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; border: 1px solid #bfdbfe; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rpf-head-icon svg { width: 20px; height: 20px; color: #2563eb; }
.rpf-head-titles { min-width: 0; flex: 1; }
.rpf-head-title-row { display: flex; align-items: center; gap: 8px; }
.rpf-head-title { font-size: 15px; font-weight: 700; color: var(--rp-text-main); margin: 0; }
.rpf-badge-active { font-size: 10px; font-weight: 600; color: #16a34a; background: #ecfdf5; border: 1px solid #bbf7d0; border-radius: 20px; padding: 2px 9px 2px 7px; display: inline-flex; align-items: center; gap: 4px; }
.rpf-badge-active::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: #16a34a; }
.rpf-head-sub { font-size: 11.5px; color: var(--rp-text-muted); margin: 2px 0 0; }
.rpf-head-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.rpf-head-close { width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: var(--rp-text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rpf-head-close:hover { background: #f1f5f9; color: var(--rp-text-main); }
.rpf-head-close svg { width: 16px; height: 16px; }

.rpf-warn { margin: 0 0 14px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; }
.rpf-warn svg { width: 16px; height: 16px; color: #d97706; flex-shrink: 0; }
.rpf-warn-text { font-size: 11.5px; color: #b45309; }
.rpf-warn-text b { color: #92400e; font-weight: 600; }

.rpf-body { flex: 1; overflow-y: auto; min-height: 0; }
.rpf-body-inner { padding: 18px 24px 24px; }

.rpf-summary { background: #fff; border: 1px solid var(--rp-border); border-radius: 12px; box-shadow: 0 1px 3px rgba(15,23,42,.04); padding: 14px 16px; margin-bottom: 14px; display: flex; align-items: flex-start; gap: 18px; }
.rpf-sum-col { min-width: 0; }
.rpf-sum-name  { flex: 1.6 1 160px; }
.rpf-sum-desc  { flex: 2 1 180px; }
.rpf-sum-users { flex: 1.1 1 120px; }
.rpf-sum-access{ flex: 0.9 1 90px; }
.rpf-sum-perms { flex: 1.5 1 150px; }
.rpf-sum-col + .rpf-sum-col { border-left: 1px solid #f1f5f9; padding-left: 18px; }
.rpf-field-label { font-size: 10px; font-weight: 600; color: var(--rp-text-muted); text-transform: uppercase; letter-spacing: .3px; margin-bottom: 5px; display: block; }
.rpf-field-label .req { color: #dc2626; }
.rpf-input { width: 100%; border: 1px solid var(--rp-border); border-radius: 8px; padding: 9px 12px; font-size: 12px; color: var(--rp-text-main); background: #f8fafc; outline: none; transition: border-color .15s, background .15s; }
.rpf-input:focus { border-color: var(--rp-primary); background: #fff; box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12); }
.rpf-input[readonly] { color: var(--rp-text-muted); cursor: not-allowed; background: #f1f5f9; }
.rpf-lock-hint { display: flex; align-items: center; gap: 4px; margin-top: 4px; font-size: 9.5px; color: var(--rp-text-light); }
.rpf-lock-hint svg { width: 11px; height: 11px; flex-shrink: 0; }
.rpf-summary-users { display: flex; align-items: center; gap: 6px; }
.rpf-uav { width: 26px; height: 26px; border-radius: 50%; border: 2px solid #fff; margin-left: -6px; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #fff; }
.rpf-uav:first-child { margin-left: 0; }
.rpf-users-count { font-size: 11px; color: var(--rp-text-mid); font-weight: 500; }
.rpf-access-pill { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.rpf-access-pill::before { content:''; width:6px; height:6px; border-radius:50%; background: currentColor; }
.rpf-prog-top { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px; }
.rpf-prog-frac { font-size: 12px; font-weight: 700; color: var(--rp-text-main); }
.rpf-prog-frac span { color: var(--rp-text-muted); font-weight: 500; }
.rpf-prog-pct { font-size: 11px; font-weight: 600; color: var(--rp-primary-deep); }
.rpf-prog-track { height: 6px; border-radius: 4px; background: #e2e8f0; overflow: hidden; }
.rpf-prog-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, #009ef7, #2563eb); transition: width .3s ease; }

.rpf-tabs { display: flex; gap: 18px; border-bottom: 1px solid var(--rp-border); margin-bottom: 14px; }
.rpf-tab { padding: 0 0 10px; font-size: 12px; font-weight: 500; color: var(--rp-text-muted); cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; user-select: none; }
.rpf-tab.active { color: var(--rp-primary-deep); font-weight: 600; border-bottom-color: var(--rp-primary); }

.rpf-toolbar { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; }
.rpf-quick-label { font-size: 11px; font-weight: 600; color: var(--rp-text-mid); margin-right: 2px; }
.rpf-qbtn { background: #fff; border: 1px solid var(--rp-border); border-radius: 7px; padding: 6px 11px; font-size: 11px; font-weight: 500; color: var(--rp-text-mid); cursor: pointer; transition: all .15s; }
.rpf-qbtn:hover { border-color: var(--rp-primary); color: var(--rp-primary-deep); }
.rpf-qbtn.primary { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; font-weight: 600; }
.rpf-search { flex: 1 1 150px; display: flex; align-items: center; gap: 7px; background: #fff; border: 1px solid var(--rp-border); border-radius: 7px; padding: 6px 11px; min-width: 130px; }
.rpf-search svg { width: 13px; height: 13px; color: var(--rp-text-light); flex-shrink: 0; }
.rpf-search input { border: none; outline: none; background: transparent; font-size: 11.5px; color: var(--rp-text-main); width: 100%; }

.rpf-grid { display: flex; gap: 14px; align-items: flex-start; }
.rpf-main { flex: 1 1 auto; min-width: 0; }
.rpf-aside { flex: 0 0 240px; display: flex; flex-direction: column; gap: 12px; }

.rpf-ptable { display: flex; flex-direction: column; gap: 12px; }
.rpf-ptable-head, .rpf-row { display: grid; grid-template-columns: 1fr 88px 88px 88px; align-items: center; }
.rpf-ptable-head {
    position: sticky; top: 0; z-index: 3; background: #fff;
    border: 1px solid var(--rp-border); border-radius: 10px; box-shadow: 0 1px 3px rgba(15,23,42,.04);
}
.rpf-th { padding: 10px 14px; font-size: 10.5px; font-weight: 700; color: var(--rp-text-muted); text-transform: uppercase; letter-spacing: .3px; }
.rpf-th-action { display: flex; flex-direction: column; align-items: center; gap: 3px; text-align: center; }
.rpf-th-action svg { width: 14px; height: 14px; color: var(--rp-primary-deep); }

.rpf-sec-group {
    background: #fff; border: 1px solid var(--rp-border); border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15,23,42,.04); overflow: hidden;
}
.rpf-sec-line { display: flex; align-items: center; gap: 10px; padding: 12px 14px; cursor: pointer; user-select: none; }
.rpf-sec-line:hover { background: #f8fafc; }
.rpf-sec-group:not(.collapsed) .rpf-sec-line { border-bottom: 1px solid #f1f5f9; }
.rpf-sec-icon { width: 30px; height: 30px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rpf-sec-icon svg { width: 15px; height: 15px; color: #2563eb; }
.rpf-sec-names { flex: 1; display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.rpf-sec-title { font-size: 12.5px; font-weight: 700; color: var(--rp-text-main); }
.rpf-sec-subtitle { font-size: 10.5px; color: var(--rp-text-muted); }
.rpf-sec-count { background: #eff6ff; color: #2563eb; font-size: 9.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px; flex-shrink: 0; }
.rpf-sec-caret { width: 14px; height: 14px; color: var(--rp-text-light); transition: transform .2s; flex-shrink: 0; }
.rpf-sec-group.collapsed .rpf-sec-caret { transform: rotate(-90deg); }
.rpf-sec-group.collapsed .rpf-sec-rows { display: none; }
.rpf-sec-rows { padding: 10px 12px 12px; display: flex; flex-direction: column; gap: 8px; background: #f8fafc; }

.rpf-row {
    background: #fff; border: 1px solid var(--rp-border); border-radius: 9px;
    padding: 9px 12px; box-shadow: 0 1px 2px rgba(15,23,42,.03);
}
.rpf-row-label { font-size: 11.5px; color: var(--rp-text-mid); font-weight: 500; padding-left: 4px; }
.rpf-row-cell { display: flex; align-items: center; justify-content: center; }

.rpf-chk { display: inline-flex; align-items: center; gap: 7px; cursor: pointer; user-select: none; }
.rpf-chk input { position: absolute; opacity: 0; width: 0; height: 0; }
.rpf-chk-box { width: 17px; height: 17px; border-radius: 5px; border: 1.5px solid #cbd5e1; background: #fff; flex-shrink: 0; transition: all .15s; background-repeat: no-repeat; background-position: center; background-size: 11px; }
.rpf-chk input:checked + .rpf-chk-box { background-color: var(--rp-primary); border-color: var(--rp-primary); background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E"); }
.rpf-chk input:disabled + .rpf-chk-box { background-color: #f1f5f9; border-color: #e2e8f0; cursor: not-allowed; }
.rpf-chk input:disabled ~ .rpf-chk-lbl { color: #94a3b8; }
.rpf-chk-lbl { font-size: 11.5px; color: var(--rp-text-mid); font-weight: 500; }
.rpf-chk-center { margin: 0; }

.rpf-radio { display: inline-flex; align-items: center; gap: 7px; cursor: pointer; user-select: none; }
.rpf-radio input { position: absolute; opacity: 0; width: 0; height: 0; }
.rpf-radio-dot { width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #cbd5e1; background: #fff; flex-shrink: 0; transition: all .15s; position: relative; }
.rpf-radio input:checked + .rpf-radio-dot { border-color: var(--rp-primary); }
.rpf-radio input:checked + .rpf-radio-dot::after { content: ''; position: absolute; inset: 3px; border-radius: 50%; background: var(--rp-primary); }
.rpf-radio input:disabled + .rpf-radio-dot { background: #f1f5f9; border-color: #e2e8f0; }
.rpf-radio input:disabled ~ .rpf-radio-lbl { color: #94a3b8; }
.rpf-radio-lbl { font-size: 11.5px; color: var(--rp-text-mid); font-weight: 500; }
.rpf-radio-lbl small { display: block; font-size: 9.5px; color: var(--rp-text-light); font-weight: 400; }

.rpf-acard { background: #fff; border: 1px solid var(--rp-border); border-radius: 11px; box-shadow: 0 1px 3px rgba(15,23,42,.04); padding: 14px; }
.rpf-acard-title { font-size: 11.5px; font-weight: 700; color: var(--rp-text-main); margin: 0 0 12px; display: flex; align-items: center; gap: 6px; }
.rpf-acard-title svg { width: 14px; height: 14px; color: #2563eb; }

.rpf-donut-wrap { display: flex; align-items: center; gap: 12px; }
.rpf-donut { width: 84px; height: 84px; flex-shrink: 0; position: relative; }
.rpf-donut svg { transform: rotate(-90deg); }
.rpf-donut-center { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.rpf-donut-num { font-size: 13px; font-weight: 700; color: var(--rp-text-main); line-height: 1; }
.rpf-donut-cap { font-size: 8.5px; color: var(--rp-text-muted); margin-top: 1px; }
.rpf-legend { flex: 1; display: flex; flex-direction: column; gap: 6px; }
.rpf-legend-row { display: flex; align-items: center; gap: 6px; font-size: 10.5px; color: var(--rp-text-mid); }
.rpf-legend-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.rpf-legend-val { margin-left: auto; font-weight: 700; color: var(--rp-text-main); }

.rpf-scope-row { display: flex; align-items: center; gap: 9px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
.rpf-scope-row:last-child { border-bottom: none; }
.rpf-adv-row { display: flex; align-items: center; gap: 9px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
.rpf-adv-row:last-child { border-bottom: none; }
.rpf-adv-row .rpf-chk { width: 100%; }
.rpf-adv-grid { display: flex; flex-direction: column; }

.rpf-fbtn { display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; padding: 8px 14px; font-size: 12px; font-weight: 600; cursor: pointer; border: 1px solid var(--rp-border); background: #fff; color: var(--rp-text-mid); transition: all .15s; }
.rpf-fbtn:hover { border-color: #cbd5e1; background: #f8fafc; color: var(--rp-text-main); }
.rpf-fbtn svg { width: 13px; height: 13px; }
.rpf-fbtn.primary { background: var(--rp-primary); border-color: var(--rp-primary); color: #fff; }
.rpf-fbtn.primary:hover { background: #0284c7; border-color: #0284c7; }
.rpf-fbtn.danger { color: #dc2626; border-color: #fecaca; }
.rpf-fbtn.danger:hover { background: #fef2f2; border-color: #dc2626; }
.rpf-fbtn .indicator-progress { display: none; }
.rpf-fbtn.loading .indicator-label { display: none; }
.rpf-fbtn.loading .indicator-progress { display: inline-flex; align-items: center; gap: 6px; }

/* ==========================================================================
   PRINT-TO-PDF EXECUTIVE RBAC REPORT
   ========================================================================== */
#rp-print-report-canvas { display: none; }
@media print {
    body * { visibility: hidden !important; }
    #rp-print-report-canvas, #rp-print-report-canvas * { visibility: visible !important; }
    #rp-print-report-canvas {
        display: block !important;
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 12px 16px !important;
    }
    @page { size: A4 landscape; margin: 10mm; }
    .rp-print-header { border-bottom: 2px solid #009ef7; padding-bottom: 10px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: flex-end; }
    .rp-print-title { font-size: 16px; font-weight: 700; margin: 0; }
    .rp-print-sub { font-size: 10px; color: #64748b; margin-top: 2px; }
    .rp-print-meta { font-size: 9.5px; color: #475569; text-align: right; }
    .rp-print-table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .rp-print-table th { background: #f1f5f9; color: #1e293b; font-weight: 700; padding: 6px 8px; border-bottom: 2px solid #cbd5e1; text-align: left; }
    .rp-print-table td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; color: #334155; vertical-align: top; }
}

/* ==========================================================================
   RESPONSIVE BREAKPOINTS (15.6" 1366x768 & 1920x1080 + Tablet/Mobile)
   ========================================================================== */
@media (max-width: 1440px) {
    .rpf-aside { flex-basis: 218px; }
    .rpf-grid { gap: 12px; }
    .rp-entity-grid {
        grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.55fr) minmax(0, 1.5fr) minmax(0, 1.35fr) minmax(0, 1.3fr) !important;
    }
}

@media (max-width: 1240px) {
    .rp-kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
    .rp-entity-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    .rp-subcard-governance {
        grid-column: 1 / -1 !important;
    }
    .rpf-grid { flex-direction: column; }
    .rpf-aside { flex-basis: auto; width: 100%; flex-direction: row; flex-wrap: wrap; }
    .rpf-aside > .rpf-acard { flex: 1 1 240px; }
    .rpf-summary { flex-wrap: wrap; }
    .rpf-summary .rpf-sum-col + .rpf-sum-col { border-left: none; padding-left: 0; }
}

@media (max-width: 768px) {
    .rp-page { padding: 0 10px 20px !important; }
    .rp-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .rp-entity-grid { grid-template-columns: minmax(0, 1fr) !important; }
    .rp-header-card { flex-direction: column; align-items: stretch !important; }
    .rp-add-banner { flex-direction: column; align-items: flex-start !important; }
    .rp-drawer { width: 100%; max-width: 100vw; }
    .rpf-head-inner, .rpf-body-inner { padding-left: 14px; padding-right: 14px; }
    .rpf-head-inner { flex-wrap: wrap; }
    .rpf-head-actions { order: 3; width: 100%; }
    .rpf-ptable-head, .rpf-row { grid-template-columns: 1fr 48px 48px 48px; }
    .rpf-sec-subtitle { display: none; }
}

@media (max-width: 460px) {
    .rp-kpi-grid { grid-template-columns: minmax(0, 1fr) !important; }
}
</style>
@endpush

@section('content')

<?php
/* ── Role Permission Listing — Zero DB queries in view; pre-computed via RolePermissionService ── */

$cardPalette = $cardPalette ?? [
    ['bg' => '#eff6ff', 'stroke' => '#2563eb', 'icon' => 'shield-check'],
    ['bg' => '#eef2ff', 'stroke' => '#4f46e5', 'icon' => 'users'],
    ['bg' => '#ecfdf5', 'stroke' => '#16a34a', 'icon' => 'user'],
    ['bg' => '#fdf2f8', 'stroke' => '#db2777', 'icon' => 'message'],
    ['bg' => '#fffbeb', 'stroke' => '#d97706', 'icon' => 'currency'],
    ['bg' => '#fef2f2', 'stroke' => '#dc2626', 'icon' => 'shield'],
    ['bg' => '#f1f5f9', 'stroke' => '#64748b', 'icon' => 'settings'],
];

if (!function_exists('rpAccessLevel')) {
    function rpAccessLevel($grpCount, $roleName = '') {
        $name = strtolower($roleName);
        if (str_contains($name, 'admin') || $grpCount >= 5) return ['label' => 'High Access',   'class' => 'badge-high'];
        if ($grpCount >= 3)                                   return ['label' => 'Medium Access', 'class' => 'badge-medium'];
        return                                                       ['label' => 'Low Access',    'class' => 'badge-low'];
    }
}

$avColors = $avColors ?? ['#2563eb', '#4f46e5', '#16a34a', '#d97706', '#0891b2', '#db2777', '#9333ea', '#0f766e'];

if (!function_exists('rpCardIcon')) {
    function rpCardIcon($name, $stroke) {
        $icons = [
            'shield-check' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
            'users'        => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'user'         => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'message'      => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
            'currency'     => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
            'shield'       => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
            'settings'     => '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>',
        ];
        $d = $icons[$name] ?? $icons['settings'];
        return '<svg viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $d . '</svg>';
    }
}

/* Pre-compute additional KPI/tab counts from already-loaded collection (0 DB queries) */
$highAccessCount      = 0;
$staffedRolesCount    = 0;
$unassignedRolesCount = 0;
$uniquePermGroupsCount = count($groupTotals ?? []);

if (!empty($roles)) {
    foreach ($roles as $rItem) {
        $rStat   = $allRoleStats[$rItem->id] ?? [];
        $rUsers  = (int) ($rStat['userCount'] ?? 0);
        $rGroups = (int) ($rStat['grpCount'] ?? 0);
        $rAcc    = $rStat['access'] ?? rpAccessLevel($rGroups, $rItem->name ?? '');

        if (($rAcc['class'] ?? '') === 'badge-high') {
            $highAccessCount++;
        }
        if ($rUsers > 0) {
            $staffedRolesCount++;
        } else {
            $unassignedRolesCount++;
        }
    }
}
?>

<div class="toolbar" id="kt_toolbar" style="height:0;overflow:hidden;padding:0;margin:0;border:none;">
    <div id="kt_toolbar_container" class="container-fluid"></div>
</div>

<div class="rp-page">

    {{-- ══════════════════════════════════════════════════════════════
         ENTERPRISE HEADER TOOLBAR CARD (Client Tracker Inspired)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="rp-header-card">
        <div class="rp-header-left">
            <div class="rp-header-icon-box">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <div>
                <div class="rp-header-title-row">
                    <h1 class="rp-header-title">Roles &amp; Permissions</h1>
                    <span class="rp-verified-badge" title="Enterprise RBAC Governance">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="m9 12 2 2 4-4" stroke-width="2.4"/>
                        </svg>
                    </span>
                    <span class="rp-header-count-pill rp-num">{{ (int) ($totalRolesCount ?? 0) }} Roles</span>
                </div>
                <div class="rp-header-subtitle">Create, govern, and audit role-based access control (RBAC) policies across your organization.</div>
            </div>
        </div>

        <div class="rp-header-actions">
            {{-- Client-First Export Dropdown (0% Server Strain) --}}
            <div class="dropdown">
                <button type="button" class="rp-hdr-btn" id="rpExportDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Export RBAC matrix">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span>Export</span>
                    <i class="fas fa-chevron-down" style="font-size:10px;color:#64748b;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end rp-dropdown-menu" aria-labelledby="rpExportDropdownBtn">
                    <li>
                        <a class="rp-dropdown-item" href="javascript:void(0);" onclick="exportRolesCsv()">
                            <span class="rp-drop-ico csv"><i class="fas fa-file-csv"></i></span>
                            <span>
                                <span class="rp-drop-title">Export Role Matrix (CSV)</span>
                                <span class="rp-drop-sub">Instant browser-side snapshot</span>
                            </span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1 border-gray-200"></li>
                    <li>
                        <a class="rp-dropdown-item" href="javascript:void(0);" onclick="printRolesReport()">
                            <span class="rp-drop-ico pdf"><i class="fas fa-print"></i></span>
                            <span>
                                <span class="rp-drop-title">Save / Print Audit Report (PDF)</span>
                                <span class="rp-drop-sub">Executive RBAC summary</span>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            @if(in_array("add_roles_permission", $userPermissions))
                <button type="button" class="btn-rp-primary" id="addNewRoleBtn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Add New Role</span>
                </button>
            @endif
        </div>
    </div>

    {{-- ── KPI Stat Cards Row (Client Tracker Inspired) ── --}}
    @include('service_company.role-permission.components.role_stat_cards')

    {{-- ── Rule 20 Tier 1 Toolbar Card & Tier 2 Dedicated Sub-Tabs Card ── --}}
    @include('service_company.role-permission.components.role_filter_bar')

    {{-- ── Rule 20 Tier 3 Primary Role Card Stream ── --}}
    <div class="rp-cards-stream" id="rolesGrid">

        @if(!empty($roles))
            @foreach($roles as $rlist)
                @include('service_company.role-permission.components.role_card_item', ['rlist' => $rlist, 'loop' => $loop])
            @endforeach
        @endif

        {{-- Empty State --}}
        <div class="rp-empty-state" id="rolesEmptyState">
            <div class="rp-empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <div class="rp-empty-title">No matching roles found</div>
            <div class="rp-empty-sub">Try adjusting your search query, KPI filter, or category tab.</div>
            <button type="button" class="rp-reset-btn mt-2" onclick="document.getElementById('clearFilters').click()">
                <i class="fas fa-undo-alt"></i>
                <span>Reset Filters</span>
            </button>
        </div>

        {{-- Enterprise Create Custom Role CTA Banner --}}
        @if(in_array("add_roles_permission", $userPermissions))
            <div class="rp-add-banner" id="addNewRole">
                <div class="rp-add-banner-left">
                    <div class="rp-add-plus">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                    <div>
                        <div class="rp-add-title">Create Custom Access Role</div>
                        <div class="rp-add-sub">Define granular module-level Add, Edit, and View permissions tailored for your team.</div>
                    </div>
                </div>
                <span class="rp-add-cta-pill">
                    <i class="fas fa-plus"></i>
                    <span>Add New Role</span>
                </span>
            </div>
        @endif

    </div>
</div>

{{-- ── Client-First Print-to-PDF Report Canvas ── --}}
<div id="rp-print-report-canvas"></div>

{{-- ── Role Detail Quick-View Drawer ── --}}
<div class="rp-drawer-backdrop" id="roleDrawerBackdrop" onclick="closeRoleDrawer()"></div>
<div class="rp-drawer" id="roleDrawer">
    <div class="rp-drawer-head">
        <div class="rp-drawer-head-top">
            <div class="rp-drawer-title-wrap">
                <div class="rp-drawer-icon" id="drawerIcon"></div>
                <div>
                    <div class="rp-drawer-title-row">
                        <h3 class="rp-drawer-title" id="drawerTitle"></h3>
                        <span class="access-badge" id="drawerAccessBadge"></span>
                    </div>
                    <div class="rp-drawer-meta" id="drawerMeta"></div>
                </div>
            </div>
            <div class="rp-drawer-head-actions">
                <button type="button" class="rp-drawer-icon-btn" id="drawerMenuBtn" onclick="toggleDrawerMenu(event)" aria-label="More actions">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                </button>
                <div class="rp-drawer-menu" id="drawerMenu"></div>
                <button type="button" class="rp-drawer-icon-btn" onclick="closeRoleDrawer()" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>
        <div class="rp-drawer-tabs">
            <div class="rp-drawer-tab active" data-pane="overview" onclick="switchDrawerTab('overview')">Overview</div>
            <div class="rp-drawer-tab" data-pane="permissions" onclick="switchDrawerTab('permissions')" id="drawerTabPermissions">Permissions</div>
            <div class="rp-drawer-tab" data-pane="users" onclick="switchDrawerTab('users')" id="drawerTabUsers">Users</div>
        </div>
    </div>

    <div class="rp-drawer-body">
        <div class="rp-drawer-pane active" id="drawerPaneOverview"></div>
        <div class="rp-drawer-pane" id="drawerPanePermissions"></div>
        <div class="rp-drawer-pane" id="drawerPaneUsers"></div>
    </div>

    <div class="rp-drawer-footer" id="drawerFooter"></div>
</div>

{{-- ── Add / Edit Role Form Drawer (single wide right drawer, opened for both Add New Role and Edit) ── --}}
<div class="rpf-backdrop" id="roleFormBackdrop" onclick="closeRoleForm()"></div>
<div class="rpf-drawer" id="roleFormDrawer" aria-hidden="true">
    <div class="rpf-form-slot" id="roleFormBody"></div>
</div>

{{-- ── Legacy AJAX Modal ── --}}
<div class="modal fade bs-modal-md in" id="managePermissionModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">Loading...</div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}<?php echo TM_VERSION; ?>"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>
/* Zero Metronic toolbar height gap cleanly */
(function removeToolbarGap() {
    var targets = ['kt_wrapper', 'kt_content', 'kt_post'];
    function zeroGap() {
        targets.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) { el.style.setProperty('padding-top', '0', 'important'); el.style.setProperty('margin-top', '0', 'important'); }
        });
        document.body.style.setProperty('--kt-toolbar-height', '0px', 'important');
        document.body.style.setProperty('--kt-toolbar-height-tablet-and-mobile', '0px', 'important');
    }
    zeroGap();
    document.addEventListener('DOMContentLoaded', zeroGap);
    window.addEventListener('load', zeroGap);
    window.addEventListener('resize', zeroGap);
})();

/* ── Existing permission assignment logic (preserved) ── */
$(function () { $('.assign-role-permission').on('change', assignRollPermission); });

var animating = false;
var masteranimate = false;

var assignRollPermission = function () {
    var roleId = $(this).data('role-id');
    var permissionId = $(this).data('permission-id');
    var assignPermission = $(this).is(':checked') ? 'yes' : 'no';
    var url = '{{route('service_company.role-permission.store')}}';
    $.easyAjax({ url: url, type: "POST", data: { 'roleId': roleId, 'permissionId': permissionId, 'assignPermission': assignPermission, '_token': '{{ csrf_token() }}' } });
};

$('.select_all_permission').change(function () {
    var roleId = $(this).val();
    if ($(this).is(':checked')) {
        var url = '{{ route('service_company.role-permission.assignAllPermission') }}';
        $.easyAjax({ url: url, type: "POST", data: { 'roleId': roleId, '_token': '{{ csrf_token() }}' },
            success: function () {
                masteranimate = true;
                if (!animating) {
                    var masterStatus = true;
                    $('.assign-role-permission').off('change');
                    $('input.permission_'+roleId).each(function(index){
                        if ($('input.permission_'+roleId)[index].checked != masterStatus) $(this).trigger('click');
                    });
                    $('.assign-role-permission').on('change', assignRollPermission);
                }
                masteranimate = false;
            }
        });
    } else {
        var url = '{{ route('service_company.role-permission.removeAllPermission') }}';
        $.easyAjax({ url: url, type: "POST", data: { 'roleId': roleId, '_token': '{{ csrf_token() }}' },
            success: function () {
                masteranimate = true;
                if (!animating) {
                    var masterStatus = false;
                    $('.assign-role-permission').off('change');
                    $('input.permission_'+roleId).each(function(index){
                        if ($('input.permission_'+roleId)[index].checked != masterStatus) $(this).trigger('click');
                    });
                    $('.assign-role-permission').on('change', assignRollPermission);
                }
                masteranimate = false;
            }
        });
    }
});

$('.show-members').click(function () {
    var id = $(this).data('role-id');
    var url = '{{ route('service_company.role-permission.showMembers', ':id')}}';
    url = url.replace(':id', id);
    $('#modelHeading').html('Role Members');
    $.ajaxModal('#managePermissionModal', url);
});

/* ── Global Filter & Tab Synchronization State ── */
window.rpActiveTab = 'all';

function syncTabAndKpiUI(tabKey) {
    window.rpActiveTab = tabKey || 'all';
    $('.rp-kpi-card.rp-kpi-filter').removeClass('active');
    $('.rp-kpi-card.rp-kpi-filter[data-kpi-filter="' + window.rpActiveTab + '"]').addClass('active');

    $('.js-rp-tab').removeClass('active');
    $('.js-rp-tab[data-tab="' + window.rpActiveTab + '"]').addClass('active');
}

function applyRoleKpiFilter(tabKey) {
    syncTabAndKpiUI(tabKey);
    if (typeof window.rpFilterCards === 'function') {
        window.rpFilterCards();
    }
}

function applyRoleSubTab(tabKey) {
    syncTabAndKpiUI(tabKey);
    if (typeof window.rpFilterCards === 'function') {
        window.rpFilterCards();
    }
}

/* ── Client-First CSV & Print-to-PDF Export Engine ── */
function collectVisibleRoleDetails() {
    var rows = [];
    document.querySelectorAll('#rolesGrid .rp-primary-card:not(.tab-hidden)').forEach(function (card) {
        try {
            var d = JSON.parse(card.getAttribute('data-role-detail') || '{}');
            if (d && d.id) rows.push(d);
        } catch (e) {}
    });
    return rows;
}

function exportRolesCsv() {
    var roles = collectVisibleRoleDetails();
    if (!roles.length) {
        Swal.fire({ icon: 'info', title: 'No roles to export', text: 'Adjust your filters to include at least one role.' });
        return;
    }
    var csvEscape = function (v) {
        var s = String(v == null ? '' : v).replace(/"/g, '""');
        return '"' + s + '"';
    };
    var lines = [
        ['Role Code', 'Role Name', 'Role Type', 'Access Level', 'Permission Groups', 'Total Permissions', 'Assigned Users', 'Assigned Member Names', 'Created On', 'Last Updated'].map(csvEscape).join(',')
    ];
    roles.forEach(function (r) {
        var members = (r.users || []).map(function (u) { return u.name + ' <' + u.email + '>'; }).join('; ');
        lines.push([
            r.roleCode || '',
            r.name || '',
            r.isCustom ? 'Custom Role' : 'System Role',
            (r.access && r.access.label) ? r.access.label : '',
            r.grpCount || 0,
            r.totalPerms || 0,
            r.userCount || 0,
            members || 'None',
            r.createdOn || '—',
            r.updatedOn || '—'
        ].map(csvEscape).join(','));
    });

    var blob = new Blob([lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'jero-rbac-roles-matrix-' + new Date().toISOString().slice(0, 10) + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function printRolesReport() {
    var roles = collectVisibleRoleDetails();
    var canvas = document.getElementById('rp-print-report-canvas');
    if (!canvas) return;
    var rowsHtml = roles.map(function (r) {
        var members = (r.users || []).map(function (u) { return esc(u.name); }).join(', ') || 'No users assigned';
        return '<tr>' +
            '<td><strong>' + esc(r.roleCode) + '</strong></td>' +
            '<td><strong>' + esc(r.name) + '</strong></td>' +
            '<td>' + (r.isCustom ? 'Custom Role' : 'System Role') + '</td>' +
            '<td>' + esc((r.access && r.access.label) || '') + '</td>' +
            '<td>' + (r.grpCount || 0) + ' groups (' + (r.totalPerms || 0) + ' perms)</td>' +
            '<td>' + (r.userCount || 0) + '</td>' +
            '<td>' + members + '</td>' +
            '<td>' + esc(r.updatedOn || '—') + '</td>' +
        '</tr>';
    }).join('');

    canvas.innerHTML =
        '<div class="rp-print-header">' +
            '<div><h2 class="rp-print-title">Roles &amp; Permissions Governance Matrix</h2><div class="rp-print-sub">Organization RBAC Policy Snapshot</div></div>' +
            '<div class="rp-print-meta">Generated: ' + esc(new Date().toLocaleString()) + '<br>Roles Included: ' + roles.length + '</div>' +
        '</div>' +
        '<table class="rp-print-table">' +
            '<thead><tr><th>Code</th><th>Role Name</th><th>Type</th><th>Access Tier</th><th>Permissions</th><th>Users</th><th>Assigned Members</th><th>Updated</th></tr></thead>' +
            '<tbody>' + rowsHtml + '</tbody>' +
        '</table>';

    window.print();
}

/* ── Main Document Ready Controllers ── */
$(document).ready(function () {

    /* Open Add/Edit Role Form Drawer */
    $('#addNewRole, #addNewRoleBtn').on('click', function (e) {
        e.preventDefault();
        var url = "{{ route('service_company.settings.loadRole',':id') }}".replace(':id', 0);
        loadRoleFormDrawer(url);
    });

    $(document).on('click', '.editRole', function (e) {
        e.preventDefault();
        if (typeof closeRoleDrawer === 'function') closeRoleDrawer();
        var id = $(this).data('id');
        var url = "{{ route('service_company.settings.loadEditRole',':id') }}".replace(':id', id);
        loadRoleFormDrawer(url);
    });

    /* Delete role */
    $(document).on('click', '.role_delete', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: "Are you sure?",
            text: "This custom role will be deleted permanently. Assigned users will be reassigned to Recruiter.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, delete it!"
        }).then(function (result) {
            if (result.isConfirmed) {
                var url = "{{ route('service_company.settings.manualDeleteRole',':id') }}".replace(':id', id);
                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function (response) {
                        if (response.status === "success") {
                            Swal.fire("Deleted", "Role has been deleted successfully", "success")
                                .then(function () { location.reload(); });
                        }
                    }
                });
            }
        });
    });

    /* Debounced Search (250ms) + Interactive Clear Button + '/' Shortcut */
    var rpSearchTimer = null;
    $('#roleSearch').on('input', function () {
        var val = $(this).val();
        if (val.length > 0) {
            $('#btnRoleSearchClear').show();
        } else {
            $('#btnRoleSearchClear').hide();
        }
        clearTimeout(rpSearchTimer);
        rpSearchTimer = setTimeout(function () {
            window.rpFilterCards();
        }, 200);
    });

    $('#btnRoleSearchClear').on('click', function () {
        $('#roleSearch').val('').focus();
        $(this).hide();
        window.rpFilterCards();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && !/INPUT|TEXTAREA|SELECT/.test((document.activeElement || {}).tagName)) {
            e.preventDefault();
            var inp = document.getElementById('roleSearch');
            if (inp) inp.focus();
        }
    });

    /* Dropdown filters */
    $('#filterRoleType, #filterAccessLevel').on('change', function () {
        $(this).closest('.rp-filter-pill').toggleClass('is-active', $(this).val() !== '');
        window.rpFilterCards();
    });

    /* Reset Filters */
    $('#clearFilters').on('click', function () {
        $('#roleSearch').val('');
        $('#btnRoleSearchClear').hide();
        $('#filterRoleType, #filterAccessLevel').val('');
        $('.rp-filter-pill').removeClass('is-active');
        syncTabAndKpiUI('all');
        window.rpFilterCards();
    });

    /* Sort */
    $('#sortRoles').on('change', function () {
        var sortBy = $(this).val();
        var grid = $('#rolesGrid');
        var cards = grid.find('.rp-primary-card').toArray();
        cards.sort(function (a, b) {
            if (sortBy === 'name')        return String($(a).data('role-name')).localeCompare(String($(b).data('role-name')));
            if (sortBy === 'users')       return parseInt($(b).data('user-count') || 0) - parseInt($(a).data('user-count') || 0);
            if (sortBy === 'perms')       return parseInt($(b).data('grp-count') || 0) - parseInt($(a).data('grp-count') || 0);
            if (sortBy === 'total_perms') return parseInt($(b).data('perm-count') || 0) - parseInt($(a).data('perm-count') || 0);
            return 0;
        });
        grid.find('.rp-primary-card').detach();
        var frag = document.createDocumentFragment();
        cards.forEach(function (c) { frag.appendChild(c); });
        grid.prepend(frag);
    });

    /* Unified Filtering Engine across KPI Cards, Tier 2 Sub-Tabs, Dropdowns, and Search */
    window.rpFilterCards = function () {
        var search       = ($('#roleSearch').val() || '').toLowerCase().trim();
        var typeFilter   = $('#filterRoleType').val() || '';
        var accessFilter = $('#filterAccessLevel').val() || '';
        var activeTab    = window.rpActiveTab || 'all';
        var visibleCount = 0;

        $('.rp-primary-card').each(function () {
            var $card      = $(this);
            var roleType   = $card.data('role-type');
            var roleAccess = $card.data('access-level');
            var userCount  = parseInt($card.data('user-count') || 0, 10);
            var corpus     = String($card.attr('data-search-text') || $card.data('role-name') || '');

            var tabMatch = true;
            if (activeTab === 'system')      tabMatch = (roleType === 'system');
            else if (activeTab === 'custom') tabMatch = (roleType === 'custom');
            else if (activeTab === 'high_access') tabMatch = (roleAccess === 'badge-high');
            else if (activeTab === 'staffed')     tabMatch = (userCount > 0);
            else if (activeTab === 'unassigned')  tabMatch = (userCount === 0);

            var typeMatch   = !typeFilter || (roleType === typeFilter);
            var accessMatch = !accessFilter || (roleAccess === accessFilter);
            var searchMatch = !search || corpus.indexOf(search) !== -1;

            var isMatch = tabMatch && typeMatch && accessMatch && searchMatch;
            $card.toggleClass('tab-hidden', !isMatch);
            if (isMatch) visibleCount++;
        });

        $('#rpVisibleCount').text(visibleCount);
        $('#rolesEmptyState').toggleClass('show', visibleCount === 0);
    };

    /* Apply default sort ("Most Users") on load */
    $('#sortRoles').trigger('change');
    window.rpFilterCards();
});

/* ── Role Detail Drawer ── */
var avColors = ['#2563eb','#4f46e5','#16a34a','#d97706','#0891b2','#db2777','#9333ea','#0f766e'];
var currentDrawerRole = null;

function esc(str) {
    return $('<div>').text(str == null ? '' : str).html();
}

function getInitials(name) {
    var parts = String(name || '').trim().split(/\s+/).filter(Boolean);
    if (parts.length >= 2) {
        return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
    }
    return parts.length ? parts[0].substring(0, 2).toUpperCase() : '?';
}

function svgIcon(path, stroke) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="' + (stroke || 'currentColor') + '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + path + '</svg>';
}

var ICON_SHIELD_CHECK = '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>';
var ICON_USERS        = '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>';
var ICON_SHIELD       = '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>';
var ICON_CHECK        = '<polyline points="20 6 9 17 4 12"/>';
var ICON_ALERT        = '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>';
var ICON_EDIT         = '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>';
var ICON_COPY         = '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>';
var ICON_TRASH        = '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>';
var ICON_ARROW_RIGHT  = '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>';

function openRoleDrawer(el, initialTab) {
    if (!el) return;
    var data = JSON.parse(el.getAttribute('data-role-detail') || '{}');
    currentDrawerRole = data;

    document.querySelectorAll('.rp-primary-card').forEach(function (c) { c.classList.remove('selected'); });
    el.classList.add('selected');

    var badgeClass = (data.access && data.access.class) ? data.access.class : 'badge-low';
    var badgeLabel = (data.access && data.access.label) ? data.access.label : 'Low Access';
    var typeLabel  = data.isCustom ? 'Custom Role' : 'System Role';

    document.getElementById('drawerIcon').style.background = (data.palette && data.palette.bg) ? data.palette.bg : '#eff6ff';
    document.getElementById('drawerIcon').innerHTML = svgIcon(ICON_SHIELD_CHECK, (data.palette && data.palette.stroke) ? data.palette.stroke : '#2563eb');
    document.getElementById('drawerTitle').textContent = data.name || '';
    document.getElementById('drawerAccessBadge').className = 'access-badge ' + badgeClass;
    document.getElementById('drawerAccessBadge').textContent = badgeLabel;
    document.getElementById('drawerMeta').textContent = typeLabel + ' • ID: #' + (data.roleCode || '');
    document.getElementById('drawerTabPermissions').textContent = 'Permissions (' + (data.totalPerms || 0) + ')';
    document.getElementById('drawerTabUsers').textContent = 'Users (' + (data.userCount || 0) + ')';

    var menuHtml = '';
    menuHtml += '<div class="rp-drawer-menu-item editRole" data-id="' + data.id + '" onclick="closeDrawerMenu()">' + svgIcon(ICON_EDIT) + ' Configure Role</div>';
    menuHtml += '<div class="rp-drawer-menu-item" onclick="duplicateRoleNotice(); closeDrawerMenu();">' + svgIcon(ICON_COPY) + ' Duplicate Role</div>';
    if (data.canDelete) {
        menuHtml += '<div class="rp-drawer-menu-item danger role_delete" data-id="' + data.id + '" onclick="closeDrawerMenu()">' + svgIcon(ICON_TRASH) + ' Delete Role</div>';
    }
    document.getElementById('drawerMenu').innerHTML = menuHtml;
    closeDrawerMenu();

    var coverageHtml = '';
    var covObj = data.coverage || {};
    Object.keys(covObj).forEach(function (bucketName) {
        var b = covObj[bucketName];
        coverageHtml +=
            '<div class="rp-dcov-row">' +
                '<div class="rp-dcov-top"><span class="rp-dcov-name">' + esc(bucketName) + '</span><span class="rp-dcov-frac">' + b.assigned + '/' + b.total + ' (' + b.pct + '%)</span></div>' +
                '<div class="rp-dcov-track"><div class="rp-dcov-fill" style="width:' + b.pct + '%;background:' + b.color + ';"></div></div>' +
            '</div>';
    });

    var warningHtml = '';
    if (badgeClass === 'badge-high') {
        warningHtml =
            '<div class="rp-dwarning">' +
                svgIcon(ICON_ALERT, '#d97706') +
                '<div>' +
                    '<p class="rp-dwarning-title">High Access Role</p>' +
                    '<p class="rp-dwarning-text">This role grants access to elevated operational modules. Policy updates impact ' + data.userCount + ' assigned user' + (data.userCount === 1 ? '' : 's') + '.</p>' +
                '</div>' +
            '</div>';
    }

    document.getElementById('drawerPaneOverview').innerHTML =
        '<div class="rp-dmetrics">' +
            '<div class="rp-dmetric"><div class="rp-dmetric-icon" style="background:#eff6ff;">' + svgIcon(ICON_SHIELD_CHECK, '#2563eb') + '</div><div class="rp-dmetric-val">' + (data.grpCount || 0) + '</div><div class="rp-dmetric-lbl">Permission Groups</div></div>' +
            '<div class="rp-dmetric"><div class="rp-dmetric-icon" style="background:#eef2ff;">' + svgIcon(ICON_USERS, '#4f46e5') + '</div><div class="rp-dmetric-val">' + (data.userCount || 0) + '</div><div class="rp-dmetric-lbl">Assigned Users</div></div>' +
            '<div class="rp-dmetric"><div class="rp-dmetric-icon" style="background:#fffbeb;">' + svgIcon(ICON_SHIELD, '#d97706') + '</div><div class="rp-dmetric-val">' + badgeLabel.replace(' Access', '') + '</div><div class="rp-dmetric-lbl">Access Tier</div></div>' +
        '</div>' +
        '<div class="rp-dcard">' +
            '<p class="rp-dcard-title">Role Information</p>' +
            '<div class="rp-dinfo-grid">' +
                '<div><div class="rp-dinfo-lbl">Role Type</div><div class="rp-dinfo-val">' + typeLabel + '</div></div>' +
                '<div><div class="rp-dinfo-lbl">Role Code</div><div class="rp-dinfo-val">#' + esc(data.roleCode || '—') + '</div></div>' +
                '<div><div class="rp-dinfo-lbl">Created On</div><div class="rp-dinfo-val">' + (data.createdOn || '—') + '</div></div>' +
                '<div><div class="rp-dinfo-lbl">Last Updated</div><div class="rp-dinfo-val">' + (data.updatedOn || '—') + '</div></div>' +
            '</div>' +
        '</div>' +
        '<div class="rp-dcard">' +
            '<p class="rp-dcard-title">Domain Coverage</p>' +
            coverageHtml +
        '</div>' +
        '<div class="rp-view-all-link" onclick="switchDrawerTab(\'permissions\')">View all ' + (data.totalPerms || 0) + ' permissions ' + svgIcon(ICON_ARROW_RIGHT) + '</div>' +
        warningHtml;

    var permsHtml = '';
    if (data.permissions && data.permissions.length > 0) {
        data.permissions.forEach(function (grp) {
            permsHtml += '<div class="rp-dperm-group"><p class="rp-dperm-group-title">' + esc(grp.group) + ' (' + grp.items.length + ')</p>';
            grp.items.forEach(function (item) {
                permsHtml += '<div class="rp-dperm-item">' + svgIcon(ICON_CHECK) + ' ' + esc(item) + '</div>';
            });
            permsHtml += '</div>';
        });
    } else {
        permsHtml = '<div class="rp-dempty">No permissions assigned to this role.</div>';
    }
    document.getElementById('drawerPanePermissions').innerHTML = permsHtml;

    var usersHtml = '';
    if (data.users && data.users.length > 0) {
        data.users.forEach(function (u, idx) {
            usersHtml +=
                '<div class="rp-duser-item">' +
                    '<div class="rp-duser-av" style="background:' + avColors[idx % avColors.length] + ';">' + esc(getInitials(u.name)) + '</div>' +
                    '<div><div class="rp-duser-name">' + esc(u.name) + '</div><div class="rp-duser-email">' + esc(u.email) + '</div></div>' +
                '</div>';
        });
    } else {
        usersHtml = '<div class="rp-dempty">No users assigned to this role.</div>';
    }
    document.getElementById('drawerPaneUsers').innerHTML = usersHtml;

    var footerHtml = '<button type="button" class="rp-dbtn primary editRole" data-id="' + data.id + '">' + svgIcon(ICON_EDIT) + ' Configure Permissions</button>';
    if (data.canDelete) {
        footerHtml += '<button type="button" class="rp-dbtn danger role_delete" data-id="' + data.id + '">' + svgIcon(ICON_TRASH) + ' Delete</button>';
    }
    document.getElementById('drawerFooter').innerHTML = footerHtml;

    switchDrawerTab(initialTab || 'overview');
    document.getElementById('roleDrawer').classList.add('open');
    document.getElementById('roleDrawerBackdrop').classList.add('open');
}

function closeRoleDrawer() {
    document.getElementById('roleDrawer').classList.remove('open');
    document.getElementById('roleDrawerBackdrop').classList.remove('open');
    document.querySelectorAll('.rp-primary-card').forEach(function (c) { c.classList.remove('selected'); });
    closeDrawerMenu();
}

function switchDrawerTab(pane) {
    document.querySelectorAll('.rp-drawer-tab').forEach(function (t) { t.classList.toggle('active', t.getAttribute('data-pane') === pane); });
    document.querySelectorAll('.rp-drawer-pane').forEach(function (p) { p.classList.remove('active'); });
    var map = { overview: 'drawerPaneOverview', permissions: 'drawerPanePermissions', users: 'drawerPaneUsers' };
    var target = document.getElementById(map[pane] || 'drawerPaneOverview');
    if (target) target.classList.add('active');
}

function toggleDrawerMenu(e) {
    e.stopPropagation();
    document.getElementById('drawerMenu').classList.toggle('open');
}

function closeDrawerMenu() {
    document.getElementById('drawerMenu').classList.remove('open');
}

function duplicateRoleNotice() {
    Swal.fire({ icon: 'info', title: 'Coming soon', text: 'Duplicating roles isn\'t available yet.', confirmButtonColor: '#009ef7' });
}

document.addEventListener('click', function (e) {
    var menu = document.getElementById('drawerMenu');
    var btn = document.getElementById('drawerMenuBtn');
    if (menu && menu.classList.contains('open') && !menu.contains(e.target) && e.target !== btn) {
        closeDrawerMenu();
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeRoleDrawer();
});

/* ── Add / Edit Role Form Drawer ── */
function openRoleFormFromCard(card) {
    if (!card) return;
    var id = card.getAttribute('data-role-id');
    if (!id) return;
    var url = "{{ route('service_company.settings.loadEditRole', ':id') }}".replace(':id', id);
    loadRoleFormDrawer(url);
}

function loadRoleFormDrawer(url) {
    var $slot = $('#roleFormBody');
    $slot.html('<div class="rpf-loading"><div class="rpf-spinner"></div><span>Loading role permissions matrix…</span></div>');
    openRoleForm();
    $.ajax({
        type: 'GET', url: url,
        success: function (response) {
            if (response && response.status === 'success') {
                $slot.html(response.view);
                initRoleFormDrawer();
            } else {
                $slot.html('<div class="rpf-loading" style="color:#dc2626;">Unable to load the form. Please try again.</div>');
            }
        },
        error: function () {
            $slot.html('<div class="rpf-loading" style="color:#dc2626;">Something went wrong while loading the form.</div>');
        }
    });
}

function syncRoleFormAsideOffset() {
    var aside = document.getElementById('kt_aside');
    var isStaticSidebar = window.innerWidth >= 992;
    var w = (aside && isStaticSidebar && aside.getBoundingClientRect().left <= 0)
        ? aside.getBoundingClientRect().width : 0;
    document.documentElement.style.setProperty('--rpf-aside-w', w + 'px');
}

function openRoleForm() {
    syncRoleFormAsideOffset();
    document.getElementById('roleFormDrawer').classList.add('open');
    document.getElementById('roleFormBackdrop').classList.add('open');
    document.body.style.overflow = 'hidden';
    window.addEventListener('resize', syncRoleFormAsideOffset);
}

function closeRoleForm() {
    document.getElementById('roleFormDrawer').classList.remove('open');
    document.getElementById('roleFormBackdrop').classList.remove('open');
    document.body.style.overflow = '';
    window.removeEventListener('resize', syncRoleFormAsideOffset);
}

function initRoleFormDrawer() {
    var root = document.getElementById('roleFormBody');
    if (!root) return;

    var collapseBtn = root.querySelector('[data-rpf="collapse"]');
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            var secs = root.querySelectorAll('.rpf-sec-group');
            var anyExpanded = root.querySelector('.rpf-sec-group:not(.collapsed)');
            secs.forEach(function (s) { s.classList.toggle('collapsed', !!anyExpanded); });
            collapseBtn.querySelector('.rpf-collapse-lbl').textContent = anyExpanded ? 'Expand All' : 'Collapse All';
        });
    }

    root.querySelectorAll('.rpf-tab').forEach(function (t) {
        t.addEventListener('click', function () {
            root.querySelectorAll('.rpf-tab').forEach(function (x) { x.classList.remove('active'); });
            t.classList.add('active');
            var view = t.getAttribute('data-tab');
            var main = root.querySelector('.rpf-main');
            if (view === 'module') {
                if (main) main.style.display = '';
                root.querySelectorAll('.rpf-aside .rpf-acard').forEach(function (c, i) { c.style.display = (i === 0) ? '' : 'none'; });
            } else if (view === 'advanced') {
                if (main) main.style.display = 'none';
                root.querySelectorAll('.rpf-aside .rpf-acard').forEach(function (c, i) { c.style.display = (i === 0) ? 'none' : ''; });
            } else {
                if (main) main.style.display = '';
                root.querySelectorAll('.rpf-aside .rpf-acard').forEach(function (c) { c.style.display = ''; });
            }
        });
    });

    var search = root.querySelector('[data-rpf="search"]');
    if (search) {
        search.addEventListener('input', function () {
            var q = search.value.toLowerCase().trim();
            root.querySelectorAll('.rpf-sec-group').forEach(function (sec) {
                var secVisible = false;
                sec.querySelectorAll('.rpf-row').forEach(function (row) {
                    var name = (row.getAttribute('data-mod-name') || '').toLowerCase();
                    var hit = !q || name.indexOf(q) !== -1;
                    row.style.display = hit ? '' : 'none';
                    if (hit) secVisible = true;
                });
                sec.style.display = secVisible ? '' : 'none';
                if (q) sec.classList.remove('collapsed');
            });
        });
    }

    function setAllPerms(state) {
        root.querySelectorAll('input.rpf-perm-check:not(:disabled)').forEach(function (c) { c.checked = state; });
        recalcRoleSummary();
    }
    var qSelectAll = root.querySelector('[data-rpf="select-all"]');
    var qClearAll  = root.querySelector('[data-rpf="clear-all"]');
    var qViewOnly  = root.querySelector('[data-rpf="view-only"]');
    var qFull      = root.querySelector('[data-rpf="full-access"]');
    function setRadio(name, value) {
        var r = root.querySelector('input[name="' + name + '"][value="' + value + '"]');
        if (r) r.checked = true;
    }
    if (qSelectAll) qSelectAll.addEventListener('click', function () { setAllPerms(true); });
    if (qClearAll)  qClearAll.addEventListener('click', function () { setAllPerms(false); });
    if (qFull) qFull.addEventListener('click', function () {
        setRadio('all_requisition', '1');
        setRadio('add_team_on_requisition', '1');
        setAllPerms(true);
    });
    if (qViewOnly) qViewOnly.addEventListener('click', function () {
        root.querySelectorAll('input.rpf-perm-check:not(:disabled)').forEach(function (c) {
            var lbl = (c.getAttribute('data-action') || '').toLowerCase();
            c.checked = (lbl.indexOf('view') !== -1);
        });
        setRadio('all_requisition', '0');
        recalcRoleSummary();
    });

    var master = root.querySelector('input.rpf-master');
    if (master) {
        master.addEventListener('change', function () { setAllPerms(master.checked); });
    }

    root.querySelectorAll('input.rpf-perm-check, input[name="all_requisition"], input[name="add_team_on_requisition"]').forEach(function (c) {
        c.addEventListener('change', recalcRoleSummary);
    });

    recalcRoleSummary();

    function recalcRoleSummary() {
        var checks = root.querySelectorAll('input.rpf-perm-check');
        var total = 0, enabled = 0;
        checks.forEach(function (c) { total++; if (c.checked) enabled++; });
        var allReq = root.querySelector('input[name="all_requisition"][value="1"]');
        var addTeam = root.querySelector('input[name="add_team_on_requisition"][value="1"]');
        total += 2;
        if (allReq && allReq.checked) enabled++;
        if (addTeam && addTeam.checked) enabled++;

        var disabled = total - enabled;
        var pct = total ? Math.round((enabled / total) * 100) : 0;

        var partial = 0;
        root.querySelectorAll('.rpf-row').forEach(function (row) {
            var boxes = row.querySelectorAll('input.rpf-perm-check');
            if (!boxes.length) return;
            var on = 0; boxes.forEach(function (b) { if (b.checked) on++; });
            if (on > 0 && on < boxes.length) partial++;
        });

        setText('.rpf-prog-enabled', enabled);
        setText('.rpf-prog-total', total);
        setText('.rpf-prog-pct', pct + '%');
        var fill = root.querySelector('.rpf-prog-fill'); if (fill) fill.style.width = pct + '%';

        var circ = 2 * Math.PI * 34;
        var ring = root.querySelector('.rpf-donut-ring');
        if (ring) { ring.style.strokeDasharray = circ; ring.style.strokeDashoffset = circ * (1 - enabled / (total || 1)); }
        setText('.rpf-donut-num', enabled);
        setText('.rpf-legend-enabled', enabled);
        setText('.rpf-legend-disabled', disabled);
        setText('.rpf-legend-partial', partial);

        var pill = root.querySelector('.rpf-access-pill');
        if (pill) {
            var lvl = pct >= 70 ? { t: 'High', c: '#b91c1c', b: '#fee2e2' } : (pct >= 35 ? { t: 'Medium', c: '#d97706', b: '#fffbeb' } : { t: 'Low', c: '#059669', b: '#ecfdf5' });
            pill.textContent = lvl.t; pill.style.color = lvl.c; pill.style.background = lvl.b;
        }

        if (master) master.checked = (enabled >= total && total > 0);
    }

    function setText(sel, val) { var el = root.querySelector(sel); if (el) el.textContent = val; }
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && document.getElementById('roleFormDrawer').classList.contains('open')) closeRoleForm();
});

/* Switchery init */
$('.js-switch').each(function () { new Switchery($(this)[0], $(this).data()); });
</script>
@endpush
