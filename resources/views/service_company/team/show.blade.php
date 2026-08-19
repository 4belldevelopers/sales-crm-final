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
      <li class="nav-item me-3"  onclick="fetchTeamJobs()"> <a class="nav-link" data-toggle="tab" href="#user_requisitions"> <span class="nav-text font-weight-bold">Jobs</span> </a> </li>
      <li class="nav-item me-3"  onclick="fetchTeamCandidate()"> <a class="nav-link" data-toggle="tab" href="#user_candidate"> <span class="nav-text font-weight-bold">Candidate</span> </a> </li>
      <li class="nav-item me-3"  onclick="fetchTeamCandidateInterviewTracker()"> <a class="nav-link" data-toggle="tab" href="#user_interview_tracker"> <span class="nav-text font-weight-bold">Interview Tracker</span> </a> </li>
      <li class="nav-item me-3"  onclick="fetchTeamCandidateJoining()"> <a class="nav-link" data-toggle="tab" href="#user_joining"> <span class="nav-text font-weight-bold">Joinings</span> </a> </li>
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
                                    <option value="{{ $val_co->id }}">{{ $val_co->country_name }}</option>
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
          @php
            $rkMonths = collect($rankings['months'] ?? [])->values();
            $rkCur    = $rkMonths->get(0);
            $curI  = $rankings['internalRanks'][$rkCur]  ?? ['rank'=>'-','score'=>0];
            // trend (oldest -> newest, last 12)
            $rkTrend  = $rkMonths->slice(0,12)->reverse()->values();
            $rkTLabels = $rkTrend->map(fn($m)=>date('M y', strtotime($m.'-01')))->all();
            $rkTRanks = $rkTrend->map(fn($m)=> (is_numeric($rankings['internalRanks'][$m]['rank'] ?? null) ? (int)$rankings['internalRanks'][$m]['rank'] : null))->all();
            $rkValidRanks = array_filter($rkTRanks, fn($v)=>$v!==null);
            $rkMaxRank = $rkValidRanks ? max($rkValidRanks) : 10;
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
            $lbTop   = collect($leaderboard['top'] ?? []);
            $lbMe    = $leaderboard['me'] ?? null;
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
              <div class="srk-stat"><span class="srk-st-ic ic-ind"><i class="fas fa-users"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Candidates Added</div><div class="srk-st-num">{{ @$total_candidates ?? 0 }}</div></div><span class="srk-spark ind">{!! $rkSpark !!}</span></div>
              <div class="srk-stat"><span class="srk-st-ic ic-grn"><i class="fas fa-calendar-check"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Interviews Conducted</div><div class="srk-st-num">{{ (int) array_sum((array)(@$interviewValuesMonth ?? [])) }}</div></div><span class="srk-spark grn">{!! $rkSpark !!}</span></div>
              <div class="srk-stat"><span class="srk-st-ic ic-vio"><i class="fas fa-briefcase"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Offers Made</div><div class="srk-st-num">{{ @$candidateValuesTotaloffer ?? 0 }}</div></div><span class="srk-spark vio">{!! $rkSpark !!}</span></div>
              <div class="srk-stat"><span class="srk-st-ic ic-amb"><i class="fas fa-handshake"></i></span><div class="srk-st-body"><div class="srk-st-lbl">Joinings</div><div class="srk-st-num">{{ @$candidateValuesTotaljoin ?? 0 }}</div></div><span class="srk-spark amb">{!! $rkSpark !!}</span></div>
            </div>

            <!-- Leaderboard + Trend + Journey -->
            <div class="srk-mid3">
              <!-- Leaderboard -->
              <div class="srk-card">
                <div class="srk-head"><span class="srk-head-t">Leaderboard <small>(Top 5)</small></span></div>
                @php $lbTop5 = $lbTop->take(5); @endphp
                @if($lbTop5->count())
                  <div class="srk-lb">
                    @foreach($lbTop5 as $lrow)
                      @php $isMe = (int)($lrow['id'] ?? 0) === (int)(@$users->id ?? -1); $rk=$lrow['rank']; @endphp
                      <div class="srk-lb-row {{ $isMe?'me':'' }}">
                        <span class="srk-lb-rk {{ $rk==1?'g1':($rk==2?'g2':($rk==3?'g3':'')) }}">{{ $rk<=3 ? '' : $rk }}@if($rk<=3)<i class="fas fa-medal"></i>@endif</span>
                        @if(!empty($lrow['image']))
                          <img class="srk-lb-ava" src="{{ $lrow['image'] }}" alt="">
                        @else
                          <span class="srk-lb-ava srk-lb-ini">{{ strtoupper(substr($lrow['name'] ?? 'U',0,1)) }}</span>
                        @endif
                        <span class="srk-lb-name">{{ $lrow['name'] ?? 'User' }} @if($isMe)<em class="srk-lb-you">You</em>@endif</span>
                        <span class="srk-lb-score">{{ $lrow['score'] ?? 0 }}</span>
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
            })();
          </script>
        </div>
  <!--end begin::my rank Content-->

    

  <!--begin::candidateDashboard Content-->
        <div class="tab-pane" id="candidateDashboard" role="tabpanel">
          @php
            // My Dashboard cards mirror the overall (all-time) header cards, not a date window.
            $sdbActive=(int)($scp_active ?? $counts->get('active',0) ?? 0); $sdbHold=(int)($counts->get('hold',0)); $sdbClosed=(int)($counts->get('inactive',0));
            $sdbAdded=(int)($scp_cand ?? @$total_candidates ?? 0);
            $sdbInReview=(int)(@$inreview_count ?? 0); $sdbSourcing=(int)(@$sourcing_count ?? 0); $sdbScreen=(int)(@$screening_count ?? 0);
            $sdbEval=(int)(@$evaluation_count ?? 0); $sdbNego=(int)(@$negotiation_count ?? 0);
            $sdbOffer=(int)($scpOffersTotal ?? @$offer_count ?? 0);
            $sdbJoined=(int)($scp_join ?? @$joined_count ?? 0); $sdbReject=(int)(@$Rejected_count ?? 0);
            $sdbInterviews=(int)($scp_intv ?? @$interviewsTrackerTotal ?? 0);
            $sdbConv=$sdbAdded>0 ? round($sdbJoined/$sdbAdded*100,1) : 0;
            $sdbWin='';
            $sdbStages=[['In Review',$sdbInReview,'#bdbd26'],['Sourcing',$sdbSourcing,'#000000'],['Screening',$sdbScreen,'#c000ff'],['Evaluation',$sdbEval,'#2896ff'],['Negotiation',$sdbNego,'#ff4098'],['Offer',$sdbOffer,'#ffab00'],['Joined',$sdbJoined,'#06c732'],['Rejected',$sdbReject,'#ff0202']];
          @endphp
          <div class="sdb-wrap">
            <div class="sdb-top">
              <div>
                <div class="sdb-title">My Dashboard <span>&#128075;</span></div>
                <div class="sdb-sub">Here's what's happening with your recruitment.</div>
              </div>
              @if($sdbWin)<span class="sdb-window"><i class="far fa-calendar-alt"></i> {{ $sdbWin }}</span>@endif
            </div>
            <div class="sdb-stats">
              <div class="sdb-stat"><span class="sdb-st-ic ic-ind"><i class="fas fa-briefcase"></i></span><div class="sdb-st-lbl">Active Jobs</div><div class="sdb-st-num">{{ $sdbActive }}</div></div>
              <div class="sdb-stat"><span class="sdb-st-ic ic-grn"><i class="fas fa-users"></i></span><div class="sdb-st-lbl">Candidates Added</div><div class="sdb-st-num">{{ $sdbAdded }}</div></div>
              <div class="sdb-stat"><span class="sdb-st-ic ic-blu"><i class="fas fa-calendar-check"></i></span><div class="sdb-st-lbl">Interviews</div><div class="sdb-st-num">{{ $sdbInterviews }}</div></div>
              <div class="sdb-stat"><span class="sdb-st-ic ic-amb"><i class="fas fa-file-signature"></i></span><div class="sdb-st-lbl">Offers</div><div class="sdb-st-num">{{ $sdbOffer }}</div></div>
              <div class="sdb-stat"><span class="sdb-st-ic ic-vio"><i class="fas fa-handshake"></i></span><div class="sdb-st-lbl">Joinings</div><div class="sdb-st-num">{{ $sdbJoined }}</div></div>
              <div class="sdb-stat"><span class="sdb-st-ic ic-rose"><i class="fas fa-chart-line"></i></span><div class="sdb-st-lbl">Joining Rate</div><div class="sdb-st-num">{{ $sdbConv }}%</div></div>
            </div>
            <div class="sdb-mid">
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
              <div class="sdb-card">
                <div class="sdb-head"><span class="sdb-head-t"><i class="fas fa-bolt sdb-bolt"></i> Action Center</span></div>
                <div class="sdb-ac">
                  <div class="sdb-ac-row"><span class="sdb-ac-ic ic-ind"><i class="fas fa-briefcase"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Active Jobs</div><div class="sdb-ac-s">Jobs currently open for hiring</div></div><span class="sdb-ac-n">{{ $sdbActive }}</span></div>
                  <div class="sdb-ac-row"><span class="sdb-ac-ic ic-amb"><i class="fas fa-file-signature"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Offer Stage</div><div class="sdb-ac-s">Candidates currently at offer stage</div></div><span class="sdb-ac-n">{{ $sdbOffer }}</span></div>
                  <div class="sdb-ac-row"><span class="sdb-ac-ic ic-vio"><i class="fas fa-comments-dollar"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">In Negotiation</div><div class="sdb-ac-s">Candidates in salary negotiation</div></div><span class="sdb-ac-n">{{ $sdbNego }}</span></div>
                  <div class="sdb-ac-row"><span class="sdb-ac-ic ic-grn"><i class="fas fa-handshake"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Joined</div><div class="sdb-ac-s">Candidates who have joined</div></div><span class="sdb-ac-n">{{ $sdbJoined }}</span></div>
                  <div class="sdb-ac-row"><span class="sdb-ac-ic ic-rose"><i class="fas fa-pause-circle"></i></span><div class="sdb-ac-body"><div class="sdb-ac-t">Jobs on Hold</div><div class="sdb-ac-s">Jobs temporarily paused</div></div><span class="sdb-ac-n">{{ $sdbHold }}</span></div>
                </div>
                <a href="#user_requisitions" data-toggle="tab" class="sdb-ac-all">View All Jobs <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
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
                sdbArea(off.getContext('2d'),'#f59e0b',{!! json_encode(@$labelsCommoffer ?? []) !!},{!! json_encode(@$candidateValuesCommoffer ?? []) !!});
                var jn=document.getElementById('sdb_join'); if(jn) sdbArea(jn.getContext('2d'),'#16a34a',{!! json_encode(@$labelsCommjoin ?? []) !!},{!! json_encode(@$candidateValuesCommjoin ?? []) !!});
                var iv=document.getElementById('sdb_interview'); if(iv) sdbArea(iv.getContext('2d'),'#7c4dff',{!! json_encode(@$interviewlabelsMonth ?? []) !!},{!! json_encode(@$interviewValuesMonth ?? []) !!});
                var cn=document.getElementById('sdb_candidates'); if(cn) sdbBar(cn.getContext('2d'),'#2196f3',{!! json_encode(@$labels ?? []) !!},{!! json_encode(@$candidateValues ?? []) !!});
              }
              document.addEventListener('click',function(e){var a=e.target.closest('a[href="#candidateDashboard"]');if(a)setTimeout(drawSdb,60);});
              if(window.jQuery){jQuery(function($){$('a[href="#candidateDashboard"]').on('shown.bs.tab',drawSdb);});}
              setTimeout(drawSdb,400);
            })();
          </script>
          <!-- legacy dashboard markup kept hidden to preserve existing chart bindings -->
          <div class="d-none" aria-hidden="true">
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
                                        id="kt_card_widget_4_chart_<?php echo @$users->id; ?>" 
                                        style="min-width: 170px; min-height: 170px" 
                                        data-kt-size="170" 
                                        data-kt-line="11">
                                        <span></span>
                                        <canvas id="allJobGrapg_<?php echo @$users->id; ?>" height="70" width="70"></canvas>
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
                                  <div id="kt_card_widget_6_chart_<?php echo @$users->id; ?>" class="w-100" style="height: 200px">
                                      <div 
                                          id="kt_card_widget_4_chart_<?php echo @$users->id; ?>" 
                                          style="min-width: 100%; min-height: 200px">
                                          <canvas id="candidates_left_chart_<?php echo @$users->id; ?>" height="170"></canvas>
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

                                      <canvas id="myChart_<?php echo @$users->id; ?>"></canvas>

                                      
          
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
                                        id="kt_card_widget_4_chart_<?php echo @$users->id; ?>" 
                                        style="min-width: 100%; min-height: 200px">
                                        <canvas id="candidates_offer_chart_<?php echo @$users->id; ?>" height="170"></canvas>
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
                                        id="kt_card_widget_4_chart_<?php echo @$users->id; ?>" 
                                        style="min-width: 100%; min-height: 200px">
                                        <canvas id="candidates_join_chart_<?php echo @$users->id; ?>" height="170"></canvas>
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
                                            <div class="tab-pane fade active show" id="kt_stats_widget_6_tab_1_<?php echo @$users->id; ?>" role="tabpanel">
                                                <!--begin::Table container-->

                                                <div id="kt_card_widget_6_chart_<?php echo @$users->id; ?>" class="w-100" style="height: 200px">

                                                    <div class="inetview_recent_chart_<?php echo @$users->id; ?> d-none" id="kt_card_widget_4_chart_<?php echo @$users->id; ?>" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="inetview_recent_chart_<?php echo @$users->id; ?>" height="300"></canvas>
                                                    </div>
                                                    
                                                    <div class="inetview_recent_chart_month_<?php echo @$users->id; ?>" id="kt_card_widget_4_chart" 
                                                            style="min-width: 100%; min-height: 200px">
                                                            <canvas id="inetview_recent_chart_month_<?php echo @$users->id; ?>" height="230"></canvas>
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
          </div>
        </div>
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


   

