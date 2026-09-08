@php
    /**
     * Intent: Direct Client Request Details Drawer UI component.
     * Reuses the existing Direct Client Details design system from dviewMore.blade.php.
     */
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
    $endOfMonthcomman = Carbon::now()->endOfMonth();
    $endDate = Carbon::today()->endOfDay();
    $startDate = Carbon::today()->subDays(6)->startOfDay();

    // Data extraction with safe fallbacks
    $cur_company_id = auth()->user()->company_id ?? 0;
    $detail_from = @$directClientDetail->from_company_id ?: @$directClientDetail->company_id;

    $kpiActive = $active_requisition ?? 0;
    $kpiSubmitted = 0;
    $kpiInterviews = 0;
    $kpiOffers = 0;
    $kpiJoins = 0;

    if ($detail_from) {
        try {
            $kpiJobs = GeneralMethod::DirectClientJobsCountAllStatus($detail_from, $cur_company_id);
            $kpiPerf = GeneralMethod::DirectClientCandidatesCountList($detail_from, $cur_company_id);
            if (isset($kpiJobs->job_active)) $kpiActive = $kpiJobs->job_active;
            if (isset($kpiPerf->all_candidates)) $kpiSubmitted = $kpiPerf->all_candidates;
            if (isset($kpiPerf->all_interview)) $kpiInterviews = $kpiPerf->all_interview;
            if (isset($kpiPerf->offers)) $kpiOffers = $kpiPerf->offers;
            if (isset($kpiPerf->Joined)) $kpiJoins = $kpiPerf->Joined;
        } catch (\Throwable $e) {
            // Keep default KPI active count fallback
        }
    }

    $detailLogo = '';
    if (!empty(@$directClientDetail->logo_logical_path) && strlen(@$directClientDetail->logo_logical_path) > 10) {
        $detailLogo = url('public/' . @$directClientDetail->logo_logical_path);
    } elseif (!empty(@$directClientDetail->logo) && strlen(@$directClientDetail->logo) > 10) {
        $detailLogo = @$directClientDetail->logo;
    }

    $detailName = ucwords(@$directClientDetail->name);
    if (empty($detailName) && !empty(@$directClientDetail->company_name)) {
        $detailName = ucwords(@$directClientDetail->company_name);
    }
    $detailInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', trim($detailName ?: 'C')), 0, 2))));

    $industries_name = '';
    if (!empty(@$directClientDetail->industry) && isset($department[@$directClientDetail->industry])) {
        $industries_name = $department[@$directClientDetail->industry];
    } elseif (!empty(@$directClientDetail->industries) && isset($industries[@$directClientDetail->industries])) {
        $industries_name = $industries[@$directClientDetail->industries];
    }

    $tagsArr = array_filter(array_map('trim', explode(',', (string) @$directClientDetail->tags)));
@endphp

