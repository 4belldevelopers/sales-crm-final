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
.td-prev-hero-img { background:rgba(255,255,255,.1); border-radius:8px; height:100px; display:flex; align-items:center; justify-content:center; }
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

{{-- ════════════════ COMPANY INFO TAB ════════════════ --}}
<div class="cps-panel" id="panel-company">
<div class="row g-4">

    {{-- ── Left: Controls ── --}}
    <div class="col-lg-7">

        {{-- Company Information --}}
        <div class="cps-section">
            <div class="cps-section-title">Company Information</div>
            <div class="cps-section-sub">Add your company details that will be displayed on the career page.</div>

            {{-- Logo + Industry row --}}
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="cps-label">Company Logo <i class="ti ti-info-circle" style="font-size:11px;color:#94A3B8;"></i></label>
                    <div class="ci-logo-wrap">
                        <div class="ci-logo-placeholder" id="ciLogoBox">
                            <i class="ti ti-building" style="font-size:24px;color:#3B82F6;"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:12px;font-weight:600;color:#374151;" id="ciLogoName">No logo uploaded</div>
                            <div class="ci-logo-meta">PNG · 300 × 300px</div>
                            <div class="ci-logo-actions">
                                <label class="ci-change-btn" style="cursor:pointer;">
                                    Change
                                    <input type="file" name="ci_logo" accept="image/*" style="display:none;" onchange="ciPreviewLogo(this)">
                                </label>
                                <button type="button" class="ci-del-btn" onclick="ciDelLogo()">
                                    <i class="ti ti-trash" style="font-size:13px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <label class="cps-label">Industry</label>
                    <select class="ci-select" name="ci_industry">
                        <option value="">Select Industry</option>
                        @foreach(['Information Technology & Services','Software Development','Staffing & Recruiting','Human Resources','Financial Services','Healthcare','Education','E-Commerce','Manufacturing','Consulting'] as $ind)
                        <option value="{{ $ind }}">{{ $ind }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Company Name --}}
            <div class="mb-3">
                <label class="cps-label">Company Name <span style="color:#EF4444;">*</span></label>
                <input type="text" class="ci-input" name="ci_company_name"
                       value="{{ $companyName }}" placeholder="Enter company name">
            </div>

            {{-- Headquarters --}}
            <div class="mb-3">
                <label class="cps-label">Headquarters</label>
                <input type="text" class="ci-input" name="ci_headquarters"
                       placeholder="e.g. Gurugram, Haryana, India">
            </div>

            {{-- Tagline --}}
            <div class="mb-3">
                <label class="cps-label">Tagline / Short Description <span style="color:#EF4444;">*</span></label>
                <textarea class="ci-textarea" name="ci_tagline" maxlength="100" rows="2"
                          id="ciTagline" placeholder="e.g. Building the future of recruitment with technology and trust."
                          oninput="document.getElementById('ciTaglineCount').textContent=this.value.length+'/100';"></textarea>
                <div style="display:flex;justify-content:flex-end;">
                    <span class="ci-char-count" id="ciTaglineCount">0/100</span>
                </div>
            </div>

            {{-- Website + LinkedIn --}}
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="cps-label">Company Website</label>
                    <div class="ci-input-icon">
                        <i class="ti ti-link ci-icon"></i>
                        <input type="url" class="ci-input" name="ci_website" placeholder="https://www.yoursite.com">
                        <i class="ti ti-external-link ci-icon-right" style="font-size:13px;"></i>
                    </div>
                </div>
                <div class="col-6">
                    <label class="cps-label">LinkedIn Page</label>
                    <div class="ci-input-icon">
                        <i class="ti ti-brand-linkedin ci-icon" style="color:#0077B5;"></i>
                        <input type="url" class="ci-input" name="ci_linkedin" placeholder="https://linkedin.com/company/...">
                        <i class="ti ti-brand-linkedin ci-icon-right" style="color:#0077B5;font-size:13px;"></i>
                    </div>
                </div>
            </div>

            {{-- Founded Year + Company Size --}}
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="cps-label">Founded Year</label>
                    <input type="number" class="ci-input" name="ci_founded_year"
                           min="1900" max="2099" placeholder="e.g. 2016">
                </div>
                <div class="col-6">
                    <label class="cps-label">Company Size</label>
                    <select class="ci-select" name="ci_company_size">
                        @foreach(['1 - 10 Employees','11 - 50 Employees','51 - 200 Employees','201 - 500 Employees','501 - 1000 Employees','1001 - 5000 Employees','5001+ Employees'] as $sz)
                        <option value="{{ $sz }}">{{ $sz }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- X (Twitter) --}}
            <div>
                <label class="cps-label">X (Twitter) Handle <span style="font-size:10px;color:#94A3B8;font-weight:400;">(Optional)</span></label>
                <div class="ci-input-icon">
                    <i class="ti ti-brand-x ci-icon"></i>
                    <input type="url" class="ci-input" name="ci_twitter" placeholder="https://x.com/yourhandle">
                    <i class="ti ti-brand-x ci-icon-right" style="font-size:13px;"></i>
                </div>
            </div>
        </div>

        {{-- Company Description --}}
        <div class="cps-section">
            <div class="cps-section-title">Company Description</div>
            <div class="cps-section-sub">Tell candidates about your company, mission, vision and what makes you unique.</div>

            {{-- About Us --}}
            <div class="mb-3">
                <label class="cps-label">About Us <span style="color:#EF4444;">*</span></label>
                <div class="ci-rich-toolbar">
                    <select style="height:26px;border:1px solid #E2E8F0;border-radius:5px;font-size:11px;color:#374151;background:#fff;padding:0 6px;cursor:pointer;">
                        <option>Paragraph</option><option>Heading 1</option><option>Heading 2</option>
                    </select>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Bold"><b>B</b></button>
                    <button type="button" class="ci-rich-btn" title="Italic"><i>I</i></button>
                    <button type="button" class="ci-rich-btn" title="Underline"><u>U</u></button>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Bullet list"><i class="ti ti-list" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Ordered list"><i class="ti ti-list-numbers" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Outdent"><i class="ti ti-indent-decrease" style="font-size:13px;"></i></button>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Align left"><i class="ti ti-align-left" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Align center"><i class="ti ti-align-center" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Align right"><i class="ti ti-align-right" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Justify"><i class="ti ti-align-justified" style="font-size:13px;"></i></button>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Link"><i class="ti ti-link" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Image"><i class="ti ti-photo" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Table"><i class="ti ti-table" style="font-size:13px;"></i></button>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Undo"><i class="ti ti-arrow-back-up" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Redo"><i class="ti ti-arrow-forward-up" style="font-size:13px;"></i></button>
                </div>
                <textarea class="ci-rich-textarea" name="ci_about" maxlength="2000" id="ciAbout"
                          oninput="document.getElementById('ciAboutCount').textContent=this.value.length+'/2000';"
                          placeholder="Describe your company..."></textarea>
                <div style="display:flex;justify-content:flex-end;">
                    <span class="ci-char-count" id="ciAboutCount">0/2000</span>
                </div>
            </div>

            {{-- Mission + Vision --}}
            <div class="row g-3">
                <div class="col-6">
                    <label class="cps-label">Mission</label>
                    <textarea class="ci-textarea" name="ci_mission" maxlength="200" id="ciMission" rows="3"
                              placeholder="e.g. To simplify recruitment and empower organizations to build great teams."
                              oninput="document.getElementById('ciMissionCount').textContent=this.value.length+'/200';"></textarea>
                    <div style="display:flex;justify-content:flex-end;">
                        <span class="ci-char-count" id="ciMissionCount">0/200</span>
                    </div>
                </div>
                <div class="col-6">
                    <label class="cps-label">Vision</label>
                    <textarea class="ci-textarea" name="ci_vision" maxlength="200" id="ciVision" rows="3"
                              placeholder="e.g. To be the world's most trusted recruitment technology platform."
                              oninput="document.getElementById('ciVisionCount').textContent=this.value.length+'/200';"></textarea>
                    <div style="display:flex;justify-content:flex-end;">
                        <span class="ci-char-count" id="ciVisionCount">0/200</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Right: Preview + Highlights ── --}}
    <div class="col-lg-5">

        {{-- Company Preview --}}
        <div class="cps-sidebar-card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 18px 12px;border-bottom:1px solid #F1F5F9;">
                <h4 style="margin:0 0 2px;">Company Preview</h4>
                <p class="sub" style="margin:0;">See how your company information will appear on the career page.</p>
            </div>
            <div class="ci-prev-card" style="border:none;border-radius:0;">
                <div class="ci-prev-banner">
                    <div class="ci-prev-logo-circle">
                        <i class="ti ti-building" style="font-size:22px;color:#3B82F6;" id="ciPrevLogoIcon"></i>
                    </div>
                </div>
                <div class="ci-prev-body">
                    <div class="ci-prev-name">
                        {{ $companyName }}
                        <i class="ti ti-circle-check-filled" style="font-size:14px;color:#2563EB;"></i>
                    </div>
                    <div class="ci-prev-tagline">Building the future of recruitment with technology and trust.</div>
                    <div class="ci-prev-meta">
                        <div class="ci-prev-meta-item"><i class="ti ti-map-pin" style="font-size:11px;"></i> Gurugram, Haryana, India</div>
                        <div class="ci-prev-meta-item"><i class="ti ti-users" style="font-size:11px;"></i> 201 – 500 Employees</div>
                        <div class="ci-prev-meta-item"><i class="ti ti-calendar" style="font-size:11px;"></i> Founded 2016</div>
                    </div>
                    <div class="ci-prev-social">
                        <span class="ci-prev-meta-item" style="font-size:9px;color:#2563EB;">
                            <i class="ti ti-world" style="font-size:10px;"></i> www.4bell.com
                        </span>
                        <i class="ti ti-brand-linkedin" style="font-size:14px;color:#0077B5;"></i>
                        <i class="ti ti-brand-x" style="font-size:14px;color:#0F172A;"></i>
                    </div>
                    <div class="ci-prev-about-title">About Us</div>
                    <div class="ci-prev-about-text">
                        {{ $companyName }} is a recruitment technology company on a mission to simplify hiring for businesses and create meaningful career opportunities for talent across the globe.<br><br>
                        Our AI-powered platform connects the right people with the right opportunities – faster, smarter and more efficiently.
                    </div>
                    <div class="ci-prev-stats">
                        <div>
                            <div class="ci-prev-stat-n">2016</div>
                            <div class="ci-prev-stat-l">Founded</div>
                        </div>
                        <div>
                            <div class="ci-prev-stat-n">5+</div>
                            <div class="ci-prev-stat-l">Countries</div>
                        </div>
                        <div>
                            <div class="ci-prev-stat-n">500+</div>
                            <div class="ci-prev-stat-l">Employees</div>
                        </div>
                        <div>
                            <div class="ci-prev-stat-n">1000+</div>
                            <div class="ci-prev-stat-l">Clients</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Company Details (Highlights) --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 3px;">Company Details (Highlights)</h4>
            <p class="sub">Add key highlights to build trust and credibility.</p>
            <div id="ciHighlightsList">
                @php
                $ciHls = [
                    ['icon'=>'ti-users',   'bg'=>'#EFF6FF','ic'=>'#2563EB','label'=>'Employees',        'val'=>'500+'],
                    ['icon'=>'ti-world',   'bg'=>'#F0FDF4','ic'=>'#16A34A','label'=>'Countries',         'val'=>'5+'],
                    ['icon'=>'ti-users-group','bg'=>'#FFF7ED','ic'=>'#F97316','label'=>'Clients',       'val'=>'1000+'],
                    ['icon'=>'ti-calendar','bg'=>'#FAF5FF','ic'=>'#7C3AED','label'=>'Years in Business','val'=>'8+'],
                ];
                @endphp
                @foreach($ciHls as $hl)
                <div class="ci-hl-row">
                    <div class="ci-hl-left">
                        <div class="ci-hl-icon" style="background:{{ $hl['bg'] }};">
                            <i class="ti {{ $hl['icon'] }}" style="font-size:15px;color:{{ $hl['ic'] }};"></i>
                        </div>
                        <div>
                            <div class="ci-hl-label">{{ $hl['label'] }}</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="ci-hl-val">{{ $hl['val'] }}</div>
                        <button type="button" class="ci-hl-del" onclick="this.closest('.ci-hl-row').remove()">
                            <i class="ti ti-trash" style="font-size:12px;"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="ci-add-hl" onclick="ciAddHighlight()">
                <i class="ti ti-plus" style="font-size:13px;"></i> Add Highlight
            </button>
        </div>

    </div>
