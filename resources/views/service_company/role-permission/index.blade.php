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
#kt_toolbar, .toolbar { display: none !important; height: 0 !important; min-height: 0 !important; padding: 0 !important; margin: 0 !important; overflow: hidden !important; }
.post { padding: 0 !important; }
/* Zero the CSS variable Metronic reads for toolbar-height padding */
:root, body { --kt-toolbar-height: 0px !important; --kt-toolbar-height-tablet-and-mobile: 0px !important; }

/* ── Page wrapper ── */
.rp-page { padding: 18px 28px 24px; background: #f1f3f8; min-height: calc(100vh - 65px); margin-top: 57px; }

/* ── Page header card ── */
.rp-header-card {
    background: #fff; border: 1px solid #eaf0fb; border-radius: 12px;
    padding: 20px 22px; margin-bottom: 18px;
    display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
}
.rp-page-title { display: flex; align-items: center; gap: 8px; }
.rp-page-title h1 { font-size: 12px; font-weight: 700; color: #1e2640; margin: 0; line-height: 1.2; }
.rp-page-title .shield-icon { color: #5e72e4; }
.rp-page-subtitle { font-size: 12px; color: #8b93b8; margin: 4px 0 0; font-weight: 400; }
.rp-header-actions { display: flex; gap: 8px; align-items: center; flex-shrink: 0; }

/* ── Filter card ── */
.rp-filter-card {
    background: #fff; border: 1px solid #eaf0fb; border-radius: 12px;
    padding: 16px 18px; margin-bottom: 20px;
}

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
.rp-stat-val { font-size: 12px; font-weight: 700; color: #1e2640; line-height: 1; }
.rp-stat-lbl { font-size: 12px; color: #5a6080; font-weight: 500; margin-top: 2px; }
.rp-stat-sub { font-size: 11px; font-weight: 500; margin-top: 2px; }

/* ── Filter row ── */
.rp-filter-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
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
.rp-empty-title { font-size: 12px; font-weight: 600; color: #5a6080; }
.rp-empty-sub { font-size: 12px; color: #b0bbd8; }

/* ── Grid layout ── */
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
.rp-card-role-type { font-size: 12px; color: #8b93b8; font-weight: 400; margin-top: 1px; }
.rp-card-role-type.custom { color: #5e72e4; }

/* Access badges */
.access-badge {
    font-size: 12px; font-weight: 600; padding: 3px 10px;
    border-radius: 20px; white-space: nowrap; flex-shrink: 0;
}
.badge-high { background: #fde8e8; color: #c0392b; }
.badge-medium { background: #e3f1ff; color: #1a73e8; }
.badge-low { background: #e8f8f0; color: #27ae60; }

.rp-card-desc { font-size: 12px; color: #8b93b8; margin-bottom: 10px; line-height: 1.5; }

.rp-card-counts { display: flex; gap: 20px; margin-bottom: 10px; }
.rp-count-val { font-size: 12px; font-weight: 700; color: #1e2640; line-height: 1; }
.rp-count-lbl { font-size: 12px; color: #b0bbd8; font-weight: 400; margin-top: 1px; }

.rp-perm-label { font-size: 12px; color: #b0bbd8; margin-bottom: 5px; font-weight: 500; }
.rp-perm-tags { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 10px; }
.rp-perm-tag {
    font-size: 12px; background: #f1f3fb; color: #5a6080;
    padding: 3px 8px; border-radius: 5px; font-weight: 500;
}

.rp-users-row { display: flex; align-items: center; gap: 6px; margin-bottom: 10px; }
.rp-users-label { font-size: 12px; color: #b0bbd8; font-weight: 500; }
.rp-avatars-stack { display: flex; }
.rp-mini-av {
    width: 22px; height: 22px; border-radius: 50%;
    border: 2px solid #fff; display: flex; align-items: center;
    justify-content: center; font-size: 8px; font-weight: 700;
    color: #fff; margin-left: -5px; flex-shrink: 0;
}
.rp-mini-av:first-child { margin-left: 0; }
.rp-more-users { font-size: 12px; color: #8b93b8; }

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

/* "Edit" text button (replaces the eye/pencil icons) — opens the same role form drawer */
.rp-edit-btn {
    display: inline-flex; align-items: center; gap: 5px;
    background: transparent; border: 1px solid #dde2f0; border-radius: 7px;
    padding: 5px 12px; font-size: 11px; font-weight: 600; color: #5e72e4;
    cursor: pointer; font-family: 'Poppins', sans-serif; transition: all .15s;
}
.rp-edit-btn:hover { background: #eef0fb; border-color: #c5ccee; }
.rp-edit-btn svg { width: 12px; height: 12px; }

/* "Delete" text button (replaces the trash icon) */
.rp-del-btn {
    display: inline-flex; align-items: center; gap: 5px;
    background: transparent; border: 1px solid #f5d5d0; border-radius: 7px;
    padding: 5px 12px; font-size: 11px; font-weight: 600; color: #c0392b;
    cursor: pointer; font-family: 'Poppins', sans-serif; transition: all .15s;
}
.rp-del-btn:hover { background: #fde8e8; border-color: #c0392b; }
.rp-del-btn svg { width: 12px; height: 12px; }

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
.rp-add-title { font-size: 12px; font-weight: 600; color: #5a6080; }
.rp-add-sub { font-size: 12px; color: #b0bbd8; text-align: center; line-height: 1.5; }

/* ── Hidden tab filter ── */
.rp-role-card.tab-hidden { display: none; }

/* ══════════════ Role Detail Drawer ══════════════ */
.rp-drawer-backdrop {
    position: fixed; inset: 0; background: rgba(20,26,46,.45);
    z-index: 1050; opacity: 0; visibility: hidden; transition: opacity .25s ease;
}
.rp-drawer-backdrop.open { opacity: 1; visibility: visible; }

.rp-drawer {
    position: fixed; top: 0; right: -440px; width: 420px; max-width: 92vw; height: 100vh;
    background: #fff; z-index: 1060; box-shadow: -10px 0 32px rgba(20,26,46,.16);
    transition: right .3s ease; display: flex; flex-direction: column;
    font-family: 'Poppins', sans-serif;
}
.rp-drawer.open { right: 0; }

.rp-drawer-head { padding: 18px 20px 0; flex-shrink: 0; }
.rp-drawer-head-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
.rp-drawer-title-wrap { display: flex; align-items: center; gap: 10px; min-width: 0; }
.rp-drawer-icon {
    width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.rp-drawer-icon svg { width: 20px; height: 20px; }
.rp-drawer-title-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.rp-drawer-title { font-size: 12px; font-weight: 700; color: #1e2640; margin: 0; }
.rp-drawer-meta { font-size: 11px; color: #8b93b8; margin-top: 3px; }
.rp-drawer-head-actions { display: flex; gap: 4px; flex-shrink: 0; position: relative; }
.rp-drawer-icon-btn {
    width: 28px; height: 28px; border-radius: 7px; display: flex;
    align-items: center; justify-content: center; color: #8b93b8;
    cursor: pointer; background: transparent; border: none; flex-shrink: 0;
}
.rp-drawer-icon-btn:hover { background: #f1f3f8; color: #5a6080; }
.rp-drawer-icon-btn svg { width: 15px; height: 15px; }

.rp-drawer-menu {
    position: absolute; top: 34px; right: 0; background: #fff; border: 1px solid #eaf0fb;
    border-radius: 8px; box-shadow: 0 8px 24px rgba(20,26,46,.12); min-width: 150px;
    padding: 6px; display: none; z-index: 5;
}
.rp-drawer-menu.open { display: block; }
.rp-drawer-menu-item {
    display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 6px;
    font-size: 12px; color: #5a6080; cursor: pointer; white-space: nowrap;
}
.rp-drawer-menu-item svg { width: 13px; height: 13px; flex-shrink: 0; }
.rp-drawer-menu-item:hover { background: #f1f3f8; }
.rp-drawer-menu-item.danger { color: #c0392b; }
.rp-drawer-menu-item.danger:hover { background: #fde8e8; }

.rp-drawer-tabs { display: flex; gap: 16px; padding: 14px 20px 0; border-bottom: 1px solid #f1f3f8; flex-shrink: 0; }
.rp-drawer-tab {
    padding: 0 0 10px; font-size: 12px; font-weight: 500; color: #8b93b8; cursor: pointer;
    border-bottom: 2px solid transparent; margin-bottom: -1px; user-select: none;
}
.rp-drawer-tab.active { color: #5e72e4; font-weight: 600; border-bottom-color: #5e72e4; }

.rp-drawer-body { flex: 1; overflow-y: auto; padding: 18px 20px; }
.rp-drawer-pane { display: none; }
.rp-drawer-pane.active { display: block; }

.rp-drawer-footer {
    padding: 14px 20px; border-top: 1px solid #f1f3f8; display: flex; gap: 8px; flex-shrink: 0;
}
.rp-drawer-footer .rp-dbtn { flex: 1; justify-content: center; }

.rp-dbtn {
    display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;
    padding: 9px 12px; font-size: 12px; font-weight: 600; cursor: pointer;
    border: 1px solid #dde2f0; background: #fff; color: #5a6080;
    font-family: 'Poppins', sans-serif; transition: border-color .15s, background .15s;
}
.rp-dbtn svg { width: 13px; height: 13px; }
.rp-dbtn:hover { border-color: #c5ccee; background: #f8f9fc; }
.rp-dbtn.danger { color: #c0392b; border-color: #f5d5d0; }
.rp-dbtn.danger:hover { background: #fde8e8; border-color: #c0392b; }

/* metric mini-cards */
.rp-dmetrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 18px; }
.rp-dmetric { background: #f8f9fc; border-radius: 10px; padding: 12px 8px; text-align: center; }
.rp-dmetric-icon {
    width: 26px; height: 26px; border-radius: 7px; margin: 0 auto 6px;
    display: flex; align-items: center; justify-content: center;
}
.rp-dmetric-icon svg { width: 14px; height: 14px; }
.rp-dmetric-val { font-size: 12px; font-weight: 700; color: #1e2640; line-height: 1.2; }
.rp-dmetric-lbl { font-size: 9.5px; color: #8b93b8; margin-top: 2px; }

/* Info / coverage cards */
.rp-dcard { background: #f8f9fc; border-radius: 10px; padding: 14px; margin-bottom: 14px; }
.rp-dcard-title { font-size: 12px; font-weight: 600; color: #1e2640; margin: 0 0 12px; }
.rp-dinfo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 14px; }
.rp-dinfo-lbl { font-size: 10px; color: #8b93b8; }
.rp-dinfo-val { font-size: 11.5px; font-weight: 600; color: #1e2640; margin-top: 3px; }

.rp-dcov-row { margin-bottom: 12px; }
.rp-dcov-row:last-child { margin-bottom: 0; }
.rp-dcov-top { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px; }
.rp-dcov-name { font-size: 11px; color: #5a6080; font-weight: 500; }
.rp-dcov-frac { font-size: 10.5px; color: #8b93b8; font-weight: 600; }
.rp-dcov-track { height: 6px; border-radius: 4px; background: #e8eaf2; overflow: hidden; }
.rp-dcov-fill { height: 100%; border-radius: 4px; }

.rp-view-all-link {
    display: inline-flex; align-items: center; gap: 4px; font-size: 11.5px;
    color: #5e72e4; font-weight: 600; cursor: pointer; margin-bottom: 14px;
}
.rp-view-all-link svg { width: 12px; height: 12px; }

.rp-dwarning {
    background: #fff8ec; border: 1px solid #ffe3ad; border-radius: 10px;
    padding: 12px 14px; display: flex; gap: 10px; align-items: flex-start;
}
.rp-dwarning svg { color: #f39c12; flex-shrink: 0; margin-top: 1px; width: 16px; height: 16px; }
.rp-dwarning-title { font-size: 12px; font-weight: 700; color: #8a5a00; margin: 0 0 3px; }
.rp-dwarning-text { font-size: 11px; color: #a3762b; line-height: 1.5; margin: 0; }

/* Permissions tab */
.rp-dperm-group { margin-bottom: 16px; }
.rp-dperm-group:last-child { margin-bottom: 0; }
.rp-dperm-group-title {
    font-size: 10.5px; font-weight: 600; color: #8b93b8; text-transform: uppercase;
    letter-spacing: .3px; margin: 0 0 8px;
}
.rp-dperm-item {
    display: flex; align-items: center; gap: 8px; padding: 7px 0;
    border-bottom: 1px solid #f5f6fa; font-size: 12px; color: #1e2640;
}
.rp-dperm-item:last-child { border-bottom: none; }
.rp-dperm-item svg { width: 13px; height: 13px; color: #27ae60; flex-shrink: 0; }

/* Users tab */
.rp-duser-item { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid #f5f6fa; }
.rp-duser-item:last-child { border-bottom: none; }
.rp-duser-av {
    width: 32px; height: 32px; border-radius: 8px; display: flex;
    align-items: center; justify-content: center; color: #fff; font-weight: 700;
    font-size: 12px; flex-shrink: 0;
}
.rp-duser-name { font-size: 12px; font-weight: 600; color: #1e2640; }
.rp-duser-email { font-size: 10.5px; color: #8b93b8; }

.rp-dempty { font-size: 11.5px; color: #b0bbd8; text-align: center; padding: 24px 8px; }

/* ══════════════ Add / Edit Role FORM Drawer (screenshot-2 styled, real-data driven) ══════════════ */
/* Guard against any padding/border pushing an element past its container's width —
   the #1 cause of a stray horizontal scrollbar inside the drawer. */
#roleFormBody, #roleFormBody * { box-sizing: border-box; }

/* The backdrop/drawer only cover the MAIN content area, not the fixed left sidebar
   (#kt_aside) — JS sets --rpf-aside-w from the sidebar's real rendered width at open
   time, so this stays correct whether the sidebar is expanded or minimized. */
:root { --rpf-aside-w: 0px; }
.rpf-backdrop {
    position: fixed; top: 0; right: 0; bottom: 0; left: var(--rpf-aside-w);
    background: rgba(20,26,46,.45);
    z-index: 1070; opacity: 0; visibility: hidden; transition: opacity .25s ease, left .2s ease;
}
.rpf-backdrop.open { opacity: 1; visibility: visible; }

.rpf-drawer {
    position: fixed; top: 0; right: 0; bottom: 0; left: var(--rpf-aside-w); height: 100vh;
    background: #fff; z-index: 1080; box-shadow: -12px 0 40px rgba(20,26,46,.20);
    transform: translateX(100%);
    transition: transform .32s cubic-bezier(.4,0,.2,1), left .2s ease;
    display: flex; flex-direction: column; overflow: hidden;
    font-family: 'Poppins', sans-serif;
}
.rpf-drawer.open { transform: translateX(0); }

/* Content inside the drawer stays centered and capped so the layout doesn't over-stretch
   on large/4K monitors, while still filling 15.6" laptop widths (1366–1920px) */
.rpf-container { max-width: 1600px; margin: 0 auto; width: 100%; box-sizing: border-box; }

/* Loading / error states */
.rpf-loading { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; color: #8b93b8; font-size: 12px; }
.rpf-spinner { width: 26px; height: 26px; border: 3px solid #e3e7f3; border-top-color: #5e72e4; border-radius: 50%; animation: rpf-spin .7s linear infinite; }
@keyframes rpf-spin { to { transform: rotate(360deg); } }

/* Form shell — the drawer, its slot, and the injected form are all flex columns with
   min-height:0 all the way down, so .rpf-body's overflow-y:auto actually gets a bounded
   height to scroll within instead of the form silently growing past the viewport. */
.rpf-form-slot { flex: 1; display: flex; flex-direction: column; min-height: 0; width: 100%; }
.rpf-form { display: flex; flex-direction: column; flex: 1; min-height: 0; width: 100%; }

/* Header — full-bleed white bar, content centered/capped via .rpf-container */
.rpf-head { background: #fff; padding: 14px 0; border-bottom: 1px solid #eaf0fb; flex-shrink: 0; }
.rpf-head-inner { display: flex; align-items: center; gap: 12px; padding: 0 24px; }
.rpf-head-icon { width: 40px; height: 40px; border-radius: 11px; background: #eef0fb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rpf-head-icon svg { width: 20px; height: 20px; color: #5e72e4; }
.rpf-head-titles { min-width: 0; flex: 1; }
.rpf-head-title-row { display: flex; align-items: center; gap: 8px; }
.rpf-head-title { font-size: 12px; font-weight: 700; color: #1e2640; margin: 0; }
.rpf-badge-active { font-size: 9.5px; font-weight: 600; color: #27ae60; background: #e8f8f0; border-radius: 20px; padding: 2px 9px 2px 7px; display: inline-flex; align-items: center; gap: 4px; }
.rpf-badge-active::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: #27ae60; }
.rpf-head-sub { font-size: 11px; color: #8b93b8; margin: 2px 0 0; }
/* Action buttons live in the header (footer removed) */
.rpf-head-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.rpf-head-close { width: 30px; height: 30px; border-radius: 8px; border: none; background: transparent; color: #8b93b8; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rpf-head-close:hover { background: #f1f3f8; color: #5a6080; }
.rpf-head-close svg { width: 16px; height: 16px; }

/* Warning banner (edit only) — lives inside the scrollable body, above the summary card */
.rpf-warn { margin: 0 0 14px; background: #fff8ec; border: 1px solid #ffe3ad; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; }
.rpf-warn svg { width: 16px; height: 16px; color: #f39c12; flex-shrink: 0; }
.rpf-warn-text { font-size: 11.5px; color: #a3762b; }
.rpf-warn-text b { color: #8a5a00; font-weight: 600; }

/* Scroll body — the scroll container is full-width so the scrollbar sits flush at the
   drawer's edge; the actual content is centered/capped one level in via .rpf-body-inner */
.rpf-body { flex: 1; overflow-y: auto; min-height: 0; }
.rpf-body-inner { padding: 18px 24px 20px; }

/* Hero / summary card — single row: Role Name | Description | Assigned Users | Access Level | Permissions */
.rpf-summary { background: #fff; border: 1px solid #eaf0fb; border-radius: 12px; box-shadow: 0 1px 3px rgba(20,26,46,.05); padding: 14px 16px; margin-bottom: 14px; display: flex; align-items: flex-start; gap: 18px; }
.rpf-sum-col { min-width: 0; }
.rpf-sum-name  { flex: 1.6 1 160px; }
.rpf-sum-desc  { flex: 2 1 180px; }
.rpf-sum-users { flex: 1.1 1 120px; }
.rpf-sum-access{ flex: 0.9 1 90px; }
.rpf-sum-perms { flex: 1.5 1 150px; }
.rpf-sum-col + .rpf-sum-col { border-left: 1px solid #f1f3f8; padding-left: 18px; }
.rpf-field-label { font-size: 10px; font-weight: 600; color: #8b93b8; text-transform: uppercase; letter-spacing: .3px; margin-bottom: 5px; display: block; }
.rpf-field-label .req { color: #e74c3c; }
.rpf-input { width: 100%; border: 1px solid #dde2f0; border-radius: 8px; padding: 9px 12px; font-size: 12px; color: #1e2640; font-family: 'Poppins', sans-serif; background: #f8f9fc; outline: none; transition: border-color .15s, background .15s; }
.rpf-input:focus { border-color: #5e72e4; background: #fff; }
.rpf-input[readonly] { color: #8b93b8; cursor: not-allowed; background: #f1f3f8; }
.rpf-lock-hint { display: flex; align-items: center; gap: 4px; margin-top: 4px; font-size: 9.5px; color: #a0a7c0; }
.rpf-lock-hint svg { width: 11px; height: 11px; flex-shrink: 0; }
.rpf-summary-users { display: flex; align-items: center; gap: 6px; }
.rpf-uav { width: 26px; height: 26px; border-radius: 50%; border: 2px solid #fff; margin-left: -6px; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #fff; }
.rpf-uav:first-child { margin-left: 0; }
.rpf-users-count { font-size: 11px; color: #5a6080; font-weight: 500; }
.rpf-access-pill { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.rpf-access-pill::before { content:''; width:6px; height:6px; border-radius:50%; background: currentColor; }
.rpf-prog-top { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px; }
.rpf-prog-frac { font-size: 12px; font-weight: 700; color: #1e2640; }
.rpf-prog-frac span { color: #8b93b8; font-weight: 500; }
.rpf-prog-pct { font-size: 11px; font-weight: 600; color: #5e72e4; }
.rpf-prog-track { height: 6px; border-radius: 4px; background: #e8eaf2; overflow: hidden; }
.rpf-prog-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg,#5e72e4,#7b8cf0); transition: width .3s ease; }

/* Tabs */
.rpf-tabs { display: flex; gap: 18px; border-bottom: 1px solid #eaf0fb; margin-bottom: 14px; }
.rpf-tab { padding: 0 0 10px; font-size: 12px; font-weight: 500; color: #8b93b8; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; user-select: none; }
.rpf-tab.active { color: #5e72e4; font-weight: 600; border-bottom-color: #5e72e4; }

/* Quick actions + toolbar */
.rpf-toolbar { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; }
.rpf-quick-label { font-size: 11px; font-weight: 600; color: #5a6080; margin-right: 2px; }
.rpf-qbtn { background: #fff; border: 1px solid #dde2f0; border-radius: 7px; padding: 6px 11px; font-size: 11px; font-weight: 500; color: #5a6080; cursor: pointer; font-family: 'Poppins', sans-serif; transition: all .15s; }
.rpf-qbtn:hover { border-color: #5e72e4; color: #5e72e4; }
.rpf-qbtn.primary { background: #eef0fb; border-color: #d4d9f5; color: #5e72e4; font-weight: 600; }
.rpf-search { flex: 1 1 150px; display: flex; align-items: center; gap: 7px; background: #fff; border: 1px solid #dde2f0; border-radius: 7px; padding: 6px 11px; min-width: 130px; }
.rpf-search svg { width: 13px; height: 13px; color: #b0bbd8; flex-shrink: 0; }
.rpf-search input { border: none; outline: none; background: transparent; font-size: 11.5px; color: #1e2640; font-family: 'Poppins', sans-serif; width: 100%; }

/* Two-column grid: modules + aside rail */
.rpf-grid { display: flex; gap: 14px; align-items: flex-start; }
.rpf-main { flex: 1 1 auto; min-width: 0; }
.rpf-aside { flex: 0 0 236px; display: flex; flex-direction: column; gap: 12px; }

/* ══ Fixed-column permission table: Permission Groups | Add | Edit | View ══
   Card + subcard treatment on a plain white canvas: the header bar and each section
   are white "cards", and every permission group inside a section is its own white
   "subcard" — separation comes from border + soft shadow only, not background tint. */
.rpf-ptable { display: flex; flex-direction: column; gap: 12px; }

.rpf-ptable-head, .rpf-row { display: grid; grid-template-columns: 1fr 88px 88px 88px; align-items: center; }

.rpf-ptable-head {
    position: sticky; top: 0; z-index: 3; background: #fff;
    border: 1px solid #eaf0fb; border-radius: 10px; box-shadow: 0 1px 3px rgba(20,26,46,.05);
}
.rpf-th { padding: 10px 14px; font-size: 10.5px; font-weight: 700; color: #8b93b8; text-transform: uppercase; letter-spacing: .3px; }
.rpf-th-action { display: flex; flex-direction: column; align-items: center; gap: 3px; text-align: center; }
.rpf-th-action svg { width: 14px; height: 14px; color: #5e72e4; }

/* Section card */
.rpf-sec-group {
    background: #fff; border: 1px solid #eaf0fb; border-radius: 12px;
    box-shadow: 0 1px 3px rgba(20,26,46,.05); overflow: hidden;
}
.rpf-sec-line { display: flex; align-items: center; gap: 10px; padding: 12px 14px; cursor: pointer; user-select: none; }
.rpf-sec-line:hover { background: #fafbff; }
.rpf-sec-group:not(.collapsed) .rpf-sec-line { border-bottom: 1px solid #f1f3f8; }
.rpf-sec-icon { width: 30px; height: 30px; border-radius: 8px; background: #eef0fb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rpf-sec-icon svg { width: 15px; height: 15px; color: #5e72e4; }
.rpf-sec-names { flex: 1; display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.rpf-sec-title { font-size: 12px; font-weight: 700; color: #1e2640; }
.rpf-sec-subtitle { font-size: 10.5px; color: #8b93b8; }
.rpf-sec-count { background: #eef0fb; color: #5e72e4; font-size: 9.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px; flex-shrink: 0; }
.rpf-sec-caret { width: 14px; height: 14px; color: #b0bbd8; transition: transform .2s; flex-shrink: 0; }
.rpf-sec-group.collapsed .rpf-sec-caret { transform: rotate(-90deg); }
.rpf-sec-group.collapsed .rpf-sec-rows { display: none; }

/* Rows container inside a section card, holding one subcard per permission group */
.rpf-sec-rows { padding: 10px 12px 12px; display: flex; flex-direction: column; gap: 8px; background: #fafbfd; }

/* Subcard — one per permission group (e.g. "Clients") */
.rpf-row {
    background: #fff; border: 1px solid #eef0f6; border-radius: 9px;
    padding: 9px 12px; box-shadow: 0 1px 2px rgba(20,26,46,.04);
}
.rpf-row-label { font-size: 11.5px; color: #5a6080; font-weight: 500; padding-left: 4px; }
.rpf-row-cell { display: flex; align-items: center; justify-content: center; }

/* Checkbox */
.rpf-chk { display: inline-flex; align-items: center; gap: 7px; cursor: pointer; user-select: none; }
.rpf-chk input { position: absolute; opacity: 0; width: 0; height: 0; }
.rpf-chk-box { width: 17px; height: 17px; border-radius: 5px; border: 1.5px solid #cdd4e8; background: #fff; flex-shrink: 0; transition: all .15s; background-repeat: no-repeat; background-position: center; background-size: 11px; }
.rpf-chk input:checked + .rpf-chk-box { background-color: #5e72e4; border-color: #5e72e4; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E"); }
.rpf-chk input:disabled + .rpf-chk-box { background-color: #eef0f5; border-color: #e0e4ee; cursor: not-allowed; }
.rpf-chk input:disabled ~ .rpf-chk-lbl { color: #b8bfd2; }
.rpf-chk-lbl { font-size: 11.5px; color: #5a6080; font-weight: 500; }
.rpf-chk-center { margin: 0; }

/* Radio chip */
.rpf-radio { display: inline-flex; align-items: center; gap: 7px; cursor: pointer; user-select: none; }
.rpf-radio input { position: absolute; opacity: 0; width: 0; height: 0; }
.rpf-radio-dot { width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #cdd4e8; background: #fff; flex-shrink: 0; transition: all .15s; position: relative; }
.rpf-radio input:checked + .rpf-radio-dot { border-color: #5e72e4; }
.rpf-radio input:checked + .rpf-radio-dot::after { content: ''; position: absolute; inset: 3px; border-radius: 50%; background: #5e72e4; }
.rpf-radio input:disabled + .rpf-radio-dot { background: #eef0f5; border-color: #e0e4ee; }
.rpf-radio input:disabled ~ .rpf-radio-lbl { color: #b8bfd2; }
.rpf-radio-lbl { font-size: 11.5px; color: #5a6080; font-weight: 500; }
.rpf-radio-lbl small { display: block; font-size: 9.5px; color: #b0bbd8; font-weight: 400; }

/* Aside cards */
.rpf-acard { background: #fff; border: 1px solid #eaf0fb; border-radius: 11px; box-shadow: 0 1px 3px rgba(20,26,46,.05); padding: 14px; }
.rpf-acard-title { font-size: 11.5px; font-weight: 700; color: #1e2640; margin: 0 0 12px; display: flex; align-items: center; gap: 6px; }
.rpf-acard-title svg { width: 14px; height: 14px; color: #5e72e4; }

/* Donut */
.rpf-donut-wrap { display: flex; align-items: center; gap: 12px; }
.rpf-donut { width: 84px; height: 84px; flex-shrink: 0; position: relative; }
.rpf-donut svg { transform: rotate(-90deg); }
.rpf-donut-center { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.rpf-donut-num { font-size: 12px; font-weight: 700; color: #1e2640; line-height: 1; }
.rpf-donut-cap { font-size: 8px; color: #8b93b8; margin-top: 1px; }
.rpf-legend { flex: 1; display: flex; flex-direction: column; gap: 6px; }
.rpf-legend-row { display: flex; align-items: center; gap: 6px; font-size: 10.5px; color: #5a6080; }
.rpf-legend-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.rpf-legend-val { margin-left: auto; font-weight: 700; color: #1e2640; }

/* Scope + advanced rows */
.rpf-scope-row { display: flex; align-items: center; gap: 9px; padding: 8px 0; border-bottom: 1px solid #f5f6fa; }
.rpf-scope-row:last-child { border-bottom: none; }
.rpf-adv-row { display: flex; align-items: center; gap: 9px; padding: 8px 0; border-bottom: 1px solid #f5f6fa; }
.rpf-adv-row:last-child { border-bottom: none; }
.rpf-adv-row .rpf-chk { width: 100%; }
.rpf-adv-grid { display: flex; flex-direction: column; }

/* Footer — full-bleed white bar, content centered/capped via .rpf-container */
.rpf-footer { background: #fff; border-top: 1px solid #eaf0fb; padding: 12px 0; flex-shrink: 0; }
.rpf-footer-inner { display: flex; align-items: center; gap: 8px; padding: 0 24px; }
.rpf-footer .rpf-foot-spacer { flex: 1; font-size: 10.5px; color: #b0bbd8; text-align: center; }
.rpf-fbtn { display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; padding: 9px 16px; font-size: 12px; font-weight: 600; cursor: pointer; border: 1px solid #dde2f0; background: #fff; color: #5a6080; font-family: 'Poppins', sans-serif; transition: all .15s; }
.rpf-fbtn:hover { border-color: #c5ccee; background: #f8f9fc; }
.rpf-fbtn svg { width: 13px; height: 13px; }
.rpf-fbtn.primary { background: #5e72e4; border-color: #5e72e4; color: #fff; }
.rpf-fbtn.primary:hover { background: #4c5fd5; border-color: #4c5fd5; }
.rpf-fbtn.danger { color: #c0392b; border-color: #f5d5d0; }
.rpf-fbtn.danger:hover { background: #fde8e8; border-color: #c0392b; }
.rpf-fbtn .indicator-progress { display: none; }
.rpf-fbtn.loading .indicator-label { display: none; }
.rpf-fbtn.loading .indicator-progress { display: inline-flex; align-items: center; gap: 6px; }

/* ══════════════ Responsive breakpoints (page + drawer) ══════════════
   Tuned so the whole redesign — cards grid, stats row, and the full-page
   Add/Edit drawer — fits a 15.6" laptop (1366×768 / 1536×864 / 1920×1080)
   without horizontal scroll, then degrades gracefully to tablet/mobile. */

/* ── 15.6" laptop range: tighten the drawer's two-column grid slightly ── */
@media (max-width: 1440px) {
    .rpf-aside { flex-basis: 216px; }
    .rpf-grid { gap: 12px; }
    .rp-cards-grid { grid-template-columns: repeat(3, 1fr); }
}

/* ── Small laptop / large tablet: drop stats to 3-up, stack drawer aside below main ── */
@media (max-width: 1180px) {
    .rp-stats { grid-template-columns: repeat(3, 1fr); }
    .rpf-grid { flex-direction: column; }
    .rpf-aside { flex-basis: auto; width: 100%; flex-direction: row; flex-wrap: wrap; }
    .rpf-aside > .rpf-acard { flex: 1 1 240px; }
    /* Hero row wraps to two lines but stays a "row" layout */
    .rpf-summary { flex-wrap: wrap; }
    .rpf-summary .rpf-sum-col + .rpf-sum-col { border-left: none; padding-left: 0; }
}

/* ── Tablet ── */
@media (max-width: 992px) {
    .rp-cards-grid { grid-template-columns: repeat(2, 1fr); }
    .rp-page { padding: 14px 18px 20px; }
    .rpf-head-inner, .rpf-body-inner { padding-left: 16px; padding-right: 16px; }
    .rpf-ptable-head, .rpf-row { grid-template-columns: 1fr 64px 64px 64px; }
    .rpf-row-label { padding-left: 24px; }
}

/* ── Mobile ── */
@media (max-width: 640px) {
    .rp-header-card { flex-direction: column; align-items: stretch; }
    .rp-stats { grid-template-columns: repeat(2, 1fr); }
    .rp-cards-grid { grid-template-columns: 1fr; }
    .rp-filter-row { align-items: stretch; }
    .rp-search-box { flex-basis: auto; }
    .rp-sort-label { margin-left: 0; }
    .rp-drawer { width: 100%; max-width: 100vw; }

    .rpf-grid { flex-direction: column; }
    .rpf-aside { flex-basis: auto; width: 100%; flex-direction: row; flex-wrap: wrap; }
    .rpf-aside > .rpf-acard { flex: 1 1 200px; }
    .rpf-summary { flex-direction: column; gap: 12px; }
    .rpf-summary .rpf-sum-col + .rpf-sum-col { border-left: none; padding-left: 0; }
    .rpf-head-inner, .rpf-body-inner { padding-left: 14px; padding-right: 14px; }
    .rpf-head-inner { flex-wrap: wrap; }
    .rpf-head-actions { order: 3; width: 100%; }
    .rpf-toolbar { gap: 6px; }
    .rpf-ptable-head, .rpf-row { grid-template-columns: 1fr 48px 48px 48px; }
    .rpf-th-action { font-size: 9px; }
    .rpf-row-label { padding-left: 16px; font-size: 11px; }
    .rpf-sec-subtitle { display: none; }
}

@media (max-width: 420px) {
    .rp-stats { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<?php
/* ── Real permission display names (name -> display_name), fetched once for the drawer's Permissions tab ── */
$permDisplayMap = DB::table('permissions')->pluck('display_name', 'name');

/* ── Permission Coverage buckets: maps the 19 real permission group_names onto 4 display
   categories (approximation agreed with product owner — DB has no such grouping natively) ── */
$coverageBuckets = [
    'Recruitment'        => ['Jobs', 'Job Applications', 'Offer', 'Joining', 'Schedule', 'Requisition', 'Team On Requisition'],
    'Talent Marketplace' => ['Bench', 'Staffing Partners'],
    'Sales & CRM'        => ['Clients', 'Team', 'Reports'],
    'Admin & Security'   => ['Roles & Permissions', 'Manage Settings', 'Settings', 'Website', 'Subscription', 'Salary', 'Salary Break'],
];
$bucketColors = [
    'Recruitment'        => '#27ae60',
    'Talent Marketplace' => '#1a73e8',
    'Sales & CRM'        => '#5e72e4',
    'Admin & Security'   => '#c0392b',
];

/* ── Total assignable permissions per group_name, company-wide (permissions table is global, not tenant-scoped) ── */
$excludedPermIds = [58, 59, 14, 15, 16, 17];
$groupTotals = DB::table('permissions')
    ->where('status', 1)
    ->whereNotIn('id', $excludedPermIds)
    ->select('group_name', DB::raw('count(*) as cnt'))
    ->groupBy('group_name')
    ->pluck('cnt', 'group_name');

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

        $roleUsers = DB::table('role_user')
            ->join('users', 'users.id', '=', 'role_user.user_id')
            ->where('role_user.role_id', $roleId)
            ->where('users.type', 0)
            ->select('users.id', 'users.name', 'users.email')
            ->get();
        $uCount = $roleUsers->count();

        $totalAssignedUsers += $uCount;
        $grpAllForStats = $rlist->grouped_permissions ?? [];
        $grpCount = count($grpAllForStats);
        $totalPermGroups += $grpCount;

        $totalAssignedPerms = 0;
        foreach ($grpAllForStats as $permNames) {
            $totalAssignedPerms += count($permNames);
        }

        $coverage = [];
        foreach ($coverageBuckets as $bucketName => $groupNames) {
            $assigned = 0;
            $total = 0;
            foreach ($groupNames as $gn) {
                $assigned += isset($grpAllForStats[$gn]) ? count($grpAllForStats[$gn]) : 0;
                $total += $groupTotals[$gn] ?? 0;
            }
            $coverage[$bucketName] = [
                'assigned' => $assigned,
                'total'    => $total,
                'pct'      => $total > 0 ? round(($assigned / $total) * 100) : 0,
                'color'    => $bucketColors[$bucketName],
            ];
        }

        if ($rlist->manual_add) {
            $customRolesCount++;
        } else {
            $systemRolesCount++;
        }

        $allRoleStats[$roleId] = [
            'userCount'          => $uCount,
            'users'              => $roleUsers,
            'grpCount'           => $grpCount,
            'totalAssignedPerms' => $totalAssignedPerms,
            'coverage'           => $coverage,
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

{{-- Kept empty/hidden: Metronic's layout JS reads #kt_toolbar offsetHeight to set content top-padding.
     height:0 + overflow:hidden ensures JS measures 0 and adds no gap above rp-page. --}}
<div class="toolbar" id="kt_toolbar" style="height:0;overflow:hidden;padding:0;margin:0;border:none;">
    <div id="kt_toolbar_container" class="container-fluid"></div>
</div>

<div class="rp-page">

    {{-- ── Page Header Card ── --}}
    <div class="rp-header-card">
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
            <button class="btn-rp-primary" id="addNewRoleBtn">
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

    {{-- ── Filter Card ── --}}
    <div class="rp-filter-card">
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
    </div>

    {{-- ── Role Cards ── --}}
    <div class="rp-cards-grid" id="rolesGrid">

                @if(!empty($roles))
                    <?php $cardIndex = 0; ?>
                    @foreach($roles as $rlist)
                    <?php
                        $roleId      = $rlist->id;
                        $uCount      = $allRoleStats[$roleId]['userCount'] ?? 0;
                        $roleUsers   = $allRoleStats[$roleId]['users'] ?? collect();
                        $grpCount    = $allRoleStats[$roleId]['grpCount']  ?? 0;
                        $totalAssignedPerms = $allRoleStats[$roleId]['totalAssignedPerms'] ?? 0;
                        $coverage    = $allRoleStats[$roleId]['coverage'] ?? [];
                        $palette     = $cardPalette[$cardIndex % count($cardPalette)];
                        $access      = rpAccessLevel($grpCount, $rlist->name ?? '');
                        $isCustom    = $rlist->manual_add;
                        $roleType    = $isCustom ? 'custom' : 'system';
                        $grpAll      = $rlist->grouped_permissions ?? [];
                        /* grouped_permissions is a Collection — use its real group-name keys, not a
                           (array) cast (which would expose the Collection's internal *items props) */
                        $grpKeys     = $grpAll instanceof \Illuminate\Support\Collection ? $grpAll->keys()->all() : array_keys((array) $grpAll);
                        $grpShown    = array_slice($grpKeys, 0, 3);
                        $grpExtra    = count($grpKeys) > 3 ? count($grpKeys) - 3 : 0;
                        $canEdit     = in_array("edit_roles_permission", $userPermissions) && ($rlist->priority != 0) && ($rlist->name != 'admin');
                        $canDelete   = (bool) $rlist->manual_add;
                        $roleCode    = ($isCustom ? 'CUS-' : 'SYS-') . str_pad($roleId, 2, '0', STR_PAD_LEFT);

                        /* Flatten assigned permissions grouped by real group_name, with display names, for the drawer's Permissions tab */
                        $permissionsFlat = [];
                        foreach ($grpAll as $gn => $permNames) {
                            $items = [];
                            foreach ($permNames as $permName) {
                                $items[] = $permDisplayMap[$permName] ?? ucwords(str_replace(['_', '-'], ' ', $permName));
                            }
                            $permissionsFlat[] = ['group' => $gn, 'items' => $items];
                        }

                        /* Build JSON for the detail drawer (all data embedded up-front — no AJAX needed to open it) */
                        $detailJson = json_encode([
                            'id'          => $roleId,
                            'name'        => ucwords($rlist->display_name),
                            'roleCode'    => $roleCode,
                            'isCustom'    => $isCustom,
                            'grpCount'    => $grpCount,
                            'totalPerms'  => $totalAssignedPerms,
                            'userCount'   => $uCount,
                            'access'      => $access,
                            'coverage'    => $coverage,
                            'permissions' => $permissionsFlat,
                            'users'       => $roleUsers->map(fn($u) => ['name' => $u->name, 'email' => $u->email])->values(),
                            'createdOn'   => $rlist->created_at ? \Carbon\Carbon::parse($rlist->created_at)->format('d M, Y') : null,
                            'updatedOn'   => $rlist->updated_at ? \Carbon\Carbon::parse($rlist->updated_at)->format('d M, Y') : null,
                            'palette'     => $palette,
                            'canEdit'     => $canEdit,
                            'canDelete'   => $canDelete,
                        ]);
                        $cardIndex++;
                    ?>
                    <div class="rp-role-card"
                         data-role-type="{{ $roleType }}"
                         data-role-name="{{ strtolower(ucwords($rlist->display_name)) }}"
                         data-user-count="{{ $uCount }}"
                         data-grp-count="{{ $grpCount }}"
                         data-access-level="{{ $access['class'] }}"
                         data-role-id="{{ $roleId }}"
                         data-role-detail='{{ $detailJson }}'
                         onclick="openRoleFormFromCard(this)">

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
                            <span class="rp-perm-tag">{{ \App\Helper\GeneralMethod::roleGroupLabel($gk) }}</span>
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
                            <span style="font-size:12px;color:#b0bbd8;">No users assigned</span>
                            @endif
                        </div>

                        <div class="rp-card-actions" onclick="event.stopPropagation()">
                            <div class="rp-action-group">
                                <button type="button" class="rp-edit-btn" onclick="event.stopPropagation(); openRoleFormFromCard(this.closest('.rp-role-card'));">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Edit
                                </button>
                                @if($rlist->manual_add)
                                <button class="rp-del-btn role_delete" data-id="{{ $roleId }}" onclick="event.stopPropagation()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6"/><path d="M14 11v6"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                    </svg>
                                    Delete
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif

                {{-- Add New Role card --}}
                @if(in_array("add_roles_permission", $userPermissions))
                <div class="rp-add-card" id="addNewRole">
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

{{-- ── Role Detail Drawer ── --}}
<div class="rp-drawer-backdrop" id="roleDrawerBackdrop" onclick="closeRoleDrawer()"></div>
<div class="rp-drawer" id="roleDrawer">
    <div class="rp-drawer-head">
        <div class="rp-drawer-head-top">
            <div class="rp-drawer-title-wrap">
                <div class="rp-drawer-icon" id="drawerIcon"></div>
                <div>
                    <div class="rp-drawer-title-row">
                        <h3 class="rp-drawer-title" id="drawerTitle"></h3>
                        <span class="access-badge" id="drawerAccessBadge"></span>
                    </div>
                    <div class="rp-drawer-meta" id="drawerMeta"></div>
                </div>
            </div>
            <div class="rp-drawer-head-actions">
                <button type="button" class="rp-drawer-icon-btn" id="drawerMenuBtn" onclick="toggleDrawerMenu(event)" aria-label="More actions">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                </button>
                <div class="rp-drawer-menu" id="drawerMenu"></div>
                <button type="button" class="rp-drawer-icon-btn" onclick="closeRoleDrawer()" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>
        <div class="rp-drawer-tabs">
            <div class="rp-drawer-tab active" data-pane="overview" onclick="switchDrawerTab('overview')">Overview</div>
            <div class="rp-drawer-tab" data-pane="permissions" onclick="switchDrawerTab('permissions')" id="drawerTabPermissions">Permissions</div>
            <div class="rp-drawer-tab" data-pane="users" onclick="switchDrawerTab('users')" id="drawerTabUsers">Users</div>
        </div>
    </div>

    <div class="rp-drawer-body">
        <div class="rp-drawer-pane active" id="drawerPaneOverview"></div>
        <div class="rp-drawer-pane" id="drawerPanePermissions"></div>
        <div class="rp-drawer-pane" id="drawerPaneUsers"></div>
    </div>

    <div class="rp-drawer-footer" id="drawerFooter"></div>
</div>

{{-- ── Add / Edit Role Form Drawer (single wide right drawer, opened for both Add New Role and Edit) ── --}}
<div class="rpf-backdrop" id="roleFormBackdrop" onclick="closeRoleForm()"></div>
<div class="rpf-drawer" id="roleFormDrawer" aria-hidden="true">
    {{-- AJAX injects rolelist (Add) or loadEditRole (Edit) — both include the shared _role_form partial --}}
    <div class="rpf-form-slot" id="roleFormBody"></div>
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
/* Remove toolbar-height gap: Metronic JS sets inline padding-top on #kt_wrapper
   equal to --kt-toolbar-height (55px). Run immediately + after KTLayout fires. */
(function removeToolbarGap() {
    var targets = ['kt_wrapper', 'kt_content', 'kt_post'];
    function zeroGap() {
        targets.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) { el.style.setProperty('padding-top', '0', 'important'); el.style.setProperty('margin-top', '0', 'important'); }
        });
        document.body.style.setProperty('--kt-toolbar-height', '0px', 'important');
        document.body.style.setProperty('--kt-toolbar-height-tablet-and-mobile', '0px', 'important');
    }
    zeroGap();
    document.addEventListener('DOMContentLoaded', zeroGap);
    window.addEventListener('load', zeroGap);
    // Metronic reinitialises on resize — keep gap at 0
    window.addEventListener('resize', zeroGap);
})();

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

    /* ── Open the Add/Edit Role form drawer (same drawer for both) ── */
    $('#addNewRole, #addNewRoleBtn').on('click', function (e) {
        e.preventDefault();
        var url = "{{ route('service_company.settings.loadRole',':id') }}".replace(':id', 0);
        loadRoleFormDrawer(url);
    });

    $(document).on('click', '.editRole', function (e) {
        e.preventDefault();
        if (typeof closeRoleDrawer === 'function') closeRoleDrawer();
        var id = $(this).data('id');
        var url = "{{ route('service_company.settings.loadEditRole',':id') }}".replace(':id', id);
        loadRoleFormDrawer(url);
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
        var search      = $('#roleSearch').val().toLowerCase().trim();
        var typeFilter  = $('#filterRoleType').val();
        var accessFilter = $('#filterAccessLevel').val();
        var visibleCount = 0;

        $('.rp-role-card').each(function () {
            var typeMatch   = !typeFilter || ($(this).data('role-type') === typeFilter);
            var accessMatch = !accessFilter || ($(this).data('access-level') === accessFilter);
            var searchMatch = !search || $(this).data('role-name').includes(search);
            var isMatch     = typeMatch && accessMatch && searchMatch;
            $(this).toggleClass('tab-hidden', !isMatch);
            if (isMatch) visibleCount++;
        });

        $('#rolesEmptyState').toggleClass('show', visibleCount === 0);
    }

    /* Apply default sort ("Most Users") on load so the dropdown reflects reality */
    $('#sortRoles').trigger('change');
});

/* ── Role Detail Drawer ── */
var avColors = ['#5e72e4','#1a73e8','#27ae60','#e74c3c','#f39c12','#9b59b6','#1abc9c','#e91e8c'];
var currentDrawerRole = null;

function ucwords(str) {
    return str.replace(/\b\w/g, function (l) { return l.toUpperCase(); });
}

function esc(str) {
    return $('<div>').text(str == null ? '' : str).html();
}

function svgIcon(path, stroke) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="'+(stroke||'currentColor')+'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'+path+'</svg>';
}

var ICON_SHIELD_CHECK = '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>';
var ICON_USERS        = '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>';
var ICON_SHIELD       = '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>';
var ICON_CHECK        = '<polyline points="20 6 9 17 4 12"/>';
var ICON_ALERT        = '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>';
var ICON_EDIT         = '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>';
var ICON_COPY         = '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>';
var ICON_TRASH        = '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>';
var ICON_ARROW_RIGHT  = '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>';

function openRoleDrawer(el) {
    var data = JSON.parse(el.getAttribute('data-role-detail'));
    currentDrawerRole = data;

    document.querySelectorAll('.rp-role-card').forEach(function (c) { c.classList.remove('selected'); });
    el.classList.add('selected');

    var badgeClass = data.access.class;
    var badgeLabel = data.access.label;
    var typeLabel  = data.isCustom ? 'Custom Role' : 'System Role';

    /* Header */
    document.getElementById('drawerIcon').style.background = data.palette.bg;
    document.getElementById('drawerIcon').innerHTML = svgIcon(ICON_SHIELD_CHECK, data.palette.stroke);
    document.getElementById('drawerTitle').textContent = data.name;
    document.getElementById('drawerAccessBadge').className = 'access-badge ' + badgeClass;
    document.getElementById('drawerAccessBadge').textContent = badgeLabel;
    document.getElementById('drawerMeta').textContent = typeLabel + ' • ID: ' + data.roleCode;
    document.getElementById('drawerTabPermissions').textContent = 'Permissions (' + data.totalPerms + ')';
    document.getElementById('drawerTabUsers').textContent = 'Users (' + data.userCount + ')';

    /* Head menu (Edit / Duplicate / Delete shortcuts) */
    var menuHtml = '';
    if (data.canEdit) {
        menuHtml += '<div class="rp-drawer-menu-item editRole" data-id="'+data.id+'" onclick="closeDrawerMenu()">'+svgIcon(ICON_EDIT)+' Edit Role</div>';
    }
    menuHtml += '<div class="rp-drawer-menu-item" onclick="duplicateRoleNotice(); closeDrawerMenu();">'+svgIcon(ICON_COPY)+' Duplicate Role</div>';
    if (data.canDelete) {
        menuHtml += '<div class="rp-drawer-menu-item danger role_delete" data-id="'+data.id+'" onclick="closeDrawerMenu()">'+svgIcon(ICON_TRASH)+' Delete Role</div>';
    }
    document.getElementById('drawerMenu').innerHTML = menuHtml;
    closeDrawerMenu();

    /* Overview pane */
    var coverageHtml = '';
    Object.keys(data.coverage).forEach(function (bucketName) {
        var b = data.coverage[bucketName];
        coverageHtml +=
            '<div class="rp-dcov-row">' +
                '<div class="rp-dcov-top"><span class="rp-dcov-name">'+esc(bucketName)+'</span><span class="rp-dcov-frac">'+b.assigned+'/'+b.total+'</span></div>' +
                '<div class="rp-dcov-track"><div class="rp-dcov-fill" style="width:'+b.pct+'%;background:'+b.color+';"></div></div>' +
            '</div>';
    });

    var warningHtml = '';
    if (badgeClass === 'badge-high') {
        warningHtml =
            '<div class="rp-dwarning">' +
                svgIcon(ICON_ALERT, '#f39c12') +
                '<div>' +
                    '<p class="rp-dwarning-title">High Access Role</p>' +
                    '<p class="rp-dwarning-text">This role has access to sensitive modules. Changes will affect ' + data.userCount + ' user' + (data.userCount === 1 ? '' : 's') + '.</p>' +
                '</div>' +
            '</div>';
    }

    document.getElementById('drawerPaneOverview').innerHTML =
        '<div class="rp-dmetrics">' +
            '<div class="rp-dmetric"><div class="rp-dmetric-icon" style="background:#eef0fb;">'+svgIcon(ICON_USERS,'#5e72e4')+'</div><div class="rp-dmetric-val">'+data.grpCount+'</div><div class="rp-dmetric-lbl">Permission Groups</div></div>' +
            '<div class="rp-dmetric"><div class="rp-dmetric-icon" style="background:#e3f1ff;">'+svgIcon(ICON_USERS,'#1a73e8')+'</div><div class="rp-dmetric-val">'+data.userCount+'</div><div class="rp-dmetric-lbl">Assigned Users</div></div>' +
            '<div class="rp-dmetric"><div class="rp-dmetric-icon" style="background:#fff8e6;">'+svgIcon(ICON_SHIELD,'#f39c12')+'</div><div class="rp-dmetric-val">'+badgeLabel.replace(' Access','')+'</div><div class="rp-dmetric-lbl">Access Level</div></div>' +
        '</div>' +

        '<div class="rp-dcard">' +
            '<p class="rp-dcard-title">Role Information</p>' +
            '<div class="rp-dinfo-grid">' +
                '<div><div class="rp-dinfo-lbl">Role Type</div><div class="rp-dinfo-val">'+typeLabel+'</div></div>' +
                '<div><div class="rp-dinfo-lbl">Created On</div><div class="rp-dinfo-val">'+(data.createdOn || '—')+'</div></div>' +
                '<div><div class="rp-dinfo-lbl">Last Updated</div><div class="rp-dinfo-val">'+(data.updatedOn || '—')+'</div></div>' +
            '</div>' +
        '</div>' +

        '<div class="rp-dcard">' +
            '<p class="rp-dcard-title">Permission Coverage</p>' +
            coverageHtml +
        '</div>' +

        '<div class="rp-view-all-link" onclick="switchDrawerTab(\'permissions\')">View all permissions '+svgIcon(ICON_ARROW_RIGHT)+'</div>' +

        warningHtml;

    /* Permissions pane */
    var permsHtml = '';
    if (data.permissions.length > 0) {
        data.permissions.forEach(function (grp) {
            permsHtml += '<div class="rp-dperm-group"><p class="rp-dperm-group-title">'+esc(grp.group)+'</p>';
            grp.items.forEach(function (item) {
                permsHtml += '<div class="rp-dperm-item">'+svgIcon(ICON_CHECK)+' '+esc(item)+'</div>';
            });
            permsHtml += '</div>';
        });
    } else {
        permsHtml = '<div class="rp-dempty">No permissions assigned to this role.</div>';
    }
    document.getElementById('drawerPanePermissions').innerHTML = permsHtml;

    /* Users pane */
    var usersHtml = '';
    if (data.users.length > 0) {
        data.users.forEach(function (u, idx) {
            var initial = u.name ? u.name.charAt(0).toUpperCase() : '?';
            usersHtml +=
                '<div class="rp-duser-item">' +
                    '<div class="rp-duser-av" style="background:'+avColors[idx % avColors.length]+';">'+initial+'</div>' +
                    '<div><div class="rp-duser-name">'+esc(u.name)+'</div><div class="rp-duser-email">'+esc(u.email)+'</div></div>' +
                '</div>';
        });
    } else {
        usersHtml = '<div class="rp-dempty">No users assigned to this role.</div>';
    }
    document.getElementById('drawerPaneUsers').innerHTML = usersHtml;

    /* Footer actions */
    var footerHtml = '';
    if (data.canEdit) {
        footerHtml += '<button type="button" class="rp-dbtn editRole" data-id="'+data.id+'">'+svgIcon(ICON_EDIT)+' Edit Role</button>';
    }
    footerHtml += '<button type="button" class="rp-dbtn" onclick="duplicateRoleNotice()">'+svgIcon(ICON_COPY)+' Duplicate Role</button>';
    if (data.canDelete) {
        footerHtml += '<button type="button" class="rp-dbtn danger role_delete" data-id="'+data.id+'">'+svgIcon(ICON_TRASH)+' Delete Role</button>';
    }
    document.getElementById('drawerFooter').innerHTML = footerHtml;

    switchDrawerTab('overview');
    document.getElementById('roleDrawer').classList.add('open');
    document.getElementById('roleDrawerBackdrop').classList.add('open');
}

function closeRoleDrawer() {
    document.getElementById('roleDrawer').classList.remove('open');
    document.getElementById('roleDrawerBackdrop').classList.remove('open');
    document.querySelectorAll('.rp-role-card').forEach(function (c) { c.classList.remove('selected'); });
    closeDrawerMenu();
}

function switchDrawerTab(pane) {
    document.querySelectorAll('.rp-drawer-tab').forEach(function (t) { t.classList.toggle('active', t.getAttribute('data-pane') === pane); });
    document.querySelectorAll('.rp-drawer-pane').forEach(function (p) { p.classList.remove('active'); });
    var map = { overview: 'drawerPaneOverview', permissions: 'drawerPanePermissions', users: 'drawerPaneUsers' };
    document.getElementById(map[pane]).classList.add('active');
}

function toggleDrawerMenu(e) {
    e.stopPropagation();
    document.getElementById('drawerMenu').classList.toggle('open');
}

function closeDrawerMenu() {
    document.getElementById('drawerMenu').classList.remove('open');
}

function duplicateRoleNotice() {
    Swal.fire({ icon: 'info', title: 'Coming soon', text: 'Duplicating roles isn\'t available yet.', confirmButtonColor: '#5e72e4' });
}

document.addEventListener('click', function (e) {
    var menu = document.getElementById('drawerMenu');
    var btn = document.getElementById('drawerMenuBtn');
    if (menu && menu.classList.contains('open') && !menu.contains(e.target) && e.target !== btn) {
        closeDrawerMenu();
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeRoleDrawer();
});

/* ══════════════ Add / Edit Role FORM Drawer ══════════════ */

/* Open the drawer and AJAX-load the shared role form (Add id=0, or Edit id) into it */
/* Clicking a role card (or its View button) on the main page now opens the SAME full-page
   Add-Role-style form drawer, pre-filled for that role (edit mode) — replacing the old
   read-only Overview drawer so Add and Edit share one identical design. */
function openRoleFormFromCard(card) {
    if (!card) return;
    var id = card.getAttribute('data-role-id');
    if (!id) return;
    var url = "{{ route('service_company.settings.loadEditRole', ':id') }}".replace(':id', id);
    loadRoleFormDrawer(url);
}

function loadRoleFormDrawer(url) {
    var $slot = $('#roleFormBody');
    $slot.html('<div class="rpf-loading"><div class="rpf-spinner"></div><span>Loading role form…</span></div>');
    openRoleForm();
    $.ajax({
        type: 'GET', url: url,
        success: function (response) {
            if (response && response.status === 'success') {
                $slot.html(response.view);
                initRoleFormDrawer();
            } else {
                $slot.html('<div class="rpf-loading" style="color:#c0392b;">Unable to load the form. Please try again.</div>');
            }
        },
        error: function () {
            $slot.html('<div class="rpf-loading" style="color:#c0392b;">Something went wrong while loading the form.</div>');
        }
    });
}

/* Measure the real rendered width of the fixed left sidebar (#kt_aside) so the drawer/backdrop
   start right after it instead of covering it — works whether the sidebar is expanded or
   minimized, and re-measures on resize so it never drifts stale while the drawer is open. */
function syncRoleFormAsideOffset() {
    var aside = document.getElementById('kt_aside');
    /* Below Metronic's own lg breakpoint the sidebar becomes an off-canvas overlay rather
       than a static column, so the drawer should take the full width in that case. */
    var isStaticSidebar = window.innerWidth >= 992;
    var w = (aside && isStaticSidebar && aside.getBoundingClientRect().left <= 0)
        ? aside.getBoundingClientRect().width : 0;
    document.documentElement.style.setProperty('--rpf-aside-w', w + 'px');
}

function openRoleForm() {
    syncRoleFormAsideOffset();
    document.getElementById('roleFormDrawer').classList.add('open');
    document.getElementById('roleFormBackdrop').classList.add('open');
    document.body.style.overflow = 'hidden';
    window.addEventListener('resize', syncRoleFormAsideOffset);
}

function closeRoleForm() {
    document.getElementById('roleFormDrawer').classList.remove('open');
    document.getElementById('roleFormBackdrop').classList.remove('open');
    document.body.style.overflow = '';
    window.removeEventListener('resize', syncRoleFormAsideOffset);
}

/* Wire up all interactions inside the freshly-injected form (tabs, quick actions, search,
   collapse, per-module + master toggles, live permission summary / donut / progress). */
function initRoleFormDrawer() {
    var root = document.getElementById('roleFormBody');
    if (!root) return;

    /* ── Collapse All / Expand All (per-section collapse is wired inline via onclick in the blade) ── */
    var collapseBtn = root.querySelector('[data-rpf="collapse"]');
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            var secs = root.querySelectorAll('.rpf-sec-group');
            var anyExpanded = root.querySelector('.rpf-sec-group:not(.collapsed)');
            secs.forEach(function (s) { s.classList.toggle('collapsed', !!anyExpanded); });
            collapseBtn.querySelector('.rpf-collapse-lbl').textContent = anyExpanded ? 'Expand All' : 'Collapse All';
        });
    }

    /* ── Tabs (All / Module / Advanced) ── */
    root.querySelectorAll('.rpf-tab').forEach(function (t) {
        t.addEventListener('click', function () {
            root.querySelectorAll('.rpf-tab').forEach(function (x) { x.classList.remove('active'); });
            t.classList.add('active');
            var view = t.getAttribute('data-tab');
            var main = root.querySelector('.rpf-main');
            var aside = root.querySelector('.rpf-aside');
            if (view === 'module') {
                if (main) main.style.display = '';
                root.querySelectorAll('.rpf-aside .rpf-acard').forEach(function (c, i) { c.style.display = (i === 0) ? '' : 'none'; });
            } else if (view === 'advanced') {
                if (main) main.style.display = 'none';
                root.querySelectorAll('.rpf-aside .rpf-acard').forEach(function (c, i) { c.style.display = (i === 0) ? 'none' : ''; });
            } else {
                if (main) main.style.display = '';
                root.querySelectorAll('.rpf-aside .rpf-acard').forEach(function (c) { c.style.display = ''; });
            }
        });
    });

    /* ── Search permissions ── */
    var search = root.querySelector('[data-rpf="search"]');
    if (search) {
        search.addEventListener('input', function () {
            var q = search.value.toLowerCase().trim();
            root.querySelectorAll('.rpf-sec-group').forEach(function (sec) {
                var secVisible = false;
                sec.querySelectorAll('.rpf-row').forEach(function (row) {
                    var name = (row.getAttribute('data-mod-name') || '').toLowerCase();
                    var hit = !q || name.indexOf(q) !== -1;
                    row.style.display = hit ? '' : 'none';
                    if (hit) secVisible = true;
                });
                sec.style.display = secVisible ? '' : 'none';
                if (q) sec.classList.remove('collapsed');
            });
        });
    }

    /* ── Quick actions: Select All / Clear All / View Only / Full Access ── */
    function setAllPerms(state) {
        root.querySelectorAll('input.rpf-perm-check:not(:disabled)').forEach(function (c) { c.checked = state; });
        recalcRoleSummary();
    }
    var qSelectAll = root.querySelector('[data-rpf="select-all"]');
    var qClearAll  = root.querySelector('[data-rpf="clear-all"]');
    var qViewOnly  = root.querySelector('[data-rpf="view-only"]');
    var qFull      = root.querySelector('[data-rpf="full-access"]');
    function setRadio(name, value) {
        var r = root.querySelector('input[name="' + name + '"][value="' + value + '"]');
        if (r) r.checked = true;
    }
    if (qSelectAll) qSelectAll.addEventListener('click', function () { setAllPerms(true); });
    if (qClearAll)  qClearAll.addEventListener('click', function () { setAllPerms(false); });
    if (qFull) qFull.addEventListener('click', function () {
        setRadio('all_requisition', '1');
        setRadio('add_team_on_requisition', '1');
        setAllPerms(true);
    });
    if (qViewOnly) qViewOnly.addEventListener('click', function () {
        root.querySelectorAll('input.rpf-perm-check:not(:disabled)').forEach(function (c) {
            var lbl = (c.getAttribute('data-action') || '').toLowerCase();
            c.checked = (lbl.indexOf('view') !== -1);
        });
        setRadio('all_requisition', '0');
        recalcRoleSummary();
    });

    /* ── Master "Administrator Access / Select all" ── */
    var master = root.querySelector('input.rpf-master');
    if (master) {
        master.addEventListener('change', function () { setAllPerms(master.checked); });
    }

    /* ── Recalc live summary on any permission change ── */
    root.querySelectorAll('input.rpf-perm-check, input[name="all_requisition"], input[name="add_team_on_requisition"]').forEach(function (c) {
        c.addEventListener('change', recalcRoleSummary);
    });

    recalcRoleSummary();

    function recalcRoleSummary() {
        var checks = root.querySelectorAll('input.rpf-perm-check');
        var total = 0, enabled = 0;
        checks.forEach(function (c) { total++; if (c.checked) enabled++; });
        /* Access-scope + add-team radios count as 2 tunable permissions (parity with backend 58/59) */
        var allReq = root.querySelector('input[name="all_requisition"][value="1"]');
        var addTeam = root.querySelector('input[name="add_team_on_requisition"][value="1"]');
        total += 2;
        if (allReq && allReq.checked) enabled++;
        if (addTeam && addTeam.checked) enabled++;

        var disabled = total - enabled;
        var pct = total ? Math.round((enabled / total) * 100) : 0;

        /* Partial = permission-group rows with some-but-not-all of Add/Edit/View checked */
        var partial = 0;
        root.querySelectorAll('.rpf-row').forEach(function (row) {
            var boxes = row.querySelectorAll('input.rpf-perm-check');
            if (!boxes.length) return;
            var on = 0; boxes.forEach(function (b) { if (b.checked) on++; });
            if (on > 0 && on < boxes.length) partial++;
        });

        /* Progress bar + fraction */
        setText('.rpf-prog-enabled', enabled);
        setText('.rpf-prog-total', total);
        setText('.rpf-prog-pct', pct + '%');
        var fill = root.querySelector('.rpf-prog-fill'); if (fill) fill.style.width = pct + '%';

        /* Donut */
        var circ = 2 * Math.PI * 34; /* r=34 */
        var ring = root.querySelector('.rpf-donut-ring');
        if (ring) { ring.style.strokeDasharray = circ; ring.style.strokeDashoffset = circ * (1 - enabled / (total || 1)); }
        setText('.rpf-donut-num', enabled);
        setText('.rpf-legend-enabled', enabled);
        setText('.rpf-legend-disabled', disabled);
        setText('.rpf-legend-partial', partial);

        /* Access level pill */
        var pill = root.querySelector('.rpf-access-pill');
        if (pill) {
            var lvl = pct >= 70 ? { t: 'High', c: '#c0392b', b: '#fde8e8' } : (pct >= 35 ? { t: 'Medium', c: '#e67e22', b: '#fff3e0' } : { t: 'Low', c: '#27ae60', b: '#e8f8f0' });
            pill.textContent = lvl.t; pill.style.color = lvl.c; pill.style.background = lvl.b;
        }

        /* Keep master checkbox in sync */
        if (master) master.checked = (enabled >= total && total > 0);
    }

    function setText(sel, val) { var el = root.querySelector(sel); if (el) el.textContent = val; }
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && document.getElementById('roleFormDrawer').classList.contains('open')) closeRoleForm();
});

/* ── Switchery init ── */
$('.js-switch').each(function () { new Switchery($(this)[0], $(this).data()); });
</script>
@endpush
