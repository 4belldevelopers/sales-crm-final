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
    $company_display_name = '';

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

    $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
    $matchBg = $matchStyle['bg'];
    $matchColor = $matchStyle['color'];
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
    $salCurrencyCode = $currency[(int)@$jobDetails->min_salary_currency] ?? 'INR';
    $currencyGlyphs = [
        'INR' => '₹', 'EUR' => '€', 'JPY' => '¥', 'GBP' => '£', 'USD' => '$',
        'AED' => 'د.إ', 'CAD' => 'C$', 'AUD' => 'A$', 'SGD' => 'S$',
    ];
    $salCurrencySym = $currencyGlyphs[$salCurrencyCode] ?? ($salCurrencyCode . ' ');
    $salSuffix = ['/Annum', '/Month', '/Hour'][(int)(@$jobDetails->min_sal_mode ?? 0)] ?? '';
    $salaryDisplay = '';
    if ($min_salary_raw > 0 || $max_salary_raw > 0) {
        $salaryMinDisplay = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($min_salary_raw));
        $salaryMaxDisplay = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($max_salary_raw));
        $salaryDisplay = $salaryMinDisplay . ' - ' . $salaryMaxDisplay;
    }

    $employment_type = ($jobDetails->employment_type == 1) ? 'Contract' : 'Full-time';
    $employmentClass = ($jobDetails->employment_type == 1) ? 'bxr-pos-contract' : 'bxr-pos-fulltime';
    $employmentIconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 12h18"/></svg>';

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
    if ($workMode === 'Remote') {
        $workModeIconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5a10 10 0 0 1 14 0M8.5 16a5 5 0 0 1 7 0M12 20h.01"/></svg>';
    } elseif ($workMode === 'Hybrid') {
        $workModeIconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s-7-7.5-7-12a7 7 0 1 1 14 0c0 4.5-7 12-7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>';
    } else {
        $workModeIconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16M9 21v-6h6v6M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01"/></svg>';
    }

    /* ── Client Type Badge & Direct Client detection ──────
       If job belongs to an external/direct client company ($jobDetails->comId != $company_id
       or recommendation_id > 0 with to_company_id == $company_id), mark as Direct Client.
       Otherwise mark as Internal / Internal Client. ── */
    $isDirectClientJob = ($recommendation_id > 0 && (int) $to_company_id === (int) $company_id)
        || ((int) ($jobDetails->comId ?? 0) > 0 && (int) $jobDetails->comId !== (int) $company_id)
        || ((int) $from_company_id > 0 && (int) $from_company_id !== (int) $company_id);

    static $bxrClientCache = [];
    $clientId = (int) ($jobDetails->client_id ?? $jobDetails->clientId ?? 0);
    $clientName = $jobDetails->client_name ?? $jobDetails->clients_name ?? '';
    $clientLogo = '';
    $isSelfCompany = false;

    if ($clientId > 0) {
        if (!array_key_exists($clientId, $bxrClientCache)) {
            $clientRow = DB::table('clients')->where('id', $clientId)->select('name', 'self_company', 'logo', 'logo_logical_path')->first();
            $bxrClientCache[$clientId] = [
                'name' => $clientRow->name ?? '',
                'self_company' => ((int) ($clientRow->self_company ?? 0)) === 1,
                'logo' => !empty($clientRow->logo_logical_path)
                    ? (str_starts_with($clientRow->logo_logical_path, 'http') ? $clientRow->logo_logical_path : url('public/'.$clientRow->logo_logical_path))
                    : (!empty($clientRow->logo) ? (str_starts_with($clientRow->logo, 'http') ? $clientRow->logo : url('public/'.$clientRow->logo)) : '')
            ];
        }
        $isSelfCompany = $bxrClientCache[$clientId]['self_company'];
        $clientLogo = $bxrClientCache[$clientId]['logo'];
        if (empty($clientName)) {
            $clientName = $bxrClientCache[$clientId]['name'];
        }
    } else {
        $isSelfCompany = ((int) ($jobDetails->self_company ?? $row->self_company ?? 0)) === 1;
    }

    static $bxrCompNameCache = [];
    $parentCompName = '';
    if ($isDirectClientJob) {
        $parentCompId = (int) ($from_company_id ?: ($jobDetails->comId ?? 0));
        if ($parentCompId > 0) {
            if (!array_key_exists($parentCompId, $bxrCompNameCache)) {
                $bxrCompNameCache[$parentCompId] = DB::table('companies')->where('id', $parentCompId)->value('company_name') ?? '';
            }
            $parentCompName = $bxrCompNameCache[$parentCompId];
        }
        if (empty($parentCompName)) {
            $parentCompName = !empty($jobDetails->company_name) ? $jobDetails->company_name : (!empty($row->company_name) ? $row->company_name : ($row->clients_name ?? ''));
        }
        $parentCompName = ucwords($parentCompName);

        $clientBadgeKey   = 'direct';
        $clientBadgeLabel = 'Direct Client';
        $clientBadgeSvg   = '<svg viewBox="0 0 24 24" width="11.5" height="11.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7l2-2h14l2 2v13H3z"/><circle cx="12" cy="13" r="3.2"/></svg>';
        $company_display_name = $parentCompName;
        $logo_path = @$jobDetails->logo ?? '';
    } else {
        $clientBadgeKey   = 'internal';
        $clientBadgeLabel = $isSelfCompany ? __('messages.internal_tag') : 'Internal Client';
        $clientBadgeSvg   = '<svg viewBox="0 0 24 24" width="11.5" height="11.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-7h6v7"/></svg>';
        $company_display_name = ucwords(($isSelfCompany || empty($clientName)) ? (@$jobDetails->company_name ?? @$row->other_company ?? '') : $clientName);
        $logo_path = $isSelfCompany ? (@$jobDetails->logo ?? '') : $clientLogo;
    }

    $initialStr = !empty($company_display_name) ? trim($company_display_name)[0] : (trim($displayTitle)[0] ?? 'J');
    $str      = strtolower($initialStr);
    $ord      = ord($str);
    $initials = strtoupper($str);
    if     ($ord >= 97  && $ord <= 101) { $initBg = '#FEE2E2'; $initClr = '#DC2626'; }
    elseif ($ord >= 102 && $ord <= 106) { $initBg = '#FEF3C7'; $initClr = '#D97706'; }
    elseif ($ord >= 107 && $ord <= 111) { $initBg = '#DBEAFE'; $initClr = '#2563EB'; }
    elseif ($ord >= 112 && $ord <= 116) { $initBg = '#DCFCE7'; $initClr = '#16A34A'; }
    elseif ($ord >= 117 && $ord <= 121) { $initBg = '#1E293B'; $initClr = '#94A3B8'; }
    else                                { $initBg = '#F1F5F9'; $initClr = '#475569'; }

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
    if ($statusLabel == 'Active')   { $statusLabel = 'Hiring'; $statusClass = 'bxr-job-status bxr-job-status--hiring'; }
    elseif ($statusLabel == 'Inactive') { $statusLabel = 'Closed'; $statusClass = 'bxr-job-status bxr-job-status--closed'; }
    elseif ($statusLabel == 'Hold')     { $statusLabel = 'Hold';   $statusClass = 'bxr-job-status bxr-job-status--hold'; }
    else                                { $statusClass = 'bxr-job-status bxr-job-status--neutral'; }
    if ($statusLabel === 'Hiring') {
        $statusIconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
    } elseif ($statusLabel === 'Hold') {
        $statusIconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>';
    } elseif ($statusLabel === 'Closed') {
        $statusIconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7l2-2h14l2 2v13H3z"/><path d="M10 11h4"/></svg>';
    } else {
        $statusIconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="5"/></svg>';
    }

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
    if ($isDirectClientJob) {
        $recommendation_text = 'Direct Client';
    } elseif ($isSelfCompany) {
        $recommendation_text = 'Internal';
    } else {
        $recommendation_text = 'Internal Client';
    }
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

    /* ── Added by / Posted by / Direct Client Job Captain resolution ── */
    if (!isset($spokpersonMap) || !is_object($spokpersonMap)) {
        $spokpersonMap = collect();
    }
    $row->ClientComID = $row->ClientComID ?? $to_company_id ?? $jobDetails->comId ?? 0;
    $row->rec_candidate_assign_id = $row->rec_candidate_assign_id ?? $row->id ?? null;
    $row->posted_by = $row->posted_by ?? $parentAgencyUserName ?? null;
    $row->submitted_by_name = $row->submitted_by_name ?? $parentAgencyUserName ?? null;

    if($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0){
        $spokKey = ($row->jobId ?? $row->job_id ?? 0) . '_' . $row->ClientComID;
        $spokName = (isset($spokpersonMap) && is_object($spokpersonMap)) ? ($spokpersonMap->get($spokKey) ?? null) : null;
        $rowpostedby = $spokName ? $spokName : $row->posted_by;
        $rowpostedby = @$row->rec_candidate_assign_id ? $row->posted_by : $rowpostedby;
    } else {
        $rowpostedby = $row->posted_by;
    }
    if (empty($rowpostedby)) {
        $rowpostedby = !empty($row->submitted_by_name) ? $row->submitted_by_name : (!empty($row->spokperson_name) ? $row->spokperson_name : (@$row->source ?: ''));
    }

    // Direct Client Job Captain Name (spok_user_id > 0 for direct client comId)
    static $bxrCaptainCache = [];
    $directClientCaptainName = '';
    $directClientComId = (int) ($row->ClientComID ?: ($to_company_id ?: ($jobDetails->comId ?? 0)));
    $capKey = $jobId . '_' . $directClientComId;

    if ($isDirectClientJob && $jobId > 0 && $directClientComId > 0) {
        if (!array_key_exists($capKey, $bxrCaptainCache)) {
            $bxrCaptainCache[$capKey] = DB::table('job_assign')
                ->join('users', 'users.id', '=', 'job_assign.spok_user_id')
                ->where('job_assign.job_id', $jobId)
                ->where('job_assign.company_id', $directClientComId)
                ->where('job_assign.spok_user_id', '>', 0)
                ->value('users.name') ?? '';
        }
        $directClientCaptainName = ucwords($bxrCaptainCache[$capKey]);
    }

    $addedUserDisplay = ($isDirectClientJob && !empty($directClientCaptainName))
        ? $directClientCaptainName
        : (!empty($rowpostedby) ? $rowpostedby : ($addedByName ?: '—'));

