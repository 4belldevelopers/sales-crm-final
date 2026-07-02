@extends('layouts.service_company_layout')

@push('head-script')
<style>
/* ──────────────────────────────────────────────
   Offer Tracker — redesigned layout
   Font: Poppins (inherited), max 14px per UI spec
────────────────────────────────────────────── */
body { overflow-x: hidden; }

/* Page header */
.ot-page-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 24px 0;
}
.ot-page-head h4 { font-size: 20px; font-weight: 700; margin: 0; color: #0f172a; }
.ot-page-head p  { font-size: 12px; color: #64748b; margin: 2px 0 0; }
.ot-head-actions { display: flex; gap: 10px; align-items: center; }
.ot-btn { display: flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: 1px solid #e2e8f0; background: #fff; cursor: pointer; color: #374151; }
.ot-btn:hover { background: #f8fafc; }
.ot-btn svg { width: 15px; height: 15px; }
.ot-btn-more { background: #fff; }

/* ── KPI stat cards ── */
.ot-stats-grid {
    display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px;
    padding: 18px 24px 0;
}
.ot-stat-card {
    background: #fff; border-radius: 14px; padding: 18px 20px;
    border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 14px;
}
.ot-stat-icon {
    width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ot-stat-icon svg { width: 22px; height: 22px; }
.ot-stat-body { flex: 1; min-width: 0; }
.ot-stat-label { font-size: 11px; color: #94a3b8; font-weight: 500; margin: 0 0 4px; text-transform: uppercase; letter-spacing: .4px; }
.ot-stat-value { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1; }
.ot-stat-delta { font-size: 11px; margin-top: 5px; display: flex; align-items: center; gap: 3px; }
.ot-stat-delta.up { color: #22c55e; }
.ot-stat-delta.down { color: #ef4444; }
.ot-stat-delta.neutral { color: #64748b; }

/* ── Pipeline section ── */
.ot-pipeline-wrap {
    background: #fff; border-radius: 14px; border: 1px solid #f1f5f9;
    margin: 18px 24px 0; padding: 18px 22px 20px;
}
.ot-pipeline-head {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;
}
.ot-pipeline-head h5 { font-size: 14px; font-weight: 600; color: #0f172a; margin: 0; }
.ot-pipeline-range {
    border: 1px solid #e2e8f0; border-radius: 8px; padding: 5px 10px; font-size: 12px; color: #374151; background: #fff; cursor: pointer;
}
.ot-pipeline-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; }
.ot-pipe-card {
    text-align: center; padding: 14px 10px 16px;
    border: 1px solid #f1f5f9; border-radius: 12px; position: relative;
}
.ot-pipe-label { font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .3px; }
.ot-pipe-count { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 2px; line-height: 1.1; }
.ot-pipe-sub   { font-size: 11px; color: #94a3b8; }
.ot-pipe-bar   { height: 4px; border-radius: 99px; background: #f1f5f9; margin-top: 10px; overflow: hidden; }
.ot-pipe-fill  { height: 100%; border-radius: 99px; transition: width .5s; }
.ot-pipe-pct   { font-size: 11px; color: #94a3b8; margin-top: 5px; }

/* ── Table card ── */
.ot-card {
    background: #fff; border-radius: 14px; border: 1px solid #f1f5f9;
    margin: 18px 24px 24px; overflow: hidden;
}

/* Search + filter bar */
.ot-filter-bar {
    display: flex; align-items: center; gap: 12px; padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
}
.ot-search-wrap {
    flex: 1; position: relative;
}
.ot-search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; width: 16px; height: 16px; pointer-events: none; }
.ot-search-input {
    width: 100%; border: 1px solid #e2e8f0; border-radius: 9px; padding: 8px 12px 8px 36px;
    font-size: 13px; color: #374151; outline: none; background: #f8fafc;
}
.ot-search-input:focus { border-color: #6366f1; background: #fff; }
.ot-location-select {
    border: 1px solid #e2e8f0; border-radius: 9px; padding: 8px 12px; font-size: 13px; color: #374151; background: #fff; cursor: pointer; outline: none; min-width: 160px;
}
.ot-location-select:focus { border-color: #6366f1; }

/* Tabs row */
.ot-tabs-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 20px; border-bottom: 1px solid #f1f5f9;
}
.ot-tabs { display: flex; gap: 0; overflow-x: auto; }
.ot-tab {
    padding: 11px 16px; font-size: 12px; font-weight: 500; color: #64748b;
    cursor: pointer; border-bottom: 2px solid transparent; white-space: nowrap; user-select: none;
    display: flex; align-items: center; gap: 6px;
}
.ot-tab:hover { color: #374151; }
.ot-tab.active { color: #6366f1; border-bottom-color: #6366f1; font-weight: 600; }
.ot-tab-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 20px; height: 18px; border-radius: 99px; font-size: 10px; font-weight: 700;
    background: #f1f5f9; color: #64748b; padding: 0 5px;
}
.ot-tab.active .ot-tab-badge { background: #e0e7ff; color: #6366f1; }

.ot-sort-bar { display: flex; align-items: center; gap: 10px; padding: 8px 0; }
.ot-sort-label { font-size: 12px; color: #94a3b8; white-space: nowrap; }
.ot-sort-select {
    border: 1px solid #e2e8f0; border-radius: 7px; padding: 5px 10px; font-size: 12px; color: #374151; background: #fff; outline: none; cursor: pointer;
}
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
.ot-pending-badge { font-size: 11px; color: #f59e0b; background: #fef3c7; padding: 3px 8px; border-radius: 6px; font-weight: 600; }
.ot-create-btn { font-size: 12px; color: #6366f1; font-weight: 600; text-decoration: none; }
.ot-create-btn:hover { text-decoration: underline; }

/* Offer Status */
.ot-offer-badge {
    display: inline-block; padding: 3px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; margin-bottom: 3px;
}
.ot-offer-sub { font-size: 11px; color: #64748b; display: block; }

/* Joining */
.ot-join-date      { font-size: 13px; font-weight: 600; color: #1e293b; display: block; }
.ot-join-time      { font-size: 11px; color: #94a3b8; display: block; margin-top: 1px; }
.ot-join-countdown { font-size: 11px; font-weight: 600; display: block; margin-top: 2px; }
.ot-na             { font-size: 14px; color: #cbd5e1; }
.ot-na-sub         { font-size: 11px; color: #94a3b8; display: block; }

/* Stage */
.ot-td-stage { text-align: center; }
.ot-stage-btn {
    display: inline-block; padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
    background: #ede9fe; color: #7c3aed; cursor: pointer; text-decoration: none;
    transition: background .12s;
}
.ot-stage-btn:hover { background: #ddd6fe; }
.ot-stage-disabled { background: #f1f5f9; color: #94a3b8; cursor: default; }

/* Empty state */
.ot-empty td { padding: 60px 14px; text-align: center; }
.ot-empty-inner p { margin: 12px 0 0; font-size: 13px; color: #94a3b8; }

/* Table footer */
.ot-table-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 20px; border-top: 1px solid #f1f5f9; background: #fafafa;
}
.ot-showing { font-size: 12px; color: #64748b; }
.ot-rows-per { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b; }
.ot-rows-select {
    border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 8px; font-size: 12px; background: #fff; cursor: pointer;
}
.ot-pagination-wrap { display: flex; align-items: center; }
.ot-pagination-wrap .pagination { margin: 0; }

/* loading shimmer cards */
.ot-shimmer { background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; height: 16px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

@media (max-width: 1200px) {
    .ot-stats-grid { grid-template-columns: repeat(3, 1fr); }
    .ot-pipeline-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .ot-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .ot-pipeline-grid { grid-template-columns: repeat(2, 1fr); }
    .ot-page-head { flex-direction: column; align-items: flex-start; gap: 12px; }
}
</style>
@endpush

@section('content')

{{-- ── Page header ── --}}
<div class="ot-page-head">
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

{{-- ── KPI Stats (server-rendered) ── --}}
@php
    $statCards = [
        ['label'=>'Total Offers',      'key'=>'total',        'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z', 'bg'=>'#ede9fe','ic'=>'#7c3aed','delta'=>null],
        ['label'=>'Pending Offers',    'key'=>'pending',      'icon'=>'M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10Zm0-7v2m0-6v2',                                               'bg'=>'#fef3c7','ic'=>'#d97706','delta'=>null],
        ['label'=>'Sent for Approval', 'key'=>'sent',         'icon'=>'M6 12l4 4 8-8',                                                                                                                          'bg'=>'#dbeafe','ic'=>'#2563eb','delta'=>null],
        ['label'=>'Expired',           'key'=>'expired',      'icon'=>'M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10Zm0-7V8m0 7v2',                                                'bg'=>'#fee2e2','ic'=>'#dc2626','delta'=>null],
        ['label'=>'Joining Soon',      'key'=>'joining_soon', 'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z',                                       'bg'=>'#dcfce7','ic'=>'#16a34a','delta'=>'Next 7 days'],
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
            <p class="ot-stat-value">{{ $offerStats[$card['key']] ?? 0 }}</p>
            @if($card['delta'])
            <div class="ot-stat-delta neutral"><span>{{ $card['delta'] }}</span></div>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- ── Pipeline (server-rendered) ── --}}
<div class="ot-pipeline-wrap">
    <div class="ot-pipeline-head">
        <h5>Offers Pipeline</h5>
        <select class="ot-pipeline-range">
            <option>This Month</option>
        </select>
    </div>
    <div class="ot-pipeline-grid">
        @foreach($offerPipeline as $p)
        <div class="ot-pipe-card">
            <div class="ot-pipe-label">{{ $p['label'] }}</div>
            <div class="ot-pipe-count" style="color:{{ $p['color'] }}">{{ $p['count'] }}</div>
            @if(isset($p['sub'])) <div class="ot-pipe-sub">{{ $p['sub'] }}</div> @endif
            <div class="ot-pipe-bar"><div class="ot-pipe-fill" style="width:{{ $p['pct'] }}%;background:{{ $p['color'] }}"></div></div>
            <div class="ot-pipe-pct">{{ $p['pct'] }}%</div>
        </div>
        @endforeach
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
    </div>

    {{-- Tabs + Sort --}}
    <div class="ot-tabs-bar">
        <div class="ot-tabs" id="ot_tabs">
            <div class="ot-tab active" data-filter="all">All <span class="ot-tab-badge" id="tab_total">—</span></div>
            <div class="ot-tab" data-filter="pending">Pending <span class="ot-tab-badge" id="tab_pending">—</span></div>
            <div class="ot-tab" data-filter="sent">Sent for Approval <span class="ot-tab-badge" id="tab_sent">—</span></div>
            <div class="ot-tab" data-filter="accepted">Accepted <span class="ot-tab-badge" id="tab_accepted">—</span></div>
            <div class="ot-tab" data-filter="joining_soon">Joining Soon <span class="ot-tab-badge" id="tab_joining">—</span></div>
            <div class="ot-tab" data-filter="expired">Expired <span class="ot-tab-badge" id="tab_expired">—</span></div>
        </div>
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
                    <th>Candidate <span class="ot-sort-ico">⇵</span></th>
                    <th>Job <span class="ot-sort-ico">⇵</span></th>
                    <th>Job Status, ID & Client <span class="ot-sort-ico">⇵</span></th>
                    <th>Compensation <span class="ot-sort-ico">⇵</span></th>
                    <th>Offer Status <span class="ot-sort-ico">⇵</span></th>
                    <th>Joining <span class="ot-sort-ico">⇵</span></th>
                    <th style="text-align:center;">Stage <span class="ot-sort-ico">⇵</span></th>
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
            },
            success: function (resp) {
                document.getElementById('offer-tracker').innerHTML = resp.html || '<tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">No offers found</td></tr>';
                document.getElementById('ot_pagination').innerHTML  = resp.pagination || '';
            },
            error: function () {
                document.getElementById('offer-tracker').innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:#ef4444;">Error loading data. Please refresh.</td></tr>';
            }
        });
    }

    /* ── Tab badge counts from server-rendered stats ── */
    (function () {
        var map = {
            tab_total   : {{ $offerStats['total'] ?? 0 }},
            tab_pending : {{ $offerStats['pending'] ?? 0 }},
            tab_sent    : {{ $offerStats['sent'] ?? 0 }},
            tab_accepted: {{ $offerStats['accepted'] ?? 0 }},
            tab_joining : {{ $offerStats['joining_soon'] ?? 0 }},
            tab_expired : {{ $offerStats['expired'] ?? 0 }},
        };
        Object.keys(map).forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.textContent = map[id];
        });
    })();

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
        fetchTable(true);
    });

    document.getElementById('ot_size').addEventListener('change', function () {
        state.size = parseInt(this.value, 10);
        fetchTable(true);
    });

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
