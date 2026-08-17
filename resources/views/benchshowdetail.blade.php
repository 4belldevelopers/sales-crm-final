@php
    use App\Helper\GeneralMethod;
@endphp
<style>
    .text-justify { text-align: justify !important; }
    .iti__flag { margin-top: 4px !important; }
    #kt_activitiesBenchApplication_close svg { color: #ef4444; }
</style>
<link href="{{ asset('demo/css/candidates-details.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

<style>
/* ===== THREE-CARD LAYOUT & STYLING FOR BENCH CANDIDATE DRAWER ===== */
#kt_activitiesBenchApplication { overflow-y: auto !important; overflow-x: hidden !important; max-height: 100vh !important; height: 100vh !important; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
#bench_application_show_right_bar { min-height: 100%; height: auto !important; overflow: visible !important; }
#kt_activitiesBenchApplication .page-wrapper { display: flex; flex-direction: column; gap: 12px; background: #f1f5f9; min-height: 100%; padding-bottom: 60px !important; font-family: 'Poppins', sans-serif; overflow-y: visible !important; }
#kt_activitiesBenchApplication .cdr-outer-card,
#kt_activitiesBenchApplication .card,
#kt_activitiesBenchApplication .content-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px !important; box-shadow: 0 1px 3px rgba(15,23,42,.04), 0 4px 12px rgba(15,23,42,.03); overflow: hidden; flex-shrink: 0; margin: 0 16px; }

/* ===== TOP ACTION BAR ===== */
#kt_activitiesBenchApplication .cdr-topbar { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #fff; border-bottom: 1px solid #e2e8f0; margin-bottom: 4px; }
#kt_activitiesBenchApplication .cdr-topbar-group { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
#kt_activitiesBenchApplication .cdr-action-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 12px; font-weight: 600; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #334155; text-decoration: none; transition: all 0.15s ease; cursor: pointer; }
#kt_activitiesBenchApplication .cdr-btn-primary { background: #4f46e5; color: #fff; border-color: #4f46e5; }
#kt_activitiesBenchApplication .cdr-topbar-close { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #e2e8f0; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease; padding: 0; }
#kt_activitiesBenchApplication .cdr-topbar-close:hover { background: #fee2e2; border-color: #fca5a5; }

