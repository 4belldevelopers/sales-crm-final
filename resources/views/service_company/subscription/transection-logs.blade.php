@extends('layouts.service_company_layout')

@push('head-script')
<style>
    /* ============================================================
     * Transaction Log V2 — Coins Activity Page Styles
     * Metronic theme compatible, Poppins font, max 14px, clean icon design
     * ============================================================ */

    /* Filter Bar */
    .txlog-filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
        padding: 14px 16px;
        background: #f9f9f9;
        border-bottom: 1px solid #ebebeb;
    }
    .txlog-filter-bar .form-control,
    .txlog-filter-bar .form-select {
        font-size: 13px;
        max-width: 200px;
    }
    .txlog-filter-bar .btn {
        font-size: 13px;
    }

    /* Timeline items */
    .txlog-timeline {
        padding: 0 16px;
        min-height: 200px;
    }
    .txlog-item {
        display: flex;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px dashed #e8e8e8;
        align-items: flex-start;
        animation: txfadeIn 0.25s ease;
    }
    .txlog-item:last-child {
        border-bottom: none;
    }
    @keyframes txfadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .txlog-icon {
        width: 40px; height: 40px;
        background: #f1f3f4;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .txlog-icon svg { width: 18px; height: 18px; opacity: 0.6; }

    /* Columns */
    .txlog-col-date  { min-width: 110px; flex-shrink: 0; }
    .txlog-col-event { flex: 1; min-width: 0; }
    .txlog-col-actor { min-width: 160px; flex-shrink: 0; }
    .txlog-col-coins { min-width: 120px; flex-shrink: 0; text-align: right; }
    .txlog-col-bal   { min-width: 100px; flex-shrink: 0; text-align: right; }

    .txlog-col-date .date-main   { font-size: 13px; font-weight: 600; color: #181c32; }
    .txlog-col-date .date-time   { font-size: 11px; color: #a1a5b7; }
    .txlog-col-event .ev-desc    { font-size: 13px; font-weight: 600; color: #181c32; word-break: break-word; }
    .txlog-col-event .ev-type    { font-size: 11px; color: #a1a5b7; text-transform: capitalize; }
    .txlog-col-actor .act-name   { font-size: 13px; font-weight: 600; color: #181c32; }
    .txlog-col-actor .act-cmp    { font-size: 11px; color: #a1a5b7; }
    .txlog-col-coins .coins-val  { font-size: 13px; font-weight: 700; }
    .txlog-col-coins .coins-lbl  { font-size: 11px; color: #a1a5b7; }
    .txlog-col-bal .bal-val      { font-size: 13px; font-weight: 600; color: #009ef7; }
    .txlog-col-bal .bal-lbl      { font-size: 11px; color: #a1a5b7; }

    .badge-credit { background: #e8fff3; color: #50cd89; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; }
    .badge-debit  { background: #fff5f8; color: #f1416c; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; }
    .badge-settle { background: #fff8dd; color: #ffc700; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; }

    /* Pagination */
    .txlog-pagination {
        display: flex; gap: 5px; justify-content: flex-end;
        align-items: center; padding: 14px 16px;
        flex-wrap: wrap;
    }
    .txlog-pagination .pg-info {
        flex: 1; font-size: 12px; color: #7e8299; font-weight: 500; min-width: 200px;
    }
    .txlog-pagination .page-link {
        padding: 0.3rem 0.65rem; font-size: 13px;
        color: #3f4254; border: none; border-radius: 0.375rem;
        cursor: pointer; font-weight: 500; text-decoration: none;
        background: transparent; transition: background 0.18s;
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px;
    }
    .txlog-pagination .page-link:hover { background: #eff2f5; }
    .txlog-pagination .page-link.active { background: #009ef7; color: #fff; }
    .txlog-pagination .page-link.disabled { opacity: 0.4; pointer-events: none; }

    /* Loading overlay */
    .txlog-loading {
        display: flex; align-items: center; justify-content: center;
        padding: 40px 0; gap: 10px; color: #7e8299; font-size: 13px;
    }
    .txlog-spinner {
        width: 20px; height: 20px;
        border: 2px solid #e4e6ef;
        border-top-color: #009ef7;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Empty state */
    .txlog-empty {
        text-align: center; padding: 50px 16px; color: #a1a5b7; font-size: 13px;
    }
    .txlog-empty svg { width: 40px; height: 40px; opacity: 0.3; margin-bottom: 10px; }
</style>
@endpush

@section('content')

{{-- ============================================================
     Toolbar
     ============================================================ --}}
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
        <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <div class="d-flex flex-column">
                <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                    <span class="text-gray-800 mb-1">Coins Activity</span>
                </h5>
                <span class="text-gray-600 fs-7">Your coin transaction history</span>
            </div>
        </div>
        <div class="d-flex flex-column">
            <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                <a href="{{ url('service_company/subscribe') }}" class="text-gray-800 mb-1 fs-7">
                    View Coin Usage Table &rarr;
                </a>
            </h5>
        </div>
    </div>
</div>

{{-- ============================================================
     Content
     ============================================================ --}}
<div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid px-3">
            <div class="card">
                <div class="card-body pt-3 px-0 pb-4">
                    <div class="card shadow-none">
                        <div class="card-body">
                            <div class="card card-custom mx-2">

                                {{-- Tab Navigation --}}
                                <div class="card-header card-header-tabs-line">
                                    <div class="card-toolbar">
                                        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
                                            <li class="nav-item me-3">
                                                <a class="nav-link txlog-tab active" href="#" data-pagetype="operationaltab" id="tab-operational">
                                                    <span class="nav-text font-weight-bold">Operational Coins Activity</span>
                                                </a>
                                            </li>
                                            <li class="nav-item me-3">
                                                <a class="nav-link txlog-tab" href="#" data-pagetype="salestab" id="tab-sales">
                                                    <span class="nav-text font-weight-bold">Sales Coins Activity</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Filter Bar --}}
                                <div class="txlog-filter-bar">
                                    <div>
                                        <label class="form-label fs-7 mb-1">Search</label>
                                        <input type="text" id="txlog-search" class="form-control form-control-sm"
                                               placeholder="Event description or user name..." style="width:240px;">
                                    </div>
                                    <div>
                                        <label class="form-label fs-7 mb-1">From Date</label>
                                        <input type="date" id="txlog-date-from" class="form-control form-control-sm">
                                    </div>
                                    <div>
                                        <label class="form-label fs-7 mb-1">To Date</label>
                                        <input type="date" id="txlog-date-to" class="form-control form-control-sm">
                                    </div>
                                    <div>
                                        <label class="form-label fs-7 mb-1">Type</label>
                                        <select id="txlog-type" class="form-select form-select-sm" style="width:130px;">
                                            <option value="">All Types</option>
                                            <option value="credit">Credit</option>
                                            <option value="debit">Debit</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label fs-7 mb-1">Per Page</label>
                                        <select id="txlog-per-page" class="form-select form-select-sm" style="width:90px;">
                                            <option value="10">10</option>
                                            <option value="20" selected>20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    <div style="margin-top:auto;">
                                        <button id="txlog-reset" class="btn btn-sm btn-light-secondary">Reset</button>
                                    </div>
                                </div>

                                {{-- Timeline Content Area --}}
                                <div class="card-body py-0">
                                    <div id="txlog-content" class="txlog-timeline">
                                        <div class="txlog-loading" id="txlog-loader">
                                            <div class="txlog-spinner"></div>
                                            <span>Loading transactions&hellip;</span>
                                        </div>
                                    </div>

                                    {{-- Pagination Row --}}
                                    <div class="txlog-pagination" id="txlog-pagination" style="display:none;"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('footer-script')
<script>
/**
 * Transaction Log V2 — AJAX-driven timeline with filters, search, pagination.
 * No page reload on tab switch or pagination. Metronic-compatible.
 * Tenancy enforced server-side; all data arrives from /service_company/transection-log/data.
 */
(function () {
    'use strict';

    // Build the AJAX URL from window.location to be subdirectory-agnostic.
    // Replaces the trailing page segment with the data endpoint.
    var dataUrl = window.location.href.split('?')[0].replace(/\/+$/, '').replace(/transection-log$/, 'transection-log/data');

    // ─── State ────────────────────────────────────────────────────────────────
    var state = {
        pagetype    : 'operationaltab',
        search      : '',
        date_from   : '',
        date_to     : '',
        transection_type: '',
        per_page    : 20,
        page        : 1,
    };
    var debounceTimer = null;

    // ─── DOM refs ─────────────────────────────────────────────────────────────
    var elContent   = document.getElementById('txlog-content');
    var elLoader    = document.getElementById('txlog-loader');
    var elPagination= document.getElementById('txlog-pagination');
    var elSearch    = document.getElementById('txlog-search');
    var elDateFrom  = document.getElementById('txlog-date-from');
    var elDateTo    = document.getElementById('txlog-date-to');
    var elType      = document.getElementById('txlog-type');
    var elPerPage   = document.getElementById('txlog-per-page');
    var elReset     = document.getElementById('txlog-reset');

    // ─── Fetch ────────────────────────────────────────────────────────────────
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
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(json) {
            if (json.success) {
                renderItems(json.data);
                renderPagination(json);
            } else {
                showError('Unable to load transactions. Please try again.');
            }
        })
        .catch(function() {
            showError('A network error occurred. Please refresh the page.');
        });
    }

    // ─── Render timeline items ─────────────────────────────────────────────────
    function renderItems(items) {
        elLoader.style.display = 'none';

        if (!items || items.length === 0) {
            elContent.innerHTML = emptyState();
            elPagination.style.display = 'none';
            return;
        }

        var html = '';
        items.forEach(function(row) {
            var dateObj    = new Date(row.created_at);
            var dateStr    = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            var timeStr    = dateObj.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });
            var isCredit   = row.transection_type === 'credit';
            var isSettle   = row.settlement_coins && parseFloat(row.settlement_coins) > 0;
            var typeBadge  = isCredit
                ? '<span class="badge-credit">Credit</span>'
                : '<span class="badge-debit">Debit</span>';
            var settleNote = isSettle
                ? '<span class="badge-settle ms-1">Settlement ' + parseFloat(row.settlement_coins) + ' Coins</span>'
                : '';

            html += '<div class="txlog-item">' +
                '<div class="txlog-icon">' + iconSvg() + '</div>' +
                '<div class="txlog-col-date">' +
                    '<div class="date-main">' + esc(dateStr) + '</div>' +
                    '<div class="date-time">' + esc(timeStr) + '</div>' +
                '</div>' +
                '<div class="txlog-col-event">' +
                    '<div class="ev-desc">' + esc(row.event || '—') + '</div>' +
                    '<div class="ev-type">' + typeBadge + settleNote + '</div>' +
                '</div>' +
                '<div class="txlog-col-actor">' +
                    '<div class="act-name">' + esc(row.actor_name) + '</div>' +
                    '<div class="act-cmp">' + esc(row.actor_company) + '</div>' +
                '</div>' +
                '<div class="txlog-col-coins">' +
                    '<div class="coins-val">' + parseFloat(row.display_coins) + ' <span style="font-size:11px;font-weight:400;">Coins</span></div>' +
                    '<div class="coins-lbl">' + capitalize(row.transection_type) + '</div>' +
                '</div>' +
                '<div class="txlog-col-bal">' +
                    '<div class="bal-val">' + parseFloat(row.balance_coins) + '</div>' +
                    '<div class="bal-lbl">Balance</div>' +
                '</div>' +
            '</div>';
        });

        elContent.innerHTML = html;
    }

    // ─── Render pagination ────────────────────────────────────────────────────
    function renderPagination(json) {
        if (json.last_page <= 1) {
            elPagination.style.display = 'none';
            return;
        }
        elPagination.style.display = 'flex';

        var fromNum = json.from || 0;
        var toNum   = json.to   || 0;
        var html    = '<div class="pg-info">Showing ' + fromNum + ' – ' + toNum + ' of ' + json.total + ' records</div><div style="display:flex;gap:4px;">';

        // Previous
        html += '<a href="#" class="page-link ' + (json.current_page <= 1 ? 'disabled' : '') + '" data-pg="' + (json.current_page - 1) + '">&lsaquo; Prev</a>';

        // Page numbers: window of 5
        var start = Math.max(1, json.current_page - 2);
        var end   = Math.min(json.last_page, json.current_page + 2);
        if (start > 1) {
            html += '<a href="#" class="page-link" data-pg="1">1</a>';
            if (start > 2) html += '<span class="page-link disabled">&hellip;</span>';
        }
        for (var i = start; i <= end; i++) {
            html += '<a href="#" class="page-link ' + (i === json.current_page ? 'active' : '') + '" data-pg="' + i + '">' + i + '</a>';
        }
        if (end < json.last_page) {
            if (end < json.last_page - 1) html += '<span class="page-link disabled">&hellip;</span>';
            html += '<a href="#" class="page-link" data-pg="' + json.last_page + '">' + json.last_page + '</a>';
        }

        // Next
        html += '<a href="#" class="page-link ' + (json.current_page >= json.last_page ? 'disabled' : '') + '" data-pg="' + (json.current_page + 1) + '">Next &rsaquo;</a>';
        html += '</div>';

        elPagination.innerHTML = html;

        // Bind pagination clicks
        elPagination.querySelectorAll('.page-link[data-pg]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                if (el.classList.contains('disabled') || el.classList.contains('active')) return;
                state.page = parseInt(el.getAttribute('data-pg'));
                fetchData();
            });
        });
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    function showLoader() {
        elLoader.style.display = 'flex';
        elContent.innerHTML = '';
        elContent.appendChild(elLoader);
        elPagination.style.display = 'none';
    }

    function showError(msg) {
        elLoader.style.display = 'none';
        elContent.innerHTML = '<div class="txlog-empty"><p>' + esc(msg) + '</p></div>';
        elPagination.style.display = 'none';
    }

    function emptyState() {
        return '<div class="txlog-empty">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>' +
            '<p>No transaction logs found for the selected filters.</p></div>';
    }

    function esc(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function iconSvg() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
    }

    function resetPage() { state.page = 1; }

    // ─── Event: Tab switch ────────────────────────────────────────────────────
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

    // ─── Event: Filters ───────────────────────────────────────────────────────
    elSearch.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            state.search = elSearch.value.trim();
            resetPage();
            fetchData();
        }, 450);
    });

    elDateFrom.addEventListener('change', function() {
        state.date_from = elDateFrom.value;
        resetPage();
        fetchData();
    });

    elDateTo.addEventListener('change', function() {
        state.date_to = elDateTo.value;
        resetPage();
        fetchData();
    });

    elType.addEventListener('change', function() {
        state.transection_type = elType.value;
        resetPage();
        fetchData();
    });

    elPerPage.addEventListener('change', function() {
        state.per_page = parseInt(elPerPage.value);
        resetPage();
        fetchData();
    });

    elReset.addEventListener('click', function() {
        elSearch.value   = '';
        elDateFrom.value = '';
        elDateTo.value   = '';
        elType.value     = '';
        elPerPage.value  = '20';
        state.search     = '';
        state.date_from  = '';
        state.date_to    = '';
        state.transection_type = '';
        state.per_page   = 20;
        resetPage();
        fetchData();
    });

    // ─── Check if URL has a pagetype param (backward compatibility) ───────────
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('pagetype')) {
        var pt = urlParams.get('pagetype');
        if (pt === 'salestab' || pt === 'operationaltab') {
            state.pagetype = pt;
            // Activate correct tab visually
            document.querySelectorAll('.txlog-tab').forEach(function(t) {
                t.classList.toggle('active', t.getAttribute('data-pagetype') === pt);
            });
        }
    }

    // ─── Initial load ─────────────────────────────────────────────────────────
    fetchData();

})();
</script>
@endpush
