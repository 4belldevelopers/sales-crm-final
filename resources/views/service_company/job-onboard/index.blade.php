@extends('layouts.service_company_layout')

@push('head-script')
{{-- Font Awesome — required so the currency symbols (e.g. <i class="fa fa-inr">) render in the Compensation column. --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<style>
/* ──────────────────────────────────────────────
   Joining Tracker — redesigned layout
   Font: Poppins (inherited), 14px hard cap per UI spec.
   Built to run with near-zero extra server load: the table + every KPI/tab number
   come from ONE cached Elasticsearch aggregation (cvtracker.allcandidates,status_cus=6).
────────────────────────────────────────────── */
.jt-scope, .jt-scope * { font-family: 'Poppins', sans-serif !important; box-sizing: border-box; }

/* Page header */
.jt-page-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px 0; }
.jt-page-head h4 { font-size: 16px; font-weight: 700; margin: 0; color: #0f172a; }
.jt-page-head p  { font-size: 11px; color: #64748b; margin: 2px 0 0; }
.jt-head-actions { display: flex; gap: 10px; align-items: center; }
.jt-btn { display: flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #e2e8f0; background: #fff; cursor: pointer; color: #374151; }
.jt-btn:hover { background: #f8fafc; }
.jt-btn svg { width: 14px; height: 14px; }

/* KPI stat cards */
.jt-stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; padding: 14px 24px 0; }
.jt-stat-card { background: #fff; border-radius: 14px; padding: 13px 16px; border: 1px solid #f1f5f9; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
.jt-stat-icon { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
.jt-stat-icon svg { width: 18px; height: 18px; }
.jt-stat-body { flex: 1; min-width: 0; }
.jt-stat-label { font-size: 10px; color: #94a3b8; font-weight: 500; margin: 0 0 2px; text-transform: uppercase; letter-spacing: .4px; }
.jt-stat-value { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.15; }
.jt-stat-delta { font-size: 10px; margin-top: 4px; display: flex; align-items: center; gap: 3px; font-weight: 500; }
.jt-stat-delta.up { color: #16a34a; } .jt-stat-delta.down { color: #dc2626; }
.jt-stat-delta.warn { color: #d97706; } .jt-stat-delta.info { color: #0891b2; }
.jt-stat-link { font-size: 11px; font-weight: 600; color: #6366f1; cursor: pointer; margin-top: 4px; display: inline-flex; align-items: center; gap: 3px; }
.jt-stat-link:hover { color: #4f46e5; }

/* Table card */
.jt-card { background: #fff; border-radius: 14px; border: 1px solid #f1f5f9; margin: 18px 24px 24px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.04); }

/* Search + filter bar */
.jt-filter-bar { display: flex; align-items: center; gap: 12px; padding: 16px 20px; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; }
.jt-search-wrap { flex: 1; min-width: 220px; position: relative; }
.jt-search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; width: 16px; height: 16px; pointer-events: none; }
.jt-search-input { width: 100%; border: 1px solid #e2e8f0; border-radius: 9px; padding: 8px 12px 8px 36px; font-size: 13px; color: #374151; outline: none; background: #f8fafc; }
.jt-search-input:focus { border-color: #6366f1; background: #fff; }
.jt-location-select { border: 1px solid #e2e8f0; border-radius: 9px; padding: 8px 12px; font-size: 13px; color: #374151; background: #f8fafc; cursor: pointer; outline: none; min-width: 160px; }
.jt-location-select:focus { border-color: #6366f1; background: #fff; }

/* Tabs row */
.jt-tabs-bar { display: flex; align-items: center; justify-content: space-between; padding: 0 20px; border-bottom: 1px solid #f1f5f9; gap: 12px; flex-wrap: wrap; }
.jt-tabs { display: flex; gap: 0; overflow-x: auto; }
.jt-tab { padding: 11px 16px; font-size: 12px; font-weight: 500; color: #64748b; cursor: pointer; border-bottom: 2px solid transparent; white-space: nowrap; user-select: none; display: flex; align-items: center; gap: 6px; transition: color .15s; }
.jt-tab:hover { color: #374151; }
.jt-tab-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 18px; border-radius: 99px; font-size: 10px; font-weight: 700; background: #f1f5f9; color: #64748b; padding: 0 5px; transition: all .15s; }
.jt-tab[data-filter="all"].active           { color: #0f172a; border-bottom-color: #0f172a; font-weight: 700; }
.jt-tab[data-filter="all"].active .jt-tab-badge           { background: #e2e8f0; color: #0f172a; }
.jt-tab[data-filter="joining_today"].active  { color: #7c3aed; border-bottom-color: #7c3aed; font-weight: 700; }
.jt-tab[data-filter="joining_today"].active .jt-tab-badge  { background: #ede9fe; color: #7c3aed; }
.jt-tab[data-filter="this_week"].active      { color: #2563eb; border-bottom-color: #2563eb; font-weight: 700; }
.jt-tab[data-filter="this_week"].active .jt-tab-badge      { background: #dbeafe; color: #2563eb; }
.jt-tab[data-filter="delayed"].active        { color: #ea580c; border-bottom-color: #ea580c; font-weight: 700; }
.jt-tab[data-filter="delayed"].active .jt-tab-badge        { background: #ffedd5; color: #ea580c; }
.jt-tab[data-filter="no_show"].active        { color: #dc2626; border-bottom-color: #dc2626; font-weight: 700; }
.jt-tab[data-filter="no_show"].active .jt-tab-badge        { background: #fee2e2; color: #dc2626; }
.jt-tab[data-filter="pending_docs"].active   { color: #d97706; border-bottom-color: #d97706; font-weight: 700; }
.jt-tab[data-filter="pending_docs"].active .jt-tab-badge   { background: #fef3c7; color: #d97706; }

.jt-sort-bar { display: flex; align-items: center; gap: 10px; padding: 8px 0; }
.jt-sort-label { font-size: 12px; color: #94a3b8; white-space: nowrap; }
.jt-sort-select { border: 1px solid #e2e8f0; border-radius: 9px; padding: 8px 12px; font-size: 13px; color: #374151; background: #f8fafc; outline: none; cursor: pointer; min-width: 150px; }
.jt-sort-select:focus { border-color: #6366f1; background: #fff; }
.jt-view-btns { display: flex; gap: 4px; }
.jt-view-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #94a3b8; }
.jt-view-btn.active { background: #f1f5f9; color: #374151; border-color: #cbd5e1; }
.jt-view-btn svg { width: 14px; height: 14px; }

/* Table */
.jt-table { width: 100%; border-collapse: collapse; }
.jt-table thead tr { background: #f8fafc; }
.jt-table th { padding: 10px 14px; font-size: 11px; font-weight: 600; color: #64748b; text-align: left; white-space: nowrap; letter-spacing: .3px; border-bottom: 1px solid #f1f5f9; }
.jt-table th .jt-sort-ico { margin-left: 4px; color: #cbd5e1; font-size: 10px; }
.jt-table th.jt-th-sort { cursor: pointer; user-select: none; }
.jt-table th.jt-th-sort:hover { color: #334155; }
.jt-table th.jt-th-sort.active { color: #6366f1; }
.jt-table th.jt-th-sort.active .jt-sort-ico { color: #6366f1; }
.jt-row { border-bottom: 1px solid #f8fafc; transition: background .12s; }
.jt-row:hover { background: #fafbff; }
.jt-td { padding: 12px 14px; vertical-align: middle; }

/* Candidate / HR owner cells */
.jt-cand { display: flex; align-items: center; gap: 10px; }
.jt-avatar { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; flex-shrink: 0; }
.jt-avatar-img { width: 40px; height: 40px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
.jt-avatar-sm, .jt-avatar-img.jt-avatar-sm { width: 34px; height: 34px; font-size: 13px; }
.jt-avatar-link { display: inline-flex; }
.jt-cand-info { min-width: 0; }
.jt-cand-name { font-size: 13px; font-weight: 600; color: #0f172a; display: block; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
.jt-cand-name:hover { color: #6366f1; }
.jt-cand-role { font-size: 11px; color: #94a3b8; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
.jt-bench { font-size: 9px; background: #dbeafe; color: #2563eb; padding: 1px 6px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle; }
.jt-owner-name { font-size: 13px; font-weight: 600; color: #0f172a; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
.jt-owner-role { font-size: 11px; color: #94a3b8; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }

/* Job & Department */
.jt-job-title { font-size: 13px; font-weight: 600; color: #1e293b; display: block; }
.jt-job-type  { font-size: 11px; color: #94a3b8; display: block; margin-top: 2px; }

/* Joining status */
.jt-status-link { text-decoration: none; display: block; cursor: pointer; }
.jt-status-pill { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; margin-bottom: 3px; }
.jt-status-sub { font-size: 11px; color: #64748b; display: block; }

/* Job Status */
.jt-jobstatus { font-size: 13px; font-weight: 600; color: #1e293b; display: inline-block; }
.jt-jobstatus-sub { font-size: 11px; color: #94a3b8; display: block; margin-top: 2px; }
/* Closed requisitions read red (background + text) */
.jt-jobstatus.jt-closed { background: #fee2e2; color: #dc2626; padding: 2px 9px; border-radius: 6px; }

/* Offer Amount */
.jt-comp-amount { font-size: 13px; font-weight: 600; color: #0f172a; display: block; white-space: nowrap; }
.jt-comp-type   { font-size: 11px; color: #94a3b8; display: block; margin-top: 2px; }

/* Stage */
.jt-td-stage { min-width: 120px; }
.jt-stage-link { text-decoration: none; display: block; cursor: pointer; }
.jt-stage-pill { display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; margin-bottom: 3px; }
.jt-stage-pill:not([class*="badge-"]):not([class*="bg-"]) { background: #f1f5f9; color: #475569; }
.jt-stage-sub { font-size: 11px; color: #94a3b8; display: block; }
.jt-stage-link:hover .jt-stage-pill { filter: brightness(0.97); }

/* Joining date */
.jt-join-date { font-size: 13px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 5px; }
.jt-join-date svg { width: 14px; height: 14px; }
.jt-join-time { font-size: 11px; color: #94a3b8; display: block; margin-top: 2px; }
.jt-join-countdown { font-size: 11px; font-weight: 600; display: block; margin-top: 2px; }
.jt-na { font-size: 14px; color: #cbd5e1; display: block; }
.jt-na-sub { font-size: 11px; color: #94a3b8; display: block; }

/* Colour tones (reused from GeneralMethod::icoinText output) */
.jt-avatar.bg-light-danger  { background:#fee2e2 !important; color:#dc2626 !important; }
.jt-avatar.bg-light-warning { background:#fef3c7 !important; color:#d97706 !important; }
.jt-avatar.bg-light-primary { background:#e0e7ff !important; color:#4f46e5 !important; }
.jt-avatar.bg-light-success { background:#dcfce7 !important; color:#16a34a !important; }
.jt-avatar.bg-light-info    { background:#cffafe !important; color:#0891b2 !important; }
.jt-avatar.bg-dark, .jt-avatar.bg-light-secondary { background:#e2e8f0 !important; color:#475569 !important; }

/* Empty state */
.jt-empty td { padding: 60px 14px; text-align: center; }
.jt-empty-inner p { margin: 12px 0 0; font-size: 13px; color: #94a3b8; }

/* Table footer */
.jt-table-footer { display: flex; align-items: center; justify-content: flex-end; padding: 12px 20px; }
.jt-rows-per { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b; }
.jt-rows-select { border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 8px; font-size: 12px; background: #fff; cursor: pointer; }

/* Responsive — 15.6" laptops & smaller */
@media (max-width: 1200px) { .jt-stats-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px)  {
    .jt-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .jt-page-head { flex-direction: column; align-items: flex-start; gap: 12px; }
    .jt-card { margin: 14px 12px; }
    .jt-stats-grid, .jt-page-head { padding-left: 12px; padding-right: 12px; }
}
</style>
@endpush

@section('content')
<div class="jt-scope">

{{-- ── Page header ── --}}
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div class="jt-page-head" style="padding:0;width:100%">
            <div>
                <h4>Joining Tracker</h4>
                <p>Track and manage candidate joining and onboarding in one place</p>
            </div>
            <div class="jt-head-actions">
                <button class="jt-btn" onclick="exportJoining()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M8 11l4 4 4-4M5 21h14"/></svg>
                    Export
                </button>
                <button class="jt-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
                    More Actions
                </button>
            </div>
        </div>
    </div>
</div>

<div class="content d-flex flex-column flex-column-fluid pt-2" id="kt_content">
<div class="post d-flex flex-column-fluid" id="kt_post">
<div class="container-fluid px-0">

{{-- ── KPI Stats (seeded server-side, refreshed live from ES counts) ── --}}
@php
    // Current month label for the "This Month's Joinings" card, e.g. "July 26".
    $thisMonthLabel = date('F y');
    $statCards = [
        ['label'=>'Total Joined','key'=>'total','bg'=>'#ede9fe','ic'=>'#7c3aed','sub'=>'text','tone'=>'info','delta'=>'All joinings tracked',
         'icon'=>'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
        ['label'=>"Today's Joinings",'key'=>'today','bg'=>'#dbeafe','ic'=>'#2563eb','sub'=>'link',
         'icon'=>'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5'],
        ['label'=>'Onboarded','key'=>'onboarded','bg'=>'#d1fae5','ic'=>'#059669','sub'=>'pct','tone'=>'up',
         'icon'=>'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
        ['label'=>'Backed Out','key'=>'backed_out','bg'=>'#fee2e2','ic'=>'#dc2626','sub'=>'pct','tone'=>'down',
         'icon'=>'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
        ['label'=>"This Month's Joinings",'key'=>'this_month','bg'=>'#fef3c7','ic'=>'#d97706','sub'=>'text','tone'=>'info','delta'=>$thisMonthLabel,
         'icon'=>'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5M9 15.75l1.75 1.75L14 13.5'],
    ];
@endphp
<div class="jt-stats-grid">
    @foreach($statCards as $card)
    <div class="jt-stat-card">
        <div class="jt-stat-icon" style="background:{{ $card['bg'] }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="{{ $card['ic'] }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $card['icon'] }}"/></svg>
        </div>
        <div class="jt-stat-body">
            <p class="jt-stat-label">{{ $card['label'] }}</p>
            <p class="jt-stat-value" data-kpi="{{ $card['key'] }}">{{ $joiningStats[$card['key']] ?? 0 }}</p>
            @if($card['sub'] === 'link')
                <span class="jt-stat-link" data-jump-tab="joining_today">View Today's List
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </span>
            @elseif($card['sub'] === 'pct')
                <div class="jt-stat-delta {{ $card['tone'] }}"><span data-kpi-pct="{{ $card['key'] }}">0% of total joining</span></div>
            @else
                <div class="jt-stat-delta {{ $card['tone'] }}"><span>{{ $card['delta'] }}</span></div>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- ── Table card ── --}}
<div class="jt-card">
    {{-- Search + Location filter --}}
    <div class="jt-filter-bar">
        <div class="jt-search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            <input type="text" class="jt-search-input" id="jt_search" placeholder="Search by candidate name, job or offer ID..." />
        </div>
        <select class="jt-location-select" id="jt_country">
            <option value="">All Locations</option>
            @foreach($countries as $c)
            <option value="{{ $c->id }}">{{ $c->country_name }}</option>
            @endforeach
        </select>
        <div class="jt-sort-bar">
            <span class="jt-sort-label">Sort by:</span>
            <select class="jt-sort-select" id="jt_sort">
                <option value="12">Latest Joining Date</option>
                <option value="13">Oldest Joining Date</option>
                <option value="onboarded">Onboarded</option>
                <option value="backed_out">Backed Out</option>
            </select>
        </div>
    </div>

    {{-- Tabs + view toggle --}}
    <div class="jt-tabs-bar">
        <div class="jt-tabs" id="jt_tabs">
            <div class="jt-tab active" data-filter="all">All <span class="jt-tab-badge" data-tab="total">—</span></div>
            <div class="jt-tab" data-filter="joining_today">Joining Today <span class="jt-tab-badge" data-tab="today">—</span></div>
            <div class="jt-tab" data-filter="this_week">This Week <span class="jt-tab-badge" data-tab="this_week">—</span></div>
            <div class="jt-tab" data-filter="delayed">Delayed <span class="jt-tab-badge" data-tab="delayed">—</span></div>
            <div class="jt-tab" data-filter="no_show">No Show <span class="jt-tab-badge" data-tab="no_show">—</span></div>
            <div class="jt-tab" data-filter="pending_docs">Pending Docs <span class="jt-tab-badge" data-tab="pending_docs">—</span></div>
        </div>
        <div class="jt-view-btns">
            <button class="jt-view-btn active" title="List">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <button class="jt-view-btn" title="Grid" disabled style="opacity:.5;cursor:not-allowed;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            </button>
        </div>
    </div>

    {{-- Table --}}
    @if(in_array('view_joining', $userPermissions))
    <div style="overflow-x:auto;">
        <table class="jt-table">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Requisitions</th>
                    <th>Job Status</th>
                    <th>Ownership</th>
                    <th class="jt-th-sort" data-asc="14" data-desc="15">Compensation <span class="jt-sort-ico">⇵</span></th>
                    <th class="jt-th-sort" data-asc="13" data-desc="12">Joining Date <span class="jt-sort-ico">⇵</span></th>
                    <th>Stage</th>
                </tr>
            </thead>
            <tbody id="joining-tracker">
                <tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;font-size:13px;">
                    <img src="{{ asset('assets/images/Loading2.gif') }}" height="32" alt=""> &nbsp; Loading...
                </td></tr>
            </tbody>
        </table>
    </div>

    <div id="jt_pagination"></div>
    @else
    <div style="padding:40px;text-align:center;color:#ef4444;font-size:13px;font-weight:600;">
        ACCESS DENIED — Contact your administrator
    </div>
    @endif
</div>

{{-- Change Candidate Stage drawer --}}
<style>
  #csd_drawer .sd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 28px; border-bottom: 1px solid #eef0f4; flex-wrap: wrap; }
  #csd_drawer .sd-topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
  #csd_drawer .sd-topbar-ico { width: 42px; height: 42px; border-radius: 10px; background: #eef4ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
  #csd_drawer .sd-topbar-title { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #181c32; line-height: 1.3; }
  #csd_drawer .sd-topbar-sub { font-family: 'Poppins', sans-serif; font-size: 11.5px; font-weight: 400; color: #7e8299; margin-top: 1px; }
  #csd_drawer .sd-topbar-right { display: flex; align-items: center; gap: 10px; }
  #csd_drawer .sd-btn-cancel { border: 1px solid #e4e6ef; background: #fff; color: #3f4254; border-radius: 24px; padding: 9px 18px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; }
  #csd_drawer .sd-btn-cancel:hover { background: #f8f9fb; }
  #csd_drawer .sd-btn-confirm { border: none; background: #3e97ff; color: #fff; border-radius: 24px; padding: 9px 20px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: filter .15s; }
  #csd_drawer .sd-btn-confirm:hover { filter: brightness(0.94); }
  #csd_drawer .sd-btn-confirm:disabled { background: #c2c9d6 !important; cursor: not-allowed; }
  #csd_drawer .sd-divider { width: 1px; height: 22px; background: #e4e6ef; }
  #csd_drawer .sd-close { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #7e8299; cursor: pointer; flex-shrink: 0; }
  #csd_drawer .sd-close:hover { background: #f8f9fb; color: #f64e60; }
</style>
<div id="csd_drawer_toggle" style="display:none;"></div>
<div id="csd_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="candidate-stage"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'100%', 'sm':'92%', 'lg':'80%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#csd_drawer_toggle" data-kt-drawer-close="#csd_drawer_close">
  <div class="d-flex flex-column h-100 w-100">
    <div class="sd-topbar">
      <div class="sd-topbar-left">
        <span class="sd-topbar-ico"><i class="fa fa-random"></i></span>
        <div>
          <div class="sd-topbar-title">Change Candidate Stage</div>
          <div class="sd-topbar-sub" id="csd_drawer_subtitle">&nbsp;</div>
        </div>
      </div>
      <div class="sd-topbar-right">
        <button type="button" class="sd-btn-cancel" id="stage_sub_stage_close">Cancel</button>
        <button type="button" class="sd-btn-confirm" id="custom_stage_submit">
          <span class="indicator-label"><span class="csdChangeLabel">Submit</span> <i class="fa fa-arrow-right"></i></span>
        </button>
        <span class="sd-divider"></span>
        <span class="sd-close" id="csd_drawer_close"><i class="fa fa-times"></i></span>
      </div>
    </div>
    <div class="flex-grow-1 px-6 py-4" style="overflow-y:auto;">
      <div class="stage_body_content" id="stage_body_content"></div>
    </div>
  </div>
</div>

</div>{{-- /container-fluid --}}
</div>{{-- /kt_post --}}
</div>{{-- /kt_content --}}
</div>{{-- /jt-scope --}}
@endsection

@push('footer-script')
<script>
(function () {
    'use strict';

    /* ── state ── */
    var state = { page: 1, size: 10, search: '', country: '', sort: '12', tab: 'all' };

    var URL_CANDIDATES = '{!! route('service_company.cvtracker.allcandidates') !!}';

    function loaderRow() {
        return '<tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;font-size:13px;"><img src="{{ asset('assets/images/Loading2.gif') }}" height="32" alt=""> &nbsp; Loading...</td></tr>';
    }

    /* ── fetch table (one request also returns every KPI/tab count) ── */
    function fetchTable(resetPage) {
        if (resetPage) state.page = 1;
        document.getElementById('joining-tracker').innerHTML = loaderRow();

        $.ajax({
            url  : URL_CANDIDATES + '?page=' + state.page,
            type : 'GET',
            data : {
                status_cus         : 6,
                size               : state.size,
                cus_search         : state.search,
                country            : state.country,
                sortingfilter      : state.sort,
                joining_tab_filter : state.tab,
            },
            success: function (resp) {
                document.getElementById('joining-tracker').innerHTML = resp.html || '<tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">No joining records found</td></tr>';
                document.getElementById('jt_pagination').innerHTML = resp.pagination || '';
                if (resp.joiningCounts) updateCounts(resp.joiningCounts);
            },
            error: function () {
                document.getElementById('joining-tracker').innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:#ef4444;">Error loading data. Please refresh.</td></tr>';
            }
        });
    }

    /* ── Sync KPI cards + tab badges from the single ES aggregation ── */
    function updateCounts(c) {
        if (!c) return;
        var n = function (v) { return (v != null ? v : 0); };
        var total = n(c.total);

        /* Tab badges */
        document.querySelectorAll('[data-tab]').forEach(function (el) {
            var k = el.getAttribute('data-tab');
            el.textContent = n(c[k]);
        });

        /* KPI stat values */
        document.querySelectorAll('[data-kpi]').forEach(function (el) {
            var k = el.getAttribute('data-kpi');
            if (c[k] != null) el.textContent = c[k];
        });

        /* KPI sub-percentages ("X% of total joining") */
        document.querySelectorAll('[data-kpi-pct]').forEach(function (el) {
            var k = el.getAttribute('data-kpi-pct');
            var pct = total > 0 ? Math.round((n(c[k]) / total) * 100) : 0;
            el.textContent = pct + '% of total joining';
        });
    }

    /* ── events ── */
    var searchTimer;
    document.getElementById('jt_search').addEventListener('input', function () {
        clearTimeout(searchTimer);
        var val = this.value;
        searchTimer = setTimeout(function () { state.search = val; fetchTable(true); }, 380);
    });

    document.getElementById('jt_country').addEventListener('change', function () {
        state.country = this.value; fetchTable(true);
    });

    document.getElementById('jt_sort').addEventListener('change', function () {
        var v = this.value;
        if (v === 'onboarded' || v === 'backed_out') {
            // Status views: filter the table to those candidates, drop the tab-bar highlight,
            // and fall back to the latest-joining-date order.
            setTabsInactive();
            state.tab  = v;
            state.sort = '12';
        } else {
            state.sort = v;
        }
        clearHeaderSort();
        fetchTable(true);
    });

    /* Column-header sort (mirrors the Sort-by dropdown) */
    function clearHeaderSort() {
        document.querySelectorAll('.jt-th-sort').forEach(function (th) {
            th.classList.remove('active');
            var ico = th.querySelector('.jt-sort-ico'); if (ico) ico.textContent = '⇵';
        });
    }
    document.querySelectorAll('.jt-th-sort').forEach(function (th) {
        th.addEventListener('click', function () {
            var goAsc = this.dataset.dir !== 'asc';
            clearHeaderSort();
            this.dataset.dir = goAsc ? 'asc' : 'desc';
            this.classList.add('active');
            var ico = this.querySelector('.jt-sort-ico'); if (ico) ico.textContent = goAsc ? '↑' : '↓';
            state.sort = goAsc ? this.dataset.asc : this.dataset.desc;
            var sel = document.getElementById('jt_sort'); if (sel) sel.value = state.sort;
            fetchTable(true);
        });
    });

    /* tab clicks */
    function setTabsInactive() {
        document.querySelectorAll('#jt_tabs .jt-tab').forEach(function (t) { t.classList.remove('active'); });
    }
    function activateTab(filter) {
        document.querySelectorAll('#jt_tabs .jt-tab').forEach(function (t) {
            t.classList.toggle('active', t.dataset.filter === filter);
        });
        state.tab = filter;
        // A real tab supersedes the Sort dropdown's status views — revert it to "Latest".
        var sel = document.getElementById('jt_sort');
        if (sel && (sel.value === 'onboarded' || sel.value === 'backed_out')) { sel.value = '12'; state.sort = '12'; }
        fetchTable(true);
    }
    document.querySelectorAll('#jt_tabs .jt-tab').forEach(function (tab) {
        tab.addEventListener('click', function () { activateTab(this.dataset.filter); });
    });
    /* "View Today's List" card link jumps to the Joining Today tab */
    document.querySelectorAll('[data-jump-tab]').forEach(function (el) {
        el.addEventListener('click', function () { activateTab(this.dataset.jumpTab); });
    });

    /* pagination (delegated) */
    document.getElementById('jt_pagination').addEventListener('click', function (e) {
        var link = e.target.closest('.page-link');
        if (!link || link.classList.contains('disabled')) return;
        e.preventDefault();
        var pg = parseInt(link.dataset.page, 10);
        if (!pg) return;
        state.page = pg;
        fetchTable(false);
    });

    /* Stage sub-stage drawer */
    $(document).on('click', '#stage_sub_stage_close', function () {
        $('#csd_drawer_close').trigger('click');
    });

    $(document).on('click', '.stage_sub_stage', function () {
        $('#stage_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');
        $('#csd_drawer_toggle').trigger('click');
        var id  = $(this).data('row-id');
        var url = '{{ route('service_company.job-applications.stage_body_content', ':id') }}'.replace(':id', id);
        $.ajax({ type:'GET', url:url, success:function(r){
            if(r.status=='success') {
                $('#stage_body_content').html(r.view);
                $('#csd_drawer_subtitle').text($('#stage_body_content .csd-info-name').first().text());
            }
        } });
    });

    $(document).on('click', '#custom_stage_submit', function (e) {
        e.preventDefault();
        var ms = $('input[name="master_status"]:checked').val();
        var cm = $('#comment').val();
        if (ms == 8 && !cm) { Swal.fire("Warning",'InReview Comment Required!',"info"); return; }
        $(this).prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Waiting...');
        $.ajax({
            url:'{{ route('service_company.job-applications.stage_submit') }}', type:'POST',
            data: $('#status_frm').serialize(),
            success:function(r){
                $('#custom_stage_submit').prop("disabled",false).html('<span class="indicator-label">Submit</span>');
                if(r.status=='success'){
                    Swal.fire("Success",'Application stage updated!',"success").then(function(){ $("#stage_sub_stage_close").click(); });
                    fetchTable(false);
                } else if (r.status == 'cvpending') {
                    Swal.fire("Warning",'Resume pending this job application.',"warning").then(function(){ $("#stage_sub_stage_close").click(); });
                } else if (r.status == 'candidatevalidation') {
                    Swal.fire("Warning", r.message, "warning").then(function(){ $("#stage_sub_stage_close").click(); });
                }
            }
        });
    });

    /* Export (print-to-PDF, no server round-trip) */
    window.exportJoining = function () {
        var prev = document.title;
        document.title = 'Joining-Tracker-' + new Date().toISOString().slice(0,10);
        var done = function(){ document.title = prev; window.removeEventListener('afterprint', done); };
        window.addEventListener('afterprint', done);
        window.print();
        setTimeout(done, 1500);
    };

    /* ── init ── */
    fetchTable(false);
})();
</script>
@endpush
