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
#kt_activitiesApplication #personal_details .summary-text,#kt_activitiesApplication #personal_details .reason-summary-text{font-size:12px!important;line-height:1.7!important;}
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
.jw-side-drawer:has(.jw-drawer-body:not(.d-none)),.jw-side-drawer.jw-drawer-open{pointer-events:auto;}
.jw-drawer-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.45);opacity:0;cursor:pointer;transition:opacity .28s;}
.jw-side-drawer:has(.jw-drawer-body:not(.d-none)) .jw-drawer-backdrop,.jw-side-drawer.jw-drawer-open .jw-drawer-backdrop{opacity:1;}
.jw-drawer-panel{position:absolute;right:0;top:0;bottom:0;width:480px;max-width:96vw;background:#fff;display:flex;flex-direction:column;box-shadow:-4px 0 32px rgba(15,23,42,.14);border-left:1px solid #e2e8f0;transform:translateX(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);}
#jw-skill-drawer .jw-drawer-panel{width:576px;}
.jw-side-drawer:has(.jw-drawer-body:not(.d-none)) .jw-drawer-panel,.jw-side-drawer.jw-drawer-open .jw-drawer-panel{transform:translateX(0);}
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
/* Multi-Stage Stepper Progress Modal / Bar in RDC Footer */
.rdc-footer-progress{display:flex;flex-direction:column;gap:5px;margin-bottom:10px;}
.rdc-footer-progress-info{display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:11.5px;}
.rdc-footer-progress-step{font-weight:600;color:#4f46e5;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:11px;}
.rdc-footer-progress-pct{font-weight:700;color:#4f46e5;font-size:11px;}
.rdc-footer-progress-track{height:4px;width:100%;background:#e2e8f0;border-radius:2px;overflow:hidden;}
.rdc-footer-progress-bar{height:100%;width:0%;background:linear-gradient(90deg,#4f46e5,#06b6d4,#10b981);border-radius:2px;transition:width 0.2s ease;}
.rdc-footer-progress-detail{font-size:10.5px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
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
/* Word (.doc) Unsupported Popup Modal Notification */
.rdc-doc-modal-backdrop{position:fixed;inset:0;background:rgba(15,23,42,.6);z-index:1300002;display:none;align-items:center;justify-content:center;padding:16px;-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);transition:opacity .2s ease;}
.rdc-doc-modal{background:#fff;border-radius:12px;width:100%;max-width:440px;box-shadow:0 20px 25px -5px rgba(0,0,0,.15),0 10px 10px -5px rgba(0,0,0,.06);font-family:'Poppins',sans-serif;overflow:hidden;border:1px solid #e2e8f0;animation:rdcDocPop .22s cubic-bezier(.16,1,.3,1);}
@keyframes rdcDocPop{from{opacity:0;transform:scale(.94) translateY(6px);}to{opacity:1;transform:scale(1) translateY(0);}}
.rdc-doc-modal-header{display:flex;align-items:center;justify-content:space-between;padding:18px 20px 0;}
.rdc-doc-modal-icon{width:40px;height:40px;border-radius:10px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.rdc-doc-modal-icon svg{width:22px;height:22px;}
.rdc-doc-modal-close{width:28px;height:28px;border:1px solid #e2e8f0;border-radius:6px;background:#fff;cursor:pointer;color:#64748b;display:flex;align-items:center;justify-content:center;padding:0;transition:all .15s ease;}
.rdc-doc-modal-close:hover{background:#fee2e2;color:#dc2626;border-color:#fca5a5;}
.rdc-doc-modal-close svg{width:13px;height:13px;}
.rdc-doc-modal-body{padding:14px 20px 18px;}
.rdc-doc-modal-title{font-size:14px;font-weight:700;color:#0f172a;margin:0 0 8px;line-height:1.35;font-family:'Poppins',sans-serif;}
.rdc-doc-modal-text{font-size:12.5px;color:#475569;margin:0;line-height:1.55;font-family:'Poppins',sans-serif;}
.rdc-doc-modal-footer{display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:12px 20px 16px;background:#f8fafc;border-top:1px solid #f1f5f9;}
.rdc-doc-modal-btn{font-family:'Poppins',sans-serif;font-size:12px;font-weight:600;padding:8px 16px;border-radius:7px;cursor:pointer;transition:all .15s ease;display:inline-flex;align-items:center;gap:6px;}
.rdc-doc-modal-btn-primary{background:#4f46e5;color:#fff;border:none;}
.rdc-doc-modal-btn-primary:hover{background:#4338ca;}
.rdc-doc-modal-btn-primary svg{width:13px;height:13px;}
.rdc-doc-modal-btn-secondary{background:#fff;color:#64748b;border:1px solid #cbd5e1;}
.rdc-doc-modal-btn-secondary:hover{background:#f1f5f9;color:#334155;}
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
/* Recruiter Notes, Reason for Job Change & Visa drawers */
#jw-recruiter-note-drawer .rn-section, #jw-reason-drawer .rn-section, #jw-visa-drawer .rn-section{margin-bottom:16px;}
#jw-recruiter-note-drawer .rn-label, #jw-reason-drawer .rn-label, #jw-visa-drawer .rn-label{font-size:11px;font-weight:600;color:#3f4254;margin-bottom:6px;display:flex;align-items:center;gap:4px;}
#jw-recruiter-note-drawer .rn-label span, #jw-reason-drawer .rn-label span, #jw-visa-drawer .rn-label span{font-weight:400;color:#94a3b8;font-size:10.5px;}
#jw-recruiter-note-drawer .rn-select, #jw-reason-drawer .rn-select, #jw-visa-drawer .rn-select{width:100%;border:1.5px solid #e4e6ef;border-radius:8px;padding:8px 10px;font-size:12px;color:#181c32;background:#f8f9fb;cursor:pointer;font-family:'Poppins',sans-serif;}
#jw-recruiter-note-drawer .rn-editor{min-height:220px;max-height:400px;border:1.5px solid #e4e6ef;border-radius:8px;padding:12px 14px;font-size:12px;font-family:'Poppins',sans-serif;line-height:1.75;color:#181c32;background:#f8f9fb;outline:none;overflow-y:auto;word-break:break-word;}
#jw-recruiter-note-drawer .rn-editor:focus{border-color:#3e97ff;background:#fff;}
#jw-recruiter-note-drawer .rn-editor:empty::before{content:attr(data-placeholder);color:#94a3b8;pointer-events:none;}
#jw-reason-drawer #jw_reason_for_change_editor{font-family:'Poppins',sans-serif;font-size:12px;color:#181c32;border:1.5px solid #e4e6ef;border-radius:8px;padding:10px 12px;line-height:1.6;background:#f8f9fb;}
#jw-reason-drawer #jw_reason_for_change_editor:focus{border-color:#d97706;background:#fff;outline:none;box-shadow:0 0 0 3px rgba(217,119,6,0.1);}
#jw-visa-drawer .visa-chip-btn.active{background:#2563eb!important;color:#fff!important;border-color:#2563eb!important;}
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

/* ===== STANDARDIZED PROFILE OVERVIEW CARDS (Master Standard: Recruiter Comments) ===== */
#kt_activitiesApplication #personal_details .cardshow {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03);
  padding: 24px;
  overflow: hidden;
  font-family: 'Poppins', sans-serif;
  margin-bottom: 0 !important;
}

#kt_activitiesApplication #personal_details .card-header-sidebar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: -24px -24px 16px;
  padding: 14px 24px;
  border-bottom: 1px solid #f0f4f8;
  background: #fff;
  position: relative;
}

#kt_activitiesApplication #personal_details .card-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

#kt_activitiesApplication #personal_details .card-icon svg {
  width: 18px;
  height: 18px;
}

#kt_activitiesApplication #personal_details .card-title {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.25;
  font-family: 'Poppins', sans-serif;
  margin: 0;
}

/* Master Recruiter Comments text & empty note style */
#kt_activitiesApplication #personal_details .po-recruiter-note-text {
  font-size: 12px;
  line-height: 1.65;
  color: #334155;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .po-no-notes {
  font-size: 12px;
  color: #94a3b8;
  font-style: italic;
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

/* ===== COMPENSATION DETAILS ===== */
#kt_activitiesApplication #personal_details .comp-items-list {
  display: flex;
  flex-direction: column;
}

#kt_activitiesApplication #personal_details .comp-item-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f1f5f9;
  min-height: 42px;
}

#kt_activitiesApplication #personal_details .comp-item-row:last-child {
  border-bottom: none;
}

#kt_activitiesApplication #personal_details .comp-item-left {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

#kt_activitiesApplication #personal_details .comp-row-icon {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

#kt_activitiesApplication #personal_details .comp-row-icon svg {
  width: 15px;
  height: 15px;
}

#kt_activitiesApplication #personal_details .comp-icon-green {
  background: #dcfce7;
  color: #16a34a;
}

#kt_activitiesApplication #personal_details .comp-icon-blue {
  background: #eff6ff;
  color: #2563eb;
}

#kt_activitiesApplication #personal_details .comp-icon-purple {
  background: #f5f3ff;
  color: #7c3aed;
}

#kt_activitiesApplication #personal_details .comp-icon-amber {
  background: #fef3c7;
  color: #d97706;
}

#kt_activitiesApplication #personal_details .comp-icon-red {
  background: #fee2e2;
  color: #dc2626;
}

#kt_activitiesApplication #personal_details .comp-icon-mint {
  background: #dcfce7;
  color: #059669;
}

#kt_activitiesApplication #personal_details .comp-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
  white-space: nowrap;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .comp-value {
  font-size: 12px;
  font-weight: 600;
  color: #0f172a;
  text-align: right;
  white-space: nowrap;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .comp-curr-symbol {
  font-weight: 600 !important;
  color: #0f172a;
  margin-right: 2px;
  display: inline-block;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}

#kt_activitiesApplication #personal_details .comp-badge-purple {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 3px 10px;
  background: #ede9fe;
  color: #6d28d9;
  font-size: 11px;
  font-weight: 600;
  border-radius: 12px;
  line-height: 1.3;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .comp-badge-green {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 3px 10px;
  background: #dcfce7;
  color: #16a34a;
  font-size: 11px;
  font-weight: 600;
  border-radius: 12px;
  line-height: 1.3;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .comp-badge-gray {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 3px 10px;
  background: #f1f5f9;
  color: #64748b;
  font-size: 11px;
  font-weight: 600;
  border-radius: 12px;
  line-height: 1.3;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .comp-comments-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px 12px;
  margin-top: 12px;
  display: flex;
  align-items: center;
  gap: 10px;
}

#kt_activitiesApplication #personal_details .comp-comments-icon {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  background: #dbeafe;
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

#kt_activitiesApplication #personal_details .comp-comments-icon svg {
  width: 15px;
  height: 15px;
}

#kt_activitiesApplication #personal_details .comp-comments-content {
  flex: 1;
  min-width: 0;
}

#kt_activitiesApplication #personal_details .comp-comments-label {
  font-size: 11px;
  color: #64748b;
  font-weight: 500;
  margin-bottom: 2px;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .comp-comments-text {
  font-size: 12px;
  color: #0f172a;
  font-weight: 600;
  line-height: 1.35;
  word-break: break-word;
  font-family: 'Poppins', sans-serif;
}

/* ===== OWNERSHIP SECTION ===== */
#kt_activitiesApplication #personal_details .own-items-list {
  display: flex;
  flex-direction: column;
}

#kt_activitiesApplication #personal_details .own-item-row {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 8px 0;
  border-bottom: 1px solid #f1f5f9;
}

#kt_activitiesApplication #personal_details .own-item-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

#kt_activitiesApplication #personal_details .own-row-icon {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}

#kt_activitiesApplication #personal_details .own-row-icon svg {
  width: 15px;
  height: 15px;
}

#kt_activitiesApplication #personal_details .own-icon-blue {
  background: #eff6ff;
  color: #2563eb;
}

#kt_activitiesApplication #personal_details .own-icon-green {
  background: #dcfce7;
  color: #16a34a;
}

#kt_activitiesApplication #personal_details .own-icon-amber {
  background: #fef3c7;
  color: #d97706;
}

#kt_activitiesApplication #personal_details .own-icon-purple {
  background: #f5f3ff;
  color: #7c3aed;
}

#kt_activitiesApplication #personal_details .own-item-body {
  flex: 1;
  min-width: 0;
}

#kt_activitiesApplication #personal_details .own-label {
  font-size: 11.5px;
  color: #64748b;
  font-weight: 500;
  margin-bottom: 2px;
  font-family: 'Poppins', sans-serif;
  line-height: 1.2;
}

#kt_activitiesApplication #personal_details .own-value {
  font-size: 12px;
  font-weight: 600;
  color: #0f172a;
  font-family: 'Poppins', sans-serif;
  line-height: 1.35;
  word-break: break-word;
  padding-left: 0 !important;
  margin-left: 0 !important;
}

#kt_activitiesApplication #personal_details .own-datetime-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 5px 10px;
  margin-top: 3px;
  max-width: 100%;
  flex-wrap: wrap;
}

#kt_activitiesApplication #personal_details .own-dt-item {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  font-weight: 500;
  color: #0f172a;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .own-dt-item svg {
  color: #64748b;
  flex-shrink: 0;
}

#kt_activitiesApplication #personal_details .own-dt-sep {
  color: #cbd5e1;
  font-weight: 400;
  font-size: 11.5px;
  user-select: none;
}

/* ===== CONTACT DETAILS SECTION ===== */
#kt_activitiesApplication #personal_details .cnt-items-list {
  display: flex;
  flex-direction: column;
}

#kt_activitiesApplication #personal_details .cnt-item-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 0;
  border-bottom: 1px solid #f1f5f9;
}

#kt_activitiesApplication #personal_details .cnt-item-row:last-child {
  border-bottom: none;
}

#kt_activitiesApplication #personal_details .cnt-row-icon {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

#kt_activitiesApplication #personal_details .cnt-row-icon svg {
  width: 15px;
  height: 15px;
}

#kt_activitiesApplication #personal_details .cnt-icon-blue {
  background: #eff6ff;
  color: #2563eb;
}

#kt_activitiesApplication #personal_details .cnt-icon-purple {
  background: #f5f3ff;
  color: #7c3aed;
}

#kt_activitiesApplication #personal_details .cnt-icon-green {
  background: #dcfce7;
  color: #16a34a;
}

#kt_activitiesApplication #personal_details .cnt-icon-amber {
  background: #fff7ed;
  color: #ea580c;
}

#kt_activitiesApplication #personal_details .cnt-item-body {
  flex: 1;
  min-width: 0;
}

#kt_activitiesApplication #personal_details .cnt-label {
  font-size: 11.5px;
  color: #64748b;
  font-weight: 500;
  margin-bottom: 2px;
  font-family: 'Poppins', sans-serif;
  line-height: 1.2;
}

#kt_activitiesApplication #personal_details .cnt-value {
  font-size: 12px;
  font-weight: 600;
  color: #0f172a;
  font-family: 'Poppins', sans-serif;
  line-height: 1.35;
  word-break: break-all;
}

#kt_activitiesApplication #personal_details .cnt-val-link {
  color: #0f172a;
  text-decoration: none;
  transition: color .15s ease;
}

#kt_activitiesApplication #personal_details .cnt-val-link:hover {
  color: #2563eb;
}

#kt_activitiesApplication #personal_details .cnt-phone-val {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 600;
  color: #0f172a;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .cnt-phone-val .phone-cell {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .cnt-phone-val .phone-flag-box {
  display: inline-flex;
  align-items: center;
  width: 20px;
  height: 15px;
  border-radius: 2px;
  overflow: hidden;
  box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
  flex-shrink: 0;
}

#kt_activitiesApplication #personal_details .cnt-phone-val .phone-dial-code {
  font-size: 12px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  font-family: 'Poppins', sans-serif !important;
}

#kt_activitiesApplication #personal_details .cnt-phone-val .phone-digits {
  font-size: 12px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  font-family: 'Poppins', sans-serif !important;
}

#kt_activitiesApplication #personal_details .cnt-copy-btn {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  flex-shrink: 0;
  padding: 0;
  transition: all .15s ease;
}

#kt_activitiesApplication #personal_details .cnt-copy-btn:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: #2563eb;
}

#kt_activitiesApplication #personal_details .cnt-whatsapp-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 8px 12px;
  margin-top: 10px;
  text-decoration: none;
  transition: all .18s ease;
  cursor: pointer;
}

#kt_activitiesApplication #personal_details .cnt-whatsapp-banner:hover {
  background: #dcfce7;
  border-color: #86efac;
}

#kt_activitiesApplication #personal_details .cnt-wa-left {
  display: flex;
  align-items: center;
}

#kt_activitiesApplication #personal_details .cnt-wa-sep {
  width: 1px;
  height: 14px;
  background: #86efac;
  margin: 0 10px;
}

#kt_activitiesApplication #personal_details .cnt-wa-text {
  font-size: 12px;
  font-weight: 600;
  color: #15803d;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .cnt-wa-arrow {
  color: #16a34a;
  display: flex;
  align-items: center;
}

#kt_activitiesApplication #personal_details .cnt-linkedin-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  padding: 8px 12px;
  margin-top: 8px;
  text-decoration: none;
  transition: all .18s ease;
  cursor: pointer;
}

#kt_activitiesApplication #personal_details .cnt-linkedin-banner:hover {
  background: #dbeafe;
  border-color: #93c5fd;
}

#kt_activitiesApplication #personal_details .cnt-li-left {
  display: flex;
  align-items: center;
}

#kt_activitiesApplication #personal_details .cnt-li-sep {
  width: 1px;
  height: 14px;
  background: #93c5fd;
  margin: 0 10px;
}

#kt_activitiesApplication #personal_details .cnt-li-text {
  font-size: 12px;
  font-weight: 600;
  color: #1d4ed8;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .cnt-li-arrow {
  color: #2563eb;
  display: flex;
  align-items: center;
}

/* ===== OTHER DETAILS & AGENCY / RM DETAILS STANDARDIZATION ===== */
#kt_activitiesApplication #personal_details .agency-grid {
  display: grid;
  grid-template-columns: 120px 1fr;
  gap: 8px 16px;
}

#kt_activitiesApplication #personal_details .agency-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .agency-value {
  font-size: 12px;
  color: #0f172a;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .agency-value a {
  color: #2563eb;
  text-decoration: none;
}

#kt_activitiesApplication #personal_details .agency-value a:hover {
  text-decoration: underline;
}

#kt_activitiesApplication #personal_details .rm-details h4 {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 4px;
  font-family: 'Poppins', sans-serif;
}

#kt_activitiesApplication #personal_details .rm-contact-item {
  font-size: 12px;
  color: #475569;
  font-family: 'Poppins', sans-serif;
}
</style>

<script>
   $("timeline-item:last-child").addClass("timeline-item:last-child");