</div>

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>
</div>{{-- end panel-company --}}

{{-- ════════════════ BENEFITS TAB ════════════════ --}}
<div class="cps-panel" id="panel-benefits">
<div class="row g-4">

    {{-- ── Left: Controls ── --}}
    <div class="col-lg-7">

        {{-- Benefits Section Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Benefits Section Settings</div>
            <div class="cps-section-sub">Showcase the perks and advantages of working at your company.</div>

            <div class="row g-3 mb-3">
                {{-- Enable toggle --}}
                <div class="col-6">
                    <label class="cps-label">Enable Benefits Section</label>
                    <div class="bn-toggle-row" style="margin-top:6px;">
                        <label class="cps-switch">
                            <input type="checkbox" name="benefits_enabled" value="1" checked>
                            <span class="cps-slider"></span>
                        </label>
                        <span class="bn-toggle-label">Display benefits section on the career page</span>
                    </div>
                </div>
                {{-- Section Title --}}
                <div class="col-6">
                    <label class="cps-label">Section Title</label>
                    <input type="text" class="bn-input" name="benefits_title" id="bnTitle"
                           maxlength="60" value="Why Work With Us"
                           oninput="document.getElementById('bnTitleCount').textContent=this.value.length+'/60';">
                    <div style="display:flex;justify-content:flex-end;">
                        <span class="bn-char-count" id="bnTitleCount">17/60</span>
                    </div>
                </div>
            </div>

            {{-- Section Description --}}
            <div class="mb-4">
                <label class="cps-label">Section Description</label>
                <textarea class="bn-textarea" name="benefits_description" id="bnDesc" maxlength="200"
                          oninput="document.getElementById('bnDescCount').textContent=this.value.length+'/200';"
                          placeholder="e.g. We believe in building a culture where people can do their best work and grow together.">We believe in building a culture where people can do their best work and grow together. Here are some of the benefits our team enjoys.</textarea>
                <div style="display:flex;justify-content:flex-end;">
                    <span class="bn-char-count" id="bnDescCount">117/200</span>
                </div>
            </div>

            <div class="row g-3">
                {{-- Display Style --}}
                <div class="col-7">
                    <label class="cps-label">Display Style</label>
                    <p style="font-size:11px;color:#94A3B8;margin:0 0 10px;">Choose how benefits are displayed</p>
                    <div class="bn-style-grid">
                        <div class="bn-style-card selected" onclick="selectBnStyle('grid',this)">
                            <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                            <i class="ti ti-layout-grid bn-style-icon"></i>
                            <div class="bn-style-name">Grid View</div>
                            <div class="bn-style-desc">Best for most cases</div>
                        </div>
                        <div class="bn-style-card" onclick="selectBnStyle('icon-list',this)">
                            <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                            <i class="ti ti-list bn-style-icon"></i>
                            <div class="bn-style-name">Icon List</div>
                            <div class="bn-style-desc">Simple list layout</div>
                        </div>
                        <div class="bn-style-card" onclick="selectBnStyle('card-row',this)">
                            <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                            <i class="ti ti-layout-columns bn-style-icon"></i>
                            <div class="bn-style-name">Card Row</div>
                            <div class="bn-style-desc">Horizontal scroll</div>
                        </div>
                        <div class="bn-style-card" onclick="selectBnStyle('two-col',this)">
                            <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                            <i class="ti ti-columns bn-style-icon"></i>
                            <div class="bn-style-name">Two Column</div>
                            <div class="bn-style-desc">Compact layout</div>
                        </div>
                    </div>
                    <input type="hidden" name="benefits_display_style" id="bnStyleInput" value="grid">
                </div>

                {{-- Number of Benefits to Show --}}
                <div class="col-5">
                    <label class="cps-label">Number of Benefits to Show</label>
                    <p style="font-size:11px;color:#94A3B8;margin:0 0 10px;">Limit the number of benefits visible on the career page</p>
                    <select class="bn-select" name="benefits_count">
                        @foreach([4,6,8,10,12] as $n)
                        <option value="{{ $n }}" {{ $n==8?'selected':'' }}>{{ $n }} Benefits</option>
                        @endforeach
                    </select>
                    <p style="font-size:11px;color:#94A3B8;margin:8px 0 0;">You can add more benefits, but only selected number will be shown.</p>
                </div>
            </div>
        </div>

        {{-- Manage Benefits --}}
        <div class="cps-section">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                <div>
                    <div class="cps-section-title" style="margin:0;">Manage Benefits</div>
                    <div class="cps-section-sub" style="margin:2px 0 0;">Add, edit and reorder benefits. Drag to reorder the items.</div>
                </div>
                <button type="button" class="bn-add-btn" onclick="bnAddBenefit()">
                    <i class="ti ti-plus" style="font-size:13px;"></i> Add Benefit
                </button>
            </div>

            <div id="bnBenefitsList" style="margin-top:16px;">
                @php
                $bnBenefits = [
                    ['title'=>'Health Insurance',      'desc'=>'Comprehensive health coverage for you and your family',    'icon'=>'ti-heart',        'bg'=>'#DCFCE7','ic'=>'#16A34A'],
                    ['title'=>'Flexible Work',         'desc'=>'Work from anywhere with flexible hours',                   'icon'=>'ti-layout-grid',  'bg'=>'#EFF6FF','ic'=>'#2563EB'],
                    ['title'=>'Learning & Development','desc'=>'Continuous learning with courses and certifications',       'icon'=>'ti-book',         'bg'=>'#FAF5FF','ic'=>'#7C3AED'],
                    ['title'=>'Career Growth',         'desc'=>'Clear career path and growth opportunities',               'icon'=>'ti-trending-up',  'bg'=>'#FFF7ED','ic'=>'#F97316'],
                    ['title'=>'Paid Time Off',         'desc'=>'Generous leave policy for work-life balance',              'icon'=>'ti-umbrella',     'bg'=>'#FFF1F2','ic'=>'#E11D48'],
                ];
                @endphp
                @foreach($bnBenefits as $bn)
                <div class="bn-benefit-row">
                    <span class="bn-drag"><i class="ti ti-grip-vertical"></i></span>
                    <div class="bn-benefit-icon" style="background:{{ $bn['bg'] }};">
                        <i class="ti {{ $bn['icon'] }}" style="font-size:18px;color:{{ $bn['ic'] }};"></i>
                    </div>
                    <div class="bn-benefit-info">
                        <div class="bn-benefit-title">{{ $bn['title'] }}</div>
                        <div class="bn-benefit-desc">{{ $bn['desc'] }}</div>
                    </div>
                    <div class="bn-benefit-actions">
                        <button type="button" class="bn-act-btn eye" title="Toggle visibility">
                            <i class="ti ti-eye"></i>
                        </button>
                        <button type="button" class="bn-act-btn edit" title="Edit">
                            <i class="ti ti-pencil"></i>
                        </button>
                        <button type="button" class="bn-act-btn del" title="Delete"
                                onclick="this.closest('.bn-benefit-row').remove()">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Tip bar --}}
            <div class="bn-tip-bar">
                <i class="ti ti-info-circle" style="font-size:14px;flex-shrink:0;"></i>
                Tip: Keep benefit titles short and descriptions clear for better engagement.
            </div>
        </div>

    </div>

    {{-- ── Right: Preview + Tips ── --}}
    <div class="col-lg-5">

        {{-- Live Preview --}}
        <div class="cps-sidebar-card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 18px 12px;border-bottom:1px solid #F1F5F9;">
                <h4 style="margin:0 0 2px;">Live Preview</h4>
                <p class="sub" style="margin:0;">See how the benefits section will appear on your career page.</p>
            </div>
            <div style="padding:14px;">
                <div class="bn-prev-wrap">
                    <div class="bn-prev-nav">
                        <div class="bn-prev-nav-logo">
                            <i class="ti ti-building" style="font-size:12px;color:#2563EB;"></i>
                            {{ $companyName }}
                        </div>
                        <div class="bn-prev-nav-links">
                            <span class="bn-prev-nav-link">Jobs</span>
                            <span class="bn-prev-nav-link">Teams</span>
                            <span class="bn-prev-nav-link">Life at 4Bell</span>
                            <span class="bn-prev-nav-link">Benefits</span>
                            <span class="bn-prev-nav-link">About Us</span>
                        </div>
                        <div class="bn-prev-nav-btn">View Openings</div>
                    </div>
                    <div class="bn-prev-section">
                        <div class="bn-prev-title">Why Work With Us</div>
                        <div class="bn-prev-desc">We believe in building a culture where people can do their best work and grow together. Here are some of the benefits our team enjoys.</div>
                        <div class="bn-prev-grid">
                            @php
                            $prevCards = [
                                ['icon'=>'ti-heart',       'bg'=>'#DCFCE7','ic'=>'#16A34A','name'=>'Health Insurance',      'desc'=>'Comprehensive health coverage for you and your family'],
                                ['icon'=>'ti-layout-grid', 'bg'=>'#EFF6FF','ic'=>'#2563EB','name'=>'Flexible Work',         'desc'=>'Work from anywhere with flexible hours'],
                                ['icon'=>'ti-book',        'bg'=>'#FAF5FF','ic'=>'#7C3AED','name'=>'Learning & Development','desc'=>'Continuous learning with courses and certifications'],
                                ['icon'=>'ti-trending-up', 'bg'=>'#FFF7ED','ic'=>'#F97316','name'=>'Career Growth',         'desc'=>'Clear career path and growth opportunities'],
                                ['icon'=>'ti-umbrella',    'bg'=>'#FFF1F2','ic'=>'#E11D48','name'=>'Paid Time Off',         'desc'=>'Generous leave policy for work-life balance'],
                                ['icon'=>'ti-users-group', 'bg'=>'#F0FDF4','ic'=>'#059669','name'=>'Team Culture',          'desc'=>'Collaborative, inclusive and supportive environment'],
                                ['icon'=>'ti-heart-rate',  'bg'=>'#EFF6FF','ic'=>'#0EA5E9','name'=>'Wellness Program',      'desc'=>'Mental and physical wellness support'],
                                ['icon'=>'ti-gift',        'bg'=>'#FFFBEB','ic'=>'#D97706','name'=>'Performance Bonus',     'desc'=>'Rewarding performance and contributions'],
                            ];
                            @endphp
                            @foreach($prevCards as $pc)
                            <div class="bn-prev-card">
                                <div class="bn-prev-card-icon" style="background:{{ $pc['bg'] }};">
                                    <i class="ti {{ $pc['icon'] }}" style="font-size:14px;color:{{ $pc['ic'] }};"></i>
                                </div>
                                <div class="bn-prev-card-name">{{ $pc['name'] }}</div>
                                <div class="bn-prev-card-desc">{{ $pc['desc'] }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="bn-prev-link">
                            View Full Career Page <i class="ti ti-external-link" style="font-size:9px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 14px;">Tips for Great Benefits Section</h4>
            <div class="bn-tips-list">
                <div class="bn-tip-item">
                    <i class="ti ti-star" style="color:#F59E0B;"></i>
                    Highlight 6-8 key benefits that matter most to candidates.
                </div>
                <div class="bn-tip-item">
                    <i class="ti ti-circle-check" style="color:#2563EB;"></i>
                    Use simple icons and short descriptions.
                </div>
                <div class="bn-tip-item">
                    <i class="ti ti-heart" style="color:#E11D48;"></i>
                    Focus on unique benefits that differentiate your company.
                </div>
                <div class="bn-tip-item">
                    <i class="ti ti-refresh" style="color:#16A34A;"></i>
                    Keep the section updated and relevant.
                </div>
                <div class="bn-tip-item">
                    <i class="ti ti-chart-bar" style="color:#7C3AED;"></i>
                    Benefits help improve candidate trust and engagement.
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>
</div>{{-- end panel-benefits --}}

{{-- ════════════════ JOB SETTINGS TAB ════════════════ --}}
<div class="cps-panel" id="panel-jobs">
<div class="row g-4">

    {{-- ── Left ── --}}
    <div class="col-lg-7">

        {{-- Job Listing Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Job Listing Settings</div>
            <div class="cps-section-sub">Configure how jobs are displayed and apply process for candidates.</div>

            <div class="row g-4">
                {{-- Left col: dropdowns --}}
                <div class="col-6">
                    <div class="mb-4">
                        <label class="cps-label">Job Display Style</label>
                        <select class="js-select" name="js_display_style">
                            <option value="card" selected>Card View</option>
                            <option value="list">List View</option>
                            <option value="compact">Compact View</option>
                        </select>
                        <div class="js-hint">Choose how job openings are displayed on your career page.</div>
                    </div>
                    <div class="mb-4">
                        <label class="cps-label">Jobs Per Page</label>
                        <select class="js-select" name="js_per_page">
                            @foreach([6,9,12,15,20] as $n)
                            <option value="{{ $n }}" {{ $n==12?'selected':'' }}>{{ $n }} Jobs</option>
                            @endforeach
                        </select>
                        <div class="js-hint">Number of jobs to display per page.</div>
                    </div>
                    <div>
                        <label class="cps-label">Default Job Sort By</label>
                        <select class="js-select" name="js_sort_by">
                            <option value="newest" selected>Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="relevant">Most Relevant</option>
                            <option value="salary">Highest Salary</option>
                        </select>
                        <div class="js-hint">Choose the default sorting for job listings.</div>
                    </div>
                </div>

                {{-- Right col: toggles --}}
                <div class="col-6" style="display:flex;flex-direction:column;gap:20px;">
                    @php
                    $jsToggles = [
                        ['name'=>'js_show_posted_date',  'label'=>'Show Job Posted Date',       'desc'=>'Display the job posted date on job cards.',      'checked'=>true],
                        ['name'=>'js_show_experience',   'label'=>'Show Experience on Job Card', 'desc'=>'Display experience required on job cards.',      'checked'=>true],
                        ['name'=>'js_show_salary',       'label'=>'Show Salary on Job Card',     'desc'=>'Display salary or salary range on job cards.',   'checked'=>true],
                        ['name'=>'js_show_tags',         'label'=>'Show Job Tags / Skills',      'desc'=>'Display skills and tags on job cards.',          'checked'=>true],
                    ];
                    @endphp
                    @foreach($jsToggles as $jt)
                    <div class="js-toggle-row">
                        <div>
                            <div class="js-toggle-label">{{ $jt['label'] }}</div>
                            <div class="js-toggle-desc">{{ $jt['desc'] }}</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="{{ $jt['name'] }}" value="1" {{ $jt['checked']?'checked':'' }}>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Apply Process Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Apply Process Settings</div>
            <div class="cps-section-sub">Manage the application process and required information.</div>

            <div class="row g-4">
                {{-- Apply Option radios --}}
                <div class="col-6">
                    <label class="cps-label">Apply Option</label>
                    <div class="js-radio-group" style="margin-top:8px;">
                        <label class="js-radio-item">
                            <input type="radio" name="js_apply_option" value="resume" checked>
                            <div>
                                <div class="js-radio-label">Apply with Resume</div>
                                <div class="js-radio-desc">Candidates can apply by uploading resume.</div>
                            </div>
                        </label>
                        <label class="js-radio-item">
                            <input type="radio" name="js_apply_option" value="profile">
                            <div>
                                <div class="js-radio-label">Apply with Profile</div>
                                <div class="js-radio-desc">Candidates must fill the application form.</div>
                            </div>
                        </label>
                        <label class="js-radio-item">
                            <input type="radio" name="js_apply_option" value="both">
                            <div>
                                <div class="js-radio-label">Both Resume & Profile</div>
                                <div class="js-radio-desc">Candidates can choose either option.</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Right: toggles + redirect --}}
                <div class="col-6" style="display:flex;flex-direction:column;gap:18px;">
                    <div class="js-toggle-row">
                        <div>
                            <div class="js-toggle-label">Require Login to Apply</div>
                            <div class="js-toggle-desc">Candidates must login or register to apply.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="js_require_login" value="1">
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    <div class="js-toggle-row">
                        <div>
                            <div class="js-toggle-label">Auto Acknowledgement Email</div>
                            <div class="js-toggle-desc">Send confirmation email to candidates after applying.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="js_auto_ack_email" value="1" checked>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    <div>
                        <label class="cps-label">Redirect After Apply</label>
                        <select class="js-select" name="js_redirect_after_apply" style="margin-top:4px;">
                            <option value="thank_you" selected>Thank You Page</option>
                            <option value="job_list">Job Listings Page</option>
                            <option value="home">Career Home Page</option>
                            <option value="custom">Custom URL</option>
                        </select>
                        <div class="js-hint">Choose where candidates will be redirected.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Job Detail Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Job Detail Settings</div>
            <div class="cps-section-sub">Choose what information appears on the job detail page.</div>
            <div class="js-chk-grid" style="margin-top:4px;">
                @php
                $jsChks = [
                    ['name'=>'js_show_description',   'label'=>'Show Job Description',      'checked'=>true],
                    ['name'=>'js_show_key_resp',       'label'=>'Show Key Responsibilities', 'checked'=>true],
                    ['name'=>'js_show_requirements',   'label'=>'Show Requirements',         'checked'=>true],
                    ['name'=>'js_show_benefits',       'label'=>'Show Benefits',             'checked'=>true],
                    ['name'=>'js_show_skills',         'label'=>'Show Skills / Tags',        'checked'=>true],
                    ['name'=>'js_show_salary_range',   'label'=>'Show Salary Range',         'checked'=>true],
                    ['name'=>'js_show_company_info',   'label'=>'Show Company Info',         'checked'=>true],
                    ['name'=>'js_show_similar_jobs',   'label'=>'Show Similar Jobs',         'checked'=>true],
                    ['name'=>'js_show_deadline',       'label'=>'Show Application Deadline', 'checked'=>false],
                    ['name'=>'js_show_department',     'label'=>'Show Department',           'checked'=>false],
                    ['name'=>'js_show_job_id',         'label'=>'Show Job ID',               'checked'=>false],
                ];
                @endphp
                @foreach($jsChks as $jc)
                <label class="js-chk-item">
                    <input type="checkbox" name="{{ $jc['name'] }}" value="1" {{ $jc['checked']?'checked':'' }}>
                    {{ $jc['label'] }}
                </label>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── Right: Preview + Additional Options ── --}}
    <div class="col-lg-5">

        {{-- Live Preview --}}
        <div class="cps-sidebar-card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 18px 12px;border-bottom:1px solid #F1F5F9;">
                <h4 style="margin:0 0 2px;">Live Preview</h4>
                <p class="sub" style="margin:0;">See how job listings will appear on your career page.</p>
            </div>
            <div style="padding:12px;">
                <div class="js-prev-wrap">
                    <div class="js-prev-nav">
                        <div class="js-prev-nav-logo">
                            <i class="ti ti-building" style="font-size:11px;color:#2563EB;"></i>
                            {{ $companyName }}
                        </div>
                        <div class="js-prev-nav-links">
                            <span class="js-prev-nav-link">Jobs</span>
                            <span class="js-prev-nav-link">Teams</span>
                            <span class="js-prev-nav-link">Life at 4Bell</span>
                            <span class="js-prev-nav-link">Benefits</span>
                            <span class="js-prev-nav-link">About Us</span>
                        </div>
                        <div class="js-prev-nav-btn">View Openings</div>
                    </div>
                    <div class="js-prev-body">

                        {{-- Job Card 1 --}}
                        <div class="js-job-card">
                            <span class="js-job-bookmark"><i class="ti ti-bookmark"></i></span>
                            <div class="js-job-badge">New</div>
                            <div class="js-job-title">Senior Backend Developer</div>
                            <div class="js-job-meta">
                                <span><i class="ti ti-briefcase" style="font-size:8px;"></i> Engineering</span>
                                <span>•</span>
                                <span><i class="ti ti-map-pin" style="font-size:8px;"></i> Gurugram, India</span>
                                <span>•</span>
                                <span>Full-time</span>
                                <span style="margin-left:auto;font-size:8px;color:#94A3B8;">Posted 2 days ago</span>
                            </div>
                            <div class="js-job-info">
                                <span><i class="ti ti-clock" style="font-size:8px;"></i> 5-7 Years</span>
                                <span><i class="ti ti-currency-rupee" style="font-size:8px;"></i> ₹18 – ₹25 LPA</span>
                            </div>
                            <div class="js-job-desc">We are looking for a skilled Backend Developer to build scalable and high-performance</div>
                            <div class="js-job-tags">
                                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                    <span class="js-tag">PHP</span>
                                    <span class="js-tag">Laravel</span>
                                    <span class="js-tag">MySQL</span>
                                    <span class="js-tag">REST API</span>
                                    <span class="js-tag">AWS</span>
                                    <span class="js-tag" style="color:#2563EB;background:#EFF6FF;">+3 more</span>
                                </div>
                                <div class="js-job-btn">View Job</div>
                            </div>
                        </div>

                        {{-- Job Card 2 --}}
                        <div class="js-job-card">
                            <span class="js-job-bookmark"><i class="ti ti-bookmark"></i></span>
                            <div class="js-job-title" style="margin-bottom:2px;">UI/UX Designer</div>
                            <div class="js-job-meta">
                                <span><i class="ti ti-palette" style="font-size:8px;"></i> Design</span>
                                <span>•</span>
                                <span><i class="ti ti-world" style="font-size:8px;"></i> Remote</span>
                                <span>•</span>
                                <span>Full-time</span>
                                <span style="margin-left:auto;font-size:8px;color:#94A3B8;">Posted 5 days ago</span>
                            </div>
                            <div class="js-job-info">
                                <span><i class="ti ti-clock" style="font-size:8px;"></i> 2-4 Years</span>
                                <span><i class="ti ti-currency-rupee" style="font-size:8px;"></i> ₹8 – ₹12 LPA</span>
                            </div>
                            <div class="js-job-desc">Create intuitive and engaging user experiences for our web and mobile applications.</div>
                            <div class="js-job-tags">
                                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                    <span class="js-tag">Figma</span>
                                    <span class="js-tag">Adobe XD</span>
                                    <span class="js-tag">UI Design</span>
                                    <span class="js-tag">Wireframing</span>
                                    <span class="js-tag" style="color:#2563EB;background:#EFF6FF;">+2 more</span>
                                </div>
                                <div class="js-job-btn">View Job</div>
                            </div>
                        </div>

                        <div class="js-prev-link">
                            View Full Career Page <i class="ti ti-external-link" style="font-size:9px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Options --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 4px;">Additional Options</h4>
            @php
            $jsAddOpts = [
                ['icon'=>'ti-bookmark',  'bg'=>'#EFF6FF','ic'=>'#2563EB','title'=>'Enable Save Job',       'desc'=>'Allow candidates to save jobs for later.',      'name'=>'js_enable_save_job',  'checked'=>true],
                ['icon'=>'ti-share',     'bg'=>'#F0FDF4','ic'=>'#16A34A','title'=>'Enable Share Job',      'desc'=>'Allow candidates to share jobs with others.',   'name'=>'js_enable_share_job', 'checked'=>true],
                ['icon'=>'ti-printer',   'bg'=>'#FFF7ED','ic'=>'#F97316','title'=>'Enable Print Job',      'desc'=>'Allow candidates to print job details.',        'name'=>'js_enable_print_job', 'checked'=>false],
                ['icon'=>'ti-eye',       'bg'=>'#FAF5FF','ic'=>'#7C3AED','title'=>'Show Job View Count',   'desc'=>'Display number of views on job details.',       'name'=>'js_show_view_count',  'checked'=>false],
            ];
            @endphp
            @foreach($jsAddOpts as $ao)
            <div class="js-addopt-row">
                <div class="js-addopt-left">
                    <div class="js-addopt-icon" style="background:{{ $ao['bg'] }};">
                        <i class="ti {{ $ao['icon'] }}" style="font-size:16px;color:{{ $ao['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="js-addopt-title">{{ $ao['title'] }}</div>
                        <div class="js-addopt-desc">{{ $ao['desc'] }}</div>
                    </div>
                </div>
                <label class="cps-switch" style="flex-shrink:0;margin-top:4px;">
                    <input type="checkbox" name="{{ $ao['name'] }}" value="1" {{ $ao['checked']?'checked':'' }}>
                    <span class="cps-slider"></span>
                </label>
            </div>
            @endforeach
        </div>

    </div>
