@extends('layouts.service_company_layout')
@php use App\Helper\GeneralMethod; @endphp

<style>
* { font-family:'Poppins',sans-serif; box-sizing:border-box; }

/* ── Page wrapper ── */
.cps-page { background:#F1F5F9; min-height:100vh; padding:28px 32px; }

/* ── Top bar ── */
.cps-topbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; }
.cps-title { font-size:20px; font-weight:700; color:#0F172A; margin:0; line-height:1.3; }
.cps-subtitle { font-size:13px; color:#64748B; margin:0 0 20px; }
.cps-visit-btn {
    display:inline-flex; align-items:center; gap:7px;
    padding:10px 22px; background:#2563EB; color:#fff !important;
    border-radius:9px; font-size:13px; font-weight:600;
    text-decoration:none; border:none; cursor:pointer; white-space:nowrap;
    box-shadow:0 2px 8px rgba(37,99,235,.25);
}
.cps-visit-btn:hover { background:#1D4ED8; color:#fff !important; }

/* ── Tab bar ── */
.cps-tab-bar {
    background:#fff; border-radius:12px 12px 0 0;
    border:1px solid #E2E8F0; border-bottom:none;
    display:flex; align-items:stretch; flex-wrap:nowrap; overflow-x:auto;
    padding:0 8px;
}
.cps-tab-bar::-webkit-scrollbar { height:0; }
.cps-tab {
    display:inline-flex; align-items:center; gap:6px;
    padding:14px 16px; font-size:12px; font-weight:600; color:#64748B;
    cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px;
    white-space:nowrap; transition:color .15s,border-color .15s; flex-shrink:0;
}
.cps-tab i { font-size:14px; }
.cps-tab:hover { color:#2563EB; }
.cps-tab.active { color:#2563EB; border-bottom-color:#2563EB; }

/* ── Tab content wrapper ── */
.cps-body {
    background:#fff; border:1px solid #E2E8F0; border-top:none;
    border-radius:0 0 12px 12px; padding:28px;
}

/* ── Section card ── */
.cps-section { border:1px solid #E2E8F0; border-radius:10px; padding:24px; margin-bottom:20px; background:#fff; }
.cps-section-title { font-size:15px; font-weight:700; color:#0F172A; margin:0 0 3px; }
.cps-section-sub   { font-size:12px; color:#94A3B8; margin:0 0 22px; }

.cps-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:20px 28px; }
@media(max-width:768px){ .cps-grid2 { grid-template-columns:1fr; } }

.cps-label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
.cps-hint  { font-size:11px; color:#94A3B8; margin-top:5px; }

.cps-input {
    width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px;
    padding:0 12px; font-size:13px; color:#0F172A; outline:none;
    font-family:'Poppins',sans-serif;
}
.cps-input:focus { border-color:#2563EB; }

.cps-select {
    width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px;
    padding:0 12px; font-size:13px; color:#0F172A; outline:none;
    font-family:'Poppins',sans-serif; background:#fff; appearance:none; cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 10px center;
}
.cps-select:focus { border-color:#2563EB; }

/* URL row */
.cps-url-wrap { display:flex; gap:8px; align-items:center; }
.cps-url-wrap .cps-input { flex:1; background:#F8FAFC; color:#64748B; }
.cps-copy-btn {
    height:40px; padding:0 18px; border:1.5px solid #2563EB; border-radius:8px;
    color:#2563EB; font-size:12px; font-weight:600; background:#fff; cursor:pointer;
    white-space:nowrap; font-family:'Poppins',sans-serif;
}
.cps-copy-btn:hover { background:#EFF6FF; }
.cps-url-ok { font-size:11px; color:#16A34A; display:flex; align-items:center; gap:4px; margin-top:6px; }

/* Toggle */
.cps-toggle-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
.cps-toggle-desc { font-size:12px; color:#64748B; line-height:1.5; }
.cps-switch { position:relative; display:inline-block; width:46px; height:25px; flex-shrink:0; margin-top:2px; }
.cps-switch input { opacity:0; width:0; height:0; }
.cps-slider { position:absolute; cursor:pointer; inset:0; background:#CBD5E1; border-radius:34px; transition:.25s; }
.cps-slider:before { position:absolute; content:''; height:19px; width:19px; left:3px; bottom:3px;
    background:#fff; border-radius:50%; transition:.25s; box-shadow:0 1px 4px rgba(0,0,0,.2); }
input:checked + .cps-slider { background:#2563EB; }
input:checked + .cps-slider:before { transform:translateX(21px); }

/* Color */
.cps-color-row { display:flex; align-items:center; gap:10px; }
.cps-color-swatch { width:38px; height:38px; border-radius:8px; border:1.5px solid #E2E8F0; cursor:pointer; flex-shrink:0; padding:2px; }

/* Logo */
.cps-logo-row { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
.cps-logo-img { width:80px; height:40px; object-fit:contain; border:1.5px solid #E2E8F0; border-radius:8px; padding:4px; background:#F8FAFC; }
.cps-logo-placeholder { width:80px; height:40px; background:#F1F5F9; border:1.5px solid #E2E8F0; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:9px; color:#94A3B8; }
.cps-logo-name { font-size:12px; font-weight:600; color:#374151; }
.cps-logo-meta { font-size:10px; color:#94A3B8; }
.cps-change-btn { height:34px; padding:0 14px; border:1.5px solid #E2E8F0; border-radius:8px;
    font-size:12px; font-weight:600; color:#374151; background:#fff; cursor:pointer; font-family:'Poppins',sans-serif; }

/* Layout cards */
.cps-layout-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
.cps-layout-card {
    border:1.5px solid #E2E8F0; border-radius:10px; padding:14px 10px;
    cursor:pointer; text-align:center; transition:all .15s; position:relative;
}
.cps-layout-card:hover { border-color:#93C5FD; }
.cps-layout-card.selected { border-color:#2563EB; background:#EFF6FF; }
.cps-lc-check {
    position:absolute; top:8px; right:8px; width:18px; height:18px;
    background:#2563EB; border-radius:50%; display:none; align-items:center; justify-content:center;
}
.cps-layout-card.selected .cps-lc-check { display:flex; }
.cps-lc-icon { width:38px; height:28px; background:#E2E8F0; border-radius:6px;
    margin:0 auto 8px; display:flex; align-items:center; justify-content:center; }
.cps-lc-name { font-size:12px; font-weight:700; color:#0F172A; }
.cps-lc-desc { font-size:10px; color:#2563EB; font-weight:500; margin-top:2px; }

/* Display cards */
.cps-display-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.cps-display-card {
    border:1.5px solid #E2E8F0; border-radius:10px; padding:14px;
    cursor:pointer; transition:all .15s; display:flex; align-items:center; gap:10px;
}
.cps-display-card:hover { border-color:#93C5FD; }
.cps-display-card.selected { border-color:#2563EB; background:#EFF6FF; }
.cps-dc-name { font-size:12px; font-weight:700; color:#0F172A; }
.cps-dc-desc { font-size:10px; color:#64748B; margin-top:2px; }

/* ── Right sidebar ── */
.cps-sidebar-card {
    background:#fff; border:1px solid #E2E8F0; border-radius:12px;
    padding:20px; margin-bottom:16px;
}
.cps-sidebar-card h4 { font-size:14px; font-weight:700; color:#0F172A; margin:0 0 3px; }
.cps-sidebar-card .sub { font-size:11px; color:#94A3B8; margin:0 0 14px; }

/* Preview box */
.cps-preview-box { border:1.5px solid #E2E8F0; border-radius:10px; overflow:hidden; }
.cps-prev-hdr { background:linear-gradient(135deg,#4F46E5 0%,#7C3AED 100%); padding:14px 12px; }
.cps-prev-hdr-title { font-size:11px; color:rgba(255,255,255,.8); margin:0 0 2px; }
.cps-prev-hdr-name  { font-size:13px; font-weight:700; color:#fff; margin:0 0 10px; }
.cps-prev-search { background:rgba(255,255,255,.92); border-radius:6px; padding:6px 10px; font-size:9px; color:#64748B; }
.cps-prev-body { background:#F8FAFC; padding:12px; }
.cps-prev-stats { display:flex; gap:16px; margin-bottom:10px; }
.cps-prev-stat-n { font-size:13px; font-weight:700; color:#0F172A; }
.cps-prev-stat-l { font-size:9px; color:#94A3B8; }
.cps-prev-positions { font-size:10px; font-weight:700; color:#374151; margin-bottom:8px; }
.cps-prev-job { background:#fff; border:1px solid #E2E8F0; border-radius:6px; padding:7px 9px; margin-bottom:6px; }
.cps-prev-job-t { font-size:9px; font-weight:700; color:#0F172A; }
.cps-prev-job-s { font-size:8px; color:#94A3B8; }
.cps-prev-footer { text-align:center; padding-top:6px; }
.cps-prev-footer a { font-size:10px; color:#2563EB; font-weight:600; text-decoration:none; }

/* Quick actions */
.cps-qa { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #F1F5F9; }
.cps-qa:last-child { border-bottom:none; padding-bottom:0; }
.cps-qa-left { display:flex; align-items:center; gap:10px; }
.cps-qa-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.cps-qa-title { font-size:12px; font-weight:600; color:#0F172A; }
.cps-qa-desc  { font-size:10px; color:#94A3B8; }

/* Save button */
.cps-save-btn {
    width:100%; padding:12px; background:#2563EB; color:#fff;
    border:none; border-radius:8px; font-size:13px; font-weight:600;
    cursor:pointer; font-family:'Poppins',sans-serif;
    display:flex; align-items:center; justify-content:center; gap:8px;
}
.cps-save-btn:hover { background:#1D4ED8; }

/* ── Tab panels ── */
.cps-panel { display:none; }
.cps-panel.active { display:block; }

/* ── Hero Section ── */
.hs-label { font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; display:block; }
.hs-hint  { font-size:11px; color:#94A3B8; margin-top:5px; }

/* Layout style cards */
.hs-layout-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:22px; }
.hs-layout-card {
    border:1.5px solid #E2E8F0; border-radius:10px; padding:10px 8px 8px;
    cursor:pointer; text-align:center; transition:all .15s; position:relative; background:#F8FAFC;
}
.hs-layout-card:hover { border-color:#93C5FD; }
.hs-layout-card.selected { border-color:#2563EB; background:#EFF6FF; }
.hs-lc-check {
    position:absolute; top:7px; right:7px; width:16px; height:16px;
    background:#2563EB; border-radius:50%; display:none; align-items:center; justify-content:center;
}
.hs-layout-card.selected .hs-lc-check { display:flex; }
.hs-lc-thumb {
    height:44px; background:#E2E8F0; border-radius:6px; margin-bottom:7px;
    display:flex; align-items:center; justify-content:center; overflow:hidden;
}
.hs-lc-name { font-size:10px; font-weight:600; color:#374151; line-height:1.3; }

/* Input / Textarea */
.hs-input {
    width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px;
    padding:0 12px; font-size:13px; color:#0F172A; outline:none;
    font-family:'Poppins',sans-serif;
}
.hs-input:focus { border-color:#2563EB; }
.hs-textarea {
    width:100%; border:1.5px solid #E2E8F0; border-radius:8px;
    padding:10px 12px; font-size:13px; color:#0F172A; outline:none; resize:vertical;
    font-family:'Poppins',sans-serif; min-height:80px;
}
.hs-textarea:focus { border-color:#2563EB; }
.hs-char-count { font-size:11px; color:#94A3B8; text-align:right; margin-top:3px; }

/* Highlight points tags */
.hs-tags-wrap { display:flex; flex-wrap:wrap; gap:8px; align-items:center; padding:10px 12px;
    border:1.5px solid #E2E8F0; border-radius:8px; min-height:42px; background:#fff; }
.hs-tag { display:inline-flex; align-items:center; gap:6px; padding:4px 10px;
    background:#EFF6FF; border:1px solid #BFDBFE; border-radius:20px;
    font-size:11px; font-weight:600; color:#1D4ED8; }
.hs-tag-icon { width:16px; height:16px; background:#DBEAFE; border-radius:50%;
    display:flex; align-items:center; justify-content:center; font-size:8px; }
.hs-tag-del { cursor:pointer; color:#93C5FD; font-size:13px; line-height:1; font-weight:700; }
.hs-tag-del:hover { color:#2563EB; }
.hs-add-point { display:inline-flex; align-items:center; gap:5px; padding:6px 14px;
    border:1.5px dashed #CBD5E1; border-radius:20px; font-size:11px; font-weight:600;
    color:#64748B; cursor:pointer; background:transparent; margin-top:8px;
    font-family:'Poppins',sans-serif; }
.hs-add-point:hover { border-color:#2563EB; color:#2563EB; }

/* CTA rows */
.hs-cta-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; align-items:end; }
.hs-cta-newtab { display:flex; align-items:center; gap:6px; font-size:12px; color:#64748B; margin-top:6px; cursor:pointer; }

/* Background style radios */
.hs-bg-options { display:flex; gap:20px; flex-wrap:wrap; margin-bottom:16px; }
.hs-bg-opt { display:flex; align-items:center; gap:6px; cursor:pointer; font-size:12px; font-weight:500; color:#374151; }
.hs-bg-opt input { accent-color:#2563EB; width:15px; height:15px; cursor:pointer; }

/* Gradient swatches */
.hs-grad-grid { display:flex; gap:8px; flex-wrap:wrap; }
.hs-grad-swatch {
    width:62px; height:44px; border-radius:8px; cursor:pointer;
    border:2px solid transparent; transition:all .15s; position:relative;
}
.hs-grad-swatch.selected { border-color:#2563EB; }
.hs-grad-swatch .hs-gs-check {
    position:absolute; top:4px; right:4px; width:14px; height:14px;
    background:#2563EB; border-radius:50%; display:none; align-items:center; justify-content:center;
}
.hs-grad-swatch.selected .hs-gs-check { display:flex; }
.hs-grad-custom { width:62px; height:44px; border-radius:8px; border:1.5px solid #E2E8F0;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    font-size:9px; color:#64748B; cursor:pointer; gap:2px; background:#F8FAFC; }

/* Hero live preview */
.hs-prev-wrap { border:1.5px solid #E2E8F0; border-radius:10px; overflow:hidden; }
.hs-prev-nav { background:#fff; padding:10px 14px; display:flex; align-items:center;
    justify-content:space-between; border-bottom:1px solid #F1F5F9; }
.hs-prev-nav-logo { font-size:11px; font-weight:700; color:#0F172A; }
.hs-prev-nav-links { display:flex; gap:10px; }
.hs-prev-nav-link { font-size:9px; color:#64748B; }
.hs-prev-nav-btn { font-size:9px; font-weight:600; color:#fff; background:#2563EB;
    padding:4px 10px; border-radius:5px; }
.hs-prev-hero { background:linear-gradient(135deg,#EEF2FF 0%,#E0E7FF 100%);
    padding:24px 20px; display:grid; grid-template-columns:1fr 1fr; gap:16px; align-items:center; min-height:200px; }
.hs-prev-badge { display:inline-flex; align-items:center; gap:4px; font-size:8px; font-weight:600;
    color:#2563EB; background:#EFF6FF; padding:3px 8px; border-radius:20px; margin-bottom:8px; }
.hs-prev-heading { font-size:14px; font-weight:800; color:#0F172A; line-height:1.3; margin-bottom:6px; }
.hs-prev-heading span { color:#2563EB; }
.hs-prev-desc { font-size:8px; color:#64748B; line-height:1.5; margin-bottom:10px; }
.hs-prev-highlights { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
.hs-prev-hl { display:flex; align-items:center; gap:4px; font-size:8px; color:#374151; }
.hs-prev-hl i { font-size:9px; color:#2563EB; }
.hs-prev-btns { display:flex; gap:6px; }
.hs-prev-btn-primary { font-size:8px; font-weight:700; color:#fff; background:#2563EB;
    padding:5px 12px; border-radius:5px; }
.hs-prev-btn-secondary { font-size:8px; font-weight:700; color:#374151;
    border:1px solid #E2E8F0; padding:5px 12px; border-radius:5px; background:#fff; }
.hs-prev-img { background:#C7D2FE; border-radius:8px; height:160px;
    display:flex; align-items:center; justify-content:center; overflow:hidden; }
.hs-prev-img img { width:100%; height:100%; object-fit:cover; border-radius:8px; }

/* Upload illustration */
.hs-upload-box { border:1.5px dashed #CBD5E1; border-radius:10px; padding:24px;
    text-align:center; cursor:pointer; background:#FAFBFC; transition:border-color .15s; }
.hs-upload-box:hover { border-color:#2563EB; }
.hs-upload-icon { font-size:28px; color:#94A3B8; margin-bottom:6px; }
.hs-upload-title { font-size:12px; font-weight:600; color:#374151; }
.hs-upload-meta  { font-size:11px; color:#94A3B8; margin-top:2px; }
.hs-illus-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-top:12px; }
.hs-illus-thumb { border:2px solid #E2E8F0; border-radius:8px; overflow:hidden;
    aspect-ratio:1; cursor:pointer; transition:border-color .15s; display:flex; align-items:center; justify-content:center; background:#F1F5F9; }
.hs-illus-thumb.selected { border-color:#2563EB; }
.hs-illus-thumb i { font-size:20px; color:#94A3B8; }

/* Tips */
.hs-tips { }
.hs-tip { display:flex; align-items:flex-start; gap:10px; padding:10px 0;
    border-bottom:1px solid #F1F5F9; font-size:12px; color:#374151; }
.hs-tip:last-child { border-bottom:none; }
.hs-tip-icon { width:24px; height:24px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:11px; }

/* Bottom action bar */
.hs-action-bar { display:flex; align-items:center; justify-content:space-between;
    padding:16px 24px; border-top:1px solid #E2E8F0; margin:-28px -28px -0px; margin-top:20px; }
.hs-reset-btn { padding:10px 20px; border:1.5px solid #E2E8F0; border-radius:8px;
    font-size:13px; font-weight:600; color:#374151; background:#fff; cursor:pointer;
    font-family:'Poppins',sans-serif; }
.hs-save-btn { padding:10px 22px; background:#2563EB; color:#fff; border:none;
    border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;
    font-family:'Poppins',sans-serif; display:inline-flex; align-items:center; gap:7px; }
.hs-save-btn:hover { background:#1D4ED8; }
</style>

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content" style="background:#fff;">
<div class="post d-flex flex-column-fluid" id="kt_post">
<div id="kt_content_container" class="container-fluid px-5 pt-4 pb-6" style="background:#fff;">

@php
    $company_id     = auth()->user()->company_id;
    $company        = $company ?? null;
    $careerSettings = $careerSettings ?? null;
    $companyName    = $company->company_name ?? auth()->user()->name ?? 'Company';
    $careerUrl      = url('career-jobs/' . str_replace(' ', '-', $companyName) . '/' . $company_id);
    $primaryColor   = $careerSettings->primary_color    ?? '#2563EB';
    $secondaryColor = $careerSettings->secondary_color  ?? '#10B981';
    $fontFamily     = $careerSettings->font_family      ?? 'Poppins';
    $pageLayout     = $careerSettings->page_layout      ?? 'modern';
    $jobsDisplay    = $careerSettings->jobs_display     ?? 'card';
    $isActive       = $careerSettings->career_page_status ?? 1;
@endphp

{{-- Top banner --}}
<div style="margin-top:-46px;margin-left:-20px;margin-right:-20px;margin-bottom:20px;
     background:linear-gradient(135deg,#1E3A8A 0%,#3B82F6 50%,#6366F1 100%);
     border-radius:0 0 16px 16px;padding:28px 32px 24px;position:relative;overflow:hidden;">

    {{-- decorative circles --}}
    <div style="position:absolute;top:-40px;right:120px;width:200px;height:200px;
         border-radius:50%;background:rgba(255,255,255,.06);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-60px;right:60px;width:160px;height:160px;
         border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
    <div style="position:absolute;top:10px;right:320px;width:80px;height:80px;
         border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>

    <div style="display:flex;align-items:center;justify-content:space-between;position:relative;z-index:1;">
        {{-- Left: icon + text --}}
        <div style="display:flex;align-items:center;gap:18px;">
            <div style="width:52px;height:52px;background:rgba(255,255,255,.18);border-radius:14px;
                 display:flex;align-items:center;justify-content:center;flex-shrink:0;
                 box-shadow:0 4px 14px rgba(0,0,0,.15);">
                <i class="ti ti-layout-navbar" style="font-size:26px;color:#fff;"></i>
            </div>
            <div>
                <div style="font-size:20px;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-.3px;">
                    Career Page Settings
                </div>
                <div style="font-size:12px;color:rgba(255,255,255,.75);margin-top:3px;">
                    Customize your career page appearance, content and preferences
                </div>
                {{-- Status badge --}}
                <div style="display:inline-flex;align-items:center;gap:5px;margin-top:8px;
                     padding:3px 10px;background:rgba(255,255,255,.15);border-radius:20px;
                     border:1px solid rgba(255,255,255,.25);">
                    <span style="width:7px;height:7px;border-radius:50%;background:#4ADE80;display:inline-block;"></span>
                    <span style="font-size:11px;font-weight:600;color:#fff;">Career Page Live</span>
                </div>
            </div>
        </div>

        {{-- Right: stats + button --}}
        <div style="display:flex;align-items:center;gap:28px;">
            <div style="display:flex;gap:24px;">
                <div style="text-align:center;">
                    <div style="font-size:22px;font-weight:800;color:#fff;line-height:1;">8</div>
                    <div style="font-size:10px;color:rgba(255,255,255,.7);margin-top:2px;">Sections</div>
                </div>
                <div style="width:1px;background:rgba(255,255,255,.2);"></div>
                <div style="text-align:center;">
                    <div style="font-size:22px;font-weight:800;color:#fff;line-height:1;">100%</div>
                    <div style="font-size:10px;color:rgba(255,255,255,.7);margin-top:2px;">Customizable</div>
                </div>
                <div style="width:1px;background:rgba(255,255,255,.2);"></div>
                <div style="text-align:center;">
                    <div style="font-size:22px;font-weight:800;color:#fff;line-height:1;">SEO</div>
                    <div style="font-size:10px;color:rgba(255,255,255,.7);margin-top:2px;">Optimized</div>
                </div>
            </div>
            <a href="{{ $careerUrl }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:7px;padding:11px 22px;
                      background:#fff;color:#2563EB;border-radius:10px;font-size:13px;
                      font-weight:700;text-decoration:none;white-space:nowrap;flex-shrink:0;
                      box-shadow:0 4px 16px rgba(0,0,0,.2);">
                <i class="ti ti-external-link" style="font-size:15px;"></i> Visit Career Page
            </a>
        </div>
    </div>
</div>

{{-- Tab bar --}}
<div class="cps-tab-bar">
    <div class="cps-tab active" data-tab="general">
        <i class="ti ti-settings-2"></i> General
    </div>
    <div class="cps-tab" data-tab="hero">
        <i class="ti ti-layout-navbar"></i> Hero Section
    </div>
    <div class="cps-tab" data-tab="company">
        <i class="ti ti-building"></i> Company Info
    </div>
    <div class="cps-tab" data-tab="benefits">
        <i class="ti ti-gift"></i> Benefits
    </div>
    <div class="cps-tab" data-tab="jobs">
        <i class="ti ti-briefcase"></i> Job Settings
    </div>
    <div class="cps-tab" data-tab="seo">
        <i class="ti ti-world"></i> SEO & Social
    </div>
    <div class="cps-tab" data-tab="integrations">
        <i class="ti ti-plug"></i> Integrations
    </div>
    <div class="cps-tab" data-tab="advanced">
        <i class="ti ti-adjustments-horizontal"></i> Advanced
    </div>
</div>

{{-- Tab body --}}
<div class="cps-body">
<form id="careerSettingsForm" method="POST"
      action="{{ route('service_company.career.page.settings.save') }}"
      enctype="multipart/form-data">
@csrf

{{-- ════════════════ GENERAL TAB ════════════════ --}}
<div class="cps-panel active" id="panel-general">
<div class="row g-4">
    {{-- ── Left: Main content ── --}}
    <div class="col-lg-8">

        {{-- General Settings --}}
        <div class="cps-section" id="tab-general">
            <div class="cps-section-title">General Settings</div>
            <div class="cps-section-sub">Manage your career page basic information and preferences.</div>

            <div class="cps-grid2">

                {{-- Career Page URL --}}
                <div>
                    <label class="cps-label">
                        Career Page URL
                        <i class="ti ti-info-circle" style="font-size:11px;color:#94A3B8;margin-left:3px;"></i>
                    </label>
                    <div class="cps-url-wrap">
                        <input type="text" class="cps-input" id="careerPageUrl"
                               value="{{ $careerUrl }}" readonly>
                        <button type="button" class="cps-copy-btn" onclick="copyCareerUrl()">Edit</button>
                    </div>
                    <div class="cps-url-ok">
                        <i class="ti ti-circle-check-filled" style="font-size:12px;"></i>
                        URL is available
                    </div>
                    <div class="cps-hint">Customize your career page URL</div>
                </div>

                {{-- Career Page Status --}}
                <div>
                    <label class="cps-label">Career Page Status</label>
                    <div class="cps-toggle-row" style="margin-top:4px;">
                        <div class="cps-toggle-desc">Your career page is live and visible to candidates.</div>
                        <label class="cps-switch">
                            <input type="checkbox" name="career_page_status" value="1"
                                   {{ $isActive ? 'checked' : '' }}>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Default Language --}}
                <div>
                    <label class="cps-label">Default Language</label>
                    <select class="cps-select" name="default_language">
                        <option value="en"  {{ ($careerSettings->default_language ?? 'en') == 'en'  ? 'selected':'' }}>English</option>
                        <option value="hi"  {{ ($careerSettings->default_language ?? '')   == 'hi'  ? 'selected':'' }}>Hindi</option>
                        <option value="ar"  {{ ($careerSettings->default_language ?? '')   == 'ar'  ? 'selected':'' }}>Arabic</option>
                        <option value="fr"  {{ ($careerSettings->default_language ?? '')   == 'fr'  ? 'selected':'' }}>French</option>
                    </select>
                    <div class="cps-hint">Select the default language for your career page</div>
                </div>

                {{-- Currency --}}
                <div>
                    <label class="cps-label">Currency</label>
                    <select class="cps-select" name="currency">
                        <option value="INR" {{ ($careerSettings->currency ?? 'INR') == 'INR' ? 'selected':'' }}>INR (₹)</option>
                        <option value="USD" {{ ($careerSettings->currency ?? '')    == 'USD' ? 'selected':'' }}>USD ($)</option>
                        <option value="EUR" {{ ($careerSettings->currency ?? '')    == 'EUR' ? 'selected':'' }}>EUR (€)</option>
                        <option value="GBP" {{ ($careerSettings->currency ?? '')    == 'GBP' ? 'selected':'' }}>GBP (£)</option>
                    </select>
                    <div class="cps-hint">Select currency to display salary information</div>
                </div>

            </div>
        </div>

        {{-- Branding & Appearance --}}
        <div class="cps-section">
            <div class="cps-section-title">Branding & Appearance</div>
            <div class="cps-section-sub">Customize how your career page looks and represents your brand.</div>

            <div class="cps-grid2">

                {{-- Primary Color --}}
                <div>
                    <label class="cps-label">Primary Color</label>
                    <div class="cps-color-row">
                        <input type="color" class="cps-color-swatch" id="primaryPicker"
                               value="{{ $primaryColor }}"
                               oninput="document.getElementById('primaryText').value=this.value;updatePreview();">
                        <input type="text" class="cps-input" id="primaryText"
                               name="primary_color" value="{{ $primaryColor }}"
                               oninput="document.getElementById('primaryPicker').value=this.value;updatePreview();">
                    </div>
                </div>

                {{-- Secondary Color --}}
                <div>
                    <label class="cps-label">Secondary Color</label>
                    <div class="cps-color-row">
                        <input type="color" class="cps-color-swatch" id="secondaryPicker"
                               value="{{ $secondaryColor }}"
                               oninput="document.getElementById('secondaryText').value=this.value;">
                        <input type="text" class="cps-input" id="secondaryText"
                               name="secondary_color" value="{{ $secondaryColor }}"
                               oninput="document.getElementById('secondaryPicker').value=this.value;">
                    </div>
                </div>

                {{-- Font Family --}}
                <div>
                    <label class="cps-label">Font Family</label>
                    <select class="cps-select" name="font_family">
                        @foreach(['Poppins','Inter','Roboto','Open Sans','Montserrat'] as $f)
                        <option value="{{ $f }}" {{ $fontFamily == $f ? 'selected':'' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Career Page Logo --}}
                <div>
                    <label class="cps-label">Career Page Logo</label>
                    <div class="cps-logo-row">
                        @if(!empty($company->logo))
                            <img src="{{ $company->logo }}" alt="Logo" class="cps-logo-img" id="logoPreviewImg">
                        @else
                            <div class="cps-logo-placeholder" id="logoPreviewImg">No Logo</div>
                        @endif
                        <div style="flex:1;min-width:0;">
                            <div class="cps-logo-name">{{ $company->logo ? basename($company->logo) : 'No logo uploaded' }}</div>
                            <div class="cps-logo-meta">PNG · 120 x 40 px</div>
                        </div>
                        <label class="cps-change-btn" style="cursor:pointer;">
                            Change
                            <input type="file" name="career_logo" accept="image/*"
                                   style="display:none;" onchange="previewLogo(this)">
                        </label>
                    </div>
                    <div class="cps-hint">Recommended: 120x40px, PNG with transparent background</div>
                </div>

            </div>
        </div>

        {{-- Layout Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Layout Settings</div>
            <div class="cps-section-sub">Choose layout and display preferences for your career page.</div>

            <div class="cps-grid2">

                {{-- Page Layout --}}
                <div>
                    <label class="cps-label">Page Layout</label>
                    <div class="cps-layout-grid">
                        @foreach(['modern'=>['Modern','Best for engagement'],'classic'=>['Classic','Clean and professional'],'minimal'=>['Minimal','Simple and focused']] as $k=>$v)
                        <div class="cps-layout-card {{ $pageLayout==$k?'selected':'' }}"
                             onclick="selectLayout('{{ $k }}',this)">
                            <div class="cps-lc-check">
                                <i class="ti ti-check" style="font-size:9px;color:#fff;"></i>
                            </div>
                            <div class="cps-lc-icon">
                                <i class="ti ti-layout-grid" style="font-size:15px;color:#64748B;"></i>
                            </div>
                            <div class="cps-lc-name">{{ $v[0] }}</div>
                            <div class="cps-lc-desc">{{ $v[1] }}</div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="page_layout" id="pageLayoutInput" value="{{ $pageLayout }}">
                </div>

                {{-- Jobs Display Style --}}
                <div>
                    <label class="cps-label">Jobs Display Style</label>
                    <div class="cps-display-grid">
                        <div class="cps-display-card {{ $jobsDisplay=='card'?'selected':'' }}"
                             onclick="selectDisplay('card',this)">
                            <i class="ti ti-layout-grid" style="font-size:22px;color:#2563EB;"></i>
                            <div>
                                <div class="cps-dc-name">Card View</div>
                                <div class="cps-dc-desc">Best visual experience</div>
                            </div>
                        </div>
                        <div class="cps-display-card {{ $jobsDisplay=='list'?'selected':'' }}"
                             onclick="selectDisplay('list',this)">
                            <i class="ti ti-list" style="font-size:22px;color:#64748B;"></i>
                            <div>
                                <div class="cps-dc-name">List View</div>
                                <div class="cps-dc-desc">Compact and detailed</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="jobs_display" id="jobsDisplayInput" value="{{ $jobsDisplay }}">
                </div>

            </div>
        </div>

    </div>

    {{-- ── Right: Sidebar ── --}}
    <div class="col-lg-4">

        {{-- Live Preview --}}
        <div class="cps-sidebar-card">
            <h4>Live Preview</h4>
            <p class="sub">See how your career page looks to candidates</p>
            <div class="cps-preview-box">
                <div class="cps-prev-hdr" id="previewHeader"
                     style="background:linear-gradient(135deg,{{ $primaryColor }},#7C3AED);">
                    <div class="cps-prev-hdr-title">Build the Future with</div>
                    <div class="cps-prev-hdr-name">{{ $companyName }}</div>
                    <div class="cps-prev-search">🔍 Search by skills, position or titles...</div>
                </div>
                <div class="cps-prev-body">
                    <div class="cps-prev-stats">
                        <div><div class="cps-prev-stat-n">32</div><div class="cps-prev-stat-l">Open Positions</div></div>
                        <div><div class="cps-prev-stat-n">15</div><div class="cps-prev-stat-l">Departments</div></div>
                        <div><div class="cps-prev-stat-n">220+</div><div class="cps-prev-stat-l">Employees</div></div>
                    </div>
                    <div class="cps-prev-positions">Our Open Positions</div>
                    <div class="cps-prev-job">
                        <div class="cps-prev-job-t">Senior Developer</div>
                        <div class="cps-prev-job-s">Frontend</div>
                    </div>
                    <div class="cps-prev-job">
                        <div class="cps-prev-job-t">UI/UX Designer</div>
                        <div class="cps-prev-job-s">Design</div>
                    </div>
                    <div class="cps-prev-job">
                        <div class="cps-prev-job-t">Product Manager</div>
                        <div class="cps-prev-job-s">Product</div>
                    </div>
                    <div class="cps-prev-footer">
                        <a href="{{ $careerUrl }}" target="_blank">
                            Preview Full Page <i class="ti ti-external-link" style="font-size:10px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="cps-sidebar-card">
            <h4>Quick Actions</h4>
            @php
            $qaItems = [
                ['icon'=>'ti-photo',     'bg'=>'#EFF6FF','ic'=>'#2563EB','title'=>'Upload Company Logo',   'desc'=>'Recommended size: 120x40px'],
                ['icon'=>'ti-camera',    'bg'=>'#FFF7ED','ic'=>'#F97316','title'=>'Update Company Images', 'desc'=>'Add photos for gallery section'],
                ['icon'=>'ti-file-text', 'bg'=>'#F0FDF4','ic'=>'#16A34A','title'=>'Manage Custom Pages',  'desc'=>'Add custom content pages'],
                ['icon'=>'ti-mail',      'bg'=>'#FFF1F2','ic'=>'#E11D48','title'=>'Email Templates',      'desc'=>'Customize career related emails'],
            ];
            @endphp
            @foreach($qaItems as $qa)
            <div class="cps-qa">
                <div class="cps-qa-left">
                    <div class="cps-qa-icon" style="background:{{ $qa['bg'] }};">
                        <i class="ti {{ $qa['icon'] }}" style="font-size:16px;color:{{ $qa['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="cps-qa-title">{{ $qa['title'] }}</div>
                        <div class="cps-qa-desc">{{ $qa['desc'] }}</div>
                    </div>
                </div>
                <i class="ti ti-chevron-right" style="font-size:14px;color:#CBD5E1;"></i>
            </div>
            @endforeach
        </div>

        {{-- Save Changes --}}
        <div class="cps-sidebar-card">
            <h4>Save Changes</h4>
            <div style="font-size:11px;color:#94A3B8;margin-bottom:12px;">Don't forget to save your changes</div>
            <button type="submit" class="cps-save-btn">
                <i class="ti ti-device-floppy" style="font-size:16px;"></i> Save All Changes
            </button>
        </div>

    </div>
</div>{{-- end row --}}
</form>
</div>{{-- end panel-general --}}

{{-- ════════════════ HERO SECTION TAB ════════════════ --}}
<div class="cps-panel" id="panel-hero">
<div class="row g-4">

    {{-- ── Left: Controls ── --}}
    <div class="col-lg-7">

        {{-- Layout Style --}}
        <div class="cps-section">
            <div class="cps-section-title">Layout Style</div>
            <div class="cps-section-sub">Choose how your hero section is structured</div>
            <div class="hs-layout-grid">
                <div class="hs-layout-card selected" onclick="selectHeroLayout('left-right',this)">
                    <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                    <div class="hs-lc-thumb">
                        <svg width="52" height="30" viewBox="0 0 52 30"><rect x="2" y="4" width="22" height="22" rx="2" fill="#A5B4FC"/><rect x="28" y="4" width="22" height="22" rx="2" fill="#C7D2FE"/></svg>
                    </div>
                    <div class="hs-lc-name">Left Content<br>Right Image</div>
                </div>
                <div class="hs-layout-card" onclick="selectHeroLayout('center',this)">
                    <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                    <div class="hs-lc-thumb">
                        <svg width="52" height="30" viewBox="0 0 52 30"><rect x="8" y="8" width="36" height="14" rx="2" fill="#A5B4FC"/></svg>
                    </div>
                    <div class="hs-lc-name">Center<br>Content</div>
                </div>
                <div class="hs-layout-card" onclick="selectHeroLayout('fullwidth',this)">
                    <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                    <div class="hs-lc-thumb">
                        <svg width="52" height="30" viewBox="0 0 52 30"><rect x="2" y="2" width="48" height="26" rx="2" fill="#A5B4FC"/></svg>
                    </div>
                    <div class="hs-lc-name">Full Width<br>Background</div>
                </div>
                <div class="hs-layout-card" onclick="selectHeroLayout('video',this)">
                    <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                    <div class="hs-lc-thumb">
                        <svg width="52" height="30" viewBox="0 0 52 30"><rect x="2" y="2" width="48" height="26" rx="2" fill="#C7D2FE"/><polygon points="20,10 20,20 34,15" fill="#6366F1"/></svg>
                    </div>
                    <div class="hs-lc-name">Video<br>Background</div>
                </div>
            </div>
            <input type="hidden" name="hero_layout" id="heroLayoutInput" value="left-right">
        </div>

        {{-- Badge / Eyebrow Text --}}
        <div class="cps-section">
            <label class="hs-label">Badge / Eyebrow Text</label>
            <input type="text" class="hs-input" name="hero_badge" id="heroBadge"
                   maxlength="80" placeholder="e.g. 🚀 Now Hiring — Join Our Team"
                   oninput="document.getElementById('heroBadgeCount').textContent=this.value.length+'/80';">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                <span class="hs-hint">Short text shown above the heading to grab attention</span>
                <span class="hs-char-count" id="heroBadgeCount">0/80</span>
            </div>
        </div>

        {{-- Main Heading --}}
        <div class="cps-section">
            <label class="hs-label">Main Heading</label>
            <input type="text" class="hs-input" name="hero_heading" id="heroHeading"
                   maxlength="80" placeholder="e.g. Build the Future with TechCorp"
                   oninput="document.getElementById('heroHeadingCount').textContent=this.value.length+'/80';">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                <span class="hs-hint">Use <code style="font-size:10px;background:#F1F5F9;padding:1px 4px;border-radius:4px;">&lt;span class="text-primary"&gt;keyword&lt;/span&gt;</code> to highlight a word</span>
                <span class="hs-char-count" id="heroHeadingCount">0/80</span>
            </div>
        </div>

        {{-- Description --}}
        <div class="cps-section">
            <label class="hs-label">Description</label>
            <textarea class="hs-textarea" name="hero_description" id="heroDesc" maxlength="200" rows="3"
                      placeholder="e.g. Join our team of innovators and help shape the future of technology. We offer great culture, growth, and benefits."
                      oninput="document.getElementById('heroDescCount').textContent=this.value.length+'/200';"></textarea>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                <span class="hs-hint">Brief description shown below the heading</span>
                <span class="hs-char-count" id="heroDescCount">0/200</span>
            </div>
        </div>

        {{-- Highlight Points --}}
        <div class="cps-section">
            <label class="hs-label">Highlight Points</label>
            <div class="hs-tags-wrap" id="hsTagsWrap">
                <div class="hs-tag">
                    <span class="hs-tag-icon"><i class="ti ti-check" style="font-size:8px;color:#2563EB;"></i></span>
                    Great Culture
                    <span class="hs-tag-del" onclick="removeHsTag(this)">×</span>
                </div>
                <div class="hs-tag">
                    <span class="hs-tag-icon"><i class="ti ti-check" style="font-size:8px;color:#2563EB;"></i></span>
                    Remote Friendly
                    <span class="hs-tag-del" onclick="removeHsTag(this)">×</span>
                </div>
                <div class="hs-tag">
                    <span class="hs-tag-icon"><i class="ti ti-check" style="font-size:8px;color:#2563EB;"></i></span>
                    Competitive Pay
                    <span class="hs-tag-del" onclick="removeHsTag(this)">×</span>
                </div>
            </div>
            <button type="button" class="hs-add-point" onclick="addHsTag()">
                <i class="ti ti-plus" style="font-size:11px;"></i> Add Highlight Point
            </button>
            <div class="hs-hint" style="margin-top:6px;">These appear as checkmarks in the hero section</div>
        </div>

        {{-- Primary CTA Button --}}
        <div class="cps-section">
            <label class="hs-label">Primary CTA Button</label>
            <div class="hs-cta-grid">
                <div>
                    <label class="hs-label" style="font-weight:500;font-size:11px;">Button Text</label>
                    <input type="text" class="hs-input" name="hero_cta1_text" placeholder="e.g. Explore Open Roles">
                </div>
                <div>
                    <label class="hs-label" style="font-weight:500;font-size:11px;">Button Link</label>
                    <input type="text" class="hs-input" name="hero_cta1_link" placeholder="e.g. /careers/jobs">
                </div>
            </div>
            <label class="hs-cta-newtab" style="margin-top:8px;">
                <input type="checkbox" name="hero_cta1_newtab" style="accent-color:#2563EB;width:14px;height:14px;">
                Open in new tab
            </label>
        </div>

        {{-- Secondary CTA --}}
        <div class="cps-section">
            <label class="hs-label">Secondary CTA Button <span style="font-size:10px;color:#94A3B8;font-weight:400;">(Optional)</span></label>
            <div class="hs-cta-grid">
                <div>
                    <label class="hs-label" style="font-weight:500;font-size:11px;">Button Text</label>
                    <input type="text" class="hs-input" name="hero_cta2_text" placeholder="e.g. Learn About Us">
                </div>
                <div>
                    <label class="hs-label" style="font-weight:500;font-size:11px;">Button Link</label>
                    <input type="text" class="hs-input" name="hero_cta2_link" placeholder="e.g. /about">
                </div>
            </div>
            <label class="hs-cta-newtab" style="margin-top:8px;">
                <input type="checkbox" name="hero_cta2_newtab" style="accent-color:#2563EB;width:14px;height:14px;">
                Open in new tab
            </label>
        </div>

        {{-- Background Style --}}
        <div class="cps-section">
            <div class="cps-section-title">Background Style</div>
            <div class="cps-section-sub">Choose the background style for your hero section</div>
            <div class="hs-bg-options">
                <label class="hs-bg-opt">
                    <input type="radio" name="hero_bg_style" value="gradient" checked> Gradient
                </label>
                <label class="hs-bg-opt">
                    <input type="radio" name="hero_bg_style" value="image"> Image
                </label>
                <label class="hs-bg-opt">
                    <input type="radio" name="hero_bg_style" value="video"> Video
                </label>
                <label class="hs-bg-opt">
                    <input type="radio" name="hero_bg_style" value="solid"> Solid Color
                </label>
            </div>
            <label class="hs-label" style="margin-bottom:10px;">Gradient Style</label>
            <div class="hs-grad-grid">
                <div class="hs-grad-swatch selected" style="background:linear-gradient(135deg,#4F46E5,#7C3AED);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#4F46E5,#7C3AED)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#0EA5E9,#2563EB);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#0EA5E9,#2563EB)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#10B981,#059669);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#10B981,#059669)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#F59E0B,#EF4444);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#F59E0B,#EF4444)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#EC4899,#8B5CF6);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#EC4899,#8B5CF6)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#0F172A,#1E293B);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#0F172A,#1E293B)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-custom" onclick="document.getElementById('customGradientPicker').click()">
                    <i class="ti ti-palette" style="font-size:16px;"></i>
                    <span>Custom</span>
                    <input type="color" id="customGradientPicker" style="display:none;"
                           onchange="selectGradient(null,'linear-gradient(135deg,'+this.value+','+this.value+')')">
                </div>
            </div>
            <input type="hidden" name="hero_bg_gradient" id="heroGradientInput" value="linear-gradient(135deg,#4F46E5,#7C3AED)">
        </div>

    </div>

    {{-- ── Right: Preview + Upload + Tips ── --}}
    <div class="col-lg-5">

        {{-- Live Preview --}}
        <div class="cps-sidebar-card" style="padding:16px;">
            <h4 style="margin-bottom:3px;">Live Preview</h4>
            <p class="sub">How your hero section will look</p>
            <div class="hs-prev-wrap">
                <div class="hs-prev-nav">
                    <div class="hs-prev-nav-logo">{{ $companyName }}</div>
                    <div class="hs-prev-nav-links">
                        <span class="hs-prev-nav-link">Jobs</span>
                        <span class="hs-prev-nav-link">About</span>
                        <span class="hs-prev-nav-link">Culture</span>
                    </div>
                    <div class="hs-prev-nav-btn">Apply Now</div>
                </div>
                <div class="hs-prev-hero" id="hsHeroPreview" style="background:linear-gradient(135deg,#EEF2FF,#E0E7FF);">
                    <div>
                        <div class="hs-prev-badge">
                            <i class="ti ti-rocket" style="font-size:8px;"></i>
                            Now Hiring
                        </div>
                        <div class="hs-prev-heading" id="hsPreviewHeading">Build the Future with <span>{{ $companyName }}</span></div>
                        <div class="hs-prev-desc" id="hsPreviewDesc">Join our team of innovators and help shape the future of technology.</div>
                        <div class="hs-prev-highlights">
                            <div class="hs-prev-hl"><i class="ti ti-check"></i> Great Culture</div>
                            <div class="hs-prev-hl"><i class="ti ti-check"></i> Remote Friendly</div>
                        </div>
                        <div class="hs-prev-btns">
                            <div class="hs-prev-btn-primary">Explore Roles</div>
                            <div class="hs-prev-btn-secondary">Learn More</div>
                        </div>
                    </div>
                    <div class="hs-prev-img">
                        <i class="ti ti-users" style="font-size:36px;color:#818CF8;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Background Illustration --}}
        <div class="cps-sidebar-card" style="padding:16px;">
            <h4 style="margin-bottom:3px;">Background Illustration</h4>
            <p class="sub">Upload or choose an illustration for the hero section</p>
            <label class="hs-upload-box" for="heroIllustrationUpload">
                <div class="hs-upload-icon"><i class="ti ti-cloud-upload"></i></div>
                <div class="hs-upload-title">Click to Upload Image</div>
                <div class="hs-upload-meta">PNG, SVG · Max 2MB · Recommended 600×480px</div>
                <input type="file" id="heroIllustrationUpload" name="hero_illustration"
                       accept="image/*" style="display:none;" onchange="previewHeroIllus(this)">
            </label>
            <div class="hs-illus-grid" style="margin-top:12px;">
                <div class="hs-illus-thumb selected">
                    <i class="ti ti-users" style="font-size:18px;color:#818CF8;"></i>
                </div>
                <div class="hs-illus-thumb">
                    <i class="ti ti-rocket" style="font-size:18px;color:#34D399;"></i>
                </div>
                <div class="hs-illus-thumb">
                    <i class="ti ti-building" style="font-size:18px;color:#60A5FA;"></i>
                </div>
                <div class="hs-illus-thumb">
                    <i class="ti ti-chart-bar" style="font-size:18px;color:#F472B6;"></i>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="cps-sidebar-card" style="padding:16px;">
            <h4 style="margin-bottom:10px;">💡 Tips for a great hero</h4>
            <div class="hs-tips">
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#EFF6FF;color:#2563EB;">1</div>
                    <div>Keep your heading <strong>under 10 words</strong> for best readability on mobile.</div>
                </div>
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#FFF7ED;color:#F97316;">2</div>
                    <div>Use <strong>action words</strong> in your CTA: "Explore Roles", "Join Us Today".</div>
                </div>
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#F0FDF4;color:#16A34A;">3</div>
                    <div>Add <strong>3–5 highlight points</strong> to quickly communicate your culture.</div>
                </div>
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#FFF1F2;color:#E11D48;">4</div>
                    <div>Choose a <strong>gradient that matches</strong> your brand's primary color.</div>
                </div>
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#FAF5FF;color:#7C3AED;">5</div>
                    <div>A <strong>high-contrast illustration</strong> on the right keeps focus on the text.</div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn" onclick="resetHeroSection()">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>

</div>{{-- end panel-hero --}}

</div>{{-- end cps-body --}}

</div>
</div>
</div>
<!--end::Content-->

<script>
/* ── Tabs ── */
document.querySelectorAll('.cps-tab').forEach(function(tab){
    tab.addEventListener('click', function(){
        document.querySelectorAll('.cps-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.cps-panel').forEach(p => p.classList.remove('active'));
        var panel = document.getElementById('panel-' + this.dataset.tab);
        if(panel) panel.classList.add('active');
    });
});

/* ── Layout card ── */
function selectLayout(key, el){
    document.querySelectorAll('.cps-layout-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('pageLayoutInput').value = key;
}

/* ── Display card ── */
function selectDisplay(key, el){
    document.querySelectorAll('.cps-display-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('jobsDisplayInput').value = key;
}

/* ── Copy URL ── */
function copyCareerUrl(){
    var input = document.getElementById('careerPageUrl');
    input.select();
    try { document.execCommand('copy'); toastr.success('Career page URL copied!'); } catch(e){}
}

/* ── Logo preview ── */
function previewLogo(input){
    if(input.files && input.files[0]){
        var reader = new FileReader();
        reader.onload = function(e){
            var el = document.getElementById('logoPreviewImg');
            if(el.tagName === 'IMG'){ el.src = e.target.result; }
            else {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'cps-logo-img';
                img.id = 'logoPreviewImg';
                el.parentNode.replaceChild(img, el);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* ── Live preview color ── */
function updatePreview(){
    var c = document.getElementById('primaryText').value;
    if(/^#[0-9A-Fa-f]{6}$/.test(c)){
        document.getElementById('previewHeader').style.background =
            'linear-gradient(135deg,' + c + ',#7C3AED)';
    }
}

/* ── Hero layout card ── */
function selectHeroLayout(key, el){
    document.querySelectorAll('.hs-layout-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('heroLayoutInput').value = key;
}

/* ── Gradient swatch ── */
function selectGradient(el, val){
    document.querySelectorAll('.hs-grad-swatch').forEach(s => s.classList.remove('selected'));
    if(el) el.classList.add('selected');
    document.getElementById('heroGradientInput').value = val;
    document.getElementById('hsHeroPreview').style.background = val;
}

/* ── Highlight tag remove ── */
function removeHsTag(el){
    el.parentElement.remove();
}

/* ── Add highlight tag ── */
function addHsTag(){
    var text = prompt('Enter highlight point:');
    if(!text || !text.trim()) return;
    var wrap = document.getElementById('hsTagsWrap');
    var tag = document.createElement('div');
    tag.className = 'hs-tag';
    tag.innerHTML = '<span class="hs-tag-icon"><i class="ti ti-check" style="font-size:8px;color:#2563EB;"></i></span>'
        + text.trim()
        + '<span class="hs-tag-del" onclick="removeHsTag(this)">×</span>';
    wrap.appendChild(tag);
}

/* ── Hero illustration preview ── */
function previewHeroIllus(input){
    if(input.files && input.files[0]){
        var reader = new FileReader();
        reader.onload = function(e){
            var box = document.querySelector('.hs-prev-img');
            box.innerHTML = '<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* ── Reset hero section ── */
function resetHeroSection(){
    document.querySelector('[name="hero_badge"]').value = '';
    document.querySelector('[name="hero_heading"]').value = '';
    document.querySelector('[name="hero_description"]').value = '';
    document.getElementById('heroBadgeCount').textContent = '0/80';
    document.getElementById('heroHeadingCount').textContent = '0/80';
    document.getElementById('heroDescCount').textContent = '0/200';
    selectGradient(document.querySelector('.hs-grad-swatch'), 'linear-gradient(135deg,#4F46E5,#7C3AED)');
}

/* ── Form submit (AJAX) ── */
document.getElementById('careerSettingsForm').addEventListener('submit', function(e){
    e.preventDefault();
    var fd = new FormData(this);
    $.ajax({
        url: this.action, type:'POST', data: fd,
        processData:false, contentType:false,
        success: function(res){
            if(res.status === 'success'){
                toastr.success(res.message || 'Settings saved successfully!');
            }
        },
        error: function(){ toastr.error('Failed to save. Please try again.'); }
    });
});
</script>
@endsection
