@extends('layouts.service_company_layout')

@push('head-script')
<style>
/* ──────────────────────────────────────────────
   Offer Tracker — redesigned layout v2
   Font: Poppins (inherited), max 14px per UI spec
────────────────────────────────────────────── */
body { overflow-x: hidden; }

/* Poppins everywhere in the hero + pipeline; 16px hard cap per spec. */
.ot-page-head, .ot-stats-grid, .ot-pipeline-wrap,
.ot-page-head *, .ot-stats-grid *, .ot-pipeline-wrap * {
    font-family: 'Poppins', sans-serif !important;
}

/* Page header (hero) */
.ot-page-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 24px 0;
}
.ot-page-head h4 { font-size: 16px; font-weight: 700; margin: 0; color: #0f172a; }
.ot-page-head p  { font-size: 11px; color: #64748b; margin: 2px 0 0; }
.ot-head-actions { display: flex; gap: 10px; align-items: center; }
.ot-btn { display: flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 500; border: 1px solid #e2e8f0; background: #fff; cursor: pointer; color: #374151; }
.ot-btn:hover { background: #f8fafc; }
.ot-btn svg { width: 14px; height: 14px; }
.ot-btn-more { background: #fff; }

/* ── KPI stat cards (hero boxes) — heights trimmed ~20% ── */
.ot-stats-grid {
    display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px;
    padding: 14px 24px 0;
}
.ot-stat-card {
    background: #fff; border-radius: 14px; padding: 13px 16px;
    border: 1px solid #f1f5f9; display: flex; align-items: flex-start; gap: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.ot-stat-icon {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;
}
.ot-stat-icon svg { width: 18px; height: 18px; }
.ot-stat-body { flex: 1; min-width: 0; }
.ot-stat-label { font-size: 10px; color: #94a3b8; font-weight: 500; margin: 0 0 2px; text-transform: uppercase; letter-spacing: .4px; }
.ot-stat-value { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.1; }
.ot-stat-delta {
    font-size: 10px; margin-top: 4px; display: flex; align-items: center; gap: 3px; font-weight: 500;
}
.ot-stat-delta.up   { color: #16a34a; }
.ot-stat-delta.down { color: #dc2626; }
.ot-stat-delta.warn { color: #d97706; }
.ot-stat-delta.info { color: #0891b2; }
.ot-delta-ico { width: 13px; height: 13px; flex-shrink: 0; }

/* ── Pipeline section ── */
.ot-pipeline-wrap {
    background: #fff; border-radius: 14px; border: 1px solid #f1f5f9;
    margin: 14px 24px 0; padding: 14px 20px 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.ot-pipeline-head {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;
}
.ot-pipeline-head h5 { font-size: 14px; font-weight: 600; color: #0f172a; margin: 0; }
.ot-pipeline-range {
    border: 1px solid #e2e8f0; border-radius: 8px; padding: 5px 10px; font-size: 12px; color: #374151; background: #fff; cursor: pointer; display: flex; align-items: center; gap: 5px;
}
/* ── Arrow/breadcrumb pipeline cards ──
   Interlocking chevron segments built with clip-path only.
   A small flex `gap` leaves the clean white chevron seam between
   segments (no separate chevron elements, no negative margins —
   that combination was what broke the layout previously). The whole
   row lives in a horizontally-scrollable rail so the funnel never
   wraps or crushes on 15.6" / smaller corporate laptop screens. */
.ot-pipeline-rail { overflow-x: auto; overflow-y: hidden; padding-bottom: 2px; }
.ot-pipeline-rail::-webkit-scrollbar { height: 6px; }
.ot-pipeline-rail::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
/* No flex gap: segments OVERLAP via negative margin so each arrow point
   nests inside the next segment's notch, leaving only a thin parallel
   white seam (arrow depth 14px, overlap 10px → ~4px seam). */
.ot-pipeline-flow {
    display: flex; align-items: stretch; gap: 0;
}
.ot-pipe-card-v2 {
    flex: 1 1 0; min-width: 150px; position: relative;
    padding: 8px 14px 9px 24px;
    margin-left: -10px;
    /* middle segment: notch on the left, arrow point on the right */
    clip-path: polygon(0 0, calc(100% - 14px) 0, 100% 50%, calc(100% - 14px) 100%, 0 100%, 14px 50%);
}
/* First card — softly rounded left corners + arrow right (no left notch) */
.ot-pipe-card-v2:first-child {
    margin-left: 0; padding-left: 16px;
    clip-path: polygon(0 6px, 6px 0, calc(100% - 14px) 0, 100% 50%, calc(100% - 14px) 100%, 6px 100%, 0 calc(100% - 6px));
}
/* Last card — left notch + softly rounded right corners (no arrow point) */
.ot-pipe-card-v2:last-child {
    padding-right: 16px;
    clip-path: polygon(0 0, calc(100% - 6px) 0, 100% 6px, 100% calc(100% - 6px), calc(100% - 6px) 100%, 0 100%, 14px 50%);
}
.ot-pipe-head-row {
    display: flex; align-items: center; gap: 6px; margin-bottom: 5px;
}
.ot-pipe-icon-sm { width: 18px; height: 18px; flex-shrink: 0; }
.ot-pipe-icon-sm svg { width: 16px; height: 16px; }
.ot-pipe-lbl { font-size: 12px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ot-pipe-big { font-size: 16px; font-weight: 700; color: #0f172a; line-height: 1.1; margin-bottom: 2px; }
.ot-pipe-pct-v2 { font-size: 11px; color: #64748b; font-weight: 500; }
.ot-pipe-sub-v2 { font-size: 10px; color: #94a3b8; margin-top: 1px; }
/* Ratio card: percentage on the left, fraction pinned to the top-right corner */
.ot-pipe-ratio-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
.ot-pipe-ratio-row .ot-pipe-big { margin-bottom: 0; }
.ot-pipe-ratio-row .ot-pipe-sub-v2 { margin-top: 2px; white-space: nowrap; }

/* ── Table card ── */
.ot-card {
    background: #fff; border-radius: 14px; border: 1px solid #f1f5f9;
    margin: 18px 24px 24px; overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}

/* Search + filter bar */
.ot-filter-bar {
    display: flex; align-items: center; gap: 12px; padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9; flex-wrap: wrap;
}
.ot-filter-bar .ot-sort-bar { padding: 0; flex-shrink: 0; }
.ot-search-wrap { flex: 1; min-width: 220px; position: relative; }
.ot-search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; width: 16px; height: 16px; pointer-events: none; }
.ot-search-input {
    width: 100%; border: 1px solid #e2e8f0; border-radius: 9px; padding: 8px 12px 8px 36px;
    font-size: 13px; color: #374151; outline: none; background: #f8fafc;
}
.ot-search-input:focus { border-color: #6366f1; background: #fff; }
.ot-location-select {
    border: 1px solid #e2e8f0; border-radius: 9px; padding: 8px 12px; font-size: 13px; color: #374151; background: #f8fafc; cursor: pointer; outline: none; min-width: 160px;
}
.ot-location-select:focus { border-color: #6366f1; background: #fff; }

/* Tabs row */
.ot-tabs-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 20px; border-bottom: 1px solid #f1f5f9;
}
.ot-tabs { display: flex; gap: 0; overflow-x: auto; }
.ot-tab {
    padding: 11px 16px; font-size: 12px; font-weight: 500; color: #64748b;
    cursor: pointer; border-bottom: 2px solid transparent; white-space: nowrap; user-select: none;
    display: flex; align-items: center; gap: 6px; transition: color .15s;
}
.ot-tab:hover { color: #374151; }
.ot-tab-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 20px; height: 18px; border-radius: 99px; font-size: 10px; font-weight: 700;
    background: #f1f5f9; color: #64748b; padding: 0 5px; transition: all .15s;
}

/* Per-tab active colors */
.ot-tab[data-filter="all"].active        { color: #0f172a;  border-bottom-color: #0f172a;  font-weight: 700; }
.ot-tab[data-filter="all"].active .ot-tab-badge        { background: #e2e8f0; color: #0f172a; }
.ot-tab[data-filter="pending"].active    { color: #d97706;  border-bottom-color: #d97706;  font-weight: 700; }
.ot-tab[data-filter="pending"].active .ot-tab-badge    { background: #fef3c7; color: #d97706; }
.ot-tab[data-filter="sent"].active       { color: #2563eb;  border-bottom-color: #2563eb;  font-weight: 700; }
.ot-tab[data-filter="sent"].active .ot-tab-badge       { background: #dbeafe; color: #2563eb; }
.ot-tab[data-filter="accepted"].active   { color: #16a34a;  border-bottom-color: #16a34a;  font-weight: 700; }
.ot-tab[data-filter="accepted"].active .ot-tab-badge   { background: #dcfce7; color: #16a34a; }
.ot-tab[data-filter="joining_soon"].active { color: #0891b2; border-bottom-color: #0891b2; font-weight: 700; }
.ot-tab[data-filter="joining_soon"].active .ot-tab-badge { background: #cffafe; color: #0891b2; }
.ot-tab[data-filter="expired"].active    { color: #dc2626;  border-bottom-color: #dc2626;  font-weight: 700; }
.ot-tab[data-filter="expired"].active .ot-tab-badge    { background: #fee2e2; color: #dc2626; }

.ot-sort-bar { display: flex; align-items: center; gap: 10px; padding: 8px 0; }
.ot-sort-label { font-size: 12px; color: #94a3b8; white-space: nowrap; }
.ot-sort-select {
    border: 1px solid #e2e8f0; border-radius: 9px; padding: 8px 12px; font-size: 13px; color: #374151; background: #f8fafc; outline: none; cursor: pointer; min-width: 160px;
}
.ot-sort-select:focus { border-color: #6366f1; background: #fff; }
.ot-view-btns { display: flex; gap: 4px; }
.ot-view-btn {
    width: 30px; height: 30px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff;
    display: flex; align-items: center; justify-content: center; cursor: pointer; color: #94a3b8;
}
.ot-view-btn.active { background: #f1f5f9; color: #374151; border-color: #cbd5e1; }
.ot-view-btn svg { width: 14px; height: 14px; }

/* Table */
.ot-table { width: 100%; border-collapse: collapse; }
.ot-table thead tr { background: #f8fafc; }
.ot-table th {
    padding: 10px 14px; font-size: 11px; font-weight: 600; color: #64748b;
    text-align: left; white-space: nowrap; letter-spacing: .3px;
    border-bottom: 1px solid #f1f5f9;
}
.ot-table th .ot-sort-ico { margin-left: 4px; color: #cbd5e1; font-size: 10px; }
.ot-table th.ot-th-sort { cursor: pointer; user-select: none; }
.ot-table th.ot-th-sort:hover { color: #334155; }
.ot-table th.ot-th-sort:hover .ot-sort-ico { color: #94a3b8; }
.ot-table th.ot-th-sort.active { color: #6366f1; }
.ot-table th.ot-th-sort.active .ot-sort-ico { color: #6366f1; }
.ot-row { border-bottom: 1px solid #f8fafc; transition: background .12s; }
.ot-row:hover { background: #fafbff; }
.ot-td { padding: 12px 14px; vertical-align: middle; }

/* Candidate cell */
.ot-cand { display: flex; align-items: center; gap: 10px; }
.ot-avatar {
    width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center;
    justify-content: center; font-size: 15px; font-weight: 700; flex-shrink: 0;
}
.ot-avatar-img { width: 38px; height: 38px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
.ot-cand-info { min-width: 0; }
.ot-cand-name {
    font-size: 13px; font-weight: 600; color: #0f172a; display: block; text-decoration: none;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;
}
.ot-cand-name:hover { color: #6366f1; }
.ot-cand-role { font-size: 11px; color: #94a3b8; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }
.ot-bench { font-size: 9px; background: #fef3c7; color: #d97706; padding: 1px 5px; border-radius: 4px; font-weight: 600; }

/* Job cell */
.ot-job-title { font-size: 13px; font-weight: 600; color: #1e293b; display: block; }
.ot-job-type  { font-size: 11px; color: #94a3b8; display: block; margin-top: 2px; }

/* Status pill */
.ot-status-pill {
    display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: 11px; font-weight: 600; margin-bottom: 3px;
}
.ot-job-meta { font-size: 11px; color: #64748b; display: block; margin-top: 2px; }

/* Compensation */
.ot-comp-amount { font-size: 13px; font-weight: 600; color: #0f172a; display: block; }
.ot-comp-type   { font-size: 11px; color: #94a3b8; display: block; margin-top: 1px; }
.ot-comp-change { font-size: 11px; color: #6366f1; font-weight: 600; display: block; margin-top: 2px; }
a:hover .ot-comp-change { text-decoration: underline; }
.ot-pending-badge { font-size: 11px; color: #f59e0b; background: #fef3c7; padding: 3px 8px; border-radius: 6px; font-weight: 600; }
.ot-create-btn { font-size: 12px; color: #f59e0b; font-weight: 700; text-decoration: none; }
.ot-create-btn:hover { color: #d97706; text-decoration: underline; }

/* Offer Status badge with icon */
.ot-offer-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px 3px 8px; border-radius: 8px; font-size: 11px; font-weight: 700; margin-bottom: 3px;
}
.ot-offer-badge svg { width: 12px; height: 12px; flex-shrink: 0; }
.ot-offer-sub { font-size: 11px; color: #64748b; display: block; }
.ot-offer-date { font-size: 10px; color: #94a3b8; display: block; margin-top: 1px; }

/* Joining */
.ot-join-date      { font-size: 13px; font-weight: 600; color: #1e293b; display: block; }
.ot-join-time      { font-size: 11px; color: #94a3b8; display: block; margin-top: 1px; }
.ot-join-countdown { font-size: 11px; font-weight: 600; display: block; margin-top: 2px; }
.ot-na             { font-size: 14px; color: #cbd5e1; }
.ot-na-sub         { font-size: 11px; color: #94a3b8; display: block; }

/* Stage — outlined button */
.ot-td-stage { text-align: center; }
.ot-stage-btn {
    display: inline-block; padding: 5px 16px; border-radius: 8px; font-size: 12px; font-weight: 600;
    background: transparent; color: #7c3aed; border: 1.5px solid #c4b5fd;
    cursor: pointer; text-decoration: none; transition: all .15s;
}
.ot-stage-btn:hover { background: #ede9fe; border-color: #7c3aed; color: #7c3aed; }
.ot-stage-disabled {
    background: transparent; color: #94a3b8; border: 1.5px solid #e2e8f0; cursor: default;
}
.ot-stage-disabled:hover { background: transparent; border-color: #e2e8f0; }

/* Empty state */
.ot-empty td { padding: 60px 14px; text-align: center; }
.ot-empty-inner p { margin: 12px 0 0; font-size: 13px; color: #94a3b8; }

/* Table footer */
.ot-table-footer {
    display: flex; align-items: center; justify-content: flex-end;
    padding: 12px 20px; border-top: none; background: transparent;
}
.ot-rows-per { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b; }
.ot-rows-select {
    border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 8px; font-size: 12px; background: #fff; cursor: pointer;
}

/* loading shimmer cards */
.ot-shimmer { background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; height: 16px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Tighten a touch on ~1366px 15.6" laptops so all six
   segments fit the row without triggering the scroll rail. */
@media (max-width: 1440px) {
    .ot-pipe-card-v2 { padding: 8px 10px 9px 22px; min-width: 142px; }
}
@media (max-width: 1200px) {
    .ot-stats-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .ot-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .ot-page-head { flex-direction: column; align-items: flex-start; gap: 12px; }
    /* The funnel stays intact and simply scrolls horizontally on phones. */
    .ot-pipe-card-v2 { min-width: 144px; }
}
</style>
@endpush

@section('content')

{{-- ── Toolbar (page header, matches Metronic layout pattern) ── --}}
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
<div class="ot-page-head" style="padding:0;width:100%">
    <div>
        <h4>Offer Tracker</h4>
        <p>Track and manage all your offers in one place</p>
    </div>
    <div class="ot-head-actions">
        <button class="ot-btn" onclick="exportOffers()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M8 11l4 4 4-4M5 21h14"/></svg>
            Export
        </button>
        <button class="ot-btn ot-btn-more">
            More Actions
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
        </button>
    </div>
</div>
    </div>{{-- /kt_toolbar_container --}}
</div>{{-- /kt_toolbar --}}

<div class="content d-flex flex-column flex-column-fluid pt-2" id="kt_content">
<div class="post d-flex flex-column-fluid" id="kt_post">
<div class="container-fluid px-0">

{{-- ── KPI Stats (server-rendered) ── --}}
@php
    $statCards = [
        [
            'label'  => 'Total Offers',
            'key'    => 'total',
            'icon'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z',
            'bg'     => '#ede9fe', 'ic' => '#7c3aed',
            'delta'  => 'All offers tracked', 'delta_type' => 'info',
            'delta_ico' => null,
        ],
        [
            'label'  => 'Pending Offers',
            'key'    => 'pending',
            'icon'   => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
            'bg'     => '#fef3c7', 'ic' => '#d97706',
            'delta'  => 'Awaiting action', 'delta_type' => 'warn',
            'delta_ico' => 'up',
        ],
        [
            'label'  => 'Offer Released',
            'key'    => 'sent',
            'icon'   => 'M6 12L3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12zm0 0h7.5',
            'bg'     => '#dbeafe', 'ic' => '#2563eb',
            'delta'  => 'Released to candidate', 'delta_type' => 'up',
            'delta_ico' => 'up',
        ],
        [
            'label'  => 'Expired',
            'key'    => 'expired',
            'icon'   => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
            'bg'     => '#fee2e2', 'ic' => '#dc2626',
            'delta'  => 'Need renewal', 'delta_type' => 'down',
            'delta_ico' => 'down',
        ],
        [
            'label'  => 'Joining Soon',
            'key'    => 'joining_soon',
            'icon'   => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5',
            'bg'     => '#cffafe', 'ic' => '#0891b2',
            'delta'  => 'Next 7 days', 'delta_type' => 'info',
            'delta_ico' => null,
        ],
    ];
@endphp
<div class="ot-stats-grid">
    @foreach($statCards as $card)
    <div class="ot-stat-card">
        <div class="ot-stat-icon" style="background:{{ $card['bg'] }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="{{ $card['ic'] }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $card['icon'] }}"/></svg>
        </div>
        <div class="ot-stat-body">
            <p class="ot-stat-label">{{ $card['label'] }}</p>
            <p class="ot-stat-value" data-kpi="{{ $card['key'] }}">{{ $offerStats[$card['key']] ?? 0 }}</p>
            <div class="ot-stat-delta {{ $card['delta_type'] }}">
                @if($card['delta_ico'] === 'up')
                    <svg class="ot-delta-ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg>
                @elseif($card['delta_ico'] === 'down')
                    <svg class="ot-delta-ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8h-5V4H9v8H4z"/></svg>
                @endif
                <span>{{ $card['delta'] }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Pipeline (server-rendered) ── --}}
<div class="ot-pipeline-wrap">
    <div class="ot-pipeline-head">
        <h5>Offers Pipeline</h5>
        <select class="ot-pipeline-range" id="ot_pipeline_range">
            <option value="current">This Month</option>
            <option value="6m">Last 6 Months</option>
            <option value="all">All Time</option>
        </select>
    </div>
    <div class="ot-pipeline-rail">
        <div class="ot-pipeline-flow">
            @foreach($offerPipeline as $p)
            <div class="ot-pipe-card-v2" data-pipe="{{ $p['key'] ?? '' }}" style="background:{{ $p['icon_bg'] ?? '#f8fafc' }}">
                <div class="ot-pipe-head-row">
                    <span class="ot-pipe-icon-sm">
                        <svg viewBox="0 0 24 24" fill="none" stroke="{{ $p['icon_clr'] ?? $p['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $p['icon'] ?? '' }}"/>
                        </svg>
                    </span>
                    <span class="ot-pipe-lbl" style="color:{{ $p['color'] }}">{{ $p['label'] }}</span>
                </div>
                @if(isset($p['sub']))
                    {{-- Ratio card: percentage (left) and the fraction (top-right) share one row --}}
                    <div class="ot-pipe-ratio-row">
                        <span class="ot-pipe-big">{{ $p['pct'] }}%</span>
                        <span class="ot-pipe-sub-v2">{{ $p['sub'] }}</span>
                    </div>
                @else
                    <div class="ot-pipe-big">{{ $p['count'] }}</div>
                    <div class="ot-pipe-pct-v2">{{ $p['pct'] }}%</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Table card ── --}}
<div class="ot-card">
    {{-- Search + Location filter --}}
    <div class="ot-filter-bar">
        <div class="ot-search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            <input type="text" class="ot-search-input" id="ot_search" placeholder="Search by candidate name, job or offer ID..." />
        </div>
        <select class="ot-location-select" id="ot_country">
            <option value="">All Locations</option>
            @foreach($countries as $c)
            <option value="{{ $c->id }}">{{ $c->country_name }}</option>
            @endforeach
        </select>
        <div class="ot-sort-bar">
            <span class="ot-sort-label">Sort by:</span>
            <select class="ot-sort-select" id="ot_sort">
                <option value="">Latest Updated</option>
                <option value="10">Newest (Offer Date)</option>
                <option value="11">Oldest (Offer Date)</option>
                <option value="12">Newest (Join Date)</option>
                <option value="13">Oldest (Join Date)</option>
                <option value="14">Low → High (Amount)</option>
                <option value="15">High → Low (Amount)</option>
            </select>
        </div>
    </div>

    {{-- Tabs + Sort --}}
    <div class="ot-tabs-bar">
        <div class="ot-tabs" id="ot_tabs">
            <div class="ot-tab active" data-filter="all">All <span class="ot-tab-badge" id="tab_total">—</span></div>
            <div class="ot-tab" data-filter="pending">Pending <span class="ot-tab-badge" id="tab_pending">—</span></div>
            <div class="ot-tab" data-filter="sent">Offer Released <span class="ot-tab-badge" id="tab_sent">—</span></div>
            <div class="ot-tab" data-filter="accepted">Accepted <span class="ot-tab-badge" id="tab_accepted">—</span></div>
            <div class="ot-tab" data-filter="joining_soon">Joining Soon <span class="ot-tab-badge" id="tab_joining">—</span></div>
            <div class="ot-tab" data-filter="expired">Expired <span class="ot-tab-badge" id="tab_expired">—</span></div>
        </div>
        <div class="ot-sort-bar">
            <div class="ot-view-btns">
                <button class="ot-view-btn active" title="List">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <button class="ot-view-btn" title="Grid">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    @if(in_array('view_offer', $userPermissions))
    <div style="overflow-x:auto;">
        <table class="ot-table">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Job</th>
                    <th>Job Status, ID &amp; Client</th>
                    <th class="ot-th-sort" data-asc="14" data-desc="15">Compensation <span class="ot-sort-ico">⇵</span></th>
                    <th class="ot-th-sort" data-asc="11" data-desc="10">Offer Status <span class="ot-sort-ico">⇵</span></th>
                    <th class="ot-th-sort" data-asc="13" data-desc="12">Joining <span class="ot-sort-ico">⇵</span></th>
                    <th style="text-align:center;">Stage</th>
                </tr>
            </thead>
            <tbody id="offer-tracker">
                <tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;font-size:13px;">
                    <img src="{{ asset('assets/images/Loading2.gif') }}" height="32" alt=""> &nbsp; Loading...
                </td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer: pagination view already includes "Showing X to Y" info --}}
    <div id="ot_pagination"></div>
    <div class="ot-table-footer" style="border-top:none;padding-top:8px;">
        <div></div>
        <div></div>
        <div class="ot-rows-per">
            Rows per page:
            <select class="ot-rows-select" id="ot_size">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>
    @else
    <div style="padding:40px;text-align:center;color:#ef4444;font-size:13px;font-weight:600;">
        ACCESS DENIED — Contact your administrator
    </div>
    @endif
</div>

{{-- Modals (kept from original) --}}
<div class="modal fade" id="fwd_offer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header flex-stack">
                <h2 id="titleSet"></h2>
                <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)"/>
                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"/>
                    </svg>
                    </span>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="application_fwd_offer_body_content"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header flex-stack">
                <h2>Stage and sub stage</h2>
                <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal" id="stage_sub_stage_close">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)"/>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="stage_body_content" id="stage_body_content"></div>
        </div>
    </div>
</div>

</div>{{-- /container-fluid --}}
</div>{{-- /kt_post --}}
</div>{{-- /kt_content --}}

@endsection

@push('footer-script')
<script>
(function () {
    'use strict';

    /* ── state ── */
    var state = {
        page    : 1,
        size    : 10,
        search  : '',
        country : '',
        sort    : '',
        tab     : 'all',
        pipelineRange : 'current',
    };

    /* ── URLs ── */
    var URL_CANDIDATES = '{!! route('service_company.cvtracker.allcandidates') !!}';

    /* ── fetch table ── */
    function fetchTable(resetPage) {
        if (resetPage) state.page = 1;
        var loader = '<tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;font-size:13px;"><img src="{{ asset('assets/images/Loading2.gif') }}" height="32" alt=""> &nbsp; Loading...</td></tr>';
        document.getElementById('offer-tracker').innerHTML = loader;

        $.ajax({
            url  : URL_CANDIDATES + '?page=' + state.page,
            type : 'GET',
            data : {
                status_cus       : 5,
                size             : state.size,
                cus_search       : state.search,
                country          : state.country,
                sortingfilter    : state.sort,
                offer_tab_filter : state.tab,
                pipeline_range   : state.pipelineRange,
            },
            success: function (resp) {
                document.getElementById('offer-tracker').innerHTML = resp.html || '<tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">No offers found</td></tr>';
                document.getElementById('ot_pagination').innerHTML  = resp.pagination || '';
                if (resp.counts) updateOfferCounts(resp.counts, resp.pipelineCounts);
            },
            error: function () {
                document.getElementById('offer-tracker').innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:#ef4444;">Error loading data. Please refresh.</td></tr>';
            }
        });
    }

    /* ── Sync tab badges + KPI cards + pipeline from the Elasticsearch counts ──
       Every number comes from the same aggregation that backs the table, so the
       badges/hero/pipeline always match the actual filtered result set. */
    function updateOfferCounts(c, p) {
        if (!c) return;
        var n = function (v) { return (v != null ? v : 0); };

        /* Tab badges — from the full table result set */
        var badges = {
            tab_total: c.total, tab_pending: c.pending, tab_sent: c.sent,
            tab_accepted: c.accepted, tab_joining: c.joining_soon, tab_expired: c.expired
        };
        Object.keys(badges).forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.textContent = n(badges[id]);
        });

        /* KPI stat cards (data-kpi) — from the full table result set */
        var kpi = {
            total: c.total, pending: c.pending, sent: c.sent,
            expired: c.expired, joining_soon: c.joining_soon
        };
        document.querySelectorAll('[data-kpi]').forEach(function (el) {
            var k = el.getAttribute('data-kpi');
            if (kpi[k] != null) el.textContent = kpi[k];
        });

        /* Pipeline arrows (data-pipe) — driven by the date-windowed counts
           (dropdown: this month / last 6 months / all time); falls back to
           the full counts if the server didn't return a windowed set. */
        var pc = p || c;
        var ptotal = +pc.total || 0;
        var pctOf = function (v) { return ptotal > 0 ? Math.round((v / ptotal) * 100) : 0; };
        var ratio = pctOf(n(pc.joined));
        var pipe = {
            total:    { count: ptotal,        pct: 100 },
            sent:     { count: n(pc.sent),    pct: pctOf(n(pc.sent)) },
            accepted: { count: n(pc.accepted),pct: pctOf(n(pc.accepted)) },
            rejected: { count: n(pc.rejected),pct: pctOf(n(pc.rejected)) },
            joined:   { count: n(pc.joined),  pct: ratio },
            ratio:    { count: ratio + '%',   pct: ratio, sub: n(pc.joined) + '/' + ptotal }
        };
        document.querySelectorAll('[data-pipe]').forEach(function (card) {
            var d = pipe[card.getAttribute('data-pipe')];
            if (!d) return;
            var big = card.querySelector('.ot-pipe-big');
            var pctEl = card.querySelector('.ot-pipe-pct-v2');
            var subEl = card.querySelector('.ot-pipe-sub-v2');
            if (big)   big.textContent = d.count;
            if (pctEl) pctEl.textContent = d.pct + '%';
            if (subEl) subEl.textContent = d.sub || '';
        });
    }

    /* ── events ── */
    /* search (debounced) */
    var searchTimer;
    document.getElementById('ot_search').addEventListener('input', function () {
        clearTimeout(searchTimer);
        var val = this.value;
        searchTimer = setTimeout(function () {
            state.search = val;
            fetchTable(true);
        }, 380);
    });

    document.getElementById('ot_country').addEventListener('change', function () {
        state.country = this.value;
        fetchTable(true);
    });

    document.getElementById('ot_sort').addEventListener('change', function () {
        state.sort = this.value;
        clearHeaderSort();          /* dropdown wins → drop any column indicator */
        fetchTable(true);
    });

    /* ── Column-header sorting (kept in sync with the Sort-by dropdown) ──
       Each sortable <th> carries the backend sort codes for asc/desc; clicking
       toggles direction, mirrors the value into #ot_sort, and refetches. */
    function clearHeaderSort() {
        document.querySelectorAll('.ot-th-sort').forEach(function (th) {
            th.classList.remove('active');
            var ico = th.querySelector('.ot-sort-ico');
            if (ico) ico.textContent = '⇵';
        });
    }
    document.querySelectorAll('.ot-th-sort').forEach(function (th) {
        th.addEventListener('click', function () {
            var asc  = this.dataset.asc;
            var desc = this.dataset.desc;
            var goAsc = this.dataset.dir !== 'asc';   /* toggle; default → asc first */
            clearHeaderSort();
            this.dataset.dir = goAsc ? 'asc' : 'desc';
            this.classList.add('active');
            var ico = this.querySelector('.ot-sort-ico');
            if (ico) ico.textContent = goAsc ? '↑' : '↓';
            state.sort = goAsc ? asc : desc;
            var sel = document.getElementById('ot_sort');
            if (sel) sel.value = state.sort;          /* keep dropdown reflecting the header */
            fetchTable(true);
        });
    });

    document.getElementById('ot_size').addEventListener('change', function () {
        state.size = parseInt(this.value, 10);
        fetchTable(true);
    });

    /* Offers Pipeline date window (this month / last 6 months / all time) */
    var pipeRangeEl = document.getElementById('ot_pipeline_range');
    if (pipeRangeEl) {
        pipeRangeEl.addEventListener('change', function () {
            state.pipelineRange = this.value;
            fetchTable(false);   /* only the pipeline counts change; keep the current page */
        });
    }

    /* tab clicks */
    document.querySelectorAll('#ot_tabs .ot-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('#ot_tabs .ot-tab').forEach(function (t) { t.classList.remove('active'); });
            this.classList.add('active');
            state.tab = this.dataset.filter;
            fetchTable(true);
        });
    });

    /* pagination (delegated) */
    document.getElementById('ot_pagination').addEventListener('click', function (e) {
        var link = e.target.closest('.page-link');
        if (!link || link.classList.contains('disabled')) return;
        e.preventDefault();
        var pg = parseInt(link.dataset.page, 10);
        if (!pg) return;
        state.page = pg;
        fetchTable(false);
    });

    /* Stage sub-stage */
    $(document).on('click', '.stage_sub_stage', function () {
        $('#stage_body_content').html('');
        var id  = $(this).data('row-id');
        var url = '{{ route('service_company.job-applications.stage_body_content', ':id') }}'.replace(':id', id);
        $.ajax({ type:'GET', url:url, success:function(r){ if(r.status=='success') $('#stage_body_content').html(r.view); } });
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
                    Swal.fire("Success",'Stage updated!',"success").then(function(){ $("#stage_sub_stage_close").click(); });
                    fetchTable(false);
                }
            }
        });
    });

    /* Fwd offer */
    $(document).on('click', '.fwd_offer', function (e) {
        e.preventDefault();
        var jobId     = $(this).data('jobId');
        var candidate = $(this).data('candidate');
        var mtype     = $(this).data('mtype');
        var offerId   = $(this).data('offerId');
        var url = '{{ route('service_company.cvtracker.offer_send_mail', ':id') }}'.replace(':id', jobId);
        $.ajax({
            type:'POST', url:url,
            data:{_token:'{{ csrf_token() }}', _method:'POST', arr:[candidate], jobIdArr:[jobId], mtype:mtype, offerId:offerId},
            success:function(r){ if(r.status=='success'){ $('#titleSet').text(r.title); $('#application_fwd_offer_body_content').html(r.view); } }
        });
    });

    /* Client-job offer: show warning instead of opening create-offer drawer */
    $(document).on('click', '.ot-client-offer-warn', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Not Allowed',
                text: 'This offer belongs to a client job. You cannot create or edit the offer here.',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: { confirmButton: 'btn btn-primary' }
            });
        }
    });

    /* Export */
    window.exportOffers = function () {
        var prevTitle = document.title;
        document.title = 'Offer-Tracker-' + new Date().toISOString().slice(0,10);
        var done = function(){ document.title = prevTitle; window.removeEventListener('afterprint', done); };
        window.addEventListener('afterprint', done);
        window.print();
        setTimeout(done, 1500);
    };

    /* ── init ── */
    fetchTable(false);

})();
</script>
@endpush
