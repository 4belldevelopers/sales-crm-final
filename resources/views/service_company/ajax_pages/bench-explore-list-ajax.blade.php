@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode      = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency        = GeneralMethod::currency();
    $job_status_arr  = GeneralMethod::job_status();
@endphp

@if (count($data) > 0)
@foreach ($data as $rowArr)
@php
    $row = (object) $rowArr;
    $jobDetails      = GeneralMethod::jobDetails($row->jobId);
    $company_id      = auth()->user()->company_id;
    $relationget     = GeneralMethod::getrRcommendationRelationData($company_id, $row->jobId);

    $from_company_id   = $relationget->from_company_id ?? null;
    $to_company_id     = $relationget->to_company_id   ?? null;
    $recommendation_id = $relationget->id              ?? null;
    $final_status      = $relationget->final_status    ?? null;

    $row->final_status       = $final_status;
    $row->recommendation_id  = $recommendation_id;
    $row->to_company_id      = $to_company_id;
    $row->from_company_id    = $from_company_id;
    $row->companyidcount     = $jobDetails->comId;
    $row->clients_name       = $jobDetails->client_name;

    $job_assign  = $jobDetails->job_assign;
    $jobAssign   = collect($job_assign);

    /* ── Company logo / initials ─────────────────── */
    $profile_detail_class = ($recommendation_id > 0 && @$row->to_company_id == $company_id)
                          ? 'client-show-detail' : 'show-detail';

    $logo_path = @$jobDetails->logo ?? '';
    $company_display_name = ucwords(@$jobDetails->company_name ?? '');

    $str      = strtolower(trim(@$jobDetails->title)[0] ?? 'j');
    $ord      = ord($str);
    $initials = strtoupper($str);
    if     ($ord >= 97  && $ord <= 101) { $initBg = '#FEE2E2'; $initClr = '#DC2626'; }
    elseif ($ord >= 102 && $ord <= 106) { $initBg = '#FEF3C7'; $initClr = '#D97706'; }
    elseif ($ord >= 107 && $ord <= 111) { $initBg = '#DBEAFE'; $initClr = '#2563EB'; }
    elseif ($ord >= 112 && $ord <= 116) { $initBg = '#DCFCE7'; $initClr = '#16A34A'; }
    elseif ($ord >= 117 && $ord <= 121) { $initBg = '#1E293B'; $initClr = '#94A3B8'; }
    else                                { $initBg = '#F1F5F9'; $initClr = '#475569'; }

    /* ── Skills + Match % — same computation/highlighting as AppTracker's
       candidate-row Match badge (resources/views/service_company/ajax_pages/all_apptracker.blade.php),
       just replicated here in the job-perspective direction: this candidate's
       skills matched against THIS job's required skills. No new queries —
       job_metronics_skills is already eager-loaded on $jobDetails. ── */
    $skillArr = DB::table('job_application_skills')
        ->where('job_application_id', $bench_id)
        ->pluck('skills')->toArray();
    $skillArrc_skills = array_column($jobDetails->job_metronics_skills, 'skill_name');
    $percentage       = GeneralMethod::skill_percentage($jobDetails->id, $skillArr);

    $matchNum = (float) rtrim($percentage ?: '0%', '%');
    if ($matchNum == 0) { $matchBg = '#F1F5F9'; $matchColor = '#64748B'; }
    elseif ($matchNum < 50) { $matchBg = '#FEE2E2'; $matchColor = '#DC2626'; }
    elseif ($matchNum <= 90) { $matchBg = '#FEF3C7'; $matchColor = '#D97706'; }
    else { $matchBg = '#DCFCE7'; $matchColor = '#16A34A'; }
    $matchBadge = '<span class="bxr-match-badge" style="background:'.$matchBg.';color:'.$matchColor.';">Match '.$percentage.'</span>';

    $matchedSkillsArr   = GeneralMethod::matchedSkillsList($skillArr, $skillArrc_skills);
    $unmatchedSkillsArr = array_values(array_udiff($skillArr, $matchedSkillsArr, 'strcasecmp'));

    $skillHtml = '';
    foreach ($matchedSkillsArr as $sk) {
        $skillHtml .= '<span class="bxr-skill matched" title="Matches a required skill">' . ucfirst($sk) . '</span>';
    }
    $maxShow = 3;
    $unmatchedShown = array_slice($unmatchedSkillsArr, 0, $maxShow);
    foreach ($unmatchedShown as $sk) {
        $skillHtml .= '<span class="bxr-skill">' . ucfirst($sk) . '</span>';
    }
    $unmatchedRemaining = max(count($unmatchedSkillsArr) - count($unmatchedShown), 0);
    if ($unmatchedRemaining > 0) {
        $skillHtml .= '<span class="bxr-skill more">+' . $unmatchedRemaining . ' more</span>';
    }

    /* ── Experience ──────────────────────────────── */
    $expMax = @$jobDetails->max_exp ?? '—';
    $expMin = @$jobDetails->min_exp ?? '—';
    $expRange = ($expMin && $expMax) ? "{$expMin} - {$expMax} Years" : "{$expMax}+ Years";

    /* ── Salary — same display format as the jobs page (all_jobs_ajax):
       "₹ 1500K - 2500K /Annum" via thousandsCurrencyFormat + mode suffix ── */
    $min_salary_raw = (float)($jobDetails->min_salary ?? 0);
    $max_salary_raw = (float)($jobDetails->max_salary ?? 0);
    $salCurrencySym = $currencySymbole[@$jobDetails->min_salary_currency] ?? '₹';
    $salSuffix = ['/Annum', '/Month', '/Hour'][(int)(@$jobDetails->min_sal_mode ?? 0)] ?? '';
    $salaryDisplay = '';
    if ($min_salary_raw > 0 || $max_salary_raw > 0) {
        $salaryDisplay = GeneralMethod::thousandsCurrencyFormat($min_salary_raw)
            . ' - ' . GeneralMethod::thousandsCurrencyFormat($max_salary_raw);
    }

    $employment_type = ($jobDetails->employment_type == 1) ? 'Contract' : 'Full-time';

    /* ── Location — city list comes straight off the ES job document
       (client_city_jobs is already in $jobDetails), replacing the two
       per-row MySQL lookups that used to run here. Labels match the jobs
       page: Office / Remote / Hybrid ('any' and 'other' both mean hybrid
       in the data). ── */
    $jobid         = $jobDetails->id;
    $cityArr       = is_array(@$jobDetails->client_city_jobs) ? $jobDetails->client_city_jobs : [];
    $jobCityCount  = count($cityArr);
    $firstCityName = $cityArr[0]['city_name'] ?? '';
    $workMode      = 'Office';
    $cityDisplay   = ($jobCityCount > 1) ? 'Multiple' : ucwords(mb_substr($firstCityName, 0, 15));

    if ($jobDetails->work_type == 'yes') {
        $workMode    = 'Remote';
        $cityDisplay = 'Remote';
    } elseif (in_array($jobDetails->work_type, ['any', 'other'], true)) {
        $workMode = 'Hybrid';
    }

    /* ── Job type — same 3-way derivation as the jobs page (all_jobs_ajax):
       received via a direct-client recommendation → "Direct Client" (green
       handshake); own in-house job (self_company) → "Internal" (blue
       building); own job for an internal client → "Internal Client". ── */
    if ($recommendation_id > 0 && @$row->to_company_id == $company_id) {
        $typeLabel = 'Direct ' . __('messages.clinet_tag');
        $typeClass = 'bxr-type-client';
        $typeIcon  = '<i class="fas fa-handshake" style="color:#09b760;"></i>';
    } elseif ((int)(@$jobDetails->self_company ?? 0) === 1) {
        $typeLabel = __('messages.internal_tag');
        $typeClass = 'bxr-type-internal';
        $typeIcon  = '<i class="fas fa-building" style="color:#3e97ff;"></i>';
    } else {
        $typeLabel = __('messages.internal_tag') . ' ' . __('messages.clinet_tag');
        $typeClass = 'bxr-type-internal';
        $typeIcon  = '<i class="fas fa-building" style="color:#3e97ff;"></i>';
    }

    /* Category/department line (e.g. "IT (Information Technology)") — one
       lookup per DISTINCT category on the page, memoized across rows. */
    static $bxrDeptCache = [];
    $deptName = '';
    $catId = @$jobDetails->category_id;
    if ($catId) {
        if (!array_key_exists($catId, $bxrDeptCache)) {
            $bxrDeptCache[$catId] = DB::table('job_categories')->where('id', $catId)->value('name') ?? '';
        }
        $deptName = $bxrDeptCache[$catId];
    }

    /* ── Job status ──────────────────────────────── */
    $statusLabel = ucwords($jobDetails->status ?? '');
    if ($statusLabel == 'Active')   { $statusLabel = 'Hiring'; $statusClass = 'bxr-b gr'; }
    elseif ($statusLabel == 'Inactive') { $statusLabel = 'Closed'; $statusClass = 'bxr-b rd'; }
    elseif ($statusLabel == 'Hold')     { $statusLabel = 'Hold';   $statusClass = 'bxr-b yw'; }
    else                                { $statusClass = 'bxr-b gy'; }

    /* ── Hiring Manager ──────────────────────────── */
    $hiringManager    = '—';
    $hiringDesig      = '';
    if (count($jobDetails->client_contact_info_jobs) > 0) {
        $hiringManager = ucwords($jobDetails->client_contact_info_jobs[0]['contact_person_name'] ?? '');
        $hiringDesig   = $jobDetails->client_contact_info_jobs[0]['designation'] ?? '';
    }
    if ($jobDetails->comId != $company_id) {
        $captains = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', $jobDetails->comId)->values()->all();
        if (count($captains) > 0) {
            $hiringManager = ucfirst($captains[0]['user_name'] ?? '');
        }
    }

    /* ── Internal / Client tag ───────────────────── */
    $recommendation_text = ($jobDetails->comId == $company_id)
        ? __('messages.internal_tag') : __('messages.clinet_tag');
    $internalBadge = ($jobDetails->comId == $company_id)
        ? '<span class="bxr-b bl">Internal</span>'
        : '<span class="bxr-b pu">Client</span>';
    $jobIdText = $jobDetails->id . '-' . $recommendation_text;

    /* ── Job stage ───────────────────────────────── */
    $stageName  = ucwords(mb_substr($row->name ?? '', 0, 12));
    $stageCss   = $row->css_style ?? 'badge-light';
    $privateTag = '';
    if (@$row->job_master_status_id == 8) {
        $privateTag = '<span class="bxr-b gy" style="margin-top:4px;">Private</span>';
    }
    $childName = '';
    if (!empty(@$row->childStatus) && @$row->childStatus > 0) {
        $cs = DB::table('candidate_status')->where('id', $row->childStatus)->first();
        if ($cs) { $childName = $cs->name; }
    }
    $job_wise_condidates = DB::table('add_job_wise_condidates')
        ->join('master_status','master_status.id','=','add_job_wise_condidates.master_status_id')
        ->select('add_job_wise_condidates.master_status_id','add_job_wise_condidates.child_status_id','master_status.name','master_status.css_style')
        ->where('add_job_wise_condidates.job_id', $jobDetails->id)
        ->where('add_job_wise_condidates.job_application_id', $bench_id)->first();

    /* ── Added by / date ─────────────────────────── */
    $addedDate  = GeneralMethod::ymdTodmyNotTime($row->created_at ?? '');
    $daysAgo    = '';
    if (!empty($row->created_at)) {
        try { $daysAgo = Carbon::parse($row->created_at)->diffForHumans(); } catch(\Exception $e){}
    }

    /* ── Added by user ───────────────────────────── */
    $addedByName = '';
    $addedByUser = DB::table('users')->where('id', $row->submitted_by ?? 0)->select('name')->first();
    if ($addedByUser) { $addedByName = ucwords($addedByUser->name); }
