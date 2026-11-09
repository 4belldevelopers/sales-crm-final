@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/clientList.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
<style>
/* ── Guaranteed Card-Based Header Design (Rule 20 Enterprise Standard) ── */
.kt_content_agency,
#kt_content.kt_content_agency {
  margin-top: -65px !important;
  padding-top: 12px !important;
}
@media (max-width: 991.98px) {
  .kt_content_agency,
  #kt_content.kt_content_agency {
    margin-top: -55px !important;
    padding-top: 10px !important;
  }
}
.cd-activities-drawer {
  width: 80% !important;
  max-width: 80vw !important;
  min-width: 0 !important;
  height: 100vh !important;
  overflow-y: auto !important;
}
#kt_activitiesDirectClient_body {
  min-height: 100vh !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
}
@media (max-width: 1400px) {
  .cd-activities-drawer {
    width: 80% !important;
    max-width: 80vw !important;
  }
}
@media (max-width: 992px) {
  .cd-activities-drawer {
    width: 85% !important;
    max-width: 85vw !important;
  }
}
@media (max-width: 576px) {
  .cd-activities-drawer {
    width: 90% !important;
    max-width: 90vw !important;
  }
}
.cl-header, .cl-header-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  padding: 10px 18px !important;
  margin-top: 0 !important;
  margin-bottom: 12px !important;
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
.cl-export-btn:hover,
.cl-export-btn.show,
.cl-export-btn.active {
  background: #f8fafc !important;
  border-color: #cbd5e1 !important;
}
.cl-export-dropdown-menu {
  display: none;
  position: absolute !important;
  top: 100% !important;
  right: 0 !important;
  left: auto !important;
  margin-top: 6px !important;
  min-width: 270px !important;
  padding: 6px !important;
  background: #ffffff !important;
  border-radius: 10px !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.08) !important;
  z-index: 1080 !important;
}
.cl-export-dropdown-menu.show {
  display: block !important;
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
.cl-add-btn-primary svg {
  stroke: #ffffff !important;
  color: #ffffff !important;
  fill: none !important;
  flex-shrink: 0 !important;
  display: inline-block !important;
  vertical-align: middle !important;
}
.cl-add-btn-primary:hover {
  background: #0086d4 !important;
  border-color: #0086d4 !important;
  color: #ffffff !important;
}
.cl-add-btn-primary:hover svg {
  stroke: #ffffff !important;
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
.cl-loc-combobox-btn.has-filter,
.cl-ind-combobox-btn.has-filter,
.cl-act-combobox-btn.has-filter,
.cl-pipe-combobox-btn.has-filter,
.cl-sort-combobox-btn.has-filter {
  border-color: #009ef7 !important;
  background: #f0fdf4 !important;
}
.cl-loc-combobox-btn.has-filter .cl-pill-val,
.cl-ind-combobox-btn.has-filter .cl-pill-val,
.cl-act-combobox-btn.has-filter .cl-pill-val,
.cl-pipe-combobox-btn.has-filter .cl-pill-val,
.cl-sort-combobox-btn.has-filter .cl-pill-val {
  color: #009ef7 !important;
}
.cl-pill-clear {
  color: #94a3b8 !important;
  font-size: 11px !important;
  cursor: pointer !important;
  padding: 1px 4px !important;
  border-radius: 50% !important;
  margin-left: 2px !important;
  display: none;
}
.cl-pill-clear:hover {
  background: #e2e8f0 !important;
  color: #0f172a !important;
}
.cl-pill-lbl { font-size: 10px !important; font-weight: 600 !important; color: #94a3b8 !important; text-transform: uppercase !important; }
.cl-pill-val { font-size: 12px !important; font-weight: 600 !important; color: #0f172a !important; }
.cl-pill-icon svg { color: #2563eb; }

/* ── Enterprise Combobox Popovers (Exact Reference Design Standard) ── */
.cl-loc-dropdown-panel {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  width: 310px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06);
  z-index: 1050;
  display: none;
  flex-direction: column;
  overflow: hidden;
  animation: clLocFadeIn 0.15s ease-out;
}
@keyframes clLocFadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
.cl-loc-dropdown-panel.show { display: flex; }
.cl-loc-search-box {
  padding: 10px 12px;
  border-bottom: 1px solid #f1f5f9;
  background: #fafafa;
  position: relative;
}
.cl-loc-search-inp {
  width: 100%;
  height: 34px;
  border: 1px solid #e2e8f0;
  border-radius: 7px;
  padding: 0 28px 0 30px;
  font-size: 12px;
  font-family: inherit;
  color: #0f172a;
  background: #ffffff;
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.cl-loc-search-inp:focus {
  border-color: #f59e0b;
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.15);
}
.cl-loc-search-icon {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}
.cl-loc-search-clear {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 11px;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 50%;
  display: none;
}
.cl-loc-search-clear:hover { color: #0f172a; background: #e2e8f0; }
.cl-loc-dropdown-body {
  max-height: 290px;
  overflow-y: auto;
  padding: 6px 0;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}
.cl-loc-dropdown-body::-webkit-scrollbar { width: 5px; }
.cl-loc-dropdown-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.cl-loc-section-hdr {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94a3b8;
  padding: 6px 14px 4px 14px;
}
.cl-loc-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 7px 14px;
  font-size: 12px;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  transition: background 0.12s ease;
}
.cl-loc-item:hover { background: #f1f5f9; }
.cl-loc-item.selected {
  background: #fffbeb;
  color: #b45309;
  font-weight: 600;
}
.cl-loc-item-left {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
  flex: 1;
}
.cl-loc-item-title {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cl-loc-badge {
  font-size: 10.5px;
  font-weight: 600;
  padding: 1px 7px;
  border-radius: 12px;
  background: #e2e8f0;
  color: #475569;
  margin-left: 6px;
  flex-shrink: 0;
}
.cl-loc-item.selected .cl-loc-badge { background: #fde68a; color: #92400e; }
.cl-loc-expand-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  border-radius: 4px;
  color: #94a3b8;
  cursor: pointer;
  margin-left: 4px;
  transition: transform 0.15s ease, background 0.15s ease;
}
.cl-loc-expand-btn:hover { background: #e2e8f0; color: #334155; }
.cl-loc-expand-btn.expanded svg { transform: rotate(90deg); }
.cl-loc-city-list {
  display: none;
  background: #f8fafc;
  border-left: 2px solid #e2e8f0;
  margin-left: 24px;
  padding: 2px 0;
}
.cl-loc-city-list.show { display: block; }
.cl-loc-city-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 5px 14px 5px 12px;
  font-size: 11.5px;
  font-weight: 500;
  color: #334155;
  cursor: pointer;
  transition: background 0.12s ease;
}
.cl-loc-city-item:hover { background: #f1f5f9; }
.cl-loc-city-item.selected { background: #fef3c7; color: #b45309; font-weight: 600; }
.cl-loc-divider { height: 1px; background: #f1f5f9; margin: 4px 0; }
.cl-loc-empty { padding: 18px 14px; text-align: center; font-size: 11.5px; color: #94a3b8; }

/* Industry Popover */
.cl-ind-dropdown-panel {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  width: 320px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06);
  z-index: 1055;
  display: none;
  flex-direction: column;
  overflow: hidden;
  animation: clIndFadeIn 0.15s ease-out;
}
@keyframes clIndFadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
.cl-ind-dropdown-panel.show { display: flex; }
.cl-ind-search-box {
  padding: 10px 12px;
  border-bottom: 1px solid #f1f5f9;
  background: #fafafa;
  position: relative;
}
.cl-ind-search-inp {
  width: 100%;
  height: 34px;
  border: 1px solid #e2e8f0;
  border-radius: 7px;
  padding: 0 28px 0 30px;
  font-size: 12px;
  font-family: inherit;
  color: #0f172a;
  background: #ffffff;
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.cl-ind-search-inp:focus {
  border-color: #009ef7;
  box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.15);
}
.cl-ind-search-icon {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}
.cl-ind-search-clear {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 11px;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 50%;
  display: none;
}
.cl-ind-search-clear:hover { color: #0f172a; background: #e2e8f0; }
.cl-ind-dropdown-body {
  max-height: 300px;
  overflow-y: auto;
  padding: 6px 0;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}
.cl-ind-dropdown-body::-webkit-scrollbar { width: 5px; }
.cl-ind-dropdown-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.cl-ind-quick-chips {
  padding: 6px 12px 10px 12px;
  border-bottom: 1px solid #f1f5f9;
  background: #f8fafc;
}
.cl-ind-chips-title {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #64748b;
  margin-bottom: 6px;
  display: flex;
  align-items: center;
  gap: 4px;
}
.cl-ind-chips-row { display: flex; flex-wrap: wrap; gap: 5px; }
.cl-ind-chip {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 3px 8px;
  font-size: 11px;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  transition: all 0.15s ease;
  user-select: none;
}
.cl-ind-chip:hover { border-color: #009ef7; color: #009ef7; background: #f1faff; }
.cl-ind-chip.active {
  background: #009ef7 !important;
  border-color: #009ef7 !important;
  color: #ffffff !important;
}
.cl-ind-section-hdr {
  font-size: 10.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #64748b;
  padding: 8px 14px 4px 14px;
  display: flex;
  align-items: center;
  gap: 6px;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
  margin-top: 4px;
}
.cl-ind-section-hdr:first-child { margin-top: 0; border-top: none; }
.cl-ind-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 14px;
  font-size: 12px;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  transition: background 0.12s ease;
  user-select: none;
}
.cl-ind-item:hover, .cl-ind-item.focused { background: #f1f5f9; }
.cl-ind-item.selected {
  background: #f1faff;
  color: #009ef7;
  font-weight: 600;
}
.cl-ind-item-left {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
  flex: 1;
}
.cl-ind-item-title {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cl-ind-check {
  color: #009ef7;
  display: inline-flex;
  align-items: center;
  flex-shrink: 0;
  margin-left: 8px;
}
.cl-ind-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #cbd5e1;
  flex-shrink: 0;
  transition: background 0.12s ease;
}
.cl-ind-item:hover .cl-ind-dot, .cl-ind-item.focused .cl-ind-dot, .cl-ind-item.selected .cl-ind-dot {
  background: #009ef7;
}
.cl-ind-sec-tag {
  font-size: 9.5px;
  font-weight: 500;
  padding: 1.5px 5px;
  border-radius: 4px;
  background: #f1f5f9;
  color: #64748b;
  white-space: nowrap;
}
.cl-ind-hdr-icon {
  display: inline-flex;
  align-items: center;
  color: #009ef7;
}
.cl-ind-empty { padding: 24px 16px; text-align: center; color: #64748b; }
.cl-ind-empty-icon { margin-bottom: 6px; color: #94a3b8; }
.cl-ind-empty-title { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
.cl-ind-empty-sub { font-size: 11.5px; color: #64748b; margin-bottom: 8px; }

/* Activity & Pipeline Popovers */
.cl-act-dropdown-panel, .cl-pipe-dropdown-panel {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  width: 285px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06);
  z-index: 1055;
  display: none;
  flex-direction: column;
  overflow: hidden;
  padding: 6px 0;
  animation: clPopoverFadeIn 0.15s ease-out;
}
@keyframes clPopoverFadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
.cl-act-dropdown-panel.show, .cl-pipe-dropdown-panel.show { display: flex; }
.cl-act-section-hdr, .cl-pipe-section-hdr {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94a3b8;
  padding: 6px 14px 4px 14px;
  user-select: none;
}
.cl-act-section-hdr:not(:first-child), .cl-pipe-section-hdr:not(:first-child) {
  border-top: 1px solid #f1f5f9;
  margin-top: 4px;
  padding-top: 8px;
}
.cl-act-item, .cl-pipe-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 14px;
  font-size: 12px;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  transition: all 0.12s ease;
  user-select: none;
  outline: none;
}
.cl-act-item:hover, .cl-act-item:focus,
.cl-pipe-item:hover, .cl-pipe-item:focus {
  background: #f1f5f9;
  color: #0f172a;
}
.cl-act-item.selected, .cl-pipe-item.selected {
  background: #eff6ff;
  color: #009ef7;
  font-weight: 600;
}
.cl-act-item-left, .cl-pipe-item-left {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
  flex: 1;
}
.cl-act-item-title, .cl-pipe-item-title {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cl-act-item-right, .cl-pipe-item-right {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.cl-act-badge, .cl-pipe-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 1.5px 6.5px;
  border-radius: 10px;
  background: #f1f5f9;
  color: #64748b;
}
.cl-act-item.selected .cl-act-badge, .cl-pipe-item.selected .cl-pipe-badge {
  background: #dbeafe;
  color: #0284c7;
}
.cl-act-check, .cl-pipe-check {
  font-size: 11px;
  font-weight: 700;
  color: #009ef7;
  display: inline-flex;
  align-items: center;
  width: 14px;
  justify-content: center;
}

/* Dots */
.cl-act-dot, .cl-pipe-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
  flex-shrink: 0;
}
.cl-dot-green  { background: #10b981; }
.cl-dot-blue   { background: #3b82f6; }
.cl-dot-amber  { background: #f59e0b; }
.cl-dot-slate  { background: #94a3b8; }
.cl-dot-purple { background: #8b5cf6; }
.cl-dot-violet { background: #6366f1; }
.cl-dot-all    { background: #64748b; }

/* Sort Popover */
.cl-sort-dropdown-panel {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  width: 260px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06);
  z-index: 1055;
  display: none;
  flex-direction: column;
  overflow: hidden;
  padding: 6px 0;
  animation: clSortFadeIn 0.15s ease-out;
}
@keyframes clSortFadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
.cl-sort-dropdown-panel.show { display: flex; }
.cl-sort-section-hdr {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94a3b8;
  padding: 6px 14px 4px 14px;
  user-select: none;
}
.cl-sort-section-hdr:not(:first-child) {
  border-top: 1px solid #f1f5f9;
  margin-top: 4px;
  padding-top: 8px;
}
.cl-sort-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 14px;
  font-size: 12px;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  transition: all 0.12s ease;
  user-select: none;
  outline: none;
}
.cl-sort-item:hover, .cl-sort-item:focus {
  background: #f1f5f9;
  color: #0f172a;
}
.cl-sort-item.selected {
  background: #eff6ff;
  color: #009ef7;
  font-weight: 600;
}
.cl-sort-item-left {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
  flex: 1;
}
.cl-sort-item-icon {
  color: #64748b;
  flex-shrink: 0;
  transition: color 0.12s ease;
}
.cl-sort-item:hover .cl-sort-item-icon,
.cl-sort-item.selected .cl-sort-item-icon {
  color: #009ef7;
  stroke: #009ef7;
}
.cl-sort-item-title {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cl-sort-direction-badge {
  font-size: 10px;
  font-weight: 600;
  color: #94a3b8;
  margin-right: 4px;
}
.cl-sort-check {
  font-size: 11px;
  font-weight: 700;
  color: #009ef7;
  display: inline-flex;
  align-items: center;
  width: 14px;
  justify-content: center;
}

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
  display: none !important;
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
  grid-template-columns: minmax(0, 2.8fr) minmax(0, 1.0fr) minmax(0, 0.85fr) minmax(0, 2.85fr) !important;
  gap: 8px !important;
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
.cl-subcard.cl-subcard-pipeline {
  padding: 9px 8px 8px !important;
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
  line-height: 1.3 !important;
  cursor: pointer !important;
  word-break: break-word !important;
  display: -webkit-box !important;
  -webkit-line-clamp: 2 !important;
  -webkit-box-orient: vertical !important;
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
  justify-content: center !important;
  width: 100% !important;
  min-width: 0 !important;
  padding-top: 2px !important;
}
.cl-pipeline-flow {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 1px !important;
  width: 100% !important;
  min-width: 0 !important;
}
.cl-pipeline-flow::-webkit-scrollbar { display: none; }
.cl-pipe-step {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  flex: 1 1 0% !important;
  min-width: 0 !important;
  padding: 2px 0 !important;
  border-radius: 6px !important;
  overflow: hidden !important;
  text-align: center !important;
  transition: all 0.15s ease !important;
  cursor: default !important;
  position: relative !important;
}
.cl-pipe-step:hover {
  background: rgba(241, 245, 249, 0.7) !important;
}
.cl-pipe-step.has-count {
  cursor: pointer !important;
}
.cl-pipe-pill {
  min-width: 18px !important;
  height: 18px !important;
  padding: 0 3px !important;
  border-radius: 6px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 11px !important;
  font-weight: 700 !important;
  line-height: 1 !important;
  font-variant-numeric: tabular-nums !important;
  transition: all 0.15s ease !important;
  border: 1px solid transparent !important;
}
.cl-pipe-step.has-count .cl-pipe-pill {
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06) !important;
  transform: translateY(-0.5px) !important;
}

/* Stage Colors (High-Contrast Vibrant Palette) */
.step-sourcing .cl-pipe-pill, .pill-sourcing     { color: #0891b2 !important; }
.step-sourcing .cl-pipe-label                    { color: #0891b2 !important; }
.step-sourcing.has-count .cl-pipe-pill           { background: #ecfeff !important; border: 1px solid #a5f3fc !important; color: #0e7490 !important; }

.step-screening .cl-pipe-pill, .pill-screening   { color: #9333ea !important; }
.step-screening .cl-pipe-label                   { color: #9333ea !important; }
.step-screening.has-count .cl-pipe-pill          { background: #faf5ff !important; border: 1px solid #e9d5ff !important; color: #7e22ce !important; }

.step-evaluation .cl-pipe-pill, .pill-evaluation { color: #4f46e5 !important; }
.step-evaluation .cl-pipe-label                  { color: #4f46e5 !important; }
.step-evaluation.has-count .cl-pipe-pill         { background: #eef2ff !important; border: 1px solid #c7d2fe !important; color: #4338ca !important; }

.step-negotiations .cl-pipe-pill, .pill-negotiations { color: #db2777 !important; }
.step-negotiations .cl-pipe-label                    { color: #db2777 !important; }
.step-negotiations.has-count .cl-pipe-pill           { background: #fdf2f8 !important; border: 1px solid #fbcfe8 !important; color: #be185d !important; }

.step-offer .cl-pipe-pill, .pill-offer           { color: #d97706 !important; }
.step-offer .cl-pipe-label                       { color: #d97706 !important; }
.step-offer.has-count .cl-pipe-pill              { background: #fffbeb !important; border: 1px solid #fde68a !important; color: #b45309 !important; }

.step-joined .cl-pipe-pill, .pill-joined         { color: #16a34a !important; }
.step-joined .cl-pipe-label                      { color: #16a34a !important; }
.step-joined.has-count .cl-pipe-pill             { background: #f0fdf4 !important; border: 1px solid #bbf7d0 !important; color: #15803d !important; }

.step-rejected .cl-pipe-pill, .pill-rejected     { color: #ef4444 !important; }
.step-rejected .cl-pipe-label                    { color: #ef4444 !important; }
.step-rejected.has-count .cl-pipe-pill           { background: #fef2f2 !important; border: 1px solid #fecaca !important; color: #b91c1c !important; }

.step-interview {
  background: #f8fafc !important;
  border: 1px dashed #e2e8f0 !important;
  border-radius: 6px !important;
  padding: 1px 2px !important;
  box-sizing: border-box !important;
}
.step-interview .cl-pipe-pill, .pill-interview   { color: #2563eb !important; }
.step-interview .cl-pipe-label                   { color: #2563eb !important; font-weight: 700 !important; }
.step-interview.has-count                        { background: #eff6ff !important; border: 1px solid #bfdbfe !important; }
.step-interview.has-count .cl-pipe-pill          { background: #2563eb !important; color: #ffffff !important; }
.step-interview.has-count .cl-pipe-label         { color: #1d4ed8 !important; }

.cl-pipe-label {
  font-size: 7.5px !important;
  font-weight: 600 !important;
  text-transform: capitalize !important;
  margin-top: 2px !important;
  letter-spacing: -0.2px !important;
  white-space: nowrap !important;
  line-height: 1.15 !important;
  text-align: center !important;
  width: 100% !important;
  max-width: 100% !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cl-pipe-divider {
  width: 1px !important;
  height: 20px !important;
  background: #cbd5e1 !important;
  margin: 0 3px !important;
  flex-shrink: 0 !important;
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
  .cl-subcards-grid {
    grid-template-columns: minmax(0, 2.6fr) minmax(0, 0.95fr) minmax(0, 0.8fr) minmax(0, 2.85fr) !important;
    gap: 8px !important;
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
  .cl-subcards-grid {
    grid-template-columns: minmax(0, 2.5fr) minmax(0, 0.9fr) minmax(0, 0.8fr) minmax(0, 2.8fr) !important;
    gap: 6px !important;
  }
  .cl-pipe-label {
    font-size: 7px !important;
    letter-spacing: -0.25px !important;
  }
}

@media (max-width: 992px) {
  .cl-subcards-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 8px !important;
  }
  .cl-subcard-pipeline {
    grid-column: span 2 !important;
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
  .cl-subcards-grid {
    grid-template-columns: 1fr !important;
  }
  .cl-subcard-pipeline {
    grid-column: span 1 !important;
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
    $currentTab = $initialTab ?? 'all';
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
        var newlyAdded = counts.newlyAdded !== undefined ? Number(counts.newlyAdded) : (counts.spartnerNewlyAdded !== undefined ? Number(counts.spartnerNewlyAdded) : null);
        var topPerforming = counts.topPerforming !== undefined ? Number(counts.topPerforming) : (counts.spartnerTopPerforming !== undefined ? Number(counts.spartnerTopPerforming) : null);
        var hiring = counts.workingOnJobs !== undefined ? Number(counts.workingOnJobs) : (counts.spartnerHiring !== undefined ? Number(counts.spartnerHiring) : null);
        var inactive = counts.inactive !== undefined ? Number(counts.inactive) : (counts.spartnerInactive !== undefined ? Number(counts.spartnerInactive) : null);
        var request = counts.sentRequest !== undefined ? Number(counts.sentRequest) : (counts.spartnerRequest !== undefined ? Number(counts.spartnerRequest) : null);

        if (all !== null && !isNaN(all)) {
            $('#span_spartnerAll').text(all);
            window.AGENCY_DATA_COUNTERS.spartnerAll = all;
            $('#span_spartnerAll').closest('.cl-card').find('.cl-card-sub span').text('+' + Math.max(all, 1) + ' registered');
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
            $('#span_spartnerRequest').closest('.cl-card').find('.cl-card-sub span').text(request > 0 ? request + ' action needed' : 'All confirmed');
        }
    }
    window.updateAgencyTabCounters = updateAgencyTabCounters;
</script>

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-0 kt_content_agency" id="kt_content">
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
                <div class="cl-export-wrapper dropdown" id="clExportWrapper">
                    <button type="button" class="cl-export-btn" id="clExportDropdownBtn" onclick="toggleAgencyExportDropdown(event)" title="Export agency portfolio">
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
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm cl-export-dropdown-menu" id="clExportDropdownMenu" aria-labelledby="clExportDropdownBtn">
                        <li>
                            <a class="dropdown-item cl-export-option" href="javascript:void(0);" onclick="triggerAgencyExport('filtered_csv', event)">
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
                            <a class="dropdown-item cl-export-option" href="javascript:void(0);" onclick="triggerAgencyExport('all_csv', event)">
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
                            <a class="dropdown-item cl-export-option" href="javascript:void(0);" onclick="triggerAgencyExport('executive_pdf', event)">
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
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" style="stroke: #ffffff !important; color: #ffffff !important;">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Add / Invite Agency</span>
                </button>
                @endif
            </div>
        </div>

        <!-- ── 6 STAT CARDS FILTER GRID (Rule 20 Enterprise Standard) ── -->
        <div class="cl-stat-grid">
            {{-- Card 1: Total Agencies --}}
            <a href="#" onclick="setAgencyTab('all', this); return false;" class="cl-card {{ ($currentTab ?? 'all') === 'all' ? 'active' : '' }}" data-tab="all" title="View all verified connected staffing agency partners">
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
                <div class="cl-card-sub green" title="Total registered agency count">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="m18 15-6-6-6 6"/></svg>
                    <span>+{{ max($totalAgenciesCount, 1) }} registered</span>
                </div>
            </a>

            {{-- Card 2: Newly Added Agencies --}}
            <a href="#" onclick="setAgencyTab('newly_added', this); return false;" class="cl-card {{ ($currentTab ?? 'all') === 'newly_added' ? 'active' : '' }}" data-tab="newly_added" title="View agencies onboarded in the last 180 days">
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
                <div class="cl-card-sub blue" title="Agencies partnered within the last 180 days">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="m18 15-6-6-6 6"/></svg>
                    <span>Last 180 days</span>
                </div>
            </a>

            {{-- Card 3: Top Performing Agencies --}}
            <a href="#" onclick="setAgencyTab('top_performing', this); return false;" class="cl-card {{ ($currentTab ?? 'all') === 'top_performing' ? 'active' : '' }}" data-tab="top_performing" title="View high-performing agencies with verified candidate placements, offers, or interviews">
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
                <div class="cl-card-sub green" title="Agencies with successful placements and candidate velocity">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                    </svg>
                    <span>With placements</span>
                </div>
            </a>

            {{-- Card 4: Active Agencies --}}
            <a href="#" onclick="setAgencyTab('hiring', this); return false;" class="cl-card {{ ($currentTab ?? 'all') === 'hiring' ? 'active' : '' }}" data-tab="hiring" title="View active agencies currently working on live assigned jobs">
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
                <div class="cl-card-sub green" title="Agencies actively assigned to open recruitment jobs">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="m18 15-6-6-6 6"/></svg>
                    <span>Working on jobs</span>
                </div>
            </a>

            {{-- Card 5: Inactive Agencies --}}
            <a href="#" onclick="setAgencyTab('inactive', this); return false;" class="cl-card {{ ($currentTab ?? 'all') === 'inactive' ? 'active' : '' }}" data-tab="inactive" title="View agencies with zero currently active job assignments">
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
                <div class="cl-card-sub pink" title="Agencies with no currently active shared positions">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    <span>0 active jobs</span>
                </div>
            </a>

            {{-- Card 6: Pending Invitations --}}
            <a href="#" onclick="setAgencyTab('request', this); return false;" class="cl-card {{ ($currentTab ?? 'all') === 'request' ? 'active' : '' }}" data-tab="request" title="View outbound agency partnership invitations awaiting acceptance or registration">
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
                <div class="cl-card-sub purple" title="Invitations sent to prospective agency partners">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                    <span>{{ $pendingRequestsCount > 0 ? $pendingRequestsCount . ' action needed' : 'All confirmed' }}</span>
                </div>
            </a>
        </div>

        <!-- ── SEARCH & FILTER BAR (Rule 20 Enterprise Standard) ── -->
        <div class="cl-filter-bar">
            {{-- Search Input (Enterprise Tier 1 Micro-Search Bar) --}}
            <div class="cl-search-box" id="clSearchBoxWrapper">
                <svg class="cl-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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

            {{-- Filter 1: Enterprise Location Combobox Popover --}}
            <div class="cl-loc-dropdown-wrapper" id="clLocDropdownWrapper">
                <div class="cl-loc-combobox-btn" id="clLocBtn" onclick="toggleLocationDropdown(event)" role="button" tabindex="0" aria-haspopup="listbox" aria-expanded="false" title="Filter by agency headquarters country or location">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="cl-pill-texts">
                        <span class="cl-pill-lbl">Location</span>
                        <span class="cl-pill-val" id="locationDisplay">All Locations</span>
                    </div>
                    <div class="cl-pill-clear" id="locationClearBtn" onclick="clearLocationFilterFromPill(event)" title="Reset Location to All" style="display:none;">✕</div>
                    <div class="cl-pill-chevron">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

                <div class="cl-loc-dropdown-panel" id="clLocDropdownPanel">
                    <div class="cl-loc-search-box">
                        <svg class="cl-loc-search-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" class="cl-loc-search-inp" id="clLocSearchInp" placeholder="Search countries or cities..." autocomplete="off" oninput="handleLocationSearchInput(this.value)" />
                        <span class="cl-loc-search-clear" id="clLocSearchClear" onclick="clearLocationSearch()">✕</span>
                    </div>
                    <div class="cl-loc-dropdown-body" id="clLocDropdownBody">
                        {{-- Rendered dynamically from window.initialLocationFacets & window.allCountriesList via User RAM --}}
                    </div>
                </div>

                <input type="hidden" id="country" name="country" value="" />
                <input type="hidden" id="country_name" name="country_name" value="" />
                <input type="hidden" id="city_id" name="city_id" value="" />
                <input type="hidden" id="city_name" name="city_name" value="" />
                <input type="hidden" id="unspecified_location" name="unspecified_location" value="0" />
            </div>

            {{-- Filter 2: Enterprise Industry Combobox Popover --}}
            <div class="cl-ind-dropdown-wrapper" id="clIndDropdownWrapper">
                <div class="cl-ind-combobox-btn" id="clIndBtn" onclick="toggleIndustryDropdown(event)" role="button" tabindex="0" aria-haspopup="listbox" aria-expanded="false" title="Filter by Industry">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                    <div class="cl-pill-texts">
                        <span class="cl-pill-lbl">Industry</span>
                        <span class="cl-pill-val" id="industryDisplay">All Industries</span>
                    </div>
                    <div class="cl-pill-clear" id="industryClearBtn" onclick="clearIndustryFilterFromPill(event)" title="Reset to All Industries" style="display:none;">✕</div>
                    <div class="cl-pill-chevron">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

                <div class="cl-ind-dropdown-panel" id="clIndDropdownPanel" role="listbox" aria-label="Industries Filter">
                    <div class="cl-ind-search-box">
                        <svg class="cl-ind-search-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" class="cl-ind-search-inp" id="clIndSearchInp" placeholder="Search 160+ industries or sectors..." autocomplete="off" oninput="handleIndustrySearchInput(this.value)" />
                        <span class="cl-ind-search-clear" id="clIndSearchClear" onclick="clearIndustrySearch()">✕</span>
                    </div>
                    <div class="cl-ind-dropdown-body" id="clIndDropdownBody">
                        {{-- Rendered dynamically from window.initialIndustriesManifest via User RAM --}}
                    </div>
                </div>

                <input type="hidden" id="industry" name="industry" value="" />
                <input type="hidden" id="industry_id" name="industry_id" value="" />
                <input type="hidden" id="sector_id" name="sector_id" value="" />
            </div>

            {{-- Filter 3: Enterprise Activity Recency Combobox Popover --}}
            <div class="cl-act-dropdown-wrapper" id="clActDropdownWrapper">
                <button type="button" class="cl-act-combobox-btn" id="clActBtn" onclick="toggleActivityDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="clActDropdownPanel" title="Filter by Recency">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="cl-pill-texts">
                        <span class="cl-pill-lbl">Activity</span>
                        <span class="cl-pill-val" id="activityDisplay">All Activity</span>
                    </div>
                    <div class="cl-pill-clear" id="activityClearBtn" onclick="clearActivityFilter(event)" title="Reset Activity" style="display:none;">✕</div>
                    <div class="cl-pill-chevron">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>

                <div class="cl-act-dropdown-panel" id="clActDropdownPanel" role="listbox" aria-labelledby="clActBtn" tabindex="-1">
                    {{-- Section 1: Overview / Reset --}}
                    <div class="cl-act-section-hdr">Activity Overview</div>
                    <div class="cl-act-item selected" id="act-opt-all" data-act-val="all" onclick="selectActivityOption('all', 'All Activity', event)" role="option" aria-selected="true" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-all"></span>
                            <span class="cl-act-item-title">All Activity</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCountAll">{{ $agencyRecencyCounts['all'] ?? 0 }}</span>
                            <span class="cl-act-check">✓</span>
                        </div>
                    </div>

                    {{-- Section 2: Recency (Strictly Mutually Exclusive Buckets) --}}
                    <div class="cl-act-section-hdr">Recency (Mutually Exclusive)</div>
                    <div class="cl-act-item" id="act-opt-7d" data-act-val="7d" onclick="selectActivityOption('7d', 'Active This Week', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-green"></span>
                            <span class="cl-act-item-title">Active This Week (0–7d)</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCount7d">{{ $agencyRecencyCounts['7d'] ?? 0 }}</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-act-item" id="act-opt-30d" data-act-val="30d" onclick="selectActivityOption('30d', 'Active This Month', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-blue"></span>
                            <span class="cl-act-item-title">Active This Month (8–30d)</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCount30d">{{ $agencyRecencyCounts['30d'] ?? 0 }}</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-act-item" id="act-opt-90d" data-act-val="90d" onclick="selectActivityOption('90d', 'Needs Outreach', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-amber"></span>
                            <span class="cl-act-item-title">Needs Outreach (31–90d)</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCount90d">{{ $agencyRecencyCounts['90d'] ?? 0 }}</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-act-item" id="act-opt-dormant" data-act-val="dormant" onclick="selectActivityOption('dormant', 'Dormant (> 90d)', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-slate"></span>
                            <span class="cl-act-item-title">Dormant (> 90d Inactive)</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCountDormant">{{ $agencyRecencyCounts['dormant'] ?? 0 }}</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-act-item" id="act-opt-never" data-act-val="never" onclick="selectActivityOption('never', 'Never Engaged', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-purple"></span>
                            <span class="cl-act-item-title">Never Engaged (No Touch)</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCountNever">{{ $agencyRecencyCounts['never'] ?? 0 }}</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="activity" name="activity" value="" />
            </div>

            {{-- Filter 4: Enterprise Candidate Pipeline Combobox Popover --}}
            <div class="cl-pipe-dropdown-wrapper" id="clPipeDropdownWrapper">
                <button type="button" class="cl-pipe-combobox-btn" id="clPipeBtn" onclick="togglePipelineDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="clPipeDropdownPanel" title="Filter by Candidate Pipeline">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                    </div>
                    <div class="cl-pill-texts">
                        <span class="cl-pill-lbl">Pipeline</span>
                        <span class="cl-pill-val" id="pipelineDisplay">All Pipeline</span>
                    </div>
                    <div class="cl-pill-clear" id="pipelineClearBtn" onclick="clearPipelineFilter(event)" title="Reset Pipeline" style="display:none;">✕</div>
                    <div class="cl-pill-chevron">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>

                <div class="cl-pipe-dropdown-panel" id="clPipeDropdownPanel" role="listbox" aria-labelledby="clPipeBtn" tabindex="-1">
                    <div class="cl-pipe-section-hdr">Pipeline State</div>
                    <div class="cl-pipe-item selected" id="pipe-opt-all" data-pipe-val="all" onclick="selectPipelineOption('all', 'All Pipeline', event)" role="option" aria-selected="true" tabindex="0">
                        <div class="cl-pipe-item-left">
                            <span class="cl-pipe-dot cl-dot-all"></span>
                            <span class="cl-pipe-item-title">All Pipeline</span>
                        </div>
                        <div class="cl-pipe-item-right">
                            <span class="cl-pipe-badge" id="pipeCountAll">{{ $agencyPipelineCounts['all'] ?? 0 }}</span>
                            <span class="cl-pipe-check">✓</span>
                        </div>
                    </div>
                    <div class="cl-pipe-item" id="pipe-opt-active" data-pipe-val="active" onclick="selectPipelineOption('active', 'Pipeline Moving', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-pipe-item-left">
                            <span class="cl-pipe-dot cl-dot-violet"></span>
                            <span class="cl-pipe-item-title">Pipeline Moving (Active)</span>
                        </div>
                        <div class="cl-pipe-item-right">
                            <span class="cl-pipe-badge" id="pipeCountActive">{{ $agencyPipelineCounts['active'] ?? 0 }}</span>
                            <span class="cl-pipe-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-pipe-item" id="pipe-opt-none" data-pipe-val="none" onclick="selectPipelineOption('none', 'No Active Pipeline', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-pipe-item-left">
                            <span class="cl-pipe-dot cl-dot-slate"></span>
                            <span class="cl-pipe-item-title">No Active Pipeline</span>
                        </div>
                        <div class="cl-pipe-item-right">
                            <span class="cl-pipe-badge" id="pipeCountNone">{{ $agencyPipelineCounts['none'] ?? 0 }}</span>
                            <span class="cl-pipe-check" style="display:none;">✓</span>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="pipeline" name="pipeline" value="" />
            </div>

            {{-- Filter 5: Enterprise Sort Combobox Popover --}}
            <div class="cl-sort-dropdown-wrapper" id="clSortDropdownWrapper">
                <button type="button" class="cl-sort-combobox-btn" id="clSortBtn" onclick="toggleSortDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="clSortDropdownPanel" title="Sort Agency Listing">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="4" x2="7" y2="20"/><polyline points="3 8 7 4 11 8"/><line x1="17" y1="4" x2="17" y2="20"/><polyline points="13 16 17 20 21 16"/>
                        </svg>
                    </div>
                    <div class="cl-pill-texts">
                        <span class="cl-pill-lbl">Sort</span>
                        <span class="cl-pill-val" id="sortDisplay">Recently Added</span>
                    </div>
                    <div class="cl-pill-clear" id="sortClearBtn" onclick="clearSortFilter(event)" title="Reset Sort" style="display:none;">✕</div>
                    <div class="cl-pill-chevron">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>

                <div class="cl-sort-dropdown-panel" id="clSortDropdownPanel" role="listbox" aria-labelledby="clSortBtn" tabindex="-1">
                    <div class="cl-sort-section-hdr">Chronological</div>
                    <div class="cl-sort-item selected" id="sort-opt-recent" data-sort-val="recent" onclick="selectSortOption('recent', 'Recently Added', event)" role="option" aria-selected="true" tabindex="0">
                        <div class="cl-sort-item-left">
                            <svg class="cl-sort-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span class="cl-sort-item-title">Recently Added</span>
                        </div>
                        <div class="cl-sort-item-right">
                            <span class="cl-sort-direction-badge">Newest ↓</span>
                            <span class="cl-sort-check">✓</span>
                        </div>
                    </div>
                    <div class="cl-sort-item" id="sort-opt-oldest" data-sort-val="oldest" onclick="selectSortOption('oldest', 'Oldest First', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-sort-item-left">
                            <svg class="cl-sort-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span class="cl-sort-item-title">Oldest First</span>
                        </div>
                        <div class="cl-sort-item-right">
                            <span class="cl-sort-direction-badge">Oldest ↑</span>
                            <span class="cl-sort-check" style="display:none;">✓</span>
                        </div>
                    </div>

                    <div class="cl-sort-section-hdr">Alphabetical</div>
                    <div class="cl-sort-item" id="sort-opt-name_asc" data-sort-val="name_asc" onclick="selectSortOption('name_asc', 'Agency Name (A → Z)', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-sort-item-left">
                            <svg class="cl-sort-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <span class="cl-sort-item-title">Agency Name (A → Z)</span>
                        </div>
                        <div class="cl-sort-item-right">
                            <span class="cl-sort-direction-badge">A → Z</span>
                            <span class="cl-sort-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-sort-item" id="sort-opt-name_desc" data-sort-val="name_desc" onclick="selectSortOption('name_desc', 'Agency Name (Z → A)', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-sort-item-left">
                            <svg class="cl-sort-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <span class="cl-sort-item-title">Agency Name (Z → A)</span>
                        </div>
                        <div class="cl-sort-item-right">
                            <span class="cl-sort-direction-badge">Z → A</span>
                            <span class="cl-sort-check" style="display:none;">✓</span>
                        </div>
                    </div>

                    <div class="cl-sort-section-hdr">Workload & Jobs</div>
                    <div class="cl-sort-item" id="sort-opt-jobs_desc" data-sort-val="jobs_desc" onclick="selectSortOption('jobs_desc', 'Most Shared Jobs', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-sort-item-left">
                            <svg class="cl-sort-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <span class="cl-sort-item-title">Most Shared Jobs</span>
                        </div>
                        <div class="cl-sort-item-right">
                            <span class="cl-sort-direction-badge">High → Low</span>
                            <span class="cl-sort-check" style="display:none;">✓</span>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="sortingfilter" name="sortingfilter" value="recent" />
            </div>
        </div>

        @if(in_array("view_staffing_partners", $userPermissions))
        <!-- ── TABLE / CARD STREAM ── -->
        <div class="tbl">
            <div class="tbl-w">
                <div class="tbl-scroll-container">

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

<!-- ── DRAWER FOR ADD / INVITE AGENCY (Rule 20 Enterprise Card + Sub-Card Design) ── -->
<style>
    .add-agency-drawer {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 560px;
        max-width: 95vw;
        height: 100vh;
        background: #ffffff;
        box-shadow: -10px 0 35px rgba(15, 23, 42, 0.12);
        z-index: 1050;
        display: none;
        flex-direction: column;
        justify-content: space-between;
        font-family: 'Poppins', sans-serif;
    }
    .add-agency-drawer.is-active {
        display: flex !important;
    }
    .add-agency-drawer-body::-webkit-scrollbar {
        width: 5px;
    }
    .add-agency-drawer-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .add-agency-drawer-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .add-agency-drawer-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<div class="add-agency-drawer-overlay" id="add_agency_drawer_overlay" aria-hidden="true" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.45); backdrop-filter:blur(2px); z-index:1049;"></div>
<div class="add-agency-drawer" id="add_agency_drawer" role="dialog" aria-modal="true" aria-labelledby="add_agency_drawer_title" aria-hidden="true">
    <!-- DRAWER HEADER (FIXED TOP WITH DISTINCT BACKGROUND) -->
    <div style="padding:16px 22px; background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom:1.5px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
        <div class="d-flex align-items-center gap-3">
            <div style="width:38px; height:38px; border-radius:10px; background:#ffffff; border:1px solid #cbd5e1; display:flex; align-items:center; justify-content:center; color:#009ef7; box-shadow:0 1px 2px rgba(0,0,0,0.05); flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7.5" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
            </div>
            <div>
                <h2 class="m-0" id="add_agency_drawer_title" style="font-size:15.5px; font-weight:700; color:#0f172a; line-height:1.25;">Invite Staffing Partner</h2>
                <span style="font-size:11.5px; font-weight:400; color:#64748b;">Direct collaboration & pipeline requisition sharing</span>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-icon add_staffing_close" id="add_agency_drawer_close_btn" aria-label="Close" style="width:32px; height:32px; border-radius:8px; background:#ffffff; border:1px solid #cbd5e1; color:#64748b; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.2s; box-shadow:0 1px 2px rgba(0,0,0,0.04);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- DRAWER BODY (SCROLLABLE CONTENT - COMPACT 15.6" FIT) -->
    <div class="add-agency-drawer-body" style="padding:16px 20px; overflow-y:auto; flex-grow:1; display:flex; flex-direction:column; gap:12px; background:#ffffff;">
        <form id="add_staffing_partners_form" name="add_staffing_partners_form" action="#" onsubmit="return false;" style="display:flex; flex-direction:column; gap:12px;">
            <!-- SUB-CARD 1: SMART AGENCY SEARCH & INVITATION INPUT (DISTINCT SOFT SKY BLUE BACKGROUND) -->
            <div style="background:linear-gradient(135deg, #f0f7ff 0%, #f8fbff 100%); border:1.5px solid #bae6fd; border-radius:12px; padding:14px 16px; box-shadow:0 1px 3px rgba(2,132,199,0.05);">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label for="staffing_partners_search_input" class="m-0 d-flex align-items-center gap-1" style="font-size:13px; font-weight:700; color:#0f172a;">
                        <span style="width:7px; height:7px; border-radius:50%; background:#0284c7; display:inline-block;"></span>
                        <span>Option 1: Direct Agency Invite</span>
                    </label>
                    <span class="badge" style="background:#dbeafe; color:#0369a1; border:1px solid #bfdbfe; font-size:10px; font-weight:600; padding:2px 8px; border-radius:12px;">Live Search</span>
                </div>
                <div style="font-size:11.5px; color:#475569; margin-top:2px; margin-bottom:10px; line-height:1.4;">
                    Search registered staffing firms across JERO, or type an agency email:
                </div>

                <!-- Search Input with Live Autocomplete -->
                <div class="position-relative mb-2">
                    <div class="input-group" style="border:1.5px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:38px; box-shadow:0 1px 2px rgba(0,0,0,0.04); transition:border-color 0.2s, box-shadow 0.2s;">
                        <span class="input-group-text bg-transparent border-0 ps-3 pe-2" style="color:#94a3b8;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </span>
                        <input type="text" id="staffing_partners_search_input" class="form-control border-0 shadow-none px-2" placeholder="Type agency name or email address..." autocomplete="off" style="height:36px; font-size:12.5px; font-weight:500; color:#0f172a;">
                        <button type="button" id="btn_clear_agency_search" class="btn btn-sm btn-icon position-absolute end-0 top-50 translate-middle-y me-2 d-none" style="z-index:5; width:20px; height:20px; border-radius:50%; background:#e2e8f0; color:#475569; border:none; display:flex; align-items:center; justify-content:center; padding:0;" title="Clear">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <!-- Hidden target email input used by submit action -->
                    <input type="hidden" name="staffing_partners_email" id="staffing_partners_email" value="">

                    <!-- Autocomplete Results Floating Dropdown -->
                    <div id="agency_autocomplete_dropdown" class="position-absolute start-0 end-0 bg-white border border-gray-300 rounded-3 shadow-lg d-none" style="top:44px; z-index:1070; max-height:240px; overflow-y:auto; padding:4px 0;">
                        <!-- Results dynamically injected by JS -->
                    </div>
                </div>

                <!-- Selected Agency Preview Badge / Card -->
                <div id="selected_agency_preview" class="d-none p-2 mb-2 rounded-2 border d-flex align-items-center justify-content-between" style="background:#f0fdf4; border-color:#86efac !important;">
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <div id="selected_agency_avatar" style="width:28px; height:28px; border-radius:6px; background:#16a34a; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11px; flex-shrink:0;">
                            AG
                        </div>
                        <div class="min-w-0">
                            <div id="selected_agency_name" style="font-size:12px; font-weight:700; color:#0f172a;" class="text-truncate">Agency Name</div>
                            <div id="selected_agency_email" style="font-size:11px; color:#15803d;" class="text-truncate">email@agency.com</div>
                        </div>
                    </div>
                    <button type="button" id="btn_remove_selected_agency" class="btn btn-sm btn-icon btn-light-danger ms-2 flex-shrink-0" style="width:22px; height:22px; border-radius:6px;" title="Remove Selection">✕</button>
                </div>

                <div id="staffing_email_feedback" class="mb-2 fs-8 text-danger" style="display:none;"></div>

                <button type="button" class="btn btn-primary fw-bold w-100" id="submit_staffing_partners" style="background-color:#009ef7; border-radius:8px; height:38px; display:flex; align-items:center; justify-content:center; gap:8px; font-size:12.5px; box-shadow:0 2px 5px rgba(0,158,247,0.25); border:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    <span>Send Partnership Invite</span>
                    <span class="badge ms-1" style="background:rgba(255,255,255,0.22); color:#ffffff; font-size:10px; font-weight:600; padding:2px 6px; border-radius:4px;">5 Coins</span>
                </button>
            </div>

            <!-- OR DIVIDER -->
            <div class="d-flex align-items-center my-0" style="gap:10px;">
                <div class="flex-grow-1" style="height:1px; background:#e2e8f0;"></div>
                <span style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.8px; background:#f1f5f9; padding:2px 10px; border-radius:12px; border:1px solid #e2e8f0;">OR SHARE DIRECT LINK</span>
                <div class="flex-grow-1" style="height:1px; background:#e2e8f0;"></div>
            </div>

            <!-- SUB-CARD 2: SHARE DIRECT REGISTRATION LINK (DISTINCT SOFT EMERALD GREEN BACKGROUND) -->
            @php
                $encodeCompanyId = base64_encode(auth()->user()->company_id);
                $shareRegistrationUrl = url('/company-registration-invitation/' . $encodeCompanyId);
            @endphp
            <div style="background:linear-gradient(135deg, #f0fdf4 0%, #f7fef9 100%); border:1.5px solid #bbf7d0; border-radius:12px; padding:14px 16px; box-shadow:0 1px 3px rgba(16,185,129,0.05);">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label for="kt_share_earn_link_input" class="m-0 d-flex align-items-center gap-1" style="font-size:13px; font-weight:700; color:#0f172a;">
                        <span style="width:7px; height:7px; border-radius:50%; background:#10b981; display:inline-block;"></span>
                        <span>Option 2: Share Registration Link</span>
                    </label>
                    <span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-size:10px; font-weight:600; padding:2px 8px; border-radius:12px;">0 Coins • Direct Link</span>
                </div>
                <div style="font-size:11.5px; color:#475569; margin-top:2px; margin-bottom:10px; line-height:1.4;">
                    Share this unique link via WhatsApp, Slack, LinkedIn or email to let agencies self-register:
                </div>
                <div class="input-group mb-2" style="border:1.5px solid #cbd5e1; border-radius:8px; overflow:hidden; height:38px; background:#ffffff; box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                    <input id="kt_share_earn_link_input" type="text" class="form-control border-0 shadow-none ps-3 pe-1" value="{{ $shareRegistrationUrl }}" readonly style="background:#ffffff; font-size:11px; font-weight:500; color:#334155; height:36px; white-space:nowrap;" title="{{ $shareRegistrationUrl }}">
                    <button type="button" id="kt_share_earn_link_copy_button" class="btn fw-bold px-2" data-clipboard-target="#kt_share_earn_link_input" style="background:#059669; color:#ffffff; height:38px; min-width:78px; display:flex; align-items:center; justify-content:center; gap:4px; font-size:11px; border:none; transition:all 0.2s;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span>Copy Link</span>
                    </button>
                </div>

                <!-- Quick Share Direct Actions -->
                <div class="d-flex align-items-center justify-content-between pt-1" style="font-size:11px; color:#64748b;">
                    <span>Quick Share via:</span>
                    <div class="d-flex align-items-center gap-1">
                        <a href="https://api.whatsapp.com/send?text={{ urlencode('Join our staffing agency partner network on JERO: ' . $shareRegistrationUrl) }}" target="_blank" rel="noopener" class="btn btn-sm btn-light px-2 py-0 border" style="font-size:10.5px; font-weight:600; color:#15803d; background:#ffffff; border-radius:4px; height:24px; display:flex; align-items:center;">WhatsApp</a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareRegistrationUrl) }}" target="_blank" rel="noopener" class="btn btn-sm btn-light px-2 py-0 border" style="font-size:10.5px; font-weight:600; color:#0284c7; background:#ffffff; border-radius:4px; height:24px; display:flex; align-items:center;">LinkedIn</a>
                        <a href="mailto:?subject={{ urlencode('Staffing Partner Registration Invitation') }}&body={{ urlencode('Join our staffing agency partner network on JERO: ' . $shareRegistrationUrl) }}" class="btn btn-sm btn-light px-2 py-0 border" style="font-size:10.5px; font-weight:600; color:#475569; background:#ffffff; border-radius:4px; height:24px; display:flex; align-items:center;">Email</a>
                    </div>
                </div>
            </div>

            <!-- SUB-CARD 3: SECURITY & POLICY NOTICE -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px;">
                <div class="d-flex align-items-center gap-2">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#009ef7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <polyline points="9 12 11 14 15 10"></polyline>
                    </svg>
                    <div style="font-size:11px; color:#64748b; line-height:1.4;">
                        <strong style="color:#0f172a; font-weight:600;">Scanner-Safe Security:</strong> 256-bit token with 7-day TTL. Auto-coin refund applies if recipient email bounces.
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- DRAWER FOOTER (FIXED BOTTOM WITH DISTINCT BACKGROUND) -->
    <div style="padding:12px 20px; background:#f8fafc; border-top:1.5px solid #e2e8f0; flex-shrink:0;">
        <button type="button" class="btn w-100 fw-semibold add_staffing_close" data-dismiss="modal" style="height:38px; border-radius:8px; background:#ffffff; border:1.5px solid #cbd5e1; color:#475569; font-size:12.5px; transition:all 0.2s; box-shadow:0 1px 2px rgba(0,0,0,0.04);">Close Drawer</button>
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
    // ── Agency Listing Facets & Data Payload (User RAM - Client-First) ──
    window.initialLocationFacets = {!! json_encode($agencyLocationFacets ?? ['total' => 0, 'located_count' => 0, 'unspecified_count' => 0, 'hubs' => []]) !!};
    window.allCountriesList = {!! json_encode($countries ?? []) !!};
    window.initialIndustriesManifest = {!! json_encode($industriesManifest ?? App\Services\IndustryTaxonomyService::getGlobalManifest()) !!};
    window.initialRecencyCounts = {!! json_encode($agencyRecencyCounts ?? ['all' => 0, '7d' => 0, '30d' => 0, '90d' => 0, 'dormant' => 0, 'never' => 0]) !!};
    window.initialPipelineCounts = {!! json_encode($agencyPipelineCounts ?? ['all' => 0, 'active' => 0, 'none' => 0]) !!};

    // ── Helper Utilities ──
    function escapeAttr(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/'/g, '&#39;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function highlightText(text, query) {
        if (!query || !text) return escapeAttr(text);
        const escaped = escapeAttr(text);
        const cleanQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${cleanQuery})`, 'gi');
        return escaped.replace(regex, '<span class="cl-ind-mark">$1</span>');
    }

    const SECTOR_ICON_MAP = {
        1: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
        2: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
        3: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>',
        4: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/></svg>',
        5: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
        6: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
        7: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
        8: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>',
        9: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>',
        10: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/></svg>'
    };

    let activeIndSectorFilter = 'all';

    // ── Location Popover Engine ──
    function toggleLocationDropdown(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        const $panel = $('#clLocDropdownPanel');
        const isVisible = $panel.hasClass('show');
        if (window.AgencyListingManager) window.AgencyListingManager.closeAllDropdowns();
        if (!isVisible) {
            $panel.addClass('show');
            $('#clLocBtn').addClass('active');
            renderLocationDropdown($('#clLocSearchInp').val() || '');
            setTimeout(() => $('#clLocSearchInp').focus(), 50);
        }
    }

    function renderLocationDropdown(filterQuery = '') {
        const q = (filterQuery || '').toLowerCase().trim();
        const $body = $('#clLocDropdownBody');
        const facets = window.initialLocationFacets || { total: 0, located_count: 0, unspecified_count: 0, hubs: [] };
        const hubs = facets.hubs || [];
        const unspecifiedCount = facets.unspecified_count || 0;
        const countries = window.allCountriesList || [];
        const currentCountry = window.AgencyListingManager ? window.AgencyListingManager.countryFilter : '';
        const currentCity = window.AgencyListingManager ? window.AgencyListingManager.cityFilter : '';
        const isUnspecified = window.AgencyListingManager ? window.AgencyListingManager.unspecifiedLocation : false;

        let html = '';

        // Option 1: All Locations (Reset)
        const isAllSelected = !currentCountry && !isUnspecified;
        if (!q || 'all locations'.includes(q) || 'clear'.includes(q)) {
            html += `
                <div class="cl-loc-item ${isAllSelected ? 'selected' : ''}" onclick="resetLocationFilter(true)">
                    <div class="cl-loc-item-left">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m2 12 20 0"/></svg>
                        <span class="cl-loc-item-title">All Locations</span>
                    </div>
                    <span class="cl-loc-badge">${facets.total || 0}</span>
                </div>
            `;
        }

        // Active Agency Hubs Section
        let hubHtml = '';
        hubs.forEach(function(hub) {
            const countryMatch = hub.country_name.toLowerCase().includes(q);
            const matchingCities = (hub.cities || []).filter(c => c.city_name.toLowerCase().includes(q));

            if (!q || countryMatch || matchingCities.length > 0) {
                const isCountrySelected = (currentCountry == hub.country_id && !currentCity && !isUnspecified);
                const hasCities = (hub.cities && hub.cities.length > 0);
                const isExpanded = (q.length > 0) || (currentCountry == hub.country_id);

                hubHtml += `
                    <div class="cl-loc-item-container" data-country-id="${hub.country_id}">
                        <div class="cl-loc-item ${isCountrySelected ? 'selected' : ''}" onclick="selectLocationCountry(${hub.country_id}, '${escapeAttr(hub.country_name)}', true)">
                            <div class="cl-loc-item-left">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span class="cl-loc-item-title">${highlightText(hub.country_name, q)}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="cl-loc-badge">${hub.count}</span>
                                ${hasCities ? `
                                    <span class="cl-loc-expand-btn ${isExpanded ? 'expanded' : ''}" onclick="toggleCityExpand(event, ${hub.country_id})">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                    </span>
                                ` : ''}
                            </div>
                        </div>
                `;

                if (hasCities) {
                    hubHtml += `<div class="cl-loc-city-list ${isExpanded ? 'show' : ''}" id="clLocCities_${hub.country_id}">`;
                    hub.cities.forEach(function(city) {
                        if (!q || countryMatch || city.city_name.toLowerCase().includes(q)) {
                            const isCitySelected = (currentCountry == hub.country_id && (currentCity == city.city_id || currentCity == city.city_name));
                            hubHtml += `
                                <div class="cl-loc-city-item ${isCitySelected ? 'selected' : ''}" onclick="selectLocationCity(${hub.country_id}, '${escapeAttr(hub.country_name)}', '${city.city_id}', '${escapeAttr(city.city_name)}', true)">
                                    <span>${highlightText(city.city_name, q)}</span>
                                    <span class="cl-loc-badge" style="font-size:9.5px; padding:0 5px;">${city.count}</span>
                                </div>
                            `;
                        }
                    });
                    hubHtml += `</div>`;
                }

                hubHtml += `</div>`;
            }
        });

        if (hubHtml) {
            html += `<div class="cl-loc-section-hdr">Your Agency Hubs</div>${hubHtml}`;
        }

        // Unspecified Location option
        if (unspecifiedCount > 0 && (!q || 'location not specified'.includes(q) || 'unspecified'.includes(q) || 'unknown'.includes(q))) {
            const isUnspecifiedSelected = (isUnspecified === true || isUnspecified === 1);
            html += `
                <div class="cl-loc-item ${isUnspecifiedSelected ? 'selected' : ''}" onclick="selectLocationUnspecified(true)">
                    <div class="cl-loc-item-left">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        <span class="cl-loc-item-title" style="color:#64748b;">Location Not Specified</span>
                    </div>
                    <span class="cl-loc-badge" style="background:#f1f5f9; color:#64748b;">${unspecifiedCount}</span>
                </div>
            `;
        }

        // All 250 Countries Section
        let globalHtml = '';
        let globalCount = 0;
        const maxGlobalToShow = q ? 50 : 15;

        countries.forEach(function(c) {
            const inHubs = hubs.some(h => h.country_id == c.id || h.country_name.toLowerCase() === c.country_name.toLowerCase());
            if (inHubs && !q) return;

            if (!q || c.country_name.toLowerCase().includes(q)) {
                if (globalCount < maxGlobalToShow) {
                    globalCount++;
                    const isSelected = (currentCountry == c.id && !currentCity && !isUnspecified);
                    globalHtml += `
                        <div class="cl-loc-item ${isSelected ? 'selected' : ''}" onclick="selectLocationCountry(${c.id}, '${escapeAttr(c.country_name)}', true)">
                            <div class="cl-loc-item-left">
                                <span style="font-size:13px; line-height:1;">${c.emoji || '🌐'}</span>
                                <span class="cl-loc-item-title">${highlightText(c.country_name, q)}</span>
                            </div>
                        </div>
                    `;
                }
            }
        });

        if (globalHtml) {
            html += `
                <div class="cl-loc-divider"></div>
                <div class="cl-loc-section-hdr">${q ? 'Global Countries Match' : 'All Countries'}</div>
                ${globalHtml}
            `;
            if (!q && countries.length > maxGlobalToShow) {
                html += `<div style="padding:4px 14px 8px 14px; font-size:10.5px; color:#94a3b8; font-style:italic;">Type above to search all 250 countries...</div>`;
            }
        }

        if (!html) {
            html = `<div class="cl-loc-empty">No locations matching "${escapeAttr(q)}"</div>`;
        }

        $body.html(html);
    }

    function toggleCityExpand(e, countryId) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        const $cities = $(`#clLocCities_${countryId}`);
        const $btn = $(e.currentTarget);
        $cities.toggleClass('show');
        $btn.toggleClass('expanded');
    }

    function selectLocationCountry(countryId, countryName, triggerFetch = true) {
        if (window.AgencyListingManager) {
            window.AgencyListingManager.countryFilter = countryId;
            window.AgencyListingManager.countryName = countryName;
            window.AgencyListingManager.cityFilter = '';
            window.AgencyListingManager.cityName = '';
            window.AgencyListingManager.unspecifiedLocation = false;
        }
        $('#country').val(countryId);
        $('#country_name').val(countryName);
        $('#city_id').val('');
        $('#city_name').val('');
        $('#unspecified_location').val('0');

        $('#locationDisplay').text(countryName);
        $('#clLocBtn').addClass('has-filter').removeClass('active');
        $('#locationClearBtn').show();
        $('#clLocDropdownPanel').removeClass('show');

        if (triggerFetch && window.AgencyListingManager) {
            window.AgencyListingManager.loadPage(1);
        }
    }

    function selectLocationCity(countryId, countryName, cityId, cityName, triggerFetch = true) {
        if (window.AgencyListingManager) {
            window.AgencyListingManager.countryFilter = countryId;
            window.AgencyListingManager.countryName = countryName;
            window.AgencyListingManager.cityFilter = cityId || cityName;
            window.AgencyListingManager.cityName = cityName;
            window.AgencyListingManager.unspecifiedLocation = false;
        }
        $('#country').val(countryId);
        $('#country_name').val(countryName);
        $('#city_id').val(cityId);
        $('#city_name').val(cityName);
        $('#unspecified_location').val('0');

        const displayLabel = `${countryName}: ${cityName}`;
        $('#locationDisplay').text(displayLabel);
        $('#clLocBtn').addClass('has-filter').removeClass('active');
        $('#locationClearBtn').show();
        $('#clLocDropdownPanel').removeClass('show');

        if (triggerFetch && window.AgencyListingManager) {
            window.AgencyListingManager.loadPage(1);
        }
    }

    function selectLocationUnspecified(triggerFetch = true) {
        if (window.AgencyListingManager) {
            window.AgencyListingManager.countryFilter = '';
            window.AgencyListingManager.countryName = '';
            window.AgencyListingManager.cityFilter = '';
            window.AgencyListingManager.cityName = '';
            window.AgencyListingManager.unspecifiedLocation = true;
        }
        $('#country').val('');
        $('#country_name').val('');
        $('#city_id').val('');
        $('#city_name').val('');
        $('#unspecified_location').val('1');

        $('#locationDisplay').text('Location Not Specified');
        $('#clLocBtn').addClass('has-filter').removeClass('active');
        $('#locationClearBtn').show();
        $('#clLocDropdownPanel').removeClass('show');

        if (triggerFetch && window.AgencyListingManager) {
            window.AgencyListingManager.loadPage(1);
        }
    }

    function resetLocationFilter(triggerFetch = true) {
        if (window.AgencyListingManager) {
            window.AgencyListingManager.countryFilter = '';
            window.AgencyListingManager.countryName = '';
            window.AgencyListingManager.cityFilter = '';
            window.AgencyListingManager.cityName = '';
            window.AgencyListingManager.unspecifiedLocation = false;
        }
        $('#country').val('');
        $('#country_name').val('');
        $('#city_id').val('');
        $('#city_name').val('');
        $('#unspecified_location').val('0');

        $('#locationDisplay').text('All Locations');
        $('#clLocBtn').removeClass('has-filter active');
        $('#locationClearBtn').hide();
        $('#clLocDropdownPanel').removeClass('show');

        if (triggerFetch && window.AgencyListingManager) {
            window.AgencyListingManager.loadPage(1);
        }
    }

    function clearLocationFilterFromPill(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        resetLocationFilter(true);
    }

    function handleLocationSearchInput(val) {
        const q = (val || '').trim();
        if (q.length > 0) {
            $('#clLocSearchClear').show();
        } else {
            $('#clLocSearchClear').hide();
        }
        renderLocationDropdown(q);
    }

    function clearLocationSearch() {
        $('#clLocSearchInp').val('');
        $('#clLocSearchClear').hide();
        renderLocationDropdown('');
        $('#clLocSearchInp').focus();
    }

    // ── Industry Popover Engine ──
    function toggleIndustryDropdown(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        const $panel = $('#clIndDropdownPanel');
        const isVisible = $panel.hasClass('show');
        if (window.AgencyListingManager) window.AgencyListingManager.closeAllDropdowns();
        if (!isVisible) {
            $panel.addClass('show');
            $('#clIndBtn').addClass('active');
            renderIndustryDropdown($('#clIndSearchInp').val() || '');
            setTimeout(() => $('#clIndSearchInp').focus(), 50);
        }
    }

    function filterIndustryBySector(sectorId, e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        activeIndSectorFilter = sectorId;
        renderIndustryDropdown($('#clIndSearchInp').val() || '');
    }

    function renderIndustryDropdown(filterQuery = '') {
        const q = (filterQuery || '').toLowerCase().trim();
        const $body = $('#clIndDropdownBody');
        const manifest = window.initialIndustriesManifest || { industries: [], sectors: [] };
        const allIndustries = manifest.industries || [];
        const selectedId = window.AgencyListingManager ? String(window.AgencyListingManager.industryFilter || '') : '';

        let html = '';

        // Quick Chips (All, Tech & AI, FinTech, Health, Retail & D2C, Other)
        if (!q) {
            html += `
                <div class="cl-ind-quick-chips">
                    <div class="cl-ind-chips-title">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#009ef7" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>Quick Sectors</span>
                    </div>
                    <div class="cl-ind-chips-row">
                        <button type="button" class="cl-ind-chip ${activeIndSectorFilter === 'all' ? 'active' : ''}" onclick="filterIndustryBySector('all', event)">All</button>
                        <button type="button" class="cl-ind-chip ${activeIndSectorFilter === 1 ? 'active' : ''}" onclick="filterIndustryBySector(1, event)">Tech & AI</button>
                        <button type="button" class="cl-ind-chip ${activeIndSectorFilter === 2 ? 'active' : ''}" onclick="filterIndustryBySector(2, event)">FinTech</button>
                        <button type="button" class="cl-ind-chip ${activeIndSectorFilter === 3 ? 'active' : ''}" onclick="filterIndustryBySector(3, event)">Healthcare</button>
                        <button type="button" class="cl-ind-chip ${activeIndSectorFilter === 6 ? 'active' : ''}" onclick="filterIndustryBySector(6, event)">Retail & D2C</button>
                        <button type="button" class="cl-ind-chip ${activeIndSectorFilter === 'other' ? 'active' : ''}" onclick="filterIndustryBySector('other', event)">Other</button>
                    </div>
                </div>
            `;
        }

        // Option 1: All Industries (Reset option)
        const isAllSelected = !selectedId;
        if (!q || 'all industries'.includes(q) || 'clear'.includes(q) || 'all'.includes(q)) {
            html += `
                <div class="cl-ind-item ${isAllSelected ? 'selected' : ''}" data-id="" onclick="clearIndustryFilter(true)">
                    <div class="cl-ind-item-left">
                        <span class="cl-ind-dot"></span>
                        <span class="cl-ind-item-title">All Industries</span>
                    </div>
                    ${isAllSelected ? '<span class="cl-ind-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>' : ''}
                </div>
            `;
        }

        // Filter industries
        let matches = allIndustries.filter(function(item) {
            if (activeIndSectorFilter !== 'all' && !q) {
                if (activeIndSectorFilter === 'other') {
                    if (item.id !== 147 && item.sector_id !== 5 && item.sector_id !== 10) return false;
                } else if (item.sector_id !== activeIndSectorFilter) {
                    return false;
                }
            }
            if (!q) return true;

            const nameMatch = (item.name || '').toLowerCase().includes(q);
            const sectorMatch = (item.sector_name || '').toLowerCase().includes(q);
            const kwMatch = (item.keywords || '').toLowerCase().includes(q);
            const acronymMatch = (item.acronyms || []).some(a => a.toLowerCase().includes(q));
            const synMatch = (item.synonyms || []).some(s => s.toLowerCase().includes(q));

            return nameMatch || sectorMatch || kwMatch || acronymMatch || synMatch;
        });

        if (matches.length === 0) {
            html += `
                <div class="cl-ind-empty">
                    <div class="cl-ind-empty-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </div>
                    <div class="cl-ind-empty-title">No industry matching "${escapeAttr(q)}"</div>
                    <div class="cl-ind-empty-sub">Select universal classification:</div>
                    <button type="button" class="btn btn-sm btn-light-primary" style="font-size:11.5px; font-weight:600; padding:6px 14px; border-radius:6px; display:inline-flex; align-items:center; gap:6px;" onclick="selectIndustryFilter(147, 'Other / Diversified Services', true)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Select "Other / Diversified Services"
                    </button>
                </div>
            `;
            $body.html(html);
            return;
        }

        let currentSecId = null;
        matches.forEach(function(item, idx) {
            if (!q && item.sector_id !== currentSecId) {
                currentSecId = item.sector_id;
                const secIcon = SECTOR_ICON_MAP[currentSecId] || SECTOR_ICON_MAP[5];
                html += `
                    <div class="cl-ind-section-hdr">
                        <span class="cl-ind-hdr-icon">${secIcon}</span>
                        <span>${escapeAttr(item.sector_name || 'General')}</span>
                    </div>
                `;
            }

            const isSelected = String(item.id) === selectedId;
            const displayName = q ? highlightText(item.name, q) : escapeAttr(item.name);
            const sectorTag = q ? `<span class="cl-ind-sec-tag">${escapeAttr((item.sector_name || '').split(',')[0].split('&')[0].trim())}</span>` : '';

            html += `
                <div class="cl-ind-item ${isSelected ? 'selected' : ''}" data-idx="${idx}" data-id="${item.id}" onclick="selectIndustryFilter(${item.id}, '${escapeAttr(item.name)}', true)" role="option" aria-selected="${isSelected ? 'true' : 'false'}">
                    <div class="cl-ind-item-left">
                        <span class="cl-ind-dot"></span>
                        <span class="cl-ind-item-title" title="${escapeAttr(item.name)}">${displayName}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        ${sectorTag}
                        ${isSelected ? '<span class="cl-ind-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>' : ''}
                    </div>
                </div>
            `;
        });

        $body.html(html);
    }

    function selectIndustryFilter(id, name, triggerFetch = true) {
        const val = id ? String(id) : '';
        if (window.AgencyListingManager) {
            window.AgencyListingManager.industryFilter = val;
            window.AgencyListingManager.industryName = name;
        }
        $('#industry').val(val);
        $('#industry_id').val(val);

        $('#industryDisplay').text(name || 'All Industries');
        if (val) {
            $('#clIndBtn').addClass('has-filter').removeClass('active');
            $('#industryClearBtn').show();
        } else {
            $('#clIndBtn').removeClass('has-filter active');
            $('#industryClearBtn').hide();
        }
        $('#clIndDropdownPanel').removeClass('show');

        if (triggerFetch && window.AgencyListingManager) {
            window.AgencyListingManager.loadPage(1);
        }
    }

    function clearIndustryFilter(triggerFetch = true) {
        selectIndustryFilter('', 'All Industries', triggerFetch);
    }

    function clearIndustryFilterFromPill(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        clearIndustryFilter(true);
    }

    function handleIndustrySearchInput(val) {
        const q = (val || '').trim();
        if (q.length > 0) {
            $('#clIndSearchClear').show();
        } else {
            $('#clIndSearchClear').hide();
        }
        renderIndustryDropdown(q);
    }

    function clearIndustrySearch() {
        $('#clIndSearchInp').val('');
        $('#clIndSearchClear').hide();
        renderIndustryDropdown('');
        $('#clIndSearchInp').focus();
    }

    // ── Activity Recency Engine ──
    function toggleActivityDropdown(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        const $panel = $('#clActDropdownPanel');
        const isVisible = $panel.hasClass('show');
        if (window.AgencyListingManager) window.AgencyListingManager.closeAllDropdowns();
        if (!isVisible) {
            $panel.addClass('show');
            $('#clActBtn').addClass('active');
        }
    }

    function selectActivityOption(val, label, e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        const key = (val === 'all' || !val) ? '' : val;
        if (window.AgencyListingManager) {
            window.AgencyListingManager.activityFilter = key;
            window.AgencyListingManager.activityName = label;
        }
        $('#activity').val(key);
        $('#activityDisplay').text(label || 'All Activity');

        $('#clActDropdownPanel .cl-act-item').removeClass('selected').attr('aria-selected', 'false').find('.cl-act-check').hide();
        const targetId = key ? `act-opt-${key}` : 'act-opt-all';
        $(`#${targetId}`).addClass('selected').attr('aria-selected', 'true').find('.cl-act-check').show();

        if (key) {
            $('#clActBtn').addClass('has-filter').removeClass('active');
            $('#activityClearBtn').show();
        } else {
            $('#clActBtn').removeClass('has-filter active');
            $('#activityClearBtn').hide();
        }
        $('#clActDropdownPanel').removeClass('show');

        if (window.AgencyListingManager) {
            window.AgencyListingManager.loadPage(1);
        }
    }

    function clearActivityFilter(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        selectActivityOption('all', 'All Activity', e);
    }

    // ── Pipeline Engine ──
    function togglePipelineDropdown(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        const $panel = $('#clPipeDropdownPanel');
        const isVisible = $panel.hasClass('show');
        if (window.AgencyListingManager) window.AgencyListingManager.closeAllDropdowns();
        if (!isVisible) {
            $panel.addClass('show');
            $('#clPipeBtn').addClass('active');
        }
    }

    function selectPipelineOption(val, label, e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        const key = (val === 'all' || !val) ? '' : val;
        if (window.AgencyListingManager) {
            window.AgencyListingManager.pipelineFilter = key;
            window.AgencyListingManager.pipelineName = label;
        }
        $('#pipeline').val(key);
        $('#pipelineDisplay').text(label || 'All Pipeline');

        $('#clPipeDropdownPanel .cl-pipe-item').removeClass('selected').attr('aria-selected', 'false').find('.cl-pipe-check').hide();
        const targetId = key ? `pipe-opt-${key}` : 'pipe-opt-all';
        $(`#${targetId}`).addClass('selected').attr('aria-selected', 'true').find('.cl-pipe-check').show();

        if (key) {
            $('#clPipeBtn').addClass('has-filter').removeClass('active');
            $('#pipelineClearBtn').show();
        } else {
            $('#clPipeBtn').removeClass('has-filter active');
            $('#pipelineClearBtn').hide();
        }
        $('#clPipeDropdownPanel').removeClass('show');

        if (window.AgencyListingManager) {
            window.AgencyListingManager.loadPage(1);
        }
    }

    function clearPipelineFilter(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        selectPipelineOption('all', 'All Pipeline', e);
    }

    // ── Sort Engine ──
    function toggleSortDropdown(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        const $panel = $('#clSortDropdownPanel');
        const isVisible = $panel.hasClass('show');
        if (window.AgencyListingManager) window.AgencyListingManager.closeAllDropdowns();
        if (!isVisible) {
            $panel.addClass('show');
            $('#clSortBtn').addClass('active');
        }
    }

    function selectSortOption(val, label, e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        const sortVal = val || 'recent';
        if (window.AgencyListingManager) {
            window.AgencyListingManager.sortFilter = sortVal;
        }
        $('#sortingfilter').val(sortVal);
        $('#sortDisplay').text(label || 'Recently Added');

        $('#clSortDropdownPanel .cl-sort-item').removeClass('selected').attr('aria-selected', 'false').find('.cl-sort-check').hide();
        const targetId = `sort-opt-${sortVal}`;
        $(`#${targetId}`).addClass('selected').attr('aria-selected', 'true').find('.cl-sort-check').show();

        if (sortVal !== 'recent') {
            $('#clSortBtn').addClass('has-filter').removeClass('active');
            $('#sortClearBtn').show();
        } else {
            $('#clSortBtn').removeClass('has-filter active');
            $('#sortClearBtn').hide();
        }
        $('#clSortDropdownPanel').removeClass('show');

        if (window.AgencyListingManager) {
            window.AgencyListingManager.loadPage(1);
        }
    }

    function clearSortFilter(e) {
        if (e) { e.stopPropagation(); e.preventDefault(); }
        selectSortOption('recent', 'Recently Added', e);
    }

    // ── Enterprise In-Browser Agency Exporter (Rule 3 Client-First & CWE-1236 Defense) ──
    window.AgencyExportManager = {
        sanitizeCsv: function(val) {
            if (val === null || val === undefined) return '""';
            var s = String(val).trim();
            if (/^[=+\-@\t\r]/.test(s)) s = "'" + s;
            return '"' + s.replace(/"/g, '""') + '"';
        },

        exportFilteredCsv: function() {
            var self = this;
            var rows = [];
            var exportId = 'EXP-AGY-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-' + Math.floor(1000 + Math.random() * 9000);

            var manifestLines = [
                '# ==============================================================================',
                '# JERO ENTERPRISE ATS AGENCY PORTFOLIO EXPORT MANIFEST (CLIENT-FIRST)',
                '# Export ID: ' + exportId,
                '# Exported At: ' + new Date().toISOString(),
                '# Scope: Filtered Visible Agencies',
                '# Company ID: ' + {{ (int)auth()->user()->company_id }},
                '# Exported By: ' + '{{ addslashes(auth()->user()->name ?? "User") }}',
                '# =============================================================================='
            ];

            var headers = [
                'Agency Name', 'Industry', 'Company Size', 'City', 'State', 'Country',
                'Active Jobs', 'Hold Jobs', 'Closed Jobs',
                'Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offer', 'Joined', 'Rejected',
                'Status', 'Partnered Date', 'Last Activity'
            ];

            rows.push(manifestLines.join('\r\n'));
            rows.push(headers.map(function(h) { return '"' + h + '"'; }).join(','));

            var count = 0;
            $('#staffingpartnerajax .cl-primary-card').each(function() {
                var $card = $(this);
                var name = $card.find('.cl-name').text().trim();
                var ind = $card.find('.cl-ind').text().trim();
                var size = $card.find('.cl-size').text().trim();

                var city = $card.find('.cl-loc-city').text().trim();
                var state = $card.find('.cl-loc-state').text().trim();
                var country = $card.find('.cl-loc-country').text().trim();

                var activeJobs = $card.find('.cl-jobs-body .cl-job-line:eq(0) .cl-job-num').text().trim() || '0';
                var holdJobs = $card.find('.cl-jobs-body .cl-job-line:eq(1) .cl-job-num').text().trim() || '0';
                var closedJobs = $card.find('.cl-jobs-body .cl-job-line:eq(2) .cl-job-num').text().trim() || '0';

                var sourcing = $card.find('.cl-pipe-step:eq(0) .cl-pipe-num').text().trim() || '0';
                var screening = $card.find('.cl-pipe-step:eq(1) .cl-pipe-num').text().trim() || '0';
                var evaluation = $card.find('.cl-pipe-step:eq(2) .cl-pipe-num').text().trim() || '0';
                var negotiation = $card.find('.cl-pipe-step:eq(3) .cl-pipe-num').text().trim() || '0';
                var offer = $card.find('.cl-pipe-step:eq(4) .cl-pipe-num').text().trim() || '0';
                var joined = $card.find('.cl-pipe-step:eq(5) .cl-pipe-num').text().trim() || '0';
                var rejected = $card.find('.cl-pipe-step:eq(6) .cl-pipe-num').text().trim() || '0';

                var status = $card.find('.cl-status-pill').text().trim() || 'Active';
                var partnerDate = $card.find('.cl-strip-left span:contains("Partnered:")').parent().text().replace('Partnered:', '').trim() || '-';
                var lastAct = $card.find('.cl-strip-left span:contains("Activity:")').parent().text().replace('Activity:', '').trim() || '-';

                if (name) {
                    count++;
                    rows.push([
                        self.sanitizeCsv(name),
                        self.sanitizeCsv(ind),
                        self.sanitizeCsv(size),
                        self.sanitizeCsv(city),
                        self.sanitizeCsv(state),
                        self.sanitizeCsv(country),
                        activeJobs, holdJobs, closedJobs,
                        sourcing, screening, evaluation, negotiation, offer, joined, rejected,
                        self.sanitizeCsv(status),
                        self.sanitizeCsv(partnerDate),
                        self.sanitizeCsv(lastAct)
                    ].join(','));
                }
            });

            if (count === 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'info', title: 'No Agencies', text: 'No agencies found on the current view to export.' });
                } else {
                    alert('No agencies found on the current view to export.');
                }
                return;
            }

            var csvContent = '\uFEFF' + rows.join('\r\n');
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var url = URL.createObjectURL(blob);
            var link = document.createElement('a');
            var dateStr = new Date().toISOString().slice(0, 10);
            link.setAttribute('href', url);
            link.setAttribute('download', 'jero_agencies_filtered_' + dateStr + '.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        },

        exportAllCsv: function() {
            window.location.href = `{{ url('service_company/consultancy/export/0/10000') }}`;
        },

        exportPdf: function() {
            window.print();
        }
    };

    window.toggleAgencyExportDropdown = function(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        const $menu = $('#clExportDropdownMenu');
        const isShown = $menu.hasClass('show');
        if (window.AgencyListingManager) window.AgencyListingManager.closeAllDropdowns();
        if (!isShown) {
            $menu.addClass('show');
            $('#clExportDropdownBtn').addClass('show active');
        } else {
            $menu.removeClass('show');
            $('#clExportDropdownBtn').removeClass('show active');
        }
    };

    window.triggerAgencyExport = function(type, e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        $('#clExportDropdownMenu').removeClass('show');
        $('#clExportDropdownBtn').removeClass('show active');
        if (type === 'filtered_csv') {
            window.AgencyExportManager.exportFilteredCsv();
        } else if (type === 'all_csv') {
            window.AgencyExportManager.exportAllCsv();
        } else if (type === 'executive_pdf') {
            window.AgencyExportManager.exportPdf();
        }
    };

    // ── Export Dropdown Outside Click Listener ──
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#clExportWrapper').length) {
            $('#clExportDropdownMenu').removeClass('show');
            $('#clExportDropdownBtn').removeClass('show active');
        }
    });

    $(document).on('click', '#clExportDropdownMenu a', function(e) {
        $('#clExportDropdownMenu').removeClass('show');
        $('#clExportDropdownBtn').removeClass('show active');
    });

    // ── Tab Management ──
    window.setAgencyTab = function(tabKey, el) {
        $('.cl-stat-grid .cl-card').removeClass('active');
        if (el) {
            $(el).addClass('active');
        } else {
            $(`.cl-stat-grid .cl-card[data-tab="${tabKey}"]`).addClass('active');
        }
        if (window.AgencyListingManager) {
            window.AgencyListingManager.setTab(tabKey);
        }
    };

    window.clearAgencySearchInput = function(e) {
        if (e) e.stopPropagation();
        $('#kt_tagify_2').val('');
        $('#clSearchClearBtn, #clSearchMatchCount').hide();
        if (window.AgencyListingManager) {
            window.AgencyListingManager.setSearch('');
        }
    };

    // ── Drawer Architecture ──
    window.openAgencyDrawer = function(id) {
        if (!id) return;
        $('[data-row-id]').closest('.tbl-r, .cl-primary-card').removeClass('client-selected');
        $(`[data-row-id="${id}"]`).closest('.tbl-r, .cl-primary-card').addClass('client-selected');

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

            var overlay = document.querySelector('.drawer-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'drawer-overlay agency-drawer-overlay';
                document.body.appendChild(overlay);
            }
            overlay.style.zIndex = '999998';
            overlay.style.cursor = 'pointer';
            overlay.onclick = function(e) {
                if (e) { e.preventDefault(); e.stopPropagation(); }
                window.closeAgencyDrawer();
            };
        }

        $('#kt_activitiesDirectClient_body').html('<div class="d-flex align-items-center justify-content-center p-10" style="min-height:300px;"><div class="spinner-border text-primary" role="status" style="width:3rem; height:3rem;"></div></div>');

        let url = "{{ route('service_company.consultancy.dviewmore', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            cache: false,
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
                    try { inst.hide(); } catch(e){}
                }
            }
            drawerEl.classList.remove('drawer-on');
            drawerEl.style.removeProperty('transform');
            drawerEl.style.removeProperty('z-index');
            drawerEl.style.display = 'none';
        }
        document.querySelectorAll('.drawer-overlay.agency-drawer-overlay, .drawer-overlay, .custom-cand-drawer-overlay').forEach(function(ov) {
            ov.remove();
        });
        $('.tbl-r.client-selected, .cl-primary-card.client-selected').removeClass('client-selected');
        document.body.removeAttribute('data-kt-drawer-activities');
        document.body.removeAttribute('data-kt-drawer');
        document.body.style.overflow = '';
    };

    // ── Agency Listing Manager: SWR Cache, URL Sync & Complete Page Isolation (Rule 17) ──
    const AgencyListingManager = {
        currentPage: 1,
        activeTab: '{{ $initialTab ?? "all" }}',
        searchQuery: '',
        countryFilter: '',
        countryName: '',
        cityFilter: '',
        cityName: '',
        unspecifiedLocation: false,
        industryFilter: '',
        industryName: '',
        sectorFilter: '',
        activityFilter: '',
        activityName: '',
        pipelineFilter: '',
        pipelineName: '',
        sortFilter: 'recent',
        searchDebounceTimer: null,
        cache: {},
        activeXhr: null,

        init: function() {
            this.readUrlParams();
            this.bindEvents();

            if (!$('#staffingpartnerajax').children().length || this.hasCustomInitialFilters()) {
                this.loadPage(this.currentPage, true);
            }
        },

        readUrlParams: function() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('tab')) {
                const tab = params.get('tab');
                if (['all', 'newly_added', 'top_performing', 'hiring', 'inactive', 'request'].indexOf(tab) > -1) {
                    this.activeTab = tab;
                    $('.cl-stat-grid .cl-card').removeClass('active');
                    $(`.cl-stat-grid .cl-card[data-tab="${tab}"]`).addClass('active');
                }
            }
            if (params.has('search') || params.has('q')) {
                this.searchQuery = params.get('search') || params.get('q') || '';
                $('#kt_tagify_2').val(this.searchQuery);
                if (this.searchQuery) $('#clSearchClearBtn').show();
            }
            if (params.has('country')) {
                this.countryFilter = params.get('country');
                $('#country').val(this.countryFilter);
                $('#clLocBtn').addClass('has-filter');
                $('#locationClearBtn').show();
            }
            if (params.has('industry')) {
                this.industryFilter = params.get('industry');
                $('#industry').val(this.industryFilter);
                $('#clIndBtn').addClass('has-filter');
                $('#industryClearBtn').show();
            }
            if (params.has('activity')) {
                this.activityFilter = params.get('activity');
                $('#activity').val(this.activityFilter);
                $('#clActBtn').addClass('has-filter');
                $('#activityClearBtn').show();
                $('#clActDropdownPanel .cl-act-item').removeClass('selected').attr('aria-selected', 'false').find('.cl-act-check').hide();
                $(`#act-opt-${this.activityFilter}`).addClass('selected').attr('aria-selected', 'true').find('.cl-act-check').show();
            }
            if (params.has('pipeline')) {
                this.pipelineFilter = params.get('pipeline');
                $('#pipeline').val(this.pipelineFilter);
                $('#clPipeBtn').addClass('has-filter');
                $('#pipelineClearBtn').show();
                $('#clPipeDropdownPanel .cl-pipe-item').removeClass('selected').attr('aria-selected', 'false').find('.cl-pipe-check').hide();
                $(`#pipe-opt-${this.pipelineFilter}`).addClass('selected').attr('aria-selected', 'true').find('.cl-pipe-check').show();
            }
            if (params.has('sort') || params.has('sortingfilter')) {
                this.sortFilter = params.get('sort') || params.get('sortingfilter') || 'recent';
                $('#sortingfilter').val(this.sortFilter);
                if (this.sortFilter !== 'recent') {
                    $('#clSortBtn').addClass('has-filter');
                    $('#sortClearBtn').show();
                }
                $('#clSortDropdownPanel .cl-sort-item').removeClass('selected').attr('aria-selected', 'false').find('.cl-sort-check').hide();
                $(`#sort-opt-${this.sortFilter}`).addClass('selected').attr('aria-selected', 'true').find('.cl-sort-check').show();
            }
            if (params.has('page')) {
                const page = parseInt(params.get('page'), 10);
                if (!isNaN(page) && page > 0) this.currentPage = page;
            }
        },

        hasCustomInitialFilters: function() {
            return !!(this.searchQuery || this.countryFilter || this.cityFilter || this.unspecifiedLocation || this.industryFilter || this.activityFilter || this.pipelineFilter || (this.sortFilter && this.sortFilter !== 'recent') || this.currentPage > 1 || (this.activeTab && this.activeTab !== 'all'));
        },

        syncUrl: function() {
            const params = new URLSearchParams();
            if (this.activeTab && this.activeTab !== 'all') params.set('tab', this.activeTab);
            if (this.searchQuery) params.set('search', this.searchQuery);
            if (this.countryFilter) params.set('country', this.countryFilter);
            if (this.cityFilter) params.set('city', this.cityFilter);
            if (this.unspecifiedLocation) params.set('unspecified_location', '1');
            if (this.industryFilter) params.set('industry', this.industryFilter);
            if (this.activityFilter) params.set('activity', this.activityFilter);
            if (this.pipelineFilter) params.set('pipeline', this.pipelineFilter);
            if (this.sortFilter && this.sortFilter !== 'recent') params.set('sort', this.sortFilter);
            if (this.currentPage > 1) params.set('page', this.currentPage);

            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.replaceState({
                tab: this.activeTab,
                search: this.searchQuery,
                country: this.countryFilter,
                city: this.cityFilter,
                unspecified_location: this.unspecifiedLocation,
                industry: this.industryFilter,
                activity: this.activityFilter,
                pipeline: this.pipelineFilter,
                sort: this.sortFilter,
                page: this.currentPage
            }, '', newUrl);
        },

        setTab: function(tabKey) {
            if (this.activeTab === tabKey) return;
            this.activeTab = tabKey;
            this.loadPage(1);
        },

        setSearch: function(query) {
            this.searchQuery = query;
            this.loadPage(1);
        },

        closeAllDropdowns: function() {
            $('.cl-loc-dropdown-panel, .cl-ind-dropdown-panel, .cl-act-dropdown-panel, .cl-pipe-dropdown-panel, .cl-sort-dropdown-panel, #clExportDropdownMenu').removeClass('show');
            $('#clLocBtn, #clIndBtn, #clActBtn, #clPipeBtn, #clSortBtn, #clExportDropdownBtn').removeClass('active show');
        },

        getCacheKey: function(page) {
            return `tab:${this.activeTab}|p:${page}|q:${this.searchQuery}|co:${this.countryFilter}|ci:${this.cityFilter}|un:${this.unspecifiedLocation}|ind:${this.industryFilter}|act:${this.activityFilter}|pip:${this.pipelineFilter}|srt:${this.sortFilter}`;
        },

        loadPage: function(page, bypassCache) {
            this.currentPage = page || 1;
            this.syncUrl();

            const cacheKey = this.getCacheKey(this.currentPage);
            const self = this;

            // SWR In-Memory cache check
            if (!bypassCache && this.cache[cacheKey]) {
                const cached = this.cache[cacheKey];
                this.renderResponse(cached);
                this.fetchPage(this.currentPage, cacheKey, true);
                return;
            }

            this.fetchPage(this.currentPage, cacheKey, false);
        },

        fetchPage: function(page, cacheKey, isBackground) {
            const self = this;
            if (!isBackground) {
                $('.loadingMessagestaffingpartnerajax').css({ display: 'block' });
            }

            if (this.activeXhr && typeof this.activeXhr.abort === 'function') {
                this.activeXhr.abort();
            }

            this.activeXhr = $.ajax({
                url: `{{ route('service_company.consultancy.data_ajax') }}?page=${page}`,
                type: 'GET',
                data: {
                    tab: self.activeTab,
                    search: self.searchQuery,
                    cus_search: self.searchQuery,
                    country: self.countryFilter,
                    country_id: self.countryFilter,
                    city: self.cityFilter,
                    city_id: self.cityFilter,
                    unspecified_location: self.unspecifiedLocation ? 1 : 0,
                    industry: self.industryFilter,
                    industry_id: self.industryFilter,
                    activity: self.activityFilter,
                    pipeline: self.pipelineFilter,
                    sortingfilter: self.sortFilter,
                    per_page: 10
                },
                success: function(response) {
                    $('.loadingMessagestaffingpartnerajax').css({ display: 'none' });
                    self.cache[cacheKey] = response;
                    self.renderResponse(response);
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        $('.loadingMessagestaffingpartnerajax').css({ display: 'none' });
                    }
                }
            });
        },

        renderResponse: function(response) {
            if (!response) return;
            $('#staffingpartnerajax').html(response.html);
            $('#paginationstaffingpartner').html(response.pagination);

            if (response.total !== undefined) {
                if (this.searchQuery) {
                    $('#clSearchMatchCount').text(`${response.total} matches`).show();
                } else {
                    $('#clSearchMatchCount').hide();
                }
            }

            if (response.agencyDataCnt) {
                window.updateAgencyTabCounters(response.agencyDataCnt);
            }

            // Update reactive facet counters if provided
            if (response.locationFacets) {
                window.initialLocationFacets = response.locationFacets;
            }
            if (response.recencyCounts) {
                window.initialRecencyCounts = response.recencyCounts;
                $('#actCountAll').text(response.recencyCounts.all || 0);
                $('#actCount7d').text(response.recencyCounts['7d'] || 0);
                $('#actCount30d').text(response.recencyCounts['30d'] || 0);
                $('#actCount90d').text(response.recencyCounts['90d'] || 0);
                $('#actCountDormant').text(response.recencyCounts.dormant || 0);
                $('#actCountNever').text(response.recencyCounts.never || 0);
            }
            if (response.pipelineCounts) {
                window.initialPipelineCounts = response.pipelineCounts;
                $('#pipeCountAll').text(response.pipelineCounts.all || 0);
                $('#pipeCountActive').text(response.pipelineCounts.active || 0);
                $('#pipeCountNone').text(response.pipelineCounts.none || 0);
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

            // Keyboard Shortcuts: Ctrl+K / Cmd+K / Esc
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

            // Row Click Delegation to Open Drawer (ignoring actions, links, buttons, inputs)
            $(document).on('click', '.tbl-r, .cl-primary-card, .show-detail-direct', function(e) {
                if ($(e.target).closest('input, button, a, .tbl-cb, .tbl-cell-check, .dropdown-menu, .resend-agency-invite').length > 0) {
                    return;
                }
                const id = $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id') || $(this).find('[data-row-id]').first().data('row-id');
                if (id) {
                    window.openAgencyDrawer(id);
                }
            });

            // Drawer Close Handlers & Overlay Clicks
            $(document).on('click', '#kt_activitiesDirectClient_close, #kt_activitiesDirectClient_close_btn, .cd-close-drawer, .drawer-overlay, .agency-drawer-overlay', function(e) {
                if (e) e.preventDefault();
                window.closeAgencyDrawer();
            });

            // Auto close drawer when clicking anywhere outside of the drawer
            $(document).on('click.agencyDrawerOutside mousedown.agencyDrawerOutside touchstart.agencyDrawerOutside', function(e) {
                var drawerEl = document.getElementById('kt_activitiesDirectClient');
                if (drawerEl && (drawerEl.classList.contains('drawer-on') || drawerEl.style.transform === 'none' || $(drawerEl).is(':visible'))) {
                    if ($(e.target).hasClass('drawer-overlay') || $(e.target).hasClass('agency-drawer-overlay')) {
                        window.closeAgencyDrawer();
                        return;
                    }
                    if (!drawerEl.contains(e.target) && !$(e.target).closest('.tbl-r, .cl-primary-card, .show-detail-direct, #open_add_agency_drawer, .swal2-container, .modal, .select2-container').length) {
                        window.closeAgencyDrawer();
                    }
                }
            });

            // ─── Add Agency Drawer: Smart Autocomplete & Search ───
            let agencySearchTimeout = null;

            function clearAgencySelection() {
                $('#staffing_partners_search_input').val('');
                $('#staffing_partners_email').val('');
                $('#selected_agency_preview').addClass('d-none');
                $('#agency_autocomplete_dropdown').addClass('d-none').empty();
                $('#btn_clear_agency_search').addClass('d-none');
                $('#staffing_email_feedback').hide().empty();
            }

            function openAddAgencyDrawer() {
                $('#add_agency_drawer_overlay').fadeIn(150);
                $('#add_agency_drawer').addClass('is-active').css('display', 'flex').hide().fadeIn(150);
                setTimeout(function() {
                    $('#staffing_partners_search_input').focus();
                }, 160);
            }

            function closeAddAgencyDrawer() {
                $('#add_agency_drawer_overlay').fadeOut(150);
                $('#add_agency_drawer').fadeOut(150, function() {
                    $(this).removeClass('is-active');
                });
                clearAgencySelection();
            }

            $('#open_add_agency_drawer').on('click', function(e) {
                if (e) e.preventDefault();
                openAddAgencyDrawer();
            });

            $(document).on('click', '.add_staffing_close, #add_agency_drawer_overlay, #add_agency_drawer_close_btn', function(e) {
                if (e) e.preventDefault();
                closeAddAgencyDrawer();
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#add_agency_drawer').is(':visible')) {
                    closeAddAgencyDrawer();
                }
            });

            $(document).on('click', '#btn_clear_agency_search, #btn_remove_selected_agency', function(e) {
                if (e) e.preventDefault();
                clearAgencySelection();
                $('#staffing_partners_search_input').focus();
            });

            // Live debounced search input
            $(document).on('input', '#staffing_partners_search_input', function() {
                const query = $(this).val().trim();
                const $dropdown = $('#agency_autocomplete_dropdown');

                if (query.length > 0) {
                    $('#btn_clear_agency_search').removeClass('d-none');
                } else {
                    $('#btn_clear_agency_search').addClass('d-none');
                }

                if ($('#selected_agency_preview').is(':visible')) {
                    $('#selected_agency_preview').addClass('d-none');
                    $('#staffing_partners_email').val('');
                }

                clearTimeout(agencySearchTimeout);

                if (query.length < 2) {
                    $dropdown.addClass('d-none').empty();
                    return;
                }

                $dropdown.html('<div class="p-3 text-center text-muted fs-8"><span class="spinner-border spinner-border-sm me-2 text-primary" style="width:12px; height:12px;"></span>Searching agencies...</div>').removeClass('d-none');

                agencySearchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('service_company.consultancy.search_agency_autocomplete') }}",
                        type: 'GET',
                        data: { q: query },
                        dataType: 'json',
                        success: function(res) {
                            $dropdown.empty();
                            const results = (res && res.data) ? res.data : [];

                            if (results.length === 0) {
                                let noResultHtml = '<div class="p-3 text-muted fs-8 text-center">No registered agencies found.</div>';
                                if (query.includes('@')) {
                                    noResultHtml += `
                                        <div class="agency-search-external-opt p-2 px-3 border-top d-flex align-items-center justify-content-between cursor-pointer bg-hover-light-primary" data-email="${escapeAttr(query)}" style="transition:background 0.15s ease;">
                                            <div class="d-flex align-items-center gap-2 min-w-0">
                                                <div style="width:28px; height:28px; border-radius:6px; background:#eff6ff; color:#009ef7; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0;">✉</div>
                                                <div class="min-w-0">
                                                    <div class="fs-8 fw-semibold text-gray-900 text-truncate">Invite external agency</div>
                                                    <div class="fs-9 text-muted text-truncate">${escapeAttr(query)}</div>
                                                </div>
                                            </div>
                                            <span class="badge badge-light-primary fs-9 fw-bold">Invite</span>
                                        </div>
                                    `;
                                }
                                $dropdown.html(noResultHtml);
                            } else {
                                results.forEach(function(ag) {
                                    let badgeHtml = '';
                                    let itemClass = 'agency-search-item';
                                    if (ag.connection_status === 'connected') {
                                        badgeHtml = '<span class="badge badge-light-success fs-9 fw-semibold">Connected</span>';
                                        itemClass += ' is-connected opacity-75';
                                    } else if (ag.connection_status === 'pending') {
                                        badgeHtml = '<span class="badge badge-light-warning fs-9 fw-semibold">Invite Pending</span>';
                                        itemClass += ' is-pending opacity-75';
                                    } else {
                                        badgeHtml = '<span class="badge badge-light-primary fs-9 fw-bold">Select Agency</span>';
                                    }

                                    let avatarHtml = '';
                                    if (ag.logo_url) {
                                        avatarHtml = `<img src="${escapeAttr(ag.logo_url)}" alt="" style="width:30px; height:30px; border-radius:6px; object-fit:contain; border:1px solid #e2e8f0; flex-shrink:0;">`;
                                    } else {
                                        avatarHtml = `<div style="width:30px; height:30px; border-radius:6px; background:#eef2ff; color:#4f46e5; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11px; flex-shrink:0;">${escapeAttr(ag.initials || 'AG')}</div>`;
                                    }

                                    const itemHtml = `
                                        <div class="${itemClass} p-2 px-3 border-bottom d-flex align-items-center justify-content-between cursor-pointer bg-hover-light" 
                                             data-id="${ag.id}" 
                                             data-name="${escapeAttr(ag.company_name)}" 
                                             data-email="${escapeAttr(ag.email)}" 
                                             data-logo="${escapeAttr(ag.logo_url || '')}"
                                             data-initials="${escapeAttr(ag.initials || 'AG')}"
                                             data-status="${ag.connection_status}"
                                             style="transition:background 0.15s ease;">
                                            <div class="d-flex align-items-center gap-2 min-w-0">
                                                ${avatarHtml}
                                                <div class="min-w-0">
                                                    <div class="fs-8 fw-bold text-gray-900 text-truncate">${escapeAttr(ag.company_name)}</div>
                                                    <div class="fs-9 text-muted text-truncate">${escapeAttr(ag.email)}</div>
                                                </div>
                                            </div>
                                            <div class="ms-2 flex-shrink-0">
                                                ${badgeHtml}
                                            </div>
                                        </div>
                                    `;
                                    $dropdown.append(itemHtml);
                                });

                                if (query.includes('@')) {
                                    $dropdown.append(`
                                        <div class="agency-search-external-opt p-2 px-3 d-flex align-items-center justify-content-between cursor-pointer bg-hover-light-primary" data-email="${escapeAttr(query)}" style="transition:background 0.15s ease;">
                                            <div class="d-flex align-items-center gap-2 min-w-0">
                                                <div style="width:28px; height:28px; border-radius:6px; background:#eff6ff; color:#009ef7; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0;">✉</div>
                                                <div class="min-w-0">
                                                    <div class="fs-8 fw-semibold text-gray-900 text-truncate">Invite external email directly</div>
                                                    <div class="fs-9 text-muted text-truncate">${escapeAttr(query)}</div>
                                                </div>
                                            </div>
                                            <span class="badge badge-light-primary fs-9 fw-bold">Invite</span>
                                        </div>
                                    `);
                                }
                            }
                        },
                        error: function() {
                            $dropdown.html('<div class="p-3 text-danger fs-8 text-center">Failed to load agency search results.</div>');
                        }
                    });
                }, 300);
            });

            // Select an Agency from Autocomplete list
            $(document).on('click', '.agency-search-item', function() {
                const status = $(this).data('status');
                const name = $(this).data('name');
                const email = $(this).data('email');
                const logo = $(this).data('logo');
                const initials = $(this).data('initials');

                if (status === 'connected') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'info', title: 'Already Connected', text: `"${name}" is already an active staffing partner with your organization.` });
                    } else {
                        alert(`"${name}" is already an active staffing partner.`);
                    }
                    return;
                }

                if (status === 'pending') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'info', title: 'Invite Pending', text: `An invitation has already been sent to "${name}" (${email}) and is pending response.` });
                    } else {
                        alert(`An invitation has already been sent to "${name}".`);
                    }
                    return;
                }

                // Available to select
                $('#staffing_partners_email').val(email);
                $('#staffing_partners_search_input').val(name);
                $('#selected_agency_name').text(name);
                $('#selected_agency_email').text(email);

                if (logo) {
                    $('#selected_agency_avatar').html(`<img src="${escapeAttr(logo)}" alt="" style="width:100%; height:100%; object-fit:contain; border-radius:6px;">`);
                } else {
                    $('#selected_agency_avatar').text(initials || 'AG');
                }

                $('#selected_agency_preview').removeClass('d-none');
                $('#agency_autocomplete_dropdown').addClass('d-none').empty();
                $('#btn_clear_agency_search').removeClass('d-none');
                $('#staffing_email_feedback').hide().empty();
            });

            // Select external email option from dropdown
            $(document).on('click', '.agency-search-external-opt', function() {
                const email = $(this).data('email');
                $('#staffing_partners_email').val(email);
                $('#staffing_partners_search_input').val(email);
                $('#agency_autocomplete_dropdown').addClass('d-none').empty();
                $('#btn_clear_agency_search').removeClass('d-none');
                $('#staffing_email_feedback').hide().empty();
            });

            // Close autocomplete dropdown on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#staffing_partners_search_input, #agency_autocomplete_dropdown').length) {
                    $('#agency_autocomplete_dropdown').addClass('d-none');
                }
            });

            // Submit Agency Invitation via Email
            $(document).on('click', '#submit_staffing_partners', function(e) {
                if (e) e.preventDefault();
                let email = $('#staffing_partners_email').val().trim();
                if (!email) {
                    email = $('#staffing_partners_search_input').val().trim();
                }

                if (!email || !email.includes('@')) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'warning', title: 'Agency Email Required', text: 'Please search and select a registered agency, or enter a valid email address.' });
                    } else if (typeof swal !== 'undefined') {
                        swal({ title: 'Warning!', text: 'Please enter a valid email address.', type: 'warning' });
                    } else {
                        alert('Please enter a valid email address.');
                    }
                    return;
                }

                const $btn = $('#submit_staffing_partners');
                const origBtnHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending Invite...');

                const url = "{{ route('service_company.consultancy.staffing_invitation', 0) }}";
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { 
                        staffing_partners_email: email,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(origBtnHtml);
                        clearAgencySelection();
                        if (response.status === 'warning') {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'warning', title: 'Notice', text: response.message });
                            } else {
                                alert(response.message);
                            }
                        } else if (response.status === 'success') {
                            closeAddAgencyDrawer();
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'success', title: 'Invitation Sent!', text: response.message || 'Invitation sent successfully.' });
                            } else {
                                alert('Invitation sent successfully.');
                            }
                            self.loadPage(1, true);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(origBtnHtml);
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to send invitation.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        } else {
                            alert(msg);
                        }
                    }
                });
            });

            // Share Registration Link Copy Button
            $(document).on('click', '#kt_share_earn_link_copy_button', function(e) {
                if (e) e.preventDefault();
                const copyInput = document.getElementById("kt_share_earn_link_input");
                if (copyInput) {
                    copyInput.select();
                    copyInput.setSelectionRange(0, 99999);
                    const linkValue = copyInput.value;
                    const $btn = $('#kt_share_earn_link_copy_button');
                    const origHtml = $btn.html();

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(linkValue).then(function() {
                            $btn.html('<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="20 6 9 17 4 12"></polyline></svg> Copied!');
                            setTimeout(function() { $btn.html(origHtml); }, 2000);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Registration link copied to clipboard!',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        }).catch(function() {
                            document.execCommand('copy');
                            $btn.html('Copied!');
                            setTimeout(function() { $btn.html(origHtml); }, 2000);
                        });
                    } else {
                        document.execCommand('copy');
                        $btn.html('Copied!');
                        setTimeout(function() { $btn.html(origHtml); }, 2000);
                    }
                }
            });

            // Resend Agency Invitation Click Handler
            $(document).on('click', '.resend-agency-invite', function(e) {
                if (e) { e.preventDefault(); e.stopPropagation(); }
                const email = $(this).data('email');
                if (!email) return;
                const $btn = $(this);
                const originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" style="width:11px; height:11px;"></span> Resending...');

                $.ajax({
                    url: "{{ route('service_company.consultancy.staffing_invitation', 0) }}",
                    type: 'POST',
                    data: {
                        staffing_partners_email: email,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        if (response.status === 'success') {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'success', title: 'Invitation Resent', text: response.message || 'Invitation resent successfully.' });
                            } else {
                                alert('Invitation resent successfully.');
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'warning', title: 'Notice', text: response.message });
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to resend invitation.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        } else {
                            alert(msg);
                        }
                    }
                });
            });

            // Cancel Agency Invitation Click Handler
            $(document).on('click', '.cancel-agency-invite', function(e) {
                if (e) { e.preventDefault(); e.stopPropagation(); }
                const invId = $(this).data('invitation-id');
                if (!invId) return;

                const confirmed = confirm('Are you sure you want to cancel this pending partnership invitation?');
                if (!confirmed) return;

                const $btn = $(this);
                const originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" style="width:11px; height:11px;"></span> Cancelling...');

                $.ajax({
                    url: "{{ url('/service/consultancy/cancel_invitation') }}/" + invId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        if (response.status === 'success') {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'success', title: 'Cancelled', text: response.message || 'Invitation cancelled successfully.' });
                            } else {
                                alert('Invitation cancelled successfully.');
                            }
                            self.loadPage(1, true);
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'warning', title: 'Notice', text: response.message });
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to cancel invitation.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        } else {
                            alert(msg);
                        }
                    }
                });
            });

            // Outside click closes popovers
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.cl-loc-dropdown-wrapper, .cl-ind-dropdown-wrapper, .cl-act-dropdown-wrapper, .cl-pipe-dropdown-wrapper, .cl-sort-dropdown-wrapper').length) {
                    self.closeAllDropdowns();
                }
            });

            // Browser Back/Forward navigation support
            window.addEventListener('popstate', function(e) {
                if (e.state) {
                    self.activeTab = e.state.tab || 'all';
                    self.searchQuery = e.state.search || '';
                    self.countryFilter = e.state.country || '';
                    self.cityFilter = e.state.city || '';
                    self.unspecifiedLocation = e.state.unspecified_location || false;
                    self.industryFilter = e.state.industry || '';
                    self.activityFilter = e.state.activity || '';
                    self.pipelineFilter = e.state.pipeline || '';
                    self.sortFilter = e.state.sort || 'recent';
                    self.currentPage = e.state.page || 1;

                    $('.cl-stat-grid .cl-card').removeClass('active');
                    $(`.cl-stat-grid .cl-card[data-tab="${self.activeTab}"]`).addClass('active');
                    $('#kt_tagify_2').val(self.searchQuery);
                    $('#country').val(self.countryFilter);
                    $('#industry').val(self.industryFilter);
                    $('#activity').val(self.activityFilter);
                    $('#pipeline').val(self.pipelineFilter);
                    $('#sortingfilter').val(self.sortFilter);

                    self.loadPage(self.currentPage, true);
                }
            });
        }
    };

    window.AgencyListingManager = AgencyListingManager;

    // Pagination Click Handler (Universal: buttons & anchors)
    $(document).on('click', '#paginationstaffingpartner .pagi-page, #paginationstaffingpartner .pagi-arrow, #paginationstaffingpartner a', function(e) {
        e.preventDefault();
        if ($(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active') || $(this).hasClass('pagi-active-disabled')) return;
        let page = $(this).data('page');
        if (!page && $(this).attr('href')) {
            const pageMatch = $(this).attr('href').match(/page=(\d+)/);
            if (pageMatch) page = parseInt(pageMatch[1], 10);
        }
        if (page) {
            AgencyListingManager.loadPage(parseInt(page, 10));
            const $target = $('#staffingpartnerajax');
            if ($target.length) {
                $('html, body').animate({ scrollTop: $target.offset().top - 120 }, 200);
            }
        }
    });

    $(document).ready(function() {
        AgencyListingManager.init();
    });
</script>
@endpush
@endsection
