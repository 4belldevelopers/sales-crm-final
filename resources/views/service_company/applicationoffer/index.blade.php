{{--
  JERO ATS — Service Company Offer Tracker (Rule 20 Modern Card + Sub-Card Design System)
  Intent: Enterprise-grade, page-isolated Offer Tracker featuring Client-Tracker-inspired
          interactive KPI stat cards, interlocking Offers Pipeline funnel, Rule 20 Search/Filter
          & Sub-Tabs cards, and Card + Sub-Card offer stream powered by Elasticsearch projections,
          3-tier Browser RAM/IndexedDB/Redis caching, and real-time Reverb synchronization.
  Tenancy & Security: Strictly scoped by auth()->user()->company_id across IndexedDB, BroadcastChannel,
                      and Reverb channels. Zero MySQL reads on listing path.
  Screen Fit: 100% fluid fit on 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow.
--}}
@extends('layouts.service_company_layout')

@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
/* ==========================================================================
   Offer Tracker — Page-Isolated Enterprise Card + Sub-Card Design System
   Font: Poppins strictly throughout; tabular-nums for all metrics/currency
   ========================================================================== */
:root {
    --ot-primary: #009ef7;
    --ot-primary-deep: #2563eb;
    --ot-primary-soft: #eff6ff;
    --ot-success: #16a34a;
    --ot-success-soft: #ecfdf5;
    --ot-warning: #d97706;
    --ot-warning-soft: #fffbeb;
    --ot-danger: #dc2626;
    --ot-danger-soft: #fef2f2;
    --ot-indigo: #4f46e5;
    --ot-indigo-soft: #eef2ff;
    --ot-cyan: #0891b2;
    --ot-cyan-soft: #ecfeff;
    --ot-rose: #db2777;
    --ot-rose-soft: #fdf2f8;
    --ot-text-main: #0f172a;
    --ot-text-mid: #475569;
    --ot-text-muted: #64748b;
    --ot-text-light: #94a3b8;
    --ot-border: #e2e8f0;
    --ot-border-soft: #f1f5f9;
    --ot-bg-subcard: #f8fafc;
}

/* Header Spacing: Snug fit removing empty gap with main header & 0% horizontal page scroll */
#kt_toolbar { display: none !important; }
.kt_content_offertracker,
#kt_content.kt_content_offertracker,
#kt_content {
    margin-top: -48px !important;
    padding-top: 0 !important;
}
@media (max-width: 991.98px) {
    .kt_content_offertracker,
    #kt_content.kt_content_offertracker,
    #kt_content {
        margin-top: -38px !important;
        padding-top: 0 !important;
    }
}
#kt_post { margin-top: 0 !important; }
#kt_content_container,
body { overflow-x: hidden !important; }

/* Font definitions: Protect FontAwesome icon fonts from Poppins universal override */
.ot-dashboard,
.ot-dashboard button,
.ot-dashboard input,
.ot-dashboard select,
.ot-dashboard textarea,
.ot-dashboard h1,
.ot-dashboard h2,
.ot-dashboard h3,
.ot-dashboard h4,
.ot-dashboard h5,
.ot-dashboard h6,
.ot-dashboard p,
.ot-dashboard a,
.ot-dashboard span:not([class*="fa-"]):not([class^="fa-"]),
.ot-dashboard div {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

.ot-dashboard * {
    box-sizing: border-box !important;
}

/* Explicit FontAwesome Icon font preservation */
.ot-dashboard i.fa,
.ot-dashboard i.fas,
.ot-dashboard i.far,
.ot-dashboard i.fal,
.ot-dashboard i.fab,
.ot-dashboard [class*="fa-"],
.ot-dashboard [class^="fa-"] {
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
    font-style: normal !important;
    font-variant: normal !important;
    text-rendering: auto !important;
    line-height: 1 !important;
}
.ot-dashboard i.fab {
    font-family: "Font Awesome 6 Brands", "Font Awesome 5 Brands" !important;
}
.ot-dashboard i.fa-solid,
.ot-dashboard i.fas {
    font-weight: 900 !important;
}
.ot-dashboard i.fa-regular,
.ot-dashboard i.far {
    font-weight: 400 !important;
}

.ot-dashboard {
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 14px 24px !important;
    color: var(--ot-text-main);
    -webkit-font-smoothing: antialiased;
}

.ot-num,
.ot-kpi-num,
.ot-tab-badge,
.ot-pipe-big,
.ot-pipe-pct-v2,
.ot-pipe-sub-v2,
.ot-comp-amount {
    font-variant-numeric: tabular-nums !important;
}

/* ==========================================================================
   HEADER CARD (Client Tracker Inspired Enterprise Header)
   ========================================================================== */
.ot-header-card {
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
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

.ot-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.ot-header-icon-box {
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

.ot-header-title-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}

.ot-header-title {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: var(--ot-text-main) !important;
    letter-spacing: -0.015em !important;
    margin: 0 !important;
    line-height: 1.25 !important;
}

.ot-verified-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #16a34a !important;
}

.ot-header-count-pill {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 2px 9px !important;
    border-radius: 999px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    background: var(--ot-primary-soft) !important;
    color: var(--ot-primary-deep) !important;
    border: 1px solid #bfdbfe !important;
    line-height: 1.3 !important;
}

.ot-header-subtitle {
    font-size: 12px !important;
    font-weight: 400 !important;
    color: var(--ot-text-muted) !important;
    margin-top: 2px !important;
    line-height: 1.35 !important;
}

.ot-header-actions {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
    flex-wrap: wrap !important;
}

.ot-hdr-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 36px !important;
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 8px !important;
    padding: 0 13px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: var(--ot-text-main) !important;
    cursor: pointer !important;
    text-decoration: none !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.ot-hdr-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: var(--ot-primary-deep) !important;
}

.ot-hdr-btn.is-syncing i.fa-sync-alt {
    animation: otSpin 0.8s linear infinite;
}

@keyframes otSpin {
    to { transform: rotate(360deg); }
}

.ot-dropdown-menu {
    min-width: 245px !important;
    padding: 6px !important;
    border-radius: 10px !important;
    border: 1px solid var(--ot-border) !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06) !important;
    background: #ffffff !important;
    margin-top: 6px !important;
    z-index: 1050 !important;
}

.ot-dropdown-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 8px 10px !important;
    border-radius: 7px !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: background 0.12s ease !important;
}

.ot-dropdown-item:hover {
    background: #f1f5f9 !important;
}

