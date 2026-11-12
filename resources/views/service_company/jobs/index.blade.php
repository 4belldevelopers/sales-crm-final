@extends('layouts.service_company_layout')
@push('head-script')
<!-- Responsive CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<!-- Responsive CSS End -->
<style>
  /* .customedivclass{ 
    padding: 1rem !important;
   } */
@media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr
  {width: max-content!important;}
  .table > :not(caption) > * > *
  { padding: 0rem; }
  td:nth-child(n+2),
  th:nth-child(n+2)
  { width: revert!important;}
}
@media only screen and (min-width: 992px){
  /* Intent: size the Total Jobs drawer at 80% of the content canvas, not the left sidebar. */
  #kt_activitiesJob {
    --jero-service-aside-width: 225px;
    width: min(80vw, calc((100vw - var(--jero-service-aside-width)) * .8)) !important;
    max-width: calc(100vw - var(--jero-service-aside-width)) !important;
  }
  body.aside-minimize #kt_activitiesJob { --jero-service-aside-width: 75px; }
}
@media only screen and (max-width: 991.98px){
  #kt_activitiesJob {
    width: 100vw !important;
    max-width: 100vw !important;
  }
}
@media only screen and (min-width: 992px){
  #kt_activitiesRecruiter {
    width: min(1320px, 96vw) !important;
    max-width: 96vw !important;
  }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesTerms {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesTerms {width: 1200px;}
}
/* @media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesStaffing {width: 1100px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesStaffing {width: 1200px;}
} */
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesHM {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesHM {width: 1200px;}
}

@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesClientjob_toggle {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesClientjob_toggle {width: 1200px;}
}


.table-custom-class tbody tr {
    height: 82px; /* Adjust the height as per your requirement */
    position: relative;
}
.modal-backdrop.show {
    opacity: 0 !important;
    display: none;
}

.hidden-button {
  display: none; /* Initially hidden */
  position: absolute;
  bottom: 10px; /* Adjust position as needed */
  left: 50%;
  transform: translateX(-50%);
  padding: 10px 20px;
  background-color: #007BFF;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.show_hidden:hover .hidden-button {
  display: block; /* Show the button on hover */
}

/* 15.6" Screen Fit: Guaranteed Zero Horizontal Overflow per Rule 14 & Rule 20 */
#kt_content_container {
  width: 100% !important;
  max-width: 100% !important;
  overflow-x: hidden !important;
}
.filter-bar {
  flex-wrap: nowrap !important;
}
.filter-bar .search-input-wrap {
  min-width: 140px;
  flex-shrink: 1;
}
.filter-bar .search-input,
.filter-bar .filter-select,
.filter-bar .sortingfilterselect,
.filter-bar .more-filters-btn {
  font-size: 12.5px !important;
  white-space: nowrap;
}
#kt_content.flex-column-fluid {
  flex: 0 0 auto !important;
  padding-bottom: 0 !important;
}

/* Multi-City Work Location Badge & Enterprise Popover (0% Server Load) */
.table-card {
  overflow: visible !important;
}
.table-header.table-header-standard,
.table-header.table-header-request {
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}
.loc-row {
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  font-size: 11.5px !important;
  color: var(--text-mid, #5e6278) !important;
  white-space: nowrap !important;
  cursor: pointer;
}
.loc-row .city-name-text {
  max-width: 68px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: inline-block;
  vertical-align: middle;
}
.loc-more-badge-wrap {
  position: relative;
  display: inline-flex;
  align-items: center;
}
.loc-more-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #f1f5f9;
  color: #009ef7;
  border: 1px solid #c2e7ff;
  border-radius: 9px;
  font-size: 9.5px;
  font-weight: 700;
  padding: 0 4px;
  line-height: 13px;
  cursor: pointer;
  vertical-align: middle;
  transition: all 0.15s ease-in-out;
  flex-shrink: 0;
}
.loc-more-badge:hover,
.loc-more-badge-wrap:hover .loc-more-badge,
.loc-row:hover .loc-more-badge,
.loc-more-badge-wrap.is-clicked-active .loc-more-badge,
.loc-row.is-clicked-active .loc-more-badge {
  background: #009ef7;
  color: #ffffff;
  border-color: #009ef7;
  box-shadow: 0 1px 3px rgba(0, 158, 247, 0.35);
}
.loc-popover-card {
  position: absolute;
  bottom: calc(100% + 7px);
  left: 50%;
  transform: translateX(-50%);
  background: #1e1e2d;
  color: #ffffff;
  border-radius: 6px;
  padding: 8px 10px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.45);
  z-index: 99999;
  min-width: 140px;
  max-width: 250px;
  pointer-events: none;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.15s ease-in-out, visibility 0.15s ease-in-out;
}
.loc-more-badge-wrap:hover .loc-popover-card,
.loc-row:hover .loc-popover-card,
.loc-more-badge-wrap.is-clicked-active .loc-popover-card,
.loc-row.is-clicked-active .loc-popover-card {
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}
.loc-popover-card::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #1e1e2d transparent transparent transparent;
}
.loc-popover-body {
  display: flex !important;
  flex-direction: column !important;
  gap: 4px !important;
  max-height: 180px;
  overflow-y: auto;
  padding: 2px 0;
}
.loc-popover-body::-webkit-scrollbar {
  width: 4px;
}
.loc-popover-body::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 2px;
}
.loc-popover-item {
  font-size: 11px;
  font-weight: 500;
  color: #f1f1f2;
  display: flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
  padding: 2px 4px;
  border-radius: 4px;
}
.loc-popover-item:hover {
  background: rgba(255, 255, 255, 0.08);
}
.loc-popover-item i {
  font-size: 9px;
  color: #009ef7;
  flex-shrink: 0;
}
.loc-popover-primary i {
  color: #50cd89;
  font-size: 8.5px;
}
.loc-popover-primary strong {
  color: #ffffff;
  font-weight: 600;
}

/* ==========================================================================
   Executive Requisition Source Metric Strip (5 Responsive Metric Cards)
   ========================================================================== */
.cd-jobs-kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    gap: 10px !important;
    margin-bottom: 12px !important;
    width: 100% !important;
}

@media (max-width: 1200px) {
    .cd-jobs-kpi-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 768px) {
    .cd-jobs-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

.cd-kpi-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 12px 14px !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    text-decoration: none !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    transition: all 0.15s ease-in-out !important;
    cursor: pointer !important;
    min-width: 0 !important;
}

.cd-kpi-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
    transform: translateY(-1px);
}

.cd-kpi-card.cd-kpi-active,
.cd-kpi-card.jh-card-active {
    border-color: #009ef7 !important;
    background: #f0f9ff !important;
    box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.2) !important;
}

.cd-kpi-icon {
    width: 36px !important;
    height: 36px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14.5px !important;
    flex-shrink: 0 !important;
}

.cd-kpi-icon.icon-blue {
    background: #eff6ff !important;
    color: #2563eb !important;
}

.cd-kpi-icon.icon-emerald {
    background: #ecfdf5 !important;
    color: #059669 !important;
}

.cd-kpi-icon.icon-amber {
    background: #fffbeb !important;
    color: #d97706 !important;
}

.cd-kpi-icon.icon-cyan {
    background: #ecfeff !important;
    color: #0891b2 !important;
}

.cd-kpi-icon.icon-purple {
    background: #f5f3ff !important;
    color: #7c3aed !important;
}

.cd-kpi-info {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    flex: 1 !important;
}

.cd-kpi-label {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cd-kpi-num {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
    font-variant-numeric: tabular-nums !important;
    font-family: 'Poppins', sans-serif !important;
}

.cd-kpi-card.cd-kpi-active .cd-kpi-num,
.cd-kpi-card.jh-card-active .cd-kpi-num {
    color: #009ef7 !important;
}

/* ==========================================================================
   Tier 3: Enterprise Search Bar & Filters Card
   ========================================================================== */
.cd-jobs-search-filter-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
    margin-bottom: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.cd-jobs-search-filter-card .cd-jobs-search-subcard {
    flex: 1 1 280px !important;
    width: auto !important;
    max-width: none !important;
    min-width: 0 !important;
    height: 38px !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 0 14px !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.15s ease-in-out !important;
}

.cd-jobs-search-filter-card .cd-jobs-search-subcard:hover {
    background: #ffffff !important;
    border-color: #94a3b8 !important;
}

.cd-jobs-search-filter-card .cd-jobs-search-subcard:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.14) !important;
}

.cd-jobs-search-filter-card .cd-jobs-search-subcard i.fa-search {
    color: #64748b !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}

.cd-jobs-search-filter-card .cd-jobs-search-subcard input {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12.5px !important;
    font-weight: 500 !important;
    color: #0f172a !important;
    width: 100% !important;
    height: 36px !important;
    line-height: 36px !important;
    font-family: inherit !important;
}

/* ==========================================================================
   Filter Pills Group inside Search & Filters Card
   ========================================================================== */
.cd-jobs-filters-group {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    flex-shrink: 0 !important;
}

.cd-filter-pill-wrap {
    display: flex !important;
    align-items: center !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 0 10px !important;
    height: 38px !important;
    gap: 6px !important;
    transition: all 0.15s ease !important;
}

.cd-filter-pill-wrap:hover {
    background: #ffffff !important;
    border-color: #94a3b8 !important;
}

.cd-filter-pill-wrap:focus-within {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.cd-filter-label {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    white-space: nowrap !important;
}

.cd-filter-select {
    border: none !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    padding: 0 22px 0 0 !important;
    height: 36px !important;
    line-height: 36px !important;
    cursor: pointer !important;
    font-family: 'Poppins', sans-serif !important;
}

@media (max-width: 767.98px) {
    .cd-jobs-filters-group {
        width: 100% !important;
        flex-shrink: 1 !important;
    }
    .cd-filter-pill-wrap {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }
}

.cd-action-link {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #009ef7 !important;
    text-decoration: none !important;
    display: inline-block !important;
    margin-top: 2px !important;
    transition: color 0.15s ease !important;
    cursor: pointer !important;
}

.cd-action-link:hover {
    color: #0077b6 !important;
    text-decoration: underline !important;
}

.cd-jobs-stream-wrapper {
    position: relative !important;
    width: 100% !important;
    min-height: 200px !important;
}

.cd-track-badge {
    text-decoration: none !important;
}
</style>

@include('service_company.client.components.tab-jobs-style')
<link href="{{ asset('demo/css/joblist.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

<style>
/* Page-isolated overrides loaded after shared component stylesheets (Rule 17) */

/* Header Card — Inspired by Clients Tracker Executive Header */
.cd-jobs-header-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 14px 20px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
}

.cd-jobs-header-left {
    gap: 12px !important;
    min-width: 0 !important;
}

.cd-jobs-header-icon {
    width: 44px !important;
    height: 44px !important;
    border-radius: 10px !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #2563eb !important;
    font-size: 18px !important;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.06) !important;
}

.cd-jobs-header-title-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}

.cd-jobs-header-title {
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    letter-spacing: -0.015em !important;
    line-height: 1.2 !important;
    margin: 0 !important;
}

.cd-jobs-verified-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}

.cd-jobs-header-count-pill {
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 2px 9px !important;
    border-radius: 9999px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #dbeafe !important;
    font-variant-numeric: tabular-nums !important;
    line-height: 1.35 !important;
}

.cd-jobs-header-sub {
    font-size: 13px !important;
    font-weight: 400 !important;
    color: #64748b !important;
    margin-top: 3px !important;
    line-height: 1.35 !important;
}

.cd-jobs-add-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    border-radius: 8px !important;
    padding: 8px 16px !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    line-height: 1.25 !important;
    box-shadow: 0 1px 3px rgba(0, 158, 247, 0.25) !important;
    transition: all 0.15s ease !important;
}

.cd-jobs-add-btn:hover {
    transform: translateY(-0.5px);
    box-shadow: 0 3px 8px rgba(0, 158, 247, 0.32) !important;
}

/* 5 KPI Cards — Inspired by Clients Tracker Executive Stat Strip */
.cd-jobs-kpi-grid {
    gap: 10px !important;
    margin-bottom: 14px !important;
}

.cd-kpi-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 11px !important;
    padding: 10px 12px !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: stretch !important;
    justify-content: space-between !important;
    gap: 0 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease !important;
}

.cd-kpi-top {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    margin-bottom: 6px !important;
    min-width: 0 !important;
    width: 100% !important;
}

.cd-kpi-icon {
    width: 36px !important;
    height: 36px !important;
    min-width: 36px !important;
    border-radius: 8px !important;
    font-size: 15px !important;
    transition: all 0.15s ease !important;
}

