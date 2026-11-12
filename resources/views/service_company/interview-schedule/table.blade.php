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

.it-dashboard,
.it-dashboard * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
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
   TIER 1: Pure White Top Header Toolbar Card
   ========================================================================== */
.it-header-card {
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
    border-radius: 10px !important;
    padding: 12px 18px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
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
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    background: var(--it-primary-soft) !important;
    color: var(--it-primary-deep) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 16px !important;
    flex-shrink: 0 !important;
}

.it-header-title-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}

.it-header-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: var(--it-text-main) !important;
    line-height: 1.25 !important;
    letter-spacing: -0.2px !important;
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
    font-size: 11.5px !important;
    color: var(--it-text-muted) !important;
    margin-top: 2px !important;
    line-height: 1.25 !important;
}

.it-header-right {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

/* Search Sub-Card inside Tier 1 Right Corner */
.it-search-subcard {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    background: #f8fafc !important;
    border: 1px solid var(--it-border) !important;
    border-radius: 8px !important;
    padding: 0 12px !important;
    height: 36px !important;
    width: 300px !important;
    max-width: 100% !important;
    transition: all 0.15s ease-in-out !important;
}

.it-search-subcard:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}

.it-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: var(--it-primary) !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.it-search-subcard i.fa-search {
    color: var(--it-text-light) !important;
    font-size: 12.5px !important;
    flex-shrink: 0 !important;
}

.it-search-subcard input {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    color: var(--it-text-main) !important;
    width: 100% !important;
    padding: 0 !important;
    height: 34px !important;
    line-height: 34px !important;
}

.it-search-subcard input::placeholder {
    color: var(--it-text-light) !important;
    font-size: 11.5px !important;
    font-weight: 400 !important;
}

.it-search-clear {
    background: none !important;
    border: none !important;
    color: var(--it-text-light) !important;
    font-size: 11px !important;
    padding: 2px 4px !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}

.it-search-clear:hover {
    color: var(--it-danger) !important;
}

.it-refresh-btn {
    height: 36px !important;
    padding: 0 14px !important;
    border-radius: 8px !important;
    border: 1px solid var(--it-border) !important;
    background: #ffffff !important;
    color: var(--it-text-mid) !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.it-refresh-btn:hover {
    background: #f8fafc !important;
    border-color: var(--it-primary) !important;
    color: var(--it-primary) !important;
}

.it-refresh-btn.is-syncing i {
    animation: itSpin 0.8s linear infinite;
}

@keyframes itSpin {
    to { transform: rotate(360deg); }
}

/* ==========================================================================
   TIER 2: Executive KPI Metric Strip (4 Clickable Metric Cards)
   ========================================================================== */
.it-kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 10px !important;
    margin-bottom: 12px !important;
    width: 100% !important;
}

.it-kpi-card {
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
    border-radius: 10px !important;
    padding: 12px 14px !important;
    display: flex !important;
    align-items: center !important;
    gap: 11px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    transition: all 0.15s ease-in-out !important;
    cursor: pointer !important;
    min-width: 0 !important;
    text-align: left !important;
}

.it-kpi-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
    transform: translateY(-1px);
}

.it-kpi-card.it-kpi-active {
    border-color: var(--it-primary) !important;
    background: #f0f9ff !important;
    box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.18) !important;
}

.it-kpi-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 15px !important;
    flex-shrink: 0 !important;
}

