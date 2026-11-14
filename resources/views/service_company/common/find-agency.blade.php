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

/* ── TIER 1: Top Header Toolbar Card ── */
.fa-header-card {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 14px 20px !important;
    margin-bottom: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 16px !important;
    flex-wrap: wrap !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
}
.fa-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}
.fa-header-icon-box {
    width: 44px !important;
    height: 44px !important;
    border-radius: 10px !important;
    background: #eff6ff !important;
    border: 1px solid #dbeafe !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #2563eb !important;
    font-size: 18px !important;
    flex-shrink: 0 !important;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.06) !important;
}
.fa-header-title-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}
.fa-header-title {
    font-size: 19px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    letter-spacing: -0.015em !important;
    margin: 0 !important;
    line-height: 1.2 !important;
}
.fa-header-subtitle {
    font-size: 12.5px !important;
    font-weight: 400 !important;
    color: #64748b !important;
    margin-top: 3px !important;
    line-height: 1.35 !important;
}
.fa-header-actions {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-shrink: 0 !important;
}
.fa-trust-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: #ecfdf5 !important;
    border: 1px solid #a7f3d0 !important;
    color: #065f46 !important;
    border-radius: 20px !important;
    padding: 6px 12px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
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
    padding: 10px 11px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-decoration: none;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
    min-width: 0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
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
    gap: 8px;
    margin-bottom: 6px;
    min-width: 0;
}
.cl-card-icon {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 14px;
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
.cl-card-num {
    font-size: 19px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.1;
    white-space: nowrap;
}
.cl-card-lbl {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}
.cl-card-sub {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
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
.cl-card.active[data-tab="all"] .cl-card-num { color: #1d4ed8 !important; }
.cl-card.active[data-tab="all"] .cl-card-lbl { color: #1e40af !important; font-weight: 700 !important; }

.cl-card.active[data-tab="bench"] {
    background: #eef2ff !important;
    border-color: #818cf8 !important;
    box-shadow: 0 0 0 1.5px #4f46e5, 0 4px 14px rgba(79, 70, 229, 0.12) !important;
}
.cl-card.active[data-tab="bench"] .cl-card-icon { background: #ffffff !important; color: #4f46e5 !important; }
.cl-card.active[data-tab="bench"] .cl-card-num { color: #3730a3 !important; }
.cl-card.active[data-tab="bench"] .cl-card-lbl { color: #4338ca !important; font-weight: 700 !important; }

.cl-card.active[data-tab="contract"] {
    background: #ecfdf5 !important;
    border-color: #34d399 !important;
    box-shadow: 0 0 0 1.5px #059669, 0 4px 14px rgba(5, 150, 105, 0.12) !important;
}
.cl-card.active[data-tab="contract"] .cl-card-icon { background: #ffffff !important; color: #059669 !important; }
.cl-card.active[data-tab="contract"] .cl-card-num { color: #065f46 !important; }
.cl-card.active[data-tab="contract"] .cl-card-lbl { color: #047857 !important; font-weight: 700 !important; }

.cl-card.active[data-tab="fulltime"] {
    background: #faf5ff !important;
    border-color: #c084fc !important;
    box-shadow: 0 0 0 1.5px #9333ea, 0 4px 14px rgba(147, 51, 234, 0.12) !important;
}
.cl-card.active[data-tab="fulltime"] .cl-card-icon { background: #ffffff !important; color: #9333ea !important; }
.cl-card.active[data-tab="fulltime"] .cl-card-num { color: #6b21a8 !important; }
.cl-card.active[data-tab="fulltime"] .cl-card-lbl { color: #7e22ce !important; font-weight: 700 !important; }

.cl-card.active[data-tab="india"] {
    background: #fffbeb !important;
    border-color: #facc15 !important;
    box-shadow: 0 0 0 1.5px #d97706, 0 4px 14px rgba(217, 119, 6, 0.12) !important;
}
.cl-card.active[data-tab="india"] .cl-card-icon { background: #ffffff !important; color: #d97706 !important; }
.cl-card.active[data-tab="india"] .cl-card-num { color: #92400e !important; }
.cl-card.active[data-tab="india"] .cl-card-lbl { color: #b45309 !important; font-weight: 700 !important; }

.cl-card.active[data-tab="global"] {
    background: #f0fdfa !important;
    border-color: #2dd4bf !important;
    box-shadow: 0 0 0 1.5px #0d9488, 0 4px 14px rgba(13, 148, 136, 0.12) !important;
}
.cl-card.active[data-tab="global"] .cl-card-icon { background: #ffffff !important; color: #0d9488 !important; }
.cl-card.active[data-tab="global"] .cl-card-num { color: #115e59 !important; }
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
    padding: 10px 14px 8px 14px !important;
    margin-bottom: 2px !important;
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

/* 4 Discrete Sub-Cards Grid */
.cl-subcards-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.4fr) minmax(0, 1.3fr) minmax(0, 1.7fr) !important;
    gap: 8px !important;
    width: 100% !important;
    align-items: stretch !important;
}

/* Sub-Card Base Container */
.cl-subcard {
    background: #f8fafc !important;
    border: 1px solid #edf2f7 !important;
    border-radius: 8px !important;
    padding: 8px 10px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: flex-start !important;
    min-width: 0 !important;
    box-sizing: border-box !important;
    position: relative !important;
}

/* Mandatory Dedicated Header Background (Rule 20) */
.cl-subcard-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 5px 8px !important;
    margin: -8px -10px 8px -10px !important;
    background: #f1f5f9 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    border-radius: 7px 7px 0 0 !important;
    min-height: 24px !important;
}
.cl-subcard-title {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.35px !important;
    color: #475569 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Sub-Card 1: Agency Identity */
.cl-identity-body {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.agency-logo-wrap {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
}
.agency-logo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.agency-logo-initials {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 700;
}
.agency-meta-info {
    min-width: 0;
    flex: 1;
}
.agency-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-decoration: none;
    line-height: 1.25;
}
.agency-name:hover {
    color: #009ef7;
    text-decoration: underline;
}
.agency-ind-pill {
    font-size: 11px;
    color: #64748b;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.agency-skills-footer {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 6px;
    align-items: center;
}
.cl-skill-tag {
    font-size: 9.5px;
    font-weight: 600;
    background: #eef2ff;
    color: #4f46e5;
    border: 1px solid #e0e7ff;
    border-radius: 4px;
    padding: 1px 5px;
    line-height: 1.2;
}
.cl-skill-tag.more {
    background: #f1f5f9;
    color: #64748b;
    border-color: #e2e8f0;
}

/* Sub-Card 2: Location & Recruiters */
.cl-loc-meta-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.cl-meta-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11.5px;
}
.cl-meta-icon {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    flex-shrink: 0;
}
.cl-meta-icon.loc { background: #fef2f2; }
.cl-meta-icon.team { background: #eff6ff; }
.cl-meta-text {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
    min-width: 0;
}
.cl-meta-val {
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cl-meta-sub {
    font-size: 9.5px;
    color: #64748b;
}

/* Sub-Card 3: Commercials */
.cl-rates-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 3px;
    height: 100%;
}
.rate-highlight-val {
    font-size: 14.5px;
    font-weight: 700;
    color: #059669;
    letter-spacing: -0.01em;
}
.rate-type-desc {
    font-size: 10.5px;
    font-weight: 500;
    color: #64748b;
}

/* Sub-Card 4: Talent Pool */
.cl-talent-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    margin-top: 2px;
}
.talent-stat-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 4px 6px;
    text-align: center;
}
.talent-stat-box.bench {
    background: #fffbeb;
    border-color: #fde68a;
}
.talent-stat-box .stat-num {
    font-size: 13px;
    font-weight: 700;
    line-height: 1.1;
}
.talent-stat-box .stat-lbl {
    font-size: 9px;
    font-weight: 600;
    color: #64748b;
    margin-top: 2px;
}

/* ── Bottom Activity Strip ── */
.cl-card-bottom-strip {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    padding: 6px 10px !important;
    margin-top: 8px !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
}
.cl-bottom-left {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    color: #64748b;
}
.cl-status-dot-pulse {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    display: inline-block;
}
.cl-activity-lbl strong {
    color: #0f172a;
    font-weight: 600;
}
.cl-verified-badge-text {
    font-size: 10.5px;
    color: #059669;
    font-weight: 600;
}
.cl-bottom-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.cl-action-btn, .cl-view-profile-btn {
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 5px 12px !important;
    white-space: nowrap !important;
    height: 30px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
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

/* ── 15.6" Screen Responsive Breakpoints ── */
@media (max-width: 1200px) {
    .cl-subcards-grid {
        grid-template-columns: minmax(0, 2fr) minmax(0, 1.3fr) minmax(0, 1.2fr) minmax(0, 1.5fr) !important;
        gap: 6px !important;
    }
}
@media (max-width: 992px) {
    .cl-stat-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
    .cl-subcards-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
@media (max-width: 640px) {
    .cl-stat-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    .cl-subcards-grid {
        grid-template-columns: 1fr !important;
    }
    .cl-card-bottom-strip {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
}
</style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_find_agency" id="kt_content" style="margin-top: -70px;">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid px-3">

            <!-- ── TIER 1: Header Toolbar Card (Rule 20) ── -->
            <div class="fa-header-card">
                <div class="fa-header-left">
                    <div class="fa-header-icon-box">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div>
                        <div class="fa-header-title-row">
                            <h1 class="fa-header-title">Find Trusted Hiring Agencies</h1>
                            <span class="fa-trust-pill">
                                <i class="fas fa-shield-alt"></i> Verified Marketplace
                            </span>
                        </div>
                        <div class="fa-header-subtitle">
                            Discover & collaborate with verified recruitment partners tailored to your exact hiring mandates.
                        </div>
                    </div>
                </div>
                <div class="fa-header-actions">
                    <button type="button" class="btn btn-sm btn-light-primary" onclick="resetAllAgencyFilters()">
                        <i class="fas fa-redo me-1"></i>Refresh Catalog
                    </button>
                </div>
            </div>

            <!-- ── TIER 2: 6 Stat KPI Cards Row (Single Horizontal Row) ── -->
            <div class="cl-stat-grid" id="agencyKpiGrid">
                
                <!-- 01 Total Agencies -->
                <div class="cl-card active" data-tab="all" onclick="selectAgencyTab('all', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon blue">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-num" id="kpi_total_agencies">{{ $kpiCounts['total_agencies'] ?? $allCompanies }}</div>
                            <div class="cl-card-lbl">01 Total Agencies</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-check-circle text-primary font-size-11"></i>
                        <span>All verified partners</span>
                    </div>
                </div>

                <!-- 02 Bench Agencies -->
                <div class="cl-card" data-tab="bench" onclick="selectAgencyTab('bench', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon indigo">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-num" id="kpi_bench_agencies">{{ $kpiCounts['bench_agencies'] ?? 0 }}</div>
                            <div class="cl-card-lbl">02 Bench Agencies</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-user-check text-indigo font-size-11"></i>
                        <span>Immediate bench talent</span>
                    </div>
                </div>

                <!-- 03 Contract Staffing -->
                <div class="cl-card" data-tab="contract" onclick="selectAgencyTab('contract', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon green">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-num" id="kpi_contract_staffing">{{ $kpiCounts['contract_staffing'] ?? 0 }}</div>
                            <div class="cl-card-lbl">03 Contract Staffing</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-sync-alt text-success font-size-11"></i>
                        <span>Recurring monthly hiring</span>
                    </div>
                </div>

                <!-- 04 Full-Time Hiring -->
                <div class="cl-card" data-tab="fulltime" onclick="selectAgencyTab('fulltime', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon purple">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-num" id="kpi_fulltime_hiring">{{ $kpiCounts['fulltime_hiring'] ?? 0 }}</div>
                            <div class="cl-card-lbl">04 Full-Time Hiring</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-briefcase text-purple font-size-11"></i>
                        <span>Permanent recruitment</span>
                    </div>
                </div>

                <!-- 05 Indian Agencies -->
                <div class="cl-card" data-tab="india" onclick="selectAgencyTab('india', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon orange">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-num" id="kpi_indian_agencies">{{ $kpiCounts['indian_agencies'] ?? 0 }}</div>
                            <div class="cl-card-lbl">05 Indian Agencies</div>
                        </div>
                    </div>
                    <div class="cl-card-sub">
                        <i class="fas fa-flag text-warning font-size-11"></i>
                        <span>Domestic India coverage</span>
                    </div>
                </div>

                <!-- 06 Global Agencies -->
                <div class="cl-card" data-tab="global" onclick="selectAgencyTab('global', this)" role="button" tabindex="0">
                    <div class="cl-card-top">
                        <div class="cl-card-icon teal">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="cl-card-meta">
                            <div class="cl-card-num" id="kpi_global_agencies">{{ $kpiCounts['global_agencies'] ?? 0 }}</div>
                            <div class="cl-card-lbl">06 Global Agencies</div>
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
@endsection

@push('footer-script')
<script>
    // ── Global State & Caches ──
    window.agencyState = {
        tab: 'all',
        search: '',
        location: '',
        industry: '',
        sort: 'best',
        page: 1,
        perPage: localStorage.getItem('agencyPerPage') || 10
    };

    window.JeroAgencyRAM = new Map();
    window.JeroAgencyDetailCache = window.JeroAgencyDetailCache || new Map();
    var activeAgencyXHR = null;
    var activeDetailXHR = null;
    var searchDebounceTimer = null;

    // ── Tab Selection ──
    function selectAgencyTab(tabKey, element) {
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
    }

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.cl-loc-dropdown-wrapper, .cl-ind-dropdown-wrapper, .cl-sort-dropdown-wrapper').length) {
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

        // Update KPI counters if returned
        if (response.kpi_counts) {
            if (response.kpi_counts.total_agencies !== undefined) $('#kpi_total_agencies').text(response.kpi_counts.total_agencies);
            if (response.kpi_counts.bench_agencies !== undefined) $('#kpi_bench_agencies').text(response.kpi_counts.bench_agencies);
            if (response.kpi_counts.contract_staffing !== undefined) $('#kpi_contract_staffing').text(response.kpi_counts.contract_staffing);
            if (response.kpi_counts.fulltime_hiring !== undefined) $('#kpi_fulltime_hiring').text(response.kpi_counts.fulltime_hiring);
            if (response.kpi_counts.indian_agencies !== undefined) $('#kpi_indian_agencies').text(response.kpi_counts.indian_agencies);
            if (response.kpi_counts.global_agencies !== undefined) $('#kpi_global_agencies').text(response.kpi_counts.global_agencies);
        }
    }

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
                    if (event_id == 1) {
                        $('#sp_list_' + id).removeClass('btn-primary')
                                      .addClass('btn-light-danger')
                                      .attr('data-row-event', '-1')
                                      .html('<i class="fas fa-times-circle me-1"></i>Cancel Request');
                    } else {
                        $('#sp_list_' + id).removeClass('btn-light-danger')
                                      .addClass('btn-primary')
                                      .attr('data-row-event', '1')
                                      .html('<i class="fas fa-paper-plane me-1"></i>Send Request');
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

    // ── Keyboard & Lifecycle Handlers ──
    $(document).ready(function() {
        // Initial Fetch
        fetchAgencies(1);

        // Search Input with Debounce (30ms for instant client-first responsiveness)
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
            }
        });
    });
</script>
@endpush