</script>
@php 
      
    $jobId = $application->jobId ?? $application->job_id;
    $jobData = $jobData ?? (empty($jobId) ? null : DB::table('jobs')->select('max_sal_mode as jobMode','max_salary_currency as jobCurrency')->where('id','=',$jobId)->first());
    $isDirectCandidate = (@$application->condidate_type === 'other');
    $jobMode = $isDirectCandidate ? (int) (@$application->current_ctc_mode ?? 0) : ($jobData->jobMode ?? @$application->current_ctc_mode);
    $jobMode = isset($salaryMode[$jobMode]) ? $jobMode : 0;
    $jobCurrency = $isDirectCandidate ? (@$application->current_ctc_cur ?? 0) : ($jobData->jobCurrency ?? @$application->current_ctc_cur);
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
  
      $candRawCity = (!empty(@$application->city_name) && !preg_match('/^\d+$/', trim((string) @$application->city_name)))
          ? @$application->city_name
          : ((!empty(@$application->city) && !preg_match('/^\d+$/', trim((string) @$application->city))) ? @$application->city : '');
      $city_name	= $candRawCity ? ucwords($candRawCity).',' : '';

      $candRawState = (!empty(@$application->state_name) && !preg_match('/^\d+$/', trim((string) @$application->state_name)))
          ? @$application->state_name
          : ((!empty(@$application->state) && !preg_match('/^\d+$/', trim((string) @$application->state))) ? @$application->state : '');
      $state_name	= $candRawState ? ucwords($candRawState).',' : '';

      $candRawCountry = (!empty(@$application->country_name) && !preg_match('/^\d+$/', trim((string) @$application->country_name)))
          ? @$application->country_name
          : ((!empty(@$application->country) && !preg_match('/^\d+$/', trim((string) @$application->country))) ? @$application->country : '');
      $country_name	= ucwords($candRawCountry);	
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
      $__hdrSkillsShown = $__hdrSkillsArr;
      $__hdrSkillsMore  = 0;

      // Initials for avatar (up to 2 letters).
      $__hdrFullName = trim((string) @$application->full_name);
      $__hdrInitials = '';
      if ($__hdrFullName !== '') {
          $__hdrParts = preg_split('/\s+/', $__hdrFullName);
          $__hdrInitials = strtoupper(substr($__hdrParts[0] ?? '', 0, 1) . (isset($__hdrParts[1]) ? substr($__hdrParts[1], 0, 1) : ''));
      }
      if ($__hdrInitials === '') { $__hdrInitials = 'NA'; }

      $__hdrIsVerified = (bool) preg_match('/verified|active|success/i', (string) @$application->css_style);

      // Availability — same NP mode rules used across candidate lists & profile.
      $__hdrAvailability = '';
      $__isBenchCand = (@$application->condidate_type == 'bench_condidate');
      if ((@$application->serving_notice_period_mode == 0) && (int) @$application->np_id > 0) {
          $__hdrAvailability = $application->np_id . ' Days';
      } elseif (@$application->serving_notice_period_mode == 1 && !empty($application->np_id)) {
          $__npDateTs = strtotime($application->np_id);
          if ($__npDateTs !== false && date('Y-m-d', $__npDateTs) < date('Y-m-d')) {
              $__hdrAvailability = 'Immediate Joiner';
          } elseif ($__npDateTs !== false) {
              $__dateFmt = date('d M Y', $__npDateTs);
              $__hdrAvailability = $__isBenchCand ? ('Available ' . $__dateFmt) : ('SNP LWD ' . $__dateFmt);
          } else {
              $__hdrAvailability = trim((string) @$application->np_id);
          }
      } elseif (@$application->serving_notice_period_mode == 2 || strtolower((string)@$application->np_id) === 'immediate') {
          $__hdrAvailability = 'Immediate Joiner';
          if (! empty($application->last_working_date)) {
              $__hdrAvailability .= ' - LWD ' . date('d M Y', strtotime($application->last_working_date));
          }
      } else {
          $__hdrAvailability = trim((string) @$application->np_id);
      }
      if ($__hdrAvailability === '') { $__hdrAvailability = '—'; }
      $__hdrIsImmediate = (stripos($__hdrAvailability, 'Immediate') !== false);

      // Skill match — unified canonical matching engine (identical to candidate lists).
      $__hdrMatchCalc = GeneralMethod::calculateSkillMatch($jobSkill ?? [], $__hdrSkillsArr);
      $__hdrJobTotal   = $__hdrMatchCalc['total'];
      $__hdrMatchedCount = $__hdrMatchCalc['matched_count'];
      $__hdrNotMatched = max(0, $__hdrJobTotal - $__hdrMatchedCount);
      $__hdrOverallPct = $__hdrMatchCalc['percentage'];
      $__hdrMatchedSkillNames = $__hdrMatchCalc['matched_skills'];

      $__hdrJobSkills = [];
      if (!empty($jobSkill)) {
          foreach ($jobSkill as $__js) {
              $__js = (object) $__js;
              $__sn = trim((string) (@$__js->skill_name ?? @$__js->skill ?? ''));
              if ($__sn !== '') { $__hdrJobSkills[mb_strtolower($__sn)] = $__sn; }
          }
      }

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

      // Salary under budget & currency conversion — centralized CurrencyService
      $__budgetEval = \App\Services\CurrencyService::evaluateBudgetMatch(@$job, @$application);
      $__hdrUnderBudget = $__budgetEval['under_budget'];

      // Dual-currency converted compensation (Candidate original preserved; converted to Job Currency)
      $__candJobComp = \App\Services\CurrencyService::convertCandidateToJobCurrency(@$application, @$job);
      $__hdrCtcText  = $__candJobComp['formatted_ctc'];
      $__hdrEctcText = $__candJobComp['formatted_ectc'];
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
  <input type="hidden" id="candidate_wise_id" value="{{ @$application->wiseId ?: @$jobWiseCanId }}" />
  <input type="hidden" id="candidate_core_id" value="{{ @$application->id }}" />

  {{-- TOP ACTION BAR (outside any card, spans full width — mirrors job drawer top-actions) --}}
  <div class="cdr-topbar" aria-label="Candidate actions">
    <div class="cdr-topbar-group">
      @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='other' && $editprofile===true)
        <a href="{{ route('service_company.job-applications.edit', [ GeneralMethod::value_encrypt($jobWiseCanId)]) }}?currentPage={{ GeneralMethod::value_encrypt($currentPage) }}&currentSlug={{ GeneralMethod::value_encrypt($currentSlug) }}" class="cdr-action-btn cdr-btn-primary {{ $dnone }}" title="Edit candidate profile">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
          Edit
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
          Email
        </a>

        @if(@$application->condidate_type=='other' && $notVisiableBench)
          <a href="javascript:void(0);" class="cdr-action-btn cdr-btn-wa sendwhatsappmodal {{ $dnone }}" title="Send WhatsApp message">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20.52 3.48A11.94 11.94 0 0 0 12 0C5.37 0 .05 5.32.05 11.95c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.9 11.9 0 0 0 5.7 1.45h.01c6.63 0 11.95-5.32 11.95-11.95 0-3.19-1.24-6.19-3.44-8.37z"/>
            </svg>
            WhatsApp
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

      {{-- Forward Resume Header Action (Rule 1, Rule 3, Rule 7, Rule 15, Rule 17) --}}
      <button type="button" class="cdr-action-btn cdr-btn-secondary {{ $dnone }}" id="hdr_fwd_resume_btn" onclick="openCandidateForwardResume({{ (int)$application->id }}, {{ !empty($jobId) ? (int)$jobId : 'null' }})" title="Forward candidate resume to client or hiring team">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="22" y1="2" x2="11" y2="13"></line>
          <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
        </svg>
        Forward Resume
      </button>

      <div class="cdr-topbar-split" role="separator" aria-hidden="true"></div>

      @php
        $_rawResumeUrl = !empty($application->resume) ? $application->resume : (!empty($resume) ? $resume : '');
        $_resumeExt = strtolower(pathinfo(parse_url($_rawResumeUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        $_hasValidResume = !empty($_rawResumeUrl) && (empty($_resumeExt) || in_array($_resumeExt, ['pdf', 'doc', 'docx', 'htm', 'html']));
      @endphp

      @if($_hasValidResume)
        @if($resumeAwsPath)
          <div class="cdr-dl-wrap" id="hdrDlWrapApp">
            <button type="button" class="cdr-action-btn cdr-btn-secondary" id="hdrDlToggleApp" aria-haspopup="true" aria-expanded="false">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              Download Resume
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="cdr-caret" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div id="hdrDlMenuApp" class="cdr-dl-menu">
              <a href="{{ route('download.resume', ['id' => $application->id,'path' => rtrim(strtr(base64_encode($_rawResumeUrl), '+/', '-_'), '=')]) }}" target="_blank" class="cdr-dl-item">
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
          <a href="{{ route('download.resume', ['id' => $application->id,'path' => rtrim(strtr(base64_encode($_rawResumeUrl), '+/', '-_'), '=')]) }}" target="_blank" class="cdr-action-btn cdr-btn-secondary downloadCount {{ $dnone }}" id="hdrDlToggleApp" role="button" aria-haspopup="true" aria-expanded="false" title="Download resume">
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

  {{-- ===== SCOPED CANDIDATE ROOT (Strict Isolation & 0% Server Strain) ===== --}}
  <div id="jw_candidate_root_detail" data-candidate-root data-candidate-id="{{ (int) @$application->id }}" data-candidate-field-version="1" style="display:contents;">

  {{-- Authoritative Scoped Semantic Vault for Client Snapshot Resolution --}}
  <div class="jw-candidate-semantic-vault" style="display:none;" aria-hidden="true">
    <span data-candidate-field="candidate_name" data-value="{{ ucwords(@$application->full_name) }}"></span>
    <span data-candidate-field="current_company" data-value="{{ (@$application->current_company) ? ucwords(@$application->current_company) : ucwords(@$application->cur_company_name ?: '') }}"></span>
    <span data-candidate-field="total_experience" data-value="{{ $__bdTotalExp }}"></span>
    <span data-candidate-field="education" data-value="{{ strtoupper(@$application->qualification_name ?: (@$application->qualification ?: '')) }}"></span>
    <span data-candidate-field="work_mode" data-value="{{ ucwords(@$application->wm ?: '') }}"></span>
    <span data-candidate-field="location" data-value="{{ trim(($city_name ?? '').' '.($state_name ?? '')) ?: (@$application->current_location ?: '') }}"></span>
    <span data-candidate-field="notice_period" data-value="{{ $__hdrAvailability }}"></span>
    <span data-candidate-field="notice_mode" data-value="{{ @$application->serving_notice_period_mode }}"></span>
    <span data-candidate-field="notice_lwd" data-value="{{ @$application->last_working_date }}"></span>
    <span data-candidate-field="phone" data-value="{{ @$application->phone }}"></span>
    <span data-candidate-field="email" data-value="{{ @$application->email }}"></span>
    <span data-candidate-field="linkedin" data-value="{{ @$application->linkedin }}"></span>
    <span data-candidate-field="candidate_rating" data-value="{{ (int) @$application->rating }}"></span>
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
            @php $__mpbClass = \App\Helper\GeneralMethod::getSkillMatchBadgeStyle($__hdrOverallPct)['class']; @endphp
            <span class="cdr-match-pct-badge {{ $__mpbClass }} jw-drawer-match-badge" data-candidate-id="{{ @$application->id }}" data-match-pct="{{ $__hdrOverallPct }}">{{ $__hdrOverallPct }}% Match</span>
          @endif
        </div>

        <div class="cdr-id-body">
          <div class="cdr-name-row">
            <h2 class="cdr-name" data-candidate-field="candidate_name">{{ ucwords(@$application->full_name ?: '—') }}</h2>
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
              <span class="cdr-meta-item" data-candidate-field="total_experience" data-value="{{ $__bdTotalExp }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                {{ $__bdTotalExp }} Yrs Experience
              </span>
            @endif
            @if($__candCityState)
              <span class="cdr-meta-item" data-candidate-field="location" data-value="{{ $__candCityState }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $__candCityState }}
              </span>
            @endif
            @if($__dispCompany)
              <span class="cdr-meta-item" data-candidate-field="current_company" data-value="{{ $__dispCompany }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                {{ $__dispCompany }}
              </span>
            @endif
            @if($__hdrAvailability !== '—')
              <span class="cdr-meta-item {{ $__hdrIsImmediate ? 'cmi-green' : '' }}" data-candidate-field="notice_period" data-value="{{ $__hdrAvailability }}">
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
            @php
              $__sNorm = mb_strtolower(trim($__s));
              $__isMatch = false;
              if (!empty($__hdrJobSkills)) {
                  foreach ($__hdrJobSkills as $__jsNorm => $__jsOriginal) {
                      if (\App\Helper\GeneralMethod::skillMatchesPhrasePrefix($__jsOriginal, $__s)) {
                          $__isMatch = true;
                          break;
                      }
                  }
              }
            @endphp
            <span class="cdr-skill-tag {{ $__isMatch ? 'cdr-skill-match' : 'cdr-skill-plain' }}">{{ ucwords(strtolower($__s)) }}</span>
          @endforeach
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
        <div class="cdr-pipeline-value" data-candidate-field="work_mode" data-value="{{ ucwords(@$application->wm ?: '') }}">{{ ucwords(@$application->wm) ?: '—' }}</div>
      </div>
    </div>

    {{-- Education --}}
    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-indigo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.66 1.79 3 4 3s4-1.34 4-3v-5"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Education</div>
        <div class="cdr-pipeline-value" data-candidate-field="education" data-value="{{ strtoupper(@$application->qualification_name ?: (@$application->qualification ?: '')) }}" title="{{ strtoupper(@$application->qualification_name) }}">{{ \Illuminate\Support\Str::limit(strtoupper(@$application->qualification_name), 10, '…') ?: '—' }}</div>
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
        // recruiting company logo + name (cached Redis Base64 data-URI for 0ms, 0-network authoritative embedding with proxy fallback)
        $rcvCompanyLogo = '';
        $rcvCompanyLogoRaw = '';
        $rcvCompanyName = '';
        $__rcvCid = auth()->user()->company_id ?? 0;
        if($__rcvCid){
            $__rcvC = $rcvCompany ?? DB::table('companies')->select('logo','company_name')->where('id', $__rcvCid)->first();
            $rcvCompanyName = ucwords((string) (@$__rcvC->company_name ?? ''));
            $__rcvLogo = $__rcvC->logo ?? '';
            $rcvCompanyLogoRaw = $__rcvLogo;
            if(!empty($__rcvLogo) && strlen($__rcvLogo) > 5){
                // 1. Authoritative Redis-cached Base64 Data URI (0ms, 0-network, 100% reliable)
                $rcvCompanyLogo = \Cache::remember('company_logo_data_uri:'.$__rcvCid.':'.md5($__rcvLogo), 86400, function() use ($__rcvLogo) {
                    try {
                        $body = null;
                        $mime = 'image/png';
                        $r2Key = '';
                        if (str_starts_with($__rcvLogo, 'https://file.jero.works/')) {
                            $r2Key = ltrim((string) parse_url($__rcvLogo, PHP_URL_PATH), '/');
                        } elseif (!filter_var($__rcvLogo, FILTER_VALIDATE_URL)) {
                            $r2Key = ltrim($__rcvLogo, '/');
                        }
                        if (!empty($r2Key) && \Storage::disk('r2')->exists($r2Key)) {
                            $body = \Storage::disk('r2')->get($r2Key);
                            $ext = strtolower(pathinfo($r2Key, PATHINFO_EXTENSION));
                            $map = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'webp' => 'image/webp'];
                            $mime = $map[$ext] ?? 'image/png';
                        }
                        if (!$body && !filter_var($__rcvLogo, FILTER_VALIDATE_URL)) {
                            $local = public_path(ltrim($__rcvLogo, '/'));
                            if (file_exists($local) && is_readable($local)) {
                                $body = file_get_contents($local);
                                $ext = strtolower(pathinfo($local, PATHINFO_EXTENSION));
                                $map = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'webp' => 'image/webp'];
                                $mime = $map[$ext] ?? 'image/png';
                            }
                        }
                        if (!$body && filter_var($__rcvLogo, FILTER_VALIDATE_URL)) {
                            $resp = \Illuminate\Support\Facades\Http::timeout(5)->withOptions(['verify' => false])->get($__rcvLogo);
                            if ($resp->successful()) {
                                $body = $resp->body();
                                $ct = $resp->header('Content-Type');
                                if ($ct && str_starts_with($ct, 'image/')) {
                                    $mime = explode(';', $ct)[0];
                                }
                            }
                        }
                        if ($body && strlen($body) <= 1024 * 1024) {
                            if (str_starts_with($body, "\x89PNG\r\n\x1a\n")) {
                                $mime = 'image/png';
                            } elseif (str_starts_with($body, "\xFF\xD8\xFF")) {
                                $mime = 'image/jpeg';
                            }
                            return 'data:' . $mime . ';base64,' . base64_encode($body);
                        }
                    } catch (\Throwable $e) {}
                    return '';
                });

                // Fallback to relative proxy route if data URI generation was empty
                if (empty($rcvCompanyLogo)) {
                    $rcvCompanyLogo = route('service_company.job-applications.company-logo-proxy', ['cid' => $__rcvCid], false);
                }
            }
        }
        // candidate summary for the submission banner
        $__te = trim((string) @$application->total_exp);
        $rcvBExp = ($__te !== '' && $__te !== '0') ? ($__te . ' Yrs') : '--';
        $__isBenchCand = (@$application->condidate_type == 'bench_condidate');
        if (@$application->serving_notice_period_mode == 0) {
            $rcvBNotice = !empty(@$application->np_id) ? (@$application->np_id . ' Days') : '--';
        } elseif (@$application->serving_notice_period_mode == 1) {
            $__npDateTs = !empty(@$application->np_id) ? strtotime(@$application->np_id) : false;
            if ($__npDateTs !== false && date('Y-m-d', $__npDateTs) < date('Y-m-d')) {
                $rcvBNotice = 'Immediate';
            } elseif ($__npDateTs !== false) {
                $__dateFmt = date('d M Y', $__npDateTs);
                $rcvBNotice = $__isBenchCand ? ('Available ' . $__dateFmt) : ('SNP LWD ' . $__dateFmt);
            } else {
                $rcvBNotice = trim((string) @$application->np_id);
            }
        } elseif (@$application->serving_notice_period_mode == 2 || strtolower((string)@$application->np_id) === 'immediate') {
            $rcvBNotice = 'Immediate';
            if (! empty($application->last_working_date)) {
                $rcvBNotice .= ' - LWD ' . date('d M Y', strtotime($application->last_working_date));
            }
        } else {
            $rcvBNotice = trim((string) @$application->np_id);
        }
        if($rcvBNotice === '') $rcvBNotice = '--';
        $rcvBLoc = trim(@$city_name, ', ');
        if($rcvBLoc === '') $rcvBLoc = trim(ucwords((string) @$country_name), ', ');
        if($rcvBLoc === '') $rcvBLoc = '--';
        $rcvBCctc = (!empty($__candJobComp['formatted_ctc']) && $__candJobComp['formatted_ctc'] !== '—') ? $__candJobComp['formatted_ctc'] : '--';
        $rcvBEctc = (!empty($__candJobComp['formatted_ectc']) && $__candJobComp['formatted_ectc'] !== '—') ? $__candJobComp['formatted_ectc'] : '--';
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
        #resume_cv .rcv-kw-cols { display:flex; flex-direction:column; gap:8px; margin-top:0; }
        #resume_cv .rcv-kw-group { display:flex; align-items:flex-start; gap:8px; flex-wrap:wrap; margin-top:0; }
        #resume_cv .rcv-kw-title { font-size:10.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.4px; margin-top:4px; margin-bottom:0; flex-shrink:0; white-space:nowrap; }
        #resume_cv .rcv-chips { display:flex; flex-wrap:wrap; gap:8px; align-items:flex-start; }
        #resume_cv .rcv-chip-wrap { display:inline-flex; align-items:center; }
        #resume_cv .rcv-chip { display:inline-flex; align-items:center; gap:5px; padding:4px 9px; border-radius:20px; font-size:11px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; user-select:none; transition:all .12s; }
        #resume_cv .rcv-chip .rcv-cnt { font-size:10px; background:#e2e8f0; color:#475569; border-radius:10px; padding:0 5px; min-width:15px; text-align:center; }
        #resume_cv .rcv-chip.found { border-color:#bbf7d0; background:#f0fdf4; color:#15803d; }
        #resume_cv .rcv-chip.found .rcv-cnt { background:#bbf7d0; color:#15803d; }
        #resume_cv .rcv-chip.missing { opacity:.55; }
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
        #resume_cv .rcv-textlayer mark.rcv-hit,
        #resume_cv .rcv-textlayer mark.rcv-hit-exact,
        #resume_cv .rcv-textlayer mark.rcv-hit-token { background:rgba(251,191,36,.45); color:transparent; border-radius:2px; }
        #resume_cv .rcv-textlayer mark.rcv-hit-active { background:rgba(249,115,22,.8) !important; box-shadow:0 0 0 2px rgba(249,115,22,.6); }
        #resume_cv .rcv-textlayer span.rcv-wasm-hit-span { display:block; pointer-events:auto; }
        #resume_cv .rcv-textlayer span.rcv-wasm-hit-span mark.rcv-hit { display:block; width:100%; height:100%; line-height:inherit; }
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
        #resume_cv .rcv-html mark.rcv-hit,
        #resume_cv .rcv-html mark.rcv-hit-exact,
        #resume_cv .rcv-html mark.rcv-hit-token { background:rgba(251,191,36,.5); color:inherit; border-radius:2px; padding:0 1px; }
        #resume_cv .rcv-html mark.rcv-hit-active { background:rgba(249,115,22,.75) !important; box-shadow:0 0 0 2px rgba(249,115,22,.4); }
        #resume_cv .rcv-note { background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; font-size:12px; padding:10px 14px; border-radius:8px; margin:12px; }
        @media (max-width:640px){ #resume_cv .rcv-summary { width:100%; } #resume_cv .rcv-sumitem { flex:1; } }
      </style>

      <div class="rcv-wrap"
            data-url="{{ $rcvFetchUrl ?? '' }}"
            data-public="{{ $rcvPublicUrl ?? '' }}"
            data-ext="{{ $rcvExt ?? '' }}"
            data-logo="{{ $rcvCompanyLogo ?? '' }}"
            data-logo-raw="{{ $rcvCompanyLogoRaw ?? '' }}"
            data-company="{{ $rcvCompanyName ?? '' }}"
            data-pdfconv="{{ $rcvConvertUrl ?? '' }}"
            data-r2-sign-url="{{ route('service_company.job-applications.r2-download-url', ['id' => $application->id]) }}"
            data-mask="{{ !empty($rcvMaskContact) ? '1' : '0' }}"
            data-b-exp="{{ $rcvBExp ?? '--' }}"
            data-b-cctc="{{ $rcvBCctc ?? '--' }}"
            data-b-ectc="{{ $rcvBEctc ?? '--' }}"
            data-b-notice="{{ $rcvBNotice ?? '--' }}"
            data-b-loc="{{ $rcvBLoc ?? '--' }}"
            data-b-presented="{{ $rcvBPresented ?? '--' }}"
            data-ispdf="{{ !empty($rcvIsPdf) ? '1' : '0' }}">
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
              <button type="button" id="rcvToggleMarkup" class="btn btn-sm btn-icon btn-light" title="Touch & Stylus Markup" style="width:28px;height:28px;border-radius:6px;margin-right:4px;display:inline-flex;align-items:center;justify-content:center;color:#4b5563;border:1px solid #e5e7eb;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
              </button>
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

        var ATOMIC_TECH_MAP = {
          'ci/cd': 'CI/CD',
          'pl/sql': 'PL/SQL',
          'tcp/ip': 'TCP/IP',
          'ui/ux': 'UI/UX',
          'c/c++': 'C/C++',
          'i/o': 'I/O',
          'os/2': 'OS/2',
          'client/server': 'Client/Server'
        };
        var STOP_WORDS = { 'and':1, 'or':1, 'in':1, 'of':1, 'for':1, 'with':1, 'the':1, 'to':1, 'on':1, 'at':1, 'a':1, 'an':1, '&':1 };

        function createKeywordEntry(rawName, type){
          var trimmed = String(rawName||'').trim();
          if(!trimmed) return null;
          var lower = trimmed.toLowerCase();

          var isAtomic = !!ATOMIC_TECH_MAP[lower];
          var subterms = [];
          if(isAtomic){
            subterms = [ATOMIC_TECH_MAP[lower]];
          } else {
            subterms = trimmed.split(/[\/,|]|\bor\b/i).map(function(s){ return s.trim(); }).filter(Boolean);
            if(!subterms.length) subterms = [trimmed];
          }

          var tokens = [];
          // 1. If split into multiple subterms (e.g. "Postman / Rest Assured")
          if(subterms.length > 1){
            subterms.forEach(function(st){
              if(tokens.indexOf(st) === -1) tokens.push(st);
            });
          }
          // 2. Multi-word phrases: extract individual meaningful words (e.g. "Automation", "Testing")
          subterms.forEach(function(st){
            var words = st.split(/[\s\-_\/]+/).map(function(w){ return w.trim(); }).filter(function(w){
              return w.length >= 2 && !STOP_WORDS[w.toLowerCase()];
            });
            if(words.length > 1 || (subterms.length === 1 && words.length > 1)){
              words.forEach(function(w){
                if(tokens.indexOf(w) === -1 && w.toLowerCase() !== trimmed.toLowerCase()) tokens.push(w);
              });
            }
          });

          // 3. Special atomic acronyms like CI/CD, C/C++ -> also expose sub-tokens CI, CD, C, C++
          if(lower === 'ci/cd' || lower === 'c/c++' || lower === 'tcp/ip' || lower === 'ui/ux' || lower === 'pl/sql'){
            var slashParts = trimmed.split(/[\/]/).map(function(p){ return p.trim(); }).filter(Boolean);
            slashParts.forEach(function(p){
              if(tokens.indexOf(p) === -1 && p.toLowerCase() !== trimmed.toLowerCase()) tokens.push(p);
            });
          }

          var tokenStates = {};
          tokens.forEach(function(tok){
            tokenStates[tok] = { active: true, count: 0 };
          });

          return {
            name: trimmed,
            type: type || 'job',
            active: false,
            subterms: subterms,
            tokens: tokens,
            tokenStates: tokenStates,
            phraseCount: 0,
            count: 0
          };
        }

        function seedKeywords(){
          jobSkills.forEach(function(s){
            var entry = createKeywordEntry(s, 'job');
            if(entry && !keywords[entry.name]) keywords[entry.name] = entry;
          });
        }

        /**
         * Intent: Build strict regex pattern for a phrase or word ensuring word boundaries and zero-consumption assertions.
         */
        function skillTermToRegexPattern(term){
          var trimmed = String(term).trim();
          if(!trimmed) return '';
          var words = trimmed.split(/[\s\-]+/).filter(Boolean);
          if(!words.length) return escRe(trimmed);

          var wp = words.map(function(w){
            var cw = escRe(w);
            if(/^[a-zA-Z]+$/.test(w)){
              if(w.length > 3 && w.toLowerCase().endsWith('s') && !w.toLowerCase().endsWith('ss')){
                cw = escRe(w.slice(0, -1)) + 's?';
              } else if(w.length > 2 && !/^(k8s|redis|express|cypress|canvas|pandas|devops|spring|jboss)$/i.test(w)){
                cw = cw + 's?';
              }
            }
            return cw;
          });

          var phrase = wp.join('[\\s\\-]+');
          phrase = phrase.replace(/\\\/|\//g, '[\\s\\-_/]*');

          var startB;
          if(/^[a-zA-Z0-9]/.test(trimmed)){
            startB = '(?<=^|[^a-zA-Z0-9_])';
          } else {
            startB = '(?<=^|[^a-zA-Z0-9_\\.])';
          }

          var endB;
          if(/[+#]$/.test(trimmed)){
            endB = '(?=$|[^a-zA-Z0-9+#])';
          } else if(/[a-zA-Z0-9]$/.test(trimmed)){
            endB = '(?=$|[^a-zA-Z0-9_])';
          } else {
            endB = '(?=$|[\\s,;:.!?()\\[\\]\\/\\-—•"\'`])';
          }

          return startB + phrase + endB;
        }

        function buildSkillRegex(skillName){
          var entry = keywords[skillName] || createKeywordEntry(skillName, 'custom');
          var patterns = [];
          entry.subterms.forEach(function(st){
            var p = skillTermToRegexPattern(st);
            if(p) patterns.push(p);
          });
          entry.tokens.forEach(function(tok){
            var tp = skillTermToRegexPattern(tok);
            if(tp && patterns.indexOf(tp) === -1) patterns.push(tp);
          });
          return patterns.length ? new RegExp('(' + patterns.join('|') + ')', 'gi') : null;
        }

        function buildRegexBundle(){
          var exactPatterns = [];
          var tokenPatterns = [];
          var exactLookup = {};

          Object.keys(keywords).forEach(function(k){
            var entry = keywords[k];
            if(!entry.active) return;

            // Main phrase patterns
            entry.subterms.forEach(function(st){
              var p = skillTermToRegexPattern(st);
              if(p){
                exactPatterns.push(p);
                exactLookup[st.toLowerCase()] = true;
              }
            });

            // Active sub-tokens
            entry.tokens.forEach(function(tok){
              if(entry.tokenStates[tok] && entry.tokenStates[tok].active){
                var tp = skillTermToRegexPattern(tok);
                if(tp && exactPatterns.indexOf(tp) === -1 && tokenPatterns.indexOf(tp) === -1){
                  tokenPatterns.push(tp);
                }
              }
            });
          });

          exactPatterns.sort(function(a,b){ return b.length - a.length; });
          tokenPatterns.sort(function(a,b){ return b.length - a.length; });

          var allPatterns = [].concat(exactPatterns, tokenPatterns);
          if(!allPatterns.length) return null;

          return {
            allRe: new RegExp('(' + allPatterns.join('|') + ')', 'gi'),
            exactLookup: exactLookup
          };
        }

        function countOccurrences(){
          Object.keys(keywords).forEach(function(k){
            var entry = keywords[k];
            var totalCount = 0;

            // 1. Check exact phrase / subterms
            var subtermPatterns = entry.subterms.map(skillTermToRegexPattern).filter(Boolean);
            if(subtermPatterns.length){
              var phraseRe = new RegExp('(' + subtermPatterns.join('|') + ')', 'gi');
              var mPhrase = resumeText ? resumeText.match(phraseRe) : null;
              entry.phraseCount = mPhrase ? mPhrase.length : 0;
              totalCount += entry.phraseCount;
            } else {
              entry.phraseCount = 0;
            }

            // 2. Check each token individually
            entry.tokens.forEach(function(tok){
              var tokPat = skillTermToRegexPattern(tok);
              if(tokPat){
                var tokRe = new RegExp(tokPat, 'gi');
                var mTok = resumeText ? resumeText.match(tokRe) : null;
                var tCnt = mTok ? mTok.length : 0;
                if(!entry.tokenStates[tok]) entry.tokenStates[tok] = { active: true, count: 0 };
                entry.tokenStates[tok].count = tCnt;
                if(entry.phraseCount === 0){
                  totalCount += tCnt;
                }
              }
            });

            entry.count = totalCount;
          });
        }

        function chipHtml(name){
          var k = keywords[name];
          var hasHits = k.count > 0 || k.phraseCount > 0;
          var cls = 'rcv-chip' + (hasHits ? ' found' : ' missing') + (k.active ? ' active' : '');
          var rm = (k.type==='custom') ? '<span class="rcv-chip-x" data-rm="'+esc(name)+'" title="Remove">&times;</span>' : '';
          
          var displayCount = k.phraseCount > 0 ? k.phraseCount : k.count;
          var mainChip = '<span class="'+cls+'" data-kw="'+esc(name)+'" title="Toggle '+esc(name)+'">'+esc(name)+'<span class="rcv-cnt">'+displayCount+'</span>'+rm+'</span>';

          return '<div class="rcv-chip-wrap" data-kw="'+esc(name)+'">' + mainChip + '</div>';
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
          Object.keys(keywords).forEach(function(k){
            if(keywords[k].count > 0 || keywords[k].phraseCount > 0) matched++; else missing++;
          });
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
          var reObj = hlOn ? buildRegexBundle() : null;
          var re = reObj ? reObj.allRe : null;

          if(rcvMode === 'html'){
            var cont = $id('rcvHtml');
            if(cont){
              cont.innerHTML = htmlOriginal;
              if(hideContact) highlightInNode(cont, REDACT_RE, 'rcv-redact');
              if(re) highlightInNode(cont, re);
            }
            hits = Array.prototype.slice.call(document.querySelectorAll('#rcvHtml mark.rcv-hit'));
          } else {
            // Dynamic text layer coordinate resolution for WASM-converted DOCX resumes
            var collector = window.agyCollectWasmDocxWordHits || (typeof agyCollectWasmDocxWordHits === 'function' ? agyCollectWasmDocxWordHits : null);
            if(window.__rcvActiveWasmDoc && window.__rcvDocxWasmHitsSearched && typeof collector === 'function'){
              var activeKws = [];
              Object.keys(keywords).forEach(function(k){
                if(keywords[k].active){
                  activeKws.push(k);
                  if(keywords[k].tokens){
                    keywords[k].tokens.forEach(function(t){
                      if(keywords[k].tokenStates[t] && keywords[k].tokenStates[t].active && activeKws.indexOf(t) === -1) activeKws.push(t);
                    });
                  }
                }
              });
              var missingKws = activeKws.filter(function(k){
                return !window.__rcvDocxWasmHitsSearched[k.toLowerCase()];
              });
              if(missingKws.length > 0){
                var newHits = collector(window.__rcvActiveWasmDoc, missingKws);
                var chipUpdated = false;
                missingKws.forEach(function(k){
                  window.__rcvDocxWasmHitsSearched[k.toLowerCase()] = true;
                  var kHits = newHits.filter(function(h){ return h.word.toLowerCase() === k.toLowerCase(); });
                  if(keywords[k] && (!keywords[k].count || keywords[k].count < kHits.length)){
                    keywords[k].count = kHits.length;
                    chipUpdated = true;
                  }
                });
                if(chipUpdated) renderChips();
                if(newHits && newHits.length){
                  window.__rcvDocxWasmHits = (window.__rcvDocxWasmHits || []).concat(newHits);
                  var pageWraps = document.querySelectorAll('#rcvPages .rcv-page');
                  pageWraps.forEach(function(wrap, idx){
                    var pNum = idx + 1;
                    var tl = wrap.querySelector('.rcv-textlayer');
                    if(!tl) return;
                    var pNewHits = newHits.filter(function(h){ return h.page === pNum; });
                    if(!pNewHits.length) return;
                    var baseW = wrap.__baseW || (wrap.clientWidth ? (wrap.clientWidth * 20) : 11905.6);
                    var baseH = wrap.__baseH || (wrap.clientHeight ? (wrap.clientHeight * 20) : 16837.8);
                    var sX = wrap.clientWidth / baseW;
                    var sY = wrap.clientHeight / baseH;

                    pNewHits.forEach(function(h){
                      var sp = document.createElement('span');
                      sp.className = 'rcv-wasm-hit-span';
                      sp.setAttribute('data-kw', h.word.toLowerCase());
                      sp.style.position = 'absolute';
                      sp.style.left = Math.round(h.x * sX) + 'px';
                      sp.style.top = Math.round(h.y * sY) + 'px';
                      sp.style.width = Math.max(10, Math.round(h.w * sX)) + 'px';
                      sp.style.height = Math.round(h.h * sY) + 'px';
                      sp.style.fontSize = Math.max(9, Math.round(h.h * sY * 0.82)) + 'px';
                      sp.style.lineHeight = Math.round(h.h * sY) + 'px';
                      sp.style.color = 'transparent';
                      sp.style.whiteSpace = 'pre';
                      sp.textContent = h.word;
                      tl.appendChild(sp);
                    });
                  });
                }
              }
            }

            var spans = document.querySelectorAll('#rcvPages .rcv-textlayer span');
            spans.forEach(function(sp){
              if(sp.dataset.orig === undefined) sp.dataset.orig = sp.textContent;
              var t = sp.dataset.orig;
              if(!t) return;
              if(!re && !hideContact){ sp.textContent = t; return; }

              var html = esc(t);
              // Redact contact information first
              if(hideContact){
                html = redactHtml(html);
              }
              // Highlight active keywords in single uniform highlight color
              if(re){
                re.lastIndex = 0;
                html = html.replace(re, function(match){
                  return '<mark class="rcv-hit">' + match + '</mark>';
                });
              }
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
          if(keywords[name].tokens){
            keywords[name].tokens.forEach(function(tok){
              if(keywords[name].tokenStates[tok]){
                keywords[name].tokenStates[tok].active = keywords[name].active;
              }
            });
          }
          renderChips(); applyHighlights();
        }


        function addKeyword(name, type){
          name = (name||'').trim();
          if(!name) return;
          var inputParts = name.split(',').map(function(s){ return s.trim(); }).filter(Boolean);
          inputParts.forEach(function(part){
            var key = Object.keys(keywords).filter(function(k){ return k.toLowerCase()===part.toLowerCase(); })[0];
            if(key){
              keywords[key].active = true;
              if(keywords[key].tokens){
                keywords[key].tokens.forEach(function(t){
                  if(keywords[key].tokenStates[t]) keywords[key].tokenStates[t].active = true;
                });
              }
            } else {
              var entry = createKeywordEntry(part, type||'custom');
              if(entry){
                entry.active = true;
                keywords[entry.name] = entry;
              }
            }
          });
          countOccurrences();
          renderChips();
          applyHighlights();
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
          var markupBtn = $id('rcvToggleMarkup');
          if(markupBtn){
            markupBtn.addEventListener('click', function(){
              if(window.__agyTouchAnnotator){
                window.__agyTouchAnnotator.toggleMarkup();
                var active = window.__agyTouchAnnotator.isMarkupActive;
                markupBtn.style.background = active ? '#e0f2fe' : '';
                markupBtn.style.color = active ? '#0284c7' : '#4b5563';
                markupBtn.style.borderColor = active ? '#0284c7' : '#e5e7eb';
              }
            });
          }
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
          // chip click toggles highlight on/off (only if not clicking remove button)
          document.addEventListener('click', function(e){
            if(e.target.closest && e.target.closest('#resume_cv .rcv-chip-x')) return;
            var chip = e.target.closest && e.target.closest('#resume_cv .rcv-chip[data-kw]');
            if(chip){ toggleKw(chip.getAttribute('data-kw')); }
          });
        }

        function withTimeout(promise, ms){
          return Promise.race([ promise, new Promise(function(_,rej){ setTimeout(function(){ rej(new Error('timeout')); }, ms); }) ]);
        }

        async function resolveRcvSources(){
          if(typeof window.ensureDownloadCenterR2Urls === 'function'){
            try { await window.ensureDownloadCenterR2Urls(); } catch(e){}
          }
          var rdc = document.getElementById('rdcData');
          var wrap = document.querySelector('#resume_cv .rcv-wrap');
          var r2Direct = (rdc && rdc.getAttribute('data-r2-direct-url')) || '';
          var origR2Direct = (rdc && rdc.getAttribute('data-original-r2-direct-url')) || '';
          var r2Public = (rdc && rdc.getAttribute('data-r2-public-url')) || '';
          var origR2Public = (rdc && rdc.getAttribute('data-original-r2-public-url')) || '';
          var dlUrl = (rdc && rdc.getAttribute('data-dl-url')) || '';
          var wrapUrl = (wrap && wrap.getAttribute('data-url')) || '';
          var wrapPublic = (wrap && wrap.getAttribute('data-public')) || '';

          var signUrl = (rdc && rdc.getAttribute('data-r2-sign-url')) || (wrap && wrap.getAttribute('data-r2-sign-url')) || '';
          if(!r2Direct && !origR2Direct && signUrl){
            try {
              var sResp = await fetch(signUrl, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
              });
              if(sResp.ok){
                var data = await sResp.json();
                if(data && data.status === 'success'){
                  r2Direct = data.r2_direct_url || '';
                  origR2Direct = data.original_r2_direct_url || '';
                  r2Public = data.r2_public_url || '';
                  origR2Public = data.original_r2_public_url || '';
                  if(rdc){
                    if(r2Direct) rdc.setAttribute('data-r2-direct-url', r2Direct);
                    if(r2Public) rdc.setAttribute('data-r2-public-url', r2Public);
                    if(origR2Direct) rdc.setAttribute('data-original-r2-direct-url', origR2Direct);
                    if(origR2Public) rdc.setAttribute('data-original-r2-public-url', origR2Public);
                  }
                }
              }
            } catch(e){}
          }

          var sources = [];
          [r2Direct, origR2Direct, r2Public, origR2Public, wrapUrl, dlUrl, wrapPublic].forEach(function(u){
            if(u && sources.indexOf(u) === -1) sources.push(u);
          });
          return {
            sources: sources,
            primaryDirectUrl: r2Direct || origR2Direct || r2Public || origR2Public || wrapUrl || dlUrl,
            downloadUrl: origR2Direct || r2Direct || origR2Public || r2Public || dlUrl || wrapUrl
          };
        }

        async function fetchResumeBytesWithFallback(sources){
          for(var i = 0; i < sources.length; i++){
            var src = sources[i];
            if(!src) continue;
            try {
              var isDirectR2 = src.indexOf('file.jero.works') !== -1 || src.indexOf('r2.cloudflarestorage.com') !== -1;
              var fetchOpts = isDirectR2
                ? { mode: 'cors', cache: 'default' }
                : { credentials: 'same-origin', cache: 'default' };
              var resp = await withTimeout(fetch(src, fetchOpts), 25000);
              if(resp.ok){
                var ab = await resp.arrayBuffer();
                if(ab && ab.byteLength > 100){
                  return new Uint8Array(ab);
                }
              }
            } catch(err){
              console.warn('Failed to fetch resume bytes from source ' + src + ':', err);
            }
          }
          throw new Error('Unable to load resume file bytes from available sources.');
        }

        async function extractDocxText(docxBytes){
          try {
            if(typeof window.__rsEnsureJsZip === 'function') await window.__rsEnsureJsZip();
            else if(typeof rsEnsureJsZip === 'function') await rsEnsureJsZip();
            else if(!window.JSZip && typeof loadScript === 'function') {
              try { await loadScript('/js/jszip.min.js'); } catch(e){}
            }
            var JSZipLib = window.JSZip;
            if(!JSZipLib) return '';
            var zip = await JSZipLib.loadAsync(docxBytes);
            var docXmlFile = zip.file('word/document.xml');
            if(!docXmlFile) return '';
            var xmlStr = await docXmlFile.async('string');
            var matches = xmlStr.match(/<w:t\b[^>]*>([\s\S]*?)<\/w:t>/gi) || [];
            var textParts = [];
            for(var i = 0; i < matches.length; i++){
              var t = matches[i].replace(/<[^>]+>/g, '').trim();
              if(t) textParts.push(t);
            }
            return textParts.join(' ');
          } catch(e){
            console.warn('extractDocxText error:', e);
            return '';
          }
        }

        function showDocUnsupportedNotice(downloadUrl){
          $id('rcvLoading').style.display = 'none';
          $id('rcvPages').style.display = 'none';
          var ifr = $id('rcvIframe');
          if(ifr) ifr.style.display = 'none';

          var wrap = document.querySelector('#resume_cv .rcv-wrap');
          if(!downloadUrl){
            downloadUrl = (wrap && (wrap.getAttribute('data-url') || wrap.getAttribute('data-public'))) || '#';
            var rdc = document.getElementById('rdcData');
            if(rdc){
              var r2Orig = rdc.getAttribute('data-original-r2-direct-url') || rdc.getAttribute('data-original-r2-public-url') || rdc.getAttribute('data-dl-url');
              if(r2Orig) downloadUrl = r2Orig;
            }
          }

          var openNew = $id('rcvOpenNew');
          if(openNew && downloadUrl) openNew.href = downloadUrl;
          var dlItem = document.querySelector('#rcvDlMenu a.rcv-dl-item');
          if(dlItem && downloadUrl) dlItem.href = downloadUrl;

          // Notification banner directly ABOVE the viewer
          var viewport = $id('rcvViewport');
          if(!document.getElementById('rcvDocNotice') && viewport){
            var notice = document.createElement('div');
            notice.id = 'rcvDocNotice';
            notice.style.cssText = 'background:#fffbeb;border:1px solid #fef3c7;border-left:4px solid #f59e0b;padding:12px 16px;border-radius:8px;margin:12px 14px 0;display:flex;align-items:center;justify-content:space-between;gap:12px;box-shadow:0 1px 2px rgba(0,0,0,0.05);';
            notice.innerHTML = '<div style="display:flex;align-items:center;gap:10px;">' +
              '<svg style="width:20px;height:20px;color:#d97706;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
              '<span style="font-size:12.5px;font-weight:500;color:#92400e;line-height:1.4;">This Word (.doc) file version isn’t supported. Please upload the resume as PDF or download the original resume.</span>' +
              '</div>' +
              '<a id="rcvDocDownloadBtn" href="' + downloadUrl + '" target="_blank" class="rcv-btn" style="background:#f59e0b;color:#fff;border:none;padding:7px 14px;border-radius:7px;font-size:11.5px;font-weight:600;text-decoration:none;white-space:nowrap;cursor:pointer;flex-shrink:0;">Download Original (.doc)</a>';
            viewport.parentNode.insertBefore(notice, viewport);
          }

          // Inside viewport: clean placeholder (NO popup alert)
          if(viewport){
            viewport.innerHTML = '<div style="color:#cbd5e1;font-size:13px;padding:60px 20px;text-align:center;max-width:480px;">' +
              '<svg style="width:40px;height:40px;margin:0 auto 12px;display:block;opacity:0.6;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="15" x2="15" y2="15"/></svg>' +
              'Preview is unavailable for legacy binary (.doc) Word files.<br>Please download the original file above or upload the resume as PDF.' +
              '</div>';
          }

          countOccurrences();
          renderChips();
        }

        async function renderPdf(source, fallback, attempt){
          attempt = attempt || 0;
          var fail = function(msg){ if(typeof fallback === 'function'){ fallback(); } else { showIframe(msg || 'Showing résumé in preview mode.'); } };
          if(typeof pdfjsLib === 'undefined'){ fail('PDF viewer library not available.'); return; }
          try { var ver = pdfjsLib.version || '2.11.338'; pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/'+ver+'/pdf.worker.min.js'; } catch(e){}
          var pagesEl = $id('rcvPages');

          var bytes;
          try {
            if(source instanceof Uint8Array){
              bytes = source;
            } else if(source instanceof ArrayBuffer){
              bytes = new Uint8Array(source);
            } else if(typeof Blob !== 'undefined' && source instanceof Blob){
              var ab = await source.arrayBuffer();
              bytes = new Uint8Array(ab);
            } else if(typeof source === 'string'){
              var resp = await withTimeout(fetch(source, { credentials:'same-origin' }), 35000);
              if(resp.status === 202){
                var wait = Math.min(2500 + (attempt * 250), 6000);
                $id('rcvLoading').style.display = 'block';
                $id('rcvLoading').textContent = 'Preparing résumé preview…';
                if(attempt < 40){
                  setTimeout(function(){ renderPdf(source, fallback, attempt + 1); }, wait);
                  return;
                }
                throw new Error('conversion-timeout');
              }
              if(!resp.ok) throw new Error('HTTP '+resp.status);
              var fetchedAb = await resp.arrayBuffer();
              bytes = new Uint8Array(fetchedAb);
            } else {
              throw new Error('Invalid PDF source');
            }
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
          var extractedText = '';
          for(var p=1; p<=doc.numPages; p++){
            var page = await doc.getPage(p);
            var base = page.getViewport({ scale:1 });
            var scale = (containerW-2)/base.width;
            var viewport = page.getViewport({ scale:scale });
            var wrap = document.createElement('div'); wrap.className='rcv-page';
            wrap.style.width = Math.floor(viewport.width)+'px';
            wrap.style.height = Math.floor(viewport.height)+'px';
            wrap.__baseW = base.width * 20;
            wrap.__baseH = base.height * 20;
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
            extractedText += tc.items.map(function(i){ return i.str; }).join(' ') + '\n';
            var isDocxWasm = !!(window.__rcvActiveWasmDoc || (window.__rcvDocxWasmHits && window.__rcvDocxWasmHits.length));
            if(!isDocxWasm){
              try {
                var task = pdfjsLib.renderTextLayer({ textContent:tc, container:tl, viewport:viewport, textDivs:[] });
                if(task && task.promise) await task.promise;
              } catch(e){}
            } else {
              // Exact WASM twip coordinates for DOCX resumes: strict h.page === p, zero ghost marks, exact word widths
              var pHits = (window.__rcvDocxWasmHits || []).filter(function(h){ return h.page === p; });
              var sX = viewport.width / (base.width * 20);
              var sY = viewport.height / (base.height * 20);

              pHits.forEach(function(h){
                var sp = document.createElement('span');
                sp.className = 'rcv-wasm-hit-span';
                sp.setAttribute('data-kw', h.word.toLowerCase());
                sp.style.position = 'absolute';
                sp.style.left = Math.round(h.x * sX) + 'px';
                sp.style.top = Math.round(h.y * sY) + 'px';
                sp.style.width = Math.max(10, Math.round(h.w * sX)) + 'px';
                sp.style.height = Math.round(h.h * sY) + 'px';
                sp.style.fontSize = Math.max(9, Math.round(h.h * sY * 0.82)) + 'px';
                sp.style.lineHeight = Math.round(h.h * sY) + 'px';
                sp.style.color = 'transparent';
                sp.style.whiteSpace = 'pre';
                sp.textContent = h.word;
                tl.appendChild(sp);
              });
            }
          }
          if(extractedText.trim() && (!resumeText || extractedText.length > resumeText.length)){
            resumeText = extractedText;
            window.__rcvResumeText = resumeText;
          }
          countOccurrences();
          // default: highlight job skills that are present
          Object.keys(keywords).forEach(function(k){
            var hasHits = (keywords[k].count > 0 || keywords[k].phraseCount > 0);
            keywords[k].active = (keywords[k].type==='job' && hasHits);
            if(keywords[k].tokens){
              keywords[k].tokens.forEach(function(tok){
                var ts = keywords[k].tokenStates[tok];
                if(ts) ts.active = (keywords[k].active && ts.count > 0);
              });
            }
          });
          renderChips();
          applyHighlights();

          // Phase 7: Mount touch gesture & stylus annotation overlays
          try {
            if(typeof AgyTouchAnnotator === 'function'){
              if(!window.__agyTouchAnnotator){
                window.__agyTouchAnnotator = new AgyTouchAnnotator('rcvPages');
              }
              window.__agyTouchAnnotator.mountPageOverlays();
            }
          } catch(annotatorErr){
            console.warn('AgyTouchAnnotator mount skipped:', annotatorErr);
          }
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
          window.__rcvResumeText = resumeText;
          countOccurrences();
          Object.keys(keywords).forEach(function(k){
            var hasHits = (keywords[k].count > 0 || keywords[k].phraseCount > 0);
            keywords[k].active = (keywords[k].type==='job' && hasHits);
            if(keywords[k].tokens){
              keywords[k].tokens.forEach(function(tok){
                var ts = keywords[k].tokenStates[tok];
                if(ts) ts.active = (keywords[k].active && ts.count > 0);
              });
            }
          });
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

        async function init(){
          if(inited) return;
          var pane = document.getElementById('resume_cv');
          if(!pane || pane.getAttribute('data-rcv-inited') === '1') return;  // another (stale) instance already owns this DOM
          pane.setAttribute('data-rcv-inited', '1');
          inited = true;

          if(window.__rcvActiveWasmDoc && typeof window.__rcvActiveWasmDoc.free === 'function'){
            try { window.__rcvActiveWasmDoc.free(); } catch(e){}
          }
          window.__rcvActiveWasmDoc = null;
          window.__rcvDocxWasmHits = [];
          window.__rcvDocxWasmHitsSearched = {};

          var wrap = pane.querySelector('.rcv-wrap');
          hideContact = wrap.getAttribute('data-mask') !== '0';
          var fallbackUrl = wrap.getAttribute('data-url');
          var isPdf = wrap.getAttribute('data-ispdf')==='1';
          var ext = (wrap.getAttribute('data-ext') || '').toLowerCase();

          seedKeywords();
          renderChips();
          wireControls();

          // 1. Handle Legacy .doc format (Direct inline banner above viewer, no popup alert)
          if(ext === 'doc'){
            showDocUnsupportedNotice();
            return;
          }

          // 2. Resolve Direct R2 Sources
          var rcvInfo = await resolveRcvSources();
          if(rcvInfo.downloadUrl){
            var openNew = $id('rcvOpenNew');
            if(openNew) openNew.href = rcvInfo.downloadUrl;
            var dlItem = document.querySelector('#rcvDlMenu a.rcv-dl-item');
            if(dlItem) dlItem.href = rcvInfo.downloadUrl;
          }

          // 3. Handle PDF format
          if(isPdf || ext === 'pdf'){
            try {
              $id('rcvLoading').textContent = 'Loading résumé from Cloudflare R2…';
              var pdfBytes = await fetchResumeBytesWithFallback(rcvInfo.sources);
              await renderPdf(pdfBytes);
            } catch(pdfErr){
              console.warn('Direct R2 PDF fetch failed, trying fallback:', pdfErr);
              if(fallbackUrl){
                renderPdf(fallbackUrl, function(){ showIframe(); });
              } else {
                showIframe('Résumé file is not available for preview.');
              }
            }
            return;
          }

          // 4. Handle DOCX format via High-Fidelity Client Converter with Searchable Text Layer
          if(ext === 'docx'){
            try {
              $id('rcvLoading').textContent = 'Loading Word document from Cloudflare R2…';
              var docxBytes = await fetchResumeBytesWithFallback(rcvInfo.sources);
              $id('rcvLoading').textContent = 'Converting Word document to PDF…';

              // Extract text directly from docx as baseline guarantee
              try {
                var docxText = await extractDocxText(docxBytes);
                if(docxText){
                  resumeText = docxText;
                  window.__rcvResumeText = docxText;
                }
              } catch(e){}

              var pdfBlob = null;
              var wasmConverter = window.__renderAgyStrategy2WasmDocxToPdfBlob || (typeof renderAgyStrategy2WasmDocxToPdfBlob === 'function' ? renderAgyStrategy2WasmDocxToPdfBlob : null);
              var ensureWasm = window.__rsEnsureOpenDocLab || (typeof rsEnsureOpenDocLab === 'function' ? rsEnsureOpenDocLab : null);
              if(!wasmConverter && typeof ensureWasm === 'function'){
                await ensureWasm(false);
                wasmConverter = window.__renderAgyStrategy2WasmDocxToPdfBlob || (typeof renderAgyStrategy2WasmDocxToPdfBlob === 'function' ? renderAgyStrategy2WasmDocxToPdfBlob : null);
              }
              if(typeof wasmConverter === 'function'){
                var allKwList = Object.keys(keywords);
                pdfBlob = await wasmConverter(docxBytes, {
                  keywords: allKwList,
                  fullText: resumeText || docxText || '',
                  searchableText: true,
                  statusCallback: function(statusMsg){
                    var loadingEl = $id('rcvLoading');
                    if(loadingEl) loadingEl.textContent = statusMsg || 'Rendering Word document via WASM…';
                  }
                });
              }

              if(!pdfBlob){
                var fallbackConverter = window.__renderAgyTrueSectionDocxToPdfBlob || (typeof renderAgyTrueSectionDocxToPdfBlob === 'function' ? renderAgyTrueSectionDocxToPdfBlob : null);
                if(typeof fallbackConverter === 'function'){
                  try {
                    pdfBlob = await fallbackConverter(docxBytes, {
                      searchableText: true,
                      statusCallback: function(statusMsg){
                        var loadingEl = $id('rcvLoading');
                        if(loadingEl) loadingEl.textContent = statusMsg || 'Rendering Word document…';
                      }
                    });
                  } catch(fbErr){}
                }
              }

              if(pdfBlob){
                await renderPdf(pdfBlob, function(){
                  renderDocx(rcvInfo.primaryDirectUrl || fallbackUrl);
                });
                return;
              }
              throw new Error('All DOCX converters failed');
            } catch(docxErr){
              console.warn('DOCX conversion failed, falling back to Mammoth/iframe:', docxErr);
              if(rcvInfo.primaryDirectUrl || fallbackUrl){
                renderDocx(rcvInfo.primaryDirectUrl || fallbackUrl);
              } else {
                showIframe();
              }
            }
            return;
          }

          // Fallback for other extensions
          if(fallbackUrl){
            renderDocx(fallbackUrl);
          } else {
            showIframe();
            renderChips();
          }
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
                            $cnt = 0;
                          if (empty($notesArr) || count($notesArr) === 0) {
                              echo '<li class="p-4 text-center text-muted notes-placeholder">No recruiter notes added yet.</li>';
                          } else {
                              foreach ($notesArr as $key => $notes) {
                                  $date = $notes->created_at;
                                  $noteDate = date('d-M-Y', strtotime($date));
                                  $noteTitme = date('h:i A', strtotime($date));
                                  $posted_by_image = '';
                                  if (! empty($notes->posted_by_image)) {
                                      $posted_by_image = @$notes->posted_by_image;
                                  }

                                  $bgTextDecore = GeneralMethod::icoinText(@$notes->name);

                                  ?>
                        <li class="media" id="note-{{ $notes->id }}">
                          <div class="timeline-item">
                            <?php if ($cnt % 3 == 0) { ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"></rect>
                                  <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3"></path>
                                  <path d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z" fill="#000000"></path>
                                </g>
                              </svg>
                              </span> </div>
                            <?php } elseif ($cnt % 3 == 1) { ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-warning svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                  <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
                                  <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
                                </g>
                              </svg>
                              </span> </div>
                            <?php } elseif ($cnt % 3 == 2) { ?>
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
                         <?php $cnt++;
                              }
                          }	?>
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
            @if(!empty(trim((string)@$application->careersummary)))
              <p class="summary-text" style="font-size:12px; line-height:1.65; color:#334155; margin:0;">{{@$application->careersummary}}</p>
            @else
              <p class="po-no-notes" style="font-size:12px; font-style:italic; color:#94a3b8; margin:0;">No professional summary provided.</p>
            @endif
          </section>

          <!-- ===== REASON FOR JOB CHANGE CARD (Rule 14, Rule 17) ===== -->
          <section class="cardshow" id="reason_for_job_change_card">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: #fffbeb; color: #d97706;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a4.5 4.5 0 0 0 4.5-4.5v0a4.5 4.5 0 0 0-4.5-4.5H11"/><polyline points="14 6 11 10 14 14"/><circle cx="6" cy="5" r="3"/>
                </svg>
              </div>
              <h3 class="card-title">Reason for Job Change</h3>
              @if($editComman)
              <button type="button" class="jw-card-edit-btn editReasonForChange" id="btn_edit_reason_change" title="Edit Reason for Job Change">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
              </button>
              @endif
            </div>
            @if(!empty(@$application->reason_for_job_change))
              <p class="reason-summary-text reason-change-display-text" id="drawer_reason_display_text" style="margin:0; color:#334155; line-height:1.65; word-break:break-word;">{{ @$application->reason_for_job_change }}</p>
            @else
              <p class="reason-summary-text reason-change-display-text text-muted" id="drawer_reason_display_text" style="margin:0; font-style:italic; color:#94a3b8; line-height:1.65;">No reason for job change specified.</p>
            @endif
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
                            $dedupedSkills = [];
                            foreach($jobSkill as $skill_item)
                            {
                              $sObj = (object) $skill_item;
                              $nameKey = mb_strtolower(trim((string) ($sObj->skill_name ?? '')));
                              if ($nameKey === '') {
                                continue;
                              }
                              if (!isset($dedupedSkills[$nameKey])) {
                                $dedupedSkills[$nameKey] = clone $sObj;
                              } else {
                                if (empty($dedupedSkills[$nameKey]->skill_value) && !empty($sObj->skill_value)) {
                                  $dedupedSkills[$nameKey]->skill_value = $sObj->skill_value;
                                }
                                if ((int) ($dedupedSkills[$nameKey]->rating ?? 0) < (int) ($sObj->rating ?? 0)) {
                                  $dedupedSkills[$nameKey]->rating = $sObj->rating;
                                }
                              }
                            }

                            $html		=	'<div class="row">';	
                          foreach($dedupedSkills as $skill_val)
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
                            '.ucwords(strtolower(trim((string) $skill_val->skill_name))).'
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

          <section class="cardshow">
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
              <h3 class="card-title">Recruiter Comments</h3>
              @if($editComman)
              <button type="button" class="jw-card-edit-btn editRecruiterNote">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
              </button>
              @endif
            </div>
            @if(!empty($application->recruiter_note))
              <div class="po-recruiter-note-text jw-recruiter-note-display" data-candidate-id="{{ @$application->id }}">{!! $application->recruiter_note !!}</div>
            @else
              <p class="po-no-notes jw-recruiter-note-display" data-candidate-id="{{ @$application->id }}">No recruiter comments added yet.</p>
            @endif
          </section>

          <!-- ===== REDESIGNED COMPENSATION SECTION (Rule 14, Rule 17, Rule 18) ===== -->
          @php
            // Authoritative Currency & Symbol Resolution
            $jobCurrencyKey = $isDirectCandidate ? (@$application->current_ctc_cur ?? 0) : ($jobData->jobCurrency ?? @$application->current_ctc_cur ?? 0);
            $currCode = \App\Services\CurrencyService::resolveCurrencyCode($jobCurrencyKey);
            $currencySymbolsMap = \App\Services\CurrencyService::getCurrencySymbols();
            $currSymbol = $currencySymbolsMap[$currCode] ?? ($currCode === 'INR' ? '₹' : ($currCode === 'USD' ? '$' : ($currCode === 'EUR' ? '€' : ($currCode === 'GBP' ? '£' : ($currCode === 'AED' ? 'د.إ' : $currCode)))));
            
            $ectcJobCurrencyKey = $isDirectCandidate ? (@$application->current_ectc_cur ?? $jobCurrencyKey) : ($jobData->jobCurrency ?? @$application->current_ectc_cur ?? $jobCurrencyKey);
            $ectcCurrCode = \App\Services\CurrencyService::resolveCurrencyCode($ectcJobCurrencyKey);
            $ectcCurrSymbol = $currencySymbolsMap[$ectcCurrCode] ?? ($currencySymbolsMap[$currCode] ?? $currSymbol);

            $payFormatViewSalary = GeneralMethod::formatViewSalary($application->ctc_yearly, $symbols, $jobMode);
            $billFormatViewSalary = GeneralMethod::formatViewSalary($application->ectc_yearly, $symbols, $jobMode);
            $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
            $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);

            $current_ctc_mode = ($application->condidate_type == 'bench_condidate') ? @$application->pay_rate_mode : @$jobMode;
            if ($application->condidate_type == 'bench_condidate') {
                $ctc = '*******';
            }
            $current_ectc_mode = @$jobMode;
            $hasOfferInHand = strtolower(trim((string)@$application->oh)) === 'yes';
          @endphp

          <section class="cardshow comp-card">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: #fff7ed; color: #ea580c;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                  <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
              </div>
              <h3 class="card-title">Compensation Details</h3>
            </div>

            <div class="comp-items-list">
              {{-- 1. Current Salary / Pay Rate --}}
              @if($application->condidate_type=='other' || $application->condidate_type=='bench_condidate')
                <div class="comp-item-row">
                  <div class="comp-item-left">
                    <div class="comp-row-icon comp-icon-green" title="Current Salary">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 7a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4c0 1.5.8 2.8 2 3.5V19a3 3 0 0 0 3 3h4a3 3 0 0 0 3-3v-8.5c1.2-.7 2-2 2-3.5z"></path>
                        <line x1="12" y1="10" x2="12" y2="16"></line>
                        <path d="M10 12h4"></path>
                      </svg>
                    </div>
                    <span class="comp-label">{{ (@$application->condidate_type=='other') ? 'Current Salary' : 'Pay Rate' }}</span>
                  </div>
                  <span class="comp-value">
                    @if($ctc !== '*******')
                      <span class="comp-curr-symbol">{{ $currSymbol }}</span>{{ rtrim(rtrim((string)$ctc, '0'), '.') }} {{ $salaryMode[$current_ctc_mode ?? 0] ?? 'Annual' }}
                    @else
                      {{ $ctc }}
                    @endif
                  </span>
                </div>
              @endif

              {{-- 2. Expected Salary / Bill Rate --}}
              <div class="comp-item-row">
                <div class="comp-item-left">
                  <div class="comp-row-icon comp-icon-blue" title="Expected Salary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="18" y1="20" x2="18" y2="10"></line>
                      <line x1="12" y1="20" x2="12" y2="4"></line>
                      <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                  </div>
                  <span class="comp-label">{{ ($application->condidate_type=='other') ? 'Expected Salary' : 'Bill Rate' }}</span>
                </div>
                <span class="comp-value">
                  <span class="comp-curr-symbol">{{ $ectcCurrSymbol }}</span>{{ rtrim(rtrim((string)@$ectc, '0'), '.') }} {{ $salaryMode[$current_ectc_mode ?? 0] ?? 'Annual' }}
                </span>
              </div>

              {{-- 3. Compensation Type --}}
              <div class="comp-item-row">
                <div class="comp-item-left">
                  <div class="comp-row-icon comp-icon-purple" title="Compensation Type">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                      <polyline points="2 17 12 22 22 17"></polyline>
                      <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                  </div>
                  <span class="comp-label">Compensation Type</span>
                </div>
                <span class="comp-badge-purple">{{ $salaryMode[$current_ectc_mode ?? 0] ?? 'Annual' }}</span>
              </div>

              {{-- 4. Currency (Replaces "Salary Type" with Currency) --}}
              <div class="comp-item-row">
                <div class="comp-item-left">
                  <div class="comp-row-icon comp-icon-amber" title="Currency">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <ellipse cx="12" cy="7" rx="9" ry="3"></ellipse>
                      <path d="M3 7v6c0 1.66 4.03 3 9 3s9-1.34 9-3V7"></path>
                      <path d="M3 13v6c0 1.66 4.03 3 9 3s9-1.34 9-3v-6"></path>
                    </svg>
                  </div>
                  <span class="comp-label">Currency</span>
                </div>
                <span class="comp-value">
                  {{ $currCode }}{{ (!empty($currSymbol) && $currSymbol !== $currCode) ? ' (' . $currSymbol . ')' : '' }}
                </span>
              </div>

              {{-- 5. Employment Type --}}
              <div class="comp-item-row">
                <div class="comp-item-left">
                  <div class="comp-row-icon comp-icon-red" title="Employment Type">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                      <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                  </div>
                  <span class="comp-label">Employment Type</span>
                </div>
                <span class="comp-value">{{ @$application->employmentType ?: 'Full Time' }}</span>
              </div>

              {{-- 6. Any Offer from other organization (if condidate_type == 'other') --}}
              @if($application->condidate_type=='other')
                <div class="comp-item-row">
                  <div class="comp-item-left">
                    <div class="comp-row-icon comp-icon-mint" title="Any Offer from other organization">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <path d="m9 15 2 2 4-4"></path>
                      </svg>
                    </div>
                    <span class="comp-label">Any Offer from other organization</span>
                  </div>
                  <span class="{{ $hasOfferInHand ? 'comp-badge-green' : 'comp-badge-gray' }}">{{ ucwords(@$application->oh ?: 'No') }}</span>
                </div>
              @endif
            </div>

            {{-- 7. Salary Details Comments --}}
            @if($application->condidate_type=='other')
              <div class="comp-comments-box">
                <div class="comp-comments-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    <line x1="8" y1="9" x2="16" y2="9"></line>
                    <line x1="8" y1="13" x2="14" y2="13"></line>
                  </svg>
                </div>
                <div class="comp-comments-content">
                  <div class="comp-comments-label">Salary Details Comments</div>
                  <div class="comp-comments-text">{{ !empty(trim((string)@$application->comment)) ? ucwords(@$application->comment) : 'In Progress' }}</div>
                </div>
              </div>
            @endif
          </section>

          <!-- ===== REDESIGNED OWNERSHIP SECTION (Rule 14, Rule 17, Rule 18) ===== -->
          @php
              $hiringHrName = '';
              if(@$clientjobAssignListCaptain){
                  $hiringHrName = ucwords(strtolower(@$clientjobAssignListCaptain));
              } else if(!empty($client_contuser_arr)){
                  $names = [];
                  foreach($client_contuser_arr as $cpn){
                      $cpn = (object) $cpn;
                      if(!empty($cpn->contact_person_name)){
                          $names[] = ucwords(strtolower($cpn->contact_person_name));
                      }
                  }
                  $hiringHrName = implode(', ', $names);
              }

              $targetJobId = $jobId ?? @$application->job_id ?? @$application->jobId ?? 0;
              $hdrCompanyOrClient = $targetJobId > 0 ? GeneralMethod::companyDetailsByClient($targetJobId) : '';
              if (empty($hdrCompanyOrClient)) {
                  $companies = $submittedByCompany ?? (!empty($application->submitted_by) ? DB::table('companies')->select('companies.company_name')->join('users','users.company_id','companies.id')->where('users.id', $application->submitted_by)->first() : null);
                  $hdrCompanyOrClient = @$companies->company_name ?? '';
              }

              $originalDate = @$application->created_at;
              $ownerDate    = !empty($originalDate) ? date("d-M-Y", strtotime($originalDate)) : '—';
              $ownerTime    = !empty($originalDate) ? date("h:i A", strtotime($originalDate)) : '—';
          @endphp

          <section class="cardshow own-card">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: #f5f3ff; color: #7c3aed;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
              </div>
              <h3 class="card-title">Ownership</h3>
            </div>

            <div class="own-items-list">
              {{-- 1. Hiring HR --}}
              <div class="own-item-row">
                <div class="own-row-icon own-icon-blue" title="Hiring HR">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                </div>
                <div class="own-item-body">
                  <div class="own-label">Hiring HR</div>
                  <div class="own-value">{{ !empty($hiringHrName) ? $hiringHrName : '—' }}</div>
                </div>
              </div>

              {{-- 2. Recruiter Name --}}
              <div class="own-item-row">
                <div class="own-row-icon own-icon-green" title="Recruiter Name">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                  </svg>
                </div>
                <div class="own-item-body">
                  <div class="own-label">Recruiter Name</div>
                  <div class="own-value">{{ !empty(@$posted_by) ? ucwords(@$posted_by) : '—' }}</div>
                </div>
              </div>

              {{-- 3. Company Name --}}
              <div class="own-item-row">
                <div class="own-row-icon own-icon-amber" title="Company Name">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="2" width="16" height="20" rx="2"></rect>
                    <path d="M9 22v-4h6v4"></path>
                    <path d="M8 6h.01"></path><path d="M16 6h.01"></path>
                    <path d="M8 10h.01"></path><path d="M16 10h.01"></path>
                    <path d="M8 14h.01"></path><path d="M16 14h.01"></path>
                  </svg>
                </div>
                <div class="own-item-body">
                  <div class="own-label">Company Name</div>
                  <div class="own-value">{{ !empty($hdrCompanyOrClient) ? ucwords(@$hdrCompanyOrClient) : '—' }}</div>
                </div>
              </div>

              {{-- 4. Upload Date & Time --}}
              <div class="own-item-row">
                <div class="own-row-icon own-icon-purple" title="Upload Date & Time">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                </div>
                <div class="own-item-body">
                  <div class="own-label">Upload Date &amp; Time</div>
                  <div class="own-datetime-pill">
                    <span class="own-dt-item">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                      </svg>
                      {{ @$ownerDate }}
                    </span>
                    <span class="own-dt-sep">|</span>
                    <span class="own-dt-item">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                      </svg>
                      {{ @$ownerTime }}
                    </span>
                    <span class="own-dt-sep">|</span>
                    <span class="own-dt-item">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                      </svg>
                      IST
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </section>


          <!-- ===== REDESIGNED CONTACT DETAILS SECTION (Rule 14, Rule 17, Rule 18) ===== -->
          @if($application->condidate_type=='other')
          <section class="cardshow cnt-card">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: #eff6ff; color: #2563eb;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <h3 class="card-title">Contact Details</h3>
            </div>

            <div class="cnt-items-list">
              {{-- 1. Email ID-1 --}}
              <div class="cnt-item-row">
                <div class="cnt-row-icon cnt-icon-blue" title="Email ID-1">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                  </svg>
                </div>
                <div class="cnt-item-body">
                  <div class="cnt-label">Email ID-1</div>
                  <div class="cnt-value">
                    @if(!empty(@$application->email))
                      <a href="mailto:{{ @$application->email }}" class="cnt-val-link">{{ @$application->email }}</a>
                    @else
                      -
                    @endif
                  </div>
                </div>
                @if(!empty(@$application->email))
                  <button type="button" class="cnt-copy-btn" onclick="copyContactText('{{ addslashes(@$application->email) }}', this)" title="Copy Email">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                      <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                  </button>
                @endif
              </div>

              {{-- 2. Email ID-2 --}}
              <div class="cnt-item-row">
                <div class="cnt-row-icon cnt-icon-purple" title="Email ID-2">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                  </svg>
                </div>
                <div class="cnt-item-body">
                  <div class="cnt-label">Email ID-2</div>
                  <div class="cnt-value">
                    @if(!empty(@$application->secondrary_email))
                      <a href="mailto:{{ @$application->secondrary_email }}" class="cnt-val-link">{{ @$application->secondrary_email }}</a>
                    @else
                      -
                    @endif
                  </div>
                </div>
                @if(!empty(@$application->secondrary_email))
                  <button type="button" class="cnt-copy-btn" onclick="copyContactText('{{ addslashes(@$application->secondrary_email) }}', this)" title="Copy Secondary Email">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                      <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                  </button>
                @endif
              </div>

              {{-- 3. Contact Number-1 --}}
              <div class="cnt-item-row">
                <div class="cnt-row-icon cnt-icon-green" title="Contact Number-1">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                  </svg>
                </div>
                <div class="cnt-item-body">
                  <div class="cnt-label">Contact Number-1</div>
                  <div class="cnt-phone-val">
                    @if(!empty(@$application->phone))
                      {!! GeneralMethod::formatContactPhoneHtml(!empty($application->phone_code) ? $application->phone_code : '+91', @$application->phone) !!}
                    @else
                      -
                    @endif
                  </div>
                </div>
                @if(!empty(@$application->phone))
                  @php
                    $copyPhone1 = GeneralMethod::formatContactPhoneForTel(!empty($application->phone_code) ? $application->phone_code : '+91', @$application->phone);
                  @endphp
                  <button type="button" class="cnt-copy-btn" onclick="copyContactText('{{ addslashes($copyPhone1) }}', this)" title="Copy Phone Number">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                      <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                  </button>
                @endif
              </div>

              {{-- 4. Contact Number-2 --}}
              <div class="cnt-item-row">
                <div class="cnt-row-icon cnt-icon-amber" title="Contact Number-2">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                  </svg>
                </div>
                <div class="cnt-item-body">
                  <div class="cnt-label">Contact Number-2</div>
                  <div class="cnt-phone-val">
                    @if(!empty(@$application->secondary_mobile))
                      {!! GeneralMethod::formatContactPhoneHtml(!empty($application->second_phone_code) ? $application->second_phone_code : '+91', @$application->secondary_mobile) !!}
                    @else
                      -
                    @endif
                  </div>
                </div>
                @if(!empty(@$application->secondary_mobile))
                  @php
                    $copyPhone2 = GeneralMethod::formatContactPhoneForTel(!empty($application->second_phone_code) ? $application->second_phone_code : '+91', @$application->secondary_mobile);
                  @endphp
                  <button type="button" class="cnt-copy-btn" onclick="copyContactText('{{ addslashes($copyPhone2) }}', this)" title="Copy Secondary Phone">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                      <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                  </button>
                @endif
              </div>
            </div>

            {{-- 5. WhatsApp & LinkedIn Actions --}}
            @if(!empty(@$application->phone))
              @php
                $waPhone = GeneralMethod::formatContactPhoneForTel(!empty($application->phone_code) ? $application->phone_code : '+91', @$application->phone);
                $waDigits = ltrim($waPhone, '+');
              @endphp
              <a href="https://api.whatsapp.com/send?phone={{ $waDigits }}" target="_blank" class="cnt-whatsapp-banner" title="Chat on WhatsApp">
                <div class="cnt-wa-left">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                  </svg>
                  <div class="cnt-wa-sep"></div>
                  <span class="cnt-wa-text">Chat on WhatsApp</span>
                </div>
                <div class="cnt-wa-arrow">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                  </svg>
                </div>
              </a>
            @endif

            @php
              $linkedin = @$application->linkedin;
              if (!empty($linkedin) && !preg_match('/^(https?:\/\/|www\.)/i', $linkedin)) {
                  $linkedin = 'https://' . $linkedin;
              }
            @endphp
            @if(!empty(@$application->linkedin))
              <a href="{{ $linkedin }}" target="_blank" class="cnt-linkedin-banner" title="Connect on LinkedIn">
                <div class="cnt-li-left">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                    <rect x="2" y="9" width="4" height="12"></rect>
                    <circle cx="4" cy="4" r="2"></circle>
                  </svg>
                  <div class="cnt-li-sep"></div>
                  <span class="cnt-li-text">Connect on LinkedIn</span>
                </div>
                <div class="cnt-li-arrow">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                  </svg>
                </div>
              </a>
            @endif
          </section>

          <!-- ===== VISA STATUS & WORK AUTHORIZATION CARD (Rule 14, Rule 17) ===== -->
          @php
            $rawVisa = trim((string) @$application->visastatu);
            $visaMeta = \App\Services\VisaResolutionService::resolve($rawVisa);
          @endphp
          <section class="cardshow" id="visa_status_card">
            <div class="card-header-sidebar">
              <div class="card-icon" style="background: #eff6ff; color: #2563eb;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect x="3" y="4" width="18" height="16" rx="3"/>
                  <circle cx="9" cy="10" r="2"/>
                  <line x1="15" y1="8" x2="17" y2="8"/>
                  <line x1="15" y1="12" x2="17" y2="12"/>
                  <line x1="7" y1="16" x2="17" y2="16"/>
                </svg>
              </div>
              <h3 class="card-title">Visa Status &amp; Work Authorization</h3>
              @if($editComman)
              <button type="button" class="jw-card-edit-btn editVisaStatus" id="btn_edit_visa_status" title="Edit Visa Status & Work Authorization">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
              </button>
              @endif
            </div>

            <div id="visa_card_populated" class="{{ $visaMeta['has_visa'] ? '' : 'd-none' }}" style="margin-top: 10px;">
              <div class="comp-list" style="display:flex; flex-direction:column; gap:8px;">
                {{-- Work Authorization / Status --}}
                <div class="comp-row" style="display:flex; align-items:center; justify-content:space-between; padding:4px 0; border-bottom:1px solid #f1f5f9;">
                  <span class="comp-label" style="font-size:12px; font-weight:500; color:#64748b;">Visa / Work Status</span>
                  <span class="badge fw-bold" id="visa_display_badge" style="{{ $visaMeta['badge_style'] }} font-size:11.5px; padding:4px 9px; border-radius:6px;">
                    {{ $visaMeta['name'] }}
                  </span>
                </div>

                {{-- Visa Country --}}
                <div class="comp-row" style="display:flex; align-items:center; justify-content:space-between; padding:4px 0; border-bottom:1px solid #f1f5f9;">
                  <span class="comp-label" style="font-size:12px; font-weight:500; color:#64748b;">Visa Country</span>
                  <span class="comp-value fw-semibold" id="visa_display_country" style="font-size:12px; color:#1e293b;">
                    {{ $visaMeta['country_display'] }}
                  </span>
                </div>

                {{-- Visa Type / Category --}}
                <div class="comp-row" style="display:flex; align-items:center; justify-content:space-between; padding:4px 0; border-bottom:1px solid #f1f5f9;">
                  <span class="comp-label" style="font-size:12px; font-weight:500; color:#64748b;">Visa Type</span>
                  <span class="comp-value" id="visa_display_type" style="font-size:12px; color:#334155; max-width:65%; text-align:right;">
                    {{ $visaMeta['type'] }}
                  </span>
                </div>

                {{-- Work Authorization Eligibility --}}
                <div class="comp-row" style="display:flex; align-items:center; justify-content:space-between; padding:4px 0;">
                  <span class="comp-label" style="font-size:12px; font-weight:500; color:#64748b;">Work Authorization</span>
                  <span class="comp-value fw-medium" id="visa_display_eligibility" style="font-size:12px; color:#0f766e;">
                    <i class="fa-solid fa-shield-check me-1" style="color:#0d9488; font-size:11px;"></i>{{ $visaMeta['eligibility'] }}
                  </span>
                </div>
              </div>
            </div>

            <p class="po-no-notes text-muted {{ $visaMeta['has_visa'] ? 'd-none' : '' }}" id="visa_empty_display" style="font-size:12px; font-style:italic; color:#94a3b8; margin:8px 0 0;">
              No visa or work authorization details recorded yet.
            </p>
          </section>
          @else
            <div id="firstPhoneDisplay" class="fw-bolder text-gray-800 fs-6 d-none"> </div>
            <div id="secondPhoneDisplay" class="fw-bolder text-gray-800 fs-6 d-none"> </div>
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
                        @php
                          $dedupedModalSkills = [];
                          foreach($jobSkill ?? [] as $sk_item) {
                              $skO = (object) $sk_item;
                              $mk = mb_strtolower(trim((string) ($skO->skill_name ?? '')));
                              if ($mk === '') continue;
                              if (!isset($dedupedModalSkills[$mk])) {
                                  $dedupedModalSkills[$mk] = clone $skO;
                              } else {
                                  if (empty($dedupedModalSkills[$mk]->skill_value) && !empty($skO->skill_value)) {
                                      $dedupedModalSkills[$mk]->skill_value = $skO->skill_value;
                                  }
                                  if ((int) ($dedupedModalSkills[$mk]->rating ?? 0) < (int) ($skO->rating ?? 0)) {
                                      $dedupedModalSkills[$mk]->rating = $skO->rating;
                                  }
                              }
                          }
                        @endphp
                            @foreach($dedupedModalSkills as $skill_val)
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
              <div class="jw-drawer-title">Edit Recruiter Comments</div>
              <div class="jw-drawer-subtitle">Update internal comments for this candidate</div>
            </div>
            <button type="button" class="jw-drawer-close editRecruiterNoteCancel" aria-label="Close">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="jw-drawer-body rn-drawer-body d-none">
            <input type="hidden" id="rn_app_id" value="{{ @$application->id }}">

            {{-- Template selector --}}
            <div class="rn-section" id="rn-template-wrap">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <label class="rn-label mb-0">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;color:#a1a5b7;flex-shrink:0;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                  Select Template <span>(Optional — loads content into comments)</span>
                </label>
                <button type="button" class="btn btn-sm btn-light-warning py-0 px-2" id="btn_sync_drawer_rm_template" title="Automatically prefill and sync comments with candidate details" style="font-size:10.5px;height:22px;line-height:20px;border-radius:4px;font-weight:500;">
                  <i class="fa-solid fa-arrows-rotate me-1" style="font-size:9px;"></i> Sync Details
                </button>
              </div>
              <select class="rn-select" id="rn_template_select">
                <option value="">Select Comment Template...</option>
                <optgroup label="Candidate Fit & Experience">
                  <option value="101">Strong Fit</option>
                  <option value="104">Similar Company / Product Experience</option>
                </optgroup>
                <optgroup label="Availability & Notice Period">
                  <option value="102">🟢 Immediate Joiner</option>
                  <option value="103">🟢 Serving Notice Period</option>
                </optgroup>
              </select>
            </div>

            {{-- Contenteditable editor — renders existing HTML notes correctly --}}
            <div class="rn-section">
              <label class="rn-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;color:#a1a5b7;flex-shrink:0;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Internal Comments
              </label>
              <div class="rn-editor" id="jw_recruiter_note_editor" contenteditable="true" data-placeholder="Add internal recruiter comments about this candidate...">{!! @$application->recruiter_note !!}</div>
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

      {{-- DRAWER 5: Reason for Job Change --}}
      <div class="jw-side-drawer" id="jw-reason-drawer">
        <div class="jw-drawer-backdrop editReasonCancel"></div>
        <div class="jw-drawer-panel">
          <div class="jw-drawer-header">
            <div class="jw-drawer-icon-wrap" style="background:#fffbeb;color:#d97706;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a4.5 4.5 0 0 0 4.5-4.5v0a4.5 4.5 0 0 0-4.5-4.5H11"/><polyline points="14 6 11 10 14 14"/><circle cx="6" cy="5" r="3"/>
              </svg>
            </div>
            <div class="jw-drawer-title-group">
              <div class="jw-drawer-title">Reason for Job Change</div>
              <div class="jw-drawer-subtitle">Select a scenario template or enter a custom reason</div>
            </div>
            <button type="button" class="jw-drawer-close editReasonCancel" aria-label="Close">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="jw-drawer-body reason-drawer-body d-none">
            <input type="hidden" id="reason_app_id" value="{{ @$application->id }}">

            {{-- Template Selector --}}
            <div class="rn-section" id="reason-template-wrap">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <label class="rn-label mb-0" for="reason_template_select_drawer">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;color:#a1a5b7;flex-shrink:0;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                  Select Reason Template <span>(Pre-fills reason)</span>
                </label>
                <button type="button" class="btn btn-sm btn-light-warning py-0 px-2" id="btn_save_custom_reason_drawer" title="Save current text as a custom reusable template" style="font-size:10.5px;height:22px;line-height:20px;border-radius:4px;font-weight:500;">
                  <i class="fa-solid fa-plus me-1" style="font-size:9px;"></i> Save as Custom
                </button>
              </div>
              <select class="rn-select" id="reason_template_select_drawer">
                <option value="">-- Select Template (or type custom below) --</option>
                <optgroup label="Growth & Role Progression">
                  <option value="reason_growth">Career Growth</option>
                  <option value="reason_better_opp">Better Career Opportunity</option>
                  <option value="reason_higher_comp">Higher Compensation</option>
                  <option value="reason_better_role_tech">Better Role / Technology</option>
                  <option value="reason_higher_resp">Higher Responsibility</option>
                </optgroup>
                <optgroup label="Stability & Organizational Shifts">
                  <option value="reason_seeking_stability">Seeking Stability</option>
                  <option value="reason_proj_ending">Current Project/Contract Ending</option>
                  <option value="reason_restructuring">Company Restructuring / Layoff</option>
                  <option value="reason_org_changes">Organizational Changes</option>
                  <option value="reason_mgmt_change">Management / Team Change</option>
                </optgroup>
                <optgroup label="Availability & Logistics">
                  <option value="reason_immediate_joiner">Looking for Immediate Joiner Opportunity</option>
                  <option value="reason_notice_constraints">Notice Period / Joining Constraints</option>
                  <option value="reason_relocation">Relocation</option>
                  <option value="reason_commute_issue">Work Location / Commute Issue</option>
                  <option value="reason_wfh_hybrid">Work-from-Home / Hybrid Requirement</option>
                  <option value="reason_international">International Opportunity</option>
                </optgroup>
                <optgroup label="Personal & Life Milestones">
                  <option value="reason_medical">Medical Emergency</option>
                  <option value="reason_personal">Personal Reasons</option>
                  <option value="reason_career_break">Career Break</option>
                  <option value="reason_role_misalign">Role Misalignment</option>
                  <option value="reason_other">Other</option>
                </optgroup>
              </select>
            </div>

            {{-- Textarea editor --}}
            <div class="rn-section mt-3">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <label class="rn-label mb-0" for="jw_reason_for_change_editor">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;color:#a1a5b7;flex-shrink:0;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                  Candidate Reason Details
                </label>
                <span class="text-muted" id="reason_drawer_char_counter" style="font-size:11px; font-weight:500;">
                  {{ strlen(@$application->reason_for_job_change ?? '') }} / 250
                </span>
              </div>
              <textarea id="jw_reason_for_change_editor" class="form-control" rows="4" maxlength="250" placeholder="Specify candidate's transition driver or pick from standard recruitment scenarios...">{{ @$application->reason_for_job_change }}</textarea>
            </div>
          </div>
          <div class="jw-drawer-footer">
            <a type="button" class="btn-jw-save" id="reason-save-btn" href="javascript:void(0);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Save Changes
            </a>
            <a type="button" class="btn-jw-cancel editReasonCancel" href="javascript:void(0);">Cancel</a>
          </div>
        </div>
      </div>{{-- /#jw-reason-drawer --}}

      {{-- DRAWER 6: Visa Status & Work Authorization --}}
      <div class="jw-side-drawer" id="jw-visa-drawer">
        <div class="jw-drawer-backdrop editVisaCancel"></div>
        <div class="jw-drawer-panel">
          <div class="jw-drawer-header">
            <div class="jw-drawer-icon-wrap" style="background:#eff6ff;color:#2563eb;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="16" rx="3"/>
                <circle cx="9" cy="10" r="2"/>
                <line x1="15" y1="8" x2="17" y2="8"/>
                <line x1="15" y1="12" x2="17" y2="12"/>
                <line x1="7" y1="16" x2="17" y2="16"/>
              </svg>
            </div>
            <div class="jw-drawer-title-group">
              <div class="jw-drawer-title">Visa Status &amp; Work Authorization</div>
              <div class="jw-drawer-subtitle">Update legal right-to-work status and visa categorization</div>
            </div>
            <button type="button" class="jw-drawer-close editVisaCancel" aria-label="Close">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="jw-drawer-body visa-drawer-body d-none">
            <input type="hidden" id="visa_app_id" value="{{ @$application->id }}">

            {{-- Quick Filter Chips --}}
            <div class="rn-section" style="margin-bottom:14px;">
              <label class="rn-label mb-2">
                <i class="fa-solid fa-bolt text-warning me-1"></i> Quick Filter Region
              </label>
              <div class="d-flex flex-wrap gap-1" id="visa_region_chips">
                <button type="button" class="btn btn-sm btn-light py-1 px-2 border visa-chip-btn active" data-region="all" style="font-size:11px; font-weight:600; border-radius:6px;">🌐 All</button>
                <button type="button" class="btn btn-sm btn-light py-1 px-2 border visa-chip-btn" data-region="United States" style="font-size:11px; font-weight:600; border-radius:6px;">🇺🇸 US</button>
                <button type="button" class="btn btn-sm btn-light py-1 px-2 border visa-chip-btn" data-region="Canada" style="font-size:11px; font-weight:600; border-radius:6px;">🇨🇦 Canada</button>
                <button type="button" class="btn btn-sm btn-light py-1 px-2 border visa-chip-btn" data-region="India" style="font-size:11px; font-weight:600; border-radius:6px;">🇮🇳 India</button>
                <button type="button" class="btn btn-sm btn-light py-1 px-2 border visa-chip-btn" data-region="United Kingdom" style="font-size:11px; font-weight:600; border-radius:6px;">🇬🇧 UK</button>
                <button type="button" class="btn btn-sm btn-light py-1 px-2 border visa-chip-btn" data-region="United Arab Emirates" style="font-size:11px; font-weight:600; border-radius:6px;">🇦🇪 UAE</button>
                <button type="button" class="btn btn-sm btn-light py-1 px-2 border visa-chip-btn" data-region="Singapore" style="font-size:11px; font-weight:600; border-radius:6px;">🇸🇬 SG</button>
                <button type="button" class="btn btn-sm btn-light py-1 px-2 border visa-chip-btn" data-region="Australia" style="font-size:11px; font-weight:600; border-radius:6px;">🇦🇺 AU</button>
                <button type="button" class="btn btn-sm btn-light py-1 px-2 border visa-chip-btn" data-region="European Union" style="font-size:11px; font-weight:600; border-radius:6px;">🇪🇺 EU</button>
              </div>
            </div>

            {{-- Work Authorization Select --}}
            <div class="rn-section" style="margin-bottom:16px;">
              <label class="rn-label" for="visa_select_drawer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;color:#a1a5b7;flex-shrink:0;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Select Work Authorization / Visa
              </label>
              <select class="rn-select" id="visa_select_drawer" style="width:100%; border-radius:8px; border:1.5px solid #e4e6ef; font-size:12px; color:#181c32; padding:8px 10px; background:#f8f9fb;">
                <option value="">-- None / Clear Visa Status --</option>
                <optgroup label="United States (US)" data-region="United States">
                  <option value="US Citizen" {{ in_array($rawVisa, ['Citizen', 'US Citizen', 'US Citizen (USC)']) ? 'selected' : '' }}>US Citizen (USC)</option>
                  <option value="Green Card" {{ in_array($rawVisa, ['Green Card', 'Permanent Resident (Green Card)']) ? 'selected' : '' }}>Permanent Resident (Green Card)</option>
                  <option value="GC-EAD" {{ in_array($rawVisa, ['GC-EAD', 'GC EAD', 'Green Card EAD']) ? 'selected' : '' }}>GC-EAD (Adjustment of Status)</option>
                  <option value="H1B" {{ in_array($rawVisa, ['H1B', 'H-1B', 'H1-B']) ? 'selected' : '' }}>H-1B (Specialty Occupation)</option>
                  <option value="H-4 EAD" {{ in_array($rawVisa, ['H-4 EAD', 'H4-EAD', 'H4 EAD']) ? 'selected' : '' }}>H-4 EAD (Spouse Dependent)</option>
                  <option value="L-2 EAD" {{ in_array($rawVisa, ['L-2 EAD', 'L2-EAD', 'L2 EAD']) ? 'selected' : '' }}>L-2 EAD (Intracompany Dependent)</option>
                  <option value="TN Permit" {{ in_array($rawVisa, ['TN Permit', 'TN Visa', 'TN-1', 'TN-2']) ? 'selected' : '' }}>TN Visa (USMCA - Canada / Mexico)</option>
                  <option value="OPT-EAD" {{ in_array($rawVisa, ['F1', 'OPT', 'OPT-EAD', 'STEM-OPT', 'OPT / STEM-OPT']) ? 'selected' : '' }}>OPT / STEM-OPT EAD</option>
                  <option value="CPT" {{ $rawVisa === 'CPT' ? 'selected' : '' }}>CPT (Curricular Practical Training)</option>
                  <option value="E-3" {{ in_array($rawVisa, ['E-3', 'E3']) ? 'selected' : '' }}>E-3 (Australian Specialty Occupation)</option>
                  <option value="O-1" {{ in_array($rawVisa, ['O-1', 'O1']) ? 'selected' : '' }}>O-1 (Extraordinary Ability)</option>
                  <option value="L1" {{ in_array($rawVisa, ['L1', 'L-1']) ? 'selected' : '' }}>L-1 (Intracompany Transferee)</option>
                  <option value="Other US EAD" {{ in_array($rawVisa, ['Other US EAD', 'TPS EAD', 'Asylee EAD']) ? 'selected' : '' }}>Other US EAD (TPS / Asylee / DACA)</option>
                </optgroup>
                <optgroup label="Canada (CA)" data-region="Canada">
                  <option value="Canadian Citizen" {{ $rawVisa === 'Canadian Citizen' ? 'selected' : '' }}>Canadian Citizen</option>
                  <option value="Canada PR" {{ in_array($rawVisa, ['Canada PR', 'Canadian Permanent Resident']) ? 'selected' : '' }}>Canada Permanent Resident (PR)</option>
                  <option value="Canada Open Work Permit" {{ in_array($rawVisa, ['Canada Open Work Permit', 'PGWP']) ? 'selected' : '' }}>Canada Open Work Permit / PGWP</option>
                  <option value="Canada Closed Work Permit" {{ in_array($rawVisa, ['Canada Closed Work Permit', 'LMIA Work Permit']) ? 'selected' : '' }}>Canada LMIA / Closed Work Permit</option>
                </optgroup>
                <optgroup label="India (IN)" data-region="India">
                  <option value="Indian Citizen" {{ $rawVisa === 'Indian Citizen' ? 'selected' : '' }}>Indian Citizen / Domestic Right to Work</option>
                  <option value="OCI / PIO" {{ in_array($rawVisa, ['OCI / PIO', 'OCI', 'PIO']) ? 'selected' : '' }}>OCI / PIO Cardholder</option>
                  <option value="India Work Visa" {{ in_array($rawVisa, ['India Work Visa', 'Employment Visa (India)']) ? 'selected' : '' }}>India Employment Visa</option>
                </optgroup>
                <optgroup label="United Kingdom (UK)" data-region="United Kingdom">
                  <option value="UK Citizen" {{ in_array($rawVisa, ['UK Citizen', 'British Citizen']) ? 'selected' : '' }}>British Citizen</option>
                  <option value="UK ILR" {{ in_array($rawVisa, ['UK ILR', 'Indefinite Leave to Remain']) ? 'selected' : '' }}>UK Indefinite Leave to Remain (ILR)</option>
                  <option value="UK Skilled Worker" {{ in_array($rawVisa, ['UK Skilled Worker', 'Tier 2']) ? 'selected' : '' }}>UK Skilled Worker / Tier 2</option>
                  <option value="UK Graduate Visa" {{ $rawVisa === 'UK Graduate Visa' ? 'selected' : '' }}>UK Graduate Visa</option>
                </optgroup>
                <optgroup label="United Arab Emirates (Dubai / UAE)" data-region="United Arab Emirates">
                  <option value="UAE Golden Visa" {{ in_array($rawVisa, ['UAE Golden Visa', 'UAE Golden / Green Visa', 'Golden Visa']) ? 'selected' : '' }}>UAE Golden Visa (10-Year Self-Sponsored)</option>
                  <option value="UAE Green Visa" {{ in_array($rawVisa, ['UAE Green Visa', 'Green Visa']) ? 'selected' : '' }}>UAE Green Visa (5-Year Skilled / Freelance)</option>
                  <option value="UAE Employment Visa" {{ in_array($rawVisa, ['UAE Employment Visa', 'UAE Work Visa', 'Dubai Employment Visa']) ? 'selected' : '' }}>UAE Employment Residence Visa</option>
                  <option value="UAE Freelance Visa" {{ in_array($rawVisa, ['UAE Freelance Visa', 'Dubai Freelance Visa', 'Remote Work Visa (UAE)']) ? 'selected' : '' }}>UAE Freelance / Remote Work Visa</option>
                  <option value="UAE National" {{ in_array($rawVisa, ['UAE National', 'UAE Citizen', 'Emirati Citizen']) ? 'selected' : '' }}>UAE / GCC National</option>
                  <option value="GCC Iqama" {{ in_array($rawVisa, ['GCC Iqama', 'Saudi Iqama', 'GCC Work Permit']) ? 'selected' : '' }}>GCC Resident Work Permit / Iqama</option>
                </optgroup>
                <optgroup label="Singapore (SG)" data-region="Singapore">
                  <option value="Singapore Citizen" {{ $rawVisa === 'Singapore Citizen' ? 'selected' : '' }}>Singapore Citizen (SC)</option>
                  <option value="Singapore PR" {{ in_array($rawVisa, ['Singapore PR', 'Singapore Permanent Resident']) ? 'selected' : '' }}>Singapore Permanent Resident (PR)</option>
                  <option value="Singapore EP" {{ in_array($rawVisa, ['Singapore EP', 'Employment Pass', 'Singapore Employment Pass']) ? 'selected' : '' }}>Singapore Employment Pass (EP)</option>
                  <option value="Singapore S Pass" {{ in_array($rawVisa, ['Singapore S Pass', 'S Pass', 'S-Pass']) ? 'selected' : '' }}>Singapore S Pass (Skilled Staff)</option>
                  <option value="Singapore ONE Pass" {{ in_array($rawVisa, ['Singapore ONE Pass', 'ONE Pass', 'Tech.Pass', 'PEP']) ? 'selected' : '' }}>Singapore ONE Pass / Tech.Pass / PEP</option>
                  <option value="Singapore LOC / DP" {{ in_array($rawVisa, ['Singapore LOC / DP', 'LOC', 'Dependant Pass']) ? 'selected' : '' }}>Singapore LOC / Dependant Pass</option>
                </optgroup>
                <optgroup label="Australia & New Zealand" data-region="Australia">
                  <option value="Australia Citizen / PR" {{ in_array($rawVisa, ['Australia Citizen / PR', 'Australian Citizen', 'Australian PR']) ? 'selected' : '' }}>Australian Citizen / Permanent Resident</option>
                  <option value="Australia TSS 482" {{ in_array($rawVisa, ['Australia TSS 482', 'TSS 482', 'Subclass 482']) ? 'selected' : '' }}>Australia TSS Visa (Subclass 482)</option>
                  <option value="Australia Skilled 189/190" {{ in_array($rawVisa, ['Australia Skilled 189/190', 'Subclass 189', 'Subclass 190']) ? 'selected' : '' }}>Australia Skilled Work Visa (Subclass 189/190)</option>
                  <option value="Australia Graduate 485" {{ in_array($rawVisa, ['Australia Graduate 485', 'Subclass 485']) ? 'selected' : '' }}>Australia Graduate Visa (Subclass 485)</option>
                  <option value="New Zealand Citizen / PR" {{ in_array($rawVisa, ['New Zealand Citizen / PR', 'NZ Citizen', 'NZ PR']) ? 'selected' : '' }}>New Zealand Citizen / Resident</option>
                </optgroup>
                <optgroup label="European Union (EU)" data-region="European Union">
                  <option value="EU Citizen" {{ in_array($rawVisa, ['EU Citizen', 'EU / EEA Citizen']) ? 'selected' : '' }}>EU / EEA Citizen</option>
                  <option value="EU Blue Card" {{ $rawVisa === 'EU Blue Card' ? 'selected' : '' }}>EU Blue Card</option>
                  <option value="Germany - Opportunity Card / Work Visa" {{ $rawVisa === 'Germany - Opportunity Card / Work Visa' ? 'selected' : '' }}>Germany - Opportunity Card / Work Visa</option>
                  <option value="Netherlands - Highly Skilled Migrant" {{ $rawVisa === 'Netherlands - Highly Skilled Migrant' ? 'selected' : '' }}>Netherlands - Highly Skilled Migrant</option>
                  <option value="Ireland - Critical Skills Employment Permit" {{ $rawVisa === 'Ireland - Critical Skills Employment Permit' ? 'selected' : '' }}>Ireland - Critical Skills Employment Permit</option>
                  <option value="Authorized Work Permit" {{ in_array($rawVisa, ['Authorized Work Permit']) ? 'selected' : '' }}>Authorized Work Permit</option>
                  <option value="Requires Sponsorship" {{ in_array($rawVisa, ['Requires Sponsorship', 'Requires International Visa Sponsorship']) ? 'selected' : '' }}>Requires International Visa Sponsorship</option>
                </optgroup>
              </select>
            </div>

            {{-- Custom Override Input --}}
            <div class="rn-section">
              <label class="rn-label" for="visa_custom_input">
                <i class="fa-solid fa-pen-to-square me-1" style="font-size:11px;color:#a1a5b7;"></i>
                Custom / Unlisted Visa Status <span>(Optional)</span>
              </label>
              <input type="text" id="visa_custom_input" class="form-control" maxlength="100" placeholder="e.g. France Talent Passport, Japan Engineer Visa, etc." style="font-size:12px; border-radius:8px; border:1.5px solid #e4e6ef; background:#f8f9fb; padding:8px 12px; color:#181c32;">
            </div>

            {{-- Live Preview Box --}}
            <div class="p-3 mt-3 rounded border" style="background:#f8fafc; border-color:#e2e8f0;">
              <div class="fw-bold text-gray-700 mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Resolved Card Preview</div>
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-muted" style="font-size:11px;">Status:</span>
                <span id="preview_visa_name" class="fw-bold" style="font-size:11.5px; color:#2563eb;">{{ $visaMeta['name'] }}</span>
              </div>
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-muted" style="font-size:11px;">Country:</span>
                <span id="preview_visa_country" class="fw-semibold" style="font-size:11.5px; color:#1e293b;">{{ $visaMeta['country_display'] }}</span>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted" style="font-size:11px;">Type:</span>
                <span id="preview_visa_type" style="font-size:11px; color:#64748b;">{{ $visaMeta['type'] }}</span>
              </div>
            </div>
          </div>
          <div class="jw-drawer-footer">
            <a type="button" class="btn-jw-save" id="visa-save-btn" href="javascript:void(0);">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Save Changes
            </a>
            <a type="button" class="btn-jw-cancel editVisaCancel" href="javascript:void(0);">Cancel</a>
          </div>
        </div>
      </div>{{-- /#jw-visa-drawer --}}

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

     
        setTimeout(() => {
            $('.iti__flag-container').each(function(index) {
                var cnt = $(this).attr('data-row');
                const flagContainer = document.getElementById(`phoneDisplayReal_${cnt}`);
                if (!flagContainer) return;
                var calling_code = $(this).attr('data-code');
                
                const flagDiv = document.createElement("div");
                flagDiv.className = `iti__flag iti__${calling_code}`;

                const dialCode = (typeof countryDialCodes !== 'undefined' && countryDialCodes[calling_code]) ? countryDialCodes[calling_code] : (calling_code ? (calling_code.startsWith('+') ? calling_code : '+' + calling_code) : "");
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
       // Open: add class to outer drawer for reliable pointer-events + transform trigger
       $('#jw-skill-drawer').addClass('jw-drawer-open');
       $('.editSkillUpdate').removeClass('d-none');
       $('.editSkillCancel').removeClass('d-none');
       $('.editSkill').addClass('d-none');
       $('.job_skill_edit').removeClass('d-none');
       $('.job_skill_view').addClass('d-none');
       // Prevent body scroll while drawer is open
       $('body').addClass('jw-drawer-active');
    });
    $(document).on('click', '.editSkillCancel', function(e) { 
       e.preventDefault();
       e.stopPropagation();
       // Close: remove class and restore all states
       $('#jw-skill-drawer').removeClass('jw-drawer-open');
       $('.editSkillUpdate').addClass('d-none');
       $('.editSkillCancel').addClass('d-none');
       $('.editSkill').removeClass('d-none');
       $('.job_skill_edit').addClass('d-none');
       $('.job_skill_view').removeClass('d-none');
       $('body').removeClass('jw-drawer-active');
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
            url				: '{{ route('service_company.job-applications.skillExperienceUpdate') }}?_token={{ csrf_token() }}',
                  type 			: 'POST',
                  data 			: formData,
                  processData		: false,  	// tell jQuery not to process the data
                  contentType		: false,  	// tell jQuery not to set contentType
                  redirect			: true,
                  success			: function (response) {
                    $btn.html('Save Changes');
                    $btn.prop('disabled', false); // Re-enable after success
                    $("#skill-update-form").html('Save Changes');
                    // Close drawer: remove open class and restore default states
                    $('#jw-skill-drawer').removeClass('jw-drawer-open');
                    $('body').removeClass('jw-drawer-active');
                    $('.editSkillUpdate').addClass('d-none');
                    $('.editSkillCancel').addClass('d-none');
                    $('.job_skill_edit').addClass('d-none');
                    $('.job_skill_view').removeClass('d-none');
                    $('.editSkill').removeClass('d-none');
                    if (response.html) {
                        $('.job_skill_view').html(response.html);
                    }
                    if (response.skill_match !== undefined && window.JWRealtimeSync && typeof window.JWRealtimeSync.updateMatchScore === 'function') {
                        window.JWRealtimeSync.updateMatchScore(response.candidate_id, response.wise_ids, response.skill_match, true);
                    }
                    // Synchronize select inputs in side drawer so reopening shows updated values
                    if (response.data && Array.isArray(response.data)) {
                        response.data.forEach(function(item) {
                            if (item.skillId) {
                                if (typeof item.rating !== 'undefined') {
                                    $('select[name="rating[' + item.skillId + ']"]').val(item.rating);
                                }
                            }
                        });
                    }
                    // Instant DOM synchronization for Download Resume Center
                    var rsDataEl = document.getElementById('rsData');
                    if (rsDataEl && response.skill_match !== undefined) {
                        rsDataEl.setAttribute('data-match', response.skill_match);
                    }
                    if (rsDataEl && response.data && Array.isArray(response.data)) {
                        try {
                            var currentSkills = JSON.parse(rsDataEl.getAttribute('data-skills') || '[]');
                            response.data.forEach(function(item) {
                                var found = currentSkills.find(function(s) {
                                    return (s.id && s.id == item.skillId) || (s.name && item.skill_name && s.name.toLowerCase() === item.skill_name.toLowerCase());
                                });
                                if (found) {
                                    if (typeof item.rating !== 'undefined') found.rating = parseInt(item.rating, 10) || 0;
                                    if (typeof item.skill_value !== 'undefined') found.expText = item.skill_value;
                                }
                            });
                            rsDataEl.setAttribute('data-skills', JSON.stringify(currentSkills));
                        } catch(e) {}
                    }
                    if (window.AgyResumeRenderer && window.AgyResumeRenderer.JeroRdcDB) {
                        try {
                            var candData = typeof readData === 'function' ? readData() : null;
                            if (candData) window.AgyResumeRenderer.JeroRdcDB.saveCandidate(candData);
                        } catch(e) {}
                    }
                    // Multi-tier atomic cache invalidation for both wiseId and core candidateId
                    var wId = $('#candidate_wise_id').val() || '{{ @$application->wiseId ?: @$jobWiseCanId }}';
                    var cId = $('#candidate_core_id').val() || '{{ @$application->id }}';
                    if (window.invalidateCandidateDrawerCache) {
                        if (wId) window.invalidateCandidateDrawerCache(wId);
                        if (cId) window.invalidateCandidateDrawerCache(cId);
                    }
                    Swal.fire("Success", 'Candidate Skill Experience updated successfully !',"success");
                  },
                error : function (xhr) {
                  $btn.html('Save Changes');
                  $btn.prop('disabled', false);
                  $("#skill-update-form").html('Save Changes');
                  var msg = 'Something went wrong. Please try again.';
                  try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp && resp.message) msg = resp.message;
                  } catch(e) {}
                  Swal.fire("Warning", msg, "warning");
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
       // Open: add class to outer drawer for reliable pointer-events + transform
       $('#jw-slots-drawer').addClass('jw-drawer-open');
       $('.editSlots').addClass('d-none');
       $('.job_slots_edit').removeClass('d-none');
       // Hide whichever display state is currently showing
       $('.job_slots_view').addClass('d-none');
       $('.po-slots-empty').addClass('d-none');
       $('body').addClass('jw-drawer-active');
    });
    $(document).on('click', '.editSlotsCancel', function(e) {
       e.preventDefault();
       e.stopPropagation();
       // Close: remove class and restore all states
       $('#jw-slots-drawer').removeClass('jw-drawer-open');
       $('.editSlots').removeClass('d-none');
       $('.job_slots_edit').addClass('d-none');
       // Restore whichever display state is appropriate
       if ($('.job_slots_view').length) {
           $('.job_slots_view').removeClass('d-none');
       } else {
           $('.po-slots-empty').removeClass('d-none');
       }
       $('body').removeClass('jw-drawer-active');
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
    @php
        $drawerSkillsList = [];
        if (!empty($jobSkill)) {
            foreach ($jobSkill as $__jsk) {
                $__jskObj = (object) $__jsk;
                $__skName = trim((string) (@$__jskObj->skill_name ?? ''));
                if ($__skName !== '' && !in_array($__skName, $drawerSkillsList, true)) {
                    $drawerSkillsList[] = $__skName;
                }
            }
        }
        if (empty($drawerSkillsList) && !empty($application->skills)) {
            foreach (explode(',', $application->skills) as $__cs) {
                $__cs = trim($__cs);
                if ($__cs !== '' && !in_array($__cs, $drawerSkillsList, true)) {
                    $drawerSkillsList[] = $__cs;
                }
            }
        }
        $drawerSkillsStr = !empty($drawerSkillsList) ? implode(', ', array_slice($drawerSkillsList, 0, 5)) : 'required skills';
        $drawerExpStr = !empty($application->total_experience) ? $application->total_experience . ' years' : (!empty($application->rel_exp) ? $application->rel_exp . ' years' : '5+ years');
        $drawerCompStr = !empty($application->company_name) ? $application->company_name : (!empty($application->current_company) ? $application->current_company : '');
        $drawerNoticeStr = !empty($application->notice_period) ? $application->notice_period : (!empty($application->serving_notice_period_mode) ? 'Serving Notice Period' : '2–3 days');
        $drawerLwdStr = !empty($application->last_working_date) ? \Carbon\Carbon::parse($application->last_working_date)->format('d M Y') : 'Immediate';
    @endphp

    var drawerCandidateVars = {
        relevantExperience: @json($drawerExpStr),
        allSkills: @json($drawerSkillsStr),
        currentCompany: @json($drawerCompStr),
        joiningTimeline: @json($drawerNoticeStr),
        lastWorkingDay: @json($drawerLwdStr),
        jobChangeReason: 'career advancement'
    };

    var rnFallbackTemplates = {
        '101': {
            id: 101,
            title: 'Strong Fit',
            category: 'Candidate Fit & Experience',
            content: "Strong match for the requirement with [relevant experience] of relevant experience in [all skills in job]. Currently working on a similar product environment and appears technically aligned with the role."
        },
        '104': {
            id: 104,
            title: 'Similar Company / Product Experience',
            category: 'Candidate Fit & Experience',
            content: "Currently working[current company context] in a product-based environment with responsibilities very similar to this position. Good understanding of Agile development and cross-functional collaboration."
        },
        '102': {
            id: 102,
            title: '🟢 Immediate Joiner',
            category: 'Availability & Notice Period',
            content: "Immediate joiner and available to start within [joining timeline]. Relevant experience matches the requirement closely. Candidate is actively looking due to [reason for job change]."
        },
        '103': {
            id: 103,
            title: '🟢 Serving Notice Period',
            category: 'Availability & Notice Period',
            content: "Currently serving notice period with last working day on [last working day]. Candidate is open to completing the process immediately and can join from the following week."
        }
    };
    var rnTemplates = Object.assign({}, rnFallbackTemplates);
    var rnTemplatesLoaded = false;

    function interpolateDrawerTemplate(content) {
        if (!content) return '';
        var v = drawerCandidateVars;
        var out = content;
        out = out.replace(/\[relevant experience\]/gi, v.relevantExperience || '5+ years');
        out = out.replace(/\[all skills in job\]/gi, v.allSkills || 'required skills');
        out = out.replace(/\(Mentioned all the skills in job\)/gi, v.allSkills || 'required skills');
        if (v.currentCompany) {
            out = out.replace(/\[current company context\]/gi, ' at ' + v.currentCompany);
        } else {
            out = out.replace(/\[current company context\]/gi, '');
        }
        out = out.replace(/\[joining timeline\]/gi, v.joiningTimeline || '2–3 days');
        out = out.replace(/\[reason for job change\]/gi, v.jobChangeReason || 'career advancement');
        out = out.replace(/\[last working day\]/gi, v.lastWorkingDay || 'Immediate');
        return out.replace(/[ \t]{2,}/g, ' ').trim();
    }

    $(document).on('click', '.editRecruiterNote', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // Open: add class to outer drawer for reliable pointer-events + transform trigger
        $('#jw-recruiter-note-drawer').addClass('jw-drawer-open');
        $('.rn-drawer-body').removeClass('d-none');
        $('body').addClass('jw-drawer-active');

        if (!rnTemplatesLoaded) {
            $.getJSON('{{ route('service_company.job-applications.recruiterNoteTemplates') }}', function(res) {
                if (res.status === 'success' && res.data && res.data.length > 0) {
                    var customGroup = '';
                    $.each(res.data, function(i, tpl) {
                        rnTemplates[tpl.id] = tpl;
                        if ($('#rn_template_select option[value="' + tpl.id + '"]').length === 0) {
                            customGroup += '<option value="' + tpl.id + '">' + $('<div>').text(tpl.title).html() + '</option>';
                        }
                    });
                    if (customGroup) {
                        $('#rn_template_select').append('<optgroup label="Custom Templates">' + customGroup + '</optgroup>');
                    }
                    rnTemplatesLoaded = true;
                }
            });
        }
    });

    $(document).on('click', '.editRecruiterNoteCancel', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // Close: remove class and reset drawer state
        $('#jw-recruiter-note-drawer').removeClass('jw-drawer-open');
        $('.rn-drawer-body').addClass('d-none');
        $('body').removeClass('jw-drawer-active');
    });

    $(document).on('change', '#rn_template_select', function() {
        var id = $(this).val();
        if (id && rnTemplates[id]) {
            var rawContent = rnTemplates[id].content || '';
            var formatted = interpolateDrawerTemplate(rawContent);
            $('#jw_recruiter_note_editor').html(formatted);
        }
    });

    $(document).on('click', '#btn_sync_drawer_rm_template', function(e) {
        e.preventDefault();
        var selectedId = $('#rn_template_select').val() || '101';
        if (!selectedId || !rnTemplates[selectedId]) {
            selectedId = '101';
            $('#rn_template_select').val('101');
        }
        var rawContent = rnTemplates[selectedId] ? rnTemplates[selectedId].content : '';
        var formatted = interpolateDrawerTemplate(rawContent);
        $('#jw_recruiter_note_editor').html(formatted);
        if (typeof toastr !== 'undefined') {
            toastr.success('Recruiter comments synchronized with candidate details!');
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
                            $('.po-recruiter-note-text').after('<p class="po-no-notes" style="font-size:12px;color:#94a3b8;font-style:italic;margin:0;">No recruiter comments added yet.</p>');
                        }
                        $('.po-no-notes').show();
                    }
                    if (window.JWRealtimeSync && typeof window.JWRealtimeSync.updateRecruiterNote === 'function') {
                        window.JWRealtimeSync.updateRecruiterNote(appId, note, true);
                    }
                    // Instant DOM synchronization for Download Resume Center
                    var rsDataEl = document.getElementById('rsData');
                    if (rsDataEl) {
                        var plainNote = $('<div>').html(note || '').text().trim();
                        rsDataEl.setAttribute('data-recruiter-note', plainNote);
                    }
                    if (window.AgyResumeRenderer && window.AgyResumeRenderer.JeroRdcDB) {
                        try {
                            var candData = typeof readData === 'function' ? readData() : null;
                            if (candData) window.AgyResumeRenderer.JeroRdcDB.saveCandidate(candData);
                        } catch(e) {}
                    }
                    // Multi-tier atomic cache invalidation for both wiseId and core candidateId
                    var wId = $('#candidate_wise_id').val() || '{{ @$application->wiseId ?: @$jobWiseCanId }}';
                    var cId = $('#candidate_core_id').val() || appId;
                    if (window.invalidateCandidateDrawerCache) {
                        if (wId) window.invalidateCandidateDrawerCache(wId);
                        if (cId) window.invalidateCandidateDrawerCache(cId);
                    }
                    if (window.invalidateCvTrackerDownloadResumeCache) {
                        if (wId) window.invalidateCvTrackerDownloadResumeCache(wId);
                        if (cId) window.invalidateCvTrackerDownloadResumeCache(cId);
                    }

                    // Close drawer first; remove open class; blur active element
                    $('#jw-recruiter-note-drawer').removeClass('jw-drawer-open');
                    $('.rn-drawer-body').addClass('d-none');
                    $('body').removeClass('jw-drawer-active');
                    if (document.activeElement) { document.activeElement.blur(); }
                    // Wait for drawer slide-out transition (280ms) before re-enabling
                    // button and firing Swal — prevents Swal-close click from landing
                    // on the now-hidden Edit button and reopening the drawer
                    setTimeout(function() {
                        $btn.html('Save Changes');
                        $btn.prop('disabled', false);
                        Swal.fire('Success', 'Recruiter comments updated successfully!', 'success');
                    }, 350);
                } else {
                    $btn.html('Save Changes');
                    $btn.prop('disabled', false);
                }
            },
            error   : function(xhr) {
                $btn.html('Save Changes');
                $btn.prop('disabled', false);
                var msg = 'Something went wrong. Please try again.';
                try { var r = JSON.parse(xhr.responseText); if (r && r.message) msg = r.message; } catch(e) {}
                Swal.fire('Warning', msg, 'warning');
            }
        });
    });

    // ===== REASON FOR JOB CHANGE DRAWER LOGIC (Rule 14, Rule 17) =====
    var jwReasonTemplates = {
        'reason_growth': 'Looking for upward career growth and clear advancement pathways that are currently constrained in the current organizational setup.',
        'reason_better_opp': 'Actively exploring better career opportunities with broader scope, progressive work culture, and advanced technical exposure.',
        'reason_higher_comp': 'Seeking competitive compensation aligned with current market standards, proven capabilities, and overall industry experience.',
        'reason_better_role_tech': 'Seeking transition to modern technology stacks, product architecture, and more hands-on technical challenges.',
        'reason_higher_resp': 'Prepared to take on higher technical ownership, cross-functional project leadership, and larger team responsibilities.',
        'reason_seeking_stability': 'Looking for long-term organizational stability, financial soundness, and sustainable project continuity.',
        'reason_proj_ending': 'Current project/contract engagement is approaching successful completion, actively looking for the next long-term role.',
        'reason_restructuring': 'Impacted by organizational restructuring and company-wide downsizing; actively available for immediate placement.',
        'reason_org_changes': 'Recent strategic changes in company direction and organizational priorities have prompted seeking a better-aligned opportunity.',
        'reason_mgmt_change': 'Recent internal team restructuring and leadership changes have resulted in realignment of roles and priorities.',
        'reason_immediate_joiner': 'Available as an immediate joiner with zero notice period constraints, looking for immediate onboarding.',
        'reason_notice_constraints': 'Currently serving notice period and actively seeking roles aligned with current availability and joining timeline.',
        'reason_relocation': 'Relocating to the target location for personal/family reasons, seeking a local or remote opportunity.',
        'reason_commute_issue': 'Looking for an opportunity closer to residence or with reduced commute time to enhance day-to-day productivity.',
        'reason_wfh_hybrid': 'Seeking a structured hybrid or remote work arrangement to optimize productivity and achieve healthy work-life balance.',
        'reason_international': 'Actively exploring international opportunities, cross-border projects, or roles offering visa sponsorship.',
        'reason_medical': 'Took necessary time to attend to family medical priorities; the situation is fully resolved and ready for full-time commitment.',
        'reason_personal': 'Seeking a career transition due to personal commitments while remaining dedicated to professional growth.',
        'reason_career_break': 'Returning to the active workforce after a planned career break with refreshed skills and high enthusiasm.',
        'reason_role_misalign': 'Current day-to-day responsibilities have diverged from the original job profile; looking for a role matching core strengths.',
        'reason_other': 'Exploring new opportunities to better align personal career milestones with long-term professional aspirations.'
    };

    function loadCustomReasonTemplates() {
        try {
            var raw = localStorage.getItem('jero_custom_reason_templates');
            if (raw) {
                var customs = JSON.parse(raw);
                if (Array.isArray(customs) && customs.length > 0) {
                    $('#reason_template_select_drawer optgroup[data-custom="true"]').remove();
                    var $optgroup = $('<optgroup label="Custom Saved Templates" data-custom="true"></optgroup>');
                    customs.forEach(function(item, idx) {
                        var valKey = 'custom_' + idx;
                        jwReasonTemplates[valKey] = item.text;
                        $optgroup.append($('<option>', { value: valKey, text: '★ ' + item.title }));
                    });
                    $('#reason_template_select_drawer').append($optgroup);
                }
            }
        } catch (e) {
            console.error('Failed to load custom reason templates from localStorage:', e);
        }
    }

    $(document).on('click', '.editReasonForChange', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#jw-reason-drawer').addClass('jw-drawer-open');
        $('.reason-drawer-body').removeClass('d-none');
        $('body').addClass('jw-drawer-active');
        loadCustomReasonTemplates();

        var currentText = $('#drawer_reason_display_text').hasClass('text-muted') ? '' : $('#drawer_reason_display_text').text().trim();
        if (currentText && currentText !== 'No reason for job change specified.') {
            $('#jw_reason_for_change_editor').val(currentText);
        }
        var count = ($('#jw_reason_for_change_editor').val() || '').length;
        $('#reason_drawer_char_counter').text(count + ' / 250');
    });

    $(document).on('click', '.editReasonCancel', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#jw-reason-drawer').removeClass('jw-drawer-open');
        $('.reason-drawer-body').addClass('d-none');
        $('body').removeClass('jw-drawer-active');
    });

    $(document).on('change', '#reason_template_select_drawer', function() {
        var key = $(this).val();
        if (key && jwReasonTemplates[key]) {
            var text = jwReasonTemplates[key];
            if (text.length > 250) text = text.substring(0, 250);
            $('#jw_reason_for_change_editor').val(text);
            $('#reason_drawer_char_counter').text(text.length + ' / 250');
        }
    });

    $(document).on('input keyup paste', '#jw_reason_for_change_editor', function() {
        var len = $(this).val().length;
        $('#reason_drawer_char_counter').text(len + ' / 250');
    });

    $(document).on('click', '#btn_save_custom_reason_drawer', function(e) {
        e.preventDefault();
        var currentVal = ($('#jw_reason_for_change_editor').val() || '').trim();
        if (!currentVal) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Empty Reason', 'Please enter a reason before saving it as a custom template.', 'info');
            } else {
                alert('Please enter a reason before saving it as a custom template.');
            }
            return;
        }

        var templateName = prompt('Enter a short title for this custom reason template:', currentVal.substring(0, 30));
        if (!templateName || !templateName.trim()) return;

        try {
            var customs = [];
            var raw = localStorage.getItem('jero_custom_reason_templates');
            if (raw) customs = JSON.parse(raw);
            if (!Array.isArray(customs)) customs = [];

            customs.unshift({ title: templateName.trim(), text: currentVal });
            localStorage.setItem('jero_custom_reason_templates', JSON.stringify(customs.slice(0, 20)));
            loadCustomReasonTemplates();
            if (typeof toastr !== 'undefined') {
                toastr.success('Custom template "' + templateName.trim() + '" saved successfully!');
            }
        } catch (err) {
            console.error('Failed to save custom template:', err);
        }
    });

    $(document).on('click', '#reason-save-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        if ($btn.prop('disabled')) return;
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

        var reason = $('#jw_reason_for_change_editor').val();
        var appId  = $('#reason_app_id').val();

        $.ajax({
            url: '{{ route('service_company.job-applications.reasonForChangeUpdate') }}?_token={{ csrf_token() }}',
            type: 'POST',
            data: { appId: appId, reason_for_job_change: reason },
            success: function(response) {
                if (response.status) {
                    var trimmed = (reason || '').trim();
                    if (trimmed) {
                        $('#drawer_reason_display_text')
                            .removeClass('text-muted')
                            .css({ 'font-style': 'normal', 'color': '#334155' })
                            .text(trimmed);
                    } else {
                        $('#drawer_reason_display_text')
                            .addClass('text-muted')
                            .css({ 'font-style': 'italic', 'color': '#94a3b8' })
                            .text('No reason for job change specified.');
                    }

                    if (typeof drawerCandidateVars !== 'undefined') {
                        drawerCandidateVars.jobChangeReason = trimmed || 'career advancement';
                    }

                    // Invalidate caches if available
                    var wId = $('#job_wise_id').val() || null;
                    var cId = appId;
                    if (window.invalidateCandidateDrawerCache) {
                        if (wId) window.invalidateCandidateDrawerCache(wId);
                        if (cId) window.invalidateCandidateDrawerCache(cId);
                    }

                    $('#jw-reason-drawer').removeClass('jw-drawer-open');
                    $('.reason-drawer-body').addClass('d-none');
                    $('body').removeClass('jw-drawer-active');
                    if (document.activeElement) { document.activeElement.blur(); }

                    setTimeout(function() {
                        $btn.html('Save Changes');
                        $btn.prop('disabled', false);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Success', 'Reason for job change updated successfully!', 'success');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success('Reason for job change updated successfully!');
                        }
                    }, 350);
                } else {
                    $btn.html('Save Changes');
                    $btn.prop('disabled', false);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Warning', response.message || 'Could not update reason.', 'warning');
                    }
                }
            },
            error: function(xhr) {
                $btn.html('Save Changes');
                $btn.prop('disabled', false);
                var msg = 'Something went wrong. Please try again.';
                try { var r = JSON.parse(xhr.responseText); if (r && r.message) msg = r.message; } catch(e) {}
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Warning', msg, 'warning');
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(msg);
                }
            }
        });
    });

    // ===== VISA STATUS & WORK AUTHORIZATION DRAWER LOGIC (Rule 14, Rule 17) =====
    $(document).on('click', '.editVisaStatus', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#jw-visa-drawer').addClass('jw-drawer-open');
        $('.visa-drawer-body').removeClass('d-none');
        $('body').addClass('jw-drawer-active');
    });

    $(document).on('click', '.editVisaCancel', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#jw-visa-drawer').removeClass('jw-drawer-open');
        $('.visa-drawer-body').addClass('d-none');
        $('body').removeClass('jw-drawer-active');
    });

    // Quick filter chips for regions
    $(document).on('click', '.visa-chip-btn', function(e) {
        e.preventDefault();
        var region = $(this).data('region');
        $('.visa-chip-btn').removeClass('active');
        $(this).addClass('active');

        if (region === 'all') {
            $('#visa_select_drawer optgroup').show();
        } else {
            $('#visa_select_drawer optgroup').each(function() {
                var grpRegion = $(this).data('region');
                if (grpRegion === region) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Live preview update
    function updateVisaDrawerPreview() {
        var selectedVal = $('#visa_custom_input').val().trim() || $('#visa_select_drawer').val() || '';
        if (!selectedVal) {
            $('#preview_visa_name').text('Not Specified').css('color', '#94a3b8');
            $('#preview_visa_country').text('Not Specified');
            $('#preview_visa_type').text('Not Specified');
            return;
        }

        // Basic client-side resolution for preview
        var country = 'Global 🌐';
        var selOpt = $('#visa_select_drawer option:selected');
        var optGroup = selOpt.closest('optgroup').attr('label') || '';

        if (optGroup.indexOf('United States') !== -1) country = 'United States 🇺🇸';
        else if (optGroup.indexOf('Canada') !== -1) country = 'Canada 🇨🇦';
        else if (optGroup.indexOf('India') !== -1) country = 'India 🇮🇳';
        else if (optGroup.indexOf('United Kingdom') !== -1) country = 'United Kingdom 🇬🇧';
        else if (optGroup.indexOf('Emirates') !== -1) country = 'United Arab Emirates 🇦🇪';
        else if (optGroup.indexOf('Singapore') !== -1) country = 'Singapore 🇸🇬';
        else if (optGroup.indexOf('Australia') !== -1) country = 'Australia 🇦🇺';
        else if (optGroup.indexOf('European') !== -1) country = 'European Union 🇪🇺';

        $('#preview_visa_name').text(selectedVal).css('color', '#2563eb');
        $('#preview_visa_country').text(country);
        $('#preview_visa_type').text(selOpt.text() || selectedVal);
    }

    $(document).on('change', '#visa_select_drawer', function() {
        if ($(this).val()) {
            $('#visa_custom_input').val('');
        }
        updateVisaDrawerPreview();
    });

    $(document).on('input keyup paste', '#visa_custom_input', function() {
        if ($(this).val().trim()) {
            $('#visa_select_drawer').val('');
        }
        updateVisaDrawerPreview();
    });

    // Save Visa Status via AJAX
    $(document).on('click', '#visa-save-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        if ($btn.prop('disabled')) return;
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

        var visaVal = ($('#visa_custom_input').val() || '').trim() || $('#visa_select_drawer').val() || '';
        var appId   = $('#visa_app_id').val();

        $.ajax({
            url: '{{ route('service_company.job-applications.visaStatusUpdate') }}?_token={{ csrf_token() }}',
            type: 'POST',
            data: { appId: appId, visastatu: visaVal },
            success: function(response) {
                if (response.status) {
                    var r = response.resolved || {};
                    if (r.has_visa) {
                        $('#visa_card_populated').removeClass('d-none');
                        $('#visa_empty_display').addClass('d-none');

                        $('#visa_display_badge')
                            .text(r.name || visaVal)
                            .attr('style', (r.badge_style || 'background:#eff6ff; color:#1d4ed8;') + ' font-size:11.5px; padding:4px 9px; border-radius:6px;');
                        $('#visa_display_country').text(r.country_display || r.country || '—');
                        $('#visa_display_type').text(r.type || '—');
                        $('#visa_display_eligibility').html('<i class="fa-solid fa-shield-check me-1" style="color:#0d9488; font-size:11px;"></i>' + (r.eligibility || 'Authorized to Work'));
                    } else {
                        $('#visa_card_populated').addClass('d-none');
                        $('#visa_empty_display').removeClass('d-none');
                    }

                    // Invalidate caches if available
                    var wId = $('#job_wise_id').val() || null;
                    var cId = appId;
                    if (window.invalidateCandidateDrawerCache) {
                        if (wId) window.invalidateCandidateDrawerCache(wId);
                        if (cId) window.invalidateCandidateDrawerCache(cId);
                    }

                    $('#jw-visa-drawer').removeClass('jw-drawer-open');
                    $('.visa-drawer-body').addClass('d-none');
                    $('body').removeClass('jw-drawer-active');
                    if (document.activeElement) { document.activeElement.blur(); }

                    setTimeout(function() {
                        $btn.html('Save Changes');
                        $btn.prop('disabled', false);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Success', 'Visa Status & Work Authorization updated successfully!', 'success');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success('Visa Status & Work Authorization updated successfully!');
                        }
                    }, 350);
                } else {
                    $btn.html('Save Changes');
                    $btn.prop('disabled', false);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Warning', response.message || 'Could not update visa status.', 'warning');
                    }
                }
            },
            error: function(xhr) {
                $btn.html('Save Changes');
                $btn.prop('disabled', false);
                var msg = 'Something went wrong. Please try again.';
                try { var res = JSON.parse(xhr.responseText); if (res && res.message) msg = res.message; } catch(e) {}
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Warning', msg, 'warning');
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(msg);
                }
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
  $rdcResumeRaw = (string)(@$application->resume ?: (@$resume ?? ''));
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
  $rsSeenSkillKeys = [];
  if(!empty($skillExperiencesCount) && !empty($skillExperiences)){
    foreach($skillExperiences as $exp){
      $exp=(object)$exp;
      $sn=trim((string)($exp->skill_key??'')); if(!$sn) continue;
      $normKey = $rsSkillNormalize($sn);
      if(isset($rsSeenSkillKeys[$normKey])) continue;
      $rsSeenSkillKeys[$normKey] = true;
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
      $normKey = $rsSkillNormalize($sn);
      if(isset($rsSeenSkillKeys[$normKey])) continue;
      $rsSeenSkillKeys[$normKey] = true;
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
      $normKey = $rsSkillNormalize($sn);
      if($sn && !isset($rsSeenSkillKeys[$normKey])) {
        $rsSeenSkillKeys[$normKey] = true;
        $rsSkills[]=['name'=>$sn,'expText'=>'--','exp'=>0,'rating'=>0];
      }
    }
  }
  $rsMatchPct = (!empty($__hdrOverallPct) && (int)$__hdrOverallPct > 0) ? (int)$__hdrOverallPct : (int)round((float)(@$application->skill_match??0));
  if($rsMatchPct===0&&!empty($__hdrJobTotal)) $rsMatchPct=(int)round(($__hdrMatchedCount??0)/$__hdrJobTotal*100);
  $rsWm=trim((string)(@$application->wm??''));
  $rsWorkMode=$rsWm!=='' ? ucwords(str_replace(['_', '-'], ' ', strtolower($rsWm))) : '—';
  $rsNpMode=(int)(@$application->serving_notice_period_mode??-1);
  $rsNpRaw=trim((string)@$application->np_id);
  $rsNpTs=$rsNpRaw!=='' ? strtotime($rsNpRaw) : false;
  $rsIsBench = (@$application->condidate_type === 'bench_condidate');
  if($rsNpMode===2||strtolower($rsNpRaw)==='immediate'){
      $rsNotice='Immediate';
      if (!empty($application->last_working_date)) {
          $rsNotice .= ' - LWD '.date('d M Y', strtotime($application->last_working_date));
      }
  }
  elseif($rsNpMode===0&&(int)$rsNpRaw>0){ $rsNotice=$rsNpRaw.' Days'; }
  elseif($rsNpMode===1&&$rsNpTs!==false&&(int)date('Y',$rsNpTs)>1970){
      if (date('Y-m-d', $rsNpTs) < date('Y-m-d')) {
          $rsNotice = 'Immediate';
      } else {
          $rsNotice = $rsIsBench ? ('Available '.date('d M Y',$rsNpTs)) : ('SNP LWD '.date('d M Y',$rsNpTs));
      }
  }
  elseif($rsNpTs!==false&&(int)date('Y',$rsNpTs)>1970){
      $rsNotice = $rsIsBench ? ('Available '.date('d M Y',$rsNpTs)) : ('SNP LWD '.date('d M Y',$rsNpTs));
  }
  else { $rsNotice=$rsNpRaw; }
  if($rsNotice===''||$rsNotice==='Available '||$rsNotice==='SNP LWD '||str_contains($rsNotice,'01 Jan 1970')) $rsNotice='Not Specified';
  $rsIsImmediate=($rsNpMode===2||strtolower($rsNpRaw)==='immediate'||stripos($rsNotice,'Immediate')!==false);

  $candCurLoc = (!empty(@$application->current_location) && !preg_match('/^\d+$/', trim((string) @$application->current_location))) ? trim((string) @$application->current_location) : '';
  $rsLoc=trim(trim($city_name??'',', ').' '.trim($state_name??'',', '));
  if($rsLoc==='' || $rsLoc==='--') $rsLoc=$candCurLoc;
  if($rsLoc==='' || $rsLoc==='--') $rsLoc=trim($country_name??'',', ');
  if($rsLoc==='') $rsLoc='Not Specified';

  $rsTotalExp=trim((string)(@$application->total_exp??''));
  $rsTotalExp=trim($rsTotalExp," \t\n\r\0\x0B.");
  if ($rsTotalExp !== '' && (float)$rsTotalExp == 0.0) {
      $rsExpStr = 'Fresher';
  } elseif ($rsTotalExp !== '') {
      $rsExpStr = $rsTotalExp.' Year'.((float)$rsTotalExp==1.0?'':'s');
  } else {
      $rsExpStr = 'Not Specified';
  }

  $rsEdu = strtoupper(trim((string)(@$application->qualification_name ?: (@$application->qualification ?: (@$application->education ?: '')))));
  if(preg_match('/^\d+$/', $rsEdu)) $rsEdu = '';
  if($rsEdu==='') $rsEdu = 'Not Specified';

  $rsValidation=[];
  if(!empty($application->resume))   $rsValidation[]=['label'=>'RESUME','status'=>'Verified'];
  if(!empty($application->email)||!empty($application->secondrary_email)) $rsValidation[]=['label'=>'EMAIL ID','status'=>'Verified'];
  if(!empty($application->phone)||!empty($application->secondary_mobile)) $rsValidation[]=['label'=>'PHONE NUMBER','status'=>'Verified'];
  else $rsValidation[]=['label'=>'PHONE NUMBER','status'=>'-'];
  $hasLinkedin = !empty($application->linkedin) && !in_array(strtolower(trim((string) $application->linkedin)), ['--', '-', 'na', 'n/a', 'null', 'none']);
  if($hasLinkedin) $rsValidation[]=['label'=>'LINKEDIN ID','status'=>'Verified'];
  else $rsValidation[]=['label'=>'LINKEDIN ID','status'=>'-'];
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
  data-education="{{ $rsEdu }}"
  data-work-mode="{{ $rsWorkMode }}"
  data-candidate-rating="{{ (int) @$application->rating }}"
  data-report-id="JR{{ now()->format('Ymd') }}{{ str_pad((string)@$application->id, 6, '0', STR_PAD_LEFT) }}"
  data-report-date="{{ now()->format('d M Y') }}"
  data-recruiter-name="{{ $rsCaptainName }}"
  data-report-company-name="{{ $rcvCompanyName ?? '' }}"
  data-report-company-logo="{{ $rcvCompanyLogo ?? '' }}"
  data-report-company-logo-raw="{{ $rcvCompanyLogoRaw ?? '' }}"
  data-ai-version="Live Intelligence"
  data-match="{{ $rsMatchPct }}"
  data-is-immediate="{{ $rsIsImmediate ? '1' : '0' }}"
  @php
    $rsNoteRaw = strip_tags((string)(@$application->recruiter_note ?? ''));
    $rsSummaryFinal = $rsCareerSummary ?: trim(strip_tags(html_entity_decode((string)(@$application->cover_letter??''),ENT_QUOTES,'UTF-8')));
  @endphp
  data-recruiter-note="{{ \Illuminate\Support\Str::limit($rsNoteRaw, 500) }}"
  data-career-summary="{{ \Illuminate\Support\Str::limit($rsSummaryFinal, 500) }}"
  data-job-title="{{ ucwords($job->title??'') }}"
  data-skills='@json($rsSkills)'
  data-validation='@json($rsValidation)'></div>
</div>{{-- /#jw_candidate_root_detail (Scoped Candidate Context) --}}

@include('service_company.job-applications.partials._cvtracker_download_resume_engine')

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
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

{{-- 5: Pre-Screened Report --}}
      <label class="rdc-opt" data-opt="5" data-coins="1">
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
          <span class="rdc-ocost">1 Coin</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

{{-- 41: Resume + Company Branding --}}
      <label class="rdc-opt" data-opt="41" data-coins="2">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="41">
        <span class="rdc-onum">41</span>
        <span class="rdc-oi" style="background:#ecfeff;color:#0e7490;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h10l6 6v10a2 2 0 0 1-2 2H4V4z"/><path d="M14 4v6h6"/><path d="M8 13h8"/><path d="M8 17h5"/><path d="M3 7h18"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Company Branding</span>
          <span class="rdc-odesc">Add your company logo and shared-by header above the resume while keeping all candidate details visible.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">2 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 42: Pre-Screened Report + Resume + Company Branding --}}
      <label class="rdc-opt" data-opt="42" data-coins="3">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="42">
        <span class="rdc-onum">42</span>
        <span class="rdc-oi" style="background:#ecfdf5;color:#059669;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h5"/><path d="m15 18 2 2 4-4"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume + Company Branding</span>
          <span class="rdc-odesc">Deliver one polished PDF with the pre-screened report first, followed by the candidate resume with company branding.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">3 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 43: Pre-Screened Report + Resume without Company Branding --}}
      <label class="rdc-opt" data-opt="43" data-coins="3">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="43">
        <span class="rdc-onum">43</span>
        <span class="rdc-oi" style="background:#f8fafc;color:#475569;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/><path d="m4 4 16 16"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume without Company Branding</span>
          <span class="rdc-odesc">Download the same report-and-resume package without company logo or company name on the report page.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">3 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 44: Resume + Highlighted Keywords --}}
      <label class="rdc-opt" data-opt="44" data-coins="2">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="44">
        <span class="rdc-onum">44</span>
        <span class="rdc-oi rdc-ic-green" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"/><path d="m7 16 8.5-8.5 3 3L10 19H7v-3z"/><path d="M9 7h6"/><path d="M9 11h4"/><path d="M14 2v6h6"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlighted Keywords</span>
          <span class="rdc-odesc">Download a clean resume copy with the most relevant skills highlighted for quick recruiter review.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">2 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 45: Resume + Highlighted Keywords + Company Branding --}}
      <label class="rdc-opt" data-opt="45" data-coins="3">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="45">
        <span class="rdc-onum">45</span>
        <span class="rdc-oi rdc-ic-green" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"/><path d="m7 16 8.5-8.5 3 3L10 19H7v-3z"/><path d="M9 7h6"/><path d="M9 11h4"/><path d="M14 2v6h6"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Resume + Highlighted Keywords + Company Branding</span>
          <span class="rdc-odesc">Download a branded resume with job-matching keywords highlighted for faster recruiter screening.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">3 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 46: Pre-Screened Report + Resume + Highlighted Keywords + Company Branding --}}
      <label class="rdc-opt" data-opt="46" data-coins="3">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="46">
        <span class="rdc-onum">46</span>
        <span class="rdc-oi" style="background:#eef2ff;color:#4338ca;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M3 12h18"/><path d="M7 7h10v10H7z"/><path d="m15 9 2 2-2 2"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume + Highlighted Keywords + Company Branding</span>
          <span class="rdc-odesc">Create the recruiter package: pre-screened report, branded resume pages, and highlighted job-matching skills.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">3 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 47: Pre-Screened Report + Resume + Highlighted Keywords without Company Branding --}}
      <label class="rdc-opt" data-opt="47" data-coins="3">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="47">
        <span class="rdc-onum">47</span>
        <span class="rdc-oi" style="background:#f8fafc;color:#475569;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M3 12h18"/><path d="M7 7h10v10H7z"/><path d="m4 4 16 16"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Pre-Screened Report + Resume + Highlighted Keywords without Company Branding</span>
          <span class="rdc-odesc">Create the recruiter package with report, resume, and highlighted skills, without company branding.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">3 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 48: Hide Contact Details --}}
      <label class="rdc-opt" data-opt="48" data-coins="2">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="48">
        <span class="rdc-onum">48</span>
        <span class="rdc-oi rdc-ic-blue" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M8 11h8"/><path d="M8 15h5"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details</span>
          <span class="rdc-odesc">Keep candidate name visible while hiding email, phone, mobile, contact and LinkedIn details in browser.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">2 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 49: Hide Candidate Name & Contacts Details --}}
      <label class="rdc-opt" data-opt="49" data-coins="2">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="49">
        <span class="rdc-onum">49</span>
        <span class="rdc-oi rdc-ic-blue" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M9 12h6"/><path d="M12 9v6"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Candidate Name & Contacts Details</span>
          <span class="rdc-odesc">Hide candidate name, email, and phone numbers in a browser-only resume copy.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">2 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 50: Hide Contact Details + Company Branding --}}
      <label class="rdc-opt" data-opt="50" data-coins="3">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="50">
        <span class="rdc-onum">50</span>
        <span class="rdc-oi rdc-ic-green" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M8 12h8"/><path d="M4 20h16"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details + Company Branding</span>
          <span class="rdc-odesc">Keep the name visible, hide contact and LinkedIn details, then add your company header.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">3 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 51: Hide Contact Details + Pre-Screened Report --}}
      <label class="rdc-opt" data-opt="51" data-coins="3">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="51">
        <span class="rdc-onum">51</span>
        <span class="rdc-oi" style="background:#f5f3ff;color:#6d28d9;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details + Pre-Screened Report</span>
          <span class="rdc-odesc">Keep the name visible, hide contact and LinkedIn details, then merge the pre-screened report.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">3 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 52: Hide Contact Details + Pre-Screened Report without Company Branding --}}
      <label class="rdc-opt" data-opt="52" data-coins="3">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="52">
        <span class="rdc-onum">52</span>
        <span class="rdc-oi" style="background:#f8fafc;color:#475569;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="m4 4 16 16"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details + Pre-Screened Report without Company Branding</span>
          <span class="rdc-odesc">Keep the name visible, hide contact and LinkedIn details, then merge a no-brand report.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">3 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 53: Hide Contact Details + Pre-Screened Report + Highlighted Keywords --}}
      <label class="rdc-opt" data-opt="53" data-coins="4">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="53">
        <span class="rdc-onum">53</span>
        <span class="rdc-oi" style="background:#fdf2f8;color:#be185d;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v6c0 5 3.4 7.8 8 9 4.6-1.2 8-4 8-9V7l-8-4z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h5"/><path d="m15 18 2 2 4-4"/><path d="m9 11-6 6v3h3l6-6"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details + Pre-Screened Report + Highlighted Keywords</span>
          <span class="rdc-odesc">Branded pre-screened report on Page 1, followed by contact-hidden resume with role-matched skills highlighted.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">Instant</span>
        </span>
        <span class="rdc-ocirc" aria-hidden="true"></span>
      </label>

      {{-- 54: Hide Contact Details + Pre-Screened Report + Highlighted Keywords without Company Branding --}}
      <label class="rdc-opt" data-opt="54" data-coins="4">
        <input class="rdc-rinput" type="radio" name="rdcFormat" value="54">
        <span class="rdc-onum">54</span>
        <span class="rdc-oi" style="background:#fdf2f8;color:#be185d;" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><circle cx="12" cy="16" r="1"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><path d="M8 17h5"/><path d="m15 18 2 2 4-4"/></svg>
        </span>
        <span class="rdc-obody">
          <span class="rdc-otitle">Hide Contact Details + Pre-Screened Report + Highlighted Keywords without Company Branding</span>
          <span class="rdc-odesc">Keep the name visible, highlight role-matched skills, hide contact channels, and attach an unbranded pre-screened report.</span>
        </span>
        <span class="rdc-ometa">
          <span class="rdc-ocost">4 Coins</span>
          <span class="rdc-otime">Instant</span>
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
    {{-- Live Multi-Stage Stepper Progress Modal / Bar --}}
    <div class="rdc-footer-progress" id="rdcFooterProgress" style="display: none;">
      <div class="rdc-footer-progress-info">
        <span class="rdc-footer-progress-step" id="rdcStatusStep">Initializing document compiler...</span>
        <span class="rdc-footer-progress-pct" id="rdcStatusPct">0%</span>
      </div>
      <div class="rdc-footer-progress-track">
        <div class="rdc-footer-progress-bar" id="rdcProgressBar"></div>
      </div>
      <span class="rdc-footer-progress-detail" id="rdcStatusDetail">Client WASM engine processing document in browser RAM...</span>
    </div>
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

{{-- Word (.doc) Version Unsupported Popup Notification Modal --}}
<div id="rdcDocModalBackdrop" class="rdc-doc-modal-backdrop" aria-hidden="true" style="display:none;">
  <div class="rdc-doc-modal" role="alertdialog" aria-modal="true" aria-labelledby="rdcDocModalTitle" aria-describedby="rdcDocModalDesc">
    <div class="rdc-doc-modal-header">
      <div class="rdc-doc-modal-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
      </div>
      <button type="button" class="rdc-doc-modal-close" id="rdcDocModalCloseBtn" aria-label="Close" title="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    </div>
    <div class="rdc-doc-modal-body">
      <h3 id="rdcDocModalTitle" class="rdc-doc-modal-title">This Word (.doc) file version isn’t supported.</h3>
      <p id="rdcDocModalDesc" class="rdc-doc-modal-text">Please upload the resume as PDF or download the original resume using Option 1.</p>
    </div>
    <div class="rdc-doc-modal-footer">
      <button type="button" class="rdc-doc-modal-btn rdc-doc-modal-btn-primary" id="rdcDocModalOpt1Btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Download Option 1
      </button>
      <button type="button" class="rdc-doc-modal-btn rdc-doc-modal-btn-secondary" id="rdcDocModalDismissBtn">Close</button>
    </div>
  </div>
</div>

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
  var progressEl = latestById('rdcFooterProgress');
  var barEl      = latestById('rdcProgressBar');
  var pctEl      = latestById('rdcStatusPct');
  var stepEl     = latestById('rdcStatusStep');
  var detailEl   = latestById('rdcStatusDetail');
  var opts     = drawer ? drawer.querySelectorAll('#rdcOptsList .rdc-opt') : document.querySelectorAll('#rdcOptsList .rdc-opt');

  function updateProgress(p){
    if(!p) return;
    var numPct = 0;
    var stepText = '';
    var detailText = '';
    if(typeof p === 'object'){
      numPct = Math.min(100, Math.max(0, p.percent != null ? p.percent : 0));
      stepText = p.step || '';
      detailText = p.detail || '';
    } else {
      var match = String(p || '').match(/(\d+)\s*%/);
      numPct = match ? Math.min(100, Math.max(0, parseInt(match[1], 10))) : 0;
      stepText = String(p || '');
    }
    if(progressEl) progressEl.style.display = 'flex';
    if(barEl) barEl.style.width = numPct + '%';
    if(pctEl) pctEl.textContent = numPct + '%';
    if(stepEl && stepText) stepEl.textContent = stepText;
    if(detailEl && detailText) detailEl.textContent = detailText;
    if(btnLbl){
      btnLbl.textContent = 'Downloading Resume (' + numPct + '%)...';
    }
  }
  window.__rdcUpdateProgress = updateProgress;
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
    return 'Jeroworks_Candidate.' + ext;
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

  var docUnsupportedMsg = 'This Word (.doc) file version isn’t supported. Please upload the resume as PDF or download the original resume using Option 1.';

    var optMeta = {
    1: {cost:1, label:'Download Resume',       avail:hasResume && !!(originalR2PublicUrl || originalR2DirectUrl || dlUrl),  dlKey:null, unavailableMsg:'Original resume is not available for download.'},
    5: {cost:1, label:'Download Pre-Screened Report',   avail:true,  dlKey:'rs'},
    41:{cost:2, label:'Resume + Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-branded', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Resume + Company Branding is browser-only and available for direct PDF/DOCX resumes.')},
    42:{cost:3, label:'Pre-Screened Report + Resume + Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-prescreened-branded', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Pre-Screened Report + Resume + Company Branding is browser-only and available for direct PDF/DOCX resumes.')},
    43:{cost:3, label:'Pre-Screened Report + Resume without Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-prescreened-nobrand', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Pre-Screened Report + Resume without Company Branding is browser-only and available for direct PDF/DOCX resumes.')},
    44:{cost:2, label:'Resume + Highlighted Keywords', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-highlighted', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Resume + Highlighted Keywords is browser-only and available for direct PDF/DOCX resumes.')},
    45:{cost:3, label:'Resume + Highlighted Keywords + Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-highlighted-branded', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Resume + Highlighted Keywords + Company Branding is browser-only and available for direct PDF/DOCX resumes.')},
    46:{cost:3, label:'Pre-Screened Report + Resume + Highlighted Keywords + Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-prescreened-highlighted-branded', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Pre-Screened Report + Resume + Highlighted Keywords + Company Branding is browser-only and available for direct PDF/DOCX resumes.')},
    47:{cost:3, label:'Pre-Screened Report + Resume + Highlighted Keywords without Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-prescreened-highlighted-nobrand', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Pre-Screened Report + Resume + Highlighted Keywords without Company Branding is browser-only and available for direct PDF/DOCX resumes.')},
    48:{cost:2, label:'Hide Contact Details', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-contact-hidden', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Hide Contact Details is browser-only and available for direct PDF/DOCX resumes.')},
    49:{cost:2, label:'Hide Candidate Name & Contacts Details', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-anonymized-all', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Hide Candidate Name & Contacts Details is browser-only and available for direct PDF/DOCX resumes.')},
    50:{cost:3, label:'Hide Contact Details + Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-contact-hidden-branded', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Hide Contact Details + Company Branding is browser-only and available for direct PDF/DOCX resumes.')},
    51:{cost:3, label:'Hide Contact Details + Pre-Screened Report', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-contact-hidden-prescreened', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Hide Contact Details + Pre-Screened Report is browser-only and available for direct PDF/DOCX resumes.')},
    52:{cost:3, label:'Hide Contact Details + Pre-Screened Report without Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-contact-hidden-prescreened-nobrand', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Hide Contact Details + Pre-Screened Report without Company Branding is browser-only and available for direct PDF/DOCX resumes.')},
    53:{cost:4, label:'Hide Contact Details + Pre-Screened Report + Highlighted Keywords', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-contact-hidden-prescreened-highlighted', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Hide Contact Details + Pre-Screened Report + Highlighted Keywords is browser-only and available for direct PDF/DOCX resumes.')},
    54:{cost:4, label:'Hide Contact Details + Pre-Screened Report + Highlighted Keywords without Company Branding', avail:(['PDF','DOCX'].indexOf(resumeExt) !== -1) && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl), dlKey:'test-agy-strategy2-contact-hidden-prescreened-highlighted-nobrand', unavailableMsg:(resumeExt === 'DOC' ? docUnsupportedMsg : 'Hide Contact Details + Pre-Screened Report + Highlighted Keywords without Company Branding is browser-only and available for direct PDF/DOCX resumes.')}
  };

  var selectedOpt = 1;
  var isLoading   = false;

  function downloadButtonLabel(label){
    label=rdcCleanLocal(label) || 'Download';
    if(/^(download|edit)\b/i.test(label)) return label;
    return 'Download '+label;
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
      optMeta[1].avail = (originalR2Available && !!(originalR2DirectUrl || originalR2PublicUrl)) || !!dlUrl;
    }
    [41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54].forEach(function(id){
      if(optMeta[id]){
        optMeta[id].avail = ['PDF','DOCX'].indexOf(resumeExt) !== -1 && !!(r2DirectUrl || r2PublicUrl || originalR2DirectUrl || originalR2PublicUrl || dlUrl);
      }
    });
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
  window.ensureDownloadCenterR2Urls = ensureDownloadCenterR2Urls;

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
      {theme:'blue',icon:'resume',title:'1. Resume Options',pill:"Get the candidate's resume in different formats.",ids:[1,41]},
      {theme:'rose',icon:'report',title:'2. Pre-Screened Report',pill:'Get AI-powered insights and verification details.',ids:[5]},
      {theme:'green',icon:'package',title:'3. Recruiter Packages (Report + Resume)',pill:'',ids:[42,43]},
      {theme:'amber',icon:'keyword',title:'4. Highlighted skills',pill:'',ids:[44,45,46,47]},
      {theme:'purple',icon:'privacy',title:'5. Privacy & Anonymization',pill:'Protect candidate information as needed.',ids:[48,49,50,51,52,53,54]}
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
    hiddenBin.setAttribute('aria-hidden','true');
    hiddenBin.style.display='none';
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

  function preloadOnDemandRdcAssets(){
    /* Ultra-lean on-demand preload: ONLY triggers when Download Resume Center opens.
       Zero bytes of overhead during candidate browsing. */
    ensureDownloadCenterR2Urls().then(function(){
      var data = typeof readData === 'function' ? readData() : {};
      var testSources = rdcCollectDownloadSources([r2DirectUrl,r2PublicUrl,originalR2DirectUrl,originalR2PublicUrl,dlUrl]);
      var cacheKey = testSources[0] || (data && (data.id || data.name) ? 'cand_' + (data.id || data.name) : '');
      window.__agyResumeCache = window.__agyResumeCache || {};

      // 1. Pre-fetch candidate resume binary into memory cache silently
      if(cacheKey && (!window.__agyResumeCache[cacheKey] || !window.__agyResumeCache[cacheKey].byteLength)){
        fetchResumeBytesFromSources(testSources).then(function(bytes){
          if(bytes && bytes.byteLength){
            window.__agyResumeCache[cacheKey] = bytes;
            if(resumeExt === 'DOCX' && typeof mammoth !== 'undefined'){
              try {
                mammoth.convertToHtml({ arrayBuffer: bytes.buffer || bytes }).then(function(res){
                  if(res && res.value) window.__rcvResumeHtml = res.value;
                }).catch(function(){});
                mammoth.extractRawText({ arrayBuffer: bytes.buffer || bytes }).then(function(res){
                  if(res && res.value){
                    window.__rcvResumeText = res.value;
                    window.__rdcPreloadedResumeText = res.value;
                  }
                }).catch(function(){});
              } catch(e){}
            }
          }
        }).catch(function(e){ console.warn('On-demand resume preload skipped:', e); });
      }

      // 2. Pre-embed company logo & metric fonts in background
      if(data && (data.reportCompanyLogo || data.reportCompanyLogoRaw) && typeof rsLogoDataUri === 'function'){
        rsLogoDataUri(data.reportCompanyLogo, data.reportCompanyLogoRaw).catch(function(){});
      }
      if(typeof agyEnsureMetricFonts === 'function'){
        agyEnsureMetricFonts().catch(function(){});
      }

      // 3. Pre-warm OpenDoc WASM engine if DOCX
      if(resumeExt === 'DOCX' && typeof rsEnsureOpenDocLab === 'function'){
        rsEnsureOpenDocLab(false).catch(function(){});
      }

      // 4. Pre-scan matched keywords
      if(typeof rsDrawerHighlightedKeywords === 'function'){
        try { rsDrawerHighlightedKeywords(); } catch(e){}
      }
    }).catch(function(){});
  }

  function openRdc(){
    if(!hasDownloadableResume()){
      showNoResumeMessage();
      return;
    }
    renderOpenRdc();
    preloadOnDemandRdcAssets();
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
      var n = parseInt(opt.getAttribute('data-opt'), 10);
      var m = optMeta[n];
      if(m && !m.avail && resumeExt === 'DOC'){
        showDocFileUnsupportedPopup();
        return;
      }
      selectOpt(n);
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
    if(m.dlKey && m.dlKey.indexOf('test-agy-') === 0 && typeof window.__preloadAgyMetricFonts === 'function') window.__preloadAgyMetricFonts();
    if((m.dlKey === 'test-agy-strategy2-branded' || m.dlKey === 'test-agy-strategy2-prescreened-branded' || m.dlKey === 'test-agy-strategy2-prescreened-nobrand' || m.dlKey === 'test-agy-strategy2-highlighted-branded' || m.dlKey === 'test-agy-strategy2-prescreened-highlighted-branded' || m.dlKey === 'test-agy-strategy2-prescreened-highlighted-nobrand' || m.dlKey === 'test-agy-strategy2-contact-hidden-branded' || m.dlKey === 'test-agy-strategy2-contact-hidden-prescreened' || m.dlKey === 'test-agy-strategy2-contact-hidden-prescreened-nobrand' || m.dlKey === 'test-agy-strategy2-contact-hidden-prescreened-highlighted' || m.dlKey === 'test-agy-strategy2-contact-hidden-prescreened-highlighted-nobrand') && typeof window.__preloadRSCombined === 'function') window.__preloadRSCombined();
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

  var docModalBackdrop = latestById('rdcDocModalBackdrop');
  var docModalCloseBtn = latestById('rdcDocModalCloseBtn');
  var docModalDismissBtn = latestById('rdcDocModalDismissBtn');
  var docModalOpt1Btn = latestById('rdcDocModalOpt1Btn');

  /**
   * Displays modal popup notification for legacy Word (.doc) files.
   * Intent: Notifies user that .doc version is not supported in client WASM/browser mode
   * and provides a direct 1-click fallback to Option 1 (Original Resume).
   */
  function showDocFileUnsupportedPopup(){
    if(docModalBackdrop){
      docModalBackdrop.style.display = 'flex';
      docModalBackdrop.setAttribute('aria-hidden', 'false');
    }
    showToast('This Word (.doc) file version isn’t supported.', 'warn');
  }

  function hideDocFileUnsupportedPopup(){
    if(docModalBackdrop){
      docModalBackdrop.style.display = 'none';
      docModalBackdrop.setAttribute('aria-hidden', 'true');
    }
  }

  if(docModalCloseBtn) docModalCloseBtn.addEventListener('click', hideDocFileUnsupportedPopup);
  if(docModalDismissBtn) docModalDismissBtn.addEventListener('click', hideDocFileUnsupportedPopup);
  if(docModalBackdrop){
    docModalBackdrop.addEventListener('click', function(e){
      if(e.target === docModalBackdrop) hideDocFileUnsupportedPopup();
    });
  }
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape' && docModalBackdrop && docModalBackdrop.style.display === 'flex'){
      hideDocFileUnsupportedPopup();
    }
  });

  if(docModalOpt1Btn){
    docModalOpt1Btn.addEventListener('click', function(){
      hideDocFileUnsupportedPopup();
      selectOpt(1);
      if(dlBtn){
        setTimeout(function(){
          dlBtn.click();
        }, 120);
      }
    });
  }

  window.__showDocFileUnsupportedPopup = showDocFileUnsupportedPopup;

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
    totalSeconds = totalSeconds || 30;
    prefix = prefix || 'Downloading Resume...';
    if(dlBtn){
      dlBtn.innerHTML =
        '<span class="rdc-spin"></span>' +
        '<span id="rdcBtnLabel" class="rdc-countdown-text">' + escapeCountdownText(prefix) + '</span>';
      btnLbl = latestById('rdcBtnLabel');
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
        if(btnLbl){
          btnLbl.textContent = escapeCountdownText(prefix) + ' Finalizing...';
        }
        finish(true);
      }, totalSeconds * 1000);
    });
  }

  function beginDownloadCountdown(prefix){
    /* Purpose: standardize the RDC loading UI with a live progress spinner preserving #rdcBtnLabel. */
    runSmartCountdown(prefix || 'Downloading Resume...', 30);
  }

  function resetBtn(){
    stopSmartCountdown();
    isLoading = false;
    if(progressEl) progressEl.style.display = 'none';
    if(barEl) barEl.style.width = '0%';
    if(pctEl) pctEl.textContent = '0%';
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
        if(resumeExt === 'DOC'){
          showDocFileUnsupportedPopup();
          return;
        }
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
        updateProgress({ percent: 30, step: 'Fetching original resume...', detail: 'Retrieving file bytes from storage...' });
        beginDownloadCountdown('Downloading Resume, Kindly wait.');
        try {
          updateProgress({ percent: 75, step: 'Preparing document...', detail: 'Finalizing original resume document...' });
          startOriginalResumeDownload({
            originalR2PublicUrl:originalR2PublicUrl,
            r2PublicUrl:r2PublicUrl,
            originalR2DirectUrl:originalR2DirectUrl,
            r2DirectUrl:r2DirectUrl,
            r2RedirectUrl:r2SignUrl ? (r2SignUrl + (r2SignUrl.indexOf('?') === -1 ? '?' : '&') + 'redirect=1') : '',
            dlUrl:dlUrl,
            resumeExt:resumeExt
          });
          updateProgress({ percent: 100, step: 'Original resume ready!', detail: 'Document ready for download' });
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

      if(m.dlKey === 'rs'){
        /* Option 5: Pre-Screened Report — fully client-side vector PDF */
        if(window.__rsBusy || (typeof window.__buildRS !== 'function' && (!window.AgyCvTrackerDownloadResumeEngine || typeof window.AgyCvTrackerDownloadResumeEngine.buildRecruiterSummary !== 'function'))){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        updateProgress({ percent: 20, step: 'Generating Pre-Screened Report...', detail: 'Compiling candidate evaluation dossier...' });
        beginDownloadCountdown('Generating Report, Kindly wait.');
        var rsFn = (window.AgyCvTrackerDownloadResumeEngine && typeof window.AgyCvTrackerDownloadResumeEngine.buildRecruiterSummary === 'function')
          ? window.AgyCvTrackerDownloadResumeEngine.buildRecruiterSummary
          : window.__buildRS;
        rsFn({ statusCallback: updateProgress }).then(function(ok){
          if(ok){
            updateProgress({ percent: 100, step: 'Pre-Screened Report ready!', detail: 'Document ready for download' });
            setTimeout(closeRdc, 500);
          }
        }).catch(function(){
          showToast('Could not generate Pre-Screened Report. Please try again.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else if(m.dlKey && m.dlKey.indexOf('test-agy-') === 0){
        /* Options 41 through 53 */
        if(window.__rsBusy || (typeof window.__processAgyTestOption !== 'function' && (!window.AgyCvTrackerDownloadResumeEngine || typeof window.AgyCvTrackerDownloadResumeEngine.processOption !== 'function'))){
          showToast('Please wait a moment and try again.', 'warn');
          resetBtn(); return;
        }
        if(resumeExt === 'DOC'){
          showDocFileUnsupportedPopup();
          resetBtn(); return;
        }
        if(['PDF','DOCX'].indexOf(resumeExt) === -1){
          showToast(m.unavailableMsg || 'Download is available for PDF and DOCX resumes.', 'warn');
          resetBtn(); return;
        }
        updateProgress({ percent: 10, step: 'Initializing document compiler...', detail: 'Client WASM engine processing document in browser RAM...' });
        runSmartCountdown('Downloading Resume...', 30);
        ensureDownloadCenterR2Urls().then(function(){
          var testSources = rdcCollectDownloadSources([r2DirectUrl,r2PublicUrl,originalR2DirectUrl,originalR2PublicUrl,dlUrl]);
          if(!testSources.length) throw new Error('Direct resume URL is not available.');
          var optFn = (window.AgyCvTrackerDownloadResumeEngine && typeof window.AgyCvTrackerDownloadResumeEngine.processOption === 'function')
            ? window.AgyCvTrackerDownloadResumeEngine.processOption
            : window.__processAgyTestOption;
          return optFn(selectedOpt,{
            resumeUrl:testSources,
            resumeExt:resumeExt,
            resumePdfUrl:resumePdfUrl,
            statusCallback: updateProgress
          });
        }).then(function(ok){
          if(ok){
            updateProgress({ percent: 100, step: 'Document ready!', detail: 'Download completed successfully' });
            setTimeout(closeRdc, 500);
          }
        }).catch(function(err){
          console.error('Document conversion failed:',err);
          showToast(err && err.message ? err.message : 'Document conversion failed.', 'warn');
        }).finally(function(){
          resetBtn();
        });

      } else {
        showToast('Selected download option is unavailable.', 'warn');
        resetBtn();
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

<style>
  /* Ensure Forward Resume drawer always displays above Candidate Drawer (z-index: 999999) */
  #kt_test_fwd_resume_drawer,
  #kt_test_fwd_resume_drawer_aptk,
  #kt_test_fwd_resume_drawer_clientapp {
    z-index: 1000050 !important;
  }
  /* Preserve smooth horizontal scrolling for Candidate Drawer action bar on 15.6" screens */
  #kt_activitiesApplication .cdr-topbar-group {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  #kt_activitiesApplication .cdr-topbar-group::-webkit-scrollbar {
    display: none;
  }
</style>

<script>
/**
 * Forward Resume Launcher for Candidate Drawer (Rule 1, Rule 3, Rule 7, Rule 15, Rule 17)
 * Authoritative Client-First execution: auto-selects ONLY the currently open candidate
 * and launches the existing Forward Resume workflow (ForwardResumeManager V4.1 with circuit-breaker fallback).
 */
(function() {
  function ensureForwardResumeDrawerShell() {
    var drawerEl = document.getElementById('kt_test_fwd_resume_drawer') ||
                   document.getElementById('kt_test_fwd_resume_drawer_aptk') ||
                   document.getElementById('kt_test_fwd_resume_drawer_clientapp');
    if (!drawerEl) {
      var shellMarkup = 
        '<div id="kt_test_fwd_resume_drawer_toggle" style="display:none;"></div>' +
        '<div id="kt_test_fwd_resume_drawer" class="drawer drawer-end bg-body" data-kt-drawer="true" data-kt-drawer-name="test-fwd-resume"' +
        '    data-kt-drawer-activate="true" data-kt-drawer-overlay="false"' +
        '    data-kt-drawer-width="{default:\'90%\', \'lg\': \'80%\'}" data-kt-drawer-direction="end"' +
        '    data-kt-drawer-toggle="#kt_test_fwd_resume_drawer_toggle" data-kt-drawer-close="#kt_test_fwd_resume_drawer_close"' +
        '    style="z-index: 1000050 !important;">' +
        '  <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column">' +
        '    <div class="card-header px-5 py-0 flex-shrink-0" style="display:none!important;">' +
        '      <div class="d-flex align-items-center py-1"><h3 class="card-title fw-bolder text-dark mb-0">Forward Resume</h3></div>' +
        '      <div class="card-toolbar">' +
        '        <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_test_fwd_resume_drawer_close">' +
        '          <span class="svg-icon svg-icon-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" fill="none"/></svg></span>' +
        '        </button>' +
        '      </div>' +
        '    </div>' +
        '    <div class="card-body position-relative px-0 py-0 flex-grow-1 overflow-auto" id="application_test_fwd_resume_body_content"></div>' +
        '  </div>' +
        '</div>';
      $('body').append(shellMarkup);
      drawerEl = document.getElementById('kt_test_fwd_resume_drawer');
      if (typeof KTDrawer !== 'undefined') {
        KTDrawer.createInstances();
      }
    }
    if (drawerEl) {
      if (!drawerEl.classList.contains('drawer')) {
        drawerEl.classList.add('drawer', 'drawer-end');
      }
      drawerEl.style.setProperty('z-index', '1000050', 'important');
    }
    return drawerEl;
  }

  function ensureForwardResumeManager() {
    if (window.ForwardResumeManager) {
      return Promise.resolve(window.ForwardResumeManager);
    }
    return new Promise(function(resolve, reject) {
      var existing = document.getElementById('fwd_resume_manager_script');
      if (existing) {
        if (window.ForwardResumeManager) return resolve(window.ForwardResumeManager);
        existing.addEventListener('load', function() { resolve(window.ForwardResumeManager); });
        existing.addEventListener('error', function(e) { reject(e); });
        return;
      }
      var s = document.createElement('script');
      s.id = 'fwd_resume_manager_script';
      s.src = "{{ asset('assets/modules/fwd-resume/ForwardResumeManager.js') }}?v={{ @filemtime(public_path('assets/modules/fwd-resume/ForwardResumeManager.js')) ?: '4.1' }}";
      s.onload = function() { resolve(window.ForwardResumeManager); };
      s.onerror = function(err) { reject(err); };
      document.body.appendChild(s);
    });
  }

  window.openCandidateForwardResume = function(candidateId, jobId) {
    if (!candidateId) {
      console.warn('[CDR-FWD] Candidate ID is missing.');
      return;
    }

    var candArr = [parseInt(candidateId, 10)];
    var jobArr = (jobId && parseInt(jobId, 10) > 0) ? [parseInt(jobId, 10)] : [];

    var drawerEl = ensureForwardResumeDrawerShell();

    // Synchronously activate DOM display immediately (< 1ms)
    if (drawerEl) {
      if (!drawerEl.classList.contains('drawer')) {
        drawerEl.classList.add('drawer', 'drawer-end');
      }
      drawerEl.classList.add('drawer-on');
      drawerEl.style.setProperty('transform', 'none', 'important');
      drawerEl.style.setProperty('display', 'flex', 'important');
      drawerEl.style.setProperty('z-index', '1000050', 'important');
      if (typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (!drawer) {
          KTDrawer.createInstances();
          drawer = KTDrawer.getInstance(drawerEl);
        }
        if (drawer) { drawer.show(); }
      }
    }

    if (window.ForwardResumeManager) {
      window.ForwardResumeManager._lastOpenedTime = Date.now();
    }

    ensureForwardResumeManager().then(function(mgr) {
      if (mgr && typeof mgr.initRoutes === 'function' && (!mgr._routes || !mgr._routes.data)) {
        mgr.initRoutes({
          shell: "{{ asset('assets/modules/fwd-resume/shell.v4.html') }}?v={{ @filemtime(public_path('assets/modules/fwd-resume/shell.v4.html')) ?: '4.1' }}",
          engine: "{{ asset('assets/modules/fwd-resume/engine.v4.js') }}?v={{ @filemtime(public_path('assets/modules/fwd-resume/engine.v4.js')) ?: '4.1' }}",
          data: "{{ route('service_company.cvtracker.test_fwd_resume_data') }}",
          recipients: "{{ route('service_company.cvtracker.fwd_recipients') }}",
          intent: "{{ route('service_company.cvtracker.test_fwd_resume_intent') }}",
          send: "{{ route('service_company.cvtracker.test_fwd_resume_send') }}",
          quickUpload: "{{ route('service_company.cvtracker.quick_upload_resume') }}",
          emailTemplate: "{{ url('service_company/cvtracker/email_template') }}",
          legacyFallback: "{{ url('service_company/cvtracker/test_fwd_resume') }}"
        });
      }

      // Enforce clean state: isolate strictly to current candidate
      if (mgr) {
        mgr._lastOpenedTime = Date.now();
        mgr._currentCandidateIds = candArr;
        mgr._currentJobIdArr = jobArr;
        mgr._compiledAttachments = {};
        mgr._recipientsCache = null;
        mgr.open(candArr, jobArr, {});
      }
    }).catch(function(err) {
      console.warn('[CDR-FWD] V4.1 Engine initialization error, triggering fallback:', err);
      if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer) { drawer.show(); } else { $(drawerEl).addClass('drawer-on'); }
      } else if (drawerEl) {
        $(drawerEl).addClass('drawer-on');
      }

      var bodyContainer = $('#application_test_fwd_resume_body_content');
      if (bodyContainer.length) {
        bodyContainer.html(
          '<div class="d-flex align-items-center justify-content-center" style="min-height:240px;">' +
          '<span class="spinner-border spinner-border-sm text-success me-2"></span> Loading Forward Resume Engine...</div>'
        );

        var firstJobId = jobArr.length > 0 ? jobArr[0] : 0;
        var url = "{{ route('service_company.cvtracker.test_fwd_resume', ':id') }}".replace(':id', firstJobId);

        $.ajax({
          type: 'POST',
          url: url,
          data: {
            '_token': "{{ csrf_token() }}",
            'arr': candArr,
            'jobIdArr': jobArr,
            'matchMap': {}
          },
          cache: false,
          success: function(response) {
            if (response && response.status === 'success' && response.view) {
              bodyContainer.html(response.view);
            } else {
              bodyContainer.html('<div class="p-8 text-center text-danger">' + ((response && response.message) || 'Failed to initialize Forward Resume') + '</div>');
            }
          },
          error: function() {
            bodyContainer.html('<div class="p-8 text-center text-danger">Server error initializing Forward Resume.</div>');
          }
        });
      }
    });
  };

  $(document).off('click.fwdResumeClose').on('click.fwdResumeClose', '#kt_test_fwd_resume_drawer_close', function() {
    if (window.ForwardResumeManager && typeof window.ForwardResumeManager.close === 'function') {
      window.ForwardResumeManager.close();
    } else {
      var drawerEl = document.getElementById('kt_test_fwd_resume_drawer') ||
                     document.getElementById('kt_test_fwd_resume_drawer_aptk') ||
                     document.getElementById('kt_test_fwd_resume_drawer_clientapp');
      if (drawerEl) {
        drawerEl.classList.remove('drawer-on');
        drawerEl.style.removeProperty('transform');
        if (typeof KTDrawer !== 'undefined') {
          var drawer = KTDrawer.getInstance(drawerEl);
          if (drawer) drawer.hide();
        }
      }
    }
  });

  window.copyContactText = function(text, btnEl) {
    if (!text || text === '-') return;
    function showCopied(btn) {
      if (!btn) return;
      var originalHtml = btn.innerHTML;
      btn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
      btn.style.borderColor = '#86EFAC';
      btn.style.background = '#F0FDF4';
      setTimeout(function() {
        btn.innerHTML = originalHtml;
        btn.style.borderColor = '';
        btn.style.background = '';
      }, 1500);
    }
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(function() { showCopied(btnEl); }).catch(function() {
        fallbackCopy(text, btnEl);
      });
    } else {
      fallbackCopy(text, btnEl);
    }
    function fallbackCopy(val, btn) {
      var t = document.createElement('textarea');
      t.value = val;
      t.style.position = 'fixed';
      t.style.opacity = '0';
      document.body.appendChild(t);
      t.focus();
      t.select();
      try { document.execCommand('copy'); showCopied(btn); } catch(e){}
      document.body.removeChild(t);
    }
  };
})();
</script>