.it-kpi-icon.icon-blue    { background: #eff6ff !important; color: #2563eb !important; }
.it-kpi-icon.icon-emerald { background: #ecfdf5 !important; color: #059669 !important; }
.it-kpi-icon.icon-purple  { background: #f5f3ff !important; color: #7c3aed !important; }
.it-kpi-icon.icon-red     { background: #fef2f2 !important; color: #dc2626 !important; }

.it-kpi-body {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    flex: 1 !important;
}

.it-kpi-label {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: var(--it-text-muted) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.it-kpi-val-row {
    display: flex !important;
    align-items: baseline !important;
    gap: 8px !important;
    margin-top: 2px !important;
}

.it-kpi-num {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: var(--it-text-main) !important;
    line-height: 1.15 !important;
    font-variant-numeric: tabular-nums !important;
}

.it-kpi-card.it-kpi-active .it-kpi-num {
    color: var(--it-primary) !important;
}

.it-kpi-hint {
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: var(--it-text-light) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* ==========================================================================
   TIER 3: Pure White Sub-Tabs & Quick Filters Card
   ========================================================================== */
.it-tabs-card {
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
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

.it-tabs-group {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
    min-width: 0 !important;
}

.it-tab-btn {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: var(--it-text-mid) !important;
    border: 1px solid var(--it-border) !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
}

.it-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: var(--it-text-main) !important;
}

/* Single Active Primary Blue Highlight Rule */
.it-tab-btn.active {
    background: var(--it-primary) !important;
    color: #ffffff !important;
    border-color: var(--it-primary) !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.22) !important;
}

.it-tab-badge {
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
    color: var(--it-text-mid) !important;
    font-variant-numeric: tabular-nums !important;
    line-height: 1 !important;
}

.it-tab-btn.active .it-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

.it-tab-btn:not(.active) .it-tab-badge.badge-today     { background: #dcfce7 !important; color: #15803d !important; }
.it-tab-btn:not(.active) .it-tab-badge.badge-upcoming  { background: #eff6ff !important; color: #1d4ed8 !important; }
.it-tab-btn:not(.active) .it-tab-badge.badge-pending   { background: #fef3c7 !important; color: #b45309 !important; }
.it-tab-btn:not(.active) .it-tab-badge.badge-selected  { background: #dcfce7 !important; color: #15803d !important; }
.it-tab-btn:not(.active) .it-tab-badge.badge-resched   { background: #e0f2fe !important; color: #0369a1 !important; }
.it-tab-btn:not(.active) .it-tab-badge.badge-rejected  { background: #fee2e2 !important; color: #b91c1c !important; }

.it-inline-filters {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    flex-shrink: 0 !important;
}

.it-filter-pill {
    display: flex !important;
    align-items: center !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 0 10px !important;
    height: 34px !important;
    gap: 6px !important;
    transition: all 0.15s ease !important;
}

.it-filter-pill:hover {
    background: #ffffff !important;
    border-color: #94a3b8 !important;
}

.it-filter-pill:focus-within {
    background: #ffffff !important;
    border-color: var(--it-primary) !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.it-filter-pill-lbl {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: var(--it-text-muted) !important;
    white-space: nowrap !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.it-filter-select {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: var(--it-text-main) !important;
    padding: 0 18px 0 0 !important;
    height: 32px !important;
    line-height: 32px !important;
    cursor: pointer !important;
}

.it-reset-btn {
    height: 34px !important;
    padding: 0 11px !important;
    border-radius: 8px !important;
    border: 1px solid var(--it-border) !important;
    background: #ffffff !important;
    color: var(--it-primary-deep) !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.it-reset-btn:hover {
    background: var(--it-primary-soft) !important;
    border-color: #bfdbfe !important;
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

/* Upper Discrete Sub-Cards Grid (5 Responsive Fractional Columns) */
.it-card-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2.05fr) minmax(0, 1.8fr) minmax(0, 1.55fr) minmax(0, 1.45fr) minmax(0, 1.65fr) !important;
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
    gap: 6px !important;
    min-height: 18px !important;
}

.it-subcard-label {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.35px !important;
    color: var(--it-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
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
    font-size: 10px !important;
    color: var(--it-text-light) !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
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
    right: 0 !important;
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15) !important;
    z-index: 80 !important;
    display: none !important;
    min-width: 145px !important;
    padding: 4px !important;
}

.it-followup-menu.open {
    display: block !important;
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
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
    line-height: 1.3 !important;
}

.it-scope-direct {
    background: #ecfdf5 !important;
    color: #059669 !important;
    border: 1px solid #a7f3d0 !important;
}

.it-scope-internal {
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
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

.it-round-pill {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    background: #ffffff !important;
    border: 1px solid var(--it-border) !important;
    color: var(--it-text-mid) !important;
    white-space: nowrap !important;
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

.it-time-tag {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 1px 5px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

.it-time-today    { background: #dcfce7 !important; color: #15803d !important; }
.it-time-upcoming { background: #e0f2fe !important; color: #0369a1 !important; }
.it-time-past     { background: #f1f5f9 !important; color: #64748b !important; }

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

/* Sub-Card 5: Evaluation & Feedback */
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

.it-fb-btn-add:hover {
    background: #1d4ed8 !important;
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
@media (max-width: 1440px) {
    .it-card-grid {
        grid-template-columns: minmax(0, 2fr) minmax(0, 1.75fr) minmax(0, 1.55fr) minmax(0, 1.4fr) minmax(0, 1.6fr) !important;
        gap: 7px !important;
        padding: 9px 11px !important;
    }
    .it-subcard { padding: 7px 9px !important; }
    .it-search-subcard { width: 250px !important; }
}

@media (max-width: 1200px) {
    .it-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    .it-card-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }
}

@media (max-width: 768px) {
    .it-header-card { padding: 12px 14px !important; }
    .it-header-right { width: 100% !important; }
    .it-search-subcard { flex: 1 1 auto !important; width: auto !important; }
    .it-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .it-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
}

@media (max-width: 520px) {
    .it-kpi-grid { grid-template-columns: minmax(0, 1fr) !important; }
    .it-card-grid { grid-template-columns: minmax(0, 1fr) !important; }
}
</style>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
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
             TIER 1: TOP HEADER TOOLBAR CARD (Title + Dynamic Search + Sync)
             ══════════════════════════════════════════════════════════════ -->
        <div class="it-header-card">
          <div class="it-header-left">
            <div class="it-header-icon">
              <i class="fas fa-calendar-check"></i>
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
            <div class="it-search-subcard">
              <i class="fas fa-search"></i>
              <input type="text" id="kt_tagify_2" placeholder="Search candidate, job, client, recruiter..." autocomplete="off">
              <span id="loader_search" class="loader_search hidden_search" style="display:none;flex-shrink:0;"></span>
              <button type="button" class="it-search-clear" id="itSearchClear" style="display:none;" title="Clear search">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <button type="button" class="it-refresh-btn" id="itRefreshBtn" onclick="forceSyncInterviewTracker()" title="Sync live interviews">
              <i class="fas fa-sync-alt"></i>
              <span>Sync</span>
            </button>
          </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
             TIER 2: EXECUTIVE KPI METRIC STRIP (4 Interactive Filter Cards)
             ══════════════════════════════════════════════════════════════ -->
        <div class="it-kpi-grid" id="statsGrid">
          <button type="button" class="it-kpi-card" data-kpi-tab="this_month" onclick="filterByKpi('this_month')">
            <div class="it-kpi-icon icon-blue">
              <i class="far fa-calendar-alt"></i>
            </div>
            <div class="it-kpi-body">
              <div class="it-kpi-label">Interviews This Month</div>
              <div class="it-kpi-val-row">
                <span class="it-kpi-num" id="stat-interview-month">0</span>
                <span class="it-kpi-hint">{{ now()->format('M Y') }}</span>
              </div>
            </div>
          </button>

          <button type="button" class="it-kpi-card" data-kpi-tab="today" onclick="filterByKpi('today')">
            <div class="it-kpi-icon icon-emerald">
              <i class="fas fa-calendar-day"></i>
            </div>
            <div class="it-kpi-body">
              <div class="it-kpi-label">Today's Interviews</div>
              <div class="it-kpi-val-row">
                <span class="it-kpi-num" id="stat-today">0</span>
                <span class="it-kpi-hint">{{ now()->format('d M') }}</span>
              </div>
            </div>
          </button>

          <button type="button" class="it-kpi-card" data-kpi-tab="upcoming" onclick="filterByKpi('upcoming')">
            <div class="it-kpi-icon icon-purple">
              <i class="far fa-clock"></i>
            </div>
            <div class="it-kpi-body">
              <div class="it-kpi-label">Upcoming Interviews</div>
              <div class="it-kpi-val-row">
                <span class="it-kpi-num" id="stat-upcoming">0</span>
                <span class="it-kpi-hint">Scheduled ahead</span>
              </div>
            </div>
          </button>

          <button type="button" class="it-kpi-card" data-kpi-tab="pending_feedback" onclick="filterByKpi('pending_feedback')">
            <div class="it-kpi-icon icon-red">
              <i class="far fa-comment-dots"></i>
            </div>
            <div class="it-kpi-body">
              <div class="it-kpi-label">Feedback Pending</div>
              <div class="it-kpi-val-row">
                <span class="it-kpi-num" id="stat-feedback-pending">0</span>
                <span class="it-kpi-hint">Action required</span>
              </div>
            </div>
          </button>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
             TIER 3: SUB-TABS & FILTERS CARD (Pure White #ffffff)
             ══════════════════════════════════════════════════════════════ -->
        <div class="it-tabs-card">
          <div class="it-tabs-group">
            <button type="button" class="it-tab-btn active" data-tab="all" onclick="filterByTab('all')">
              <span>All Interviews</span>
              <span class="it-tab-badge" id="tab-count-all">0</span>
            </button>
            <button type="button" class="it-tab-btn" data-tab="today" onclick="filterByTab('today')">
              <span>Today</span>
              <span class="it-tab-badge badge-today" id="tab-count-today">0</span>
            </button>
            <button type="button" class="it-tab-btn" data-tab="upcoming" onclick="filterByTab('upcoming')">
              <span>Upcoming</span>
              <span class="it-tab-badge badge-upcoming" id="tab-count-upcoming">0</span>
            </button>
            <button type="button" class="it-tab-btn" data-tab="pending_feedback" onclick="filterByTab('pending_feedback')">
              <span>Feedback Pending</span>
              <span class="it-tab-badge badge-pending" id="tab-count-pending">0</span>
            </button>
            <button type="button" class="it-tab-btn" data-tab="selected" onclick="filterByTab('selected')">
              <span>Selected</span>
              <span class="it-tab-badge badge-selected" id="tab-count-selected">0</span>
            </button>
            <button type="button" class="it-tab-btn" data-tab="rescheduled" onclick="filterByTab('rescheduled')">
              <span>Rescheduled</span>
              <span class="it-tab-badge badge-resched" id="tab-count-rescheduled">0</span>
            </button>
            <button type="button" class="it-tab-btn" data-tab="rejected" onclick="filterByTab('rejected')">
              <span>Rejected</span>
              <span class="it-tab-badge badge-rejected" id="tab-count-rejected">0</span>
            </button>
          </div>

          <div class="it-inline-filters">
            <div class="it-filter-pill">
              <span class="it-filter-pill-lbl"><i class="fas fa-video text-muted"></i> Mode:</span>
              <select id="itModeFilter" class="form-select form-select-sm it-filter-select">
                <option value="All" selected>All Modes</option>
                <option value="2">Video Call</option>
                <option value="1">Telecon</option>
                <option value="3">Face to Face</option>
              </select>
            </div>

            <div class="it-filter-pill">
              <span class="it-filter-pill-lbl"><i class="fas fa-sort-amount-down text-muted"></i> Sort:</span>
              <select id="sortingfilter" class="form-select form-select-sm it-filter-select">
                <option value="" selected>Newest Interviews</option>
                <option value="1">Today's Interviews ({{ now()->format('d M Y') }})</option>
                <option value="4">Soonest Scheduled</option>
                <option value="3">Latest Scheduled</option>
                <option value="Feedback Pending">Feedback Pending</option>
                <option value="Interview Pending">Upcoming Interviews</option>
              </select>
            </div>

            <button type="button" class="it-reset-btn" onclick="resetFilters()" title="Reset search, tabs, and filters">
              <i class="fas fa-undo-alt"></i>
              <span>Reset</span>
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
// ── Debounced Search, Clear & Filter Triggers ──────────────────────────────
var itSearchDebounceTimer = null;

$(document).ready(function() {
    $('#kt_tagify_2').on('input', function() {
        var val = $(this).val() || '';
        $('#itSearchClear').toggle(val.length > 0);
        clearTimeout(itSearchDebounceTimer);
        itSearchDebounceTimer = setTimeout(function() {
            itCurrentPage = 1;
            fetchCandidates(1);
        }, 300);
    });

    $('#itSearchClear').on('click', function() {
        $('#kt_tagify_2').val('');
        $(this).hide();
        itCurrentPage = 1;
        fetchCandidates(1);
    });

    $('#sortingfilter').on('change', function() {
        itCurrentPage = 1;
        fetchCandidates(1);
    });

    $('#itModeFilter').on('change', function() {
        filterValues.mode = $(this).val() || 'All';
        itCurrentPage = 1;
        fetchCandidates(1);
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

// ── Tab, KPI & Filter Controllers ──────────────────────────────────────────
let activeTab = 'all';
let filterValues = { stage: 'All', status: 'All', owner: 'All', vendor: 'All', mode: 'All' };

function syncActiveKpiAndTabs(tab) {
    document.querySelectorAll('.it-tab-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tab);
    });
    document.querySelectorAll('.it-kpi-card').forEach(function(card) {
        card.classList.toggle('it-kpi-active', card.getAttribute('data-kpi-tab') === tab);
    });
}

function filterByTab(tab) {
    activeTab = tab || 'all';
    syncActiveKpiAndTabs(activeTab);
    itCurrentPage = 1;
    fetchCandidates(1, $('#kt_tagify_2').val());
}

function filterByKpi(kpiTab) {
    var nextTab = (activeTab === kpiTab) ? 'all' : kpiTab;
    filterByTab(nextTab);
}

function resetFilters() {
    itRamCache.clear();
    if (window.JeroInterviewScheduleLocalStore) {
        window.JeroInterviewScheduleLocalStore.purge();
    }
    $('#sortingfilter').val('');
    $('#itModeFilter').val('All');
    filterValues.mode = 'All';
    activeTab = 'all';
    syncActiveKpiAndTabs('all');
    $('#kt_tagify_2').val('');
    $('#itSearchClear').hide();
    itCurrentPage = 1;
    fetchCandidates(1, '', true);
}

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
    if (!isOpen) menu.classList.add('open');
}

// ── Browser-RAM LRU Cache ──────────────────────────────────────────────────
var itRamCache = new Map();
var IT_MAX_RAM_CACHE = 25;
var itCurrentXhr = null;

// ── JeroInterviewScheduleLocalStore — Native Browser IndexedDB Engine ────────
const JeroInterviewScheduleLocalStore = (function() {
    const DB_NAME = 'jero_interview_schedule_db';
    const DB_VERSION = 2; // Bumped for Card + Sub-Card HTML schema
    const CURRENT_TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
    const CURRENT_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
    const CACHE_TTL_MS = 15 * 60 * 1000;

    let dbPromise = null;

    function getDB() {
        if (!window.indexedDB) return Promise.resolve(null);
        if (dbPromise) return dbPromise;

        dbPromise = new Promise((resolve) => {
            try {
                const req = window.indexedDB.open(DB_NAME, DB_VERSION);
                req.onupgradeneeded = function(e) {
                    const db = e.target.result;
                    if (!db.objectStoreNames.contains('schedule_cache')) {
                        db.createObjectStore('schedule_cache', { keyPath: 'cache_key' });
                    } else {
                        try { e.target.transaction.objectStore('schedule_cache').clear(); } catch (err) {}
                    }
                    if (!db.objectStoreNames.contains('meta')) {
                        db.createObjectStore('meta', { keyPath: 'id' });
                    }
                };
                req.onsuccess = function(e) {
                    const db = e.target.result;
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
                req.onerror = function() { resolve(null); };
            } catch (err) {
                resolve(null);
            }
        });

        return dbPromise;
    }

    return {
        get: async function(key) {
            const db = await getDB();
            if (!db || !key) return null;
            return new Promise((resolve) => {
                try {
                    const tx = db.transaction(['schedule_cache'], 'readonly');
                    const store = tx.objectStore('schedule_cache');
                    const req = store.get(key);
                    req.onsuccess = function() {
                        const res = req.result;
                        const lastMut = parseInt(localStorage.getItem('jero_it_last_mutation') || '0', 10);
                        if (res && (Date.now() - res.timestamp < CACHE_TTL_MS) && (!lastMut || res.timestamp > lastMut)) {
                            resolve(res.payload);
                        } else {
                            resolve(null);
                        }
                    };
                    req.onerror = function() { resolve(null); };
                } catch (e) {
                    resolve(null);
                }
            });
        },
        set: async function(key, payload) {
            const db = await getDB();
            if (!db || !key || !payload) return;
            try {
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
            const db = await getDB();
            if (!db) return;
            try {
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

    $('#Interview-trackers').html(response.html);
    $('#pagination').html(response.pagination);

    if (response.from !== undefined && response.to !== undefined && response.total !== undefined) {
        $('#showingText').text(
            'Showing ' + response.from + ' to ' + response.to + ' of ' + response.total + ' interviews'
        );
    } else if (response.total !== undefined) {
        $('#showingText').text('Showing ' + response.total + ' interviews');
    }

    if (response.stats) {
        $('#stat-interview-month').text(response.stats.interview_month ?? 0);
        $('#stat-today').text(response.stats.today ?? 0);
        $('#stat-upcoming').text(response.stats.upcoming ?? 0);
        $('#stat-feedback-pending').text(response.stats.feedback_pending ?? 0);
    }

    var tc = response.tab_counts || {};
    var totalAll = tc.all !== undefined ? tc.all : (response.total ?? 0);
    $('#itHeaderTotalBadge').text(totalAll);
    $('#tab-count-all').text(totalAll);
    $('#tab-count-today').text(tc.today ?? (response.stats ? (response.stats.today ?? 0) : 0));
    $('#tab-count-upcoming').text(tc.upcoming ?? (response.stats ? (response.stats.upcoming ?? 0) : 0));
    $('#tab-count-pending').text(tc.pending_feedback ?? (response.stats ? (response.stats.feedback_pending ?? 0) : 0));
    $('#tab-count-selected').text(tc.selected ?? 0);
    $('#tab-count-rejected').text(tc.rejected ?? 0);
    $('#tab-count-rescheduled').text(tc.rescheduled ?? 0);
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

    var payload = {
        page:               itCurrentPage,
        size:               itCurrentSize,
        custom_status:      $('#status').val() || '',
        search:             $('#inputSearch').val() || '',
        custom_hiringtype:  $('#sortingfilter').val() || '',
        custom_search:      $('#kt_tagify_2').val() || '',
        tab:                activeTab,
        stage_filter:       filterValues.stage,
        status_filter:      filterValues.status,
        owner_filter:       filterValues.owner,
        vendor_filter:      filterValues.vendor,
        mode_filter:        filterValues.mode
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
            renderInterviewData(itRamCache.get(cacheKey));
            return;
        }

        var idbCached = await JeroInterviewScheduleLocalStore.get(cacheKey);
        if (idbCached) {
            itRamCache.set(cacheKey, idbCached);
            renderInterviewData(idbCached);
            return;
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
            if (itRamCache.size >= IT_MAX_RAM_CACHE) {
                var oldestKey = itRamCache.keys().next().value;
                itRamCache.delete(oldestKey);
            }
            itRamCache.set(cacheKey, response);
            JeroInterviewScheduleLocalStore.set(cacheKey, response);
            renderInterviewData(response);
        },
        error: function(xhr, status) {
            if (status === 'abort') {
                return;
            }
            $('#tableLoading').removeClass('show');
            $('.loadingMessage').css({ display: 'none' });
            $('.loader_search').css({ display: 'none' });
            $('.page-loader').css('display', 'none');

            if (xhr.status === 419) {
                itHandleAjaxFailure(xhr);
            } else if (!navigator.onLine || xhr.status === 0) {
                $('#Interview-trackers').html(
                    '<div class="it-empty-card">' +
                    '  <div class="it-empty-icon" style="background:#fef2f2!important;color:#dc2626!important;"><i class="fas fa-wifi"></i></div>' +
                    '  <div class="it-empty-title">Network Connection Lost</div>' +
                    '  <div class="it-empty-sub">You are currently offline. Check your internet connection and click Sync.</div>' +
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
