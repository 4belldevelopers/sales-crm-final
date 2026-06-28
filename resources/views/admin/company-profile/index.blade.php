@extends('layouts.demo_admin_app')

@php
    use App\Helper\GeneralMethod;

    /* ---- Real company data (safe fallbacks so the page never breaks) ---- */
    $c            = $company ?? null;
    $companyName  = $c?->company_name      ?: 'Your Company';
    $companyLogo  = $c?->logo_url          ?: asset('assets/logo-not-found.png');
    $companyMail  = $c?->company_email     ?: '';
    $companyPhone = trim(($c?->calling_code ?? '') . ' ' . ($c?->company_phone ?? ''));
    $website      = $c?->website           ?: '';
    $linkedin     = $c?->linkedin          ?: '';
    $address      = $c?->address           ?: 'Location not set';
    $companySize  = $c?->company_size      ?: '—';
    $founded      = $c?->founded           ?: '—';
    $description  = $c?->description        ?: 'Add a short description about your company so candidates and agencies understand who you are and what you do.';

    /* Stats from controller (real counts) */
    $st = $stats ?? ['active_jobs'=>0,'candidates'=>0,'interviews'=>0,'offers'=>0,'joinings'=>0,'team'=>0];
@endphp

@push('head-script')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" />
<style>
/* ============================================================
   COMPANY PROFILE / DASHBOARD  -  scoped styles
   Poppins everywhere, base font 14px (max), tuned for 15.6" laptops
   ============================================================ */
#cp-page, #cp-page * { font-family:'Poppins', sans-serif; }
#cp-page { font-size:13px; color:#3f4254; }
#cp-page .fs-tiny  { font-size:11px; }
#cp-page .fs-sm    { font-size:12px; }
#cp-page .fs-base  { font-size:13px; }
#cp-page .fs-lg    { font-size:14px; }      /* maximum allowed */
#cp-page h1,#cp-page h2,#cp-page h3,#cp-page h4,#cp-page h5,#cp-page h6{ font-size:14px; margin:0; }
#cp-page .cp-num   { font-size:18px; font-weight:700; line-height:1.1; }
#cp-page .text-dark{ color:#181c32 !important; }
#cp-page .muted    { color:#a1a5b7; }

/* cards */
#cp-page .cp-card{ background:#fff; border:1px solid #f1f1f4; border-radius:12px; box-shadow:0 2px 10px rgba(20,20,40,.03); }
#cp-page .cp-card .cp-card-body{ padding:16px 18px; }

/* hero */
#cp-page .cp-logo{ width:78px; height:78px; border-radius:14px; object-fit:cover; border:1px solid #eef0f3; background:#fff; }
#cp-page .cp-verified{ color:#1b84ff; }
#cp-page .cp-chip{ display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:6px; font-size:11px; font-weight:500; background:#eef3ff; color:#1b84ff; }
#cp-page .cp-meta{ display:inline-flex; align-items:center; gap:6px; color:#5e6278; font-size:12px; }
#cp-page .cp-meta i{ color:#a1a5b7; }

/* buttons */
#cp-page .cp-btn{ display:inline-flex; align-items:center; gap:7px; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:500; border:1px solid transparent; cursor:pointer; transition:.15s; text-decoration:none; }
#cp-page .cp-btn-primary{ background:#1b84ff; color:#fff; }
#cp-page .cp-btn-primary:hover{ background:#056ee9; color:#fff; }
#cp-page .cp-btn-light{ background:#f4f6fa; color:#3f4254; border-color:#eef0f3; }
#cp-page .cp-btn-light:hover{ background:#eaeef5; color:#181c32; }
#cp-page .cp-btn-success{ background:#17c653; color:#fff; }
#cp-page .cp-btn-success:hover{ background:#13a544; color:#fff; }
#cp-page .cp-btn-sm{ padding:6px 12px; font-size:12px; }

/* donut */
#cp-page .cp-donut{ width:118px; height:118px; border-radius:50%; display:grid; place-items:center; }
#cp-page .cp-donut .cp-donut-inner{ width:86px; height:86px; background:#fff; border-radius:50%; display:grid; place-items:center; text-align:center; }

/* coins */
#cp-page .cp-coin-ic{ width:40px; height:40px; border-radius:10px; display:grid; place-items:center; font-size:16px; }

/* tabs */
#cp-page .cp-tabs{ display:flex; gap:4px; flex-wrap:wrap; border-bottom:1px solid #f1f1f4; }
#cp-page .cp-tab{ border:none; background:transparent; padding:12px 14px 14px; font-size:13px; font-weight:500; color:#5e6278; cursor:pointer; position:relative; display:flex; flex-direction:column; align-items:center; line-height:1.25; text-align:center; }
#cp-page .cp-tab small{ font-size:10px; color:#a1a5b7; font-weight:400; }
#cp-page .cp-tab.active{ color:#1b84ff; }
#cp-page .cp-tab.active::after{ content:''; position:absolute; left:8px; right:8px; bottom:-1px; height:2px; background:#1b84ff; border-radius:2px; }
#cp-page .cp-tab:hover{ color:#181c32; }

