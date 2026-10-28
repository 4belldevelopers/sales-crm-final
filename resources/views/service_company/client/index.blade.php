@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/clientList.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
<style>
/* ── Guaranteed Card-Based Header Design ── */
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
  background: #fef3c7 !important;
  border: 1px solid #fde68a !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #d97706 !important;
  flex-shrink: 0 !important;
  box-shadow: 0 1px 2px rgba(217, 119, 6, 0.06) !important;
}
.cl-header-icon-box svg {
  color: #d97706 !important;
  stroke: #d97706 !important;
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
.cl-export-btn:hover, .cl-export-btn:focus, .cl-export-btn.show {
  background: #f8fafc !important;
  border-color: #cbd5e1 !important;
  color: #0f172a !important;
}
.cl-export-btn:disabled {
  opacity: 0.65 !important;
  cursor: not-allowed !important;
}
.cl-export-btn svg {
  color: #0f172a !important;
  stroke: #0f172a !important;
}
.cl-export-chevron {
  transition: transform 0.15s ease !important;
  margin-left: 2px !important;
}
.cl-export-btn.show .cl-export-chevron {
  transform: rotate(180deg) !important;
}
.cl-export-dropdown-menu {
  min-width: 270px !important;
  padding: 6px !important;
  border-radius: 10px !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06) !important;
  background: #ffffff !important;
  margin-top: 6px !important;
  z-index: 1050 !important;
}
.cl-export-option {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  padding: 8px 12px !important;
  border-radius: 8px !important;
  transition: all 0.12s ease !important;
  text-decoration: none !important;
  cursor: pointer !important;
}
.cl-export-option:hover {
  background: #f1f5f9 !important;
}
.cl-export-opt-icon {
  width: 32px !important;
  height: 32px !important;
  border-radius: 6px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
}
.cl-export-opt-icon.csv {
  background: #eff6ff !important;
  color: #2563eb !important;
}
.cl-export-opt-icon.all {
  background: #f0fdf4 !important;
  color: #16a34a !important;
}
.cl-export-opt-icon.pdf {
  background: #fef2f2 !important;
  color: #dc2626 !important;
}
.cl-export-opt-text {
  display: flex !important;
  flex-direction: column !important;
  line-height: 1.25 !important;
}
.cl-export-opt-title {
  font-size: 13px !important;
  font-weight: 600 !important;
  color: #1e293b !important;
}
.cl-export-opt-sub {
  font-size: 11px !important;
  font-weight: 400 !important;
  color: #64748b !important;
  margin-top: 2px !important;
}

/* ── 100% Client-Side Dedicated Executive Print-to-PDF Engine ── */
@media print {
  body * {
    visibility: hidden !important;
  }
  #cl-print-report-canvas, #cl-print-report-canvas * {
    visibility: visible !important;
  }
  #cl-print-report-canvas {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    display: block !important;
    background: #ffffff !important;
    color: #0f172a !important;
    font-family: 'Poppins', sans-serif !important;
    padding: 10px 15px !important;
  }
  @page {
    size: A4 landscape;
    margin: 8mm 10mm 8mm 10mm;
  }
  .print-report-header {
    border-bottom: 2px solid #009ef7;
    padding-bottom: 10px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
  }
  .print-report-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
  }
  .print-report-sub {
    font-size: 10px;
    color: #64748b;
    margin-top: 2px;
  }
  .print-report-meta {
    text-align: right;
    font-size: 9px;
    color: #475569;
    line-height: 1.4;
  }
  .print-kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
    margin-bottom: 14px;
  }
  .print-kpi-card {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 6px 10px;
    background: #f8fafc;
  }
  .print-kpi-num {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
  }
  .print-kpi-lbl {
    font-size: 8.5px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .print-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 9.5px;
  }
  .print-table th {
    background-color: #f1f5f9;
    color: #1e293b;
    font-weight: 700;
    padding: 5px 7px;
    border-bottom: 2px solid #cbd5e1;
    text-align: left;
  }
  .print-table td {
    padding: 5px 7px;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
  }
  .print-table tr {
    page-break-inside: avoid !important;
  }
  .print-footer {
    margin-top: 14px;
    padding-top: 6px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    font-size: 8.5px;
    color: #94a3b8;
  }
}
.cl-add-btn-primary {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  background: #f59e0b !important;
  border: none !important;
  border-radius: 8px !important;
  padding: 8px 20px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  cursor: pointer !important;
  text-decoration: none !important;
  box-shadow: 0 1px 3px rgba(245, 158, 11, 0.25) !important;
  transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease !important;
  white-space: nowrap !important;
}
.cl-add-btn-primary:hover {
  background: #d97706 !important;
  color: #0f172a !important;
  box-shadow: 0 2px 6px rgba(217, 119, 6, 0.35) !important;
  transform: translateY(-0.5px) !important;
}
.cl-add-btn-primary svg {
  color: #0f172a !important;
  stroke: #0f172a !important;
}

/* ── Modern Metronic Location Combobox (15.6-inch & Desktop Responsive) ── */
.cl-loc-dropdown-wrapper {
  position: relative;
  display: inline-block;
}
.cl-loc-combobox-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  height: 42px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 0 12px;
  min-width: 155px;
  max-width: 210px;
  cursor: pointer;
  user-select: none;
  transition: all 0.15s ease;
}
.cl-loc-combobox-btn:hover, .cl-loc-combobox-btn.active {
  border-color: #cbd5e1;
  background: #f8fafc;
}
.cl-loc-combobox-btn.has-filter {
  border-color: #f59e0b;
  background: #fffbeb;
}
.cl-loc-combobox-btn.has-filter .cl-pill-val {
  color: #b45309;
}
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
.cl-loc-dropdown-panel.show {
  display: flex;
}
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
.cl-loc-search-clear:hover {
  color: #0f172a;
  background: #e2e8f0;
}
.cl-loc-dropdown-body {
  max-height: 290px;
  overflow-y: auto;
  padding: 6px 0;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}
.cl-loc-dropdown-body::-webkit-scrollbar {
  width: 5px;
}
.cl-loc-dropdown-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
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
.cl-loc-item:hover {
  background: #f1f5f9;
}
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
.cl-loc-item.selected .cl-loc-badge {
  background: #fde68a;
  color: #92400e;
}
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
.cl-loc-expand-btn:hover {
  background: #e2e8f0;
  color: #334155;
}
.cl-loc-expand-btn.expanded svg {
  transform: rotate(90deg);
}
.cl-loc-city-list {
  display: none;
  background: #f8fafc;
  border-left: 2px solid #e2e8f0;
  margin-left: 24px;
  padding: 2px 0;
}
.cl-loc-city-list.show {
  display: block;
}
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
.cl-loc-city-item:hover {
  background: #f1f5f9;
}
.cl-loc-city-item.selected {
  background: #fef3c7;
  color: #b45309;
  font-weight: 600;
}
.cl-loc-divider {
  height: 1px;
  background: #f1f5f9;
  margin: 4px 0;
}
.cl-loc-empty {
  padding: 18px 14px;
  text-align: center;
  font-size: 11.5px;
  color: #94a3b8;
}

/* ── Guaranteed Enterprise Industry Combobox Filter ── */
.cl-ind-dropdown-wrapper {
  position: relative;
  display: inline-block;
}
.cl-ind-combobox-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  height: 42px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 0 12px;
  min-width: 155px;
  max-width: 220px;
  cursor: pointer;
  user-select: none;
  transition: all 0.15s ease;
  position: relative;
}
.cl-ind-combobox-btn:hover, .cl-ind-combobox-btn.active {
  border-color: #cbd5e1;
  background: #f8fafc;
}
.cl-ind-combobox-btn.has-filter {
  border-color: #009ef7;
  background: #f1faff;
}
.cl-ind-combobox-btn.has-filter .cl-pill-val {
  color: #009ef7;
  font-weight: 600;
}
.cl-ind-combobox-btn.has-filter .cl-pill-icon svg {
  color: #009ef7;
  stroke: #009ef7;
}
.cl-pill-clear {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #e2e8f0;
  color: #64748b;
  font-size: 10px;
  line-height: 1;
  cursor: pointer;
  margin-left: 2px;
  transition: all 0.12s ease;
}
.cl-pill-clear:hover {
  background: #cbd5e1;
  color: #0f172a;
}
.cl-ind-combobox-btn.has-filter .cl-pill-clear {
  background: #bae6fd;
  color: #0284c7;
}
.cl-ind-combobox-btn.has-filter .cl-pill-clear:hover {
  background: #0284c7;
  color: #ffffff;
}
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
.cl-ind-dropdown-panel.show {
  display: flex;
}
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
.cl-ind-search-clear:hover {
  color: #0f172a;
  background: #e2e8f0;
}
.cl-ind-dropdown-body {
  max-height: 300px;
  overflow-y: auto;
  padding: 6px 0;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}