.ot-drop-ico {
    width: 30px !important;
    height: 30px !important;
    border-radius: 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    font-size: 13px !important;
}
.ot-drop-ico.csv { background: #eff6ff; color: #2563eb; }
.ot-drop-ico.pdf { background: #fef2f2; color: #dc2626; }
.ot-drop-ico.act { background: #ecfdf5; color: #059669; }
.ot-drop-ico.rst { background: #fffbeb; color: #d97706; }

.ot-drop-title {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    display: block !important;
    line-height: 1.25 !important;
}

.ot-drop-sub {
    font-size: 10.5px !important;
    color: #64748b !important;
    display: block !important;
    margin-top: 1px !important;
}

/* ==========================================================================
   KPI STAT CARDS — Client Tracker Inspired (6 Cards in 1 Horizontal Row)
   ========================================================================== */
.ot-kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    gap: 9px !important;
    margin-bottom: 12px !important;
    width: 100% !important;
}

.ot-kpi-card {
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
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

.ot-kpi-card:hover {
    transform: translateY(-1px) !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
    text-decoration: none !important;
}

.ot-kpi-top {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 6px !important;
    min-width: 0 !important;
    width: 100% !important;
}

.ot-kpi-icon {
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

.ot-kpi-icon svg {
    width: 17px !important;
    height: 17px !important;
}

.ot-kpi-icon.blue   { background: #eff6ff !important; color: #2563eb !important; }
.ot-kpi-icon.amber  { background: #fffbeb !important; color: #d97706 !important; }
.ot-kpi-icon.indigo { background: #eef2ff !important; color: #4f46e5 !important; }
.ot-kpi-icon.green  { background: #ecfdf5 !important; color: #16a34a !important; }
.ot-kpi-icon.cyan   { background: #ecfeff !important; color: #0891b2 !important; }
.ot-kpi-icon.rose   { background: #fdf2f8 !important; color: #db2777 !important; }
.ot-kpi-icon.red    { background: #fef2f2 !important; color: #dc2626 !important; }

.ot-kpi-meta {
    min-width: 0 !important;
    flex: 1 !important;
    overflow: hidden !important;
}

.ot-kpi-num-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 4px !important;
}

.ot-kpi-num {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: var(--ot-text-main) !important;
    line-height: 1.1 !important;
    white-space: nowrap !important;
}

.ot-kpi-lbl {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: var(--ot-text-muted) !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

.ot-kpi-pending-badge {
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

.ot-kpi-dot-pulse {
    width: 5px !important;
    height: 5px !important;
    border-radius: 50% !important;
    background: #d97706 !important;
    display: inline-block !important;
    animation: otPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes otPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.45; transform: scale(1.2); }
}

.ot-kpi-sub {
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

.ot-kpi-sub svg { width: 11px !important; height: 11px !important; flex-shrink: 0 !important; }
.ot-kpi-sub.slate  { color: #64748b !important; }
.ot-kpi-sub.amber  { color: #d97706 !important; }
.ot-kpi-sub.indigo { color: #4f46e5 !important; }
.ot-kpi-sub.green  { color: #16a34a !important; }
.ot-kpi-sub.cyan   { color: #0891b2 !important; }
.ot-kpi-sub.rose   { color: #db2777 !important; }
.ot-kpi-sub.red    { color: #dc2626 !important; }

/* ── Client Tracker Style Active KPI Highlight States ── */
.ot-kpi-card.active[data-tab="all"] {
    background: #eff6ff !important;
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 1.5px #2563eb, 0 4px 14px rgba(37, 99, 235, 0.12) !important;
}
.ot-kpi-card.active[data-tab="all"] .ot-kpi-icon { background: #ffffff !important; color: #2563eb !important; box-shadow: 0 1px 3px rgba(37, 99, 235, 0.16) !important; }
.ot-kpi-card.active[data-tab="all"] .ot-kpi-num  { color: #1d4ed8 !important; }
.ot-kpi-card.active[data-tab="all"] .ot-kpi-lbl  { color: #1e40af !important; font-weight: 700 !important; }

.ot-kpi-card.active[data-tab="pending"] {
    background: #fffbeb !important;
    border-color: #fbbf24 !important;
    box-shadow: 0 0 0 1.5px #d97706, 0 4px 14px rgba(217, 119, 6, 0.13) !important;
}
.ot-kpi-card.active[data-tab="pending"] .ot-kpi-icon { background: #ffffff !important; color: #d97706 !important; box-shadow: 0 1px 3px rgba(217, 119, 6, 0.18) !important; }
.ot-kpi-card.active[data-tab="pending"] .ot-kpi-num  { color: #b45309 !important; }
.ot-kpi-card.active[data-tab="pending"] .ot-kpi-lbl  { color: #92400e !important; font-weight: 700 !important; }

.ot-kpi-card.active[data-tab="sent"] {
    background: #eef2ff !important;
    border-color: #818cf8 !important;
    box-shadow: 0 0 0 1.5px #4f46e5, 0 4px 14px rgba(79, 70, 229, 0.12) !important;
}
.ot-kpi-card.active[data-tab="sent"] .ot-kpi-icon { background: #ffffff !important; color: #4f46e5 !important; box-shadow: 0 1px 3px rgba(79, 70, 229, 0.16) !important; }
.ot-kpi-card.active[data-tab="sent"] .ot-kpi-num  { color: #3730a3 !important; }
.ot-kpi-card.active[data-tab="sent"] .ot-kpi-lbl  { color: #4338ca !important; font-weight: 700 !important; }

.ot-kpi-card.active[data-tab="accepted"] {
    background: #ecfdf5 !important;
    border-color: #34d399 !important;
    box-shadow: 0 0 0 1.5px #16a34a, 0 4px 14px rgba(22, 163, 74, 0.13) !important;
}
.ot-kpi-card.active[data-tab="accepted"] .ot-kpi-icon { background: #ffffff !important; color: #16a34a !important; box-shadow: 0 1px 3px rgba(22, 163, 74, 0.16) !important; }
.ot-kpi-card.active[data-tab="accepted"] .ot-kpi-num  { color: #15803d !important; }
.ot-kpi-card.active[data-tab="accepted"] .ot-kpi-lbl  { color: #166534 !important; font-weight: 700 !important; }

.ot-kpi-card.active[data-tab="joining_soon"] {
    background: #ecfeff !important;
    border-color: #22d3ee !important;
    box-shadow: 0 0 0 1.5px #0891b2, 0 4px 14px rgba(8, 145, 178, 0.13) !important;
}
.ot-kpi-card.active[data-tab="joining_soon"] .ot-kpi-icon { background: #ffffff !important; color: #0891b2 !important; box-shadow: 0 1px 3px rgba(8, 145, 178, 0.16) !important; }
.ot-kpi-card.active[data-tab="joining_soon"] .ot-kpi-num  { color: #0e7490 !important; }
.ot-kpi-card.active[data-tab="joining_soon"] .ot-kpi-lbl  { color: #155e75 !important; font-weight: 700 !important; }

.ot-kpi-card.active[data-tab="rejected"] {
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
    box-shadow: 0 0 0 1.5px #dc2626, 0 4px 14px rgba(220, 38, 38, 0.14) !important;
}
.ot-kpi-card.active[data-tab="rejected"] .ot-kpi-icon { background: #ffffff !important; color: #dc2626 !important; box-shadow: 0 1px 3px rgba(220, 38, 38, 0.16) !important; }
.ot-kpi-card.active[data-tab="rejected"] .ot-kpi-num  { color: #b91c1c !important; }
.ot-kpi-card.active[data-tab="rejected"] .ot-kpi-lbl  { color: #991b1b !important; font-weight: 700 !important; }

.ot-kpi-card.active[data-tab="expired"] {
    background: #fdf2f8 !important;
    border-color: #f472b6 !important;
    box-shadow: 0 0 0 1.5px #db2777, 0 4px 14px rgba(219, 39, 119, 0.14) !important;
}
.ot-kpi-card.active[data-tab="expired"] .ot-kpi-icon { background: #ffffff !important; color: #db2777 !important; box-shadow: 0 1px 3px rgba(219, 39, 119, 0.16) !important; }
.ot-kpi-card.active[data-tab="expired"] .ot-kpi-num  { color: #9d174d !important; }
.ot-kpi-card.active[data-tab="expired"] .ot-kpi-lbl  { color: #831843 !important; font-weight: 700 !important; }

/* ==========================================================================
   OFFERS PIPELINE FUNNEL CARD
   ========================================================================== */
.ot-pipeline-wrap {
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 10px !important;
    margin-bottom: 12px !important;
    padding: 12px 16px 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
}

.ot-pipeline-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 10px !important;
    gap: 10px !important;
}

.ot-pipeline-title-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.ot-pipeline-head h5 {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: var(--ot-text-main) !important;
    margin: 0 !important;
}

.ot-pipeline-sub-tag {
    font-size: 10px !important;
    font-weight: 600 !important;
    color: var(--ot-text-muted) !important;
    background: #f1f5f9 !important;
    padding: 2px 8px !important;
    border-radius: 999px !important;
}

.ot-pipeline-range {
    border: 1px solid var(--ot-border) !important;
    border-radius: 8px !important;
    padding: 5px 10px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: var(--ot-text-main) !important;
    background: #f8fafc !important;
    cursor: pointer !important;
    outline: none !important;
}

.ot-pipeline-range:focus {
    border-color: var(--ot-primary) !important;
    background: #ffffff !important;
}

.ot-pipeline-rail {
    overflow-x: auto;
    overflow-y: hidden;
    padding-bottom: 2px;
}
.ot-pipeline-rail::-webkit-scrollbar { height: 5px; }
.ot-pipeline-rail::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

.ot-pipeline-flow {
    display: flex !important;
    align-items: stretch !important;
    gap: 0 !important;
    width: 100% !important;
}

.ot-pipe-card-v2 {
    flex: 1 1 0 !important;
    min-width: 138px !important;
    position: relative !important;
    padding: 8px 14px 9px 22px !important;
    margin-left: -10px !important;
    clip-path: polygon(0 0, calc(100% - 14px) 0, 100% 50%, calc(100% - 14px) 100%, 0 100%, 14px 50%);
    transition: filter 0.15s ease !important;
}

.ot-pipe-card-v2:hover {
    filter: brightness(0.97);
}

.ot-pipe-card-v2:first-child {
    margin-left: 0 !important;
    padding-left: 14px !important;
    clip-path: polygon(0 6px, 6px 0, calc(100% - 14px) 0, 100% 50%, calc(100% - 14px) 100%, 6px 100%, 0 calc(100% - 6px));
}

.ot-pipe-card-v2:last-child {
    padding-right: 14px !important;
    clip-path: polygon(0 0, calc(100% - 6px) 0, 100% 6px, 100% calc(100% - 6px), calc(100% - 6px) 100%, 0 100%, 14px 50%);
}

.ot-pipe-head-row {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    margin-bottom: 4px !important;
}

.ot-pipe-icon-sm { width: 16px !important; height: 16px !important; flex-shrink: 0 !important; display: inline-flex; align-items: center; }
.ot-pipe-icon-sm svg { width: 15px !important; height: 15px !important; }
.ot-pipe-lbl { font-size: 11px !important; font-weight: 700 !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ot-pipe-data-row { display: flex !important; align-items: baseline !important; justify-content: space-between !important; gap: 6px !important; }
.ot-pipe-big { font-size: 16px !important; font-weight: 700 !important; color: #0f172a !important; line-height: 1.15 !important; }
.ot-pipe-pct-v2 { font-size: 11px !important; color: #64748b !important; font-weight: 600 !important; }
.ot-pipe-sub-v2 { font-size: 10px !important; color: #64748b !important; font-weight: 600 !important; }

/* ==========================================================================
   RULE 20 TIER 1 & TIER 2: TOOLBAR & DEDICATED SUB-TABS CARDS
   ========================================================================== */
.ot-toolbar-card {
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.ot-search-subcard {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex: 1 1 280px !important;
    min-width: 220px !important;
    height: 38px !important;
    background: #f8fafc !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 8px !important;
    padding: 0 10px 0 12px !important;
    transition: all 0.15s ease !important;
}

.ot-search-subcard:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}

.ot-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: var(--ot-primary) !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.ot-search-subcard i.fa-search {
    color: var(--ot-text-light) !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}

.ot-search-subcard input {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12.5px !important;
    font-weight: 500 !important;
    color: var(--ot-text-main) !important;
    width: 100% !important;
    height: 34px !important;
    padding: 0 !important;
}

.ot-search-subcard input::placeholder {
    color: var(--ot-text-light) !important;
    font-size: 12px !important;
    font-weight: 400 !important;
}

.ot-search-clear-btn {
    display: none !important;
}

.ot-search-kbd {
    font-size: 9.5px !important;
    font-weight: 600 !important;
    color: #94a3b8 !important;
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 4px !important;
    padding: 2px 5px !important;
    line-height: 1 !important;
    flex-shrink: 0 !important;
    user-select: none !important;
}

.ot-inline-filters {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    flex-shrink: 0 !important;
}

.ot-filter-pill {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 38px !important;
    background: #f8fafc !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 8px !important;
    padding: 0 10px !important;
    transition: all 0.15s ease !important;
}

.ot-filter-pill:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}

.ot-filter-pill:focus-within {
    background: #ffffff !important;
    border-color: var(--ot-primary) !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.ot-filter-pill-lbl {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: var(--ot-text-muted) !important;
    white-space: nowrap !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.ot-filter-select {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: var(--ot-text-main) !important;
    height: 34px !important;
    padding-right: 6px !important;
    cursor: pointer !important;
}

.ot-reset-btn {
    height: 38px !important;
    padding: 0 12px !important;
    border-radius: 8px !important;
    border: 1px solid var(--ot-border) !important;
    background: #ffffff !important;
    color: var(--ot-primary-deep) !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.ot-reset-btn:hover {
    background: var(--ot-primary-soft) !important;
    border-color: #bfdbfe !important;
}

/* Tier 2: Dedicated White Sub-Tabs Card */
.ot-tabs-card {
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
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

.ot-tabs-group {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
    min-width: 0 !important;
}

.ot-tab-btn {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 6px 13px !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: var(--ot-text-mid) !important;
    border: 1px solid var(--ot-border) !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
}

.ot-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: var(--ot-text-main) !important;
}

/* Strict Rule 20 Single Active Primary Blue Highlight */
.ot-tab-btn.active {
    background: var(--ot-primary) !important;
    color: #ffffff !important;
    border-color: var(--ot-primary) !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.22) !important;
}

.ot-tab-badge {
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
    color: var(--ot-text-mid) !important;
    line-height: 1 !important;
}

.ot-tab-btn.active .ot-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

.ot-tab-btn:not(.active) .ot-tab-badge.badge-pending  { background: #fef3c7 !important; color: #b45309 !important; }
.ot-tab-btn:not(.active) .ot-tab-badge.badge-sent     { background: #eff6ff !important; color: #1d4ed8 !important; }
.ot-tab-btn:not(.active) .ot-tab-badge.badge-accepted { background: #dcfce7 !important; color: #15803d !important; }
.ot-tab-btn:not(.active) .ot-tab-badge.badge-joining  { background: #ecfeff !important; color: #0e7490 !important; }
.ot-tab-btn:not(.active) .ot-tab-badge.badge-expired  { background: #fee2e2 !important; color: #b91c1c !important; }

.ot-tabs-right-meta {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 11px !important;
    color: var(--ot-text-muted) !important;
    font-weight: 500 !important;
    flex-shrink: 0 !important;
}

.ot-live-chip {
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

.ot-live-dot {
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    background: #16a34a !important;
    animation: otPulse 2s infinite;
}

/* ==========================================================================
   RULE 20 TIER 3: PRIMARY OFFER CARD & 5 DISCRETE SUB-CARDS STREAM
   ========================================================================== */
.ot-stream-wrapper {
    position: relative !important;
    width: 100% !important;
    min-height: auto !important;
}

.ot-cards-stream {
    width: 100% !important;
    max-width: 100% !important;
}

.ot-entity-card {
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 10px !important;
    margin-bottom: 8px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
}

.ot-entity-card:last-child {
    margin-bottom: 0 !important;
}

.ot-entity-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
    transform: translateY(-1px);
}

.ot-entity-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2.05fr) minmax(0, 1.65fr) minmax(0, 1.65fr) minmax(0, 1.05fr) minmax(0, 1.35fr) minmax(0, 1.25fr) !important;
    gap: 8px !important;
    padding: 10px 12px !important;
    width: 100% !important;
    align-items: stretch !important;
}

.ot-subcard {
    background: var(--ot-bg-subcard) !important;
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

.ot-subcard-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 5px !important;
    min-height: 18px !important;
}

.ot-subcard-label {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.35px !important;
    color: var(--ot-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.ot-subcard-badges {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-shrink: 0 !important;
}

.ot-micro-pill {
    font-size: 9px !important;
    font-weight: 700 !important;
    padding: 1.5px 6px !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}

.ot-micro-date {
    font-size: 9.5px !important;
    font-weight: 500 !important;
    color: var(--ot-text-light) !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}

.ot-pill-neutral        { background: #ffffff !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; }
.ot-pill-bench          { background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fde68a !important; }
.ot-pill-internal-bench { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.ot-pill-agency-bench   { background: #fef3c7 !important; color: #92400e !important; border: 1px solid #fcd34d !important; }
.ot-pill-campus         { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }

/* Sub-Card 1: Candidate */
.ot-subcard-cand.ot-clickable-subcard {
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.ot-subcard-cand.ot-clickable-subcard:hover,
.ot-subcard-cand.ot-clickable-subcard:focus-visible {
    border-color: #93c5fd !important;
    background: #f0f9ff !important;
    outline: none !important;
}

.ot-subcard-cand.ot-clickable-subcard:hover .ot-cand-name {
    color: var(--ot-primary-deep) !important;
    text-decoration: underline !important;
}

.ot-cand-body {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    min-width: 0 !important;
}

.ot-avatar {
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

.ot-avatar-img {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    border-radius: 8px !important;
    object-fit: cover !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    border: 1px solid var(--ot-border) !important;
}

.ot-cand-text {
    flex: 1 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.ot-cand-name {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: var(--ot-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
    line-height: 1.3 !important;
    text-decoration: none !important;
    cursor: pointer !important;
}

.ot-cand-name:hover {
    color: var(--ot-primary) !important;
}

.ot-cand-role {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: var(--ot-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
    margin-top: 1px !important;
}

.ot-cand-loc {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 10px !important;
    color: var(--ot-text-light) !important;
    margin-top: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.ot-cand-company {
    display: flex !important;
    align-items: center !important;
    gap: 4.5px !important;
    font-size: 10.5px !important;
    color: var(--ot-text-muted) !important;
    font-weight: 500 !important;
    margin-top: 1.5px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.ot-cand-company i {
    font-size: 9.5px !important;
    color: var(--ot-text-light) !important;
    flex-shrink: 0 !important;
}

.ot-cand-company span {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Sub-Card 2: Job Details */
.ot-scope-pill {
    font-size: 8px !important;
    font-weight: 700 !important;
    padding: 1px 5px !important;
    border-radius: 3px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
    display: inline-flex !important;
    align-items: center !important;
}
.ot-scope-pill.type-direct, .ot-scope-direct   { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
.ot-scope-pill.type-internal, .ot-scope-internal { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.ot-scope-pill.type-my-org { background: #fef3c7 !important; color: #92400e !important; border: 1px solid #fcd34d !important; }

.ot-job-status-pill {
    font-size: 8px !important;
    font-weight: 700 !important;
    padding: 1px 5px !important;
    border-radius: 3px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    white-space: nowrap !important;
    line-height: 1.3 !important;
}
.ot-pill-hiring  { background: #dcfce7 !important; color: #15803d !important; border: 1px solid #bbf7d0 !important; }
.ot-pill-hold    { background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fde68a !important; }
.ot-pill-closed  { background: #fee2e2 !important; color: #b91c1c !important; border: 1px solid #fecaca !important; }
.ot-pill-neutral { background: #f1f5f9 !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; }

.ot-job-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
    min-width: 0 !important;
}

.ot-job-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: var(--ot-text-main) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.ot-job-meta {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 10.5px !important;
    color: var(--ot-text-muted) !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    min-width: 0 !important;
}

.ot-job-id {
    font-weight: 700 !important;
    color: #0284c7 !important;
    flex-shrink: 0 !important;
}

.ot-job-client {
    font-weight: 600 !important;
    color: var(--ot-text-mid) !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    text-transform: uppercase !important;
    font-size: 10px !important;
}

.ot-job-tags {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    margin-top: 2px !important;
}

.ot-tag-chip {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    color: var(--ot-text-muted) !important;
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 4px !important;
    padding: 1px 6px !important;
}

/* Sub-Card 3: Compensation & Status (Mirrors Sub-Card 6 Pipeline & Stage Layout) */
.ot-subcard-comp-status.ot-clickable-subcard {
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.ot-subcard-comp-status.ot-clickable-subcard:hover,
.ot-subcard-comp-status.ot-clickable-subcard:focus-visible {
    border-color: #93c5fd !important;
    background: #f0f9ff !important;
    outline: none !important;
}

.ot-subcard-comp-status.ot-clickable-subcard:hover .ot-cs-cue {
    text-decoration: underline !important;
}

.ot-subcard-comp-status .ot-subcard-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 4px !important;
    min-width: 0 !important;
}

.ot-subcard-comp-status .ot-subcard-label {
    flex-shrink: 0 !important;
    overflow: visible !important;
    text-overflow: clip !important;
    white-space: nowrap !important;
}

.ot-subcard-comp-status .ot-micro-pill {
    flex-shrink: 1 !important;
    min-width: 0 !important;
    max-width: 85px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    font-size: 8px !important;
    padding: 1px 5px !important;
}

.ot-comp-status-body {
    display: flex !important;
    flex-direction: column !important;
    justify-content: flex-start !important;
    gap: 3px !important;
    min-width: 0 !important;
    width: 100% !important;
}

.ot-cs-amount-row {
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
    min-width: 0 !important;
    line-height: 1.25 !important;
}

.ot-cs-amount {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: var(--ot-text-main) !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    font-variant-numeric: tabular-nums !important;
}

.ot-cs-amount.is-pending {
    color: #d97706 !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
}

.ot-cs-status-row {
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
    min-width: 0 !important;
}

.ot-cs-emp-group {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 3px !important;
    width: 100% !important;
    min-width: 0 !important;
    margin: 2px 0 !important;
}

.ot-emp-btn {
    border: 1px solid #e2e8f0 !important;
    background: #f8fafc !important;
    color: #475569 !important;
    font-size: 8.5px !important;
    font-weight: 600 !important;
    padding: 2px 4px !important;
    border-radius: 4px !important;
    line-height: 1.2 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
    text-align: center !important;
    width: 100% !important;
    box-sizing: border-box !important;
    font-family: inherit !important;
}

.ot-emp-btn:hover {
    background: #eff6ff !important;
    border-color: #93c5fd !important;
    color: #1d4ed8 !important;
}

.ot-emp-btn.is-active {
    background: #eff6ff !important;
    border-color: #3b82f6 !important;
    color: #1d4ed8 !important;
    font-weight: 700 !important;
}

.ot-offer-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    width: fit-content !important;
    max-width: 100% !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.ot-offer-badge svg {
    width: 11px !important;
    height: 11px !important;
    flex-shrink: 0 !important;
}

.ot-cs-cue-row {
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
    min-width: 0 !important;
    margin-top: 1px !important;
}

.ot-cs-cue {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #009ef7 !important;
    line-height: 1.2 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
}

.ot-cs-cue.locked {
    color: var(--ot-text-muted) !important;
    font-weight: 500 !important;
    font-size: 10px !important;
}

/* Sub-Card 4: Joining */
.ot-joining-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
    min-width: 0 !important;
}

.ot-join-date {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    color: var(--ot-text-main) !important;
}

.ot-join-date i {
    color: var(--ot-primary-deep) !important;
    font-size: 11px !important;
}

.ot-join-time {
    font-size: 10px !important;
    color: var(--ot-text-muted) !important;
    font-weight: 500 !important;
}

.ot-join-empty {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #cbd5e1 !important;
    line-height: 1.2 !important;
}

/* Sub-Card 5: Ownership & Recruiter (Reference Image 2) */
.ot-owner-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
    min-width: 0 !important;
}

.ot-owner-name {
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
}

.ot-owner-name i {
    color: #2563eb !important;
    font-size: 11px !important;
    flex-shrink: 0 !important;
}

.ot-owner-company {
    font-size: 10.5px !important;
    color: #475569 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
}

.ot-owner-company i {
    color: #94a3b8 !important;
    font-size: 10px !important;
    flex-shrink: 0 !important;
}

.ot-owner-submitted {
    font-size: 10px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
}

.ot-owner-submitted i {
    color: #94a3b8 !important;
    font-size: 10px !important;
    flex-shrink: 0 !important;
}

/* Sub-Card 6: Pipeline & Stage (Reference Image 3, Entire Card Clickable) */
.ot-subcard-stage {
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.ot-subcard-stage:hover,
.ot-subcard-stage:focus-visible {
    border-color: #93c5fd !important;
    background: #f0f9ff !important;
    outline: none !important;
}

.ot-subcard-stage:hover .ot-stage-cue {
    text-decoration: underline !important;
}

.ot-stage-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2.5px !important;
    min-width: 0 !important;
}

.ot-stage-trigger {
    display: flex !important;
    flex-direction: column !important;
    gap: 2.5px !important;
    text-decoration: none !important;
    min-width: 0 !important;
}

.ot-stage-pill {
    display: inline-flex !important;
    align-items: center !important;
    width: fit-content !important;
    max-width: 100% !important;
    padding: 2px 8px !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.ot-substage-txt {
    font-size: 10.5px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.25 !important;
}

.ot-substage-txt strong {
    color: #0f172a !important;
    font-weight: 600 !important;
}

.ot-stage-cue {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #009ef7 !important;
    line-height: 1.2 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
}

/* Bottom Strip */
.ot-entity-bottom {
    background: #ffffff !important;
    border-top: 1px solid var(--ot-border-soft) !important;
    padding: 7px 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}

.ot-bottom-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    min-width: 0 !important;
    font-size: 10.5px !important;
    color: var(--ot-text-muted) !important;
}

.ot-bottom-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    min-width: 0 !important;
}

.ot-bottom-lbl {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.25px !important;
    color: var(--ot-text-light) !important;
}

.ot-bottom-strong {
    color: var(--ot-text-main) !important;
    font-weight: 600 !important;
}

.ot-bottom-muted {
    color: var(--ot-text-muted) !important;
    font-weight: 500 !important;
}

.ot-dot-sep {
    color: #cbd5e1 !important;
    font-size: 9px !important;
}

.ot-stage-btn {
    display: inline-flex !important;
    align-items: center !important;
    padding: 2px 9px !important;
    border-radius: 5px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    background: #f5f3ff !important;
    color: #6d28d9 !important;
    border: 1px solid #ddd6fe !important;
    cursor: pointer !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
}

.ot-stage-btn:hover {
    background: #ede9fe !important;
    border-color: #8b5cf6 !important;
}

.ot-stage-btn.ot-stage-offered {
    background: #fff7ed !important;
    color: #c2410c !important;
    border-color: #fdba74 !important;
}

.ot-stage-btn.ot-stage-offered:hover {
    background: #ffedd5 !important;
    border-color: #ea580c !important;
}

.ot-stage-disabled {
    background: #f8fafc !important;
    color: #94a3b8 !important;
    border-color: #e2e8f0 !important;
    cursor: default !important;
}

.ot-substage-pill {
    display: inline-flex !important;
    align-items: center !important;
    padding: 1.5px 7px !important;
    border-radius: 4px !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    background: #f8fafc !important;
    color: var(--ot-text-mid) !important;
    border: 1px solid var(--ot-border) !important;
}

.ot-bottom-right {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-shrink: 0 !important;
}

.ot-action-ghost {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    background: #ffffff !important;
    color: var(--ot-text-mid) !important;
    border: 1px solid var(--ot-border) !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.ot-action-ghost:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: var(--ot-text-main) !important;
}

.ot-action-primary {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 4px 11px !important;
    border-radius: 6px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    background: #eff6ff !important;
    color: var(--ot-primary-deep) !important;
    border: 1px solid #bfdbfe !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.ot-action-primary:hover {
    background: var(--ot-primary) !important;
    border-color: var(--ot-primary) !important;
    color: #ffffff !important;
}

/* Loading, Empty & Footer Cards */
.ot-loading-card {
    background: #ffffff !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 36px 20px !important;
    text-align: center !important;
    color: var(--ot-text-muted) !important;
    font-size: 12.5px !important;
    font-weight: 500 !important;
    margin-bottom: 12px !important;
}

.ot-empty-card {
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 10px !important;
    padding: 48px 20px !important;
    text-align: center !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    margin-bottom: 12px !important;
}

.ot-empty-icon {
    width: 50px !important;
    height: 50px !important;
    margin: 0 auto 12px !important;
    border-radius: 12px !important;
    background: var(--ot-primary-soft) !important;
    color: var(--ot-primary-deep) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.ot-empty-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: var(--ot-text-main) !important;
}

.ot-empty-sub {
    font-size: 12px !important;
    color: var(--ot-text-muted) !important;
    margin-top: 4px !important;
}

.ot-empty-reset-btn {
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

.ot-footer-card {
    background: #ffffff !important;
    border: 1px solid var(--ot-border) !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    margin-top: 8px !important;
}

.ot-footer-card .pagi-bar {
    border-top: none !important;
    padding: 6px 14px !important;
    min-height: 42px !important;
    max-height: 44px !important;
    height: 42px !important;
    box-sizing: border-box !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: #ffffff !important;
    flex-wrap: nowrap !important;
    gap: 10px !important;
    width: 100% !important;
}

.ot-footer-card .pagi-info {
    font-size: 11.5px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    line-height: 1 !important;
    white-space: nowrap !important;
}

.ot-footer-card .pagi-right {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-shrink: 0 !important;
}

.ot-footer-card .pagi-arrow {
    width: 26px !important;
    height: 26px !important;
    min-width: 26px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    background: #ffffff !important;
    color: #64748b !important;
    font-size: 11px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 !important;
    line-height: 1 !important;
    transition: all 0.15s ease !important;
}

.ot-footer-card .pagi-arrow:hover:not(.pagi-disabled) {
    background: #eff6ff !important;
    border-color: #93c5fd !important;
    color: #1d4ed8 !important;
}

.ot-footer-card .pagi-arrow.pagi-disabled {
    opacity: 0.35 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
}

.ot-footer-card .pagi-page {
    min-width: 26px !important;
    height: 26px !important;
    padding: 0 6px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    background: #ffffff !important;
    color: #334155 !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    line-height: 1 !important;
    transition: all 0.15s ease !important;
}

.ot-footer-card .pagi-page:hover:not(.pagi-active-disabled) {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}

.ot-footer-card .pagi-page.pagi-active {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    pointer-events: none !important;
}

.ot-footer-card .pagi-dots {
    font-size: 11.5px !important;
    color: #94a3b8 !important;
    padding: 0 2px !important;
}

.ot-footer-card .pagi-perpage {
    height: 26px !important;
    padding: 0 8px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #475569 !important;
    background: #f8fafc !important;
    margin-left: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
}

/* ==========================================================================
   15.6" SCREEN FIT & RESPONSIVE BREAKPOINTS (1366x768 & 1920x1080)
   ========================================================================== */
@media (max-width: 1440px) {
    .ot-kpi-grid { gap: 7px !important; }
    .ot-kpi-card { padding: 9px 9px !important; }
    .ot-kpi-top  { gap: 7px !important; margin-bottom: 5px !important; }
    .ot-kpi-icon { width: 30px !important; height: 30px !important; min-width: 30px !important; border-radius: 7px !important; }
    .ot-kpi-icon svg { width: 15px !important; height: 15px !important; }
    .ot-kpi-num  { font-size: 16.5px !important; }
    .ot-kpi-lbl  { font-size: 10px !important; }
    .ot-kpi-sub  { font-size: 9.5px !important; }

    .ot-pipe-card-v2 { padding: 8px 10px 9px 20px !important; min-width: 132px !important; }

    .ot-entity-grid {
        grid-template-columns: minmax(0, 2.05fr) minmax(0, 1.65fr) minmax(0, 1.65fr) minmax(0, 1.05fr) minmax(0, 1.35fr) minmax(0, 1.25fr) !important;
        gap: 6px !important;
        padding: 8px 10px !important;
    }
    .ot-subcard { padding: 7px 9px !important; }
    .ot-search-kbd { display: none !important; }
}

@media (max-width: 1150px) {
    .ot-kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
    .ot-entity-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
}

@media (max-width: 768px) {
    .ot-dashboard { padding: 0 10px 20px !important; }
    .ot-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .ot-entity-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .ot-header-card { flex-direction: column !important; align-items: flex-start !important; }
    .ot-header-actions { width: 100% !important; justify-content: flex-start !important; }
}

@media (max-width: 520px) {
    .ot-kpi-grid { grid-template-columns: minmax(0, 1fr) !important; }
    .ot-entity-grid { grid-template-columns: minmax(0, 1fr) !important; }
}

/* Print / Executive PDF styling */
@media print {
    .ot-header-actions, .ot-toolbar-card, .ot-tabs-card, .ot-footer-card,
    .ot-bottom-right, .ot-create-offer-cta { display: none !important; }
    .ot-dashboard { padding: 0 !important; }
    .ot-entity-card { break-inside: avoid; box-shadow: none !important; }
}
</style>
@endpush

@section('content')

<div class="content d-flex flex-column flex-column-fluid pt-0 kt_content_offertracker" id="kt_content">
<div class="post d-flex flex-column-fluid" id="kt_post">
<div class="container-fluid px-0" id="kt_content_container">

<div class="ot-dashboard" id="offerTrackerDashboard">

    {{-- ══════════════════════════════════════════════════════════════
         ENTERPRISE HEADER TOOLBAR CARD (Client Tracker Inspired)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ot-header-card">
        <div class="ot-header-left">
            <div class="ot-header-icon-box">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <path d="m9 15 2 2 4-4"/>
                </svg>
            </div>
            <div>
                <div class="ot-header-title-row">
                    <h1 class="ot-header-title">Offer Tracker</h1>
                    <span class="ot-verified-badge" title="Verified ATS Module">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="m9 12 2 2 4-4" stroke-width="2.4"/>
                        </svg>
                    </span>
                    <span class="ot-header-count-pill ot-num" id="otHeaderTotalBadge">{{ $offerStats['total'] ?? 0 }}</span>
                </div>
                <div class="ot-header-subtitle">Manage compensation proposals, approval workflows, candidate acceptances, and joining readiness.</div>
            </div>
        </div>

        <div class="ot-header-actions">
            <button type="button" class="ot-hdr-btn" id="otRefreshBtn" onclick="window.jeroOfferTracker && window.jeroOfferTracker.refresh(false)" title="Sync live offers from Elasticsearch">
                <i class="fas fa-sync-alt text-primary"></i>
                <span>Sync</span>
            </button>

            <div class="dropdown">
                <button type="button" class="ot-hdr-btn" id="otExportDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span>Export</span>
                    <i class="fas fa-chevron-down" style="font-size:10px;color:#64748b;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end ot-dropdown-menu" aria-labelledby="otExportDropdownBtn">
                    <li>
                        <a class="ot-dropdown-item" href="javascript:void(0);" onclick="exportOffersCsv()">
                            <span class="ot-drop-ico csv"><i class="fas fa-file-csv"></i></span>
                            <span>
                                <span class="ot-drop-title">Export Current View (CSV)</span>
                                <span class="ot-drop-sub">Instant browser-side download</span>
                            </span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1 border-gray-200"></li>
                    <li>
                        <a class="ot-dropdown-item" href="javascript:void(0);" onclick="exportOffers()">
                            <span class="ot-drop-ico pdf"><i class="fas fa-print"></i></span>
                            <span>
                                <span class="ot-drop-title">Save / Print Report (PDF)</span>
                                <span class="ot-drop-sub">Executive offer summary</span>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="dropdown">
                <button type="button" class="ot-hdr-btn" id="otOfferSettingsBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Offer Configuration & Settings">
                    <i class="fas fa-cog text-primary"></i>
                    <span>Offer Settings</span>
                    <i class="fas fa-chevron-down" style="font-size:10px;color:#64748b;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end ot-dropdown-menu" aria-labelledby="otOfferSettingsBtn" style="min-width: 250px;">
                    <li>
                        <h6 class="dropdown-header text-uppercase text-muted fw-bolder fs-8 tracking-wider px-4 pt-2 pb-1">Offer Configuration</h6>
                    </li>
                    <li>
                        <a class="ot-dropdown-item" href="{{ route('service_company.account.index') }}#offerterms" target="_blank">
                            <span class="ot-drop-ico pdf"><i class="fas fa-file-contract"></i></span>
                            <span>
                                <span class="ot-drop-title">Offer Letter Templates</span>
                                <span class="ot-drop-sub">Manage legal terms &amp; wording</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="ot-dropdown-item" href="{{ route('service_company.account.index') }}#offercomponent" target="_blank">
                            <span class="ot-drop-ico csv"><i class="fas fa-coins"></i></span>
                            <span>
                                <span class="ot-drop-title">Compensation Components</span>
                                <span class="ot-drop-sub">Salary, bonus &amp; allowance rules</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="ot-dropdown-item" href="{{ route('service_company.account.index') }}#interview_Stage" target="_blank">
                            <span class="ot-drop-ico rst"><i class="fas fa-tasks"></i></span>
                            <span>
                                <span class="ot-drop-title">Approval Stages &amp; Pipeline</span>
                                <span class="ot-drop-sub">Stage transitions &amp; sign-offs</span>
                            </span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1 border-gray-200"></li>
                    <li>
                        <h6 class="dropdown-header text-uppercase text-muted fw-bolder fs-8 tracking-wider px-4 pt-1 pb-1">Quick Filters</h6>
                    </li>
                    <li>
                        <a class="ot-dropdown-item" href="javascript:void(0);" onclick="selectOfferTab('pending')">
                            <span class="ot-drop-ico rst"><i class="far fa-clock"></i></span>
                            <span>
                                <span class="ot-drop-title">Pending Offers</span>
                                <span class="ot-drop-sub">Review offers awaiting creation</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="ot-dropdown-item" href="javascript:void(0);" onclick="selectOfferTab('rejected')">
                            <span class="ot-drop-ico rst"><i class="fas fa-times-circle text-danger"></i></span>
                            <span>
                                <span class="ot-drop-title">Rejected Offers</span>
                                <span class="ot-drop-sub">Review candidate declined offers</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="ot-dropdown-item" href="javascript:void(0);" onclick="window.jeroOfferTracker && window.jeroOfferTracker.resetFilters()">
                            <span class="ot-drop-ico csv"><i class="fas fa-undo-alt text-primary"></i></span>
                            <span>
                                <span class="ot-drop-title">Reset All Filters</span>
                                <span class="ot-drop-sub">Clear search, location &amp; tab</span>
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
        $totalOffersInit   = (int) ($offerStats['total'] ?? 0);
        $pendingOffersInit = (int) ($offerStats['pending'] ?? 0);
        $sentOffersInit    = (int) ($offerStats['sent'] ?? 0);
        $acceptedInit      = (int) ($offerStats['accepted'] ?? 0);
        $rejectedInit      = (int) ($offerStats['rejected'] ?? 0);
        $expiredInit       = (int) ($offerStats['expired'] ?? 0);
        $acceptedPctInit   = $totalOffersInit > 0 ? round(($acceptedInit / $totalOffersInit) * 100) : 0;
    @endphp
    <div class="ot-kpi-grid" id="otKpiGrid">
        {{-- Card 1: Total Offers --}}
        <button type="button" class="ot-kpi-card ot-kpi-filter active" data-tab="all" onclick="selectOfferTab('all')">
            <div class="ot-kpi-top">
                <div class="ot-kpi-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <div class="ot-kpi-meta">
                    <div class="ot-kpi-num-row">
                        <div class="ot-kpi-num" data-kpi="total">{{ $totalOffersInit }}</div>
                        <span class="ot-kpi-pending-badge" id="kpiPendingPill" style="{{ $pendingOffersInit > 0 ? '' : 'display:none;' }}">
                            <span class="ot-kpi-dot-pulse"></span>
                            <span id="kpiPendingPillText">{{ $pendingOffersInit }} pending</span>
                        </span>
                    </div>
                    <div class="ot-kpi-lbl">Total Offers</div>
                </div>
            </div>
            <div class="ot-kpi-sub slate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>All offers tracked</span>
            </div>
        </button>

        {{-- Card 2: Pending Offers --}}
        <button type="button" class="ot-kpi-card ot-kpi-filter" data-tab="pending" onclick="selectOfferTab('pending')">
            <div class="ot-kpi-top">
                <div class="ot-kpi-icon amber">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="ot-kpi-meta">
                    <div class="ot-kpi-num" data-kpi="pending">{{ $pendingOffersInit }}</div>
                    <div class="ot-kpi-lbl">Pending Offers</div>
                </div>
            </div>
            <div class="ot-kpi-sub amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m18 15-6-6-6 6"/></svg>
                <span>Awaiting action</span>
            </div>
        </button>

        {{-- Card 3: Offer Released --}}
        <button type="button" class="ot-kpi-card ot-kpi-filter" data-tab="sent" onclick="selectOfferTab('sent')">
            <div class="ot-kpi-top">
                <div class="ot-kpi-icon indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </div>
                <div class="ot-kpi-meta">
                    <div class="ot-kpi-num" data-kpi="sent">{{ $sentOffersInit }}</div>
                    <div class="ot-kpi-lbl">Offer Released</div>
                </div>
            </div>
            <div class="ot-kpi-sub indigo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <span>Emailed to candidate</span>
            </div>
        </button>

        {{-- Card 4: Accepted --}}
        <button type="button" class="ot-kpi-card ot-kpi-filter" data-tab="accepted" onclick="selectOfferTab('accepted')">
            <div class="ot-kpi-top">
                <div class="ot-kpi-icon green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div class="ot-kpi-meta">
                    <div class="ot-kpi-num" data-kpi="accepted">{{ $acceptedInit }}</div>
                    <div class="ot-kpi-lbl">Accepted</div>
                </div>
            </div>
            <div class="ot-kpi-sub green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
                <span id="kpiAcceptedPct">{{ $acceptedPctInit }}% of total offers</span>
            </div>
        </button>

        {{-- Card 5: Offer Rejected --}}
        <button type="button" class="ot-kpi-card ot-kpi-filter" data-tab="rejected" onclick="selectOfferTab('rejected')">
            <div class="ot-kpi-top">
                <div class="ot-kpi-icon red">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div class="ot-kpi-meta">
                    <div class="ot-kpi-num" data-kpi="rejected">{{ $rejectedInit }}</div>
                    <div class="ot-kpi-lbl">Offer Rejected</div>
                </div>
            </div>
            <div class="ot-kpi-sub red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <span>Candidate declined</span>
            </div>
        </button>

        {{-- Card 6: Expired --}}
        <button type="button" class="ot-kpi-card ot-kpi-filter" data-tab="expired" onclick="selectOfferTab('expired')">
            <div class="ot-kpi-top">
                <div class="ot-kpi-icon rose">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div class="ot-kpi-meta">
                    <div class="ot-kpi-num" data-kpi="expired">{{ $expiredInit }}</div>
                    <div class="ot-kpi-lbl">Expired</div>
                </div>
            </div>
            <div class="ot-kpi-sub rose">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
                <span>Need renewal</span>
            </div>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         RULE 20 TIER 1: SEARCH & FILTER TOOLBAR CARD
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ot-toolbar-card">
        <div class="ot-search-subcard">
            <i class="fas fa-search"></i>
            <input type="text" id="ot_search" placeholder="Search by candidate name, job title, client, recruiter, or ID..." autocomplete="off" />
            <kbd class="ot-search-kbd" id="otSearchKbd" title="Press Ctrl+K to search">Ctrl+K</kbd>
        </div>

        <div class="ot-inline-filters">
            <div class="ot-filter-pill">
                <span class="ot-filter-pill-lbl"><i class="fas fa-map-marker-alt text-muted"></i> Location:</span>
                <select class="ot-filter-select" id="ot_country" aria-label="Filter by location">
                    <option value="">All Locations</option>
                    @foreach($countries as $c)
                    <option value="{{ $c->id }}">{{ $c->country_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="ot-filter-pill">
                <span class="ot-filter-pill-lbl"><i class="fas fa-sort-amount-down text-muted"></i> Sort:</span>
                <select class="ot-filter-select" id="ot_sort" aria-label="Sort offers">
                    <option value="">Latest Updated</option>
                    <option value="10">Newest (Offer Date)</option>
                    <option value="11">Oldest (Offer Date)</option>
                    <option value="12">Newest (Join Date)</option>
                    <option value="13">Oldest (Join Date)</option>
                    <option value="14">Low → High (Amount)</option>
                    <option value="15">High → Low (Amount)</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         RULE 20 TIER 3: PRIMARY OFFER CARD + SUB-CARDS STREAM
         ══════════════════════════════════════════════════════════════ --}}
    @if(in_array('view_offer', $userPermissions))
        <div class="ot-stream-wrapper">
            <div id="offer-tracker" class="ot-cards-stream">
                <div class="ot-loading-card">
                    <img src="{{ asset('assets/images/Loading2.gif') }}" height="28" alt=""> &nbsp; Loading offers...
                </div>
            </div>
        </div>

        <div class="ot-footer-card">
            <div id="ot_pagination"></div>
        </div>
    @else
        <div class="ot-empty-card">
            <div class="ot-empty-icon" style="background:#fef2f2!important;color:#dc2626!important;">
                <i class="fas fa-lock"></i>
            </div>
            <div class="ot-empty-title">Access Denied</div>
            <div class="ot-empty-sub">You do not have permission to view Offer Tracker. Contact your administrator.</div>
        </div>
    @endif

</div>{{-- /ot-dashboard --}}

{{-- Add Approver — right-side drawer (replaces #fwd_offer modal) --}}
<style>
#kt_fwd_offer_drawer {
    position: fixed !important;
    top: 0; right: 0; bottom: 0;
    width: 700px !important;
    z-index: 1055 !important;
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform .3s ease;
    box-shadow: -4px 0 24px rgba(0,0,0,.12);
}
#kt_fwd_offer_drawer.drawer-on {
    transform: translateX(0) !important;
}
#kt_fwd_offer_overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,.35);
    z-index: 1054;
}
#kt_fwd_offer_overlay.active { display: block; }
#application_fwd_offer_body_content {
    overflow: hidden !important;
    flex: 1 1 0 !important;
    min-height: 0 !important;
    position: relative !important;
}
</style>

<div id="kt_fwd_offer_overlay"></div>
<div id="kt_fwd_offer_drawer" class="bg-body">
    <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column" style="border-radius:0;">
        <div class="card-header px-5 py-3 d-flex align-items-center justify-content-between flex-shrink-0" style="border-bottom:1px solid #e4e6ef; min-height:56px;">
            <h3 class="fw-bold text-dark mb-0 fs-5" id="titleSet">Add Approver</h3>
            <button type="button" id="kt_fwd_offer_close" class="btn btn-sm btn-icon btn-active-light-danger">
                <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)"/>
                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"/>
                    </svg>
                </span>
            </button>
        </div>
        <div id="application_fwd_offer_body_content" class="flex-grow-1 overflow-auto position-relative px-0 py-0"></div>
    </div>
</div>

{{-- Change Candidate Stage — right-side drawer --}}
<style>
  #csd_drawer .sd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); flex-wrap: wrap; position: sticky; top: 0; z-index: 20; }
  #csd_drawer .sd-topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
  #csd_drawer .sd-topbar-ico { width: 42px; height: 42px; border-radius: 10px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; box-shadow: 0 1px 2px rgba(37, 99, 235, 0.08); }
  #csd_drawer .sd-topbar-title { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #0f172a; line-height: 1.3; }
  #csd_drawer .sd-topbar-sub { font-family: 'Poppins', sans-serif; font-size: 11.5px; font-weight: 400; color: #64748b; margin-top: 1px; }
  #csd_drawer .sd-topbar-right { display: flex; align-items: center; gap: 10px; }
  #csd_drawer .sd-btn-cancel { border: 1px solid #cbd5e1; background: #ffffff; color: #334155; border-radius: 8px; padding: 8px 18px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04); transition: all .15s ease; }
  #csd_drawer .sd-btn-cancel:hover { background: #f1f5f9; border-color: #94a3b8; color: #0f172a; }
  #csd_drawer .sd-btn-confirm { border: none; background: #2563eb; color: #fff; border-radius: 8px; padding: 9px 20px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: filter .15s; }
  #csd_drawer .sd-btn-confirm:hover { filter: brightness(0.94); }
  #csd_drawer .sd-btn-confirm:disabled { background: #c2c9d6 !important; cursor: not-allowed; }
  #csd_drawer .sd-divider { width: 1px; height: 22px; background: #e2e8f0; }
  #csd_drawer .sd-close { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #64748b; border: 1px solid transparent; cursor: pointer; flex-shrink: 0; transition: all .15s ease; }
  #csd_drawer .sd-close:hover { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
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
(function () {
    'use strict';

    /* ── state ── */
    var state = {
        page          : 1,
        size          : parseInt(localStorage.getItem('candidatePerPage'), 10) || 10,
        search        : '',
        country       : '',
        sort          : '',
        tab           : 'all',
        pipelineRange : 'current',
    };

    /* ─────────────────────────────────────────────────────────────────────────────
       JeroOfferLocalStore — Native Browser IndexedDB Engine for 0% Avoidable Hits
       (Rule 6: Client-First / User RAM & Storage; Multi-Tenant Isolation)
    ───────────────────────────────────────────────────────────────────────────── */
    var JERO_OFFER_DB_NAME = 'JeroOfferLocalStore';
    var JERO_OFFER_DB_VERSION = 1;
    var JERO_TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
    var JERO_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
    var JERO_OFFER_TTL_MS = 10 * 60 * 1000; // 10 minutes cache TTL

    var clientCache = {};
    var activeAjaxRequest = null;
    var offerBroadcastChannel = null;

    var JeroOfferLocalStore = (function () {
        var dbPromise = null;

        function getDB() {
            if (typeof window === 'undefined' || !window.indexedDB) {
                return Promise.resolve(null);
            }
            if (dbPromise) return dbPromise;

            dbPromise = new Promise(function (resolve) {
                try {
                    var req = window.indexedDB.open(JERO_OFFER_DB_NAME, JERO_OFFER_DB_VERSION);
                    req.onupgradeneeded = function (e) {
                        var db = e.target.result;
                        if (!db.objectStoreNames.contains('offers_cache')) {
                            db.createObjectStore('offers_cache', { keyPath: 'cache_key' });
                        }
                        if (!db.objectStoreNames.contains('meta')) {
                            db.createObjectStore('meta', { keyPath: 'id' });
                        }
                    };
                    req.onsuccess = function (e) {
                        var db = e.target.result;
                        try {
                            var tx = db.transaction(['meta'], 'readwrite');
                            var metaStore = tx.objectStore('meta');
                            var metaReq = metaStore.get('current_context');
                            metaReq.onsuccess = function () {
                                var meta = metaReq.result;
                                if (meta && (meta.tenant_id !== JERO_TENANT_ID || meta.user_id !== JERO_USER_ID)) {
                                    try {
                                        var wipeTx = db.transaction(['offers_cache', 'meta'], 'readwrite');
                                        wipeTx.objectStore('offers_cache').clear();
                                        wipeTx.objectStore('meta').put({
                                            id: 'current_context',
                                            tenant_id: JERO_TENANT_ID,
                                            user_id: JERO_USER_ID,
                                            updated_at: Date.now()
                                        });
                                    } catch (ex) {}
                                } else if (!meta) {
                                    try {
                                        metaStore.put({
                                            id: 'current_context',
                                            tenant_id: JERO_TENANT_ID,
                                            user_id: JERO_USER_ID,
                                            updated_at: Date.now()
                                        });
                                    } catch (ex) {}
                                }
                            };
                            metaReq.onerror = function () {};
                        } catch (err) {}
                        resolve(db);
                    };
                    req.onerror = function () { resolve(null); };
                    req.onblocked = function () { resolve(null); };
                } catch (err) {
                    resolve(null);
                }
            });

            return dbPromise;
        }

        return {
            get: function (key) {
                return Promise.race([
                    getDB().then(function (db) {
                        if (!db || !key) return null;
                        return new Promise(function (resolve) {
                            try {
                                var tx = db.transaction(['offers_cache'], 'readonly');
                                var store = tx.objectStore('offers_cache');
                                var req = store.get(key);
                                req.onsuccess = function () {
                                    var res = req.result;
                                    if (res && (Date.now() - res.timestamp < JERO_OFFER_TTL_MS)) {
                                        resolve(res.payload);
                                    } else {
                                        resolve(null);
                                    }
                                };
                                req.onerror = function () { resolve(null); };
                            } catch (e) {
                                resolve(null);
                            }
                        });
                    }).catch(function () { return null; }),
                    new Promise(function (resolve) {
                        setTimeout(function () { resolve(null); }, 250);
                    })
                ]);
            },
            set: function (key, payload) {
                return getDB().then(function (db) {
                    if (!db || !key || !payload) return;
                    try {
                        var tx = db.transaction(['offers_cache'], 'readwrite');
                        var store = tx.objectStore('offers_cache');
                        store.put({
                            cache_key: key,
                            payload: payload,
                            timestamp: Date.now()
                        });
                    } catch (e) {}
                }).catch(function () {});
            },
            purge: function () {
                return getDB().then(function (db) {
                    if (!db) return;
                    try {
                        var tx = db.transaction(['offers_cache'], 'readwrite');
                        tx.objectStore('offers_cache').clear();
                    } catch (e) {}
                }).catch(function () {});
            }
        };
    })();

    /* ── Multi-Tab Synchronization via BroadcastChannel ── */
    if (typeof window.BroadcastChannel !== 'undefined') {
        try {
            offerBroadcastChannel = new BroadcastChannel('jero_offer_tracker_sync');
            offerBroadcastChannel.onmessage = function (event) {
                if (!event || !event.data) return;
                if (event.data.type === 'OFFER_MUTATION_SYNC') {
                    invalidateClientCache(false);
                    fetchTable(false);
                }
            };
        } catch (e) {}
    }

    function invalidateClientCache(broadcastSync) {
        clientCache = {};
        JeroOfferLocalStore.purge();
        if (broadcastSync !== false && offerBroadcastChannel) {
            try {
                offerBroadcastChannel.postMessage({ type: 'OFFER_MUTATION_SYNC', timestamp: Date.now() });
            } catch (e) {}
        }
    }

    var URL_CANDIDATES = '{!! route('service_company.cvtracker.allcandidates') !!}';

    /* ── Sync active tab + KPI card state ── */
    function syncActiveTabUI(tabKey) {
        document.querySelectorAll('#ot_tabs .ot-tab-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.filter === tabKey);
        });
        document.querySelectorAll('#otKpiGrid .ot-kpi-filter').forEach(function (card) {
            card.classList.toggle('active', card.dataset.tab === tabKey);
        });
    }

    window.selectOfferTab = function (tabKey) {
        if (!tabKey) tabKey = 'all';
        state.tab = tabKey;
        syncActiveTabUI(tabKey);
        fetchTable(true);
    };

    /* ── fetch table (3-Layer Execution: RAM -> IndexedDB -> Server) ── */
    function fetchTable(resetPage) {
        if (resetPage) state.page = 1;

        var cacheKey = JSON.stringify({
            p: state.page,
            s: state.size,
            q: state.search,
            c: state.country,
            srt: state.sort,
            t: state.tab,
            pr: state.pipelineRange
        });

        if (clientCache[cacheKey]) {
            renderTableResponse(clientCache[cacheKey]);
            return;
        }

        JeroOfferLocalStore.get(cacheKey).then(function (cachedInIDB) {
            if (cachedInIDB) {
                clientCache[cacheKey] = cachedInIDB;
                renderTableResponse(cachedInIDB);
                return;
            }
            executeServerFetch(cacheKey);
        }).catch(function () {
            executeServerFetch(cacheKey);
        });
    }

    function setSyncSpin(isSpinning) {
        var btn = document.getElementById('otRefreshBtn');
        if (btn) btn.classList.toggle('is-syncing', !!isSpinning);
    }

    function executeServerFetch(cacheKey) {
        if (activeAjaxRequest && activeAjaxRequest.readyState !== 4) {
            try { activeAjaxRequest.abort(); } catch (e) {}
        }

        setSyncSpin(true);
        var trackerEl = document.getElementById('offer-tracker');
        if (trackerEl) {
            trackerEl.innerHTML = '<div class="ot-loading-card"><img src="{{ asset('assets/images/Loading2.gif') }}" height="28" alt=""> &nbsp; Loading offers...</div>';
        }

        activeAjaxRequest = $.ajax({
            url  : URL_CANDIDATES + '?page=' + state.page,
            type : 'GET',
            dataType: 'json',
            data : {
                status_cus       : 5,
                size             : state.size,
                per_page         : state.size,
                cus_search       : state.search,
                country          : state.country,
                sortingfilter    : state.sort,
                offer_tab_filter : state.tab,
                pipeline_range   : state.pipelineRange,
            },
            success: function (resp) {
                activeAjaxRequest = null;
                setSyncSpin(false);
                if (typeof resp === 'string') {
                    try { resp = JSON.parse(resp); } catch (e) {}
                }
                clientCache[cacheKey] = resp;
                JeroOfferLocalStore.set(cacheKey, resp);
                renderTableResponse(resp);
            },
            error: function (xhr, status) {
                setSyncSpin(false);
                if (status === 'abort') return;
                activeAjaxRequest = null;
                var el = document.getElementById('offer-tracker');
                if (el) {
                    el.innerHTML = '<div class="ot-empty-card"><div class="ot-empty-title" style="color:#dc2626;">Error Loading Offers</div><div class="ot-empty-sub">Unable to reach search projection. Please click Sync or refresh.</div></div>';
                }
            }
        });
    }

    function renderTableResponse(resp) {
        if (!resp) return;
        var trackerEl = document.getElementById('offer-tracker');
        var pagiEl    = document.getElementById('ot_pagination');
        var summaryEl = document.getElementById('otShowingSummary');

        if (trackerEl) {
            var html = resp.html;
            if (typeof html === 'string' && html.trim().length > 0) {
                trackerEl.innerHTML = html;
            } else {
                trackerEl.innerHTML = '<div class="ot-empty-card"><div class="ot-empty-title">No Offers Found</div><div class="ot-empty-sub">No offer records match your active filter criteria.</div></div>';
            }
        }
        if (pagiEl && resp.pagination !== undefined) {
            pagiEl.innerHTML = resp.pagination || '';
        }
        if (summaryEl && resp.total !== undefined) {
            var totalVal = Number(resp.total) || 0;
            summaryEl.textContent = totalVal + (totalVal === 1 ? ' offer matching' : ' offers matching');
        }
        if (resp.counts) {
            try {
                updateOfferCounts(resp.counts, resp.pipelineCounts);
            } catch (e) {
                console.error('[Offer Tracker] Error updating counts:', e);
            }
        }
    }

    /* ── Sync tab badges + KPI cards + pipeline from Elasticsearch counts ── */
    function updateOfferCounts(c, p) {
        if (!c) return;
        var n = function (v) { return (v != null ? Number(v) : 0); };

        var badges = {
            tab_total: c.total, tab_pending: c.pending, tab_sent: c.sent,
            tab_accepted: c.accepted, tab_rejected: c.rejected, tab_expired: c.expired
        };
        Object.keys(badges).forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.textContent = n(badges[id]);
        });

        var kpi = {
            total: n(c.total),
            pending: n(c.pending),
            sent: n(c.sent),
            accepted: n(c.accepted),
            rejected: n(c.rejected),
            expired: n(c.expired),
            joining_soon: n(c.joining_soon)
        };
        document.querySelectorAll('[data-kpi]').forEach(function (el) {
            var k = el.getAttribute('data-kpi');
            if (kpi[k] != null) el.textContent = kpi[k];
        });

        var headerBadge = document.getElementById('otHeaderTotalBadge');
        if (headerBadge) headerBadge.textContent = kpi.total;

        var pendingPill     = document.getElementById('kpiPendingPill');
        var pendingPillText = document.getElementById('kpiPendingPillText');
        if (pendingPill && pendingPillText) {
            if (kpi.pending > 0) {
                pendingPill.style.display = 'inline-flex';
                pendingPillText.textContent = kpi.pending + ' pending';
            } else {
                pendingPill.style.display = 'none';
            }
        }

        var acceptedPctEl = document.getElementById('kpiAcceptedPct');
        if (acceptedPctEl) {
            var accPct = kpi.total > 0 ? Math.round((kpi.accepted / kpi.total) * 100) : 0;
            acceptedPctEl.textContent = accPct + '% of total offers';
        }

        /* Pipeline funnel arrows */
        var pc = p || c;
        var ptotal = +pc.total || 0;
        var pctOf = function (v) { return ptotal > 0 ? Math.round((v / ptotal) * 100) : 0; };
        var ratio = pctOf(n(pc.joined));
        var pipe = {
            total:    { count: ptotal,        pct: 100 },
            sent:     { count: n(pc.sent),    pct: pctOf(n(pc.sent)) },
            accepted: { count: n(pc.accepted),pct: pctOf(n(pc.accepted)) },
            rejected: { count: n(pc.rejected),pct: pctOf(n(pc.rejected)) },
            joined:   { count: n(pc.joined),  pct: ratio },
            ratio:    { count: ratio + '%',   pct: ratio, sub: n(pc.joined) + '/' + ptotal }
        };
        document.querySelectorAll('[data-pipe]').forEach(function (card) {
            var d = pipe[card.getAttribute('data-pipe')];
            if (!d) return;
            var big   = card.querySelector('.ot-pipe-big');
            var pctEl = card.querySelector('.ot-pipe-pct-v2');
            var subEl = card.querySelector('.ot-pipe-sub-v2');
            if (big)   big.textContent = d.count;
            if (pctEl) pctEl.textContent = d.pct + '%';
            if (subEl) subEl.textContent = d.sub || '';
        });
    }

    function resetFilters() {
        state.page    = 1;
        state.search  = '';
        state.country = '';
        state.sort    = '';
        state.tab     = 'all';

        var sInp = document.getElementById('ot_search');
        var sClr = document.getElementById('otSearchClear');
        var cSel = document.getElementById('ot_country');
        var rSel = document.getElementById('ot_sort');
        if (sInp) sInp.value = '';
        if (sClr) sClr.style.display = 'none';
        if (cSel) cSel.value = '';
        if (rSel) rSel.value = '';

        syncActiveTabUI('all');
        fetchTable(true);
    }

    /* ── Setup Event Listeners after DOM is ready ── */
    function setupEventListeners() {
        var searchTimer;
        var searchInput = document.getElementById('ot_search');
        var searchClear = document.getElementById('otSearchClear');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                var val = this.value;
                if (searchClear) searchClear.style.display = val.length > 0 ? 'inline-flex' : 'none';
                searchTimer = setTimeout(function () {
                    state.search = val;
                    fetchTable(true);
                }, 300);
            });
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && this.value) {
                    this.value = '';
                    if (searchClear) searchClear.style.display = 'none';
                    state.search = '';
                    fetchTable(true);
                }
            });
        }

        if (searchClear) {
            searchClear.addEventListener('click', function () {
                if (searchInput) searchInput.value = '';
                this.style.display = 'none';
                state.search = '';
                fetchTable(true);
            });
        }

        /* Ctrl+K shortcut focuses search */
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                if (searchInput) searchInput.focus();
            }
        });

        var countrySelect = document.getElementById('ot_country');
        if (countrySelect) {
            countrySelect.addEventListener('change', function () {
                state.country = this.value;
                fetchTable(true);
            });
        }

        var sortSelect = document.getElementById('ot_sort');
        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                state.sort = this.value;
                fetchTable(true);
            });
        }

        var pagiWrap = document.getElementById('ot_pagination');
        if (pagiWrap) {
            pagiWrap.addEventListener('change', function (e) {
                if (e.target.classList.contains('pagi-perpage')) {
                    var selectedPerPage = parseInt(e.target.value, 10);
                    if (selectedPerPage) {
                        state.size = selectedPerPage;
                        localStorage.setItem('candidatePerPage', selectedPerPage);
                        fetchTable(true);
                    }
                }
            });

            pagiWrap.addEventListener('click', function (e) {
                var link = e.target.closest('.pagi-page, .pagi-arrow');
                if (!link || link.classList.contains('pagi-disabled') || link.classList.contains('pagi-active-disabled')) return;
                e.preventDefault();
                var pg = parseInt(link.dataset.page, 10);
                if (!pg) return;
                state.page = pg;
                fetchTable(false);
            });
        }

        var pipeRangeEl = document.getElementById('ot_pipeline_range');
        if (pipeRangeEl) {
            pipeRangeEl.addEventListener('change', function () {
                state.pipelineRange = this.value;
                fetchTable(false);
            });
        }

        document.querySelectorAll('#ot_tabs .ot-tab-btn').forEach(function (tab) {
            tab.addEventListener('click', function () {
                selectOfferTab(this.dataset.filter);
            });
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
        $.ajax({ type:'GET', url:url, success:function(r){
            if(r.status=='success') {
                $('#stage_body_content').html(r.view);
                $('#csd_drawer_subtitle').text($('#stage_body_content .csd-info-name').first().text());
            }
        } });
    });

    $(document).on('click', '#custom_stage_submit', function (e) {
        e.preventDefault();
        var ms = $('input[name="master_status"]:checked').val();
        var cm = $('#comment').val();
        if (ms == 8 && !cm) { Swal.fire("Warning",'InReview Comment Required!',"info"); return; }
        if ($('#main_substage').is(':visible') && $('input[name="sub_stages"]:checked').length === 0) { Swal.fire("Warning",'Please select a sub stage to continue.',"info"); return; }
        $(this).prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Waiting...');
        $.ajax({
            url:'{{ route('service_company.job-applications.stage_submit') }}', type:'POST',
            data: $('#status_frm').serialize(),
            success:function(r){
                $('#custom_stage_submit').prop("disabled",false).html('<span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>');
                if (typeof csdUpdateSubmitButton === 'function') {
                    csdUpdateSubmitButton($('input[name="master_status"]:checked').attr('id'));
                }
                if(r.status=='success'){
                    Swal.fire("Success",'Stage updated!',"success").then(function(){ $("#stage_sub_stage_close").click(); });
                    invalidateClientCache();
                    fetchTable(false);
                } else if (r.status == 'cvpending') {
                    Swal.fire("Warning", 'Resume pending this job application.', "warning").then(function() {
                        $("#stage_sub_stage_close").click();
                    });
                } else if (r.status == 'candidatevalidation') {
                    if (typeof window.showStageRequiredFieldsModal === 'function') {
                        window.showStageRequiredFieldsModal(r);
                    } else {
                        Swal.fire("Warning", r.message, "warning").then(function() {
                            $("#stage_sub_stage_close").click();
                        });
                    }
                }
            }
        });
    });

    /* ── Add Approver drawer helpers ── */
    function openApproverDrawer() {
        $('#kt_fwd_offer_drawer').addClass('drawer-on');
        $('#kt_fwd_offer_overlay').addClass('active');
        $('body').css('overflow', 'hidden');
        setTimeout(function() {
            var sc = document.getElementById('kt_activities_scroll');
            if (sc) sc.style.removeProperty('height');
        }, 50);
    }
    function closeApproverDrawer() {
        $('#kt_fwd_offer_drawer').removeClass('drawer-on');
        $('#kt_fwd_offer_overlay').removeClass('active');
        $('body').css('overflow', '');
        setTimeout(function() {
            var sc = document.getElementById('kt_activities_scroll');
            if (sc) sc.style.removeProperty('height');
        }, 50);
    }
    $(document).on('click', '#kt_fwd_offer_close, #kt_fwd_offer_overlay', closeApproverDrawer);

    $(document).on('click', '.fwd_offer', function (e) {
        e.preventDefault();
        var jobId     = $(this).data('jobid');
        var candidate = $(this).data('candidate');
        var mtype     = $(this).data('mtype');
        var offerId   = $(this).data('offerid');
        var url = '{{ route('service_company.cvtracker.offer_send_mail', ':id') }}'.replace(':id', jobId);
        $('#titleSet').text('Add Approver');
        $('#application_fwd_offer_body_content').html('<div style="padding:60px;text-align:center;"><img src="{{ asset('assets/images/Loading2.gif') }}" height="40" alt="Loading..."></div>');
        openApproverDrawer();
        $.ajax({
            type:'POST', url:url,
            data:{_token:'{{ csrf_token() }}', _method:'POST', arr:[candidate], jobIdArr:[jobId], mtype:mtype, offerId:offerId},
            success:function(r){
                if(r.status=='success'){
                    $('#titleSet').text(r.title);
                    $('#application_fwd_offer_body_content').html(r.view);
                }
            },
            error: function() {
                $('#application_fwd_offer_body_content').html('<div style="padding:40px;text-align:center;color:#ef4444;">Failed to load. Please try again.</div>');
            }
        });
    });

    /* Employment Type Selector click handling on Compensation subcard */
    $(document).on('click', '.ot-emp-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        var empType = $(this).data('emp-type');
        var offerId = $(this).data('row-offerid');
        var $subcard = $(this).closest('.ot-subcard-comp-status');
        $subcard.attr('data-emp-type', empType);
        $subcard.find('.ot-emp-btn').removeClass('is-active');
        $(this).addClass('is-active');

        $('#kt_create_offer_toggle').trigger('click');
        var url = "{{ route('service_company.job-applications.create_offer', ':id') }}";
        url = url.replace(':id', offerId) + '?emp_type=' + encodeURIComponent(empType);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function () {
                $('#offer_create_show_right_bar').html('<img src="' + baseurl + '" height="35px" width="100%">');
            },
            success: function (response) {
                if (response.status === 'permisionInfo') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            text: response.view,
                            icon: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                    $('#kt_create_offer_toggle').trigger('click');
                    return false;
                }
                $('#offer_create_show_right_bar').html(response.view);
                if ($('#employment_type').length) {
                    $('#employment_type').val(empType).trigger('change');
                }
            },
            error: function (xhr) {
                var errTxt = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to load offer details. Please try again.';
                $('#offer_create_show_right_bar').html(
                    '<div class="card card-flush m-6 p-8 text-center border border-dashed border-danger">' +
                    '  <div class="text-danger fw-bold fs-4 mb-2">Error Loading Offer</div>' +
                    '  <div class="text-gray-700 fs-6 mb-4">' + errTxt + '</div>' +
                    '  <div><button type="button" class="btn btn-sm btn-light-primary" onclick="$(\'#kt_create_offer_toggle\').trigger(\'click\');">Close</button></div>' +
                    '</div>'
                );
            }
        });
    });

    /* Client-job offer warning */
    $(document).on('click', '.ot-client-offer-warn', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Not Allowed',
                text: 'This offer belongs to a client job. You cannot create or edit the offer here.',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: { confirmButton: 'btn btn-primary' }
            });
        }
    });

    /* Client-First Browser-Side CSV Export (0% Server Read Strain) */
    window.exportOffersCsv = function () {
        var cards = document.querySelectorAll('#offer-tracker .ot-entity-card');
        if (!cards.length) {
            if (typeof Swal !== 'undefined') Swal.fire('No Data', 'There are no visible offers to export.', 'info');
            return;
        }
        var esc = function (v) { return '"' + String(v || '').replace(/"/g, '""') + '"'; };
        var rows = [['Candidate', 'Job Title', 'Job ID', 'Client', 'Compensation', 'Offer Status', 'Joining', 'Owner', 'Offer Ref']];
        cards.forEach(function (card) {
            rows.push([
                card.dataset.candName || '',
                card.dataset.jobTitle || '',
                card.dataset.jobId || '',
                card.dataset.clientName || '',
                card.dataset.comp || '',
                card.dataset.offerStatus || '',
                card.dataset.joining || '',
                card.dataset.owner || '',
                card.dataset.offerId ? ('#OFF-' + card.dataset.offerId) : ''
            ]);
        });
        var csv = rows.map(function (r) { return r.map(esc).join(','); }).join('\r\n');
        var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'Offer-Tracker-' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    /* Executive Print-to-PDF Export */
    window.exportOffers = function () {
        var prevTitle = document.title;
        document.title = 'Offer-Tracker-' + new Date().toISOString().slice(0,10);
        var done = function(){ document.title = prevTitle; window.removeEventListener('afterprint', done); };
        window.addEventListener('afterprint', done);
        window.print();
        setTimeout(done, 1500);
    };

    /* ── Real-time Reverb / WebSocket Delta Synchronization ── */
    var JERO_COMPANY_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
    var reverbDebounceTimer = null;

    function initOfferReverbSync() {
        if (typeof window.Echo === 'undefined' || typeof window.Echo.private !== 'function' || !JERO_COMPANY_ID) {
            return;
        }
        try {
            window.Echo.private('company.' + JERO_COMPANY_ID)
                .listen('.offer.updated', function () {
                    invalidateClientCache();
                    if (reverbDebounceTimer) clearTimeout(reverbDebounceTimer);
                    reverbDebounceTimer = setTimeout(function () {
                        fetchTable(false);
                    }, 250);
                });

            if (window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                window.Echo.connector.pusher.connection.bind('connected', function () {
                    invalidateClientCache();
                    fetchTable(false);
                });
            }
        } catch (err) {
            console.warn('[Reverb Offer Tracker] Subscription warning:', err);
        }
    }

    /* ── Global Bridge for Drawers & Sub-Components ── */
    window.jeroOfferTracker = {
        refresh: function (resetPage) {
            invalidateClientCache(true);
            fetchTable(resetPage === true);
        },
        invalidateClientCache: invalidateClientCache,
        fetchTable: fetchTable,
        resetFilters: resetFilters
    };

    window.addEventListener('jero:offer_updated', function () {
        invalidateClientCache(false);
        fetchTable(false);
    });

    /* ── Init ── */
    $(function () {
        setupEventListeners();
        fetchTable(false);
        initOfferReverbSync();
    });

})();
</script>
@endpush