/* stat tiles */
#cp-page .cp-stat{ display:flex; align-items:center; gap:12px; }
#cp-page .cp-stat-ic{ width:42px; height:42px; border-radius:10px; display:grid; place-items:center; font-size:17px; flex:0 0 42px; }
#cp-page .cp-up{ color:#17c653; font-size:11px; font-weight:600; }
#cp-page .cp-down{ color:#f8285a; font-size:11px; font-weight:600; }

/* generic rows */
#cp-page .cp-row{ display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px dashed #f1f1f4; }
#cp-page .cp-row:last-child{ border-bottom:none; }
#cp-page .cp-tag{ display:inline-block; padding:5px 11px; border-radius:6px; background:#f4f6fa; color:#3f4254; font-size:12px; font-weight:500; margin:0 5px 5px 0; }
#cp-page .cp-iconbox{ width:30px; height:30px; border-radius:8px; display:inline-grid; place-items:center; font-size:13px; }

/* status pills */
#cp-page .pill{ padding:2px 9px; border-radius:20px; font-size:10px; font-weight:600; }
#cp-page .pill-green{ background:#e9fbef; color:#17c653; }
#cp-page .pill-orange{ background:#fff4e5; color:#f6a609; }
#cp-page .pill-red{ background:#ffeef1; color:#f8285a; }

/* ===== Interview Pipeline Builder ===== */
#cp-page .cp-pipeline{ display:flex; gap:14px; overflow-x:auto; padding-bottom:8px; }
#cp-page .cp-stage{ flex:1 1 0; min-width:215px; background:#f9fafc; border:1px solid #f1f1f4; border-radius:12px; display:flex; flex-direction:column; }
#cp-page .cp-stage-head{ border-radius:12px 12px 0 0; padding:13px 14px; color:#fff; display:flex; align-items:center; justify-content:space-between; }
#cp-page .cp-stage-head .cp-stage-title{ display:flex; align-items:center; gap:9px; font-size:13px; font-weight:600; }
#cp-page .cp-stage-head .cp-stage-ic{ width:26px; height:26px; border-radius:7px; background:rgba(255,255,255,.22); display:grid; place-items:center; font-size:13px; }
#cp-page .cp-stage-dots{ background:rgba(255,255,255,.22); width:26px; height:26px; border-radius:7px; border:none; color:#fff; cursor:pointer; }
#cp-page .cp-stage-body{ padding:12px; min-height:120px; flex:1; }
#cp-page .cp-sub-label{ font-size:11px; font-weight:600; color:#a1a5b7; text-transform:uppercase; letter-spacing:.3px; margin-bottom:9px; }
#cp-page .cp-substage{ display:flex; align-items:center; gap:9px; background:#fff; border:1px solid #eef0f3; border-radius:9px; padding:9px 11px; margin-bottom:8px; font-size:12px; font-weight:500; color:#3f4254; cursor:grab; transition:.15s; }
#cp-page .cp-substage:hover{ box-shadow:0 3px 10px rgba(20,20,40,.08); transform:translateY(-1px); }
#cp-page .cp-substage.dragging{ opacity:.45; }
#cp-page .cp-substage .cp-sub-ic{ width:24px; height:24px; border-radius:6px; display:grid; place-items:center; font-size:11px; flex:0 0 24px; }
#cp-page .cp-stage.drag-over .cp-stage-body{ outline:2px dashed #b6c4ff; outline-offset:-6px; border-radius:10px; }
#cp-page .cp-locked{ font-size:11px; color:#f8285a; font-weight:500; }

/* info strip */
#cp-page .cp-info{ display:flex; align-items:flex-start; gap:12px; }
#cp-page .cp-info-ic{ width:40px; height:40px; border-radius:10px; display:grid; place-items:center; font-size:17px; flex:0 0 40px; }

/* sysstage box */
#cp-page .cp-sysbox{ display:flex; align-items:flex-start; gap:11px; background:#f9fafc; border:1px solid #f1f1f4; border-radius:10px; padding:12px 14px; }

/* tab panes */
#cp-page .cp-pane{ display:none; }
#cp-page .cp-pane.active{ display:block; animation:cpfade .25s ease; }
@keyframes cpfade{ from{opacity:0; transform:translateY(4px);} to{opacity:1; transform:none;} }

/* tidy scrollbars */
#cp-page .cp-pipeline::-webkit-scrollbar,#cp-page .cp-tabs::-webkit-scrollbar{ height:6px; }
#cp-page .cp-pipeline::-webkit-scrollbar-thumb{ background:#e1e3ea; border-radius:6px; }

