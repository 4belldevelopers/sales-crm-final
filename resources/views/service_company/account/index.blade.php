@extends('layouts.service_company_layout')
<?php
use App\Helper\GeneralMethod;
// dd("jd");
$productAllowCompany = GeneralMethod::companiesType();
?>
<link rel="stylesheet" href="{{ asset('demo/plugins/custom/jkanban/jkanban.bundle.css') }}">
<style>
body {overflow-x:hidden; }
.table > :not(caption) > * > * { padding: 0.9rem 0.2rem!important; }
.kanban-container .kanban-board { float: none; flex-shrink: 0; margin-bottom: 1.25rem; background: #fff; border-radius: 0.475rem; padding-right: 1.5em!important; }
header { position: relative !important; top: 0; left: 0; display: flex; flex-direction: row; flex-wrap: wrap; width: 100%; height: 80px; background-color: #318ebb; box-shadow: 0 0px 8px rgb(212 212 212 / 40%);}
.svg-icon.svg-icon-danger-Sourcing svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #318ebb !important }
.svg-icon.svg-icon-danger-Screening svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease;fill: #c000ff !important }
.svg-icon.svg-icon-danger-Evaluation svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #009ef7 !important }
.svg-icon.svg-icon-danger-Negotiation svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #ff4098 !important}
.svg-icon.svg-icon-danger-Offer svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #ffab00 !important}
.svg-icon.svg-icon-danger-Joined svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #50cd89 !important }


@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesJob {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesJob {width: 1200px;}
}

.keyword_compant {
    display: flex !important;
}
.swal2-container.swal2-center>.swal2-popup {
    display: block !important;
}
.card {
    box-shadow: 0px 0px 20px 8px rgb(1 6 24 / 2%) !important;
    border: 0px solid;
}

input[type="radio"]:checked {
    border-color: transparent;
    background-color: currentColor;
}

/* ===== Company Dashboard redesign (scoped .cad) — Poppins, <=14px, Metronic-friendly ===== */
.cad, .cad * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; box-sizing:border-box; }
.cad { font-size:13px; color:#1f2937; }
.cad-hero { display:grid; grid-template-columns:1.5fr 1.1fr 0.9fr; gap:16px; margin:0 8px 14px; }
.cad-coins { display:flex; flex-direction:column; gap:12px; }
.cad-coin { padding:13px 15px; display:flex; flex-direction:column; gap:8px; }
.cad-coin-top { display:flex; align-items:flex-start; gap:11px; }
.cad-coin-ic { width:38px; height:38px; flex:0 0 38px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; }
.cad-coin-ic svg { width:19px; height:19px; }
.cad-coin-ic.op { background:#eef2ff; color:#3b55e6; } .cad-coin-ic.sa { background:#e8fff3; color:#16a34a; }
.cad-coin-mid { flex:1; min-width:0; }
.cad-coin-lbl { font-size:11.5px; color:#64748b; font-weight:600; }
.cad-coin-num { font-size:14px; font-weight:800; color:#0f172a; line-height:1.2; }
.cad-coin-sub { font-size:10px; color:#94a3b8; }
.cad-coin-btn { font-size:11px; font-weight:600; color:#3b55e6; text-decoration:none; border:1px solid #e2e8f0; border-radius:7px; padding:4px 9px; white-space:nowrap; }
.cad-coin-btn:hover { background:#f1f5f9; color:#3b55e6; }
.cad-coin-foot { font-size:10.5px; color:#94a3b8; border-top:1px dashed #eef0f5; padding-top:7px; }
.cad-coin-foot b { color:#475569; font-weight:600; }
@media (max-width:1400px){ .cad-hero { grid-template-columns:1.4fr 1fr; } .cad-coins { grid-column:1 / -1; flex-direction:row; } .cad-coin { flex:1; } }
@media (max-width:768px){ .cad-coins { flex-direction:column; } }
.cad-card { background:#fff; border:1px solid #eef0f5; border-radius:14px; box-shadow:0 1px 3px rgba(20,30,60,.04); }
.cad-id { padding:18px 20px; display:flex; gap:16px; align-items:flex-start; }
.cad-logo { width:74px; height:74px; border-radius:16px; flex-shrink:0; overflow:hidden; background:linear-gradient(135deg,#eef2ff,#e7efff); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#3b55e6; border:1px solid #e6ecf5; }
.cad-logo img { width:100%; height:100%; object-fit:cover; }
.cad-id-main { flex:1; min-width:0; }
.cad-name { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.cad-name h2 { font-size:14px; font-weight:700; color:#0f172a; margin:0; }
.cad-verif { width:16px; height:16px; color:#00a3ff; flex-shrink:0; }
.cad-emp-chip { display:inline-flex; align-items:center; gap:5px; background:#f1edff; color:#6741d9; font-size:10.5px; font-weight:600; padding:3px 9px; border-radius:20px; }
.cad-tag { font-size:12px; color:#64748b; margin-top:3px; }
.cad-meta { display:flex; flex-wrap:wrap; gap:8px 16px; margin-top:11px; }
.cad-meta a, .cad-meta span { display:inline-flex; align-items:center; gap:6px; font-size:11.5px; color:#475569; text-decoration:none; }
.cad-meta svg { width:14px; height:14px; color:#94a3b8; flex-shrink:0; }
.cad-meta a:hover { color:#3b55e6; }
.cad-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:14px; }
.cad-btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:8px 15px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#fff; color:#334155; text-decoration:none; transition:all .15s; }
.cad-btn svg { width:14px; height:14px; }
.cad-btn:hover { background:#f1f5f9; color:#334155; }
.cad-btn-primary { background:#3b55e6; color:#fff; border-color:#3b55e6; box-shadow:0 4px 12px rgba(59,85,230,.22); }
.cad-btn-primary:hover { background:#2f46c4; color:#fff; }
.cad-pc { padding:16px 20px; }
.cad-pc-h { font-size:13px; font-weight:700; color:#0f172a; margin-bottom:10px; }
.cad-pc-body { display:flex; gap:16px; align-items:center; }
.cad-donut { position:relative; width:104px; height:104px; flex-shrink:0; }
.cad-donut svg { transform:rotate(-90deg); }
.cad-donut-c { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.cad-donut-pct { font-size:14px; font-weight:800; color:#0f172a; line-height:1; }
.cad-donut-lbl { font-size:9.5px; color:#94a3b8; margin-top:2px; }
.cad-pc-list { flex:1; display:flex; flex-direction:column; gap:6px; }
.cad-pc-item { display:flex; align-items:center; justify-content:space-between; font-size:11.5px; color:#475569; }
.cad-pc-state { display:inline-flex; align-items:center; gap:4px; font-size:10.5px; font-weight:600; }
.cad-pc-state.done { color:#16a34a; }
.cad-pc-state.pend { color:#d97706; }
.cad-pc-state svg { width:13px; height:13px; }
.cad-pc-foot { margin-top:11px; padding-top:10px; border-top:1px dashed #eef0f5; font-size:11px; color:#94a3b8; }
.cad-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin:0 8px 14px; }
.cad-stat { background:#fff; border:1px solid #eef0f5; border-radius:12px; padding:13px 15px; display:flex; align-items:center; gap:12px; box-shadow:0 1px 3px rgba(20,30,60,.04); }
.cad-stat-ic { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.cad-stat-ic svg { width:19px; height:19px; }
.cad-bl { background:#eef2ff; color:#3b55e6; } .cad-gr { background:#e8fff3; color:#16a34a; } .cad-am { background:#fff8e6; color:#d97706; } .cad-pu { background:#f5efff; color:#7c3aed; }
.cad-stat-lbl { font-size:11.5px; color:#64748b; }
.cad-stat-num { font-size:14px; font-weight:800; color:#0f172a; line-height:1.2; }
.cad-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.cad-sec { padding:18px 20px; }
.cad-sec-h { display:flex; align-items:center; gap:9px; font-size:14px; font-weight:700; color:#0f172a; margin-bottom:14px; }
.cad-sec-h .ci { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; }
.cad-sec-h .ci svg { width:16px; height:16px; }
.cad-desc { font-size:12.5px; color:#475569; line-height:1.6; }
.cad-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding:9px 0; border-bottom:1px dashed #eef2f7; }
.cad-row:last-child { border-bottom:none; }
.cad-row .k { font-size:12px; color:#94a3b8; flex-shrink:0; }
.cad-row .v { font-size:12.5px; font-weight:600; color:#1f2937; text-align:right; word-break:break-word; }
.cad-row .v a { color:#3b55e6; text-decoration:none; }
.cad-tags { display:flex; flex-wrap:wrap; gap:6px; }
.cad-tagi { background:#eef2ff; color:#3b5bdb; font-size:10.5px; font-weight:600; padding:3px 10px; border-radius:6px; }
.cad-grid3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
.cad-chip { font-size:10px; font-weight:700; padding:2px 9px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; white-space:nowrap; }
.cad-chip svg { width:11px; height:11px; }
.cad-chip.ok { background:#e8fff3; color:#16a34a; }
.cad-chip.warn { background:#fff8e6; color:#d97706; }
.cad-chip.off { background:#fff1f3; color:#e11d48; }
.cad-mini { border:1px solid #eef0f5; border-radius:11px; padding:12px 13px; margin-bottom:10px; }
.cad-mini:last-child { margin-bottom:0; }
.cad-mini-h { display:flex; align-items:center; justify-content:space-between; gap:8px; font-size:12px; font-weight:700; color:#0f172a; }
.cad-mini-v { font-size:14px; font-weight:800; color:#0f172a; margin-top:6px; }
.cad-mini-s { font-size:10.5px; color:#94a3b8; margin-top:2px; }
.cad-team { display:flex; flex-wrap:wrap; gap:6px; margin-top:12px; }
.cad-team-av { width:30px; height:30px; border-radius:50%; overflow:hidden; background:#eef2ff; color:#3b55e6; display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; border:1px solid #e6ecf5; }
.cad-team-av img { width:100%; height:100%; object-fit:cover; }
.cad-adm { display:flex; gap:11px; align-items:flex-start; padding:9px 0; border-bottom:1px dashed #eef2f7; }
.cad-adm:last-child { border-bottom:none; }
.cad-adm-av { width:34px; height:34px; flex:0 0 34px; border-radius:50%; overflow:hidden; background:#eef2ff; color:#3b55e6; display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; border:1px solid #e6ecf5; }
.cad-adm-av img { width:100%; height:100%; object-fit:cover; }
.cad-adm-mid { min-width:0; }
.cad-adm-name { font-size:12.5px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.cad-adm-pri { font-style:normal; font-size:9px; font-weight:700; color:#3b55e6; background:#e6ebff; padding:1px 6px; border-radius:10px; }
.cad-adm-role { font-size:11px; color:#64748b; margin-top:1px; }
.cad-adm-c { font-size:10.5px; color:#94a3b8; margin-top:2px; word-break:break-word; }
@media (max-width:1100px){ .cad-hero { grid-template-columns:1fr; } .cad-stats { grid-template-columns:repeat(2,1fr); } .cad-grid2 { grid-template-columns:1fr; } .cad-grid3 { grid-template-columns:1fr 1fr; } }
@media (max-width:768px){ .cad-stats { grid-template-columns:1fr 1fr; } .cad-grid3 { grid-template-columns:1fr; } }

/* ===== Interview Pipeline Builder (scoped .ips) ===== */
.ips, .ips * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; }
.ips-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; margin:4px 2px 6px; }
.ips-title { font-size:14px; font-weight:700; color:#0f172a; }
.ips-sub { font-size:12px; color:#94a3b8; margin-top:3px; max-width:580px; }
.ips-sys { display:flex; gap:10px; align-items:center; background:#f8f9fc; border:1px solid #eef0f5; border-radius:11px; padding:10px 13px; max-width:340px; }
.ips-sys-ic { width:32px; height:32px; flex:0 0 32px; border-radius:9px; background:#eef2ff; color:#3b55e6; display:inline-flex; align-items:center; justify-content:center; }
.ips-sys-ic svg { width:16px; height:16px; }
.ips-sys-t { font-size:12px; font-weight:700; color:#0f172a; }
.ips-sys-s { font-size:10.5px; color:#94a3b8; line-height:1.4; }
.ips #kt_docs_jkanban_basic1 .kanban-container { display:block; padding-bottom:10px; }
.ips #kt_docs_jkanban_basic1 .kanban-container .row { display:grid !important; grid-template-columns:repeat(3,1fr); gap:16px; margin:0 0 16px !important; align-items:start; }
.ips #kt_docs_jkanban_basic1 .kanban-board { width:auto !important; max-width:none !important; margin:0 !important; padding:0 !important; background:#fff !important; border:1px solid #eef0f5; border-radius:14px; box-shadow:0 1px 3px rgba(20,30,60,.04); overflow:hidden; display:flex; flex-direction:column; }
@media (max-width:992px){ .ips #kt_docs_jkanban_basic1 .kanban-container .row { grid-template-columns:repeat(2,1fr); } }
@media (max-width:600px){ .ips #kt_docs_jkanban_basic1 .kanban-container .row { grid-template-columns:1fr; } }
.ips #kt_docs_jkanban_basic1 .kanban-board-header { position:relative !important; top:auto !important; left:auto !important; width:100% !important; height:auto !important; min-height:auto !important; margin:0 !important; padding:11px 13px !important; border-radius:0 !important; display:flex !important; align-items:center; justify-content:space-between; box-shadow:none !important; }
.ips #kt_docs_jkanban_basic1 .kanban-title-board { color:#fff !important; font-size:13px !important; font-weight:700 !important; }
.ips #kt_docs_jkanban_basic1 .kanban-board-header .plus-button .btn { background:rgba(255,255,255,.22) !important; color:#fff !important; border:0 !important; border-radius:7px; font-size:11px !important; font-weight:600; padding:4px 9px !important; display:inline-flex; align-items:center; gap:2px; }
.ips #kt_docs_jkanban_basic1 .kanban-board-header .plus-button .btn:hover { background:rgba(255,255,255,.36) !important; }
.ips #kt_docs_jkanban_basic1 .kanban-board-header .plus-button .btn .svg-icon svg [fill] { fill:#fff !important; }
.ips #kt_docs_jkanban_basic1 .kanban-drag { background:#fff !important; margin:0 !important; padding:12px !important; min-height:44px; position:relative !important; list-style:none; }
.ips #kt_docs_jkanban_basic1 .kanban-drag::before { content:"Sub Stages"; display:block; font-size:10.5px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:#94a3b8; margin-bottom:9px; }
.ips #kt_docs_jkanban_basic1 .kanban-item { background:#fff !important; border:1px solid #eef0f5 !important; border-radius:9px !important; padding:9px 11px !important; margin-bottom:8px !important; box-shadow:0 1px 2px rgba(20,30,60,.04) !important; display:flex !important; align-items:center; justify-content:space-between; }
.ips #kt_docs_jkanban_basic1 .kanban-item span.fw-bold { font-size:12px !important; color:#1f2937 !important; font-weight:600 !important; }
.ips #kt_docs_jkanban_basic1 .kanban-item .edit_delete .svg-icon svg { width:16px; height:16px; }
.ips-foot { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin:18px 2px 4px; padding-top:16px; border-top:1px solid #eef0f5; }
.ips-foot-item { display:flex; gap:11px; align-items:flex-start; }
.ips-foot-ic { width:36px; height:36px; flex:0 0 36px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; }
.ips-foot-ic svg { width:18px; height:18px; }
.ips-foot-t { font-size:12.5px; font-weight:700; color:#0f172a; }
.ips-foot-s { font-size:11px; color:#94a3b8; line-height:1.45; margin-top:2px; }
@media (max-width:992px){ .ips-foot { grid-template-columns:1fr; } }

/* ===== Agency Terms & Conditions template (scoped .atc) ===== */
.atc, .atc * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; }
.atc-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; margin:4px 2px 16px; }
.atc-title { font-size:14px; font-weight:700; color:#0f172a; }
.atc-sub { font-size:12px; color:#94a3b8; margin-top:4px; max-width:620px; line-height:1.5; }
.atc-sub b { color:#3b55e6; font-weight:600; }
.atc-head-actions { display:flex; gap:8px; flex-wrap:wrap; }
.atc-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 15px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#fff; color:#334155; text-decoration:none; transition:all .15s; }
.atc-btn:hover { background:#f1f5f9; color:#334155; }
.atc-btn svg { width:14px; height:14px; }
.atc-btn-primary { background:#3b55e6; color:#fff; border-color:#3b55e6; box-shadow:0 4px 12px rgba(59,85,230,.22); }
.atc-btn-primary:hover { background:#2f46c4; color:#fff; }
.atc-btn-danger { background:#fff; color:#e11d48; border-color:#fadce2; }
.atc-btn-danger:hover { background:#fff1f3; color:#e11d48; }
.atc-card { background:#fff; border:1px solid #eef0f5; border-radius:14px; box-shadow:0 1px 3px rgba(20,30,60,.04); margin:0 2px 14px; }
.atc-meta { display:flex; align-items:center; flex-wrap:wrap; gap:18px 26px; padding:15px 18px; }
.atc-meta-id { display:flex; align-items:center; gap:12px; }
.atc-meta-ic { width:42px; height:42px; flex:0 0 42px; border-radius:11px; background:#f1edff; color:#6741d9; display:inline-flex; align-items:center; justify-content:center; }
.atc-meta-ic svg { width:21px; height:21px; }
.atc-meta-lbl { font-size:10.5px; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.3px; }
.atc-meta-v { font-size:12.5px; font-weight:700; color:#0f172a; margin-top:2px; }
.atc-status { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; color:#16a34a; }
.atc-status .dot { width:7px; height:7px; border-radius:50%; background:#16a34a; }
.atc-meta .atc-btn { margin-left:auto; }
.atc-sec { padding:16px 18px; }
.atc-sec-h { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:13px; }
.atc-sec-t { font-size:13px; font-weight:700; color:#0f172a; }
.atc-sec-s { font-size:11px; color:#94a3b8; margin-top:2px; }
.atc-vars { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; }
.atc-var { border:1px solid #eef0f5; border-radius:10px; padding:10px 11px; }
.atc-var code { font-size:11px; font-weight:700; color:#6741d9; background:#f1edff; padding:2px 7px; border-radius:6px; display:inline-block; }
.atc-var-s { font-size:11px; color:#64748b; margin-top:6px; }
.atc-notice { display:flex; gap:11px; align-items:flex-start; background:#fff8eb; border:1px solid #fdebc8; border-radius:12px; padding:13px 16px; margin:0 2px 14px; }
.atc-notice-ic { width:26px; height:26px; flex:0 0 26px; border-radius:50%; background:#fdebc8; color:#d97706; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; font-style:italic; }
.atc-notice-t { font-size:12.5px; font-weight:700; color:#b45309; }
.atc-notice-s { font-size:11.5px; color:#92723a; line-height:1.5; margin-top:3px; }
.atc-editor { border:1px solid #eef0f5; border-radius:11px; padding:16px 18px; max-height:520px; overflow:auto; background:#fff; }
.atc-editor, .atc-editor * { font-size:13px; line-height:1.6; color:#334155; }
.atc-editor h1,.atc-editor h2,.atc-editor h3 { font-size:14px !important; }
@media (max-width:1200px){ .atc-vars { grid-template-columns:repeat(3,1fr); } }
@media (max-width:768px){ .atc-vars { grid-template-columns:1fr 1fr; } .atc-meta .atc-btn { margin-left:0; } }

</style>

@section('content')
 
@php $logo_path				= '';//	url('img/client-default.png'); @endphp
@if(!empty(@$account->logo) && strlen(@$account->logo)>10)
		@php //$logo_path		=  url('public/user-uploads/company-logo/'.@$account->logo); @endphp
			@php $logo_path		=  @$account->logo; 
      
      @endphp 
@endif
@php
  if (!empty(@$account->website) && empty(@$account->logo)){
          $domainGet = GeneralMethod::getDomain($account->website);
          $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
          $google = GeneralMethod::getDomainIconCompany($googleIcon,$account->id,false);
          $logo_path  = ($google) ? $googleIcon : '';
  }

@endphp
 
<!--begin::Toolbar-->
<div class="toolbar d-none" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::All Client-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <span class="text-gray-800 mb-1">Company Profile</span> </h5>
      </div>
      <!--end::All Client-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      <!--end::Separator-->
    </div>
    <!--end::Page title-->
    <!--begin::Actions-->
    <div class="d-flex align-items-center py-1">
      <!--begin::Search-->
      <div class="d-flex align-items-center position-relative my-1">
        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
        <span class="svg-icon svg-icon-1 position-absolute ms-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
          <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
        </svg>
        </span>
        <!--end::Svg Icon-->
        <input type="hidden" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>
      <!--end::Search-->
    </div>
    <!--end::Actions-->
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content" style="margin-top:-50px;">
<!--begin::Post-->
<div class="post d-flex flex-column-fluid" id="kt_post">
<!--begin::Container-->
<div id="kt_content_container" class="container-fluid px-0">
<!--begin::Company Dashboard hero (redesigned)-->
@php
  // Profile completion — derived only from already-loaded $account (no extra queries)
  $cad_pc = [
    'Basic Information' => (!empty(@$account->company_name) && !empty(@$industries_name)),
    'Company Details'   => (!empty(@$account->company_size) || !empty(@$account->founded)),
    'Branding'          => !empty($logo_path),
    'Description'       => !empty(trim(strip_tags((string)@$account->description))),
    'Location'          => (!empty($company_city_name) || !empty($company_country_name)),
    'Website'           => !empty(@$account->website),
  ];
  $cad_done  = count(array_filter($cad_pc));
  $cad_total = count($cad_pc);
  $cad_pct   = $cad_total ? round($cad_done/$cad_total*100) : 0;
  $cad_r = 52; $cad_circ = 2*M_PI*$cad_r; $cad_off = $cad_circ*(1-$cad_pct/100);
  $cad_initial = strtoupper(substr(trim((string)@$account->company_name),0,1));
  $cad_size = '';
  if(!empty(@$account->company_size_from)){
    $cad_size = (@$account->company_size_from >= 10000) ? $account->company_size_from.'+ employees' : trim($account->company_size_from.' - '.@$account->company_size).' employees';
  }
@endphp
<div class="cad">
  <div class="cad-hero">
    <!-- Identity -->
    <div class="cad-card cad-id">
      <div class="cad-logo">@if(!empty($logo_path))<img src="{{ $logo_path }}" alt="">@else{{ $cad_initial }}@endif</div>
      <div class="cad-id-main">
        <div class="cad-name">
          <h2>{{ ucwords(@$account->company_name) }}</h2>
          <svg class="cad-verif" viewBox="0 0 24 24" fill="currentColor"><path d="M10.08 3.72c.8-1.56 3.03-1.56 3.84 0a2.07 2.07 0 0 0 2.58.95c1.67-.53 3.25 1.04 2.71 2.71a2.07 2.07 0 0 0 .95 2.58c1.56.8 1.56 3.03 0 3.84a2.07 2.07 0 0 0-.95 2.58c.54 1.67-1.04 3.25-2.71 2.71a2.07 2.07 0 0 0-2.58.95c-.8 1.56-3.03 1.56-3.84 0a2.07 2.07 0 0 0-2.58-.95c-1.67.54-3.25-1.04-2.71-2.71a2.07 2.07 0 0 0-.95-2.58c-1.56-.8-1.56-3.03 0-3.84a2.07 2.07 0 0 0 .95-2.58c-.53-1.67 1.04-3.25 2.71-2.71a2.07 2.07 0 0 0 2.58-.95z"/><path d="M14.86 9.19c.2-.24.52-.25.76-.05.24.21.2.52 0 .76l-3.75 4.75c-.21.25-.58.27-.82.06l-2.57-2.29a.54.54 0 0 1-.05-.81c.21-.24.57-.26.8-.05l2.14 1.9 3.49-4.27z" fill="#fff"/></svg>
          <span class="cad-emp-chip"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg> Verified Employer</span>
        </div>
        <div class="cad-tag">{{ @$industries_name ?: 'Staffing & Recruiting Solutions' }}</div>
        <div class="cad-meta">
          @if($company_city_name || $company_country_name)<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>{{ trim(ucwords($company_city_name).(($company_city_name&&$company_country_name)?', ':'').ucwords($company_country_name),', ') }}</span>@endif
          @if($cad_size)<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>{{ $cad_size }}</span>@endif
          @if(@$account->founded)<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>Founded {{ $account->founded }}</span>@endif
          @if(@$account->website)<a href="{{ (strpos($account->website,'http')===0?'':'//').$account->website }}" target="_blank"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/></svg>{{ $account->website }}</a>@endif
        </div>
        <div class="cad-actions">
          @if(in_array("edit_settings", $userPermissions))
          <a href="{{ route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}" class="cad-btn cad-btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4z"/></svg> Edit Profile</a>
          @endif
          @if(@$account->website)<a href="{{ (strpos($account->website,'http')===0?'':'//').$account->website }}" target="_blank" class="cad-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6M10 14 21 3"/></svg> Visit Website</a>@endif
        </div>
      </div>
    </div>
    <!-- Profile completion -->
    <div class="cad-card cad-pc">
      <div class="cad-pc-h">Profile Completion</div>
      <div class="cad-pc-body">
        <div class="cad-donut">
          <svg width="104" height="104" viewBox="0 0 120 120">
            <circle cx="60" cy="60" r="{{ $cad_r }}" fill="none" stroke="#eef0f5" stroke-width="11"/>
            <circle cx="60" cy="60" r="{{ $cad_r }}" fill="none" stroke="#6741d9" stroke-width="11" stroke-linecap="round" stroke-dasharray="{{ $cad_circ }}" stroke-dashoffset="{{ $cad_off }}"/>
          </svg>
          <div class="cad-donut-c"><div class="cad-donut-pct">{{ $cad_pct }}%</div><div class="cad-donut-lbl">Complete</div></div>
        </div>
        <div class="cad-pc-list">
          @foreach($cad_pc as $label => $done)
            <div class="cad-pc-item"><span>{{ $label }}</span>
              @if($done)<span class="cad-pc-state done"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg> Completed</span>
              @else<span class="cad-pc-state pend"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg> Pending</span>@endif
            </div>
          @endforeach
        </div>
      </div>
      <div class="cad-pc-foot">Complete your profile to build trust and attract more talent.</div>
    </div>
    <!-- Coins -->
    @php
      $opEnd = @$operationCoins->end_date; $saEnd = @$salesCoins->end_date;
      $opDays = ($opEnd && strtotime($opEnd) > time()) ? (int) floor((strtotime($opEnd)-time())/86400) : 0;
      $saDays = ($saEnd && strtotime($saEnd) > time()) ? (int) floor((strtotime($saEnd)-time())/86400) : 0;
      $coinUrl = \Route::has('subscribe.newindex') ? route('subscribe.newindex') : 'javascript:void(0);';
    @endphp
    <div class="cad-coins">
      <div class="cad-card cad-coin">
        <div class="cad-coin-top">
          <span class="cad-coin-ic op"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/></svg></span>
          <div class="cad-coin-mid">
            <div class="cad-coin-lbl">Operation Coins</div>
            <div class="cad-coin-num">{{ number_format((int) (@$operationCoins->coins ?? 0)) }}</div>
            <div class="cad-coin-sub">Available</div>
          </div>
          <a href="{{ $coinUrl }}" class="cad-coin-btn">View Details</a>
        </div>
        @if($opEnd)<div class="cad-coin-foot">Expires on <b>{{ date('d M Y', strtotime($opEnd)) }}</b> · {{ $opDays }} days left</div>@endif
      </div>
      <div class="cad-card cad-coin">
        <div class="cad-coin-top">
          <span class="cad-coin-ic sa"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/></svg></span>
          <div class="cad-coin-mid">
            <div class="cad-coin-lbl">Sales Coins</div>
            <div class="cad-coin-num">{{ number_format((int) (@$salesCoins->coins ?? 0)) }}</div>
            <div class="cad-coin-sub">Available</div>
          </div>
          <a href="{{ $coinUrl }}" class="cad-coin-btn">View Details</a>
        </div>
        @if($saEnd)<div class="cad-coin-foot">Expires on <b>{{ date('d M Y', strtotime($saEnd)) }}</b> · {{ $saDays }} days left</div>@endif
      </div>
    </div>
  </div>
  <!-- Stat strip (real, already-loaded data — no extra queries) -->
  <div class="cad-stats">
    <div class="cad-stat"><div class="cad-stat-ic cad-bl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></div><div><div class="cad-stat-lbl">Active Jobs</div><div class="cad-stat-num">{{ (int) @$activeJobs }}</div></div></div>
    <div class="cad-stat"><div class="cad-stat-ic cad-gr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg></div><div><div class="cad-stat-lbl">Team Members</div><div class="cad-stat-num">{{ (int) @$team_count }}</div></div></div>
    <div class="cad-stat"><div class="cad-stat-ic cad-am"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><div><div class="cad-stat-lbl">Founded</div><div class="cad-stat-num">{{ @$account->founded ?: '—' }}</div></div></div>
    <div class="cad-stat"><div class="cad-stat-ic cad-pu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg></div><div><div class="cad-stat-lbl">Profile Completion</div><div class="cad-stat-num">{{ $cad_pct }}%</div></div></div>
  </div>
</div>
<!--end::Company Dashboard hero-->
<!--beign::Company Details (legacy, hidden)-->
<div class="card mb-5 mb-xl-5 mx-2 d-none">
  <div class="card-body pt-5 pb-0 px-5">
    <!--begin::Details-->
    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
      <!--begin: Pic-->
      <div class="me-7 mb-4">
        <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
          @php
          $bgTextDecore = $strVal = '';
          $str = trim($account->company_name);
          $str = $strVal = strtolower($str[0]);
          @endphp
          @if (!empty($logo_path)) 
          <img src="{{ @$logo_path }}" alt="image">
          @else
          <div class="symbol-label fs-3 {{ @$bgTextDecore}} border-radius-5"><span>{{ ucwords(@$strVal)}}</span></div>
          @endif


          <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
        </div>
      </div>
      <!--end::Pic-->
      <!--begin::Info-->
      <div class="flex-grow-1">
        <!--begin::Row-1-->
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
          <!--begin::Candidate Details-->
          <div class="d-flex flex-column">
           
            <div class="d-flex align-items-center mb-2"> 
				<span class="text-gray-900 fs-2 fw-bolder me-2">{{ ucwords(@$account->company_name) }}</span> 
				<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Verified">
              <span class="svg-icon svg-icon-1 svg-icon-success">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
              </svg>
              </span>
              </a> 
			  </div>
            
            <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
              <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"/>
                  <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                  <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                </g>
              </svg>
              </span> 
			  	{{ @$industries_name }}
			  </a> 
			  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 me-5">
              <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
              </svg>
              </span>
			  	{{ ucwords($company_city_name) }} {{ ' , ' }} {{ ucwords($company_country_name) }}
              </a>
			  
			   <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
              <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                  <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                </g>
              </svg>
              </span>
              ------- </a> 
			  </div>
            <!--end::Designaiton-->
          </div>
          <!--end::Company Details-->
        </div>
        <!--end::Row-1-->
        <!--begin::Row-2-->
        <div class="d-flex flex-wrap flex-stack">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column flex-grow-1 pe-8">
            <div class="row w-75"> <span class="">
				 @if(!empty($client->tags))
					 @php $tags	=	explode(',', $client->tags); @endphp 
					 @foreach($tags as $key=>$val) 
						{!! '<span class="badge badge-light me-2">'.ucwords(@$val).'</span>' !!}
					 @endforeach
				 @endif		
              </span> </div>
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Row-2-->
      </div>
      <!--end::Info-->
    </div>
    <!--begin::separator-->
    {{-- <div class="separator separator-solid my-7"></div> --}}
    <!--end::separator-->
    <!--begin::section-->
    <div class="d-flex align-items-center flex-wrap pb-7 d-none">
      <!--begin: Item1-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <!--begin::Svg-->
        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"/>
            <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
            <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
            <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Type</span> <span class="fw-bolder fs-3">
            
				@if($account->industries_type >= 0)
					{{ $industries_type_arr[$account->industries_type] }}    
				@endif						
          </span> </div>
      </div>
      <!--end: Item1-->
      <!--begin: Item2-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <!--begin::Svg-->
        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <polygon points="0 0 24 0 24 24 0 24"></polygon>
            <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
            <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        <div class="d-flex flex-column text-dark-75"> 
		    	<span class="fw-bolder fs-6">Employees</span> <span class="fw-bolder fs-3">{{ @$account->company_size }}</span> 
		    </div>
      </div>
      <!--end: Item2-->
      <!--begin: Item3-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <!--begin::Svg-->
              <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"></rect>
                  <path d="M4.875,20.75 C4.63541667,20.75 4.39583333,20.6541667 4.20416667,20.4625 L2.2875,18.5458333 C1.90416667,18.1625 1.90416667,17.5875 2.2875,17.2041667 C2.67083333,16.8208333 3.29375,16.8208333 3.62916667,17.2041667 L4.875,18.45 L8.0375,15.2875 C8.42083333,14.9041667 8.99583333,14.9041667 9.37916667,15.2875 C9.7625,15.6708333 9.7625,16.2458333 9.37916667,16.6291667 L5.54583333,20.4625 C5.35416667,20.6541667 5.11458333,20.75 4.875,20.75 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z" fill="#000000"></path>
                </g>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <div class="d-flex flex-column text-dark-75"> 
               <span class="fw-bolder fs-6">Active Req.</span> <span class="fw-bolder fs-3">{{ @$activeJobs }}</span> 
              </div>
      </div>
      <!--end: Item3-->
      <!--begin: Item4-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <!--begin::Svg-->
        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"></rect>
            <path d="M7,3 L17,3 C19.209139,3 21,4.790861 21,7 C21,9.209139 19.209139,11 17,11 L7,11 C4.790861,11 3,9.209139 3,7 C3,4.790861 4.790861,3 7,3 Z M7,9 C8.1045695,9 9,8.1045695 9,7 C9,5.8954305 8.1045695,5 7,5 C5.8954305,5 5,5.8954305 5,7 C5,8.1045695 5.8954305,9 7,9 Z" fill="#000000"></path>
            <path d="M7,13 L17,13 C19.209139,13 21,14.790861 21,17 C21,19.209139 19.209139,21 17,21 L7,21 C4.790861,21 3,19.209139 3,17 C3,14.790861 4.790861,13 7,13 Z M17,19 C18.1045695,19 19,18.1045695 19,17 C19,15.8954305 18.1045695,15 17,15 C15.8954305,15 15,15.8954305 15,17 C15,18.1045695 15.8954305,19 17,19 Z" fill="#000000" opacity="0.3"></path>
          </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          <div class="d-flex flex-column text-dark-75"> 
            <span class="fw-bolder fs-6">Founded</span> <span class="fw-bolder fs-3">{{ @$yearAgo }}</span> 
          </div>
      </div>
      <!--end: Item4-->
      <!--begin: Item5-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
          <!--begin::Svg-->
          <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"></rect>
              <path d="M12,21 C7.02943725,21 3,16.9705627 3,12 C3,7.02943725 7.02943725,3 12,3 C16.9705627,3 21,7.02943725 21,12 C21,16.9705627 16.9705627,21 12,21 Z M14.1654881,7.35483745 L9.61055177,10.3622525 C9.47921741,10.4489666 9.39637436,10.592455 9.38694497,10.7495509 L9.05991526,16.197949 C9.04337012,16.4735952 9.25341309,16.7104632 9.52905936,16.7270083 C9.63705011,16.7334903 9.74423017,16.7047714 9.83451193,16.6451626 L14.3894482,13.6377475 C14.5207826,13.5510334 14.6036256,13.407545 14.613055,13.2504491 L14.9400847,7.80205104 C14.9566299,7.52640477 14.7465869,7.28953682 14.4709406,7.27299168 C14.3629499,7.26650974 14.2557698,7.29522855 14.1654881,7.35483745 Z" fill="#000000"></path>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Joined</span> <span class="fw-bolder fs-3">{{ @$joined }}</span> </div>
      </div>
      <!--end: Item5-->
      <!--begin: Item-->
      <div class="d-flex align-items-center flex-lg-fill my-1">
            <!--begin::Svg Icon-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
              </g>
            </svg>
            </span>
        <!--end::Svg Icon-->
        <div class="symbol-group symbol-hover">
					 		
          @if(!empty($team))
              @foreach($team as $teamVal)
                @php
                  $name				=	'';
                  $name				=	ucwords($teamVal->name);
                  $logo_path			=	@$teamVal->image; 	
                  $bgTextDecore =  GeneralMethod::icoinText($name);	
                @endphp	
                @if(!empty($logo_path))
                {!! '<div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="'.@$name.'"><img alt="Pic" src="'.@$logo_path.'"></div>' !!}
                @else
                <div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="{{ @$name }}">
                  
                  <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$name)}}" >
                    <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                    <a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
                      <div class="symbol-label fs-8 fs-8 {{ $bgTextDecore}} border-radius-100">{{ substr($name, 0, 1)}}</div>
                    </a>		 
                    </div>
                    </div>

                </div>
                @endif

              @endforeach
          @endif                                 
          @if($team_count > 5)
                {!! '<div class="symbol symbol1 symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="List.."><span class="symbol-label symbol-label1 font-weight-bold">5+</span></div>' !!}
          @endif                                  
 
        </div>
      </div>
      <!--end: Item-->
    </div>
    <!--end::sections-->
    <!--end::Details-->
  </div>
</div>
<!--end::Company Details-->
<!--begin::Tabs-->
<div class="card card-custom mx-2">
<!--Begin::Tabs Header-->
<div class="card-header card-header-tabs-line">
  <div class="card-toolbar">
    <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
      <li class="nav-item me-3"> <a class="nav-link {{$overview_tab}}" data-toggle="tab" href="#overview"> <span class="nav-text font-weight-bold">Overview</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#interview_stage"> <span class="nav-text font-weight-bold">Interview Stage</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#settings"> <span class="nav-text font-weight-bold">Settings</span> </a> </li>

      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#offerterms"> 
          <span class="nav-text font-weight-bold">Terms & Conditions</span>
          <span class="fw-bold text-muted d-block fs-7" style=" text-align: center;">(For Candidate Offer Letter)</span>
           </a> 
      </li>

      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#offercomponent"> 
        <span class="nav-text font-weight-bold">Salary Structure</span>
        <span class="fw-bold text-muted d-block fs-7" style=" text-align: center;">(For Candidate Offer Letter)</span>
         </a> 
    </li>

    <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#terms" style="display: inline-grid;padding-bottom: 5px;"> <span class="nav-text font-weight-bold">Terms & Conditions</span>
      <span class="fw-bold text-muted d-block fs-7" style=" text-align: center;">(For {{__('messages.Agency')}})</span> </a> </li>

        @if(@$productAllowCompany)
         <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#services"> 
        <span class="nav-text font-weight-bold">Commericials</span>
        <span class="fw-bold text-muted d-block fs-7" style=" text-align: center;">(For Clients)</span>
         </a> </li>
        

      <li class="nav-item me-3"> <a class="nav-link {{$boostkeyword_tab}}" data-toggle="tab" href="#boostkeyword"> <span
          class="nav-text font-weight-bold"> Ads Settings  </span> </a> </li> 

      @endif
    </ul>
  </div>
</div>
<!--end::Tabs Header-->
<!--Begin::Body-->
<div class="card-body py-0">
<div class="tab-content pt-5">
<!--begin::Overview Content-->
<div class="tab-pane {{$overview_tab}}" id="overview" role="tabpanel">
  <!--begin::Overview header-->
  <div class="card-header cursor-pointer px-0">
    <!--begin::Card title-->
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Overview</h3>
    </div>
    <!--end::Card title-->
    @if(in_array("edit_settings", $userPermissions))
    <!--begin::Action-->
    <a href="{{ route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}" class="btn btn-primary align-self-center">Edit Profile</a>
    <!--ensd::Action-->
    @endif </div>
  <!--end::Overview header-->
  <div class="card-body px-0 py-7"> @if(in_array("view_settings", $userPermissions))
    <!--begin::overview Content (redesigned)-->
    <div class="cad">
      <div class="cad-grid3">
        <!-- About -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-bl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>About {{ ucwords(@$account->company_name) }}</div>
          @if(!empty(trim(strip_tags((string)@$account->description))))
            <div class="cad-desc">{!! @$account->description !!}</div>
          @else
            <div class="cad-desc" style="color:#94a3b8;">No company description added yet.</div>
          @endif
          @if(!empty(@$client->tags))
            <div style="margin-top:14px;">
              <div class="cad-row" style="border:none;padding-bottom:6px;"><span class="k">Specialties</span></div>
              <div class="cad-tags">@foreach(explode(',', $client->tags) as $tg)@if(trim($tg)!=='')<span class="cad-tagi">{{ ucwords(trim($tg)) }}</span>@endif @endforeach</div>
            </div>
          @endif
        </div>
        <!-- Company details -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-pu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M8 10h.01M16 10h.01"/></svg></span>Company Information</div>
          <div class="cad-row"><span class="k">Industry</span><span class="v">{{ @$industries_name ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Company Size</span><span class="v">{{ $cad_size ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Headquarters</span><span class="v">{{ trim(ucwords($company_city_name).(($company_city_name&&$company_country_name)?', ':'').ucwords($company_country_name),', ') ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Type</span><span class="v">@if(isset($account->industries_type) && $account->industries_type >= 0){{ @$industries_type_arr[$account->industries_type] }}@else—@endif</span></div>
          <div class="cad-row"><span class="k">Founded</span><span class="v">{{ @$account->founded ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Website</span><span class="v">@if(!empty(@$account->website))<a href="{{ (strpos($account->website,'http')===0?'':'//').$account->website }}" target="_blank">{{ $account->website }}</a>@else—@endif</span></div>
        </div>
        <!-- Configuration Checklist -->
        @php
          $cad_stageCount = count((array)@$interviewStage_sourcing)+count((array)@$interviewStage_screening)+count((array)@$interviewStage_evaluation)+count((array)@$interviewStage_negotiation)+count((array)@$interviewStage_offered)+count((array)@$interviewStage_joined)+count((array)@$interviewStage_reject);
          $cad_cfg = [
            'Company profile completed'   => ($cad_pct >= 100),
            'Interview stages configured' => ($cad_stageCount > 0),
            'Offer letter template set'   => !empty(trim(strip_tags((string)@$account->offer_terms))),
            'Agency terms & conditions'   => !empty(trim(strip_tags((string)@$account->terms))),
            'Salary structure configured' => (!empty(@$offer_points) && count($offer_points) > 0),
            'Commercials configured'      => ((@$contractStatus->status ?? 0) || (@$fullTimeStatus->status ?? 0)),
          ];
        @endphp
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-gr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3 8-8"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></span>Configuration Checklist</div>
          @foreach($cad_cfg as $label => $ok)
            <div class="cad-row"><span class="k">{{ $label }}</span><span class="v">
              @if($ok)<span class="cad-chip ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg> Completed</span>
              @else<span class="cad-chip warn">Pending</span>@endif
            </span></div>
          @endforeach
        </div>
        <!-- Services & Rates -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-am"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>Services &amp; Rates</div>
          <div class="cad-mini">
            <div class="cad-mini-h">Contract Hiring
              <span class="cad-chip {{ (@$contractStatus->status) ? 'ok' : 'off' }}">{{ (@$contractStatus->status) ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="cad-mini-s">{{ (int) @$contractCount }} {{ (int)@$contractCount === 1 ? 'country' : 'countries' }} served</div>
          </div>
          <div class="cad-mini">
            <div class="cad-mini-h">Full-time Hiring
              <span class="cad-chip {{ (@$fullTimeStatus->status) ? 'ok' : 'off' }}">{{ (@$fullTimeStatus->status) ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="cad-mini-s">{{ (int) @$fullTimeCount }} {{ (int)@$fullTimeCount === 1 ? 'country' : 'countries' }} served</div>
          </div>
        </div>
        <!-- Settings -->
        @php
          $cad_cur = optional($currencyArr->firstWhere('id', @$account->currency));
          $cad_tf  = optional($time_formatArr->firstWhere('id', @$account->time_format));
          $cad_lang= optional($languagesArr->firstWhere('id', @$account->language));
          $cad_dup = (@$account->app_duplicate == 2) ? 'Overall system'.((@$account->app_duplicate_days)?' · '.$account->app_duplicate_days.' days':'')
                    : ((@$account->app_duplicate == 1) ? 'Job-wise'.((@$account->app_duplicate_days)?' · '.$account->app_duplicate_days.' days':'') : 'Disabled');
        @endphp
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-pu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>Settings</div>
          <div class="cad-row"><span class="k">Duplicate Status</span><span class="v">{{ $cad_dup }}</span></div>
          <div class="cad-row"><span class="k">Time Zone</span><span class="v">{{ @$account->timezone ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Currency</span><span class="v">{{ $cad_cur->code ? $cad_cur->code.' · '.$cad_cur->country : '—' }}</span></div>
          <div class="cad-row"><span class="k">Time Format</span><span class="v">{{ $cad_tf->name ?: '—' }}</span></div>
          <div class="cad-row"><span class="k">Language</span><span class="v">{{ $cad_lang->name ?: '—' }}</span></div>
        </div>
        <!-- Team Overview -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-bl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg></span>Team Overview</div>
          <div class="cad-row"><span class="k">Active Recruiters</span><span class="v">{{ (int) @$team_count }}</span></div>
          <div class="cad-row"><span class="k">Interview Stages</span><span class="v">{{ $cad_stageCount }}</span></div>
          @if(!empty(@$team) && count($team))
            <div class="cad-team">
              @foreach($team as $tm)
                @if(!empty($tm->image))<span class="cad-team-av" title="{{ ucwords($tm->name) }}"><img src="{{ $tm->image }}" alt=""></span>
                @else<span class="cad-team-av" title="{{ ucwords($tm->name) }}">{{ strtoupper(substr($tm->name,0,1)) }}</span>@endif
              @endforeach
              @if(@$team_count > count($team))<span class="cad-team-av">+{{ $team_count - count($team) }}</span>@endif
            </div>
          @endif
        </div>
        <!-- Marketplace Performance -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-am"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l1-5h16l1 5M4 9h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9zM9 13h6"/></svg></span>Marketplace Performance</div>
          <div class="cad-row"><span class="k">Agencies Empaneled</span><span class="v">{{ (int) @$agenciesEmpaneled }}</span></div>
          <div class="cad-row"><span class="k">Active Agencies</span><span class="v">{{ (int) @$agenciesActive }}</span></div>
        </div>
        <!-- Administrators -->
        <div class="cad-card cad-sec">
          <div class="cad-sec-h"><span class="ci cad-bl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>Administrators</div>
          @forelse(@$administrators ?? [] as $adm)
            <div class="cad-adm">
              @if(!empty($adm->image))<span class="cad-adm-av"><img src="{{ $adm->image }}" alt=""></span>
              @else<span class="cad-adm-av">{{ strtoupper(substr($adm->name,0,1)) }}</span>@endif
              <div class="cad-adm-mid">
                <div class="cad-adm-name">{{ ucwords($adm->name) }}@if(@$adm->root_user)<em class="cad-adm-pri">Primary</em>@endif</div>
                <div class="cad-adm-role">{{ @$adm->designation ?: 'Administrator' }}</div>
                <div class="cad-adm-c">{{ $adm->email }}@if(@$adm->mobile) · {{ $adm->mobile }}@endif</div>
              </div>
            </div>
          @empty
            <div class="cad-desc" style="color:#94a3b8;">No administrators found.</div>
          @endforelse
        </div>
      </div>
    </div>
    <!--end::overview Content (redesigned)-->
    <div class="row d-none">
      <!--begin::Col-Desc-->
      <div class="col-lg-12 mb-7"> {!! @$account->description !!} </div>
      <div class="col-lg-12">
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Website</label>
          <div class="col-lg-9"> <a href="javascript:void(0);" class="fw-bold fs-6 text-primary">{{ @$account->website }}</a> </div>
        </div>
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Industry</label>
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
            {{ @$industries_name }}
            </span> </div>
        </div>
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Size</label>
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
            @if(@$account->company_size_from >= 10000)
              {{ @$account->company_size_from }}+ employees 
            @else
             {{ @$account->company_size_from }} - {{ @$account->company_size }} employees 
            @endif

            <a class="d-none" href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="4Bell is Information Technology Compnay">
            <!--begin::Svg Icon-->
            <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"></rect>
                <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"></circle>
                <rect fill="#000000" x="11" y="10" width="2" height="7" rx="1"></rect>
                <rect fill="#000000" x="11" y="7" width="2" height="2" rx="1"></rect>
              </g>
            </svg>
            </span>
            </a> </span>
           </div>
        </div>
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Headquarters</label>
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
		  	{{ ucwords($company_city_name) }} {{ ' , ' }} {{ ucwords($company_country_name) }}
            </span> </div>
        </div>
        <div class="row mb-7">
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Type</label>
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">                           
					  @if($account->industries_type >= 0)					  	
						 {{ $industries_type_arr[$account->industries_type] }}   
					  @endif				             	
            </span> </div>
        </div>      
        <div class="row mb-7">         
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Founded</label>          
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">{{ @$account->founded }}</span> </div>         
        </div>        
        <div class="row mb-7">          
          <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Specialties</label>          
          <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">{{ @$client->tags}}</span> </div>          
        </div>
       
      </div>
    </div>
    
    @endif </div>
</div>
<!--end::overview Content-->
<!--begin::Interview Stage Content-->
<div class="tab-pane ips" id="interview_Stage" role="tabpanel">
  <!--begin::Pipeline Builder header-->
  <div class="ips-head">
    <div>
      <div class="ips-title">Interview Pipeline Builder</div>
      <div class="ips-sub">Design and manage your interview pipeline. Configure stages and sub-stages for your hiring process.</div>
    </div>
    <div class="ips-sys">
      <span class="ips-sys-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
      <div><div class="ips-sys-t">System Stage</div><div class="ips-sys-s">Some stages are system defined and cannot be edited or deleted.</div></div>
    </div>
  </div>
  <!--end::Pipeline Builder header-->
  <div class="card-body px-0 py-7">
    <div id="kt_docs_jkanban_basic1">
      <div class="kanban-container" style="">
        <div class="row w-100" style="">
          <div data-id="Sourcing" data-order="1" class="col-md-4 kanban-board px-0" id="base_sourcing" >
            <header class="kanban-board-header dark d-flex align-items-center justify-content-between position-absolute bg-transparent" style="background-color:#318ebb !important" >
              <div class="kanban-title-board">Sourcing</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn-light interview_stage" data-class="bg-secondary" data-title="Sourcing" data-id="1" data-root="root_sourcing" data-toggle="modal" data-target="#add_interview_sub_stage" style="color:#318ebb !important"> <span class="svg-icon svg-icon svg-icon me-0" >
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                  </g>
                </svg>
                </span> Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_sourcing" style="margin-top:80px;" >
                @if(!empty($interviewStage_sourcing))
                    @foreach($interviewStage_sourcing as $val)									  				
                    {!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between bg-secondary root_child">
                                <span class="fw-bold" style="color:#318ebb !important" >'.@$val->name.'</span>
                                <div class="edit_delete cursor-pointer">                 
                                  <a class="stage_block text-danger delete_process" href="javascript:void(0);" rel="'.@$val->id.'" >
                                    <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Sourcing">
                                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                      </g>
                                      </svg>
                                    </span>
                                  </a>          											 
                                </div>
                                </li>' !!}
                    @endforeach
                @endif	

            </ul>
            @endif 
          </div>
          <div data-id="Screening" data-order="2" class="col-md-4 kanban-board px-0" >
            <header class="kanban-board-header screening d-flex align-items-center justify-content-between" style="background-color:#c000ff !important" >
              <div class="kanban-title-board text-white">Screening</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm bg-light-info interview_stage" data-class="screening-light" data-title="Screening" data-id="2" data-root="root_screening" data-toggle="modal" data-target="#add_interview_sub_stage" style="background-color:#fae9ff !important; color:#c000ff !important">
                <!--begin::Svg Icon-->
                <span class="svg-icon svg-icon svg-icon me-0">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                  </g>
                </svg>
                </span>
                <!--end::Svg Icon-->
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_screening">
                @if(!empty($interviewStage_screening))
                    @foreach($interviewStage_screening as $val)
                      {!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between screening-light" data-class="">
                            <span class="fw-bold">'.@$val->name.'</span>
                            <div class="edit_delete cursor-pointer">
                              <a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'">
                                <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Screening">
                                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                    <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                  </g>
                                  </svg>
                                </span>
                              </a>              
                            </div>
                            </li>' !!}
                    @endforeach
                @endif
            </ul>
            @endif 
          </div>
          <div data-id="Evaluation" data-order="3" class="col-md-4 kanban-board px-0">
            <header class="kanban-board-header primary d-flex align-items-center justify-content-between">
              <div class="kanban-title-board text-white">Evaluation</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn btn-sm bg-light-primary interview_stage" data-class="bg-light-primary" data-title="Evaluation" data-id="3" data-root="root_evaluation" data-toggle="modal" data-target="#add_interview_sub_stage" style="color: #009ef7 !important">
                <!--begin::Svg Icon-->
                <span class="svg-icon svg-icon svg-icon me-0">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                  </g>
                </svg>
                </span>
                <!--end::Svg Icon-->
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_evaluation" style="position: relative;" >
              @if(!empty($interviewStage_evaluation))
                @foreach($interviewStage_evaluation as $val)
                  {!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between bg-light-primary" data-class="">
                    <span class="fw-bold" style="color:#009ef7">'.$val->name.'</span>
                    <div class="edit_delete cursor-pointer">
                      <a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'" >
                        <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Evaluation">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"/>
                            <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                            <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                          </g>
                          </svg>
                        </span>				
                      </a>          
                    </div>
                    </li>' !!}
                @endforeach
              @endif
            </ul>
            @endif 
          </div>
        </div>
        <div class="row w-100" style="">
          <div data-id="Negotation" data-order="4" class="col-md-4 kanban-board px-0">
            <header class="kanban-board-header negotation d-flex align-items-center justify-content-between">
              <div class="kanban-title-board text-white">Negotiation</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn btn-sm negotation-light interview_stage" data-class="negotation-light" data-title="Negotation" data-id="4" data-root="root_negotation"  data-toggle="modal" data-target="#add_interview_sub_stage">
                <!--begin::Svg Icon-->
                <span class="svg-icon svg-icon svg-icon me-0">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                  </g>
                </svg>
                </span>
                <!--end::Svg Icon-->
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_negotation" style="position: relative;">
              @if(!empty($interviewStage_negotiation))
                @foreach($interviewStage_negotiation as $val)
                  {!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between negotation-light" data-class="">
                        <span class="fw-bold">'.$val->name.'</span>
                        <div class="edit_delete cursor-pointer">						
                          <a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'">
                            <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Negotiation">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                              </g>
                              </svg>
                            </span>
                          </a>             						
                        </div>
                        </li>' !!}
                @endforeach
              @endif
            </ul>
            @endif 
          </div>
          <div data-id="Offer" data-order="5" class="col-md-4 kanban-board px-0" >
            <header class="kanban-board-header offer d-flex align-items-center justify-content-between" style="background-color:#ffab00!important;">
              <div class="kanban-title-board text-white">Offer</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn btn-sm offer-light interview_stage" data-class="offer-light" data-title="Offer" data-id="5" data-root="root_offer" data-toggle="modal" data-target="#add_interview_sub_stage">
                <!--begin::Svg Icon-->
                <span class="svg-icon svg-icon svg-icon me-0">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                  </g>
                </svg>
                </span>
                <!--end::Svg Icon-->
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_offer" style="position: relative;">
                @if(!empty($interviewStage_offered))
                  @foreach($interviewStage_offered as $val)
                    {!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between offer-light" data-class="">
                          <span class="fw-bold">'.$val->name.'</span>
                          <div class="edit_delete cursor-pointer">
                            <a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'">
                              <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Offer">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                                </svg>
                              </span> 
                            </a>     
                          </div>
                          </li>' !!}
                @endforeach
                @endif
            </ul>
            @endif 
          </div>
          <div data-id="Joined" data-order="5" class="col-md-4 kanban-board px-0">
            <header class="kanban-board-header success d-flex align-items-center justify-content-between">
              <div class="kanban-title-board text-white">Joined</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn btn-sm joined-light interview_stage" data-class="joined-light" data-title="Joined" data-id="6" data-root="root_joined" data-toggle="modal" data-target="#add_interview_sub_stage">
                <!--begin::Svg Icon-->
                <span class="svg-icon svg-icon svg-icon me-0">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                  </g>
                </svg>
                </span>
                <!--end::Svg Icon-->
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_joined" style="position: relative;">
                @if(!empty($interviewStage_joined))
                  @foreach($interviewStage_joined as $val)
                  @php
                    $delete_hide = '';
                    if($val->delete_status==0){
                      $delete_hide = 'd-none';
                    }
                  @endphp
                  
                 
                    {!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between joined-light" data-class="">
                          <span class="fw-bold">'.$val->name.'</span>
                         
                          <div class="edit_delete cursor-pointer '.$delete_hide.'">
                            <a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'">
                              <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Joined">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"/>
                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                                </svg>
                              </span> 
                            </a>     
                          </div>
                         

                          </li>' !!}
                  @endforeach
                @endif 
            </ul>
            @endif 
          </div>
        </div>
        <div class="row w-100" style="">
          <div data-id="Rejected" data-order="7" class="col-md-4 kanban-board px-0">
            <header class="kanban-board-header rejectedset d-flex align-items-center justify-content-between">
              <div class="kanban-title-board text-white">Rejected</div>
              <div class="plus-button"> @if(in_array("add_settings", $userPermissions)) <a class="btn btn-sm btn btn-sm rejectedset-light interview_stage" data-class="rejected-light" data-title="Rejected" data-id="7" data-root="root_rejected"  data-toggle="modal" data-target="#add_interview_sub_stage">
                <!--begin::Svg Icon-->
                <span class="svg-icon svg-icon svg-icon me-0">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                    <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"></rect>
                  </g>
                </svg>
                </span>
                <!--end::Svg Icon-->
                Add </a> @endif </div>
            </header>
            @if(in_array("view_settings", $userPermissions))
            <ul class="kanban-drag bg-light" id="root_rejected" style="position: relative;">
              @if(!empty($interviewStage_reject))
                @foreach($interviewStage_reject as $val)
                @php
                $delete_hide = '';
                 if($val->delete_status==0){
                  $delete_hide = 'd-none';
                 }
                @endphp
                  
          
                  {!! '<li id ="'.@$val->id.'" class="kanban-item d-flex align-items-center justify-content-between rejectedset-light" data-class="">
                        <span class="fw-bold">'.$val->name.'</span>
                       
                        <div class="edit_delete cursor-pointer '.$delete_hide.'">						
                          <a class=" stage_block text-danger" href="javascript:void(0);" rel="'.@$val->id.'">
                            <span class="svg-icon svg-icon-danger svg-icon-2x svg-icon-danger-Negotiation">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                              </g>
                              </svg>
                            </span>
                          </a>             						
                        </div>
                        
                        </li>' !!}
                @endforeach
              @endif
            </ul>
            @endif 
          </div>
        </div>


      </div>
    </div>
    <!--begin::Pipeline footer-->
    <div class="ips-foot">
      <div class="ips-foot-item">
        <span class="ips-foot-ic cad-bl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
        <div><div class="ips-foot-t">Manage Sub-Stages</div><div class="ips-foot-s">Add or remove sub-stages under each stage to match your hiring process.</div></div>
      </div>
      <div class="ips-foot-item">
        <span class="ips-foot-ic cad-pu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12.5" r="2.5"/><path d="M12 2a10 10 0 0 0 0 20 2.5 2.5 0 0 0 2.5-2.5c0-.7-.3-1.3-.7-1.7-.4-.4-.7-1-.7-1.7A2.5 2.5 0 0 1 15.6 14H17a5 5 0 0 0 5-5c0-3.9-4.5-7-10-7z"/></svg></span>
        <div><div class="ips-foot-t">Color-Coded Stages</div><div class="ips-foot-s">Each stage is color-coded to stay consistent with candidate pipeline views.</div></div>
      </div>
      <div class="ips-foot-item">
        <span class="ips-foot-ic cad-am"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
        <div><div class="ips-foot-t">System Stage</div><div class="ips-foot-s">Some stages are system defined and cannot be edited or deleted.</div></div>
      </div>
    </div>
    <!--end::Pipeline footer-->
  </div>
  <!--end::Interview stage Content-->
</div>
<!--end::Interview Stage Content-->

<!--begin::Terms & conditions Content-->
<div class="tab-pane atc" id="terms" role="tabpanel">
  @php
    $atcCustom  = !empty(trim(strip_tags((string)@$account->terms)));
    $atcUpdated = @$account->updated_at;
    $atcVars = [
      '{TODAY_DATE}'       => "Today's Date",
      '{AGENCY_NAME}'      => 'Agency Name',
      '{AGENCY_ADDRESS}'   => 'Agency Address',
      '{COMPANY_NAME}'     => 'Company Name',
      '{COMPANY_ADDRESS}'  => 'Company Address',
    ];
  @endphp
  <!--begin::Header-->
  <div class="atc-head">
    <div>
      <div class="atc-title">Agency Terms &amp; Conditions Template</div>
      <div class="atc-sub">Create and manage the default Terms &amp; Conditions shared with agencies when jobs are assigned. <b>Agencies must accept these terms</b> before working on any position.</div>
    </div>
    <div class="atc-head-actions">
      <a href="javascript:;" id="editBtn" class="atc-btn">Edit</a>
      <a href="javascript:;" id="updateCompanyTermsTop" class="atc-btn atc-btn-primary updateCompanyTermsTop"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg> Save Template</a>
    </div>
  </div>
  <!--end::Header-->
  <!--begin::Meta-->
  <div class="atc-card atc-meta">
    <div class="atc-meta-id">
      <span class="atc-meta-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg></span>
      <div><div class="atc-meta-lbl">Template Name</div><div class="atc-meta-v">Agency Terms &amp; Conditions</div></div>
    </div>
    <div><div class="atc-meta-lbl">Source</div><div class="atc-meta-v">{{ $atcCustom ? 'Custom' : 'Default' }}</div></div>
    @if($atcUpdated)<div><div class="atc-meta-lbl">Last Updated</div><div class="atc-meta-v">{{ date('d M Y, h:i A', strtotime($atcUpdated)) }}</div></div>@endif
    <div><div class="atc-meta-lbl">Status</div><div class="atc-meta-v"><span class="atc-status"><span class="dot"></span> Active</span></div></div>
    <a href="javascript:;" id="setToDefaultCompanyTermsTop" class="atc-btn atc-btn-danger setToDefaultCompanyTermsTop"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 2v6h6"/><path d="M3.5 8a9 9 0 1 0 2.1-3.4L3 8"/></svg> Restore Default</a>
  </div>
  <!--end::Meta-->
  <!--begin::Variables-->
  <div class="atc-card atc-sec">
    <div class="atc-sec-h"><div><div class="atc-sec-t">Variables Used in this Template</div><div class="atc-sec-s">These variables are replaced with actual values automatically.</div></div></div>
    <div class="atc-vars">
      @foreach($atcVars as $code => $desc)
        <div class="atc-var"><code>{{ $code }}</code><div class="atc-var-s">{{ $desc }}</div></div>
      @endforeach
    </div>
  </div>
  <!--end::Variables-->
  <!--begin::Acceptance Notice-->
  <div class="atc-notice">
    <span class="atc-notice-ic">i</span>
    <div>
      <div class="atc-notice-t">Agency Acceptance Notice</div>
      <div class="atc-notice-s">This Terms &amp; Conditions template is automatically shared with agencies whenever jobs are assigned to them. Agencies must review and accept the agreement before they can submit candidates for any position.</div>
    </div>
  </div>
  <!--end::Acceptance Notice-->
  <!--begin::Template Content-->
  <div class="atc-card atc-sec">
    <div class="atc-sec-h"><div><div class="atc-sec-t">Template Content</div><div class="atc-sec-s">Edit the terms and conditions you want to share with agencies.</div></div></div>
    <form id="terms_content" name="terms_content" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" method="post">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <input type="hidden" name="pageterms" id="pageterms" value="terms" />
      @csrf
      @method('POST')
      <div class="atc-editor view-terms" id="viewTerms"> {!! (@$account->terms) ? @$account->terms :  $global_settings->terms !!} </div>
      <div class="edit-terms" id="editTerms" style="display:none;">
        <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target">{!! (@$account->terms) ? @$account->terms :  $global_settings->terms !!}</textarea>
        <div class="card-footer d-flex justify-content-center py-6 px-9 bg-white d-none">
          <button class="btn btn-primary me-2 updateCompanyTerms" type="button" id="updateCompanyTerms">Save Changes</button>
          <button class="btn btn-primary me-2 setToDefaultCompanyTerms" type="button" id="setToDefaultCompanyTerms">Set to Default</button>
        </div>
      </div>
    </form>
  </div>
  <!--end::Template Content-->
</div>
<!--end::Terms & Canditions Content-->

<!--begin::offerterms Terms & conditions Content-->
<div class="tab-pane" id="offerterms" role="tabpanel">
  <!--begin::Overview header-->
  <div class="card-header cursor-pointer px-0">
    <!--begin::Card title-->
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Terms & Conditions</h3>
    </div>
    <!--end::Card title-->
    <!--begin::Action-->
    <div class="card-title m-0">
    <a href="javascript:;" class="btn btn-success me-1 align-self-center updateCompanyTermsofferTop" id="updateCompanyTermsofferTop">Save Changes</a>
    <a href="javascript:;" class="btn btn-warning me-1 align-self-center setToDefaultCompanyTermsofferTop" id="setToDefaultCompanyTermsofferTop">Set to Default</a>
    <a href="javascript:;" class="btn btn-primary align-self-center" id="editBtnoffer">Edit</a>
    </div>
    <!--ensd::Action-->
  </div>
  <!--end::Terms & Canditions header-->
  <div class="card-body px-0 py-7"> 
    <!--begin::Terms & Canditions Content-->
    <div class="row">
      <div class="col-lg-12 mb-7 view-terms" id="viewTermsoffer"> {!! (@$account->offer_terms) ? @$account->offer_terms :  $global_settings->offer_terms !!}</div>
      <form  id="terms_contentoffer" name="terms_contentoffer" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" method="post">
          <meta name="csrf-token" content="{{ csrf_token() }}" />
          <input type="hidden" name="pageterms" id="pagetermsoffer" value="termsoffer" />
          @csrf  
          @method('POST')
        <!--begin::Col-Desc-->
       


        <div class="col-lg-12 edit-terms" id="editTermsoffer" style="display:none;">
        
            <textarea id="kt_docs_tinymce_hiddenoffer" name="kt_docs_tinymce_hiddenoffer" class="tox-target">{!! (@$account->offer_terms) ? @$account->offer_terms :  $global_settings->offer_terms !!}</textarea>
          
        
          <div class="card-footer d-flex justify-content-center py-6 px-9 bg-white d-none">
          
            <button class="btn btn-primary me-2 updateCompanyTermsoffer" type="button" id="updateCompanyTermsoffer">Save Changes</button>
            <button class="btn btn-primary me-2 setToDefaultCompanyTermsoffer" type="button" id="setToDefaultCompanyTermsoffer">Set to Default</button>
          </div>
        </div>
  
      </form>

    </div>

  </div>
</div>
<!--end::offerterms Terms & Canditions Content-->


<!--begin::Offer components Content-->
<div class="tab-pane" id="offercomponent" role="tabpanel">
  <!--begin::Overview header-->
  <div class="card-header cursor-pointer px-0">
    <!--begin::Card title-->
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Salary Structure</h3>
    </div>
    <!--end::Card title-->
    <!--begin::Action-->
    <a href="{{ route('service_company.account.offerAmountChange',[encrypt(0)]) }}" class="btn btn-primary align-self-center" id="">Edit</a>
    <!--ensd::Action-->
  </div>
  <!--end::Terms & Canditions header-->
  <div class="card-body px-0 py-7"> 
    <div class="row"  id="editoffercomponent">
        <div class="col-lg-12 mb-7 view-terms"> </div>
    </div>
    <div class="row"  id="viewoffercomponent">
      <div class="row">
        <table class="table-row-dashed fs-6 gy-5 no-footer" id="pointList"
            role="grid" aria-describedby="myTable_info"
            style="width: 1270px;">
            <thead>
                <tr class="fw-bolder text-muted other_section {{ $section_other }}"
                    role="row">
                    <th class="sorting_disabled" style="width: 25%!important">Components
                    </th>
                    <th class="sorting_disabled"
                        style="width:10% !important">View</th>
                    <th class="sorting_disabled"
                        style="width: 7% !important;">Percentage</th>
                    <th class="sorting_disabled"
                        style="width: 3% !important;">&nbsp;</th>
                    <th class="sorting_disabled"
                        style="width: 10% !important;">Condition</th>
                    <th class="sorting_disabled change_text_first"
                        style="width: 17% !important;">{{ $textShow }}</th>
                    <th class="sorting_disabled"
                        style="width:15% !important">Annually</th>
                </tr>
                <tr class="fw-bolder text-muted hourly_section {{ $section_hourly }}"
                    role="row">
                    <th class="sorting_disabled"
                        style="width: 47% !important;">Components</th>
                    <th class="sorting_disabled"
                        style="width: 12% !important;">Percentage</th>
                    <th class="sorting_disabled"
                        style="width: 3% !important;">&nbsp;</th>
                    <th class="sorting_disabled"
                        style="width: 10% !important;">Condition</th>
                    <th class="sorting_disabled change_text"
                        style="width:15% !important">{{ $textShow }}</th>
                </tr>
            </thead>
            <tbody>


                @if (isset($OfferJobApplication) && count($OfferJobApplication) > 0)
                    @foreach ($OfferJobApplication as $key=> $offer)
                        @foreach ($offer['Basic_offer_points'] as $key => $basic)
                            <tr class="other_section other_section_row row-template {{ $key > 0 ? 'remove_row' : '' }} {{ $section_other }}"
                                role="row">
                                <td class="">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_points"
                                        name="offer_point[]"
                                        id="offer_point_{{ $basic->id }}"
                                        data-id="{{ $basic->id }}"
                                        data-offerid="{{ $basic->id }}"
                                        value="{{ $basic->basic_poins }}"
                                        placeholder="Enter Point">
                                </td>
                                <td class="">
                                    <select &nbsp="" class="form-select form-select-solid basic_view" arial-label="Select  View" name="basic_view[]" id="basic_view_{{ $basic->id }}" data-id="{{ $basic->id }}"
                                    data-offerid="{{ $basic->id }}"
                                    style="width: 100px;display: initial; ">
                                    <option value="0" {{ $basic->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                    <option value="1" {{ $basic->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                    <option value="2" {{ $basic->view_type == 2 ? 'selected' : '' }}> Both</option>
                                    </select>
                                </td>

                                <td class="">

                                    <input &nbsp="" type="text"
                                        class="form-control form-select-solid option_percentage"
                                        name="option_percentage[]"
                                        id="option_percentage_{{ $basic->id }}"
                                        data-id="{{ $basic->id }}"
                                        data-offerid="{{ $basic->id }}"
                                        value="{{ $basic->basic_percentage }}"
                                        placeholder="%">


                                </td>
                                <td class="" style="position: relative;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> </td>
                                <td class="">
                                    <select &nbsp=""
                                        class="form-select form-select-solid option_salarymode"
                                        arial-label="Select Annual Salary"
                                        name="option_salarymode[]"
                                        id="option_salarymode_{{ $basic->id }}"
                                        data-id="{{ $basic->id }}"
                                        data-offerid="{{ $basic->id }}"
                                        style="width: 100px;display: initial; ">
                                        <option value="0"
                                            {{ $basic->basic_condition_s == 0 ? 'selected' : '' }}>
                                            Amount </option>
                                            @if($key>0)
                                            <option value="1"
                                            {{ $basic->basic_condition_s == 1 ? 'selected' : '' }}>
                                            Basic</option>
                                            @endif
                                    </select>
                                </td>
                                <td class="">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_monthly"
                                        name="offer_monthly[]"
                                        id="offer_monthly_{{ $basic->id }}"
                                        data-id="{{ $basic->id }}"
                                        data-offerid="{{ $basic->id }}"
                                        value="{{ $basic->basic_monthly }}"
                                        placeholder="0.00">
                                </td>
                                <td class="">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_yearly {{ $key > 0 ? 'other_salary_get' : 'basic_salary_get' }}"
                                        name="offer_yearly[]"
                                        id="offer_yearly_{{ $basic->id }}"
                                        data-id="{{ $basic->id }}"
                                        data-offerid="{{ $basic->id }}"
                                        value="{{ $basic->basic_yearly }}"
                                        placeholder="0.00">
                                </td>
                                <td style="float:right">
                                    <button type="button"
                                        class="btn btn-light-danger font-weight-bolder remove_points"
                                        {{ $key > 0 ? '' : 'disabled' }}>
                                        <span
                                            class="svg-icon svg-icon svg-icon-2x">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                width="24px"
                                                height="24px"
                                                viewBox="0 0 24 24"
                                                version="1.1">
                                                <g stroke="none"
                                                    stroke-width="1"
                                                    fill="none"
                                                    fill-rule="evenodd">
                                                    <rect fill="#000000"
                                                        x="4" y="11"
                                                        width="16"
                                                        height="2"
                                                        rx="1">
                                                    </rect>
                                                </g>
                                            </svg>
                                        </span> Delete </button>
                                </td>
                            </tr>

                            <tr class="hourly_section hourly_section_row row-template {{ $key > 0 ? 'remove_row' : '' }} {{ $section_hourly }}"
                                role="row">
                                <td class="">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_point_hourly"
                                        name="offer_point_hourly[]"
                                        id="offer_point_{{ $basic->id }}"
                                        data-id="{{ $basic->id }}"
                                        data-offerid="{{ $basic->id }}"
                                        value="{{ $basic->basic_poins }}"
                                        placeholder="Enter Point">
                                </td>
                                <td class="">
                                    <input &nbsp="" type="text"
                                        class="form-control form-select-solid option_percentage_hourly"
                                        name="option_percentage_hourly[]"
                                        id="option_percentage_hourly{{ $basic->id }}"
                                        data-id="{{ $basic->id }}"
                                        data-offerid="{{ $basic->id }}"
                                        value="{{ $basic->basic_percentage }}"
                                        placeholder="%">

                                   
                                </td>
                                <td class="">
                                    <select &nbsp=""
                                        class="form-select form-select-solid option_salarymode_hourly"
                                        arial-label="Select Annual Salary"
                                        name="option_salarymode_hourly[]"
                                        id="option_salarymode_hourly{{ $basic->id }}"
                                        data-id="{{ $basic->id }}"
                                        data-offerid="{{ $basic->id }}"
                                        style="width: 100px;display: initial; ">
                                        <option value="0"
                                            {{ $basic->basic_condition_s == 0 ? 'selected' : '' }}>
                                            Amount </option>
                                        <option value="1"
                                            {{ $basic->basic_condition_s == 1 ? 'selected' : '' }}>
                                            Basic</option>
                                    </select>
                                </td>
                                <td class="hourly_section">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_hourly {{ $key > 0 ? 'other_salary_get_hourly' : 'basic_salary_get_hourly' }}"
                                        name="offer_hourly[]"
                                        id="offer_hourly_{{ $basic->id }}"
                                        data-id="{{ $basic->id }}"
                                        data-offerid="{{ $basic->id }}"
                                        value="{{ $basic->basic_yearly }}"
                                        placeholder="0.00">
                                </td>
                                
                            </tr>
                        @endforeach
                    @endforeach
                @else
                    @if ($offer_points)
                        @foreach ($offer_points as $key => $value)
                            <tr class="other_section other_section_row row-template {{ $key > 1 ? 'remove_row' : '' }} {{ $section_other }}"
                                role="row">
                                <td class="" style="width: 25%!important">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_points"
                                        name="offer_point[]"
                                        id="offer_point_{{ $value->id }}"
                                        data-id="{{ $value->id }}"
                                        data-offerid="{{ $value->id }}"
                                        value="{{ $value->points }}"
                                        placeholder="Enter Point">
                                </td>
                                <td class="" style="width:10% !important">
                                    <select &nbsp="" class="form-select form-select-solid basic_view" arial-label="Select  View" name="basic_view[]" id="basic_view_{{ $value->id }}" data-id="{{ $value->id }}"
                                    data-offerid="{{ $value->id }}"
                                    style="width: 100%;display: initial; ">
                                    <option value="0" {{ $value->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                    <option value="1" {{ $value->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                    <option value="2" {{ $value->view_type == 2 ? 'selected' : '' }}> Both</option>
                                    </select>
                                </td>

                                <td class="" style="width:7% !important">

                                    <input &nbsp="" type="text"
                                        class="form-control form-select-solid option_percentage"
                                        name="option_percentage[]"
                                        id="option_percentage_{{ $value->id }}"
                                        data-id="{{ $value->id }}"
                                        data-offerid="{{ $value->id }}"
                                        value="{{ $value->percentage }}"
                                        placeholder="%">
                                </td>
                                <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> </td>
                                <td class="" style="width:10% !important">
                                    <select &nbsp=""
                                        class="form-select form-select-solid option_salarymode"
                                        arial-label="Select Annual Salary"
                                        name="option_salarymode[]"
                                        id="option_salarymode_{{ $value->id }}"
                                        data-id="{{ $value->id }}"
                                        data-offerid="{{ $value->id }}"
                                        style="width: 100%;display: initial; ">
                                        <option value="0"
                                            selected="selected">Amount
                                        </option>
                                         @if($key>0)
                                           <option value="1">Basic</option>
                                         @endif
                                    </select>
                                </td>
                                <td class="" style="width:17% !important">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_monthly"
                                        name="offer_monthly[]"
                                        id="offer_monthly_{{ $value->id }}"
                                        data-id="{{ $value->id }}"
                                        data-offerid="{{ $value->id }}"
                                        value="" placeholder="0.00">
                                </td>
                                <td class="" style="width:15% !important">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_yearly {{ $key > 1 ? 'other_salary_get' : 'basic_salary_get' }}"
                                        name="offer_yearly[]"
                                        id="offer_yearly_{{ $value->id }}"
                                        data-id="{{ $value->id }}"
                                        data-offerid="{{ $value->id }}"
                                        value="" placeholder="0.00">
                                </td>
                               
                                
                            </tr>
                         @endforeach
                    @endif
                    @if ($offer_point_hourly)
                        @foreach ($offer_point_hourly as $key=> $value)
                            <tr class="hourly_section hourly_section_row row-template {{ $key > 1 ? 'remove_row' : '' }} {{ $section_hourly }}"
                                role="row">
                                <td class=""
                                    style="width: 47% !important;">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_point_hourly"
                                        name="offer_point_hourly[]"
                                        id="offer_point_hourly{{ $value->id }}"
                                        data-id="{{ $value->id }}"
                                        data-offerid="{{ $value->id }}"
                                        value="{{ $value->points }}"
                                        placeholder="Enter Point">
                                </td>
                                <td class=""
                                    style="width: 12% !important;">

                                    <input &nbsp="" type="text"
                                        class="form-control form-select-solid option_percentage_hourly"
                                        name="option_percentage_hourly[]"
                                        id="option_percentage_hourly{{ $value->id }}"
                                        data-id="{{ $value->id }}"
                                        data-offerid="{{ $value->id }}"
                                        value="{{ $value->percentage }}"
                                        placeholder="%">
                                </td>
                                <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                </td>
                                <td class=""
                                    style="width: 10% !important;">
                                    <select &nbsp=""
                                        class="form-select form-select-solid option_salarymode_hourly"
                                        arial-label="Select Annual Salary"
                                        name="option_salarymode_hourly[]"
                                        id="option_salarymode_hourly{{ $value->id }}"
                                        data-id="{{ $value->id }}"
                                        data-offerid="{{ $value->id }}"
                                        style="width: 100%;display: initial; ">
                                        <option value="0"
                                            selected="selected">Amount
                                        </option>
                                        @if($key>0)
                                        <option value="1">Basic
                                        </option>
                                        @endif
                                    </select>
                                </td>
                                <td class=""
                                    style="width: 15% !important;">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid offer_hourly {{ $key > 1 ? 'other_salary_get_hourly' : 'basic_salary_get_hourly' }}"
                                        name="offer_hourly[]"
                                        id="offer_hourly{{ $value->id }}"
                                        data-id="{{ $value->id }}"
                                        data-offerid="{{ $value->id }}"
                                        value="" placeholder="0.00">
                                </td>
                               
                                
                            </tr>
                        @endforeach
                    @endif

                @endif


            </tbody>



        </table>
    </div>

    <div class="row">
        <table class="table-row-dashed fs-6 gy-5 no-footer" id="bonusList"
            role="grid" aria-describedby="bonusList_info"
            style="width: 1270px;">
            <thead>
                <tr class="other_section other_section_row row-template {{ $section_other }}">
                    <th colspan="8" class="sorting_disabled"
                        style="width: 102px;">Other Component</th>
                  
                </tr>
                <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}">
                    <th colspan="7" class="sorting_disabled other_text"
                        style="width: 102px;">Other Component</th>
                </tr>
            </thead>


            <tbody>
                @if(isset($offer_point_other) && count($offer_point_other) > 0)
                        @foreach ($offer_point_other as $offer)
                            <tr class="other_section other_section_row row-template remove_row {{ $section_other }}"
                                role="row">
                                <td class="" style="width: 25% !important;">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid bonus_points"
                                        name="bonus_points[]"
                                        id="bonus_points_{{ $offer->id }}"
                                        data-id="{{ $offer->id }}"
                                        data-offerid="{{ $offer->id }}"
                                        value="{{ $offer->points }}"
                                        placeholder="Enter Point">
                                </td>
                                <td class="" style="width: 10% !important;">
                                    <select &nbsp="" class="form-select form-select-solid bonus_view" arial-label="Select  View" name="bonus_view[]" id="bonus_view_{{ $offer->id }}" data-id="{{ $offer->id }}"
                                    data-offerid="{{ $offer->id }}"
                                    style="width: 100%;display: initial; ">
                                    <option value="0" {{ @$offer->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                    <option value="1" {{ @$offer->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                    <option value="2" {{ @$offer->view_type == 2 ? 'selected' : '' }}> Both</option>
                                    </select>
                                </td>


                                <td class="" style="width: 7% !important;">
                                    <input &nbsp="" type="text"
                                        class="form-control form-select-solid bonus_option_percentage"
                                        name="bonus_option_percentage[]"
                                        id="bonus_option_percentage{{ $offer->id }}"
                                        data-id="{{ $offer->id }}"
                                        data-offerid="{{ $offer->id }}"
                                        value="{{ $offer->percentage }}"
                                        placeholder="%">

                                </td>
                                <td class="" style="position: relative; width: 3% !important;">
                                     <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                </td>
                                <td class="" style="width: 10% !important;">
                                    <select &nbsp=""
                                        class="form-select form-select-solid bonus_option_salarymode"
                                        arial-label="Select Annual Salary"
                                        name="bonus_option_salarymode[]"
                                        id="bonus_option_salarymode_{{ $offer->id }}"
                                        data-id="{{ $offer->id }}"
                                        data-offerid="{{ $offer->id }}"
                                        style="width: 100%;display: initial; ">
                                        <option value="0"
                                            {{ $offer->condition_check == 0 ? 'selected' : '' }}>
                                            Amount </option>
                                        <option value="1"
                                            {{ $offer->condition_check == 1 ? 'selected' : '' }}>
                                            Basic</option>
                                    </select>
                                </td>
                                <td class="" style="width: 17% !important;">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid bonus_offer_monthly"
                                        name="bonus_offer_monthly[]"
                                        id="bonus_offer_monthly_{{ $offer->id }}"
                                        data-id="{{ $offer->id }}"
                                        data-offerid="{{ $offer->id }}"
                                        value=""
                                        placeholder="0.00">
                                </td>
                                <td class="" style="width: 15% !important;">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid bonus_offer_yearly"
                                        name="bonus_offer_yearly[]"
                                        id="bonus_offer_yearly{{ $offer->id }}"
                                        data-id="{{ $offer->id }}"
                                        data-offerid="{{ $offer->id }}"
                                        value=""
                                        placeholder="0.00">
                                </td>
                               
                            </tr>
                        @endforeach
                @endif
                @if(isset($offer_point_other_hourly) && count($offer_point_other_hourly) > 0)
                        @foreach ($offer_point_other_hourly as $offer)
                            <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}"
                                role="row">
                                <td class=""
                                    style="width: 47% !important;">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid bonus_point_hourly"
                                        name="bonus_point_hourly[]"
                                        id="bonus_points_hourly{{ $offer->id }}"
                                        data-id="{{ $offer->id }}"
                                        data-offerid="{{ $offer->id }}"
                                        value="{{ $offer->points }}"
                                        placeholder="Enter Point">
                                </td>
                                <td class=""
                                    style="width: 12% !important;">
                                    <input &nbsp="" type="text"
                                        class="form-control form-select-solid bonus_option_percentage_hourly"
                                        name="bonus_option_percentage_hourly[]"
                                        id="bonus_option_percentage_hourly{{ $offer->id }}"
                                        data-id="{{ $offer->id }}"
                                        data-offerid="{{ $offer->id }}"
                                        value="{{ $offer->percentage }}"
                                        placeholder="%">
                                </td>
                                <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                </td>
                                <td class=""
                                    style="width: 10% !important;">
                                    <select &nbsp=""
                                        class="form-select form-select-solid bonus_option_salarymode_hourly"
                                        arial-label="Select Annual Salary"
                                        name="bonus_option_salarymode_hourly[]"
                                        id="bonus_option_salarymode_hourly{{ $offer->id }}"
                                        data-id="{{ $offer->id }}"
                                        data-offerid="{{ $offer->id }}"
                                        style="width: 100px;display: initial; ">
                                        <option value="0"
                                            {{ $offer->condition_check == 0 ? 'selected' : '' }}>
                                            CTC </option>
                                        <option value="1"
                                            {{ $offer->condition_check == 1 ? 'selected' : '' }}>
                                            Basic</option>
                                    </select>
                                </td>
                                <td class=""
                                    style="width: 15% !important;">
                                    <input &nbsp="" type="text"
                                        class="form-control form-control-solid bonus_offer_hourly"
                                        name="bonus_offer_hourly[]"
                                        id="bonus_offer_hourly{{ $offer->id }}"
                                        data-id="{{ $offer->id }}"
                                        data-offerid="{{ $offer->id }}"
                                        value=""
                                        placeholder="0.00">
                                </td>
                                
                            </tr>
                        @endforeach
                @endif

            </tbody>
           
        </table>
    </div>

       </div>

  </div>
</div>
<!--end::Offer components-->




<!--begin::Services Content-->
<div class="tab-pane" id="services" role="tabpanel">
  <!--begin::Overview header-->
  <div class="card-header cursor-pointer px-0">
    <!--begin::Card title-->
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Services</h3>
    </div>
    <!--end::Card title-->
  </div>
  <!--end::Services header-->
  <div class="card-body px-0 py-7"> 
    <!--begin::Services Content-->
    <div class="row">
      <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
          <!--begin::Container-->
          <div id="kt_content_container" class="container-fluid container-fluid pl-1 pe-2">
            <!--begin::Row-->
            <div class="row">

              <div class="col-md-6">
                <div class="card card-flush h-md-100">
                  <div class="card-header">
                    <div class="card-title">
                      <h1>Fulltime Hiring Services</h1>

                      <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" style="position: absolute; right: 5px;">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input update_main_status_fulltime" data-row_id="{{ encrypt((@$fullTimeStatus->id))}}" type="checkbox" value="{{ @$fullTimeStatus->status }}" {{ (@$fullTimeStatus->status) ? 'checked' : '' }} />
                            
                        </label>
                        <span id="f_text" style="margin-left: 8px;">{{ (@$fullTimeStatus->status) ? 'ON' : 'OFF' }} </span>
                    </a>

                    </div>
                  </div>

                  <div class="card-body pt-1">
                    {{-- <div class="fw-bolder text-gray-600 mb-5 fs-6">Total users with this role: </div> --}}
                    <div class="d-flex flex-column text-gray-600">

                      <div class="d-flex align-items-center py-2 fs-6 class_change_fulltime" style="{{ @$fullTimeStatus->status ? '' : 'opacity: 0.2 !important' }}">
                         <span class="bullet bg-primary me-3"></span>
                         <em>One Time Payment for Service</em>
                      </div>
                      <div class="d-flex align-items-center py-2 fs-6 class_change_fulltime" style="{{ @$fullTimeStatus->status ? '' : 'opacity: 0.2 !important' }}">
                        <span class="bullet bg-primary me-3"></span>
                        <em>Commercials Added</em>
                      </div>
                      <div class="d-flex align-items-center py-2 fs-6 class_change_fulltime" style="{{ @$fullTimeStatus->status ? '' : 'opacity: 0.2 !important' }}">
                        <span class="bullet bg-primary me-3"></span>
                        <em><span id="fullTimeCount">{{ @$fullTimeCount }} </span> Countries Selected for Services</em>
                      </div>

                    </div>
                  </div>
                  <div class="card-footer flex-wrap pt-0"> 
                       <a href="javascript:;" class="btn btn-light btn-active-primary my-1 me-2 ser_show-detail" data-type="2">View/Edit</a>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="card card-flush h-md-100">
                  <div class="card-header">
                    <div class="card-title">
                      <h1>Contract Hiring Services</h1>

                      <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" style="position: absolute; right: 5px;">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input contract_main_status_fulltime" data-row_id="{{ encrypt((@$contractStatus->id))}}" type="checkbox" value="{{ @$contractStatus->status }}" {{ (@$contractStatus->status) ? 'checked' : '' }} />
                        </label>
                        <span id="c_text" style="margin-left: 8px;">{{ (@$contractStatus->status) ? 'ON' : 'OFF' }} </span>
                    </a>

                    </div>
                  </div>

                  <div class="card-body pt-1">
                    {{-- <div class="fw-bolder text-gray-600 mb-5 fs-6">Total users with this role: </div> --}}
                    <div class="d-flex flex-column text-gray-600">

                      <div class="d-flex align-items-center py-2 fs-6 class_change_contract" style="{{ @$contractStatus->status ? '' : 'opacity: 0.2 !important' }}">
                        <span class="bullet bg-primary me-3"></span>
                        <em>Houry / Monthly Payment for Services</em>
                     </div>
                     <div class="d-flex align-items-center py-2 fs-6 class_change_contract" style="{{ @$contractStatus->status ? '' : 'opacity: 0.2 !important' }}">
                       <span class="bullet bg-primary me-3"></span>
                       <em>Commercials Added</em>
                     </div>
                     <div class="d-flex align-items-center py-2 fs-6 class_change_contract" style="{{ @$contractStatus->status ? '' : 'opacity: 0.2 !important' }}">
                       <span class="bullet bg-primary me-3"></span>
                       <em><span id="contractCount">{{ @$contractCount }} </span> Countries Selected for Services</em>
                     </div>

                    </div>
                  </div>
                  <div class="card-footer flex-wrap pt-0"> 
                       <a href="javascript:;" class="btn btn-light btn-active-primary my-1 me-2 ser_show-detail" data-type="1">View/Edit</a>
                  </div>
                </div>
              </div>


            </div>
          </div>
        </div>
      </div>

      
    </div>
    
  </div>
</div>
<!--end::Services Content-->


        <!--begin::boost keyword Content-->
        <div class="tab-pane {{$boostkeyword_tab}}" id="boostkeyword" role="tabpanel">

           <!--begin::Overview header-->
                <div class="card-header cursor-pointer px-0">
                   <!--begin::Card title-->
                        <div class="card-title m-0">
                          <h3 class="fw-bolder m-0">Ads Settings</h3>
                        </div>
                      <!--end::Card title-->
                      <!--begin::Action-->
                        <a href="javascript:;" class="btn btn-primary align-self-center" id="editBtnCampaign">Create Campaign</a>
                    <!--ensd::Action-->
                </div>
            <!--end::Terms & Canditions header-->

            <table
                                                    class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class dataTable no-footer"
                                                    id="myTable" role="grid" aria-describedby="myTable_info"
                                                    style="width: 1270px;">
                                                    <thead>
                                                        <tr class="fw-bolder text-muted" role="row">
                                                            
                                                            <th class="sorting_disabled">Sr.No.</th>
                                                            <th class="sorting_disabled">Status</th>
                                                            <th class="sorting_disabled">Campaign</th>
                                                            <th class="sorting_disabled">Keyword</th>
                                                            <th class="sorting_disabled">Country</th>
                                                            <th class="sorting_disabled">Type</th>
                                                            <th class="sorting_disabled">Click</th>
                                                            <th class="sorting_disabled">Cost per click</th>
                                                            <th class="sorting_disabled">Sales Coins</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 fw-bold">

                                                      @if($boostkeywords)
                                                        @foreach ($boostkeywords as $key=> $boostkeyword)
                                                        @php
                                                              $salse_coins = DB::table('company_coins')->where('company_id','=',$boostkeyword->company_id)->where('event_type','=','sales')->first();

                                                              $chk = $disabled = '';
                                                              $status = $boostkeyword->status;
                                                              $status_text = 'Inactive';
                                                              $status_text_color = "text-danger";
                                                              if ($status == 1) {
                                                                  $chk = 'checked="checked"';
                                                                  $status_text = 'Active';
                                                                  $status_text_color = "text-success";
                                                              }
                                                              $coin_event_id = '';
                                                              if($boostkeyword->country_id==101 && $boostkeyword->campaign_type==0){
                                                                $coin_event_id= '19';
                                                              }elseif($boostkeyword->country_id==101 && $boostkeyword->campaign_type==1){
                                                                $coin_event_id= '21';
                                                              }elseif($boostkeyword->country_id!=101 && $boostkeyword->campaign_type==1){
                                                                $coin_event_id= '20';
                                                              }elseif($boostkeyword->country_id!=101 && $boostkeyword->campaign_type==1){
                                                                $coin_event_id= '22';
                                                              }

                                                              $deductionCoins = DB::table('coin_order_events')
                                                              ->where('coin_order_id','=',$salse_coins->coin_order_id)
                                                              ->where('coin_event_id','=',$coin_event_id)
                                                              ->where('event_type','=','sales')
                                                              ->first();

                                                        @endphp


                                                        <tr class="fw-bolder text-muted" role="row">
                                                            <td class="sorting_disabled">{{($key+1)}}.</td>
                                                            <td class="sorting_disabled">
                                                              <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                                                <input class="form-check-input chkStatus" type="checkbox" value="active" name="staus" {{@$chk}}  onclick="boostHandleClick(this, {{$boostkeyword->id}})" {{$disabled}} >
                                                              </div>
                                                                                      <span class="fw-bold d-block fs-7 {{$status_text_color }} status_text">{{$status_text}}</span>
                                                            </td>
                                                            <td class="sorting_disabled">{{$boostkeyword->campaign_name}}</td>
                                                            <td class="sorting_disabled">{{$boostkeyword->skill_name}}</td>
                                                            <td class="sorting_disabled">{{$boostkeyword->country_name}}</td>
                                                            <td class="sorting_disabled">{{$boostkeyword->campaign_type==1 ? 'Contract' : 'Full-time'}}</td>
                                                            <td class="sorting_disabled">{{$boostkeyword->clickCount}}</td>
                                                            <td class="sorting_disabled">{{($deductionCoins) ? $deductionCoins->deductions : ''}} Sales Coin</td>
                                                            <td class="sorting_disabled">{{$salse_coins->coins}}</td>
                                                          </tr>
                                                        @endforeach


                                                      @endif

                                                    </tbody>
                                                  </table>


            {{-- <form id="boost_keyword_form" class="form" action="#">
                    <div class="card-body px-0 py-7">
                            <div class="col-lg-12 mt-7">
                                <input type="hidden" name="keyword_compant_id" id="keyword_compant_id" value="{{ @$account->id }}" />
                                <label class="form-label required fw-bolder">Enter Keywords:</label>
                                <input class="form-control form-control-solid keyword_compant" name="tags" value="{{ $boostkeywords }}" placeholder="Enter Keywords" value="" id="tags"/>
                                <span class="form-text text-muted">Please select your keywords</span> 
                            </div>
                                <!--end::Tags-->
                    </div>

                    <div class="text-center pt-15">
                        <button type="submit" id="operational_top_up_new_submit" class="btn btn-primary">
                            <span class="indicator-label indicator-label-top-up">Submit</span>
                            <span class="indicator-progress indicator-progress-top-up">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
            </form> --}}
          </div>

    <!--end::boost keyword Content-->




<!--begin::Settings Content-->
<div class="tab-pane" id="settings" role="tabpanel">
<!--begin::Form-->
<form  id="setting_content" name="setting_content" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" method="post">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <input type="hidden" name="pagename" id="pagename" value="settings" />
  @csrf  
  @method('POST')
  <!--begin::Main body div-->
  <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0"> @if(in_array("view_settings", $userPermissions))
    <div class="card-body px-0 pt-0 pb-0">
      <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
        <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#setting_one" aria-expanded="true" aria-controls="kt_account_profile_details">
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Settings</h3>
          </div>
        </div>
        <div id="setting_one" class="collapse show">
          <div class="card-body border-top py-8 px-2">
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label required fw-bold fs-6">Language</label>
              <div class="col-lg-9 fv-row">
                <select name="language" id="language" aria-label="Select a Language" data-placeholder="Select a language..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value=""  >Select a Language...</option>
                    @if(!empty($languagesArr))
                    @foreach($languagesArr as $lval)
                        @php $sel	=	''; 
                        if($account->language == $lval->id ){
                            $sel	=	'selected="selected"'; 
                          }elseif($lval->id == 1){
                             $sel	=	'selected="selected"';
                          }
                        @endphp 
                        
                        {!! '<option  value="'.$lval->id.'" '.@$sel.'>'.$lval->name.'</option>' !!}
                    @endforeach
                    @endif 
                </select>
                <div class="form-text">Please select a preferred language, including date, time, and number formatting.</div>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label required fw-bold fs-6">Time Zone</label>
              <div class="col-lg-9 fv-row">
                <select name="timezone" id="timezone" aria-label="Select a Time Zone" data-placeholder="Select a time zone..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value="" >Select a timezone...</option>
                    @if(!empty($timezone))
                     <!-- Popular Time Zones -->
                          <optgroup label="Popular Time Zones">
                            @foreach($timezone as $tval)
                                @if($tval->priority)
                                    @php
                                        $sel = '';
                                        if ($account->timezone == $tval->time_zone) {
                                            $sel = 'selected="selected"';
                                        } elseif ($tval->time_zone == "Asia/Kolkata") {
                                            $sel = 'selected="selected"';
                                        }
                                    @endphp
                                    {!! '<option value="'.$tval->time_zone.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' ('.$tval->time_zone.') - '.$tval->timezone_abbreviation.'</option>' !!}
                                @endif
                            @endforeach
                        </optgroup>

                        <!-- Other Time Zones -->
                        <optgroup label="All Time Zones">
                            @foreach($timezone as $tval)
                                @if(!$tval->priority) {{-- Non-priority time zones --}}
                                    @php
                                        $sel = '';
                                        if ($account->timezone == $tval->time_zone) {
                                            $sel = 'selected="selected"';
                                        } elseif ($tval->time_zone == "Asia/Kolkata") {
                                            $sel = 'selected="selected"';
                                        }
                                    @endphp
                                    {!! '<option value="'.$tval->time_zone.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' ('.$tval->time_zone.') - '.$tval->timezone_abbreviation.'</option>' !!}
                                @endif
                            @endforeach
                        </optgroup>
                    @endif
                </select>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label fw-bold fs-6">Currency</label>
              <div class="col-lg-9 fv-row">
                <select name="currency" id="currency" aria-label="Select a Currency" data-placeholder="Select a currency..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value="" >Select a currency...</option>
                  @if(!empty($currencyArr))
                    @foreach($currencyArr as $cval)
                      @php $sel	=	''; 
                      if($account->currency == $cval->id ){
                          $sel	=	'selected="selected"';
                      }elseif($cval->id==53){
                        $sel	=	'selected="selected"';
                      } 
                     @endphp
                      {!! '<option  value="'.$cval->id.'" '.$sel.'>'.$cval->code.' - '.$cval->country.'</option>' !!}
                    @endforeach
                  @endif
                </select>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>

            
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label fw-bold fs-6">Bench Show Mode</label>
              <div class="col-lg-9 fv-row">
                @php
                    $modeShow = ["Annual","Monthly","Hourly"];
                @endphp
                <select name="bench_show_mode" id="bench_show_mode" aria-label="Select a Mode" data-placeholder="Select a Mode..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value="" >Select a Mode...</option>
                  @if(!empty($modeShow))
                    @foreach($modeShow as $key=> $cval)
                      @php $sel	=	''; 
                      if($account->bench_show_mode == $key ){
                          $sel	=	'selected="selected"';
                      } 
                     @endphp
                      {!! '<option  value="'.$key.'" '.$sel.'>'.$cval.'</option>' !!}
                    @endforeach
                  @endif
                </select>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>


            {{-- <div class="row mb-6">
              <label class="col-lg-3 col-form-label fw-bold fs-6">Countries We are hiring</label>
              <div class="col-lg-9 fv-row">
                <select name="countries[]" id="countries" aria-label="Select a countries" data-placeholder="Select a countries..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true" multiple>
                  <option value="" >Select a countries...</option>
                  @if(!empty($countries))
                    @foreach($countries as  $cval)
                      @php $sel	=	''; 
                      if(in_array($cval->id,$countriesforhirring)){
                          $sel	=	'selected="selected"';
                      } 
                     @endphp
                      {!! '<option  value="'.$cval->id.'" '.$sel.'>'.$cval->country_name.'</option>' !!}
                    @endforeach
                  @endif
                </select>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div> --}}

           
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label fw-bold fs-6">Time Format</label>
              <div class="col-lg-9 fv-row">
                <select name="time_format" id="time_format" aria-label="Select a time format" data-placeholder="Select a time format..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value="" >Select a time format...</option>
                @if(!empty($time_formatArr))
                  @foreach($time_formatArr as $tval)
                    @php $sel	=	'';
                    if($account->time_format == $tval->id ){
                       $sel	=	'selected="selected"'; 
                      }elseif($tval->id==1){
                       $sel	=	'selected="selected"'; 
                      }
                     @endphp 
                    {!! '<option  value="'.$tval->id.'"  '.$sel.'>'.$tval->name.'</option>' !!}
                  @endforeach
                @endif	
                </select>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
           
            <div class="row mb-6 d-none">
              <!--begin::Title-->
              <label class="col-lg-3 col-form-label fw-bold fs-6">Date Format</label>
              <!--end::Title-->
              <!--begin::Description-->
              <div class="col-lg-9 fv-row">
                <!--begin::Input-->
                <select name="date_format" id="date_format" aria-label="Select a date format" data-placeholder="Select a date format..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                  <option value="" >Select a date format...</option>
                    @if(!empty($date_formatArr))
                      @foreach($date_formatArr as $dval)
                        @php $sel	=	''; 
                        if($account->date_format == $dval->id ){
                          $sel	=	'selected="selected"';
                        }elseif($dval->id==1){
                          $sel	=	'selected="selected"';
                        }
                           
                          
                          @endphp 
                          {!! '<option  value="'.$dval->id.'"  '.$sel.'>'.$dval->name.'</option>' !!}
                      }
                      @endforeach
                    @endif
                </select>
                <!--end::Input-->
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
              <!--end::Description-->
            </div>


          </div>
        </div>
      </div>
      <!--end::Main Settings Content-->
      <!--begin::Connected Accounts-->
      <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0 d-none">
        <!--begin::Card header-->
        <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_connected_accounts" aria-expanded="true" aria-controls="kt_account_connected_accounts">
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Connected Accounts</h3>
          </div>
        </div>
        <!--end::Card header-->
        <!--begin::Content-->
        <div id="kt_account_connected_accounts" class="collapse show">
          <!--begin::Card body-->
          <div class="card-body border-top py-8 px-2">
            <!--begin::Items-->
            <div class="py-2">
              <!--begin::Item-->
              <div class="d-flex flex-stack">
                <div class="d-flex"> <img src="{{ asset('img/images/google-icon.svg') }}" class="w-30px me-6" alt="" />
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Google</a>
                    <div class="fs-6 fw-bold text-gray-400">Plan properly your workflow</div>
                  </div>
                </div>
                <div class="d-flex justify-content-end"> <a href="javascript:void(0);" class="google_config" rel="GOOGLE" data-bs-toggle="modal" data-bs-target="#google_config"><i class="fas fa-cogs"></i>&nbsp;Configuration&nbsp;</a>
                  <div class="form-check form-check-solid form-switch">
                    <input class="form-check-input w-45px h-30px" type="checkbox" id="google_status" name="google_status" value="1" @php if($account->google_status == 1){ echo 'checked="checked"'; } @endphp  >
                    <label class="form-check-label" for="googleswitch"></label>
                  </div>
                </div>
              </div>
              <!--end::Item-->
              <div class="separator separator-dashed my-5"></div>
              <!--begin::Item-->
              <div class="d-flex flex-stack">
                <div class="d-flex"> <img src="{{asset('img/images/github.svg')}}" class="w-30px me-6" alt="" />
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Zoom</a>
                    <div class="fs-6 fw-bold text-gray-400">Keep eye on on your Repositories</div>
                  </div>
                </div>
                <div class="d-flex justify-content-end"> <a href="javascript:void(0);" class="zoom_config" rel="ZOOM" data-bs-toggle="modal" data-bs-target="#zoom_config"><i class="fas fa-cogs"></i>&nbsp;Configuration&nbsp;</a>
                  <div class="form-check form-check-solid form-switch">
                    <input class="form-check-input w-45px h-30px" type="checkbox" id="zoom_status" name="zoom_status" value="1" @php if($account->zoom_status == 1){ echo 'checked="checked"'; } @endphp >
                    <label class="form-check-label" for="githubswitch"></label>
                  </div>
                </div>
              </div>
              <!--end::Item-->
              <div class="separator separator-dashed my-5"></div>
              <!--begin::Item-->
              {{-- <div class="d-flex flex-stack">
                <div class="d-flex"> <img src="{{asset('img/images/google-icon.svg')}}" class="w-30px me-6" alt="" />
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Google Sheet ID</a>
                    <div class="fs-6 fw-bold text-gray-400">Keep eye on on your Google Sheet Id</div>
                  </div>
                </div>
                <div class="d-flex justify-content-end"> <a href="javascript:void(0);" class="google_sheet_config" rel="ZOOM" data-bs-toggle="modal" data-bs-target="#google_sheet_config"><i class="fas fa-cogs"></i>&nbsp;Configuration&nbsp;</a>
                  <div class="form-check form-check-solid form-switch">
                    <input class="form-check-input w-45px h-30px" type="checkbox" id="zoom_status" name="zoom_status" value="1" @php if($account->zoom_status == 1){ echo 'checked="checked"'; } @endphp >
                    <label class="form-check-label" for="githubswitch"></label>
                  </div>
                </div>
              </div> --}}
              <!--end::Item-->

              <div class="separator separator-dashed my-5"></div>
              <!--begin::Item-->
              <div class="d-flex flex-stack">
                <div class="d-flex"> <img src="{{asset('img/images/slack-icon.svg')}}" class="w-30px me-6" alt="" />
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Microsoft</a>
                    <div class="fs-6 fw-bold text-gray-400">Integrate Projects Discussions</div>
                  </div>
                </div>
                <div class="d-flex justify-content-end">
                  <div class="form-check form-check-solid form-switch">
                    <input class="form-check-input w-45px h-30px" type="checkbox" id="microsoft_status" name="microsoft_status" />
                    <label class="form-check-label" for="slackswitch"></label>
                  </div>
                </div>
              </div>
              <!--end::Item-->
            </div>
            <!--end::Items-->
          </div>
          <!--end::Card body-->
        </div>
        <!--end::Content-->
      </div>
      <!--end::Connected Accounts-->
      <!--begin::Notifications-->  
      <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
        <!--begin::Card header-->
        <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_notification_connected_notification" aria-expanded="true" aria-controls="kt_notification_connected_notification">
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Email Notifications</h3>
          </div>
        </div>
        <!--end::Card header-->
        <!--begin::Content-->
        <div id="kt_notification_connected_notification" class="collapse show">
          <!--begin::Card body-->
          <div class="card-body border-top py-8 px-2"><div class="row mb-6">
            <label class="col-lg-3 col-form-label required fw-bold fs-6">Recuitment</label>
            <div class="col-lg-9 fv-row">
              <input type="email" name="recuitment_email" id="recuitment_email" value="{{ (@$notification['Recuitment']->email) ? @$notification['Recuitment']->email : @$account->company_email }}" placeholder="Enter email" class="form-control form-control-solid">
              <div class="form-text">All recruitment-related communications will be sent exclusively to this email address.</div>
              <div class="fv-plugins-message-container invalid-feedback"></div>
            </div>
          </div>
            <div class="row mb-6">
              <label class="col-lg-3 col-form-label required fw-bold fs-6">Sales</label>
              <div class="col-lg-9 fv-row">
                <input type="email" name="sales_email" id="sales_email" value="{{ (@$notification['Sales']->email) ? @$notification['Sales']->email : @$account->company_email }}" placeholder="Enter email" class="form-control form-control-solid">
                <div class="form-text">All Sales-related communications will be sent exclusively to this email address.</div>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            
          </div>
          <!--end::Card body-->
        </div>
        <!--end::Content-->
      </div>
      <!--end::Notifications-->
  <!--begin::Application Duplicate Settings-->
  <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
    <!--begin::Card header-->
    <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#application_duplicate_settings" aria-expanded="true" aria-controls="application_duplicate_settings">
      <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Application Duplicate Setting</h3>
      </div>
    </div>
    <!--end::Card header-->
    <!--begin::Content-->
    <div id="application_duplicate_settings" class="collapse show">
      <!--begin::Card body-->
      <div class="card-body border-top py-8 px-2">
 
          @php $app_duplicate_overroll_val	= $app_duplicate_requisition_wise_val	=	'';  @endphp 
          @if($account->app_duplicate  == 2)
            @php $app_duplicate_overroll_val			=	$account->app_duplicate_days; @endphp 
          @endif
          @if($account->app_duplicate  == 1)
            @php $app_duplicate_requisition_wise_val	=	$account->app_duplicate_days; @endphp 
          @endif


        <div class="form-group fv-plugins-icon-container has-success">
          <div class="row">
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="app_duplicate" value="2" @php if($account->app_duplicate == 2){ echo 'checked="checked"'; } @endphp  >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Overrall System</span>
              <input type="number" name="app_duplicate_overroll" id="app_duplicate_overroll" value="{{ @$app_duplicate_overroll_val }}" placeholder="Enter Days" class="w-33 rounded input_style">
              </span> <span class="option-body">In the overall system, any candidate added to the same or a different job within a specified number of days will be considered a duplicate, and the candidate's status will be marked in red. Duplicate candidates will be detected based on the same phone number and email address only.</span> </span> </label>
            </div>
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="app_duplicate" value="1" @php if($account->app_duplicate == 1){ echo 'checked="checked"'; } @endphp  >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Job wise</span>
              <input type="number" name="app_duplicate_requisition_wise" value="{{ @$app_duplicate_requisition_wise_val }}" placeholder="Enter Days" class="w-33 rounded input_style">
              </span> <span class="option-body">Any candidate added to the same job with the same job ID within a specified number of days will be considered a duplicate, and the candidate's status will be marked in red. Duplicate candidates will be detected based on the same phone number and email address only.</span> <br></span> </label>
            </div>
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="app_duplicate" value="0" @php if($account->app_duplicate == 0){ echo 'checked="checked"'; } @endphp  >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">None</span> </span> <span class="option-body">Duplicates candidates will not be detected by the system based on the same phone number and email address.</span><br><br><br> </span> </label>
            </div>
          </div>
          <div class="form-check"></div>
          <div class="form-text text-muted"></div>
        </div>
      </div>
      <!--end::Card body-->
    </div>
    <!--end::Content-->
  </div>
  <!--end::Application Duplicate Settings-->
  <!--begin::Account Type-->
  <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0 d-none">
    <!--begin::Card header-->
    <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#acount_type" aria-expanded="true" aria-controls="acount_type">
      <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Account Type</h3>
      </div>
    </div>
    <!--end::Card header-->
    <!--begin::Content-->
    <div id="acount_type" class="collapse show">
      <!--begin::Card body-->
      <div class="card-body border-top py-8 px-2">
        <div class="form-group fv-plugins-icon-container has-success">
          <div class="row">
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="acount_type" value="1" checked="checked" @php if($account->acount_type == 1){ echo 'checked="checked"'; } @endphp >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Public</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
            </div>
            <div class="col-lg-4">
              <label class="option"> <span class="option-control"> <span class="radio radio-outline">
              <input type="radio" name="acount_type" value="2" @php if($account->acount_type == 2){ echo 'checked="checked"'; } @endphp >
              <span></span> </span> </span> <span class="option-label"> <span class="option-head"> <span class="option-title fs-4 fw-bolder">Private</span> </span> <span class="option-body">Estimated 2-5 Day Shipping (Duties and taxes may be due upon delivery)</span> </span> </label>
            </div>
          </div>
          <div class="form-check"></div>
          <div class="form-text text-muted"></div>
        </div>
      </div>
      <!--end::Card body-->
    </div>
    <!--end::Content-->
  </div>
  <!--end::Account Type-->
  <!--begin::Department Type-->
  <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
    <!--begin::Card header-->
    <div class="card-header row">
      <div class="cursor-pointer px-0 card-title m-0 col-lg-9" role="button" data-toggle="collapse" data-target="#department" aria-expanded="true" aria-controls="department">
        <h3 class="fw-bolder m-0">Department</h3>
      </div>
      <div class="card-title m-0 col-lg-2 "> @if(in_array("add_settings", $userPermissions)) <a href="javascript:void(0);" class="btn btn-primary align-self-center position-absolute end-0" data-toggle="modal" data-target="#add_department"> Add Department </a> @endif </div>
    </div>
    <!--end::Card header-->
    <!--begin::Content-->
    <div id="department" class="collapse show">
      <!--begin::Card body-->
      <div class="card-body border-top py-8 px-2 dept_content">
        @php 
              $dept		=	'';
              $cnt		=	0;
              if(!empty($departmentArr)){
              foreach($departmentArr as $dval){
                            if($cnt %3==0)
                            {
                              $dept	.=	'<div class="form-group fv-plugins-icon-container has-success">
                                    <div class="row">';					
                            }
                            $cnt++;
                            $dept	.=	'<div class="col-lg-4">
                                            <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                                            <div class="fs-4">'.$dval->name.'</div>
                                            <div class="edit_delete cursor-pointer">													  
                          <a class="text-primary me-2 d-none" href="javascript:void(0);">
                                                <span class="svg-icon svg-icon-primary">                           
                                                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                  <rect x="0" y="0" width="24" height="24"></rect>
                                                  <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
                                                  <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
                                                  </g>
                                                  </svg>
                                                </span>Edit
                                              </a>                        
                                              
                                              
                                              <!--begin::Delete Icon-->
                                              <a class="text-danger dept_delete" href="javascript:void(0);" rel="'.$dval->id.'">
                                                <span class="svg-icon svg-icon-danger svg-icon-2x">
                                                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                  <rect x="0" y="0" width="24" height="24"></rect>
                                                  <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                                  <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                                                  </g>
                                                  </svg>
                                                </span>
                                                <!-- Delete -->
                                              </a>          
                                              <!--end::Delete Icon-->
                                            </div>
                                            </div>
                                            </div>';
                            if($cnt %3==0)
                            {										
                              $dept	.=	'	</div>
                                    <div class="form-check"></div>
                                    <div class="form-text text-muted"></div>
                                  </div>';
                            }			 
              }
              }			 
                  
              echo $dept;									 
        @endphp 
      </div>
      <!--end::Card body-->
    </div>
    <!--end::Content-->
  </div>
  <!--end::Department Type-->
  <!--begin::Deactivate Account-->
  {{-- <div class="card mb-0 mb-xl-0 mx-0 shadow-none rounded-0">
    <!--begin::Card header-->
    <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#deactivate_account" aria-expanded="true" aria-controls="deactivate_account">
      <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Deactivate Account</h3>
      </div>
    </div>
    
    <div id="deactivate_account" class="collapse show">
      <div class="card-body border-top py-8 px-2">
        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
          <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"></rect>
            <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black"></rect>
            <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black"></rect>
          </svg>
          </span>
          <div class="d-flex flex-stack flex-grow-1">
            <div class="fw-bold">
              <h4 class="text-gray-900 fw-bolder">You Are Deactivating Your Account</h4>
              <div class="fs-6 text-gray-700">For extra security, this requires you to confirm your email or phone number when you reset yousignr password. <br>
                <a class="fw-bolder" href="#">Learn more</a> </div>
            </div>
          </div>
        </div>
        
        <div class="form-check form-check-solid fv-row fv-plugins-icon-container">
          <input name="status" class="form-check-input" type="checkbox" value="inactive" id="status" @php if($account->status == 'inactive'){ echo 'checked="checked"'; } @endphp >
          <label class="form-check-label fw-bold ps-2 fs-6" for="deactivate">I confirm my account deactivation</label>
          <div class="fv-plugins-message-container invalid-feedback"></div>
        </div>
       
      </div>
     
      <div class="card-footer d-flex justify-content-end py-3 px-9 d-none">
        <button id="kt_account_deactivate_account_submit" type="submit" class="btn btn-danger fw-bold">Deactivate Account</button>
      </div>
      
      <input type="hidden">
      <div></div>
    </div>
    
  </div> --}}
  <!--end::Deactivate Account-->
  </div>
  <!--end::Main body content-->
  @endif 
  
  
@if(in_array("edit_settings", $userPermissions))
  <div class="card-footer d-flex justify-content-center py-6 px-9 bg-white">
   <a href="{{ route('service_company.account.index') }}" class="btn btn-secondary me-2" >
    	Cancel
	</a>
    <button class="btn btn-primary me-2 updateCompanyProfile" type="button" id="updateCompanyProfile">Save Changes</button>
  </div>
@endif
  </div>
 
</form>
<!--end::Form-->
</div>
<!--end::Settings Content-->
</div>
<!--end::Body-->
</div>
<!--end::Tabs-->
<!--end::Card-->
</div>
<!--end::Tabs-->
</div>
<!--end::Container-->
</div>


<!--begin::Add Interview Sub-Stage modal-->
<div class="modal fade show" id="add_interview_sub_stage" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span>
        </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="add_interview_sub_stage_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <input type="hidden" name="ltb_stage" id="ltb_stage" value="" />
          <input type="hidden" name="ltb_root" id="ltb_root" value="" />
          <input type="hidden" name="ltb_class" id="ltb_class" value="" />
          <div class="mb-11 text-center">
            <h1 class="h1_title">&nbsp;</h1>
          </div>
          <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required lbl_title">&nbsp;</span> </label>
            <input type="text" class="form-control form-control-solid" placeholder="Enter Sub Stage" name="ltb_stage_name" id="ltb_stage_name">
            <div class="fv-plugins-message-container invalid-feedback">
            </div>
          </div>
          <div class="text-center">
            <button type="button" class="btn btn-secondary me-3" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="ltb_submit">Submit</button>
          </div>
          <div></div>
        </form>
        <div id="add_interview_sub_stage_form_loader">&nbsp;</div>
      </div>
    </div>
  </div>
</div>
<!--end::Add Interview Sub-Stage modal--> 

<!--begin::Google config-->
<div class="modal fade show" id="google_config" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal" data-bs-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="google_frm" name="google_frm" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="google_success_alert" style="display:none"> <strong>Success!</strong> Update goole configuration successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="google_warning_alert" style="display:none"> <strong>Warning!</strong> Goole configuration can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Google Configuration Settings</h1>
          </div>
          <div class="google_load" id="google_load"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Google config-->

<!--begin::Add department modal-->
<div class="modal fade show" id="add_department" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="add_department_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="dept_success_alert" style="display:none"> <strong>Success!</strong> Department Added successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="dept_warning_alert" style="display:none"> <strong>Warning!</strong> Department can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Add Department</h1>
          </div>
          <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">Department Name</span> </label>
            <input type="text" class="form-control form-control-solid" placeholder="Enter Department Name" name="dept_name" id="dept_name" maxlength="35">
            <div class="fv-plugins-message-container invalid-feedback"> </div>
          </div>
          <div class="text-center">
            <button type="button" class="btn btn-secondary me-3" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary submit_department">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Add department modal-->

<!--begin::Zoom config-->
<div class="modal fade show" id="zoom_config" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="zoom_configfrm" class="form fv-plugins-bootstrap5 fv-plugins-framework zoom_configfrm" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="zoom_success_alert" style="display:none"> <strong>Success!</strong> Update zoom configuration successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="zoom_warning_alert" style="display:none"> <strong>Warning!</strong> Zoom configuration can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Zoom Configuration Settings</h1>
          </div>
          <div class="zoom_load" id="zoom_load"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--begin::Zoom config-->

<!--begin::Google Sheet config-->
<div class="modal fade show" id="google_sheet_config" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="google_sheet_configfrm" class="form fv-plugins-bootstrap5 fv-plugins-framework google_sheet_configfrm" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="google_sheet_success_alert" style="display:none"> <strong>Success!</strong> Update Google Sheet configuration successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="google_sheet_warning_alert" style="display:none"> <strong>Warning!</strong> Google Sheet configuration can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Google Sheet Configuration Settings</h1>
          </div>
          <div class="google_sheet_load" id="google_sheet_load"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--begin::Zoom config-->


<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark"></h3>
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



@endsection
@push('footer-script')

<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>

  $('input[name="app_duplicate"]').on('change', function () {
      $('input[name="app_duplicate"]').removeAttr('style');

      if ($(this).is(':checked')) {
          $(this).css({
              'border-color': 'transparent',
              'background-color': 'currentColor'
          });
      }
  }).trigger('change');

if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
            
            CKEDITOR.tools.enableHtml5Elements( document );
            CKEDITOR.config.height 		=	'auto';
            CKEDITOR.config.width 		=	'auto';

            CKEDITOR.plugins.add('customDropdown', {
                  icons: 'customDropdown',
                  init: function (editor) {
                      editor.ui.addRichCombo('customFields', {
                          label: 'Insert Field',
                          title: 'Insert Field',
                          voiceLabel: 'Insert Field',
                          className: 'cke_format',
                          multiSelect: false,

                          panel: {
                              css: [CKEDITOR.skin.getPath('editor')].concat(editor.config.contentsCss),
                              voiceLabel: editor.lang.panelVoiceLabel
                          },

                          init: function () {
                              // Add options to the dropdown
                              this.add('CANDIDATE_NAME', 'Candidate Name');
                              this.add('CANDIDATE_EMAIL', 'Candidate Email');
                              this.add('CANDIDATE_MOBILE', 'Candidate Mobile No.');
                              this.add('COMPANY_NAME', 'Company Name');
                              this.add('JOB_TITLE', 'Job Position');
                              this.add('JOINING_DATE', 'Joining date');
                              this.add('AGENCY_NAME', 'Agency Name');
                              this.add('AGENCY_ADDRESS', 'Agency Address');
                              this.add('COMPANY_NAME', 'Company Name');
                              this.add('COMPANY_ADDRESS', 'Company Address');
                              this.add('TODAY_DATE', 'Today Date');
                          },

                          onClick: function (value) {
                              // Insert the selected field into the editor
                              editor.insertText('[' + value + ']');
                          }
                      });
                  }
            });
            


            
            var initSample 				= ( function() 
            {
                    var wysiwygareaAvailable 	= isWysiwygareaAvailable(),
                    isBBCodeBuiltIn 			= !!CKEDITOR.plugins.get( 'bbcode' );
            
                return function() {
                                            var editorElement = CKEDITOR.document.getById( 'kt_docs_tinymce_hidden' );
                                    
                                            if ( isBBCodeBuiltIn ) {
                                                editorElement.setHtml(
                                                    'Hello world!\n\n' +
                                                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                                                );
                                            }
                                            if ( wysiwygareaAvailable ) {
                                                // CKEDITOR.replace( 'kt_docs_tinymce_hidden' );
                                                CKEDITOR.replace('kt_docs_tinymce_hidden', {
                                                  extraPlugins: 'customDropdown', // Add the custom dropdown plugin
                                                  toolbarGroups: [
                                                      { name: 'clipboard', groups: ['clipboard', 'undo'] },
                                                      { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
                                                      { name: 'links' },
                                                      { name: 'insert' },
                                                      { name: 'forms' },
                                                      { name: 'tools' },
                                                      { name: 'document', groups: ['mode', 'document', 'doctools'] },
                                                      { name: 'others' },
                                                      { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                                                      { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
                                                      { name: 'styles' },
                                                      { name: 'colors' },
                                                      { name: 'customFields', groups: ['customFields'] }
                                                  ]
                                                });
                                            } else {
                                                editorElement.setAttribute( 'contenteditable', 'true' );   
                                                CKEDITOR.inline( 'kt_docs_tinymce_hidden' );
                                            }							
                                };
                function isWysiwygareaAvailable() 
                {
                    if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
                        return true;
                    }
                    return !!CKEDITOR.plugins.get( 'wysiwygarea' );
                }
                
            } )();
            
            
            initSample();


            var initSampleoffer 				= ( function() 
            {
                    var wysiwygareaAvailable 	= isWysiwygareaAvailable(),
                    isBBCodeBuiltIn 			= !!CKEDITOR.plugins.get( 'bbcode' );
            
                return function() {
                                            var editorElement = CKEDITOR.document.getById( 'kt_docs_tinymce_hiddenoffer' );
                                    
                                            if ( isBBCodeBuiltIn ) {
                                                editorElement.setHtml(
                                                    'Hello world!\n\n' +
                                                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                                                );
                                            }
                                            if ( wysiwygareaAvailable ) {
                                                // CKEDITOR.replace( 'kt_docs_tinymce_hiddenoffer' );
                                                CKEDITOR.replace('kt_docs_tinymce_hiddenoffer', {
                                                  extraPlugins: 'customDropdown', // Add the custom dropdown plugin
                                                  toolbarGroups: [
                                                      { name: 'clipboard', groups: ['clipboard', 'undo'] },
                                                      { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
                                                      { name: 'links' },
                                                      { name: 'insert' },
                                                      { name: 'forms' },
                                                      { name: 'tools' },
                                                      { name: 'document', groups: ['mode', 'document', 'doctools'] },
                                                      { name: 'others' },
                                                      { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                                                      { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
                                                      { name: 'styles' },
                                                      { name: 'colors' }
                                                  ]
                                                });

                                            } else {
                                                editorElement.setAttribute( 'contenteditable', 'true' );   
                                                CKEDITOR.inline( 'kt_docs_tinymce_hiddenoffer' );
                                            }							
                                };
                function isWysiwygareaAvailable() 
                {
                    if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
                        return true;
                    }
                    return !!CKEDITOR.plugins.get( 'wysiwygarea' );
                }
                
            } )();

            initSampleoffer();


            

document.getElementById('add_interview_sub_stage_form').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault(); 
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editBtn'); 
    var viewTerms = document.getElementById('viewTerms');
    var editTerms = document.getElementById('editTerms');
    var updateCompanyTermsTop = document.getElementById('updateCompanyTermsTop'); 
    var setToDefaultCompanyTermsTop = document.getElementById('setToDefaultCompanyTermsTop'); 
    updateCompanyTermsTop.style.display = "none";
    setToDefaultCompanyTermsTop.style.display = "none";
    
    editBtn.addEventListener('click', function() {
        if (viewTerms.style.display === "none") {
            viewTerms.style.display = "block";
            editTerms.style.display = "none";
            editBtn.textContent = "Edit";
            updateCompanyTermsTop.style.display = "none";
            setToDefaultCompanyTermsTop.style.display = "none";
        } else {
            viewTerms.style.display = "none";
            editTerms.style.display = "block";
            editBtn.textContent = "View";
            updateCompanyTermsTop.style.display = "inline-flex";
            setToDefaultCompanyTermsTop.style.display = "inline-flex";
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editBtnoffer'); 
    var viewTerms = document.getElementById('viewTermsoffer');
    var editTerms = document.getElementById('editTermsoffer');
   var updateCompanyTermsofferTop = document.getElementById('updateCompanyTermsofferTop'); 
   var setToDefaultCompanyTermsofferTop = document.getElementById('setToDefaultCompanyTermsofferTop'); 
   updateCompanyTermsofferTop.style.display = "none";
   setToDefaultCompanyTermsofferTop.style.display = "none";

    editBtn.addEventListener('click', function() {
        if (viewTerms.style.display === "none") {
            viewTerms.style.display = "block";
            editTerms.style.display = "none";
            editBtn.textContent = "Edit";
            updateCompanyTermsofferTop.style.display = "none";
            setToDefaultCompanyTermsofferTop.style.display = "none";
        } else {
            viewTerms.style.display = "none";
            editTerms.style.display = "block";
            editBtn.textContent = "View";           
            updateCompanyTermsofferTop.style.display = "block";
            setToDefaultCompanyTermsofferTop.style.display = "block";
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editBtnofferComponent'); 
    var viewTerms = document.getElementById('viewoffercomponent');

    editBtn.addEventListener('click', function() {
        if (viewTerms.getAttribute('data-editing') === 'true') {
            // Switch to "View" mode
            viewTerms.querySelectorAll('.editable').forEach(function(cell) {
                var input = cell.querySelector('input');
                if (input) {
                    cell.textContent = input.value; // Replace input with text
                }
            });
            viewTerms.setAttribute('data-editing', 'false');
            editBtn.textContent = "Edit";

        } else {
            // Switch to "Edit" mode
            viewTerms.querySelectorAll('.editable').forEach(function(cell) {
                var text = cell.textContent;
                // Add form-control and form-control-solid classes
                cell.innerHTML = '<input type="text" value="' + text + '" class="form-control form-control-solid" style="width: 33%;">';
            });
            viewTerms.setAttribute('data-editing', 'true');
            editBtn.textContent = "View";
        }
    });
});

$(document).on('click','.updateCompanyTermsofferTop',function(){
      $('.updateCompanyTermsoffer').click();
})
$(document).on('click','.setToDefaultCompanyTermsofferTop',function(){
      $('.setToDefaultCompanyTermsoffer').click();
})

$(document).on('click','.updateCompanyTermsTop',function(){
      $('.updateCompanyTerms').click();
})
$(document).on('click','.setToDefaultCompanyTermsTop',function(){
      $('.setToDefaultCompanyTerms').click();
})






var input = document.querySelector('input[name=tags]'); 
   // initialize Tagify on the above input node reference
new Tagify(input)


  

      $('body').on('click', '.interview_stage', function(){
    
            $('#add_interview_sub_stage_form').show();
            $('#add_interview_sub_stage_form_loader').hide();
            
            $('.h1_title').html('');	
            $('.lbl_title').html('');	
            $('#ltb_stage_name').val('');	
            
            var id 				=	$(this).data('id');
            var root			=	$(this).data('root');	
            var ltb_class		=	$(this).data('class');
            
            if(id == 1){
              $('.h1_title').html('Add Sourcing Sub-Stage');	
            $('.lbl_title').html('Sourcing Sub stage Name')
            }else if(id == 2){
              $('.h1_title').html('Add Screening Sub-Stage');	
            $('.lbl_title').html('Screening Sub stage Name')
            }else if(id == 3){
              $('.h1_title').html('Add Evaluation Sub-Stage');	
            $('.lbl_title').html('Evaluation Sub stage Name')
            }else if(id == 4){
              $('.h1_title').html('Add Negotation Sub-Stage');	
            $('.lbl_title').html('Negotation Sub stage Name')
            }else if(id == 5){
              $('.h1_title').html('Add Offer Sub-Stage');	
            $('.lbl_title').html('Offer Sub stage Name')
            }else if(id == 6){
              $('.h1_title').html('Add Joined Sub-Stage');	
            $('.lbl_title').html('Joined Sub stage Name')
            }else if(id == 7){
              $('.h1_title').html('Add Rejected Sub-Stage');	
            $('.lbl_title').html('Rejected Sub stage Name')
            }
            $('#ltb_stage').val(id);
            $('#ltb_root').val(root); 
            $('#ltb_class').val(ltb_class); 
      });

      $('body').on('click', '#ltb_submit', function(e){
          
          var ltb_stage			=	$('#ltb_stage').val();
          var ltb_root			=	$('#ltb_root').val();
          var ltb_stage_name		=	$('#ltb_stage_name').val();
          var ltb_class			=	$('#ltb_class').val();
          var url 				=	"{{ route('service_company.account.addIntStage') }}";
          var token 				=	"{{ csrf_token() }}";
          var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
                      
          $.ajax({
                type		: 'POST',
                url			: url,
                beforeSend	: function() {
                              $('#add_interview_sub_stage_form').hide();
                              $('#add_interview_sub_stage_form_loader').show();
                              $('#add_interview_sub_stage_form_loader').html('<img src="'+baseurl+'" height="60%" width="100%">');
                            },
                data		: {'_token': token, 'ltb_stage':ltb_stage, 'ltb_stage_name':ltb_stage_name, 'ltb_class':ltb_class },
                success		: function (response) {
                                    if (response.status == "success") {
                                                        $("#"+ltb_root).last().append(response.stageHtml);
                                                        $('#add_interview_sub_stage_form_loader').show();
                                                        $('#add_interview_sub_stage_form').hide();
                                                        $('#add_interview_sub_stage_form_loader').html(response.message);
                                                      }
                                }
            });
              
      });	  
      
      

      function boostHandleClick(currentValue, currentId) {
          var chkStatus = currentValue.checked;
          var userId = currentId;
          var statusText = $(currentValue).parents('td').find('.status_text');
          if ($(currentValue).is(':checked')) {
              statusText.text('Active').removeClass('text-danger').addClass('text-success');
          } else {
              statusText.text('Inactive').removeClass('text-success').addClass('text-danger');
          }
      
          var url = "{{ route('service_company.account.boostStatusUpdate') }}";
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

	 
      $('body').on('click', '.delete_process', function(){
                  var id = 123;
                  swal({
                      title				: "@lang('errors.areYouSure')",
                      text				: "@lang('errors.deleteWarning')",
                      type				: "warning",
                      showCancelButton	: true,
                      confirmButtonColor	: "#DD6B55",
                      confirmButtonText	: "@lang('app.delete')",
                      cancelButtonText	: "@lang('app.cancel')",
                      closeOnConfirm		: true,
                      closeOnCancel		: true
                  }, function(isConfirm){
                      if (isConfirm) {
                          var url 	=	"{{ route('service_company.job-categories.destroy',':id') }}";
                          url 		=	url.replace(':id', id);
                          var token 	=	"{{ csrf_token() }}";
                          $.ajax({
                      type	: 'POST',
                      url	: url,
                      data	: {'_token': token, '_method': 'DELETE'},
                      success: function (response) {
                        if (response.status == "success") {
                          $.unblockUI();
                          table._fnDraw();
                        }
                      }
                });
                      }
                  });
      });
    
      $('#kt_docs_jkanban_basic1').on('click', '.stage_block', function() { 
        
        var block 		=	$(this).attr('rel');		
          if(block > 0){
                  var url 				= "{{ route('service_company.account.delIntStage') }}";
                  var token 				= "{{ csrf_token() }}";							
                  var baseurl    			= "{{ asset('assets/images/Loading2.gif') }}"; 
                
                      const swalWithBootstrapButtons = Swal.mixin({
                                      customClass		: {	confirmButton	: 'btn btn-success',	cancelButton	: 'btn btn-danger'},
                                      buttonsStyling	: false
                      })
                      swalWithBootstrapButtons.fire({
                                title				: 'Are you sure?',
                                text				: "You won't be able to revert this!",
                                icon				: 'warning',
                                showCancelButton	: true,
                                confirmButtonText	: 'Yes, Delete it!',
                                cancelButtonText	: 'No, cancel!',
                                reverseButtons	: true
                      }).then((result) => {					
                              if (result.isConfirmed) {
                              
                                $.ajax({
                                      type		: 'POST',
                                      url			: url,
                                      beforeSend	: function() {},
                                      data		: {'_token': token, 'block':block},
                                      success		: function (response) {
                                        if (response.status == "success") {
                                                          $('#'+block).remove();	 
                                                          swalWithBootstrapButtons.fire
                                                        ('Deleted!',	'Selected request has been Deleted successfully.',	'success')																														
                                        }
                                        else
                                        {
                                          swalWithBootstrapButtons.fire
                                          ( 'Opps Error, Please try again!', 'Something wrong, please try again.', 'success' )
                                        }								  
                                      }
                                });

                              } 
                              else if (result.dismiss === Swal.DismissReason.cancel) {
                                swalWithBootstrapButtons.fire('Cancelled', 'Selected request is safe :)', 'error' )
                              }				
                    })
          } 
                  
      });  
			 
      $('body').on('click', '.google_config', function(){
                    var id    					=	123;
                    var url   					=	"{{ route('service_company.account.google',':id') }}";
                    url     					=	url.replace(':id', id);
                    var token 					=	"{{ csrf_token() }}";								
                    var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
                    $.ajax({
                          type			:	'GET',
                          url				:	url,
                          beforeSend		:	function() {		
                                      $('#google_load').html('<img src="'+baseurl+'" height="60%" width="100%">');
                                  },
                          success			: function (response) {
                                              if (response.status == "success") {
                                                $('#google_load').html(response.view);	 	
                                              }	
                                          }
                    });
      });
   		
		  $('body').on('click', '.zoom_config', function(){   								
   								var id    					=	123;
   								var url   					=	"{{ route('service_company.account.zoom',':id') }}";
   								url     					=	url.replace(':id', id);
   								var token 					=	"{{ csrf_token() }}";								
   								var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
   								$.ajax({
   											type		:	'GET',
   											url			:	url,
   											beforeSend	: 	function() {		
   														 				$('#zoom_load').html('<img src="'+baseurl+'" height="60%" width="100%">');
   															},
   											success		: 	function (response) {
   																					if (response.status == "success") {
   																						$('#zoom_load').html(response.view);	 	
   																					}	
   																			 }
   								});   								
   		});


      $('body').on('click', '.google_sheet_config', function(){   								
   								var id    					=	123;
   								var url   					=	"{{ route('service_company.account.zoom',':id') }}";
   								url     					=	url.replace(':id', id);
   								var token 					=	"{{ csrf_token() }}";								
   								var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
   								$.ajax({
   											type		:	'GET',
   											url			:	url,
   											beforeSend	: 	function() {		
   														 				$('#google_sheet_load').html('<img src="'+baseurl+'" height="60%" width="100%">');
   															},
   											success		: 	function (response) {
   																					if (response.status == "success") {
   																						$('#google_sheet_load').html(response.view);	 	
   																					}	
   																			 }
   								});   								
   		});

   
   
</script>      

<script>

$(document).ready(function(){
   
   			$('.submit_department').click(function(){
   									var dept_name	=	$('#dept_name').val();								
									if(dept_name ==''){
											$('#dept_name').val('');	
											$("#dept_warning_alert").fadeTo(2000, 500).slideUp(500, function() {
												$("#dept_warning_alert").slideUp(500);
											});	
											return false;
									}
									var url 					= "{{ route('service_company.account.storeDept') }}";
									var token 					= "{{ csrf_token() }}";
									var baseurl    				= "{{ asset('assets/images/Loading2.gif') }}"; 		
									$.ajax({
												type		: 'POST',
												url			: url,
												beforeSend	: function() { $('.dept_content').html('<img src="'+baseurl+'" height="60%" width="100%">'); },
												data		: {'_token': token, 'dept_name':dept_name },
												success		: function (response) {
																						if (response.status == "success") {
																																$('#dept_name').val('');
																																$("#dept_success_alert").fadeTo(2000, 500).slideUp(500, function() {
																																	$("#dept_success_alert").slideUp(500);
																																});
																																$('.dept_content').html(response.html);																																	
																														  }
																				 }
									});
   			});
				
			  $('body').on('click', '.dept_delete', function(){
   	   			
   				   var id				=	$(this).attr('rel');   			   			
				   Swal.fire({
				   title				: "@lang('errors.areYouSure')",
				   text					: "@lang('errors.deleteWarning')",
				   type					: "warning",
				   showCancelButton		: true,
				   confirmButtonColor	: "#DD6B55",
				   confirmButtonText	: "@lang('app.delete')",
				   cancelButtonText		: "@lang('app.cancel')",
				   closeOnConfirm		: true,
				   closeOnCancel		: true
				   })
						   .then((result) => {
											  if (result.isConfirmed) {
											  
																			var url 		=	"{{ route('service_company.job-categories.destroy',':id') }}";
																			url 			=	url.replace(':id', id);
																			var token 		=	"{{ csrf_token() }}";
																			$.ajax({
																						type	: 'POST',
																						url		: url,
																						data	: {'_token': token, '_method': 'DELETE'},
																						success	: function (response) {
																														  if(response.status == "success") {
																																Swal.fire('Deleted!', 'Your record has been deleted.', 'success');
                                                                $('.dept_content').html(response.html);	
																														   }
																													  }
																				});											   																			
																	  }
						   })	
   			});				
			
        $('body').on('click', '#updateCompanyProfile', function(){
                    
            var data 				=	new FormData();
            var form_data 			=	$('#setting_content').serializeArray();
            $.each(form_data, function (key, input) {
                data.append(input.name, input.value);
            });				
            
              $.ajax({
                    url					:	'{{route('service_company.account.update', @$account->id)}}',
                    headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    method				:	"POST",
                    processData			:	false,
                    contentType			:	false,
                    data				:	data,
                    redirect			:	true,
                    success				:	function (data) {

                                            if(data.status == 'warning'){
                                                Swal.fire({
                                                            text				: data.message,
                                                            icon				: "error",
                                                            buttonsStyling		: !1,
                                                            confirmButtonText	: "Ok, got it!",
                                                            customClass			: { confirmButton: "btn btn-light" },
                                                }); 

                                            }

                                          if(data.status == 'success'){
                                              Swal.fire("Success", "Account settings update successfully!", "success");	
                                          }
                                }
                  });				
        });	


            $('body').on('click', '#updateCompanyTerms', function(){
                    $("#updateCompanyTermsTop").text("Please wait...");
                    var data 				=	new FormData();
                    var kt_docs_tinymce_hidden = CKEDITOR.instances.kt_docs_tinymce_hidden.getData();	
                    data.append("description", kt_docs_tinymce_hidden);
                    var form_data 			=	$('#terms_content').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });		
                    data.append("descriptiontype", "terms");
                       
                      $.ajax({
                            url					:	'{{route('service_company.account.update', @$account->id)}}',
                            headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method				:	"POST",
                            processData			:	false,
                            contentType			:	false,
                            data				:	data,
                            redirect			:	true,
                            success				:	function (data) {
                                  $("#updateCompanyTermsTop").text("Save Changes")
                                  
                                                  if(data.status == 'success'){
                                                    $('#viewTerms').html(data.terms);	
                                                      Swal.fire("Success", "Terms & Condition update successfully!", "success");	
                                                      $('#viewTerms').show();
                                                      $('#editTerms').hide(); 
                                                      $("#editBtn").text("Edit");
                                                      $('.updateCompanyTermsTop').hide(); 
                                                      $('.setToDefaultCompanyTermsTop').hide(); 

                                                  }
                                        }
                          });				
                });

                $('body').on('click', '#updateCompanyTermsoffer', function(){
                    $("#updateCompanyTermsofferTop").text("Please wait...");
                    var data 				=	new FormData();
                    var kt_docs_tinymce_hiddenoffer = CKEDITOR.instances.kt_docs_tinymce_hiddenoffer.getData();	
                    data.append("description", kt_docs_tinymce_hiddenoffer);
                    var form_data 			=	$('#terms_contentoffer').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });		
                    data.append("descriptiontype", "offer_terms");
                       
                      $.ajax({
                            url					:	'{{route('service_company.account.update', @$account->id)}}',
                            headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method				:	"POST",
                            processData			:	false,
                            contentType			:	false,
                            data				:	data,
                            redirect			:	true,
                            success				:	function (data) {
                                                $("#updateCompanyTermsofferTop").text("Save Changes");
                                                  if(data.status == 'success'){
                                                    $('#viewTermsoffer').html(data.terms);	
                                                      Swal.fire("Success", "Terms & Condition update successfully!", "success");	
                                                      $('#viewTermsoffer').show();
                                                      $('#editTermsoffer').hide();
                                                      $("#editBtnoffer").text("Edit");
                                                      $('.updateCompanyTermsofferTop').hide(); 
                                                      $('.setToDefaultCompanyTermsofferTop').hide(); 

                                                  }
                                        }
                          });				
                });



                $('body').on('click', '#setToDefaultCompanyTerms', function(){
                    $("#setToDefaultCompanyTermsTop").text("Please wait...");
                    var data 				=	new FormData();
                    var kt_docs_tinymce_hidden = 'setDefault';	
                    data.append("description", kt_docs_tinymce_hidden);
                    var form_data 			=	$('#terms_content').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });		
                    CKEDITOR.replace('kt_docs_tinymce_hidden');
                    data.append("description", kt_docs_tinymce_hidden);
                    data.append("descriptiontype", "terms");
                       
                      $.ajax({
                            url					:	'{{route('service_company.account.update', @$account->id)}}',
                            headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method				:	"POST",
                            processData			:	false,
                            contentType			:	false,
                            data				:	data,
                            redirect			:	true,
                            success				:	function (data) {
                                    $("#setToDefaultCompanyTermsTop").text("Set to Default")
                                                  if(data.status == 'success'){
                                                    var kt_docs_tinymce_hidden = CKEDITOR.instances.kt_docs_tinymce_hidden.setData(data.terms);	
                                                  }
                                        }
                          });				
                });

                $('body').on('click', '#setToDefaultCompanyTermsoffer', function(){
                    
                    var data 				=	new FormData();
                    var kt_docs_tinymce_hiddenoffer = 'setDefault';	
                    data.append("description", kt_docs_tinymce_hiddenoffer);
                    $("#setToDefaultCompanyTermsofferTop").text("Please wait...");
                    var form_data 			=	$('#terms_content').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });		
                    CKEDITOR.replace('kt_docs_tinymce_hiddenoffer');
                    data.append("description", kt_docs_tinymce_hiddenoffer);
                    data.append("descriptiontype", "offer_terms");
                       
                      $.ajax({
                            url					:	'{{route('service_company.account.update', @$account->id)}}',
                            headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method				:	"POST",
                            processData			:	false,
                            contentType			:	false,
                            data				:	data,
                            redirect			:	true,
                            success				:	function (data) {
                                                    $("#setToDefaultCompanyTermsofferTop").text("Set to Default")
                                                  if(data.status == 'success'){
                                                    var kt_docs_tinymce_hidden = CKEDITOR.instances.kt_docs_tinymce_hiddenoffer.setData(data.terms);	
                                                  }
                                        }
                          });				
                });


                $('.ser_show-detail').click(function(){
                  $('#kt_activitiesJob_toggle').trigger('click');		
                  var id    		=	$(this).attr('data-type');
                  var url   		=	"{{ route('service_company.jobs.serviceviewmore',':id') }}?viewmore=viewmore";
                  url     		=	url.replace(':id', id);
                  var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
                  $.ajax({
                          type  		: 'GET',
                          url   		: url,
                          beforeSend	: function() {
                                                $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                    },
                          success 	: function (response) {
                              $('#kt_activitiesJob_body').html(response.view);	
                          }
                        });
                });



                $(document).on('click','.rateTypeClass', function(){
      var rateType = $(this).val();
      if(rateType==2){
            $('.rate-section').addClass('d-none');
            $('.rate-perecentage').removeClass('d-none');
      }else{
            $('.rate-section').removeClass('d-none');
            $('.rate-perecentage').addClass('d-none');
      }
      });

       $(document).on('change','select[name="sal_mode"]', function(){
            var selectedValue = $(this).val();
            $('select[name="sal_mode"]').val(selectedValue);
        });
        
        $(document).on('change','select[name="salary_currency"]', function(){
            var selectedValue = $(this).val();
            $('select[name="salary_currency"]').val(selectedValue);
        });


        $(document).on('click','.update_main_status_fulltime', function(){

           var status = $('.update_main_status_fulltime').val();
           if(status == 1){
            var changeStatus = 0;
            $('#f_text').text('OFF');
           }else{
            var changeStatus = 1;
            $('#f_text').text('ON');
           }
           var id = $(this).attr('data-row_id');
           
           var url       	=	"{{ route('service_company.jobs.updateserviceviewmore',':id') }}";
           var token       =	"{{ csrf_token() }}";
                $.ajax({
                                   type		: 'POST',
                                   url			: url,
                                   data		: {'_token': token, '_method': 'POST', 'id' : id, 'changeStatus':changeStatus},
                                   success 	: function (response) {
                                           if (response.status == "success") {
                                             if(changeStatus==1){
                                                $('.class_change_fulltime').attr('style','');
                                             }else{
                                                $('.class_change_fulltime').attr('style','opacity: 0.2 !important;');
                                             }
                                            var status = $('.update_main_status_fulltime').val(changeStatus);
                                           }
                                     }
                           });
                          


        });

        $(document).on('click','.contract_main_status_fulltime', function(){

        var status = $('.contract_main_status_fulltime').val();
        if(status == 1){
        var changeStatus = 0;
        $('#c_text').text('OFF');
        }else{
        var changeStatus = 1;
        $('#c_text').text('ON');
        }
        var id = $(this).attr('data-row_id');

        var url       	=	"{{ route('service_company.jobs.updateserviceviewmore',':id') }}";
        var token       =	"{{ csrf_token() }}";
            $.ajax({
                                type		: 'POST',
                                url			: url,
                                data		: {'_token': token, '_method': 'POST', 'id' : id, 'changeStatus':changeStatus},
                                success 	: function (response) {
                                        if (response.status == "success") {

                                          if(changeStatus==1){
                                                $('.class_change_contract').attr('style','');
                                             }else{
                                                $('.class_change_contract').attr('style','opacity: 0.2 !important;');
                                             }

                                          var status = $('.contract_main_status_fulltime').val(changeStatus);
                                        }
                                  }
                        });
                      


        });





        $('body').on('click', '#editBtnCampaign', function(){
			
              var url 				=	"{{ route('service_company.auth-custom-package.validatePackage') }}";
              var token 				=	"{{ csrf_token() }}";							
              var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
              var eventId 			=	0; 
              $.ajax({
                    type		: 'POST',
                    url			: url,
                    beforeSend	: function(){ },
                    data		: {'_token': token, 'event':'KEYWORD_ADD','eventId':eventId},
                    success		: function (response){
                                          if (response.status == "success"){  																					  																												                                                                                            	var url   			=	"{{url('service_company/kayaccount/key-add-create')}}";
                                                window.location 	=	url;
                                            }else if (response.status == "info"){   
                                              sweetAlert("Fail!", response.message, "error");	
                                            }										 
                                    }
                  });
          
        });



        $(document).on('click','#form-submit', function(){					 
						 
             const swalWithBootstrapButtons = Swal.mixin({
                 customClass: {
                         confirmButton: 'btn btn-success',
                         cancelButton: 'btn btn-danger'
                       },
                  buttonsStyling: false
             })
             swalWithBootstrapButtons.fire({
                               title				: 'Are you sure?',
                               text				: "You won't be able to revert this!",
                               icon				: 'warning',
                               showCancelButton	: true,
                               confirmButtonText	: 'Yes, update it!',
                               cancelButtonText	: 'No, cancel!',
                               reverseButtons	: true 
                           }).then((result) => {
               if (result.isConfirmed) {
                     
                           var id        	=	1;
                           var url       	=	"{{ route('service_company.jobs.addUpdateJobServices',':id') }}";
                           url         	=	url.replace(':id', id);
                           var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
                           var token       =	"{{ csrf_token() }}";
                           var arrCountry 		=	[];
                           $('.countries:checked').each(function () {
                            arrCountry.push($(this).val());
                           });
                           var countryCount = arrCountry.length;
                           var rate_val = $(".rate_val").map(function() {
                              return this.value;
                            }).get();
                            var rateTypeClass = $('.rateTypeClass').val();
                            var main_status = $('.main_status').val();
                            var sal_mode = $('select[name="sal_mode"]').val();
                            var salary_currency = $('select[name="salary_currency"]').val();
                            var updateId = $('.updateId').val();
                            var commType = $('.commType').val();
                            if(commType==2){
                              sal_mode =null;
                              salary_currency =null;

                              rate_val = $(".perecentage_val").map(function() {
                              return this.value;
                            }).get();


                            }
                         				
                           $.ajax({
                                   type		: 'POST',
                                   url			: url,
                                   data		: {'_token': token, '_method': 'POST', 'arrCountry' : arrCountry, 'rate_val': rate_val,'rateTypeClass':rateTypeClass,'sal_mode':sal_mode,'salary_currency':salary_currency,'updateId':updateId,'commType':commType,'main_status':main_status},
                                   success 	: function (response) {
                                           if (response.status == "success") {
                                            if(rateTypeClass==2){
                                             $('#fullTimeCount').text(countryCount);
                                            }
                                            if(rateTypeClass==1){
                                              $('#contractCount').text(countryCount);
                                            }
                                               swalWithBootstrapButtons.fire(
                                                                 'Success!',
                                                                 'Updated Successfully.',
                                                                 'success'
                                                               )
                                           }
                                     }
                           });
                        
               } 
               else if (result.dismiss === Swal.DismissReason.cancel) {
                     swalWithBootstrapButtons.fire(
                                      'Cancelled',
                                     'Selected request is safe :)',
                                     'error'
                                     )
               }
             })	});




	
});

</script>	
@endpush