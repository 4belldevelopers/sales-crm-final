@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/clientList.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
<style>
/* ── Guaranteed Card-Based Header Design (Rule 20 Enterprise Standard) ── */
.cl-header, .cl-header-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  padding: 14px 20px !important;
  margin-bottom: 14px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 16px !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
}
.cl-header-left {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  min-width: 0 !important;
}
.cl-header-icon-box {
  width: 44px !important;
  height: 44px !important;
  border-radius: 10px !important;
  background: #eff6ff !important;
  border: 1px solid #bfdbfe !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #2563eb !important;
  flex-shrink: 0 !important;
  box-shadow: 0 1px 2px rgba(37, 99, 235, 0.06) !important;
}
.cl-header-title-row {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}
.cl-header-title {
  font-size: 20px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  letter-spacing: -0.015em !important;
  margin: 0 !important;
  line-height: 1.2 !important;
  font-family: 'Poppins', sans-serif !important;
}
.cl-verified-badge {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.cl-verified-badge svg {
  color: #16a34a !important;
  stroke: #16a34a !important;
}
.cl-header-subtitle {
  font-size: 13px !important;
  font-weight: 400 !important;
  color: #64748b !important;
  margin-top: 3px !important;
  line-height: 1.35 !important;
  font-family: 'Poppins', sans-serif !important;
}
.cl-header-actions {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  flex-shrink: 0 !important;
}
.cl-export-wrapper {
  position: relative !important;
  display: inline-block !important;
}
.cl-export-btn {
  display: inline-flex !important;
  align-items: center !important;
  gap: 7px !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 8px 16px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  cursor: pointer !important;
  text-decoration: none !important;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
  transition: all 0.15s ease !important;
  white-space: nowrap !important;
}
.cl-export-btn:hover {
  background: #f8fafc !important;
  border-color: #cbd5e1 !important;
}
.cl-export-dropdown-menu {
  min-width: 260px !important;
  padding: 6px !important;
  border-radius: 10px !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
  z-index: 1050 !important;
}
.cl-export-option {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  padding: 8px 10px !important;
  border-radius: 6px !important;
  transition: background 0.12s ease !important;
  text-decoration: none !important;
}
.cl-export-option:hover {
  background: #f1f5f9 !important;
}
.cl-export-opt-icon {
  width: 28px !important;
  height: 28px !important;
  border-radius: 6px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
}
.cl-export-opt-icon.csv { background: #eff6ff; color: #2563eb; }
.cl-export-opt-icon.all { background: #f0fdf4; color: #16a34a; }
.cl-export-opt-icon.pdf { background: #fef2f2; color: #dc2626; }
.cl-export-opt-text { display: flex; flex-direction: column; min-width: 0; }
.cl-export-opt-title { font-size: 12.5px; font-weight: 600; color: #0f172a; line-height: 1.25; }
.cl-export-opt-sub { font-size: 11px; color: #64748b; line-height: 1.2; margin-top: 2px; }

.cl-add-btn-primary {
  display: inline-flex !important;
  align-items: center !important;
  gap: 7px !important;
  background: #009ef7 !important;
  border: 1px solid #009ef7 !important;
  border-radius: 8px !important;
  padding: 8px 18px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #ffffff !important;
  cursor: pointer !important;
  text-decoration: none !important;
  box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
  transition: all 0.15s ease !important;
  white-space: nowrap !important;
}
.cl-add-btn-primary:hover {
  background: #0086d4 !important;
  border-color: #0086d4 !important;
  color: #ffffff !important;
}

/* ── 6 Stat Cards Row (Strictly 1 Single Horizontal Row — Never Wraps) ── */
.cl-stat-grid {
  display: grid !important;
  grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
  gap: 8px !important;
  margin-bottom: 14px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}
.cl-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 10px 10px !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  text-decoration: none !important;
  color: inherit !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
  transition: all 0.15s ease !important;
  cursor: pointer !important;
  min-width: 0 !important;
  overflow: hidden !important;
}
.cl-card:hover {
  transform: translateY(-2px) !important;
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
}
.cl-card.active {
  border-color: #009ef7 !important;
  box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.15), 0 2px 6px rgba(0, 158, 247, 0.1) !important;
}
.cl-card-top {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  min-width: 0 !important;
}
.cl-card-icon {
  width: 32px !important;
  height: 32px !important;
  min-width: 32px !important;
  border-radius: 7px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
}
.cl-card-icon svg {
  width: 16px !important;
  height: 16px !important;
}
.cl-card-icon.blue   { background: #eff6ff; color: #2563eb; }
.cl-card-icon.indigo { background: #eef2ff; color: #4f46e5; }
.cl-card-icon.green  { background: #f0fdf4; color: #16a34a; }
.cl-card-icon.pink   { background: #fdf2f8; color: #db2777; }
.cl-card-icon.purple { background: #faf5ff; color: #9333ea; }

.cl-card-meta {
  display: flex !important;
  flex-direction: column !important;
  min-width: 0 !important;
  flex: 1 1 0% !important;
  overflow: hidden !important;
}
.cl-card-num {
  font-size: 17px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.15 !important;
  font-variant-numeric: tabular-nums !important;
  font-family: 'Poppins', sans-serif !important;
}
.cl-card-lbl {
  font-size: 11px !important;
  font-weight: 500 !important;
  color: #64748b !important;
  line-height: 1.2 !important;
  margin-top: 1px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cl-card-sub {
  display: flex !important;
  align-items: center !important;
  gap: 3.5px !important;
  font-size: 10px !important;
  font-weight: 600 !important;
  margin-top: 6px !important;
  padding-top: 4px !important;
  border-top: 1px dashed #f1f5f9 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cl-card-sub span {
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
}
.cl-card-sub.green  { color: #16a34a; }
.cl-card-sub.blue   { color: #2563eb; }
.cl-card-sub.pink   { color: #db2777; }
.cl-card-sub.purple { color: #9333ea; }

/* ── Filter Bar & Micro Search Box ── */
.cl-filter-bar {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 10px 14px !important;
  margin-bottom: 14px !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  flex-wrap: wrap !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
}
.cl-search-box {
  position: relative !important;
  flex: 1 1 280px !important;
  min-width: 240px !important;
  height: 38px !important;
  display: flex !important;
  align-items: center !important;
}
.cl-search-icon {
  position: absolute !important;
  left: 12px !important;
  width: 15px !important;
  height: 15px !important;
  color: #94a3b8 !important;
  pointer-events: none !important;
}
.cl-search-inp {
  width: 100% !important;
  height: 100% !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 0 85px 0 34px !important;
  font-size: 12px !important;
  color: #0f172a !important;
  font-family: 'Poppins', sans-serif !important;
  transition: all 0.15s ease !important;
}
.cl-search-inp:focus {
  background: #ffffff !important;
  border-color: #009ef7 !important;
  box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
  outline: none !important;
}
.cl-search-right-affix {
  position: absolute !important;
  right: 8px !important;
  display: flex !important;
  align-items: center !important;
  gap: 5px !important;
}
.cl-search-count-pill {
  font-size: 10px !important;
  font-weight: 700 !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  padding: 2px 6px !important;
  border-radius: 10px !important;
  border: 1px solid #bfdbfe !important;
}
.cl-search-clear-btn {
  background: transparent !important;
  border: none !important;
  color: #94a3b8 !important;
  font-size: 12px !important;
  cursor: pointer !important;
  padding: 2px 4px !important;
  border-radius: 4px !important;
  line-height: 1 !important;
}
.cl-search-clear-btn:hover { color: #0f172a; background: #e2e8f0; }
.cl-search-kbd-hint {
  font-size: 9.5px !important;
  font-weight: 600 !important;
  color: #94a3b8 !important;
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 4px !important;
  padding: 1px 4px !important;
}

/* Combobox Pill Buttons */
.cl-loc-dropdown-wrapper,
.cl-ind-dropdown-wrapper,
.cl-act-dropdown-wrapper,
.cl-pipe-dropdown-wrapper,
.cl-sort-dropdown-wrapper {
  position: relative !important;
}
.cl-loc-combobox-btn,
.cl-ind-combobox-btn,
.cl-act-combobox-btn,
.cl-pipe-combobox-btn,
.cl-sort-combobox-btn {
  height: 38px !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 0 12px !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  cursor: pointer !important;
  font-size: 12px !important;
  color: #334155 !important;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
  transition: all 0.15s ease !important;
  white-space: nowrap !important;
}
.cl-loc-combobox-btn:hover,
.cl-ind-combobox-btn:hover,
.cl-act-combobox-btn:hover,
.cl-pipe-combobox-btn:hover,
.cl-sort-combobox-btn:hover {
  background: #f8fafc !important;
  border-color: #cbd5e1 !important;
}
.cl-pill-lbl { font-size: 10px !important; font-weight: 600 !important; color: #94a3b8 !important; text-transform: uppercase !important; }
.cl-pill-val { font-size: 12px !important; font-weight: 600 !important; color: #0f172a !important; }
.cl-pill-icon svg { color: #2563eb; }

/* Popover Panels */
.cl-loc-dropdown-panel,
.cl-ind-dropdown-panel,
.cl-act-dropdown-panel,
.cl-pipe-dropdown-panel,
.cl-sort-dropdown-panel {
  position: absolute !important;
  top: calc(100% + 6px) !important;
  right: 0 !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 8px !important;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
  z-index: 1050 !important;
  min-width: 220px !important;
  display: none;
}
.cl-act-dropdown-panel,
.cl-pipe-dropdown-panel,
.cl-sort-dropdown-panel {
  min-width: 240px !important;
}
.cl-loc-dropdown-panel.show,
.cl-ind-dropdown-panel.show,
.cl-act-dropdown-panel.show,
.cl-pipe-dropdown-panel.show,
.cl-sort-dropdown-panel.show {
  display: block !important;
}

.cl-act-section-hdr,
.cl-sort-section-hdr,
.cl-pipe-section-hdr {
  font-size: 10px !important;
  font-weight: 700 !important;
  color: #94a3b8 !important;
  text-transform: uppercase !important;
  padding: 6px 8px 4px !important;
}
.cl-act-item,
.cl-sort-item,
.cl-pipe-item {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 6px 8px !important;
  border-radius: 6px !important;
  cursor: pointer !important;
  font-size: 11.5px !important;
  color: #334155 !important;
  transition: background 0.12s ease !important;
}
.cl-act-item:hover,
.cl-sort-item:hover,
.cl-pipe-item:hover {
  background: #f1f5f9 !important;
  color: #0f172a !important;
}
.cl-act-item.selected,
.cl-sort-item.selected,
.cl-pipe-item.selected {
  background: #eff6ff !important;
  color: #2563eb !important;
  font-weight: 600 !important;
}
.cl-act-dot, .cl-pipe-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 6px;
}
.cl-dot-all    { background: #94a3b8; }
.cl-dot-green  { background: #16a34a; }
.cl-dot-blue   { background: #2563eb; }
.cl-dot-amber  { background: #f59e0b; }
.cl-dot-slate  { background: #64748b; }
.cl-dot-purple { background: #9333ea; }
.cl-dot-violet { background: #7c3aed; }

/* ── Table & Card Stream Container (Rule 20 Modern Card + Sub-Card System) ── */
.tbl {
  width: 100% !important;
  max-width: 100% !important;
  overflow-x: hidden !important;
}
.tbl-w {
  width: 100% !important;
  background: transparent !important;
}
.tbl-scroll-container {
  width: 100% !important;
  position: relative !important;
}
.tbl-h {
  display: grid !important;
  grid-template-columns: 2.2fr 1.3fr 1fr 2.5fr !important;
  gap: 12px !important;
  padding: 8px 14px !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  margin-bottom: 10px !important;
}
.tbl-th-subcard {
  font-size: 11px !important;
  font-weight: 700 !important;
  color: #475569 !important;
  text-transform: uppercase !important;
  display: flex !important;
  align-items: center !important;
  gap: 5px !important;
}
.tbl-th-label {
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Primary Card (Tier 3) */
.cl-primary-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 14px !important;
  margin-bottom: 12px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.15s ease !important;
  display: flex !important;
  flex-direction: column !important;
  position: relative !important;
  cursor: pointer !important;
}
.cl-primary-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
}

/* Sub-Cards Grid */
.cl-subcards-grid {
  display: grid !important;
  grid-template-columns: 2.2fr 1.3fr 1fr 2.5fr !important;
  gap: 12px !important;
  margin-bottom: 10px !important;
}
.cl-subcard {
  background: #f8fafc !important;
  border: 1px solid #eef2f6 !important;
  border-radius: 8px !important;
  padding: 10px 12px !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  min-width: 0 !important;
}
.cl-subcard-head {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  margin-bottom: 8px !important;
}
.cl-subcard-title {
  font-size: 10.5px !important;
  font-weight: 700 !important;
  color: #64748b !important;
  text-transform: uppercase !important;
  letter-spacing: 0.02em !important;
}
.cl-subcard-badges {
  display: flex !important;
  align-items: center !important;
  gap: 5px !important;
}
.cl-scope-pill {
  font-size: 9.5px !important;
  font-weight: 700 !important;
  padding: 1.5px 6px !important;
  border-radius: 4px !important;
  text-transform: uppercase !important;
}
.cl-scope-pill.pill-direct   { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.cl-scope-pill.pill-internal { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }

.cl-status-pill {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  font-size: 9.5px !important;
  font-weight: 700 !important;
  padding: 1.5px 6px !important;
  border-radius: 4px !important;
}
.cl-status-pill.pill-active   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.cl-status-pill.pill-inactive { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }
.cl-status-pill-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: currentColor;
}

/* Identity Subcard Body */
.cl-identity-body {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
}
.cl-logo-box {
  width: 42px !important;
  height: 42px !important;
  border-radius: 8px !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  overflow: hidden !important;
  flex-shrink: 0 !important;
}
.cl-logo-img { width: 100%; height: 100%; object-fit: cover; }
.cl-initials-fallback {
  font-size: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}
.cl-details-box {
  display: flex !important;
  flex-direction: column !important;
  min-width: 0 !important;
}
.cl-name {
  font-size: 13.5px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.25 !important;
  cursor: pointer !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cl-name:hover { color: #009ef7 !important; }
.cl-ind { font-size: 11px !important; color: #64748b !important; margin-top: 2px !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; }
.cl-size { font-size: 10px !important; color: #94a3b8 !important; }

/* Location Subcard Body */
.cl-loc-body {
  display: flex !important;
  align-items: flex-start !important;
  gap: 6px !important;
}
.cl-loc-pin { color: #2563eb; flex-shrink: 0; margin-top: 2px; }
.cl-loc-lines { display: flex; flex-direction: column; min-width: 0; }
.cl-loc-city { font-size: 12px; font-weight: 600; color: #0f172a; line-height: 1.2; }
.cl-loc-state, .cl-loc-country { font-size: 10.5px; color: #64748b; line-height: 1.2; }
.cl-loc-empty { font-size: 11px; color: #94a3b8; }

/* Jobs Subcard Body */
.cl-jobs-body {
  display: flex !important;
  flex-direction: column !important;
  gap: 3px !important;
}
.cl-job-line {
  display: flex !important;
  align-items: center !important;
  gap: 5px !important;
  font-size: 11px !important;
  color: #64748b !important;
}
.cl-job-line.has-count { font-weight: 600 !important; color: #0f172a !important; }
.cl-job-bullet {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}
.bullet-hiring  { background: #16a34a; }
.bullet-hold    { background: #f59e0b; }
.bullet-closed  { background: #64748b; }
.bullet-neutral { background: #cbd5e1; }
.cl-job-num { font-variant-numeric: tabular-nums; min-width: 12px; }
.cl-job-label { font-size: 10.5px; }

/* Candidate Pipeline Subcard Body */
.cl-pipe-body {
  display: flex !important;
  align-items: center !important;
}
.cl-pipeline-flow {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  width: 100% !important;
  overflow-x: auto !important;
}
.cl-pipeline-flow::-webkit-scrollbar { display: none; }
.cl-pipe-step {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  gap: 2px !important;
  flex: 1 !important;
}
.cl-pipe-pill {
  min-width: 20px !important;
  height: 18px !important;
  padding: 0 5px !important;
  border-radius: 9px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 10.5px !important;
  font-weight: 700 !important;
  background: #f1f5f9 !important;
  color: #64748b !important;
  font-variant-numeric: tabular-nums !important;
}
.cl-pipe-step.has-count .pill-sourcing     { background: #eff6ff; color: #2563eb; }
.cl-pipe-step.has-count .pill-screening    { background: #eef2ff; color: #4f46e5; }
.cl-pipe-step.has-count .pill-evaluation   { background: #faf5ff; color: #9333ea; }
.cl-pipe-step.has-count .pill-negotiations { background: #fef3c7; color: #d97706; }
.cl-pipe-step.has-count .pill-offer        { background: #ecfdf5; color: #059669; }
.cl-pipe-step.has-count .pill-joined       { background: #dcfce7; color: #16a34a; }
.cl-pipe-step.has-count .pill-rejected     { background: #fee2e2; color: #dc2626; }
.cl-pipe-step.has-count .pill-interview    { background: #fef3c7; color: #b45309; }

.cl-pipe-label {
  font-size: 9.5px !important;
  color: #94a3b8 !important;
  white-space: nowrap !important;
}
.cl-pipe-divider {
  width: 1px;
  height: 22px;
  background: #e2e8f0;
  margin: 0 2px;
}

/* Bottom Activity Strip */
.cl-card-bottom-strip {
  background: #f8fafc !important;
  border: 1px solid #eef2f6 !important;
  border-radius: 6px !important;
  padding: 7px 12px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  font-size: 11px !important;
  color: #64748b !important;
  margin-top: 2px !important;
}
.cl-bottom-meta {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  flex-wrap: wrap !important;
}
.cl-meta-item {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
}
.cl-meta-dot { color: #cbd5e1; }
.cl-apps-badge {
  background: #e2e8f0;
  color: #475569;
  font-size: 10px;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 8px;
}
.cl-apps-badge.has-apps {
  background: #eff6ff;
  color: #2563eb;
}
.cl-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  display: inline-block;
}

/* Empty State */
.cl-empty-state {
  text-align: center !important;
  padding: 48px 24px !important;
  background: #ffffff !important;
  border: 1px dashed #cbd5e1 !important;
  border-radius: 12px !important;
  margin: 16px 0 !important;
}
.cl-empty-icon {
  width: 64px !important;
  height: 64px !important;
  border-radius: 50% !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin: 0 auto 12px !important;
}
.cl-empty-title {
  font-size: 15px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin-bottom: 4px !important;
}
.cl-empty-desc {
  font-size: 12px !important;
  color: #64748b !important;
}

/* ── 15.6" Laptop Responsiveness (1366x768 & 1920x1080) — Locked 6 Cards on 1 Row ── */
@media (max-width: 1440px) {
  .cl-stat-grid {
    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    gap: 6px !important;
  }
  .cl-card {
    padding: 8px 7px !important;
  }
  .cl-card-top {
    gap: 6px !important;
    margin-bottom: 2px !important;
  }
  .cl-card-icon {
    width: 28px !important;
    height: 28px !important;
    min-width: 28px !important;
    border-radius: 6px !important;
  }
  .cl-card-icon svg {
    width: 14px !important;
    height: 14px !important;
  }
  .cl-card-num {
    font-size: 16px !important;
  }
  .cl-card-lbl {
    font-size: 10px !important;
  }
  .cl-card-sub {
    font-size: 9px !important;
    gap: 3px !important;
    margin-top: 4px !important;
    padding-top: 3px !important;
  }
  .cl-card-sub svg {
    width: 10px !important;
    height: 10px !important;
  }
  .cl-subcards-grid, .tbl-h {
    grid-template-columns: 2.2fr 1.3fr 1fr 2.5fr !important;
    gap: 10px !important;
  }
}

@media (max-width: 1150px) {
  .cl-stat-grid {
    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    gap: 5px !important;
  }
  .cl-card {
    padding: 6px 5px !important;
    border-radius: 8px !important;
  }
  .cl-card-top {
    gap: 5px !important;
    margin-bottom: 2px !important;
  }
  .cl-card-icon {
    width: 24px !important;
    height: 24px !important;
    min-width: 24px !important;
    border-radius: 5px !important;
  }
  .cl-card-icon svg {
    width: 12px !important;
    height: 12px !important;
  }
  .cl-card-num {
    font-size: 14.5px !important;
  }
  .cl-card-lbl {
    font-size: 9px !important;
  }
  .cl-card-sub {
    font-size: 8.5px !important;
    gap: 2px !important;
    margin-top: 3px !important;
    padding-top: 2px !important;
  }
  .cl-card-sub svg {
    width: 9px !important;
    height: 9px !important;
  }
}

@media (max-width: 768px) {
  .cl-stat-grid {
    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    gap: 4px !important;
  }
  .cl-card {
    padding: 5px 4px !important;
  }
  .cl-card-icon {
    width: 20px !important;
    height: 20px !important;
    min-width: 20px !important;
  }
  .cl-card-num {
    font-size: 13px !important;
  }
  .cl-card-lbl {
    font-size: 8px !important;
  }
  .cl-card-sub {
    font-size: 7.5px !important;
  }
  .cl-subcards-grid, .tbl-h {
    grid-template-columns: 1fr !important;
  }
  .cl-header {
    flex-direction: column !important;
    align-items: flex-start !important;
  }
}
</style>
@endpush

@section('content')
@php
    $totalAgenciesCount = (int)($spartnerDataCnt['spartnerAll'] ?? 0);
    $newlyAddedCount = (int)($spartnerDataCnt['newlyAdded'] ?? 0);
    $topPerformingCount = (int)($spartnerDataCnt['topPerforming'] ?? 0);
    $activeAgenciesCount = (int)($spartnerDataCnt['workingOnJobs'] ?? 0);
    $inactiveAgenciesCount = (int)max(0, $totalAgenciesCount - $activeAgenciesCount);
    $pendingRequestsCount = (int)($spartnerDataCnt['sentRequest'] ?? 0);
@endphp

<script>
    // ── Authoritative Single Source of Truth for Agency Tab Counters ──
    window.AGENCY_DATA_COUNTERS = {
        spartnerAll: {{ (int)($totalAgenciesCount ?? 0) }},
        spartnerNewlyAdded: {{ (int)($newlyAddedCount ?? 0) }},
        spartnerTopPerforming: {{ (int)($topPerformingCount ?? 0) }},
        spartnerHiring: {{ (int)($activeAgenciesCount ?? 0) }},
        spartnerInactive: {{ (int)($inactiveAgenciesCount ?? 0) }},
        spartnerRequest: {{ (int)($pendingRequestsCount ?? 0) }}
    };

    function updateAgencyTabCounters(counts) {
        if (!counts || typeof counts !== 'object') return;

        var all = counts.spartnerAll !== undefined ? Number(counts.spartnerAll) : null;
        var newlyAdded = counts.spartnerNewlyAdded !== undefined ? Number(counts.spartnerNewlyAdded) : null;
        var topPerforming = counts.spartnerTopPerforming !== undefined ? Number(counts.spartnerTopPerforming) : null;
        var hiring = counts.spartnerHiring !== undefined ? Number(counts.spartnerHiring) : null;
        var inactive = counts.spartnerInactive !== undefined ? Number(counts.spartnerInactive) : null;
        var request = counts.spartnerRequest !== undefined ? Number(counts.spartnerRequest) : null;

        if (all !== null && !isNaN(all)) {
            $('#span_spartnerAll').text(all);
            window.AGENCY_DATA_COUNTERS.spartnerAll = all;
        }
        if (newlyAdded !== null && !isNaN(newlyAdded)) {
            $('#span_spartnerNewlyAdded').text(newlyAdded);
            window.AGENCY_DATA_COUNTERS.spartnerNewlyAdded = newlyAdded;
        }
        if (topPerforming !== null && !isNaN(topPerforming)) {
            $('#span_spartnerTopPerforming').text(topPerforming);
            window.AGENCY_DATA_COUNTERS.spartnerTopPerforming = topPerforming;
        }
        if (hiring !== null && !isNaN(hiring)) {
            $('#span_spartnerHiring').text(hiring);
            window.AGENCY_DATA_COUNTERS.spartnerHiring = hiring;
        }
        if (inactive !== null && !isNaN(inactive)) {
            $('#span_spartnerInactive').text(inactive);
            window.AGENCY_DATA_COUNTERS.spartnerInactive = inactive;
        }
        if (request !== null && !isNaN(request)) {
            $('#span_spartnerRequest').text(request);
            window.AGENCY_DATA_COUNTERS.spartnerRequest = request;
        }
    }
    window.updateAgencyTabCounters = updateAgencyTabCounters;
</script>

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

        <!-- ── TIER 1: TOP HEADER TOOLBAR CARD ── -->
        <div class="cl-header cl-header-card">
            <div class="cl-header-left">
                <div class="cl-header-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="cl-header-title-row">
                        <h1 class="cl-header-title">Agencies Tracker</h1>
                        <span class="cl-verified-badge" title="Verified ATS Partner Module">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="m9 12 2 2 4-4" stroke-width="2.5"/>
                            </svg>
                        </span>
                    </div>
                    <div class="cl-header-subtitle">Manage recruitment agency partners, shared jobs, candidate submissions, and placement performance.</div>
                </div>
            </div>

            <div class="cl-header-actions">
                <div class="cl-export-wrapper dropdown">
                    <button type="button" class="cl-export-btn" id="clExportDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Export agency portfolio">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        <span id="clExportBtnLabel">Export</span>
                        <svg class="cl-export-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm cl-export-dropdown-menu" aria-labelledby="clExportDropdownBtn">
                        <li>
                            <a class="dropdown-item cl-export-option" href="javascript:void(0);" onclick="triggerAgencyExport('filtered_csv')">
                                <div class="cl-export-opt-icon csv">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <path d="M8 13h2"/><path d="M8 17h8"/><path d="M14 13h2"/>
                                    </svg>
                                </div>
                                <div class="cl-export-opt-text">
                                    <span class="cl-export-opt-title">Export Filtered View (CSV)</span>
                                    <span class="cl-export-opt-sub">Current filtered agencies only</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item cl-export-option" href="javascript:void(0);" onclick="triggerAgencyExport('all_csv')">
                                <div class="cl-export-opt-icon all">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                                        <path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>
                                    </svg>
                                </div>
                                <div class="cl-export-opt-text">
                                    <span class="cl-export-opt-title">Export All Agencies (CSV)</span>
                                    <span class="cl-export-opt-sub">Full database snapshot</span>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1 border-gray-200"></li>
                        <li>
                            <a class="dropdown-item cl-export-option" href="javascript:void(0);" onclick="triggerAgencyExport('executive_pdf')">
                                <div class="cl-export-opt-icon pdf">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9V2h12v7"/>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                        <rect x="6" y="14" width="12" height="8"/>
                                    </svg>
                                </div>
                                <div class="cl-export-opt-text">
                                    <span class="cl-export-opt-title">Save / Print Report (PDF)</span>
                                    <span class="cl-export-opt-sub">Executive landscape summary</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                @if(in_array("add_staffing_partners", $userPermissions))
                <button type="button" class="cl-add-btn-primary" id="open_add_agency_drawer">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Add / Invite Agency</span>
                </button>
                @endif
            </div>
        </div>

        <!-- ── 6 STAT CARDS FILTER GRID ── -->
        <div class="cl-stat-grid">
            {{-- Card 1: Total Agencies --}}
            <a href="#" onclick="setAgencyTab('all', this); return false;" class="cl-card active" data-tab="all">
                <div class="cl-card-top">
                    <div class="cl-card-icon blue">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_spartnerAll">{{ $totalAgenciesCount }}</div>
                        <div class="cl-card-lbl">Total Agencies</div>
                    </div>
                </div>
                <div class="cl-card-sub green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="m18 15-6-6-6 6"/></svg>
                    <span>+{{ max($totalAgenciesCount, 1) }} registered</span>
                </div>
            </a>

            {{-- Card 2: Newly Added Agencies --}}
            <a href="#" onclick="setAgencyTab('newly_added', this); return false;" class="cl-card" data-tab="newly_added">
                <div class="cl-card-top">
                    <div class="cl-card-icon indigo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="19" y1="8" x2="19" y2="14"/>
                            <line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_spartnerNewlyAdded">{{ $newlyAddedCount }}</div>
                        <div class="cl-card-lbl">Newly Added</div>
                    </div>
                </div>
                <div class="cl-card-sub blue">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="m18 15-6-6-6 6"/></svg>
                    <span>Upto 180 days (max 10)</span>
                </div>
            </a>

            {{-- Card 3: Top Performing Agencies --}}
            <a href="#" onclick="setAgencyTab('top_performing', this); return false;" class="cl-card" data-tab="top_performing">
                <div class="cl-card-top">
                    <div class="cl-card-icon green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_spartnerTopPerforming">{{ $topPerformingCount }}</div>
                        <div class="cl-card-lbl">Top Performing</div>
                    </div>
                </div>
                <div class="cl-card-sub green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                    </svg>
                    <span>High placement score</span>
                </div>
            </a>

            {{-- Card 4: Active Agencies --}}
            <a href="#" onclick="setAgencyTab('hiring', this); return false;" class="cl-card" data-tab="hiring">
                <div class="cl-card-top">
                    <div class="cl-card-icon green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="12.01"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_spartnerHiring">{{ $activeAgenciesCount }}</div>
                        <div class="cl-card-lbl">Active Agencies</div>
                    </div>
                </div>
                <div class="cl-card-sub green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="m18 15-6-6-6 6"/></svg>
                    <span>Working on jobs</span>
                </div>
            </a>

            {{-- Card 5: Inactive Agencies --}}
            <a href="#" onclick="setAgencyTab('inactive', this); return false;" class="cl-card" data-tab="inactive">
                <div class="cl-card-top">
                    <div class="cl-card-icon pink">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_spartnerInactive">{{ $inactiveAgenciesCount }}</div>
                        <div class="cl-card-lbl">Inactive Agencies</div>
                    </div>
                </div>
                <div class="cl-card-sub pink">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    <span>0 active submissions</span>
                </div>
            </a>

            {{-- Card 6: Pending Invitations --}}
            <a href="#" onclick="setAgencyTab('request', this); return false;" class="cl-card" data-tab="request">
                <div class="cl-card-top">
                    <div class="cl-card-icon purple">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_spartnerRequest">{{ $pendingRequestsCount }}</div>
                        <div class="cl-card-lbl">Pending Invites</div>
                    </div>
                </div>
                <div class="cl-card-sub purple">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                    <span>{{ $pendingRequestsCount > 0 ? $pendingRequestsCount . ' action needed' : 'All confirmed' }}</span>
                </div>
            </a>
        </div>

        <!-- ── SEARCH & FILTER BAR ── -->
        <div class="cl-filter-bar">
            {{-- Search Input (Enterprise Tier 1 Micro-Search Bar) --}}
            <div class="cl-search-box" id="clSearchBoxWrapper">
                <svg class="cl-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input 
                    type="text" 
                    class="cl-search-inp" 
                    name="inputJobSearch" 
                    value="" 
                    placeholder="Search agencies by name, specialization, location, representative, ID..." 
                    id="kt_tagify_2" 
                    autocomplete="off" 
                    spellcheck="false" 
                    aria-label="Search agency listing" 
                    role="searchbox"
                />
                <div class="cl-search-right-affix">
                    <span class="cl-search-count-pill" id="clSearchMatchCount" style="display:none;" title="Matching agencies count">0 matches</span>
                    <button type="button" class="cl-search-clear-btn" id="clSearchClearBtn" title="Clear search (Esc)" aria-label="Clear search" style="display:none;" onclick="clearAgencySearchInput(event)">✕</button>
                    <kbd class="cl-search-kbd-hint" id="clSearchKbdHint" title="Press Ctrl+K to search">Ctrl+K</kbd>
                </div>
                <div id="loader_search" class="loader_search hidden_search" style="display:none;position:absolute;right:12px;top:50%;transform:translateY(-50%);"></div>
            </div>

            {{-- Filter 1: Location Combobox Popover --}}
            <div class="cl-loc-dropdown-wrapper" id="clLocDropdownWrapper">
                <div class="cl-loc-combobox-btn" id="clLocBtn" onclick="toggleLocationDropdown(event)">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="cl-pill-texts">
                        <span class="cl-pill-lbl">Location</span>
                        <span class="cl-pill-val" id="locationDisplay">All Locations</span>
                    </div>
                    <div class="cl-pill-chevron">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

                <div class="cl-loc-dropdown-panel" id="clLocDropdownPanel">
                    <div class="cl-loc-search-box">
                        <input type="text" class="cl-search-inp" style="height:32px; padding:0 10px;" id="clLocSearchInp" placeholder="Search countries or cities..." autocomplete="off" oninput="handleLocationSearchInput(this.value)" />
                    </div>
                    <div class="cl-loc-dropdown-body mt-2" id="clLocDropdownBody" style="max-height: 220px; overflow-y: auto;">
                        <div class="cl-act-item selected" onclick="selectLocationOption('', 'All Locations', event)">
                            <span>All Locations</span>
                        </div>
                        @if ($countries)
                            @foreach ($countries as $c)
                                <div class="cl-act-item" onclick="selectLocationOption('{{ $c->id }}', '{{ $c->country_name }}', event)">
                                    <span>{{ $c->country_name }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <input type="hidden" id="country" name="country" value="" />
            </div>

            {{-- Filter 2: Performance Combobox Popover --}}
            <div class="cl-pipe-dropdown-wrapper" id="clPerfDropdownWrapper">
                <button type="button" class="cl-pipe-combobox-btn" id="clPerfBtn" onclick="togglePerfDropdown(event)" title="Filter by Agency Performance">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="cl-pill-texts">
                        <span class="cl-pill-lbl">Performance</span>
                        <span class="cl-pill-val" id="perfDisplay">All Performance</span>
                    </div>
                    <div class="cl-pill-chevron">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>

                <div class="cl-pipe-dropdown-panel" id="clPerfDropdownPanel">
                    <div class="cl-pipe-section-hdr">Performance Tier</div>
                    <div class="cl-pipe-item selected" onclick="selectPerfOption('', 'All Performance', event)">
                        <span>All Performance</span>
                    </div>
                    <div class="cl-pipe-item" onclick="selectPerfOption('high', 'High Performers (5+ Placements)', event)">
                        <span>High Performers</span>
                    </div>
                    <div class="cl-pipe-item" onclick="selectPerfOption('medium', 'Active (1–4 Placements)', event)">
                        <span>Medium Activity</span>
                    </div>
                    <div class="cl-pipe-item" onclick="selectPerfOption('low', 'Getting Started (0 Placements)', event)">
                        <span>Low / New</span>
                    </div>
                </div>
                <input type="hidden" id="agency_performance" name="agency_performance" value="" />
            </div>

            {{-- Filter 3: Sort Combobox Popover --}}
            <div class="cl-sort-dropdown-wrapper" id="clSortDropdownWrapper">
                <button type="button" class="cl-sort-combobox-btn" id="clSortBtn" onclick="toggleSortDropdown(event)" title="Sort Agency Listing">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="4" x2="7" y2="20"/><polyline points="3 8 7 4 11 8"/><line x1="17" y1="4" x2="17" y2="20"/><polyline points="13 16 17 20 21 16"/>
                        </svg>
                    </div>
                    <div class="cl-pill-texts">
                        <span class="cl-pill-lbl">Sort</span>
                        <span class="cl-pill-val" id="sortDisplay">Recently Added</span>
                    </div>
                    <div class="cl-pill-chevron">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>

                <div class="cl-sort-dropdown-panel" id="clSortDropdownPanel">
                    <div class="cl-sort-section-hdr">Chronological</div>
                    <div class="cl-sort-item selected" onclick="selectSortOption('recent', 'Recently Added', event)">
                        <span>Recently Added</span>
                    </div>
                    <div class="cl-sort-item" onclick="selectSortOption('oldest', 'Oldest First', event)">
                        <span>Oldest First</span>
                    </div>
                    <div class="cl-sort-section-hdr">Alphabetical</div>
                    <div class="cl-sort-item" onclick="selectSortOption('name_asc', 'Agency Name (A → Z)', event)">
                        <span>Agency Name (A → Z)</span>
                    </div>
                    <div class="cl-sort-item" onclick="selectSortOption('name_desc', 'Agency Name (Z → A)', event)">
                        <span>Agency Name (Z → A)</span>
                    </div>
                    <div class="cl-sort-section-hdr">Activity</div>
                    <div class="cl-sort-item" onclick="selectSortOption('jobs_desc', 'Most Shared Jobs', event)">
                        <span>Most Shared Jobs</span>
                    </div>
                </div>
                <input type="hidden" id="sortingfilter" name="sortingfilter" value="" />
            </div>
        </div>

        @if(in_array("view_staffing_partners", $userPermissions))
        <!-- ── TABLE / CARD STREAM ── -->
        <div class="tbl">
            <div class="tbl-w">
                <div class="tbl-scroll-container">
                    <div class="tbl-h" id="agencyTableHead">
                        <div class="tbl-th-subcard">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                <span>Agency Identity</span>
                            </div>
                        </div>
                        <div class="tbl-th-subcard">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>Location</span>
                            </div>
                        </div>
                        <div class="tbl-th-subcard">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                <span>Shared Jobs</span>
                            </div>
                        </div>
                        <div class="tbl-th-subcard">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                                <span>Candidate Pipeline</span>
                            </div>
                        </div>
                    </div>

                    <div class="loadingMessage loadingMessagestaffingpartnerajax" style="display:none; position:absolute; left:50%; top:50%; transform:translate(-50%, -50%); z-index:10; background:rgba(255,255,255,0.9); padding:10px 20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">Processing...</div>
                    <div id="staffingpartnerajax">{!! $initialHtml ?? '' !!}</div>
                </div>

                <!-- ── PAGINATION BAR ── -->
                <div id="paginationstaffingpartner" class="mt-3">{!! $initialPagination ?? '' !!}</div>
            </div>
        </div>
        @endif

    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->

<!-- ── DRAWER FOR AGENCY DETAILS ── -->
<div id="kt_activitiesDirectClient_toggle"></div>
<div id="kt_activitiesDirectClient" class="bg-body cd-activities-drawer" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close" data-kt-drawer-width="{default:'90%', 'sm': '85%', 'md': '80%', 'lg': '80%', 'xl': '80%'}">
  <div class="card shadow-none rounded-0 w-100">
    <button type="button" id="kt_activitiesDirectClient_close" class="d-none" aria-hidden="true"></button>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesDirectClient_body"> &nbsp; </div>
  </div>
</div>

<!-- ── DRAWER FOR ADD / INVITE AGENCY ── -->
<div class="add-agency-drawer-overlay" id="add_agency_drawer_overlay" aria-hidden="true" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:1049;"></div>
<div class="add-agency-drawer" id="add_agency_drawer" role="dialog" aria-modal="true" aria-labelledby="add_agency_drawer_title" aria-hidden="true" style="display:none; position:fixed; top:0; right:0; bottom:0; width:460px; max-width:90%; background:#ffffff; box-shadow:-5px 0 25px rgba(0,0,0,0.15); z-index:1050; padding:24px; overflow-y:auto; flex-direction:column; justify-content:space-between;">
    <div>
        <div class="d-flex align-items-center justify-content-between pb-4 border-bottom mb-4">
            <h2 class="fs-5 fw-bolder text-dark m-0" id="add_agency_drawer_title">Invite Agency or Consultant</h2>
            <button type="button" class="btn btn-sm btn-icon btn-light add_staffing_close" id="add_agency_drawer_close_btn" aria-label="Close">✕</button>
        </div>
        <form id="add_staffing_partners_form" name="add_staffing_partners_form" action="#" onsubmit="return false;">
            <div class="mb-4">
                <label for="staffing_partners_email" class="fw-bold fs-7 mb-2 text-dark d-block">Invite Agency by Email Address</label>
                <div class="input-group">
                    <input type="email" name="staffing_partners_email" id="staffing_partners_email" class="form-control form-control-solid" placeholder="Enter agency email address" autocomplete="off">
                    <button type="button" class="btn btn-primary fw-bold" id="submit_staffing_partners">Invite</button>
                </div>
            </div>
            @php
                $encode = base64_encode(auth()->user()->company_id);
                $Url = url('/company-registration-invitation/'.$encode);
            @endphp
            <div class="text-center my-3 text-muted fw-bold fs-8">── OR ──</div>
            <div class="mb-4">
                <label for="kt_share_earn_link_input" class="fw-bold fs-7 mb-2 text-dark d-block">Share Agency Registration Link</label>
                <div class="input-group">
                    <input id="kt_share_earn_link_input" type="text" class="form-control form-control-solid" value="{{ $Url }}" readonly>
                    <button type="button" id="kt_share_earn_link_copy_button" class="btn btn-light fw-bold" data-clipboard-target="#kt_share_earn_link_input">Copy</button>
                </div>
            </div>
        </form>
    </div>
    <div class="pt-4 border-top">
        <button type="button" class="btn btn-light w-100 add_staffing_close" data-dismiss="modal">Cancel</button>
    </div>
</div>

<!-- ── PRINT-TO-PDF EXECUTIVE CANVAS ── -->
<div id="cl-print-report-canvas" class="d-none" aria-hidden="true">
    <div class="p-4 bg-white">
        <h1 class="fs-4 fw-bold">Agency Portfolio & Recruitment Performance Report</h1>
        <div class="text-muted fs-7">{{ auth()->user()->company->company_name ?? 'JERO Enterprise ATS' }} • Confidential Review</div>
    </div>
</div>

@push('footer-script')
<script>
    // ── Global Filter & Action Callbacks (Strict Rule 17 Isolation) ──
    window.setAgencyTab = function(tabKey, el) {
        $('.cl-stat-grid .cl-card').removeClass('active');
        $(el).addClass('active');
        if (window.AgencyListingManager) {
            window.AgencyListingManager.activeTab = tabKey;
            if (tabKey === 'newly_added') {
                window.AgencyListingManager.perfFilter = '';
            } else if (tabKey === 'top_performing') {
                window.AgencyListingManager.perfFilter = 'high';
            } else if (tabKey === 'hiring') {
                window.AgencyListingManager.perfFilter = 'high';
            } else if (tabKey === 'inactive') {
                window.AgencyListingManager.perfFilter = 'low';
            } else {
                window.AgencyListingManager.perfFilter = '';
            }
            window.AgencyListingManager.loadPage(1);
        }
    };

    window.clearAgencySearchInput = function(e) {
        if (e) e.stopPropagation();
        $('#kt_tagify_2').val('');
        $('#clSearchClearBtn, #clSearchMatchCount').hide();
        if (window.AgencyListingManager) {
            window.AgencyListingManager.searchQuery = '';
            window.AgencyListingManager.loadPage(1);
        }
    };

    window.toggleLocationDropdown = function(e) {
        if (e) e.stopPropagation();
        const $panel = $('#clLocDropdownPanel');
        const isShown = $panel.hasClass('show');
        if (window.AgencyListingManager) window.AgencyListingManager.closeAllDropdowns();
        if (!isShown) $panel.addClass('show');
    };

    window.selectLocationOption = function(id, name, e) {
        if (e) e.stopPropagation();
        $('#locationDisplay').text(name);
        $('#country').val(id);
        if (window.AgencyListingManager) {
            window.AgencyListingManager.countryFilter = id;
            window.AgencyListingManager.closeAllDropdowns();
            window.AgencyListingManager.loadPage(1);
        }
    };

    window.handleLocationSearchInput = function(e) {
        const val = (e.target.value || '').toLowerCase();
        $('#clLocList .cl-loc-item').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(val) > -1);
        });
    };

    window.togglePerfDropdown = function(e) {
        if (e) e.stopPropagation();
        const $panel = $('#clPerfDropdownPanel');
        const isShown = $panel.hasClass('show');
        if (window.AgencyListingManager) window.AgencyListingManager.closeAllDropdowns();
        if (!isShown) $panel.addClass('show');
    };

    window.selectPerfOption = function(val, label, e) {
        if (e) e.stopPropagation();
        $('#perfDisplay').text(label);
        $('#agency_performance').val(val);
        if (window.AgencyListingManager) {
            window.AgencyListingManager.perfFilter = val;
            window.AgencyListingManager.closeAllDropdowns();
            window.AgencyListingManager.loadPage(1);
        }
    };

    window.toggleSortDropdown = function(e) {
        if (e) e.stopPropagation();
        const $panel = $('#clSortDropdownPanel');
        const isShown = $panel.hasClass('show');
        if (window.AgencyListingManager) window.AgencyListingManager.closeAllDropdowns();
        if (!isShown) $panel.addClass('show');
    };

    window.selectSortOption = function(val, label, e) {
        if (e) e.stopPropagation();
        $('#sortDisplay').text(label);
        $('#sortingfilter').val(val);
        if (window.AgencyListingManager) {
            window.AgencyListingManager.sortFilter = val;
            window.AgencyListingManager.closeAllDropdowns();
            window.AgencyListingManager.loadPage(1);
        }
    };

    window.triggerAgencyExport = function(type) {
        if (type === 'filtered_csv') {
            const q = (window.AgencyListingManager && window.AgencyListingManager.searchQuery) ? window.AgencyListingManager.searchQuery : '';
            window.location.href = `{{ url('service_company/consultancy/export/0/1000') }}?search=${encodeURIComponent(q)}`;
        } else if (type === 'all_csv') {
            window.location.href = `{{ url('service_company/consultancy/export/0/10000') }}`;
        } else if (type === 'executive_pdf') {
            window.print();
        }
    };

    window.openAgencyDrawer = function(id) {
        if (!id) return;
        $('[data-row-id]').closest('.tbl-r').removeClass('client-selected');
        $(`[data-row-id="${id}"]`).closest('.tbl-r').addClass('client-selected');

        var drawerEl = document.getElementById('kt_activitiesDirectClient');
        if (drawerEl) {
            var opened = false;
            if (typeof KTDrawer !== 'undefined') {
                var drawer = (typeof KTDrawer.getInstance === 'function') ? KTDrawer.getInstance(drawerEl) : null;
                if (!drawer && typeof KTDrawer === 'function') {
                    try { drawer = new KTDrawer(drawerEl); } catch (e) {}
                }
                if (!drawer && typeof KTDrawer.createInstances === 'function') {
                    try {
                        KTDrawer.createInstances();
                        drawer = KTDrawer.getInstance(drawerEl);
                    } catch (e) {}
                }
                if (drawer && typeof drawer.show === 'function') {
                    drawer.show();
                    opened = true;
                }
            }
            if (!opened) {
                var toggleEl = document.getElementById('kt_activitiesDirectClient_toggle');
                if (toggleEl) {
                    if (typeof toggleEl.click === 'function') {
                        toggleEl.click();
                    } else {
                        $(toggleEl).trigger('click');
                    }
                }
            }
            if (!drawerEl.classList.contains('drawer')) {
                drawerEl.classList.add('drawer', 'drawer-end');
            }
            drawerEl.classList.add('drawer-on');
            drawerEl.style.transform = 'none';
            drawerEl.style.zIndex = '999999';

            var existingOverlay = document.querySelector('.drawer-overlay');
            if (!existingOverlay) {
                var overlay = document.createElement('div');
                overlay.className = 'drawer-overlay agency-drawer-overlay';
                overlay.style.zIndex = '999998';
                overlay.onclick = function() {
                    window.closeAgencyDrawer();
                };
                document.body.appendChild(overlay);
            }
        }

        $('#kt_activitiesDirectClient_body').html('<div class="d-flex align-items-center justify-content-center p-10" style="min-height:300px;"><div class="spinner-border text-primary" role="status" style="width:3rem; height:3rem;"></div></div>');

        let url = "{{ route('service_company.consultancy.dviewmore', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.view) {
                    $('#kt_activitiesDirectClient_body').html(response.view);
                } else if (response.view) {
                    $('#kt_activitiesDirectClient_body').html(response.view);
                } else if (typeof response === 'string') {
                    $('#kt_activitiesDirectClient_body').html(response);
                }
            },
            error: function(xhr) {
                $('#kt_activitiesDirectClient_body').html('<div class="alert alert-danger m-5">Failed to load agency details. Please try again.</div>');
            }
        });
    };

    window.closeAgencyDrawer = function() {
        var drawerEl = document.getElementById('kt_activitiesDirectClient');
        if (drawerEl) {
            if (typeof KTDrawer !== 'undefined' && typeof KTDrawer.getInstance === 'function') {
                var inst = KTDrawer.getInstance(drawerEl);
                if (inst && typeof inst.hide === 'function') {
                    inst.hide();
                }
            }
            drawerEl.classList.remove('drawer-on');
            drawerEl.style.removeProperty('transform');
            drawerEl.style.removeProperty('z-index');
        }
        document.querySelectorAll('.drawer-overlay.agency-drawer-overlay, .drawer-overlay').forEach(function(ov) {
            ov.remove();
        });
        document.body.removeAttribute('data-kt-drawer-activities');
        document.body.removeAttribute('data-kt-drawer');
        document.body.style.overflow = '';
    };

    // ── Agency Listing Manager Object ──
    const AgencyListingManager = {
        currentPage: 1,
        activeTab: 'all',
        searchQuery: '',
        countryFilter: '',
        perfFilter: '',
        sortFilter: '',
        searchDebounceTimer: null,

        init: function() {
            this.bindEvents();
            // If initialHtml is empty, fetch page 1
            if (!$('#staffingpartnerajax').children().length) {
                this.loadPage(1);
            }
        },

        bindEvents: function() {
            const self = this;

            // Debounced Search (300ms)
            $('#kt_tagify_2').on('input', function() {
                const val = $(this).val();
                self.searchQuery = val;
                if (val.length > 0) {
                    $('#clSearchClearBtn').show();
                } else {
                    $('#clSearchClearBtn').hide();
                }
                clearTimeout(self.searchDebounceTimer);
                self.searchDebounceTimer = setTimeout(function() {
                    self.loadPage(1);
                }, 300);
            });

            // Keyboard Shortcut (Ctrl+K)
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    $('#kt_tagify_2').focus();
                }
                if (e.key === 'Escape') {
                    self.closeAllDropdowns();
                    window.closeAgencyDrawer();
                }
            });

            // Row-Level and Button Delegation to Open Drawer
            $(document).on('click', '.tbl-r, .show-detail-direct', function(e) {
                if ($(e.target).closest('input, button, a, .tbl-cb, .tbl-cell-check, .dropdown-menu').length > 0) {
                    return;
                }
                const id = $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id') || $(this).find('[data-row-id]').first().data('row-id');
                if (id) {
                    window.openAgencyDrawer(id);
                }
            });

            // Drawer Close Handlers
            $(document).on('click', '#kt_activitiesDirectClient_close, #kt_activitiesDirectClient_close_btn, .cd-close-drawer', function(e) {
                if (e) e.preventDefault();
                window.closeAgencyDrawer();
            });

            // Add Agency Drawer Open/Close
            $('#open_add_agency_drawer').on('click', function(e) {
                if (e) e.preventDefault();
                $('#add_agency_drawer_overlay, #add_agency_drawer').fadeIn(150);
            });
            $(document).on('click', '.add_staffing_close, #add_agency_drawer_overlay, #add_agency_drawer_close_btn', function(e) {
                if (e) e.preventDefault();
                $('#add_agency_drawer_overlay, #add_agency_drawer').fadeOut(150);
            });

            // Submit Agency Invitation via Email
            $(document).on('click', '#submit_staffing_partners', function(e) {
                if (e) e.preventDefault();
                const email = $('#staffing_partners_email').val().trim();
                if (!email) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'warning', title: 'Email required', text: 'Please enter an agency email address.' });
                    } else if (typeof swal !== 'undefined') {
                        swal({ title: 'Warning!', text: 'Please enter an agency email address.', type: 'warning' });
                    } else {
                        alert('Please enter an agency email address.');
                    }
                    return;
                }
                const url = "{{ route('service_company.consultancy.staffing_invitation', 0) }}";
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: { staffing_partners_email: email },
                    success: function(response) {
                        $('#staffing_partners_email').val('');
                        if (response.status === 'warning') {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'warning', title: 'Warning', text: response.message });
                            } else if (typeof swal !== 'undefined') {
                                swal({ title: 'Warning!', text: response.message, type: 'warning' });
                            } else {
                                alert(response.message);
                            }
                        } else if (response.status === 'success') {
                            $('#add_agency_drawer_overlay, #add_agency_drawer').fadeOut(150);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'success', title: 'Invited!', text: response.message || 'Invitation sent successfully.' });
                            } else if (typeof swal !== 'undefined') {
                                swal({ title: 'Success!', text: response.message || 'Invitation sent successfully.', type: 'success' });
                            } else {
                                alert('Invitation sent successfully.');
                            }
                            self.loadPage(1);
                        }
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to send invitation.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        } else {
                            alert(msg);
                        }
                    }
                });
            });

            // Copy Link
            $('#kt_share_earn_link_copy_button').on('click', function() {
                const copyText = document.getElementById("kt_share_earn_link_input");
                if (copyText) {
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    navigator.clipboard.writeText(copyText.value);
                    $(this).text('Copied!');
                    const btn = $(this);
                    setTimeout(function() { btn.text('Copy'); }, 2000);
                }
            });

            // Outside click closes popovers
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.cl-loc-dropdown-wrapper, .cl-pipe-dropdown-wrapper, .cl-sort-dropdown-wrapper').length) {
                    self.closeAllDropdowns();
                }
            });
        },

        closeAllDropdowns: function() {
            $('.cl-loc-dropdown-panel, .cl-pipe-dropdown-panel, .cl-sort-dropdown-panel').removeClass('show');
        },

        loadPage: function(page) {
            this.currentPage = page || 1;
            const self = this;

            $('.loadingMessagestaffingpartnerajax').css({ display: 'block' });

            $.ajax({
                url: `{{ route('service_company.consultancy.data_ajax') }}?page=${this.currentPage}`,
                type: 'GET',
                data: {
                    tab: self.activeTab,
                    search: self.searchQuery,
                    cus_search: self.searchQuery,
                    country: self.countryFilter,
                    agency_performance: self.perfFilter,
                    sortingfilter: self.sortFilter,
                    per_page: 10
                },
                success: function(response) {
                    $('.loadingMessagestaffingpartnerajax').css({ display: 'none' });
                    $('#staffingpartnerajax').html(response.html);
                    $('#paginationstaffingpartner').html(response.pagination);
                    if (response.total !== undefined) {
                        $('#span_spartnerAll').text(response.total);
                        if (self.searchQuery) {
                            $('#clSearchMatchCount').text(`${response.total} matches`).show();
                        } else {
                            $('#clSearchMatchCount').hide();
                        }
                    }
                },
                error: function() {
                    $('.loadingMessagestaffingpartnerajax').css({ display: 'none' });
                }
            });
        }
    };

    window.AgencyListingManager = AgencyListingManager;

    // Pagination Click Handler
    $(document).on('click', '#paginationstaffingpartner a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            const pageMatch = url.match(/page=(\d+)/);
            const page = pageMatch ? pageMatch[1] : 1;
            AgencyListingManager.loadPage(page);
        }
    });

    $(document).ready(function() {
        AgencyListingManager.init();
    });
</script>
@endpush
@endsection
