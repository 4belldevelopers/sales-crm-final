@extends('layouts.service_company_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/icheck/skins/all.css') }}<?php echo TM_VERSION; ?>">
<link href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<style>
body { overflow-x: hidden; font-family: 'Poppins', sans-serif !important; }

/* ── Override Metronic wrapper defaults ── */
#kt_wrapper { background: #f1f3f8 !important; }
#kt_content, .content { padding: 0 !important; margin: 0 !important; }
.toolbar { display: none !important; }
.post { padding: 0 !important; }

/* ── Page wrapper ── */
.rp-page { padding: 24px 28px; background: #f1f3f8; min-height: calc(100vh - 65px); }

/* ── Page header ── */
.rp-page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
.rp-page-title { display: flex; align-items: center; gap: 8px; }
.rp-page-title h1 { font-size: 20px; font-weight: 700; color: #1e2640; margin: 0; line-height: 1.2; }
.rp-page-title .shield-icon { color: #5e72e4; }
.rp-page-subtitle { font-size: 12px; color: #8b93b8; margin: 4px 0 0; font-weight: 400; }
.rp-header-actions { display: flex; gap: 8px; align-items: center; }

/* ── Buttons ── */
.btn-rp-primary {
    background: #5e72e4; color: #fff; border: none; border-radius: 8px;
    padding: 9px 16px; font-size: 12px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px;
    font-family: 'Poppins', sans-serif; transition: background .15s;
    text-decoration: none;
}
.btn-rp-primary:hover { background: #4c5fd5; color: #fff; text-decoration: none; }

/* ── Stat cards ── */
.rp-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 20px; }
.rp-stat-card {
    background: #fff; border-radius: 10px; padding: 14px 16px;
    border: 1px solid #eaf0fb; display: flex; align-items: center; gap: 12px;
}
.rp-stat-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.rp-stat-icon svg { width: 22px; height: 22px; }
.rp-stat-val { font-size: 22px; font-weight: 700; color: #1e2640; line-height: 1; }
.rp-stat-lbl { font-size: 11px; color: #5a6080; font-weight: 500; margin-top: 2px; }
.rp-stat-sub { font-size: 10px; font-weight: 500; margin-top: 2px; }

/* ── Tabs ── */
.rp-tabs-bar {
    display: flex; align-items: center; gap: 2px;
    background: #fff; border-radius: 10px; padding: 5px;
    border: 1px solid #eaf0fb; width: fit-content; margin-bottom: 16px;
}
.rp-tab {
    padding: 7px 16px; border-radius: 7px; font-size: 12px;
    font-weight: 500; color: #8b93b8; cursor: pointer;
    transition: all .15s; user-select: none; white-space: nowrap;
}
.rp-tab.active { background: #5e72e4; color: #fff; font-weight: 600; }
.rp-tab:not(.active):hover { background: #f1f3f8; color: #5a6080; }

/* ── Filter row ── */
.rp-filter-row { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
.rp-search-box {
    display: flex; align-items: center; gap: 8px;
    background: #fff; border: 1px solid #dde2f0; border-radius: 8px;
    padding: 7px 12px; flex: 0 0 220px;
}
.rp-search-box input {
    border: none; outline: none; font-size: 12px; color: #1e2640;
    font-family: 'Poppins', sans-serif; width: 100%; background: transparent;
}
.rp-search-box input::placeholder { color: #b0bbd8; }
.rp-search-box svg { width: 14px; height: 14px; color: #b0bbd8; flex-shrink: 0; }
.rp-filter-btn {
    background: #fff; border: 1px solid #dde2f0; border-radius: 8px;
    padding: 7px 12px; font-size: 12px; color: #5a6080; font-weight: 500;
    display: inline-flex; align-items: center; gap: 5px; cursor: pointer;
    font-family: 'Poppins', sans-serif; transition: border-color .15s;
}
.rp-filter-btn:hover { border-color: #5e72e4; color: #5e72e4; }
.rp-filter-btn svg { width: 12px; height: 12px; }
.rp-filter-select {
    appearance: none; -webkit-appearance: none;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235a6080' stroke-width='2.5' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 8px center;
    background-size: 11px; border: 1px solid #dde2f0; border-radius: 8px;
    padding: 7px 26px 7px 12px; font-size: 12px; color: #5a6080; font-weight: 500;
    font-family: 'Poppins', sans-serif; cursor: pointer; outline: none;
    transition: border-color .15s;
}
.rp-filter-select:hover, .rp-filter-select:focus { border-color: #5e72e4; }
.rp-filter-select.is-active { border-color: #5e72e4; color: #5e72e4; background-color: #f6f8ff; }
.rp-filter-clear { font-size: 12px; color: #5e72e4; font-weight: 500; cursor: pointer; }
.rp-sort-label { font-size: 12px; color: #8b93b8; margin-left: auto; }
.rp-sort-select {
    border: 1px solid #dde2f0; border-radius: 7px; padding: 6px 10px;
    font-size: 12px; color: #5a6080; background: #fff;
    font-family: 'Poppins', sans-serif; cursor: pointer; outline: none;
}

/* ── Empty state ── */
.rp-empty-state {
    display: none; grid-column: 1 / -1; flex-direction: column; align-items: center;
    justify-content: center; gap: 10px; padding: 48px 16px; text-align: center;
}
.rp-empty-state.show { display: flex; }
.rp-empty-icon {
    width: 48px; height: 48px; border-radius: 50%; background: #f1f3f8;
    display: flex; align-items: center; justify-content: center; color: #b0bbd8;
}
.rp-empty-icon svg { width: 22px; height: 22px; }
.rp-empty-title { font-size: 13px; font-weight: 600; color: #5a6080; }
.rp-empty-sub { font-size: 11px; color: #b0bbd8; }

/* ── Grid layout ── */
.rp-grid-wrapper { display: flex; gap: 0; align-items: flex-start; }
.rp-cards-area { flex: 1; min-width: 0; }
.rp-cards-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }

/* ── Role card ── */
.rp-role-card {
    background: #fff; border: 1px solid #eaf0fb; border-radius: 12px;
    padding: 16px; cursor: pointer; transition: border-color .15s, box-shadow .15s;
    position: relative;
}
.rp-role-card:hover { border-color: #c5ccee; box-shadow: 0 2px 12px rgba(94,114,228,.08); }
.rp-role-card.selected { border-color: #5e72e4; box-shadow: 0 2px 16px rgba(94,114,228,.14); }

.rp-card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 8px; }
.rp-card-icon-wrap { display: flex; align-items: center; gap: 10px; }
.rp-card-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.rp-card-icon svg { width: 20px; height: 20px; }
.rp-card-role-name { font-size: 12px; font-weight: 700; color: #1e2640; line-height: 1.3; }
.rp-card-role-type { font-size: 10px; color: #8b93b8; font-weight: 400; margin-top: 1px; }
.rp-card-role-type.custom { color: #5e72e4; }

/* Access badges */
.access-badge {
    font-size: 10px; font-weight: 600; padding: 3px 9px;
    border-radius: 20px; white-space: nowrap; flex-shrink: 0;
}
.badge-high { background: #fde8e8; color: #c0392b; }
.badge-medium { background: #e3f1ff; color: #1a73e8; }
.badge-low { background: #e8f8f0; color: #27ae60; }

.rp-card-desc { font-size: 11px; color: #8b93b8; margin-bottom: 10px; line-height: 1.5; }

.rp-card-counts { display: flex; gap: 20px; margin-bottom: 10px; }
.rp-count-val { font-size: 18px; font-weight: 700; color: #1e2640; line-height: 1; }
.rp-count-lbl { font-size: 10px; color: #b0bbd8; font-weight: 400; margin-top: 1px; }

.rp-perm-label { font-size: 10px; color: #b0bbd8; margin-bottom: 5px; font-weight: 500; }
.rp-perm-tags { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 10px; }
.rp-perm-tag {
    font-size: 10px; background: #f1f3fb; color: #5a6080;
    padding: 2px 7px; border-radius: 5px; font-weight: 500;
}

.rp-users-row { display: flex; align-items: center; gap: 6px; margin-bottom: 10px; }
.rp-users-label { font-size: 10px; color: #b0bbd8; font-weight: 500; }
.rp-avatars-stack { display: flex; }
.rp-mini-av {
    width: 22px; height: 22px; border-radius: 50%;
    border: 2px solid #fff; display: flex; align-items: center;
    justify-content: center; font-size: 8px; font-weight: 700;
    color: #fff; margin-left: -5px; flex-shrink: 0;
}
.rp-mini-av:first-child { margin-left: 0; }
.rp-more-users { font-size: 10px; color: #8b93b8; }

.rp-card-actions {
    display: flex; align-items: center; justify-content: flex-start;
    border-top: 1px solid #f1f3f8; padding-top: 10px; margin-top: 4px;
}
.rp-action-group { display: flex; gap: 6px; }
.rp-action-btn {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    color: #b0bbd8; cursor: pointer; border: none; background: transparent;
    transition: background .12s, color .12s;
}
.rp-action-btn:hover { background: #f1f3f8; color: #5a6080; }
.rp-action-btn svg { width: 14px; height: 14px; }
.rp-action-btn.danger:hover { background: #fde8e8; color: #c0392b; }

/* ── Add New Role card ── */
.rp-add-card {
    background: #fff; border: 2px dashed #c5d0ea; border-radius: 12px;
    padding: 16px; display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 8px;
    cursor: pointer; min-height: 160px; transition: border-color .15s;
}
.rp-add-card:hover { border-color: #5e72e4; }
.rp-add-plus {
    width: 36px; height: 36px; border: 2px dashed #c5d0ea; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; color: #b0bbd8;
    transition: border-color .15s, color .15s;
}
.rp-add-card:hover .rp-add-plus { border-color: #5e72e4; color: #5e72e4; }
.rp-add-plus svg { width: 16px; height: 16px; }
.rp-add-title { font-size: 13px; font-weight: 600; color: #5a6080; }
.rp-add-sub { font-size: 11px; color: #b0bbd8; text-align: center; line-height: 1.5; }

/* ── Right detail panel ── */
.rp-detail-panel {
    width: 0; overflow: hidden; transition: width .25s ease;
    background: #fff; border-left: 1px solid #eaf0fb; flex-shrink: 0;
    display: flex; flex-direction: column;
}
.rp-detail-panel.open { width: 280px; overflow-y: auto; }
.rp-detail-inner { padding: 16px; min-width: 280px; }

.rp-detail-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; }
.rp-detail-name { font-size: 15px; font-weight: 700; color: #1e2640; margin: 0 0 6px; }
.rp-detail-badges { display: flex; gap: 5px; flex-wrap: wrap; }
.rp-detail-ctrl { display: flex; gap: 4px; }
.rp-detail-ctrl-btn {
    width: 26px; height: 26px; border-radius: 6px; display: flex;
    align-items: center; justify-content: center; color: #b0bbd8;
    cursor: pointer; background: #f1f3f8; border: none; flex-shrink: 0;
}
.rp-detail-ctrl-btn:hover { background: #eaf0fb; color: #5a6080; }
.rp-detail-ctrl-btn svg { width: 13px; height: 13px; }

.rp-detail-stat-tabs { display: grid; grid-template-columns: repeat(3, 1fr); border: 1px solid #eaf0fb; border-radius: 10px; overflow: hidden; margin-bottom: 14px; }
.rp-dtab { padding: 10px 6px; text-align: center; font-size: 10px; color: #8b93b8; cursor: pointer; border-right: 1px solid #eaf0fb; }
.rp-dtab:last-child { border-right: none; }
.rp-dtab.active { background: #f6f8ff; color: #5e72e4; }
.rp-dtab-val { font-size: 16px; font-weight: 700; color: #1e2640; display: block; line-height: 1.2; margin-bottom: 2px; }
.rp-dtab.active .rp-dtab-val { color: #5e72e4; }

.rp-dsection { margin-bottom: 16px; }
.rp-dsection-title { font-size: 11px; font-weight: 600; color: #5a6080; margin: 0 0 10px; }
.rp-ddesc { font-size: 11px; color: #8b93b8; line-height: 1.6; }

.rp-pgroup-item { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f3f8; }
.rp-pgroup-item:last-child { border-bottom: none; }
.rp-pgroup-left { display: flex; align-items: center; gap: 8px; }
.rp-pgroup-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; }
.rp-pgroup-icon svg { width: 14px; height: 14px; }
.rp-pgroup-name { font-size: 11px; font-weight: 500; color: #1e2640; }
.rp-pgroup-right { display: flex; align-items: center; gap: 4px; font-size: 10px; color: #8b93b8; }
.rp-pgroup-right svg { width: 10px; height: 10px; }

.rp-user-item { display: flex; align-items: center; justify-content: space-between; padding: 6px 0; }
.rp-user-left { display: flex; align-items: center; gap: 8px; }
.rp-user-av {
    width: 32px; height: 32px; border-radius: 8px;
    background: #5e72e4; display: flex; align-items: center;
    justify-content: center; color: #fff; font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.rp-user-name { font-size: 11px; font-weight: 600; color: #1e2640; }
.rp-user-email { font-size: 10px; color: #b0bbd8; }
.rp-status-indicator { display: flex; align-items: center; gap: 4px; }
.rp-status-dot { width: 7px; height: 7px; border-radius: 50%; background: #2ecc71; }
.rp-status-txt { font-size: 10px; color: #27ae60; }

.rp-security-row { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f3f8; }
.rp-security-row:last-child { border-bottom: none; }
.rp-security-lbl { font-size: 11px; color: #8b93b8; }
.rp-security-val { font-size: 11px; font-weight: 600; color: #5a6080; text-align: right; max-width: 60%; }

/* ── Hidden tab filter ── */
.rp-role-card[data-role-type] { }
.rp-role-card.tab-hidden { display: none; }
</style>
@endpush

@section('content')

<?php
/* ── Pre-compute stats ── */
$totalRolesCount    = !empty($roles) ? count($roles) : 0;
$customRolesCount   = 0;
$systemRolesCount   = 0;
$totalAssignedUsers = 0;
$totalPermGroups    = 0;
$allRoleStats       = [];

if (!empty($roles)) {
    foreach ($roles as $rlist) {
        $roleId = $rlist->id;

        $uCount = DB::table('role_user')
            ->join('users', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id', $roleId)
            ->where('users.type', 0)
            ->count();

        $totalAssignedUsers += $uCount;
        $grpCount = count($rlist->grouped_permissions ?? []);
        $totalPermGroups += $grpCount;

        if ($rlist->manual_add) {
            $customRolesCount++;
        } else {
            $systemRolesCount++;
        }

        $allRoleStats[$roleId] = [
            'userCount' => $uCount,
            'grpCount'  => $grpCount,
        ];
    }
}

/* ── Icon & color palette for cards ── */
$cardPalette = [
    ['bg' => '#eef0fb', 'stroke' => '#5e72e4', 'icon' => 'shield-check'],
    ['bg' => '#e3f1ff', 'stroke' => '#1a73e8', 'icon' => 'users'],
    ['bg' => '#e8f8f0', 'stroke' => '#27ae60', 'icon' => 'user'],
    ['bg' => '#fff0f5', 'stroke' => '#e91e8c', 'icon' => 'message'],
    ['bg' => '#fff8e6', 'stroke' => '#f39c12', 'icon' => 'currency'],
    ['bg' => '#fde8e8', 'stroke' => '#c0392b', 'icon' => 'shield'],
    ['bg' => '#f1f3f8', 'stroke' => '#8b93b8', 'icon' => 'settings'],
];

/* Access level based on permission group count */
if (!function_exists('rpAccessLevel')) {
    function rpAccessLevel($grpCount, $roleName = '') {
        $name = strtolower($roleName);
        if (str_contains($name, 'admin') || $grpCount >= 5) return ['label' => 'High Access',   'class' => 'badge-high'];
        if ($grpCount >= 3)                                   return ['label' => 'Medium Access', 'class' => 'badge-medium'];
        return                                                       ['label' => 'Low Access',    'class' => 'badge-low'];
    }
}

/* Avatar bg colors for stack */
$avColors = ['#5e72e4','#1a73e8','#27ae60','#e74c3c','#f39c12','#9b59b6','#1abc9c','#e91e8c'];

/* Card SVG icons (inline, keyed by name) */
if (!function_exists('rpCardIcon')) {
    function rpCardIcon($name, $stroke) {
        $icons = [
        'shield-check' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
        'users'        => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'user'         => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'message'      => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'currency'     => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
        'shield'       => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'settings'     => '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>',
    ];
        $d = $icons[$name] ?? $icons['settings'];
        return '<svg viewBox="0 0 24 24" fill="none" stroke="'.$stroke.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'.$d.'</svg>';
    }
}
?>

<div class="rp-page">

    {{-- ── Page Header ── --}}
    <div class="rp-page-header">
        <div>
            <div class="rp-page-title">
                <h1>Roles &amp; Permissions</h1>
                <svg class="shield-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <p class="rp-page-subtitle">Create, manage and control access for different roles in your organization.</p>
        </div>
        <div class="rp-header-actions">
            @if(in_array("add_roles_permission", $userPermissions))
            <button class="btn-rp-primary" data-toggle="modal" data-target="#kt_modal_add_role" id="addNewRoleBtn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add New Role
            </button>
            @endif
        </div>
    </div>

    {{-- ── Stats Row ── --}}
    <div class="rp-stats">
        <div class="rp-stat-card">
            <div class="rp-stat-icon" style="background:#eef0fb;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#5e72e4" stroke-width="2" stroke-linecap="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="rp-stat-val">{{ $totalRolesCount }}</div>
                <div class="rp-stat-lbl">Total Roles</div>
                <div class="rp-stat-sub" style="color:#5e72e4;">All roles in system</div>
            </div>
        </div>
        <div class="rp-stat-card">
            <div class="rp-stat-icon" style="background:#e8f8f0;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#27ae60" stroke-width="2" stroke-linecap="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="rp-stat-val">{{ $systemRolesCount }}</div>
                <div class="rp-stat-lbl">System Roles</div>
                <div class="rp-stat-sub" style="color:#27ae60;">Built-in roles</div>
            </div>
        </div>
        <div class="rp-stat-card">
            <div class="rp-stat-icon" style="background:#fff8e6;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#f39c12" stroke-width="2" stroke-linecap="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="rp-stat-val">{{ $customRolesCount }}</div>
                <div class="rp-stat-lbl">Custom Roles</div>
                <div class="rp-stat-sub" style="color:#f39c12;">User-defined</div>
            </div>
        </div>
        <div class="rp-stat-card">
            <div class="rp-stat-icon" style="background:#eef0fb;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#5e72e4" stroke-width="2" stroke-linecap="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div>
                <div class="rp-stat-val">{{ $totalAssignedUsers }}</div>
                <div class="rp-stat-lbl">Assigned Users</div>
                <div class="rp-stat-sub" style="color:#5e72e4;">Across all roles</div>
            </div>
        </div>
        <div class="rp-stat-card">
            <div class="rp-stat-icon" style="background:#eef0fb;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#5e72e4" stroke-width="2" stroke-linecap="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
            </div>
            <div>
                <div class="rp-stat-val">{{ $totalPermGroups }}</div>
                <div class="rp-stat-lbl">Permission Groups</div>
                <div class="rp-stat-sub" style="color:#5e72e4;">System groups</div>
            </div>
        </div>
    </div>

    {{-- ── Tab Bar ── --}}
    <div class="rp-tabs-bar">
        <div class="rp-tab active" data-tab="all">All Roles ({{ $totalRolesCount }})</div>
        <div class="rp-tab" data-tab="system">System Roles ({{ $systemRolesCount }})</div>
        <div class="rp-tab" data-tab="custom">Custom Roles ({{ $customRolesCount }})</div>
    </div>

    {{-- ── Filter Row ── --}}
    <div class="rp-filter-row">
        <div class="rp-search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" id="roleSearch" placeholder="Search roles by name...">
        </div>
        <select class="rp-filter-select" id="filterRoleType">
            <option value="">Role Type</option>
            <option value="system">System Role</option>
            <option value="custom">Custom Role</option>
        </select>
        <select class="rp-filter-select" id="filterAccessLevel">
            <option value="">Access Level</option>
            <option value="badge-high">High Access</option>
            <option value="badge-medium">Medium Access</option>
            <option value="badge-low">Low Access</option>
        </select>
        <span class="rp-filter-clear" id="clearFilters">Clear Filters</span>
        <span class="rp-sort-label">Sort by:</span>
        <select class="rp-sort-select" id="sortRoles">
            <option value="users">Most Users</option>
            <option value="name">Name A–Z</option>
            <option value="perms">Permissions</option>
        </select>
    </div>

    {{-- ── Grid + Detail Panel ── --}}
    <div class="rp-grid-wrapper">

        {{-- Cards ── --}}
        <div class="rp-cards-area">
            <div class="rp-cards-grid" id="rolesGrid">

                @if(!empty($roles))
                    <?php $cardIndex = 0; ?>
                    @foreach($roles as $rlist)
                    <?php
                        $roleId      = $rlist->id;
                        $uCount      = $allRoleStats[$roleId]['userCount'] ?? 0;
                        $grpCount    = $allRoleStats[$roleId]['grpCount']  ?? 0;
                        $palette     = $cardPalette[$cardIndex % count($cardPalette)];
                        $access      = rpAccessLevel($grpCount, $rlist->name ?? '');
                        $isCustom    = $rlist->manual_add;
                        $roleType    = $isCustom ? 'custom' : 'system';
                        $grpAll      = $rlist->grouped_permissions ?? [];
                        $grpKeys     = array_keys((array)$grpAll);
                        $grpShown    = array_slice($grpKeys, 0, 3);
                        $grpExtra    = count($grpKeys) > 3 ? count($grpKeys) - 3 : 0;

                        /* Build JSON for detail panel */
                        $detailJson = json_encode([
                            'id'          => $roleId,
                            'name'        => ucwords($rlist->display_name),
                            'isCustom'    => $isCustom,
                            'grpCount'    => $grpCount,
                            'userCount'   => $uCount,
                            'access'      => $access,
                            'groups'      => $grpKeys,
                            'palette'     => $palette,
                        ]);
                        $cardIndex++;
                    ?>
                    <div class="rp-role-card"
                         data-role-type="{{ $roleType }}"
                         data-role-name="{{ strtolower(ucwords($rlist->display_name)) }}"
                         data-user-count="{{ $uCount }}"
                         data-grp-count="{{ $grpCount }}"
                         data-access-level="{{ $access['class'] }}"
                         data-role-detail='{{ $detailJson }}'
                         onclick="openRoleDetail(this)">

                        <div class="rp-card-top">
                            <div class="rp-card-icon-wrap">
                                <div class="rp-card-icon" style="background:{{ $palette['bg'] }};">
                                    {!! rpCardIcon($palette['icon'], $palette['stroke']) !!}
                                </div>
                                <div>
                                    <div class="rp-card-role-name">{{ ucwords($rlist->display_name) }}</div>
                                    <div class="rp-card-role-type {{ $isCustom ? 'custom' : '' }}">
                                        {{ $isCustom ? 'Custom Role' : 'System Role' }}
                                    </div>
                                </div>
                            </div>
                            <span class="access-badge {{ $access['class'] }}">{{ $access['label'] }}</span>
                        </div>

                        <div class="rp-card-counts">
                            <div>
                                <div class="rp-count-val">{{ $grpCount }}</div>
                                <div class="rp-count-lbl">Permission Groups</div>
                            </div>
                            <div>
                                <div class="rp-count-val">{{ $uCount }}</div>
                                <div class="rp-count-lbl">{{ $uCount == 1 ? 'User' : 'Users' }}</div>
                            </div>
                        </div>

                        @if(count($grpShown) > 0)
                        <div class="rp-perm-label">Permission Groups</div>
                        <div class="rp-perm-tags">
                            @foreach($grpShown as $gk)
                            <span class="rp-perm-tag">{{ ucwords($gk) }}</span>
                            @endforeach
                            @if($grpExtra > 0)
                            <span class="rp-perm-tag">+{{ $grpExtra }} more</span>
                            @endif
                        </div>
                        @endif

                        <div class="rp-users-row">
                            <span class="rp-users-label">Users</span>
                            @if($uCount > 0)
                            <div class="rp-avatars-stack">
                                @for($ai = 0; $ai < min($uCount, 4); $ai++)
                                <div class="rp-mini-av" style="background:{{ $avColors[$ai % count($avColors)] }};">
                                    {{ chr(65 + $ai) }}
                                </div>
                                @endfor
                            </div>
                            @if($uCount > 4)
                            <span class="rp-more-users">+{{ $uCount - 4 }} more</span>
                            @endif
                            @else
                            <span style="font-size:11px;color:#b0bbd8;">No users assigned</span>
                            @endif
                        </div>

                        <div class="rp-card-actions" onclick="event.stopPropagation()">
                            <div class="rp-action-group">
                                <a href="{{ url('/service_company/settings/show-role/'.$roleId) }}"
                                   class="rp-action-btn" title="View Role" onclick="event.stopPropagation()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                                @if(in_array("edit_roles_permission", $userPermissions))
                                @if(($rlist->priority != 0) && ($rlist->name != 'admin'))
                                <button class="rp-action-btn editRole" title="Edit Role"
                                        data-toggle="modal" data-target="#kt_modal_edit_role"
                                        data-id="{{ $roleId }}" onclick="event.stopPropagation()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                @endif
                                @endif
                                @if($rlist->manual_add)
                                <button class="rp-action-btn danger role_delete" title="Delete Role"
                                        data-id="{{ $roleId }}" onclick="event.stopPropagation()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6"/><path d="M14 11v6"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif

                {{-- Add New Role card --}}
                @if(in_array("add_roles_permission", $userPermissions))
                <div class="rp-add-card" id="addNewRole" data-toggle="modal" data-target="#kt_modal_add_role">
                    <div class="rp-add-plus">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                    <div class="rp-add-title">Add New Role</div>
                    <div class="rp-add-sub">Create a custom role with specific<br>permissions for your team.</div>
                </div>
                @endif

                <div class="rp-empty-state" id="rolesEmptyState">
                    <div class="rp-empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <div class="rp-empty-title">No roles found</div>
                    <div class="rp-empty-sub">Try adjusting your search or filters.</div>
                </div>

            </div>
        </div>

        {{-- ── Right Detail Panel ── --}}
        <div class="rp-detail-panel" id="roleDetailPanel">
            <div class="rp-detail-inner" id="roleDetailInner">
                {{-- Populated by JS --}}
            </div>
        </div>

    </div>
</div>

{{-- ── Add Role Modal ── --}}
<div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Add Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"/>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 my-7">
                <div class="stage_body_content" id="stage_body_content"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Edit Role Modal ── --}}
<div class="modal fade" id="kt_modal_edit_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Edit Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"/>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 my-7">
                <div class="edit_body_content" id="edit_body_content"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Legacy AJAX Modal ── --}}
<div class="modal fade bs-modal-md in" id="managePermissionModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">Loading...</div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}<?php echo TM_VERSION; ?>"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>
/* ── Existing permission assignment logic (unchanged) ── */
$(function () { $('.assign-role-permission').on('change', assignRollPermission); });

var animating = false;
var masteranimate = false;

var assignRollPermission = function () {
    var roleId = $(this).data('role-id');
    var permissionId = $(this).data('permission-id');
    var assignPermission = $(this).is(':checked') ? 'yes' : 'no';
    var url = '{{route('service_company.role-permission.store')}}';
    $.easyAjax({ url: url, type: "POST", data: { 'roleId': roleId, 'permissionId': permissionId, 'assignPermission': assignPermission, '_token': '{{ csrf_token() }}' } });
};

$('.assign-role-permission').change(assignRollPermission());

$('.select_all_permission').change(function () {
    var roleId = $(this).val();
    if ($(this).is(':checked')) {
        var url = '{{ route('service_company.role-permission.assignAllPermission') }}';
        $.easyAjax({ url: url, type: "POST", data: { 'roleId': roleId, '_token': '{{ csrf_token() }}' },
            success: function () {
                masteranimate = true;
                if (!animating) {
                    var masterStatus = true;
                    $('.assign-role-permission').off('change');
                    $('input.permission_'+roleId).each(function(index){
                        if ($('input.permission_'+roleId)[index].checked != masterStatus) $(this).trigger('click');
                    });
                    $('.assign-role-permission').on('change', assignRollPermission);
                }
                masteranimate = false;
            }
        });
    } else {
        var url = '{{ route('service_company.role-permission.removeAllPermission') }}';
        $.easyAjax({ url: url, type: "POST", data: { 'roleId': roleId, '_token': '{{ csrf_token() }}' },
            success: function () {
                masteranimate = true;
                if (!animating) {
                    var masterStatus = false;
                    $('.assign-role-permission').off('change');
                    $('input.permission_'+roleId).each(function(index){
                        if ($('input.permission_'+roleId)[index].checked != masterStatus) $(this).trigger('click');
                    });
                    $('.assign-role-permission').on('change', assignRollPermission);
                }
                masteranimate = false;
            }
        });
    }
});

$('.show-members').click(function () {
    var id = $(this).data('role-id');
    var url = '{{ route('service_company.role-permission.showMembers', ':id')}}';
    url = url.replace(':id', id);
    $('#modelHeading').html('Role Members');
    $.ajaxModal('#managePermissionModal', url);
});

/* ── Load role form into add modal ── */
$(document).ready(function () {

    $('#addNewRole, #addNewRoleBtn').on('click', function (e) {
        e.preventDefault();
        var url = "{{ route('service_company.settings.loadRole',':id') }}".replace(':id', 0);
        $('#stage_body_content').html('<div class="text-center py-10"><span class="spinner-border spinner-border-sm align-middle me-2"></span>Loading...</div>');
        $('#kt_modal_add_role').modal('show');
        $.ajax({ type: 'GET', url: url,
            success: function (response) {
                if (response.status === "success") {
                    $('#stage_body_content').html(response.view);
                } else {
                    $('#stage_body_content').html('<div class="text-center text-danger py-10">Unable to load the form. Please try again.</div>');
                }
            },
            error: function () {
                $('#stage_body_content').html('<div class="text-center text-danger py-10">Something went wrong while loading the form.</div>');
            }
        });
    });

    $(document).on('click', '.editRole', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var url = "{{ route('service_company.settings.loadEditRole',':id') }}".replace(':id', id);
        $('#edit_body_content').html('<div class="text-center py-10"><span class="spinner-border spinner-border-sm align-middle me-2"></span>Loading...</div>');
        $('#kt_modal_edit_role').modal('show');
        $.ajax({ type: 'GET', url: url,
            success: function (response) {
                if (response.status === "success") {
                    $('#edit_body_content').html(response.view);
                } else {
                    $('#edit_body_content').html('<div class="text-center text-danger py-10">Unable to load the form. Please try again.</div>');
                }
            },
            error: function () {
                $('#edit_body_content').html('<div class="text-center text-danger py-10">Something went wrong while loading the form.</div>');
            }
        });
    });

    /* ── Delete role ── */
    $(document).on('click', '.role_delete', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: "Are you sure?", text: "This role will be deleted permanently!",
            icon: "warning", showCancelButton: true,
            confirmButtonColor: "#d33", cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then(function (result) {
            if (result.isConfirmed) {
                var url = "{{ route('service_company.settings.manualDeleteRole',':id') }}".replace(':id', id);
                $.ajax({ type: 'GET', url: url,
                    success: function (response) {
                        if (response.status === "success") {
                            Swal.fire("Deleted", "Role has been deleted successfully", "success")
                                .then(function () { location.reload(); });
                        }
                    }
                });
            }
        });
    });

    /* ── Tab filtering ── */
    $('.rp-tab').on('click', function () {
        $('.rp-tab').removeClass('active');
        $(this).addClass('active');
        filterCards();
    });

    /* ── Search ── */
    $('#roleSearch').on('input', function () { filterCards(); });

    /* ── Role Type / Access Level dropdown filters ── */
    $('#filterRoleType, #filterAccessLevel').on('change', function () {
        $(this).toggleClass('is-active', $(this).val() !== '');
        filterCards();
    });

    /* ── Clear filters ── */
    $('#clearFilters').on('click', function () {
        $('#roleSearch').val('');
        $('#filterRoleType, #filterAccessLevel').val('').removeClass('is-active');
        $('.rp-tab').removeClass('active').filter('[data-tab="all"]').addClass('active');
        filterCards();
    });

    /* ── Sort ── */
    $('#sortRoles').on('change', function () {
        var sortBy = $(this).val();
        var grid = $('#rolesGrid');
        var cards = grid.find('.rp-role-card').toArray();
        cards.sort(function (a, b) {
            if (sortBy === 'name') return $(a).data('role-name').localeCompare($(b).data('role-name'));
            if (sortBy === 'users') return parseInt($(b).data('user-count')) - parseInt($(a).data('user-count'));
            if (sortBy === 'perms') return parseInt($(b).data('grp-count')) - parseInt($(a).data('grp-count'));
            return 0;
        });
        var addCard = grid.find('.rp-add-card').detach();
        grid.find('.rp-role-card').detach();
        var frag = document.createDocumentFragment();
        cards.forEach(function (c) { frag.appendChild(c); });
        grid.prepend(frag);
        grid.find('.rp-empty-state').before(addCard);
    });

    function filterCards() {
        var tab         = $('.rp-tab.active').data('tab');
        var search      = $('#roleSearch').val().toLowerCase().trim();
        var typeFilter  = $('#filterRoleType').val();
        var accessFilter = $('#filterAccessLevel').val();
        var visibleCount = 0;

        $('.rp-role-card').each(function () {
            var tabMatch    = (tab === 'all') || ($(this).data('role-type') === tab);
            var typeMatch   = !typeFilter || ($(this).data('role-type') === typeFilter);
            var accessMatch = !accessFilter || ($(this).data('access-level') === accessFilter);
            var searchMatch = !search || $(this).data('role-name').includes(search);
            var isMatch     = tabMatch && typeMatch && accessMatch && searchMatch;
            $(this).toggleClass('tab-hidden', !isMatch);
            if (isMatch) visibleCount++;
        });

        $('#rolesEmptyState').toggleClass('show', visibleCount === 0);
    }

    /* Apply default sort ("Most Users") on load so the dropdown reflects reality */
    $('#sortRoles').trigger('change');
});

/* ── Right detail panel ── */
var avColors = ['#5e72e4','#1a73e8','#27ae60','#e74c3c','#f39c12','#9b59b6','#1abc9c','#e91e8c'];

function openRoleDetail(el) {
    var data = JSON.parse(el.getAttribute('data-role-detail'));
    var panel = document.getElementById('roleDetailPanel');
    var inner = document.getElementById('roleDetailInner');

    /* Deselect previous */
    document.querySelectorAll('.rp-role-card').forEach(function (c) { c.classList.remove('selected'); });
    el.classList.add('selected');

    /* Build avatar stack */
    var avHtml = '';
    if (data.userCount > 0) {
        var shown = Math.min(data.userCount, 4);
        for (var i = 0; i < shown; i++) {
            avHtml += '<div class="rp-mini-av" style="background:'+avColors[i % avColors.length]+';">'+String.fromCharCode(65+i)+'</div>';
        }
        avHtml = '<div class="rp-avatars-stack">'+avHtml+'</div>';
        if (data.userCount > 4) avHtml += '<span class="rp-more-users">+' + (data.userCount - 4) + ' more</span>';
    }

    /* Build permission group list */
    var groupIcons = [
        {bg:'#fde8e8', stroke:'#c0392b'},
        {bg:'#e3f1ff', stroke:'#1a73e8'},
        {bg:'#e8f8f0', stroke:'#27ae60'},
        {bg:'#fff8e6', stroke:'#f39c12'},
        {bg:'#eef0fb', stroke:'#5e72e4'},
    ];
    var grpHtml = '';
    data.groups.slice(0, 8).forEach(function (g, idx) {
        var gi = groupIcons[idx % groupIcons.length];
        grpHtml += '<div class="rp-pgroup-item">' +
            '<div class="rp-pgroup-left">' +
                '<div class="rp-pgroup-icon" style="background:'+gi.bg+';">' +
                    '<svg viewBox="0 0 24 24" fill="none" stroke="'+gi.stroke+'" stroke-width="2" stroke-linecap="round" width="14" height="14"><circle cx="12" cy="12" r="3"/></svg>' +
                '</div>' +
                '<span class="rp-pgroup-name">'+ucwords(g)+'</span>' +
            '</div>' +
            '<div class="rp-pgroup-right">Permissions' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>' +
            '</div>' +
        '</div>';
    });

    /* Access badge */
    var badgeClass = data.access.class;
    var badgeLabel = data.access.label;
    var typeLabel  = data.isCustom ? 'Custom Role' : 'System Role';

    inner.innerHTML =
        '<div class="rp-detail-header">' +
            '<div>' +
                '<div class="rp-detail-name">'+data.name+'</div>' +
                '<div class="rp-detail-badges">' +
                    '<span style="background:#eef0fb;color:#5e72e4;font-size:10px;font-weight:600;padding:3px 9px;border-radius:5px;">'+typeLabel+'</span>' +
                    '<span class="access-badge '+badgeClass+'">'+badgeLabel+'</span>' +
                '</div>' +
            '</div>' +
            '<div class="rp-detail-ctrl">' +
                '<button class="rp-detail-ctrl-btn" onclick="closeRoleDetail()">' +
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
                '</button>' +
            '</div>' +
        '</div>' +

        '<div class="rp-detail-stat-tabs">' +
            '<div class="rp-dtab active"><span class="rp-dtab-val">'+data.grpCount+'</span>Perm Groups</div>' +
            '<div class="rp-dtab"><span class="rp-dtab-val">'+data.userCount+'</span>Users</div>' +
            '<div class="rp-dtab"><span class="rp-dtab-val">'+(data.isCustom ? 'Custom' : '100%')+'</span>Access</div>' +
        '</div>' +

        '<div class="rp-dsection">' +
            '<div class="rp-dsection-title">Description</div>' +
            '<div class="rp-ddesc">'+data.name+' role with '+badgeLabel.toLowerCase()+' to system features and '+data.grpCount+' permission group'+(data.grpCount===1?'':'s')+'.</div>' +
        '</div>' +

        (grpHtml ? '<div class="rp-dsection"><div class="rp-dsection-title">Permission Groups ('+data.groups.length+')</div>'+grpHtml+'</div>' : '') +

        '<div class="rp-dsection">' +
            '<div class="rp-dsection-title">Assigned Users ('+data.userCount+')</div>' +
            (data.userCount > 0
                ? '<div class="rp-user-item"><div class="rp-user-left">'+avHtml+'<div><div class="rp-user-name">'+data.userCount+' user'+(data.userCount===1?'':'s')+' assigned</div><div class="rp-user-email">Click "View Role" for full member list</div></div></div><div class="rp-status-indicator"><div class="rp-status-dot"></div><span class="rp-status-txt">Active</span></div></div>'
                : '<div style="font-size:11px;color:#b0bbd8;padding:6px 0;">No users assigned to this role.</div>'
            ) +
        '</div>' +

        '<div class="rp-dsection">' +
            '<div class="rp-dsection-title">Security &amp; Risk</div>' +
            '<div class="rp-security-row"><span class="rp-security-lbl">Access Level</span><span class="access-badge '+badgeClass+'">'+badgeLabel+'</span></div>' +
            '<div class="rp-security-row"><span class="rp-security-lbl">Role Type</span><span class="rp-security-val">'+typeLabel+'</span></div>' +
        '</div>';

    panel.classList.add('open');
}

function closeRoleDetail() {
    document.getElementById('roleDetailPanel').classList.remove('open');
    document.querySelectorAll('.rp-role-card').forEach(function (c) { c.classList.remove('selected'); });
}

function ucwords(str) {
    return str.replace(/\b\w/g, function (l) { return l.toUpperCase(); });
}

/* ── Switchery init ── */
$('.js-switch').each(function () { new Switchery($(this)[0], $(this).data()); });
</script>
@endpush