@endphp

<tr>
    {{-- ① JOB DETAILS --}}
    <td style="max-width:280px;">
        <div class="bxr-job-row">
            {{-- Logo / initial --}}
            @if(!empty($logo_path))
                <img src="{{ $logo_path }}" alt="" class="bxr-co-logo {{ $profile_detail_class }}"
                     data-row-id="{{ $jobDetails->id }}" style="cursor:pointer;">
            @else
                <div class="bxr-co-init {{ $profile_detail_class }}" data-row-id="{{ $jobDetails->id }}"
                     style="background:{{ $initBg }};color:{{ $initClr }};cursor:pointer;">{{ $initials }}</div>
            @endif

            <div style="min-width:0;">
                <div class="bxr-job-title {{ $profile_detail_class }}"
                     data-row-id="{{ $jobDetails->id }}"
                     data-row-job_applications_id="{{ $bench_id }}"
                     style="cursor:pointer;">
                    {{ ucwords(mb_substr(@$jobDetails->title, 0, 22)) }}
                </div>
                <div class="bxr-job-co">{{ $company_display_name }}</div>
                <div class="bxr-skills">
                    {!! $matchBadge !!}
                    {!! $skillHtml !!}
                </div>
            </div>
        </div>
    </td>

    {{-- ② EXPERIENCE --}}
    <td style="white-space:nowrap;vertical-align:middle;">
        <div style="display:flex;align-items:center;gap:5px;margin-bottom:4px;">
            <i class="ti ti-clock" style="font-size:12px;color:#94A3B8;"></i>
            <span style="font-size:12px;font-weight:600;color:#374151;">{{ $expRange }}</span>
        </div>
        @if($salaryDisplay)
        <div style="display:flex;align-items:center;gap:4px;margin-bottom:6px;">
            <i class="ti ti-currency-rupee" style="font-size:12px;color:#94A3B8;"></i>
            <span style="font-size:12px;font-weight:600;color:#374151;">{{ $salaryDisplay }}</span>
        </div>
        @endif
        <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
            {!! $internalBadge !!}
            <span style="color:#CBD5E1;">•</span>
            <span style="font-size:11px;color:#64748B;font-weight:600;">{{ $jobDetails->id }}</span>
        </div>
    </td>

    {{-- ③ HIRING & LOCATION --}}
    <td style="white-space:nowrap;vertical-align:middle;">
        <div style="margin-bottom:5px;">
            <span class="{{ $statusClass }}">
                <i class="ti ti-circle-filled" style="font-size:7px;"></i> {{ $statusLabel }}
            </span>
        </div>
        @if($cityDisplay && $cityDisplay !== '—')
        <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:2px;">{{ $cityDisplay }}</div>
        @endif
        <div style="font-size:11px;color:#94A3B8;display:flex;align-items:center;gap:3px;">
            <i class="ti ti-map-pin" style="font-size:11px;"></i> {{ $workMode }}
        </div>
    </td>

    {{-- ④ HIRING MANAGER --}}
    @php
        $skipDesig = ['role','designation','n/a','na','--','-','none',''];
        $showDesig = $hiringDesig && !in_array(strtolower(trim($hiringDesig)), $skipDesig);
    @endphp
    <td style="white-space:nowrap;vertical-align:middle;">
        <div style="font-size:13px;font-weight:700;color:#1A1D23;margin-bottom:2px;">{{ $hiringManager }}</div>
        @if($showDesig)
        <div style="font-size:11px;color:#64748B;margin-bottom:3px;">{{ $hiringDesig }}</div>
        @endif
        <span class="bxr-b gy" style="font-size:10px;">{{ $employment_type }}</span>
    </td>

    {{-- ⑤ JOB STAGE --}}
    <td>
        <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-start;">
            @if(!empty($job_wise_condidates) && $job_wise_condidates->master_status_id == 8)
                <a href="javascript:void(0);" data-row-id="{{ $jobDetails->id }}"
                   class="client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
                    <span class="badge fw-bolder {{ $stageCss }} px-2 py-1" style="font-size:11px;">{{ $stageName }}</span>
                </a>
            @else
                <span class="badge fw-bolder {{ $stageCss }} px-2 py-1" style="font-size:11px;">{{ $stageName }}</span>
            @endif
            @if($childName)
                <span class="bxr-b gy" style="font-size:10px;">{{ $childName }}</span>
            @endif
            {!! $privateTag !!}
        </div>
    </td>

    {{-- ⑥ ADDED BY --}}
    <td style="white-space:nowrap;">
        @if($addedByName)
        <div class="bxr-c1" style="font-size:12px;">{{ mb_substr($addedByName,0,16) }}</div>
        @endif
        <div class="bxr-c2">{{ $addedDate }}</div>
        @if($daysAgo)
        <div class="bxr-c2" style="color:#94A3B8;font-size:10px;margin-top:2px;">{{ $daysAgo }}</div>
        @endif
    </td>
</tr>

@endforeach
@else
<tr>
    <td colspan="6" style="text-align:center;padding:40px 0;color:#94A3B8;">
        <i class="ti ti-inbox" style="font-size:32px;display:block;margin-bottom:8px;"></i>
        No companies processing this candidate yet
    </td>
</tr>
@endif
