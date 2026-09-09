@extends('layouts.service_company_layout')
@push('head-script')
{{-- Poppins is already loaded once, globally, by the layout — no second font fetch here. --}}
<style>
/* ═══════════════════════════════════════════════
   BENCH EXPLORE — EXACT REFERENCE MATCH (bxr-)
═══════════════════════════════════════════════ */
#kt_content{ margin-top:-70px !important; }

.bxr-page{
    font-family:'Poppins',sans-serif;
    background:#F2F4F8;
    min-height:100vh;
    padding:18px 0 40px;
}
.bxr-page *{ font-family:'Poppins',sans-serif; box-sizing:border-box; }

/* ══ 1. PROFILE HERO — segmented stat card: identity | Experience/Bill Rate
   | Bench Status/On Bench | 4 icon KPIs, separated by vertical dividers.
   Labels indigo, values dark bold, circular avatar with online dot. ══ */
.bxr-hero{
    background:#fff;
    border:1px solid #EEF0F4; border-radius:14px;
    padding:14px 20px;
    display:flex; align-items:center;
    flex-wrap:wrap; row-gap:14px;
    margin-bottom:16px;
}
.bxr-hero-id{ display:flex; align-items:center; gap:14px; min-width:220px; padding-right:8px; }
.bxr-av-wrap{ position:relative; flex-shrink:0; }
.bxr-avatar{ width:56px; height:56px; border-radius:50%; object-fit:cover; }
.bxr-avatar-init{
    width:56px; height:56px; border-radius:50%;
    background:linear-gradient(135deg,#6D28D9,#4F46E5);
    display:flex; align-items:center; justify-content:center;
    font-size:22px; font-weight:700; color:#fff;
}
.bxr-online-dot{
    position:absolute; bottom:2px; right:2px;
    width:12px; height:12px;
    background:#22C55E; border:2px solid #fff; border-radius:50%;
}
.bxr-name{ font-size:14px; font-weight:700; color:#181C32; line-height:1.25; }
.bxr-role{ font-size:12px; font-weight:600; color:#4F46E5; margin:2px 0 4px; }
.bxr-loc{ font-size:11px; font-weight:500; color:#7E8299; display:flex; align-items:center; gap:4px; }
.bxr-loc i{ color:#4F46E5; font-size:12px; }
.bxr-hero-sep{ width:1px; align-self:stretch; background:#EEF0F4; margin:0 18px; }
.bxr-facts{ display:flex; flex-direction:column; gap:9px; justify-content:center; min-width:96px; }
.bxr-fact-label{ font-size:11px; font-weight:600; color:#4F46E5; display:block; margin-bottom:2px; }
.bxr-fact-value{ font-size:13px; font-weight:700; color:#181C32; line-height:1.2; }
/* Stage badge — colors set per-candidate via inline CSS custom properties:
   Bench=blue, Placed=green, Hold=orange (see $bxrStage* in the markup). */
.bxr-bench-tag{
    display:inline-flex; align-items:center; gap:5px;
    font-size:11px; font-weight:600;
    padding:2px 9px; border-radius:20px;
    background:var(--bxr-stage-bg,#F0FDF4); color:var(--bxr-stage-color,#16A34A);
    border:1.5px solid var(--bxr-stage-border,#86EFAC);
    width:max-content;
}
.bxr-bench-dot{ width:6px; height:6px; border-radius:50%; background:var(--bxr-stage-color,#16A34A); flex-shrink:0; }
/* Icon KPI columns: label / tinted circular icon / number, top to bottom */
.bxr-kpis{ display:flex; align-items:stretch; flex:1; justify-content:space-evenly; min-width:300px; }
.bxr-kpi{
    display:flex; flex-direction:column; align-items:center; gap:6px;
    padding:0 16px; position:relative; justify-content:center;
}
.bxr-kpi+.bxr-kpi::before{
    content:''; position:absolute; left:0; top:8%; height:84%;
    width:1px; background:#EEF0F4;
}
.bxr-kpi-lbl{ font-size:11px; font-weight:600; color:#4F46E5; white-space:nowrap; }
.bxr-kpi-icon{
    width:34px; height:34px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:15px; flex-shrink:0;
}
.bxr-kpi-icon.vi{ background:#EFF6FF; color:#2563EB; }
.bxr-kpi-icon.dl{ background:#F0FDF4; color:#16A34A; }
.bxr-kpi-icon.aj{ background:#FFF7ED; color:#EA580C; }
.bxr-kpi-icon.iv{ background:#EEF2FF; color:#4F46E5; }
.bxr-kpi-num{ font-size:15px; font-weight:700; color:#181C32; line-height:1.1; }

/* ══ 2. SUMMARY CARDS (== tabs) — EXACT port of sections/service_company/
   apptrackerHeader.blade.php's .apth-card component (the real "Application
   Tracker KPI card"), just renamed bxr- and reduced to a 3-col grid since
   this hero only has 3 cards. Every value below (padding, radius, font
   sizes/weights, icon size, active border/bg/text colors) is copied 1:1. ══ */
.bxr-sum-row{
    display:grid; grid-template-columns:repeat(3, minmax(0,1fr));
    gap:10px; margin-bottom:16px; width:100%;
}
.bxr-sum-card{
    display:flex; flex-direction:column; min-width:0; overflow:hidden;
    background:#fff; border:1px solid #e4e6ef; border-radius:10px;
    padding:10px 12px; text-decoration:none;
    transition:border-color .15s, background-color .15s;
}
.bxr-sum-card:hover{ text-decoration:none; border-color:#b5b5c3; }
a.bxr-sum-link{ color:inherit; cursor:pointer; }

/* Active tab state — identical to .apth-card.active, driven by CSS custom
   properties so each card keeps its own accent color while active. */
.bxr-sum-card.bxr-sum-active{ border-color:var(--bxr-accent); background:var(--bxr-tint); }
.bxr-sum-card.sc-p{ --bxr-accent:#6D28D9; --bxr-tint:#F0EDFF; }
.bxr-sum-card.sc-b{ --bxr-accent:#1D4ED8; --bxr-tint:#EBF5FF; }
.bxr-sum-card.sc-o{ --bxr-accent:#C2410C; --bxr-tint:#FFF3EA; }

.bxr-sum-label{
    font-size:11px; font-weight:500; color:#7e8299; margin:0 0 5px; line-height:1.3;
    display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2; overflow:hidden;
    transition:color .15s, font-weight .15s;
}
.bxr-sum-card.bxr-sum-active .bxr-sum-label{ font-weight:700; color:#181c32; }

.bxr-sum-row2{ display:flex; align-items:center; justify-content:space-between; gap:6px; }
.bxr-sum-row2-left{ display:flex; align-items:center; gap:6px; min-width:0; }
.bxr-sum-icon{
    width:19px; height:19px; border-radius:50%;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
    background:var(--bxr-tint); color:var(--bxr-accent); font-size:10px;
}
.bxr-sum-icon svg{ width:10px; height:10px; }
.bxr-sum-num{
    font-size:13px; font-weight:700; color:#181c32; line-height:1.2;
    min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.bxr-sum-growth{
    font-size:9px; font-weight:500; color:var(--bs-primary,#3699ff); white-space:nowrap;
    display:inline-flex; align-items:center; gap:2px; flex-shrink:0;
}
@media(max-width:576px){ .bxr-sum-row{ grid-template-columns:1fr; } }

/* ══ 3. SEARCH BAR ══════════════════════════════ */
.bxr-search-wrap{
    background:#fff;
    padding:14px 20px;
    display:flex; align-items:center; gap:12px;
    border-bottom:1px solid #F0F2F5;
    margin-bottom:4px;
    flex-wrap:wrap;
}
.bxr-search-box{
    flex:1; min-width:200px;
    position:relative;
}
.bxr-search-box i{
    position:absolute; left:14px; top:50%;
    transform:translateY(-50%);
    color:#CBD5E1; font-size:16px;
}
.bxr-search-box input{
    width:100%; height:44px;
    border:1.5px solid #E5E9F0; border-radius:10px;
    padding:0 16px 0 44px;
    font-size:13px; font-weight:500;
    color:#334155; background:#F8FAFC;
    outline:none; font-family:'Poppins',sans-serif;
    transition:border-color .18s, box-shadow .18s;
}
.bxr-search-box input:focus{
    border-color:#2563EB; background:#fff;
    box-shadow:0 0 0 3px rgba(37,99,235,.07);
}
.bxr-search-box input::placeholder{ color:#CBD5E1; }
.bxr-sort-label{ font-size:12px; font-weight:600; color:#64748B; white-space:nowrap; }
.bxr-sort-sel{
    height:44px; min-width:160px;
    border:1.5px solid #E5E9F0; border-radius:10px;
    padding:0 12px;
    font-size:13px; font-weight:500; color:#334155;
    background:#F8FAFC; outline:none;
    font-family:'Poppins',sans-serif;
}
.bxr-status-sel{
    height:44px; min-width:130px;
    border:1.5px solid #E5E9F0; border-radius:10px;
    padding:0 12px;
    font-size:13px; font-weight:500; color:#334155;
    background:#F8FAFC; outline:none;
    font-family:'Poppins',sans-serif;
    display:none; /* hidden — status is in filter-form */
}
.bxr-refresh{
    width:44px; height:44px;
    border:1.5px solid #E5E9F0; border-radius:10px;
    background:#F8FAFC;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:17px; color:#64748B;
    transition:all .2s; flex-shrink:0;
}
.bxr-refresh:hover{ background:#2563EB; color:#fff; border-color:#2563EB; transform:rotate(90deg); }

/* ══ 4. STAGE PIPELINE FLOW — same connected circle+chevron pattern as
   AppTracker's .hp-flow (resources/views/service_company/apptracker/index.blade.php),
   bxr- prefixed to match this page's existing naming. ══ */
.bxr-flow-card{
    background:#fff;
    border:1px solid #eef0f4;
    border-radius:14px;
    box-shadow:0 1px 2px rgba(16,24,40,.04);
    padding:16px 20px;
    margin-bottom:16px;
}
.bxr-flow{
    display:flex; align-items:center; justify-content:space-between;
    gap:8px; width:100%; flex-wrap:nowrap;
    overflow-x:auto; padding-bottom:2px;
}
.bxr-total-link{ text-decoration:none; flex-shrink:0; }
.bxr-total{
    width:72px; height:72px; border-radius:14px;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:2px; transition:background-color .15s; cursor:pointer;
}
.bxr-total.bxr-total-active{ background:linear-gradient(135deg,#2563EB,#1D4ED8); }
.bxr-total.bxr-total-gray{ background:#E4E6EF; }
.bxr-total-icon{ color:#fff; display:flex; }
.bxr-total.bxr-total-gray .bxr-total-icon{ color:#7E8299; }
.bxr-total-icon i{ font-size:16px; }
.bxr-total-count{ font-size:15px; font-weight:700; color:#fff; line-height:1.2; }
.bxr-total.bxr-total-gray .bxr-total-count{ color:#3F4254; }
.bxr-total-label{ font-size:9px; font-weight:500; color:#DBEAFE; text-align:center; line-height:1.2; padding:0 4px; }
.bxr-total.bxr-total-gray .bxr-total-label{ color:#7E8299; }

.bxr-eq{ font-size:18px; font-weight:600; color:#B5B5C3; flex-shrink:0; }
.bxr-chevron{ display:flex; color:#C2C5CC; flex-shrink:0; }
.bxr-chevron svg{ width:16px; height:16px; }
.bxr-divider{ width:1px; height:44px; background:#E4E6EF; flex-shrink:0; margin:0 4px; }

.bxr-stage{ text-decoration:none; display:flex; flex-direction:column; align-items:center; gap:6px; flex-shrink:0; cursor:pointer; }
.bxr-circle{
    width:60px; height:60px; border-radius:50%;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:1px; background:#F2F3F7; border:2px solid #E4E6EF;
    transition:border-color .15s, background-color .15s;
}
.bxr-circle .bxr-circle-icon{ display:flex; color:#99A1B7; transition:color .15s; font-size:14px; }
.bxr-circle .bxr-circle-count{ font-size:13px; font-weight:700; color:#5E6278; line-height:1.2; transition:color .15s; }
.bxr-circle.bxr-c-active{ border-color:var(--bxr-color); background:var(--bxr-bg); }
.bxr-circle.bxr-c-active .bxr-circle-icon{ color:var(--bxr-color); }
.bxr-circle.bxr-c-active .bxr-circle-count{ color:var(--bxr-color); }
.bxr-stage-label{
    font-size:11px; font-weight:500; color:#7E8299;
    text-align:center; white-space:nowrap; transition:color .15s, font-weight .15s;
}
.bxr-stage-label.bxr-l-active{ font-weight:700; color:#181C32; }

@media(max-width:1440px){
    .bxr-total{ width:64px; height:64px; }
    .bxr-circle{ width:52px; height:52px; }
}
@media(max-width:576px){
    .bxr-flow{ justify-content:flex-start; }
}

/* ══ 5. TABLE AREA ══════════════════════════════ */
.bxr-tbl-wrap{
    background:#fff;
    border-radius:0;
}
.bxr-tbl-loading{
    display:none; align-items:center; justify-content:center;
    width:44px; height:44px; flex-shrink:0;
}
.bxr-tbl-scroll{ overflow-x:auto; }
.bxr-tbl{
    width:100%; border-collapse:collapse;
    min-width:860px;
}
.bxr-tbl thead tr{ background:#F8FAFC; }
.bxr-tbl thead th{
    padding:11px 16px;
    font-size:10px; font-weight:700;
    color:#94A3B8; text-transform:uppercase;
    letter-spacing:.6px;
    border-bottom:1px solid #F0F2F5;
    white-space:nowrap; font-family:'Poppins',sans-serif;
}
.bxr-tbl tbody tr{
    border-bottom:1px solid #F8FAFC;
    transition:background .13s;
}
.bxr-tbl tbody tr:hover{ background:#F8FAFF; }
.bxr-tbl tbody tr:last-child{ border-bottom:none; }
.bxr-tbl td{ padding:14px 16px; vertical-align:middle; }

/* Company logo */
.bxr-co-logo{
    width:36px; height:36px; border-radius:8px;
    object-fit:contain; border:1px solid #EDF2F7;
    background:#F8FAFC; flex-shrink:0;
}
.bxr-co-init{
    width:36px; height:36px; border-radius:8px;
    background:linear-gradient(135deg,#4F46E5,#7C3AED);
    display:flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:700; color:#fff; flex-shrink:0;
}
.bxr-job-row{ display:flex; align-items:flex-start; gap:10px; }
.bxr-job-title{ font-size:13px; font-weight:700; color:#1A1D23; margin-bottom:1px; }
.bxr-job-co{ font-size:11px; color:#64748B; margin-bottom:5px; }
.bxr-skills{ display:flex; flex-wrap:wrap; gap:4px; margin-top:3px; }
.bxr-skill{
    font-size:10px; font-weight:600;
    background:#F1F5F9; color:#475569;
    padding:2px 7px; border-radius:4px;
}
.bxr-skill.more{ background:#EFF6FF; color:#2563EB; }
/* Matched keyword — candidate skill that satisfies one of the job's required
   skills (same green treatment as AppTracker's matched-skill badge) */
.bxr-skill.matched{ background:#DCFCE7; color:#15803D; border:1px solid #BBF7D0; }
/* Match % badge — AppTracker's exact score-color thresholds, replicated here */
.bxr-match-badge{ font-size:10px; font-weight:700; padding:2px 8px; border-radius:4px; white-space:nowrap; }

/* Cell text */
.bxr-c1{ font-size:12px; font-weight:600; color:#1A1D23; margin-bottom:2px; }
.bxr-c2{ font-size:11px; color:#64748B; }
.bxr-c3{ font-size:11px; color:#94A3B8; margin-top:2px; }

/* Inline badges */
.bxr-b{
    display:inline-flex; align-items:center; gap:3px;
    font-size:10px; font-weight:600;
    padding:2px 8px; border-radius:4px;
    white-space:nowrap;
}
.bxr-b.gr{ background:#DCFCE7; color:#16A34A; }
.bxr-b.bl{ background:#DBEAFE; color:#1D4ED8; }
.bxr-b.yw{ background:#FEF9C3; color:#A16207; }
.bxr-b.pu{ background:#F5F3FF; color:#7C3AED; }
.bxr-b.gy{ background:#F1F5F9; color:#475569; }
.bxr-b.rd{ background:#FEE2E2; color:#DC2626; }

/* Pagination wrapper — content rendered by pagination partial */
.bxr-pagi{ display:block; }

/* ══ Responsive ══════════════════════════════════ */
@media(max-width:900px){
    .bxr-hero{ flex-direction:column; align-items:flex-start; }
    .bxr-hero-sep{ display:none; }
    .bxr-kpis{ width:100%; min-width:0; flex-wrap:wrap; }
    .bxr-sum-row{ grid-template-columns:1fr; }
}

/* ══ 6. FILTER TOOLBAR (search / sort / filter button) ══════ */
.bxr-toolbar-card{
    background:#fff; padding:14px 20px;
    display:flex; align-items:center; gap:12px;
    border-bottom:1px solid #F0F2F5;
    margin-bottom:4px; flex-wrap:wrap;
}
.bxr-search-wrap2{ flex:1; min-width:220px; position:relative; }
.bxr-search-icon2{ position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#CBD5E1; display:flex; }
.bxr-search-input{
    width:100%; height:44px; border:1.5px solid #E5E9F0; border-radius:10px;
    padding:0 16px 0 42px; font-size:13px; font-weight:500; color:#334155;
    background:#F8FAFC; outline:none; font-family:'Poppins',sans-serif;
    transition:border-color .18s, box-shadow .18s;
}
.bxr-search-input:focus{ border-color:#2563EB; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.07); }
.bxr-sort-wrap{ position:relative; width:190px; flex-shrink:0; }
.bxr-sort-btn{
    width:100%; height:44px; border:1.5px solid #E5E9F0; border-radius:10px;
    background:#F8FAFC; padding:0 12px; display:flex; align-items:center; gap:8px;
    font-size:13px; font-weight:500; color:#334155; cursor:pointer;
}
.bxr-sort-btn svg{ flex-shrink:0; color:#94A3B8; }
.bxr-sort-btn .arrow{ margin-left:auto; transition:transform .15s; color:#94A3B8; }
.bxr-sort-btn.open .arrow{ transform:rotate(180deg); }
.bxr-sort-panel{
    position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:50;
    background:#fff; border:1px solid #E5E9F0; border-radius:10px;
    box-shadow:0 8px 24px rgba(15,23,42,.09); padding:6px; max-height:260px; overflow-y:auto;
}
.bxr-sort-item{ padding:9px 12px; font-size:12.5px; font-weight:500; color:#374151; border-radius:7px; cursor:pointer; }
.bxr-sort-item:hover{ background:#F5F8FF; }
.bxr-sort-item.active{ background:#EFF6FF; color:#2563EB; font-weight:700; }

/* ══ 7. REUSABLE CLIENT TYPE BADGE ═══════════════════
   Single component used across the platform to distinguish
   Internal Client (blue) from Direct Client (green). The
   thin left stripe + soft pill body + colored icon
   reads instantly; works inside any cell without restyling.
   ═══════════════════════════════════════════════════════ */
.client-badge{
  display:inline-flex; align-items:center; gap:6px;
  position:relative; padding:2px 10px;
  border-radius:6px;
  font-size:10.5px; font-weight:600; line-height:1.2;
  border:1px solid transparent; font-family:'Poppins',sans-serif;
  width:max-content; max-width:100%;
}
.client-badge-stripe{
  position:absolute; left:-1px; top:2px; bottom:2px; width:3px;
  border-radius:2px; background:currentColor;
}
.client-badge i{ font-size:11.5px; line-height:1; flex-shrink:0; }
.client-badge-label{ white-space:nowrap; }
.client-badge-id{ opacity:0.7; font-weight:500; padding-left:2px; }

.client-badge--internal{ background:#EBF5FF; color:#1D4ED8; border-color:#BFDBFE; }
.client-badge--internal .client-badge-stripe{ background:#1D4ED8; }

.client-badge--direct{ background:#ECFDF5; color:#047857; border-color:#A7F3D0; }
.client-badge--direct .client-badge-stripe{ background:#059669; }

/* ══ 8. REDESIGNED CELL HELPERS — Job Details / Experience /
   Hiring & Loc / Hiring Manager columns of the bench_explore
   table. Poppins ≤14px, semantic tokens, vertical centering. ══ */
.bxr-tbl tbody td{ vertical-align:middle !important; }

.bxr-col-exp-stack{ display:flex; flex-direction:column; gap:6px; min-width:130px; }
.bxr-col-exp-row{
  display:flex; align-items:center; gap:6px;
  font-size:11.5px; font-weight:600; color:#181C32; line-height:1.2;
}
.bxr-col-exp-row .bxr-icon-box{
  width:13px; height:13px; display:inline-flex; align-items:center;
  justify-content:center; color:#94A3B8; flex-shrink:0; font-size:11px;
  line-height:1; font-weight:700;
}
.bxr-col-exp-mode{
  font-size:9.5px; color:#94A3B8; font-weight:600; padding-left:2px;
  text-transform:uppercase; letter-spacing:0.3px;
}
.bxr-col-loc-stack{ display:flex; flex-direction:column; gap:4px; min-width:130px; }
.bxr-col-loc-row{
  display:flex; align-items:center; gap:6px;
  font-size:12px; font-weight:600; color:#181C32; line-height:1.2;
}
.bxr-col-loc-row .bxr-icon-box{
  width:13px; height:13px; display:inline-flex; align-items:center;
  justify-content:center; color:#2563EB; flex-shrink:0; font-size:12px;
}
.bxr-col-loc-mode{ font-size:10.5px; color:#7E8299; }
.bxr-col-hm-row{ display:flex; align-items:center; gap:8px; min-width:0; }
.bxr-col-hm-av{
  width:30px; height:30px; border-radius:8px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  font-weight:700; font-size:11.5px;
  letter-spacing:0.2px;
}
.bxr-col-hm-av--filled{ background:#EEF2FF; color:#4F46E5; }
.bxr-col-hm-av--empty{ background:#F1F5F9; color:#94A3B8; border:1px dashed #E4E6EF; }
.bxr-col-hm-info{ min-width:0; display:flex; flex-direction:column; gap:1px; }
.bxr-col-hm-name{
  font-size:12px; font-weight:700; color:#1A1D23; line-height:1.2;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  max-width:140px;
}
.bxr-col-hm-name.hmlisting{ cursor:pointer; }
.bxr-col-hm-name.hmlisting:hover{ color:#2563EB; }
.bxr-col-hm-role{ font-size:10.5px; color:#94A3B8; font-weight:500; }
</style>
@endpush

@section('content')

{{-- ── Hidden compat layer: keeps all original IDs alive ─────── --}}
<div aria-hidden="true"
     style="position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;opacity:0;z-index:-9;">
    @php
        /* url('.../bench_explore/') silently drops the trailing slash
           (Laravel's UrlGenerator normalizes it away), so concatenating the
           id directly onto it produced "bench_explore123" instead of
           "bench_explore/123" — a URL that can never match the
           bench_explore/{id?}/{stage?} route and 404s on every click. */
        $bench_explore_url      = url('service_company/bench-candidates/bench_explore') . '/' . @$bench_detail->id . '/all';
        $recommend_req_url      = url('service_company/bench-candidates/recommend_req/')          .'/'. @$bench_detail->id;
        $recommend_internal_req = url('service_company/bench-candidates/recommend_internal_req/') .'/'. @$bench_detail->id;
        $tabactive = [
            'bench_explore_pool_active'      => 'tabactive',
            'bench_explore_pool_url'         => $bench_explore_url,
            'bench_explore_pool_id'          => 'bench_explore_pool_id',
            'bench_explore_counter'          => $recomCount['bench_explore_counter'],
            'recommend_req_active'           => '',
            'recommend_req_url'              => $recommend_req_url,
            'recommend_req_id'               => 'recommend_req_id',
            'recommend_req_counter'          => $recomCount['recommend_req_counter'],
            'recommend_req_internal_active'  => '',
            'recommend_req_internal_url'     => $recommend_internal_req,
            'recommend_req_internal_id'      => 'recommend_req_internal_id',
            'recommend_req_internal_counter' => $recomCount['recommend_internal_req_counter'],
        ];
    @endphp
    @include('sections.service_company.bench-toolbar',    ['job'=>@$job,'bench_detail'=>@$bench_detail])
    @include('sections.service_company.benchExploreHeader',['tabactive'=>$tabactive])
    <form action="" id="filter-form">
        @csrf
        <input type="hidden" name="bench_id"    id="bench_id"    value="{{ @$bench_id }}">
        <input type="hidden" name="stage"       id="stage"       value="{{ @$stage }}">
        <input type="text"   name="inputSearch" id="inputSearch" value="">
    </form>
    <div id="bg_all" class="bg-total-active"></div>
    <span id="tx_1"></span><span id="tx_2"></span><span id="tx_3"></span>
    <span id="tx_4"></span><span id="tx_5"></span><span id="tx_6"></span><span id="tx_7"></span>
    <span class="nav_status"></span><span class="bg d-none"></span>
</div>

@php
    $sourcing_bg=$screening_bg=$interview_bg=$negotation_bg=$offer_bg=$joining_bg=$rejected_bg=$All_candidate_bg='alert bg-gray text-center circle_boxes';
    $sourcing_text=$screening_text=$interview_text=$negotation_text=$offer_text=$joining_text=$rejected_text=$All_candidate_text='text-center color-all';
    if(!empty(@$candStatusClass['Index'])){
        if     (@$candStatusClass['Index']=='Sourcing')    {$sourcing_bg  ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$sourcing_text  ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Screening')   {$screening_bg ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$screening_text ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Interview')   {$interview_bg ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$interview_text ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Negotiation') {$negotation_bg='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$negotation_text='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Offered')     {$offer_bg     ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$offer_text     ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Joined')      {$joining_bg   ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$joining_text   ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Rejected')    {$rejected_bg  ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$rejected_text  ='text-center '.@$candStatusClass['nav_text'];}
        else                                               {$All_candidate_bg='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$All_candidate_text='text-center '.@$candStatusClass['nav_text'];}
    }
    $tr_class=(!empty($mstatus_id)&&$mstatus_id=='all')?'bg-total-active':'bg-gray';
@endphp

@if(in_array('view_bench',$userPermissions))
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
 <div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-0">
  <div class="bxr-page">

  {{-- ══ SECTION 1 · PROFILE HERO — segmented stat card:
       identity | Experience + Bill Rate | Bench Status + On Bench | icon KPIs ══ --}}
  @php
    $imgFile  = @$bench_detail->profile_image ?? @$bench_detail->image ?? @$bench_detail->photo ?? null;
    /* $bench_detail->name is the candidate's CURRENT STAGE (from the
       bench_master_status join — Bench/Hold/Placed/Pool/etc.), not their
       name. The candidate's actual name is full_name. */
    $candName = @$bench_detail->full_name ?? 'Candidate';
    $initLtr  = strtoupper(substr($candName,0,1));
    $bxrStageLabel = ucwords(@$bench_detail->name ?? 'Bench');
    $bxrStageKey = strtolower($bxrStageLabel);
    if (str_contains($bxrStageKey, 'placed'))     { $bxrStageColor='#16A34A'; $bxrStageBg='#F0FDF4'; $bxrStageBorder='#86EFAC'; }
    elseif (str_contains($bxrStageKey, 'hold'))   { $bxrStageColor='#EA580C'; $bxrStageBg='#FFF7ED'; $bxrStageBorder='#FDBA74'; }
    elseif (str_contains($bxrStageKey, 'bench'))  { $bxrStageColor='#2563EB'; $bxrStageBg='#EFF6FF'; $bxrStageBorder='#93C5FD'; }
    else                                           { $bxrStageColor='#64748B'; $bxrStageBg='#F1F5F9'; $bxrStageBorder='#CBD5E1'; }
    /* Bill Rate — same source fields the old bench-toolbar partial displayed
       (bill_rate_amount/currency/mode, already in the select). */
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

  {{-- ══ SECTION 2 · SUMMARY CARDS — also function as tabs to the sibling
       Recommended Client Jobs / Recommended Internal Jobs pages, so all
       three cards read as one shared component across all three pages ══ --}}
  <div class="bxr-sum-row">
    {{-- Card 1: purple — this page (Pool Jobs), always active here --}}
    <a href="{{ $bench_explore_url }}" class="bxr-sum-card sc-p bxr-sum-link bxr-sum-active">
      <p class="bxr-sum-label">Total Companies Added</p>
      <div class="bxr-sum-row2">
        <span class="bxr-sum-row2-left">
          <span class="bxr-sum-icon"><i class="ti ti-users-group"></i></span>
          <span class="bxr-sum-num bxr-live">{{ $recomCount['bench_explore_counter'] }}</span>
        </span>
        <span class="bxr-sum-growth">
          View List
          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </span>
      </div>
    </a>
    {{-- Card 2: blue — links to the Recommended Client Jobs list page --}}
    <a href="{{ $recommend_req_url }}" class="bxr-sum-card sc-b bxr-sum-link">
      <p class="bxr-sum-label">Recommended Client Jobs</p>
      <div class="bxr-sum-row2">
        <span class="bxr-sum-row2-left">
          <span class="bxr-sum-icon"><i class="ti ti-briefcase"></i></span>
          <span class="bxr-sum-num" id="recommend_req_id">{{ $recomCount['recommend_req_counter'] }}</span>
        </span>
        <span class="bxr-sum-growth">
          View List
          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </span>
      </div>
    </a>
    {{-- Card 3: orange — links to the Recommended Internal Jobs list page --}}
    <a href="{{ $recommend_internal_req }}" class="bxr-sum-card sc-o bxr-sum-link">
      <p class="bxr-sum-label">Recommended Internal Jobs</p>
      <div class="bxr-sum-row2">
        <span class="bxr-sum-row2-left">
          <span class="bxr-sum-icon"><i class="ti ti-building-skyscraper"></i></span>
          <span class="bxr-sum-num" id="recommend_req_internal_id">{{ $recomCount['recommend_internal_req_counter'] }}</span>
        </span>
        <span class="bxr-sum-growth">
          View List
          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </span>
      </div>
    </a>
  </div>

  {{-- ══ SECTION 3 · SEARCH / SORT / FILTER TOOLBAR ═══ --}}
  <div class="bxr-toolbar-card">
    <div class="bxr-search-wrap2">
      <span class="bxr-search-icon2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      </span>
      <input type="text" id="kt_tagify_2" name="inputJobSearch" class="bxr-search-input"
             placeholder="Search by company, job or recruiter...">
    </div>

    <div class="bxr-sort-wrap" id="bxr_sort_wrap">
      <button type="button" class="bxr-sort-btn" id="bxr_sort_btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5h10M11 9h7M11 13h4M3 17l3 3 3-3M6 18V4"/></svg>
        <span id="bxr_sort_label">Newest to Oldest</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </button>
      <select id="hiringtype" style="display:none;">
        <option value="All">Last Activity</option>
        <option value="1" selected>Newest to Oldest</option>
        <option value="2">Oldest to Newest</option>
        <option value="3">Low to High (Salary)</option>
        <option value="4">High to Low (Salary)</option>
      </select>
      <div class="bxr-sort-panel d-none" id="bxr_sort_panel"></div>
    </div>

    <div class="bxr-tbl-loading loadingMessagebenchCandidateExploreJobAjax" style="display:none;" title="Processing...">
      <span class="spinner-border spinner-border-sm text-primary" style="width:13px;height:13px;border-width:2px;"></span>
    </div>

    <button class="bxr-refresh" onclick="fetchBenchExploreJobCandidateList()" title="Refresh">
      <i class="ti ti-refresh"></i>
    </button>
  </div>

  {{-- ══ SECTION 4 · STAGE PIPELINE FLOW — same connected-circle pattern as
       AppTracker's Hiring Pipeline card ══ --}}
  @php
      $bxrStages = [
          ['rel'=>'1','label'=>'Sourcing',   'icon'=>'ti-users-group',     'color'=>'#7C3AED','bg'=>'#EEEBFF'],
          ['rel'=>'2','label'=>'Screening',  'icon'=>'ti-clipboard-list',  'color'=>'#0284C7','bg'=>'#E0F2FE'],
          ['rel'=>'3','label'=>'Evaluation', 'icon'=>'ti-search',          'color'=>'#0EA5E9','bg'=>'#E0F2FE'],
          ['rel'=>'4','label'=>'Negotiation','icon'=>'ti-3d-cube-sphere',  'color'=>'#D97706','bg'=>'#FEF3C7'],
          ['rel'=>'5','label'=>'Offer',      'icon'=>'ti-briefcase',       'color'=>'#059669','bg'=>'#D1FAE5'],
          ['rel'=>'6','label'=>'Joined',     'icon'=>'ti-circle-check',    'color'=>'#16A34A','bg'=>'#DCFCE7'],
      ];
      $bxrRejected = ['rel'=>'7','label'=>'Rejected','icon'=>'ti-circle-x','color'=>'#E11D48','bg'=>'#FFE4E6'];
  @endphp
  <div class="bxr-flow-card">
    <div class="bxr-flow" id="bxr-stages-row">

      {{-- ALL / TOTAL — active by default --}}
      <a class="application_sttus_nav bxr-total-link" href="javascript:void(0);" rel="all">
        <div class="bxr-total bxr-total-active" data-bxr="all">
          <span class="bxr-total-icon"><i class="ti ti-layout-grid"></i></span>
          <span class="bxr-total-count" id="totalcnt">0</span>
          <span class="bxr-total-label">All Jobs</span>
        </div>
      </a>
      <span class="bxr-eq">=</span>

      @foreach($bxrStages as $i => $s)
        <a class="application_sttus_nav bxr-stage" href="javascript:void(0);" rel="{{ $s['rel'] }}">
          <div class="bxr-circle" data-bxr="{{ $s['rel'] }}" style="--bxr-color:{{ $s['color'] }};--bxr-bg:{{ $s['bg'] }};">
            <span class="bxr-circle-icon"><i class="ti {{ $s['icon'] }}"></i></span>
            <span class="bxr-circle-count" id="bg_{{ $s['rel'] }}">0</span>
          </div>
          <div class="bxr-stage-label" data-bxr-label="{{ $s['rel'] }}">{{ $s['label'] }}</div>
        </a>
        <span class="bxr-chevron">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
        </span>
      @endforeach

      <span class="bxr-divider"></span>
      <a class="application_sttus_nav bxr-stage" href="javascript:void(0);" rel="{{ $bxrRejected['rel'] }}">
        <div class="bxr-circle" data-bxr="{{ $bxrRejected['rel'] }}" style="--bxr-color:{{ $bxrRejected['color'] }};--bxr-bg:{{ $bxrRejected['bg'] }};">
          <span class="bxr-circle-icon"><i class="ti {{ $bxrRejected['icon'] }}"></i></span>
          <span class="bxr-circle-count" id="bg_{{ $bxrRejected['rel'] }}">0</span>
        </div>
        <div class="bxr-stage-label" data-bxr-label="{{ $bxrRejected['rel'] }}">{{ $bxrRejected['label'] }}</div>
      </a>

    </div>{{-- /bxr-flow --}}
  </div>{{-- /bxr-flow-card --}}
  {{-- Kept alive only so tableLoad()'s totalcnt_plain write is a harmless no-op-safe target; not shown. --}}
  <span id="totalcnt_plain" style="display:none;"></span>

  {{-- ══ SECTION 5 · TABLE ══════════════════════════════ --}}
  <div class="bxr-tbl-wrap">
    <div class="bxr-tbl-scroll">
      <table id="bench_explore_listing" class="bxr-tbl table-custom-class">
        <thead>
          <tr>
            <th style="min-width:220px;">JOB DETAILS</th>
            <th style="min-width:130px;">EXPERIENCE</th>
            <th style="min-width:130px;">HIRING &amp; LOCATION</th>
            <th style="min-width:130px;">HIRING MANAGER</th>
            <th style="min-width:110px;">JOB STAGE</th>
            <th style="min-width:110px;">ADDED BY</th>
          </tr>
        </thead>
        <tbody id="benchCandidateExploreJobAjax"></tbody>
      </table>
    </div>

    <div class="bxr-pagi">
      <div id="paginationbenchCandidateExploreJob"></div>
    </div>
  </div>

  </div>{{-- /bxr-page --}}
  </div>
 </div>
</div>
@endif

{{-- Drawer — unchanged ── --}}
<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
     data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'95%', 'sm':'90%', 'lg':'80%'}"
     data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle"
     data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>

@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>

{{-- ── Pipeline flow active-state visual (AppTracker-style circles) ── --}}
<script>
/* totalcnt_plain has no visible target anymore, but tableLoad() still writes
   to it — keeping this a harmless no-op instead of touching that function. */
function syncPillCounts(){
    $('#totalcnt_plain').text($('#totalcnt').text());
}
function bxrSetActiveStage(page){
    $('.bxr-total').removeClass('bxr-total-active').addClass('bxr-total-gray');
    $('.bxr-circle').removeClass('bxr-c-active');
    $('.bxr-stage-label').removeClass('bxr-l-active');
    if(page==='all'){
        $('.bxr-total').removeClass('bxr-total-gray').addClass('bxr-total-active');
    } else {
        $('.bxr-circle[data-bxr="'+page+'"]').addClass('bxr-c-active');
        $('.bxr-stage-label[data-bxr-label="'+page+'"]').addClass('bxr-l-active');
    }
}
$(document).ready(function(){
    bxrSetActiveStage($('#stage').val()||'all');
});
$(document).on('click','.application_sttus_nav',function(){
    bxrSetActiveStage($(this).attr('rel'));
});
</script>

{{-- ── Original stage-click logic — UNCHANGED ── --}}
<script>
$(document).ready(function(){
    var cur=$('#stage').val();
    if(cur=='all'){$('.nav_status').html('All Candidate').css({background:'linear-gradient(to left,#01e074,#0581d9)',color:'#fff'});}
    $(document).on('click','.application_sttus_nav',function(){
        $(".bg").removeClass('bg-Asourcing bg-Ascreening bg-Ainterview bg-Anegotation bg-Aoffer bg-Ajoiner bg-Arejected').addClass('bg-gray');
        ['1','2','3','4','5','6','7'].forEach(function(n){
            $('#tx_'+n).removeClass('text-Asourcing text-Ascreening text-Ainterview text-Anegotation text-Aoffer text-Ajoiner text-Arejected').addClass('color-all');
        });
        $('#bg_all').removeClass('bg-total-active').addClass('bg-gray');
        var page=$(this).attr('rel'), bid=$('#bench_id').val();
        var M={1:['bg-Asourcing','text-Asourcing','Sourcing',{background:'#efefef',color:'#000'}],
               2:['bg-Ascreening','text-Ascreening','Screening',{background:'#fff9ed',color:'#ffab00'}],
               3:['bg-Ainterview','text-Ainterview','Interview',{background:'#f0f8ff',color:'#2896ff'}],
               4:['bg-Anegotation','text-Anegotation','Negotiation',{background:'#ffe6f2',color:'#ff4098'}],
               5:['bg-Aoffer','text-Aoffer','Offered',{background:'#d4ffde',color:'#06c732'}],
               6:['bg-Ajoiner','text-Ajoiner','Joined',{background:'#f7deff',color:'#c000ff'}],
               7:['bg-Arejected','text-Arejected','Rejected',{background:'#ffe2e5',color:'#ff0202'}]};
        if(M[page]){$('#bg_'+page).removeClass('bg-gray').addClass(M[page][0]);$('#tx_'+page).removeClass('color-all').addClass(M[page][1]);$('.nav_status').html(M[page][2]).css(M[page][3]);}
        else if(page=='all'){$('.nav_status').html('All Candidate').css({background:'linear-gradient(to left,#01e074,#0581d9)',color:'#fff'});$('#bg_all').removeClass('bg-gray').addClass('bg-total-active');}
        $('#stage').val(page);
        var url='<?php echo url('/service_company/bench-candidates/bench_explore/'); ?>';
        window.history.pushState('page2','Title',url+'/'+bid+'/'+page);
        fetchBenchExploreJobCandidateList();
    });
});
</script>

{{-- ── BenchExploreTabManager: Centralized Tab & Lazy Loading Manager ── --}}
<script>
var BenchExploreTabManager = (function(){
    var state = {
        activeTab: $('#stage').val() || 'all',
        activePage: 1,
        searchDebounceTimer: null,
        activeXHR: null,
        requestSeqId: 0,
        cache: {}
    };

    return {
        init: function() {
            var self = this;
            self.bindEvents();
            self.loadCurrentTab(1);
        },

        bindEvents: function() {
            var self = this;

            // Debounced input typing handler (300ms)
            $(document).on('input', '#kt_tagify_2', function(){
                clearTimeout(state.searchDebounceTimer);
                state.searchDebounceTimer = setTimeout(function(){
                    self.loadCurrentTab(1);
                }, 300);
            });

            // Enter key immediate trigger
            $(document).on('keydown', '#kt_tagify_2', function(e){
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    clearTimeout(state.searchDebounceTimer);
                    self.loadCurrentTab(1);
                }
            });
        },

        getCacheKey: function(page) {
            var bid = $('#bench_id').val(),
                stage = $('#stage').val(),
                sort = $('#hiringtype').val(),
                cs = $('#kt_tagify_2').val(),
                st = $('[name="bxr_status"]:checked').val() || 'active',
                activity = $('[name="bxr_activity"]:checked').val() || '1',
                company = (typeof bxrTagifyCompany !== 'undefined') ? JSON.stringify(bxrTagifyCompany.value) : '[]',
                location = (typeof bxrTagifyLocation !== 'undefined') ? JSON.stringify(bxrTagifyLocation.value) : '[]',
                minSal = $('#bxr_min_sal').val(),
                maxSal = $('#bxr_max_sal').val(),
                perPage = localStorage.getItem('candidatePerPage') || 10;

            return [bid, stage, sort, cs, st, activity, company, location, minSal, maxSal, perPage, page].join('|');
        },

        loadCurrentTab: function(page) {
            var self = this;
            page = page || 1;
            state.activePage = page;

            if (state.activeXHR && state.activeXHR.readyState !== 4) {
                state.activeXHR.abort();
            }

            var cacheKey = self.getCacheKey(page);
            var now = Date.now();

            if (state.cache[cacheKey] && (now - state.cache[cacheKey].timestamp < 30000)) {
                self.render(state.cache[cacheKey]);
                return Promise.resolve(state.cache[cacheKey]);
            }

            state.requestSeqId++;
            var seqId = state.requestSeqId;
            $('.loadingMessagebenchCandidateExploreJobAjax').css({display: 'flex'});

            var perPage = localStorage.getItem('candidatePerPage') || 10;
            var bid = $('#bench_id').val(),
                stage = $('#stage').val(),
                sort = $('#hiringtype').val(),
                cs = $('#kt_tagify_2').val(),
                st = $('[name="bxr_status"]:checked').val() || 'active',
                activity = $('[name="bxr_activity"]:checked').val() || '1',
                company = (typeof bxrTagifyCompany !== 'undefined') ? JSON.stringify(bxrTagifyCompany.value) : '[]',
                location = (typeof bxrTagifyLocation !== 'undefined') ? JSON.stringify(bxrTagifyLocation.value) : '[]',
                minSal = $('#bxr_min_sal').val(),
                maxSal = $('#bxr_max_sal').val();

            state.activeXHR = $.ajax({
                url: "{!! route('service_company.bench-candidates.bench_explore_listing_job_ajax') !!}?page=" + page,
                type: 'GET',
                data: {
                    bench_id: bid, stage: stage, cus_search: cs, statusfilter: st, sortingfilter: sort,
                    bxr_company: company, bxr_location: location,
                    bxr_min_sal: minSal, bxr_max_sal: maxSal, bxr_activity: activity,
                    per_page: perPage, size: perPage
                },
                success: function(r) {
                    if (seqId !== state.requestSeqId) return;
                    $('.loadingMessagebenchCandidateExploreJobAjax').css({display: 'none'});
                    
                    state.cache[cacheKey] = {
                        html: r.html,
                        total: r.total,
                        pagination: r.pagination,
                        counters: r.counters,
                        allCounter: r.allCounter,
                        timestamp: Date.now()
                    };

                    self.render(state.cache[cacheKey]);
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        $('.loadingMessagebenchCandidateExploreJobAjax').css({display: 'none'});
                    }
                }
            });

            return state.activeXHR;
        },

        render: function(res) {
            var overallTotal = (res.counters ? (res.counters.allCandidate || res.counters.all) : null) || res.allCounter || res.total;
            $('#benchCandidateExploreJobAjax').html(res.html);
            $('#bench_explore_pool_id').html(overallTotal);
            $('.bxr-live').html(overallTotal);
            $('#bxr_footer_number').text(res.total);
            $('#paginationbenchCandidateExploreJob').html(res.pagination);

            if (res.counters) {
                var d = res.counters;
                var totalCandidateCnt = d.allCandidate || d.all || overallTotal;
                $('#bg_1').html(d.Sourcing || 0);   $('#bg_2').html(d.Screening || 0);
                $('#bg_3').html(d.Evaluation || 0); $('#bg_4').html(d.Negotiation || 0);
                $('#bg_5').html(d.Offered || 0);    $('#bg_6').html(d.Joined || 0);
                $('#bg_7').html(d.Rejected || 0);   $('#totalcnt').html(totalCandidateCnt);
                $('#totalcnt_plain').html(totalCandidateCnt);
                syncPillCounts();
            } else {
                tableLoad();
            }
        }
    };
})();

var table;
function filtersdataget(){ return BenchExploreTabManager.loadCurrentTab(1); }
function fetchBenchExploreJobCandidateList(page){ return BenchExploreTabManager.loadCurrentTab(page); }

function tableLoad(){
    var bid=$('#bench_id').val(),stage=$('#stage').val(),q=$('#inputSearch').val();
    var url='{{ route('service_company.bench-candidates.bench_explore_listing_counter',[':bench_id',':stage',':inputSearch']) }}';
    url=url.replace(':bench_id',bid).replace(':stage',stage).replace(':inputSearch',q);
    $.ajax({type:'GET',url:url,success:function(r){
        var d={allCandidate:0,Sourcing:0,Screening:0,Rejected:0,Offered:0,Negotiation:0,Joined:0,Evaluation:0};
        if(r.allCounter>0) d=r.data;
        setTimeout(function(){
            $('#bg_1').html(d.Sourcing);   $('#bg_2').html(d.Screening);
            $('#bg_3').html(d.Evaluation); $('#bg_4').html(d.Negotiation);
            $('#bg_5').html(d.Offered);    $('#bg_6').html(d.Joined);
            $('#bg_7').html(d.Rejected);   $('#totalcnt').html(d.allCandidate);
            $('#totalcnt_plain').html(d.allCandidate);
            syncPillCounts();
        },100);
    }});
}
$(document).on('click','.show-detail',function(){
    $('#kt_activitiesJob_toggle').trigger('click');
    var id=$(this).data('row-id');
    var url="{{ route('service_company.jobs.viewmore',':id') }}?viewmore=viewmore&conid={{ $bench_detail->id }}&bechshowtab=1";
    url=url.replace(':id',id);
    var gif="{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({type:'GET',url:url,beforeSend:function(){$('#kt_activitiesJob_body').html('<img src="'+gif+'" height="35px" width="100%">');},success:function(r){$('#kt_activitiesJob_body').html(r.view);}});
});
$(document).on('click','.client-show-detail',function(){
    $('#kt_activitiesJob_toggle').trigger('click');
    var id=$(this).data('row-id');
    var url="{{ route('service_company.jobs.viewmore',':id') }}?viewmore=clientviewmore&conid={{ $bench_detail->id }}&bechshowtab=1";
    url=url.replace(':id',id);
    var gif="{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({type:'GET',url:url,beforeSend:function(){$('#kt_activitiesJob_body').html('<img src="'+gif+'" height="35px" width="100%">');},success:function(r){$('#kt_activitiesJob_body').html(r.view);}});
});

var bxrPaginationLoading = false;
$(document).ready(function(){
    BenchExploreTabManager.init();
    $(document).on('click','#paginationbenchCandidateExploreJob .pagi-page, #paginationbenchCandidateExploreJob .pagi-arrow',function(e){
        e.preventDefault();
        var $btn=$(this);
        if (bxrPaginationLoading || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled')) return;
        var pg=$btn.data('page');
        if(!pg) return;
        bxrPaginationLoading = true;
        BenchExploreTabManager.loadCurrentTab(pg)
            .then(function(){ bxrPaginationLoading = false; })
            .catch(function(){ bxrPaginationLoading = false; });
    });
    $(document).on('change','#paginationbenchCandidateExploreJob .pagi-perpage',function(e){
        var selectedPerPage = $(this).val();
        if (selectedPerPage) {
            localStorage.setItem('candidatePerPage', selectedPerPage);
            BenchExploreTabManager.loadCurrentTab(1);
        }
    });
});
</script>

{{-- ── Filter drawer: toast, sort panel, Tagify, active-filter chips ── --}}
<script>
const bxrToast = Swal.mixin({
    toast: true, position: 'top-end', showConfirmButton: false,
    timer: 2600, timerProgressBar: true,
    didOpen: (toast) => { toast.onmouseenter = Swal.stopTimer; toast.onmouseleave = Swal.resumeTimer; }
});

/* ── Custom sort dropdown driven by the real #hiringtype <select> ── */
function bxrRenderSortPanel(){
    var $panel=$('#bxr_sort_panel').empty();
    var current=$('#hiringtype').val();
    $('#hiringtype option').each(function(){
        var $opt=$(this), val=$opt.attr('value'), label=$opt.text();
        $('<div class="bxr-sort-item"></div>').text(label)
            .toggleClass('active', val===current)
            .on('click', function(){
                $('#hiringtype').val(val);
                $('#bxr_sort_label').text(label);
                bxrCloseSortPanel();
                filtersdataget();
            }).appendTo($panel);
    });
}
function bxrOpenSortPanel(){ bxrRenderSortPanel(); $('#bxr_sort_panel').removeClass('d-none'); $('#bxr_sort_btn').addClass('open'); }
function bxrCloseSortPanel(){ $('#bxr_sort_panel').addClass('d-none'); $('#bxr_sort_btn').removeClass('open'); }
$(document).on('click','#bxr_sort_btn',function(e){
    e.stopPropagation();
    if ($('#bxr_sort_panel').hasClass('d-none')) bxrOpenSortPanel(); else bxrCloseSortPanel();
});
$(document).on('click',function(e){ if(!$(e.target).closest('#bxr_sort_wrap').length) bxrCloseSortPanel(); });

</script>
@endpush
