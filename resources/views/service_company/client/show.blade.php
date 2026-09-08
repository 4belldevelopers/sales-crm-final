@php
use App\Helper\GeneralMethod;
use Carbon\Carbon;
@endphp
  @php
      
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
        $endOfMonthcomman =  Carbon::now()->endOfMonth();
        
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();

  @endphp
<style>
    @media only screen and (min-width: 900px) and (max-width: 2560px) {

        .table td,
        .table th,
        .table tr {
            width: max-content !important;
        }

        .table> :not(caption)>*>* {
            padding: 0.75em 0rem !important;
        }

        td:nth-child(n+2),
        th:nth-child(n+2) {
            width: revert !important;
            border-bottom: 1px dashed #f5f8fa !important;
        }
    }
    .iti__flag {
        margin-top: 2px !important;
      }
</style>

<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <input type="hidden" name="hid_client_id" id="hid_client_id" value="{{ $client->id }}" />
    <!--begin::Content-->
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        <!--beign::candidate detail-->
        {{-- ── Redesigned detail header (Agency-style): profile card + KPI cards ── --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
          /* ── Profile card ── */
          .cd-hero .profile-card { background:#fff; border-radius:12px; border:1px solid #eff2f5; padding:24px; margin:0 8px 16px; box-shadow:0 2px 12px rgba(17,28,68,.04); }
          .cd-hero .profile-top { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; }
          .cd-hero .profile-left { display:flex; gap:18px; flex:1; min-width:0; }
          .cd-hero .logo-wrap { position:relative; flex-shrink:0; }
          .cd-hero .logo-circle { width:88px; height:88px; border-radius:50%; background:#f8f9fa; border:2px solid #e4e6ef; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:32px; font-weight:700; color:#5e6278; }
          .cd-hero .logo-circle img { width:100%; height:100%; object-fit:cover; }
          .cd-hero .verified-check { position:absolute; top:-2px; right:-2px; width:24px; height:24px; background:#50cd89; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2.5px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,.08); }
          .cd-hero .verified-check i { color:#fff; font-size:10px; }
          .cd-hero .profile-info { flex:1; min-width:0; }
          .cd-hero .profile-name-row { display:flex; align-items:center; gap:10px; margin-bottom:8px; flex-wrap:wrap; }
          .cd-hero .profile-name { font-size:20px; font-weight:700; color:#181c32; }
          .cd-hero .verified-badge { display:inline-flex; align-items:center; gap:4px; background:#e8fff3; color:#47be7d; font-size:11px; font-weight:600; padding:4px 10px; border-radius:50px; border:1px solid rgba(71,190,125,.2); }
          .cd-hero .verified-badge i { font-size:9px; }
          .cd-hero .profile-meta { display:flex; align-items:center; gap:4px 16px; flex-wrap:wrap; font-size:12.5px; color:#7e8299; margin-bottom:12px; }
          .cd-hero .meta-item { display:flex; align-items:center; gap:6px; }
          .cd-hero .meta-item i { font-size:11px; color:#a1a5b7; }
          .cd-hero .meta-divider { color:#d6dae6; }
          .cd-hero .meta-link { color:#009ef7; text-decoration:none; display:flex; align-items:center; gap:4px; }
          .cd-hero .meta-link:hover { text-decoration:underline; }
          .cd-hero .meta-link i.fa-external-link-alt { font-size:9px; }
          /* ── Service/skill tags — colored ATS pills ── */
          .cd-hero .service-tags { display:flex; flex-wrap:wrap; gap:7px; margin-top:2px; }
          .cd-hero .tag {
            display:inline-flex; align-items:center; gap:5px;
            padding:4px 11px;
            background:linear-gradient(135deg,#eef3ff 0%,#e8f0fe 100%);
            border:1px solid #c8d8f8;
            border-radius:20px;
            font-size:11.5px; font-weight:600; color:#3b5fc8;
            letter-spacing:.2px;
          }
          .cd-hero .tag::before { content:"\f02b"; font-family:"Font Awesome 6 Free"; font-weight:900; font-size:9px; color:#7ca0f0; }
          /* ── Status / edit right column ── */
          .cd-hero .profile-right { display:flex; flex-direction:column; align-items:flex-end; gap:12px; flex-shrink:0; }
          .cd-hero .status-badge { display:inline-flex; align-items:center; gap:6px; background:#e8fff3; color:#17a96a; font-size:12.5px; font-weight:600; padding:6px 14px; border-radius:6px; border:1px solid rgba(80,205,137,.25); }
          .cd-hero .status-dot { width:8px; height:8px; background:#50cd89; border-radius:50%; position:relative; }
          .cd-hero .status-dot::after { content:""; position:absolute; inset:-3px; border-radius:50%; border:1px solid #50cd89; animation:cdpulse 2s infinite; }
          @keyframes cdpulse { 0%,100%{ transform:scale(1); opacity:1; } 50%{ transform:scale(1.35); opacity:0; } }

          /* ── KPI counter cards ── */
          .cd-hero .kpi-row { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin:0 8px 16px; }
          .cd-hero .kpi-box {
            background:#fff; border-radius:12px;
            border:1px solid #eff2f5;
            border-left:4px solid #e4e6ef;
            padding:16px 18px;
            transition:all .22s;
            position:relative; overflow:hidden;
          }
          .cd-hero .kpi-box:hover { transform:translateY(-3px); box-shadow:0 10px 28px rgba(17,28,68,.07); }
          /* Color-coded left accent per metric */
          .cd-hero .kpi-box:nth-child(1) { border-left-color:#009ef7; }
          .cd-hero .kpi-box:nth-child(2) { border-left-color:#50cd89; }
          .cd-hero .kpi-box:nth-child(3) { border-left-color:#7239ea; }
          .cd-hero .kpi-box:nth-child(4) { border-left-color:#f6a723; }
          .cd-hero .kpi-box:nth-child(5) { border-left-color:#14b8a6; }
          .cd-hero .kpi-header { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
          .cd-hero .kpi-icon-box { width:42px; height:42px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
          .cd-hero .kpi-icon-blue   { background:#e8f6ff; color:#009ef7; }
          .cd-hero .kpi-icon-green  { background:#e2f9ee; color:#1fad70; }
          .cd-hero .kpi-icon-purple { background:#f3eeff; color:#7239ea; }
          .cd-hero .kpi-icon-orange { background:#fff4d6; color:#d98d00; }
          .cd-hero .kpi-icon-teal   { background:#e5faf7; color:#0d9488; }
          .cd-hero .kpi-label { font-size:11.5px; font-weight:700; color:#5e6278; line-height:1.35; flex:1; min-width:0; }
          .cd-hero .kpi-number { font-size:30px; font-weight:800; color:#181c32; line-height:1; letter-spacing:-0.5px; }
          .cd-hero .kpi-number-sub { font-size:10.5px; color:#a1a5b7; font-weight:500; margin-top:3px; }
          @media (max-width:1200px){ .cd-hero .kpi-row { grid-template-columns:repeat(3,1fr); } }
          @media (max-width:768px){ .cd-hero .kpi-row { grid-template-columns:repeat(2,1fr); } .cd-hero .profile-top { flex-direction:column; } }

          /* ── Jobs card list (.jobx) ── */
          .jobx, .jobx * { font-family:'Poppins',sans-serif; box-sizing:border-box; }
          .jobx .table-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.05); border:1px solid #e4e6ef; overflow:hidden; }
          .jobx .table-header, .jobx .job-row { display:grid; grid-template-columns:2.5fr 1fr 1.1fr 1.2fr .9fr .9fr 2.6fr; align-items:start; column-gap:12px; }
          .jobx .table-header { padding:9px 12px; background:#f7f8fc; border-bottom:1.5px solid #e4e6ef; align-items:center; }
          .jobx .th { font-size:10.5px; font-weight:600; color:#6d7284; text-transform:uppercase; letter-spacing:.4px; display:flex; align-items:center; gap:4px; }
          .jobx .job-row { padding:12px; border-bottom:1px solid #e4e6ef; transition:background .12s; }
          .jobx .job-row:last-of-type { border-bottom:none; }
          .jobx .job-row:hover { background:#fafbff; }
          /* Col: Job Details */
          .jobx .col-job { display:flex; gap:9px; align-items:flex-start; }
          .jobx .drag-col { display:flex; align-items:center; padding-top:14px; flex-shrink:0; color:#a1a5b7; cursor:grab; }
          .jobx .job-logo { width:46px; height:46px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:17px; font-weight:800; color:#fff; overflow:hidden; }
          .jobx .job-logo img { width:100%; height:100%; object-fit:cover; }
          .jobx .job-info { flex:1; min-width:0; }
          .jobx .job-title a { font-size:12.5px; font-weight:700; color:#181c32; text-decoration:none; display:block; line-height:1.35; }
          .jobx .job-title a:hover { color:#3b55e6; }
          .jobx .job-dept { font-size:11px; color:#6d7284; margin-top:1px; }
          .jobx .job-client { font-size:11px; color:#6d7284; font-weight:500; margin-top:1px; margin-bottom:5px; }
          .jobx .job-meta-line { display:flex; align-items:center; flex-wrap:wrap; gap:4px; font-size:10.5px; }
          .jobx .it-job-id { font-size:10px; color:#6a6b70; font-weight:500; }
          .jobx .it-dot-separator { width:3px; height:3px; border-radius:50%; background:#a1a5b7; }
          .jobx .badge-type { display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:20px; font-size:9.5px; font-weight:600; }
          .jobx .badge-internal { background:#eef2ff; color:#3b55e6; }
          .jobx .badge-client   { background:#eefff6; color:#09b760; }
          .jobx .badge-direct   { background:#fff4ee; color:#f4682a; }
          .jobx .skills-inline .badge { font-size:9.5px; padding:1px 5px; margin:0 1px 0 0; border-radius:4px; }
          /* Col: Experience */
          .jobx .col-exp { display:flex; flex-direction:column; gap:5px; padding-top:2px; }
          .jobx .exp-row, .jobx .sal-row { display:flex; align-items:center; gap:5px; font-size:11.5px; font-weight:500; color:#181c32; white-space:nowrap; }
          .jobx .exp-row svg, .jobx .sal-row svg { width:13px; height:13px; color:#a1a5b7; flex-shrink:0; }
          /* Col: Hiring & Location */
          .jobx .col-hl { display:flex; flex-direction:column; gap:4px; padding-top:2px; }
          .jobx .badge-hiring { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:20px; font-size:10.5px; font-weight:600; width:fit-content; cursor:default; }
          .jobx .badge-hiring::before { content:""; width:6px; height:6px; border-radius:50%; }
          .jobx .badge-hiring.active-bg { background:#eafff3; color:#17c653; border:1px solid #b8f0d4; }
          .jobx .badge-hiring.active-bg::before { background:#17c653; }
          .jobx .badge-hiring.hold-bg   { background:#fff8e1; color:#ffa800; border:1px solid #fde4a0; }
          .jobx .badge-hiring.hold-bg::before   { background:#ffa800; }
          .jobx .badge-hiring.closed-bg { background:#ffeef1; color:#f1416c; border:1px solid #ffd5d5; }
          .jobx .badge-hiring.closed-bg::before { background:#f1416c; }
          .jobx .loc-row, .jobx .mode-row { display:flex; align-items:center; gap:4px; font-size:11px; color:#6d7284; font-weight:500; }
          .jobx .loc-row svg, .jobx .mode-row svg { width:12px; height:12px; color:#a1a5b7; flex-shrink:0; }
          /* Col: Hiring Manager */
          .jobx .col-hm { font-size:11.5px; padding-top:2px; }
          .jobx .hm-name  { font-size:12px; font-weight:600; color:#181c32; margin-bottom:2px; }
          .jobx .hm-title { font-size:11px; color:#6d7284; margin-bottom:1px; }
          .jobx .hm-company { font-size:11px; color:#a1a5b7; }
          /* Col: Team / Agency */
          .jobx .col-action { display:flex; flex-direction:column; gap:4px; padding-top:2px; }
          .jobx .action-more { font-size:10.5px; color:#3b55e6; cursor:pointer; font-weight:500; }
          .jobx .jobx-add-link { font-size:11px; color:#5e6278; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; padding:4px 9px; border:1.5px dashed #d1d3e0; border-radius:7px; background:#fafbfc; transition:all .15s; }
          .jobx .jobx-add-link:hover { border-color:#3b55e6; color:#3b55e6; background:#f0f3ff; }
          /* Col: Pipeline */
          .jobx .col-pipeline { display:flex; flex-direction:column; gap:4px; padding-left:10px; border-left:1px solid #eef0f5; }
          .jobx .pipeline-stages { display:flex; align-items:flex-start; gap:2px; flex-wrap:nowrap; padding-bottom:2px; }
          .jobx .stage { display:flex; flex-direction:column; align-items:center; gap:2px; flex:1; min-width:0; }
          .jobx .stage-label { font-size:8.5px; font-weight:600; color:#6d7284; text-transform:uppercase; letter-spacing:.3px; }
          .jobx .stage-badge { min-width:22px; height:20px; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; }
          .jobx .stage-badge a { font-size:9px; font-weight:700; color:inherit; text-decoration:none; }
          .jobx .stage-badge a:hover { opacity:.75; }
          .jobx .s-inw { background:#ffffde; color:#bdbd26; } .jobx .s-inw a { color:#bdbd26; }
          .jobx .s-sou { background:#efefef; color:#000; }   .jobx .s-sou a { color:#000; }
          .jobx .s-scr { background:#f7deff; color:#c000ff; }.jobx .s-scr a { color:#c000ff; }
          .jobx .s-eva { background:#f0f8ff; color:#2896ff; }.jobx .s-eva a { color:#2896ff; }
          .jobx .s-neg { background:#ffe6f2; color:#ff4098; }.jobx .s-neg a { color:#ff4098; }
          .jobx .s-off { background:#fff9ed; color:#ffab00; }.jobx .s-off a { color:#ffab00; }
          .jobx .s-joi { background:#d4ffde; color:#06c732; }.jobx .s-joi a { color:#06c732; }
          .jobx .s-rej { background:#ffe2e5; color:#ff0202; }.jobx .s-rej a { color:#ff0202; }
          .jobx .pipeline-total { font-size:10px; font-weight:600; color:#6d7284; display:flex; flex-wrap:wrap; gap:4px; }
          .jobx .pipeline-total strong { color:#181c32; }
          .jobx .add-date { font-size:10px; color:#a1a5b7; display:flex; align-items:center; gap:4px; }
          .jobx .job-empty { padding:34px 12px; text-align:center; color:#a1a5b7; font-size:12px; }
          /* Skills sub-row (below each job-row, indented to clear drag+logo) */
          .jobx .job-row-skills { display:flex; align-items:center; flex-wrap:wrap; gap:5px; padding:3px 12px 10px 83px; border-bottom:1px solid #e4e6ef; font-size:10.5px; }
          .jobx .job-row-skills .sk-label { font-weight:600; color:#6d7284; flex-shrink:0; }
          .jobx .job-row-skills .sk-tag { display:inline-flex; padding:1px 7px; border-radius:4px; background:#f1f3fa; color:#5e6278; font-size:9.5px; font-weight:600; }
          .jobx .job-row:last-of-type + .job-row-skills { border-bottom:none; }
          @media (max-width:1366px){ .jobx .table-header, .jobx .job-row { grid-template-columns:2.2fr .95fr 1fr 1.1fr .85fr .85fr 2.1fr; } }
          @media (max-width:1024px){ .jobx .table-card { overflow-x:auto; } .jobx .table-header, .jobx .job-row { min-width:980px; } }

          /* ── Interview Tracker table redesign ── */
          #interview_tracker table { border-collapse:separate; border-spacing:0; width:100%; }
          #interview_tracker table thead tr th {
            font-size:11px; font-weight:700; color:#7e8299; letter-spacing:.5px;
            text-transform:uppercase; padding:9px 12px; white-space:nowrap;
            background:#f8f9fb; border-bottom:2px solid #eef0f8;
          }
          #interview_tracker table thead tr th:first-child { border-radius:8px 0 0 8px; }
          #interview_tracker table thead tr th:last-child  { border-radius:0 8px 8px 0; }
          #interview_tracker table tbody tr { transition:background .12s; }
          #interview_tracker table tbody tr:hover td { background:#f5f8ff; }
          #interview_tracker table tbody td { padding:10px 12px; vertical-align:middle; border-bottom:1px solid #f1f3fa; }
          #interview_tracker table tbody tr:last-child td { border-bottom:none; }
          /* Mode pills */
          .cit-mode-pill { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
          .cit-mode-online  { background:#e8f0fe; color:#3b5fc8; border:1px solid #c8d8f8; }
          .cit-mode-offline { background:#f3eeff; color:#7239ea; border:1px solid #d9c8f8; }
          .cit-mode-phone   { background:#e8fff3; color:#17a75a; border:1px solid #b8f0d4; }
          .cit-mode-default { background:#f1f3fa; color:#5e6278; border:1px solid #e4e6ef; }
          /* Level tag under mode */
          .cit-level-tag { display:inline-flex; padding:1px 7px; border-radius:5px; font-size:10px; font-weight:600; background:#f1f3fa; color:#7e8299; margin-top:3px; }
          /* Source badge */
          .cit-source-badge { display:inline-flex; padding:3px 9px; border-radius:20px; background:#f8f9fb; border:1px solid #e4e6ef; font-size:11px; font-weight:600; color:#5e6278; }
          /* Date cell */
          .cit-date-bold { font-size:13px; font-weight:700; color:#181c32; }
          .cit-date-time { font-size:11px; color:#a1a5b7; font-weight:500; }
          /* Feedback status pills */
          .cit-fb-pill { display:inline-flex; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
          .cit-fb-selected   { background:#e8fff3; color:#17a75a; border:1px solid #b8f0d4; }
          .cit-fb-rejected   { background:#fff0f0; color:#d9534f; border:1px solid #ffd5d5; }
          .cit-fb-hold       { background:#fff8e8; color:#d98d00; border:1px solid #fde4a0; }
          .cit-fb-next       { background:#e8f0fe; color:#3b5fc8; border:1px solid #c8d8f8; }
          .cit-fb-default    { background:#f1f3fa; color:#7e8299; border:1px solid #e4e6ef; }
          /* Star rating */
          .cit-stars { display:inline-flex; gap:2px; }
          .cit-stars .cit-star-fill  { color:#f6a723; font-size:12px; }
          .cit-stars .cit-star-empty { color:#e4e6ef; font-size:12px; }
          .cit-rating-label { font-size:10.5px; color:#a1a5b7; font-weight:500; margin-top:2px; }

          /* ── Follow-Up dropdown (Interview Tracker tab) ── */
          #interview_tracker {
            --cit-primary   : #3b5fc8;
            --cit-success   : #17a75a;
            --cit-danger    : #d9534f;
            --cit-border    : #e4e6ef;
            --cit-radius    : 6px;
            --cit-radius-lg : 8px;
            --cit-text-muted: #a1a5b7;
            --cit-text-main : #181c32;
            --cit-bg-main   : #f5f8ff;
            --cit-transition: all .15s ease;
            --cit-shadow-lg : 0 8px 24px rgba(17,28,68,.10);
          }
          .cit-followup-dropdown { position:relative; display:inline-block; margin-top:3px; }
          .cit-followup-btn {
            display:inline-flex; align-items:center; gap:4px;
            padding:3px 8px; border-radius:var(--cit-radius);
            font-size:10px; font-weight:500; font-family:'Poppins',sans-serif;
            border:1px solid var(--cit-border); background:#fff;
            color:var(--cit-text-muted); cursor:pointer;
            transition:var(--cit-transition);
          }
          .cit-followup-btn:hover { background:var(--cit-bg-main); color:var(--cit-primary); border-color:var(--cit-primary); }
          .cit-followup-btn svg  { width:10px; height:10px; }
          .cit-followup-menu {
            position:absolute; top:calc(100% + 4px); left:0;
            background:#fff; border:1px solid var(--cit-border);
            border-radius:var(--cit-radius-lg); box-shadow:var(--cit-shadow-lg);
            z-index:9999; display:none; min-width:150px; overflow:hidden;
          }
          .cit-followup-menu.open { display:block; }
          .cit-followup-item {
            padding:7px 10px; font-size:10px; cursor:pointer;
            transition:var(--cit-transition); display:flex; align-items:center;
            gap:6px; color:var(--cit-text-main);
          }
          .cit-followup-item:hover { background:var(--cit-bg-main); color:var(--cit-primary); }
          .cit-followup-item svg  { width:10px; height:10px; }

          /* ── Job type + ID tags (Interview Tracker) ── */
          .cit-job-tags        { display:flex; align-items:center; gap:5px; margin-top:3px; flex-wrap:wrap; }
          .cit-job-type-badge  { display:inline-flex; align-items:center; padding:2px 7px; border-radius:20px; font-size:9.5px; font-weight:600; }
          .cit-badge-internal  { background:#eef2ff; color:#3b55e6; }
          .cit-badge-client    { background:#eefff6; color:#09b760; }
          .cit-job-id-tag      { font-size:10px; color:#6a6b70; font-weight:500; }
          .cit-dot-sep         { width:3px; height:3px; border-radius:50%; background:#a1a5b7; display:inline-block; }
        </style>
        @php
            // KPI counts for the internal-client detail header cached for 15 minutes to guarantee < 60ms shell rendering.
            $cur_company_id = auth()->user()->company_id;
            $kpiCacheKey = "ats:client_kpi:{$cur_company_id}:{$client->id}";
            $kpiData = cache()->remember($kpiCacheKey, 900, function() use ($cur_company_id, $client) {
                $kpiJobs = GeneralMethod::InternalClientJobsCountAllStatus($cur_company_id, $client->id);
                $kpiPerf = GeneralMethod::InternalClientCandidatesCountList($client->id);
                return [
                    'kpiActive' => $kpiJobs->job_active ?? 0,
                    'kpiSubmitted' => $kpiPerf->all_candidates ?? 0,
                    'kpiInterviews' => $kpiPerf->all_interview ?? 0,
                    'kpiOffers' => $kpiPerf->offers ?? 0,
                    'kpiJoins' => $kpiPerf->Joined ?? 0,
                ];
            });
            $kpiActive     = $kpiData['kpiActive'];
            $kpiSubmitted  = $kpiData['kpiSubmitted'];
            $kpiInterviews = $kpiData['kpiInterviews'];
            $kpiOffers     = $kpiData['kpiOffers'];
            $kpiJoins      = $kpiData['kpiJoins'];

            $detailLogo = '';
            if (!empty(@$client->logo_logical_path) && strlen(@$client->logo_logical_path) > 10) $detailLogo = @$client->logo_logical_path;
            $detailName = ucwords(@$client->name);
            $detailInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', trim($detailName)), 0, 2))));

            $industries_name = '';
            if (trim((string) @$client->industry) != '') { $industries_name = $department[@$client->industry] ?? ''; }
            $tagsArr = array_filter(array_map('trim', explode(',', (string) @$client->tags)));
        @endphp
        <div class="cd-hero">
          <div class="profile-card">
            <div class="profile-top">
              <div class="profile-left">
                <div class="logo-wrap">
                  <div class="logo-circle">
                    @if (!empty($detailLogo))
                      <img src="{{ $detailLogo }}" alt="logo">
                    @else
                      {{ $detailInitials }}
                    @endif
                  </div>
                  <div class="verified-check"><i class="fas fa-check"></i></div>
                </div>
                <div class="profile-info">
                  <div class="profile-name-row">
                    <span class="profile-name">{{ $detailName }}</span>
                    <span class="verified-badge">Verified <i class="fas fa-check"></i></span>
                  </div>
                  <div class="profile-meta">
                    @if ($industries_name)
                      <span class="meta-item"><i class="fas fa-briefcase"></i>{{ $industries_name }}</span>
                      <span class="meta-divider">|</span>
                    @endif
                    <span class="meta-item"><i class="fas fa-map-marker-alt"></i>{{ ucwords($city_name) }}@if($country_name), {{ ucwords($country_name) }}@endif</span>
                    @if (!empty(@$client->website))
                      <span class="meta-divider">|</span>
                      <a href="{{ @$client->website }}" target="_blank" class="meta-link"><i class="fas fa-globe"></i>{{ @$client->website }}<i class="fas fa-external-link-alt"></i></a>
                    @endif
                  </div>
                  <div class="service-tags">
                    @foreach ($tagsArr as $val)
                      <span class="tag">{{ ucwords($val) }}</span>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="profile-right">
                <span class="status-badge"><span class="status-dot"></span>Active</span>
                @if(in_array('edit_clients', $userPermissions))
                <a href="{{ route('service_company.clients.edit', GeneralMethod::value_encrypt($client->id)) }}" class="btn btn-sm btn-light-primary fw-bold" style="font-size:13px;">
                  <i class="fas fa-edit me-1" style="font-size:12px;"></i>Edit Client
                </a>
                @endif
              </div>
            </div>
          </div>
          <div class="kpi-row">
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-blue"><i class="fas fa-briefcase"></i></div>
                <span class="kpi-label">Active Jobs</span>
              </div>
              <div class="kpi-number">{{ $kpiActive }}</div>
              <div class="kpi-number-sub">Open positions</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-green"><i class="fas fa-user-check"></i></div>
                <span class="kpi-label">Candidates Submitted</span>
              </div>
              <div class="kpi-number">{{ $kpiSubmitted }}</div>
              <div class="kpi-number-sub">Total in pipeline</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-purple"><i class="fas fa-calendar-alt"></i></div>
                <span class="kpi-label">Interviews Scheduled</span>
              </div>
              <div class="kpi-number">{{ $kpiInterviews }}</div>
              <div class="kpi-number-sub">Scheduled rounds</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-orange"><i class="fas fa-file-contract"></i></div>
                <span class="kpi-label">Offers Generated</span>
              </div>
              <div class="kpi-number">{{ $kpiOffers }}</div>
              <div class="kpi-number-sub">Offer letters sent</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-teal"><i class="fas fa-user-tie"></i></div>
                <span class="kpi-label">Successful Joins</span>
              </div>
              <div class="kpi-number">{{ $kpiJoins }}</div>
              <div class="kpi-number-sub">Placements closed</div>
            </div>
          </div>
        </div>
        <!--end::candidate details-->
        {{-- ── Redesigned tab bar + Overview (Agency-style); scoped to .cd-maintabs / cd-* classes ── --}}
        <style>
          /* ── Tab navigation ── */
          .cd-maintabs { border-bottom:none !important; flex-wrap:wrap; gap:2px; }
          .cd-maintabs .nav-item { margin-right:0 !important; }
          .cd-maintabs .nav-link {
            padding:12px 18px; border:none !important;
            position:relative; background:none !important;
            border-radius:8px 8px 0 0;
            transition:background .15s;
          }
          .cd-maintabs .nav-link:hover { background:rgba(0,158,247,.06) !important; }
          .cd-maintabs .nav-link .nav-text {
            font-size:13px; font-weight:600 !important;
            color:#5e6278;
            transition:color .15s;
            display:inline-flex; align-items:center; gap:6px;
          }
          .cd-maintabs .nav-link:hover .nav-text { color:#009ef7; }
          .cd-maintabs .nav-link.active { background:rgba(0,158,247,.07) !important; }
          .cd-maintabs .nav-link.active .nav-text { color:#009ef7; font-weight:700 !important; }
          .cd-maintabs .nav-link.active::after {
            content:""; position:absolute; bottom:0; left:12px; right:12px;
            height:3px; background:#009ef7; border-radius:3px 3px 0 0;
          }
          /* Tab count badge */
          .cd-tab-badge {
            display:inline-flex; align-items:center; justify-content:center;
            min-width:20px; height:18px; padding:0 5px;
            background:#009ef7; color:#fff;
            font-size:10px; font-weight:700; border-radius:10px;
            line-height:1;
          }
          .cd-maintabs .nav-link:not(.active) .cd-tab-badge { background:#e4e6ef; color:#5e6278; }
          .cd-content-grid { display:grid; grid-template-columns:1.2fr 0.8fr; gap:16px; align-items:start; }
          @media (max-width:992px){ .cd-content-grid { grid-template-columns:1fr; } }
          .cd-card { background:#fff; border-radius:12px; border:1px solid #eff2f5; padding:24px; }
          .cd-card-head { display:flex; align-items:center; gap:10px; margin-bottom:20px; }
          .cd-card-ic { width:32px; height:32px; border-radius:6px; background:#f5f8fa; display:flex; align-items:center; justify-content:center; color:#a1a5b7; font-size:13px; }
          .cd-card-title { font-size:15px; font-weight:700; color:#181c32; }
          .cd-about { font-size:13px; color:#7e8299; line-height:1.7; }
          .cd-info-list { display:flex; flex-direction:column; }
          .cd-info-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px dashed #eff2f5; gap:12px; }
          .cd-info-row:last-child { border-bottom:none; }
          .cd-info-label { font-size:12px; color:#a1a5b7; font-weight:500; flex-shrink:0; }
          .cd-info-value { font-size:12px; color:#3f4254; font-weight:600; text-align:right; display:flex; align-items:center; gap:6px; justify-content:flex-end; flex-wrap:wrap; }
          .cd-info-value a { color:#009ef7; text-decoration:none; }
          .cd-info-value a:hover { text-decoration:underline; }
          /* data-tab card chrome (Jobs / Interview Tracker / Dashboard) */
          .cd-tabcard { background:#fff; border-radius:12px; border:1px solid #eff2f5; overflow:hidden; margin-top:4px; }
          .cd-tabcard-head { padding:18px 24px; border-bottom:1px solid #eff2f5; }
          .cd-tabcard-title { font-size:16px; font-weight:700; color:#181c32; }
          .cd-tabcard-sub { font-size:12px; color:#a1a5b7; margin-top:2px; }
          .cd-tabwrap { overflow-x:auto; padding:8px 24px 22px; }
          .cd-tabwrap > .card-body { padding-left:0 !important; padding-right:0 !important; }
          .cd-tabwrap table { margin-bottom:0; }
          /* Contacts tab (consultancy data-table look) */
          .cd-tabcard-actions { display:flex; align-items:center; gap:10px; }
          .cd-search { position:relative; }
          .cd-search i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#b5b5c3; font-size:12px; }
          .cd-search input { padding:8px 14px 8px 34px; border:1px solid #e4e6ef; border-radius:8px; font-size:13px; outline:none; width:200px; font-family:inherit; }
          .cd-contacts .data-table { width:100%; border-collapse:collapse; min-width:760px; }
          .cd-contacts .data-table thead tr { background:#f8f9fa; }
          .cd-contacts .data-table th { padding:12px 16px; text-align:left; font-size:11px; font-weight:600; color:#7e8299; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; border:none !important; }
          .cd-contacts .data-table th i { font-size:9px; margin-left:4px; color:#b5b5c3; }
          .cd-contacts .data-table td { padding:14px 16px; font-size:13px; color:#3f4254; border-bottom:1px solid #f5f8fa; vertical-align:middle; }
          .cd-contacts .data-table tr:hover td { background:#fafbfc; }
          .cd-contacts .table-checkbox { width:16px; height:16px; accent-color:#009ef7; cursor:pointer; }
          .cd-contacts .contact-cell { display:flex; align-items:center; gap:12px; }
          .cd-contacts .contact-avatar-table { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:14px; color:#fff; flex-shrink:0; }
          .cd-contacts .contact-name-table { font-size:14px; font-weight:600; color:#181c32; }
          .cd-contacts .contact-primary-badge { display:inline-flex; align-items:center; gap:3px; background:#e8fff3; color:#47be7d; font-size:10px; font-weight:600; padding:2px 8px; border-radius:50px; margin-top:2px; }
          .cd-contacts .contact-primary-badge i { font-size:8px; }
          .cd-contacts .email-cell { color:#3f4254; font-size:13px; }
          .cd-contacts .email-cell:hover { color:#009ef7; }
          .cd-contacts .phone-cell { display:flex; align-items:center; gap:8px; font-size:13px; color:#3f4254; }
          .cd-contacts .phone-digits { font-size:13px; color:#3f4254; font-weight:500; }
          .cd-contacts .status-badge-table { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; padding:4px 12px; border-radius:50px; }
          .cd-contacts .status-badge-table.active { background:#e8fff3; color:#47be7d; }
          .cd-contacts .status-badge-table.inactive { background:#f5f8fa; color:#a1a5b7; }
          .cd-contacts .status-badge-table i { font-size:6px; }
          .cd-contacts .action-btns { display:flex; align-items:center; gap:6px; }
          .cd-contacts .action-btn-table { width:32px; height:32px; border-radius:6px; border:1px solid #e4e6ef; background:#fff; color:#7e8299; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; font-size:12px; text-decoration:none; }
          .cd-contacts .action-btn-table:hover { border-color:#009ef7; color:#009ef7; background:#f1faff; }
        </style>
        <!--begin::Tabs-->
        <div class="card card-custom mx-2">
            <!--Begin::Tabs Header-->
            <div class="card-header card-header-tabs-line">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x cd-maintabs"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#overview">
                                <span class="nav-text"><i class="fas fa-th-large" style="font-size:11px;opacity:.7;"></i>Overview</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#contacts">
                                <span class="nav-text"><i class="fas fa-address-book" style="font-size:11px;opacity:.7;"></i>Contacts</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#requisitions">
                                <span class="nav-text">
                                    <i class="fas fa-briefcase" style="font-size:11px;opacity:.7;"></i>Jobs
                                    @if($kpiActive > 0)<span class="cd-tab-badge">{{ $kpiActive }}</span>@endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#candidate_list">
                                <span class="nav-text"><i class="fas fa-users" style="font-size:11px;opacity:.7;"></i>Candidate
                                    @if($kpiSubmitted > 0)<span class="cd-tab-badge">{{ $kpiSubmitted }}</span>@endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#interview_tracker">
                                <span class="nav-text">
                                    <i class="fas fa-calendar-alt" style="font-size:11px;opacity:.7;"></i>Interview Tracker
                                    @if($kpiInterviews > 0)<span class="cd-tab-badge">{{ $kpiInterviews }}</span>@endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#candidate_joining">
                                <span class="nav-text"><i class="fas fa-user-check" style="font-size:11px;opacity:.7;"></i>Joinings
                                    @if($kpiJoins > 0)<span class="cd-tab-badge">{{ $kpiJoins }}</span>@endif
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#candidateDashboard">
                                <span class="nav-text"><i class="fas fa-chart-bar" style="font-size:11px;opacity:.7;"></i>Client Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!--end::Tabs Header-->
            <!--Begin::Body-->
            <div class="card-body py-0">
                <div class="tab-content pt-5">
                    <!--begin::Overview Content-->
                    <div class="tab-pane active" id="overview" role="tabpanel">
                        @if(in_array('edit_clients', $userPermissions))
                        <div class="d-flex justify-content-end pt-4 pb-2">
                            <a href="{{ route('service_company.clients.edit', GeneralMethod::value_encrypt($client->id)) }}" class="btn btn-primary align-self-center">Edit Client</a>
                        </div>
                        @endif
                        <div class="cd-content-grid py-5">
                            <div class="cd-card">
                                <div class="cd-card-head">
                                    <div class="cd-card-ic"><i class="fas fa-info-circle"></i></div>
                                    <h3 class="cd-card-title">About the Client</h3>
                                </div>
                                <div class="cd-about">
                                    {!! @$client->description !!}
                                </div>
                            </div>
                            <div class="cd-card">
                                <div class="cd-card-head">
                                    <div class="cd-card-ic"><i class="fas fa-building"></i></div>
                                    <h3 class="cd-card-title">Client Information</h3>
                                </div>
                                <div class="cd-info-list">
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Headquarters</span>
                                        <span class="cd-info-value">{{ ucwords($city_name) }}@if($country_name), {{ ucwords($country_name) }}@endif</span>
                                    </div>
                                    @if (!empty(@$client->website))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Website</span>
                                        <span class="cd-info-value"><a href="{{ @$client->website }}" target="_blank">{{ @$client->website }} <i class="fas fa-external-link-alt"></i></a></span>
                                    </div>
                                    @endif
                                    @if (!empty(@$client->email))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Email</span>
                                        <span class="cd-info-value"><a href="mailto:{{ @$client->email }}">{{ @$client->email }}</a></span>
                                    </div>
                                    @endif
                                    @if ($industries_name)
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Industry</span>
                                        <span class="cd-info-value">{{ $industries_name }}</span>
                                    </div>
                                    @endif
                                    @if (isset($client->company_type) && $client->company_type !== '' && isset($companyType[$client->company_type]))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Company Type</span>
                                        <span class="cd-info-value">{{ $companyType[$client->company_type] }}</span>
                                    </div>
                                    @endif
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Vendor ID</span>
                                        <span class="cd-info-value">VEN-{{ @$client->id }}</span>
                                    </div>
                                    @if (@$client->founded)
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Founded</span>
                                        <span class="cd-info-value">{{ @$client->founded }}</span>
                                    </div>
                                    @endif
                                    @if (@$client->company_sizes)
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Number of Employees</span>
                                        <span class="cd-info-value">{{ number_format(@$client->company_sizes) }}+</span>
                                    </div>
                                    @endif
                                    @php $tagsList = array_filter(array_map('trim', explode(',', (string) @$client->tags))); @endphp
                                    @if (count($tagsList))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Specialties</span>
                                        <span class="cd-info-value">{{ ucwords(implode(', ', $tagsList)) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="contacts" role="tabpanel">
                        <div class="cd-tabcard cd-contacts">
                            <div class="cd-tabcard-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                <div>
                                    <div class="cd-tabcard-title">Contacts</div>
                                    <div class="cd-tabcard-sub">Manage and view all contacts associated with this client.</div>
                                </div>
                                <div class="cd-tabcard-actions">
                                    <div class="cd-search"><i class="fas fa-search"></i><input type="text" id="contactSearchInput" placeholder="Search contacts..." /></div>
                                </div>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="data-table" id="contactList">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="table-checkbox" /></th>
                                            <th>Contact <i class="fas fa-sort"></i></th>
                                            <th>Designation <i class="fas fa-sort"></i></th>
                                            <th>Email <i class="fas fa-sort"></i></th>
                                            <th>Phone <i class="fas fa-sort"></i></th>
                                            <th>Status <i class="fas fa-sort"></i></th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::Contacts Content-->

                    <!--begin::candidate_list Candidate List-->
                    <div class="tab-pane" id="candidate_list" role="tabpanel">

                        <div class="card-body px-0 py-7">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->

                                    <tr class="fw-bolder text-muted">

                                        <th class="min-w-150px">Name</th>
                                        <th class="min-w-15px">Exp.</th>
                                        <th class="min-w-15px">Salary</th>
                                        <th class="min-w-15px">Notice</th>
                                        <th class="min-w-15px">Qualification</th>
                                        <th class="min-w-15px">Location</th>
                                        <th class="min-w-15px"> Source</th>
                                        <th class="min-w-15px">Upload</th>
                                        <th class="min-w-15px">Jobs</th>
                                        <th class="min-w-15px">Stage</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <tbody id="candidatetList">

                                </tbody>

                            </table>
                            <!--end::Table-->
                            <div id="paginationcandidate">
                                <!-- Pagination links will be appended here -->
                            </div>
                        </div>
                        <!--end::Contacts Content-->
                    </div>
                    <!--end::candidate_list Candidate List-->

                    <!--begin::interview_tracker Candidate List-->
                    <div class="tab-pane" id="interview_tracker" role="tabpanel">
                        <div class="cd-tabcard"><div class="cd-tabcard-head"><div class="cd-tabcard-title">Interview Tracker</div><div class="cd-tabcard-sub">Interviews scheduled with this client's candidates.</div></div><div class="cd-tabwrap">

                        <div class="card-body px-0 py-7">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->

                                    <tr class="fw-bolder text-muted">
                                        <th>Candidate</th>
                                        <th>Job</th>
                                        <th>Mode</th>
                                        <th><i class="fa fa-users" style="font-size:13px"></i> Interviewers</th>
                                        <th>Source</th>
                                        <th><i class="fas fa-calendar-alt" style="font-size:11px; opacity:.7"></i> Date</th>
                                        <th>Feedback</th>
                                        <th><i class="fas fa-star" style="font-size:11px; color:#f6a723"></i> Rating</th>
                                        <th>Stage</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <tbody id="candidatetListinterviewtracker">

                                </tbody>

                            </table>
                            <!--end::Table-->
                            <div id="paginationcandidateinterviewtracker">
                                <!-- Pagination links will be appended here -->
                            </div>
                        </div>
                        <!--end::Contacts Content-->
                        </div></div>
                    </div>
                    <!--end::interview_tracker Candidate List-->

                    <!--begin::candidate_joining Candidate List-->
                    <div class="tab-pane" id="candidate_joining" role="tabpanel">

                        <div class="card-body px-0 py-7">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->

                                    <tr class="fw-bolder text-muted">

                                        <th class="min-w-150px">Name</th>
                                        <th class="min-w-15px">Exp.</th>
                                        <th class="min-w-15px">Salary</th>
                                        <th class="min-w-15px">Notice</th>
                                        <th class="min-w-15px">Qualification</th>
                                        <th class="min-w-15px">Location</th>
                                        <th class="min-w-15px"> Source</th>
                                        <th class="min-w-15px">Upload</th>
                                        <th class="min-w-15px">Jobs</th>
                                        <th class="min-w-15px">Stage</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <tbody id="candidatetListJoin">

                                </tbody>

                            </table>
                            <!--end::Table-->
                            <div id="paginationcandidatejoin">
                                <!-- Pagination links will be appended here -->
                            </div>
                        </div>
                        <!--end::Contacts Content-->
                    </div>
                    <!--end::candidate_joining Candidate List-->


                    

                <!--begin::candidateDashboard Content-->
                    <div class="tab-pane" id="candidateDashboard" role="tabpanel">
                        <div class="cd-tabcard"><div class="cd-tabcard-head"><div class="cd-tabcard-title">Client Dashboard</div><div class="cd-tabcard-sub">Hiring analytics for this client.</div></div><div class="cd-tabwrap">
                            <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0">
                            <div class="card-body px-0 pt-0 pb-0">
                                <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">

                                <div class="row gx-5 gx-xl-10 mb-xl-10">
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                                            @php
                                            $active_count = $counts->get('active', 0);
                                            $hold_count = $counts->get('hold', 0);
                                            $inactive_count = $counts->get('inactive', 0);
                                            $alljobs_count = $active_count + $hold_count + $inactive_count;
                                            @endphp
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">   
                                                    <!--begin::Info--> 
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Currency-->
                                                        <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                                        <!--end::Currency-->

                                                        <!--begin::Amount-->
                                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $inactive_count+$active_count+$hold_count }}</span>
                                                        <!--end::Amount-->

                                                        <!--begin::Badge-->
                                                        {{-- <span class="badge badge-light-success fs-base">                                
                                                            <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i> 
                                                            2.2%
                                                        </span>             --}}
                                                        <!--end::Badge--> 
                                                    </div>
                                                    <!--end::Info--> 

                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">All Jobs</span>
                                                    <!--end::Subtitle--> 
                                                </div>
                                                <!--end::Title-->      
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body pt-2 pb-4 d-flex align-items-center">
                                                <!--begin::Chart-->
                                                <div class="d-flex flex-center me-5 pt-2">
                                                    <div 
                                                        id="kt_card_widget_4_chart_<?php echo @$client->id; ?>" 
                                                        style="min-width: 170px; min-height: 170px" 
                                                        data-kt-size="170" 
                                                        data-kt-line="11">
                                                        <span></span>
                                                        <canvas id="allJobGrapg_<?php echo @$client->id; ?>" height="70" width="70"></canvas>
                                                    </div>
                                                </div>
                                                <!--end::Chart-->
                                                
                                                <!--begin::Labels-->
                                                <div class="d-flex flex-column content-justify-center w-100">
                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center">
                                                        <!--begin::Bullet-->
                                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #22c55e"></div>
                                                        <!--end::Bullet-->

                                                        <!--begin::Label-->
                                                        <div class="text-gray-500 flex-grow-1 me-4">Active Jobs</div>
                                                        <!--end::Label-->

                                                        <!--begin::Stats-->
                                                        <div class="fw-bolder text-gray-700 text-xxl-end">{{ $active_count }}</div>
                                                        <!--end::Stats-->
                                                    </div>
                                                    <!--end::Label-->

                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center my-3">
                                                        <!--begin::Bullet-->
                                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #ffab00"></div>
                                                        <!--end::Bullet-->

                                                        <!--begin::Label-->
                                                        <div class="text-gray-500 flex-grow-1 me-4">Hold Jobs</div>
                                                        <!--end::Label-->

                                                        <!--begin::Stats-->
                                                        <div class="fw-bolder text-gray-700 text-xxl-end">{{ $hold_count }}</div>
                                                        <!--end::Stats-->                    
                                                    </div>
                                                    <!--end::Label-->

                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center">
                                                        <!--begin::Bullet-->
                                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #ff0202"></div>
                                                        <!--end::Bullet-->

                                                        <!--begin::Label-->
                                                        <div class="text-gray-500 flex-grow-1 me-4">Closed Jobs</div>
                                                        <!--end::Label-->

                                                        <!--begin::Stats-->
                                                        <div class=" fw-bolder text-gray-700 text-xxl-end">{{ $inactive_count }}</div>
                                                        <!--end::Stats-->                      
                                                    </div>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Labels-->
                                            </div>
                                            <!--end::Card body-->

                                        </div>                       
                                    </div>
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush  h-md-50 mb-5 mb-xl-10">
                                        <!--begin::Card widget 6-->
                                        <div class="card card-flush  h-md-50 mb-5 mb-xl-10">
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">   
                                                    <!--begin::Info--> 
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Currency-->
                                                        <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                                        <!--end::Currency-->

                                                        <!--begin::Amount-->
                                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotal}}</span>
                                                        <!--end::Amount-->

                                                        <!--begin::Badge-->
                                                        {{-- <span class="badge badge-light-success fs-base">                                
                                                            <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i> 
                                                            2.6%
                                                        </span>             --}}
                                                        <!--end::Badge--> 
                                                    </div>
                                                    <!--end::Info--> 

                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Added in last 7 days</span>
                                                    <!--end::Subtitle--> 
                                                </div>
                                                <!--end::Title-->           
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body d-flex align-items-end px-3 pb-10">
                                                <!--begin::Chart-->
                                                <div id="kt_card_widget_6_chart_<?php echo @$client->id; ?>" class="w-100" style="height: 200px">
                                                    <div 
                                                        id="kt_card_widget_4_chart_<?php echo @$client->id; ?>" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="candidates_left_chart_<?php echo @$client->id; ?>" height="170"></canvas>
                                                    </div>
                                                </div>
                                                <!--end::Chart-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                        <!--end::Card widget 6--> 
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-cus-12 col-xl-12 col-xxl-12 mb-5 mb-xl-0">
                                        <div class="card card-flush overflow-hidden h-md-100">
                                            <!--begin::Header-->
                                            <div class="card-header py-5">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">   
                                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$total_candidates}} </span>         
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Added In {{ date('d M y',strtotime($startOfMonth)) }} To {{ date('d M y',strtotime($endOfMonth)) }}</span>
                                                    {{-- <span class="text-gray-500 mt-1 fw-semibold fs-6">Users from all channels</span> --}}
                                                </h3>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body d-flex justify-content-between flex-column pb-1 px-0" style="position: relative">  
                                                <!--begin::Chart-->
                                                {{-- <div style="position: absolute; bottom: 50px; width: 222px; height: 53px; background: #ffffff; z-index: 99;"> </div> --}}
                                                <div id="kt_charts_widget_3" class="min-h-auto">

                                                    <canvas id="myChart_<?php echo @$client->id; ?>"></canvas>

                                                    
                        
                                                    {{-- <div id="chart-container" style="width: 600px; height: 420px; margin: auto;"></div> --}}
                                                </div><br>
                                                <!--end::Chart--> 
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                    </div>
                                </div>
                                <div class="row gx-5 gx-xl-10 mb-xl-10">
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush h-md-50 mb-xl-10">
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">   
                                                    <!--begin::Info--> 
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Amount-->
                                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaloffer}}</span>
                                                        <!--end::Amount-->
                                                        
                                                        <!--begin::Badge-->
                                                        {{-- <span class="badge badge-light-danger fs-base">                                
                                                            <i class="ki-outline ki-arrow-down fs-5 text-danger ms-n1"></i> 
                                                            2.2%
                                                        </span>             --}}
                                                        <!--end::Badge-->                
                                                    </div>
                                                    <!--end::Info--> 

                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Offers In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>             
                                                    <!--end::Subtitle--> 
                                                </div>
                                                <!--end::Title-->         
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body d-flex align-items-end pt-0">
                                                <!--begin::Progress-->
                                                

                                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                                    <div 
                                                        id="kt_card_widget_4_chart_<?php echo @$client->id; ?>" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="candidates_offer_chart_<?php echo @$client->id; ?>" height="170"></canvas>
                                                    </div>
                                                </div>
                                                <!--end::Progress-->
                                            </div>
                                            <!--end::Card body-->

                                        </div>                        
                                    </div>
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush h-md-50 mb-xl-10">
                                        
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">                
                                                    <!--begin::Amount-->
                                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaljoin}}</span>
                                                    <!--end::Amount-->           

                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Joined In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>
                                                    <!--end::Subtitle--> 
                                                </div>
                                                <!--end::Title-->           
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Card body-->
                                            <div class="card-body d-flex flex-column justify-content-end pe-0">
                                                <!--begin::Title-->
                                                <!--end::Title-->

                                                <!--begin::Users group-->
                                                <div class="symbol-group symbol-hover flex-nowrap">

                                                    <div 
                                                        id="kt_card_widget_4_chart_<?php echo @$client->id; ?>" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="candidates_join_chart_<?php echo @$client->id; ?>" height="170"></canvas>
                                                    </div>


                                                        
                                                </div>
                                                <!--end::Users group-->
                                            </div>
                                            <!--end::Card body-->

                                        </div>
                                    </div>

                                </div>

                                <!--Start Interview Section-->
                                    <div class="row gy-5 g-xl-10">
                                        <!--begin::Col-->
                                        <div class="col-xl-4 mb-xl-10">
                                            
                                            <!--begin::List widget 17-->
                                            <div class="card card-flush h-xl-100">
                                                <!--begin::Header-->
                                                <div class="card-header pt-7">
                                                    <!--begin::Title-->
                                                    <h3 class="card-title align-items-start flex-column">
                                                        <span class="card-label fw-bold text-gray-800">Upcoming Interviews</span>
                                                    </h3>
                                                    <!--end::Title-->
                                                    <!--end::Toolbar-->
                                                </div>
                                                <!--end::Header-->

                                                <!--begin::Body-->
                                                <div class="card-body pt-0"> 
                                                    
                                                        <!--begin::Content-->                                  
                                                        <div class="d-flex flex-stack my-5">
                                                            <span class="text-gray-500 fs-7 fw-bold"></span>
                                                            
                                                            <span class="text-gray-500 fw-bold fs-7"></span>
                                                        </div>                   
                                                        <!--end::Content-->

                                                        @if (count($upcomingData) > 0)
                                                            @foreach ($upcomingData as $rowArr)

                                                                @php
                                                                $row = (object) $rowArr;
                                                                $schedule_date = Carbon::parse($row->schedule_date)->format('M d');
                                                                $schedule_date_explode = explode(" ",$schedule_date);
                                                                @endphp
                                                    
                                                                <!--begin::Item-->
                                                                <div class="d-flex flex-stack">                 
                                                                    <!--begin::Wrapper-->                                  
                                                                    <div class="d-flex align-items-center me-3">
                                                                        <!--begin::Icon-->                         
                                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-40px h-40px mr-2 text-center" style="display: inline-grid">
                                                                        <span style="font-size: 12px; font-weight: 600;">{{$schedule_date_explode[0]}}</span><span style="color: #373bed; font-size: 17px; font-weight: 900;">{{$schedule_date_explode[1]}}</span>                
                                                                        </a>                 
                                                                        <!--end::Icon-->

                                                                        <!--begin::Section-->                                  
                                                                        <div class="flex-grow-1">
                                                                            <a href="javascript:;" class="text-gray-800 text-hover-primary fs-5 fw-bold lh-0">{{ucwords(mb_substr(@$row->full_name, 0, 20))}}</a>
                                                                            
                                                                            <span class="text-gray-500 fw-semibold d-block fs-7">{{ucwords(mb_substr(@$row->current_designation, 0, 21))}}</span>
                                                                        </div>                   
                                                                        <!--end::Section-->
                                                                    </div>                   
                                                                    <!--end::Wrapper-->                 

                                                                    <!--begin::Value-->
                                                                    <span class="text-gray-800 fw-bold fs-6">
                                                                        {{$row->schedule_to_time. ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '')}} <br>
                                                                        <span class="text-gray-500 fw-semibold d-block fs-7">
                                                                        {{ucwords(mb_substr($row->mode_name, 0, 13))}} 
                                                                        <span> 
                                                                    </span>
                                                                    <!--end::Value-->               
                                                                </div>
                                                                <!--end::Item-->

                                                                <!--begin::Separator-->
                                                                <div class="separator separator-dashed my-4"></div>
                                                                <!--end::Separator-->
                                                            @endforeach
                                                        @else
                                                            <table border="1" width="100%" cellpadding="10">
                                                                <tbody>
                                                                        <tr>
                                                                            <td colspan="4" align="center">No records found</td>
                                                                        </tr>
                                                                </tbody>
                                                            </table>
                                                        @endif                                   
                                                    
                                                </div>
                                                <!--end::Body-->
                                            </div>
                                            <!--end::List widget 17-->     
                                        </div>
                                        <!--end::Col-->      

                                        <!--begin::Col-->
                                        <div class="col-xl-8 mb-5 mb-xl-10">
                                            
                                            <!--begin::Table widget 6-->
                                            <div class="card card-flush h-xl-100">
                                                <!--begin::Header-->
                                                <div class="card-header pt-7" style="position: relative">
                                                    <!--begin::Title-->
                                                    <h3 class="card-title align-items-start flex-column">			
                                                        <span class="card-label fw-bold text-gray-800">Candidates Interviews</span>
                                                    </h3>
                                                    <!--end::Title-->
                                                    <span class="" style="position: absolute; right: 14px; top: 26px;"> 
                                                    <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdatagetmonth(this.value)">
                                                            <option value="1">Last 6 Months</option>
                                                            <option value="0">Last 7 Days</option>
                                                        </select>
                                                    </span>
                                                    
                                                </div>
                                                <!--end::Header-->

                                                <!--begin::Body-->
                                                <div class="card-body">       
                                                    <!--begin::Tab Content-->
                                                    <div class="tab-content">
                                                            <!--begin::Tap pane-->
                                                            <div class="tab-pane fade active show" id="kt_stats_widget_6_tab_1_<?php echo @$client->id; ?>" role="tabpanel">
                                                                <!--begin::Table container-->

                                                                <div id="kt_card_widget_6_chart_<?php echo @$client->id; ?>" class="w-100" style="height: 200px">

                                                                    <div class="inetview_recent_chart_<?php echo @$client->id; ?> d-none" id="kt_card_widget_4_chart_<?php echo @$client->id; ?>" 
                                                                        style="min-width: 100%; min-height: 200px">
                                                                        <canvas id="inetview_recent_chart_<?php echo @$client->id; ?>" height="300"></canvas>
                                                                    </div>
                                                                    
                                                                    <div class="inetview_recent_chart_month_<?php echo @$client->id; ?>" id="kt_card_widget_4_chart" 
                                                                            style="min-width: 100%; min-height: 200px">
                                                                            <canvas id="inetview_recent_chart_month_<?php echo @$client->id; ?>" height="230"></canvas>
                                                                    </div>

                                                                </div>

                                                                    
                                                                <!--end::Table-->
                                                            </div>
                                                            <!--end::Tap pane-->
                                                    </div>
                                                    <!--end::Tab Content-->        
                                                </div>
                                                <!--end: Card Body-->
                                            </div>
                                            <!--end::Table widget 6-->   
                                        </div>
                                        <!--end::Col-->      
                                    </div>
                                <!--End Interview Section-->


                                </div>
                            </div>
                            </div>
                        </div></div>
                    </div>
                <!--end begin::candidateDashboard Content-->



                    <!--begin::Requisitions Content-->
                    <div class="tab-pane" id="requisitions" role="tabpanel">
                        <div class="cd-tabcard"><div class="cd-tabcard-head"><div class="cd-tabcard-title">Jobs</div><div class="cd-tabcard-sub">Roles open with this client.</div></div><div class="cd-tabwrap">
                        <!--begin::Sub tabs-->
                        {{-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 border-bottom-0">
                        <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#all-requisitions">All Req.</a> 
                        </li> --}}
                        {{-- <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#client-requisitions">HCL Req.</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#custom-requisitions">Custom Req.</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#jobs">Jobs</a> </li> --}}
                        {{-- </ul> --}}
                        <!--end::Sub tabs-->
                        <!--begin::Requisitions Content-->
                        <div class="tab-content" id="myTabContent">
                            <!--begin::All Req.-->
                            <div class="tab-pane fade show active" id="all-requisitions" role="tabpanel">
                                <!--begin::All Req. header-->
                                <div class="card-header cursor-pointer px-0 d-none">
                                    <!--begin::Card title-->
                                    <div class="card-title m-0">
                                        <h3 class="fw-bolder m-0">All Jobs</h3>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Actions-->
                                    <div class="d-flex align-items-center py-1">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1 me-3">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                        height="2" rx="1"
                                                        transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                    <path
                                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <input type="text" id="inputJobSearch" name="inputJobSearch"
                                                class="form-control form-control-solid w-250px ps-14"
                                                style="padding: .6rem 1rem;" placeholder="Search..." />
                                        </div>
                                        <!--end::Search-->
                                        <div class="card-toolbar">
                                            <!--begin::Toolbar-->
                                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">



                                                @if (in_array('add_jobs', $userPermissions))
                                                    <!--begin::Add Requisitions-->
                                                    {{-- echo url('service_company/jobs/create'); --}}
                                                    <a
                                                        href="{{ url('service_company/jobs/curstomcreate/' . $client->id) }}">
                                                        <button type="button"
                                                            class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                                            fill="#000000" fill-rule="nonzero"
                                                                            opacity="0.3" />
                                                                        <path
                                                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                                            fill="#000000" fill-rule="nonzero" />
                                                                    </g>
                                                                </svg>
                                                            </span> Add Jobs</button>
                                                    </a>
                                                    <!--end::Add Requisitions-->
                                                @endif
                                            </div>
                                            <!--end::Toolbar-->
                                        </div>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::All Req. header-->
                                <!--begin::All Req. Content-->
                                <div class="card-body px-0 py-4">
                                    @if (in_array('view_jobs', $userPermissions))
                                        <div class="jobx">
                                            <div class="table-card">
                                                <div class="table-header">
                                                    <div class="th">Job Details</div>
                                                    <div class="th">Experience</div>
                                                    <div class="th">Hiring &amp; Location</div>
                                                    <div class="th">Hiring Manager</div>
                                                    <div class="th"><i class="fa fa-users me-1"></i> Team</div>
                                                    <div class="th"><i class="fa fa-handshake me-1"></i> Agency</div>
                                                    <div class="th">Hiring Pipeline <i class="fas fa-info-circle ms-1" style="opacity:.4;font-size:10px;"></i></div>
                                                </div>
                                                <div id="clientJobsListajax"></div>
                                            </div>
                                        </div>
                                        <div id="paginationclientjobs" class="mt-3"></div>
                                    @endif
                                </div>
                                <!--end::All Req. Content-->
                            </div>
                            <!--end::All Req.-->
                            <!--end::Client Req.-->
                            <div class="tab-pane fade" id="client-requisitions" role="tabpanel">
                                <!--begin::Client Req. header-->
                                <div class="card-header cursor-pointer px-0">
                                    <!--begin::Card title-->
                                    <div class="card-title m-0">
                                        <h3 class="fw-bolder m-0">HCL Jobs</h3>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Client Req. header-->
                                <!--begin::Client Req. Content-->
                                <div class="card-body px-0 py-7">
                                    <!--begin:Table-->
                                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="fw-bolder text-muted">
                                                <th class="w-10px pe-0">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            data-kt-check="true"
                                                            data-kt-check-target="#kt_table_users .form-check-input2"
                                                            value="1" />
                                                    </div>
                                                </th>
                                                <th class="">Jobs</th>
                                                <th class="">Exp.</th>
                                                <th class="">Salary</th>
                                                <th class="">Location</th>
                                                <th class="">Status</th>
                                                <th class="">Date</th>
                                                <th class="">HM | CP</th>
                                                <th class="">S.Partners</th>
                                                <th class="">TOT</th>
                                                <th class="">SOU</th>
                                                <th class="">SCR</th>
                                                <th class="">EVA</th>
                                                <th class="">NEO</th>
                                                <th class="">OFF</th>
                                                <th class="">JOI</th>
                                                <th class="">REJ</th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">
                                            <!--begin::Table row-->
                                            <tr class="">
                                                <!--begin::Checkbox-->
                                                <td class="">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input form-check-input2"
                                                            type="checkbox" value="1" />
                                                    </div>
                                                </td>
                                                <!--end::Checkbox-->
                                                <!--begin::Requisitions=-->
                                                <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                                    <div
                                                        class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                                                        <a href="javascript:void(0);">
                                                            <div
                                                                class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                                                U</div>
                                                        </a>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::User details-->
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder text-hover-primary fs-6">UI/UX
                                                            Designer</a> <a href="javascript:void(0);"
                                                            class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a>
                                                    </div>
                                                    <!--begin::User details-->
                                                </td>
                                                <!--end::Requisitions-->
                                                <!--begin::Exp.-->
                                                <td class="">
                                                    <div class="d-flex flex-column">
                                                         <span
                                                            class="text-dark fw-bolder fs-6">22+</span> 
                                                        <span
                                                            class="text-muted fw-bold text-muted d-block fs-7">18+</span>
                                                    </div>
                                                </td>
                                                <!--end::Exp.-->
                                                <!--begin::Salary-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> 
                                                        <span
                                                            class="text-dark fw-bolder fs-6">50 LPA</span> 
                                                            <span
                                                            class="fw-bold d-block fs-7">30 LPA</span> 
                                                    </div>
                                                </td>
                                                <!--end::Salary-->
                                                <!--begin::Location-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> 
                                                        <span
                                                            class="text-dark fw-bolder fs-6">Gurgaon</span>
                                                        <div class="d-flex align-items-center"> <span
                                                                class="fw-bold text-muted d-block fs-7">Haryana</span>
                                                            &nbsp;|&nbsp; <span
                                                                class="fw-bold text-muted d-block fs-7">INDIA</span>
                                                            <div> </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!--end::Location-->
                                                <!--begin::Status | Requisition ID-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="fs-6 hold-bg text-hover-warning">Hold</a> <span
                                                            class="fw-bold text-muted d-block fs-7">R2</span> 
                                                    </div>
                                                </td>
                                                <!--end::Status | Requisition ID-->
                                                <!--begin::Date | Openings-->
                                                <td class="">
                                                    <div class="d-flex flex-column">
                                                         <span
                                                            class="text-dark fw-bolder fs-6">28-Apr-2021</span> <span
                                                            class="text-muted fw-bold d-block fs-7">774</span> 
                                                    </div>
                                                </td>
                                                <!--end::Date | Openings-->
                                                <!--begin::Hiring Manager | Contact Person-->
                                                <td class="">
                                                    <div class="d-flex flex-column">
                                                        <div class="symbol-group symbol-hover">
                                                            <div class="symbol symbol-30 symbol-circle"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Pawan"> <img alt="Pic"
                                                                    src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_4.jpg">
                                                            </div>
                                                            <div class="symbol symbol-30 symbol-circle symbol-light"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title=""
                                                                data-original-title="More users list.."> <span
                                                                    class="symbol-label font-weight-bold">1+</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>

                                                <!--end::Hiring Manager | Contact Person-->
                                                <!--begin::Staffing Partners-->
                                                <td class="">
                                                    <div class="d-flex flex-column">
                                                        <div class="symbol-group symbol-hover">
                                                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title="" data-original-title="Pradeep">
                                                                <img alt="Pic"
                                                                    src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg">
                                                            </div>
                                                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title="" data-original-title="Abinesh">
                                                                <img alt="Pic"
                                                                    src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_24.jpg">
                                                            </div>
                                                            <div class="symbol symbol-30 symbol-circle symbol-light"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                                data-original-title="More users list.."> <span
                                                                    class="symbol-label font-weight-bold">2+</span> </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!--end::Staffing Partners-->
                                                <!--begin::TOT-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">65</a> <a
                                                            href="javascript:void(0);"
                                                            class="text-muted fw-bold d-block fs-7 text-hover-primary">10</a>
                                                    </div>
                                                </td>
                                                <!--end::TOT-->
                                                <!--begin::SOU-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">0</a> 
                                                    </div>
                                                </td>
                                                <!--end::SOU-->
                                                <!--begin::SCR-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">23</a> 
                                                    </div>
                                                </td>
                                                <!--end::SCR-->
                                                <!--begin::EVA-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">0</a> 
                                                    </div>
                                                </td>
                                                <!--end::EVA-->
                                                <!--begin::NEO-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">24</a> 
                                                    </div>
                                                </td>
                                                <!--end::NEO-->
                                                <!--begin::OFF-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">83</a> 
                                                    </div>
                                                </td>
                                                <!--end::OFF-->
                                                <!--begin::JOI-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">49</a> 
                                                    </div>
                                                </td>
                                                <!--end::JOI-->
                                                <!--begin::REJ-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">0</a> 
                                                    </div>
                                                </td>
                                                <!--end::REJ-->
                                            </tr>
                                         <!--end::Table row-->
                                        </tbody>

                                     <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                             <!--end::Client Req. Content-->
                            </div>
                        <!--end::Client Req.-->
                        <!--begin::Custom Req.-->
                        <div class="tab-pane fade" id="custom-requisitions" role="tabpanel">
                            <!--begin::Custom Req. header-->
                            <div class="card-header cursor-pointer px-0">
                                <!--begin::Card title-->
                                <div class="card-title m-0">
                                    <h3 class="fw-bolder m-0">Custom Jobs</h3>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Actions-->
                                <div class="d-flex align-items-center py-1">
                                    <div class="card-toolbar">
                                        <!--begin::Toolbar-->
                                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                            <!--begin::Add Requisitions-->
                                            <a href="{{ url('service_company/jobs/create') }}">
                                                <button type="button"
                                                    class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
                                                    <!--begin::Svg Icon-->
                                                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                                <path
                                                                    d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                                    fill="#000000" fill-rule="nonzero"
                                                                    opacity="0.3" />
                                                                <path
                                                                    d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                                    fill="#000000" fill-rule="nonzero" />
                                                            </g>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    Add Jobs </button>
                                            </a>
                                            <!--end::Add Requisitions-->
                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Custom Req. header-->
                            <!--begin::Custom Req. Content-->
                            <div class="card-body px-0 py-7">
                                <!--begin:Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                    <!--begin::Table head-->
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-0">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        data-kt-check="true"
                                                        data-kt-check-target="#kt_table_users .form-check-input3"
                                                        value="1" />
                                                </div>
                                            </th>
                                            <th class="">Jobs</th>
                                            <th class="">Exp.</th>
                                            <th class="">Salary</th>
                                            <th class="">Location</th>
                                            <th class="">Status</th>
                                            <th class="">Date</th>
                                            <th class="">HM | CP</th>
                                            <th class="">S.Partners</th>
                                            <th class="">TOT</th>
                                            <th class="">SOU</th>
                                            <th class="">SCR</th>
                                            <th class="">EVA</th>
                                            <th class="">NEO</th>
                                            <th class="">OFF</th>
                                            <th class="">JOI</th>
                                            <th class="">REJ</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="text-gray-600 fw-bold">
                                        <!--begin::Table row-->
                                        <tr class="">
                                            <!--begin::Checkbox-->
                                            <td class="">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input form-check-input3" type="checkbox"
                                                        value="1" />
                                                </div>
                                            </td>
                                            <!--end::Checkbox-->
                                            <!--begin::Requisitions=-->
                                            <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                                <div
                                                    class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                                                    <a href="javascript:void(0);">
                                                        <div class="symbol-label border-radius-5"> <img
                                                                src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3"
                                                                alt="Company Logo" class="border-radius-5 w-100">
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <!--begin::User details-->
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder text-hover-primary fs-6">Frontend
                                                        Developer</a> <a href="javascript:void(0);"
                                                        class="fw-bold text-muted d-block fs-7 text-hover-primary">HCl
                                                        Technologies</a> </div>
                                                <!--begin::User details-->
                                            </td>
                                            <!--end::Requisitions-->
                                            <!--begin::Exp.-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">30+</span> <span
                                                        class="text-muted fw-bold text-muted d-block fs-7">27+</span>
                                                </div>
                                            </td>
                                            <!--end::Exp.-->
                                            <!--begin::Salary-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">70 LPA</span> <span
                                                        class="fw-bold d-block fs-7">67 LPA</span> </div>
                                            </td>
                                            <!--end::Salary-->
                                            <!--begin::Location-->
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">Noida</span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="fw-bold text-muted d-block fs-7">U.P</span>
                                                        &nbsp;|&nbsp; <span
                                                            class="fw-bold text-muted d-block fs-7">INDIA</span>
                                                        <div> </div>
                                            </td>
                                            <!--end::Location-->
                                            <!--begin::Status | Requisition ID-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="fs-6 closed-bg text-hover-danger">Closed</a> <span
                                                        class="fw-bold text-muted d-block fs-7">R3-Custom</span> </div>
                                            </td>
                                            <!--end::Status | Requisition ID-->
                                            <!--begin::Date | Openings-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">29-Aug-2021</span> <span
                                                        class="text-muted fw-bold d-block fs-7">33</span> </div>
                                            </td>
                                            <!--end::Date | Openings-->
                                            <!--begin::Hiring Manager | Contact Person-->
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <div class="symbol-group symbol-hover">
                                                        <div class="symbol symbol-30 symbol-circle"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Priyanka"> <img alt="Pic"
                                                                src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg">
                                                        </div>
                                                        <div class="symbol symbol-30 symbol-circle symbol-light"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="More users list..">
                                                            <span class="symbol-label font-weight-bold">1+</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!--end::Hiring Manager | Contact Person-->
                                            <!--begin::Staffing Partners-->
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <div class="symbol-group symbol-hover">
                                                        <div class="symbol symbol-30 symbol-circle"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="One"> <img
                                                                alt="Pic"
                                                                src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg">
                                                        </div>
                                                        <div class="symbol symbol-30 symbol-circle"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="Two"> <img
                                                                alt="Pic"
                                                                src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg">
                                                        </div>
                                                        <div class="symbol symbol-30 symbol-circle symbol-light"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="More users list..">
                                                            <span class="symbol-label font-weight-bold">2+</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!--end::Staffing Partners-->
                                            <!--begin::TOT-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">425</a> <a
                                                        href="javascript:void(0);"
                                                        class="text-muted fw-bold d-block fs-7 text-hover-primary">64</a>
                                                </div>
                                            </td>
                                            <!--end::TOT-->
                                            <!--begin::SOU-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">345</a>
                                                </div>
                                            </td>
                                            <!--end::SOU-->
                                            <!--begin::SCR-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                                                </div>
                                            </td>
                                            <!--end::SCR-->
                                            <!--begin::EVA-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">94</a>
                                                </div>
                                            </td>
                                            <!--end::EVA-->
                                            <!--begin::NEO-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">03</a>
                                                </div>
                                            </td>
                                            <!--end::NEO-->
                                            <!--begin::OFF-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                                                </div>
                                            </td>
                                            <!--end::OFF-->
                                            <!--begin::JOI-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">45</a>
                                                </div>
                                            </td>
                                            <!--end::JOI-->
                                            <!--begin::REJ-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">540</a>
                                                </div>
                                            </td>
                                            <!--end::REJ-->
                                        </tr>
                                        <!--end::Table row-->
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Custom Req. Content-->
                        </div>
                        <!--end::Custom Req.-->
                        <div class="tab-pane fade" id="jobs" role="tabpanel">Jobs - Content</div>
                        </div>
                    <!--end::Requisitions Content-->
                        </div></div>
                </div>
                <!--end::Requisitions Content-->
            </div>
        </div>
        <!--end::Body-->
    </div>
    <!--end:Tabs-->
</div>
<!--end::Content-->
</div>
<!--end::Body-->

<!--begin::Footer-->
<div class="card-footer py-5 text-center d-none" id="kt_activities_footer"> <a
        href="../../demo1/dist/pages/profile/activity.html" class="btn btn-bg-body text-primary">
        &nbsp;
        <span class="svg-icon svg-icon-3 svg-icon-primary"></span>
    </a> </div>
<!--end::Footer-->

</div>


<script>

    var myChartId = "<?php echo @$client->id; ?>";
    

            var showDataLabels = {
                          id: 'showDataLabels',
                          afterDatasetsDraw(chart) {
                              const { ctx } = chart;
                              chart.data.datasets.forEach((dataset, i) => {
                                  const meta = chart.getDatasetMeta(i);
                                  meta.data.forEach((bar, index) => {
                                      const value = dataset.data[index];
                                      ctx.save();
                                      ctx.fillStyle = 'black';
                                      ctx.font = 'bold 12px sans-serif';
                                      ctx.textAlign = 'center';
                                      ctx.fillText(value, bar.x, bar.y - 1.5);
                                      ctx.restore();
                                  });
                              });
                          }
            };

            var showDataLabelsvertical = {
                id: 'showDataLabelsvertical',
                afterDatasetsDraw(chart) {
                    var { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        var meta = chart.getDatasetMeta(i);
                        meta.data.forEach((bar, index) => {
                            var value = dataset.data[index];
                            ctx.save();
                            ctx.fillStyle = 'black';
                            ctx.font = 'bold 12px sans-serif';
                            ctx.textAlign = 'left';  // align left for horizontal bars
                            ctx.textBaseline = 'middle';

                            // place text at the end of bar (right side)
                            ctx.fillText(value, bar.x + 1, bar.y);
                            ctx.restore();
                        });
                    });
                }
            };


            function loadCountryChart() {

            var ctx = document.getElementById(`myChart_${myChartId}`).getContext('2d');
            

                var countryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['In Review','Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offer','Joined','Rejected'],
                    datasets: [{
                        label: 'Users',
                        data: ["{{$inreview_count}}", "{{$sourcing_count}}", "{{$screening_count}}", "{{$evaluation_count}}", "{{$negotiation_count}}", "{{$offer_count}}", "{{$joined_count}}","{{$Rejected_count}}"],
                        backgroundColor: [
                            '#bdbd26', // inreview_count
                            '#000000', // sourcing_count
                            '#c000ff', // screening_count
                            '#2896ff', // evaluation_count
                            '#ff4098', // negotiation_count
                            '#ffab00', // offer_count
                            '#06c732',
                            '#ff0202' // joined_count // joined_count
                        ],
                        borderRadius: 10,
                        barThickness: 30
                    }]
                },
                options: {
                    indexAxis: 'y', // makes it horizontal
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.x.toLocaleString()
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    if (value <= 10) return value;
                                    else if (value % 100 === 0) return value.toLocaleString();
                                    else return '';
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                },
                plugins: [showDataLabelsvertical] // ✅ custom labels plugin
            });
               }

                setTimeout(() => {
                  loadCountryChart();
             }, 300);  


             // Get the canvas element
        
            var ctx_job_div = document.getElementById(`allJobGrapg_${myChartId}`).getContext('2d');
              // Create the donut chart
            var allJobGrapg = new Chart(ctx_job_div, {
                type: 'doughnut',
                data: {
                    labels: ['Active Jobs', 'Hold Jobs', 'Closed Jobs'], // Labels for the chart
                    datasets: [{
                        label: 'Total',
                        data: ['{{ $active_count }}', '{{ $hold_count }}', '{{ $inactive_count }}'], // Values for the chart
                        backgroundColor: [
                            '#22c55e', // Green for 'Active Jobs'
                            '#ffab00', // Blue for 'Hold Jobs'
                            '#ff0202'  // Gray for 'Closed Jobs'
                        ],
                        hoverOffset: 4 // Highlight on hover
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide the legend in the chart area
                        }
                    },
                    cutout: '70%' // To create the donut hole
                }
            });


            
            var candidates_left_chart = document.getElementById(`candidates_left_chart_${myChartId}`).getContext('2d');

            var allJobGraphBar = new Chart(candidates_left_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValues) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });


            
            var candidates_offer_chart = document.getElementById(`candidates_offer_chart_${myChartId}`).getContext('2d');

            var allofferGraphBar = new Chart(candidates_offer_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsCommoffer) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValuesCommoffer) !!},
                        backgroundColor: [
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00' 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            
            var candidates_join_chart = document.getElementById(`candidates_join_chart_${myChartId}`).getContext('2d');

            var alljoinGraphBar = new Chart(candidates_join_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsCommjoin) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValuesCommjoin) !!},
                        backgroundColor: [
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732'  
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            var inetview_recent_chart_month = document.getElementById(`inetview_recent_chart_month_${myChartId}`).getContext('2d');

            var netview_recentGraphBarMonth = new Chart(inetview_recent_chart_month, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($interviewlabelsMonth) !!},
                    datasets: [{
                        label: 'Total Interviews',
                        data: {!! json_encode($interviewValuesMonth) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            var inetview_recent_chart = document.getElementById(`inetview_recent_chart_${myChartId}`).getContext('2d');

            var netview_recentGraphBar = new Chart(inetview_recent_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($interviewlabels) !!},
                    datasets: [{
                        label: 'Total Interviews',
                        data: {!! json_encode($interviewValues) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });


            function filtersdatagetmonth(valSelected){
                if(valSelected==1){
                      $(`.inetview_recent_chart_month_${myChartId}`).removeClass('d-none');
                      $(`.inetview_recent_chart_${myChartId}`).addClass('d-none');
                }else{
                      $(`.inetview_recent_chart_month_${myChartId}`).addClass('d-none');
                      $(`.inetview_recent_chart_${myChartId}`).removeClass('d-none');
                }

            }



            
    </script>


<script>
    function initContactsDataTable() {
        if ($.fn.DataTable.isDataTable('#contactList')) {
            return;
        }
        var table = $('#contactList').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            destroy: true,
            ordering: false, //disable ordering			
            "bLengthChange": false, //thought this line could hide the LengthMenu
            sDom: 'lrtip',
            oLanguage: {
                sLengthMenu: "_MENU_",
            },
            ajax: {
                'url': '{!! route('service_company.clients.contactdata') !!}?clientId=' + {{ @$client->id }},
                "data": function(d) {
                    d.search = $('#contactSearchInput').val() || $('#inputSearch').val()
                }
            },
            "drawCallback": function(settings) {
                $('[data-toggle="tooltip1"]').tooltip();
                $('[data-toggle="tooltip2"]').tooltip();
            },
            columns: [
                { data: 'chkbox', name: 'chkbox', searchable: false, orderable: false },
                { data: 'Contact', name: 'Contact', searchable: false, orderable: false },
                { data: 'Designation', name: 'Designation', searchable: false, orderable: false },
                { data: 'Email', name: 'Email', searchable: false, orderable: false },
                { data: 'Phone', name: 'Phone', searchable: false, orderable: false },
                { data: 'Status', name: 'Status', searchable: false, orderable: false },
                { data: 'Actions', name: 'Actions', searchable: false, orderable: false }
            ]
        });
        new $.fn.dataTable.FixedHeader(table);

        setTimeout(() => {
            $('.iti__flag-container').each(function(index) {
                var cnt = $(this).attr('data-row');
                const flagContainer = document.getElementById(`phoneDisplay_${cnt}`);
                if (!flagContainer) return;
                var calling_code = $(this).attr('data-code');
                
                const flagDiv = document.createElement("div");
                flagDiv.className = `iti__flag iti__${calling_code}`;
                const dialCode = countryDialCodes[calling_code] || "";
                const dialCodeSpan = document.createElement("span");
                dialCodeSpan.textContent = dialCode;

                flagContainer.appendChild(flagDiv);
                flagContainer.appendChild(dialCodeSpan);
            });
        }, 1000);
    }
</script>
<script>
    $(document).ready(function() {
        if (window.performance && performance.mark) {
            performance.mark('client_drawer_render_start');
            setTimeout(function() {
                performance.mark('client_drawer_render_end');
                performance.measure('client_drawer_render_time', 'client_drawer_render_start', 'client_drawer_render_end');
                var entries = performance.getEntriesByName('client_drawer_render_time');
                if (entries.length > 0) {
                    console.log('⚡ [APM Telemetry] Client Drawer Shell DOM Render Latency: ' + entries[0].duration.toFixed(2) + ' ms');
                }
            }, 100);
        }
        // Phase 9 Optimization: Lazy load tab data on tab activation & debounce search inputs
        var loadedTabs = {};

        if ($('#candidate_list').hasClass('active') || $('a[href="#candidate_list"]').hasClass('active')) {
            fetchCandidates();
            loadedTabs['candidate_list'] = true;
        } else if ($('#contacts').hasClass('active') || $('a[href="#contacts"]').hasClass('active')) {
            initContactsDataTable();
            loadedTabs['contacts'] = true;
        }

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"], a[data-bs-toggle="tab"]', function (e) {
            var target = $(e.target).attr('href');
            if (!target) return;
            target = target.replace('#', '');

            if (loadedTabs[target]) return;

            if (target === 'contacts') {
                initContactsDataTable();
                loadedTabs[target] = true;
            } else if (target === 'candidate_list') {
                fetchCandidates();
                loadedTabs[target] = true;
            } else if (target === 'requisitions') {
                fetchJobsClient();
                loadedTabs[target] = true;
            } else if (target === 'interview_tracker') {
                fetchCandidatesInverViewTracker();
                loadedTabs[target] = true;
            } else if (target === 'candidate_joining') {
                fetchCandidatesJoin();
                loadedTabs[target] = true;
            } else if (target === 'candidateDashboard') {
                fetchClientDashboardAnalytics();
                loadedTabs[target] = true;
            }
        });

        $(document).on('keyup', '#contactSearchInput', function() {
            if ($.fn.DataTable.isDataTable('#contactList')) {
                $('#contactList').DataTable().search($(this).val()).draw();
            }
        });

        var jobSearchTimer = null;
        $('#inputJobSearch').on('keyup', function() {
            clearTimeout(jobSearchTimer);
            jobSearchTimer = setTimeout(function() {
                fetchJobsClient();
            }, 300);
        });

        var searchTimer = null;
        $('#search').on('keyup', function() {
            clearTimeout(searchTimer);
            const search = $('#search').val();
            searchTimer = setTimeout(function() {
                fetchCandidates(1, search);
            }, 300);
        });

        $(document).off('click', '#paginationcandidate [data-page], #paginationcandidate .page-link, #paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow');
        $(document).on('click', '#paginationcandidate [data-page], #paginationcandidate .page-link, #paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            fetchCandidates(page, search);
        });

        $(document).off('click', '#paginationcandidatejoin [data-page], #paginationcandidatejoin .page-link, #paginationcandidatejoin .pagi-page, #paginationcandidatejoin .pagi-arrow');
        $(document).on('click', '#paginationcandidatejoin [data-page], #paginationcandidatejoin .page-link, #paginationcandidatejoin .pagi-page, #paginationcandidatejoin .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            fetchCandidatesJoin(page, search);
        });

        $(document).off('click', '#paginationcandidateinterviewtracker [data-page], #paginationcandidateinterviewtracker .page-link, #paginationcandidateinterviewtracker .pagi-page, #paginationcandidateinterviewtracker .pagi-arrow');
        $(document).on('click', '#paginationcandidateinterviewtracker [data-page], #paginationcandidateinterviewtracker .page-link, #paginationcandidateinterviewtracker .pagi-page, #paginationcandidateinterviewtracker .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            fetchCandidatesInverViewTracker(page, search);
        });

        $(document).off('click', '#paginationclientjobs [data-page], #paginationclientjobs .page-link, #paginationclientjobs .pagi-page, #paginationclientjobs .pagi-arrow');
        $(document).on('click', '#paginationclientjobs [data-page], #paginationclientjobs .page-link, #paginationclientjobs .pagi-page, #paginationclientjobs .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputJobSearch').val() || '';
            fetchJobsClient(page, search);
        });


    });

    function fetchCandidates(page = 1, search = '') {
        var clientid = '{{ $client->id }}';
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/client-candidate-list') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#candidatetList').html(response.html);
                $('#paginationcandidate').html(response.pagination);
            }
        });
    }

    function fetchCandidatesJoin(page = 1, search = '') {
        var clientid = '{{ $client->id }}';
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/client-candidate-join-list') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#candidatetListJoin').html(response.html);
                $('#paginationcandidatejoin').html(response.pagination);
            }
        });
    }

    function fetchCandidatesInverViewTracker(page = 1, search = '') {
        var clientid = '{{ $client->id }}';
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/client-candidate-interview-tracker') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#candidatetListinterviewtracker').html(response.html);
                $('#paginationcandidateinterviewtracker').html(response.pagination);
            }
        });
    }

    function fetchClientDashboardAnalytics() {
        var clientId = '{{ $client->id }}';
        return $.ajax({
            url: `{{ url('service_company/clients/dashboard-analytics') }}/${clientId}`,
            type: 'GET',
            success: function(response) {
                if (response && response.status === 'success' && response.data) {
                    console.log('⚡ [Client Dashboard APM] Analytics loaded lazily');
                }
            }
        });
    }

    // ── Follow-Up actions for the Interview Tracker (client show page) ──
    function citToggleFollowUp(btn) {
        var menu   = btn.nextElementSibling;
        var isOpen = menu.classList.contains('open');
        document.querySelectorAll('.cit-followup-menu.open').forEach(function (m) { m.classList.remove('open'); });
        if (!isOpen) { menu.classList.add('open'); }
    }

    function citSetFollowUp(el, id, status) {
        document.querySelectorAll('.cit-followup-menu.open').forEach(function (m) { m.classList.remove('open'); });
        var dropdown = el.closest('.cit-followup-dropdown');
        var trigger  = dropdown ? dropdown.querySelector('.cit-followup-trigger') : null;
        var labels   = { 'confirmed': 'Confirmed', 'no-response': 'No Response', 'rescheduled': 'Rescheduled' };
        var colors   = { 'confirmed': 'var(--cit-success)', 'no-response': 'var(--cit-danger)', 'rescheduled': 'var(--cit-primary)' };
        $.ajax({
            url  : '{{ route("service_company.interview-schedule.followup_update") }}',
            type : 'POST',
            data : { _token: '{{ csrf_token() }}', id: id, status: status },
            success: function (resp) {
                if (resp && resp.status === 'success') {
                    if (trigger) {
                        var lbl = trigger.querySelector('.cit-fu-label');
                        if (lbl) { lbl.textContent = labels[status] || 'Follow Up'; }
                        trigger.style.color       = colors[status] || '';
                        trigger.style.borderColor = colors[status] || '';
                        var ctaIcon = trigger.querySelector('.cit-cta-icon');
                        if (ctaIcon) { ctaIcon.remove(); }
                    }
                } else {
                    Swal.fire({ text: (resp && resp.msg) ? resp.msg : 'Could not update follow-up status.', icon: 'error', confirmButtonText: 'Ok' });
                }
            },
            error: function () {
                Swal.fire({ text: 'Could not update follow-up status.', icon: 'error', confirmButtonText: 'Ok' });
            }
        });
    }

    // Close any open cit follow-up menu on outside click
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.cit-followup-dropdown').length) {
            document.querySelectorAll('.cit-followup-menu.open').forEach(function (m) { m.classList.remove('open'); });
        }
    });

    function fetchJobsClient(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/jobs_side_bar_job_ajax/data') }}?page=${page}`,
            type: 'GET',
            data: {
                search: $('#inputJobSearch').val(),
                client_id: $('#hid_client_id').val(),
                custom:true
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#clientJobsListajax').html(response.html);
                $('#paginationclientjobs').html(response.pagination);
            }
        });
    }




    // var allreq = $('#allreq').dataTable({

    //     responsive: true,
    //     processing: true,
    //     serverSide: true,
    //     destroy: true,
    //     ordering: false, //disable ordering
    //     "bLengthChange": false, //thought this line could hide the LengthMenu
    //     sDom: 'lrtip',
    //     oLanguage: {
    //         sLengthMenu: "_MENU_",
    //     },
    //     "pageLength": 10,
    //     colReorder: true,
    //     ajax: {
    //         'url': '{!! route('service_company.jobs.data') !!}',
    //         "data": function(d) {
    //             d.search = $('#inputJobSearch').val(), d.client_id = $('#hid_client_id').val(), d.custom =
    //                 true
    //         }
    //     },
    //     "drawCallback": function(settings) {
    //         $('[data-toggle="tooltip1"]').tooltip();
    //         $('[data-toggle="tooltip2"]').tooltip();
    //         $('[data-toggle="tooltip3"]').tooltip();
    //         $('[data-toggle="tooltip4"]').tooltip();
    //     },

    //     "columnDefs": [{
    //             "width": "50px",
    //             "targets": 0
    //         },
    //         {
    //             "width": "100px",
    //             "targets": 1,
    //             "class": "d-flex align-items-center border-0"
    //         },
    //         {
    //             "width": "50px",
    //             "targets": 2
    //         },
    //         {
    //             "width": "50px",
    //             "targets": 3
    //         },
    //         {
    //             "width": "200px",
    //             "targets": 4
    //         },
    //         {
    //             "width": "50px",
    //             "targets": 5
    //         },
    //         {
    //             "width": "200px",
    //             "targets": 6
    //         },
    //         {
    //             "width": "150px",
    //             "targets": 7
    //         },
    //         {
    //             "width": "150px",
    //             "targets": 8
    //         },

    //         {
    //             "width": "20px",
    //             "targets": 9
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 10
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 11
    //         },

    //         {
    //             "width": "20px",
    //             "targets": 12
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 13
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 14
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 15
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 16
    //         }
    //     ],
    //     columns: [{
    //             data: 'chkbox',
    //             name: 'chkbox',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Requisition',
    //             name: 'Requisition',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Exp',
    //             name: 'Exp',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Salary',
    //             name: 'Salary',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Location',
    //             name: 'Location',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'Status',
    //             name: 'Status',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Date',
    //             name: 'Date',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'HM',
    //             name: 'HM',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'Partners',
    //             name: 'Partners',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'TOT',
    //             name: 'TOT',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'SOU',
    //             name: 'SOU',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'SCR',
    //             name: 'SCR',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'EVA',
    //             name: 'EVA',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'NEO',
    //             name: 'NEO',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'OFF',
    //             name: 'OFF',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'JOI',
    //             name: 'JOI',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'REJ',
    //             name: 'REJ',
    //             orderable: false,
    //             searchable: false
    //         }

    //     ]
    // });
    // new $.fn.dataTable.FixedHeader(allreq);


</script>
