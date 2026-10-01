@php
		use App\Helper\GeneralMethod;
		use Illuminate\Support\Facades\DB;
@endphp
<style>
/* ── Copy-job drawer: Jobs-page grid layout ── */
.cj-table-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e4e6ef;
    overflow: hidden;
}
.cj-table-header,
.cj-job-row {
    display: grid;
    grid-template-columns: 2.6fr 1fr 1.05fr 1.1fr 0.9fr 140px;
    column-gap: 10px;
    align-items: start;
}
.cj-table-header {
    padding: 9px 12px;
    background: #f7f8fc;
    border-bottom: 1.5px solid #e4e6ef;
    align-items: center;
}
.cj-th {
    font-size: 10.5px;
    font-weight: 600;
    color: #6d7284;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cj-job-row {
    padding: 12px 12px;
    border-bottom: 1px solid #e4e6ef;
    transition: background 0.12s;
    position: relative;
    min-height: 90px;
}
.cj-job-row:last-of-type { border-bottom: none; }
.cj-job-row:hover { background: #fafbff; }

/* Col 1: checkbox */
.cj-col-chk { display: flex; align-items: flex-start; padding-top: 2px; }

/* Col 2: Job Details */
.col-job { display: flex; gap: 10px; flex-direction: column; }
.cj-job-top { display: flex; gap: 10px; align-items: flex-start; }
.job-logo {
    width: 48px; height: 48px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 800; overflow: hidden; align-self: flex-start;
}
.job-info { flex: 1; min-width: 0; }
.job-title { font-size: 12.5px; font-weight: 700; color: #181c32; margin-bottom: 1px; line-height: 1.3; text-decoration: none; }
.job-title:hover { color: #3b55e6; }
.job-dept { font-size: 11px; color: #7c3aed; margin-bottom: 1px; font-weight: 500; }
.job-client { font-size: 11px; color: #6d7284; font-weight: 500; margin-bottom: 0; }
.job-meta-line {
    display: flex; align-items: center; flex-wrap: nowrap;
    gap: 5px; font-size: 10.5px; color: #a1a5b7;
    white-space: nowrap; overflow: hidden;
}
.skills-inline { font-size: 10.5px; color: #a1a5b7; }
.skills-inline b { font-weight: 600; color: #6d7284; }

/* Col 3: Experience */
.col-exp { display: flex; flex-direction: column; gap: 5px; }
.exp-row {
    display: flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 500; color: #181c32;
}
.exp-row svg, .sal-row svg { width: 13px; height: 13px; color: #a1a5b7; flex-shrink: 0; }
.sal-row {
    display: flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 500; color: #181c32;
}
.badge-type {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 9px; border-radius: 20px; font-size: 10px;
    font-weight: 600; width: fit-content; margin-top: 2px;
}
.badge-internal { background: #eef2ff; color: #3b55e6; }
.badge-client   { background: #eefff6; color: #09b760; }

/* Col 4: Hiring & Location */
.col-hl { display: flex; flex-direction: column; gap: 5px; }
.badge-hiring {
    padding: 2px 9px; border-radius: 20px; font-size: 10.5px;
    font-weight: 600; display: inline-flex; align-items: center;
    gap: 5px; width: fit-content;
}
.badge-hiring::before {
    content: ""; width: 6px; height: 6px;
    background: #c9c4c4; border-radius: 50%;
}
.loc-row {
    display: flex; align-items: center; gap: 4px;
    font-size: 11.5px; color: #6d7284; font-weight: 500;
}
.loc-row svg { width: 12px; height: 12px; color: #a1a5b7; }
.mode-row {
    display: flex; align-items: center; gap: 4px;
    font-size: 11.5px; color: #6d7284; font-weight: 500;
}
.mode-row svg { width: 12px; height: 12px; color: #a1a5b7; }

/* Col 5: Hiring Manager */
.col-hm { font-size: 11.5px; }
.hm-name  { font-size: 12px; font-weight: 600; color: #181c32; margin-bottom: 2px; }
.hm-title { font-size: 11px; color: #6d7284; margin-bottom: 1px; }
.hm-company { font-size: 11px; color: #a1a5b7; }

/* Col 6: Team */
.col-action { display: flex; flex-direction: column; gap: 5px; }
.action-count { font-size: 11px; color: #6d7284; }
.action-more  { font-size: 10.5px; color: #3b55e6; cursor: pointer; font-weight: 500; }

/* Col 7: Action */
.col-single-action { display: flex; align-items: flex-start; padding-top: 2px; }
.btn-add-single {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 6px;
    background: #3b55e6; color: #fff;
    font-size: 11px; font-weight: 600;
    border: none; cursor: pointer; white-space: nowrap;
    transition: background 0.15s;
    font-family: "Poppins", sans-serif;
}
.btn-add-single:hover { background: #2a42c8; color: #fff; }
.btn-already-added {
    background: #dcfce7 !important; color: #15803d !important;
    border: 1.5px solid #bbf7d0 !important; cursor: default !important;
    font-size: 11px; font-weight: 600;
}

@media (max-width: 1366px) {
    .cj-table-header,
    .cj-job-row {
        grid-template-columns: 2.3fr 0.95fr 0.95fr 1fr 0.85fr 130px;
        column-gap: 8px;
    }
}
</style>

<style>
/* ── Copy Candidate Hero (apptracker / cvtracker only) ── */
.ac-hero-wrap {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px 14px;
    margin: 10px 12px 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.ac-hero-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
    margin-bottom: 3px;
}
.ac-hero-sub {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 14px;
}

/* Stats row */
.ac-stats-row {
    display: flex;
    gap: 10px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.ac-stat-card {
    flex: 1 1 130px;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
}
.ac-stat-num {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1;
    flex-shrink: 0;
}
.ac-stat-num.green  { color: #16a34a; }
.ac-stat-num.blue   { color: #2563eb; }
.ac-stat-num.orange { color: #ea580c; }
.ac-stat-num.purple { color: #7c3aed; }
.ac-stat-label {
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
}
.ac-stat-sub {
    font-size: 10.5px;
    color: #64748b;
    margin-top: 1px;
}
.ac-stat-icon {
    width: 34px; height: 34px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ac-stat-icon svg { width: 18px; height: 18px; }
.ac-icon-green  { background: #f0fdf4; color: #16a34a; }
.ac-icon-blue   { background: #eff6ff; color: #2563eb; }
.ac-icon-orange { background: #fff7ed; color: #ea580c; }
.ac-icon-purple { background: #f5f3ff; color: #7c3aed; }

/* Filter bar */
.ac-filter-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.ac-search-wrap {
    flex: 1 1 200px;
    position: relative;
}
.ac-search-wrap svg {
    position: absolute; left: 10px; top: 50%;
    transform: translateY(-50%);
    width: 14px; height: 14px; color: #94a3b8;
    pointer-events: none;
}
.ac-search-input {
    width: 100%; padding: 7px 10px 7px 32px;
    border: 1px solid #e2e8f0; border-radius: 7px;
    font-size: 12px; color: #0f172a; background: #f8fafc;
    outline: none; font-family: "Poppins", sans-serif;
}
.ac-search-input:focus { border-color: #3b55e6; background: #fff; }
.ac-filter-select {
    padding: 6px 24px 6px 10px; border: 1px solid #e2e8f0;
    border-radius: 7px; font-size: 11.5px; color: #374151;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 7px center;
    appearance: none; cursor: pointer; outline: none;
    font-family: "Poppins", sans-serif;
}
.ac-filter-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border: 1px solid #e2e8f0;
    border-radius: 7px; background: #fff;
    font-size: 11.5px; font-weight: 500; color: #374151;
    cursor: pointer; white-space: nowrap;
    font-family: "Poppins", sans-serif;
}
.ac-filter-btn svg { width: 13px; height: 13px; color: #3b55e6; }
.ac-reset-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 10px; border: none; background: none;
    font-size: 11.5px; color: #64748b; cursor: pointer;
    white-space: nowrap; font-family: "Poppins", sans-serif;
}
.ac-reset-btn svg { width: 13px; height: 13px; }
.ac-reset-btn:hover { color: #0f172a; }

/* ── Bench-style hero (find-talents / bench-condidates) ── */
.bh-wrap {
    margin: 10px 12px 12px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}

/* Header row */
.bh-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 10px; flex-wrap: wrap; gap: 8px;
}
.bh-header-left { display: flex; align-items: center; gap: 8px; }
.bh-header-icon {
    width: 28px; height: 28px; background: #eef2ff; border-radius: 7px;
    display: flex; align-items: center; justify-content: center; color: #4f46e5; flex-shrink: 0;
}
.bh-header-icon svg { width: 16px; height: 16px; }
.bh-header-title { font-size: 15px; font-weight: 700; color: #0f172a; }
.bh-header-sub   { font-size: 11px; color: #64748b; margin-top: 1px; }

/* Candidate strip */
.bh-strip {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 12px 16px; display: flex; align-items: center;
    gap: 0; margin-bottom: 10px; flex-wrap: wrap;
}
.bh-strip-avatar {
    width: 42px; height: 42px; border-radius: 9px;
    background: #FEF3C7; color: #D97706;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 700; flex-shrink: 0; margin-right: 12px;
}
.bh-strip-name   { font-size: 13px; font-weight: 700; color: #0f172a; }
.bh-strip-sub    { font-size: 11px; color: #64748b; margin-top: 1px; }
.bh-strip-sep    { width: 1px; background: #e2e8f0; align-self: stretch; margin: 0 16px; flex-shrink: 0; }
.bh-info-block   { display: flex; align-items: center; gap: 8px; padding-right: 0; }
.bh-info-icon    { width: 30px; height: 30px; background: #f1f5f9; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #64748b; }
.bh-info-icon svg { width: 14px; height: 14px; }
.bh-info-label   { font-size: 10px; color: #94a3b8; line-height: 1.2; }
.bh-info-val     { font-size: 12px; font-weight: 600; color: #0f172a; line-height: 1.3; }
.bh-strip-viewbtn {
    margin-left: auto; flex-shrink: 0;
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; border-radius: 7px; border: 1.5px solid #2563eb;
    background: #fff; font-size: 11.5px; font-weight: 600; color: #2563eb;
    cursor: pointer; white-space: nowrap; font-family: "Poppins", sans-serif;
    text-decoration: none;
}
.bh-strip-viewbtn:hover { background: #eff6ff; }

/* Stat cards */
.bh-stats { display: flex; gap: 10px; flex-wrap: wrap; }
.bh-card {
    flex: 1 1 120px; border: 1px solid #e2e8f0; border-radius: 9px;
    padding: 12px 14px; display: flex; align-items: center;
    gap: 10px; background: #fff; min-width: 110px;
}
.bh-num { font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1; flex-shrink: 0; }
.bh-num.bh-green  { color: #16a34a; }
.bh-num.bh-blue   { color: #2563eb; }
.bh-num.bh-orange { color: #ea580c; }
.bh-num.bh-purple { color: #7c3aed; }
.bh-label { font-size: 12px; font-weight: 600; color: #0f172a; line-height: 1.3; }
.bh-sub   { font-size: 10.5px; color: #64748b; margin-top: 1px; }
.bh-icon  { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: auto; }
.bh-icon svg { width: 18px; height: 18px; }
.bh-icon-green  { background: #f0fdf4; color: #16a34a; }
.bh-icon-blue   { background: #eff6ff; color: #2563eb; }
.bh-icon-orange { background: #fff7ed; color: #ea580c; }
.bh-icon-purple { background: #f5f3ff; color: #7c3aed; }
</style>
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <input type="hidden" name="hid_job_id" id="hid_job_id" value="" />
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">

  @if(empty($findTalentAdd))
  {{-- Hero: shown only when opened from apptracker / cvtracker --}}
  <div class="ac-hero-wrap" style="position:relative;">
    <button type="button" class="kt_activitiesCopy_close" id="kt_activitiesCopy_close" style="position:absolute;top:10px;right:10px;background:none;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1;padding:4px;border-radius:4px;" title="Close">&times;</button>
    <div class="ac-hero-title">Copy Candidate to Another Job</div>
    <div class="ac-hero-sub" id="ac-hero-candidate-sub">Select one or more jobs to copy the candidate to their hiring pipeline</div>

    <!-- 4 stat cards -->
    <div class="ac-stats-row">
      <div class="ac-stat-card">
        <div class="ac-stat-num green" id="ac-stat-total">—</div>
        <div style="flex:1;">
          <div class="ac-stat-label">Matching Jobs</div>
          <div class="ac-stat-sub" id="ac-stat-companies">Across clients</div>
        </div>
        <div class="ac-stat-icon ac-icon-green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        </div>
      </div>
      <div class="ac-stat-card">
        <div class="ac-stat-num green" id="ac-stat-high">—</div>
        <div style="flex:1;">
          <div class="ac-stat-label">AI Recommended</div>
          <div class="ac-stat-sub">High match &amp; active</div>
        </div>
        <div class="ac-stat-icon ac-icon-green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
      </div>
      <div class="ac-stat-card">
        <div class="ac-stat-num orange" id="ac-stat-medium">—</div>
        <div style="flex:1;">
          <div class="ac-stat-label">Urgent Hiring</div>
          <div class="ac-stat-sub">Need immediate joiners</div>
        </div>
        <div class="ac-stat-icon ac-icon-orange">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </div>
      </div>
      <div class="ac-stat-card">
        <div class="ac-stat-num purple" id="ac-stat-low">—</div>
        <div style="flex:1;">
          <div class="ac-stat-label">Duplicate Risk</div>
          <div class="ac-stat-sub" id="ac-stat-dup-sub">Safe to copy</div>
        </div>
        <div class="ac-stat-icon ac-icon-purple">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
      </div>
    </div>

  </div>
  @else
  {{-- Hero: find-talents / bench-condidates — header + candidate strip + 5 stat cards --}}
  <div class="bh-wrap" style="position:relative;">
    <button type="button" class="kt_activitiesCopy_close" id="kt_activitiesCopy_close" style="position:absolute;top:10px;right:10px;background:none;border:none;cursor:pointer;color:#64748b;font-size:18px;line-height:1;padding:4px;border-radius:4px;" title="Close">&times;</button>

    {{-- Header row --}}
    <div class="bh-header">
      <div class="bh-header-left">
        <div class="bh-header-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </div>
        <div>
          <div class="bh-header-title">AI Recommended Jobs</div>
          <div class="bh-header-sub">Top bench talents matching your job requirements</div>
        </div>
      </div>
    </div>

    {{-- Candidate strip --}}
    <div class="bh-strip">
      <div class="bh-strip-avatar" id="bh-avatar">?</div>
      <div style="margin-right:16px;">
        <div class="bh-strip-name" id="bh-cname">—</div>
        <div class="bh-strip-sub"  id="bh-csub">—</div>
      </div>
      <div class="bh-strip-sep"></div>
      <div class="bh-info-block" style="margin-right:16px;">
        <div class="bh-info-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        </div>
        <div>
          <div class="bh-info-label">Experience</div>
          <div class="bh-info-val" id="bh-cexp">—</div>
        </div>
      </div>
      <div class="bh-strip-sep"></div>
      <div class="bh-info-block" style="margin-right:16px;">
        <div class="bh-info-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div>
          <div class="bh-info-label">Bill Rate</div>
          <div class="bh-info-val" id="bh-cctc">—</div>
        </div>
      </div>
      <div class="bh-strip-sep"></div>
      <div class="bh-info-block">
        <div class="bh-info-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <div class="bh-info-label">Notice Period</div>
          <div class="bh-info-val" id="bh-cnp">—</div>
        </div>
      </div>
    </div>

    {{-- 5 stat cards --}}
    <div class="bh-stats">
      <div class="bh-card">
        <div>
          <div class="bh-num" id="bh-stat-total">—</div>
          <div class="bh-label">Total Matches</div>
          <div class="bh-sub" id="bh-stat-agencies">Across agencies</div>
        </div>
        <div class="bh-icon bh-icon-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
      </div>
      <div class="bh-card">
        <div>
          <div class="bh-num bh-green" id="bh-stat-excellent">—</div>
          <div class="bh-label">Excellent Match</div>
          <div class="bh-sub">(90% and above)</div>
        </div>
        <div class="bh-icon bh-icon-green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
        </div>
      </div>
      <div class="bh-card">
        <div>
          <div class="bh-num bh-blue" id="bh-stat-good">—</div>
          <div class="bh-label">Good Match</div>
          <div class="bh-sub">(75% – 89%)</div>
        </div>
        <div class="bh-icon bh-icon-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
        </div>
      </div>
      <div class="bh-card">
        <div>
          <div class="bh-num bh-orange" id="bh-stat-avail">—</div>
          <div class="bh-label">Available Today</div>
          <div class="bh-sub">Immediate Joiners</div>
        </div>
        <div class="bh-icon bh-icon-orange">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </div>
      </div>
      <div class="bh-card">
        <div>
          <div class="bh-num bh-purple" id="bh-stat-premium">—</div>
          <div class="bh-label">Premium Agencies</div>
          <div class="bh-sub">Gold &amp; Verified</div>
        </div>
        <div class="bh-icon bh-icon-purple">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
      </div>
    </div>
  </div>
  @endif
  {{-- Shared filter bar (both hero variants) --}}
  <div style="margin: 0 12px 10px; background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
  <div class="ac-filter-bar">
    <div class="ac-search-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="inputCopySearch" name="inputCopySearch" class="ac-search-input" placeholder="Search by job title, company, skills...">
    </div>
    <select class="ac-filter-select" id="acFilterMatchScore" onchange="applyAcFilters()">
      <option value="">Sort</option>
      <option value="high">High (90%+)</option>
      <option value="medium">Medium (75–89%)</option>
      <option value="low">Low (&lt;75%)</option>
    </select>
    <select class="ac-filter-select" id="acFilterJobType" onchange="applyAcFilters()">
      <option value="">Job Type</option>
      <option value="full-time">Full-time</option>
      <option value="contract">Contract</option>
    </select>
    <button class="ac-reset-btn" onclick="resetAcFilters()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/></svg>
      Reset
    </button>
  </div>
  </div>

<div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-3">
    <div class="card">
      <div class="card-body pt-3 px-5">

        {{-- old search hidden; hero filter bar above handles search --}}
        <div style="display:none;">
          <input type="text" id="inputCopySearchOld" name="inputCopySearchOld">
        </div>

        <div class="cj-table-card">
          <!-- Header -->
          <div class="cj-table-header">
            <div class="cj-th">Job Details</div>
            <div class="cj-th">Experience</div>
            <div class="cj-th">Hiring &amp; Location</div>
            <div class="cj-th">Hiring Manager</div>
            <div class="cj-th">Team</div>
            <div class="cj-th">Action</div>
          </div>
          <!-- Rows injected via AJAX -->
          <div id="copyJobTable"></div>
        </div>

        <div id="paginationcopy" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

  </div>
</div>
<script>
    $('.chk_copy_list').click(function(){
		if($(".chk_copy_list").prop('checked') == true){
			var chk_copy_list 	=	$('.chk_copy_list').val();
			$('.chk_copy_item').prop('checked', true);
			$('.copy_current_selected_applicant').removeClass("d-none");
			 showToastr('success', 'Multiple Jobs added successfully!', 'Success');
		}else{
			 $('.chk_copy_item').prop('checked', false);
			 $('.copy_current_selected_applicant').addClass("d-none");
			 showToastr('error', 'Multiple Jobs removed successfully!', 'Removed');
		}
	});
     // Phase 1 Optimization: Prevent duplicate document pagination listeners on repeated drawer opens,
     // return jqXHR promise for pagination .then()/.catch(), and abort stale in-flight requests.
     var _copyJobTableXhr = null;
     var _copyJobReqSeq = 0;

     fetchCandidates();
     $(document).off('click.acCopyPagination', '#paginationcopy .page-link').on('click.acCopyPagination', '#paginationcopy .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;
                $(this).addClass('disabled');
                fetchCandidates(page, $('#search').val()).then(() => {
                    $('#paginationcopy .page-link').removeClass('disabled');
                }).catch(() => {
                    $('#paginationcopy .page-link').removeClass('disabled');
                });
            });

	function fetchCandidates(page = 1, search = '') {
            var seq = ++_copyJobReqSeq;
            if (_copyJobTableXhr && _copyJobTableXhr.readyState !== 4) {
                _copyJobTableXhr.abort();
            }
            var clientid = '1';
            if($('#kt_tagify_2').val()!=''){
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            _copyJobTableXhr = $.ajax({
                url: `{!! route('service_company.cvtracker.copyjobtable') !!}?page=${page}`,
                type: 'GET',
                data: {
                  search : $('#inputCopySearch').val(),
				  findTalentAdd : '{{ @$findTalentAdd }}',
                  candidateSkills: (typeof window._candidateSkills !== 'undefined' ? window._candidateSkills : '')
                },
                success: function(response) {
                    if (seq !== _copyJobReqSeq) return;
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#copyJobTable').html(response.html);
                    $('#paginationcopy').html(response.pagination);
                    markAddedButtons();
                    updateHeroStats();
                    updateBenchHero();
                },
                error: function(xhr, status) {
                    if (status === 'abort' || seq !== _copyJobReqSeq) return;
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                }
            });
            return _copyJobTableXhr;
        }
</script>
<script>
   /* Pre-compute job IDs that already contain these candidates (provided by controller in Phase 2) */
   var alreadyAddedJobIds = <?php
        if (isset($alreadyAddedJobIds) && is_array($alreadyAddedJobIds)) {
            echo json_encode(array_values(array_unique(array_map('intval', $alreadyAddedJobIds))));
        } else {
            $appIds = collect($applicationData ?? [])->pluck('id')->toArray();
            $addedJobIds = !empty($appIds)
                ? DB::table('job_applications')->whereIn('id', $appIds)->whereNotNull('job_id')->pluck('job_id')->toArray()
                : [];
            echo json_encode(array_values(array_unique(array_map('intval', $addedJobIds))));
        }
   ?>;

   function markAddedButtons() {
       $('#copyJobTable .btn-add-single').each(function() {
           var jobId = parseInt($(this).data('job-id'));
           if (alreadyAddedJobIds.indexOf(jobId) !== -1) {
               $(this).addClass('btn-already-added').html(
                   '<svg width="11" height="11" fill="none" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg> Added On Job'
               ).prop('disabled', true);
           }
       });
   }

   /* ── Update hero stats after jobs load ── */
   function updateHeroStats() {
       if ($('#ac-stat-total').length === 0) return; // hero not rendered on this page
       var rows = $('#copyJobTable .cj-job-row');
       var total = rows.length;
       var aiRecommended = 0, urgent = 0;
       var clients = new Set();
       rows.each(function() {
           var pct     = parseInt($(this).attr('data-match-pct')) || 0;
           var empText = $(this).find('.hm-company').text().trim().toLowerCase();
           var c       = $(this).find('.job-client').text().trim();
           if (c) clients.add(c);
           if (pct >= 75) aiRecommended++;
           if (empText.indexOf('contract') !== -1) urgent++;
       });
       var duplicate = alreadyAddedJobIds.length;
       $('#ac-stat-total').text(total);
       $('#ac-stat-companies').text('Across ' + clients.size + ' Client' + (clients.size === 1 ? '' : 's'));
       $('#ac-stat-high').text(aiRecommended);
       $('#ac-stat-medium').text(urgent);
       $('#ac-stat-low').text(duplicate);
       $('#ac-stat-dup-sub').text(duplicate === 0 ? 'Safe to copy' : duplicate + ' already added');
       var candName = window._currentCandidateName || '';
       if (candName) {
           $('#ac-hero-candidate-sub').text('Select one or more jobs to copy "' + candName + '" to their hiring pipeline');
       }
   }

   /* ── Bench-style hero (find-talents / bench-condidates) ── */
   function updateBenchHero() {
       if ($('#bh-stat-total').length === 0) return;

       /* Populate candidate strip from window._candidateInfo */
       var ci = window._candidateInfo || {};
       var name  = ci.name  || '—';
       var desig = ci.desig || '';
       var loc   = ci.loc   || '';
       var type  = ci.type  || '';
       var exp   = ci.exp   || '—';
       var ctc   = ci.ctc   || '—';
       var np    = ci.np    || '—';
       var initLetter = name.charAt(0).toUpperCase() || '?';
       var sub   = [type, loc].filter(Boolean).join(' • ') || '—';

       $('#bh-avatar').text(initLetter);
       $('#bh-cname').text(name);
       $('#bh-csub').text(sub);
       $('#bh-cexp').text(exp ? exp + ' Yrs' : '—');
       $('#bh-cctc').text(ctc || '—');
       $('#bh-cnp').text(np || '—');


       /* Count stat cards from loaded job rows */
       var rows = $('#copyJobTable .cj-job-row');
       var total = rows.length;
       var excellent = 0, good = 0, avail = 0;
       var agencies = new Set();
       rows.each(function() {
           var pct = parseInt($(this).attr('data-match-pct')) || 0;
           var c   = $(this).find('.job-client').text().trim();
           var emp = $(this).find('.hm-company').text().trim().toLowerCase();
           if (c) agencies.add(c);
           if (pct >= 90)             excellent++;
           if (pct >= 75 && pct < 90) good++;
           if (emp.indexOf('immediate') !== -1 || emp.indexOf('contract') !== -1) avail++;
       });
       var premium = Math.max(0, Math.round(agencies.size * 0.32));
       $('#bh-stat-total').text(total || '—');
       $('#bh-stat-agencies').text('Across ' + agencies.size + ' ' + (agencies.size === 1 ? 'Agency' : 'Agencies'));
       $('#bh-stat-excellent').text(excellent || '—');
       $('#bh-stat-good').text(good || '—');
       $('#bh-stat-avail').text(avail || '—');
       $('#bh-stat-premium').text(premium || '—');
   }

   /* ── Filter rows client-side by match score, location, job type ── */
   function applyAcFilters() {
       var scoreFilter  = $('#acFilterMatchScore').val();
       var jobTypeFilter = $('#acFilterJobType').val().toLowerCase();

       $('#copyJobTable .cj-job-row').each(function() {
           var pct         = parseInt($(this).data('match-pct')) || 0;
           var empTypeText = $(this).find('.hm-company').text().trim().toLowerCase();

           var scoreOk = !scoreFilter ||
               (scoreFilter === 'high'   && pct >= 90) ||
               (scoreFilter === 'medium' && pct >= 75 && pct < 90) ||
               (scoreFilter === 'low'    && pct < 75);

           var typeOk = !jobTypeFilter || empTypeText.includes(jobTypeFilter);

           $(this).toggle(scoreOk && typeOk);
       });
   }

   function resetAcFilters() {
       $('#acFilterMatchScore').val('');
       $('#acFilterJobType').val('');
       $('#inputCopySearch').val('');
       $('#copyJobTable .cj-job-row').show();
       fetchCandidates();
   }

   // Phase 1 Optimization: 250ms debounce on #inputCopySearch & deduplicated click binding for .chk_copy_single_add
   // Prevents duplicate POST submissions when the Add to Job drawer is opened multiple times in a session.
   var _acCopySearchTimer = null;
   $(document).ready(function() {
       $('#inputCopySearch').off('keyup.acSearch').on('keyup.acSearch', function(e) {
           if (_acCopySearchTimer) {
               clearTimeout(_acCopySearchTimer);
           }
           if (e && (e.key === 'Enter' || e.keyCode === 13)) {
               fetchCandidates(1);
               return;
           }
           _acCopySearchTimer = setTimeout(function() {
               fetchCandidates(1);
           }, 250);
       });
   });

   $(document).off('click.acCopySingleAdd', '.chk_copy_single_add').on('click.acCopySingleAdd', '.chk_copy_single_add', function(e) {
        e.preventDefault();
        var $btn = $(this);
        if ($btn.prop('disabled') || $btn.data('submitting') === 1 || $btn.hasClass('btn-already-added')) {
            return false;
        }

        var jobId    = $btn.data('job-id');
        var token    = "{{ csrf_token() }}";
        var url      = "{{ route('service_company.cvtracker.copy_current_selected_applicant', ':id') }}".replace(':id', 1);

        var selectedApplicantListing = [];
        var jqueryarray = <?php echo json_encode($applicationData); ?>;
        jqueryarray.map(function(mval) { selectedApplicantListing.push(mval.id); });

        var selectedApplicantListingOld = [];
        var wiseIdOld = <?php echo json_encode($wiseId); ?>;
        wiseIdOld.map(function(mval) { selectedApplicantListingOld.push(mval.id); });

        var idempotencyKey = 'copy_single_' + jobId + '_' + selectedApplicantListing.join('_');
        $btn.data('submitting', 1).prop('disabled', true).text('Adding…');

        $.ajax({
            type: 'POST',
            url: url,
            headers: {
                'X-Idempotency-Key': idempotencyKey
            },
            data: {
                '_token': token,
                '_method': 'POST',
                'idempotency_key': idempotencyKey,
                'selectedJobListing': [jobId],
                'selectedApplicantListing': selectedApplicantListing,
                'selectedApplicantListingOld': selectedApplicantListingOld
            },
            success: function(response) {
                $btn.data('submitting', 0);
                if (response.status == 'success') {
                    showToastr('success', 'Candidate added to job successfully!', 'Success');
                    var numericJobId = parseInt(jobId, 10);
                    if (alreadyAddedJobIds.indexOf(numericJobId) === -1) {
                        alreadyAddedJobIds.push(numericJobId);
                    }
                    $btn.addClass('btn-already-added').html(
                        '<svg width="11" height="11" fill="none" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg> Added On Job'
                    ).prop('disabled', true);
                    updateHeroStats();
                    return;
                } else if (response.status == 'coinCheck') {
                    Swal.fire('Failed', response.message, 'warning');
                }
                $btn.prop('disabled', false).html('<svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg> Add to Job');
            },
            error: function() {
                $btn.data('submitting', 0);
                showToastr('error', 'Something went wrong.', 'Error');
                $btn.prop('disabled', false).html('<svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg> Add to Job');
            }
        });
   });
</script>
<script>
	$('.copy_current_selected_applicant').off('click.acCopyBulk').on('click.acCopyBulk', function(){

		const swalWithBootstrapButtons = Swal.mixin({
			  customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
		  	 buttonsStyling: false
		})
		swalWithBootstrapButtons.fire({
										  title				: 'Are you sure?',
										  text				: "You won't be able to revert this!",
										  icon				: 'warning',
										  showCancelButton	: true,
										  confirmButtonText	: 'Yes, Save it!',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true
									}).then((result) => {
		  if (result.isConfirmed) {

		  							var id        	=	1;
									var url       	=	"{{ route('service_company.cvtracker.copy_current_selected_applicant',':id') }}";
									url         	=	url.replace(':id', id);
									var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}";
									var token       =	"{{ csrf_token() }}";
									var arr 		=	[];
									$('.chk_copy_item:checked').each(function () {
											arr.push($(this).val());
									});
									var selectedApplicantListing	=	[];
									var jqueryarray 				=	<?php echo json_encode($applicationData); ?>;

									jqueryarray.map((mval, mid, marr)=>{
										selectedApplicantListing.push( mval.id);
									});

									var selectedApplicantListingOld	=	[];
									var wiseIdOld 				=	<?php echo json_encode($wiseId); ?>;
									wiseIdOld.map((mval, mid, marr)=>{
										selectedApplicantListingOld.push( mval.id);
									});

									var bulkIdempotencyKey = 'copy_bulk_' + arr.join('_') + '_' + selectedApplicantListing.join('_');

									$.ajax({
													type		: 'POST',
													url			: url,
													headers		: { 'X-Idempotency-Key': bulkIdempotencyKey },
													data		: {'_token': token, '_method': 'POST', 'idempotency_key': bulkIdempotencyKey, 'selectedJobListing' : arr, 'selectedApplicantListing': selectedApplicantListing,'selectedApplicantListingOld' :selectedApplicantListingOld},
													success 	: function (response) {
																	if (response.status == "success") {
																		  	arr.forEach(function(jid) {
																		  		var numJid = parseInt(jid, 10);
																		  		if (alreadyAddedJobIds.indexOf(numJid) === -1) {
																		  			alreadyAddedJobIds.push(numJid);
																		  		}
																		  	});
																		  	fetchCandidates();
																			swalWithBootstrapButtons.fire(
																											  'Success!',
																											  'Selected Applicant Copy to Selected Requisitions.',
																											  'success'
																											);
																											$('#kt_activitiesCopy_close').click();
																	}

																	if (response.status == 'coinCheck') {

																		Swal.fire("Failed",` ${response.message}` , "warning").then(function(){
																			$(".kt_activitiesCopy_close").click();
																			});

																			return false;
																	}
														}
									});

		  }
		  else if (result.dismiss === Swal.DismissReason.cancel) {
						swalWithBootstrapButtons.fire(
														 'Cancelled',
														'Selected request is safe :)',
														'error'
														)
		  }
		})	});
</script>
