@extends('layouts.service_company_layout')
@push('head-script')
<style>
/* ═══════════════════════════════════════════════
   RECOMMENDED INTERNAL JOBS — AppTracker-pattern redesign (rrj-)
═══════════════════════════════════════════════ */
#kt_content{ margin-top:-70px !important; }
.rrj-page{ font-family:'Poppins',sans-serif; background:#F2F4F8; min-height:100vh; padding:18px 0 40px; }
.rrj-page *{ font-family:'Poppins',sans-serif; box-sizing:border-box; }

/* ══ 1. PROFILE HERO ════════════════════════════ */
.rrj-hero{ background:#fff; padding:22px 28px; display:flex; align-items:center; justify-content:space-between; gap:24px; flex-wrap:wrap; border-bottom:1px solid #F0F2F5; margin-bottom:4px; }
.rrj-hero-left{ display:flex; align-items:center; gap:18px; flex:1; min-width:260px; }
.rrj-avatar{ width:64px; height:64px; border-radius:16px; object-fit:cover; border:3px solid #E8EDF5; }
.rrj-avatar-init{ width:64px; height:64px; border-radius:16px; background:linear-gradient(135deg,#4F46E5,#7C3AED); display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:700; color:#fff; flex-shrink:0; }
.rrj-name{ font-size:16px; font-weight:700; color:#1A1D23; margin:0 0 3px; }
.rrj-role{ font-size:12px; font-weight:600; color:#374151; margin:0 0 6px; }
.rrj-meta-line{ display:flex; flex-wrap:wrap; gap:16px; }
.rrj-meta-item{ display:flex; align-items:center; gap:5px; font-size:11.5px; font-weight:500; color:#64748B; }
.rrj-meta-item i{ font-size:13px; color:#94A3B8; }

/* ══ 2. TABS (Pool Jobs / Recommend Client Jobs / Recommend Internal Jobs) ══ */
.rrj-tabs{ display:flex; align-items:center; gap:0; flex-shrink:0; flex-wrap:wrap; }
.rrj-tab{ display:flex; flex-direction:column; align-items:flex-start; gap:2px; padding:8px 20px; text-decoration:none; position:relative; }
.rrj-tab+.rrj-tab::before{ content:''; position:absolute; left:0; top:15%; height:70%; width:1px; background:#E5E9F0; }
.rrj-tab-name{ font-size:12px; font-weight:600; color:#64748B; white-space:nowrap; }
.rrj-tab-count{ font-size:17px; font-weight:800; color:#0F172A; line-height:1.1; }
.rrj-tab.rrj-tab-active .rrj-tab-name{ color:#2563EB; }
.rrj-tab.rrj-tab-active .rrj-tab-count{ color:#2563EB; }
.rrj-tab.rrj-tab-active{ border-bottom:3px solid #2563EB; }

/* ══ 3. TOOLBAR — search / sort / filter (AppTracker plf-/simf- pattern) ══ */
.rrj-toolbar-card{ background:#fff; padding:14px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid #F0F2F5; margin-bottom:16px; flex-wrap:wrap; }
.rrj-search-wrap{ flex:1; min-width:220px; position:relative; }
.rrj-search-icon{ position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#CBD5E1; display:flex; }
.rrj-search-input{ width:100%; height:44px; border:1.5px solid #E5E9F0; border-radius:10px; padding:0 16px 0 42px; font-size:13px; font-weight:500; color:#334155; background:#F8FAFC; outline:none; font-family:'Poppins',sans-serif; transition:border-color .18s, box-shadow .18s; }
.rrj-search-input:focus{ border-color:#2563EB; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.07); }
.rrj-sort-wrap{ position:relative; width:200px; flex-shrink:0; }
.rrj-sort-btn{ width:100%; height:44px; border:1.5px solid #E5E9F0; border-radius:10px; background:#F8FAFC; padding:0 12px; display:flex; align-items:center; gap:8px; font-size:13px; font-weight:500; color:#334155; cursor:pointer; }
.rrj-sort-btn svg{ flex-shrink:0; color:#94A3B8; }
.rrj-sort-btn .arrow{ margin-left:auto; transition:transform .15s; color:#94A3B8; }
.rrj-sort-btn.open .arrow{ transform:rotate(180deg); }
.rrj-sort-panel{ position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:50; background:#fff; border:1px solid #E5E9F0; border-radius:10px; box-shadow:0 8px 24px rgba(15,23,42,.09); padding:6px; max-height:260px; overflow-y:auto; }
.rrj-sort-item{ padding:9px 12px; font-size:12.5px; font-weight:500; color:#374151; border-radius:7px; cursor:pointer; }
.rrj-sort-item:hover{ background:#F5F8FF; }
.rrj-sort-item.active{ background:#EFF6FF; color:#2563EB; font-weight:700; }
.rrj-filter-btn{ height:44px; padding:0 16px; border:1.5px solid #E5E9F0; border-radius:10px; background:#fff; color:#374151; font-size:13px; font-weight:600; display:flex; align-items:center; gap:8px; position:relative; flex-shrink:0; }
.rrj-filter-btn:hover{ border-color:#2563EB; color:#2563EB; }
.rrj-count-badge{ font-size:10px; }

/* ══ 4. LOADING SPINNER — viewport-fixed, same as AppTracker's ══ */
.loadingMessagebenchCandidateRecommendedReqAjax{ display:none; position:fixed; inset:0; z-index:1050; align-items:center; justify-content:center; background:rgba(255,255,255,.55); }
.loadingMessagebenchCandidateRecommendedReqAjax .spinner-border{ width:2.75rem; height:2.75rem; }

/* ══ 5. FILTER DRAWER (rrj-) ══════════════════════════════════ */
.rrj-drawer{ background:#fff; }
.rrj-card{ display:flex; flex-direction:column; }
.rrj-header{ display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid #F0F2F5; flex-shrink:0; }
.rrj-header-icon{ width:40px; height:40px; border-radius:10px; background:#EFF6FF; color:#2563EB; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rrj-title{ font-size:16px; font-weight:700; color:#181c32; margin:0; }
.rrj-subtitle{ font-size:12px; color:#94A3B8; margin:2px 0 0; }
.rrj-close{ width:34px; height:34px; border-radius:9px; border:1px solid #E5E9F0; background:#fff; color:#64748B; display:flex; align-items:center; justify-content:center; cursor:pointer; }
.rrj-close:hover{ background:#F8FAFC; }
.rrj-body{ padding:0 24px; overflow-y:auto; }
.rrj-section{ padding:18px 0; border-bottom:1px solid #F5F7FA; }
.rrj-section-last{ border-bottom:none; }
.rrj-section-head{ display:flex; align-items:center; gap:12px; margin-bottom:12px; }
.rrj-icon{ width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rrj-icon-blue{ background:#EFF6FF; color:#2563EB; }
.rrj-icon-green{ background:#ECFDF5; color:#059669; }
.rrj-icon-amber{ background:#FFFBEB; color:#B45309; }
.rrj-icon-purple{ background:#F5F3FF; color:#7C3AED; }
.rrj-icon-rose{ background:#FDF2F8; color:#BE185D; }
.rrj-section-title{ font-size:13.5px; font-weight:700; color:#181c32; }
.rrj-section-desc{ font-size:11.5px; color:#94A3B8; margin-top:1px; }
.rrj-mini-label{ font-size:11px; font-weight:600; color:#64748B; margin-bottom:4px; display:block; }
.rrj-pill-row{ display:flex; flex-wrap:wrap; gap:8px; }
.rrj-pill-row .btn-check{ position:absolute; opacity:0; pointer-events:none; }
.rrj-pill-row label{ margin:0; cursor:pointer; min-width:88px; }
.rrj-footer{ display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 24px; border-top:1px solid #F0F2F5; flex-shrink:0; background:#fff; }
.rrj-footer-count{ display:flex; align-items:center; gap:10px; }
.rrj-footer-icon{ width:38px; height:38px; border-radius:10px; background:#EFF6FF; color:#2563EB; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rrj-footer-number{ font-size:16px; font-weight:800; color:#181c32; line-height:1.1; }
.rrj-footer-label{ font-size:11px; color:#94A3B8; }
.rrj-footer-actions{ display:flex; gap:8px; }

/* Skills Tagify — mandatory star (kept from the legacy modal, restyled) */
.tagify__tag{ display:flex; align-items:center; }
.tagify__tag .star{ margin-left:5px; cursor:pointer; font-size:16px; position:relative; top:-1px; color:#CBD5E1; }
.tagify__tag .star.checked{ color:#EF1632; }

/* ══ 6. RESULTS CARD LIST — agency-card container ══ */
.rrj-results-wrap{ background:#fff; border-radius:14px; border:1px solid #EEF0F4; padding:16px; }

@media(max-width:900px){
    .rrj-hero{ flex-direction:column; }
    .rrj-tabs{ width:100%; }
}
</style>
@endpush

@section('content')

{{-- ── Hidden compat: keep #inputSearch / #status alive purely because
     fetchBenchRecommendedReqCandidateList() still reads them (always empty/
     'active' in practice — same as before this redesign) ── --}}
<div aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;opacity:0;z-index:-9;">
    <input type="hidden" id="inputSearch" value="">
</div>

@php
    $rrj_bench_explore_url = url('service_company/bench-candidates/bench_explore/') . '/' . @$bench_detail->id . '/all';
    $rrj_recommend_req_url = url('service_company/bench-candidates/recommend_req/') . '/' . @$bench_detail->id;
    $rrj_recommend_internal_url = url('service_company/bench-candidates/recommend_internal_req/') . '/' . @$bench_detail->id;
@endphp

@if(in_array('view_jobs', $userPermissions))
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
 <div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-0">
  <div class="rrj-page">

  {{-- ══ SECTION 1 · PROFILE HERO + TABS ══════════════════════ --}}
  <div class="rrj-hero">
    <div class="rrj-hero-left">
      @php
        $rrjCandName = ucwords(@$bench_detail->full_name ?? 'Candidate');
        $rrjInitLtr  = strtoupper(substr($rrjCandName,0,1));
      @endphp
      <div class="rrj-avatar-init">{{ $rrjInitLtr }}</div>
      <div>
        <div class="rrj-name">{{ $rrjCandName }}</div>
        <div class="rrj-role">{{ ucwords(@$bench_detail->current_designation ?? '') }}</div>
        <div class="rrj-meta-line">
          @if(@$bench_detail->total_exp || @$bench_detail->rel_exp)
          <span class="rrj-meta-item"><i class="ti ti-clock"></i>{{ @$bench_detail->total_exp }}-{{ @$bench_detail->rel_exp }} Yrs</span>
          @endif
          <span class="rrj-meta-item">
            <span style="color:#94A3B8;">Bench ID:</span>&nbsp;<span style="font-weight:700;color:#1A56DB;">B-{{ @$bench_detail->id }}</span>
          </span>
          @if(@$bench_detail->city_name)
          <span class="rrj-meta-item"><i class="ti ti-map-pin"></i>{{ @$bench_detail->city_name }}@if(@$bench_detail->state_name), {{ @$bench_detail->state_name }}@endif</span>
          @endif
        </div>
      </div>
    </div>

    <div class="rrj-tabs">
      <a href="{{ $rrj_bench_explore_url }}" class="rrj-tab">
        <span class="rrj-tab-name">Pool Jobs</span>
        <span class="rrj-tab-count" id="bench_explore_pool_id">{{ $recomCount['bench_explore_counter'] ?? 0 }}</span>
      </a>
      <a href="{{ $rrj_recommend_req_url }}" class="rrj-tab">
        <span class="rrj-tab-name">Recommend Client Jobs</span>
        <span class="rrj-tab-count" id="recommend_req_id">{{ $recomCount['recommend_req_counter'] ?? 0 }}</span>
      </a>
      <a href="{{ $rrj_recommend_internal_url }}" class="rrj-tab rrj-tab-active">
        <span class="rrj-tab-name">Recommend Internal Jobs</span>
        <span class="rrj-tab-count" id="recommend_req_internal_id">{{ $recomCount['recommend_internal_req_counter'] ?? 0 }}</span>
      </a>
    </div>
  </div>

  {{-- ══ SECTION 2 · SEARCH / SORT / FILTER TOOLBAR ═══ --}}
  <div class="rrj-toolbar-card">
    <div class="rrj-search-wrap">
      <span class="rrj-search-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      </span>
      <input type="text" id="kt_tagify_2" class="rrj-search-input" placeholder="Search by job title, company or recruiter..." autocomplete="off">
    </div>

    <div class="rrj-sort-wrap" id="rrj_sort_wrap">
      <button type="button" class="rrj-sort-btn" id="rrj_sort_btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
        <span id="rrj_sort_label">Best Match</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </button>
      <select id="hiringtype" style="display:none;">
        <option value="10" selected>Best Match</option>
        <option value="1">Newest to Oldest (Date)</option>
        <option value="2">Oldest to Newest (Date)</option>
        <option value="3">Low to High (Salary)</option>
        <option value="4">High to Low (Salary)</option>
      </select>
      <div class="rrj-sort-panel d-none" id="rrj_sort_panel"></div>
    </div>

    <button type="button" id="rrj_filter_toggle" class="btn rrj-filter-btn position-relative">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
      <span>Filter</span>
      <span class="badge badge-circle badge-primary rrj-count-badge d-none" id="rrj_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
    </button>
  </div>

  {{-- ══ Filter Drawer ══════════════════════════════════════════ --}}
  <div id="rrj_filter_drawer" class="bg-body rrj-drawer" data-kt-drawer="true" data-kt-drawer-name="rrj-filter"
      data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
      data-kt-drawer-width="{default:'95%', 'sm':'90%', 'lg':'80%'}" data-kt-drawer-direction="end"
      data-kt-drawer-toggle="#rrj_filter_toggle" data-kt-drawer-close="#rrj_filter_close">
    <div id="rrj_card" class="card shadow-none rounded-0 w-100 h-100 rrj-card">
      <div class="rrj-header">
        <div class="d-flex align-items-center gap-3">
          <span class="rrj-header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
          </span>
          <div>
            <h3 class="rrj-title">Filter Jobs</h3>
            <p class="rrj-subtitle">Narrow down jobs matching this candidate</p>
          </div>
        </div>
        <button type="button" class="rrj-close" id="rrj_filter_close" title="Close">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="rrj-body">
        <form id="createSchedule" class="ajax-form" method="post" onsubmit="return false;">

          {{-- Skills/Keywords --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4.5h6M9 4.5a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 4.5H9ZM9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15M9 12l2 2 4-4"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Skills / Keywords</div>
                <div class="rrj-section-desc">Click the star on a skill to make it mandatory</div>
              </div>
            </div>
            <input class="form-control form-control-solid" name="kt_tagify_users" value="{{ @$default_skill }}" id="kt_tagify_users" placeholder="Add a skill or keyword" />
          </div>

          {{-- Location --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-green">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Job Location</div>
                <div class="rrj-section-desc">City, state or country</div>
              </div>
            </div>
            <input class="form-control form-control-solid" value="{{ @$default_loc }}" id="kt_tagify_location" placeholder="Enter location..." />
          </div>

          {{-- Experience --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Experience</div>
                <div class="rrj-section-desc">Required experience range, in years</div>
              </div>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="rrj-mini-label">Minimum</label>
                <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                  <option value="" @if(@$min_exp==='' || @$min_exp===null) selected @endif>Select Experience</option>
                  <option value="0" @if(0==@$min_exp) selected @endif>Freshers</option>
                  @for($i=1; $i<=30; $i++)
                  <option value="{{ $i }}" @if($i==@$min_exp) selected @endif>{{ $i }} Year{{ ($i>=1) ? 's' : '' }}</option>
                  @endfor
                </select>
              </div>
              <div class="col-6">
                <label class="rrj-mini-label">Maximum</label>
                <select class="form-select form-select-solid" name="max_exp" id="max_exp">
                  <option value="" @if(@$max_exp==='' || @$max_exp===null) selected @endif>Select Experience</option>
                  <option value="0" @if(0==@$max_exp) selected @endif>Freshers</option>
                  @for($i=1; $i<=30; $i++)
                  <option value="{{ $i }}" @if($i==@$max_exp) selected @endif>{{ $i }} Year{{ ($i>=1) ? 's' : '' }}</option>
                  @endfor
                </select>
              </div>
            </div>
          </div>

          {{-- Salary --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-purple">
                <span style="font-size:15px;font-weight:700;line-height:1;">₹</span>
              </span>
              <div>
                <div class="rrj-section-title">Salary</div>
                <div class="rrj-section-desc">Offered salary range for the job</div>
              </div>
            </div>
            <div class="row g-2">
              <div class="col-3">
                <label class="rrj-mini-label">Currency</label>
                <select class="form-select form-select-solid" name="min_salary_currency" id="min_salary_currency">
                  <option value="">Any</option>
                  <option value="0" @if(0==@$min_salary_currency) selected @endif>INR</option>
                  <option value="1" @if(1==@$min_salary_currency) selected @endif>EUR</option>
                  <option value="2" @if(2==@$min_salary_currency) selected @endif>JPY</option>
                  <option value="3" @if(3==@$min_salary_currency) selected @endif>GBP</option>
                  <option value="4" @if(4==@$min_salary_currency) selected @endif>USD</option>
                  <option value="5" @if(5==@$min_salary_currency) selected @endif>AED</option>
                </select>
              </div>
              <div class="col-3">
                <label class="rrj-mini-label">Mode</label>
                <select class="form-select form-select-solid" name="sal_mode" id="sal_mode">
                  <option value="">Any</option>
                  <option value="0" @if(0==@$min_sal_mode) selected @endif>Annual</option>
                  <option value="1" @if(1==@$min_sal_mode) selected @endif>Monthly</option>
                  <option value="2" @if(2==@$min_sal_mode) selected @endif>Hourly</option>
                </select>
              </div>
              <div class="col-3">
                <label class="rrj-mini-label">Min</label>
                <input type="number" class="form-control form-control-solid" name="min_sal" id="min_sal" placeholder="Any" value="{{ @$min_salary }}">
              </div>
              <div class="col-3">
                <label class="rrj-mini-label">Max</label>
                <input type="number" class="form-control form-control-solid" name="max_sal" id="max_sal" placeholder="Any" value="{{ @$max_salary }}">
              </div>
            </div>
          </div>

          {{-- Hiring Type --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-9 0h12A1.5 1.5 0 0 1 19.5 7.5V18a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V7.5A1.5 1.5 0 0 1 6 6Zm-1.5 5.25h15"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Hiring Type</div>
                <div class="rrj-section-desc">Full-time or contract roles</div>
              </div>
            </div>
            <div class="rrj-pill-row">
              <div>
                <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="" id="app_int_outsource1" checked>
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="app_int_outsource1">All</label>
              </div>
              <div>
                <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="0" id="app_int_outsource2">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="app_int_outsource2">Fulltime</label>
              </div>
              <div>
                <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="1" id="app_int_outsource3">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="app_int_outsource3">Contract</label>
              </div>
            </div>
          </div>

          {{-- Activity --}}
          <div class="rrj-section rrj-section-last">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Activity</div>
                <div class="rrj-section-desc">How recently the job was posted/updated</div>
              </div>
            </div>
            <div class="rrj-pill-row">
              <div>
                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="1" id="app_int_activity1" checked>
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="app_int_activity1">All</label>
              </div>
              <div>
                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="2" id="app_int_activity2">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="app_int_activity2">30 Days</label>
              </div>
              <div>
                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="3" id="app_int_activity3">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="app_int_activity3">60 Days</label>
              </div>
              <div>
                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="4" id="app_int_activity4">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="app_int_activity4">90 Days</label>
              </div>
              <div>
                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="5" id="app_int_activity5">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="app_int_activity5">180 Days</label>
              </div>
              <div>
                <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="6" id="app_int_activity6">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="app_int_activity6">1 Year</label>
              </div>
            </div>
          </div>

        </form>
      </div>

      <div class="rrj-footer">
        <div class="rrj-footer-count">
          <span class="rrj-footer-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9m0 10h9M4 7h1m0 0v10m0 0h1M4 12h6"/></svg>
          </span>
          <div>
            <div class="rrj-footer-number" id="rrj_footer_number">0</div>
            <div class="rrj-footer-label">Jobs Match</div>
          </div>
        </div>
        <div class="rrj-footer-actions">
          <button type="button" class="btn btn-sm btn-light" id="rrj_reset_btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
            Reset
          </button>
          <button type="button" class="btn btn-sm btn-primary" id="rrj_apply_btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
            Apply Filters
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ SECTION 3 · RESULTS ═══════════════════════════════════ --}}
  <div class="loadingMessage loadingMessagebenchCandidateRecommendedReqAjax">
    <div class="spinner-border text-primary" role="status"></div>
  </div>

  <div class="rrj-results-wrap">
    <div class="agencies-list" id="benchCandidateRecommendedReqAjax"></div>
    <div id="paginationbenchCandidateRecommendedReq"></div>
  </div>

  </div>{{-- /rrj-page --}}
  </div>
 </div>
</div>
@else
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <table id="no-access" class="table align-middle table-row-dashed fs-6 gy-5">
        <thead><tr class="fw-bolder text-muted"><th><div align="center">ACCESS DENIED CONTACT ADMINISTRATOR</div></th></tr></thead>
      </table>
    </div>
  </div>
</div>
@endif

<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
     data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'95%', 'sm':'90%', 'lg':'80%'}"
     data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Requisition</h3>
      </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close">
          <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
              </g>
            </svg>
          </span>
        </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>

@endsection
@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

{{-- ── Skills Tagify (mandatory-star) + Location Tagify — same behavior as
     the old filter modal, now scoped to the drawer only (no more bidirectional
     sync/"autoclickstar" hack with the main search box — that coupling was a
     bug, not a feature: it silently turned the plain search field into a
     multi-tag skills filter). ── --}}
<script>
const rrjInputElm = document.querySelector('#kt_tagify_users');
const rrjTagifySkills = new Tagify(rrjInputElm, {
    templates: {
        tag: function(tagData) {
            tagData = tagData || {};
            const value = tagData.value || '';
            const isChecked = tagData.checked ? 'checked' : '';
            return `<tag title="${value}" contenteditable='false' spellcheck="false" class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                <x title='' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x>
                <div><span class="star ${isChecked}">★</span><span class="tagify__tag-text">${value}</span></div>
            </tag>`;
        }
    },
    transformTag: function(tagData) { tagData.checked = tagData.checked || false; return tagData; }
});
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('star')) {
        const tagElm = e.target.closest('.tagify__tag');
        const value = tagElm.querySelector('.tagify__tag-text').textContent;
        e.target.classList.toggle('checked');
        const tagData = rrjTagifySkills.value.find(tag => tag.value === value);
        if (tagData) { tagData.checked = e.target.classList.contains('checked'); rrjTagifySkills.updateValueByDOMTags(); }
    }
});
function rrjDebounce(func, delay) {
    let t; return function(...args) { clearTimeout(t); t = setTimeout(() => func.apply(this, args), delay); };
}
const rrjFetchSkillSuggestions = rrjDebounce(function(query) {
    $.ajax({
        url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
        method: 'GET',
        success: function(data) { if (Array.isArray(data)) { rrjTagifySkills.settings.whitelist = data; rrjTagifySkills.dropdown.show.call(rrjTagifySkills); } },
        error: function(err) { console.error("Error fetching Skill:", err); }
    });
}, 200);
rrjTagifySkills.on('input', function(e) {
    var query = e.detail.value.trim();
    if (query.length < 1) return;
    rrjFetchSkillSuggestions(query);
});

var rrjInputLoc = document.querySelector("#kt_tagify_location");
var rrjCities = @json($cities);
var rrjTagifyLocation = new Tagify(rrjInputLoc, {
    whitelist: rrjCities, maxTags: 10,
    dropdown: { maxItems: 20, classname: "", enabled: 0, closeOnSelect: false }
});
rrjInputLoc.addEventListener('keydown', function(e) { if (e.key === "Enter") { e.preventDefault(); } });
rrjTagifyLocation.on('input', function(e) { rrjTagifyLocation.dropdown.show(); });
rrjTagifyLocation.on('dropdown:select', function(e) { rrjTagifyLocation.addTags([e.detail.data.value]); });
</script>

{{-- ── Sort panel + filter drawer chrome ── --}}
<script>
function rrjRenderSortPanel(){
    var $panel=$('#rrj_sort_panel').empty();
    var current=$('#hiringtype').val();
    $('#hiringtype option').each(function(){
        var $opt=$(this), val=$opt.attr('value'), label=$opt.text();
        $('<div class="rrj-sort-item"></div>').text(label)
            .toggleClass('active', val===current)
            .on('click', function(){
                $('#hiringtype').val(val);
                $('#rrj_sort_label').text(label);
                rrjCloseSortPanel();
                fetchBenchRecommendedReqCandidateList();
            }).appendTo($panel);
    });
}
function rrjOpenSortPanel(){ rrjRenderSortPanel(); $('#rrj_sort_panel').removeClass('d-none'); $('#rrj_sort_btn').addClass('open'); }
function rrjCloseSortPanel(){ $('#rrj_sort_panel').addClass('d-none'); $('#rrj_sort_btn').removeClass('open'); }
$(document).on('click','#rrj_sort_btn',function(e){
    e.stopPropagation();
    if ($('#rrj_sort_panel').hasClass('d-none')) rrjOpenSortPanel(); else rrjCloseSortPanel();
});
$(document).on('click',function(e){ if(!$(e.target).closest('#rrj_sort_wrap').length) rrjCloseSortPanel(); });

$(document).on('keydown', '#kt_tagify_2', function(e){
    if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); fetchBenchRecommendedReqCandidateList(); }
});

$(document).on('click','#rrj_reset_btn',function(){
    rrjTagifySkills.removeAllTags();
    rrjTagifyLocation.removeAllTags();
    $('#min_exp,#max_exp,#min_salary_currency,#sal_mode,#min_sal,#max_sal').val('');
    $('#app_int_outsource1,#app_int_activity1').prop('checked', true);
});
$(document).on('click','#rrj_apply_btn',function(){
    fetchBenchRecommendedReqCandidateList();
    $('#rrj_filter_close').trigger('click');
});
</script>

{{-- ── Preserved job/client/HM/team drawer handlers + bench-request send —
     UNCHANGED from before this redesign ── --}}
<script>
$(document).ready(function() {

    $(document).on("click",".checkrequestsend", function(){
        swal("Recommendation Alert", 'Please move the candidate to Bench status before sending the request', "warning");
    });

    $(document).on("click",".request-send", function(){
        var ele = $(this);
        var jobId  = $(this).attr('data-jobId');
        var conId  = $(this).attr('data-conId');
        swal({
            title: "Are you sure?",
            text: " Do you want to Submit Candidate request on this Job?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, I am sure!',
            cancelButtonText: "No, cancel it!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if (isConfirm){
                var url = "{{ route('service_company.jobs.recommandedrequestcondidate',':id') }}";
                url = url.replace(':id', jobId)+"?conId="+conId;
                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function (response) {
                        if(response.status=='success'){
                            swal("Recommend!", "Candidates are successfully Recommend!", "success");
                            var jobId  = ele.attr('data-jobId');
                            var conId  = ele.attr('data-conId');
                            var matlaccess = 1;
                            var url = "{{ route('service_company.jobs.mailrecommandedrequestcondidate',':id') }}";
                            url = url.replace(':id', jobId)+"?conId="+conId+"?matlaccess="+matlaccess;
                            fetchBenchRecommendedReqCandidateList();
                            $.ajax({ type: 'GET', url: url, success: function (response) {} });
                        }else{
                            fetchBenchRecommendedReqCandidateList();
                            swal("Recommend Failed", response.message, "warning").then(function(){
                                fetchBenchRecommendedReqCandidateList();
                            });
                        }
                    }
                });
            } else {
                swal("Cancelled", "", "error");
            }
        });
    });
});
</script>
<script>
function updateJobStatus(id, statusName)
{
    var url = "{{ route('service_company.jobs.updatestatus',':id') }}";
    url = url.replace(':id', id);
    var token = "{{ csrf_token() }}";
    $.easyAjax({
        type: 'POST',
        url: url,
        data: {'_token': token, '_method': 'POST', 'id':id, 'statusName':statusName},
        success: function (response){ fetchBenchRecommendedReqCandidateList(); }
    });
}

$(document).on('click', '.show-detail', function () {
    $('#kt_activitiesJob_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.viewmore',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET', url: url,
        beforeSend: function() { $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); },
        success: function (response) { $('#kt_activitiesJob_body').html(response.view); }
    });
});

$(document).on('click', '.status_stage', function () {
    $('#status_body_content').html('');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.editJobStatus',':id') }}";
    url = url.replace(':id', id);
    $.ajax({
        type: 'GET', url: url,
        success: function (response) { if (response.status == "success") { $('#status_body_content').html(response.view); } }
    });
});

$(document).on('click', '.client-show-detail', function () {
    $('#kt_activitiesClientjob_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.clientviewmore',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET', url: url,
        beforeSend: function() { $('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); },
        success: function (response) { $('#kt_activitiesclientjob_body').html(response.view); }
    });
});

$(document).on('click', '.spartner-listing', function () {
    $('#kt_activitiesStaffing_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.spartnerlisting',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET', url: url,
        beforeSend: function() { $('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); },
        success: function (response) { $('#kt_activities_body').html(response.view); }
    });
});
</script>
<script>
$(document).ready(function(){
    $(document).on('click', '.hmlisting', function(){
        $('#kt_activitiesRecruiter_toggle').trigger('click');
        var id = $(this).data('row-id');
        var token = "{{ csrf_token() }}";
        var url = "{{ route('service_company.jobs.hmlisting',':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET', url: url, data: {'_token': token, 'id' : id},
            beforeSend: function() { $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); },
            success: function (response) { $('#kt_activitiesRecruiter_body').html(response.view); }
        });
    });
});
</script>
<script>
$(document).on('click', '.client-hm-listing', function () {
    $('#kt_activitiesCHM_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.clienthmlisting',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET', url: url,
        beforeSend: function() { $('#kt_activitiesCHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); },
        success: function (response) { $('#kt_activitiesCHM_body').html(response.view); }
    });
});

$(document).on('click', '.team-listing', function () {
    $('#kt_activitiesTEAM_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.currentteamlisting',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET', url: url,
        beforeSend: function() { $('#kt_activitiesTEAM_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); },
        success: function (response) { $('#kt_activitiesTEAM_body').html(response.view); }
    });
});
</script>
<script>
$(document).on('click', '.hm-listing', function () {
    $('#kt_activitiesHM_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.hmlisting',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET', url: url,
        beforeSend: function() { $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); },
        success: function (response) { $('#kt_activitiesHM_body').html(response.view); }
    });
});
</script>

{{-- ── Core data fetch — SAME response-handling as the client-jobs twin
     page, but hitting all_internal_data_ajax() (own-company jobs only) and
     updating the #recommend_req_internal_id tab counter instead. Same fix
     for the "No data available" first-paint bug (see comment on the client
     page): the very first automatic load doesn't apply the candidate's
     pre-filled skills+designation as a hard filter. ── --}}
<script>
var rrjInitialLoad = true;

function fetchBenchRecommendedReqCandidateList(page = 1, search = '') {
    $('.loadingMessagebenchCandidateRecommendedReqAjax').css({ display: 'flex' });
    $.ajax({
        url: `{!! route('service_company.recommend.all_internal_data_ajax') !!}?page=${page}`,
        type: 'GET',
        data: {
            search: $('#inputSearch').val(),
            condidate_id: "{{$segment}}",
            custom_search: $('#kt_tagify_2').val(),
            custom_status: 'active',
            custom_hiringtype: $('#hiringtype').val(),
            cus_search: rrjInitialLoad ? '[]' : $('#kt_tagify_users').val(),
            min_exp: $('#min_exp').val(),
            max_exp: $('#max_exp').val(),
            kt_tagify_location: $('#kt_tagify_location').val(),
            min_sal: $('#min_sal').val(),
            max_sal: $('#max_sal').val(),
            min_salary_currency: $('#min_salary_currency').val(),
            sal_mode: $('#sal_mode').val(),
            app_int_activity: $('[name="app_int_activity"]:checked').val(),
            app_int_outsource: $('[name="app_int_outsource"]:checked').val()
        },
        success: function(response) {
            rrjInitialLoad = false;
            $('.loadingMessagebenchCandidateRecommendedReqAjax').css({ display: 'none' });
            if (!response || typeof response.html === 'undefined') {
                console.error('all_internal_data_ajax: unexpected response', response);
                return;
            }
            $('#benchCandidateRecommendedReqAjax').html(response.html);
            $('#recommend_req_internal_id').html(response.total);
            $('#rrj_footer_number').text(response.total);
            $('#paginationbenchCandidateRecommendedReq').html(response.pagination);
        },
        error: function(xhr) {
            rrjInitialLoad = false;
            $('.loadingMessagebenchCandidateRecommendedReqAjax').css({ display: 'none' });
            console.error('all_internal_data_ajax failed:', xhr.status, xhr.responseText);
        }
    });
}

function filtersdataget(){ fetchBenchRecommendedReqCandidateList(); }

$(document).ready(function() {
    fetchBenchRecommendedReqCandidateList();
    $(document).on('click', '#paginationbenchCandidateRecommendedReq .page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (!page || $(this).hasClass('disabled')) return;
        $(this).addClass('disabled');
        fetchBenchRecommendedReqCandidateList(page, $('#search').val()).then(() => {
            $('#paginationbenchCandidateRecommendedReq .page-link').removeClass('disabled');
        }).catch(() => {
            $('#paginationbenchCandidateRecommendedReq .page-link').removeClass('disabled');
        });
    });
});
</script>
@endpush