@endphp

<tr style="height: 102px; position: relative;">
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

            <div class="bxr-job-info">
                <div class="bxr-job-title {{ $profile_detail_class }}"
                     data-row-id="{{ $jobDetails->id }}"
                     data-row-job_applications_id="{{ $bench_id }}"
                     title="{{ ucwords($displayTitle) }}"
                     style="cursor:pointer;">
                    {{ ucwords(mb_substr($displayTitle, 0, 34)) }}
                </div>
                @if($deptName)
                    <div class="bxr-job-dept">{{ $deptName }}</div>
                @endif
                <div class="bxr-job-co">{{ $company_display_name }}</div>
            </div>
        </div>
        <div class="bxr-skills" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
            {!! $matchBadge !!}
            {!! $skillHtml !!}
        </div>
    </td>

    {{-- ② EXPERIENCE --}}
    <td>
        <div class="bxr-col-exp-stack">
            <div class="bxr-col-exp-row">
                <span class="bxr-icon-box"><svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
                <span>{{ $expRange }}</span>
            </div>
            @if($salaryDisplay)
            <div class="bxr-col-exp-row">
                <span class="bxr-icon-box">{{ $salCurrencySym }}</span>
                <span data-jero-currency-amount="{{ $min_salary_raw }}" data-jero-currency-max="{{ $max_salary_raw }}" data-jero-currency-original="{{ $salCurrencyCode }}" data-jero-currency-mode="{{ $salSuffix }}">{{ $salaryDisplay }}{!! $salSuffix !== '' ? ' <span class="bxr-col-exp-mode">'.$salSuffix.'</span>' : '' !!}</span>
            </div>
            @endif
            <div>
                <span class="bxr-pos-badge {{ $employmentClass }}">
                    {!! $employmentIconSvg !!}
                    {{ $employment_type }}
                </span>
            </div>
        </div>
    </td>

    {{-- ③ LOCATION --}}
    <td>
        <div class="bxr-col-loc-stack">
            <div>
                <span class="{{ $statusClass }}">
                    {!! $statusIconSvg !!} {{ $statusLabel }}
                </span>
            </div>
            @if($cityDisplay && $cityDisplay !== '—')
            <div class="bxr-col-loc-row">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;display:inline-block;vertical-align:middle;"><path d="M12 22s-7-7.5-7-12a7 7 0 1 1 14 0c0 4.5-7 12-7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <span>{{ $cityDisplay }}</span>
            </div>
            <div class="bxr-col-loc-mode">{!! $workModeIconSvg !!}{{ $workMode }}</div>
            @endif
        </div>
    </td>

    {{-- ④ HIRING MANAGER --}}
    <td>
        <div class="bxr-col-hm-row">
            <div class="bxr-col-hm-info">
                <span class="bxr-col-hm-name">{{ $hiringManager }}</span>
                @if($showDesig)
                    <span class="bxr-col-hm-role">{{ $hiringDesig }}</span>
                @else
                    <span class="bxr-col-hm-role">Hiring Manager</span>
                @endif
                <span class="client-badge client-badge--{{ $clientBadgeKey }}" style="margin-top:3px;">
                    <span class="client-badge-stripe"></span>
                    {!! $clientBadgeSvg !!}
                    <span class="client-badge-label">{{ $clientBadgeLabel }}</span>
                    <span class="client-badge-id">· {{ $jobDetails->id }}</span>
                </span>
            </div>
        </div>
    </td>

    {{-- ⑤ STAGE --}}
    <td>
        <div class="bxr-stage-cell">
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
    <td>
        <div class="bxr-added-cell">
            <span class="bxr-added-name" title="{{ $addedUserDisplay }}">{{ ucwords(mb_substr($addedUserDisplay, 0, 18)) ?: '—' }}</span>
            @if($addedDate)
                <span class="bxr-added-date">{{ $addedDate }}</span>
            @endif
            @if($daysAgo)
                <span class="bxr-stage-added">{{ $daysAgo }}</span>
            @endif
        </div>
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
