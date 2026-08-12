@extends('layouts.service_company_layout')
@push('head-script')

<style>
@media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td,.table th,.table tr{width:max-content!important}
  .table>:not(caption)>*>*{padding:0rem}
  td:nth-child(n+2),th:nth-child(n+2){width:revert!important}
}
@media only screen and (min-width:1024px) and (max-width:1440px){#client_stage_sub_stage{width:1025px}#kt_client_activitiesApplication{width:1025px}}
@media only screen and (min-width:1440px) and (max-width:2560px){#client_stage_sub_stage{width:1200px}#kt_client_activitiesApplication{width:1200px}}

/* ===== Talent Interview Tracker Design (mirrors Interview Tracker) ===== */
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

/* Stats */
.it-stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:14px}
.it-stat-card{background:white;border-radius:var(--it-radius-xl);padding:12px 14px;box-shadow:var(--it-shadow);border:1px solid var(--it-border-light);transition:var(--it-transition);cursor:pointer;min-width:0;display:flex;align-items:center;gap:10px}
.it-stat-card:hover{transform:translateY(-2px);box-shadow:var(--it-shadow-md)}
.it-stat-icon-wrap{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.it-stat-icon-wrap svg{width:18px;height:18px}
.it-stat-icon-blue{background:#dbeafe;color:#2563eb}
.it-stat-icon-yellow{background:#fef3c7;color:#d97706}
.it-stat-icon-purple{background:#f3e8ff;color:#7c3aed}
.it-stat-icon-green{background:#d1fae5;color:#059669}
.it-stat-icon-red{background:#fee2e2;color:#dc2626}
.it-stat-body{display:flex;flex-direction:column;gap:2px;min-width:0}
.it-stat-label{font-size:10px;color:var(--it-text-muted);font-weight:500;line-height:1.2}
.it-stat-value-row{display:flex;align-items:center;gap:5px;line-height:1}
.it-stat-value{font-size:16px;font-weight:700;color:var(--it-text-main);letter-spacing:-.3px}
.it-stat-trend{font-size:10px;font-weight:600;display:inline-flex;align-items:center;gap:1px}
.it-stat-trend svg{width:10px;height:10px}
.it-trend-up{color:var(--it-success)}
.it-trend-down{color:var(--it-danger)}

/* Filters */
.it-filters-bar{background:white;border-radius:var(--it-radius-xl);padding:8px 12px;display:flex;gap:8px;align-items:center;margin-bottom:12px;box-shadow:var(--it-shadow);border:1px solid var(--it-border-light);flex-wrap:wrap}
.it-search-box{flex:1 1 auto!important;width:auto!important;max-width:none!important;min-width:180px;position:relative}
.it-search-box input{width:100%;padding:6px 10px 6px 30px;border:1px solid var(--it-border);border-radius:var(--it-radius);font-size:11px;font-family:'Poppins',sans-serif;color:var(--it-text-main);outline:none;transition:var(--it-transition);background:var(--it-bg-main);height:32px}
.it-search-box input:focus{border-color:var(--it-primary);background:white;box-shadow:0 0 0 3px var(--it-primary-light)}
.it-search-box svg{position:absolute;left:8px;top:50%;transform:translateY(-50%);color:var(--it-text-light);pointer-events:none;width:14px;height:14px}
.it-filter-dropdown{position:relative;min-width:100px;flex:0 0 auto}
.it-filter-trigger{display:flex;align-items:center;justify-content:space-between;gap:6px;padding:6px 10px;border:1px solid var(--it-border);border-radius:var(--it-radius);font-size:11px;font-family:'Poppins',sans-serif;color:var(--it-text-main);background:white;cursor:pointer;transition:var(--it-transition);white-space:nowrap;width:100%;height:32px}
.it-filter-trigger:hover{border-color:var(--it-text-light)}
.it-filter-trigger .it-label{color:var(--it-text-muted);font-size:10px;margin-right:3px}
.it-filter-trigger .it-value{font-weight:500}
.it-filter-trigger svg{width:12px;height:12px}
.it-filter-menu{position:absolute;top:calc(100% + 4px);left:0;right:0;background:white;border:1px solid var(--it-border);border-radius:var(--it-radius-lg);box-shadow:var(--it-shadow-lg);z-index:60;display:none;overflow:hidden;max-height:220px;overflow-y:auto}
.it-filter-menu.open{display:block}
.it-filter-menu-item{padding:7px 10px;font-size:11px;cursor:pointer;transition:var(--it-transition);display:flex;align-items:center;gap:6px}
.it-filter-menu-item:hover{background:var(--it-bg-main)}
.it-filter-menu-item.selected{background:var(--it-primary-light);color:var(--it-primary);font-weight:500}
.it-filter-actions{display:flex;gap:6px;margin-left:auto}
.it-btn-reset{background:transparent;border:none;color:var(--it-primary);font-weight:500;padding:6px 8px;height:32px;display:inline-flex;align-items:center;gap:5px;border-radius:var(--it-radius);font-size:11px;font-family:'Poppins',sans-serif;cursor:pointer;transition:var(--it-transition)}
.it-btn-reset:hover{background:var(--it-primary-light)}

/* Table */
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

/* Checkbox — hidden */
.it-checkbox{display:none!important}
.it-data-table th:first-child,
.it-data-table td:first-child{display:none!important}

/* Candidate cell */
.it-candidate-cell{display:flex;gap:8px}
.it-avatar{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:600;flex-shrink:0;text-transform:uppercase}
.it-avatar-green{background:#10b981}.it-avatar-red{background:#ef4444}.it-avatar-blue{background:#3b82f6}
.it-avatar-purple{background:#8b5cf6}.it-avatar-orange{background:#f97316}.it-avatar-gray{background:#6b7280}
.it-candidate-info{min-width:0}
.it-candidate-name{font-weight:600;font-size:11px;color:var(--it-text-main);margin-bottom:1px}
.it-candidate-role{font-size:10px;color:var(--it-text-muted);margin-bottom:3px}

/* Job cell */
.it-job-title{font-weight:600;font-size:11px;color:var(--it-text-main);margin-bottom:1px}
.it-job-company{font-size:10px;color:var(--it-text-muted);margin-bottom:4px}
.it-job-tags{display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.it-job-type{display:inline-flex;align-items:center;padding:2px 6px;border-radius:4px;font-size:9px;font-weight:600;background:var(--it-primary-light);color:var(--it-primary)}
.it-job-type-internal{background:#eef2ff;color:#4f46e5}
.it-job-type-direct{background:#f0fdf4;color:#16a34a}
.it-job-id{font-size:10px;color:var(--it-primary);font-weight:500;text-decoration:none}
.it-job-id:hover{text-decoration:underline}

/* Date cell */
.it-date-cell{display:flex;flex-direction:column;gap:3px}
.it-date-row{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--it-text-main);font-weight:500}
.it-date-row svg{color:var(--it-text-light);flex-shrink:0;width:12px;height:12px}
.it-date-row .it-mode{color:var(--it-text-muted);font-size:10px;font-weight:400}

/* Person cell */
.it-person-cell{display:flex;flex-direction:column;gap:2px}
.it-person-name{font-weight:600;font-size:11px;color:var(--it-text-main)}
.it-person-company{font-size:10px;color:var(--it-text-muted)}

/* Stage / badge */
.it-stage-cell{display:flex;flex-direction:column;gap:3px}
.it-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:500;width:fit-content}
.it-badge-blue{background:#dbeafe;color:#1e40af}.it-badge-purple{background:#f3e8ff;color:#6b21a8}
.it-badge-orange{background:#ffedd5;color:#9a3412}.it-badge-red{background:#fee2e2;color:#991b1b}
.it-badge-green{background:#d1fae5;color:#065f46}.it-badge-yellow{background:#fef9c3;color:#854d0e}
.it-badge-gray{background:#e2e8f0;color:#374151}.it-badge-pink{background:#fce7f3;color:#9d174d}
.it-stage-status{display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:500}
.it-stage-status.pending{color:var(--it-warning)}
.it-stage-status svg{width:10px;height:10px}

/* Feedback cell */
.it-feedback-cell{display:flex;flex-direction:column;gap:4px}
.it-feedback-btn{width:fit-content;padding:4px 10px;border-radius:var(--it-radius);font-size:10px;font-weight:600;font-family:'Poppins',sans-serif;cursor:pointer;transition:var(--it-transition);display:inline-flex;align-items:center;gap:3px;border:1px solid transparent}
.it-feedback-btn-outline-green{border-color:var(--it-success);color:var(--it-success);background:transparent}
.it-feedback-btn-outline-green:hover{background:var(--it-success-light)}
.it-feedback-btn-outline-orange{border-color:var(--it-orange);color:var(--it-orange);background:transparent}
.it-feedback-btn-outline-orange:hover{background:var(--it-orange-light)}

/* Footer */
.it-table-footer{display:flex;justify-content:space-between;align-items:center;padding:10px 14px;border-top:1px solid var(--it-border-light);font-size:10px;color:var(--it-text-muted)}
.it-pagination{display:flex;align-items:center;gap:3px}
.it-page-btn{min-width:26px;height:26px;display:flex;align-items:center;justify-content:center;border-radius:var(--it-radius);border:none;background:transparent;color:var(--it-text-muted);font-size:11px;font-family:'Poppins',sans-serif;font-weight:500;cursor:pointer;transition:var(--it-transition);padding:0 6px}
.it-page-btn:hover{background:var(--it-bg-main);color:var(--it-text-main)}
.it-page-btn.active{background:var(--it-primary);color:white}
.it-page-btn:disabled{opacity:.4;cursor:not-allowed}
.it-page-btn svg{width:14px;height:14px}

.it-loading{display:none;padding:30px;text-align:center;color:var(--it-text-muted);font-size:12px}
.it-loading.show{display:block}
#benchInterviewRows{display:contents}
#kt_toolbar{display:none!important}
#kt_content{margin-top:0!important}
#kt_post{margin-top:0!important}

@media(max-width:1150px){.it-stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){.it-stats-grid{grid-template-columns:1fr 1fr}.it-filters-bar{flex-direction:column;align-items:stretch}.it-search-box{max-width:none!important;width:100%!important}}
</style>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
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
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></svg>
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
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
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
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
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
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="kt_tagify_2" placeholder="Search candidate, job, recruiter..." autocomplete="off">
          </div>
          <input type="hidden" id="sortingfilter" value="">
          <div class="it-filter-dropdown" style="min-width:200px;">
            <button class="it-filter-trigger" onclick="toggleFilterMenu(this)" type="button">
              <span class="it-label">Sort</span>
              <span class="it-value" id="filter-sort-label">Newest Interviews</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
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
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
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
          <div class="it-table-footer">
            <div id="showingText">Loading...</div>
            <div style="display:flex;align-items:center;gap:14px;">
              <div class="it-pagination" id="paginationbenchCandidateInterviewSchedule"></div>
            </div>
          </div>
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

// ---- Data fetch ----
function fetchBenchInterviewScheduleCandidateList(page = 1) {
    $('.loadingMessagebenchCandidateInterviewScheduleAjax').addClass('show');
    return $.ajax({
        url: `{!! route('service_company.bench-candidates.benchinterviewdata_ajax') !!}?page=${page}`,
        type: 'GET',
        data: {
            custom_search: $('#kt_tagify_2').val(),
            custom_hiringtype: $('#sortingfilter').val()
        },
        success: function(response) {
            $('.loadingMessagebenchCandidateInterviewScheduleAjax').removeClass('show');
            if (response.total !== undefined) {
                $('#total_record').html(response.total + ' record(s)');
                $('#showingText').text('Showing ' + response.total + ' entries');
            }
            $('#benchInterviewRows').html(response.html);
            $('#paginationbenchCandidateInterviewSchedule').html(response.pagination);

            if (response.stats) {
                $('#stat-interview-month').text(response.stats.interview_month ?? 0);
                $('#stat-today').text(response.stats.today ?? 0);
                $('#stat-upcoming').text(response.stats.upcoming ?? 0);
                $('#stat-feedback-pending').text(response.stats.feedback_pending ?? 0);
            }
        },
        error: function() {
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

    $(document).on('click', '#paginationbenchCandidateInterviewSchedule .page-link, #paginationbenchCandidateInterviewSchedule [data-page]', function(e) {
        e.preventDefault();
        var page = $(this).data('page') || 1;
        if (!page || $(this).hasClass('disabled')) return;
        fetchBenchInterviewScheduleCandidateList(page);
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
