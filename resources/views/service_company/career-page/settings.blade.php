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

/* ── Company Info ── */
.ci-logo-wrap { display:flex; align-items:center; gap:14px; padding:14px; border:1.5px solid #E2E8F0; border-radius:10px; background:#F8FAFC; }
.ci-logo-img  { width:64px; height:64px; object-fit:contain; border-radius:10px; background:#fff; border:1px solid #E2E8F0; padding:4px; }
.ci-logo-placeholder { width:64px; height:64px; border-radius:10px; background:#EFF6FF; border:1px solid #BFDBFE; display:flex; align-items:center; justify-content:center; }
.ci-logo-meta { font-size:11px; color:#94A3B8; margin-top:2px; }
.ci-logo-actions { display:flex; gap:8px; margin-top:8px; }
.ci-change-btn { height:32px; padding:0 14px; border:1.5px solid #E2E8F0; border-radius:7px; font-size:12px; font-weight:600; color:#374151; background:#fff; cursor:pointer; font-family:'Poppins',sans-serif; }
.ci-del-btn { width:32px; height:32px; border:1.5px solid #FEE2E2; border-radius:7px; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#EF4444; }
.ci-input { width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 12px; font-size:13px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; }
.ci-input:focus { border-color:#2563EB; }
.ci-input-icon { position:relative; }
.ci-input-icon .ci-input { padding-left:36px; }
.ci-input-icon .ci-icon { position:absolute; left:11px; top:50%; transform:translateY(-50%); font-size:15px; color:#94A3B8; pointer-events:none; }
.ci-input-icon .ci-icon-right { position:absolute; right:11px; top:50%; transform:translateY(-50%); font-size:15px; color:#94A3B8; cursor:pointer; }
.ci-select { width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 12px; font-size:13px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; background:#fff; appearance:none; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }
.ci-select:focus { border-color:#2563EB; }
.ci-textarea { width:100%; border:1.5px solid #E2E8F0; border-radius:8px; padding:10px 12px; font-size:13px; color:#0F172A; outline:none; resize:vertical; font-family:'Poppins',sans-serif; min-height:70px; }
.ci-textarea:focus { border-color:#2563EB; }
.ci-char-count { font-size:11px; color:#94A3B8; text-align:right; margin-top:3px; }
.ci-rich-toolbar { display:flex; align-items:center; gap:2px; padding:8px 10px; border:1.5px solid #E2E8F0; border-bottom:none; border-radius:8px 8px 0 0; background:#F8FAFC; flex-wrap:wrap; }
.ci-rich-btn { width:28px; height:28px; border:none; background:transparent; border-radius:5px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#374151; font-family:'Poppins',sans-serif; }
.ci-rich-btn:hover { background:#E2E8F0; }
.ci-rich-sep  { width:1px; height:20px; background:#E2E8F0; margin:0 4px; }
.ci-rich-textarea { width:100%; border:1.5px solid #E2E8F0; border-top:none; border-radius:0 0 8px 8px; padding:12px; font-size:13px; color:#0F172A; outline:none; resize:vertical; font-family:'Poppins',sans-serif; min-height:120px; }
.ci-rich-textarea:focus { border-color:#2563EB; }

/* Company Preview card */
.ci-prev-card { border:1.5px solid #E2E8F0; border-radius:12px; overflow:hidden; }
.ci-prev-banner { height:80px; background:linear-gradient(135deg,#1E3A8A,#3B82F6); position:relative; }
.ci-prev-logo-circle { position:absolute; bottom:-24px; left:20px; width:50px; height:50px; border-radius:12px; background:#fff; border:2px solid #E2E8F0; display:flex; align-items:center; justify-content:center; }
.ci-prev-body { padding:32px 20px 20px; background:#fff; }
.ci-prev-name { font-size:16px; font-weight:800; color:#0F172A; display:flex; align-items:center; gap:6px; margin-bottom:2px; }
.ci-prev-tagline { font-size:11px; color:#64748B; margin-bottom:12px; }
.ci-prev-meta { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:12px; }
.ci-prev-meta-item { display:flex; align-items:center; gap:4px; font-size:10px; color:#64748B; }
.ci-prev-social { display:flex; gap:8px; margin-bottom:16px; }
.ci-prev-social a { font-size:9px; color:#2563EB; }
.ci-prev-about-title { font-size:12px; font-weight:700; color:#0F172A; margin-bottom:6px; }
.ci-prev-about-text { font-size:10px; color:#64748B; line-height:1.6; margin-bottom:14px; }
.ci-prev-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; border-top:1px solid #F1F5F9; padding-top:14px; }
.ci-prev-stat-n { font-size:14px; font-weight:800; color:#2563EB; }
.ci-prev-stat-l { font-size:9px; color:#94A3B8; margin-top:1px; }

/* Highlights list */
.ci-hl-row { display:flex; align-items:center; justify-content:space-between; padding:11px 14px; border:1px solid #E2E8F0; border-radius:8px; margin-bottom:8px; background:#fff; }
.ci-hl-left { display:flex; align-items:center; gap:10px; }
.ci-hl-icon { width:32px; height:32px; border-radius:8px; background:#EFF6FF; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.ci-hl-label { font-size:12px; font-weight:600; color:#374151; }
.ci-hl-val   { font-size:13px; font-weight:700; color:#0F172A; }
.ci-hl-del   { width:28px; height:28px; border:1px solid #FEE2E2; border-radius:7px; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#EF4444; font-size:13px; }
.ci-add-hl { display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#2563EB; cursor:pointer; padding:8px 0; background:none; border:none; font-family:'Poppins',sans-serif; }

/* ── Benefits ── */
.bn-toggle-row { display:flex; align-items:center; gap:10px; margin-bottom:0; }
.bn-toggle-label { font-size:12px; color:#64748B; }
.bn-input { width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 12px; font-size:13px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; }
.bn-input:focus { border-color:#2563EB; }
.bn-textarea { width:100%; border:1.5px solid #E2E8F0; border-radius:8px; padding:10px 12px; font-size:13px; color:#0F172A; outline:none; resize:vertical; font-family:'Poppins',sans-serif; min-height:90px; }
.bn-textarea:focus { border-color:#2563EB; }
.bn-char-count { font-size:11px; color:#94A3B8; text-align:right; margin-top:3px; }

/* Display style cards */
.bn-style-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; }
.bn-style-card { border:1.5px solid #E2E8F0; border-radius:10px; padding:14px 10px 10px; cursor:pointer; text-align:center; transition:all .15s; position:relative; background:#fff; }
.bn-style-card:hover { border-color:#93C5FD; }
.bn-style-card.selected { border-color:#2563EB; background:#EFF6FF; }
.bn-style-check { position:absolute; top:8px; right:8px; width:18px; height:18px; background:#2563EB; border-radius:50%; display:none; align-items:center; justify-content:center; }
.bn-style-card.selected .bn-style-check { display:flex; }
.bn-style-icon { font-size:22px; color:#64748B; margin-bottom:6px; display:block; }
.bn-style-card.selected .bn-style-icon { color:#2563EB; }
.bn-style-name { font-size:11px; font-weight:700; color:#0F172A; }
.bn-style-desc { font-size:9px; color:#64748B; margin-top:2px; }

.bn-select { width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 12px; font-size:13px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; background:#fff; appearance:none; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }

/* Benefit rows */
.bn-benefit-row { display:flex; align-items:center; gap:10px; padding:12px 14px; border:1px solid #E2E8F0; border-radius:10px; margin-bottom:8px; background:#fff; cursor:grab; }
.bn-benefit-row:hover { border-color:#BFDBFE; }
.bn-drag { color:#CBD5E1; font-size:16px; cursor:grab; flex-shrink:0; }
.bn-benefit-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.bn-benefit-info { flex:1; min-width:0; }
.bn-benefit-title { font-size:13px; font-weight:700; color:#0F172A; }
.bn-benefit-desc  { font-size:11px; color:#94A3B8; margin-top:1px; }
.bn-benefit-actions { display:flex; align-items:center; gap:6px; flex-shrink:0; }
.bn-act-btn { width:30px; height:30px; border:1px solid #E2E8F0; border-radius:7px; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px; transition:all .15s; }
.bn-act-btn:hover { background:#F8FAFC; }
.bn-act-btn.eye   { color:#2563EB; border-color:#BFDBFE; }
.bn-act-btn.edit  { color:#64748B; }
.bn-act-btn.del   { color:#EF4444; border-color:#FEE2E2; }

/* Add Benefit button */
.bn-add-btn { display:inline-flex; align-items:center; gap:6px; padding:9px 18px; background:#2563EB; color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif; white-space:nowrap; }
.bn-add-btn:hover { background:#1D4ED8; }

/* Tip bar */
.bn-tip-bar { display:flex; align-items:center; gap:8px; padding:10px 14px; background:#EFF6FF; border-radius:8px; margin-top:12px; font-size:11px; color:#2563EB; font-weight:500; }

/* Benefits live preview */
.bn-prev-wrap { border:1.5px solid #E2E8F0; border-radius:10px; overflow:hidden; }
.bn-prev-nav { background:#fff; padding:8px 12px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #F1F5F9; }
.bn-prev-nav-logo { display:flex; align-items:center; gap:5px; font-size:10px; font-weight:700; color:#0F172A; }
.bn-prev-nav-links { display:flex; gap:8px; }
.bn-prev-nav-link { font-size:8px; color:#64748B; }
.bn-prev-nav-btn  { font-size:8px; font-weight:700; color:#fff; background:#2563EB; padding:3px 8px; border-radius:4px; }
.bn-prev-section { background:#fff; padding:18px 14px; }
.bn-prev-title { font-size:14px; font-weight:800; color:#0F172A; margin-bottom:4px; }
.bn-prev-desc  { font-size:8px; color:#64748B; line-height:1.5; margin-bottom:14px; }
.bn-prev-grid  { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
.bn-prev-card  { text-align:center; padding:10px 6px; border:1px solid #F1F5F9; border-radius:8px; }
.bn-prev-card-icon { width:30px; height:30px; border-radius:8px; margin:0 auto 6px; display:flex; align-items:center; justify-content:center; }
.bn-prev-card-name { font-size:8px; font-weight:700; color:#0F172A; margin-bottom:3px; line-height:1.3; }
.bn-prev-card-desc { font-size:7px; color:#94A3B8; line-height:1.4; }
.bn-prev-link  { text-align:center; padding-top:10px; font-size:9px; color:#2563EB; font-weight:600; display:flex; align-items:center; justify-content:center; gap:3px; }

/* Tips card */
.bn-tips-list { }
.bn-tip-item  { display:flex; align-items:flex-start; gap:8px; padding:8px 0; border-bottom:1px solid #F8FAFC; font-size:12px; color:#374151; }
.bn-tip-item:last-child { border-bottom:none; }
.bn-tip-item i { font-size:13px; flex-shrink:0; margin-top:1px; }

/* ── Job Settings ── */
.js-select { width:100%; height:42px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 36px 0 12px; font-size:13px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; background:#fff; appearance:none; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }
.js-select:focus { border-color:#2563EB; }
.js-hint { font-size:11px; color:#94A3B8; margin-top:5px; }
.js-toggle-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding:0; }
.js-toggle-label { font-size:13px; font-weight:600; color:#0F172A; margin-bottom:2px; }
.js-toggle-desc  { font-size:11px; color:#94A3B8; }
.js-radio-group  { display:flex; flex-direction:column; gap:12px; }
.js-radio-item   { display:flex; align-items:flex-start; gap:10px; cursor:pointer; }
.js-radio-item input[type="radio"] { accent-color:#2563EB; width:16px; height:16px; flex-shrink:0; margin-top:2px; cursor:pointer; }
.js-radio-label  { font-size:13px; font-weight:600; color:#0F172A; }
.js-radio-desc   { font-size:11px; color:#94A3B8; margin-top:1px; }
.js-chk-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px 0; }
.js-chk-item { display:flex; align-items:center; gap:7px; font-size:12px; font-weight:500; color:#374151; cursor:pointer; }
.js-chk-item input[type="checkbox"] { accent-color:#2563EB; width:15px; height:15px; cursor:pointer; }

/* Job preview cards */
.js-prev-wrap { border:1.5px solid #E2E8F0; border-radius:10px; overflow:hidden; }
.js-prev-nav  { background:#fff; padding:8px 12px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #F1F5F9; }
.js-prev-nav-logo { display:flex; align-items:center; gap:5px; font-size:10px; font-weight:700; color:#0F172A; }
.js-prev-nav-links { display:flex; gap:8px; }
.js-prev-nav-link  { font-size:8px; color:#64748B; }
.js-prev-nav-btn   { font-size:8px; font-weight:700; color:#fff; background:#2563EB; padding:3px 8px; border-radius:4px; }
.js-prev-body { background:#F8FAFC; padding:12px; }
.js-job-card  { background:#fff; border:1px solid #E2E8F0; border-radius:8px; padding:10px 12px; margin-bottom:8px; position:relative; }
.js-job-badge { display:inline-block; font-size:7px; font-weight:700; padding:2px 6px; border-radius:4px; background:#DCFCE7; color:#16A34A; margin-bottom:6px; }
.js-job-title { font-size:11px; font-weight:800; color:#0F172A; margin-bottom:2px; }
.js-job-meta  { display:flex; gap:8px; font-size:8px; color:#64748B; margin-bottom:6px; flex-wrap:wrap; }
.js-job-meta span { display:flex; align-items:center; gap:2px; }
.js-job-info  { display:flex; gap:12px; font-size:8px; color:#64748B; margin-bottom:6px; }
.js-job-desc  { font-size:8px; color:#64748B; line-height:1.5; margin-bottom:6px; }
.js-job-tags  { display:flex; flex-wrap:wrap; gap:4px; align-items:center; justify-content:space-between; }
.js-tag { font-size:7px; font-weight:600; padding:2px 7px; border-radius:4px; background:#F1F5F9; color:#374151; }
.js-job-btn { font-size:8px; font-weight:700; color:#fff; background:#2563EB; padding:4px 10px; border-radius:5px; white-space:nowrap; }
.js-job-bookmark { position:absolute; top:10px; right:12px; font-size:12px; color:#CBD5E1; }
.js-prev-link { text-align:center; padding:8px 0 4px; font-size:9px; color:#2563EB; font-weight:600; display:flex; align-items:center; justify-content:center; gap:3px; }

/* Additional Options card */
.js-addopt-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding:12px 0; border-bottom:1px solid #F8FAFC; }
.js-addopt-row:last-child { border-bottom:none; padding-bottom:0; }
.js-addopt-left { display:flex; align-items:flex-start; gap:10px; }
.js-addopt-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.js-addopt-title { font-size:12px; font-weight:600; color:#0F172A; }
.js-addopt-desc  { font-size:10px; color:#94A3B8; margin-top:1px; }

/* ── SEO & Social ── */
.seo-input { width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 12px; font-size:13px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; }
.seo-input:focus { border-color:#2563EB; }
.seo-textarea { width:100%; border:1.5px solid #E2E8F0; border-radius:8px; padding:10px 12px; font-size:13px; color:#0F172A; outline:none; resize:vertical; font-family:'Poppins',sans-serif; min-height:90px; }
.seo-textarea:focus { border-color:#2563EB; }
.seo-hint { font-size:11px; color:#94A3B8; margin-top:5px; }
.seo-char { font-size:11px; color:#94A3B8; text-align:right; margin-top:3px; }
.seo-select { width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 12px; font-size:13px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; background:#fff; appearance:none; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }
.seo-select:focus { border-color:#2563EB; }
/* Keyword tags */
.seo-tags-wrap { border:1.5px solid #E2E8F0; border-radius:8px; padding:8px 10px; min-height:80px; background:#fff; cursor:text; }
.seo-tag { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; background:#EFF6FF; border:1px solid #BFDBFE; border-radius:5px; font-size:11px; font-weight:600; color:#1D4ED8; margin:3px; }
.seo-tag-del { cursor:pointer; color:#93C5FD; font-weight:700; font-size:12px; }
.seo-tag-del:hover { color:#2563EB; }
.seo-tag-input { border:none; outline:none; font-size:12px; font-family:'Poppins',sans-serif; color:#374151; width:120px; padding:3px 2px; background:transparent; }
/* Slug row */
.seo-slug-wrap { position:relative; }
.seo-slug-wrap .seo-input { padding-right:32px; }
.seo-slug-url { font-size:11px; color:#94A3B8; margin-top:5px; }
.seo-slug-url a { color:#2563EB; font-weight:600; text-decoration:none; }
/* Social rows */
.seo-social-row { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #F8FAFC; }
.seo-social-row:last-child { border-bottom:none; }
.seo-social-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:16px; }
.seo-social-label { width:90px; font-size:12px; font-weight:600; color:#374151; flex-shrink:0; }
.seo-social-input { flex:1; height:36px; border:1.5px solid #E2E8F0; border-radius:7px; padding:0 10px; font-size:12px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; }
.seo-social-input:focus { border-color:#2563EB; }
.seo-add-link { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#2563EB; cursor:pointer; background:none; border:none; font-family:'Poppins',sans-serif; padding:8px 0 0; }
/* Search engine preview */
.seo-se-prev { border:1.5px solid #E2E8F0; border-radius:10px; padding:16px 18px; background:#fff; }
.seo-se-url  { font-size:11px; color:#16A34A; margin-bottom:4px; }
.seo-se-title { font-size:15px; font-weight:700; color:#2563EB; margin-bottom:5px; cursor:pointer; }
.seo-se-title:hover { text-decoration:underline; }
.seo-se-desc { font-size:12px; color:#374151; line-height:1.6; }
/* Social share preview */
.seo-share-prev { border:1.5px solid #E2E8F0; border-radius:10px; overflow:hidden; }
.seo-share-logo-bar { padding:8px 12px; display:flex; align-items:center; gap:6px; border-bottom:1px solid #F1F5F9; }
.seo-share-hero { background:linear-gradient(135deg,#1E3A8A,#3B82F6); padding:22px 20px; position:relative; min-height:100px; display:flex; align-items:flex-end; }
.seo-share-hero-text { color:#fff; position:relative; z-index:1; }
.seo-share-hero-title { font-size:16px; font-weight:800; line-height:1.2; margin-bottom:6px; }
.seo-share-hero-sub   { font-size:10px; opacity:.85; }
.seo-share-body { padding:12px 14px; background:#fff; }
.seo-share-og-title { font-size:13px; font-weight:700; color:#0F172A; margin-bottom:4px; }
.seo-share-og-desc  { font-size:11px; color:#64748B; line-height:1.5; margin-bottom:6px; }
.seo-share-og-url   { font-size:10px; color:#94A3B8; margin-bottom:10px; }
.seo-share-btns { display:flex; gap:8px; flex-wrap:wrap; }
.seo-share-btn { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border:1px solid #E2E8F0; border-radius:6px; font-size:10px; font-weight:600; color:#374151; cursor:pointer; background:#fff; }
/* Additional SEO options */
.seo-addopt-row { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; padding:10px 0; border-bottom:1px solid #F8FAFC; }
.seo-addopt-row:last-child { border-bottom:none; padding-bottom:0; }
.seo-addopt-left { display:flex; align-items:flex-start; gap:10px; }
.seo-addopt-icon { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.seo-addopt-title { font-size:12px; font-weight:600; color:#0F172A; }
.seo-addopt-desc  { font-size:10px; color:#94A3B8; margin-top:1px; }

/* ── Integrations ── */
.int-search-bar { display:flex; gap:10px; align-items:center; margin-bottom:16px; }
.int-search-wrap { flex:1; position:relative; }
.int-search-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:15px; color:#94A3B8; }
.int-search-input { width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 12px 0 36px; font-size:13px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; }
.int-search-input:focus { border-color:#2563EB; }
.int-cat-select { height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 32px 0 12px; font-size:12px; font-weight:600; color:#374151; background:#fff; appearance:none; cursor:pointer; font-family:'Poppins',sans-serif; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; white-space:nowrap; }
/* Category tabs */
.int-tabs { display:flex; gap:0; border-bottom:1.5px solid #E2E8F0; margin-bottom:20px; overflow-x:auto; }
.int-tabs::-webkit-scrollbar { height:0; }
.int-tab { padding:10px 16px; font-size:12px; font-weight:600; color:#64748B; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1.5px; white-space:nowrap; transition:color .15s,border-color .15s; }
.int-tab:hover { color:#2563EB; }
.int-tab.active { color:#2563EB; border-bottom-color:#2563EB; }
/* Integration cards grid */
.int-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:16px; }
.int-card { border:1.5px solid #E2E8F0; border-radius:12px; padding:20px 18px 16px; background:#fff; transition:box-shadow .15s; }
.int-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }
.int-card-logo { height:36px; display:flex; align-items:center; margin-bottom:12px; }
.int-card-logo img { max-height:36px; max-width:120px; object-fit:contain; }
.int-card-name { font-size:13px; font-weight:700; color:#0F172A; margin-bottom:4px; }
.int-card-desc { font-size:11px; color:#64748B; line-height:1.5; margin-bottom:14px; min-height:34px; }
.int-card-footer { display:flex; align-items:center; gap:8px; }
.int-connected-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; background:#DCFCE7; color:#16A34A; border-radius:6px; font-size:10px; font-weight:700; }
.int-connect-btn { font-size:12px; font-weight:600; color:#2563EB; background:none; border:none; cursor:pointer; font-family:'Poppins',sans-serif; padding:0; }
.int-connect-btn:hover { text-decoration:underline; }
.int-manage-btn { font-size:12px; font-weight:600; color:#64748B; background:none; border:none; cursor:pointer; font-family:'Poppins',sans-serif; padding:0; }
.int-manage-btn:hover { color:#2563EB; }
/* Request card */
.int-request-card { border:1.5px solid #E2E8F0; border-radius:12px; padding:20px 24px; display:flex; align-items:center; gap:20px; background:#F8FAFC; }
.int-request-icon { width:56px; height:56px; flex-shrink:0; background:linear-gradient(135deg,#EFF6FF,#E0E7FF); border-radius:50%; display:flex; align-items:center; justify-content:center; }
.int-request-title { font-size:14px; font-weight:700; color:#0F172A; margin-bottom:4px; }
.int-request-desc  { font-size:12px; color:#64748B; }
.int-request-btn   { padding:10px 20px; background:#fff; border:1.5px solid #E2E8F0; border-radius:8px; font-size:12px; font-weight:700; color:#374151; cursor:pointer; font-family:'Poppins',sans-serif; white-space:nowrap; flex-shrink:0; }
.int-request-btn:hover { border-color:#2563EB; color:#2563EB; }
/* Active integrations */
.int-active-row { display:flex; align-items:center; justify-content:space-between; padding:11px 0; border-bottom:1px solid #F8FAFC; }
.int-active-row:last-child { border-bottom:none; padding-bottom:0; }
.int-active-left { display:flex; align-items:center; gap:10px; }
.int-active-logo { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid #E2E8F0; background:#fff; }
.int-active-name { font-size:12px; font-weight:700; color:#0F172A; }
.int-active-sync { font-size:10px; color:#94A3B8; margin-top:1px; }
.int-active-right { display:flex; align-items:center; gap:8px; }
.int-connected-sm { padding:2px 8px; background:#DCFCE7; color:#16A34A; border-radius:5px; font-size:10px; font-weight:700; }
.int-gear-btn { width:28px; height:28px; border:1px solid #E2E8F0; border-radius:7px; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#64748B; font-size:13px; }
/* Webhooks */
.int-wh-row { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #F8FAFC; }
.int-wh-row:last-child { border-bottom:none; }
.int-wh-left { display:flex; align-items:center; gap:10px; }
.int-wh-icon { width:34px; height:34px; border-radius:9px; background:#F1F5F9; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.int-wh-name { font-size:12px; font-weight:600; color:#0F172A; }
.int-wh-url  { font-size:10px; color:#94A3B8; margin-top:1px; }
.int-wh-right { display:flex; align-items:center; gap:8px; }
.int-active-badge { padding:2px 8px; background:#DCFCE7; color:#16A34A; border-radius:5px; font-size:10px; font-weight:700; }
.int-dots-btn { width:24px; height:24px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#94A3B8; font-size:16px; border-radius:5px; }
.int-dots-btn:hover { background:#F1F5F9; color:#374151; }
.int-add-wh-btn { display:inline-flex; align-items:center; gap:5px; padding:6px 14px; border:1.5px solid #2563EB; border-radius:7px; font-size:11px; font-weight:700; color:#2563EB; background:#fff; cursor:pointer; font-family:'Poppins',sans-serif; }
.int-view-all { text-align:center; padding-top:8px; font-size:11px; font-weight:600; color:#2563EB; cursor:pointer; }
/* Other settings */
.int-other-row { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; padding:11px 0; border-bottom:1px solid #F8FAFC; }
.int-other-row:last-child { border-bottom:none; padding-bottom:0; }
.int-other-left { display:flex; align-items:flex-start; gap:10px; }
.int-other-icon { width:32px; height:32px; border-radius:8px; background:#EFF6FF; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.int-other-title { font-size:12px; font-weight:600; color:#0F172A; }
.int-other-desc  { font-size:10px; color:#94A3B8; margin-top:1px; }
.int-arrow-btn  { width:28px; height:28px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#CBD5E1; font-size:16px; flex-shrink:0; margin-top:2px; }

/* ── Advanced ── */
.adv-toggle-row { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; padding:12px 0; border-bottom:1px solid #F8FAFC; }
.adv-toggle-row:last-child { border-bottom:none; padding-bottom:0; }
.adv-toggle-label { font-size:13px; font-weight:600; color:#0F172A; }
.adv-toggle-desc  { font-size:11px; color:#94A3B8; margin-top:2px; }
.adv-select { width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 34px 0 12px; font-size:13px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; background:#fff; appearance:none; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; margin-top:6px; }
.adv-select:focus { border-color:#2563EB; }
.adv-hint { font-size:11px; color:#94A3B8; margin-top:5px; }

/* Performance score gauge */
.adv-perf-card { border:1.5px solid #E2E8F0; border-radius:12px; padding:18px 16px; background:#fff; }
.adv-gauge-wrap { text-align:center; margin-bottom:12px; }
.adv-gauge-score { font-size:28px; font-weight:800; color:#0F172A; }
.adv-gauge-total { font-size:13px; color:#94A3B8; }
.adv-gauge-label { font-size:11px; font-weight:700; color:#16A34A; margin-top:2px; }
.adv-perf-check { display:flex; align-items:center; justify-content:space-between; font-size:11px; padding:5px 0; border-bottom:1px solid #F8FAFC; }
.adv-perf-check:last-child { border-bottom:none; padding-bottom:0; }
.adv-perf-check-name { display:flex; align-items:center; gap:5px; color:#374151; }
.adv-perf-check-name i { font-size:12px; color:#16A34A; }
.adv-perf-excellent { color:#16A34A; font-weight:600; }
.adv-perf-good { color:#F59E0B; font-weight:600; }

/* Security mini-card */
.adv-sec-card { border:1.5px solid #E2E8F0; border-radius:12px; padding:18px 16px; background:#fff; }
.adv-sec-url  { font-size:12px; font-weight:600; color:#0F172A; margin-bottom:2px; }
.adv-sec-link { font-size:12px; color:#2563EB; display:flex; align-items:center; gap:4px; margin-bottom:12px; text-decoration:none; }
.adv-copy-btn { width:100%; padding:8px; border:1.5px solid #E2E8F0; border-radius:8px; font-size:12px; font-weight:600; color:#374151; background:#fff; cursor:pointer; font-family:'Poppins',sans-serif; text-align:center; }
.adv-copy-btn:hover { border-color:#2563EB; color:#2563EB; }
.adv-share-title { font-size:11px; font-weight:600; color:#374151; margin:14px 0 8px; }
.adv-share-desc  { font-size:10px; color:#94A3B8; margin-bottom:8px; }
.adv-share-icons { display:flex; gap:8px; margin-bottom:10px; }
.adv-share-icon  { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; cursor:pointer; }
.adv-preview-btn { width:100%; padding:8px; border:1.5px solid #E2E8F0; border-radius:8px; font-size:12px; font-weight:600; color:#374151; background:#fff; cursor:pointer; font-family:'Poppins',sans-serif; text-align:center; display:flex; align-items:center; justify-content:center; gap:5px; }
.adv-preview-btn:hover { border-color:#2563EB; color:#2563EB; }

/* Maintenance card */
.adv-maint-card { border:1.5px solid #E2E8F0; border-radius:10px; padding:16px; background:#FFFBF0; margin-top:10px; }
.adv-maint-header { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:12px; }
.adv-maint-title { font-size:13px; font-weight:700; color:#0F172A; }
.adv-maint-desc  { font-size:11px; color:#94A3B8; margin-top:2px; }
.adv-maint-textarea { width:100%; border:1.5px solid #E2E8F0; border-radius:8px; padding:10px 12px; font-size:12px; color:#0F172A; outline:none; resize:vertical; font-family:'Poppins',sans-serif; min-height:60px; background:#fff; }

/* Custom Code editor */
.adv-code-tabs { display:flex; border-bottom:1.5px solid #E2E8F0; margin-bottom:0; }
.adv-code-tab { padding:8px 14px; font-size:12px; font-weight:600; color:#64748B; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1.5px; transition:all .15s; }
.adv-code-tab:hover { color:#2563EB; }
.adv-code-tab.active { color:#2563EB; border-bottom-color:#2563EB; }
.adv-code-editor { background:#1E293B; border-radius:0 0 10px 10px; padding:14px; font-family:'Courier New',monospace; font-size:11px; line-height:1.7; color:#94A3B8; overflow-x:auto; position:relative; }
.adv-code-line { display:flex; gap:14px; }
.adv-code-ln   { color:#475569; min-width:16px; user-select:none; flex-shrink:0; }
.adv-code-text { color:#CBD5E1; }
.adv-code-tag  { color:#7DD3FC; }
.adv-code-attr { color:#86EFAC; }
.adv-code-val  { color:#FCA5A5; }
.adv-code-comment { color:#64748B; }
.adv-code-textarea { width:100%; min-height:140px; background:#1E293B; color:#CBD5E1; border:none; outline:none; font-family:'Courier New',monospace; font-size:11px; line-height:1.7; resize:vertical; padding:0; }

/* Analytics tracking */
.adv-track-input-wrap { position:relative; margin-bottom:12px; }
.adv-track-input { width:100%; height:40px; border:1.5px solid #E2E8F0; border-radius:8px; padding:0 90px 0 12px; font-size:12px; color:#0F172A; outline:none; font-family:'Poppins',sans-serif; }
.adv-track-input:focus { border-color:#2563EB; }
.adv-track-badge { position:absolute; right:8px; top:50%; transform:translateY(-50%); padding:3px 9px; background:#DCFCE7; color:#16A34A; border-radius:5px; font-size:10px; font-weight:700; }
.adv-verify-btn { position:absolute; right:8px; top:50%; transform:translateY(-50%); padding:4px 10px; border:1.5px solid #2563EB; border-radius:6px; font-size:10px; font-weight:700; color:#2563EB; background:#fff; cursor:pointer; font-family:'Poppins',sans-serif; white-space:nowrap; }
.adv-track-label { font-size:12px; font-weight:600; color:#374151; margin-bottom:5px; }

/* Tracking status card */
.adv-track-status { border:1.5px solid #E2E8F0; border-radius:10px; padding:14px; margin-top:12px; }
.adv-ts-row { display:flex; align-items:center; justify-content:space-between; padding:5px 0; }
.adv-ts-name { font-size:11px; color:#374151; }
.adv-ts-badge { padding:2px 8px; background:#DCFCE7; color:#16A34A; border-radius:4px; font-size:10px; font-weight:700; }
.adv-track-desc { font-size:10px; color:#64748B; line-height:1.5; margin-top:10px; display:flex; align-items:flex-start; gap:8px; }

/* Export & Backup */
.adv-export-row { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:12px 0; border-bottom:1px solid #F8FAFC; }
.adv-export-row:last-child { border-bottom:none; padding-bottom:0; }
.adv-export-label { font-size:12px; font-weight:600; color:#0F172A; }
.adv-export-desc  { font-size:10px; color:#94A3B8; margin-top:2px; }
.adv-export-btn { padding:7px 16px; border:1.5px solid #E2E8F0; border-radius:7px; font-size:11px; font-weight:700; color:#374151; background:#fff; cursor:pointer; font-family:'Poppins',sans-serif; white-space:nowrap; flex-shrink:0; }
.adv-export-btn:hover { border-color:#2563EB; color:#2563EB; }
.adv-backup-info { font-size:11px; color:#374151; }
.adv-backup-date { font-size:10px; color:#94A3B8; }
.adv-dl-btn { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; color:#2563EB; cursor:pointer; background:none; border:none; font-family:'Poppins',sans-serif; padding:4px 0 0; }

/* ── Template Design ── */
/* Template thumbnails */
.td-tpl-grid { display:flex; gap:12px; overflow-x:auto; padding-bottom:4px; }
.td-tpl-grid::-webkit-scrollbar { height:0; }
.td-tpl-card { border:2px solid #E2E8F0; border-radius:10px; overflow:hidden; cursor:pointer; transition:all .15s; position:relative; flex-shrink:0; width:130px; }
.td-tpl-card:hover { border-color:#93C5FD; }
.td-tpl-card.selected { border-color:#2563EB; }
.td-tpl-check { position:absolute; top:7px; right:7px; width:20px; height:20px; background:#2563EB; border-radius:50%; display:none; align-items:center; justify-content:center; z-index:2; }
.td-tpl-card.selected .td-tpl-check { display:flex; }
.td-tpl-thumb { height:80px; overflow:hidden; }
.td-tpl-info  { padding:8px 10px 10px; background:#fff; }
.td-tpl-name  { font-size:12px; font-weight:700; color:#0F172A; }
.td-tpl-desc  { font-size:10px; color:#94A3B8; margin-top:1px; }

/* Customize design sidebar */
.td-design-wrap { display:flex; gap:0; border:1.5px solid #E2E8F0; border-radius:10px; overflow:hidden; }
.td-design-nav { width:160px; flex-shrink:0; border-right:1.5px solid #E2E8F0; background:#F8FAFC; }
.td-design-nav-item { display:flex; align-items:center; gap:8px; padding:10px 14px; font-size:12px; font-weight:600; color:#64748B; cursor:pointer; border-left:2px solid transparent; transition:all .15s; }
.td-design-nav-item:hover { background:#EFF6FF; color:#2563EB; }
.td-design-nav-item.active { background:#EFF6FF; color:#2563EB; border-left-color:#2563EB; }
.td-design-tab { display:none; }
.td-design-tab.active { display:block; }
.td-design-nav-item i { font-size:14px; }
.td-design-content { flex:1; padding:20px; min-width:0; }
.td-design-section-title { font-size:13px; font-weight:700; color:#0F172A; margin:0 0 2px; }
.td-design-section-sub   { font-size:11px; color:#94A3B8; margin:0 0 14px; }

/* Color inputs */
.td-color-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px; }
.td-color-label { font-size:11px; font-weight:600; color:#374151; margin-bottom:5px; }
.td-color-row   { display:flex; align-items:center; gap:7px; }
.td-color-swatch { width:34px; height:34px; border-radius:7px; border:1.5px solid #E2E8F0; cursor:pointer; padding:2px; flex-shrink:0; }
.td-color-input  { flex:1; height:34px; border:1.5px solid #E2E8F0; border-radius:7px; padding:0 8px; font-size:11px; font-weight:600; color:#374151; outline:none; font-family:'Poppins',sans-serif; }
.td-color-input:focus { border-color:#2563EB; }

/* Theme mode radios */
.td-theme-opts { display:flex; gap:18px; margin-bottom:16px; }
.td-theme-opt  { display:flex; align-items:center; gap:6px; cursor:pointer; font-size:12px; font-weight:500; color:#374151; }
.td-theme-opt input { accent-color:#2563EB; width:15px; height:15px; cursor:pointer; }

/* Layout style cards */
.td-layout-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
.td-layout-card { border:2px solid #E2E8F0; border-radius:9px; padding:12px 10px 10px; cursor:pointer; text-align:center; transition:all .15s; position:relative; }
.td-layout-card:hover { border-color:#93C5FD; }
.td-layout-card.selected { border-color:#2563EB; background:#EFF6FF; }
.td-lc-check { position:absolute; top:7px; right:7px; width:16px; height:16px; background:#2563EB; border-radius:50%; display:none; align-items:center; justify-content:center; }
.td-layout-card.selected .td-lc-check { display:flex; }
.td-lc-thumb  { height:36px; background:#E2E8F0; border-radius:5px; margin-bottom:7px; display:flex; align-items:center; justify-content:center; }
.td-layout-card.selected .td-lc-thumb { background:#BFDBFE; }
.td-lc-name   { font-size:11px; font-weight:700; color:#0F172A; }
.td-lc-desc   { font-size:9px; color:#64748B; margin-top:2px; }

/* Custom assets */
.td-asset-row  { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #F8FAFC; }
.td-asset-row:last-child { border-bottom:none; padding-bottom:0; }
.td-asset-thumb { width:48px; height:32px; border:1.5px solid #E2E8F0; border-radius:7px; display:flex; align-items:center; justify-content:center; background:#F8FAFC; flex-shrink:0; overflow:hidden; }
.td-asset-info  { flex:1; min-width:0; }
.td-asset-name  { font-size:12px; font-weight:600; color:#374151; }
.td-asset-size  { font-size:10px; color:#94A3B8; margin-top:1px; }
.td-asset-edit  { width:32px; height:32px; border:1.5px solid #E2E8F0; border-radius:7px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#64748B; flex-shrink:0; }
.td-asset-edit:hover { border-color:#2563EB; color:#2563EB; }

/* Additional options */
.td-opt-row { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; padding:10px 0; border-bottom:1px solid #F8FAFC; }
.td-opt-row:last-child { border-bottom:none; padding-bottom:0; }
.td-opt-left { display:flex; align-items:flex-start; gap:10px; }
.td-opt-icon { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.td-opt-title { font-size:12px; font-weight:600; color:#0F172A; }
.td-opt-desc  { font-size:10px; color:#94A3B8; margin-top:1px; }

/* Live preview */
.td-prev-device-bar { display:flex; align-items:center; gap:6px; }
.td-prev-device-btn { width:30px; height:30px; border:1.5px solid #E2E8F0; border-radius:7px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#94A3B8; font-size:14px; transition:all .15s; }
.td-prev-device-btn:hover,
.td-prev-device-btn.active { border-color:#2563EB; color:#2563EB; background:#EFF6FF; }
.td-live-prev { border:1.5px solid #E2E8F0; border-radius:10px; overflow:hidden; }
.td-prev-nav  { background:#fff; padding:8px 14px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #F1F5F9; }
.td-prev-nav-logo { display:flex; align-items:center; gap:5px; font-size:11px; font-weight:800; color:#0F172A; }
.td-prev-nav-links { display:flex; gap:10px; }
.td-prev-nav-link  { font-size:8px; color:#64748B; }
.td-prev-nav-btn   { font-size:8px; font-weight:700; color:#fff; background:#2563EB; padding:4px 10px; border-radius:5px; }
/* Hero */
.td-prev-hero { background:linear-gradient(135deg,#0F172A 0%,#1E3A8A 60%,#3B82F6 100%); padding:22px 20px; display:grid; grid-template-columns:1fr 1fr; gap:12px; align-items:center; }
.td-prev-hero-title { font-size:18px; font-weight:900; color:#fff; line-height:1.2; margin-bottom:6px; }
.td-prev-hero-sub { font-size:9px; color:rgba(255,255,255,.75); line-height:1.5; margin-bottom:12px; }
.td-prev-hero-search { display:flex; gap:0; background:#fff; border-radius:6px; overflow:hidden; }
.td-prev-hero-search input { flex:1; border:none; outline:none; padding:6px 10px; font-size:8px; font-family:'Poppins',sans-serif; color:#64748B; }
.td-prev-hero-search-btn { padding:6px 12px; background:#2563EB; color:#fff; font-size:8px; font-weight:700; border:none; cursor:pointer; font-family:'Poppins',sans-serif; white-space:nowrap; }
.td-prev-hero-img { border-radius:8px; overflow:hidden; align-self:center; }
/* Why section */
.td-prev-why { background:#fff; padding:16px 14px; text-align:center; }
.td-prev-why-title { font-size:13px; font-weight:800; color:#0F172A; margin-bottom:3px; }
.td-prev-why-sub   { font-size:8px; color:#64748B; margin-bottom:12px; }
.td-prev-why-grid  { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
.td-prev-why-card  { padding:8px 4px; }
.td-prev-why-icon  { width:28px; height:28px; border-radius:8px; margin:0 auto 5px; display:flex; align-items:center; justify-content:center; }
.td-prev-why-name  { font-size:8px; font-weight:700; color:#0F172A; margin-bottom:2px; }
.td-prev-why-desc  { font-size:7px; color:#64748B; line-height:1.4; }
/* CTA banner */
.td-prev-cta { background:linear-gradient(135deg,#1E3A8A,#2563EB); padding:14px 18px; display:flex; align-items:center; justify-content:space-between; }
.td-prev-cta-title { font-size:12px; font-weight:800; color:#fff; }
.td-prev-cta-sub   { font-size:8px; color:rgba(255,255,255,.8); margin-top:2px; }
.td-prev-cta-btn   { font-size:8px; font-weight:700; color:#2563EB; background:#fff; padding:6px 12px; border-radius:5px; white-space:nowrap; flex-shrink:0; }

/* Publish card */
.td-publish-card { background:linear-gradient(135deg,#EFF6FF,#F0FDF4); border:1.5px solid #BFDBFE; border-radius:10px; padding:16px; margin-top:14px; }
.td-publish-title { font-size:13px; font-weight:700; color:#0F172A; margin-bottom:4px; }
.td-publish-desc  { font-size:11px; color:#64748B; line-height:1.5; margin-bottom:12px; }
.td-publish-btn   { width:100%; padding:10px; background:#2563EB; color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; font-family:'Poppins',sans-serif; display:flex; align-items:center; justify-content:center; gap:6px; }
.td-publish-btn:hover { background:#1D4ED8; }

/* ── Templates ── */
.tpl-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
.tpl-card { border:1.5px solid #E2E8F0; border-radius:12px; overflow:hidden; cursor:pointer; transition:all .15s; position:relative; }
.tpl-card:hover { border-color:#93C5FD; box-shadow:0 4px 16px rgba(37,99,235,.1); }
.tpl-card.selected { border-color:#2563EB; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
.tpl-card-check { position:absolute; top:10px; right:10px; width:22px; height:22px; background:#2563EB; border-radius:50%; display:none; align-items:center; justify-content:center; z-index:2; }
.tpl-card.selected .tpl-card-check { display:flex; }
.tpl-thumb { height:120px; display:flex; align-items:center; justify-content:center; }
.tpl-info { padding:12px 14px 14px; border-top:1px solid #F1F5F9; }
.tpl-name  { font-size:13px; font-weight:700; color:#0F172A; margin-bottom:2px; }
.tpl-desc  { font-size:11px; color:#64748B; }
.tpl-badge { display:inline-block; font-size:9px; font-weight:700; padding:2px 8px; border-radius:20px; margin-top:6px; }
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
    <div class="cps-tab" data-tab="templates">
        <i class="ti ti-layout-grid-add"></i> Templates Design
    </div>
</div>

{{-- Tab body --}}
<div class="cps-body">
<form id="careerSettingsForm" method="POST"
      action="{{ route('service_company.career.page.settings.save') }}"
      enctype="multipart/form-data">
@csrf

    {{-- Tab Panels --}}
    @include('service_company.career-page.tabs.general')
    @include('service_company.career-page.tabs.hero')
    @include('service_company.career-page.tabs.company')
    @include('service_company.career-page.tabs.benefits')
    @include('service_company.career-page.tabs.jobs')
    @include('service_company.career-page.tabs.seo')
    @include('service_company.career-page.tabs.integrations')
    @include('service_company.career-page.tabs.advanced')
    @include('service_company.career-page.tabs.templates')

</form>{{-- careerSettingsForm --}}
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

/* ── Company Info logo preview ── */
function ciPreviewLogo(input){
    if(input.files && input.files[0]){
        var reader = new FileReader();
        reader.onload = function(e){
            var box = document.getElementById('ciLogoBox');
            box.innerHTML = '<img src="'+e.target.result+'" class="ci-logo-img" style="width:64px;height:64px;">';
            document.getElementById('ciLogoName').textContent = input.files[0].name;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function ciDelLogo(){
    document.getElementById('ciLogoBox').innerHTML = '<i class="ti ti-building" style="font-size:24px;color:#3B82F6;"></i>';
    document.getElementById('ciLogoName').textContent = 'No logo uploaded';
}

/* ── Add company highlight ── */
function ciAddHighlight(){
    var label = prompt('Highlight label (e.g. Offices):');
    if(!label || !label.trim()) return;
    var val = prompt('Value (e.g. 10+):');
    if(!val) val = '';
    var list = document.getElementById('ciHighlightsList');
    var row = document.createElement('div');
    row.className = 'ci-hl-row';
    row.innerHTML = '<div class="ci-hl-left"><div class="ci-hl-icon" style="background:#EFF6FF;">'
        +'<i class="ti ti-star" style="font-size:15px;color:#2563EB;"></i></div>'
        +'<div><div class="ci-hl-label">'+label.trim()+'</div></div></div>'
        +'<div style="display:flex;align-items:center;gap:10px;"><div class="ci-hl-val">'+val.trim()+'</div>'
        +'<button type="button" class="ci-hl-del" onclick="this.closest(\'.ci-hl-row\').remove()">'
        +'<i class="ti ti-trash" style="font-size:12px;"></i></button></div>';
    list.appendChild(row);
}

/* ── Advanced: custom code tab switch ── */
function switchCodeTab(el, targetId){
    document.querySelectorAll('.adv-code-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    ['adv-code-header','adv-code-footer','adv-code-body'].forEach(function(id){
        var el = document.getElementById(id);
        if(el) el.style.display = id===targetId ? 'block' : 'none';
    });
}

/* ── Integration category tabs ── */
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.int-tab').forEach(function(tab){
        tab.addEventListener('click', function(){
            document.querySelectorAll('.int-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

/* ── SEO keyword tag input ── */
function seoAddKw(e){
    if(e.key === 'Enter' || e.key === ','){
        e.preventDefault();
        var input = document.getElementById('seoKwInput');
        var val = input.value.replace(',','').trim();
        if(!val) return;
        var wrap = document.getElementById('seoKeywordsWrap');
        var tag = document.createElement('span');
        tag.className = 'seo-tag';
        tag.innerHTML = val + ' <span class="seo-tag-del" onclick="this.parentElement.remove()">×</span>';
        wrap.insertBefore(tag, input);
        input.value = '';
        // update search engine preview title live
        var titleEl = document.getElementById('seoMetaTitle');
        if(titleEl) document.getElementById('seoPreviewTitle').textContent = titleEl.value;
    }
}
/* Live-update SEO preview */
document.addEventListener('DOMContentLoaded', function(){
    var t = document.getElementById('seoMetaTitle');
    var d = document.getElementById('seoMetaDesc');
    if(t) t.addEventListener('input', function(){ document.getElementById('seoPreviewTitle').textContent = this.value; });
    if(d) d.addEventListener('input', function(){ document.getElementById('seoPreviewDesc').textContent = this.value; });
});
/* Add social link row */
function seoAddSocialRow(){
    var list = document.getElementById('seoSocialList');
    var row = document.createElement('div');
    row.className = 'seo-social-row';
    row.innerHTML = '<div class="seo-social-icon" style="background:#F8FAFC;"><i class="ti ti-share" style="color:#64748B;font-size:16px;"></i></div>'
        +'<input type="text" class="seo-social-input" style="width:90px;flex:none;" placeholder="Platform" value="">'
        +'<input type="url" class="seo-social-input" placeholder="https://" value="">'
        +'<label class="cps-switch" style="flex-shrink:0;"><input type="checkbox" value="1" checked><span class="cps-slider"></span></label>';
    list.appendChild(row);
}

/* ── Benefits display style ── */
function selectBnStyle(key, el){
    document.querySelectorAll('.bn-style-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('bnStyleInput').value = key;
}

/* ── Add benefit row ── */
function bnAddBenefit(){
    var title = prompt('Benefit title:');
    if(!title || !title.trim()) return;
    var desc = prompt('Short description:') || '';
    var list = document.getElementById('bnBenefitsList');
    var row = document.createElement('div');
    row.className = 'bn-benefit-row';
    row.innerHTML = '<span class="bn-drag"><i class="ti ti-grip-vertical"></i></span>'
        +'<div class="bn-benefit-icon" style="background:#EFF6FF;"><i class="ti ti-star" style="font-size:18px;color:#2563EB;"></i></div>'
        +'<div class="bn-benefit-info"><div class="bn-benefit-title">'+title.trim()+'</div>'
        +'<div class="bn-benefit-desc">'+desc.trim()+'</div></div>'
        +'<div class="bn-benefit-actions">'
        +'<button type="button" class="bn-act-btn eye"><i class="ti ti-eye"></i></button>'
        +'<button type="button" class="bn-act-btn edit"><i class="ti ti-pencil"></i></button>'
        +'<button type="button" class="bn-act-btn del" onclick="this.closest(\'.bn-benefit-row\').remove()"><i class="ti ti-trash"></i></button>'
        +'</div>';
    list.appendChild(row);
}

/* ── Template Design tab helpers ── */
/* Per-template color + bg presets */
var tdTemplatePresets = {
    modern:    { primary:'#2563EB', secondary:'#1F2937', accent:'#10B981', bg:'#FFFFFF' },
    corporate: { primary:'#1F2937', secondary:'#374151', accent:'#3B82F6', bg:'#FFFFFF' },
    creative:  { primary:'#7C3AED', secondary:'#EC4899', accent:'#F59E0B', bg:'#FFFFFF' },
    minimal:   { primary:'#64748B', secondary:'#94A3B8', accent:'#2563EB', bg:'#F8FAFC' },
    tech:      { primary:'#38BDF8', secondary:'#1E293B', accent:'#818CF8', bg:'#0F172A' },
};

function tdSelectTemplate(key, el){
    document.querySelectorAll('.td-tpl-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('tdTemplateInput').value = key;

    /* Apply preset colors to the Customize Design inputs */
    var preset = tdTemplatePresets[key];
    if(!preset) return;
    var fields = {
        tdColorPrimary:   preset.primary,
        tdColorSecondary: preset.secondary,
        tdColorAccent:    preset.accent,
        tdColorBg:        preset.bg,
    };
    Object.keys(fields).forEach(function(id){
        var swatch = document.getElementById(id);
        var text   = document.getElementById(id + 'Text');
        if(swatch) swatch.value = fields[id];
        if(text)   text.value   = fields[id];
    });
    /* Reflect in live preview immediately */
    tdUpdatePreview();
}

function tdSwitchDesignTab(key, el){
    document.querySelectorAll('.td-design-nav-item').forEach(n => n.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.td-design-tab').forEach(t => t.style.display='none');
    var t = document.getElementById('td-design-'+key);
    if(t) t.style.display='block';
}

function tdSelectLayout(key, el){
    document.querySelectorAll('.td-layout-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('tdLayoutInput').value = key;
}

function tdSwitchDevice(device, el){
    document.querySelectorAll('.td-prev-device-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    var frame = document.getElementById('tdPreviewFrame');
    if(device==='desktop') frame.style.maxWidth='100%';
    else if(device==='tablet') frame.style.maxWidth='420px';
    else frame.style.maxWidth='260px';
}

function syncColorSwatch(swatchId, textInput){
    var val = textInput.value;
    if(/^#[0-9A-Fa-f]{6}$/.test(val)) document.getElementById(swatchId).value = val;
}

/* Live preview: apply current colors to preview frame */
function tdUpdatePreview(){
    var p = document.getElementById('tdColorPrimaryText').value   || '#2563EB';
    var s = document.getElementById('tdColorSecondaryText').value || '#1F2937';
    var a = document.getElementById('tdColorAccentText').value    || '#10B981';
    var b = document.getElementById('tdColorBgText').value        || '#FFFFFF';
    var frame = document.getElementById('tdPreviewFrame');
    if(!frame) return;
    frame.style.setProperty('--tdP', p);
    frame.style.setProperty('--tdS', s);
    frame.style.setProperty('--tdA', a);
    frame.style.background = b;
    /* update hero gradient */
    var hero = frame.querySelector('.td-prev-hero');
    if(hero) hero.style.background = 'linear-gradient(135deg,'+p+','+s+')';
    /* update nav button */
    var navBtn = frame.querySelector('.td-prev-nav-btn');
    if(navBtn) navBtn.style.background = p;
    /* update search button */
    var srchBtn = frame.querySelector('.td-prev-hero-search-btn');
    if(srchBtn) srchBtn.style.background = p;
    /* update CTA */
    var cta = frame.querySelector('.td-prev-cta');
    if(cta) cta.style.background = 'linear-gradient(135deg,'+p+','+s+')';
    var ctaBtn = frame.querySelector('.td-prev-cta-btn');
    if(ctaBtn) ctaBtn.style.color = p;
}

/* Preview uploaded asset inline */
function tdPreviewAsset(input, thumbId){
    if(!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e){
        var box = document.getElementById(thumbId);
        box.innerHTML = '<img src="'+e.target.result+'" style="max-width:60px;max-height:36px;object-fit:contain;">';
    };
    reader.readAsDataURL(input.files[0]);
}

/* Apply live preview colors on load */
document.addEventListener('DOMContentLoaded', function(){
    tdUpdatePreview();
});

/* ── Template select (legacy) ── */
function selectTemplate(key, el){
    document.querySelectorAll('.tpl-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    if(document.getElementById('pageTemplateInput')) document.getElementById('pageTemplateInput').value = key;
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
