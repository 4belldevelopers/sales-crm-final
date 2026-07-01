@extends('layouts.service_company_layout')
@push('head-script')
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>
        @media only screen and (min-width: 900px) and (max-width: 2560px) {
            .table td, .table th, .table tr { width: max-content !important; }
            .table> :not(caption)>*>* { padding: 0rem; }
            td:nth-child(n+2), th:nth-child(n+2) { width: revert !important; }
        }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) { #kt_activitiesJob { width: 1025px; } }
        @media only screen and (min-width: 1440px) and (max-width: 2560px) { #kt_activitiesJob { width: 1200px; } }
        #load_jobs tbody tr { height: 82px; position: relative; }
    </style>

    <style>
        #kt_content_container,
        #kt_toolbar_container {
            font-family: 'Poppins', sans-serif;
            color: #1e2129;
        }

        /* ---------- Toolbar (Metronic toolbar-fixed handles position:fixed via body class) ---------- */
        #kt_toolbar {
            background: #fff !important;
            border-bottom: 1px solid #eef0f4;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .team-page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e2129;
            margin: 0;
            line-height: 1.2;
        }
        .team-page-subtitle {
            font-size: 13px;
            color: #7c8398;
            margin-top: 2px;
        }
        .team-header-actions { gap: 10px; }
        .team-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: 9px;
            border: 1px solid #eef0f4;
            background: #fff;
            color: #1e2129;
            transition: all .15s ease;
            white-space: nowrap;
        }
        .team-btn i { font-size: 14px; }
        .team-btn-light:hover { background: #f1f3f7; color: #1e2129; }
        .team-btn-primary {
            background: #6366f1;
            border-color: #6366f1;
            color: #fff;
        }
        .team-btn-primary:hover { background: #4f46e5; color: #fff; }

        /* ---------- Stat cards ---------- */
        .team-stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 18px;
        }
        .team-stat-card {
            background: #fff;
            border: 1px solid #eef0f4;
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .team-stat-ico {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .ico-blue   { background: #eaf1ff; color: #3b82f6; }
        .ico-green  { background: #e7f8f0; color: #12b76a; }
        .ico-amber  { background: #fff4e5; color: #f79009; }
        .ico-purple { background: #f1ecff; color: #7c5cfc; }
        .team-stat-label { font-size: 12px; color: #7c8398; margin-bottom: 2px; }
        .team-stat-value { font-size: 24px; font-weight: 700; color: #1e2129; line-height: 1; }
        .team-stat-sub { font-size: 11px; color: #7c8398; margin-top: 5px; }

        /* ---------- Main card ---------- */
        .team-main-card {
            background: #fff;
            border: 1px solid #eef0f4;
            border-radius: 14px;
            overflow: hidden;
        }

        /* ---------- Tabs ---------- */
        .team-tabs {
            display: flex;
            gap: 26px;
            padding: 0 22px;
            border-bottom: 1px solid #eef0f4;
        }
        .team-tab {
            font-size: 14px;
            font-weight: 600;
            color: #7c8398;
            padding: 16px 2px;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            white-space: nowrap;
        }
        .team-tab:hover { color: #1e2129; }
        .team-tab.active { color: #6366f1; border-bottom-color: #6366f1; }

        /* ---------- Filter bar ---------- */
        .team-filter-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 0 12px;
            flex-wrap: wrap;
        }
        /* Search with icon using input-group so FA CSS icon is inside naturally */
        .team-search-wrap {
            flex: 1;
            min-width: 240px;
            position: relative;
            display: flex;
            align-items: center;
        }
        .team-search-wrap .team-search-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #a3aabf;
            font-size: 13px;
            pointer-events: none;
            line-height: 1;
        }
        .team-search-wrap input {
            width: 100%;
            height: 40px;
            border: 1.5px solid #e4e7f0;
            border-radius: 10px;
            padding: 0 14px 0 38px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            background: #fff;
            color: #1e2129;
            outline: none;
            transition: border-color .15s;
        }
        .team-search-wrap input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
        .team-search-wrap input::placeholder { color: #b0b7c9; }

        /* ---------- Sort By card ---------- */
        .team-sort-card {
            display: inline-flex;
            align-items: center;
            gap: 0;
            background: #fff;
            border: 1.5px solid #e4e7f0;
            border-radius: 10px;
            padding: 0 14px;
            height: 40px;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
            white-space: nowrap;
        }
        .team-sort-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #7c8398;
            letter-spacing: .2px;
            padding-right: 10px;
            border-right: 1.5px solid #e4e7f0;
            height: 22px;
        }
        .team-sort-label i { font-size: 12px; color: #6366f1; }
        .team-select {
            border: none;
            background: transparent;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #1e2129;
            padding: 0 28px 0 10px;
            height: 100%;
            -webkit-appearance: none;
            appearance: none;
            cursor: pointer;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2.5'><path d='M6 9l6 6 6-6'/></svg>");
            background-repeat: no-repeat;
            background-position: right 0 center;
            outline: none;
        }

        /* ---------- Table ---------- */
        .team-table { width: 100%; margin: 0; }
        .team-table thead th {
            font-size: 12px;
            font-weight: 600;
            text-transform: none;
            color: #7c8398;
            border-bottom: 1px solid #eef0f4;
            padding: 12px 10px;
            background: #fbfbfd;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .team-table tbody td {
            padding: 14px 10px;
            border-bottom: 1px solid #eef0f4;
            vertical-align: middle;
            font-size: 13px;
        }
        .team-table tbody tr:hover { background: #fafbff; }
        .team-avatar {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 14px;
            overflow: hidden; flex-shrink: 0;
        }
        .team-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .team-member-name { font-size: 13px; font-weight: 600; color: #1e2129; }
        .team-member-role { font-size: 12px; color: #7c8398; }
        .team-member-mail { font-size: 12px; color: #7c8398; }

        .team-rank-badge { font-size: 14px; font-weight: 700; color: #1e2129; }
        .team-rank-month { font-size: 11px; color: #7c8398; }
        .team-score-link {
            display: inline-block;
            margin-top: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #12b76a;
            background: #e7f8f0;
            border-radius: 6px;
            padding: 2px 8px;
            cursor: pointer;
        }

        .team-workload { display: flex; gap: 22px; }
        .team-wl-item { display: flex; flex-direction: column; gap: 2px; }
        .team-wl-num { font-size: 13px; font-weight: 700; color: #1e2129; display: flex; align-items: center; gap: 5px; }
        .team-wl-num i { font-size: 12px; }
        .team-wl-label { font-size: 11px; color: #7c8398; }
        .wl-blue i { color: #3b82f6; }
        .wl-purple i { color: #7c5cfc; }
        .wl-green i { color: #12b76a; }

        .team-role-pill {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            border-radius: 7px;
            padding: 4px 11px;
        }
        .role-admin { background: #f1ecff; color: #7c5cfc; }
        .role-lead  { background: #eaf1ff; color: #3b82f6; }
        .role-default { background: #f1f3f7; color: #7c8398; }
        .team-role-edit-btn {
            width: 26px; height: 26px;
            border-radius: 6px;
            border: 1px solid #eef0f4;
            background: #fff;
            color: #7c8398;
            display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all .15s ease;
        }
        .team-role-edit-btn i { font-size: 11px; }
        .team-role-edit-btn:hover { background: #eef2ff; color: #6366f1; border-color: #6366f1; }

        .team-status-cell { display: flex; align-items: center; gap: 9px; }
        .team-switch { position: relative; display: inline-block; width: 36px; height: 20px; }
        .team-switch input { opacity: 0; width: 0; height: 0; }
        .team-switch .slider {
            position: absolute; cursor: pointer; inset: 0;
            background: #d5d8e0; border-radius: 20px; transition: .2s;
        }
        .team-switch .slider:before {
            content: ""; position: absolute; height: 14px; width: 14px;
            left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .2s;
        }
        .team-switch input:checked + .slider { background: #6366f1; }
        .team-switch input:checked + .slider:before { transform: translateX(16px); }
        .team-switch input:disabled + .slider { opacity: .6; cursor: not-allowed; }
        .team-status-text { font-size: 12px; font-weight: 600; }
        .team-status-text.is-active { color: #12b76a; }
        .team-status-text.is-inactive { color: #f04438; }

        .team-lastactive { display: flex; align-items: center; gap: 7px; font-size: 12px; color: #7c8398; }
        .team-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .dot-green { background: #12b76a; }
        .dot-amber { background: #f79009; }
        .dot-gray  { background: #c2c7d2; }

        .team-action-btn {
            width: 30px; height: 30px;
            border-radius: 8px;
            border: none; background: transparent;
            color: #7c8398;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .team-action-btn:hover { background: #f1f3f7; color: #1e2129; }
        .team-action-wrap { position: relative; display: inline-block; }
        .team-action-menu {
            position: absolute;
            right: 0;
            top: 34px;
            min-width: 160px;
            background: #fff;
            border: 1px solid #eef0f4;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(20, 25, 40, .12);
            padding: 6px;
            z-index: 50;
            display: none;
        }
        .team-action-menu.show { display: block; }
        .team-action-item {
            display: block;
            font-size: 12px;
            color: #1e2129;
            padding: 8px 12px;
            border-radius: 7px;
            cursor: pointer;
        }
        .team-action-item:hover { background: #eef2ff; color: #6366f1; }

        /* ---------- Footer / pagination ---------- */
        .team-pagination-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 4px 4px;
            border-top: 1px solid #eef0f4;
            margin-top: 4px;
            gap: 14px;
            flex-wrap: wrap;
        }
        .team-pagination-info { font-size: 12px; color: #7c8398; }
        .team-pagination-pages { display: flex; align-items: center; gap: 6px; }
        .team-page-link {
            min-width: 32px; height: 32px;
            border: 1px solid #eef0f4;
            background: #fff;
            border-radius: 8px;
            font-size: 12px; font-weight: 600;
            color: #1e2129;
            display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer; padding: 0 8px;
        }
        .team-page-link.is-active { background: #6366f1; border-color: #6366f1; color: #fff; }
        .team-page-link.is-disabled { opacity: .45; pointer-events: none; }
        .team-page-dots { color: #7c8398; padding: 0 2px; }
        .team-pagination-rows { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #7c8398; }
        .team-pagination-rows select {
            height: 32px; border: 1px solid #eef0f4;
            border-radius: 8px; font-size: 12px; padding: 0 8px; background: #fff;
        }

        #team_rank_user .modal-dialog { max-width: 1180px; }

        /* ========== Fixed Toolbar ========== */
        #kt_toolbar {
            position: fixed !important;
            top: 65px !important;        /* Metronic header height */
            left: 265px !important;      /* Metronic aside width (expanded) */
            right: 0 !important;
            z-index: 98 !important;
            background: #fff !important;
            border-bottom: 1px solid #eef0f4 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,.06) !important;
        }
        /* Push content down so it doesn't hide behind the fixed toolbar */
        #kt_content {
            padding-top: calc(var(--kt-toolbar-height, 55px) + 10px) !important;
        }
        /* When aside is minimized (collapsed to icon-only ~75px) */
        .aside-minimize:not(.aside-minimize-hover) #kt_toolbar {
            left: 75px !important;
        }

        /* ========== Invite Drawer ========== */
        .invite-overlay {
            position: fixed; inset: 0;
            background: rgba(15,20,40,.45);
            backdrop-filter: blur(2px);
            z-index: 1040;
            opacity: 0; pointer-events: none;
            transition: opacity .28s ease;
        }
        .invite-overlay.open { opacity: 1; pointer-events: all; }

        .invite-drawer {
            position: fixed;
            top: 0; right: 0; bottom: 0;
            width: 520px;
            max-width: 100vw;
            background: #fff;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            box-shadow: -8px 0 40px rgba(15,20,40,.13);
        }
        .invite-drawer.open { transform: translateX(0); }

        /* Header */
        .invite-drawer-head {
            padding: 28px 32px 20px;
            border-bottom: 1px solid #f0f1f6;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .invite-drawer-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
            margin: 0 0 4px;
        }
        .invite-drawer-subtitle {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }
        .invite-drawer-close {
            width: 34px; height: 34px;
            border-radius: 8px;
            border: none;
            background: #f3f4f6;
            color: #374151;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: background .15s;
            flex-shrink: 0;
            margin-left: 16px;
            margin-top: 2px;
        }
        .invite-drawer-close:hover { background: #e5e7eb; color: #111; }

        /* Body */
        .invite-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 28px 32px;
        }

        /* Field */
        .invite-field { margin-bottom: 22px; }
        .invite-field-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .invite-field-label .req { color: #ef4444; margin-left: 1px; }
        .invite-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .invite-input-icon {
            position: absolute;
            left: 14px;
            color: #9ca3af;
            font-size: 15px;
            pointer-events: none;
            line-height: 1;
        }
        .invite-input-wrap input,
        .invite-input-wrap select {
            width: 100%;
            height: 48px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0 16px 0 44px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            -webkit-appearance: none;
            appearance: none;
        }
        .invite-input-wrap input::placeholder,
        .invite-input-wrap select.placeholder-active { color: #9ca3af; }
        .invite-input-wrap input:focus,
        .invite-input-wrap select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }
        /* chevron for select */
        .invite-select-wrap { position: relative; }
        .invite-select-wrap::after {
            content: '';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 14px; height: 14px;
            background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'><path d='M6 9l6 6 6-6'/></svg>") center/contain no-repeat;
        }
        .invite-field-hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 7px;
        }

        /* Footer */
        .invite-drawer-foot {
            padding: 20px 32px 28px;
            border-top: 1px solid #f0f1f6;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-shrink: 0;
        }
        .invite-btn-cancel {
            height: 44px;
            padding: 0 24px;
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #374151;
            cursor: pointer;
            transition: background .15s;
        }
        .invite-btn-cancel:hover { background: #f3f4f6; }
        .invite-btn-send {
            height: 44px;
            padding: 0 24px;
            border-radius: 10px;
            border: none;
            background: #6366f1;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            transition: background .15s;
        }
        .invite-btn-send:hover { background: #4f46e5; }
        .invite-btn-send:disabled { opacity: .65; cursor: not-allowed; }
        .invite-spinner { display: none; }
        .invite-btn-send.loading .invite-label { display: none; }
        .invite-btn-send.loading .invite-spinner { display: inline-block; }

        @media (max-width: 1200px) {
            .team-stats-row { grid-template-columns: repeat(2, 1fr); }
            .team-workload { gap: 14px; }
        }
        @media (max-width: 768px) {
            .team-stats-row { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
            <div class="page-title d-flex flex-column">
                <h1 class="team-page-title">Team</h1>
                <span class="team-page-subtitle">Manage your team members, workloads and performance.</span>
            </div>
            <div class="d-flex align-items-center team-header-actions">
                <input type="hidden" id="inputSearch" data-kt-user-table-filter="search" placeholder="Search..." />
                <a href="javascript:void(0);" class="team-btn team-btn-light" id="openInviteDrawer">
                    <i class="fa-regular fa-envelope"></i> <span>Invite Member</span>
                </a>
                <a href="javascript:void(0);" class="team-btn team-btn-light" onclick="exportTeam()">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i> <span>Export</span>
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                @php
                    // Fallback: derive the stat-card / tab counts here if the controller did not
                    // supply them (e.g. while a long-running worker still holds an older index()).
                    // Same logic as ServiceCompanyTeamController@index. Tenancy: User::company()
                    // is company-scoped; raw DB queries are filtered by company_id explicitly.
                    if (!isset($teamCounts)) {
                        $teamCid = auth()->user()->company_id;
                        $teamStatus = \App\Models\User::query()->company()
                            ->selectRaw('COUNT(*) as total')
                            ->selectRaw("SUM(status = 'active') as active")
                            ->selectRaw("SUM(status = 'inactive') as inactive")
                            ->selectRaw("SUM(password = '') as invited")
                            ->first();
                        $teamCaptain = \Illuminate\Support\Facades\DB::table('job_assign')
                            ->where('company_id', $teamCid)->where('spok_user_id', '>', 0)
                            ->distinct()->count('spok_user_id');
                        $teamTrm = \Illuminate\Support\Facades\DB::table('job_applications')
                            ->where('company_id', $teamCid)->where('submitted_by', '>', 0)
                            ->distinct()->count('submitted_by');
                        $teamCounts = [
                            'total'     => (int) ($teamStatus->total ?? 0),
                            'active'    => (int) ($teamStatus->active ?? 0),
                            'inactive'  => (int) ($teamStatus->inactive ?? 0),
                            'invited'   => (int) ($teamStatus->invited ?? 0),
                            'suspended' => 0,
                            'captain'   => (int) $teamCaptain,
                            'trm'       => (int) $teamTrm,
                        ];
                    }
                @endphp

                {{-- Stat cards --}}
                <div class="team-stats-row">
                    <div class="team-stat-card">
                        <div class="team-stat-ico ico-blue"><i class="fa-solid fa-users"></i></div>
                        <div>
                            <div class="team-stat-label">Total Members</div>
                            <div class="team-stat-value">{{ $teamCounts['total'] }}</div>
                        </div>
                    </div>
                    <div class="team-stat-card">
                        <div class="team-stat-ico ico-green"><i class="fa-solid fa-user-check"></i></div>
                        <div>
                            <div class="team-stat-label">Active Members</div>
                            <div class="team-stat-value">{{ $teamCounts['active'] }}</div>
                        </div>
                    </div>
                    <div class="team-stat-card">
                        <div class="team-stat-ico ico-amber"><i class="fa-regular fa-user"></i></div>
                        <div>
                            <div class="team-stat-label">Inactive Members</div>
                            <div class="team-stat-value">{{ $teamCounts['inactive'] }}</div>
                        </div>
                    </div>
                    <div class="team-stat-card">
                        <div class="team-stat-ico ico-purple"><i class="fa-solid fa-crown"></i></div>
                        <div>
                            <div class="team-stat-label">Captain / TRM</div>
                            <div class="team-stat-value">{{ $teamCounts['captain'] + $teamCounts['trm'] }}</div>
                            <div class="team-stat-sub">Captain {{ $teamCounts['captain'] }} &nbsp;•&nbsp; TRM
                                {{ $teamCounts['trm'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="card team-main-card">
                    {{-- Status tabs --}}
                    <div class="team-tabs">
                        <a class="team-tab active" data-status="all">All Members ({{ $teamCounts['total'] }})</a>
                        <a class="team-tab" data-status="active">Active ({{ $teamCounts['active'] }})</a>
                        <a class="team-tab" data-status="inactive">Inactive ({{ $teamCounts['inactive'] }})</a>
                        <a class="team-tab" data-status="invited">Invited ({{ $teamCounts['invited'] }})</a>
                    </div>

                    <div class="card-body pt-3 px-5">
                        {{-- Filter bar --}}
                        <div class="team-filter-bar">
                            <div class="team-search-wrap">
                                <span class="team-search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" id="kt_tagify_2" placeholder="Search by name, email or phone..."
                                    onkeyup="filtersdataget()" />
                            </div>
                            <div class="team-sort-card">
                                <span class="team-sort-label">
                                    <i class="fa-solid fa-arrow-up-wide-short"></i> Sort by
                                </span>
                                <select class="team-select" id="sortingfilter" onchange="filtersdataget()">
                                    <option value="">Last Active</option>
                                    <option value="1">Newest to Oldest</option>
                                    <option value="2">Oldest to Newest</option>
                                </select>
                            </div>
                        </div>

                        @if (in_array('view_team', $userPermissions))
                            <div class="loadingMessage loadingMessageteamsListajax"
                                style="display: none; position: absolute;">Processing...</div>
                            <div class="table-responsive">
                                <table class="team-table" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>Member</th>
                                            <th>Rank</th>
                                            <th>Workload</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Last Active</th>
                                            <th style="text-align:right;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="teamsListajax"> </tbody>
                                </table>
                            </div>
                            <div id="paginationTeams"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content-->

        {{-- Invite Member Drawer --}}
        <div class="invite-overlay" id="inviteOverlay"></div>
        <div class="invite-drawer" id="inviteDrawer" role="dialog" aria-modal="true" aria-label="Invite Team Member">
            {{-- Header --}}
            <div class="invite-drawer-head">
                <div>
                    <p class="invite-drawer-title">Invite Team Member</p>
                    <p class="invite-drawer-subtitle">Add a new team member and set their role &amp; permissions</p>
                </div>
                <button class="invite-drawer-close" id="inviteDrawerClose" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="invite-drawer-body">
                <form id="inviteForm" enctype="multipart/form-data">
                    @csrf

                    {{-- Name --}}
                    <div class="invite-field">
                        <label class="invite-field-label">Name <span class="req">*</span></label>
                        <div class="invite-input-wrap">
                            <span class="invite-input-icon"><i class="fa-regular fa-user"></i></span>
                            <input type="text" name="name" id="inviteName" placeholder="Enter full name" autocomplete="off" />
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="invite-field">
                        <label class="invite-field-label">Email <span class="req">*</span></label>
                        <div class="invite-input-wrap">
                            <span class="invite-input-icon"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" name="email" id="inviteEmail" placeholder="Enter email address" autocomplete="off" />
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="invite-field">
                        <label class="invite-field-label">Role &amp; Permissions <span class="req">*</span></label>
                        <div class="invite-input-wrap invite-select-wrap">
                            <span class="invite-input-icon"><i class="fa-solid fa-shield-halved"></i></span>
                            <select name="role_id" id="inviteRole">
                                <option value="" disabled selected>Select role</option>
                                {{-- Populated by AJAX --}}
                                <div id="inviteRoleOptions"></div>
                            </select>
                        </div>
                        <p class="invite-field-hint">Role will define the permissions the user will have in the system.</p>
                    </div>

                    {{-- Message area --}}
                    <div id="inviteMsg" style="display:none; font-size:13px; padding:10px 14px; border-radius:8px; margin-top:4px;"></div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="invite-drawer-foot">
                <button class="invite-btn-cancel" id="inviteCancelBtn">Cancel</button>
                <button class="invite-btn-send" id="inviteSubmitBtn">
                    <span class="invite-label"><i class="fa-solid fa-paper-plane me-1"></i> Send Invitation</span>
                    <span class="invite-spinner"><span class="spinner-border spinner-border-sm"></span> Sending...</span>
                </button>
            </div>
        </div>
        {{-- End Invite Drawer --}}

        <!--begin::Update role-->
        <div class="modal fade show" id="update_role" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <div class="modal-content">
                    <div class="modal-header" id="update_role_header" data-dismiss="modal">
                        <h2 class="fw-bolder">Update role</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-danger update_role_close"> <span
                                class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                        transform="rotate(-45 6 17.3137)"></rect>
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)"></rect>
                                </svg>
                            </span> </div>
                    </div>
                    <div class="modal-body scroll-y mx-5" id="update_role_body"> </div>
                </div>
            </div>
        </div>
        <!--end::Update role-->


        <div class="modal fade" id="status_stage" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header flex-stack">
                        <h2>Requisition Status</h2>
                        <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                            id="status_sub_stage_close"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                        transform="rotate(-45 6 17.3137)" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)" />
                                </svg>
                            </span> </div>
                    </div>
                    <div class="status_body_content" id="status_body_content"></div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="team_rank_user" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content custmbg" style="border-radius:18px;">
                    <div class="modal-body sb-wrap" style="padding:20px 24px 24px;" id="team_scoreboard_body">
                        <div style="text-align:center; padding:40px; color:#8a93a8;">Loading scoreboard...</div>
                    </div>
                </div>
            </div>
        </div>


    @endsection

    @push('footer-script')
        <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
        <div class="closeclass"></div>

        <!--<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>-->
        <script>
            var input2 = document.querySelector("#kt_tagify_2");
            // new Tagify(input2);
        </script>
        <script>
            $('#kt_tagify_2').keyup(function() {
                // table._fnDraw();
				fetchTeams();
            });

            function filtersdataget() {
                // table._fnDraw();
				fetchTeams();
            }

            $(document).on('click', '.teamViewScore', function() {
                var userId = $(this).data('user-id');
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var url = "{{ route('service_company.team.scoreboard') }}";

                $('#team_scoreboard_body').html('<div style="text-align:center; padding:40px;"><img src="' + baseurl + '" height="35px" width="100%"></div>');

                // Show modal — support both Bootstrap 4 (jQuery .modal) and Bootstrap 5 (native API)
                var modalEl = document.getElementById('team_rank_user');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    bsModal.show();
                } else {
                    $(modalEl).modal('show');
                }

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: { user_id: userId },
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#team_scoreboard_body').html(response.view);
                        } else {
                            $('#team_scoreboard_body').html('<div style="text-align:center;padding:40px;color:#e53935;">Could not load scoreboard.</div>');
                        }
                    },
                    error: function(xhr) {
                        $('#team_scoreboard_body').html('<div style="text-align:center;padding:40px;color:#e53935;">Error ' + xhr.status + ': ' + xhr.statusText + '</div>');
                    }
                });
            });

            $(document).on('click', '.update_role_close', function() {


                // alert("ji");
                $("#update_role").removeAttr("style");
            });
            $(document).on('click', '#kt_activitiesClient_close', function() {
                $(".closeclass").removeAttr("style");
                $(".closeclass").removeClass("drawer-overlay");
                $("#kt_activitiesClient").removeAttr("style");
            });

            $(document).on('click', '.closeclass', function() {

                $("#kt_activitiesClient_close").click();
            });
            $(document).on('click', '.drawer-overlay', function() {
              $("#kt_activitiesClient_close").click();
            });


            $(document).on('click', '.chk_list_item', () => {
                var i = 0;
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                setTimeout(() => {
                    if (arr.length > 0) {
                        $('.teamdelete').removeClass("d-none");
                    } else {
                        $('.teamdelete').addClass("d-none");
                    }
                }, 10);

            });
            $(document).on('click', '.chk_list', () => {
                if ($(".chk_list").prop('checked') == true) {
                    var chk_list = $('.chk_list').val();
                    $('.chk_list_item').attr('checked', 'checked');
                    $('.teamdelete').addClass("d-none");
                } else {
                    $('.chk_list_item').removeAttr('checked');
                    $('.teamdelete').removeClass("d-none");
                }
            });

            $(document).on('click', '.teamdelete', () => {
                var arr = [];
                $('.chk_list_item:checked').each(function() {
                    arr.push($(this).val());
                });
                var id = 0;
                var url = "{{ route('service_company.team.teamDelete', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You want to delete team!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'POST',
                                'arr': arr
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status == "success") {
                                    // table._fnDraw();
									fetchTeams();
                                    $('.teamdelete').addClass("d-none");
                                    Swal.fire({
                                        text: "Team inactive successfully.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            }
                        });

                    } else if (result.dismiss === Swal.DismissReason.cancel) {

                        swalWithBootstrapButtons.fire('Cancelled', 'Team not delete:)', 'error')
                    }

                })

            });

            function handleClick(currentValue, currentId) {
  
                var chkStatus = currentValue.checked;
                var userId = currentId;
                var statusText = $(currentValue).parents('td').find('.status_text');
                if ($(currentValue).is(':checked')) {
                    statusText.text('Active').removeClass('text-danger').addClass('text-success');
                } else {
                    statusText.text('Inactive').removeClass('text-success').addClass('text-danger');
                }
                var url = "{{ route('service_company.team.statusUpdate') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                $.ajax({
                    type: 'POST',
                    url: url,
                    beforeSend: function() {},
                    data: {
                        '_token': token,
                        'chkStatus': chkStatus,
                        'userId': userId
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            fetchTeams(); 
                            $('#' + block).remove();  
                        }
                    }
                });
            }

            function reSendhandleClick(currentValue, currentId) {

                var chkStatus = currentValue.checked;
                var userId = currentId;
                var url = "{{ route('service_company.team.reSendmail') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";


                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You want to resend mail!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            beforeSend: function() {},
                            data: {
                                '_token': token,
                                'chkStatus': chkStatus,
                                'userId': userId
                            },
                            success: function(response) {
                                if (response.status == "success") {

                                    Swal.fire({
                                        text: "Team member Re-send successfully. set password link has been sent to team member email.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });


                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {

                        swalWithBootstrapButtons.fire('Cancelled', 'mail not send:)', 'error')
                    }

                })





            }


            $(document).on('click', '.update-user-role', function() {

                $('.update_role').trigger('click');

                var id = $(this).data('row-id');
                var url = "{{ route('service_company.team.editrole', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                // alert(url);
                $.ajax({
                    type: 'GET',
                    url: url,
                    beforeSend: function() {
                        $('#update_role_body').html('<img src="' + baseurl +
                            '" height="35px" width="100%">');
                    },
                    success: function(response) {
                        $("#update_role").attr("style", "display: block;");
                        $('#update_role_body').html(response.view);

                        /*setTimeout(
                        ()=>
                        {
                        		if (response.status == "success")
                        		{
                        			$('#kt_activitiesClient_body').html(response.view);
                        		}
                        }, 500);*/
                    }
                });

            });

            // ---- Invite Drawer ----
            function openInviteDrawer() {
                var rolesUrl = "{{ route('service_company.team.add_team_member_form', '123') }}";
                // Load roles into select
                $.get(rolesUrl, function(response) {
                    if (response.status === 'coinCheck') {
                        Swal.fire("Insufficient Coins", response.message, "warning");
                        return;
                    }
                    if (response.status === 'success') {
                        // Parse roles from the returned HTML and populate select
                        var $html = $(response.view);
                        var $select = $('#inviteRole');
                        $select.find('option:not([disabled])').remove();
                        $html.find('input[type="radio"][name="role_id"]').each(function() {
                            var val = $(this).val();
                            var label = $(this).siblings('label').find('.fw-bolder').text().trim()
                                     || $(this).closest('.d-flex').find('.fw-bolder').text().trim();
                            if (val && label) {
                                $select.append('<option value="' + val + '">' + label + '</option>');
                            }
                        });
                    }
                });
                // Reset form
                $('#inviteForm')[0].reset();
                $('#inviteMsg').hide().text('');
                $('#inviteSubmitBtn').removeClass('loading').prop('disabled', false);
                // Open
                $('#inviteOverlay, #inviteDrawer').addClass('open');
                document.body.style.overflow = 'hidden';
            }

            function closeInviteDrawer() {
                $('#inviteOverlay, #inviteDrawer').removeClass('open');
                document.body.style.overflow = '';
            }

            $('#openInviteDrawer').on('click', openInviteDrawer);
            $('#inviteDrawerClose, #inviteCancelBtn').on('click', closeInviteDrawer);
            $('#inviteOverlay').on('click', closeInviteDrawer);
            $(document).on('keydown', function(e) { if (e.key === 'Escape') closeInviteDrawer(); });

            $('#inviteSubmitBtn').on('click', function() {
                var name  = $.trim($('#inviteName').val());
                var email = $.trim($('#inviteEmail').val());
                var role  = $('#inviteRole').val();

                if (!name) {
                    showInviteMsg('Please enter the full name.', 'error'); return;
                }
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    showInviteMsg('Please enter a valid email address.', 'error'); return;
                }
                if (!role) {
                    showInviteMsg('Please select a role.', 'error'); return;
                }

                var $btn = $(this);
                $btn.addClass('loading').prop('disabled', true);
                $('#inviteMsg').hide();

                var formData = new FormData($('#inviteForm')[0]);

                $.ajax({
                    url: '{{ route("service_company.team.store") }}?_token={{ csrf_token() }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $btn.removeClass('loading').prop('disabled', false);
                        if (response.msgType === 'success') {
                            closeInviteDrawer();
                            fetchTeams();
                            Swal.fire('Invitation Sent!', response.message, 'success');
                        } else {
                            showInviteMsg(response.message || 'Something went wrong.', 'error');
                        }
                    },
                    error: function() {
                        $btn.removeClass('loading').prop('disabled', false);
                        showInviteMsg('Server error. Please try again.', 'error');
                    }
                });
            });

            function showInviteMsg(msg, type) {
                var $m = $('#inviteMsg');
                $m.text(msg)
                  .css({
                      background: type === 'error' ? '#fef2f2' : '#f0fdf4',
                      color:      type === 'error' ? '#dc2626'  : '#16a34a',
                      border:     type === 'error' ? '1px solid #fecaca' : '1px solid #bbf7d0'
                  })
                  .show();
            }
        </script>

        <script>
            function WorkingStatus(currentValue, currentId) {
                var chkStatus = currentValue.checked;
                var userId = currentId;
                var url = "{{ route('service_company.team.WorkingStatus') }}";
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "You want to update Working Status.!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            beforeSend: function() {},
                            data: {
                                '_token': token,
                                'chkStatus': chkStatus,
                                'userId': userId
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    swalWithBootstrapButtons.fire('Success!',
                                        'Working status update successfully.', 'success')
                                } else if (response.status == "info") {
                                    if (chkStatus == true) {
                                        $('#working_status_' + userId).prop('checked', false);
                                    } else {
                                        $('#working_status_' + userId).prop('checked', true);
                                    }
                                    swalWithBootstrapButtons.fire('Fail!', response.message, 'error')
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        if (chkStatus == true) {
                            $('#working_status_' + userId).prop('checked', false);
                        } else {
                            $('#working_status_' + userId).prop('checked', true);
                        }
                        swalWithBootstrapButtons.fire('Cancelled', 'Working status can not update:)', 'error')
                    }

                })


            }

            function jointableLoad(userId) {
                return false;
                //    var userId		=	$('#userId').val();
                table = $('#myTable_joining').dataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ordering: false, //disable ordering
                    "bLengthChange": false, //thought this line could hide the LengthMenu
                    sDom: 'lrtip',
                    oLanguage: {
                        sLengthMenu: "_MENU_",
                    },
                    ajax: '{!! route('service_company.team.joiningdata') !!}?userId=' + userId,
                    columns: [{
                            data: 'chkbox',
                            name: 'chkbox',
                            width: '5%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Name',
                            name: 'Name',
                            width: '20%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Contact',
                            name: 'Contact',
                            width: '15%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Requisitions',
                            name: 'Requisitions',
                            width: '15%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Source',
                            name: 'Source',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Offer',
                            name: 'Offer',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Joining',
                            name: 'Joining',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Stage',
                            name: 'Stage',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
                new $.fn.dataTable.FixedHeader(jointable);
            }

            function load_interviewSchedule(userId) {
                return false;

                var userId = $('#userId').val();

                table = $('#load_interviewSchedule').dataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ordering: false, //disable ordering
                    "bLengthChange": false, //thought this line could hide the LengthMenu
                    sDom: 'lrtip',
                    oLanguage: {
                        sLengthMenu: "_MENU_",
                    },
                    ajax: '{!! route('service_company.team.intdata') !!}?userId=' + userId,

                    "fnDrawCallback": function(settings, start, end, max, total, pre) {
                        var total_record = this.fnSettings().fnRecordsTotal();
                        $('#total_record').html(total_record);
                        $("body").tooltip({
                            selector: '[data-toggle="tooltip"]'
                        });
                    },


                    "columnDefs": [{
                            "width": "50px",
                            "targets": 0
                        },
                        {
                            "width": "150px",
                            "targets": 1,
                            "class": "align-items-center"
                        },
                        {
                            "width": "50px",
                            "targets": 2
                        },
                        {
                            "width": "50px",
                            "targets": 3
                        },
                        {
                            "width": "50px",
                            "targets": 4
                        },
                        {
                            "width": "50px",
                            "targets": 5
                        },
                        {
                            "width": "50px",
                            "targets": 6
                        },
                        {
                            "width": "50px",
                            "targets": 7
                        },
                        {
                            "width": "50px",
                            "targets": 8
                        },
                        {
                            "width": "50px",
                            "targets": 9
                        }
                    ],

                    columns: [{
                            data: 'chkbox',
                            name: 'chkbox',
                            width: '1%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Name',
                            name: 'Name',
                            width: '15%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Requisitions',
                            name: 'Requisitions',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'InterviewMode',
                            name: 'InterviewMode',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Interviewers',
                            name: 'Interviewers',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Source',
                            name: 'Source',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'InterviewDate',
                            name: 'InterviewDate',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Feedback',
                            name: 'Feedback',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Rating',
                            name: 'Rating',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Stage',
                            name: 'Stage',
                            width: '10%',
                            searchable: false,
                            orderable: false
                        }
                    ],

                });
                new $.fn.dataTable.FixedHeader(table);

            }

            function load_application(userId) {
                return false;
                // var userId		=	$('#userId').val();
                application_table = $('#load_application').dataTable({

                    responsive: true,
                    processing: true,
                    ordering: false, //disable ordering
                    destroy: true,
                    stateSave: true,
                    "bLengthChange": false,
                    sDom: 'lrtip',
                    ajax: '{!! route('service_company.team.appdata') !!}?userId=' + userId,
                    "initComplete": function(settings, json) {
                        $('.clientStatusLoader').html('');
                        $('.jobLoader').html('');
                        $('.sourceLoader').html('');
                        $('.search_np_idLoader').html('');
                        $('.search_passportLoader').html('');
                        $('.statusLoader').html('');
                    },

                    columns: [{
                            data: 'chkbox',
                            name: 'chkbox',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Name',
                            name: 'Name',
                            orderable: false,
                            searchable: false,
                            width: '15%'
                        },
                        // {data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '15%'},						
                        {
                            data: 'Exp',
                            name: 'Exp',
                            searchable: false,
                            orderable: false,
                            width: '5%'
                        },
                        {
                            data: 'Salary',
                            name: 'Salary',
                            searchable: false,
                            orderable: false,
                            width: '5%'
                        },
                        {
                            data: 'NoticePeriod',
                            name: 'NoticePeriod',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Qualifaction',
                            name: 'Qualifaction',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'Location',
                            name: 'Location',
                            orderable: false
                        },
                        {
                            data: 'Source',
                            name: 'Source',
                            orderable: false
                        },
                        {
                            data: 'UploadDate',
                            name: 'UploadDate',
                            orderable: false
                        },
                        {
                            data: 'Requisitions',
                            name: 'Requisitions',
                            orderable: false
                        },
                        {
                            data: 'Stage',
                            name: 'Stage',
                            orderable: false
                        }
                    ]

                });
                new $.fn.dataTable.FixedHeader(application_table);

            }

            $(document).on('keyup', function() {
                var userId = $('#kt_tagify_hidden').val();
                load_jobs(userId);
            });
            function load_jobs(userId) {
                return false;
                var load_jobs = $('#load_jobs').dataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ordering: false, //disable ordering   			
                    "bLengthChange": false, //thought this line could hide the LengthMenu
                    sDom: 'lrtip',
                    oLanguage: {
                        sLengthMenu: "_MENU_",
                    },
                    ajax: {
                        'url': '{!! route('service_company.team.jobdata') !!}?userId=' + userId,
                        "data": function(d) {
                            d.search = $('#inputSearch').val(), d.custom_search = $('#kt_tagify_search').val(), d
                                .custom_status = $('#statusjb').val(), d.custom_hiringtype = $('#hiringtypejob')
                                .val(), d.profile = true
                        }
                    },
                    "drawCallback": function(settings) {
                        $('[data-toggle="tooltip1"]').tooltip();
                        $('[data-toggle="tooltip2"]').tooltip();
                        $('[data-toggle="tooltip3"]').tooltip();
                        $('[data-toggle="tooltip4"]').tooltip();
                    },
                    "columnDefs": [{
                            "width": "50px",
                            "targets": 0,
                            "width": "4%",
                            "class": ""
                        },
                        {
                            "width": "200px",
                            "targets": 1,
                            "class": "d-flex align-items-center"
                        },
                        {
                            "width": "50px",
                            "targets": 2
                        },
                        {
                            "width": "50px",
                            "targets": 3
                        },
                        {
                            "width": "200px",
                            "targets": 4
                        },
                        {
                            "width": "50px",
                            "targets": 5
                        },
                        {
                            "width": "200px",
                            "targets": 6
                        },
                        {
                            "width": "150px",
                            "targets": 7
                        },
                        {
                            "width": "150px",
                            "targets": 8
                        },

                        {
                            "width": "20px",
                            "targets": 9
                        },
                        {
                            "width": "20px",
                            "targets": 10
                        },
                        {
                            "width": "20px",
                            "targets": 11
                        },
                        {
                            "width": "20px",
                            "targets": 12
                        },
                        {
                            "width": "20px",
                            "targets": 13
                        },
                        {
                            "width": "20px",
                            "targets": 14
                        },
                        {
                            "width": "20px",
                            "targets": 15
                        },
                        {
                            "width": "20px",
                            "targets": 16
                        }
                    ],
                    columns: [{
                            data: 'chkbox',
                            name: 'chkbox',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Requisition',
                            name: 'Requisition',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Exp',
                            name: 'Exp',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Salary',
                            name: 'Salary',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Location',
                            name: 'Location',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'Status',
                            name: 'Status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Date',
                            name: 'Date',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'HM',
                            name: 'HM',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'Partners',
                            name: 'Partners',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'TOT',
                            name: 'TOT',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'SOU',
                            name: 'SOU',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'SCR',
                            name: 'SCR',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'EVA',
                            name: 'EVA',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'NEO',
                            name: 'NEO',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'OFF',
                            name: 'OFF',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'JOI',
                            name: 'JOI',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'REJ',
                            name: 'REJ',
                            orderable: false,
                            searchable: false
                        }
                    ]

                });
                new $.fn.dataTable.FixedHeader(load_jobs);
            }

			$(document).ready(function() {
				fetchTeams();
				$(document).on('click', '#paginationTeams [data-page]', function(e) {
						e.preventDefault();

						const $btn = $(this);
						const page = $btn.data('page');
						if (!page || $btn.hasClass('is-disabled') || $btn.hasClass('is-active')
							|| $btn.hasClass('disabled') || $btn.hasClass('active')) return;

						$btn.addClass('is-disabled');

						fetchTeams(page, $('#search').val()).then(() => {
							$('#paginationTeams [data-page]').removeClass('is-disabled');
						}).catch(() => {
							$('#paginationTeams [data-page]').removeClass('is-disabled');
						});

				});
			});
            
               var page_no = localStorage.getItem('currentPageTeam');
                if (!page_no) {
                    page_no = 1;
                }
			

			// Active status tab + rows-per-page for the redesigned list.
			var teamStatusTab = 'all';
			var teamPageSize = 10;

			function fetchTeams(page = page_no, search = '') {
				$('.loadingMessageteamsListajax').css({ display: 'block' });
				return $.ajax({
					url: `{!! route('service_company.team.data_ajax') !!}?page=${page}`,
					type: 'GET',
					data: {
						search: $('#inputSearch').val(),
						cus_search: $('#kt_tagify_2').val(),
						country: $('#country').val(),
						sortingfilter: $('#sortingfilter').val(),
						statusTab: teamStatusTab,
						size: teamPageSize
					},
					success: function(response) {
						$('.loadingMessageteamsListajax').css({ display: 'none' });
                        localStorage.setItem('currentPageTeam', 1);
						$('#teamsListajax').html(response.html);
						$('#paginationTeams').html(response.pagination);
						updateTeamSelectedCount();
					}
				});
			}

			// Switch status tabs (All / Active / Inactive / Invited / Suspended)
			$(document).on('click', '.team-tab', function() {
				$('.team-tab').removeClass('active');
				$(this).addClass('active');
				teamStatusTab = $(this).data('status');
				fetchTeams(1);
			});

			// Rows-per-page selector in the footer
			function changeTeamPageSize(val) {
				teamPageSize = parseInt(val) || 10;
				fetchTeams(1);
			}

			function clearTeamFilters() {
				$('#kt_tagify_2').val('');
				$('#sortingfilter').val('');
				fetchTeams(1);
			}

			// Keep the "N selected" bulk counter in sync
			function updateTeamSelectedCount() {
				$('#teamSelectedCount').text($('.chk_list_item:checked').length);
			}
			$(document).on('change', '.chk_list_item', updateTeamSelectedCount);

			// Export hook — reuses the existing add/export plumbing
			function exportTeam() {
				window.print();
			}

            // ---- Recalculate toolbar fixed position from live DOM ----
            function positionToolbar() {
                var headerH  = $('#kt_header').outerHeight(true) || 65;
                var asideW   = $('#kt_aside').outerWidth(true)   || 265;
                var toolbarH = $('#kt_toolbar').outerHeight(true) || 55;
                $('#kt_toolbar').css({ top: headerH, left: asideW });
                $('#kt_content').css({ 'padding-top': toolbarH + 10 });
            }
            $(document).ready(positionToolbar);
            $(window).on('resize', positionToolbar);

			// Row actions dropdown
			function toggleTeamMenu(btn) {
				var menu = btn.nextElementSibling;
				var isOpen = menu.classList.contains('show');
				document.querySelectorAll('.team-action-menu.show').forEach(function(m) { m.classList.remove('show'); });
				if (!isOpen) menu.classList.add('show');
			}
			$(document).on('click', function(e) {
				if (!$(e.target).closest('.team-action-wrap').length) {
					$('.team-action-menu.show').removeClass('show');
				}
			});



        </script>
    @endpush
