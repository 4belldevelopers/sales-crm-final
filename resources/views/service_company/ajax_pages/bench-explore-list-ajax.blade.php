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
@php
    /**
     * Phase 5 N+1 Query Optimization:
     * Hoist candidate skills lookup outside row loop (bench_id is constant per request).
     */
    $bench_skill_list = !empty($bench_id) ? DB::table('job_application_skills')
        ->where('job_application_id', $bench_id)
        ->pluck('skills')->toArray() : [];
    static $bxrChildStatusCache = [];
    static $bxrUserCache = [];
    static $bxrJobWiseCandCache = [];
@endphp
@foreach ($data as $rowArr)
@php
    $row = (object) $rowArr;
    $jobId = $row->jobId ?? $row->job_id ?? 0;
    if ($jobId <= 0 && ! empty($row->id)) {
        $jobId = DB::table('add_job_wise_condidates')->where('id', $row->id)->value('job_id') ?? 0;
    }
    $row->jobId = $jobId;
    $jobDetails      = GeneralMethod::jobDetails($jobId);
    $company_id      = auth()->user()->company_id;
    $relationget     = GeneralMethod::getrRcommendationRelationData($company_id, $jobId);

    $from_company_id   = $relationget->from_company_id ?? null;
    $to_company_id     = $relationget->to_company_id   ?? null;
    $recommendation_id = $relationget->id              ?? null;
    $final_status      = $relationget->final_status    ?? null;

    $row->final_status       = $final_status;
    $row->recommendation_id  = $recommendation_id;
    $row->to_company_id      = $to_company_id;
    $row->from_company_id    = $from_company_id;
    $row->companyidcount     = $jobDetails->comId ?? null;
    $row->clients_name       = $jobDetails->client_name ?? null;

    $job_assign  = $jobDetails->job_assign ?? [];
    $jobAssign   = collect($job_assign);

    /* ── Company logo / initials ─────────────────── */
    $profile_detail_class = ($recommendation_id > 0 && @$row->to_company_id == $company_id)
                          ? 'client-show-detail' : 'show-detail';

    $logo_path = @$jobDetails->logo ?? '';
    $company_display_name = ucwords(@$jobDetails->company_name ?? @$row->other_company ?? @$row->client_name ?? '');

    $displayTitle = @$jobDetails->title;
    if (empty($displayTitle) || str_starts_with($displayTitle, 'Requisition #')) {
        if (!empty($row->title)) {
            $displayTitle = $row->title;
        } elseif (!empty($row->name)) {
            $displayTitle = $row->name;
        }
    }
    if (empty($displayTitle)) {
        $displayTitle = 'Requisition #' . $jobId;
    }

    $str      = strtolower(trim($displayTitle)[0] ?? 'j');
    $ord      = ord($str);
    $initials = strtoupper($str);
    if     ($ord >= 97  && $ord <= 101) { $initBg = '#FEE2E2'; $initClr = '#DC2626'; }
    elseif ($ord >= 102 && $ord <= 106) { $initBg = '#FEF3C7'; $initClr = '#D97706'; }
    elseif ($ord >= 107 && $ord <= 111) { $initBg = '#DBEAFE'; $initClr = '#2563EB'; }
    elseif ($ord >= 112 && $ord <= 116) { $initBg = '#DCFCE7'; $initClr = '#16A34A'; }
    elseif ($ord >= 117 && $ord <= 121) { $initBg = '#1E293B'; $initClr = '#94A3B8'; }
    else                                { $initBg = '#F1F5F9'; $initClr = '#475569'; }

    /* ── Skills + Match % ────────────────────────── */
    $skillArr = $bench_skill_list;
    $jobSkillsRaw = $jobDetails->job_metronics_skills ?? [];
    $skillArrc_skills = is_array($jobSkillsRaw) ? array_column($jobSkillsRaw, 'skill_name') : [];
    $percentage       = GeneralMethod::skill_percentage($jobDetails->id ?? 0, $skillArr);

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
       "₹ 1500K - 2500K /annum" via thousandsCurrencyFormat + mode suffix ── */
    $min_salary_raw = (float)($jobDetails->min_salary ?? 0);
    $max_salary_raw = (float)($jobDetails->max_salary ?? 0);
    /* GeneralMethod::currencySymbole() returns raw <i class="fa fa-…"> markup
       that depends on FontAwesome being loaded — on this AJAX response path
       the font isn't always present, so the literal tag leaks through as text.
       Map currency_id → unicode glyph instead. */
    $currencyGlyphs = [0=>'₹', 1=>'€', 2=>'¥', 3=>'£', 4=>'$', 5=>'د.إ'];
    $salCurrencySym = $currencyGlyphs[(int)@$jobDetails->min_salary_currency] ?? '₹';
    $salSuffix = ['/annum', '/month', '/hour'][(int)(@$jobDetails->min_sal_mode ?? 0)] ?? '';
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

    /* ── Client Type Badge — derived from the same branch that the jobs
       page uses to colour its $internalBadge. Pure data-derivation, no
       new queries. Routed recommendations (recommendation_id > 0 with
       to_company_id == viewer's company_id) → Direct Client (green);
       in-house (self_company == 1) or any non-routed route → Internal
       Client (blue). The numeric "· jobId" suffix matches the legacy
       "Internal Client · 1410" / "Direct Client · 1390" form so a
       reader familiar with the page sees the ID jump out at the same
       spot it always did. The badge occupies the third vertical slot
       in the EXPERIENCE column (under exp + salary) for visual
       hierarchy: profile-anchored text first, classification last. ── */
    if ($recommendation_id > 0 && @$row->to_company_id == $company_id) {
        $clientBadgeKey   = 'direct';
        $clientBadgeLabel = 'Direct Client';
        $clientBadgeSvg   = '<svg viewBox="0 0 24 24" width="11.5" height="11.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7l2-2h14l2 2v13H3z"/><circle cx="12" cy="13" r="3.2"/></svg>';
    } else {
        $clientBadgeKey   = 'internal';
        $clientBadgeLabel = 'Internal Client';
        $clientBadgeSvg   = '<svg viewBox="0 0 24 24" width="11.5" height="11.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-7h6v7"/></svg>';
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

    /* HM avatar — initials if a name is available, em-dash placeholder
       for the empty case. Empty state uses a dashed grey fill so the
       column reads cleanly even when the data isn't populating. */
    $hmHasName  = ($hiringManager !== '—' && trim($hiringManager) !== '');
    $hmInitials = $hmHasName ? strtoupper(mb_substr($hiringManager, 0, 1)) : '–';
    $skipDesigList = ['role','designation','n/a','na','--','-','none',''];
    $showDesig = $hiringDesig && !in_array(strtolower(trim($hiringDesig)), $skipDesigList);

    /* ── Internal / Client tag ───────────────────── */
    $recommendation_text = ($jobDetails->comId == $company_id)
        ? __('messages.internal_tag') : __('messages.clinet_tag');
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
        $csId = (int)$row->childStatus;
        if (!array_key_exists($csId, $bxrChildStatusCache)) {
            $bxrChildStatusCache[$csId] = DB::table('candidate_status')->where('id', $csId)->value('name') ?? '';
        }
        $childName = $bxrChildStatusCache[$csId];
    }

    $jwKey = $jobDetails->id . '_' . $bench_id;
    if (!array_key_exists($jwKey, $bxrJobWiseCandCache)) {
        $bxrJobWiseCandCache[$jwKey] = DB::table('add_job_wise_condidates')
            ->join('master_status','master_status.id','=','add_job_wise_condidates.master_status_id')
            ->select('add_job_wise_condidates.master_status_id','add_job_wise_condidates.child_status_id','master_status.name','master_status.css_style')
            ->where('add_job_wise_condidates.job_id', $jobDetails->id)
            ->where('add_job_wise_condidates.job_application_id', $bench_id)->first();
    }
    $job_wise_condidates = $bxrJobWiseCandCache[$jwKey];

    /* ── Added by / date ─────────────────────────── */
    $addedDate  = GeneralMethod::ymdTodmyNotTime($row->created_at ?? '');
    $daysAgo    = '';
    if (!empty($row->created_at)) {
        try { $daysAgo = Carbon::parse($row->created_at)->diffForHumans(); } catch(\Exception $e){}
    }

    /* ── Added by user ───────────────────────────── */
    $addedByName = '';
    $subUserId = (int)($row->submitted_by ?? 0);
    if ($subUserId > 0) {
        if (!array_key_exists($subUserId, $bxrUserCache)) {
            $bxrUserCache[$subUserId] = DB::table('users')->where('id', $subUserId)->value('name') ?? '';
        }
        $addedByName = ucwords($bxrUserCache[$subUserId]);
    }
