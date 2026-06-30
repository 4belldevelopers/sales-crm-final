@extends('layouts.service_company_layout')
@push('head-script')
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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

        /* ---------- Header ---------- */
        #kt_toolbar { background: transparent; }
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
            padding: 16px 0 8px;
            flex-wrap: wrap;
        }
        .team-search {
            position: relative;
            flex: 1;
            min-width: 240px;
        }
        .team-search i {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #7c8398;
            font-size: 14px;
        }
        .team-search input {
            width: 100%;
            height: 40px;
            border: 1px solid #eef0f4;
            border-radius: 9px;
            padding: 0 38px 0 14px;
            font-size: 13px;
            background: #fff;
            color: #1e2129;
        }
        .team-select {
            height: 40px;
            border: 1px solid #eef0f4;
            border-radius: 9px;
            padding: 0 32px 0 13px;
            font-size: 13px;
            color: #1e2129;
            background: #fff url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237c8398' stroke-width='2.5'><path d='M6 9l6 6 6-6'/></svg>") no-repeat right 11px center;
            -webkit-appearance: none;
            appearance: none;
            cursor: pointer;
        }
        .team-sort-label { font-size: 13px; color: #7c8398; white-space: nowrap; }
        .team-sort-label b { color: #1e2129; font-weight: 600; }

        /* ---------- Bulk actions ---------- */
        .team-bulk-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 0;
        }
        .team-bulk-count { font-size: 13px; color: #7c8398; }
        .team-bulk-btn {
            font-size: 13px;
            font-weight: 600;
            color: #7c8398;
            border: 1px solid #eef0f4;
            border-radius: 8px;
            padding: 7px 13px;
            background: #fff;
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
                <a href="javascript:void(0);" class="team-btn team-btn-light add_team_member" data-toggle="modal"
                    data-target="#add_team_member">
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
                            <div class="team-search">
                                <input type="text" id="kt_tagify_2" placeholder="Search by name, email or phone..."
                                    onkeyup="filtersdataget()" />
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                            <div class="ms-auto d-flex align-items-center gap-3">
                                <span class="team-sort-label">Sort by:
                                    <select class="team-select" id="sortingfilter" onchange="filtersdataget()"
                                        style="height:34px; border:none; padding-right:24px; font-weight:600;">
                                        <option value="">Last Active</option>
                                        <option value="1">Newest to Oldest</option>
                                        <option value="2">Oldest to Newest</option>
                                    </select>
                                </span>
                            </div>
                        </div>

                        {{-- Bulk actions --}}
                        <div class="team-bulk-row">
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input chk_list" type="checkbox" value="1">
                            </div>
                            <span class="team-bulk-count"><span id="teamSelectedCount">0</span> selected</span>
                            <button type="button" class="team-bulk-btn">Bulk Actions <i
                                    class="fa-solid fa-chevron-down ms-1" style="font-size:10px;"></i></button>
                        </div>

                        @if (in_array('view_team', $userPermissions))
                            <div class="loadingMessage loadingMessageteamsListajax"
                                style="display: none; position: absolute;">Processing...</div>
                            <div class="table-responsive">
                                <table class="team-table" id="myTable">
                                    <thead>
                                        <tr>
                                            <th style="width:34px;"></th>
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

        <!--begin::Add Team Member Modal-->
        <div class="modal fade" id="add_team_member" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <div class="modal-content">
                    <div class="modal-header" id="add_team_member_header">
                        <h2 class="fw-bolder">Add Team Member</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-danger" data-dismiss="modal"> <span
                                class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                        transform="rotate(-45 6 17.3137)" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)" />
                                </svg>
                            </span> </div>


                    </div>
                    <div class="modal-body scroll-y mx-5">
                        <form id="add_team_member_form" name="add_team_member_form" class="form" action="#"
                            method="post">
                            <meta name="csrf-token" content="{{ csrf_token() }}" />
                            @csrf
                            @method('POST')
                            <div id="load_add_team_invitation_content"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Add team member popup-->

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
                $("#team_rank_user").modal("show");

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: { user_id: userId },
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#team_scoreboard_body').html(response.view);
                        }
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

            $(document).on('click', '.add_team_member', function() {
                var id = 123;
                var url = "{{ route('service_company.team.add_team_member_form', ':id') }}";
                url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                $.ajax({
                    type: 'GET',
                    url: url,
                    beforeSend: function() {
                        $('#load_add_team_invitation_content').html('<img src="' + baseurl +
                            '" height="60%" width="100%">');
                    },
                    success: function(response) {
                        if (response.status == 'coinCheck') {
                            Swal.fire("Failed", ` ${response.message}`, "warning").then(function() {
                                $(".btn-active-icon-primary").click();
                            });
                            return false;
                        }
                        if (response.status == "success") {
                            $('#load_add_team_invitation_content').html(response.view);
                        }

                    }
                });
            });
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
