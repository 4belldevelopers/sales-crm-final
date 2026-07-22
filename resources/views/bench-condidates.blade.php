<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bench Candidates</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('demo/css/style-bench-candidates.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link rel="canonical" href="" />
    <link rel="shortcut icon" type="image/x-icon"  href="{{ asset('img/favicon.svg')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/css/style.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/css/intlTelInput.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        html { scroll-behavior: auto !important; }
        :root {
            --primary-blue:#2563EB; --blue-hover:#1D4ED8;
            --green:#22C55E; --green-light:#DCFCE7; --green-text:#166534;
            --orange:#F97316; --orange-light:#FFEDD5; --orange-text:#9A3412;
            --red:#EF4444; --red-light:#FEE2E2; --red-text:#991B1B;
            --purple:#8B5CF6; --purple-light:#EDE9FE;
            --blue-light:#DBEAFE; --blue-text:#1E40AF;
            --text-primary:#0F172A; --text-secondary:#64748B; --text-muted:#94A3B8;
            --border:#E2E8F0; --bg:#F8FAFC; --white:#FFFFFF;
            --shadow-sm:0 1px 3px rgba(0,0,0,.08);
            --shadow-md:0 4px 12px rgba(0,0,0,.1);
            --r-sm:6px; --r-md:10px; --r-lg:14px;
        }

        /* ── PAGE WRAPPER ── */
        .bc-page { max-width:1280px; margin:0 auto; padding:24px 20px; font-family:'Poppins',sans-serif; }

        /* ── HERO CARD ── */
        .bc-hero {
            display:grid; grid-template-columns:1fr 1fr; gap:24px;
            background:var(--white); border:1px solid var(--border);
            border-radius:var(--r-lg); padding:28px; margin-bottom:24px;
            box-shadow:var(--shadow-sm);
        }
        @media(max-width:900px){ .bc-hero{grid-template-columns:1fr;} }

        /* LEFT */
        .bc-hero-left { display:flex; flex-direction:column; gap:16px; }
        .bc-hero-top  { display:flex; align-items:flex-start; gap:18px; }
        .bc-logo {
            width:88px; height:88px; border-radius:var(--r-md);
            border:1px solid var(--border); display:flex; align-items:center;
            justify-content:center; background:var(--bg); flex-shrink:0; overflow:hidden;
        }
        .bc-logo img { width:100%; height:100%; object-fit:contain; }
        .bc-logo-init {
            font-size:32px; font-weight:700; color:var(--primary-blue); text-transform:uppercase;
        }
        .bc-verified-badge {
            display:inline-flex; align-items:center; gap:5px;
            background:var(--green-light); color:var(--green-text);
            font-size:10px; font-weight:600; padding:3px 8px; border-radius:20px; margin-bottom:4px;
        }
        .bc-company-name {
            font-size:20px; font-weight:700; color:var(--text-primary);
            display:flex; align-items:center; gap:6px; margin:0;
        }
        .bc-company-name svg { width:18px; height:18px; color:var(--primary-blue); }
        .bc-rating { display:flex; align-items:center; gap:6px; margin:4px 0; font-size:13px; }
        .bc-stars { color:#F59E0B; font-size:14px; }
        .bc-rating-val { font-weight:700; color:var(--text-primary); }
        .bc-rating-count { color:var(--text-secondary); }
        .bc-top-badge {
            display:inline-flex; align-items:center; gap:4px;
            background:var(--blue-light); color:var(--blue-text);
            font-size:10px; font-weight:600; padding:3px 8px; border-radius:20px;
        }
        .bc-tagline { font-size:14px; font-weight:600; color:var(--text-primary); margin:0; }
        .bc-desc { font-size:12px; color:var(--text-secondary); margin:0; line-height:1.6; }
        .bc-meta { display:flex; flex-wrap:wrap; gap:12px 20px; }
        .bc-meta-item { display:flex; align-items:center; gap:5px; font-size:11px; color:var(--text-secondary); }
        .bc-meta-item svg { width:13px; height:13px; color:var(--text-muted); }
        .bc-actions { display:flex; flex-wrap:wrap; gap:8px; margin-top:4px; }
        .bc-btn-primary {
            display:inline-flex; align-items:center; gap:6px;
            background:var(--primary-blue); color:#fff; font-size:13px; font-weight:600;
            padding:9px 18px; border-radius:var(--r-sm); border:none; cursor:pointer; transition:background .2s;
        }
        .bc-btn-primary:hover { background:var(--blue-hover); }
        .bc-btn-outline {
            display:inline-flex; align-items:center; gap:6px;
            background:var(--white); color:var(--text-secondary); font-size:12px; font-weight:500;
            padding:8px 14px; border-radius:var(--r-sm); border:1px solid var(--border); cursor:pointer; transition:all .2s;
        }
        .bc-btn-outline:hover { border-color:var(--primary-blue); color:var(--primary-blue); }
        .bc-btn-outline svg { width:14px; height:14px; }

        /* RIGHT */
        .bc-hero-right { display:flex; flex-direction:column; gap:14px; border-left:1px solid var(--border); padding-left:24px; }
        @media(max-width:900px){ .bc-hero-right{border-left:none;padding-left:0;border-top:1px solid var(--border);padding-top:18px;} }

        /* Stats row — no card borders, icon + number + label inline */
        .bc-stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:0; }
        .bc-stat { display:flex; align-items:center; gap:10px; padding:0 8px 0 0; }
        .bc-stat + .bc-stat { border-left:1px solid var(--border); padding-left:12px; }
        .bc-stat-icon-wrap {
            width:34px; height:34px; border-radius:8px;
            display:flex; align-items:center; justify-content:center; flex-shrink:0;
        }
        .bc-stat-icon-wrap svg { width:18px; height:18px; }
        .bc-stat-icon-wrap.purple { background:#EDE9FE; color:#7C3AED; }
        .bc-stat-icon-wrap.teal   { background:#CCFBF1; color:#0D9488; }
        .bc-stat-icon-wrap.green  { background:#DCFCE7; color:#16A34A; }
        .bc-stat-icon-wrap.orange { background:#FEF3C7; color:#D97706; }
        .bc-stat-body { display:flex; flex-direction:column; }
        .bc-stat-num { font-size:17px; font-weight:700; color:var(--text-primary); line-height:1.1; }
        .bc-stat-label { font-size:10px; color:var(--text-secondary); margin-top:2px; line-height:1.2; }

        /* Pricing divider */
        .bc-pricing-divider { border:none; border-top:1px solid var(--border); margin:2px 0; }

        .bc-pricing-label {
            font-size:10px; font-weight:700; text-transform:uppercase;
            letter-spacing:.8px; color:var(--text-muted); margin-bottom:8px; text-align:center;
        }
        .bc-hiring-label {
            font-size:10px; font-weight:700; text-transform:uppercase;
            letter-spacing:.8px; color:var(--text-muted); margin-bottom:8px; text-align:left;
        }
        .bc-pricing-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .bc-price-card {
            border:1.5px solid var(--border); border-radius:var(--r-md); padding:14px 16px;
            display:flex; flex-direction:column; gap:2px;
        }
        .bc-price-card.green { border-color:#BBF7D0; background:#F0FDF4; }
        .bc-price-card.blue  { border-color:#BFDBFE; background:#EFF6FF; }
        .bc-price-icon-wrap {
            width:30px; height:30px; border-radius:7px;
            display:flex; align-items:center; justify-content:center; margin-bottom:6px;
        }
        .bc-price-card.green .bc-price-icon-wrap { background:#BBF7D0; color:#15803D; }
        .bc-price-card.blue  .bc-price-icon-wrap { background:#BFDBFE; color:#1D4ED8; }
        .bc-price-icon-wrap svg { width:16px; height:16px; }
        .bc-price-type { font-size:11px; color:var(--text-secondary); font-weight:500; }
        .bc-price-amount { font-size:22px; font-weight:700; line-height:1.2; margin:2px 0; }
        .bc-price-card.green .bc-price-amount { color:#16A34A; }
        .bc-price-card.blue  .bc-price-amount { color:#1E3A5F; }
        .bc-price-basis { font-size:10px; color:var(--text-secondary); }

        .bc-hiring-tags { display:flex; flex-wrap:wrap; gap:6px; }
        .bc-hiring-tag {
            padding:5px 14px; border:1px solid var(--border); border-radius:20px;
            font-size:11px; font-weight:500; color:var(--text-secondary); background:var(--white);
        }

        /* ── CANDIDATE LIST ── */
        .bc-list-wrap { background:var(--white); border:1px solid var(--border); border-radius:var(--r-lg); overflow:hidden; box-shadow:var(--shadow-sm); }
        .bc-list-header {
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;
            gap:10px; padding:16px 20px; border-bottom:1px solid var(--border);
        }
        .bc-list-title { font-size:14px; font-weight:700; color:var(--text-primary); }
        .bc-list-count { font-size:12px; color:var(--text-secondary); margin-left:6px; }
        .bc-search-bar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .bc-search-input {
            padding:8px 14px; border:1px solid var(--border); border-radius:var(--r-sm);
            font-size:13px; color:var(--text-primary); font-family:'Poppins',sans-serif;
            outline:none; width:220px;
        }
        .bc-search-input:focus { border-color:var(--primary-blue); }
        .bc-sort-select {
            padding:8px 28px 8px 10px; border:1px solid var(--border); border-radius:var(--r-sm);
            font-size:12px; color:var(--text-secondary); background:var(--white);
            outline:none; cursor:pointer; font-family:'Poppins',sans-serif;
        }
        .bc-search-btn {
            padding:8px 16px; background:var(--primary-blue); color:#fff;
            border:none; border-radius:var(--r-sm); font-size:13px; font-weight:600;
            cursor:pointer; font-family:'Poppins',sans-serif;
        }

        /* TABLE */
        .bc-table-scroll { overflow-x:auto; }
        .bc-table { width:100%; border-collapse:collapse; min-width:900px; }
        .bc-table thead th {
            padding:11px 16px; font-size:11px; font-weight:600; text-transform:uppercase;
            letter-spacing:.5px; color:var(--text-secondary); background:var(--white);
            border-bottom:1px solid var(--border); white-space:nowrap; text-align:left;
        }
        .bc-table tbody tr { border-bottom:1px solid var(--border); transition:background .15s; }
        .bc-table tbody tr:last-child { border-bottom:none; }
        .bc-table tbody tr:hover { background:#FAFAFA; }
        .bc-table tbody td { padding:14px 16px; vertical-align:top; }

        /* Candidate cell */
        .bc-cand-cell { display:flex; align-items:flex-start; gap:10px; }
        .bc-avatar {
            width:38px; height:38px; border-radius:50%; display:flex; align-items:center;
            justify-content:center; font-size:14px; font-weight:700; color:#fff; flex-shrink:0;
        }
        .bc-cand-name { font-size:13px; font-weight:600; color:var(--text-primary); }
        .bc-cand-role { font-size:11px; color:var(--text-secondary); margin-top:2px; }
        .bc-skill-tags { display:flex; flex-wrap:wrap; gap:4px; margin-top:5px; }
        .bc-skill-tag {
            display:inline-block; padding:1px 7px; background:var(--blue-light);
            color:var(--blue-text); border-radius:20px; font-size:10px; font-weight:500;
        }
        /* experience */
        .bc-exp-main { font-size:13px; font-weight:700; color:var(--text-primary); }
        .bc-exp-sub  { font-size:11px; color:var(--text-secondary); margin-top:2px; }
        /* availability */
        .bc-avail-badge {
            display:inline-flex; align-items:center; gap:5px;
            padding:3px 9px; border-radius:20px; font-size:11px; font-weight:600;
        }
        .bc-avail-dot { width:6px; height:6px; border-radius:50%; background:currentColor; flex-shrink:0; }
        .avail-green  { background:var(--green-light);  color:var(--green-text); }
        .avail-orange { background:var(--orange-light); color:var(--orange-text); }
        .avail-red    { background:var(--red-light);    color:var(--red-text); }
        .bc-avail-date{ font-size:11px; color:var(--text-secondary); margin-top:3px; }
        /* location */
        .bc-loc-city  { font-size:13px; font-weight:500; color:var(--text-primary); }
        .bc-loc-sub   { font-size:11px; color:var(--text-secondary); margin-top:2px; }
        /* salary */
        .bc-sal-main  { font-size:13px; font-weight:700; color:var(--text-primary); }
        .bc-sal-sub   { font-size:11px; color:var(--text-secondary); margin-top:2px; }
        /* status */
        .bc-status-main { font-size:12px; font-weight:600; }
        .bc-status-sub  { font-size:11px; color:var(--text-secondary); margin-top:2px; }
        /* action btn */
        .bc-add-btn {
            display:inline-flex; align-items:center; gap:5px;
            background:var(--primary-blue); color:#fff; font-size:12px; font-weight:600;
            padding:7px 14px; border-radius:var(--r-sm); border:none; cursor:pointer;
            white-space:nowrap; font-family:'Poppins',sans-serif; transition:background .2s;
        }
        .bc-add-btn:hover { background:var(--blue-hover); }

        /* PAGINATION */
        .bc-pagination { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-top:1px solid var(--border); flex-wrap:wrap; gap:8px; }
        .bc-page-info { font-size:12px; color:var(--text-secondary); }
        .bc-page-controls { display:flex; gap:4px; }
        .bc-page-btn {
            min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;
            border:1px solid var(--border); background:var(--white); border-radius:var(--r-sm);
            font-size:12px; color:var(--text-secondary); cursor:pointer; padding:0 8px; transition:all .15s;
            text-decoration:none;
        }
        .bc-page-btn:hover:not(.bc-page-btn-disabled) { border-color:var(--text-muted); color:var(--text-primary); }
        .bc-page-btn.active { background:var(--primary-blue); border-color:var(--primary-blue); color:#fff; font-weight:600; }
        .bc-page-btn-disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }

        /* media: KT drawers */
        @media only screen and (min-width:1024px) and (max-width:1440px) { #kt_activitiesCopy{width:1025px;} }
        @media only screen and (min-width:1440px) and (max-width:2560px) { #kt_activitiesCopy{width:1200px;} }
        @media only screen and (min-width:1024px) and (max-width:1440px) { #kt_activitiesBenchApplication{width:1025px;} }
        @media only screen and (min-width:1440px) and (max-width:2560px) { #kt_activitiesBenchApplication{width:1200px;} }

        /* Star-rated skills (find-talent design) */
        .skill-item { display:flex;align-items:center;gap:6px;margin-bottom:3px; }
        .skill-name { font-size:12px;color:#181c32;width:72px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
        .stars { display:flex;gap:1px; }
        .star_skill { width:13px;height:13px;color:#fbbf24;flex-shrink:0; }
        .star_skill.empty { color:#e2e8f0; }
    </style>
</head>

@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    
@endphp


<body>
@php
    $logo_path = '';
    if(!empty(@$account->logo) && strlen(@$account->logo)>10){ $logo_path = @$account->logo; }
    $initLetter = strtoupper(substr(trim(@$account->company_name),0,1));

    $recommendateRow = DB::table('recommendation_relation')
        ->select('id','from_company_id','from_status','to_status','final_status')
        ->where(function($q) use($company_id,$to_company_id){
            $q->where(function($q2) use($company_id,$to_company_id){
                $q2->where('from_company_id',$company_id)->where('to_company_id',$to_company_id);
            })->orWhere(function($q2) use($company_id,$to_company_id){
                $q2->where('from_company_id',$to_company_id)->where('to_company_id',$company_id);
            });
        })->first();

    /* Avatar colour by first letter */
    $avatarColors = ['#10B981','#3B82F6','#8B5CF6','#F59E0B','#EF4444','#06B6D4','#EC4899'];
    $avatarColor  = $avatarColors[ord(strtolower($initLetter)) % count($avatarColors)];
@endphp

<main>
<div class="bc-page">

    {{-- ═══════════════════ HERO ═══════════════════ --}}
    <div class="bc-hero">

        {{-- LEFT ─ company identity --}}
        <div class="bc-hero-left">
            <div class="bc-hero-top">
                {{-- Logo --}}
                <div class="bc-logo">
                    @if(!empty($logo_path))
                        <img src="{{ $logo_path }}" alt="{{ @$account->company_name }}">
                    @else
                        <span class="bc-logo-init" style="color:{{ $avatarColor }};">{{ $initLetter }}</span>
                    @endif
                </div>

                {{-- Info --}}
                <div>
                    <div class="bc-verified-badge">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Verified Staffing Partner
                    </div>
                    <h2 class="bc-company-name">
                        {{ ucwords(@$account->company_name) }}
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </h2>
                    <div class="bc-rating">
                        <span class="bc-stars">★★★★★</span>
                        <span class="bc-rating-val">4.8</span>
                        <span class="bc-rating-count">(124 Reviews)</span>
                        <span class="bc-top-badge">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            Top Performer
                        </span>
                    </div>
                    <p class="bc-tagline">{{ @$industries_name ?: 'Enterprise Recruitment Partner' }}</p>
                    <p class="bc-desc">{!! strip_tags(@$account->description) ? mb_substr(strip_tags(@$account->description),0,120).'…' : 'Delivering pre-vetted, high-performing talent across India and globally. AI-powered sourcing. Faster hiring. Better outcomes.' !!}</p>
                </div>
            </div>

            {{-- Meta row --}}
            <div class="bc-meta">
                <span class="bc-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ ucwords($company_city_name) }}, {{ ucwords($company_country_name) }}
                </span>
                @if(@$account->founded)
                <span class="bc-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ @$account->founded }}+ Years in Business
                </span>
                @endif
                @if(@$account->website)
                <span class="bc-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    {{ @$account->website }}
                </span>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="bc-actions">
                @if($recommendateRow)
                    @if($recommendateRow->final_status == 0 || $recommendateRow->final_status == '')
                        <button class="bc-btn-primary send_job_recommanded_request" data-row-event="-1" id="sp_list_" data-row-id="{{ $to_company_id }}">
                            Cancel Request
                        </button>
                    @else
                        <button class="bc-btn-primary" style="background:#16A34A;" id="sp_list_0" data-row-fromcompany="{{ $company_id }}" data-row-tocompany="{{ $to_company_id }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Connected
                        </button>
                    @endif
                @else
                    <button class="bc-btn-primary send_job_recommanded_request" data-row-jobid="" data-row-event="1" id="sp_list_{{ $to_company_id }}" data-boosterCom="0" data-boost-ids="" data-row-id="{{ $to_company_id }}">
                        View Bench Talent
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                @endif
                <button class="bc-btn-outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Contact Us
                </button>
                <button class="bc-btn-outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    Share Profile
                </button>
                <button class="bc-btn-outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    Follow
                </button>
            </div>
        </div>

        {{-- RIGHT ─ stats + pricing + hiring types --}}
        <div class="bc-hero-right">
            {{-- Stats --}}
            <div class="bc-stats-grid">
                <div class="bc-stat">
                    <div class="bc-stat-icon-wrap purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="bc-stat-body">
                        <div class="bc-stat-num">{{ $total }}</div>
                        <div class="bc-stat-label">Active Candidates</div>
                    </div>
                </div>
                <div class="bc-stat">
                    <div class="bc-stat-icon-wrap teal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div class="bc-stat-body">
                        <div class="bc-stat-num">6.8 <span style="font-size:11px;font-weight:500;">Yrs</span></div>
                        <div class="bc-stat-label">Average Experience</div>
                    </div>
                </div>
                <div class="bc-stat">
                    <div class="bc-stat-icon-wrap green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </div>
                    <div class="bc-stat-body">
                        <div class="bc-stat-num">{{ $techCount ?? 48 }}</div>
                        <div class="bc-stat-label">Technologies</div>
                    </div>
                </div>
                <div class="bc-stat">
                    <div class="bc-stat-icon-wrap orange">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="bc-stat-body">
                        <div class="bc-stat-num">30 <span style="font-size:11px;font-weight:500;">mins</span></div>
                        <div class="bc-stat-label">Avg. Response Time</div>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div>
                <hr class="bc-pricing-divider">
                <div class="bc-pricing-label">Pricing at a Glance</div>
                <div class="bc-pricing-grid">
                    @if(@$fullTimeStatus->status==1)
                    <div class="bc-price-card green">
                        <div class="bc-price-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        </div>
                        <div class="bc-price-type">Full Time Rate</div>
                        <div class="bc-price-amount">{{ @$fullTimeStatusRate }}% / Hire</div>
                        <div class="bc-price-basis">On Annual CTC Basis</div>
                    </div>
                    @else
                    <div class="bc-price-card green">
                        <div class="bc-price-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        </div>
                        <div class="bc-price-type">Full Time Rate</div>
                        <div class="bc-price-amount">—</div>
                        <div class="bc-price-basis">Not Listed</div>
                    </div>
                    @endif

                    @if(@$contractStatus->status==1)
                    <div class="bc-price-card blue">
                        <div class="bc-price-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="bc-price-type">Contract Rate</div>
                        <div class="bc-price-amount">{!!$currencySymbole[(@$contractStatus->salary_currency)?$contractStatus->salary_currency:0]!!}{{ @$contractStatusRate }}/{{ mb_substr($salary_mode[(@$contractStatus->sal_mode)?@$contractStatus->sal_mode:0],0,2) }}</div>
                        <div class="bc-price-basis">On Contract Basis</div>
                    </div>
                    @else
                    <div class="bc-price-card blue">
                        <div class="bc-price-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="bc-price-type">Contract Rate</div>
                        <div class="bc-price-amount">—</div>
                        <div class="bc-price-basis">Not Listed</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Hiring types --}}
            <div>
                <div class="bc-hiring-label">Hiring Types We Support</div>
                <div class="bc-hiring-tags">
                    <span class="bc-hiring-tag">Permanent</span>
                    <span class="bc-hiring-tag">Contract</span>
                    <span class="bc-hiring-tag">C2H</span>
                    <span class="bc-hiring-tag">Freelance</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ CANDIDATE LIST ═══════════════════ --}}
    <div class="bc-list-wrap" id="candidate-section">
        <div class="bc-list-header">
            <div>
                <span class="bc-list-title">Bench Candidate List</span>
                <span class="bc-list-count">({{ $total }} candidates)</span>
            </div>
            <form method="GET" action="" style="display:contents;">
                <div class="bc-search-bar">
                    <input type="text" name="cus_search" value="{{ $searchTerms }}" class="bc-search-input" placeholder="Search by keyword...">
                    <select class="bc-sort-select" name="sortingfilter" id="sortingfilter" onchange="this.form.submit()">
                        <option value="Show All" {{ $valCheck=='Show All'?'selected':'' }}>Sort</option>
                        <option value="1"  {{ $valCheck=='1' ?'selected':'' }}>Newest to Oldest</option>
                        <option value="2"  {{ $valCheck=='2' ?'selected':'' }}>Oldest to Newest</option>
                        <option value="9"  {{ $valCheck=='9' ?'selected':'' }}>Notice Period (Immediate)</option>
                        <option value="5"  {{ $valCheck=='5' ?'selected':'' }}>Low to High (Notice)</option>
                        <option value="6"  {{ $valCheck=='6' ?'selected':'' }}>High to Low (Notice)</option>
                        <option value="7"  {{ $valCheck=='7' ?'selected':'' }}>High to Low (Experience)</option>
                        <option value="8"  {{ $valCheck=='8' ?'selected':'' }}>Low to High (Experience)</option>
                    </select>
                    <button type="submit" class="bc-search-btn">Search</button>
                </div>
            </form>
        </div>

        <div class="bc-table-scroll">
            <table class="bc-table">
                <thead>
                    <tr>
                        <th style="width:22%;">Talent</th>
                        <th style="width:10%;">Experience</th>
                        <th style="width:15%;">Skills</th>
                        <th style="width:13%;">Availability</th>
                        <th style="width:13%;">Location</th>
                        <th style="width:15%;">Expected Salary</th>
                        <th style="width:12%;text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $sal_mode_bc         = 0;
                    $min_salary_currency_bc = 0;
                    $symbolsValue_bc     = \App\Helper\GeneralMethod::currencyConverter()['getKey'] ?? [0=>'INR',1=>'EUR',2=>'JPY',3=>'GBP',4=>'USD',5=>'AED'];
                @endphp
                @if(count($data) > 0)
                    @foreach($data as $rowArr)
                    @php
                        $row = (object)$rowArr;

                        /* Avatar colour */
                        $bgTextDecore = $strVal = '';
                        $str = trim(@$row->full_name);
                        $str = $strVal = strtolower($str[0] ?? 'a');
                        $str = ord($str);
                        if      ($str >= 97  && $str <= 101) { $bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
                        elseif  ($str >= 102 && $str <= 106) { $bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
                        elseif  ($str >= 107 && $str <= 111) { $bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
                        elseif  ($str >= 112 && $str <= 116) { $bgTextDecore = 'fs-3 bg-light-success text-success'; }
                        elseif  ($str >= 117 && $str <= 121) { $bgTextDecore = 'fs-3 bg-dark text-secondary'; }
                        else                                 { $bgTextDecore = 'fs-3 bg-light-secondary text-dark'; }
                        $profile_sidebar_css = 'pbench_show_application_detail';

                        /* Skills badges (bottom of talent cell) */
                        $skillExperiences = DB::table('bench_candidate_skill_experience')
                            ->where('candidate_id', $row->id)->get();
                        $skillArr = explode(',', @$row->skills);
                        $skilltag = '';
                        $viewMore = 0;
                        if ($skillExperiences->isNotEmpty()) {
                            foreach ($skillExperiences as $key => $exp) {
                                if ($key < 5) {
                                    $skilltag .= '<span class="badge badge-light my-1" style="font-weight:400;color:#3f4254;">' . ucfirst($exp->skill_key) . '</span> ';
                                } else { $viewMore++; }
                            }
                        } else {
                            foreach (array_slice($skillArr, 0, 5) as $sval) {
                                $sval = trim($sval);
                                if ($sval) $skilltag .= '<span class="badge badge-light my-1" style="font-weight:400;color:#3f4254;">' . ucfirst($sval) . '</span> ';
                            }
                        }
                        if ($viewMore > 0) {
                            $skilltag .= '<span class="badge badge-light my-1" style="font-weight:500;color:#7e8299;">+' . $viewMore . ' more</span>';
                        }

                        /* Experience */
                        $experience = trim((string) @$row->total_exp);
                        if (strpos($experience, '.') !== false) {
                            $parts = explode('.', $experience);
                            $formattedExperience = (isset($parts[1]) && $parts[1] === '00') ? $parts[0] : $experience;
                        } else { $formattedExperience = $experience; }
                        $workMode = trim((string) @$row->wm);

                        /* Availability */
                        $npArr  = ['Notice period', 'Available from', 'Immediate'];
                        $npDate = $row->np_id;
                        $modeNp = (int)($row->serving_notice_period_mode ?? 0);
                        if ($modeNp == 0) {
                            $npDate = $row->np_id . ($row->np_id == 1 ? ' Day' : ' Days');
                        } elseif ($modeNp == 1) {
                            $formatted = date('d-M-y', strtotime($row->np_id));
                            if (date('Y-m-d', strtotime($row->np_id)) < date('Y-m-d')) { $npDate = 'Immediate'; $modeNp = 2; }
                            else { $npDate = 'SNP LWD ' . $formatted; }
                        } else { $npDate = ''; }
                        if ($npDate === 'Immediate' || $modeNp == 2) { $npColorStyle = 'color:#16a34a !important;'; }
                        elseif ($modeNp == 0)                        { $npColorStyle = 'color:#d97706 !important;'; }
                        elseif ($modeNp == 1)                        { $npColorStyle = 'color:#7c3aed !important;'; }
                        else                                         { $npColorStyle = 'color:#181c32 !important;'; }
                        $npLabel       = $npArr[$modeNp] ?? 'Notice period';
                        $outsourcingLabel = ($row->outsourcing_type ?? 0) == 1 ? 'Full-time' : 'Contract';

                        /* Bench status badge */
                        $benchStatusMap = [
                            1 => ['label'=>'Pool',   'bg'=>'#dbeafe','color'=>'#1d4ed8'],
                            2 => ['label'=>'Active', 'bg'=>'#dcfce7','color'=>'#15803d'],
                            3 => ['label'=>'Placed', 'bg'=>'#f3e8ff','color'=>'#7c3aed'],
                            4 => ['label'=>'Hold',   'bg'=>'#fef3c7','color'=>'#d97706'],
                        ];
                        $benchStatus = $benchStatusMap[(int)($row->status ?? 2)] ?? $benchStatusMap[2];

                        /* Location */
                        $row_city_name    = @$row->city_name;
                        $row_country_name = @$row->country_name;

                        /* Expected Salary (bill rate) */
                        $currency_code_bc        = $symbolsValue_bc[$min_salary_currency_bc] ?? 'INR';
                        $billFormatViewSalary = GeneralMethod::formatViewSalary($row->bill_rate_amount, $currency_code_bc, $sal_mode_bc);
                        $bill_rate_currency   = $currencySymbole[$billFormatViewSalary->currency] ?? '';
                        $bill_rate_mode       = $salaryMode[$sal_mode_bc] ?? '';
                        $bill_rate_amount     = GeneralMethod::formatK($billFormatViewSalary->amount);
                        $comission_label      = ($row->comission_type ?? 0) == 1 ? 'Free' : 'Paid';
                        $comission_color      = '#16a34a';
                    @endphp
                    <tr style="height:102px;position:relative;">

                        {{-- Talent --}}
                        <td>
                            @php
                                echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                    <a href="javascript:void(0);" data-row-id="' . @$row->id . '" class="' . $profile_sidebar_css . '">
                                        <div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
                                    </a>
                                </div>
                                <div class="d-flex flex-column" style="overflow:hidden;">
                                    <div class="d-flex align-items-center gap-1">
                                        <a href="javascript:void(0);" data-row-id="' . @$row->id . '"
                                           class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . '"
                                           data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">'
                                           . ucwords(mb_substr(@$row->full_name, 0, 18)) . '
                                        </a>
                                        <span style="background:' . $benchStatus['bg'] . ';color:' . $benchStatus['color'] . ';font-size:9px;font-weight:700;padding:1px 6px;border-radius:4px;white-space:nowrap;">' . $benchStatus['label'] . '</span>
                                    </div>
                                    <a href="javascript:void(0);" data-row-id="' . @$row->id . '"
                                       class="text-muted fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . '"
                                       data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">'
                                       . ucwords(mb_substr(@$row->current_designation, 0, 22)) . '
                                    </a>
                                </div>
                                <div style="position:absolute;bottom:0;white-space:nowrap;width:94%;overflow:hidden;">
                                    <span style="font-size:10px;font-weight:600;color:#7e8299;margin-right:4px;">Skills</span>'
                                    . $skilltag .
                                '</div>';
                            @endphp
                        </td>

                        {{-- Experience --}}
                        <td>
                            @php
                                echo '<div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $formattedExperience . ' Y</a>'
                                    . ($workMode ? '<span class="fw-bold d-block" style="font-size:10px;color:#7c3aed;">' . $workMode . '</span>' : '<span class="text-muted fw-bold d-block fs-7">—</span>') .
                                '</div>';
                            @endphp
                        </td>

                        {{-- Skills (star-rated) --}}
                        <td>
                            @php
                                $skillHtml = '';
                                foreach ($skillExperiences as $key => $exp) {
                                    if ($key >= 3) break;
                                    $skillHtml .= '<div class="skill-item">
                                        <span class="skill-name">' . ucfirst($exp->skill_key) . '</span>
                                        <div class="stars">
                                            <svg class="star_skill ' . (@$exp->rating >= 1 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            <svg class="star_skill ' . (@$exp->rating >= 2 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            <svg class="star_skill ' . (@$exp->rating >= 3 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            <svg class="star_skill ' . (@$exp->rating >= 4 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            <svg class="star_skill ' . (@$exp->rating >= 5 ? '' : 'empty') . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        </div>
                                    </div>';
                                }
                                echo $skillHtml ?: '<span class="text-muted fs-7">—</span>';
                            @endphp
                        </td>

                        {{-- Availability --}}
                        <td>
                            @php
                                echo '<div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="fw-bolder fs-6" style="' . $npColorStyle . '">' . $npLabel . '</a>
                                    <span class="text-muted fw-bold d-block fs-7">' . $outsourcingLabel . '</span>
                                </div>';
                            @endphp
                        </td>

                        {{-- Location --}}
                        <td>
                            @php
                                echo '<div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"
                                       data-toggle="tooltip" title="' . ucwords($row_city_name) . ', ' . ucwords($row_country_name) . '">'
                                       . ucwords(mb_substr($row_city_name, 0, 12)) . ', ' . ucwords(mb_substr($row_country_name, 0, 10)) . '
                                    </a>
                                    <span class="text-muted fw-bold d-block fs-7"
                                          data-toggle="tooltip" title="' . @$row->qualification_name . '">'
                                          . strtoupper(mb_substr(@$row->qualification_name, 0, 14)) . '
                                    </span>
                                </div>';
                            @endphp
                        </td>

                        {{-- Expected Salary --}}
                        <td>
                            @php
                                echo '<div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'
                                        . @$bill_rate_currency . $bill_rate_amount
                                        . '<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/' . $bill_rate_mode . '</span>
                                    </a>
                                    <span class="fw-bold d-block fs-7" style="color:' . $comission_color . ';">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
                                        ' . $comission_label . '
                                    </span>
                                    <span class="fw-bold d-block fs-7" style="color:#6d7991;">Service</span>
                                </div>';
                            @endphp
                        </td>

                        {{-- Action --}}
                        <td style="text-align:center;">
                            @php
                                $html = '';
                                if ($row->status == 2) {
                                    if (($row->countforadd ?? 0) >= 3) {
                                        $html = '<a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a>';
                                    } else {
                                        $html = '<div class="d-flex flex-column" style="margin-bottom:6px;">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary openSideBar"
                                               data-row-source_from_type="2"
                                               data-row-id="' . @$row->id . '"
                                               data-row-jobid="">Add to Job</a>
                                        </div>';
                                    }
                                } else {
                                    $html = '<a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a>';
                                }
                                echo $html;
                            @endphp
                            <a href="javascript:;" class="fw-bold fs-7 text-hover-primary {{ $profile_sidebar_css }}" data-row-id="{{ $row->id }}">View Profile</a>
                        </td>

                    </tr>
                    @endforeach
                @else
                    <tr style="height:102px;position:relative;background:#fffcfc7a;">
                        <td colspan="7" style="text-align:center;color:black;">
                            No candidates available
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @php $totalPages=$last_page; $currentPage=$current_page; @endphp
        <div class="bc-pagination">
            <div class="bc-page-info">
                Showing page {{ $currentPage }} of {{ $totalPages }} &nbsp;·&nbsp; {{ $total }} candidates
            </div>
            <div class="bc-page-controls">
                @if($currentPage>1)
                    <a href="?page={{ $currentPage-1 }}&cus_search={{ $searchTerms }}&sortingfilter={{ $valCheck }}" class="bc-page-btn">‹ Prev</a>
                @else
                    <span class="bc-page-btn bc-page-btn-disabled">‹ Prev</span>
                @endif

                @for($i=1;$i<=$totalPages;$i++)
                    @if($i==$currentPage)
                        <span class="bc-page-btn active">{{ $i }}</span>
                    @elseif(abs($i-$currentPage)<=2 || $i==1 || $i==$totalPages)
                        <a href="?page={{ $i }}&cus_search={{ $searchTerms }}&sortingfilter={{ $valCheck }}" class="bc-page-btn">{{ $i }}</a>
                    @elseif(abs($i-$currentPage)==3)
                        <span class="bc-page-btn bc-page-btn-disabled" style="border:none;">…</span>
                    @endif
                @endfor

                @if($currentPage<$totalPages)
                    <a href="?page={{ $currentPage+1 }}&cus_search={{ $searchTerms }}&sortingfilter={{ $valCheck }}" class="bc-page-btn">Next ›</a>
                @else
                    <span class="bc-page-btn bc-page-btn-disabled">Next ›</span>
                @endif
            </div>
        </div>
    </div>

</div>
</main>

    
    <div id="kt_activitiesCopy_toggle"></div>
    <div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle"
        data-kt-drawer-close=".kt_activitiesCopy_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Select the job checkbox, then click "Save" to add the candidate</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">

                  
                    <div class="">
                        <button type="button" id="submit-frm-btn" class="btn btn-primary copy_current_selected_applicant d-none"> 
                            <span class="indicator-label">Save to Jobs</span> 
                            <span class="indicator-progress">
                                Please wait...
                            </span> 
                        </button>
                    </div>


                    <button type="button"
                        class="btn btn-sm btn-icon btn-active-light-primary me-n5 kt_activitiesCopy_close"
                        id="kt_activitiesCopy_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="application_copy_body_content"></div>
        </div>
    </div>
        
    <div id="kt_activitiesBenchApplication_toggle"></div>
    <div id="kt_activitiesBenchApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activitiesBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesBenchApplication_toggle" data-kt-drawer-close="#kt_activitiesBenchApplication_close">
    <div class="card shadow-none rounded-0 w-100" id="bench_application_show_right_bar" ></div>
    </div>


    <footer>
        <p>&copy; 2025 Your Company. All rights reserved.</p>
    </footer>

    <script src="{{ asset('demo/plugins/global/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/widgets.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/apps/chat/chat.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}{{ TM_VERSION }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/js/intlTelInput.min.js"></script>
<script src="{{ asset('front/assets/js/number-to-words.js') }}"></script>
<script src="{{ asset('front/assets/js/countryDialCodes.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>

  function showToastr(type, message, title) {
    switch(type) {
        case 'success':
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.success(message, title);
            break;

        case 'error':
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.error(message, title);
            break;

        case 'info':
            toastr.options.positionClass = 'toast-top-left';
            toastr.info(message, title);
            break;

        case 'warning':
            toastr.options.positionClass = 'toast-bottom-left';
            toastr.warning(message, title);
            break;

        default:
            toastr.options.positionClass = 'toast-top-center';
            toastr.info("Unknown status", "Notice");
    }
}


        $(document).on('click', '.openSideBar', function() {
            $('#kt_activitiesCopy_toggle').trigger('click');
            var id = 1;
            var url = "{{ route('service_company.cvtracker.talent-add', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var idid = $(this).data('row-id');
            var currentUrl = window.location.href;
            // alert(idid);
            var arr = [idid];
           
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr
                },
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "block") {
                        Swal.fire("Warning", 'Bench candidate cannot be copy ', "warning").then(
                            function() {
                                $("#kt_activitiesCopy_close").click();
                            });
                        return false;
                    }

                    if (response.status == "success") {
                        $('#application_copy_body_content').html(response.view);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                       Swal.fire({
                            title: "Session Expired",
                            text: "Please login again.",
                            icon: "error",
                            allowOutsideClick: true, // ⛔ disable outside click
                            allowEscapeKey: false,    // ⛔ disable Esc key
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ url('login') }}?url=" + encodeURIComponent(currentUrl);
                            }
                            $('#kt_activitiesCopy_close').click();
                        });

                    } else {
                        Swal.fire("Error", "Something went wrong. Try again.", "error");
                    }
                }
            });
        });

        $(document).on('click','.bench_show_application_detail',function(){
			//  table.on('click', '.bench_show_application_detail', function (){
                        var company_id = "{{ $company_id }}";
						$('#kt_activitiesBenchApplication_toggle').trigger('click');
						var id    		=	$(this).data('row-id');
						var url 		=	"{{ route('get.benchCandidateDetails',':id') }}";
						url     		=	url.replace(':id', id);		
						var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
						$.ajax({
										type  		:	'GET',
										url   		:	url,
                                        data : {
                                            company_id : company_id   // ✅ PASS HERE
                                        },
										beforeSend	:	function() {
													  				$('#bench_application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
														},
										success 	:	function (response) {
																			$('#bench_application_show_right_bar').html(response.view);
														}
								});
		});

        $(document).on('change', '.chk_copy_item', function () {
           const isChecked = $(this).is(':checked');
           const checkedCount = $('.chk_copy_item:checked').length;

            // Show individual toast message only for this one checkbox
           if (isChecked) {
                showToastr('success', 'Job selected successfully!', 'Selected');
            } else {
                showToastr('error', 'Job removed successfully!', 'Removed');
            }

            // Show/hide the bulk action button based on overall selection
            if (checkedCount > 0) {
                $('.copy_current_selected_applicant').removeClass("d-none");
            } else {
                $('.copy_current_selected_applicant').addClass("d-none");
            }
        });

    $('body').off('click', '.send_job_recommanded_request').on('click', '.send_job_recommanded_request', function(event) {
        event.preventDefault();

        var $this = $(this);
        // Disable the button to prevent multiple clicks 

        var from_company_id = $this.data('row-id');
        var current_jobid = $this.data('row-jobid');
        var event_id = $this.data('row-event');
        var matchcount = $this.data('matchcount');
        var id = from_company_id;
        var kt_tagify_location = $('#kt_tagify_location').val();
        var page  = $('.active_disabled ').data('page');
        var boostercom = $(this).data('boostercom');
        var boostids = '';  

        var currentUrl = window.location.href;
        if(id==0){
            Swal.fire({
                        title: "Session Expired",
                        text: "Please login again.",
                        icon: "error",
                        allowOutsideClick: true, // ⛔ disable outside click
                        allowEscapeKey: false,    // ⛔ disable Esc key
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ url('login') }}?url=" + encodeURIComponent(currentUrl);
                        }
                        $('#kt_activitiesCopy_close').click();
            });
            return false;
        }

        
        $this.prop('disabled', true);

        if(boostercom==1){
            var boostids =  $(this).data('boost-ids');
        }


        var url = "{{ route('service_company.agency.recommanded_request', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            type: 'GET',
            url: url,
            data: {
                'from_company_id': from_company_id,
                'event_id': event_id,
                'current_jobid': current_jobid,
                'boostercom': boostercom,
                'boostids': boostids,
                'matchcount': matchcount,
                'kt_tagify_location': kt_tagify_location
            },
            success: function(response) {
                if (response.status === "success") {
                    if(event_id==1){
                        $('#sp_list_' + id).removeClass('btn-light-primary send_job_recommanded_request send_only_job_invitation')
                                      .addClass('btn-light-danger')
                                      .text('Cancel Request');
                    }else{
                        $('#sp_list_' + id).removeClass('btn-light-primary send_job_recommanded_request send_only_job_invitation')
                                      .addClass('btn-light-success')
                                      .text('Request Sent');
                    }

                    Swal.fire("Success", response.message, "success");
                    location.reload();
                }else if (response.status === "warning") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "info") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "closeJob") {
                    Swal.fire("Warning", response.message, "warning");
                }
                // Re-enable the button after processing
                $this.prop('disabled', false);
            },
            error: function() {
                // Re-enable the button if there's an error
                $this.prop('disabled', false);
            }
        });
    });
        

    
        document.addEventListener('DOMContentLoaded', function() {
            const searchButton = document.querySelector('.search-button');
            const candidateSection = document.getElementById('candidate-section');
            const filterSelect = document.querySelector('.filter-select');
            const searchInputt= document.querySelector('.search-input');

            // Scroll on search button click
            searchButton.addEventListener('click', function() {
                candidateSection.scrollIntoView({ behavior: 'smooth' });
            });

            // Scroll on page load if a filter is selected
            if (filterSelect.value !== '') {
                candidateSection.scrollIntoView({ behavior: 'smooth' });
            }
            if (searchInputt.value !== '') {
                candidateSection.scrollIntoView({ behavior: 'smooth' });
            }
        });

        

    </script>

</body>

</html>
