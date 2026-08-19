@extends('layouts.service_company_layout')

@push('head-script')
<style>
/* ==========================================================================
 * Transaction Log V2 — Professional Design
 * Metronic-compatible · Poppins · max 14px · clean icon-based · no 3D
 * ========================================================================== */

/* ── Summary Stats Bar ────────────────────────────────────────────────────── */
.txlog-stats-bar {
    display: flex;
    gap: 12px;
    padding: 16px 20px 0;
    flex-wrap: wrap;
}
.txlog-stat-card {
    flex: 1;
    min-width: 160px;
    background: #fff;
    border: 1px solid #f1f1f4;
    border-radius: 10px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: box-shadow 0.2s;
}
.txlog-stat-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.txlog-stat-icon {
    width: 40px; height: 40px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.txlog-stat-icon.ic-blue  { background: #eef6ff; }
.txlog-stat-icon.ic-green { background: #e8fff3; }
.txlog-stat-icon.ic-red   { background: #fff5f8; }
.txlog-stat-icon.ic-amber { background: #fff8dd; }
.txlog-stat-icon svg { width: 20px; height: 20px; }
.txlog-stat-info .stat-val  { font-size: 18px; font-weight: 700; color: #181c32; line-height: 1.2; }
.txlog-stat-info .stat-lbl  { font-size: 11px; color: #a1a5b7; font-weight: 500; margin-top: 2px; }

/* ── Filter Bar ───────────────────────────────────────────────────────────── */
.txlog-filter-bar {
    display: flex;
    flex-wrap: nowrap;        /* keep all controls on ONE row */
    gap: 10px;
    align-items: flex-end;
    padding: 12px 20px 12px;
    background: #fafafa;
    border-top: 1px solid #f1f1f4;
    border-bottom: 1px solid #f1f1f4;
    overflow-x: auto;         /* allow horizontal scroll on very small screens */
}
.txlog-filter-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.txlog-filter-label {
    font-size: 11px;
    font-weight: 600;
    color: #7e8299;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.txlog-filter-bar .form-control,
.txlog-filter-bar .form-select {
    font-size: 13px;
    border: 1.5px solid #e4e6ef;
    border-radius: 7px;
    background: #fff;
    color: #3f4254;
    height: 36px;
    padding: 0 10px;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
    box-shadow: none;
}
.txlog-filter-bar .form-control:focus,
.txlog-filter-bar .form-select:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0,158,247,.12);
}
.txlog-filter-bar .form-select { padding-right: 28px; }
.txlog-search-wrap {
    position: relative;
}
.txlog-search-wrap .search-ico {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #b5b5c3;
    pointer-events: none;
    display: flex;
}
.txlog-search-wrap .search-ico svg { width: 15px; height: 15px; }
.txlog-search-wrap input { padding-left: 32px !important; }

/* Month preset chips */
.txlog-preset-chips {
    display: flex;
    gap: 6px;
    align-items: flex-end;
    padding-bottom: 0;
}
.txlog-chip {
    font-size: 11px;
    font-weight: 600;
    padding: 0 10px;
    height: 28px;
    border-radius: 20px;
    border: 1.5px solid #e4e6ef;
    background: #fff;
    color: #7e8299;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    transition: all 0.15s;
    white-space: nowrap;
    margin-bottom: 4px;
}
.txlog-chip:hover, .txlog-chip.active-chip {
    background: #009ef7;
    border-color: #009ef7;
    color: #fff;
}

.btn-txreset {
    height: 36px;
    padding: 0 16px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 7px;
    border: 1.5px solid #e4e6ef;
    background: #fff;
    color: #7e8299;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
}
.btn-txreset:hover { border-color: #d1d5db; color: #3f4254; background: #f9f9f9; }
.btn-txreset svg { width: 13px; height: 13px; }

/* ── Shared CSS Grid — header + data rows share the same columns ─────────── */
/*   icon | date | event | actor | amount | balance                           */
/*   50px   130px  1fr    170px   115px    100px                               */
.txlog-grid {
    display: grid;
    grid-template-columns: 50px 130px 1fr 170px 115px 100px;
    align-items: center;
    padding: 0 20px;
}

/* ── Table-style Timeline Header ─────────────────────────────────────────── */
.txlog-table-header {
    background: #fafafa;
    border-bottom: 1.5px solid #f1f1f4;
    padding-top: 8px;
    padding-bottom: 8px;
}
.txlog-table-header .th {
    font-size: 11px;
    font-weight: 700;
    color: #a1a5b7;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.txlog-table-header .th-right { text-align: right; }

/* ── Timeline Content ─────────────────────────────────────────────────────── */
.txlog-timeline { min-height: 200px; }
.txlog-item {
    border-bottom: 1px solid #f4f4f4;
    transition: background 0.12s;
    animation: txfadeIn 0.22s ease;
    padding-top: 12px;
    padding-bottom: 12px;
}
.txlog-item:hover { background: #fafcff; }
.txlog-item:last-child { border-bottom: none; }
@keyframes txfadeIn {
    from { opacity: 0; transform: translateY(3px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Icon cell */
.txlog-ic-col { display: flex; align-items: center; }
.txlog-row-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.txlog-row-icon svg { width: 15px; height: 15px; }
.ri-credit { background: #e8fff3; }
.ri-debit  { background: #fff5f8; }
.ri-settle { background: #fff8dd; }

/* Data cells — right-align amount and balance */
.tx-col-event  { padding-right: 16px; min-width: 0; }
.tx-col-amount { text-align: right; padding-right: 4px; }
.tx-col-bal    { text-align: right; }

/* Cell typography */
.cell-main { font-size: 13px; font-weight: 600; color: #181c32; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cell-sub  { font-size: 11px; color: #b5b5c3; margin-top: 1px; }
.tx-col-event .cell-main { white-space: normal; overflow: visible; text-overflow: unset; }

/* Badges */
.tx-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}
.tx-badge.credit { background: #e8fff3; color: #50cd89; }
.tx-badge.debit  { background: #fff5f8; color: #f1416c; }
.tx-badge.settle { background: #fff8dd; color: #f6c000; }
.tx-badge .dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

/* Amount coloring */
.amt-credit { color: #50cd89; font-size: 13px; font-weight: 700; }
.amt-debit  { color: #f1416c; font-size: 13px; font-weight: 700; }
.bal-num    { font-size: 13px; font-weight: 700; color: #009ef7; }

/* ── Pagination ───────────────────────────────────────────────────────────── */
.txlog-pagination {
    display: flex;
    gap: 4px;
    justify-content: flex-end;
    align-items: center;
    padding: 14px 20px;
    border-top: 1px solid #f1f1f4;
    flex-wrap: wrap;
}
.txlog-pagination .pg-info {
    flex: 1;
    font-size: 12px;
    color: #a1a5b7;
    font-weight: 500;
    min-width: 180px;
}
.txlog-pagination .page-link {
    padding: 0 10px;
    font-size: 12px;
    font-weight: 600;
    color: #5e6278;
    border: 1.5px solid transparent;
    border-radius: 7px;
    cursor: pointer;
    text-decoration: none;
    background: transparent;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
}
.txlog-pagination .page-link:hover { background: #f1f3f4; color: #181c32; }
.txlog-pagination .page-link.active { background: #009ef7; color: #fff; border-color: #009ef7; }
.txlog-pagination .page-link.disabled { opacity: 0.35; pointer-events: none; }

/* ── Loading & Empty ──────────────────────────────────────────────────────── */
.txlog-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 50px 0;
    gap: 10px;
    color: #a1a5b7;
    font-size: 13px;
}
.txlog-spinner {
    width: 22px; height: 22px;
    border: 2.5px solid #e4e6ef;
    border-top-color: #009ef7;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.txlog-empty {
    text-align: center;
    padding: 55px 20px;
    color: #b5b5c3;
    font-size: 13px;
}
.txlog-empty .empty-ico {
    width: 52px; height: 52px;
    background: #f4f4f4;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}
.txlog-empty .empty-ico svg { width: 24px; height: 24px; opacity: 0.4; }
.txlog-empty p { margin: 0; font-weight: 500; }
.txlog-empty .empty-sub { font-size: 11px; color: #d1d1d1; margin-top: 4px; }
</style>
@endpush

@section('content')

{{-- Toolbar --}}
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
        <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <div class="d-flex flex-column">
                <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                    <span class="text-gray-800 mb-1">Coins Activity</span>
                </h5>
                <span class="text-gray-500 fs-7">Track your operational and sales coin transactions</span>
            </div>
        </div>
        <div>
            <a href="{{ url('service_company/subscribe') }}"
               class="btn btn-sm btn-light-primary fw-bold fs-7">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1" style="vertical-align:-2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                View Coin Usage Table
            </a>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid px-3">
            <div class="card shadow-sm">

                {{-- Tab Navigation --}}
                <div class="card-header card-header-tabs-line border-bottom-0 pt-0 px-5">
                    <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
                            <li class="nav-item me-3">
                                <a class="nav-link txlog-tab active" href="#" data-pagetype="operationaltab" id="tab-operational">
                                    <span class="nav-text fw-bold">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1" style="vertical-align:-1px"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                                        Operational Coins
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link txlog-tab" href="#" data-pagetype="salestab" id="tab-sales">
                                    <span class="nav-text fw-bold">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1" style="vertical-align:-1px"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                        Sales Coins
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    {{-- Summary Stats (right side of header) --}}
                    <div class="card-toolbar ms-auto" id="txlog-header-stats" style="gap:12px;display:flex;align-items:center;padding: 10px 0;">
                        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background:#f9fafb;border:1px solid #f1f1f4;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#50cd89;display:inline-block;"></span>
                            <span class="fs-7 fw-bold text-gray-600">Credit: <span id="stat-credit" class="text-success">—</span></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background:#f9fafb;border:1px solid #f1f1f4;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#f1416c;display:inline-block;"></span>
                            <span class="fs-7 fw-bold text-gray-600">Debit: <span id="stat-debit" class="text-danger">—</span></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background:#f9fafb;border:1px solid #f1f1f4;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#009ef7;display:inline-block;"></span>
                            <span class="fs-7 fw-bold text-gray-600">Total: <span id="stat-total" class="text-primary">—</span></span>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="txlog-filter-bar">

                    {{-- Search --}}
                    <div class="txlog-filter-group">
                        <span class="txlog-filter-label">Search</span>
                        <div class="txlog-search-wrap">
                            <span class="search-ico">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </span>
                            <input type="text" id="txlog-search" class="form-control"
                                   placeholder="Event or user name…" style="width:230px;">
                        </div>
                    </div>

                    {{-- Date From --}}
                    <div class="txlog-filter-group">
                        <span class="txlog-filter-label">From Date</span>
                        <input type="date" id="txlog-date-from" class="form-control" style="width:148px;">
                    </div>

                    {{-- Date To --}}
                    <div class="txlog-filter-group">
                        <span class="txlog-filter-label">To Date</span>
                        <input type="date" id="txlog-date-to" class="form-control" style="width:148px;">
                    </div>

                    {{-- Preset chips --}}
                    <div class="txlog-filter-group">
                        <span class="txlog-filter-label">Quick Range</span>
                        <div class="txlog-preset-chips">
                            <button class="txlog-chip active-chip" data-preset="this_month">This Month</button>
                            <button class="txlog-chip" data-preset="last_month">Last Month</button>
                            <button class="txlog-chip" data-preset="last_7">7 Days</button>
                            <button class="txlog-chip" data-preset="all">All Time</button>
                        </div>
                    </div>

                    {{-- Type --}}
                    <div class="txlog-filter-group">
                        <span class="txlog-filter-label">Type</span>
                        <select id="txlog-type" class="form-select" style="width:130px;">
                            <option value="">All Types</option>
                            <option value="credit">Credit</option>
                            <option value="debit">Debit</option>
                        </select>
                    </div>


                    {{-- Reset --}}
                    <div class="txlog-filter-group" style="justify-content:flex-end;">
                        <span class="txlog-filter-label" style="visibility:hidden;">x</span>
                        <button id="txlog-reset" class="btn-txreset">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            Reset
                        </button>
                    </div>

                </div>

                {{-- Table Column Headers --}}
                <div class="txlog-table-header txlog-grid" id="txlog-table-header">
                    <div></div>
                    <div class="th">Date &amp; Time</div>
                    <div class="th">Event Description</div>
                    <div class="th">Initiated By</div>
                    <div class="th" style="text-align:right;">Amount</div>
                    <div class="th" style="text-align:right;">Balance</div>
                </div>

                {{-- Timeline Content --}}
                <div class="txlog-timeline" id="txlog-content">
                    <div class="txlog-loading" id="txlog-loader">
                        <div class="txlog-spinner"></div>
                        <span>Loading transactions…</span>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="txlog-pagination" id="txlog-pagination" style="display:none;"></div>

            </div>{{-- /card --}}
        </div>
    </div>
</div>

@endsection

@push('footer-script')
<script>
/**
 * Transaction Log V2 — AJAX-driven professional timeline.
 * Defaults to current month date range on load.
 * Metronic-compatible · tenancy scoped server-side.
 */
(function () {
    'use strict';

    // Subdirectory-agnostic AJAX URL
    var dataUrl = window.location.href
        .split('?')[0]
        .replace(/\/+$/, '')
        .replace(/transection-log$/, 'transection-log/data');

    // ── Date helpers ──────────────────────────────────────────────────────────
    function pad(n) { return String(n).padStart(2, '0'); }
    function fmt(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }

    function getMonthRange(offset) {
        var now  = new Date();
        var year = now.getFullYear();
        var mon  = now.getMonth() + offset;      // 0-based, supports negative
        var first = new Date(year, mon, 1);
        var last  = new Date(year, mon + 1, 0);  // day 0 = last day of prev month
        return { from: fmt(first), to: fmt(last) };
    }

    function thisMonthRange() { return getMonthRange(0); }
    function lastMonthRange() { return getMonthRange(-1); }

    function last7Range() {
        var to   = new Date();
        var from = new Date();
        from.setDate(to.getDate() - 6);
        return { from: fmt(from), to: fmt(to) };
    }

    // ── State ─────────────────────────────────────────────────────────────────
    var range  = thisMonthRange();   // default = current month
    var state  = {
        pagetype        : 'operationaltab',
        search          : '',
        date_from       : range.from,
        date_to         : range.to,
        transection_type: '',
        per_page        : 10,
        page            : 1,
    };
    var debounceTimer = null;

    // ── DOM refs ──────────────────────────────────────────────────────────────
    var elContent    = document.getElementById('txlog-content');
    var elLoader     = document.getElementById('txlog-loader');
    var elPagination = document.getElementById('txlog-pagination');
    var elSearch     = document.getElementById('txlog-search');
    var elDateFrom   = document.getElementById('txlog-date-from');
    var elDateTo     = document.getElementById('txlog-date-to');
    var elType       = document.getElementById('txlog-type');
    var elPerPage    = document.getElementById('txlog-per-page');
    var elReset      = document.getElementById('txlog-reset');
    var elStatCredit = document.getElementById('stat-credit');
    var elStatDebit  = document.getElementById('stat-debit');
    var elStatTotal  = document.getElementById('stat-total');

    // Set default dates on inputs
    elDateFrom.value = state.date_from;
    elDateTo.value   = state.date_to;

    // ── Fetch ─────────────────────────────────────────────────────────────────
    function fetchData() {
        showLoader();
        var params = new URLSearchParams({
            pagetype         : state.pagetype,
            search           : state.search,
            date_from        : state.date_from,
            date_to          : state.date_to,
            transection_type : state.transection_type,
            per_page         : state.per_page,
            page             : state.page,
        });

        fetch(dataUrl + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(json) {
            if (json.success) {
                renderItems(json.data);
                renderPagination(json);
                updateStats(json.data);
            } else {
                showError('Unable to load transactions. Please try again.');
            }
        })
        .catch(function() {
            showError('A network error occurred. Please refresh the page.');
        });
    }

    // ── Stats mini-update ─────────────────────────────────────────────────────
    function updateStats(items) {
        var credit = 0, debit = 0;
        (items || []).forEach(function(row) {
            if (row.transection_type === 'credit') credit += parseFloat(row.display_coins) || 0;
            else debit += parseFloat(row.display_coins) || 0;
        });
        elStatCredit.textContent = credit.toFixed(0) + ' C';
        elStatDebit.textContent  = debit.toFixed(0)  + ' C';
        elStatTotal.textContent  = (items ? items.length : 0) + ' rows';
    }

    // ── Render rows ───────────────────────────────────────────────────────────
    function renderItems(items) {
        elLoader.style.display = 'none';
        if (!items || items.length === 0) {
            elContent.innerHTML = emptyState();
            elPagination.style.display = 'none';
            return;
        }

        var html = '';
        items.forEach(function(row) {
            var d         = new Date(row.created_at);
            var dateStr   = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            var timeStr   = d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });
            var isCredit  = row.transection_type === 'credit';
            var isSettle  = row.settlement_coins && parseFloat(row.settlement_coins) > 0;

            var iconClass = isSettle ? 'ri-settle' : (isCredit ? 'ri-credit' : 'ri-debit');
            var iconColor = isSettle ? '#f6c000'   : (isCredit ? '#50cd89'   : '#f1416c');
            var iconSvg   = isCredit
                ? '<svg viewBox="0 0 24 24" fill="none" stroke="'+iconColor+'" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg>'
                : '<svg viewBox="0 0 24 24" fill="none" stroke="'+iconColor+'" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>';

            var typeBadge = '<span class="tx-badge '+(isCredit?'credit':'debit')+'"><span class="dot"></span>'+(isCredit?'Credit':'Debit')+'</span>';
            var settleBdg = isSettle
                ? ' <span class="tx-badge settle"><span class="dot"></span>Settlement</span>'
                : '';

            var amtClass  = isCredit ? 'amt-credit' : 'amt-debit';
            var amtSign   = isCredit ? '+' : '−';
            var displayAmt = parseFloat(row.display_coins || 0);
            var displayBal = parseFloat(row.balance_coins || 0);

            html += '<div class="txlog-item txlog-grid">' +
                '<div class="txlog-ic-col">' +
                    '<div class="txlog-row-icon '+iconClass+'">' + iconSvg + '</div>' +
                '</div>' +
                '<div>' +
                    '<div class="cell-main">' + esc(dateStr) + '</div>' +
                    '<div class="cell-sub">' + esc(timeStr) + '</div>' +
                '</div>' +
                '<div class="tx-col-event">' +
                    '<div class="cell-main" style="font-size:12px;">' + esc(row.event || '—') + '</div>' +
                    '<div class="cell-sub" style="margin-top:3px;">' + typeBadge + settleBdg + '</div>' +
                '</div>' +
                '<div>' +
                    '<div class="cell-main">' + esc(row.actor_name) + '</div>' +
                    '<div class="cell-sub">' + esc(row.actor_company) + '</div>' +
                '</div>' +
                '<div class="tx-col-amount">' +
                    '<div class="'+amtClass+'">' + amtSign + ' ' + displayAmt.toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:2}) + '</div>' +
                    '<div class="cell-sub">Coins</div>' +
                '</div>' +
                '<div class="tx-col-bal">' +
                    '<div class="bal-num">' + displayBal.toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:2}) + '</div>' +
                    '<div class="cell-sub">Balance</div>' +
                '</div>' +
            '</div>';
        });
        elContent.innerHTML = html;
    }

    // ── Pagination ────────────────────────────────────────────────────────────
    function renderPagination(json) {
        if (json.last_page <= 1 && (!json.from)) {
            elPagination.style.display = 'none';
            return;
        }
        elPagination.style.display = 'flex';

        var from = json.from || 0, to = json.to || 0;
        var html = '<div class="pg-info">Showing <strong>' + from + '&ndash;' + to + '</strong> of <strong>' + json.total + '</strong> records</div><div style="display:flex;gap:3px;">';

        html += pgLink('&lsaquo;', json.current_page - 1, json.current_page <= 1);

        var start = Math.max(1, json.current_page - 2);
        var end   = Math.min(json.last_page, json.current_page + 2);
        if (start > 1) {
            html += pgLink('1', 1, false);
            if (start > 2) html += '<span class="page-link disabled">&hellip;</span>';
        }
        for (var i = start; i <= end; i++) {
            html += pgLink(i, i, false, i === json.current_page);
        }
        if (end < json.last_page) {
            if (end < json.last_page - 1) html += '<span class="page-link disabled">&hellip;</span>';
            html += pgLink(json.last_page, json.last_page, false);
        }

        html += pgLink('&rsaquo;', json.current_page + 1, json.current_page >= json.last_page);
        html += '</div>';

        elPagination.innerHTML = html;
        elPagination.querySelectorAll('.page-link[data-pg]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                if (el.classList.contains('disabled') || el.classList.contains('active')) return;
                state.page = parseInt(el.getAttribute('data-pg'));
                fetchData();
            });
        });
    }

    function pgLink(label, pg, disabled, active) {
        var cls = 'page-link' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
        return '<a href="#" class="' + cls + '" data-pg="' + pg + '">' + label + '</a>';
    }

    // ── Preset chips ──────────────────────────────────────────────────────────
    function applyPreset(preset) {
        var r;
        document.querySelectorAll('.txlog-chip').forEach(function(c) {
            c.classList.toggle('active-chip', c.getAttribute('data-preset') === preset);
        });
        if (preset === 'this_month')  r = thisMonthRange();
        else if (preset === 'last_month') r = lastMonthRange();
        else if (preset === 'last_7')  r = last7Range();
        else { r = { from: '', to: '' }; }   // all time

        elDateFrom.value = r.from;
        elDateTo.value   = r.to;
        state.date_from  = r.from;
        state.date_to    = r.to;
        state.page = 1;
        fetchData();
    }

    document.querySelectorAll('.txlog-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            applyPreset(chip.getAttribute('data-preset'));
        });
    });

    // ── Helpers ───────────────────────────────────────────────────────────────
    function showLoader() {
        elLoader.style.display = 'flex';
        elContent.innerHTML = '';
        elContent.appendChild(elLoader);
        elPagination.style.display = 'none';
    }
    function showError(msg) {
        elLoader.style.display = 'none';
        elContent.innerHTML = '<div class="txlog-empty"><div class="empty-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div><p>' + esc(msg) + '</p></div>';
    }
    function emptyState() {
        return '<div class="txlog-empty"><div class="empty-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><p>No transactions found</p><div class="empty-sub">Try adjusting your filters or date range</div></div>';
    }
    function esc(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function resetPage() { state.page = 1; }

    // ── Events ────────────────────────────────────────────────────────────────
    document.querySelectorAll('.txlog-tab').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.txlog-tab').forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');
            state.pagetype = tab.getAttribute('data-pagetype');
            resetPage();
            fetchData();
        });
    });

    elSearch.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            state.search = elSearch.value.trim();
            // Clear preset highlight on manual date change
            document.querySelectorAll('.txlog-chip').forEach(function(c) { c.classList.remove('active-chip'); });
            resetPage(); fetchData();
        }, 450);
    });

    elDateFrom.addEventListener('change', function() {
        state.date_from = elDateFrom.value;
        document.querySelectorAll('.txlog-chip').forEach(function(c) { c.classList.remove('active-chip'); });
        resetPage(); fetchData();
    });

    elDateTo.addEventListener('change', function() {
        state.date_to = elDateTo.value;
        document.querySelectorAll('.txlog-chip').forEach(function(c) { c.classList.remove('active-chip'); });
        resetPage(); fetchData();
    });

    elType.addEventListener('change', function() {
        state.transection_type = elType.value;
        resetPage(); fetchData();
    });

    elPerPage.addEventListener('change', function() {
        state.per_page = parseInt(elPerPage.value);
        resetPage(); fetchData();
    });

    elReset.addEventListener('click', function() {
        var r        = thisMonthRange();
        elSearch.value   = '';
        elDateFrom.value = r.from;
        elDateTo.value   = r.to;
        elType.value     = '';
        if (elPerPage) elPerPage.value  = '10';
        state.search     = '';
        state.date_from  = r.from;
        state.date_to    = r.to;
        state.transection_type = '';
        state.per_page   = 10;
        document.querySelectorAll('.txlog-chip').forEach(function(c) {
            c.classList.toggle('active-chip', c.getAttribute('data-preset') === 'this_month');
        });
        resetPage(); fetchData();
    });

    // ── Backward-compat: ?pagetype= in URL ───────────────────────────────────
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('pagetype')) {
        var pt = urlParams.get('pagetype');
        if (pt === 'salestab' || pt === 'operationaltab') {
            state.pagetype = pt;
            document.querySelectorAll('.txlog-tab').forEach(function(t) {
                t.classList.toggle('active', t.getAttribute('data-pagetype') === pt);
            });
        }
    }

    // ── Initial load with current month default ───────────────────────────────
    fetchData();

})();
</script>
@endpush