<style>
    @media only screen and (min-width: 900px) and (max-width: 2560px) {
        .table td, .table th, .table tr { width: max-content !important; }
        .table > :not(caption) > * > * { padding: 0.75em 0rem !important; }
        td:nth-child(n+2), th:nth-child(n+2) { width: revert !important; border-bottom: 1px dashed #f5f8fa !important; }
    }
    .table-custom-class tbody tr { height: 82px; position: relative; }
    .iti__flag { margin-top: 2px !important; }
</style>

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

  /* Contacts tab styling */
  .cd-tabcard { background:#fff; border-radius:12px; border:1px solid #eff2f5; overflow:hidden; margin-top:4px; }
  .cd-tabcard-head { padding:18px 24px; border-bottom:1px solid #eff2f5; }
  .cd-tabcard-title { font-size:16px; font-weight:700; color:#181c32; }
  .cd-tabcard-sub { font-size:12px; color:#a1a5b7; margin-top:2px; }
  .cd-contacts .data-table { width:100%; border-collapse:collapse; min-width:600px; }
  .cd-contacts .data-table thead tr { background:#f8f9fa; }
  .cd-contacts .data-table th { padding:12px 16px; text-align:left; font-size:11px; font-weight:600; color:#7e8299; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; border:none !important; }
  .cd-contacts .data-table td { padding:14px 16px; font-size:13px; color:#3f4254; border-bottom:1px solid #f5f8fa; vertical-align:middle; }
  .cd-contacts .data-table tr:hover td { background:#fafbfc; }
  .cd-contacts .contact-avatar-table { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:13px; background:#009ef7; color:#fff; flex-shrink:0; }
  .cd-contacts .contact-name-table { font-size:14px; font-weight:600; color:#181c32; }
</style>

<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <input type="hidden" name="hid_client_id" id="hid_client_id" value="{{ @$directClientDetail->client_id }}" />

    <!--begin::Content-->
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">

        <!-- Hero Profile Card + KPI row -->
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
                                @if (!empty(@$directClientDetail->website))
                                    <span class="meta-divider">|</span>
                                    <a href="{{ @$directClientDetail->website }}" target="_blank" class="meta-link"><i class="fas fa-globe"></i>{{ @$directClientDetail->website }} <i class="fas fa-external-link-alt"></i></a>
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
                    <div class="kpi-number">{{ $kpiInterviews }}</div>
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

        <!-- Tabs Section -->
        <div class="card card-custom mx-2">
            <div class="card-header card-header-tabs-line">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x cd-maintabs" role="tablist">
                        <li class="nav-item me-3">
                            <a class="nav-link active" data-toggle="tab" href="#overview_req">
                                <span class="nav-text font-weight-bold">Overview</span>
                            </a>
                        </li>
                        @if (!empty($client_contact_info) && count($client_contact_info) > 0)
                        <li class="nav-item me-3">
                            <a class="nav-link" data-toggle="tab" href="#contacts_req">
                                <span class="nav-text font-weight-bold">Contacts</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="card-body py-0">
                <div class="tab-content pt-5">
                    <!-- Overview Tab Pane -->
                    <div class="tab-pane active" id="overview_req" role="tabpanel">
                        <div class="cd-content-grid py-5">
                            <div class="cd-card">
                                <div class="cd-card-head">
                                    <div class="cd-card-ic"><i class="fas fa-info-circle"></i></div>
                                    <h3 class="cd-card-title">About the Client</h3>
                                </div>
                                <div class="cd-about">
                                    {!! @$directClientDetail->description !!}
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
                                    @if ($industries_name)
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Industry</span>
                                        <span class="cd-info-value">{{ $industries_name }}</span>
                                    </div>
                                    @endif
                                    @if (!empty(@$directClientDetail->company_type) && isset($companyType[@$directClientDetail->company_type]))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Company Type</span>
                                        <span class="cd-info-value">{{ $companyType[@$directClientDetail->company_type] }}</span>
                                    </div>
                                    @endif
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Vendor ID</span>
                                        <span class="cd-info-value">VEN-{{ @$directClientDetail->client_id }}</span>
                                    </div>
                                    @if (@$directClientDetail->founded)
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Founded</span>
                                        <span class="cd-info-value">
                                            @php
                                                $currentYr = date('Y');
                                                $foundedYr = @$directClientDetail->founded;
                                                $diffYr = $currentYr - $foundedYr;
                                                echo $diffYr > 0 ? $diffYr . ' Years ago' : $foundedYr;
                                            @endphp
                                        </span>
                                    </div>
                                    @endif
                                    @if (@$directClientDetail->company_size)
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Number of Employees</span>
                                        <span class="cd-info-value">{{ number_format(@$directClientDetail->company_size) }}+</span>
                                    </div>
                                    @endif
                                    @if (!empty(@$directClientDetail->to_created_at))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Connected On</span>
                                        <span class="cd-info-value">{{ date('d M, Y', strtotime(@$directClientDetail->to_created_at)) }}</span>
                                    </div>
                                    @endif
                                    @if (count($tagsArr))
                                    <div class="cd-info-row">
                                        <span class="cd-info-label">Specialties</span>
                                        <span class="cd-info-value">{{ ucwords(implode(', ', $tagsArr)) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contacts Tab Pane -->
                    @if (!empty($client_contact_info) && count($client_contact_info) > 0)
                    <div class="tab-pane" id="contacts_req" role="tabpanel">
                        <div class="cd-tabcard cd-contacts my-5">
                            <div class="cd-tabcard-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                <div>
                                    <div class="cd-tabcard-title">Contacts</div>
                                    <div class="cd-tabcard-sub">Contacts associated with this client.</div>
                                </div>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Contact</th>
                                            <th>Designation</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($client_contact_info as $cci)
                                        <tr>
                                            <td>
                                                <div style="display:flex;align-items:center;gap:10px;">
                                                    <div class="contact-avatar-table">
                                                        {{ strtoupper(mb_substr(@$cci->contact_person_name ?: 'C', 0, 1)) }}
                                                    </div>
                                                    <span class="contact-name-table">{{ @$cci->contact_person_name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ @$cci->designation ?: '-' }}</td>
                                            <td><a href="mailto:{{ @$cci->email }}" style="color:#009ef7;">{{ @$cci->email ?: '-' }}</a></td>
                                            <td>{{ @$cci->phone ?: '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