$(document).ready(function() {
    fetchTeamJobs();
    fetchTeamCandidate();
    fetchTeamCandidateJoining();
    fetchTeamCandidateInterviewTracker();

  $(document).on('click', '#paginationTeamtjobs .page-link', function(e) {
          e.preventDefault();

          const page = $(this).data('page');
          if (!page || $(this).hasClass('disabled')) return;

          // Disable the clicked pagination link
          $(this).addClass('disabled');

          // Fetch data
          fetchTeamJobs(page, $('#search').val()).then(() => {
              // Re-enable the links after successful data fetch
              $('#paginationTeamtjobs .page-link').removeClass('disabled');
          }).catch(() => {
              // Re-enable the links even if there's an error
              $('#paginationTeamtjobs .page-link').removeClass('disabled');
          });
          
  });

  $(document).on('click', '#paginationTeamtCandidates .page-link', function(e) {
          e.preventDefault();

          const page = $(this).data('page');
          if (!page || $(this).hasClass('disabled')) return;

          // Disable the clicked pagination link
          $(this).addClass('disabled');

          // Fetch data
          fetchTeamCandidate(page, $('#search').val()).then(() => {
              // Re-enable the links after successful data fetch
              $('#paginationTeamtCandidates .page-link').removeClass('disabled');
          }).catch(() => {
              // Re-enable the links even if there's an error
              $('#paginationTeamtCandidates .page-link').removeClass('disabled');
          });
          
  });
  $(document).on('click', '#paginationteamjoiningjobs .page-link', function(e) {
          e.preventDefault();

          const page = $(this).data('page');
          if (!page || $(this).hasClass('disabled')) return;

          // Disable the clicked pagination link
          $(this).addClass('disabled');

          // Fetch data
          fetchTeamCandidateJoining(page, $('#search').val()).then(() => {
              // Re-enable the links after successful data fetch
              $('#paginationteamjoiningjobs .page-link').removeClass('disabled');
          }).catch(() => {
              // Re-enable the links even if there's an error
              $('#paginationteamjoiningjobs .page-link').removeClass('disabled');
          });
          
  });
  $(document).on('click', '#paginationteaminterviewjobs .page-link', function(e) {
          e.preventDefault();

          const page = $(this).data('page');
          if (!page || $(this).hasClass('disabled')) return;

          // Disable the clicked pagination link
          $(this).addClass('disabled');

          // Fetch data
          fetchTeamCandidateInterviewTracker(page, $('#search').val()).then(() => {
              // Re-enable the links after successful data fetch
              $('#paginationteaminterviewjobs .page-link').removeClass('disabled');
          }).catch(() => {
              // Re-enable the links even if there's an error
              $('#paginationteaminterviewjobs .page-link').removeClass('disabled');
          });
          
  });

  document.getElementById('kt_tagify_search').addEventListener('keyup', function() {
		var userId = $('#kt_tagify_hidden').val();
		fetchTeamJobs();
	});


  // $(document).on('keyup',function(){
	// 	var userId = $('#kt_tagify_hidden').val();
	// 	fetchTeamJobs();
	// });


});

