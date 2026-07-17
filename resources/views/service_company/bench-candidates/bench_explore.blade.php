@extends('layouts.service_company_layout')
@push('head-script')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════
   BENCH EXPLORE — EXACT REFERENCE MATCH (bxr-)
═══════════════════════════════════════════════ */
#kt_content{ margin-top:-70px !important; }

.bxr-page{
    font-family:'Poppins',sans-serif;
    background:#F2F4F8;
    min-height:100vh;
    padding:18px 0 40px;
}
.bxr-page *{ font-family:'Poppins',sans-serif; box-sizing:border-box; }

/* ══ 1. PROFILE HERO ════════════════════════════ */
.bxr-hero{
    background:#fff;
    border-radius:0;
    padding:22px 28px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:24px;
    flex-wrap:wrap;
    border-bottom:1px solid #F0F2F5;
    margin-bottom:4px;
}

/* Avatar */
.bxr-av-wrap{ position:relative; flex-shrink:0; }
.bxr-avatar{
    width:82px; height:82px; border-radius:50%;
    object-fit:cover;
    border:3px solid #E8EDF5;
}
.bxr-avatar-init{
    width:82px; height:82px; border-radius:50%;
    background:linear-gradient(135deg,#4F46E5,#7C3AED);
    display:flex; align-items:center; justify-content:center;
    font-size:28px; font-weight:700; color:#fff;
}
.bxr-online-dot{
    position:absolute; bottom:4px; left:4px;
    width:14px; height:14px;
    background:#22C55E; border:2.5px solid #fff;
    border-radius:50%;
}

/* Hero left */
.bxr-hero-left{ display:flex; align-items:center; gap:18px; flex:1; min-width:260px; }
.bxr-info{}
.bxr-name-row{ display:flex; align-items:center; gap:10px; margin-bottom:3px; flex-wrap:wrap; }
.bxr-name{ font-size:22px; font-weight:700; color:#1A1D23; margin:0; }
.bxr-bench-tag{
    font-size:11px; font-weight:600;
    padding:3px 10px; border-radius:20px;
    background:#F0FDF4; color:#16A34A;
    border:1.5px solid #86EFAC;
}
.bxr-info-row{
    display:flex; align-items:center; gap:16px;
    flex-wrap:wrap; margin-bottom:8px;
}
.bxr-role{ font-size:13px; font-weight:600; color:#374151; margin:0; }
.bxr-meta-line{ display:flex; flex-wrap:wrap; gap:18px; }
.bxr-meta-item{ display:flex; align-items:center; gap:5px; font-size:12px; font-weight:500; color:#64748B; }
.bxr-meta-item i{ font-size:13px; color:#94A3B8; }
.bxr-bench-id-val{
    font-weight:700; color:#1A56DB;
    letter-spacing:.3px; font-size:12px;
}

/* KPI right */
.bxr-kpi-row{
    display:flex; align-items:center;
    gap:0; flex-shrink:0;
    flex-wrap:wrap;
}
.bxr-kpi{
    display:flex; align-items:center; gap:12px;
    padding:12px 28px;
    position:relative;
}
.bxr-kpi+.bxr-kpi::before{
    content:''; position:absolute; left:0; top:15%; height:70%;
    width:1px; background:#E5E9F0;
}
.bxr-kpi-icon{
    width:40px; height:40px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:18px; flex-shrink:0;
}
.bxr-kpi-icon.vi{ background:#EFF6FF; color:#2563EB; }
.bxr-kpi-icon.dl{ background:#F0FDF4; color:#16A34A; }
.bxr-kpi-icon.aj{ background:#EFF6FF; color:#0EA5E9; }
.bxr-kpi-icon.iv{ background:#F5F3FF; color:#7C3AED; }
.bxr-kpi-body{}
.bxr-kpi-num{ font-size:22px; font-weight:800; color:#1A1D23; line-height:1.1; }
.bxr-kpi-lbl{ font-size:11px; font-weight:500; color:#94A3B8; white-space:nowrap; }
.bxr-hero-menu{
    align-self:flex-start;
    width:32px; height:32px; border-radius:8px;
    border:1px solid #E5E9F0; background:#fff;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; color:#94A3B8; font-size:16px;
    transition:all .18s; flex-shrink:0;
}
.bxr-hero-menu:hover{ background:#F8FAFC; }

/* ══ 2. SUMMARY CARDS ═══════════════════════════ */
.bxr-sum-row{
    display:flex; gap:12px;
    margin-bottom:4px;
    flex-wrap:wrap;
    padding:12px 16px;
    background:#fff;
}
/* Each card fills 1/3 with a tinted background */
.bxr-sum-card{
    flex:1; min-width:200px;
    display:flex; align-items:center; gap:20px;
    padding:22px 24px;
    border-radius:14px;
    transition:filter .18s;
}
/* Per-card tinted background */
.bxr-sum-card.sc-p{ background:#F0EDFF; }   /* purple  */
.bxr-sum-card.sc-b{ background:#EBF5FF; }   /* blue    */
.bxr-sum-card.sc-o{ background:#FFF3EA; }   /* orange  */

/* Icon box */
.bxr-sum-icon{
    width:60px; height:60px; border-radius:16px;
    display:flex; align-items:center; justify-content:center;
    font-size:28px; flex-shrink:0;
}
.bxr-sum-card.sc-p .bxr-sum-icon{ background:#DDD6FE; color:#6D28D9; }
.bxr-sum-card.sc-b .bxr-sum-icon{ background:#BFDBFE; color:#1D4ED8; }
.bxr-sum-card.sc-o .bxr-sum-icon{ background:#FED7AA; color:#C2410C; }

/* Text */
.bxr-sum-label{
    font-size:12px; font-weight:600;
    color:#64748B; margin-bottom:4px; line-height:1.2;
}
.bxr-sum-num{
    font-size:34px; font-weight:800;
    color:#0F172A; line-height:1; margin-bottom:6px;
}
.bxr-sum-growth{
    font-size:12px; font-weight:600; color:#16A34A;
    display:flex; align-items:center; gap:3px;
}

/* ══ 3. SEARCH BAR ══════════════════════════════ */
.bxr-search-wrap{
    background:#fff;
    padding:14px 20px;
    display:flex; align-items:center; gap:12px;
    border-bottom:1px solid #F0F2F5;
    margin-bottom:4px;
    flex-wrap:wrap;
}
.bxr-search-box{
    flex:1; min-width:200px;
    position:relative;
}
.bxr-search-box i{
    position:absolute; left:14px; top:50%;
    transform:translateY(-50%);
    color:#CBD5E1; font-size:16px;
}
.bxr-search-box input{
    width:100%; height:44px;
    border:1.5px solid #E5E9F0; border-radius:10px;
    padding:0 16px 0 44px;
    font-size:13px; font-weight:500;
    color:#334155; background:#F8FAFC;
    outline:none; font-family:'Poppins',sans-serif;
    transition:border-color .18s, box-shadow .18s;
}
.bxr-search-box input:focus{
    border-color:#2563EB; background:#fff;
    box-shadow:0 0 0 3px rgba(37,99,235,.07);
}
.bxr-search-box input::placeholder{ color:#CBD5E1; }
.bxr-sort-label{ font-size:12px; font-weight:600; color:#64748B; white-space:nowrap; }
.bxr-sort-sel{
    height:44px; min-width:160px;
    border:1.5px solid #E5E9F0; border-radius:10px;
    padding:0 12px;
    font-size:13px; font-weight:500; color:#334155;
    background:#F8FAFC; outline:none;
    font-family:'Poppins',sans-serif;
}
.bxr-status-sel{
    height:44px; min-width:130px;
    border:1.5px solid #E5E9F0; border-radius:10px;
    padding:0 12px;
    font-size:13px; font-weight:500; color:#334155;
    background:#F8FAFC; outline:none;
    font-family:'Poppins',sans-serif;
    display:none; /* hidden — status is in filter-form */
}
.bxr-refresh{
    width:44px; height:44px;
    border:1.5px solid #E5E9F0; border-radius:10px;
    background:#F8FAFC;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:17px; color:#64748B;
    transition:all .2s; flex-shrink:0;
}
.bxr-refresh:hover{ background:#2563EB; color:#fff; border-color:#2563EB; transform:rotate(90deg); }

/* ══ 4. STAGE NAV ═══════════════════════════════ */
.bxr-stages-wrap{
    background:#fff;
    border-bottom:1.5px solid #E8ECF2;
    margin-bottom:16px;
    overflow-x:auto;
}
.bxr-stages{
    display:flex; align-items:center;
    width:100%;
    min-width:max-content;
    padding:0;
    height:72px;
}

/* ── Each tab ─────────────────────────────────── */
.bxr-stab{
    display:flex; flex-direction:row;
    align-items:center; justify-content:center;
    flex:1;                        /* fill remaining width evenly */
    min-width:110px;               /* don't shrink below this */
    height:100%;
    padding:0 12px;
    cursor:pointer;
    transition:background .13s;
    position:relative;
    user-select:none;
    gap:10px;
    border-bottom:3px solid transparent;
    margin-bottom:-1.5px;
}
/* divider line between every tab */
.bxr-stab + .bxr-stab::before{
    content:''; position:absolute; left:0; top:18%; height:64%;
    width:1px; background:#E8ECF2;
}
.bxr-stab:not(.bxr-active):hover{ background:#F5F8FF; }

/* ── ACTIVE pill ──────────────────────────────── */
.bxr-stab.bxr-active{ border-bottom:3px solid #2563EB; flex:0 0 auto; padding:0 20px; min-width:auto; }
.bxr-stab.bxr-active .bxr-stab-pill{
    display:inline-flex; align-items:center; gap:7px;
    background:#2563EB; color:#fff;
    padding:9px 20px; border-radius:10px;
    font-size:14px; font-weight:700; line-height:1;
    white-space:nowrap;
}
.bxr-stab.bxr-active .bxr-stab-icon-box,
.bxr-stab.bxr-active .bxr-stab-right{ display:none !important; }

/* ── INACTIVE tab ─────────────────────────────── */
.bxr-stab:not(.bxr-active) .bxr-stab-pill{ display:none !important; }

/* Colored icon box */
.bxr-stab-icon-box{
    display:none;                   /* shown via :not(.bxr-active) below */
    width:36px; height:36px;
    border-radius:10px;
    align-items:center; justify-content:center;
    font-size:18px; flex-shrink:0;
}
.bxr-stab:not(.bxr-active) .bxr-stab-icon-box{ display:flex; }

/* Right column */
.bxr-stab-right{
    display:none;
    flex-direction:column; align-items:flex-start; gap:2px;
}
.bxr-stab:not(.bxr-active) .bxr-stab-right{ display:flex; }

.bxr-stab-name{
    font-size:11px; font-weight:600; color:#64748B;
    white-space:nowrap; line-height:1;
}
.bxr-stab-cnt-plain{
    font-size:17px; font-weight:800; color:#0F172A;
    line-height:1.1;
}

/* ── Per-stage icon-box colours ───────────────── */
.bxr-stab[rel="1"] .bxr-stab-icon-box{ background:#EEEBFF; color:#7C3AED; }  /* Sourcing    purple  */
.bxr-stab[rel="2"] .bxr-stab-icon-box{ background:#E0F2FE; color:#0284C7; }  /* Screening   sky     */
.bxr-stab[rel="3"] .bxr-stab-icon-box{ background:#E0F2FE; color:#0EA5E9; }  /* Evaluation  sky     */
.bxr-stab[rel="4"] .bxr-stab-icon-box{ background:#FEF3C7; color:#D97706; }  /* Negotiation amber   */
.bxr-stab[rel="5"] .bxr-stab-icon-box{ background:#D1FAE5; color:#059669; }  /* Offer       green   */
.bxr-stab[rel="6"] .bxr-stab-icon-box{ background:#DCFCE7; color:#16A34A; }  /* Joined      green   */
.bxr-stab[rel="7"] .bxr-stab-icon-box{ background:#FFE4E6; color:#E11D48; }  /* Rejected    rose    */

/* ══ 5. TABLE AREA ══════════════════════════════ */
.bxr-tbl-wrap{
    background:#fff;
    border-radius:0;
}
.bxr-tbl-header{
    padding:16px 24px 14px;
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px;
    border-bottom:1px solid #F0F2F5;
}
.bxr-tbl-title{ font-size:14px; font-weight:700; color:#1A1D23; }
.bxr-tbl-count{ font-size:14px; font-weight:400; color:#64748B; }
.bxr-tbl-loading{
    display:none; align-items:center; gap:8px;
    font-size:12px; color:#94A3B8;
}
.bxr-tbl-scroll{ overflow-x:auto; }
.bxr-tbl{
    width:100%; border-collapse:collapse;
    min-width:860px;
}
.bxr-tbl thead tr{ background:#F8FAFC; }
.bxr-tbl thead th{
    padding:11px 16px;
    font-size:10px; font-weight:700;
    color:#94A3B8; text-transform:uppercase;
    letter-spacing:.6px;
    border-bottom:1px solid #F0F2F5;
    white-space:nowrap; font-family:'Poppins',sans-serif;
}
.bxr-tbl tbody tr{
    border-bottom:1px solid #F8FAFC;
    transition:background .13s;
}
.bxr-tbl tbody tr:hover{ background:#F8FAFF; }
.bxr-tbl tbody tr:last-child{ border-bottom:none; }
.bxr-tbl td{ padding:14px 16px; vertical-align:middle; }

/* Company logo */
.bxr-co-logo{
    width:36px; height:36px; border-radius:8px;
    object-fit:contain; border:1px solid #EDF2F7;
    background:#F8FAFC; flex-shrink:0;
}
.bxr-co-init{
    width:36px; height:36px; border-radius:8px;
    background:linear-gradient(135deg,#4F46E5,#7C3AED);
    display:flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:700; color:#fff; flex-shrink:0;
}
.bxr-job-row{ display:flex; align-items:flex-start; gap:10px; }
.bxr-job-title{ font-size:13px; font-weight:700; color:#1A1D23; margin-bottom:1px; }
.bxr-job-co{ font-size:11px; color:#64748B; margin-bottom:5px; }
.bxr-skills{ display:flex; flex-wrap:wrap; gap:4px; margin-top:3px; }
.bxr-skill{
    font-size:10px; font-weight:600;
    background:#F1F5F9; color:#475569;
    padding:2px 7px; border-radius:4px;
}
.bxr-skill.more{ background:#EFF6FF; color:#2563EB; }

/* Cell text */
.bxr-c1{ font-size:12px; font-weight:600; color:#1A1D23; margin-bottom:2px; }
.bxr-c2{ font-size:11px; color:#64748B; }
.bxr-c3{ font-size:11px; color:#94A3B8; margin-top:2px; }

/* Inline badges */
.bxr-b{
    display:inline-flex; align-items:center; gap:3px;
    font-size:10px; font-weight:600;
    padding:2px 8px; border-radius:4px;
    white-space:nowrap;
}
.bxr-b.gr{ background:#DCFCE7; color:#16A34A; }
.bxr-b.bl{ background:#DBEAFE; color:#1D4ED8; }
.bxr-b.yw{ background:#FEF9C3; color:#A16207; }
.bxr-b.pu{ background:#F5F3FF; color:#7C3AED; }
.bxr-b.gy{ background:#F1F5F9; color:#475569; }
.bxr-b.rd{ background:#FEE2E2; color:#DC2626; }

/* Pagination wrapper — content rendered by pagination partial */
.bxr-pagi{ display:block; }

/* ══ Responsive ══════════════════════════════════ */
@media(max-width:900px){
    .bxr-hero{ flex-direction:column; }
    .bxr-kpi-row{ flex-wrap:wrap; width:100%; }
    .bxr-kpi+.bxr-kpi::before{ display:none; }
    .bxr-sum-row{ flex-direction:column; }
    .bxr-sum-card{ border-right:none; }
}
</style>
@endpush

@section('content')

{{-- ── Hidden compat layer: keeps all original IDs alive ─────── --}}
<div aria-hidden="true"
     style="position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;opacity:0;z-index:-9;">
    @php
        $bench_explore_url      = url('service_company/bench-candidates/bench_explore/')          . @$bench_detail->id . '/all';
        $recommend_req_url      = url('service_company/bench-candidates/recommend_req/')          .'/'. @$bench_detail->id;
        $recommend_internal_req = url('service_company/bench-candidates/recommend_internal_req/') .'/'. @$bench_detail->id;
        $tabactive = [
            'bench_explore_pool_active'      => 'tabactive',
            'bench_explore_pool_url'         => $bench_explore_url,
            'bench_explore_pool_id'          => 'bench_explore_pool_id',
            'bench_explore_counter'          => $recomCount['bench_explore_counter'],
            'recommend_req_active'           => '',
            'recommend_req_url'              => $recommend_req_url,
            'recommend_req_id'               => 'recommend_req_id',
            'recommend_req_counter'          => $recomCount['recommend_req_counter'],
            'recommend_req_internal_active'  => '',
            'recommend_req_internal_url'     => $recommend_internal_req,
            'recommend_req_internal_id'      => 'recommend_req_internal_id',
            'recommend_req_internal_counter' => $recomCount['recommend_internal_req_counter'],
        ];
    @endphp
    @include('sections.service_company.bench-toolbar',    ['job'=>@$job,'bench_detail'=>@$bench_detail])
    @include('sections.service_company.benchExploreHeader',['tabactive'=>$tabactive])
    <form action="" id="filter-form">
        @csrf
        <input type="hidden" name="bench_id"    id="bench_id"    value="{{ @$bench_id }}">
        <input type="hidden" name="stage"       id="stage"       value="{{ @$stage }}">
        <input type="text"   name="inputSearch" id="inputSearch" value="">
    </form>
    <div id="bg_all" class="bg-total-active"></div>
    <span id="tx_1"></span><span id="tx_2"></span><span id="tx_3"></span>
    <span id="tx_4"></span><span id="tx_5"></span><span id="tx_6"></span><span id="tx_7"></span>
    <span class="nav_status"></span><span class="bg d-none"></span>
</div>

@php
    $sourcing_bg=$screening_bg=$interview_bg=$negotation_bg=$offer_bg=$joining_bg=$rejected_bg=$All_candidate_bg='alert bg-gray text-center circle_boxes';
    $sourcing_text=$screening_text=$interview_text=$negotation_text=$offer_text=$joining_text=$rejected_text=$All_candidate_text='text-center color-all';
    if(!empty(@$candStatusClass['Index'])){
        if     (@$candStatusClass['Index']=='Sourcing')    {$sourcing_bg  ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$sourcing_text  ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Screening')   {$screening_bg ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$screening_text ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Interview')   {$interview_bg ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$interview_text ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Negotiation') {$negotation_bg='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$negotation_text='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Offered')     {$offer_bg     ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$offer_text     ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Joined')      {$joining_bg   ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$joining_text   ='text-center '.@$candStatusClass['nav_text'];}
        elseif (@$candStatusClass['Index']=='Rejected')    {$rejected_bg  ='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$rejected_text  ='text-center '.@$candStatusClass['nav_text'];}
        else                                               {$All_candidate_bg='alert text-center circle_boxes '.@$candStatusClass['bg_class'];$All_candidate_text='text-center '.@$candStatusClass['nav_text'];}
    }
    $tr_class=(!empty($mstatus_id)&&$mstatus_id=='all')?'bg-total-active':'bg-gray';
@endphp

@if(in_array('view_bench',$userPermissions))
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
 <div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-0">
  <div class="bxr-page">

  {{-- ══ SECTION 1 · PROFILE HERO ══════════════════════ --}}
  <div class="bxr-hero">
    {{-- Left: avatar + details --}}
    <div class="bxr-hero-left">
      @php
        $imgFile  = @$bench_detail->profile_image ?? @$bench_detail->image ?? @$bench_detail->photo ?? null;
        $candName = @$bench_detail->name ?? 'Candidate';
        $initLtr  = strtoupper(substr($candName,0,1));
      @endphp
      <div class="bxr-av-wrap">
        @if($imgFile)
          <img src="{{ asset('assets/images/candidates/'.$imgFile) }}" class="bxr-avatar" alt="{{ $candName }}">
        @else
          <div class="bxr-avatar-init">{{ $initLtr }}</div>
        @endif
        <div class="bxr-online-dot"></div>
      </div>
      <div class="bxr-info">
        {{-- Row 1: Name + Bench badge --}}
        <div class="bxr-name-row">
          <span class="bxr-name">{{ $candName }}</span>
          <span class="bxr-bench-tag">+ Bench</span>
        </div>

        {{-- Row 2: Role  •  Location --}}
        <div class="bxr-info-row">
          <span class="bxr-role">{{ @$bench_detail->current_designation ?? '' }}</span>
          @if(@$bench_detail->city_name||@$bench_detail->city||@$bench_detail->location)
          <span class="bxr-meta-item">
            <i class="ti ti-map-pin"></i>
            {{ @$bench_detail->city_name ?? @$bench_detail->city ?? @$bench_detail->location }}@if(@$bench_detail->state_name), {{ @$bench_detail->state_name }}@endif
          </span>
          @endif
        </div>

        {{-- Row 3: Experience  •  Salary  •  Bench ID --}}
        <div class="bxr-meta-line">
          @if(@$bench_detail->experience||@$bench_detail->total_exp)
          <span class="bxr-meta-item">
            <i class="ti ti-clock"></i>
            {{ @$bench_detail->experience ?? @$bench_detail->total_exp }} Years
          </span>
          @endif
          @if(@$bench_detail->expected_salary||@$bench_detail->salary||@$bench_detail->ctc)
          <span class="bxr-meta-item">
            <i class="ti ti-currency-rupee"></i>
            {{ number_format(@$bench_detail->expected_salary ?? @$bench_detail->salary ?? @$bench_detail->ctc) }} / Year
          </span>
          @endif
          <span class="bxr-meta-item">
            <span style="color:#94A3B8;font-size:12px;">Bench ID:</span>&nbsp;
            <span class="bxr-bench-id-val">{{ @$bench_detail->bench_unique_id ?? ('B-'.str_pad(@$bench_detail->id??0,5,'0',STR_PAD_LEFT)) }}</span>
          </span>
        </div>
      </div>
    </div>

    {{-- Right: KPIs + menu --}}
    <div style="display:flex;align-items:center;gap:12px;">
      <div class="bxr-kpi-row">
        <div class="bxr-kpi">
          <div class="bxr-kpi-icon vi"><i class="ti ti-eye"></i></div>
          <div class="bxr-kpi-body">
            <div class="bxr-kpi-num">{{ @$bench_detail->total_views??@$bench_detail->views??0 }}</div>
            <div class="bxr-kpi-lbl">Views</div>
          </div>
        </div>
        <div class="bxr-kpi">
          <div class="bxr-kpi-icon dl"><i class="ti ti-download"></i></div>
          <div class="bxr-kpi-body">
            <div class="bxr-kpi-num">{{ @$bench_detail->total_downloads??@$bench_detail->downloads??0 }}</div>
            <div class="bxr-kpi-lbl">Downloads</div>
          </div>
        </div>
        <div class="bxr-kpi">
          <div class="bxr-kpi-icon aj"><i class="ti ti-send"></i></div>
          <div class="bxr-kpi-body">
            <div class="bxr-kpi-num">{{ @$recomCount['recommend_req_counter']??0 }}</div>
            <div class="bxr-kpi-lbl">Applied on Jobs</div>
          </div>
        </div>
        <div class="bxr-kpi">
          <div class="bxr-kpi-icon iv"><i class="ti ti-calendar-event"></i></div>
          <div class="bxr-kpi-body">
            <div class="bxr-kpi-num">{{ @$bench_detail->total_interviews??@$bench_detail->interviews??0 }}</div>
            <div class="bxr-kpi-lbl">Interviews</div>
          </div>
        </div>
      </div>
      <button class="bxr-hero-menu"><i class="ti ti-dots-vertical"></i></button>
    </div>
  </div>

  {{-- ══ SECTION 2 · SUMMARY CARDS ════════════════════ --}}
  <div class="bxr-sum-row">
    {{-- Card 1: purple --}}
    <div class="bxr-sum-card sc-p">
      <div class="bxr-sum-icon"><i class="ti ti-users-group"></i></div>
      <div>
        <div class="bxr-sum-label">Total Companies Added</div>
        <div class="bxr-sum-num bxr-live">{{ $recomCount['bench_explore_counter'] }}</div>
        <div class="bxr-sum-growth">
          <i class="ti ti-arrow-up" style="font-size:11px;"></i> 1 new today
        </div>
      </div>
    </div>
    {{-- Card 2: blue --}}
    <div class="bxr-sum-card sc-b">
      <div class="bxr-sum-icon"><i class="ti ti-briefcase"></i></div>
      <div>
        <div class="bxr-sum-label">Recommended Jobs</div>
        <div class="bxr-sum-num">{{ $recomCount['recommend_req_counter'] }}</div>
        <div class="bxr-sum-growth">
          <i class="ti ti-arrow-up" style="font-size:11px;"></i> 5 new today
        </div>
      </div>
    </div>
    {{-- Card 3: orange --}}
    <div class="bxr-sum-card sc-o">
      <div class="bxr-sum-icon"><i class="ti ti-building-skyscraper"></i></div>
      <div>
        <div class="bxr-sum-label">Recommended Internal Jobs</div>
        <div class="bxr-sum-num">{{ $recomCount['recommend_internal_req_counter'] }}</div>
        <div class="bxr-sum-growth">
          <i class="ti ti-arrow-up" style="font-size:11px;"></i> 2 new today
        </div>
      </div>
    </div>
  </div>

  {{-- ══ SECTION 3 · SEARCH BAR ════════════════════════ --}}
  <div class="bxr-search-wrap">
    <div class="bxr-search-box">
      <i class="ti ti-search"></i>
      <input type="text" id="kt_tagify_2" name="inputJobSearch"
             placeholder="Search by company, job or recruiter..."
             value="" onchange="filtersdataget()">
    </div>
    <span class="bxr-sort-label">Sort By</span>
    <select class="bxr-sort-sel" id="hiringtype" onchange="filtersdataget()">
      <option value="All">Last Activity</option>
      <option value="1" selected>Newest to Oldest</option>
      <option value="2">Oldest to Newest</option>
      <option value="3">Low to High (Salary)</option>
      <option value="4">High to Low (Salary)</option>
    </select>
    {{-- Status hidden but functional --}}
    <select class="bxr-status-sel" id="status" onchange="filtersdataget()">
      <option value="">All Status</option>
      <option value="all">All</option>
      <option value="active" selected>Active</option>
      <option value="hold">Hold</option>
      <option value="inactive">Closed</option>
      <option value="removed">Removed</option>
    </select>
    <button class="bxr-refresh" onclick="fetchBenchExploreJobCandidateList()" title="Refresh">
      <i class="ti ti-refresh"></i>
    </button>
  </div>

  {{-- ══ SECTION 4 · STAGE NAVIGATION ═════════════════ --}}
  <div class="bxr-stages-wrap">
    <div class="bxr-stages" id="bxr-stages-row">

      {{-- ALL — active default --}}
      <div class="bxr-stab application_sttus_nav bxr-active" rel="all">
        <div class="bxr-stab-pill">All <span id="totalcnt">0</span></div>
        <div class="bxr-stab-icon-box" style="background:#EFF6FF;color:#2563EB;"><i class="ti ti-layout-grid"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">All</div>
          <div class="bxr-stab-cnt-plain" id="totalcnt_plain">0</div>
        </div>
      </div>

      {{-- SOURCING --}}
      <div class="bxr-stab application_sttus_nav" rel="1">
        <div class="bxr-stab-pill">Sourcing <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-users-group"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Sourcing</div>
          <div class="bxr-stab-cnt-plain" id="bg_1">0</div>
        </div>
      </div>

      {{-- SCREENING --}}
      <div class="bxr-stab application_sttus_nav" rel="2">
        <div class="bxr-stab-pill">Screening <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-clipboard-list"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Screening</div>
          <div class="bxr-stab-cnt-plain" id="bg_2">0</div>
        </div>
      </div>

      {{-- EVALUATION --}}
      <div class="bxr-stab application_sttus_nav" rel="3">
        <div class="bxr-stab-pill">Evaluation <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-search"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Evaluation</div>
          <div class="bxr-stab-cnt-plain" id="bg_3">0</div>
        </div>
      </div>

      {{-- NEGOTIATION --}}
      <div class="bxr-stab application_sttus_nav" rel="4">
        <div class="bxr-stab-pill">Negotiation <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-3d-cube-sphere"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Negotiation</div>
          <div class="bxr-stab-cnt-plain" id="bg_4">0</div>
        </div>
      </div>

      {{-- OFFER --}}
      <div class="bxr-stab application_sttus_nav" rel="5">
        <div class="bxr-stab-pill">Offer <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-briefcase"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Offer</div>
          <div class="bxr-stab-cnt-plain" id="bg_5">0</div>
        </div>
      </div>

      {{-- JOINED --}}
      <div class="bxr-stab application_sttus_nav" rel="6">
        <div class="bxr-stab-pill">Joined <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-circle-check"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Joined</div>
          <div class="bxr-stab-cnt-plain" id="bg_6">0</div>
        </div>
      </div>

      {{-- REJECTED --}}
      <div class="bxr-stab application_sttus_nav" rel="7">
        <div class="bxr-stab-pill">Rejected <span>0</span></div>
        <div class="bxr-stab-icon-box"><i class="ti ti-circle-x"></i></div>
        <div class="bxr-stab-right">
          <div class="bxr-stab-name">Rejected</div>
          <div class="bxr-stab-cnt-plain" id="bg_7">0</div>
        </div>
      </div>

    </div>{{-- /bxr-stages --}}
  </div>{{-- /bxr-stages-wrap --}}

  {{-- ══ SECTION 5 · TABLE ══════════════════════════════ --}}
  <div class="bxr-tbl-wrap">
    <div class="bxr-tbl-header">
      <span class="bxr-tbl-title">
        Companies Processing This Candidate
        <span class="bxr-tbl-count bxr-live">({{ $recomCount['bench_explore_counter'] }})</span>
      </span>
      <div class="bxr-tbl-loading loadingMessagebenchCandidateExploreJobAjax" style="display:none;">
        <span class="spinner-border spinner-border-sm text-primary" style="width:13px;height:13px;border-width:2px;"></span>
        Processing...
      </div>
    </div>

    <div class="bxr-tbl-scroll">
      <table id="bench_explore_listing" class="bxr-tbl table-custom-class">
        <thead>
          <tr>
            <th style="width:40px;">
              <div class="form-check form-check-sm form-check-custom form-check-solid ms-2">
                <input class="form-check-input" type="checkbox" value="1">
              </div>
            </th>
            <th style="min-width:220px;">JOB DETAILS</th>
            <th style="min-width:130px;">EXPERIENCE</th>
            <th style="min-width:130px;">HIRING &amp; LOCATION</th>
            <th style="min-width:130px;">HIRING MANAGER</th>
            <th style="min-width:110px;">JOB STAGE</th>
            <th style="min-width:110px;">ADDED BY</th>
          </tr>
        </thead>
        <tbody id="benchCandidateExploreJobAjax"></tbody>
      </table>
    </div>

    <div class="bxr-pagi">
      <div id="paginationbenchCandidateExploreJob"></div>
    </div>
  </div>

  </div>{{-- /bxr-page --}}
  </div>
 </div>
</div>
@endif

{{-- Drawer — unchanged ── --}}
<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
     data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
     data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle"
     data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>

@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>

{{-- ── Stage tab active-state visual (new design) ── --}}
<script>
/* Keep pill spans in sync with the plain-count spans updated by AJAX */
function syncPillCounts(){
    /* "All" tab: totalcnt is already the #totalcnt span inside the pill */
    var allVal=$('#totalcnt').text();
    $('#totalcnt_plain').text(allVal);

    /* Stage tabs 1–7 */
    [1,2,3,4,5,6,7].forEach(function(n){
        var val=$('#bg_'+n).text();
        /* pill span is the last <span> child of .bxr-stab-pill in that tab */
        $('.bxr-stab[rel="'+n+'"] .bxr-stab-pill span:last').text(val);
    });
}
$(document).ready(function(){
    var init=$('#stage').val()||'all';
    $('.bxr-stab').removeClass('bxr-active');
    $('.bxr-stab[rel="'+init+'"]').addClass('bxr-active');
});
$(document).on('click','.bxr-stab',function(){
    $('.bxr-stab').removeClass('bxr-active');
    $(this).addClass('bxr-active');
});
</script>

{{-- ── Original stage-click logic — UNCHANGED ── --}}
<script>
$(document).ready(function(){
    var cur=$('#stage').val();
    if(cur=='all'){$('.nav_status').html('All Candidate').css({background:'linear-gradient(to left,#01e074,#0581d9)',color:'#fff'});}
    $(document).on('click','.application_sttus_nav',function(){
        $(".bg").removeClass('bg-Asourcing bg-Ascreening bg-Ainterview bg-Anegotation bg-Aoffer bg-Ajoiner bg-Arejected').addClass('bg-gray');
        ['1','2','3','4','5','6','7'].forEach(function(n){
            $('#tx_'+n).removeClass('text-Asourcing text-Ascreening text-Ainterview text-Anegotation text-Aoffer text-Ajoiner text-Arejected').addClass('color-all');
        });
        $('#bg_all').removeClass('bg-total-active').addClass('bg-gray');
        var page=$(this).attr('rel'), bid=$('#bench_id').val();
        var M={1:['bg-Asourcing','text-Asourcing','Sourcing',{background:'#efefef',color:'#000'}],
               2:['bg-Ascreening','text-Ascreening','Screening',{background:'#fff9ed',color:'#ffab00'}],
               3:['bg-Ainterview','text-Ainterview','Interview',{background:'#f0f8ff',color:'#2896ff'}],
               4:['bg-Anegotation','text-Anegotation','Negotiation',{background:'#ffe6f2',color:'#ff4098'}],
               5:['bg-Aoffer','text-Aoffer','Offered',{background:'#d4ffde',color:'#06c732'}],
               6:['bg-Ajoiner','text-Ajoiner','Joined',{background:'#f7deff',color:'#c000ff'}],
               7:['bg-Arejected','text-Arejected','Rejected',{background:'#ffe2e5',color:'#ff0202'}]};
        if(M[page]){$('#bg_'+page).removeClass('bg-gray').addClass(M[page][0]);$('#tx_'+page).removeClass('color-all').addClass(M[page][1]);$('.nav_status').html(M[page][2]).css(M[page][3]);}
        else if(page=='all'){$('.nav_status').html('All Candidate').css({background:'linear-gradient(to left,#01e074,#0581d9)',color:'#fff'});$('#bg_all').removeClass('bg-gray').addClass('bg-total-active');}
        $('#stage').val(page);
        var url='<?php echo url('/service_company/bench-candidates/bench_explore/'); ?>';
        window.history.pushState('page2','Title',url+'/'+bid+'/'+page);
        fetchBenchExploreJobCandidateList();
    });
});
</script>

{{-- ── Search / AJAX / counters / drawer — UNCHANGED ── --}}
<script>
$(document).ready(function(){
    $('#kt_tagify_2').keyup(function(){ fetchBenchExploreJobCandidateList(); });
});
var table;
function filtersdataget(){ fetchBenchExploreJobCandidateList(); }

function tableLoad(){
    var bid=$('#bench_id').val(),stage=$('#stage').val(),q=$('#inputSearch').val();
    var url='{{ route('service_company.bench-candidates.bench_explore_listing_counter',[':bench_id',':stage',':inputSearch']) }}';
    url=url.replace(':bench_id',bid).replace(':stage',stage).replace(':inputSearch',q);
    $.ajax({type:'GET',url:url,success:function(r){
        var d={allCandidate:0,Sourcing:0,Screening:0,Rejected:0,Offered:0,Negotiation:0,Joined:0,Evaluation:0};
        if(r.allCounter>0) d=r.data;
        setTimeout(function(){
            $('#bg_1').html(d.Sourcing);   $('#bg_2').html(d.Screening);
            $('#bg_3').html(d.Evaluation); $('#bg_4').html(d.Negotiation);
            $('#bg_5').html(d.Offered);    $('#bg_6').html(d.Joined);
            $('#bg_7').html(d.Rejected);   $('#totalcnt').html(d.allCandidate);
            /* keep plain count spans in sync too */
            $('#totalcnt_plain').html(d.allCandidate);
            syncPillCounts();
        },100);
    }});
}
$(document).on('click','.show-detail',function(){
    $('#kt_activitiesJob_toggle').trigger('click');
    var id=$(this).data('row-id');
    var url="{{ route('service_company.jobs.viewmore',':id') }}?viewmore=viewmore&conid={{ $bench_detail->id }}&bechshowtab=1";
    url=url.replace(':id',id);
    var gif="{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({type:'GET',url:url,beforeSend:function(){$('#kt_activitiesJob_body').html('<img src="'+gif+'" height="35px" width="100%">');},success:function(r){$('#kt_activitiesJob_body').html(r.view);}});
});
$(document).on('click','.client-show-detail',function(){
    $('#kt_activitiesJob_toggle').trigger('click');
    var id=$(this).data('row-id');
    var url="{{ route('service_company.jobs.viewmore',':id') }}?viewmore=clientviewmore&conid={{ $bench_detail->id }}&bechshowtab=1";
    url=url.replace(':id',id);
    var gif="{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({type:'GET',url:url,beforeSend:function(){$('#kt_activitiesJob_body').html('<img src="'+gif+'" height="35px" width="100%">');},success:function(r){$('#kt_activitiesJob_body').html(r.view);}});
});
$(document).ready(function(){
    fetchBenchExploreJobCandidateList();
    $(document).on('click','#paginationbenchCandidateExploreJob .page-link',function(e){
        e.preventDefault();
        var pg=$(this).data('page');
        if(!pg||$(this).hasClass('disabled')) return;
        $(this).addClass('disabled');
        fetchBenchExploreJobCandidateList(pg,$('#search').val())
            .then(function(){$('#paginationbenchCandidateExploreJob .page-link').removeClass('disabled');})
            .catch(function(){$('#paginationbenchCandidateExploreJob .page-link').removeClass('disabled');});
    });
});
function fetchBenchExploreJobCandidateList(page,search){
    page=page||1;
    var bid=$('#bench_id').val(),stage=$('#stage').val(),
        q=$('#inputSearch').val(),sort=$('#hiringtype').val(),
        st=$('#status').val(),cs=$('#kt_tagify_2').val();
    $('.loadingMessagebenchCandidateExploreJobAjax').css({display:'flex'});
    return $.ajax({
        url:"{!! route('service_company.bench-candidates.bench_explore_listing_job_ajax') !!}?page="+page,
        type:'GET',
        data:{bench_id:bid,stage:stage,search:q,cus_search:cs,statusfilter:st,sortingfilter:sort},
        success:function(r){
            $('.loadingMessagebenchCandidateExploreJobAjax').css({display:'none'});
            $('#benchCandidateExploreJobAjax').html(r.html);
            $('#bench_explore_pool_id').html(r.total);
            $('.bxr-live').html(r.total);
            $('#paginationbenchCandidateExploreJob').html(r.pagination);
            tableLoad();
        }
    });
}
</script>
@endpush
