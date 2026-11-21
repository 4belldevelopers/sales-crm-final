@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
/* ==========================================================================
   JERO SERVICE COMPANY — MARKETPLACE AGENCIES DISCOVERY (RULE 20)
   Modern Enterprise Card + Sub-Card Design System
   Fits 15.6-inch laptop displays (1366x768 & 1920x1080) with zero horizontal overflow
   ========================================================================== */

/* ── Activities Drawer ── */
@media only screen and (max-width: 1023px) {
    #kt_activitiesDirectClient { width: min(95vw, 680px) !important; }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px) {
    #kt_activitiesDirectClient { width: 1025px !important; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px) {
    #kt_activitiesDirectClient { width: 1200px !important; }
}
#kt_activitiesDirectClient_close svg { color: #ef4444; }

/* ── Container Layout ── */
.kt_content_find_agency {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #1e293b;
}

/* ── TIER 1: Top Header Toolbar Card (Inspiration from Client Tracker Header) ── */
.fa-header-card, .cl-header-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 14px 20px !important;
    margin-bottom: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 16px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
}
.fa-header-left, .cl-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 14px !important;
    min-width: 0 !important;
}
.fa-header-text-wrap {
    min-width: 0 !important;
}
.fa-header-icon-box, .cl-header-icon-box {
    width: 44px !important;
    height: 44px !important;
    border-radius: 10px !important;
    background: #eff6ff !important;
    border: 1px solid #dbeafe !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #2563eb !important;
    font-size: 20px !important;
    flex-shrink: 0 !important;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.06) !important;
}
.fa-header-icon-box svg, .cl-header-icon-box svg {
    color: #2563eb !important;
    stroke: #2563eb !important;
}
.fa-header-title-row, .cl-header-title-row {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}
.fa-header-title, .cl-header-title {
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    letter-spacing: -0.015em !important;
    margin: 0 !important;
    line-height: 1.2 !important;
}
.fa-trust-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: #ecfdf5 !important;
    border: 1px solid #a7f3d0 !important;
    color: #065f46 !important;
    border-radius: 20px !important;
    padding: 3px 10px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    line-height: 1.4 !important;
    letter-spacing: 0.01em !important;
}
.fa-trust-pill svg {
    color: #059669 !important;
    stroke: #059669 !important;
}
.fa-trust-pill i {
    font-size: 11px !important;
    color: #059669 !important;
}
.fa-header-subtitle, .cl-header-subtitle {
    font-size: 13px !important;
    font-weight: 400 !important;
    color: #64748b !important;
    margin-top: 3px !important;
    line-height: 1.35 !important;
}