function fetchTeamJobs(page = 1, search = '') {
 var userId  = '{{ @$userId }}';
      $('.loadingMessageteamJobsListajax').css({ display: 'block' });
      $.ajax({
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
              $('.loadingMessage').css({ display: 'none' });
              $('#teamJobsListajax').html(response.html);
              $('#paginationTeamtjobs').html(response.pagination);
          }
      });
}



function fetchTeamCandidate(page = 1, search = '') {
  var userId  = '{{ @$userId }}';
      $('.loadingMessageteamCandidatesListajax').css({ display: 'block' });
      $.ajax({
          url: `{!! route('service_company.team.appdata_ajax') !!}?page=${page}`,
          type: 'GET',
          data: {
              userId: userId,
              cus_search : $('#candidate_kt_tagify_2').val(),
              sortingfilter : $('#candidatesortingfilter').val(),
              app_int_status : $('#stagefiltercandidate').val(),
          },
          success: function(response) {
              $('.loadingMessage').css({ display: 'none' });
              $('#teamCandidatesListajax').html(response.html);
              $('#paginationTeamtCandidates').html(response.pagination);
          }
      });
}
function fetchTeamCandidateJoining(page = 1, search = '') {
  var userId  = '{{ @$userId }}';
  var sortingfilter          			= $('#joinsortingfilter').val();
          var country          			= $('#country').val();
          var cus_search          			= $('#join_kt_tagify_2').val();
      $('.loadingMessageteamjoiningJobsListajax').css({ display: 'block' });
      $.ajax({
          url: `{!! route('service_company.team.joiningdata_ajax') !!}?page=${page}`,
          type: 'GET',
          data: {
              userId: userId,
              sortingfilter : sortingfilter,
              country : country,
              cus_search:cus_search
          },
          success: function(response) {
              $('.loadingMessage').css({ display: 'none' });
              $('#teamjoiningJobsListajax').html(response.html);
              $('#paginationteamjoiningjobs').html(response.pagination);
          }
      });
}
function fetchTeamCandidateInterviewTracker(page = 1, search = '') {
   var userId  = '{{ @$userId }}';
      $('.loadingMessageteaminterviewJobsListajax').css({ display: 'block' });
      $.ajax({
          url: `{!! route('service_company.team.intdata_ajax') !!}?page=${page}`,
          type: 'GET',
          data: {
              userId: userId,
              custom_hiringtype : $('#interviewfiltersdataget').val(),
              custom_search : $('#interviewtracker_kt_tagify_2').val()
          },
          success: function(response) {
              $('.loadingMessage').css({ display: 'none' });
              $('#teaminterviewJobsListajax').html(response.html);
              $('#paginationteaminterviewjobs').html(response.pagination);
          }
      });
}

function filtersdatagetcandidates() {
     fetchTeamCandidate();
}
document.getElementById('candidate_kt_tagify_2').addEventListener('keyup', function() {
    fetchTeamCandidate();
});

function interviewfiltersdataget() {
     fetchTeamCandidateInterviewTracker();
}
document.getElementById('interviewtracker_kt_tagify_2').addEventListener('keyup', function() {
    fetchTeamCandidateInterviewTracker();
});

function filtersdatagetjoin() {
     fetchTeamCandidateJoining();
}
document.getElementById('join_kt_tagify_2').addEventListener('keyup', function() {
    fetchTeamCandidateJoining();
});


   
   	
</script>
{{-- @endpush --}}
    
