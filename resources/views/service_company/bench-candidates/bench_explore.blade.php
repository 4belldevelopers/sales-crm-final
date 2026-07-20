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

/* ══ 1. PROFILE HERO ════════════════════════════ */
.bxr-hero{
    background:#fff;
    border-radius:0;
    padding:22px 28px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:24px;
    flex-wrap:wrap;
    border-bottom:1px solid #F0F2F5;
    margin-bottom:4px;
}

/* Avatar */
.bxr-av-wrap{ position:relative; flex-shrink:0; }
.bxr-avatar{
    width:82px; height:82px; border-radius:16px;
    object-fit:cover;
    border:3px solid #E8EDF5;
}
.bxr-avatar-init{
    width:82px; height:82px; border-radius:16px;
    background:linear-gradient(135deg,#4F46E5,#7C3AED);
    display:flex; align-items:center; justify-content:center;
    font-size:28px; font-weight:700; color:#fff;
}
.bxr-online-dot{
    position:absolute; bottom:-3px; right:-3px;
    width:14px; height:14px;
    background:#22C55E; border:2.5px solid #fff;
    border-radius:50%;
}

/* Hero left */
.bxr-hero-left{ display:flex; align-items:center; gap:18px; flex:1; min-width:260px; }
.bxr-info{}
.bxr-name-row{ display:flex; align-items:center; gap:10px; margin-bottom:3px; flex-wrap:wrap; }
.bxr-name{ font-size:16px; font-weight:700; color:#1A1D23; margin:0; }
.bxr-bench-tag{
    display:inline-flex; align-items:center; gap:5px;
    font-size:11px; font-weight:600;
    padding:3px 10px; border-radius:20px;
    background:#F0FDF4; color:#16A34A;
    border:1.5px solid #86EFAC;
}
.bxr-bench-dot{
    width:6px; height:6px; border-radius:50%;
    background:#16A34A; flex-shrink:0;
}
.bxr-info-row{
    display:flex; align-items:center; gap:16px;
    flex-wrap:wrap; margin-bottom:8px;
}
.bxr-role{ font-size:13px; font-weight:600; color:#374151; margin:0; }
.bxr-meta-line{ display:flex; flex-wrap:wrap; gap:18px; }
.bxr-meta-item{ display:flex; align-items:center; gap:5px; font-size:12px; font-weight:500; color:#64748B; }
.bxr-meta-item i{ font-size:13px; color:#94A3B8; }
.bxr-bench-id-val{
    font-weight:700; color:#1A56DB;
    letter-spacing:.3px; font-size:12px;
}

/* KPI right */
.bxr-kpi-row{
    display:flex; align-items:center;
    gap:0; flex-shrink:0;
    flex-wrap:wrap;
}
.bxr-kpi{
    display:flex; align-items:center; gap:12px;
    padding:12px 28px;
    position:relative;
}
.bxr-kpi+.bxr-kpi::before{
    content:''; position:absolute; left:0; top:15%; height:70%;
    width:1px; background:#E5E9F0;
}
.bxr-kpi-icon{
    width:40px; height:40px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:18px; flex-shrink:0;
}
.bxr-kpi-icon.vi{ background:#EFF6FF; color:#2563EB; }
.bxr-kpi-icon.dl{ background:#F0FDF4; color:#16A34A; }
.bxr-kpi-icon.aj{ background:#EFF6FF; color:#0EA5E9; }
.bxr-kpi-icon.iv{ background:#F5F3FF; color:#7C3AED; }
.bxr-kpi-body{}
.bxr-kpi-num{ font-size:22px; font-weight:800; color:#1A1D23; line-height:1.1; }
.bxr-kpi-lbl{ font-size:11px; font-weight:500; color:#94A3B8; white-space:nowrap; }
.bxr-hero-menu{
    align-self:flex-start;
    width:32px; height:32px; border-radius:8px;
    border:1px solid #E5E9F0; background:#fff;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; color:#94A3B8; font-size:16px;
    transition:all .18s; flex-shrink:0;
}
.bxr-hero-menu:hover{ background:#F8FAFC; }

/* ══ 2. SUMMARY CARDS ═══════════════════════════ */
.bxr-sum-row{
    display:flex; gap:12px;
    margin-bottom:4px;
    flex-wrap:wrap;
    padding:12px 16px;
    background:#fff;
}
/* Each card fills 1/3 with a tinted background */
.bxr-sum-card{
    flex:1; min-width:200px;
    display:flex; align-items:center; gap:20px;
    padding:22px 24px;
    border-radius:14px;
    transition:filter .18s;
}
/* Per-card tinted background */
.bxr-sum-card.sc-p{ background:#F0EDFF; }   /* purple  */
.bxr-sum-card.sc-b{ background:#EBF5FF; }   /* blue    */
.bxr-sum-card.sc-o{ background:#FFF3EA; }   /* orange  */

/* Icon box */
.bxr-sum-icon{
    width:60px; height:60px; border-radius:16px;
    display:flex; align-items:center; justify-content:center;
    font-size:28px; flex-shrink:0;
}
.bxr-sum-card.sc-p .bxr-sum-icon{ background:#DDD6FE; color:#6D28D9; }
.bxr-sum-card.sc-b .bxr-sum-icon{ background:#BFDBFE; color:#1D4ED8; }
.bxr-sum-card.sc-o .bxr-sum-icon{ background:#FED7AA; color:#C2410C; }

/* Text */
.bxr-sum-label{
    font-size:12px; font-weight:600;
    color:#64748B; margin-bottom:4px; line-height:1.2;
}
.bxr-sum-num{
    font-size:34px; font-weight:800;
    color:#0F172A; line-height:1; margin-bottom:6px;
}
.bxr-sum-growth{
    font-size:12px; font-weight:600; color:#16A34A;
    display:flex; align-items:center; gap:3px;
}

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

/* ══ 4. STAGE NAV ═══════════════════════════════ */
.bxr-stages-wrap{
    background:#fff;
    border-bottom:1.5px solid #E8ECF2;
    margin-bottom:16px;
    overflow-x:auto;
}
.bxr-stages{
    display:flex; align-items:center;
    width:100%;
    min-width:max-content;
    padding:0;
    height:72px;
}

/* ── Each tab ─────────────────────────────────── */
.bxr-stab{
    display:flex; flex-direction:row;
    align-items:center; justify-content:center;
    flex:1;                        /* fill remaining width evenly */
    min-width:110px;               /* don't shrink below this */
    height:100%;
    padding:0 12px;
    cursor:pointer;
    transition:background .13s;
    position:relative;
    user-select:none;
    gap:10px;
    border-bottom:3px solid transparent;
    margin-bottom:-1.5px;
}
/* divider line between every tab */
.bxr-stab + .bxr-stab::before{
    content:''; position:absolute; left:0; top:18%; height:64%;
    width:1px; background:#E8ECF2;
}
.bxr-stab:not(.bxr-active):hover{ background:#F5F8FF; }

/* ── ACTIVE pill ──────────────────────────────── */
.bxr-stab.bxr-active{ border-bottom:3px solid #2563EB; flex:0 0 auto; padding:0 20px; min-width:auto; }
.bxr-stab.bxr-active .bxr-stab-pill{
    display:inline-flex; align-items:center; gap:7px;
    background:#2563EB; color:#fff;
    padding:9px 20px; border-radius:10px;
    font-size:14px; font-weight:700; line-height:1;
    white-space:nowrap;
}
.bxr-stab.bxr-active .bxr-stab-icon-box,
.bxr-stab.bxr-active .bxr-stab-right{ display:none !important; }

/* ── INACTIVE tab ─────────────────────────────── */
.bxr-stab:not(.bxr-active) .bxr-stab-pill{ display:none !important; }

/* Colored icon box */
.bxr-stab-icon-box{
    display:none;                   /* shown via :not(.bxr-active) below */
    width:36px; height:36px;
    border-radius:10px;
    align-items:center; justify-content:center;
    font-size:18px; flex-shrink:0;
}
.bxr-stab:not(.bxr-active) .bxr-stab-icon-box{ display:flex; }

/* Right column */
.bxr-stab-right{
    display:none;
    flex-direction:column; align-items:flex-start; gap:2px;
}
.bxr-stab:not(.bxr-active) .bxr-stab-right{ display:flex; }

.bxr-stab-name{
    font-size:11px; font-weight:600; color:#64748B;
    white-space:nowrap; line-height:1;
}
.bxr-stab-cnt-plain{
    font-size:17px; font-weight:800; color:#0F172A;
    line-height:1.1;
}

/* ── Per-stage icon-box colours ───────────────── */
.bxr-stab[rel="1"] .bxr-stab-icon-box{ background:#EEEBFF; color:#7C3AED; }  /* Sourcing    purple  */
.bxr-stab[rel="2"] .bxr-stab-icon-box{ background:#E0F2FE; color:#0284C7; }  /* Screening   sky     */
.bxr-stab[rel="3"] .bxr-stab-icon-box{ background:#E0F2FE; color:#0EA5E9; }  /* Evaluation  sky     */
.bxr-stab[rel="4"] .bxr-stab-icon-box{ background:#FEF3C7; color:#D97706; }  /* Negotiation amber   */
.bxr-stab[rel="5"] .bxr-stab-icon-box{ background:#D1FAE5; color:#059669; }  /* Offer       green   */
.bxr-stab[rel="6"] .bxr-stab-icon-box{ background:#DCFCE7; color:#16A34A; }  /* Joined      green   */
.bxr-stab[rel="7"] .bxr-stab-icon-box{ background:#FFE4E6; color:#E11D48; }  /* Rejected    rose    */

/* ══ 5. TABLE AREA ══════════════════════════════ */
.bxr-tbl-wrap{
    background:#fff;
    border-radius:0;
}
.bxr-tbl-header{
    padding:16px 24px 14px;
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px;
    border-bottom:1px solid #F0F2F5;
}
.bxr-tbl-title{ font-size:14px; font-weight:700; color:#1A1D23; }
.bxr-tbl-count{ font-size:14px; font-weight:400; color:#64748B; }
.bxr-tbl-loading{
    display:none; align-items:center; gap:8px;
    font-size:12px; color:#94A3B8;
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
    .bxr-hero{ flex-direction:column; }
    .bxr-kpi-row{ flex-wrap:wrap; width:100%; }
    .bxr-kpi+.bxr-kpi::before{ display:none; }
    .bxr-sum-row{ flex-direction:column; }
    .bxr-sum-card{ border-right:none; }
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
.bxr-filter-btn{
    height:44px; padding:0 16px; border:1.5px solid #E5E9F0; border-radius:10px;
    background:#fff; color:#374151; font-size:13px; font-weight:600;
    display:flex; align-items:center; gap:8px; position:relative; flex-shrink:0;
}
.bxr-filter-btn:hover{ border-color:#2563EB; color:#2563EB; }
.bxr-count-badge{ font-size:10px; }

/* ══ 7. APPLIED FILTERS CARD ═════════════════════════════════ */
.bxr-applied-card{
    border:1px solid #eef0f4; border-radius:14px; background:#fff;
    padding:16px 18px; margin:0 0 16px; font-family:'Poppins',sans-serif;
}
.bxr-applied-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; flex-wrap:wrap; gap:8px; }
.bxr-applied-title{ display:flex; align-items:center; gap:8px; font-size:13.5px; font-weight:700; color:#181c32; }
.bxr-applied-count{
    background:#2563eb; color:#fff; font-size:11px; font-weight:700; border-radius:50%;
    min-width:19px; height:19px; display:inline-flex; align-items:center; justify-content:center; padding:0 4px;
}
.bxr-applied-clearall{ font-size:12px; font-weight:500; color:#dc2626; display:inline-flex; align-items:center; gap:4px; text-decoration:none; }
.bxr-applied-clearall:hover{ color:#b91c1c; }
.bxr-applied-chips{ display:flex; flex-wrap:wrap; gap:8px; }
.bxr-applied-chip{
    display:inline-flex; align-items:center; gap:6px; border-radius:8px; padding:6px 10px;
    font-size:12px; font-weight:600; border:1px solid transparent; white-space:nowrap;
}
.bxr-applied-chip .ic{ display:inline-flex; align-items:center; flex-shrink:0; }
.bxr-applied-chip-remove{ cursor:pointer; opacity:.55; display:inline-flex; align-items:center; margin-left:2px; }
.bxr-applied-chip-remove:hover{ opacity:1; }
.bxr-applied-chip.cat-company{ background:#eff6ff; color:#1d4ed8; border-color:#dbeafe; }
.bxr-applied-chip.cat-location{ background:#ecfdf5; color:#059669; border-color:#d1fae5; }
.bxr-applied-chip.cat-salary{ background:#fffbeb; color:#b45309; border-color:#fde68a; }
.bxr-applied-chip.cat-status{ background:#f5f3ff; color:#7c3aed; border-color:#ede9fe; }
.bxr-applied-chip.cat-activity{ background:#fdf2f8; color:#be185d; border-color:#fbcfe8; }
.bxr-applied-more{ font-size:12px; font-weight:600; color:#2563eb; cursor:pointer; padding:6px 4px; }

/* ══ 8. FILTER DRAWER (bxr-, adapted from dsf-/dib-) ═════════ */
.bxr-drawer{ background:#fff; }
.bxr-card{ display:flex; flex-direction:column; }
.bxr-header{
    display:flex; align-items:center; justify-content:space-between;
    padding:20px 24px; border-bottom:1px solid #F0F2F5; flex-shrink:0;
}
.bxr-header-icon{
    width:40px; height:40px; border-radius:10px; background:#EFF6FF; color:#2563EB;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.bxr-title{ font-size:16px; font-weight:700; color:#181c32; margin:0; }
.bxr-count-pill{
    background:#2563eb; color:#fff; font-size:11px; font-weight:700; border-radius:20px;
    padding:2px 9px; min-width:19px; text-align:center;
}
.bxr-subtitle{ font-size:12px; color:#94A3B8; margin:2px 0 0; }
.bxr-close{
    width:34px; height:34px; border-radius:9px; border:1px solid #E5E9F0; background:#fff;
    color:#64748B; display:flex; align-items:center; justify-content:center; cursor:pointer;
}
.bxr-close:hover{ background:#F8FAFC; }
.bxr-body{ padding:0 24px; }
.bxr-section{ padding:18px 0; border-bottom:1px solid #F5F7FA; }
.bxr-section-last{ border-bottom:none; }
.bxr-section-head{ display:flex; align-items:center; gap:12px; margin-bottom:12px; }
.bxr-icon{
    width:32px; height:32px; border-radius:9px; display:flex; align-items:center;
    justify-content:center; flex-shrink:0;
}
.bxr-icon-blue{ background:#EFF6FF; color:#2563EB; }
.bxr-icon-green{ background:#ECFDF5; color:#059669; }
.bxr-icon-amber{ background:#FFFBEB; color:#B45309; }
.bxr-icon-purple{ background:#F5F3FF; color:#7C3AED; }
.bxr-icon-rose{ background:#FDF2F8; color:#BE185D; }
.bxr-section-title{ font-size:13.5px; font-weight:700; color:#181c32; }
.bxr-section-desc{ font-size:11.5px; color:#94A3B8; margin-top:1px; }
.bxr-mini-label{ font-size:11px; font-weight:600; color:#64748B; margin-bottom:4px; display:block; }
.bxr-salary-row .form-select, .bxr-salary-row .form-control{ font-size:13px; }

/* Active-filters chip bar inside the drawer body */
.bxr-activebar{
    background:#F8FAFC; border:1px solid #EEF0F4; border-radius:12px;
    padding:12px 14px; margin:16px 0;
}
.bxr-activebar-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
.bxr-activebar-label{ font-size:12px; font-weight:700; color:#374151; }
.bxr-clearall{ font-size:11.5px; font-weight:600; color:#dc2626; display:inline-flex; align-items:center; gap:4px; text-decoration:none; }
.bxr-clearall:hover{ color:#b91c1c; }
.bxr-chips{ display:flex; flex-wrap:wrap; gap:7px; }
.bxr-chip{
    display:inline-flex; align-items:center; gap:5px; background:#fff; border:1px solid #E5E9F0;
    border-radius:7px; padding:5px 9px; font-size:11.5px; font-weight:600; color:#374151;
}
.bxr-chip-remove{ cursor:pointer; opacity:.55; }
.bxr-chip-remove:hover{ opacity:1; }

/* Pill-style radio groups (Status / Activity) */
.bxr-pill-row{ display:flex; flex-wrap:wrap; gap:8px; }
.bxr-pill-row .btn-check{ position:absolute; opacity:0; pointer-events:none; }
.bxr-pill-row label{ margin:0; cursor:pointer; min-width:88px; }

.bxr-footer{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    padding:16px 24px; border-top:1px solid #F0F2F5; flex-shrink:0; background:#fff;
}
.bxr-footer-count{ display:flex; align-items:center; gap:10px; }
.bxr-footer-icon{
    width:38px; height:38px; border-radius:10px; background:#EFF6FF; color:#2563EB;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.bxr-footer-number{ font-size:16px; font-weight:800; color:#181c32; line-height:1.1; }
.bxr-footer-label{ font-size:11px; color:#94A3B8; }
.bxr-footer-actions{ display:flex; gap:8px; }
</style>
@endpush

@section('content')

{{-- ── Hidden compat layer: keeps all original IDs alive ─────── --}}
<div aria-hidden="true"
     style="position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;opacity:0;z-index:-9;">
    @php
        $bench_explore_url      = url('service_company/bench-candidates/bench_explore/')          . @$bench_detail->id . '/all';
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

  {{-- ══ SECTION 1 · PROFILE HERO ══════════════════════ --}}
  <div class="bxr-hero">
    {{-- Left: avatar + details --}}
    <div class="bxr-hero-left">
      @php
        $imgFile  = @$bench_detail->profile_image ?? @$bench_detail->image ?? @$bench_detail->photo ?? null;
        $candName = @$bench_detail->name ?? 'Candidate';
        $initLtr  = strtoupper(substr($candName,0,1));
      @endphp
      <div class="bxr-av-wrap">
        @if($imgFile)
          <img src="{{ asset('assets/images/candidates/'.$imgFile) }}" class="bxr-avatar" alt="{{ $candName }}">
        @else
          <div class="bxr-avatar-init">{{ $initLtr }}</div>
        @endif
        <div class="bxr-online-dot"></div>
      </div>
      <div class="bxr-info">
        {{-- Row 1: Name + Bench badge --}}
        <div class="bxr-name-row">
          <span class="bxr-name">{{ $candName }}</span>
          <span class="bxr-bench-tag"><span class="bxr-bench-dot"></span>Bench</span>
        </div>

        {{-- Row 2: Role  •  Location --}}
        <div class="bxr-info-row">
          <span class="bxr-role">{{ @$bench_detail->current_designation ?? '' }}</span>
          @if(@$bench_detail->city_name||@$bench_detail->city||@$bench_detail->location)
          <span class="bxr-meta-item">
            <i class="ti ti-map-pin"></i>
            {{ @$bench_detail->city_name ?? @$bench_detail->city ?? @$bench_detail->location }}@if(@$bench_detail->state_name), {{ @$bench_detail->state_name }}@endif
          </span>
          @endif
        </div>

        {{-- Row 3: Experience  •  Salary  •  Bench ID --}}
        <div class="bxr-meta-line">
          @if(@$bench_detail->experience||@$bench_detail->total_exp)
          <span class="bxr-meta-item">
            <i class="ti ti-clock"></i>
            {{ @$bench_detail->experience ?? @$bench_detail->total_exp }} Years
          </span>
          @endif
          @if(@$bench_detail->expected_salary||@$bench_detail->salary||@$bench_detail->ctc)
          <span class="bxr-meta-item">
            <i class="ti ti-currency-rupee"></i>
            {{ number_format(@$bench_detail->expected_salary ?? @$bench_detail->salary ?? @$bench_detail->ctc) }} / Year
          </span>
          @endif
          <span class="bxr-meta-item">
            <span style="color:#94A3B8;font-size:12px;">Bench ID:</span>&nbsp;
            <span class="bxr-bench-id-val">{{ @$bench_detail->bench_unique_id ?? ('B-'.str_pad(@$bench_detail->id??0,5,'0',STR_PAD_LEFT)) }}</span>
          </span>
        </div>
      </div>
    </div>

    {{-- Right: KPIs + menu --}}
    <div style="display:flex;align-items:center;gap:12px;">
      <div class="bxr-kpi-row">
        <div class="bxr-kpi">
          <div class="bxr-kpi-icon vi"><i class="ti ti-eye"></i></div>
          <div class="bxr-kpi-body">
            <div class="bxr-kpi-num">{{ @$bench_detail->total_views??@$bench_detail->views??0 }}</div>
            <div class="bxr-kpi-lbl">Views</div>
          </div>
        </div>
        <div class="bxr-kpi">
          <div class="bxr-kpi-icon dl"><i class="ti ti-download"></i></div>
          <div class="bxr-kpi-body">
            <div class="bxr-kpi-num">{{ @$bench_detail->total_downloads??@$bench_detail->downloads??0 }}</div>
            <div class="bxr-kpi-lbl">Downloads</div>
          </div>
        </div>
        <div class="bxr-kpi">
          <div class="bxr-kpi-icon aj"><i class="ti ti-send"></i></div>
          <div class="bxr-kpi-body">
            <div class="bxr-kpi-num">{{ @$recomCount['recommend_req_counter']??0 }}</div>
            <div class="bxr-kpi-lbl">Applied on Jobs</div>
          </div>
        </div>
        <div class="bxr-kpi">
          <div class="bxr-kpi-icon iv"><i class="ti ti-calendar-event"></i></div>
          <div class="bxr-kpi-body">
            <div class="bxr-kpi-num">{{ @$bench_detail->total_interviews??@$bench_detail->interviews??0 }}</div>
            <div class="bxr-kpi-lbl">Interviews</div>
          </div>
        </div>
      </div>
      <button class="bxr-hero-menu"><i class="ti ti-dots-vertical"></i></button>
    </div>
  </div>

  {{-- ══ SECTION 2 · SUMMARY CARDS ════════════════════ --}}
  <div class="bxr-sum-row">
    {{-- Card 1: purple --}}
    <div class="bxr-sum-card sc-p">
      <div class="bxr-sum-icon"><i class="ti ti-users-group"></i></div>
      <div>
        <div class="bxr-sum-label">Total Companies Added</div>
        <div class="bxr-sum-num bxr-live">{{ $recomCount['bench_explore_counter'] }}</div>
        <div class="bxr-sum-growth">
          <i class="ti ti-arrow-up" style="font-size:11px;"></i> 1 new today
        </div>
      </div>
    </div>
    {{-- Card 2: blue --}}
    <div class="bxr-sum-card sc-b">
      <div class="bxr-sum-icon"><i class="ti ti-briefcase"></i></div>
      <div>
        <div class="bxr-sum-label">Recommended Jobs</div>
        <div class="bxr-sum-num">{{ $recomCount['recommend_req_counter'] }}</div>
        <div class="bxr-sum-growth">
          <i class="ti ti-arrow-up" style="font-size:11px;"></i> 5 new today
        </div>
      </div>
    </div>
    {{-- Card 3: orange --}}
    <div class="bxr-sum-card sc-o">
      <div class="bxr-sum-icon"><i class="ti ti-building-skyscraper"></i></div>
      <div>
        <div class="bxr-sum-label">Recommended Internal Jobs</div>
        <div class="bxr-sum-num">{{ $recomCount['recommend_internal_req_counter'] }}</div>
        <div class="bxr-sum-growth">
          <i class="ti ti-arrow-up" style="font-size:11px;"></i> 2 new today
        </div>
      </div>
    </div>
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

    <button type="button" id="kt_bxr_filter_toggle" class="btn bxr-filter-btn position-relative">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
      <span>Filter<span id="bxr_filter_count_text"></span></span>
      <span class="badge badge-circle badge-primary bxr-count-badge d-none" id="bxr_count_badge" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
    </button>

    <button class="bxr-refresh" onclick="fetchBenchExploreJobCandidateList()" title="Refresh">
      <i class="ti ti-refresh"></i>
    </button>
  </div>

  {{-- ══ Applied Filters summary card — hidden until Apply is clicked ══ --}}
  <div class="bxr-applied-card d-none" id="bxr_applied_card" style="margin:0 0 4px;">
    <div class="bxr-applied-head">
      <span class="bxr-applied-title">
        Active Filters
        <span class="bxr-applied-count" id="bxr_applied_count">0</span>
      </span>
      <a href="javascript:void(0);" class="bxr-applied-clearall" id="bxr_applied_clear_all">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
        Clear All
      </a>
    </div>
    <div class="bxr-applied-chips" id="bxr_applied_chips"></div>
  </div>

  {{-- ══ Filter Drawer ══════════════════════════════════════════ --}}
  <div id="kt_bxr_filter_drawer" class="bg-body bxr-drawer" data-kt-drawer="true" data-kt-drawer-name="bxr-filter"
      data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
      data-kt-drawer-width="{default:'92%', 'sm': '80%'}" data-kt-drawer-direction="end"
      data-kt-drawer-toggle="#kt_bxr_filter_toggle" data-kt-drawer-close="#kt_bxr_filter_close">
    <div id="bxr_card" class="card shadow-none rounded-0 w-100 h-100 bxr-card">
      <div class="bxr-header">
        <div class="d-flex align-items-center gap-3">
          <span class="bxr-header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
          </span>
          <div>
            <div class="d-flex align-items-center gap-2">
              <h3 class="bxr-title">Filter Companies</h3>
              <span class="bxr-count-pill" id="bxr_header_count">0</span>
            </div>
            <p class="bxr-subtitle">Narrow down the companies processing this candidate</p>
          </div>
        </div>
        <button type="button" class="bxr-close" id="kt_bxr_filter_close" title="Close">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="bxr-body">
        <form id="bxrFilterForm" class="ajax-form" method="post" onsubmit="return false;">

          <div class="bxr-activebar d-none" id="bxr_active_bar">
            <div class="bxr-activebar-head">
              <span class="bxr-activebar-label">Active Filters <span id="bxr_active_count">(0)</span></span>
              <a href="javascript:void(0);" class="bxr-clearall" id="bxr_clear_all">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m3 0-1 14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1L4 6"/></svg>
                Clear All
              </a>
            </div>
            <div class="bxr-chips" id="bxr_active_chips"></div>
          </div>

          {{-- Company / Client Name --}}
          <div class="bxr-section">
            <div class="bxr-section-head">
              <span class="bxr-icon bxr-icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1"/></svg>
              </span>
              <div>
                <div class="bxr-section-title">Company / Client Name</div>
                <div class="bxr-section-desc">Which companies is this candidate submitted to</div>
              </div>
            </div>
            <input class="form-control form-control-solid" id="kt_tagify_bxr_company"
                placeholder="Search company or client name" />
          </div>

          {{-- Job Location --}}
          <div class="bxr-section">
            <div class="bxr-section-head">
              <span class="bxr-icon bxr-icon-green">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
              </span>
              <div>
                <div class="bxr-section-title">Job Location</div>
                <div class="bxr-section-desc">City or work location of the job</div>
              </div>
            </div>
            <input class="form-control form-control-solid" id="kt_tagify_bxr_location"
                placeholder="Search location (e.g. Bangalore, Pune, Remote)" />
          </div>

          {{-- Salary / Bill Rate --}}
          <div class="bxr-section">
            <div class="bxr-section-head">
              <span class="bxr-icon bxr-icon-amber">
                <span style="font-size:15px;font-weight:700;line-height:1;">₹</span>
              </span>
              <div>
                <div class="bxr-section-title">Salary / Bill Rate</div>
                <div class="bxr-section-desc">Offered salary range for the job</div>
              </div>
            </div>
            <div class="row g-2 bxr-salary-row">
              <div class="col-3">
                <label class="bxr-mini-label">Currency</label>
                <select class="form-select form-select-solid" id="bxr_min_salary_currency">
                  <option value="">Any</option>
                  <option value="0">INR</option>
                  <option value="1">EUR</option>
                  <option value="2">JPY</option>
                  <option value="3">GBP</option>
                  <option value="4">USD</option>
                  <option value="5">AED</option>
                </select>
              </div>
              <div class="col-3">
                <label class="bxr-mini-label">Mode</label>
                <select class="form-select form-select-solid" id="bxr_sal_mode">
                  <option value="">Any</option>
                  <option value="0">Annual</option>
                  <option value="1">Monthly</option>
                  <option value="2">Hourly</option>
                </select>
              </div>
              <div class="col-3">
                <label class="bxr-mini-label">Min</label>
                <input type="number" class="form-control form-control-solid" id="bxr_min_sal" placeholder="Any">
              </div>
              <div class="col-3">
                <label class="bxr-mini-label">Max</label>
                <input type="number" class="form-control form-control-solid" id="bxr_max_sal" placeholder="Any">
              </div>
            </div>
          </div>

          {{-- Status --}}
          <div class="bxr-section">
            <div class="bxr-section-head">
              <span class="bxr-icon bxr-icon-purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 8v8M8 6h4a4 4 0 0 1 4 4M8 18h4a4 4 0 0 0 4-4"/></svg>
              </span>
              <div>
                <div class="bxr-section-title">Status</div>
                <div class="bxr-section-desc">Job's current status</div>
              </div>
            </div>
            <div class="bxr-pill-row" data-radio-group="bxr_status">
              <div>
                <input type="radio" class="btn-check bxr_status" name="bxr_status" value="all" id="bxr_status_all">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_status_all">All</label>
              </div>
              <div>
                <input type="radio" class="btn-check bxr_status" name="bxr_status" value="active" id="bxr_status_active" checked>
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_status_active">Active</label>
              </div>
              <div>
                <input type="radio" class="btn-check bxr_status" name="bxr_status" value="hold" id="bxr_status_hold">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_status_hold">Hold</label>
              </div>
              <div>
                <input type="radio" class="btn-check bxr_status" name="bxr_status" value="inactive" id="bxr_status_inactive">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_status_inactive">Closed</label>
              </div>
              <div>
                <input type="radio" class="btn-check bxr_status" name="bxr_status" value="removed" id="bxr_status_removed">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_status_removed">Removed</label>
              </div>
            </div>
          </div>

          {{-- Activity --}}
          <div class="bxr-section bxr-section-last">
            <div class="bxr-section-head">
              <span class="bxr-icon bxr-icon-rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 14l4-4 3 3 5-6"/></svg>
              </span>
              <div>
                <div class="bxr-section-title">Activity</div>
                <div class="bxr-section-desc">How recently the stage last changed</div>
              </div>
            </div>
            <div class="bxr-pill-row" data-radio-group="bxr_activity">
              <div>
                <input type="radio" class="btn-check bxr_activity" name="bxr_activity" value="1" id="bxr_activity1" checked>
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_activity1">All</label>
              </div>
              <div>
                <input type="radio" class="btn-check bxr_activity" name="bxr_activity" value="2" id="bxr_activity2">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_activity2">30 Days</label>
              </div>
              <div>
                <input type="radio" class="btn-check bxr_activity" name="bxr_activity" value="3" id="bxr_activity3">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_activity3">60 Days</label>
              </div>
              <div>
                <input type="radio" class="btn-check bxr_activity" name="bxr_activity" value="4" id="bxr_activity4">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_activity4">90 Days</label>
              </div>
              <div>
                <input type="radio" class="btn-check bxr_activity" name="bxr_activity" value="5" id="bxr_activity5">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_activity5">180 Days</label>
              </div>
              <div>
                <input type="radio" class="btn-check bxr_activity" name="bxr_activity" value="6" id="bxr_activity6">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3" for="bxr_activity6">1 Year</label>
              </div>
            </div>
          </div>

        </form>
      </div>

      <div class="bxr-footer">
        <div class="bxr-footer-count">
          <span class="bxr-footer-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </span>
          <div>
            <div class="bxr-footer-number" id="bxr_footer_number">{{ $recomCount['bench_explore_counter'] }}</div>
            <div class="bxr-footer-label">Companies Match</div>
          </div>
        </div>
        <div class="bxr-footer-actions">
          <button type="button" class="btn btn-sm btn-light" id="bxr_close_btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            Close
          </button>
          <button type="button" class="btn btn-sm btn-light" id="bxr_reset_btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
            Reset
          </button>
          <button type="button" class="btn btn-sm btn-primary" id="bxr_apply_btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
            Apply Filters
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ SECTION 4 · STAGE NAVIGATION ═════════════════ --}}
  <div class="bxr-stages-wrap">
    <div class="bxr-stages" id="bxr-stages-row">

      {{-- ALL — active default --}}
      <div class="bxr-stab application_sttus_nav bxr-active" rel="all">
        <div class="bxr-stab-pill">All <span id="totalcnt">0</span></div>
        <div class="bxr-stab-icon-box" style="background:#EFF6FF;color:#2563EB;"><i class="ti ti-layout-grid"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">All</div>
          <div class="bxr-stab-cnt-plain" id="totalcnt_plain">0</div>
        </div>
      </div>

      {{-- SOURCING --}}
      <div class="bxr-stab application_sttus_nav" rel="1">
        <div class="bxr-stab-pill">Sourcing <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-users-group"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Sourcing</div>
          <div class="bxr-stab-cnt-plain" id="bg_1">0</div>
        </div>
      </div>

      {{-- SCREENING --}}
      <div class="bxr-stab application_sttus_nav" rel="2">
        <div class="bxr-stab-pill">Screening <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-clipboard-list"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Screening</div>
          <div class="bxr-stab-cnt-plain" id="bg_2">0</div>
        </div>
      </div>

      {{-- EVALUATION --}}
      <div class="bxr-stab application_sttus_nav" rel="3">
        <div class="bxr-stab-pill">Evaluation <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-search"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Evaluation</div>
          <div class="bxr-stab-cnt-plain" id="bg_3">0</div>
        </div>
      </div>

      {{-- NEGOTIATION --}}
      <div class="bxr-stab application_sttus_nav" rel="4">
        <div class="bxr-stab-pill">Negotiation <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-3d-cube-sphere"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Negotiation</div>
          <div class="bxr-stab-cnt-plain" id="bg_4">0</div>
        </div>
      </div>

      {{-- OFFER --}}
      <div class="bxr-stab application_sttus_nav" rel="5">
        <div class="bxr-stab-pill">Offer <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-briefcase"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Offer</div>
          <div class="bxr-stab-cnt-plain" id="bg_5">0</div>
        </div>
      </div>

      {{-- JOINED --}}
      <div class="bxr-stab application_sttus_nav" rel="6">
        <div class="bxr-stab-pill">Joined <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-circle-check"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Joined</div>
          <div class="bxr-stab-cnt-plain" id="bg_6">0</div>
        </div>
      </div>

      {{-- REJECTED --}}
      <div class="bxr-stab application_sttus_nav" rel="7">
        <div class="bxr-stab-pill">Rejected <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-circle-x"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Rejected</div>
          <div class="bxr-stab-cnt-plain" id="bg_7">0</div>
        </div>
      </div>

    </div>{{-- /bxr-stages --}}
  </div>{{-- /bxr-stages-wrap --}}

  {{-- ══ SECTION 5 · TABLE ══════════════════════════════ --}}
  <div class="bxr-tbl-wrap">
    <div class="bxr-tbl-header">
      <span class="bxr-tbl-title">
        Companies Processing This Candidate
        <span class="bxr-tbl-count bxr-live">({{ $recomCount['bench_explore_counter'] }})</span>
      </span>
      <div class="bxr-tbl-loading loadingMessagebenchCandidateExploreJobAjax" style="display:none;">
        <span class="spinner-border spinner-border-sm text-primary" style="width:13px;height:13px;border-width:2px;"></span>
        Processing...
      </div>
    </div>

    <div class="bxr-tbl-scroll">
      <table id="bench_explore_listing" class="bxr-tbl table-custom-class">
        <thead>
          <tr>
            <th style="width:40px;">
              <div class="form-check form-check-sm form-check-custom form-check-solid ms-2">
                <input class="form-check-input" type="checkbox" value="1">
              </div>
            </th>
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
     data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
     data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle"
     data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>

@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>

{{-- ── Stage tab active-state visual (new design) ── --}}
<script>
/* Keep pill spans in sync with the plain-count spans updated by AJAX */
function syncPillCounts(){
    /* "All" tab: totalcnt is already the #totalcnt span inside the pill */
    var allVal=$('#totalcnt').text();
    $('#totalcnt_plain').text(allVal);

    /* Stage tabs 1–7 */
    [1,2,3,4,5,6,7].forEach(function(n){
        var val=$('#bg_'+n).text();
        /* pill span is the last <span> child of .bxr-stab-pill in that tab */
        $('.bxr-stab[rel="'+n+'"] .bxr-stab-pill span:last').text(val);
    });
}
$(document).ready(function(){
    var init=$('#stage').val()||'all';
    $('.bxr-stab').removeClass('bxr-active');
    $('.bxr-stab[rel="'+init+'"]').addClass('bxr-active');
});
$(document).on('click','.bxr-stab',function(){
    $('.bxr-stab').removeClass('bxr-active');
    $(this).addClass('bxr-active');
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

{{-- ── Search / AJAX / counters / drawer ── --}}
<script>
// Enter-key re-runs the same fetch the sort dropdown/pagination already use —
// no separate endpoint, and no auto-apply on every keystroke.
$(document).on('keydown', '#kt_tagify_2', function(e){
    if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); fetchBenchExploreJobCandidateList(); }
});
var table;
function filtersdataget(){ fetchBenchExploreJobCandidateList(); }

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
            /* keep plain count spans in sync too */
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
// Pagination clicks were dead: the shared pagination partial renders
// .pagi-page/.pagi-arrow, not the old Bootstrap .page-link this handler used
// to listen for. A boolean guard replaces the old (nonexistent) .disabled
// class toggle so rapid clicks can't fire overlapping requests.
var bxrPaginationLoading = false;
$(document).ready(function(){
    fetchBenchExploreJobCandidateList();
    $(document).on('click','#paginationbenchCandidateExploreJob .pagi-page, #paginationbenchCandidateExploreJob .pagi-arrow',function(e){
        e.preventDefault();
        var $btn=$(this);
        if (bxrPaginationLoading || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled')) return;
        var pg=$btn.data('page');
        if(!pg) return;
        bxrPaginationLoading = true;
        fetchBenchExploreJobCandidateList(pg)
            .then(function(){ bxrPaginationLoading = false; })
            .catch(function(){ bxrPaginationLoading = false; });
    });
});
function fetchBenchExploreJobCandidateList(page){
    page=page||1;
    var bid=$('#bench_id').val(),stage=$('#stage').val(),
        sort=$('#hiringtype').val(),cs=$('#kt_tagify_2').val(),
        st=$('[name="bxr_status"]:checked').val()||'active',
        activity=$('[name="bxr_activity"]:checked').val()||'1',
        company=(typeof bxrTagifyCompany!=='undefined')?JSON.stringify(bxrTagifyCompany.value):'[]',
        location=(typeof bxrTagifyLocation!=='undefined')?JSON.stringify(bxrTagifyLocation.value):'[]',
        minSal=$('#bxr_min_sal').val(),maxSal=$('#bxr_max_sal').val();
    $('.loadingMessagebenchCandidateExploreJobAjax').css({display:'flex'});
    return $.ajax({
        url:"{!! route('service_company.bench-candidates.bench_explore_listing_job_ajax') !!}?page="+page,
        type:'GET',
        data:{
            bench_id:bid,stage:stage,cus_search:cs,statusfilter:st,sortingfilter:sort,
            bxr_company:company,bxr_location:location,
            bxr_min_sal:minSal,bxr_max_sal:maxSal,bxr_activity:activity
        },
        success:function(r){
            $('.loadingMessagebenchCandidateExploreJobAjax').css({display:'none'});
            $('#benchCandidateExploreJobAjax').html(r.html);
            $('#bench_explore_pool_id').html(r.total);
            $('.bxr-live').html(r.total);
            $('#bxr_footer_number').text(r.total);
            $('#paginationbenchCandidateExploreJob').html(r.pagination);
            tableLoad();
        }
    });
}
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

/* ── Drawer width + viewport-bound layout (same measurement approach as
   Similar Candidates'/In-house Bench's filter drawer) ── */
function bxrSetDrawerWidth(){
    var wrapper=document.getElementById('kt_wrapper');
    var contentWidth=wrapper?wrapper.getBoundingClientRect().width:window.innerWidth;
    if(!contentWidth) contentWidth=window.innerWidth;
    var drawerEl=document.getElementById('kt_bxr_filter_drawer');
    if(drawerEl) drawerEl.style.width=Math.round(contentWidth*0.8)+'px';
}
$(document).ready(bxrSetDrawerWidth);
$(window).on('resize', bxrSetDrawerWidth);
$(document).on('click','#kt_bxr_filter_toggle', bxrSetDrawerWidth);

function bxrEnforceDrawerLayout(){
    var drawerEl=document.getElementById('kt_bxr_filter_drawer');
    var cardEl=document.getElementById('bxr_card');
    var bodyEl=cardEl?cardEl.querySelector('.bxr-body'):null;
    var headerEl=cardEl?cardEl.querySelector('.bxr-header'):null;
    var footerEl=cardEl?cardEl.querySelector('.bxr-footer'):null;
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
$(document).ready(bxrEnforceDrawerLayout);
$(window).on('resize', bxrEnforceDrawerLayout);
$(document).on('click','#kt_bxr_filter_toggle, #kt_bxr_filter_close', bxrEnforceDrawerLayout);

/* ── Tagify: Company/Client Name + Job Location (plain multi-tag, no
   whitelist/suggestions — these match ES text fields directly) ── */
var bxrTagifyCompany, bxrTagifyLocation;
$(document).ready(function(){
    var elCompany=document.querySelector('#kt_tagify_bxr_company');
    if (elCompany && typeof Tagify!=='undefined'){
        bxrTagifyCompany=new Tagify(elCompany,{maxTags:10,dropdown:{enabled:0}});
        bxrTagifyCompany.on('add', bxrUpdateActiveFiltersUI);
        bxrTagifyCompany.on('remove', bxrUpdateActiveFiltersUI);
    }
    var elLocation=document.querySelector('#kt_tagify_bxr_location');
    if (elLocation && typeof Tagify!=='undefined'){
        bxrTagifyLocation=new Tagify(elLocation,{maxTags:10,dropdown:{enabled:0}});
        bxrTagifyLocation.on('add', bxrUpdateActiveFiltersUI);
        bxrTagifyLocation.on('remove', bxrUpdateActiveFiltersUI);
    }
});

function bxrPillLabel($checkedInput){
    return $.trim($checkedInput.closest('div').next('label').text());
}

/* ── In-drawer Active Filters chip bar (draft, live on every field change) ── */
function bxrUpdateActiveFiltersUI(){
    var chips=[];
    if (typeof bxrTagifyCompany!=='undefined') bxrTagifyCompany.value.forEach(function(t){
        chips.push({label:t.value, clear:function(){ bxrTagifyCompany.removeTags(t.value); }});
    });
    if (typeof bxrTagifyLocation!=='undefined') bxrTagifyLocation.value.forEach(function(t){
        chips.push({label:t.value, clear:function(){ bxrTagifyLocation.removeTags(t.value); }});
    });
    var minSal=$('#bxr_min_sal').val(), maxSal=$('#bxr_max_sal').val();
    if (minSal || maxSal){
        chips.push({label:(minSal||'0')+'-'+(maxSal||'Any'), clear:function(){ $('#bxr_min_sal,#bxr_max_sal').val(''); }});
    }
    var statusEl=$('[name="bxr_status"]:checked');
    if (statusEl.val() && statusEl.val()!=='active'){
        chips.push({label:bxrPillLabel(statusEl), clear:function(){ $('#bxr_status_active').prop('checked',true); }});
    }
    var activityEl=$('[name="bxr_activity"]:checked');
    if (activityEl.val() && activityEl.val()!=='1'){
        chips.push({label:bxrPillLabel(activityEl), clear:function(){ $('#bxr_activity1').prop('checked',true); }});
    }

    var $chips=$('#bxr_active_chips').empty();
    chips.forEach(function(c){
        $chips.append(
            $('<span class="bxr-chip"></span>').append($('<span></span>').text(c.label))
                .append($('<span class="bxr-chip-remove">&times;</span>').on('click',function(){ c.clear(); bxrUpdateActiveFiltersUI(); }))
        );
    });
    var n=chips.length;
    $('#bxr_active_bar').toggleClass('d-none', n===0);
    $('#bxr_active_count').text('('+n+')');
    $('#bxr_header_count').text(n);
    $('#bxr_count_badge').toggleClass('d-none', n===0).text(n);
    $('#bxr_filter_count_text').text(n>0?' ('+n+')':'');
}
$(document).on('change keyup','#bxr_min_sal, #bxr_max_sal', bxrUpdateActiveFiltersUI);
$(document).on('change','[name="bxr_status"], [name="bxr_activity"]',function(){
    bxrUpdateActiveFiltersUI();
    if (typeof bxrEnforceDrawerLayout==='function') bxrEnforceDrawerLayout();
});

/* ── Applied Filters summary card (main page, reflects last-APPLIED state) ── */
function bxrCategoryIcon(cat){
    var paths={
        company:'<path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1"/>',
        location:'<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        status:'<circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 8v8M8 6h4a4 4 0 0 1 4 4M8 18h4a4 4 0 0 0 4-4"/>',
        activity:'<path d="M3 3v18h18M7 14l4-4 3 3 5-6"/>'
    };
    if (cat==='salary') return '<span style="font-size:11px;font-weight:700;line-height:1;">₹</span>';
    return '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">'+(paths[cat]||'')+'</svg>';
}
function bxrBuildAppliedItems(){
    var items=[];
    if (typeof bxrTagifyCompany!=='undefined') bxrTagifyCompany.value.forEach(function(t){
        items.push({cat:'company', label:t.value, clear:function(){ bxrTagifyCompany.removeTags(t.value); }});
    });
    if (typeof bxrTagifyLocation!=='undefined') bxrTagifyLocation.value.forEach(function(t){
        items.push({cat:'location', label:t.value, clear:function(){ bxrTagifyLocation.removeTags(t.value); }});
    });
    var minSal=$('#bxr_min_sal').val(), maxSal=$('#bxr_max_sal').val();
    if (minSal || maxSal){
        items.push({cat:'salary', label:(minSal||'0')+'-'+(maxSal||'Any'), clear:function(){ $('#bxr_min_sal,#bxr_max_sal').val(''); }});
    }
    var statusEl=$('[name="bxr_status"]:checked');
    if (statusEl.val() && statusEl.val()!=='active'){
        items.push({cat:'status', label:bxrPillLabel(statusEl), clear:function(){ $('#bxr_status_active').prop('checked',true); }});
    }
    var activityEl=$('[name="bxr_activity"]:checked');
    if (activityEl.val() && activityEl.val()!=='1'){
        items.push({cat:'activity', label:bxrPillLabel(activityEl), clear:function(){ $('#bxr_activity1').prop('checked',true); }});
    }
    return items;
}
function bxrAppliedChipRemoved(){
    fetchBenchExploreJobCandidateList();
    bxrUpdateActiveFiltersUI();
    bxrCaptureSnapshot();
    bxrRenderAppliedFiltersCard();
}
function bxrRenderAppliedFiltersCard(){
    var items=bxrBuildAppliedItems();
    var $chips=$('#bxr_applied_chips').empty();
    items.forEach(function(it){
        var $chip=$('<span class="bxr-applied-chip"></span>').addClass('cat-'+it.cat);
        $chip.append('<span class="ic">'+bxrCategoryIcon(it.cat)+'</span>');
        $chip.append($('<span></span>').text(it.label));
        $chip.append($('<span class="bxr-applied-chip-remove">&times;</span>').on('click',function(){ it.clear(); bxrAppliedChipRemoved(); }));
        $chips.append($chip);
    });
    $('#bxr_applied_card').toggleClass('d-none', items.length===0);
    $('#bxr_applied_count').text(items.length);
}
$(document).on('click','#bxr_applied_clear_all',function(){
    $('#bxr_clear_all').trigger('click');
    fetchBenchExploreJobCandidateList();
    bxrCaptureSnapshot();
    bxrRenderAppliedFiltersCard();
});
$(document).on('click','#bxr_clear_all',function(){
    if (typeof bxrTagifyCompany!=='undefined') bxrTagifyCompany.removeAllTags();
    if (typeof bxrTagifyLocation!=='undefined') bxrTagifyLocation.removeAllTags();
    $('#bxr_min_sal, #bxr_max_sal, #bxr_min_salary_currency, #bxr_sal_mode').val('');
    $('#bxr_status_active, #bxr_activity1').prop('checked', true);
    bxrUpdateActiveFiltersUI();
});
$(document).on('click','#bxr_reset_btn',function(){
    $('#bxr_clear_all').trigger('click');
    fetchBenchExploreJobCandidateList();
    bxrCaptureSnapshot();
    bxrRenderAppliedFiltersCard();
});

/* ── Draft-until-Apply: snapshot on open, restore on close-without-apply ── */
var bxrSnapshot=null, bxrApplying=false;
function bxrCaptureSnapshot(){
    bxrSnapshot={
        company:(typeof bxrTagifyCompany!=='undefined')?bxrTagifyCompany.value.map(function(t){return t.value;}):[],
        location:(typeof bxrTagifyLocation!=='undefined')?bxrTagifyLocation.value.map(function(t){return t.value;}):[],
        min_sal:$('#bxr_min_sal').val(), max_sal:$('#bxr_max_sal').val(),
        min_salary_currency:$('#bxr_min_salary_currency').val(), sal_mode:$('#bxr_sal_mode').val(),
        status:$('[name="bxr_status"]:checked').val(), activity:$('[name="bxr_activity"]:checked').val()
    };
}
function bxrRestoreSnapshot(){
    if (!bxrSnapshot) return;
    if (typeof bxrTagifyCompany!=='undefined'){ bxrTagifyCompany.removeAllTags(); if(bxrSnapshot.company.length) bxrTagifyCompany.addTags(bxrSnapshot.company); }
    if (typeof bxrTagifyLocation!=='undefined'){ bxrTagifyLocation.removeAllTags(); if(bxrSnapshot.location.length) bxrTagifyLocation.addTags(bxrSnapshot.location); }
    $('#bxr_min_sal').val(bxrSnapshot.min_sal);
    $('#bxr_max_sal').val(bxrSnapshot.max_sal);
    $('#bxr_min_salary_currency').val(bxrSnapshot.min_salary_currency);
    $('#bxr_sal_mode').val(bxrSnapshot.sal_mode);
    if (bxrSnapshot.status) $('[name="bxr_status"][value="'+bxrSnapshot.status+'"]').prop('checked',true);
    if (bxrSnapshot.activity) $('[name="bxr_activity"][value="'+bxrSnapshot.activity+'"]').prop('checked',true);
    bxrUpdateActiveFiltersUI();
}
function bxrBindDrawerLifecycle(){
    var el=document.getElementById('kt_bxr_filter_drawer');
    if (!el || typeof KTDrawer==='undefined' || !KTDrawer.getInstance) return setTimeout(bxrBindDrawerLifecycle,300);
    var inst=KTDrawer.getInstance(el);
    if (!inst) return setTimeout(bxrBindDrawerLifecycle,300);
    inst.on('kt.drawer.shown', function(){
        bxrApplying=false;
        bxrCaptureSnapshot();
        bxrEnforceDrawerLayout();
        setTimeout(bxrEnforceDrawerLayout,50);
    });
    inst.on('kt.drawer.after.hidden', function(){
        if (!bxrApplying) bxrRestoreSnapshot();
        bxrApplying=false;
    });
}
$(document).ready(bxrBindDrawerLifecycle);

$(document).on('click','#bxr_close_btn',function(){ $('#kt_bxr_filter_close').trigger('click'); });
$(document).on('click','#bxr_apply_btn',function(){
    bxrApplying=true;
    fetchBenchExploreJobCandidateList();
    bxrRenderAppliedFiltersCard();
    $('#kt_bxr_filter_close').trigger('click');
});

$(document).ready(function(){
    setTimeout(bxrUpdateActiveFiltersUI,600);
    setTimeout(bxrCaptureSnapshot,800);
    setTimeout(bxrRenderAppliedFiltersCard,700);
});
</script>
@endpush
