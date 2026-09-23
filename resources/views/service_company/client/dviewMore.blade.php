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

    .table-custom-class tbody tr {
        height: 82px;
        /* Adjust the height as per your requirement */
        position: relative;
    }
    .iti__flag {
        margin-top: 2px !important;
      }
</style>


<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    
    <!--begin::Content-->
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        <!--beign::candidate detail-->
        {{-- ── Redesigned detail header (Agency-style): profile card + KPI cards ── --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
          .cd-hero .profile-card { background:#fff; border-radius:12px; border:1px solid #eff2f5; padding:24px; margin:0 8px 16px; }
          .cd-hero .profile-top { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; }
          .cd-hero .profile-left { display:flex; gap:18px; flex:1; min-width:0; }
          .cd-hero .logo-wrap { position:relative; flex-shrink:0; }
          .cd-hero .logo-circle { width:88px; height:88px; border-radius:50%; background:#f8f9fa; border:1px solid #e4e6ef; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:32px; font-weight:700; color:#5e6278; }
          .cd-hero .logo-circle img { width:100%; height:100%; object-fit:cover; }
          .cd-hero .verified-check { position:absolute; top:-2px; right:-2px; width:24px; height:24px; background:#50cd89; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2.5px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,.08); }
          .cd-hero .verified-check i { color:#fff; font-size:10px; }
          .cd-hero .profile-info { flex:1; min-width:0; }
          .cd-hero .profile-name-row { display:flex; align-items:center; gap:10px; margin-bottom:8px; flex-wrap:wrap; }
          .cd-hero .profile-name { font-size:20px; font-weight:700; color:#181c32; }
          .cd-hero .verified-badge { display:inline-flex; align-items:center; gap:4px; background:#e8fff3; color:#47be7d; font-size:11px; font-weight:600; padding:4px 10px; border-radius:50px; }
          .cd-hero .verified-badge i { font-size:9px; }
          .cd-hero .profile-meta { display:flex; align-items:center; gap:4px 16px; flex-wrap:wrap; font-size:13px; color:#a1a5b7; margin-bottom:12px; }
          .cd-hero .meta-item { display:flex; align-items:center; gap:6px; }
          .cd-hero .meta-item i { font-size:11px; color:#b5b5c3; }
          .cd-hero .meta-divider { color:#e4e6ef; font-size:10px; }
          .cd-hero .meta-link { color:#009ef7; text-decoration:none; display:flex; align-items:center; gap:4px; }
          .cd-hero .meta-link:hover { text-decoration:underline; }
          .cd-hero .meta-link i.fa-external-link-alt { font-size:9px; }
          .cd-hero .service-tags { display:flex; flex-wrap:wrap; gap:8px; }
          .cd-hero .tag { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; background:#f5f8fa; border:1px solid #e4e6ef; border-radius:6px; font-size:12px; font-weight:500; color:#5e6278; }
          .cd-hero .profile-right { display:flex; flex-direction:column; align-items:flex-end; gap:12px; flex-shrink:0; }
          .cd-hero .status-badge { display:inline-flex; align-items:center; gap:6px; background:#e8fff3; color:#47be7d; font-size:13px; font-weight:600; padding:6px 14px; border-radius:6px; border:1px solid rgba(80,205,137,.15); }
          .cd-hero .status-dot { width:8px; height:8px; background:#50cd89; border-radius:50%; position:relative; }
          .cd-hero .status-dot::after { content:""; position:absolute; inset:-3px; border-radius:50%; border:1px solid #50cd89; animation:cdpulse 2s infinite; }
          @keyframes cdpulse { 0%,100% { transform:scale(1); opacity:1; } 50% { transform:scale(1.3); opacity:0; } }
          .cd-hero .kpi-row { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; margin:0 8px 16px; }
          .cd-hero .kpi-box { background:#fff; border-radius:12px; border:1px solid #eff2f5; padding:13px; transition:all .2s; }
          .cd-hero .kpi-box:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.04); }
          .cd-hero .kpi-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
          .cd-hero .kpi-icon-box { width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
          .cd-hero .kpi-icon-blue { background:#f1faff; color:#009ef7; }
          .cd-hero .kpi-icon-green { background:#e8fff3; color:#50cd89; }
          .cd-hero .kpi-icon-purple { background:#f8f5ff; color:#7239ea; }
          .cd-hero .kpi-icon-orange { background:#fff8dd; color:#f6a723; }
          .cd-hero .kpi-icon-teal { background:#f0fdfa; color:#14b8a6; }
          .cd-hero .kpi-label { font-size:12px; font-weight:600; color:#7e8299; line-height:1.3; }
          .cd-hero .kpi-number { font-size:28px; font-weight:700; color:#181c32; line-height:1; }
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
          .jobx .col-exp { display:flex; flex-direction:column; gap:5px; padding-top:2px; }
          .jobx .exp-row, .jobx .sal-row { display:flex; align-items:center; gap:5px; font-size:11.5px; font-weight:500; color:#181c32; white-space:nowrap; }
          .jobx .exp-row svg, .jobx .sal-row svg { width:13px; height:13px; color:#a1a5b7; flex-shrink:0; }
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
          .jobx .col-hm { font-size:11.5px; padding-top:2px; }
          .jobx .hm-name  { font-size:12px; font-weight:600; color:#181c32; margin-bottom:2px; }
          .jobx .hm-title { font-size:11px; color:#6d7284; margin-bottom:1px; }
          .jobx .hm-company { font-size:11px; color:#a1a5b7; }
          .jobx .col-action { display:flex; flex-direction:column; gap:4px; padding-top:2px; }
          .jobx .action-more { font-size:10.5px; color:#3b55e6; cursor:pointer; font-weight:500; }
          .jobx .jobx-add-link { font-size:11px; color:#5e6278; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; padding:4px 9px; border:1.5px dashed #d1d3e0; border-radius:7px; background:#fafbfc; transition:all .15s; }
          .jobx .jobx-add-link:hover { border-color:#3b55e6; color:#3b55e6; background:#f0f3ff; }
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
          .jobx .job-row-skills { display:flex; align-items:center; flex-wrap:wrap; gap:5px; padding:3px 12px 10px 83px; border-bottom:1px solid #e4e6ef; font-size:10.5px; }
          .jobx .job-row-skills .sk-label { font-weight:600; color:#6d7284; flex-shrink:0; }
          .jobx .job-row-skills .sk-tag { display:inline-flex; padding:1px 7px; border-radius:4px; background:#f1f3fa; color:#5e6278; font-size:9.5px; font-weight:600; }
          .jobx .job-row:last-of-type + .job-row-skills { border-bottom:none; }
          @media (max-width:1366px){ .jobx .table-header, .jobx .job-row { grid-template-columns:2.2fr .95fr 1fr 1.1fr .85fr .85fr 2.1fr; } }
          @media (max-width:1024px){ .jobx .table-card { overflow-x:auto; } .jobx .table-header, .jobx .job-row { min-width:980px; } }
        </style>
        @php
            $cur_company_id = auth()->user()->company_id;
            $detail_from = @$directClientDetail->from_company_id ?: (@$directClientDetail->companies_id ?: (@$directClientDetail->company_id ?: @$directClientDetail->client_id));
            $dc_id = $detail_from;
            $kpiCacheKey = "ats:direct_client_kpi_v3:{$cur_company_id}:{$detail_from}";
            $kpiData = cache()->remember($kpiCacheKey, 900, function() use ($detail_from, $cur_company_id) {
                $kpiJobs = GeneralMethod::DirectClientJobsCountAllStatus($detail_from, $cur_company_id);
                $kpiPerf = GeneralMethod::DirectClientCandidatesCountList($detail_from, $cur_company_id);
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
            if (!empty(@$directClientDetail->logo_logical_path) && strlen(@$directClientDetail->logo_logical_path) > 10) $detailLogo = @$directClientDetail->logo_logical_path;
            if (!empty(@$directClientDetail->logo) && strlen(@$directClientDetail->logo) > 10) $detailLogo = @$directClientDetail->logo;
            $detailName = ucwords(@$directClientDetail->company_name);
            $detailInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', trim($detailName)), 0, 2))));

            $industries_name = '';
            if (trim((string) @$directClientDetail->industries) != '') { $industries_name = $industries[@$directClientDetail->industries] ?? ''; }
            $tagsArr = array_filter(array_map('trim', explode(',', (string) @$directClientDetail->tags)));
        @endphp
        <div class="cd-hero">
          <input type="hidden" name="direct_client_id" id="direct_client_id" value="{{ $directClientDetail->client_id }}" />
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
                    @if (!empty(@$directClientDetail->website))
                      <span class="meta-divider">|</span>
                      <a href="{{ @$directClientDetail->website }}" target="_blank" class="meta-link"><i class="fas fa-globe"></i>{{ @$directClientDetail->website }}<i class="fas fa-external-link-alt"></i></a>
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
              </div>
            </div>
          </div>
          <div class="kpi-row">
            <div class="kpi-box">
              <div class="kpi-header"><div class="kpi-icon-box kpi-icon-blue"><i class="fas fa-file-alt"></i></div><span class="kpi-label">Active Jobs</span></div>
              <div class="kpi-number">{{ $kpiActive }}</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header"><div class="kpi-icon-box kpi-icon-green"><i class="fas fa-id-badge"></i></div><span class="kpi-label">Candidates Submitted</span></div>
              <div class="kpi-number">{{ $kpiSubmitted }}</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header"><div class="kpi-icon-box kpi-icon-purple"><i class="fas fa-people-arrows"></i></div><span class="kpi-label">Interviews Scheduled</span></div>
              <div class="kpi-number" id="kpiInterviewsScheduledCount">{{ $kpiInterviews }}</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header"><div class="kpi-icon-box kpi-icon-orange"><i class="fas fa-gift"></i></div><span class="kpi-label">Offers Generated</span></div>
              <div class="kpi-number">{{ $kpiOffers }}</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header"><div class="kpi-icon-box kpi-icon-teal"><i class="fas fa-calendar-check"></i></div><span class="kpi-label">Successful Joins</span></div>
              <div class="kpi-number">{{ $kpiJoins }}</div>
            </div>
          </div>
        </div>
        <!--end::candidate details-->
        {{-- ── Redesigned tab bar + Overview (Agency-style); scoped to .cd-maintabs / cd-* classes ── --}}
        <style>
          .cd-maintabs { border-bottom:none !important; flex-wrap:wrap; }
          .cd-maintabs .nav-item { margin-right:0 !important; }
          .cd-maintabs .nav-link { padding:14px 20px; border:none !important; position:relative; background:none !important; }
          .cd-maintabs .nav-link .nav-text { font-size:13px; font-weight:500 !important; color:#a1a5b7; transition:color .2s; }
          .cd-maintabs .nav-link:hover .nav-text { color:#009ef7; }
          .cd-maintabs .nav-link.active .nav-text { color:#009ef7; font-weight:600 !important; }
          .cd-maintabs .nav-link.active::after { content:""; position:absolute; bottom:0; left:20px; right:20px; height:2.5px; background:#009ef7; border-radius:2px 2px 0 0; }
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
          .cd-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:1px solid #e4e6ef; background:#fff; color:#5e6278; }
          .cd-btn-primary { background:#009ef7; color:#fff; border-color:#009ef7; }
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
                                    <span class="cd-tab-badge" id="tabBadgeInterviewTracker" @if($kpiInterviews <= 0) style="display:none;" @endif>{{ $kpiInterviews }}</span>
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
                    <div class="tab-pane active" id="overview" role="tabpanel">
                        <div class="cd-content-grid py-5">
                            <div class="cd-card">
                                <div class="cd-card-head">
                                    <div class="cd-card-ic"><i class="fas fa-info-circle"></i></div>
                                    <h3 class="cd-card-title">About the Client</h3>
                                </div>
                                <div class="cd-about">
                                    @php
                                        $description = @$directClientDetail->description
                                            ? @$directClientDetail->description
                                            : @$directClientDetail->companies_description;
                                    @endphp
                                    {!! $description !!}
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
                                    @if (!empty(@$directClientDetail->website))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Website</span>
                                        <span class="cd-info-value"><a href="{{ @$directClientDetail->website }}" target="_blank">{{ @$directClientDetail->website }} <i class="fas fa-external-link-alt"></i></a></span>
                                    </div>
                                    @endif
                                    @if (!empty(@$directClientDetail->email))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Email</span>
                                        <span class="cd-info-value"><a href="mailto:{{ @$directClientDetail->email }}">{{ @$directClientDetail->email }}</a></span>
                                    </div>
                                    @endif
                                    @if ($industries_name)
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Industry</span>
                                        <span class="cd-info-value">{{ $industries_name }}</span>
                                    </div>
                                    @endif
                                    @if (@$directClientDetail->industries_type > 0)
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Company Type</span>
                                        <span class="cd-info-value">{{ $companyType[@$directClientDetail->industries_type] ?? '' }}</span>
                                    </div>
                                    @endif
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Vendor ID</span>
                                        <span class="cd-info-value">VEN-{{ @$directClientDetail->id }}</span>
                                    </div>
                                    @if (@$directClientDetail->founded)
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Founded</span>
                                        <span class="cd-info-value">{{ @$directClientDetail->founded }}</span>
                                    </div>
                                    @endif
                                    @if (!empty(@$directClientDetail->company_sizes))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Number of Employees</span>
                                        <span class="cd-info-value">{{ is_numeric(@$directClientDetail->company_sizes) ? number_format((float)@$directClientDetail->company_sizes) . '+' : @$directClientDetail->company_sizes }}</span>
                                    </div>
                                    @endif
                                    @if (!empty(@$directClientDetail->to_created_at))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Connected On</span>
                                        <span class="cd-info-value">{{ date('d M, Y', strtotime(@$directClientDetail->to_created_at)) }}</span>
                                    </div>
                                    @endif
                                    @php $tagsList = array_filter(array_map('trim', explode(',', (string) @$directClientDetail->tags))); @endphp
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
                                    <div class="cd-search"><i class="fas fa-search"></i><input type="text" id="dcontactSearchInput" placeholder="Search contacts..." /></div>
                                </div>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="data-table" id="dcontactList">
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
                                      <th class="min-w-15px">Notice Period</th>
                                      <th class="min-w-15px">Location</th>
                                      <th class="min-w-15px"> Source</th>
                                      <th class="min-w-15px">Upload Date</th>
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

                                      <th class="min-w-150px">Name</th>
                                      <th class="min-w-15px">Job</th>
                                      <th class="min-w-15px">Interview Mode</th>
                                      <th class="min-w-15px">Interviewers</th>
                                      <th class="min-w-15px">Source</th>
                                      <th class="min-w-15px">Interview Date</th>
                                      <th class="min-w-15px">Feedback</th>
                                      <th class="min-w-15px">Rating</th>
                                      <th class="min-w-15px">Stage</th>
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
                                      <th class="min-w-15px">Notice Period</th>
                                      <th class="min-w-15px">Location</th>
                                      <th class="min-w-15px"> Source</th>
                                      <th class="min-w-15px">Upload Date</th>
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
                        <div class="cd-tabcard">
                            <div class="cd-tabcard-head d-flex align-items-center justify-content-between pb-3 mb-4" style="border-bottom: 1px solid #edf2f7;">
                                <div>
                                    <div class="cd-tabcard-title fs-4 fw-bold text-gray-900 d-flex align-items-center gap-2">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="3" y="3" width="7" height="7" rx="1.5" fill="#3B82F6"/>
                                            <rect x="14" y="3" width="7" height="7" rx="1.5" fill="#10B981"/>
                                            <rect x="14" y="14" width="7" height="7" rx="1.5" fill="#F59E0B"/>
                                            <rect x="3" y="14" width="7" height="7" rx="1.5" fill="#6366F1"/>
                                        </svg>
                                        Client Dashboard
                                    </div>
                                    <div class="cd-tabcard-sub text-gray-500 fs-7 fw-medium mt-1">Real-time hiring & recruitment performance analytics for this client.</div>
                                </div>
                                <span class="badge badge-light-primary fw-semibold fs-8 px-3 py-2">Analytics Overview</span>
                            </div>

                            <div class="cd-tabwrap">
                                <div class="card mb-3 shadow-none border-0 rounded-3">
                                    <div class="card-body p-0">

                                        <!-- Row 1: Jobs & 7-Day Sourcing Cards -->
                                        <div class="row g-4 mb-4">
                                            <!-- Card 1: Jobs Breakdown -->
                                            <div class="col-md-6 col-lg-6">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    @php
                                                    $active_count = $counts->get('active', 0);
                                                    $hold_count = $counts->get('hold', 0);
                                                    $inactive_count = $counts->get('inactive', 0);
                                                    $alljobs_count = $active_count + $hold_count + $inactive_count;
                                                    @endphp
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcAllJobsCount_{{ $dc_id }}">{{ $inactive_count+$active_count+$hold_count }}</span>
                                                                <span class="badge badge-light-success fs-8 fw-bold">Jobs Overview</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">All Client Jobs</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-1 pb-4 px-5 d-flex align-items-center justify-content-between">
                                                        <div class="d-flex flex-center" style="min-width: 140px; min-height: 140px;">
                                                            <div id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 140px; min-height: 140px" data-kt-size="140" data-kt-line="11">
                                                                <canvas id="allJobGrapg_{{ $dc_id }}" height="70" width="70"></canvas>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column gap-2 w-100 ps-4">
                                                            <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background: #f8fafc;">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="w-8px h-8px rounded-circle me-2" style="background-color: #22c55e"></span>
                                                                    <span class="text-gray-700 fs-7 fw-semibold">Active Jobs</span>
                                                                </div>
                                                                <span class="fw-bold text-gray-900 fs-7" id="dcActiveJobsCount_{{ $dc_id }}">{{ $active_count }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background: #f8fafc;">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="w-8px h-8px rounded-circle me-2" style="background-color: #ffab00"></span>
                                                                    <span class="text-gray-700 fs-7 fw-semibold">Hold Jobs</span>
                                                                </div>
                                                                <span class="fw-bold text-gray-900 fs-7" id="dcHoldJobsCount_{{ $dc_id }}">{{ $hold_count }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background: #f8fafc;">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="w-8px h-8px rounded-circle me-2" style="background-color: #ff0202"></span>
                                                                    <span class="text-gray-700 fs-7 fw-semibold">Closed Jobs</span>
                                                                </div>
                                                                <span class="fw-bold text-gray-900 fs-7" id="dcClosedJobsCount_{{ $dc_id }}">{{ $inactive_count }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Card 2: Added in last 7 days -->
                                            <div class="col-md-6 col-lg-6">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcAdded7DaysCount_{{ $dc_id }}">{{$candidateValuesTotal}}</span>
                                                                <span class="badge badge-light-info fs-8 fw-bold">7-Day Activity</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">Candidates Sourced (Last 7 Days)</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-0 px-4 pb-3 d-flex align-items-end">
                                                        <div id="kt_card_widget_6_chart_{{ $dc_id }}" class="w-100" style="height: 150px">
                                                            <div id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 100%; min-height: 150px">
                                                                <canvas id="candidates_left_chart_{{ $dc_id }}" height="150"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Row 2: 6-Month Sourcing Trend Hero Card -->
                                        <div class="row g-4 mb-4">
                                            <div class="col-12">
                                                <div class="card card-flush shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcTotalCandidatesCount_{{ $dc_id }}">{{$total_candidates}}</span>
                                                                <span class="badge badge-light-primary fs-8 fw-bold">6-Month Trend</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">Candidates Sourced ({{ date('d M Y',strtotime($startOfMonth)) }} - {{ date('d M Y',strtotime($endOfMonth)) }})</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-2 px-4 pb-3">
                                                        <div id="kt_charts_widget_3" class="w-100" style="height: 320px; min-height: 320px;">
                                                            <canvas id="myChart_{{ $dc_id }}" height="320" style="height: 320px; min-height: 320px;"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Row 3: Offers & Joinings Trend Cards -->
                                        <div class="row g-4 mb-4">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcTotalOffersCount_{{ $dc_id }}">{{$candidateValuesTotaloffer}}</span>
                                                                <span class="badge badge-light-warning fs-8 fw-bold">Offers</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">Offers Generated ({{ date("M Y", strtotime($startOfMonthcomman)) }} - {{ date("M Y", strtotime($endOfMonthcomman)) }})</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-0 px-4 pb-3">
                                                        <div id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 100%; min-height: 150px">
                                                            <canvas id="candidates_offer_chart_{{ $dc_id }}" height="150"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-6">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcTotalJoinedCount_{{ $dc_id }}">{{$candidateValuesTotaljoin}}</span>
                                                                <span class="badge badge-light-success fs-8 fw-bold">Joins</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">Candidates Joined ({{ date("M Y", strtotime($startOfMonthcomman)) }} - {{ date("M Y", strtotime($endOfMonthcomman)) }})</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-0 px-4 pb-3">
                                                        <div id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 100%; min-height: 150px">
                                                            <canvas id="candidates_join_chart_{{ $dc_id }}" height="150"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Row 4: Interview Section -->
                                        <div class="row g-4">
                                            <!-- Upcoming Interviews -->
                                            <div class="col-lg-5 col-xl-4">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <h3 class="card-title fw-bold text-gray-900 fs-6 d-flex align-items-center gap-2">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                            Upcoming Interviews
                                                        </h3>
                                                    </div>
                                                    <div class="card-body pt-1 px-5 pb-4">
                                                        @if (count($upcomingData) > 0)
                                                            @foreach ($upcomingData as $rowArr)
                                                                @php
                                                                $row = (object) $rowArr;
                                                                $schedule_date = Carbon::parse($row->schedule_date)->format('M d');
                                                                $schedule_date_explode = explode(" ",$schedule_date);
                                                                @endphp
                                                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                                                                    <div class="d-flex align-items-center me-3">
                                                                        <div class="rounded-3 text-center px-2 py-1 me-3" style="background: #eef2ff; border: 1px solid #e0e7ff; min-width: 44px;">
                                                                            <span style="font-size: 10px; font-weight: 700; color: #6366f1; text-transform: uppercase; display: block;">{{$schedule_date_explode[0]}}</span>
                                                                            <span style="color: #4f46e5; font-size: 15px; font-weight: 900; line-height: 1;">{{$schedule_date_explode[1]}}</span>
                                                                        </div>
                                                                        <div>
                                                                            <div class="text-gray-900 fs-7 fw-bold mb-0 text-truncate" style="max-width: 140px;">{{ucwords(mb_substr(@$row->full_name, 0, 20))}}</div>
                                                                            <span class="text-gray-500 fs-8 d-block text-truncate" style="max-width: 140px;">{{ucwords(mb_substr(@$row->current_designation, 0, 21))}}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <span class="badge badge-light-primary fs-8 fw-semibold px-2 py-1 mb-1 d-inline-block">{{$row->schedule_to_time. ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '')}}</span>
                                                                        <span class="text-gray-500 fs-8 d-block">{{ucwords(mb_substr($row->mode_name, 0, 13))}}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="text-center py-6 px-3 rounded-3" style="background: #f8fafc; border: 1px dashed #e2e8f0;">
                                                                <svg class="mb-2" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                <div class="text-gray-500 fs-7 fw-medium">No upcoming interviews scheduled</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Candidates Interviews Chart -->
                                            <div class="col-lg-7 col-xl-8">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                                                        <h3 class="card-title fw-bold text-gray-900 fs-6 d-flex align-items-center gap-2">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                                                            Candidates Interviews
                                                        </h3>
                                                        <select class="form-select form-select-sm form-select-solid w-140px fs-7 border-0" id="sortingfilter" onchange="filtersdatagetmonth(this.value)">
                                                            <option value="1">Last 6 Months</option>
                                                            <option value="0">Last 7 Days</option>
                                                        </select>
                                                    </div>
                                                    <div class="card-body pt-1 px-4 pb-3">
                                                        <div id="kt_card_widget_6_chart_{{ $dc_id }}" class="w-100" style="height: 200px">
                                                            <div class="inetview_recent_chart_{{ $dc_id }} d-none" id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 100%; min-height: 200px">
                                                                <canvas id="inetview_recent_chart_{{ $dc_id }}" height="200"></canvas>
                                                            </div>
                                                            <div class="inetview_recent_chart_month_{{ $dc_id }}" id="kt_card_widget_4_chart_month_{{ $dc_id }}" style="min-width: 100%; min-height: 200px">
                                                                <canvas id="inetview_recent_chart_month_{{ $dc_id }}" height="200"></canvas>
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
                    </div>
                <!--end begin::candidateDashboard Content-->
                

                    <div class="tab-pane" id="requisitions" role="tabpanel">
                        <div class="cd-tabcard"><div class="cd-tabcard-head"><div class="cd-tabcard-title">Jobs</div><div class="cd-tabcard-sub">Roles open with this client.</div></div><div class="cd-tabwrap">
                        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 border-bottom-0 d-none">
                            <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab"
                                    href="#all-requisitions">&nbsp;</a> </li>
                            {{-- <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#client-requisitions">HCL Req.</a> </li>
                          <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#custom-requisitions">Custom Req.</a> </li>
                          <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#jobs">Jobs</a> </li> --}}
                        </ul>
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
                                            <input type="text" id="inputSearch" data-kt-user-table-filter="search"
                                                class="form-control form-control-solid w-250px ps-14"
                                                style="padding: .6rem 1rem;" placeholder="Search..." />
                                        </div>
                                        <!--end::Search-->
                                        <div class="card-toolbar">
                                            <!--begin::Toolbar-->
                                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                                <!--begin::Filter-->
                                                <button type="button"
                                                    class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                                    data-kt-menu-flip="top-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                                    <span class="svg-icon svg-icon-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none">
                                                            <path
                                                                d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                                                fill="black" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    Filter</button>
                                                <!--begin::Menu 1-->
                                                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px"
                                                    data-kt-menu="true">
                                                    <!--begin::Header-->
                                                    <div class="px-7 py-5">
                                                        <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <!--begin::Separator-->
                                                    <div class="separator border-gray-200"></div>
                                                    <!--end::Separator-->
                                                    <!--begin::Content-->
                                                    <div class="px-7 py-5" data-kt-user-table-filter="form">
                                                        <!--begin::Input group-->
                                                        <div class="mb-10">
                                                            <label class="form-label fs-6 fw-bold">Role:</label>
                                                            <select class="form-select form-select-solid fw-bolder"
                                                                data-kt-select2="true"
                                                                data-placeholder="Select option"
                                                                data-allow-clear="true"
                                                                data-kt-user-table-filter="role"
                                                                data-hide-search="true">
                                                                <option></option>
                                                                <option value="Administrator">Administrator</option>
                                                                <option value="Analyst">Analyst</option>
                                                                <option value="Developer">Developer</option>
                                                                <option value="Support">Support</option>
                                                                <option value="Trial">Trial</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Input group-->
                                                        <div class="mb-10">
                                                            <label class="form-label fs-6 fw-bold">Two Step
                                                                Verification:</label>
                                                            <select class="form-select form-select-solid fw-bolder"
                                                                data-kt-select2="true"
                                                                data-placeholder="Select option"
                                                                data-allow-clear="true"
                                                                data-kt-user-table-filter="two-step"
                                                                data-hide-search="true">
                                                                <option></option>
                                                                <option value="Enabled">Enabled</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Actions-->
                                                        <div class="d-flex justify-content-end">
                                                            <button type="reset"
                                                                class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                                                data-kt-menu-dismiss="true"
                                                                data-kt-user-table-filter="reset">Reset</button>
                                                            <button type="submit"
                                                                class="btn btn-primary fw-bold px-6"
                                                                data-kt-menu-dismiss="true"
                                                                data-kt-user-table-filter="filter">Apply</button>
                                                        </div>
                                                        <!--end::Actions-->
                                                    </div>
                                                    <!--end::Content-->
                                                </div>
                                                <!--end::Menu 1-->
                                                <!--end::Filter-->
                                                @if (in_array('add_jobs', $userPermissions))
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
                                                <div id="directclientJobsListajax"></div>
                                            </div>
                                        </div>
                                        <div id="directpaginationclientjobs" class="mt-3"></div>
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


                                            <!--end::Table row-->
                                        </tbody>

                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Client Req. Content-->
                            </div>
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
        href="../../demo1/dist/pages/profile/activity.html" class="btn btn-bg-body text-primary">View All Activities
        <!--begin::Svg Icon | path: icons/duotone/Navigation/Right-2.svg-->
        <span class="svg-icon svg-icon-3 svg-icon-primary">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24" />
                    <rect fill="#000000" opacity="0.5"
                        transform="translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000)"
                        x="7.5" y="7.5" width="2" height="9" rx="1" />
                    <path
                        d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z"
                        fill="#000000" fill-rule="nonzero"
                        transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                </g>
            </svg>
        </span>
        <!--end::Svg Icon-->
    </a> </div>
<!--end::Footer-->
</div>

<script>
           var myChartId = "{{ $dc_id }}";
    window.dcChartInstances = window.dcChartInstances || {};

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
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(value, bar.x + 1, bar.y);
                    ctx.restore();
                });
            });
        }
    };

    function renderDirectClientDashboardCharts(d, clientId) {
        if (!d) return;

        var activeCount = d.counts ? (d.counts.active || 0) : 0;
        var holdCount = d.counts ? (d.counts.hold || 0) : 0;
        var inactiveCount = d.counts ? (d.counts.inactive || 0) : 0;

        function createOrUpdateChart(canvasId, config) {
            var canvas = document.getElementById(canvasId);
            if (!canvas) return;
            if (window.dcChartInstances[canvasId]) {
                window.dcChartInstances[canvasId].destroy();
            }
            var ctx = canvas.getContext('2d');
            window.dcChartInstances[canvasId] = new Chart(ctx, config);
        }

        createOrUpdateChart(`allJobGrapg_${clientId}`, {
            type: 'doughnut',
            data: {
                labels: ['Active Jobs', 'Hold Jobs', 'Closed Jobs'],
                datasets: [{
                    label: 'Total',
                    data: [activeCount, holdCount, inactiveCount],
                    backgroundColor: ['#22c55e', '#ffab00', '#ff0202'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });

        createOrUpdateChart(`candidates_left_chart_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.labels || [],
                datasets: [{
                    label: 'Total Candidates',
                    data: d.candidateValues || [],
                    backgroundColor: Array((d.labels || []).length).fill('#2196f3'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });

        createOrUpdateChart(`myChart_${clientId}`, {
            type: 'bar',
            data: {
                labels: ['In Review', 'Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offer', 'Joined', 'Rejected'],
                datasets: [{
                    label: 'Users',
                    data: [
                        d.inreview_count || 0,
                        d.sourcing_count || 0,
                        d.screening_count || 0,
                        d.evaluation_count || 0,
                        d.negotiation_count || 0,
                        d.offer_count || 0,
                        d.joined_count || 0,
                        d.Rejected_count || 0
                    ],
                    backgroundColor: [
                        '#bdbd26', '#000000', '#c000ff', '#2896ff',
                        '#ff4098', '#ffab00', '#06c732', '#ff0202'
                    ],
                    borderRadius: 10,
                    barThickness: 20,
                    maxBarThickness: 22
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ctx.parsed.x.toLocaleString() } }
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
                    y: { ticks: { font: { weight: 'bold' } } }
                }
            },
            plugins: [showDataLabelsvertical]
        });

        createOrUpdateChart(`candidates_offer_chart_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.labelsCommoffer || [],
                datasets: [{
                    label: 'Total Candidates',
                    data: d.candidateValuesCommoffer || [],
                    backgroundColor: Array((d.labelsCommoffer || []).length).fill('#ffab00'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });

        createOrUpdateChart(`candidates_join_chart_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.labelsCommjoin || [],
                datasets: [{
                    label: 'Total Candidates',
                    data: d.candidateValuesCommjoin || [],
                    backgroundColor: Array((d.labelsCommjoin || []).length).fill('#06c732'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });

        createOrUpdateChart(`inetview_recent_chart_month_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.interviewlabelsMonth || [],
                datasets: [{
                    label: 'Total Interviews',
                    data: d.interviewValuesMonth || [],
                    backgroundColor: Array((d.interviewlabelsMonth || []).length).fill('#2196f3'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });

        createOrUpdateChart(`inetview_recent_chart_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.interviewlabels || [],
                datasets: [{
                    label: 'Total Interviews',
                    data: d.interviewValues || [],
                    backgroundColor: Array((d.interviewlabels || []).length).fill('#2196f3'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });
    }

    function filtersdatagetmonth(valSelected){
        if(valSelected == 1){
            $(`.inetview_recent_chart_month_${myChartId}`).removeClass('d-none');
            $(`.inetview_recent_chart_${myChartId}`).addClass('d-none');
        } else {
            $(`.inetview_recent_chart_month_${myChartId}`).addClass('d-none');
            $(`.inetview_recent_chart_${myChartId}`).removeClass('d-none');
        }
    }

            
    </script>

    
<script>


   // alert("hi");
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
            'url': '{!! route('service_company.clients.contactdata') !!}?company=' + {{ @$client->id }},
            "data": function(d) {
                d.search = $('#inputSearch').val()
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

    

</script>
<script>
    // var table = $('#allreqsss').dataTable({

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
    //             d.search = $('#inputSearch').val(), d.client_id = $('#direct_client_id').val(), d.custom =
    //                 false
    //         }
    //     },
    //     // ajax			: {'url' : '{!! route('service_company.jobs.all_data') !!}', "data": function ( d ) { d.search =	$('#inputSearch').val(), d.client_id =	$('#direct_client_id').val(), d.sharejob =	'sharejob' } },	
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
    // new $.fn.dataTable.FixedHeader(table);
</script>
<script>
    var company_id = {{ $directClientDetail->company_id ?? $directClientDetail->from_company_id ?? $directClientDetail->companies_id ?? $directClientDetail->client_id ?? 0 }};
    function initDContactsDataTable() {
        if ($.fn.DataTable.isDataTable('#dcontactList')) {
            return;
        }
        var dcontactList = $('#dcontactList').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            destroy: true,
            ordering: false, //	disable ordering
            "bLengthChange": false, //	thought this line could hide the LengthMenu
            sDom: 'lrtip',
            oLanguage: {
                sLengthMenu: "_MENU_",
            },
            "pageLength": 10,
            colReorder: true,
            ajax: {
                'url': '{!! route('service_company.clients.dcontactList') !!}?company_id=' + company_id,
                "data": function(d) {
                    d.search = $('#dcontactSearchInput').val() || $('#inputSearch').val(), d.client_id = $('#direct_client_id').val()
                }
            },
            "drawCallback": function(settings) {
                $('[data-toggle="tooltip1"]').tooltip();
            },
            "columnDefs": [{
                    "width": "50px",
                    "targets": 0
                },
                {
                    "width": "100px",
                    "targets": 1,
                    "class": "align-items-center border-0"
                },
                {
                    "width": "50px",
                    "targets": 2
                },
                {
                    "width": "200px",
                    "targets": 3
                }
            ],
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
        new $.fn.dataTable.FixedHeader(dcontactList);
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

    $(document).ready(function() {
        var loadedTabs = {};

        if ($('#candidate_list').hasClass('active') || $('a[href="#candidate_list"]').hasClass('active')) {
            directfetchCandidates();
            loadedTabs['candidate_list'] = true;
        } else if ($('#contacts').hasClass('active') || $('a[href="#contacts"]').hasClass('active')) {
            initDContactsDataTable();
            loadedTabs['contacts'] = true;
        }

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"], a[data-bs-toggle="tab"]', function (e) {
            var target = $(e.target).attr('href');
            if (!target) return;
            target = target.replace('#', '');

            if (loadedTabs[target]) return;

            if (target === 'contacts') {
                initDContactsDataTable();
                loadedTabs[target] = true;
            } else if (target === 'candidate_list') {
                directfetchCandidates();
                loadedTabs[target] = true;
            } else if (target === 'requisitions') {
                fetchJobsClientdirect();
                loadedTabs[target] = true;
            } else if (target === 'interview_tracker') {
                directfetchCandidatesInverViewTracker();
                loadedTabs[target] = true;
            } else if (target === 'candidate_joining') {
                directfetchCandidatesJoin();
                loadedTabs[target] = true;
            } else if (target === 'candidateDashboard') {
                fetchClientDashboardAnalytics();
                loadedTabs[target] = true;
            }
        });

        $(document).on('keyup', '#dcontactSearchInput', function() {
            if ($.fn.DataTable.isDataTable('#dcontactList')) {
                $('#dcontactList').DataTable().search($(this).val()).draw();
            }
        });

        $('#search').on('keyup', function() {
            const search = $(this).val();
            directfetchCandidates(1, search);
        });

        $(document).off('click', '#paginationcandidate [data-page], #paginationcandidate .page-link, #paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow');
        $(document).on('click', '#paginationcandidate [data-page], #paginationcandidate .page-link, #paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            directfetchCandidates(page, search);
        });

        $(document).off('click', '#paginationcandidatejoin [data-page], #paginationcandidatejoin .page-link, #paginationcandidatejoin .pagi-page, #paginationcandidatejoin .pagi-arrow');
        $(document).on('click', '#paginationcandidatejoin [data-page], #paginationcandidatejoin .page-link, #paginationcandidatejoin .pagi-page, #paginationcandidatejoin .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            directfetchCandidatesJoin(page, search);
        });

        $(document).off('click', '#paginationcandidateinterviewtracker [data-page], #paginationcandidateinterviewtracker .page-link, #paginationcandidateinterviewtracker .pagi-page, #paginationcandidateinterviewtracker .pagi-arrow');
        $(document).on('click', '#paginationcandidateinterviewtracker [data-page], #paginationcandidateinterviewtracker .page-link, #paginationcandidateinterviewtracker .pagi-page, #paginationcandidateinterviewtracker .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            directfetchCandidatesInverViewTracker(page, search);
        });

        $(document).off('click', '#directpaginationclientjobs [data-page], #directpaginationclientjobs .page-link, #directpaginationclientjobs .pagi-page, #directpaginationclientjobs .pagi-arrow');
        $(document).on('click', '#directpaginationclientjobs [data-page], #directpaginationclientjobs .page-link, #directpaginationclientjobs .pagi-page, #directpaginationclientjobs .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputJobSearch').val() || '';
            fetchJobsClientdirect(page, search);
        });


    });

    function directfetchCandidates(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        var clientid = '{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}';
        return $.ajax({
            url: `{{ url('service_company/direct-client-candidate-list') }}?page=${page}`,
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

    function directfetchCandidatesJoin(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        var clientid = '{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}';
        return $.ajax({
            url: `{{ url('service_company/direct-client-candidate-join-list') }}?page=${page}`,
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

    function directfetchCandidatesInverViewTracker(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        var clientid = '{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}';
        return $.ajax({
            url: `{{ url('service_company/direct-client-candidate-interview-tracker') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#candidatetListinterviewtracker').html(response.html);
                $('#paginationcandidateinterviewtracker').html(response.pagination);
                if (response.pagination && typeof response.pagination.total !== 'undefined') {
                    $('#kpiInterviewsScheduledCount').text(response.pagination.total);
                    var $badge = $('#tabBadgeInterviewTracker');
                    if ($badge.length) {
                        $badge.text(response.pagination.total);
                        if (response.pagination.total > 0) { $badge.show(); } else { $badge.hide(); }
                    }
                }
            }
        });
    }

    function fetchJobsClientdirect(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/jobs_side_bar_job_ajax/data') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search || $('#inputJobSearch').val(),
                client_id: $('#direct_client_id').val() || '{{ $directClientDetail->client_id }}',
                custom:false
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#directclientJobsListajax').html(response.html);
                $('#directpaginationclientjobs').html(response.pagination);
            }
        });
    }

    function fetchClientDashboardAnalytics() {
        var clientId = '{{ $dc_id }}';
        return $.ajax({
            url: `{{ url('service_company/clients/dashboard-analytics') }}/${clientId}`,
            type: 'GET',
            success: function(response) {
                if (response && response.status === 'success' && response.data) {
                    console.log('⚡ [Client Dashboard APM] Analytics loaded lazily for direct client', response.data);
                    var d = response.data;
                    var activeCount = d.counts ? (d.counts.active || 0) : 0;
                    var holdCount = d.counts ? (d.counts.hold || 0) : 0;
                    var inactiveCount = d.counts ? (d.counts.inactive || 0) : 0;
                    var totalJobs = activeCount + holdCount + inactiveCount;

                    $(`#dcAllJobsCount_${clientId}`).text(totalJobs);
                    $(`#dcActiveJobsCount_${clientId}`).text(activeCount);
                    $(`#dcHoldJobsCount_${clientId}`).text(holdCount);
                    $(`#dcClosedJobsCount_${clientId}`).text(inactiveCount);

                    $(`#dcAdded7DaysCount_${clientId}`).text(d.candidateValuesTotal || 0);
                    $(`#dcTotalCandidatesCount_${clientId}`).text(d.total_candidates || 0);
                    $(`#dcTotalOffersCount_${clientId}`).text(d.candidateValuesTotaloffer || 0);
                    $(`#dcTotalJoinedCount_${clientId}`).text(d.candidateValuesTotaljoin || 0);

                    renderDirectClientDashboardCharts(d, clientId);
                }
            }
        });
    }

    $(document).ready(function() {
        fetchClientDashboardAnalytics();
    });


</script>
