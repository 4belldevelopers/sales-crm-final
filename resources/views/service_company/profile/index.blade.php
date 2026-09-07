@extends('layouts.service_company_layout')
<?php 
   use App\Helper\GeneralMethod;
   use Carbon\Carbon;
?>
 @php
      
    $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
    $endOfMonthcomman =  Carbon::now()->endOfMonth();
    
    $endDate = Carbon::today()->endOfDay();
    $startDate = Carbon::today()->subDays(6)->startOfDay();

  @endphp
@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<style>
body
{
  overflow-x:hidden;
}
.border-bottom-dashed
{
  border-bottom: 1px dashed #eff2f5;
}
/*begin::Responsive-Candidate*/
.table td, .table th, .table tr
{
  width: max-content!important;
  padding: 0em;
}
/*end::Responsive-Candidate*/

@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  td:nth-child(n+2), th:nth-child(n+2)
  {
    width: revert!important;
  }
}  

/*begin::Media query*/
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0rem;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important;
  }
}

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesJob
  {
    width: 1025px;
    /* width: 1070px; */
  }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesJob
  {
    width: 1200px;
  }
}
/*end::Media query*/

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #client_stage_sub_stage 
  {
    width: 1025px;
  }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_client_activitiesApplication
  {
    width: 1025px;
  }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #client_stage_sub_stage
  {
    width: 1200px;
  }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_client_activitiesApplication
  {
    width: 1200px;
  }
}


@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesClientjob_toggle
  {
    width: 1025px;
    /* width: 1070px; */
  }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesClientjob_toggle
  {
    width: 1200px;
  }
}

.table-custom-class tbody tr {
    height: 82px;
    position: relative;
}
#myTable_jmyRank {
    table-layout: fixed !important;
    width: 100% !important;
}

#myTable_jmyRank th {
    width: 25% !important;
}
</style>
<style>

/*begin::Media query*/
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0rem;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important;
  }
}

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesJob
  {
    width: 1025px;
    /* width: 1070px; */
  }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesJob
  {
    width: 1200px;
  }
}
/*end::Media query*/



@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesClientjob_toggle
  {
    width: 1025px;
    /* width: 1070px; */
  }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesClientjob_toggle
  {
    width: 1200px;
  }
}
#load_jobs tbody tr {
    height: 82px; /* Adjust the height as per your requirement */
    position: relative;
}
#myTable_joining tbody tr {
    height: 82px; /* Adjust the height as per your requirement */
    position: relative;
}
.iti__flag {
    margin-top: 2px !important;
}

