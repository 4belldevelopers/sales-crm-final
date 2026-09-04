<?php
use App\Helper\GeneralMethod;
use Carbon\Carbon;
// dd("jd");
?>
@php 
	$userImage		=	'';
  if(!empty($users->image)){
    $userImage		= 	@$users->image; 	
  }
	
@endphp

  @php
      
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
        $endOfMonthcomman =  Carbon::now()->endOfMonth();
        
				$endDate = Carbon::today()->endOfDay();
				$startDate = Carbon::today()->subDays(6)->startOfDay();

  @endphp

<style>
.iti__flag {
    margin-top: 2px !important;
}

/* ===== Team profile (show) — modern profile look, scoped to .tp-wrap ===== */
.tp-wrap, .tp-wrap *{ font-family:'Poppins', sans-serif; }
.tp-wrap{ font-size:13px; color:#1f2a37; background:#f5f7fb !important; }

/* Cap all font sizes to <=14px (per UI standard) */
.tp-wrap .fs-1,.tp-wrap .fs-2,.tp-wrap .fs-2x,.tp-wrap .fs-2hx,.tp-wrap .fs-3,.tp-wrap .fs-3x,
.tp-wrap h1,.tp-wrap h2,.tp-wrap h3,.tp-wrap h4,.tp-wrap h5{ font-size:14px !important; line-height:1.35; }
.tp-wrap .fs-4,.tp-wrap .fs-5,.tp-wrap .fs-6{ font-size:13px !important; }
.tp-wrap .fs-7,.tp-wrap .fs-8{ font-size:12px !important; }

/* Cards */
.tp-wrap .card{ border:1px solid #eef0f5 !important; border-radius:12px !important; box-shadow:0 1px 2px rgba(20,30,60,.03) !important; }
.tp-wrap .card.shadow-none{ box-shadow:none !important; }

/* ---- Profile header ---- */
.tp-wrap .symbol.symbol-100px > img,
.tp-wrap .symbol.symbol-lg-140px > img,
.tp-wrap .symbol.symbol-100px .symbol-label{
    width:88px !important; height:88px !important;
    border-radius:16px !important; object-fit:cover;
    font-size:30px !important; font-weight:600;
}
.tp-wrap .symbol.symbol-100px .symbol-label span{ font-size:30px !important; }
.tp-wrap .symbol.symbol-100px .h-20px.w-20px{ width:18px !important; height:18px !important; }

/* Name + meta */
.tp-wrap .text-gray-900.fs-2{ font-size:14px !important; font-weight:700 !important; }
.tp-wrap .badge.badge-light{ background:#eef2ff !important; color:#3b5bdb !important; font-weight:500; border-radius:6px; padding:4px 9px; font-size:11px !important; }

/* Close button */
.tp-wrap #kt_activitiesClientTeam_close{ border:1px solid #eef0f5; border-radius:8px; background:#fff; width:34px; height:34px; }
.tp-wrap #kt_activitiesClientTeam_close:hover{ background:#fdecee; }

/* ---- Tabs (underline -> modern) ---- */
.tp-wrap .card-header-tabs-line{ border-bottom:1px solid #eef0f5; padding:0 6px; min-height:auto; }
.tp-wrap .nav-tabs{ border:0 !important; gap:2px; flex-wrap:wrap; }
.tp-wrap .nav-tabs .nav-item{ margin:0 !important; }
.tp-wrap .nav-tabs .nav-link{
    border:0 !important; border-bottom:2px solid transparent !important;
    padding:11px 14px !important; color:#6b7585 !important; background:transparent !important;
    border-radius:0 !important;
}
.tp-wrap .nav-tabs .nav-link .nav-text{ font-size:13px !important; font-weight:500 !important; }
.tp-wrap .nav-tabs .nav-link:hover{ color:#2b63f6 !important; }
.tp-wrap .nav-tabs .nav-link.active,
.tp-wrap .nav-tabs .nav-link.active.show{
    color:#2b63f6 !important; border-bottom:2px solid #2b63f6 !important;
}
.tp-wrap .nav-tabs .nav-link.active .nav-text{ font-weight:600 !important; }

/* ---- Overview rows ---- */
.tp-wrap .col-form-label,.tp-wrap label.text-muted{ font-size:12px !important; }
.tp-wrap .fw-bolder.text-gray-800{ font-weight:600 !important; }

/* ---- Buttons ---- */
.tp-wrap .btn-primary{ background:#2b63f6 !important; border-color:#2b63f6 !important; border-radius:8px; font-size:12.5px !important; font-weight:600; }
.tp-wrap .btn-primary:hover{ background:#1f53e0 !important; border-color:#1f53e0 !important; }
.tp-wrap .btn{ border-radius:8px; }

/* ---- Inputs / selects ---- */
.tp-wrap .form-control,.tp-wrap .form-select{ font-size:13px !important; border-radius:8px !important; }

/* ---- Tables ---- */
.tp-wrap table.table thead tr th{ font-size:11px !important; text-transform:uppercase; letter-spacing:.3px; color:#8a94a6 !important; font-weight:600 !important; }
.tp-wrap table.table tbody td,.tp-wrap table.table td{ font-size:12.5px !important; }

/* ---- Dashboard stat numbers stay within 14px but emphasised ---- */
.tp-wrap .fs-2hx{ font-size:14px !important; font-weight:700 !important; }

/* ---- Responsive (fits 15.6" laptops & down) ---- */
@media (max-width:992px){
    .tp-wrap .nav-tabs{ overflow-x:auto; flex-wrap:nowrap; }
    .tp-wrap .nav-tabs .nav-link{ white-space:nowrap; }
}
@media (max-width:768px){
    .tp-wrap .d-flex.flex-wrap.flex-sm-nowrap{ flex-direction:column; }
    .tp-wrap .me-7{ margin-right:0 !important; margin-bottom:14px !important; }
}

/* ===== Profile-style hero + overview (scoped to .scp), mirrors profile page ===== */
.scp, .scp * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; box-sizing:border-box; }
.scp { font-size:13px; color:#1f2937; }
.scp-hero { background:linear-gradient(135deg,#eef2ff 0%,#eaf2ff 55%,#f5f8ff 100%); border:1px solid #e6ecf5; border-radius:14px; padding:16px 20px; display:flex; flex-wrap:wrap; gap:18px; align-items:center; margin-bottom:14px; position:relative; }
.scp-hero-avatar { width:74px; height:74px; border-radius:50%; overflow:hidden; flex-shrink:0; background:#dbe4f5; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#3b55e6; border:3px solid #fff; box-shadow:0 4px 14px rgba(0,0,0,.08); }
.scp-hero-avatar img { width:100%; height:100%; object-fit:cover; }
.scp-hero-main { flex:1; min-width:220px; }
.scp-name { font-size:14px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:7px; line-height:1.3; }
.scp-name .scp-verif { width:14px; height:14px; color:#2563eb; }
.scp-role { font-size:12px; color:#475569; margin-top:3px; }
.scp-emp-badge { display:inline-flex; align-items:center; gap:6px; margin-top:8px; font-size:11px; font-weight:600; padding:4px 11px; border-radius:30px; }
.scp-emp-badge.on { background:#e8fff3; color:#15803d; }
.scp-emp-badge.off { background:#fff1f3; color:#e11d48; }
.scp-tags { display:flex; flex-wrap:wrap; gap:5px; margin-top:9px; }
.scp-tag { background:#eef2ff; color:#3b5bdb; font-size:10.5px; font-weight:600; padding:2px 9px; border-radius:6px; }
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
.scp-hero-actions { display:flex; flex-direction:column; gap:7px; min-width:160px; align-self:flex-end; }
.scp-btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:8px 14px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#fff; color:#334155; text-decoration:none; transition:all .15s; }
.scp-btn svg { width:14px; height:14px; }
.scp-btn:hover { background:#f1f5f9; color:#334155; }
.scp-btn-primary { background:#3b55e6; color:#fff; border-color:#3b55e6; box-shadow:0 4px 12px rgba(59,85,230,.25); }
.scp-btn-primary:hover { background:#2f46c4; color:#fff; }
.scp-hero-close { position:absolute; top:12px; right:14px; z-index:3; width:30px; height:30px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; color:#64748b; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; padding:0; }
.scp-hero-close:hover { background:#fdecee; color:#e11d48; }
.scp-hero-close svg { width:15px; height:15px; }
.scp-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:14px; }
.scp-stat { background:#fff; border:1px solid #eef2f7; border-radius:12px; padding:13px 14px; display:flex; align-items:center; gap:12px; box-shadow:0 1px 3px rgba(0,0,0,.04); }
.scp-stat-ic { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.scp-stat-ic svg { width:19px; height:19px; }
.scp .ic-blue { background:#eef2ff; color:#3b55e6; } .scp .ic-green { background:#e8fff3; color:#16a34a; } .scp .ic-amber { background:#fff8e6; color:#d97706; } .scp .ic-purple { background:#f5efff; color:#7c3aed; } .scp .ic-rose { background:#fff1f3; color:#e11d48; }
.scp-stat-lbl { font-size:11.5px; color:#64748b; font-weight:500; }
.scp-stat-num { font-size:14px; font-weight:800; color:#0f172a; line-height:1.2; }
.scp-stat-cap { font-size:10px; color:#94a3b8; font-weight:500; margin-top:2px; }
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
.scp-row .v { font-size:12.5px; font-weight:600; color:#1f2937; text-align:right; word-break:break-word; }
.scp-row .v a { color:#3b55e6; text-decoration:none; }
.scp-pill { display:inline-flex; align-items:center; gap:4px; background:#e8fff3; color:#15803d; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; }
.scp-pill.off { background:#fff1f3; color:#e11d48; }
@media (max-width:1200px){ .scp-stats { grid-template-columns:repeat(3,1fr); } .scp-grid3 { grid-template-columns:1fr 1fr; } }
@media (max-width:768px){ .scp-stats { grid-template-columns:1fr 1fr; } .scp-grid3 { grid-template-columns:1fr; } .scp-pc-list { grid-template-columns:1fr; } .scp-hero-actions { min-width:100%; } }
  </style>
    <link href="{{ asset('demo/css/profile-lists.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <div id="kt_activities_scroll" class="tp-wrap position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::User Details-->
    @php
      $scp_pc = [
        'Basic Information'   => (!empty(@$users->name) && !empty(@$users->designation)),
        'Company Details'     => !empty(@$users->company_name),
        'Contact Information'  => (!empty(@$users->email) && !empty(@$users->mobile)),
        'Profile Image'       => (!empty(@$users->image) && strlen(@$users->image)>10),
      ];
      $scp_pct = count($scp_pc) ? round(count(array_filter($scp_pc))/count($scp_pc)*100) : 0;
      $scp_active = isset($counts) ? (int) $counts->get('active',0) : 0;
      $scp_cand   = (int) (@$total_candidates ?? 0);
      $scp_intv   = (int) (@$interviewsTrackerTotal ?? 0);
      $scp_join   = (int) (@$total_candidates_join ?? 0);
      $scp_rank   = '-';
      $__rmonths  = collect(@$rankings['months'] ?? [])->values();
      $__rkey     = $__rmonths->get(0); // current month — matches the My Rank tab's current internal rank
      if($__rkey !== null){ $scp_rank = @$rankings['internalRanks'][$__rkey]['rank'] ?? '-'; }
      $scp_isActive = (strtolower((string)@$users->status) === 'active');
    @endphp
    <div class="scp px-2 pt-2">
      <div class="scp-hero">
        <button type="button" class="scp-hero-close" id="kt_activitiesClientTeam_close" title="Close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <div class="scp-hero-avatar">
          @if(!empty($userImage))<img src="{{ str_replace('index.php','',@$userImage) }}" alt="">@else{{ strtoupper(substr(@$users->name,0,1)) }}@endif
        </div>
        <div class="scp-hero-main">
          <div class="scp-name">{{ ucwords(@$users->name) }}
            <svg class="scp-verif" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 1.8 3 .1 1 2.8 2.4 1.7-.9 2.9.9 2.9-2.4 1.7-1 2.8-3 .1L12 22l-2.4-1.8-3-.1-1-2.8L3.2 15.6l.9-2.9-.9-2.9 2.4-1.7 1-2.8 3-.1L12 2z"/><path d="M10.6 14.6l-2.2-2.2-1.1 1.1 3.3 3.3 5.6-5.6-1.1-1.1z" fill="#fff"/></svg>
          </div>
          <div class="scp-role">{{ ucwords(@$users->designation) }}@if(!empty(@$users->company_name)) · {{ ucwords(@$users->company_name) }}@endif</div>
          <span class="scp-emp-badge {{ $scp_isActive ? 'on' : 'off' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="9"/></svg>
            {{ $scp_isActive ? 'Active' : 'Inactive' }}
          </span>
          @if(!empty(@$users->skills))
            <div class="scp-tags">
              @foreach(array_slice(explode(',', $users->skills),0,6) as $val)<span class="scp-tag">{{ trim($val) }}</span>@endforeach
            </div>
          @endif
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
          <a href="{{ route('service_company.team.edit', [$userId]) }}" class="scp-btn scp-btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4z"/></svg> Edit Profile</a>
        </div>
      </div>
      <div class="scp-stats">
        <div class="scp-stat"><div class="scp-stat-ic ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></div><div><div class="scp-stat-lbl">Active Jobs</div><div class="scp-stat-num">{{ $scp_active }}</div><div class="scp-stat-cap">Currently active</div></div></div>
        <div class="scp-stat"><div class="scp-stat-ic ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg></div><div><div class="scp-stat-lbl">Candidates Added</div><div class="scp-stat-num">{{ number_format($scp_cand) }}</div><div class="scp-stat-cap">Overall count</div></div></div>
        <div class="scp-stat"><div class="scp-stat-ic ic-amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><div><div class="scp-stat-lbl">Interviews</div><div class="scp-stat-num">{{ number_format($scp_intv) }}</div><div class="scp-stat-cap">Overall count</div></div></div>
        <div class="scp-stat"><div class="scp-stat-ic ic-purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 11l2 2 4-4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div><div><div class="scp-stat-lbl">Joinings</div><div class="scp-stat-num">{{ number_format($scp_join) }}</div><div class="scp-stat-cap">Overall count</div></div></div>
        <div class="scp-stat"><div class="scp-stat-ic ic-rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.5 13l1.5 8-5-3-5 3 1.5-8"/></svg></div><div><div class="scp-stat-lbl">Rank</div><div class="scp-stat-num">#{{ $scp_rank }}</div><div class="scp-stat-cap">Internal rank</div></div></div>
      </div>
    </div>
    <!--end::User Details-->
    <!--begin::Tabs-->
    <div class="card card-custom mx-2">
    <!--Begin::Tabs Header-->
    <div class="card-header card-header-tabs-line">
  <div class="card-toolbar">
        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
      <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#user_overview"> <span class="nav-text font-weight-bold">Overview</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_requisitions"> <span class="nav-text font-weight-bold">Jobs</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_candidate"> <span class="nav-text font-weight-bold">Candidate</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_interview_tracker"> <span class="nav-text font-weight-bold">Interview Tracker</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_joining"> <span class="nav-text font-weight-bold">Joinings</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#myRank"> <span class="nav-text font-weight-bold">My Rank</span> </a> </li>
       <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#candidateDashboard"> <span class="nav-text font-weight-bold">My Dashboard</span> </a> </li>
      <li class="nav-item me-3 d-none"> <a class="nav-link" data-toggle="tab" href="#user_settings"> <span class="nav-text font-weight-bold">Settings</span> </a> </li>
    </ul>
      </div>
</div>
    <!--end::Tabs Header-->
    <!--Begin::Tabs Body-->
    <div class="card-body py-0">
    <div class="tab-content pt-5">
	
  <!--begin::Overview Content-->
  <div class="tab-pane active show" id="user_overview" role="tabpanel">
        <div class="scp py-5">
          @php $scp_edit = route('service_company.team.edit', [$userId]); @endphp
          <div class="scp-grid3">
            <!-- Personal Information -->
            <div class="scp-card">
              <div class="scp-card-head">
                <div class="scp-card-title"><span class="ci ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>Personal Information</div>
                <a href="{{ $scp_edit }}" class="scp-edit">Edit</a>
              </div>
              <div class="scp-row"><span class="k">Full Name</span><span class="v">{{ ucwords(@$users->name) }}</span></div>
              <div class="scp-row"><span class="k">User ID</span><span class="v">C-{{ @$users->id }}</span></div>
              <div class="scp-row"><span class="k">Designation</span><span class="v">{{ ucwords(@$users->designation) }}</span></div>
              <div class="scp-row"><span class="k">Email</span><span class="v">{{ @$users->email }}</span></div>
              <div class="scp-row"><span class="k">Phone</span><span class="v" style="display:flex;align-items:center;gap:4px;justify-content:flex-end;"><span id="firstPhoneDisplay" style="display:flex;"></span>{{ @$users->mobile }}</span></div>
              @if(!empty(@$users->skills))
                <div class="scp-row"><span class="k">Skills</span><span class="v" style="display:flex;flex-wrap:wrap;gap:4px;justify-content:flex-end;">@foreach(array_slice(explode(',', $users->skills),0,8) as $val)<span class="scp-tag">{{ trim($val) }}</span>@endforeach</span></div>
              @endif
            </div>
            <!-- Company Information -->
            <div class="scp-card">
              <div class="scp-card-head">
                <div class="scp-card-title"><span class="ci ic-purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M8 10h.01M16 10h.01"/></svg></span>Company Information</div>
                <a href="{{ $scp_edit }}" class="scp-edit">Edit</a>
              </div>
              <div class="scp-row"><span class="k">Company Name</span><span class="v">{{ ucwords(@$users->company_name) }}</span></div>
              <div class="scp-row"><span class="k">Website</span><span class="v">@if(!empty(@$users->website))<a href="{{ (strpos($users->website,'http')===0?'':'//').$users->website }}" target="_blank">{{ $users->website }}</a>@else—@endif</span></div>
              <div class="scp-row"><span class="k">Country</span><span class="v text-uppercase">{{ ucwords(@$countryName) }}</span></div>
              <div class="scp-row"><span class="k">State</span><span class="v">{{ ucwords(@$stateName) }}</span></div>
              <div class="scp-row"><span class="k">City</span><span class="v">{{ ucwords(@$users->city) }}</span></div>
            </div>
            <!-- Account & Activity -->
            <div class="scp-card">
              <div class="scp-card-head">
                <div class="scp-card-title"><span class="ci ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>Account &amp; Activity</div>
              </div>
              <div class="scp-row"><span class="k">Status</span><span class="v"><span class="scp-pill {{ $scp_isActive ? '' : 'off' }}">{{ $scp_isActive ? 'Active' : 'Inactive' }}</span></span></div>
              <div class="scp-row"><span class="k">Active Jobs</span><span class="v">{{ $scp_active }}</span></div>
              <div class="scp-row"><span class="k">Candidates Added</span><span class="v">{{ number_format($scp_cand) }}</span></div>
              <div class="scp-row"><span class="k">Interviews</span><span class="v">{{ number_format($scp_intv) }}</span></div>
              <div class="scp-row"><span class="k">Joinings</span><span class="v">{{ number_format($scp_join) }}</span></div>
            </div>
          </div>
        </div>
    </div>
  <!--end::overview Content-->
  
  <!--begin::Requisitions Content-->
  <div class="tab-pane" id="user_requisitions" role="tabpanel">
        {{-- <div class="card-header cursor-pointer px-0">
          <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Jobs</h3>
              </div>
        </div> --}}
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
          </div>
     <div class="loadingMessage loadingMessageteamJobsListajax" style="display: none; position: absolute;">Processing...</div>
      <div class="jobx">
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
      </div>
    </div>
  </div>
  <!--end::Requisitions Content-->
  
  <!--begin::Candidate Content-->
  <div class="tab-pane" id="user_candidate" role="tabpanel">
      {{-- <div class="card-header cursor-pointer px-0">
        <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Candidate</h3>
        </div>
      </div> --}}
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



          <div class="loadingMessage loadingMessageteamCandidatesListajax" style="display: none; position: absolute;">Processing...</div>
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="load_application">
      <thead>
            <tr class="fw-bolder text-muted">
          {{-- <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
              <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
            </div></th> --}}
          <th class="">Name</th>
          {{-- <th class="">Contacts</th> --}}
          <th class="">Exp.</th>
          <th class="">Salary</th>
          <th class="">Notice</th>
          <th class="">Upload</th>
          <th class="">Location</th>
          {{-- <th class="">Source</th> --}}
          <th class="">Jobs</th>
          <th class="">Job Status</th>
          <th class="">Stage</th>
        </tr>
          </thead>
          <tbody id="teamCandidatesListajax"> </tbody>
    </table>
    <div id="paginationTeamtCandidates"></div>
      </div>
  </div>
  <!--end::Candidate Content-->
	
	
  <!--begin::Interview Tracker Content-->
  <div class="tab-pane" id="user_interview_tracker" role="tabpanel">
    {{-- <div class="card-header cursor-pointer px-0">
      <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Interview Tracker</h3>
      </div> 
    </div> --}}
    <div class="card-body px-0 py-7">   

      <div class="it-wrap">
        <div class="it-filters-bar">
          <div class="it-search-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="interviewtrackerinputJobSearch" value="" placeholder="Search candidate, job, recruiter..." id="interviewtracker_kt_tagify_2" autocomplete="off" />
            <div id="loader_search" class="loader_search hidden_search" style="position:absolute; top:9px; right:10px;"></div>
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
      </div>


      <div class="loadingMessage loadingMessageteamCandidatesListajax" style="display: none; position: absolute;">Processing...</div>
          <div class="it-wrap">
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
        </div>
  </div>
  <!--end::Interview Tracker  Content-->
	
	
  <!--begin::Joinings Content-->
  <div class="tab-pane" id="user_joining" role="tabpanel">
    {{-- <div class="card-header cursor-pointer px-0">
      <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Joinings</h3>
      </div>
    </div> --}}
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
                                    <option value="{{ $val_co->id }}">{{ $val_co->country_name ?? $val_co->name ?? '' }}</option>
                                    @endforeach
                                @endif

                                
                            </select>
                          </div>
                    </div>


      <div class="loadingMessage loadingMessageteamjoiningJobsListajax" style="display: none; position: absolute;">Processing...</div>
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
  </div>
  <!--end::Joinings Content-->

        <!--begin::my rank Content-->
        <div class="tab-pane" id="myRank" role="tabpanel">
          <div class="loadingMessage loadingMessageteamRankajax" style="display: none; position: absolute;">Processing...</div>
          <div id="teamRankContentAjax"></div>
        </div>
        <!--end begin::my rank Content-->

    

  <!--begin::candidateDashboard Content-->
        <div class="tab-pane" id="candidateDashboard" role="tabpanel">
            <div id="teamDashboardContentAjax">
                <div class="text-center py-10" id="teamDashboardSpinner">
                    <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="sr-only">Loading Dashboard...</span>
                    </div>
                    <div class="text-muted mt-3 font-weight-bold">Loading dashboard metrics & performance trends...</div>
                </div>
            </div>
        </div>
        <!--end::candidateDashboard Content-->

  <!--end begin::candidateDashboard Content-->





    <!--begin::Settings Content-->
    <div class="tab-pane" id="user_settings" role="tabpanel">
  <form  id="setting_content1" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
        
        <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0">
     
      <div class="card-body px-0 pt-0 pb-0">
            
            <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
          
          <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_signin_method">
                <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Sign-in Method</h3>
            </div>
              </div>
          
          <div id="kt_account_signin_method" class="collapse show">
                
                <div class="card-body border-top py-8 px-2">
              
              <div class="d-flex flex-wrap align-items-center">
                   
                    <div id="kt_signin_email">
                  <div class="fs-6 fw-bolder mb-1">Email Address</div>
                  <div class="fw-bold text-gray-600"><?php echo @$users->email; ?></div>
                </div>
                    
                    <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                  <div class="row mb-6">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                      <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label for="emailaddress" class="form-label fs-6 fw-bolder mb-3">Enter New Email Address</label>
                            <input type="email" class="form-control form-control-lg form-control-solid" id="emailaddress" placeholder="Email Address" name="emailaddress" value="<?php echo @$users->email; ?>">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                          </div>
                    </div>
                        <div class="col-lg-6">
                      <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label for="confirmemailpassword" class="form-label fs-6 fw-bolder mb-3">Confirm Password</label>
                            <input type="password" class="form-control form-control-lg form-control-solid" name="confirmemailpassword" id="confirmemailpassword">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                          </div>
                    </div>
                      </div>
                  <div class="d-flex">
                        <button id="kt_signin_submit" type="button" class="btn btn-primary me-2 px-6">Update Email</button>
                        <button id="kt_signin_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                      </div>
                </div>
                    
                  </div>
             
              <div class="separator separator-dashed my-6"></div>
              
              <div class="d-flex flex-wrap align-items-center mb-10">
                    
                    <div id="kt_signin_password">
                  <div class="fs-6 fw-bolder mb-1">Password</div>
                  <div class="fw-bold text-gray-600">************</div>
                </div>
                    
                    <div id="kt_signin_password_button" class="ms-auto"> <a href="javascript:void(0);" class="btn btn-light btn-active-light-primary reset_password" data-toggle="modal" data-target="#reset_password">Reset Password</a> </div>
                    
                  </div>
             
            </div>
               
              </div>
          
        </div>
            
            <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0 d-none">
          
          <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_connected_accounts" aria-expanded="true" aria-controls="kt_account_connected_accounts">
                <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Connected Accounts</h3>
            </div>
              </div>
         
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
                  <div class="d-flex justify-content-end">
                        <div class="form-check form-check-solid form-switch">
                      <input class="form-check-input w-45px h-30px" type="checkbox" id="googleswitch" @if($users->google_status == 1) {{ 'checked="checked"' }} @endif  disabled="disabled">
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
                  <div class="d-flex justify-content-end">
                        <div class="form-check form-check-solid form-switch">
                      <input class="form-check-input w-45px h-30px" type="checkbox" id="githubswitch" @if($users->zoom_status == 1) {{ 'checked="checked"' }} @endif disabled="disabled">
                      <label class="form-check-label" for="githubswitch"></label>
                    </div>
                      </div>
                </div>
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
                      <input class="form-check-input w-45px h-30px" type="checkbox" id="slackswitch" @if($users->microsoft_status == 1)  {{ 'checked="checked"' }} @endif  disabled="disabled" />
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
          </div>
      <!--end::Main body content-->
    </div>
        <!--end::Main body div-->
      </form>
  <!--end::Form-->
</div>
    <!--end::Settings Content-->
    <input type="hidden" name="userId" id="userId" value="{{ @$userId }}" />
{{-- @push('footer-script') --}}
    <script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>



    <script>
      
            var myChartId = "<?php echo @$users->id; ?>";

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
                        data: ['{{ $active_count ?? 0 }}', '{{ $hold_count ?? 0 }}', '{{ $inactive_count ?? 0 }}'], // Values for the chart

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


      $('#myTable_jmyRank').DataTable({
              pageLength: 10,          // Default number of rows per page
              lengthChange: false,     // 🔒 disables the lengthMenu dropdown
              ordering: false,
              paging: true,
              searching: false,
              autoWidth: false, // 👈 Disable auto column width calculation
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


   

/**
 * Debounce helper for input search fields.
 * Prevents firing rapid back-to-back XHR queries on every keystroke (300ms delay).
 */
function debounce(func, wait) {
    var timeout;
    wait = wait || 300;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            func.apply(context, args);
        }, wait);
    };
}

window.teamActiveXHRs = {
    jobs: null,
    candidate: null,
    joining: null,
    interview: null,
    rank: null,
    dashboard: null
};

function cancelInFlightXHR(key) {
    if (window.teamActiveXHRs[key] && window.teamActiveXHRs[key].readyState !== 4) {
        window.teamActiveXHRs[key].abort();
    }
}

window.destroyAllTeamCharts = function() {
    if (window.teamCharts) {
        Object.keys(window.teamCharts).forEach(function(key) {
            if (window.teamCharts[key] && typeof window.teamCharts[key].destroy === 'function') {
                try { window.teamCharts[key].destroy(); } catch(e) {}
                window.teamCharts[key] = null;
            }
        });
    }
};

window.TeamViewMoreTabManager = {
    tabs: {
        'user_overview': { loaded: true, loading: false },
        'user_requisitions': { loaded: false, loading: false },
        'user_candidate': { loaded: false, loading: false },
        'user_interview_tracker': { loaded: false, loading: false },
        'user_joining': { loaded: false, loading: false },
        'myRank': { loaded: false, loading: false },
        'candidateDashboard': { loaded: false, loading: false }
    },
    init: function() {
        var self = this;
        $(document).on('shown.bs.tab', 'a[data-toggle="tab"], a[data-bs-toggle="tab"]', function(e) {
            var href = $(this).attr('href');
            if (!href) return;
            var target = href.replace('#', '');
            self.loadTabIfNeeded(target);
        });
    },

    loadTabIfNeeded: function(tabKey) {
        if (!this.tabs[tabKey] || this.tabs[tabKey].loaded || this.tabs[tabKey].loading) {
            return;
        }

        this.setLoading(tabKey, true);
        switch (tabKey) {
            case 'user_requisitions':
                fetchTeamJobs();
                break;
            case 'user_candidate':
                fetchTeamCandidate();
                break;
            case 'user_interview_tracker':
                fetchTeamCandidateInterviewTracker();
                break;
            case 'user_joining':
                fetchTeamCandidateJoining();
                break;
            case 'myRank':
                if (typeof fetchTeamRankData === 'function') { fetchTeamRankData(); }
                break;
            case 'candidateDashboard':
                if (typeof fetchTeamDashboardData === 'function') { fetchTeamDashboardData(); }
                break;
        }
    },
    markLoaded: function(tabKey) {
        if (this.tabs[tabKey]) {
            this.tabs[tabKey].loaded = true;
            this.tabs[tabKey].loading = false;
        }
    },
    setLoading: function(tabKey, isLoading) {
        if (this.tabs[tabKey]) {
            this.tabs[tabKey].loading = !!isLoading;
        }
    }
};

$(document).ready(function() {
    TeamViewMoreTabManager.init();
    var activeTab = $('a[data-toggle="tab"].active, a[data-bs-toggle="tab"].active').attr('href');
    if (activeTab) {
        TeamViewMoreTabManager.loadTabIfNeeded(activeTab.replace('#', ''));
    }


  $(document).on('click', '#paginationTeamtjobs .page-link, #paginationTeamtjobs .pagi-page, #paginationTeamtjobs .pagi-arrow, #paginationTeamtjobs .team-page-link', function(e) {
          e.preventDefault();

          const page = $(this).data('page');
          if (!page || $(this).hasClass('disabled') || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active')) return;

          // Disable pagination links during fetch to prevent rapid duplicate clicks
          $('#paginationTeamtjobs button, #paginationTeamtjobs .page-link, #paginationTeamtjobs .team-page-link').addClass('disabled pagi-disabled');

          // Fetch data and re-enable links on completion
          var promise = fetchTeamJobs(page, $('#search').val());
          if (promise && typeof promise.always === 'function') {
              promise.always(function() {
                  $('#paginationTeamtjobs button, #paginationTeamtjobs .page-link, #paginationTeamtjobs .team-page-link').removeClass('disabled pagi-disabled');
              });
          } else {
              $('#paginationTeamtjobs button, #paginationTeamtjobs .page-link, #paginationTeamtjobs .team-page-link').removeClass('disabled pagi-disabled');
          }
  });

  $(document).on('click', '#paginationTeamtCandidates .page-link, #paginationTeamtCandidates .pagi-page, #paginationTeamtCandidates .pagi-arrow, #paginationTeamtCandidates .team-page-link', function(e) {
          e.preventDefault();

          const page = $(this).data('page');
          if (!page || $(this).hasClass('disabled') || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active')) return;

          // Disable pagination links during fetch to prevent rapid duplicate clicks
          $('#paginationTeamtCandidates button, #paginationTeamtCandidates .page-link, #paginationTeamtCandidates .team-page-link').addClass('disabled pagi-disabled');

          // Fetch data and re-enable links on completion
          var promise = fetchTeamCandidate(page, $('#search').val());
          if (promise && typeof promise.always === 'function') {
              promise.always(function() {
                  $('#paginationTeamtCandidates button, #paginationTeamtCandidates .page-link, #paginationTeamtCandidates .team-page-link').removeClass('disabled pagi-disabled');
              });
          } else {
              $('#paginationTeamtCandidates button, #paginationTeamtCandidates .page-link, #paginationTeamtCandidates .team-page-link').removeClass('disabled pagi-disabled');
          }
  });

  $(document).on('click', '#paginationteamjoiningjobs .page-link, #paginationteamjoiningjobs .pagi-page, #paginationteamjoiningjobs .pagi-arrow, #paginationteamjoiningjobs .team-page-link', function(e) {
          e.preventDefault();

          const page = $(this).data('page');
          if (!page || $(this).hasClass('disabled') || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active')) return;

          // Disable pagination links during fetch to prevent rapid duplicate clicks
          $('#paginationteamjoiningjobs button, #paginationteamjoiningjobs .page-link, #paginationteamjoiningjobs .team-page-link').addClass('disabled pagi-disabled');

          // Fetch data and re-enable links on completion
          var promise = fetchTeamCandidateJoining(page, $('#search').val());
          if (promise && typeof promise.always === 'function') {
              promise.always(function() {
                  $('#paginationteamjoiningjobs button, #paginationteamjoiningjobs .page-link, #paginationteamjoiningjobs .team-page-link').removeClass('disabled pagi-disabled');
              });
          } else {
              $('#paginationteamjoiningjobs button, #paginationteamjoiningjobs .page-link, #paginationteamjoiningjobs .team-page-link').removeClass('disabled pagi-disabled');
          }
  });

  $(document).on('click', '#paginationteaminterviewjobs .page-link, #paginationteaminterviewjobs .pagi-page, #paginationteaminterviewjobs .pagi-arrow, #paginationteaminterviewjobs .team-page-link', function(e) {
          e.preventDefault();

          const page = $(this).data('page');
          if (!page || $(this).hasClass('disabled') || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active')) return;

          // Disable pagination links during fetch to prevent rapid duplicate clicks
          $('#paginationteaminterviewjobs button, #paginationteaminterviewjobs .page-link, #paginationteaminterviewjobs .team-page-link').addClass('disabled pagi-disabled');

          // Fetch data and re-enable links on completion
          var promise = fetchTeamCandidateInterviewTracker(page, $('#search').val());
          if (promise && typeof promise.always === 'function') {
              promise.always(function() {
                  $('#paginationteaminterviewjobs button, #paginationteaminterviewjobs .page-link, #paginationteaminterviewjobs .team-page-link').removeClass('disabled pagi-disabled');
              });
          } else {
              $('#paginationteaminterviewjobs button, #paginationteaminterviewjobs .page-link, #paginationteaminterviewjobs .team-page-link').removeClass('disabled pagi-disabled');
          }
  });

  var searchEl = document.getElementById('kt_tagify_search');
  if (searchEl) {
      searchEl.addEventListener('keyup', debounce(function() {
          fetchTeamJobs();
      }, 300));
  }

});

window.safeInjectHTML = function(selector, htmlString) {
    if (window.requestAnimationFrame) {
        window.requestAnimationFrame(function() {
            var $target = typeof selector === 'string' ? $(selector) : selector;
            if ($target && $target.length) {
                $target.html(htmlString);
            }
        });
    } else {
        var $target = typeof selector === 'string' ? $(selector) : selector;
        if ($target && $target.length) {
            $target.html(htmlString);
        }
    }
};

window.renderTeamTabError = function(containerSelector, retryFnName, message) {
    var msg = message || 'Failed to load tab data. Please check your network connection and try again.';
    var errorHtml = `
        <div class="alert alert-custom alert-light-danger fade show m-5 p-5 shadow-sm rounded" role="alert">
            <div class="alert-icon"><i class="fa-solid fa-triangle-exclamation text-danger fs-3"></i></div>
            <div class="alert-text font-weight-bolder text-dark ml-2">
                ${msg}
            </div>
            <div class="alert-close ml-4">
                <button type="button" class="btn btn-sm btn-outline-danger font-weight-bolder px-4" onclick="${retryFnName}()">
                    <i class="fa-solid fa-rotate-right mr-1"></i> Retry
                </button>
            </div>
        </div>
    `;
    safeInjectHTML(containerSelector, errorHtml);
};

function fetchTeamJobs(page = 1, search = '') {
    cancelInFlightXHR('jobs');
    var userId = '{{ @$userId }}';
    $('.loadingMessageteamJobsListajax').css({ display: 'block' });
    return window.teamActiveXHRs['jobs'] = $.ajax({
        url: `{!! route('service_company.team.jobdata_ajax') !!}?page=${page}`,
        type: 'GET',
        data: {
            userId: userId,
            search: $('#inputSearch').val(),
            custom_search: $('#kt_tagify_search').val(),
            custom_status: $('#statusjb').val(),
            custom_hiringtype: $('#hiringtypejob').val(),
            profile: true
        },
        success: function(response) {
            $('.loadingMessage').css({ display: 'none' });
            safeInjectHTML('#teamJobsListajax', response.html);
            safeInjectHTML('#paginationTeamtjobs', response.pagination);
            TeamViewMoreTabManager.markLoaded('user_requisitions');
        },
        error: function(xhr, status, error) {
            if (status === 'abort') return;
            $('.loadingMessage').css({ display: 'none' });
            TeamViewMoreTabManager.setLoading('user_requisitions', false);
            renderTeamTabError('#teamJobsListajax', 'fetchTeamJobs');
        }
    });
}

function fetchTeamCandidate(page = 1, search = '') {
    cancelInFlightXHR('candidate');
    var userId = '{{ @$userId }}';
    $('.loadingMessageteamCandidatesListajax').css({ display: 'block' });
    return window.teamActiveXHRs['candidate'] = $.ajax({
        url: `{!! route('service_company.team.appdata_ajax') !!}?page=${page}`,
        type: 'GET',
        data: {
            userId: userId,
            cus_search: $('#candidate_kt_tagify_2').val(),
            sortingfilter: $('#candidatesortingfilter').val(),
            app_int_status: $('#stagefiltercandidate').val(),
        },
        success: function(response) {
            $('.loadingMessage').css({ display: 'none' });
            safeInjectHTML('#teamCandidatesListajax', response.html);
            safeInjectHTML('#paginationTeamtCandidates', response.pagination);
            TeamViewMoreTabManager.markLoaded('user_candidate');
        },
        error: function(xhr, status, error) {
            if (status === 'abort') return;
            $('.loadingMessage').css({ display: 'none' });
            TeamViewMoreTabManager.setLoading('user_candidate', false);
            renderTeamTabError('#teamCandidatesListajax', 'fetchTeamCandidate');
        }
    });
}

function fetchTeamCandidateJoining(page = 1, search = '') {
    cancelInFlightXHR('joining');
    var userId = '{{ @$userId }}';
    var sortingfilter = $('#joinsortingfilter').val();
    var country = $('#country').val();
    var cus_search = $('#join_kt_tagify_2').val();
    $('.loadingMessageteamjoiningJobsListajax').css({ display: 'block' });
    return window.teamActiveXHRs['joining'] = $.ajax({
        url: `{!! route('service_company.team.joiningdata_ajax') !!}?page=${page}`,
        type: 'GET',
        data: {
            userId: userId,
            sortingfilter: sortingfilter,
            country: country,
            cus_search: cus_search
        },
        success: function(response) {
            $('.loadingMessage').css({ display: 'none' });
            safeInjectHTML('#teamjoiningJobsListajax', response.html);
            safeInjectHTML('#paginationteamjoiningjobs', response.pagination);
            TeamViewMoreTabManager.markLoaded('user_joining');
        },
        error: function(xhr, status, error) {
            if (status === 'abort') return;
            $('.loadingMessage').css({ display: 'none' });
            TeamViewMoreTabManager.setLoading('user_joining', false);
            renderTeamTabError('#teamjoiningJobsListajax', 'fetchTeamCandidateJoining');
        }
    });
}

function fetchTeamCandidateInterviewTracker(page = 1, search = '') {
    cancelInFlightXHR('interview');
    var userId = '{{ @$userId }}';
    $('.loadingMessageteaminterviewJobsListajax').css({ display: 'block' });
    return window.teamActiveXHRs['interview'] = $.ajax({
        url: `{!! route('service_company.team.intdata_ajax') !!}?page=${page}`,
        type: 'GET',
        data: {
            userId: userId,
            custom_hiringtype: $('#interviewfiltersdataget').val(),
            custom_search: $('#interviewtracker_kt_tagify_2').val()
        },
        success: function(response) {
            $('.loadingMessage').css({ display: 'none' });
            safeInjectHTML('#teaminterviewJobsListajax', response.html);
            safeInjectHTML('#paginationteaminterviewjobs', response.pagination);
            TeamViewMoreTabManager.markLoaded('user_interview_tracker');
        },
        error: function(xhr, status, error) {
            if (status === 'abort') return;
            $('.loadingMessage').css({ display: 'none' });
            TeamViewMoreTabManager.setLoading('user_interview_tracker', false);
            renderTeamTabError('#teaminterviewJobsListajax', 'fetchTeamCandidateInterviewTracker');
        }
    });
}

function fetchTeamRankData() {
    cancelInFlightXHR('rank');
    if (window.teamCharts && window.teamCharts.rankTrend) {
        try { window.teamCharts.rankTrend.destroy(); } catch(e) {}
        window.teamCharts.rankTrend = null;
    }
    var userId = '{{ @$userId }}';
    $('.loadingMessageteamRankajax').css({ display: 'block' });
    return window.teamActiveXHRs['rank'] = $.ajax({
        url: `{!! route('service_company.team.rankdata_ajax') !!}`,
        type: 'GET',
        data: { userId: userId },
        success: function(response) {
            $('.loadingMessage').css({ display: 'none' });
            safeInjectHTML('#teamRankContentAjax', response.html);
            TeamViewMoreTabManager.markLoaded('myRank');
        },
        error: function(xhr, status, error) {
            if (status === 'abort') return;
            $('.loadingMessage').css({ display: 'none' });
            TeamViewMoreTabManager.setLoading('myRank', false);
            renderTeamTabError('#teamRankContentAjax', 'fetchTeamRankData');
        }
    });
}

function fetchTeamDashboardData() {
    cancelInFlightXHR('dashboard');
    if (window.teamCharts) {
        ['sdbOffer', 'sdbJoin', 'sdbInterview', 'sdbCandidates'].forEach(function(k) {
            if (window.teamCharts[k] && typeof window.teamCharts[k].destroy === 'function') {
                try { window.teamCharts[k].destroy(); } catch(e) {}
                window.teamCharts[k] = null;
            }
        });
    }
    var userId = '{{ @$userId }}';
    safeInjectHTML('#teamDashboardContentAjax', `
        <div class="text-center py-10" id="teamDashboardSpinner">
            <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                <span class="sr-only">Loading Dashboard...</span>
            </div>
            <div class="text-muted mt-3 font-weight-bold">Loading dashboard metrics & performance trends...</div>
        </div>
    `);
    return window.teamActiveXHRs['dashboard'] = $.ajax({
        url: `{!! route('service_company.team.dashboarddata_ajax') !!}`,
        type: 'GET',
        data: { userId: userId },
        success: function(response) {
            if (response && response.html) {
                safeInjectHTML('#teamDashboardContentAjax', response.html);
                TeamViewMoreTabManager.markLoaded('candidateDashboard');
            } else {
                TeamViewMoreTabManager.setLoading('candidateDashboard', false);
                renderTeamTabError('#teamDashboardContentAjax', 'fetchTeamDashboardData', 'Failed to load dashboard data.');
            }
        },
        error: function(xhr, status, error) {
            if (status === 'abort') return;
            TeamViewMoreTabManager.setLoading('candidateDashboard', false);
            renderTeamTabError('#teamDashboardContentAjax', 'fetchTeamDashboardData');
        }
    });
}

function filtersdatagetcandidates() {
     fetchTeamCandidate();
}
var candSearchEl = document.getElementById('candidate_kt_tagify_2');
if (candSearchEl) {
    candSearchEl.addEventListener('keyup', debounce(function() {
        fetchTeamCandidate();
    }, 300));
}

function interviewfiltersdataget() {
     fetchTeamCandidateInterviewTracker();
}
var intvSearchEl = document.getElementById('interviewtracker_kt_tagify_2');
if (intvSearchEl) {
    intvSearchEl.addEventListener('keyup', debounce(function() {
        fetchTeamCandidateInterviewTracker();
    }, 300));
}

function filtersdatagetjoin() {
     fetchTeamCandidateJoining();
}
var joinSearchEl = document.getElementById('join_kt_tagify_2');
if (joinSearchEl) {
    joinSearchEl.addEventListener('keyup', debounce(function() {
        fetchTeamCandidateJoining();
    }, 300));
}


   
   	
</script>
{{-- @endpush --}}
    
