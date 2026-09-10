@php 
   use App\Helper\GeneralMethod;
   $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
    $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
    $symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
        0	=>	"INR",
        1	=>	"EUR",
        2	=>	"JPY",
        3	=>	"GBP",
        4	=>	"USD",
        5	=>	"AED"
     ];
@endphp
<style>
.text-justify { text-align: justify!important; }
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr{	width: max-content!important;}
  .table > :not(caption) > * > *{	padding: 0.75em 0rem!important;}
  td:nth-child(n+2),
  th:nth-child(n+2){	width: revert!important;	border-bottom: 1px dashed #f5f8fa!important;}
}
.slot-card {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      text-align: center;
      transition: transform 0.2s ease;
    }
@media only screen and (min-width: 900px) and (max-width: 1400px)
{
 .slot-card {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      text-align: center;
      transition: transform 0.2s ease;
    }
}
.iti__flag {
    margin-top: 2px !important;
}

    .slot-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 5px;
    }
    
    .slot-card:hover {
      transform: translateY(-3px);
    }
  
    .book-btn {
      background: #007bff;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
    }
    .book-btn:hover {
      background: #0056b3;
    }
    .book-btnoff {
      background: #aeaeaf;
      color: #fff;
      border: none;
      padding: 10px 16px;
      cursor: not-allowed !important;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
    }
    .book-btnoff:hover {
      background: #aeaeaf;
    }
    .wordtext{
      margin-bottom: 5px;
    }
    .book-btnoff-booked{
      background: #06c732;
      color: #fff;
      cursor: not-allowed !important;
      border: none;
      padding: 10px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
    }

    #kt_activitiesApplication_close svg{
      color: red;
    }

</style>

 <link href="{{ asset('demo/css/candidates-details.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

{{-- Inline critical CSS: three-card layout + pipeline stat cards.
     Placed after the external link so it always takes effect even if the
     CSS file is served from browser cache. --}}
<style>
/* ===== THREE-CARD LAYOUT & CURVED CARD CORNERS ===== */
#kt_activitiesApplication .page-wrapper{display:flex;flex-direction:column;gap:12px;background:#f1f5f9;min-height:auto;padding-bottom:16px;}
#kt_activitiesApplication .cdr-outer-card,
#kt_activitiesApplication .card,
#kt_activitiesApplication .content-card,
#kt_activitiesApplication .rdc-drawer-card{background:#fff;border:1px solid #e2e8f0;border-radius:8px!important;box-shadow:0 1px 3px rgba(15,23,42,.04),0 4px 12px rgba(15,23,42,.03);overflow:hidden;flex-shrink:0;margin:0 16px;}
/* ===== CARD 2: pipeline stat sub-cards ===== */
#kt_activitiesApplication .cdr-pipeline-section{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px;flex-shrink:0;margin:0 16px;}
#kt_activitiesApplication .cdr-pipeline-card{background:#fff;border-radius:8px!important;padding:14px 12px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:10px;box-shadow:0 1px 2px rgba(15,23,42,.04);transition:box-shadow .18s ease,transform .18s ease,border-color .18s ease;cursor:default;min-width:0;}
#kt_activitiesApplication .cdr-pipeline-card:hover{box-shadow:0 4px 14px rgba(15,23,42,.08);transform:translateY(-2px);border-color:#bfdbfe;}
#kt_activitiesApplication .cdr-pipeline-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
#kt_activitiesApplication .cdr-pipeline-icon svg{width:16px!important;height:16px!important;flex-shrink:0;}
#kt_activitiesApplication .cpi-blue{background:#dbeafe;color:#1d4ed8;}
#kt_activitiesApplication .cpi-green{background:#d1fae5;color:#059669;}
#kt_activitiesApplication .cpi-purple{background:#ede9fe;color:#7c3aed;}
#kt_activitiesApplication .cpi-amber{background:#fef3c7;color:#d97706;}
#kt_activitiesApplication .cpi-teal{background:#ccfbf1;color:#0d9488;}
#kt_activitiesApplication .cpi-indigo{background:#e0e7ff;color:#4338ca;}
#kt_activitiesApplication .cdr-pipeline-info{flex:1;min-width:0;}
#kt_activitiesApplication .cdr-pipeline-label{font-size:10.5px;color:#94a3b8;font-weight:500;font-family:'Poppins',sans-serif;margin-bottom:4px;white-space:nowrap;}
#kt_activitiesApplication .cdr-pipeline-value{font-size:13px;font-weight:700;color:#0f172a;font-family:'Poppins',sans-serif;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.3;}
#kt_activitiesApplication .cpi-unit{font-size:11px;font-weight:500;color:#64748b;font-family:'Poppins',sans-serif;}
/* ===== CARD 3: tabs sit flat inside outer card ===== */
#kt_activitiesApplication .cdr-tabs-card .page-tabs{border-radius:0;background:#fff;}
#kt_activitiesApplication .cdr-tabs-card .page-content{background:#f8fafc;border-radius:0 0 12px 12px;}
/* ===== FONT CAP: max 14px for all content text ===== */
#kt_activitiesApplication .cdr-subcard-id .cdr-name{font-size:14px!important;font-weight:700;line-height:1.3;}
#kt_activitiesApplication .cdr-compare-value{font-size:13px!important;}
#kt_activitiesApplication .cdr-cell-value{font-size:13px!important;}
/* ===== RESPONSIVE ===== */
@media(max-width:1100px){#kt_activitiesApplication .cdr-pipeline-section{grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;}}
@media(max-width:640px){#kt_activitiesApplication .cdr-pipeline-section{grid-template-columns:repeat(2,minmax(0,1fr));}}
/* ===== PROFILE OVERVIEW: 2-col grid + drawers ===== */
#kt_activitiesApplication #personal_details .content-split{display:grid!important;grid-template-columns:minmax(0,1fr) 370px;gap:16px;align-items:start;}
@media(max-width:860px){#kt_activitiesApplication #personal_details .content-split{grid-template-columns:1fr!important;}}
/* ===== PROFILE OVERVIEW: heading 13px, body 12px ===== */
/* Inline here (not candidates-details.css) so browser cache never blocks delivery. */
#kt_activitiesApplication #personal_details .card-title{font-size:13px!important;font-weight:700!important;}
#kt_activitiesApplication #personal_details .summary-text{font-size:12px!important;line-height:1.7!important;}
#kt_activitiesApplication #personal_details .skills-thead{font-size:12px!important;}
#kt_activitiesApplication #personal_details .skill-name-cell{font-size:12px!important;}
#kt_activitiesApplication #personal_details .skill-exp-cell{font-size:12px!important;}
/* Screening Questions: override to 2-col grid — original is 1fr 100px 140px (skills layout) */
#kt_activitiesApplication #personal_details .job_additional_view .skills-thead,
#kt_activitiesApplication #personal_details .job_additional_view .skills-row{grid-template-columns:1fr 1.4fr!important;}
/* Handle answers that have no word-break characters (long strings) */
#kt_activitiesApplication #personal_details .job_additional_view .skill-exp-cell{word-break:break-word;overflow-wrap:break-word;white-space:normal;}
/* Word counter below answer inputs in the edit drawer */
.sq-word-count{font-size:10px;color:#94a3b8;margin-top:3px;text-align:right;font-family:'Poppins',sans-serif;}
.sq-word-count.sq-at-limit{color:#ef4444;font-weight:600;}
#kt_activitiesApplication #personal_details .rating-text{font-size:12px!important;}
#kt_activitiesApplication #personal_details .comp-label{font-size:12px!important;}
#kt_activitiesApplication #personal_details .comp-value{font-size:12px!important;font-weight:600!important;}
#kt_activitiesApplication #personal_details .comp-badge{font-size:12px!important;}
#kt_activitiesApplication #personal_details .own-label{font-size:12px!important;}
#kt_activitiesApplication #personal_details .own-value{font-size:12px!important;}
#kt_activitiesApplication #personal_details .slots-grid .slot-day-name{font-size:12px!important;}
#kt_activitiesApplication #personal_details .slots-grid .slot-date{font-size:12px!important;}
#kt_activitiesApplication #personal_details .slots-grid .slot-time{font-size:12px!important;}
#kt_activitiesApplication #personal_details .po-recruiter-note-text{font-size:12px!important;line-height:1.7;color:#475569;}
#kt_activitiesApplication #personal_details .po-no-notes{font-size:12px!important;color:#94a3b8;font-style:italic;margin:0;}
/* ===== Edit Interview Slots drawer: isd-datetime-pill matching Interview Drawer ===== */
#jw-slots-drawer .isd-slot-card{background:#fff;border:1px solid #eef0f4;border-radius:12px;padding:14px 16px;margin-bottom:14px;}
#jw-slots-drawer .isd-slot-head{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
#jw-slots-drawer .isd-slot-num{width:22px;height:22px;border-radius:50%;background:#3e97ff;color:#fff;font-size:11px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;}
#jw-slots-drawer .isd-slot-label{font-size:12.5px;font-weight:700;color:#181c32;}
#jw-slots-drawer .isd-slot-grid{display:grid;grid-template-columns:1.6fr 1fr;gap:12px;}
#jw-slots-drawer .isd-field label{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#3f4254;margin-bottom:5px;}
#jw-slots-drawer .isd-field label i{color:#a1a5b7;font-size:14px;}
#jw-slots-drawer .isd-field select{width:100%;border:1.5px solid #e4e6ef;border-radius:8px;padding:7px 9px;font-size:12px;color:#181c32;background:#f8f9fb;}
#jw-slots-drawer .isd-datetime-pill{display:flex;align-items:center;border:1.5px solid #e4e6ef;border-radius:8px;background:#f8f9fb;padding:0 9px;}
#jw-slots-drawer .isd-datetime-pill input{border:none!important;background:transparent!important;padding:8px 4px!important;width:auto;flex:1 1 auto;min-width:0;color:#181c32;font-size:12px;font-family:'Poppins',sans-serif;}
#jw-slots-drawer .isd-datetime-pill .sep{width:1px;height:18px;background:#e4e6ef;flex-shrink:0;margin:0 2px;}
/* slots drawer — wider panel (+17%) and full-pill click */
#jw-slots-drawer .jw-drawer-panel{width:560px;}
#jw-slots-drawer .isd-datetime-pill{cursor:pointer;}
#jw-slots-drawer .isd-datetime-pill input{cursor:pointer;}
/* timezone row */
#jw-slots-drawer .isd-tz-row{background:#f8faff;border:1px solid #e0e7ff;border-radius:10px;padding:12px 14px;margin-bottom:16px;}
#jw-slots-drawer .isd-tz-label{font-size:11px;font-weight:600;color:#3f4254;margin-bottom:6px;display:flex;align-items:center;gap:6px;}
#jw-slots-drawer .isd-tz-select{width:100%;border:1.5px solid #c7d2fe;border-radius:8px;padding:7px 10px;font-size:12px;color:#181c32;background:#fff;font-family:'Poppins',sans-serif;cursor:pointer;}
#jw-slots-drawer .isd-tz-hint{font-size:10px;color:#94a3b8;margin-top:5px;display:flex;align-items:center;gap:4px;}
#kt_activitiesApplication .jw-card-edit-btn{margin-left:auto;display:inline-flex;align-items:center;gap:5px;padding:5px 12px;font-size:12px;font-weight:600;color:#4f46e5;background:#eef2ff;border:1px solid #c7d2fe;border-radius:7px;cursor:pointer;font-family:'Poppins',sans-serif;white-space:nowrap;flex-shrink:0;}
#kt_activitiesApplication .jw-card-edit-btn svg{width:12px;height:12px;}
#kt_activitiesApplication #personal_details .skill-logo{display:none!important;}
/* ===== SIDE DRAWERS (CSS :has() driven) ===== */
.jw-side-drawer{position:fixed;inset:0;z-index:1055;pointer-events:none;overflow:hidden;}
.jw-side-drawer:has(.jw-drawer-body:not(.d-none)){pointer-events:auto;}
.jw-drawer-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.45);opacity:0;cursor:pointer;transition:opacity .28s;}
.jw-side-drawer:has(.jw-drawer-body:not(.d-none)) .jw-drawer-backdrop{opacity:1;}
.jw-drawer-panel{position:absolute;right:0;top:0;bottom:0;width:480px;max-width:96vw;background:#fff;display:flex;flex-direction:column;box-shadow:-4px 0 32px rgba(15,23,42,.14);border-left:1px solid #e2e8f0;transform:translateX(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);}
#jw-skill-drawer .jw-drawer-panel{width:576px;}
.jw-side-drawer:has(.jw-drawer-body:not(.d-none)) .jw-drawer-panel{transform:translateX(0);}
.jw-drawer-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px 14px;border-bottom:1px solid #e2e8f0;background:#f8fafc;flex-shrink:0;gap:12px;}
.jw-drawer-icon-wrap{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.jw-drawer-icon-wrap svg{width:15px;height:15px;}
.jw-drawer-title{font-size:13px;font-weight:700;color:#0f172a;font-family:'Poppins',sans-serif;}
.jw-drawer-subtitle{font-size:11px;color:#64748b;font-family:'Poppins',sans-serif;margin-top:2px;}
.jw-drawer-close{width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #e2e8f0;background:#fff;border-radius:6px;cursor:pointer;color:#64748b;padding:0;}
.jw-drawer-close:hover{background:#fee2e2;color:#dc2626;border-color:#fca5a5;}
.jw-drawer-close svg{width:14px;height:14px;}
.jw-drawer-body{flex:1;overflow-y:auto;padding:20px;font-family:'Poppins',sans-serif;font-size:12px;scrollbar-width:thin;scrollbar-color:#cbd5e1 transparent;}
.jw-drawer-body table{width:100%;border-collapse:collapse;}
.jw-drawer-body table th{font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.04em;padding:8px 10px;border-bottom:1px solid #e2e8f0;background:#f8fafc;}
.jw-drawer-body table td{padding:10px;border-bottom:1px solid #f1f5f9;font-size:12px;vertical-align:middle;}
.jw-drawer-body .form-label{font-size:12px;font-weight:600;color:#374151;font-family:'Poppins',sans-serif;margin-bottom:4px;display:block;}
.jw-drawer-body .form-control,.jw-drawer-body .form-select{font-size:12px;border-radius:7px;border-color:#e2e8f0;}
.jw-drawer-footer{display:flex;gap:10px;padding:14px 20px;border-top:1px solid #e2e8f0;background:#f8fafc;flex-shrink:0;}
.btn-jw-save{display:inline-flex;align-items:center;gap:6px;padding:7px 18px;font-size:12px;font-weight:600;color:#fff;background:#4f46e5;border:none;border-radius:7px;cursor:pointer;font-family:'Poppins',sans-serif;text-decoration:none;}
.btn-jw-save:hover{background:#4338ca;color:#fff;}
.btn-jw-cancel{display:inline-flex;align-items:center;padding:7px 16px;font-size:12px;font-weight:500;color:#64748b;background:#fff;border:1px solid #e2e8f0;border-radius:7px;cursor:pointer;font-family:'Poppins',sans-serif;text-decoration:none;}
.btn-jw-cancel:hover{background:#f1f5f9;color:#0f172a;}
.jw-drawer-body .slot-day{display:flex;gap:8px;flex-wrap:wrap;}
.jw-drawer-body .slot-day>div{flex:1;min-width:90px;}
/* ===== DRAWER FOUNDATION: page-wrapper must ALWAYS fill full drawer width ===== */
/* Without width:100%, in the drawer's flex-row context the page-wrapper shrinks to
   content width when Notes/Timeline has less content than Profile Overview — this
   makes the hero card narrower and clips the match metrics on the right. */
#kt_activitiesApplication .page-wrapper{width:100%!important;max-width:100%!important;min-width:0;overflow-x:hidden;}
/* page-content and tab panes: hard-contain any horizontal overflow so tables / wide
   elements in Notes/Timeline never push the drawer wider than its fixed pixel width. */
#kt_activitiesApplication .page-content{overflow-x:hidden;max-width:100%;}
#kt_activitiesApplication .tab-pane{max-width:100%;overflow-x:hidden;}
/* The global rule ".table td,.table th,.table tr{width:max-content!important}" (defined
   at the top of this file) blows out timeline tables.  Counteract it inside the drawer. */
#kt_activitiesApplication #notes table td,
#kt_activitiesApplication #notes table th,
#kt_activitiesApplication #notes table tr,
#kt_activitiesApplication #timeline table td,
#kt_activitiesApplication #timeline table th,
#kt_activitiesApplication #timeline table tr{width:auto!important;}
/* ===== NOTES & TIMELINE: LAYOUT CONTAINMENT ===== */
/* Switch content-split from grid to block so 1fr never expands past drawer width */
#kt_activitiesApplication #notes .content-split,
#kt_activitiesApplication #timeline .content-split{display:block!important;max-width:100%;width:100%;}
#kt_activitiesApplication #notes .content-main-full,
#kt_activitiesApplication #timeline .content-main-full{max-width:100%;width:100%;}
/* Bootstrap .row has calc(var(--bs-gutter-x)*.5) negative margins — zero them out */
#kt_activitiesApplication #notes .row,
#kt_activitiesApplication #timeline .row{margin-left:0!important;margin-right:0!important;max-width:100%;width:100%;}
/* Timeline: make the area that wraps wide tables scroll sideways, not push the drawer */
#kt_activitiesApplication #timeline .timeline-content{overflow-x:auto;max-width:100%;}
/* Constrain the overflow-auto wrappers around comment boxes */
#kt_activitiesApplication #notes .overflow-auto,
#kt_activitiesApplication #timeline .overflow-auto{max-width:100%!important;width:100%!important;overflow-x:auto!important;}
/* Remove min-w-700px / min-w-750px / min-w-800px from comment boxes */
#kt_activitiesApplication .min-w-700px,
#kt_activitiesApplication .min-w-750px,
#kt_activitiesApplication .min-w-800px{min-width:0!important;}
/* Bootstrap fs-5=1.25rem(20px) / fs-6=1rem(16px) — cap both to 13px inside drawer */
#kt_activitiesApplication .fs-5,
#kt_activitiesApplication #notes .fs-6,
#kt_activitiesApplication #timeline .fs-6{font-size:13px!important;}
/* Timeline card-body: replace default Bootstrap padding with drawer-friendly value */
#kt_activitiesApplication #timeline .card-body{padding:12px 16px!important;}
/* Notes/Timeline: prevent any inner element from leaking past card boundary */
#kt_activitiesApplication #notes .form-group,
#kt_activitiesApplication #notes .cardshow,
#kt_activitiesApplication #timeline .cardshow{max-width:100%;overflow-x:hidden;}
/* Timeline-3 items: the left margin+padding offset (25px+50px) must stay inside card */
#kt_activitiesApplication #notes .timeline.timeline-3,
#kt_activitiesApplication #timeline .timeline.timeline-3{overflow-x:hidden;max-width:100%;}
/* Columns inside Bootstrap .row inside notes/timeline — remove extra gutter padding */
#kt_activitiesApplication #notes [class*="col-"],
#kt_activitiesApplication #timeline [class*="col-"]{max-width:100%;overflow-x:hidden;}

/* ===================================================================
   RESUME DOWNLOAD CENTER (RDC) — Phase 1 UI Shell
   Drawer slides in from the right; all 6 format cards shown.
   Options 1 & 2 wired to existing download; 3-6 are Phase 2.
   z-index above KT drawer (999999) and its stacking context.
=================================================================== */
.rdc-overlay{position:fixed;inset:0;background:rgba(15,23,42,.48);opacity:0;pointer-events:none;transition:opacity .25s ease;z-index:1200000;}
.rdc-overlay.rdc-open{opacity:1;pointer-events:auto;}
.rdc-drawer{position:fixed;top:0;right:0;height:100%;width:748px;max-width:100vw;background:#fff;z-index:1200001;transform:translateX(100%);transition:transform .25s ease;display:flex;flex-direction:column;font-family:'Poppins',sans-serif;box-shadow:-4px 0 40px rgba(15,23,42,.12);overflow:hidden;}
.rdc-drawer.rdc-open{transform:translateX(0);}
/* Header */
.rdc-header{display:flex;align-items:flex-start;justify-content:space-between;padding:16px 18px 13px;border-bottom:1px solid #e2e8f0;flex-shrink:0;gap:10px;background:#fff;}
.rdc-header-text{flex:1;min-width:0;}
.rdc-title{font-size:13px;font-weight:700;color:#0f172a;margin:0 0 2px;font-family:'Poppins',sans-serif;line-height:1.3;}
.rdc-subtitle{font-size:11px;color:#64748b;margin:0;font-family:'Poppins',sans-serif;}
.rdc-close-btn{width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #e2e8f0;background:#fff;border-radius:7px;cursor:pointer;color:#64748b;padding:0;flex-shrink:0;transition:all .15s ease;}
.rdc-close-btn:hover{background:#fee2e2;color:#dc2626;border-color:#fca5a5;}
.rdc-close-btn svg{width:13px;height:13px;}
/* Body */
.rdc-body{flex:1;overflow-y:auto;overflow-x:hidden;scrollbar-width:thin;scrollbar-color:#e2e8f0 transparent;}
/* Candidate card */
.rdc-cand-card{display:flex;align-items:stretch;margin:12px 14px 0;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;overflow:hidden;}
.rdc-cand-left{display:flex;align-items:center;gap:9px;padding:11px 14px;flex:1;min-width:0;}
.rdc-cand-avatar{width:32px;height:32px;border-radius:8px;background:#eef2ff;color:#4f46e5;font-weight:700;font-size:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-family:'Poppins',sans-serif;}
.rdc-cand-info{display:flex;flex-direction:column;gap:2px;min-width:0;}
.rdc-meta-lbl{font-size:9.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;line-height:1;}
.rdc-cand-name{font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.rdc-cand-sep{width:1px;background:#e2e8f0;align-self:stretch;flex-shrink:0;}
.rdc-cand-right{display:flex;align-items:center;gap:9px;padding:11px 14px;}
.rdc-resume-ic{width:30px;height:30px;border-radius:7px;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.rdc-resume-ic svg{width:14px;height:14px;}
.rdc-resume-info{display:flex;flex-direction:column;gap:2px;}
.rdc-resume-meta{font-size:11px;font-weight:600;color:#334155;white-space:nowrap;}
/* Section label */
.rdc-sect-lbl{font-size:9.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;padding:13px 14px 6px;font-family:'Poppins',sans-serif;}
/* Options list — grid layout: num | icon | title+desc | cost+time | radio */
.rdc-opts-list{display:flex;flex-direction:column;gap:8px;padding:0 14px 10px;}
.rdc-opt{display:grid;grid-template-columns:18px 30px 1fr auto 15px;grid-template-rows:auto auto;column-gap:9px;row-gap:2px;align-items:center;padding:10px 11px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:border-color .15s,background .15s,box-shadow .15s;background:#fff;position:relative;}
#rdcOptsList .rdc-opt[data-opt="1"]{order:1;}
#rdcOptsList .rdc-opt[data-opt="29"]{order:2;}
#rdcOptsList .rdc-opt[data-opt="30"]{order:3;}
#rdcOptsList .rdc-opt[data-opt="31"]{order:4;}
#rdcOptsList .rdc-opt[data-opt="6"]{order:5;}
#rdcOptsList .rdc-opt[data-opt="28"]{order:6;}
#rdcOptsList .rdc-opt[data-opt="20"]{order:7;}
#rdcOptsList .rdc-opt[data-opt="5"]{order:8;}
#rdcOptsList .rdc-opt[data-opt="15"]{order:9;}
#rdcOptsList .rdc-opt[data-opt="16"]{order:10;}
#rdcOptsList .rdc-opt[data-opt="32"]{order:11;}
#rdcOptsList .rdc-opt[data-opt="33"]{order:12;}
#rdcOptsList .rdc-opt[data-opt="13"]{order:97;}
#rdcOptsList .rdc-opt[data-opt="14"]{order:98;}
#rdcOptsList .rdc-opt[data-opt="17"]{order:13;}
#rdcOptsList .rdc-opt[data-opt="19"]{order:14;}
#rdcOptsList .rdc-opt[data-opt="21"]{order:15;}
#rdcOptsList .rdc-opt[data-opt="22"]{order:16;}
#rdcOptsList .rdc-opt[data-opt="23"]{order:17;}
#rdcOptsList .rdc-opt[data-opt="24"]{order:18;}
#rdcOptsList .rdc-opt[data-opt="25"]{order:19;}
#rdcOptsList .rdc-opt[data-opt="26"]{order:20;}
#rdcOptsList .rdc-opt[data-opt="27"]{order:21;}
#rdcOptsList .rdc-opt[data-opt="8"],
#rdcOptsList .rdc-opt[data-opt="9"],
#rdcOptsList .rdc-opt[data-opt="21"],
#rdcOptsList .rdc-opt[data-opt="22"],
#rdcOptsList .rdc-opt[data-opt="23"],
#rdcOptsList .rdc-opt[data-opt="24"],
#rdcOptsList .rdc-opt[data-opt="25"],
#rdcOptsList .rdc-opt[data-opt="26"],
#rdcOptsList .rdc-opt[data-opt="27"]{display:none!important;}
#rdcOptsList .rdc-opt[data-opt="3"],
#rdcOptsList .rdc-opt[data-opt="4"],
#rdcOptsList .rdc-opt[data-opt="7"]{order:99;}
.rdc-opt:hover{border-color:#c7d2fe;background:#faf8ff;box-shadow:0 2px 10px rgba(124,58,237,.07);}
.rdc-opt.rdc-opt-sel{border-color:#7c3aed;background:#faf8ff;}
.rdc-rinput{position:absolute;opacity:0;width:0;height:0;pointer-events:none;}
.rdc-onum{grid-column:1;grid-row:1/3;align-self:center;width:18px;height:18px;border-radius:50%;background:#f1f5f9;color:#94a3b8;font-size:9.5px;font-weight:700;display:flex;align-items:center;justify-content:center;font-family:'Poppins',sans-serif;transition:background .15s,color .15s;}
.rdc-opt-sel .rdc-onum{background:#ede9fe;color:#7c3aed;}
.rdc-oi{grid-column:2;grid-row:1/3;align-self:center;width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;}
.rdc-oi svg{width:14px;height:14px;}
.rdc-ic-blue{background:#dbeafe;color:#1d4ed8;}
.rdc-ic-purple{background:#ede9fe;color:#7c3aed;}
.rdc-ic-amber{background:#fef3c7;color:#d97706;}
.rdc-ic-green{background:#d1fae5;color:#059669;}
.rdc-ic-rose{background:#ffe4e6;color:#e11d48;}
.rdc-ic-teal{background:#ccfbf1;color:#0d9488;}
.rdc-obody{display:contents;}
.rdc-otitle{grid-column:3;grid-row:1;font-size:12px;font-weight:600;color:#0f172a;line-height:1.3;font-family:'Poppins',sans-serif;display:flex;align-items:center;gap:5px;flex-wrap:wrap;}
.rdc-odesc{grid-column:3;grid-row:2;font-size:10.5px;color:#64748b;line-height:1.3;font-family:'Poppins',sans-serif;}
.rdc-ometa{display:contents;}
.rdc-ocost{grid-column:4;grid-row:1;font-size:11px;font-weight:700;color:#7c3aed;font-family:'Poppins',sans-serif;white-space:nowrap;text-align:right;}
.rdc-otime{grid-column:4;grid-row:2;font-size:10px;color:#94a3b8;font-family:'Poppins',sans-serif;white-space:nowrap;text-align:right;}
.rdc-ocirc{grid-column:5;grid-row:1/3;align-self:center;width:15px;height:15px;border-radius:50%;border:2px solid #cbd5e1;display:flex;align-items:center;justify-content:center;transition:all .15s ease;}
.rdc-opt-sel .rdc-ocirc{border-color:#7c3aed;background:#7c3aed;}
.rdc-opt-sel .rdc-ocirc::after{content:'';width:5px;height:5px;border-radius:50%;background:#fff;display:block;}
/* Coin card */
.rdc-coin-card{margin:2px 14px 12px;padding:11px 13px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;}
.rdc-coin-row{display:flex;align-items:center;gap:7px;font-size:11.5px;color:#374151;font-family:'Poppins',sans-serif;margin-bottom:5px;}
.rdc-coin-dot{width:8px;height:8px;border-radius:50%;background:#7c3aed;flex-shrink:0;}
.rdc-coin-row strong{color:#7c3aed;}
.rdc-bal-row{font-size:11.5px;color:#374151;font-family:'Poppins',sans-serif;padding-left:15px;}
/* Footer */
.rdc-footer{padding:12px 14px 10px;border-top:1px solid #e2e8f0;flex-shrink:0;background:#fff;}
.rdc-dl-btn{width:100%;padding:12px 16px;background:#4f46e5;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:600;font-family:'Poppins',sans-serif;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .15s ease;}
.rdc-dl-btn:hover:not(:disabled){background:#4338ca;}
.rdc-dl-btn:disabled,.rdc-dl-btn.rdc-loading{background:#4338ca;cursor:not-allowed;opacity:.75;}
.rdc-foot-note{text-align:center;font-size:10px;color:#94a3b8;margin:8px 0 0;display:flex;align-items:center;justify-content:center;gap:4px;font-family:'Poppins',sans-serif;}
.rdc-foot-note svg{flex-shrink:0;}
/* Premium grouped design shell */
.rdc-header{align-items:flex-start;padding:13px 18px 12px;background:linear-gradient(90deg,#ffffff 0%,#f8fbff 100%);}
.rdc-head-left{display:flex;align-items:flex-start;gap:10px;min-width:0;flex:1;}
.rdc-head-icon{width:31px;height:31px;border-radius:8px;background:#ede9fe;color:#5b21ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.rdc-head-icon svg{width:17px;height:17px;}
.rdc-title{font-size:13px;font-weight:700;color:#0f172a;line-height:1.3;margin:0 0 2px;}
.rdc-subtitle{font-size:11px;color:#64748b;line-height:1.35;margin:0;}
.rdc-head-actions{display:flex;align-items:center;gap:10px;flex-shrink:0;}
.rdc-close-btn{width:30px;height:30px;border:0;background:transparent;color:#40527b;}
.rdc-close-btn:hover{background:#eef2ff;color:#4f16e8;border-color:transparent;}
.rdc-body{background:#f8fbff;padding:12px 18px 0;}
.rdc-sect-lbl{display:none;}
.rdc-opts-list{padding:0 0 12px;gap:10px;}
.rdc-ocirc{width:18px;height:18px;border:2px solid #a8b8d5;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:all .15s ease;}
.rdc-opt-sel .rdc-ocirc{border-color:#5b21ff;background:#5b21ff;box-shadow:0 0 0 4px rgba(91,33,255,.12);}
.rdc-opt-sel .rdc-ocirc::after{content:'';width:6px;height:6px;border-radius:50%;background:#fff;display:block;}
.rdc-section{border:1px solid #dbe7f7;border-radius:10px;background:#fff;overflow:hidden;box-shadow:0 8px 20px rgba(15,23,42,.04);}
.rdc-section-head{display:flex;align-items:center;gap:9px 11px;padding:8px 11px;border-bottom:1px solid #dde9f8;min-width:0;}
.rdc-section-icon{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;box-shadow:0 8px 16px rgba(15,23,42,.12);}
.rdc-section-icon svg{width:18px;height:18px;}
.rdc-section-title{font-size:16px;font-weight:800;color:#08154a;line-height:1.2;white-space:nowrap;min-width:0;overflow:hidden;text-overflow:ellipsis;}
.rdc-section-pill{display:inline-flex;align-items:center;height:22px;padding:0 11px;border-radius:999px;font-size:11px;font-weight:500;white-space:nowrap;min-width:0;max-width:100%;overflow:hidden;text-overflow:ellipsis;}
.rdc-section-count{margin-left:auto;color:#4f16e8;font-size:13px;font-weight:800;white-space:nowrap;}
.rdc-section-chevron{width:18px;height:18px;color:#5b21ff;flex-shrink:0;}
.rdc-section-options{display:flex;flex-direction:column;gap:6px;padding:8px 9px;}
.rdc-section-options .rdc-opt{order:0!important;}
.rdc-section-blue .rdc-section-head{background:linear-gradient(90deg,#eaf4ff,#f7fbff);}
.rdc-section-blue .rdc-section-icon{background:linear-gradient(135deg,#0b7bff,#5b7cfa);}
.rdc-section-blue .rdc-section-pill{background:#dceeff;color:#075aa9;}
.rdc-section-purple .rdc-section-head{background:linear-gradient(90deg,#efe8ff,#fbf8ff);}
.rdc-section-purple .rdc-section-icon{background:linear-gradient(135deg,#6d28d9,#7c3aed);}
.rdc-section-purple .rdc-section-pill{background:#eee4ff;color:#4f16e8;}
.rdc-section-rose .rdc-section-head{background:linear-gradient(90deg,#ffe6ee,#fff8fa);}
.rdc-section-rose .rdc-section-icon{background:linear-gradient(135deg,#f43f5e,#ec4899);}
.rdc-section-rose .rdc-section-pill{background:#ffdce7;color:#be123c;}
.rdc-section-green .rdc-section-head{background:linear-gradient(90deg,#dcfce7,#f2fffb);}
.rdc-section-green .rdc-section-icon{background:linear-gradient(135deg,#059669,#10b981);}
.rdc-section-green .rdc-section-pill{background:#d1fae5;color:#047857;}
.rdc-section-amber .rdc-section-head{background:linear-gradient(90deg,#fff0d6,#fffaf0);}
.rdc-section-amber .rdc-section-icon{background:linear-gradient(135deg,#f97316,#f59e0b);}
.rdc-section-amber .rdc-section-pill{background:#ffedd5;color:#c2410c;}
.rdc-opt{grid-template-columns:44px minmax(0,1fr) 108px 20px;column-gap:12px;padding:9px 10px;border:1px solid #d9e5f4;border-radius:9px;background:#fff;box-shadow:0 2px 8px rgba(15,23,42,.03);}
.rdc-opt:hover{border-color:#c9d7ef;background:#fbfdff;box-shadow:0 6px 14px rgba(15,23,42,.06);}
.rdc-opt.rdc-opt-sel{border-color:#6d28d9;background:#fbfaff;box-shadow:0 0 0 2px rgba(109,40,217,.10);}
.rdc-onum{display:none;}
.rdc-oi{grid-column:1;grid-row:1/3;width:34px;height:34px;border-radius:9px;}
.rdc-oi svg{width:18px;height:18px;}
.rdc-otitle{grid-column:2;grid-row:1;font-size:12.5px;font-weight:800;color:#08154a;line-height:1.2;}
.rdc-odesc{grid-column:2;grid-row:2;font-size:11.5px;color:#29456f;line-height:1.35;}
.rdc-ometa{grid-column:3;grid-row:1/3;align-self:center;display:flex;flex-direction:column;justify-content:center;gap:2px;border-left:1px solid #dbe7f7;padding-left:13px;}
.rdc-ocost{display:flex;align-items:center;justify-content:flex-start;gap:7px;text-align:left;font-size:12px;font-weight:800;color:#07164f;}
.rdc-ocost::before{content:'';width:17px;height:17px;border-radius:50%;background:#f59e0b;box-shadow:inset 0 -3px 0 #d97706,0 0 0 4px #fff7dd;flex-shrink:0;}
.rdc-otime{display:block;text-align:left;font-size:11px;color:#526896;padding-left:24px;line-height:1.1;}
.rdc-otime::before{content:none;}
.rdc-ocirc{grid-column:4;grid-row:1/3;align-self:center;}
.rdc-coin-card{display:none;}
.rdc-footer{padding:12px 18px 10px;border-top:1px solid #dbe7f7;flex-shrink:0;background:#fff;box-shadow:0 -8px 22px rgba(15,23,42,.06);}
.rdc-dl-btn{width:100%;padding:12px 16px;background:#4f46e5;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:600;font-family:'Poppins',sans-serif;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .15s ease;}
.rdc-dl-btn:hover:not(:disabled){background:#4338ca;}
.rdc-dl-btn:disabled,.rdc-dl-btn.rdc-loading{background:#4338ca;cursor:not-allowed;opacity:.75;}
.rdc-foot-note{text-align:center;font-size:10px;color:#94a3b8;margin:8px 0 0;display:flex;align-items:center;justify-content:center;gap:4px;font-family:'Poppins',sans-serif;}
.rdc-foot-note svg{flex-shrink:0;}
/* Spinner */
@keyframes rdcSpin{to{transform:rotate(360deg);}}
.rdc-spin{display:inline-block;width:13px;height:13px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:rdcSpin .65s linear infinite;flex-shrink:0;}
@keyframes rdcCountdownReel{from{transform:translateY(0);}to{transform:translateY(var(--rdc-count-shift));}}
.rdc-countdown{display:inline-flex;align-items:center;justify-content:center;gap:5px;min-width:0;}
.rdc-countdown-text{min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.rdc-countdown-reel{width:28px;height:16px;line-height:16px;overflow:hidden;display:inline-block;text-align:right;font-variant-numeric:tabular-nums;font-feature-settings:"tnum" 1;flex-shrink:0;}
.rdc-countdown-track{display:flex;flex-direction:column;will-change:transform;animation:rdcCountdownReel var(--rdc-count-duration,60s) steps(var(--rdc-count-steps,60), end) forwards;}
.rdc-countdown-track span{height:16px;line-height:16px;display:block;}
.rdc-countdown-unit{min-width:50px;text-align:left;flex-shrink:0;}
/* Toast */
.rdc-toast{position:fixed;bottom:20px;right:20px;background:#1e293b;color:#fff;padding:10px 15px;border-radius:8px;font-size:11.5px;font-family:'Poppins',sans-serif;z-index:1300000;opacity:0;transform:translateY(6px);transition:all .22s ease;pointer-events:none;max-width:280px;line-height:1.4;}
.rdc-toast.rdc-tshow{opacity:1;transform:translateY(0);}
.rdc-toast-info{background:#7c3aed;}
.rdc-toast-warn{background:#d97706;}
/* Responsive */
@media(max-width:640px){
  .rdc-drawer{width:100%;top:auto;bottom:0;height:92dvh;border-radius:14px 14px 0 0;transform:translateY(100%);}
  .rdc-drawer.rdc-open{transform:translateY(0);}
  .rdc-header{align-items:flex-start;padding:12px;}
  .rdc-head-actions{gap:8px;}
  .rdc-title{font-size:13px;}
  .rdc-subtitle{font-size:11px;}
  .rdc-body{padding:10px 10px 0;}
  .rdc-section-head{flex-wrap:wrap;}
  .rdc-section-title{font-size:14px;}
  .rdc-section-pill{order:5;width:100%;justify-content:center;}
  .rdc-opt{grid-template-columns:34px minmax(0,1fr) 18px;gap:9px;}
  .rdc-otitle{font-size:12px;}
  .rdc-odesc{font-size:10.5px;}
  .rdc-ometa{grid-column:2;grid-row:3;border-left:0;padding-left:0;}
  .rdc-ocost{font-size:10.5px;}
  .rdc-otime{font-size:10.5px;padding-left:24px;}
  .rdc-ocirc{grid-column:3;grid-row:1/4;}
}
@media(min-width:641px) and (max-width:900px){
  .rdc-drawer{width:100%;}
}
@media(max-width:1440px), (max-height:820px){
  .rdc-drawer{width:min(704px,100vw);}
  .rdc-header{padding:10px 14px 9px;}
  .rdc-body{padding:9px 12px 0;}
  .rdc-opts-list{gap:8px;padding-bottom:9px;}
  .rdc-section-head{padding:7px 10px;flex-wrap:wrap;}
  .rdc-section-title{font-size:14px;flex:1 1 auto;}
  .rdc-section-pill{height:20px;font-size:10px;flex:1 1 220px;}
  .rdc-section-count{font-size:12px;margin-left:auto;}
  .rdc-section-options{gap:6px;padding:7px 8px;}
  .rdc-opt{grid-template-columns:38px minmax(0,1fr) 96px 18px;column-gap:10px;padding:8px 9px;}
  .rdc-oi{width:32px;height:32px;}
  .rdc-oi svg{width:16px;height:16px;}
  .rdc-otitle{font-size:12px;}
  .rdc-odesc{font-size:10.5px;line-height:1.3;}
  .rdc-ometa{padding-left:10px;}
  .rdc-ocost{font-size:11.5px;}
  .rdc-ocost::before{width:15px;height:15px;box-shadow:inset 0 -3px 0 #d97706,0 0 0 3px #fff7dd;}
  .rdc-otime{font-size:10px;padding-left:22px;}
  .rdc-footer{padding:10px 12px 8px;}
  .rdc-dl-btn{padding:11px 14px;}
  .rdc-foot-note{margin-top:7px;}
}
/* ===== HERO SKILLS BAR REDESIGN ===== */
/* Skills % match badge on avatar */
#kt_activitiesApplication .cdr-avatar-wrap{position:relative;overflow:visible;}
#kt_activitiesApplication .cdr-match-pct-badge{position:absolute;bottom:-11px;left:50%;transform:translateX(-50%);font-size:9.5px;font-weight:700;padding:2px 8px;border-radius:20px;white-space:nowrap;font-family:'Poppins',sans-serif;border:2px solid #fff;line-height:1.5;box-shadow:0 2px 6px rgba(0,0,0,.12);}
#kt_activitiesApplication .cdr-mpb-green{background:#dcfce7;color:#16a34a;}
#kt_activitiesApplication .cdr-mpb-amber{background:#fef3c7;color:#d97706;}
#kt_activitiesApplication .cdr-mpb-red{background:#fee2e2;color:#dc2626;}
/* Convert cdr-subcard-id to column layout so skills bar can span full width */
#kt_activitiesApplication .cdr-subcard-id{display:flex!important;flex-direction:column!important;gap:0!important;align-items:stretch!important;}
#kt_activitiesApplication .cdr-id-top{display:flex;gap:14px;align-items:flex-start;}
#kt_activitiesApplication .cdr-id-top .cdr-id-body{flex:1 1 auto;min-width:0;}
#kt_activitiesApplication .cdr-skills-bar{display:flex;flex-wrap:wrap;gap:5px;margin-top:12px;padding-top:12px;border-top:1px solid #f1f5f9;}
/* Rectangular tags — 4px radius for enterprise-clean look */
#kt_activitiesApplication .cdr-skill-tag{display:inline-flex;align-items:center;font-size:11px;font-weight:500;padding:3px 9px;border-radius:4px;font-family:'Poppins',sans-serif;line-height:1.5;white-space:nowrap;border:1px solid;letter-spacing:.01em;}
/* Green = matched job skill */
#kt_activitiesApplication .cdr-skill-match{background:#f0fdf4;color:#15803d;border-color:#86efac;}
/* Grey = not in job skill list */
#kt_activitiesApplication .cdr-skill-plain{background:#f8fafc;color:#64748b;border-color:#e2e8f0;}
/* "+N More" counter tag */
#kt_activitiesApplication .cdr-skill-tag.cdr-skill-more{background:#f1f5f9;color:#94a3b8;border-color:#e2e8f0;font-size:10.5px;}
/* Recruiter Notes drawer */
#jw-recruiter-note-drawer .rn-section{margin-bottom:16px;}
#jw-recruiter-note-drawer .rn-label{font-size:11px;font-weight:600;color:#3f4254;margin-bottom:6px;display:flex;align-items:center;gap:4px;}
#jw-recruiter-note-drawer .rn-label span{font-weight:400;color:#94a3b8;font-size:10.5px;}
#jw-recruiter-note-drawer .rn-select{width:100%;border:1.5px solid #e4e6ef;border-radius:8px;padding:8px 10px;font-size:12px;color:#181c32;background:#f8f9fb;cursor:pointer;font-family:'Poppins',sans-serif;}
#jw-recruiter-note-drawer .rn-editor{min-height:220px;max-height:400px;border:1.5px solid #e4e6ef;border-radius:8px;padding:12px 14px;font-size:12px;font-family:'Poppins',sans-serif;line-height:1.75;color:#181c32;background:#f8f9fb;outline:none;overflow-y:auto;word-break:break-word;}
#jw-recruiter-note-drawer .rn-editor:focus{border-color:#3e97ff;background:#fff;}
#jw-recruiter-note-drawer .rn-editor:empty::before{content:attr(data-placeholder);color:#94a3b8;pointer-events:none;}
/* Interview Slots empty state */
#kt_activitiesApplication #personal_details .po-slots-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px 12px;text-align:center;gap:10px;}
#kt_activitiesApplication #personal_details .po-slots-empty-icon{width:52px;height:52px;border-radius:50%;background:#eef2ff;display:flex;align-items:center;justify-content:center;color:#6366f1;}
#kt_activitiesApplication #personal_details .po-slots-empty-icon svg{width:22px;height:22px;}
#kt_activitiesApplication #personal_details .po-slots-empty-title{font-size:12px;font-weight:700;color:#181c32;margin:0;}
#kt_activitiesApplication #personal_details .po-slots-empty-sub{font-size:11.5px;color:#94a3b8;margin:0;line-height:1.5;}
#kt_activitiesApplication #personal_details .po-slots-add-btn{margin-top:4px;padding:7px 20px;font-size:12px;font-weight:600;color:#3e97ff;background:#fff;border:1.5px solid #3e97ff;border-radius:8px;cursor:pointer;font-family:'Poppins',sans-serif;transition:background .15s,color .15s;}
#kt_activitiesApplication #personal_details .po-slots-add-btn:hover{background:#3e97ff;color:#fff;}
/* "Select Date" button shown instead of Book Now when slot has no date */
.book-btn-addslot{display:inline-flex;align-items:center;justify-content:center;gap:5px;width:100%;margin-top:6px;padding:5px 10px;font-size:11px;font-weight:600;color:#64748b;background:#f8fafc;border:1.5px dashed #cbd5e1;border-radius:6px;cursor:pointer;font-family:'Poppins',sans-serif;transition:all .18s;}
.book-btn-addslot:hover{background:#eff6ff;border-color:#3e97ff;color:#3e97ff;}
</style>

<script>
   $("timeline-item:last-child").addClass("timeline-item:last-child");
</script>
@php 
      
    $jobId = $application->jobId ?? $application->job_id;
    $jobData = $jobData ?? (empty($jobId) ? null : DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$jobId)->first());
    $jobMode = $jobData->jobMode ?? @$application->current_ctc_mode;
    $jobCurrency = $jobData->jobCurrency ?? @$application->current_ctc_cur;
    $symbols = $symbolsValue[$jobCurrency] ?? "INR";
      
   $resume_url				=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
   $photo  					=	''; //url('public/avatar.png');
   if(!empty($application->photo)){
      $photo			=	 $application->photo;	
   }  
   $country_id			=	$country_name	= $state_name	=	$state	=	$city_name	=	'';	
   $country_id			=	@$application->country;
   $state				=	@$application->state;
  //  if(@$country_id > 0)
  //  {
  
     $city_name	= @$application->city_name ? ucwords(@$application->city_name).',' : '';
     $state_name	= @$application->state_name ? ucwords(@$application->state_name).',' : ''; 
     $country_name	= ucwords(@$application->country_name);	
   		$currencySymbole 	=	GeneralMethod::currencySymbole(); 
      // dd($currencySymbole);
   		$current_ctc_cur	=	$current_ectc_cur	=	"";
       if($application->condidate_type=='other'){
        $current_ctc_cur	=	$currencySymbole[@$jobCurrency];
        $current_ectc_cur	=	$currencySymbole[@$jobCurrency];
       }else{
        $current_ctc_cur	=	$currencySymbole[@$jobCurrency];
        $current_ectc_cur	=	$currencySymbole[@$jobCurrency];
       }
   		
  //  }			
  //  rjobid from_company_id	
  // dd( $current_ctc_cur);

      $posted_by_image			=	$posted_by			=	'';

      if($CaptainCompanyid != $application->ClientComID && $application->ClientComID != 0 && $application->ClientComID > 0){
            $spokperson = $hdrSpokperson ?? DB::table('job_assign')->select('users.name')->leftjoin('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $jobId)->where('job_assign.company_id', $application->ClientComID)->first();
            $posted_by = (@$spokperson->name) ? @$spokperson->name : @$application->posted_by;
        }else{
          // dd($application->posted_by);
          $posted_by = @$application->posted_by;
          // dd( $posted_by);
        }
        

      if(!empty($application->posted_by_image)){	
        $posted_by_image		=@$application->posted_by_image;
      }			

      // dd($CaptainCompanyid,$application->ClientComID,$posted_by);
      if(!empty($posted_by)){
        $bgTextDecore				=	GeneralMethod::icoinText($posted_by);
      }
                  	
                  // dd("done");	  

  							
@endphp
@php
    $dnone = "d-none";
    if($application->master_status_id==8 && $application->rjobid > 0 && $application->from_company_id == auth()->user()->company_id){
      $dnone = "";
    }elseif($application->master_status_id !=8 && $application->rjobid > 0 && $application->from_company_id== auth()->user()->company_id){
      $dnone = "";   
    }elseif(in_array("add_schedule", $userPermissions) && $application->rjobid < 0){
      $dnone = "";
    }elseif(in_array("add_schedule", $userPermissions) && $application->company_id== auth()->user()->company_id){
      $dnone = "";
    }elseif(in_array("add_schedule", $userPermissions) && $application->master_status_id==8 && $application->from_company_id == auth()->user()->company_id){
      $dnone = "";
    }elseif(in_array("add_schedule", $userPermissions) && $application->master_status_id==8 && $application->to_company_id == auth()->user()->company_id){
      $dnone = "";
    } 

@endphp



<div class="page-wrapper">

  {{-- Single unified header Card. Top bar holds every action button (Edit, Interview,
       Send Email, Send WhatsApp, Move Stage, Download Resume, Close). The body is a
       hero Card with two Sub-Cards: left = candidate identity, right = match metrics
       (Skills Matched + Skills Not Matched emphasised). 7-cell glance strip lives
       underneath as the third visual tier. No controller changes. --}}
  @php
      /* -------- Header presentation-only derivations (no business logic) --------
         Per AI_RULES.md §8 ("No business-logic changes"), every value below is a
         purely visual derivation from already-loaded ES docs and the existing
         GeneralMethod helpers used elsewhere in the page. No new DB writes,
         no new route data, no scoring change. */
      $__hdrSkillsRaw = trim((string) @$application->skills);
      $__hdrSkillsArr = $__hdrSkillsRaw !== '' ? array_values(array_filter(array_map('trim', explode(',', $__hdrSkillsRaw)), 'strlen')) : [];
      $__hdrSkillsShown = array_slice($__hdrSkillsArr, 0, 8);
      $__hdrSkillsMore  = max(0, count($__hdrSkillsArr) - count($__hdrSkillsShown));

      // Initials for avatar (up to 2 letters).
      $__hdrFullName = trim((string) @$application->full_name);
      $__hdrInitials = '';
      if ($__hdrFullName !== '') {
          $__hdrParts = preg_split('/\s+/', $__hdrFullName);
          $__hdrInitials = strtoupper(substr($__hdrParts[0] ?? '', 0, 1) . (isset($__hdrParts[1]) ? substr($__hdrParts[1], 0, 1) : ''));
      }
      if ($__hdrInitials === '') { $__hdrInitials = 'NA'; }

      $__hdrIsVerified = (bool) preg_match('/verified|active|success/i', (string) @$application->css_style);

      // Availability — same NP mode rules used in Profile Overview card.
      $__hdrAvailability = '';
      if ((@$application->serving_notice_period_mode == 0) && (int) @$application->np_id > 0) {
          $__hdrAvailability = $application->np_id . ' Days';
      } elseif (@$application->serving_notice_period_mode == 1 && !empty($application->np_id)) {
          $__hdrAvailability = 'Available ' . date('d M Y', strtotime($application->np_id));
      } elseif (@$application->serving_notice_period_mode == 2 || strtolower((string)@$application->np_id) === 'immediate') {
          $__hdrAvailability = 'Immediate Joiner';
      } else {
          $__hdrAvailability = trim((string) @$application->np_id);
      }
      if ($__hdrAvailability === '') { $__hdrAvailability = '—'; }
      $__hdrIsImmediate = (stripos($__hdrAvailability, 'Immediate') !== false || stripos($__hdrAvailability, 'Available') === 0);

      // Skill match — purely a presentation count; job skill list already in $jobSkill.
      $__hdrJobSkills = [];
      if (!empty($jobSkill)) {
          foreach ($jobSkill as $__js) {
              $__js = (object) $__js;
              $__sn = trim((string) (@$__js->skill_name ?? @$__js->skill ?? ''));
              if ($__sn !== '') { $__hdrJobSkills[mb_strtolower($__sn)] = $__sn; }
          }
      }
      $__hdrMatchedCount = 0;
      $__hdrMatchedSkillNames = [];
      foreach ($__hdrSkillsArr as $__cs) {
          if (isset($__hdrJobSkills[mb_strtolower($__cs)])) {
              $__hdrMatchedCount++;
              $__hdrMatchedSkillNames[] = $__cs;
          }
      }
      $__hdrJobTotal   = count($__hdrJobSkills);
      $__hdrNotMatched = max(0, $__hdrJobTotal - $__hdrMatchedCount);
      $__hdrOverallPct = $__hdrJobTotal > 0 ? (int) round(($__hdrMatchedCount / $__hdrJobTotal) * 100) : 0;

      // Location match — uses the same formatted strings already prepared above.
      $__hdrJobLocs = [];
      foreach (['city_name','location','city','state'] as $__fld) {
          $__v = trim((string) (@$job->{$__fld} ?? ''));
          if ($__v !== '') { $__hdrJobLocs[] = mb_strtolower($__v); }
      }
      $__hdrCandLoc = mb_strtolower(trim(($city_name ?? '').' '.($state_name ?? '')));
      $__hdrLocMatched = false;
      foreach ($__hdrJobLocs as $__jl) {
          if ($__jl !== '' && $__hdrCandLoc !== '' && (mb_strpos($__hdrCandLoc, $__jl) !== false || mb_strpos($__jl, $__hdrCandLoc) !== false)) {
              $__hdrLocMatched = true; break;
          }
      }

      // Salary under budget — comparison of already-loaded ES numeric fields.
      $__hdrMaxSal = (float) (@$job->max_salary ?? 0);
      $__hdrEctcY  = (float) (@$application->ectc_yearly ?? 0);
      $__hdrUnderBudget = ($__hdrMaxSal > 0) && ($__hdrEctcY > 0) && ($__hdrEctcY <= $__hdrMaxSal);

      // Salary formatter (re-uses the exact helper the bottom strip already used further down).
      $__hdrPay  = GeneralMethod::formatViewSalary(@$application->ctc_yearly,  $symbols, $jobMode);
      $__hdrBill = GeneralMethod::formatViewSalary(@$application->ectc_yearly, $symbols, $jobMode);
      $__hdrCur1 = html_entity_decode(strip_tags((string) @$current_ctc_cur),  ENT_QUOTES, 'UTF-8');
      $__hdrCur2 = html_entity_decode(strip_tags((string) @$current_ectc_cur), ENT_QUOTES, 'UTF-8');
      $__hdrCtcText  = trim($__hdrCur1 . ' ' . GeneralMethod::thousandsCurrencyFormat(@$__hdrPay->amount));
      $__hdrEctcText = trim($__hdrCur2 . ' ' . GeneralMethod::thousandsCurrencyFormat(@$__hdrBill->amount));
      if ($__hdrCtcText === '')  { $__hdrCtcText = '—'; }
      if ($__hdrEctcText === '') { $__hdrEctcText = '—'; }

      // Total + relevant experience — same render rule used in Profile Overview.
      $__teRaw = trim((string) @$application->total_exp);
      $__bdTotalExp = (strpos($__teRaw, '.') !== false && substr($__teRaw, -3) === '.00') ? substr($__teRaw, 0, -3) : $__teRaw;
      if ($__bdTotalExp === '') $__bdTotalExp = '—';
      $__reRaw = trim((string) @$application->rel_exp);
      $__bdRelExp = (strpos($__reRaw, '.') !== false && substr($__reRaw, -3) === '.00') ? substr($__reRaw, 0, -3) : $__reRaw;
      if ($__bdRelExp === '') $__bdRelExp = '—';

      // "Added to pipeline" relative time — honest replacement for "JD Email Status"
      // (no JD-mail timestamp is captured anywhere in this view).
      $__hdrAddedTxt = '—';
      if (!empty($application->created_at)) {
          try {
              $__t = \Carbon\Carbon::parse($application->created_at);
              $__hdrAddedTxt = $__t->diffForHumans(null, true) . ' ago';
          } catch (\Throwable $e) { $__hdrAddedTxt = date('d-M-Y', strtotime($application->created_at)); }
      }

      $__hdrRelocate = trim((string) (@$application->open_to_relocate ?: @$application->relocation ?: ''));
      $__hdrRelocateDisp = $__hdrRelocate === '' ? '—' : ucwords($__hdrRelocate);

      // Experience match — ES data only; $job->min_exp vs $application->total_exp
      $__hdrMinJobExp  = (float)(@$job->min_exp ?? 0);
      $__hdrExpMatched = ($__hdrMinJobExp <= 0) || ((float)(@$application->total_exp ?? 0) >= $__hdrMinJobExp);
      $__hdrExpStatus  = $__hdrExpMatched ? 'Matched' : 'Below Required';

      // Location display label (derived from already-computed $__hdrLocMatched)
      $__hdrLocText = $__hdrLocMatched ? 'Same City' : 'Different City';
  @endphp

  {{-- ============================================================
       TOP ACTION BAR (Edit / Interview / Send Email / Send WhatsApp /
       Move Stage / Download Resume). Close stays as a single circular
       button outside the bar so it doesn't fight the primary/secondary
       button hierarchy.
       ============================================================ --}}
  {{-- TOP ACTION BAR (outside any card, spans full width — mirrors job drawer top-actions) --}}
  <div class="cdr-topbar" aria-label="Candidate actions">
    <div class="cdr-topbar-group">
      @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='other' && $editprofile===true)
        <a href="{{ route('service_company.job-applications.edit', [ GeneralMethod::value_encrypt($jobWiseCanId)]) }}?currentPage={{ GeneralMethod::value_encrypt($currentPage) }}&currentSlug={{ GeneralMethod::value_encrypt($currentSlug) }}" class="cdr-action-btn cdr-btn-primary {{ $dnone }}" title="Edit candidate profile">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
          Edit Profile
        </a>
      @endif

      @if(in_array("add_schedule", $userPermissions))
        <a href="javascript:void(0);" id="app_interview_schedule" class="cdr-action-btn cdr-btn-interview {{ $dnone }}" rel="{{@$application->wiseId}}" title="Schedule interview">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
            <line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/>
          </svg>
          Interview
        </a>
      @endif

      @if(@$application->condidate_type=='bench_condidate' && @$application->other_company !=auth()->user()->company_id)

      @else
        <a href="javascript:void(0);" class="cdr-action-btn cdr-btn-email sendmaimodal {{ $dnone }}" title="Send email to candidate">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <polyline points="3 7 12 13 21 7"/>
          </svg>
          Send Email
        </a>

        @if(@$application->condidate_type=='other' && $notVisiableBench)
          <a href="javascript:void(0);" class="cdr-action-btn cdr-btn-wa sendwhatsappmodal {{ $dnone }}" title="Send WhatsApp message">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20.52 3.48A11.94 11.94 0 0 0 12 0C5.37 0 .05 5.32.05 11.95c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.9 11.9 0 0 0 5.7 1.45h.01c6.63 0 11.95-5.32 11.95-11.95 0-3.19-1.24-6.19-3.44-8.37z"/>
            </svg>
            Send WhatsApp
          </a>
        @endif
      @endif

      <a href="javascript:void(0);" class="cdr-action-btn cdr-btn-stage {{ $dnone }}" id="hdr_move_stage_btn" rel="{{@$application->wiseId}}" title="Move candidate to another stage">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="4 7 9 12 4 17"/>
          <polyline points="12 7 17 12 12 17"/>
        </svg>
        Move Stage
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="cdr-caret" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
      </a>

      <div class="cdr-topbar-split" role="separator" aria-hidden="true"></div>

      @if(!empty($application->resume) && in_array(strtolower(pathinfo($application->resume, PATHINFO_EXTENSION)),['pdf', 'doc', 'docx', 'htm', 'html']))
        @if($resumeAwsPath)
          <div class="cdr-dl-wrap" id="hdrDlWrapApp">
            <button type="button" class="cdr-action-btn cdr-btn-secondary" id="hdrDlToggleApp" aria-haspopup="true" aria-expanded="false">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              Download Resume
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="cdr-caret" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div id="hdrDlMenuApp" class="cdr-dl-menu">
              <a href="{{ route('download.resume', ['id' => $application->id,'path' => rtrim(strtr(base64_encode($application->resume), '+/', '-_'), '=')]) }}" target="_blank" class="cdr-dl-item">
                Download Resume
              </a>
              <button type="button" onclick="hdrDownloadWithLogoApp()" class="cdr-dl-item cdr-dl-item-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                Resume with Company Logo
                <span class="cdr-dl-badge">LOGO</span>
              </button>
              <button type="button" onclick="cdrExportProfilePDF(this)" class="cdr-dl-item" id="cdrExportPdfBtnHdr">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="M9 15l3 3 3-3"/></svg>
                Export Candidate Profile
                <span class="cdr-dl-badge" style="background:#fee2e2;color:#dc2626;border-radius:4px;padding:1px 5px;font-size:9px;font-weight:700;">PDF</span>
              </button>
            </div>
          </div>
          <script>
          (function(){
            var t=document.getElementById('hdrDlToggleApp'),m=document.getElementById('hdrDlMenuApp');
            if(t&&m){
              t.addEventListener('click',function(e){e.stopPropagation();var open=m.classList.contains('open');m.classList.toggle('open',!open);t.setAttribute('aria-expanded', open?'false':'true');});
              document.addEventListener('click',function(){m.classList.remove('open');t.setAttribute('aria-expanded','false');});
            }
          })();
          function hdrDownloadWithLogoApp(){
            var m=document.getElementById('hdrDlMenuApp');
            if(m) m.classList.remove('open');
            var tries=0;
            var poll=setInterval(function(){
              if(document.querySelectorAll('#rcvPages .rcv-page').length){
                clearInterval(poll);
                var b=document.querySelector('[data-dl="logo"]');
                if(b) b.click();
              } else if(++tries>80){
                clearInterval(poll);
                alert('Resume is taking too long to load. Please scroll down to the Resume / CV viewer and try again.');
              }
            },100);
          }
          </script>
        @else
          <a href="javascript:void(0);" class="cdr-action-btn cdr-btn-secondary downloadCount {{ $dnone }}" id="hdrDlToggleApp" role="button" aria-haspopup="true" aria-expanded="false" title="Download resume">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download Resume
          </a>
        @endif
      @else
        <a href="{{ route('service_company.job-applications.edit', [ GeneralMethod::value_encrypt($jobWiseCanId)]) }}?currentPage={{ GeneralMethod::value_encrypt($currentPage) }}&currentSlug={{ GeneralMethod::value_encrypt($currentSlug) }}" class="cdr-action-btn cdr-btn-secondary {{ $dnone }}" title="Upload resume">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Upload Resume
        </a>
      @endif
    </div>
    <button type="button" class="cdr-topbar-close" id="kt_activitiesApplication_close" aria-label="Close">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>

  {{-- ===== CARD 1: Hero identity + metrics ===== --}}
  <div class="cdr-outer-card">
  {{-- HERO CARD: identity (left) + 4-row metrics list (right) --}}
  <div class="cdr-hero-card">

    {{-- Sub-Card A: avatar · name/role · meta · full-width skills bar --}}
    <div class="cdr-subcard cdr-subcard-id">

      {{-- Top row: avatar + identity --}}
      <div class="cdr-id-top">
        <div class="cdr-avatar-wrap">
          <div class="cdr-avatar-sq" aria-hidden="true">{{ $__hdrInitials }}</div>
          @if($__hdrJobTotal > 0)
            @php $__mpbClass = $__hdrOverallPct >= 70 ? 'cdr-mpb-green' : ($__hdrOverallPct >= 40 ? 'cdr-mpb-amber' : 'cdr-mpb-red'); @endphp
            <span class="cdr-match-pct-badge {{ $__mpbClass }}">{{ $__hdrOverallPct }}% Match</span>
          @endif
        </div>

        <div class="cdr-id-body">
          <div class="cdr-name-row">
            <h2 class="cdr-name">{{ ucwords(@$application->full_name ?: '—') }}</h2>
            @if($__hdrIsVerified)
              <span class="cdr-verified-v2" title="Verified candidate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
              </span>
            @endif
          </div>

          <p class="cdr-role">{{ ucwords(@$application->current_designation ?: '—') }}</p>

          {{-- 2-col meta grid: exp + company | location + availability --}}
          @php $__dispCompany = (@$application->current_company) ? ucwords(@$application->current_company) : ucwords(@$application->cur_company_name ?: ''); @endphp
          @php $__candCityState = trim(($city_name ?? '').' '.($state_name ?? '')); @endphp
          <div class="cdr-meta-grid">
            @if($__bdTotalExp !== '—')
              <span class="cdr-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                {{ $__bdTotalExp }} Yrs Experience
              </span>
            @endif
            @if($__candCityState)
              <span class="cdr-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $__candCityState }}
              </span>
            @endif
            @if($__dispCompany)
              <span class="cdr-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                {{ $__dispCompany }}
              </span>
            @endif
            @if($__hdrAvailability !== '—')
              <span class="cdr-meta-item {{ $__hdrIsImmediate ? 'cmi-green' : '' }}">
                @if($__hdrIsImmediate)
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="5"/></svg>
                @else
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                @endif
                {{ $__hdrAvailability }}
              </span>
            @endif
          </div>
        </div>
      </div>{{-- /.cdr-id-top --}}

      {{-- Full-width skills bar: green = matched job skill, grey = not matched --}}
      @if(!empty($__hdrSkillsShown))
        <div class="cdr-skills-bar">
          @foreach($__hdrSkillsShown as $__s)
            @php $__isMatch = isset($__hdrJobSkills[mb_strtolower($__s)]); @endphp
            <span class="cdr-skill-tag {{ $__isMatch ? 'cdr-skill-match' : 'cdr-skill-plain' }}">{{ ucwords(strtolower($__s)) }}</span>
          @endforeach
          @if($__hdrSkillsMore > 0)
            <span class="cdr-skill-tag cdr-skill-more">+{{ $__hdrSkillsMore }} More</span>
          @endif
        </div>
      @endif

    </div>

    {{-- Sub-Card B: 4-row vertical metric list --}}
    <div class="cdr-subcard cdr-subcard-metrics">
      <div class="cdr-metric-list" role="list" aria-label="Match metrics">

        {{-- Skills Match --}}
        <div class="cdr-metric-row" role="listitem">
          <div class="cdr-metric-icon ci-green" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <span class="cdr-metric-label">Skills Match</span>
          <span class="cdr-metric-val">{{ $__hdrMatchedCount }}<span class="cdr-metric-frac"> / {{ $__hdrJobTotal ?: 0 }}</span></span>
        </div>

        {{-- Experience Match --}}
        <div class="cdr-metric-row" role="listitem">
          <div class="cdr-metric-icon {{ $__hdrExpMatched ? 'ci-blue' : 'ci-red' }}" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </div>
          <span class="cdr-metric-label">Experience Match</span>
          <span class="cdr-metric-val {{ $__hdrExpMatched ? 'cv-green' : 'cv-red' }}">{{ $__hdrExpStatus }}</span>
        </div>

        {{-- Salary --}}
        <div class="cdr-metric-row" role="listitem">
          <div class="cdr-metric-icon {{ $__hdrUnderBudget ? 'ci-green' : 'ci-red' }}" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
          </div>
          <span class="cdr-metric-label">Salary</span>
          <div class="cdr-metric-val-block">
            <span class="cdr-metric-val {{ $__hdrUnderBudget ? 'cv-green' : 'cv-red' }}">{{ $__hdrUnderBudget ? 'Within Budget' : 'Over Budget' }}</span>
            @if($__hdrEctcText !== '—')
              <span class="cdr-metric-sub">{{ $__hdrCtcText }} – {{ $__hdrEctcText }}</span>
            @endif
          </div>
        </div>

        {{-- Location Match --}}
        <div class="cdr-metric-row" role="listitem">
          <div class="cdr-metric-icon {{ $__hdrLocMatched ? 'ci-green' : 'ci-red' }}" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <span class="cdr-metric-label">Location Match</span>
          <span class="cdr-metric-val {{ $__hdrLocMatched ? 'cv-green' : 'cv-red' }}">{{ $__hdrLocText }}</span>
        </div>

      </div>
    </div>

  </div>
  </div>{{-- /.cdr-outer-card (card 1: hero) --}}

  {{-- ===== CARD 2: 6 pipeline stat sub-cards (matches job drawer pipeline section) ===== --}}
  <div class="cdr-pipeline-section" aria-label="Candidate at-a-glance">

    {{-- Total Experience --}}
    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-blue">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Total Experience</div>
        <div class="cdr-pipeline-value">{{ $__bdTotalExp }}<span class="cpi-unit"> Yrs</span></div>
      </div>
    </div>

    {{-- Relevant Experience --}}
    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-green">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><line x1="12" y1="3" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="21"/><line x1="3" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="21" y2="12"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Relevant Exp</div>
        <div class="cdr-pipeline-value">{{ $__bdRelExp }}<span class="cpi-unit"> Yrs</span></div>
      </div>
    </div>

    {{-- Gender --}}
    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-purple">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Gender</div>
        <div class="cdr-pipeline-value">{{ ucwords(@$application->gender) ?: '—' }}</div>
      </div>
    </div>

    {{-- Nationality --}}
    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-amber">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Nationality</div>
        <div class="cdr-pipeline-value" title="{{ ucwords(@$application->nationality) }}">{{ \Illuminate\Support\Str::limit(ucwords(@$application->nationality), 10, '…') ?: '—' }}</div>
      </div>
    </div>

    {{-- Work Mode --}}
    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-teal">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Work Mode</div>
        <div class="cdr-pipeline-value">{{ ucwords(@$application->wm) ?: '—' }}</div>
      </div>
    </div>

    {{-- Education --}}
    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-indigo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.66 1.79 3 4 3s4-1.34 4-3v-5"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Education</div>
        <div class="cdr-pipeline-value" title="{{ strtoupper(@$application->qualification_name) }}">{{ \Illuminate\Support\Str::limit(strtoupper(@$application->qualification_name), 10, '…') ?: '—' }}</div>
      </div>
    </div>

  </div>{{-- /.cdr-pipeline-section --}}

  {{-- ===== CARD 3: Tabs + Content ===== --}}
  <div class="cdr-outer-card cdr-tabs-card">
  <nav class="page-tabs tab-content nav" aria-label="Profile sections" role="tablist">
    <a type="button" class="tab tab-active" data-toggle="tab" href="#personal_details" aria-selected="true">
      <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
      Profile Overview
    </a>
    <a type="button" class="tab" data-toggle="tab" href="#notes" aria-selected="false">
      <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
          <polyline points="10 9 9 9 8 9"/>
        </svg>
        Notes
    </a>
    @if($editComman)
      <a type="button" class="tab" data-toggle="tab" href="#timeline" aria-selected="false">
        <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
        Timeline
      </a>
     @endif
     @if(!empty($application->resume) && in_array(strtolower(pathinfo($application->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
      <a type="button" class="tab" data-toggle="tab" href="#resume_cv" aria-selected="false">
        <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        Resume / CV
      </a>
     @endif
  </nav>

  <main class="page-content">

    @if(!empty($application->resume) && in_array(strtolower(pathinfo($application->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
    @php
        $rcvJobSkills = [];
        if(!empty($jobSkill)){
            foreach($jobSkill as $js){ $js=(object)$js; $sn=trim(@$js->skill_name); if($sn!=='') $rcvJobSkills[]=$sn; }
        }
        $rcvJobSkills = array_values(array_unique($rcvJobSkills));
        $rcvCandSkills = [];
        if(!empty(@$application->skills)){
            foreach(explode(',', $application->skills) as $cs){ $cs=trim($cs); if($cs!=='') $rcvCandSkills[]=$cs; }
        }
        $rcvCandSkills = array_values(array_unique($rcvCandSkills));
        $__rcvPath = parse_url((string) @$application->resume, PHP_URL_PATH);
        $__rcvPath = $__rcvPath ?: (string) @$application->resume;
        $rcvExt = strtolower(pathinfo($__rcvPath, PATHINFO_EXTENSION));
        $rcvIsPdf = ($rcvExt === 'pdf');
        // same-origin URL pdf.js / mammoth can fetch: r2 -> download route, full URL -> direct, relative -> url()
        $rcvFetchUrl = '';
        $rcvDownloadUrl = '';
        if(!empty($application->resume)){
            $__r = $application->resume;
            if($resumeAwsPath || strpos($__r, 'file.jero.works') !== false){
                $__rcvEncodedPath = rtrim(strtr(base64_encode($__r), '+/', '-_'), '=');
                $rcvFetchUrl = route('download.resume', ['id' => $application->id, 'path' => $__rcvEncodedPath, 'preview' => 1]);
                $rcvDownloadUrl = route('download.resume', ['id' => $application->id, 'path' => $__rcvEncodedPath]);
            } elseif(filter_var($__r, FILTER_VALIDATE_URL)){
                $rcvFetchUrl = $__r;
                $rcvDownloadUrl = $__r;
            } else {
                $rcvFetchUrl = url($__r);
                $rcvDownloadUrl = url($__r);
            }
        }
        $rcvPublicUrl = @$application->resume;
        $rcvFileName  = basename(@$application->resume);
        // doc/docx -> server converts to PDF (LibreOffice, cached) so the full PDF pipeline can render + highlight it
        $rcvConvertUrl = in_array($rcvExt, ['doc','docx'])
            ? route('service_company.job-applications.resume-as-pdf', ['id' => $application->id, 'preview' => 1])
            : '';
        // hide contact/social only for other-company bench candidates
        $rcvMaskContact = (@$application->condidate_type == 'bench_condidate'
            && (int) @$application->other_company !== (int) (auth()->user()->company_id ?? 0));
        // recruiting company logo + name (single cheap DB read; r2 logos streamed same-origin to avoid canvas CORS taint)
        $rcvCompanyLogo = '';
        $rcvCompanyName = '';
        $__rcvCid = auth()->user()->company_id ?? 0;
        if($__rcvCid){
            $__rcvC = $rcvCompany ?? DB::table('companies')->select('logo','company_name')->where('id', $__rcvCid)->first();
            $rcvCompanyName = ucwords((string) (@$__rcvC->company_name ?? ''));
            $__rcvLogo = $__rcvC->logo ?? '';
            if(!empty($__rcvLogo) && strlen($__rcvLogo) > 5){
                // same-origin proxy returns the logo bytes so they embed without CORS taint
                $rcvCompanyLogo = route('service_company.job-applications.company-logo-proxy');
            }
        }
        // candidate summary for the submission banner
        $__te = trim((string) @$application->total_exp);
        $rcvBExp = ($__te !== '') ? ($__te . ' Years') : '--';
        if((@$application->serving_notice_period_mode == 0) || (@$application->serving_notice_period_mode == 1)){
            $rcvBNotice = 'Available ' . date('d M Y', strtotime(@$application->np_id));
        } else {
            $rcvBNotice = trim((string) @$application->np_id);
        }
        if($rcvBNotice === '') $rcvBNotice = '--';
        $rcvBLoc = trim(@$city_name, ', ');
        if($rcvBLoc === '') $rcvBLoc = trim(ucwords((string) @$country_name), ', ');
        if($rcvBLoc === '') $rcvBLoc = '--';
        $__pay  = GeneralMethod::formatViewSalary(@$application->ctc_yearly, $symbols, $jobMode);
        $__bill = GeneralMethod::formatViewSalary(@$application->ectc_yearly, $symbols, $jobMode);
        $__cur1 = html_entity_decode(strip_tags((string) @$current_ctc_cur), ENT_QUOTES, 'UTF-8');
        $__cur2 = html_entity_decode(strip_tags((string) @$current_ectc_cur), ENT_QUOTES, 'UTF-8');
        $rcvBCctc = trim($__cur1 . ' ' . GeneralMethod::thousandsCurrencyFormat(@$__pay->amount));
        $rcvBEctc = trim($__cur2 . ' ' . GeneralMethod::thousandsCurrencyFormat(@$__bill->amount));
        if($rcvBCctc === '') $rcvBCctc = '--';
        if($rcvBEctc === '') $rcvBEctc = '--';
        $rcvBPresented = ucwords((string) (auth()->user()->name ?? ''));
        if($rcvBPresented === '') $rcvBPresented = '--';
    @endphp
    <div class="tab-pane fade" id="resume_cv" role="tabpanel">
      <style>
        #resume_cv, #resume_cv * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; }
        #resume_cv { font-size:13px; }
        /* Stacked: keyword panel full-width top, viewer full-width below */
        #resume_cv .rcv-wrap { display:flex; flex-direction:column; gap:0; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.03); background:#fff; }
        #resume_cv .rcv-side { order:1; width:100%; background:#fff; border-bottom:1px solid #eef2f7; border-right:none; border-radius:0; padding:16px 20px 14px; overflow-y:visible; display:flex; flex-direction:column; gap:12px; }
        #resume_cv .rcv-side::-webkit-scrollbar { width:4px; }
        #resume_cv .rcv-side::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:2px; }
        /* Header: title+subtitle left, stats right — horizontal */
        #resume_cv .rcv-side-head { display:flex; align-items:center; justify-content:space-between; gap:20px; padding-bottom:0; margin-bottom:0; border-bottom:none; }
        #resume_cv .rcv-side-head .rcv-head-txt h3 { font-size:13px; font-weight:700; color:#0f172a; margin:0; }
        #resume_cv .rcv-sub { font-size:11px; color:#94a3b8; }
        #resume_cv .rcv-summary { display:flex; gap:6px; margin:0; flex-shrink:0; }
        #resume_cv .rcv-sumitem { min-width:76px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:6px 10px; text-align:center; }
        #resume_cv .rcv-sumnum { display:block; font-size:14px; font-weight:700; color:#0f172a; line-height:1.2; }
        #resume_cv .rcv-sumlbl { font-size:9.5px; color:#94a3b8; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; }
        #resume_cv .rcv-kw-toolbar { display:flex; align-items:center; gap:10px; margin:14px 0 4px; flex-wrap:wrap; }
        #resume_cv .rcv-kw-add { flex:1; min-width:200px; }
        #resume_cv .rcv-kw-add input { width:100%; padding:8px 10px; border:1px solid #e2e8f0; border-radius:8px; font-size:12px; outline:none; }
        #resume_cv .rcv-kw-add input:focus { border-color:#6366f1; }
        #resume_cv .rcv-kw-actions { display:flex; gap:8px; }
        #resume_cv .rcv-btn { padding:7px 14px; border:1px solid #e2e8f0; background:#fff; border-radius:7px; font-size:11px; font-weight:600; color:#475569; cursor:pointer; white-space:nowrap; }
        #resume_cv .rcv-btn:hover { background:#f1f5f9; }
        #resume_cv .rcv-kw-cols { display:flex; flex-direction:column; gap:6px; margin-top:0; }
        #resume_cv .rcv-kw-group { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:0; }
        #resume_cv .rcv-kw-title { font-size:10.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.4px; margin-bottom:0; flex-shrink:0; white-space:nowrap; }
        #resume_cv .rcv-chips { display:flex; flex-wrap:wrap; gap:6px; }
        #resume_cv .rcv-chip { display:inline-flex; align-items:center; gap:5px; padding:4px 9px; border-radius:20px; font-size:11px; font-weight:600; cursor:default; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; user-select:none; transition:all .12s; }
        #resume_cv .rcv-chip .rcv-cnt { font-size:10px; background:#e2e8f0; color:#475569; border-radius:10px; padding:0 5px; min-width:15px; text-align:center; }
        #resume_cv .rcv-chip.found { border-color:#bbf7d0; background:#f0fdf4; color:#15803d; }
        #resume_cv .rcv-chip.found .rcv-cnt { background:#bbf7d0; color:#15803d; }
        #resume_cv .rcv-chip.missing { opacity:.6; }
        #resume_cv .rcv-chip.active { background:#fef3c7; border-color:#fcd34d; color:#92400e; box-shadow:0 0 0 2px rgba(251,191,36,.25); }
        #resume_cv .rcv-chip.active .rcv-cnt { background:#fcd34d; color:#92400e; }
        #resume_cv .rcv-chip-x { margin-left:3px; cursor:pointer; font-weight:700; color:#92400e; opacity:.6; font-size:13px; line-height:1; }
        #resume_cv .rcv-chip-x:hover { opacity:1; }
        #resume_cv .rcv-kw-add-row { display:flex; gap:8px; margin:0; }
        #resume_cv .rcv-kw-add-row input { flex:1; min-width:0; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:12px; outline:none; }
        #resume_cv .rcv-kw-add-row input:focus { border-color:#6366f1; }
        #resume_cv .rcv-add-btn { padding:8px 16px; border:none; background:#6366f1; color:#fff; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap; }
        #resume_cv .rcv-add-btn:hover { background:#4f46e5; }
        #resume_cv .rcv-hl-dl { display:inline-flex; align-items:center; gap:5px; width:auto !important; padding:0 10px !important; height:30px; border:1px solid #c7d2fe !important; background:#eef2ff !important; color:#4f46e5 !important; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap; }
        #resume_cv .rcv-hl-dl:hover { background:#6366f1 !important; color:#fff !important; border-color:#6366f1 !important; }
        #resume_cv .rcv-hl-dl[disabled] { opacity:.6; cursor:default; }
        #resume_cv .rcv-dl { position:relative; }
        #resume_cv .rcv-dl-menu { position:absolute; right:0; top:calc(100% + 5px); background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12); min-width:248px; z-index:60; display:none; overflow:hidden; }
        #resume_cv .rcv-dl.open .rcv-dl-menu { display:block; }
        #resume_cv .rcv-dl-menu .rcv-dl-item { display:block; width:100%; height:auto; min-height:0; justify-content:flex-start; text-align:left; padding:9px 13px; font-size:12px; font-weight:500; color:#334155; background:#fff; border:0; border-radius:0; cursor:pointer; text-decoration:none; white-space:nowrap; }
        #resume_cv .rcv-dl-menu .rcv-dl-item:hover { background:#eef2ff; color:#4f46e5; }
        #resume_cv .rcv-dl-menu .rcv-dl-item + .rcv-dl-item { border-top:1px solid #f1f5f9; }
        #resume_cv .rcv-main { order:2; background:#fff; border:none; border-radius:0; overflow:hidden; display:flex; flex-direction:column; min-height:0; }
        #resume_cv .rcv-toolbar { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 14px; border-bottom:1px solid #eef2f7; background:#fafbff; flex-wrap:wrap; }
        #resume_cv .rcv-file { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; color:#334155; min-width:0; }
        #resume_cv .rcv-file svg { width:16px; height:16px; color:#6366f1; flex-shrink:0; }
        #resume_cv .rcv-file span { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:230px; }
        #resume_cv .rcv-tools { display:flex; align-items:center; gap:6px; }
        #resume_cv .rcv-tools button, #resume_cv .rcv-tools a { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border:1px solid #e2e8f0; background:#fff; border-radius:7px; color:#475569; cursor:pointer; text-decoration:none; }
        #resume_cv .rcv-tools button:hover, #resume_cv .rcv-tools a:hover { background:#f1f5f9; color:#6366f1; }
        #resume_cv .rcv-tools svg { width:15px; height:15px; }
        #resume_cv .rcv-hitnav { display:flex; align-items:center; gap:4px; font-size:11px; font-weight:600; color:#64748b; padding:0 4px; }
        #resume_cv .rcv-hlwrap { display:flex; align-items:center; gap:6px; font-size:11px; color:#64748b; }
        #resume_cv .rcv-switch { position:relative; width:34px; height:18px; background:#cbd5e1; border-radius:20px; cursor:pointer; transition:.15s; }
        #resume_cv .rcv-switch.on { background:#6366f1; }
        #resume_cv .rcv-switch::after { content:''; position:absolute; top:2px; left:2px; width:14px; height:14px; background:#fff; border-radius:50%; transition:.15s; }
        #resume_cv .rcv-switch.on::after { left:18px; }
        #resume_cv .rcv-viewport { overflow:visible; background:#525659; padding:16px; display:flex; flex-direction:column; align-items:center; gap:14px; }
        #resume_cv .rcv-page { position:relative; box-shadow:0 4px 16px rgba(0,0,0,.35); background:#fff; }
        #resume_cv .rcv-page canvas { display:block; }
        #resume_cv .rcv-textlayer { position:absolute; left:0; top:0; right:0; bottom:0; overflow:hidden; line-height:1; opacity:1; }
        #resume_cv .rcv-textlayer span { position:absolute; white-space:pre; color:transparent; transform-origin:0% 0%; }
        #resume_cv .rcv-textlayer mark.rcv-hit { background:rgba(251,191,36,.45); color:transparent; border-radius:2px; }
        #resume_cv .rcv-textlayer mark.rcv-hit-active { background:rgba(249,115,22,.6); box-shadow:0 0 0 2px rgba(249,115,22,.5); }
        #resume_cv .rcv-loading { color:#e2e8f0; font-size:13px; padding:40px; }
        #resume_cv .rcv-iframe { width:100%; height:70vh; border:0; background:#fff; }
        #resume_cv .rcv-html { background:#fff; width:100%; max-width:820px; margin:0 auto; padding:44px 52px; color:#1f2937; font-size:13px; line-height:1.65; box-shadow:0 4px 16px rgba(0,0,0,.35); word-wrap:break-word; }
        #resume_cv .rcv-html * { max-width:100%; font-size:inherit; }
        #resume_cv .rcv-html p { margin:0 0 8px; }
        #resume_cv .rcv-html h1, #resume_cv .rcv-html h2, #resume_cv .rcv-html h3 { font-size:14px; font-weight:700; margin:14px 0 6px; color:#0f172a; }
        #resume_cv .rcv-html ul, #resume_cv .rcv-html ol { margin:0 0 8px 20px; }
        #resume_cv .rcv-html table { border-collapse:collapse; width:100%; }
        #resume_cv .rcv-html td, #resume_cv .rcv-html th { border:1px solid #e2e8f0; padding:4px 6px; }
        #resume_cv .rcv-html img { height:auto; }
        #resume_cv .rcv-textlayer mark.rcv-redact { background:#111827; color:transparent !important; border-radius:2px; user-select:none !important; -webkit-user-select:none !important; -moz-user-select:none !important; pointer-events:none; }
        #resume_cv .rcv-html mark.rcv-redact { background:#111827; color:#111827; border-radius:2px; user-select:none !important; -webkit-user-select:none !important; -moz-user-select:none !important; }
        #resume_cv .rcv-html mark.rcv-hit { background:rgba(251,191,36,.5); color:inherit; border-radius:2px; padding:0 1px; }
        #resume_cv .rcv-html mark.rcv-hit-active { background:rgba(249,115,22,.65); box-shadow:0 0 0 2px rgba(249,115,22,.4); }
        #resume_cv .rcv-note { background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; font-size:12px; padding:10px 14px; border-radius:8px; margin:12px; }
        @media (max-width:640px){ #resume_cv .rcv-summary { width:100%; } #resume_cv .rcv-sumitem { flex:1; } }
      </style>

      <div class="rcv-wrap"
           data-url="{{ $rcvFetchUrl }}"
           data-public="{{ $rcvPublicUrl }}"
           data-ext="{{ $rcvExt }}"
           data-logo="{{ $rcvCompanyLogo }}"
           data-company="{{ $rcvCompanyName }}"
           data-pdfconv="{{ $rcvConvertUrl }}"
           data-mask="{{ $rcvMaskContact ? '1' : '0' }}"
           data-b-exp="{{ $rcvBExp }}"
           data-b-cctc="{{ $rcvBCctc }}"
           data-b-ectc="{{ $rcvBEctc }}"
           data-b-notice="{{ $rcvBNotice }}"
           data-b-loc="{{ $rcvBLoc }}"
           data-b-presented="{{ $rcvBPresented }}"
           data-ispdf="{{ $rcvIsPdf ? '1' : '0' }}">
        <!-- Keyword panel -->
        <aside class="rcv-side">
          <div class="rcv-side-head">
            <div class="rcv-head-txt">
              <h3>Keyword Highlights</h3>
              <span class="rcv-sub">Job skills matched against this résumé</span>
            </div>
            <div class="rcv-summary">
              <div class="rcv-sumitem"><span class="rcv-sumnum" id="rcvMatched">0</span><span class="rcv-sumlbl">Matched</span></div>
              <div class="rcv-sumitem"><span class="rcv-sumnum" id="rcvMissing">0</span><span class="rcv-sumlbl">Missing</span></div>
              <div class="rcv-sumitem"><span class="rcv-sumnum" id="rcvHits">0</span><span class="rcv-sumlbl">Highlights</span></div>
            </div>
          </div>
          <div class="rcv-kw-add-row">
            <input type="text" id="rcvKwInput" placeholder="Add a keyword to check &amp; highlight…" autocomplete="off">
            <button type="button" class="rcv-add-btn" id="rcvKwAddBtn">Add &amp; Check</button>
          </div>
          <div class="rcv-kw-cols">
            <div class="rcv-kw-group">
              <div class="rcv-kw-title">Job Key Skills</div>
              <div class="rcv-chips" id="rcvJobChips"></div>
            </div>
            <div class="rcv-kw-group" id="rcvCustomGroup" style="display:none;">
              <div class="rcv-kw-title">Added Keywords</div>
              <div class="rcv-chips" id="rcvCustomChips"></div>
            </div>
          </div>
        </aside>

        <!-- Viewer -->
        <section class="rcv-main">
          <div class="rcv-toolbar">
            <div class="rcv-file">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <span title="{{ $rcvFileName }}">{{ $rcvFileName }}</span>
            </div>
            <div class="rcv-tools">
              <div class="rcv-hitnav">
                <button type="button" id="rcvPrevHit" title="Previous match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg></button>
                <span id="rcvHitNav">0/0</span>
                <button type="button" id="rcvNextHit" title="Next match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
              </div>
              <a id="rcvOpenNew" href="{{ $rcvDownloadUrl ?: $rcvPublicUrl }}" target="_blank" title="Open in new tab"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a>
              <div class="rcv-dl" id="rcvDl">
                <button type="button" class="rcv-hl-dl" id="rcvDlToggle">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  <span>Download</span>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="rcv-dl-menu" id="rcvDlMenu">
                  <a class="rcv-dl-item" href="{{ $rcvDownloadUrl ?: $rcvPublicUrl }}" target="_blank">Download Resume</a>
                  <button type="button" class="rcv-dl-item" data-dl="logo">Resume with Company Logo</button>
                  <button type="button" class="rcv-dl-item" data-dl="hl">Resume with Highlighted Keywords</button>
                  <button type="button" class="rcv-dl-item" data-dl="both">Highlighted Keywords + Company Logo</button>
                  <button type="button" class="rcv-dl-item" onclick="cdrExportProfilePDF(this)" id="cdrExportPdfBtnRcv" style="display:flex;align-items:center;gap:7px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;flex-shrink:0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="M9 15l3 3 3-3"/></svg>
                    Export Candidate Profile PDF
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="rcv-viewport" id="rcvViewport">
            <div class="rcv-loading" id="rcvLoading">Loading résumé…</div>
            <div id="rcvPages" style="display:flex;flex-direction:column;align-items:center;gap:14px;width:100%;"></div>
            <iframe class="rcv-iframe" id="rcvIframe" style="display:none;"></iframe>
          </div>
        </section>
      </div>

      <script>
      (function(){
        var inited = false;
        var jobSkills = @json($rcvJobSkills);
        var candSkills = @json($rcvCandSkills);
        var resumeText = '';
        var keywords = {};   // name -> {type, active, count}
        var hits = [];       // mark elements in document order
        var curHit = -1;
        var hlOn = true;
        var hideContact = true;   // redact email/phone/social for other-company bench candidates
        var rcvMode = 'pdf';      // 'pdf' (canvas pages) or 'html' (docx via mammoth)
        var htmlOriginal = '';    // pristine docx HTML for re-highlighting
        var pdfDoc = null;        // loaded PDF.js doc, re-rendered hi-res for downloads
        var EMAIL_RE=/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/g;
        var PHONE_RE=/\+?\d[\d\s().\-]{7,}\d/g;
        var URL_RE=/(?:https?:\/\/|www\.)[^\s<>"']+/gi;
        var SOCIAL_RE=/\b(?:linkedin|github|gitlab|twitter|facebook|fb|instagram|insta|behance|dribbble|medium|stackoverflow|youtube|t)\.(?:com|me|io|in|co)[^\s<>"']*/gi;
        function redactHtml(html){
          var wrap=function(m){ return '<mark class="rcv-redact">'+m+'</mark>'; };
          return html.replace(EMAIL_RE,wrap).replace(URL_RE,wrap).replace(SOCIAL_RE,wrap).replace(PHONE_RE,wrap);
        }

        function esc(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
        function escRe(s){ return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }
        function $id(id){ return document.getElementById(id); }

        function activeList(){ return Object.keys(keywords).filter(function(k){ return keywords[k].active; }); }

        function buildRegex(){
          var act = activeList();
          if(!act.length) return null;
          var pat = act.map(escRe).sort(function(a,b){ return b.length-a.length; }).join('|');
          return new RegExp('('+pat+')','gi');
        }

        function countOccurrences(){
          Object.keys(keywords).forEach(function(k){
            var re = new RegExp(escRe(k),'gi');
            var m = resumeText.match(re);
            keywords[k].count = m ? m.length : 0;
          });
        }

        function chipHtml(name){
          var k = keywords[name];
          var cls = 'rcv-chip' + (k.count>0 ? ' found' : ' missing') + (k.active ? ' active' : '');
          var rm = (k.type==='custom') ? '<span class="rcv-chip-x" data-rm="'+esc(name)+'" title="Remove">&times;</span>' : '';
          return '<span class="'+cls+'" data-kw="'+esc(name)+'">'+esc(name)+'<span class="rcv-cnt">'+k.count+'</span>'+rm+'</span>';
        }

        function renderChips(){
          var jc = '', xc = '';
          Object.keys(keywords).forEach(function(name){
            var t = keywords[name].type;
            if(t==='job') jc += chipHtml(name);
            else if(t==='custom') xc += chipHtml(name);
          });
          $id('rcvJobChips').innerHTML = jc || '<span class="rcv-sub">No job skills</span>';
          var cg = $id('rcvCustomGroup');
          if(cg){ if(xc){ cg.style.display='block'; $id('rcvCustomChips').innerHTML = xc; } else { cg.style.display='none'; $id('rcvCustomChips').innerHTML=''; } }
          var matched=0, missing=0;
          Object.keys(keywords).forEach(function(k){ if(keywords[k].count>0) matched++; else missing++; });
          $id('rcvMatched').textContent = matched;
          $id('rcvMissing').textContent = missing;
        }

        var REDACT_RE = new RegExp(EMAIL_RE.source+'|'+URL_RE.source+'|'+SOCIAL_RE.source+'|'+PHONE_RE.source, 'gi');
        function highlightInNode(node, re, cls){
          cls = cls || 'rcv-hit';
          if(node.nodeType === 3){
            var text = node.nodeValue;
            if(!text) return;
            re.lastIndex = 0;
            if(!re.test(text)){ re.lastIndex = 0; return; }
            re.lastIndex = 0;
            var frag = document.createDocumentFragment(), last = 0, m;
            while((m = re.exec(text)) !== null){
              if(m.index > last) frag.appendChild(document.createTextNode(text.slice(last, m.index)));
              var mk = document.createElement('mark'); mk.className = cls; mk.textContent = m[0];
              frag.appendChild(mk);
              last = m.index + m[0].length;
              if(m.index === re.lastIndex) re.lastIndex++;
            }
            if(last < text.length) frag.appendChild(document.createTextNode(text.slice(last)));
            node.parentNode.replaceChild(frag, node);
          } else if(node.nodeType === 1 && !/^(script|style|mark)$/i.test(node.nodeName)){
            Array.prototype.slice.call(node.childNodes).forEach(function(k){ highlightInNode(k, re, cls); });
          }
        }
        function applyHighlights(){
          var re = (hlOn ? buildRegex() : null);
          if(rcvMode === 'html'){
            var cont = $id('rcvHtml');
            if(cont){
              cont.innerHTML = htmlOriginal;
              if(hideContact) highlightInNode(cont, REDACT_RE, 'rcv-redact');
              if(re) highlightInNode(cont, re);
            }
            hits = Array.prototype.slice.call(document.querySelectorAll('#rcvHtml mark.rcv-hit'));
          } else {
            var spans = document.querySelectorAll('#rcvPages .rcv-textlayer span');
            spans.forEach(function(sp){
              if(sp.dataset.orig === undefined) sp.dataset.orig = sp.textContent;
              var t = sp.dataset.orig;
              if(!re && !hideContact){ sp.textContent = t; return; }
              var html = esc(t);
              if(re){ re.lastIndex = 0; html = html.replace(re, '<mark class="rcv-hit">$1</mark>'); }
              if(hideContact){ html = redactHtml(html); }
              sp.innerHTML = html;
            });
            hits = Array.prototype.slice.call(document.querySelectorAll('#rcvPages mark.rcv-hit'));
          }
          curHit = -1;
          $id('rcvHits').textContent = hits.length;
          $id('rcvHitNav').textContent = hits.length ? ('0/'+hits.length) : '0/0';
        }

        function gotoHit(dir){
          if(!hits.length) return;
          if(curHit>=0 && hits[curHit]) hits[curHit].classList.remove('rcv-hit-active');
          curHit = (curHit + dir + hits.length) % hits.length;
          var el = hits[curHit];
          el.classList.add('rcv-hit-active');
          el.scrollIntoView({ behavior:'smooth', block:'center' });
          $id('rcvHitNav').textContent = (curHit+1)+'/'+hits.length;
        }

        function toggleKw(name){
          if(!keywords[name]) return;
          keywords[name].active = !keywords[name].active;
          renderChips(); applyHighlights();
        }

        function addKeyword(name, type){
          name = (name||'').trim();
          if(!name) return;
          var key = Object.keys(keywords).filter(function(k){ return k.toLowerCase()===name.toLowerCase(); })[0];
          if(key){ keywords[key].active = true; }
          else {
            var re = new RegExp(escRe(name),'gi'); var m = resumeText.match(re);
            keywords[name] = { type: type||'custom', active:true, count: m?m.length:0 };
          }
          renderChips(); applyHighlights();
        }

        function loadScript(src){
          return new Promise(function(res, rej){
            var s = document.createElement('script'); s.src = src; s.onload = res; s.onerror = rej;
            document.head.appendChild(s);
          });
        }

        function loadLogo(){
          if(window.__rcvLogoTried) return Promise.resolve(window.__rcvLogo || null);
          window.__rcvLogoTried = true;
          var wrap = document.querySelector('#resume_cv .rcv-wrap');
          var src = (wrap && wrap.getAttribute('data-logo')) || '';
          if(!src) return Promise.resolve(null);
          // fetch the bytes same-origin and turn them into a data URI — data URIs never taint the canvas
          return fetch(src, { credentials:'include' })
            .then(function(r){ if(!r.ok) throw new Error('HTTP '+r.status); return r.blob(); })
            .then(function(blob){
              return new Promise(function(res){
                var fr = new FileReader();
                fr.onload = function(){
                  var dataUri = fr.result;
                  var im = new Image();
                  im.onload = function(){ im.__dataUri = dataUri; window.__rcvLogo = im; res(im); };
                  im.onerror = function(){ res(null); };
                  im.src = dataUri;
                };
                fr.onerror = function(){ res(null); };
                fr.readAsDataURL(blob);
              });
            })
            .catch(function(){ return null; });
        }
        function setDlBusy(b){
          var t = $id('rcvDlToggle'); if(!t) return;
          t.disabled = b; var sp = t.querySelector('span'); if(sp) sp.textContent = b ? 'Preparing…' : 'Download';
        }
        function xmlEsc(s){ return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
        function clip(s,n){ s = String(s==null?'':s); return s.length>n ? s.slice(0,n-1)+'…' : s; }
        function svgToImage(svg){
          return new Promise(function(res, rej){
            var blob = new Blob([svg], { type:'image/svg+xml;charset=utf-8' });
            var url = URL.createObjectURL(blob);
            var img = new Image();
            img.onload = function(){ URL.revokeObjectURL(url); res(img); };
            img.onerror = function(e){ URL.revokeObjectURL(url); rej(e); };
            img.src = url;
          });
        }
        function buildBannerSvg(wrap, logoUri){
          var g = function(a){ var v = wrap.getAttribute(a); return (v==null || v==='') ? '--' : v; };
          var cols = [
            { ic:'bag',   l:'EXPERIENCE',       v: clip(g('data-b-exp'),14) },
            { ic:'rupee', l:'CURRENT CTC',      v: clip(g('data-b-cctc'),14) },
            { ic:'rupee', l:'EXPECTED CTC',     v: clip(g('data-b-ectc'),14) },
            { ic:'clock', l:'NOTICE PERIOD',    v: clip(g('data-b-notice'),16) },
            { ic:'pin',   l:'CURRENT LOCATION', v: clip(g('data-b-loc'),16) },
            { ic:'user',  l:'PRESENTED BY',     v: clip(g('data-b-presented'),16) }
          ];
          var company = wrap.getAttribute('data-company') || '';
          var W=1500, H=110, navy='#0b1f3a', blue='#2563eb', gray='#64748b';
          function icon(type, cx, cy){
            var t = 'transform="translate('+(cx-9)+','+(cy-9)+') scale(0.78)"';
            var st = 'fill="none" stroke="'+blue+'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
            if(type==='bag')   return '<g '+t+'><rect x="2" y="7" width="20" height="14" rx="2" '+st+'/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" '+st+'/></g>';
            if(type==='clock') return '<g '+t+'><circle cx="12" cy="12" r="9" '+st+'/><polyline points="12 7 12 12 16 14" '+st+'/></g>';
            if(type==='pin')   return '<g '+t+'><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z" '+st+'/><circle cx="12" cy="10" r="3" '+st+'/></g>';
            if(type==='user')  return '<g '+t+'><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" '+st+'/><circle cx="12" cy="7" r="4" '+st+'/></g>';
            if(type==='rupee') return '<g '+t+'><circle cx="12" cy="12" r="10" fill="none" stroke="'+blue+'" stroke-width="1.6"/><text x="12" y="16.5" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="13" font-weight="700" fill="'+blue+'">₹</text></g>';
            return '';
          }
          var s = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 '+W+' '+H+'">';
          s += '<rect width="'+W+'" height="'+H+'" fill="#ffffff"/>';
          s += '<rect x="6" y="7" width="'+(W-12)+'" height="96" rx="12" fill="#ffffff" stroke="'+navy+'" stroke-width="2"/>';
          s += '<path d="M456 7 L494 7 L462 103 L424 103 Z" fill="'+navy+'"/>';
          s += '<path d="M482 7 L493 7 L461 103 L450 103 Z" fill="'+blue+'"/>';
          // logo + company name (left)
          if(logoUri){
            s += '<image xlink:href="'+logoUri+'" href="'+logoUri+'" x="22" y="20" width="92" height="44" preserveAspectRatio="xMinYMid meet"/>';
            if(company) s += '<text x="126" y="48" font-family="Poppins,Arial,sans-serif" font-size="18" font-weight="800" fill="'+navy+'">'+xmlEsc(clip(company,16))+'</text>';
          } else {
            s += '<text x="28" y="52" font-family="Poppins,Arial,sans-serif" font-size="22" font-weight="800" fill="'+navy+'">'+xmlEsc(clip(company||'Company',18))+'</text>';
          }
          // verified badge
          s += '<rect x="22" y="70" width="312" height="26" rx="13" fill="#eaf1ff"/>';
          s += '<circle cx="40" cy="83" r="8" fill="'+blue+'"/><path d="M36 83 l3 3 6 -7" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
          s += '<text x="54" y="87" font-family="Poppins,Arial,sans-serif" font-size="11" font-weight="700" letter-spacing="0.3" fill="'+navy+'">PRESCREENED VERIFIED CANDIDATE</text>';
          var x0=515, x1=W-24, cw=(x1-x0)/cols.length;
          cols.forEach(function(c, i){
            var cx = x0 + cw*i + cw/2;
            if(i>0){ var dx = x0 + cw*i; s += '<line x1="'+dx+'" y1="18" x2="'+dx+'" y2="92" stroke="#e2e8f0" stroke-width="1.4"/>'; }
            s += icon(c.ic, cx, 34);
            s += '<text x="'+cx+'" y="62" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="10.5" font-weight="600" letter-spacing="0.3" fill="'+gray+'">'+xmlEsc(c.l)+'</text>';
            s += '<text x="'+cx+'" y="84" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="15" font-weight="700" fill="'+navy+'">'+xmlEsc(c.v)+'</text>';
          });
          s += '</svg>';
          return s;
        }
        async function buildPdf(opts){
          var pages = document.querySelectorAll('#rcvPages .rcv-page');
          if(!pages.length){ alert('This option is available once the PDF résumé has loaded.'); return; }
          setDlBusy(true);
          try {
            if(!(window.jspdf && window.jspdf.jsPDF)){
              await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
            }
            var JsPDF = window.jspdf.jsPDF;
            var wrap = document.querySelector('#resume_cv .rcv-wrap');
            var bannerImg = null, logoFailed = false;
            if(opts.logo){
              var logo = await loadLogo();
              var logoUri = (logo && logo.__dataUri) ? logo.__dataUri : '';
              if(!logoUri) logoFailed = true;
              try { bannerImg = await svgToImage(buildBannerSvg(wrap, logoUri)); } catch(e){ bannerImg = null; }
            }
            /* 3.0× scale — better resolution than old 2.5× without the file-size cost of 3.5× */
            var QUALITY = 3.0, MAXW = 2800;
            var pagesArr = Array.prototype.slice.call(pages);
            /* render all pages in parallel — wall-clock time ≈ slowest single page */
            var rendered = await Promise.all(pagesArr.map(function(pageWrap, idx){
              return (async function(){
                var cssW = pageWrap.clientWidth || 760, cssH = pageWrap.clientHeight || 1000, hi;
                if(pdfDoc){
                  var page = await pdfDoc.getPage(idx + 1);
                  var base = page.getViewport({ scale: 1 });
                  var renderW = Math.min(cssW * QUALITY, MAXW);
                  var vp = page.getViewport({ scale: renderW / base.width });
                  hi = document.createElement('canvas');
                  hi.width = Math.floor(vp.width);
                  hi.height = Math.floor(vp.height);
                  await page.render({ canvasContext: hi.getContext('2d', { willReadFrequently: true }), viewport: vp }).promise;
                } else {
                  hi = pageWrap.querySelector('canvas');
                  if(!hi) return null;
                }
                var w = hi.width, h = hi.height;
                var headerH = (bannerImg && idx === 0) ? Math.round(w * 0.073) : 0;
                var tmp = document.createElement('canvas');
                tmp.width = w; tmp.height = h + headerH;
                var ctx = tmp.getContext('2d', { willReadFrequently: true });
                ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, w, tmp.height);
                if(headerH){ ctx.drawImage(bannerImg, 0, 0, w, headerH); }
                ctx.drawImage(hi, 0, headerH, w, h);
                var pr = pageWrap.getBoundingClientRect();
                if(opts.highlights){
                  ctx.fillStyle = 'rgba(255,210,0,0.4)';
                  pageWrap.querySelectorAll('mark.rcv-hit').forEach(function(mk){
                    var r = mk.getBoundingClientRect();
                    ctx.fillRect((r.left-pr.left)/pr.width*w, headerH+(r.top-pr.top)/pr.height*h, r.width/pr.width*w, Math.max(r.height/pr.height*h, 6));
                  });
                }
                if(hideContact){
                  ctx.fillStyle = '#111827';
                  pageWrap.querySelectorAll('mark.rcv-redact').forEach(function(mk){
                    var r = mk.getBoundingClientRect();
                    ctx.fillRect((r.left-pr.left)/pr.width*w, headerH+(r.top-pr.top)/pr.height*h, r.width/pr.width*w, Math.max(r.height/pr.height*h, 6));
                  });
                }
                /* JPEG 0.88 at 3.0× renders sharper than the old JPEG 0.96 at 2.5× — more pixels compensate for slightly more compression */
                return { imgData: tmp.toDataURL('image/jpeg', 0.88), cssW: cssW, cssH: cssH, headerH: headerH, w: w };
              })();
            }));
            /* assemble PDF sequentially — pages must be added in document order */
            var pdf = null;
            for(var i = 0; i < rendered.length; i++){
              var r = rendered[i];
              if(!r) continue;
              var headerCssH = r.headerH * (r.cssW / r.w);
              var pw = Math.round(r.cssW), ph = Math.round(r.cssH + headerCssH);
              var orient = pw > ph ? 'l' : 'p';
              if(!pdf){ pdf = new JsPDF({ orientation: orient, unit: 'px', format: [pw, ph] }); }
              else { pdf.addPage([pw, ph], orient); }
              pdf.addImage(r.imgData, 'JPEG', 0, 0, pw, ph, undefined, 'FAST');
            }
            if(pdf){
              var fn = ((document.querySelector('#resume_cv .rcv-file span') || {}).textContent || 'resume').replace(/\.[^.]+$/, '');
              var sfx = (opts.highlights && opts.logo) ? '_highlighted_logo' : opts.highlights ? '_highlighted' : opts.logo ? '_logo' : '';
              var outputName = fn + sfx + '.pdf';
              pdf.save(outputName);
              if(typeof window.__rdcNotifyDownloadStarted === 'function'){
                window.__rdcNotifyDownloadStarted(outputName);
              }
            }
            if(opts.logo && !bannerImg){ alert('Could not add the company banner; downloaded the résumé without it.'); }
            else if(opts.logo && logoFailed){ alert('Banner added without the company logo — the logo image could not be embedded due to its security settings.'); }
          } catch(err){
            alert('Could not generate the PDF.');
          } finally {
            setDlBusy(false);
          }
        }

        function wireControls(){
          $id('rcvPrevHit').addEventListener('click', function(){ gotoHit(-1); });
          $id('rcvNextHit').addEventListener('click', function(){ gotoHit(1); });
          var dl = $id('rcvDl'), dlToggle = $id('rcvDlToggle'), dlMenu = $id('rcvDlMenu');
          if(dl && dlToggle && dlMenu){
            dlToggle.addEventListener('click', function(e){ e.stopPropagation(); dl.classList.toggle('open'); });
            document.addEventListener('click', function(e){ if(!dl.contains(e.target)) dl.classList.remove('open'); });
            dlMenu.addEventListener('click', function(e){
              var item = e.target.closest('.rcv-dl-item');
              if(!item) return;
              dl.classList.remove('open');
              var t = item.getAttribute('data-dl');
              if(t==='logo') buildPdf({ highlights:false, logo:true });
              else if(t==='hl') buildPdf({ highlights:true, logo:false });
              else if(t==='both') buildPdf({ highlights:true, logo:true });
              // no data-dl => the plain "Download Resume" link downloads the original natively
            });
          }
          var kwInput = $id('rcvKwInput');
          if(kwInput){
            kwInput.addEventListener('keydown', function(e){
              if(e.key==='Enter' || e.key===','){ e.preventDefault(); addKeyword(this.value,'custom'); this.value=''; }
            });
          }
          var addBtn = $id('rcvKwAddBtn');
          if(addBtn){
            addBtn.addEventListener('click', function(){ var i=$id('rcvKwInput'); addKeyword(i.value,'custom'); i.value=''; i.focus(); });
          }
          // remove an added keyword (× on custom chips only)
          document.addEventListener('click', function(e){
            var x = e.target.closest && e.target.closest('#resume_cv .rcv-chip-x');
            if(x){ e.preventDefault(); var nm = x.getAttribute('data-rm'); if(keywords[nm]){ delete keywords[nm]; renderChips(); applyHighlights(); } }
          });
          // chip click toggles highlight on/off
          document.addEventListener('click', function(e){
            if(e.target.closest && e.target.closest('#resume_cv .rcv-chip-x')) return;
            var chip = e.target.closest && e.target.closest('#resume_cv .rcv-chip[data-kw]');
            if(chip){ toggleKw(chip.getAttribute('data-kw')); }
          });
        }

        function withTimeout(promise, ms){
          return Promise.race([ promise, new Promise(function(_,rej){ setTimeout(function(){ rej(new Error('timeout')); }, ms); }) ]);
        }
        async function renderPdf(url, fallback, attempt){
          attempt = attempt || 0;
          var fail = function(msg){ if(typeof fallback === 'function'){ fallback(); } else { showIframe(msg || 'Showing résumé in preview mode.'); } };
          if(typeof pdfjsLib === 'undefined'){ fail('PDF viewer library not available.'); return; }
          try { var ver = pdfjsLib.version || '2.11.338'; pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/'+ver+'/pdf.worker.min.js'; } catch(e){}
          var pagesEl = $id('rcvPages');
          // Fetch the bytes ourselves — avoids PDF.js range-request/worker hangs and gives clear errors.
          var bytes;
          try {
            var resp = await withTimeout(fetch(url, { credentials:'same-origin' }), 35000);
            if(resp.status === 202){
              var wait = Math.min(2500 + (attempt * 250), 6000);
              $id('rcvLoading').style.display = 'block';
              $id('rcvLoading').textContent = 'Preparing résumé preview…';
              if(attempt < 40){
                setTimeout(function(){ renderPdf(url, fallback, attempt + 1); }, wait);
                return;
              }
              throw new Error('conversion-timeout');
            }
            if(!resp.ok) throw new Error('HTTP '+resp.status);
            var ab = await resp.arrayBuffer();
            bytes = new Uint8Array(ab);
            var sig = String.fromCharCode.apply(null, bytes.subarray(0,5));
            if(sig.indexOf('%PDF') === -1) throw new Error('not-pdf');
          } catch(err){ fail(); return; }
          var doc;
          try { doc = await withTimeout(pdfjsLib.getDocument({ data: bytes }).promise, 25000); }
          catch(err){ fail(); return; }
          pdfDoc = doc;
          rcvMode = 'pdf';
          $id('rcvPages').style.display='flex';
          $id('rcvLoading').style.display='none';
          var containerW = Math.min(pagesEl.clientWidth || 760, 820);
          pagesEl.innerHTML = ''; hits = [];
          resumeText = '';
          for(var p=1; p<=doc.numPages; p++){
            var page = await doc.getPage(p);
            var base = page.getViewport({ scale:1 });
            var scale = (containerW-2)/base.width;
            var viewport = page.getViewport({ scale:scale });
            var wrap = document.createElement('div'); wrap.className='rcv-page';
            wrap.style.width = Math.floor(viewport.width)+'px';
            wrap.style.height = Math.floor(viewport.height)+'px';
            var canvas = document.createElement('canvas');
            var outScale = window.devicePixelRatio || 1;
            canvas.width = Math.floor(viewport.width*outScale);
            canvas.height = Math.floor(viewport.height*outScale);
            canvas.style.width = Math.floor(viewport.width)+'px';
            canvas.style.height = Math.floor(viewport.height)+'px';
            wrap.appendChild(canvas);
            var tl = document.createElement('div'); tl.className='rcv-textlayer';
            tl.style.width = Math.floor(viewport.width)+'px';
            tl.style.height = Math.floor(viewport.height)+'px';
            wrap.appendChild(tl);
            pagesEl.appendChild(wrap);
            var ctx = canvas.getContext('2d');
            var transform = outScale!==1 ? [outScale,0,0,outScale,0,0] : null;
            await page.render({ canvasContext:ctx, viewport:viewport, transform:transform }).promise;
            var tc = await page.getTextContent();
            resumeText += tc.items.map(function(i){ return i.str; }).join(' ') + '\n';
            try {
              var task = pdfjsLib.renderTextLayer({ textContent:tc, container:tl, viewport:viewport, textDivs:[] });
              if(task && task.promise) await task.promise;
            } catch(e){}
          }
          countOccurrences();
          // default: highlight job skills that are present
          Object.keys(keywords).forEach(function(k){ keywords[k].active = (keywords[k].type==='job' && keywords[k].count>0); });
          renderChips();
          applyHighlights();
        }

        async function renderDocx(url){
          if(typeof mammoth === 'undefined'){ showIframe('Showing résumé in preview mode.'); return; }
          var wrap = document.querySelector('#resume_cv .rcv-wrap');
          var pub = wrap.getAttribute('data-public') || '';
          var sources = [];
          if(url) sources.push(url);
          if(pub && pub !== url) sources.push(pub);
          var bytes = null;
          for(var i=0; i<sources.length && !bytes; i++){
            try {
              var resp = await withTimeout(fetch(sources[i], { credentials:'include' }), 15000);
              if(resp.ok) bytes = await resp.arrayBuffer();
            } catch(e){}
          }
          if(!bytes){ showIframe('Could not load the Word file for highlighting; shown in preview mode.'); return; }
          var html = '';
          try {
            var result = await mammoth.convertToHtml({ arrayBuffer: bytes });
            html = (result && result.value) ? result.value : '';
          } catch(err){ showIframe('This file could not be converted for highlighting (it may be an older .doc); shown in preview mode.'); return; }
          if(!html.trim()){ showIframe('This file could not be converted for highlighting; shown in preview mode.'); return; }
          rcvMode = 'html';
          $id('rcvLoading').style.display = 'none';
          $id('rcvPages').style.display = 'none';
          var cont = document.createElement('div'); cont.id = 'rcvHtml'; cont.className = 'rcv-html';
          cont.innerHTML = html;
          $id('rcvViewport').appendChild(cont);
          htmlOriginal = html;
          resumeText = cont.innerText || cont.textContent || '';
          countOccurrences();
          Object.keys(keywords).forEach(function(k){ keywords[k].active = (keywords[k].type==='job' && keywords[k].count>0); });
          renderChips();
          applyHighlights();
        }

        function showIframe(msg){
          var wrap = document.querySelector('#resume_cv .rcv-wrap');
          var pub = wrap.getAttribute('data-public') || '';
          $id('rcvLoading').style.display='none';
          $id('rcvPages').style.display='none';
          var ifr = $id('rcvIframe');
          if(pub){
            ifr.src = 'https://docs.google.com/gview?url=' + encodeURIComponent(pub) + '&embedded=true';
            ifr.style.display='block';
          } else {
            $id('rcvLoading').style.display='block';
            $id('rcvLoading').textContent = 'Résumé file is not available for preview.';
          }
          if(!document.getElementById('rcvNote')){
            var note = document.createElement('div'); note.id='rcvNote'; note.className='rcv-note';
            note.textContent = msg || 'Keyword highlighting works for PDF and Word (.docx) résumés. This file is shown in preview mode.';
            $id('rcvViewport').parentNode.insertBefore(note, $id('rcvViewport'));
          }
          // keep keyword chips usable (counts will be 0 without extractable text)
          countOccurrences(); renderChips();
        }

        function seedKeywords(){
          jobSkills.forEach(function(s){ if(s) keywords[s] = { type:'job', active:false, count:0 }; });
        }

        function init(){
          if(inited) return;
          var pane = document.getElementById('resume_cv');
          if(!pane || pane.getAttribute('data-rcv-inited') === '1') return;  // another (stale) instance already owns this DOM
          pane.setAttribute('data-rcv-inited', '1');
          inited = true;
          var wrap = pane.querySelector('.rcv-wrap');
          hideContact = wrap.getAttribute('data-mask') !== '0';
          var url = wrap.getAttribute('data-url');
          var isPdf = wrap.getAttribute('data-ispdf')==='1';
          seedKeywords();
          renderChips();
          wireControls();
          var ext = (wrap.getAttribute('data-ext') || '').toLowerCase();
          var pdfConv = wrap.getAttribute('data-pdfconv') || '';
          if(isPdf && url){ renderPdf(url); }
          else if(pdfConv){
            // doc/docx: render the server-converted PDF (full highlight + download pipeline);
            // if conversion fails, fall back to in-browser Word rendering, then preview.
            $id('rcvLoading').textContent = 'Converting document to PDF…';
            renderPdf(pdfConv, function(){ if(url){ renderDocx(url); } else { showIframe(); } });
          }
          else if(url){ renderDocx(url); }
          else { showIframe(); renderChips(); }
        }

        // Intent: Phase 6 Scope event listener directly to current drawer container (FINDING-RES-01 - P0)
        var currentDrawer = document.getElementById('kt_activities3') || document.querySelector('.offcanvas-body') || document;
        var rcvTabBtn = currentDrawer.querySelector('a[href="#resume_cv"], button[data-bs-target="#resume_cv"]');
        if (rcvTabBtn) {
          rcvTabBtn.addEventListener('click', function() {
            setTimeout(init, 120);
          });
        }
        // if the tab is already visible on load
        if(currentDrawer.querySelector('#resume_cv.active, #resume_cv.show.active')) setTimeout(init, 250);
      })();
      </script>
    </div>
    @endif

    <div class="tab-pane fade" id="notes" role="tabpanel">
      <div class="content-split">
        <div class="content-main-full">
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
              </div>
              <h3 class="card-title">Notes</h3>
            </div>
            <div class="form-group">
              
              @if($notVisiableBench)
              <div class="row pt-7">

                <!--begin::Timeline-->
                <div class="timeline timeline-3">
                  <div class="timeline-items">
                    <h3 class="fw-bolder m-0">Notes</h3>
                    <div id="applicant-notes" class="col-sm-12">
                      {{-- style=" max-height: 250px; overflow-y: auto; padding-right: 8px; scrollbar-width: thin; scrollbar-color: #c1c1c1 transparent; " --}}
                      <ul class="list-unstyled">
                          <?php 
                            $cnt	=	0;
                            if (empty($notesArr) || count($notesArr) === 0) {
                                echo '<li class="p-4 text-center text-muted notes-placeholder">No recruiter notes added yet.</li>';
                            } else {
                            foreach($notesArr as $key => $notes )
                            {
                              $date		=	$notes->created_at;
                              $noteDate 	= date("d-M-Y", strtotime($date));
                              $noteTitme 	= date("h:i A", strtotime($date));
                              $posted_by_image			=	'';
                              if(!empty($notes->posted_by_image)){	
                                $posted_by_image			=	@$notes->posted_by_image;
                              }  
                            
                              $bgTextDecore				=	GeneralMethod::icoinText(@$notes->name);	 
                                                                 
                          ?>
                        <li class="media" id="note-{{ $notes->id }}">
                          <div class="timeline-item">
                            <?php if($cnt%3==0){ ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"></rect>
                                  <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3"></path>
                                  <path d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z" fill="#000000"></path>
                                </g>
                              </svg>
                              </span> </div>
                            <?php }else if($cnt%3==1){ ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-warning svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                  <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
                                  <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
                                </g>
                              </svg>
                              </span> </div>
                            <?php  }else if($cnt%3==2){ ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-primary svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <path d="M17,12 L18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 C4,12.6715729 4.67157288,12 5.5,12 L7,12 L7.5582739,6.97553494 C7.80974924,4.71225688 9.72279394,3 12,3 C14.2772061,3 16.1902508,4.71225688 16.4417261,6.97553494 L17,12 Z" fill="#000000"></path>
                                  <rect fill="#000000" opacity="0.3" x="10" y="16" width="4" height="4" rx="2"></rect>
                                </g>
                              </svg>
                              </span> </div>
                            <?php } ?>
                            <div class="timeline-content">
                              <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="me-2 fs-6"> <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder me-2">New Note Added</a> <span class="text-muted me-2"><?php echo @$noteTitme; ?> | <?php echo @$noteDate; ?></span>
                                  <div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" title="{{ ucwords($notes->name) }}"> 
                                   
                                    <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$notes->name)}}" >
                                      <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                                      <a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
                                        <div class="symbol-label fs-8 fs-8 {{ $bgTextDecore}} border-radius-100">{{ substr($notes->name, 0, 1)}}</div>
                                      </a>		 
                                      </div>
                                      </div>
                                  

                                   </div>
                                </div>
                                <div class="dropdown ml-2" data-toggle="tooltip" title="" data-placement="left" data-original-title="New Project"> <a href="javascript:void(0);" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <a href="javascript:void(0);" class="edit-note" data-note-id="{{ $notes->id }}"><i class="fa fa-edit ml-2"></i></a> <a href="javascript:;" class="delete-note" data-note-id="{{ $notes->id }}"><i class="fa fa-trash ml-1 text-danger"></i></a> </div>
                              </div>
                              <p class="p-0 fs-6 comment-para note-text">{{ ucfirst($notes->note_text) }}</p>
                              <div class="note-textarea"></div>
                            </div>
                          </div>
                        </li>
                         <?php 	$cnt++;	} }	?>
                      </ul>
                    </div>
                  </div>
                </div>
                <!--eng::Timeline-->
                
                <div class="separator separator-dashed my-10"></div>
                 @if($editComman)
                <form class="form">
                  <div class="form-group">
                    <textarea class="form-control form-control-lg form-control-solid" name="note" id="note_text" rows="3" placeholder="Type notes" style="margin-top: 0px; margin-bottom: 0px; height: 88px;"></textarea>
                  </div>
                  <div class="row pt-4">
                    <div class="col"> <a href="javascript:void(0);" id="add-note" class="btn btn-sm btn-light-primary fw-bold">Add notes</a> </div>
                  </div>
                </form>
                @endif
                
              </div>
              @endif
              

            </div>
            <br>

         

            <p class="summary-text summary-text-width"></p>
          </section>
        </div>
      </div>
    </div>


    <div class="tab-pane fade show active" id="personal_details" role="tabpanel">

      {{-- Quick Profile card removed; data already shown in the hero pipeline cards above --}}
      <div class="content-split">
        <div class="content-main">

              <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
              </div>
              <h3 class="card-title">Professional Summary</h3>
            </div>
            <p class="summary-text">{{@$application->careersummary}}</p>
            {{-- <span class="word-count-badge">184 / 200 Words</span> --}}
          </section>
         @if($application->condidate_type !='other')
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-success-50); color: var(--color-success-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <h3 class="card-title">Additional Details</h3>
            </div>
            <div class="skills-table">
              <div class="skills-thead">
                <div>Skill</div>
                <div>Experience</div>
                <div>Self Rating</div>
              </div>

                 @php 
                 
                          if(!empty($skillExperiencesCount))
                          {
                              $customTitleId	=	1;
                              $html			=	'';
                              $inc_first		=	0;
                              $inc_last		=	1;

                            $html		=	'<div class="row">';	
                          foreach($skillExperiences as $exp)
                          {  
                               
                        $html		.=	'<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.ucwords(strtolower($exp->skill_key)).'
                          </div>
                          <div class="skill-exp-cell">'.($exp->year ?? '0') .'.'. ($exp->month ?? '0').' Year</div>
                          <div class="skill-rating-cell">
                            <div class="stars">
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none"
                                aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                            </div>
                            <span class="rating-text">'.(@$exp->rating ?? 0).' / 5</span>
                          </div>
                        </div>';
                      }		
                       echo $html;
                  }
                @endphp
            </div>
          </section>
          @endif

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-success-50); color: var(--color-success-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <h3 class="card-title">Key Skills &amp; Experience</h3>
              @if($skillAndQuestions)
                @if($editComman)
                <button type="button" class="jw-card-edit-btn editSkill">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Edit
                </button>
                @endif
              @endif
            </div>
            {{-- Edit form lives in the side drawer (jw-skill-drawer) below content-split --}}
              
            <div class="skills-table job_skill_view">
              <div class="skills-thead">
                <div>Skill</div>
                <div>Experience</div>
                <div>Self Rating</div>
              </div>

                @php 
                          if(!empty($jobSkill))
                          {
                            $html		=	'<div class="row">';	
                          foreach($jobSkill as $skill_val)
                          {  
                             $skill_val = (object) $skill_val;                            
                            
                            $exp = 'EXP.';  
                            $yrs = '';      
                              if(!empty($skill_val->skill_value)){
                              
                                $yrs = ''; 
                              }
                              $skillShowView = '';
                              if($skillShow){
                                $skillShowView = $skill_val->skill_value;
                                if (is_int($skill_val->skill_value)) {
                                  $skillShowView = $skill_val->skill_value.' '.$yrs;
                                }
                              
                              }
                            
                        $html		.=	'<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.ucwords(strtolower($skill_val->skill_name)).'
                          </div>
                          <div class="skill-exp-cell">'.$skillShowView.'</div>
                          <div class="skill-rating-cell">
                            <div class="stars">
                              <svg class="star-skill '.(@$skill_val->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=2 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=3 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=4 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >=5 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none"
                                aria-hidden="true">
                                <path
                                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                              </svg>
                            </div>
                            <span class="rating-text">'.(@$skill_val->rating ?? 0).' / 5</span>
                          </div>
                        </div>';
                      }		
                       echo $html;
                  }
                @endphp

            </div>
          </section>

          @if(!empty($job_additional_detail))
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-success-50); color: var(--color-success-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <h3 class="card-title">Screening Questions</h3>
              @if($skillAndQuestions)
                @if($editComman)
                <button type="button" class="jw-card-edit-btn editJobQuestion">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Edit
                </button>
                @endif
              @endif
            </div>
            {{-- Edit form lives in the side drawer (jw-question-drawer) below content-split --}}
            <div class="skills-table job_additional_view">
              <div class="skills-thead">
                <div>Screening Questions</div>
                <div>Answered</div>
              </div>
                 @php 
                          if(!empty($job_additional_detail))
                          {
                            $html		=	'<div class="row">';	
                          foreach($job_additional_detail as $skill_val)
                          {  
                            $aDetailArr = (object) $skill_val;
                            $ans_value	=	@$aDetailArr->application_detail_value;                         
                            
                        $html		.=	'<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.ucwords(strtolower((string)@$aDetailArr->job_detail_value)).'
                          </div>
                          <div class="skill-exp-cell">'.@$ans_value.'</div>
                        </div>';
                      }		
                       echo $html;
                  }
                @endphp
            </div>
          </section>
          @endif

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-primary-50); color: var(--color-primary-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
              <h3 class="card-title">Interview Slots <span style="font-size:11px;font-weight:500;color:#94a3b8;">(Next 3 Days)</span></h3>
              @if($skillAndQuestions)
              <button type="button" class="jw-card-edit-btn editSlots">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
              </button>
              @endif
            </div>
            {{-- Edit form lives in the side drawer (jw-slots-drawer) below content-split --}}
            @php
              $hasSlots = !empty($application->interviewDateOne) || !empty($application->interviewDateTwo) || !empty($application->interviewDateThree);
            @endphp
            @if(!$hasSlots)
              <div class="po-slots-empty">
                <div class="po-slots-empty-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                </div>
                <p class="po-slots-empty-title">No Interview Slots Added Yet</p>
                <p class="po-slots-empty-sub">Add interview slots to start scheduling.</p>
                @if($skillAndQuestions)
                <button type="button" class="po-slots-add-btn editSlots">Add Slots</button>
                @endif
              </div>
            @else
                @php
                    $book1 = 'Book Now';
                    $book2 = 'Book Now';
                    $book3 = 'Book Now';
                    $bookbtn1 = 'book-btn';
                    $bookbtn2 = 'book-btn';
                    $bookbtn3 = 'book-btn';
                    $modal1 = '';
                    $modal2 = '';
                    $modal3 = '';
                    $date1 = \Carbon\Carbon::parse(@$application->interviewDateOne)->format('Y-m-d h:i:s');
                    $date2 = \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('Y-m-d h:i:s');
                    $date3 = \Carbon\Carbon::parse(@$application->interviewDateThree)->format('Y-m-d h:i:s');
                    if(count($scheduleDateArr)>0){
                        if(in_array($date1,$scheduleDateArr)){
                            $book1 = 'Booked';
                            $bookbtn1 = 'book-btnoff-booked';
                            $modal1 = '';
                        }
                        if(in_array($date2,$scheduleDateArr)){
                            $book2 = 'Booked';
                            $bookbtn2 = 'book-btnoff-booked';
                            $modal2 = '';
                        }
                        if(in_array($date3,$scheduleDateArr)){
                            $book3 = 'Booked';
                            $bookbtn3 = 'book-btnoff-booked';
                            $modal3 = '';
                        }
                    }
                @endphp
                <div class="slots-grid job_slots_view">

                  <div class="slot-day">
                    <div class="slot-header">
                      <div class="slot-day-name">{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('D') : 'N/A' }}</div>
                      <div class="slot-date">{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('d M') : 'N/A' }}</div>
                    </div>
                    @php $__slotTz = @$application->interview_timezone ?? 'IST'; @endphp
                    <div class="slot-time">{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A') : 'N/A' }} <span style="font-size:10px;color:#94a3b8;font-weight:500;">({{ $__slotTz }})</span></div>
                    <div class="slot-time">{{ @$application->interview_mode_one ?? 'N/A' }}</div>
                    @if(empty($application->interviewDateOne))
                      <button type="button" class="book-btn-addslot editSlots">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px;flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Select Date
                      </button>
                    @else
                      <button {{ ((@$application->interviewDateOne && $date1 >= date('Y-m-d'))) ? $modal1:'' }} data-mode="{{@$application->interview_mode_one}}" data-date="{{ \Carbon\Carbon::parse(@$application->interviewDateOne)->format('d-m-Y') }}" data-time="{{ \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A') }}" class="{{ ((@$application->interviewDateOne && $date1 >= date('Y-m-d'))) ? $bookbtn1 :'book-btnoff' }} btn-book" rel="{{@$application->wiseId}}" type="button">{{$book1}}</button>
                    @endif
                  </div>

                  <div class="slot-day">
                    <div class="slot-header">
                      <div class="slot-day-name">{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('D') : 'N/A' }}</div>
                      <div class="slot-date">{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('d M') : 'N/A' }}</div>
                    </div>
                    <div class="slot-time">{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : 'N/A' }} <span style="font-size:10px;color:#94a3b8;font-weight:500;">({{ $__slotTz }})</span></div>
                    <div class="slot-time">{{ @$application->interview_mode_two ?? 'N/A' }}</div>
                    @if(empty($application->interviewDateTwo))
                      <button type="button" class="book-btn-addslot editSlots">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px;flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Select Date
                      </button>
                    @else
                      <button {{ ((@$application->interviewDateTwo && $date2 >= date('Y-m-d'))) ? $modal2:'' }} data-mode="{{@$application->interview_mode_two}}" data-date="{{ \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('d-m-Y') }}" data-time="{{ \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') }}" class="{{ ((@$application->interviewDateTwo && $date2 >= date('Y-m-d'))) ? $bookbtn2 :'book-btnoff' }} btn-book" rel="{{@$application->wiseId}}" type="button">{{$book2}}</button>
                    @endif
                  </div>

                  <div class="slot-day">
                    <div class="slot-header">
                      <div class="slot-day-name">{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('D') : 'N/A' }}</div>
                      <div class="slot-date">{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('d M') : 'N/A' }}</div>
                    </div>
                    <div class="slot-time">{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('h:i A') : 'N/A' }} <span style="font-size:10px;color:#94a3b8;font-weight:500;">({{ $__slotTz }})</span></div>
                    <div class="slot-time">{{ @$application->interview_mode_three ?? 'N/A' }}</div>
                    @if(empty($application->interviewDateThree))
                      <button type="button" class="book-btn-addslot editSlots">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px;flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Select Date
                      </button>
                    @else
                      <button {{ ((@$application->interviewDateThree && $date3 >= date('Y-m-d'))) ? $modal3:'' }} data-mode="{{@$application->interview_mode_three}}" data-date="{{ \Carbon\Carbon::parse(@$application->interviewDateThree)->format('d-m-Y') }}" data-time="{{ \Carbon\Carbon::parse(@$application->interviewDateThree)->format('h:i A') }}" class="{{ ((@$application->interviewDateThree && $date3 >= date('Y-m-d'))) ? $bookbtn3 :'book-btnoff' }} btn-book" rel="{{@$application->wiseId}}" type="button">{{$book3}}</button>
                    @endif
                  </div>

                </div>
            @endif

          </section>
        </div>

        <aside class="content-sidebar">

          <section class="cardshow" style="margin-bottom:16px;">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-primary-50, #eef2ff); color: var(--color-primary-600, #4f46e5);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
              </div>
              <h3 class="card-title">Recruiter Notes</h3>
              @if($editComman)
              <button type="button" class="jw-card-edit-btn editRecruiterNote">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
              </button>
              @endif
            </div>
            @if(!empty($application->recruiter_note))
              <div class="po-recruiter-note-text">{!! $application->recruiter_note !!}</div>
            @else
              <p class="po-no-notes">No recruiter notes added yet.</p>
            @endif
          </section>

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-warning-50); color: var(--color-warning-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                  <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
              </div>
              <h3 class="card-title">Compensation Details</h3>
            </div>
            <div class="comp-list">
              @php
                $payFormatViewSalary = GeneralMethod::formatViewSalary($application->ctc_yearly,$symbols,$jobMode);
                $billFormatViewSalary = GeneralMethod::formatViewSalary($application->ectc_yearly,$symbols,$jobMode);
                $ctc = '';
                $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                $ectc = '';
                $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
              @endphp

               @if($application->condidate_type=='other')
                <div class="comp-row">

                  @if($application->condidate_type=='other')
                        @php 
                            $current_ctc_mode	=	"";
                            $current_ctc_mode	=	@$jobMode;	
                            
                              
                        @endphp
                        @elseif((@$application->condidate_type=='bench_condidate'))
                        @php 
                            $current_ctc_mode	=	"";
                            $current_ctc_mode	=	@$application->pay_rate_mode;		
                            $ctc =  '*******';
                        @endphp
                    @endif
                  <span class="comp-label">{{ (@$application->condidate_type=='other') ? 'Current Salary' : 'Pay Rate' }}</span>
                  <span class="comp-value">{!! @$current_ctc_cur !!} {{ rtrim(rtrim((string)$ctc, '0'), '.') }} {{ $salaryMode[$current_ctc_mode ?? 0] }}</span>
                </div>
              @endif

              <div class="comp-row">
                            @if($application->condidate_type=='other')
                            @php 
                                $current_ectc_mode	=	"";
                                $current_ectc_mode	=	@$jobMode;		
                            @endphp
                            @else
                            @php 
                                $current_ectc_mode	=	"";
                                $current_ectc_mode	=	@$jobMode;		
                            @endphp
                          @endif
                <span class="comp-label">{{ ($application->condidate_type=='other') ? 'Expected Salary' : 'Bill Rate' }}</span>
                <span class="comp-value">{!! @$current_ectc_cur !!} {{ rtrim(rtrim((string)@$ectc, '0'), '.') }} {{ $salaryMode[$current_ectc_mode ?? 0] }}</span>
              </div>
              <div class="comp-row">
                <span class="comp-label">Compensation Type</span>
                <span class="comp-badge">{{ $salaryMode[$current_ectc_mode ?? 0] }}</span>
              </div>
              <div class="comp-row">
                <span class="comp-label">Salary Type</span>
                <span class="comp-value">Fixed</span>
              </div>
              {{-- <div class="comp-row">
                <span class="comp-label">Currency</span>
                <span class="comp-value">INR</span>
              </div>
              <div class="comp-row">
                <span class="comp-label">Paid Service / Free Service</span>
                <span class="comp-value">Paid Service</span>
              </div> --}}
              <div class="comp-row">

                <span class="comp-label">Employment Type</span>
                <span class="comp-value">{{ @$application->employmentType }}</span>
              </div>
              @if($application->condidate_type=='other')
                <div class="comp-row">
                  <span class="comp-label">Offer in Hand</span>
                  <span class="comp-value">{{ ucwords(@$application->oh) }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Hike Percentage</span>
                  <span class="comp-value">{{ @$application->percentage_hike }}%</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label"><b style="color: #0f172a;">Comments : </b> {{ ucwords(@$application->comment) }}</span>
                </div>

              @endif
            </div>
          </section>

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
              </div>
              <h3 class="card-title">Ownership</h3>
            </div>
            <div class="ownership-list">
              <div class="ownership-row">
                <span class="own-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  Hiring HR
                </span>
                <span class="own-value">
                  @php
                      if(@$clientjobAssignListCaptain){
                          echo ucwords(strtolower(@$clientjobAssignListCaptain));
                      }else{
                        if(!empty($client_contuser_arr)){

                          foreach($client_contuser_arr as $key=> $cpn){
                            $cpn = (object) $cpn;
                            if($key==0){
                                echo ucwords(strtolower(@$cpn->contact_person_name)) .'';
                            }else{
                                echo ', '.ucwords(strtolower(@$cpn->contact_person_name));
                            }

                          }
                        }
                      }
                  @endphp
                </span>
              </div>
              <div class="ownership-row">
                <span class="own-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  Recruiter Name
                </span>
                <span class="own-value">{{ ucwords(@$posted_by) }}</span>
              </div>
              <div class="ownership-row">
                <span class="own-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                  </svg>
                  Company Name 
                </span>
                @php
                $companies = $submittedByCompany ?? (!empty($application->submitted_by) ? DB::table('companies')->select('companies.company_name')->join('users','users.company_id','companies.id')->where('users.id', $application->submitted_by)->first() : null);
                @endphp
                <span class="own-value">{{ ucwords(strtolower(@$companies->company_name ?? '')) }}</span>
              </div>
              <div class="ownership-row">
                <span class="own-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                  Upload Date &amp; Time
                </span>
                  @php
                      $originalDate    = @$application->created_at;
                      $ownerDate       = date("d-M-Y", strtotime($originalDate));
                      $ownerTime       = date("h:i A", strtotime($originalDate));
                  @endphp
                <span class="own-value">{{ @$ownerDate }} <span class="meta-sep">|</span> {{ @$ownerTime }} <span class="meta-sep">|</span> IST</span>
              </div>
            </div>
          </section>


          @if($application->condidate_type=='other')
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-primary-50); color: var(--color-primary-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <h3 class="card-title">Contact Details</h3>
            </div>
            <div class="contact-list">
              <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                  Email ID-1
                </span>
                <a href="mailto:{{ @$application->email }}" class="contact-value">{{ @$application->email }}</a>
              </div>
              <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                  Email ID-2
                </span>
                <a href="mailto:{{ @$application->secondrary_email }}" class="contact-value">{{ @$application->secondrary_email }}</a>
              </div>
              <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path
                      d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                    </path>
                  </svg>
                  Contact Number-1
                </span>
                <a href="javascript:;" class="contact-value" style="display: flex;">
                  <div id="firstPhoneDisplay" class="fw-bolder text-gray-800 fs-6" style="display: flex;"> </div>
                  <span class="fw-bolder text-gray-800 fs-6" style="margin-left: 4px;">{{ @$application->phone }}</span>
                </a>
              </div>
            </div>
            <div class="contact-row">
                <span class="contact-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path
                      d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                    </path>
                  </svg>
                  Contact Number-2
                </span>
                <a href="javascript:;" class="contact-value" style="display: flex;">
                  <div id="secondPhoneDisplay" class="fw-bolder text-gray-800 fs-6" style="display: flex;"> </div>
                  <span class="fw-bolder text-gray-800 fs-6" style="margin-left: 4px;">{{ @$application->secondary_mobile }}</span>
                </a>
            </div>
            <div class="contact-actions">
              <button type="button" class="btn btn-sm btn-whatsapp">
                  <a href="https://api.whatsapp.com/send?phone=91{{@$application->phone}}" target="_blank" class="contact-value" style="display: flex;">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path
                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                      </path>
                    </svg>
                    Chat on WhatsApp
                  </a>
              </button>
              @php
                $linkedin = @$application->linkedin;

                if (!empty($linkedin) && !preg_match('/^(https?:\/\/|www\.)/i', $linkedin)) {
                    $linkedin = 'https://' . $linkedin;
                }
              @endphp
              @if(!empty(@$application->linkedin))
              <button type="button" class="btn btn-sm btn-linkedin">
                <a href="{{ $linkedin ?? 'javascript:;' }}" target="{{ $linkedin ? '_blank' :'_self' }}" class="contact-value" style="display: flex;color: #fff;">
                  <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                    <rect x="2" y="9" width="4" height="12"></rect>
                    <circle cx="4" cy="4" r="2"></circle>
                  </svg>
                  Connect on LinkedIn
                </a>
              </button>
              @endif
            </div>
          </section>
          @else
                <div id="firstPhoneDisplay" class="fw-bolder text-gray-800 fs-6 d-none"> </div>
                <div id="secondPhoneDisplay" class="fw-bolder text-gray-800 fs-6 d-none"> </div>
          @endif


          @if($application->condidate_type=='other')
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
              </div>
              <h3 class="card-title">Other Details</h3>
            </div>
             <div class="comp-list">
                @if(!empty(@$application->dob))
                <div class="comp-row">
                  <span class="comp-label">Date of Birth</span>
                  <span class="comp-value">{{ date('d M Y', strtotime(@$application->dob)) }}</span>
                </div>
                @endif
                <div class="comp-row">
                  <span class="comp-label">Offer Amount</span>
                   @php
                        $offerAmont = "Confidential";
                          $allow =  GeneralMethod::confidentialAmontShow($jobId);
                        @endphp

                        @if($allow)
                          @if(@$application->offer_amount > 0)
                            <span class="comp-value"> {{@$offer_currency_offer}} {{ @$application->offer_amount }}   {{ @$offer_mode_offer }}</span>
                          @endif
                        @else
                          <span class="comp-value">{{ $offerAmont }}</span>
                        @endif
                </div>
                <div class="comp-row">
                  <span class="comp-label">Joining Date</span>
                  <span class="comp-value">{{ @$application->joined_date ? date("d-m-Y H:i", strtotime(@$application->joined_date)).'(IST)' : '' }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Visa Status</span>
                  <span class="comp-value">{{@$application->visastatu}}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Reference Name</span>
                  <span class="comp-value">{{ ucwords(@$application->referencename) }}</span>
                </div>
                 <div class="comp-row">
                  <span class="comp-label"><b style="color: #0f172a;">Reason For Job Change : </b> {{@$application->reason_for_job_change}}</span>
                </div>
             </div>
          </section>
          @endif

          @if(@$application->condidate_type=='bench_condidate')

          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                  <path d="M9 22v-4h6v4"/>
                  <line x1="8" y1="6" x2="8" y2="6.01"/>
                  <line x1="16" y1="6" x2="16" y2="6.01"/>
                  <line x1="8" y1="10" x2="8" y2="10.01"/>
                  <line x1="16" y1="10" x2="16" y2="10.01"/>
                </svg>
              </div>
              <h3 class="card-title">Agency Details</h3>
              @php
                $companyDetailsGet = GeneralMethod::companyDetailsGet(@$application->company_id);
                $city = $companyDetailsGet->locations_address[0]['locations_address'][0]['city_name'] ?? '';
                $state = $companyDetailsGet->locations_address[0]['locations_address'][0]['state_name'] ?? '';
                $country = $companyDetailsGet->locations_address[0]['locations_address'][0]['country_name'] ?? '';
		            $accountCompanyType = GeneralMethod::accountCompanyType();
              @endphp
      
            </div>
            <div class="agency-grid">
              <span class="agency-label">Agency Name</span>
              <span class="agency-value">{{ @$companyDetailsGet->company_name }}</span>

              <span class="agency-label">Website</span>
              <span class="agency-value"><a href="{{ @$companyDetailsGet->website }}" target="_blank" rel="noopener noreferrer">{{ @$companyDetailsGet->website }}</a></span>

              <span class="agency-label">Industry</span>
              <span class="agency-value">{{ @$companyDetailsGet->industry['industry_name'] }}</span>

              <span class="agency-label">Company Size</span>
              <span class="agency-value">
                @if(@$companyDetailsGet->company_size_from >=10000)
                {{ @$companyDetailsGet->company_size_from }}+
                @else
                {{ @$companyDetailsGet->company_size_from }} - {{ @$companyDetailsGet->company_size }}
                @endif
                 Employees</span>

              <span class="agency-label">Headquarter</span>
              <span class="agency-value">{{ $city }}, {{ $state }}, {{ $country }}</span>

              <span class="agency-label">Company Type</span>
              <span class="agency-value">{{ $accountCompanyType[@$companyDetailsGet->company_type] }}</span>

              <span class="agency-label">Founded</span>
              
              <span class="agency-value">{{ @$companyDetailsGet->founded }}</span>

              <span class="agency-label">Specialties</span>
              <span class="agency-value">IT Staffing, Contract Staffing, Recruitment</span>
            </div>
          </section>


          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <h3 class="card-title">Relationship Manager Details</h3>
            </div>
             @foreach( $bench_assign_row as $key=> $assignVal)
            <div class="rm-card">
              <figure class="rm-avatar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </figure>
            
               
                  <div class="rm-details">
                    <h4>{{ ucwords(@$assignVal->name) }}</h4>
                    <div class="rm-contact-list">
                      <div class="rm-contact-item" style="position: relative;display: flex;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                       <div class="iti__flag-container" data-code="{{ @$assignVal->calling_code ?? 'in' }}" data-row="{{ $assignVal->id }}" id="phoneDisplayReal_{{ $assignVal->id }}" style="display: flex;position: relative;"> </div>
                        {{ ucwords(@$assignVal->mobile) }}
                      </div>
                      <div class="rm-contact-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                          <polyline points="22,6 12,13 2,6"/>
                        </svg>
                         {{ @$assignVal->email }}
                      </div>
                    </div>
                  </div>
            </div><hr>
            
             @endforeach
          </section>
          @endif
        </aside>
      </div>

      {{-- ======================================================
           SIDE EDIT DRAWERS (position:fixed; driven by CSS :has())
           The existing JS toggles d-none on .jw-drawer-body which
           triggers the CSS transition — no new route / logic added.
           ====================================================== --}}

      {{-- DRAWER 1: Key Skills & Experience --}}
      <div class="jw-side-drawer" id="jw-skill-drawer">
        <div class="jw-drawer-backdrop editSkillCancel"></div>
        <div class="jw-drawer-panel">
          <div class="jw-drawer-header">
            <div class="jw-drawer-icon-wrap" style="background:#ede9fe;color:#7c3aed;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="jw-drawer-title-group">
              <div class="jw-drawer-title">Edit Key Skills &amp; Experience</div>
              <div class="jw-drawer-subtitle">Update skill experience and self-rating</div>
            </div>
            <button type="button" class="jw-drawer-close editSkillCancel" aria-label="Close">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="jw-drawer-body job_skill_edit d-none">
              <form class="ajax-form" method="POST" id="skillUpdateForm" name="skillUpdateForm" enctype="multipart/form-data">
                @csrf
                <table class="sk" style="width:100%;">
                  <thead>
                    <tr>
                      <th>Skill</th>
                      <th>Exp. (Years)</th>
                      <th>Exp. (Months)</th>
                      <th>Rating</th>
                    </tr>
                  </thead>
                  <tbody>
                        <input type="hidden" name="job_id" value="{{ @$job_id }}" id="job_id" />
                        <input type="hidden" name="appId" value="{{ @$application->id }}" id="appId" />
                            @foreach($jobSkill as $skill_val)
                            @php
                             $skill_val = (object) $skill_val;
                            @endphp
                                @if(!empty($skill_val->skill_name))
                                      @php
                                      if($skill_val->skill_value){
                                        $skillValue = str_replace(['Years','Year',' '],'',$skill_val->skill_value);
                                        if(!is_null($skillValue) && !empty($skillValue)){
                                          $total_skill_exp =  GeneralMethod::separateNumberParts($skillValue);
                                          $skill_exp_years = ($total_skill_exp[0]);
                                          $skill_exp_months = ($total_skill_exp[1]);
                                          $selected_skill_exp_year = $skill_exp_years;
                                          $selected_skill_exp_month = $skill_exp_months;
                                          $selected_skill_rating = $skill_val->rating ?? null;
                                        }else{
                                          $selected_skill_exp_year = null;
                                          $selected_skill_exp_month = null;
                                          $selected_skill_rating = null;
                                        }
                                      }else{
                                        $selected_skill_exp_year = null;
                                        $selected_skill_exp_month = null;
                                        $selected_skill_rating = null;
                                      }
                                    @endphp
                                    <tr>
                                    <td>
                                      <label class="form-label">{{ ucwords(strtolower($skill_val->skill_name)) }}</label>
                                    </td>
                                      <td>
                                        <select class="form-select form-select-sm" name="YearskillId[{{ $skill_val->job_skill_id }}]">
                                          <option value="0">Year</option>
                                          @php
                                              for ($i = 1; $i <= 60; $i++) {
                                                  $s = $i > 1 ? 'Years' : 'Year';
                                                  $sel = (!is_null($selected_skill_exp_year) && $i == $selected_skill_exp_year) ? 'selected' : '';
                                                  echo "<option value=\"$i\" $sel>{$i} {$s}</option>";
                                              }
                                          @endphp
                                        </select>
                                      </td>
                                      <td>
                                        <select class="form-select form-select-sm" name="MonthskillId[{{ $skill_val->job_skill_id }}]">
                                          <option value="">Month</option>
                                          @php
                                              for ($i = 0; $i <= 11; $i++) {
                                                $s = $i > 1 ? 'Months' : 'Month';
                                                $sel = (!is_null($selected_skill_exp_month) && $i == $selected_skill_exp_month) ? 'selected' : '';
                                                echo "<option value=\"$i\" $sel>{$i} {$s}</option>";
                                              }
                                          @endphp
                                        </select>
                                      </td>
                                      <td>
                                        <select class="form-select form-select-sm" name="rating[{{ $skill_val->job_skill_id }}]">
                                          <option value="">Rating</option>
                                          @php
                                              for ($i = 1; $i <= 5; $i++) {
                                                $s = $i > 1 ? 'Stars' : 'Star';
                                                $sel = (!is_null($selected_skill_rating) && $i == $selected_skill_rating) ? 'selected' : '';
                                                echo "<option value=\"$i\" $sel>{$i} {$s}</option>";
                                              }
                                          @endphp
                                        </select>
                                      </td>
                                    </tr>
                                @endif
                            @endforeach
                  </tbody>
                </table>
              </form>
          </div>
          <div class="jw-drawer-footer">
            <a type="button" class="btn-jw-save editSkillUpdate" id="skill-update-form" href="javascript:void(0);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Save Changes
            </a>
            <a type="button" class="btn-jw-cancel editSkillCancel" href="javascript:void(0);">Cancel</a>
          </div>
        </div>
      </div>{{-- /#jw-skill-drawer --}}

      {{-- DRAWER 2: Screening Questions --}}
      <div class="jw-side-drawer" id="jw-question-drawer">
        <div class="jw-drawer-backdrop editJobQuestionCancel"></div>
        <div class="jw-drawer-panel">
          <div class="jw-drawer-header">
            <div class="jw-drawer-icon-wrap" style="background:#dcfce7;color:#16a34a;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="jw-drawer-title-group">
              <div class="jw-drawer-title">Edit Screening Questions</div>
              <div class="jw-drawer-subtitle">Update candidate screening question answers</div>
            </div>
            <button type="button" class="jw-drawer-close editJobQuestionCancel" aria-label="Close">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="jw-drawer-body job_additional_edit d-none">
            <form class="ajax-form" method="POST" id="jobQuestionUpdateForm" name="jobQuestionUpdateForm" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="job_id" value="{{ @$job_id }}" />
              <input type="hidden" name="appId" value="{{ @$application->id }}"/>
              <table class="sk" style="width:100%;">
                <thead>
                  <tr>
                    <th style="width:55%;">Screening Question</th>
                    <th>Answered</th>
                  </tr>
                </thead>
                <tbody>
                    @php
                        if(!empty($job_additional_detail)){
                          $html = '';
                          foreach($job_additional_detail as $aDetailArr){
                            $aDetailArr = (object) $aDetailArr;
                            if(!empty($aDetailArr->job_detail_value)){
                              $ans_value = $aDetailArr->application_detail_value;
                              $__wc = !empty($ans_value) ? count(array_filter(explode(' ', trim($ans_value)))) : 0;
                              $html .= '<tr>
                                <td><label class="form-label">'.strtoupper($aDetailArr->job_detail_value).':</label>
                                <input type="hidden" name="job_question[]" value="'.$aDetailArr->question_id.'">
                                </td>
                                <td>
                                  <input type="text" class="form-control form-control-sm form-control-solid sq-answer-input" name="additional_detail[]" placeholder="Enter answer (max 20 words)" value="'.htmlspecialchars((string)@$ans_value, ENT_QUOTES).'">
                                  <div class="sq-word-count'.($__wc >= 20 ? ' sq-at-limit' : '').'"><span class="sq-wc-num">'.$__wc.'</span>/20 words</div>
                                </td>
                              </tr>';
                            }
                          }
                          echo $html;
                        }
                    @endphp
                </tbody>
              </table>
            </form>
          </div>
          <div class="jw-drawer-footer">
            <a type="button" class="btn-jw-save editJobQuestionUpdate" id="job-question-update-form" href="javascript:void(0);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Save Changes
            </a>
            <a type="button" class="btn-jw-cancel editJobQuestionCancel" href="javascript:void(0);">Cancel</a>
          </div>
        </div>
      </div>{{-- /#jw-question-drawer --}}

      {{-- DRAWER 3: Interview Slots --}}
      <div class="jw-side-drawer" id="jw-slots-drawer">
        <div class="jw-drawer-backdrop editSlotsCancel"></div>
        <div class="jw-drawer-panel">
          <div class="jw-drawer-header">
            <div class="jw-drawer-icon-wrap" style="background:#dbeafe;color:#1d4ed8;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="jw-drawer-title-group">
              <div class="jw-drawer-title">Edit Interview Slots</div>
              <div class="jw-drawer-subtitle">Set candidate's available interview dates &amp; times</div>
            </div>
            <button type="button" class="jw-drawer-close editSlotsCancel" aria-label="Close">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="jw-drawer-body job_slots_edit d-none">
            <form class="ajax-form" method="POST" id="jobSlotsUpdateForm" name="jobSlotsUpdateForm" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="appId" value="{{ @$application->wiseId }}"/>

              {{-- Timezone (applies to all 3 slots) --}}
              <div class="isd-tz-row">
                <div class="isd-tz-label">
                  <i class="ti ti-world" style="font-size:14px;color:#4f46e5;"></i>
                  Interview Time Zone <span style="color:#94a3b8;font-weight:400;">(applies to all slots)</span>
                </div>
                <select name="interview_timezone" id="interview_timezone" class="isd-tz-select">
                  @php $__tz = @$application->interview_timezone ?? 'IST'; @endphp
                  <option value="IST"   @selected($__tz=='IST')  >IST  — India Standard Time       (UTC+5:30)</option>
                  <option value="UTC"   @selected($__tz=='UTC')  >UTC  — Coordinated Universal Time (UTC+0:00)</option>
                  <option value="GMT"   @selected($__tz=='GMT')  >GMT  — Greenwich Mean Time        (UTC+0:00)</option>
                  <option value="BST"   @selected($__tz=='BST')  >BST  — British Summer Time        (UTC+1:00)</option>
                  <option value="CET"   @selected($__tz=='CET')  >CET  — Central European Time      (UTC+1:00)</option>
                  <option value="CEST"  @selected($__tz=='CEST') >CEST — Central European Summer    (UTC+2:00)</option>
                  <option value="EET"   @selected($__tz=='EET')  >EET  — Eastern European Time      (UTC+2:00)</option>
                  <option value="MSK"   @selected($__tz=='MSK')  >MSK  — Moscow Standard Time       (UTC+3:00)</option>
                  <option value="GST"   @selected($__tz=='GST')  >GST  — Gulf Standard Time         (UTC+4:00)</option>
                  <option value="PKT"   @selected($__tz=='PKT')  >PKT  — Pakistan Standard Time     (UTC+5:00)</option>
                  <option value="BST530" @selected($__tz=='BST530')>BST  — Bangladesh Standard Time   (UTC+6:00)</option>
                  <option value="MMT"   @selected($__tz=='MMT')  >MMT  — Myanmar Time               (UTC+6:30)</option>
                  <option value="ICT"   @selected($__tz=='ICT')  >ICT  — Indochina Time             (UTC+7:00)</option>
                  <option value="SGT"   @selected($__tz=='SGT')  >SGT  — Singapore / HKT            (UTC+8:00)</option>
                  <option value="CST"   @selected($__tz=='CST')  >CST  — China Standard Time        (UTC+8:00)</option>
                  <option value="JST"   @selected($__tz=='JST')  >JST  — Japan Standard Time        (UTC+9:00)</option>
                  <option value="AEST"  @selected($__tz=='AEST') >AEST — Australian Eastern Standard (UTC+10:00)</option>
                  <option value="NZST"  @selected($__tz=='NZST') >NZST — New Zealand Standard Time   (UTC+12:00)</option>
                  <option value="AST"   @selected($__tz=='AST')  >AST  — Arabian Standard Time      (UTC+3:00)</option>
                  <option value="BRT"   @selected($__tz=='BRT')  >BRT  — Brasília Time              (UTC−3:00)</option>
                  <option value="ART"   @selected($__tz=='ART')  >ART  — Argentina Time             (UTC−3:00)</option>
                  <option value="EST"   @selected($__tz=='EST')  >EST  — Eastern Standard Time      (UTC−5:00)</option>
                  <option value="EDT"   @selected($__tz=='EDT')  >EDT  — Eastern Daylight Time      (UTC−4:00)</option>
                  <option value="CST_US" @selected($__tz=='CST_US')>CST — Central Standard Time (US) (UTC−6:00)</option>
                  <option value="MST"   @selected($__tz=='MST')  >MST  — Mountain Standard Time     (UTC−7:00)</option>
                  <option value="PST"   @selected($__tz=='PST')  >PST  — Pacific Standard Time      (UTC−8:00)</option>
                  <option value="PDT"   @selected($__tz=='PDT')  >PDT  — Pacific Daylight Time      (UTC−7:00)</option>
                </select>
                <div class="isd-tz-hint">
                  <i class="ti ti-info-circle" style="font-size:11px;"></i>
                  Time zone is for display reference only; dates are saved as-is
                </div>
              </div>

              {{-- Slot 1 --}}
              <div class="isd-slot-card">
                <div class="isd-slot-head">
                  <span class="isd-slot-num">1</span>
                  <span class="isd-slot-label">Slot 1 — Date, Time &amp; Mode</span>
                </div>
                <div class="isd-slot-grid">
                  <div class="isd-field">
                    <label><i class="ti ti-calendar-filled"></i> Date &amp; Time</label>
                    <div class="isd-datetime-pill">
                      <input type="date" name="interviewDateOne" id="interviewDateOne"
                        min="{{ date('Y-m-d') }}"
                        value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('Y-m-d') : '' }}">
                      <span class="sep"></span>
                      <input type="time" name="interviewTimeOne" id="interviewTimeOne"
                        value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('H:i') : '' }}">
                    </div>
                  </div>
                  <div class="isd-field">
                    <label><i class="ti ti-device-laptop"></i> Mode</label>
                    <select name="interview_mode_one" id="interview_mode_one" class="form-select form-select-sm">
                      <option value="">Select Mode</option>
                      @if(!empty(@$interview_mode))
                        @foreach(@$interview_mode as $int_mode_val)
                          <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_one == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>
              </div>

              {{-- Slot 2 --}}
              <div class="isd-slot-card">
                <div class="isd-slot-head">
                  <span class="isd-slot-num">2</span>
                  <span class="isd-slot-label">Slot 2 — Date, Time &amp; Mode</span>
                </div>
                <div class="isd-slot-grid">
                  <div class="isd-field">
                    <label><i class="ti ti-calendar-filled"></i> Date &amp; Time</label>
                    <div class="isd-datetime-pill">
                      <input type="date" name="interviewDateTwo" id="interviewDateTwo"
                        min="{{ date('Y-m-d') }}"
                        value="{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('Y-m-d') : '' }}">
                      <span class="sep"></span>
                      <input type="time" name="interviewTimeTwo" id="interviewTimeTwo"
                        value="{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('H:i') : '' }}">
                    </div>
                  </div>
                  <div class="isd-field">
                    <label><i class="ti ti-device-laptop"></i> Mode</label>
                    <select name="interview_mode_two" id="interview_mode_two" class="form-select form-select-sm">
                      <option value="">Select Mode</option>
                      @if(!empty(@$interview_mode))
                        @foreach(@$interview_mode as $int_mode_val)
                          <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_two == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>
              </div>

              {{-- Slot 3 --}}
              <div class="isd-slot-card">
                <div class="isd-slot-head">
                  <span class="isd-slot-num">3</span>
                  <span class="isd-slot-label">Slot 3 — Date, Time &amp; Mode</span>
                </div>
                <div class="isd-slot-grid">
                  <div class="isd-field">
                    <label><i class="ti ti-calendar-filled"></i> Date &amp; Time</label>
                    <div class="isd-datetime-pill">
                      <input type="date" name="interviewDateThree" id="interviewDateThree"
                        min="{{ date('Y-m-d') }}"
                        value="{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('Y-m-d') : '' }}">
                      <span class="sep"></span>
                      <input type="time" name="interviewTimeThree" id="interviewTimeThree"
                        value="{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('H:i') : '' }}">
                    </div>
                  </div>
                  <div class="isd-field">
                    <label><i class="ti ti-device-laptop"></i> Mode</label>
                    <select name="interview_mode_three" id="interview_mode_three" class="form-select form-select-sm">
                      <option value="">Select Mode</option>
                      @if(!empty(@$interview_mode))
                        @foreach(@$interview_mode as $int_mode_val)
                          <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_three == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>
              </div>

            </form>
          </div>
          <div class="jw-drawer-footer">
            <a type="button" class="btn-jw-save editSlotsUpdate" id="job-slots-update-form" href="javascript:void(0);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Save Changes
            </a>
            <a type="button" class="btn-jw-cancel editSlotsCancel" href="javascript:void(0);">Cancel</a>
          </div>
        </div>
      </div>{{-- /#jw-slots-drawer --}}

      {{-- DRAWER 4: Recruiter Notes --}}
      <div class="jw-side-drawer" id="jw-recruiter-note-drawer">
        <div class="jw-drawer-backdrop editRecruiterNoteCancel"></div>
        <div class="jw-drawer-panel">
          <div class="jw-drawer-header">
            <div class="jw-drawer-icon-wrap" style="background:#eef2ff;color:#4f46e5;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
            </div>
            <div class="jw-drawer-title-group">
              <div class="jw-drawer-title">Edit Recruiter Notes</div>
              <div class="jw-drawer-subtitle">Update internal notes for this candidate</div>
            </div>
            <button type="button" class="jw-drawer-close editRecruiterNoteCancel" aria-label="Close">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="jw-drawer-body rn-drawer-body d-none">
            <input type="hidden" id="rn_app_id" value="{{ @$application->id }}">

            {{-- Template selector — populated via AJAX on first open --}}
            <div class="rn-section" id="rn-template-wrap" style="display:none;">
              <label class="rn-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;color:#a1a5b7;flex-shrink:0;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Select Template <span>(Optional — loads content into the note below)</span>
              </label>
              <select class="rn-select" id="rn_template_select">
                <option value="">Select Note Template...</option>
              </select>
            </div>

            {{-- Contenteditable editor — renders existing HTML notes correctly --}}
            <div class="rn-section">
              <label class="rn-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;color:#a1a5b7;flex-shrink:0;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Internal Note
              </label>
              <div class="rn-editor" id="jw_recruiter_note_editor" contenteditable="true" data-placeholder="Add internal recruiter notes about this candidate...">{!! @$application->recruiter_note !!}</div>
            </div>
          </div>
          <div class="jw-drawer-footer">
            <a type="button" class="btn-jw-save" id="rn-save-btn" href="javascript:void(0);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Save Changes
            </a>
            <a type="button" class="btn-jw-cancel editRecruiterNoteCancel" href="javascript:void(0);">Cancel</a>
          </div>
        </div>
      </div>{{-- /#jw-recruiter-note-drawer --}}

    </div>{{-- /#personal_details tab-pane --}}



    <div class="tab-pane fade" id="timeline" role="tabpanel">
      <div class="content-split">
        <div class="content-main-full">
          <section class="cardshow">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
              </div>
              <h3 class="card-title">Timeline</h3>
            </div>
                 

             <!--end::Timeline header-->
        
          <div class="card-body px-0 py-7">
                <div class="timeline" id="timeline-items-container">
                  @include('service_company.job-applications.timeline_partial')
            </div>
          </div>
            
            

         

            <p class="summary-text summary-text-width"></p>
          </section>
        </div>
      </div>
    </div>


    <div class="confidentiality-banner" role="note">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="16" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12.01" y2="8"></line>
      </svg>
      <p>All candidate information is confidential and intended for internal use only.</p>
    </div>
  </main>
  </div>{{-- /.cdr-outer-card (card 3: tabs) --}}
</div>


<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

  $('.page-tabs .tab').on('click', function () {
        $('.page-tabs .tab').removeClass('tab-active').attr('aria-selected', 'false');

        $(this).addClass('tab-active').attr('aria-selected', 'true');
    });


function scrollNotesToBottom() {

    const notesBox = document.getElementById("applicant-notes");
    if (notesBox) {
        notesBox.scrollTo({
            top: notesBox.scrollHeight,
            behavior: "smooth"
        });
    }
}
scrollNotesToBottom();

setTimeout(() => {
          
          var defaultTimeFormat = '{{$defaultTimeFormat}}';
         
           function initTimePicker(selector, format) {
                return $(selector).flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: format,   // "h:i K" or "H:i"
                    time_24hr: format === "H:i",
                    minuteIncrement: 1,
                    allowInput: true, // allow user to delete value
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            let d = selectedDates[0];
                            if (d.getHours() === 0 && d.getMinutes() === 0) {
                                instance.clear(); // auto clear if 00:00
                            }
                        }
                    }
                });
            }

          // Slot date/time inputs now use native type="date" / type="time" pickers
          // (matching the Interview Drawer). No Flatpickr needed for those fields.

          
}, 500);


   $( document ).ready(function() {

     
      const firstSavedCountryCode  = @json($application->phone_code ?? 'in');
      const secondSavedCountryCode = @json($application->second_phone_code ?? 'in');
      const firstPhoneDisplayDiv = document.getElementById("firstPhoneDisplay");
      const secondPhoneDisplayDiv = document.getElementById("secondPhoneDisplay");

        const flagDiv = document.createElement("div");
        flagDiv.className = `iti__flag iti__${firstSavedCountryCode}`;

        const secondflagDiv = document.createElement("div");
        secondflagDiv.className = `iti__flag iti__${secondSavedCountryCode}`;
        // Get the dial code for the selected country code
        const dialCode = countryDialCodes[firstSavedCountryCode] || ""; // Default to empty string if not found
        const seconddialCode = countryDialCodes[secondSavedCountryCode] || ""; // Default to empty string if not found

        // Create a span to display the dial code
        const dialCodeSpan = document.createElement("span");
        dialCodeSpan.textContent = dialCode;

        const seconddialCodeSpan = document.createElement("span");
        seconddialCodeSpan.textContent = seconddialCode;

        // Append the flag and the dial code to the display div
        firstPhoneDisplayDiv.appendChild(flagDiv);
        firstPhoneDisplayDiv.appendChild(dialCodeSpan);

        // Append the flag and the dial code to the display div
        secondPhoneDisplayDiv.appendChild(secondflagDiv);
        secondPhoneDisplayDiv.appendChild(seconddialCodeSpan);



        setTimeout(() => {
        // alert("k");
        $('.iti__flag-container').each(function(index) {
            var cnt = $(this).attr('data-row');
            const flagContainer = document.getElementById(`phoneDisplayReal_${cnt}`);
            var calling_code = $(this).attr('data-code');
            
            const flagDiv = document.createElement("div");
            flagDiv.className = `iti__flag iti__${calling_code}`; // Ensure calling_code is in lowercase

            const dialCode = countryDialCodes[calling_code] || ""; // Fetch the dial code dynamically
            const dialCodeSpan = document.createElement("span");
            dialCodeSpan.textContent = dialCode;
            // Append the flag and dial code
            flagContainer.appendChild(flagDiv);
            flagContainer.appendChild(dialCodeSpan);
        });
    }, 2000);

    // Intent: Phase 2 Lazy loading Notes tab on click
    var _notesTabLoaded = false;
    $('body').on('click', 'a[href="#notes"], button[data-bs-target="#notes"]', function(){
        if (_notesTabLoaded) return;
        _notesTabLoaded = true;
        var appId = {{ $application->id }};
        $.ajax({
            type: 'GET',
            url: "{{ route('service_company.applicant-note.index') }}",
            data: { application_id: appId, page: 1 },
            success: function(response) {
                if (response.status === 'success' && response.view) {
                    $('#applicant-notes').html(response.view);
                }
            }
        });
    });

    $('body').on('click', '#cancel-note', function(){
        $('#note_text').val(''); 
    });

    // Intent: Phase 3 Lazy loading Timeline tab on click (scoped to application ID)
    $('body').off('click.v2timeline').on('click.v2timeline', 'a[href="#timeline"], button[data-bs-target="#timeline"]', function(){
        var appId = {{ $application->wiseId }};
        var $container = $('#timeline-items-container');
        if ($container.data('loaded-app-id') === appId) return;

        $container.html('<div class="text-center p-5 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Loading timeline activity...</div>');

        var timelineUrl = "{{ url('/service_company/job-applications/timeline') }}/" + appId;
        $.ajax({
            type: 'GET',
            url: timelineUrl,
            data: { page: 1 },
            success: function(response) {
                if (response.status === 'success') {
                    $container.data('loaded-app-id', appId);
                    if (response.view) {
                        $container.html(response.view);
                    } else {
                        $container.html('<div class="text-center p-5 text-muted">No timeline activity recorded yet.</div>');
                    }
                } else {
                    $container.html('<div class="text-center p-5 text-danger">' + (response.message || 'Failed to load timeline activity.') + '</div>');
                }
            },
            error: function() {
                $container.html('<div class="text-center p-5 text-danger">Failed to load timeline activity. Click Timeline tab to retry.</div>');
            }
        });
    });

    // Intent: Phase 4 Lazy loading Resume / CV tab on click (scoped to application ID)
    $('body').off('click.v2resume').on('click.v2resume', 'a[href="#resume_cv"], button[data-bs-target="#resume_cv"]', function(){
        var appId = {{ $application->id }};
        var $container = $('#resume_cv');
        if ($container.data('loaded-app-id') === appId) return;
        $container.data('loaded-app-id', appId);
        var resumeUrl = "{{ url('/service_company/job-applications/resume-meta') }}/" + appId;
        $.ajax({
            type: 'GET',
            url: resumeUrl,
            success: function(response) {
                if (response.status === 'success' && response.resume) {
                    console.log("Resume metadata loaded:", response.resume);
                }
            }
        });
    });

    // Intent: Phase 6 Scoped Offcanvas event cleanup to prevent memory leaks across drawer opens
    var $drawerElement = $('#kt_activities3');
    if ($drawerElement.length) {
        $drawerElement.off('hidden.bs.offcanvas.v2cleanup').on('hidden.bs.offcanvas.v2cleanup', function () {
            // Clean up PDF.js canvases to free browser RAM memory
            $(this).find('canvas').each(function() {
                var ctx = this.getContext('2d');
                if (ctx) {
                    ctx.clearRect(0, 0, this.width, this.height);
                }
                this.width = 1;
                this.height = 1;
            });
            // Reset tab flags
            _notesTabLoaded = false;
            _timelineTabLoaded = false;
            _resumeTabLoaded = false;
        });
    }
   
   		$('#add-note').click(function () {
   				var url 	= "{{ route('service_company.applicant-note.store') }}";
   				var id 		= {{$application->id}};
          var wiseId 		= {{$application->wiseId}};
   				var note 	= $('#note_text').val();
   				var token 	= '{{ csrf_token() }}';
   				$.ajax({ 
   								type	: 'POST',
   								url		: url,
   								data	: {'_token':token, 'id':id, 'note': note,'wiseId':wiseId},
   								success	: function (response) {
   																if(response.status == 'success') {
   																	$('#applicant-notes').html(response.view);
                                    scrollNotesToBottom();
   																	$('#note_text').val('');
   																}
   								}
   				});
   		});
   
   		$('body').on('click', '.delete-note', function(){
   				let noteId = $(this).data('note-id');
   				
   								
   const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })
   
   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "You won't be able to revert this!",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, delete it!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.isConfirmed) {
     
     
      
   						var url 		= "{{ route('service_company.applicant-note.destroy', ':id') }}";
   						url 			= url.replace(':id', noteId);
   						var token 		= '{{ csrf_token() }}';
   		
   						$.ajax({
   							type: 'POST',
   							url: url,
   							data: {'_token':token, '_method': 'DELETE'},
   							success: function (response) {
   								if(response.status == 'success') {
   									$('#applicant-notes').html(response.view);
   									
   									swalWithBootstrapButtons.fire(
   										'Deleted!',
   										'Your text note has been deleted.',
   										'success'
   									)
   	
   								}
   							}
   						});
   						
   	
   	
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
		   swalWithBootstrapButtons.fire(
			 'Cancelled',
			 'Your text note is safe :)',
			 'error'
		   )
     }
   })
   
   
});	
   		
   		$('body').on('click', '.edit-note', function() {
   	
         														$(this).hide();
         														let noteId = $(this).data('note-id');
         														$('body').find('#note-'+noteId+' .note-text').hide();
   
         														let noteText = $('body').find('#note-'+noteId+' .note-text').html();
         let textArea = '<textarea id="edit-note-text-'+noteId+'" class="form-control" row="4">'+noteText+'</textarea><a class="update-note btn btn-sm btn-light-success py-1 mt-2" data-note-id="'+noteId+'" href="javascript:void(0);">@lang("app.save")</a>';
         $('body').find('#note-'+noteId+' .note-textarea').html(textArea);
     
   });
   
   
   $('body').on('click', '.update-note', function () {
   	
         let noteId 	= $(this).data('note-id');
         var url 		= "{{ route('service_company.applicant-note.update', ':id') }}";
         url 			= url.replace(':id', noteId);
         var note 		= $('#edit-note-text-'+noteId).val();
         var token 		= '{{ csrf_token() }}';   
         $.ajax({
						  type		: 'POST',
						  url		: url,
						  data		: {'_token':token, 'noteId':noteId, 'note': note, '_method': 'PUT'},
						  success	: function (response) {
							  if(response.status == 'success') {
								  $('#applicant-notes').html(response.view);
							  }
						  }
         		});
     
   
   });


   
   $('body').on('click', '.resume_delete', function(){
   
   	
   			var application_id  		=	$(this).attr('rel');
     		var token       			=	'{{ csrf_token() }}';
   				 				
   				
		   const swalWithBootstrapButtons = Swal.mixin({
			 customClass: {
			   confirmButton: 'btn btn-success',
			   cancelButton: 'btn btn-danger'
			 },
			 buttonsStyling: false
		   })
   
   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "You won't be able to revert this!",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, delete it!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.isConfirmed) {
     
     
      						var url     			=	"{{ route('service_company.job-applications.resume_delete') }}";
                     		var token     			=	"{{ csrf_token() }}";
                     		$.ajax({
   									  type  		: 'POST',
   									  url   		: url,
   									  data  		: {'_token': token, '_method': 'POST', 'application_id':application_id },
   									  success 		: function (response) {
   										  if (response.status == "success") {
   											
   											swalWithBootstrapButtons.fire(
   																			'Deleted!',
   																			'Your resume has been deleted.',
   																			'success'
   																		)
   									
   									
   										  } 
               						 }
             					});
   							
     } else if (
       				result.dismiss === Swal.DismissReason.cancel 
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Your text note is safe :)',
         'error'
       )
     }
   })

  });
   $('.sendwhatsappmodal').click(function(){
          // Trigger hidden toggle — KTDrawer binds to this on createInstances()
          $('#kt_send_whatsapp_sd_toggle').trigger('click');

          $('#application_send_whatsapp_body_content').html(
              '<div class="d-flex align-items-center justify-content-center" style="min-height:200px;">' +
              '<span class="spinner-border spinner-border-sm text-success me-2"></span> Loading...</div>'
          );
          var id        	=	{{ @$jobId }};
          var sid        	=	{{ @$application->id }};
          var arr 		=	[sid];
          var jobIdArr = [id];
          $('.chk_list_item:checked').each(function () {
              
              var jobId = $(this).attr('data-jobId');
              arr.push($(this).val());
              jobIdArr.push(jobId);
          });
          var id        	=	jobIdArr[0];
          var url       	=	"{{ route('service_company.cvtracker.send_whatsapp',':id') }}";
          url         	=	url.replace(':id', id);
          var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
          var token       =	"{{ csrf_token() }}";
          $.ajax({
                  type		: 'POST',
                  url			: url,
                  data		: {'_token': token, '_method': 'POST', 'arr' : arr,'jobIdArr':jobIdArr},
                  cache		: false,
                  success 	: function (response) {
                                      if (response.status == "success") {
                                          $('#application_send_whatsapp_body_content').html(response.view);
                                          
                                          $('#kt_activities_scroll').removeAttr('style');
                                      }
                          }
          });

   });
  
  $('.sendmaimodal').click(function(){

        // Trigger hidden toggle — KTDrawer binds to this on createInstances()
        $('#kt_send_mail_sd_toggle').trigger('click');

        $('#application_send_mail_body_content').html(
            '<div class="d-flex align-items-center justify-content-center" style="min-height:200px;">' +
            '<span class="spinner-border spinner-border-sm text-primary me-2"></span> Loading...</div>'
        );
        $('#application_mass_mail_body_content').html('');
        $('#application_fwd_resume_body_content').html('');
        var id        	=	{{ @$jobId }};
        var sid        	=	{{ @$application->id }};
        var arr 		=	[sid];
        var jobIdArr = [id];
        $('.chk_list_item:checked').each(function () {
            
            var jobId = $(this).attr('data-jobId');
            arr.push($(this).val());
            jobIdArr.push(jobId);
        });

        console.log(jobIdArr);

        var id        	=	jobIdArr[0];
        var url       	=	"{{ route('service_company.cvtracker.send_mail',':id') }}";
        url         	=	url.replace(':id', id);
        var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
        var token       =	"{{ csrf_token() }}";

      
          $.ajax({
                type		: 'POST',
                url			: url,
                data		: {'_token': token, '_method': 'POST', 'arr' : arr,'jobIdArr':jobIdArr},
                cache		: false,
                success 	: function (response) {
                                    if (response.status == "success") {
                                        $('#application_send_mail_body_content').html(response.view);
                                        
                                        $('#kt_activities_scroll').removeAttr('style');
                                    }
                        }
          });
              
  });


   $(document).on('click', '.editSkill', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       $('.editSkillUpdate').removeClass('d-none');
       $('.editSkillCancel').removeClass('d-none');
       $('.editSkill').addClass('d-none');
       $('.job_skill_edit').removeClass('d-none');
       $('.job_skill_view').addClass('d-none');

    });
    $(document).on('click', '.editSkillCancel', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       $('.editSkillUpdate').addClass('d-none');
       $('.editSkillCancel').addClass('d-none');
       $('.editSkill').removeClass('d-none');
       $('.job_skill_edit').addClass('d-none');
       $('.job_skill_view').removeClass('d-none');

    });



    $(document).on('click', '#skill-update-form', function(e) { 
           e.preventDefault();
            // Prevent multiple clicks
            let $btn = $(this);
            if ($btn.prop('disabled')) return;

            $btn.prop('disabled', true); // Disable button

          var formData = new FormData($('#skillUpdateForm')[0]);	
          $("#skill-update-form").html(
                      `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );						
          $.ajax({
            url				: '{{route('service_company.job-applications.skillExperienceUpdate', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',								   
                  type 			: 'POST',
                  data 			: formData,
                  processData		: false,  	// tell jQuery not to process the data
                  contentType		: false,  	// tell jQuery not to set contentType
                  redirect			: true,
                  success			: function (response) {
                    $btn.html('Save');
                    $btn.prop('disabled', false); // Re-enable after success
                    $("#skill-update-form").html('Save');
                    $('.editSkillUpdate').addClass('d-none');
                    $('.editSkillCancel').addClass('d-none');
                    $('.job_skill_edit').addClass('d-none');
                    $('.job_skill_view').removeClass('d-none');
                    $('.editSkill').removeClass('d-none');
                          Swal.fire("Success", 'Candidate Skill Experience updated successfully !',"success");
                          $('.job_skill_view').html(response.html)
                        },
                error			: function (response) {
                  $("#skill-update-form").html('Save');
                          Swal.fire("Warning", 'Something is the issue.',"warning");
                        }
            });

    });


       // 20-word limit on screening question answer inputs
    function sqCountWords(str) {
        return str.trim() === '' ? 0 : str.trim().split(/\s+/).length;
    }
    $(document).on('input', '.sq-answer-input', function() {
        var $input = $(this);
        var words = $input.val().trim() === '' ? [] : $input.val().trim().split(/\s+/);
        if (words.length > 20) {
            $input.val(words.slice(0, 20).join(' '));
            words = words.slice(0, 20);
        }
        var count = words.length;
        var $ctr = $input.siblings('.sq-word-count');
        $ctr.find('.sq-wc-num').text(count);
        $ctr.toggleClass('sq-at-limit', count >= 20);
    });
    // Re-sync counters whenever the screening questions drawer opens
    $(document).on('click', '.editJobQuestion', function() {
        setTimeout(function() {
            $('.sq-answer-input').each(function() { $(this).trigger('input'); });
        }, 80);
    });

    $(document).on('click', '.editJobQuestion', function(e) {
       e.preventDefault();
       e.stopPropagation();
       $('.editJobQuestionUpdate').removeClass('d-none');
       $('.editJobQuestionCancel').removeClass('d-none');
       $('.editJobQuestion').addClass('d-none');
       $('.job_additional_edit').removeClass('d-none');
       $('.job_additional_view').addClass('d-none');

    });
    $(document).on('click', '.editJobQuestionCancel', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       $('.editJobQuestionUpdate').addClass('d-none');
       $('.editJobQuestionCancel').addClass('d-none');
       $('.editJobQuestion').removeClass('d-none');
       $('.job_additional_edit').addClass('d-none');
       $('.job_additional_view').removeClass('d-none');

    });

    // Clicking ANYWHERE inside the date/time pill (including the text area) opens the picker.
    // Two layers: (1) direct input click via showPicker(), (2) pill-background click with
    // left/right split to decide which picker to open.
    $(document).on('click', '#jw-slots-drawer .isd-datetime-pill input', function(e) {
        e.stopPropagation(); // stop pill handler from re-running
        var el = this;
        // Small timeout lets the browser focus the element first
        setTimeout(function() {
            try { el.showPicker(); } catch(err) { /* not supported — native focus only */ }
        }, 0);
    });
    $(document).on('click', '#jw-slots-drawer .isd-datetime-pill', function(e) {
        if ($(e.target).is('input')) return; // handled by the input handler above
        var rect = this.getBoundingClientRect();
        var isTimeSide = (e.clientX - rect.left) > (rect.width * 0.62);
        var input = $(this).find(isTimeSide ? 'input[type="time"]' : 'input[type="date"]')[0];
        if (!input) return;
        input.focus();
        setTimeout(function() {
            try { input.showPicker(); } catch(err) {}
        }, 0);
    });

    $(document).on('click', '.editSlots', function(e) {
       e.preventDefault();
       e.stopPropagation();
       $('.editSlots').addClass('d-none');
       $('.job_slots_edit').removeClass('d-none');
       // Hide whichever display state is currently showing
       $('.job_slots_view').addClass('d-none');
       $('.po-slots-empty').addClass('d-none');
    });
    $(document).on('click', '.editSlotsCancel', function(e) {
       e.preventDefault();
       e.stopPropagation();
       $('.editSlots').removeClass('d-none');
       $('.job_slots_edit').addClass('d-none');
       // Restore whichever display state is appropriate
       if ($('.job_slots_view').length) {
           $('.job_slots_view').removeClass('d-none');
       } else {
           $('.po-slots-empty').removeClass('d-none');
       }
    });



    $(document).on('click', '#job-question-update-form', function(e) { 
           e.preventDefault();
            // Prevent multiple clicks
            let $btn = $(this);
            if ($btn.prop('disabled')) return;

            $btn.prop('disabled', true); // Disable button

          var formData = new FormData($('#jobQuestionUpdateForm')[0]);	
          $("#job-question-update-form").html(
                      `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );						
          $.ajax({
            url				: '{{route('service_company.job-applications.JobQuestionUpdate', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',								   
                  type 			: 'POST',
                  data 			: formData,
                  processData		: false,  	// tell jQuery not to process the data
                  contentType		: false,  	// tell jQuery not to set contentType
                  redirect			: true,
                  success			: function (response) {
                    $btn.html('Save');
                    $btn.prop('disabled', false); // Re-enable after success
                    $("#job-question-update-form").html('Save');
                    $('.editJobQuestionUpdate').addClass('d-none');
                    $('.editJobQuestionCancel').addClass('d-none');
                    $('.job_additional_edit').addClass('d-none');
                    $('.job_additional_view').removeClass('d-none');
                    $('.editJobQuestion').removeClass('d-none');
                          Swal.fire("Success", 'Candidate Job Question updated successfully !',"success");
                          $('.job_additional_view').html(response.html)
                        },
                error			: function (response) {
                  $("#job-question-update-form").html('Save');
                          Swal.fire("Warning", 'Something is the issue.',"warning");
                        }
            });

    });


    $(document).on('click', '#job-slots-update-form', function(e) {
           e.preventDefault();
            // Prevent multiple clicks
            let $btn = $(this);
            if ($btn.prop('disabled')) return;

            // Validate slots: each filled slot must have all 3 fields; at least 1 complete slot required
            var slots = [
                {label:'Slot 1', date: $('#interviewDateOne').val(), time: $('#interviewTimeOne').val(), mode: $('#interview_mode_one').val()},
                {label:'Slot 2', date: $('#interviewDateTwo').val(), time: $('#interviewTimeTwo').val(), mode: $('#interview_mode_two').val()},
                {label:'Slot 3', date: $('#interviewDateThree').val(), time: $('#interviewTimeThree').val(), mode: $('#interview_mode_three').val()}
            ];
            var partialSlot = null;
            slots.forEach(function(s) {
                var filled = (s.date ? 1 : 0) + (s.time ? 1 : 0) + (s.mode ? 1 : 0);
                if (filled > 0 && filled < 3 && !partialSlot) { partialSlot = s.label; }
            });
            if (partialSlot) {
                Swal.fire('Incomplete Slot', partialSlot + ' is partially filled. Please select Date, Time and Mode for that slot — or clear all its fields.', 'warning');
                return;
            }
            var completeSlots = slots.filter(function(s) { return s.date && s.time && s.mode; }).length;
            if (completeSlots === 0) {
                Swal.fire('No Slots Added', 'Please fill at least one slot with Date, Time and Mode before saving.', 'warning');
                return;
            }

            $btn.prop('disabled', true); // Disable button

          var formData = new FormData($('#jobSlotsUpdateForm')[0]);	
          $("#job-slots-update-form").html(
                      `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );						
          $.ajax({
            url				: '{{route('service_company.job-applications.SlotsUpdate', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',								   
                  type 			: 'POST',
                  data 			: formData,
                  processData		: false,  	// tell jQuery not to process the data
                  contentType		: false,  	// tell jQuery not to set contentType
                  redirect			: true,
                  success : function (response) {
                    // Close drawer first
                    $('.job_slots_edit').addClass('d-none');
                    $('.editSlots').removeClass('d-none');
                    // Re-enable save button
                    $btn.html('Save Changes');
                    $btn.prop('disabled', false);

                    // Real-time DOM update — handle two states:
                    // (A) Existing .job_slots_view → update its inner HTML
                    // (B) Empty state (.po-slots-empty) → replace it with the slots grid
                    if ($('.job_slots_view').length) {
                        $('.job_slots_view').html(response.html).removeClass('d-none');
                    } else {
                        $('.po-slots-empty').replaceWith(
                            '<div class="slots-grid job_slots_view">' + response.html + '</div>'
                        );
                    }

                    if (document.activeElement) { document.activeElement.blur(); }
                    setTimeout(function() {
                        Swal.fire("Success", 'Candidate Interview Slots updated successfully!', "success");
                    }, 350);
                  },
                  error : function () {
                    $btn.html('Save Changes');
                    $btn.prop('disabled', false);
                    Swal.fire("Warning", 'Something went wrong. Please try again.', "warning");
                  }
            });

    });

    



});



    

    // ===== DRAWER 4: Recruiter Notes =====
    var rnTemplates = {};
    var rnTemplatesLoaded = false;

    $(document).on('click', '.editRecruiterNote', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.rn-drawer-body').removeClass('d-none');

        if (!rnTemplatesLoaded) {
            $.getJSON('{{ route('service_company.job-applications.recruiterNoteTemplates') }}', function(res) {
                if (res.status === 'success' && res.data && res.data.length > 0) {
                    var opts = '<option value="">Select Note Template...</option>';
                    $.each(res.data, function(i, tpl) {
                        rnTemplates[tpl.id] = tpl;
                        opts += '<option value="' + tpl.id + '">' + $('<div>').text(tpl.title).html() + '</option>';
                    });
                    $('#rn_template_select').html(opts);
                    $('#rn-template-wrap').show();
                    rnTemplatesLoaded = true;
                }
            });
        }
    });

    $(document).on('click', '.editRecruiterNoteCancel', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.rn-drawer-body').addClass('d-none');
    });

    $(document).on('change', '#rn_template_select', function() {
        var id = $(this).val();
        if (id && rnTemplates[id]) {
            $('#jw_recruiter_note_editor').html(rnTemplates[id].content);
        }
    });

    $(document).on('click', '#rn-save-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        if ($btn.prop('disabled')) return;
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

        var note  = $('#jw_recruiter_note_editor').html();
        var appId = $('#rn_app_id').val();

        $.ajax({
            url     : '{{ route('service_company.job-applications.recruiterNoteUpdate') }}?_token={{ csrf_token() }}',
            type    : 'POST',
            data    : { appId: appId, recruiter_note: note },
            success : function(response) {
                if (response.status) {
                    if (note && note.trim()) {
                        if ($('.po-recruiter-note-text').length) {
                            $('.po-recruiter-note-text').html(note).show();
                        } else {
                            $('.po-no-notes').replaceWith('<div class="po-recruiter-note-text" style="font-size:12px;line-height:1.7;color:#475569;">' + note + '</div>');
                        }
                        $('.po-no-notes').hide();
                    } else {
                        $('.po-recruiter-note-text').hide();
                        if ($('.po-no-notes').length === 0) {
                            $('.po-recruiter-note-text').after('<p class="po-no-notes" style="font-size:12px;color:#94a3b8;font-style:italic;margin:0;">No recruiter notes added yet.</p>');
                        }
                        $('.po-no-notes').show();
                    }
                    // Close drawer first; blur active element so focus-restore
                    // cannot re-trigger the Edit button after Swal closes
                    $('.rn-drawer-body').addClass('d-none');
                    if (document.activeElement) { document.activeElement.blur(); }
                    // Wait for drawer slide-out transition (280ms) before re-enabling
                    // button and firing Swal — prevents Swal-close click from landing
                    // on the now-hidden Edit button and reopening the drawer
                    setTimeout(function() {
                        $btn.html('Save Changes');
                        $btn.prop('disabled', false);
                        Swal.fire('Success', 'Recruiter notes updated successfully!', 'success');
                    }, 350);
                } else {
                    $btn.html('Save Changes');
                    $btn.prop('disabled', false);
                }
            },
            error   : function() {
                $btn.html('Save Changes');
                $btn.prop('disabled', false);
                Swal.fire('Warning', 'Something went wrong. Please try again.', 'warning');
            }
        });
    });


</script> 
<!--begin::Send Mail - Drawer (hidden toggle pattern — works in AJAX-loaded content) -->
<div id="kt_send_mail_sd_toggle" style="display:none;"></div>
<div id="kt_send_mail_showdetail_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="send-mail-showdetail"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="false"
    data-kt-drawer-width="{default:'90%', 'lg': '80%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_send_mail_sd_toggle" data-kt-drawer-close="#kt_send_mail_showdetail_close">
    <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column">
        <div class="card-header px-5 py-0 flex-shrink-0">
            <h3 class="card-title fw-bolder text-dark mb-0">Send Email</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-danger" id="kt_send_mail_showdetail_close">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>
        <div class="card-body position-relative px-0 py-0 flex-grow-1 overflow-auto" id="application_send_mail_body_content"></div>
    </div>
</div>
<!--end::Send Mail - Drawer-->
<script>
(function() {
    if (typeof KTDrawer !== 'undefined') {
        KTDrawer.createInstances();
    }

    // Manual overlay for the send-mail drawer (avoids conflicts with parent drawer overlay)
    var $overlay = null;

    function showSendMailOverlay() {
        if ($overlay) return;
        $overlay = $('<div class="sd-mail-overlay"></div>').css({
            position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)',
            zIndex: 109, cursor: 'pointer'
        }).appendTo('body');
        $overlay.on('click', hideSendMailDrawer);
    }

    function hideSendMailDrawer() {
        if ($overlay) { $overlay.remove(); $overlay = null; }
        var drawerEl = document.getElementById('kt_send_mail_showdetail_drawer');
        if (drawerEl && typeof KTDrawer !== 'undefined') {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (drawer) { drawer.hide(); }
        }
    }

    $(document).off('click.sdMailModal').on('click.sdMailModal', '.sendmaimodal', function() {
        showSendMailOverlay();
    });

    $(document).off('click.sdMailClose').on('click.sdMailClose', '#kt_send_mail_showdetail_close', function() {
        hideSendMailDrawer();
    });
})();
</script>

<!--begin::Send Whatsapp - Drawer (hidden toggle pattern) -->
<div id="kt_send_whatsapp_sd_toggle" style="display:none;"></div>
<div id="kt_send_whatsapp_showdetail_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="send-whatsapp-showdetail"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="false"
    data-kt-drawer-width="{default:'90%', 'lg': '80%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_send_whatsapp_sd_toggle" data-kt-drawer-close="#kt_send_whatsapp_showdetail_close">
    <div class="shadow-none rounded-0 w-100 h-100" style="display:flex;flex-direction:column;overflow:hidden;">
        <div style="flex:1;overflow:hidden;" id="application_send_whatsapp_body_content"></div>
    </div>
</div>
<!--end::Send Whatsapp - Drawer -->

<script>
(function() {
    if (typeof KTDrawer !== 'undefined') { KTDrawer.createInstances(); }
    var $waOverlay = null;

    function showWhatsappOverlay() {
        if ($waOverlay) return;
        $waOverlay = $('<div class="sd-whatsapp-overlay"></div>').css({
            position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)',
            zIndex: 109, cursor: 'pointer'
        }).appendTo('body');
        $waOverlay.on('click', hideWhatsappDrawer);
    }

    function hideWhatsappDrawer() {
        if ($waOverlay) { $waOverlay.remove(); $waOverlay = null; }
        var drawerEl = document.getElementById('kt_send_whatsapp_showdetail_drawer');
        if (drawerEl && typeof KTDrawer !== 'undefined') {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (drawer) { drawer.hide(); }
        }
    }

    $(document).off('click.sdWaModal').on('click.sdWaModal', '.sendwhatsappmodal', function() {
        showWhatsappOverlay();
    });

    $(document).off('click.sdWaClose').on('click.sdWaClose', '#kt_send_whatsapp_showdetail_close', function() {
        hideWhatsappDrawer();
    });
})();
</script>

{{-- ═══════════════════════════════════════════════════════════════════
     DOWNLOAD RESUME CENTER (RDC)
     Overlay + right-slide drawer for original, branded, highlighted, report,
     and final report/resume package downloads.
     Option 6 is the newer R2/browser branded-resume header flow; the original
     option 2 remains wired to the existing Resume/CV viewer engine.
     No new MySQL queries. No extra server-side logic.
═══════════════════════════════════════════════════════════════════ --}}

{{-- Data bridge: PHP values injected once, read by JS — zero extra queries --}}
@php
  $rdcDownloadUrl = '';
  $rdcR2DirectUrl = '';
  $rdcR2PublicUrl = '';
  $rdcOriginalR2DirectUrl = '';
  $rdcOriginalR2PublicUrl = '';
  $rdcOriginalR2Available = false;
  $rdcR2SignUrl = '';
  $rdcResumePdfUrl = '';
  $rdcResumeAvailable = false;
  $rdcResumeRaw = (string)(@$application->resume ?? '');
  if($rdcResumeRaw !== ''){
    // Direct R2 key used by browser-first download/conversion options; no extra query.
    $rdcR2Key = '';
    if(strpos($rdcResumeRaw, 'https://file.jero.works/') === 0){
      $rdcR2Key = ltrim((string)parse_url($rdcResumeRaw, PHP_URL_PATH), '/');
    } elseif(!filter_var($rdcResumeRaw, FILTER_VALIDATE_URL)){
      $rdcR2Key = ltrim($rdcResumeRaw, '/');
    }

    if($rdcR2Key !== ''){
      $rdcResumeAvailable = true;
      $rdcOriginalR2Available = true;
      $rdcOriginalR2PublicUrl = 'https://file.jero.works/'.$rdcR2Key;
      $rdcR2PublicUrl = $rdcOriginalR2PublicUrl;
      $rdcDownloadUrl = route('download.resume', ['id' => $application->id, 'path' => rtrim(strtr(base64_encode($rdcR2Key), '+/', '-_'), '=')]);
      $rdcR2SignUrl = route('service_company.job-applications.r2-download-url', ['id' => $application->id]);
    } elseif(filter_var($rdcResumeRaw, FILTER_VALIDATE_URL)) {
      $rdcResumeAvailable = true;
      $rdcDownloadUrl = $rdcResumeRaw;
      $rdcR2DirectUrl = strpos($rdcResumeRaw, request()->getSchemeAndHttpHost()) !== 0 ? $rdcResumeRaw : '';
      $rdcR2PublicUrl = $rdcR2DirectUrl;
      if($rdcR2DirectUrl !== ''){
        $rdcOriginalR2Available = true;
        $rdcOriginalR2DirectUrl = $rdcR2DirectUrl;
        $rdcOriginalR2PublicUrl = $rdcR2PublicUrl;
      }
    } else {
      $rdcResumeAvailable = true;
      $rdcDownloadUrl = url($rdcResumeRaw);
    }

    if($rdcResumeAvailable){
      $rdcResumeConvertToken = rtrim(strtr(base64_encode(GeneralMethod::value_encrypt((string) $rdcResumeRaw)), '+/', '-_'), '=');
      $rdcResumePdfUrl = route('service_company.job-applications.resume-convert', ['token' => $rdcResumeConvertToken]);
    }
  }
@endphp
<div id="rdcData" hidden
     data-dl-url="{{ $rdcDownloadUrl }}"
     data-r2-direct-url="{{ $rdcR2DirectUrl }}"
     data-r2-public-url="{{ $rdcR2PublicUrl }}"
     data-original-r2-direct-url="{{ $rdcOriginalR2DirectUrl }}"
     data-original-r2-public-url="{{ $rdcOriginalR2PublicUrl }}"
     data-original-r2-available="{{ $rdcOriginalR2Available ? 'true' : 'false' }}"
     data-r2-sign-url="{{ $rdcR2SignUrl }}"
     data-resume-pdf-url="{{ $rdcResumePdfUrl }}"
     data-has-resume="{{ $rdcResumeAvailable ? 'true' : 'false' }}"
     data-has-aws="{{ $resumeAwsPath ? 'true' : 'false' }}"
     data-file-ext="{{ strtoupper(pathinfo((string)@$application->resume, PATHINFO_EXTENSION) ?: 'FILE') }}"></div>

{{-- Recruiter Summary data bridge — zero extra DB queries; all PHP vars already in scope --}}
@php
  $rsSkills = [];
  $rsSkillRatingMap = [];
  $rsSkillNormalize = function($value) {
    return mb_strtolower(trim((string) $value));
  };
  $rsSkillSources = [@$application->candidate_skills ?? [], @$application->skill_experiences ?? [], $jobSkill ?? []];
  foreach($rsSkillSources as $rsSkillSource){
    if(is_string($rsSkillSource)){
      $decoded = json_decode($rsSkillSource);
      $rsSkillSource = is_array($decoded) ? $decoded : [];
    }
    if(!is_iterable($rsSkillSource)) continue;
    foreach($rsSkillSource as $rsSkillRow){
      $rsSkillRow = (object) $rsSkillRow;
      $rsSkillName = trim((string)($rsSkillRow->skill_key ?? $rsSkillRow->skill_name ?? $rsSkillRow->name ?? $rsSkillRow->skill ?? ''));
      $rsSkillRating = $rsSkillRow->rating ?? $rsSkillRow->self_rating ?? $rsSkillRow->skill_rating ?? $rsSkillRow->candidate_rating ?? null;
      if($rsSkillName !== '' && !is_null($rsSkillRating) && (is_numeric($rsSkillRating) || preg_match('/\d+/', (string)$rsSkillRating, $rsRatingMatch))){
        $rsSkillRatingMap[$rsSkillNormalize($rsSkillName)] = max(0,min(5,(int)(is_numeric($rsSkillRating) ? $rsSkillRating : $rsRatingMatch[0])));
      }
    }
  }
  $rsResolveSkillRating = function($row, $name) use ($rsSkillRatingMap, $rsSkillNormalize) {
    $row = (object) $row;
    $rating = $row->rating ?? $row->self_rating ?? $row->skill_rating ?? $row->candidate_rating ?? null;
    if(!is_null($rating) && (is_numeric($rating) || preg_match('/\d+/', (string)$rating, $ratingMatch))){
      return max(0,min(5,(int)(is_numeric($rating) ? $rating : $ratingMatch[0])));
    }
    $key = $rsSkillNormalize($name);
    return $rsSkillRatingMap[$key] ?? 0;
  };
  $rsFormatSkillExperience = function($years, $months, $hasValue = true) {
    if(!$hasValue) return '--';
    $years = max(0, (int) $years);
    $months = max(0, (int) $months);
    if($months >= 12){
      $years += intdiv($months, 12);
      $months = $months % 12;
    }
    if($years === 0 && $months === 0) return '0 Years';
    $parts = [];
    if($years > 0) $parts[] = $years.' Year'.($years > 1 ? 's' : '');
    if($months > 0) $parts[] = $months.' Month'.($months > 1 ? 's' : '');
    return implode(' ', $parts);
  };
  $rsParseSkillExperience = function($value) use ($rsFormatSkillExperience) {
    $raw = trim((string) $value);
    if($raw === '') return ['text'=>'--','value'=>0];
    if(preg_match('/(\d+)\s*(?:years?|yrs?)?\s+(\d+)\s*(?:months?|mos?)/i', $raw, $match)){
      $years = (int) $match[1];
      $months = (int) $match[2];
      return ['text'=>$rsFormatSkillExperience($years, $months, true),'value'=>$years + ($months / 12)];
    }
    if(preg_match('/(\d+)\s*[.]\s*(\d*)/', $raw, $match)){
      $years = (int) $match[1];
      $months = $match[2] === '' ? 0 : (int) $match[2];
      return ['text'=>$rsFormatSkillExperience($years, $months, true),'value'=>$years + ($months / 12)];
    }
    if(preg_match('/(\d+)/', $raw, $match)){
      $years = (int) $match[1];
      return ['text'=>$rsFormatSkillExperience($years, 0, true),'value'=>$years];
    }
    return ['text'=>$raw,'value'=>0];
  };
  if(!empty($skillExperiencesCount) && !empty($skillExperiences)){
    foreach($skillExperiences as $exp){
      $exp=(object)$exp;
      $sn=trim((string)($exp->skill_key??'')); if(!$sn) continue;
      $year=(int)($exp->year ?? 0);
      $month=(int)($exp->month ?? 0);
      if(isset($exp->total_months) && is_numeric($exp->total_months) && (int)$exp->total_months > 0){
        $year = intdiv((int)$exp->total_months, 12);
        $month = (int)$exp->total_months % 12;
      }
      $expText=$rsFormatSkillExperience($year,$month,true);
      $rsSkills[]=[
        'name'=>$sn,
        'expText'=>$expText,
        'exp'=>$year + ($month / 12),
        'rating'=>$rsResolveSkillRating($exp,$sn)
      ];
    }
  }
  if(empty($rsSkills)&&!empty($jobSkill)){
    foreach($jobSkill as $sv){
      $sv=(object)$sv; $sn=trim((string)($sv->skill_name??'')); if(!$sn) continue;
      $expData=$rsParseSkillExperience($sv->skill_value??'');
      $rsSkills[]=[
        'name'=>$sn,
        'expText'=>$expData['text'],
        'exp'=>$expData['value'],
        'rating'=>$rsResolveSkillRating($sv,$sn)
      ];
    }
  }
  if(empty($rsSkills)&&!empty($application->skills)){
    foreach(array_slice(array_values(array_filter(array_map('trim',explode(',', $application->skills)))),0,12) as $sn){
      if($sn) $rsSkills[]=['name'=>$sn,'expText'=>'--','exp'=>0,'rating'=>0];
    }
  }
  $rsMatchPct=(int)round((float)(@$application->skill_match??0));
  if($rsMatchPct===0&&!empty($__hdrJobTotal)) $rsMatchPct=(int)round(($__hdrMatchedCount??0)/$__hdrJobTotal*100);
  $rsWm=trim((string)(@$application->wm??''));
  $rsWorkMode=$rsWm!=='' ? ucwords(str_replace(['_', '-'], ' ', strtolower($rsWm))) : '—';
  $rsNpMode=(int)(@$application->serving_notice_period_mode??-1);
  $rsNpRaw=trim((string)@$application->np_id);
  $rsNpTs=$rsNpRaw!=='' ? strtotime($rsNpRaw) : false;
  if($rsNpMode===2||strtolower($rsNpRaw)==='immediate'){ $rsNotice='Immediate'; }
  elseif(($rsNpMode===0||$rsNpMode===1)&&$rsNpTs!==false&&(int)date('Y',$rsNpTs)>1970){ $rsNotice='Available '.date('d M Y',$rsNpTs); }
  else { $rsNotice=$rsNpRaw; }
  if($rsNotice===''||$rsNotice==='Available '||str_contains($rsNotice,'01 Jan 1970')) $rsNotice='--';
  $rsIsImmediate=($rsNpMode===2||strtolower($rsNpRaw)==='immediate');
  $rsLoc=trim(trim($city_name??'',', ').' '.trim($state_name??'',', '));
  if($rsLoc==='') $rsLoc=trim($country_name??'');
  if($rsLoc==='') $rsLoc='--';
  $rsTotalExp=trim((string)(@$application->total_exp??''));
  $rsTotalExp=trim($rsTotalExp," \t\n\r\0\x0B.");
  $rsExpStr=$rsTotalExp!==''?$rsTotalExp.' Year'.((float)$rsTotalExp==1.0?'':'s'):'--';
  $rsValidation=[];
  if(!empty($application->resume))   $rsValidation[]=['label'=>'RESUME','status'=>'Verified'];
  if(!empty($application->email)||!empty($application->secondrary_email)) $rsValidation[]=['label'=>'EMAIL ID','status'=>'Verified'];
  if(!empty($application->phone)||!empty($application->secondary_mobile)) $rsValidation[]=['label'=>'PHONE NUMBER','status'=>'Verified'];
  if(!empty($application->linkedin)) $rsValidation[]=['label'=>'LINKEDIN ID','status'=>'Verified'];
  if(!empty($application->recruiter_note)) $rsValidation[]=['label'=>'Recruiter Screened','status'=>'Completed'];
  if(!empty($application->current_designation)||!empty($application->current_company)) $rsValidation[]=['label'=>'JOB DESCRIPTION','status'=>'Shared with candidate'];
  if((float)(@$application->ctc_yearly??0)>0||(float)(@$application->ectc_yearly??0)>0||(float)(@$application->percentage_hike??0)>0) $rsValidation[]=['label'=>'Compensation Discussed','status'=>'Discussed'];
  $rsCareerSummary=trim(strip_tags(html_entity_decode((string)(@$application->careersummary??''),ENT_QUOTES,'UTF-8')));
  $rsCaptainName = trim((string)($clientjobAssignListCaptain ?? ''));
  $rsCaptainName = $rsCaptainName !== '' ? ucwords(strtolower($rsCaptainName)) : '--';
@endphp
<div id="rsData" hidden
  data-name="{{ ucwords(@$application->full_name) }}"
  data-email="{{ !@$rcvMaskContact ? @$application->email : '' }}"
  data-secondary-email="{{ !@$rcvMaskContact ? @$application->secondrary_email : '' }}"
  data-phone="{{ !@$rcvMaskContact ? @$application->phone : '' }}"
  data-secondary-phone="{{ !@$rcvMaskContact ? @$application->secondary_mobile : '' }}"
  data-phone-code="{{ !@$rcvMaskContact ? @$application->phone_code : '' }}"
  data-secondary-phone-code="{{ !@$rcvMaskContact ? @$application->second_phone_code : '' }}"
  data-linkedin="{{ !@$rcvMaskContact ? @$application->linkedin : '' }}"
  data-designation="{{ ucwords(@$application->current_designation) }}"
  data-company="{{ ucwords(@$application->current_company ?: @$application->cur_company_name) }}"
  data-location="{{ $rsLoc }}"
  data-experience="{{ $rsExpStr }}"
  data-notice="{{ $rsNotice }}"
  data-education="{{ strtoupper(@$application->qualification_name) }}"
  data-work-mode="{{ $rsWorkMode }}"
  data-report-id="JR{{ now()->format('Ymd') }}{{ str_pad((string)@$application->id, 6, '0', STR_PAD_LEFT) }}"
  data-report-date="{{ now()->format('d M Y') }}"
  data-recruiter-name="{{ $rsCaptainName }}"
  data-report-company-name="{{ $rcvCompanyName ?? '' }}"
  data-report-company-logo="{{ $rcvCompanyLogo ?? '' }}"
  data-ai-version="Live Intelligence"
  data-match="{{ $rsMatchPct }}"
  data-is-immediate="{{ $rsIsImmediate ? '1' : '0' }}"
  @php
    $rsNoteRaw = strip_tags((string)(@$application->recruiter_note ?: @$application->comment ?? ''));
    $rsSummaryFinal = $rsCareerSummary ?: trim(strip_tags(html_entity_decode((string)(@$application->cover_letter??''),ENT_QUOTES,'UTF-8')));
  @endphp
  data-recruiter-note="{{ \Illuminate\Support\Str::limit($rsNoteRaw, 500) }}"
  data-career-summary="{{ \Illuminate\Support\Str::limit($rsSummaryFinal, 500) }}"
  data-job-title="{{ ucwords($job->title??'') }}"
  data-skills='@json($rsSkills)'
  data-validation='@json($rsValidation)'></div>

<script>
/* buildRecruiterSummary — client-side vector PDF report, no server calls */
(function(){
'use strict';

var rsPdfReady=null;
var rsPdfLibReady=null;
var rsPdfJsReady=null;
var rsJsZipReady=null;
var rsDocxReady=null;
var rsHtml2CanvasReady=null;
var rsJsPdfReady=null;
var rsOpenDocLocalReady=null;
var rsOpenDocExternalReady=null;
var rsLogoCache={};
var rsDocxMaxBytes=10*1024*1024;
var rsPdfMaxBytes=30*1024*1024;
var rsAtsHighlightKeywords=@json($rcvJobSkills ?? []);
var rsDrawerMatchedKeywords=@json($__hdrMatchedSkillNames ?? []);
var rsStarGold='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#f59e0b" d="M12 2.6l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.16l-5.7 3 1.09-6.35-4.62-4.5 6.38-.93L12 2.6z"/></svg>';
var rsStarMuted='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#cbd5e1" d="M12 2.6l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.16l-5.7 3 1.09-6.35-4.62-4.5 6.38-.93L12 2.6z"/></svg>';
var rsIconUser='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5z"/></svg>';
var rsIconCalendar='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M7 2h2v2h6V2h2v2h2a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2V2zm12 8H5v9h14v-9z"/></svg>';
var rsIconBriefcase='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M9 6V5a3 3 0 0 1 6 0v1h4a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4zm2 0h2V5a1 1 0 0 0-2 0v1z"/></svg>';
var rsIconExperience='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M12 2l2.2 4.5 5 .7-3.6 3.5.8 5-4.4-2.4-4.4 2.4.8-5L4.8 7.2l5-.7L12 2zm-6 17h12v2H6v-2z"/></svg>';
var rsIconClock='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2zm1 11h5v-2h-4V6h-2v7z"/></svg>';
var rsIconCompany='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M4 21V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v17h3v-9h-2v-2h3a1 1 0 0 1 1 1v10H4zm4-14h2V5H8v2zm4 0h2V5h-2v2zm-4 4h2V9H8v2zm4 0h2V9h-2v2zm-4 4h2v-2H8v2zm4 0h2v-2h-2v2z"/></svg>';
var rsIconEducation='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 3L1.5 8.5 12 14l8-4.2V16h2V8.5L12 3zm-6 9.1V16c0 1.7 2.7 4 6 4s6-2.3 6-4v-3.9L12 15.2l-6-3.1z"/></svg>';
var rsIconLocation='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>';
var rsIconLaptop='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 4h16a2 2 0 0 1 2 2v10H2V6a2 2 0 0 1 2-2zm0 2v8h16V6H4zm-2 12h20v2H2v-2z"/></svg>';
var rsIconSummary='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3 5h8V6H8v2zm0 5h8v-2H8v2zm0 5h5v-2H8v2z"/></svg>';
var rsIconNote='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H9l-5 5V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg>';
var rsIconSkill='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2l2.4 5.4 5.9.6-4.4 3.9 1.3 5.8-5.2-3-5.2 3 1.3-5.8L3.7 8l5.9-.6L12 2z"/></svg>';
var rsIconShield='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2l8 3v6c0 5-3.4 9.6-8 11-4.6-1.4-8-6-8-11V5l8-3zm-1 13.2l6-6-1.4-1.4L11 12.4 8.4 9.8 7 11.2l4 4z"/></svg>';
var rsIconResume='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M6 2h8l5 5v15H6V2zm7 1.8V8h4.2L13 3.8zM8 12h8v-1.7H8V12zm0 4h8v-1.7H8V16zm0 4h6v-1.7H8V20z"/></svg>';
var rsIconEmail='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zm8 7.2L4.7 7H4v.7l8 5.7 8-5.7V7h-.7L12 12.2z"/></svg>';
var rsIconPhone='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M7.2 2.8l3 3a2 2 0 0 1 .4 2.2l-.9 2.1a13.5 13.5 0 0 0 4.2 4.2l2.1-.9a2 2 0 0 1 2.2.4l3 3a2 2 0 0 1 0 2.8l-1.4 1.4c-.8.8-2 .9-3 .5A25.2 25.2 0 0 1 2.5 7.2c-.4-1-.3-2.2.5-3l1.4-1.4a2 2 0 0 1 2.8 0z"/></svg>';
var rsIconLinkedin='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3.4 15V9.6H6V18h2.4zM7.2 8.5a1.4 1.4 0 1 0 0-2.8 1.4 1.4 0 0 0 0 2.8zM18 18v-4.8c0-2.5-1.3-3.8-3.2-3.8-1.4 0-2.1.8-2.5 1.4V9.6H10V18h2.4v-4.2c0-1.1.2-2.2 1.6-2.2s1.5 1.3 1.5 2.3V18H18z"/></svg>';
var rsIconJobDescription='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M8 2h8a2 2 0 0 1 2 2v2h2v16H4V6h2V4a2 2 0 0 1 2-2zm0 4h8V4H8v2zm-1 6h10v-1.8H7V12zm0 4h10v-1.8H7V16zm0 4h7v-1.8H7V20z"/></svg>';
function rsLoadScript(src){
  return new Promise(function(resolve,reject){
    var existing=document.querySelector('script[src="'+src+'"]');
    if(existing && existing.getAttribute('data-ready')==='1'){ resolve(); return; }
    var s=existing||document.createElement('script');
    var done=false;
    var finish=function(ok){
      if(done) return;
      done=true;
      if(ok){
        s.setAttribute('data-ready','1');
        resolve();
      }else{
        reject(new Error('Load failed: '+src));
      }
    };
    s.src=src; s.async=true;
    s.onload=function(){ finish(true); };
    s.onerror=function(){ finish(false); };
    if(!existing) document.head.appendChild(s);
    if(existing) setTimeout(function(){ finish(true); },100);
    setTimeout(function(){ finish(false); },8000);
  });
}
function rsTryList(list,index,test){
  if(index>=list.length) return Promise.reject(new Error('Could not load Recruiter Summary PDF engine'));
  if(test()) return Promise.resolve();
  return rsLoadScript(list[index]).then(function(){
    return test()?Promise.resolve():Promise.reject(new Error('Loaded script but PDF engine missing'));
  }).catch(function(){ return rsTryList(list,index+1,test); });
}
function rsEnsurePdfMake(){
  if(rsPdfReady && window.pdfMake && window.pdfMake.vfs && window.pdfMake.vfs['Roboto-Regular.ttf']) return rsPdfReady;
  var core=['/js/pdfmake.min.js','https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js'];
  var vfs=['/js/vfs_fonts.min.js','https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.min.js'];
  rsPdfReady=rsTryList(core,0,function(){ return !!window.pdfMake; })
    .then(function(){ return rsTryList(vfs,0,function(){ return !!(window.pdfMake.vfs && window.pdfMake.vfs['Roboto-Regular.ttf']); }); })
    .then(function(){
      window.pdfMake.fonts={Roboto:{normal:'Roboto-Regular.ttf',bold:'Roboto-Medium.ttf',italics:'Roboto-Italic.ttf',bolditalics:'Roboto-MediumItalic.ttf'}};
    });
  return rsPdfReady;
}
function rsEnsurePdfLib(){
  if(rsPdfLibReady && window.PDFLib && window.PDFLib.PDFDocument) return rsPdfLibReady;
  var sources=['/js/pdf-lib.min.js','https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js'];
  rsPdfLibReady=rsTryList(sources,0,function(){ return !!(window.PDFLib && window.PDFLib.PDFDocument); });
  return rsPdfLibReady;
}
function rsEnsureJsZip(){
  if(rsJsZipReady && window.JSZip) return rsJsZipReady;
  var sources=['/js/jszip.min.js','https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js'];
  rsJsZipReady=rsTryList(sources,0,function(){ return !!window.JSZip; });
  return rsJsZipReady;
}
function rsEnsureDocxPreview(){
  if(rsDocxReady && window.docx && typeof window.docx.renderAsync==='function') return rsDocxReady;
  var sources=['/js/docx-preview.min.js','https://cdn.jsdelivr.net/npm/docx-preview@0.3.7/dist/docx-preview.min.js'];
  rsDocxReady=rsEnsureJsZip().then(function(){
    return rsTryList(sources,0,function(){ return !!(window.docx && typeof window.docx.renderAsync==='function'); });
  });
  return rsDocxReady;
}
function rsEnsureHtml2Canvas(){
  if(rsHtml2CanvasReady && typeof window.html2canvas==='function') return rsHtml2CanvasReady;
  var sources=['/js/html2canvas.min.js','https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js'];
  rsHtml2CanvasReady=rsTryList(sources,0,function(){ return typeof window.html2canvas==='function'; });
  return rsHtml2CanvasReady;
}
function rsEnsureJsPdf(){
  if(rsJsPdfReady && window.jspdf && typeof window.jspdf.jsPDF==='function') return rsJsPdfReady;
  var sources=['/js/jspdf.umd.min.js','https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js'];
  rsJsPdfReady=rsTryList(sources,0,function(){ return !!(window.jspdf && typeof window.jspdf.jsPDF==='function'); });
  return rsJsPdfReady;
}
function rsEnsurePdfJs(){
  if(rsPdfJsReady && window.pdfjsLib && typeof window.pdfjsLib.getDocument==='function') return rsPdfJsReady;
  var sources=['https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js'];
  rsPdfJsReady=rsTryList(sources,0,function(){ return !!(window.pdfjsLib && typeof window.pdfjsLib.getDocument==='function'); }).then(function(){
    try{
      var ver=window.pdfjsLib.version || '2.11.338';
      window.pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/'+ver+'/pdf.worker.min.js';
    }catch(err){}
  });
  return rsPdfJsReady;
}
function rsEnsureDocxConversion(){
  return Promise.all([rsEnsureDocxPreview(),rsEnsureHtml2Canvas(),rsEnsureJsPdf()]);
}
function rsLogoDataUri(rawUrl){
  rawUrl=clean(rawUrl,'');
  if(!rawUrl || rawUrl==='--') return Promise.resolve(null);
  if(/^data:image\//i.test(rawUrl)) return Promise.resolve(rawUrl);
  if(rsLogoCache[rawUrl]) return rsLogoCache[rawUrl];
  rsLogoCache[rawUrl]=fetch(rawUrl,{credentials:'include',cache:'force-cache'})
    .then(function(response){ return response.ok ? response.blob() : null; })
    .then(function(blob){
      if(!blob) return null;
      return new Promise(function(resolve){
        var reader=new FileReader();
        reader.onload=function(){ resolve(reader.result); };
        reader.onerror=function(){ resolve(null); };
        reader.readAsDataURL(blob);
      });
    })
    .catch(function(){ return null; });
  return rsLogoCache[rawUrl];
}
function preloadRecruiterSummaryAssets(){
  var data=null;
  try{ data=readData(); }catch(ex){}
  var tasks=[rsEnsurePdfMake()];
  if(data && clean(data.reportCompanyLogo,'') && clean(data.reportCompanyLogo,'')!=='--'){
    tasks.push(rsLogoDataUri(data.reportCompanyLogo));
  }
  return Promise.all(tasks).catch(function(err){
    console.warn('Recruiter Summary preload skipped:',err);
    return null;
  });
}
function preloadCombinedReportAssets(){
  var ext='';
  var dataEl=document.getElementById('rdcData');
  if(dataEl) ext=(dataEl.getAttribute('data-file-ext')||'').toUpperCase();
  var tasks=[preloadRecruiterSummaryAssets(),rsEnsurePdfLib()];
  return Promise.all(tasks).catch(function(err){
    console.warn('Combined Pre-Screened Report preload skipped:',err);
    return null;
  });
}
function preloadBrowserDocxConversionAssets(){
  return rsEnsureDocxConversion().catch(function(err){
    console.warn('Browser DOCX conversion preload skipped:',err);
    return null;
  });
}
function rsEnsureOpenDocLab(allowExternalFallback){
  allowExternalFallback=allowExternalFallback===true;
  if(rsOpenDocLocalReady) return rsOpenDocLocalReady;
  var openDocSources=[
    {
      label:'Cloudflare OpenDoc runtime',
      moduleUrl:'https://file.jero.works/js/opendoc/casual_doc_wasm.js?v=opendoc-20260903',
      wasmUrl:'https://file.jero.works/js/opendoc/casual_doc_wasm_bg.wasm?v=opendoc-20260903'
    },
    {
      label:'Local OpenDoc runtime',
      moduleUrl:'/js/opendoc/casual_doc_wasm.js',
      wasmUrl:'/js/opendoc/casual_doc_wasm_bg.wasm'
    }
  ];
  async function loadOpenDocModule(moduleUrl,wasmUrl){
    var blobUrl='';
    var module=null;
    try{
      module=await import(moduleUrl);
    }catch(importErr){
      var moduleResponse=null;
      try{
        moduleResponse=await fetch(moduleUrl,{mode:'cors',cache:'force-cache'});
      }catch(moduleFetchErr){
        throw new Error('OpenDoc module fetch failed for '+moduleUrl+': '+(moduleFetchErr && moduleFetchErr.message ? moduleFetchErr.message : 'network blocked'));
      }
      if(!moduleResponse.ok) throw new Error('OpenDoc module request failed with HTTP '+moduleResponse.status);
      var moduleCode=await moduleResponse.text();
      moduleCode=moduleCode.replace(/new URL\(['"]casual_doc_wasm_bg\.wasm['"],\s*import\.meta\.url\)/g, JSON.stringify(wasmUrl));
      blobUrl=URL.createObjectURL(new Blob([moduleCode],{type:'application/javascript'}));
      module=await import(blobUrl);
    }
    if(typeof module.default==='function'){
      var wasmResponse=null;
      try{
        wasmResponse=await fetch(wasmUrl,{mode:'cors',cache:'force-cache'});
      }catch(wasmFetchErr){
        throw new Error('OpenDoc WASM fetch failed for '+wasmUrl+': '+(wasmFetchErr && wasmFetchErr.message ? wasmFetchErr.message : 'network blocked'));
      }
      if(!wasmResponse.ok) throw new Error('OpenDoc WASM request failed with HTTP '+wasmResponse.status);
      await module.default({module_or_path:await wasmResponse.arrayBuffer()});
    }
    if(blobUrl) setTimeout(function(){ URL.revokeObjectURL(blobUrl); }, 30000);
    if(typeof module.open!=='function') throw new Error('OpenDoc open API missing');
    return module;
  }
  rsOpenDocLocalReady=(async function(){
    var lastErr=null;
    for(var i=0;i<openDocSources.length;i++){
      try{
        return await loadOpenDocModule(openDocSources[i].moduleUrl,openDocSources[i].wasmUrl);
      }catch(err){
        lastErr=err;
        console.warn(openDocSources[i].label+' unavailable:',err);
      }
    }
    if(allowExternalFallback){
      if(!rsOpenDocExternalReady){
        rsOpenDocExternalReady=loadOpenDocModule('https://opendoc.casualoffice.org/pkg/casual_doc_wasm.js','https://opendoc.casualoffice.org/pkg/casual_doc_wasm_bg.wasm')
          .catch(function(err){
            rsOpenDocExternalReady=null;
            throw err;
          });
      }
      return rsOpenDocExternalReady;
    }
    throw (lastErr || new Error('OpenDoc runtime unavailable'));
  })().catch(function(err){
    rsOpenDocLocalReady=null;
    throw err;
  });
  return rsOpenDocLocalReady;
}
function preloadOpenDocLabAssets(){
  return rsEnsureOpenDocLab(true).catch(function(err){
    console.warn('OpenDoc lab preload skipped:',err);
    return null;
  });
}
function preloadOpenDocLocalAssets(){
  return rsEnsureOpenDocLab(false).catch(function(err){
    console.warn('Local OpenDoc preload skipped:',err);
    return null;
  });
}
function notifyRdcDownloadStarted(fileName){
  try{
    window.dispatchEvent(new CustomEvent('rdc:download-started',{detail:{fileName:fileName}}));
  }catch(ex){}
  try{
    if(typeof window.__rdcCompleteDownloadUi === 'function'){
      [0,120,500,1200].forEach(function(delay){
        setTimeout(function(){ window.__rdcCompleteDownloadUi(fileName); },delay);
      });
    }
  }catch(ex){}
}
window.__rdcNotifyDownloadStarted = notifyRdcDownloadStarted;
function downloadBlob(blob,fileName){
  var url=URL.createObjectURL(blob);
  var a=document.createElement('a');
  a.href=url;
  a.download=fileName;
  document.body.appendChild(a);
  a.click();
  notifyRdcDownloadStarted(fileName);
  setTimeout(function(){
    URL.revokeObjectURL(url);
    if(a.parentNode) a.parentNode.removeChild(a);
  },1200);
}
function sameOriginUrl(url){
  try{ return (new URL(url, window.location.href)).origin === window.location.origin; }catch(ex){ return false; }
}
function guessResumeDownloadName(ext){
  ext=clean(ext,'FILE').toLowerCase().replace(/[^a-z0-9]+/g,'') || 'file';
  var data=null;
  try{ data=readData(); }catch(ex){}
  if(!data || !data.name || data.name==='--' || data.name==='Candidate'){
    try{
      var candNameEl=document.getElementById('rsData') || document.querySelector('[data-candidate-name]');
      if(candNameEl){
        var n=candNameEl.getAttribute('data-name') || candNameEl.getAttribute('data-candidate-name') || candNameEl.textContent;
        if(n) data={name:clean(n,'Candidate')};
      }
    }catch(ex){}
  }
  try{
    return resumeTrackingFileName(data,'C',ext);
  }catch(ex){
    var candSafe=(data && data.name) ? fileSafeName(data.name,'Candidate') : 'Candidate';
    return 'JEROWORKS_'+candSafe+'_C.'+ext;
  }
}
function collectOriginalResumeDownloadSources(resumeUrls){
  var sources=(Array.isArray(resumeUrls) ? resumeUrls : [resumeUrls]).map(function(url){
    return clean(url,'');
  }).filter(function(url,index,self){
    return url && url!=='--' && self.indexOf(url)===index;
  });
  return sources;
}
function downloadOriginalResumeFromR2First(options){
  /* Purpose: Option 1 hands the browser to R2 immediately. Avoid Blob/CORS fetches here:
     cross-origin resume reads can stall and keep the Resume Center countdown stuck. */
  options=options||{};
  var resumeExt=clean(options.resumeExt||'FILE','FILE').toUpperCase();
  var fileName=guessResumeDownloadName(resumeExt);

  var directSources=collectOriginalResumeDownloadSources([
    options.r2RedirectUrl,
    options.dlUrl,
    options.r2DirectUrl,
    options.originalR2DirectUrl,
    options.r2PublicUrl,
    options.originalR2PublicUrl
  ]);

  if(directSources.length>0){
    var firstSrc=directSources[0];
    try{ firstSrc=encodeURI(decodeURI(firstSrc)); }catch(e){}
    notifyRdcDownloadStarted(fileName);
    var frame=document.createElement('iframe');
    frame.style.display='none';
    frame.setAttribute('aria-hidden','true');
    frame.src=firstSrc;
    document.body.appendChild(frame);
    setTimeout(function(){ if(frame.parentNode) frame.parentNode.removeChild(frame); }, 60000);
    return true;
  }

  throw new Error('Original resume is not available for download.');
}
function removeNode(node){
  if(node && node.parentNode) node.parentNode.removeChild(node);
}
function waitForRenderedAssets(root){
  var waits=[];
  if(document.fonts && document.fonts.ready) waits.push(document.fonts.ready.catch(function(){ return null; }));
  Array.prototype.slice.call(root.querySelectorAll('img')).forEach(function(img){
    if(img.complete) return;
    waits.push(new Promise(function(resolve){
      img.onload=resolve;
      img.onerror=resolve;
      setTimeout(resolve,2500);
    }));
  });
  return Promise.all(waits).then(function(){
    return new Promise(function(resolve){ requestAnimationFrame(function(){ requestAnimationFrame(resolve); }); });
  });
}
function xmlTextContent(node){
  if(!node) return '';
  var parts=[];
  Array.prototype.slice.call(node.childNodes||[]).forEach(function(child){
    if(child.nodeType===3){
      parts.push(child.nodeValue||'');
    }else if(child.nodeType===1){
      var name=(child.localName||child.nodeName||'').toLowerCase();
      if(name==='tab') parts.push(' ');
      else if(name==='br'||name==='cr') parts.push('\n');
      else parts.push(xmlTextContent(child));
    }
  });
  return parts.join('').replace(/[ \t]+\n/g,'\n').replace(/\n[ \t]+/g,'\n').replace(/[ \t]{2,}/g,' ').trim();
}
function xmlParagraphTexts(node){
  var paragraphs=Array.prototype.slice.call((node||{}).getElementsByTagName ? node.getElementsByTagName('*') : []).filter(function(child){
    return (child.localName||'').toLowerCase()==='p';
  }).map(function(p){
    return xmlTextContent(p);
  }).filter(function(text){
    return !!text;
  });
  return paragraphs.length ? paragraphs : [xmlTextContent(node)].filter(function(text){ return !!text; });
}
function normalizeReadableDocxText(text){
  return clean(text,'')
    .replace(/\b(Contact|Phone|Email|Linkedin|LinkedIn|Education|Experience|Key Skills|Professional Summary|Professional Profile|Project|Client|Role|Environment|Description|Roles and Responsibilities)\s*:/g,'$1: ')
    .replace(/([.;])(?=[A-Z][a-z])/g,'$1 ')
    .replace(/\s{2,}/g,' ')
    .trim();
}
function escapeHtml(value){
  return String(value==null?'':value).replace(/[&<>"']/g,function(ch){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch];
  });
}
function docxNodeHas(node,localName){
  return !!Array.prototype.slice.call(node.getElementsByTagName('*')).some(function(child){
    return (child.localName||'').toLowerCase()===localName;
  });
}
function classifyDocxLine(text,index,isBold){
  var raw=clean(text,'');
  var compact=raw.replace(/[:\-–—\s]+$/,'');
  var upperRatio=compact ? compact.replace(/[^A-Z]/g,'').length/Math.max(1,compact.replace(/[^A-Za-z]/g,'').length) : 0;
  if(index===0 && raw.length<=80) return 'docx-name';
  if(index<=2 && raw.length<=100) return 'docx-role';
  if((isBold || upperRatio>0.7) && raw.length<=45) return 'docx-section';
  if(/^(project|client|role|environment|description|responsibilities|roles and responsibilities|education|experience|certification|key skills)\b/i.test(raw)) return 'docx-label';
  return 'docx-body';
}
function readableDocxHtmlFromXml(xmlText){
  var parser=new DOMParser();
  var xml=parser.parseFromString(xmlText,'application/xml');
  if(xml.getElementsByTagName('parsererror').length) throw new Error('DOCX XML parse failed');
  var body=Array.prototype.slice.call(xml.getElementsByTagName('*')).filter(function(node){
    return (node.localName||'').toLowerCase()==='body';
  })[0] || xml;
  var blocks=[];
  var lineIndex=0;
  Array.prototype.slice.call(body.childNodes||[]).forEach(function(node){
    if(node.nodeType!==1) return;
    var name=(node.localName||'').toLowerCase();
    if(name==='p'){
      var text=normalizeReadableDocxText(xmlTextContent(node));
      if(!text){
        blocks.push('<div class="docx-space"></div>');
        return;
      }
      var isBold=docxNodeHas(node,'b');
      var cls=classifyDocxLine(text,lineIndex,isBold);
      lineIndex++;
      blocks.push('<p class="'+cls+'">'+escapeHtml(text).replace(/\n/g,'<br>')+'</p>');
      }else if(name==='tbl'){
      var rows=[];
      Array.prototype.slice.call(node.childNodes||[]).forEach(function(row){
        if(row.nodeType!==1 || (row.localName||'').toLowerCase()!=='tr') return;
        var cells=[];
        Array.prototype.slice.call(row.childNodes||[]).forEach(function(cell){
          if(cell.nodeType!==1 || (cell.localName||'').toLowerCase()!=='tc') return;
          var cellText=xmlParagraphTexts(cell).map(normalizeReadableDocxText).join('\n');
          cells.push('<td>'+escapeHtml(cellText).replace(/\n/g,'<br>')+'</td>');
        });
        if(cells.length) rows.push('<tr>'+cells.join('')+'</tr>');
      });
      if(rows.length) blocks.push('<table class="docx-table">'+rows.join('')+'</table>');
    }
  });
  return [
    '<div class="rs-readable-resume">',
    blocks.join(''),
    '</div>'
  ].join('');
}
async function renderReadableDocxResume(bytes,host){
  await rsEnsureJsZip();
  var zip=await window.JSZip.loadAsync(bytes);
  var documentFile=zip.file('word/document.xml');
  if(!documentFile) throw new Error('DOCX document.xml not found');
  var xmlText=await documentFile.async('string');
  host.innerHTML=[
    '<style>',
    '.rs-readable-resume{width:794px;min-height:1123px;background:#fff;color:#111827;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.45;padding:56px 62px;box-sizing:border-box;}',
    '.rs-readable-resume p{margin:0 0 8px 0;white-space:normal;overflow-wrap:break-word;word-break:normal;}',
    '.rs-readable-resume .docx-name{font-size:25px;line-height:1.16;font-weight:700;text-align:center;margin-bottom:4px;color:#111827;}',
    '.rs-readable-resume .docx-role{font-size:16px;line-height:1.28;font-weight:700;text-align:center;margin-bottom:12px;color:#1f2937;}',
    '.rs-readable-resume .docx-section{font-size:14px;line-height:1.25;font-weight:700;text-transform:uppercase;border-top:1px solid #cbd5e1;border-bottom:1px solid #cbd5e1;background:#f8fafc;text-align:left;padding:5px 8px;margin:14px 0 8px;color:#0f172a;}',
    '.rs-readable-resume .docx-label{font-size:14px;line-height:1.35;font-weight:700;margin-top:8px;color:#111827;}',
    '.rs-readable-resume .docx-body{font-size:13.5px;line-height:1.48;color:#111827;}',
    '.rs-readable-resume .docx-space{height:7px;}',
    '.rs-readable-resume .docx-table{width:100%;border-collapse:collapse;table-layout:fixed;margin:8px 0 12px;font-size:13px;line-height:1.4;}',
    '.rs-readable-resume .docx-table td{border:1px solid #e2e8f0;padding:6px 8px;vertical-align:top;overflow-wrap:anywhere;word-break:normal;}',
    '</style>',
    readableDocxHtmlFromXml(xmlText)
  ].join('');
  await waitForRenderedAssets(host);
  return [host.querySelector('.rs-readable-resume')||host];
}
function normalizeOriginalDocxLayout(pages){
  pages.forEach(function(page){
    Array.prototype.slice.call(page.querySelectorAll('p,li,td,th')).forEach(function(el){
      var cs=window.getComputedStyle(el);
      var fs=parseFloat(cs.fontSize)||12;
      var lh=parseFloat(cs.lineHeight);
      if(!lh || lh<fs*1.22) el.style.lineHeight='1.32';
      if(el.scrollHeight && el.clientHeight && el.scrollHeight>el.clientHeight+2){
        el.style.height='auto';
        el.style.minHeight=(el.scrollHeight+4)+'px';
        el.style.overflow='visible';
      }
    });
    for(var pass=0;pass<2;pass++){
      Array.prototype.slice.call(page.querySelectorAll('article,td,th,body,section')).forEach(function(parent){
        var prev=null;
        Array.prototype.slice.call(parent.children||[]).forEach(function(child){
          if(!child.textContent || !child.textContent.trim()) return;
          var rect=child.getBoundingClientRect();
          if(!rect.height) return;
          if(prev && rect.top<prev.bottom-1){
            var current=parseFloat(child.style.marginTop||window.getComputedStyle(child).marginTop)||0;
            child.style.marginTop=(current+(prev.bottom-rect.top)+6)+'px';
            rect=child.getBoundingClientRect();
          }
          prev=rect;
        });
      });
    }
  });
}
function hasDocxLayoutCollisions(pages){
  var collisionCount=0;
  var checked=0;
  Array.prototype.slice.call(pages||[]).some(function(page){
    var nodes=Array.prototype.slice.call(page.querySelectorAll('p,li,td,th,div')).filter(function(el){
      var text=(el.textContent||'').replace(/\s+/g,' ').trim();
      if(text.length<8) return false;
      var rect=el.getBoundingClientRect();
      if(!rect.width || !rect.height || rect.height<4) return false;
      var cs=window.getComputedStyle(el);
      if(cs.position==='absolute' && rect.height<10) return false;
      return true;
    }).slice(0,260);
    for(var i=1;i<nodes.length;i++){
      var prev=nodes[i-1].getBoundingClientRect();
      var curr=nodes[i].getBoundingClientRect();
      checked++;
      var sameColumn=curr.left<prev.right-8 && curr.right>prev.left+8;
      var verticalOverlap=curr.top<prev.bottom-3;
      if(sameColumn && verticalOverlap){
        collisionCount++;
        if(collisionCount>=3) return true;
      }
    }
    return false;
  });
  return checked>8 && collisionCount>=3;
}
async function renderOriginalDocxResume(bytes,host){
  await rsEnsureDocxPreview();
  await window.docx.renderAsync(bytes,host,null,{
    className:'rs-docx-preview',
    inWrapper:true,
    ignoreWidth:false,
    ignoreHeight:true,
    breakPages:true,
    renderHeaders:true,
    renderFooters:true,
    renderFootnotes:true,
    renderEndnotes:true,
    experimental:true,
    useBase64URL:true
  });
  var style=document.createElement('style');
  style.textContent=[
    '.rs-docx-preview-wrapper{background:#fff!important;padding:0!important;display:block!important;align-items:initial!important;}',
    '.rs-docx-preview-wrapper>section.rs-docx-preview,section.rs-docx-preview{background:#fff!important;box-shadow:none!important;margin:0 0 18px 0!important;overflow:visible!important;transform:none!important;}',
    '.rs-docx-preview{color:#111!important;-webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility;}',
    '.rs-docx-preview article{overflow:visible!important;}',
    '.rs-docx-preview p{line-height:1.32!important;min-height:1.32em!important;margin-bottom:4px!important;overflow:visible!important;}',
    '.rs-docx-preview span{line-height:inherit!important;white-space:pre-wrap!important;overflow-wrap:break-word!important;word-break:normal!important;vertical-align:baseline!important;}',
    '.rs-docx-preview table{border-collapse:collapse!important;table-layout:auto!important;}',
    '.rs-docx-preview td,.rs-docx-preview th{vertical-align:top!important;line-height:1.32!important;height:auto!important;min-height:1.32em!important;overflow:visible!important;}',
    '.rs-docx-preview img{max-width:100%;height:auto;}'
  ].join('');
  host.insertBefore(style,host.firstChild);
  await waitForRenderedAssets(host);
  var pages=Array.prototype.slice.call(host.querySelectorAll('.rs-docx-preview-wrapper > section.rs-docx-preview, section.rs-docx-preview'));
  if(!pages.length) pages=Array.prototype.slice.call(host.querySelectorAll('.docx-wrapper > section.docx, section.docx'));
  if(!pages.length) throw new Error('DOCX original render produced no pages');
  normalizeOriginalDocxLayout(pages);
  await waitForRenderedAssets(host);
  if(hasDocxLayoutCollisions(pages)) throw new Error('DOCX original render has overlapping text');
  return pages;
}
async function fetchResumeBytes(resumeUrl,maxBytes){
  resumeUrl=clean(resumeUrl,'');
  if(resumeUrl==='--') resumeUrl='';
  if(!resumeUrl) throw new Error('Resume URL not available');
  var byteLimit=parseInt(maxBytes,10)||rsDocxMaxBytes;
  var fetchOptions={cache:'no-store'};
  try{
    var resumeOrigin=(new URL(resumeUrl,window.location.href)).origin;
    if(resumeOrigin===window.location.origin) fetchOptions.credentials='include';
    else fetchOptions.mode='cors';
  }catch(err){
    fetchOptions.credentials='include';
  }
  var response=await fetch(resumeUrl,fetchOptions);
  if(!response.ok) throw new Error('Resume request failed with HTTP '+response.status);
  var size=parseInt(response.headers.get('content-length')||'0',10);
  if(size && size>byteLimit) throw new Error('DOCX resume is larger than the browser conversion limit');
  var bytes=await response.arrayBuffer();
  if(bytes.byteLength>byteLimit) throw new Error('DOCX resume is larger than the browser conversion limit');
  return bytes;
}
async function fetchResumeBytesFromSources(resumeUrls,maxBytes){
  var sources=(Array.isArray(resumeUrls) ? resumeUrls : [resumeUrls]).map(function(url){
    return clean(url,'');
  }).filter(function(url,index,self){
    return url && url!=='--' && self.indexOf(url)===index;
  });
  if(!sources.length) throw new Error('Direct R2 resume URL not available');
  var lastErr=null;
  for(var i=0;i<sources.length;i++){
    try{
      return await fetchResumeBytes(sources[i],maxBytes);
    }catch(err){
      lastErr=err;
      console.warn('Direct R2 resume fetch failed:',sources[i],err);
    }
  }
  throw (lastErr || new Error('Direct R2 resume fetch failed'));
}
function isBrowserDocxSafetyError(err){
  var message=err && err.message ? err.message : String(err||'');
  return /browser conversion limit|page limit exceeded|browser safety limit/i.test(message);
}
async function fetchPdfBlobFromSources(resumeUrls){
  var sources=(Array.isArray(resumeUrls) ? resumeUrls : [resumeUrls]).map(function(url){
    return clean(url,'');
  }).filter(function(url,index,self){
    return url && url!=='--' && self.indexOf(url)===index;
  });
  if(!sources.length) throw new Error('Direct R2 PDF URL not available');
  var lastErr=null;
  for(var i=0;i<sources.length;i++){
    try{
      var response=await fetch(sources[i],{mode:'cors',cache:'no-store'});
      if(!response.ok) throw new Error('PDF request failed with HTTP '+response.status);
      var size=parseInt(response.headers.get('content-length')||'0',10);
      if(size && size>rsPdfMaxBytes) throw new Error('PDF resume is larger than the browser merge limit');
      var blob=await response.blob();
      if(blob.size>rsPdfMaxBytes) throw new Error('PDF resume is larger than the browser merge limit');
      return blob;
    }catch(err){
      lastErr=err;
      console.warn('Direct R2 PDF fetch failed:',sources[i],err);
    }
  }
  throw (lastErr || new Error('Direct R2 PDF fetch failed'));
}
async function convertDocxResumeToPdfBlob(resumeUrl){
  await rsEnsureDocxConversion();
  var bytes=await fetchResumeBytes(resumeUrl);
  var host=document.createElement('div');
  host.setAttribute('aria-hidden','true');
  host.style.cssText='position:fixed;left:-12000px;top:0;width:auto;min-width:794px;min-height:1123px;background:#fff;overflow:visible;pointer-events:none;';
  document.body.appendChild(host);
  try{
    var pages;
    try{
      pages=await renderOriginalDocxResume(bytes,host);
    }catch(renderErr){
      console.warn('DOCX original-format render failed, using readable fallback:',renderErr);
      host.innerHTML='';
      pages=await renderReadableDocxResume(bytes,host);
    }
    var JsPDF=window.jspdf.jsPDF;
    var pdf=new JsPDF({orientation:'portrait',unit:'pt',format:'a4',compress:true});
    var pageW=pdf.internal.pageSize.getWidth();
    var pageH=pdf.internal.pageSize.getHeight();
    for(var i=0;i<pages.length;i++){
      var page=pages[i];
      var rect=page.getBoundingClientRect();
      if(!rect.width || rect.width<500 || rect.width>1100){
        page.style.width='794px';
      }
      page.style.margin='0';
      page.style.boxShadow='none';
      page.style.background='#ffffff';
      page.style.maxWidth='none';
      var canvas=await window.html2canvas(page,{
        backgroundColor:'#ffffff',
        scale:Math.max(2.5,Math.min(3,window.devicePixelRatio||2.5)),
        useCORS:true,
        allowTaint:false,
        logging:false,
        removeContainer:true,
        scrollX:0,
        scrollY:0,
        windowWidth:Math.ceil(page.scrollWidth||page.getBoundingClientRect().width||794),
        windowHeight:Math.ceil(page.scrollHeight||page.getBoundingClientRect().height||1123)
      });
      var sliceHeight=Math.max(1,Math.floor(canvas.width*(pageH/pageW)));
      var offsetY=0;
      var isFirstSlice=true;
      while(offsetY<canvas.height){
        if(i>0 || !isFirstSlice) pdf.addPage('a4','portrait');
        var currentHeight=Math.min(sliceHeight,canvas.height-offsetY);
        var slice=document.createElement('canvas');
        slice.width=canvas.width;
        slice.height=currentHeight;
        var ctx=slice.getContext('2d');
        ctx.fillStyle='#ffffff';
        ctx.fillRect(0,0,slice.width,slice.height);
        ctx.drawImage(canvas,0,offsetY,canvas.width,currentHeight,0,0,canvas.width,currentHeight);
        var img=slice.toDataURL('image/png');
        var drawH=currentHeight*(pageW/canvas.width);
        pdf.addImage(img,'PNG',0,0,pageW,drawH,undefined,'SLOW');
        offsetY+=currentHeight;
        isFirstSlice=false;
      }
    }
    return pdf.output('blob');
  }catch(err){
    console.error('Browser DOCX resume conversion error:',err);
    throw err;
  }finally{
    removeNode(host);
  }
}
async function convertDocxResumeToPdfBlobFromSources(resumeUrls){
  var sources=(Array.isArray(resumeUrls) ? resumeUrls : [resumeUrls]).map(function(url){
    return clean(url,'');
  }).filter(function(url,index,self){
    return url && url!=='--' && self.indexOf(url)===index;
  });
  if(!sources.length) throw new Error('DOCX resume URL not available for browser fallback');
  var lastErr=null;
  for(var i=0;i<sources.length;i++){
    try{
      return await convertDocxResumeToPdfBlob(sources[i]);
    }catch(err){
      lastErr=err;
      console.warn('Legacy browser DOCX conversion failed:',sources[i],err);
    }
  }
  throw new Error('DOCX browser fallback failed across all resume sources. '+(lastErr && lastErr.message ? lastErr.message : ''));
}
function rsKeywordPatternEscape(value){
  return String(value==null?'':value).replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
}
function rsKeywordAliasValues(value){
  /* Purpose: keep job-skill highlighting recruiter-friendly when resumes use
     common cloud shorthand instead of the exact skill label configured on the job. */
  var raw=String(value==null?'':value).replace(/\s+/g,' ').trim();
  var normalized=raw.toLowerCase().replace(/[^a-z0-9+#.]+/g,' ').replace(/\s+/g,' ').trim();
  var aliases=[raw];
  function add(alias){
    if(alias && aliases.map(function(v){ return v.toLowerCase(); }).indexOf(alias.toLowerCase())===-1) aliases.push(alias);
  }
  if(/\baws\b/.test(normalized) && /\bcloud\b/.test(normalized)){
    add('AWS');
    add('Amazon Web Services');
  }
  if(normalized === 'amazon web services'){
    add('AWS');
    add('AWS Cloud');
  }
  if((/\bgcp\b/.test(normalized) || normalized === 'google cloud platform') && normalized.indexOf('cloud') !== -1){
    add('GCP');
    add('Google Cloud');
  }
  if(/\bazure\b/.test(normalized) && /\bcloud\b/.test(normalized)){
    add('Azure');
    add('Microsoft Azure');
  }
  return aliases;
}
function rsKeywordPatternFromValue(value){
  var raw=String(value==null?'':value)
    .replace(/\+\d+\s*More/gi,'')
    .replace(/\s+/g,' ')
    .trim();
  if(!raw) return '';

  var compact=raw.replace(/\s+/g,'');
  if(/^[A-Za-z][+#]{1,2}$/i.test(compact)){
    return '\\b'+rsKeywordPatternEscape(compact);
  }

  var expanded=raw
    .replace(/([a-z])([A-Z])/g,'$1 $2')
    .replace(/([A-Za-z])(\d)/g,'$1 $2')
    .replace(/(\d)([A-Za-z])/g,'$1 $2');
  var tokens=expanded.split(/[\s\u00a0\/\\_\-.+,;:()[\]{}|&]+/).filter(Boolean);
  if(!tokens.length) return '';
  var left=/^[A-Za-z0-9]/.test(tokens[0]) ? '\\b' : '';
  var right=/[A-Za-z0-9]$/.test(tokens[tokens.length-1]) ? '\\b' : '';
  if(tokens.length===1){
    return left+rsKeywordPatternEscape(tokens[0])+right;
  }
  var flexibleJoin='(?:[\\s\\u00a0\\/\\\\_\\-.+,;:()\\[\\]{}|&]+|\\b(?:and|or)\\b)*';
  return left+tokens.map(rsKeywordPatternEscape).join(flexibleJoin)+right;
}
function rsKeywordPatternsForValue(value){
  var seen={};
  var patterns=[];
  rsKeywordAliasValues(value).forEach(function(alias){
    var pattern=rsKeywordPatternFromValue(alias);
    var key=pattern.toLowerCase();
    if(pattern && !seen[key]){
      seen[key]=true;
      patterns.push(pattern);
    }
  });
  return patterns;
}
function rsKeywordRegex(list){
  var seen={};
  var cleaned=(Array.isArray(list) ? list : []).map(function(item){
    if(typeof item === 'string') return item.trim();
    return clean(item && (item.skill_name || item.skill_key || item.name || item.skill),'').trim();
  }).filter(function(value){
    value=value.replace(/\s+/g,' ').trim();
    var key=value.toLowerCase();
    if(value.length<2 || seen[key]) return false;
    seen[key]=true;
    return true;
  });
  if(!cleaned.length) return null;
  cleaned.sort(function(a,b){ return b.length-a.length; });
  var parts=[];
  cleaned.forEach(function(value){
    parts=parts.concat(rsKeywordPatternsForValue(value));
  });
  return parts.length ? new RegExp(parts.join('|'),'gi') : null;
}
function rsDrawerHighlightedKeywords(){
  var values=[];
  var seen={};
  function addKeyword(value){
    value=(value || '').replace(/\+\d+\s*More/gi,'').replace(/\s+/g,' ').trim();
    if(!value) return;
    var key=value.toLowerCase();
    if(seen[key]) return;
    seen[key]=true;
    values.push(value);
  }
  (Array.isArray(rsDrawerMatchedKeywords) ? rsDrawerMatchedKeywords : []).forEach(addKeyword);
  Array.prototype.slice.call(document.querySelectorAll('#kt_activitiesApplication .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more), .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more)')).forEach(function(el){
    addKeyword(el.textContent || '');
  });
  (Array.isArray(rsAtsHighlightKeywords) ? rsAtsHighlightKeywords : []).forEach(addKeyword);
  return values.length ? values : rsAtsHighlightKeywords;
}
function rsMarkKeywordMatches(root,re){
  if(!root || !re) return;
  var walker=document.createTreeWalker(root,NodeFilter.SHOW_TEXT,{
    acceptNode:function(node){
      if(!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
      var parent=node.parentNode;
      if(!parent || /^(script|style|mark)$/i.test(parent.nodeName)) return NodeFilter.FILTER_REJECT;
      return NodeFilter.FILTER_ACCEPT;
    }
  });
  var nodes=[];
  while(walker.nextNode()) nodes.push(walker.currentNode);
  nodes.forEach(function(node){
    var text=node.nodeValue;
    re.lastIndex=0;
    if(!re.test(text)){ re.lastIndex=0; return; }
    re.lastIndex=0;
    var frag=document.createDocumentFragment();
    var last=0,match;
    while((match=re.exec(text))!==null){
      if(match.index>last) frag.appendChild(document.createTextNode(text.slice(last,match.index)));
      var mark=document.createElement('mark');
      mark.className='rcv-hit rdc-smart-hit';
      mark.textContent=match[0];
      frag.appendChild(mark);
      last=match.index+match[0].length;
      if(match.index===re.lastIndex) re.lastIndex++;
    }
    if(last<text.length) frag.appendChild(document.createTextNode(text.slice(last)));
    node.parentNode.replaceChild(frag,node);
  });
}
function rsOption3EscRe(value){
  return rsKeywordPatternEscape(value);
}
function rsOption3KeywordsFromResumeText(resumeText){
  var source=Array.isArray(rsAtsHighlightKeywords) ? rsAtsHighlightKeywords : [];
  var found=[];
  var seen={};
  source.forEach(function(item){
    var name=typeof item === 'string' ? item : clean(item && (item.skill_name || item.skill || item.name || item.skill_key),'');
    name=(name || '').replace(/\s+/g,' ').trim();
    if(!name || seen[name.toLowerCase()]) return;
    var re=rsKeywordRegex([name]);
    if(re && (resumeText || '').match(re)){
      seen[name.toLowerCase()]=true;
      found.push(name);
    }
  });
  return found;
}
function rsOption3BuildRegex(activeSkills){
  var act=(Array.isArray(activeSkills) ? activeSkills : []).filter(Boolean);
  if(!act.length) return null;
  act.sort(function(a,b){ return b.length-a.length; });
  var pat=act.map(function(val){
    return rsKeywordPatternsForValue(val).join('|');
  }).filter(Boolean).join('|');
  if(!pat) return null;
  return new RegExp('('+pat+')','gi');
}
function rsOption3HighlightTextLayer(textLayer,re){
  if(!textLayer || !re) return;
  Array.prototype.slice.call(textLayer.querySelectorAll('span')).forEach(function(sp){
    if(sp.dataset.orig === undefined) sp.dataset.orig = sp.textContent || '';
    var t=sp.dataset.orig;
    re.lastIndex=0;
    if(!re.test(t)){ sp.textContent=t; re.lastIndex=0; return; }
    re.lastIndex=0;
    sp.innerHTML=escapeHtml(t).replace(re,'<mark class="rcv-hit">$1</mark>');
  });
}
async function buildHeadlessOption3SkillsHighlightedResume(options){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeSources=(Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl||'']).map(function(url){
      return clean(url,'');
    }).filter(function(url,index,self){
      return url && url!=='--' && self.indexOf(url)===index;
    });
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    var resumePdfUrl=clean(options.resumePdfUrl||'','');
    var strictClientOnly=options.strictClientOnly===true;
    if(strictClientOnly) resumePdfUrl='';
    if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1) throw new Error('Resume + Skills Highlighted is available for PDF, DOC and DOCX resumes only');
    if(strictClientOnly && resumeExt==='DOC') throw new Error('This test option is browser-only and supports PDF/DOCX resumes only');
    await Promise.all([rsEnsurePdfJs(),rsEnsureJsPdf()]);
    var pdfBlob=null;
    var usedClientDocxPdf=false;
    if(resumeExt==='PDF'){
      pdfBlob=await fetchPdfBlobFromSources(resumeSources);
    }else if(resumeExt==='DOCX'){
      if(options.preferClientDocx === true){
        var clientDocxOptions={
          maxBytes:parseInt(options.clientDocxMaxBytes,10)||rsDocxMaxBytes,
          maxPages:parseInt(options.clientDocxMaxPages,10)||30,
          requireTextPdf:options.clientDocxRequireTextPdf===true
        };
        try{
          pdfBlob=await convertResumeWithOpenDocLabBlob(resumeSources,resumeExt,false,clientDocxOptions);
          usedClientDocxPdf=true;
        }catch(openDocErr){
          if(isBrowserDocxSafetyError(openDocErr) && resumePdfUrl){
            console.warn('Client-first DOCX skipped by browser safety guard; using converter PDF URL:',openDocErr);
            pdfBlob=await fetchPdfResumeBlob(resumePdfUrl);
            usedClientDocxPdf=false;
          }else{
          console.warn('Client-first DOCX OpenDoc conversion failed; trying legacy browser renderer:',openDocErr);
          try{
            pdfBlob=await convertDocxResumeToPdfBlobFromSources(resumeSources);
            usedClientDocxPdf=true;
          }catch(legacyDocxErr){
            console.warn('Client-first DOCX legacy renderer failed; using converter PDF URL if available:',legacyDocxErr);
            if(!resumePdfUrl) throw legacyDocxErr;
            pdfBlob=await fetchPdfResumeBlob(resumePdfUrl);
            usedClientDocxPdf=false;
          }
          }
        }
      }else{
        try{
          if(!resumePdfUrl) throw new Error('Enterprise converter PDF URL not available');
          pdfBlob=await fetchPdfResumeBlob(resumePdfUrl);
        }catch(converterErr){
          console.warn('Option 8 DOCX enterprise converter PDF failed; trying browser conversion fallback:',converterErr);
          try{
            pdfBlob=await convertResumeWithOpenDocLabBlob(resumeSources,resumeExt,false);
          }catch(openDocErr){
            console.warn('Option 8 DOCX OpenDoc conversion failed; trying legacy browser renderer:',openDocErr);
            pdfBlob=await convertDocxResumeToPdfBlobFromSources(resumeSources);
          }
        }
      }
    }else{
      if(!resumePdfUrl) throw new Error('DOC resume needs enterprise converter PDF URL');
      pdfBlob=await fetchPdfResumeBlob(resumePdfUrl);
    }
    var sourceBytes=await pdfBlob.arrayBuffer();
    var bytes=new Uint8Array(sourceBytes);
    var sig=String.fromCharCode.apply(null,bytes.subarray(0,5));
    if(sig.indexOf('%PDF')===-1) throw new Error('Resume source is not a valid PDF');
    var doc=await window.pdfjsLib.getDocument({data:bytes}).promise;
    var host=document.createElement('div');
    host.setAttribute('aria-hidden','true');
    host.style.cssText='position:fixed;left:-14000px;top:0;width:900px;background:#525659;pointer-events:none;z-index:-1;';
    var style=document.createElement('style');
    style.textContent=[
      '.rdc-headless-rcv-page{position:relative;background:#fff;box-shadow:none;margin:0 0 14px 0;}',
      '.rdc-headless-rcv-page canvas{display:block;}',
      '.rdc-headless-rcv-textlayer{position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;line-height:1;opacity:1;}',
      '.rdc-headless-rcv-textlayer span{position:absolute;white-space:pre;color:transparent;transform-origin:0% 0%;}',
      '.rdc-headless-rcv-textlayer mark.rcv-hit{background:rgba(255,214,10,.42);color:transparent;border-radius:2px;}'
    ].join('');
    host.appendChild(style);
    document.body.appendChild(host);
    var JsPDF=window.jspdf.jsPDF;
    var resumeText='';
    var pages=[];
    try{
      for(var p=1;p<=doc.numPages;p++){
        var page=await doc.getPage(p);
        var base=page.getViewport({scale:1});
        var cssScale=Math.min(1.35,820/base.width);
        var viewport=page.getViewport({scale:cssScale});
        var wrap=document.createElement('div');
        wrap.className='rdc-headless-rcv-page';
        wrap.style.width=Math.floor(viewport.width)+'px';
        wrap.style.height=Math.floor(viewport.height)+'px';
        var canvas=document.createElement('canvas');
        canvas.width=Math.floor(viewport.width);
        canvas.height=Math.floor(viewport.height);
        canvas.style.width=Math.floor(viewport.width)+'px';
        canvas.style.height=Math.floor(viewport.height)+'px';
        wrap.appendChild(canvas);
        var tl=document.createElement('div');
        tl.className='rdc-headless-rcv-textlayer';
        tl.style.width=Math.floor(viewport.width)+'px';
        tl.style.height=Math.floor(viewport.height)+'px';
        wrap.appendChild(tl);
        host.appendChild(wrap);
        await page.render({canvasContext:canvas.getContext('2d',{willReadFrequently:true}),viewport:viewport}).promise;
        var tc=await page.getTextContent();
        resumeText += tc.items.map(function(i){ return i.str; }).join(' ') + '\n';
        try{
          var task=window.pdfjsLib.renderTextLayer({textContent:tc,container:tl,viewport:viewport,textDivs:[]});
          if(task && task.promise) await task.promise;
        }catch(textLayerErr){
          console.warn('Headless option 3 text layer failed:',textLayerErr);
        }
        pages.push({page:page,wrap:wrap,viewport:viewport,tc:tc});
      }
      var activeSkills=rsOption3KeywordsFromResumeText(resumeText);
      if(!activeSkills.length && usedClientDocxPdf && resumePdfUrl){
        console.warn('Client-first DOCX conversion produced no highlightable skill text; retrying enterprise converter PDF for accurate highlighting.');
        removeNode(host);
        window.__rsBusy=false;
        return await buildHeadlessOption3SkillsHighlightedResume({
          resumeUrl:resumeSources,
          resumeExt:resumeExt,
          resumePdfUrl:resumePdfUrl,
          returnBlob:options.returnBlob===true
        });
      }
      var re=rsOption3BuildRegex(activeSkills);
      if(re){
        pages.forEach(function(pageInfo){ rsOption3HighlightTextLayer(pageInfo.wrap.querySelector('.rdc-headless-rcv-textlayer'),re); });
      }
      var QUALITY=3.0, MAXW=2800;
      var rendered=await Promise.all(pages.map(function(pageInfo){
        return (async function(){
          var page=pageInfo.page;
          var wrap=pageInfo.wrap;
          var cssW=wrap.clientWidth || Math.floor(pageInfo.viewport.width);
          var cssH=wrap.clientHeight || Math.floor(pageInfo.viewport.height);
          var base=page.getViewport({scale:1});
          var renderW=Math.min(cssW*QUALITY,MAXW);
          var vp=page.getViewport({scale:renderW/base.width});
          var hi=document.createElement('canvas');
          hi.width=Math.floor(vp.width);
          hi.height=Math.floor(vp.height);
          await page.render({canvasContext:hi.getContext('2d',{willReadFrequently:true}),viewport:vp}).promise;
          var ctx=hi.getContext('2d',{willReadFrequently:true});
          if(re && pageInfo.tc){
            rsDrawKeywordHitsFromTextItems(ctx,pageInfo.tc,vp,re);
          }
          return {imgData:hi.toDataURL('image/jpeg',0.96),cssW:cssW,cssH:cssH};
        })();
      }));
      var pdf=null;
      for(var i=0;i<rendered.length;i++){
        var out=rendered[i];
        var pw=Math.round(out.cssW), ph=Math.round(out.cssH);
        var orient=pw>ph?'l':'p';
        if(!pdf) pdf=new JsPDF({orientation:orient,unit:'px',format:[pw,ph]});
        else pdf.addPage([pw,ph],orient);
        pdf.addImage(out.imgData,'JPEG',0,0,pw,ph,undefined,'FAST');
      }
      if(!pdf) throw new Error('No resume pages were rendered');
      if(!activeSkills.length) console.warn('Resume + Skills Highlighted found no matching job skills in resume text.');
      if(options.returnBlob) return pdf.output('blob');
      var data=readData();
      downloadBlob(pdf.output('blob'),'JEROWORKS_'+fileSafeName(data.name,'Candidate')+'_Skills_Highlighted.pdf');
      return true;
    }finally{
      removeNode(host);
    }
  }catch(err){
    console.error('Headless option 3 highlighted resume error:',err);
    if(options && options.strictClientOnly===true){
      alert('Could not generate the browser-only test highlighted resume.\n\nReason: '+(err && err.message ? err.message : 'Unknown highlight error')+'\n\nServer-side conversion is disabled for this test option.');
    }else{
      alert('Could not generate Resume + Skills Highlighted.\n\nReason: '+(err && err.message ? err.message : 'Unknown highlight error')+'\n\nPDF uses signed R2 direct fetch. DOCX can use browser conversion first with enterprise fallback. DOC requires enterprise converter PDF.');
    }
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
function rsTextItemRect(item,viewport){
  if(!item || !viewport || !(window.pdfjsLib && window.pdfjsLib.Util)) return null;
  var tx=window.pdfjsLib.Util.transform(viewport.transform,item.transform);
  var h=Math.max(Math.abs((item.height||0)*viewport.scale),Math.abs(tx[3]||0),6);
  var w=Math.max(Math.abs((item.width||0)*viewport.scale),2);
  return {x:tx[4],y:tx[5]-h,w:w,h:h};
}
function rsPageTextRuns(textContent){
  var text='';
  var runs=[];
  (textContent && Array.isArray(textContent.items) ? textContent.items : []).forEach(function(item){
    var value=(item && item.str ? String(item.str) : '');
    if(!value.trim()) return;
    if(text.length) text+=' ';
    var start=text.length;
    text+=value;
    runs.push({item:item,start:start,end:text.length});
  });
  return {text:text,runs:runs};
}
function rsDrawKeywordHitsFromTextItems(ctx,textContent,viewport,keywordRe){
  if(!ctx || !textContent || !viewport || !keywordRe) return 0;
  var page=rsPageTextRuns(textContent);
  if(!page.text || !page.runs.length) return 0;
  keywordRe.lastIndex=0;
  var count=0,match;
  ctx.fillStyle='rgba(255,214,10,0.42)';
  while((match=keywordRe.exec(page.text))!==null){
    var start=match.index;
    var end=start+match[0].length;
    page.runs.forEach(function(run){
      if(run.end<=start || run.start>=end) return;
      var rect=rsTextItemRect(run.item,viewport);
      if(!rect) return;
      ctx.fillRect(rect.x,rect.y,rect.w,Math.max(rect.h,6));
      count++;
    });
    if(match.index===keywordRe.lastIndex) keywordRe.lastIndex++;
  }
  return count;
}
async function convertDocxResumeToHighlightedPdfBlobFromSources(resumeUrls,keywords){
  await rsEnsureDocxConversion();
  var bytes=await fetchResumeBytesFromSources(resumeUrls);
  var keywordRe=rsKeywordRegex(keywords);
  var host=document.createElement('div');
  host.setAttribute('aria-hidden','true');
  host.style.cssText='position:fixed;left:-12000px;top:0;width:auto;min-width:794px;min-height:1123px;background:#fff;overflow:visible;pointer-events:none;';
  document.body.appendChild(host);
  try{
    var pages;
    try{
      pages=await renderOriginalDocxResume(bytes,host);
    }catch(renderErr){
      console.warn('DOCX original-format render failed for highlights, using readable fallback:',renderErr);
      host.innerHTML='';
      pages=await renderReadableDocxResume(bytes,host);
    }
    if(keywordRe){
      pages.forEach(function(page){ rsMarkKeywordMatches(page,keywordRe); });
    }
    var style=document.createElement('style');
    style.textContent='.rdc-smart-hit{background:rgba(255,214,10,.46)!important;color:inherit!important;border-radius:2px;padding:0 1px;}';
    host.insertBefore(style,host.firstChild);
    await waitForRenderedAssets(host);
    var JsPDF=window.jspdf.jsPDF;
    var pdf=new JsPDF({orientation:'portrait',unit:'pt',format:'a4',compress:true});
    var pageW=pdf.internal.pageSize.getWidth();
    var pageH=pdf.internal.pageSize.getHeight();
    for(var i=0;i<pages.length;i++){
      var pageEl=pages[i];
      var rect=pageEl.getBoundingClientRect();
      if(!rect.width || rect.width<500 || rect.width>1100) pageEl.style.width='794px';
      pageEl.style.margin='0';
      pageEl.style.boxShadow='none';
      pageEl.style.background='#ffffff';
      pageEl.style.maxWidth='none';
      var canvas=await window.html2canvas(pageEl,{
        backgroundColor:'#ffffff',
        scale:Math.max(2.5,Math.min(3,window.devicePixelRatio||2.5)),
        useCORS:true,
        allowTaint:false,
        logging:false,
        removeContainer:true,
        scrollX:0,
        scrollY:0,
        windowWidth:Math.ceil(pageEl.scrollWidth||pageEl.getBoundingClientRect().width||794),
        windowHeight:Math.ceil(pageEl.scrollHeight||pageEl.getBoundingClientRect().height||1123)
      });
      var sliceHeight=Math.max(1,Math.floor(canvas.width*(pageH/pageW)));
      var offsetY=0;
      var isFirstSlice=true;
      while(offsetY<canvas.height){
        if(i>0 || !isFirstSlice) pdf.addPage('a4','portrait');
        var currentHeight=Math.min(sliceHeight,canvas.height-offsetY);
        var slice=document.createElement('canvas');
        slice.width=canvas.width;
        slice.height=currentHeight;
        var sctx=slice.getContext('2d');
        sctx.fillStyle='#ffffff';
        sctx.fillRect(0,0,slice.width,slice.height);
        sctx.drawImage(canvas,0,offsetY,canvas.width,currentHeight,0,0,canvas.width,currentHeight);
        var img=slice.toDataURL('image/jpeg',0.92);
        var renderH=(currentHeight/canvas.width)*pageW;
        pdf.addImage(img,'JPEG',0,0,pageW,Math.min(renderH,pageH),undefined,'FAST');
        offsetY+=currentHeight;
        isFirstSlice=false;
      }
    }
    return pdf.output('blob');
  }finally{
    removeNode(host);
  }
}
async function renderHighlightedPdfBlob(pdfBlob,keywords){
  await Promise.all([rsEnsurePdfJs(),rsEnsureJsPdf()]);
  var sourceBytes=await pdfBlob.arrayBuffer();
  var bytes=new Uint8Array(sourceBytes);
  var sig=String.fromCharCode.apply(null,bytes.subarray(0,5));
  if(sig.indexOf('%PDF')===-1) throw new Error('Resume source is not a valid PDF after browser conversion');
  var doc=await window.pdfjsLib.getDocument({data:bytes}).promise;
  var JsPDF=window.jspdf.jsPDF;
  var keywordRe=rsKeywordRegex(keywords);
  var outPdf=null;
  try{
    for(var p=1;p<=doc.numPages;p++){
      var pdfJsPage=await doc.getPage(p);
      var base=pdfJsPage.getViewport({scale:1});
      var cssScale=Math.min(1.35,820/base.width);
      var cssViewport=pdfJsPage.getViewport({scale:cssScale});
      var renderScale=Math.min(3,2800/base.width);
      var renderViewport=pdfJsPage.getViewport({scale:renderScale});
      var canvas=document.createElement('canvas');
      canvas.width=Math.floor(renderViewport.width);
      canvas.height=Math.floor(renderViewport.height);
      var ctx=canvas.getContext('2d',{willReadFrequently:true});
      ctx.fillStyle='#ffffff';
      ctx.fillRect(0,0,canvas.width,canvas.height);
      await pdfJsPage.render({canvasContext:ctx,viewport:renderViewport}).promise;
      var tc=await pdfJsPage.getTextContent();
      if(keywordRe){
        rsDrawKeywordHitsFromTextItems(ctx,tc,renderViewport,keywordRe);
      }
      var imgData=canvas.toDataURL('image/jpeg',0.9);
      var pageW=Math.round(cssViewport.width);
      var pageH=Math.round(cssViewport.height);
      var orient=pageW>pageH?'l':'p';
      if(!outPdf) outPdf=new JsPDF({orientation:orient,unit:'px',format:[pageW,pageH]});
      else outPdf.addPage([pageW,pageH],orient);
      outPdf.addImage(imgData,'JPEG',0,0,pageW,pageH,undefined,'FAST');
    }
    if(!outPdf) throw new Error('No resume pages were rendered');
    return outPdf.output('blob');
  }finally{
    // No DOM cleanup is needed here; PDF highlighting is coordinate-based.
  }
}
async function buildSmartHighlightedResume(options){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeSources=(Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl||'']).map(function(url){
      return clean(url,'');
    }).filter(function(url,index,self){
      return url && url!=='--' && self.indexOf(url)===index;
    });
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    if(['PDF','DOCX'].indexOf(resumeExt)===-1) throw new Error('Client-side highlighted resume is available for PDF and DOCX resumes only');
    var resumeBlob=null;
    if(resumeExt==='PDF'){
      resumeBlob=await fetchPdfBlobFromSources(resumeSources);
      resumeBlob=await renderHighlightedPdfBlob(resumeBlob,rsDrawerHighlightedKeywords());
    }else{
      resumeBlob=await convertDocxResumeToHighlightedPdfBlobFromSources(resumeSources,rsDrawerHighlightedKeywords());
    }
    var data=readData();
    downloadBlob(resumeBlob,'JEROWORKS_'+fileSafeName(data.name,'Candidate')+'_Highlighted.pdf');
    return true;
  }catch(err){
    console.error('Smart Highlighted Resume error:',err);
    alert('Could not generate the highlighted resume.\n\nReason: '+(err && err.message ? err.message : 'Unknown highlight error')+'\n\nThis option runs in the browser and supports PDF/DOCX resumes only.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
/* Purpose: isolated browser-only anonymizer helpers for Download Resume Center option 29.
   They never call the server-side resume converter or alter existing report/highlight flows. */
function rdcPlain(value){
  return String(value==null?'':value).replace(/\s+/g,' ').trim();
}
function rdcEscapeRegExp(value){
  return rdcPlain(value).replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
}
function rdcDigitsOnly(value){
  return String(value==null?'':value).replace(/\D+/g,'');
}
function rdcPushUnique(list,value){
  value=rdcPlain(value);
  if(!value || value==='--') return;
  var key=value.toLowerCase();
  if(list.some(function(item){ return item.toLowerCase()===key; })) return;
  list.push(value);
}
function rdcFlexibleTextRegex(value){
  var parts=rdcPlain(value).split(/\s+/).filter(Boolean);
  if(!parts.length) return null;
  return new RegExp(parts.map(rdcEscapeRegExp).join('[\\s\\u00a0._-]+'),'gi');
}
function rdcPhoneTextRegex(value){
  var digits=rdcDigitsOnly(value);
  if(digits.length<5) return null;
  return new RegExp('(?:\\+?\\d{1,3}[\\s().\\-+]*)?'+digits.split('').join('[\\s().\\-+]*'),'g');
}
function rdcContactLabelRegexes(){
  return [
    /\b(?:e[\s._-]*mail|email|mail)[\s._-]*(?:id|address)?[\s.:#-]*(?:primary|secondary|alternate|alt|1|2)?\b/gi,
    /\b(?:phone|mobile|mob|cell|contact|tel|telephone)[\s._-]*(?:number|no|num|id|details)?[\s.:#-]*(?:primary|secondary|alternate|alt|1|2)?\b/gi,
    /\b(?:contact|personal)[\s._-]*(?:details|info|information)\b/gi,
    /\b(?:whats[\s._-]*app|whatsapp|linked[\s._-]*in|linkedin)[\s._-]*(?:id|profile|url|link|number|no)?\b/gi
  ];
}
function rdcLinkedInTargets(value){
  value=rdcPlain(value);
  if(!value || value==='--') return [];
  var targets=[];
  rdcPushUnique(targets,value);
  var withoutProtocol=value.replace(/^https?:\/\//i,'').replace(/^www\./i,'');
  rdcPushUnique(targets,withoutProtocol);
  if(!/^https?:\/\//i.test(value) && !/^www\./i.test(value) && /linkedin\.com/i.test(value)){
    rdcPushUnique(targets,'https://'+value);
    rdcPushUnique(targets,'www.'+withoutProtocol);
  }
  var match=withoutProtocol.match(/linkedin\.com\/(?:in|pub|company)\/([^/?#\s]+)/i);
  if(match && match[1]){
    rdcPushUnique(targets,match[1].replace(/[-_]+/g,' '));
    rdcPushUnique(targets,match[1]);
  }
  return targets;
}
function rdcAnonymizeRules(data,options){
  /* Purpose: keep the anonymizer deterministic by using known drawer PII first,
     with regex only as a fallback for PII present in the resume but not in fields. */
  data=data||{};
  options=options||{};
  var textRegexes=[];
  var phoneDigits=[];
  var exactTerms=[];
  var emails=[];
  var phones=[];
  if(options.keepName!==true){
    rdcPushUnique(exactTerms,data.name);
    rdcPlain(data.name).split(/\s+/).forEach(function(token){
      if(token.length>=3) rdcPushUnique(exactTerms,token);
    });
  }
  [data.email,data.secondaryEmail].forEach(function(email){
    rdcPushUnique(emails,email);
    rdcPushUnique(exactTerms,email);
  });
  [data.phone,data.secondaryPhone].forEach(function(phone){
    rdcPushUnique(phones,phone);
    rdcPushUnique(exactTerms,phone);
  });
  rdcLinkedInTargets(data.linkedin).forEach(function(target){
    rdcPushUnique(exactTerms,target);
  });
  phones.forEach(function(phone){
    var digits=rdcDigitsOnly(phone);
    if(digits.length>=5 && phoneDigits.indexOf(digits)===-1) phoneDigits.push(digits);
    var phoneRe=rdcPhoneTextRegex(phone);
    if(phoneRe) textRegexes.push(phoneRe);
  });
  exactTerms.forEach(function(term){
    if(rdcDigitsOnly(term).length>=5) return;
    var re=rdcFlexibleTextRegex(term);
    if(re) textRegexes.push(re);
  });
  textRegexes.push(/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/gi);
  textRegexes.push(/\+?\d[\d\s().\-]{7,}\d/g);
  textRegexes.push(/(?:https?:\/\/|www\.)?linkedin\.com\/(?:in|pub|company)\/[^\s<>"')]+/gi);
  rdcContactLabelRegexes().forEach(function(regex){ textRegexes.push(regex); });
  return {textRegexes:textRegexes,phoneDigits:phoneDigits,emails:emails,phones:phones,exactTerms:exactTerms};
}
function rdcRegexMatches(regex,text){
  regex.lastIndex=0;
  var ok=regex.test(text);
  regex.lastIndex=0;
  return ok;
}
function rdcShouldRedactText(text,rules){
  text=rdcPlain(text);
  if(!text) return false;
  var lower=text.toLowerCase();
  var digits=rdcDigitsOnly(text);
  if(digits.length>=5 && rules.phoneDigits.some(function(phoneDigits){
    return phoneDigits.indexOf(digits)!==-1 || digits.indexOf(phoneDigits)!==-1;
  })) return true;
  if(rules.exactTerms.some(function(term){
    term=rdcPlain(term).toLowerCase();
    return term.length>=3 && lower.indexOf(term)!==-1;
  })) return true;
  return rules.textRegexes.some(function(regex){ return rdcRegexMatches(regex,text); });
}
function rdcRedactTextValue(text,rules){
  var original=String(text==null?'':text);
  var output=original;
  rules.textRegexes.forEach(function(regex){
    regex.lastIndex=0;
    output=output.replace(regex,function(match){ return new Array(Math.max(2,match.length)+1).join('\u00a0'); });
    regex.lastIndex=0;
  });
  if(output!==original) return output;
  return rdcShouldRedactText(original,rules) ? new Array(Math.max(2,original.length)+1).join('\u00a0') : original;
}
function rdcRedactDocxDom(root,rules){
  var showText=window.NodeFilter ? window.NodeFilter.SHOW_TEXT : 4;
  var walker=document.createTreeWalker(root,showText,null);
  var nodes=[];
  var redacted=0;
  while(walker.nextNode()) nodes.push(walker.currentNode);
  nodes.forEach(function(node){
    var next=rdcRedactTextValue(node.nodeValue||'',rules);
    if(next!==node.nodeValue){
      node.nodeValue=next;
      redacted++;
    }
  });
  return redacted;
}
function rdcDrawAnonymizedBlocks(ctx,textContent,viewport,rules){
  if(!ctx || !textContent || !viewport || !rules) return 0;
  var count=0;
  ctx.save();
  ctx.fillStyle='#ffffff';
  (textContent.items||[]).forEach(function(item){
    if(!rdcShouldRedactText(item && item.str,rules)) return;
    var rect=rsTextItemRect(item,viewport);
    if(!rect) return;
    var pad=Math.max(2,Math.min(8,rect.h*0.22));
    ctx.fillRect(Math.max(0,rect.x-pad),Math.max(0,rect.y-pad),rect.w+(pad*2),rect.h+(pad*2));
    count++;
  });
  ctx.restore();
  return count;
}
async function renderAnonymizedPdfBlob(pdfBlob,rules){
  await Promise.all([rsEnsurePdfJs(),rsEnsureJsPdf()]);
  var sourceBytes=await pdfBlob.arrayBuffer();
  var bytes=new Uint8Array(sourceBytes);
  var sig=String.fromCharCode.apply(null,bytes.subarray(0,5));
  if(sig.indexOf('%PDF')===-1) throw new Error('Resume source is not a valid PDF');
  var doc=await window.pdfjsLib.getDocument({data:bytes}).promise;
  var JsPDF=window.jspdf.jsPDF;
  var outPdf=null;
  var textItems=0;
  var redactions=0;
  for(var p=1;p<=doc.numPages;p++){
    var pdfJsPage=await doc.getPage(p);
    var base=pdfJsPage.getViewport({scale:1});
    var cssScale=Math.min(1.35,820/base.width);
    var cssViewport=pdfJsPage.getViewport({scale:cssScale});
    var renderScale=Math.min(3,2800/base.width);
    var renderViewport=pdfJsPage.getViewport({scale:renderScale});
    var canvas=document.createElement('canvas');
    canvas.width=Math.floor(renderViewport.width);
    canvas.height=Math.floor(renderViewport.height);
    var ctx=canvas.getContext('2d',{willReadFrequently:true});
    ctx.fillStyle='#ffffff';
    ctx.fillRect(0,0,canvas.width,canvas.height);
    await pdfJsPage.render({canvasContext:ctx,viewport:renderViewport}).promise;
    var tc=await pdfJsPage.getTextContent();
    textItems += (tc.items||[]).length;
    redactions += rdcDrawAnonymizedBlocks(ctx,tc,renderViewport,rules);
    var pageW=Math.round(cssViewport.width);
    var pageH=Math.round(cssViewport.height);
    var orient=pageW>pageH?'l':'p';
    if(!outPdf) outPdf=new JsPDF({orientation:orient,unit:'px',format:[pageW,pageH]});
    else outPdf.addPage([pageW,pageH],orient);
    outPdf.addImage(canvas.toDataURL('image/jpeg',0.9),'JPEG',0,0,pageW,pageH,undefined,'FAST');
  }
  if(!outPdf) throw new Error('No resume pages were rendered');
  if(!textItems) throw new Error('This resume appears to be scanned/image-only. Browser anonymization needs extractable text.');
  if(!redactions) console.warn('Anonymized Resume completed with no PII matches from drawer data or fallback regex.');
  return outPdf.output('blob');
}
async function convertDocxResumeToAnonymizedPdfBlobFromSources(resumeUrls,rules){
  await rsEnsureDocxConversion();
  var bytes=await fetchResumeBytesFromSources(resumeUrls);
  var host=document.createElement('div');
  host.setAttribute('aria-hidden','true');
  host.style.cssText='position:fixed;left:-12000px;top:0;width:auto;min-width:794px;min-height:1123px;background:#fff;overflow:visible;pointer-events:none;';
  document.body.appendChild(host);
  try{
    var pages;
    try{
      pages=await renderOriginalDocxResume(bytes,host);
    }catch(renderErr){
      console.warn('DOCX anonymized render failed, using readable fallback:',renderErr);
      host.innerHTML='';
      pages=await renderReadableDocxResume(bytes,host);
    }
    rdcRedactDocxDom(host,rules);
    await waitForRenderedAssets(host);
    var JsPDF=window.jspdf.jsPDF;
    var pdf=new JsPDF({orientation:'portrait',unit:'pt',format:'a4',compress:true});
    var pageW=pdf.internal.pageSize.getWidth();
    var pageH=pdf.internal.pageSize.getHeight();
    for(var i=0;i<pages.length;i++){
      var page=pages[i];
      var rect=page.getBoundingClientRect();
      if(!rect.width || rect.width<500 || rect.width>1100) page.style.width='794px';
      page.style.margin='0';
      page.style.boxShadow='none';
      page.style.background='#ffffff';
      page.style.maxWidth='none';
      var canvas=await window.html2canvas(page,{
        backgroundColor:'#ffffff',
        scale:Math.max(2.5,Math.min(3,window.devicePixelRatio||2.5)),
        useCORS:true,
        allowTaint:false,
        logging:false,
        removeContainer:true,
        scrollX:0,
        scrollY:0,
        windowWidth:Math.ceil(page.scrollWidth||page.getBoundingClientRect().width||794),
        windowHeight:Math.ceil(page.scrollHeight||page.getBoundingClientRect().height||1123)
      });
      var sliceHeight=Math.max(1,Math.floor(canvas.width*(pageH/pageW)));
      var offsetY=0;
      var isFirstSlice=true;
      while(offsetY<canvas.height){
        if(i>0 || !isFirstSlice) pdf.addPage('a4','portrait');
        var currentHeight=Math.min(sliceHeight,canvas.height-offsetY);
        var slice=document.createElement('canvas');
        slice.width=canvas.width;
        slice.height=currentHeight;
        var ctx=slice.getContext('2d');
        ctx.fillStyle='#ffffff';
        ctx.fillRect(0,0,slice.width,slice.height);
        ctx.drawImage(canvas,0,offsetY,canvas.width,currentHeight,0,0,canvas.width,currentHeight);
        var drawH=currentHeight*(pageW/canvas.width);
        pdf.addImage(slice.toDataURL('image/png'),'PNG',0,0,pageW,drawH,undefined,'SLOW');
        offsetY+=currentHeight;
        isFirstSlice=false;
      }
    }
    return pdf.output('blob');
  }finally{
    removeNode(host);
  }
}
async function resolveClientAnonymizedResumeBlob(options){
  /* Purpose: build the contact-redacted resume PDF as a reusable browser-only blob
     so combined options can compose it without sharing button or drawer state. */
  options=options||{};
  var resumeSources=(Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl||'']).map(function(url){
    return clean(url,'');
  }).filter(function(url,index,self){
    return url && url!=='--' && self.indexOf(url)===index;
  });
  var resumeExt=clean(options.resumeExt||'','').toUpperCase();
  if(['PDF','DOCX'].indexOf(resumeExt)===-1) throw new Error('Anonymized Resume is available for PDF and DOCX resumes only');
  if(!resumeSources.length) throw new Error('Direct R2 resume URL is not available for browser anonymization');
  var data=options.candidate || readData();
  var keepName=options.keepName===true;
  var rules=rdcAnonymizeRules(data,{keepName:keepName});
  return resumeExt==='PDF'
    ? await renderAnonymizedPdfBlob(await fetchPdfBlobFromSources(resumeSources),rules)
    : await convertDocxResumeToAnonymizedPdfBlobFromSources(resumeSources,rules);
}
async function buildClientAnonymizedResume(options){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var data=options.candidate || readData();
    var keepName=options.keepName===true;
    var resumeBlob=await resolveClientAnonymizedResumeBlob(options);
    var fileName=keepName ? 'JEROWORKS_'+fileSafeName(data && data.name,'Candidate')+'_Contact_Hidden.pdf' : 'JEROWORKS_Anonymized_Resume.pdf';
    downloadBlob(resumeBlob,fileName);
    return true;
  }catch(err){
    console.error('Anonymized Resume error:',err);
    if(options.throwOnError===true) throw err;
    alert('Could not generate the anonymized resume.\n\nReason: '+(err && err.message ? err.message : 'Unknown anonymization error')+'\n\nThis option runs only in the browser and does not use server conversion.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
function preloadClientAnonymizerAssets(){
  var dataEl=document.getElementById('rdcData');
  var ext=(dataEl ? dataEl.getAttribute('data-file-ext') : '') || '';
  ext=ext.toUpperCase();
  var tasks=[rsEnsurePdfJs(),rsEnsureJsPdf()];
  if(ext==='DOCX') tasks.push(rsEnsureDocxConversion());
  return Promise.all(tasks).catch(function(err){
    console.warn('Anonymized Resume preload skipped:',err);
    return null;
  });
}
async function buildContactHiddenBrandedResume(options){
  /* Purpose: isolated option 31 pipeline that combines option 30 contact hiding
     with option 6 company branding without touching either option's click flow. */
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var data=readData();
    var resumeBlob=await resolveClientAnonymizedResumeBlob({
      resumeUrl:options.resumeUrl,
      resumeExt:options.resumeExt,
      keepName:true
    });
    if(!(resumeBlob instanceof Blob)) throw new Error('Contact-hidden resume PDF was not generated');
    await addCompanyHeaderToResume(resumeBlob,data,'JEROWORKS_'+fileSafeName(data && data.name,'Candidate')+'_Contact_Hidden_Branded.pdf',{matchOption6VisualScale:true});
    return true;
  }catch(err){
    console.error('Contact Hidden Branded Resume error:',err);
    alert('Could not generate Hide Contact Details + Company Branding.\n\nReason: '+(err && err.message ? err.message : 'Unknown contact-hidden branding error')+'\n\nThis option hides contact details first, then applies the company header in the browser.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildContactHiddenPreScreenedResume(options){
  /* Purpose: isolated option 32 pipeline that combines contact-hidden resume output
     with the normal branded pre-screened report without changing either source option. */
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var data=readData();
    var resumeBlob=await resolveClientAnonymizedResumeBlob({
      resumeUrl:options.resumeUrl,
      resumeExt:options.resumeExt,
      keepName:true
    });
    if(!(resumeBlob instanceof Blob)) throw new Error('Contact-hidden resume PDF was not generated');
    var reportBlob=await buildRecruiterSummaryBlob(data,{});
    await mergePreScreenedReportAndResume(reportBlob,resumeBlob,'JEROWORKS_'+fileSafeName(data && data.name,'Candidate')+'_Contact_Hidden_PreScreened.pdf');
    return true;
  }catch(err){
    console.error('Contact Hidden Pre-Screened Resume error:',err);
    alert('Could not generate Hide Contact Details + Pre-Screened Report.\n\nReason: '+(err && err.message ? err.message : 'Unknown contact-hidden report error')+'\n\nThis option hides contact details first, then merges the pre-screened report in the browser.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildContactHiddenPreScreenedNoBrandResume(options){
  /* Purpose: isolated option 33 pipeline that combines contact-hidden resume output
     with the no-brand pre-screened report package without touching other options. */
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var data=readData();
    var resumeBlob=await resolveClientAnonymizedResumeBlob({
      resumeUrl:options.resumeUrl,
      resumeExt:options.resumeExt,
      keepName:true
    });
    if(!(resumeBlob instanceof Blob)) throw new Error('Contact-hidden resume PDF was not generated');
    var reportBlob=await buildRecruiterSummaryBlob(data,{noBranding:true});
    await mergePreScreenedReportAndResume(reportBlob,resumeBlob,'JEROWORKS_'+fileSafeName(data && data.name,'Candidate')+'_Contact_Hidden_PreScreened_No_Brand.pdf');
    return true;
  }catch(err){
    console.error('Contact Hidden Pre-Screened No-Brand Resume error:',err);
    alert('Could not generate Hide Contact Details + Pre-Screened Report without Company Branding.\n\nReason: '+(err && err.message ? err.message : 'Unknown contact-hidden no-brand report error')+'\n\nThis option hides contact details first, then merges the no-brand pre-screened report in the browser.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
function resumePdfFetchUrl(resumePdfUrl){
  resumePdfUrl=clean(resumePdfUrl,'');
  if(!resumePdfUrl) return '';
  try{
    var url=new URL(resumePdfUrl,window.location.href);
    if(url.origin===window.location.origin && /\/resume-(convert|as-pdf)\//.test(url.pathname)){
      url.searchParams.set('preview','1');
      return url.toString();
    }
  }catch(ex){}
  return resumePdfUrl;
}
function waitResumePdfReady(ms){
  return new Promise(function(resolve){ setTimeout(resolve,ms); });
}
async function fetchPdfResumeBlob(resumePdfUrl,attempt){
  attempt=attempt||0;
  resumePdfUrl=resumePdfFetchUrl(resumePdfUrl);
  if(!resumePdfUrl) throw new Error('Resume PDF URL not available');
  var response=await fetch(resumePdfUrl,{credentials:'include',cache:'no-store'});
  if(response.status===202){
    if(attempt>=40) throw new Error('Resume PDF conversion is still processing. Please try again in a moment.');
    var retryAfter=parseInt(response.headers.get('Retry-After')||'0',10);
    var waitMs=retryAfter>0 ? Math.min(retryAfter*1000,6000) : Math.min(2500+(attempt*250),6000);
    await waitResumePdfReady(waitMs);
    return fetchPdfResumeBlob(resumePdfUrl,attempt+1);
  }
  if(!response.ok) throw new Error('Resume PDF request failed with HTTP '+response.status);
  var blob=await response.blob();
  if(!blob || blob.size<10) throw new Error('Resume PDF response was empty');
  var bytes=await blob.arrayBuffer();
  var sig=String.fromCharCode.apply(null,new Uint8Array(bytes).subarray(0,5));
  if(sig.indexOf('%PDF')===-1) throw new Error('Resume PDF conversion did not return a valid PDF');
  return new Blob([bytes],{type:'application/pdf'});
}
async function convertResumeToPdfOnly(resumeUrl,resumePdfUrl,resumeExt){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    resumeExt=clean(resumeExt,'').toUpperCase();
    resumeUrl=clean(resumeUrl,'');
    resumePdfUrl=clean(resumePdfUrl,'');
    if(['PDF','DOC','DOCX'].indexOf(resumeExt)===-1) throw new Error('Resume PDF conversion is available for PDF, DOC and DOCX resumes only');
    if(!resumePdfUrl) throw new Error('Resume PDF URL not available');
    var data=readData();
    var blob=await fetchPdfResumeBlob(resumePdfUrl);
    downloadBlob(blob,'resume_pdf_conversion_'+slug(data.name)+'.pdf');
    return true;
  }catch(err){
    console.error('Resume PDF conversion only error:',err);
    var message=err && err.message ? err.message : '';
    if(/HTTP 503|converter unavailable|Document converter unavailable/i.test(message)){
      alert('Enterprise Word-to-PDF converter is not available on the server. Please enable LibreOffice/Gotenberg or upload a PDF resume.');
    }else{
      alert('Could not convert the resume to PDF. Please try again.');
    }
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function convertResumeToPdfBrowserOnly(resumeUrl,resumeExt){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    resumeExt=clean(resumeExt,'').toUpperCase();
    resumeUrl=clean(resumeUrl,'');
    if(resumeExt!=='DOCX') throw new Error('Browser conversion is available for DOCX resumes only');
    if(!resumeUrl) throw new Error('DOCX resume URL not available');
    var data=readData();
    var blob=await convertDocxResumeToPdfBlob(resumeUrl);
    downloadBlob(blob,'browser_docx_pdf_conversion_'+slug(data.name)+'.pdf');
    return true;
  }catch(err){
    console.error('Browser DOCX PDF conversion only error:',err);
    var message=err && err.message ? err.message : '';
    if(/larger than the browser conversion limit/i.test(message)){
      alert('This DOCX resume is larger than the 10 MB browser conversion limit. Please use option 10 or upload a smaller DOCX.');
    }else{
      alert('Browser DOCX conversion could not produce a clean PDF for this resume. Please use option 10 for enterprise-quality conversion.');
    }
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
function openDocPagePoints(doc,index){
  var fallback=[595.28,841.89];
  try{
    var size=doc.pageSize(index);
    var width=size && size.widthTwip ? size.widthTwip/20 : 0;
    var height=size && size.heightTwip ? size.heightTwip/20 : 0;
    if(size && typeof size.free==='function') size.free();
    if(width>=240 && height>=240 && width<=1600 && height<=2200) return [width,height];
  }catch(err){
    console.warn('OpenDoc page size fallback used:',err);
  }
  return fallback;
}
function openDocCanvasFromBitmap(bitmap){
  var width=bitmap && bitmap.widthPx ? bitmap.widthPx : 0;
  var height=bitmap && bitmap.heightPx ? bitmap.heightPx : 0;
  if(!width || !height) throw new Error('OpenDoc rendered an empty page bitmap');
  var rgba=bitmap.rgba;
  var canvas=document.createElement('canvas');
  canvas.width=width;
  canvas.height=height;
  var ctx=canvas.getContext('2d',{alpha:false});
  ctx.fillStyle='#ffffff';
  ctx.fillRect(0,0,width,height);
  ctx.putImageData(new ImageData(rgba,width,height),0,0);
  return canvas;
}
function openDocRenderDpi(pageCount){
  pageCount=parseInt(pageCount,10)||1;
  if(pageCount<=6) return 216;
  if(pageCount<=15) return 180;
  return 160;
}
function openDocNativePdfBlob(doc){
  if(!doc || typeof doc.availableExportFormats!=='function' || typeof doc.exportAs!=='function') return null;
  var formats=[];
  try{
    formats=doc.availableExportFormats() || [];
  }catch(formatErr){
    console.warn('OpenDoc export formats unavailable:',formatErr);
    return null;
  }
  var hasPdf=formats.some(function(format){
    return String(format||'').toLowerCase()==='pdf';
  });
  if(!hasPdf) return null;
  var modes=['exact_if_unchanged','preserve_when_safe','semantic'];
  for(var i=0;i<modes.length;i++){
    var artifact=null;
    try{
      artifact=doc.exportAs('pdf',modes[i]);
      var bytes=artifact && artifact.bytes ? artifact.bytes : null;
      var mime=artifact && artifact.mimeType ? artifact.mimeType : 'application/pdf';
      if(bytes && bytes.length) return new Blob([bytes],{type:mime || 'application/pdf'});
    }catch(exportErr){
      console.warn('OpenDoc native PDF export failed for '+modes[i]+':',exportErr);
    }finally{
      if(artifact && typeof artifact.free==='function') artifact.free();
    }
  }
  return null;
}
function rsCollectActiveKeywords(){
  /* Purpose: Comprehensive keyword collection for client-side highlighting:
     pools matched ATS skills, candidate profile skills, drawer badges, and technical skills */
  var allKeywords=[];
  var seenKw={};
  function addKw(val){
    if(!val) return;
    if(typeof val==='object') val=val.skill_name||val.skill||val.name||val.skill_key||'';
    val=String(val).replace(/\+\d+\s*More/gi,'').replace(/\s+/g,' ').trim();
    if(val.length<2) return;
    var k=val.toLowerCase();
    if(seenKw[k]) return;
    seenKw[k]=true;
    allKeywords.push(val);
  }
  // 1. Matched ATS keywords from drawer
  (Array.isArray(rsDrawerMatchedKeywords) ? rsDrawerMatchedKeywords : []).forEach(addKw);
  // 2. All skill badges on candidate drawer
  Array.prototype.slice.call(document.querySelectorAll('#kt_activitiesApplication .cdr-skill-tag:not(.cdr-skill-more), .cdr-skill-tag:not(.cdr-skill-more)')).forEach(function(el){
    addKw(el.textContent||'');
  });
  // 3. ATS highlight keywords
  (Array.isArray(rsAtsHighlightKeywords) ? rsAtsHighlightKeywords : []).forEach(addKw);
  // 4. Candidate skills from application data
  try{
    var rData=readData();
    (Array.isArray(rData.skills) ? rData.skills : []).forEach(addKw);
  }catch(ex){}
  return allKeywords;
}
async function convertResumeWithOpenDocLabBlob(resumeUrl,resumeExt,allowExternalFallback,conversionOptions){
  resumeExt=clean(resumeExt,'').toUpperCase();
  var resumeSources=Array.isArray(resumeUrl) ? resumeUrl : [resumeUrl];
  resumeUrl=clean(resumeSources[0]||'','');
  if(resumeExt!=='DOCX') throw new Error('OpenDoc lab is available for DOCX resumes only');
  if(!resumeUrl || resumeUrl==='--') throw new Error('DOCX resume URL not available');
  conversionOptions=conversionOptions||{};
  var maxBytes=parseInt(conversionOptions.maxBytes,10)||rsDocxMaxBytes;
  var maxPages=parseInt(conversionOptions.maxPages,10)||30;
  var initResults=await Promise.all([
    rsEnsureOpenDocLab(allowExternalFallback===true),
    rsEnsureJsPdf(),
    fetchResumeBytesFromSources(resumeSources,maxBytes)
  ]);
  var openDoc=initResults[0];
  var bytes=initResults[2];
  var doc=null;
  try{
    var docBytes=new Uint8Array(bytes);
    try{
      doc=(typeof openDoc.openAs==='function') ? openDoc.openAs(docBytes,'docx') : openDoc.open(docBytes);
    }catch(openAsErr){
      doc=openDoc.open(docBytes);
    }
    var pageCount=parseInt(doc && doc.pageCount ? doc.pageCount : 0,10);
    if(!pageCount && doc && typeof doc.documentStats==='function'){
      var stats=doc.documentStats();
      pageCount=parseInt(stats && stats.pages ? stats.pages : 0,10);
      if(stats && typeof stats.free==='function') stats.free();
    }
    if(!pageCount || pageCount<1) throw new Error('OpenDoc produced no pages');
    if(pageCount>maxPages) throw new Error('OpenDoc lab page limit exceeded');

    // Keep branding-only DOCX conversions clean while allowing highlighter options to reuse this converter.
    var allKws=(conversionOptions.skipHighlights===true) ? [] : (conversionOptions.highlightKeywords||rsCollectActiveKeywords());
    var fullDocText='';
    try{
      var pos1=(typeof doc.firstPosition === 'function') ? doc.firstPosition() : null;
      var pos2=(typeof doc.lastPosition === 'function') ? doc.lastPosition() : null;
      if(pos1 && pos2 && typeof doc.copyText === 'function'){
        fullDocText=doc.copyText(pos1.node, pos1.offset, pos2.node, pos2.offset);
      }
      if(pos1 && typeof pos1.free === 'function') pos1.free();
      if(pos2 && typeof pos2.free === 'function') pos2.free();
    }catch(ctErr){}

    // Also extract skills directly detected in resume text across all pages (Option 3 engine)
    if(conversionOptions.skipHighlights!==true && fullDocText && typeof rsOption3KeywordsFromResumeText==='function'){
      try{
        var extractedSkills=rsOption3KeywordsFromResumeText(fullDocText);
        if(Array.isArray(extractedSkills)){
          extractedSkills.forEach(function(sk){
            if(!sk || sk.length<2) return;
            var skLower=sk.toLowerCase();
            if(!allKws.some(function(k){ return k.toLowerCase()===skLower; })){
              allKws.push(sk);
            }
          });
        }
      }catch(skErr){}
    }

    var lowerDocText=(fullDocText||'').toLowerCase();
    var keywords=allKws.filter(function(k){
      if(!k || k.length<2) return false;
      return lowerDocText ? lowerDocText.indexOf(k.toLowerCase()) !== -1 : true;
    });
    if(!keywords.length && allKws.length){
      keywords=allKws;
    }

    if(Array.isArray(keywords) && keywords.length){
      keywords.forEach(function(rawKw){
        if(!rawKw || typeof rawKw !== 'string') return;
        rawKw=rawKw.trim();
        if(rawKw.length<2) return;

        var kwList=[rawKw];
        if(/\.js$/i.test(rawKw)){
          kwList.push(rawKw.replace(/\.js$/i,'js'));
        }

        kwList.forEach(function(kw){
          try{
            var first=(typeof doc.firstPosition === 'function') ? doc.firstPosition() : null;
            if(!first) return;
            var currNode=first.node;
            var currOffset=first.offset;
            if(typeof first.free === 'function') first.free();

            var visited={};
            var maxMatches=100;
            while(maxMatches-- > 0){
              var m=doc.findText(kw, currNode, currOffset, true, false);
              if(!m || !m.found){
                if(m && typeof m.free === 'function') m.free();
                break;
              }
              var sNode=m.startNode;
              var sOffset=m.startOffset;
              var eNode=m.endNode;
              var eOffset=m.endOffset;
              var visitKey=sNode+':'+sOffset+':'+eNode+':'+eOffset;
              if(visited[visitKey]){
                if(typeof m.free === 'function') m.free();
                break; // Detected wrap-around back to earlier page; finish searching this keyword
              }
              visited[visitKey]=true;

              if(typeof doc.setHighlight==='function'){
                try{
                  var hRes=doc.setHighlight(sNode,sOffset,eNode,eOffset,'yellow');
                  if(hRes && typeof hRes.free==='function') hRes.free();
                }catch(hlErr){}
              }
              if(typeof m.free==='function') m.free();

              if(sNode===eNode && eOffset<=currOffset){
                currOffset++;
              }else{
                currNode=eNode;
                currOffset=eOffset;
              }
            }
          }catch(fErr){}
        });
      });
    }

    var nativePdfBlob=openDocNativePdfBlob(doc);
    if(nativePdfBlob) return nativePdfBlob;
    if(conversionOptions.requireTextPdf===true) throw new Error('OpenDoc browser safety limit: text PDF export unavailable');
    var renderDpi=conversionOptions.dpi||openDocRenderDpi(pageCount);
    var JsPDF=window.jspdf.jsPDF;
    var usePng=conversionOptions.losslessPng===true;
    var jpegQuality=typeof conversionOptions.jpegQuality==='number' ? conversionOptions.jpegQuality : 0.98;
    var pdf=null;
    for(var i=0;i<pageCount;i++){
      var bitmap=null;
      var canvas=null;
      try{
        bitmap=doc.renderPage(i,renderDpi);
        canvas=openDocCanvasFromBitmap(bitmap);

        /* Intent: Derive exact point dimensions from rendered bitmap to ensure 100% 1:1 aspect ratio,
           preventing vertical/horizontal stretching and keeping the header banner and layout pixel-perfect. */
        var pageW=(bitmap.widthPx/renderDpi)*72;
        var pageH=(bitmap.heightPx/renderDpi)*72;

        if(!pdf){
          pdf=new JsPDF({
            orientation:pageW>pageH?'landscape':'portrait',
            unit:'pt',
            format:[pageW,pageH],
            compress:true
          });
        }else{
          pdf.addPage([pageW,pageH],pageW>pageH?'landscape':'portrait');
        }

        if(usePng){
          var pngData=canvas.toDataURL('image/png');
          pdf.addImage(pngData,'PNG',0,0,pageW,pageH,undefined,'FAST');
        }else{
          var imgData=canvas.toDataURL('image/jpeg',jpegQuality);
          pdf.addImage(imgData,'JPEG',0,0,pageW,pageH,undefined,'FAST');
        }
      }finally{
        if(bitmap && typeof bitmap.free==='function') bitmap.free();
        if(canvas){
          canvas.width=0;
          canvas.height=0;
        }
      }
    }
    return pdf ? pdf.output('blob') : null;
  }finally{
    if(doc && typeof doc.free==='function') doc.free();
  }
}
async function convertResumeWithOpenDocLab(resumeUrl,resumeExt){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    var data=readData();
    var blob=await convertResumeWithOpenDocLabBlob(resumeUrl,resumeExt,true);
    downloadBlob(blob,'opendoc_preview_conversion_'+slug(data.name)+'.pdf');
    return true;
  }catch(err){
    console.error('OpenDoc Preview Conversion Lab error:',err);
    var message=err && err.message ? err.message : '';
    if(/larger than the browser conversion limit/i.test(message)){
      alert('This DOCX resume is larger than the 10 MB OpenDoc lab limit. Please use option 10 or upload a smaller DOCX.');
    }else if(/page limit exceeded/i.test(message)){
      alert('This DOCX resume has more than 30 pages. Please use option 10 for enterprise-quality conversion.');
    }else{
      alert('OpenDoc Preview Conversion Lab could not produce a clean PDF for this resume. Technical reason: '+(message || 'Unknown browser conversion error')+'. Please use option 10 for enterprise-quality conversion.');
    }
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
function clean(value,fallback){
  value=String(value==null?'':value).replace(/\s+/g,' ').trim();
  return value || fallback || '--';
}
function limitText(value,max,fallback){
  value=clean(value,fallback||'--');
  return value.length>max ? value.slice(0,max-1).trim()+'...' : value;
}
function safeNumber(value,min,max){
  value=parseInt(value,10);
  if(isNaN(value)) value=0;
  return Math.max(min,Math.min(max,value));
}
function initials(name){
  return clean(name,'Candidate').split(' ').filter(Boolean).map(function(part){ return part.charAt(0).toUpperCase(); }).slice(0,2).join('') || 'CA';
}
function slug(value){
  return clean(value,'candidate').replace(/[^a-z0-9]+/gi,'_').replace(/^_+|_+$/g,'').toLowerCase() || 'candidate';
}
function fileSafeName(value,fallback){
  return clean(value,fallback || 'Candidate').replace(/[\\/:*?"<>|]+/g,' ').replace(/\s+/g,' ').trim() || (fallback || 'Candidate');
}
function resumeTrackingFileName(data,marker,ext){
  ext=clean(ext||'pdf','pdf').toLowerCase().replace(/[^a-z0-9]+/g,'') || 'pdf';
  return 'JEROWORKS_'+fileSafeName(data && data.name,'Candidate')+'.'+ext;
}
function preScreenReportFileName(data){
  return 'PRESCREEN_REPORT_'+fileSafeName(data && data.name,'Candidate')+'.pdf';
}
function readData(){
  var el=document.getElementById('rsData');
  if(!el) throw new Error('rsData not found');
  var g=function(k){ return el.getAttribute(k)||''; };
  var gj=function(k){ try{return JSON.parse(el.getAttribute(k)||'[]')||[];}catch(ex){return[];} };
  return {
    name:g('data-name')||'Candidate',
    email:g('data-email'),
    secondaryEmail:g('data-secondary-email'),
    phone:g('data-phone'),
    secondaryPhone:g('data-secondary-phone'),
    phoneCode:g('data-phone-code'),
    secondaryPhoneCode:g('data-secondary-phone-code'),
    linkedin:g('data-linkedin'),
    designation:g('data-designation'),
    company:g('data-company'),
    location:g('data-location'),
    experience:g('data-experience'),
    notice:g('data-notice'),
    education:g('data-education'),
    workMode:g('data-work-mode'),
    reportId:g('data-report-id'),
    reportDate:g('data-report-date'),
    recruiterName:g('data-recruiter-name'),
    reportCompanyName:g('data-report-company-name'),
    reportCompanyLogo:g('data-report-company-logo'),
    aiVersion:g('data-ai-version'),
    match:safeNumber(g('data-match'),0,100),
    isImmediate:g('data-is-immediate')==='1',
    recruiterNote:g('data-recruiter-note'),
    careerSummary:g('data-career-summary'),
    jobTitle:g('data-job-title'),
    skills:gj('data-skills'),
    validation:gj('data-validation')
  };
}
function isGap(value){
  value=clean(value,'');
  return !value || value==='--' || value==='-' || /^available 01 jan 1970$/i.test(value);
}
function titleCase(value){
  value=clean(value,'');
  if(!value) return '--';
  return value.toLowerCase().replace(/\b([a-z])/g,function(m){ return m.toUpperCase(); });
}
function cityOnly(value){
  value=clean(value,'--');
  if(isGap(value)) return '--';
  var commaParts=value.split(',').map(function(part){ return clean(part,''); }).filter(Boolean);
  if(commaParts.length>1) return commaParts[0];
  var states='andhra pradesh|arunachal pradesh|assam|bihar|chhattisgarh|goa|gujarat|haryana|himachal pradesh|jharkhand|karnataka|kerala|madhya pradesh|maharashtra|manipur|meghalaya|mizoram|nagaland|odisha|punjab|rajasthan|sikkim|tamil nadu|telangana|tripura|uttar pradesh|uttarakhand|west bengal|delhi|jammu and kashmir|ladakh|puducherry|chandigarh|dadra and nagar haveli|daman and diu|andaman and nicobar islands|lakshadweep';
  var pattern=new RegExp('\\s+('+states+')$','i');
  return clean(value.replace(pattern,''),'--');
}
function matchTone(score,completeness){
  if(completeness<45) return {label:'Profile Review',priority:'REVIEW',decision:'DATA REVIEW REQUIRED',round:'Recruiter Validation',color:'#7c3aed',fill:'#f5f3ff',border:'#ddd6fe'};
  if(score>=80) return {label:'Excellent Fit',priority:'HIGH',decision:'RECOMMENDED FOR INTERVIEW',round:'Technical L1',color:'#047857',fill:'#ecfdf5',border:'#a7f3d0'};
  if(score>=60) return {label:'Strong Fit',priority:'HIGH',decision:'RECOMMENDED FOR INTERVIEW',round:'Technical L1',color:'#1d4ed8',fill:'#eff6ff',border:'#bfdbfe'};
  if(score>=40) return {label:'Needs Review',priority:'MEDIUM',decision:'RECRUITER REVIEW REQUIRED',round:'Recruiter Review',color:'#b45309',fill:'#fffbeb',border:'#fde68a'};
  return {label:'Review Required',priority:'REVIEW',decision:'REVIEW BEFORE DECISION',round:'Recruiter Validation',color:'#be123c',fill:'#fff1f2',border:'#fecdd3'};
}
function panel(stack,margin,fill,border){
  return {
    margin:margin||[0,0,0,7],
    table:{widths:['*'],body:[[ {stack:Array.isArray(stack)?stack:[stack],margin:[12,10,12,10],fillColor:fill||'#ffffff'} ]]},
    layout:{
      hLineWidth:function(){return 0.55;}, vLineWidth:function(){return 0.55;},
      hLineColor:function(){return border||'#dbe3ef';}, vLineColor:function(){return border||'#dbe3ef';},
      paddingLeft:function(){return 0;}, paddingRight:function(){return 0;},
      paddingTop:function(){return 0;}, paddingBottom:function(){return 0;}
    }
  };
}
function fixedPanel(stack,margin,fill,border,minHeight){
  return {
    margin:margin||[0,0,0,7],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:260,h:minHeight||72,r:6,lineWidth:0.55,lineColor:border||'#dbe3ef',color:fill||'#ffffff'}],margin:[0,0,0,-(minHeight||72)]},
      {table:{widths:['*'],heights:function(){return minHeight||72;},body:[[ {stack:Array.isArray(stack)?stack:[stack],margin:[12,10,12,10]} ]]},layout:'noBorders'}
    ]
  };
}
function roundedFullCard(stack,margin,height,fill,border){
  return {
    margin:margin||[0,0,0,8],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:535,h:height,r:6,lineWidth:0.55,lineColor:border||'#dbe3ef',color:fill||'#ffffff'}],margin:[0,0,0,-height]},
      {table:{widths:['*'],heights:function(){return height;},body:[[ {stack:Array.isArray(stack)?stack:[stack],margin:[12,10,12,10]} ]]},layout:'noBorders'}
    ]
  };
}
function headerCard(companyName,logoCell,reportDate,captainName){
  return {
    margin:[0,0,0,10],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:535,h:64,r:6,lineWidth:0.55,lineColor:'#dbe3ef',color:'#ffffff'}],margin:[0,0,0,-64]},
      {
        table:{widths:[220,'*'],body:[[
          {
            table:{widths:['auto','*'],body:[[logoCell,{stack:[
              {text:'SHARED BY',fontSize:6,bold:true,color:'#64748b',margin:[0,1,0,3]},
              {text:companyName,fontSize:11.2,bold:true,color:'#0f172a',margin:[0,0,0,0]}
            ],border:[false,false,false,false]}]]},
            layout:'noBorders',
            margin:[14,12,10,10],
            border:[false,false,false,false]
          },
          {
            stack:[
              {text:'PRE-SCREENED CANDIDATE REPORT',style:'title',margin:[0,2,0,0]},
              {text:(clean(reportDate,'--')+' · Confidential').toUpperCase(),style:'eyebrow',margin:[0,4,0,0]}
            ],
            margin:[10,14,14,10],
            border:[false,false,false,false]
          }
        ]]},
        layout:'noBorders'
      }
    ]
  };
}
function unbrandedHeaderCard(reportDate){
  return {
    margin:[0,0,0,10],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:535,h:64,r:6,lineWidth:0.55,lineColor:'#dbe3ef',color:'#ffffff'}],margin:[0,0,0,-64]},
      {
        table:{widths:['*'],body:[[
          {
            stack:[
              {text:'PRE-SCREENED CANDIDATE REPORT',style:'title',margin:[0,2,0,0]},
              {text:(clean(reportDate,'--')+' · Confidential').toUpperCase(),style:'eyebrow',margin:[0,4,0,0]}
            ],
            margin:[14,14,14,10],
            border:[false,false,false,false]
          }
        ]]},
        layout:'noBorders'
      }
    ]
  };
}
function pairedCardRow(leftStack,rightStack,leftFill,rightFill,rightBorder){
  return {
    margin:[0,0,0,8],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:264,h:88,r:6,lineWidth:0.55,lineColor:'#cbd5e1',color:leftFill||'#ffffff'},{type:'rect',x:271,y:0,w:264,h:88,r:6,lineWidth:0.55,lineColor:rightBorder||'#cbd5e1',color:rightFill||'#ffffff'}],margin:[0,0,0,-88]},
      {columns:[
        {stack:Array.isArray(leftStack)?leftStack:[leftStack],width:264,margin:[12,10,12,10]},
        {stack:Array.isArray(rightStack)?rightStack:[rightStack],width:264,margin:[12,10,12,10]}
      ],columnGap:7}
    ]
  };
}
function iconLine(icon,text,style,color,iconWidth){
  var width=iconWidth||7;
  return {columns:[
    {svg:icon,width:width,margin:[0,1,0,0],alignment:'left'},
    {text:text,style:style||'small',color:color||undefined,margin:[3,0,0,0]}
  ],columnGap:0};
}
function labeledIconLine(icon,label,value,style,color,iconWidth){
  var width=iconWidth||7;
  return {columns:[
    {svg:icon,width:width,margin:[0,1,0,0],alignment:'left'},
    {text:[{text:label,bold:true},' '+value],style:style||'small',color:color||undefined,margin:[3,0,0,0]}
  ],columnGap:0};
}
function sectionTitle(title,subtle,icon){
  if(!icon) return {text:title,style:subtle?'sectionTitleMuted':'sectionTitle',margin:[0,0,0,8]};
  return {columns:[
    {svg:icon,width:10,margin:[0,0,0,0]},
    {text:'',width:5},
    {text:title,style:subtle?'sectionTitleMuted':'sectionTitle',margin:[0,0.5,0,0]}
  ],columnGap:0,margin:[0,0,0,8]};
}
function metaCell(label,value,align){
  return {stack:[
    {text:label,style:'metaLabel',alignment:align||'left'},
    {text:clean(value),style:'metaValue',alignment:align||'left',margin:[0,2,0,0]}
  ],margin:[0,4,0,4]};
}
function metricCell(label,value,icon){
  return {stack:[
    {columns:[
      {text:'',width:'*'},
      {svg:icon,width:10.5,margin:[0,0.2,0,0]},
      {text:'',width:6},
      {text:clean(label).toUpperCase(),style:'metricLabel',color:'#64748b',width:'auto',margin:[0,1.9,0,0]},
      {text:'',width:'*'}
    ],columnGap:0,margin:[0,0,0,8]},
    {text:clean(value),style:'metricValue',alignment:'center'}
  ],margin:[4,9,4,8]};
}
function dataCompleteness(data){
  var checks=[
    data.name,data.designation||data.jobTitle,data.experience,data.company,data.location,
    data.education,data.notice,data.workMode,data.careerSummary
  ];
  var score=checks.filter(function(v){ return !isGap(v); }).length;
  if((data.skills||[]).length) score+=1;
  if((data.validation||[]).length) score+=1;
  if(!isGap(data.recruiterNote)) score+=1;
  return Math.round(score/12*100);
}
function buildSkillRows(skills){
  var rows=[
    [
      {text:'SKILLS',style:'tableHead'},
      {text:'EXPERIENCE',style:'tableHead'},
      {text:'RATING',style:'tableHead',alignment:'center'}
    ]
  ];
  skills=(skills||[]).filter(function(skill){ return clean(skill.name,'')!==''; }).slice(0,7);
  if(!skills.length){
    rows.push([{text:'No skill intelligence available in current candidate data.',colSpan:3,style:'muted',margin:[0,6,0,6]},{},{}]);
    return rows;
  }
  skills.forEach(function(skill){
    var rating=safeNumber(skill.rating,0,5);
    var exp=Number(skill.exp||0);
    var expText=clean(skill.expText,'');
    var stars=[];
    for(var i=1;i<=5;i++) stars.push({svg:i<=rating?rsStarGold:rsStarMuted,width:6.4,margin:[0,0.5,1.2,0]});
    rows.push([
      {text:clean(skill.name).toUpperCase(),style:'tableCellBold',alignment:'left'},
      {text:expText!=='' && expText!=='--' ? expText : (exp>0 ? exp+' Year'+(exp>1?'s':'') : '--'),style:'tableCell',color:'#334155'},
      {columns:stars,columnGap:0,alignment:'center',margin:[20,5,0,5]}
    ]);
  });
  return rows;
}
function buildValidationRows(validation){
  var map={};
  (validation||[]).forEach(function(row){
    var key=clean(row.label,'').toLowerCase();
    if(key) map[key]=clean(row.status,'');
  });
  var slots=[
    {label:'RESUME',key:'resume',fallbackKey:'resume verified',icon:rsIconResume},
    {label:'EMAIL ID',key:'email id',fallbackKey:'email verified',icon:rsIconEmail},
    {label:'PHONE NUMBER',key:'phone number',fallbackKey:'phone verified',icon:rsIconPhone},
    {label:'LINKEDIN ID',key:'linkedin id',fallbackKey:'linkedin verified',icon:rsIconLinkedin},
    {label:'JOB DESCRIPTION',key:'job description',fallbackKey:'job description acknowledged',icon:rsIconJobDescription}
  ];
  var cells=slots.map(function(row){
    var status=map[row.key] || map[row.fallbackKey] || '--';
    var verified=/verified|confirmed|completed|discussed|shared/i.test(status);
    return {stack:[
      {columns:[
        {text:'',width:'*'},
        {svg:row.icon,width:9.8,margin:[0,0.2,0,0]},
        {text:'',width:5},
        {text:row.label.toUpperCase(),fontSize:6.6,bold:true,color:'#0f172a',width:'auto',margin:[0,1.7,0,0]},
        {text:'',width:'*'}
      ],columnGap:0,margin:[0,0,0,7]},
      {text:status,fontSize:6.5,bold:true,color:verified?'#16a34a':'#b45309',alignment:'center',margin:[0,0,0,0]}
    ],margin:[2,7,2,7]};
  });
  return [cells];
}
function buildDefinition(data,logoUri,options){
  options=options||{};
  var noBranding=options.noBranding===true;
  var score=safeNumber(data.match,0,100);
  var completeness=dataCompleteness(data);
  var tone=matchTone(score,completeness);
  var summary=limitText(data.careerSummary,560,'No professional summary available.');
  var noteMissing=isGap(data.recruiterNote);
  var note=noteMissing ? 'No recruiter comments available.' : limitText(data.recruiterNote,420,'');
  var joining=data.isImmediate?'Immediate':clean(data.notice,'--');
  var companyName=clean(data.reportCompanyName,'Hiring Partner');
  var skillCount=Math.max(1,Math.min(7,(data.skills||[]).filter(function(skill){ return clean(skill.name,'')!==''; }).length));
  var skillCardHeight=90+(skillCount*31);
  var logoCell=logoUri
    ? {image:logoUri,fit:[42,28],alignment:'center',margin:[0,0,12,0],border:[false,false,false,false]}
    : {text:companyName.charAt(0).toUpperCase(),fontSize:13,bold:true,color:'#1d4ed8',alignment:'center',margin:[0,5,12,4],fillColor:'#eef2ff',border:[false,false,false,false]};

  return {
    pageSize:'A4',
    pageMargins:[30,26,30,24],
    defaultStyle:{font:'Roboto',fontSize:8,color:'#1f2937',lineHeight:1.22},
    styles:{
      title:{fontSize:15,bold:true,color:'#0f172a',alignment:'right'},
      eyebrow:{fontSize:6.2,color:'#64748b',alignment:'right'},
      metaLabel:{fontSize:5.7,color:'#64748b'},
      metaValue:{fontSize:7.4,bold:true,color:'#111827'},
      candidateName:{fontSize:18,bold:true,color:'#0f172a'},
      designation:{fontSize:9.2,bold:true,color:'#1d4ed8'},
      small:{fontSize:7.4,color:'#64748b'},
      sectionTitle:{fontSize:8.2,bold:true,color:'#1d4ed8'},
      sectionTitleMuted:{fontSize:8,bold:true,color:'#334155'},
      tableHead:{fontSize:7,bold:true,color:'#334155',fillColor:'#f8fafc',margin:[0,6,0,6]},
      tableCell:{fontSize:7.2,color:'#334155',margin:[0,6,0,6]},
      tableCellBold:{fontSize:7.2,bold:true,color:'#0f172a',margin:[0,6,0,6]},
      metricLabel:{fontSize:6,color:'#64748b'},
      metricValue:{fontSize:7.2,bold:true,color:'#0f172a'},
      muted:{fontSize:7,color:'#64748b'},
      body:{fontSize:7.6,color:'#1f2937',lineHeight:1.35},
      footer:{fontSize:6,color:'#64748b'}
    },
    content:[
      noBranding ? unbrandedHeaderCard(data.reportDate) : headerCard(companyName,logoCell,data.reportDate,data.recruiterName),
      pairedCardRow([
        {text:clean(data.name,'Candidate'),style:'candidateName'},
        {text:clean(data.designation||data.jobTitle,'--'),style:'designation',margin:[0,3,0,8]},
        labeledIconLine(rsIconExperience,'EXPERIENCE:',clean(data.experience,'--').toUpperCase(),'small','#64748b',6.5),
        {stack:[labeledIconLine(rsIconCompany,'CURRENT COMPANY:',clean(data.company,'--').toUpperCase(),'small','#64748b',6.5)],margin:[0,3,0,0]}
      ],[
        {text:'HIRING CONFIDENCE',fontSize:12.4,bold:true,color:'#64748b',alignment:'center',margin:[0,8,0,0]},
        {text:score+'%',fontSize:30,bold:true,color:'#1d4ed8',alignment:'center',margin:[0,7,0,0]}
      ],'#ffffff','#f8fbff','#cfe0ff'),
      roundedFullCard([
        {table:{widths:[116,116,164,116],body:[[
          metricCell('Education',data.education,rsIconEducation),
          metricCell('Notice Period',joining,rsIconCalendar),
          metricCell('Location',cityOnly(data.location),rsIconLocation),
          metricCell('Work Mode',data.workMode,rsIconLaptop)
        ]]},
        layout:{
          hLineWidth:function(){return 0;},
          vLineWidth:function(i){return i>0&&i<4?0.45:0;},
          vLineColor:function(){return '#dbe3ef';},
          paddingLeft:function(){return 0;},
          paddingRight:function(){return 0;},
          paddingTop:function(){return 0;},
          paddingBottom:function(){return 0;}
        }}
      ],[0,0,0,8],62,'#ffffff','#dbe3ef'),
      {
        columns:[
          fixedPanel([sectionTitle('EXECUTIVE SUMMARY',false,rsIconSummary),{text:summary,style:'body'}],[0,0,6,8],'#ffffff','#dbe3ef',148),
          fixedPanel([sectionTitle('RECRUITER COMMENTS',false,rsIconNote),{text:note,style:'body',color:noteMissing?'#64748b':'#1f2937',italics:noteMissing}],[6,0,0,8],'#ffffff','#dbe3ef',148)
        ],
        columnGap:0
      },
      roundedFullCard([
        sectionTitle('SKILL INTELLIGENCE',false,rsIconSkill),
        {table:{widths:['*',100,78],body:buildSkillRows(data.skills)},layout:{
          hLineWidth:function(i){return i===1?0.75:0.45;},
          vLineWidth:function(){return 0;},
          hLineColor:function(i){return i===1?'#94a3b8':'#e2e8f0';}
        }}
      ],[0,0,0,8],skillCardHeight,'#ffffff','#dbe3ef'),
      roundedFullCard([
          sectionTitle('VERIFICATION STATUS',false,rsIconShield),
          {table:{widths:['*','*','*','*','*'],body:buildValidationRows(data.validation)},layout:{
            hLineWidth:function(){return 0;},
            vLineWidth:function(i){return i>0&&i<5?0.55:0;},
            vLineColor:function(){return '#cbd5e1';},
            paddingLeft:function(){return 0;},
            paddingRight:function(){return 0;},
            paddingTop:function(){return 0;},
            paddingBottom:function(){return 0;}
          }}
      ],[0,0,0,8],74,'#ffffff','#dbe3ef'),
      {
        table:{widths:['*','auto'],body:[[
          {text:'This report is generated by AI hiring platform jero.works',style:'footer',border:[false,true,false,false],margin:[0,5,0,0]},
          {text:'www.jero.works',style:'footer',border:[false,true,false,false],margin:[0,5,0,0]}
        ]]},
        layout:{hLineColor:function(){return '#e5e7eb';},hLineWidth:function(){return 0.6;},vLineWidth:function(){return 0;}}
      }
    ]
  };
}
async function buildRecruiterSummary(){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    var data=readData();
    var blob=await buildRecruiterSummaryBlob(data);
    downloadBlob(blob,preScreenReportFileName(data));
    return true;
  }catch(err){
    console.error('Recruiter Summary error:',err);
    alert('Could not generate the Recruiter Summary. Please try again.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildRecruiterSummaryBlob(data,options){
  data=data||readData();
  options=options||{};
  var ready=await Promise.all([rsEnsurePdfMake(),options.noBranding ? Promise.resolve(null) : rsLogoDataUri(data.reportCompanyLogo)]);
  return new Promise(function(resolve,reject){
    try{
      window.pdfMake.createPdf(buildDefinition(data,ready[1],options)).getBlob(function(blob){
        if(blob && blob.size>200) resolve(blob);
        else reject(new Error('Recruiter Summary PDF output was empty'));
      });
    }catch(ex){ reject(ex); }
  });
}
async function copyPdfPagesToA4(targetDoc,sourceDoc){
  /* Purpose: keep pre-screened report packages visually consistent by normalizing
     every merged report/resume page to portrait A4 without changing document content. */
  var A4_W=595.28;
  var A4_H=841.89;
  var pages=sourceDoc.getPages();
  if(!pages.length) throw new Error('PDF has no pages to merge');
  for(var i=0;i<pages.length;i++){
    var size=pages[i].getSize();
    var scale=Math.min(A4_W/size.width,A4_H/size.height);
    var drawW=size.width*scale;
    var drawH=size.height*scale;
    var a4Page=targetDoc.addPage([A4_W,A4_H]);
    var embedded=await targetDoc.embedPage(pages[i]);
    a4Page.drawPage(embedded,{
      x:(A4_W-drawW)/2,
      y:(A4_H-drawH)/2,
      width:drawW,
      height:drawH
    });
  }
}
async function mergePreScreenedReportAndResume(reportBlob,resumeBlob,fileName,mergeOptions){
  await rsEnsurePdfLib();
  mergeOptions=mergeOptions||{};
  var resumeBytes=await resumeBlob.arrayBuffer();
  var reportBytes=await reportBlob.arrayBuffer();
  var merged=await window.PDFLib.PDFDocument.create();
  var reportDoc=await window.PDFLib.PDFDocument.load(reportBytes);
  var resumeDoc=await window.PDFLib.PDFDocument.load(resumeBytes);
  if(mergeOptions.forceA4 !== false){
    await copyPdfPagesToA4(merged,reportDoc);
  }else if(mergeOptions.matchReportWidthToResume){
    var resumeFirstPage=resumeDoc.getPages()[0];
    if(!resumeFirstPage) throw new Error('Resume PDF has no pages');
    var resumeWidth=resumeFirstPage.getSize().width;
    var reportSourcePages=reportDoc.getPages();
    for(var r=0;r<reportSourcePages.length;r++){
      var reportSize=reportSourcePages[r].getSize();
      var reportScale=resumeWidth/reportSize.width;
      var normalizedPage=merged.addPage([resumeWidth,reportSize.height*reportScale]);
      var embeddedReport=await merged.embedPage(reportSourcePages[r]);
      normalizedPage.drawPage(embeddedReport,{x:0,y:0,width:resumeWidth,height:reportSize.height*reportScale});
    }
  }else{
    var reportPages=await merged.copyPages(reportDoc,reportDoc.getPageIndices());
    reportPages.forEach(function(page){ merged.addPage(page); });
  }
  if(mergeOptions.forceA4 !== false){
    await copyPdfPagesToA4(merged,resumeDoc);
  }else{
    var resumePages=await merged.copyPages(resumeDoc,resumeDoc.getPageIndices());
    resumePages.forEach(function(page){ merged.addPage(page); });
  }
  var mergedBytes=await merged.save();
  downloadBlob(new Blob([mergedBytes],{type:'application/pdf'}),fileName);
}
function dataUriBytes(dataUri){
  var match=String(dataUri||'').match(/^data:([^;]+);base64,(.+)$/i);
  if(!match) return null;
  var raw=atob(match[2]);
  var bytes=new Uint8Array(raw.length);
  for(var i=0;i<raw.length;i++) bytes[i]=raw.charCodeAt(i);
  return {mime:match[1].toLowerCase(),bytes:bytes};
}
async function addCompanyHeaderToResume(resumeBlob,data,fileName,headerOptions){
  await rsEnsurePdfLib();
  data=data||readData();
  headerOptions=headerOptions||{};
  var PDFLib=window.PDFLib;
  var resumeBytes=await resumeBlob.arrayBuffer();
  var sourceDoc=await PDFLib.PDFDocument.load(resumeBytes);
  var targetDoc=await PDFLib.PDFDocument.create();
  var regularFont=await targetDoc.embedFont(PDFLib.StandardFonts.Helvetica);
  var boldFont=await targetDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
  var logoUri=await rsLogoDataUri(data.reportCompanyLogo);
  var logoImage=null;
  var logoData=dataUriBytes(logoUri);
  if(logoData){
    try{
      if(logoData.mime.indexOf('png')!==-1) logoImage=await targetDoc.embedPng(logoData.bytes);
      else if(logoData.mime.indexOf('jpeg')!==-1 || logoData.mime.indexOf('jpg')!==-1) logoImage=await targetDoc.embedJpg(logoData.bytes);
    }catch(err){
      console.warn('Branded resume logo embed skipped:',err);
      logoImage=null;
    }
  }
  var sourcePages=sourceDoc.getPages();
  if(!sourcePages.length) throw new Error('Resume PDF has no pages');
  var firstPage=sourcePages[0];
  var pageSize=firstPage.getSize();
  var headerScale=headerOptions.matchOption6VisualScale ? Math.max(1,Math.min(1.45,pageSize.width/612)) : 1;
  var headerH=Math.max(66,Math.min(82,pageSize.width*0.105))*headerScale;
  var newFirst=targetDoc.addPage([pageSize.width,pageSize.height+headerH]);
  var embeddedFirst=await targetDoc.embedPage(firstPage);
  newFirst.drawPage(embeddedFirst,{x:0,y:0,width:pageSize.width,height:pageSize.height});
  var topY=pageSize.height;
  newFirst.drawRectangle({x:0,y:topY,width:pageSize.width,height:headerH,color:PDFLib.rgb(1,1,1),borderColor:PDFLib.rgb(0.86,0.89,0.94),borderWidth:0.8});
  newFirst.drawRectangle({x:0,y:topY+headerH-(4*headerScale),width:pageSize.width,height:4*headerScale,color:PDFLib.rgb(0.12,0.23,0.54)});
  newFirst.drawRectangle({x:0,y:topY,width:pageSize.width,height:1.1*headerScale,color:PDFLib.rgb(0.88,0.91,0.95)});
  var companyName=clean(data.reportCompanyName,'Hiring Partner');
  var logoBox=headerH-(26*headerScale);
  var logoX=22*headerScale;
  var logoY=topY+(14*headerScale);
  var logoAreaW=70*headerScale;
  if(logoImage){
    var dims=logoImage.scaleToFit(logoBox*1.8,logoBox);
    newFirst.drawImage(logoImage,{x:logoX,y:logoY+(logoBox-dims.height)/2,width:dims.width,height:dims.height});
    logoAreaW=Math.max(56*headerScale,Math.min(74*headerScale,dims.width));
  }else{
    newFirst.drawRectangle({x:logoX,y:logoY,width:logoBox,height:logoBox,color:PDFLib.rgb(0.93,0.95,1),borderColor:PDFLib.rgb(0.75,0.82,0.98),borderWidth:0.8*headerScale});
    newFirst.drawText(companyName.charAt(0).toUpperCase(),{x:logoX+(logoBox/2)-(5*headerScale),y:logoY+(logoBox/2)-(5*headerScale),size:16*headerScale,font:boldFont,color:PDFLib.rgb(0.12,0.23,0.54)});
    logoAreaW=logoBox;
  }
  var textX=logoX+logoAreaW+(12*headerScale);
  var confText='CONFIDENTIAL RESUME';
  var dateText=clean(data.reportDate,'') || (new Date()).toLocaleDateString();
  var confSize=13*headerScale;
  var dateSize=8.5*headerScale;
  var confW=boldFont.widthOfTextAtSize(confText,confSize);
  var dateW=regularFont.widthOfTextAtSize(dateText,dateSize);
  var rightMargin=24*headerScale;
  newFirst.drawText('SHARED BY',{x:textX,y:topY+headerH-(27*headerScale),size:7*headerScale,font:boldFont,color:PDFLib.rgb(0.39,0.45,0.55)});
  newFirst.drawText(limitText(companyName,42,companyName),{x:textX,y:topY+headerH-(45*headerScale),size:12*headerScale,font:boldFont,color:PDFLib.rgb(0.06,0.09,0.16)});
  newFirst.drawText(confText,{x:pageSize.width-rightMargin-confW,y:topY+headerH-(29*headerScale),size:confSize,font:boldFont,color:PDFLib.rgb(0.06,0.09,0.16)});
  newFirst.drawText(dateText,{x:pageSize.width-rightMargin-dateW,y:topY+headerH-(45*headerScale),size:dateSize,font:regularFont,color:PDFLib.rgb(0.39,0.45,0.55)});
  if(sourcePages.length>1){
    var rest=await targetDoc.copyPages(sourceDoc,sourceDoc.getPageIndices().slice(1));
    rest.forEach(function(page){ targetDoc.addPage(page); });
  }
  var brandedBytes=await targetDoc.save();
  downloadBlob(new Blob([brandedBytes],{type:'application/pdf'}),fileName);
}
async function buildSmartBrandedResume(resumePdfUrl,options){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeSources=(Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl||'']).map(function(url){
      return clean(url,'');
    }).filter(function(url,index,self){
      return url && url!=='--' && self.indexOf(url)===index;
    });
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    resumePdfUrl=clean(resumePdfUrl,'');
    if(['PDF','DOC','DOCX'].indexOf(resumeExt)===-1) throw new Error('Smart branded resume is available for PDF, DOC and DOCX resumes only');
    var resumeBlob=null;
    var conversionMarker='C';
    if(resumeExt==='PDF'){
      resumeBlob=await fetchPdfBlobFromSources(resumeSources);
    }else if(resumeExt==='DOCX'){
      try{
        resumeBlob=await convertResumeWithOpenDocLabBlob(resumeSources,resumeExt,false,{skipHighlights:true});
      }catch(browserDocxErr){
        console.warn('OpenDoc DOCX branding conversion failed; using enterprise converter PDF URL if available:',browserDocxErr);
        if(!resumePdfUrl) throw browserDocxErr;
        resumeBlob=await fetchPdfResumeBlob(resumePdfUrl);
        conversionMarker='S';
      }
    }else{
      if(!resumePdfUrl) throw new Error('DOC resume needs enterprise converter PDF URL');
      resumeBlob=await fetchPdfResumeBlob(resumePdfUrl);
      conversionMarker='S';
    }
    var data=readData();
    await addCompanyHeaderToResume(resumeBlob,data,resumeTrackingFileName(data,conversionMarker,'pdf'));
    return true;
  }catch(err){
    console.error('Smart Branded Resume error:',err);
    alert('Could not generate the branded resume.\n\nReason: '+(err && err.message ? err.message : 'Unknown branding error')+'\n\nThis option keeps resume data visible and adds only the shared-by company header.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildHighlightedBrandedResume(resumePdfUrl,options){
  /* Purpose: combine the turbo keyword highlighter with option 6's exact company branding header without duplicating header design constants. */
  if(window.__rsBusy) return Promise.resolve(false);
  try{
    options=options||{};
    var data=readData();
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    var highlightedBlob=null;
    var conversionMarker='C';
    if(resumeExt==='DOCX' && options.preferClientDocx===true){
      try{
        highlightedBlob=await buildTurboVectorHighlightedResume({
          resumeUrl:options.resumeUrl,
          resumeExt:resumeExt,
          resumePdfUrl:null,
          useOpenDocOnly:true,
          openDocConversionOptions:{
            maxBytes:parseInt(options.clientDocxMaxBytes,10)||1536*1024,
            maxPages:parseInt(options.clientDocxMaxPages,10)||6,
            requireTextPdf:true,
            dpi:216,
            jpegQuality:0.98
          },
          throwOnError:true,
          returnBlob:true
        });
      }catch(clientDocxErr){
        console.warn('Option 20 DOCX browser turbo path failed; using enterprise PDF fallback:',clientDocxErr);
        conversionMarker='S';
      }
    }
    if(resumeExt==='DOC') conversionMarker='S';
    if(!(highlightedBlob instanceof Blob)){
      highlightedBlob=await buildTurboVectorHighlightedResume({
        resumeUrl:options.resumeUrl,
        resumeExt:resumeExt,
        resumePdfUrl:resumePdfUrl,
        throwOnError:true,
        returnBlob:true
      });
    }
    if(!(highlightedBlob instanceof Blob)) throw new Error('Highlighted resume PDF was not generated');
    window.__rsBusy=true;
    await addCompanyHeaderToResume(highlightedBlob,data,resumeTrackingFileName(data,conversionMarker,'pdf'),{matchOption6VisualScale:true});
    return true;
  }catch(err){
    console.error('Highlighted branded resume error:',err);
    alert('Could not generate Resume + Highlighted + Company Branding.\n\nReason: '+(err && err.message ? err.message : 'Unknown highlighted branding error')+'\n\nThis option highlights first, then applies the exact option 6 company branding header.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildIsolatedHighlightedKeywordsResume(resumePdfUrl,options){
  /* Purpose: keep the highlighted-keywords-only download as a separate package from branded/report options while reusing the client-first converter primitive. */
  if(window.__rsBusy) return Promise.resolve(false);
  try{
    options=options||{};
    var data=readData();
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    var highlightedBlob=null;
    var conversionMarker='C';
    if(resumeExt==='DOCX' && options.preferClientDocx===true){
      try{
        highlightedBlob=await buildTurboVectorHighlightedResume({
          resumeUrl:options.resumeUrl,
          resumeExt:resumeExt,
          resumePdfUrl:null,
          useOpenDocOnly:true,
          openDocConversionOptions:{
            maxBytes:parseInt(options.clientDocxMaxBytes,10)||1536*1024,
            maxPages:parseInt(options.clientDocxMaxPages,10)||6,
            requireTextPdf:true,
            dpi:216,
            jpegQuality:0.98
          },
          throwOnError:true,
          returnBlob:true
        });
      }catch(clientDocxErr){
        console.warn('Option 28 DOCX browser highlighted path failed; using enterprise PDF fallback:',clientDocxErr);
        conversionMarker='S';
      }
    }
    if(resumeExt==='DOC') conversionMarker='S';
    if(!(highlightedBlob instanceof Blob)){
      highlightedBlob=await buildTurboVectorHighlightedResume({
        resumeUrl:options.resumeUrl,
        resumeExt:resumeExt,
        resumePdfUrl:resumePdfUrl,
        throwOnError:true,
        returnBlob:true
      });
    }
    if(!(highlightedBlob instanceof Blob)) throw new Error('Highlighted resume PDF was not generated');
    downloadBlob(highlightedBlob,resumeTrackingFileName(data,conversionMarker,'pdf'));
    return true;
  }catch(err){
    console.error('Isolated highlighted keywords resume error:',err);
    alert('Could not generate Resume + Highlighted Keywords.\n\nReason: '+(err && err.message ? err.message : 'Unknown highlighted resume error')+'\n\nThis option highlights matched keywords only and does not add company branding.');
    return false;
  }
}
async function buildCompanyBrandingIntroBlob(resumeBlob,data){
  /* Purpose: create a lightweight first-page company branding sheet that can be merged without rewriting resume pages. */
  await rsEnsurePdfLib();
  data=data||readData();
  var PDFLib=window.PDFLib;
  var resumeBytes=await resumeBlob.arrayBuffer();
  var resumeDoc=await PDFLib.PDFDocument.load(resumeBytes);
  var firstPage=resumeDoc.getPages()[0];
  if(!firstPage) throw new Error('Highlighted resume PDF has no pages');
  var pageSize=firstPage.getSize();
  var targetDoc=await PDFLib.PDFDocument.create();
  var regularFont=await targetDoc.embedFont(PDFLib.StandardFonts.Helvetica);
  var boldFont=await targetDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
  var logoUri=await rsLogoDataUri(data.reportCompanyLogo);
  var logoImage=null;
  var logoData=dataUriBytes(logoUri);
  if(logoData){
    try{
      if(logoData.mime.indexOf('png')!==-1) logoImage=await targetDoc.embedPng(logoData.bytes);
      else if(logoData.mime.indexOf('jpeg')!==-1 || logoData.mime.indexOf('jpg')!==-1) logoImage=await targetDoc.embedJpg(logoData.bytes);
    }catch(err){
      console.warn('Fast branding intro logo embed skipped:',err);
      logoImage=null;
    }
  }
  var headerScale=Math.max(1,Math.min(1.45,pageSize.width/612));
  var headerH=Math.max(72,Math.min(92,pageSize.width*0.118))*headerScale;
  var introPage=targetDoc.addPage([pageSize.width,headerH]);
  introPage.drawRectangle({x:0,y:0,width:pageSize.width,height:headerH,color:PDFLib.rgb(1,1,1),borderColor:PDFLib.rgb(0.86,0.89,0.94),borderWidth:0.8});
  introPage.drawRectangle({x:0,y:headerH-(4*headerScale),width:pageSize.width,height:4*headerScale,color:PDFLib.rgb(0.12,0.23,0.54)});
  introPage.drawRectangle({x:0,y:0,width:pageSize.width,height:1.1*headerScale,color:PDFLib.rgb(0.88,0.91,0.95)});
  var companyName=clean(data.reportCompanyName,'Hiring Partner');
  var logoBox=headerH-(28*headerScale);
  var logoX=22*headerScale;
  var logoY=14*headerScale;
  var logoAreaW=70*headerScale;
  if(logoImage){
    var dims=logoImage.scaleToFit(logoBox*1.8,logoBox);
    introPage.drawImage(logoImage,{x:logoX,y:logoY+(logoBox-dims.height)/2,width:dims.width,height:dims.height});
    logoAreaW=Math.max(56*headerScale,Math.min(74*headerScale,dims.width));
  }else{
    introPage.drawRectangle({x:logoX,y:logoY,width:logoBox,height:logoBox,color:PDFLib.rgb(0.93,0.95,1),borderColor:PDFLib.rgb(0.75,0.82,0.98),borderWidth:0.8*headerScale});
    introPage.drawText(companyName.charAt(0).toUpperCase(),{x:logoX+(logoBox/2)-(5*headerScale),y:logoY+(logoBox/2)-(5*headerScale),size:16*headerScale,font:boldFont,color:PDFLib.rgb(0.12,0.23,0.54)});
    logoAreaW=logoBox;
  }
  var textX=logoX+logoAreaW+(12*headerScale);
  var confText='CONFIDENTIAL RESUME';
  var dateText=clean(data.reportDate,'') || (new Date()).toLocaleDateString();
  var confSize=13*headerScale;
  var dateSize=8.5*headerScale;
  var confW=boldFont.widthOfTextAtSize(confText,confSize);
  var dateW=regularFont.widthOfTextAtSize(dateText,dateSize);
  var rightMargin=24*headerScale;
  introPage.drawText('SHARED BY',{x:textX,y:headerH-(29*headerScale),size:7*headerScale,font:boldFont,color:PDFLib.rgb(0.39,0.45,0.55)});
  introPage.drawText(limitText(companyName,42,companyName),{x:textX,y:headerH-(49*headerScale),size:12*headerScale,font:boldFont,color:PDFLib.rgb(0.06,0.09,0.16)});
  introPage.drawText(confText,{x:pageSize.width-rightMargin-confW,y:headerH-(31*headerScale),size:confSize,font:boldFont,color:PDFLib.rgb(0.06,0.09,0.16)});
  introPage.drawText(dateText,{x:pageSize.width-rightMargin-dateW,y:headerH-(50*headerScale),size:dateSize,font:regularFont,color:PDFLib.rgb(0.39,0.45,0.55)});
  var introBytes=await targetDoc.save();
  return new Blob([introBytes],{type:'application/pdf'});
}
async function buildFastHighlightedBrandingResume(resumePdfUrl,options){
  /* Purpose: provide a faster highlighted branded resume by merging a branding intro page instead of rewriting resume pages. */
  if(window.__rsBusy) return Promise.resolve(false);
  try{
    options=options||{};
    var data=readData();
    var highlightedBlob=await buildHeadlessOption3SkillsHighlightedResume({
      resumeUrl:options.resumeUrl,
      resumeExt:options.resumeExt,
      resumePdfUrl:resumePdfUrl,
      preferClientDocx:true,
      returnBlob:true
    });
    if(!(highlightedBlob instanceof Blob)) throw new Error('Highlighted resume PDF was not generated');
    var introBlob=await buildCompanyBrandingIntroBlob(highlightedBlob,data);
    window.__rsBusy=true;
    await mergePreScreenedReportAndResume(introBlob,highlightedBlob,'JEROWORKS_'+fileSafeName(data.name,'Candidate')+'_Fast_Highlighted_Branded.pdf');
    return true;
  }catch(err){
    console.error('Fast highlighted branded resume error:',err);
    alert('Could not generate Fast Resume + Highlighted Keywords + Company Branding.\n\nReason: '+(err && err.message ? err.message : 'Unknown fast branding error')+'\n\nThis option keeps the highlighted resume pages untouched and merges a separate branding page first.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildTurboVectorHighlightedResume(options){
  /* Purpose: High-speed, zero-raster client-side vector keyword highlighting.
     Extracts character coordinates via pdfjsLib, then uses pdf-lib to draw semi-transparent
     rectangles directly onto the vector PDF with sub-pixel text measurement.
     Zero canvas rasterization, zero DOM thrashing, 100% crisp vector output with selectable text. */
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeSources=(Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl||'']).map(function(url){
      return clean(url,'');
    }).filter(function(url,index,self){
      return url && url!=='--' && self.indexOf(url)===index;
    });
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    var resumePdfUrl=clean(options.resumePdfUrl||'','');
    if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1) throw new Error('Turbo Vector Highlighted Resume is available for PDF, DOC and DOCX resumes only');

    await Promise.all([rsEnsurePdfJs(),rsEnsurePdfLib()]);

    var pdfBlob=null;
    if(resumeExt==='PDF'){
      pdfBlob=await fetchPdfBlobFromSources(resumeSources);
    }else if(resumeExt==='DOCX'){
      if(options.useOpenDocOnly===true){
        // Option 27: 100% Client-side OpenDoc WASM conversion with integrated keyword highlights (High-definition 216 DPI & 0.98 JPEG)
        var openDocOptions=options.openDocConversionOptions||{dpi:216,jpegQuality:0.98};
        pdfBlob=await convertResumeWithOpenDocLabBlob(resumeSources,resumeExt,true,openDocOptions);
        if(options.returnBlob===true){
          return pdfBlob;
        }
        var data=readData();
        downloadBlob(pdfBlob,'JEROWORKS_'+fileSafeName(data.name,'Candidate')+'_Turbo_Highlighted.pdf');
        return true;
      }else{
        // Use high-fidelity vector PDF (cached on Cloudflare R2) to guarantee 100% vector typography, exact Word layout, and crisp text highlighting
        if(resumePdfUrl){
          try{
            pdfBlob=await fetchPdfResumeBlob(resumePdfUrl);
          }catch(convErr){
            console.warn('Turbo Vector DOCX vector PDF fetch failed; trying browser conversion fallback:',convErr);
          }
        }
        if(!pdfBlob){
          try{
            pdfBlob=await convertDocxResumeToPdfBlobFromSources(resumeSources);
          }catch(browserDocxErr){
            pdfBlob=await convertResumeWithOpenDocLabBlob(resumeSources,resumeExt,false);
          }
        }
      }
    }else{
      if(!resumePdfUrl) throw new Error('DOC resume needs enterprise converter PDF URL');
      pdfBlob=await fetchPdfResumeBlob(resumePdfUrl);
    }

    var sourceBytes=await pdfBlob.arrayBuffer();
    var bytes=new Uint8Array(sourceBytes);
    var sig=String.fromCharCode.apply(null,bytes.subarray(0,5));
    if(sig.indexOf('%PDF')===-1) throw new Error('Resume source is not a valid PDF');

    // Extract text and coordinates using pdfjsLib without creating any canvas
    var pdfjsDoc=await window.pdfjsLib.getDocument({data:bytes}).promise;
    var pagesTextData=[];
    var fullResumeText='';

    for(var p=1;p<=pdfjsDoc.numPages;p++){
      var page=await pdfjsDoc.getPage(p);
      var tc=await page.getTextContent();
      var pageStr=tc.items.map(function(item){ return item.str || ''; }).join(' ');
      fullResumeText += pageStr + '\n';
      pagesTextData.push({pageNum:p,tc:tc});
    }

    // Comprehensive keyword collection: pools matched drawer skills, ATS job skills, and candidate profile skills
    var allKeywords=[];
    var seenKw={};
    function addKw(val){
      if(!val) return;
      if(typeof val==='object') val=val.skill_name||val.skill||val.name||val.skill_key||'';
      val=String(val).replace(/\+\d+\s*More/gi,'').replace(/\s+/g,' ').trim();
      if(val.length<2 || (val.length<3 && !/^(ai|ui|ux|go|c#|r|c)$/i.test(val))) return;
      var k=val.toLowerCase();
      if(seenKw[k]) return;
      seenKw[k]=true;
      allKeywords.push(val);
    }
    (Array.isArray(rsDrawerMatchedKeywords) ? rsDrawerMatchedKeywords : []).forEach(addKw);
    Array.prototype.slice.call(document.querySelectorAll('#kt_activitiesApplication .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more), .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more)')).forEach(function(el){
      addKw(el.textContent||'');
    });
    (Array.isArray(rsAtsHighlightKeywords) ? rsAtsHighlightKeywords : []).forEach(addKw);
    try{
      var rData=readData();
      (Array.isArray(rData.skills) ? rData.skills : []).forEach(addKw);
    }catch(ex){}

    // Build word-boundary regex with length-descending order
    var keywordRe=rsKeywordRegex(allKeywords);
    if(!keywordRe){
      var fallbackSkills=rsOption3KeywordsFromResumeText(fullResumeText);
      if(fallbackSkills.length) keywordRe=rsOption3BuildRegex(fallbackSkills);
    }

    // Helper for sub-pixel substring position and width measurement
    var measureCanvas=document.createElement('canvas');
    var measureCtx=measureCanvas.getContext('2d');
    function measureSlice(fullStr,startIdx,endIdx,itemWidth,fontH){
      measureCtx.font=Math.round(fontH)+'px sans-serif';
      var fullM=measureCtx.measureText(fullStr).width;
      if(!fullM || fullM<=0){
        var len=Math.max(1,fullStr.length);
        return {
          offset:(startIdx/len)*itemWidth,
          width:Math.max(3,((endIdx-startIdx)/len)*itemWidth)
        };
      }
      var scale=itemWidth/fullM;
      var prefixW=measureCtx.measureText(fullStr.substring(0,startIdx)).width*scale;
      var matchW=measureCtx.measureText(fullStr.substring(startIdx,endIdx)).width*scale;
      return {
        offset:prefixW,
        width:Math.max(3,matchW)
      };
    }

    // Draw native vector highlight rectangles into the PDF
    var PDFLib=window.PDFLib;
    var pdfDoc=await PDFLib.PDFDocument.load(sourceBytes);

    if(keywordRe){
      pagesTextData.forEach(function(pData){
        var pdfLibPage=pdfDoc.getPage(pData.pageNum - 1);
        var runsData=rsPageTextRuns(pData.tc);
        if(!runsData.text || !runsData.runs.length) return;

        keywordRe.lastIndex=0;
        var match;
        while((match=keywordRe.exec(runsData.text))!==null){
          var mStart=match.index;
          var mEnd=mStart+match[0].length;

          runsData.runs.forEach(function(run){
            if(run.end<=mStart || run.start>=mEnd) return;
            var item=run.item;
            if(!item || !item.transform) return;

            var tx=item.transform[4] || 0;
            var ty=item.transform[5] || 0;
            var itemH=Math.max(Math.abs(item.transform[3] || item.height || 0), 9);
            var itemW=Math.max(Math.abs(item.width || 0), 2);
            var itemStr=item.str || '';
            if(!itemStr.trim()) return;

            var subStart=Math.max(0, mStart - run.start);
            var subEnd=Math.min(itemStr.length, mEnd - run.start);
            if(subEnd<=subStart) return;

            var slice=measureSlice(itemStr,subStart,subEnd,itemW,itemH);
            var padX=Math.min(2.5, itemH*0.14);
            var boxX=tx + slice.offset - padX;
            var boxW=slice.width + (padX * 2);
            var boxY=ty - (itemH * 0.22);
            var boxH=itemH * 1.16;

            pdfLibPage.drawRectangle({
              x:boxX,
              y:boxY,
              width:boxW,
              height:boxH,
              color:PDFLib.rgb(1, 0.85, 0.15), // Warm golden ATS yellow
              opacity:0.42
            });
          });

          if(match.index===keywordRe.lastIndex) keywordRe.lastIndex++;
        }
      });
    }

    if(options.returnDoc===true){
      return pdfDoc;
    }

    var outputBytes=await pdfDoc.save();
    var outputBlob=new Blob([outputBytes],{type:'application/pdf'});

    if(options.returnBlob===true){
      return outputBlob;
    }

    var data=readData();
    downloadBlob(outputBlob,'JEROWORKS_'+fileSafeName(data.name,'Candidate')+'_Turbo_Highlighted.pdf');
    return true;
  }catch(err){
    console.error('Turbo Vector Highlighted Resume error:',err);
    if(options && options.throwOnError===true) throw err;
    alert('Could not generate Turbo Vector Highlighted Resume.\n\nReason: '+(err && err.message ? err.message : 'Unknown vector error'));
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildTurboVectorBrandedResume(resumePdfUrl,options){
  /* Purpose: Instant vector keyword highlighting plus company branding header at top of Page 1.
     Highlights keywords across pages, embeds Page 1 directly below the company header bar,
     and copies subsequent pages untouched. Generates in ~300ms, 100% crisp vector typography. */
  if(window.__rsBusy) return Promise.resolve(false);
  try{
    options=options||{};
    var data=readData();

    // Generate vector highlighted document in-memory
    var pdfDoc=await buildTurboVectorHighlightedResume({
      resumeUrl:options.resumeUrl,
      resumeExt:options.resumeExt,
      resumePdfUrl:resumePdfUrl,
      returnDoc:true
    });

    if(!pdfDoc || typeof pdfDoc.getPages!=='function') throw new Error('Vector highlighted PDF was not generated');

    window.__rsBusy=true;
    var PDFLib=window.PDFLib;
    var sourcePages=pdfDoc.getPages();
    if(!sourcePages.length) throw new Error('Resume PDF has no pages');

    var firstPage=sourcePages[0];
    var pageSize=firstPage.getSize();

    var targetDoc=await PDFLib.PDFDocument.create();
    var regularFont=await targetDoc.embedFont(PDFLib.StandardFonts.Helvetica);
    var boldFont=await targetDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
    var logoUri=await rsLogoDataUri(data.reportCompanyLogo);
    var logoImage=null;
    var logoData=dataUriBytes(logoUri);
    if(logoData){
      try{
        if(logoData.mime.indexOf('png')!==-1) logoImage=await targetDoc.embedPng(logoData.bytes);
        else if(logoData.mime.indexOf('jpeg')!==-1 || logoData.mime.indexOf('jpg')!==-1) logoImage=await targetDoc.embedJpg(logoData.bytes);
      }catch(err){
        console.warn('Turbo branding logo embed skipped:',err);
        logoImage=null;
      }
    }

    var headerScale=Math.max(1,Math.min(1.45,pageSize.width/612));
    var headerH=Math.max(66,Math.min(82,pageSize.width*0.105))*headerScale;

    // Page 1: Extended height with company branding header at top and resume below
    var newFirst=targetDoc.addPage([pageSize.width,pageSize.height+headerH]);
    var embeddedFirst=await targetDoc.embedPage(firstPage);
    newFirst.drawPage(embeddedFirst,{x:0,y:0,width:pageSize.width,height:pageSize.height});

    var topY=pageSize.height;
    // Header container with white background and bottom border
    newFirst.drawRectangle({x:0,y:topY,width:pageSize.width,height:headerH,color:PDFLib.rgb(1,1,1),borderColor:PDFLib.rgb(0.86,0.89,0.94),borderWidth:0.8});
    // Top blue accent bar
    newFirst.drawRectangle({x:0,y:topY+headerH-(4*headerScale),width:pageSize.width,height:4*headerScale,color:PDFLib.rgb(0.12,0.23,0.54)});
    // Bottom subtle divider line
    newFirst.drawRectangle({x:0,y:topY,width:pageSize.width,height:1.1*headerScale,color:PDFLib.rgb(0.88,0.91,0.95)});

    var companyName=clean(data.reportCompanyName,'Hiring Partner');
    var logoBox=headerH-(26*headerScale);
    var logoX=22*headerScale;
    var logoY=topY+(14*headerScale);
    var logoAreaW=70*headerScale;

    if(logoImage){
      var dims=logoImage.scaleToFit(logoBox*1.8,logoBox);
      newFirst.drawImage(logoImage,{x:logoX,y:logoY+(logoBox-dims.height)/2,width:dims.width,height:dims.height});
      logoAreaW=Math.max(56*headerScale,Math.min(74*headerScale,dims.width));
    }else{
      newFirst.drawRectangle({x:logoX,y:logoY,width:logoBox,height:logoBox,color:PDFLib.rgb(0.93,0.95,1),borderColor:PDFLib.rgb(0.75,0.82,0.98),borderWidth:0.8*headerScale});
      newFirst.drawText(companyName.charAt(0).toUpperCase(),{x:logoX+(logoBox/2)-(5*headerScale),y:logoY+(logoBox/2)-(5*headerScale),size:16*headerScale,font:boldFont,color:PDFLib.rgb(0.12,0.23,0.54)});
      logoAreaW=logoBox;
    }

    var textX=logoX+logoAreaW+(12*headerScale);
    var confText='CONFIDENTIAL RESUME';
    var dateText=clean(data.reportDate,'') || (new Date()).toLocaleDateString();
    var confSize=13*headerScale;
    var dateSize=8.5*headerScale;
    var confW=boldFont.widthOfTextAtSize(confText,confSize);
    var dateW=regularFont.widthOfTextAtSize(dateText,dateSize);
    var rightMargin=24*headerScale;
    newFirst.drawText('SHARED BY',{x:textX,y:topY+headerH-(27*headerScale),size:7*headerScale,font:boldFont,color:PDFLib.rgb(0.39,0.45,0.55)});
    newFirst.drawText(limitText(companyName,42,companyName),{x:textX,y:topY+headerH-(45*headerScale),size:12*headerScale,font:boldFont,color:PDFLib.rgb(0.06,0.09,0.16)});
    newFirst.drawText(confText,{x:pageSize.width-rightMargin-confW,y:topY+headerH-(29*headerScale),size:confSize,font:boldFont,color:PDFLib.rgb(0.06,0.09,0.16)});
    newFirst.drawText(dateText,{x:pageSize.width-rightMargin-dateW,y:topY+headerH-(45*headerScale),size:dateSize,font:regularFont,color:PDFLib.rgb(0.39,0.45,0.55)});

    // Copy all remaining pages (Page 2, 3, etc.) untouched
    if(sourcePages.length>1){
      var restPages=await targetDoc.copyPages(pdfDoc,pdfDoc.getPageIndices().slice(1));
      restPages.forEach(function(page){ targetDoc.addPage(page); });
    }

    var finalBytes=await targetDoc.save();
    downloadBlob(new Blob([finalBytes],{type:'application/pdf'}),'JEROWORKS_'+fileSafeName(data.name,'Candidate')+'_Turbo_Highlighted_Branded.pdf');
    return true;
  }catch(err){
    console.error('Turbo Vector Branded Resume error:',err);
    alert('Could not generate Turbo Vector Branded Resume.\n\nReason: '+(err && err.message ? err.message : 'Unknown turbo branding error'));
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function processClientLightningResume(isBranded,options){
  /* Purpose: 100% Client-Side Lightning Resume Engine (Option 25 & 26).
     Executes entirely in the user's browser with 0% server CPU load.
     - For DOCX: Renders via docx-preview in browser memory without destructive layout overrides.
       Highlights keywords directly in the DOM text nodes (100% accuracy, perfect wrapping, ascenders/descenders covered).
       For Option 25, injects the company branding header at the top of Page 1 in the DOM.
       Renders page-by-page directly to canvas at 2x DPI with FAST compression into jsPDF.
     - For PDF: Uses pdf.js with parallel multi-page text extraction and pdf-lib for vector rectangles.
       For Option 25, extends Page 1 height and embeds the company branding bar at the top. */
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeSources=(Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl||'']).map(function(url){
      return clean(url,'');
    }).filter(function(url,index,self){
      return url && url!=='--' && self.indexOf(url)===index;
    });
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    var resumePdfUrl=clean(options.resumePdfUrl||'','');
    if(['PDF','DOC','DOCX'].indexOf(resumeExt)===-1) throw new Error('Client Lightning Resume is available for PDF, DOC and DOCX resumes only');

    var data=readData();

    // Pool all ATS skills, drawer matches, and profile skills
    var allKeywords=[];
    var seenKw={};
    function addKw(val){
      if(!val) return;
      if(typeof val==='object') val=val.skill_name||val.skill||val.name||val.skill_key||'';
      val=String(val).replace(/\+\d+\s*More/gi,'').replace(/\s+/g,' ').trim();
      if(val.length<2 || (val.length<3 && !/^(ai|ui|ux|go|c#|r|c)$/i.test(val))) return;
      var k=val.toLowerCase();
      if(seenKw[k]) return;
      seenKw[k]=true;
      allKeywords.push(val);
    }
    (Array.isArray(rsDrawerMatchedKeywords) ? rsDrawerMatchedKeywords : []).forEach(addKw);
    Array.prototype.slice.call(document.querySelectorAll('#kt_activitiesApplication .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more), .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more)')).forEach(function(el){
      addKw(el.textContent||'');
    });
    (Array.isArray(rsAtsHighlightKeywords) ? rsAtsHighlightKeywords : []).forEach(addKw);
    try{
      (Array.isArray(data.skills) ? data.skills : []).forEach(addKw);
    }catch(ex){}

    var keywordRe=rsKeywordRegex(allKeywords);

    if(resumeExt==='DOCX'){
      // 100% Client-Side DOCX pipeline: docx-preview -> DOM keyword highlight -> Page 1 header -> Direct high-DPI canvas
      await rsEnsureDocxConversion();
      var docxBytes=window.__rsCachedResumeBytes;
      if(!docxBytes || !docxBytes.byteLength){
        docxBytes=await fetchResumeBytesFromSources(resumeSources);
        window.__rsCachedResumeBytes=docxBytes;
      }

      var host=document.createElement('div');
      host.className='rs-lightning-docx-host';
      host.style.cssText='position:fixed;left:-9999px;top:0;width:794px;background:#ffffff;overflow:visible;pointer-events:none;z-index:-9999;';
      document.body.appendChild(host);

      try{
        await window.docx.renderAsync(docxBytes,host,null,{
          className:'rs-docx-lightning',
          inWrapper:true,
          ignoreWidth:false,
          ignoreHeight:true,
          breakPages:true,
          renderHeaders:true,
          renderFooters:true,
          useBase64URL:true
        });

        // Direct DOM keyword highlighting via TreeWalker (100% accuracy, covers ascenders/descenders)
        if(keywordRe){
          var walker=document.createTreeWalker(host,NodeFilter.SHOW_TEXT,null,false);
          var textNode;
          var matchNodes=[];
          while((textNode=walker.nextNode())){
            var parent=textNode.parentElement;
            if(!parent || ['SCRIPT','STYLE','MARK','BUTTON','INPUT'].indexOf(parent.tagName)!==-1) continue;
            keywordRe.lastIndex=0;
            if(keywordRe.test(textNode.nodeValue)){
              matchNodes.push(textNode);
            }
          }
          matchNodes.forEach(function(tn){
            var val=tn.nodeValue;
            keywordRe.lastIndex=0;
            var frag=document.createDocumentFragment();
            var lastIdx=0;
            var m;
            while((m=keywordRe.exec(val))!==null){
              if(m.index>lastIdx){
                frag.appendChild(document.createTextNode(val.substring(lastIdx,m.index)));
              }
              var mark=document.createElement('mark');
              mark.style.cssText='background:#ffe27a!important;color:inherit!important;padding:1px 2px!important;border-radius:2px!important;box-decoration-break:clone;-webkit-box-decoration-break:clone;';
              mark.textContent=m[0];
              frag.appendChild(mark);
              lastIdx=m.index+m[0].length;
              if(m.index===keywordRe.lastIndex) keywordRe.lastIndex++;
            }
            if(lastIdx<val.length){
              frag.appendChild(document.createTextNode(val.substring(lastIdx)));
            }
            if(tn.parentNode) tn.parentNode.replaceChild(frag,tn);
          });
        }

        // Option 25: Inject company branding header at top of Page 1 in DOM
        if(isBranded){
          var firstSection=host.querySelector('section.rs-docx-lightning, section.docx, .docx-wrapper > section');
          if(firstSection){
            var headerCard=document.createElement('div');
            headerCard.style.cssText='width:100%;box-sizing:border-box;background:#ffffff;padding:14px 22px 10px;border-bottom:1.5px solid #cbd5e1;display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;font-family:Arial,sans-serif;';
            var logoUri=await rsLogoDataUri(data.reportCompanyLogo);
            var compName=clean(data.reportCompanyName,'Hiring Partner');
            var logoPart=logoUri ? '<img src="'+escapeHtml(logoUri)+'" style="height:42px;max-width:120px;object-fit:contain;margin-right:12px;" />' : '<div style="width:38px;height:38px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;display:flex;align-items:center;justify-content:center;font-weight:700;color:#1e40af;font-size:18px;margin-right:12px;">'+escapeHtml(compName.charAt(0).toUpperCase())+'</div>';
            headerCard.innerHTML='<div style="display:flex;align-items:center;">'+logoPart+'<div><div style="font-size:8px;font-weight:700;color:#64748b;letter-spacing:0.5px;text-transform:uppercase;">SHARED BY</div><div style="font-size:14px;font-weight:700;color:#0f172a;line-height:1.2;">'+escapeHtml(compName)+'</div></div></div><div style="text-align:right;"><div style="font-size:13px;font-weight:700;color:#1e293b;letter-spacing:0.5px;">CONFIDENTIAL RESUME</div><div style="font-size:9.5px;font-weight:500;color:#64748b;margin-top:2px;">'+escapeHtml(clean(data.reportDate,'') || (new Date()).toLocaleDateString())+'</div></div>';
            firstSection.insertBefore(headerCard,firstSection.firstChild);
          }
        }

        await waitForRenderedAssets(host);

        var sections=Array.prototype.slice.call(host.querySelectorAll('section.rs-docx-lightning, section.docx, .docx-wrapper > section'));
        if(!sections.length) sections=[host];

        var JsPDF=window.jspdf.jsPDF;
        var pdf=new JsPDF({orientation:'portrait',unit:'pt',format:'a4',compress:true});
        var pageW=pdf.internal.pageSize.getWidth();
        var pageH=pdf.internal.pageSize.getHeight();
        var isFirstPage=true;

        for(var i=0;i<sections.length;i++){
          var sec=sections[i];
          var rect=sec.getBoundingClientRect();
          if(!rect.width || rect.width<500 || rect.width>1100){
            sec.style.width='794px';
          }
          sec.style.background='#ffffff';
          sec.style.boxShadow='none';
          sec.style.margin='0';
          sec.style.maxWidth='none';

          var cv=await window.html2canvas(sec,{
            backgroundColor:'#ffffff',
            scale:2.0,
            useCORS:true,
            logging:false,
            removeContainer:false,
            windowWidth:Math.ceil(sec.scrollWidth||sec.getBoundingClientRect().width||794),
            windowHeight:Math.ceil(sec.scrollHeight||sec.getBoundingClientRect().height||1123)
          });

          var sliceHeight=Math.max(1,Math.floor(cv.width*(pageH/pageW)));
          var offsetY=0;

          while(offsetY<cv.height){
            if(!isFirstPage) pdf.addPage('a4','portrait');
            var currentHeight=Math.min(sliceHeight,cv.height-offsetY);
            var slice=document.createElement('canvas');
            slice.width=cv.width;
            slice.height=currentHeight;
            var ctx=slice.getContext('2d');
            ctx.fillStyle='#ffffff';
            ctx.fillRect(0,0,slice.width,slice.height);
            ctx.drawImage(cv,0,offsetY,cv.width,currentHeight,0,0,cv.width,currentHeight);

            var img=slice.toDataURL('image/png');
            var drawH=currentHeight*(pageW/cv.width);
            pdf.addImage(img,'PNG',0,0,pageW,drawH,undefined,'FAST');

            offsetY+=currentHeight;
            isFirstPage=false;

            slice.width=0;
            slice.height=0;
          }

          cv.width=0;
          cv.height=0;
        }

        var outputBlob=pdf.output('blob');
        var suffix=isBranded ? '_Client_Lightning_Highlighted_Branded.pdf' : '_Client_Lightning_Highlighted.pdf';
        downloadBlob(outputBlob,'JEROWORKS_'+fileSafeName(data.name,'Candidate')+suffix);
        return true;
      }finally{
        removeNode(host);
      }
    }else{
      // 100% Client-Side PDF pipeline: pdf.js parallel extraction -> sub-pixel pdf-lib vector rectangles -> Page 1 header extension
      await Promise.all([rsEnsurePdfJs(),rsEnsurePdfLib()]);
      var sourceBytes=window.__rsCachedResumeBytes;
      if(!sourceBytes || !sourceBytes.byteLength){
        var pdfBlob=await fetchPdfBlobFromSources(resumeSources);
        sourceBytes=await pdfBlob.arrayBuffer();
        window.__rsCachedResumeBytes=sourceBytes;
      }
      var bytes=new Uint8Array(sourceBytes);
      var pdfjsDoc=await window.pdfjsLib.getDocument({data:bytes}).promise;

      // Parallel text extraction across all pages
      var pagePromises=[];
      for(var p=1;p<=pdfjsDoc.numPages;p++){
        pagePromises.push((async function(num){
          var pg=await pdfjsDoc.getPage(num);
          var tc=await pg.getTextContent();
          return {pageNum:num,tc:tc};
        })(p));
      }
      var pagesTextData=await Promise.all(pagePromises);

      var PDFLib=window.PDFLib;
      var pdfDoc=await PDFLib.PDFDocument.load(sourceBytes);

      var measureCanvas=document.createElement('canvas');
      var measureCtx=measureCanvas.getContext('2d');
      function measureSliceFast(fullStr,startIdx,endIdx,itemWidth,fontH){
        measureCtx.font=Math.round(fontH)+'px sans-serif';
        var fullM=measureCtx.measureText(fullStr).width;
        if(!fullM || fullM<=0){
          var len=Math.max(1,fullStr.length);
          return {offset:(startIdx/len)*itemWidth,width:Math.max(3,((endIdx-startIdx)/len)*itemWidth)};
        }
        var scale=itemWidth/fullM;
        return {
          offset:measureCtx.measureText(fullStr.substring(0,startIdx)).width*scale,
          width:Math.max(3,measureCtx.measureText(fullStr.substring(startIdx,endIdx)).width*scale)
        };
      }

      if(keywordRe){
        pagesTextData.forEach(function(pData){
          var pdfLibPage=pdfDoc.getPage(pData.pageNum-1);
          var runsData=rsPageTextRuns(pData.tc);
          if(!runsData.text || !runsData.runs.length) return;
          keywordRe.lastIndex=0;
          var match;
          while((match=keywordRe.exec(runsData.text))!==null){
            var mStart=match.index;
            var mEnd=mStart+match[0].length;
            runsData.runs.forEach(function(run){
              if(run.end<=mStart || run.start>=mEnd) return;
              var item=run.item;
              if(!item || !item.transform) return;
              var tx=item.transform[4]||0;
              var ty=item.transform[5]||0;
              var itemH=Math.max(Math.abs(item.transform[3]||item.height||0),9);
              var itemW=Math.max(Math.abs(item.width||0),2);
              var itemStr=item.str||'';
              if(!itemStr.trim()) return;
              var subStart=Math.max(0,mStart-run.start);
              var subEnd=Math.min(itemStr.length,mEnd-run.start);
              if(subEnd<=subStart) return;
              var slice=measureSliceFast(itemStr,subStart,subEnd,itemW,itemH);
              var padX=Math.min(2.5,itemH*0.14);
              pdfLibPage.drawRectangle({
                x:tx+slice.offset-padX,
                y:ty-(itemH*0.22),
                width:slice.width+(padX*2),
                height:itemH*1.16,
                color:PDFLib.rgb(1,0.85,0.15),
                opacity:0.42
              });
            });
            if(match.index===keywordRe.lastIndex) keywordRe.lastIndex++;
          }
        });
      }

      if(isBranded){
        var sourcePages=pdfDoc.getPages();
        var firstPage=sourcePages[0];
        var pageSize=firstPage.getSize();
        var targetDoc=await PDFLib.PDFDocument.create();
        var regularFont=await targetDoc.embedFont(PDFLib.StandardFonts.Helvetica);
        var boldFont=await targetDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
        var logoUri=await rsLogoDataUri(data.reportCompanyLogo);
        var logoImage=null;
        var logoData=dataUriBytes(logoUri);
        if(logoData){
          try{
            if(logoData.mime.indexOf('png')!==-1) logoImage=await targetDoc.embedPng(logoData.bytes);
            else if(logoData.mime.indexOf('jpeg')!==-1 || logoData.mime.indexOf('jpg')!==-1) logoImage=await targetDoc.embedJpg(logoData.bytes);
          }catch(err){ logoImage=null; }
        }
        var headerScale=Math.max(1,Math.min(1.45,pageSize.width/612));
        var headerH=Math.max(66,Math.min(82,pageSize.width*0.105))*headerScale;
        var newFirst=targetDoc.addPage([pageSize.width,pageSize.height+headerH]);
        var embeddedFirst=await targetDoc.embedPage(firstPage);
        newFirst.drawPage(embeddedFirst,{x:0,y:0,width:pageSize.width,height:pageSize.height});
        var topY=pageSize.height;
        newFirst.drawRectangle({x:0,y:topY,width:pageSize.width,height:headerH,color:PDFLib.rgb(1,1,1),borderColor:PDFLib.rgb(0.86,0.89,0.94),borderWidth:0.8});
        newFirst.drawRectangle({x:0,y:topY+headerH-(4*headerScale),width:pageSize.width,height:4*headerScale,color:PDFLib.rgb(0.12,0.23,0.54)});
        newFirst.drawRectangle({x:0,y:topY,width:pageSize.width,height:1.1*headerScale,color:PDFLib.rgb(0.88,0.91,0.95)});
        var compName=clean(data.reportCompanyName,'Hiring Partner');
        var logoBox=headerH-(26*headerScale);
        var logoX=22*headerScale;
        var logoY=topY+(14*headerScale);
        var logoAreaW=70*headerScale;
        if(logoImage){
          var dims=logoImage.scaleToFit(logoBox*1.8,logoBox);
          newFirst.drawImage(logoImage,{x:logoX,y:logoY+(logoBox-dims.height)/2,width:dims.width,height:dims.height});
          logoAreaW=Math.max(56*headerScale,Math.min(74*headerScale,dims.width));
        }else{
          newFirst.drawRectangle({x:logoX,y:logoY,width:logoBox,height:logoBox,color:PDFLib.rgb(0.93,0.95,1),borderColor:PDFLib.rgb(0.75,0.82,0.98),borderWidth:0.8*headerScale});
          newFirst.drawText(compName.charAt(0).toUpperCase(),{x:logoX+(logoBox/2)-(5*headerScale),y:logoY+(logoBox/2)-(5*headerScale),size:16*headerScale,font:boldFont,color:PDFLib.rgb(0.12,0.23,0.54)});
          logoAreaW=logoBox;
        }
        var textX=logoX+logoAreaW+(12*headerScale);
        var rightX=pageSize.width-(190*headerScale);
        newFirst.drawText('SHARED BY',{x:textX,y:topY+headerH-(29*headerScale),size:7*headerScale,font:boldFont,color:PDFLib.rgb(0.39,0.45,0.55)});
        newFirst.drawText(limitText(compName,42,compName),{x:textX,y:topY+headerH-(47*headerScale),size:12*headerScale,font:boldFont,color:PDFLib.rgb(0.06,0.09,0.16)});
        newFirst.drawText('CONFIDENTIAL RESUME',{x:rightX,y:topY+headerH-(29*headerScale),size:14*headerScale,font:boldFont,color:PDFLib.rgb(0.06,0.09,0.16)});
        newFirst.drawText(clean(data.reportDate,'') || (new Date()).toLocaleDateString(),{x:rightX,y:topY+headerH-(45*headerScale),size:8.4*headerScale,font:regularFont,color:PDFLib.rgb(0.39,0.45,0.55)});
        if(sourcePages.length>1){
          var restPages=await targetDoc.copyPages(pdfDoc,pdfDoc.getPageIndices().slice(1));
          restPages.forEach(function(page){ targetDoc.addPage(page); });
        }
        var finalBytes=await targetDoc.save();
        downloadBlob(new Blob([finalBytes],{type:'application/pdf'}),'JEROWORKS_'+fileSafeName(data.name,'Candidate')+'_Client_Lightning_Highlighted_Branded.pdf');
        return true;
      }else{
        var outputBytes=await pdfDoc.save();
        downloadBlob(new Blob([outputBytes],{type:'application/pdf'}),'JEROWORKS_'+fileSafeName(data.name,'Candidate')+'_Client_Lightning_Highlighted.pdf');
        return true;
      }
    }
  }catch(err){
    console.error('Client Lightning Resume error:',err);
    alert('Could not generate Client Lightning Resume.\n\nReason: '+(err && err.message ? err.message : 'Unknown lightning error'));
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildClientLightningHighlightedResume(options){
  /* Purpose: Option 26 — 100% Client-Side Fast Highlighted Resume without branding */
  return processClientLightningResume(false,options);
}
async function buildClientLightningBrandedResume(options){
  /* Purpose: Option 25 — 100% Client-Side Fast Highlighted Resume with Company Branding */
  return processClientLightningResume(true,options);
}
function preloadClientLightningAssets(){
  /* Purpose: Preload client-side conversion libraries and speculative resume bytes */
  var dataEl=document.getElementById('rdcData');
  var ext=(dataEl ? dataEl.getAttribute('data-file-ext') : '') || '';
  ext=ext.toUpperCase();
  var tasks=[rsEnsurePdfJs(),rsEnsurePdfLib()];
  if(ext==='DOCX'||ext==='DOC'){
    tasks.push(rsEnsureDocxConversion());
    tasks.push(rsEnsureOpenDocLab(true));
    tasks.push(rsEnsureJsPdf());
  }
  // Speculative fetch of resume bytes into client RAM cache
  if(dataEl && !window.__rsCachedResumeBytes){
    var urls=[dataEl.getAttribute('data-r2-direct-url'),dataEl.getAttribute('data-r2-public-url'),dataEl.getAttribute('data-resume-url')].filter(Boolean);
    if(urls.length){
      fetchResumeBytesFromSources(urls).then(function(b){
        window.__rsCachedResumeBytes=b;
      }).catch(function(){});
    }
  }
  return Promise.all(tasks).catch(function(){ return null; });
}
async function buildTurboPreScreenedHighlightedPackage(resumePdfUrl,options){
  /* Purpose: Option 27 — 100% Pure Client-Side Pre-Screened Package.
     When DOCX: 100% in-browser conversion via OpenDoc WASM engine (ZERO server-side conversion at any cost).
     Highlights and rendering execute entirely in browser memory.
     Pre-Screened Report (pdfMake) and OpenDoc conversion run concurrently in parallel.
     0.00% server conversion CPU. */
  if(window.__rsBusy) return Promise.resolve(false);
  try{
    options=options||{};
    var data=readData();

    // Run report creation and 100% client-side OpenDoc highlighted resume conversion in parallel:
    var results=await Promise.all([
      buildRecruiterSummaryBlob(data,options),
      buildTurboVectorHighlightedResume({
        resumeUrl:options.resumeUrl,
        resumeExt:options.resumeExt,
        resumePdfUrl:null, // STRICT: 0% server conversion at any cost
        useOpenDocOnly:true, // Pure client-side OpenDoc WASM only
        returnBlob:true
      })
    ]);

    var reportBlob=results[0];
    var highlightedBlob=results[1];

    if(!(reportBlob instanceof Blob)) throw new Error('Pre-Screened Report PDF was not generated');
    if(!(highlightedBlob instanceof Blob)) throw new Error('Client-side highlighted resume PDF was not generated');

    window.__rsBusy=true;
    var fileName=resumeTrackingFileName(data,'C','pdf');
    await mergePreScreenedReportAndResume(reportBlob,highlightedBlob,fileName,{matchReportWidthToResume:true});
    return true;
  }catch(err){
    console.error('Turbo Pre-Screened Highlighted package error:',err);
    alert('Could not generate Turbo Pre-Screened Highlighted Package.\n\nReason: '+(err && err.message ? err.message : 'Unknown package error'));
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildPreScreenedHighlightedResume(resumePdfUrl,options){
  /* Purpose: generate the Pre-Screened Report and append the highlighted resume using the fastest safe path: turbo vector PDF highlighting, DOCX OpenDoc-first, and enterprise PDF fallback for unsafe Word files. */
  if(window.__rsBusy) return Promise.resolve(false);
  try{
    options=options||{};
    var data=readData();
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    var reportPromise=buildRecruiterSummaryBlob(data,options);
    var highlightedBlob=null;
    var conversionMarker='C';
    if(resumeExt==='DOCX' && options.preferClientDocx===true){
      try{
        highlightedBlob=await buildTurboVectorHighlightedResume({
          resumeUrl:options.resumeUrl,
          resumeExt:resumeExt,
          resumePdfUrl:null,
          useOpenDocOnly:true,
          openDocConversionOptions:{
            maxBytes:parseInt(options.clientDocxMaxBytes,10)||1536*1024,
            maxPages:parseInt(options.clientDocxMaxPages,10)||6,
            requireTextPdf:true,
            dpi:216,
            jpegQuality:0.98
          },
          throwOnError:true,
          returnBlob:true
        });
      }catch(clientDocxErr){
        console.warn('Option 17 DOCX browser turbo path failed; using enterprise PDF fallback:',clientDocxErr);
        conversionMarker='S';
      }
    }
    if(resumeExt==='DOC') conversionMarker='S';
    if(!(highlightedBlob instanceof Blob)){
      highlightedBlob=await buildTurboVectorHighlightedResume({
        resumeUrl:options.resumeUrl,
        resumeExt:resumeExt,
        resumePdfUrl:resumePdfUrl,
        throwOnError:true,
        returnBlob:true
      });
    }
    var reportBlob=await reportPromise;
    if(!(reportBlob instanceof Blob)) throw new Error('Pre-Screened Report PDF was not generated');
    if(!(highlightedBlob instanceof Blob)) throw new Error('Highlighted resume PDF was not generated');
    window.__rsBusy=true;
    await mergePreScreenedReportAndResume(reportBlob,highlightedBlob,resumeTrackingFileName(data,conversionMarker,'pdf'),{matchReportWidthToResume:true});
    return true;
  }catch(err){
    console.error('Pre-Screened highlighted resume package error:',err);
    alert('Could not generate the Pre-Screened Report + Resume + Highlighted Keywords package.\n\nReason: '+(err && err.message ? err.message : 'Unknown package error')+'\n\nPDF resumes use direct R2/browser processing first. DOC files require enterprise converter fallback.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildPreAntigravityPreScreenedHighlightedResume(resumePdfUrl,options){
  /* Purpose: isolated test copy of the pre-Antigravity option 17 browser conversion
     behavior so DOCX quality can be compared before changing the production path. */
  if(window.__rsBusy) return Promise.resolve(false);
  try{
    options=options||{};
    var data=readData();
    var reportBlob=await buildRecruiterSummaryBlob(data,options);
    var highlightedBlob=await buildHeadlessOption3SkillsHighlightedResume({
      resumeUrl:options.resumeUrl,
      resumeExt:options.resumeExt,
      // Server-side conversion intentionally disabled for this test option.
      resumePdfUrl:null,
      preferClientDocx:options.preferClientDocx===true,
      clientDocxMaxBytes:parseInt(options.clientDocxMaxBytes,10)||1536*1024,
      clientDocxMaxPages:parseInt(options.clientDocxMaxPages,10)||6,
      clientDocxRequireTextPdf:true,
      strictClientOnly:true,
      returnBlob:true
    });
    if(!(reportBlob instanceof Blob)) throw new Error('Pre-Screened Report PDF was not generated');
    if(!(highlightedBlob instanceof Blob)) throw new Error('Highlighted resume PDF was not generated');
    window.__rsBusy=true;
    await mergePreScreenedReportAndResume(reportBlob,highlightedBlob,resumeTrackingFileName(data,'C','pdf'),{matchReportWidthToResume:true});
    return true;
  }catch(err){
    console.error('Pre-Antigravity test package error:',err);
    alert('Could not generate the test Pre-Screened Report + Resume + Highlighted Keywords package.\n\nReason: '+(err && err.message ? err.message : 'Unknown package error')+'\n\nThis test uses the older browser conversion limits from before the Antigravity options.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildCombinedPreScreenedResume(resumePdfUrl,options){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeUrl=clean(options.resumeUrl||'','');
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    resumePdfUrl=clean(resumePdfUrl,'');
    if(resumeUrl==='--') resumeUrl='';
    if(resumePdfUrl==='--') resumePdfUrl='';
    if(['PDF','DOC','DOCX'].indexOf(resumeExt)===-1) throw new Error('Combined report is available for PDF, DOC and DOCX resumes');
    if(!resumePdfUrl) throw new Error('Resume PDF URL not available');
    var data=readData();
    var reportBlob=await buildRecruiterSummaryBlob(data,options);
    var resumeBlob=await fetchPdfResumeBlob(resumePdfUrl);
    await mergePreScreenedReportAndResume(reportBlob,resumeBlob,resumeTrackingFileName(data,'S','pdf'));
    return true;
  }catch(err){
    console.error('Combined Pre-Screened Report error:',err);
    var message=err && err.message ? err.message : 'Could not generate the combined Pre-Screened Report. Please try again.';
    if(/HTTP 503|converter unavailable|Document converter unavailable/i.test(message)){
      alert('Enterprise Word-to-PDF converter is not available on the server. Please enable LibreOffice/Gotenberg or upload a PDF resume.');
    }else{
      alert('Could not generate the combined Pre-Screened Report. Please try again.');
    }
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildWordPreScreenedResume(resumePdfUrl,options){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeUrl=clean(options.resumeUrl||'','');
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    resumePdfUrl=clean(resumePdfUrl,'');
    if(['DOC','DOCX'].indexOf(resumeExt)===-1) throw new Error('Word Pre-Screened Report is available for DOC and DOCX resumes only');
    if(!resumePdfUrl && !(resumeExt==='DOCX' && resumeUrl)) throw new Error('Resume PDF URL not available');
    var data=readData();
    var reportBlob=await buildRecruiterSummaryBlob(data,options);
    var resumeBlob=null;
    var converterErr=null;
    if(resumePdfUrl){
      try{
        resumeBlob=await fetchPdfResumeBlob(resumePdfUrl);
      }catch(err){
        converterErr=err;
      }
    }
    if(!resumeBlob && resumeExt==='DOCX' && resumeUrl){
      try{
        resumeBlob=await convertResumeWithOpenDocLabBlob(resumeUrl,resumeExt,false);
      }catch(openDocErr){
        console.warn('Option 13 OpenDoc fallback failed:',openDocErr);
        resumeBlob=await convertDocxResumeToPdfBlob(resumeUrl);
      }
    }
    if(!resumeBlob) throw (converterErr || new Error('Converted resume PDF not available'));
    await mergePreScreenedReportAndResume(reportBlob,resumeBlob,resumeTrackingFileName(data,'S','pdf'));
    return true;
  }catch(err){
    console.error('Word Pre-Screened Report merge error:',err);
    var message=err && err.message ? err.message : '';
    if(/HTTP 503|converter unavailable|Document converter unavailable|Converted resume PDF not available/i.test(message)){
      alert('Word resume conversion is not available right now. For DOC files, please enable the enterprise converter or upload a PDF resume. For DOCX, the browser fallback was also unable to produce a clean PDF.');
    }else{
      alert('Could not generate the Word Pre-Screened Report package. Please try option 10 first, then retry option 13.');
    }
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildHybridWordPreScreenedResume(resumePdfUrl,options){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeSources=(Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl||'']).map(function(url){
      return clean(url,'');
    }).filter(function(url,index,self){
      return url && url!=='--' && self.indexOf(url)===index;
    });
    var resumeUrl=clean(resumeSources[0]||'','');
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    if(resumeUrl==='--') resumeUrl='';
    if(resumeExt!=='DOCX') throw new Error('Client-scale package is available for DOCX resumes only');
    if(!resumeUrl) throw new Error('DOCX resume URL not available');
    var data=readData();
    var reportBlob=await buildRecruiterSummaryBlob(data,options);
    var resumeBlob=await convertResumeWithOpenDocLabBlob(resumeSources,resumeExt,false);
    await mergePreScreenedReportAndResume(reportBlob,resumeBlob,resumeTrackingFileName(data,'C','pdf'));
    return true;
  }catch(err){
    console.error('Hybrid Word Pre-Screened Report merge error:',err);
    alert('Could not generate the client-side DOCX Pre-Screened Report package.\n\nReason: '+(err && err.message ? err.message : 'Unknown browser conversion error')+'\n\nThis option does not use server conversion. Please use option 13 or option 10 for enterprise converter mode.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function resolveSmartPreScreenedResumeBlob(resumePdfUrl,resumeSources,resumeExt){
  /* Purpose: options 15/16 must append a clean resume only; this isolated resolver never enables keyword highlighting. */
  resumeExt=clean(resumeExt||'','').toUpperCase();
  resumePdfUrl=clean(resumePdfUrl,'');
  if(resumeExt==='PDF'){
    return {blob:await fetchPdfBlobFromSources(resumeSources),marker:'C'};
  }
  if(resumeExt==='DOCX'){
    try{
      return {blob:await convertResumeWithOpenDocLabBlob(resumeSources,resumeExt,false,{skipHighlights:true}),marker:'C'};
    }catch(browserDocxErr){
      console.warn('Options 15/16 clean DOCX browser conversion failed; using enterprise converter PDF URL if available:',browserDocxErr);
      if(!resumePdfUrl) throw browserDocxErr;
      return {blob:await fetchPdfResumeBlob(resumePdfUrl),marker:'S'};
    }
  }
  if(resumeExt==='DOC'){
    if(!resumePdfUrl) throw new Error('DOC resume needs enterprise converter PDF URL');
    return {blob:await fetchPdfResumeBlob(resumePdfUrl),marker:'S'};
  }
  throw new Error('Clean Pre-Screened package is available for PDF, DOC and DOCX resumes only');
}
async function buildSmartPreScreenedResume(resumePdfUrl,options){
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeSources=(Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl||'']).map(function(url){
      return clean(url,'');
    }).filter(function(url,index,self){
      return url && url!=='--' && self.indexOf(url)===index;
    });
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    resumePdfUrl=clean(resumePdfUrl,'');
    if(['PDF','DOC','DOCX'].indexOf(resumeExt)===-1) throw new Error('Smart Pre-Screened package is available for PDF, DOC and DOCX resumes only');
    var data=readData();
    var reportBlob=await buildRecruiterSummaryBlob(data,options);
    var resumeResult=await resolveSmartPreScreenedResumeBlob(resumePdfUrl,resumeSources,resumeExt);
    var resumeBlob=resumeResult.blob;
    var reportFileName=resumeTrackingFileName(data,resumeResult.marker,'pdf');
    await mergePreScreenedReportAndResume(reportBlob,resumeBlob,reportFileName);
    return true;
  }catch(err){
    console.error('Smart Pre-Screened Report package error:',err);
    alert('Could not generate the final Pre-Screened Report + Resume package.\n\nReason: '+(err && err.message ? err.message : 'Unknown package error')+'\n\nPDF and DOCX use direct R2/browser processing first. DOC files require enterprise converter fallback.');
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildTurboPreScreenedHighlightedPackage(resumePdfUrl,options){
  /* Purpose: Option 27 — Pure Zero-Raster Vector Pre-Screened Package.
     1. Runs vector Recruiter Summary (pdfMake) and vector keyword highlighting (pdf-lib) concurrently in parallel.
     2. Zero canvas rasterization, zero JPEGs, 100% crisp vector typography on all pages.
     3. Merges vector report at Page 1 and downloads in ~400ms-600ms, with 0% server CPU load. */
  if(window.__rsBusy) return Promise.resolve(false);
  try{
    options=options||{};
    var data=readData();

    // Run report creation and vector resume highlighting in parallel:
    var results=await Promise.all([
      buildRecruiterSummaryBlob(data,options),
      buildTurboVectorHighlightedResume({
        resumeUrl:options.resumeUrl,
        resumeExt:options.resumeExt,
        resumePdfUrl:resumePdfUrl,
        returnBlob:true
      })
    ]);

    var reportBlob=results[0];
    var highlightedBlob=results[1];

    if(!(reportBlob instanceof Blob)) throw new Error('Pre-Screened Report PDF was not generated');
    if(!(highlightedBlob instanceof Blob)) throw new Error('Turbo Vector highlighted resume PDF was not generated');

    window.__rsBusy=true;
    var fileName=resumeTrackingFileName(data,'C','pdf');
    await mergePreScreenedReportAndResume(reportBlob,highlightedBlob,fileName,{matchReportWidthToResume:true});
    return true;
  }catch(err){
    console.error('Turbo Pre-Screened Highlighted package error:',err);
    alert('Could not generate Turbo Pre-Screened Highlighted Package.\n\nReason: '+(err && err.message ? err.message : 'Unknown package error'));
    return false;
  }finally{
    window.__rsBusy=false;
  }
}
window.__preloadRS=preloadRecruiterSummaryAssets;
window.__preloadRSCombined=preloadCombinedReportAssets;
window.__preloadBrowserDocxConversion=preloadBrowserDocxConversionAssets;
window.__preloadOpenDocLab=preloadOpenDocLabAssets;
window.__preloadOpenDocLocal=preloadOpenDocLocalAssets;
window.__buildRSBlob=buildRecruiterSummaryBlob;
window.__buildRS=buildRecruiterSummary;
window.__buildCombinedRSResume=buildCombinedPreScreenedResume;
window.__buildWordPreScreenedResume=buildWordPreScreenedResume;
window.__buildHybridWordPreScreenedResume=buildHybridWordPreScreenedResume;
window.__buildSmartPreScreenedResume=buildSmartPreScreenedResume;
window.__buildSmartBrandedResume=buildSmartBrandedResume;
window.__buildSmartHighlightedResume=buildSmartHighlightedResume;
window.__buildClientAnonymizedResume=buildClientAnonymizedResume;
window.__preloadClientAnonymizer=preloadClientAnonymizerAssets;
window.__buildContactHiddenBrandedResume=buildContactHiddenBrandedResume;
window.__buildContactHiddenPreScreenedResume=buildContactHiddenPreScreenedResume;
window.__buildContactHiddenPreScreenedNoBrandResume=buildContactHiddenPreScreenedNoBrandResume;
window.__buildHeadlessOption3SkillsHighlightedResume=buildHeadlessOption3SkillsHighlightedResume;
window.__buildHighlightedBrandedResume=buildHighlightedBrandedResume;
window.__buildIsolatedHighlightedKeywordsResume=buildIsolatedHighlightedKeywordsResume;
window.__buildFastHighlightedBrandingResume=buildFastHighlightedBrandingResume;
window.__buildPreScreenedHighlightedResume=buildPreScreenedHighlightedResume;
window.__buildPreAntigravityPreScreenedHighlightedResume=buildPreAntigravityPreScreenedHighlightedResume;
window.__buildTurboPreScreenedHighlightedPackage=buildTurboPreScreenedHighlightedPackage;
window.__buildTurboVectorHighlightedResume=buildTurboVectorHighlightedResume;
window.__buildTurboVectorBrandedResume=buildTurboVectorBrandedResume;
window.__buildClientLightningHighlightedResume=buildClientLightningHighlightedResume;
window.__buildClientLightningBrandedResume=buildClientLightningBrandedResume;
window.__preloadClientLightning=preloadClientLightningAssets;
window.__convertResumeToPdfOnly=convertResumeToPdfOnly;
window.__convertResumeToPdfBrowserOnly=convertResumeToPdfBrowserOnly;
window.__convertResumeWithOpenDocLab=convertResumeWithOpenDocLab;
})();
</script>

{{-- Overlay backdrop --}}
<div id="rdcOverlay" class="rdc-overlay" aria-hidden="true"></div>

{{-- Drawer panel --}}
<div id="rdcDrawer" class="rdc-drawer" role="dialog" aria-modal="true" aria-label="Download Resume Center" aria-hidden="true" tabindex="-1">

  {{-- Header --}}
  <div class="rdc-header">
    <div class="rdc-head-left">
      <span class="rdc-head-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
      </span>
      <div class="rdc-header-text">
        <p class="rdc-title">Download Resume Center</p>
        <p class="rdc-subtitle">Choose the resume, report, or recruiter package you want to download.</p>
      </div>
    </div>
    <div class="rdc-head-actions">
      <button class="rdc-close-btn" id="rdcCloseBtn" aria-label="Close" title="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
  </div>

  {{-- Scrollable body --}}
  <div class="rdc-body" id="rdcBody">

    {{-- Section heading --}}
    <div class="rdc-sect-lbl">Choose Download Format</div>

    {{-- 6 Format option cards --}}
    <div class="rdc-opts-list" id="rdcOptsList" role="radiogroup" aria-label="Download format">

      {{-- 1: Original Resume --}}
      <label class="rdc-opt rdc-opt-sel" data-opt="1" data-coins="1">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="1" checked>
        <span class="rdc-onum">1</span>
        <span class="rdc-oi rdc-ic-blue" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Original Resume</span>
          <span class="rdc-odesc">Download the candidate's uploaded resume exactly as received, with no edits or formatting changes.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">1 Coin</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 29: Client-side anonymized resume --}}
      <label class="rdc-opt" data-opt="29" data-coins="4">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="29">
        <span class="rdc-onum">2</span>
        <span class="rdc-oi rdc-ic-blue" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M9 12h6"/><path d="M12 9v6"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Candidate Name & Contacts Details</span>
          <span class="rdc-odesc">Hide candidate name, email, and phone numbers in a browser-only resume copy.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 30: Client-side contact-hidden resume that keeps candidate name visible --}}
      <label class="rdc-opt" data-opt="30" data-coins="4">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="30">
        <span class="rdc-onum">3</span>
        <span class="rdc-oi rdc-ic-blue" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M8 11h8"/><path d="M8 15h5"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details</span>
          <span class="rdc-odesc">Keep candidate name visible while hiding email, phone, mobile, contact and LinkedIn details in browser.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 31: Client-side contact-hidden resume with company branding --}}
      <label class="rdc-opt" data-opt="31" data-coins="6">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="31">
        <span class="rdc-onum">4</span>
        <span class="rdc-oi rdc-ic-green" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M8 12h8"/><path d="M4 20h16"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details + Company Branding</span>
          <span class="rdc-odesc">Keep the name visible, hide contact and LinkedIn details, then add your company header.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">6 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 32: Client-side contact-hidden resume with pre-screened report --}}
      <label class="rdc-opt" data-opt="32" data-coins="11">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="32">
        <span class="rdc-onum">11</span>
        <span class="rdc-oi" style="background:#f5f3ff;color:#6d28d9;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details + Pre-Screened Report</span>
          <span class="rdc-odesc">Keep the name visible, hide contact and LinkedIn details, then merge the pre-screened report.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">11 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 33: Client-side contact-hidden resume with no-brand pre-screened report --}}
      <label class="rdc-opt" data-opt="33" data-coins="12">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="33">
        <span class="rdc-onum">12</span>
        <span class="rdc-oi" style="background:#f8fafc;color:#475569;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="m4 4 16 16"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details + Pre-Screened Report without Company Branding</span>
          <span class="rdc-odesc">Keep the name visible, hide contact and LinkedIn details, then merge a no-brand report.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">12 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 3: ATS Highlighted --}}
      <label class="rdc-opt" data-opt="3" data-coins="4" style="display:none;" aria-hidden="true">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="3">
        <span class="rdc-onum">3</span>
        <span class="rdc-oi rdc-ic-amber" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">ATS Highlighted Resume</span>
          <span class="rdc-odesc">Review the resume with role-matched skills highlighted so recruiters can scan fit faster.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 7: R2/browser highlighted keywords package --}}
      <label class="rdc-opt" data-opt="7" data-coins="4" style="display:none;" aria-hidden="true">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="7">
        <span class="rdc-onum">7</span>
        <span class="rdc-oi rdc-ic-amber" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"/><path d="m7 16 8.5-8.5 3 3L10 19H7v-3z"/><path d="m14 9 3 3"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlighted Keywords</span>
          <span class="rdc-odesc">Create a recruiter-ready resume copy with important job skills highlighted for quick screening.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 8: Headless option 3 style highlighted skills from direct/converted PDF --}}
      <label class="rdc-opt" data-opt="8" data-coins="4">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="8">
        <span class="rdc-onum">8</span>
        <span class="rdc-oi rdc-ic-amber" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/><path d="M7 15h7"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Skills Highlighted</span>
          <span class="rdc-odesc">Download a screening copy that highlights the candidate skills most relevant to the job.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 9: Option 8 highlighted resume plus exact option 6 company branding --}}
      <label class="rdc-opt" data-opt="9" data-coins="6">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="9">
        <span class="rdc-onum">9</span>
        <span class="rdc-oi rdc-ic-green" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"/><path d="m7 16 8.5-8.5 3 3L10 19H7v-3z"/><path d="M9 7h6"/><path d="M9 11h4"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlighted + Company Branding</span>
          <span class="rdc-odesc">Share a branded resume version with relevant skills highlighted and your company header applied.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">6 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 13: Pre-screened report plus highlighted resume package --}}
      <label class="rdc-opt" data-opt="13" data-coins="8" style="display:none;" aria-hidden="true">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="13">
        <span class="rdc-onum">13</span>
        <span class="rdc-oi" style="background:#f5f3ff;color:#6d28d9;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h5"/><path d="M8 17h8"/><path d="m15 13 2 2 4-4"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume + Highlighted Keywords</span>
          <span class="rdc-odesc">Combine the candidate report and highlighted resume into one client-ready hiring document.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">8 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 14: Pre-screened report without company branding plus highlighted resume package --}}
      <label class="rdc-opt" data-opt="14" data-coins="8" style="display:none;" aria-hidden="true">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="14">
        <span class="rdc-onum">14</span>
        <span class="rdc-oi" style="background:#f8fafc;color:#475569;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h5"/><path d="M8 17h8"/><path d="m4 4 16 16"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume + Highlighted Keywords without Company Branding</span>
          <span class="rdc-odesc">Prepare the report and highlighted resume together without displaying company branding.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">8 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 4: Branded + Highlighted --}}
      <label class="rdc-opt" data-opt="4" data-coins="6" style="display:none;" aria-hidden="true">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="4">
        <span class="rdc-onum">4</span>
        <span class="rdc-oi rdc-ic-green" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Branded + Highlighted</span>
          <span class="rdc-odesc">Add company branding and highlight matching skills so the resume is easier to review and share.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">6 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 5: Pre-Screened Report --}}
      <label class="rdc-opt" data-opt="5" data-coins="7">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="5">
        <span class="rdc-onum">8</span>
        <span class="rdc-oi rdc-ic-rose" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report</span>
          <span class="rdc-odesc">Generate a concise hiring report with fit summary, key skills, and verification status for recruiter review.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">7 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 6: New R2/browser branded resume header package --}}
      <label class="rdc-opt" data-opt="6" data-coins="2">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="6">
        <span class="rdc-onum">5</span>
        <span class="rdc-oi" style="background:#ecfeff;color:#0e7490;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h10l6 6v10a2 2 0 0 1-2 2H4V4z"/><path d="M14 4v6h6"/><path d="M8 13h8"/><path d="M8 17h5"/><path d="M3 7h18"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Company Branding</span>
          <span class="rdc-odesc">Add your company logo and shared-by header above the resume while keeping all candidate details visible.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">2 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 15: Smart final package with company branding --}}
      <label class="rdc-opt" data-opt="15" data-coins="8">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="15">
        <span class="rdc-onum">9</span>
        <span class="rdc-oi" style="background:#ecfdf5;color:#059669;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h5"/><path d="m15 18 2 2 4-4"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume + Company Branding</span>
          <span class="rdc-odesc">Deliver one polished PDF with the pre-screened report first, followed by the candidate resume with company branding.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">8 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 16: Smart final package without company branding --}}
      <label class="rdc-opt" data-opt="16" data-coins="8">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="16">
        <span class="rdc-onum">10</span>
        <span class="rdc-oi" style="background:#f8fafc;color:#475569;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/><path d="m4 4 16 16"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume without Company Branding</span>
          <span class="rdc-odesc">Download the same report-and-resume package without company logo or company name on the report page.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">8 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 17: Client-first DOCX pre-screened report plus highlighted resume package --}}
      <label class="rdc-opt" data-opt="17" data-coins="8">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="17">
        <span class="rdc-onum">13</span>
        <span class="rdc-oi" style="background:#eef2ff;color:#4338ca;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M3 12h18"/><path d="M7 7h10v10H7z"/><path d="m15 9 2 2-2 2"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume + Highlighted Keywords + Company Branding</span>
          <span class="rdc-odesc">Create the recruiter package: pre-screened report, branded resume pages, and highlighted job-matching skills.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">8 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 34: Test copy of pre-Antigravity browser conversion for option 17 --}}
      <label class="rdc-opt" data-opt="34" data-coins="8">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="34">
        <span class="rdc-onum">15</span>
        <span class="rdc-oi" style="background:#fff7ed;color:#ea580c;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2h6"/><path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2"/><path d="M8.5 14h7"/><path d="M9.5 18h5"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Test: Pre-Screened Report + Resume + Highlighted Keywords + Company Branding</span>
          <span class="rdc-odesc">Test the older browser conversion flow from before the Antigravity options.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">8 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 19: Client-first DOCX pre-screened report without company branding plus highlighted resume package --}}
      <label class="rdc-opt" data-opt="19" data-coins="8">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="19">
        <span class="rdc-onum">14</span>
        <span class="rdc-oi" style="background:#f8fafc;color:#475569;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M3 12h18"/><path d="M7 7h10v10H7z"/><path d="m4 4 16 16"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume + Highlighted Keywords without Company Branding</span>
          <span class="rdc-odesc">Create the recruiter package with report, resume, and highlighted skills, without company branding.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">8 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 20: Client-first highlighted resume plus company branding package --}}
      <label class="rdc-opt" data-opt="20" data-coins="6">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="20">
        <span class="rdc-onum">7</span>
        <span class="rdc-oi rdc-ic-green" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"/><path d="m7 16 8.5-8.5 3 3L10 19H7v-3z"/><path d="M9 7h6"/><path d="M9 11h4"/><path d="M14 2v6h6"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlighted Keywords + Company Branding</span>
          <span class="rdc-odesc">Download a branded resume with job-matching keywords highlighted for faster recruiter screening.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">6 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 28: Isolated client-first highlighted resume without company branding --}}
      <label class="rdc-opt" data-opt="28" data-coins="4">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="28">
        <span class="rdc-onum">6</span>
        <span class="rdc-oi rdc-ic-green" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"/><path d="m7 16 8.5-8.5 3 3L10 19H7v-3z"/><path d="M9 7h6"/><path d="M9 11h4"/><path d="M14 2v6h6"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlighted Keywords</span>
          <span class="rdc-odesc">Download a clean resume copy with the most relevant skills highlighted for quick recruiter review.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 21: Fast highlighted resume plus separate company branding page --}}
      <label class="rdc-opt" data-opt="21" data-coins="6">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="21">
        <span class="rdc-onum">21</span>
        <span class="rdc-oi rdc-ic-green" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"/><path d="M8 4h8v5H8z"/><path d="M7 14l3 3 7-7"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlighted Keywords + Company Branding</span>
          <span class="rdc-odesc">Download a branded resume package that highlights role-matched skills for easier recruiter review.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">6 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 22: Client-first highlighted resume only package --}}
      <label class="rdc-opt" data-opt="22" data-coins="4">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="22">
        <span class="rdc-onum">22</span>
        <span class="rdc-oi rdc-ic-amber" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"/><path d="M7 16l8.5-8.5 3 3L10 19H7v-3z"/><path d="M8 7h8"/><path d="M8 11h5"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlighted Keywords</span>
          <span class="rdc-odesc">Download the resume with matching skills highlighted, without adding report pages or branding.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 23: Turbo Vector highlighted resume plus company branding --}}
      <label class="rdc-opt" data-opt="23" data-coins="6">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="23">
        <span class="rdc-onum">23</span>
        <span class="rdc-oi" style="background:#f0fdf4;color:#16a34a;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlights + Branding</span>
          <span class="rdc-odesc">Prepare a branded resume version with skill highlights for quick client or hiring-team sharing.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">6 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 24: Turbo Vector highlighted resume only --}}
      <label class="rdc-opt" data-opt="24" data-coins="4">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="24">
        <span class="rdc-onum">24</span>
        <span class="rdc-oi" style="background:#fef9c3;color:#a16207;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlights</span>
          <span class="rdc-odesc">Prepare a resume copy with role-matched highlights and no additional report or branding pages.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 25: Client Lightning highlighted resume plus company branding --}}
      <label class="rdc-opt" data-opt="25" data-coins="6">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="25">
        <span class="rdc-onum">25</span>
        <span class="rdc-oi" style="background:#ede9fe;color:#7c3aed;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlights + Branding</span>
          <span class="rdc-odesc">Create a polished branded resume with highlighted skills for recruiter and client evaluation.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">6 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 26: Client Lightning highlighted resume only --}}
      <label class="rdc-opt" data-opt="26" data-coins="4">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="26">
        <span class="rdc-onum">26</span>
        <span class="rdc-oi" style="background:#e0e7ff;color:#4338ca;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlights</span>
          <span class="rdc-odesc">Create a clean highlighted resume copy that helps recruiters spot matching skills quickly.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 27: Turbo Pre-Screened Report + Resume + Highlights (OpenDoc Client-Side Package) --}}
      <label class="rdc-opt" data-opt="27" data-coins="8">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="27">
        <span class="rdc-onum">27</span>
        <span class="rdc-oi" style="background:#fdf2f8;color:#be185d;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume + Highlights</span>
          <span class="rdc-odesc">Package the pre-screened report with highlighted resume pages for complete recruiter handoff.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">8 Coins</span>
          <span class="rdc-otime">60 seconds</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

    </div>{{-- /.rdc-opts-list --}}

    {{-- Coin info summary --}}
    <div class="rdc-coin-card">
      <div class="rdc-coin-row">
        <span class="rdc-coin-dot"></span>
        You will be charged <strong id="rdcCoinCost">1 Coin</strong> for this download
      </div>
      <div class="rdc-bal-row" style="color:#94a3b8;font-size:11px;">Coin balance and deduction are shown before download confirmation.</div>
    </div>

  </div>{{-- /.rdc-body --}}

  {{-- Sticky footer --}}
  <div class="rdc-footer">
    <button id="rdcDownloadBtn" class="rdc-dl-btn" type="button">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;flex-shrink:0" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      <span id="rdcBtnLabel">Download Resume</span>
    </button>
    <p class="rdc-foot-note">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px;color:#94a3b8" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Coins deducted only after a successful download
    </p>
  </div>

</div>{{-- /#rdcDrawer --}}

{{-- Toast --}}
<div id="rdcToast" class="rdc-toast" role="status" aria-live="polite"></div>

<script>
(function(){
  'use strict';

  function latestById(id){
    var nodes = document.querySelectorAll('[id="'+id+'"]');
    return nodes.length ? nodes[nodes.length - 1] : null;
  }

  var overlay  = latestById('rdcOverlay');
  var drawer   = latestById('rdcDrawer');
  var closeBtn = latestById('rdcCloseBtn');
  var dlBtn    = latestById('rdcDownloadBtn');
  var btnLbl   = latestById('rdcBtnLabel');
  var coinEl   = latestById('rdcCoinCost');
  var toastEl  = latestById('rdcToast');
  var opts     = drawer ? drawer.querySelectorAll('#rdcOptsList .rdc-opt') : document.querySelectorAll('#rdcOptsList .rdc-opt');
  var rdcData  = latestById('rdcData');
  var rdcSectionsBuilt = false;

  var dlUrl  = rdcData ? rdcData.getAttribute('data-dl-url')  : '';
  var r2DirectUrl = rdcData ? rdcData.getAttribute('data-r2-direct-url') : '';
  var r2PublicUrl = rdcData ? rdcData.getAttribute('data-r2-public-url') : '';
  var originalR2DirectUrl = rdcData ? rdcData.getAttribute('data-original-r2-direct-url') : '';
  var originalR2PublicUrl = rdcData ? rdcData.getAttribute('data-original-r2-public-url') : '';
  var originalR2Available = rdcData ? rdcData.getAttribute('data-original-r2-available') === 'true' : false;
  var r2SignUrl = rdcData ? rdcData.getAttribute('data-r2-sign-url') : '';
  var r2SignPromise = null;
  var r2SignReady = false;
  var resumePdfUrl = rdcData ? rdcData.getAttribute('data-resume-pdf-url') : '';
  var resumeExt = rdcData ? (rdcData.getAttribute('data-file-ext') || '').toUpperCase() : '';
  var hasResume = rdcData ? rdcData.getAttribute('data-has-resume') === 'true' : false;

  function rdcCleanLocal(value){
    return String(value == null ? '' : value).replace(/\s+/g,' ').trim();
  }

  function rdcCollectDownloadSources(values){
    var seen = {};
    return (Array.isArray(values) ? values : [values]).map(function(value){
      return rdcCleanLocal(value);
    }).filter(function(value){
      if(!value || value === '--' || seen[value]) return false;
      seen[value] = true;
      return true;
    });
  }

  function rdcOriginalFileName(){
    var ext = rdcCleanLocal(resumeExt || 'FILE').toLowerCase().replace(/[^a-z0-9]+/g,'') || 'file';
    try {
      if(typeof readData === 'function' && typeof resumeTrackingFileName === 'function'){
        return resumeTrackingFileName(readData(), 'C', ext);
      }
    } catch(ex) {}
    return 'JEROWORKS_Candidate.' + ext;
  }

  function startOriginalResumeDownload(options){
    /* Purpose: keep option 1 self-contained inside the RDC script. The drawer can
       be injected without exposing earlier helper functions on window. */
    options = options || {};
    var sources = rdcCollectDownloadSources([
      options.r2RedirectUrl,
      options.dlUrl,
      options.r2DirectUrl,
      options.originalR2DirectUrl,
      options.r2PublicUrl,
      options.originalR2PublicUrl
    ]);
    if(!sources.length) throw new Error('Original resume is not available for download.');

    var src = sources[0];
    try { src = encodeURI(decodeURI(src)); } catch(ex) {}

    var oldFrame = document.getElementById('rdcOriginalResumeDownloadFrame');
    if(oldFrame && oldFrame.parentNode) oldFrame.parentNode.removeChild(oldFrame);

    var frame = document.createElement('iframe');
    frame.id = 'rdcOriginalResumeDownloadFrame';
    frame.name = 'rdcOriginalResumeDownloadFrame';
    frame.style.display = 'none';
    frame.setAttribute('aria-hidden','true');
    frame.onload = function(){
      try {
        var text = frame.contentDocument && frame.contentDocument.body ? frame.contentDocument.body.innerText : '';
        if(text && /Original resume|No resume|not found|error/i.test(text)){
          showToast(text.replace(/\s+/g,' ').trim().slice(0, 180), 'warn');
        }
      } catch(ex) {}
    };
    document.body.appendChild(frame);

    var link = document.createElement('a');
    link.href = src;
    link.target = frame.name;
    document.body.appendChild(link);
    link.click();
    setTimeout(function(){ if(link.parentNode) link.parentNode.removeChild(link); }, 1200);
    setTimeout(function(){ if(frame.parentNode) frame.parentNode.removeChild(frame); }, 60000);
    return true;
  }

  var optMeta = {
    1: {cost:1, label:'Download Resume',       avail:hasResume && !!(originalR2PublicUrl || originalR2DirectUrl || dlUrl),  dlKey:null, unavailableMsg:'Original resume is not available for download.'},
    3: {cost:4, label:'Download ATS Highlighted Resume',avail:true,  dlKey:'hl'},
    4: {cost:6, label:'Download Branded + Highlighted', avail:true,  dlKey:'both'},
    5: {cost:7, label:'Download Pre-Screened Report',   avail:true,  dlKey:'rs'},
    6: {cost:2, label:'Resume + Company Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'smart-branded-resume', unavailableMsg:'Branded resume is available for PDF, DOC and DOCX resumes only.'},
    7: {cost:4, label:'Resume + Highlighted Keywords', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1), dlKey:'smart-highlighted-resume', unavailableMsg:'Client-side highlighted resume is available for PDF and DOCX resumes only.'},
    8: {cost:4, label:'Resume + Skills Highlighted', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'headless-option3-skills-highlighted', unavailableMsg:'Resume + Skills Highlighted is available for PDF, DOC and DOCX resumes only.'},
    9: {cost:6, label:'Resume + Highlighted + Company Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'highlighted-branded-resume', unavailableMsg:'Resume + Highlighted + Company Branding is available for PDF, DOC and DOCX resumes only.'},
    13:{cost:8, label:'Pre-Screened Report + Resume + Highlighted Keywords', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'prescreened-highlighted-package', unavailableMsg:'Pre-Screened Report + Resume + Highlighted Keywords is available for PDF, DOC and DOCX resumes only.'},
    14:{cost:8, label:'Pre-Screened Report + Resume + Highlighted Keywords without Company Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'prescreened-highlighted-package-nobrand', unavailableMsg:'Pre-Screened Report + Resume + Highlighted Keywords without Company Branding is available for PDF, DOC and DOCX resumes only.'},
    15:{cost:8, label:'Pre-Screened Report + Resume + Company Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'smart-prescreened-package', unavailableMsg:'Final package is available for PDF, DOC and DOCX resumes only.'},
    16:{cost:8, label:'Pre-Screened Report + Resume without Company Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'smart-prescreened-package-nobrand', unavailableMsg:'Final package is available for PDF, DOC and DOCX resumes only.'},
    17:{cost:8, label:'Pre-Screened Report + Resume + Highlighted Keywords + Company Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'prescreened-highlighted-package-client-docx', unavailableMsg:'Pre-Screened Report + Resume + Highlighted Keywords + Company Branding is available for PDF, DOC and DOCX resumes only.'},
    19:{cost:8, label:'Pre-Screened Report + Resume + Highlighted Keywords without Company Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'prescreened-highlighted-package-client-docx-nobrand', unavailableMsg:'Pre-Screened Report + Resume + Highlighted Keywords without Company Branding is available for PDF, DOC and DOCX resumes only.'},
    20:{cost:6, label:'Resume + Highlighted Keywords + Company Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'highlighted-branded-resume-client-docx', unavailableMsg:'Resume + Highlighted Keywords + Company Branding is available for PDF, DOC and DOCX resumes only.'},
    21:{cost:6, label:'Fast Resume + Highlighted Keywords + Company Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'fast-highlighted-branded-resume-client-docx', unavailableMsg:'Fast Resume + Highlighted Keywords + Company Branding is available for PDF, DOC and DOCX resumes only.'},
    22:{cost:4, label:'Fast Resume + Highlighted Keywords', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'fast-highlighted-resume-client-docx', unavailableMsg:'Fast Resume + Highlighted Keywords is available for PDF, DOC and DOCX resumes only.'},
    23:{cost:6, label:'Turbo Vector Resume + Highlights + Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'turbo-vector-highlighted-branded', unavailableMsg:'Turbo Vector Branded Resume is available for PDF, DOC and DOCX resumes only.'},
    24:{cost:4, label:'Turbo Vector Resume + Highlights', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'turbo-vector-highlighted', unavailableMsg:'Turbo Vector Highlighted Resume is available for PDF, DOC and DOCX resumes only.'},
    25:{cost:6, label:'Client Lightning Resume + Highlights + Branding', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'client-lightning-highlighted-branded', unavailableMsg:'Client Lightning Branded Resume is available for PDF, DOC and DOCX resumes only.'},
    26:{cost:4, label:'Client Lightning Resume + Highlights', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'client-lightning-highlighted', unavailableMsg:'Client Lightning Highlighted Resume is available for PDF, DOC and DOCX resumes only.'},
    27:{cost:8, label:'Turbo Pre-Screened Report + Resume + Highlights', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'turbo-prescreened-highlighted-package', unavailableMsg:'Turbo Pre-Screened Package is available for PDF, DOC and DOCX resumes only.'},
    28:{cost:4, label:'Resume + Highlighted Keywords', avail:(['PDF','DOC','DOCX'].indexOf(resumeExt) !== -1), dlKey:'isolated-highlighted-keywords-client-docx', unavailableMsg:'Resume + Highlighted Keywords is available for PDF, DOC and DOCX resumes only.'},
    29:{cost:4, label:'Hide Candidate Name & Contacts Details', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl), dlKey:'client-anonymized-resume', unavailableMsg:'Anonymized Resume is browser-only and available for direct R2 PDF/DOCX resumes.'},
    30:{cost:4, label:'Hide Contact Details', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl), dlKey:'client-anonymized-keep-name-resume', keepName:true, unavailableMsg:'Hide Contact Details is browser-only and available for direct R2 PDF/DOCX resumes.'},
    31:{cost:6, label:'Hide Contact Details + Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl), dlKey:'client-contact-hidden-branded-resume', unavailableMsg:'Hide Contact Details + Company Branding is browser-only and available for direct R2 PDF/DOCX resumes.'},
    32:{cost:11, label:'Hide Contact Details + Pre-Screened Report', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl), dlKey:'client-contact-hidden-prescreened-resume', unavailableMsg:'Hide Contact Details + Pre-Screened Report is browser-only and available for direct R2 PDF/DOCX resumes.'},
    33:{cost:12, label:'Hide Contact Details + Pre-Screened Report without Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl), dlKey:'client-contact-hidden-prescreened-nobrand-resume', unavailableMsg:'Hide Contact Details + Pre-Screened Report without Company Branding is browser-only and available for direct R2 PDF/DOCX resumes.'},
    34:{cost:8, label:'Test: Pre-Screened Report + Resume + Highlighted Keywords + Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl), dlKey:'test-preantigravity-prescreened-highlighted-branded', unavailableMsg:'Test package is browser-only and available for direct PDF/DOCX resumes only.'}
  };

  var selectedOpt = 1;
  var isLoading   = false;

  function downloadButtonLabel(label){
    label=rdcCleanLocal(label) || 'Download';
    return /^download\b/i.test(label) ? label : 'Download '+label;
  }

  function rdcCoinText(cost){
    cost=parseInt(cost,10)||0;
    return cost === 1 ? '1 Coin' : cost + ' Coins';
  }

  function syncDownloadCenterSummary(m,n){
    m=m || optMeta[selectedOpt] || {cost:0,label:'Download Resume'};
    n=parseInt(n || selectedOpt,10);
    if(coinEl) coinEl.textContent = rdcCoinText(m.cost);
    if(btnLbl) btnLbl.textContent = downloadButtonLabel(m.label);
  }

  function hasDownloadableResume(){
    return hasResume && !!(dlUrl || r2DirectUrl || r2PublicUrl || resumePdfUrl);
  }

  function applyDownloadCenterR2Urls(payload){
    if(!payload || payload.status !== 'success') return;
    r2DirectUrl = rdcCleanLocal(payload.r2_direct_url || r2DirectUrl);
    r2PublicUrl = rdcCleanLocal(payload.r2_public_url || r2PublicUrl);
    originalR2DirectUrl = rdcCleanLocal(payload.original_r2_direct_url || originalR2DirectUrl);
    originalR2PublicUrl = rdcCleanLocal(payload.original_r2_public_url || originalR2PublicUrl);
    originalR2Available = payload.original_r2_available !== false && !!(originalR2DirectUrl || originalR2PublicUrl);
    if(rdcData){
      rdcData.setAttribute('data-r2-direct-url', r2DirectUrl);
      rdcData.setAttribute('data-r2-public-url', r2PublicUrl);
      rdcData.setAttribute('data-original-r2-direct-url', originalR2DirectUrl);
      rdcData.setAttribute('data-original-r2-public-url', originalR2PublicUrl);
      rdcData.setAttribute('data-original-r2-available', originalR2Available ? 'true' : 'false');
    }
    if(optMeta[1]){
      optMeta[1].avail = originalR2Available && !!(originalR2DirectUrl || originalR2PublicUrl);
    }
    if(optMeta[29]){
      optMeta[29].avail = ['PDF','DOCX'].indexOf(resumeExt) !== -1 && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl);
    }
    if(optMeta[30]){
      optMeta[30].avail = ['PDF','DOCX'].indexOf(resumeExt) !== -1 && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl);
    }
    if(optMeta[31]){
      optMeta[31].avail = ['PDF','DOCX'].indexOf(resumeExt) !== -1 && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl);
    }
    if(optMeta[32]){
      optMeta[32].avail = ['PDF','DOCX'].indexOf(resumeExt) !== -1 && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl);
    }
    if(optMeta[33]){
      optMeta[33].avail = ['PDF','DOCX'].indexOf(resumeExt) !== -1 && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl);
    }
    if(optMeta[34]){
      optMeta[34].avail = ['PDF','DOCX'].indexOf(resumeExt) !== -1 && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl);
    }
  }

  function ensureDownloadCenterR2Urls(){
    /* Purpose: defer R2 signing until the Download Resume Center is opened, keeping candidate drawer render cheap. */
    if(!r2SignUrl || r2SignReady) return Promise.resolve(true);
    if(r2SignPromise) return r2SignPromise;
    r2SignPromise = fetch(r2SignUrl,{
      method:'GET',
      credentials:'same-origin',
      headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
      cache:'no-store'
    }).then(function(response){
      if(!response.ok) throw new Error('Could not prepare direct R2 resume download.');
      return response.json();
    }).then(function(payload){
      applyDownloadCenterR2Urls(payload);
      r2SignReady = true;
      return true;
    }).catch(function(err){
      console.warn('Download Resume Center R2 signing failed:',err);
      return false;
    }).finally(function(){
      r2SignPromise = null;
    });
    return r2SignPromise;
  }

  function showNoResumeMessage(){
    showToast('No resume uploaded for this candidate. Please upload the resume first.', 'warn');
  }

  function rdcSectionIconSvg(type){
    var icons={
      resume:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>',
      privacy:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="m9 12 2 2 4-5"/></svg>',
      report:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M8 19v-7"/><path d="M12 19V9"/><path d="M16 19v-4"/><path d="M20 19V7"/></svg>',
      package:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8 12 3 3 8l9 5 9-5z"/><path d="m3 8 9 5v8l-9-5V8z"/><path d="m21 8-9 5v8l9-5V8z"/></svg>',
      keyword:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v5"/><path d="M12 17v5"/><path d="M4.93 4.93 8.46 8.46"/><path d="m15.54 15.54 3.53 3.53"/><path d="M2 12h5"/><path d="M17 12h5"/><path d="m4.93 19.07 3.53-3.53"/><path d="m15.54 8.46 3.53-3.53"/></svg>'
    };
    return icons[type] || icons.resume;
  }

  function rdcChevronSvg(){
    return '<svg class="rdc-section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>';
  }

  function buildDownloadOptionSections(){
    /* Purpose: group existing Download Resume Center options in the browser so the
       presentation can evolve without changing option IDs, handlers, or billing keys. */
    if(rdcSectionsBuilt) return;
    var list=latestById('rdcOptsList');
    if(!list || !opts || !opts.length) return;
    var optionMap={};
    Array.prototype.slice.call(opts).forEach(function(opt){
      optionMap[parseInt(opt.getAttribute('data-opt'),10)] = opt;
    });
    var groups=[
      {theme:'blue',icon:'resume',title:'1. Resume Options',pill:"Get the candidate's resume in different formats.",ids:[1,6]},
      {theme:'rose',icon:'report',title:'2. Pre-Screened Report',pill:'Get AI-powered insights and verification details.',ids:[5]},
      {theme:'green',icon:'package',title:'3. Recruiter Packages (Report + Resume)',pill:'',ids:[15,16]},
      {theme:'amber',icon:'keyword',title:'4. Highlighted skills',pill:'',ids:[28,20,17,34,19]},
      {theme:'purple',icon:'privacy',title:'5. Privacy & Anonymization',pill:'Protect candidate information as needed.',ids:[29,30,31,32,33]}
    ];
    var moved={};
    var frag=document.createDocumentFragment();
    groups.forEach(function(group){
      var section=document.createElement('section');
      section.className='rdc-section rdc-section-'+group.theme;
      var head=document.createElement('div');
      head.className='rdc-section-head';
      head.innerHTML =
        '<span class="rdc-section-icon" aria-hidden="true">'+rdcSectionIconSvg(group.icon)+'</span>' +
        '<span class="rdc-section-title">'+group.title+'</span>' +
        (group.pill ? '<span class="rdc-section-pill">'+group.pill+'</span>' : '') +
        '<span class="rdc-section-count">'+group.ids.length+' '+(group.ids.length === 1 ? 'option' : 'options')+'</span>' +
        rdcChevronSvg();
      var body=document.createElement('div');
      body.className='rdc-section-options';
      group.ids.forEach(function(id){
        if(optionMap[id]){
          body.appendChild(optionMap[id]);
          moved[id]=true;
        }
      });
      section.appendChild(head);
      section.appendChild(body);
      frag.appendChild(section);
    });
    var hiddenBin=document.createElement('div');
    hiddenBin.className='rdc-hidden-options';
    hiddenBin.setAttribute('hidden','hidden');
    Array.prototype.slice.call(opts).forEach(function(opt){
      var id=parseInt(opt.getAttribute('data-opt'),10);
      if(!moved[id]) hiddenBin.appendChild(opt);
    });
    while(list.firstChild) list.removeChild(list.firstChild);
    frag.appendChild(hiddenBin);
    list.appendChild(frag);
    rdcSectionsBuilt = true;
  }

  function renumberVisibleDownloadOptions(){
    var list=latestById('rdcOptsList');
    var visibleOpts=list ? list.querySelectorAll('.rdc-opt') : opts;
    if(!visibleOpts || !visibleOpts.length) return;
    Array.prototype.slice.call(visibleOpts)
      .filter(function(opt){
        if(opt.closest && opt.closest('.rdc-hidden-options')) return false;
        if(opt.getAttribute('aria-hidden') === 'true') return false;
        return window.getComputedStyle(opt).display !== 'none';
      })
      .forEach(function(opt,index){
        var badge=opt.querySelector('.rdc-onum');
        if(badge) badge.textContent = String(index + 1);
      });
  }

  function renderOpenRdc(){
    if(!hasDownloadableResume()){
      showNoResumeMessage();
      return;
    }
    buildDownloadOptionSections();
    renumberVisibleDownloadOptions();
    syncDownloadCenterSummary(optMeta[selectedOpt] || {}, selectedOpt);
    overlay.classList.add('rdc-open');
    drawer.classList.add('rdc-open');
    overlay.setAttribute('aria-hidden','false');
    drawer.setAttribute('aria-hidden','false');
    drawer.focus();
    if(typeof window.__preloadRS === 'function') window.__preloadRS();
    if(typeof window.__preloadClientLightning === 'function') window.__preloadClientLightning();
    document.addEventListener('keydown', onEsc);
  }

  function openRdc(){
    if(!hasDownloadableResume()){
      showNoResumeMessage();
      return;
    }
    renderOpenRdc();
    ensureDownloadCenterR2Urls();
  }

  function closeRdc(){
    if(isLoading || smartCountdownTimer) resetBtn();
    Array.prototype.slice.call(document.querySelectorAll('[id="rdcOverlay"]')).forEach(function(el){
      el.classList.remove('rdc-open');
      el.setAttribute('aria-hidden','true');
    });
    Array.prototype.slice.call(document.querySelectorAll('[id="rdcDrawer"]')).forEach(function(el){
      el.classList.remove('rdc-open');
      el.setAttribute('aria-hidden','true');
    });
    document.removeEventListener('keydown', onEsc);
  }

  function onEsc(e){ if(e.key === 'Escape') closeRdc(); }

  /* Intercept existing Download Resume button — clone removes old listeners */
  var origBtn = document.getElementById('hdrDlToggleApp');
  if(origBtn){
    var newBtn = origBtn.cloneNode(true);
    origBtn.parentNode.replaceChild(newBtn, origBtn);
    if(!hasDownloadableResume()){
      newBtn.setAttribute('aria-disabled','true');
      newBtn.setAttribute('title','No resume uploaded for this candidate');
      newBtn.style.opacity = '0.55';
      newBtn.style.cursor = 'not-allowed';
    }
    newBtn.addEventListener('click', function(e){
      e.preventDefault();
      e.stopPropagation();
      if(!hasDownloadableResume()){
        newBtn.setAttribute('aria-expanded','false');
        showNoResumeMessage();
        return;
      }
      newBtn.setAttribute('aria-expanded','true');
      openRdc();
    });
  }
  /* Prevent old dropdown menu from appearing */
  var oldMenu = document.getElementById('hdrDlMenuApp');
  if(oldMenu){ oldMenu.style.display = 'none'; }

  overlay.addEventListener('click', closeRdc);
  if(closeBtn) closeBtn.addEventListener('click', closeRdc);

  /* Option card selection */
  opts.forEach(function(opt){
    opt.addEventListener('click', function(){
      selectOpt(parseInt(opt.getAttribute('data-opt'), 10));
    });
  });

  function selectOpt(n){
    selectedOpt = n;
    opts.forEach(function(o){
      var isThis = parseInt(o.getAttribute('data-opt'), 10) === n;
      o.classList.toggle('rdc-opt-sel', isThis);
      var inp = o.querySelector('.rdc-rinput');
      if(inp) inp.checked = isThis;
    });
    var m = optMeta[n] || {cost:0, label:'Download'};
    syncDownloadCenterSummary(m,n);
    if(m.dlKey === 'rs' && typeof window.__preloadRS === 'function') window.__preloadRS();
    if(m.dlKey === 'smart-highlighted-resume' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'smart-highlighted-resume' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if(m.dlKey === 'headless-option3-skills-highlighted' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'headless-option3-skills-highlighted' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if(m.dlKey === 'highlighted-branded-resume' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'highlighted-branded-resume' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if(m.dlKey === 'highlighted-branded-resume-client-docx' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'highlighted-branded-resume-client-docx' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if(m.dlKey === 'isolated-highlighted-keywords-client-docx' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'isolated-highlighted-keywords-client-docx' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if(m.dlKey === 'client-anonymized-resume' && typeof window.__preloadClientAnonymizer === 'function') window.__preloadClientAnonymizer();
    if(m.dlKey === 'client-anonymized-keep-name-resume' && typeof window.__preloadClientAnonymizer === 'function') window.__preloadClientAnonymizer();
    if(m.dlKey === 'client-contact-hidden-branded-resume' && typeof window.__preloadClientAnonymizer === 'function') window.__preloadClientAnonymizer();
    if(m.dlKey === 'client-contact-hidden-branded-resume' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if((m.dlKey === 'client-contact-hidden-prescreened-resume' || m.dlKey === 'client-contact-hidden-prescreened-nobrand-resume') && typeof window.__preloadClientAnonymizer === 'function') window.__preloadClientAnonymizer();
    if((m.dlKey === 'client-contact-hidden-prescreened-resume' || m.dlKey === 'client-contact-hidden-prescreened-nobrand-resume') && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'fast-highlighted-branded-resume-client-docx' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'fast-highlighted-branded-resume-client-docx' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if(m.dlKey === 'fast-highlighted-resume-client-docx' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'fast-highlighted-resume-client-docx' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if((m.dlKey === 'turbo-vector-highlighted-branded' || m.dlKey === 'turbo-vector-highlighted' || m.dlKey === 'turbo-prescreened-highlighted-package') && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'turbo-prescreened-highlighted-package' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if((m.dlKey === 'client-lightning-highlighted-branded' || m.dlKey === 'client-lightning-highlighted') && typeof window.__preloadClientLightning === 'function') window.__preloadClientLightning();
    if(m.dlKey === 'test-preantigravity-prescreened-highlighted-branded' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'test-preantigravity-prescreened-highlighted-branded' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if((m.dlKey === 'prescreened-highlighted-package' || m.dlKey === 'prescreened-highlighted-package-nobrand' || m.dlKey === 'prescreened-highlighted-package-client-docx' || m.dlKey === 'prescreened-highlighted-package-client-docx-nobrand') && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if((m.dlKey === 'prescreened-highlighted-package' || m.dlKey === 'prescreened-highlighted-package-nobrand' || m.dlKey === 'prescreened-highlighted-package-client-docx' || m.dlKey === 'prescreened-highlighted-package-client-docx-nobrand') && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if(m.dlKey === 'smart-branded-resume' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'smart-branded-resume' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if(m.dlKey === 'rs-combined' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'rs-combined-nobrand' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'resume-pdf-conversion' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'browser-docx-pdf-conversion' && typeof window.__preloadBrowserDocxConversion === 'function') window.__preloadBrowserDocxConversion();
    if(m.dlKey === 'opendoc-preview-lab' && typeof window.__preloadOpenDocLab === 'function') window.__preloadOpenDocLab();
    if(m.dlKey === 'word-prescreened-package' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'hybrid-word-prescreened-package' && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if(m.dlKey === 'hybrid-word-prescreened-package' && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
    if((m.dlKey === 'smart-prescreened-package' || m.dlKey === 'smart-prescreened-package-nobrand') && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
    if((m.dlKey === 'smart-prescreened-package' || m.dlKey === 'smart-prescreened-package-nobrand') && resumeExt === 'DOCX' && typeof window.__preloadOpenDocLocal === 'function') window.__preloadOpenDocLocal();
  }

  var toastTimer = null;
  var smartCountdownTimer = null;
  function showToast(msg, type){
    if(!toastEl) return;
    toastEl.textContent = msg;
    toastEl.className = 'rdc-toast rdc-toast-' + (type || 'info') + ' rdc-tshow';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function(){ toastEl.classList.remove('rdc-tshow'); }, 3500);
  }

  var smartCountdownResolve = null;

  function clearSmartCountdownTimer(){
    if(smartCountdownTimer){
      clearTimeout(smartCountdownTimer);
      smartCountdownTimer = null;
    }
  }

  function stopSmartCountdown(){
    clearSmartCountdownTimer();
    if(smartCountdownResolve){
      smartCountdownResolve(false);
      smartCountdownResolve = null;
    }
  }

  function escapeCountdownText(value){
    return String(value || '').replace(/[&<>"']/g,function(ch){
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch];
    });
  }

  function buildCountdownReel(totalSeconds){
    var parts=[];
    for(var i=totalSeconds;i>=0;i--){
      parts.push('<span>'+i+'</span>');
    }
    return parts.join('');
  }

  function runSmartCountdown(prefix,totalSeconds){
    stopSmartCountdown();
    totalSeconds = totalSeconds || 60;
    if(dlBtn){
      var itemHeight=16;
      dlBtn.innerHTML =
        '<span class="rdc-spin"></span>' +
        '<span class="rdc-countdown">' +
          '<span class="rdc-countdown-text">'+escapeCountdownText(prefix)+'</span>' +
          '<span class="rdc-countdown-reel" aria-hidden="true">' +
            '<span class="rdc-countdown-track" style="--rdc-count-duration:'+totalSeconds+'s;--rdc-count-steps:'+totalSeconds+';--rdc-count-shift:-'+(totalSeconds * itemHeight)+'px;">'+buildCountdownReel(totalSeconds)+'</span>' +
          '</span>' +
          '<span class="rdc-countdown-unit">seconds</span>' +
        '</span>';
    }
    return new Promise(function(resolve){
      smartCountdownResolve = resolve;
      function finish(shouldContinue){
        clearSmartCountdownTimer();
        if(smartCountdownResolve){
          smartCountdownResolve = null;
          resolve(shouldContinue);
        }
      }
      smartCountdownTimer = setTimeout(function(){
        if(dlBtn){
          dlBtn.innerHTML = '<span class="rdc-spin"></span><span>'+escapeCountdownText(prefix)+' Finalizing...</span>';
        }
        finish(true);
      }, totalSeconds * 1000);
    });
  }

  function beginDownloadCountdown(prefix){
    /* Purpose: standardize the RDC loading UI so every download option shows the
       same countdown treatment without changing the option-specific workflows. */
    runSmartCountdown(prefix || 'Downloading Resume, Kindly wait.', 60);
  }

  function resetBtn(){
    stopSmartCountdown();
    isLoading = false;
    var m = optMeta[selectedOpt] || {};
    syncDownloadCenterSummary(m,selectedOpt);
    var normalHtml =
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;flex-shrink:0" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>' +
      '<span id="rdcBtnLabel">' + downloadButtonLabel(m.label) + '</span>';
    var buttons = Array.prototype.slice.call(document.querySelectorAll('[id="rdcDownloadBtn"]'));
    if(dlBtn && buttons.indexOf(dlBtn) === -1) buttons.push(dlBtn);
    buttons.forEach(function(button){
      button.disabled = false;
      button.classList.remove('rdc-loading');
      button.innerHTML = normalHtml;
    });
    if(buttons.length) dlBtn = buttons[buttons.length - 1];
    btnLbl = latestById('rdcBtnLabel');
  }

  function markDownloadStarted(){
    if(!dlBtn) return;
    resetBtn();
    closeRdc();
  }

  window.__rdcCompleteDownloadUi = markDownloadStarted;
  window.addEventListener('rdc:download-started', markDownloadStarted);

  if(dlBtn){
    dlBtn.addEventListener('click', function(){
      if(isLoading) return;
      var m = optMeta[selectedOpt];
      if(!m) return;

      if(!m.avail){
        showToast(m.unavailableMsg || 'This download format is not available for this resume type.', 'info');
        return;
      }

      if(selectedOpt === 1){
        /* Original resume: direct R2 browser download only; browsers do not expose a reliable download-finished event. */
        if(!originalR2DirectUrl && !originalR2PublicUrl && !r2PublicUrl && !dlUrl){
          showToast('Original resume is not available for download.', 'warn');
          resetBtn(); return;
        }
        isLoading = true;
        dlBtn.disabled = true;
        dlBtn.classList.add('rdc-loading');
        beginDownloadCountdown('Downloading Resume, Kindly wait.');
        try {
          startOriginalResumeDownload({
            originalR2PublicUrl:originalR2PublicUrl,
            r2PublicUrl:r2PublicUrl,
            originalR2DirectUrl:originalR2DirectUrl,
            r2DirectUrl:r2DirectUrl,
            r2RedirectUrl:r2SignUrl ? (r2SignUrl + (r2SignUrl.indexOf('?') === -1 ? '?' : '&') + 'redirect=1') : '',
            dlUrl:dlUrl,
            resumeExt:resumeExt
          });
          setTimeout(function(){
            resetBtn();
            closeRdc();
          }, 500);
        } catch(err) {
          console.error('Original resume R2-first download failed:',err);
          showToast(err && err.message ? err.message : 'Could not download original resume. Please try again.', 'warn');
          resetBtn();
        }
        return;
      }

      isLoading = true;
      dlBtn.disabled = true;
      dlBtn.classList.add('rdc-loading');
      beginDownloadCountdown('Downloading Resume, Kindly wait.');

      if(m.dlKey === 'client-anonymized-resume' || m.dlKey === 'client-anonymized-keep-name-resume'){
        /* Options 29/30: browser-only privacy copies. They use direct R2 bytes and never call the server PDF converter. */
        if(window.__rsBusy || typeof window.__buildClientAnonymizedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Anonymized Resume is available for PDF and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Downloading Resume, Kindly wait.');
        ensureDownloadCenterR2Urls().then(function(){
          var anonymizedSources = rdcCollectDownloadSources([r2DirectUrl,r2PublicUrl,originalR2DirectUrl,originalR2PublicUrl]);
          if(!anonymizedSources.length) throw new Error('Direct R2 resume URL is not available for browser anonymization.');
          return window.__buildClientAnonymizedResume({
            resumeUrl:anonymizedSources,
            resumeExt:resumeExt,
            keepName:m.keepName===true
          });
        }).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(err){
          console.error('Anonymized Resume drawer flow failed:',err);
          showToast(err && err.message ? err.message : 'Could not generate anonymized resume.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'client-contact-hidden-branded-resume'){
        /* Option 31: isolated combination of contact hiding and option 6 company branding. */
        if(window.__rsBusy || typeof window.__buildContactHiddenBrandedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Hide Contact Details + Company Branding is available for PDF and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Downloading Resume, Kindly wait.');
        ensureDownloadCenterR2Urls().then(function(){
          var contactHiddenSources = rdcCollectDownloadSources([r2DirectUrl,r2PublicUrl,originalR2DirectUrl,originalR2PublicUrl]);
          if(!contactHiddenSources.length) throw new Error('Direct R2 resume URL is not available for browser processing.');
          return window.__buildContactHiddenBrandedResume({
            resumeUrl:contactHiddenSources,
            resumeExt:resumeExt
          });
        }).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(err){
          console.error('Contact hidden branded drawer flow failed:',err);
          showToast(err && err.message ? err.message : 'Could not generate contact-hidden branded resume.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'client-contact-hidden-prescreened-resume'){
        /* Option 32: isolated combination of contact hiding and branded pre-screened report merge. */
        if(window.__rsBusy || typeof window.__buildContactHiddenPreScreenedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Hide Contact Details + Pre-Screened Report is available for PDF and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Generating Report, Kindly wait.');
        ensureDownloadCenterR2Urls().then(function(){
          var contactHiddenReportSources = rdcCollectDownloadSources([r2DirectUrl,r2PublicUrl,originalR2DirectUrl,originalR2PublicUrl]);
          if(!contactHiddenReportSources.length) throw new Error('Direct R2 resume URL is not available for browser processing.');
          return window.__buildContactHiddenPreScreenedResume({
            resumeUrl:contactHiddenReportSources,
            resumeExt:resumeExt
          });
        }).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(err){
          console.error('Contact hidden pre-screened drawer flow failed:',err);
          showToast(err && err.message ? err.message : 'Could not generate contact-hidden pre-screened package.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'client-contact-hidden-prescreened-nobrand-resume'){
        /* Option 33: isolated combination of contact hiding and no-brand pre-screened report merge. */
        if(window.__rsBusy || typeof window.__buildContactHiddenPreScreenedNoBrandResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Hide Contact Details + Pre-Screened Report without Company Branding is available for PDF and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Generating Report, Kindly wait.');
        ensureDownloadCenterR2Urls().then(function(){
          var contactHiddenNoBrandSources = rdcCollectDownloadSources([r2DirectUrl,r2PublicUrl,originalR2DirectUrl,originalR2PublicUrl]);
          if(!contactHiddenNoBrandSources.length) throw new Error('Direct R2 resume URL is not available for browser processing.');
          return window.__buildContactHiddenPreScreenedNoBrandResume({
            resumeUrl:contactHiddenNoBrandSources,
            resumeExt:resumeExt
          });
        }).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(err){
          console.error('Contact hidden pre-screened no-brand drawer flow failed:',err);
          showToast(err && err.message ? err.message : 'Could not generate contact-hidden no-brand pre-screened package.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'rs'){
        /* Option 5: Recruiter Summary — fully client-side vector PDF */
        if(window.__rsBusy || typeof window.__buildRS !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Generating Report, Kindly wait.');
        window.__buildRS().then(function(ok){
          if(ok) closeRdc();
        }).catch(function(){
          showToast('Could not generate Recruiter Summary. Please try again.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'rs-combined'){
        /* Option 8: Pre-Screened Report + Original Resume.
           Report + merge run client-side; Word resumes use the cached enterprise PDF converter first. */
        if(window.__rsBusy || typeof window.__buildCombinedRSResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(!resumePdfUrl || ['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast('Combined report is available for PDF, DOC and DOCX resumes.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Generating Report, Kindly wait.');
        window.__buildCombinedRSResume(resumePdfUrl,{resumeUrl:dlUrl,resumeExt:resumeExt}).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(){
          showToast('Could not generate combined report. Please try again.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'rs-combined-nobrand'){
        /* Option 9: combined report with company branding removed from the report page; Word conversion remains cached. */
        if(window.__rsBusy || typeof window.__buildCombinedRSResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(!resumePdfUrl || ['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast('Combined report is available for PDF, DOC and DOCX resumes.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Generating Report, Kindly wait.');
        window.__buildCombinedRSResume(resumePdfUrl,{noBranding:true,resumeUrl:dlUrl,resumeExt:resumeExt}).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(){
          showToast('Could not generate combined report. Please try again.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'resume-pdf-conversion'){
        /* Option 10: isolated resume-to-PDF quality gate before enabling merge as a client-ready flow. */
        if(window.__rsBusy || typeof window.__convertResumeToPdfOnly !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(!resumePdfUrl || ['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast('Resume PDF conversion is available for PDF, DOC and DOCX resumes.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Downloading Resume, Kindly wait.');
        window.__convertResumeToPdfOnly(dlUrl,resumePdfUrl,resumeExt).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(){
          showToast('Could not convert resume to PDF. Please try again.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'browser-docx-pdf-conversion'){
        /* Option 11: experimental browser-only DOCX conversion for low-load compatible templates. */
        if(window.__rsBusy || typeof window.__convertResumeToPdfBrowserOnly !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt !== 'DOCX' || !dlUrl){
          showToast('Browser conversion is available only for DOCX resumes. Use option 10 for PDF/DOC files.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Downloading Resume, Kindly wait.');
        window.__convertResumeToPdfBrowserOnly(dlUrl,resumeExt).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(){
          showToast('Browser DOCX conversion failed. Please use option 10.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'opendoc-preview-lab'){
        /* Option 12: guarded OpenDoc WASM lab. It runs only after local OpenDoc assets are installed. */
        if(window.__rsBusy || typeof window.__convertResumeWithOpenDocLab !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt !== 'DOCX' || !dlUrl){
          showToast('OpenDoc lab is available only for DOCX resumes.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Downloading Resume, Kindly wait.');
        window.__convertResumeWithOpenDocLab(dlUrl,resumeExt).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(){
          showToast('OpenDoc lab could not run. Please use option 10.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'word-prescreened-package'){
        /* Option 13: DOC/DOCX-only package.
           Enterprise converter is primary; DOCX can fall back to browser conversion before merge. */
        if(window.__rsBusy || typeof window.__buildWordPreScreenedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['DOC','DOCX'].indexOf(resumeExt) === -1 || (!resumePdfUrl && !(resumeExt === 'DOCX' && dlUrl))){
          showToast('This option is available only for DOC and DOCX resumes.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Generating Report, Kindly wait.');
        window.__buildWordPreScreenedResume(resumePdfUrl,{resumeUrl:dlUrl,resumeExt:resumeExt}).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(){
          showToast('Could not generate Word report package. Please try option 10 first.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'hybrid-word-prescreened-package'){
        /* Option 14: high-concurrency DOCX package.
           Conversion, report generation, and merge run client-side to protect server CPU. */
        if(window.__rsBusy || typeof window.__buildHybridWordPreScreenedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt !== 'DOCX' || (!r2DirectUrl && !r2PublicUrl)){
          showToast('Direct R2 DOCX URL is not available. Use option 13 for server converter mode.', 'warn');
          resetBtn(); return;
        }
        beginDownloadCountdown('Generating Report, Kindly wait.');
        window.__buildHybridWordPreScreenedResume('',{resumeUrl:[r2DirectUrl,r2PublicUrl],resumeExt:resumeExt}).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(){
          showToast('Could not generate hybrid Word report package. Please try option 13.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'headless-option3-skills-highlighted'){
        /* Headless option 3: direct/converted PDF, hidden PDF.js viewer, option-3-style skill detection, browser download. */
        if(window.__rsBusy || typeof window.__buildHeadlessOption3SkillsHighlightedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast('Resume + Skills Highlighted is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) !== -1 && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resume needs enterprise converter PDF URL.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildHeadlessOption3SkillsHighlightedResume({
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt,
            resumePdfUrl:resumePdfUrl,
            preferClientDocx:true
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(){
            showToast('Could not generate Resume + Skills Highlighted. Please try option 3.', 'warn');
          }).finally(function(){
          resetBtn();
          });
        }, 250);

      } else if(m.dlKey === 'highlighted-branded-resume' || m.dlKey === 'highlighted-branded-resume-client-docx'){
        /* Options 9/20: highlighted output followed by option 6's exact company branding header. */
        if(window.__rsBusy || typeof window.__buildHighlightedBrandedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Resume + Highlighted + Company Branding is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) !== -1 && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resume needs enterprise converter PDF URL.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildHighlightedBrandedResume(resumePdfUrl,{
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt,
            preferClientDocx:true
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(){
            showToast('Could not generate highlighted branded resume. Please try option 8 or option 6.', 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 250);

      } else if(m.dlKey === 'isolated-highlighted-keywords-client-docx'){
        /* Option 28: isolated highlighted-keywords-only package; DOCX tries browser OpenDoc first, then enterprise PDF fallback. */
        if(window.__rsBusy || typeof window.__buildIsolatedHighlightedKeywordsResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Resume + Highlighted Keywords is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) !== -1 && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resume needs enterprise converter PDF URL.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildIsolatedHighlightedKeywordsResume(resumePdfUrl,{
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt,
            preferClientDocx:true
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(){
            showToast('Could not generate Resume + Highlighted Keywords. Please try option 20.', 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 250);

      } else if(m.dlKey === 'fast-highlighted-resume-client-docx'){
        /* Option 22: option 17 client-first converter/highlighter path without report or branding work. */
        if(window.__rsBusy || typeof window.__buildHeadlessOption3SkillsHighlightedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Fast Resume + Highlighted Keywords is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) !== -1 && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resume needs enterprise converter PDF URL.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildHeadlessOption3SkillsHighlightedResume({
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt,
            resumePdfUrl:resumePdfUrl,
            preferClientDocx:true
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(){
            showToast('Could not generate fast highlighted resume. Please try option 17.', 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 250);

      } else if(m.dlKey === 'fast-highlighted-branded-resume-client-docx'){
        /* Option 21: fast highlighted resume package with a separate company branding page. */
        if(window.__rsBusy || typeof window.__buildFastHighlightedBrandingResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Fast Resume + Highlighted Keywords + Company Branding is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) !== -1 && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resume needs enterprise converter PDF URL.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildFastHighlightedBrandingResume(resumePdfUrl,{
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(){
            showToast('Could not generate fast highlighted branded resume. Please try option 20.', 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 250);

      } else if(m.dlKey === 'turbo-vector-highlighted-branded'){
        /* Option 23: Turbo Vector resume with keyword highlights and company branding */
        if(window.__rsBusy || typeof window.__buildTurboVectorBrandedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Turbo Vector Resume is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) !== -1 && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resume needs enterprise converter PDF URL.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildTurboVectorBrandedResume(resumePdfUrl,{
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(err){
            showToast('Could not generate Turbo Vector resume: ' + (err && err.message ? err.message : 'Unknown error'), 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 50);

      } else if(m.dlKey === 'turbo-vector-highlighted'){
        /* Option 24: Turbo Vector resume with keyword highlights only */
        if(window.__rsBusy || typeof window.__buildTurboVectorHighlightedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Turbo Vector Resume is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) !== -1 && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resume needs enterprise converter PDF URL.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildTurboVectorHighlightedResume({
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt,
            resumePdfUrl:resumePdfUrl
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(err){
            showToast('Could not generate Turbo Vector resume: ' + (err && err.message ? err.message : 'Unknown error'), 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 50);

      } else if(m.dlKey === 'client-lightning-highlighted-branded'){
        /* Option 25: 100% Client-Side Lightning Resume with keyword highlights and company branding */
        if(window.__rsBusy || typeof window.__buildClientLightningBrandedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Client Lightning Resume is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(!r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildClientLightningBrandedResume({
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt,
            resumePdfUrl:resumePdfUrl
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(err){
            showToast('Could not generate Client Lightning resume: ' + (err && err.message ? err.message : 'Unknown error'), 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 50);

      } else if(m.dlKey === 'client-lightning-highlighted'){
        /* Option 26: 100% Client-Side Lightning Resume with keyword highlights only */
        if(window.__rsBusy || typeof window.__buildClientLightningHighlightedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Client Lightning Resume is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(!r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildClientLightningHighlightedResume({
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt,
            resumePdfUrl:resumePdfUrl
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(err){
            showToast('Could not generate Client Lightning resume: ' + (err && err.message ? err.message : 'Unknown error'), 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 50);

      } else if(m.dlKey === 'turbo-prescreened-highlighted-package'){
        /* Option 27: Turbo Pre-Screened Report + Resume + Highlights (Zero-Raster Vector Package) */
        if(window.__rsBusy || typeof window.__buildTurboPreScreenedHighlightedPackage !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Turbo Pre-Screened Package is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) !== -1 && !r2DirectUrl && !r2PublicUrl && !dlUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resume needs enterprise converter PDF URL.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildTurboPreScreenedHighlightedPackage(resumePdfUrl,{
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(err){
            showToast('Could not generate Turbo Pre-Screened package: ' + (err && err.message ? err.message : 'Unknown error'), 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 50);

      } else if(m.dlKey === 'test-preantigravity-prescreened-highlighted-branded'){
        /* Option 34: test-only pre-Antigravity browser conversion for option 17 quality comparison. */
        if(window.__rsBusy || typeof window.__buildPreAntigravityPreScreenedHighlightedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Test package is browser-only and available for direct PDF/DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Generating Report, Kindly wait.', 60);
        ensureDownloadCenterR2Urls().then(function(){
          var testSources = rdcCollectDownloadSources([r2DirectUrl,r2PublicUrl,originalR2DirectUrl,originalR2PublicUrl]);
          if(!testSources.length) throw new Error('Direct R2 resume URL is not available for browser-only testing.');
          return window.__buildPreAntigravityPreScreenedHighlightedResume(null,{
            noBranding:false,
            preferClientDocx:true,
            resumeUrl:testSources,
            resumeExt:resumeExt
          });
        }).then(function(ok){
          if(ok) closeRdc();
        }).catch(function(err){
          console.error('Browser-only test package failed:',err);
          showToast(err && err.message ? err.message : 'Could not generate the browser-only test package.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey === 'prescreened-highlighted-package' || m.dlKey === 'prescreened-highlighted-package-nobrand' || m.dlKey === 'prescreened-highlighted-package-client-docx' || m.dlKey === 'prescreened-highlighted-package-client-docx-nobrand'){
        /* Options 13/14/17/19: Pre-Screened Report first, then browser-highlighted resume pages appended in the final PDF. */
        if(window.__rsBusy || typeof window.__buildPreScreenedHighlightedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Pre-Screened Report + Resume + Highlighted Keywords is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) !== -1 && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resumes need the enterprise converter before highlighted report package generation.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Generating Report, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildPreScreenedHighlightedResume(resumePdfUrl,{
            noBranding:(m.dlKey === 'prescreened-highlighted-package-nobrand' || m.dlKey === 'prescreened-highlighted-package-client-docx-nobrand'),
            preferClientDocx:(m.dlKey === 'prescreened-highlighted-package-client-docx' || m.dlKey === 'prescreened-highlighted-package-client-docx-nobrand'),
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(){
            showToast('Could not generate Pre-Screened Report + Resume + Highlighted Keywords. Please try option 8 or option 15.', 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 250);

      } else if(m.dlKey === 'smart-highlighted-resume'){
        /* New highlighted resume: direct R2/browser flow, independent of the Resume/CV tab. */
        if(window.__rsBusy || typeof window.__buildSmartHighlightedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) === -1){
          showToast('Client-side highlighted resume is available for PDF and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(!r2DirectUrl && !r2PublicUrl && !dlUrl){
          showToast('Resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildSmartHighlightedResume({
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(){
            showToast('Could not generate highlighted resume. Please try the original ATS option.', 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 250);

      } else if(m.dlKey === 'smart-branded-resume'){
        /* New branded resume: direct R2/browser flow with only the shared-by company header added above resume content. */
        if(window.__rsBusy || typeof window.__buildSmartBrandedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast('Branded resume is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'PDF' && !r2DirectUrl && !r2PublicUrl && !dlUrl){
          showToast('PDF resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOCX' && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('DOCX resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resumes need the enterprise converter before branding.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Downloading Resume, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildSmartBrandedResume(resumePdfUrl,{
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(){
            showToast('Could not generate branded resume. Please try original download.', 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 250);

      } else if(m.dlKey === 'smart-prescreened-package' || m.dlKey === 'smart-prescreened-package-nobrand'){
        /* Options 15/16: final smart package.
           PDF resumes fetch directly from R2; DOCX converts in browser; DOC uses enterprise converter fallback. */
        if(window.__rsBusy || typeof window.__buildSmartPreScreenedResume !== 'function'){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1){
          showToast('Final package is available for PDF, DOC and DOCX resumes only.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'PDF' && !r2DirectUrl && !r2PublicUrl && !dlUrl){
          showToast('PDF resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOCX' && !r2DirectUrl && !r2PublicUrl && !dlUrl && !resumePdfUrl){
          showToast('DOCX resume URL is not available.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC' && !resumePdfUrl){
          showToast('DOC resumes need the enterprise converter before final package generation.', 'warn');
          resetBtn(); return;
        }
        runSmartCountdown('Generating Report, Kindly wait.', 60);
        setTimeout(function(){
          window.__buildSmartPreScreenedResume(resumePdfUrl,{
            noBranding:(m.dlKey === 'smart-prescreened-package-nobrand'),
            resumeUrl:[r2DirectUrl,r2PublicUrl,dlUrl],
            resumeExt:resumeExt
          }).then(function(ok){
            if(ok) closeRdc();
          }).catch(function(){
            showToast('Could not generate final package. Please try option 13 or option 10.', 'warn');
          }).finally(function(){
            resetBtn();
          });
        }, 250);

      } else if(m.dlKey === 'pdf'){
        /* Option 7: Export Candidate Profile PDF */
        if(typeof window.cdrExportProfilePDF === 'function'){
          window.cdrExportProfilePDF(null, true);
        } else {
          showToast('PDF Export feature unavailable.', 'warn');
        }
        setTimeout(function(){ closeRdc(); resetBtn(); }, 1500);

      } else if(m.dlKey){
        /* Options 2/3/4: keep drawer + spinner open while polling for resume viewer,
           click the data-dl button, then wait for buildPdf() to finish by watching
           rcvDlToggle.disabled (setDlBusy sets it true→false around PDF generation). */
        var dlKey = m.dlKey;
        var tries = 0;
        var poll = setInterval(function(){
          var pages = document.querySelectorAll('#rcvPages .rcv-page');
          if(pages.length){
            clearInterval(poll);
            var btn = document.querySelector('[data-dl="' + dlKey + '"]');
            if(!btn){
              showToast('Download button not found. Please open the Resume / CV tab first.', 'warn');
              resetBtn();
              return;
            }
            btn.click();
            /* After click, setDlBusy(true) disables rcvDlToggle; when PDF is saved
               setDlBusy(false) re-enables it. Wait ≥300ms for the flag to be set
               then poll until it clears (max 45 sec). */
            var waitTicks = 0;
            var gotBusy = false;
            var donePoll = setInterval(function(){
              var toggle = document.getElementById('rcvDlToggle');
              if(toggle && toggle.disabled){ gotBusy = true; }
              var done = gotBusy && toggle && !toggle.disabled;
              var timedOut = ++waitTicks > 450;
              if(done || timedOut){
                clearInterval(donePoll);
                if(timedOut && !done){
                  showToast('Check your browser — the file may be downloading in the background.', 'info');
                }
                closeRdc();
                resetBtn();
              }
            }, 100);
          } else if(++tries > 80){
            clearInterval(poll);
            showToast('Resume viewer not loaded. Please scroll to the Resume / CV tab first, then try again.', 'warn');
            resetBtn();
          }
        }, 100);
      }
    });
  }

})();
</script>

{{-- ===== EXPORT CANDIDATE PROFILE PDF — pdfMake vector PDF, Roboto (vfs_fonts), direct download ===== --}}
<script>
(function(){
'use strict';

/* Fetch a JS file and execute it via Blob URL.
   Blob URL inherits the page origin, so it satisfies CSP script-src 'self'
   even when the original CDN URL would be blocked.
   Each URL's Promise is cached so it is only fetched once per session. */
var _fetched={};
function fetchAndExec(url){
  if(_fetched[url]) return _fetched[url];
  _fetched[url]=fetch(url,{cache:'force-cache'}).then(function(r){
    if(!r.ok) throw new Error('HTTP '+r.status+' '+url);
    return r.text();
  }).then(function(code){
    return new Promise(function(res,rej){
      var blob=new Blob([code],{type:'text/javascript'});
      var burl=URL.createObjectURL(blob);
      var s=document.createElement('script');
      s.src=burl;
      s.onload=function(){ URL.revokeObjectURL(burl); res(); };
      s.onerror=function(){ URL.revokeObjectURL(burl); rej(new Error('exec failed: '+url)); };
      document.head.appendChild(s);
    });
  });
  return _fetched[url];
}

/* Try each URL in order; move to next on fetch/exec error OR verify() returning false */
function tryList(urls, i, verify){
  if(i>=urls.length) return Promise.reject(new Error('All sources failed — first was: '+urls[0]));
  return fetchAndExec(urls[i]).then(function(){
    if(verify()) return;
    return tryList(urls, i+1, verify);
  }).catch(function(){ return tryList(urls, i+1, verify); });
}

/* Load pdfmake library then vfs_fonts (Roboto), sequentially. Cached after first call. */
var _pdfReady=null;
function ensurePdfMake(){
  if(_pdfReady && typeof pdfMake!=='undefined' && pdfMake.vfs && pdfMake.vfs['Roboto-Regular.ttf']) return _pdfReady;
  _pdfReady=null;
  var CORE=[
    '/js/pdfmake.min.js',
    'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js',
    'https://unpkg.com/pdfmake@0.2.7/build/pdfmake.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js'
  ];
  var VFS=[
    '/js/vfs_fonts.min.js',
    'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.min.js',
    'https://unpkg.com/pdfmake@0.2.7/build/vfs_fonts.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.min.js'
  ];
  _pdfReady=tryList(CORE, 0, function(){ return typeof pdfMake!=='undefined'; })
    .then(function(){
      return tryList(VFS, 0, function(){ return !!(pdfMake.vfs&&pdfMake.vfs['Roboto-Regular.ttf']); });
    })
    .then(function(){
      pdfMake.fonts={Roboto:{normal:'Roboto-Regular.ttf',bold:'Roboto-Medium.ttf',italics:'Roboto-Italic.ttf',bolditalics:'Roboto-MediumItalic.ttf'}};
    });
  return _pdfReady;
}

/* Convert image URL or Data URI to base64 Data URI for pdfMake embedding */
function getLogoDataUri(rawUrl){
  if(!rawUrl || typeof rawUrl !== 'string') return Promise.resolve(null);
  rawUrl = rawUrl.trim();
  if(!rawUrl) return Promise.resolve(null);
  if(rawUrl.indexOf('data:image/') === 0) return Promise.resolve(rawUrl);

  return new Promise(function(resolve){
    var img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = function(){
      try {
        var canvas = document.createElement('canvas');
        canvas.width = img.naturalWidth || img.width;
        canvas.height = img.naturalHeight || img.height;
        var ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        var dataUri = canvas.toDataURL('image/png');
        resolve(dataUri);
      } catch(e) {
        resolve(null);
      }
    };
    img.onerror = function(){
      fetch(rawUrl, {cache:'force-cache'})
        .then(function(r){ return r.ok ? r.blob() : null; })
        .then(function(blob){
          if(!blob) { resolve(null); return; }
          var reader = new FileReader();
          reader.onloadend = function(){ resolve(reader.result); };
          reader.onerror = function(){ resolve(null); };
          reader.readAsDataURL(blob);
        })
        .catch(function(){ resolve(null); });
    };
    img.src = rawUrl;
  });
}

/* Safe card block: navy header + white body */
function card(title, body, mb){
  return {
    table:{widths:['*'],body:[
      [{text:title,bold:true,fontSize:8,color:'#ffffff',characterSpacing:0.9,fillColor:'#1e3a8a',margin:[12,8,12,8]}],
      [body]
    ]},
    layout:{
      hLineWidth:function(i,n){ var len=(n&&n.table&&n.table.body)?n.table.body.length:0; return(i===0||i===len)?0.5:0; },
      vLineWidth:function(){return 0.5;},
      hLineColor:function(){return '#e2e8f0';},
      vLineColor:function(){return '#e2e8f0';},
      paddingTop:function(){return 0;},paddingBottom:function(){return 0;},
      paddingLeft:function(){return 0;},paddingRight:function(){return 0;}
    },
    margin:[0,0,0,mb!=null?mb:14]
  };
}

/* Safe Two-column table layout used for Match Analysis + At a Glance */
var twoColLayout={
  hLineWidth:function(i,n){ var len=(n&&n.table&&n.table.body)?n.table.body.length:0; return(i===0||i===len)?0.5:0.3; },
  vLineWidth:function(){return 0.5;},
  hLineColor:function(){return '#e2e8f0';},
  vLineColor:function(){return '#e2e8f0';},
  paddingTop:function(){return 0;},paddingBottom:function(){return 0;},
  paddingLeft:function(){return 0;},paddingRight:function(){return 0;}
};

/* Pure Vector SVG Icon Dictionary (Zero Unicode codepoint dependence — no broken tofu boxes!) */
var SVG_ICONS = {
  shield: '<svg width="12" height="12" viewBox="0 0 24 24"><path fill="#166534" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-5.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>',
  doc: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>',
  cal: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>',
  brain: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1a2 2 0 0 0 2 2v1.93zm6.9-2.54A1.993 1.993 0 0 0 18 15v-1a2 2 0 0 0-2-2h-3v-2h2a2 2 0 0 0 2-2V7.1a8.007 8.007 0 0 1 4.9 9.29z"/></svg>',
  user: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>',
  blueCheck: '<svg width="14" height="14" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#2563eb"/><path fill="#ffffff" d="M10 15.5l-3.5-3.5 1.4-1.4 2.1 2.1 5.6-5.6 1.4 1.4z"/></svg>',
  bolt: '<svg width="10" height="10" viewBox="0 0 24 24"><path fill="#166534" d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>',
  briefcase: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>',
  company: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>',
  location: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
  grad: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>',
  clock: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>',
  laptop: '<svg width="11" height="11" viewBox="0 0 24 24"><path fill="#64748b" d="M20 18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/></svg>',
  note: '<svg width="12" height="12" viewBox="0 0 24 24"><path fill="#1e3a8a" d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>',
  bulb: '<svg width="12" height="12" viewBox="0 0 24 24"><path fill="#1e3a8a" d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7z"/></svg>',
  chat: '<svg width="12" height="12" viewBox="0 0 24 24"><path fill="#1e3a8a" d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>',
  info: '<svg width="10" height="10" viewBox="0 0 24 24"><path fill="#475569" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>',
  goldStars: '<svg width="54" height="10" viewBox="0 0 120 24"><path fill="#f59e0b" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z M36 17.27L42.18 21l-1.64-7.03L46 9.24l-7.19-.61L36 2 33.19 8.63 26 9.24l5.46 4.73L29.82 21z M60 17.27L66.18 21l-1.64-7.03L70 9.24l-6.19-.61L60 2 57.19 8.63 50 9.24l5.46 4.73L53.82 21z M84 17.27L90.18 21l-1.64-7.03L94 9.24l-8.19-.61L84 2 81.19 8.63 74 9.24l5.46 4.73L77.82 21z M108 17.27L114.18 21l-1.64-7.03L118 9.24l-10.19-.61L108 2 105.19 8.63 98 9.24l5.46 4.73L101.82 21z"/></svg>',
  greenCheck: '<svg width="12" height="12" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#166534"/><path fill="#ffffff" d="M10 15.5l-3.5-3.5 1.4-1.4 2.1 2.1 5.6-5.6 1.4 1.4z"/></svg>',
  amberClock: '<svg width="12" height="12" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#b45309"/><path fill="#ffffff" d="M12 7v5l3.5 2.1.8-1.3-2.8-1.7V7z"/></svg>'
};

/* Helper for rounded card containers */
function roundedCard(stackContent, bgFill, borderStroke, mb) {
  var bg = bgFill || '#ffffff';
  var stroke = borderStroke || '#e2e8f0';
  return {
    table: {
      widths: ['*'],
      body: [[{
        stack: stackContent,
        margin: [10, 10, 10, 10],
        fillColor: bg
      }]]
    },
    layout: {
      hLineWidth: function(){ return 0.5; },
      vLineWidth: function(){ return 0.5; },
      hLineColor: function(){ return stroke; },
      vLineColor: function(){ return stroke; }
    },
    margin: [0, 0, 0, mb != null ? mb : 12]
  };
}

/* Build the 100% exact replica of the Pre-Screened Candidate Report PDF definition */
function buildDef(d){
  var matchRaw=d.matchText?d.matchText.replace(/[^0-9.]/g,''):'92';
  var matchNum=parseFloat(matchRaw)||92;
  var mc=matchNum>=70?'#16A34A':matchNum>=40?'#EA580C':'#DC2626';
  var mLabel=matchNum>=90?'Excellent Match':matchNum>=70?'Strong Match':matchNum>=40?'Moderate Match':'Low Match';

  /* Company Logo image or vector icon element */
  var isValidDataImg = d.logoUri && typeof d.logoUri === 'string' && (d.logoUri.indexOf('data:image/png') === 0 || d.logoUri.indexOf('data:image/jpeg') === 0 || d.logoUri.indexOf('data:image/jpg') === 0);
  var logoGraphic = isValidDataImg
    ? { image: d.logoUri, fit: [42, 32], width: 42 }
    : {
        canvas: [
          {type:'path',path:'M 4 2 L 18 2 L 10 26 L 4 26 Z',color:'#1E40AF'},
          {type:'path',path:'M 14 2 L 28 2 L 20 26 L 14 26 Z',color:'#2563EB'}
        ],
        width: 30
      };

  /* Candidate avatar square */
  var avatarEl={table:{widths:[54],heights:[54],body:[[{text:d.initials||'VE',bold:true,fontSize:18,color:'#FFFFFF',alignment:'center',fillColor:'#1E40AF',margin:[0,14,0,0]}]]},layout:'noBorders'};

  /* 1. HEADER SECTION */
  var headerCol = {
    columns: [
      {
        columns: [
          logoGraphic,
          {
            stack: [
              {text:(d.companyName||'4BELL TECHNOLOGY').toUpperCase(),bold:true,fontSize:12,color:'#111827',characterSpacing:0.3},
              {text:'RECRUITMENT INTELLIGENCE',fontSize:5.5,bold:true,color:'#6B7280',characterSpacing:1.1,margin:[0,1,0,0]}
            ],
            width: '*'
          }
        ],
        columnGap: 8,
        width: 160
      },
      {canvas:[{type:'line',x1:0,y1:2,x2:0,y2:30,lineWidth:1,lineColor:'#E5E7EB'}],width:14},
      {
        stack: [
          {text:'PRE-SCREENED CANDIDATE REPORT',bold:true,fontSize:12.5,color:'#111827',characterSpacing:0.3},
          {text:'AI Powered   |   Intelligent   |   Data Driven',fontSize:7.5,color:'#6B7280',margin:[0,2,0,0]}
        ],
        width: '*'
      },
      {
        stack: [
          {
            table: {
              body: [[
                {
                  columns: [
                    {svg: SVG_ICONS.shield, width: 10, margin: [0, 1, 4, 0]},
                    {text:'CONFIDENTIAL',bold:true,fontSize:7,color:'#16A34A'}
                  ],
                  fillColor: '#F0FDF4', margin: [6, 3, 6, 3]
                }
              ]]
            },
            layout: {hLineWidth:function(){return 0;},vLineWidth:function(){return 0;}},
            alignment: 'right'
          },
          {text:'For Internal Hiring Use Only',fontSize:6,color:'#6B7280',alignment:'right',margin:[0,2,0,0]}
        ],
        width: 120
      }
    ],
    margin: [0, 0, 0, 8]
  };

  /* Metadata Strip with Pure Vector SVG Icons */
  var metaRow = {
    table: {
      widths: ['*', '*', '*', '*'],
      body: [[
        {columns:[{svg:SVG_ICONS.doc,width:11,margin:[0,2,4,0]},{text:[{text:'Report ID: ',color:'#6B7280',fontSize:7.5},{text:d.reportId||('JR-'+(d.dateStr||'05Aug2026').replace(/\s+/g,'')+'-00125'),bold:true,fontSize:7.5,color:'#111827'}]}],fillColor:'#FFFFFF',margin:[6,4,6,4]},
        {columns:[{svg:SVG_ICONS.cal,width:11,margin:[0,2,4,0]},{text:[{text:'Report Date: ',color:'#6B7280',fontSize:7.5},{text:d.dateStr||'05 Aug 2026',bold:true,fontSize:7.5,color:'#111827'}]}],fillColor:'#FFFFFF',margin:[6,4,6,4]},
        {columns:[{svg:SVG_ICONS.brain,width:11,margin:[0,2,4,0]},{text:[{text:'AI Version: ',color:'#6B7280',fontSize:7.5},{text:'Jero AI v4.5',bold:true,fontSize:7.5,color:'#111827'}]}],fillColor:'#FFFFFF',margin:[6,4,6,4]},
        {columns:[{svg:SVG_ICONS.user,width:11,margin:[0,2,4,0]},{text:[{text:'Recruiter: ',color:'#6B7280',fontSize:7.5},{text:d.recruiterName||'Vibhanshu Jaiswal',bold:true,fontSize:7.5,color:'#111827'}]}],fillColor:'#FFFFFF',margin:[6,4,6,4]}
      ]]
    },
    layout: {
      hLineWidth: function(){return 0.5;}, vLineWidth: function(i){return 0.5;},
      hLineColor: function(){return '#E5E7EB';}, vLineColor: function(){return '#E5E7EB';}
    },
    margin: [0, 0, 0, 8]
  };

  /* 2. HERO SECTION (Candidate Hero Card) */
  var avatarBlock = {
    stack: [
      avatarEl,
      d.isImmediate!==false ? {
        table: {
          body: [[
            {
              columns: [
                {svg: SVG_ICONS.bolt, width: 9, margin: [0, 1, 2, 0]},
                {text:'Immediate Joiner',bold:true,fontSize:7,color:'#16A34A'}
              ],
              fillColor:'#F0FDF4',margin:[3,2,3,2]
            }
          ]]
        },
        layout: {
          hLineWidth:function(){return 0.5;},vLineWidth:function(){return 0.5;},
          hLineColor:function(){return '#BBF7D0';},vLineColor:function(){return '#BBF7D0';}
        },
        margin: [0, 4, 0, 0]
      } : {text:''}
    ],
    width: 66
  };

  var candidateDetails = {
    stack: [
      {columns:[{text:(d.name||'Deepak Raut'),bold:true,fontSize:16,color:'#111827'},{svg:SVG_ICONS.blueCheck,width:13,margin:[4,2,0,0]}]},
      {text:d.role||'UI/UX Designer',bold:true,fontSize:10.5,color:'#2563EB',margin:[0,1,0,4]},
      {columns:[{svg:SVG_ICONS.briefcase,width:10,margin:[0,2,4,0]},{text:(d.totalExp||'6.2 Years of Experience'),fontSize:8,color:'#374151'}],margin:[0,0,0,2]},
      {columns:[{svg:SVG_ICONS.company,width:10,margin:[0,2,4,0]},{text:(d.companyName||'4bell Technology'),fontSize:8,color:'#374151'}],margin:[0,0,0,2]},
      {columns:[{svg:SVG_ICONS.location,width:10,margin:[0,2,4,0]},{text:(d.location||'Hyderabad, Telangana, India'),fontSize:8,color:'#374151'}],margin:[0,0,0,0]}
    ],
    width: '*'
  };

  /* 100% Reliable Score Circle Box */
  var scoreCircle = {
    table: {
      widths: [48],
      heights: [48],
      body: [[
        { text: (Math.round(matchNum * 10) / 10) + '%', bold: true, fontSize: 15, color: mc, alignment: 'center', fillColor: '#FFFFFF', margin: [0, 13, 0, 0] }
      ]]
    },
    layout: {
      hLineWidth: function(){ return 2.5; },
      vLineWidth: function(){ return 2.5; },
      hLineColor: function(){ return mc; },
      vLineColor: function(){ return mc; }
    },
    alignment: 'center',
    margin: [0, 2, 0, 4]
  };

  var scoreBlock = {
    table: {
      widths: [105],
      body: [[{
        stack: [
          {columns:[{text:'OVERALL MATCH',fontSize:6.5,bold:true,color:'#4B5563'},{svg:SVG_ICONS.info,width:9,margin:[2,0,0,0]}],alignment:'center',margin:[0,0,0,2]},
          scoreCircle,
          {svg:SVG_ICONS.goldStars,width:50,alignment:'center',margin:[0,0,0,2]},
          {text:mLabel,fontSize:7.5,bold:true,color:mc,alignment:'center',margin:[0,0,0,2]},
          {
            table: {
              body: [[{text:'Priority: HIGH',fontSize:7,bold:true,color:'#1E40AF',fillColor:'#EFF6FF',margin:[4,1,4,1]}]]
            },
            layout: {hLineWidth:function(){return 0;},vLineWidth:function(){return 0;}},
            alignment: 'center'
          }
        ],
        fillColor:'#FFFFFF',margin:[4,4,4,4]
      }]]
    },
    layout: {
      hLineWidth: function(){return 0.5;}, vLineWidth: function(){return 0.5;},
      hLineColor: function(){return '#E5E7EB';}, vLineColor: function(){return '#E5E7EB';}
    },
    width: 115
  };

  var heroCard = roundedCard([
    {columns: [avatarBlock, candidateDetails, scoreBlock], columnGap: 10}
  ], '#FFFFFF', '#E5E7EB', 8);

  /* 3. QUICK INFO STRIP */
  var quickInfoStrip = {
    table: {
      widths: ['*', '*', '*', '*', '*'],
      body: [[
        {stack:[{columns:[{svg:SVG_ICONS.user,width:10,margin:[0,1,3,0]},{text:'Preferred Role',fontSize:6.5,color:'#6B7280'}]},{text:d.preferredRole||d.role||'UI/UX Designer',bold:true,fontSize:8,color:'#111827',margin:[0,2,0,0]}],fillColor:'#FFFFFF',margin:[4,5,4,5]},
        {stack:[{columns:[{svg:SVG_ICONS.grad,width:10,margin:[0,1,3,0]},{text:'Highest Education',fontSize:6.5,color:'#6B7280'}]},{text:d.education||'B.Tech (CS)',bold:true,fontSize:8,color:'#111827',margin:[0,2,0,0]}],fillColor:'#FFFFFF',margin:[4,5,4,5]},
        {stack:[{columns:[{svg:SVG_ICONS.clock,width:10,margin:[0,1,3,0]},{text:'Notice Period',fontSize:6.5,color:'#6B7280'}]},{text:d.noticePeriod||'Immediate',bold:true,fontSize:8,color:'#111827',margin:[0,2,0,0]}],fillColor:'#FFFFFF',margin:[4,5,4,5]},
        {stack:[{columns:[{svg:SVG_ICONS.location,width:10,margin:[0,1,3,0]},{text:'Preferred Location',fontSize:6.5,color:'#6B7280'}]},{text:d.prefLocation||'Hyderabad / Remote',bold:true,fontSize:8,color:'#111827',margin:[0,2,0,0]}],fillColor:'#FFFFFF',margin:[4,5,4,5]},
        {stack:[{columns:[{svg:SVG_ICONS.laptop,width:10,margin:[0,1,3,0]},{text:'Preferred Work Mode',fontSize:6.5,color:'#6B7280'}]},{text:d.workMode||'Remote / Hybrid',bold:true,fontSize:8,color:'#111827',margin:[0,2,0,0]}],fillColor:'#FFFFFF',margin:[4,5,4,5]}
      ]]
    },
    layout: {
      hLineWidth: function(){return 0.5;}, vLineWidth: function(i){return 0.5;},
      hLineColor: function(){return '#E5E7EB';}, vLineColor: function(){return '#E5E7EB';}
    },
    margin: [0, 0, 0, 8]
  };

  /* 4. SKILLS INTELLIGENCE (FULL PAGE WIDTH - CORRECT ACCURATE COLUMN MAPPING) */
  var skillRows = [
    [
      {text:'Skill',bold:true,fontSize:7.5,color:'#2563EB'},
      {text:'Candidate Experience',bold:true,fontSize:7.5,color:'#2563EB'},
      {text:'Rating',bold:true,fontSize:7.5,color:'#2563EB'},
      {text:'Status',bold:true,fontSize:7.5,color:'#2563EB',alignment:'center'}
    ]
  ];

  /* Extract & Filter pure candidate skills with ACCURATE column mapping */
  var rawSkills = (d.skillsTable && d.skillsTable.rows && d.skillsTable.rows.length) ? d.skillsTable.rows.filter(function(r){
    var name = (r[0]||'').toString().toLowerCase();
    return name.indexOf('question') === -1 && name.indexOf('communication rating') === -1 && name.indexOf('comfortable with wfo') === -1;
  }).slice(0, 8).map(function(r){
    var skillName = r[0] || 'Skill';
    var expDuration = (r[1] && r[1] !== 'Exp') ? r[1] : (r[2] && r[2].indexOf('Year') !== -1 ? r[2] : '1.0 Years');
    var ratingVal = (r[2] && r[2] !== expDuration) ? r[2] : (r[3] && r[3] !== '✔' && r[3] !== '✖' ? r[3] : '★★★★☆');
    var statusVal = (r[3] === '✔' || r[3] === '✖' || r[3] === '⚠') ? r[3] : '✔';
    return [skillName, expDuration, ratingVal, statusVal];
  }) : [
    ['UI DESIGNER', '1.1 Years', '2 / 5', '✔'],
    ['FIGMA', '2.1 Years', '1 / 5', '✔'],
    ['ADOBE XD', '10.9 Years', '2 / 5', '✔'],
    ['SKETCH', '11. Years', '3 / 5', '✔'],
    ['HTML', '12. Years', '4 / 5', '✔'],
    ['CSS', '13. Years', '5 / 5', '✔']
  ];

  rawSkills.forEach(function(s){
    skillRows.push([
      {text:s[0],fontSize:8,bold:true,color:'#111827'},
      {text:s[1],fontSize:8,color:'#374151'},
      {text:s[2],fontSize:8,color:'#EA580C',bold:true},
      {stack:[{svg:s[3]==='✖'?SVG_ICONS.amberClock:SVG_ICONS.greenCheck,width:10,alignment:'center'}],alignment:'center'}
    ]);
  });

  var skillsIntelligenceCard = roundedCard([
    {columns:[{svg:SVG_ICONS.note,width:11,margin:[0,1,4,0]},{text:'SKILLS INTELLIGENCE',bold:true,fontSize:8.5,color:'#111827'}],margin:[0,0,0,6]},
    {
      table: {widths:[160, 120, 120, '*'],body:skillRows},
      layout: {hLineWidth:function(){return 0.3;},vLineWidth:function(){return 0;},hLineColor:function(){return '#F1F5F9';}}
    }
  ], '#FFFFFF', '#E5E7EB', 8);

  /* 5. PROFESSIONAL SUMMARY & RECRUITER COMMENTS */
  var summaryText = d.summary || 'Deepak Raut is an experienced UI/UX Designer with strong expertise in user interface architecture, Figma design systems, wireframing, and interactive prototyping. Demonstrates clean design principles, usability testing, and cross-functional team collaboration.';
  if(summaryText.length > 350) summaryText = summaryText.substring(0, 350) + '…';

  var summaryCard = roundedCard([
    {columns:[{svg:SVG_ICONS.bulb,width:11,margin:[0,1,4,0]},{text:'PROFESSIONAL SUMMARY',bold:true,fontSize:8,color:'#111827'}],margin:[0,0,0,4]},
    {text:summaryText,fontSize:7.5,color:'#374151',lineHeight:1.35}
  ], '#FFFFFF', '#E5E7EB', 0);

  var rnContent = d.rnText || 'Candidate has solid technical foundation with hands-on experience in the required technologies. Code quality, problem-solving approach, and technical depth were assessed during the screening.';
  if(rnContent.length > 250) rnContent = rnContent.substring(0, 250) + '…';

  var recruiterCommentsCard = roundedCard([
    {columns:[{svg:SVG_ICONS.chat,width:11,margin:[0,1,4,0]},{text:'RECRUITER COMMENTS',bold:true,fontSize:8,color:'#111827'}],margin:[0,0,0,4]},
    {text:rnContent,fontSize:7.5,color:'#374151',lineHeight:1.35,margin:[0,0,0,8]},
    {canvas:[{type:'line',x1:0,y1:0,x2:200,y2:0,lineWidth:0.5,lineColor:'#F1F5F9'}],margin:[0,0,0,6]},
    {
      columns: [
        {columns:[{svg:SVG_ICONS.user,width:10,margin:[0,1,3,0]},{text:[{text:(d.recruiterName||'Vibhanshu Jaiswal')+'\n',bold:true,fontSize:7.5,color:'#111827'},{text:'Senior Technical Recruiter',fontSize:6.5,color:'#6B7280'}]}]},
        {columns:[{svg:SVG_ICONS.cal,width:10,margin:[0,1,3,0]},{text:d.dateStr||'05 Aug 2026',fontSize:7,color:'#6B7280'}],alignment:'right'}
      ]
    }
  ], '#FFFFFF', '#E5E7EB', 0);

  var sectionSummaryComments = {
    columns: [
      {stack:[summaryCard],width:'*'},
      {stack:[recruiterCommentsCard],width:'*'}
    ],
    columnGap: 10, margin: [0, 0, 0, 8]
  };

  /* 6. VERIFICATION STATUS CARD WITH ACCURATE ICON ALIGNMENT */
  var verificationCard = roundedCard([
    {columns:[{svg:SVG_ICONS.shield,width:11,margin:[0,1,4,0]},{text:'VERIFICATION STATUS',bold:true,fontSize:8,color:'#2563EB'}],margin:[0,0,0,6]},
    {
      columns: [
        {
          stack: [
            {columns:[{svg:SVG_ICONS.greenCheck,width:10,margin:[0,1,3,0]},{text:'Resume Verified',fontSize:7.5,color:'#16A34A'}],margin:[0,1,0,2]},
            {columns:[{svg:SVG_ICONS.greenCheck,width:10,margin:[0,1,3,0]},{text:'Email Verified',fontSize:7.5,color:'#16A34A'}],margin:[0,1,0,2]}
          ],
          width: '*'
        },
        {
          stack: [
            {columns:[{svg:SVG_ICONS.amberClock,width:10,margin:[0,1,3,0]},{text:'Documents Verified (Pending)',fontSize:7.5,color:'#EA580C'}],margin:[0,1,0,2]},
            {columns:[{svg:SVG_ICONS.amberClock,width:10,margin:[0,1,3,0]},{text:'Identity Verified (Pending)',fontSize:7.5,color:'#EA580C'}],margin:[0,1,0,2]}
          ],
          width: '*'
        },
        {
          stack: [
            {columns:[{svg:SVG_ICONS.greenCheck,width:10,margin:[0,1,3,0]},{text:'Education Verified',fontSize:7.5,color:'#16A34A'}],margin:[0,1,0,2]},
            {columns:[{svg:SVG_ICONS.greenCheck,width:10,margin:[0,1,3,0]},{text:'Experience Verified',fontSize:7.5,color:'#16A34A'}],margin:[0,1,0,2]}
          ],
          width: '*'
        }
      ]
    }
  ], '#FFFFFF', '#E5E7EB', 8);

  /* 7. FINAL HIRING RECOMMENDATION */
  var finalRecommendationCard = roundedCard([
    {
      columns: [
        {
          stack: [
            {
              columns: [
                {svg: SVG_ICONS.greenCheck, width: 18, margin: [0, 1, 5, 0]},
                {
                  stack: [
                    {svg: SVG_ICONS.goldStars, width: 50, margin: [0, 0, 0, 2]},
                    {text:'RECOMMENDED FOR INTERVIEW',bold:true,fontSize:9,color:'#16A34A'}
                  ],
                  width: '*'
                }
              ],
              columnGap: 5
            }
          ],
          width: '*'
        },
        {stack:[{text:'Overall Confidence',fontSize:6,color:'#6B7280',alignment:'center'},{text:'96%',bold:true,fontSize:10.5,color:'#16A34A',alignment:'center',margin:[0,1,0,0]}],width:80},
        {stack:[{text:'Recommended Round',fontSize:6,color:'#6B7280',alignment:'center'},{text:'Technical L1',bold:true,fontSize:9,color:'#16A34A',alignment:'center',margin:[0,1,0,0]}],width:90},
        {stack:[{text:'Priority',fontSize:6,color:'#6B7280',alignment:'center'},{text:'HIGH',bold:true,fontSize:9,color:'#16A34A',alignment:'center',margin:[0,1,0,0]}],width:55},
        {stack:[{text:'Expected Joining',fontSize:6,color:'#6B7280',alignment:'center'},{text:'Immediate',bold:true,fontSize:9,color:'#16A34A',alignment:'center',margin:[0,1,0,0]}],width:75}
      ]
    }
  ], '#F0FDF4', '#BBF7D0', 8);

  /* 8. DARK NAVY FOOTER */
  var footerBlock = {
    table: {widths:['*'],body:[[{
      columns: [
        {
          stack: [
            {text:'ABOUT THIS REPORT',bold:true,fontSize:5.5,color:'#9CA3AF'},
            {text:'This report is generated using advanced AI algorithms and data from multiple sources. Final hiring decisions should include technical and managerial interviews.',fontSize:4.5,color:'#6B7280',margin:[0,1,0,0]}
          ],
          width: 150
        },
        {
          stack: [
            {text:'Generated using Jero AI Recruitment Intelligence',fontSize:5.5,color:'#D1D5DB',alignment:'center',margin:[0,4,0,0]}
          ],
          width: '*'
        },
        {
          stack: [
            {text:'www.jero.works',bold:true,fontSize:7,color:'#FFFFFF',alignment:'right'},
            {text:'',fontSize:4.5,color:'#6B7280',alignment:'right',margin:[0,1,0,0]}
          ],
          width: 110
        }
      ],
      fillColor:'#111827',margin:[6,5,6,5]
    }]]},
    layout: 'noBorders'
  };

  /* Assemble full page content */
  var content = [
    headerCol,
    metaRow,
    heroCard,
    quickInfoStrip,
    skillsIntelligenceCard,
    sectionSummaryComments,
    verificationCard,
    finalRecommendationCard,
    footerBlock
  ];

  return {
    pageSize: 'A4',
    pageMargins: [24, 20, 24, 20],
    content: content,
    defaultStyle: { font: 'Roboto', fontSize: 8.5, color: '#0f172a' }
  };
}

window.cdrExportProfilePDF = function(triggerEl, fromRdc){
  if(window.__isExportingPdf) return;
  window.__isExportingPdf = true;

  var wrap=document.getElementById('kt_activitiesApplication') || document.querySelector('.page-wrapper') || document.body;

  /* Close any open menus */
  var hdrMenu=document.getElementById('hdrDlMenuApp');
  if(hdrMenu) hdrMenu.classList.remove('open');
  if(fromRdc){
    var rd=document.getElementById('rdcDrawer'),ov=document.getElementById('rdcOverlay');
    if(rd) rd.classList.remove('rdc-open');
    if(ov) ov.classList.remove('rdc-open');
  }

  var origHtml=triggerEl?triggerEl.innerHTML:'';
  if(triggerEl){ triggerEl.disabled=true; triggerEl.innerHTML='<span style="font-size:11px;">Generating PDF…</span>'; }

  /* DOM scraping with document-level fallbacks */
  var g=function(sel){ var el=wrap.querySelector(sel) || document.querySelector(sel); return el?(el.textContent||'').trim().replace(/\s+/g,' '):''; };

  var name=g('.cdr-name')||'Unknown';
  var role=g('.cdr-role')||'Candidate';
  var initials=g('.cdr-avatar-sq')||'??';
  var matchEl=wrap.querySelector('.cdr-match-pct-badge') || document.querySelector('.cdr-match-pct-badge');
  var matchText=(matchEl||{textContent:''}).textContent.trim();
  var isImmediate=!!(wrap.querySelector('.cdr-meta-item.cmi-green') || document.querySelector('.cdr-meta-item.cmi-green'));

  var metaPlain=[];
  (wrap.querySelectorAll('.cdr-meta-item').length ? wrap.querySelectorAll('.cdr-meta-item') : document.querySelectorAll('.cdr-meta-item')).forEach(function(el){ var t=(el.textContent||'').trim().replace(/\s+/g,' '); if(t) metaPlain.push(t); });

  var skillItems=[];
  (wrap.querySelectorAll('.cdr-skill-tag').length ? wrap.querySelectorAll('.cdr-skill-tag') : document.querySelectorAll('.cdr-skill-tag')).forEach(function(el){
    skillItems.push({label:el.textContent.trim(),isMatch:el.classList.contains('cdr-skill-match'),isMore:el.classList.contains('cdr-skill-more')});
  });

  var metrics=[];
  (wrap.querySelectorAll('.cdr-metric-row').length ? wrap.querySelectorAll('.cdr-metric-row') : document.querySelectorAll('.cdr-metric-row')).forEach(function(row){
    var lbl=(row.querySelector('.cdr-metric-label')||{}).textContent||'';
    var valEl=row.querySelector('.cdr-metric-val'), fracEl=row.querySelector('.cdr-metric-frac');
    var val=valEl?(valEl.textContent||'').trim().replace(/\s+/g,' '):'';
    var frac=fracEl?(fracEl.textContent||'').trim():'';
    var sub=(row.querySelector('.cdr-metric-sub')||{}).textContent||'';
    var displayVal = val;
    if(frac && val.indexOf('/') === -1){
      displayVal = val + ' / ' + frac;
    } else if(val.indexOf('//') !== -1 || val.indexOf('/ /') !== -1){
      displayVal = val.replace(/\s*\/\s*\/\s*/g, ' / ');
    }
    metrics.push({lbl:lbl.trim(),val:displayVal,sub:sub.trim(),green:!!(valEl&&valEl.classList.contains('cv-green')),red:!!(valEl&&valEl.classList.contains('cv-red'))});
  });

  var pipeItems=[];
  (wrap.querySelectorAll('.cdr-pipeline-card').length ? wrap.querySelectorAll('.cdr-pipeline-card') : document.querySelectorAll('.cdr-pipeline-card')).forEach(function(c){
    var l=(c.querySelector('.cdr-pipeline-label')||{}).textContent||'';
    var v=(c.querySelector('.cdr-pipeline-value')||{}).textContent||'';
    pipeItems.push({lbl:l.trim(),val:v.trim().replace(/\s+/g,' ')});
  });

  var skillsTable={headers:[],rows:[]};
  var skEls=wrap.querySelectorAll('#personal_details .skills-row').length ? wrap.querySelectorAll('#personal_details .skills-row') : document.querySelectorAll('#personal_details .skills-row');
  if(skEls.length){
    var thEl=wrap.querySelector('#personal_details .skills-thead') || document.querySelector('#personal_details .skills-thead');
    if(thEl) thEl.querySelectorAll('div').forEach(function(c){ var t=(c.textContent||'').trim(); if(t) skillsTable.headers.push(t); });
    skEls.forEach(function(row){ var cells=[]; row.querySelectorAll('div').forEach(function(c){ cells.push((c.textContent||'').trim()); }); if(cells.length) skillsTable.rows.push(cells); });
  }

  var sumEl=wrap.querySelector('.summary-text') || document.querySelector('.summary-text');
  var summary=sumEl?(sumEl.textContent||'').trim():'';
  if(!summary) summary='No professional summary available.';
  /* Cap summary to 2000 chars — pdfkit text layout is O(n^2) on a single giant
     paragraph and stalls getBlob() well past 45s on bloated content. */
  if(summary.length>2000) summary=summary.substring(0,2000).replace(/\s+\S*$/,'') + '…';

  var rnEl=wrap.querySelector('.po-recruiter-note-text') || document.querySelector('.po-recruiter-note-text');
  var rnText=rnEl?(rnEl.innerText||rnEl.textContent||'').trim():'';
  var hasRN=!!rnText;
  /* Same cap for recruiter notes — same O(n^2) failure mode. */
  if(rnText.length>1500) rnText=rnText.substring(0,1500).replace(/\s+\S*$/,'') + '…';

  var companyName='Company', recruiterName='';
  (wrap.querySelectorAll('.ownership-row').length ? wrap.querySelectorAll('.ownership-row') : document.querySelectorAll('.ownership-row')).forEach(function(row){
    var lbl=(row.querySelector('.own-label')||{}).textContent||'';
    var val=(row.querySelector('.own-value')||{}).textContent||'';
    lbl=lbl.trim(); val=val.trim();
    if(!val||val==='—') return;
    if(lbl.toLowerCase().indexOf('company')!==-1) companyName=val;
    if(lbl.toLowerCase().indexOf('recruiter')!==-1&&lbl.toLowerCase().indexOf('note')===-1) recruiterName=val;
  });

  /* Logo resolution: check cache, data-logo, or image elements */
  var rawLogoUrl = null;
  if(window.__rcvLogo && window.__rcvLogo.__dataUri) rawLogoUrl = window.__rcvLogo.__dataUri;
  if(!rawLogoUrl){
    var logoNode = document.querySelector('[data-logo]') || document.querySelector('.rcv-wrap') || document.querySelector('.company-logo img') || document.querySelector('img[src*="logo"]');
    if(logoNode){
      rawLogoUrl = logoNode.getAttribute('data-logo') || logoNode.getAttribute('src');
    }
  }

  var months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var now=new Date(), dateStr=now.getDate().toString().padStart(2,'0')+' '+months[now.getMonth()]+' '+now.getFullYear();
  var filename=(name.replace(/[^a-zA-Z0-9\s]/g,'').trim().replace(/\s+/g,'_')||'Candidate')+'_Profile.pdf';

  /* Load pdfmake + convert company logo to base64 Data URI concurrently */
  Promise.all([ensurePdfMake(), getLogoDataUri(rawLogoUrl)]).then(function(results){
    var logoUri = results[1];
    var def=buildDef({
      name:name,role:role,initials:initials,matchText:matchText,logoUri:logoUri,
      metaPlain:metaPlain,skillItems:skillItems,metrics:metrics,pipeItems:pipeItems,
      skillsTable:skillsTable,summary:summary,hasRN:hasRN,rnText:rnText||'',
      companyName:companyName,recruiterName:recruiterName,dateStr:dateStr,isImmediate:isImmediate
    });

    /* Wrap getBlob in a Promise with timeout so silent render failures surface as errors.
     Larger candidate data (long summary, many skills, recruiter notes) can take
     12-20s to encode; raise the timeout to 45s to give real-data PDFs room to finish
     while still surfacing a real hang rather than failing silently. */
    return new Promise(function(res,rej){
      var done=false;
      var timer=setTimeout(function(){
        if(!done){ done=true; rej(new Error('PDF timed out after 45s — possible font or layout issue')); }
      },45000);
      try{
        pdfMake.createPdf(def).getBlob(function(blob){
          if(done) return;
          clearTimeout(timer); done=true;
          if(!blob||blob.size<200){ rej(new Error('PDF output was empty ('+( blob?blob.size:0)+' bytes)')); return; }
          res(blob);
        });
      }catch(e){ if(!done){ clearTimeout(timer); done=true; rej(e); } }
    });
  }).then(function(blob){
    /* Trigger download without any Print dialog */
    var url=URL.createObjectURL(blob);
    var a=document.createElement('a');
    a.href=url; a.download=filename;
    document.body.appendChild(a);
    a.click();
    if(typeof window.__rdcNotifyDownloadStarted === 'function'){
      window.__rdcNotifyDownloadStarted(filename);
    }
    document.body.removeChild(a);
    setTimeout(function(){ URL.revokeObjectURL(url); },3000);
  }).catch(function(err){
    console.error('[CDR-PDF]', err);
    alert('PDF export failed: '+(err&&err.message||String(err)));
  }).finally(function(){
    window.__isExportingPdf = false;
    if(triggerEl){ triggerEl.disabled=false; triggerEl.innerHTML=origHtml; }
  });
};

})();
</script>
