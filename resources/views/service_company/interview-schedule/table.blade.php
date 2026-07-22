@extends('layouts.service_company_layout')
@push('head-script')

<style>

@media  only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr {width: max-content!important;}
  .table > :not(caption) > * > * {padding: 0rem;}
  td:nth-child(n+2),
  th:nth-child(n+2) {width: revert!important; }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #client_stage_sub_stage {width: 1025px;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_client_activitiesApplication {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #client_stage_sub_stage {width: 1200px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_client_activitiesApplication {width: 1200px;}
}

/* ===== Interview Tracker New Design ===== */
:root {
    --it-primary: #2563eb;
    --it-primary-light: #eff6ff;
    --it-primary-dark: #1d4ed8;
    --it-success: #10b981;
    --it-success-light: #f0fdf4;
    --it-warning: #f59e0b;
    --it-warning-light: #fffbeb;
    --it-danger: #ef4444;
    --it-danger-light: #fef2f2;
    --it-purple: #8b5cf6;
    --it-purple-light: #f5f3ff;
    --it-orange: #f97316;
    --it-orange-light: #fff7ed;
    --it-text-main: #1e293b;
    --it-text-muted: #64748b;
    --it-text-light: #94a3b8;
    --it-border: #e2e8f0;
    --it-border-light: #f1f5f9;
    --it-bg-main: #f8fafc;
    --it-bg-card: #ffffff;
    --it-radius: 6px;
    --it-radius-lg: 10px;
    --it-radius-xl: 12px;
    --it-shadow: 0 1px 3px rgba(0,0,0,0.08);
    --it-shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
    --it-shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
    --it-transition: all 0.2s ease;
}

