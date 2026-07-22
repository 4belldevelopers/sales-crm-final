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
@if (count($data) > 0)
    @php
        /* Page-level batch lookups — one query each for the whole page
           instead of one per row (was 2 Elasticsearch searches + 2 MySQL
           queries per job card, ~40 round-trips a page).
           Tenancy: the relation map is scoped to the viewer's company inside
           the bulk helper; skills are filtered per-row by each job's own
           comId below, exactly as the old per-row query did. */
        $pageJobIds  = array_values(array_filter(array_map(fn ($r) => $r['id'] ?? null, $data)));
        $pageViewerCompanyId = auth()->user()->company_id;
        $relationMap = GeneralMethod::getrRcommendationRelationDataBulk($pageViewerCompanyId, $pageJobIds);
        $benchReqMap = GeneralMethod::benchRequestCheckBulk($condidate_id, $pageJobIds);
        $skillsByJob = DB::table('job_metronics_skills')->whereIn('job_id', $pageJobIds)->get()->groupBy('job_id');
    @endphp
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $targetJobId = $row->id;
    $company_id = auth()->user()->company_id;

    $relationget = $relationMap[$targetJobId] ?? (object) [];
    $from_company_id = $relationget->from_company_id ?? null;
    $to_company_id = $relationget->to_company_id ?? null;
    $recommendation_id = $relationget->id ?? null;
    $final_status = $relationget->final_status ?? null;

    $row->final_status = $final_status;
    $row->recommendation_id =$recommendation_id;
    $row->recommendation_relation_id =$recommendation_id;
    $row->to_company_id = $to_company_id;
    $row->from_company_id = $from_company_id;
    $row->companyidcount= $row->comId;
    $row->clients_name = $row->client_name;

    /* Client-type detection — distinguishes an Internal-route recommendation
       (sister/internal company in the from→to chain) from a Direct Client
       recommendation (external). Used to render the reusable .client-badge
       below. Pure data-derivation, no new controller calls — the relation map
       is already loaded above for $to_company_id/$from_company_id. */
    $vcid         = (int) auth()->user()->company_id;
    $relFromCid   = (int) ($row->from_company_id ?? 0);
    $relToCid     = (int) ($row->to_company_id ?? 0);
    $isInternalCx = ($relFromCid === $vcid) || ($relToCid === $vcid);
    $clientTypeKey   = $isInternalCx ? 'internal' : 'direct';
    $clientTypeLabel = $isInternalCx ? 'Internal Client' : 'Direct Client';
    /* The numeric suffix "Internal Client · 1410" is the job-owning company
       (comId) — same on both badges, so users scan "Internal\D" first, ID
       second. */
    $clientTypeId   = (string) ($row->comId ?? $row->companyidcount ?? '');
    if ($clientTypeId === '' || $clientTypeId === '0') { $clientTypeId = ''; }

    $bgTextDecore = $strVal = '';
        $str = trim(@$row->title);
        $str = $strVal = strtolower($str[0]);
        $str = ord($str);
        if ($str >= 97 && $str <= 101) {
            $bgTextDecore = 'fs-3 bg-light-danger text-danger';
        } elseif ($str >= 102 && $str <= 106) {
            $bgTextDecore = 'fs-3 bg-light-warning text-warning';
        } elseif ($str >= 107 && $str <= 111) {
            $bgTextDecore = 'fs-3 bg-light-primary text-primary';
        } elseif ($str >= 112 && $str <= 116) {
            $bgTextDecore = 'fs-3 bg-light-success text-success';
        } elseif ($str >= 117 && $str <= 121) {
            $bgTextDecore = 'fs-3 bg-dark text-secondary';
        } else {
            $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
        }

        $country_id = $country_name = '';
        $jobid = $row->id;

        $client_city_jobs = $row->client_city_jobs;
        $jobCitySelected = is_array($client_city_jobs) ? count($client_city_jobs) : 0;

        $employment_type = 'Full-time';
        if ($row->employment_type == 1) {
            $employment_type = 'Contract';
        }
        $employmentBadgeClass = ($employment_type === 'Contract') ? 'badge-inline badge-contract' : 'badge-inline badge-fulltime';

        $classAdd = "bench-match";
        $textAdd = "Bench Required";
        $textClassAdd = "bench-badge";

        $min_sal_mode = $max_sal_mode = $max_salary_currency = $max_salary = $min_salary = '';

        $mod = $sal_mode ?? 0;
        $cur = $min_salary_currency ?? 0;

        $min_sal_mode = $salaryMode[$mod];
        $max_sal_mode = $salaryMode[$mod];
        $salary_currency = $currencySymbole[@$cur];
        $currencymain = GeneralMethod::currency();
        $currency = $currencymain[$cur];
        $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->max_yearly, 0);
        $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->min_yearly, 0);

        $ectc = $convertedAmounEctc[$mod];
        $ctc = $convertedAmounCtc[$mod];

        $max_salary = '';
        $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
        $min_salary = '';
        $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);

        $work_type = 'Work From Office';
        if($row->work_type=='no'){
            $work_type = "Work From Office";
        }elseif($row->work_type=='yes'){
            $work_type = "Remote";
        }elseif($row->work_type=='other'){
            $work_type = "Hybrid";
        }elseif($row->work_type=='any'){
            $work_type = "Any";
        }
        /* Work-mode badge class + icon — same icon sizing/color as the rest of
           the .job-meta-line pills in JOB DETAILS, so all inline badges share
           one visual rhythm. */
        if ($work_type === 'Remote') {
            $workTypeBadgeClass = 'badge-inline badge-worktype badge-remote';
            $workTypeIcon = 'ri-home-wifi-line';
        } elseif ($work_type === 'Hybrid') {
            $workTypeBadgeClass = 'badge-inline badge-worktype badge-hybrid';
            $workTypeIcon = 'ri-building-2-line';
        } elseif ($work_type === 'Any') {
            $workTypeBadgeClass = 'badge-inline badge-worktype badge-any';
            $workTypeIcon = 'ri-shuffle-line';
        } else {
            $workTypeBadgeClass = 'badge-inline badge-worktype badge-onsite';
            $workTypeIcon = 'ri-map-pin-user-line';
        }

    @endphp

                <div class="agency-card jc-row-wrap {{ $classAdd }}">
                    {{-- Top-right status ribbon — same as before so existing
                         CSS (.bench-badge / "Bench Required" pill) keeps paying
                         off and the .bench-match border style still applies. --}}
                    <div class="{{ $textClassAdd }}"> {{ $textAdd }}</div>

                    {{-- ══ 6-COLUMN GRID — matches service_company/jobs's
                         joblist.css layout language, so the recommended-jobs
                         view feels like an extension of the Jobs module:
                           1. JOB DETAILS   (logo + title + client + meta)
                           2. EXPERIENCE    (vertical: exp / salary / client-type)
                           3. HIRING & LOC  (city + work-mode line)
                           4. HIRING MANAGER (avatar + name)
                           5. STATUS        (status pill + rec. tag)
                           6. ACTIONS       (favorite + bench-req + view T&C) ══ --}}
                    <div class="jc-row">

                        {{-- ── COL 1 · JOB DETAILS ── --}}
                        <div class="jc-col jc-col-job">
                            <div class="job-logo {{ $bgTextDecore }} client-show-detail" data-row-id="{{ $row->id }}" style="cursor: pointer;">{{ ucwords($strVal) }}</div>
                            <div class="job-info">
                                <div class="job-title client-show-detail" data-row-id="{{ $row->id }}">
                                    <a href="javascript:void(0);" style="cursor: pointer;">{{ ucwords(mb_substr(@$row->title, 0, 30)) }}</a>
                                    <i class="ri-verified-badge-fill color-blue"></i>
                                </div>
                                <div class="job-client">
                                    <i class="ri-building-line"></i>
                                    {{ ucwords(@$row->clients_name ?: (@$row->client_name ?: '—')) }}
                                </div>
                                <div class="job-meta-line">
                                    <span class="job-date-inline">
                                        <i class="ri-calendar-line"></i>
                                        @php
                                            $created_at = $row->created_at;
                                            $newDate = GeneralMethod::ymdTodmyNotTime($created_at);
                                            echo $newDate;
                                        @endphp
                                    </span>
                                    <span class="meta-sep">·</span>
                                    <span class="{{ $employmentBadgeClass }}">{{ ucwords($employment_type) }}</span>
                                    <span class="meta-sep">·</span>
                                    <span class="{{ $workTypeBadgeClass }}">
                                        <i class="{{ $workTypeIcon }}"></i> {{ $work_type }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ── COL 2 · EXPERIENCE (vertical: exp / salary / client-type badge) ── --}}
                        <div class="jc-col jc-col-exp">
                            <div class="exp-row">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <span class="exp-row-text">
                                    {{ ((@$row->min_exp) ? @$row->min_exp.' Y' : 'Any') }} - {{ ((@$row->max_exp) ? @$row->max_exp.' Y' : 'Any') }}
                                </span>
                            </div>
                            <div class="sal-row">
                                <i class="sal-currency">{!! $salary_currency !!}</i>
                                <span class="sal-row-text">{{ $min_salary }} - {{ $max_salary }}</span>
                                <span class="sal-mode">{{ mb_substr($min_sal_mode,0,1) ?? '' }}</span>
                            </div>
                            {{-- Reusable Client Type Badge — thin left indicator
                                 bar (matches the spec's "blue left indicator"/
                                 "green left indicator") + soft teal/gold pill
                                 body + colored icon. Used across the Jobs module
                                 as the canonical "Internal vs Direct" tag. --}}
                            <div class="client-badge client-badge--{{ $clientTypeKey }}">
                                <span class="client-badge-stripe"></span>
                                @if($isInternalCx)
                                    <i class="ri-government-line"></i>
                                @else
                                    <i class="ri-customer-service-2-line"></i>
                                @endif
                                <span class="client-badge-label">{{ $clientTypeLabel }}</span>
                                @if($clientTypeId !== '')
                                    <span class="client-badge-id">· {{ $clientTypeId }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- ── COL 3 · HIRING & LOCATION ── --}}
                        <div class="jc-col jc-col-loc">
                            @php
                                $locPrimary = '—';
                                $locCity    = '';
                                $locModeBlk = '';
                                if ($row->work_type == 'yes') {
                                    $locPrimary = 'Remote';
                                    $locModeBlk = 'Remote · Open to remote candidates';
                                } elseif ($row->work_type == 'no' || $row->work_type =='other' || $row->work_type =='any') {
                                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                        $firstCity  = collect($client_city_jobs)->first();
                                        $cityName   = $firstCity['city_name'] ?? null;
                                        $countryNm  = $firstCity['country_name'] ?? null;
                                        $locCity    = ucwords(mb_substr(@$cityName, 0, 18));
                                        $locModeBlk = ucwords(mb_substr(@$countryNm, 0, 10)) . ($locCity !== '' ? ' · '.$locCity : '');
                                        $locPrimary = $locCity !== '' ? $locCity : 'Multiple';
                                    } else {
                                        $locPrimary = 'Multiple Cities';
                                        $locModeBlk = $jobCitySelected . ' locations available';
                                    }
                                }
                            @endphp
                            <div class="loc-row">
                                <i class="ri-map-pin-line"></i>
                                <span class="loc-row-text">{{ $locPrimary }}</span>
                            </div>
                            <div class="loc-mode">{{ $locModeBlk }}</div>
                        </div>

                        {{-- ── COL 4 · HIRING MANAGER ── --}}
                        @php
                            /* HM data isn't always populated (the old code had
                               it hard-set to '----' and the contact lookup
                               commented out). Try common shapes, fall back to a
                               dash + grey initial so the column reads cleanly
                               even without a name. When a name IS available,
                               the .hmlisting class is wired up to the existing
                               delegated recruiter drawer — same handler as the
                               jobs page. */
                            $hmName = '—';
                            if (isset($row->client_contact_info_jobs) && is_array($row->client_contact_info_jobs) && count($row->client_contact_info_jobs) > 0) {
                                $hmName = @$row->client_contact_info_jobs[0]['contact_person_name'] ?: ($row->clients_name ?: '—');
                            } elseif (!empty($row->hiring_manager ?? null)) {
                                $hmName = $row->hiring_manager;
                            }
                            $hmHasName  = ($hmName !== '—' && trim($hmName) !== '');
                            $hmInitials = $hmHasName ? strtoupper(mb_substr($hmName, 0, 1)) : '–';
                        @endphp
                        <div class="jc-col jc-col-hm">
                            <div class="hm-row">
                                <span class="hm-avatar {{ $hmHasName ? 'hm-avatar--filled' : 'hm-avatar--empty' }}">{{ $hmInitials }}</span>
                                <div class="hm-info">
                                    <span class="hm-name {{ $hmHasName ? 'hmlisting' : '' }}"@if($hmHasName) data-row-id="{{ $row->id }}"@endif>{{ ucwords($hmName) }}</span>
                                    <span class="hm-role">Hiring Manager</span>
                                </div>
                            </div>
                        </div>

                        {{-- ── COL 5 · STATUS / PIPELINE ── --}}
                        <div class="jc-col jc-col-status">
                            @php
                                $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                                $status = ucwords($row->status);
                                $class = $job_status[$row->status];
                                $recommendation_id = $row->recommendation_id;

                                if ($status == 'Active') { $status = 'Hiring'; }
                                if ($status == 'Inactive') { $status = 'Closed'; }
                                if (
                                    ($row->final_status == 2 || $row->final_status == 3) &&
                                    @$row->to_company_id == auth()->user()->company_id
                                ) {
                                    $status = 'Removed';
                                    $class = 'closed-bg';
                                }

                                $recommendation_text = __('messages.clinet_tag');
                                $ulink =
                                    '<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ' . @$class . ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' . $status . '</a>';

                                if (@$row->to_company_id == auth()->user()->company_id) {
                                    $ulink = '<a href="javascript:void(0);" class="fs-6 ' . @$class . ' text-hover-success">' . $status . '</a>';
                                }
                            @endphp
                            <span class="status-pill status-pill--{{ strtolower($status) }}">{!! $ulink !!}</span>
                            <span class="status-rec">{{ $recommendation_text }}</span>
                        </div>

                        {{-- ── COL 6 · ACTIONS ── --}}
                        <div class="jc-col jc-col-actions">
                            @php
                                $benchRequestCheck = $benchReqMap[$row->id] ?? null;
                                $requestsend = "checkrequestsend";
                                if ($currentRowJob->status == 2) {
                                    $requestsend = "request-send";
                                }
                            @endphp
                            <i class="ri-heart-line favorite-icon"></i>
                            <div class="action-stack">
                                @if ($benchRequestCheck == null)
                                    <a href="javascript:void(0);" data-jobId="{{ $row->id }}" data-conId="{{ $currentRowJob->id }}" class="btn btn-xsm btn-primary {{ $requestsend }}">
                                        <i class="ri-send-plane-line"></i> Send Bench Request
                                    </a>
                                @else
                                    @if ($benchRequestCheck->ac_status == 1)
                                        <span class="action-status action-status--added"><i class="ri-check-line"></i> Added</span>
                                    @else
                                        <span class="action-status action-status--sent"><i class="ri-time-line"></i> Request sent</span>
                                    @endif
                                @endif
                                <button class="btn btn-xsm btn-outline jc-tc-btn client-terms-listing-find-job" data-row-id="{{ $row->id }}" data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">
                                    <i class="ri-chat-3-line"></i> View T&amp;C
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ── SKILLS row — short match% badge + up to 4 tag pills,
                         tucked in a vertical-margin-only strip below the grid
                         so the row's height stays compact while preserving the
                         existing skill/match logic 1:1. ── --}}
                    @php
                        $skilltag = '';
                        $skilltagclass = '';
                        $totalSkill = 0;
                        $matchBadge = '';
                        if (empty($limit)) {
                            $skillArr = ($skillsByJob[$row->id] ?? collect())
                                ->where('company_id', $row->comId)
                                ->values();

                            $jobRequiredSkillNames = $skillArr->pluck('skill_name')->filter()->values()->all();
                            $candidateSkillArr = array_values(array_filter(array_map('trim', explode(',', $currentRowJob->skills ?? ''))));
                            $matchedSkillsArr = \App\Helper\GeneralMethod::matchedSkillsList($candidateSkillArr, $jobRequiredSkillNames);
                            $totalRequired = count($jobRequiredSkillNames);
                            $matchPct = $totalRequired > 0 ? round(min((count($matchedSkillsArr) / $totalRequired) * 100, 100), 1) : 0;
                            if ($matchPct == 0) { $matchBg = '#f1f5f9'; $matchColor = '#64748b'; }
                            elseif ($matchPct < 50) { $matchBg = '#fee2e2'; $matchColor = '#dc2626'; }
                            elseif ($matchPct <= 90) { $matchBg = '#fef3c7'; $matchColor = '#d97706'; }
                            else { $matchBg = '#dcfce7'; $matchColor = '#16a34a'; }
                            $matchBadge = '<span class="badge my-1" style="background:'.$matchBg.';color:'.$matchColor.';font-weight:600;">Match '.$matchPct.'%</span>  ';

                            if (!empty($skillArr)) {
                                foreach ($skillArr as $key => $sval) {
                                    $isMatched = in_array($sval->skill_name, $matchedSkillsArr, true)
                                        || collect($matchedSkillsArr)->contains(fn($m) => strcasecmp($m, $sval->skill_name) === 0);
                                    if ($key <= 3) {
                                        $skilltag .= $isMatched
                                            ? '<span class="tag-badge jc-skill-matched" title="Matches candidate skill">' . ucfirst($sval->skill_name) . '</span>  '
                                            : '<span class="tag-badge jc-skill-neutral">' . ucfirst($sval->skill_name) . '</span>  ';
                                    }
                                    $totalSkill++;
                                }
                            }
                        } else {
                            $skilltagclass = 'd-none';
                        }
                    @endphp

                    @if (empty($limit))
                        <div class="jc-skills-row {{ $skilltagclass }}" style="margin-top:8px;">
                            {!! $matchBadge !!}{!! $skilltag !!}
                            @if ($totalSkill > 4)
                                <span class="tag-badge jc-skill-overflow client-show-detail" data-row-id="{{ $row->id }}"><a href="javascript:void(0);">+{{ $totalSkill - 4 }}</a></span>
                            @endif
                        </div>
                    @endif
                </div>

    @endforeach
@else
    <div class="rrj-empty">
        <div class="rrj-empty-icon"><i class="ti ti-briefcase-off"></i></div>
        <div class="rrj-empty-title">No matching jobs found</div>
        <div class="rrj-empty-desc">No active client jobs match this candidate's current search and filters. Try widening the skills, location or salary range.</div>
        <button type="button" class="btn btn-sm btn-light-primary" onclick="if(typeof rrjTagifySkills!=='undefined'){$('#rrj_clear_all').trigger('click');fetchBenchRecommendedReqCandidateList();}">
            <i class="ti ti-filter-off" style="margin-right:4px;"></i>Clear Filters
        </button>
    </div>
@endif