</div>

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>
</div>{{-- end panel-jobs --}}

{{-- ════════════════ SEO & SOCIAL TAB ════════════════ --}}
<div class="cps-panel" id="panel-seo">
<div class="row g-4">

    {{-- ── Left ── --}}
    <div class="col-lg-7">

        {{-- SEO Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">SEO Settings</div>
            <div class="cps-section-sub">Optimize your career page for search engines and social sharing.</div>

            <div class="row g-3">
                {{-- Meta Title --}}
                <div class="col-6">
                    <label class="cps-label">Meta Title</label>
                    <input type="text" class="seo-input" name="seo_meta_title" id="seoMetaTitle"
                           maxlength="60" value="Careers at 4Bell Technology | Join Our Team"
                           oninput="document.getElementById('seoTitleCount').textContent=this.value.length+'/60';">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:3px;">
                        <span class="seo-hint">This will appear as the title in search engine results.</span>
                        <span class="seo-char" id="seoTitleCount">45/60</span>
                    </div>
                </div>

                {{-- Meta Keywords --}}
                <div class="col-6">
                    <label class="cps-label">Meta Keywords <span style="font-size:10px;color:#94A3B8;font-weight:400;">(Optional)</span></label>
                    <div class="seo-tags-wrap" id="seoKeywordsWrap" onclick="document.getElementById('seoKwInput').focus()">
                        @foreach(['careers','jobs','hiring','work with us','join our team','4bell technology'] as $kw)
                        <span class="seo-tag">{{ $kw }} <span class="seo-tag-del" onclick="this.parentElement.remove()">×</span></span>
                        @endforeach
                        <input type="text" class="seo-tag-input" id="seoKwInput"
                               placeholder="Add keyword..." onkeydown="seoAddKw(event)">
                    </div>
                    <div class="seo-hint" style="margin-top:5px;">Add relevant keywords separated by commas.</div>
                </div>

                {{-- Meta Description --}}
                <div class="col-6">
                    <label class="cps-label">Meta Description</label>
                    <textarea class="seo-textarea" name="seo_meta_desc" id="seoMetaDesc" maxlength="160"
                              oninput="document.getElementById('seoDescCount').textContent=this.value.length+'/160';"
                              rows="4">Explore exciting career opportunities at 4Bell Technology. Join our mission to build innovative solutions and grow your career with a talented team.</textarea>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:3px;">
                        <span class="seo-hint">This will appear as the description in search engine results.</span>
                        <span class="seo-char" id="seoDescCount">146/160</span>
                    </div>
                </div>

                {{-- Robots Meta Tag --}}
                <div class="col-6">
                    <label class="cps-label">Robots Meta Tag</label>
                    <select class="seo-select" name="seo_robots" style="margin-top:2px;">
                        <option value="index,follow" selected>Index, Follow</option>
                        <option value="noindex,follow">No Index, Follow</option>
                        <option value="index,nofollow">Index, No Follow</option>
                        <option value="noindex,nofollow">No Index, No Follow</option>
                    </select>
                    <div class="seo-hint">Choose how search engines should index your page.</div>
                </div>

                {{-- URL Slug --}}
                <div class="col-6">
                    <label class="cps-label">URL Slug</label>
                    <div class="seo-slug-wrap">
                        <input type="text" class="seo-input" name="seo_slug" id="seoSlug"
                               value="careers" placeholder="careers">
                    </div>
                    <div class="seo-slug-url">
                        Your career page URL will be:
                        <a href="{{ $careerUrl }}" target="_blank">{{ $careerUrl }}</a>
                        <i class="ti ti-external-link" style="font-size:10px;margin-left:2px;"></i>
                    </div>
                </div>

                {{-- Canonical URL --}}
                <div class="col-6">
                    <label class="cps-label">Canonical URL <span style="font-size:10px;color:#94A3B8;font-weight:400;">(Optional)</span></label>
                    <input type="url" class="seo-input" name="seo_canonical"
                           placeholder="https://www.yoursite.com/careers" value="{{ $careerUrl }}">
                    <div class="seo-hint">Use canonical URL to prevent duplicate content issues.</div>
                </div>
            </div>
        </div>

        {{-- Social Media Links --}}
        <div class="cps-section">
            <div class="cps-section-title">Social Media Links</div>
            <div class="cps-section-sub">Add your social media profiles to display on the career page.</div>

            @php
            $seoSocials = [
                ['icon'=>'ti-brand-linkedin','bg'=>'#EFF6FF','ic'=>'#0077B5','label'=>'LinkedIn',    'name'=>'seo_linkedin',   'url'=>'https://www.linkedin.com/company/4bell','checked'=>true],
                ['icon'=>'ti-brand-x',       'bg'=>'#F8FAFC', 'ic'=>'#0F172A','label'=>'X (Twitter)', 'name'=>'seo_twitter',    'url'=>'https://twitter.com/4bell_tech',        'checked'=>true],
                ['icon'=>'ti-brand-facebook','bg'=>'#EFF6FF','ic'=>'#1877F2','label'=>'Facebook',    'name'=>'seo_facebook',   'url'=>'https://www.facebook.com/4belltechnology','checked'=>true],
                ['icon'=>'ti-brand-instagram','bg'=>'#FFF1F2','ic'=>'#E1306C','label'=>'Instagram',  'name'=>'seo_instagram',  'url'=>'https://www.instagram.com/4bell_tech',  'checked'=>true],
                ['icon'=>'ti-brand-youtube', 'bg'=>'#FFF1F2','ic'=>'#FF0000','label'=>'YouTube',     'name'=>'seo_youtube',    'url'=>'https://www.youtube.com/@4belltech',     'checked'=>true],
            ];
            @endphp

            <div id="seoSocialList">
                @foreach($seoSocials as $ss)
                <div class="seo-social-row">
                    <div class="seo-social-icon" style="background:{{ $ss['bg'] }};">
                        <i class="ti {{ $ss['icon'] }}" style="color:{{ $ss['ic'] }};"></i>
                    </div>
                    <div class="seo-social-label">{{ $ss['label'] }}</div>
                    <input type="url" class="seo-social-input" name="{{ $ss['name'] }}" value="{{ $ss['url'] }}" placeholder="https://">
                    <label class="cps-switch" style="flex-shrink:0;">
                        <input type="checkbox" name="{{ $ss['name'] }}_enabled" value="1" {{ $ss['checked']?'checked':'' }}>
                        <span class="cps-slider"></span>
                    </label>
                </div>
                @endforeach
            </div>

            <button type="button" class="seo-add-link" onclick="seoAddSocialRow()">
                <i class="ti ti-plus" style="font-size:13px;"></i> Add Social Link
            </button>
        </div>

    </div>

    {{-- ── Right ── --}}
    <div class="col-lg-5">

        {{-- Search Engine Preview --}}
        <div class="cps-sidebar-card">
            <h4 style="margin:0 0 3px;">Search Engine Preview</h4>
            <p class="sub">See how your career page will appear in search results.</p>
            <div class="seo-se-prev">
                <div class="seo-se-url">https://www.4bell.com/careers</div>
                <div class="seo-se-title" id="seoPreviewTitle">Careers at 4Bell Technology | Join Our Team</div>
                <div class="seo-se-desc" id="seoPreviewDesc">Explore exciting career opportunities at 4Bell Technology. Join our mission to build innovative solutions and grow your career with a talented team.</div>
            </div>
        </div>

        {{-- Social Share Preview --}}
        <div class="cps-sidebar-card" style="margin-top:16px;padding:0;overflow:hidden;">
            <div style="padding:14px 18px 12px;border-bottom:1px solid #F1F5F9;">
                <h4 style="margin:0 0 2px;">Social Share Preview</h4>
                <p class="sub" style="margin:0;">See how your career page will look when shared on social platforms.</p>
            </div>
            <div style="padding:14px;">
                <div class="seo-share-prev">
                    <div class="seo-share-logo-bar">
                        <i class="ti ti-building" style="font-size:14px;color:#2563EB;"></i>
                        <span style="font-size:11px;font-weight:700;color:#0F172A;">{{ $companyName }}</span>
                    </div>
                    <div class="seo-share-hero">
                        {{-- decorative person silhouette --}}
                        <div style="position:absolute;right:0;top:0;bottom:0;width:45%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;">
                            <i class="ti ti-users" style="font-size:52px;color:rgba(255,255,255,.3);"></i>
                        </div>
                        <div class="seo-share-hero-text">
                            <div class="seo-share-hero-title">We're Building<br>the Future Together</div>
                            <div class="seo-share-hero-sub">Join our team of innovators and<br>create impact that matters.</div>
                        </div>
                    </div>
                    <div class="seo-share-body">
                        <div class="seo-share-og-title">Careers at 4Bell Technology | Join Our Team</div>
                        <div class="seo-share-og-desc">Explore exciting career opportunities at 4Bell Technology. Join our mission to build innovative solutions and grow your career with a talented team.</div>
                        <div class="seo-share-og-url">4bell.com</div>
                        <div class="seo-share-btns">
                            <div class="seo-share-btn"><i class="ti ti-brand-facebook" style="color:#1877F2;font-size:12px;"></i> Facebook</div>
                            <div class="seo-share-btn"><i class="ti ti-brand-linkedin" style="color:#0077B5;font-size:12px;"></i> LinkedIn</div>
                            <div class="seo-share-btn"><i class="ti ti-brand-x" style="color:#0F172A;font-size:12px;"></i> X (Twitter)</div>
                            <div class="seo-share-btn"><i class="ti ti-brand-whatsapp" style="color:#25D366;font-size:12px;"></i> WhatsApp</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional SEO Options --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 4px;">Additional SEO Options</h4>
            @php
            $seoAddOpts = [
                ['icon'=>'ti-world',        'bg'=>'#EFF6FF','ic'=>'#2563EB','title'=>'Enable Open Graph Tags',         'desc'=>'Allow search engines to better understand your content.',   'name'=>'seo_og_tags',      'checked'=>true],
                ['icon'=>'ti-brand-twitter','bg'=>'#EFF6FF','ic'=>'#1DA1F2','title'=>'Enable Twitter Card Tags',       'desc'=>'Optimize your page for better visibility on Twitter.',      'name'=>'seo_twitter_card', 'checked'=>true],
                ['icon'=>'ti-code',         'bg'=>'#F0FDF4','ic'=>'#16A34A','title'=>'Add Structured Data (JSON-LD)',  'desc'=>'Help search engines understand your job postings.',         'name'=>'seo_structured_data','checked'=>true],
                ['icon'=>'ti-sitemap',      'bg'=>'#FAF5FF','ic'=>'#7C3AED','title'=>'Automatically Generate Sitemap','desc'=>'Include career page in sitemap.xml',                        'name'=>'seo_sitemap',      'checked'=>true],
            ];
            @endphp
            @foreach($seoAddOpts as $ao)
            <div class="seo-addopt-row">
                <div class="seo-addopt-left">
                    <div class="seo-addopt-icon" style="background:{{ $ao['bg'] }};">
                        <i class="ti {{ $ao['icon'] }}" style="font-size:15px;color:{{ $ao['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="seo-addopt-title">{{ $ao['title'] }}</div>
                        <div class="seo-addopt-desc">{{ $ao['desc'] }}</div>
                    </div>
                </div>
                <label class="cps-switch" style="flex-shrink:0;margin-top:3px;">
                    <input type="checkbox" name="{{ $ao['name'] }}" value="1" {{ $ao['checked']?'checked':'' }}>
                    <span class="cps-slider"></span>
                </label>
            </div>
            @endforeach
        </div>

    </div>
</div>

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>
</div>{{-- end panel-seo --}}

{{-- ════════════════ INTEGRATIONS TAB ════════════════ --}}
<div class="cps-panel" id="panel-integrations">
<div class="row g-4">

    {{-- ── Left: Connect & Automate ── --}}
    <div class="col-lg-7">

        <div class="cps-section-title" style="font-size:16px;margin-bottom:3px;">Connect & Automate</div>
        <div class="cps-section-sub" style="margin-bottom:18px;">Integrate your career page with external tools and services to streamline your hiring workflow.</div>

        {{-- Search + Filter --}}
        <div class="int-search-bar">
            <div class="int-search-wrap">
                <i class="ti ti-search"></i>
                <input type="text" class="int-search-input" placeholder="Search integrations...">
            </div>
            <select class="int-cat-select">
                <option>All Categories</option>
                <option>ATS & HR Tools</option>
                <option>Analytics</option>
                <option>Marketing</option>
                <option>Communication</option>
                <option>Automation</option>
                <option>Other</option>
            </select>
        </div>

        {{-- Category Tabs --}}
        <div class="int-tabs">
            <div class="int-tab active">All Integrations</div>
            <div class="int-tab">ATS & HR Tools</div>
            <div class="int-tab">Analytics</div>
            <div class="int-tab">Marketing</div>
            <div class="int-tab">Communication</div>
            <div class="int-tab">Automation</div>
            <div class="int-tab">Other</div>
        </div>

        {{-- Integration Cards --}}
        @php
        $intCards = [
            ['name'=>'Greenhouse',        'desc'=>'Sync jobs, candidates and applications seamlessly with Greenhouse.',   'connected'=>true,  'color'=>'#24A862'],
            ['name'=>'Lever',             'desc'=>'Export job postings and import candidates from Lever.',                'connected'=>false, 'color'=>'#999'],
            ['name'=>'Workday',           'desc'=>'Integrate with Workday Recruiting for centralized talent management.', 'connected'=>false, 'color'=>'#CC0000'],
            ['name'=>'BambooHR',          'desc'=>'Sync candidate data and employee information effortlessly.',           'connected'=>false, 'color'=>'#73AC39'],
            ['name'=>'Zapier',            'desc'=>'Connect with 5,000+ apps and automate your hiring workflows.',        'connected'=>false, 'color'=>'#FF4A00'],
            ['name'=>'Make (Integromat)', 'desc'=>'Build scenarios and automate repetitive tasks.',                      'connected'=>false, 'color'=>'#6D04D9'],
            ['name'=>'Google Analytics 4','desc'=>'Track career page traffic and candidate engagement.',                  'connected'=>true,  'color'=>'#E37400'],
            ['name'=>'Microsoft Clarity', 'desc'=>'Understand user behavior on your career page.',                       'connected'=>false, 'color'=>'#0078D4'],
            ['name'=>'Hotjar',            'desc'=>'See how candidates interact with your career site.',                  'connected'=>false, 'color'=>'#FD3A5C'],
        ];
        $intIcons = [
            'Greenhouse'        => ['ti-leaf',          '#24A862'],
            'Lever'             => ['ti-adjustments',   '#555'],
            'Workday'           => ['ti-briefcase',      '#CC0000'],
            'BambooHR'          => ['ti-tree',           '#73AC39'],
            'Zapier'            => ['ti-bolt',           '#FF4A00'],
            'Make (Integromat)' => ['ti-puzzle',         '#6D04D9'],
            'Google Analytics 4'=> ['ti-chart-bar',      '#E37400'],
            'Microsoft Clarity' => ['ti-activity',       '#0078D4'],
            'Hotjar'            => ['ti-flame',          '#FD3A5C'],
        ];
        @endphp

        <div class="int-grid">
            @foreach($intCards as $ic)
            @php $ico = $intIcons[$ic['name']] ?? ['ti-plug','#64748B']; @endphp
            <div class="int-card">
                <div class="int-card-logo">
                    <div style="width:38px;height:38px;border-radius:10px;background:{{ $ic['color'] }}18;display:flex;align-items:center;justify-content:center;">
                        <i class="ti {{ $ico[0] }}" style="font-size:22px;color:{{ $ico[1] }};"></i>
                    </div>
                </div>
                <div class="int-card-name">{{ $ic['name'] }}</div>
                <div class="int-card-desc">{{ $ic['desc'] }}</div>
                <div class="int-card-footer">
                    @if($ic['connected'])
                        <span class="int-connected-badge">
                            <i class="ti ti-circle-check-filled" style="font-size:11px;"></i> Connected
                        </span>
                        <button type="button" class="int-manage-btn">Manage Settings</button>
                    @else
                        <button type="button" class="int-connect-btn">Connect</button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Request Integration --}}
        <div class="int-request-card">
            <div class="int-request-icon">
                <i class="ti ti-plug-connected" style="font-size:28px;color:#2563EB;"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="int-request-title">Can't find the integration you need?</div>
                <div class="int-request-desc">We're constantly adding new integrations. Let us know which tools you'd like to connect.</div>
            </div>
            <button type="button" class="int-request-btn">Request Integration</button>
        </div>

    </div>

    {{-- ── Right ── --}}
    <div class="col-lg-5">

        {{-- Active Integrations --}}
        <div class="cps-sidebar-card">
            <h4 style="margin:0 0 3px;">Active Integrations</h4>
            <p class="sub">These integrations are currently connected to your career page.</p>

            @php
            $activeInts = [
                ['icon'=>'ti-leaf',      'bg'=>'#DCFCE7','ic'=>'#24A862','name'=>'Greenhouse',        'sync'=>'Last synced: 2 mins ago'],
                ['icon'=>'ti-chart-bar', 'bg'=>'#FFF7ED','ic'=>'#E37400','name'=>'Google Analytics 4','sync'=>'Last synced: 15 mins ago'],
                ['icon'=>'ti-mail',      'bg'=>'#EFF6FF','ic'=>'#2563EB','name'=>'SendGrid',           'sync'=>'Last synced: 1 hour ago'],
            ];
            @endphp
            @foreach($activeInts as $ai)
            <div class="int-active-row">
                <div class="int-active-left">
                    <div class="int-active-logo" style="background:{{ $ai['bg'] }};">
                        <i class="ti {{ $ai['icon'] }}" style="font-size:18px;color:{{ $ai['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="int-active-name">{{ $ai['name'] }}</div>
                        <div class="int-active-sync">{{ $ai['sync'] }}</div>
                    </div>
                </div>
                <div class="int-active-right">
                    <span class="int-connected-sm">Connected</span>
                    <div class="int-gear-btn"><i class="ti ti-settings"></i></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Webhooks --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                <h4 style="margin:0;">Webhooks</h4>
                <button type="button" class="int-add-wh-btn">
                    <i class="ti ti-plus" style="font-size:12px;"></i> Add Webhook
                </button>
            </div>
            <p class="sub">Use webhooks to send real-time data to your endpoints.</p>

            @php
            $webhooks = [
                ['name'=>'Application Received','url'=>'https://hooks.4bell.com/career/applications'],
                ['name'=>'Candidate Hired',      'url'=>'https://hooks.4bell.com/career/hired'],
            ];
            @endphp
            @foreach($webhooks as $wh)
            <div class="int-wh-row">
                <div class="int-wh-left">
                    <div class="int-wh-icon">
                        <i class="ti ti-webhook" style="font-size:16px;color:#64748B;"></i>
                    </div>
                    <div>
                        <div class="int-wh-name">{{ $wh['name'] }}</div>
                        <div class="int-wh-url">{{ $wh['url'] }}</div>
                    </div>
                </div>
                <div class="int-wh-right">
                    <span class="int-active-badge">Active</span>
                    <div class="int-dots-btn">⋮</div>
                </div>
            </div>
            @endforeach

            <div class="int-view-all">View All Webhooks</div>
        </div>

        {{-- Other Settings --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 4px;">Other Settings</h4>

            @php
            $intOther = [
                ['icon'=>'ti-refresh',    'bg'=>'#EFF6FF','ic'=>'#2563EB','title'=>'Enable Job Posting Sync',     'desc'=>'Automatically sync job openings to connected platforms.', 'toggle'=>true,  'on'=>true,  'link'=>false],
                ['icon'=>'ti-users',      'bg'=>'#F0FDF4','ic'=>'#16A34A','title'=>'Enable Candidate Sync',       'desc'=>'Sync candidate data between integrations.',              'toggle'=>true,  'on'=>true,  'link'=>false],
                ['icon'=>'ti-database',   'bg'=>'#FFF7ED','ic'=>'#F97316','title'=>'Data Mapping',                'desc'=>'Map fields between Jero ATS and integrated platforms.',  'toggle'=>false, 'on'=>false, 'link'=>true],
                ['icon'=>'ti-trash',      'bg'=>'#FFF1F2','ic'=>'#EF4444','title'=>'Disconnect All Integrations', 'desc'=>'Remove all connected integrations from your career page.','toggle'=>false, 'on'=>false, 'link'=>true],
            ];
            @endphp
            @foreach($intOther as $io)
            <div class="int-other-row">
                <div class="int-other-left">
                    <div class="int-other-icon" style="background:{{ $io['bg'] }};">
                        <i class="ti {{ $io['icon'] }}" style="font-size:15px;color:{{ $io['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="int-other-title">{{ $io['title'] }}</div>
                        <div class="int-other-desc">{{ $io['desc'] }}</div>
                    </div>
                </div>
                @if($io['toggle'])
                <label class="cps-switch" style="flex-shrink:0;margin-top:3px;">
                    <input type="checkbox" value="1" {{ $io['on']?'checked':'' }}>
                    <span class="cps-slider"></span>
                </label>
                @else
                <div class="int-arrow-btn"><i class="ti ti-chevron-right"></i></div>
                @endif
            </div>
            @endforeach
        </div>

    </div>
</div>

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>
</div>{{-- end panel-integrations --}}

{{-- ════════════════ ADVANCED TAB ════════════════ --}}
<div class="cps-panel" id="panel-advanced">
<div class="row g-4">

    {{-- ── Left ── --}}
    <div class="col-lg-6">

        {{-- Performance & Caching --}}
        <div class="cps-section">
            <div class="cps-section-title">Performance & Caching</div>
            <div class="cps-section-sub">Improve speed and deliver a seamless experience to your candidates.</div>

            <div class="row g-0">
                <div class="col-7" style="padding-right:16px;">
                    <div class="adv-toggle-row">
                        <div>
                            <div class="adv-toggle-label">Enable Caching</div>
                            <div class="adv-toggle-desc">Cache career page content for faster load times.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="adv_caching" value="1" checked>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    <div style="padding:12px 0 12px;">
                        <div class="adv-toggle-label" style="margin-bottom:4px;">Cache Duration (TTL)</div>
                        <div class="adv-hint" style="margin-bottom:4px;">Select how long cached content should be stored.</div>
                        <select class="adv-select" name="adv_cache_ttl">
                            <option value="30m">30 Minutes</option>
                            <option value="1h" selected>1 Hour</option>
                            <option value="6h">6 Hours</option>
                            <option value="24h">24 Hours</option>
                        </select>
                    </div>
                    <div class="adv-toggle-row">
                        <div>
                            <div class="adv-toggle-label">CDN Delivery</div>
                            <div class="adv-toggle-desc">Serve career page assets (images, CSS, JS) via CDN.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="adv_cdn" value="1" checked>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    <div class="adv-toggle-row" style="border-bottom:none;padding-bottom:0;">
                        <div>
                            <div class="adv-toggle-label">Image Optimization</div>
                            <div class="adv-toggle-desc">Automatically compress and optimize images.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="adv_img_opt" value="1" checked>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                </div>
                <div class="col-5">
                    <div class="adv-perf-card">
                        {{-- SVG gauge --}}
                        <div class="adv-gauge-wrap">
                            <svg width="100" height="58" viewBox="0 0 100 58">
                                <path d="M10 55 A40 40 0 0 1 90 55" fill="none" stroke="#E2E8F0" stroke-width="8" stroke-linecap="round"/>
                                <path d="M10 55 A40 40 0 0 1 90 55" fill="none" stroke="#16A34A" stroke-width="8" stroke-linecap="round" stroke-dasharray="125.6" stroke-dashoffset="5"/>
                            </svg>
                            <div style="margin-top:-10px;">
                                <span class="adv-gauge-score">97</span><span class="adv-gauge-total">/100</span>
                                <div style="font-size:10px;color:#94A3B8;margin-top:1px;">Performance Score</div>
                                <div class="adv-gauge-label">Excellent</div>
                            </div>
                        </div>
                        @foreach(['Page Speed'=>'Excellent','First Contentful Paint'=>'Excellent','Largest Contentful Paint'=>'Excellent','Cumulative Layout Shift'=>'Good'] as $metric=>$rating)
                        <div class="adv-perf-check">
                            <span class="adv-perf-check-name"><i class="ti ti-circle-check-filled"></i> {{ $metric }}</span>
                            <span class="{{ $rating==='Good'?'adv-perf-good':'adv-perf-excellent' }}">{{ $rating }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Security & Access --}}
        <div class="cps-section">
            <div class="cps-section-title">Security & Access</div>
            <div class="cps-section-sub">Control access and protect your career page.</div>

            <div class="row g-0">
                <div class="col-7" style="padding-right:16px;">
                    @php
                    $secToggles = [
                        ['name'=>'adv_pwd_protect',   'label'=>'Password Protection',           'desc'=>'Restrict access to your career page with a password.',      'checked'=>false],
                        ['name'=>'adv_ip_whitelist',  'label'=>'Allowed IP Addresses',          'desc'=>'Allow access only from specific IP addresses.',             'checked'=>false],
                        ['name'=>'adv_block_indexing','label'=>'Block Search Engine Indexing',  'desc'=>'Prevent search engines from indexing your career page.',    'checked'=>false],
                        ['name'=>'adv_recaptcha',     'label'=>'Enable reCAPTCHA on Apply Form','desc'=>'Add reCAPTCHA to prevent spam applications.',              'checked'=>true],
                    ];
                    @endphp
                    @foreach($secToggles as $st)
                    <div class="adv-toggle-row" style="{{ $loop->last?'border-bottom:none;padding-bottom:0;':'' }}">
                        <div>
                            <div class="adv-toggle-label">{{ $st['label'] }}</div>
                            <div class="adv-toggle-desc">{{ $st['desc'] }}</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="{{ $st['name'] }}" value="1" {{ $st['checked']?'checked':'' }}>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    @endforeach
                </div>
                <div class="col-5">
                    <div class="adv-sec-card">
                        <div class="adv-toggle-label" style="margin-bottom:6px;">Career Page URL</div>
                        <a href="{{ $careerUrl }}" target="_blank" class="adv-sec-link">
                            {{ $careerUrl }} <i class="ti ti-external-link" style="font-size:11px;"></i>
                        </a>
                        <button type="button" class="adv-copy-btn" onclick="copyCareerUrl()">Copy Link</button>
                        <div class="adv-share-title">Share Preview</div>
                        <div class="adv-share-desc">Share a preview of your career page.</div>
                        <div class="adv-share-icons">
                            <div class="adv-share-icon" style="background:#EFF6FF;"><i class="ti ti-brand-linkedin" style="color:#0077B5;"></i></div>
                            <div class="adv-share-icon" style="background:#EFF6FF;"><i class="ti ti-brand-facebook" style="color:#1877F2;"></i></div>
                            <div class="adv-share-icon" style="background:#F8FAFC;"><i class="ti ti-brand-x" style="color:#0F172A;"></i></div>
                            <div class="adv-share-icon" style="background:#F0FDF4;"><i class="ti ti-mail" style="color:#16A34A;"></i></div>
                        </div>
                        <button type="button" class="adv-preview-btn">
                            View Public Preview <i class="ti ti-external-link" style="font-size:11px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Maintenance Mode --}}
        <div class="cps-section">
            <div class="cps-section-title">Maintenance Mode</div>
            <div class="cps-section-sub">Put your career page in maintenance mode.</div>

            <div class="adv-maint-card">
                <div class="adv-maint-header">
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <div style="width:36px;height:36px;border-radius:9px;background:#FFF7ED;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="ti ti-tools" style="font-size:18px;color:#F97316;"></i>
                        </div>
                        <div>
                            <div class="adv-maint-title">Maintenance Mode</div>
                            <div class="adv-maint-desc">When enabled, visitors will see a maintenance message.</div>
                        </div>
                    </div>
                    <label class="cps-switch" style="flex-shrink:0;margin-top:4px;">
                        <input type="checkbox" name="adv_maintenance" value="1">
                        <span class="cps-slider"></span>
                    </label>
                </div>
                <label class="cps-label">Maintenance Message</label>
                <textarea class="adv-maint-textarea" name="adv_maintenance_msg" id="advMaintMsg" maxlength="200"
                          oninput="document.getElementById('advMaintCount').textContent=this.value.length+'/200';"
                          rows="3">We're making some updates to serve you better. Please check back shortly!</textarea>
                <div style="display:flex;justify-content:flex-end;margin-top:3px;">
                    <span style="font-size:11px;color:#94A3B8;" id="advMaintCount">66/200</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Right ── --}}
    <div class="col-lg-6">

        {{-- Custom Code --}}
        <div class="cps-sidebar-card" style="padding:0;overflow:hidden;margin-bottom:16px;">
            <div style="padding:14px 18px 0;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                    <h4 style="margin:0;">Custom Code</h4>
                    <a href="javascript:void(0);" style="font-size:12px;font-weight:600;color:#2563EB;display:flex;align-items:center;gap:4px;text-decoration:none;">
                        Learn more <i class="ti ti-external-link" style="font-size:11px;"></i>
                    </a>
                </div>
                <p class="sub" style="margin:0 0 12px;">Add custom code to further customize your career page.</p>
                <div class="adv-code-tabs">
                    <div class="adv-code-tab active" onclick="switchCodeTab(this,'adv-code-header')">Header Code</div>
                    <div class="adv-code-tab" onclick="switchCodeTab(this,'adv-code-footer')">Footer Code</div>
                    <div class="adv-code-tab" onclick="switchCodeTab(this,'adv-code-body')">Body End Code</div>
                </div>
            </div>
            <div class="adv-code-editor" id="adv-code-header">
                @php
                $codeLines = [
                    ['ln'=>1,  'text'=>'<!-- Google Tag Manager -->'],
                    ['ln'=>2,  'text'=>'<script>'],
                    ['ln'=>3,  'text'=>'(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':'],
                    ['ln'=>4,  'text'=>'new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],'],
                    ['ln'=>5,  'text'=>'j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src='],
                    ['ln'=>6,  'text'=>'\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;'],
                    ['ln'=>7,  'text'=>'f.parentNode.insertBefore(j,f);'],
                    ['ln'=>8,  'text'=>'})(window,document,\'script\',\'dataLayer\',\'GTM-XXXXXXX\');'],
                    ['ln'=>9,  'text'=>'</script>'],
                    ['ln'=>10, 'text'=>'<!-- End Google Tag Manager -->'],
                ];
                @endphp
                @foreach($codeLines as $cl)
                <div class="adv-code-line">
                    <span class="adv-code-ln">{{ $cl['ln'] }}</span>
                    <span class="adv-code-text">{{ $cl['text'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="adv-code-editor" id="adv-code-footer" style="display:none;">
                <div class="adv-code-line"><span class="adv-code-ln">1</span><span class="adv-code-text adv-code-comment">{{-- Add footer scripts here --}}</span></div>
            </div>
            <div class="adv-code-editor" id="adv-code-body" style="display:none;">
                <div class="adv-code-line"><span class="adv-code-ln">1</span><span class="adv-code-text adv-code-comment">{{-- Add body end scripts here --}}</span></div>
            </div>
        </div>

        {{-- Analytics & Tracking --}}
        <div class="cps-sidebar-card" style="margin-bottom:16px;">
            <h4 style="margin:0 0 3px;">Analytics & Tracking</h4>
            <p class="sub">Track and analyze candidate behavior and page performance.</p>

            <div class="row g-0">
                <div class="col-7" style="padding-right:12px;">
                    <div class="adv-track-label">Google Analytics 4 Measurement ID</div>
                    <div class="adv-track-input-wrap">
                        <input type="text" class="adv-track-input" name="adv_ga4_id" placeholder="G-XXXXXXXXX" value="G-XXXXXXXXX">
                        <span class="adv-track-badge">Connected</span>
                    </div>
                    <div class="adv-track-label">Facebook Pixel ID <span style="font-weight:400;color:#94A3B8;">(Optional)</span></div>
                    <div class="adv-track-input-wrap">
                        <input type="text" class="adv-track-input" name="adv_fb_pixel" placeholder="Pixel ID" value="123456789012345">
                        <span class="adv-track-badge">Connected</span>
                    </div>
                    <div class="adv-track-label">LinkedIn Insight Tag ID <span style="font-weight:400;color:#94A3B8;">(Optional)</span></div>
                    <div class="adv-track-input-wrap">
                        <input type="text" class="adv-track-input" name="adv_linkedin_tag" placeholder="Tag ID" value="abcdef12-3456-7890-abcd-ef1234567890">
                        <span class="adv-track-badge">Connected</span>
                    </div>
                    <div class="adv-track-label">Hotjar Site ID <span style="font-weight:400;color:#94A3B8;">(Optional)</span></div>
                    <div class="adv-track-input-wrap">
                        <input type="text" class="adv-track-input" name="adv_hotjar_id" placeholder="Site ID" value="1234567">
                        <button type="button" class="adv-verify-btn">Verify Connection</button>
                    </div>
                </div>
                <div class="col-5">
                    <div class="adv-track-status">
                        <div style="font-size:12px;font-weight:700;color:#0F172A;margin-bottom:8px;">Tracking Status</div>
                        @foreach(['Page Views'=>'Active','Job View Events'=>'Active','Application Events'=>'Active','Form Submissions'=>'Active'] as $ts=>$status)
                        <div class="adv-ts-row">
                            <span class="adv-ts-name">{{ $ts }}</span>
                            <span class="adv-ts-badge">{{ $status }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="adv-track-desc" style="margin-top:10px;">
                        <i class="ti ti-chart-line" style="font-size:16px;color:#2563EB;flex-shrink:0;margin-top:2px;"></i>
                        Tracking events are automatically fired when candidates interact with your career page.
                    </div>
                </div>
            </div>
        </div>

        {{-- Export & Backup --}}
        <div class="cps-sidebar-card">
            <h4 style="margin:0 0 3px;">Export & Backup</h4>
            <p class="sub">Backup your career page settings or export configuration.</p>

            <div class="row g-0">
                <div class="col-7" style="padding-right:12px;">
                    <div class="adv-export-row">
                        <div>
                            <div class="adv-export-label">Export Settings</div>
                            <div class="adv-export-desc">Download all career page settings as a JSON file.</div>
                        </div>
                        <button type="button" class="adv-export-btn">Export JSON</button>
                    </div>
                    <div class="adv-export-row" style="border-bottom:none;padding-bottom:0;">
                        <div>
                            <div class="adv-export-label">Backup Now</div>
                            <div class="adv-export-desc">Create a backup of your current career page settings.</div>
                        </div>
                        <button type="button" class="adv-export-btn">Create Backup</button>
                    </div>
                </div>
                <div class="col-5">
                    <div style="border:1.5px solid #E2E8F0;border-radius:10px;padding:14px;background:#F8FAFC;">
                        <div style="font-size:12px;font-weight:700;color:#0F172A;margin-bottom:6px;">Last Backup</div>
                        <div class="adv-backup-info">14 May 2025, 11:45 AM</div>
                        <div class="adv-backup-date" style="margin-top:2px;">by {{ auth()->user()->name ?? 'Admin' }}</div>
                        <button type="button" class="adv-dl-btn" style="margin-top:10px;">
                            <i class="ti ti-download" style="font-size:13px;"></i> Download Backup
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>
</div>{{-- end panel-advanced --}}

{{-- ════════════════ TEMPLATES DESIGN TAB ════════════════ --}}
<div class="cps-panel" id="panel-templates">
<div class="row g-0" style="min-height:600px;">

    {{-- ── LEFT COLUMN ── --}}
    <div class="col-lg-7" style="padding:28px 24px 28px 28px;border-right:1.5px solid #F1F5F9;">

        {{-- 1. CHOOSE TEMPLATE --}}
        <div style="margin-bottom:28px;">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#0F172A;">Choose Template</div>
                    <div style="font-size:11px;color:#94A3B8;margin-top:1px;">Select a pre-designed template or start from scratch.</div>
                </div>
                <a href="#" style="font-size:11px;font-weight:600;color:#2563EB;text-decoration:none;white-space:nowrap;">Browse More Templates <i class="ti ti-arrow-right" style="font-size:11px;"></i></a>
            </div>

            <div class="td-tpl-grid mt-3" id="tdTemplateGrid">
                @php
                $savedTpl = $careerSettings->td_template ?? 'modern';
                $tdTemplates = [
                    ['key'=>'modern',    'name'=>'Modern',    'desc'=>'Clean & professional', 'from'=>'#1E3A8A','to'=>'#3B82F6'],
                    ['key'=>'corporate', 'name'=>'Corporate', 'desc'=>'Classic & formal',     'from'=>'#1F2937','to'=>'#374151'],
                    ['key'=>'creative',  'name'=>'Creative',  'desc'=>'Bold & vibrant',       'from'=>'#7C3AED','to'=>'#EC4899'],
                    ['key'=>'minimal',   'name'=>'Minimal',   'desc'=>'Simple & elegant',     'from'=>'#E2E8F0','to'=>'#CBD5E1','dark_text'=>true],
                    ['key'=>'tech',      'name'=>'Tech',      'desc'=>'Dark & modern',        'from'=>'#0F172A','to'=>'#1E293B'],
                ];
                @endphp
                @foreach($tdTemplates as $tt)
                <div class="td-tpl-card {{ $tt['key']===$savedTpl?'selected':'' }}" onclick="tdSelectTemplate('{{ $tt['key'] }}',this)">
                    <div class="td-tpl-check"><i class="ti ti-check" style="font-size:10px;color:#fff;"></i></div>
                    <div class="td-tpl-thumb" style="background:linear-gradient(135deg,{{ $tt['from'] }},{{ $tt['to'] }});">
                        <div style="padding:10px 8px;">
                            <div style="height:5px;background:rgba(255,255,255,.8);border-radius:3px;width:60%;margin-bottom:4px;"></div>
                            <div style="height:3px;background:rgba(255,255,255,.45);border-radius:3px;width:80%;margin-bottom:3px;"></div>
                            <div style="height:3px;background:rgba(255,255,255,.45);border-radius:3px;width:65%;margin-bottom:8px;"></div>
                            <div style="height:14px;background:rgba(255,255,255,.9);border-radius:3px;width:50%;"></div>
                        </div>
                    </div>
                    <div class="td-tpl-info">
                        <div class="td-tpl-name">{{ $tt['name'] }}</div>
                        <div class="td-tpl-desc">{{ $tt['desc'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <input type="hidden" name="td_template" id="tdTemplateInput" value="{{ $careerSettings->td_template ?? 'modern' }}">
        </div>

        {{-- 2. CUSTOMIZE DESIGN --}}
        <div style="margin-bottom:28px;">
            <div style="font-size:14px;font-weight:700;color:#0F172A;margin-bottom:2px;">Customize Design</div>
            <div style="font-size:11px;color:#94A3B8;margin-bottom:12px;">Personalize colors, fonts, and layout to match your brand.</div>

            <div class="td-design-wrap">
                {{-- Sidebar nav --}}
                <div class="td-design-nav">
                    @php
                    $tdDesignTabs = [
                        ['key'=>'colors',  'icon'=>'ti-palette',    'label'=>'Colors'],
                        ['key'=>'typo',    'icon'=>'ti-letter-t',   'label'=>'Typography'],
                        ['key'=>'header',  'icon'=>'ti-layout-navbar','label'=>'Header & Navigation'],
                        ['key'=>'buttons', 'icon'=>'ti-button',     'label'=>'Buttons'],
                        ['key'=>'sections','icon'=>'ti-layout',      'label'=>'Sections'],
                        ['key'=>'footer',  'icon'=>'ti-layout-bottombar','label'=>'Footer'],
                        ['key'=>'css',     'icon'=>'ti-code',        'label'=>'Custom CSS'],
                    ];
                    @endphp
                    @foreach($tdDesignTabs as $dt)
                    <div class="td-design-nav-item {{ $dt['key']==='colors'?'active':'' }}" onclick="tdSwitchDesignTab('{{ $dt['key'] }}',this)">
                        <i class="ti {{ $dt['icon'] }}"></i> {{ $dt['label'] }}
                    </div>
                    @endforeach
                </div>

                {{-- Content panels --}}
                <div class="td-design-content">
                    {{-- Colors panel --}}
                    <div class="td-design-tab active" id="td-design-colors">
                        <div class="td-design-section-title">Color Scheme</div>
                        <div class="td-design-section-sub">Choose colors for your career page.</div>
                        @php
                            $tdCP = $careerSettings->td_color_primary   ?? '#2563EB';
                            $tdCS = $careerSettings->td_color_secondary ?? '#1F2937';
                            $tdCA = $careerSettings->td_color_accent    ?? '#10B981';
                            $tdCB = $careerSettings->td_color_bg        ?? '#FFFFFF';
                            $tdTM = $careerSettings->td_theme_mode      ?? 'light';
                            $tdLY = $careerSettings->td_layout          ?? 'wide';
                            $tdTpl= $careerSettings->td_template        ?? 'modern';
                        @endphp
                        {{-- 2×2 color grid: Primary / Secondary / Accent / Background --}}
                        <div class="td-color-grid">
                            <div>
                                <div class="td-color-label">Primary Color</div>
                                <div class="td-color-row">
                                    <input type="color" class="td-color-swatch" value="{{ $tdCP }}" id="tdColorPrimary" oninput="document.getElementById('tdColorPrimaryText').value=this.value;tdUpdatePreview()">
                                    <input type="text" class="td-color-input" id="tdColorPrimaryText" name="td_color_primary" value="{{ $tdCP }}" maxlength="7" oninput="syncColorSwatch('tdColorPrimary',this);tdUpdatePreview()">
                                </div>
                            </div>
                            <div>
                                <div class="td-color-label">Secondary Color</div>
                                <div class="td-color-row">
                                    <input type="color" class="td-color-swatch" value="{{ $tdCS }}" id="tdColorSecondary" oninput="document.getElementById('tdColorSecondaryText').value=this.value;tdUpdatePreview()">
                                    <input type="text" class="td-color-input" id="tdColorSecondaryText" name="td_color_secondary" value="{{ $tdCS }}" maxlength="7" oninput="syncColorSwatch('tdColorSecondary',this);tdUpdatePreview()">
                                </div>
                            </div>
                            <div>
                                <div class="td-color-label">Accent Color</div>
                                <div class="td-color-row">
                                    <input type="color" class="td-color-swatch" value="{{ $tdCA }}" id="tdColorAccent" oninput="document.getElementById('tdColorAccentText').value=this.value;tdUpdatePreview()">
                                    <input type="text" class="td-color-input" id="tdColorAccentText" name="td_color_accent" value="{{ $tdCA }}" maxlength="7" oninput="syncColorSwatch('tdColorAccent',this);tdUpdatePreview()">
                                </div>
                            </div>
                            <div>
                                <div class="td-color-label">Background Color</div>
                                <div class="td-color-row">
                                    <input type="color" class="td-color-swatch" value="{{ $tdCB }}" id="tdColorBg" oninput="document.getElementById('tdColorBgText').value=this.value;tdUpdatePreview()">
                                    <input type="text" class="td-color-input" id="tdColorBgText" name="td_color_bg" value="{{ $tdCB }}" maxlength="7" oninput="syncColorSwatch('tdColorBg',this);tdUpdatePreview()">
                                </div>
                            </div>
                        </div>
                        {{-- Theme mode --}}
                        <div class="td-design-section-title" style="margin-top:4px;">Theme Mode</div>
                        <div class="td-theme-opts" style="margin-bottom:16px;">
                            <label class="td-theme-opt"><input type="radio" name="td_theme_mode" value="light" {{ $tdTM==='light'?'checked':'' }}> Light Mode</label>
                            <label class="td-theme-opt"><input type="radio" name="td_theme_mode" value="dark" {{ $tdTM==='dark'?'checked':'' }}> Dark Mode</label>
                            <label class="td-theme-opt"><input type="radio" name="td_theme_mode" value="system" {{ $tdTM==='system'?'checked':'' }}> System Default</label>
                        </div>

                        {{-- Layout Style --}}
                        <div class="td-design-section-title">Layout Style</div>
                        <div class="td-design-section-sub">Choose how content is displayed across the page.</div>
                        @php $savedLayout = $careerSettings->td_layout ?? 'wide'; @endphp
                        <div class="td-layout-grid" id="tdLayoutGrid">
                            <div class="td-layout-card {{ $savedLayout==='wide'?'selected':'' }}" onclick="tdSelectLayout('wide',this)">
                                <div class="td-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                <div class="td-lc-thumb">
                                    <div style="display:flex;gap:3px;width:80%;height:20px;">
                                        <div style="flex:1;background:rgba(37,99,235,.3);border-radius:2px;"></div>
                                        <div style="flex:3;background:rgba(37,99,235,.15);border-radius:2px;"></div>
                                    </div>
                                </div>
                                <div class="td-lc-name">Wide</div>
                                <div class="td-lc-desc">Best for large screens</div>
                            </div>
                            <div class="td-layout-card {{ $savedLayout==='boxed'?'selected':'' }}" onclick="tdSelectLayout('boxed',this)">
                                <div class="td-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                <div class="td-lc-thumb">
                                    <div style="width:55%;height:20px;background:#E2E8F0;border-radius:3px;margin:0 auto;"></div>
                                </div>
                                <div class="td-lc-name">Boxed</div>
                                <div class="td-lc-desc">Content in a centered container</div>
                            </div>
                            <div class="td-layout-card {{ $savedLayout==='fullwidth'?'selected':'' }}" onclick="tdSelectLayout('fullwidth',this)">
                                <div class="td-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                <div class="td-lc-thumb">
                                    <div style="width:100%;height:20px;background:#E2E8F0;border-radius:3px;"></div>
                                </div>
                                <div class="td-lc-name">Full Width</div>
                                <div class="td-lc-desc">Edge to edge layout</div>
                            </div>
                        </div>
                        <input type="hidden" name="td_layout" id="tdLayoutInput" value="{{ $tdLY }}">
                    </div>

                    {{-- Other design tabs (hidden) --}}
                    <div class="td-design-tab" id="td-design-typo" style="display:none;">
                        <div class="td-design-section-title">Typography</div>
                        <div class="td-design-section-sub">Choose fonts for headings and body text.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-letter-t" style="font-size:24px;display:block;margin-bottom:6px;"></i>Font settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-header" style="display:none;">
                        <div class="td-design-section-title">Header & Navigation</div>
                        <div class="td-design-section-sub">Configure your career page header and navigation menu.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-layout-navbar" style="font-size:24px;display:block;margin-bottom:6px;"></i>Header settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-buttons" style="display:none;">
                        <div class="td-design-section-title">Buttons</div>
                        <div class="td-design-section-sub">Customize the style of buttons on your career page.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-button" style="font-size:24px;display:block;margin-bottom:6px;"></i>Button settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-sections" style="display:none;">
                        <div class="td-design-section-title">Sections</div>
                        <div class="td-design-section-sub">Control which sections appear on your career page.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-layout" style="font-size:24px;display:block;margin-bottom:6px;"></i>Section settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-footer" style="display:none;">
                        <div class="td-design-section-title">Footer</div>
                        <div class="td-design-section-sub">Customize the footer of your career page.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-layout-bottombar" style="font-size:24px;display:block;margin-bottom:6px;"></i>Footer settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-css" style="display:none;">
                        <div class="td-design-section-title">Custom CSS</div>
                        <div class="td-design-section-sub">Add your own CSS to further customize the look.</div>
                        <textarea name="td_custom_css" rows="8" style="width:100%;border:1.5px solid #E2E8F0;border-radius:7px;padding:10px;font-size:11px;font-family:'Courier New',monospace;color:#374151;outline:none;resize:vertical;" placeholder="/* Add your custom CSS here */&#10;.career-page { }">{{ $careerSettings->td_custom_css ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. CUSTOM ASSETS --}}
        <div style="margin-bottom:28px;">
            <div style="font-size:14px;font-weight:700;color:#0F172A;margin-bottom:2px;">Custom Assets</div>
            <div style="font-size:11px;color:#94A3B8;margin-bottom:14px;">Upload your brand assets for a consistent look.</div>
            <div style="border:1.5px solid #E2E8F0;border-radius:10px;overflow:hidden;">
                {{-- Company Logo --}}
                <div class="td-asset-row" style="padding:14px 16px;">
                    <div class="td-asset-thumb" id="tdLogoThumb">
                        @if(!empty($careerSettings->td_logo_path))
                            <img src="{{ asset($careerSettings->td_logo_path) }}" style="max-width:60px;max-height:36px;object-fit:contain;">
                        @else
                            <i class="ti ti-photo" style="font-size:18px;color:#CBD5E1;"></i>
                        @endif
                    </div>
                    <div class="td-asset-info">
                        <div class="td-asset-name">Company Logo</div>
                        <div class="td-asset-size">{{ !empty($careerSettings->td_logo_path) ? 'Logo uploaded' : 'Recommended size: 200 × 60px' }}</div>
                    </div>
                    <div class="td-asset-edit" onclick="document.getElementById('tdLogoUpload').click()" title="Upload logo">
                        <i class="ti ti-pencil" style="font-size:14px;"></i>
                    </div>
                    <input type="file" id="tdLogoUpload" name="td_logo" accept="image/*" style="display:none;" onchange="tdPreviewAsset(this,'tdLogoThumb')">
                </div>
                {{-- Favicon --}}
                <div class="td-asset-row" style="padding:14px 16px;border-top:1px solid #F1F5F9;">
                    <div class="td-asset-thumb" id="tdFaviconThumb">
                        @if(!empty($careerSettings->td_favicon_path))
                            <img src="{{ asset($careerSettings->td_favicon_path) }}" style="width:32px;height:32px;object-fit:contain;">
                        @else
                            <i class="ti ti-brand-chrome" style="font-size:16px;color:#CBD5E1;"></i>
                        @endif
                    </div>
                    <div class="td-asset-info">
                        <div class="td-asset-name">Favicon</div>
                        <div class="td-asset-size">{{ !empty($careerSettings->td_favicon_path) ? 'Favicon uploaded' : 'Recommended size: 32 × 32px' }}</div>
                    </div>
                    <div class="td-asset-edit" onclick="document.getElementById('tdFaviconUpload').click()" title="Upload favicon">
                        <i class="ti ti-pencil" style="font-size:14px;"></i>
                    </div>
                    <input type="file" id="tdFaviconUpload" name="td_favicon" accept="image/*" style="display:none;" onchange="tdPreviewAsset(this,'tdFaviconThumb')">
                </div>
            </div>
        </div>

        {{-- 4. ADDITIONAL OPTIONS --}}
        <div>
            <div style="font-size:14px;font-weight:700;color:#0F172A;margin-bottom:2px;">Additional Options</div>
            <div style="font-size:11px;color:#94A3B8;margin-bottom:14px;">Enable or disable extra features on your career page.</div>
            <div style="border:1.5px solid #E2E8F0;border-radius:10px;overflow:hidden;">
                @php
                $tdOptions = [
                    ['key'=>'life',      'icon'=>'ti-heart',     'icon_bg'=>'#FFF1F2', 'icon_color'=>'#E11D48', 'name'=>"Show 'Life at Company' Section", 'desc'=>'Display photos and culture content', 'val'=> $careerSettings->td_opt_life      ?? 1],
                    ['key'=>'testi',     'icon'=>'ti-quote',     'icon_bg'=>'#EFF6FF', 'icon_color'=>'#2563EB', 'name'=>'Show Testimonials',               'desc'=>'Feature employee testimonials',      'val'=> $careerSettings->td_opt_testi     ?? 1],
                    ['key'=>'animation', 'icon'=>'ti-sparkles',  'icon_bg'=>'#F0FDF4', 'icon_color'=>'#16A34A', 'name'=>'Enable Animation',                'desc'=>'Smooth scroll and entrance effects', 'val'=> $careerSettings->td_opt_animation ?? 1],
                ];
                @endphp
                @foreach($tdOptions as $i => $opt)
                <div class="td-opt-row" style="padding:13px 16px;{{ $i>0?'border-top:1px solid #F1F5F9;':'' }}">
                    <div class="td-opt-left">
                        <div class="td-opt-icon" style="background:{{ $opt['icon_bg'] }};">
                            <i class="ti {{ $opt['icon'] }}" style="font-size:14px;color:{{ $opt['icon_color'] }};"></i>
                        </div>
                        <div>
                            <div class="td-opt-title">{{ $opt['name'] }}</div>
                            <div class="td-opt-desc">{{ $opt['desc'] }}</div>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="td_opt_{{ $opt['key'] }}" value="1" {{ $opt['val']?'checked':'' }} style="width:40px;height:22px;cursor:pointer;">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>{{-- end left col --}}

    {{-- ── RIGHT COLUMN ── --}}
    <div class="col-lg-5" style="padding:28px 24px;background:#F8FAFC;">

        {{-- Live Preview header --}}
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <div style="font-size:14px;font-weight:700;color:#0F172A;">Live Preview</div>
                <div style="font-size:11px;color:#94A3B8;margin-top:1px;">See how your career page looks with the selected design.</div>
            </div>
            <div class="td-prev-device-bar">
                <div class="td-prev-device-btn active" onclick="tdSwitchDevice('desktop',this)" title="Desktop"><i class="ti ti-device-desktop"></i></div>
                <div class="td-prev-device-btn" onclick="tdSwitchDevice('tablet',this)" title="Tablet"><i class="ti ti-device-tablet"></i></div>
                <div class="td-prev-device-btn" onclick="tdSwitchDevice('mobile',this)" title="Mobile"><i class="ti ti-device-mobile"></i></div>
            </div>
        </div>

        {{-- Preview frame --}}
        <div id="tdPreviewFrame" class="td-live-prev" style="transition:max-width .2s;max-width:100%;">
            {{-- Navbar --}}
            <div class="td-prev-nav">
                <div class="td-prev-nav-logo">
                    <div style="width:22px;height:22px;background:linear-gradient(135deg,#1E3A8A,#3B82F6);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;font-weight:900;">4B</div>
                    <span>4Bell Technology</span>
                </div>
                <div class="td-prev-nav-links">
                    <span class="td-prev-nav-link">Jobs</span>
                    <span class="td-prev-nav-link">Life at 4Bell</span>
                    <span class="td-prev-nav-link">Teams</span>
                    <span class="td-prev-nav-link">About Us</span>
                </div>
                <div class="td-prev-nav-btn">View Openings</div>
            </div>

            {{-- Hero --}}
            <div class="td-prev-hero">
                <div>
                    <div class="td-prev-hero-title">Build the Future<br>Together</div>
                    <div class="td-prev-hero-sub">Join our team of innovators and create<br>technology that shapes tomorrow.</div>
                    <div class="td-prev-hero-search">
                        <input type="text" placeholder="Search for roles..." readonly>
                        <button class="td-prev-hero-search-btn">Search Jobs</button>
                    </div>
                </div>
                <div class="td-prev-hero-img">
                    <div style="text-align:center;">
                        <div style="display:flex;gap:4px;justify-content:center;margin-bottom:4px;">
                            @for($f=0;$f<4;$f++)
                            <div style="width:22px;height:22px;border-radius:50%;background:rgba(255,255,255,{{ 0.5+$f*0.1 }});border:1.5px solid rgba(255,255,255,.4);"></div>
                            @endfor
                        </div>
                        <div style="font-size:7px;color:rgba(255,255,255,.7);">Join 2,400+ team members</div>
                    </div>
                </div>
            </div>

            {{-- Why Work Here --}}
            <div class="td-prev-why">
                <div class="td-prev-why-title">Why Work at 4Bell Technology?</div>
                <div class="td-prev-why-sub">Discover what makes us a great place to work</div>
                <div class="td-prev-why-grid">
                    @php
                    $whyBenefits = [
                        ['icon'=>'ti-rocket','bg'=>'#EFF6FF','color'=>'#2563EB','name'=>'Innovative Culture','desc'=>'Work on cutting-edge projects'],
                        ['icon'=>'ti-trending-up','bg'=>'#F0FDF4','color'=>'#16A34A','name'=>'Growth Opportunities','desc'=>'Level up your career fast'],
                        ['icon'=>'ti-home','bg'=>'#FFF7ED','color'=>'#F97316','name'=>'Work-Life Balance','desc'=>'Flexible hybrid work options'],
                        ['icon'=>'ti-heart','bg'=>'#FFF1F2','color'=>'#E11D48','name'=>'Great Benefits','desc'=>'Health, dental, and more'],
                    ];
                    @endphp
                    @foreach($whyBenefits as $wb)
                    <div class="td-prev-why-card">
                        <div class="td-prev-why-icon" style="background:{{ $wb['bg'] }};">
                            <i class="ti {{ $wb['icon'] }}" style="font-size:13px;color:{{ $wb['color'] }};"></i>
                        </div>
                        <div class="td-prev-why-name">{{ $wb['name'] }}</div>
                        <div class="td-prev-why-desc">{{ $wb['desc'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- CTA --}}
            <div class="td-prev-cta">
                <div>
                    <div class="td-prev-cta-title">Ready to make an impact?</div>
                    <div class="td-prev-cta-sub">Join thousands of professionals who chose 4Bell</div>
                </div>
                <div class="td-prev-cta-btn">View Openings</div>
            </div>
        </div>

        {{-- Publishing Changes --}}
        <div class="td-publish-card">
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
                <div style="width:34px;height:34px;background:#EFF6FF;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="ti ti-cloud-upload" style="font-size:17px;color:#2563EB;"></i>
                </div>
                <div>
                    <div class="td-publish-title">Publishing Changes</div>
                    <div class="td-publish-desc">Your changes are saved automatically. Click 'Publish' to make them live on your career page.</div>
                </div>
            </div>
            <button type="button" class="td-publish-btn">
                <i class="ti ti-external-link" style="font-size:14px;"></i> Preview &amp; Publish
            </button>
        </div>

    </div>{{-- end right col --}}

</div>{{-- end row --}}

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>
</div>{{-- end panel-templates --}}

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
function tdSelectTemplate(key, el){
    document.querySelectorAll('.td-tpl-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('tdTemplateInput').value = key;
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
