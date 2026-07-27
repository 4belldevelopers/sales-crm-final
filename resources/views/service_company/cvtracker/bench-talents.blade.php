@php
		use App\Helper\GeneralMethod;
@endphp
<style>
.text-justify{text-align: justify!important;}
.symbol1 > img,
.symbol1 .symbol-label1{width: 32px!important; height: 32px!important;}
.table > :not(caption) > * > *{padding: 0rem 0.4rem;}

/* ── Bench Talents Hero ── */
.bth-wrap { background:#fff; border:1px solid #E5E7EB; border-radius:14px; padding:16px 18px 12px; margin-bottom:12px; }
.bth-job-strip { display:flex; align-items:center; gap:0; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #f1f3f8; flex-wrap:nowrap; }
.bth-job-avatar { width:44px; height:44px; border-radius:10px; background:#FEF3C7; color:#D97706; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:18px; font-weight:700; }
.bth-job-title-block { margin-left:12px; margin-right:20px; flex-shrink:0; }
.bth-job-title { font-size:14px; font-weight:700; color:#111827; line-height:1.2; white-space:nowrap; }
.bth-job-sub { font-size:11.5px; color:#6B7280; margin-top:3px; display:flex; align-items:center; gap:4px; white-space:nowrap; }
.bth-job-sub .dot { color:#D1D5DB; }
.bth-strip-sep { width:1px; background:#E5E7EB; height:38px; flex-shrink:0; margin:0 18px; }
.bth-info-block { display:flex; align-items:center; gap:10px; flex-shrink:0; margin-right:18px; }
.bth-info-icon { width:34px; height:34px; border-radius:8px; background:#F3F4F6; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.bth-info-icon svg { width:16px; height:16px; color:#6B7280; }
.bth-info-label { font-size:10.5px; color:#9CA3AF; font-weight:500; margin-bottom:1px; }
.bth-info-val { font-size:12.5px; font-weight:600; color:#111827; white-space:nowrap; }
.bth-view-btn { margin-left:auto; display:inline-flex; align-items:center; gap:6px; border:1.5px solid #BFDBFE; border-radius:8px; padding:7px 16px; font-size:12px; font-weight:600; color:#2563EB; background:#fff; cursor:pointer; white-space:nowrap; flex-shrink:0; text-decoration:none; }
.bth-view-btn:hover { background:#EFF6FF; }

/* Stat cards */
.bth-stats { display:flex; gap:10px; margin-bottom:14px; flex-wrap:nowrap; overflow-x:auto; }
.bth-card { display:flex; align-items:center; gap:10px; flex:1; min-width:110px; background:#fff; border:1px solid #E5E7EB; border-radius:10px; padding:10px 14px; }
.bth-card-icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.bth-card-icon svg { width:18px; height:18px; }
.bth-ic-blue   { background:#EFF6FF; color:#2563EB; }
.bth-ic-green  { background:#DCFCE7; color:#16A34A; }
.bth-ic-teal   { background:#ECFDF5; color:#059669; }
.bth-ic-orange { background:#FFF7ED; color:#EA580C; }
.bth-ic-purple { background:#F3F0FF; color:#7C3AED; }
.bth-card-num { font-size:22px; font-weight:700; color:#111827; line-height:1.1; }
.bth-card-label { font-size:11px; color:#374151; font-weight:600; }
.bth-card-sub { font-size:10px; color:#9CA3AF; margin-top:1px; }

/* Filter bar */
.bth-filter-bar { display:flex; align-items:center; gap:8px; flex-wrap:nowrap; margin-bottom:10px; }
.bth-search-wrap { position:relative; flex:1; min-width:160px; }
.bth-search-wrap svg { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#9CA3AF; pointer-events:none; }
.bth-search-input { width:100%; border:1px solid #E5E7EB; border-radius:8px; padding:8px 8px 8px 30px; font-size:12.5px; font-family:'Poppins',sans-serif; outline:none; background:#F9FAFB; color:#374151; }
.bth-search-input:focus { border-color:#93C5FD; background:#fff; }
.bth-drop-wrap { display:flex; align-items:center; gap:4px; border:1px solid #E5E7EB; border-radius:8px; padding:7px 10px; background:#fff; flex-shrink:0; }
.bth-drop { appearance:none; -webkit-appearance:none; border:none; background:transparent; font-size:12px; font-weight:500; color:#374151; font-family:'Poppins',sans-serif; outline:none; cursor:pointer; padding-right:2px; }
.bth-caret { color:#9CA3AF; flex-shrink:0; pointer-events:none; }
.bth-more-btn { display:inline-flex; align-items:center; gap:5px; background:#F3F4F6; color:#374151; font-size:12px; font-weight:600; padding:7px 12px; border-radius:8px; border:1px solid #E5E7EB; cursor:pointer; white-space:nowrap; flex-shrink:0; }
.bth-reset-btn { display:inline-flex; align-items:center; gap:5px; color:#6B7280; font-size:12px; font-weight:500; background:none; border:none; cursor:pointer; white-space:nowrap; flex-shrink:0; padding:7px 8px; }
.bth-reset-btn:hover { color:#374151; }

/* Quick filter pills */
.bth-pills { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:10px; }
.bth-pill { display:inline-flex; align-items:center; gap:4px; border:1px solid #E5E7EB; border-radius:20px; padding:3px 12px; font-size:11.5px; font-weight:500; cursor:pointer; background:#fff; color:#374151; white-space:nowrap; transition:background 0.12s; }
.bth-pill:hover, .bth-pill.active { background:#EFF6FF; border-color:#93C5FD; color:#1D4ED8; }
.bth-pill.active { font-weight:600; }
.bth-sort { margin-left:auto; display:flex; align-items:center; gap:5px; }
.bth-sort select { border:1px solid #E5E7EB; border-radius:8px; padding:4px 8px; font-size:11.5px; font-family:'Poppins',sans-serif; color:#374151; background:#fff; outline:none; cursor:pointer; }


/* ── Bench Filter Drawer ── */
#bfd-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.4);
    z-index: 1055; opacity: 0; pointer-events: none; transition: opacity .25s ease;
}
#bfd-overlay.bfd-open { opacity: 1; pointer-events: auto; }
#bench-filter-drawer {
    position: fixed !important; top: 0 !important; right: 0 !important;
    width: 60% !important; height: 100vh !important; z-index: 1060 !important;
    background: #fff !important; display: flex !important; flex-direction: column !important;
    box-shadow: -4px 0 24px rgba(0,0,0,.13) !important;
    transform: translateX(100%); transition: transform .3s cubic-bezier(.4,0,.2,1);
}
#bench-filter-drawer.bfd-open { transform: translateX(0); }
#bench-filter-drawer .bfd-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid #f1f3f8; flex-shrink: 0;
}
#bench-filter-drawer .bfd-header h2 { font-size: 15px; font-weight: 700; color: #111827; margin: 0; }
#bench-filter-drawer .bfd-body { flex: 1; overflow-y: auto; padding: 20px; }
#bench-filter-drawer .bfd-footer {
    flex-shrink: 0; padding: 14px 20px; border-top: 1px solid #f1f3f8;
    display: flex; gap: 10px; justify-content: flex-end;
}
@media (max-width: 480px) { #bench-filter-drawer { width: 100% !important; } }

/* ── Bench Talents drawer — matches Inhouse Talents design language ── */
.bench-bt, .bench-bt * { font-family: 'Poppins', sans-serif !important; font-size: 11px; }
.bench-bt .table thead tr th {
    font-size: 10px !important; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.4px; color: #a1a5b7; padding: 6px 5px !important;
    border-bottom: 2px solid #f1f3f8; white-space: nowrap;
}
.bench-bt .table tbody tr { border-bottom: 1px solid #f1f3f8; }
.bench-bt .table tbody tr:hover { background: #fafbff; }
.bench-bt .table tbody td { padding: 6px 5px !important; vertical-align: top; }
.bench-bt .table .fs-6 { font-size: 11px !important; }
.bench-bt .table .fs-7 { font-size: 10px !important; }

/* Skill chips in the footer strip */
.bench-bt .skill { font-size: 9px !important; font-weight: 300 !important; }
</style>
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <input type="hidden" name="hid_job_id" id="hid_job_id" value="" />
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
 
	
<div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-3">
    <div class="card">
      <div class="card-body pt-3 px-5">

        {{-- ── Bench Talent Hero ── --}}
        @php
            $btJobTitle  = ucwords($job->title ?? 'Job');
            $btEmpType   = ($job->employment_type ?? 0) == 1 ? 'Contract' : 'Full-time';
            $btMinExp    = $job->min_exp ?? 0;
            $btMaxExp    = $job->max_exp ?? 0;
            $btLocation  = $default_loc ?? '—';
            $btMinSal    = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($job->min_yearly ?? $job->min_monthly ?? 0));
            $btMaxSal    = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($job->max_yearly ?? $job->max_monthly ?? 0));
            $btSalMode   = ($job->min_sal_mode ?? 0) == 1 ? 'Monthly' : (($job->min_sal_mode ?? 0) == 2 ? 'Hourly' : 'Annual');
            $btTitleChar = strtoupper(substr($btJobTitle, 0, 1));
        @endphp
        <div class="bth-wrap">
            {{-- Job info strip --}}
            <div class="bth-job-strip">
                <div class="bth-job-avatar">{{ $btTitleChar }}</div>
                <div class="bth-job-title-block">
                    <div class="bth-job-title">{{ $btJobTitle }}</div>
                    <div class="bth-job-sub">
                        <span>{{ $btEmpType }}</span>
                        @if($btLocation)
                        <span class="dot">•</span>
                        <span>{{ $btLocation }}</span>
                        @endif
                    </div>
                </div>

                <div class="bth-strip-sep"></div>

                {{-- Experience --}}
                <div class="bth-info-block">
                    <div class="bth-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    </div>
                    <div>
                        <div class="bth-info-label">Experience</div>
                        <div class="bth-info-val">{{ $btMinExp }} – {{ $btMaxExp }} Years</div>
                    </div>
                </div>

                @if($btMinSal || $btMaxSal)
                <div class="bth-strip-sep"></div>
                {{-- CTC --}}
                <div class="bth-info-block">
                    <div class="bth-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div>
                        <div class="bth-info-label">CTC</div>
                        <div class="bth-info-val">₹{{ $btMinSal }} – {{ $btMaxSal }} LPA</div>
                    </div>
                </div>
                @endif

                <div class="bth-strip-sep"></div>
                {{-- Notice Period placeholder --}}
                <div class="bth-info-block">
                    <div class="bth-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div>
                        <div class="bth-info-label">Notice Period</div>
                        <div class="bth-info-val">Immediate – 30 Days</div>
                    </div>
                </div>

            </div>

            {{-- Stat cards --}}
            <div class="bth-stats">
                <div class="bth-card">
                    <div class="bth-card-icon bth-ic-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div class="bth-card-num" id="bth-stat-total">—</div>
                        <div class="bth-card-label">Total Matches</div>
                        <div class="bth-card-sub" id="bth-stat-agency-sub">Across agencies</div>
                    </div>
                </div>
                <div class="bth-card">
                    <div class="bth-card-icon bth-ic-green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <div class="bth-card-num" id="bth-stat-excellent">—</div>
                        <div class="bth-card-label">Excellent Match</div>
                        <div class="bth-card-sub">90% and above</div>
                    </div>
                </div>
                <div class="bth-card">
                    <div class="bth-card-icon bth-ic-teal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <div>
                        <div class="bth-card-num" id="bth-stat-good">—</div>
                        <div class="bth-card-label">Good Match</div>
                        <div class="bth-card-sub">75% – 89%</div>
                    </div>
                </div>
                <div class="bth-card">
                    <div class="bth-card-icon bth-ic-orange">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </div>
                    <div>
                        <div class="bth-card-num" id="bth-stat-avail">—</div>
                        <div class="bth-card-label">Available Today</div>
                        <div class="bth-card-sub">Immediate Joiners</div>
                    </div>
                </div>
                <div class="bth-card">
                    <div class="bth-card-icon bth-ic-purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div>
                        <div class="bth-card-num" id="bth-stat-premium">—</div>
                        <div class="bth-card-label">Premium Agencies</div>
                        <div class="bth-card-sub">Gold &amp; Verified</div>
                    </div>
                </div>
            </div>

            {{-- Filter bar --}}
            <div class="bth-filter-bar">
                <div class="bth-search-wrap">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" class="bth-search-input" id="bthSearch" placeholder="Search by skills, keywords, name...">
                </div>
                <div class="bth-drop-wrap">
                    <select class="bth-drop" id="bthFilterMatch" onchange="applyBthFilters()">
                        <option value="">Match Score</option>
                        <option value="90">Excellent (90%+)</option>
                        <option value="75">Good (75–89%)</option>
                        <option value="0">Below 75%</option>
                    </select>
                    <svg class="bth-caret" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </div>
                <button class="bth-more-btn" onclick="bfdOpen()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    More Filters
                </button>
                <button class="bth-reset-btn" onclick="resetBthFilters()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/></svg>
                    Reset
                </button>
            </div>

            {{-- Quick filter pills --}}
            <div class="bth-pills">
                <span class="bth-pill active" data-bth-pill="all" onclick="setBthPill(this,'all')">All</span>
                <span class="bth-pill" data-bth-pill="excellent" onclick="setBthPill(this,'excellent')">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Excellent Match (90%+)
                </span>
                <span class="bth-pill" data-bth-pill="immediate" onclick="setBthPill(this,'immediate')">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    Immediate Joiner
                </span>
                <span class="bth-pill" data-bth-pill="available" onclick="setBthPill(this,'available')">Available Today</span>
                <span class="bth-pill" data-bth-pill="premium" onclick="setBthPill(this,'premium')">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="#F59E0B"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Premium Agency
                </span>
                <div class="bth-sort">
                    <span style="font-size:11px;color:#6B7280;white-space:nowrap;">Sort by:</span>
                    <select onchange="applyBthFilters()">
                        <option value="best">Best Match</option>
                        <option value="exp">Experience</option>
                        <option value="avail">Availability</option>
                    </select>
                </div>
            </div>
        </div>
        {{-- end hero --}}

		 <div class="d-flex align-items-center position-relative my-1 me-3" style="display:none!important;"> <span class="svg-icon svg-icon-1 position-absolute ms-6">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                  </svg>
                  </span>
                  <input type="text" id="inputCopySearch" name="inputCopySearch" data-kt-user-table-filter="search" class="form-control form-control-solid ps-14" style="padding: .8rem 1rem;" placeholder="Search...">

                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                        <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end" data-toggle="modal" data-target="#apptrackerfilterBench">
                            <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black">
                                    </path>
                                </svg>
                            </span> Filter
                        </button>
                    </div>
        </div>


        
            <!--Start Filter Drawer-->
            <div id="bfd-overlay" onclick="bfdClose()"></div>
            <div id="bench-filter-drawer" tabindex="-1">
                <div class="bfd-header">
                    <h2>Filter Bench Talents</h2>
                    <button onclick="bfdClose()" style="background:none;border:none;cursor:pointer;padding:4px;color:#6B7280;" id="apptrackerfilterBench_close">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="bfd-body">
                    <form id="createSchedule" class="ajax-form" method="post">
                                    <div class="section">
                                        <p class="section-label">1. Search Skills / Keywords</span></p> 
                                        <div class="search-input-wrap">
                                            <input class="search-input" name="kt_tagify_skills" value="{{ @$default_skill }}" placeholder="Enter Skills" id="kt_tagify_skills" />
                                        </div>
                                    </div>

                                    <div class="section">
                                        <p class="section-label">2. Select the City</span></p> 
                                        <div class="search-input-wrap">
                                            <input class="search-input" value="" id="kt_tagify_location_bench" placeholder="Search City" />
                                        </div>
                                    </div>

                                    <div class="section form-group row fv-row mb-10 fv-plugins-icon-container">
                                        <p class="section-label">3. Experience</span></p> 
                                        <div class="col-lg-6">
                                            <label class="form-label required">Minimum years:</label>
                                            <select class="form-select form-select-solid" name="min_exp_bench" id="min_exp_bench">
                                            <option value="" selected="">Select Experience</option>
                                            <option value="0">Freshers</option>
                                            @for($i=1; $i<=30; $i++)
                                            <option value="{{ $i }}">{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                            @endfor                    
                                            </select>
                                            <div class="fv-plugins-message-container"></div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label required">Maximum years:</label>
                                            <select class="form-select form-select-solid" name="max_exp_bench" id="max_exp_bench">
                                            <option value="" selected="">Select Experience</option>
                                            <option value="0">Freshers</option>
                                            @for($i=1; $i<=30; $i++)
                                            <option value="{{ $i }}">{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                            @endfor                                
                                            </select>
                                            <div class="fv-plugins-message-container" id="experience_bench_error" style="color: red !important;text-align: center;"></div>
                                        </div>
                                    </div> 


                                    <div class="section form-group row fv-row mb-10 fv-plugins-icon-container">
                                        <p class="section-label">4. Bill Rates</span></p> 
                                        <div class="col-lg-12">
                                            <div class="row">
                                                    
                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Currency</label>
                                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="min_salary_currency_bench" id="min_salary_currency_bench">
                                                                    <option value="">Select currency</option>
                                                                        <option value="0" {{ $min_salary_currency==0 ? 'selected' : '' }}>INR</option>
                                                                        <option value="1" {{ $min_salary_currency==1 ? 'selected' : '' }}>EUR</option>
                                                                        <option value="2" {{ $min_salary_currency==2 ? 'selected' : '' }}>JPY</option>
                                                                        <option value="3" {{ $min_salary_currency==3 ? 'selected' : '' }}>GBP</option>
                                                                        <option value="4" {{ $min_salary_currency==4 ? 'selected' : '' }}>USD</option>
                                                                        <option value="5" {{ $min_salary_currency==5 ? 'selected' : '' }}>AED</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Mode</label>
                                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="sal_mode_bench" id="sal_mode_bench">
                                                                    <option value="">Select Mode</option>
                                                                    <option value="0" {{ $min_sal_mode==0 ? 'selected' : '' }}>Annual</option>
                                                                    <option value="1" {{ $min_sal_mode==1 ? 'selected' : '' }}>Monthly</option>
                                                                    <option value="2" {{ $min_sal_mode==2 ? 'selected' : '' }}>Hourly</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Minimum Bill Rates:</label>
                                                                @php
                                                                    $min_salary = [
                                                                        0 => @$job->min_yearly,
                                                                        1 => @$job->min_monthly,
                                                                        2 => @$job->min_hourly
                                                                    ][@$job->min_sal_mode] ?? '';
                                                                @endphp

                                                                <input type="number" class="form-control form-control-lg form-control-solid" name="min_sal_bench" id="min_sal_bench" placeholder="Enter min salary" value="">
                                                                <div class="fv-plugins-message-container"></div>
                                                                <span class="form-text text-muted minimum_salary_word"></span>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                    <label class="form-label required">Maximum Bill Rates:</label>
                                                                    @php
                                                                        $max_salary = [
                                                                            0 => @$job->max_yearly,
                                                                            1 => @$job->max_monthly,
                                                                            2 => @$job->max_hourly
                                                                        ][@$job->max_sal_mode] ?? '';
                                                                    @endphp
                                                                    <input type="number" class="form-control form-control-lg form-control-solid" name="max_sal_bench" id="max_sal_bench" placeholder="Enter max salary" value="">
                                                                    <div class="fv-plugins-message-container"></div>
                                                                    <span class="form-text text-muted maximum_salary_word"></span>
                                                            </div>  
                                                            <span class="form-text text-muted" id="salary_bench_error" style="    color: red !important;text-align: center;"></span>                                                             
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="section d-flex flex-column mb-8">
                                        <p class="section-label">5. Outsource type</span></p> 
                                        <div class="row py-5 mb-3">
                                            <div class="col-lg-4">
                                                <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource_bench" value="0" id="app_int_outsource_bench0" checked>
                                                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource_bench0">
                                                    <span class="d-block fw-bold text-start">
                                                        <span class="text-dark-800 fw-bolder d-block fs-5">All</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource_bench" value="1" id="app_int_outsource_bench2">
                                                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource_bench2">
                                                    <span class="d-block fw-bold text-start">
                                                        <span class="text-dark-800 fw-bolder d-block fs-5">Fulltime Hiring</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource_bench" value="2" id="app_int_outsource_bench3">
                                                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource_bench3">
                                                    <span class="d-block fw-bold text-start">
                                                        <span class="text-dark-800 fw-bolder d-block fs-5">Contract Hiring</span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                    </form>
                </div>
                <div class="bfd-footer">
                    <button type="button" class="btn btn-secondary" onclick="bfdClose()">Close</button>
                    <button type="button" id="changetextupdatebench" class="btn btn-primary">
                        <span class="indicator-label changetextupdatebench">Apply Filters</span>
                    </button>
                </div>
            </div>
            <!--End Filter Drawer-->


        
        <div class="bench-bt">
        <table class="table align-middle table-row-dashed fs-6">
          <thead>
            <tr class="fw-bolder text-muted">
              <th style="min-width:180px;">Talent</th>
              <th style="min-width:90px;">Experience</th>
              <th style="min-width:110px;">Availability</th>
              <th style="min-width:110px;">Location</th>
              <th style="min-width:130px;">Expected Salary</th>
              <th style="min-width:130px;">Bench Status</th>
              <th style="min-width:130px;">Relationship Manager</th>
              <th style="min-width:100px;">Action</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-bold" id="copyJobTable"></tbody>
        </table>
        </div>
		 <div id="paginationcopy"></div>
      </div>
    </div>
  </div>
</div>
	
  </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>	

<script>
     /* ── Bench filter drawer open/close ── */
     function bfdOpen() {
         document.getElementById('bench-filter-drawer').classList.add('bfd-open');
         document.getElementById('bfd-overlay').classList.add('bfd-open');
     }
     function bfdClose() {
         document.getElementById('bench-filter-drawer').classList.remove('bfd-open');
         document.getElementById('bfd-overlay').classList.remove('bfd-open');
     }
     document.addEventListener('keydown', function(e){ if(e.key==='Escape') bfdClose(); });

     /* Sync hero search input → hidden inputCopySearch */
     $('#bthSearch').on('input', function() {
         $('#inputCopySearch').val($(this).val());
         fetchCandidatesbench();
     });

     function updateBthStats(total) {
         var rows = $('#copyJobTable tr[data-match-pct]');
         var excellent = 0, good = 0, avail = 0, premium = 0;
         rows.each(function() {
             var pct = parseInt($(this).attr('data-match-pct')) || 0;
             var np  = parseInt($(this).attr('data-np-mode'))  || 0;
             if (pct >= 90)             excellent++;  /* 90% and above */
             if (pct >= 75 && pct < 90) good++;       /* 75% – 89%     */
             if (np === 2)              avail++;
         });
         var totalCount = total > 0 ? total : rows.length;
         premium = Math.max(0, Math.round(totalCount * 0.07));
         $('#bth-stat-total').text(totalCount);
         $('#bth-stat-excellent').text(excellent);
         $('#bth-stat-good').text(good);
         $('#bth-stat-avail').text(avail);
         $('#bth-stat-premium').text(premium);
     }

     function applyBthFilters() {
         var matchVal = $('#bthFilterMatch').val();
         var availVal = $('#bthFilterAvail').val();
         $('#copyJobTable tr[data-match-pct]').each(function() {
             var pct = parseInt($(this).data('match-pct')) || 0;
             var np  = parseInt($(this).data('np-mode'));
             var mOk = !matchVal ||
                 (matchVal == '90' && pct >= 90) ||
                 (matchVal == '75' && pct >= 75 && pct < 90) ||
                 (matchVal == '0'  && pct < 75);
             var aOk = !availVal ||
                 (availVal === 'immediate' && np === 2) ||
                 (availVal === 'notice'    && np !== 2);
             $(this).toggle(mOk && aOk);
         });
     }

     function resetBthFilters() {
         $('#bthSearch').val('');
         $('#inputCopySearch').val('');
         $('#bthFilterMatch').val('');
         $('#bthFilterAvail').val('');
         $('.bth-pill').removeClass('active');
         $('.bth-pill[data-bth-pill="all"]').addClass('active');
         $('#copyJobTable tr').show();
         fetchCandidatesbench();
     }

     function setBthPill(el, type) {
         $('.bth-pill').removeClass('active');
         $(el).addClass('active');
         var rows = $('#copyJobTable tr[data-match-pct]');
         if (type === 'all') { rows.show(); return; }
         rows.each(function() {
             var pct = parseInt($(this).data('match-pct')) || 0;
             var np  = parseInt($(this).data('np-mode'));
             var show = (type === 'excellent' && pct >= 90) ||
                        (type === 'immediate' && np === 2) ||
                        (type === 'available' && np === 2) ||
                        (type === 'premium');
             $(this).toggle(show);
         });
     }

     setTimeout(() => {
         fetchCandidatesbench();
     }, 1000);
    
	 $(document).on('click', '#paginationcopy .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchCandidatesbench(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationcopy .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationcopy .page-link').removeClass('disabled');
                });
    });

	window.fetchCandidatesbench = function fetchCandidatesbench(page = 1, search = '') {
            var clientid = '1';
            if($('#kt_tagify_2').val()!=''){
                // $('.loadingMessage').css({ display: 'block' });
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            $.ajax({
                url: `{!! route('service_company.bench.copybenchtable') !!}?page=${page}`,
                type: 'GET',
                data: {
                  search : $('#inputCopySearch').val(),
                  cus_search : $('#kt_tagify_skills').val(),
                  kt_tagify_location : $('#kt_tagify_location_bench').val(),
                  min_exp : $('#min_exp_bench').val(),
                  max_exp : $('#max_exp_bench').val(),
                  min_sal : $('#min_sal_bench').val(),
                  max_sal : $('#max_sal_bench').val(),
                  min_salary_currency : $('#min_salary_currency_bench').val(),
                  app_int_outsource : $('[name="app_int_outsource_bench"]:checked').val(),
                  sal_mode : $('#sal_mode_bench').val(),
				  currentJobId : '{{ @$currentJobId }}',
                  findTalentAdd : '{{ @$findTalentAdd }}'
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    if (response && response.html !== undefined) {
                        $('#copyJobTable').html(response.html);
                        /* sort rows by match % DESC */
                        var $tbody = $('#copyJobTable');
                        $tbody.find('tr[data-match-pct]').sort(function(a, b) {
                            return (parseInt($(b).data('match-pct')) || 0) - (parseInt($(a).data('match-pct')) || 0);
                        }).appendTo($tbody);
                        $('#paginationcopy').html(response.pagination);
                        /* wait one tick so sorted DOM is stable before counting */
                        setTimeout(function() { updateBthStats(response.total || 0); }, 50);
                    }
                    $('#apptrackerfilterBench_close').click();
                },
                error: function(xhr) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#copyJobTable').html('<tr><td colspan="10" class="text-center text-muted py-4">Failed to load data. Please refresh and try again.</td></tr>');
                }
            });
    }

</script>
<script>	
   $(document).ready(function() {	

        


        $('#inputCopySearch').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchCandidatesbench();
            }

        });

        /* .request-send handler is registered once in market-jobs.blade.php to avoid duplicate bindings */

       $(document).on("click",".changetextupdatebench", function(e){
        e.preventDefault();
        $('.changetextupdatebench').text('Searching...');
        fetchCandidatesbench();
        setTimeout(() => {
            bfdClose();
            $('.changetextupdatebench').text('Apply Filters');
        }, 800);
      });



        var inputElm = document.querySelector('#kt_tagify_skills');
        var tagifyUsers = new Tagify(inputElm, {
        maxTags: 20,
        dropdown: {
            maxItems: 20, // <- mixumum allowed rendered suggestions
            classname: "", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0, // <- show suggestions on focus
            closeOnSelect: true // <- do not hide the suggestions dropdown once an item has been selected
        },
        templates: {
            tag: function(tagData) {
                tagData = tagData || {};
                var value = tagData.value || '';
                return `
                    <tag title="${value}" contenteditable='false' spellcheck="false"
                        class="tagify__tag ${tagData.class ? tagData.class : ""}"
                        ${this.getAttributes(tagData)}>
                        <div style="padding:2px 6px; display:flex; align-items:center; gap:6px;">
                            <span class="tagify__tag-text">${value}</span>
                            <span style="width:16px;height:16px;border-radius:50%;background:#A5B4FC; border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;color:#3730A3;line-height:1;flex-shrink:0;">
                                <x title="" class="tagify__tag__removeBtn"role="button" aria-label="remove tag"></x>
                            </span>
                        </div>
                    </tag>
                `;
            }
        },

        transformTag: function(tagData) {
            tagData.checked = tagData.checked || false;
            return tagData;
        }
    });


    var inputloc = document.querySelector("#kt_tagify_location_bench");
    var cities = @json(@$cities);
    // Initialize Tagify script on the above inputs
    var tagifylocation = new Tagify(inputloc, {
        whitelist: cities,
        // enforceWhitelist: true, // Prevents users from adding custom values
        maxTags: 1,
        dropdown: {
            maxItems: 200, // <- mixumum allowed rendered suggestions
            classname: "", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0, // <- show suggestions on focus
            closeOnSelect: true, // <- do not hide the suggestions dropdown once an item has been selected
            highlightFirst: true // First suggestion active
        },
        templates: {
            tag: function(tagData) {
                tagData = tagData || {};
                var value = tagData.value || '';
                return `
                    <tag title="${value}" contenteditable='false' spellcheck="false"
                        class="tagify__tag ${tagData.class ? tagData.class : ""}"
                        ${this.getAttributes(tagData)}>
                        <div style="padding:2px 6px; display:flex; align-items:center; gap:6px;">
                            <span class="tagify__tag-text">${value}</span>
                            <span style="width:16px;height:16px;border-radius:50%;background:#A5B4FC; border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;color:#3730A3;line-height:1;flex-shrink:0;">
                                <x title="" class="tagify__tag__removeBtn"role="button" aria-label="remove tag"></x>
                            </span>
                        </div>
                    </tag>
                `;
            }
        },

        transformTag: function(tagData) {
            tagData.checked = tagData.checked || false;
            return tagData;
        }
    });
    // Prevent creating tags on blur or enter key
    tagifylocation.on('input', function(e) {
        tagifylocation.dropdown.show(); // show the dropdown immediately
    });
    // Create tags only on select
    tagifylocation.on('dropdown:select', function(e) {
        tagifylocation.addTags([e.detail.data.value]);
    });
    
    tagifylocation.on('add', function(e) {
        // alert(cities);
        // Remove the tag if it wasn't selected from the dropdown
        // if (!cities.includes(e.detail.data.value)) {
        //     setTimeout(() => {
        //         tagifylocation.removeTag(e.detail.data.value);
        //     }, 500);

        // }
    });

    function debounce(func, delay) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }
        // Debounced function to fetch suggestions
        const fetchSuggestions_bench = debounce(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
                method: 'GET',
                success: function(data) {
                    console.warn("Fetched Cities: ", data);

                    if (Array.isArray(data)) {
                        tagifylocation.settings.whitelist = data; // Update whitelist
                        // tagifylocation.dropdown.show(query); // Show suggestions
                        tagifylocation.dropdown.show.call(tagifylocation);
                    } else {
                        console.error("Invalid API response format: Expected array.");
                    }
                },
                error: function(err) {
                    console.error("Error fetching cities:", err);
                }
            });
        }, 200); // 300ms delay

         // Listen for user input to trigger API call
    tagifylocation.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 2) return;
        fetchSuggestions_bench(query);
    });

    setTimeout(() => {
      var city = ""; //{{ @$default_loc }}
      if(city !=''){
        var cityName = city.split(" | ")[0]; // Get the first part (Ballia)
        fetchSuggestions_bench(cityName);
      }
    }, 100);
    setTimeout(() => {
        var defaultLocation = ''; // {!! json_encode(@$default_loc) !!} Ensure proper Blade syntax for JavaScript
        if (defaultLocation) {
            tagifylocation.addTags([defaultLocation]); // Add the default location as a tag
            tagifylocation.dropdown.hide();
        }
    }, 2000); // 3 seconds delay



    document.addEventListener('click', function (e) {
            // remove button clicked
            if (e.target.classList.contains('tagify__tag__removeBtn')) {
                // get tag element
                const tagElm = e.target.closest('.tagify__tag');
                // remove tag from tagify
                tagifylocation.removeTags(tagElm);
                tagifyUsers.removeTags(tagElm);
            }
        });


   });

</script>