@media (max-width:1199px){ #cp-page .cp-stage{ min-width:230px; } }
</style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid pt-4" id="cp-page">
  <div class="container-fluid px-4">

    {{-- ======================= PAGE HEADING ======================= --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <div>
        <h2 class="text-dark" style="font-size:16px;font-weight:700;">Company Dashboard</h2>
        <div class="muted fs-sm mt-1"><a href="{{ route('admin.dashboard') }}" class="muted text-decoration-none">Home</a> <span class="mx-1">/</span> <span style="color:#1b84ff;">Company Dashboard</span></div>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.jobs.index') }}" class="cp-btn cp-btn-light cp-btn-sm"><i class="fa fa-briefcase"></i> Find Jobs</a>
        <a href="{{ route('admin.cvtracker.index') }}" class="cp-btn cp-btn-light cp-btn-sm"><i class="fa fa-user"></i> Find Talent</a>
        <a href="{{ route('admin.consultancy.index') }}" class="cp-btn cp-btn-light cp-btn-sm"><i class="fa fa-building"></i> Find Agency</a>
      </div>
    </div>

    {{-- ======================= HERO ROW ======================= --}}
    <div class="row g-4 mb-4">
      {{-- company identity --}}
      <div class="col-xl-5">
        <div class="cp-card h-100">
          <div class="cp-card-body">
            <div class="d-flex gap-3">
              <img src="{{ $companyLogo }}" class="cp-logo" alt="logo" onerror="this.src='{{ asset('assets/logo-not-found.png') }}'">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <span class="text-dark" style="font-size:16px;font-weight:700;">{{ $companyName }}</span>
                  <i class="fa fa-check-circle cp-verified"></i>
                  <span class="cp-chip"><i class="fa fa-shield-alt"></i> Verified Employer</span>
                </div>
                <div class="muted fs-sm mt-1">Staffing &amp; Recruiting Solutions</div>
                <div class="d-flex gap-3 flex-wrap mt-2">
                  <span class="cp-meta"><i class="fa fa-map-marker-alt"></i>{{ $address }}</span>
                  <span class="cp-meta"><i class="fa fa-users"></i>{{ $companySize }} Employees</span>
                  <span class="cp-meta"><i class="fa fa-calendar-alt"></i>Founded {{ $founded }}</span>
                  @if($linkedin)<a href="{{ $linkedin }}" target="_blank" class="cp-meta text-decoration-none"><i class="fab fa-linkedin" style="color:#0a66c2;font-size:14px;"></i></a>@endif
                </div>
                <div class="d-flex gap-2 mt-3 flex-wrap">
                  <a href="{{ route('admin.profile.edit-profile') }}" class="cp-btn cp-btn-primary cp-btn-sm"><i class="fa fa-pen"></i> Edit Profile</a>
                  @if(!empty($c?->career_page_link))
                    <a href="{{ $c?->career_page_link }}" target="_blank" class="cp-btn cp-btn-light cp-btn-sm">Preview Career Page <i class="fa fa-external-link-alt fs-tiny"></i></a>
                  @else
                    <a href="{{ route('admin.account.index') }}" class="cp-btn cp-btn-light cp-btn-sm">Preview Career Page <i class="fa fa-external-link-alt fs-tiny"></i></a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- profile completion --}}
      <div class="col-xl-4">
        <div class="cp-card h-100">
          <div class="cp-card-body">
            <div class="fw-600 text-dark fs-lg mb-3" style="font-weight:600;">Profile Completion</div>
            <div class="d-flex align-items-center gap-3">
              <div class="cp-donut" style="background:conic-gradient(#1b84ff 0deg 302deg,#eef0f3 302deg 360deg);">
                <div class="cp-donut-inner">
                  <div><div class="cp-num" style="color:#1b84ff;">84%</div><div class="fs-tiny muted">Complete</div></div>
                </div>
              </div>
              <div class="flex-grow-1">
                @foreach([['Basic Information',1],['Company Details',1],['Branding',1],['Team Information',1],['Integrations',0]] as $ck)
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fs-sm">{{ $ck[0] }}</span>
                    @if($ck[1])<span class="fs-tiny" style="color:#17c653;">Completed <i class="fa fa-check-circle"></i></span>
                    @else<span class="fs-tiny" style="color:#f6a609;">Pending <i class="fa fa-exclamation-circle"></i></span>@endif
                  </div>
                @endforeach
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-between mt-2">
              <span class="fs-tiny muted">Complete your profile to build trust and get more talent.</span>
            </div>
            <a href="{{ route('admin.account.index') }}" class="fs-sm text-decoration-none d-block text-end mt-1" style="color:#1b84ff;">View Checklist →</a>
          </div>
        </div>
      </div>

      {{-- coins --}}
      <div class="col-xl-3">
        <div class="cp-card mb-3">
          <div class="cp-card-body d-flex align-items-start gap-3">
            <div class="cp-coin-ic" style="background:#eef3ff;color:#1b84ff;"><i class="fa fa-coins"></i></div>
            <div class="flex-grow-1">
              <div class="fs-sm muted">Operation Coins</div>
              <div class="cp-num text-dark">12,450</div>
              <div class="fs-tiny muted">Available</div>
            </div>
            <a href="{{ route('admin.subscribe.index') }}" class="cp-btn cp-btn-light cp-btn-sm">Upgrade</a>
          </div>
        </div>
        <div class="cp-card">
          <div class="cp-card-body d-flex align-items-start gap-3">
            <div class="cp-coin-ic" style="background:#e9fbef;color:#17c653;"><i class="fa fa-coins"></i></div>
            <div class="flex-grow-1">
              <div class="fs-sm muted">Sales Coins</div>
              <div class="cp-num text-dark">8,230</div>
              <div class="fs-tiny muted">Available</div>
            </div>
            <a href="{{ route('admin.subscribe.index') }}" class="cp-btn cp-btn-success cp-btn-sm">Upgrade</a>
          </div>
        </div>
      </div>
    </div>

    {{-- ======================= TABS ======================= --}}
    <div class="cp-card mb-4">
      <div class="cp-tabs px-3" id="cp-tabnav">
        <button class="cp-tab active" data-tab="overview"><i class="fa fa-home mb-1"></i>Overview</button>
        <button class="cp-tab" data-tab="interview"><i class="fa fa-sitemap mb-1"></i>Interview Stage</button>
        <button class="cp-tab" data-tab="settings"><i class="fa fa-cog mb-1"></i>Settings</button>
        <button class="cp-tab" data-tab="toc-candidate"><i class="fa fa-file-alt mb-1"></i>Terms &amp; Conditions<small>(For Candidate Offer Letter)</small></button>
        <button class="cp-tab" data-tab="salary"><i class="fa fa-money-bill mb-1"></i>Salary Structure<small>(For Candidate Offer Letter)</small></button>
        <button class="cp-tab" data-tab="toc-agency"><i class="fa fa-file-contract mb-1"></i>Terms &amp; Conditions<small>(For Agency)</small></button>
        <button class="cp-tab" data-tab="commercials"><i class="fa fa-handshake mb-1"></i>Commercials<small>(For Clients)</small></button>
        <button class="cp-tab" data-tab="ads"><i class="fa fa-bullhorn mb-1"></i>Ads Settings</button>
      </div>
    </div>

    {{-- ============================================================
         OVERVIEW PANE
         ============================================================ --}}
    <div class="cp-pane active" id="pane-overview">

      {{-- stats --}}
      <div class="row g-3 mb-4">
        @php
        $statCards = [
          ['ic'=>'fa-briefcase','bg'=>'#eef3ff','cl'=>'#1b84ff','num'=>$st['active_jobs'],'lbl'=>'Active Jobs','chg'=>'20% vs last month','up'=>true],
          ['ic'=>'fa-users','bg'=>'#f3eeff','cl'=>'#7239ea','num'=>$st['candidates'],'lbl'=>'Candidates','chg'=>'18% vs last month','up'=>true],
          ['ic'=>'fa-calendar-check','bg'=>'#eef3ff','cl'=>'#1b84ff','num'=>$st['interviews'],'lbl'=>'Interviews','chg'=>'15% vs last month','up'=>true],
          ['ic'=>'fa-file-signature','bg'=>'#fff4e5','cl'=>'#f6a609','num'=>$st['offers'],'lbl'=>'Offers','chg'=>'14% vs last month','up'=>true],
          ['ic'=>'fa-check-circle','bg'=>'#e9fbef','cl'=>'#17c653','num'=>$st['joinings'],'lbl'=>'Joinings','chg'=>'10% vs last month','up'=>true],
          ['ic'=>'fa-trophy','bg'=>'#f3eeff','cl'=>'#7239ea','num'=>'#1,257','lbl'=>'Global Rank','chg'=>'Top 18% in Platform','up'=>true],
          ['ic'=>'fa-star','bg'=>'#fff4e5','cl'=>'#f6a609','num'=>'812','lbl'=>'Score','chg'=>'Excellent','up'=>true],
        ];
        @endphp
        @foreach($statCards as $s)
        <div class="col-6 col-md-4 col-xl">
          <div class="cp-card h-100"><div class="cp-card-body">
            <div class="cp-stat">
              <div class="cp-stat-ic" style="background:{{ $s['bg'] }};color:{{ $s['cl'] }};"><i class="fa {{ $s['ic'] }}"></i></div>
              <div>
                <div class="cp-num text-dark">{{ $s['num'] }}</div>
                <div class="fs-sm muted">{{ $s['lbl'] }}</div>
              </div>
            </div>
            <div class="mt-2 {{ $s['up'] ? 'cp-up' : 'cp-down' }}">
              @if(str_contains($s['chg'],'%') && str_contains($s['chg'],'vs'))<i class="fa fa-arrow-up"></i> @endif{{ $s['chg'] }}
            </div>
          </div></div>
        </div>
        @endforeach
      </div>

      {{-- 3 column body --}}
      <div class="row g-4">
        {{-- About --}}
        <div class="col-xl-4">
          <div class="cp-card h-100"><div class="cp-card-body">
            <div class="d-flex align-items-center gap-2 mb-3"><i class="fa fa-info-circle" style="color:#1b84ff;"></i><span class="fw-600 text-dark fs-lg" style="font-weight:600;">About {{ $companyName }}</span></div>
            <p class="fs-sm" style="color:#5e6278;line-height:1.6;">{{ $description }}</p>
            <div class="mt-3">
              @php $about=[
                ['fa-industry','Industry','Staffing &amp; Recruiting'],
                ['fa-map-marker-alt','Headquarters',$address],
                ['fa-users','Company Size',$companySize.' Employees'],
                ['fa-building','Type','Private'],
                ['fa-calendar-alt','Founded',$founded],
              ]; @endphp
              @foreach($about as $a)
                <div class="cp-row"><span class="muted fs-sm"><i class="fa {{ $a[0] }} me-2"></i>{{ $a[1] }}</span><span class="fs-sm text-dark">{!! $a[2] !!}</span></div>
              @endforeach
              @if($website)
                <div class="cp-row"><span class="muted fs-sm"><i class="fa fa-globe me-2"></i>Website</span><a href="{{ \Illuminate\Support\Str::startsWith($website,'http') ? $website : 'https://'.$website }}" target="_blank" class="fs-sm text-decoration-none" style="color:#1b84ff;">{{ $website }} <i class="fa fa-external-link-alt fs-tiny"></i></a></div>
              @endif
            </div>
            <div class="mt-3">
              <div class="cp-sub-label mb-2">Services</div>
              @foreach(['Staffing','Hiring','Contract Hiring','C2C Hiring'] as $tag)<span class="cp-tag">{{ $tag }}</span>@endforeach
            </div>
          </div></div>
        </div>

        {{-- performance --}}
        <div class="col-xl-4">
          <div class="cp-card mb-4"><div class="cp-card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="fw-600 text-dark fs-lg" style="font-weight:600;"><i class="fa fa-chart-line me-2" style="color:#1b84ff;"></i>Hiring Performance</span>
              <a href="{{ route('admin.customreport.index') }}" class="fs-sm text-decoration-none" style="color:#1b84ff;">View Report →</a>
            </div>
            @php $perf=[
              ['CV Shortlist Ratio','32%','6%',true],
              ['Interview Conversion','42%','5%',true],
              ['Offer Acceptance Ratio','82%','7%',true],
              ['Average Closure Time','14 Days','2 Days',false],
            ]; @endphp
            @foreach($perf as $p)
              <div class="cp-row">
                <span class="fs-sm muted">{{ $p[0] }}</span>
                <span class="d-flex align-items-center gap-2"><span class="fs-sm text-dark fw-600" style="font-weight:600;">{{ $p[1] }}</span>
                <span class="{{ $p[3] ? 'cp-up' : 'cp-down' }}"><i class="fa fa-arrow-{{ $p[3] ? 'up' : 'down' }}"></i> {{ $p[2] }}</span></span>
              </div>
            @endforeach
          </div></div>
          <div class="cp-card"><div class="cp-card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="fw-600 text-dark fs-lg" style="font-weight:600;"><i class="fa fa-store me-2" style="color:#7239ea;"></i>Marketplace Performance</span>
              <a href="{{ route('admin.consultancy.index') }}" class="fs-sm text-decoration-none" style="color:#1b84ff;">View All Agencies →</a>
            </div>
            @php $mkt=[['Agencies Empaneled','42'],['Active Agencies','19'],['Agency Contribution','36%'],['Top Agency','Hirebee Resources'],['Bench Resources Available','128']]; @endphp
            @foreach($mkt as $m)
              <div class="cp-row"><span class="fs-sm muted">{{ $m[0] }}</span><span class="fs-sm text-dark fw-600" style="font-weight:600;">{{ $m[1] }}</span></div>
            @endforeach
          </div></div>
        </div>

        {{-- checklist + team --}}
        <div class="col-xl-4">
          <div class="cp-card mb-4"><div class="cp-card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="fw-600 text-dark fs-lg" style="font-weight:600;"><i class="fa fa-tasks me-2" style="color:#17c653;"></i>Configuration Checklist</span>
              <a href="{{ route('admin.account.index') }}" class="fs-sm text-decoration-none" style="color:#1b84ff;">View All →</a>
            </div>
            @php $chk=[
              ['Company profile completed','Completed','green','fa-check-circle'],
              ['Career page live','Completed','green','fa-check-circle'],
              ['Interview stages configured','Completed','green','fa-check-circle'],
              ['Offer letter templates configured','Completed','green','fa-check-circle'],
              ['Agency commercials configured','Pending','orange','fa-exclamation-circle'],
              ['WhatsApp automation','Inactive','red','fa-times-circle'],
            ]; @endphp
            @foreach($chk as $k)
              <div class="cp-row">
                <span class="fs-sm"><i class="fa {{ $k[3] }} me-2" style="color:{{ $k[2]=='green'?'#17c653':($k[2]=='orange'?'#f6a609':'#f8285a') }};"></i>{{ $k[0] }}</span>
                <span class="pill pill-{{ $k[2] }}">{{ $k[1] }}</span>
              </div>
            @endforeach
          </div></div>
          <div class="cp-card"><div class="cp-card-body">
            <div class="fw-600 text-dark fs-lg mb-3" style="font-weight:600;"><i class="fa fa-user-friends me-2" style="color:#1b84ff;"></i>Team Overview</div>
            <div class="cp-row"><span class="fs-sm muted"><i class="fa fa-users me-2"></i>Total Recruiters</span><span class="fs-sm text-dark fw-600" style="font-weight:600;">{{ $st['team'] }}</span></div>
            <div class="cp-row"><span class="fs-sm muted"><i class="fa fa-user-check me-2"></i>Active Recruiters</span><span class="fs-sm text-dark fw-600" style="font-weight:600;">{{ max(0,$st['team']-2) }}</span></div>
            <div class="cp-row"><span class="fs-sm muted"><i class="fa fa-crown me-2"></i>Captains</span><span class="fs-sm text-dark fw-600" style="font-weight:600;">4</span></div>
            <div class="cp-row"><span class="fs-sm muted"><i class="fa fa-medal me-2"></i>Team Score</span><span class="cp-up">8,120 <i class="fa fa-arrow-up"></i> 12%</span></div>
            <a href="{{ route('admin.team.index') }}" class="cp-btn cp-btn-light cp-btn-sm w-100 justify-content-center mt-3">Manage Team</a>
          </div></div>
        </div>
      </div>

      {{-- bottom 3 cards --}}
      <div class="row g-4 mt-1">
        <div class="col-xl-4">
          <div class="cp-card h-100"><div class="cp-card-body">
            <div class="d-flex align-items-center justify-content-between mb-3"><span class="fw-600 text-dark fs-lg" style="font-weight:600;"><i class="fa fa-tags me-2" style="color:#1b84ff;"></i>Services &amp; Rates</span><a href="{{ route('admin.account.index') }}" class="fs-sm text-decoration-none" style="color:#1b84ff;">Manage Services →</a></div>
            <div class="row g-3">
              <div class="col-6"><div class="cp-sysbox flex-column"><div class="d-flex align-items-center gap-2 mb-1"><i class="fa fa-clock" style="color:#f6a609;"></i><span class="fs-sm muted">Contract Hiring</span></div><div class="cp-num text-dark">$5 <span class="fs-sm muted">/ Hour</span></div><div class="fs-tiny muted mt-1">5 Countries Served</div></div></div>
              <div class="col-6"><div class="cp-sysbox flex-column"><div class="d-flex align-items-center gap-2 mb-1"><i class="fa fa-user-tie" style="color:#17c653;"></i><span class="fs-sm muted">Fulltime Hiring</span></div><div class="cp-num text-dark">8.33% <span class="fs-sm muted">of CTC</span></div><div class="fs-tiny muted mt-1">12 Countries Served</div></div></div>
            </div>
          </div></div>
        </div>
        <div class="col-xl-4">
          <div class="cp-card h-100"><div class="cp-card-body">
            <div class="d-flex align-items-center justify-content-between mb-3"><span class="fw-600 text-dark fs-lg" style="font-weight:600;"><i class="fa fa-cog me-2" style="color:#7239ea;"></i>Settings</span><a href="{{ route('admin.account.index') }}" class="fs-sm text-decoration-none" style="color:#1b84ff;">Manage Settings →</a></div>
            @php $set=[['Duplicate Status - Job wise','60 days check'],['Time Zone',$c?->timezone ?: 'Asia/Kolkata'],['Show Currency','INR (₹) - Indian Rupee'],['Salary Type','Hourly / Monthly / Annually'],['Time Format',$c?->time_format ?: '24 Hour (13:00)']]; @endphp
            @foreach($set as $s)<div class="cp-row"><span class="fs-sm muted">{{ $s[0] }}</span><span class="fs-sm text-dark">{{ $s[1] }}</span></div>@endforeach
          </div></div>
        </div>
        <div class="col-xl-4">
          <div class="cp-card h-100"><div class="cp-card-body">
            <div class="d-flex align-items-center justify-content-between mb-3"><span class="fw-600 text-dark fs-lg" style="font-weight:600;"><i class="fa fa-user-shield me-2" style="color:#1b84ff;"></i>Administrators</span><a href="{{ route('admin.team.index') }}" class="fs-sm text-decoration-none" style="color:#1b84ff;">Manage Administrators →</a></div>
            <div class="cp-row align-items-start">
              <div class="d-flex gap-2"><span class="cp-iconbox" style="background:#eef3ff;color:#1b84ff;"><i class="fa fa-user"></i></span><div><div class="fs-sm text-dark fw-600" style="font-weight:600;">Primary Admin <span class="pill pill-green ms-1">Primary</span></div><div class="fs-tiny muted">{{ $companyMail }}</div>@if($companyPhone)<div class="fs-tiny muted">{{ $companyPhone }}</div>@endif</div></div>
            </div>
            <div class="cp-row align-items-start">
              <div class="d-flex gap-2"><span class="cp-iconbox" style="background:#f3eeff;color:#7239ea;"><i class="fa fa-user"></i></span><div><div class="fs-sm text-dark fw-600" style="font-weight:600;">Operations Head</div><div class="fs-tiny muted">Manage roles &amp; permissions from the Team module.</div></div></div>
            </div>
          </div></div>
        </div>
      </div>
    </div>

    {{-- ============================================================
         INTERVIEW STAGE PANE
         ============================================================ --}}
    <div class="cp-pane" id="pane-interview">
      <div class="cp-card mb-4"><div class="cp-card-body">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
          <div>
            <div class="fw-600 text-dark" style="font-size:15px;font-weight:600;">Interview Pipeline Builder</div>
            <div class="fs-sm muted mt-1">Design and manage your interview pipeline. Drag and drop stages to reorder.</div>
          </div>
          <div class="cp-sysbox" style="max-width:340px;">
            <i class="fa fa-lock" style="color:#a1a5b7;font-size:15px;margin-top:2px;"></i>
            <div><div class="fs-sm text-dark fw-600" style="font-weight:600;">System Stage</div><div class="fs-tiny muted">Some stages are system defined and cannot be edited or deleted.</div></div>
          </div>
        </div>

        {{-- pipeline columns --}}
        <div class="cp-pipeline" id="cp-pipeline">
          @php
          $pipeline = [
            ['key'=>'sourcing','title'=>'Sourcing','ic'=>'fa-search','color'=>'#1b84ff','locked'=>false,'subs'=>[
              ['Job Board','fa-clipboard-list'],['Employee Referral','fa-user-friends'],['Social Media','fa-share-alt'],['Career Site','fa-globe'],['Agency','fa-building']]],
            ['key'=>'screening','title'=>'Screening','ic'=>'fa-filter','color'=>'#7239ea','locked'=>false,'subs'=>[
              ['CV Review','fa-file-alt'],['Shortlist','fa-list-check'],['HR Screening','fa-headset']]],
            ['key'=>'evaluation','title'=>'Evaluation','ic'=>'fa-clipboard-check','color'=>'#4b3ff0','locked'=>false,'subs'=>[
              ['Technical Interview','fa-laptop-code'],['Assessment','fa-tasks'],['HR Interview','fa-comments']]],
            ['key'=>'negotiation','title'=>'Negotiation','ic'=>'fa-handshake','color'=>'#f6a609','locked'=>false,'subs'=>[
              ['Salary Discussion','fa-money-bill-wave'],['Final Discussion','fa-comment-dots']]],
            ['key'=>'offer','title'=>'Offer','ic'=>'fa-award','color'=>'#17c653','locked'=>false,'subs'=>[
              ['Offer Letter','fa-file-signature'],['Offer Accepted','fa-check-double']]],
            ['key'=>'rejected','title'=>'Rejected','ic'=>'fa-lock','color'=>'#f8285a','locked'=>true,'subs'=>[
              ['Duplicate','fa-copy'],['Not Interested','fa-user-slash'],['Not Relevant','fa-ban'],['Profile Mismatch','fa-random'],['Stability Issues','fa-exchange-alt'],['High Notice Period','fa-hourglass-half'],['High Salary Expectations','fa-money-check'],['Interview Rejected','fa-times'],['HR Rejected','fa-user-times'],['Other Reasons','fa-ellipsis-h'],['Proxy/Fake','fa-user-secret'],['Not looking','fa-user-clock']]],
          ];
          @endphp
          @foreach($pipeline as $stage)
          <div class="cp-stage" data-stage="{{ $stage['key'] }}" data-locked="{{ $stage['locked']?'1':'0' }}">
            <div class="cp-stage-head" style="background:{{ $stage['color'] }};">
              <span class="cp-stage-title"><span class="cp-stage-ic"><i class="fa {{ $stage['ic'] }}"></i></span>{{ $stage['title'] }}</span>
              <button class="cp-stage-dots" title="Stage options"><i class="fa fa-ellipsis-v"></i></button>
            </div>
            <div class="cp-stage-body" data-dropzone="{{ $stage['key'] }}">
              <div class="cp-sub-label">{{ $stage['locked'] ? '(Default - Locked) Sub Stages' : 'Sub Stages' }}</div>
              @foreach($stage['subs'] as $i => $sub)
                <div class="cp-substage" draggable="{{ $stage['locked']?'false':'true' }}" data-id="{{ $stage['key'].'-'.$i }}">
                  <span class="cp-sub-ic" style="background:{{ $stage['color'] }}1a;color:{{ $stage['color'] }};"><i class="fa {{ $sub[1] }}"></i></span>
                  <span class="flex-grow-1">{{ $sub[0] }}</span>
                  @if(!$stage['locked'])<i class="fa fa-grip-vertical muted" style="cursor:grab;"></i>@else<i class="fa fa-lock cp-locked"></i>@endif
                </div>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>
      </div></div>

      {{-- info strip --}}
      <div class="cp-card"><div class="cp-card-body">
        <div class="row g-4">
          <div class="col-md-4"><div class="cp-info"><div class="cp-info-ic" style="background:#eef3ff;color:#1b84ff;"><i class="fa fa-hand-pointer"></i></div><div><div class="fs-sm text-dark fw-600" style="font-weight:600;">Drag &amp; Drop</div><div class="fs-tiny muted">Reorder stages or sub-stages by dragging and dropping.</div></div></div></div>
          <div class="col-md-4"><div class="cp-info"><div class="cp-info-ic" style="background:#f3eeff;color:#7239ea;"><i class="fa fa-clock"></i></div><div><div class="fs-sm text-dark fw-600" style="font-weight:600;">SLA &amp; Automation</div><div class="fs-tiny muted">Set SLA and automate actions for each stage.</div></div></div></div>
          <div class="col-md-4"><div class="cp-info"><div class="cp-info-ic" style="background:#f4f6fa;color:#5e6278;"><i class="fa fa-lock"></i></div><div><div class="fs-sm text-dark fw-600" style="font-weight:600;">System Stage</div><div class="fs-tiny muted">Some stages are system defined and cannot be edited or deleted.</div></div></div></div>
        </div>
      </div></div>
    </div>

    {{-- ============================================================
         PLACEHOLDER PANES (design-only, wired-ready)
         ============================================================ --}}
    @foreach([
      'settings'      => ['Settings','fa-cog','Configure duplicate checks, time zone, currency, salary &amp; time formats for your company.','admin.account.index'],
      'toc-candidate' => ['Terms &amp; Conditions (Candidate Offer Letter)','fa-file-alt','Define the terms &amp; conditions that appear on candidate offer letters.','admin.account.index'],
      'salary'        => ['Salary Structure (Candidate Offer Letter)','fa-money-bill','Build the salary structure / breakup used in candidate offer letters.','admin.account.index'],
      'toc-agency'    => ['Terms &amp; Conditions (Agency)','fa-file-contract','Define the terms &amp; conditions shared with empaneled agencies.','admin.consultancy.index'],
      'commercials'   => ['Commercials (For Clients)','fa-handshake','Set commercial rate cards and billing terms for your clients.','admin.clients.index'],
      'ads'           => ['Ads Settings','fa-bullhorn','Manage job promotion &amp; advertising preferences across channels.','admin.jobs.index'],
    ] as $key => $ph)
    <div class="cp-pane" id="pane-{{ $key }}">
      <div class="cp-card"><div class="cp-card-body text-center" style="padding:48px 18px;">
        <div class="cp-info-ic mx-auto mb-3" style="width:56px;height:56px;background:#eef3ff;color:#1b84ff;font-size:22px;"><i class="fa {{ $ph[1] }}"></i></div>
        <div class="fw-600 text-dark fs-lg mb-1" style="font-weight:600;">{!! $ph[0] !!}</div>
        <div class="fs-sm muted mb-3" style="max-width:480px;margin:0 auto;">{!! $ph[2] !!}</div>
        <a href="{{ route($ph[3]) }}" class="cp-btn cp-btn-primary cp-btn-sm"><i class="fa fa-arrow-right"></i> Open module</a>
      </div></div>
    </div>
    @endforeach

  </div>
</div>
@endsection

@push('footer-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
<script>
(function(){
  /* ---------- Tabs (client-side, no server load) ---------- */
  var nav   = document.getElementById('cp-tabnav');
  var panes = document.querySelectorAll('#cp-page .cp-pane');
  function show(tab){
    document.querySelectorAll('#cp-tabnav .cp-tab').forEach(function(b){ b.classList.toggle('active', b.dataset.tab===tab); });
    panes.forEach(function(p){ p.classList.toggle('active', p.id==='pane-'+tab); });
    try{ localStorage.setItem('cp_active_tab', tab); }catch(e){}
  }
  nav.addEventListener('click', function(e){
    var btn = e.target.closest('.cp-tab'); if(!btn) return; show(btn.dataset.tab);
  });
  /* restore last tab + support #hash deep-links */
  var initial = (location.hash||'').replace('#cp-','');
  try{ initial = initial || localStorage.getItem('cp_active_tab'); }catch(e){}
  if(initial && document.getElementById('pane-'+initial)) show(initial);

  /* ---------- Interview pipeline: drag & drop sub-stages ----------
     Persisted to localStorage so reordering survives reloads with NO server call. */
  var dragged = null;
  function bindDrag(){
    document.querySelectorAll('#cp-pipeline .cp-substage[draggable="true"]').forEach(function(el){
      el.addEventListener('dragstart', function(){ dragged=el; el.classList.add('dragging'); });
      el.addEventListener('dragend',   function(){ el.classList.remove('dragging'); dragged=null;
        document.querySelectorAll('#cp-pipeline .cp-stage').forEach(function(s){ s.classList.remove('drag-over'); }); savePipeline(); });
    });
    document.querySelectorAll('#cp-pipeline .cp-stage').forEach(function(stage){
      var locked = stage.dataset.locked==='1';
      var zone   = stage.querySelector('.cp-stage-body');
      stage.addEventListener('dragover', function(e){
        if(!dragged || locked) return; e.preventDefault(); stage.classList.add('drag-over');
        var after = afterEl(zone, e.clientY);
        if(after==null){ zone.appendChild(dragged); } else { zone.insertBefore(dragged, after); }
      });
      stage.addEventListener('dragleave', function(){ stage.classList.remove('drag-over'); });
      stage.addEventListener('drop', function(e){ if(locked) return; e.preventDefault(); stage.classList.remove('drag-over'); });
    });
  }
  function afterEl(zone, y){
    var els = [].slice.call(zone.querySelectorAll('.cp-substage:not(.dragging)'));
    return els.reduce(function(closest, child){
      var box = child.getBoundingClientRect(); var offset = y - box.top - box.height/2;
      if(offset<0 && offset>closest.offset){ return {offset:offset, element:child}; } return closest;
    }, {offset:-Infinity}).element || null;
  }
  function savePipeline(){
    var map={};
    document.querySelectorAll('#cp-pipeline .cp-stage').forEach(function(stage){
      map[stage.dataset.stage] = [].map.call(stage.querySelectorAll('.cp-substage'), function(s){ return s.dataset.id; });
    });
    try{ localStorage.setItem('cp_pipeline_order', JSON.stringify(map)); }catch(e){}
  }
  function restorePipeline(){
    var raw; try{ raw = localStorage.getItem('cp_pipeline_order'); }catch(e){ return; }
    if(!raw) return;
    var map; try{ map = JSON.parse(raw); }catch(e){ return; }
    Object.keys(map).forEach(function(key){
      var stage = document.querySelector('#cp-pipeline .cp-stage[data-stage="'+key+'"]'); if(!stage) return;
      var zone  = stage.querySelector('.cp-stage-body');
      map[key].forEach(function(id){
        var node = stage.querySelector('.cp-substage[data-id="'+id+'"]'); if(node) zone.appendChild(node);
      });
    });
  }
  restorePipeline();
  bindDrag();
})();
</script>
@endpush