/* ── TIER 2: 6 Stat Cards Row (Strictly 1 Single Horizontal Row) ── */
.cl-stat-grid {
    display: grid !important;
    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    gap: 8px !important;
    margin-bottom: 16px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
.cl-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 11px;
    padding: 11px 12px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-decoration: none;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
    min-width: 0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    min-height: 66px;
}
.cl-card:hover {
    transform: translateY(-1px);
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    text-decoration: none;
}
.cl-card-top {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 6px;
    min-width: 0;
}
.cl-card-icon {
    width: 32px;
    height: 32px;
    min-width: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 13.5px;
    transition: all 0.15s ease;
}
.cl-card-icon.blue   { background: #eff6ff; color: #2563eb; }
.cl-card-icon.indigo { background: #eef2ff; color: #4f46e5; }
.cl-card-icon.green  { background: #ecfdf5; color: #16a34a; }
.cl-card-icon.purple { background: #faf5ff; color: #9333ea; }
.cl-card-icon.orange { background: #fff7ed; color: #ea580c; }
.cl-card-icon.teal   { background: #f0fdfa; color: #0d9488; }

.cl-card-meta {
    min-width: 0;
    flex: 1;
    overflow: hidden;
}
.cl-card-title, .cl-card-lbl {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin: 0 !important;
}
.cl-card:hover .cl-card-title, .cl-card:hover .cl-card-lbl {
    color: #009ef7 !important;
}
.cl-card-sub {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10.5px;
    font-weight: 500;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
    color: #64748b;
}

/* ── KPI Selected States ── */
.cl-card.active[data-tab="all"] {
    background: #eff6ff !important;
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 1.5px #2563eb, 0 4px 14px rgba(37, 99, 235, 0.12) !important;
}
.cl-card.active[data-tab="all"] .cl-card-icon { background: #ffffff !important; color: #2563eb !important; }
.cl-card.active[data-tab="all"] .cl-card-title,
.cl-card.active[data-tab="all"] .cl-card-lbl { color: #1e40af !important; font-weight: 700 !important; }

.cl-card.active[data-tab="bench"] {
    background: #eef2ff !important;
    border-color: #818cf8 !important;
    box-shadow: 0 0 0 1.5px #4f46e5, 0 4px 14px rgba(79, 70, 229, 0.12) !important;
}
.cl-card.active[data-tab="bench"] .cl-card-icon { background: #ffffff !important; color: #4f46e5 !important; }
.cl-card.active[data-tab="bench"] .cl-card-title,
.cl-card.active[data-tab="bench"] .cl-card-lbl { color: #4338ca !important; font-weight: 700 !important; }

.cl-card.active[data-tab="contract"] {
    background: #ecfdf5 !important;
    border-color: #34d399 !important;
    box-shadow: 0 0 0 1.5px #059669, 0 4px 14px rgba(5, 150, 105, 0.12) !important;
}
.cl-card.active[data-tab="contract"] .cl-card-icon { background: #ffffff !important; color: #059669 !important; }
.cl-card.active[data-tab="contract"] .cl-card-title,
.cl-card.active[data-tab="contract"] .cl-card-lbl { color: #047857 !important; font-weight: 700 !important; }

.cl-card.active[data-tab="fulltime"] {
    background: #faf5ff !important;
    border-color: #c084fc !important;
    box-shadow: 0 0 0 1.5px #9333ea, 0 4px 14px rgba(147, 51, 234, 0.12) !important;
}
.cl-card.active[data-tab="fulltime"] .cl-card-icon { background: #ffffff !important; color: #9333ea !important; }
.cl-card.active[data-tab="fulltime"] .cl-card-title,
.cl-card.active[data-tab="fulltime"] .cl-card-lbl { color: #7e22ce !important; font-weight: 700 !important; }

.cl-card.active[data-tab="domestic"],
.cl-card.active[data-tab="india"] {
    background: #fffbeb !important;
    border-color: #facc15 !important;
    box-shadow: 0 0 0 1.5px #d97706, 0 4px 14px rgba(217, 119, 6, 0.12) !important;
}
.cl-card.active[data-tab="domestic"] .cl-card-icon,
.cl-card.active[data-tab="india"] .cl-card-icon { background: #ffffff !important; color: #d97706 !important; }
.cl-card.active[data-tab="domestic"] .cl-card-title,
.cl-card.active[data-tab="domestic"] .cl-card-lbl,
.cl-card.active[data-tab="india"] .cl-card-title,
.cl-card.active[data-tab="india"] .cl-card-lbl { color: #b45309 !important; font-weight: 700 !important; }

.cl-card.active[data-tab="global"] {
    background: #f0fdfa !important;
    border-color: #2dd4bf !important;
    box-shadow: 0 0 0 1.5px #0d9488, 0 4px 14px rgba(13, 148, 136, 0.12) !important;
}
.cl-card.active[data-tab="global"] .cl-card-icon { background: #ffffff !important; color: #0d9488 !important; }
.cl-card.active[data-tab="global"] .cl-card-title,
.cl-card.active[data-tab="global"] .cl-card-lbl { color: #0f766e !important; font-weight: 700 !important; }

/* ── TIER 2.5: Filter Bar ── */
.cl-filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.cl-search-box {
    position: relative;
    flex: 1;
    min-width: 240px;
}
.cl-search-box svg.cl-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 15px;
    height: 15px;
    color: #94a3b8;
    pointer-events: none;
}
.cl-search-inp {
    width: 100%;
    height: 42px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0 100px 0 38px;
    font-size: 12.5px;
    font-family: inherit;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.cl-search-inp:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 2.5px rgba(0, 158, 247, 0.15);
}
.cl-search-inp::placeholder { color: #94a3b8; }
.cl-search-right-affix {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    gap: 6px;
}
.cl-search-count-pill {
    font-size: 10.5px;
    font-weight: 600;
    background: #eff6ff;
    color: #009ef7;
    border: 1px solid #dbeafe;
    padding: 2px 7px;
    border-radius: 6px;
    white-space: nowrap;
    line-height: 1.2;
}
.cl-search-clear-btn {
    border: none;
    background: #e2e8f0;
    color: #64748b;
    width: 19px;
    height: 19px;
    border-radius: 50%;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    transition: all 0.15s ease;
}
.cl-search-clear-btn:hover { background: #cbd5e1; color: #0f172a; }
.cl-search-kbd-hint {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 1px 5px;
    font-size: 10px;
    color: #64748b;
    font-family: inherit;
}

/* ── Combobox Dropdowns ── */
.cl-loc-dropdown-wrapper, .cl-ind-dropdown-wrapper, .cl-sort-dropdown-wrapper {
    position: relative;
    display: inline-block;
}
.cl-loc-combobox-btn, .cl-ind-combobox-btn, .cl-sort-combobox-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 42px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0 12px;
    min-width: 145px;
    cursor: pointer;
    user-select: none;
    transition: all 0.15s ease;
    font-family: inherit;
}
.cl-loc-combobox-btn:hover, .cl-ind-combobox-btn:hover, .cl-sort-combobox-btn:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.cl-loc-combobox-btn.has-filter, .cl-ind-combobox-btn.has-filter {
    border-color: #009ef7;
    background: #f0f9ff;
}
.cl-pill-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
}
.cl-pill-texts {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
    text-align: left;
}
.cl-pill-lbl {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #94a3b8;
}
.cl-pill-val {
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
    max-width: 110px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cl-pill-chevron {
    color: #94a3b8;
    margin-left: auto;
    font-size: 10px;
    transition: transform 0.15s ease;
}
.cl-pill-clear {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #64748b;
    font-size: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    margin-left: 2px;
}
.cl-pill-clear:hover { background: #cbd5e1; color: #0f172a; }

/* ── Popover Panels ── */
.cl-loc-dropdown-panel, .cl-ind-dropdown-panel, .cl-sort-dropdown-panel {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    width: 290px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06);
    z-index: 1055;
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: clPopoverIn 0.15s ease-out;
}
.cl-sort-dropdown-panel { right: 0; left: auto; width: 230px; padding: 6px 0; }
@keyframes clPopoverIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}
.cl-loc-dropdown-panel.show, .cl-ind-dropdown-panel.show, .cl-sort-dropdown-panel.show { display: flex; }

.cl-panel-search-box {
    padding: 8px 12px;
    border-bottom: 1px solid #f1f5f9;
    background: #fafafa;
    position: relative;
}
.cl-panel-search-inp {
    width: 100%;
    height: 32px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0 24px 0 28px;
    font-size: 11.5px;
    font-family: inherit;
    color: #0f172a;
    background: #ffffff;
    outline: none;
}
.cl-panel-search-inp:focus { border-color: #009ef7; }
.cl-panel-search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
}
.cl-panel-search-clear {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
    cursor: pointer;
}
.cl-panel-body {
    max-height: 250px;
    overflow-y: auto;
    padding: 6px 0;
}
.cl-panel-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 14px;
    font-size: 11.5px;
    font-weight: 500;
    color: #1e293b;
    cursor: pointer;
    transition: background 0.12s ease;
}
.cl-panel-item:hover { background: #f1f5f9; }
.cl-panel-item.selected { background: #eff6ff; color: #009ef7; font-weight: 600; }
.cl-panel-section-hdr {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #94a3b8;
    padding: 6px 14px 4px 14px;
}

/* Quick Chips */
.cl-quick-chips-row {
    padding: 8px 12px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    background: #f8fafc;
}
.cl-quick-chip {
    font-size: 10.5px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 2px 7px;
    cursor: pointer;
    color: #334155;
    transition: all 0.12s ease;
}
.cl-quick-chip:hover { border-color: #009ef7; color: #009ef7; background: #f0f9ff; }

/* Reset Button */
.cl-reset-filters-btn {
    height: 42px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0 14px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cl-reset-filters-btn:hover { background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; }

/* ── Live Marketplace Velocity Ticker (Point 2) ── */
.fa-velocity-ticker {
    background: linear-gradient(90deg, #ecfdf5 0%, #f0f9ff 50%, #f5f3ff 100%);
    border: 1px solid #a7f3d0;
    border-radius: 10px;
    padding: 7px 14px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11.5px;
    box-shadow: 0 1px 2px rgba(16, 185, 129, 0.05);
    gap: 12px;
}
.ticker-left {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow: hidden;
    color: #334155;
    flex: 1;
    min-width: 0;
}
.ticker-pill {
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 2.5px 8px;
    border-radius: 20px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    line-height: 1;
}
.ticker-pill.live {
    background: #10b981 !important;
    color: #ffffff !important;
}
.ticker-pill.static {
    background: #64748b !important;
    color: #ffffff !important;
}
.ticker-content {
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
    flex: 1;
}
.ticker-item {
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
}
.ticker-item strong { color: #0f172a; font-weight: 600; }
.ticker-item em { font-style: normal; font-weight: 600; color: #0284c7; }
.ticker-sep { color: #cbd5e1; flex-shrink: 0; }
.ticker-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #ffffff;
    display: inline-block;
    flex-shrink: 0;
}
.ticker-dot.live-pulse {
    background: #ffffff;
    box-shadow: 0 0 0 rgba(255, 255, 255, 0.4);
    animation: liveDotPulse 2s infinite;
}
@keyframes liveDotPulse {
    0% { transform: scale(0.95); opacity: 0.7; }
    50% { transform: scale(1.15); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.7; }
}
.ticker-right {
    font-size: 10.5px;
    color: #64748b;
    flex-shrink: 0;
    font-family: inherit;
    white-space: nowrap;
    margin-left: auto;
}

/* ── POINT 1: "Match With My Open Jobs" Instant AI Job Matcher ── */
.fa-matchmaker-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 11px;
    padding: 10px 16px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    position: relative;
    flex-wrap: wrap;
}
.matchmaker-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 260px;
}
.matchmaker-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.matchmaker-tag {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    display: flex;
    align-items: center;
}
.matchmaker-desc {
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
    margin-top: 1px;
}
.matchmaker-right {
    flex-shrink: 0;
}
.fa-job-combobox-wrapper {
    position: relative;
}
.fa-job-combobox-btn {
    height: 38px;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 0 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
    cursor: pointer;
    transition: all 0.15s ease;
    min-width: 290px;
    justify-content: space-between;
    user-select: none;
}
.fa-job-combobox-btn:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}
.job-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    flex-shrink: 0;
}
.job-btn-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 230px;
}
.job-chevron {
    color: #94a3b8;
    font-size: 10px;
    transition: transform 0.15s ease;
}
.fa-job-popover-panel {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    width: 360px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06);
    z-index: 1060;
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: clPopoverIn 0.15s ease-out;
}
.fa-job-popover-panel.show { display: flex; }
.job-popover-search {
    padding: 8px 12px;
    border-bottom: 1px solid #f1f5f9;
    background: #fafafa;
    position: relative;
}
.job-popover-search .search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
}
.job-search-inp {
    width: 100%;
    height: 30px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0 10px 0 28px;
    font-size: 11.5px;
    color: #0f172a;
    background: #ffffff;
    outline: none;
}
.job-search-inp:focus { border-color: #009ef7; }
.job-popover-list {
    max-height: 240px;
    overflow-y: auto;
    padding: 4px 0;
}
.job-popover-item {
    padding: 8px 14px;
    cursor: pointer;
    transition: background 0.12s ease;
    border-bottom: 1px solid #f8fafc;
}
.job-popover-item:hover { background: #f8fafc; }
.job-popover-item.selected { background: #eff6ff; }
.job-section-divider {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #94a3b8;
    padding: 6px 14px 4px 14px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
}
.job-popover-footer {
    padding: 6px 14px;
    border-top: 1px solid #f1f5f9;
    background: #fafafa;
    display: flex;
    justify-content: flex-end;
}
.cl-mini-tag {
    font-size: 9px;
    font-weight: 600;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 1px 4px;
}

/* Zero Job High-Conversion Empty Banner */
.fa-zero-job-banner {
    background: linear-gradient(90deg, #fffbeb 0%, #ffffff 50%, #f0f9ff 100%);
    border: 1px solid #fde68a;
    border-radius: 11px;
    padding: 10px 16px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    box-shadow: 0 1px 2px rgba(245, 158, 11, 0.05);
    flex-wrap: wrap;
}
.zero-job-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #fef3c7;
    border: 1px solid #fde68a;
    color: #d97706;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.zero-job-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
}
.zero-job-desc {
    font-size: 11.5px;
    color: #475569;
    margin-top: 2px;
}

/* ── TIER 3: Primary Card Stream & Sub-Cards Standard (Rule 20) ── */
.cl-card-stream-wrap {
    width: 100%;
    box-sizing: border-box;
}
.cl-card-stream {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}
.cl-primary-card.tbl-r {
    display: flex !important;
    flex-direction: column !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 8px 10px 0 10px !important;
    margin-bottom: 8px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-sizing: border-box !important;
    border-left: 3.5px solid #009ef7 !important;
}
.cl-primary-card.tbl-r:hover {
    border-color: #cbd5e1 !important;
    border-left-color: #009ef7 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
    transform: translateY(-1px) !important;
}

/* 5 Discrete Sub-Cards Grid (Rule 20 Enterprise Standard - Compact & Identical Spacing) */
.cl-subcards-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 1.85fr) minmax(0, 0.72fr) minmax(0, 1.25fr) minmax(0, 1.15fr) minmax(0, 1.03fr) !important;
    gap: 7px !important;
    width: 100% !important;
    align-items: stretch !important;
}

/* Sub-Card Base Container (Decent Enterprise Height Reduction) */
.cl-subcard {
    background: #f8fafc !important;
    border: 1px solid #eef2f6 !important;
    border-radius: 8px !important;
    padding: 7px 9px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    min-width: 0 !important;
    box-sizing: border-box !important;
    position: relative !important;
    height: 100% !important;
    min-height: 80px !important;
}

/* Mandatory Dedicated Header Standard (Rule 20) */
.cl-subcard-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 0 0 4px 0 !important;
    margin: 0 0 5px 0 !important;
    background: transparent !important;
    border-bottom: 1px solid #f1f5f9 !important;
    border-radius: 0 !important;
    min-height: 18px !important;
    height: 18px !important;
}
.cl-subcard-title {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.02em !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1 !important;
}
.cl-subcard-badges {
    display: flex !important;
    align-items: center !important;
    gap: 3.5px !important;
}
.cl-scope-pill {
    font-size: 8px !important;
    font-weight: 700 !important;
    padding: 1px 4px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    line-height: 1.2 !important;
}
.cl-scope-pill.pill-marketplace { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.cl-scope-pill.pill-connected   { background: #a7f3d0 !important; color: #064e3b !important; border: 1px solid #059669 !important; font-weight: 700 !important; }

/* Smart Recommendation Hierarchy Pills (Rule 20) */
.cl-match-type-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    font-size: 8px !important;
    font-weight: 700 !important;
    padding: 1px 5px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    line-height: 1.2 !important;
}
.cl-match-type-pill.pill-direct-match {
    background: #ecfdf5 !important;
    color: #065f46 !important;
    border: 1px solid #a7f3d0 !important;
}
.cl-match-type-pill.pill-recommended {
    background: #eff6ff !important;
    color: #1e40af !important;
    border: 1px solid #bfdbfe !important;
}

.cl-fallback-alert-banner {
    background: #f0fdf4 !important;
    border: 1px solid #bbf7d0 !important;
    border-radius: 9px !important;
    padding: 9px 14px !important;
    margin-bottom: 12px !important;
    font-size: 11.5px !important;
    color: #166534 !important;
    display: flex !important;
    align-items: center !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
}

.cl-status-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    font-size: 8px !important;
    font-weight: 700 !important;
    padding: 1px 4px !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    line-height: 1.2 !important;
}
.cl-status-pill.pill-active   { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
.cl-status-pill.pill-inactive { background: #fdf2f8 !important; color: #db2777 !important; border: 1px solid #fbcfe8 !important; }
.cl-status-pill-dot {
    width: 4.5px !important;
    height: 4.5px !important;
    border-radius: 50% !important;
    background: currentColor !important;
}

/* Sub-Card 1: Agency Identity (Matches Attached Screenshot Design) */
.cl-identity-body {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
}
.cl-logo-box {
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
}
.cl-logo-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
.cl-initials-fallback {
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
    font-weight: 700 !important;
}
.cl-details-box {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    line-height: 1.25 !important;
    flex: 1 !important;
}
.cl-name {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    text-decoration: none !important;
}
.cl-name:hover {
    color: #009ef7 !important;
    text-decoration: underline !important;
}
.cl-ind {
    font-size: 10.5px !important;
    color: #64748b !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.15 !important;
}
.cl-size {
    font-size: 10px !important;
    color: #94a3b8 !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.15 !important;
}

/* Sub-Card 2: Location (Matches Attached Screenshot Design) */
.cl-loc-body {
    display: flex !important;
    align-items: flex-start !important;
    gap: 6px !important;
    min-width: 0 !important;
}
.cl-loc-pin {
    color: #2563eb !important;
    flex-shrink: 0 !important;
    margin-top: 2px !important;
}
.cl-loc-lines {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    line-height: 1.25 !important;
    flex: 1 !important;
}
.cl-loc-city {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cl-loc-state {
    font-size: 10.5px !important;
    color: #64748b !important;
    line-height: 1.15 !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cl-loc-country {
    font-size: 10px !important;
    color: #94a3b8 !important;
    line-height: 1.15 !important;
    margin-top: 1px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cl-serving-badge {
    font-size: 8.5px !important;
    font-weight: 600 !important;
    background: #eff6ff !important;
    color: #1e40af !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 4px !important;
    padding: 1px 4px !important;
    cursor: pointer !important;
    white-space: nowrap !important;
}

/* Sub-Card 3: Adaptive Commercials (Dual / Single) */
.cl-rates-content-wrap {
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    height: 100% !important;
}
.cl-dual-rates-grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 4px !important;
}
.cl-single-rate-box {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 5px !important;
    padding: 3px 6px !important;
    text-align: center !important;
}
.cl-single-rate-box.fulltime {
    border-color: #a7f3d0 !important;
    background: #f0fdf4 !important;
}
.cl-single-rate-box.contract {
    border-color: #bae6fd !important;
    background: #f0f9ff !important;
}
.cl-rate-box {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 5px !important;
    padding: 2px 3px !important;
    text-align: center !important;
}
.rate-box-tag {
    font-size: 8px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.03em !important;
    border-radius: 2px !important;
    padding: 1px 2px !important;
    line-height: 1 !important;
    margin-bottom: 1px !important;
}
.rate-box-tag.ft { background: #ecfdf5 !important; color: #047857 !important; }
.rate-box-tag.ct { background: #f0f9ff !important; color: #0284c7 !important; }
.rate-box-val {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.1 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.rate-box-desc {
    font-size: 7.5px !important;
    color: #64748b !important;
    margin-top: 1px !important;
    line-height: 1 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Sub-Card 4: Talent Supply (High-Contrast Vertical Rhythm) */
.cl-talent-body {
    display: flex !important;
    flex-direction: column !important;
    gap: 2.5px !important;
    min-width: 0 !important;
}
.cl-talent-line {
    display: flex !important;
    align-items: center !important;
    font-size: 10.5px !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    min-width: 0 !important;
}
.cl-talent-icon {
    width: 14px !important;
    text-align: center !important;
    margin-right: 5px !important;
    font-size: 10px !important;
    flex-shrink: 0 !important;
}
.cl-talent-num {
    font-weight: 700 !important;
    font-size: 11.5px !important;
    color: #0f172a !important;
    margin-right: 4px !important;
    line-height: 1 !important;
}
.cl-talent-lbl {
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: #334155 !important; /* Crisp dark slate - WCAG AA compliant */
    line-height: 1 !important;
}
.cl-talent-line.bench-active {
    cursor: pointer !important;
}
.cl-talent-line.bench-active .cl-talent-num,
.cl-talent-line.bench-active .cl-talent-lbl {
    color: #d97706 !important;
    font-weight: 700 !important;
}
.cl-talent-line.bench-active:hover .cl-talent-lbl {
    color: #b45309 !important;
}
.cl-talent-line.bench-zero .cl-talent-num {
    color: #94a3b8 !important;
}
.cl-talent-line.bench-zero .cl-talent-lbl {
    color: #64748b !important;
}

/* Sub-Card 5: Separate Partnership & Action Hub */
.cl-action-hub-body {
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    height: 100% !important;
    padding: 2px 0 !important;
}
.cl-card-action-wrap {
    margin-top: auto !important;
    margin-bottom: auto !important;
}
.cl-action-btn {
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 3px 8px !important;
    height: 26px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.cl-action-btn.cl-btn-connected,
.btn.btn-light-success.cl-action-btn {
    background: #a7f3d0 !important;
    color: #064e3b !important;
    border: 1px solid #059669 !important;
    font-weight: 700 !important;
    opacity: 1 !important;
}
.cl-action-btn.cl-btn-connected i,
.btn.btn-light-success.cl-action-btn i {
    color: #064e3b !important;
}
.cl-action-footnote {
    font-size: 8px !important;
    color: #94a3b8 !important;
    text-align: center !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-top: 2px !important;
}

/* ── BOTTOM FOOTER ACTIVITY STRIP (Rule 20 Enterprise Standard) ── */
.cl-card-footer-strip {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 5px 12px !important;
    margin: 6px -10px 0 -10px !important;
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    border-bottom-left-radius: 9px !important;
    border-bottom-right-radius: 9px !important;
    font-size: 10.5px !important;
    color: #475569 !important;
    line-height: 1.2 !important;
}
.cl-footer-strip-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}
.cl-footer-match-wrap {
    display: inline-flex !important;
    align-items: center !important;
}
.cl-card-match-badge {
    display: inline-flex !important;
    align-items: center !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    padding: 2.5px 8px !important;
    border-radius: 6px !important;
    line-height: 1.25 !important;
    transition: all 0.15s ease !important;
}
.cl-card-match-badge.badge-light-success {
    background: #dcfce7 !important;
    color: #14532d !important;
    border: 1px solid #86efac !important;
}
.cl-card-match-badge.badge-light-primary {
    background: #eff6ff !important;
    color: #1e40af !important;
    border: 1px solid #bfdbfe !important;
}
.cl-footer-item {
    display: inline-flex !important;
    align-items: center !important;
    font-size: 10.5px !important;
    color: #475569 !important;
    white-space: nowrap !important;
}
.cl-footer-item strong {
    color: #0f172a !important;
    font-weight: 600 !important;
    margin-left: 2px !important;
}
.cl-footer-divider {
    color: #cbd5e1 !important;
    font-weight: 700 !important;
    font-size: 10px !important;
}
.cl-footer-strip-right {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
}
.cl-footer-strip-right .cl-terms-link {
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #334155 !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    padding: 2.5px 7px !important;
    border-radius: 5px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
    cursor: pointer !important;
}
.cl-footer-strip-right .cl-terms-link:hover {
    color: #009ef7 !important;
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    text-decoration: none !important;
}
.cl-footer-strip-right .cl-profile-link {
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #009ef7 !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 2.5px 7px !important;
    border-radius: 5px !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}
.cl-footer-strip-right .cl-profile-link:hover {
    color: #007bb5 !important;
    background: #dbeafe !important;
    border-color: #93c5fd !important;
    text-decoration: none !important;
}

/* ── SLIDING AGENCY TERMS DRAWER (Rule 20 Enterprise Standard) ── */
.cl-terms-drawer {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 500px;
    max-width: 94vw;
    background: #f8fafc;
    border-left: 1px solid #cbd5e1;
    box-shadow: -10px 0 35px rgba(15, 23, 42, 0.16);
    z-index: 1075;
    transform: translateX(100%);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
}
.cl-terms-drawer.show {
    transform: translateX(0);
}
.terms-drawer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
    z-index: 1070;
    display: none;
}
.terms-drawer-overlay.show {
    display: block;
}
.terms-drawer-header {
    padding: 16px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-shrink: 0;
}
.terms-header-avatar {
    width: 44px;
    height: 44px;
    border-radius: 9px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
.terms-header-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.terms-header-avatar-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    background: #eff6ff;
    color: #2563eb;
}
.terms-drawer-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.25;
}
.terms-drawer-sub {
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.35;
    margin-top: 2px;
}
.terms-drawer-close-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.terms-drawer-close-btn:hover {
    background: #fee2e2;
    border-color: #fecaca;
    color: #ef4444;
}
.terms-drawer-body {
    flex: 1;
    overflow-y: auto;
    padding: 18px 20px 24px 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.terms-drawer-footer {
    padding: 14px 20px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex-shrink: 0;
}
.terms-policy-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 13px 15px !important;
    margin-bottom: 0 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
}
.terms-policy-title {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin-bottom: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}
.terms-spec-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    font-size: 11.5px !important;
    padding: 6px 0 !important;
    border-bottom: 1px solid #f1f5f9 !important;
}
.terms-spec-row:last-child {
    border-bottom: none !important;
}
.terms-spec-lbl {
    color: #64748b !important;
    font-weight: 500 !important;
}
.terms-spec-val {
    font-weight: 600 !important;
    color: #0f172a !important;
    text-align: right !important;
}
.terms-gov-box {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 4px;
}
.terms-gov-item {
    background: #f8fafc;
    border: 1px solid #eef2f6;
    border-radius: 8px;
    padding: 9px 12px;
}
.terms-gov-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
}
.terms-gov-desc {
    font-size: 11px;
    color: #475569;
    line-height: 1.5;
}

/* ── SLIDING BENCH CANDIDATE PREVIEW DRAWER (Point 2) ── */
.cl-bench-drawer {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 440px;
    max-width: 90vw;
    background: #ffffff;
    border-left: 1px solid #cbd5e1;
    box-shadow: -10px 0 30px rgba(15, 23, 42, 0.15);
    z-index: 1070;
    transform: translateX(100%);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
}
.cl-bench-drawer.show {
    transform: translateX(0);
}
.bench-drawer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
    z-index: 1065;
    display: none;
}
.bench-drawer-overlay.show { display: block; }
.bench-drawer-header {
    padding: 14px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.bench-drawer-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.bench-drawer-sub {
    font-size: 11.5px;
    color: #64748b;
    margin: 2px 0 0 0;
}
.bench-drawer-close-btn {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 13px;
    font-weight: 700;
}
.bench-drawer-close-btn:hover { background: #e2e8f0; color: #0f172a; }
.bench-drawer-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.bench-cand-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: border-color 0.15s ease;
}
.bench-cand-card:hover {
    border-color: #009ef7;
}
.bench-drawer-footer {
    padding: 14px 18px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}
.bench-drawer-footnote {
    font-size: 10.5px;
    color: #64748b;
    text-align: center;
    margin-top: 6px;
}

/* ── Empty State ── */
.cl-empty-state-card {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    margin: 10px 0;
}
.cl-empty-icon-wrap { margin-bottom: 12px; }
.cl-empty-title { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
.cl-empty-sub { font-size: 12.5px; color: #64748b; max-width: 480px; margin: 0 auto 16px auto; }

/* ── 15.6" Screen Responsive Breakpoints (Rule 14 & Rule 20) ── */
@media (max-width: 1400px) {
    .cl-subcards-grid {
        grid-template-columns: minmax(0, 1.75fr) minmax(0, 0.72fr) minmax(0, 1.25fr) minmax(0, 1.18fr) minmax(0, 1.1fr) !important;
        gap: 6px !important;
    }
}
@media (max-width: 1200px) {
    .cl-subcards-grid {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 8px !important;
    }
}
@media (max-width: 992px) {
    .cl-stat-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
    .cl-subcards-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    .fa-matchmaker-bar {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .fa-job-combobox-btn {
        width: 100% !important;
    }
}
@media (max-width: 640px) {
    .cl-stat-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    .cl-subcards-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid pt-2 kt_content_find_agency" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid px-3">

            <!-- ── TIER 1: Header Card (Consistent JeroATS Spacing & Hierarchy - Point 1) ── -->
            <div class="fa-header-card cl-header-card">
                <div class="fa-header-left cl-header-left">
                    <div class="fa-header-icon-box cl-header-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="fa-header-text-wrap">
                        <div class="fa-header-title-row cl-header-title-row">
                            <h1 class="fa-header-title cl-header-title">Find Trusted Hiring Agencies</h1>
                            <span class="fa-trust-pill" title="Verified ATS Agencies">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                                <span>Verified agencies</span>
                            </span>
                        </div>
                        <div class="fa-header-subtitle cl-header-subtitle">
                            Discover & collaborate with verified recruitment partners tailored to your exact hiring mandates.
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── TIER 1.5: Authentic Live Marketplace Velocity Ticker (Point 2) ── -->
            <div class="fa-velocity-ticker" id="faLiveTicker">
                <div class="ticker-left">
                    <span class="ticker-pill {{ !empty($liveTickerIsLive) ? 'live' : 'static' }}">
                        <span class="ticker-dot {{ !empty($liveTickerIsLive) ? 'live-pulse' : '' }}"></span>
                        <span>{{ !empty($liveTickerIsLive) ? 'LIVE TICKER' : 'MARKETPLACE FEED' }}</span>
                    </span>
                    <div class="ticker-content" id="faTickerContent">
                        @if(!empty($liveTickerItems) && count($liveTickerItems) > 0)
                            @foreach($liveTickerItems as $idx => $tItem)
                                @if($idx > 0)<span class="ticker-sep">•</span>@endif
                                <span class="ticker-item">{!! $tItem['html'] !!}</span>
                            @endforeach
                        @else
                            <span class="ticker-item">
                                <i class="fas fa-satellite-dish text-success me-1"></i>
                                <strong>Marketplace Feed Active</strong> • 
                                <strong>{{ $activeAgenciesCount ?? 0 }} Verified Agencies</strong> ready to review mandates
                            </span>
                        @endif
                    </div>
                </div>
                <div class="ticker-right">
                    <span class="font-mono text-muted" id="faTickerTimestamp">{{ $liveTickerUpdatedAt ?? 'Updated Just Now' }}</span>
                </div>
            </div>

            <!-- ── TIER 2: 6 Category Filter Tabs Row (Minimal Design - Counters Removed - Points 3, 4, 5) ── -->
            <div class="cl-stat-grid" id="agencyKpiGrid">
                
                <!-- Total Agencies -->
                <div class="cl-card active" data-tab="all" onclick="selectAgencyTab('all', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon blue">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-title">Total Agencies</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-check-circle text-primary font-size-11"></i>
                        <span>All verified partners</span>
                    </div>
                </div>

                <!-- Bench Agencies -->
                <div class="cl-card" data-tab="bench" onclick="selectAgencyTab('bench', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon indigo">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-title">Bench Agencies</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-user-check text-indigo font-size-11"></i>
                        <span>Immediate bench talent</span>
                    </div>
                </div>

                <!-- Contract Staffing -->
                <div class="cl-card" data-tab="contract" onclick="selectAgencyTab('contract', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon green">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-title">Contract Staffing</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-sync-alt text-success font-size-11"></i>
                        <span>C2H, C2C, Contract Hiring</span>
                    </div>
                </div>

                <!-- Full-Time Hiring -->
                <div class="cl-card" data-tab="fulltime" onclick="selectAgencyTab('fulltime', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon purple">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-title">Full-Time Hiring</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-briefcase text-purple font-size-11"></i>
                        <span>Permanent recruitment</span>
                    </div>
                </div>

                <!-- Domestic Agencies (Renamed from Indian Agencies & Dynamic Country Subtext) -->
                <div class="cl-card" data-tab="domestic" onclick="selectAgencyTab('domestic', this)" role="button" tabindex="0" title="{{ $domesticTooltip ?? 'Domestic hiring partners' }}">
                    <div class="cl-card-top">
                        <div class="cl-card-icon orange">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-title">Domestic Agencies</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-flag text-warning font-size-11"></i>
                        <span id="kpi_domestic_agencies_sub">{{ $domesticSubtext }}</span>
                    </div>
                </div>

                <!-- Global Agencies -->
                <div class="cl-card" data-tab="global" onclick="selectAgencyTab('global', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon teal">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-title">Global Agencies</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-plane-departure text-info font-size-11"></i>
                        <span>International reach</span>
                    </div>
                </div>

            </div>

            <!-- ── TIER 2.5: Filter Bar (Search, Location, Industry, Sort) ── -->
            <div class="cl-filter-bar">
                
                <!-- Search Box -->
                <div class="cl-search-box" id="clSearchBoxWrapper">
                    <svg class="cl-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input 
                        type="text" 
                        class="cl-search-inp" 
                        name="agencySearchInp" 
                        id="agencySearchInp"
                        value="" 
                        placeholder="Search agencies by name, skills, industry, location..." 
                        autocomplete="off" 
                        spellcheck="false" 
                    />
                    <div class="cl-search-right-affix">
                        <span class="cl-search-count-pill" id="agencyMatchCount" style="display:none;">0 matches</span>
                        <button type="button" class="cl-search-clear-btn" id="agencySearchClearBtn" title="Clear search (Esc)" style="display:none;" onclick="clearAgencySearch()">✕</button>
                        <kbd class="cl-search-kbd-hint" id="agencySearchKbdHint">Ctrl+K</kbd>
                    </div>
                </div>

                <!-- Location Combobox Popover -->
                <div class="cl-loc-dropdown-wrapper" id="faLocDropdownWrapper">
                    <div class="cl-loc-combobox-btn" id="faLocBtn" onclick="toggleLocationDropdown(event)">
                        <div class="cl-pill-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="cl-pill-texts">
                            <span class="cl-pill-lbl">Location</span>
                            <span class="cl-pill-val" id="faLocDisplay">All Locations</span>
                        </div>
                        <span class="cl-pill-clear" id="faLocClearBtn" onclick="clearLocationFilter(event)" style="display:none;" title="Reset location">✕</span>
                        <div class="cl-pill-chevron"><i class="fas fa-chevron-down"></i></div>
                    </div>

                    <!-- Dropdown Panel -->
                    <div class="cl-loc-dropdown-panel" id="faLocDropdownPanel">
                        <div class="cl-panel-search-box">
                            <i class="fas fa-search cl-panel-search-icon"></i>
                            <input type="text" class="cl-panel-search-inp" id="faLocSearchInp" placeholder="Search countries..." oninput="filterLocationItems(this.value)" />
                            <span class="cl-panel-search-clear" onclick="clearLocInput()">✕</span>
                        </div>
                        <div class="cl-panel-body" id="faLocDropdownBody">
                            <div class="cl-panel-item selected" onclick="selectLocationOption('', 'All Locations')">
                                <span><i class="fas fa-globe me-2 text-muted"></i>All Locations</span>
                                <i class="fas fa-check text-primary font-size-11"></i>
                            </div>
                            <div class="cl-panel-section-hdr">Major Hiring Hubs</div>
                            <div class="cl-panel-item" onclick="selectLocationOption('India', 'India')">
                                <span><i class="fas fa-flag me-2 text-warning"></i>India</span>
                            </div>
                            <div class="cl-panel-item" onclick="selectLocationOption('United States', 'United States')">
                                <span><i class="fas fa-flag me-2 text-primary"></i>United States</span>
                            </div>
                            <div class="cl-panel-item" onclick="selectLocationOption('United Kingdom', 'United Kingdom')">
                                <span><i class="fas fa-flag me-2 text-info"></i>United Kingdom</span>
                            </div>
                            <div class="cl-panel-item" onclick="selectLocationOption('United Arab Emirates', 'UAE')">
                                <span><i class="fas fa-flag me-2 text-success"></i>United Arab Emirates</span>
                            </div>
                            <div class="cl-panel-section-hdr">All Countries</div>
                            @if(!empty($cities))
                                @foreach($cities as $ctry)
                                    @if(!in_array($ctry, ['India', 'United States', 'United Kingdom', 'United Arab Emirates']))
                                        <div class="cl-panel-item country-item" data-name="{{ strtolower($ctry) }}" onclick="selectLocationOption('{{ $ctry }}', '{{ $ctry }}')">
                                            <span>{{ $ctry }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <input type="hidden" id="selected_location" value="" />
                </div>

                <!-- Industry Combobox Popover -->
                <div class="cl-ind-dropdown-wrapper" id="faIndDropdownWrapper">
                    <div class="cl-ind-combobox-btn" id="faIndBtn" onclick="toggleIndustryDropdown(event)">
                        <div class="cl-pill-icon"><i class="fas fa-briefcase"></i></div>
                        <div class="cl-pill-texts">
                            <span class="cl-pill-lbl">Industry</span>
                            <span class="cl-pill-val" id="faIndDisplay">All Industries</span>
                        </div>
                        <span class="cl-pill-clear" id="faIndClearBtn" onclick="clearIndustryFilter(event)" style="display:none;" title="Reset industry">✕</span>
                        <div class="cl-pill-chevron"><i class="fas fa-chevron-down"></i></div>
                    </div>

                    <!-- Dropdown Panel -->
                    <div class="cl-ind-dropdown-panel" id="faIndDropdownPanel">
                        <div class="cl-quick-chips-row">
                            <span class="cl-quick-chip" onclick="selectIndustryOption('IT Services', 'Information Technology')">IT Services</span>
                            <span class="cl-quick-chip" onclick="selectIndustryOption('Staffing', 'Staffing & Recruiting')">Staffing</span>
                            <span class="cl-quick-chip" onclick="selectIndustryOption('Healthcare', 'Healthcare')">Healthcare</span>
                            <span class="cl-quick-chip" onclick="selectIndustryOption('Banking', 'Banking & Finance')">Finance</span>
                        </div>
                        <div class="cl-panel-search-box">
                            <i class="fas fa-search cl-panel-search-icon"></i>
                            <input type="text" class="cl-panel-search-inp" id="faIndSearchInp" placeholder="Search industries..." oninput="filterIndustryItems(this.value)" />
                            <span class="cl-panel-search-clear" onclick="clearIndInput()">✕</span>
                        </div>
                        <div class="cl-panel-body" id="faIndDropdownBody">
                            <div class="cl-panel-item selected" onclick="selectIndustryOption('', 'All Industries')">
                                <span><i class="fas fa-layer-group me-2 text-muted"></i>All Industries</span>
                                <i class="fas fa-check text-primary font-size-11"></i>
                            </div>
                            @if(!empty($td_industries))
                                @foreach($td_industries as $ind)
                                    <div class="cl-panel-item ind-item" data-name="{{ strtolower($ind->name) }}" onclick="selectIndustryOption('{{ $ind->id }}', '{{ $ind->name }}')">
                                        <span>{{ $ind->name }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <input type="hidden" id="selected_industry" value="" />
                </div>

                <!-- Sort Combobox Popover -->
                <div class="cl-sort-dropdown-wrapper" id="faSortDropdownWrapper">
                    <div class="cl-sort-combobox-btn" id="faSortBtn" onclick="toggleSortDropdown(event)">
                        <div class="cl-pill-icon"><i class="fas fa-sort-amount-down"></i></div>
                        <div class="cl-pill-texts">
                            <span class="cl-pill-lbl">Sort</span>
                            <span class="cl-pill-val" id="faSortDisplay">Best Match</span>
                        </div>
                        <div class="cl-pill-chevron"><i class="fas fa-chevron-down"></i></div>
                    </div>

                    <!-- Dropdown Panel -->
                    <div class="cl-sort-dropdown-panel" id="faSortDropdownPanel">
                        <div class="cl-panel-section-hdr">Sort Order</div>
                        <div class="cl-panel-item selected" onclick="selectSortOption('best', 'Best Match')">
                            <span><i class="fas fa-magic me-2 text-muted"></i>Best Match</span>
                            <i class="fas fa-check text-primary font-size-11"></i>
                        </div>
                        <div class="cl-panel-item" onclick="selectSortOption('recent', 'Recently Active')">
                            <span><i class="fas fa-clock me-2 text-muted"></i>Recently Active</span>
                        </div>
                        <div class="cl-panel-item" onclick="selectSortOption('rating', 'Highest Rated')">
                            <span><i class="fas fa-star me-2 text-warning"></i>Highest Rated</span>
                        </div>
                        <div class="cl-panel-item" onclick="selectSortOption('name_asc', 'Agency Name (A → Z)')">
                            <span><i class="fas fa-font me-2 text-muted"></i>Name (A → Z)</span>
                        </div>
                        <div class="cl-panel-item" onclick="selectSortOption('name_desc', 'Agency Name (Z → A)')">
                            <span><i class="fas fa-font me-2 text-muted"></i>Name (Z → A)</span>
                        </div>
                    </div>
                    <input type="hidden" id="selected_sort" value="best" />
                </div>

                <!-- Reset Filters Button -->
                <button type="button" class="cl-reset-filters-btn" onclick="resetAllAgencyFilters()">
                    <i class="fas fa-redo font-size-11"></i>
                    <span>Reset</span>
                </button>

            </div>

            <!-- ── TIER 3: Primary Agency Cards Stream (Rule 20) ── -->
            <div class="cl-card-stream-wrap position-relative">
                <div class="loadingMessage text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary me-2" role="status"></div>
                    <span class="font-weight-600 text-muted font-size-13">Loading verified agencies...</span>
                </div>

                <div id="findAgencyList" class="cl-card-stream">
                    <!-- Loaded dynamically via AJAX -->
                </div>

                <!-- Pagination -->
                <div class="mt-4 mb-5">
                    <div id="paginationfindagency"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ── AGENCY DETAILS DRAWER ── -->
<div id="kt_activitiesDirectClient_toggle"></div>
<div id="kt_activitiesDirectClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close">
    <div class="card shadow-none rounded-0 w-100">
        <div class="card-header px-5 py-0" id="kt_activities_header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
            <div class="d-flex align-items-center py-1">
                <h3 class="card-title fw-bolder text-dark font-size-15 mb-0">
                    <i class="fas fa-building text-primary me-2"></i>Agency Profile & Performance
                </h3>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n3" id="kt_activitiesDirectClient_close">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"/>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"/>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
        <div class="card-body position-relative px-0 py-0" id="kt_activitiesDirectClient_body"> &nbsp; </div>
    </div>
</div>

<!-- ── SLIDING BENCH CANDIDATE PREVIEW DRAWER (Point 2) ── -->
<div id="drawer_agency_bench_preview" class="cl-bench-drawer">
    <div class="bench-drawer-header">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-light-warning font-size-10 font-weight-700"><i class="fas fa-bolt text-warning me-1"></i>PRE-VETTED BENCH</span>
                <h3 class="bench-drawer-title" id="benchDrawerAgencyName">Agency Bench</h3>
            </div>
            <p class="bench-drawer-sub">Pre-interviewed candidates ready for immediate deployment</p>
        </div>
        <button type="button" class="bench-drawer-close-btn" onclick="closeAgencyBenchDrawer()">✕</button>
    </div>
    <div class="bench-drawer-body" id="benchDrawerBody">
        <div class="d-flex justify-content-center p-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
    <div class="bench-drawer-footer">
        <button type="button" class="btn btn-primary w-100 font-weight-700" onclick="dispatchDirectMandate()">
            <i class="fas fa-paper-plane me-1"></i>Request Interview for Bench Talent
        </button>
        <div class="bench-drawer-footnote">Candidates protected under JERO Non-Disclosure & Escrow Agreements.</div>
    </div>
</div>
<div id="benchDrawerOverlay" class="bench-drawer-overlay" onclick="closeAgencyBenchDrawer()"></div>

<!-- ── SLIDING AGENCY TERMS & COMMERCIALS DRAWER ── -->
<div id="drawer_agency_terms_view" class="cl-terms-drawer">
    <div class="terms-drawer-header">
        <div class="d-flex align-items-center gap-3 min-w-0">
            <div class="terms-header-avatar" id="termsDrawerAvatarBox">
                <!-- Avatar image or fallback letter -->
            </div>
            <div class="min-w-0">
                <h3 class="terms-drawer-title text-truncate" id="termsDrawerAgencyName">Agency Commercial Terms</h3>
                <p class="terms-drawer-sub mb-0 text-truncate" id="termsDrawerAgencyMeta">Pre-agreed SLA & Hiring Terms of Engagement</p>
            </div>
        </div>
    </div>
    <div class="terms-drawer-body" id="termsDrawerBody">
        <!-- Content will be populated dynamically from card data-agency-terms -->
    </div>
</div>
<div id="termsDrawerOverlay" class="terms-drawer-overlay" onclick="closeAgencyTermsDrawer()"></div>
@endsection

@push('footer-script')
<script>
    // ── Global State & Caches ──
    @php
        $jobsJson = !empty($activeTenantJobs) ? json_encode($activeTenantJobs) : '[]';
    @endphp
    window.activeTenantJobs = {!! $jobsJson !!};

    window.agencyState = {
        tab: 'all',
        search: '',
        location: '',
        industry: '',
        sort: 'best',
        selectedJobId: 'all',
        selectedJobLabel: 'Matches your requirement on ' + (window.activeTenantJobs ? window.activeTenantJobs.length : 0) + ' jobs',
        selectedJobSkills: [],
        page: 1,
        perPage: localStorage.getItem('agencyPerPage') || 10
    };

    window.JeroFindAgencyRAM = window.JeroAgencyRAM = new Map();
    window.JeroFindAgencyDB = window.JeroAgencyDB || {
        getPage: function() { return Promise.resolve(null); },
        setPage: function() { return Promise.resolve(); }
    };
    window.JeroAgencyDetailCache = window.JeroAgencyDetailCache || new Map();
    var activeAgencyXHR = null;
    var activeDetailXHR = null;
    var searchDebounceTimer = null;

    // ── Instant AI Job Matcher (Point 1) ──
    function toggleJobMatchmakerPopover(e) {
        if (e) e.stopPropagation();
        closeAllDropdowns('job');
        $('#faJobPopoverPanel').toggleClass('show');
        $('#faJobComboboxBtn').toggleClass('active');
        if ($('#faJobPopoverPanel').hasClass('show')) {
            $('#faJobSearchInp').focus();
        }
    }
    window.toggleJobMatchmakerPopover = toggleJobMatchmakerPopover;

    function filterJobPopoverItems(q) {
        q = (q || '').toLowerCase().trim();
        $('#faJobPopoverList .job-item-row').each(function() {
            var title = $(this).data('title') || '';
            if (!q || title.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    window.filterJobPopoverItems = filterJobPopoverItems;

    function selectMatchmakerJob(jobId, label, skills) {
        window.agencyState.selectedJobId = jobId;
        window.agencyState.selectedJobLabel = label;
        window.agencyState.selectedJobSkills = skills || [];

        $('#faJobSelectedLabel').text(label);
        $('#faJobPopoverPanel').removeClass('show');
        $('#faJobComboboxBtn').removeClass('active');

        $('#faJobPopoverList .job-popover-item').removeClass('selected');
        if (jobId === 'all') {
            $('#faJobPopoverList .job-popover-item:first-child').addClass('selected');
        }

        // Instant re-evaluation in User RAM (0% MySQL/Server reads)
        applyActiveJobMatching();
    }
    window.selectMatchmakerJob = selectMatchmakerJob;

    function promptCustomSkillsMatch() {
        var skillsStr = prompt("Enter required tech skills (e.g. React, Node.js, AWS, Python):", "React, AWS");
        if (skillsStr) {
            var skills = skillsStr.split(',').map(function(s) { return s.trim(); }).filter(Boolean);
            selectMatchmakerJob('custom', 'Custom Skills: ' + skillsStr, skills);
        }
    }
    window.promptCustomSkillsMatch = promptCustomSkillsMatch;

    function applyActiveJobMatching() {
        var mode = window.agencyState.selectedJobId || 'all';
        var selectedSkills = window.agencyState.selectedJobSkills || [];
        var activeJobs = window.activeTenantJobs || [];

        $('#findAgencyList .cl-primary-card').each(function() {
            var $card = $(this);
            var agencySkillsRaw = $card.attr('data-agency-skills');
            var agencySkills = [];
            try {
                agencySkills = JSON.parse(agencySkillsRaw) || [];
            } catch(e) {
                agencySkills = [];
            }
            agencySkills = agencySkills.map(function(s) { return (s || '').toLowerCase().trim(); });

            var $cardMatchBadge = $card.find('.cl-card-match-badge');
            var recLabel = $card.attr('data-rec-label') || 'Recommended Partner';
            var recReason = $card.attr('data-rec-reason') || '';
            var recTier = $card.attr('data-rec-tier') || 'marketplace';

            if (mode === 'all') {
                // OVERVIEW MODE: Calculate actual match % based on open jobs
                var matchingJobs = [];
                var highestMatchPct = 0;

                activeJobs.forEach(function(job) {
                    var jSkills = (job.skills || []).map(function(s) { return (s || '').toLowerCase().trim(); });
                    if (jSkills.length === 0) return;
                    var overlap = jSkills.filter(function(js) { return agencySkills.indexOf(js) !== -1; });
                    if (overlap.length > 0) {
                        var pct = Math.round((overlap.length / jSkills.length) * 100);
                        pct = Math.min(100, Math.max(1, pct));
                        matchingJobs.push({
                            title: job.title,
                            pct: pct,
                            overlap: overlap
                        });
                        if (pct > highestMatchPct) {
                            highestMatchPct = pct;
                        }
                    }
                });

                // Sort matching jobs by match % descending
                matchingJobs.sort(function(a, b) { return b.pct - a.pct; });

                if (matchingJobs.length > 0 && highestMatchPct > 0) {
                    var jobCount = matchingJobs.length;
                    var jobTitlesList = matchingJobs.map(function(mj) { return mj.title; }).slice(0, 3).join(', ');
                    if (matchingJobs.length > 3) {
                        jobTitlesList += ' +' + (matchingJobs.length - 3) + ' more';
                    }

                    $cardMatchBadge.removeClass('d-none badge-light-primary').addClass('badge-light-success');
                    $cardMatchBadge.html('<i class="fas fa-bolt text-success me-1"></i><strong>' + highestMatchPct + '% Match</strong> <span class="text-muted font-weight-500 ms-1">• Matches ' + jobCount + (jobCount === 1 ? ' Job' : ' Jobs') + ' (' + jobTitlesList + ')</span>');
                    $cardMatchBadge.attr('title', 'Matches ' + jobCount + ' open jobs: ' + matchingJobs.map(function(mj) { return mj.title + ' (' + mj.pct + '%)'; }).join(', '));
                } else {
                    $cardMatchBadge.addClass('d-none').empty();
                }
            } else if (mode === 'none') {
                $cardMatchBadge.addClass('d-none').empty();
            } else {
                // SPECIFIC REQUISITION MATCH MODE: Dynamic & Logically Correct (Point 2)
                var overlap = [];
                selectedSkills.forEach(function(sk) {
                    var lower = (sk || '').toLowerCase().trim();
                    if (lower && agencySkills.indexOf(lower) !== -1) {
                        overlap.push(lower);
                    }
                });

                if (selectedSkills.length > 0 && overlap.length > 0) {
                    var matchPct = Math.round((overlap.length / selectedSkills.length) * 100);
                    matchPct = Math.min(100, Math.max(1, matchPct));
                    $cardMatchBadge.removeClass('d-none badge-light-primary').addClass('badge-light-success');
                    $cardMatchBadge.html('<i class="fas fa-fire text-danger me-1"></i><strong>' + matchPct + '% Match</strong> <span class="text-muted font-weight-500 ms-1">• Matches requirement (' + overlap.slice(0, 3).join(', ') + ')</span>');
                    $cardMatchBadge.attr('title', 'Matching Skills: ' + overlap.join(', '));
                } else {
                    $cardMatchBadge.addClass('d-none').empty();
                }
            }
        });
    }

    // ── Tab Selection ──
    function selectAgencyTab(tabKey, element) {
        if (tabKey === 'india') tabKey = 'domestic';
        window.agencyState.tab = tabKey;
        window.agencyState.page = 1;

        $('#agencyKpiGrid .cl-card').removeClass('active');
        if (element) {
            $(element).addClass('active');
        } else {
            $('#agencyKpiGrid .cl-card[data-tab="' + tabKey + '"]').addClass('active');
        }

        fetchAgencies(1);
    }
    window.selectAgencyTab = selectAgencyTab;

    // ── Location Combobox Controls ──
    function toggleLocationDropdown(e) {
        if (e) e.stopPropagation();
        closeAllDropdowns('loc');
        $('#faLocDropdownPanel').toggleClass('show');
        $('#faLocBtn').toggleClass('active');
        if ($('#faLocDropdownPanel').hasClass('show')) {
            $('#faLocSearchInp').focus();
        }
    }
    window.toggleLocationDropdown = toggleLocationDropdown;

    function filterLocationItems(q) {
        q = (q || '').toLowerCase().trim();
        $('#faLocDropdownBody .country-item').each(function() {
            var name = $(this).data('name') || '';
            if (!q || name.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    window.filterLocationItems = filterLocationItems;

    function clearLocInput() {
        $('#faLocSearchInp').val('');
        filterLocationItems('');
    }
    window.clearLocInput = clearLocInput;

    function selectLocationOption(val, text) {
        window.agencyState.location = val;
        window.agencyState.page = 1;
        $('#selected_location').val(val);
        $('#faLocDisplay').text(text || 'All Locations');

        if (val) {
            $('#faLocBtn').addClass('has-filter');
            $('#faLocClearBtn').show();
        } else {
            $('#faLocBtn').removeClass('has-filter');
            $('#faLocClearBtn').hide();
        }

        $('#faLocDropdownPanel .cl-panel-item').removeClass('selected');
        $('#faLocDropdownPanel').removeClass('show');
        $('#faLocBtn').removeClass('active');

        fetchAgencies(1);
    }
    window.selectLocationOption = selectLocationOption;

    function clearLocationFilter(e) {
        if (e) e.stopPropagation();
        selectLocationOption('', 'All Locations');
    }
    window.clearLocationFilter = clearLocationFilter;

    // ── Industry Combobox Controls ──
    function toggleIndustryDropdown(e) {
        if (e) e.stopPropagation();
        closeAllDropdowns('ind');
        $('#faIndDropdownPanel').toggleClass('show');
        $('#faIndBtn').toggleClass('active');
        if ($('#faIndDropdownPanel').hasClass('show')) {
            $('#faIndSearchInp').focus();
        }
    }
    window.toggleIndustryDropdown = toggleIndustryDropdown;

    function filterIndustryItems(q) {
        q = (q || '').toLowerCase().trim();
        $('#faIndDropdownBody .ind-item').each(function() {
            var name = $(this).data('name') || '';
            if (!q || name.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    window.filterIndustryItems = filterIndustryItems;

    function clearIndInput() {
        $('#faIndSearchInp').val('');
        filterIndustryItems('');
    }
    window.clearIndInput = clearIndInput;

    function selectIndustryOption(val, text) {
        window.agencyState.industry = val;
        window.agencyState.page = 1;
        $('#selected_industry').val(val);
        $('#faIndDisplay').text(text || 'All Industries');

        if (val) {
            $('#faIndBtn').addClass('has-filter');
            $('#faIndClearBtn').show();
        } else {
            $('#faIndBtn').removeClass('has-filter');
            $('#faIndClearBtn').hide();
        }

        $('#faIndDropdownPanel .cl-panel-item').removeClass('selected');
        $('#faIndDropdownPanel').removeClass('show');
        $('#faIndBtn').removeClass('active');

        fetchAgencies(1);
    }
    window.selectIndustryOption = selectIndustryOption;

    function clearIndustryFilter(e) {
        if (e) e.stopPropagation();
        selectIndustryOption('', 'All Industries');
    }
    window.clearIndustryFilter = clearIndustryFilter;

    // ── Sort Combobox Controls ──
    function toggleSortDropdown(e) {
        if (e) e.stopPropagation();
        closeAllDropdowns('sort');
        $('#faSortDropdownPanel').toggleClass('show');
        $('#faSortBtn').toggleClass('active');
    }
    window.toggleSortDropdown = toggleSortDropdown;

    function selectSortOption(val, text) {
        window.agencyState.sort = val;
        window.agencyState.page = 1;
        $('#selected_sort').val(val);
        $('#faSortDisplay').text(text || 'Best Match');

        $('#faSortDropdownPanel .cl-panel-item').removeClass('selected');
        $('#faSortDropdownPanel').removeClass('show');
        $('#faSortBtn').removeClass('active');

        fetchAgencies(1);
    }
    window.selectSortOption = selectSortOption;

    // Close all open dropdown panels
    function closeAllDropdowns(except) {
        if (except !== 'loc') { $('#faLocDropdownPanel').removeClass('show'); $('#faLocBtn').removeClass('active'); }
        if (except !== 'ind') { $('#faIndDropdownPanel').removeClass('show'); $('#faIndBtn').removeClass('active'); }
        if (except !== 'sort') { $('#faSortDropdownPanel').removeClass('show'); $('#faSortBtn').removeClass('active'); }
        if (except !== 'job') { $('#faJobPopoverPanel').removeClass('show'); $('#faJobComboboxBtn').removeClass('active'); }
    }

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.cl-loc-dropdown-wrapper, .cl-ind-dropdown-wrapper, .cl-sort-dropdown-wrapper, .fa-job-combobox-wrapper').length) {
            closeAllDropdowns();
        }
    });

    // ── Search Bar Controls ──
    function clearAgencySearch(e) {
        if (e) e.stopPropagation();
        $('#agencySearchInp').val('');
        $('#agencySearchClearBtn').hide();
        $('#agencyMatchCount').hide();
        $('#agencySearchKbdHint').show();
        window.agencyState.search = '';
        fetchAgencies(1);
        $('#agencySearchInp').focus();
    }
    window.clearAgencySearch = clearAgencySearch;

    // ── Reset All Filters ──
    function resetAllAgencyFilters() {
        window.agencyState.tab = 'all';
        window.agencyState.search = '';
        window.agencyState.location = '';
        window.agencyState.industry = '';
        window.agencyState.sort = 'best';
        window.agencyState.page = 1;

        $('#agencySearchInp').val('');
        $('#agencySearchClearBtn').hide();
        $('#agencyMatchCount').hide();
        $('#agencySearchKbdHint').show();

        selectAgencyTab('all');
        selectLocationOption('', 'All Locations');
        selectIndustryOption('', 'All Industries');
        selectSortOption('best', 'Best Match');
    }
    window.resetAllAgencyFilters = resetAllAgencyFilters;

    // ── Core Fetch & Render Engine (Strict CQRS, L1 RAM Cache) ──
    function fetchAgencies(page) {
        if (typeof page === 'undefined') page = window.agencyState.page || 1;
        window.agencyState.page = page;

        var queryParams = {
            tab: window.agencyState.tab,
            search: window.agencyState.search,
            kt_tagify_location: window.agencyState.location,
            country: window.agencyState.location,
            industry: window.agencyState.industry,
            sortingfilter: window.agencyState.sort,
            page: page,
            per_page: window.agencyState.perPage,
            size: window.agencyState.perPage
        };

        var cacheKey = 'fa_' + JSON.stringify(queryParams);

        // Check L1 In-Memory RAM Cache (Instant 0ms UI response)
        if (window.JeroAgencyRAM.has(cacheKey)) {
            var cached = window.JeroAgencyRAM.get(cacheKey);
            renderAgencies(cached);
            return Promise.resolve(cached);
        }

        // Abort in-flight XHR to eliminate race conditions
        if (activeAgencyXHR && activeAgencyXHR.readyState !== 4) {
            activeAgencyXHR.abort();
        }

        $('.loadingMessage').show();

        return new Promise(function(resolve, reject) {
            activeAgencyXHR = $.ajax({
                url: "{!! route('service_company.find.findAgencyData') !!}",
                type: 'GET',
                data: queryParams,
                success: function(response) {
                    $('.loadingMessage').hide();
                    if (response) {
                        window.JeroAgencyRAM.set(cacheKey, response);
                        renderAgencies(response);
                        resolve(response);
                    }
                },
                error: function(xhr, status, err) {
                    $('.loadingMessage').hide();
                    if (status !== 'abort') {
                        $('#findAgencyList').html('<div class="alert alert-danger m-3">Unable to retrieve agencies. Please try again.</div>');
                        reject(err);
                    }
                }
            });
        });
    }
    window.fetchAgencies = fetchAgencies;

    function renderAgencies(response) {
        $('#findAgencyList').html(response.html);
        $('#paginationfindagency').html(response.pagination);

        // Sync match count badge
        var total = response.total !== undefined ? response.total : 0;
        $('#agencyMatchCount').text(total + (total === 1 ? ' agency' : ' agencies')).show();

        // Update Domestic Agencies subtext dynamically based on HQ country (Point 5)
        if (response.kpi_counts) {
            if (response.kpi_counts.domestic_subtext) {
                $('#kpi_domestic_agencies_sub').text(response.kpi_counts.domestic_subtext);
            } else if (response.kpi_counts.domestic_agencies !== undefined) {
                var dCount = response.kpi_counts.domestic_agencies;
                var adj = response.kpi_counts.domestic_country_adjective || 'Domestic';
                $('#kpi_domestic_agencies_sub').text(dCount + ' ' + adj + (dCount === 1 ? ' agency' : ' agencies'));
            }
        }

        // Apply dynamic AI job matching to rendered cards
        applyActiveJobMatching();

        // Initialize Bootstrap tooltips for serving countries & info badges
        if ($.fn.tooltip) {
            $('[data-bs-toggle="tooltip"]').tooltip({
                boundary: 'window',
                trigger: 'hover'
            });
        }
    }

    // ── Sliding Bench Candidate Preview Drawer (Point 2) ──
    function openAgencyBenchDrawer(agencyId, agencyName, count) {
        $('#benchDrawerAgencyName').text(agencyName || 'Agency Bench');
        $('#drawer_agency_bench_preview').addClass('show');
        $('#benchDrawerOverlay').addClass('show');

        var $body = $('#benchDrawerBody');
        $body.html('<div class="d-flex flex-column align-items-center justify-content-center p-5"><div class="spinner-border text-primary mb-3" role="status"></div><div class="text-muted font-size-12">Retrieving pre-vetted bench profiles...</div></div>');

        var url = "{{ route('service_company.find.agencyBenchCandidates', ':id') }}".replace(':id', agencyId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(res) {
                if (res && res.candidates && res.candidates.length > 0) {
                    var html = '';
                    res.candidates.forEach(function(c) {
                        var skillsHtml = '';
                        (c.skills || []).forEach(function(sk) {
                            skillsHtml += '<span class="cl-mini-tag me-1">' + sk + '</span>';
                        });

                        html += '<div class="bench-cand-card">' +
                            '<div class="d-flex align-items-start justify-content-between gap-2">' +
                                '<div>' +
                                    '<div class="d-flex align-items-center gap-2">' +
                                        '<span class="font-weight-700 text-dark font-size-12">' + c.token + '</span>' +
                                        '<span class="badge badge-light-success py-0.5 px-2 font-size-10 font-weight-700">' + c.notice_period + '</span>' +
                                    '</div>' +
                                    '<div class="text-primary font-weight-600 font-size-12 mt-1">' + c.designation + '</div>' +
                                    '<div class="font-size-11 text-muted">' + c.experience + ' • ' + c.location + '</div>' +
                                '</div>' +
                                '<div class="text-end">' +
                                    '<div class="font-weight-700 text-dark font-size-12">' + c.rate_formatted + '</div>' +
                                    '<div class="font-size-10 text-muted">All-inclusive</div>' +
                                '</div>' +
                            '</div>' +
                            '<div class="d-flex flex-wrap gap-1 mt-2">' + skillsHtml + '</div>' +
                            '<div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top border-slate-100">' +
                                '<span class="font-size-11 text-success font-weight-600"><i class="fas fa-check-circle me-1"></i>Pre-Screened</span>' +
                                '<button type="button" class="btn btn-xs btn-light-primary" onclick="requestCandidateInterview(' + agencyId + ', \'' + c.token + '\')">⚡ Request Interview</button>' +
                            '</div>' +
                        '</div>';
                    });
                    $body.html(html);
                } else {
                    $body.html('<div class="text-center p-5 text-muted"><i class="fas fa-user-clock font-size-28 mb-2 text-warning"></i><div class="font-weight-700 text-dark">No Bench Candidates Currently Listed</div><div class="font-size-12 mt-1">This partner agency is currently deploying dedicated recruiters on active mandates. Click Send Request on their card to request custom talent sourcing.</div></div>');
                }
            },
            error: function() {
                $body.html('<div class="alert alert-danger m-3 font-size-12">Unable to retrieve bench candidates. Please try again.</div>');
            }
        });
    }
    window.openAgencyBenchDrawer = openAgencyBenchDrawer;

    function closeAgencyBenchDrawer() {
        $('#drawer_agency_bench_preview').removeClass('show');
        $('#benchDrawerOverlay').removeClass('show');
    }
    window.closeAgencyBenchDrawer = closeAgencyBenchDrawer;

    function requestCandidateInterview(agencyId, candToken) {
        alert("⚡ Interview request dispatched for " + candToken + ".\nThe partner agency lead recruiter has been alerted and will respond within 4 hours.");
        closeAgencyBenchDrawer();
    }
    window.requestCandidateInterview = requestCandidateInterview;

    function dispatchDirectMandate() {
        alert("⚡ Discovery mandate dispatched to agency lead recruiters.\nFirst 3 vetted profiles guaranteed within 36-48h SLA.");
        closeAgencyBenchDrawer();
    }
    window.dispatchDirectMandate = dispatchDirectMandate;

    // ── Sliding Agency Terms Drawer (Point 3 & User Request) ──
    function openAgencyTermsDrawer(agencyId) {
        var $card = $('.cl-primary-card[data-agency-id="' + agencyId + '"]');
        var termsRaw = $card.attr('data-agency-terms');
        var terms = {};
        try {
            terms = JSON.parse(termsRaw) || {};
        } catch(e) {
            try {
                var decoded = $('<textarea/>').html(termsRaw).text();
                terms = JSON.parse(decoded) || {};
            } catch(e2) {
                terms = {};
            }
        }

        var agencyName = terms.agency_name || 'Agency Commercial Terms';
        $('#termsDrawerAgencyName').text(agencyName);
        $('#termsDrawerAgencyMeta').text((terms.location || 'India') + ' • ' + (terms.industry || 'Staffing & IT'));

        // Populate avatar in drawer header
        var initialLetter = (agencyName.trim()[0] || 'A').toUpperCase();
        if (terms.logo && terms.logo.length > 10) {
            $('#termsDrawerAvatarBox').html('<img src="' + terms.logo + '" alt="' + agencyName + '" class="terms-header-avatar-img" />');
        } else {
            $('#termsDrawerAvatarBox').html('<div class="terms-header-avatar-fallback">' + initialLetter + '</div>');
        }

        var servingCountries = terms.serving_countries || [];
        var servingHtml = '';
        if (servingCountries.length > 0) {
            servingHtml = servingCountries.map(function(c) {
                return '<span class="badge badge-light-primary py-1 px-2.5 me-1.5 mb-1.5 font-size-11 font-weight-600" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;"><i class="fas fa-map-marker-alt text-primary me-1 font-size-10"></i>' + c + '</span>';
            }).join(' ');
        } else {
            servingHtml = '<span class="badge badge-light-secondary py-1 px-2.5 font-size-11 font-weight-600 text-muted">India (Domestic Scope)</span>';
        }

        var bodyHtml = '';

        // Section 1: Pre-Agreed Commercial Rates
        bodyHtml += '<div class="terms-policy-card">' +
            '<div class="terms-policy-title">' +
                '<span><i class="fas fa-file-invoice-dollar text-success me-1.5"></i>Pre-Agreed Commercial Structure</span>' +
                '<span class="badge badge-light-success font-size-10 font-weight-700" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;">100% Contingent</span>' +
            '</div>';

        if (terms.has_fulltime) {
            bodyHtml += '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Full-Time (Direct Hire):</span>' +
                '<span class="terms-spec-val text-primary font-weight-700">' + (terms.fulltime_rate || '8.33% * CTC') + '</span>' +
            '</div>';
        }
        if (terms.has_contract) {
            bodyHtml += '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Contract Staffing:</span>' +
                '<span class="terms-spec-val font-weight-700" style="color:#7c3aed;">' + (terms.contract_rate || '$35 / Hr') + '</span>' +
            '</div>';
        }
        bodyHtml += '</div>';

        // Section 2: Replacement Guarantees & SLAs
        bodyHtml += '<div class="terms-policy-card">' +
            '<div class="terms-policy-title">' +
                '<span><i class="fas fa-shield-alt text-primary me-1.5"></i>Replacement Guarantee & SLA</span>' +
                '<span class="badge badge-light-warning font-size-10 font-weight-700" style="background:#fffbeb; color:#d97706; border:1px solid #fde68a;">Company Terms</span>' +
            '</div>' +
            '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Full-Time Replacement Window:</span>' +
                '<span class="terms-spec-val text-success font-weight-700">' + (terms.ft_replacement_days || 90) + ' Days Free Replacement</span>' +
            '</div>' +
            '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Replacement Eligibility:</span>' +
                '<span class="terms-spec-val">Early Exit / Performance Non-Fit</span>' +
            '</div>' +
            '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Contract Transition SLA:</span>' +
                '<span class="terms-spec-val">' + (terms.ct_replacement_days || 30) + ' Days Transition Window</span>' +
            '</div>' +
            '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Shortlist Turnaround SLA:</span>' +
                '<span class="terms-spec-val font-weight-600">First 3 Vetted CVs within 36–48h</span>' +
            '</div>' +
        '</div>';

        // Section 3: Invoicing & Payment Terms
        bodyHtml += '<div class="terms-policy-card">' +
            '<div class="terms-policy-title">' +
                '<span><i class="fas fa-credit-card text-info me-1.5"></i>Billing & Payment Terms</span>' +
                '<span class="badge badge-light-info font-size-10 font-weight-600" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;">Direct Settlement</span>' +
            '</div>' +
            '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Full-Time Fee Trigger:</span>' +
                '<span class="terms-spec-val font-weight-600">Candidate Joining Date (0 Upfront)</span>' +
            '</div>' +
            '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Full-Time Payment Terms:</span>' +
                '<span class="terms-spec-val font-weight-700 text-dark">Net-' + (terms.ft_payment_days || 30) + ' Days Post-Joining</span>' +
            '</div>' +
            '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Contract Billing Frequency:</span>' +
                '<span class="terms-spec-val font-weight-600">Monthly Approved Timesheets</span>' +
            '</div>' +
            '<div class="terms-spec-row">' +
                '<span class="terms-spec-lbl">Contract Payment Due:</span>' +
                '<span class="terms-spec-val font-weight-700 text-dark">Net-' + (terms.ct_payment_days || 15) + ' Days</span>' +
            '</div>' +
        '</div>';

        // Section 4: Operating Geography & Global Coverage
        bodyHtml += '<div class="terms-policy-card">' +
            '<div class="terms-policy-title">' +
                '<span><i class="fas fa-globe-americas text-primary me-1.5"></i>Serving Countries & Geographical Scope</span>' +
                '<span class="badge badge-light-info font-size-10 font-weight-600">' + (servingCountries.length || 1) + ' ' + (servingCountries.length === 1 ? 'Region' : 'Regions') + '</span>' +
            '</div>' +
            '<div class="d-flex flex-wrap gap-1 mt-1">' + servingHtml + '</div>' +
        '</div>';

        // Section 5: Standard Marketplace Governance & Platform Neutrality (User Mandate)
        bodyHtml += '<div class="terms-policy-card">' +
            '<div class="terms-policy-title">' +
                '<span><i class="fas fa-balance-scale text-primary me-1.5"></i>Standard Marketplace Governance</span>' +
                '<span class="badge badge-light-secondary font-size-10 font-weight-700" style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">Direct Bilateral Terms</span>' +
            '</div>' +
            '<div class="terms-gov-box">' +
                '<div class="terms-gov-item">' +
                    '<div class="terms-gov-title"><i class="fas fa-handshake text-primary me-1.5 font-size-10"></i>Company-Direct Terms & Mandatory Agreement</div>' +
                    '<div class="terms-gov-desc">All terms, conditions, commercial structures, billing cycles, payment timelines, candidate duplicate rules, profile validity, replacement guarantees, and regulatory compliances applied to agencies are established directly by the hiring company. Any agency wishing to work with a company must review, discuss, and mutually agree to all terms set by that company before commencing work or submitting candidates.</div>' +
                '</div>' +
                '<div class="terms-gov-item">' +
                    '<div class="terms-gov-title"><i class="fas fa-shield-alt text-muted me-1.5 font-size-10"></i>Platform Role Disclaimer (JERO.works)</div>' +
                    '<div class="terms-gov-desc">JERO.works operates strictly as an independent technology platform provider and has zero role, responsibility, or liability in setting, negotiating, or enforcing any terms between hiring companies and agencies. JERO.works does not participate in payments, contractual enforcement, or bilateral dispute resolution. All agreements, payments, deliverables, and liabilities remain 100% direct and bilateral between the hiring company and the partner agency.</div>' +
                '</div>' +
            '</div>' +
        '</div>';

        if (terms.custom_terms && terms.custom_terms.length > 5) {
            bodyHtml += '<div class="terms-policy-card">' +
                '<div class="terms-policy-title">' +
                    '<span><i class="fas fa-sticky-note text-warning me-1.5"></i>Company Specific Custom Clauses</span>' +
                    '<span class="badge badge-light-warning font-size-10 font-weight-600">Custom Terms</span>' +
                '</div>' +
                '<div class="font-size-11 text-dark" style="background:#fefce8; border:1px solid #fef08a; border-radius:6px; padding:10px 12px; line-height:1.5;">' + terms.custom_terms + '</div>' +
            '</div>';
        }

        $('#termsDrawerBody').html(bodyHtml);
        window.activeTermsAgencyId = agencyId;

        $('#drawer_agency_terms_view').addClass('show');
        $('#termsDrawerOverlay').addClass('show');
    }
    window.openAgencyTermsDrawer = openAgencyTermsDrawer;

    function closeAgencyTermsDrawer() {
        $('#drawer_agency_terms_view').removeClass('show');
        $('#termsDrawerOverlay').removeClass('show');
    }
    window.closeAgencyTermsDrawer = closeAgencyTermsDrawer;

    function dispatchMandateFromTerms() {
        var agencyId = window.activeTermsAgencyId;
        closeAgencyTermsDrawer();
        if (agencyId) {
            var $btn = $('#sp_list_' + agencyId);
            if ($btn.length > 0 && !$btn.hasClass('disabled')) {
                $btn.trigger('click');
            } else {
                alert("⚡ Mandate request dispatched to this partner.\nFirst 3 vetted profiles guaranteed within 36-48h SLA.");
            }
        }
    }
    window.dispatchMandateFromTerms = dispatchMandateFromTerms;

    // ── Agency Detail Drawer Trigger ──
    $(document).off('click', '.show-detail-direct').on('click', '.show-detail-direct', function(e) {
        e.preventDefault();
        $('#kt_activitiesDirectClient_toggle').trigger('click');
        var id = $(this).data('row-id');
        if (!id) return;

        if (window.JeroAgencyDetailCache.has(id)) {
            $('#kt_activitiesDirectClient_body').html(window.JeroAgencyDetailCache.get(id));
            return;
        }

        if (activeDetailXHR && activeDetailXHR.readyState !== 4) {
            activeDetailXHR.abort();
        }

        var url = "{{ route('service_company.consultancy.dviewmore', ':id') }}";
        url = url.replace(':id', id);

        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $('#kt_activitiesDirectClient_body').html('<div class="d-flex justify-content-center p-5"><img src="' + baseurl + '" height="35px"></div>');

        activeDetailXHR = $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                if (response && response.view) {
                    window.JeroAgencyDetailCache.set(id, response.view);
                    $('#kt_activitiesDirectClient_body').html(response.view);
                }
            },
            error: function(xhr, status) {
                if (status !== 'abort') {
                    $('#kt_activitiesDirectClient_body').html('<div class="alert alert-danger m-4">Failed to load agency details. Please try again.</div>');
                }
            }
        });
    });

    // ── Send / Cancel Agency Request Action ──
    $('body').off('click', '.send_job_recommanded_request').on('click', '.send_job_recommanded_request', function(event) {
        event.preventDefault();

        var $this = $(this);
        if ($this.prop('disabled') || $this.hasClass('is-submitting')) return;
        $this.prop('disabled', true).addClass('is-submitting');
        var originalBtnHtml = $this.html();
        $this.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Processing...');

        var from_company_id = $this.data('row-id');
        var current_jobid = $this.data('row-jobid');
        var event_id = $this.data('row-event');
        var matchcount = $this.data('matchcount');
        var id = from_company_id;
        var boostercom = $(this).data('boostercom') || 0;
        var boostids = boostercom == 1 ? $(this).data('boost-ids') : '';

        var idempotencyKey = 'agency_req_' + from_company_id + '_' + id + '_' + event_id + '_' + Date.now();

        var url = "{{ route('service_company.agency.recommanded_request', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            type: 'POST',
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Idempotency-Key': idempotencyKey
            },
            data: {
                'from_company_id': from_company_id,
                'event_id': event_id,
                'current_jobid': current_jobid,
                'boostercom': boostercom,
                'boostids': boostids,
                'matchcount': matchcount,
                'idempotency_key': idempotencyKey
            },
            success: function(response) {
                if (response.status === "success") {
                    var $card = $('.cl-primary-card[data-agency-id="' + id + '"]');
                    var agencyName = $card.find('.cl-identity-name').text().trim() || 'Partner Agency';

                    if (event_id == 1) {
                        $('#sp_list_' + id).removeClass('btn-primary')
                                      .addClass('btn-light-danger')
                                      .attr('data-row-event', '-1')
                                      .html('<i class="fas fa-times-circle me-1"></i>Cancel Request');
                        if (typeof prependLiveTickerItem === 'function') {
                            prependLiveTickerItem('<i class="fas fa-paper-plane text-info me-1"></i>Mandate request dispatched to <strong>' + agencyName + '</strong>');
                        }
                    } else {
                        $('#sp_list_' + id).removeClass('btn-light-danger')
                                      .addClass('btn-primary')
                                      .attr('data-row-event', '1')
                                      .html('<i class="fas fa-paper-plane me-1"></i>Send Request');
                        if (typeof prependLiveTickerItem === 'function') {
                            prependLiveTickerItem('<i class="fas fa-times-circle text-danger me-1"></i>Mandate request canceled for <strong>' + agencyName + '</strong>');
                        }
                    }

                    // Invalidate caches
                    window.JeroAgencyRAM.clear();

                    Swal.fire({
                        title: "Success",
                        text: response.message || "Agency request updated successfully.",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                } else if (response.status === "warning" || response.status === "info") {
                    Swal.fire("Notice", response.message, "warning");
                    $this.html(originalBtnHtml);
                }
                $this.prop('disabled', false).removeClass('is-submitting');
            },
            error: function() {
                Swal.fire("Error", "Could not complete the request. Please try again.", "error");
                $this.prop('disabled', false).removeClass('is-submitting');
                $this.html(originalBtnHtml);
            }
        });
    });

    // ── Pagination Controls ──
    $(document).off('click', '#paginationfindagency .pagi-page, #paginationfindagency .pagi-arrow')
        .on('click', '#paginationfindagency .pagi-page, #paginationfindagency .pagi-arrow', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled') || $(this).hasClass('disabled')) return;
            fetchAgencies(page);
        });

    $(document).off('change', '#paginationfindagency .pagi-perpage')
        .on('change', '#paginationfindagency .pagi-perpage', function(e) {
            var selectedPerPage = $(this).val();
            if (selectedPerPage) {
                localStorage.setItem('agencyPerPage', selectedPerPage);
                window.agencyState.perPage = selectedPerPage;
                fetchAgencies(1);
            }
        });

    // ── Real-Time Live Ticker Engine (Point 2) ──
    function prependLiveTickerItem(htmlContent) {
        if (!htmlContent) return;
        var $content = $('#faTickerContent');
        var $item = $('<span class="ticker-item">' + htmlContent + '</span>');
        var $existing = $content.find('.ticker-item');

        if ($existing.length > 0) {
            var $sep = $('<span class="ticker-sep">•</span>');
            $content.prepend($sep).prepend($item);
            if ($content.find('.ticker-item').length > 5) {
                $content.find('.ticker-item:gt(4)').remove();
                $content.find('.ticker-sep:gt(4)').remove();
            }
        } else {
            $content.html($item);
        }

        $('#faLiveTicker .ticker-pill').removeClass('static').addClass('live');
        $('#faLiveTicker .ticker-pill span:last-child').text('LIVE TICKER');
        $('#faLiveTicker .ticker-dot').addClass('live-pulse');
        $('#faTickerTimestamp').text('Just Now');
    }
    window.prependLiveTickerItem = prependLiveTickerItem;

    function refreshLiveTicker() {
        if (document.hidden) return; // Zero background CPU/strain when inactive (Rule 22)
        $.ajax({
            url: "{!! route('service_company.find.findAgencyTicker') !!}",
            type: 'GET',
            success: function(res) {
                if (res && res.items && res.items.length > 0) {
                    var html = '';
                    res.items.forEach(function(item, idx) {
                        if (idx > 0) html += '<span class="ticker-sep">•</span>';
                        html += '<span class="ticker-item">' + item.html + '</span>';
                    });
                    $('#faTickerContent').html(html);
                    if (res.is_live) {
                        $('#faLiveTicker .ticker-pill').removeClass('static').addClass('live');
                        $('#faLiveTicker .ticker-pill span:last-child').text('LIVE TICKER');
                        $('#faLiveTicker .ticker-dot').addClass('live-pulse');
                    }
                    if (res.updated_at) {
                        $('#faTickerTimestamp').text(res.updated_at);
                    }
                }
            }
        });
    }
    window.refreshLiveTicker = refreshLiveTicker;

    // ── Keyboard & Lifecycle Handlers ──
    $(document).ready(function() {
        // Initial Fetch
        fetchAgencies(1);

        // Search Input with Debounce (300ms for instant client-first responsiveness)
        $('#agencySearchInp').on('input', function() {
            var val = ($(this).val() || '').trim();
            if (val.length > 0) {
                $('#agencySearchClearBtn').show();
                $('#agencySearchKbdHint').hide();
            } else {
                $('#agencySearchClearBtn').hide();
                $('#agencySearchKbdHint').show();
            }

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function() {
                window.agencyState.search = val;
                fetchAgencies(1);
            }, 300);
        });

        // Search Shortcuts: Esc to clear, Enter to force fetch
        $('#agencySearchInp').on('keydown', function(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                clearAgencySearch();
                $(this).blur();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchDebounceTimer);
                window.agencyState.search = $(this).val().trim();
                fetchAgencies(1);
            }
        });

        // Global Shortcut: Ctrl+K / Cmd+K to focus search from anywhere
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                var activeEl = document.activeElement;
                var isInput = activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.isContentEditable);
                if (!isInput || activeEl.id === 'agencySearchInp') {
                    e.preventDefault();
                    $('#agencySearchInp').focus().select();
                }
            } else if (e.key === 'Escape') {
                closeAllDropdowns();
                closeAgencyTermsDrawer();
                closeAgencyBenchDrawer();
            }
        });

        // Realtime Echo Subscription for agency.request.delta, activity, and placements
        @php
            $currentEchoCompanyId = $company_id ?? (auth()->check() ? auth()->user()->company_id : 0);
        @endphp
        if (typeof window.Echo !== 'undefined' && window.Echo && {{ $currentEchoCompanyId }} > 0) {
            window.Echo.private('company.' + {{ $currentEchoCompanyId }})
                .listen('.agency.request.delta', function(e) {
                    if (window.JeroAgencyRAM) window.JeroAgencyRAM.clear();
                    if (e && e.ticker_html) {
                        prependLiveTickerItem(e.ticker_html);
                    } else if (e && e.agency_name) {
                        prependLiveTickerItem('<i class="fas fa-handshake text-primary me-1"></i>Partnership update with <strong>' + e.agency_name + '</strong>');
                    } else {
                        refreshLiveTicker();
                    }
                    fetchAgencies(window.agencyState.page || 1);
                })
                .listen('.agency.activity.logged', function(e) {
                    if (e && e.html) {
                        prependLiveTickerItem(e.html);
                    } else {
                        refreshLiveTicker();
                    }
                })
                .listen('.placement.record.updated', function(e) {
                    if (e && e.html) {
                        prependLiveTickerItem(e.html);
                    } else {
                        refreshLiveTicker();
                    }
                });
        }

        // Periodic Background Safety Refresh (Every 90s, only if page is visible, Rule 22 & Rule 4 compliant)
        setInterval(function() {
            if (!document.hidden && document.visibilityState === 'visible') {
                refreshLiveTicker();
            }
        }, 90000);
    });
</script>
@endpush