/* ===== CARD 1: HERO IDENTITY + METRICS ===== */
#kt_activitiesBenchApplication .cdr-hero-card { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 16px; padding: 18px; align-items: start; }
@media (max-width: 900px) { #kt_activitiesBenchApplication .cdr-hero-card { grid-template-columns: 1fr; } }
#kt_activitiesBenchApplication .cdr-subcard-id { display: flex; flex-direction: column; gap: 10px; }
#kt_activitiesBenchApplication .cdr-id-top { display: flex; gap: 14px; align-items: flex-start; }
#kt_activitiesBenchApplication .cdr-avatar-wrap { position: relative; flex-shrink: 0; }
#kt_activitiesBenchApplication .cdr-avatar-sq { width: 68px; height: 68px; border-radius: 12px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; font-size: 24px; font-weight: 700; display: flex; align-items: center; justify-content: center; overflow: hidden; }
#kt_activitiesBenchApplication .cdr-avatar-sq img { width: 100%; height: 100%; object-fit: cover; }
#kt_activitiesBenchApplication .cdr-avatar-online { position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; border-radius: 50%; background: #22c55e; border: 2.5px solid #fff; }
#kt_activitiesBenchApplication .cdr-id-body { flex: 1; min-width: 0; }
#kt_activitiesBenchApplication .cdr-name-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 2px; }
#kt_activitiesBenchApplication .cdr-name { font-size: 16px !important; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.3; }
#kt_activitiesBenchApplication .cdr-status-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 12px; background: #e0e7ff; color: #4338ca; text-transform: uppercase; letter-spacing: 0.03em; }
#kt_activitiesBenchApplication .cdr-role { font-size: 12px !important; color: #64748b; margin: 0 0 8px 0; font-weight: 500; }
#kt_activitiesBenchApplication .cdr-meta-grid { display: flex; flex-wrap: wrap; gap: 10px 16px; font-size: 11.5px; color: #475569; }
#kt_activitiesBenchApplication .cdr-meta-item { display: inline-flex; align-items: center; gap: 5px; }
#kt_activitiesBenchApplication .cdr-meta-item svg { width: 14px; height: 14px; color: #94a3b8; flex-shrink: 0; }
#kt_activitiesBenchApplication .cdr-skills-bar { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; padding-top: 10px; border-top: 1px solid #f1f5f9; }
#kt_activitiesBenchApplication .cdr-skill-tag { display: inline-flex; align-items: center; font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; background: #f0fdf4; color: #15803d; border: 1px solid #86efac; }
#kt_activitiesBenchApplication .social-links-bar { display: flex; align-items: center; gap: 10px; margin-top: 6px; }

/* Sub-Card B Metrics List */
#kt_activitiesBenchApplication .cdr-subcard-metrics { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; }
#kt_activitiesBenchApplication .cdr-metric-list { display: flex; flex-direction: column; gap: 10px; }
#kt_activitiesBenchApplication .cdr-metric-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12px; }
#kt_activitiesBenchApplication .cdr-metric-label { font-weight: 500; color: #64748b; }
#kt_activitiesBenchApplication .cdr-metric-val { font-weight: 700; color: #0f172a; }

/* ===== CARD 3: TABS & CONTENT ===== */
#kt_activitiesBenchApplication .cdr-tabs-card .page-tabs { display: flex; border-bottom: 1px solid #e2e8f0; background: #fff; padding: 0 16px; gap: 20px; }
#kt_activitiesBenchApplication .cdr-tabs-card .tab-btn { padding: 12px 4px; font-size: 12.5px; font-weight: 600; color: #64748b; border-bottom: 2px solid transparent; text-decoration: none; transition: all 0.15s ease; display: inline-flex; align-items: center; gap: 6px; }
#kt_activitiesBenchApplication .cdr-tabs-card .tab-btn:hover { color: #4f46e5; }
#kt_activitiesBenchApplication .cdr-tabs-card .tab-btn.active { color: #4f46e5; border-bottom-color: #4f46e5; }
#kt_activitiesBenchApplication #personal_details .content-split { display: grid !important; grid-template-columns: minmax(0, 1fr) 340px; gap: 16px; align-items: start; padding: 16px; }
@media (max-width: 860px) { #kt_activitiesBenchApplication #personal_details .content-split { grid-template-columns: 1fr !important; } }

/* Grid Details Rows */
#kt_activitiesBenchApplication .detail-box { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px; padding: 10px 12px; }
#kt_activitiesBenchApplication .detail-lbl { font-size: 10.5px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 3px; }
#kt_activitiesBenchApplication .detail-val { font-size: 12.5px; font-weight: 600; color: #0f172a; word-break: break-word; }

/* Side Commercial Card */
#kt_activitiesBenchApplication .side-summary-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; display: flex; flex-direction: column; gap: 14px; }
#kt_activitiesBenchApplication .side-card-title { font-size: 13px; font-weight: 700; color: #0f172a; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9; }
</style>

@php
    $resume_url = '<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';
    $photo = url('public/avatar.png');
    if (!empty($benchApplication->photo)) {
        $photo = url('/public/user-uploads/bench_photos/' . $benchApplication->photo);
    }
    $country_id = $country_name = $state_name = $state = $city_name = $posted_by_company = '';
    $country_id = @$benchApplication->country;
    $state = @$benchApplication->state;
    $country_name_Arr = GeneralMethod::getCountryName($country_id);
    foreach ($country_name_Arr as $cval) {
        $country_name = ucwords($cval['name']);
    }

    if ($country_id == 101) {
        $state_name = @$benchApplication->state_name;
        $city_name = @$benchApplication->city_name;
    } else {
        if ($state > 0) {
            $state_name_Arr = GeneralMethod::getStateName($country_id, $state);
            foreach ($state_name_Arr as $sval) {
                $state_name = ucwords($sval['name']);
            }
        }
        $city_name = @$benchApplication->city;
    }
    $currencySymbole = GeneralMethod::currencySymbole();
    $current_ctc_cur = $current_ectc_cur = '';
    $current_ctc_cur = $currencySymbole[(@$benchApplication->pay_rate_currency) ? @$benchApplication->pay_rate_currency : 0];
    $current_ectc_cur = $currencySymbole[(@$benchApplication->bill_rate_currency) ? @$benchApplication->bill_rate_currency : 0];
    $posted_by_company = @$benchApplication->company_name;

    // Derived values for presentation
    $fullName = ucwords(@$benchApplication->full_name ?: 'Candidate');
    $parts = preg_split('/\s+/', trim($fullName));
    $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    if (!$initials) $initials = 'CA';

    // Notice Period formatting
    $npDisplay = @$benchApplication->np_id ?: 'N/A';
    if ($benchApplication->serving_notice_period_mode == 0 && !empty($benchApplication->np_id)) {
        $npDisplay = $benchApplication->np_id . ($benchApplication->np_id == 1 ? ' Day' : ' Days');
    } elseif ($benchApplication->serving_notice_period_mode == 1 && !empty($benchApplication->np_id)) {
        $npDisplay = ($benchApplication->np_id === 'Immediate') ? 'Immediate' : 'Available from ' . GeneralMethod::ymdTodmyNotTime($benchApplication->np_id);
    }

    // Experience formatting
    $totalExpRaw = trim((string) @$benchApplication->total_exp);
    $totalExp = (strpos($totalExpRaw, '.') !== false && substr($totalExpRaw, -3) === '.00') ? substr($totalExpRaw, 0, -3) : $totalExpRaw;
    if (!$totalExp) $totalExp = '0';

    $relExpRaw = trim((string) @$benchApplication->rel_exp);
    $relExp = (strpos($relExpRaw, '.') !== false && substr($relExpRaw, -3) === '.00') ? substr($relExpRaw, 0, -3) : $relExpRaw;
    if (!$relExp) $relExp = '0';

    $locationStr = trim(implode(', ', array_filter([ucwords(@$city_name), ucwords(@$state_name), ucwords(@$country_name)])));
    if (!$locationStr) $locationStr = 'N/A';
@endphp

<div class="page-wrapper" id="kt_activitiesBenchApplication_body">

    {{-- ════ TOP ACTION BAR ════ --}}
    <div class="cdr-topbar">
        <div class="cdr-topbar-group">
            <span style="font-size:14px;font-weight:700;color:#0f172a;">Profile Details</span>
        </div>
        <button type="button" class="cdr-topbar-close" id="kt_activitiesBenchApplication_close" aria-label="Close">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- ════ CARD 1: HERO IDENTITY + KEY METRICS ════ --}}
    <div class="cdr-outer-card">
        <div class="cdr-hero-card">
            {{-- Left Identity Sub-card --}}
            <div class="cdr-subcard-id">
                <div class="cdr-id-top">
                    <div class="cdr-avatar-wrap">
                        <div class="cdr-avatar-sq">
                            @if(!empty($benchApplication->photo))
                                <img src="{{ $photo }}" alt="{{ $fullName }}">
                            @else
                                {{ $initials }}
                            @endif
                        </div>
                        <div class="cdr-avatar-online" title="Active"></div>
                    </div>
                    <div class="cdr-id-body">
                        <div class="cdr-name-row">
                            <h2 class="cdr-name">{{ $fullName }}</h2>
                            <span class="cdr-status-badge">{{ @$benchApplication->master_status_name ?: 'BENCH' }}</span>
                        </div>
                        <p class="cdr-role">{{ ucwords(@$benchApplication->current_designation ?: 'Software Engineer') }}</p>
                        
                        <div class="cdr-meta-grid">
                            <span class="cdr-meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                {{ $totalExp }} Yrs Experience
                            </span>
                            <span class="cdr-meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $locationStr }}
                            </span>
                            @if(!empty($posted_by_company))
                                <span class="cdr-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                                    {{ ucwords($posted_by_company) }}
                                </span>
                            @endif
                            <span class="cdr-meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $npDisplay }}
                            </span>
                        </div>

                        {{-- Social Links --}}
                        <div class="social-links-bar">
                            @if(@$benchApplication->linkedin)
                                <a href="{{ @$benchApplication->linkedin }}" target="_blank" title="LinkedIn"><i class="fab fa-linkedin" style="font-size:18px; color:#0A66C2;"></i></a>
                            @endif
                            @if(@$benchApplication->github)
                                <a href="{{ @$benchApplication->github }}" target="_blank" title="GitHub"><i class="fab fa-github" style="font-size:18px; color:#171515;"></i></a>
                            @endif
                            @if(@$benchApplication->behance)
                                <a href="{{ @$benchApplication->behance }}" target="_blank" title="Behance"><i class="fab fa-behance" style="font-size:18px; color:#1769FF;"></i></a>
                            @endif
                            @if(@$benchApplication->otherLink)
                                <a href="{{ @$benchApplication->otherLink }}" target="_blank" title="Other Link"><i class="fa fa-link" style="font-size:16px; color:#6c757d;"></i></a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Skill Chips Bar --}}
                @if(!empty($benchApplication->skills))
                    @php $skillsArr = array_filter(array_map('trim', explode(',', $benchApplication->skills))); @endphp
                    @if(count($skillsArr) > 0)
                        <div class="cdr-skills-bar">
                            @foreach(array_slice($skillsArr, 0, 8) as $sval)
                                <span class="cdr-skill-tag">{{ ucwords($sval) }}</span>
                            @endforeach
                            @if(count($skillsArr) > 8)
                                <span class="cdr-skill-tag" style="background:#f1f5f9;color:#64748b;border-color:#e2e8f0;">+{{ count($skillsArr) - 8 }} More</span>
                            @endif
                        </div>
                    @endif
                @endif
            </div>

            {{-- Right Sub-card: Key Metrics Summary --}}
            <div class="cdr-subcard-metrics">
                <div class="cdr-metric-list">
                    <div class="cdr-metric-row">
                        <span class="cdr-metric-label">Total Experience</span>
                        <span class="cdr-metric-val">{{ $totalExp }} Years</span>
                    </div>
                    <div class="cdr-metric-row">
                        <span class="cdr-metric-label">Relevant Exp</span>
                        <span class="cdr-metric-val">{{ $relExp }} Years</span>
                    </div>
                    <div class="cdr-metric-row">
                        <span class="cdr-metric-label">Bill Rate</span>
                        <span class="cdr-metric-val">{!! $current_ectc_cur !!} {{ @$benchApplication->bill_rate_amount ?: '—' }}</span>
                    </div>
                    <div class="cdr-metric-row">
                        <span class="cdr-metric-label">Notice Period</span>
                        <span class="cdr-metric-val">{{ $npDisplay }}</span>
                    </div>
                    <div class="cdr-metric-row">
                        <span class="cdr-metric-label">Qualification</span>
                        <span class="cdr-metric-val">{{ ucwords(@$benchApplication->qualification_name ?: 'N/A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ CARD 2: PROFILE OVERVIEW ════ --}}
    <div class="cdr-outer-card cdr-tabs-card">
        <nav class="page-tabs" role="tablist">
            <a href="#personal_details" class="tab-btn active" data-toggle="tab" role="tab">
                <i class="fa-regular fa-user"></i> Profile Overview
            </a>
        </nav>

        <div class="tab-content">
            {{-- TAB 1: Profile Overview --}}
            <div class="tab-pane active" id="personal_details" role="tabpanel">
                <div class="content-split">
                    {{-- Main Left Details Grid --}}
                    <div>
                        <h3 class="fw-bolder fs-6 mb-4 text-dark" style="font-size:14px !important;">Personal & Professional Details</h3>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Full Name</div>
                                    <div class="detail-val">{{ $fullName }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Designation</div>
                                    <div class="detail-val">{{ ucwords(@$benchApplication->current_designation ?: 'N/A') }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Company</div>
                                    <div class="detail-val">{{ ucwords(@$benchApplication->company_name ?: 'N/A') }}</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Total Experience</div>
                                    <div class="detail-val">{{ $totalExp }} Years</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Relevant Experience</div>
                                    <div class="detail-val">{{ $relExp }} Years</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Qualification</div>
                                    <div class="detail-val">{{ ucwords(@$benchApplication->qualification_name ?: 'N/A') }}</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Notice Period</div>
                                    <div class="detail-val">{{ $npDisplay }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Date of Birth</div>
                                    <div class="detail-val">
                                        {{ !empty($benchApplication->dob) ? date('d-M-Y', strtotime($benchApplication->dob)) : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Employee ID</div>
                                    <div class="detail-val">{{ @$benchApplication->employee_number ?: 'N/A' }}</div>
                                </div>
                            </div>

                            @if(@$benchApplication->status_type == 'DIRECT')
                                <div class="col-md-4">
                                    <div class="detail-box">
                                        <div class="detail-lbl">Current Salary</div>
                                        <div class="detail-val">
                                            {!! $current_ctc_cur !!}{{ @$benchApplication->ctc }}
                                            @php
                                                $ctc_mode = @$benchApplication->current_ctc_mode ?: 0;
                                                echo @$salaryMode[$ctc_mode];
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-box">
                                        <div class="detail-lbl">Expected Salary</div>
                                        <div class="detail-val">
                                            {!! $current_ectc_cur !!}{{ @$benchApplication->ectc }}
                                            @php
                                                $ectc_mode = @$benchApplication->current_ctc_mode ?: 0;
                                                echo @$salaryMode[$ectc_mode];
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-4">
                                    <div class="detail-box">
                                        <div class="detail-lbl">Pay Rate</div>
                                        <div class="detail-val">XXX-XXX-XXXX</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-box">
                                        <div class="detail-lbl">Bill Rate</div>
                                        <div class="detail-val">
                                            {!! $current_ectc_cur !!}{{ @$benchApplication->bill_rate_amount }}
                                            @php
                                                $bill_mode = @$benchApplication->bill_rate_mode ?: 0;
                                                echo @$salaryMode[$bill_mode];
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Commission Type</div>
                                    <div class="detail-val">
                                        @if(@$benchApplication->comission_type == 1) Free
                                        @elseif(@$benchApplication->comission_type == 2) Paid
                                        @else N/A
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Primary Number</div>
                                    <div class="detail-val d-flex align-items-center">
                                        <div id="firstPhoneDisplaybench" class="d-flex align-items-center"></div>
                                        <span class="ms-1">xxx-xxx-xxxx</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Primary Email</div>
                                    <div class="detail-val">xxx-xxx-xxxx</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Location</div>
                                    <div class="detail-val">{{ $locationStr }}</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Added Date</div>
                                    <div class="detail-val">
                                        {{ !empty($benchApplication->created_at) ? date('d-M-Y', strtotime($benchApplication->created_at)) : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Inhouse Status</div>
                                    <div class="detail-val">{{ ucwords(@$benchApplication->master_status_name ?: 'N/A') }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-box">
                                    <div class="detail-lbl">Added By</div>
                                    <div class="detail-val">{{ ucwords(@$posted_by ?: 'N/A') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Side Card Panel --}}
                    <div class="side-summary-card">
                        <div class="side-card-title">Commercials & Quick Actions</div>
                        
                        <div class="d-flex flex-column gap-2" style="font-size:12px;">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Bill Rate:</span>
                                <span class="fw-bold">{!! $current_ectc_cur !!} {{ @$benchApplication->bill_rate_amount ?: 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Notice Period:</span>
                                <span class="fw-bold">{{ $npDisplay }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Experience:</span>
                                <span class="fw-bold">{{ $totalExp }} Yrs</span>
                            </div>
                        </div>

                        @if(!empty($benchApplication->resume))
                            <a type="button" class="btn btn-primary btn-sm w-100" href="{{ route('download.resume', ['id' => $benchApplication->id,'path' => rtrim(strtr(base64_encode($benchApplication->resume), '+/', '-_'), '=')]) }}" target="_blank" download>
                                <i class="fa fa-download me-1"></i> Download Resume
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const firstSavedCountryCode = "{{ @$benchApplication->phone_code }}";
        const firstPhoneDisplaybenchDiv = document.getElementById("firstPhoneDisplaybench");
        if(firstPhoneDisplaybenchDiv) {
            const flagDiv = document.createElement("div");
            flagDiv.className = `iti__flag iti__${firstSavedCountryCode}`;
            const dialCode = (typeof countryDialCodes !== 'undefined' ? countryDialCodes[firstSavedCountryCode] : "") || "";
            const dialCodeSpan = document.createElement("span");
            dialCodeSpan.textContent = dialCode;
            firstPhoneDisplaybenchDiv.appendChild(flagDiv);
            firstPhoneDisplaybenchDiv.appendChild(dialCodeSpan);
        }

        setTimeout(() => {
            $('.iti__flag-container').each(function() {
                var cnt = $(this).attr('data-row');
                const flagContainer = document.getElementById(`phoneDisplayReal_${cnt}`);
                if(flagContainer) {
                    var calling_code = $(this).attr('data-code');
                    const flagDiv = document.createElement("div");
                    flagDiv.className = `iti__flag iti__${calling_code}`;
                    const dialCode = (typeof countryDialCodes !== 'undefined' ? countryDialCodes[calling_code] : "") || "";
                    const dialCodeSpan = document.createElement("span");
                    dialCodeSpan.textContent = dialCode;
                    flagContainer.appendChild(flagDiv);
                    flagContainer.appendChild(dialCodeSpan);
                }
            });
        }, 1000);
    });
</script>