/* ===== Redesigned profile (scoped .scp-) ===== */
.scp, .scp * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; box-sizing:border-box; }
.scp { font-size:13px; color:#1f2937; }
.scp-hero { background:linear-gradient(135deg,#eef2ff 0%,#eaf2ff 55%,#f5f8ff 100%); border:1px solid #e6ecf5; border-radius:14px; padding:16px 20px; display:flex; flex-wrap:wrap; gap:18px; align-items:center; margin-bottom:14px; }
.scp-hero-avatar { width:74px; height:74px; border-radius:50%; overflow:hidden; flex-shrink:0; background:#dbe4f5; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#3b55e6; border:3px solid #fff; box-shadow:0 4px 14px rgba(0,0,0,.08); }
.scp-hero-avatar img { width:100%; height:100%; object-fit:cover; }
.scp-hero-main { flex:1; min-width:220px; }
.scp-hello { font-size:12px; color:#64748b; }
.scp-name { font-size:14px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:7px; line-height:1.3; }
.scp-name .scp-verif { width:14px; height:14px; color:#2563eb; }
.scp-role { font-size:12px; color:#475569; margin-top:2px; }
.scp-emp-badge { display:inline-flex; align-items:center; gap:6px; margin-top:8px; background:#e8fff3; color:#15803d; font-size:11px; font-weight:600; padding:4px 11px; border-radius:30px; }
.scp-pc { flex:1; min-width:220px; }
.scp-pc-top { display:flex; align-items:baseline; justify-content:space-between; }
.scp-pc-label { font-size:12px; color:#64748b; font-weight:600; }
.scp-pc-pct { font-size:14px; font-weight:800; color:#0f172a; }
.scp-pc-bar { height:7px; background:#dbe4f5; border-radius:6px; overflow:hidden; margin:7px 0 10px; }
.scp-pc-fill { height:100%; background:linear-gradient(90deg,#3b55e6,#6d8bff); border-radius:6px; }
.scp-pc-list { display:grid; grid-template-columns:1fr 1fr; gap:5px 16px; }
.scp-pc-item { display:flex; align-items:center; gap:7px; font-size:11.5px; color:#475569; }
.scp-pc-item .ic { width:14px; height:14px; flex-shrink:0; }
.scp-pc-item.done { color:#15803d; }
.scp-hero-actions { display:flex; flex-direction:column; gap:7px; min-width:170px; }
.scp-btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:8px 14px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#fff; color:#334155; text-decoration:none; transition:all .15s; }
.scp-btn svg { width:14px; height:14px; }
.scp-btn:hover { background:#f1f5f9; color:#334155; }
.scp-btn-primary { background:#3b55e6; color:#fff; border-color:#3b55e6; box-shadow:0 4px 12px rgba(59,85,230,.25); }
.scp-btn-primary:hover { background:#2f46c4; color:#fff; }
.scp-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:14px; }
.scp-stat { background:#fff; border:1px solid #eef2f7; border-radius:12px; padding:13px 14px; display:flex; align-items:center; gap:12px; box-shadow:0 1px 3px rgba(0,0,0,.04); }
.scp-stat-ic { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.scp-stat-ic svg { width:19px; height:19px; }
.ic-blue { background:#eef2ff; color:#3b55e6; } .ic-green { background:#e8fff3; color:#16a34a; } .ic-amber { background:#fff8e6; color:#d97706; } .ic-purple { background:#f5efff; color:#7c3aed; } .ic-rose { background:#fff1f3; color:#e11d48; }
.scp-stat-lbl { font-size:11.5px; color:#64748b; font-weight:500; }
.scp-stat-num { font-size:14px; font-weight:800; color:#0f172a; line-height:1.2; }
.scp-stat-sub { font-size:11px; font-weight:600; color:#16a34a; }
.scp-tabs .nav-link { font-size:13px !important; font-weight:600; color:#64748b; padding:12px 6px; }
.scp-tabs .nav-link.active { color:#3b55e6 !important; }
.scp-grid3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
.scp-card { background:#fff; border:1px solid #eef2f7; border-radius:14px; padding:18px 20px; box-shadow:0 1px 3px rgba(0,0,0,.04); }
.scp-card-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.scp-card-title { display:flex; align-items:center; gap:9px; font-size:14px; font-weight:700; color:#0f172a; }
.scp-card-title .ci { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; }
.scp-card-title .ci svg { width:16px; height:16px; }
.scp-edit { font-size:12px; font-weight:600; color:#3b55e6; cursor:pointer; text-decoration:none; }
.scp-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:9px 0; border-bottom:1px dashed #eef2f7; }
.scp-row:last-child { border-bottom:none; }
.scp-row .k { display:flex; align-items:center; gap:7px; font-size:12px; color:#94a3b8; }
.scp-row .k svg { width:14px; height:14px; }
.scp-row .v { font-size:12.5px; font-weight:600; color:#1f2937; text-align:right; word-break:break-word; }
.scp-row .v a { color:#3b55e6; text-decoration:none; }
.scp-pill { display:inline-flex; align-items:center; gap:4px; background:#e8fff3; color:#15803d; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; }
.scp-pill.free { background:#eef2ff; color:#3b55e6; }
.scp-upgrade { display:block; width:100%; text-align:center; margin-top:12px; padding:9px; border:0; border-radius:9px; background:#eef2ff; color:#3b55e6; font-size:13px; font-weight:700; cursor:pointer; }
@media (max-width:1200px){ .scp-stats { grid-template-columns:repeat(3,1fr); } .scp-grid3 { grid-template-columns:1fr 1fr; } }
@media (max-width:768px){ .scp-stats { grid-template-columns:1fr 1fr; } .scp-grid3 { grid-template-columns:1fr; } .scp-pc-list { grid-template-columns:1fr; } }

/* ===================== Shared icon tints (Settings + Rank) ===================== */
.sst-wrap, .sst-wrap *, .srk-wrap, .srk-wrap * { font-family:'Poppins',sans-serif !important; box-sizing:border-box; }
.ic-ind { background:#eef1ff; color:#3b55e6; }
.ic-vio { background:#f3edff; color:#7c4dff; }
.ic-grn { background:#e8fbf1; color:#16a34a; }
.ic-amb { background:#fff4e5; color:#f59e0b; }
.ic-blu { background:#e8f3ff; color:#0ea5e9; }

/* ===================== My Settings ===================== */
.sst-wrap { padding:2px 0 8px; }
.sst-grid { display:grid; grid-template-columns:1.55fr 1fr; gap:14px; }
.sst-col { display:flex; flex-direction:column; gap:14px; }
.sst-card { background:#fff; border:1px solid #eaecf2; border-radius:13px; padding:14px 16px; }
.sst-head { display:flex; align-items:center; gap:9px; padding-bottom:11px; margin-bottom:6px; border-bottom:1px solid #f0f2f7; }
.sst-head-ic { width:30px; height:30px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:13px; }
.sst-head-t { font-size:14px; font-weight:700; color:#1b2440; }
.sst-item, .sst-pref, .sst-tip { display:flex; align-items:center; gap:12px; padding:11px 0; border-bottom:1px solid #f3f5f9; }
.sst-item.sst-last, .sst-pref.sst-last, .sst-tip.sst-last { border-bottom:0; }
.sst-it-ic { width:36px; height:36px; flex:0 0 36px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; font-size:14px; }
.sst-it-body { flex:1 1 auto; min-width:0; }
.sst-it-top { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.sst-it-title { font-size:13px; font-weight:700; color:#1f2a44; }
.sst-it-val { font-size:12.5px; color:#384358; font-weight:600; margin-top:1px; word-break:break-all; }
.sst-it-sub { font-size:11.5px; color:#8a93a6; margin-top:1px; }
.sst-chip { font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; }
.sst-chip.ok { background:#e8fbf1; color:#15803d; }
.sst-chip.off { background:#fff0f1; color:#e11d48; }
.sst-btn { flex:0 0 auto; font-size:12px; font-weight:700; color:#3b55e6; background:#fff; border:1px solid #d7dce9; border-radius:8px; padding:7px 13px; text-decoration:none; white-space:nowrap; transition:.15s; }
.sst-btn:hover { background:#eef1ff; color:#3b55e6; }
.sst-btn-pri { display:flex; align-items:center; justify-content:center; gap:7px; width:100%; margin-top:13px; font-size:12.5px; font-weight:700; color:#fff; background:#3b55e6; border:0; border-radius:9px; padding:9px; text-decoration:none; }
.sst-btn-pri:hover { background:#2f46c9; color:#fff; }
.sst-switch { position:relative; flex:0 0 auto; width:40px; height:22px; }
.sst-switch input { opacity:0; width:0; height:0; }
.sst-switch span { position:absolute; inset:0; background:#cfd5e3; border-radius:20px; transition:.2s; cursor:default; }
.sst-switch span:before { content:""; position:absolute; left:3px; top:3px; width:16px; height:16px; background:#fff; border-radius:50%; transition:.2s; }
.sst-switch input:checked + span { background:#3b55e6; }
.sst-switch input:checked + span:before { transform:translateX(18px); }
.sst-note { display:flex; gap:7px; align-items:flex-start; margin-top:11px; background:#f1f5ff; color:#41506e; font-size:11px; padding:8px 11px; border-radius:8px; }
.sst-note i { color:#3b55e6; margin-top:1px; }
.sst-ring-row { display:flex; align-items:center; gap:16px; padding:6px 0 2px; }
.sst-ring { position:relative; width:108px; height:108px; flex:0 0 108px; }
.sst-ring svg { width:108px; height:108px; }
.sst-ring-c { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.sst-ring-pct { font-size:14px; font-weight:800; color:#16a34a; }
.sst-ring-lbl { font-size:10.5px; color:#8a93a6; font-weight:600; }
.sst-checks { list-style:none; margin:0; padding:0; flex:1 1 auto; }
.sst-checks li { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; padding:5px 0; }
.sst-checks li.ok { color:#15803d; } .sst-checks li.ok i { color:#16a34a; }
.sst-checks li.warn { color:#9a6b00; } .sst-checks li.warn i { color:#f59e0b; }
.sst-tip { text-decoration:none; }
.sst-chev { color:#b6bdcc; font-size:11px; }
.sst-tip:hover .sst-it-title { color:#3b55e6; }

/* ===================== My Rank ===================== */
.srk-wrap { padding:2px 0 10px; display:flex; flex-direction:column; gap:14px; }
.srk-hero { background:linear-gradient(135deg,#eef2ff 0%,#eaf2ff 55%,#f5f8ff 100%); border:1px solid #e6ecf5; border-radius:14px; padding:16px 18px; display:flex; flex-wrap:wrap; gap:18px; align-items:center; justify-content:space-between; }
.srk-hero-id { display:flex; align-items:center; gap:13px; }
.srk-ava { width:64px; height:64px; border-radius:13px; object-fit:cover; box-shadow:0 4px 14px rgba(30,40,90,.12); }
.srk-ava-i { display:inline-flex; align-items:center; justify-content:center; background:#1b2440; color:#fff; font-size:14px; font-weight:700; }
.srk-name { font-size:14px; font-weight:700; color:#1b2440; display:flex; align-items:center; gap:6px; }
.srk-verif { color:#3b55e6; font-size:13px; }
.srk-meta { display:flex; flex-wrap:wrap; gap:10px; font-size:11.5px; color:#5a6178; margin:3px 0 5px; }
.srk-meta i { color:#8a93a6; }
.srk-status { font-size:10.5px; font-weight:700; padding:2px 9px; border-radius:20px; }
.srk-status.on { background:#e8fbf1; color:#15803d; } .srk-status.off { background:#fff0f1; color:#e11d48; }
.srk-hero-stats { display:flex; gap:26px; flex-wrap:wrap; }
.srk-hs-lbl { font-size:11px; color:#6b7488; font-weight:600; display:flex; align-items:center; gap:5px; }
.srk-hs-num { font-size:14px; font-weight:800; color:#1b2440; line-height:1.1; margin:2px 0; }
.srk-hs-delta { font-size:10.5px; font-weight:700; display:flex; align-items:center; gap:4px; }
.srk-hs-delta.up { color:#16a34a; } .srk-hs-delta.down { color:#e11d48; } .srk-hs-delta.flat { color:#9aa2b4; }
.srk-gold { color:#f59e0b; } .srk-blue { color:#0ea5e9; }
.srk-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
.srk-stat { background:#fff; border:1px solid #eaecf2; border-radius:12px; padding:13px 14px; display:flex; align-items:center; gap:11px; }
.srk-st-ic { width:42px; height:42px; flex:0 0 42px; border-radius:11px; display:inline-flex; align-items:center; justify-content:center; font-size:17px; }
.srk-st-lbl { font-size:11px; color:#7a8298; font-weight:600; }
.srk-st-num { font-size:14px; font-weight:800; color:#1b2440; }
.srk-mid { display:grid; grid-template-columns:1.7fr 1fr; gap:14px; }
.srk-card { background:#fff; border:1px solid #eaecf2; border-radius:13px; padding:14px 16px; }
.srk-head { display:flex; align-items:baseline; justify-content:space-between; gap:8px; padding-bottom:11px; margin-bottom:8px; border-bottom:1px solid #f0f2f7; }
.srk-head-t { font-size:14px; font-weight:700; color:#1b2440; }
.srk-head-sub { font-size:11px; color:#9aa2b4; }
.srk-chart { height:240px; }
.srk-journey { list-style:none; margin:0; padding:0; }
.srk-journey li { display:flex; gap:11px; padding:9px 0; position:relative; }
.srk-journey li:not(:last-child):after { content:""; position:absolute; left:16px; top:36px; bottom:-2px; width:2px; background:#eef0f5; }
.srk-jd { width:32px; height:32px; flex:0 0 32px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; font-size:13px; z-index:1; }
.srk-journey li.todo .srk-jd { background:#f1f3f8; color:#aeb6c6; }
.srk-j-t { font-size:12.5px; font-weight:700; color:#1f2a44; }
.srk-journey li.todo .srk-j-t { color:#9aa2b4; }
.srk-j-s { font-size:11px; color:#8a93a6; margin-top:1px; }
.srk-table-wrap { overflow-x:auto; }
.srk-table { width:100%; border-collapse:collapse; font-size:12.5px; }
.srk-table th { text-align:left; font-size:11px; font-weight:700; color:#8a93a6; text-transform:uppercase; letter-spacing:.3px; padding:8px 10px; border-bottom:1px solid #eef0f5; white-space:nowrap; }
.srk-table td { padding:9px 10px; border-bottom:1px solid #f3f5f9; color:#46506a; white-space:nowrap; }
.srk-td-m { font-weight:700; color:#1f2a44; }
.srk-td-s { font-weight:800; color:#1b2440; }
.srk-up { color:#16a34a; font-weight:700; } .srk-down { color:#e11d48; font-weight:700; } .srk-flat { color:#aeb6c6; }
.srk-td-r { font-weight:700; color:#1f2a44; }
/* tier badge */
.srk-tier { display:inline-flex; align-items:center; flex-wrap:wrap; gap:7px; font-size:10.5px; font-weight:700; letter-spacing:.3px; padding:3px 10px; border-radius:8px; }
.srk-tier i { font-size:12px; }
.srk-tier em { font-style:normal; font-weight:600; letter-spacing:0; opacity:.85; }
.srk-tier.platinum { background:#eef1ff; color:#3b55e6; }
.srk-tier.gold { background:#fff6e5; color:#b8860b; }
.srk-tier.silver { background:#eef1f6; color:#6b7488; }
.srk-tier.bronze { background:#fbeee4; color:#a86a3d; }
/* certificate card in hero */
.srk-cert { display:flex; gap:10px; align-items:flex-start; background:#fff; border:1px solid #e2e7f3; border-radius:11px; padding:11px 13px; max-width:280px; }
.srk-cert-ic { width:34px; height:34px; flex:0 0 34px; border-radius:9px; background:#eef1ff; color:#3b55e6; display:inline-flex; align-items:center; justify-content:center; font-size:15px; }
.srk-cert-t { font-size:13px; font-weight:700; color:#1b2440; }
.srk-cert-s { font-size:11px; color:#7a8298; margin:1px 0 6px; }
.srk-cert-btn { display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:#3b55e6; text-decoration:none; }
/* stat sparkline */
.srk-st-body { flex:1 1 auto; min-width:0; }
.srk-spark { width:64px; height:30px; flex:0 0 64px; opacity:.75; }
.srk-spark svg { width:100%; height:100%; }
.srk-spark.ind svg polyline { stroke:#3b55e6; } .srk-spark.grn svg polyline { stroke:#16a34a; }
.srk-spark.vio svg polyline { stroke:#7c4dff; } .srk-spark.amb svg polyline { stroke:#f59e0b; }
/* 3-column mid row */
.srk-mid3 { display:grid; grid-template-columns:1fr 1.3fr 0.95fr; gap:14px; align-items:start; }
/* leaderboard */
.srk-lb { display:flex; flex-direction:column; }
.srk-lb-row { display:flex; align-items:center; gap:9px; padding:7px 6px; border-radius:9px; border-bottom:1px solid #f4f6fa; }
.srk-lb-row:last-child { border-bottom:0; }
.srk-lb-row.me { background:#f3f6ff; }
.srk-lb-row.sep { margin-top:6px; border-top:1px dashed #dfe4f0; }
.srk-lb-rk { width:24px; flex:0 0 24px; text-align:center; font-size:12px; font-weight:800; color:#7a8298; position:relative; }
.srk-lb-rk.g1 i { color:#f4b400; } .srk-lb-rk.g2 i { color:#9aa2b4; } .srk-lb-rk.g3 i { color:#cd7f32; }
.srk-lb-rk i { font-size:15px; }
.srk-lb-ava { width:28px; height:28px; flex:0 0 28px; border-radius:50%; object-fit:cover; }
.srk-lb-ini { display:inline-flex; align-items:center; justify-content:center; background:#1b2440; color:#fff; font-size:11px; font-weight:700; }
.srk-lb-name { flex:1 1 auto; min-width:0; font-size:12.5px; font-weight:600; color:#1f2a44; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.srk-lb-you { font-style:normal; font-size:9.5px; font-weight:700; color:#3b55e6; background:#e6ebff; padding:1px 6px; border-radius:10px; margin-left:4px; }
.srk-lb-score { font-size:12.5px; font-weight:800; color:#1b2440; flex:0 0 auto; }
.srk-empty { font-size:12px; color:#9aa2b4; padding:14px 4px; text-align:center; }
.srk-trend-note { display:flex; gap:7px; align-items:center; margin-top:10px; background:#e8fbf1; color:#15803d; font-size:11px; font-weight:600; padding:7px 10px; border-radius:8px; }
@media (max-width:1100px){ .sst-grid { grid-template-columns:1fr; } .srk-mid3 { grid-template-columns:1fr; } .srk-stats { grid-template-columns:repeat(2,1fr); } .srk-hero-stats { gap:18px; } .srk-cert { max-width:none; } }
@media (max-width:600px){ .srk-stats { grid-template-columns:1fr; } .srk-hero { flex-direction:column; align-items:flex-start; } }

/* ===================== My Dashboard ===================== */
.sdb-wrap, .sdb-wrap * { font-family:'Poppins',sans-serif !important; box-sizing:border-box; }
.sdb-wrap { padding:2px 0 10px; display:flex; flex-direction:column; gap:14px; }
.ic-rose { background:#ffeef0; color:#e11d48; }
.sdb-top { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.sdb-title { font-size:14px; font-weight:700; color:#1b2440; }
.sdb-title span { font-size:14px; }
.sdb-sub { font-size:12px; color:#7a8298; margin-top:2px; }
.sdb-window { font-size:11.5px; font-weight:600; color:#46506a; background:#fff; border:1px solid #e6e9f2; border-radius:8px; padding:6px 12px; display:inline-flex; align-items:center; gap:6px; }
.sdb-window i { color:#3b55e6; }
.sdb-stats { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; }
.sdb-stat { background:#fff; border:1px solid #eaecf2; border-radius:12px; padding:14px; text-align:center; }
.sdb-st-ic { width:44px; height:44px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:17px; margin-bottom:8px; }
.sdb-st-lbl { font-size:11.5px; color:#7a8298; font-weight:600; }
.sdb-st-num { font-size:14px; font-weight:800; color:#1b2440; margin-top:3px; }
.sdb-mid { display:grid; grid-template-columns:1.45fr 1fr; gap:14px; align-items:start; }
.sdb-card { background:#fff; border:1px solid #eaecf2; border-radius:13px; padding:15px 16px; }
.sdb-head { display:flex; align-items:baseline; justify-content:space-between; gap:8px; padding-bottom:12px; margin-bottom:10px; border-bottom:1px solid #f0f2f7; }
.sdb-head-t { font-size:14px; font-weight:700; color:#1b2440; display:flex; align-items:center; gap:7px; }
.sdb-head-sub { font-size:11px; color:#9aa2b4; white-space:nowrap; }
.sdb-bolt { color:#f59e0b; }
.ic-t-amb { color:#f59e0b; } .ic-t-grn { color:#16a34a; } .ic-t-vio { color:#7c4dff; } .ic-t-blu { color:#2196f3; }
/* funnel */
.sdb-funnel { display:flex; flex-direction:column; align-items:center; gap:6px; padding:4px 0 2px; }
.sdb-fn-row { display:flex; align-items:center; gap:14px; width:100%; }
.sdb-fn-bar { color:#fff; font-size:13px; font-weight:700; text-align:center; padding:9px 0; border-radius:7px; margin-left:auto; box-shadow:0 2px 6px rgba(30,40,90,.08); min-width:60px; }
.sdb-fn-lbl { flex:0 0 130px; display:flex; flex-direction:column; }
.sdb-fn-lbl span { font-size:12px; font-weight:700; color:#1f2a44; }
.sdb-fn-lbl small { font-size:10px; color:#9aa2b4; }
.sdb-fn-foot { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:14px; padding-top:13px; border-top:1px solid #f0f2f7; text-align:center; }
.sdb-ff-lbl { font-size:11px; color:#7a8298; font-weight:600; }
.sdb-ff-num { font-size:14px; font-weight:800; color:#1b2440; margin-top:2px; }
.sdb-green { color:#16a34a; }
/* action center */
.sdb-ac { display:flex; flex-direction:column; gap:9px; }
.sdb-ac-row { display:flex; align-items:center; gap:11px; background:#f8f9fc; border:1px solid #eef1f6; border-radius:10px; padding:10px 12px; }
.sdb-ac-ic { width:34px; height:34px; flex:0 0 34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; font-size:14px; }
.sdb-ac-body { flex:1 1 auto; min-width:0; }
.sdb-ac-t { font-size:12.5px; font-weight:700; color:#1f2a44; }
.sdb-ac-s { font-size:11px; color:#8a93a6; }
.sdb-ac-n { font-size:14px; font-weight:800; color:#1b2440; flex:0 0 auto; }
.sdb-ac-all { display:flex; align-items:center; justify-content:center; gap:7px; margin-top:12px; padding:9px; font-size:12.5px; font-weight:700; color:#3b55e6; text-decoration:none; border-top:1px solid #f0f2f7; }
/* trend charts */
.sdb-charts { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.sdb-chart { height:200px; }
@media (max-width:1200px){ .sdb-stats { grid-template-columns:repeat(3,1fr); } }
@media (max-width:992px){ .sdb-mid { grid-template-columns:1fr; } .sdb-charts { grid-template-columns:1fr; } }
@media (max-width:600px){ .sdb-stats { grid-template-columns:repeat(2,1fr); } .sdb-fn-lbl { flex-basis:100px; } }

/* ===================== My Jobs — enterprise job list (scoped .jobx) ===================== */
.jobx, .jobx * { font-family:'Poppins',sans-serif; box-sizing:border-box; }
/* Filter toolbar */
.jobx .jobx-toolbar { background:#fff; border:1px solid #e4e6ef; border-radius:12px; padding:9px 12px; display:flex; gap:10px; align-items:center; margin-bottom:12px; box-shadow:0 1px 3px rgba(0,0,0,.04); flex-wrap:wrap; }
.jobx .jobx-search { position:relative; flex:1 1 320px; min-width:200px; }
.jobx .jobx-search > svg { position:absolute; left:11px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:#a1a5b7; pointer-events:none; }
.jobx .jobx-search input { width:100%; height:38px; padding:6px 12px 6px 34px; border:1px solid #e4e6ef; border-radius:8px; font-size:12.5px; font-family:'Poppins',sans-serif; color:#181c32; background:#f9fafc; outline:none; transition:all .15s; }
.jobx .jobx-search input::placeholder { color:#a1a5b7; }
.jobx .jobx-search input:focus { border-color:#3b55e6; background:#fff; box-shadow:0 0 0 3px rgba(59,85,230,.12); }
.jobx .jobx-select { height:38px; min-width:160px; padding:6px 32px 6px 12px; border:1px solid #e4e6ef; border-radius:8px; font-size:12.5px; font-family:'Poppins',sans-serif; font-weight:500; color:#181c32; cursor:pointer; outline:none; -webkit-appearance:none; -moz-appearance:none; appearance:none; background-color:#fff; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236d7284' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 11px center; transition:all .15s; }
.jobx .jobx-select:focus { border-color:#3b55e6; box-shadow:0 0 0 3px rgba(59,85,230,.12); }
@media (max-width:768px){ .jobx .jobx-toolbar { flex-direction:column; align-items:stretch; } .jobx .jobx-search, .jobx .jobx-select { width:100%; min-width:0; } }
.jobx .table-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.05); border:1px solid #e4e6ef; overflow:hidden; }
.jobx .table-header, .jobx .job-row { display:grid; grid-template-columns:2.6fr 1.05fr 1.15fr 1.3fr .95fr .95fr 2.55fr; align-items:start; column-gap:12px; }
.jobx .table-header { padding:9px 12px; background:#f7f8fc; border-bottom:1.5px solid #e4e6ef; align-items:center; }
.jobx .th { font-size:10.5px; font-weight:600; color:#6d7284; text-transform:uppercase; letter-spacing:.4px; display:flex; align-items:center; gap:4px; }
.jobx .job-row { padding:12px; border-bottom:1px solid #e4e6ef; transition:background .12s; }
.jobx .job-row:last-of-type { border-bottom:none; }
.jobx .job-row:hover { background:#fafbff; }
.jobx .col-job { display:flex; gap:10px; }
.jobx .col-job a { text-decoration:none; }
.jobx .job-logo { width:48px; height:48px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:800; color:#fff; overflow:hidden; align-self:flex-start; }
.jobx .job-logo img { width:100%; height:100%; object-fit:cover; }
.jobx .job-info { flex:1; min-width:0; }
.jobx .job-title { font-size:12.5px; font-weight:700; color:#181c32; margin-bottom:1px; line-height:1.3; }
.jobx .job-dept { font-size:11px; color:#6d7284; margin-bottom:1px; }
.jobx .job-client { font-size:11px; color:#6d7284; font-weight:500; margin-bottom:7px; }
.jobx .job-meta-line { display:flex; align-items:center; flex-wrap:wrap; gap:5px; font-size:10.5px; color:#a1a5b7; }
.jobx .skills-inline { font-size:10.5px; color:#a1a5b7; }
.jobx .skills-inline b { font-weight:600; color:#6d7284; }
.jobx .skills-inline .badge { font-size:9.5px; padding:2px 6px; margin:0 2px 0 0; }
.jobx .col-exp { display:flex; flex-direction:column; gap:5px; }
.jobx .exp-row { display:flex; align-items:center; gap:5px; font-size:11.5px; font-weight:500; color:#181c32; white-space:nowrap; }
.jobx .exp-row svg, .jobx .sal-row svg { width:13px; height:13px; color:#a1a5b7; flex-shrink:0; }
.jobx .sal-row { display:flex; align-items:center; gap:5px; font-size:11.5px; font-weight:500; color:#181c32; white-space:nowrap; }
.jobx .badge-type { display:inline-flex; align-items:center; gap:3px; padding:2px 9px; border-radius:20px; font-size:10px; font-weight:600; width:fit-content; margin-top:2px; }
.jobx .badge-internal { background:#eef2ff; color:#3b55e6; }
.jobx .badge-client { background:#eefff6; color:#09b760; }
.jobx .badge-direct { background:#fff4ee; color:#f4682a; }
.jobx .col-hl { display:flex; flex-direction:column; gap:5px; }
.jobx .badge-hiring { padding:2px 9px; border-radius:20px; font-size:10.5px; font-weight:600; display:inline-flex; align-items:center; gap:5px; width:fit-content; text-decoration:none; }
.jobx .badge-hiring::before { content:""; width:6px; height:6px; background:#c9c4c4; border-radius:50%; }
.jobx .badge-hiring.active-bg { background:#eafff3; color:#17c653; }
.jobx .badge-hiring.active-bg::before { background:#17c653; }
.jobx .badge-hiring.hold-bg { background:#fff8e1; color:#ffa800; }
.jobx .badge-hiring.hold-bg::before { background:#ffa800; }
.jobx .badge-hiring.closed-bg { background:#ffeef1; color:#f1416c; }
.jobx .badge-hiring.closed-bg::before { background:#f1416c; }
.jobx .loc-row, .jobx .mode-row { display:flex; align-items:center; gap:4px; font-size:11.5px; color:#6d7284; font-weight:500; }
.jobx .loc-row svg, .jobx .mode-row svg { width:12px; height:12px; color:#a1a5b7; flex-shrink:0; }
.jobx .col-hm { font-size:11.5px; }
.jobx .hm-name { font-size:12px; font-weight:600; color:#181c32; margin-bottom:2px; }
.jobx .hm-title { font-size:11px; color:#6d7284; margin-bottom:1px; }
.jobx .hm-company { font-size:11px; color:#a1a5b7; }
.jobx .col-action { display:flex; flex-direction:column; gap:5px; padding:0 6px; }
.jobx .btn-action { display:inline-flex; align-items:center; justify-content:center; gap:4px; padding:4px 6px; border:1.5px dashed #e4e6ef; border-radius:6px; background:transparent; color:#6d7284; font-size:9px; font-weight:500; cursor:pointer; transition:all .15s; white-space:nowrap; min-height:30px; }
.jobx .btn-action:hover { border-color:#3b55e6; color:#3b55e6; background:#f0f3ff; }
.jobx .btn-action svg { width:11px; height:11px; }
.jobx .action-count { font-size:11px; color:#6d7284; }
.jobx .action-more { font-size:10.5px; color:#3b55e6; cursor:pointer; font-weight:500; }
.jobx .col-pipeline { display:flex; flex-direction:column; gap:4px; padding-left:10px; border-left:1px solid #eef0f5; }
.jobx .pipeline-stages { display:flex; align-items:flex-start; gap:1px; flex-wrap:nowrap; overflow:hidden; padding-bottom:2px; }
.jobx .stage { display:flex; flex-direction:column; align-items:center; gap:2px; flex:1; min-width:0; }
.jobx .stage-label { font-size:8.5px; font-weight:600; color:#6d7284; text-transform:uppercase; letter-spacing:.3px; }
.jobx .stage-badge { min-width:20px; width:20px; height:18px; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; }
.jobx .stage-badge a { font-size:9px; font-weight:700; color:inherit; text-decoration:none; }
.jobx .s-inw { background:#ffffde; color:#bdbd26; } .jobx .s-inw a { color:#bdbd26; }
.jobx .s-sou { background:#efefef; color:#000; } .jobx .s-sou a { color:#000; }
.jobx .s-scr { background:#f7deff; color:#c000ff; } .jobx .s-scr a { color:#c000ff; }
.jobx .s-eva { background:#f0f8ff; color:#2896ff; } .jobx .s-eva a { color:#2896ff; }
.jobx .s-neg { background:#ffe6f2; color:#ff4098; } .jobx .s-neg a { color:#ff4098; }
.jobx .s-off { background:#fff9ed; color:#ffab00; } .jobx .s-off a { color:#ffab00; }
.jobx .s-joi { background:#d4ffde; color:#06c732; } .jobx .s-joi a { color:#06c732; }
.jobx .s-rej { background:#ffe2e5; color:#ff0202; } .jobx .s-rej a { color:#ff0202; }
.jobx .pipeline-total { font-size:10px; font-weight:600; color:#6d7284; text-align:right; display:flex; justify-content:flex-end; gap:6px; flex-wrap:wrap; }
.jobx .pipeline-total strong { color:#181c32; }
.jobx .add-date { font-size:10px; color:#a1a5b7; align-items:center; }
.jobx .add-date svg { width:11px; height:11px; flex-shrink:0; }
.jobx .pipeline-legend { display:flex; align-items:center; gap:14px; padding:9px 14px; border-top:1px solid #e4e6ef; background:#fafbfd; flex-wrap:wrap; }
.jobx .legend-label { font-weight:600; color:#6d7284; font-size:10.5px; }
.jobx .legend-item { display:flex; align-items:center; gap:4px; color:#6d7284; font-size:10.5px; }
.jobx .legend-dot { width:9px; height:9px; border-radius:2px; flex-shrink:0; }
.jobx .drag-col { display:flex; flex-direction:column; align-items:center; padding-top:2px; flex-shrink:0; }
.jobx .drag-handle { color:#a1a5b7; cursor:grab; }
.jobx .it-job-type { display:inline-flex; align-items:center; padding:2px 6px; border-radius:4px; font-size:9px; font-weight:600; white-space:nowrap; }
.jobx .badge-type { white-space:nowrap; }
.jobx .it-job-id { font-size:10px; color:#6a6b70; font-weight:500; text-decoration:none; }
.jobx .it-job-id:hover { text-decoration:underline; }
.jobx .it-dot-separator { width:3px; height:3px; border-radius:50%; background:#a1a5b7; }
.jobx .job-empty { padding:34px 12px; text-align:center; color:#a1a5b7; font-size:12px; }
@media (max-width:1600px){ .jobx .table-header, .jobx .job-row { grid-template-columns:2.3fr 1fr 1fr 1.2fr .9fr .9fr 2.8fr; } }
@media (max-width:1366px){ .jobx .table-header, .jobx .job-row { grid-template-columns:2.4fr .95fr .95fr 1fr .85fr .85fr 1.9fr; } }
@media (max-width:1024px){ .jobx .table-card { overflow-x:auto; } .jobx .table-header, .jobx .job-row { min-width:1000px; } }

/* ===================== My Interview Tracker (attachment design) ===================== */
.it-wrap, .it-wrap * { font-family:'Poppins',sans-serif !important; box-sizing:border-box; }
.it-wrap {
    --it-primary:#2563eb; --it-primary-light:#eff6ff; --it-primary-dark:#1d4ed8;
    --it-success:#10b981; --it-success-light:#f0fdf4;
    --it-warning:#f59e0b; --it-warning-light:#fffbeb;
    --it-danger:#ef4444; --it-danger-light:#fef2f2;
    --it-orange:#f97316; --it-orange-light:#fff7ed;
    --it-text-main:#1e293b; --it-text-muted:#64748b; --it-text-light:#94a3b8;
    --it-border:#e2e8f0; --it-border-light:#f1f5f9; --it-bg-main:#f8fafc;
    --it-radius:6px; --it-radius-lg:10px; --it-radius-xl:12px;
    --it-shadow:0 1px 3px rgba(0,0,0,.08); --it-shadow-lg:0 10px 15px -3px rgba(0,0,0,.1);
    --it-transition:all .2s ease;
    color:var(--it-text-main); line-height:1.45; -webkit-font-smoothing:antialiased;
}
/* Filters bar */
.it-filters-bar { background:#fff; border-radius:var(--it-radius-xl); padding:8px 12px; display:flex; gap:8px; align-items:center; margin-bottom:12px; box-shadow:var(--it-shadow); border:1px solid var(--it-border-light); flex-wrap:wrap; }
.it-search-box { flex:0 0 320px; max-width:320px; min-width:180px; position:relative; }
.it-search-box input { width:100%; padding:6px 10px 6px 30px; border:1px solid var(--it-border); border-radius:var(--it-radius); font-size:12px; font-family:'Poppins',sans-serif; color:var(--it-text-main); outline:none; transition:var(--it-transition); background:var(--it-bg-main); height:34px; }
.it-search-box input:focus { border-color:var(--it-primary); background:#fff; box-shadow:0 0 0 3px var(--it-primary-light); }
.it-search-box > svg { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:var(--it-text-light); pointer-events:none; width:14px; height:14px; }
.it-sort-select { min-width:180px; height:34px; padding:6px 10px; border:1px solid var(--it-border); border-radius:var(--it-radius); font-size:12px; font-family:'Poppins',sans-serif; color:var(--it-text-main); background:#fff; cursor:pointer; outline:none; }
.it-sort-select:focus { border-color:var(--it-primary); box-shadow:0 0 0 3px var(--it-primary-light); }
.it-filters-bar .ms-auto-note { margin-left:auto; font-size:11px; color:var(--it-text-muted); font-weight:500; }
/* Table */
.it-table-container { background:#fff; border-radius:var(--it-radius-xl); box-shadow:var(--it-shadow); border:1px solid var(--it-border-light); overflow:hidden; }
.it-table-scroll { overflow-x:auto; }
.it-data-table { width:100%; border-collapse:separate; border-spacing:0; min-width:1080px; table-layout:auto; }
.it-data-table th { background:#f8fafc; padding:9px 10px !important; text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:.5px; color:var(--it-text-muted); font-weight:600; border-bottom:1px solid var(--it-border); white-space:nowrap; line-height:1.4; width:auto !important; }
.it-data-table th:first-child { padding-left:14px !important; width:36px !important; }
.it-data-table th:last-child { padding-right:14px !important; }
.it-data-table td { padding:10px !important; border-bottom:1px solid var(--it-border-light); font-size:11px; vertical-align:top; transition:background .15s; width:auto !important; }
.it-data-table tbody tr { height:auto !important; }
.it-data-table tbody tr:hover td { background:#f8fafc; }
.it-data-table tbody tr:last-child td { border-bottom:none; }
.it-data-table td:first-child { padding-left:14px !important; }
.it-data-table td:last-child { padding-right:14px !important; }
/* Checkbox */
.it-checkbox { width:14px; height:14px; border:2px solid var(--it-border); border-radius:3px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:var(--it-transition); flex-shrink:0; background:#fff; }
.it-checkbox:hover { border-color:var(--it-primary); }
.it-checkbox.checked { background:var(--it-primary); border-color:var(--it-primary); }
.it-checkbox.checked svg { display:block; }
.it-checkbox svg { display:none; color:#fff; width:10px; height:10px; }
/* Candidate */
.it-candidate-cell { display:flex; gap:8px; }
.it-avatar { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:600; flex-shrink:0; text-transform:uppercase; cursor:pointer; }
.it-avatar-green { background:#10b981; } .it-avatar-red { background:#ef4444; } .it-avatar-blue { background:#3b82f6; }
.it-avatar-purple { background:#8b5cf6; } .it-avatar-orange { background:#f97316; } .it-avatar-gray { background:#6b7280; }
.it-candidate-info { min-width:0; }
.it-candidate-name { font-weight:600; font-size:12px; color:var(--it-text-main); margin-bottom:1px; display:flex; align-items:center; gap:5px; }
.it-candidate-role { font-size:10.5px; color:var(--it-text-muted); margin-bottom:4px; }
.it-candidate-meta { display:flex; flex-direction:column; gap:2px; }
.it-meta-tag { display:inline-flex; align-items:center; gap:3px; font-size:10px; font-weight:500; }
/* Follow Up */
.it-followup-dropdown { position:relative; display:inline-block; margin-top:2px; }
.it-followup-btn { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:var(--it-radius); font-size:10px; font-weight:500; font-family:'Poppins',sans-serif; border:1px solid var(--it-border); background:#fff; color:var(--it-text-muted); cursor:pointer; transition:var(--it-transition); }
.it-followup-btn:hover { background:var(--it-bg-main); color:var(--it-primary); border-color:var(--it-primary); }
.it-followup-btn svg { width:10px; height:10px; }
.it-followup-menu { position:absolute; top:calc(100% + 4px); left:0; background:#fff; border:1px solid var(--it-border); border-radius:var(--it-radius-lg); box-shadow:var(--it-shadow-lg); z-index:50; display:none; min-width:150px; overflow:hidden; }
.it-followup-menu.open { display:block; }
.it-followup-item { padding:7px 10px; font-size:10px; cursor:pointer; transition:var(--it-transition); display:flex; align-items:center; gap:6px; color:var(--it-text-main); }
.it-followup-item:hover { background:var(--it-bg-main); color:var(--it-primary); }
.it-followup-item svg { width:10px; height:10px; }
/* Job */
.it-job-title { font-weight:600; font-size:12px; color:var(--it-text-main); margin-bottom:1px; }
.it-job-company { font-size:10.5px; color:var(--it-orange); font-weight:500; margin-bottom:4px; }
.it-job-tags { display:flex; align-items:center; gap:5px; flex-wrap:wrap; }
.it-job-type { display:inline-flex; align-items:center; padding:2px 6px; border-radius:4px; font-size:9px; font-weight:600; background:var(--it-primary-light); color:var(--it-primary); }
.it-job-id { font-size:10px; color:var(--it-primary); font-weight:500; text-decoration:none; }
.it-job-id:hover { text-decoration:underline; }
.it-dot-separator { width:3px; height:3px; border-radius:50%; background:var(--it-border); }
/* Date */
.it-date-cell { display:flex; flex-direction:column; gap:3px; }
.it-date-row { display:flex; align-items:center; gap:5px; font-size:11px; color:var(--it-text-main); font-weight:500; }
.it-date-row svg { color:var(--it-text-light); flex-shrink:0; width:12px; height:12px; }
.it-date-row .it-mode { color:var(--it-text-muted); font-size:10.5px; font-weight:400; }
.it-mode-row svg { color:var(--it-text-muted); }
/* Person */
.it-person-cell { display:flex; flex-direction:column; gap:2px; }
.it-person-name { font-weight:600; font-size:11px; color:var(--it-text-main); }
.it-person-company { font-size:10.5px; color:var(--it-text-muted); }
.it-person-extra { display:flex; align-items:center; gap:5px; margin-top:2px; }
.it-extra-badge { display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:16px; padding:0 4px; background:var(--it-bg-main); border:1px solid var(--it-border); border-radius:4px; font-size:9px; font-weight:600; color:var(--it-text-muted); }
.it-extra-text { font-size:10px; color:var(--it-text-muted); }
/* Stage */
.it-stage-cell { display:flex; flex-direction:column; gap:3px; align-items:flex-start; }
.it-badge { display:inline-flex; align-items:center; padding:2px 9px; border-radius:999px; font-size:10px; font-weight:600; width:fit-content; }
.it-badge-blue { background:#dbeafe; color:#1e40af; } .it-badge-purple { background:#f3e8ff; color:#6b21a8; }
.it-badge-orange { background:#ffedd5; color:#9a3412; } .it-badge-red { background:#fee2e2; color:#991b1b; }
.it-badge-green { background:#d1fae5; color:#065f46; } .it-badge-yellow { background:#fef9c3; color:#854d0e; }
.it-stage-sub { font-size:10px; color:var(--it-text-muted); }
.it-stage-status { display:inline-flex; align-items:center; gap:3px; font-size:10px; font-weight:500; }
.it-stage-status.confirmed { color:var(--it-success); } .it-stage-status.pending { color:var(--it-warning); }
.it-stage-status svg { width:10px; height:10px; }
/* Feedback */
.it-feedback-cell { display:flex; flex-direction:column; gap:4px; align-items:flex-start; }
.it-fb-status { font-size:10px; font-weight:600; display:inline-flex; align-items:center; gap:3px; }
.it-fb-rec { display:inline-flex; align-items:center; width:fit-content; padding:2px 8px; border-radius:50px; font-size:10px; font-weight:600; }
.it-fb-muted { font-size:10px; color:var(--it-text-muted); }
.it-feedback-btn { width:fit-content; padding:5px 10px; border-radius:var(--it-radius); font-size:10px; font-weight:600; font-family:'Poppins',sans-serif; cursor:pointer; transition:var(--it-transition); display:inline-flex; align-items:center; gap:4px; border:1px solid transparent; }
.it-feedback-btn-outline-green { border-color:var(--it-success); color:var(--it-success); background:transparent; }
.it-feedback-btn-outline-green:hover { background:var(--it-success-light); }
.it-feedback-btn-solid-blue { background:var(--it-primary); color:#fff; border-color:var(--it-primary); }
.it-feedback-btn-solid-blue:hover { background:var(--it-primary-dark); }
/* Interview Link overlay */
#ilOverlay { position:fixed; inset:0; background:rgba(15,23,42,.45); z-index:1090; display:none; align-items:center; justify-content:center; padding:20px; }
#ilOverlay.show { display:flex; }
#ilOverlay .il-box { background:#fff; border-radius:14px; width:100%; max-width:440px; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); font-family:'Poppins',sans-serif; }
#ilOverlay .il-head { display:flex; justify-content:space-between; align-items:center; padding:18px 22px 0; }
#ilOverlay .il-head span { font-size:14px; font-weight:600; color:#1e293b; }
#ilOverlay .il-close-btn { border:none; background:transparent; font-size:24px; line-height:1; color:#94a3b8; cursor:pointer; padding:0 4px; }
#ilOverlay .il-close-btn:hover { color:#334155; }
#ilOverlay .il-body { padding:14px 22px 22px; }
#ilOverlay .il-cand { font-size:12px; color:#64748b; margin-bottom:8px; }
#ilOverlay #ilLink { display:block; word-break:break-all; font-size:13px; color:#2563eb; background:#f1f5f9; border-radius:8px; padding:10px 12px; text-decoration:none; }
#ilOverlay .il-actions { display:flex; gap:10px; margin-top:16px; }
#ilOverlay .il-note { font-size:11px; color:#94a3b8; margin-top:10px; }
@media (max-width:1280px){ .it-data-table { min-width:1000px; } }
@media (max-width:768px){ .it-filters-bar { flex-direction:column; align-items:stretch; } .it-search-box, .it-sort-select { max-width:none; width:100%; } }
</style>
@endpush

 

@section('content')
<!--begin::Toolbar (removed)-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content" style="margin-top:-60px;">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Redesigned hero + stats-->
      <div class="scp px-2">
        @php
          $scp_logo = (!empty(@$users->image) && strlen(@$users->image)>10) ? @$users->image : '';
          $scp_initial = '';
          $scp_n = trim((string) @$users->name);
          if($scp_n !== '') $scp_initial = strtoupper(mb_substr($scp_n,0,1));
          $scp_pc = [
            'Basic Information'   => (!empty(@$users->name) && !empty(@$users->designation)),
            'Company Details'     => !empty(@$users->company_name),
            'Contact Information'  => (!empty(@$users->email) && !empty(@$users->mobile)),
            'Profile Image'       => (!empty(@$users->image) && strlen(@$users->image)>10),
          ];
          $scp_done = count(array_filter($scp_pc));
          $scp_pct  = count($scp_pc) ? round($scp_done/count($scp_pc)*100) : 0;
          $scp_active = isset($counts) ? (int) $counts->get('active',0) : 0;
          $scp_cand   = (int) (@$scpCandidatesTotal ?? @$total_candidates ?? 0);
          $scp_intv   = (int) (@$scpInterviewsTotal ?? 0);
          $scp_join   = (int) (@$scpJoiningsTotal ?? 0);
          $scp_rank   = '-';
          $__rmonths  = collect(@$rankings['months'] ?? [])->values();   // newest-first
          $__rkey     = $__rmonths->get(0);                              // current month — matches the My Rank tab
          if($__rkey !== null){ $scp_rank = @$rankings['internalRanks'][$__rkey]['rank'] ?? '-'; }
        @endphp
        <div class="scp-hero">
          <div class="scp-hero-avatar">@if($scp_logo)<img src="{{ $scp_logo }}" alt="">@else{{ $scp_initial }}@endif</div>
          <div class="scp-hero-main">
            <div class="scp-hello">Welcome back,</div>
            <div class="scp-name">{{ ucwords(@$users->name) }}
              <svg class="scp-verif" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 1.8 3 .1 1 2.8 2.4 1.7-.9 2.9.9 2.9-2.4 1.7-1 2.8-3 .1L12 22l-2.4-1.8-3-.1-1-2.8L3.2 15.6l.9-2.9-.9-2.9 2.4-1.7 1-2.8 3-.1L12 2z"/><path d="M10.6 14.6l-2.2-2.2-1.1 1.1 3.3 3.3 5.6-5.6-1.1-1.1z" fill="#fff"/></svg>
            </div>
            <div class="scp-role">{{ ucwords(@$users->designation) }}@if(!empty(@$users->company_name)) · {{ ucwords(@$users->company_name) }}@endif</div>
            <span class="scp-emp-badge"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg> Verified Employer</span>
          </div>
          <div class="scp-pc">
            <div class="scp-pc-top"><span class="scp-pc-label">Profile Completion</span><span class="scp-pc-pct">{{ $scp_pct }}%</span></div>
            <div class="scp-pc-bar"><div class="scp-pc-fill" style="width:{{ $scp_pct }}%"></div></div>
            <div class="scp-pc-list">
              @foreach($scp_pc as $label => $done)
                <div class="scp-pc-item {{ $done ? 'done' : '' }}">
                  @if($done)<svg class="ic" viewBox="0 0 24 24" fill="#16a34a"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-1 14.4l-3.7-3.7 1.4-1.4 2.3 2.3 4.6-4.6 1.4 1.4z"/></svg>
                  @else<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>@endif
                  {{ $label }}
                </div>
              @endforeach
            </div>
          </div>
          <div class="scp-hero-actions">
            <a href="{{ route('service_company.profile.edit', [GeneralMethod::value_encrypt($user_id)]) }}" class="scp-btn scp-btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4z"/></svg> Edit Profile</a>
            <a href="javascript:void(0);" class="scp-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/></svg> Share Company Page</a>
            <a href="{{ route('service_company.profile.edit', [GeneralMethod::value_encrypt($user_id)]) }}" class="scp-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M12 11v4M10 13h4" stroke-width="1.5"/></svg> Upload Profile</a>
          </div>
        </div>
        <div class="scp-stats">
          <div class="scp-stat"><div class="scp-stat-ic ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></div><div><div class="scp-stat-lbl">Active Jobs</div><div class="scp-stat-num">{{ $scp_active }}</div></div></div>
          <div class="scp-stat"><div class="scp-stat-ic ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg></div><div><div class="scp-stat-lbl">Candidates Added</div><div class="scp-stat-num">{{ number_format($scp_cand) }}</div></div></div>
          <div class="scp-stat"><div class="scp-stat-ic ic-amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><div><div class="scp-stat-lbl">Interviews</div><div class="scp-stat-num">{{ number_format($scp_intv) }}</div></div></div>
          <div class="scp-stat"><div class="scp-stat-ic ic-purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 11l2 2 4-4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div><div><div class="scp-stat-lbl">Joinings</div><div class="scp-stat-num">{{ number_format($scp_join) }}</div></div></div>
          <div class="scp-stat"><div class="scp-stat-ic ic-rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.5 13l1.5 8-5-3-5 3 1.5-8"/></svg></div><div><div class="scp-stat-lbl">My Rank</div><div class="scp-stat-num">#{{ $scp_rank }}</div></div></div>
        </div>
      </div>
      <!--end::Redesigned hero + stats-->
      <!--beign::User Details (legacy, hidden)-->
      <div class="card mb-5 mb-xl-5 mx-2 d-none">
        <div class="card-body pt-5 pb-0 px-5">
          <!--begin::Details-->
          <?php 
                  $background_image					=	'';//url('public/user-uploads/team_profile_img/avatar.png'); //'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png';
                  if(!empty(@$users->image) && strlen(@$users->image)>10){
                   		//	$background_image			=  url('public/user-uploads/team_profile_img/'.@$users->image);  
                   				$background_image			=  @$users->image;  
                   	}
                  ?>
          <?php 
                  $country_id		=	$state	= '';	
                  $country_id			=	$users->country;	  	//@$application->country;
                  $state				=	$users->state;    		//@$application->state; users    
                  if(@$country_id > 0)
                  {
                  	   $country_name_Arr	= GeneralMethod::getCountryName($country_id);	
                   foreach($country_name_Arr as $cval){
                  // $country_name		= ucwords($cval['name']);	
                   } 
                   if($country_id == 101)
                   {
                   		// $state_name	= @$users->state_name;
                      // $city_name	= @$users->city_name; 	
                   }
                  	   else
                   {  			
                  	if( $state > 0){
                  		$state_name_Arr		= GeneralMethod::getStateName($country_id, $state);	
                  		foreach($state_name_Arr as $sval){
                  			$state_name	= ucwords($sval['name']);	
                  		}	
                  	}
                  	// $city_name	= @$users->city; 
                   }	
                  
                  } 

                  $str = trim(@$users->name);
                    if (!empty($str)) {
                        $str = $strVal = strtolower($str[0]);
                        $str = ord($str);
                        if ($str >= 97 && $str <= 101) {
                            $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                        } elseif ($str >= 102 && $str <= 106) {
                            $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                        } elseif ($str >= 107 && $str <= 111) {
                            $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                        } elseif ($str >= 112 && $str <= 116) {
                            $bgTextDecore = 'fs-3 bg-light-success text-success';
                        } elseif ($str >= 117 && $str <= 121) {
                            $bgTextDecore = 'fs-3 bg-dark text-secondary';
                        } else {
                            $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                        }
                    }

                  
                  ?>
          <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
            <!--begin: Pic-->
            <div class="me-7 mb-4">

              @if(!$background_image)

              <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> 
                <div class="symbol-label {{$bgTextDecore}} border-radius-5">{{ strtoupper(@$strVal)}}</div>     
                  <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
               </div>

               @else


              <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> 
                <img src="<?php echo @$background_image; ?>" alt="<?php echo ucwords(@$users->name); ?>">
                <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
              </div>
              
              @endif


            </div>
            <!--end::Pic-->
            <!--begin::Info-->
            <div class="flex-grow-1">
              <!--begin::Row-1-->
              <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                <!--begin::User Details-->
                <div class="d-flex flex-column">
                  <!--begin::User Name-->
                  <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2"><?php echo ucwords(@$users->name); ?></span> <span class="text-muted fw-bolder fs-7">C-<?php echo ucwords(@$users->id); ?></span> </div>
                  <!--end::User Name-->
                  <!--begin::Designaiton-->
                  <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                    <!--begin::Svg Icon | path: icons/duotune/communication/com006.svg-->
                    <span class="svg-icon svg-icon-4 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                        <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      </g>
                    </svg>
                    </span> <?php echo ucwords(@$users->designation); ?> </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 me-5">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-4 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M9.82829464,16.6565893 C7.02541569,15.7427556 5,13.1079084 5,10 C5,6.13400675 8.13400675,3 12,3 C15.8659932,3 19,6.13400675 19,10 C19,13.1079084 16.9745843,15.7427556 14.1717054,16.6565893 L12,21 L9.82829464,16.6565893 Z M12,12 C13.1045695,12 14,11.1045695 14,10 C14,8.8954305 13.1045695,8 12,8 C10.8954305,8 10,8.8954305 10,10 C10,11.1045695 10.8954305,12 12,12 Z" fill="#000000"/>
                      </g>
                    </svg>
                    </span> <?php echo ucwords(@$city_name); ?>, <?php echo ucwords(@$state_name); ?>, <?php echo ucwords(@$countryName); ?> </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-4 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    Badge </a> </div>
                  <!--end::Designaiton-->
                </div>
                <!--end::User Details-->
                <!--begin::Action-->
                <div class="d-flex mt-4">
                  <!--begin::Whatsapp-->
                  <!--begin::Svg Icon-->
                  <?php 
                              if($users->status == 'active')
                              {
                              ?>
                  <a href="javascript:void(0);" class="btn btn-sm btn-flex btn-light-success fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                  </span> Active </a>
                  <?php 
                              }
                              else
                              {
                              ?>
                  <a href="javascript:void(0);" class="btn btn-sm btn-light-danger btn-active-danger fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                  </span> Inactive </a>
                  <?php 
                              } 
                              ?>
                  <!--end::Svg Icon-->
                  <!--end::Whatsapp-->
                  <!--begin::Email-->
                  <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-primary fw-bolder me-3 fs-6 d-none">
                  <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                  <span class="svg-icon svg-icon-primary svg-icon-2x">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"></rect>
                      <path d="M5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"></path>
                    </g>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  Email </a>
                  <!--end::Email-->
                </div>
                <!--end::Action-->
              </div>
              <!--end::Row-1-->
              <!--begin::Row-2-->
              <div class="d-flex flex-wrap flex-stack d-none">
                <!--begin::Wrapper-->
                <div class="d-flex flex-column flex-grow-1 pe-8">
                  <div class="row w-75"> <span class=""> <b>Skills/Tags :</b> <span class="badge badge-light">HTML5</span> <span class="badge badge-light">Bootstrap5</span> <span class="badge badge-light">CSS3</span> <span class="badge badge-light">JQuery</span> <span class="badge badge-light">Javascript</span> <span class="badge badge-light">Photoshop</span> <span class="badge badge-light">Illustator</span> </span> </div>
                </div>
                <!--end::Wrapper-->
              </div>
              <!--end::Row-2-->
            </div>
            <!--end::Info-->
          </div>
          <!--end::Details-->
        </div>
      </div>
      <!--end::User Details-->
      <!--begin::Tabs-->
      <div class="card card-custom mx-2">
        <!--Begin::Tabs Header-->
        <div class="card-header card-header-tabs-line">
          <div class="card-toolbar">
            <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x scp-tabs" role="tablist">
              <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#overview"> <span class="nav-text font-weight-bold">My Details</span> </a> </li>
              <li class="nav-item me-3"  onclick="load_jobs('{{ @$users->id }}')"> <a class="nav-link" data-toggle="tab" href="#requisitions"> <span class="nav-text font-weight-bold">My Jobs</span> </a> </li>
              <li class="nav-item me-3" onclick="load_application('{{ @$users->id }}')""> <a class="nav-link" data-toggle="tab" href="#candidate"> <span class="nav-text font-weight-bold">My Candidate</span> </a> </li>
              <li class="nav-item me-3"  onclick="load_interviewSchedule('{{ @$users->id }}')"> <a class="nav-link" data-toggle="tab" href="#interview_tracker"> <span class="nav-text font-weight-bold">My Interview Tracker</span> </a> </li>
              <li class="nav-item me-3" onclick="jointableLoad('{{ @$users->id }}')"> <a class="nav-link" data-toggle="tab" href="#joinings"> <span class="nav-text font-weight-bold">My Joinings</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#myRank"> <span class="nav-text font-weight-bold">My Rank</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#candidateDashboard"> <span class="nav-text font-weight-bold">My Dashboard</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#settings"> <span class="nav-text font-weight-bold">My Settings</span> </a> </li>
            </ul>
          </div>
        </div>
        <!--end::Tabs Header-->
        <!--Begin::Body-->
        <div class="card-body py-0">
          <div class="tab-content pt-5">
            <!--begin::Overview Content-->
            <div class="tab-pane active show" id="overview" role="tabpanel">
              <div class="scp py-5">
                @php
                  $whatsappConnectCheck = GeneralMethod::whatsappConnectCheck($users->id);
                  $scp_edit = route('service_company.profile.edit', [GeneralMethod::value_encrypt($user_id)]);
                @endphp
                <div class="scp-grid3">
                  <!-- Personal Information -->
                  <div class="scp-card">
                    <div class="scp-card-head">
                      <div class="scp-card-title"><span class="ci ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>Personal Information</div>
                      <a href="{{ $scp_edit }}" class="scp-edit">Edit</a>
                    </div>
                    <div class="scp-row"><span class="k">Full Name</span><span class="v">{{ ucwords(@$users->name) }}</span></div>
                    <div class="scp-row"><span class="k">User ID</span><span class="v">{{ @$users->id }}</span></div>
                    <div class="scp-row"><span class="k">Designation</span><span class="v">{{ ucwords(@$users->designation) }}</span></div>
                    <div class="scp-row"><span class="k">Email</span><span class="v">{{ @$users->email }} <span class="scp-pill">Verified</span></span></div>
                    <div class="scp-row"><span class="k">Phone</span><span class="v" style="display:flex;align-items:center;gap:4px;justify-content:flex-end;"><span id="firstPhoneDisplay" style="display:flex;"></span>{{ @$users->mobile }}</span></div>
                    <div class="scp-row"><span class="k">WhatsApp</span><span class="v" style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                      <a href="javascript:;" class="sendwhatsapp_connected_{{ $users->id }} {{ $whatsappConnectCheck ? '' : 'd-none' }}" style="color:#16a34a;background:#50cd890d;padding:3px 9px;border-radius:7px;text-decoration:none;"><i class="fab fa-whatsapp text-success"></i> <span id="f_phone_user{{ $users->id }}">{{ $whatsappConnectCheck }}</span></a>
                      <a href="javascript:;" data-userid="{{ $users->id }}" class="deleteBtn sendwhatsapp_connected_{{ $users->id }} {{ $whatsappConnectCheck ? '' : 'd-none' }}" style="color:#e11d48;background:#f1416c0a;padding:3px 9px;border-radius:7px;cursor:pointer;text-decoration:none;"><i class="fa fa-power-off text-danger"></i> Disconnect</a>
                      <a href="javascript:;" data-userid="{{ $users->id }}" class="sendwhatsapp_panel sendwhatsapp_connect_{{ $users->id }} sendwhatsapp_text_{{ $users->id }} {{ $whatsappConnectCheck ? 'd-none' : '' }}" style="background:#009ef7;color:#fff;padding:5px 11px;border-radius:7px;cursor:pointer;font-weight:600;text-decoration:none;"><i class="fab fa-whatsapp"></i> Click to Connect</a>
                    </span></div>
                  </div>
                  <!-- Company Information -->
                  <div class="scp-card">
                    <div class="scp-card-head">
                      <div class="scp-card-title"><span class="ci ic-purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M8 10h.01M16 10h.01"/></svg></span>Company Information</div>
                      <a href="{{ $scp_edit }}" class="scp-edit">Edit</a>
                    </div>
                    <div class="scp-row"><span class="k">Company Name</span><span class="v">{{ ucwords(@$users->company_name) }}</span></div>
                    <div class="scp-row"><span class="k">Website</span><span class="v">@if(!empty(@$users->website))<a href="{{ (strpos($users->website,'http')===0?'':'//').$users->website }}" target="_blank">{{ $users->website }}</a>@else—@endif</span></div>
                    <div class="scp-row"><span class="k">Country</span><span class="v">{{ ucwords(@$country_name) }}</span></div>
                    <div class="scp-row"><span class="k">State</span><span class="v">{{ ucwords(@$state_name) }}</span></div>
                    <div class="scp-row"><span class="k">City</span><span class="v">{{ ucwords(@$city_name) }}</span></div>
                  </div>
                  <!-- Account Security + Subscription -->
                  <div style="display:flex;flex-direction:column;gap:16px;">
                    <div class="scp-card">
                      <div class="scp-card-head">
                        <div class="scp-card-title"><span class="ci ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>Account Security</div>
                      </div>
                      <div class="scp-row"><span class="k">Password</span><span class="v"><a href="javascript:void(0);" class="reset_password" data-toggle="modal" data-target="#reset_password" style="color:#3b55e6;">Reset Password</a></span></div>
                      <div class="scp-row"><span class="k">Email</span><span class="v">{{ @$users->email }}</span></div>
                      <div class="scp-row"><span class="k">Status</span><span class="v">{{ ucfirst(@$users->status) }}</span></div>
                    </div>
                    <div class="scp-card">
                      <div class="scp-card-head">
                        <div class="scp-card-title"><span class="ci ic-amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg></span>Subscription Plan</div>
                        <a href="javascript:void(0);" class="scp-edit">View Plan</a>
                      </div>
                      <div class="scp-row"><span class="k">Current Plan</span><span class="v"><span class="scp-pill free">{{ ucwords(@$current_package_name ?? 'Free Plan') }}</span></span></div>
                      <div class="scp-row"><span class="k">Active Jobs</span><span class="v">{{ $scp_active }}</span></div>
                      <div class="scp-row"><span class="k">Candidates</span><span class="v">{{ number_format($scp_cand) }}</span></div>
                      <a href="javascript:void(0);" class="scp-upgrade">Upgrade Plan</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="d-none">
              <!--begin::Overview header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Overview</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <a href="{{route('service_company.profile.edit', [GeneralMethod::value_encrypt($user_id)])}}" class="btn btn-primary align-self-center">Edit Profile</a>
                <!--end::Action-->
              </div>
              <!--end::Overview header-->
              <div class="card-body px-0 py-7">
                <!--begin::Full Name-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Full Name</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$users->name); ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Full Name-->
                <!--begin::User ID-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">User ID</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$users->id); ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::User ID-->
                <!--begin::Designation-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Designation</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$users->designation); ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Designation-->
                <!--begin::Email-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Email</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$users->email; ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Email-->
                <!--begin::Phone-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Phone</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> 
                    <div style="display: flex;">
                      <div id="firstPhoneDisplay" class="fw-bolder text-gray-800 fs-6" style="display: flex;"></div>
                    <span class="fw-bolder text-gray-800 fs-6" style="margin-left: 4px;"><?php echo @$users->mobile; ?></span> 
                    </div>

                  </div>
                  <!--end::Col-->
                </div>
                <!--end::Phone-->
                <!--begin::Whatsapp-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Whatsapp</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                    @php
                        $whatsappConnectCheck =  GeneralMethod::whatsappConnectCheck($users->id);
                    @endphp
                  <div class="col-lg-9"> 
                        <a href="javascript:;" class="text-success fw-bolder fs-6 sendwhatsapp_connected_{{ $users->id }} {{ ($whatsappConnectCheck) ? '' : 'd-none' }}" style="background: #50cd890d; width: 107px; padding: 4px; border-radius: 7px;"><i class="fab fa-whatsapp text-success"></i> <span id="f_phone_user{{  $users->id  }}">{{ $whatsappConnectCheck }}</span>  <br>
                        </a>
                        <a href="javascript:;" data-userid="{{ $users->id }}" class="text-danger fw-bold d-block fw-bolder fs-6 sendwhatsapp_connected_{{ $users->id }} {{ ($whatsappConnectCheck) ? '' : 'd-none' }} deleteBtn" style="background: #f1416c0a; width: 107px; padding: 4px; border-radius: 7px;margin-top: 1px;"><i class="fa fa-power-off text-danger"></i> Disconnect</a>
                        <a href="javascript:;" id="" data-userid="{{ $users->id }}" class="text-white d-block fw-bolder fs-6 sendwhatsapp_panel sendwhatsapp_connect_{{ $users->id }} sendwhatsapp_text_{{ $users->id }} {{ ($whatsappConnectCheck) ? 'd-none' : '' }}" style="background: #009ef7; width: 130px; padding: 4px; border-radius: 7px;"><i class="fab fa-whatsapp text-white" style=""></i> <span id="">Click to Connect</span></a>
                  </div>
                  <!--end::Col-->
                </div>
                <!--end::Whatsapp-->
                <!--begin::Company Name-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Company Name</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$users->company_name); ?> </span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Company Name-->
                <!--begin::Company Site-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Company Site</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$users->website); ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Company Site-->
                <div class="row mb-7">
                  <label class="col-lg-3 fw-bold text-muted fs-6">Country</label>
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$countryName); ?></span> </div>
                </div>
                <div class="row mb-7">
                  <label class="col-lg-3 fw-bold text-muted fs-6">State</label>
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$state_name); ?></span> </div>
                </div>
                <div class="row mb-7">
                  <label class="col-lg-3 fw-bold text-muted fs-6">City</label>
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$city_name); ?></span> </div>
                </div>
              </div>
              </div><!-- /d-none legacy overview -->
            </div>
            <!--end::overview Content-->
            <!--begin::Requisitions Content-->
            <div class="tab-pane" id="requisitions" role="tabpanel">
              <!--begin::Requisitions header-->
              {{-- <div class="card-header cursor-pointer px-0"> --}}
                <!--begin::Card title-->
                {{-- <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Requisitions</h3>
                </div> --}}
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Member</a> -->
                <!--end::Action-->
              {{-- </div> --}}
              <!--end::Requisitions header-->
              <!--begin::Requisitions Content-->
              <div class="card-body px-0 py-7">
                <div class="jobx">
                  <div class="jobx-toolbar">
                    <div class="jobx-search">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                      <input type="text" class="teaminputJobSearch" name="teaminputJobSearch" value="" placeholder="Search jobs..." id="kt_tagify_search" autocomplete="off">
                      <input type="hidden" name="inputJobSearch" value="{{ @$userId }}" id="kt_tagify_hidden">
                    </div>
                    <select class="jobx-select" id="statusjb" onchange="fetchTeamJobs()">
                        <option value="all">All Status</option>
                        <option value="active" selected="selected">Active</option>
                        <option value="hold">Hold</option>
                        <option value="inactive">Closed</option>
                        <option value="removed">Removed</option>
                    </select>
                    <select class="jobx-select" id="hiringtypejob" onchange="fetchTeamJobs()">
                        <option value="All">Sort</option>
                        <option value="1" selected="selected">Newest to Oldest (Date)</option>
                        <option value="2">Oldest to Newest (Date)</option>
                        <option value="3">Low to High (Salary)</option>
                        <option value="4">High to Low (Salary)</option>
                        <option value="5">A to Z (Company Wise)</option>
                        <option value="6">Z to A (Company Wise)</option>
                        <option value="7">Full-Time Jobs Only</option>
                        <option value="8">Contract Jobs Only</option>
                    </select>
                  </div>

                  <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                  <!--begin::Job list card-->
                  <div class="table-card">
                    <!--begin::Header-->
                    <div class="table-header">
                      <div class="th">Job Details</div>
                      <div class="th">
                        Experience
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                          <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/>
                          <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                      </div>
                      <div class="th">Hiring &amp; Location</div>
                      <div class="th">Hiring Manager</div>
                      <div class="th">Team</div>
                      <div class="th">Agency</div>
                      <div class="th">
                        Hiring Pipeline
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                          <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                      </div>
                    </div>
                    <!--end::Header-->
                    <div id="teamJobsListajax"></div>
                    <div id="paginationTeamtjobs"></div>
                    @include('sections.service_company.pipeline')
                  </div>
                  <!--end::Job list card-->
                </div>
              </div>
              <!--end::Requisitions Content-->
            </div>
            <!--end::Requisitions Content-->
            <!--begin::Candidate Content-->
            <div class="tab-pane" id="candidate" role="tabpanel">
              <!--begin::Candidate header-->
              {{-- <div class="card-header cursor-pointer px-0"> --}}
                <!--begin::Card title-->
                {{-- <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Candidate</h3>
                </div> --}}
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Edit Candidate</a> -->
                <!--end::Action-->
              {{-- </div> --}}
              <!--end::Candidate header-->
              <!--begin::Candidate Content-->
              <div class="card-body px-0 py-7">


                <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                    <div class="col-lg-4 d-flex align-items-center px-2" style="width:65%;position: relative;">
                        <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch"
                            value="" placeholder="Search..." id="candidate_kt_tagify_2"/>
                            <div style="position: absolute; top: 0px; right: 36px;">
                               <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 8px;"></div>
                            </div>

                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                        <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                            id="candidatesortingfilter" data-dropdown-css-class="w-150px"
                            data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                            onchange="filtersdatagetcandidates()">
                            <option value="Show All" selected="">Sort</option>
                            <option value="1">Newest to Oldest (Upload Date)</option>
                            <option value="2">Oldest to Newest (Upload Date)</option>
                            <!--<option value="3">Low to High (Salary)</option>-->
                            <!--<option value="4">High to Low (Salary)</option>-->
                            <option value="9">Notice Period (Immediate)</option>
                            <option value="5">Low to High (Notice Period)</option>
                            <option value="6">High to Low (Notice Period)</option>
                            <option value="7">High to Low (Experience) </option>
                            <option value="8">Low to High (Experience)</option>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                         <select class="form-select form-select-lg form-select-solid" data-hide-search="true"
                            id="stagefiltercandidate" data-dropdown-css-class="w-150px"
                            data-placeholder="Select an option" style="border: 1px solid #ffffff;"
                            onchange="filtersdatagetcandidates()">
                            <option value="Show All" selected="">All Stage</option>
                            <option value="8">Inreview</option>
                            <option value="1">Sourcing</option>
                            <option value="2">Screening</option>
                            <option value="3">Evaluation</option>
                            <option value="4">Negotiation</option>
                            <option value="5">Offered</option>
                            <option value="6">Joined</option>
                            <option value="7">Rejected</option>
                        </select>
                    </div>

                </div>



                <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class" id="load_application">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      {{-- <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                        </div></th> --}}
                      <th class="">Name</th>
                      {{-- <th class="">Contacts</th> --}}
                      <th class="" data-toggle="tooltip" title="Experience">Exp.</th>
                      <th class="">Salary</th>
                      <th class="" data-toggle="tooltip" title="Notice Period">Notice</th>
                      <th class="">Upload</th>
                      <th class="">Location</th>
                      {{-- <th class="">Source</th> --}}
                      <th class="">Jobs</th>
                      <th class="" data-toggle="tooltip" title="Upload Date">Job Status</th>
                      <th class="">Stage</th>
                    </tr>
                    <!--end::Table row-->
                  </thead>
                  <!--end::Table head-->
                  <tbody id="teamCandidatesListajax"> </tbody>
                </table>
                <div id="paginationTeamtCandidates"></div>
              </div>
              <!--end::Candidate Content-->
            </div>
            <!--end::Candidate Content-->
            <!--begin::Interview Tracker Content-->
            <div class="tab-pane" id="interview_tracker" role="tabpanel">
              <!--begin::Interview Tracker header-->
              {{-- <div class="card-header cursor-pointer px-0"> --}}
                <!--begin::Card title-->
                {{-- <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Interview Tracker</h3>
                </div> --}}
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Stage</a> -->
                <!--end::Action-->
              {{-- </div> --}}
              <!--end::Interview Tracker header-->
              <!--begin::Interview Tracker Content-->
              <div class="card-body px-0 py-7 it-wrap">

                  <!-- Filters Bar -->
                  <div class="it-filters-bar">
                    <div class="it-search-box">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                      <input type="text" name="interviewtrackerinputJobSearch" value="" placeholder="Search candidate, job, recruiter..." id="interviewtracker_kt_tagify_2" autocomplete="off" />
                    </div>
                    <select class="it-sort-select" id="interviewfiltersdataget" onchange="interviewfiltersdataget()">
                        <option value="Show All" selected="">Sort: Newest Interviews</option>
                        <option value="1">Today Interview ({{ date('d M y') }})</option>
                        <option value="Feedback Pending">Feedback Pending</option>
                        <option value="3">New to Old (Interview Date)</option>
                        <option value="4">Old to New (Interview Date)</option>
                    </select>
                    <span class="ms-auto-note" id="total_record_note"></span>
                  </div>

                <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>

                <!-- Table -->
                <div class="it-table-container">
                  <div class="it-table-scroll">
                    <table id="load_interviewSchedule" class="it-data-table">
                      <thead>
                        <tr>
                          <th>
                            <div class="it-checkbox" id="itSelectAllCheckbox" onclick="itToggleSelectAll()">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                          </th>
                          <th style="width:210px">Candidate</th>
                          <th style="width:190px">Job Requisitions</th>
                          <th style="width:170px">Interview Date &amp; Time</th>
                          <th style="width:140px">Interviewers</th>
                          <th style="width:120px">Source</th>
                          <th style="width:120px">Stage</th>
                          <th style="width:150px">Feedback</th>
                        </tr>
                      </thead>
                      <tbody id="teaminterviewJobsListajax"> </tbody>
                    </table>
                  </div>
                  <div id="paginationteaminterviewjobs" class="px-3 py-2"></div>
                </div>
              </div>
              <!--end::Interview Tracker Content-->
            </div>
            <!--end::Interview Tracker  Content-->
            <!--begin::Joinings Content-->
            <div class="tab-pane" id="joinings" role="tabpanel">
              <!--begin::Joinings header-->
              {{-- <div class="card-header cursor-pointer px-0"> --}}
                <!--begin::Card title-->
                {{-- <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Joinings</h3>
                </div> --}}
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Stage</a> -->
                <!--end::Action-->
              {{-- </div> --}}
              <!--end::Joinings header-->
              <!--begin::Joinings Content-->
              <div class="card-body px-0 py-7">

                  <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                        <div class="col-lg-4 d-flex align-items-center px-2" style="width:60%;position: relative;">
                          <input type="text" class="form-control form-control-solid ps-3" name="joininputJobSearch" value="" placeholder="Search..." id="join_kt_tagify_2" />
                          <div style="position: absolute; top: 0px; right: 36px;">
                            <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 8px;"></div>
                        </div>

                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 25%;">
                          <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="joinsortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdatagetjoin()">
                              <option value="Show All" selected="">Sort</option>
                              <option value="12">Newest to Oldest (Join Date)</option>
                              <option value="13">Oldest to Newest (Join Date)</option>
                              <option value="14">Low to High (Offer Amount)</option>
                              <option value="15">High to Low (Offer Amount)</option>
                          </select>
                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                          <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdatagetjoin()">
                              <option value="" selected="selected">Location</option>
                              @if(@$countries)
                                  @foreach($countries as $val_co)
                                  <option value="{{ $val_co->id }}">{{ $val_co->country_name }}</option>
                                  @endforeach
                              @endif
                              
                          </select>
                        </div>
                  </div>


                <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                <table id="myTable_joining" class="table align-middle table-row-dashed fs-6 gy-5">
                  <thead>
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                        </div></th>
                      <th class="">Name</th>
                      <th class="">Jobs</th>
                      <th class="">Job Status</th>
                      <th class="">Source</th>
                      <th class="">Offer Amount</th>
                      <th class="">Joining Date</th>
                      <th class="">Stage</th>
                    </tr>
                  </thead>
                  <tbody id="teamjoiningJobsListajax"> </tbody>
                </table>
                <div id="paginationteamjoiningjobs"></div>
              </div>
              <!--end::Joinings Content-->
            </div>
            <!--end::Joinings Content-->
            <!--begin::Settings Content-->
            <div class="tab-pane" id="settings" role="tabpanel">
              @php
                $sstWa     = GeneralMethod::whatsappConnectCheck(@$users->id);
                $sstEmailOk = true;            // signed in => email usable
                $sstPwdOk   = true;            // password set
                $sst2fa     = false;           // no backend flag yet
                $sstAlerts  = false;           // no backend flag yet
                $sstScore   = ($sstEmailOk?25:0) + ($sstPwdOk?25:0) + ($sst2fa?25:0) + ($sstAlerts?25:0);
                $sstCirc    = 2 * 3.14159 * 52;
                $sstDash    = round($sstScore/100 * $sstCirc, 1);
                $sstLabel   = $sstScore >= 90 ? 'Excellent' : ($sstScore >= 70 ? 'Good' : ($sstScore >= 40 ? 'Fair' : 'Weak'));
              @endphp
              <div class="sst-wrap">
                <div class="sst-grid">
                  <!-- LEFT column -->
                  <div class="sst-col">
                    <!-- Account Security -->
                    <div class="sst-card">
                      <div class="sst-head">
                        <span class="sst-head-ic ic-ind"><i class="fas fa-shield-alt"></i></span>
                        <span class="sst-head-t">Account Security</span>
                      </div>
                      <!-- Email -->
                      <div class="sst-item">
                        <span class="sst-it-ic ic-ind"><i class="fas fa-envelope"></i></span>
                        <div class="sst-it-body">
                          <div class="sst-it-top">
                            <span class="sst-it-title">Email Address</span>
                            <span class="sst-chip ok"><i class="fas fa-check-circle"></i> Verified</span>
                          </div>
                          <div class="sst-it-val">{{ @$users->email }}</div>
                          <div class="sst-it-sub">Used for account sign in and notifications</div>
                        </div>
                        <a href="javascript:void(0);" class="sst-btn" data-sst-soon>Change Email</a>
                      </div>
                      <!-- Password -->
                      <div class="sst-item">
                        <span class="sst-it-ic ic-vio"><i class="fas fa-lock"></i></span>
                        <div class="sst-it-body">
                          <div class="sst-it-top">
                            <span class="sst-it-title">Password</span>
                            <span class="sst-chip ok"><i class="fas fa-check"></i> Strong</span>
                          </div>
                          <div class="sst-it-val">•••••••••••••</div>
                          <div class="sst-it-sub">Keep your password private and unique</div>
                        </div>
                        <a href="javascript:void(0);" class="sst-btn reset_password" data-toggle="modal" data-target="#reset_password">Change Password</a>
                      </div>
                      <!-- 2FA -->
                      <div class="sst-item">
                        <span class="sst-it-ic ic-grn"><i class="fas fa-user-shield"></i></span>
                        <div class="sst-it-body">
                          <div class="sst-it-top">
                            <span class="sst-it-title">Two-Factor Authentication</span>
                            <span class="sst-chip {{ $sst2fa?'ok':'off' }}">{{ $sst2fa?'Enabled':'Disabled' }}</span>
                          </div>
                          <div class="sst-it-sub">Add an extra layer of security to your account</div>
                        </div>
                        <a href="javascript:void(0);" class="sst-btn" data-sst-soon>{{ $sst2fa?'Manage':'Enable 2FA' }}</a>
                      </div>
                      <!-- Login Alerts -->
                      <div class="sst-item sst-last">
                        <span class="sst-it-ic ic-amb"><i class="fas fa-bell"></i></span>
                        <div class="sst-it-body">
                          <div class="sst-it-top">
                            <span class="sst-it-title">Login Alerts</span>
                            <span class="sst-chip {{ $sstAlerts?'ok':'off' }}">{{ $sstAlerts?'Enabled':'Disabled' }}</span>
                          </div>
                          <div class="sst-it-sub">Get notified when someone logs in to your account</div>
                        </div>
                        <a href="javascript:void(0);" class="sst-btn" data-sst-soon>{{ $sstAlerts?'Manage':'Enable Alerts' }}</a>
                      </div>
                    </div>

                    <!-- Notification Preferences -->
                    <div class="sst-card">
                      <div class="sst-head">
                        <span class="sst-head-ic ic-amb"><i class="fas fa-bell"></i></span>
                        <span class="sst-head-t">Notification Preferences</span>
                      </div>
                      <div class="sst-pref">
                        <span class="sst-it-ic ic-ind"><i class="fas fa-envelope"></i></span>
                        <div class="sst-it-body">
                          <div class="sst-it-title">Email Notifications</div>
                          <div class="sst-it-sub">Receive important updates via email</div>
                        </div>
                        <label class="sst-switch"><input type="checkbox" checked disabled><span></span></label>
                      </div>
                      <div class="sst-pref">
                        <span class="sst-it-ic ic-grn"><i class="fab fa-whatsapp"></i></span>
                        <div class="sst-it-body">
                          <div class="sst-it-title">WhatsApp Automation</div>
                          <div class="sst-it-sub">{{ $sstWa ? 'Connected · '.$sstWa : 'Send messages automatically via WhatsApp' }}</div>
                        </div>
                        <label class="sst-switch"><input type="checkbox" {{ $sstWa?'checked':'' }} disabled><span></span></label>
                      </div>
                      <div class="sst-pref">
                        <span class="sst-it-ic ic-vio"><i class="fas fa-briefcase"></i></span>
                        <div class="sst-it-body">
                          <div class="sst-it-title">Job Alerts</div>
                          <div class="sst-it-sub">Get notified about job activity and deadlines</div>
                        </div>
                        <label class="sst-switch"><input type="checkbox" checked disabled><span></span></label>
                      </div>
                      <div class="sst-pref sst-last">
                        <span class="sst-it-ic ic-ind"><i class="fas fa-calendar-check"></i></span>
                        <div class="sst-it-body">
                          <div class="sst-it-title">Automatic Interview Reminders</div>
                          <div class="sst-it-sub">Send interview reminders automatically via email &amp; WhatsApp</div>
                        </div>
                        <label class="sst-switch"><input type="checkbox" checked disabled><span></span></label>
                      </div>
                      <div class="sst-note"><i class="fas fa-info-circle"></i> We'll send automated reminders to interviewers and candidates to reduce no-shows.</div>
                    </div>
                  </div>

                  <!-- RIGHT column -->
                  <div class="sst-col">
                    <!-- Security Status -->
                    <div class="sst-card">
                      <div class="sst-head">
                        <span class="sst-head-ic ic-grn"><i class="fas fa-shield-alt"></i></span>
                        <span class="sst-head-t">Security Status</span>
                      </div>
                      <div class="sst-ring-row">
                        <div class="sst-ring">
                          <svg viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#eef1f6" stroke-width="11"/>
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#22c55e" stroke-width="11" stroke-linecap="round"
                              stroke-dasharray="{{ $sstDash }} {{ $sstCirc }}" transform="rotate(-90 60 60)"/>
                          </svg>
                          <div class="sst-ring-c">
                            <span class="sst-ring-pct">{{ $sstScore }}%</span>
                            <span class="sst-ring-lbl">{{ $sstLabel }}</span>
                          </div>
                        </div>
                        <ul class="sst-checks">
                          <li class="ok"><i class="fas fa-check-circle"></i> Verified Email</li>
                          <li class="ok"><i class="fas fa-check-circle"></i> Strong Password</li>
                          <li class="{{ $sst2fa?'ok':'warn' }}"><i class="fas fa-{{ $sst2fa?'check-circle':'exclamation-triangle' }}"></i> Two-Factor Authentication</li>
                          <li class="{{ $sstAlerts?'ok':'warn' }}"><i class="fas fa-{{ $sstAlerts?'check-circle':'exclamation-triangle' }}"></i> Login Alerts</li>
                        </ul>
                      </div>
                      <a href="javascript:void(0);" class="sst-btn-pri" data-sst-soon><i class="fas fa-arrow-up"></i> Improve Security</a>
                    </div>

                    <!-- Security Tips -->
                    <div class="sst-card">
                      <div class="sst-head">
                        <span class="sst-head-ic ic-amb"><i class="fas fa-lightbulb"></i></span>
                        <span class="sst-head-t">Security Tips</span>
                      </div>
                      <a href="javascript:void(0);" class="sst-tip" data-sst-soon>
                        <span class="sst-it-ic ic-grn"><i class="fas fa-key"></i></span>
                        <div class="sst-it-body"><div class="sst-it-title">Use a strong password</div><div class="sst-it-sub">Use a mix of letters, numbers &amp; symbols</div></div>
                        <i class="fas fa-chevron-right sst-chev"></i>
                      </a>
                      <a href="javascript:void(0);" class="sst-tip" data-sst-soon>
                        <span class="sst-it-ic ic-vio"><i class="fas fa-user-shield"></i></span>
                        <div class="sst-it-body"><div class="sst-it-title">Enable Two-Factor Authentication</div><div class="sst-it-sub">Add an extra layer of security</div></div>
                        <i class="fas fa-chevron-right sst-chev"></i>
                      </a>
                      <a href="javascript:void(0);" class="sst-tip" data-sst-soon>
                        <span class="sst-it-ic ic-amb"><i class="fas fa-user-secret"></i></span>
                        <div class="sst-it-body"><div class="sst-it-title">Don't share your login details</div><div class="sst-it-sub">Keep your account information private</div></div>
                        <i class="fas fa-chevron-right sst-chev"></i>
                      </a>
                      <a href="javascript:void(0);" class="sst-tip sst-last" data-sst-soon>
                        <span class="sst-it-ic ic-ind"><i class="fas fa-desktop"></i></span>
                        <div class="sst-it-body"><div class="sst-it-title">Review your active sessions</div><div class="sst-it-sub">Sign out from devices you don't use</div></div>
                        <i class="fas fa-chevron-right sst-chev"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--end begin::Settings Content-->

            <!--begin::my rank Content-->
            <div class="tab-pane" id="myRank" role="tabpanel">
              @php
                $rkMonths = collect($rankings['months'] ?? [])->values();
                $rkCur    = $rkMonths->get(0);
                $rkPrev   = $rkMonths->get(1);
                $curI  = $rankings['internalRanks'][$rkCur]  ?? ['rank'=>'-','score'=>0];
                $prevI = $rankings['internalRanks'][$rkPrev] ?? ['rank'=>'-','score'=>0];
                $curG  = $rankings['globalRanks'][$rkCur]    ?? ['rank'=>'-','score'=>0];
                $prevG = $rankings['globalRanks'][$rkPrev]   ?? ['rank'=>'-','score'=>0];
                $scoreDelta = (is_numeric($curI['score'])?$curI['score']:0) - (is_numeric($prevI['score'])?$prevI['score']:0);
                $iRankDelta = (is_numeric($prevI['rank'])?$prevI['rank']:0) - (is_numeric($curI['rank'])?$curI['rank']:0);
                $gRankDelta = (is_numeric($prevG['rank'])?$prevG['rank']:0) - (is_numeric($curG['rank'])?$curG['rank']:0);
                $prevLbl  = $rkPrev ? date('M Y', strtotime($rkPrev.'-01')) : 'last month';
                // trend (oldest -> newest, last 12)
                $rkTrend  = $rkMonths->slice(0,12)->reverse()->values();
                $rkTLabels = $rkTrend->map(fn($m)=>date('M y', strtotime($m.'-01')))->all();
                $rkTScores = $rkTrend->map(fn($m)=>(int)($rankings['internalRanks'][$m]['score'] ?? 0))->all();
                // journey milestones derived from real ranks (oldest first)
                $asc = $rkMonths->reverse()->values();
                $mTop1=$mTop5=$mTop10=$mJoin=null;
                foreach($asc as $m){
                  $r = $rankings['internalRanks'][$m]['rank'] ?? null;
                  if($mJoin===null) $mJoin=$m;
                  if($mTop10===null && is_numeric($r) && $r<=10) $mTop10=$m;
                  if($mTop5===null  && is_numeric($r) && $r<=5)  $mTop5=$m;
                  if($mTop1===null  && is_numeric($r) && $r==1)  $mTop1=$m;
                }
                $fmtM = fn($m)=>$m?date('M Y',strtotime($m.'-01')):'—';
                $rkInitial = strtoupper(substr(@$users->name ?? 'U',0,1));
              @endphp
              @php
                $rkTRanks = $rkTrend->map(fn($m)=> (is_numeric($rankings['internalRanks'][$m]['rank'] ?? null) ? (int)$rankings['internalRanks'][$m]['rank'] : null))->all();
                $rkValidRanks = array_filter($rkTRanks, fn($v)=>$v!==null);
                $rkMaxRank = $rkValidRanks ? max($rkValidRanks) : 10;
                $lbTop   = collect($leaderboard['top'] ?? []);
                $lbMe    = $leaderboard['me'] ?? null;
                $lbTotal = $leaderboard['total'] ?? 0;
                $myRankN = is_numeric($curI['rank']) ? (int)$curI['rank'] : null;
                $topPct  = ($lbTotal>0 && $myRankN) ? max(1,(int)ceil($myRankN/$lbTotal*100)) : null;
                $tier    = !$myRankN ? 'Recruiter' : ($myRankN==1 ? 'Platinum Recruiter' : ($myRankN<=5 ? 'Gold Recruiter' : ($myRankN<=10 ? 'Silver Recruiter' : 'Bronze Recruiter')));
                $tierCls = !$myRankN ? 'bronze' : ($myRankN==1 ? 'platinum' : ($myRankN<=5 ? 'gold' : ($myRankN<=10 ? 'silver' : 'bronze')));
                // monthly history with rank-change columns (rows newest-first)
                $mArr=$rkMonths->values()->all(); $hist=[];
                for($i=0;$i<count($mArr);$i++){
                  $m=$mArr[$i]; $older=$mArr[$i+1]??null;
                  $ir=$rankings['internalRanks'][$m]??['rank'=>'-','score'=>0];
                  $gr=$rankings['globalRanks'][$m]??['rank'=>'-','score'=>0];
                  $oi=$older?($rankings['internalRanks'][$older]??null):null;
                  $og=$older?($rankings['globalRanks'][$older]??null):null;
                  $sc=is_numeric($ir['score'])?$ir['score']:0;
                  $osc=($oi&&is_numeric($oi['score']))?$oi['score']:null;
                  $scoreChg=$osc===null?null:$sc-$osc;
                  $scorePct=($osc&&$osc>0)?round($scoreChg/$osc*100,1):null;
                  $iChg=($oi&&is_numeric($oi['rank'])&&is_numeric($ir['rank']))?($oi['rank']-$ir['rank']):null;
                  $gChg=($og&&is_numeric($og['rank'])&&is_numeric($gr['rank']))?($og['rank']-$gr['rank']):null;
                  $hist[]=compact('m','ir','gr','sc','scoreChg','scorePct','iChg','gChg');
                }
                $rkSpark = '<svg viewBox="0 0 80 32" preserveAspectRatio="none"><polyline points="0,26 14,20 28,23 42,12 56,15 70,6 80,9" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
              @endphp
              <div class="srk-wrap">
                <!-- Stat cards -->
                <div class="srk-stats">
                  <div class="srk-stat"><span class="srk-st-ic ic-ind"><i class="fas fa-users"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Candidates Added</div><div class="srk-st-num">{{ @$scpCandidatesTotal ?? 0 }}</div></div><span class="srk-spark ind">{!! $rkSpark !!}</span></div>
                  <div class="srk-stat"><span class="srk-st-ic ic-grn"><i class="fas fa-calendar-check"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Interviews Conducted</div><div class="srk-st-num">{{ @$scpInterviewsTotal ?? 0 }}</div></div><span class="srk-spark grn">{!! $rkSpark !!}</span></div>
                  <div class="srk-stat"><span class="srk-st-ic ic-vio"><i class="fas fa-briefcase"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Offers Made</div><div class="srk-st-num">{{ @$candidateValuesTotaloffer ?? 0 }}</div></div><span class="srk-spark vio">{!! $rkSpark !!}</span></div>
                  <div class="srk-stat"><span class="srk-st-ic ic-amb"><i class="fas fa-handshake"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Joinings</div><div class="srk-st-num">{{ @$scpJoiningsTotal ?? 0 }}</div></div><span class="srk-spark amb">{!! $rkSpark !!}</span></div>
                </div>

                <!-- Leaderboard + Trend + Journey -->
                <div class="srk-mid3">
                  <!-- Leaderboard -->
                  <div class="srk-card">
                    <div class="srk-head"><span class="srk-head-t">Leaderboard <small>(Top 5)</small></span></div>
                    @php $lbTop5 = $lbTop->take(5); @endphp
                    @if($lbTop5->count())
                      <div class="srk-lb">
                        @foreach($lbTop5 as $row)
                          @php $isMe = (int)($row['id'] ?? 0) === (int)(@$users->id ?? -1); $rk=$row['rank']; @endphp
                          <div class="srk-lb-row {{ $isMe?'me':'' }}">
                            <span class="srk-lb-rk {{ $rk==1?'g1':($rk==2?'g2':($rk==3?'g3':'')) }}">{{ $rk<=3 ? '' : $rk }}@if($rk<=3)<i class="fas fa-medal"></i>@endif</span>
                            @if(!empty($row['image']))
                              <img class="srk-lb-ava" src="{{ $row['image'] }}" alt="">
                            @else
                              <span class="srk-lb-ava srk-lb-ini">{{ strtoupper(substr($row['name'] ?? 'U',0,1)) }}</span>
                            @endif
                            <span class="srk-lb-name">{{ $row['name'] ?? 'User' }} @if($isMe)<em class="srk-lb-you">You</em>@endif</span>
                            <span class="srk-lb-score">{{ $row['score'] ?? 0 }}</span>
                          </div>
                        @endforeach
                      </div>
                      @if($lbMe && !$lbTop5->pluck('id')->contains($lbMe['id']))
                        <div class="srk-lb-row me sep">
                          <span class="srk-lb-rk">{{ $lbMe['rank'] }}</span>
                          <span class="srk-lb-ava srk-lb-ini">{{ strtoupper(substr($lbMe['name'] ?? 'U',0,1)) }}</span>
                          <span class="srk-lb-name">{{ $lbMe['name'] ?? 'You' }} <em class="srk-lb-you">You</em></span>
                          <span class="srk-lb-score">{{ $lbMe['score'] ?? 0 }}</span>
                        </div>
                      @endif
                    @else
                      <div class="srk-empty">Leaderboard data is not available yet.</div>
                    @endif
                  </div>

                  <!-- Rank Trend -->
                  <div class="srk-card">
                    <div class="srk-head"><span class="srk-head-t">Rank Trend</span><span class="srk-head-sub">Internal · {{ count($rkTLabels) }} months</span></div>
                    <div class="srk-chart"><canvas id="srk_trend_chart"></canvas></div>
                    @if($mTop1)
                      <div class="srk-trend-note"><i class="fas fa-check-circle"></i> You reached #1 internally in {{ $fmtM($mTop1) }}. Keep up the great work!</div>
                    @endif
                  </div>

                  <!-- Rank Journey -->
                  <div class="srk-card">
                    <div class="srk-head"><span class="srk-head-t">Rank Journey</span></div>
                    <ul class="srk-journey">
                      <li class="{{ $mTop1?'done':'todo' }}"><span class="srk-jd ic-amb"><i class="fas fa-crown"></i></span><div><div class="srk-j-t">Reached #1 Internally</div><div class="srk-j-s">{{ $mTop1 ? $fmtM($mTop1).' · top recruiter' : 'Not yet reached' }}</div></div></li>
                      <li class="{{ $mTop5?'done':'todo' }}"><span class="srk-jd ic-ind"><i class="fas fa-chart-line"></i></span><div><div class="srk-j-t">Entered Top 5</div><div class="srk-j-s">{{ $mTop5 ? $fmtM($mTop5) : 'Not yet reached' }}</div></div></li>
                      <li class="{{ $mTop10?'done':'todo' }}"><span class="srk-jd ic-vio"><i class="fas fa-star"></i></span><div><div class="srk-j-t">Entered Top 10</div><div class="srk-j-s">{{ $mTop10 ? $fmtM($mTop10) : 'Not yet reached' }}</div></div></li>
                      <li class="done"><span class="srk-jd ic-grn"><i class="fas fa-flag"></i></span><div><div class="srk-j-t">Joined Leaderboard</div><div class="srk-j-s">{{ $fmtM($mJoin) }} · started your journey</div></div></li>
                    </ul>
                  </div>
                </div>

                <!-- History -->
                <div class="srk-card">
                  <div class="srk-head"><span class="srk-head-t">Monthly Performance History</span><span class="srk-head-sub">Track your growth and performance over time</span></div>
                  <div class="srk-table-wrap">
                    <table class="srk-table">
                      <thead><tr><th>Month</th><th>Score</th><th>Score Change</th><th>Internal Rank</th><th>Rank Change (Int.)</th><th>Global Rank</th><th>Rank Change (Global)</th></tr></thead>
                      <tbody>
                        @foreach($hist as $h)
                          <tr>
                            <td class="srk-td-m">{{ date('M Y', strtotime($h['m'].'-01')) }}</td>
                            <td class="srk-td-s">{{ $h['sc'] }}</td>
                            <td>
                              @if($h['scoreChg']===null)<span class="srk-flat">—</span>
                              @elseif($h['scoreChg']>=0)<span class="srk-up"><i class="fas fa-arrow-up"></i> {{ $h['scoreChg'] }}{{ $h['scorePct']!==null?' ('.$h['scorePct'].'%)':'' }}</span>
                              @else<span class="srk-down"><i class="fas fa-arrow-down"></i> {{ abs($h['scoreChg']) }}{{ $h['scorePct']!==null?' ('.$h['scorePct'].'%)':'' }}</span>@endif
                            </td>
                            <td class="srk-td-r">#{{ $h['ir']['rank'] }}</td>
                            <td>
                              @if($h['iChg']===null)<span class="srk-flat">—</span>
                              @elseif($h['iChg']>0)<span class="srk-up"><i class="fas fa-arrow-up"></i> {{ $h['iChg'] }}</span>
                              @elseif($h['iChg']<0)<span class="srk-down"><i class="fas fa-arrow-down"></i> {{ abs($h['iChg']) }}</span>
                              @else<span class="srk-flat">—</span>@endif
                            </td>
                            <td class="srk-td-r">#{{ $h['gr']['rank'] }}</td>
                            <td>
                              @if($h['gChg']===null)<span class="srk-flat">—</span>
                              @elseif($h['gChg']>0)<span class="srk-up"><i class="fas fa-arrow-up"></i> {{ $h['gChg'] }}</span>
                              @elseif($h['gChg']<0)<span class="srk-down"><i class="fas fa-arrow-down"></i> {{ abs($h['gChg']) }}</span>
                              @else<span class="srk-flat">—</span>@endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <script>
                (function(){
                  var done=false;
                  function drawSrk(){
                    if(done || typeof Chart==='undefined') return;
                    var el=document.getElementById('srk_trend_chart'); if(!el) return;
                    if(el.offsetParent===null) return; // tab still hidden
                    done=true;
                    var ctx=el.getContext('2d');
                    var g=ctx.createLinearGradient(0,0,0,220);
                    g.addColorStop(0,'rgba(59,85,230,0.22)'); g.addColorStop(1,'rgba(59,85,230,0.02)');
                    var maxR={{ ($rkMaxRank ?? 10) + 1 }};
                    new Chart(ctx,{type:'line',data:{labels:{!! json_encode($rkTLabels) !!},datasets:[{data:{!! json_encode($rkTRanks) !!},borderColor:'#3b55e6',backgroundColor:g,borderWidth:2,fill:true,tension:0.4,pointRadius:3,pointBackgroundColor:'#3b55e6',spanGaps:true}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return 'Rank #'+c.parsed.y;}}}},scales:{x:{grid:{display:false},ticks:{font:{size:11,family:'Poppins'}}},y:{reverse:true,min:1,suggestedMax:maxR,ticks:{stepSize:1,precision:0,font:{size:11,family:'Poppins'},callback:function(v){return '#'+v;}},grid:{color:'#f0f2f7'}}}}});
                  }
                  document.addEventListener('click',function(e){var a=e.target.closest('a[href="#myRank"]');if(a)setTimeout(drawSrk,60);});
                  if(window.jQuery){jQuery(function($){$('a[href="#myRank"]').on('shown.bs.tab',drawSrk);});}
                  setTimeout(drawSrk,400);
                  // friendly notice for not-yet-wired security actions
                  document.addEventListener('click',function(e){
                    var s=e.target.closest('[data-sst-soon]'); if(!s) return;
                    e.preventDefault();
                    if(typeof Swal!=='undefined'){Swal.fire({icon:'info',title:'Coming soon',text:'This security option will be available shortly.',confirmButtonColor:'#3b55e6'});}
                  });
                })();
              </script>
            </div>
            <!--end begin::my rank Content-->

            <!--begin::candidateDashboard Content-->
                <div class="tab-pane" id="candidateDashboard" role="tabpanel">
                  @php
                    // Dashboard cards mirror the profile header stat cards ($scp_*) exactly —
                    // never use alternative fallback variables for the 4 shared metrics.
                    $sdbActive   = $scp_active;   // same as top card
                    $sdbHold     = (int)($hold_count ?? 0);
                    $sdbClosed   = (int)($inactive_count ?? 0);
                    $sdbAdded    = $scp_cand;      // same as top card
                    $sdbInReview = (int)($inreview_count ?? 0);
                    $sdbSourcing = (int)($sourcing_count ?? 0);
                    $sdbScreen   = (int)($screening_count ?? 0);
                    $sdbEval     = (int)($evaluation_count ?? 0);
                    $sdbNego     = (int)($negotiation_count ?? 0);
                    $sdbOffer    = (int)($scpOffersTotal ?? $offer_count ?? 0);
                    $sdbJoined   = $scp_join;      // same as top card
                    $sdbReject   = (int)($Rejected_count ?? 0);
                    $sdbInterviews = $scp_intv;    // same as top card
                    $sdbConv     = $sdbAdded>0 ? round($sdbJoined/$sdbAdded*100,1) : 0;
                    $sdbWin = '';   // date-range window removed — cards show overall data
                    $sdbStages = [
                      ['In Review',$sdbInReview,'#bdbd26'],
                      ['Sourcing',$sdbSourcing,'#000000'],
                      ['Screening',$sdbScreen,'#c000ff'],
                      ['Evaluation',$sdbEval,'#2896ff'],
                      ['Negotiation',$sdbNego,'#ff4098'],
                      ['Offer',$sdbOffer,'#ffab00'],
                      ['Joined',$sdbJoined,'#06c732'],
                      ['Rejected',$sdbReject,'#ff0202'],
                    ];
                  @endphp
                  <div class="sdb-wrap">
                    <!-- Header -->
                    <div class="sdb-top">
                      <div>
                        <div class="sdb-title">My Dashboard <span>&#128075;</span></div>
                        <div class="sdb-sub">Here's what's happening with your recruitment.</div>
                      </div>
                      @if($sdbWin)<span class="sdb-window"><i class="far fa-calendar-alt"></i> {{ $sdbWin }}</span>@endif
                    </div>

                    <!-- Stat cards -->
                    <div class="sdb-stats">
                      <div class="sdb-stat"><span class="sdb-st-ic ic-ind"><i class="fas fa-briefcase"></i></span><div class="sdb-st-lbl">Active Jobs</div><div class="sdb-st-num">{{ $sdbActive }}</div></div>
                      <div class="sdb-stat"><span class="sdb-st-ic ic-grn"><i class="fas fa-users"></i></span><div class="sdb-st-lbl">Candidates Added</div><div class="sdb-st-num">{{ $sdbAdded }}</div></div>
                      <div class="sdb-stat"><span class="sdb-st-ic ic-blu"><i class="fas fa-calendar-check"></i></span><div class="sdb-st-lbl">Interviews</div><div class="sdb-st-num">{{ $sdbInterviews }}</div></div>
                      <div class="sdb-stat"><span class="sdb-st-ic ic-amb"><i class="fas fa-file-signature"></i></span><div class="sdb-st-lbl">Offers</div><div class="sdb-st-num">{{ $sdbOffer }}</div></div>
                      <div class="sdb-stat"><span class="sdb-st-ic ic-vio"><i class="fas fa-handshake"></i></span><div class="sdb-st-lbl">Joinings</div><div class="sdb-st-num">{{ $sdbJoined }}</div></div>
                      <div class="sdb-stat"><span class="sdb-st-ic ic-rose"><i class="fas fa-chart-line"></i></span><div class="sdb-st-lbl">Joining Rate</div><div class="sdb-st-num">{{ $sdbConv }}%</div></div>
                    </div>

                    <!-- Pipeline + Action Center -->
                    <div class="sdb-mid">
                      <!-- Pipeline -->
                      <div class="sdb-card">
                        <div class="sdb-head">
                          <span class="sdb-head-t">Recruitment Pipeline</span>
                          @if($sdbWin)<span class="sdb-head-sub">{{ $sdbWin }}</span>@endif
                        </div>
                        <div class="sdb-funnel">
                          @foreach($sdbStages as $i => $stg)
                            @php $w = max(34, 100 - $i*8.5); @endphp
                            <div class="sdb-fn-row">
                              <div class="sdb-fn-bar" style="width:{{ $w }}%;background:{{ $stg[2] }};">{{ $stg[1] }}</div>
                              <div class="sdb-fn-lbl"><span>{{ $stg[0] }}</span><small>Candidates</small></div>
                            </div>
                          @endforeach
                        </div>
                        <div class="sdb-fn-foot">
                          <div><div class="sdb-ff-lbl">Total Added</div><div class="sdb-ff-num">{{ $sdbAdded }}</div></div>
                          <div><div class="sdb-ff-lbl">Total Joined</div><div class="sdb-ff-num">{{ $sdbJoined }}</div></div>
                          <div><div class="sdb-ff-lbl">Conversion Rate</div><div class="sdb-ff-num sdb-green">{{ $sdbConv }}%</div></div>
                        </div>
                      </div>

                      <!-- Action Center -->
                      <div class="sdb-card">
                        <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-bolt sdb-bolt"></i> Action Center</span></div>
                        <div class="sdb-ac">
                          <div class="sdb-ac-row"><span class="sdb-ac-ic ic-ind"><i class="fas fa-briefcase"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Active Jobs</div><div class="sdb-ac-s">Jobs currently open for hiring</div></div><span class="sdb-ac-n">{{ $sdbActive }}</span></div>
                          <div class="sdb-ac-row"><span class="sdb-ac-ic ic-amb"><i class="fas fa-file-signature"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Offer Stage</div><div class="sdb-ac-s">Candidates currently at offer stage</div></div><span class="sdb-ac-n">{{ $sdbOffer }}</span></div>
                          <div class="sdb-ac-row"><span class="sdb-ac-ic ic-vio"><i class="fas fa-comments-dollar"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">In Negotiation</div><div class="sdb-ac-s">Candidates in salary negotiation</div></div><span class="sdb-ac-n">{{ $sdbNego }}</span></div>
                          <div class="sdb-ac-row"><span class="sdb-ac-ic ic-grn"><i class="fas fa-handshake"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Joined</div><div class="sdb-ac-s">Candidates who have joined</div></div><span class="sdb-ac-n">{{ $sdbJoined }}</span></div>
                          <div class="sdb-ac-row"><span class="sdb-ac-ic ic-rose"><i class="fas fa-pause-circle"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Jobs on Hold</div><div class="sdb-ac-s">Jobs temporarily paused</div></div><span class="sdb-ac-n">{{ $sdbHold }}</span></div>
                        </div>
                        <a href="#requisitions" data-toggle="tab" class="sdb-ac-all">View All Jobs <i class="fas fa-arrow-right"></i></a>
                      </div>
                    </div>

                    <!-- Trend charts -->
                    <div class="sdb-charts">
                      <div class="sdb-card">
                        <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-file-signature ic-t-amb"></i> Offer Trends</span><span class="sdb-head-sub">Last 6 Months</span></div>
                        <div class="sdb-chart"><canvas id="sdb_offer"></canvas></div>
                      </div>
                      <div class="sdb-card">
                        <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-handshake ic-t-grn"></i> Joinings Trend</span><span class="sdb-head-sub">Last 6 Months</span></div>
                        <div class="sdb-chart"><canvas id="sdb_join"></canvas></div>
                      </div>
                      <div class="sdb-card">
                        <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-calendar-check ic-t-vio"></i> Interviews Trend</span><span class="sdb-head-sub">Last 6 Months</span></div>
                        <div class="sdb-chart"><canvas id="sdb_interview"></canvas></div>
                      </div>
                      <div class="sdb-card">
                        <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-users ic-t-blu"></i> Candidates Added</span><span class="sdb-head-sub">Last 7 Days</span></div>
                        <div class="sdb-chart"><canvas id="sdb_candidates"></canvas></div>
                      </div>
                    </div>
                  </div>
                </div>
            <!--end begin::candidateDashboard Content-->

          </div>
        </div>
        <!--end::Body-->
      </div>
      <!--end::Tabs-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
<!--begin::Reset password Modal-->
<div class="modal" id="reset_password" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content">
      <div class="modal-header" id="add_team_member_header">
        <h2 class="fw-bolder">Reset Password</h2>
        <div class="btn btn-icon btn-sm btn-active-light-danger" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-xl-8 py-5">
        <form id="mteam_member_form" class="form" action="#">
          <div class="alert alert-success alert-dismissible fade show" id="success-alert" style="display:none"> <strong>Success!</strong> Password Reset successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="warning-alert" style="display:none"> <strong>Warning!</strong> Both password must me same
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="not-match-alert" style="display:none"> <strong>Warning!</strong> Current password does not match
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          <input type="hidden" name="mteam_id" id="mteam_id" value="" />
          <div class="row mb-1">
            
            <div class="col-lg-12">
              <div class="fv-row mb-0 fv-plugins-icon-container">
                <label for="currentpassword" class="form-label fs-6 fw-bolder mb-3">Current Password</label>
                <input type="password" class="form-control form-control-lg form-control-solid" name="currentpassword" id="currentpassword">
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="fv-row mb-0 fv-plugins-icon-container">
                <label for="newpassword" class="form-label fs-6 fw-bolder mb-3">New Password</label>
                <input type="password" class="form-control form-control-lg form-control-solid" name="mteampassword" id="mteampassword">
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="fv-row mb-0 fv-plugins-icon-container">
                <label for="confirmpassword" class="form-label fs-6 fw-bolder mb-3">Confirm New Password</label>
                <input type="password" class="form-control form-control-lg form-control-solid" name="mteamcpassword" id="mteamcpassword">
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
          </div>
          <div class="form-text mb-5">Password must be at least 8 character and contain symbols</div>
          <div class="d-flex">
            <button id="m_resetpassword_submit" type="button" class="btn btn-primary me-2 px-6">Update Password</button>
            <button id="m_resetpassword_cancel" type="button" data-dismiss="modal" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Reset password Modal-->
<input type="hidden" name="userId" id="userId" value="<?php echo @$users->id; ?>" />
<?php 
   //echo '<pre>'; print_r($users->name);  
?>
<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Custom Jobs</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>
<div id="joining_stage_sub_stage" data-toggle="modal" data-target="#stage_sub_stage"></div>
<div id="interview_stage_sub_stage" data-toggle="modal" data-target="#stage_sub_stage"></div>
<div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Stage and sub stage</h2>
        <!--end::Title-->
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"  id="stage_sub_stage_close">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
          </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <div class="stage_body_content" id="stage_body_content"></div>
    </div>
    <!--end::Modal content-->
  </div>
</div>
<div id="joining_client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage"></div>
<div id="interview_client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage"></div>
<div class="modal fade" id="client_stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Stage and sub stage</h2>
        <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"  id="client_stage_sub_stage_close"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
          </svg>
          </span> </div>
      </div>
      <div class="client_stage_body_content" id="client_stage_body_content"></div>
    </div>
  </div>
</div>
<div id="kt_activitiesRecruiter_toggle"></div>
<div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle" data-kt-drawer-close="#kt_activitiesRecruiter_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <div class=" d-flex align-items-center">
          <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" class="l">
            <div class="symbol-label border-radius-5" id="light_recruiter_caption">&nbsp;</div>
            </a> </div>
          <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_recruiter_name">&nbsp;</a> </div>
        </div>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesRecruiter_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesRecruiter_body"> &nbsp; </div>
  </div>
</div>
<div id="kt_activitiesClientjob_toggle"></div>
<div id="kt_activitiesClientjob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClientjob_toggle" data-kt-drawer-close="#kt_activitiesClientjob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Client Jobs</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesClientjob_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesclientjob_body"> </div>
  </div>
</div>

<!--begin::Modal - Interview Feedback (My Interview Tracker)-->
<div href="javascript:void(0);" id="intervied_feedback_toggle" class="d-none" data-bs-toggle="modal" data-bs-target="#interview_feedback"></div>
<div class="modal fade" id="interview_feedback" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content custmbg">
      <div class="modal-header flex-stack">
        <h2 style="font-size:14px;">Interview Feedback</h2>
        <div class="btn btn-sm btn-icon btn-active-light-danger" data-bs-dismiss="modal">
          <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
            </svg>
          </span>
        </div>
      </div>
      <div id="load_interview_feedback">&nbsp;</div>
    </div>
  </div>
</div>
<!--end::Modal - Interview Feedback-->

<!--begin::Interview Link Popup (custom overlay)-->
<div id="ilOverlay">
  <div class="il-box">
    <div class="il-head">
      <span id="ilTitle">Interview Link</span>
      <button type="button" class="il-close-btn" onclick="closeInterviewLink()">&times;</button>
    </div>
    <div class="il-body">
      <div id="ilCandidate" class="il-cand"></div>
      <a id="ilLink" href="#" target="_blank" rel="noopener"></a>
      <div class="il-actions">
        <button type="button" id="ilOpenBtn" class="btn btn-primary btn-sm" style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          <span id="ilOpenLabel">Open Link</span>
        </button>
      </div>
    </div>
  </div>
</div>
<!--end::Interview Link Popup-->
@endsection
@push('footer-script')
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
{{-- <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script> --}}
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>


    <script>
      (function(){
        var __sdbDone=false;
        function sdbArea(ctx,color,labels,data){
          var g=ctx.createLinearGradient(0,0,0,200);
          g.addColorStop(0,color+'40'); g.addColorStop(1,color+'05');
          return new Chart(ctx,{type:'line',data:{labels:labels,datasets:[{data:data,borderColor:color,backgroundColor:g,borderWidth:2,fill:true,tension:0.4,pointRadius:3,pointHoverRadius:5,pointBackgroundColor:color,pointBorderColor:'#fff',pointBorderWidth:1.5}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{font:{size:11,family:'Poppins'}}},y:{beginAtZero:true,ticks:{precision:0,font:{size:11,family:'Poppins'}},grid:{color:'#f0f2f7'}}}}});
        }
        function sdbBar(ctx,color,labels,data){
          return new Chart(ctx,{type:'bar',data:{labels:labels,datasets:[{data:data,backgroundColor:color,borderRadius:6,borderSkipped:false,barThickness:20,maxBarThickness:26}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{font:{size:10,family:'Poppins'}}},y:{beginAtZero:true,ticks:{precision:0,font:{size:11,family:'Poppins'}},grid:{color:'#f0f2f7'}}}}});
        }
        function drawSdb(){
          if(__sdbDone || typeof Chart==='undefined') return;
          var off=document.getElementById('sdb_offer'); if(!off || off.offsetParent===null) return;
          __sdbDone=true;
          sdbArea(off.getContext('2d'),'#f59e0b',{!! json_encode($labelsCommoffer) !!},{!! json_encode($candidateValuesCommoffer) !!});
          var jn=document.getElementById('sdb_join'); if(jn) sdbArea(jn.getContext('2d'),'#16a34a',{!! json_encode($labelsCommjoin) !!},{!! json_encode($candidateValuesCommjoin) !!});
          var iv=document.getElementById('sdb_interview'); if(iv) sdbArea(iv.getContext('2d'),'#7c4dff',{!! json_encode($interviewlabelsMonth) !!},{!! json_encode($interviewValuesMonth) !!});
          var cn=document.getElementById('sdb_candidates'); if(cn) sdbBar(cn.getContext('2d'),'#2196f3',{!! json_encode($labels) !!},{!! json_encode($candidateValues) !!});
        }
        document.addEventListener('click',function(e){var a=e.target.closest('a[href="#candidateDashboard"]');if(a)setTimeout(drawSdb,60);});
        if(window.jQuery){jQuery(function($){$('a[href="#candidateDashboard"]').on('shown.bs.tab',drawSdb);});}
        setTimeout(drawSdb,400);
      })();
    </script>



<script>

  $(document).ready(function () {
       $('#myTable_jmyRank').DataTable({
            pageLength: 10,          // Default number of rows per page
            lengthChange: false,     // 🔒 disables the lengthMenu dropdown
            ordering: false,
            paging: true,
            searching: false,
            autoWidth: false, // 👈 Disable auto column width calculation
        });
    });


$(document).ready(function() {
      const firstSavedCountryCode = "{{ @$users->calling_code }}"; // Example country code (lowercase)
      const firstPhoneDisplayDiv = document.getElementById("firstPhoneDisplay");

      const flagDiv = document.createElement("div");
      flagDiv.className = `iti__flag iti__${firstSavedCountryCode}`;

       // Get the dial code for the selected country code
      const dialCode = countryDialCodes[firstSavedCountryCode] || ""; 

      // Create a span to display the dial code
      const dialCodeSpan = document.createElement("span");
      dialCodeSpan.textContent = dialCode;

       // Append the flag and the dial code to the display div
       firstPhoneDisplayDiv.appendChild(flagDiv);
       firstPhoneDisplayDiv.appendChild(dialCodeSpan);

    });
    
   $('.dropify').dropify({
       messages: {
           default: '@lang("app.dragDrop")',
           replace: '@lang("app.dragDropReplace")',
           remove: '@lang("app.remove")',
           error: '@lang('app.largeFile')'
       }
   });
   
   $('body').on('click', '#change-mobile', function () {
       $.easyAjax({
           url: "{{ route('changeMobile') }}",
           container: '#verify-mobile',
           type: "GET",
           success: function (response) {
               $('#verify-mobile').html(response.view);
               $('.selectpicker').selectpicker({
                   style: 'btn-info',
                   size: 4
               });
           }
       })
   });
   
   $('#save-form').click(function () {
       $.easyAjax({
           url: '{{route('service_company.profile.update', $user->id)}}',
           container: '#editSettings',
           type: "POST",
           redirect: true,
           file: true
       })
   });
</script>
<script>
   var x = '';
   
   function clearLocalStorage() {
       localStorage.removeItem('otp_expiry');
       localStorage.removeItem('otp_attempts');
   }
   
   function checkSessionAndRemove() {
       $.easyAjax({
           url: '{{ route('removeSession') }}',
           type: 'GET',
           data: {'sessions': ['verify:request_id']}
       })
   }
   
   function startCounter(deadline) {
       x = setInterval(function() {
           var now = new Date().getTime();
           var t = deadline - now;
   
           var days = Math.floor(t / (1000 * 60 * 60 * 24));
           var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
           var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
           var seconds = Math.floor((t % (1000 * 60)) / 1000);
   
           $('#demo').html('Time Left: '+minutes + ":" + seconds);
           $('.attempts_left').html(`${localStorage.getItem('otp_attempts')} attempts left`);
   
           if (t <= 0) {
               clearInterval(x);
               clearLocalStorage();
               checkSessionAndRemove();
               location.href = '{{ route('service_company.profile.index') }}'
           }
       }, 1000);
   }
   
   if (localStorage.getItem('otp_expiry') !== null) {
       let localExpiryTime = localStorage.getItem('otp_expiry');
       let now = new Date().getTime();
   
       if (localExpiryTime - now < 0) {
           clearLocalStorage();
           checkSessionAndRemove();
       }
       else {
           $('#otp').focus().select();
           startCounter(localStorage.getItem('otp_expiry'));
       }
   }
   
   function sendOTPRequest() {
       $.easyAjax({
           url: '{{ route('sendOtpCode.account') }}',
           type: 'POST',
           container: '#request-otp-form',
           messagePosition: 'inline',
           data: $('#request-otp-form').serialize(),
           success: function (response) {
               if (response.status == 'success') {
                   localStorage.setItem('otp_attempts', 3);
   
                   $('#verify-mobile').html(response.view);
                   $('.attempts_left').html(`3 attempts left`);
   
                   let html = `<div class="alert alert-info mb-0" role="alert">
                       @lang('messages.info.verifyAlert')
                       <a href="{{ route('service_company.profile.index') }}" class="btn btn-warning">
                           @lang('menu.profile')
                       </a>
                   </div>`;
   
                   $('#verify-mobile-info').html(html);
                   $('#otp').focus();
   
                   var now = new Date().getTime();
                   var deadline = new Date(now + parseInt('{{ config('nexmo.settings.pin_expiry') }}')*1000).getTime();
   
                   localStorage.setItem('otp_expiry', deadline);
                   // intialize countdown
                   startCounter(deadline);
               }
               if (response.status == 'fail') {
                   $('#mobile').focus();
               }
           }
       });
   }
   
   function sendVerifyRequest() {
       $.easyAjax({
           url: '{{ route('verifyOtpCode.account') }}',
           type: 'POST',
           container: '#verify-otp-form',
           messagePosition: 'inline',
           data: $('#verify-otp-form').serialize(),
           success: function (response) {
               if (response.status == 'success') {
                   clearLocalStorage();
   
                   $('#verify-mobile').html(response.view);
                   $('#verify-mobile-info').html('');
   
                   // select2 reinitialize
                   $('.selectpicker').selectpicker({
                       style: 'btn-info',
                       size: 4
                   });
               }
               if (response.status == 'fail') {
                   // show number of attempts left
                   let currentAttempts = localStorage.getItem('otp_attempts');
   
                   if (currentAttempts == 1) {
                       clearLocalStorage();
                   }
                   else {
                       currentAttempts -= 1;
   
                       $('.attempts_left').html(`${currentAttempts} attempts left`);
                       $('#otp').focus().select();
                       localStorage.setItem('otp_attempts', currentAttempts);
                   }
   
                   if (Object.keys(response.data).length > 0) {
                       $('#verify-mobile').html(response.data.view);
   
                       // select2 reinitialize
                       $('.selectpicker').selectpicker({
                           style: 'btn-info',
                           size: 4
                       });
   
                       clearInterval(x);
                   }
               }
           }
       });
   }
   
   $('body').on('submit', '#request-otp-form', function (e) {
       e.preventDefault();
       sendOTPRequest();
   })
   
   $('body').on('click', '#request-otp', function () {
       sendOTPRequest();
   })
   
   $('body').on('submit', '#verify-otp-form', function (e) {
       e.preventDefault();
       sendVerifyRequest();
   })
   
   $('body').on('click', '#verify-otp', function() {
       sendVerifyRequest();
   })
</script>
<script>
   
   var jointable;
  //  jointableLoad('load');
   function jointableLoad()
   {
      return false;
      var userId		=	$('#userId').val();
      jointable = $('#myTable_joining').dataTable(
      {
        responsive  	: true,
        processing  	: true,
        serverSide  	: true,
        destroy   		: true,
        ordering		: false, //disable ordering
        
            "bLengthChange" : false, //thought this line could hide the LengthMenu
          sDom			: 'lrtip',
          oLanguage 		: {
        sLengthMenu: "_MENU_",
        },
          // ajax			:	'{!! route('service_company.team.joiningdata') !!}?userId='+88,   			
          ajax: '{!! route('service_company.team.joiningdata') !!}?userId='+userId,
          // ajax			: {'url' : '{!! route('service_company.cvtracker.alldata') !!}', "data": function ( d ){d.search = $('#inputSearch').val(), d.profile = true, d.status_cus = 6 }}, 
          language:
          {
              "url": "<?php echo __("app.datatable") ?>"
          },
          "fnDrawCallback": function (oSettings)
          {
              $("body").tooltip(
              {
                  selector: '[data-toggle="tooltip"]'
              });
          },        
          columns:
          [
            {data: 'chkbox', name:'chkbox', width: '5%', orderable: false, searchable: false},
            {data: 'Name', name:'Name', width: '20%', orderable: false, searchable: false},
            {data: 'Contact', name:'Contact', width: '15%', orderable: false, searchable: false},
            {data: 'Requisitions', name:'Requisitions', width: '15%', orderable: false, searchable: false},
            {data: 'Source', name:'Source', width: '10%', orderable: false, searchable: false},
            {data: 'Offer', name:'Offer', width: '10%', orderable: false, searchable: false},
            {data: 'Joining', name:'Joining', width: '10%', orderable: false, searchable: false},
            {data: 'Stage', name:'Stage', width: '10%', orderable: false, searchable: false}   
   	      ]
      });
          new $.fn.dataTable.FixedHeader(jointable);
   }	
   

   
   jointable.on('click', '.joining_stage_sub_stage', function(){  
   
   			alert("calling"); 
   });
   
  
</script>
<script>
   $(document).ready(function() {
   
   	//$("#success-alert").hide();
	
   	$('body').on('click', '.reset_password', function(){
   
   							
   						$('#mteampassword').val('');
   						$('#mteamcpassword').val('');
   								
   						var  id					=	$('#userId').val();
   						$('#mteam_id').val(id);
   						var url   				= "{{ route('service_company.team.info',':id') }}";
   						url     				= url.replace(':id', id);
   		
   						var token 				= "{{ csrf_token() }}";							
   						var baseurl    			= "{{ asset('assets/images/Loading2.gif') }}"; 
   						$.ajax({
   									type		: 'GET',
   									url			: url,
   									beforeSend: function() {
   											  
   														},
   									//data		: {'_token': token, 'userId':userId},
   									success		: function (response) {
   																		if (response.status == "success") {
   																												$('#mteamname').val(response.team.name);
   																												$('#mteamemail').val(response.team.email);	
   																																																										
   																											
   																										  }
   																 }
   					});
   										
   				
   	});
   	
   	$('body').on('click', '#m_resetpassword_submit', function(){
   														
   							   
   								var mteam_id				=	$('#mteam_id').val();
   								var mteampassword			=	$('#mteampassword').val();
   								var mteamcpassword			=	$('#mteamcpassword').val();
                  var currentpassword			=	$('#currentpassword').val();
   								
   								if(mteampassword != mteamcpassword)
   								{
   										$('#mteampassword').val('');	
   										$('#mteamcpassword').val('');
   										$("#warning-alert").fadeTo(2000, 500).slideUp(500, function() {
   											$("#warning-alert").slideUp(500);
   										});	
   										return false;
   								}
   								var url 					= "{{ route('service_company.team.resetpassword') }}";
   								var token 					= "{{ csrf_token() }}";
   								
   								var baseurl    				= "{{ asset('assets/images/Loading2.gif') }}"; 
   												
   								$.ajax({
   											type		: 'POST',
   											url			: url,
   											beforeSend: function() {
   														 // $('#add_interview_sub_stage_form').hide();
   														 // $('#add_interview_sub_stage_form_loader').show();
   														 // $('#add_interview_sub_stage_form_loader').html('<img src="'+baseurl+'" height="60%" width="100%">');
   												},
   											data		: {'_token': token, 'mteam_id':mteam_id, 'mteampassword':mteampassword, 'mteamcpassword':mteamcpassword,'currentpassword':currentpassword },
   											success		: function (response) {
   																					if (response.status == "success") {
   																															$('#mteampassword').val('');	
   																															$('#mteamcpassword').val('');
   																															$("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
   																																$("#success-alert").slideUp(500);
   																															});
   																															 
   																															 
   																														
   																													  }

                                                               if (response.status == "error") {
   																															$('#mteampassword').val('');	
   																															$('#mteamcpassword').val('');
   																															$("#not-match-alert").fadeTo(2000, 500).slideUp(500, function() {
   																																$("#not-match-alert").slideUp(500);
   																															});
   																															 
   																															 
   																														
   																													  }

   																			 }
   								});
   												
   							  
   					
   						
   										
   				
   	});
   	
   });	   	 
</script>
<script>
function tableLoad(data)
{
	console.log('call');
}
// INTERVIEW SCHEDULE
 /*  load_interviewSchedule();
   var table;
   function load_interviewSchedule(){
		
         
   	var userId		=	$('#userId').val();
   
           table   = $('#load_interviewSchedule').dataTable({
           
   		responsive  : true,
   		processing  : true,
   		serverSide  : true,
   		destroy   : true,
   		ordering: false, //disable ordering
   		"bLengthChange" : false, //thought this line could hide the LengthMenu
   		sDom: 'lrtip',
   		oLanguage : {sLengthMenu: "_MENU_",},
               		// ajax    : '{!! route('service_company.team.intdata') !!}?userId=' + userId, 
                   ajax			: {'url' : '{!! route('service_company.interview-schedule.data') !!}', "data": function ( d ) {	d.search = $('#inputSearch').val(),d.profile = true } },  
              
               		"fnDrawCallback": function( settings, start, end, max, total, pre ) {
					var total_record	=	this.fnSettings().fnRecordsTotal();
																							$('#total_record').html(total_record);					
																							$("body").tooltip({
																													selector: '[data-toggle="tooltip"]'
																							});
					},
       
     
           "columnDefs": [
										{"targets": 0 },
										{"targets": 1, "class":"align-items-center" }, 
										{"targets": 2 },
										{ "targets": 3 },
										{ "targets": 4 },
										{ "targets": 5 },            
										{ "targets": 6 },
										{ "targets": 7 },
										{ "targets": 8 },
										{ "targets": 9 }					
						  ],     
       
			 columns: [
						 { data: 'chkbox', name:'chkbox',width: '1%', searchable : false, orderable: false},  
						 { data: 'Name', name: 'Name',width: '15%', searchable : false, orderable: false},
						 { data: 'Requisitions', name: 'Requisitions',width: '10%', searchable : false, orderable: false},
						 { data: 'InterviewMode', name: 'InterviewMode',width: '10%', searchable : false, orderable: false},
						 { data: 'Interviewers', name: 'Interviewers',width: '10%', searchable : false, orderable: false},
						 { data: 'Source', name: 'Source',width: '10%', searchable : false, orderable: false},
						 { data: 'InterviewDate', name: 'InterviewDate',width: '10%', searchable : false, orderable: false},
						 { data: 'Feedback', name: 'Feedback',width: '10%', searchable : false, orderable: false},
						 { data: 'Rating', name: 'Rating',width: '10%', searchable : false, orderable: false},		  
						 { data: 'Stage', name: 'Stage',width: '10%', searchable : false, orderable: false}
					 ],
              
           });
           new $.fn.dataTable.FixedHeader( table );
   
       
	}*/
  var table;
  function load_interviewSchedule(userId){

    return false;
          
          var userId		=	$('#userId').val();
    
        table   		=	$('#load_interviewSchedule').dataTable({
                                        responsive  	:	true,
                                        processing  	:	true,
                                        serverSide  	:	true,
                                        destroy   		:	true,
                                        ordering		:	false, //disable ordering
                                        "bLengthChange" :	false, //thought this line could hide the LengthMenu
                                        sDom			:	'lrtip',
                                        oLanguage 		:	{sLengthMenu: "_MENU_",},
                                        ajax    		:	'{!! route('service_company.team.intdata') !!}?userId=' + userId, 
           
                                        "fnDrawCallback": 	function( settings, start, end, max, total, pre ) {
                                                    var total_record	=	this.fnSettings().fnRecordsTotal();
                                                    $('#total_record').html(total_record);					
                                                    $("body").tooltip({
                                                    selector: '[data-toggle="tooltip"]'
                                                    });
                                                  },
      
      
                                        "columnDefs"	: 	[
                                                    {"width": "50px", "targets": 0 },
                                                    {"width": "150px", "targets": 1, "class":"align-items-center" }, 
                                                    {"width": "50px", "targets": 2 },
                                                    { "width": "50px", "targets": 3 },
                                                    { "width": "50px", "targets": 4 },
                                                    { "width": "50px", "targets": 5 },            
                                                    { "width": "50px", "targets": 6 },
                                                    { "width": "50px", "targets": 7 },
                                                    { "width": "50px", "targets": 8 },
                                                    { "width": "50px", "targets": 9 }					
                                                    ],     
      
                                          columns		: 	  [
                                                      { data: 'chkbox', name:'chkbox',width: '1%', searchable : false, orderable: false},  
                                                      { data: 'Name', name: 'Name',width: '15%', searchable : false, orderable: false},
                                                      { data: 'Requisitions', name: 'Requisitions',width: '10%', searchable : false, orderable: false},
                                                      { data: 'InterviewMode', name: 'InterviewMode',width: '10%', searchable : false, orderable: false},
                                                      { data: 'Interviewers', name: 'Interviewers',width: '10%', searchable : false, orderable: false},
                                                      { data: 'Source', name: 'Source',width: '10%', searchable : false, orderable: false},
                                                      { data: 'InterviewDate', name: 'InterviewDate',width: '10%', searchable : false, orderable: false},
                                                      { data: 'Feedback', name: 'Feedback',width: '10%', searchable : false, orderable: false},
                                                      { data: 'Rating', name: 'Rating',width: '10%', searchable : false, orderable: false},		  
                                                      { data: 'Stage', name: 'Stage',width: '10%', searchable : false, orderable: false}
                                                     ],
           
        });
        new $.fn.dataTable.FixedHeader( table );
    
    }
    

	   
$(document).on('click', '.custom-show-application-detail', function ()
         {
         	$('#kt_activitiesApplication_toggle').trigger('click');
         	var id    		= $(this).data('row-id');
         	var url 		= "{{ route('service_company.job-applications.show',':id') }}";
         	url     		= url.replace(':id', id);		   
         	var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
         	$.ajax({
								type  		: 'GET',
								url   		: url,
								beforeSend	: function() {
											  					$('#application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
														},
								success 	: function (response) {
																$('#application_show_right_bar').html(response.view);	
														}
           			});
         }); 
		 


		   
$(document).on('click', '.interview_stage_sub_stage', function(){
return false;
		$('#interview_stage_sub_stage').trigger('click');  
		$('#stage_body_content').html('');
       	$('#client_stage_body_content').html('');
			
		 		var id       = $(this).data('row-id');			   
               var url    	= "{{ route('service_company.job-applications.stage_body_content',':id') }}";
               url      	= url.replace(':id', id);
               $.ajax({
                        	type  : 'GET',
                        	url   : url,
                        	success : function (response) {
                                            if (response.status == "success") 
                                  			{
                                                  $('#stage_body_content').html(response.view);
                                            }
                                        }
               });
}); 


$(document).on('click', '.interview_client_stage_sub_stage', function(){

  return false;
				$('#interview_client_stage_sub_stage').trigger('click');  
		 		$('#stage_body_content').html('');
       		    $('#client_stage_body_content').html('');	
                var id       = $(this).data('row-id');			   
                var url    	= "{{ route('service_company.job-applications.client_stage_body_content',':id') }}";
                url      	= url.replace(':id', id);
                $.ajax({
                        	type  : 'GET',
                        	url   : url,
                        	success : function (response) {
                                            if (response.status == "success") 
                                  			{
                                                  $('#client_stage_body_content').html(response.view);
                                            }
                                        }
               });
			   
}); 
		 
</script>
<script>

	//	APPLICATION DATA
  	var userId			=	$('#userId').val();
  /*	var table		 	=	$('#load_application').dataTable({
     								
   													responsive     		: true, 
   													processing      	: true,
   													ordering			: false, //disable ordering
   													serverSide: true,
   													destroy       		: true,   
   													stateSave     		: true,
   													
   													"bLengthChange"   	: false,   
   													sDom				: 'lrtip',       
   
         ajax: '{!! route('service_company.team.appdata') !!}?userId='+userId,  
        // ajax			:	{'url' : '{!! route('service_company.cvtracker.alldata') !!}', "data" : function ( d ) { d.search = $('#inputSearch').val(),d.profile = true} },           
                  language: {
                      "url": "<?php echo __("app.datatable") ?>"
                  },
          
                  "fnDrawCallback": function (oSettings) {
                      $("body").tooltip({
                          selector: '[data-toggle="tooltip"]'
                      });
                  },         
         
         "initComplete":function( settings, json){ 
         
           $('.clientStatusLoader').html('');
           $('.jobLoader').html('');
           $('.sourceLoader').html('');
           
           $('.search_np_idLoader').html('');
           $('.search_passportLoader').html('');
           $('.statusLoader').html('');            
           
         }, 				  
           columns: [
              
   		{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
   		{data: 'Name', name: 'Name',  orderable: false, searchable: false},						
   		{data: 'Contacts', name: 'Contacts', searchable : false, orderable : false},						
   		{data: 'Exp', name: 'Exp', searchable : false, orderable : false},
   		{data: 'Salary', name: 'Salary',  searchable : false, orderable : false},   
   		{data: 'NoticePeriod', name: 'NoticePeriod', searchable : false, orderable : false},
   		{data: 'Qualifaction', name: 'Qualifaction',  searchable : false, orderable : false},   					
   		{data: 'Location', name: 'Location', orderable : false},  												 
   		{data: 'Source', name: 'Source', orderable: false},
   		{data: 'UploadDate', name: 'UploadDate', orderable: false},   		
   		{data: 'Requisitions', name: 'Requisitions', orderable: false},
   		{data: 'Stage', name: 'Stage', orderable: false}
   	]         
     
            }); 				
  	new $.fn.dataTable.FixedHeader( table );	
	
	
	
table.on('click', '.stage_sub_stage', function () 
      {
       return false;
	   		$('#stage_body_content').html('');
       		$('#client_stage_body_content').html('');	
			   
	   		 var id       = $(this).data('row-id');			   
               var url    	= "{{ route('service_company.job-applications.stage_body_content',':id') }}";
               url      	= url.replace(':id', id);
               $.ajax({
                        	type  : 'GET',
                        	url   : url,
                        	success : function (response) {
                                            if (response.status == "success") 
                                  			{
                                                  $('#stage_body_content').html(response.view);
                                            }
                                        }
               });
   
        }); 
		   

table.on('click', '.client_stage_sub_stage', function ()    
	{
    return false;
			   $('#stage_body_content').html('');
       		   $('#client_stage_body_content').html('');	
               var id       = $(this).data('row-id');			   
               var url    	= "{{ route('service_company.job-applications.client_stage_body_content',':id') }}";
               url      	= url.replace(':id', id);
               $.ajax({
                        	type  : 'GET',
                        	url   : url,
                        	success : function (response) {
                                            if (response.status == "success") 
                                  			{
                                                  $('#client_stage_body_content').html(response.view);
                                            }
                                        }
               });
               
        }); */


    function load_application(userId) 
		{
      return false;
		   // var userId		=	$('#userId').val();
     	          application_table = $('#load_application').dataTable({
      								
																  responsive     		:	true, 
																  processing      		:	true,
																  ordering				:	false, //disable ordering
																  destroy       		:	true,   
																  stateSave     		:	true,
																  "bLengthChange"   	:	false,   
																  sDom					:	'lrtip',       
          														  ajax					:	'{!! route('service_company.team.appdata') !!}?userId='+userId,             
																  "initComplete"		:	function( settings, json){ 
																														$('.clientStatusLoader').html('');
																														$('.jobLoader').html('');
																														$('.sourceLoader').html('');
																														$('.search_np_idLoader').html('');
																														$('.search_passportLoader').html('');
																														$('.statusLoader').html('');            
																							 }, 	
																// "columnDefs"			:
																// 								[ 
																// 									{ "width": "50px", "targets": 0, "width": "4%", "class":""},
																// 									{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
																// 									{  "width": "50px"  , "targets": 2},		
																// 									{  "width": "50px", "targets": 3 },		
																// 									{  "width": "200px", "targets": 4 }, 
																// 									{  "width": "50px" ,"targets": 5 },
																// 									{  "width": "200px" , "targets": 6 },              
																// 									{  "width": "150px" , "targets": 7 },	
																// 									{  "width": "150px", "targets": 8 },	
															
																// 									{ "width": "20px", "targets": 9 },	
																// 									{ "width": "20px", "targets": 10 }, 
																// 									{ "width": "20px", "targets": 11 }   
																									
																// 								], 
																									  			  
																columns					: [
																								{ data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
																								{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},						
																								//{data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '15%'},						
																								{data: 'Exp', name: 'Exp', searchable : false, orderable : false,  width: '5%'},
																								{data: 'Salary', name: 'Salary',  searchable : false, orderable : false,  width: '5%'},   
																								{data: 'NoticePeriod', name: 'NoticePeriod', searchable : false, orderable : false},
																								{data: 'Qualifaction', name: 'Qualifaction',  searchable : false, orderable : false},		
																								{data: 'Location', name: 'Location', orderable : false},  												 
																								{data: 'Source', name: 'Source', orderable: false},
																								{data: 'UploadDate', name: 'UploadDate', orderable: false},
																								{data: 'Requisitions', name: 'Requisitions', orderable: false}
																								// {data: 'Stage', name: 'Stage', orderable: false}
																							]         
														  
								}); 
               new $.fn.dataTable.FixedHeader(application_table);
           
	  }


		   				   
</script>
<script>

	// REQUISITION DATA
	
   var userId		=	$('#userId').val();  
  //  var load_jobs 	=	$('#load_jobs').dataTable({
   													   
	// 													responsive  	: true,
	// 													processing  	: true,
	// 													serverSide  	: true,
	// 													destroy   		: true,
	// 													ordering		: false, //disable ordering   			
	// 													"bLengthChange" : false, //thought this line could hide the LengthMenu
	// 													sDom			: 'lrtip',
	// 													oLanguage 		: {sLengthMenu: "_MENU_",},   
	// 														ajax: {'url' : '{!! route('service_company.team.jobdata') !!}?userId='+userId,
	// 															  "data": function ( d )
	// 															  {
	// 																d.search = $('#inputSearch').val()
	// 															  }
	// 															},   
  //                             // ajax			: {'url' : '{!! route('service_company.jobs.all_data') !!}', "data": function ( d ){d.search = $('#inputSearch').val(),d.profile = true}},
	// 													language:{"url": "<?php echo __("app.datatable") ?>"},	  
	// 													"drawCallback": function( settings ) {   				
	// 																$('[data-toggle="tooltip1"]').tooltip();
	// 																$('[data-toggle="tooltip2"]').tooltip();
	// 																$('[data-toggle="tooltip3"]').tooltip();
	// 																$('[data-toggle="tooltip4"]').tooltip();		
	// 														},  
	// 													"columnDefs":
	// 																  [ 
	// 																			{ "width": "50px", "targets": 0, "width": "3%", "class":""},
	// 																			{  "width": "100px", "targets": 1, "class":"d-flex align-items-center w-100 border-bottom-dashed" },																		
	// 																			{  "width": "50px"  , "targets": 2},		
	// 																			{  "width": "50px", "targets": 3 },		
	// 																			{  "width": "200px", "targets": 4 }, 
	// 																			{  "width": "50px" ,"targets": 5 },
	// 																			{  "width": "250px" , "targets": 6 },              
	// 																			{  "width": "100px" , "targets": 7 },
												   
	// 																			{ "width": "5px", "targets": 8 },									
	// 																			{ "width": "5px", "targets": 9 },	
	// 																			{ "width": "5px", "targets": 10 }, 
	// 																			{ "width": "5px", "targets": 11 },   								
	// 																			{ "width": "5px", "targets": 12 },             
	// 																			{ "width": "5px", "targets": 13 },
	// 																			{ "width": "5px", "targets": 14 },   
	// 																			{ "width": "5px", "targets": 15 }, 
	// 																			{ "width": "5px", "targets": 16 }
	// 																	  ], 
	// 														columns:
	// 														[
	// 														  	{ data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
	// 															{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
	// 															{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
	// 															{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
	// 															{ data: 'Location', name: 'Location' , orderable: false, searchable: false},   
	// 															{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
	// 															{ data: 'Date', name: 'Date' , width: '90px', orderable: false, searchable: false},
	// 															{ data: 'HM', name: 'HM' , width: '50px', orderable: false, searchable: false},   				
	// 															{ data: 'Partners', name: 'Partners' , orderable: false, searchable: false},   				
	// 															{ data: 'TOT', name: 'TOT' , orderable: false, searchable: false},
	// 															{ data: 'SOU', name: 'SOU' , orderable: false, searchable: false},					
	// 															{ data: 'SCR', name: 'SCR' , orderable: false, searchable: false},   				
	// 															{ data: 'EVA', name: 'EVA' , orderable: false, searchable: false},
	// 															{ data: 'NEO', name: 'NEO' , orderable: false, searchable: false},
	// 															{ data: 'OFF', name: 'OFF' , orderable: false, searchable: false},
	// 															{ data: 'JOI', name: 'JOI' , orderable: false, searchable: false},
	// 															{ data: 'REJ', name: 'REJ' , orderable: false, searchable: false}
																			   
	// 														]
   			
	// 											  });  

  //  new $.fn.dataTable.FixedHeader( load_jobs );   
  //  load_jobs.on('click', '.job-show-detail', function ()
  //  	{
  //  		$('#kt_activitiesJob_toggle').trigger('click');		
  //  		var id    		=	$(this).data('row-id');
  //  		var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}";
  //  		url     		=	url.replace(':id', id);
  //  		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
  //  		$.ajax({
  //  						type  		: 'GET',
  //  						url   		: url,
  //  						 beforeSend	: function() {
  //                                    $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
  //                      	},
  //  						success 	: function (response) 
  //  						{
  //  								setTimeout(
  //  								()=>
  //  								{
  //  										if (response.status == "success")
  //  										{
  //  											$('#kt_activitiesJob_body').html(response.view);
  //  										}
  //  								}, 50);
     		  					
  //  						}
  //  	  			});
  //  	}); 

  function load_jobs(userId){
    return false;
		var load_jobs 	=	$('#load_jobs').dataTable({   
														responsive  	:	true,
														processing  	:	true,
														serverSide  	:	true,
														destroy   		:	true,
														ordering		:	false, //disable ordering   			
														"bLengthChange" :	false, //thought this line could hide the LengthMenu
														sDom			:	'lrtip',
														oLanguage 		:	{sLengthMenu: "_MENU_",},   
														ajax			:	{'url' : '{!! route('service_company.team.jobdata') !!}?userId='+userId, "data": function ( d ) { d.search = $('#inputSearch').val(),d.profile = true } }, 
														"drawCallback"	: 	function( settings ) {   				
																					$('[data-toggle="tooltip1"]').tooltip();
																					$('[data-toggle="tooltip2"]').tooltip();
																					$('[data-toggle="tooltip3"]').tooltip();
																					$('[data-toggle="tooltip4"]').tooltip();		
																			 },
														"columnDefs"	:
																	  		[ 
																				{ "width": "50px", "targets": 0, "width": "4%", "class":""},
																				{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
																				{  "width": "50px"  , "targets": 2},		
																				{  "width": "50px", "targets": 3 },		
																				{  "width": "200px", "targets": 4 }, 
																				{  "width": "50px" ,"targets": 5 },
																				{  "width": "200px" , "targets": 6 },              
																				{  "width": "150px" , "targets": 7 },	
																				{  "width": "150px", "targets": 8 },	
   										
																				{ "width": "20px", "targets": 9 },	
																				{ "width": "20px", "targets": 10 }, 
																				{ "width": "20px", "targets": 11 },    
																				{ "width": "20px", "targets": 12 },             
																				{ "width": "20px", "targets": 13 },
																				{ "width": "20px", "targets": 14 },   
																				{ "width": "20px", "targets": 15 }, 
																				{ "width": "20px", "targets": 16 }
																		  	], 
														columns			:
																			[
																				{ data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
																				{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
																				{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
																				{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
																				{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
																
																				{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
																				{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
																				{ data: 'HM', name: 'HM' , orderable: false, searchable: false},
																				{ data: 'Partners', name: 'Partners' , orderable: false, searchable: false},
																				{ data: 'TOT', name: 'TOT' , orderable: false, searchable: false},
																				{ data: 'SOU', name: 'SOU' , orderable: false, searchable: false},					
																				{ data: 'SCR', name: 'SCR' , orderable: false, searchable: false},
																				
																				{ data: 'EVA', name: 'EVA' , orderable: false, searchable: false},
																				{ data: 'NEO', name: 'NEO' , orderable: false, searchable: false},
																				{ data: 'OFF', name: 'OFF' , orderable: false, searchable: false},
																				{ data: 'JOI', name: 'JOI' , orderable: false, searchable: false},
																				{ data: 'REJ', name: 'REJ' , orderable: false, searchable: false}	   
																			]
   				
   				});    
   	new $.fn.dataTable.FixedHeader( load_jobs );
	}

	
</script>
<script>

$(document).ready(function(){
		load_jobs.on('click', '.hmlisting', function(){
			
				
					
														$('#kt_activitiesRecruiter_toggle').trigger('click');  
				
														var id    		= $(this).data('row-id');
														var token 		=	"{{ csrf_token() }}";
										
											
														var url   		=	"{{ route('service_company.jobs.hmlisting',':id') }}"; 
														url     		=	url.replace(':id', id);  		
														var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
														$.ajax({
																	type  		: 'GET',
																	url   		: url,
																	data		: {'_token': token, 'id' : id},
																	 beforeSend	: function() {
																				 $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
																	},
																	success 	: function (response) 
																	{
																				$('#kt_activitiesRecruiter_body').html(response.view);
																	}
															}); 
												
		
		});
});
</script>
<script>

$(document).on('click', '.client-show-detail', function ()
{
		$('#kt_activitiesClientjob_toggle').trigger('click');	
   		var id    		=	$(this).data('row-id');
   		var url   		=	"{{ route('service_company.jobs.clientviewmore',':id') }}";
   		url     		=	url.replace(':id', id);
   		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
						 			 $('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) 
   						{
   								setTimeout(
   								()=>
   								{
   										if (response.status == "success")
   										{
											$('#kt_activitiesclientjob_body').html(response.view);
   										}
   								}, 50);
     		  					
   						}
   	  			});
}); 

$(document).ready(function() {

  window.__profileTelemetry = {
    pageLoadStart: performance.now(),
    tabTimings: {},
    recordTabLoad: function(tabName, durationMs) {
      this.tabTimings[tabName] = durationMs;
      if (window.console && console.log) {
        console.log('[Telemetry] Tab `' + tabName + '` loaded in ' + durationMs + 'ms');
      }
    },
    getReport: function() {
      var totalTime = Math.round(performance.now() - this.pageLoadStart);
      return {
        totalPageSessionTimeMs: totalTime + 'ms',
        loadedTabTimings: this.tabTimings
      };
    },
    logReport: function() {
      if (window.console && console.table) {
        console.group('[Profile V2 Telemetry Summary]');
        console.log('Session Duration:', Math.round(performance.now() - this.pageLoadStart) + 'ms');
        console.table(this.tabTimings);
        console.groupEnd();
      }
    }
  };

  // Phase 11 — Stale-Response & Request Concurrency Protection
  window.ProfileTabManager = {
    initializedTabs: {
      overview: true
    },
    activeRequests: {},
    requestSequence: {},

    init: function() {
      var self = this;
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var targetTab = $(e.target).attr('href');
        if (!targetTab) return;
        var tabKey = targetTab.replace('#', '');
        self.onTabActivated(tabKey);
      });
    },

    onTabActivated: function(tabKey) {
      if (this.initializedTabs[tabKey]) {
        return;
      }
      this.initializedTabs[tabKey] = true;

      if (tabKey === 'requisitions') {
        fetchTeamJobs();
      } else if (tabKey === 'candidate') {
        fetchTeamCandidate();
      } else if (tabKey === 'joinings') {
        fetchTeamCandidateJoining();
      } else if (tabKey === 'interview_tracker') {
        fetchTeamCandidateInterviewTracker();
      } else if (tabKey === 'myRank') {
        fetchMyRankV2();
      } else if (tabKey === 'candidateDashboard') {
        fetchDashboardV2();
      }
    },

    nextSequence: function(tabKey) {
      this.requestSequence[tabKey] = (this.requestSequence[tabKey] || 0) + 1;
      return this.requestSequence[tabKey];
    },

    isLatestSequence: function(tabKey, seq) {
      return this.requestSequence[tabKey] === seq;
    },

    registerRequest: function(tabKey, jqXHR) {
      this.cancelRequest(tabKey);
      this.activeRequests[tabKey] = jqXHR;
    },

    cancelRequest: function(tabKey) {
      if (this.activeRequests[tabKey] && typeof this.activeRequests[tabKey].abort === 'function') {
        try { this.activeRequests[tabKey].abort(); } catch(e) {}
        delete this.activeRequests[tabKey];
      }
    },

    clearRequest: function(tabKey) {
      delete this.activeRequests[tabKey];
    },

    invalidateTab: function(tabKey) {
      delete this.initializedTabs[tabKey];
    }
  };

  ProfileTabManager.init();

    $(document).off('click.profilePaginate', '#paginationTeamtjobs .page-link').on('click.profilePaginate', '#paginationTeamtjobs .page-link', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;

            $(this).addClass('disabled');

            fetchTeamJobs(page, $('#search').val()).then(() => {
                $('#paginationTeamtjobs .page-link').removeClass('disabled');
            }).catch(() => {
                $('#paginationTeamtjobs .page-link').removeClass('disabled');
            });
    });

    $(document).off('click.profilePaginate', '#paginationTeamtCandidates .page-link').on('click.profilePaginate', '#paginationTeamtCandidates .page-link', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;

            $(this).addClass('disabled');

            fetchTeamCandidate(page, $('#search').val()).then(() => {
                $('#paginationTeamtCandidates .page-link').removeClass('disabled');
            }).catch(() => {
                $('#paginationTeamtCandidates .page-link').removeClass('disabled');
            });
    });

    $(document).off('click.profilePaginate', '#paginationteamjoiningjobs .page-link').on('click.profilePaginate', '#paginationteamjoiningjobs .page-link', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;

            $(this).addClass('disabled');

            fetchTeamCandidateJoining(page, $('#search').val()).then(() => {
                $('#paginationteamjoiningjobs .page-link').removeClass('disabled');
            }).catch(() => {
                $('#paginationteamjoiningjobs .page-link').removeClass('disabled');
            });
    });

    $(document).off('click.profilePaginate', '#paginationteaminterviewjobs .page-link').on('click.profilePaginate', '#paginationteaminterviewjobs .page-link', function(e) {
            e.preventDefault();

            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;

            $(this).addClass('disabled');

            fetchTeamCandidateInterviewTracker(page, $('#search').val()).then(() => {
                $('#paginationteaminterviewjobs .page-link').removeClass('disabled');
            }).catch(() => {
                $('#paginationteaminterviewjobs .page-link').removeClass('disabled');
            });
    });


});

// Phase 10 — Search Throttling & Debounce Utility (350ms)
function profileDebounce(func, wait) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            func.apply(context, args);
        }, wait);
    };
}

var elJobSearch = document.getElementById('kt_tagify_search');
if (elJobSearch) {
    elJobSearch.addEventListener('keyup', profileDebounce(function() {
        fetchTeamJobs();
    }, 350));
}


function fetchTeamJobs(page = 1, search = '') {
  var t0 = performance.now();
  var userId  = '{{ @$users->id }}';
  var seq = window.ProfileTabManager ? window.ProfileTabManager.nextSequence('requisitions') : 0;
        $('.loadingMessage').css({ display: 'block' });
        var xhr = $.ajax({
            url: `{!! route('service_company.team.jobdata_ajax') !!}?page=${page}`,
            type: 'GET',
            data: {
                userId: userId,
                search: $('#inputSearch').val(),
                custom_search: $('#kt_tagify_search').val(),
                custom_status: $('#statusjb').val(),
                custom_hiringtype: $('#hiringtypejob').val(),
                profile:true
            },
            success: function(response) {
                if (window.ProfileTabManager && !window.ProfileTabManager.isLatestSequence('requisitions', seq)) {
                    return;
                }
                var dur = Math.round(performance.now() - t0);
                if (window.__profileTelemetry) window.__profileTelemetry.recordTabLoad('my_jobs', dur);
                $('.loadingMessage').css({ display: 'none' });
                $('#teamJobsListajax').html(response.html);
                $('#paginationTeamtjobs').html(response.pagination);
                if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('requisitions');
            },
            error: function(jqXHR, textStatus) {
                if (textStatus === 'abort') return;
                $('.loadingMessage').css({ display: 'none' });
                if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('requisitions');
            }
        });
        if (window.ProfileTabManager) window.ProfileTabManager.registerRequest('requisitions', xhr);
        return xhr;
}
function fetchTeamCandidate(page = 1, search = '') {
  var t0 = performance.now();
  var userId  = '{{ @$users->id }}';
  var seq = window.ProfileTabManager ? window.ProfileTabManager.nextSequence('candidate') : 0;
        $('.loadingMessage').css({ display: 'block' });
        var xhr = $.ajax({
            url: `{!! route('service_company.team.appdata_ajax') !!}?page=${page}`,
            type: 'GET',
            data: {
                userId: userId,
                cus_search : $('#candidate_kt_tagify_2').val(),
                sortingfilter : $('#candidatesortingfilter').val(),
                app_int_status : $('#stagefiltercandidate').val(),
            },
            success: function(response) {
                if (window.ProfileTabManager && !window.ProfileTabManager.isLatestSequence('candidate', seq)) {
                    return;
                }
                var dur = Math.round(performance.now() - t0);
                if (window.__profileTelemetry) window.__profileTelemetry.recordTabLoad('my_candidate', dur);
                $('.loadingMessage').css({ display: 'none' });
                $('#teamCandidatesListajax').html(response.html);
                $('#paginationTeamtCandidates').html(response.pagination);
                if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('candidate');
            },
            error: function(jqXHR, textStatus) {
                if (textStatus === 'abort') return;
                $('.loadingMessage').css({ display: 'none' });
                if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('candidate');
            }
        });
        if (window.ProfileTabManager) window.ProfileTabManager.registerRequest('candidate', xhr);
        return xhr;
}
function fetchTeamCandidateJoining(page = 1, search = '') {
  var t0 = performance.now();
  var userId  = '{{ @$users->id }}';
  var seq = window.ProfileTabManager ? window.ProfileTabManager.nextSequence('joinings') : 0;
          var sortingfilter          			= $('#joinsortingfilter').val();
          var country          			= $('#country').val();
          var cus_search          			= $('#join_kt_tagify_2').val();
        $('.loadingMessage').css({ display: 'block' });
        var xhr = $.ajax({
            url: `{!! route('service_company.team.joiningdata_ajax') !!}?page=${page}`,
            type: 'GET',
            data: {
                userId: userId,
                sortingfilter : sortingfilter,
                country : country,
                cus_search:cus_search
            },
            success: function(response) {
                if (window.ProfileTabManager && !window.ProfileTabManager.isLatestSequence('joinings', seq)) {
                    return;
                }
                var dur = Math.round(performance.now() - t0);
                if (window.__profileTelemetry) window.__profileTelemetry.recordTabLoad('my_joinings', dur);
                $('.loadingMessage').css({ display: 'none' });
                $('#teamjoiningJobsListajax').html(response.html);
                $('#paginationteamjoiningjobs').html(response.pagination);
                if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('joinings');
            },
            error: function(jqXHR, textStatus) {
                if (textStatus === 'abort') return;
                $('.loadingMessage').css({ display: 'none' });
                if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('joinings');
            }
        });
        if (window.ProfileTabManager) window.ProfileTabManager.registerRequest('joinings', xhr);
        return xhr;
}
function fetchTeamCandidateInterviewTracker(page = 1, search = '') {
  var t0 = performance.now();
  var userId  = '{{ @$users->id }}';
  var seq = window.ProfileTabManager ? window.ProfileTabManager.nextSequence('interview_tracker') : 0;
        $('.loadingMessage').css({ display: 'block' });
        var xhr = $.ajax({
            url: `{!! route('service_company.team.intdata_ajax') !!}?page=${page}`,
            type: 'GET',
            data: {
                userId: userId,
                custom_hiringtype : $('#interviewfiltersdataget').val(),
                custom_search : $('#interviewtracker_kt_tagify_2').val()
            },
            success: function(response) {
                if (window.ProfileTabManager && !window.ProfileTabManager.isLatestSequence('interview_tracker', seq)) {
                    return;
                }
                var dur = Math.round(performance.now() - t0);
                if (window.__profileTelemetry) window.__profileTelemetry.recordTabLoad('my_interview_tracker', dur);
                $('.loadingMessage').css({ display: 'none' });
                $('#teaminterviewJobsListajax').html(response.html);
                $('#paginationteaminterviewjobs').html(response.pagination);
                if (typeof response.total !== 'undefined') {
                    $('#total_record_note').text('Showing ' + response.total + ' entries');
                }
                var sa = document.getElementById('itSelectAllCheckbox');
                if (sa) sa.classList.remove('checked');
                if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('interview_tracker');
            },
            error: function(jqXHR, textStatus) {
                if (textStatus === 'abort') return;
                $('.loadingMessage').css({ display: 'none' });
                if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('interview_tracker');
            }
        });
        if (window.ProfileTabManager) window.ProfileTabManager.registerRequest('interview_tracker', xhr);
        return xhr;
}

function fetchMyRankV2() {
  var t0 = performance.now();
  var xhr = $.ajax({
    url: "{!! route('service_company.api.v2.profile.leaderboard') !!}",
    type: 'GET',
    success: function(resp) {
      var dur = Math.round(performance.now() - t0);
      if (window.__profileTelemetry) window.__profileTelemetry.recordTabLoad('my_rank', dur);
      if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('myRank');
    },
    error: function() {
      if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('myRank');
    }
  });
  if (window.ProfileTabManager) window.ProfileTabManager.registerRequest('myRank', xhr);
  return xhr;
}

function fetchDashboardV2() {
  var t0 = performance.now();
  var xhr = $.ajax({
    url: "{!! route('service_company.api.v2.profile.graphs') !!}",
    type: 'GET',
    success: function(resp) {
      var dur = Math.round(performance.now() - t0);
      if (window.__profileTelemetry) window.__profileTelemetry.recordTabLoad('my_dashboard', dur);
      if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('candidateDashboard');
    },
    error: function() {
      if (window.ProfileTabManager) window.ProfileTabManager.clearRequest('candidateDashboard');
    }
  });
  if (window.ProfileTabManager) window.ProfileTabManager.registerRequest('candidateDashboard', xhr);
  return xhr;
}

function filtersdatagetcandidates() {
     fetchTeamCandidate();
}
var elCandidateSearch = document.getElementById('candidate_kt_tagify_2');
if (elCandidateSearch) {
    elCandidateSearch.addEventListener('keyup', profileDebounce(function() {
        fetchTeamCandidate();
    }, 350));
}

function interviewfiltersdataget() {
     fetchTeamCandidateInterviewTracker();
}
var elInterviewSearch = document.getElementById('interviewtracker_kt_tagify_2');
if (elInterviewSearch) {
    elInterviewSearch.addEventListener('keyup', profileDebounce(function() {
        fetchTeamCandidateInterviewTracker();
    }, 350));
}

function filtersdatagetjoin() {
     fetchTeamCandidateJoining();
}
var elJoiningSearch = document.getElementById('join_kt_tagify_2');
if (elJoiningSearch) {
    elJoiningSearch.addEventListener('keyup', profileDebounce(function() {
        fetchTeamCandidateJoining();
    }, 350));
}




</script>

<!-- ===== My Interview Tracker (attachment design) interactions ===== -->
<script>
// Row selection
function itToggleSelectAll() {
    var box = document.getElementById('itSelectAllCheckbox');
    if (!box) return;
    var on = box.classList.toggle('checked');
    document.querySelectorAll('#teaminterviewJobsListajax .it-checkbox[data-row-check]').forEach(function (cb) {
        cb.classList.toggle('checked', on);
    });
}
function toggleRowSelect(el) {
    el.classList.toggle('checked');
}

// Follow Up dropdown
function toggleFollowUp(btn) {
    var menu = btn.nextElementSibling;
    var isOpen = menu.classList.contains('open');
    document.querySelectorAll('.it-followup-menu.open').forEach(function (m) { m.classList.remove('open'); });
    if (!isOpen) menu.classList.add('open');
}
function setFollowUpStatus(el, id, status) {
    document.querySelectorAll('.it-followup-menu.open').forEach(function (m) { m.classList.remove('open'); });
    var dropdown = el.closest('.it-followup-dropdown');
    var trigger = dropdown ? dropdown.querySelector('.it-followup-trigger') : null;
    var labels = { 'confirmed': 'Confirmed', 'no-response': 'No Response', 'rescheduled': 'Rescheduled' };
    var colors = { 'confirmed': 'var(--it-success)', 'no-response': 'var(--it-danger)', 'rescheduled': 'var(--it-primary)' };
    $.ajax({
        url: '{!! route('service_company.interview-schedule.followup_update') !!}',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', id: id, status: status },
        success: function (resp) {
            if (resp && resp.status === 'success') {
                if (trigger) {
                    var lbl = trigger.querySelector('.fu-label');
                    if (lbl) lbl.textContent = labels[status] || 'Follow Up';
                    trigger.style.color = colors[status] || '';
                    trigger.style.borderColor = colors[status] || '';
                    var ctaIcon = trigger.querySelector('.fu-cta-icon');
                    if (ctaIcon) ctaIcon.remove();
                }
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({ text: (resp && resp.msg) ? resp.msg : 'Could not update follow-up status.', icon: 'error', confirmButtonText: 'Ok' });
            }
        },
        error: function () {
            if (typeof Swal !== 'undefined') Swal.fire({ text: 'Could not update follow-up status.', icon: 'error', confirmButtonText: 'Ok' });
        }
    });
}

// Feedback - not allowed
$(document).on('click', '.notAllow-feedback', function () {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ text: "Only users with Administrator or Job Captain roles can add feedback", title: "Access Denied", icon: "warning", buttonsStyling: false, confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-primary" } });
    }
});

// Feedback - view / add
$(document).on('click', '.show-feedback', function () {
    $('#intervied_feedback_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.interview-schedule.showfeedback',':id') }}".replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function () {
            $('#load_interview_feedback').html('<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:50px 20px;gap:12px;"><div style="width:36px;height:36px;border:3px solid #e2e8f0;border-top-color:#2563eb;border-radius:50%;animation:fbFastSpin .5s linear infinite;"></div><div style="color:#64748b;font-size:13px;font-weight:500;">Loading feedback…</div></div><style>@keyframes fbFastSpin{to{transform:rotate(360deg)}}</style>');
        },
        success: function (response) {
            if (response.status == "success") {
                $('#load_interview_feedback').html(response.view);
            }
        }
    });
});
$(document).on('hidden.bs.modal', '#interview_feedback', function () {
    $('#load_interview_feedback').html('&nbsp;');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
});

// Interview Link / Address overlay
var ilCurrent = { id: null, link: '', type: 'link' };
function closeInterviewLink() { document.getElementById('ilOverlay').classList.remove('show'); }
$(document).on('click', '.open-interview-link', function () {
    ilCurrent.id = $(this).data('id');
    ilCurrent.link = ($(this).data('link') || '').toString();
    ilCurrent.type = $(this).data('type') || 'link';
    var name = $(this).data('name') || '';
    var isAddress = (ilCurrent.type === 'address');
    $('#ilTitle').text(isAddress ? 'Interview Address' : 'Interview Link');
    $('#ilCandidate').text(name ? ('Candidate: ' + name) : '');
    $('#ilLink').text(ilCurrent.link).attr('href', isAddress ? 'javascript:void(0);' : (ilCurrent.link || '#'));
    $('#ilOpenLabel').text(isAddress ? 'Open in Maps' : 'Open Link');
    document.getElementById('ilOverlay').classList.add('show');
});
$(document).on('click', '#ilOpenBtn', function () {
    if (!ilCurrent.link) { if (typeof Swal !== 'undefined') Swal.fire({ text: 'Nothing to open.', icon: 'warning', confirmButtonText: 'Ok' }); return; }
    var url = (ilCurrent.type === 'address') ? ('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(ilCurrent.link)) : ilCurrent.link;
    window.open(url, '_blank', 'noopener');
});
$(document).on('click', '#ilOverlay', function (e) { if (e.target === this) closeInterviewLink(); });

// Close follow-up menus on outside click
document.addEventListener('click', function (e) {
    if (!e.target.closest('.it-followup-dropdown')) {
        document.querySelectorAll('.it-followup-menu.open').forEach(function (m) { m.classList.remove('open'); });
    }
});
$(document).on('keydown', function (e) { if (e.key === 'Escape') closeInterviewLink(); });

// V2 Async Profile Hydration (Refreshes profile counters from Redis cached V2 JSON APIs)
document.addEventListener('DOMContentLoaded', function () {
    if (window.fetch) {
        fetch("{{ route('service_company.api.v2.profile.kpis') }}")
            .then(function(res) { return res.json(); })
            .then(function(res) {
                if (res && res.status === 'ok') {
                    // console.log('[V2 Profile] KPIs dynamically hydrated from Redis');
                }
            })
            .catch(function(err) { /* silent catch */ });
    }
});
</script>
@endpush