.cl-ind-dropdown-body::-webkit-scrollbar {
  width: 5px;
}
.cl-ind-dropdown-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
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
.cl-ind-chips-row {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}
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
.cl-ind-chip:hover {
  border-color: #009ef7;
  color: #009ef7;
  background: #f1faff;
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
.cl-ind-section-hdr:first-child {
  margin-top: 0;
  border-top: none;
}
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
.cl-ind-item:hover, .cl-ind-item.focused {
  background: #f1f5f9;
}
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
.cl-ind-mark {
  background: #fef08a;
  color: #0f172a;
  padding: 0 2px;
  border-radius: 2px;
}
.cl-ind-empty {
  padding: 24px 16px;
  text-align: center;
  color: #64748b;
}
.cl-ind-empty-icon {
  margin-bottom: 6px;
  color: #94a3b8;
}
.cl-ind-empty-title {
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 2px;
}
.cl-ind-empty-sub {
  font-size: 11.5px;
  color: #64748b;
  margin-bottom: 8px;
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
.cl-ind-chip.active {
  background: #009ef7 !important;
  border-color: #009ef7 !important;
  color: #ffffff !important;
}

/* ── Sortable Table Headers ── */
.cl-sortable-th {
  cursor: pointer !important;
  user-select: none;
  transition: background-color 0.15s ease;
}
.cl-sortable-th:hover {
  background-color: #f8fafc !important;
}
.cl-sortable-th.active-sort {
  color: #2563eb !important;
}
.cl-sortable-th.active-sort svg {
  color: #2563eb !important;
  stroke: #2563eb !important;
}
.cl-sortable-th.active-sort-asc .tbl-th-sort {
  transform: rotate(180deg);
}

/* ── Guaranteed Enterprise Metronic Sort Combobox ── */
.cl-sort-dropdown-wrapper {
  position: relative;
  display: inline-block;
}
.cl-sort-combobox-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  height: 42px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 0 12px;
  min-width: 155px;
  max-width: 220px;
  cursor: pointer;
  user-select: none;
  transition: all 0.15s ease;
  position: relative;
  font-family: inherit;
  font-size: inherit;
  text-align: left;
  outline: none;
}
.cl-sort-combobox-btn:focus-visible {
  border-color: #009ef7;
  box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.15);
}
.cl-sort-combobox-btn:hover,
.cl-sort-combobox-btn.active {
  border-color: #cbd5e1;
  background: #f8fafc;
}
.cl-sort-combobox-btn.is-custom-sorted {
  border-color: #cbd5e1;
  background: #f8fafc;
}
.cl-sort-combobox-btn.is-custom-sorted .cl-pill-val {
  color: #0f172a;
  font-weight: 600;
}
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
.cl-sort-dropdown-panel.show {
  display: flex;
}
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
.cl-sort-item:hover,
.cl-sort-item:focus {
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
.cl-sort-item-right {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-shrink: 0;
}
.cl-sort-direction-badge {
  font-size: 9.5px;
  font-weight: 600;
  padding: 1.5px 5.5px;
  border-radius: 4px;
  background: #f1f5f9;
  color: #64748b;
  letter-spacing: 0.02em;
}
.cl-sort-item.selected .cl-sort-direction-badge {
  background: #dbeafe;
  color: #0284c7;
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

/* ── Guaranteed Enterprise Metronic Activity & Pipeline Popovers ── */
.cl-act-dropdown-wrapper, .cl-pipe-dropdown-wrapper {
  position: relative;
  display: inline-block;
}
.cl-act-combobox-btn, .cl-pipe-combobox-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  height: 42px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 0 12px;
  min-width: 145px;
  max-width: 215px;
  cursor: pointer;
  user-select: none;
  transition: all 0.15s ease;
  position: relative;
}
.cl-act-combobox-btn:hover,
.cl-act-combobox-btn.active,
.cl-pipe-combobox-btn:hover,
.cl-pipe-combobox-btn.active {
  border-color: #cbd5e1;
  background: #f8fafc;
}
.cl-act-combobox-btn.is-active-filter,
.cl-pipe-combobox-btn.is-active-filter {
  border-color: #009ef7;
  background: #f0f9ff;
}
.cl-act-combobox-btn.is-active-filter .cl-pill-val,
.cl-pipe-combobox-btn.is-active-filter .cl-pill-val {
  color: #009ef7;
  font-weight: 600;
}
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
.cl-act-dropdown-panel.show, .cl-pipe-dropdown-panel.show {
  display: flex;
}
.cl-act-section-hdr, .cl-pipe-section-hdr {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94a3b8;
  padding: 6px 14px 4px 14px;
  user-select: none;
}
.cl-act-section-hdr:not(:first-child) {
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
.cl-act-dot, .cl-pipe-dot, .cl-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
  flex-shrink: 0;
}
.cl-dot-green { background: #10b981; }
.cl-dot-blue { background: #3b82f6; }
.cl-dot-amber { background: #f59e0b; }
.cl-dot-slate { background: #94a3b8; }
.cl-dot-purple { background: #8b5cf6; }
.cl-dot-violet { background: #6366f1; }
.cl-dot-all { background: #64748b; }

/* Engagement Badges */
.cl-touch-hot strong { color: #059669 !important; }
.cl-touch-warm strong { color: #2563eb !important; }
.cl-touch-stale strong { color: #d97706 !important; }
.cl-touch-dormant strong { color: #64748b !important; }
.cl-touch-never strong { color: #7c3aed !important; }

/* ── 15.6-inch Screen Optimization (1366x768 & 1920x1080) ── */
@media (max-width: 1400px) {
  .cl-filter-bar {
    gap: 7px !important;
  }
  .cl-dropdown-pill, .cl-loc-combobox-btn, .cl-ind-combobox-btn, .cl-act-combobox-btn, .cl-pipe-combobox-btn, .cl-sort-combobox-btn {
    min-width: 125px !important;
    padding: 0 8px !important;
    height: 40px !important;
  }
  .cl-search-inp {
    height: 40px !important;
  }
}
.cd-activities-drawer {
  width: 80% !important;
  max-width: 80vw !important;
  min-width: 0 !important;
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
</style>
@endpush
@section('content')
@php
    $totalClientsCount = (int)($clientDataCnt['clientAll'] ?? 0);
    $internalClientsCount = (int)($clientDataCnt['clientCustom'] ?? 0);
    $directClientsCount = (int)($clientDataCnt['clientDirect'] ?? 0);
    $hiringClientsCount = (int)($clientDataCnt['clientHiring'] ?? 0);
    $inactiveClientsCount = (int)($clientDataCnt['clientInactive'] ?? max(0, $totalClientsCount - $hiringClientsCount));
    $activeJobsCount = (int)($clientDataCnt['activeJobs'] ?? 0);
    $pendingRequestsCount = (int)($clientDataCnt['clientRequest'] ?? 0);

    $directPercent = ($totalClientsCount > 0) ? round(($directClientsCount / $totalClientsCount) * 100) : 100;
@endphp

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_client" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

        <!-- ── HEADER CARD ── -->
        <div class="cl-header cl-header-card">
            <div class="cl-header-left">
                <div class="cl-header-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="cl-header-title-row">
                        <h1 class="cl-header-title">Clients Tracker</h1>
                        <span class="cl-verified-badge" title="Verified ATS Module">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="m9 12 2 2 4-4" stroke-width="2.5"/>
                            </svg>
                        </span>
                    </div>
                    <div class="cl-header-subtitle">Manage client relationships, hiring activity and recruitment performance.</div>
                </div>
            </div>

            <div class="cl-header-actions">
                <div class="cl-export-wrapper dropdown">
                    <button type="button" class="cl-export-btn" id="clExportDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Export client portfolio">
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
                            <a class="dropdown-item cl-export-option" href="javascript:void(0);" onclick="triggerClientExport('filtered_csv')">
                                <div class="cl-export-opt-icon csv">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <path d="M8 13h2"/>
                                        <path d="M8 17h8"/>
                                        <path d="M14 13h2"/>
                                    </svg>
                                </div>
                                <div class="cl-export-opt-text">
                                    <span class="cl-export-opt-title">Export Filtered View (CSV)</span>
                                    <span class="cl-export-opt-sub">Current filtered clients only</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item cl-export-option" href="javascript:void(0);" onclick="triggerClientExport('all_csv')">
                                <div class="cl-export-opt-icon all">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                                        <path d="M18 14h-8"/>
                                        <path d="M15 18h-5"/>
                                        <path d="M10 6h8v4h-8V6Z"/>
                                    </svg>
                                </div>
                                <div class="cl-export-opt-text">
                                    <span class="cl-export-opt-title">Export All Clients (CSV)</span>
                                    <span class="cl-export-opt-sub">Full database snapshot</span>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1 border-gray-200"></li>
                        <li>
                            <a class="dropdown-item cl-export-option" href="javascript:void(0);" onclick="triggerClientExport('executive_pdf')">
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

                @if(in_array("add_clients", $userPermissions))
                <a href="{{ url('service_company/clients/create') }}" class="cl-add-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Add Internal Client</span>
                </a>
                @endif
            </div>
        </div>

        <!-- ── 6 STAT CARDS ── -->
        <div class="cl-stat-grid">
            {{-- Card 1: Total Clients --}}
            <a href="#" onclick="setClientTab('all', this); return false;" class="cl-card cl-stat-filter active" data-tab="all">
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
                        <div class="cl-card-num" id="span_clientAll">{{ $totalClientsCount }}</div>
                        <div class="cl-card-lbl">Total Clients</div>
                    </div>
                </div>
                <div class="cl-card-sub green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
                    <span>+{{ max($totalClientsCount, 1) }} this month</span>
                </div>
            </a>

            {{-- Card 2: Internal Clients --}}
            <a href="#" onclick="setClientTab('internal', this); return false;" class="cl-card cl-stat-filter" data-tab="internal">
                <div class="cl-card-top">
                    <div class="cl-card-icon indigo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientCustom">{{ $internalClientsCount }}</div>
                        <div class="cl-card-lbl">Internal Clients</div>
                    </div>
                </div>
                <div class="cl-card-sub blue">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
                    <span>{{ $internalClientsCount > 0 ? $internalClientsCount . ' active internal' : 'No internal clients yet' }}</span>
                </div>
            </a>

            {{-- Card 3: Direct Clients --}}
            <a href="#" onclick="setClientTab('direct', this); return false;" class="cl-card cl-stat-filter" data-tab="direct">
                <div class="cl-card-top">
                    <div class="cl-card-icon green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                            <path d="M9 22v-4h6v4"/>
                            <path d="M8 6h.01"/>
                            <path d="M16 6h.01"/>
                            <path d="M8 10h.01"/>
                            <path d="M16 10h.01"/>
                            <path d="M8 14h.01"/>
                            <path d="M16 14h.01"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientDirect">{{ $directClientsCount }}</div>
                        <div class="cl-card-lbl">Direct Clients</div>
                    </div>
                </div>
                <div class="cl-card-sub green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <polyline points="17 11 19 13 23 9"/>
                    </svg>
                    <span>{{ $directPercent }}% of total clients</span>
                </div>
            </a>

            {{-- Card 4: Active Companies --}}
            <a href="#" onclick="setClientTab('hiring', this); return false;" class="cl-card cl-stat-filter" data-tab="hiring">
                <div class="cl-card-top">
                    <div class="cl-card-icon green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            <line x1="12" y1="12" x2="12" y2="12.01"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientHiring">{{ $hiringClientsCount }}</div>
                        <div class="cl-card-lbl">Active Companies</div>
                    </div>
                </div>
                <div class="cl-card-sub green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="9" x2="20" y2="9"/>
                        <line x1="4" y1="15" x2="20" y2="15"/>
                        <line x1="10" y1="3" x2="8" y2="21"/>
                        <line x1="16" y1="3" x2="14" y2="21"/>
                    </svg>
                    <span>{{ $activeJobsCount > 0 ? $activeJobsCount . ' active position' . ($activeJobsCount > 1 ? 's' : '') : 'No active jobs' }}</span>
                </div>
            </a>

            {{-- Card 5: Inactive Companies --}}
            <a href="#" onclick="setClientTab('inactive', this); return false;" class="cl-card cl-stat-filter" data-tab="inactive">
                <div class="cl-card-top">
                    <div class="cl-card-icon pink">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="10" y1="15" x2="10" y2="9"/>
                            <line x1="14" y1="15" x2="14" y2="9"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientInactive">{{ $inactiveClientsCount }}</div>
                        <div class="cl-card-lbl">Inactive Companies</div>
                    </div>
                </div>
                <div class="cl-card-sub pink">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    <span>0 active positions</span>
                </div>
            </a>

            {{-- Card 6: Pending Requests --}}
            <a href="#" onclick="setClientTab('request', this); return false;" class="cl-card cl-stat-filter" data-tab="request">
                <div class="cl-card-top">
                    <div class="cl-card-icon purple">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientRequest">{{ $pendingRequestsCount }}</div>
                        <div class="cl-card-lbl">Pending Requests</div>
                    </div>
                </div>
                <div class="cl-card-sub purple">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 14 14"/>
                    </svg>
                    <span>{{ $pendingRequestsCount > 0 ? $pendingRequestsCount . ' action needed' : "You're all caught up" }}</span>
                </div>
            </a>
        </div>

        <!-- ── SEARCH & FILTER BAR ── -->
        <div class="cl-filter-bar">
            {{-- Search Input --}}
            <div class="cl-search-box">
                <svg class="cl-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="cl-search-inp" name="inputJobSearch" value="" placeholder="Search clients by name, industry, location..." id="kt_tagify_2" onchange="filtersdataget()" />
                <div id="loader_search" class="loader_search hidden_search" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);"></div>
            </div>

            {{-- Filter 1: Location Combobox --}}
            <div class="cl-loc-dropdown-wrapper" id="clLocDropdownWrapper">
                <div class="cl-loc-combobox-btn" id="clLocBtn" onclick="toggleLocationDropdown(event)">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
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

                {{-- Dropdown popover panel --}}
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
                        {{-- Rendered dynamically from window.initialLocationFacets & window.allCountriesList --}}
                    </div>
                </div>

                {{-- Hidden inputs to preserve backward compatibility --}}
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

                {{-- Popover Dropdown Panel --}}
                <div class="cl-ind-dropdown-panel" id="clIndDropdownPanel" role="listbox" aria-label="Industries Filter">
                    <div class="cl-ind-search-box">
                        <svg class="cl-ind-search-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" class="cl-ind-search-inp" id="clIndSearchInp" placeholder="Search 160+ industries or sectors..." autocomplete="off" oninput="handleIndustrySearchInput(this.value)" onkeydown="handleIndustryKeydown(event)" />
                        <span class="cl-ind-search-clear" id="clIndSearchClear" onclick="clearIndustrySearch()">✕</span>
                    </div>
                    <div class="cl-ind-dropdown-body" id="clIndDropdownBody">
                        {{-- Rendered dynamically from window.initialIndustriesManifest via User RAM --}}
                    </div>
                </div>

                {{-- Hidden input for backward compatibility --}}
                <input type="hidden" id="industryFilter" name="industry" value="" />
                <input type="hidden" id="industry_id" name="industry_id" value="" />
            </div>

            {{-- Filter 3: Enterprise Activity Recency Combobox Popover --}}
            <div class="cl-act-dropdown-wrapper" id="clActDropdownWrapper">
                <button type="button" class="cl-act-combobox-btn" id="clActBtn" onclick="toggleActivityDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="clActDropdownPanel" title="Filter by Client Engagement Recency">
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
                            <span class="cl-act-badge" id="actCountAll">0</span>
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
                            <span class="cl-act-badge" id="actCount7d">0</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-act-item" id="act-opt-30d" data-act-val="30d" onclick="selectActivityOption('30d', 'Active This Month', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-blue"></span>
                            <span class="cl-act-item-title">Active This Month (8–30d)</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCount30d">0</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-act-item" id="act-opt-90d" data-act-val="90d" onclick="selectActivityOption('90d', 'Needs Outreach', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-amber"></span>
                            <span class="cl-act-item-title">Needs Outreach (31–90d)</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCount90d">0</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-act-item" id="act-opt-dormant" data-act-val="dormant" onclick="selectActivityOption('dormant', 'Dormant (> 90d)', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-slate"></span>
                            <span class="cl-act-item-title">Dormant (> 90d Inactive)</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCountDormant">0</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-act-item" id="act-opt-never" data-act-val="never" onclick="selectActivityOption('never', 'Never Engaged', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-act-item-left">
                            <span class="cl-act-dot cl-dot-purple"></span>
                            <span class="cl-act-item-title">Never Engaged (No Touch)</span>
                        </div>
                        <div class="cl-act-item-right">
                            <span class="cl-act-badge" id="actCountNever">0</span>
                            <span class="cl-act-check" style="display:none;">✓</span>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="activityFilter" name="activity" value="" />
            </div>

            {{-- Filter 4: Enterprise Pipeline State Combobox Popover --}}
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
                            <span class="cl-pipe-badge" id="pipeCountAll">0</span>
                            <span class="cl-pipe-check">✓</span>
                        </div>
                    </div>
                    <div class="cl-pipe-item" id="pipe-opt-active" data-pipe-val="active" onclick="selectPipelineOption('active', 'Pipeline Moving', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-pipe-item-left">
                            <span class="cl-pipe-dot cl-dot-violet"></span>
                            <span class="cl-pipe-item-title">Pipeline Moving (Active)</span>
                        </div>
                        <div class="cl-pipe-item-right">
                            <span class="cl-pipe-badge" id="pipeCountActive">0</span>
                            <span class="cl-pipe-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-pipe-item" id="pipe-opt-none" data-pipe-val="none" onclick="selectPipelineOption('none', 'No Active Pipeline', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-pipe-item-left">
                            <span class="cl-pipe-dot cl-dot-slate"></span>
                            <span class="cl-pipe-item-title">No Active Pipeline</span>
                        </div>
                        <div class="cl-pipe-item-right">
                            <span class="cl-pipe-badge" id="pipeCountNone">0</span>
                            <span class="cl-pipe-check" style="display:none;">✓</span>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="pipelineFilter" name="pipeline" value="" />
            </div>

            {{-- Filter 5: Enterprise Metronic Sort Combobox Popover --}}
            <div class="cl-sort-dropdown-wrapper" id="clSortDropdownWrapper">
                <button type="button" class="cl-sort-combobox-btn" id="clSortBtn" onclick="toggleSortDropdown(event)" aria-haspopup="listbox" aria-expanded="false" aria-controls="clSortDropdownPanel" title="Sort Client Listing">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="4" x2="7" y2="20"/>
                            <polyline points="3 8 7 4 11 8"/>
                            <line x1="17" y1="4" x2="17" y2="20"/>
                            <polyline points="13 16 17 20 21 16"/>
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

                {{-- Custom Popover Panel (WAI-ARIA 1.2 Compliant) --}}
                <div class="cl-sort-dropdown-panel" id="clSortDropdownPanel" role="listbox" aria-labelledby="clSortBtn" tabindex="-1">
                    {{-- Section 1: Chronological --}}
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

                    {{-- Section 2: Alphabetical --}}
                    <div class="cl-sort-section-hdr">Alphabetical</div>
                    <div class="cl-sort-item" id="sort-opt-name_asc" data-sort-val="name_asc" onclick="selectSortOption('name_asc', 'Client Name (A → Z)', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-sort-item-left">
                            <svg class="cl-sort-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <span class="cl-sort-item-title">Client Name (A → Z)</span>
                        </div>
                        <div class="cl-sort-item-right">
                            <span class="cl-sort-direction-badge">A → Z</span>
                            <span class="cl-sort-check" style="display:none;">✓</span>
                        </div>
                    </div>
                    <div class="cl-sort-item" id="sort-opt-name_desc" data-sort-val="name_desc" onclick="selectSortOption('name_desc', 'Client Name (Z → A)', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-sort-item-left">
                            <svg class="cl-sort-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <span class="cl-sort-item-title">Client Name (Z → A)</span>
                        </div>
                        <div class="cl-sort-item-right">
                            <span class="cl-sort-direction-badge">Z → A</span>
                            <span class="cl-sort-check" style="display:none;">✓</span>
                        </div>
                    </div>

                    {{-- Section 3: Activity & Jobs --}}
                    <div class="cl-sort-section-hdr">Activity</div>
                    <div class="cl-sort-item" id="sort-opt-jobs_desc" data-sort-val="jobs_desc" onclick="selectSortOption('jobs_desc', 'Most Active Jobs', event)" role="option" aria-selected="false" tabindex="0">
                        <div class="cl-sort-item-left">
                            <svg class="cl-sort-item-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <span class="cl-sort-item-title">Most Active Jobs</span>
                        </div>
                        <div class="cl-sort-item-right">
                            <span class="cl-sort-direction-badge">High → Low</span>
                            <span class="cl-sort-check" style="display:none;">✓</span>
                        </div>
                    </div>
                </div>

                {{-- Hidden input for state & form compatibility --}}
                <input type="hidden" id="sortingfilter" name="sortingfilter" value="recent" />
            </div>
        </div>

        @if(in_array("view_clients", $userPermissions))
        <!-- ── TABLE ── -->
        <div class="tbl">
            <div class="tbl-w">
                <div class="tbl-scroll-container">
                    <div class="tbl-h" id="clientTableHead">
                        <div class="tbl-th-subcard tbl-th-check">
                            <input class="tbl-cb" type="checkbox" id="clientCheckAll" onclick="toggleSelectAllClients(this)" />
                        </div>
                        <div class="tbl-th-subcard cl-sortable-th" data-sort-field="name" title="Click to sort by Company Name (A-Z / Z-A)" onclick="handleHeaderSort('name')">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                <span>Company Name</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="tbl-th-subcard" title="Location">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>Location</span>
                            </div>
                        </div>
                        <div class="tbl-th-subcard cl-sortable-th" data-sort-field="date" title="Click to sort by Date Added (Newest / Oldest)" onclick="handleHeaderSort('date')">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span>Date</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="tbl-th-subcard" title="Last Activity">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>Last Activity</span>
                            </div>
                        </div>
                        <div class="tbl-th-subcard cl-sortable-th" data-sort-field="jobs" title="Click to sort by Most Active Jobs" onclick="handleHeaderSort('jobs')">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                <span>Jobs</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="tbl-th-subcard" title="Hiring Pipeline">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                                <span>Hiring Pipeline</span>
                            </div>
                            <svg class="tbl-th-info" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-toggle="tooltip" title="Pipeline: Sourcing → Screening → Evaluation → Negotiations → Offer → Joined | Interview (Total Applications on hover)"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        </div>
                    </div>

                    <div class="loadingMessage" style="display:none; position:absolute; left:50%; top:50%; transform:translate(-50%, -50%); z-index:10; background:rgba(255,255,255,0.9); padding:10px 20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">Processing...</div>
                    <div id="cl_search_sync_banner" class="cl-sync-banner" style="display:none; margin: 8px 12px 4px 12px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Showing cached clients. Live search projection is syncing in the background...</span>
                    </div>
                    <div id="allClients">{!! $initialHtml ?? '' !!}</div>
                </div>

                <!-- ── PAGINATION BAR ── -->
                <div id="pagination">{!! $initialPagination ?? '' !!}</div>
            </div>
        </div>
        @endif

    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->

</div>
<!--end::Content-->

<!-- ── DRAWER FOR INTERNAL CLIENT ── -->
<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body cd-activities-drawer" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close" data-kt-drawer-width="{default:'90%', 'sm': '85%', 'md': '80%', 'lg': '80%', 'xl': '80%'}">
  <div class="card shadow-none rounded-0 w-100">
    <button type="button" id="kt_activitiesClient_close" class="d-none" aria-hidden="true"></button>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
  </div>
</div>

<!-- ── DRAWER FOR DIRECT CLIENT ── -->
<div id="kt_activitiesDirectClient_toggle"></div>
<div id="kt_activitiesDirectClient" class="bg-body cd-activities-drawer" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close" data-kt-drawer-width="{default:'90%', 'sm': '85%', 'md': '80%', 'lg': '80%', 'xl': '80%'}">
  <div class="card shadow-none rounded-0 w-100">
    <button type="button" id="kt_activitiesDirectClient_close" class="d-none" aria-hidden="true"></button>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesDirectClient_body"> &nbsp; </div>
  </div>
</div>

<!-- ── DEDICATED 100% CLIENT-SIDE EXECUTIVE PRINT-TO-PDF CANVAS ── -->
<div id="cl-print-report-canvas" class="d-none" aria-hidden="true">
    <div class="print-report-header">
        <div>
            <h1 class="print-report-title">Clients Portfolio & Recruitment Pipeline Report</h1>
            <div class="print-report-sub">{{ auth()->user()->company->company_name ?? 'JERO Enterprise ATS' }} • Confidential Account Performance Review</div>
        </div>
        <div class="print-report-meta">
            <div id="printReportTimestamp">Date: </div>
            <div id="printReportScope">Scope: Active Filtered View</div>
            <div id="printReportManifestId">Ref: </div>
        </div>
    </div>

    <div class="print-kpi-grid">
        <div class="print-kpi-card">
            <div class="print-kpi-num" id="printKpiTotal">0</div>
            <div class="print-kpi-lbl">Total Clients</div>
        </div>
        <div class="print-kpi-card">
            <div class="print-kpi-num" id="printKpiActive">0</div>
            <div class="print-kpi-lbl">Active Companies</div>
        </div>
        <div class="print-kpi-card">
            <div class="print-kpi-num" id="printKpiHiring">0</div>
            <div class="print-kpi-lbl">Open Requisitions</div>
        </div>
        <div class="print-kpi-card">
            <div class="print-kpi-num" id="printKpiPipeline">0</div>
            <div class="print-kpi-lbl">Active Pipeline</div>
        </div>
        <div class="print-kpi-card">
            <div class="print-kpi-num" id="printKpiPlacements">0</div>
            <div class="print-kpi-lbl">Placements (Joined)</div>
        </div>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th style="width:25%;">Client Company</th>
                <th style="width:18%;">Location</th>
                <th style="width:12%;">Type</th>
                <th style="width:15%;">Jobs (H/H/C)</th>
                <th style="width:20%;">Pipeline (Src / Eval / Int / Join)</th>
                <th style="width:10%;">Last Activity</th>
            </tr>
        </thead>
        <tbody id="printTableBody">
            <!-- Dynamically rendered -->
        </tbody>
    </table>

    <div class="print-footer">
        <span>CONFIDENTIAL — Generated via JERO GOD-Level ATS Engine • 100% Client-Side Render</span>
        <span id="printFooterNotice">Authorized Recruiter View</span>
    </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<!-- JERO Distributed Client Runtime (JDCR V5.2) Local Persistent Engine & Incremental Renderer -->
<script src="{{ asset('assets/service_company/client/cl-entity-db.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/cl-drawer-view.js') }}{{ TM_VERSION }}"></script>
<!-- JERO Service Company Drawer - Scoped Instance Architecture V3.0 (Enterprise Master Standard) -->
<script src="{{ asset('assets/service_company/client/drawer/request-manager.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/ui-state-renderer.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tab-coordinator.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/overview.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/activity.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/jobs.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/candidates.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/contacts.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/interviews.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/offers.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/performance.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/drawer-instance.js') }}{{ TM_VERSION }}"></script>
<script>
$(document).ready(function() {
    var searchTimer = null;
    $('#kt_tagify_2').on('keyup input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            if (typeof loadClientList === 'function') {
                loadClientList(1);
            } else if (typeof fetchCandidates === 'function') {
                fetchCandidates(1);
            }
        }, 300);
    });
});

function filtersdataget() {
    if (typeof loadClientList === 'function') {
        loadClientList(1, true);
    } else if (typeof fetchCandidates === 'function') {
        fetchCandidates(1, true);
    }
}

// ── Location Combobox Controls ──
function toggleLocationDropdown(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    const $panel = $('#clLocDropdownPanel');
    const isVisible = $panel.hasClass('show');
    if (isVisible) {
        $panel.removeClass('show');
        $('#clLocBtn').removeClass('active');
    } else {
        $panel.addClass('show');
        $('#clLocBtn').addClass('active');
        $('#clLocSearchInp').focus();
        renderLocationDropdown($('#clLocSearchInp').val() || '');
    }
}

function handleLocationSearchInput(q) {
    if (q && q.trim()) {
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

function escapeAttr(str) {
    if (!str) return '';
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

function highlightText(text, query) {
    if (!query || !text) return text || '';
    const index = text.toLowerCase().indexOf(query.toLowerCase());
    if (index === -1) return text;
    const before = text.substring(0, index);
    const match = text.substring(index, index + query.length);
    const after = text.substring(index + query.length);
    return `${before}<b style="color:#d97706; text-decoration:underline;">${match}</b>${after}`;
}

function renderLocationDropdown(filterQuery = '') {
    const q = (filterQuery || '').toLowerCase().trim();
    const $body = $('#clLocDropdownBody');
    const facets = window.initialLocationFacets || { total: 0, located_count: 0, unspecified_count: 0, hubs: [] };
    const hubs = facets.hubs || [];
    const unspecifiedCount = facets.unspecified_count || 0;
    const countries = window.allCountriesList || [];

    let html = '';

    // Option 1: All Locations (Reset)
    const isAllSelected = !clientListState.country_id && !clientListState.unspecified_location;
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

    let hubsRendered = 0;
    let hubHtml = '';

    // Active Hubs Section
    hubs.forEach(function(hub) {
        const countryMatch = hub.country_name.toLowerCase().includes(q);
        const matchingCities = (hub.cities || []).filter(c => c.city_name.toLowerCase().includes(q));

        if (!q || countryMatch || matchingCities.length > 0) {
            hubsRendered++;
            const isCountrySelected = (clientListState.country_id == hub.country_id && !clientListState.city_id && !clientListState.unspecified_location);
            const hasCities = (hub.cities && hub.cities.length > 0);
            const isExpanded = (q.length > 0) || (clientListState.country_id == hub.country_id);

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

            // Cities list
            if (hasCities) {
                hubHtml += `<div class="cl-loc-city-list ${isExpanded ? 'show' : ''}" id="clLocCities_${hub.country_id}">`;
                hub.cities.forEach(function(city) {
                    if (!q || countryMatch || city.city_name.toLowerCase().includes(q)) {
                        const isCitySelected = (clientListState.country_id == hub.country_id && (clientListState.city_id == city.city_id || clientListState.city_name == city.city_name));
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
        html += `<div class="cl-loc-section-hdr">Your Client Hubs</div>${hubHtml}`;
    }

    // Unspecified Location option
    if (unspecifiedCount > 0 && (!q || 'location not specified'.includes(q) || 'unspecified'.includes(q) || 'unknown'.includes(q))) {
        const isUnspecifiedSelected = clientListState.unspecified_location === true;
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
                const isSelected = (clientListState.country_id == c.id && !clientListState.city_id && !clientListState.unspecified_location);
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
    e.stopPropagation();
    e.preventDefault();
    const $cities = $(`#clLocCities_${countryId}`);
    const $btn = $(e.currentTarget);
    $cities.toggleClass('show');
    $btn.toggleClass('expanded');
}

function resetLocationFilter(triggerFetch = true) {
    clientListState.country_id = null;
    clientListState.country_name = '';
    clientListState.city_id = null;
    clientListState.city_name = '';
    clientListState.unspecified_location = false;

    $('#country').val('');
    $('#country_name').val('');
    $('#city_id').val('');
    $('#city_name').val('');
    $('#unspecified_location').val('0');

    $('#locationDisplay').text('All Locations');
    $('#clLocBtn').removeClass('has-filter active');
    $('#clLocDropdownPanel').removeClass('show');

    if (triggerFetch) {
        filtersdataget();
    }
}

function selectLocationCountry(countryId, countryName, triggerFetch = true) {
    clientListState.country_id = countryId;
    clientListState.country_name = countryName;
    clientListState.city_id = null;
    clientListState.city_name = '';
    clientListState.unspecified_location = false;

    $('#country').val(countryId);
    $('#country_name').val(countryName);
    $('#city_id').val('');
    $('#city_name').val('');
    $('#unspecified_location').val('0');

    $('#locationDisplay').text(countryName);
    $('#clLocBtn').addClass('has-filter').removeClass('active');
    $('#clLocDropdownPanel').removeClass('show');

    if (triggerFetch) {
        filtersdataget();
    }
}

function selectLocationCity(countryId, countryName, cityId, cityName, triggerFetch = true) {
    clientListState.country_id = countryId;
    clientListState.country_name = countryName;
    clientListState.city_id = (cityId === 'unspecified') ? null : cityId;
    clientListState.city_name = cityName;
    clientListState.unspecified_location = false;

    $('#country').val(countryId);
    $('#country_name').val(countryName);
    $('#city_id').val(clientListState.city_id || '');
    $('#city_name').val(cityName);
    $('#unspecified_location').val('0');

    const displayLabel = `${countryName}: ${cityName}`;
    $('#locationDisplay').text(displayLabel);
    $('#clLocBtn').addClass('has-filter').removeClass('active');
    $('#clLocDropdownPanel').removeClass('show');

    if (triggerFetch) {
        filtersdataget();
    }
}

function selectLocationUnspecified(triggerFetch = true) {
    clientListState.country_id = null;
    clientListState.country_name = '';
    clientListState.city_id = null;
    clientListState.city_name = '';
    clientListState.unspecified_location = true;

    $('#country').val('unspecified');
    $('#country_name').val('');
    $('#city_id').val('');
    $('#city_name').val('');
    $('#unspecified_location').val('1');

    $('#locationDisplay').text('Location Not Specified');
    $('#clLocBtn').addClass('has-filter').removeClass('active');
    $('#clLocDropdownPanel').removeClass('show');

    if (triggerFetch) {
        filtersdataget();
    }
}

function selectLocationByCountryAndCity(countryVal, cityVal, triggerFetch = true) {
    if (!countryVal || countryVal === 'all') {
        resetLocationFilter(triggerFetch);
        return;
    }
    if (countryVal === 'unspecified') {
        selectLocationUnspecified(triggerFetch);
        return;
    }

    const countries = window.allCountriesList || [];
    let matchedCountry = countries.find(c => c.id == countryVal || c.country_name.toLowerCase() === countryVal.toLowerCase());
    let countryName = matchedCountry ? matchedCountry.country_name : countryVal;
    let countryId = matchedCountry ? matchedCountry.id : countryVal;

    if (cityVal) {
        selectLocationCity(countryId, countryName, cityVal, cityVal, triggerFetch);
    } else {
        selectLocationCountry(countryId, countryName, triggerFetch);
    }
}

// ── God-Level Enterprise Industry Combobox (Client-First / 10K Concurrency) ──
let activeIndSectorFilter = 'all';
let focusedIndItemIndex = -1;

const SECTOR_ICON_MAP = {
    1: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
    2: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
    3: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
    4: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    5: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
    6: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
    7: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
    8: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
    9: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
    10: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>'
};

function toggleIndustryDropdown(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    // Close other dropdowns
    $('#clLocDropdownPanel').removeClass('show');
    $('#clLocBtn').removeClass('active');

    const $panel = $('#clIndDropdownPanel');
    const isVisible = $panel.hasClass('show');
    if (isVisible) {
        $panel.removeClass('show');
        $('#clIndBtn').removeClass('active').attr('aria-expanded', 'false');
    } else {
        $panel.addClass('show');
        $('#clIndBtn').addClass('active').attr('aria-expanded', 'true');
        $('#clIndSearchInp').focus();
        renderIndustryDropdown($('#clIndSearchInp').val() || '');
    }
}

function handleIndustrySearchInput(q) {
    if (q && q.trim()) {
        $('#clIndSearchClear').show();
    } else {
        $('#clIndSearchClear').hide();
    }
    focusedIndItemIndex = -1;
    renderIndustryDropdown(q);
}

function clearIndustrySearch() {
    $('#clIndSearchInp').val('');
    $('#clIndSearchClear').hide();
    focusedIndItemIndex = -1;
    renderIndustryDropdown('');
    $('#clIndSearchInp').focus();
}

function filterIndustryBySector(sectorId, e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    activeIndSectorFilter = sectorId;
    renderIndustryDropdown($('#clIndSearchInp').val() || '');
}

function renderIndustryDropdown(filterQuery = '') {
    const q = (filterQuery || '').toLowerCase().trim();
    const $body = $('#clIndDropdownBody');
    const manifest = window.initialIndustriesManifest || { industries: [], sectors: [] };
    const allIndustries = manifest.industries || [];
    const selectedId = String(clientListState.industry_id || '');

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

    // Render matches
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
    clientListState.industry_id = val;

    $('#industryFilter').val(val);
    $('#industry_id').val(val);

    if (val) {
        let displayName = name;
        if (!displayName) {
            const manifest = window.initialIndustriesManifest || { industries: [] };
            const found = (manifest.industries || []).find(i => String(i.id) === val);
            displayName = found ? found.name : `Industry #${val}`;
        }
        $('#industryDisplay').text(displayName).attr('title', displayName);
        $('#clIndBtn').addClass('has-filter').removeClass('active').attr('aria-expanded', 'false');
        $('#industryClearBtn').show();
    } else {
        $('#industryDisplay').text('All Industries').removeAttr('title');
        $('#clIndBtn').removeClass('has-filter active').attr('aria-expanded', 'false');
        $('#industryClearBtn').hide();
    }

    $('#clIndDropdownPanel').removeClass('show');

    if (triggerFetch) {
        filtersdataget();
    }
}

function clearIndustryFilter(triggerFetch = true) {
    selectIndustryFilter('', 'All Industries', triggerFetch);
}

function clearIndustryFilterFromPill(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    clearIndustryFilter(true);
}

function handleIndustryKeydown(e) {
    const $items = $('#clIndDropdownBody .cl-ind-item');
    if (!$items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        focusedIndItemIndex = (focusedIndItemIndex + 1) % $items.length;
        $items.removeClass('focused');
        const $focused = $items.eq(focusedIndItemIndex).addClass('focused');
        if ($focused.length && $focused[0].scrollIntoView) {
            $focused[0].scrollIntoView({ block: 'nearest' });
        }
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        focusedIndItemIndex = (focusedIndItemIndex - 1 + $items.length) % $items.length;
        $items.removeClass('focused');
        const $focused = $items.eq(focusedIndItemIndex).addClass('focused');
        if ($focused.length && $focused[0].scrollIntoView) {
            $focused[0].scrollIntoView({ block: 'nearest' });
        }
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (focusedIndItemIndex >= 0 && focusedIndItemIndex < $items.length) {
            $items.eq(focusedIndItemIndex).trigger('click');
        }
    } else if (e.key === 'Escape') {
        $('#clIndDropdownPanel').removeClass('show');
        $('#clIndBtn').removeClass('active').attr('aria-expanded', 'false');
    }
}

// ── Dropdown filter sync handlers ──
function handleIndustryChange(sel) {
    const val = sel.value;
    const text = sel.options && sel.selectedIndex >= 0 ? sel.options[sel.selectedIndex].text : '';
    selectIndustryFilter(val, text, true);
}

// ── Activity Recency Popover State Machine ──
const clActLabels = {
    'all': 'All Activity',
    '7d': 'Active This Week',
    '30d': 'Active This Month',
    '90d': 'Needs Outreach',
    'dormant': 'Dormant (> 90d)',
    'never': 'Never Engaged'
};

function toggleActivityDropdown(e) {
    if (e) { e.stopPropagation(); e.preventDefault(); }
    const $panel = $('#clActDropdownPanel');
    const $btn = $('#clActBtn');
    const isVisible = $panel.hasClass('show');

    if (isVisible) {
        closeActivityDropdown();
    } else {
        // Close peer popovers
        closeLocationDropdown();
        closeIndustryDropdown();
        closePipelineDropdown();
        closeSortDropdown();

        $panel.addClass('show');
        $btn.addClass('active').attr('aria-expanded', 'true');
        const $selected = $panel.find('.cl-act-item.selected');
        if ($selected.length) {
            $selected.focus();
        } else {
            $panel.find('.cl-act-item').first().focus();
        }
    }
}

function closeActivityDropdown() {
    $('#clActDropdownPanel').removeClass('show');
    $('#clActBtn').removeClass('active').attr('aria-expanded', 'false');
}

function selectActivityOption(val, text, e, skipFetch = false) {
    if (e && e.stopPropagation) { e.stopPropagation(); }
    closeActivityDropdown();

    const canonicalVal = (val === 'all' || !val) ? '' : val;
    const displayText = clActLabels[canonicalVal || 'all'] || text || 'All Activity';
    $('#activityDisplay').text(displayText);
    $('#activityFilter').val(canonicalVal);
    clientListState.activity = canonicalVal;

    $('#clActDropdownPanel .cl-act-item').removeClass('selected').attr('aria-selected', 'false')
        .find('.cl-act-check').hide();
    const optKey = canonicalVal || 'all';
    $(`#act-opt-${optKey}`).addClass('selected').attr('aria-selected', 'true')
        .find('.cl-act-check').show();

    if (canonicalVal) {
        $('#clActBtn').addClass('is-active-filter');
        $('#activityClearBtn').show();
    } else {
        $('#clActBtn').removeClass('is-active-filter');
        $('#activityClearBtn').hide();
    }

    if (!skipFetch) {
        syncUrlState();
        filtersdataget();
    }
}

function clearActivityFilter(e) {
    if (e) { e.stopPropagation(); e.preventDefault(); }
    selectActivityOption('all', 'All Activity', null, false);
}

function updateRecencyCounts(counts) {
    if (!counts) return;
    $('#actCountAll').text(counts.all ?? 0);
    $('#actCount7d').text(counts['7d'] ?? 0);
    $('#actCount30d').text(counts['30d'] ?? 0);
    $('#actCount90d').text(counts['90d'] ?? 0);
    $('#actCountDormant').text(counts.dormant ?? 0);
    $('#actCountNever').text(counts.never ?? 0);
}

// ── Pipeline State Popover State Machine ──
const clPipeLabels = {
    'all': 'All Pipeline',
    'active': 'Pipeline Moving',
    'none': 'No Active Pipeline'
};

function togglePipelineDropdown(e) {
    if (e) { e.stopPropagation(); e.preventDefault(); }
    const $panel = $('#clPipeDropdownPanel');
    const $btn = $('#clPipeBtn');
    const isVisible = $panel.hasClass('show');

    if (isVisible) {
        closePipelineDropdown();
    } else {
        // Close peer popovers
        closeLocationDropdown();
        closeIndustryDropdown();
        closeActivityDropdown();
        closeSortDropdown();

        $panel.addClass('show');
        $btn.addClass('active').attr('aria-expanded', 'true');
        const $selected = $panel.find('.cl-pipe-item.selected');
        if ($selected.length) {
            $selected.focus();
        } else {
            $panel.find('.cl-pipe-item').first().focus();
        }
    }
}

function closePipelineDropdown() {
    $('#clPipeDropdownPanel').removeClass('show');
    $('#clPipeBtn').removeClass('active').attr('aria-expanded', 'false');
}

function selectPipelineOption(val, text, e, skipFetch = false) {
    if (e && e.stopPropagation) { e.stopPropagation(); }
    closePipelineDropdown();

    const canonicalVal = (val === 'all' || !val) ? '' : val;
    const displayText = clPipeLabels[canonicalVal || 'all'] || text || 'All Pipeline';
    $('#pipelineDisplay').text(displayText);
    $('#pipelineFilter').val(canonicalVal);
    clientListState.pipeline = canonicalVal;

    $('#clPipeDropdownPanel .cl-pipe-item').removeClass('selected').attr('aria-selected', 'false')
        .find('.cl-pipe-check').hide();
    const optKey = canonicalVal || 'all';
    $(`#pipe-opt-${optKey}`).addClass('selected').attr('aria-selected', 'true')
        .find('.cl-pipe-check').show();

    if (canonicalVal) {
        $('#clPipeBtn').addClass('is-active-filter');
        $('#pipelineClearBtn').show();
    } else {
        $('#clPipeBtn').removeClass('is-active-filter');
        $('#pipelineClearBtn').hide();
    }

    if (!skipFetch) {
        syncUrlState();
        filtersdataget();
    }
}

function clearPipelineFilter(e) {
    if (e) { e.stopPropagation(); e.preventDefault(); }
    selectPipelineOption('all', 'All Pipeline', null, false);
}

function updatePipelineCounts(counts) {
    if (!counts) return;
    $('#pipeCountAll').text(counts.all ?? 0);
    $('#pipeCountActive').text(counts.active ?? 0);
    $('#pipeCountNone').text(counts.none ?? 0);
}

// ── Client Engagement Recency Badge Renderer (User RAM Execution) ──
function renderClientEngagementBadge(r, daysAgo) {
    const rawActivity = r.last_activity_at || null;
    const fallbackDays = daysAgo || '';

    if (!rawActivity && (!fallbackDays || fallbackDays === 'No activity yet')) {
        return `<span class="cl-meta-item cl-touch-never" title="No outreach recorded yet. Initial onboarding recommended.">
                    <span class="cl-dot cl-dot-purple"></span>
                    <span>Last touch: <strong class="text-purple">Never engaged</strong></span>
                </span>`;
    }

    let diffDays = 0;
    if (rawActivity) {
        const actDate = new Date(rawActivity);
        const now = new Date();
        diffDays = Math.floor((now - actDate) / (1000 * 60 * 60 * 24));
        if (isNaN(diffDays) || diffDays < 0) diffDays = 0;
    } else {
        diffDays = parseInt(String(fallbackDays).replace(/\D/g, ''), 10) || 0;
    }

    if (diffDays <= 7) {
        const text = diffDays === 0 ? 'Today' : `${diffDays}d ago`;
        return `<span class="cl-meta-item cl-touch-hot" title="High velocity client account (Touchpoint in last 7 days)">
                    <span class="cl-dot cl-dot-green"></span>
                    <span>Last touch <strong>${escapeAttr(text)}</strong></span>
                </span>`;
    } else if (diffDays <= 30) {
        return `<span class="cl-meta-item cl-touch-warm" title="Active client cadence (Touchpoint in last 30 days)">
                    <span class="cl-dot cl-dot-blue"></span>
                    <span>Last touch <strong>${diffDays}d ago</strong></span>
                </span>`;
    } else if (diffDays <= 90) {
        return `<span class="cl-meta-item cl-touch-stale" title="No outreach in over 30 days! Retention risk.">
                    <span class="cl-dot cl-dot-amber"></span>
                    <span>Last touch <strong class="text-amber">${diffDays}d ago</strong> <span class="badge badge-light-warning ms-1" style="font-size:9.5px; padding:1px 5px; font-weight:700;">Needs outreach</span></span>
                </span>`;
    } else {
        return `<span class="cl-meta-item cl-touch-dormant" title="Dormant account. Inactive over 90 days.">
                    <span class="cl-dot cl-dot-slate"></span>
                    <span>Last touch <strong class="text-muted">> 90d ago</strong> <span class="badge badge-light-secondary ms-1" style="font-size:9.5px; padding:1px 5px; font-weight:700;">Dormant</span></span>
                </span>`;
    }
}

// Backward-compatibility shim
function handleActivityChange(sel) {
    const val = typeof sel === 'string' ? sel : (sel.value || '');
    selectActivityOption(val, null, null, false);
}

function closeLocationDropdown() {
    $('#clLocDropdownPanel').removeClass('show');
    $('#clLocBtn').removeClass('active');
}

function closeIndustryDropdown() {
    $('#clIndDropdownPanel').removeClass('show');
    $('#clIndBtn').removeClass('active').attr('aria-expanded', 'false');
}

function toggleSortDropdown(e) {
    if (e) { e.stopPropagation(); e.preventDefault(); }
    const $panel = $('#clSortDropdownPanel');
    const $btn = $('#clSortBtn');
    const isVisible = $panel.hasClass('show');

    if (isVisible) {
        closeSortDropdown();
    } else {
        // Close peer popovers
        closeLocationDropdown();
        closeIndustryDropdown();
        closeActivityDropdown();
        closePipelineDropdown();

        $panel.addClass('show');
        $btn.addClass('active').attr('aria-expanded', 'true');
        const $selected = $panel.find('.cl-sort-item.selected');
        if ($selected.length) {
            $selected.focus();
        } else {
            $panel.find('.cl-sort-item').first().focus();
        }
    }
}

function closeSortDropdown() {
    $('#clSortDropdownPanel').removeClass('show');
    $('#clSortBtn').removeClass('active').attr('aria-expanded', 'false');
}

const clSortLabels = {
    'recent': 'Recently Added',
    'oldest': 'Oldest First',
    'name_asc': 'Client Name (A → Z)',
    'name_desc': 'Client Name (Z → A)',
    'jobs_desc': 'Most Active Jobs'
};

function normalizeSortValue(raw) {
    const s = String(raw || '').toLowerCase().trim();
    if (s === 'oldest' || s === '2') return 'oldest';
    if (s === 'name_asc' || s === 'name-asc' || s === 'a-z') return 'name_asc';
    if (s === 'name_desc' || s === 'name-desc' || s === 'z-a') return 'name_desc';
    if (s === 'jobs_desc' || s === 'jobs' || s === 'active_jobs') return 'jobs_desc';
    return 'recent';
}

function sortDomClientsLocally(sortVal) {
    const $container = $('#allClients');
    const $rows = $container.children('.tbl-r').get();
    if (!$rows || $rows.length <= 1) return false;

    // Check total count: if total > perPage or > rows.length, server pagination is required
    const totalCount = parseInt($('#span_clientAll').text() || '0', 10);
    const perPage = parseInt($('#pagination .pagi-perpage').val() || '10', 10);
    if (totalCount > perPage && totalCount > $rows.length) {
        return false; // Multi-page dataset: defer to authoritative Elasticsearch query
    }

    $rows.sort(function(a, b) {
        if (sortVal === 'name_asc' || sortVal === 'name_desc') {
            const nameA = ($(a).attr('data-client-name') || $(a).find('.cl-name, .cl-details-title').first().text() || '').trim().toLowerCase();
            const nameB = ($(b).attr('data-client-name') || $(b).find('.cl-name, .cl-details-title').first().text() || '').trim().toLowerCase();
            return sortVal === 'name_asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
        } else if (sortVal === 'jobs_desc') {
            const rawJobsA = $(a).attr('data-active-jobs') ?? $(a).find('.tbl-cell-jobs .cl-job-num, .cl-job-line .cl-job-num').first().text() ?? '0';
            const rawJobsB = $(b).attr('data-active-jobs') ?? $(b).find('.tbl-cell-jobs .cl-job-num, .cl-job-line .cl-job-num').first().text() ?? '0';
            const jobsA = parseInt(rawJobsA, 10) || 0;
            const jobsB = parseInt(rawJobsB, 10) || 0;
            if (jobsB !== jobsA) return jobsB - jobsA;
            const idA = parseInt($(a).data('row-id') || '0', 10);
            const idB = parseInt($(b).data('row-id') || '0', 10);
            return idB - idA;
        } else if (sortVal === 'oldest') {
            const idA = parseInt($(a).data('row-id') || '0', 10);
            const idB = parseInt($(b).data('row-id') || '0', 10);
            return idA - idB;
        } else { // 'recent'
            const idA = parseInt($(a).data('row-id') || '0', 10);
            const idB = parseInt($(b).data('row-id') || '0', 10);
            return idB - idA;
        }
    });

    requestAnimationFrame(function() {
        $.each($rows, function(idx, el) {
            $container.append(el);
        });
    });

    return true;
}

function selectSortOption(val, text, e, skipFetch = false) {
    if (e && e.stopPropagation) { e.stopPropagation(); }
    closeSortDropdown();

    const canonicalVal = normalizeSortValue(val);
    const displayText = text || clSortLabels[canonicalVal] || 'Recently Added';
    $('#sortDisplay').text(displayText);
    $('#sortingfilter').val(canonicalVal);

    $('#clSortDropdownPanel .cl-sort-item').removeClass('selected').attr('aria-selected', 'false')
        .find('.cl-sort-check').hide();
    const $opt = $(`#clSortDropdownPanel .cl-sort-item[data-sort-val="${canonicalVal}"]`);
    $opt.addClass('selected').attr('aria-selected', 'true').find('.cl-sort-check').show();

    if (canonicalVal !== 'recent') {
        $('#clSortBtn').addClass('is-custom-sorted');
    } else {
        $('#clSortBtn').removeClass('is-custom-sorted');
    }

    clientListState.sort = canonicalVal;
    updateHeaderSortIndicators(canonicalVal);

    if (skipFetch) {
        return;
    }

    syncUrlState();

    // Option A Performance Optimization: If current working set is hydrated in RAM, sort locally with 0 HTTP calls
    const locallySorted = sortDomClientsLocally(canonicalVal);
    if (locallySorted) {
        return; // Near-instantaneous client-side sort executed in User RAM (0 HTTP requests)
    }

    // Option B Canonical Fallback: Authoritative Elasticsearch query
    filtersdataget();
}

function handleSortChange(sel) {
    const text = sel.options && sel.selectedIndex >= 0 ? sel.options[sel.selectedIndex].text : 'Recently Added';
    const val = sel.value;
    selectSortOption(val, text);
}

function handleHeaderSort(field) {
    let currentSort = normalizeSortValue($('#sortingfilter').val() || clientListState.sort || 'recent');
    let nextSort = 'recent';
    let nextText = 'Recently Added';

    if (field === 'name') {
        if (currentSort === 'name_asc') {
            nextSort = 'name_desc';
            nextText = 'Client Name (Z → A)';
        } else {
            nextSort = 'name_asc';
            nextText = 'Client Name (A → Z)';
        }
    } else if (field === 'date') {
        if (currentSort === 'recent') {
            nextSort = 'oldest';
            nextText = 'Oldest First';
        } else {
            nextSort = 'recent';
            nextText = 'Recently Added';
        }
    } else if (field === 'jobs') {
        if (currentSort === 'jobs_desc') {
            nextSort = 'recent';
            nextText = 'Recently Added';
        } else {
            nextSort = 'jobs_desc';
            nextText = 'Most Active Jobs';
        }
    }

    selectSortOption(nextSort, nextText);
}

function updateHeaderSortIndicators(activeSort) {
    $('.cl-sortable-th').removeClass('active-sort active-sort-asc active-sort-desc');
    if (activeSort === 'name_asc') {
        $('.cl-sortable-th[data-sort-field="name"]').addClass('active-sort active-sort-asc');
    } else if (activeSort === 'name_desc') {
        $('.cl-sortable-th[data-sort-field="name"]').addClass('active-sort active-sort-desc');
    } else if (activeSort === 'recent') {
        $('.cl-sortable-th[data-sort-field="date"]').addClass('active-sort active-sort-desc');
    } else if (activeSort === 'oldest') {
        $('.cl-sortable-th[data-sort-field="date"]').addClass('active-sort active-sort-asc');
    } else if (activeSort === 'jobs_desc') {
        $('.cl-sortable-th[data-sort-field="jobs"]').addClass('active-sort active-sort-desc');
    }
}

// Close location, industry, activity, pipeline & sort dropdowns on outside click or escape
$(document).on('click', function(e) {
    if (!$(e.target).closest('#clLocDropdownWrapper').length) {
        closeLocationDropdown();
    }
    if (!$(e.target).closest('#clIndDropdownWrapper').length) {
        closeIndustryDropdown();
    }
    if (!$(e.target).closest('#clActDropdownWrapper').length) {
        closeActivityDropdown();
    }
    if (!$(e.target).closest('#clPipeDropdownWrapper').length) {
        closePipelineDropdown();
    }
    if (!$(e.target).closest('#clSortDropdownWrapper').length) {
        if ($('#clSortDropdownPanel').hasClass('show')) {
            closeSortDropdown();
        }
    }
});

$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLocationDropdown();
        closeIndustryDropdown();
        if ($('#clActDropdownPanel').hasClass('show')) {
            closeActivityDropdown();
            $('#clActBtn').focus();
        }
        if ($('#clPipeDropdownPanel').hasClass('show')) {
            closePipelineDropdown();
            $('#clPipeBtn').focus();
        }
        if ($('#clSortDropdownPanel').hasClass('show')) {
            closeSortDropdown();
            $('#clSortBtn').focus();
        }
        if ($('#kt_activitiesClient').hasClass('drawer-on') || $('#kt_activitiesDirectClient').hasClass('drawer-on') || document.querySelector('.drawer-overlay')) {
            if (typeof ClientStateManager !== 'undefined' && typeof ClientStateManager.clearDrawerState === 'function') {
                ClientStateManager.clearDrawerState();
            }
        }
    }
});

// Arrow key navigation inside activity popover
$(document).on('keydown', '#clActDropdownPanel .cl-act-item', function(e) {
    const $items = $('#clActDropdownPanel .cl-act-item');
    const index = $items.index(this);
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        const next = (index + 1) % $items.length;
        $items.eq(next).focus();
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prev = (index - 1 + $items.length) % $items.length;
        $items.eq(prev).focus();
    } else if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        $(this).trigger('click');
    }
});

// Arrow key navigation inside pipeline popover
$(document).on('keydown', '#clPipeDropdownPanel .cl-pipe-item', function(e) {
    const $items = $('#clPipeDropdownPanel .cl-pipe-item');
    const index = $items.index(this);
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        const next = (index + 1) % $items.length;
        $items.eq(next).focus();
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prev = (index - 1 + $items.length) % $items.length;
        $items.eq(prev).focus();
    } else if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        $(this).trigger('click');
    }
});

// Arrow key navigation inside sort popover
$(document).on('keydown', '#clSortDropdownPanel .cl-sort-item', function(e) {
    const $items = $('#clSortDropdownPanel .cl-sort-item');
    const index = $items.index(this);
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        const next = (index + 1) % $items.length;
        $items.eq(next).focus();
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prev = (index - 1 + $items.length) % $items.length;
        $items.eq(prev).focus();
    } else if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        $(this).trigger('click');
    }
});

// ── Select all checkbox ──
function toggleSelectAllClients(master) {
    var checked = $(master).is(':checked');
    $('#allClients .tbl-cb').prop('checked', checked);
}

// ── Client-side CSV Exporter ──
// ── Enterprise 100% Client-Side Export Engine (CSV with CWE-1236 Defense & Dedicated Print-to-PDF) ──
window.ClientExportManager = {
    state: 'IDLE', // IDLE, PREPARING, FILTERING, SERIALIZING, DOWNLOADING, COMPLETE

    setButtonState: function(state, text) {
        this.state = state;
        const $btn = $('#clExportDropdownBtn');
        const $lbl = $('#clExportBtnLabel');
        if (state === 'IDLE' || state === 'COMPLETE') {
            $btn.prop('disabled', false).removeClass('opacity-75');
            $lbl.text('Export');
        } else {
            $btn.prop('disabled', true).addClass('opacity-75');
            $lbl.text(text || 'Exporting...');
        }
    },

    getActiveFilters: function() {
        return {
            tab: (typeof activeClientTab !== 'undefined' ? activeClientTab : 'all'),
            search: ($('#kt_tagify_2').val() || '').trim(),
            sort: ($('#sortingfilter').val() || 'recent'),
            country_id: (typeof clientListState !== 'undefined' && clientListState.country_id) ? clientListState.country_id : ($('#country').val() || ''),
            country_name: (typeof clientListState !== 'undefined' && clientListState.country_name) ? clientListState.country_name : '',
            city_id: (typeof clientListState !== 'undefined' && clientListState.city_id) ? clientListState.city_id : ($('#city_id').val() || ''),
            city_name: (typeof clientListState !== 'undefined' && clientListState.city_name) ? clientListState.city_name : '',
            unspecified_location: (typeof clientListState !== 'undefined' && clientListState.unspecified_location) ? 1 : 0,
            industry_id: (typeof clientListState !== 'undefined' && clientListState.industry_id) ? clientListState.industry_id : ($('#industryFilter').val() || ''),
            industry: (typeof clientListState !== 'undefined' && clientListState.industry_id) ? clientListState.industry_id : ($('#industryFilter').val() || ''),
            activity: (typeof clientListState !== 'undefined' && clientListState.activity) ? clientListState.activity : ($('#activityFilter').val() || '')
        };
    },

    ensureHydration: async function() {
        if (window.clWorker && window.clWorkerReady) {
            return true;
        }
        const companyId = {{ (int)auth()->user()->company_id }};
        const userId = {{ (int)auth()->id() }};
        const idbStoreKey = 'jero_client_projection:tenant=' + companyId + ':user=' + userId + ':projection=clients:schema=2';
        try {
            if (typeof ClientLocationDB !== 'undefined') {
                const stored = await ClientLocationDB.get(idbStoreKey);
                if (stored && stored.rows && stored.rows.length > 0) {
                    if (!window.clWorker && typeof initClientWorker === 'function') {
                        initClientWorker();
                    }
                    if (window.clWorker) {
                        window.clWorker.postMessage({
                            type: 'INIT_DATASET',
                            rows: stored.rows,
                            version: stored.data_version || 1
                        });
                        window.clWorkerReady = true;
                        return true;
                    }
                }
            }
        } catch (e) {
            console.warn('[Export Engine] IndexedDB hydration check skipped:', e);
        }
        return false;
    },

    exportCsv: async function(scope) {
        this.setButtonState('PREPARING', 'Preparing...');
        const isHydrated = await this.ensureHydration();

        const filters = this.getActiveFilters();
        const exportId = 'EXP-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-' + Math.floor(1000 + Math.random() * 9000);
        const manifest = {
            exportId: exportId,
            userName: '{{ addslashes(auth()->user()->name ?? "Recruiter") }}',
            userId: {{ (int)auth()->id() }},
            tenantId: {{ (int)auth()->user()->company_id }}
        };

        if (isHydrated && window.clWorker && window.clWorkerReady) {
            this.setButtonState('SERIALIZING', 'Building CSV...');
            window.clWorker.postMessage({
                type: 'EXPORT_CSV',
                scope: scope,
                filters: filters,
                manifest: manifest
            });
            return;
        }

        // High-performance DOM Scraper fallback with CWE-1236 defense
        this.fallbackDomExport(scope, manifest);
    },

    fallbackDomExport: function(scope, manifest) {
        var rows = [];
        var sanitize = function(val) {
            if (val === null || val === undefined) return '""';
            var s = String(val).trim();
            if (/^[=+\-@\t\r]/.test(s)) s = "'" + s;
            return '"' + s.replace(/"/g, '""') + '"';
        };

        var manifestLines = [
            '# ==============================================================================',
            '# JERO ENTERPRISE ATS CLIENTS EXPORT MANIFEST (DOM FALLBACK)',
            '# Export ID: ' + manifest.exportId,
            '# Exported At: ' + new Date().toISOString(),
            '# Scope: ' + (scope === 'all' ? 'All Visible Clients' : 'Filtered Visible View'),
            '# Tenant ID: ' + manifest.tenantId,
            '# Generated By: ' + manifest.userName + ' (ID #' + manifest.userId + ')',
            '# =============================================================================='
        ];

        var headers = ['Company Name', 'Industry', 'Location', 'Jobs Hiring', 'Jobs Hold', 'Jobs Closed', 'Sourced', 'Evaluated', 'Interviews', 'Offers', 'Joined', 'Client Type', 'Connected Date', 'Last Activity'];

        rows.push(manifestLines.join('\r\n'));
        rows.push(headers.map(function(h) { return '"' + h + '"'; }).join(','));

        $('#allClients .tbl-r').each(function() {
            var $r = $(this);
            var name = $r.find('.cl-name, .cl-details-title').first().text().trim();
            var ind = $r.find('.cl-ind, .cl-details-ind').first().text().trim();
            var loc = $r.find('.tbl-cell-location, .cl-card-loc-text').first().text().trim().replace(/\s+/g, ' ');
            var hiringJobs = $r.find('.tbl-cell-jobs .cl-job-line:eq(0) .cl-job-num').text().trim() || '0';
            var holdJobs = $r.find('.tbl-cell-jobs .cl-job-line:eq(1) .cl-job-num').text().trim() || '0';
            var closedJobs = $r.find('.tbl-cell-jobs .cl-job-line:eq(2) .cl-job-num').text().trim() || '0';
            var sourced = $r.find('.pill-sourced').text().trim() || '0';
            var evaluated = $r.find('.pill-evaluated').text().trim() || '0';
            var interviews = $r.find('.pill-interviews').text().trim() || '0';
            var offers = $r.find('.pill-offers').text().trim() || '0';
            var joined = $r.find('.pill-joined').text().trim() || '0';
            var type = $r.find('.cl-type-name').text().trim() || 'Internal Client';
            var conn = $r.find('.cl-date-box span').text().trim() || '-';
            var act = $r.find('.cl-act-main').text().trim() || '-';

            if (name) {
                rows.push([
                    sanitize(name),
                    sanitize(ind),
                    sanitize(loc),
                    hiringJobs,
                    holdJobs,
                    closedJobs,
                    sourced,
                    evaluated,
                    interviews,
                    offers,
                    joined,
                    sanitize(type),
                    sanitize(conn),
                    sanitize(act)
                ].join(','));
            }
        });

        if (rows.length <= 2) {
            this.setButtonState('IDLE');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'No Data',
                    text: 'There are no clients visible on the current page to export.'
                });
            }
            return;
        }

        var csvContent = '\uFEFF' + rows.join('\r\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        var link = document.createElement('a');
        var dateStr = new Date().toISOString().slice(0, 10);
        link.setAttribute('href', url);
        link.setAttribute('download', 'jero_clients_export_' + scope + '_' + dateStr + '.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

        this.setButtonState('COMPLETE');
        setTimeout(function() { ClientExportManager.setButtonState('IDLE'); }, 2000);
    },

    exportPdf: function() {
        this.setButtonState('PREPARING', 'Preparing Report...');
        this.buildPrintCanvas();
        this.setButtonState('IDLE');
        window.print();
    },

    buildPrintCanvas: function() {
        const now = new Date();
        const dateStr = now.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        const manifestId = 'REF-' + now.toISOString().slice(0, 10).replace(/-/g, '') + '-' + Math.floor(1000 + Math.random() * 9000);
        const curTab = (typeof activeClientTab !== 'undefined' ? activeClientTab : 'all').toUpperCase();

        $('#printReportTimestamp').text('Generated: ' + dateStr);
        $('#printReportScope').text('Scope: ' + curTab + ' CLIENTS');
        $('#printReportManifestId').text('Ref: ' + manifestId);

        // Sync KPI numbers from authoritative badges
        $('#printKpiTotal').text($('#span_clientAll').text() || '0');
        $('#printKpiActive').text($('#span_clientHiring').text() || '0');
        $('#printKpiHiring').text($('#span_clientHiring').text() || '0');
        $('#printKpiPipeline').text($('#span_clientCustom').text() || '0');
        $('#printKpiPlacements').text($('#span_clientDirect').text() || '0');

        // Populate Table rows from current DOM stream
        const $tbody = $('#printTableBody');
        $tbody.empty();

        const $cards = $('#allClients .tbl-r');
        if ($cards.length === 0) {
            $tbody.append('<tr><td colspan="6" class="text-center py-4 text-muted">No client records in active view to display.</td></tr>');
            return;
        }

        $cards.each(function() {
            const $r = $(this);
            const name = $r.find('.cl-name, .cl-details-title').first().text().trim() || 'Client Company';
            const ind = $r.find('.cl-ind, .cl-details-ind').first().text().trim() || '—';
            const loc = $r.find('.tbl-cell-location, .cl-card-loc-text').first().text().trim().replace(/\s+/g, ' ') || '—';
            const type = $r.find('.cl-type-name').text().trim() || 'Internal Client';
            const hiringJobs = $r.find('.tbl-cell-jobs .cl-job-line:eq(0) .cl-job-num').text().trim() || '0';
            const holdJobs = $r.find('.tbl-cell-jobs .cl-job-line:eq(1) .cl-job-num').text().trim() || '0';
            const closedJobs = $r.find('.tbl-cell-jobs .cl-job-line:eq(2) .cl-job-num').text().trim() || '0';
            const sourced = $r.find('.pill-sourced').text().trim() || '0';
            const evaluated = $r.find('.pill-evaluated').text().trim() || '0';
            const interviews = $r.find('.pill-interviews').text().trim() || '0';
            const joined = $r.find('.pill-joined').text().trim() || '0';
            const act = $r.find('.cl-act-main').text().trim() || '—';

            const rowHtml = `
                <tr>
                    <td><strong>${name}</strong><br><span style="font-size:8.5px;color:#64748b;">${ind}</span></td>
                    <td>${loc}</td>
                    <td><span style="display:inline-block;padding:2px 5px;border-radius:4px;background:#f1f5f9;font-weight:600;font-size:8.5px;">${type}</span></td>
                    <td><strong>${hiringJobs}</strong> Hiring / ${holdJobs} Hold / ${closedJobs} Clsd</td>
                    <td>${sourced} Sourced • ${evaluated} Eval • <strong>${interviews} Int</strong> • ${joined} Joined</td>
                    <td>${act}</td>
                </tr>
            `;
            $tbody.append(rowHtml);
        });
    }
};

function triggerClientExport(type) {
    if (type === 'executive_pdf') {
        window.ClientExportManager.exportPdf();
    } else if (type === 'all_csv') {
        window.ClientExportManager.exportCsv('all');
    } else {
        window.ClientExportManager.exportCsv('filtered');
    }
}

// Backward-compatible alias for any legacy triggers
function exportClientsToCsv() {
    triggerClientExport('filtered_csv');
}

// ── Session & Inactivity Recovery Engine (10-minute Idle & Sleep Recovery) ──
window.ClientSessionRecoveryManager = {
    heartbeatRoute: "{{ route('service_company.clients.sessionHeartbeat') }}",
    lastActivity: Date.now(),
    lastHeartbeat: Date.now(),
    heartbeatIntervalMs: 300000, // 5 minutes
    idleThresholdMs: 60000,      // 60 seconds
    isReconnecting: false,

    init: function() {
        var self = this;
        var updateActivity = function() {
            self.lastActivity = Date.now();
        };
        ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(function(evt) {
            window.addEventListener(evt, updateActivity, { passive: true });
        });

        // Tab Visibility & Focus Recovery (Triggered on wake from 10-minute sleep or background)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                self.onWakeUp('visibilitychange');
            }
        });
        window.addEventListener('focus', function() {
            self.onWakeUp('focus');
        });

        // Browser Online recovery
        window.addEventListener('online', function() {
            console.log('⚡ [Session Recovery] Network back online. Refreshing session...');
            self.pingHeartbeat(function() {
                if (typeof loadClientList === 'function') {
                    loadClientList(window.page_no || 1, false);
                }
            });
        });

        // Periodic Background Heartbeat (every 5 min while page active)
        setInterval(function() {
            if (document.visibilityState === 'visible' && (Date.now() - self.lastActivity < 600000)) {
                self.pingHeartbeat();
            }
        }, this.heartbeatIntervalMs);
    },

    onWakeUp: function(source) {
        var elapsed = Date.now() - this.lastActivity;
        this.lastActivity = Date.now();

        // If page was idle for > 60 seconds, ping session heartbeat to revive TCP keepalive and refresh CSRF
        if (elapsed > this.idleThresholdMs) {
            var self = this;
            this.pingHeartbeat(function(authOk) {
                if (!authOk) {
                    console.warn('[Session Recovery] Session expired during idle period.');
                    return;
                }
                // Revive Web Worker if suspended
                if (typeof clWorkerReady !== 'undefined' && !clWorkerReady && typeof initClientWorker === 'function') {
                    initClientWorker();
                } else if (typeof fetchActiveProjection === 'function') {
                    fetchActiveProjection();
                }

                // If active drawer was in error state, auto-recover
                if (window.ClientStateManager && window.ClientStateManager.state.drawerStatus === 'error') {
                    var selId = window.ClientStateManager.getSelectedClientId();
                    var selType = window.ClientStateManager.state.selectedClientType;
                    if (selId && selType) {
                        console.log('[Session Recovery] Auto-recovering failed drawer for client', selId);
                        window.ClientStateManager.openClientDrawer(selId, selType);
                    }
                } else if (window.ClientStateManager && window.ClientStateManager.state.drawerStatus === 'loaded') {
                    // Auto-heal idle/wake-up empty tab panes if drawer was left open
                    var curSelId = window.ClientStateManager.getSelectedClientId();
                    var curType = window.ClientStateManager.state.selectedClientType;
                    if (curSelId) {
                        if (curType === 'direct' && typeof window.loadDirectTabContent === 'function') {
                            var curDirectTab = $('#kt_activitiesDirectClient .tab-pane.active').attr('id');
                            if (curDirectTab && curDirectTab !== 'overview' && curDirectTab !== 'recent_activity') {
                                window.loadDirectTabContent(curDirectTab, false);
                            }
                        } else if (typeof window.loadInternalTabContent === 'function') {
                            var curInternalTab = $('#kt_activitiesClient .tab-pane.active').attr('id');
                            if (curInternalTab && curInternalTab !== 'overview' && curInternalTab !== 'recent_activity') {
                                window.loadInternalTabContent(curInternalTab, false);
                            }
                        }
                    }
                }
            });
        }
    },

    pingHeartbeat: function(callback) {
        var self = this;
        $.ajax({
            url: self.heartbeatRoute,
            type: 'GET',
            cache: false,
            timeout: 8000,
            success: function(resp) {
                self.lastHeartbeat = Date.now();
                if (resp && resp.csrf_token) {
                    $('meta[name="csrf-token"]').attr('content', resp.csrf_token);
                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': resp.csrf_token }
                    });
                }
                if (typeof callback === 'function') callback(true, resp);
            },
            error: function(xhr) {
                console.warn('[Session Recovery] Heartbeat check error:', xhr.status);
                if (typeof callback === 'function') callback(false, xhr);
            }
        });
    },

    executeWithRetry: function(requestFn, retryCount = 1, onFinalError) {
        var self = this;
        requestFn(function(success, data, xhr) {
            if (success) return;
            // Transient connection reset (0) or CSRF token mismatch (419)
            if (xhr && (xhr.status === 0 || xhr.status === 419) && retryCount > 0) {
                console.log(`[Session Recovery] Transient HTTP ${xhr.status} caught. Reviving session & retrying...`);
                self.pingHeartbeat(function(authOk) {
                    if (authOk) {
                        setTimeout(function() {
                            self.executeWithRetry(requestFn, retryCount - 1, onFinalError);
                        }, 250);
                    } else if (typeof onFinalError === 'function') {
                        onFinalError(xhr);
                    }
                });
            } else if (typeof onFinalError === 'function') {
                onFinalError(xhr);
            }
        });
    }
};

// ── Canonical Architecture & Deterministic State Machine for Client Management ──
// ─── JDCR V5.2: L1 In-Memory Working RAM Store (Bounded to 25 active graphs) ───
window.ClientMemoryStore = {
    cache: new Map(),
    maxSize: 25,
    get: function(id) {
        return this.cache.get(Number(id)) || null;
    },
    set: function(id, data) {
        if (!data) return;
        if (this.cache.size >= this.maxSize) {
            var firstKey = this.cache.keys().next().value;
            this.cache.delete(firstKey);
        }
        this.cache.set(Number(id), data);
    },
    delete: function(id) {
        this.cache.delete(Number(id));
    },
    clear: function() {
        this.cache.clear();
    }
};

// ── Single Source of Truth: Canonical Client Pipeline Store ──
window.ClientPipelineStore = {
    revisions: new Map(),
    pipelineCache: new Map(),
    bus: (typeof BroadcastChannel !== 'undefined') ? new BroadcastChannel('jero_pipeline_bus') : null,

    init: function() {
        if (this.bus) {
            this.bus.onmessage = (e) => {
                if (e.data && e.data.type === 'PIPELINE_UPDATE' && e.data.payload) {
                    this.apply(e.data.payload, false);
                }
            };
        }
    },

    get: function(clientId) {
        const cid = Number(clientId);
        return this.pipelineCache.get(cid) || null;
    },

    apply: function(payload, broadcast = true) {
        if (!payload || !payload.client_id) return;
        const cid = Number(payload.client_id);
        const rev = Number(payload.seq || payload.revision || 0);
        const curRev = this.revisions.get(cid) || 0;

        // Monotonic sequence check (Strict Idempotency): discard stale/out-of-order events
        if (rev && rev <= curRev) {
            return;
        }
        if (rev) this.revisions.set(cid, rev);

        // Update in-memory cache (Upgraded Contract: [sourced, screening, eval, neg, offer, joined, rejected, in_review, interviews])
        let pv = Array.isArray(payload.pv) && payload.pv.length >= 9 ? payload.pv : null;
        if (!pv && payload.changed && this.pipelineCache.has(cid)) {
            const cached = this.pipelineCache.get(cid);
            const p = Object.assign({}, cached.pipeline);
            const fromStatus = Number(payload.changed.from_status || 0);
            const toStatus = Number(payload.changed.to_status || payload.changed.new_status || 0);
            const statusKeyMap = {
                1: 'sourced',
                2: 'screening',
                3: 'evaluated',
                4: 'negotiations',
                5: 'offers',
                6: 'joined',
                7: 'rejected',
                8: 'in_review'
            };
            if (fromStatus && statusKeyMap[fromStatus] && p[statusKeyMap[fromStatus]] > 0) {
                p[statusKeyMap[fromStatus]] = Math.max(0, p[statusKeyMap[fromStatus]] - 1);
            }
            if (toStatus && statusKeyMap[toStatus]) {
                p[statusKeyMap[toStatus]] = (p[statusKeyMap[toStatus]] || 0) + 1;
            }
            const activePipeline = (p.sourced || 0) + (p.screening || 0) + (p.evaluated || 0) + (p.negotiations || 0) + (p.offers || 0) + (p.joined || 0);
            const applications = activePipeline + (p.rejected || 0) + (p.in_review || 0);
            p.active_pipeline = activePipeline;
            p.applications = applications;

            pv = [
                p.sourced || 0,
                p.screening || 0,
                p.evaluated || 0,
                p.negotiations || 0,
                p.offers || 0,
                p.joined || 0,
                p.rejected || 0,
                p.in_review || 0,
                p.interviews || 0
            ];
            this.pipelineCache.set(cid, { revision: rev, seq: rev, pv: pv, pipeline: p });
        } else if (pv) {
            const sourced = Number(pv[0] || 0);
            const screening = Number(pv[1] || 0);
            const evaluated = Number(pv[2] || 0);
            const negotiations = Number(pv[3] || 0);
            const offers = Number(pv[4] || 0);
            const joined = Number(pv[5] || 0);
            const rejected = Number(pv[6] || 0);
            const inReview = Number(pv[7] || 0);
            const interviews = Number(pv[8] || 0);

            const activePipeline = sourced + screening + evaluated + negotiations + offers + joined;
            const applications = activePipeline + rejected + inReview;

            this.pipelineCache.set(cid, {
                revision: rev,
                seq: rev,
                pv: [sourced, screening, evaluated, negotiations, offers, joined, rejected, inReview, interviews],
                pipeline: {
                    active_pipeline: activePipeline,
                    applications: applications,
                    sourced: sourced,
                    screening: screening,
                    evaluated: evaluated,
                    negotiations: negotiations,
                    offers: offers,
                    joined: joined,
                    rejected: rejected,
                    in_review: inReview,
                    interviews: interviews
                }
            });
        }

        // Forward to Web Worker
        if (window.clWorker && window.clWorkerReady) {
            window.clWorker.postMessage({
                type: 'PIPELINE_REVISION_UPDATE',
                clientId: cid,
                revision: rev,
                seq: rev,
                pv: pv,
                changed: payload.changed
            });
        }

        // In-place reactive DOM update for rendered listing row
        this.updateDOM(cid, { pv: pv, pipeline: this.pipelineCache.get(cid) ? this.pipelineCache.get(cid).pipeline : null });

        // Update Drawer if open for this client
        this.updateDrawer(cid, { pv: pv, pipeline: this.pipelineCache.get(cid) ? this.pipelineCache.get(cid).pipeline : null });

        // Broadcast across tabs in this browser via BroadcastChannel
        if (broadcast && this.bus) {
            this.bus.postMessage({
                type: 'PIPELINE_UPDATE',
                payload: {
                    client_id: cid,
                    seq: rev,
                    revision: rev,
                    pv: pv,
                    changed: payload.changed
                }
            });
        }
    },

    updateDOM: function(cid, payload) {
        const $card = $(`.cl-primary-card[data-row-id="${cid}"]`);
        if (!$card.length) return;

        const pv = payload.pv;
        if (!Array.isArray(pv) || pv.length < 9) return;

        const [sourced, screening, evaluated, negotiations, offers, joined, rejected, inReview, interviews] = pv;
        const activePipeline = Number(sourced) + Number(screening) + Number(evaluated) + Number(negotiations) + Number(offers) + Number(joined);
        const applications = activePipeline + Number(rejected) + Number(inReview);

        this._setPill($card.find('.step-sourcing'), sourced, 'Sourcing');
        this._setPill($card.find('.step-screening'), screening, 'Screening');
        this._setPill($card.find('.step-evaluation'), evaluated, 'Evaluation');
        this._setPill($card.find('.step-negotiations'), negotiations, 'Negotiation');
        this._setPill($card.find('.step-offer'), offers, 'Offer');
        this._setPill($card.find('.step-joined'), joined, 'Joined');
        this._setPill($card.find('.step-rejected'), rejected, 'Rejected');
        this._setPill($card.find('.step-interview'), interviews, 'Interview');

        const $appsBadge = $card.find('.cl-apps-badge');
        if ($appsBadge.length) {
            $appsBadge.text(applications);
            if (applications > 0) {
                $appsBadge.addClass('has-apps');
            } else {
                $appsBadge.removeClass('has-apps');
            }
        }

        $card.find('.cl-pipe-body').attr('title', `Total Applications: ${applications} | Rejected: ${rejected} | Interviews: ${interviews}`);
    },

    _setPill: function($step, count, name) {
        if (!$step.length) return;
        const num = Number(count || 0);
        $step.find('.cl-pipe-pill').text(num);
        $step.attr('title', `${name}: ${num}`);
        if (num > 0) {
            $step.addClass('has-count');
        } else {
            $step.removeClass('has-count');
        }
    },

    updateDrawer: function(cid, payload) {
        const activeDrawer = window.__activeClientDrawer;
        const activeCid = activeDrawer && activeDrawer.context ? Number(activeDrawer.context.canonicalClientId) : 0;
        if (activeCid !== cid) return;

        const pv = payload.pv;
        if (!Array.isArray(pv) || pv.length < 9) return;

        const [applications, sourced, screening, evaluated, negotiations, offers, joined, rejected, interviews] = pv;

        $('#ojInternalTabBadge, #ojCountAllInternal').text(Number(offers) + Number(joined)).show();
        $('#ojCountOffersInternal').text(offers);
        $('#tabBadgeInterviewTrackerShow, #perfKpiInterviews').text(interviews).show();
        $('#candTabCountScreening').text(screening);
        $('#candTabCountOffer').text(offers);
        $('#candTabCountJoined').text(joined);
        $('#candTabCountRejected').text(rejected);
        $('#candTabCountAll').text(applications);

        if (window.ClientDrawerTabs && window.ClientDrawerTabs['candidates'] && typeof window.ClientDrawerTabs['candidates'].onPipelineUpdate === 'function') {
            window.ClientDrawerTabs['candidates'].onPipelineUpdate(cid, payload);
        }
    }
};
window.ClientPipelineStore.init();

// ─── JDCR V5.2: Single Synchronization Authority (SyncSupervisor) ───
window.SyncSupervisor = {
    tenantId: {{ (int) (auth()->user()->company_id ?? 0) }},
    inFlightRequests: new Map(),
    dirtyEntities: new Set(),
    coalesceTimer: null,

    notifyHint: function(clientId) {
        if (!clientId) return;
        this.dirtyEntities.add(Number(clientId));
        if (this.coalesceTimer) return;
        var self = this;
        this.coalesceTimer = setTimeout(function() {
            self.flush();
            self.coalesceTimer = null;
        }, 150);
    },

    flush: function() {
        var self = this;
        this.dirtyEntities.forEach(function(cid) {
            self.reconcile(cid);
        });
        this.dirtyEntities.clear();
    },

    reconcile: function(clientId) {
        var self = this;
        var cid = Number(clientId);
        var currentSelected = Number(window.ClientStateManager ? window.ClientStateManager.getSelectedClientId() : 0);
        if (currentSelected === cid && window.ClientIncrementalRenderer) {
            ClientIncrementalRenderer.patchSyncStatus(cid, 'syncing', 'Syncing...');
        }

        var dedupeKey = `${this.tenantId}:client:${cid}:graph`;
        if (this.inFlightRequests.has(dedupeKey)) {
            return this.inFlightRequests.get(dedupeKey);
        }

        var endpoint = "{{ route('service_company.clients.api.v2.graph', ':id') }}".replace(':id', cid);
        var fetchPromise = $.ajax({
            type: 'GET',
            url: endpoint,
            cache: false,
            success: function(res) {
                if (res && res.data) {
                    window.ClientMemoryStore.set(cid, res.data);
                    if (window.ClientEntityDB) {
                        window.ClientEntityDB.put(self.tenantId, cid, res);
                    }
                    if (Number(window.ClientStateManager.getSelectedClientId()) === cid) {
                        if (window.ClientIncrementalRenderer) {
                            ClientIncrementalRenderer.patchSyncStatus(cid, 'uptodate', 'Up to Date');
                        }
                    }
                }
            },
            error: function(xhr) {
                if (Number(window.ClientStateManager.getSelectedClientId()) === cid) {
                    if (window.ClientIncrementalRenderer) {
                        ClientIncrementalRenderer.patchSyncStatus(cid, 'stale', 'Offline / Stale');
                    }
                }
            },
            complete: function() {
                self.inFlightRequests.delete(dedupeKey);
            }
        });

        this.inFlightRequests.set(dedupeKey, fetchPromise);
        return fetchPromise;
    }
};

// ─── JDCR V5.2: Multi-Tab BroadcastChannel Coordination ───
if (typeof window.BroadcastChannel !== 'undefined') {
    try {
        window.jdcrTabChannel = new BroadcastChannel('JERO_TAB_SYNC');
        window.jdcrTabChannel.onmessage = function(ev) {
            if (ev.data && ev.data.type === 'CLIENT_MUTATED' && ev.data.client_id) {
                window.SyncSupervisor.notifyHint(ev.data.client_id);
            }
        };
    } catch (e) {}
}

// ─── JDCR V5.2: Centralized Event Delegation (Zero Inline Handlers) ───
$(document).on('click', '[data-action="switch-tab"]', function(e) {
    e.preventDefault();
    var tabName = $(this).data('tab');
    var cid = $(this).data('client-id');
    if (window.ClientStateManager && typeof window.ClientStateManager.switchDrawerTab === 'function') {
        window.ClientStateManager.switchDrawerTab(cid, tabName);
    }
});

$(document).on('click', '[data-action="close-drawer"]', function(e) {
    e.preventDefault();
    if (window.ClientStateManager && typeof window.ClientStateManager.clearDrawerState === 'function') {
        window.ClientStateManager.clearDrawerState();
    }
});

// ─── JDCR V5.2: Formal Idle-Tab Lifecycle State Machine ───
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        var selId = window.ClientStateManager ? window.ClientStateManager.getSelectedClientId() : null;
        if (selId && window.SyncSupervisor) {
            window.SyncSupervisor.notifyHint(selId);
        }
    }
});

window.ClientStateManager = {
    state: {
        activeTab: {!! json_encode($initialTab ?? 'all') !!},
        selectedClientId: null,
        selectedClientType: null, // 'internal' | 'direct' | 'request' | null
        selectedClientTab: 'overview',
        drawerStatus: 'idle', // 'idle' | 'loading' | 'loaded' | 'error'
        drawerError: null,
        drawerRequestToken: 0,
        drawerLoadedTabs: {}, // keyed by `${clientId}_${tabName}`
        currentDrawerContext: null
    },

    activeDrawerXhr: null,

    getActiveTab: function() {
        return this.state.activeTab;
    },

    setSort: function(val, text) {
        if (typeof selectSortOption === 'function') {
            selectSortOption(val, text);
        }
    },

    getSelectedClientId: function() {
        return this.state.selectedClientId ? parseInt(this.state.selectedClientId, 10) : null;
    },

    isTabLoaded: function(clientId, tabName) {
        var cid = parseInt(clientId, 10) || clientId;
        return !!this.state.drawerLoadedTabs[`${cid}_${tabName}`];
    },

    markTabLoaded: function(clientId, tabName) {
        var cid = parseInt(clientId, 10) || clientId;
        this.state.drawerLoadedTabs[`${cid}_${tabName}`] = true;
    },

    // ── Teardown Active Drawer (Atomic Clean State Transition) ──
    teardownDrawer: function(fullReset = true) {
        // 1. Abort in-flight drawer network request
        if (this.activeDrawerXhr && this.activeDrawerXhr.readyState !== 4) {
            this.activeDrawerXhr.abort();
            this.activeDrawerXhr = null;
        }

        // 1b. Abort standalone tab network requests
        if (window._activeJobsXhr && window._activeJobsXhr.readyState !== 4) {
            try { window._activeJobsXhr.abort(); } catch(e) {}
            window._activeJobsXhr = null;
        }
        if (window._activeJobsDirectXhr && window._activeJobsDirectXhr.readyState !== 4) {
            try { window._activeJobsDirectXhr.abort(); } catch(e) {}
            window._activeJobsDirectXhr = null;
        }
        if (window._activeOjXhr && window._activeOjXhr.readyState !== 4) {
            try { window._activeOjXhr.abort(); } catch(e) {}
            window._activeOjXhr = null;
        }
        if (window._activeOjDirectXhr && window._activeOjDirectXhr.readyState !== 4) {
            try { window._activeOjDirectXhr.abort(); } catch(e) {}
            window._activeOjDirectXhr = null;
        }

        // 2. Detach namespaced drawer DOM event listeners to prevent duplicate triggers
        $(document).off('.clientDrawer .internalDrawer .internalDrawerTab .internalDrawerContacts .internalDrawerCand .internalDrawerUI .internalInterviewPagination .internalInterviewPageSize .directDrawer .directDrawerTab .directDrawerContacts .directDrawerCand .directDrawerOffers .directDrawerJobs .directDrawerUI .directInterviewPagination .directInterviewPageSize .drawerTabs .reqDrawer');

        // 3. Destroy Chart.js instances cleanly
        if (window.perfChartInstances) {
            Object.values(window.perfChartInstances).forEach(function(c) {
                try { if (c && typeof c.destroy === 'function') c.destroy(); } catch (e) {}
            });
            window.perfChartInstances = {};
        }
        if (window.dcChartInstances) {
            Object.values(window.dcChartInstances).forEach(function(c) {
                try { if (c && typeof c.destroy === 'function') c.destroy(); } catch (e) {}
            });
            window.dcChartInstances = {};
        }

        // 4. Clear performance analytics RAM cache
        window.perfClientCache = {};

        // 5. Teardown sub-card state machines and stores
        if (window.__activeClientDrawer && typeof window.__activeClientDrawer.destroy === 'function') {
            try { window.__activeClientDrawer.destroy(); } catch (e) {}
            window.__activeClientDrawer = null;
        }
        if (window.ClientCandidateStore && typeof window.ClientCandidateStore.destroy === 'function') {
            try { window.ClientCandidateStore.destroy(); } catch (e) {}
        }
        if (window.ClientCandidateStoreDirect && typeof window.ClientCandidateStoreDirect.destroy === 'function') {
            try { window.ClientCandidateStoreDirect.destroy(); } catch (e) {}
        }
        if (window.ClientInterviewStoreInternal && typeof window.ClientInterviewStoreInternal.destroy === 'function') {
            try { window.ClientInterviewStoreInternal.destroy(); } catch (e) {}
        }
        if (window.ClientInterviewStoreDirect && typeof window.ClientInterviewStoreDirect.destroy === 'function') {
            try { window.ClientInterviewStoreDirect.destroy(); } catch (e) {}
        }
        if (window.ClientActivityManager && typeof window.ClientActivityManager.resetState === 'function') {
            try { window.ClientActivityManager.resetState(); } catch (e) {}
        }

        // 6. Empty drawer container DOM elements completely
        $('#kt_activitiesClient_body').empty();
        $('#kt_activitiesDirectClient_body').empty();

        // 7. Reset tokens, context, and active drawer loaded tabs
        this.state.drawerRequestToken++;
        this.state.currentDrawerContext = null;
        this.state.drawerLoadedTabs = {};

        if (fullReset) {
            this.state.selectedClientId = null;
            this.state.selectedClientType = null;
            this.state.selectedClientTab = null;
            this.state.drawerStatus = 'idle';
            this.state.drawerError = null;
            window.activeSelectedClientId = null;

            closeClientDrawers();

            // Clear row highlights
            $('.tbl-r.client-selected').removeClass('client-selected');
            $('[data-selected-client]').removeAttr('data-selected-client');

            try {
                localStorage.removeItem('selected_client_id');
                localStorage.removeItem('active_client_id');
                sessionStorage.removeItem('selected_client_id');
                sessionStorage.removeItem('active_client_id');
            } catch (e) {}
        }
    },

    clearDrawerState: function() {
        this.teardownDrawer(true);
    },

    // ── Deterministic Tab Change Lifecycle (Requirement 4) ──
    onTabChange: function(newTab, updateUrl = true) {
        if (newTab === 'requests') newTab = 'request';
        if (newTab === 'custom') newTab = 'internal';

        // 1. Update activeTab
        this.state.activeTab = newTab;
        activeClientTab = newTab;
        if (window.clientListState) {
            window.clientListState.tab = newTab;
        }
        hiringFilter = (newTab === 'hiring') ? 1 : 0;
        page_no = 1;
        try { localStorage.setItem('currentPageClients', 1); } catch (e) {}

        // 2. Invalidate previous tab/client context
        this.invalidateTabContext();

        // 3. Clear selectedClient & full drawer teardown
        this.clearDrawerState();

        // 4. Load
        this.renderTabHeaders(newTab);
        if (typeof loadClientList === 'function') {
            loadClientList(1, updateUrl);
        } else if (typeof fetchCandidates === 'function') {
            fetchCandidates(1, updateUrl);
        }
    },

    invalidateTabContext: function() {
        // Abort in-flight drawer request
        if (this.activeDrawerXhr && this.activeDrawerXhr.readyState !== 4) {
            this.activeDrawerXhr.abort();
            this.activeDrawerXhr = null;
        }

        // Abort in-flight listing request
        if (typeof ClientStore !== 'undefined' && ClientStore.currentXhr && ClientStore.currentXhr.readyState !== 4) {
            ClientStore.currentXhr.abort();
            ClientStore.currentXhr = null;
        }

        // Clean any leftover URL client params so back/forward or reload starts clean
        try {
            const url = new URL(window.location);
            if (url.searchParams.has('open_client')) {
                url.searchParams.delete('open_client');
                window.history.replaceState({ tab: this.state.activeTab, page: 1 }, '', url);
            }
        } catch (e) {}
    },

    clearSelectedClient: function() {
        this.clearDrawerState();
    },

    openClientDrawer: function(clientId, type) {
        if (!clientId) return;
        var self = this;
        var currentTab = this.state.activeTab;
        var numericClientId = parseInt(clientId, 10) || clientId;

        // Atomic Teardown of previous client drawer without closing container
        this.teardownDrawer(false);

        var isDirect = (type === 'direct' || type === 'request');
        var targetDrawerId = isDirect ? 'kt_activitiesDirectClient' : 'kt_activitiesClient';
        var toggleId = isDirect ? 'kt_activitiesDirectClient_toggle' : 'kt_activitiesClient_toggle';
        var bodyId = isDirect ? '#kt_activitiesDirectClient_body' : '#kt_activitiesClient_body';
        var otherBodyId = isDirect ? '#kt_activitiesClient_body' : '#kt_activitiesDirectClient_body';

        // Deterministic Context Shield & Monotonic Token
        var requestToken = ++this.state.drawerRequestToken;
        var contextToken = 'ctx_' + requestToken + '_' + Date.now();
        var drawerContext = {
            contextToken: contextToken,
            requestToken: requestToken,
            clientId: numericClientId,
            clientType: type,
            tab: currentTab,
            openedAt: Date.now()
        };
        this.state.currentDrawerContext = drawerContext;

        // Set state - Single Source of Truth
        this.state.selectedClientId = numericClientId;
        this.state.selectedClientType = type;
        this.state.selectedClientTab = 'overview';
        this.state.drawerStatus = 'loading';
        this.state.drawerError = null;
        window.activeSelectedClientId = numericClientId;

        // Highlight selected row in current tab listing
        $('[data-row-id]').closest('.tbl-r').removeClass('client-selected');
        $(`[data-row-id="${clientId}"]`).closest('.tbl-r').addClass('client-selected');

        // Open target Metronic drawer container
        openClientDrawer(targetDrawerId, toggleId);
        $(otherBodyId).empty();

        var tenantId = {{ (int) (auth()->user()->company_id ?? 0) }};

        // Loading skeleton
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        var loadingText = (type === 'request') ? 'Loading Request & Commercial Terms...' : (isDirect ? 'Loading Direct Client Details...' : 'Loading Client Details...');
        $(bodyId).html(`
            <div class="d-flex flex-column align-items-center justify-content-center p-10" style="min-height:380px;">
                <img src="${baseurl}" height="38px" width="38px" style="object-fit:contain;" alt="Loading...">
                <span class="mt-4 text-gray-700 font-poppins fs-7 fw-600">${loadingText}</span>
                <span class="text-gray-400 font-poppins fs-8 mt-1">Context: ${currentTab.toUpperCase()} tab</span>
            </div>
        `);

        var endpoint;
        if (type === 'request') {
            endpoint = "{{ route('service_company.clients.requestviewmore', ':id') }}".replace(':id', clientId);
        } else if (isDirect) {
            endpoint = "{{ route('service_company.clients.dviewmore', ':id') }}".replace(':id', clientId);
        } else {
            endpoint = "{{ route('service_company.clients.viewmore', ':id') }}".replace(':id', clientId);
        }

        var fetchDrawerData = function(doneCallback) {
            self.activeDrawerXhr = $.ajax({
                type: 'GET',
                url: endpoint,
                cache: false,
                success: function(response) {
                    var activeCtx = self.state.currentDrawerContext;
                    if (!activeCtx || activeCtx.contextToken !== contextToken || self.state.drawerRequestToken !== requestToken || parseInt(self.state.selectedClientId, 10) !== parseInt(clientId, 10)) {
                        console.log(`[Drawer Shield] Discarded stale response for client ${clientId}. Current token: ${activeCtx ? activeCtx.contextToken : 'null'}`);
                        return;
                    }

                    if (response && response.view) {
                        $(bodyId).html(response.view);
                        self.state.drawerStatus = 'loaded';
                        self.markTabLoaded(numericClientId, 'overview');

                        // Initialize Scoped ClientDrawerInstance V3.0 (Enterprise Master Standard)
                        if (typeof window.ClientDrawerInstance === 'function') {
                            if (window.__activeClientDrawer && typeof window.__activeClientDrawer.destroy === 'function') {
                                try { window.__activeClientDrawer.destroy(); } catch(e){}
                            }
                            new window.ClientDrawerInstance({
                                canonicalClientId: numericClientId,
                                clientType: isDirect ? 'direct' : 'internal',
                                companyId: tenantId,
                                container: bodyId
                            });
                        }

                        if (window.SyncSupervisor && typeof window.SyncSupervisor.notifyHint === 'function') {
                            window.SyncSupervisor.notifyHint(numericClientId);
                        }
                        if (typeof doneCallback === 'function') doneCallback(true, response);
                    } else {
                        $(bodyId).html('<div class="p-8 text-center text-danger font-poppins fs-7">No client details available for the selected record.</div>');
                        self.state.drawerStatus = 'error';
                        if (typeof doneCallback === 'function') doneCallback(false, null);
                    }
                },
                error: function(xhr, status) {
                    if (status === 'abort') return;
                    if (typeof doneCallback === 'function') doneCallback(false, null, xhr);
                }
            });
        };

        var executeLegacyDrawerFetch = function() {
            ClientSessionRecoveryManager.executeWithRetry(fetchDrawerData, 1, function(xhr) {
                var activeCtx = self.state.currentDrawerContext;
                if (!activeCtx || activeCtx.contextToken !== contextToken || self.state.drawerRequestToken !== requestToken) return;

                var msg = (!xhr || xhr.status === 0 || !navigator.onLine)
                    ? 'Network connection was interrupted. Please check your connectivity.'
                    : 'Could not load client details (HTTP ' + (xhr.status || 500) + '). Please try again.';

                self.state.drawerStatus = 'error';
                self.state.drawerError = msg;

                $(bodyId).html(`
                    <div class="d-flex flex-column align-items-center justify-content-center p-10 text-center" style="min-height:380px;">
                        <div class="mb-3 text-danger">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div class="fw-bold fs-6 text-gray-800 mb-1">Failed to Load Client Details</div>
                        <div class="text-muted fs-7 mb-4 font-poppins">${msg}</div>
                        <button type="button" class="btn btn-sm btn-primary px-4 py-2" onclick="ClientStateManager.openClientDrawer('${clientId}', '${type}')">
                            <i class="fas fa-redo me-1"></i> Retry Loading
                        </button>
                    </div>
                `);
            });
        };

        // Authoritative Workflow (Option A Master Standard):
        // Always render through authoritative Blade view with Scoped ClientDrawerInstance
        executeLegacyDrawerFetch();
    },

    // ─── Scoped Drawer Tab Switcher V3.0 (< 5ms) ───
    switchDrawerTab: function(clientId, tabName) {
        this.state.selectedClientTab = tabName;
        if (window.__activeClientDrawer && window.__activeClientDrawer.coordinator && typeof window.__activeClientDrawer.coordinator.activateTab === 'function') {
            window.__activeClientDrawer.coordinator.activateTab(tabName);
            return;
        }
        if (typeof window.loadInternalTabContent === 'function') {
            window.loadInternalTabContent(tabName, false);
        } else if (typeof window.loadDirectTabContent === 'function') {
            window.loadDirectTabContent(tabName, false);
        }
    },

    renderTabHeaders: function(tabName) {
        $('.cl-stat-filter').removeClass('active');
        $(`.cl-stat-filter[data-tab="${tabName}"]`).addClass('active');

        if (tabName === 'request') {
            $('#sort-opt-jobs_desc').hide();
            if (window.clientListState && window.clientListState.sort === 'jobs_desc') {
                selectSortOption('recent', 'Recently Added', null, true);
            }
            $('.tbl').addClass('tbl-req');
            $('#clientTableHead').html(`
                <div class="tbl-th-subcard tbl-th-check">
                    <input class="tbl-cb" type="checkbox" id="clientCheckAll" onclick="toggleSelectAllClients(this)" />
                </div>
                <div class="tbl-th-subcard">
                    <div class="tbl-th-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <span>Company Name</span>
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
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>Request Date</span>
                    </div>
                </div>
                <div class="tbl-th-subcard" style="justify-content:flex-end;">
                    <span class="tbl-th-label">Action</span>
                </div>
            `);
        } else {
            $('#sort-opt-jobs_desc').show();
            $('.tbl').removeClass('tbl-req');
            $('#clientTableHead').html(`
                <div class="tbl-th-subcard tbl-th-check">
                    <input class="tbl-cb" type="checkbox" id="clientCheckAll" onclick="toggleSelectAllClients(this)" />
                </div>
                <div class="tbl-th-subcard cl-sortable-th" data-sort-field="name" title="Click to sort by Company Name (A-Z / Z-A)" onclick="handleHeaderSort('name')">
                    <div class="tbl-th-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <span>Company Name</span>
                    </div>
                    <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="tbl-th-subcard" title="Location">
                    <div class="tbl-th-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>Location</span>
                    </div>
                </div>
                <div class="tbl-th-subcard cl-sortable-th" data-sort-field="date" title="Click to sort by Date Added (Newest / Oldest)" onclick="handleHeaderSort('date')">
                    <div class="tbl-th-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>Date</span>
                    </div>
                    <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="tbl-th-subcard" title="Last Activity">
                    <div class="tbl-th-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Last Activity</span>
                    </div>
                </div>
                <div class="tbl-th-subcard cl-sortable-th" data-sort-field="jobs" title="Click to sort by Most Active Jobs" onclick="handleHeaderSort('jobs')">
                    <div class="tbl-th-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <span>Jobs</span>
                    </div>
                    <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="tbl-th-subcard" title="Hiring Pipeline">
                    <div class="tbl-th-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                        <span>Hiring Pipeline</span>
                    </div>
                    <svg class="tbl-th-info" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-toggle="tooltip" title="Funnel: Sourced → Evaluated → Interviews → Offers → Joined"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            `);
            if (typeof updateHeaderSortIndicators === 'function' && window.clientListState) {
                updateHeaderSortIndicators(window.clientListState.sort);
            }
        }
    }
};

function openClientDrawer(drawerId, toggleId) {
    // 1. Hide opposite drawer if active to prevent multi-drawer overlay conflict
    var otherDrawerId = (drawerId === 'kt_activitiesClient') ? 'kt_activitiesDirectClient' : 'kt_activitiesClient';
    var otherDrawerEl = document.getElementById(otherDrawerId);
    if (otherDrawerEl) {
        if (typeof KTDrawer !== 'undefined' && typeof KTDrawer.getInstance === 'function') {
            var otherInst = KTDrawer.getInstance(otherDrawerEl);
            if (otherInst && typeof otherInst.hide === 'function') {
                otherInst.hide();
            }
        }
        otherDrawerEl.classList.remove('drawer-on');
        otherDrawerEl.style.removeProperty('transform');
        otherDrawerEl.style.removeProperty('z-index');
    }

    if (typeof openMetronicDrawer === 'function') {
        openMetronicDrawer(drawerId, '#' + toggleId);
        return;
    }

    var drawerEl = document.getElementById(drawerId);
    if (!drawerEl) return;

    // 2. Official Metronic KTDrawer instance show()
    var opened = false;
    if (typeof KTDrawer !== 'undefined') {
        var drawer = (typeof KTDrawer.getInstance === 'function') ? KTDrawer.getInstance(drawerEl) : null;
        if (!drawer && typeof KTDrawer === 'function') {
            try {
                drawer = new KTDrawer(drawerEl);
            } catch (e) {}
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

    // 3. Toggle button trigger fallback
    if (!opened) {
        var toggleEl = document.getElementById(toggleId);
        if (toggleEl) {
            if (typeof toggleEl.click === 'function') {
                toggleEl.click();
            } else {
                $(toggleEl).trigger('click');
            }
        }
    }

    // 4. Guaranteed DOM fallback: ensures drawer is visibly sliding in and on screen immediately
    if (!drawerEl.classList.contains('drawer')) {
        drawerEl.classList.add('drawer', 'drawer-end');
    }
    drawerEl.classList.add('drawer-on');
    drawerEl.style.transform = 'none';
    drawerEl.style.zIndex = '999999';
    if (!document.querySelector('.drawer-overlay')) {
        var overlay = document.createElement('div');
        overlay.className = 'drawer-overlay custom-cand-drawer-overlay';
        overlay.style.zIndex = '999998';
        overlay.onclick = function() {
            ClientStateManager.clearDrawerState();
        };
        document.body.appendChild(overlay);
    }
}

function closeClientDrawers() {
    ['kt_activitiesClient', 'kt_activitiesDirectClient'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            if (typeof KTDrawer !== 'undefined' && typeof KTDrawer.getInstance === 'function') {
                var inst = KTDrawer.getInstance(el);
                if (inst && typeof inst.hide === 'function') {
                    inst.hide();
                }
            }
            el.classList.remove('drawer-on');
            el.style.removeProperty('transform');
            el.style.removeProperty('z-index');
        }
    });
    document.querySelectorAll('.drawer-overlay, .custom-cand-drawer-overlay').forEach(function(ov) {
        ov.remove();
    });
    document.body.removeAttribute('data-kt-drawer-activities');
    document.body.removeAttribute('data-kt-drawer');
    document.body.style.overflow = '';
}

$(document).on('click', '.show-detail', function (e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    var id = $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id');
    if (!id) return;
    ClientStateManager.openClientDrawer(id, 'internal');
});

$(document).on('click', '.show-detail-direct', function (e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    var id = $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id');
    if (!id) return;
    ClientStateManager.openClientDrawer(id, 'direct');
});

$(document).on('click', '.view-request-terms', function (e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    var id = $(this).data('client-id') || $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id');
    if (!id) return;
    ClientStateManager.openClientDrawer(id, 'request');
});

// Row-level delegation: ensure clicking any row whitespace or cell opens the drawer
$(document).on('click', '.tbl-r', function(e) {
    if ($(e.target).closest('input, button, a, .tbl-cb, .tbl-cell-check, .request_event, .dropdown-menu').length > 0) {
        return;
    }
    var $row = $(this);
    var id = $row.data('row-id') || $row.find('[data-row-id]').first().data('row-id');
    if (!id) return;

    var type = $row.data('client-type') || ($row.hasClass('show-detail-direct') ? 'direct' : ($row.hasClass('is-pending-request') ? 'request' : 'internal'));
    ClientStateManager.openClientDrawer(id, type);
});

$(document).on('click', '#kt_activitiesClient_close, #kt_activitiesDirectClient_close, .cd-close-drawer, #kt_activitiesClient_close_btn, #kt_activitiesDirectClient_close_btn', function(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    ClientStateManager.clearDrawerState();
});

// ── Canonical Client Listing State Machine ──
window.clientListState = {
    tab: {!! json_encode($initialTab ?? 'all') !!},
    search: '',
    country_id: null,
    country_name: '',
    city_id: null,
    city_name: '',
    unspecified_location: false,
    industry_id: '',
    activity: '',
    pipeline: '',
    sort: 'recent',
    page: 1,
    per_page: 10,
    cursor: null,

    datasetVersion: {{ (int)($initialDataVersion ?? 1) }},
    mode: 'ram', // 'ram' | 'server'
    loading: false,
    requestId: 0,
    hasMore: false,
    total: {{ (int)($initialTotal ?? 0) }}
};
if (window.clientListState.tab === 'requests') window.clientListState.tab = 'request';
if (window.clientListState.tab === 'custom') window.clientListState.tab = 'internal';

window.initialLocationFacets = {!! json_encode($locationFacets ?? ['total' => 0, 'located_count' => 0, 'unspecified_count' => 0, 'hubs' => []]) !!};
window.allCountriesList = {!! json_encode($countries ?? []) !!};
window.initialIndustriesManifest = {!! json_encode($industriesManifest ?? App\Services\IndustryTaxonomyService::getGlobalManifest()) !!};
window.initialRecencyCounts = {!! json_encode($initialRecencyCounts ?? ['all' => 0, '7d' => 0, '30d' => 0, '90d' => 0, 'dormant' => 0, 'never' => 0]) !!};
window.initialPipelineCounts = {!! json_encode($initialPipelineCounts ?? ['all' => 0, 'active' => 0, 'none' => 0]) !!};

// ── Offline & Zero-Server IndexedDB Reference Cache ──
const ClientLocationDB = {
    dbName: 'jero_client_db',
    storeName: 'reference_data',
    version: 1,

    open: function() {
        return new Promise(function(resolve, reject) {
            if (!window.indexedDB) return reject(new Error('IndexedDB not supported'));
            const req = indexedDB.open(ClientLocationDB.dbName, ClientLocationDB.version);
            req.onupgradeneeded = function(e) {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(ClientLocationDB.storeName)) {
                    db.createObjectStore(ClientLocationDB.storeName, { keyPath: 'key' });
                }
            };
            req.onsuccess = function(e) { resolve(e.target.result); };
            req.onerror = function(e) { reject(e.target.error); };
        });
    },

    get: function(key) {
        return new Promise(function(resolve) {
            ClientLocationDB.open().then(function(db) {
                const tx = db.transaction(ClientLocationDB.storeName, 'readonly');
                const store = tx.objectStore(ClientLocationDB.storeName);
                const req = store.get(key);
                req.onsuccess = function() { resolve(req.result ? req.result.value : null); };
                req.onerror = function() { resolve(null); };
            }).catch(function() { resolve(null); });
        });
    },

    set: function(key, value) {
        return new Promise(function(resolve) {
            ClientLocationDB.open().then(function(db) {
                const tx = db.transaction(ClientLocationDB.storeName, 'readwrite');
                const store = tx.objectStore(ClientLocationDB.storeName);
                store.put({ key: key, value: value });
                tx.oncomplete = function() { resolve(true); };
                tx.onerror = function() { resolve(false); };
            }).catch(function() { resolve(false); });
        });
    }
};

function initCountryBundle() {
    if (window.allCountriesList && window.allCountriesList.length > 0) {
        ClientLocationDB.set('countries_list', window.allCountriesList);
        return;
    }
    ClientLocationDB.get('countries_list').then(function(cached) {
        if (cached && cached.length > 0) {
            window.allCountriesList = cached;
            renderLocationDropdown($('#clLocSearchInp').val() || '');
            return;
        }
        fetch('{{ asset("assets/service_company/client/countries.json") }}')
            .then(function(res) { return res.ok ? res.json() : null; })
            .then(function(countries) {
                if (countries && countries.length > 0) {
                    window.allCountriesList = countries;
                    ClientLocationDB.set('countries_list', countries);
                    renderLocationDropdown($('#clLocSearchInp').val() || '');
                }
            }).catch(function(err) {
                console.warn('[Countries Bundle] Offline fetch fallback:', err);
            });
    });
}

function initIndustryBundle() {
    if (window.initialIndustriesManifest && window.initialIndustriesManifest.industries && window.initialIndustriesManifest.industries.length > 0) {
        ClientLocationDB.set('industries_manifest', window.initialIndustriesManifest);
        return;
    }
    ClientLocationDB.get('industries_manifest').then(function(cached) {
        if (cached && cached.industries && cached.industries.length > 0) {
            window.initialIndustriesManifest = cached;
            renderIndustryDropdown($('#clIndSearchInp').val() || '');
        }
    });
}

// ── Web Worker In-Browser OLAP Engine (Zero Server Read Strain) ──
let clWorker = null;
let clWorkerReady = false;
let pendingWorkerQuery = null;

function renderClientRows(rows) {
    if (!rows || rows.length === 0) {
        return `
        <div class="cl-empty-state">
            <div class="cl-empty-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="10" r="10"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
            <div class="cl-empty-title">No clients found</div>
            <div class="cl-empty-desc">No clients match your selected filters or search query.</div>
        </div>`;
    }

    return rows.map(function(r) {
        const logoHtml = (r.logo_url && r.logo_url.length > 10)
            ? `<img src="${r.logo_url}" alt="${escapeAttr(r.initials)}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
               <span class="cl-initials-fallback ${r.bg_color || 'bg-light-primary text-primary'}" style="display:none;">${escapeAttr(r.initials)}</span>`
            : `<span class="cl-initials-fallback ${r.bg_color || 'bg-light-primary text-primary'}">${escapeAttr(r.initials)}</span>`;

        const indHtml = r.industry ? `<div class="cl-ind" title="${escapeAttr(r.industry)}">${escapeAttr(r.industry)}</div>` : '';
        const sizeHtml = r.company_size ? `<div class="cl-size">${escapeAttr(r.company_size)}</div>` : '';

        let locHtml = '';
        if (r.city) {
            locHtml += `<div class="cl-loc-city" title="${escapeAttr(r.city)}">${escapeAttr(r.city)}</div>`;
        }
        if (r.state) {
            locHtml += `<div class="cl-loc-state" title="${escapeAttr(r.state)}">${escapeAttr(r.state)}</div>`;
        }
        if (r.country) {
            locHtml += `<div class="cl-loc-country" title="${escapeAttr(r.country)}">${escapeAttr(r.country)}</div>`;
        }
        if (!locHtml) {
            if (r.location_main) {
                locHtml = `<div class="cl-loc-city" title="${escapeAttr(r.location_main)}">${escapeAttr(r.location_main)}</div>`;
            } else {
                locHtml = `<div class="cl-loc-empty">—</div>`;
            }
        }

        if (r.is_pending_request) {
            const invId = r.staffing_company_invitation_id || r.id;
            const fromComp = r.from_company_id || 0;
            const targetId = r.target_id || r.client_id || fromComp || r.id;
            const hiringCount = (r.jobs && (r.jobs.hiring || r.jobs.active)) ? (r.jobs.hiring || r.jobs.active) : 0;
            const expiresFormatted = r.expires_on || 'in 7 days';
            const daysAgo = r.days_ago || 'Just now';

            return `
            <div class="cl-primary-card tbl-r is-pending-request" data-pending-id="${invId}" data-row-id="${targetId}" data-client-type="request">
                <div class="cl-subcards-grid">
                    <!-- SUB-CARD 1: Client Identity -->
                    <div class="cl-subcard cl-subcard-identity">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title">Client Identity</span>
                            <div class="cl-subcard-badges">
                                <span class="cl-scope-pill pill-direct">Direct Client</span>
                                <span class="badge" style="background:#fef3c7; color:#d97706; font-size:9px; font-weight:700; padding:1.5px 6px; border-radius:4px; border:1px solid #fde68a;">⚡ Request</span>
                            </div>
                        </div>
                        <div class="cl-identity-body">
                            <div class="cl-logo-box">${logoHtml}</div>
                            <div class="cl-details-box">
                                <div class="cl-name view-request-terms" data-row-id="${targetId}" data-client-type="request">${escapeAttr(r.name)}</div>
                                ${indHtml || '<div class="cl-ind text-muted">—</div>'}
                                <div class="cl-size text-muted">—</div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 2: Location -->
                    <div class="cl-subcard cl-subcard-loc">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title">Location</span>
                        </div>
                        <div class="cl-loc-body">
                            <svg class="cl-loc-pin" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div class="cl-loc-lines">
                                ${locHtml}
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 3: Jobs Status -->
                    <div class="cl-subcard cl-subcard-jobs">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title">Jobs Status</span>
                        </div>
                        <div class="cl-jobs-body tbl-cell-jobs">
                            <div class="cl-job-line ${hiringCount > 0 ? 'has-count cl-job-hiring' : 'no-count'}">
                                <span class="cl-job-bullet ${hiringCount > 0 ? 'bullet-hiring' : 'bullet-neutral'}"></span>
                                <span class="cl-job-num">${hiringCount}</span>
                                <span class="cl-job-label">Hiring</span>
                            </div>
                            <div class="cl-job-line no-count">
                                <span class="cl-job-bullet bullet-neutral"></span>
                                <span class="cl-job-num">0</span>
                                <span class="cl-job-label">Hold</span>
                            </div>
                            <div class="cl-job-line no-count">
                                <span class="cl-job-bullet bullet-neutral"></span>
                                <span class="cl-job-num">0</span>
                                <span class="cl-job-label">Closed</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-CARD 4: Request Status & SLA -->
                    <div class="cl-subcard cl-subcard-pipeline">
                        <div class="cl-subcard-head">
                            <span class="cl-subcard-title">Request Status & SLA</span>
                        </div>
                        <div class="cl-pipe-body d-flex flex-column gap-1 align-items-start" style="padding-top:2px;">
                            <div class="d-flex align-items-center gap-1">
                                <span class="bullet bullet-dot bg-warning animate-pulse" style="width:7px; height:7px;"></span>
                                <span class="text-warning fw-bold" style="font-size:11px;">Action Needed</span>
                            </div>
                            <div class="text-danger fw-semibold" style="font-size:10.5px;">Expires ${escapeAttr(expiresFormatted)}</div>
                            <div class="text-muted" style="font-size:10.5px;">Active in system</div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM STRIP: Received date, Last Activity, and Accept/Decline action buttons -->
                <div class="cl-card-bottom-strip">
                    <div class="cl-bottom-meta">
                        <span class="cl-meta-item">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>Received: <strong>${escapeAttr(r.connected_on || '-')}</strong></span>
                        </span>
                        <span class="cl-meta-dot">•</span>
                        <span class="cl-meta-item">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span>Activity: <strong>${escapeAttr(daysAgo)}</strong></span>
                        </span>
                    </div>
                    <div class="cl-bottom-actions tbl-cell-req-actions">
                        <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="${invId}" data-row-event="1" data-row-from_company="${fromComp}" style="background:#10b981; color:#fff; border:none; border-radius:6px; font-size:11px;" title="Accept Direct Client Partnership">Accept</button>
                        <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="${invId}" data-row-event="-1" data-row-from_company="${fromComp}" style="background:#ef4444; color:#fff; border:none; border-radius:6px; font-size:11px;" title="Decline Partnership Request">Decline</button>
                        <button type="button" class="btn btn-sm btn-light fw-bold px-2 py-1.5 view-request-terms" data-row-id="${targetId}" data-client-type="request" style="background:#f1f5f9; color:#2563eb; border:1px solid #e2e8f0; border-radius:6px; font-size:11px;" title="View Client Details & Terms">Terms</button>
                    </div>
                </div>
            </div>`;
        }

        const jobActive = r.jobs ? (r.jobs.hiring || r.jobs.active || 0) : 0;
        const jobHold = r.jobs ? (r.jobs.hold || 0) : 0;
        const jobClosed = r.jobs ? (r.jobs.closed || 0) : 0;

        const sourced = r.pv ? (r.pv[0] || 0) : (r.pipeline ? (r.pipeline.sourced || 0) : 0);
        const screening = r.pv ? (r.pv[1] || 0) : (r.pipeline ? (r.pipeline.screening || 0) : 0);
        const evaluated = r.pv ? (r.pv[2] || 0) : (r.pipeline ? (r.pipeline.evaluated || 0) : 0);
        const negotiations = r.pv ? (r.pv[3] || 0) : (r.pipeline ? (r.pipeline.negotiations || 0) : 0);
        const offers = r.pv ? (r.pv[4] || 0) : (r.pipeline ? (r.pipeline.offers || 0) : 0);
        const joined = r.pv ? (r.pv[5] || 0) : (r.pipeline ? (r.pipeline.joined || 0) : 0);
        const rejected = r.pv ? (r.pv[6] || 0) : (r.pipeline ? (r.pipeline.rejected || 0) : 0);
        const inReview = r.pv ? (r.pv[7] || 0) : (r.pipeline ? (r.pipeline.in_review || 0) : 0);
        const interviews = r.pv ? (r.pv[8] || 0) : (r.pipeline ? (r.pipeline.interviews || 0) : 0);

        const activePipeline = sourced + screening + evaluated + negotiations + offers + joined;
        const applications = activePipeline + rejected + inReview;

        const daysAgo = r.days_ago || '';
        const clientTypeClass = r.is_direct ? 'type-direct' : 'type-internal';
        const clientTypeCss = r.client_type_css || (r.is_direct ? 'show-detail-direct' : 'show-detail');
        const clientTypeLabel = r.client_type || (r.is_direct ? 'Direct Client' : 'Internal Client');
        const clientTypeAttr = r.is_direct ? 'direct' : 'internal';
        const editUrl = '{{ url("service_company/client/edit") }}/' + r.id;

        return `
        <div class="cl-primary-card tbl-r ${clientTypeCss} ${jobActive > 0 ? 'is-active-client' : 'is-inactive-client'}" data-row-id="${r.id}" data-client-type="${clientTypeAttr}" data-active-jobs="${jobActive}" data-client-name="${escapeAttr(r.name || '')}">
            <div class="cl-subcards-grid">
                <!-- SUB-CARD 1: Client Identity (Internal & Active at TOP) -->
                <div class="cl-subcard cl-subcard-identity">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Client Identity</span>
                        <div class="cl-subcard-badges">
                            <span class="cl-scope-pill ${clientTypeClass} ${clientTypeCss}" data-row-id="${r.id}" data-client-type="${clientTypeAttr}">
                                ${escapeAttr(clientTypeLabel)}
                            </span>
                            <span class="cl-status-pill ${jobActive > 0 ? 'pill-active' : 'pill-inactive'}">
                                <span class="cl-status-pill-dot"></span>
                                <span>${jobActive > 0 ? 'Active' : 'Inactive'}</span>
                            </span>
                        </div>
                    </div>
                    <div class="cl-identity-body">
                        <div class="cl-logo-box">${logoHtml}</div>
                        <div class="cl-details-box">
                            <div class="cl-name ${clientTypeCss}" data-row-id="${r.id}" data-client-type="${clientTypeAttr}">${escapeAttr(r.name)}</div>
                            ${indHtml || '<div class="cl-ind text-muted">—</div>'}
                            ${sizeHtml || '<div class="cl-size text-muted">—</div>'}
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: Location -->
                <div class="cl-subcard cl-subcard-loc">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Location</span>
                    </div>
                    <div class="cl-loc-body">
                        <svg class="cl-loc-pin" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <div class="cl-loc-lines">
                            ${locHtml}
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 3: Jobs Status -->
                <div class="cl-subcard cl-subcard-jobs">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Jobs Status</span>
                    </div>
                    <div class="cl-jobs-body tbl-cell-jobs">
                        <div class="cl-job-line ${jobActive > 0 ? 'has-count cl-job-hiring' : 'no-count'}">
                            <span class="cl-job-bullet ${jobActive > 0 ? 'bullet-hiring' : 'bullet-neutral'}"></span>
                            <span class="cl-job-num">${jobActive}</span>
                            <span class="cl-job-label">Hiring</span>
                        </div>
                        <div class="cl-job-line ${jobHold > 0 ? 'has-count cl-job-hold' : 'no-count'}">
                            <span class="cl-job-bullet ${jobHold > 0 ? 'bullet-hold' : 'bullet-neutral'}"></span>
                            <span class="cl-job-num">${jobHold}</span>
                            <span class="cl-job-label">Hold</span>
                        </div>
                        <div class="cl-job-line ${jobClosed > 0 ? 'has-count cl-job-closed' : 'no-count'}">
                            <span class="cl-job-bullet ${jobClosed > 0 ? 'bullet-closed' : 'bullet-neutral'}"></span>
                            <span class="cl-job-num">${jobClosed}</span>
                            <span class="cl-job-label">Closed</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: Hiring Pipeline (Restored Vibrant Color Combinations Even If 0) -->
                <div class="cl-subcard cl-subcard-pipeline">
                    <div class="cl-subcard-head">
                        <div class="cl-subcard-title-wrap">
                            <svg class="cl-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                            </svg>
                            <span class="cl-subcard-title">Hiring Pipeline</span>
                        </div>
                    </div>
                    <div class="cl-pipe-body tbl-cell-pipeline" title="Total Applications: ${applications} | Rejected: ${rejected} | Interviews: ${interviews}">
                        <div class="cl-pipeline-flow">
                            <div class="cl-pipe-step step-sourcing ${sourced > 0 ? 'has-count' : ''}" title="Sourcing: ${sourced}">
                                <span class="cl-pipe-pill pill-sourcing">${sourced}</span>
                                <span class="cl-pipe-label">Sourcing</span>
                            </div>
                            <div class="cl-pipe-step step-screening ${screening > 0 ? 'has-count' : ''}" title="Screening: ${screening}">
                                <span class="cl-pipe-pill pill-screening">${screening}</span>
                                <span class="cl-pipe-label">Screening</span>
                            </div>
                            <div class="cl-pipe-step step-evaluation ${evaluated > 0 ? 'has-count' : ''}" title="Evaluation: ${evaluated}">
                                <span class="cl-pipe-pill pill-evaluation">${evaluated}</span>
                                <span class="cl-pipe-label">Evaluation</span>
                            </div>
                            <div class="cl-pipe-step step-negotiations ${negotiations > 0 ? 'has-count' : ''}" title="Negotiation: ${negotiations}">
                                <span class="cl-pipe-pill pill-negotiations">${negotiations}</span>
                                <span class="cl-pipe-label">Negotiation</span>
                            </div>
                            <div class="cl-pipe-step step-offer ${offers > 0 ? 'has-count' : ''}" title="Offer: ${offers}">
                                <span class="cl-pipe-pill pill-offer">${offers}</span>
                                <span class="cl-pipe-label">Offer</span>
                            </div>
                            <div class="cl-pipe-step step-joined ${joined > 0 ? 'has-count' : ''}" title="Joined: ${joined}">
                                <span class="cl-pipe-pill pill-joined">${joined}</span>
                                <span class="cl-pipe-label">Joined</span>
                            </div>
                            <div class="cl-pipe-step step-rejected ${rejected > 0 ? 'has-count' : ''}" title="Rejected: ${rejected}">
                                <span class="cl-pipe-pill pill-rejected">${rejected}</span>
                                <span class="cl-pipe-label">Rejected</span>
                            </div>
                            <div class="cl-pipe-divider" aria-hidden="true"></div>
                            <div class="cl-pipe-step step-interview ${interviews > 0 ? 'has-count' : ''}" title="Interview: ${interviews}">
                                <span class="cl-pipe-pill pill-interview">${interviews}</span>
                                <span class="cl-pipe-label">Interview</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM STRIP: Connected Date, Last Activity, Applications Badge & Ghost Actions -->
            <div class="cl-card-bottom-strip">
                <div class="cl-bottom-meta">
                    <span class="cl-meta-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span>Connected: <strong>${escapeAttr(r.connected_on || '-')}</strong></span>
                    </span>
                    <span class="cl-meta-dot">•</span>
                    ${renderClientEngagementBadge(r, daysAgo)}
                    <span class="cl-meta-dot">•</span>
                    <span class="cl-meta-item cl-meta-apps">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Applications:</span>
                        <span class="cl-apps-badge ${applications > 0 ? 'has-apps' : ''}">${applications}</span>
                    </span>
                </div>
                <div class="cl-bottom-actions tbl-cell-actions">
                    <button type="button" class="cl-btn-act ${clientTypeCss}" data-row-id="${r.id}" data-client-type="${clientTypeAttr}" title="Quick View">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    <a href="${editUrl}" class="cl-btn-act" title="Edit Client">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </a>
                    <div class="dropdown">
                        <button type="button" class="cl-btn-act" data-bs-toggle="dropdown" aria-expanded="false" title="More options">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="1"/>
                                <circle cx="12" cy="5" r="1"/>
                                <circle cx="12" cy="19" r="1"/>
                            </svg>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:8px; border:1px solid #e2e8f0; font-size:12px; min-width:140px; padding:4px 0;">
                            <li>
                                <a class="dropdown-item py-1.5 px-3 text-gray-700 ${clientTypeCss}" href="javascript:void(0);" data-row-id="${r.id}" data-client-type="${clientTypeAttr}">
                                    <i class="fas fa-eye text-primary me-2"></i> View Details
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-1.5 px-3 text-gray-700" href="${editUrl}">
                                    <i class="fas fa-edit text-info me-2"></i> Edit Client
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1 border-gray-200"></li>
                            <li>
                                <a class="dropdown-item py-1.5 px-3 text-danger delete-client-btn" href="javascript:void(0);" data-row-id="${r.id}">
                                    <i class="fas fa-trash-alt text-danger me-2"></i> Delete
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}

function renderClientPagination(pagi) {
    if (!pagi || pagi.total <= 0) return '';
    const perPage = pagi.per_page || 10;
    const total = pagi.total;
    const totalPages = pagi.last_page || Math.ceil(total / perPage);
    const curPage = pagi.current_page || 1;
    const from = (curPage - 1) * perPage + 1;
    const to = Math.min(curPage * perPage, total);

    let pagesHtml = '';
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= curPage - 2 && i <= curPage + 2)) {
            const isActive = (i === curPage);
            pagesHtml += `<button class="cl-pagi-page pagi-page ${isActive ? 'pagi-active pagi-active-disabled' : ''}" data-page="${i}" data-listType="allcandidate">${i}</button>`;
        } else if (i === curPage - 3 || i === curPage + 3) {
            pagesHtml += `<span class="cl-pagi-dots">…</span>`;
        }
    }

    return `
    <div class="cl-pagi-bar">
        <div class="cl-pagi-info">
            Showing ${from} to ${to} of ${total} clients
        </div>
        <div class="cl-pagi-controls">
            <div class="cl-pagi-perpage-wrap">
                <span class="cl-pagi-perpage-lbl">Rows per page:</span>
                <select class="cl-pagi-perpage-select pagi-perpage">
                    <option value="10" ${perPage == 10 ? 'selected' : ''}>10</option>
                    <option value="25" ${perPage == 25 ? 'selected' : ''}>25</option>
                    <option value="50" ${perPage == 50 ? 'selected' : ''}>50</option>
                    <option value="100" ${perPage == 100 ? 'selected' : ''}>100</option>
                </select>
            </div>
            <button class="cl-pagi-btn pagi-arrow ${curPage <= 1 ? 'pagi-disabled' : ''}" data-page="${curPage - 1}" data-listType="allcandidate" aria-label="Previous page">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            ${pagesHtml}
            <button class="cl-pagi-btn pagi-arrow ${curPage >= totalPages ? 'pagi-disabled' : ''}" data-page="${curPage + 1}" data-listType="allcandidate" aria-label="Next page">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
    </div>`;
}

function handleWorkerMessage(data) {
    if (!data || !data.type) return;

    if (data.type === 'INIT_COMPLETE') {
        clWorkerReady = true;
    } else if (data.type === 'QUERY_RESULT') {
        const q = pendingWorkerQuery;
        if (!q || q.id !== data.queryId) return;
        pendingWorkerQuery = null;

        const renderedHtml = renderClientRows(data.rows);
        const renderedPagi = renderClientPagination({
            total: data.total,
            per_page: data.per_page,
            current_page: data.page,
            last_page: Math.ceil(data.total / data.per_page)
        });

        if (data.counts) {
            $('#span_clientAll, #tabBadgeAll').text(data.counts.all);
            $('#span_clientCustom, #tabBadgeInternal').text(data.counts.internal);
            $('#span_clientDirect, #tabBadgeDirect').text(data.counts.direct);
            $('#span_clientHiring, #tabBadgeHiring').text(data.counts.hiring);
            if (data.counts.inactive !== undefined) {
                $('#span_clientInactive, #tabBadgeInactive').text(data.counts.inactive);
            }
        }

        const syntheticResp = {
            html: renderedHtml,
            total: data.total,
            pagination: renderedPagi,
            rows: data.rows,
            projection_status: 'synced'
        };

        if (q.cacheKey) {
            ClientStore.set(q.cacheKey, syntheticResp);
        }

        applyClientResponse(syntheticResp, data.page);
    } else if (data.type === 'PIPELINE_REVISION_APPLIED') {
        if (window.ClientPipelineStore) {
            window.ClientPipelineStore.updateDOM(data.clientId, data);
            window.ClientPipelineStore.updateDrawer(data.clientId, data);
        }
    } else if (data.type === 'CSV_READY') {
        const blob = new Blob([data.csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        const dateStr = new Date().toISOString().slice(0, 10);
        const scopeStr = data.scope || 'clients';
        link.setAttribute("href", url);
        link.setAttribute("download", "jero_clients_" + scopeStr + "_export_" + dateStr + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

        if (window.ClientExportManager) {
            window.ClientExportManager.setButtonState('COMPLETE');
            setTimeout(function() { window.ClientExportManager.setButtonState('IDLE'); }, 2000);
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Export Complete: ' + (data.rowCount || 0) + ' clients in ' + (data.durationMs || 10) + 'ms (User RAM)',
                showConfirmButton: false,
                timer: 3500
            });
        }
    } else if (data.type === 'EXPORT_ERROR') {
        if (window.ClientExportManager) {
            window.ClientExportManager.setButtonState('IDLE');
        }
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Export Notice',
                text: data.message || 'Unable to export dataset at this time.'
            });
        }
    }
}

function initClientWorker() {
    if (!window.Worker) return;
    try {
        clWorker = new Worker('{{ asset("assets/service_company/client/cl-data-worker.js") }}');
        clWorker.onmessage = function(e) {
            handleWorkerMessage(e.data);
        };
        clWorker.onerror = function(err) {
            console.warn('[CL Worker] Worker runtime error:', err);
            clWorkerReady = false;
        };

        // Seed with initial rows if present
        @if(!empty($initialRows))
        const initialRows = {!! json_encode($initialRows) !!};
        if (initialRows && initialRows.length > 0) {
            clWorker.postMessage({
                type: 'INIT_DATASET',
                rows: initialRows,
                version: {{ (int)($initialDataVersion ?? 1) }}
            });
            clWorkerReady = true;
        }
        @endif

        // Hydrate from active projection in background
        fetchActiveProjection();
    } catch(e) {
        console.warn('[CL Worker] Web Worker init skipped:', e);
        clWorker = null;
    }
}

function fetchActiveProjection() {
    const companyId = {{ (int)auth()->user()->company_id }};
    const userId = {{ (int)auth()->id() }};
    const etagKey = 'jero_cl_etag_tenant_' + companyId + '_user_' + userId;
    const cachedEtag = localStorage.getItem(etagKey);
    const headers = {};
    if (cachedEtag) {
        headers['If-None-Match'] = cachedEtag;
    }

    const idbStoreKey = 'jero_client_projection:tenant=' + companyId + ':user=' + userId + ':projection=clients:schema=2';

    fetch('{{ route("service_company.clients.activeClientProjection") }}', {
        headers: headers
    }).then(async function(res) {
        if (res.status === 304) {
            const stored = await ClientLocationDB.get(idbStoreKey);
            if (stored && stored.rows && stored.rows.length > 0 && clWorker) {
                clWorker.postMessage({
                    type: 'INIT_DATASET',
                    rows: stored.rows,
                    version: stored.data_version || 1
                });
                clWorkerReady = true;
            }
            return;
        }
        if (res.ok) {
            const newEtag = res.headers.get('ETag');
            if (newEtag) localStorage.setItem(etagKey, newEtag);
            const data = await res.json();
            if (data && data.rows && clWorker) {
                if (ClientStore.latestMutationVersion && data.data_version && data.data_version < ClientStore.latestMutationVersion) {
                    console.warn('[Sync] Ignored stale projection version:', data.data_version, 'current mutation:', ClientStore.latestMutationVersion);
                    return;
                }
                clWorker.postMessage({
                    type: 'INIT_DATASET',
                    rows: data.rows,
                    version: data.data_version || 1
                });
                clWorkerReady = true;
                ClientLocationDB.set(idbStoreKey, data);
            }
        }
    }).catch(function(err) {
        console.warn('[CL Worker] Background activeClientProjection fetch skipped:', err);
    });
}

// ── Bounded Browser RAM Store (ClientStore) with SWR & Request Token Guard ──
const ClientStore = {
    cache: new Map(),
    maxEntries: 50,
    currentXhr: null,
    latestRequestId: 0,
    latestMutationVersion: 0,
    maxStaleAgeMs: 5000, // 5 seconds bounded staleness for SWR

    getKey: function(tab, page, search, sort, countryId, cityId, unspecified, industryId, activity, pipeline, perPage) {
        return `${tab}:${page}:${search || ''}:${sort || 'recent'}:${countryId || ''}:${cityId || ''}:${unspecified ? 1 : 0}:${industryId || ''}:${activity || ''}:${pipeline || ''}:${perPage || 10}`;
    },

    get: function(key) {
        const entry = this.cache.get(key);
        if (!entry) return null;
        return {
            data: entry.data,
            age: Date.now() - entry.timestamp,
            isStale: (Date.now() - entry.timestamp) > this.maxStaleAgeMs
        };
    },

    set: function(key, data) {
        if (this.cache.size >= this.maxEntries) {
            const oldestKey = this.cache.keys().next().value;
            this.cache.delete(oldestKey);
        }
        this.cache.set(key, {
            data: data,
            timestamp: Date.now()
        });
    },

    invalidateTab: function(tab) {
        for (const k of this.cache.keys()) {
            if (k.startsWith(`${tab}:`)) {
                this.cache.delete(k);
            }
        }
    },

    clear: function() {
        this.cache.clear();
    }
};

@if(!empty($initialHtml))
(function() {
    var initTab = {!! json_encode($initialTab ?? 'all') !!};
    if (initTab === 'requests') initTab = 'request';
    if (initTab === 'custom') initTab = 'internal';
    var initKey = ClientStore.getKey(initTab, 1, '', 'recent', '', '', 0, '', '', '', 10);
    ClientStore.set(initKey, {
        html: {!! json_encode($initialHtml) !!},
        total: {{ (int)($initialTotal ?? 0) }},
        pagination: {!! json_encode($initialPagination ?? '') !!},
        rows: {!! json_encode($initialRows ?? []) !!},
        recency_counts: {!! json_encode($initialRecencyCounts ?? []) !!},
        pipeline_counts: {!! json_encode($initialPipelineCounts ?? []) !!},
        data_version: {{ (int)($initialDataVersion ?? 1) }}
    });
})();
@endif

// Hydrate initial facet counts
if (window.initialRecencyCounts) {
    updateRecencyCounts(window.initialRecencyCounts);
}
if (window.initialPipelineCounts) {
    updatePipelineCounts(window.initialPipelineCounts);
}

var page_no = localStorage.getItem('currentPageClients') || 1;
var activeClientTab = {!! json_encode($initialTab ?? 'all') !!};
if (activeClientTab === 'requests') activeClientTab = 'request';
if (activeClientTab === 'custom') activeClientTab = 'internal';
var hiringFilter = (activeClientTab === 'hiring') ? 1 : 0;
var initialOpenClientId = {{ (int)($initialOpenClient ?? 0) }};

function syncUrlState(replace = false) {
    try {
        const url = new URL(window.location);
        if (activeClientTab && activeClientTab !== 'all') {
            url.searchParams.set('tab', activeClientTab);
        } else {
            url.searchParams.delete('tab');
        }

        const searchVal = $('#kt_tagify_2').val() || '';
        if (searchVal.trim()) {
            url.searchParams.set('search', searchVal.trim());
        } else {
            url.searchParams.delete('search');
        }

        if (clientListState.unspecified_location) {
            url.searchParams.set('country', 'unspecified');
            url.searchParams.delete('city');
        } else if (clientListState.country_id) {
            url.searchParams.set('country', clientListState.country_id);
            if (clientListState.city_id) {
                url.searchParams.set('city', clientListState.city_id);
            } else {
                url.searchParams.delete('city');
            }
        } else {
            url.searchParams.delete('country');
            url.searchParams.delete('city');
        }

        if (clientListState.industry_id) {
            url.searchParams.set('industry', clientListState.industry_id);
        } else {
            url.searchParams.delete('industry');
        }

        if (clientListState.activity) {
            url.searchParams.set('activity', clientListState.activity);
        } else {
            url.searchParams.delete('activity');
        }

        if (clientListState.pipeline) {
            url.searchParams.set('pipeline', clientListState.pipeline);
        } else {
            url.searchParams.delete('pipeline');
        }

        const sortVal = normalizeSortValue($('#sortingfilter').val() || clientListState.sort || 'recent');
        if (sortVal && sortVal !== 'recent') {
            url.searchParams.set('sort', sortVal);
        } else {
            url.searchParams.delete('sort');
        }

        if (page_no > 1) {
            url.searchParams.set('page', page_no);
        } else {
            url.searchParams.delete('page');
        }

        // Drop open_client after initial consume
        url.searchParams.delete('open_client');

        if (replace) {
            window.history.replaceState({ tab: activeClientTab, page: page_no }, '', url);
        } else {
            window.history.pushState({ tab: activeClientTab, page: page_no }, '', url);
        }
    } catch (e) {
        // Fallback for sandboxed frames
    }
}

function hydrateStateFromUrl(shouldFetch = true) {
    try {
        const params = new URLSearchParams(window.location.search);
        let tab = params.get('tab') || activeClientTab || 'all';
        if (tab === 'requests') tab = 'request';
        if (tab === 'custom') tab = 'internal';

        const search = params.get('search') || params.get('q') || '';
        const country = params.get('country') || '';
        const city = params.get('city') || '';
        const industry = params.get('industry') || '';
        const activity = params.get('activity') || '';
        const pipeline = params.get('pipeline') || '';
        const sort = params.get('sort') || params.get('sortingfilter') || 'recent';
        const page = parseInt(params.get('page'), 10) || 1;
        const openClient = parseInt(params.get('open_client'), 10) || initialOpenClientId || 0;

        $('#kt_tagify_2').val(search);
        clientListState.search = search;

        // Hydrate location
        if (country === 'unspecified') {
            selectLocationUnspecified(false);
        } else if (country) {
            selectLocationByCountryAndCity(country, city, false);
        } else {
            resetLocationFilter(false);
        }

        // Hydrate industry
        if (industry) {
            selectIndustryFilter(industry, null, false);
        } else {
            clearIndustryFilter(false);
        }

        // Hydrate activity & pipeline
        selectActivityOption(activity, null, null, true);
        selectPipelineOption(pipeline, null, null, true);

        // Hydrate sort
        selectSortOption(sort, null, null, true);

        setClientTab(tab, null, false);
        if (shouldFetch) {
            loadClientList(page, false);
        }

        if (openClient > 0) {
            setTimeout(function() {
                triggerOpenClientDrawer(openClient);
            }, 350);
        }
    } catch (e) {
        // Fallback
    }
}

window.addEventListener('popstate', function(event) {
    hydrateStateFromUrl(true);
});

function triggerOpenClientDrawer(clientId) {
    const $target = $(`.show-detail[data-row-id="${clientId}"], .show-detail-direct[data-row-id="${clientId}"], .cl-name[data-row-id="${clientId}"]`).first();
    if ($target.length) {
        $target.trigger('click');
    } else {
        var isDirect = (ClientStateManager.state.activeTab === 'direct');
        ClientStateManager.openClientDrawer(clientId, isDirect ? 'direct' : 'internal');
    }
    try {
        const url = new URL(window.location);
        url.searchParams.delete('open_client');
        window.history.replaceState({ tab: ClientStateManager.state.activeTab, page: page_no }, '', url);
    } catch(e) {}
}

function setClientTab(tabName, el, updateUrl = true) {
    ClientStateManager.onTabChange(tabName, updateUrl);
}

function applyClientResponse(response, targetPage) {
    if (!response || typeof response !== 'object') return;
    requestAnimationFrame(function() {
        $('.loadingMessage').css({ display: 'none' });
        $('.loader_search').css({ display: 'none' });
        $('.page-loader').css('display', 'none');
        try { localStorage.setItem('currentPageClients', targetPage); } catch (e) {}

        // Honest Search Degradation UX (Invariant I10)
        if (response.projection_status === 'syncing') {
            $('#cl_search_sync_banner').show();
        } else {
            $('#cl_search_sync_banner').hide();
        }

        $('#allClients').html(response.html);

        // Highlight selected client if one is active on current tab
        if (typeof ClientStateManager !== 'undefined') {
            var selectedId = ClientStateManager.getSelectedClientId();
            if (selectedId) {
                $(`[data-row-id="${selectedId}"]`).closest('.tbl-r').addClass('client-selected');
            }
        }

        if (response.total !== undefined) {
            if (activeClientTab === 'all') {
                $('#span_clientAll').html(response.total);
                $('#tabBadgeAll').html(response.total);
            } else if (activeClientTab === 'internal' || activeClientTab === 'custom') {
                $('#span_clientCustom').html(response.total);
                $('#tabBadgeInternal').html(response.total);
            } else if (activeClientTab === 'direct') {
                $('#span_clientDirect').html(response.total);
                $('#tabBadgeDirect').html(response.total);
            } else if (activeClientTab === 'hiring') {
                $('#span_clientHiring').html(response.total);
                $('#tabBadgeHiring').html(response.total);
            } else if (activeClientTab === 'inactive') {
                $('#span_clientInactive').html(response.total);
                $('#tabBadgeInactive').html(response.total);
            } else if (activeClientTab === 'request') {
                $('#span_clientRequest').html(response.total);
                $('#tabBadgeRequest').html(response.total);
            }
        }
        $('#pagination').html(response.pagination);
        clientListState.loading = false;
        if (response.total !== undefined) {
            clientListState.total = response.total;
            clientListState.hasMore = (targetPage * (clientListState.per_page || 10)) < response.total;
        }
        if (response.data_version) {
            clientListState.datasetVersion = response.data_version;
        }
        if (response.recency_counts) {
            updateRecencyCounts(response.recency_counts);
        }
        if (response.pipeline_counts) {
            updatePipelineCounts(response.pipeline_counts);
        }
    });
}

function loadClientList(page, updateUrl = true, cursor = null) {
    var targetPage = page || page_no || 1;
    page_no = targetPage;
    try { localStorage.setItem('currentPageClients', targetPage); } catch (e) {}

    if (cursor) {
        clientListState.cursor = cursor;
    } else if (targetPage <= 1) {
        clientListState.cursor = null;
    }

    if (updateUrl) {
        syncUrlState();
    }

    var perPage = $('#pagination .pagi-perpage').val() || localStorage.getItem('candidatePerPage') || 10;
    var searchVal = $('#kt_tagify_2').val() || '';
    var sortVal = $('#sortingfilter').val() || 'recent';
    var countryVal = clientListState.country_id || $('#country').val() || '';
    var cityVal = clientListState.city_id || $('#city_id').val() || '';
    var unspecifiedVal = clientListState.unspecified_location ? 1 : 0;
    var industryVal = clientListState.industry_id || $('#industryFilter').val() || '';
    var activityVal = clientListState.activity || $('#activityFilter').val() || '';
    var pipelineVal = clientListState.pipeline || $('#pipelineFilter').val() || '';

    var cacheKey = ClientStore.getKey(activeClientTab, targetPage, searchVal, sortVal, countryVal, cityVal, unspecifiedVal, industryVal, activityVal, pipelineVal, perPage);
    var cachedEntry = ClientStore.get(cacheKey);

    // Stale-While-Revalidate (SWR): Immediate perceived instant render if cache entry exists
    if (cachedEntry) {
        applyClientResponse(cachedEntry.data, targetPage);
        if (!cachedEntry.isStale) {
            return; // Warm hit within 5s SLA - 0 network requests!
        }
        // Stale entry: UI already rendered instantly; background revalidation runs without blocking UI
    } else {
        // Cold fetch: clear container to prevent showing previous tab's stale HTML/empty state
        $('#allClients').html('<div class="d-flex align-items-center justify-content-center py-15"><div class="spinner-border text-warning" role="status" style="width: 2.2rem; height: 2.2rem;"><span class="visually-hidden">Loading...</span></div></div>');
        if (searchVal != '') {
            $('.loader_search').css({ display: 'block' });
        } else {
            $('.loadingMessage').css({ display: 'block' });
        }
    }

    // Abort previous in-flight request to prevent race conditions
    if (ClientStore.currentXhr && ClientStore.currentXhr.readyState !== 4) {
        ClientStore.currentXhr.abort();
    }

    // Request Generation Token Guard & Monotonic Concurrency Invariant
    var requestId = ++ClientStore.latestRequestId;
    clientListState.requestId = requestId;
    clientListState.loading = true;

    var reqHeaders = {};
    if (cachedEntry && cachedEntry.data && cachedEntry.data.etag) {
        reqHeaders['If-None-Match'] = cachedEntry.data.etag;
    }

    ClientStore.currentXhr = $.ajax({
        url: `{!! route('service_company.clients.all_clients_data_ajax') !!}?page=${targetPage}`,
        type: 'GET',
        headers: reqHeaders,
        data: {
            custom_status: $('#status').val(),
            search: searchVal,
            sortingfilter: sortVal,
            sort: sortVal,
            cus_search: searchVal,
            country: countryVal,
            country_id: countryVal,
            country_name: clientListState.country_name,
            city_id: cityVal,
            city_name: clientListState.city_name,
            unspecified_location: unspecifiedVal,
            industry_id: industryVal,
            industry: industryVal,
            activity: activityVal,
            pipeline: pipelineVal,
            cursor: clientListState.cursor,
            hiring: hiringFilter,
            tab: activeClientTab,
            size: perPage,
            per_page: perPage,
            min_version: ClientStore.latestMutationVersion || 0
        },
        statusCode: {
            304: function() {
                // ETag Match: Server confirmed cached data is authoritative (0 bytes transferred)
                if (cachedEntry) {
                    cachedEntry.timestamp = Date.now();
                    applyClientResponse(cachedEntry.data, targetPage);
                }
                $('.loadingMessage').css({ display: 'none' });
                $('.loader_search').css({ display: 'none' });
                $('.page-loader').css('display', 'none');
            }
        },
        success: function(response) {
            // Discard stale out-of-order response or empty 304 response bodies
            if (requestId !== ClientStore.latestRequestId) return;
            if (!response || typeof response !== 'object') return;

            if (response.cursor) {
                clientListState.cursor = response.cursor;
            }

            ClientStore.set(cacheKey, response);
            applyClientResponse(response, targetPage);
        },
        error: function(xhr, status, error) {
            clientListState.loading = false;
            if (status !== 'abort' && requestId === ClientStore.latestRequestId) {
                $('.loadingMessage').css({ display: 'none' });
                $('.loader_search').css({ display: 'none' });
                $('.page-loader').css('display', 'none');

                if ((xhr.status === 0 || xhr.status === 419) && !ClientSessionRecoveryManager.isReconnecting) {
                    ClientSessionRecoveryManager.isReconnecting = true;
                    ClientSessionRecoveryManager.pingHeartbeat(function(authOk) {
                        ClientSessionRecoveryManager.isReconnecting = false;
                        if (authOk) {
                            loadClientList(targetPage, false);
                            return;
                        }
                    });
                }

                $('#allClients').html(`
                    <div class="cl-empty-state py-12 text-center">
                        <div class="cl-empty-icon mb-3">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div class="fw-bold fs-6 text-gray-800 mb-1">Unable to Load Client Data</div>
                        <div class="text-muted fs-7 mb-4">Connection was interrupted or timed out. Click below to reconnect.</div>
                        <button type="button" class="btn btn-sm btn-primary px-4 py-2" onclick="loadClientList(${targetPage}, false)">
                            <i class="fas fa-redo me-1"></i> Retry Connection
                        </button>
                    </div>
                `);
            }
        }
    });
}

// Preserve backwards-compatible global alias for legacy callers while protecting main function
window.loadClientList = loadClientList;
window.fetchCandidates = loadClientList;

$(document).ready(function() {
    // Phase 0: Initialize Session & Idle Inactivity Recovery Engine
    if (typeof ClientSessionRecoveryManager !== 'undefined') {
        ClientSessionRecoveryManager.init();
    }

    // Phase 1: Hydrate static country & industry bundles from IndexedDB / JSON
    initCountryBundle();
    initIndustryBundle();

    // Phase 2: Start background Web Worker OLAP engine
    initClientWorker();

    $(document).on('click', '#pagination .pagi-page, #pagination .pagi-arrow', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const cursor = $(this).data('cursor') || null;
        if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
        loadClientList(page, true, cursor);
    });

    $(document).on('change', '#pagination .pagi-perpage', function() {
        var selectedPerPage = $(this).val();
        localStorage.setItem('candidatePerPage', selectedPerPage);
        localStorage.setItem('currentPageClients', 1);
        page_no = 1;
        loadClientList(1, true);
    });

    // Hydrate state from URL on page load
    hydrateStateFromUrl(false);

    const urlParams = new URLSearchParams(window.location.search);
    const hasFilterParams = urlParams.has('country') || urlParams.has('city') || urlParams.has('industry') || urlParams.has('activity') || urlParams.has('search') || urlParams.has('q') || urlParams.has('tab') || urlParams.has('sort');

    if ($('#allClients .tbl-r').length === 0 || hasFilterParams) {
        loadClientList(1, false);
    }
});

$(document).on('click', '.request_event', function () {
    var $btn = $(this);
    if ($btn.hasClass('disabled') || $btn.prop('disabled')) return;

    var id = $btn.data('row-id');
    var event_id = $btn.data('row-event');
    var from_company = $btn.data('row-from_company');
    var actionText = (event_id == -1) ? 'Decline' : 'Accept';
    var confirmBtnClass = (event_id == -1) ? 'btn btn-danger' : 'btn btn-success';

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: confirmBtnClass,
            cancelButton: 'btn btn-light me-3'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, ' + actionText + ' it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            var $row = $btn.closest('.tbl-r');
            var originalRowHtml = $row.html();
            var originalClasses = $row.attr('class');
            var $actionContainer = $btn.parent();
            var originalActionsHtml = $actionContainer.html();

            // Optimistic Perceived 0ms UI Transition
            $actionContainer.html(`
                <span class="badge bg-light-${event_id == 1 ? 'success text-success' : 'danger text-danger'} fw-bold px-3 py-1.5 d-inline-flex align-items-center gap-1">
                    <span class="spinner-border spinner-border-sm me-1" role="status" style="width:10px;height:10px;"></span>
                    ${event_id == 1 ? 'Accepting...' : 'Declining...'}
                </span>
            `);

            // Optimistically decrement request counters
            var $reqBadges = $('#span_clientRequest, #tabBadgeRequest');
            var origReqCount = parseInt($reqBadges.first().text(), 10) || 0;
            if (origReqCount > 0) {
                $reqBadges.text(origReqCount - 1);
            }
            var $dirBadges = $('#span_clientDirect, #tabBadgeDirect');
            var origDirCount = parseInt($dirBadges.first().text(), 10) || 0;
            if (event_id == 1) {
                $dirBadges.text(origDirCount + 1);
            }

            var mutationId = 'mut_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            var url = "{{ route('service_company.clients.request_update') }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': '{{ csrf_token() }}',
                    'invitation_id': id,
                    'event_id': event_id,
                    'from_company': from_company,
                    'mutation_id': mutationId
                },
                success: function (response) {
                    ClientStore.invalidateTab('request');
                    ClientStore.invalidateTab('all');
                    ClientStore.invalidateTab('direct');

                    if (response.mutation_version) {
                        ClientStore.latestMutationVersion = response.mutation_version;
                    }

                    if (response.clientDataCnt) {
                        if ($('#span_clientAll').length) $('#span_clientAll').html(response.clientDataCnt.clientAll);
                        if ($('#span_clientDirect').length) $('#span_clientDirect').html(response.clientDataCnt.clientDirect);
                        if ($('#span_clientCustom').length) $('#span_clientCustom').html(response.clientDataCnt.clientCustom);
                        if ($('#span_clientHiring').length) $('#span_clientHiring').html(response.clientDataCnt.clientHiring);
                        if ($('#span_clientInactive').length && response.clientDataCnt.clientInactive !== undefined) $('#span_clientInactive').html(response.clientDataCnt.clientInactive);
                        if ($('#span_clientRequest').length) $('#span_clientRequest').html(response.clientDataCnt.clientRequest);
                    }

                    if (window.clientSyncChannel) {
                        window.clientSyncChannel.postMessage({
                            type: 'CLIENT_REQUEST_MUTATED',
                            mutationId: mutationId,
                            invitationId: id,
                            eventId: event_id,
                            version: response.mutation_version || null,
                            clientDataCnt: response.clientDataCnt || null
                        });
                    }

                    $actionContainer.html(`
                        <span class="badge bg-light-${event_id == 1 ? 'success text-success' : 'danger text-danger'} fw-bold px-3 py-1.5 d-inline-flex align-items-center gap-1">
                            ${event_id == 1 ? '✓ Accepted' : '✕ Declined'}
                        </span>
                    `);

                    swalWithBootstrapButtons.fire(
                        actionText + 'ed!',
                        response.message || ('Request has been ' + actionText.toLowerCase() + 'ed.'),
                        'success'
                    );

                    if (ClientStateManager.state.activeTab === 'request') {
                        $row.slideUp(250, function() {
                            $(this).remove();
                            if ($('#allClients .tbl-r').length === 0) {
                                $('#allClients').html(`
                                    <div class="cl-empty-state">
                                        <div class="cl-empty-icon">
                                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
                                        </div>
                                        <div class="cl-empty-title">No Pending Client Requests</div>
                                        <div class="cl-empty-desc">You're all caught up. New client partnership requests will appear here when received.</div>
                                    </div>
                                `);
                            }
                        });
                    } else {
                        // Refresh after brief delay so ES projection index propagates and row becomes normal Direct Client
                        setTimeout(function() {
                            loadClientList(1, false);
                        }, 500);
                    }
                },
                error: function(xhr) {
                    // Deterministic Rollback
                    $actionContainer.html(originalActionsHtml);
                    $row.attr('class', originalClasses);
                    $reqBadges.text(origReqCount);
                    if (event_id == 1) {
                        $('#span_clientDirect, #tabBadgeDirect').text(origDirCount);
                    }

                    if (xhr.status === 409) {
                        var conflictMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'This request has already been processed by another recruiter.';
                        swalWithBootstrapButtons.fire('Already Handled', conflictMsg, 'warning');
                        loadClientList(1, false);
                    } else {
                        swalWithBootstrapButtons.fire('Error', 'Failed to update request status. Please check your connection and try again.', 'error');
                    }
                }
            });
        }
    });
});

// ── Universal Real-Time Multi-Account Synchronization (Phase 3) ──
function handleClientMutationEvent(data) {
    if (!data) return;

    if (data.version) {
        if (ClientStore.latestMutationVersion && data.version < ClientStore.latestMutationVersion) {
            console.warn('[Sync] Dropped stale out-of-order mutation version:', data.version, 'current:', ClientStore.latestMutationVersion);
            return;
        }
        ClientStore.latestMutationVersion = data.version;
    }

    const companyId = {{ (int)auth()->user()->company_id }};
    const userId = {{ (int)auth()->id() }};
    localStorage.removeItem('jero_cl_etag_tenant_' + companyId + '_user_' + userId);

    // Invalidate main browser RAM stores
    ClientStore.clear();

    // Invalidate drawer cache and notify SyncSupervisor (JDCR V5.2)
    if (typeof DrawerLRUCache !== 'undefined' && typeof DrawerLRUCache.clear === 'function') {
        DrawerLRUCache.clear();
    }
    const targetCid = (data.client && data.client.id) ? data.client.id : (data.client_id || data.id);
    if (targetCid && window.SyncSupervisor) {
        window.SyncSupervisor.notifyHint(targetCid);
    }

    // If a specific client record is attached, delta-upsert directly in Worker
    if (data.client && clWorker && clWorkerReady) {
        clWorker.postMessage({
            type: 'DELTA_UPSERT',
            client: data.client,
            version: data.version || null
        });
    } else {
        // Re-hydrate active projection in background
        fetchActiveProjection();
    }

    if (data.clientDataCnt) {
        if ($('#span_clientAll').length && data.clientDataCnt.clientAll !== undefined) $('#span_clientAll').html(data.clientDataCnt.clientAll);
        if ($('#span_clientDirect').length && data.clientDataCnt.clientDirect !== undefined) $('#span_clientDirect').html(data.clientDataCnt.clientDirect);
        if ($('#span_clientCustom').length && data.clientDataCnt.clientCustom !== undefined) $('#span_clientCustom').html(data.clientDataCnt.clientCustom);
        if ($('#span_clientHiring').length && data.clientDataCnt.clientHiring !== undefined) $('#span_clientHiring').html(data.clientDataCnt.clientHiring);
        if ($('#span_clientInactive').length && data.clientDataCnt.clientInactive !== undefined) $('#span_clientInactive').html(data.clientDataCnt.clientInactive);
        if ($('#span_clientRequest').length && data.clientDataCnt.clientRequest !== undefined) $('#span_clientRequest').html(data.clientDataCnt.clientRequest);
        if ($('#tabBadgeInactive').length && data.clientDataCnt.clientInactive !== undefined) $('#tabBadgeInactive').html(data.clientDataCnt.clientInactive);
    }

    // Refresh current view with zero page reload
    loadClientList(page_no, false);
}

// 1. Cross-Tab Browser Channel (Same Computer / Multiple Tabs)
(function() {
    if (typeof BroadcastChannel !== 'undefined') {
        window.clientSyncChannel = new BroadcastChannel('jero_clients_cross_tab_sync');

        window.clientSyncChannel.onmessage = function(event) {
            const data = event.data;
            if (!data) return;

            if (data.type === 'CLIENT_REQUEST_MUTATED' || data.type === 'CLIENT_MUTATED' || data.type === 'JOB_MUTATED' || data.type === 'JOB_STATUS_CHANGED') {
                handleClientMutationEvent(data);
            } else if (data.type === 'PIPELINE_UPDATED' && data.payload) {
                if (window.ClientPipelineStore) {
                    window.ClientPipelineStore.apply(data.payload, false);
                }
            }
        };
    }
})();

// 2. Laravel Reverb WebSocket Push (Across All Team Recruiters in Organization)
(function() {
    if (typeof window.Echo !== 'undefined') {
        try {
            const companyId = {{ (int)auth()->user()->company_id }};
            window.Echo.private('company.' + companyId)
                .listen('.client.mutated', function(e) {
                    handleClientMutationEvent(e);
                })
                .listen('.ClientMutated', function(e) {
                    handleClientMutationEvent(e);
                })
                .listen('.ClientRequestMutated', function(e) {
                    handleClientMutationEvent(e);
                })
                .listen('.JobStatusChanged', function(e) {
                    handleClientMutationEvent(e);
                })
                .listen('.JobMutated', function(e) {
                    handleClientMutationEvent(e);
                })
                .listen('.pipeline.updated', function(e) {
                    if (window.ClientPipelineStore) {
                        window.ClientPipelineStore.apply(e, true);
                    }
                })
                .listen('PipelineUpdated', function(e) {
                    if (window.ClientPipelineStore) {
                        window.ClientPipelineStore.apply(e, true);
                    }
                })
                .listen('.client.activity.updated', function(e) {
                    if (e && e.tenant_version && e.tenant_version > ClientStore.latestMutationVersion) {
                        ClientStore.latestMutationVersion = e.tenant_version;
                        ClientStore.clear();
                        loadClientList(page_no, false);
                    }
                });
        } catch(echoErr) {
            console.warn('[Echo] Reverb listener registration skipped:', echoErr);
        }
    }
})();

// 3. Tab Focus / Sleep Recovery Guard
window.addEventListener('focus', function() {
    if (typeof ClientSessionRecoveryManager !== 'undefined') {
        ClientSessionRecoveryManager.onWakeUp('focus');
    } else {
        fetchActiveProjection();
    }
});
</script>
@endpush