.it-dashboard { font-family: 'Poppins', sans-serif; background: var(--it-bg-main); color: var(--it-text-main); line-height: 1.45; -webkit-font-smoothing: antialiased; }
.it-dashboard ::-webkit-scrollbar { width: 5px; height: 5px; }
.it-dashboard ::-webkit-scrollbar-track { background: transparent; }
.it-dashboard ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.it-dashboard ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* Stats Grid */
.it-stats-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; margin-bottom: 14px; }
.it-stat-card { background: white; border-radius: var(--it-radius-xl); padding: 12px 14px; box-shadow: var(--it-shadow); border: 1px solid var(--it-border-light); transition: var(--it-transition); cursor: pointer; min-width: 0; display: flex; align-items: center; gap: 10px; }
.it-stat-card:hover { transform: translateY(-2px); box-shadow: var(--it-shadow-md); }
.it-stat-icon-wrap { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.it-stat-icon-wrap svg { width: 18px; height: 18px; }
.it-stat-icon-blue { background: #dbeafe; color: #2563eb; }
.it-stat-icon-yellow { background: #fef3c7; color: #d97706; }
.it-stat-icon-purple { background: #f3e8ff; color: #7c3aed; }
.it-stat-icon-green { background: #d1fae5; color: #059669; }
.it-stat-icon-red { background: #fee2e2; color: #dc2626; }
.it-stat-icon-sky { background: #e0f2fe; color: #0284c7; }
.it-stat-body { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.it-stat-label { font-size: 10px; color: var(--it-text-muted); font-weight: 500; line-height: 1.2; }
.it-stat-value-row { display: flex; align-items: center; gap: 5px; line-height: 1; }
.it-stat-value { font-size: 16px; font-weight: 700; color: var(--it-text-main); letter-spacing: -0.3px; }
.it-stat-trend { font-size: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 1px; }
.it-stat-trend svg { width: 10px; height: 10px; }
.it-trend-up { color: var(--it-success); }
.it-trend-down { color: var(--it-danger); }

/* Filters Bar */
.it-filters-bar { background: white; border-radius: var(--it-radius-xl); padding: 8px 12px; display: flex; gap: 8px; align-items: center; margin-bottom: 12px; box-shadow: var(--it-shadow); border: 1px solid var(--it-border-light); flex-wrap: wrap; }
.it-search-box { flex: 0 0 300px !important; width: 300px !important; max-width: 300px !important; min-width: 180px; position: relative; }
.it-search-box input { width: 100%; padding: 6px 10px 6px 30px; border: 1px solid var(--it-border); border-radius: var(--it-radius); font-size: 11px; font-family: 'Poppins', sans-serif; color: var(--it-text-main); outline: none; transition: var(--it-transition); background: var(--it-bg-main); height: 32px; }
.it-search-box input:focus { border-color: var(--it-primary); background: white; box-shadow: 0 0 0 3px var(--it-primary-light); }
.it-search-box svg { position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: var(--it-text-light); pointer-events: none; width: 14px; height: 14px; }
.it-filter-dropdown { position: relative; min-width: 100px; }
.it-filter-trigger { display: flex; align-items: center; justify-content: space-between; gap: 6px; padding: 6px 10px; border: 1px solid var(--it-border); border-radius: var(--it-radius); font-size: 11px; font-family: 'Poppins', sans-serif; color: var(--it-text-main); background: white; cursor: pointer; transition: var(--it-transition); white-space: nowrap; width: 100%; height: 32px; }
.it-filter-trigger:hover { border-color: var(--it-text-light); }
.it-filter-trigger .it-label { color: var(--it-text-muted); font-size: 10px; margin-right: 3px; }
.it-filter-trigger .it-value { font-weight: 500; }
.it-filter-trigger svg { width: 12px; height: 12px; }
.it-filter-menu { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: white; border: 1px solid var(--it-border); border-radius: var(--it-radius-lg); box-shadow: var(--it-shadow-lg); z-index: 60; display: none; overflow: hidden; max-height: 220px; overflow-y: auto; }
.it-filter-menu.open { display: block; }
.it-filter-menu-item { padding: 7px 10px; font-size: 11px; cursor: pointer; transition: var(--it-transition); display: flex; align-items: center; gap: 6px; }
.it-filter-menu-item:hover { background: var(--it-bg-main); }
.it-filter-menu-item.selected { background: var(--it-primary-light); color: var(--it-primary); font-weight: 500; }
.it-filter-actions { display: flex; gap: 6px; margin-left: auto; }
.it-btn-filter { background: white; border: 1px solid var(--it-border); color: var(--it-text-main); padding: 6px 12px; height: 32px; display: inline-flex; align-items: center; gap: 5px; border-radius: var(--it-radius); font-size: 11px; font-weight: 500; font-family: 'Poppins', sans-serif; cursor: pointer; transition: var(--it-transition); }
.it-btn-filter:hover { background: var(--it-bg-main); }
.it-btn-filter svg { width: 12px; height: 12px; }
.it-btn-reset { background: transparent; border: none; color: var(--it-primary); font-weight: 500; padding: 6px 8px; height: 32px; display: inline-flex; align-items: center; gap: 5px; border-radius: var(--it-radius); font-size: 11px; font-family: 'Poppins', sans-serif; cursor: pointer; transition: var(--it-transition); }
.it-btn-reset:hover { background: var(--it-primary-light); }

/* Tabs */
.it-tabs-nav { display: flex; gap: 3px; margin-bottom: 12px; flex-wrap: wrap; }
.it-tab-btn { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: var(--it-radius-lg); font-size: 11px; font-weight: 500; font-family: 'Poppins', sans-serif; border: none; background: transparent; color: var(--it-text-muted); cursor: pointer; transition: var(--it-transition); white-space: nowrap; height: 32px; }
.it-tab-btn:hover { background: rgba(37,99,235,0.06); color: var(--it-primary); }
.it-tab-btn.active { background: var(--it-primary); color: white; box-shadow: 0 2px 6px rgba(37,99,235,0.25); }
.it-tab-count { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 16px; padding: 0 5px; border-radius: 999px; font-size: 10px; font-weight: 600; background: rgba(0,0,0,0.06); color: currentColor; }
.it-tab-btn.active .it-tab-count { background: rgba(255,255,255,0.2); }

/* Table Container */
.it-table-container { background: white; border-radius: var(--it-radius-xl); box-shadow: var(--it-shadow); border: 1px solid var(--it-border-light); overflow: hidden; }
.it-table-scroll { overflow-x: auto; }
.it-data-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 1100px; }
.it-data-table th { background: #f8fafc; padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--it-text-muted); font-weight: 600; border-bottom: 1px solid var(--it-border); white-space: nowrap; position: sticky; top: 0; z-index: 5; line-height: 1.4; }
.it-data-table th:first-child { border-radius: var(--it-radius-xl) 0 0 0; padding-left: 14px; width: 36px; }
.it-data-table th:last-child { border-radius: 0 var(--it-radius-xl) 0 0; padding-right: 14px; }
.it-data-table td { padding: 10px 10px; border-bottom: 1px solid var(--it-border-light); font-size: 11px; vertical-align: top; transition: background 0.15s; }
.it-data-table tbody tr:hover td { background: #f8fafc; }
.it-data-table tbody tr:last-child td { border-bottom: none; }
.it-data-table td:first-child { padding-left: 14px; }
.it-data-table td:last-child { padding-right: 14px; }

/* Checkbox */
.it-checkbox { width: 14px; height: 14px; border: 2px solid var(--it-border); border-radius: 3px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: var(--it-transition); flex-shrink: 0; background: white; }
.it-checkbox:hover { border-color: var(--it-primary); }
.it-checkbox.checked { background: var(--it-primary); border-color: var(--it-primary); }
.it-checkbox.checked svg { display: block; }
.it-checkbox svg { display: none; color: white; width: 10px; height: 10px; }

/* Candidate Cell */
.it-candidate-cell { display: flex; gap: 8px; }
.it-avatar { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; font-weight: 600; flex-shrink: 0; text-transform: uppercase; }
.it-avatar-green { background: #10b981; }
.it-avatar-red { background: #ef4444; }
.it-avatar-blue { background: #3b82f6; }
.it-avatar-purple { background: #8b5cf6; }
.it-avatar-orange { background: #f97316; }
.it-avatar-gray { background: #6b7280; }
.it-candidate-info { min-width: 0; }
.it-candidate-name { font-weight: 600; font-size: 11px; color: var(--it-text-main); margin-bottom: 1px; display: flex; align-items: center; gap: 5px; }
.it-candidate-role { font-size: 10px; color: var(--it-text-muted); margin-bottom: 3px; }
.it-candidate-meta { display: flex; flex-direction: column; gap: 2px; }
.it-meta-tag { display: inline-flex; align-items: center; gap: 3px; font-size: 10px; font-weight: 500; }
.it-meta-tag.confirmed { color: var(--it-success); }
.it-meta-tag.warning { color: var(--it-warning); }
.it-meta-tag.danger { color: var(--it-danger); }
.it-meta-tag.info { color: var(--it-text-muted); }
.it-meta-tag svg { width: 10px; height: 10px; }
.it-contact-btn { width: 20px; height: 20px; border-radius: 4px; display: flex; align-items: center; justify-content: center; border: none; background: transparent; color: var(--it-text-light); cursor: pointer; transition: var(--it-transition); padding: 0; flex-shrink: 0; }
.it-contact-btn:hover { background: var(--it-primary-light); color: var(--it-primary); }
.it-contact-btn svg { width: 12px; height: 12px; }

/* Job Cell */
.it-job-title { font-weight: 600; font-size: 11px; color: var(--it-text-main); margin-bottom: 1px; }
.it-job-company { font-size: 10px; color: var(--it-text-muted); margin-bottom: 4px; }
.it-job-tags { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }
.it-job-type { display: inline-flex; align-items: center; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600; background: var(--it-primary-light); color: var(--it-primary); }
.it-job-id { font-size: 10px; color: var(--it-primary); font-weight: 500; text-decoration: none; }
.it-job-id:hover { text-decoration: underline; }
.it-dot-separator { width: 3px; height: 3px; border-radius: 50%; background: var(--it-border); }

/* Date Cell */
.it-date-cell { display: flex; flex-direction: column; gap: 3px; }
.it-date-row { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--it-text-main); font-weight: 500; }
.it-date-row svg { color: var(--it-text-light); flex-shrink: 0; width: 12px; height: 12px; }
.it-date-row .it-mode { color: var(--it-text-muted); font-size: 10px; font-weight: 400; }

/* Person Cell */
.it-person-cell { display: flex; flex-direction: column; gap: 2px; }
.it-person-name { font-weight: 600; font-size: 11px; color: var(--it-text-main); }
.it-person-company { font-size: 10px; color: var(--it-text-muted); }
.it-person-extra { display: flex; align-items: center; gap: 5px; margin-top: 2px; }
.it-extra-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 16px; padding: 0 4px; background: var(--it-bg-main); border: 1px solid var(--it-border); border-radius: 4px; font-size: 9px; font-weight: 600; color: var(--it-text-muted); }
.it-extra-text { font-size: 10px; color: var(--it-text-muted); }

/* Stage Cell */
.it-stage-cell { display: flex; flex-direction: column; gap: 3px; }
.it-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 500; width: fit-content; }
.it-badge-blue { background: #dbeafe; color: #1e40af; }
.it-badge-purple { background: #f3e8ff; color: #6b21a8; }
.it-badge-orange { background: #ffedd5; color: #9a3412; }
.it-badge-red { background: #fee2e2; color: #991b1b; }
.it-badge-green { background: #d1fae5; color: #065f46; }
.it-badge-yellow { background: #fef9c3; color: #854d0e; }
.it-stage-status { display: inline-flex; align-items: center; gap: 3px; font-size: 10px; font-weight: 500; }
.it-stage-status.confirmed { color: var(--it-success); }
.it-stage-status.pending { color: var(--it-warning); }
.it-stage-status svg { width: 10px; height: 10px; }

/* Feedback Cell */
.it-feedback-cell { display: flex; flex-direction: column; gap: 4px; }
.it-feedback-rating { display: flex; align-items: center; gap: 3px; font-size: 11px; font-weight: 600; color: var(--it-warning); }
.it-feedback-rating svg { width: 12px; height: 12px; fill: currentColor; }
.it-feedback-btn { width: fit-content; padding: 4px 10px; border-radius: var(--it-radius); font-size: 10px; font-weight: 600; font-family: 'Poppins', sans-serif; cursor: pointer; transition: var(--it-transition); display: inline-flex; align-items: center; gap: 3px; border: 1px solid transparent; }
.it-feedback-btn-outline-orange { border-color: var(--it-orange); color: var(--it-orange); background: transparent; }
.it-feedback-btn-outline-orange:hover { background: var(--it-orange-light); }
.it-feedback-btn-outline-green { border-color: var(--it-success); color: var(--it-success); background: transparent; }
.it-feedback-btn-outline-green:hover { background: var(--it-success-light); }
.it-feedback-btn-solid-blue { background: var(--it-primary); color: white; border-color: var(--it-primary); }
.it-feedback-btn-solid-blue:hover { background: var(--it-primary-dark); }

/* Table Footer */
.it-table-footer { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-top: 1px solid var(--it-border-light); font-size: 10px; color: var(--it-text-muted); }
.it-pagination { display: flex; align-items: center; gap: 3px; }
.it-page-btn { min-width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: var(--it-radius); border: none; background: transparent; color: var(--it-text-muted); font-size: 11px; font-family: 'Poppins', sans-serif; font-weight: 500; cursor: pointer; transition: var(--it-transition); padding: 0 6px; }
.it-page-btn:hover { background: var(--it-bg-main); color: var(--it-text-main); }
.it-page-btn.active { background: var(--it-primary); color: white; }
.it-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.it-page-btn svg { width: 14px; height: 14px; }
.it-page-ellipsis { padding: 0 3px; color: var(--it-text-light); }
.it-rows-per-page { display: flex; align-items: center; gap: 6px; }
.it-rows-dropdown { padding: 3px 6px; border: 1px solid var(--it-border); border-radius: var(--it-radius); font-size: 11px; font-family: 'Poppins', sans-serif; color: var(--it-text-main); background: white; cursor: pointer; outline: none; }

/* Responsive */
@media (max-width: 1440px) {
    .it-stats-grid { grid-template-columns: repeat(6, 1fr); gap: 10px; }
    .it-filters-bar { gap: 6px; padding: 6px 10px; }
    .it-search-box { max-width: 220px; }
}
@media (max-width: 1280px) {
    .it-stats-grid { grid-template-columns: repeat(6, 1fr); gap: 8px; }
}
@media (max-width: 1150px) {
    .it-stats-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
    .it-stat-card { padding: 12px 14px; }
    .it-filters-bar { flex-wrap: wrap; }
    .it-search-box { min-width: 160px; flex: 0 1 280px; max-width: 280px; }
    .it-filter-dropdown { min-width: 90px; }
    .it-filter-actions { margin-left: 0; width: 100%; justify-content: flex-end; margin-top: 4px; }
}
@media (max-width: 768px) {
    .it-stats-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    .it-filters-bar { flex-direction: column; align-items: stretch; gap: 6px; }
    .it-search-box { max-width: none; min-width: auto; width: 100%; }
    .it-filter-dropdown { width: 100%; }
    .it-filter-actions { margin-left: 0; justify-content: flex-end; margin-top: 0; }
    .it-tabs-nav { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px; gap: 2px; }
    .it-tab-btn { flex-shrink: 0; }
    .it-table-footer { flex-direction: column; gap: 8px; align-items: flex-start; }
}
@media (max-width: 480px) {
    .it-stats-grid { grid-template-columns: 1fr 1fr; }
    .it-table-footer { font-size: 9px; }
    .it-rows-per-page { display: none; }
}

/* Loading */
.it-loading { display: none; padding: 30px; text-align: center; color: var(--it-text-muted); font-size: 12px; }
.it-loading.show { display: block; }

/* Old table compatibility */
#Interview-trackers { display: contents; }

/* Hide the duplicate "Showing X to Y of Z" text that comes from the
   pagination partial — the #showingText div already provides this. */
#pagination .pagi-info { display: none !important; }

/* Follow Up Dropdown */
.it-followup-dropdown { position: relative; display: inline-block; margin-top: 2px; }
.it-followup-btn { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: var(--it-radius); font-size: 10px; font-weight: 500; font-family: 'Poppins', sans-serif; border: 1px solid var(--it-border); background: white; color: var(--it-text-muted); cursor: pointer; transition: var(--it-transition); }
.it-followup-btn:hover { background: var(--it-bg-main); color: var(--it-primary); border-color: var(--it-primary); }
.it-followup-btn svg { width: 10px; height: 10px; }
.it-followup-menu { position: absolute; top: calc(100% + 4px); left: 0; background: white; border: 1px solid var(--it-border); border-radius: var(--it-radius-lg); box-shadow: var(--it-shadow-lg); z-index: 50; display: none; min-width: 150px; overflow: hidden; }
.it-followup-menu.open { display: block; }
.it-followup-item { padding: 7px 10px; font-size: 10px; cursor: pointer; transition: var(--it-transition); display: flex; align-items: center; gap: 6px; color: var(--it-text-main); }
.it-followup-item:hover { background: var(--it-bg-main); color: var(--it-primary); }
.it-followup-item svg { width: 10px; height: 10px; }

/* Tab counts update */
.it-tab-btn { position: relative; }

</style>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid py-4" id="kt_content">
  <!--begin::Toolbar-->
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
        <div class="d-flex flex-column">
          <h5 class="d-flex align-items-center text-dark fw-bolder">Interview Tracker</h5>
        </div>
      </div>
      <div class="d-flex align-items-center py-1">
        <div class="d-flex align-items-center position-relative my-1 me-3">
          <input type="hidden" name="inputSearch" id="inputSearch" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
        </div>
        <div class="card-toolbar">
          <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base"></div>
          <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
            <div class="fw-bolder me-5"> <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
            <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--end::Toolbar-->

  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">

@if(in_array("view_schedule", $userPermissions))

      <!-- ===== New Interview Tracker Design ===== -->
      <div class="it-dashboard" id="interviewTrackerDashboard">

        <!-- Stats Grid -->
        <div class="it-stats-grid" id="statsGrid" style="grid-template-columns: repeat(4, 1fr);">
          <div class="it-stat-card">
            <div class="it-stat-icon-wrap it-stat-icon-blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="it-stat-body">
              <div class="it-stat-label">Interview This Month <span style="color:var(--it-primary);font-weight:600;">({{ now()->format('M Y') }})</span></div>
              <div class="it-stat-value-row">
                <div class="it-stat-value" id="stat-interview-month">0</div>
                <div class="it-stat-trend it-trend-up">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                  +12%
                </div>
              </div>
            </div>
          </div>
          <div class="it-stat-card">
            <div class="it-stat-icon-wrap it-stat-icon-green">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
            </div>
            <div class="it-stat-body">
              <div class="it-stat-label">Today's Interview <span style="color:var(--it-success);font-weight:600;">({{ now()->format('d M') }})</span></div>
              <div class="it-stat-value-row">
                <div class="it-stat-value" id="stat-today">0</div>
                <div class="it-stat-trend it-trend-up">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                  +20%
                </div>
              </div>
            </div>
          </div>
          <div class="it-stat-card">
            <div class="it-stat-icon-wrap it-stat-icon-purple">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="it-stat-body">
              <div class="it-stat-label">Upcomings Interview</div>
              <div class="it-stat-value-row">
                <div class="it-stat-value" id="stat-upcoming">0</div>
                <div class="it-stat-trend it-trend-up">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                  +5%
                </div>
              </div>
            </div>
          </div>
          <div class="it-stat-card">
            <div class="it-stat-icon-wrap it-stat-icon-red">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="it-stat-body">
              <div class="it-stat-label">Feedback Pending</div>
              <div class="it-stat-value-row">
                <div class="it-stat-value" id="stat-feedback-pending">0</div>
                <div class="it-stat-trend it-trend-down">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                  -8%
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters Bar -->
        <div class="it-filters-bar">
          <div class="it-search-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" id="kt_tagify_2" placeholder="Search candidate, job, recruiter..." autocomplete="off">
          </div>
          <div style="position: absolute; top: 0px; right: 36px;">
            <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 8px;"></div>
          </div>
          <input type="hidden" id="sortingfilter" value="">
          <div class="it-filter-dropdown" style="min-width: 200px;">
            <button class="it-filter-trigger" onclick="toggleFilterMenu(this)" type="button">
              <span class="it-label">Sort</span>
              <span class="it-value" id="filter-sort-label">Newest Interviews</span>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="it-filter-menu" id="filter-menu-sort">
              <div class="it-filter-menu-item selected" onclick="selectSort(this, '')">Newest Interviews</div>
              <div class="it-filter-menu-item" onclick="selectSort(this, '1')">Today's Interview ({{ now()->format('d M Y') }})</div>
              <div class="it-filter-menu-item" onclick="selectSort(this, 'Feedback Pending')">Feedback Pending</div>
              <div class="it-filter-menu-item" onclick="selectSort(this, 'Interview Pending')">Upcoming Interviews</div>
            </div>
          </div>
          <div class="it-filter-actions">
            <button class="it-btn-reset" type="button" onclick="resetFilters()">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
              Reset
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="it-table-container">
          <div class="it-table-scroll">
            <table class="it-data-table" id="myTable">
              <thead>
                <tr>
                  <th>
                    <div class="it-checkbox" id="selectAllCheckbox" onclick="toggleSelectAll()">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                      </svg>
                    </div>
                  </th>
                  <th style="width:200px">Candidate</th>
                  <th style="width:200px">Job Requisitions</th>
                  <th style="width:160px">Interview Date &amp; Time</th>
                  <th style="width:140px">Interviewers</th>
                  <th style="width:120px">Source</th>
                  <th style="width:100px">Stage</th>
                  <th style="width:150px">Feedback</th>
                </tr>
              </thead>
              <tbody class="text-gray-600 fw-bold" id="Interview-trackers">
              </tbody>
            </table>
          </div>
          <div class="it-loading" id="tableLoading">Loading interviews...</div>
          <div class="it-table-footer">
            <div id="showingText">Loading...</div>
            <div style="display:flex;align-items:center;gap:14px">
              <div class="it-pagination" id="pagination"></div>
            </div>
          </div>
        </div>

      </div>
      <!-- ===== End New Design ===== -->

@else
      <div class="it-table-container" style="margin-top:20px">
        <table class="it-data-table" id="no-access">
          <thead>
            <tr>
              <th style="text-align:center">ACCESS DENIED CONTACT ADMINISTRATOR</th>
            </tr>
          </thead>
        </table>
      </div>
@endif

    </div>
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->

<!--begin::Modal - Interview Feedback-->
<div href="javascript:void(0);" id="intervied_feedback_toggle" class="text-dark fw-bolder text-hover-primary fs-6" data-bs-toggle="modal" data-bs-target="#interview_feedback"></div>
<div class="modal fade" id="interview_feedback" tabindex="-1" aria-hidden="true" >
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

<!--begin::JD / Resume overlay panel (self-contained fixed overlay — cannot push the modal) -->
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
  /* Read-only here: hide management actions and the fragment's own close button (the extra X) */
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

<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>
$(document).ready(function() {
    $('#kt_tagify_2').keyup(function() {
        fetchCandidates();
    });

    $('#sortingfilter').change(function() {
        fetchCandidates();
    });
});

var table;

function loadTable(){
    var startDate = $('#start-date').val();
    if (startDate == '') { startDate = null; }
    var endDate = $('#end-date').val();
    if (endDate == '') { endDate = null; }
    startDate = null;
    endDate = null;
    var status = $('#status').val();
    var applicationID = $('#applicationID').val();
    var inputSearch = $('#inputSearch').val();
    var sortingfilter = $('#sortingfilter').val();
    var country = $('#country').val();
    var cus_search = $('#kt_tagify_2').val();

    table = $('#myTable').dataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        destroy: true,
        ordering: false,
        "bLengthChange": false,
        sDom: 'lrtip',
        oLanguage: { sLengthMenu: "_MENU_",},
        ajax: {'url' : '{!! route('service_company.interview-schedule.data') !!}', "data": function ( d ) {
            d.search = $('#inputSearch').val();
            d.sortingfilter = $('#sortingfilter').val();
            d.country = $('#country').val();
            d.cus_search = $('#kt_tagify_2').val();
        }},
        "fnDrawCallback": function( settings, start, end, max, total, pre ) {
            var total_record = this.fnSettings().fnRecordsTotal();
            $('#total_record').html(total_record);
            $("body").tooltip({ selector: '[data-toggle="tooltip"]' });
        },
        "columnDefs": [
            {"targets": 0 },
            {"targets": 1, "class":"align-items-center" },
            {"targets": 2 },
            { "targets": 3 },
            { "targets": 4 },
            { "targets": 5 },
            { "targets": 6 },
            { "targets": 7 }
        ],
        columns: [
            { data: 'chkbox', name:'chkbox',width: '1%', searchable : false, orderable: false},
            { data: 'Name', name: 'Name',width: '15%', searchable : false, orderable: false},
            { data: 'Requisitions', name: 'Requisitions',width: '10%', searchable : false, orderable: false},
            { data: 'InterviewDate', name: 'InterviewDate',width: '10%', searchable : false, orderable: false},
            { data: 'Interviewers', name: 'Interviewers',width: '10%', searchable : false, orderable: false},
            { data: 'Source', name: 'Source',width: '10%', searchable : false, orderable: false},
            { data: 'Feedback', name: 'Feedback',width: '10%', searchable : false, orderable: false},
            { data: 'Stage', name: 'Stage',width: '10%', searchable : false, orderable: false}
        ],
        async: false
    });
    new $.fn.dataTable.FixedHeader( table );
}

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

$(document).on('click', '.show-feedback', async function (e) {
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
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function() {
            $('#load_interview_feedback').html('<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:50px 20px;gap:12px;"><div style="width:36px;height:36px;border:3px solid #e2e8f0;border-top-color:#2563eb;border-radius:50%;animation:fbFastSpin .5s linear infinite;"></div><div style="color:#64748b;font-size:13px;font-weight:500;">Loading feedback…</div></div><style>@keyframes fbFastSpin{to{transform:rotate(360deg)}}</style>');
        },
        success: function (response) {
            if (response.status == "success") {
                $('#load_interview_feedback').html(response.view);
                if(response.download_show >=1){
                    $('.urlchange').removeClass('d-none')
                }
            }
        }
    });
});

// Reset the feedback modal on close so it always re-loads fresh next time
// (fixes: had to refresh the page before the popup would open again, because the
//  injected full-screen overlay stayed in the DOM after closing).
$(document).on('hidden.bs.modal', '#interview_feedback', function () {
    $('#load_interview_feedback').html('&nbsp;');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
});

$(document).on('click', '#app_interview_schedule_reschedule', function(){
    $("#interview_feedback .close").click();
    var id = $(this).attr('rel');
    var url = "{{ route('service_company.job-applications.create-schedule',':id') }}?reschedule=1";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function() {
            $('#app_interview_schedule_content').html('<img src="'+baseurl+'" height="35px" width="100%">');
        },
        success: function (response) {
            $('#app_interview_schedule_content').html(response.view);
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

// ── Pagination state ────────────────────────────────────────────────────────
var itCurrentPage = 1;
var itCurrentSize = 10;

$(document).ready(function() {
    fetchCandidates(1);

    // Delegate clicks to both page-number buttons and prev/next arrows rendered
    // by the pagination.blade.php partial (class .pagi-page and .pagi-arrow).
    $(document).on('click', '#pagination [data-page]', function(e) {
        e.preventDefault();
        var page = parseInt($(this).data('page'), 10);
        if (!page || page < 1 || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
        itCurrentPage = page;
        fetchCandidates(itCurrentPage);
    });

    // "Rows per page" select rendered inside #pagination by the partial.
    $(document).on('change', '#pagination [data-perpage]', function() {
        itCurrentSize = parseInt($(this).val(), 10) || 10;
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

// ===== New Design Helper Functions =====

let itSelectedIds = new Set();

function toggleSelectAll() {
    const checkbox = document.getElementById('selectAllCheckbox');
    const isChecked = checkbox.classList.contains('checked');
    if (isChecked) {
        checkbox.classList.remove('checked');
        document.querySelectorAll('.it-checkbox[data-row-check]').forEach(cb => cb.classList.remove('checked'));
        itSelectedIds.clear();
    } else {
        checkbox.classList.add('checked');
        document.querySelectorAll('.it-checkbox[data-row-check]').forEach(cb => cb.classList.add('checked'));
    }
}

function toggleRowSelect(el, id) {
    el.classList.toggle('checked');
    if (el.classList.contains('checked')) {
        itSelectedIds.add(id);
    } else {
        itSelectedIds.delete(id);
    }
}

// ===== New Design Interactive Functions =====

let activeTab = 'all';
let filterValues = { stage: 'All', status: 'All', owner: 'All', vendor: 'All', mode: 'All' };

function toggleFilterMenu(btn) {
    const menu = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');
    document.querySelectorAll('.it-filter-menu.open').forEach(m => m.classList.remove('open'));
    if (!isOpen) menu.classList.add('open');
}

function selectFilter(el, type, value) {
    const menu = el.closest('.it-filter-menu');
    const trigger = menu.previousElementSibling;
    const labelSpan = trigger.querySelector('.it-value');

    menu.querySelectorAll('.it-filter-menu-item').forEach(item => item.classList.remove('selected'));
    el.classList.add('selected');
    labelSpan.textContent = value;
    menu.classList.remove('open');

    filterValues[type] = value;
}

function filterByTab(tab) {
    activeTab = tab;
    document.querySelectorAll('.it-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector('.it-tab-btn[data-tab="' + tab + '"]').classList.add('active');
    fetchCandidates(1, $('#kt_tagify_2').val());
}

function applyFilters() {
    fetchCandidates(1, $('#kt_tagify_2').val());
}

// Sort selection -> drives custom_hiringtype (Newest / Today / Feedback Pending / Upcoming) and reloads
function selectSort(el, value) {
    var menu = el.closest('.it-filter-menu');
    var trigger = menu ? menu.previousElementSibling : null;
    menu.querySelectorAll('.it-filter-menu-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');
    if (trigger) {
        var lbl = trigger.querySelector('.it-value');
        if (lbl) lbl.textContent = el.textContent.trim();
    }
    menu.classList.remove('open');
    $('#sortingfilter').val(value);
    activeTab = 'all';
    fetchCandidates(1, $('#kt_tagify_2').val());
}

function resetFilters() {
    $('#sortingfilter').val('');
    var sortMenu = document.getElementById('filter-menu-sort');
    if (sortMenu) {
        sortMenu.querySelectorAll('.it-filter-menu-item').forEach(function (item, idx) {
            item.classList.toggle('selected', idx === 0);
        });
    }
    var sortLabel = document.getElementById('filter-sort-label');
    if (sortLabel) sortLabel.textContent = 'Newest Interviews';
    activeTab = 'all';
    $('#kt_tagify_2').val('');
    fetchCandidates(1, '');
}

function openDatePicker() {
    // Placeholder for date picker functionality
    alert('Date picker functionality to be implemented');
}

function toggleFollowUp(btn) {
    const menu = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');
    document.querySelectorAll('.it-followup-menu.open').forEach(m => m.classList.remove('open'));
    if (!isOpen) menu.classList.add('open');
}

function setFollowUpStatus(el, id, status) {
    document.querySelectorAll('.it-followup-menu.open').forEach(m => m.classList.remove('open'));
    var dropdown = el.closest('.it-followup-dropdown');
    var trigger = dropdown ? dropdown.querySelector('.it-followup-trigger') : null;
    var labels = { 'confirmed': 'Confirmed', 'no-response': 'No Response', 'rescheduled': 'Rescheduled' };
    var colors = { 'confirmed': 'var(--it-success)', 'no-response': 'var(--it-danger)', 'rescheduled': 'var(--it-primary)' };

    $.ajax({
        url: '{!! route('service_company.interview-schedule.followup_update') !!}',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', id: id, status: status },
        success: function (resp) {
            if (resp && resp.status === 'success') {
                // Update the control inline (best-effort; not required for success)
                if (trigger) {
                    var lbl = trigger.querySelector('.fu-label');
                    if (lbl) lbl.textContent = labels[status] || 'Follow Up';
                    trigger.style.color = colors[status] || '';
                    trigger.style.borderColor = colors[status] || '';
                    // Once a status is chosen, drop the "Follow Up" CTA icon so it reads as a status
                    var ctaIcon = trigger.querySelector('.fu-cta-icon');
                    if (ctaIcon) ctaIcon.remove();
                }
            } else {
                Swal.fire({ text: (resp && resp.msg) ? resp.msg : 'Could not update follow-up status.', icon: 'error', confirmButtonText: 'Ok' });
            }
        },
        error: function () {
            Swal.fire({ text: 'Could not update follow-up status.', icon: 'error', confirmButtonText: 'Ok' });
        }
    });
}

// Update fetchCandidates to include new filters and update stats.
// Page is driven by itCurrentPage; size is driven by itCurrentSize.
function fetchCandidates(page, search) {
    // Accept an explicit page argument (e.g. from resetFilters) but always
    // keep itCurrentPage in sync so the pagination handler stays coherent.
    if (page !== undefined) { itCurrentPage = page; }
    if (search !== undefined) { /* search arg ignored; read from DOM below */ }

    if ($('#kt_tagify_2').val() !== '') {
        $('.loader_search').css({ display: 'block' });
    } else {
        $('.page-loader').css('display', 'flex');
    }

    $.ajax({
        url: '{!! route('service_company.interview-schedule.data_ajax') !!}',
        type: 'GET',
        data: {
            page:               itCurrentPage,
            size:               itCurrentSize,
            custom_status:      $('#status').val(),
            search:             $('#inputSearch').val(),
            custom_hiringtype:  $('#sortingfilter').val(),
            custom_search:      $('#kt_tagify_2').val(),
            tab:                activeTab,
            stage_filter:       filterValues.stage,
            status_filter:      filterValues.status,
            owner_filter:       filterValues.owner,
            vendor_filter:      filterValues.vendor,
            mode_filter:        filterValues.mode
        },
        success: function(response) {
            $('.loadingMessage').css({ display: 'none' });
            $('.loader_search').css({ display: 'none' });
            $('.page-loader').css('display', 'none');
            $('#Interview-trackers').html(response.html);
            $('#pagination').html(response.pagination);

            // Update the "showing" text – prefer the richer from/to info if
            // the server returned it, otherwise fall back to a simple total.
            if (response.from !== undefined && response.to !== undefined && response.total !== undefined) {
                $('#showingText').text(
                    'Showing ' + response.from + ' to ' + response.to + ' of ' + response.total + ' records'
                );
            } else if (response.total) {
                $('#showingText').text('Showing ' + response.total + ' records');
            }

            // Update stats if provided (set unconditionally so 0 shows correctly)
            if (response.stats) {
                $('#stat-interview-month').text(response.stats.interview_month ?? 0);
                $('#stat-today').text(response.stats.today ?? 0);
                $('#stat-upcoming').text(response.stats.upcoming ?? 0);
                $('#stat-feedback-pending').text(response.stats.feedback_pending ?? 0);
            }

            // Update tab counts if provided
            if (response.tab_counts) {
                if (response.tab_counts.all)             $('#tab-count-all').text(response.tab_counts.all);
                if (response.tab_counts.today)           $('#tab-count-today').text(response.tab_counts.today);
                if (response.tab_counts.upcoming)        $('#tab-count-upcoming').text(response.tab_counts.upcoming);
                if (response.tab_counts.pending_feedback)$('#tab-count-pending').text(response.tab_counts.pending_feedback);
                if (response.tab_counts.selected)        $('#tab-count-selected').text(response.tab_counts.selected);
                if (response.tab_counts.rejected)        $('#tab-count-rejected').text(response.tab_counts.rejected);
                if (response.tab_counts.rescheduled)     $('#tab-count-rescheduled').text(response.tab_counts.rescheduled);
            }
        }
    });
}

// Close filter menus and followup menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.it-filter-dropdown')) {
        document.querySelectorAll('.it-filter-menu.open').forEach(m => m.classList.remove('open'));
    }
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

    // reset email button state
    $('#ilEmailBtn').prop('disabled', false);
    $('.il-email-label').removeClass('d-none');
    $('.il-email-loading').addClass('d-none');

    document.getElementById('ilOverlay').classList.add('show');
});

// Open the link, or open the address in Google Maps
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
                Swal.fire({ text: 'Emailed to ' + (resp.count || 0) + ' recipient(s).', icon: 'success', confirmButtonText: 'Ok' });
            } else {
                Swal.fire({ text: (resp && resp.msg) ? resp.msg : 'Could not send the email.', icon: 'error', confirmButtonText: 'Ok' });
            }
        },
        error: function () {
            $btn.prop('disabled', false);
            $('.il-email-label').removeClass('d-none');
            $('.il-email-loading').addClass('d-none');
            Swal.fire({ text: 'Could not send the email.', icon: 'error', confirmButtonText: 'Ok' });
        }
    });
});

// Close on click outside the box, and on Escape
$(document).on('click', '#ilOverlay', function (e) { if (e.target === this) closeInterviewLink(); });
$(document).on('keydown', function (e) { if (e.key === 'Escape') closeInterviewLink(); });
</script>

@endpush