@endphp

<tr>
    {{-- ① JOB DETAILS --}}
    <td>
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
                     title="{{ ucwords($displayTitle) }}"
                     style="cursor:pointer;">
                    {{ ucwords(mb_substr($displayTitle, 0, 22)) }}
                </div>
                <div class="bxr-job-co">{{ $company_display_name }}</div>
                <div class="bxr-skills">
                    {!! $matchBadge !!}
                    {!! $skillHtml !!}
                </div>
            </div>
        </div>
    </td>

    {{-- ② EXPERIENCE (vertical: clock ▸ exp · ₹ ▸ salary · Client Type Badge) --}}
    <td>
        <div class="bxr-col-exp-stack">
            <div class="bxr-col-exp-row">
                <span class="bxr-icon-box"><svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
                <span>{{ $expRange }}</span>
            </div>
            @if($salaryDisplay)
            <div class="bxr-col-exp-row">
                <span class="bxr-icon-box">{{ $salCurrencySym }}</span>
                <span>{{ $salaryDisplay }}{!! $salSuffix !== '' ? ' <span class="bxr-col-exp-mode">'.$salSuffix.'</span>' : '' !!}</span>
            </div>
            @endif
            <div>
                <span class="client-badge client-badge--{{ $clientBadgeKey }}">
                    <span class="client-badge-stripe"></span>
                    {!! $clientBadgeSvg !!}
                    <span class="client-badge-label">{{ $clientBadgeLabel }}</span>
                    <span class="client-badge-id">· {{ $jobDetails->id }}</span>
                </span>
            </div>
        </div>
    </td>

    {{-- ③ HIRING & LOCATION --}}
    <td>
        <div class="bxr-col-loc-stack">
            <div>
                <span class="{{ $statusClass }}">
                    <svg viewBox="0 0 24 24" width="7" height="7" fill="currentColor" aria-hidden="true" style="display:inline-block;vertical-align:middle;margin-right:4px;"><circle cx="12" cy="12" r="9"/></svg> {{ $statusLabel }}
                </span>
            </div>
            @if($cityDisplay && $cityDisplay !== '—')
            <div class="bxr-col-loc-row">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;display:inline-block;vertical-align:middle;"><path d="M12 22s-7-7.5-7-12a7 7 0 1 1 14 0c0 4.5-7 12-7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <span>{{ $cityDisplay }}</span>
            </div>
            <div class="bxr-col-loc-mode">{{ $workMode }}</div>
            @endif
        </div>
    </td>

    {{-- ④ HIRING MANAGER --}}
    <td>
        <div class="bxr-col-hm-row">
            <span class="bxr-col-hm-av {{ $hmHasName ? 'bxr-col-hm-av--filled' : 'bxr-col-hm-av--empty' }}">{{ $hmInitials }}</span>
            <div class="bxr-col-hm-info">
                <span class="bxr-col-hm-name">{{ $hiringManager }}</span>
                @if($showDesig)
                    <span class="bxr-col-hm-role">{{ $hiringDesig }}</span>
                @else
                    <span class="bxr-col-hm-role">Hiring Manager</span>
                @endif
                <span class="bxr-b gy" style="font-size:10px;margin-top:2px;align-self:flex-start;">{{ $employment_type }}</span>
            </div>
        </div>
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
        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:block;margin:0 auto 8px;"><path d="M3 13l3-8h12l3 8M3 13v7h18v-7M3 13h5l2 3h4l2-3h5"/></svg>
        No companies processing this candidate yet
    </td>
</tr>
@endif
