@extends('layouts.service_company_layout')
@push('head-script')
<style>
/* ═══════════════════════════════════════════════
   RECOMMENDED CLIENT JOBS — reuses bench_explore's exact header/summary-card
   markup (bxr-) verbatim, so switching tabs never feels like leaving the
   module, plus a Similar-Candidates-style filter drawer (simf-, renamed rrj-
   to avoid ID collisions with the actual similar_candidates.blade.php page).
═══════════════════════════════════════════════ */
#kt_content{ margin-top:-70px !important; }
.bxr-page{ font-family:'Poppins',sans-serif; background:#F2F4F8; min-height:100vh; padding:18px 0 40px; }
.bxr-page *{ font-family:'Poppins',sans-serif; box-sizing:border-box; }

/* ══ 1. PROFILE HERO — segmented stat card, identical to bench_explore's:
   identity | Experience/Bill Rate | Bench Status/On Bench | 4 icon KPIs. ══ */
.bxr-hero{ background:#fff; border:1px solid #EEF0F4; border-radius:14px; padding:14px 20px; display:flex; align-items:center; flex-wrap:wrap; row-gap:14px; margin-bottom:16px; }
.bxr-hero-id{ display:flex; align-items:center; gap:14px; min-width:220px; padding-right:8px; }
.bxr-av-wrap{ position:relative; flex-shrink:0; }
.bxr-avatar{ width:56px; height:56px; border-radius:50%; object-fit:cover; }
.bxr-avatar-init{ width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg,#6D28D9,#4F46E5); display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:700; color:#fff; }
.bxr-online-dot{ position:absolute; bottom:2px; right:2px; width:12px; height:12px; background:#22C55E; border:2px solid #fff; border-radius:50%; }
.bxr-name{ font-size:14px; font-weight:700; color:#181C32; line-height:1.25; }
.bxr-role{ font-size:12px; font-weight:600; color:#4F46E5; margin:2px 0 4px; }
.bxr-loc{ font-size:11px; font-weight:500; color:#7E8299; display:flex; align-items:center; gap:4px; }
.bxr-loc i{ color:#4F46E5; font-size:12px; }
.bxr-hero-sep{ width:1px; align-self:stretch; background:#EEF0F4; margin:0 18px; }
.bxr-facts{ display:flex; flex-direction:column; gap:9px; justify-content:center; min-width:96px; }
.bxr-fact-label{ font-size:11px; font-weight:600; color:#4F46E5; display:block; margin-bottom:2px; }
.bxr-fact-value{ font-size:13px; font-weight:700; color:#181C32; line-height:1.2; }
/* Stage badge colors set per-candidate via inline CSS custom properties —
   Bench=blue, Placed=green, Hold=orange. */
.bxr-bench-tag{ display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:600; padding:2px 9px; border-radius:20px; background:var(--bxr-stage-bg,#F0FDF4); color:var(--bxr-stage-color,#16A34A); border:1.5px solid var(--bxr-stage-border,#86EFAC); width:max-content; }
.bxr-bench-dot{ width:6px; height:6px; border-radius:50%; background:var(--bxr-stage-color,#16A34A); flex-shrink:0; }
.bxr-kpis{ display:flex; align-items:stretch; flex:1; justify-content:space-evenly; min-width:300px; }
.bxr-kpi{ display:flex; flex-direction:column; align-items:center; gap:6px; padding:0 16px; position:relative; justify-content:center; }
.bxr-kpi+.bxr-kpi::before{ content:''; position:absolute; left:0; top:8%; height:84%; width:1px; background:#EEF0F4; }
.bxr-kpi-lbl{ font-size:11px; font-weight:600; color:#4F46E5; white-space:nowrap; }
.bxr-kpi-icon{ width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
.bxr-kpi-icon.vi{ background:#EFF6FF; color:#2563EB; }
.bxr-kpi-icon.dl{ background:#F0FDF4; color:#16A34A; }
.bxr-kpi-icon.aj{ background:#FFF7ED; color:#EA580C; }
.bxr-kpi-icon.iv{ background:#EEF2FF; color:#4F46E5; }
.bxr-kpi-num{ font-size:15px; font-weight:700; color:#181C32; line-height:1.1; }

/* ══ 2. SUMMARY CARDS (== tabs) — EXACT port of sections/service_company/
   apptrackerHeader.blade.php's .apth-card component, identical to
   bench_explore.blade.php's own copy so all three pages share one look. ══ */
.bxr-sum-row{ display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:10px; margin-bottom:16px; width:100%; }
.bxr-sum-card{ display:flex; flex-direction:column; min-width:0; overflow:hidden; background:#fff; border:1px solid #e4e6ef; border-radius:10px; padding:10px 12px; text-decoration:none; transition:border-color .15s, background-color .15s; }
.bxr-sum-card:hover{ text-decoration:none; border-color:#b5b5c3; }
a.bxr-sum-link{ color:inherit; cursor:pointer; }
.bxr-sum-card.bxr-sum-active{ border-color:var(--bxr-accent); background:var(--bxr-tint); }
.bxr-sum-card.sc-p{ --bxr-accent:#6D28D9; --bxr-tint:#F0EDFF; }
.bxr-sum-card.sc-b{ --bxr-accent:#1D4ED8; --bxr-tint:#EBF5FF; }
.bxr-sum-card.sc-o{ --bxr-accent:#C2410C; --bxr-tint:#FFF3EA; }
.bxr-sum-label{ font-size:11px; font-weight:500; color:#7e8299; margin:0 0 5px; line-height:1.3; display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2; overflow:hidden; transition:color .15s, font-weight .15s; }
.bxr-sum-card.bxr-sum-active .bxr-sum-label{ font-weight:700; color:#181c32; }
.bxr-sum-row2{ display:flex; align-items:center; justify-content:space-between; gap:6px; }
.bxr-sum-row2-left{ display:flex; align-items:center; gap:6px; min-width:0; }
.bxr-sum-icon{ width:19px; height:19px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:var(--bxr-tint); color:var(--bxr-accent); font-size:10px; }
.bxr-sum-icon svg{ width:10px; height:10px; }
.bxr-sum-num{ font-size:13px; font-weight:700; color:#181c32; line-height:1.2; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.bxr-sum-growth{ font-size:9px; font-weight:500; color:var(--bs-primary,#3699ff); white-space:nowrap; display:inline-flex; align-items:center; gap:2px; flex-shrink:0; }
@media(max-width:576px){ .bxr-sum-row{ grid-template-columns:1fr; } }

/* ══ 3. TOOLBAR — identical search/sort/refresh to bench_explore.blade.php,
   plus a Filter button (this page has real filters, bench_explore doesn't) ══ */
.bxr-toolbar-card{ background:#fff; padding:14px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid #F0F2F5; margin-bottom:16px; flex-wrap:wrap; }
.bxr-search-wrap2{ flex:1; min-width:220px; position:relative; }
.bxr-search-icon2{ position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#CBD5E1; display:flex; }
.bxr-search-input{ width:100%; height:44px; border:1.5px solid #E5E9F0; border-radius:10px; padding:0 16px 0 42px; font-size:13px; font-weight:500; color:#334155; background:#F8FAFC; outline:none; font-family:'Poppins',sans-serif; transition:border-color .18s, box-shadow .18s; }
.bxr-search-input:focus{ border-color:#2563EB; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.07); }
.bxr-sort-wrap{ position:relative; width:190px; flex-shrink:0; }
.bxr-sort-btn{ width:100%; height:44px; border:1.5px solid #E5E9F0; border-radius:10px; background:#F8FAFC; padding:0 12px; display:flex; align-items:center; gap:8px; font-size:13px; font-weight:500; color:#334155; cursor:pointer; }
.bxr-sort-btn svg{ flex-shrink:0; color:#94A3B8; }
.bxr-sort-btn .arrow{ margin-left:auto; transition:transform .15s; color:#94A3B8; }
.bxr-sort-btn.open .arrow{ transform:rotate(180deg); }
.bxr-sort-panel{ position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:50; background:#fff; border:1px solid #E5E9F0; border-radius:10px; box-shadow:0 8px 24px rgba(15,23,42,.09); padding:6px; max-height:260px; overflow-y:auto; }
.bxr-sort-item{ padding:9px 12px; font-size:12.5px; font-weight:500; color:#374151; border-radius:7px; cursor:pointer; }
.bxr-sort-item:hover{ background:#F5F8FF; }
.bxr-sort-item.active{ background:#EFF6FF; color:#2563EB; font-weight:700; }
.bxr-refresh{ width:44px; height:44px; border:1.5px solid #E5E9F0; border-radius:10px; background:#F8FAFC; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:17px; color:#64748B; transition:all .2s; flex-shrink:0; }
.bxr-refresh:hover{ background:#2563EB; color:#fff; border-color:#2563EB; transform:rotate(90deg); }
.bxr-tbl-loading{ display:none; align-items:center; justify-content:center; width:44px; height:44px; flex-shrink:0; }

/* Filter button — blue icon/text at rest per spec (not just on hover),
   same 44px height as the sort control next to it. */
.rrj-filter-btn{
    height:44px; padding:0 18px; border-radius:10px !important;
    font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:7px;
    white-space:nowrap; flex-shrink:0; position:relative;
    background:#EFF6FF !important; border:1.5px solid #BFDBFE !important; color:#1D4ED8 !important;
}
.rrj-filter-btn:hover{ background:#DBEAFE !important; border-color:#93C5FD !important; }
.rrj-count-badge{ font-size:10px; }

/* ══ 4. FILTER DRAWER — Similar-Candidates simf- design language, renamed
   rrj- only to keep IDs unique on this page; every class/spacing value below
   is copied 1:1 from apptracker/similar_candidates.blade.php. ══ */
#rrj_filter_drawer.rrj-drawer{ position:fixed !important; top:0 !important; right:0 !important; bottom:0 !important; left:auto !important; height:100vh !important; max-height:100vh !important; overflow:hidden !important; z-index:1200; }
#rrj_card.rrj-card{ display:flex !important; flex-direction:column !important; font-family:'Poppins',sans-serif; position:relative !important; height:100vh !important; max-height:100vh !important; overflow:hidden !important; }
.rrj-header{ display:flex; align-items:flex-start; justify-content:space-between; padding:20px 20px 16px; border-bottom:1px solid #eef0f4; flex-shrink:0; }
.rrj-header-icon{ width:40px; height:40px; border-radius:10px; background:#eef2ff; color:#4f46e5; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rrj-title{ font-size:17px; font-weight:700; color:#181c32; margin:0; }
.rrj-count-pill{ background:#2563eb; color:#fff; font-size:11px; font-weight:700; border-radius:50%; min-width:20px; height:20px; display:inline-flex; align-items:center; justify-content:center; padding:0 4px; }
.rrj-subtitle{ font-size:12px; color:#7e8299; margin:2px 0 0; }
.rrj-close{ width:30px; height:30px; border-radius:8px; border:none; background:transparent; color:#7e8299; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rrj-close:hover{ background:#f1f5f9; color:#334155; }
.rrj-activebar{ padding:12px 0; border-bottom:1px solid #eef0f4; }
.rrj-activebar-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
.rrj-activebar-label{ font-size:12px; font-weight:600; color:#181c32; }
.rrj-clearall{ font-size:12px; font-weight:500; color:#dc2626; display:inline-flex; align-items:center; gap:4px; text-decoration:none; }
.rrj-clearall:hover{ color:#b91c1c; }
.rrj-chips{ display:flex; flex-wrap:wrap; gap:6px; }
.rrj-chip{ display:inline-flex; align-items:center; gap:6px; background:#eef2ff; color:#3730a3; border:1px solid #e0e7ff; border-radius:8px; padding:5px 7px 5px 10px; font-size:11.5px; font-weight:500; }
.rrj-chip.mandatory{ background:#dbeafe; color:#1d4ed8; border-color:#bfdbfe; }
.rrj-chip-remove{ width:15px; height:15px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; background:rgba(0,0,0,.06); cursor:pointer; flex-shrink:0; }
.rrj-chip-remove:hover{ background:rgba(0,0,0,.14); }
#rrj_card .rrj-body{ flex:1 1 auto !important; min-height:0 !important; overflow-y:auto !important; overflow-x:hidden !important; padding:4px 20px 16px; }
#rrj_card .rrj-header, #rrj_card .rrj-footer{ flex-shrink:0 !important; }
.rrj-section{ padding:16px 0; border-bottom:1px solid #f1f2f6; }
.rrj-section-last{ border-bottom:none; }
.rrj-section-head{ display:flex; align-items:flex-start; gap:10px; margin-bottom:12px; }
.rrj-icon{ width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rrj-icon-blue{ background:#eef2ff; color:#4f46e5; }
.rrj-icon-green{ background:#ecfdf5; color:#059669; }
.rrj-icon-amber{ background:#fffbeb; color:#b45309; }
.rrj-icon-rose{ background:#fff1f2; color:#be123c; }
.rrj-icon-teal{ background:#ecfeff; color:#0e7490; }
.rrj-icon-sky{ background:#f0f9ff; color:#0369a1; }
.rrj-section-title{ font-size:13.5px; font-weight:600; color:#181c32; display:flex; align-items:center; gap:6px; }
.rrj-section-desc{ font-size:11.5px; color:#99a1b7; margin-top:1px; }
.rrj-badge-primary{ font-size:9.5px; font-weight:600; color:#2563eb; background:#dbeafe; border-radius:4px; padding:1px 6px; }
.rrj-mini-label{ font-size:11px; font-weight:500; color:#7e8299; margin-bottom:4px; display:block; }
.rrj-salary-row{ flex-wrap:nowrap; }
.rrj-salary-row > [class^="col-"]{ padding-left:4px; padding-right:4px; min-width:0; }
.rrj-salary-row .form-select, .rrj-salary-row .form-control{ font-size:11px; padding-left:8px; padding-right:8px; }
.rrj-salary-row .form-select{ padding-right:20px; }
/* Salary text-visibility fix: an inherited/global rule was making the typed
   digits invisible (same symptom as a transparent -webkit-text-fill-color
   from browser autofill styling) — force both explicitly on this page's own
   number inputs so what you type is always visible, cursor included. */
.rrj-section input[type=number]{ color:#1A1D23 !important; -webkit-text-fill-color:#1A1D23 !important; opacity:1 !important; caret-color:#1A1D23 !important; }
.rrj-loc-inputwrap{ position:relative; }
.rrj-loc-chevron{ position:absolute; top:50%; right:8px; transform:translateY(-50%); width:22px; height:22px; border:none; background:transparent; color:#99a1b7; display:flex; align-items:center; justify-content:center; z-index:2; transition:transform .15s; }
.rrj-loc-chevron.open{ transform:translateY(-50%) rotate(180deg); }
.rrj-loc-suggestions{ margin-top:10px; border:1px solid #eef0f4; border-radius:8px; padding:10px; background:#fafbfc; }
.rrj-loc-label{ font-size:11px; font-weight:600; color:#99a1b7; margin-bottom:6px; }
.rrj-loc-chip-row{ display:flex; flex-wrap:wrap; gap:6px; margin-bottom:4px; }
.rrj-loc-chip{ display:inline-flex; align-items:center; gap:5px; background:#fff; border:1px solid #e4e6ef; border-radius:8px; padding:5px 10px; font-size:11.5px; font-weight:500; color:#3f4254; }
.rrj-loc-chip:hover{ border-color:#93c5fd; background:#eff6ff; color:#1d4ed8; }
.rrj-pill-row{ display:flex; flex-wrap:wrap; gap:8px; }
.rrj-pill-row .btn-check{ position:absolute; opacity:0; pointer-events:none; }
.rrj-pill{ display:inline-flex; align-items:center; gap:6px; background:#fff; border:1px solid #e4e6ef; border-radius:8px; padding:7px 12px; font-size:12px; font-weight:500; color:#3f4254; cursor:pointer; transition:border-color .15s, background-color .15s, color .15s; }
.rrj-pill-row .btn-check:checked + .rrj-pill{ border-color:#2563eb; background:#eff6ff; color:#1d4ed8; font-weight:600; }
.rrj-footer{ display:flex; align-items:center; justify-content:space-between; gap:10px; padding:14px 20px; border-top:1px solid #eef0f4; background:#f8f9fb; flex-shrink:0; flex-wrap:wrap; }
.rrj-footer-count{ display:flex; align-items:center; gap:10px; min-width:0; }
.rrj-footer-icon{ width:36px; height:36px; border-radius:8px; background:#dbeafe; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rrj-footer-number{ font-size:15px; font-weight:700; color:#181c32; line-height:1.2; }
.rrj-footer-label{ font-size:10.5px; color:#7e8299; }

/* ═══════════════════════════════════════════════
   JERO V2 JOB CARD DESIGN — MATCHING ATTACHMENT
   ═══════════════════════════════════════════════ */
.jero-job-card-v2 {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #10b981;
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    display: grid;
    grid-template-columns: 1.3fr 1.1fr 1.1fr 1fr 1.1fr 170px;
    gap: 16px;
    align-items: center;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}
.jero-job-card-v2:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-color: #cbd5e1;
}
.jero-col-header .tag-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
    flex-wrap: wrap;
}
.jero-tag-open {
    background: #ecfdf5;
    color: #10b981;
    border: 1px solid #a7f3d0;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.jero-tag-open::before {
    content: '';
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
}
.jero-tag-type {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 12px;
}
.jero-tag-req {
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    font-size: 10px;
    font-weight: 500;
    padding: 2px 8px;
    border-radius: 12px;
}
.jero-job-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 2px 0;
    cursor: pointer;
    line-height: 1.3;
}
.jero-job-title:hover { color: #2563eb; }
.jero-job-dept {
    font-size: 12px;
    font-weight: 500;
    color: #2563eb;
    margin-bottom: 8px;
}
.jero-skill-list {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.jero-skill-pill {
    background: #f1f5f9;
    color: #475569;
    font-size: 11px;
    font-weight: 500;
    padding: 2px 8px;
    border-radius: 4px;
}
.jero-skill-pill.matched {
    background: #e0e7ff;
    color: #3730a3;
    font-weight: 600;
}
.jero-specs-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 12px;
    color: #334155;
}
.jero-spec-item {
    display: flex;
    align-items: center;
    gap: 6px;
}
.jero-spec-item i {
    color: #64748b;
    font-size: 14px;
    width: 16px;
}
.jero-time-list {
    display: flex;
    flex-direction: column;
    gap: 5px;
    font-size: 11.5px;
}
.jero-time-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #475569;
}
.jero-time-item i {
    color: #64748b;
    font-size: 13px;
}
.jero-text-success { color: #16a34a !important; font-weight: 600; }
.jero-text-danger { color: #dc2626 !important; font-weight: 700; }
.jero-match-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 14px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.jero-match-head {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 5px;
}
.jero-match-num {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    margin: 4px 0 2px;
}
.jero-match-sub {
    font-size: 11px;
    color: #64748b;
}
.jero-match-link {
    font-size: 11.5px;
    font-weight: 700;
    color: #2563eb;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 6px;
}
.jero-match-link:hover { text-decoration: underline; }
.jero-terms-card {
    background: #ffffff;
    padding: 4px 6px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.jero-terms-head {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
}
.jero-terms-row {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    margin-bottom: 3px;
}
.jero-terms-label { color: #64748b; }
.jero-terms-val { font-weight: 700; color: #0f172a; }
.jero-terms-link {
    font-size: 11.5px;
    font-weight: 700;
    color: #2563eb;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 6px;
    cursor: pointer;
}
.jero-actions-stack {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.jero-btn-submit {
    background: #0066ff !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    border-radius: 6px !important;
    padding: 8px 12px !important;
    border: none !important;
    text-align: center;
    text-decoration: none;
    box-shadow: 0 2px 4px rgba(0, 102, 255, 0.2);
}
.jero-btn-submit:hover {
    background: #0052cc !important;
    color: #ffffff !important;
}
.jero-btn-outline {
    background: #ffffff !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
    font-weight: 600 !important;
    font-size: 11.5px !important;
    border-radius: 6px !important;
    padding: 6px 12px !important;
    text-align: center;
    cursor: pointer;
    text-decoration: none;
}
.jero-btn-outline:hover {
    background: #f8fafc !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
}
.jero-action-tools {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 2px 4px;
    color: #94a3b8;
}
.jero-action-tools i {
    cursor: pointer;
    font-size: 16px;
}
.jero-action-tools i:hover { color: #334155; }
@media (max-width: 1200px) {
    .jero-job-card-v2 {
        grid-template-columns: 1fr 1fr;
    }
}
@media (max-width: 768px) {
    .jero-job-card-v2 {
        grid-template-columns: 1fr;
    }
}
.rrj-footer-actions{ display:flex; gap:6px; }
.rrj-footer-actions .btn{ font-size:11.5px; padding:6px 10px; display:inline-flex; align-items:center; gap:5px; }
@media (max-width:576px){ .rrj-footer{ flex-direction:column; align-items:stretch; } .rrj-footer-actions{ justify-content:stretch; } .rrj-footer-actions .btn{ flex:1 1 auto; justify-content:center; } }

/* Tagify chip interactive states — visual colors are inline (see JS
   templates below) so they can't be overridden by plugins.bundle.css */
.tagify__tag .star{ cursor:pointer; }
.tagify__tag__removeBtn, .tagify__tag__removeBtn::before, .tagify__tag__removeBtn::after{ mask-image:none !important; -webkit-mask-image:none !important; content:none !important; }
.tagify__tag__removeBtn:hover{ background:rgba(37,99,235,.28) !important; color:#1d4ed8 !important; }
body > .tagify__dropdown{ z-index:1400 !important; }

/* ══ 5. LOADING SPINNER — same viewport-fixed treatment as Similar Candidates ══ */
.loadingMessagebenchCandidateRecommendedReqAjax{ display:none; position:fixed; inset:0; z-index:1050; align-items:center; justify-content:center; background:rgba(255,255,255,.55); }
.loadingMessagebenchCandidateRecommendedReqAjax .spinner-border{ width:2.75rem; height:2.75rem; }

/* ══ 6. RESULTS + EMPTY STATE ══ */
.rrj-results-wrap{ background:#fff; border-radius:14px; border:1px solid #EEF0F4; padding:16px; }
.rrj-empty{ display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:56px 24px; }
.rrj-empty-icon{ width:64px; height:64px; border-radius:16px; background:#F1F5F9; color:#94A3B8; display:flex; align-items:center; justify-content:center; margin-bottom:16px; font-size:28px; }
.rrj-empty-title{ font-size:15px; font-weight:700; color:#181C32; margin-bottom:4px; }
.rrj-empty-desc{ font-size:12.5px; color:#7E8299; max-width:360px; margin-bottom:16px; }

/* ═══════════════════════════════════════════════
   REDESIGNED JOB LISTING GRID (table-style rows).
   Modeled on demo/css/joblist.css (service_company/jobs) so the
   Recommended-Jobs view feels like an extension of the Jobs module,
   while staying consistent with the Application Tracker design language.
   Columns: Job Details | Experience | Hiring & Loc | Hiring Mgr | Status | Actions,
   with a compact skills sub-row below the grid (preserves skill/match
   logic from before the redesign).
   ═══════════════════════════════════════════════ */
.agencies-list{ display:flex; flex-direction:column; gap:0; }
.jc-table-header{
  display:grid;
  grid-template-columns: 2.6fr 1.4fr 1.4fr 1.2fr 0.95fr 1.4fr;
  align-items:center; gap:0; padding:9px 16px;
  background:#F7F8FC;
  border:1px solid #EEF0F4;
  border-radius:10px 10px 0 0;
  margin-bottom:-1px;
  font-family:'Poppins',sans-serif;
}
.jc-th{
  font-size:10.5px; font-weight:600; color:#7E8299;
  text-transform:uppercase; letter-spacing:0.4px;
  display:flex; align-items:center; gap:4px;
  padding-right:12px; white-space:nowrap;
}
.jc-row-wrap{
  position:relative;
  border:1px solid #EEF0F4;
  background:#fff;
  transition:box-shadow .18s, border-color .18s;
  border-radius:0; padding:0; overflow:hidden;
}
.jc-row-wrap + .jc-row-wrap{ border-top:none; }
.jc-row-wrap:last-of-type{ border-radius:0 0 10px 10px; }
.jc-row-wrap:hover{ background:#FAFBFF; border-color:#DBEAFE; }
.jc-row{
  display:grid;
  grid-template-columns: 2.6fr 1.4fr 1.4fr 1.2fr 0.95fr 1.4fr;
  align-items:start; padding:12px 16px; gap:0;
  font-family:'Poppins',sans-serif;
}
.jc-col{ min-width:0; padding-right:12px; }
.bench-badge{ position:absolute; top:0; right:16px; background:#EFF6FF; color:#1D4ED8; font-size:10px; font-weight:700; padding:3px 10px; border-radius:0 0 8px 8px; z-index:2; }

/* COL 1 — Job Details */
.jc-col-job{ display:flex; gap:10px; padding-right:12px; }
.job-logo{
  width:48px; height:48px; border-radius:10px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  font-size:18px; font-weight:800; color:#fff;
  overflow:hidden; align-self:flex-start; letter-spacing:0.5px;
}
.job-info{ flex:1; min-width:0; }
.job-title{
  font-size:12.5px; font-weight:700; color:#181C32;
  margin-bottom:2px; line-height:1.3;
  display:flex; align-items:center; gap:5px;
}
.job-title a{ color:inherit; text-decoration:none; }
.job-title a:hover{ color:#2563EB; }
.job-client{
  font-size:11px; color:#7E8299; font-weight:500;
  margin-bottom:6px; display:flex; align-items:center; gap:5px;
}
.job-client i{ color:#94A3B8; font-size:12px; }
.job-meta-line{
  display:flex; align-items:center; flex-wrap:wrap; gap:6px;
  font-size:10.5px; color:#94A3B8; margin-bottom:0;
}
.job-date-inline{ display:flex; align-items:center; gap:3px; white-space:nowrap; }
.job-date-inline i{ color:#94A3B8; font-size:11px; }
.meta-sep{ color:#E4E6EF; font-size:12px; line-height:1; }
.badge-inline{
  display:inline-flex; align-items:center; gap:4px;
  padding:1px 7px; border-radius:20px;
  font-size:10px; font-weight:600; white-space:nowrap; line-height:1.4;
}
.badge-fulltime{ background:#EEF6FF; color:#3B82F6; }
.badge-contract{ background:#FFF4EE; color:#F4682A; }
.badge-worktype i{ font-size:11px; }
.badge-remote{ background:#F0FDF4; color:#16A34A; }
.badge-hybrid{ background:#EEF2FF; color:#4F46E5; }
.badge-onsite{ background:#F8FAFC; color:#64748B; }
.badge-any{ background:#F5F3FF; color:#7C3AED; }

/* COL 2 — Experience (vertical: exp / salary / client-type badge) */
.jc-col-exp{ display:flex; flex-direction:column; gap:6px; }
.exp-row, .sal-row{
  display:flex; align-items:center; gap:6px;
  font-size:11.5px; font-weight:600; color:#181C32; line-height:1.2;
}
.exp-row svg{ width:13px; height:13px; color:#94A3B8; flex-shrink:0; }
.sal-row{ width:max-content; }
.sal-currency{
  display:inline-flex; width:13px; height:13px;
  align-items:center; justify-content:center; color:#94A3B8;
  font-size:11px; font-weight:700; flex-shrink:0;
}
.sal-mode{ font-size:9.5px; color:#94A3B8; font-weight:600; padding-left:2px; }

/* Reusable Client Type Badge — Internal (blue) / Direct (green).
   Single component reused everywhere on the platform; the tilted left
   stripe + soft pill body + colored icon reads instantly at any size. */
.client-badge{
  display:inline-flex; align-items:center; gap:6px;
  position:relative; padding:2px 10px 2px 10px;
  border-radius:6px;
  font-size:10.5px; font-weight:600; line-height:1.2;
  max-width:100%; font-family:'Poppins',sans-serif;
  border:1px solid transparent;
}
.client-badge-stripe{
  position:absolute; left:-1px; top:2px; bottom:2px; width:3px;
  border-radius:2px; background:currentColor;
}
.client-badge i{ font-size:11.5px; line-height:1; flex-shrink:0; }
.client-badge-label{ white-space:nowrap; }
.client-badge-id{ color:currentColor; opacity:0.7; font-weight:500; padding-left:2px; }
.client-badge--internal{
  background:#EBF5FF; color:#1D4ED8; border-color:#BFDBFE;
}
.client-badge--internal .client-badge-stripe{ background:#1D4ED8; }
.client-badge--direct{
  background:#ECFDF5; color:#047857; border-color:#A7F3D0;
}
.client-badge--direct .client-badge-stripe{ background:#059669; }

/* COL 3 — Hiring & Location */
.jc-col-loc{ display:flex; flex-direction:column; gap:3px; }
.loc-row{
  display:flex; align-items:center; gap:6px;
  font-size:11.5px; font-weight:600; color:#181C32; line-height:1.2;
}
.loc-row i{ color:#2563EB; font-size:12px; flex-shrink:0; }
.loc-mode{ font-size:10.5px; color:#7E8299; padding-left:18px; }

/* COL 4 — Hiring Manager */
.jc-col-hm{ display:flex; flex-direction:column; min-width:0; }
.hm-row{ display:flex; align-items:center; gap:8px; min-width:0; }
.hm-avatar{
  width:32px; height:32px; border-radius:8px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  font-weight:700; font-size:12px;
}
.hm-avatar--filled{ background:#EEF2FF; color:#4F46E5; }
.hm-avatar--empty{ background:#F1F5F9; color:#94A3B8; border:1px dashed #E4E6EF; }
.hm-info{ min-width:0; display:flex; flex-direction:column; gap:2px; }
.hm-name{ font-size:11.5px; font-weight:600; color:#181C32; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px; }
.hm-name.hmlisting{ cursor:pointer; }
.hm-name.hmlisting:hover{ color:#2563EB; }
.hm-role{ font-size:10px; color:#94A3B8; font-weight:500; }

/* COL 5 — Status */
.jc-col-status{ display:flex; flex-direction:column; gap:3px; min-width:0; }
.status-pill{
  display:inline-flex; align-items:center; gap:4px;
  padding:2px 8px; border-radius:999px;
  font-size:10.5px; font-weight:700;
  align-self:flex-start; max-width:100%;
}
.status-pill a{ color:inherit; text-decoration:none; }
.status-pill--hiring{ background:#DCFCE7; color:#15803D; border:1px solid #BBF7D0; }
.status-pill--closed, .status-pill--removed, .status-pill--inactive{ background:#FEE2E2; color:#B91C1C; border:1px solid #FECACA; }
.status-pill--hold, .status-pill--onhold{ background:#FEF3C7; color:#A16207; border:1px solid #FDE68A; }
.status-rec{ font-size:10px; color:#94A3B8; font-weight:500; padding-left:2px; }

/* COL 6 — Actions */
.jc-col-actions{ display:flex; flex-direction:column; align-items:flex-end; gap:6px; }
.favorite-icon{ cursor:pointer; color:#CBD5E1; font-size:16px; align-self:flex-end; }
.favorite-icon:hover{ color:#F43F5E; }
.action-stack{ display:flex; flex-direction:column; gap:5px; width:100%; }
.btn-xsm{ font-size:10.5px !important; padding:4px 10px !important; border-radius:6px !important; font-weight:600 !important; line-height:1.2 !important; gap:4px; display:inline-flex; align-items:center; justify-content:center; width:100%; }
.jc-tc-btn{ color:#4F46E5 !important; border-color:#E0E7FF !important; }
.jc-tc-btn:hover{ background:#EEF2FF !important; border-color:#C7D2FE !important; color:#4338CA !important; }
.action-status{
  display:inline-flex; align-items:center; gap:4px;
  padding:4px 8px; border-radius:6px;
  font-size:10.5px; font-weight:600; justify-content:center; width:100%;
}
.action-status--added{ background:#DCFCE7; color:#15803D; border:1px solid #BBF7D0; }
.action-status--sent{ background:#FEF3C7; color:#A16207; border:1px solid #FDE68A; }

/* Skills sub-row — preserved match logic, just visually tightened to fit
   beneath the grid without bloating row height. */
.jc-skills-row{ display:flex; flex-wrap:wrap; gap:4px; padding:0 16px 12px; align-items:center; }
.jc-skill-matched{ background:#DCFCE7; color:#15803D; border:1px solid #BBF7D0; }
.jc-skill-neutral{ background:#F1F5F9; color:#475569; }
.jc-skill-overflow{ background:#EEF2FF; color:#3730A3; border:1px solid #E0E7FF; }
.jc-skill-overflow a{ color:inherit; text-decoration:none; }
.tag-badge{ font-size:10px; font-weight:600; padding:2px 8px; border-radius:4px; display:inline-block; }

/* Responsive — keep the design legible as the viewport narrows. */
@media (max-width:1180px){
  .jc-table-header, .jc-row{ grid-template-columns: 2.4fr 1.3fr 1.3fr 1.1fr 0.9fr 1.3fr; }
  .job-logo{ width:42px; height:42px; font-size:16px; }
}
@media (max-width:980px){
  .jc-table-header{ display:none; }
  .jc-row{ grid-template-columns: 1fr; gap:8px; padding:14px 16px; }
  .jc-col{ padding-right:0; border-bottom:1px dashed #EEF0F4; padding-bottom:8px; }
  .jc-col:last-of-type{ border-bottom:none; padding-bottom:0; }
  .jc-col-actions{ align-items:flex-start; }
  .action-stack{ flex-direction:row; flex-wrap:wrap; }
  .action-stack .btn-xsm, .action-stack .action-status{ width:auto; }
}

@media(max-width:900px){ .bxr-hero{ flex-direction:column; align-items:flex-start; } .bxr-hero-sep{ display:none; } .bxr-kpis{ width:100%; min-width:0; flex-wrap:wrap; } .bxr-sum-row{ grid-template-columns:1fr; } .card-content{ flex-direction:column; } .card-right{ align-items:flex-start; margin-left:0; } }
</style>
@endpush

@section('content')

<div aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;opacity:0;z-index:-9;">
    <input type="hidden" id="inputSearch" value="">
</div>

@php
    /* url('.../bench_explore/') drops the trailing slash, so concatenating
       the id directly produced "bench_explore123" — a 404 on every click. */
    $bench_explore_url      = url('service_company/bench-candidates/bench_explore') . '/' . @$bench_detail->id . '/all';
    $recommend_req_url      = url('service_company/bench-candidates/recommend_req/')          .'/'. @$bench_detail->id;
    $recommend_internal_req = url('service_company/bench-candidates/recommend_internal_req/') .'/'. @$bench_detail->id;
@endphp

@if(in_array('view_jobs', $userPermissions))
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
 <div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-0">
  <div class="bxr-page">

  {{-- ══ SECTION 1 · PROFILE HERO — segmented stat card:
       identity | Experience + Bill Rate | Bench Status + On Bench | icon KPIs ══ --}}
  @php
    $imgFile  = @$bench_detail->profile_image ?? @$bench_detail->image ?? @$bench_detail->photo ?? null;
    /* $bench_detail->name is the candidate's CURRENT STAGE (Bench/Hold/
       Placed/Pool/etc, via the bench_master_status join), not their
       name — that's full_name. */
    $candName = @$bench_detail->full_name ?? 'Candidate';
    $initLtr  = strtoupper(substr($candName,0,1));
    $bxrStageLabel = ucwords(@$bench_detail->name ?? 'Bench');
    $bxrStageKey = strtolower($bxrStageLabel);
    if (str_contains($bxrStageKey, 'placed'))     { $bxrStageColor='#16A34A'; $bxrStageBg='#F0FDF4'; $bxrStageBorder='#86EFAC'; }
    elseif (str_contains($bxrStageKey, 'hold'))   { $bxrStageColor='#EA580C'; $bxrStageBg='#FFF7ED'; $bxrStageBorder='#FDBA74'; }
    elseif (str_contains($bxrStageKey, 'bench'))  { $bxrStageColor='#2563EB'; $bxrStageBg='#EFF6FF'; $bxrStageBorder='#93C5FD'; }
    else                                           { $bxrStageColor='#64748B'; $bxrStageBg='#F1F5F9'; $bxrStageBorder='#CBD5E1'; }
    /* Bill Rate — same fields the old bench-toolbar partial displayed. */
    $bxrBillRate = '—';
    if (@$bench_detail->bill_rate_amount) {
        $bxrCurSymMap = \App\Helper\GeneralMethod::currencySymbole();
        $bxrModeAbbr  = [0=>'yr',1=>'mo',2=>'hr'][(int)@$bench_detail->bill_rate_mode] ?? '';
        $bxrBillRate  = ($bxrCurSymMap[@$bench_detail->bill_rate_currency] ?? '₹')
                      . number_format((float)$bench_detail->bill_rate_amount)
                      . ($bxrModeAbbr !== '' ? ' /'.$bxrModeAbbr : '');
    }
    /* On Bench — days since the candidate record was created. */
    $bxrOnBenchDays = '—';
    if (@$bench_detail->created_at) {
        $bxrOnBenchDays = (int) abs(\Carbon\Carbon::parse($bench_detail->created_at)->diffInDays(now())) . ' Days';
    }
  @endphp
  <div class="bxr-hero">
    <div class="bxr-hero-id">
      <div class="bxr-av-wrap">
        @if($imgFile)
          <img src="{{ asset('assets/images/candidates/'.$imgFile) }}" class="bxr-avatar" alt="{{ $candName }}">
        @else
          <div class="bxr-avatar-init">{{ $initLtr }}</div>
        @endif
        <div class="bxr-online-dot"></div>
      </div>
      <div>
        <div class="bxr-name">{{ ucwords($candName) }}</div>
        <div class="bxr-role">{{ ucwords(@$bench_detail->current_designation ?? '') }}</div>
        @if(@$bench_detail->city_name)
        <div class="bxr-loc"><i class="ti ti-map-pin"></i>{{ @$bench_detail->city_name }}@if(@$bench_detail->state_name), {{ @$bench_detail->state_name }}@endif</div>
        @endif
      </div>
    </div>

    <div class="bxr-hero-sep"></div>
    <div class="bxr-facts">
      <div><span class="bxr-fact-label">Experience</span><span class="bxr-fact-value">{{ @$bench_detail->total_exp ?? 0 }} Years</span></div>
      <div><span class="bxr-fact-label">Bill Rate</span><span class="bxr-fact-value">{!! $bxrBillRate !!}</span></div>
    </div>

    <div class="bxr-hero-sep"></div>
    <div class="bxr-facts">
      <div>
        <span class="bxr-fact-label">Bench Status</span>
        <span class="bxr-bench-tag" style="--bxr-stage-bg:{{ $bxrStageBg }};--bxr-stage-color:{{ $bxrStageColor }};--bxr-stage-border:{{ $bxrStageBorder }};"><span class="bxr-bench-dot"></span>{{ $bxrStageLabel }}</span>
      </div>
      <div><span class="bxr-fact-label">On Bench</span><span class="bxr-fact-value">{{ $bxrOnBenchDays }}</span></div>
    </div>

    <div class="bxr-hero-sep"></div>
    <div class="bxr-kpis">
      <div class="bxr-kpi">
        <span class="bxr-kpi-lbl">Views</span>
        <span class="bxr-kpi-icon vi"><i class="ti ti-eye"></i></span>
        <span class="bxr-kpi-num">{{ @$heroKpis['views'] ?? 0 }}</span>
      </div>
      <div class="bxr-kpi">
        <span class="bxr-kpi-lbl">Downloads</span>
        <span class="bxr-kpi-icon dl"><i class="ti ti-download"></i></span>
        <span class="bxr-kpi-num">{{ @$heroKpis['downloads'] ?? 0 }}</span>
      </div>
      <div class="bxr-kpi">
        <span class="bxr-kpi-lbl">Applied</span>
        <span class="bxr-kpi-icon aj"><i class="ti ti-send"></i></span>
        <span class="bxr-kpi-num">{{ @$recomCount['recommend_req_counter']??0 }}</span>
      </div>
      <div class="bxr-kpi">
        <span class="bxr-kpi-lbl">Interviews</span>
        <span class="bxr-kpi-icon iv"><i class="ti ti-bell"></i></span>
        <span class="bxr-kpi-num">{{ @$heroKpis['interviews'] ?? 0 }}</span>
      </div>
    </div>
  </div>

  {{-- ══ SECTION 2 · SUMMARY CARDS (== tabs) — Recommend Client Jobs active here ══ --}}
  <div class="bxr-sum-row">
    <a href="{{ $bench_explore_url }}" class="bxr-sum-card sc-p bxr-sum-link">
      <p class="bxr-sum-label">Total Companies Added</p>
      <div class="bxr-sum-row2">
        <span class="bxr-sum-row2-left">
          <span class="bxr-sum-icon"><i class="ti ti-users-group"></i></span>
          <span class="bxr-sum-num">{{ $recomCount['bench_explore_counter'] }}</span>
        </span>
        <span class="bxr-sum-growth">View List
          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </span>
      </div>
    </a>
    <a href="{{ $recommend_req_url }}" class="bxr-sum-card sc-b bxr-sum-link bxr-sum-active">
      <p class="bxr-sum-label">Recommended Client Jobs</p>
      <div class="bxr-sum-row2">
        <span class="bxr-sum-row2-left">
          <span class="bxr-sum-icon"><i class="ti ti-briefcase"></i></span>
          <span class="bxr-sum-num" id="recommend_req_id">{{ $recomCount['recommend_req_counter'] }}</span>
        </span>
        <span class="bxr-sum-growth">View List
          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </span>
      </div>
    </a>
    <a href="{{ $recommend_internal_req }}" class="bxr-sum-card sc-o bxr-sum-link">
      <p class="bxr-sum-label">Recommended Internal Jobs</p>
      <div class="bxr-sum-row2">
        <span class="bxr-sum-row2-left">
          <span class="bxr-sum-icon"><i class="ti ti-building-skyscraper"></i></span>
          <span class="bxr-sum-num" id="recommend_req_internal_id">{{ $recomCount['recommend_internal_req_counter'] }}</span>
        </span>
        <span class="bxr-sum-growth">View List
          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </span>
      </div>
    </a>
  </div>
  <span id="bench_explore_pool_id" style="display:none;">{{ $recomCount['bench_explore_counter'] ?? 0 }}</span>

  {{-- ══ SECTION 3 · SEARCH / SORT / FILTER / REFRESH TOOLBAR ══ --}}
  <div class="bxr-toolbar-card">
    <div class="bxr-search-wrap2">
      <span class="bxr-search-icon2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      </span>
      <input type="text" id="kt_tagify_2" class="bxr-search-input" placeholder="Search by job title, company or recruiter..." autocomplete="off">
    </div>

    <div class="bxr-sort-wrap" id="rrj_sort_wrap">
      <button type="button" class="bxr-sort-btn" id="rrj_sort_btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
        <span id="rrj_sort_label">Best Match</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </button>
      <select id="hiringtype" style="display:none;">
        <option value="10" selected>Best Match</option>
        <option value="1">Newest to Oldest (Date)</option>
        <option value="2">Oldest to Newest (Date)</option>
        <option value="3">Low to High (Salary)</option>
        <option value="4">High to Low (Salary)</option>
      </select>
      <div class="bxr-sort-panel d-none" id="rrj_sort_panel"></div>
    </div>

    <button type="button" id="rrj_filter_toggle" class="btn rrj-filter-btn position-relative">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
      <span>Filter<span id="rrj_filter_count_text"></span></span>
      <span class="badge badge-circle badge-primary rrj-count-badge d-none" id="rrj_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
    </button>

    <div class="bxr-tbl-loading loadingMessagebenchCandidateRecommendedReqAjax" style="position:static;background:none;" title="Processing...">
      <span class="spinner-border spinner-border-sm text-primary" style="width:13px;height:13px;border-width:2px;"></span>
    </div>

    <button class="bxr-refresh" onclick="fetchBenchRecommendedReqCandidateList()" title="Refresh">
      <i class="ti ti-refresh"></i>
    </button>
  </div>

  {{-- ══ Applied Filters summary card — hidden until Apply Filters is clicked ══ --}}
  <div class="rrj-activebar d-none" id="rrj_applied_card" style="background:#fff;border:1px solid #eef0f4;border-radius:14px;padding:14px 16px;margin-bottom:16px;">
    <div class="rrj-activebar-head">
      <span class="rrj-activebar-label">Active Filters <span id="rrj_applied_count">(0)</span></span>
      <a href="javascript:void(0);" class="rrj-clearall" id="rrj_applied_clear_all">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
        Clear All
      </a>
    </div>
    <div class="rrj-chips" id="rrj_applied_chips"></div>
  </div>

  {{-- ══ Filter Drawer ══════════════════════════════════════════ --}}
  <div id="rrj_filter_drawer" class="bg-body rrj-drawer" data-kt-drawer="true" data-kt-drawer-name="rrj-filter"
      data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
      data-kt-drawer-width="{default:'100%', 'sm':'90%', 'lg':'80%'}" data-kt-drawer-direction="end"
      data-kt-drawer-toggle="#rrj_filter_toggle" data-kt-drawer-close="#rrj_filter_close">
    <div id="rrj_card" class="card shadow-none rounded-0 w-100 h-100 rrj-card">
      <div class="rrj-header">
        <div class="d-flex align-items-center gap-3">
          <span class="rrj-header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
          </span>
          <div>
            <div class="d-flex align-items-center gap-2">
              <h3 class="rrj-title">Filter Jobs</h3>
              <span class="rrj-count-pill" id="rrj_header_count">0</span>
            </div>
            <p class="rrj-subtitle">Narrow down jobs matching this candidate</p>
          </div>
        </div>
        <button type="button" class="rrj-close" id="rrj_filter_close" title="Close">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="rrj-body">
        <form id="createSchedule" class="ajax-form" method="post" onsubmit="return false;">

          <div class="rrj-activebar d-none" id="rrj_active_bar">
            <div class="rrj-activebar-head">
              <span class="rrj-activebar-label">Active Filters <span id="rrj_active_count">(0)</span></span>
              <a href="javascript:void(0);" class="rrj-clearall" id="rrj_clear_all">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                Clear All
              </a>
            </div>
            <div class="rrj-chips" id="rrj_active_chips"></div>
          </div>

          {{-- Skills --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 6-6 6 6 6M16 6l6 6-6 6"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Skills <span class="rrj-badge-primary">Primary</span></div>
                <div class="rrj-section-desc">Add skills required for the job — click ★ to make one mandatory</div>
              </div>
            </div>
            {{-- Pre-filled with the candidate's raw skills ONLY (not
                 $default_skill, which also appends current_designation as a
                 fake "skill") — this is the exact same field
                 benchjobAndRecomCount() filters by for the summary-card
                 count, so the initial list total matches the card instead of
                 diverging from it. --}}
            <input class="form-control form-control-solid d-flex align-items-center" name="kt_tagify_users" value="{{ @$bench_detail->skills }}" placeholder="" id="kt_tagify_users" />
          </div>

          {{-- Location --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-green">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Job Location</div>
                <div class="rrj-section-desc">Preferred cities or work locations</div>
              </div>
            </div>
            <div class="rrj-loc-inputwrap" id="rrj_loc_inputwrap">
              <input class="form-control form-control-solid" value="{{ @$default_loc }}" id="kt_tagify_location" placeholder="Search location (e.g. Bangalore, Pune, Remote)" />
              <button type="button" class="rrj-loc-chevron" id="rrj_loc_chevron" title="Show suggestions">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
              </button>
            </div>
            <div class="rrj-loc-suggestions d-none" id="rrj_loc_panel">
              <div class="rrj-loc-label">Popular Locations</div>
              <div class="rrj-loc-chip-row">
                <button type="button" class="rrj-loc-chip" data-city="Bangalore">Bangalore</button>
                <button type="button" class="rrj-loc-chip" data-city="Hyderabad">Hyderabad</button>
                <button type="button" class="rrj-loc-chip" data-city="Pune">Pune</button>
                <button type="button" class="rrj-loc-chip" data-city="Mumbai">Mumbai</button>
                <button type="button" class="rrj-loc-chip" data-city="Chennai">Chennai</button>
                <button type="button" class="rrj-loc-chip" data-city="Remote">Remote</button>
              </div>
              <div class="rrj-loc-label mt-2">Other Suggestions</div>
              <div class="rrj-loc-chip-row">
                <button type="button" class="rrj-loc-chip" data-city="Noida">Noida</button>
                <button type="button" class="rrj-loc-chip" data-city="Gurgaon">Gurgaon</button>
                <button type="button" class="rrj-loc-chip" data-city="Kolkata">Kolkata</button>
                <button type="button" class="rrj-loc-chip" data-city="Ahmedabad">Ahmedabad</button>
              </div>
            </div>
          </div>

          {{-- Experience --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-green">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Experience (Years)</div>
                <div class="rrj-section-desc">Required experience range</div>
              </div>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="rrj-mini-label">Minimum Experience</label>
                <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                  <option value="" @if(@$min_exp==='' || @$min_exp===null) selected @endif>Min Experience</option>
                  <option value="0" @if(0==@$min_exp) selected @endif>Freshers</option>
                  @for($i=1; $i<=30; $i++)
                  <option value="{{ $i }}" @if($i==@$min_exp) selected @endif>{{ $i }} Year{{ ($i>=1) ? 's' : '' }}</option>
                  @endfor
                </select>
              </div>
              <div class="col-6">
                <label class="rrj-mini-label">Maximum Experience</label>
                <select class="form-select form-select-solid" name="max_exp" id="max_exp">
                  <option value="" @if(@$max_exp==='' || @$max_exp===null) selected @endif>Max Experience</option>
                  <option value="0" @if(0==@$max_exp) selected @endif>Freshers</option>
                  @for($i=1; $i<=30; $i++)
                  <option value="{{ $i }}" @if($i==@$max_exp) selected @endif>{{ $i }} Year{{ ($i>=1) ? 's' : '' }}</option>
                  @endfor
                </select>
              </div>
            </div>
          </div>

          {{-- Salary --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-amber">
                <span style="font-size:15px;font-weight:700;line-height:1;">₹</span>
              </span>
              <div>
                <div class="rrj-section-title">Salary (CTC)</div>
                <div class="rrj-section-desc">Salary range with currency &amp; mode</div>
              </div>
            </div>
            <div class="row g-2 rrj-salary-row">
              <div class="col-3">
                <label class="rrj-mini-label">Currency</label>
                <select class="form-select form-select-solid" name="min_salary_currency" id="min_salary_currency">
                  <option value="">Any</option>
                  <option value="0" @if(0==@$min_salary_currency) selected @endif>INR (₹)</option>
                  <option value="1" @if(1==@$min_salary_currency) selected @endif>EUR</option>
                  <option value="2" @if(2==@$min_salary_currency) selected @endif>JPY</option>
                  <option value="3" @if(3==@$min_salary_currency) selected @endif>GBP</option>
                  <option value="4" @if(4==@$min_salary_currency) selected @endif>USD</option>
                  <option value="5" @if(5==@$min_salary_currency) selected @endif>AED</option>
                </select>
              </div>
              <div class="col-3">
                <label class="rrj-mini-label">Mode</label>
                <select class="form-select form-select-solid" name="sal_mode" id="sal_mode">
                  <option value="">Any</option>
                  <option value="0" @if(0==@$min_sal_mode) selected @endif>Annual</option>
                  <option value="1" @if(1==@$min_sal_mode) selected @endif>Monthly</option>
                  <option value="2" @if(2==@$min_sal_mode) selected @endif>Hourly</option>
                </select>
              </div>
              <div class="col-3">
                <label class="rrj-mini-label">Min Salary</label>
                <input type="number" class="form-control form-control-solid" name="min_sal" id="min_sal" placeholder="Any" value="{{ (@$min_salary !== '' && @$min_salary !== null) ? @$min_salary : 0 }}">
              </div>
              <div class="col-3">
                <label class="rrj-mini-label">Max Salary</label>
                <input type="number" class="form-control form-control-solid" name="max_sal" id="max_sal" placeholder="Any" value="{{ @$max_salary }}">
              </div>
            </div>
          </div>

          {{-- Work Mode (NEW) --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Work Mode</div>
                <div class="rrj-section-desc">Select one or more — leave blank for any</div>
              </div>
            </div>
            <div class="rrj-pill-row" id="rrj_work_mode_row">
              <div>
                <input type="checkbox" class="btn-check rrj_work_mode" value="no" id="rrj_work_mode_onsite">
                <label class="rrj-pill" for="rrj_work_mode_onsite">Onsite</label>
              </div>
              <div>
                {{-- job documents store hybrid as BOTH 'other' and 'any'
                     (see work_type handling in bench-recommend-req-ajax) —
                     the comma-separated value covers both spellings --}}
                <input type="checkbox" class="btn-check rrj_work_mode" value="other,any" id="rrj_work_mode_hybrid">
                <label class="rrj-pill" for="rrj_work_mode_hybrid">Hybrid</label>
              </div>
              <div>
                <input type="checkbox" class="btn-check rrj_work_mode" value="yes" id="rrj_work_mode_remote">
                <label class="rrj-pill" for="rrj_work_mode_remote">Remote</label>
              </div>
            </div>
          </div>

          {{-- Hiring Type --}}
          <div class="rrj-section">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-9 0h12A1.5 1.5 0 0 1 19.5 7.5V18a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V7.5A1.5 1.5 0 0 1 6 6Zm-1.5 5.25h15"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Hiring Type</div>
                <div class="rrj-section-desc">Full-time or contract roles</div>
              </div>
            </div>
            <div class="rrj-pill-row">
              <div><input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="" id="app_int_outsource1" checked><label class="rrj-pill" for="app_int_outsource1">All</label></div>
              <div><input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="0" id="app_int_outsource2"><label class="rrj-pill" for="app_int_outsource2">Fulltime</label></div>
              <div><input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="1" id="app_int_outsource3"><label class="rrj-pill" for="app_int_outsource3">Contract</label></div>
            </div>
          </div>

          {{-- Activity --}}
          <div class="rrj-section rrj-section-last">
            <div class="rrj-section-head">
              <span class="rrj-icon rrj-icon-sky">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg>
              </span>
              <div>
                <div class="rrj-section-title">Activity</div>
                <div class="rrj-section-desc">How recently the job was posted/updated</div>
              </div>
            </div>
            <div class="rrj-pill-row">
              <div><input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="1" id="app_int_activity1" checked><label class="rrj-pill" for="app_int_activity1">All</label></div>
              <div><input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="2" id="app_int_activity2"><label class="rrj-pill" for="app_int_activity2">30 Days</label></div>
              <div><input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="3" id="app_int_activity3"><label class="rrj-pill" for="app_int_activity3">60 Days</label></div>
              <div><input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="4" id="app_int_activity4"><label class="rrj-pill" for="app_int_activity4">90 Days</label></div>
              <div><input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="5" id="app_int_activity5"><label class="rrj-pill" for="app_int_activity5">180 Days</label></div>
              <div><input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="6" id="app_int_activity6"><label class="rrj-pill" for="app_int_activity6">1 Year</label></div>
            </div>
          </div>

        </form>
      </div>

      <div class="rrj-footer">
        <div class="rrj-footer-count">
          <span class="rrj-footer-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9m0 10h9M4 7h1m0 0v10m0 0h1M4 12h6"/></svg>
          </span>
          <div>
            <div class="rrj-footer-number" id="rrj_footer_number">0</div>
            <div class="rrj-footer-label">Jobs Match</div>
          </div>
        </div>
        <div class="rrj-footer-actions">
          <button type="button" class="btn btn-sm btn-light" id="rrj_reset_btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
            Reset
          </button>
          <button type="button" class="btn btn-sm btn-primary" id="rrj_apply_btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
            Apply Filters
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ SECTION 4 · RESULTS ══ --}}
  {{-- Deliberately NOT using the bare "loadingMessage" class here — the
       shared layout defines a global .loadingMessage{ background:rgba(0,0,0,.8);
       z-index:9999 } (a dark full-page flash), which is exactly the "black
       loading screen" bug. This element only carries our own scoped class. --}}
  <div class="loadingMessagebenchCandidateRecommendedReqAjax">
    <div class="spinner-border text-primary" role="status"></div>
  </div>

  <div class="rrj-results-wrap">
    <div class="agencies-list" id="benchCandidateRecommendedReqAjax"></div>
    <div id="paginationbenchCandidateRecommendedReq"></div>
  </div>

  </div>{{-- /bxr-page --}}
  </div>
 </div>
</div>
@else
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <table id="no-access" class="table align-middle table-row-dashed fs-6 gy-5">
        <thead><tr class="fw-bolder text-muted"><th><div align="center">ACCESS DENIED CONTACT ADMINISTRATOR</div></th></tr></thead>
      </table>
    </div>
  </div>
</div>
@endif

<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
     data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'95%', 'sm':'90%', 'lg':'80%'}"
     data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Requisition</h3>
      </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close">
          <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
              </g>
            </svg>
          </span>
        </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>

<div id="kt_activitiesTermsFindjob_toggle" style="display:none;"></div>
<div id="kt_activitiesTermsFindjob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
     data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'95%', 'sm':'90%', 'lg':'800px'}"
     data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesTermsFindjob_toggle" data-kt-drawer-close="#kt_activitiesTermsFindJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Terms &amp; Conditions</h3>
      </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesTermsFindJob_close">
          <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
                <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
              </g>
            </svg>
          </span>
        </button>
      </div>
    </div>
    <div class="card-body position-relative px-5 py-5" id="kt_activitiesTermsFindJob_body"></div>
  </div>
</div>

@endsection
@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<link rel="stylesheet" href="{{ asset('assets/node_modules/sweetalert/sweetalert.css') }}{{ TM_VERSION }}">
<script src="{{ asset('assets/node_modules/sweetalert/sweetalert.min.js') }}{{ TM_VERSION }}"></script>

{{-- ── Skills Tagify — exact template/behavior ported from
     apptracker/similar_candidates.blade.php (star toggles mandatory,
     click on the keyword text also toggles it, live /skill-suggestions
     autocomplete). ── --}}
<script>
const rrjSkillInputElm = document.querySelector('#kt_tagify_users');
const rrjTagifySkills = new Tagify(rrjSkillInputElm, {
    templates: {
        tag: function(tagData) {
            tagData = tagData || {};
            const value = tagData.value || '';
            const isChecked = tagData.checked ? 'checked' : '';
            const mandatoryClass = tagData.checked ? 'rrj-mandatory-tag' : '';
            const starGlyph = tagData.checked ? '★' : '☆';
            const chipBg    = tagData.checked ? '#dbeafe' : '#eef2ff';
            const chipBdr   = tagData.checked ? '#bfdbfe' : '#e0e7ff';
            const starColor = tagData.checked ? '#eab308' : '#94a3b8';
            const textColor = tagData.checked ? '#1d4ed8' : '#3730a3';
            return `<tag title="${value}" contenteditable='false' spellcheck="false"
                class="tagify__tag ${mandatoryClass} ${tagData.class ? tagData.class : ""}"
                ${this.getAttributes(tagData)}
                style="display:inline-flex;align-items:center;gap:6px;background:${chipBg};border:1px solid ${chipBdr};border-radius:8px;padding:5px 7px 5px 10px;margin:4px 6px 4px 0;vertical-align:middle;">
                <span class="star ${isChecked}" style="cursor:pointer;font-size:15px;line-height:1;color:${starColor};flex-shrink:0;margin:1px 3px 0px 3px;">${starGlyph}</span>
                <span class="tagify__tag-text" style="cursor:pointer;font-size:11.5px;font-weight:500;color:${textColor};line-height:1;white-space:nowrap;padding:0px 0px 0px 6px;">${value}</span>
                <x title='Remove' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"
                    style="display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:50%;background:rgba(37,99,235,.14);color:#2563eb;flex-shrink:0;font-size:12px;font-weight:700;line-height:1;cursor:pointer;padding:0;">&times;</x>
            </tag>`;
        }
    },
    transformTag: function(tagData) { tagData.checked = tagData.checked || false; return tagData; }
});
document.addEventListener('click', function(e) {
    const toggleTarget = e.target.closest('.star, .tagify__tag-text');
    if (!toggleTarget || !rrjSkillInputElm.parentElement.contains(toggleTarget)) return;
    const tagElm = toggleTarget.closest('.tagify__tag');
    if (!tagElm) return;
    const starEl = tagElm.querySelector('.star');
    const textEl = tagElm.querySelector('.tagify__tag-text');
    if (!starEl || !textEl) return;
    const value = textEl.textContent;
    starEl.classList.toggle('checked');
    const nowChecked = starEl.classList.contains('checked');
    starEl.textContent = nowChecked ? '★' : '☆';
    starEl.style.color = nowChecked ? '#eab308' : '#94a3b8';
    tagElm.style.background = nowChecked ? '#dbeafe' : '#eef2ff';
    tagElm.style.borderColor = nowChecked ? '#bfdbfe' : '#e0e7ff';
    textEl.style.color = nowChecked ? '#1d4ed8' : '#3730a3';
    const tagData = rrjTagifySkills.value.find(tag => tag.value === value);
    if (tagData) {
        setTimeout(() => { tagData.checked = nowChecked; rrjTagifySkills.updateValueByDOMTags(); rrjUpdateActiveFiltersUI(); }, 500);
    }
});
function rrjDebounce(func, delay) { let t; return function(...args) { clearTimeout(t); t = setTimeout(() => func.apply(this, args), delay); }; }
const rrjFetchSkillSuggestions = rrjDebounce(function(query) {
    $.ajax({
        url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
        method: 'GET',
        success: function(data) { if (Array.isArray(data)) { rrjTagifySkills.settings.whitelist = data; rrjTagifySkills.dropdown.show.call(rrjTagifySkills); } },
        error: function(err) { console.error("Error fetching Skill:", err); }
    });
}, 200);
rrjTagifySkills.on('input', function(e) { var q = e.detail.value.trim(); if (q.length < 1) return; rrjFetchSkillSuggestions(q); });
rrjTagifySkills.on('add', function(){ rrjUpdateActiveFiltersUI(); });
rrjTagifySkills.on('remove', function(){ rrjUpdateActiveFiltersUI(); });
</script>

{{-- ── Location Tagify — same chip template + live /city-job-suggestions
     autocomplete + "Popular Locations" quick-pick panel as Similar
     Candidates' Preferred Location section. ── --}}
<script>
var rrjInputLoc = document.querySelector("#kt_tagify_location");
var rrjTagifyLocation = new Tagify(rrjInputLoc, {
    whitelist: [], maxTags: 10,
    templates: {
        tag: function(tagData) {
            const value = tagData.value || '';
            return `<tag title="${value}" contenteditable='false' spellcheck="false"
                class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}
                style="display:inline-flex;align-items:center;gap:6px;background:#eef2ff;border:1px solid #e0e7ff;border-radius:8px;padding:5px 7px 5px 10px;margin:4px 6px 4px 0;vertical-align:middle;">
                <span class="tagify__tag-text" style="font-size:11.5px;font-weight:500;color:#3730a3;line-height:1;white-space:nowrap;">${value}</span>
                <x title='Remove' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"
                    style="display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:50%;background:rgba(37,99,235,.14);color:#2563eb;flex-shrink:0;font-size:12px;font-weight:700;line-height:1;cursor:pointer;padding:0;">&times;</x>
            </tag>`;
        }
    },
    dropdown: { maxItems: 20, classname: "", enabled: 0, closeOnSelect: true, appendTarget: document.body }
});
function rrjCloseLocPanel(){ $('#rrj_loc_panel').addClass('d-none'); $('#rrj_loc_chevron').removeClass('open'); }
function rrjOpenLocPanel(){ $('#rrj_loc_panel').removeClass('d-none'); $('#rrj_loc_chevron').addClass('open'); }
$(document).on('click', '#rrj_loc_chevron', function(e){ e.stopPropagation(); if ($('#rrj_loc_panel').hasClass('d-none')) rrjOpenLocPanel(); else rrjCloseLocPanel(); });
$(document).on('click', '.rrj-loc-chip', function() {
    rrjTagifyLocation.addTags([$(this).data('city').toString()]);
    rrjCloseLocPanel();
});
rrjTagifyLocation.on('dropdown:select', function(e) { rrjTagifyLocation.addTags([e.detail.data.value]); rrjCloseLocPanel(); });
rrjTagifyLocation.on('add', function(e) { rrjUpdateActiveFiltersUI(); rrjCloseLocPanel(); });
rrjTagifyLocation.on('remove', function(e) { rrjUpdateActiveFiltersUI(); });
const rrjFetchLocSuggestions = rrjDebounce(function(query) {
    $.ajax({
        url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(query),
        method: 'GET',
        success: function(data) {
            if (Array.isArray(data)) { rrjTagifyLocation.settings.whitelist = data; rrjCloseLocPanel(); rrjTagifyLocation.dropdown.show.call(rrjTagifyLocation, query); }
        },
        error: function(err) { console.error("Error fetching cities:", err); }
    });
}, 200);
rrjTagifyLocation.on('input', function(e) { var q = e.detail.value.trim(); if (q.length < 2) return; rrjCloseLocPanel(); rrjFetchLocSuggestions(q); });
</script>

{{-- ── Drawer layout enforcement — fixes the footer "jumping" bug. Plain
     flexbox (flex:1 1 auto + overflow-y:auto on .rrj-body) should be enough
     for a sticky footer, but KTDrawer re-applies its own inline sizing on
     certain events, and Tagify chips/native <select> focus can grow the
     body's content height inside that same tick — the combination made the
     footer visibly jump upward. Forcing an explicit, JS-computed height on
     the body (window height minus the real header/footer height) and
     position:absolute on the footer pins it in place regardless of what
     KTDrawer or content growth does. Same technique already proven on
     Similar Candidates' filter drawer. ── --}}
<script>
function rrjEnforceDrawerLayout(){
    var drawerEl=document.getElementById('rrj_filter_drawer');
    var cardEl=document.getElementById('rrj_card');
    var bodyEl=cardEl?cardEl.querySelector('.rrj-body'):null;
    var headerEl=cardEl?cardEl.querySelector('.rrj-header'):null;
    var footerEl=cardEl?cardEl.querySelector('.rrj-footer'):null;
    if(drawerEl){
        drawerEl.style.setProperty('overflow','hidden','important');
        drawerEl.style.setProperty('position','fixed','important');
        drawerEl.style.setProperty('top','0','important');
        drawerEl.style.setProperty('bottom','0','important');
        drawerEl.style.setProperty('height','100vh','important');
    }
    if(cardEl){
        cardEl.style.setProperty('display','block','important');
        cardEl.style.setProperty('position','relative','important');
        cardEl.style.setProperty('overflow','hidden','important');
        cardEl.style.setProperty('height','100vh','important');
        cardEl.style.setProperty('max-height','100vh','important');
    }
    if(bodyEl){
        var headerH=headerEl?headerEl.offsetHeight:0, footerH=footerEl?footerEl.offsetHeight:0;
        var available=Math.max(window.innerHeight-headerH-footerH,100);
        bodyEl.style.setProperty('position','absolute','important');
        bodyEl.style.setProperty('left','0','important');
        bodyEl.style.setProperty('right','0','important');
        bodyEl.style.setProperty('top',headerH+'px','important');
        bodyEl.style.setProperty('height',available+'px','important');
        bodyEl.style.setProperty('max-height',available+'px','important');
        bodyEl.style.setProperty('overflow-y','auto','important');
        bodyEl.style.setProperty('overflow-x','hidden','important');
    }
    if(footerEl){
        footerEl.style.setProperty('position','absolute','important');
        footerEl.style.setProperty('left','0','important');
        footerEl.style.setProperty('right','0','important');
        footerEl.style.setProperty('bottom','0','important');
    }
}
$(document).ready(rrjEnforceDrawerLayout);
$(window).on('resize', rrjEnforceDrawerLayout);
$(document).on('click', '#rrj_filter_toggle, #rrj_filter_close', function(){ setTimeout(rrjEnforceDrawerLayout, 0); setTimeout(rrjEnforceDrawerLayout, 60); });
/* Re-run whenever a field inside the drawer could have changed the body's
   content height (Tagify add/remove, select change, focus on any input) —
   this is the actual fix for "clicking a field moves the footer". */
$(document).on('click change focus', '#rrj_card .rrj-body, #rrj_card .rrj-body *', function(){ setTimeout(rrjEnforceDrawerLayout, 0); });
</script>

{{-- ── Sort panel + filter drawer chrome (open/close/reset/apply/active
     filters chip bar) ── --}}
<script>
function rrjRenderSortPanel(){
    var $panel=$('#rrj_sort_panel').empty();
    var current=$('#hiringtype').val();
    $('#hiringtype option').each(function(){
        var $opt=$(this), val=$opt.attr('value'), label=$opt.text();
        $('<div class="bxr-sort-item"></div>').text(label)
            .toggleClass('active', val===current)
            .on('click', function(){
                $('#hiringtype').val(val);
                $('#rrj_sort_label').text(label);
                rrjCloseSortPanel();
                fetchBenchRecommendedReqCandidateList();
            }).appendTo($panel);
    });
}
function rrjOpenSortPanel(){ rrjRenderSortPanel(); $('#rrj_sort_panel').removeClass('d-none'); $('#rrj_sort_btn').addClass('open'); }
function rrjCloseSortPanel(){ $('#rrj_sort_panel').addClass('d-none'); $('#rrj_sort_btn').removeClass('open'); }
$(document).on('click','#rrj_sort_btn',function(e){ e.stopPropagation(); if ($('#rrj_sort_panel').hasClass('d-none')) rrjOpenSortPanel(); else rrjCloseSortPanel(); });
$(document).on('click',function(e){ if(!$(e.target).closest('#rrj_sort_wrap').length) rrjCloseSortPanel(); });
$(document).on('keydown', '#kt_tagify_2', function(e){ if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); fetchBenchRecommendedReqCandidateList(); } });

function rrjPillLabel($checkedInput){ return $.trim($checkedInput.closest('div').find('label').text()); }

function rrjBuildFilterItems(){
    var items = [];
    rrjTagifySkills.value.forEach(function(t){
        items.push({cat: t.checked ? 'mandatory' : 'skill', label: t.value, mandatory: !!t.checked, clear: function(){ rrjTagifySkills.removeTags(t.value); }});
    });
    rrjTagifyLocation.value.forEach(function(t){
        items.push({cat:'location', label:t.value, clear:function(){ rrjTagifyLocation.removeTags(t.value); }});
    });
    var minSal=$('#min_sal').val(), maxSal=$('#max_sal').val();
    /* min salary 0 is the neutral default ("no lower bound") — it must not
       render as an active-filter chip or count toward the filter badge. */
    if ((parseFloat(minSal)||0) > 0 || maxSal) items.push({cat:'salary', label:(minSal||'0')+'-'+(maxSal||'Any'), clear:function(){ $('#min_sal').val(0); $('#max_sal').val(''); }});
    var minExp=$('#min_exp').val(), maxExp=$('#max_exp').val();
    if (minExp !== '' || maxExp !== '') items.push({cat:'exp', label:(minExp||'0')+'-'+(maxExp||'Any')+' yrs', clear:function(){ $('#min_exp,#max_exp').val(''); }});
    $('.rrj_work_mode:checked').each(function(){
        var $lbl=$(this).next('label');
        items.push({cat:'workmode', label:$lbl.text(), clear:function(){ $(this).prop('checked', false); }.bind(this)});
    });
    var statusEl=$('[name="app_int_outsource"]:checked');
    if (statusEl.val() && statusEl.val()!=='') items.push({cat:'hiring', label:rrjPillLabel(statusEl), clear:function(){ $('#app_int_outsource1').prop('checked',true); }});
    var activityEl=$('[name="app_int_activity"]:checked');
    if (activityEl.val() && activityEl.val()!=='1') items.push({cat:'activity', label:rrjPillLabel(activityEl), clear:function(){ $('#app_int_activity1').prop('checked',true); }});
    return items;
}
function rrjRenderChipsInto($container, items, onRemove){
    $container.empty();
    items.forEach(function(it){
        var $chip=$('<span class="rrj-chip"></span>').toggleClass('mandatory', !!it.mandatory);
        $chip.append($('<span></span>').text((it.mandatory?'★ ':'')+it.label));
        $chip.append($('<span class="rrj-chip-remove">&times;</span>').on('click', function(){ it.clear(); onRemove(); }));
        $container.append($chip);
    });
}
function rrjUpdateActiveFiltersUI(){
    var items = rrjBuildFilterItems();
    rrjRenderChipsInto($('#rrj_active_chips'), items, rrjUpdateActiveFiltersUI);
    var n = items.length;
    $('#rrj_active_bar').toggleClass('d-none', n===0);
    $('#rrj_active_count').text('('+n+')');
    $('#rrj_header_count').text(n);
    $('#rrj_count_badge').toggleClass('d-none', n===0).text(n);
    $('#rrj_filter_count_text').text(n>0?' ('+n+')':'');
}
function rrjRenderAppliedFiltersCard(){
    var items = rrjBuildFilterItems();
    rrjRenderChipsInto($('#rrj_applied_chips'), items, function(){ fetchBenchRecommendedReqCandidateList(); rrjUpdateActiveFiltersUI(); rrjRenderAppliedFiltersCard(); });
    $('#rrj_applied_card').toggleClass('d-none', items.length===0);
    $('#rrj_applied_count').text('('+items.length+')');
}
$(document).on('click','#rrj_applied_clear_all',function(){ $('#rrj_clear_all').trigger('click'); fetchBenchRecommendedReqCandidateList(); rrjRenderAppliedFiltersCard(); });
$(document).on('click','#rrj_clear_all, #rrj_reset_btn',function(){
    rrjTagifySkills.removeAllTags();
    rrjTagifyLocation.removeAllTags();
    $('#min_exp,#max_exp,#min_salary_currency,#sal_mode,#max_sal').val('');
    $('#min_sal').val(0); /* min salary resets to 0, never blank */
    $('.rrj_work_mode').prop('checked', false);
    $('#app_int_outsource1,#app_int_activity1').prop('checked', true);
    rrjUpdateActiveFiltersUI();
});
$(document).on('click','#rrj_apply_btn',function(){
    fetchBenchRecommendedReqCandidateList();
    rrjRenderAppliedFiltersCard();
    $('#rrj_filter_close').trigger('click');
});
$(document).on('change', '#min_sal, #max_sal, #min_exp, #max_exp, .rrj_work_mode, [name="app_int_outsource"], [name="app_int_activity"]', rrjUpdateActiveFiltersUI);
$(document).ready(function(){ setTimeout(rrjUpdateActiveFiltersUI, 600); setTimeout(rrjRenderAppliedFiltersCard, 700); });
</script>

{{-- ── Preserved job/client/HM/team drawer handlers + bench-request send —
     UNCHANGED business logic from before this redesign ── --}}
<script>
$(document).ready(function() {
    $(document).on("click",".checkrequestsend", function(){
        swal("Bench Request Alert", 'Please move the candidate to Bench status before sending bench request', "warning");
    });
    $(document).on("click",".request-send", function(){
        var ele = $(this);
        var jobId  = $(this).attr('data-jobId');
        var conId  = $(this).attr('data-conId');
        swal({
            title: "Are you sure?", text: "Do you want to Send Bench Request on this Job?", type: "warning",
            showCancelButton: true, confirmButtonColor: '#0066ff', confirmButtonText: 'Yes, Send Bench Request!',
            cancelButtonText: "No, cancel it!", closeOnConfirm: false, closeOnCancel: false
        },
        function(isConfirm){
            if (isConfirm){
                var url = "{{ route('service_company.jobs.recommandedrequestcondidate',':id') }}";
                url = url.replace(':id', jobId)+"?conId="+conId;
                $.ajax({
                    type: 'GET', url: url,
                    success: function (response) {
                        if(response.status=='success'){
                            swal("Request Sent!", "Bench request successfully sent for this job requirement!", "success");
                            var jobId  = ele.attr('data-jobId');
                            var conId  = ele.attr('data-conId');
                            var matlaccess = 1;
                            var url = "{{ route('service_company.jobs.mailrecommandedrequestcondidate',':id') }}";
                            url = url.replace(':id', jobId)+"?conId="+conId+"?matlaccess="+matlaccess;
                            fetchBenchRecommendedReqCandidateList();
                            $.ajax({ type: 'GET', url: url, success: function (response) {} });
                        }else{
                            fetchBenchRecommendedReqCandidateList();
                            swal("Request Failed", response.message, "warning").then(function(){ fetchBenchRecommendedReqCandidateList(); });
                        }
                    }
                });
            } else { swal("Cancelled", "", "error"); }
        });
    });
});
</script>
<script>
function updateJobStatus(id, statusName)
{
    var url = "{{ route('service_company.jobs.updatestatus',':id') }}";
    url = url.replace(':id', id);
    var token = "{{ csrf_token() }}";
    $.easyAjax({ type: 'POST', url: url, data: {'_token': token, '_method': 'POST', 'id':id, 'statusName':statusName}, success: function (response){ fetchBenchRecommendedReqCandidateList(); } });
}
$(document).on('click', '.show-detail', function () {
    $('#kt_activitiesJob_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.viewmore',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({ type: 'GET', url: url, beforeSend: function() { $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); }, success: function (response) { $('#kt_activitiesJob_body').html(response.view); } });
});
$(document).on('click', '.status_stage', function () {
    $('#status_body_content').html('');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.editJobStatus',':id') }}";
    url = url.replace(':id', id);
    $.ajax({ type: 'GET', url: url, success: function (response) { if (response.status == "success") { $('#status_body_content').html(response.view); } } });
});
$(document).on('click', '.client-show-detail', function () {
    $('#kt_activitiesClientjob_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.clientviewmore',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({ type: 'GET', url: url, beforeSend: function() { $('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); }, success: function (response) { $('#kt_activitiesclientjob_body').html(response.view); } });
});
$(document).on('click', '.spartner-listing', function () {
    $('#kt_activitiesStaffing_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.spartnerlisting',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({ type: 'GET', url: url, beforeSend: function() { $('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); }, success: function (response) { $('#kt_activities_body').html(response.view); } });
});
</script>
<script>
$(document).ready(function(){
    $(document).on('click', '.hmlisting', function(){
        $('#kt_activitiesRecruiter_toggle').trigger('click');
        var id = $(this).data('row-id');
        var token = "{{ csrf_token() }}";
        var url = "{{ route('service_company.jobs.hmlisting',':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({ type: 'GET', url: url, data: {'_token': token, 'id' : id}, beforeSend: function() { $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); }, success: function (response) { $('#kt_activitiesRecruiter_body').html(response.view); } });
    });
});
</script>
<script>
$(document).on('click', '.client-hm-listing', function () {
    $('#kt_activitiesCHM_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.clienthmlisting',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({ type: 'GET', url: url, beforeSend: function() { $('#kt_activitiesCHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); }, success: function (response) { $('#kt_activitiesCHM_body').html(response.view); } });
});
$(document).on('click', '.team-listing', function () {
    $('#kt_activitiesTEAM_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.currentteamlisting',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({ type: 'GET', url: url, beforeSend: function() { $('#kt_activitiesTEAM_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); }, success: function (response) { $('#kt_activitiesTEAM_body').html(response.view); } });
});
</script>
<script>
$(document).on('click', '.hm-listing', function () {
    $('#kt_activitiesHM_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.jobs.hmlisting',':id') }}";
    url = url.replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({ type: 'GET', url: url, beforeSend: function() { $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); }, success: function (response) { $('#kt_activitiesHM_body').html(response.view); } });
});
</script>
<script>
$(document).on('click', '.client-terms-listing-find-job', function (e){
    e.preventDefault();
    var $btn = $(this);
    var id = $btn.data('row-id') || $btn.attr('data-row-id');
    var recommendation_relation_id = $btn.data('row-recommendation_relation_id') || $btn.attr('data-row-recommendation_relation_id') || '';
    if (!id) {
        id = $btn.closest('[data-row-id]').attr('data-row-id');
    }

    var drawerElement = document.querySelector('#kt_activitiesTermsFindjob');
    if (drawerElement && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerElement) || new KTDrawer(drawerElement);
        if (drawer) { drawer.show(); } else { $('#kt_activitiesTermsFindjob_toggle').trigger('click'); }
    } else {
        $('#kt_activitiesTermsFindjob_toggle').trigger('click');
    }

    if (!id) {
        $('#kt_activitiesTermsFindJob_body').html('<div class="alert alert-danger m-4">Unable to find job requirement ID.</div>');
        return;
    }
    var url = "{{ route('service_company.jobs.termshmlisting',':id') }}";
    url = url.replace(':id', id) + "?id=" + id;
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            recommendation_relation_id: recommendation_relation_id
        },
        beforeSend: function() {
            $('#kt_activitiesTermsFindJob_body').html('<div class="text-center py-10"><img src="'+baseurl+'" height="35px"></div>');
        },
        success: function (response) {
            if (response && (response.terms || response.view)) {
                $('#kt_activitiesTermsFindJob_body').html(response.terms || response.view);
            } else if (response && response.message) {
                $('#kt_activitiesTermsFindJob_body').html('<div class="alert alert-danger m-4">' + response.message + '</div>');
            } else {
                $('#kt_activitiesTermsFindJob_body').html('<div class="alert alert-danger m-4">No terms & conditions found.</div>');
            }
        },
        error: function(xhr) {
            var errorMsg = 'Failed to load terms & conditions (' + xhr.status + ').';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg += ' ' + xhr.responseJSON.message;
            }
            $('#kt_activitiesTermsFindJob_body').html('<div class="alert alert-danger m-4">' + errorMsg + '</div>');
        }
    });
});
</script>

{{-- ── Core data fetch — SAME route/params/response-handling as before this
     redesign (business logic untouched), plus error handling and work_mode
     (additive new param, empty by default = no filter change for existing
     users). #kt_tagify_users is now pre-filled with the candidate's raw
     skills only (see the input above), so every load — including the very
     first one — already applies the same skills filter
     benchjobAndRecomCount() uses for the card/tab counters. That keeps the
     card number and the list's response.total in agreement instead of the
     card being skill-filtered while the list's first paint wasn't. ── --}}
<script>
function fetchBenchRecommendedReqCandidateList(page = 1, search = '') {
    $('.loadingMessagebenchCandidateRecommendedReqAjax').css({ display: 'flex' });
    var perPage = localStorage.getItem('candidatePerPage') || 10;
    var workModeVal = $('.rrj_work_mode:checked').map(function(){ return $(this).val(); }).get().join(',');
    /* return the jqXHR so pagination's .then()/.catch() work — without it
       they were called on undefined and threw a TypeError on every click */
    return $.ajax({
        url: `{!! route('service_company.recommend.all_data_ajax') !!}?page=${page}`,
        type: 'GET',
        data: {
            search: $('#inputSearch').val(),
            condidate_id: "{{$segment}}",
            custom_search: $('#kt_tagify_2').val(),
            custom_status: 'active',
            custom_hiringtype: $('#hiringtype').val(),
            cus_search: $('#kt_tagify_users').val(),
            min_exp: $('#min_exp').val(),
            max_exp: $('#max_exp').val(),
            kt_tagify_location: $('#kt_tagify_location').val(),
            min_sal: $('#min_sal').val(),
            max_sal: $('#max_sal').val(),
            min_salary_currency: $('#min_salary_currency').val(),
            sal_mode: $('#sal_mode').val(),
            work_mode: workModeVal,
            app_int_activity: $('[name="app_int_activity"]:checked').val(),
            app_int_outsource: $('[name="app_int_outsource"]:checked').val(),
            per_page: perPage,
            size: perPage
        },
        success: function(response) {
            $('.loadingMessagebenchCandidateRecommendedReqAjax').css({ display: 'none' });
            if (!response || typeof response.html === 'undefined') { console.error('all_data_ajax: unexpected response', response); return; }
            $('#benchCandidateRecommendedReqAjax').html(response.html);
            $('#recommend_req_id').html(response.total);
            $('#rrj_footer_number').text(response.total);
            $('#paginationbenchCandidateRecommendedReq').html(response.pagination);
        },
        error: function(xhr) {
            $('.loadingMessagebenchCandidateRecommendedReqAjax').css({ display: 'none' });
            console.error('all_data_ajax failed:', xhr.status, xhr.responseText);
        }
    });
}
function filtersdataget(){ fetchBenchRecommendedReqCandidateList(); }
$(document).ready(function() {
    fetchBenchRecommendedReqCandidateList();
    /* The shared pagination partial renders .pagi-page/.pagi-arrow buttons —
       the old handler listened for Bootstrap's .page-link, which the partial
       never outputs, so pagination clicks did nothing at all. A boolean
       guard prevents overlapping requests from rapid clicks. */
    var rrjPaginationLoading = false;
    $(document).on('click', '#paginationbenchCandidateRecommendedReq .pagi-page, #paginationbenchCandidateRecommendedReq .pagi-arrow', function(e) {
        e.preventDefault();
        var $btn = $(this);
        if (rrjPaginationLoading || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled')) return;
        var page = $btn.data('page');
        if (!page) return;
        rrjPaginationLoading = true;
        fetchBenchRecommendedReqCandidateList(page)
            .then(function(){ rrjPaginationLoading = false; })
            .catch(function(){ rrjPaginationLoading = false; });
    });
    $(document).on('change', '#paginationbenchCandidateRecommendedReq .pagi-perpage', function(e) {
        var selectedPerPage = $(this).val();
        if (selectedPerPage) {
            localStorage.setItem('candidatePerPage', selectedPerPage);
            fetchBenchRecommendedReqCandidateList(1);
        }
    });
});
</script>
@endpush
