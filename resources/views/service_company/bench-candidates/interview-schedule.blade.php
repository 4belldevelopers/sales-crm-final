@extends('layouts.service_company_layout')
@push('head-script')

<link rel="stylesheet" href="{{ asset('assets/css/service_company/bench-interview-schedule.css') }}{{ TM_VERSION }}">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
@media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td,.table th,.table tr{width:max-content!important}
  .table>:not(caption)>*>{padding:0rem}
  td:nth-child(n+2),th:nth-child(n+2){width:revert!important}
}
@media only screen and (min-width:1024px) and (max-width:1440px){#client_stage_sub_stage{width:1025px}#kt_client_activitiesApplication{width:1025px}}
@media only screen and (min-width:1440px) and (max-width:2560px){#client_stage_sub_stage{width:1200px}#kt_client_activitiesApplication{width:1200px}}

:root{
    --it-primary:#2563eb;--it-primary-light:#eff6ff;--it-primary-dark:#1d4ed8;
    --it-success:#10b981;--it-success-light:#f0fdf4;
    --it-warning:#f59e0b;--it-warning-light:#fffbeb;
    --it-danger:#ef4444;--it-danger-light:#fef2f2;
    --it-purple:#8b5cf6;--it-purple-light:#f5f3ff;
    --it-orange:#f97316;--it-orange-light:#fff7ed;
    --it-text-main:#1e293b;--it-text-muted:#64748b;--it-text-light:#94a3b8;
    --it-border:#e2e8f0;--it-border-light:#f1f5f9;
    --it-bg-main:#f8fafc;--it-bg-card:#ffffff;
    --it-radius:6px;--it-radius-lg:10px;--it-radius-xl:12px;
    --it-shadow:0 1px 3px rgba(0,0,0,.08);--it-shadow-md:0 4px 6px -1px rgba(0,0,0,.1);--it-shadow-lg:0 10px 15px -3px rgba(0,0,0,.1);
    --it-transition:all 0.2s ease;
}
.it-dashboard{font-family:'Poppins',sans-serif;background:var(--it-bg-main);color:var(--it-text-main);line-height:1.45;-webkit-font-smoothing:antialiased}
.it-dashboard ::-webkit-scrollbar{width:5px;height:5px}
.it-dashboard ::-webkit-scrollbar-track{background:transparent}
.it-dashboard ::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
.it-dashboard ::-webkit-scrollbar-thumb:hover{background:#94a3b8}

.it-stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:14px}
.it-stat-card{background:white;border-radius:var(--it-radius-xl);padding:12px 14px;box-shadow:var(--it-shadow);border:1px solid var(--it-border-light);transition:var(--it-transition);cursor:pointer;min-width:0;display:flex;align-items:center;gap:10px}
.it-stat-card:hover{transform:translateY(-2px);box-shadow:var(--it-shadow-md)}
.it-stat-icon-wrap{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.it-stat-icon-wrap svg{width:18px!important;height:18px!important;max-width:18px!important;max-height:18px!important}
.it-stat-icon-blue{background:#dbeafe;color:#2563eb}
.it-stat-icon-yellow{background:#fef3c7;color:#d97706}
.it-stat-icon-purple{background:#f3e8ff;color:#7c3aed}
.it-stat-icon-green{background:#d1fae5;color:#059669}
.it-stat-icon-red{background:#fee2e2;color:#dc2626}
.it-stat-body{display:flex;flex-direction:column;gap:2px;min-width:0}
.it-stat-label{font-size:10px;color:var(--it-text-muted);font-weight:500;line-height:1.2}
.it-stat-value-row{display:flex;align-items:center;gap:5px;line-height:1}
.it-stat-value{font-size:14px;font-weight:700;color:var(--it-text-main);letter-spacing:-.3px}
.it-stat-trend{font-size:10px;font-weight:600;display:inline-flex;align-items:center;gap:1px}
.it-stat-trend svg{width:10px!important;height:10px!important;max-width:10px!important;max-height:10px!important}
.it-trend-up{color:var(--it-success)}
.it-trend-down{color:var(--it-danger)}

.it-filters-bar{background:white;border-radius:var(--it-radius-xl);padding:8px 12px;display:flex;gap:8px;align-items:center;margin-bottom:12px;box-shadow:var(--it-shadow);border:1px solid var(--it-border-light);flex-wrap:wrap}
.it-search-box{flex:1 1 auto!important;width:auto!important;max-width:none!important;min-width:180px;position:relative}
.it-search-box input{width:100%;padding:6px 10px 6px 30px;border:1px solid var(--it-border);border-radius:var(--it-radius);font-size:11px;font-family:'Poppins',sans-serif;color:var(--it-text-main);outline:none;transition:var(--it-transition);background:var(--it-bg-main);height:32px}
.it-search-box input:focus{border-color:var(--it-primary);background:white;box-shadow:0 0 0 3px var(--it-primary-light)}
.it-search-box svg{position:absolute;left:8px;top:50%;transform:translateY(-50%);color:var(--it-text-light);pointer-events:none;width:14px!important;height:14px!important;max-width:14px!important;max-height:14px!important}
.it-filter-dropdown{position:relative;min-width:100px;flex:0 0 auto}
.it-filter-trigger{display:flex;align-items:center;justify-content:space-between;gap:6px;padding:6px 10px;border:1px solid var(--it-border);border-radius:var(--it-radius);font-size:11px;font-family:'Poppins',sans-serif;color:var(--it-text-main);background:white;cursor:pointer;transition:var(--it-transition);white-space:nowrap;width:100%;height:32px}
.it-filter-trigger:hover{border-color:var(--it-text-light)}
.it-filter-trigger .it-label{color:var(--it-text-muted);font-size:10px;margin-right:3px}
.it-filter-trigger .it-value{font-weight:500}
.it-filter-trigger svg{width:12px!important;height:12px!important;max-width:12px!important;max-height:12px!important}
.it-filter-menu{position:absolute;top:calc(100% + 4px);left:0;right:0;background:white;border:1px solid var(--it-border);border-radius:var(--it-radius-lg);box-shadow:var(--it-shadow-lg);z-index:60;display:none;overflow:hidden;max-height:220px;overflow-y:auto}
.it-filter-menu.open{display:block}
.it-filter-menu-item{padding:7px 10px;font-size:11px;cursor:pointer;transition:var(--it-transition);display:flex;align-items:center;gap:6px}
.it-filter-menu-item:hover{background:var(--it-bg-main)}
.it-filter-menu-item.selected{background:var(--it-primary-light);color:var(--it-primary);font-weight:500}
.it-filter-actions{display:flex;gap:6px;margin-left:auto}
.it-btn-reset{background:transparent;border:none;color:var(--it-primary);font-weight:500;padding:6px 8px;height:32px;display:inline-flex;align-items:center;gap:5px;border-radius:var(--it-radius);font-size:11px;font-family:'Poppins',sans-serif;cursor:pointer;transition:var(--it-transition)}
.it-btn-reset:hover{background:var(--it-primary-light)}
.it-btn-reset svg{width:14px!important;height:14px!important;max-width:14px!important;max-height:14px!important}

.it-table-container{background:white;border-radius:var(--it-radius-xl);box-shadow:var(--it-shadow);border:1px solid var(--it-border-light);overflow:hidden}
.it-table-scroll{overflow-x:auto}
.it-data-table{width:100%;border-collapse:separate;border-spacing:0;min-width:1100px}
.it-data-table th{background:#f8fafc;padding:8px 10px;text-align:left;font-size:9px;text-transform:uppercase;letter-spacing:.5px;color:var(--it-text-muted);font-weight:600;border-bottom:1px solid var(--it-border);white-space:nowrap;position:sticky;top:0;z-index:5;line-height:1.4}
.it-data-table th:first-child{border-radius:var(--it-radius-xl) 0 0 0;padding-left:14px;width:36px}
.it-data-table th:last-child{border-radius:0 var(--it-radius-xl) 0 0;padding-right:14px}
.it-data-table td{padding:10px 10px;border-bottom:1px solid var(--it-border-light);font-size:11px;vertical-align:top;transition:background .15s}
.it-data-table tbody tr:hover td{background:#f8fafc}
.it-data-table tbody tr:last-child td{border-bottom:none}
.it-data-table td:first-child{padding-left:14px}
.it-data-table td:last-child{padding-right:14px}

.it-checkbox{display:none!important}
.it-data-table th:first-child,
.it-data-table td:first-child{display:none!important}

.it-candidate-cell{display:flex;gap:8px}
.it-avatar{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:600;flex-shrink:0;text-transform:uppercase}
.it-avatar-green{background:#10b981}.it-avatar-red{background:#ef4444}.it-avatar-blue{background:#3b82f6}
.it-avatar-purple{background:#8b5cf6}.it-avatar-orange{background:#f97316}.it-avatar-gray{background:#6b7280}
.it-candidate-info{min-width:0}
.it-candidate-name{font-weight:600;font-size:11px;color:var(--it-text-main);margin-bottom:1px}
.it-candidate-role{font-size:10px;color:var(--it-text-muted);margin-bottom:3px}

.it-job-title{font-weight:600;font-size:11px;color:var(--it-text-main);margin-bottom:1px}
.it-job-company{font-size:10px;color:var(--it-text-muted);margin-bottom:4px}
.it-job-tags{display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.it-job-type{display:inline-flex;align-items:center;padding:2px 6px;border-radius:4px;font-size:9px;font-weight:600;background:var(--it-primary-light);color:var(--it-primary)}
.it-job-type-internal{background:#eef2ff;color:#4f46e5}
.it-job-type-direct{background:#f0fdf4;color:#16a34a}
.it-job-id{font-size:10px;color:var(--it-primary);font-weight:500;text-decoration:none}
.it-job-id:hover{text-decoration:underline}

.it-date-cell{display:flex;flex-direction:column;gap:3px}
.it-date-row{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--it-text-main);font-weight:500}
.it-date-row svg{color:var(--it-text-light);flex-shrink:0;width:12px!important;height:12px!important;max-width:12px!important;max-height:12px!important}
.it-date-row .it-mode{color:var(--it-text-muted);font-size:10px;font-weight:400}

.it-person-cell{display:flex;flex-direction:column;gap:2px}
.it-person-name{font-weight:600;font-size:11px;color:var(--it-text-main)}
.it-person-company{font-size:10px;color:var(--it-text-muted)}

.it-stage-cell{display:flex;flex-direction:column;gap:3px}
.it-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:500;width:fit-content}
.it-badge-blue{background:#dbeafe;color:#1e40af}.it-badge-purple{background:#f3e8ff;color:#6b21a8}
.it-badge-orange{background:#ffedd5;color:#9a3412}.it-badge-red{background:#fee2e2;color:#991b1b}
.it-badge-green{background:#d1fae5;color:#065f46}.it-badge-yellow{background:#fef9c3;color:#854d0e}
.it-badge-gray{background:#e2e8f0;color:#374151}.it-badge-pink{background:#fce7f3;color:#9d174d}
.it-stage-status{display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:500}
.it-stage-status.pending{color:var(--it-warning)}
.it-stage-status svg{width:10px!important;height:10px!important;max-width:10px!important;max-height:10px!important}

.it-feedback-cell{display:flex;flex-direction:column;gap:4px}
.it-feedback-btn{width:fit-content;padding:4px 10px;border-radius:var(--it-radius);font-size:10px;font-weight:600;font-family:'Poppins',sans-serif;cursor:pointer;transition:var(--it-transition);display:inline-flex;align-items:center;gap:3px;border:1px solid transparent}
.it-feedback-btn-outline-green{border-color:var(--it-success);color:var(--it-success);background:transparent}
.it-feedback-btn-outline-green:hover{background:var(--it-success-light)}
.it-feedback-btn-outline-orange{border-color:var(--it-orange);color:var(--it-orange);background:transparent}
.it-feedback-btn-outline-orange:hover{background:var(--it-orange-light)}

.pagi-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:12px 20px;border-top:1px solid #F0F2F5;background:#fff;font-family:'Poppins',sans-serif;width:100%}
.pagi-info{font-size:13px;font-weight:500;color:#64748B}
.pagi-right{display:flex;align-items:center;gap:6px}
.pagi-arrow{width:32px;height:32px;border:1.5px solid #E5E9F0;border-radius:8px;background:#fff;color:#64748B;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;transition:all .15s;outline:none}
.pagi-arrow:hover:not(.pagi-disabled){background:#2563EB;color:#fff;border-color:#2563EB}
.pagi-arrow.pagi-disabled{opacity:.35;pointer-events:none;cursor:not-allowed}
.pagi-page{min-width:32px;height:32px;padding:0 8px;border:1.5px solid #E5E9F0;border-radius:8px;background:#fff;color:#374151;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;font-weight:600;transition:all .15s;font-family:'Poppins',sans-serif;outline:none}
.pagi-page:hover:not(.pagi-active-disabled){background:#F5F8FF}
.pagi-page.pagi-active{background:#2563EB;color:#fff;border-color:#2563EB;pointer-events:none}
.pagi-page.pagi-active-disabled{pointer-events:none}
.pagi-dots{font-size:13px;color:#94A3B8;padding:0 4px;display:flex;align-items:center}

.it-loading{display:none;padding:30px;text-align:center;color:var(--it-text-muted);font-size:12px}
.it-loading.show{display:block}
#benchInterviewRows{display:contents}
#kt_toolbar{display:none!important}
#kt_content{margin-top:0!important}
#kt_post{margin-top:0!important}

@media(max-width:1150px){.it-stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){.it-stats-grid{grid-template-columns:1fr 1fr}.it-filters-bar{flex-direction:column;align-items:stretch}.it-search-box{max-width:none!important;width:100%!important}}
</style>
@endpush

@section('content')
<input type="hidden" name="inputSearch" id="inputSearch" />
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_client" id="kt_content" style="margin-top:0!important;padding-top:16px!important;">

  @if(in_array('view_bench', $userPermissions))
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post" style="margin-top:0!important;">
    <div id="kt_content_container" class="container-fluid px-3" style="margin-top: -58px;">

      <div class="it-dashboard" id="talentInterviewDashboard" style="width:100%;">

        <!-- Page Heading -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:12px;">
          <div>
            <div style="display:flex;align-items:center;gap:8px;font-size:20px;font-weight:700;color:#181c32;line-height:1.2;">
              Talent Interviews
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;flex-shrink:0;">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
            </div>
            <div style="font-size:12px;color:#7e8299;margin-top:3px;">Track, schedule and manage all talent interviews in one place.</div>
          </div>
        </div>

        <!-- Stats Grid -->
        <div class="it-stats-grid">
          <div class="it-stat-card">
            <div class="it-stat-icon-wrap it-stat-icon-blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="it-stat-body">
              <div class="it-stat-label">Interview This Month <span style="color:var(--it-primary);font-weight:600;">({{ now()->format('M Y') }})</span></div>
              <div class="it-stat-value-row">
                <div class="it-stat-value" id="stat-interview-month">0</div>
                <div class="it-stat-trend it-trend-up">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                  +12%
                </div>
              </div>
            </div>
          </div>
          <div class="it-stat-card">
            <div class="it-stat-icon-wrap it-stat-icon-green">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></svg>
            </div>
            <div class="it-stat-body">
              <div class="it-stat-label">Today's Interview <span style="color:var(--it-success);font-weight:600;">({{ now()->format('d M') }})</span></div>
              <div class="it-stat-value-row">
                <div class="it-stat-value" id="stat-today">0</div>
                <div class="it-stat-trend it-trend-up">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
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
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
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
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                  -8%
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters Bar -->
        <div class="it-filters-bar">
          <div class="it-search-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="kt_tagify_2" placeholder="Search candidate, job, recruiter..." autocomplete="off">
          </div>
          <input type="hidden" id="sortingfilter" value="">
          <div class="it-filter-dropdown" style="min-width:200px;">
            <button class="it-filter-trigger" onclick="toggleFilterMenu(this)" type="button">
              <span class="it-label">Sort</span>
              <span class="it-value" id="filter-sort-label">Newest Interviews</span>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="it-filter-menu" id="filter-menu-sort">
              <div class="it-filter-menu-item selected" onclick="selectSort(this,'')">Newest Interviews</div>
              <div class="it-filter-menu-item" onclick="selectSort(this,'1')">Today Interview ({{ date('d M y') }})</div>
              <div class="it-filter-menu-item" onclick="selectSort(this,'Feedback Pending')">Feedback Pending</div>
              <div class="it-filter-menu-item" onclick="selectSort(this,'4')">New to Old (Interview Date)</div>
              <div class="it-filter-menu-item" onclick="selectSort(this,'3')">Old to New (Interview Date)</div>
            </div>
          </div>
          <div class="it-filter-actions">
            <button class="it-btn-reset" type="button" onclick="resetFilters()">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
              Reset
            </button>
          </div>
        </div>

        @if(in_array('view_schedule', $userPermissions))
        <!-- Table -->
        <div class="it-table-container">
          <div class="it-table-scroll">
            <div class="loadingMessagebenchCandidateInterviewScheduleAjax it-loading">Loading interviews...</div>
            <table class="it-data-table" id="myTable">
              <thead>
                <tr>
                  <th>
                    <div class="it-checkbox" id="itSelectAllCheckbox" onclick="itToggleSelectAll()">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                  </th>
                  <th style="width:210px">Candidate</th>
                  <th style="width:190px">Job Requisitions</th>
                  <th style="width:170px">Interview Date &amp; Time</th>
                  <th style="width:140px">Interviewers</th>
                  <th style="width:120px">Source</th>
                  <th style="width:120px">Stage</th>
                  <th style="width:160px">Feedback</th>
                </tr>
              </thead>
              <tbody id="benchInterviewRows"></tbody>
            </table>
          </div>
          <div id="paginationbenchCandidateInterviewSchedule"></div>
        </div>
        @else
        <div class="it-table-container" style="margin-top:10px;">
          <table class="it-data-table">
            <thead><tr><th style="text-align:center">ACCESS DENIED CONTACT ADMINISTRATOR</th></tr></thead>
          </table>
        </div>
        @endif

      </div>

    </div>
  </div>
  <!--end::Post-->
  @endif
</div>
<!--end::Content-->

<!--begin::Modal - Interview Feedback (view-only) -->
<div href="javascript:void(0);" id="intervied_feedback_toggle" class="text-dark fw-bolder text-hover-primary fs-6"
    data-bs-toggle="modal" data-bs-target="#interview_feedback"></div>
<div class="modal fade" id="interview_feedback" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content custmbg">
      <div class="modal-header flex-stack">
        <h2>Interview Feedback</h2>
        <div class="btn btn-sm btn-icon btn-active-light-danger" data-bs-dismiss="modal">
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
<!--end::Modal-->
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/moment/moment.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}{{ TM_VERSION }}" type="text/javascript"></script>

<script>
// ---- View-only feedback (no add/edit) ----
$(document).on('click', '.show-feedback', function() {
    $('#intervied_feedback_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.interview-schedule.showfeedback', ':id') }}";
    url = url.replace(':id', id);
    $.ajax({
        type: 'GET', url: url,
        beforeSend: function() {
            $('#load_interview_feedback').html(
                '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:50px 20px;gap:12px;">'
                + '<div style="width:36px;height:36px;border:3px solid #e2e8f0;border-top-color:#2563eb;border-radius:50%;animation:fbSpin .5s linear infinite;"></div>'
                + '<div style="color:#64748b;font-size:13px;font-family:Poppins,sans-serif;">Loading feedback…</div></div>'
                + '<style>@keyframes fbSpin{to{transform:rotate(360deg)}}</style>'
            );
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#load_interview_feedback').html(response.view);
            }
        }
    });
});

$(document).on('hidden.bs.modal', '#interview_feedback', function() {
    $('#load_interview_feedback').html('&nbsp;');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
});

// ---- Client-side LRU Cache & In-flight XHR reference ----
var inFlightBenchXhr = null;
var clientBenchCache = new Map();
var currentCompanyId = {{ auth()->user()->company_id ?? 0 }};

function getAvatarClass(name) {
    if (!name) return 'it-avatar-gray';
    var str = name.trim().toLowerCase();
    if (!str) return 'it-avatar-gray';
    var code = str.charCodeAt(0);
    if (code >= 97 && code <= 101) return 'it-avatar-red';
    if (code >= 102 && code <= 106) return 'it-avatar-orange';
    if (code >= 107 && code <= 111) return 'it-avatar-blue';
    if (code >= 112 && code <= 116) return 'it-avatar-green';
    if (code >= 117 && code <= 121) return 'it-avatar-purple';
    return 'it-avatar-gray';
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function ucwords(str) {
    if (!str) return '';
    return String(str).replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });
}

function formatInterviewDate(dateStr) {
    if (!dateStr) return '';
    var d = new Date(dateStr);
    if (isNaN(d.getTime())) return escapeHtml(dateStr);
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var day = String(d.getDate()).padStart(2, '0');
    return day + ' ' + months[d.getMonth()] + ', ' + d.getFullYear();
}

function renderBenchInterviewRows(data, companyId) {
    if (!data || data.length === 0) {
        return '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--it-text-muted);font-size:12px;background:#fffcfc7a;">No data available in table</td></tr>';
    }

    var feedbackStatusMap = {1:{name:'Selected'}, 2:{name:'Hold'}, 3:{name:'Rejected'}, 4:{name:'Fake / Proxy'}, 5:{name:'Feedback Pending'}};
    var html = '';

    data.forEach(function(row) {
        var fullName = (row.full_name || '').trim();
        var designation = (row.current_designation || '').trim();
        var initial = fullName ? fullName.charAt(0).toUpperCase() : '?';
        var avatarClass = getAvatarClass(fullName);

        var shortName = fullName.length > 20 ? fullName.substring(0, 20) : fullName;
        var shortDesignation = designation.length > 21 ? designation.substring(0, 21) : designation;

        var clientTitleName = row.client_title_name_enriched || row.client_name || row.company_name || '';
        var shortClientName = clientTitleName.length > 14 ? clientTitleName.substring(0, 14) : clientTitleName;
        var jobTitle = (row.job_title || '').trim();
        var shortJobTitle = jobTitle.length > 18 ? jobTitle.substring(0, 18) : jobTitle;

        var jobTypeLabel = 'Internal';
        var jobTypeClass = 'it-job-type-internal';
        if ((row.recommendation_id || 0) > 0 && (row.to_company_id == companyId)) {
            jobTypeLabel = 'Direct Client';
            jobTypeClass = 'it-job-type-direct';
        }

        var modeIcon = '';
        if (row.interview_mode == 1) {
            modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
        } else if (row.interview_mode == 2) {
            modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>';
        } else {
            modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
        }

        var mLink = (row.interview_mention_link || '').trim();
        var isUrl = /^https?:\/\//i.test(mLink);
        var ilType = null; var ilLabel = '';
        if (row.interview_mode == 3 && mLink !== '') {
            ilType = 'address'; ilLabel = 'View Address';
        } else if (isUrl) {
            ilType = 'link'; ilLabel = 'Interview Link';
        }

        var linkHtml = '';
        if (ilType) {
            var iconSvg = (ilType === 'address')
                ? '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>'
                : '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>';

            linkHtml = '<a href="javascript:void(0);" class="it-date-row open-interview-link" ' +
                'style="color:var(--it-primary);font-weight:500;cursor:pointer;margin-top:2px;" ' +
                'data-link="' + escapeHtml(mLink) + '" ' +
                'data-id="' + row.id + '" ' +
                'data-name="' + escapeHtml(fullName) + '" ' +
                'data-type="' + ilType + '">' +
                iconSvg + ' ' + ilLabel + '</a>';
        }

        var interviewersStr = row.interviewers || '';
        var firstInterviewer = 'N/A';
        var extraInterviewers = 0;
        if (interviewersStr) {
            var parts = interviewersStr.split(' , ');
            if (parts.length > 0 && parts[0].trim()) {
                firstInterviewer = parts[0].trim();
                extraInterviewers = parts.length - 1;
            }
        }

        var postedBy = ucwords((row.posted_by || '').trim());
        var companyName = (row.company_name || '').trim();
        var shortPostedBy = postedBy.length > 12 ? postedBy.substring(0, 12) : postedBy;
        var shortCompanyName = companyName.length > 12 ? companyName.substring(0, 12) : companyName;

        var stageName = '';
        if (row.master_status_id == 8) stageName = 'Private';
        if (row.child_status_id && row.child_status_id > 0) stageName = row.child_status_name || '';

        var masterStatus = (row.master_status_name || '').trim();
        var stageLower = masterStatus.toLowerCase();
        var stageBadgeClass = 'it-badge-blue';
        if (stageLower.includes('review') || stageLower.includes('inw')) stageBadgeClass = 'it-badge-yellow';
        else if (stageLower.includes('sourc') || stageLower.includes('sou')) stageBadgeClass = 'it-badge-gray';
        else if (stageLower.includes('screen') || stageLower.includes('scr')) stageBadgeClass = 'it-badge-purple';
        else if (stageLower.includes('evaluat') || stageLower.includes('eva')) stageBadgeClass = 'it-badge-blue';
        else if (stageLower.includes('negot') || stageLower.includes('neg')) stageBadgeClass = 'it-badge-pink';
        else if (stageLower.includes('offer') || stageLower.includes('off')) stageBadgeClass = 'it-badge-orange';
        else if (stageLower.includes('join') || stageLower.includes('joi')) stageBadgeClass = 'it-badge-green';
        else if (stageLower.includes('reject') || stageLower.includes('rej')) stageBadgeClass = 'it-badge-red';

        var latestFb = row.latest_feedback_enriched || null;
        var hasAnyFb = !!latestFb;
        var attId = latestFb ? parseInt(latestFb.available_status || 0) : 0;
        var attLabelMap = {1:'Attended', 2:'No Show', 3:'Rescheduled', 4:'Cancelled by Company', 6:'Other Reason', 7:'Cancelled by Candidate'};

        var resultLabels = {'selected':'Selected','hold':'Hold','rejected':'Rejected','fake_proxy':'Fake / Proxy','feedback_pending':'Feedback Pending'};
        var resultKey = latestFb ? latestFb.interview_result : null;
        var resultText = (resultKey && resultLabels[resultKey]) ? resultLabels[resultKey] : '';
        if (!resultText && row.feedback && feedbackStatusMap[row.feedback]) {
            resultText = feedbackStatusMap[row.feedback].name;
        }

        var legacyFb = parseInt(row.feedback || 0);
        var resultColor = 'var(--it-success)';
        if (['rejected','fake_proxy'].includes(resultKey) || [3,4].includes(legacyFb)) resultColor = 'var(--it-danger)';
        else if (['hold','feedback_pending'].includes(resultKey) || [2,5].includes(legacyFb)) resultColor = 'var(--it-warning)';

        var recLabels = {'strong_hire':'Strong Hire','hire':'Hire','consider':'Consider','no_hire':'No Hire','strong_no_hire':'Strong No Hire'};
        var recKey = latestFb ? latestFb.overall_recommendation : null;
        var recText = (recKey && recLabels[recKey]) ? recLabels[recKey] : '';
        var recBg = '#eef2ff'; var recColor = '#4f46e5';
        if (['strong_hire','hire'].includes(recKey)) { recBg = '#dcfce7'; recColor = '#16a34a'; }
        else if (['no_hire','strong_no_hire'].includes(recKey)) { recBg = '#fee2e2'; recColor = '#dc2626'; }
        else if (recKey === 'consider') { recBg = '#fef3c7'; recColor = '#d97706'; }

        var attended = (attId === 1);
        var notAttended = (hasAnyFb && attId > 0 && attId !== 1);

        var feedbackCellHtml = '';
        if (attended) {
            feedbackCellHtml = '<span style="font-size:10px;font-weight:600;color:' + resultColor + ';display:inline-flex;align-items:center;gap:3px;">' +
                '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' +
                escapeHtml(resultText || 'Feedback Pending') + '</span>';
            if (recText) {
                feedbackCellHtml += ' <span style="display:inline-flex;align-items:center;width:fit-content;padding:2px 8px;border-radius:50px;font-size:10px;font-weight:600;background:' + recBg + ';color:' + recColor + ';">' + escapeHtml(recText) + '</span>';
            }
            feedbackCellHtml += ' <button type="button" class="it-feedback-btn it-feedback-btn-outline-green show-feedback" data-row-id="' + row.id + '">' +
                '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View Feedback</button>';
        } else if (notAttended) {
            feedbackCellHtml = '<span style="font-size:10px;font-weight:600;color:var(--it-warning);display:inline-flex;align-items:center;gap:3px;">' +
                '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
                escapeHtml(attLabelMap[attId] || 'Not Attended') + '</span> ' +
                '<button type="button" class="it-feedback-btn show-feedback" data-row-id="' + row.id + '" style="border:1px solid var(--it-border);color:var(--it-text-muted);background:#fff;">' +
                '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View</button>';
        } else {
            feedbackCellHtml = '<span style="font-size:10px;font-weight:600;color:var(--it-warning);display:inline-flex;align-items:center;gap:3px;">' +
                '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Pending</span> ' +
                '<span style="font-size:10px;color:var(--it-text-muted);">Feedback Pending</span>';
        }

        html += '<tr>' +
            '<td><div class="it-checkbox" data-row-check onclick="toggleRowSelect(this, ' + row.id + ')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div></td>' +
            '<td><div class="it-candidate-cell"><div class="it-avatar ' + avatarClass + ' show-application-detail" data-row-id="' + (row.wiseID || '') + '">' + initial + '</div>' +
            '<div class="it-candidate-info"><div class="it-candidate-name"><a href="javascript:void(0);" data-row-id="' + (row.wiseID || '') + '" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' + escapeHtml(fullName) + '" style="text-decoration:none;color:inherit;">' + escapeHtml(shortName) + '</a></div>' +
            '<div class="it-candidate-role"><a href="javascript:void(0);" data-row-id="' + (row.wiseID || '') + '" class="fw-bold text-muted d-block fs-7 show-application-detail text-hover-primary" data-toggle="tooltip" title="' + escapeHtml(designation) + '" style="text-decoration:none;color:inherit;">' + escapeHtml(shortDesignation) + '</a></div></div></div></td>' +
            '<td><div class="it-job-title" data-toggle="tooltip" title="' + escapeHtml(jobTitle) + '">' + escapeHtml(shortJobTitle) + '</div>' +
            '<div class="it-job-company" data-toggle="tooltip" title="' + escapeHtml(clientTitleName) + '">' + escapeHtml(shortClientName) + '</div>' +
            '<div class="it-job-tags"><span class="it-job-type ' + jobTypeClass + '">' + jobTypeLabel + '</span><span class="it-dot-separator"></span><a href="javascript:void(0);" class="it-job-id">' + (row.job_id || '') + '</a></div></td>' +
            '<td><div class="it-date-cell"><div class="it-date-row"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> ' + formatInterviewDate(row.schedule_date) + '</div>' +
            '<div class="it-date-row"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> ' + (row.schedule_to_time || '') + (row.timezone_abbreviation ? ' (' + row.timezone_abbreviation + ')' : '') + '</div>' +
            '<div class="it-date-row it-mode">' + modeIcon + ' ' + escapeHtml(row.mode_name ? row.mode_name.substring(0, 13) : '') + '</div>' + linkHtml + '</div></td>' +
            '<td><div class="it-person-cell"><div class="it-person-name">' + escapeHtml(firstInterviewer) + '</div>' +
            (extraInterviewers > 0 ? '<div class="it-person-extra"><span class="it-extra-badge">+' + extraInterviewers + '</span><span class="it-extra-text">' + (extraInterviewers + 1) + ' Interviewers</span></div>' : '') + '</div></td>' +
            '<td><div class="it-person-cell"><div class="it-person-name" data-toggle="tooltip" title="' + escapeHtml(postedBy) + '">' + escapeHtml(shortPostedBy) + '</div><div class="it-person-company" data-toggle="tooltip" title="' + escapeHtml(companyName) + '">' + escapeHtml(shortCompanyName) + '</div></div></td>' +
            '<td><div class="it-stage-cell"><span class="it-badge ' + stageBadgeClass + '">' + escapeHtml(masterStatus.substring(0, 10)) + '</span>' + (stageName ? '<span class="text-muted fw-bold d-block fs-7" style="font-size:10px;color:var(--it-text-muted);margin-top:2px;">' + escapeHtml(stageName) + '</span>' : '') + '</div></td>' +
            '<td><div class="it-feedback-cell">' + feedbackCellHtml + '</div></td>' +
            '</tr>';
    });

    return html;
}

function renderBenchInterviewPagination(response) {
    var total = response.total || 0;
    if (total === 0) return '';

    var curPage = response.current_page || 1;
    var totalPages = response.last_page || 1;
    var perPage = response.per_page || 10;

    var from = (curPage - 1) * perPage + 1;
    var to = Math.min(curPage * perPage, total);

    var html = '<div class="pagi-bar">';
    html += '<div class="pagi-info">Showing ' + from + ' to ' + to + ' of ' + total + ' records</div>';
    html += '<div class="pagi-right">';

    var prevDisabled = curPage <= 1 ? 'pagi-disabled' : '';
    html += '<button class="pagi-arrow ' + prevDisabled + '" data-page="' + (curPage - 1) + '" data-listtype="allcandidate" type="button">' +
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>' +
        '</button>';

    for (var i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= curPage - 2 && i <= curPage + 2)) {
            var activeClass = i === curPage ? 'pagi-active pagi-active-disabled' : '';
            html += '<button class="pagi-page ' + activeClass + '" data-page="' + i + '" data-listtype="allcandidate" type="button">' + i + '</button>';
        } else if (i === curPage - 3 || i === curPage + 3) {
            html += '<span class="pagi-dots">&hellip;</span>';
        }
    }

    var nextDisabled = curPage >= totalPages ? 'pagi-disabled' : '';
    html += '<button class="pagi-arrow ' + nextDisabled + '" data-page="' + (curPage + 1) + '" data-listtype="allcandidate" type="button">' +
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>' +
        '</button>';

    html += '</div></div>';
    return html;
}

function updateStatsUI(stats) {
    if (stats) {
        $('#stat-interview-month').text(stats.interview_month ?? 0);
        $('#stat-today').text(stats.today ?? 0);
        $('#stat-upcoming').text(stats.upcoming ?? 0);
        $('#stat-feedback-pending').text(stats.feedback_pending ?? 0);
    }
}

// ---- Data fetch (v2 JSON-first with AbortController & LRU Cache) ----
function fetchBenchInterviewScheduleCandidateList(page = 1) {
    var perPage = localStorage.getItem('candidatePerPage') || 10;
    var customSearch = $('#kt_tagify_2').val() || '';
    var customHiringType = $('#sortingfilter').val() || '';
    var cacheKey = page + '_' + perPage + '_' + customHiringType + '_' + customSearch;

    // (1) Client-side LRU Cache hit: render instantly with 0ms server latency (§4.8)
    if (clientBenchCache.has(cacheKey)) {
        var cached = clientBenchCache.get(cacheKey);
        $('#benchInterviewRows').html(renderBenchInterviewRows(cached.data, currentCompanyId));
        $('#paginationbenchCandidateInterviewSchedule').html(renderBenchInterviewPagination(cached));
        updateStatsUI(cached.stats);
        return;
    }

    // (2) Cancel previous in-flight request on fast typing (§4.6)
    if (inFlightBenchXhr) {
        inFlightBenchXhr.abort();
        inFlightBenchXhr = null;
    }

    $('.loadingMessagebenchCandidateInterviewScheduleAjax').addClass('show');

    inFlightBenchXhr = $.ajax({
        url: "{{ route('service_company.bench-candidates.benchinterviewdata_v2') }}?page=" + page,
        type: 'GET',
        data: {
            custom_search: customSearch,
            custom_hiringtype: customHiringType,
            per_page: perPage
        },
        success: function(response) {
            inFlightBenchXhr = null;
            $('.loadingMessagebenchCandidateInterviewScheduleAjax').removeClass('show');

            if (response.data !== undefined) {
                // Store in LRU cache (cap size at 50 items)
                clientBenchCache.set(cacheKey, response);
                if (clientBenchCache.size > 50) {
                    var firstKey = clientBenchCache.keys().next().value;
                    clientBenchCache.delete(firstKey);
                }

                $('#benchInterviewRows').html(renderBenchInterviewRows(response.data, currentCompanyId));
                $('#paginationbenchCandidateInterviewSchedule').html(renderBenchInterviewPagination(response));
                updateStatsUI(response.stats);
            } else if (response.html !== undefined) {
                // Fallback for HTML response if ever redirected to v1
                $('#benchInterviewRows').html(response.html);
                $('#paginationbenchCandidateInterviewSchedule').html(response.pagination);
                updateStatsUI(response.stats);
            }
        },
        error: function(xhr, status, error) {
            if (status === 'abort') return; // Cleanly ignore aborted requests
            inFlightBenchXhr = null;
            $('.loadingMessagebenchCandidateInterviewScheduleAjax').removeClass('show');
        }
    });
}

$(document).ready(function() {
    fetchBenchInterviewScheduleCandidateList();

    var searchTimer = null;
    $('#kt_tagify_2').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            fetchBenchInterviewScheduleCandidateList(1);
        }, 300);
    });

    $(document).on('click', '#paginationbenchCandidateInterviewSchedule .pagi-page, #paginationbenchCandidateInterviewSchedule .pagi-arrow', function(e) {
        e.preventDefault();
        var page = $(this).data('page') || 1;
        if ($(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
        fetchBenchInterviewScheduleCandidateList(page);
    });

    $(document).on('change', '#paginationbenchCandidateInterviewSchedule .pagi-perpage', function(e) {
        var selectedPerPage = $(this).val();
        if (selectedPerPage) {
            localStorage.setItem('candidatePerPage', selectedPerPage);
            fetchBenchInterviewScheduleCandidateList(1);
        }
    });
});

// ---- Filter / sort helpers ----
function toggleFilterMenu(btn) {
    var menu = btn.nextElementSibling;
    var isOpen = menu.classList.contains('open');
    document.querySelectorAll('.it-filter-menu.open').forEach(function(m) { m.classList.remove('open'); });
    if (!isOpen) menu.classList.add('open');
}

function selectSort(el, value) {
    var menu = el.closest('.it-filter-menu');
    var trigger = menu ? menu.previousElementSibling : null;
    menu.querySelectorAll('.it-filter-menu-item').forEach(function(i) { i.classList.remove('selected'); });
    el.classList.add('selected');
    if (trigger) {
        var lbl = trigger.querySelector('.it-value');
        if (lbl) lbl.textContent = el.textContent.trim();
    }
    menu.classList.remove('open');
    $('#sortingfilter').val(value);
    fetchBenchInterviewScheduleCandidateList(1);
}

function resetFilters() {
    $('#sortingfilter').val('');
    $('#kt_tagify_2').val('');
    var sortMenu = document.getElementById('filter-menu-sort');
    if (sortMenu) {
        sortMenu.querySelectorAll('.it-filter-menu-item').forEach(function(item, idx) {
            item.classList.toggle('selected', idx === 0);
        });
    }
    var sortLabel = document.getElementById('filter-sort-label');
    if (sortLabel) sortLabel.textContent = 'Newest Interviews';
    fetchBenchInterviewScheduleCandidateList(1);
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.it-filter-dropdown')) {
        document.querySelectorAll('.it-filter-menu.open').forEach(function(m) { m.classList.remove('open'); });
    }
});

// ---- Checkbox helpers ----
function itToggleSelectAll() {
    var cb = document.getElementById('itSelectAllCheckbox');
    var isChecked = cb.classList.contains('checked');
    if (isChecked) {
        cb.classList.remove('checked');
        document.querySelectorAll('.it-checkbox[data-row-check]').forEach(function(c) { c.classList.remove('checked'); });
    } else {
        cb.classList.add('checked');
        document.querySelectorAll('.it-checkbox[data-row-check]').forEach(function(c) { c.classList.add('checked'); });
    }
}
function itToggleRow(el) {
    el.classList.toggle('checked');
}
</script>
@endpush