.cd-kpi-icon.icon-blue { background: #eff6ff !important; color: #2563eb !important; }
.cd-kpi-icon.icon-emerald { background: #ecfdf5 !important; color: #16a34a !important; }
.cd-kpi-icon.icon-amber { background: #fff7ed !important; color: #ea580c !important; }
.cd-kpi-icon.icon-cyan { background: #ecfeff !important; color: #0891b2 !important; }
.cd-kpi-icon.icon-purple { background: #faf5ff !important; color: #9333ea !important; }

.cd-kpi-info {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    flex: 1 !important;
    overflow: hidden !important;
}

.cd-kpi-num-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 6px !important;
    min-width: 0 !important;
}

.cd-kpi-num {
    font-size: 19px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.1 !important;
    letter-spacing: -0.01em !important;
    font-variant-numeric: tabular-nums !important;
}

.cd-kpi-label {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    margin-top: 2px !important;
    line-height: 1.2 !important;
}

.cd-kpi-invite-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 2px 7px !important;
    background: #f5f3ff !important;
    border: 1px solid #ddd6fe !important;
    border-radius: 9999px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #7c3aed !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}

.cd-kpi-invite-dot {
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    background: #8b5cf6 !important;
    display: inline-block !important;
    box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2) !important;
}

.cd-kpi-sub {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 10.5px !important;
    font-weight: 500 !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    color: #64748b !important;
}

.cd-kpi-sub svg, .cd-kpi-sub i {
    flex-shrink: 0 !important;
    font-size: 10px !important;
}

.cd-kpi-sub.sub-blue { color: #2563eb !important; }
.cd-kpi-sub.sub-green { color: #16a34a !important; }
.cd-kpi-sub.sub-orange { color: #ea580c !important; }
.cd-kpi-sub.sub-cyan { color: #0891b2 !important; }
.cd-kpi-sub.sub-purple { color: #9333ea !important; }

/* Color-Coded Active KPI Card Highlighting (Inspired by Clients Tracker) */
.cd-kpi-card.cd-kpi-active[data-kpi="all"] {
    background: #eff6ff !important;
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 1.5px #2563eb, 0 4px 14px rgba(37, 99, 235, 0.12) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="all"] .cd-kpi-icon {
    background: #ffffff !important;
    color: #2563eb !important;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.15) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="all"] .cd-kpi-num { color: #1d4ed8 !important; }
.cd-kpi-card.cd-kpi-active[data-kpi="all"] .cd-kpi-label { color: #1e40af !important; font-weight: 700 !important; }

.cd-kpi-card.cd-kpi-active[data-kpi="direct"] {
    background: #ecfdf5 !important;
    border-color: #34d399 !important;
    box-shadow: 0 0 0 1.5px #059669, 0 4px 14px rgba(5, 150, 105, 0.12) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="direct"] .cd-kpi-icon {
    background: #ffffff !important;
    color: #059669 !important;
    box-shadow: 0 1px 3px rgba(5, 150, 105, 0.15) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="direct"] .cd-kpi-num { color: #065f46 !important; }
.cd-kpi-card.cd-kpi-active[data-kpi="direct"] .cd-kpi-label { color: #047857 !important; font-weight: 700 !important; }

.cd-kpi-card.cd-kpi-active[data-kpi="internal"] {
    background: #fff7ed !important;
    border-color: #fb923c !important;
    box-shadow: 0 0 0 1.5px #ea580c, 0 4px 14px rgba(234, 88, 12, 0.12) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="internal"] .cd-kpi-icon {
    background: #ffffff !important;
    color: #ea580c !important;
    box-shadow: 0 1px 3px rgba(234, 88, 12, 0.15) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="internal"] .cd-kpi-num { color: #9a3412 !important; }
.cd-kpi-card.cd-kpi-active[data-kpi="internal"] .cd-kpi-label { color: #c2410c !important; font-weight: 700 !important; }

.cd-kpi-card.cd-kpi-active[data-kpi="my_organization"] {
    background: #ecfeff !important;
    border-color: #22d3ee !important;
    box-shadow: 0 0 0 1.5px #0891b2, 0 4px 14px rgba(8, 145, 178, 0.12) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="my_organization"] .cd-kpi-icon {
    background: #ffffff !important;
    color: #0891b2 !important;
    box-shadow: 0 1px 3px rgba(8, 145, 178, 0.15) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="my_organization"] .cd-kpi-num { color: #155e75 !important; }
.cd-kpi-card.cd-kpi-active[data-kpi="my_organization"] .cd-kpi-label { color: #0e7490 !important; font-weight: 700 !important; }

.cd-kpi-card.cd-kpi-active[data-kpi="job_request"] {
    background: #faf5ff !important;
    border-color: #c084fc !important;
    box-shadow: 0 0 0 1.5px #9333ea, 0 4px 14px rgba(147, 51, 234, 0.12) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="job_request"] .cd-kpi-icon {
    background: #ffffff !important;
    color: #9333ea !important;
    box-shadow: 0 1px 3px rgba(147, 51, 234, 0.15) !important;
}
.cd-kpi-card.cd-kpi-active[data-kpi="job_request"] .cd-kpi-num { color: #6b21a8 !important; }
.cd-kpi-card.cd-kpi-active[data-kpi="job_request"] .cd-kpi-label { color: #7e22ce !important; font-weight: 700 !important; }

.cd-jobs-tabs-card {
    margin-bottom: 12px !important;
}
.cd-jobs-kpi-req-hidden {
    display: none !important;
}
/* Unified Total Jobs Two-Section Separation (Rule 20 Card + Sub-Card + 15.6" fit) */
.cd-jobs-unified-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.cd-jobs-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    padding: 9px 14px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
}
.cd-jobs-section-header.section-requests {
    border-left: 3.5px solid #7c3aed;
    background: linear-gradient(90deg, rgba(124, 58, 237, 0.045) 0%, #ffffff 38%);
}
.cd-jobs-section-header.section-hiring {
    border-left: 3.5px solid #009ef7;
    background: linear-gradient(90deg, rgba(0, 158, 247, 0.045) 0%, #ffffff 38%);
}
.cd-jobs-section-header-left {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.cd-jobs-section-icon {
    width: 26px;
    height: 26px;
    border-radius: 7px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
}
.section-requests .cd-jobs-section-icon {
    background: #f5f3ff;
    color: #7c3aed;
    border: 1px solid #ddd6fe;
}
.section-hiring .cd-jobs-section-icon {
    background: #f0f9ff;
    color: #009ef7;
    border: 1px solid #bae6fd;
}
.cd-jobs-section-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.01em;
}
.cd-jobs-section-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 20px;
    padding: 0 7px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}
.section-requests .cd-jobs-section-badge {
    background: #ede9fe;
    color: #6d28d9;
}
.section-hiring .cd-jobs-section-badge {
    background: #e0f2fe;
    color: #0284c7;
}
.cd-jobs-section-sub {
    font-size: 11.5px;
    color: #64748b;
    font-weight: 500;
}
.cd-jobs-section-divider {
    height: 1px;
    background: linear-gradient(90deg, rgba(226, 232, 240, 0) 0%, #cbd5e1 15%, #cbd5e1 85%, rgba(226, 232, 240, 0) 100%);
    margin: 8px 0 4px;
}
.cd-jobs-hiring-empty {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 28px 20px;
    text-align: center;
}

.cd-jobs-search-filter-card .cd-jobs-search-subcard {
    flex: 1 1 280px !important;
    width: auto !important;
    max-width: none !important;
    height: 38px !important;
}

.cd-jobs-search-filter-card .cd-jobs-search-subcard input {
    height: 36px !important;
    line-height: 36px !important;
}

#allJobList .cd-track-badge.s-sou,
.cd-subcard-pipeline .cd-track-badge.s-sou {
    background: #ecfeff !important;
    color: #0891b2 !important;
    border: 1px solid #a5f3fc !important;
}

#allJobList .cd-track-badge.s-sou:hover,
.cd-subcard-pipeline .cd-track-badge.s-sou:hover {
    background: #cffafe !important;
    color: #0e7490 !important;
    border-color: #67e8f9 !important;
}

.pipeline-legend-card .legend-item:nth-child(3) .legend-dot {
    background: #0891b2 !important;
}

.cd-badge-client-type.type-my-org {
    background: #ecfeff !important;
    color: #0891b2 !important;
    border: 1px solid #a5f3fc !important;
}

.js-open-job-drawer {
    cursor: pointer !important;
    transition: background 0.15s ease-in-out, transform 0.1s ease !important;
}

.js-open-job-drawer:hover {
    background: #f8fafc !important;
}

.js-goto-candidates {
    cursor: pointer !important;
    transition: background 0.15s ease-in-out, transform 0.1s ease !important;
}

.js-goto-candidates:hover {
    background: #f8fafc !important;
}

/* ==========================================================================
   Redesigned Job Request KPI Cards (Rule 20 Modern Card + Sub-Card Standard)
   ========================================================================== */
#allJobList .cd-job-card.cd-job-request-card .cd-job-card-grid {
    grid-template-columns: minmax(0, 2.25fr) minmax(0, 1.35fr) minmax(0, 1.15fr) minmax(0, 1.45fr) minmax(0, 1.25fr) !important;
    gap: 8px !important;
}

@media (max-width: 1440px) {
    #allJobList .cd-job-card.cd-job-request-card .cd-job-card-grid {
        grid-template-columns: minmax(0, 2.15fr) minmax(0, 1.3fr) minmax(0, 1.1fr) minmax(0, 1.45fr) minmax(0, 1.25fr) !important;
        gap: 6px !important;
    }
}

@media (max-width: 1199.98px) {
    #allJobList .cd-job-card.cd-job-request-card .cd-job-card-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 767.98px) {
    #allJobList .cd-job-card.cd-job-request-card .cd-job-card-grid {
        grid-template-columns: minmax(0, 1fr) !important;
    }
}

.cd-badge-client-type.type-request {
    background: #f5f3ff !important;
    color: #7c3aed !important;
    border: 1px solid #ddd6fe !important;
}

#allJobList .cd-job-card.cd-job-request-card .cd-header-badges {
    flex-wrap: wrap !important;
    justify-content: flex-end !important;
    gap: 3px !important;
}

.cd-req-team-disabled-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    margin-top: 3px !important;
    padding: 2px 8px !important;
    border-radius: 5px !important;
    border: 1px dashed #cbd5e1 !important;
    background: #f8fafc !important;
    color: #94a3b8 !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    line-height: 1.35 !important;
    cursor: not-allowed !important;
    width: fit-content !important;
    transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease !important;
}

.cd-req-team-disabled-btn:hover {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
    color: #64748b !important;
}

.cd-subcard-req-expiry {
    background: #fafbfe !important;
}

.cd-req-timeline-rows {
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
}

.cd-req-date-row {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 10.5px !important;
    color: #475569 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.3 !important;
}

.cd-req-date-row i {
    width: 12px !important;
    text-align: center !important;
    font-size: 10px !important;
    flex-shrink: 0 !important;
}

.cd-req-date-row strong {
    color: #0f172a !important;
    font-weight: 600 !important;
}

.cd-req-expiry-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 2px 8px !important;
    border-radius: 6px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    width: fit-content !important;
    max-width: 100% !important;
    white-space: nowrap !important;
    margin-top: 2px !important;
    font-variant-numeric: tabular-nums !important;
}

.cd-req-expiry-pill.pill-req-normal {
    background: #ecfeff !important;
    color: #0e7490 !important;
    border: 1px solid #a5f3fc !important;
}

.cd-req-expiry-pill.pill-req-warning {
    background: #fffbeb !important;
    color: #b45309 !important;
    border: 1px solid #fde68a !important;
}

.cd-req-expiry-pill.pill-req-critical {
    background: #fef2f2 !important;
    color: #dc2626 !important;
    border: 1px solid #fecaca !important;
}

.cd-req-expiry-pill.pill-req-expired {
    background: #f1f5f9 !important;
    color: #64748b !important;
    border: 1px solid #cbd5e1 !important;
}

.cd-subcard-req-action .cd-req-action-stack {
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
    width: 100% !important;
}

.cd-subcard-req-action .cd-req-btn-row {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    width: 100% !important;
}

.cd-req-btn-accept,
.cd-req-btn-reject {
    flex: 1 1 0 !important;
    height: 30px !important;
    border-radius: 7px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
    padding: 0 8px !important;
}

.cd-req-btn-accept {
    background: #009ef7 !important;
    color: #ffffff !important;
    border: 1px solid #009ef7 !important;
    box-shadow: 0 1px 2px rgba(0, 158, 247, 0.2) !important;
}

.cd-req-btn-accept:hover:not(:disabled) {
    background: #0086d6 !important;
    border-color: #0086d6 !important;
    transform: translateY(-0.5px);
}

.cd-req-btn-accept:disabled {
    opacity: 0.55 !important;
    cursor: not-allowed !important;
}

.cd-req-btn-reject {
    background: #fff1f2 !important;
    color: #e11d48 !important;
    border: 1px solid #fecdd3 !important;
}

.cd-req-btn-reject:hover:not(:disabled) {
    background: #ffe4e6 !important;
    color: #be123c !important;
    border-color: #fda4af !important;
}

.cd-req-meta-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 6px !important;
    font-size: 10px !important;
    color: #64748b !important;
}

.cd-req-terms-btn {
    color: #009ef7 !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    cursor: pointer !important;
    white-space: nowrap !important;
}

.cd-req-terms-btn:hover {
    text-decoration: underline !important;
}
</style>
@endpush

@section('content')

@php	
     use App\Helper\GeneralMethod;
     
      $job_status = GeneralMethod::job_status();
      $salaryMode = GeneralMethod::salaryMode();
      $currencySymbole = GeneralMethod::currencySymbole();
      $productAllowCompany = GeneralMethod::companiesType();
      
        $currentKpi = request('kpi', 'all');
        $tabactive = [
         'allRequisition_active'=> ($currentKpi === 'all' || empty($currentKpi)) ? 'tabactive' : '',
         'allRequisition_url'=> 'javascript:void(0);',
         'allRequisition_id'=>'top_card_all_jobs_count',
         'customRequisition_active'=> ($currentKpi === 'internal') ? 'tabactive' : '',
         'customRequisition_url'=> 'javascript:void(0);',
         'customRequisition_id'=>'top_card_custom_requisition_count',
         'myOrganization_active'=> ($currentKpi === 'my_organization') ? 'tabactive' : '',
         'myOrganization_url'=> 'javascript:void(0);',
         'myOrganization_id'=>'top_card_my_org_count',
         'clientRequisition_active'=> ($currentKpi === 'direct') ? 'tabactive' : '',
         'clientRequisition_url'=> 'javascript:void(0);',
         'clientRequisition_id'=>'top_card_client_requisition_count',
         'jobRequest_active'=> ($currentKpi === 'job_request' || $currentKpi === 'request') ? 'tabactive' : '',
         'jobRequest_url'=> 'javascript:void(0);',
         'jobRequest_id'=>'top_card_job_request_count',
         'allRequisition' =>@$dataCnt['allRequisition'],
         'customRequisition' =>@$dataCnt['customRequisition'],
         'myOrganization' =>@$dataCnt['myOrganization'] ?? 0,
         'clientRequisition' =>@$dataCnt['clientRequisition'],
         'jobRequest' =>@$dataCnt['jobRequest'],
         'productAllowCompany' =>@$productAllowCompany,
         'userPermissions' =>$userPermissions,
         'hiringCount'    =>@$dataCnt['hiringCount'],
         'holdCount'      =>@$dataCnt['holdCount'],
         'closedCount'    =>@$dataCnt['closedCount'],
         'removedCount'   =>@$dataCnt['removedCount'],
         'allTabCount'    =>@$dataCnt['allTabCount'],
        ];
        $searchbar = [
         'userPermissions'=>$userPermissions
        ];
     
@endphp     
     
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_job" id="kt_content" style="margin-top: -90px;">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

      <!-- CONTENT -->
        <div class="content">        

          @php
              $allRequisitionTotal = (int) ($tabactive['allRequisition'] ?? 0);
              $directJobsTotal = (int) ($tabactive['clientRequisition'] ?? 0);
              $internalClientJobsTotal = (int) ($tabactive['customRequisition'] ?? 0);
              $myOrgJobsTotal = (int) ($tabactive['myOrganization'] ?? 0);
              $jobRequestsTotal = (int) ($tabactive['jobRequest'] ?? 0);
              $hiringJobsTotal = (int) ($tabactive['hiringCount'] ?? 0);

              $directJobsPercent = $allRequisitionTotal > 0 ? round(($directJobsTotal / $allRequisitionTotal) * 100) : 0;
              $internalJobsPercent = $allRequisitionTotal > 0 ? round(($internalClientJobsTotal / $allRequisitionTotal) * 100) : 0;
              $myOrgJobsPercent = $allRequisitionTotal > 0 ? round(($myOrgJobsTotal / $allRequisitionTotal) * 100) : 0;
          @endphp

          <!-- ══════════════════════════════════════════════════════════════
               TIER 1: TOP HEADER TOOLBAR CARD (Inspired by Clients Tracker)
               ══════════════════════════════════════════════════════════════ -->
          <div class="cd-jobs-header-card">
            <div class="cd-jobs-header-left">
              <div class="cd-jobs-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
              </div>
              <div>
                <div class="cd-jobs-header-title-row">
                  <h1 class="cd-jobs-header-title">Jobs Tracker</h1>
                  <span class="cd-jobs-verified-badge" title="Verified ATS Module">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                      <path d="m9 12 2 2 4-4" stroke-width="2.5"/>
                    </svg>
                  </span>
                  <span class="cd-jobs-header-count-pill" id="all_jobs_count">{{ $tabactive['allTabCount'] ?? $allRequisitionTotal }}</span>
                </div>
                <div class="cd-jobs-header-sub">Manage job requisitions, client mandates, hiring pipelines and recruitment performance.</div>
              </div>
            </div>
            <div class="cd-jobs-header-right">
              @if(in_array("add_jobs", $tabactive['userPermissions'] ?? []))
                <button type="button" class="btn btn-sm btn-primary fw-bold cd-jobs-add-btn" data-toggle="modal" data-target="#add_staffing_partners" id="AddjobSection">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                  </svg>
                  <span>Add Job</span>
                </button>
              @endif
              <button type="button" id="jobSearchBtn" style="display: none;" aria-hidden="true"></button>
            </div>
          </div>

          <!-- ══════════════════════════════════════════════════════════════
               TIER 2: REQUISITION SOURCE EXECUTIVE KPI STRIP (Inspired by Clients Tracker)
               ══════════════════════════════════════════════════════════════ -->
          <div class="cd-jobs-kpi-grid">
            <a href="javascript:void(0);" data-kpi="all" onclick="filterJobsByKpi('all'); return false;" class="cd-kpi-card jh-card jh-card-source-all {{ ($currentKpi === 'all' || empty($currentKpi)) ? 'cd-kpi-active jh-card-active' : '' }}">
              <div class="cd-kpi-top">
                <div class="cd-kpi-icon icon-blue">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                  </svg>
                </div>
                <div class="cd-kpi-info">
                  <div class="cd-kpi-num-row">
                    <span class="cd-kpi-num" id="top_card_all_jobs_count">{{ $allRequisitionTotal }}</span>
                    <span class="cd-kpi-invite-badge" id="badge_jobsAllRequests" style="{{ $jobRequestsTotal > 0 ? '' : 'display:none;' }}" title="{{ $jobRequestsTotal }} Pending Job {{ $jobRequestsTotal === 1 ? 'Request' : 'Requests' }}">
                      <span class="cd-kpi-invite-dot"></span>
                      <span id="span_jobsAllRequestBadge">{{ $jobRequestsTotal }} {{ $jobRequestsTotal === 1 ? 'req' : 'reqs' }}</span>
                    </span>
                  </div>
                  <span class="cd-kpi-label">Total Jobs</span>
                </div>
              </div>
              <div class="cd-kpi-sub sub-blue">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <span id="span_kpi_all_sub">{{ $hiringJobsTotal > 0 ? $hiringJobsTotal . ' actively hiring ' . ($hiringJobsTotal === 1 ? 'role' : 'roles') : '0 actively hiring roles' }}</span>
              </div>
            </a>

            @if($tabactive['productAllowCompany'])
            <a href="javascript:void(0);" data-kpi="direct" onclick="filterJobsByKpi('direct'); return false;" class="cd-kpi-card jh-card jh-card-source-direct {{ ($currentKpi === 'direct') ? 'cd-kpi-active jh-card-active' : '' }}">
              <div class="cd-kpi-top">
                <div class="cd-kpi-icon icon-emerald">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                  </svg>
                </div>
                <div class="cd-kpi-info">
                  <div class="cd-kpi-num-row">
                    <span class="cd-kpi-num" id="top_card_client_requisition_count">{{ $directJobsTotal }}</span>
                  </div>
                  <span class="cd-kpi-label">Direct Client Jobs</span>
                </div>
              </div>
              <div class="cd-kpi-sub sub-green">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span id="span_kpi_direct_sub">{{ $directJobsPercent }}% of total jobs</span>
              </div>
            </a>

            <a href="javascript:void(0);" data-kpi="internal" onclick="filterJobsByKpi('internal'); return false;" class="cd-kpi-card jh-card jh-card-source-internal {{ ($currentKpi === 'internal') ? 'cd-kpi-active jh-card-active' : '' }}">
              <div class="cd-kpi-top">
                <div class="cd-kpi-icon icon-amber">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                    <path d="M9 22v-4h6v4"/>
                    <path d="M8 6h.01M16 6h.01M8 10h.01M16 10h.01M8 14h.01M16 14h.01"/>
                  </svg>
                </div>
                <div class="cd-kpi-info">
                  <div class="cd-kpi-num-row">
                    <span class="cd-kpi-num" id="top_card_custom_requisition_count">{{ $internalClientJobsTotal }}</span>
                  </div>
                  <span class="cd-kpi-label">Internal Client Jobs</span>
                </div>
              </div>
              <div class="cd-kpi-sub sub-orange">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="m18 15-6-6-6 6"/>
                </svg>
                <span id="span_kpi_internal_sub">{{ $internalClientJobsTotal > 0 ? $internalJobsPercent . '% of total · client mandates' : 'No internal client jobs' }}</span>
              </div>
            </a>

            <a href="javascript:void(0);" data-kpi="my_organization" onclick="filterJobsByKpi('my_organization'); return false;" class="cd-kpi-card jh-card jh-card-source-my-org {{ ($currentKpi === 'my_organization') ? 'cd-kpi-active jh-card-active' : '' }}">
              <div class="cd-kpi-top">
                <div class="cd-kpi-icon icon-cyan">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                  </svg>
                </div>
                <div class="cd-kpi-info">
                  <div class="cd-kpi-num-row">
                    <span class="cd-kpi-num" id="top_card_my_org_count">{{ $myOrgJobsTotal }}</span>
                  </div>
                  <span class="cd-kpi-label">My Organization Jobs</span>
                </div>
              </div>
              <div class="cd-kpi-sub sub-cyan">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                </svg>
                <span id="span_kpi_my_org_sub">{{ $myOrgJobsTotal > 0 ? $myOrgJobsPercent . '% of total · in-house roles' : 'No in-house roles yet' }}</span>
              </div>
            </a>

            <a href="javascript:void(0);" data-kpi="job_request" onclick="filterJobsByKpi('job_request'); return false;" class="cd-kpi-card jh-card jh-card-source-request {{ ($currentKpi === 'job_request' || $currentKpi === 'request') ? 'cd-kpi-active jh-card-active' : '' }}">
              <div class="cd-kpi-top">
                <div class="cd-kpi-icon icon-purple">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                  </svg>
                </div>
                <div class="cd-kpi-info">
                  <div class="cd-kpi-num-row">
                    <span class="cd-kpi-num" id="top_card_job_request_count">{{ $jobRequestsTotal }}</span>
                  </div>
                  <span class="cd-kpi-label">Job Requests</span>
                </div>
              </div>
              <div class="cd-kpi-sub sub-purple">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 14 14"/>
                </svg>
                <span id="span_kpi_request_sub">{{ $jobRequestsTotal > 0 ? $jobRequestsTotal . ' pending ' . ($jobRequestsTotal === 1 ? 'invitation' : 'invitations') : "You're all caught up" }}</span>
              </div>
            </a>
            @endif
          </div>

          <!-- ══════════════════════════════════════════════════════════════
               TIER 3: DEDICATED SEARCH BAR & FILTERS CARD (Enterprise Width)
               ══════════════════════════════════════════════════════════════ -->
          <div class="cd-jobs-search-filter-card">
            <div class="cd-jobs-search-subcard">
              <i class="fas fa-search"></i>
              <input type="text" class="inputJobSearch" id="inputJobSearch" name="inputJobSearch" placeholder="Search jobs by title, skills, client, ID..." autocomplete="off">
              <button type="button" class="cd-jobs-search-clear" id="btnJobSearchClear" style="display: none;" title="Clear search">✕</button>
            </div>

            <div class="cd-jobs-filters-group">
              <!-- Job Type Filter Pill -->
              <div class="cd-filter-pill-wrap">
                <span class="cd-filter-label"><i class="fas fa-briefcase me-1 text-muted"></i>Type:</span>
                <select class="form-select form-select-sm cd-filter-select" id="employment_type" onchange="fetchCandidates(1)">
                  <option value="all" selected>All Types</option>
                  <option value="0">Full-time</option>
                  <option value="1">Contract</option>
                  <option value="2">Freelance</option>
                  <option value="3">Part Time</option>
                </select>
              </div>

              <!-- Experience Filter Pill -->
              <div class="cd-filter-pill-wrap">
                <span class="cd-filter-label"><i class="far fa-clock me-1 text-muted"></i>Exp:</span>
                <select class="form-select form-select-sm cd-filter-select" id="experience_filter" onchange="fetchCandidates(1)">
                  <option value="all" selected>All Exp.</option>
                  <option value="0-1">0-1 yr</option>
                  <option value="1-3">1-3 yrs</option>
                  <option value="3-5">3-5 yrs</option>
                  <option value="5+">5+ yrs</option>
                </select>
              </div>

              <!-- Sort By Filter Pill -->
              <div class="cd-filter-pill-wrap">
                <span class="cd-filter-label"><i class="fas fa-sort-amount-down me-1 text-muted"></i>Sort:</span>
                <select class="form-select form-select-sm cd-filter-select" id="hiringtype" onchange="fetchCandidates(1)">
                  <option value="1" selected>Newest First</option>
                  <option value="2">Oldest First</option>
                  <option value="3">Low-High (Salary)</option>
                  <option value="4">High-Low (Salary)</option>
                  <option value="5">A-Z (Company)</option>
                  <option value="6">Z-A (Company)</option>
                  <option value="7" class="js-req-only-sort" style="display:none;">Expiring Soonest</option>
                </select>
              </div>
            </div>
          </div>

          <!-- ══════════════════════════════════════════════════════════════
               TIER 4: DEDICATED STATUS SUB-TABS CARD (Pure White #ffffff)
               ══════════════════════════════════════════════════════════════ -->
          <div class="cd-jobs-tabs-card">
            <div class="cd-jobs-tabs-group">
              <button type="button" class="cd-jobs-tab-btn jh-tab jh-tab-active active" data-status="all" onclick="filterJobsByStatus('all'); return false;">
                <span>All Statuses</span>
                <span class="cd-jobs-tab-badge" id="filterCountAll">{{ $tabactive['allTabCount'] ?? $tabactive['allRequisition'] }}</span>
              </button>
              <button type="button" class="cd-jobs-tab-btn jh-tab" data-status="active" onclick="filterJobsByStatus('active'); return false;">
                <span>Hiring</span>
                <span class="cd-jobs-tab-badge badge-hiring" id="filterCountHiring">{{ $tabactive['hiringCount'] ?? 0 }}</span>
              </button>
              <button type="button" class="cd-jobs-tab-btn jh-tab" data-status="hold" onclick="filterJobsByStatus('hold'); return false;">
                <span>On Hold</span>
                <span class="cd-jobs-tab-badge badge-hold" id="filterCountHold">{{ $tabactive['holdCount'] ?? 0 }}</span>
              </button>
              <button type="button" class="cd-jobs-tab-btn jh-tab" data-status="inactive" onclick="filterJobsByStatus('inactive'); return false;">
                <span>Closed</span>
                <span class="cd-jobs-tab-badge badge-closed" id="filterCountClosed">{{ $tabactive['closedCount'] ?? 0 }}</span>
              </button>
              <button type="button" class="cd-jobs-tab-btn jh-tab" data-status="removed" onclick="filterJobsByStatus('removed'); return false;">
                <span>Removed</span>
                <span class="cd-jobs-tab-badge badge-inactive" id="filterCountRemoved">{{ $tabactive['removedCount'] ?? 0 }}</span>
              </button>
            </div>
            <input type="hidden" id="status" value="all">
          </div>

          <!-- ══════════════════════════════════════════════════════════════
               TIER 5: PRIMARY ENTITY CARD STREAM (Modern Card + Sub-Cards)
               ══════════════════════════════════════════════════════════════ -->
          <div class="cd-jobs-stream-wrapper">
            <div class="loadingMessage" style="display: none; position: absolute; z-index: 10; background: rgba(255,255,255,0.85); padding: 8px 16px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-size: 12px; font-weight: 600; color: #009ef7;">
              <i class="fas fa-spinner fa-spin me-2"></i>Processing...
            </div>
            <div id="allJobList" class="cd-jobs-stream"></div>
            <div id="pagination" class="mt-4"></div>
            <div class="pipeline-legend-card mt-4">
              @include('sections.service_company.pipeline')
            </div>
          </div>
        </div>
        <!-- /content -->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
<div class="modal fade bs-modal-sm in" id="scheduleDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingStatus"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn default" data-dismiss="modal">Close</button>
        <button type="button" class="btn blue">Save changes</button>
      </div>
    </div>
  </div>
</div>




<div class="modal fade bs-modal-lg in" id="JobDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="JobDetailModalStatusData"></span> </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-md in" id="scheduleDetailModal_vis" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_vis"></span> </div>
    </div>
  </div>
</div>

<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none bg-custom-bar rounded-0 w-100">
   {{-- header removed --}}
    <div class="card-body bg-custom-bar position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>
<div id="kt_activitiesClientjob_toggle"></div>
<div id="kt_activitiesClientjob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClientjob_toggle" data-kt-drawer-close="#kt_activitiesClientjob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Direct Client Requisition</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesClientjob_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesclientjob_body"> </div>
  </div>
</div>
<div id="kt_activitiesRecruiter_toggle"></div>
<div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1320px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle" data-kt-drawer-close="#kt_activitiesRecruiter_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <div class=" d-flex align-items-center">
          <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" class="l">
            <div class="symbol-label border-radius-5" id="light_recruiter_caption">&nbsp;</div>
            </a> </div>
          <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_recruiter_name">&nbsp;</a> </div>
        </div>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesRecruiter_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesRecruiter_body"> &nbsp; </div>
  </div>
</div>


<div id="kt_activitiesTEAM_toggle"></div>
<div id="kt_activitiesTEAM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesTEAM_toggle" data-kt-drawer-close="#kt_activitiesTEAM_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
            style="float:left"> 
            <a href="javascript:void(0);" class="l">
                <div class="symbol-label border-radius-5" id="light_logo_company">&nbsp;</div>
            </a> 
        </div>
        <h3 class="card-title fw-bolder text-dark">Team Members</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesTEAM_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesTEAM_body"> </div>
  </div>
</div>

<div id="kt_activitiesCHM_toggle"></div>
<div id="kt_activitiesCHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCHM_toggle" data-kt-drawer-close="#kt_activitiesCHM_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
            style="float:left"> 
            <a href="javascript:void(0);" class="l">
                <div class="symbol-label border-radius-5" id="light_client_caption">&nbsp;</div>
            </a> 
        </div>

        <h3 class="card-title fw-bolder text-dark" id="company_or_client">Direct Client Contact Person</h3>

        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesCHM_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesCHM_body"> </div>
  </div>
</div>
<!--end::Staffing Partners sidebar-->
<!-- Job Status drawer — replaces the old #status_stage modal. Slides in from the right,
     capped to 80% of the viewport width on laptop/desktop (per design spec) so the top
     menu bar stays visible/reachable beside it; expands to full width on small screens
     where 80% would leave too little room for the two-column layout. Same trigger class
     (.status_stage) and same AJAX endpoints as before — only the chrome changed. -->
<style>
  #status_drawer .sd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 28px; border-bottom: 1px solid #eef0f4; flex-wrap: wrap; }
  #status_drawer .sd-topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
  #status_drawer .sd-topbar-ico { width: 42px; height: 42px; border-radius: 10px; background: #eef4ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
  #status_drawer .sd-topbar-title { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #181c32; line-height: 1.3; }
  #status_drawer .sd-topbar-sub { font-family: 'Poppins', sans-serif; font-size: 11.5px; font-weight: 400; color: #7e8299; margin-top: 1px; }
  #status_drawer .sd-topbar-right { display: flex; align-items: center; gap: 10px; }
  #status_drawer .sd-btn-cancel { border: 1px solid #e4e6ef; background: #fff; color: #3f4254; border-radius: 8px; padding: 9px 16px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; }
  #status_drawer .sd-btn-cancel:hover { background: #f8f9fb; }
  #status_drawer .sd-btn-confirm { border: none; background: #3e97ff; color: #fff; border-radius: 8px; padding: 9px 18px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: background .15s; }
  #status_drawer .sd-btn-confirm:hover { background: #2f7fe0; }
  #status_drawer .sd-btn-confirm:disabled { background: #c2c9d6; cursor: not-allowed; }
  #status_drawer .sd-close { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #7e8299; cursor: pointer; flex-shrink: 0; }
  #status_drawer .sd-close:hover { background: #f8f9fb; color: #f64e60; }
</style>
<div id="status_drawer_toggle" style="display:none;"></div>
<div id="status_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="job-status"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'100%', 'sm': '92%', 'lg': '80%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#status_drawer_toggle" data-kt-drawer-close="#status_drawer_close">
  <div class="d-flex flex-column h-100 w-100">
    <div class="sd-topbar">
      <div class="sd-topbar-left">
        <span class="sd-topbar-ico"><i class="fas fa-briefcase"></i></span>
        <div>
          <div class="sd-topbar-title">Job Status</div>
          <div class="sd-topbar-sub">Update the job status and manage its visibility across the platform.</div>
        </div>
      </div>
      <div class="sd-topbar-right">
        <button type="button" class="sd-btn-cancel" id="status_sub_stage_close">Cancel</button>
        <button type="button" class="sd-btn-confirm" id="status_confirm_btn" disabled onclick="newUpdateJobstatus()">
          <span class="indicator-label changetextupdate">Update Status</span> <i class="fas fa-arrow-right"></i>
        </button>
        <span class="sd-close" id="status_drawer_close"><i class="fa fa-xmark"></i></span>
      </div>
    </div>
    <div class="status_body_content flex-grow-1" id="status_body_content" style="overflow-y:auto;"></div>
  </div>
</div>





@endsection
@push('footer-script')

<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>

<!--<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>-->


<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>


<script>

@if(session('packageStatus'))
 Swal.fire("Failed", "{{ session('packageStatus') }}" , "warning");	
@endif


   $(document).ready(function() {	


    $('body').on('click', '#AddjobSection', function(){
			
      var url 				=	"{{ route('service_company.auth-custom-package.validatePackage') }}";
      var token 				=	"{{ csrf_token() }}";							
      var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
      var eventId 			=	2; 
      $.ajax({
            type		: 'POST',
            url			: url,
            beforeSend	: function(){ },
            data		: {'_token': token, 'event':'CVACCESS','eventId':eventId},
            success		: function (response){
                                  if (response.status == "success"){  																					  																												                                                                                            	var url   			=	"{{url('service_company/jobs/create')}}";
                                        window.location 	=	url;
                                     }else if (response.status == "info"){   
                                      sweetAlert("Fail!", response.message, "error");	
                                     }										 
                            }
          });
  
});



   });
</script>



        <!--<link href="https://preview.keenthemes.com/html/keen/docs/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />-->
        <!--<script src="https://preview.keenthemes.com/html/keen/docs/assets/plugins/global/plugins.bundle.js"></script>-->
        <script>
            var input1 = document.querySelector("#kt_tagify_1");
            // var input2 = document.querySelector("#kt_tagify_2");
            
            new Tagify(input1);
            // new Tagify(input2);
        </script>

<script>
       // Phase 2: Client Caching & IndexedDB Integration (Rule 6 Multi-Tenant Isolation)
    const JeroJobsLocalStore = (function() {
        const DB_NAME = 'jero_service_jobs_db';
        const DB_VERSION = 1;
        const CURRENT_TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
        const CURRENT_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
        const JOBS_CACHE_TTL_MS = 15 * 60 * 1000; // 15 minutes TTL

        let dbPromise = null;

        function getDB() {
            if (!window.indexedDB) return Promise.resolve(null);
            if (dbPromise) return dbPromise;

            dbPromise = new Promise((resolve) => {
                try {
                    const req = window.indexedDB.open(DB_NAME, DB_VERSION);
                    req.onupgradeneeded = function(e) {
                        const db = e.target.result;
                        if (!db.objectStoreNames.contains('jobs_cache')) {
                            db.createObjectStore('jobs_cache', { keyPath: 'cache_key' });
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
                                    // Tenant / User mismatch: purge immediately to prevent cross-tenant data leaks
                                    const wipeTx = db.transaction(['jobs_cache', 'meta'], 'readwrite');
                                    wipeTx.objectStore('jobs_cache').clear();
                                    wipeTx.objectStore('meta').put({ id: 'current_context', tenant_id: CURRENT_TENANT_ID, user_id: CURRENT_USER_ID, updated_at: Date.now() });
                                } else if (!meta) {
                                    const setTx = db.transaction(['meta'], 'readwrite');
                                    setTx.objectStore('meta').put({ id: 'current_context', tenant_id: CURRENT_TENANT_ID, user_id: CURRENT_USER_ID, updated_at: Date.now() });
                                }
                            };
                        } catch (err) {
                            console.warn('JeroJobsLocalStore tenancy check error:', err);
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
                        const tx = db.transaction(['jobs_cache'], 'readonly');
                        const store = tx.objectStore('jobs_cache');
                        const req = store.get(key);
                        req.onsuccess = function() {
                            const res = req.result;
                            if (res && (Date.now() - res.timestamp < JOBS_CACHE_TTL_MS)) {
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
                    const tx = db.transaction(['jobs_cache'], 'readwrite');
                    const store = tx.objectStore('jobs_cache');
                    store.put({
                        cache_key: key,
                        payload: payload,
                        timestamp: Date.now()
                    });
                } catch (e) {
                    // Non-fatal if storage quota exceeded
                }
            },
            purge: async function() {
                const db = await getDB();
                if (!db) return;
                try {
                    const tx = db.transaction(['jobs_cache'], 'readwrite');
                    tx.objectStore('jobs_cache').clear();
                } catch (e) {
                    // Non-fatal
                }
            }
        };
    })();
    window.JeroJobsLocalStore = JeroJobsLocalStore;
// In-memory cache for loaded job drawer HTML to eliminate duplicate network fetches
    window.jobDrawerCache = window.jobDrawerCache || {};
    window.lastAuthoritativeSearch = '';

    function executeJobSearch(page = 1) {
        localStorage.setItem('currentPageJobs', page);
        var currentSearch = ($('.inputJobSearch').val() || $('#inputSearch').val() || '').trim();
        window.lastAuthoritativeSearch = currentSearch;
        fetchCandidates(page);
    }

    // Debounced search input handler: filters loaded jobs in browser RAM at 250ms with 0 server requests
    var jobFilterDebounceTimer = null;
    $(document).on('input', '.inputJobSearch, #inputSearch', function() {
        var val = $(this).val();
        $('.inputJobSearch, #inputSearch').not(this).val(val);
        // Instant visual feedback for currently visible items in RAM
        if (window.jobsState && window.jobsState.all && window.jobsState.all.length > 0) {
            filtersdata();
        }

        clearTimeout(jobFilterDebounceTimer);
        jobFilterDebounceTimer = setTimeout(function() {
            var trimmed = val.trim();
            if (trimmed !== window.lastAuthoritativeSearch) {
                executeJobSearch(1);
            }
        }, 350);
    });

    // Enter key handler for authoritative search across full dataset
    $(document).on('keydown', '.inputJobSearch, #inputSearch', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            clearTimeout(jobFilterDebounceTimer);
            executeJobSearch(1);
        }
    });

    // Single click handler for Search button: authoritative search
    $(document).on('click', '#jobSearchBtn', function(e) {
        e.preventDefault();
        clearTimeout(jobFilterDebounceTimer);
        executeJobSearch(1);
    });

    $(document).on('change', '#status,#hiringtype,#employment_type,#experience_filter', function() {
        if (this.id === 'status') {
            // Sync the tab row to match the selected status
            var val = $(this).val();
            var tabStatus = (val === 'inactive') ? 'inactive' : val;
            $('.jh-tab').removeClass('jh-tab-active');
            $('.jh-tab[data-status="' + tabStatus + '"]').addClass('jh-tab-active');
        }
        syncJobCustomSelect(this.id);
        localStorage.setItem('currentPageJobs', 1);
        fetchCandidates(1);
    });

    window.currentKpiFilter = (new URLSearchParams(window.location.search)).get('kpi') || 'all';

    function syncTopKpiCounts(dataCnt) {
        if (!dataCnt) return;
        var total = Number(dataCnt.allRequisition !== undefined ? dataCnt.allRequisition : (dataCnt.allTabCount || 0));
        var direct = Number(dataCnt.clientRequisition || 0);
        var internal = Number(dataCnt.customRequisition || 0);
        var myOrg = Number(dataCnt.myOrganization || 0);
        var jobReq = Number(dataCnt.jobRequest || 0);
        var hiring = Number(dataCnt.hiringCount || 0);

        $('#top_card_all_jobs_count').text(total);
        $('#top_card_client_requisition_count').text(direct);
        $('#top_card_custom_requisition_count').text(internal);
        $('#top_card_my_org_count').text(myOrg);
        $('#top_card_job_request_count').text(jobReq);

        // Sync Client-Tracker-inspired executive micro-telemetry subtitles & request alert badge
        var directPct = total > 0 ? Math.round((direct / total) * 100) : 0;
        var internalPct = total > 0 ? Math.round((internal / total) * 100) : 0;
        var myOrgPct = total > 0 ? Math.round((myOrg / total) * 100) : 0;

        $('#span_kpi_all_sub').text(hiring > 0 ? (hiring + ' actively hiring ' + (hiring === 1 ? 'role' : 'roles')) : '0 actively hiring roles');
        $('#span_kpi_direct_sub').text(directPct + '% of total jobs');
        $('#span_kpi_internal_sub').text(internal > 0 ? (internalPct + '% of total · client mandates') : 'No internal client jobs');
        $('#span_kpi_my_org_sub').text(myOrg > 0 ? (myOrgPct + '% of total · in-house roles') : 'No in-house roles yet');
        $('#span_kpi_request_sub').text(jobReq > 0 ? (jobReq + ' pending ' + (jobReq === 1 ? 'invitation' : 'invitations')) : "You're all caught up");

        if (jobReq > 0) {
            $('#span_jobsAllRequestBadge').text(jobReq + ' ' + (jobReq === 1 ? 'req' : 'reqs'));
            $('#badge_jobsAllRequests').attr('title', jobReq + ' Pending Job ' + (jobReq === 1 ? 'Request' : 'Requests')).show();
        } else {
            $('#badge_jobsAllRequests').hide();
        }
    }

    /**
     * Immediately synchronize status tab badges and header pill counter from controller dataCnt payload
     * (prevents stale counts during asynchronous refresh after Accept / Reject).
     */
    function syncStatusTabCountsFromDataCnt(dataCnt) {
        if (!dataCnt) return;
        var kpi = window.currentKpiFilter || 'all';
        if (kpi === 'job_request' || kpi === 'request') {
            var reqTotal = Number(dataCnt.jobRequest || 0);
            $('#all_jobs_count').text(reqTotal);
            return;
        }
        if (kpi === 'all' || !kpi) {
            var allCnt = Number(dataCnt.allRequisition !== undefined ? dataCnt.allRequisition : (dataCnt.allTabCount || 0));
            var hireCnt = Number(dataCnt.hiringCount || 0);
            var holdCnt = Number(dataCnt.holdCount || 0);
            var closeCnt = Number(dataCnt.closedCount || 0);
            var remCnt = Number(dataCnt.removedCount || 0);
            $('#filterCountAll, .jh-tab[data-status="all"] .jh-badge, .cd-jobs-tab-btn[data-status="all"] .cd-jobs-tab-badge').text(allCnt);
            $('#filterCountHiring, .jh-tab[data-status="active"] .jh-badge, .cd-jobs-tab-btn[data-status="active"] .cd-jobs-tab-badge').text(hireCnt);
            $('#filterCountHold, .jh-tab[data-status="hold"] .jh-badge, .cd-jobs-tab-btn[data-status="hold"] .cd-jobs-tab-badge').text(holdCnt);
            $('#filterCountClosed, .jh-tab[data-status="inactive"] .jh-badge, .cd-jobs-tab-btn[data-status="inactive"] .cd-jobs-tab-badge').text(closeCnt);
            $('#filterCountRemoved, .jh-tab[data-status="removed"] .jh-badge, .cd-jobs-tab-btn[data-status="removed"] .cd-jobs-tab-badge').text(remCnt);

            var activeStatus = ($('#status').val() || 'all').toLowerCase();
            var statusHeaderCount = allCnt;
            if (activeStatus === 'active') statusHeaderCount = hireCnt;
            else if (activeStatus === 'hold') statusHeaderCount = holdCnt;
            else if (activeStatus === 'inactive') statusHeaderCount = closeCnt;
            else if (activeStatus === 'removed') statusHeaderCount = remCnt;
            $('#all_jobs_count').text(statusHeaderCount);
        }
    }

    function updateKpiViewToggle(kpiType) {
        var isReq = (kpiType === 'job_request' || kpiType === 'request');
        var $reqSortOpt = $('#hiringtype option.js-req-only-sort');
        if (isReq) {
            $('.cd-jobs-tabs-card, .jh-tabs-card, .pipeline-legend-card').addClass('cd-jobs-kpi-req-hidden').hide();
            $('.cd-jobs-search-filter-card').removeClass('cd-jobs-kpi-req-hidden').show();
            $reqSortOpt.prop('hidden', false).prop('disabled', false).show();
        } else {
            $('.cd-jobs-tabs-card, .cd-jobs-search-filter-card, .jh-filter-bar, .jh-tabs-card, .pipeline-legend-card').removeClass('cd-jobs-kpi-req-hidden').show();
            $reqSortOpt.prop('hidden', true).prop('disabled', true).hide();
            if ($('#hiringtype').val() === '7') {
                $('#hiringtype').val('1');
            }
        }
    }

    function filterJobsByKpi(kpiType) {
        window.currentKpiFilter = kpiType;
        $('.jh-card, .cd-kpi-card').removeClass('jh-card-active cd-kpi-active');
        $(`.jh-card[data-kpi="${kpiType}"], .cd-kpi-card[data-kpi="${kpiType}"]`).addClass('jh-card-active cd-kpi-active');

        $('.jh-radio-input').prop('checked', false);
        $(`.jh-card[data-kpi="${kpiType}"] .jh-radio-input`).prop('checked', true);

        // Reflect current KPI in address bar without full page reload
        try {
            var currentUrl = new URL(window.location.href);
            if (kpiType && kpiType !== 'all') {
                currentUrl.searchParams.set('kpi', kpiType);
            } else {
                currentUrl.searchParams.delete('kpi');
            }
            window.history.replaceState({}, '', currentUrl.pathname + currentUrl.search + currentUrl.hash);
        } catch (e) {}

        // Sync sidebar active state between CRM -> Job Request and Hiring -> Job Tracker
        if (kpiType === 'job_request' || kpiType === 'request') {
            $('a[href*="kpi=job_request"], a[href*="job_request"]').addClass('active');
            $('a[href="{{ route('service_company.jobs.index') }}"]').not('[href*="kpi="]').removeClass('active');
        } else {
            $('a[href*="kpi=job_request"], a[href*="job_request"]').removeClass('active');
            $('a[href="{{ route('service_company.jobs.index') }}"]').not('[href*="kpi="]').addClass('active');
        }

        updateKpiViewToggle(kpiType);

        localStorage.setItem('currentPageJobs', 1);
        fetchCandidates(1);
    }

    // Dynamic Search Clear Handler (300ms Debounce Integration)
    $(document).on('click', '#btnJobSearchClear', function() {
        $('.inputJobSearch, #inputJobSearch').val('');
        $(this).hide();
        executeJobSearch(1);
    });

    $(document).on('input', '.inputJobSearch, #inputJobSearch', function() {
        var sVal = $(this).val();
        if (sVal && sVal.trim().length > 0) {
            $('#btnJobSearchClear').show();
        } else {
            $('#btnJobSearchClear').hide();
        }
    });

    // Called from the tab row — filters table and marks tab active
    function filterJobsByStatus(status) {
        $('#status').val(status);
        syncJobCustomSelect('status');
        $('.jh-tab, .cd-jobs-tab-btn').removeClass('jh-tab-active active');
        $('.jh-tab[data-status="' + status + '"], .cd-jobs-tab-btn[data-status="' + status + '"]').addClass('jh-tab-active active');
        localStorage.setItem('currentPageJobs', 1);
        fetchCandidates(1);
    }

    function syncJobCustomSelect(selectId) {
        var $select = $('#' + selectId);
        var value = $select.val();
        var label = $select.find('option:selected').text();
        var $wrap = $('.jh-select-trigger[data-select-target="' + selectId + '"]').closest('.jh-custom-select');
        $wrap.find('.jh-select-value').text(label);
        $wrap.find('.jh-select-option').removeClass('is-selected');
        $wrap.find('.jh-select-option[data-value="' + value + '"]').addClass('is-selected');
    }

    $(document).on('click', '.jh-select-trigger', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $wrap = $(this).closest('.jh-custom-select');
        $('.jh-custom-select').not($wrap).removeClass('is-open').find('.jh-select-trigger').attr('aria-expanded', 'false');
        $wrap.toggleClass('is-open');
        $(this).attr('aria-expanded', $wrap.hasClass('is-open') ? 'true' : 'false');
    });

    $(document).on('click', '.jh-select-option', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $option = $(this);
        var $wrap = $option.closest('.jh-custom-select');
        var selectId = $wrap.find('.jh-select-trigger').data('select-target');
        $('#' + selectId).val($option.data('value'));
        syncJobCustomSelect(selectId);
        $wrap.removeClass('is-open').find('.jh-select-trigger').attr('aria-expanded', 'false');
        if (selectId === 'status') {
            var val = $option.data('value');
            $('.jh-tab').removeClass('jh-tab-active');
            $('.jh-tab[data-status="' + val + '"]').addClass('jh-tab-active');
        }
        localStorage.setItem('currentPageJobs', 1);
        filtersdata();
    });

    $(document).on('click', function(e) {
        $('.jh-custom-select').removeClass('is-open').find('.jh-select-trigger').attr('aria-expanded', 'false');
        if (!$(e.target).closest('.loc-row, .loc-popover-card').length) {
            $('.loc-more-badge-wrap').removeClass('is-clicked-active');
        }
    });

    // Work location popover click toggle (support click in addition to mouse hover)
    $(document).on('click', '.loc-row', function(e) {
        var $badgeWrap = $(this).find('.loc-more-badge-wrap');
        if ($badgeWrap.length) {
            e.stopPropagation();
            var wasActive = $badgeWrap.hasClass('is-clicked-active');
            $('.loc-more-badge-wrap').removeClass('is-clicked-active');
            if (!wasActive) {
                $badgeWrap.addClass('is-clicked-active');
            }
        }
    });

    // Cross-tab / window cache invalidation for Job Drawer
    window.purgeJobDrawerCache = function(jobId) {
        window.jobDrawerCache = window.jobDrawerCache || {};
        if (jobId) {
            delete window.jobDrawerCache['job_' + jobId + '_regular'];
            delete window.jobDrawerCache['job_' + jobId + '_client'];
        } else {
            window.jobDrawerCache = {};
        }
    };

    $(window).on('focus storage', function(e) {
        if (e.type === 'storage' && e.originalEvent && e.originalEvent.key === 'job_drawer_bust') {
            window.jobDrawerCache = {};
        }
    });

    $(document).on('change', '.jh-select-native', function() {
        syncJobCustomSelect(this.id);
    });

    syncJobCustomSelect('status');
    syncJobCustomSelect('employment_type');
    syncJobCustomSelect('experience_filter');
    syncJobCustomSelect('hiringtype');
    
   
   
    $(document).on('click', '.show-detail', function ()
   	{
      // alert("kkss");
   		$('#kt_activitiesJob_toggle').trigger('click');		
      
      // localStorage is already kept current by fetchCandidates; no DOM read needed
      var currentPageJobs = parseInt(localStorage.getItem('currentPageJobs')) || 1;
      localStorage.setItem('currentPageJobs', currentPageJobs);

   		var id = $(this).data('row-id');
        var cacheKey = 'job_' + id + '_regular';
        if (localStorage.getItem('job_drawer_bust_id_' + id) || localStorage.getItem('job_drawer_bust')) {
            if (window.jobDrawerCache) {
                delete window.jobDrawerCache[cacheKey];
                delete window.jobDrawerCache['job_' + id + '_client'];
            }
            localStorage.removeItem('job_drawer_bust_id_' + id);
        }
        if (window.jobDrawerCache && window.jobDrawerCache[cacheKey]) {
            $('#kt_activitiesJob_body').html(window.jobDrawerCache[cacheKey]);
            return;
        }
   		var url = "{{ route('service_company.jobs.viewmore',':id') }}?viewmore=viewmore";
   		url = url.replace(':id', id);   		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
									window.jobDrawerCache[cacheKey] = response.view;
									$('#kt_activitiesJob_body').html(response.view);
   						},
   						error		: function (xhr) {
   							var msg = (xhr.status === 0 || !navigator.onLine)
   								? 'You are offline. Please check your connection.'
   								: 'Could not load job details (HTTP ' + xhr.status + '). Please try again.';
   							$('#kt_activitiesJob_body').html('<div style="padding:24px;text-align:center;font-family:Poppins,sans-serif;font-size:13px;color:#f64e60;">' + msg + '</div>');
   						}
   	  			});
   	});


  $(document).on('click', '#status_sub_stage_close', function () { $('#status_drawer_close').trigger('click'); });

  $(document).on('click', '.status_stage', function () {
		// Direct Client jobs (recommended to this company, not owned by it)
		// stay clickable, but only the owning company may actually change
		// the status — enforced server-side too, in
		// ServiceCompanyJobsController::newUpdateJobstatus(). Notify instead
		// of opening a drawer whose submit would just get rejected.
		if ($(this).data('direct-client') == 1) {
			Swal.fire({
				icon: 'warning',
				title: 'Restricted',
				text: 'Direct Client Job Status cannot be changed by Agencies'
			});
			return false;
		}
		$('#status_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');
		$('#status_confirm_btn').prop('disabled', true).css('background', '');
		$('.changetextupdate').text('Update Status');
		$('#status_drawer_toggle').trigger('click');
		var id        	=	$(this).data('row-id');
		var url       	=	"{{ route('service_company.jobs.editJobStatus',':id') }}";
		url           	=	url.replace(':id', id);
		$.ajax({
					type  	: 'GET',
					url   	: url,
					success : function (response) {
						if (response.status == "success") {
							$('#status_body_content').html(response.view);
						} else {
							$('#status_body_content').html('<div class="text-center py-20 text-danger">Could not load the job status details. Please close and try again.</div>');
						}
					},
					error : function () {
						$('#status_body_content').html('<div class="text-center py-20 text-danger">Something went wrong loading this job\'s status. Please close and try again.</div>');
					}
		});
});
	
$(document).on('click', '.client-show-detail', function ()
{
  $('#kt_activitiesJob_toggle').trigger('click');		
   		var id = $(this).data('row-id');
        var cacheKey = 'job_' + id + '_client';
        if (localStorage.getItem('job_drawer_bust_id_' + id) || localStorage.getItem('job_drawer_bust')) {
            if (window.jobDrawerCache) {
                delete window.jobDrawerCache[cacheKey];
                delete window.jobDrawerCache['job_' + id + '_regular'];
            }
            localStorage.removeItem('job_drawer_bust_id_' + id);
        }
        if (window.jobDrawerCache && window.jobDrawerCache[cacheKey]) {
            $('#kt_activitiesJob_body').html(window.jobDrawerCache[cacheKey]);
            return;
        }
   		var url = "{{ route('service_company.jobs.viewmore',':id') }}?viewmore=clientviewmore";
   		url = url.replace(':id', id);   		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
									window.jobDrawerCache[cacheKey] = response.view;
									$('#kt_activitiesJob_body').html(response.view);
   						},
   						error		: function (xhr) {
   							var msg = (xhr.status === 0 || !navigator.onLine)
   								? 'You are offline. Please check your connection.'
   								: 'Could not load job details (HTTP ' + xhr.status + '). Please try again.';
   							$('#kt_activitiesJob_body').html('<div style="padding:24px;text-align:center;font-family:Poppins,sans-serif;font-size:13px;color:#f64e60;">' + msg + '</div>');
   						}
   	  			});

                // $('#kt_activitiesClientjob_toggle').trigger('click');	
                // var id    		=	$(this).data('row-id');
                // var url   		=	"{{ route('service_company.jobs.clientviewmore',':id') }}"; //clientviewmore
                // url     		=	url.replace(':id', id);
                // var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
                // $.ajax({
                // 				type  		: 'GET',
                // 				url   		: url,
                // 				 beforeSend	: function() {
                // 							 $('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');			
                // 				},
                // 				success 	: function (response) {
                // 						$('#kt_activitiesclientjob_body').html(response.view);		
                // 				}
                // 		});
}); 

// Point 6: Click anywhere on Job Details or Compensation & Exp sub-cards -> open Job Drawer
$(document).on('click', '.js-open-job-drawer', function(e) {
    if ($(e.target).closest('.status_stage, .loc-more-badge-wrap, .loc-popover-card, .loc-row, a, button').length) {
        return;
    }
    e.preventDefault();
    e.stopPropagation();

    var jobId = $(this).data('row-id');
    var isDirect = $(this).data('direct') == 1;

    if (isDirect) {
        var trigger = $('<span class="client-show-detail" data-row-id="' + jobId + '"></span>');
        $('body').append(trigger);
        trigger.trigger('click');
        trigger.remove();
    } else {
        var trigger = $('<span class="show-detail" data-row-id="' + jobId + '"></span>');
        $('body').append(trigger);
        trigger.trigger('click');
        trigger.remove();
    }
});

// Point 6: Click anywhere on Hiring Pipeline sub-card -> move to Candidate Listing
$(document).on('click', '.js-goto-candidates', function(e) {
    if ($(e.target).closest('.cd-track-badge, a, button').length) {
        return;
    }
    e.preventDefault();
    e.stopPropagation();

    var url = $(this).data('apptracker-url');
    if (url) {
        window.location.href = url;
    }
});
   
      var _isUpdatingStatusLock = false;
   function updateJobStatus(id, statusName)
   {
     if (_isUpdatingStatusLock) return false;
     _isUpdatingStatusLock = true;

     var url = "{{ route('service_company.jobs.updatestatus',':id') }}";
     url = url.replace(':id', id);
     var token = "{{ csrf_token() }}";
     var mutationId = 'mut_st_' + id + '_' + Date.now();

     $.easyAjax({
         type: 'POST',
         url: url,
         data: {
             '_token': token, 
             '_method': 'POST', 
             'id': id, 
             'statusName': statusName,
             'client_mutation_id': mutationId
         },
         success: function (response){
             _isUpdatingStatusLock = false;
             window.jobDrawerCache = {};
             if (window.JeroJobsLocalStore) { JeroJobsLocalStore.purge(); }
             fetchCandidates();
         },
         error: function (xhr) {
             _isUpdatingStatusLock = false;
             if (typeof showToastr === 'function') {
                 showToastr('error', 'Status update could not be completed. Please try again.', 'Action Failed');
             }
         }
     });
   }
     
</script>
<script>

$(document).ready(function(){

  $(document).on('click', '.hmlisting', function(){
			
														$('#kt_activitiesRecruiter_toggle').trigger('click');  
														var id    		= $(this).data('row-id');
														var token 		=	"{{ csrf_token() }}";
										
											
														var url   		=	"{{ route('service_company.jobs.hmlisting',':id') }}"; 
														url     		=	url.replace(':id', id);  		
														var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
														$.ajax({
																	type  		: 'GET',
																	url   		: url,
																	data		: {'_token': token, 'id' : id},
																	 beforeSend	: function() {
																				 $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
																	},
																	success 	: function (response) {
																				$('#kt_activitiesRecruiter_body').html(response.view);
																	}
															}); 
												
		
		});

  // Intent: load the existing agencies drawer for dynamically-rendered Add Agency controls.
  $(document).off('click.spartnerListing', '.spartner-listing').on('click.spartnerListing', '.spartner-listing', function(e){
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) {
            e.stopImmediatePropagation();
        }

        var $btn = $(this);
        if ($btn.data('in-flight')) return false;

        $('#kt_activitiesRecruiter_toggle').trigger('click');
        var id = $btn.data('row-id');

        if (window.currentActiveDrawerJobId !== id) {
            window.currentActiveDrawerJobId = id;
            if (window.selectedAgencyIds) {
                window.selectedAgencyIds.clear();
            }
        }

        var url = "{{ route('service_company.jobs.spartnerlisting',':id') }}".replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        var loaderHtml = '<div style="padding:40px;text-align:center;font-family:Poppins,sans-serif;"><img src="'+baseurl+'" height="35px" style="margin-bottom:10px;"><div style="font-size:13px;color:#5e6278;font-weight:500;">Loading agencies...</div></div>';
        var errorHtml = '<div style="padding:24px;text-align:center;font-family:Poppins,sans-serif;font-size:13px;color:#f64e60;">Could not load agencies. Please try again.</div>';

        window.jobDrawerCache = window.jobDrawerCache || {};
        var cacheKey = 'spartner_' + id;
        var hasCachedDrawer = !!window.jobDrawerCache[cacheKey];

        if (hasCachedDrawer) {
            // Instant 0ms paint from user RAM
            $('#kt_activitiesRecruiter_body').html(window.jobDrawerCache[cacheKey]);
        } else {
            $('#kt_activitiesRecruiter_body').html(loaderHtml);
        }

        $btn.data('in-flight', true);

        // Cancel previous pending drawer AJAX if active
        if (window.pendingSpartnerXhr && typeof window.pendingSpartnerXhr.abort === 'function') {
            try { window.pendingSpartnerXhr.abort(); } catch(err) {}
        }

        window.pendingSpartnerXhr = $.ajax({
            type: 'GET',
            url: url,
            cache: false,
            headers: {
                'X-Has-Representation': hasCachedDrawer ? '1' : '0'
            },
            success: function (response) {
                if (response && response.view) {
                    window.jobDrawerCache[cacheKey] = response.view;
                    $('#kt_activitiesRecruiter_body').html(response.view);
                } else if (hasCachedDrawer) {
                    // HTTP 304 Not Modified: representation already rendered from user RAM
                } else {
                    // Safe fallback if server sent empty response unexpectedly
                    $.getJSON(url + '?_force=1', function(fresh) {
                        if (fresh && fresh.view) {
                            window.jobDrawerCache[cacheKey] = fresh.view;
                            $('#kt_activitiesRecruiter_body').html(fresh.view);
                        } else {
                            $('#kt_activitiesRecruiter_body').html(errorHtml);
                        }
                    }).fail(function() {
                        $('#kt_activitiesRecruiter_body').html(errorHtml);
                    });
                }
            },
            error: function (xhr, status) {
                if (status !== 'abort' && !hasCachedDrawer) {
                    $('#kt_activitiesRecruiter_body').html(errorHtml);
                }
            },
            complete: function() {
                $btn.data('in-flight', false);
                window.pendingSpartnerXhr = null;
            }
        });
  });
    
});
</script>
<script>

$(document).on('click', '.client-hm-listing', function ()
   	{	
   		$('#kt_activitiesCHM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.jobs.clienthmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesCHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) {
								$('#kt_activitiesCHM_body').html(response.view); 
                if(response.logo){
                    $('#light_client_caption').removeClass('d-none');
                    $('#light_client_caption').html('<img src="' + response.logo +
                      '" height="35px" width="100%">');
                  }else{
                    $('#light_client_caption').addClass('d-none');
                  }
                $('#company_or_client').text(response.company_or_client);							
   						}
   	  			});
   	});
	
     $(document).on('click', '.team-listing', function ()
   	{
   	
   		$('#kt_activitiesTEAM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.jobs.currentteamlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activitiesTEAM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) {
								$('#kt_activitiesTEAM_body').html(response.view);		
                if(response.logo){
                    $('#light_logo_company').removeClass('d-none');
                    $('#light_logo_company').html('<img src="' + response.logo +
                      '" height="35px" width="100%">');
                  }else{
                    $('#light_logo_company').addClass('d-none');
                  }
    						}
    	  			});
    	});

    // Guard Add Team clicks on pending (unaccepted) job requests
    $(document).on('click', '.js-req-team-disabled', function (e) {
        e.preventDefault();
        e.stopPropagation();
        Swal.fire({
            text: 'Please accept the job request before adding your recruitment team.',
            icon: 'info',
            buttonsStyling: false,
            confirmButtonText: 'Ok, got it!',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
    });

    // Accept Job Request handler
    $(document).on('click', '.request_approval', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        var jobId = $btn.attr('data-row-id') || $btn.data('row-id');
        var recommendation_relation_id = $btn.attr('data-row-recommendation_relation_id') || $btn.data('row-recommendation_relation_id');
        var status = $btn.attr('data-row-status') || $btn.data('row-status') || 'accept';
        var originalHtml = $btn.html();

        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Please wait...');

        $.ajax({
            type: 'GET',
            url: "{{ route('service_company.jobs.requestapproval') }}",
            data: {
                jobId: jobId,
                rec_relation_id: recommendation_relation_id,
                status: status
            },
            success: function (response) {
                if (response.status === 'success' || response.status === 'accept') {
                    window.handleJobRequestMutationSuccess(recommendation_relation_id, response, function () {
                        Swal.fire({
                            text: 'Job request accepted successfully.',
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    });
                } else if (response.code === 'REQUEST_EXPIRED') {
                    window.handleJobRequestMutationSuccess(recommendation_relation_id, response, function () {
                        Swal.fire({
                            text: response.message || 'This job request has expired and cannot be accepted.',
                            icon: 'warning',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    });
                } else if (response.status === 'ignoreClient') {
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.fire({
                        text: 'Client details could not be verified for this company.',
                        icon: 'warning',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                } else {
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.fire({
                        text: response.message || 'Unable to accept job request.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(originalHtml);
                Swal.fire({
                    text: 'Something went wrong while accepting the job request. Please try again.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
        });
    });

    // Reject Job Request handler
    $(document).on('click', '.request_reject', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        var jobId = $btn.attr('data-row-id') || $btn.data('row-id');
        var recommendation_relation_id = $btn.attr('data-row-recommendation_relation_id') || $btn.data('row-recommendation_relation_id');
        var status = $btn.attr('data-row-status') || $btn.data('row-status') || 'ignore';

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Reject',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var originalHtml = $btn.html();
                $btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Please wait...');

                $.ajax({
                    type: 'GET',
                    url: "{{ route('service_company.jobs.requestapproval') }}",
                    data: {
                        jobId: jobId,
                        rec_relation_id: recommendation_relation_id,
                        status: status
                    },
                    success: function (response) {
                        window.handleJobRequestMutationSuccess(recommendation_relation_id, response, function () {
                            swalWithBootstrapButtons.fire('Rejected!', 'Selected request has been rejected.', 'success');
                        });
                    },
                    error: function () {
                        $btn.prop('disabled', false).html(originalHtml);
                        swalWithBootstrapButtons.fire('Error', 'Something went wrong while rejecting the request.', 'error');
                    }
                });
            }
        });
    });
</script>
<script>


   	
   	
  $('body').on('click', '.team_event_not', function(){
					    	Swal.fire({
									text				: "You are not authorized to change the status. Please contact the administrator for assistance.!",
									icon				: "warning",
									buttonsStyling		: false,
									confirmButtonText	: "Ok, got it!",
									customClass			: {
																confirmButton: "btn btn-primary"
														  }
                    });
	});



      $(document).ready(function() {
            // $.noConflict();

            if (window.currentKpiFilter && window.currentKpiFilter !== 'all') {
                $('.jh-card').removeClass('jh-card-active');
                $(`.jh-card[data-kpi="${window.currentKpiFilter}"]`).addClass('jh-card-active');
                $('.jh-radio-input').prop('checked', false);
                $(`.jh-card[data-kpi="${window.currentKpiFilter}"] .jh-radio-input`).prop('checked', true);
            }

            updateKpiViewToggle(window.currentKpiFilter);
            fetchCandidates();

            // Pagination bar markup (service_company.ajax_pages.pagination) uses .pagi-page /
            // .pagi-arrow buttons and a #rowsPerPageSelect dropdown, not the old .page-link
            // links this handler used to target — that mismatch is why Next/page-numbers and
            // the per-page selector did nothing. Disabled states (.pagi-disabled /
            // .pagi-active-disabled) already carry pointer-events:none in CSS, so clicks on
            // them never reach here; the :not() below is just a defensive second check.
            $(document).on('click', '#pagination .pagi-page:not(.pagi-active-disabled), #pagination .pagi-arrow:not(.pagi-disabled)', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page) return;

                fetchCandidates(page);
            });

        });

        var page_no = localStorage.getItem('currentPageJobs');
        if (!page_no) {
            page_no = 1;
        }
        localStorage.removeItem('jobsPageSize');
        var pageSize = 10;

        /**
         * Client-Side Jobs Store & Rendering Engine
         * Enables instant 0ms searching, filtering, sorting, and UI updates on the client side.
         */
        window.jobsState = {
            all: [],
            filtered: [],
            total: 0
        };

        function formatCompanyName(value) {
            return value ? String(value).toUpperCase() : '';
        }

        function formatJobPositionName(value) {
            if (!value) return '';
            var str = String(value).trim();
            if (str === '--------' || /^[-–—\s]+$/.test(str)) return '--------';
            return str.replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
        }

        function formatSalaryMode(value) {
            if (!value) return '';
            var text = String(value).replace('/', '').trim();
            var normalized = text.toLowerCase();
            if (normalized.indexOf('ann') !== -1 || normalized === 'yearly' || normalized === 'annual') return 'Annum';
            if (normalized.indexOf('month') !== -1) return 'Monthly';
            if (normalized.indexOf('hour') !== -1) return 'Hourly';
            return text;
        }

        function getEmploymentTypeMeta(value, rawValue) {
            var text = String(value || '').toLowerCase();
            var raw = String(rawValue || '');
            if (raw === '1' || text.indexOf('contract') !== -1) {
                return { icon: 'fa-file-signature', tone: 'contract' };
            }
            if (raw === '2' || text.indexOf('freelance') !== -1) {
                return { icon: 'fa-user-clock', tone: 'freelance' };
            }
            if (raw === '3' || text.indexOf('part') !== -1) {
                return { icon: 'fa-clock', tone: 'parttime' };
            }
            return { icon: 'fa-briefcase', tone: 'fulltime' };
        }

        function escapeHtmlAttr(val) {
            return String(val || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function formatRelativeTimeAgo(isoStr) {
            if (!isoStr) return '';
            var ts = Date.parse(isoStr);
            if (isNaN(ts)) return '';
            var diffSec = Math.max(0, Math.floor((Date.now() - ts) / 1000));
            if (diffSec < 60) return 'Just now';
            var mins = Math.floor(diffSec / 60);
            if (mins < 60) return mins + 'm ago';
            var hrs = Math.floor(mins / 60);
            if (hrs < 24) return hrs + 'h ago';
            var days = Math.floor(hrs / 24);
            if (days < 30) return days + 'd ago';
            var months = Math.floor(days / 30);
            return months + 'mo ago';
        }

        function computeExpiryTelemetry(expiresIso, jobEndDateIso) {
            var nowMs = Date.now();
            var reqTs = expiresIso ? Date.parse(expiresIso) : NaN;
            var jobTs = jobEndDateIso ? Date.parse(jobEndDateIso) : NaN;
            var targetTs = !isNaN(reqTs) ? reqTs : (!isNaN(jobTs) ? jobTs : NaN);
            if (!isNaN(reqTs) && !isNaN(jobTs)) {
                targetTs = Math.min(reqTs, jobTs);
            }

            if (isNaN(targetTs)) {
                return { isExpired: false, toneClass: 'pill-req-normal', borderColor: '#7c3aed', countdownText: 'Active Request', remainingMs: Infinity };
            }

            var diffMs = targetTs - nowMs;
            if (diffMs <= 0) {
                return { isExpired: true, toneClass: 'pill-req-expired', borderColor: '#94a3b8', countdownText: 'Expired', remainingMs: 0 };
            }

            var totalSec = Math.floor(diffMs / 1000);
            var days = Math.floor(totalSec / 86400);
            var hrs = Math.floor((totalSec % 86400) / 3600);
            var mins = Math.floor((totalSec % 3600) / 60);
            var secs = totalSec % 60;

            var label = '';
            if (days >= 1) {
                label = days + 'd ' + hrs + 'h left';
            } else if (hrs >= 1) {
                label = (hrs < 12)
                    ? (String(hrs).padStart(2, '0') + 'h ' + String(mins).padStart(2, '0') + 'm ' + String(secs).padStart(2, '0') + 's left')
                    : (hrs + 'h ' + mins + 'm left');
            } else {
                label = String(mins).padStart(2, '0') + 'm ' + String(secs).padStart(2, '0') + 's left';
            }

            if (diffMs < 12 * 3600 * 1000) {
                return { isExpired: false, toneClass: 'pill-req-critical', borderColor: '#ef4444', countdownText: 'Expires in ' + label, remainingMs: diffMs };
            }
            if (diffMs < 48 * 3600 * 1000) {
                return { isExpired: false, toneClass: 'pill-req-warning', borderColor: '#f59e0b', countdownText: 'Expires in ' + label, remainingMs: diffMs };
            }
            return { isExpired: false, toneClass: 'pill-req-normal', borderColor: '#7c3aed', countdownText: 'Expires in ' + label, remainingMs: diffMs };
        }

        window.jobRequestTimerId = window.jobRequestTimerId || null;
        function tickJobRequestCountdowns() {
            var $timers = $('#allJobList .js-req-live-timer');
            if (!$timers.length) {
                if (window.jobRequestTimerId) {
                    clearInterval(window.jobRequestTimerId);
                    window.jobRequestTimerId = null;
                }
                return;
            }
            $timers.each(function() {
                var $pill = $(this);
                var meta = computeExpiryTelemetry($pill.attr('data-expires-iso'), $pill.attr('data-job-expires-iso'));
                $pill.removeClass('pill-req-normal pill-req-warning pill-req-critical pill-req-expired').addClass(meta.toneClass);
                $pill.find('.js-req-timer-text').text(meta.countdownText);
                var $card = $pill.closest('.cd-job-request-card');
                if ($card.length) {
                    $card.css('border-left-color', meta.borderColor);
                    if (meta.isExpired) {
                        $card.find('.js-req-action-container').html('<div><span class="badge badge-light-danger fw-bold py-2 px-3 fs-7"><i class="fas fa-hourglass-end text-danger me-1"></i>Expired</span></div>');
                    }
                }
            });
        }

        function ensureJobRequestCountdownTimer() {
            tickJobRequestCountdowns();
            if (!window.jobRequestTimerId && $('#allJobList .js-req-live-timer').length) {
                window.jobRequestTimerId = setInterval(tickJobRequestCountdowns, 1000);
            }
        }

        function buildSharedJobSubcards(job, opts) {
            opts = opts || {};
            var isDirect = Boolean(job.is_direct_client || opts.isJobRequest);
            var profileDetailClass = isDirect ? 'client-show-detail' : 'show-detail';
            var directClientAttr = isDirect ? '1' : '0';
            var displayTitle = formatJobPositionName(job.title);
            var displayClientName = (isDirect && job.parent_company_name) ? formatCompanyName(job.parent_company_name) : formatCompanyName(job.clients_name);
            var displayHiringManager = formatJobPositionName(job.hiring_manager);
            var displaySalaryMode = formatSalaryMode(job.min_sal_mode);
            var salaryModeText = displaySalaryMode ? ' /' + displaySalaryMode : '';
            var statusIcon = job.status === 'Hiring' ? 'fa-user-group' : (job.status === 'Hold' ? 'fa-pause' : 'fa-box-archive');

            var statusCls = 'status-hiring';
            var displayStatus = job.status || 'Hiring';
            var statusBorderColor = '#10b981';
            if (job.status_raw === 'hold' || displayStatus === 'Hold' || displayStatus === 'On Hold') {
                statusCls = 'status-hold';
                displayStatus = 'On Hold';
                statusBorderColor = '#f59e0b';
            } else if (job.status_raw === 'inactive' || displayStatus === 'Closed') {
                statusCls = 'status-closed';
                displayStatus = 'Closed';
                statusBorderColor = '#ef4444';
            } else if (displayStatus === 'Removed' || job.status_raw === 'removed') {
                statusCls = 'status-inactive';
                displayStatus = 'Removed';
                statusBorderColor = '#94a3b8';
            }

            var statusBadgeHtml = '';
            if (displayStatus === 'Removed' || opts.editableStatus === false) {
                statusBadgeHtml = '<span class="cd-status-badge ' + statusCls + '"><i class="fas ' + statusIcon + ' me-1"></i>' + displayStatus + '</span>';
            } else {
                statusBadgeHtml = '<span data-row-id="' + job.id + '" data-direct-client="' + directClientAttr + '" class="cd-status-badge ' + statusCls + ' status_stage" title="Click to update status"><i class="fas ' + statusIcon + ' me-1"></i>' + displayStatus + '</span>';
            }

            var isOwnOrg = Boolean(job.is_self_company || job.recommendation_text === 'My Organization');
            var clientTypeLabel = opts.badgeLabel || (job.is_direct_client ? 'Direct Client' : (isOwnOrg ? 'My Organization' : ((job.recommendation_text) ? job.recommendation_text : 'Internal Client')));
            var clientTypeCls = opts.badgeCls || (job.is_direct_client ? 'type-direct' : (isOwnOrg ? 'type-my-org' : 'type-internal'));
            var clientTypeIcon = opts.badgeIcon || (isOwnOrg ? 'fa-id-badge' : (job.is_direct_client ? 'fa-shield-alt' : 'fa-building'));

            var logoHtml = '';
            if (job.logo_path) {
                logoHtml = '<img src="' + escapeHtmlAttr(job.logo_path) + '" alt="Company Logo" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';"><span style="display:none; color: #009ef7; font-weight: 700;">' + (job.initial_letter || 'J') + '</span>';
            } else {
                logoHtml = '<span style="color: #009ef7; font-weight: 700;">' + (job.initial_letter || 'J') + '</span>';
            }

            var isSalaryConfidential = Boolean(
                job.is_salary_confidential ||
                job.salary_visibility === 'confidential' ||
                job.salary_visibility === 'fully_confidential' ||
                job.salary_display === 'Confidential' ||
                ((parseFloat(job.min_salary) === 0 || !job.min_salary || job.min_salary === '0') &&
                 (parseFloat(job.max_salary) === 0 || !job.max_salary || job.max_salary === '0'))
            );

            var salaryText = isSalaryConfidential
                ? '<span class="text-danger" style="font-weight: 600;"><i class="fas fa-lock me-1"></i>Confidential</span>'
                : (job.min_salary_currency || '$') + ' ' + (job.min_salary || '0') + ' - ' + (job.max_salary || '0') + salaryModeText;

            var expText = (job.min_exp || 0) + ' - ' + (job.max_exp || 0) + ' Yrs';
            if (job.min_exp == job.max_exp && job.min_exp > 0) {
                expText = job.min_exp + ' Yrs';
            }

            var workModeClean = 'Work From Office';
            var rawWork = (job.work_type || '').toLowerCase();
            if (rawWork === 'remote' || rawWork === 'yes' || rawWork === 'wfh') {
                workModeClean = 'Remote';
            } else if (rawWork === 'hybrid' || rawWork === 'other') {
                workModeClean = 'Hybrid';
            }

            var locations = Array.isArray(job.work_locations) ? job.work_locations : [];
            var allCitiesList = (Array.isArray(job.cities) && job.cities.length > 0)
                ? job.cities.filter(Boolean)
                : (locations.length > 0 ? locations.map(function(l) { return l.city_name; }).filter(Boolean) : (job.primary_city || job.city ? [job.primary_city || job.city] : []));

            var seenCityKeys = new Set();
            var deduplicatedCities = [];
            allCitiesList.forEach(function(c) {
                var cleanC = formatJobPositionName(c);
                var cKey = cleanC.toLowerCase().trim();
                if (cKey && !seenCityKeys.has(cKey)) {
                    seenCityKeys.add(cKey);
                    deduplicatedCities.push(cleanC);
                }
            });
            allCitiesList = deduplicatedCities;

            var primaryCityName = job.primary_city ? formatJobPositionName(job.primary_city) : (allCitiesList.length > 0 ? allCitiesList[0] : (job.city ? formatJobPositionName(job.city) : ''));
            var primaryKey = primaryCityName.toLowerCase().trim();
            var remainingCities = allCitiesList.filter(function(c) {
                return c.toLowerCase().trim() !== primaryKey;
            });
            var extraCitiesCount = remainingCities.length;
            var remainingCitiesText = remainingCities.join(', ');
            var distinctCountries = locations.map(function(l) { return (l.country_name || '').trim(); }).filter(Boolean);
            var hasMultipleCountries = (new Set(distinctCountries.map(function(c) { return c.toLowerCase(); }))).size > 1;

            var locContentHtml = '';
            if (primaryCityName) {
                var cityTitle = extraCitiesCount > 0
                    ? 'Primary Location: ' + primaryCityName + ' | Other Locations (+' + extraCitiesCount + '): ' + remainingCitiesText
                    : 'Location: ' + primaryCityName;

                locContentHtml = '<span class="city-name-text" title="' + escapeHtmlAttr(cityTitle) + '">' + primaryCityName.substring(0, 16) + '</span>';
                if (extraCitiesCount > 0) {
                    var remainingTooltip = 'Other Locations (+' + extraCitiesCount + '): ' + remainingCitiesText;
                    var popoverItems = allCitiesList.map(function(c) {
                        var matchLoc = locations.find(function(l) { return (l.city_name || '').toLowerCase() === c.toLowerCase(); });
                        var countrySuffix = (hasMultipleCountries && matchLoc && matchLoc.country_name) ? ' <span style="opacity: 0.65; font-size: 9.5px;">(' + formatJobPositionName(matchLoc.country_name) + ')</span>' : '';
                        return '<div class="loc-popover-item"><i class="fas fa-map-marker-alt"></i> <span>' + c + countrySuffix + '</span></div>';
                    }).join('');

                    locContentHtml += '<div class="loc-more-badge-wrap" title="' + escapeHtmlAttr(remainingTooltip) + '">' +
                        '<span class="loc-more-badge" title="' + escapeHtmlAttr(remainingTooltip) + '">+' + extraCitiesCount + '</span>' +
                        '<div class="loc-popover-card">' +
                            '<div class="loc-popover-body">' + popoverItems + '</div>' +
                        '</div>' +
                    '</div>';
                }
            } else {
                locContentHtml = '<span class="city-name-text text-muted" style="color: #a1a5b7;">Not specified</span>';
            }

            var skillsHtml = '';
            if (job.skills && job.skills.length > 0) {
                skillsHtml = job.skills.map(function(s) {
                    return '<span class="cd-skill-pill">' + escapeHtmlAttr(s) + '</span>';
                }).join('');
            } else {
                skillsHtml = '<span class="text-muted" style="font-size: 10.5px;">No skills specified</span>';
            }

            var hiringManagerDisplay = (displayHiringManager && displayHiringManager !== '--------') ? displayHiringManager : '';
            var hmRole = (job.hiring_manager_designation && job.hiring_manager_designation !== '--------') ? ' (' + formatJobPositionName(job.hiring_manager_designation) + ')' : '';

            // SUB-CARD 1: JOB DETAILS
            var detailsHtml = '';
            detailsHtml += '    <div class="cd-job-subcard cd-subcard-details js-open-job-drawer" data-row-id="' + job.id + '" data-direct="' + directClientAttr + '" title="Click to view job details">';
            detailsHtml += '      <div class="cd-subcard-header">';
            detailsHtml += '        <span class="cd-subcard-label">Job Details</span>';
            detailsHtml += '        <div class="cd-header-badges">';
            if (opts.isJobRequest) {
                detailsHtml += '          <span class="cd-badge-client-type type-direct"><i class="fas fa-shield-alt me-1"></i>Direct Client</span>';
                detailsHtml += '          <span class="cd-badge-client-type ' + clientTypeCls + '"><i class="fas ' + clientTypeIcon + ' me-1"></i>' + clientTypeLabel + '</span>';
            } else {
                detailsHtml += '          <span class="cd-badge-client-type ' + clientTypeCls + '"><i class="fas ' + clientTypeIcon + ' me-1"></i>' + clientTypeLabel + '</span>';
            }
            detailsHtml += '          ' + statusBadgeHtml;
            detailsHtml += '        </div>';
            detailsHtml += '      </div>';
            detailsHtml += '      <div class="cd-details-body">';
            detailsHtml += '        <div class="cd-logo-box ' + (job.bg_text_decore || '') + '">' + logoHtml + '</div>';
            detailsHtml += '        <div class="cd-details-text">';
            detailsHtml += '          <a href="javascript:void(0);" class="cd-job-title ' + profileDetailClass + '" data-row-id="' + job.id + '" title="' + escapeHtmlAttr(displayTitle) + '">' + (displayTitle ? displayTitle.substring(0, 32) : 'Untitled Job') + '</a>';
            detailsHtml += '          <div class="cd-job-meta">';
            detailsHtml += '            <span class="cd-job-id">#' + job.id + '</span>';
            detailsHtml += '            <span class="cd-bullet">&bull;</span>';
            detailsHtml += '            <span class="cd-job-dept" title="' + escapeHtmlAttr(job.department || '') + '">' + (job.department || 'Information Technology') + '</span>';
            detailsHtml += '          </div>';
            detailsHtml += '          <div class="cd-job-client-row">';
            detailsHtml += '            <span class="cd-job-client-name" title="' + escapeHtmlAttr(displayClientName) + '">' + (displayClientName ? displayClientName.substring(0, 20) : 'CLIENT') + '</span>';
            detailsHtml += '            <span class="cd-bullet">&bull;</span>';
            detailsHtml += '            <span class="cd-job-emp-type"><i class="fas fa-briefcase text-primary me-1"></i>' + (job.employment_type || 'Full-Time') + '</span>';
            detailsHtml += '          </div>';
            detailsHtml += '        </div>';
            detailsHtml += '      </div>';
            detailsHtml += '    </div>';

            // SUB-CARD 2: COMPENSATION & EXP
            var compLocHtml = '';
            compLocHtml += '    <div class="cd-job-subcard cd-subcard-comp-loc js-open-job-drawer" data-row-id="' + job.id + '" data-direct="' + directClientAttr + '" title="Click to view job details">';
            compLocHtml += '      <div class="cd-subcard-header">';
            compLocHtml += '        <span class="cd-subcard-label">Compensation &amp; Exp</span>';
            compLocHtml += '      </div>';
            compLocHtml += '      <div class="cd-subcard-body">';
            compLocHtml += '        <div class="cd-val-salary"><strong>' + salaryText + '</strong></div>';
            compLocHtml += '        <div class="cd-val-exp"><span>' + expText + '</span></div>';
            compLocHtml += '        <div class="cd-val-loc">';
            compLocHtml += '          <i class="fas fa-map-marker-alt text-muted me-1"></i>' + locContentHtml;
            compLocHtml += '          <span class="cd-bullet">&bull;</span>';
            compLocHtml += '          <span>' + workModeClean + '</span>';
            compLocHtml += '        </div>';
            compLocHtml += '      </div>';
            compLocHtml += '    </div>';

            return {
                detailsHtml: detailsHtml,
                compLocHtml: compLocHtml,
                skillsHtml: skillsHtml,
                displayTitle: displayTitle,
                displayClientName: displayClientName,
                displayHiringManager: displayHiringManager,
                hiringManagerDisplay: hiringManagerDisplay,
                hmRole: hmRole,
                statusBorderColor: statusBorderColor
            };
        }

        function renderJobRequestsClientSide(requests) {
            if (!requests || requests.length === 0) {
                return '<div class="cd-jobs-empty text-center py-16 px-6 my-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px;">' +
                       '<i class="fas fa-handshake fa-2x mb-3 text-muted d-block"></i>' +
                       '<div class="fw-bold fs-6 text-gray-700 mb-1">No Active Job Requests Found</div>' +
                       '<div class="text-muted fs-7">There are no pending client job requests matching the current criteria.</div></div>';
            }

            var html = '';
            requests.forEach(function(job) {
                var shared = buildSharedJobSubcards(job, {
                    isJobRequest: true,
                    badgeLabel: 'Job Request',
                    badgeCls: 'type-request',
                    badgeIcon: 'fa-handshake',
                    editableStatus: false
                });
                var expMeta = computeExpiryTelemetry(job.expires_at_iso, job.job_end_date_iso);
                var recId = job.recommendation_relation_id || job.id;
                var isAcceptedReq = Boolean(job.is_accepted || job.final_status === 1);

                var sentDateDisplay = job.sent_at_display || job.added_date || '—';
                var sentTimePart = job.sent_time_display ? ' <span class="text-muted">(' + job.sent_time_display + ')</span>' : '';
                var reqExpiryDatePart = job.expires_at_date || '';
                var reqExpiryTimePart = job.expires_at_time_tz ? ' <span class="text-muted">(' + job.expires_at_time_tz + ')</span>' : '';
                var reqExpiryFullDisplay = job.expires_at_display || '—';

                var postedDateDisplay = job.posted_date || job.added_date || sentDateDisplay || '—';

                var requestedByName = (job.sent_by_name && job.sent_by_name !== '--------')
                    ? formatJobPositionName(job.sent_by_name)
                    : (shared.hiringManagerDisplay ? shared.hiringManagerDisplay : '');
                var requestedByRole = '';
                if (job.sent_by_name && job.sent_by_name !== '--------') {
                    requestedByRole = (job.sent_by_designation && job.sent_by_designation !== '--------')
                        ? ' (' + formatJobPositionName(job.sent_by_designation) + ')'
                        : '';
                } else {
                    requestedByRole = shared.hmRole || '';
                }

                // Dynamic Team Sub-card state (follows Job Card Team design; disabled Add Team until accepted)
                var teamCount = parseInt(job.partners_count, 10) || 0;
                var teamLeadName = job.partners_first_name
                    ? formatJobPositionName(job.partners_first_name)
                    : (teamCount > 0 ? 'Team' : 'Not Assigned');
                var teamMemberText = '0 Members';
                if (teamCount > 1) {
                    teamMemberText = '+ ' + (teamCount - 1) + ' other member' + (teamCount - 1 > 1 ? 's' : '');
                } else if (teamCount === 1) {
                    teamMemberText = '1 Member';
                }

                var openingsNum = parseInt(job.total_positions, 10) || 1;
                var openingsLabel = openingsNum + ' Opening' + (openingsNum > 1 ? 's' : '');

                html += '<div class="cd-job-card cd-job-request-card" data-entity="job-request" data-job-id="' + job.id + '" data-rec-id="' + recId + '" style="border-left: 3.5px solid ' + expMeta.borderColor + ' !important;">';
                html += '  <div class="cd-job-card-grid">';

                // SUB-CARD 1 & SUB-CARD 2 (Job Details + Compensation & Exp)
                html += shared.detailsHtml;
                html += shared.compLocHtml;

                // SUB-CARD 3: TEAM (Dynamic design matching Hiring Job Card; locked until request is accepted)
                html += '    <div class="cd-job-subcard cd-subcard-team">';
                html += '      <div class="cd-subcard-header">';
                html += '        <span class="cd-subcard-label"><i class="fa fa-users me-1 text-muted"></i>Team</span>';
                html += '      </div>';
                html += '      <div class="cd-subcard-body">';
                if (isAcceptedReq) {
                    html += '        <div class="cd-val-name team-listing" data-row-id="' + job.id + '" title="' + escapeHtmlAttr(teamLeadName) + '"><strong>' + teamLeadName + '</strong></div>';
                    html += '        <div class="cd-val-sub">' + teamMemberText + '</div>';
                    html += '        <a href="javascript:void(0);" class="cd-action-link team-listing" data-row-id="' + job.id + '">+ Add / View</a>';
                } else {
                    html += '        <div class="cd-val-name" style="cursor:default; color:#64748b;" title="' + escapeHtmlAttr(teamLeadName) + '"><strong>' + teamLeadName + '</strong></div>';
                    html += '        <div class="cd-val-sub">' + teamMemberText + '</div>';
                    html += '        <button type="button" class="cd-req-team-disabled-btn js-req-team-disabled" aria-disabled="true" data-row-id="' + job.id + '" data-rec-id="' + recId + '" title="Accept job request before adding your recruitment team"><i class="fas fa-plus" style="font-size:9px;"></i> Add Team</button>';
                }
                html += '      </div>';
                html += '    </div>';

                // SUB-CARD 4: SENT DATE & REQUEST EXPIRY (with Date, Time & Timezone)
                html += '    <div class="cd-job-subcard cd-subcard-req-expiry">';
                html += '      <div class="cd-subcard-header">';
                html += '        <span class="cd-subcard-label"><i class="far fa-clock me-1 text-muted"></i>Sent &amp; Expiry</span>';
                html += '      </div>';
                html += '      <div class="cd-req-timeline-rows">';
                html += '        <div class="cd-req-date-row" title="Job request sent on ' + escapeHtmlAttr(sentDateDisplay + (job.sent_time_display ? ', ' + job.sent_time_display : '')) + '">';
                html += '          <i class="far fa-paper-plane text-primary"></i>';
                html += '          <span>Sent: <strong>' + sentDateDisplay + '</strong>' + sentTimePart + '</span>';
                html += '        </div>';
                html += '        <div class="cd-req-date-row" title="Request expiry date &amp; time: ' + escapeHtmlAttr(reqExpiryFullDisplay) + '">';
                html += '          <i class="far fa-calendar-times text-warning"></i>';
                if (reqExpiryDatePart && reqExpiryDatePart !== '—') {
                    html += '          <span>Req Expiry: <strong>' + reqExpiryDatePart + '</strong>' + reqExpiryTimePart + '</span>';
                } else {
                    html += '          <span>Req Expiry: <strong>' + reqExpiryFullDisplay + '</strong></span>';
                }
                html += '        </div>';
                html += '        <div class="cd-req-expiry-pill ' + expMeta.toneClass + ' js-req-live-timer" data-expires-iso="' + escapeHtmlAttr(job.expires_at_iso || '') + '" data-job-expires-iso="' + escapeHtmlAttr(job.job_end_date_iso || '') + '" data-rec-id="' + recId + '" data-job-id="' + job.id + '" title="Request Expiry: ' + escapeHtmlAttr(reqExpiryFullDisplay) + '">';
                html += '          <i class="far fa-hourglass"></i><span class="js-req-timer-text">' + expMeta.countdownText + '</span>';
                html += '        </div>';
                html += '      </div>';
                html += '    </div>';

                // SUB-CARD 5: REQUEST ACTION & COMMERCIAL TERMS
                html += '    <div class="cd-job-subcard cd-subcard-req-action">';
                html += '      <div class="cd-subcard-header">';
                html += '        <span class="cd-subcard-label">Action Required</span>';
                html += '        <span class="text-muted" style="font-size:9.5px; font-weight:600;">Req #' + recId + '</span>';
                html += '      </div>';
                html += '      <div class="cd-req-action-stack">';
                html += '        <div class="js-req-action-container">';
                if (expMeta.isExpired) {
                    html += '          <div><span class="badge badge-light-danger fw-bold py-2 px-3 fs-7"><i class="fas fa-hourglass-end text-danger me-1"></i>Expired</span></div>';
                } else {
                    html += '          <div class="cd-req-btn-row">';
                    html += '            <button type="button" class="cd-req-btn-accept request_approval" data-row-status="accept" data-row-id="' + job.id + '" data-row-recommendation_relation_id="' + recId + '"><i class="fas fa-check"></i>Accept</button>';
                    html += '            <button type="button" class="cd-req-btn-reject request_reject" data-row-status="ignore" data-row-id="' + job.id + '" data-row-recommendation_relation_id="' + recId + '"><i class="fas fa-times"></i>Reject</button>';
                    html += '          </div>';
                }
                html += '        </div>';
                html += '        <div class="cd-req-meta-footer">';
                html += '          <span><strong>' + openingsLabel + '</strong></span>';
                html += '          <a href="javascript:void(0);" class="cd-req-terms-btn client-terms-listing" data-row-id="' + job.id + '" data-row-recommendation_relation_id="' + recId + '"><i class="fas fa-file-contract me-1"></i>View Terms</a>';
                html += '        </div>';
                html += '      </div>';
                html += '    </div>';

                html += '  </div>'; // End upper grid

                // SUB-CARD 6: BOTTOM ACTIVITY & SKILLS STRIP (Posted Date & Requested By; SLA removed)
                html += '  <div class="cd-job-card-bottom">';
                html += '    <div class="cd-bottom-left">';
                html += '      <span><i class="far fa-calendar-alt text-muted me-1"></i>Posted Date: <strong>' + postedDateDisplay + '</strong></span>';
                if (requestedByName) {
                    html += '      <span class="cd-bullet">&bull;</span>';
                    html += '      <span><i class="far fa-user text-muted me-1"></i>Requested By: <strong>' + requestedByName + requestedByRole + '</strong></span>';
                }
                html += '    </div>';
                html += '    <div class="cd-bottom-skills">' + shared.skillsHtml + '</div>';
                html += '  </div>';

                html += '</div>';
            });

            return html;
        }

        function renderJobsClientSide(jobs) {
            if (!jobs || jobs.length === 0) {
                return '<div class="cd-jobs-empty text-center py-16 px-6 my-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px;">' +
                       '<i class="fa fa-briefcase fa-2x mb-3 text-muted d-block"></i>' +
                       '<div class="fw-bold fs-6 text-gray-700 mb-1">No Job Requisitions Found</div>' +
                       '<div class="text-muted fs-7">No job requisitions match the current criteria.</div></div>';
            }

            var html = '';
            jobs.forEach(function(job) {
                var shared = buildSharedJobSubcards(job, { editableStatus: true });
                var displayAgencyName = formatCompanyName(job.agency_company_name);

                // Team Sub-card
                var leadName = job.partners_first_name ? formatJobPositionName(job.partners_first_name) : 'Team';
                var memberCountText = 'No other members';
                if (job.partners_count > 1) {
                    memberCountText = '+ ' + (job.partners_count - 1) + ' other member' + (job.partners_count - 1 > 1 ? 's' : '');
                } else if (job.partners_count === 1) {
                    memberCountText = '1 Member';
                }
                var teamDrawerClass = job.is_direct_client ? 'team-listing' : 'hm-listing';

                // Agency Sub-card (No artificial matching badge)
                var agencyDisplayText = '—';
                if (job.is_direct_client) {
                    agencyDisplayText = displayAgencyName || 'Direct Client';
                } else if (job.agency_count > 0) {
                    agencyDisplayText = job.agency_count + (job.agency_count > 1 ? ' Agencies' : ' Agency');
                }

                // Pipeline Sub-card
                var apptrackerBase = job.is_direct_client
                    ? "{{ url('/service_company/apptracker/clientapp') }}/" + job.encrypted_id + "/"
                    : "{{ url('/service_company/apptracker') }}/" + job.encrypted_id + "/";

                var allAppUrl = job.is_direct_client
                    ? "{{ url('/service_company/apptracker/clientapp') }}/" + job.encrypted_id + "/all"
                    : "{{ url('/service_company/apptracker') }}/" + job.encrypted_id + "/all";

                var stageLabels = [
                    { id: 1, key: 's-sou', label: 'SOU', title: 'Sourcing' },
                    { id: 2, key: 's-scr', label: 'SCR', title: 'Screening' },
                    { id: 3, key: 's-eva', label: 'EVA', title: 'Evaluation' },
                    { id: 4, key: 's-neg', label: 'NEG', title: 'Negotiation' },
                    { id: 5, key: 's-off', label: 'OFF', title: 'Offer' },
                    { id: 6, key: 's-joi', label: 'JOI', title: 'Joining' },
                    { id: 7, key: 's-rej', label: 'REJ', title: 'Rejected' }
                ];

                var pipelineTrackHtml = '<div class="cd-track-stage" title="INW - In Review"><span class="cd-track-label">INW</span><a href="' + allAppUrl + '" class="cd-track-badge s-inw text-decoration-none">' + (job.inreview_counter || 0) + '</a></div>';
                stageLabels.forEach(function(stg) {
                    var count = (job.stage_counts && job.stage_counts[stg.id]) ? job.stage_counts[stg.id] : 0;
                    var stgUrl = apptrackerBase + stg.id;
                    pipelineTrackHtml += '<div class="cd-track-stage" title="' + stg.label + ' - ' + stg.title + '"><span class="cd-track-label">' + stg.label + '</span><a href="' + stgUrl + '" class="cd-track-badge ' + stg.key + ' text-decoration-none">' + count + '</a></div>';
                });

                var formattedDate = job.posted_date || job.added_date || '';

                // MASTER PRIMARY CARD (Rule 20 Modern Enterprise Card + Sub-Card)
                html += '<div class="cd-job-card" data-entity="job" data-job-id="' + job.id + '" style="border-left: 3.5px solid ' + shared.statusBorderColor + ' !important;">';
                html += '  <div class="cd-job-card-grid">';
                html += shared.detailsHtml;
                html += shared.compLocHtml;

                // SUB-CARD 3: TEAM
                html += '    <div class="cd-job-subcard cd-subcard-team">';
                html += '      <div class="cd-subcard-header">';
                html += '        <span class="cd-subcard-label"><i class="fa fa-users me-1 text-muted"></i>Team</span>';
                html += '      </div>';
                html += '      <div class="cd-subcard-body">';
                html += '        <div class="cd-val-name ' + teamDrawerClass + '" data-row-id="' + job.id + '" title="' + escapeHtmlAttr(leadName) + '"><strong>' + leadName + '</strong></div>';
                html += '        <div class="cd-val-sub">' + memberCountText + '</div>';
                html += '        <a href="javascript:void(0);" class="cd-action-link ' + teamDrawerClass + '" data-row-id="' + job.id + '">+ Add / View</a>';
                html += '      </div>';
                html += '    </div>';

                // SUB-CARD 4: AGENCY
                html += '    <div class="cd-job-subcard cd-subcard-agency">';
                html += '      <div class="cd-subcard-header">';
                html += '        <span class="cd-subcard-label"><i class="fa fa-handshake me-1 text-muted"></i>Agency</span>';
                html += '      </div>';
                html += '      <div class="cd-subcard-body">';
                if (agencyDisplayText !== '—') {
                    html += '        <div class="cd-agency-val" title="' + escapeHtmlAttr(agencyDisplayText) + '"><strong>' + agencyDisplayText + '</strong></div>';
                    if (!job.is_direct_client) {
                        html += '        <a href="javascript:void(0);" data-row-id="' + job.id + '" class="cd-action-link spartner-listing">+ Manage Agency</a>';
                    }
                } else {
                    html += '        <div class="cd-agency-empty">—</div>';
                    html += '        <button type="button" class="btn btn-xs btn-light-primary spartner-listing py-1 px-2 mt-1" data-row-id="' + job.id + '" style="font-size: 11px;"><i class="fas fa-plus me-1"></i>Add Agency</button>';
                }
                html += '      </div>';
                html += '    </div>';

                // SUB-CARD 5: HIRING PIPELINE
                html += '    <div class="cd-job-subcard cd-subcard-pipeline js-goto-candidates" data-apptracker-url="' + allAppUrl + '" title="Click to view all candidates">';
                html += '      <div class="cd-subcard-header">';
                html += '        <span class="cd-subcard-label">Hiring Pipeline</span>';
                html += '      </div>';
                html += '      <div class="cd-subcard-body">';
                html += '        <div class="cd-pipeline-track">' + pipelineTrackHtml + '</div>';
                html += '        <div class="cd-track-summary">';
                html += '          <span>Apps: <strong>' + (job.application_counter || 0) + '</strong></span>';
                html += '          <span class="cd-bullet">&bull;</span>';
                html += '          <span>Interviews: <strong>' + (job.interview_counter || 0) + '</strong></span>';
                html += '        </div>';
                html += '      </div>';
                html += '    </div>';

                html += '  </div>'; // End upper grid

                // SUB-CARD 6: BOTTOM ACTIVITY STRIP
                html += '  <div class="cd-job-card-bottom">';
                html += '    <div class="cd-bottom-left">';
                if (formattedDate) {
                    html += '      <span><i class="far fa-calendar-alt text-muted me-1"></i>Posted: <strong>' + formattedDate + '</strong></span>';
                }
                if (shared.hiringManagerDisplay) {
                    html += '      <span class="cd-bullet">&bull;</span>';
                    html += '      <span><i class="far fa-user text-muted me-1"></i>HM: <strong>' + shared.hiringManagerDisplay + shared.hmRole + '</strong></span>';
                }
                html += '    </div>';
                html += '    <div class="cd-bottom-skills">' + shared.skillsHtml + '</div>';
                html += '  </div>';

                html += '</div>';
            });

            return html;
        }

        /**
         * Synchronizes UI state immediately after Job Request Accept / Reject / Expired actions:
         * drops acted request optimistically, updates all KPI & status badges, purges IndexedDB,
         * and reloads fresh authoritative ES data bypassing local cache.
         */
        window.handleJobRequestMutationSuccess = function(recId, response, onNotify) {
            var dataCnt = (response && response.dataCnt) ? response.dataCnt : null;
            if (dataCnt) {
                syncTopKpiCounts(dataCnt);
                syncStatusTabCountsFromDataCnt(dataCnt);
            }

            if (window.jobsState && Array.isArray(window.jobsState.jobRequests)) {
                window.jobsState.jobRequests = window.jobsState.jobRequests.filter(function(r) {
                    return String(r.recommendation_relation_id || r.id) !== String(recId);
                });
            }

            var kpi = window.currentKpiFilter || 'all';
            if (kpi === 'job_request' || kpi === 'request') {
                if (window.jobsState && Array.isArray(window.jobsState.all)) {
                    window.jobsState.all = window.jobsState.all.filter(function(r) {
                        return String(r.recommendation_relation_id || r.id) !== String(recId);
                    });
                    window.jobsState.filtered = window.jobsState.all;
                    $('#allJobList').html(renderJobRequestsClientSide(window.jobsState.all));
                    if (window.jobsState.all.length > 0) {
                        ensureJobRequestCountdownTimer();
                    } else if (window.jobRequestTimerId) {
                        clearInterval(window.jobRequestTimerId);
                        window.jobRequestTimerId = null;
                    }
                }
            } else if (kpi === 'all' || !kpi) {
                var curJobs = (window.jobsState && Array.isArray(window.jobsState.filtered))
                    ? window.jobsState.filtered
                    : ((window.jobsState && Array.isArray(window.jobsState.all)) ? window.jobsState.all : []);
                var curReqs = (window.jobsState && Array.isArray(window.jobsState.jobRequests))
                    ? window.jobsState.jobRequests
                    : [];
                if (window.jobsState) {
                    window.jobsState.filteredRequests = curReqs;
                }
                renderTotalJobsUnifiedStream(curReqs, curJobs);
            }

            if (typeof onNotify === 'function') {
                onNotify();
            }

            var refreshLive = function() {
                fetchCandidates(1, '', undefined, { bypassCache: true });
            };
            if (window.JeroJobsLocalStore && typeof window.JeroJobsLocalStore.purge === 'function') {
                Promise.resolve(window.JeroJobsLocalStore.purge()).then(refreshLive, refreshLive);
            } else {
                refreshLive();
            }
        };

        /**
         * Renders the unified Total Jobs tab view:
         * 1) Active/unexpired Job Requests at the top (when count > 0) using exact Job Request Card UI
         * 2) Existing Hiring Jobs below with clear section separation (or clean empty state)
         */
        function renderTotalJobsUnifiedStream(requests, jobs) {
            var activeReqs = (Array.isArray(requests) ? requests : []).filter(function(req) {
                return !computeExpiryTelemetry(req.expires_at_iso, req.job_end_date_iso).isExpired;
            });
            var activeJobs = Array.isArray(jobs) ? jobs : [];
            var html = '';

            if (activeReqs.length > 0) {
                html += '<div class="cd-jobs-unified-section cd-jobs-unified-requests">';
                html += '  <div class="cd-jobs-section-header section-requests">';
                html += '    <div class="cd-jobs-section-header-left">';
                html += '      <span class="cd-jobs-section-icon"><i class="fas fa-handshake"></i></span>';
                html += '      <span class="cd-jobs-section-title">Pending Job Requests</span>';
                html += '      <span class="cd-jobs-section-badge">' + activeReqs.length + '</span>';
                html += '    </div>';
                html += '    <span class="cd-jobs-section-sub">Accept to convert into an active Hiring Job or Reject to dismiss</span>';
                html += '  </div>';
                html += renderJobRequestsClientSide(activeReqs);
                html += '</div>';
                html += '<div class="cd-jobs-section-divider"></div>';
                html += '<div class="cd-jobs-unified-section cd-jobs-unified-hiring">';
                html += '  <div class="cd-jobs-section-header section-hiring">';
                html += '    <div class="cd-jobs-section-header-left">';
                html += '      <span class="cd-jobs-section-icon"><i class="fas fa-briefcase"></i></span>';
                html += '      <span class="cd-jobs-section-title">Hiring Jobs</span>';
                html += '      <span class="cd-jobs-section-badge">' + activeJobs.length + '</span>';
                html += '    </div>';
                html += '    <span class="cd-jobs-section-sub">Active &amp; managed job requisitions</span>';
                html += '  </div>';
                if (activeJobs.length > 0) {
                    html += renderJobsClientSide(activeJobs);
                } else {
                    html += '<div class="cd-jobs-hiring-empty">' +
                            '<i class="fa fa-briefcase fa-lg text-muted mb-2 d-block"></i>' +
                            '<div class="fw-bold fs-6 text-gray-700 mb-1">No Hiring Jobs Found</div>' +
                            '<div class="text-muted fs-7">Accept a pending job request above or add a new job to see it listed here.</div></div>';
                }
                html += '</div>';
            } else if (activeJobs.length > 0) {
                html += renderJobsClientSide(activeJobs);
            } else {
                html += '<div class="cd-jobs-empty text-center py-16 px-6 my-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px;">' +
                        '<i class="fa fa-briefcase fa-2x mb-3 text-muted d-block"></i>' +
                        '<div class="fw-bold fs-6 text-gray-700 mb-1">No Job Requisitions Found</div>' +
                        '<div class="text-muted fs-7">No jobs match the current criteria.</div></div>';
            }

            $('#allJobList').html(html);
            if (activeReqs.length > 0) {
                ensureJobRequestCountdownTimer();
            } else if (window.jobRequestTimerId) {
                clearInterval(window.jobRequestTimerId);
                window.jobRequestTimerId = null;
            }
        }

        function filtersdata() {
            var hasJobs = window.jobsState && Array.isArray(window.jobsState.all) && window.jobsState.all.length > 0;
            var hasReqs = window.jobsState && Array.isArray(window.jobsState.jobRequests) && window.jobsState.jobRequests.length > 0;
            if (!hasJobs && !hasReqs) {
                return fetchCandidates(1);
            }

            var kpi = window.currentKpiFilter || 'all';
            var isJobRequestTab = (kpi === 'job_request' || kpi === 'request');
            var isTotalJobsTab = (!kpi || kpi === 'all');
            var searchTerm = ($('.inputJobSearch').val() || $('#inputSearch').val() || '').toLowerCase().trim();
            var status = $('#status').val() || 'all';
            var employmentType = $('#employment_type').val() || 'all';
            var expFilter = $('#experience_filter').val() || 'all';
            var sortVal = $('#hiringtype').val() || '1';

            // Shared matcher across both job request cards & hiring job cards (zero duplicated filter rules)
            function matchesSharedCriteria(item, isReqItem) {
                if (isReqItem) {
                    if (computeExpiryTelemetry(item.expires_at_iso, item.job_end_date_iso).isExpired) {
                        return false;
                    }
                } else {
                    if (kpi === 'my_organization') {
                        if (item.is_direct_client || !item.is_self_company) return false;
                    } else if (kpi === 'internal') {
                        if (item.is_direct_client || item.is_self_company) return false;
                    } else if (kpi === 'direct') {
                        if (!item.is_direct_client) return false;
                    }

                    if (status !== 'all' && status !== '') {
                        var s = (item.status || '').toLowerCase();
                        var sr = (item.status_raw || '').toLowerCase();
                        if (status === 'active' && s !== 'hiring' && sr !== 'active') return false;
                        if (status === 'hold' && s !== 'hold' && sr !== 'hold') return false;
                        if (status === 'inactive' && s !== 'closed' && sr !== 'inactive') return false;
                        if (status === 'removed' && s !== 'removed') return false;
                    }
                }

                if (searchTerm !== '') {
                    var matchTitle = (item.title || '').toLowerCase().includes(searchTerm);
                    var matchDept = (item.department || '').toLowerCase().includes(searchTerm);
                    var matchClient = (item.clients_name || '').toLowerCase().includes(searchTerm);
                    var matchCity = (item.city || '').toLowerCase().includes(searchTerm) ||
                        (Array.isArray(item.cities) && item.cities.some(function(c) { return String(c).toLowerCase().includes(searchTerm); })) ||
                        (Array.isArray(item.work_locations) && item.work_locations.some(function(wl) { return (wl.city_name || '').toLowerCase().includes(searchTerm) || (wl.country_name || '').toLowerCase().includes(searchTerm); }));
                    var matchHM = (item.hiring_manager || '').toLowerCase().includes(searchTerm) || (item.sent_by_name || '').toLowerCase().includes(searchTerm);
                    var matchSkills = (item.skills || []).some(function(sk) { return sk.toLowerCase().includes(searchTerm); });
                    var matchId = String(item.id) === searchTerm || String(item.recommendation_relation_id || '') === searchTerm;
                    if (!matchTitle && !matchDept && !matchClient && !matchCity && !matchHM && !matchSkills && !matchId) {
                        return false;
                    }
                }

                if (employmentType !== 'all' && employmentType !== '') {
                    if (String(item.employment_type_raw) !== String(employmentType)) return false;
                }

                if (expFilter !== 'all' && expFilter !== '') {
                    var minE = parseInt(item.min_exp) || 0;
                    var maxE = parseInt(item.max_exp) || 0;
                    if (expFilter === '0-1' && !(minE <= 1 && maxE >= 0)) return false;
                    if (expFilter === '1-3' && !(minE <= 3 && maxE >= 1)) return false;
                    if (expFilter === '3-5' && !(minE <= 5 && maxE >= 3)) return false;
                    if (expFilter === '5+' && maxE < 5) return false;
                }

                return true;
            }

            function sortItems(items, isReqList) {
                return items.slice().sort(function(a, b) {
                    if (sortVal === '7' && isReqList) {
                        var aRem = computeExpiryTelemetry(a.expires_at_iso, a.job_end_date_iso).remainingMs;
                        var bRem = computeExpiryTelemetry(b.expires_at_iso, b.job_end_date_iso).remainingMs;
                        return aRem - bRem;
                    }
                    if (sortVal === '1') {
                        return isReqList
                            ? ((b.recommendation_relation_id || b.id) - (a.recommendation_relation_id || a.id))
                            : (b.id - a.id);
                    }
                    if (sortVal === '2') {
                        return isReqList
                            ? ((a.recommendation_relation_id || a.id) - (b.recommendation_relation_id || b.id))
                            : (a.id - b.id);
                    }
                    if (sortVal === '3') return (parseFloat(a.max_salary_raw ?? a.min_salary) || 0) - (parseFloat(b.max_salary_raw ?? b.min_salary) || 0);
                    if (sortVal === '4') return (parseFloat(b.max_salary_raw ?? b.max_salary) || 0) - (parseFloat(a.max_salary_raw ?? a.max_salary) || 0);
                    if (sortVal === '5') return (a.clients_name || '').localeCompare(b.clients_name || '');
                    if (sortVal === '6') return (b.clients_name || '').localeCompare(a.clients_name || '');
                    return b.id - a.id;
                });
            }

            if (isJobRequestTab) {
                var filteredReqsOnly = sortItems(
                    (window.jobsState.all || []).filter(function(item) { return matchesSharedCriteria(item, true); }),
                    true
                );
                window.jobsState.filtered = filteredReqsOnly;
                $('#allJobList').html(renderJobRequestsClientSide(filteredReqsOnly));
                ensureJobRequestCountdownTimer();
                $('#all_jobs_count').html(filteredReqsOnly.length);
                return;
            }

            var filteredJobs = sortItems(
                (window.jobsState.all || []).filter(function(item) { return matchesSharedCriteria(item, false); }),
                false
            );
            window.jobsState.filtered = filteredJobs;

            if (isTotalJobsTab) {
                // Pending requests always appear at top regardless of selected status filter tab
                var filteredTopReqs = sortItems(
                    (window.jobsState.jobRequests || []).filter(function(req) { return matchesSharedCriteria(req, true); }),
                    true
                );
                window.jobsState.filteredRequests = filteredTopReqs;
                renderTotalJobsUnifiedStream(filteredTopReqs, filteredJobs);
            } else {
                $('#allJobList').html(renderJobsClientSide(filteredJobs));
            }
            $('#all_jobs_count').html(filteredJobs.length);
        }

        function applyJobsResponse(response, page) {
            window.jobDrawerCache = {};
            window.currentJobsAjax = null;
            $('#jobSearchBtn').prop('disabled', false).removeClass('disabled');
            $('.loadingMessage').css({ display: 'none' });
            $('.loader_search').css({ display: 'none' });
            $('.page-loader').css('display', 'none');
            localStorage.setItem('currentPageJobs', page);

            updateKpiViewToggle(window.currentKpiFilter);

            var kpi = window.currentKpiFilter || 'all';
            var isJobRequestTab = (kpi === 'job_request' || kpi === 'request');
            var isTotalJobsTab = (!kpi || kpi === 'all');

            if (isJobRequestTab) {
                if (response.jobs && response.jobs.length > 0) {
                    window.jobsState = {
                        all: response.jobs,
                        filtered: response.jobs,
                        jobRequests: response.jobs,
                        filteredRequests: response.jobs,
                        total: response.total
                    };
                    $('#allJobList').html(renderJobRequestsClientSide(response.jobs));
                    ensureJobRequestCountdownTimer();
                } else if (response.html && response.html.trim() !== '') {
                    $('#allJobList').html(response.html);
                } else {
                    window.jobsState = { all: [], filtered: [], jobRequests: [], filteredRequests: [], total: 0 };
                    $('#allJobList').html(renderJobRequestsClientSide([]));
                }
            } else if (isTotalJobsTab) {
                var rawJobs = Array.isArray(response.jobs) ? response.jobs : [];
                var rawReqs = Array.isArray(response.job_requests)
                    ? response.job_requests.filter(function(req) {
                        return !computeExpiryTelemetry(req.expires_at_iso, req.job_end_date_iso).isExpired;
                    })
                    : [];
                window.jobsState = {
                    all: rawJobs,
                    filtered: rawJobs,
                    jobRequests: rawReqs,
                    filteredRequests: rawReqs,
                    total: response.total !== undefined ? response.total : rawJobs.length,
                    requestsTotal: rawReqs.length
                };
                if (rawJobs.length === 0 && rawReqs.length === 0 && response.html && response.html.trim() !== '') {
                    $('#allJobList').html(response.html);
                } else {
                    renderTotalJobsUnifiedStream(rawReqs, rawJobs);
                }
            } else {
                if (response.jobs && response.jobs.length > 0) {
                    window.jobsState = {
                        all: response.jobs,
                        filtered: response.jobs,
                        jobRequests: [],
                        filteredRequests: [],
                        total: response.total
                    };
                    $('#allJobList').html(renderJobsClientSide(response.jobs));
                } else if (response.html) {
                    $('#allJobList').html(response.html);
                } else {
                    window.jobsState = { all: [], filtered: [], jobRequests: [], filteredRequests: [], total: 0 };
                    $('#allJobList').html(
                        '<div class="cd-jobs-empty text-center py-16 px-6 my-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px;">' +
                        '<i class="fa fa-briefcase fa-2x mb-3 text-muted d-block"></i>' +
                        '<div class="fw-bold fs-6 text-gray-700 mb-1">No Job Requisitions Found</div>' +
                        '<div class="text-muted fs-7">No jobs match the current criteria.</div></div>'
                    );
                }
            }
            $('#all_jobs_count').html(response.total !== undefined ? response.total : 0);
            $('#pagination').html(response.pagination || '');

            if (response.status_counts) {
                $('#filterCountAll, .jh-tab[data-status="all"] .jh-badge, .cd-jobs-tab-btn[data-status="all"] .cd-jobs-tab-badge').text(response.status_counts.all !== undefined ? response.status_counts.all : 0);
                $('#filterCountHiring, .jh-tab[data-status="active"] .jh-badge, .cd-jobs-tab-btn[data-status="active"] .cd-jobs-tab-badge').text(response.status_counts.active !== undefined ? response.status_counts.active : 0);
                $('#filterCountHold, .jh-tab[data-status="hold"] .jh-badge, .cd-jobs-tab-btn[data-status="hold"] .cd-jobs-tab-badge').text(response.status_counts.hold !== undefined ? response.status_counts.hold : 0);
                $('#filterCountClosed, .jh-tab[data-status="inactive"] .jh-badge, .cd-jobs-tab-btn[data-status="inactive"] .cd-jobs-tab-badge').text(response.status_counts.inactive !== undefined ? response.status_counts.inactive : 0);
                $('#filterCountRemoved, .jh-tab[data-status="removed"] .jh-badge, .cd-jobs-tab-btn[data-status="removed"] .cd-jobs-tab-badge').text(response.status_counts.removed !== undefined ? response.status_counts.removed : 0);
            }

            syncTopKpiCounts(response.dataCnt);
        }

        async function fetchCandidates(page = page_no, search = '', size = pageSize, options = {}) {
            options = options || {};
            window.latestSearchRequestId = (window.latestSearchRequestId || 0) + 1;
            const thisRequestId = window.latestSearchRequestId;

            const searchVal = ($('.inputJobSearch').val() || $('#inputSearch').val() || search || '').trim();
            const cacheKey = [
                'v6',
                window.currentKpiFilter || 'all',
                $('#status').val() || 'all',
                $('#hiringtype').val() || '',
                $('#employment_type').val() || '',
                $('#experience_filter').val() || '',
                searchVal,
                page,
                size
            ].join('_');

            // Phase 2: Instant Cache Hydration from IndexedDB (<20ms render) unless explicitly bypassed
            let cached = null;
            if (!options.bypassCache) {
                try {
                    cached = await JeroJobsLocalStore.get(cacheKey);
                } catch (err) {}
            }

            if (cached && thisRequestId === window.latestSearchRequestId) {
                applyJobsResponse(cached, page);
            } else {
                $('#jobSearchBtn').prop('disabled', true).addClass('disabled');
                if (searchVal !== '') {
                    $('.loader_search').css({ display: 'block' });
                    $('.page-loader').css('display', 'flex');
                } else {
                    $('.page-loader').css('display', 'flex');
                }
            }

            if (window.currentJobsAjax && typeof window.currentJobsAjax.abort === 'function') {
                window.currentJobsAjax.abort();
            }

            window.currentJobsAjax = $.ajax({
                url: `{!! route('service_company.jobs.all_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  requisition_type: window.currentKpiFilter || 'all',
                  custom_status : $('#status').val(),
                  search : searchVal,
                  custom_hiringtype : $('#hiringtype').val(),
                  employment_type : $('#employment_type').val(),
                  experience_filter : $('#experience_filter').val(),
                  custom_search : searchVal,
                  size : size
                },
                success: function(response) {
                    if (thisRequestId !== window.latestSearchRequestId) {
                        return; // Ignore superseded / stale responses
                    }
                    // Persist response into IndexedDB for instant repeat views
                    if (window.JeroJobsLocalStore) {
                        JeroJobsLocalStore.set(cacheKey, response);
                    }
                    applyJobsResponse(response, page);
                },
                error: function(xhr, status, error) {
                    if (thisRequestId !== window.latestSearchRequestId || status === 'abort') return;
                    window.currentJobsAjax = null;
                    $('#jobSearchBtn').prop('disabled', false).removeClass('disabled');
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');

                    // If we already rendered from IndexedDB cache, do not overwrite with error screen
                    if (cached) return;

                    var msg, icon;
                    if (xhr.status === 0 || !navigator.onLine) {
                        msg  = 'You are currently offline or the server is unreachable. Please check your connection and try again.';
                        icon = 'warning';
                    } else if (xhr.status === 503 || xhr.status === 500) {
                        msg  = 'The search service is temporarily unavailable. Please try again in a moment.';
                        icon = 'error';
                    } else {
                        msg  = 'Could not load jobs (HTTP ' + xhr.status + '). Please refresh the page.';
                        icon = 'error';
                    }

                    $('#allJobList').html(
                        '<div style="text-align:center;padding:40px 20px;font-family:Poppins,sans-serif;">' +
                        '<i class="fa fa-exclamation-triangle" style="font-size:32px;color:#f64e60;margin-bottom:12px;display:block;"></i>' +
                        '<p style="font-size:13px;color:#5e6278;margin:0;">' + msg + '</p>' +
                        '<button onclick="fetchCandidates()" class="btn btn-sm btn-primary mt-4" style="font-size:12px;">Retry</button>' +
                        '</div>'
                    );
                    $('#pagination').html('');
                }
            });

            return window.currentJobsAjax;
        }

    



    var UrlMailJobId = "{{$UrlMailJobId}}";
    if(UrlMailJobId){

      setTimeout(() => {
         $('#kt_activitiesJob_toggle').trigger('click');		
      

        var id    		=	UrlMailJobId;
        var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?viewmore=viewmore";
        url     		=	url.replace(':id', id);
        var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
        $.ajax({
                type  		: 'GET',
                url   		: url,
                beforeSend	: function() {
                                      $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                          },
                success 	: function (response) {
                    $('#kt_activitiesJob_body').html(response.view);	
                }
              });
      }, 200);
       
    }

    $(document).on('click', '.jd-drawer-close-btn, #kt_activitiesJob_close', function(e) {
        e.preventDefault();
        var drawerEl = this.closest('[data-kt-drawer="true"]') || document.getElementById('kt_activitiesJob');
        if (drawerEl) {
            if (typeof KTDrawer !== 'undefined') {
                var inst = KTDrawer.getInstance(drawerEl);
                if (!inst && typeof KTDrawer === 'function') {
                    inst = new KTDrawer(drawerEl);
                }
                if (inst && typeof inst.hide === 'function') {
                    inst.hide();
                    return;
                }
            }
            drawerEl.classList.remove('drawer-on');
            document.querySelectorAll('.drawer-overlay').forEach(function(el) { el.remove(); });
            document.body.removeAttribute('data-kt-drawer-activities');
            document.body.style.overflow = '';
        }
    });


    // Phase 4: Real-time Reverb Delta Synchronization
    function initJobsReverbSync() {
        if (!window.Echo || typeof window.Echo.private !== 'function') {
            return;
        }

        const companyId = {{ (int) (auth()->user()->company_id ?? 0) }};
        if (!companyId) return;

        try {
            window.Echo.private('company.' + companyId)
                .listen('.JobStatusChanged', function(event) {
                    if (!event || !event.jobId) return;

                    const jobId = parseInt(event.jobId, 10);
                    const newStatus = (event.newStatus || '').toLowerCase();

                    // 1. Purge local IndexedDB and drawer caches so fresh data is guaranteed
                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }
                    window.jobDrawerCache = {};

                    // 2. Instant Delta Patch in Browser RAM & DOM
                    if (window.jobsState && window.jobsState.all) {
                        let targetJob = window.jobsState.all.find(j => parseInt(j.id, 10) === jobId);
                        if (targetJob) {
                            targetJob.status_raw = newStatus;
                            targetJob.status = (newStatus === 'active') ? 'Hiring' : ((newStatus === 'hold') ? 'Hold' : 'Closed');
                            
                            // Re-render currently visible filtered list in RAM (<2ms) preserving Total Jobs unified view
                            if ((!window.currentKpiFilter || window.currentKpiFilter === 'all') && typeof renderTotalJobsUnifiedStream === 'function') {
                                renderTotalJobsUnifiedStream(window.jobsState.filteredRequests || window.jobsState.jobRequests || [], window.jobsState.filtered || []);
                            } else {
                                $('#allJobList').html(renderJobsClientSide(window.jobsState.filtered));
                            }
                        }
                    }

                    // 3. Update status counters if supplied
                    if (event.status_counts) {
                        $('#filterCountAll, .jh-tab[data-status="all"] .jh-badge, .cd-jobs-tab-btn[data-status="all"] .cd-jobs-tab-badge').text(event.status_counts.all || 0);
                        $('#filterCountHiring, .jh-tab[data-status="active"] .jh-badge, .cd-jobs-tab-btn[data-status="active"] .cd-jobs-tab-badge').text(event.status_counts.active || 0);
                        $('#filterCountHold, .jh-tab[data-status="hold"] .jh-badge, .cd-jobs-tab-btn[data-status="hold"] .cd-jobs-tab-badge').text(event.status_counts.hold || 0);
                        $('#filterCountClosed, .jh-tab[data-status="inactive"] .jh-badge, .cd-jobs-tab-btn[data-status="inactive"] .cd-jobs-tab-badge').text(event.status_counts.inactive || 0);
                        $('#filterCountRemoved, .jh-tab[data-status="removed"] .jh-badge, .cd-jobs-tab-btn[data-status="removed"] .cd-jobs-tab-badge').text(event.status_counts.removed || 0);
                    }

                    // 4. Show non-intrusive notification if toastr is loaded
                    if (typeof showToastr === 'function' && event.jobTitle) {
                        showToastr('info', 'Job "' + event.jobTitle + '" status changed to ' + event.newStatus, 'Job Status Updated');
                    }
                })
                .listen('.candidate.assigned', function(event) {
                    if (!event || !event.jobId) return;
                    const jobId = parseInt(event.jobId, 10);

                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }

                    // Direct DOM patch of pipeline badge on the job card (0ms latency)
                    const $jobCard = $('.cd-job-card[data-job-id="' + jobId + '"], .job-row[data-job-id="' + jobId + '"]');
                    if ($jobCard.length > 0) {
                        const stageId = parseInt(event.masterStatusId, 10);
                        if (stageId) {
                            const $badge = $jobCard.find('.cd-track-badge[href*="/' + stageId + '"], .stage-badge a[href*="/' + stageId + '"]');
                            if ($badge.length > 0) {
                                const currentCount = parseInt($badge.text().trim(), 10) || 0;
                                $badge.text(currentCount + 1);
                            }
                        }
                    }
                })
                .listen('.CandidateDrawerUpdated', function(event) {
                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }
                    window.jobDrawerCache = {};
                })
                .listen('.JobSkillsUpdated', function(event) {
                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }
                    window.jobDrawerCache = {};
                });

            // Reconnection recovery: purge cache to ensure re-sync with server authoritative state
            if (window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                window.Echo.connector.pusher.connection.bind('connected', function() {
                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }
                });
            }
        } catch (err) {
            console.warn('[Reverb Jobs] sync error:', err);
        }
    }

    $(document).ready(function() {
        initJobsReverbSync();
    });

</script>

@endpush
