@php
    if (!class_exists(\App\Services\AppTrackerCardPresenter::class, false)) {
        $presenterFile = app_path('Services/AppTrackerCardPresenter.php');
        if (file_exists($presenterFile)) {
            require_once $presenterFile;
        }
    }
    use App\Helper\GeneralMethod;
    use App\Services\AppTrackerCardPresenter;
    use App\Services\ReferenceDataCache;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $company_id = $company_id ?? (auth()->user()->company_id ?? null);
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = ReferenceDataCache::feedbackStatus();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
    $symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
        0 => "INR",
        1 => "EUR",
        2 => "JPY",
        3 => "GBP",
        4 => "USD",
        5 => "AED"
    ];

    $stageRailColors = [
        8 => '#f59e0b', // InReview
        1 => '#0891b2', // Sourcing (Client Tracker cyan)
        2 => '#a855f7', // Screening
        3 => '#009ef7', // Evaluation / Interview
        4 => '#ec4899', // Negotiation
        5 => '#f97316', // Offered
        6 => '#10b981', // Joined
        7 => '#ef4444', // Rejected
        9 => '#ef4444', // Rejected / Archived
    ];
    $stageBadgeThemes = [
        8 => 'background:#fef3c7;color:#92400e;border-color:#fde68a;',
        1 => 'background:#ecfeff;color:#0e7490;border-color:#a5f3fc;',
        2 => 'background:#faf5ff;color:#7e22ce;border-color:#e9d5ff;',
        3 => 'background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;',
        4 => 'background:#fdf2f8;color:#be185d;border-color:#fbcfe8;',
        5 => 'background:#fff7ed;color:#c2410c;border-color:#fed7aa;',
        6 => 'background:#dcfce7;color:#15803d;border-color:#bbf7d0;',
        7 => 'background:#fee2e2;color:#b91c1c;border-color:#fecaca;',
        9 => 'background:#fee2e2;color:#b91c1c;border-color:#fecaca;',
    ];

    // Pre-fetch job mode/currency & required skills once if $rowMaps was not passed
    $jobIdInt = (int) ($jobid ?? 0);
    $cachedJobData = isset($rowMaps['jobs'][$jobIdInt])
        ? (object) $rowMaps['jobs'][$jobIdInt]
        : ($jobIdInt > 0 ? DB::table('jobs')->select('max_sal_mode', 'max_salary_currency')->where('id', $jobIdInt)->first() : null);

    if (isset($rowMaps['skills'][$jobIdInt])) {
        $jobRequiredSkills = collect($rowMaps['skills'][$jobIdInt])->pluck('skill_name')->toArray();
    } elseif ($jobIdInt > 0) {
        $jobRequiredSkills = array_values(array_unique(array_filter(array_map('trim', DB::table('job_metronics_skills')->where('job_id', $jobIdInt)->pluck('skill_name')->toArray()))));
    } else {
        $jobRequiredSkills = [];
    }
    $countryNameCache = [];
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $row->id = $row->candidate_id ?? ($row->id ?? 0);
            $row->condidate_id = $row->wiseId ?? ($row->condidate_id ?? 0);
            $candAppId = (int) $row->id;
            $wiseId = (int) $row->condidate_id;

            $jobMode = $cachedJobData->max_sal_mode ?? (@$row->current_ctc_mode ?? 0);
            $jobCurrency = $cachedJobData->max_salary_currency ?? (@$row->current_ctc_cur ?? 0);
            $symbols = $symbolsValue[$jobCurrency] ?? \App\Services\CurrencyService::resolveCurrencyCode($jobCurrency);

            // Skills & Match %
            $rawSkillsStr = (string) (@$row->skills ?? '');
            $skillArr = array_values(array_filter(array_map('trim', explode(',', $rawSkillsStr))));
            $percentage = GeneralMethod::skill_percentage_from_array($jobRequiredSkills, $skillArr);
            $matchScore = (int) round((float) rtrim(@$percentage ?: '0', '%'));
            $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
            $matchBg = $matchStyle['bg'];
            $matchColor = $matchStyle['color'];
            $matchedSkillsArr = GeneralMethod::matchedSkillsList($skillArr, $jobRequiredSkills);
            $unmatchedSkillsArr = array_values(array_udiff($skillArr, $matchedSkillsArr, 'strcasecmp'));

            // Avatar / Initial & Duplicate Flag
            $trimmedFullName = trim((string) (@$row->full_name ?? ''));
            $displayFullName = $trimmedFullName !== '' ? ucwords($trimmedFullName) : 'Unnamed Candidate';
            $firstChar = $trimmedFullName !== '' ? strtolower(mb_substr($trimmedFullName, 0, 1)) : 'c';
            $charOrd = ord($firstChar);
            if ($charOrd >= 97 && $charOrd <= 101) {
                $bgTextDecore = 'bg-light-danger text-danger';
            } elseif ($charOrd >= 102 && $charOrd <= 106) {
                $bgTextDecore = 'bg-light-warning text-warning';
            } elseif ($charOrd >= 107 && $charOrd <= 111) {
                $bgTextDecore = 'bg-light-primary text-primary';
            } elseif ($charOrd >= 112 && $charOrd <= 116) {
                $bgTextDecore = 'bg-light-success text-success';
            } elseif ($charOrd >= 117 && $charOrd <= 121) {
                $bgTextDecore = 'bg-dark text-white';
            } else {
                $bgTextDecore = 'bg-light-secondary text-dark';
            }

            $duplicate = (int) ($row->duplicate ?? 0);
            $flag = $duplicate === 1 ? 1 : 0;
            $isBench = (@$row->condidate_type == 'bench_condidate');
            $photo = !empty(@$row->photo) ? $row->photo : '';

            // Experience formatting (preserving exact decimals like 3.0, 5.6, 9.10; trimming dangling dots like "10.")
            $fmtExp = function ($val) {
                $s = trim((string) $val);
                if ($s === '' || $s === '.' || $s === '0.') {
                    return '0';
                }
                if (strpos($s, '.') !== false) {
                    $parts = explode('.', $s, 2);
                    if ($parts[1] === '00' || $parts[1] === '') {
                        return $parts[0] !== '' ? $parts[0] : '0';
                    }
                }
                return $s;
            };
            $total_exp_raw = $row->total_exp ?? 0;
            $rel_exp_raw = $row->rel_exp ?? 0;
            if ((string) $total_exp_raw === '0.') {
                $total_exp_raw = '0';
                $rel_exp_raw = str_replace('.', '', (string) $rel_exp_raw);
            }
            $formattedExperience = $fmtExp($total_exp_raw);
            $rel_formattedExperience = ($total_exp_raw == 0 && $rel_exp_raw == 0) ? '' : $fmtExp($rel_exp_raw);
            $printExpStr = $formattedExperience . ' Y' . ($rel_formattedExperience !== '' ? ' (Rel: ' . $rel_formattedExperience . ' Y)' : '');

            // Compensation formatting
            $safeJobCurrency = $jobCurrency ?? 0;
            $safeJobMode = $jobMode ?? 0;
            $mod = $safeJobMode;

            $currencymain = GeneralMethod::currency();
            $currencyCode = $currencymain[$safeJobCurrency] ?? \App\Services\CurrencyService::resolveCurrencyCode($safeJobCurrency);
            if (!empty($row->ectc_yearly)) {
                GeneralMethod::conversionRates($currencyCode, $row->ectc_yearly, 0);
                GeneralMethod::conversionRates($currencyCode, $row->ctc_yearly ?? 0, 0);
            }
            $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly ?? 0, $symbols, $mod);
            $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly ?? 0, $symbols, $mod);
            $current_ctc_cur = \App\Services\CurrencyService::resolvePlainSymbolFromLegacy(
                $safeJobCurrency,
                $currencySymbole,
                $billFormatViewSalary->symbol ?? null
            );
            $ctc = explode('.', GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount))[0];
            $ectc = explode('.', GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount))[0];
            $current_ctc_mode = $salaryMode[$safeJobMode] ?? '';
            $current_ectc_mode = $salaryMode[$safeJobMode] ?? '';
            $ectcModeSuffix = $current_ectc_mode !== '' ? '/' . $current_ectc_mode : '';
            $ctcModeSuffix = $current_ctc_mode !== '' ? '/' . $current_ctc_mode : '';
            $outsourcing_type = (@$row->outsourcing_type == 1) ? 'Fulltime' : 'Contract';
            $printSalaryStr = $current_ctc_cur . $ectc . $ectcModeSuffix . ($isBench ? ' (' . $outsourcing_type . ')' : ' | Cur: ' . $current_ctc_cur . $ctc . $ctcModeSuffix);

            // Notice period (only `NP: X Days` / `NP: Immediate` / `SNP LWD ...`; hidden if empty)
            $noticeData = AppTrackerCardPresenter::resolveNoticePeriod($row, true);
            $np = $noticeData['display'];
            $npClass = 'caf-notice-' . $noticeData['classSuffix'];

            // Interview schedule (pre-fetched via $rowMaps['interviews'] & stage_interviews)
            $masterStatusId = (int) ($row->job_master_status_id ?? 8);
            $canScheduleInterview = in_array('add_schedule', (array) ($userPermissions ?? []));
            $schedule_date = '';
            $schedule_time = '';
            $iKey = $candAppId . '_' . $jobIdInt;
            $wKey = 'w_' . $wiseId . '_' . $jobIdInt;
            $stageIntRow = null;
            if (isset($rowMaps['interviews'])) {
                $intRow = $rowMaps['interviews'][$wKey] ?? ($rowMaps['interviews'][$iKey] ?? null);
                $intRowQ = $rowMaps['interviews'][$iKey] ?? ($rowMaps['interviews'][$wKey] ?? null);
                if (isset($rowMaps['stage_interviews'])) {
                    $stageIntRow = $rowMaps['stage_interviews'][$wKey . '_' . $masterStatusId] ?? ($rowMaps['stage_interviews'][$iKey . '_' . $masterStatusId] ?? null);
                } elseif ($intRow && (int) ($intRow->stage ?? 0) === $masterStatusId && strtolower((string) ($intRow->status ?? '')) !== 'canceled') {
                    $stageIntRow = $intRow;
                }
            } else {
                $intRow = DB::table('interview_schedules')
                    ->select('*')
                    ->where('interview_job_wise_con_id', $wiseId)
                    ->orderBy('interview_schedules.id', 'DESC')
                    ->first();
                $intRowQ = DB::table('interview_schedules')
                    ->select('*')
                    ->where('job_application_id', $candAppId)
                    ->orderBy('interview_schedules.id', 'DESC')
                    ->first();
                $stageIntRow = DB::table('interview_schedules')
                    ->select('*')
                    ->where(function ($q) use ($candAppId, $wiseId) {
                        if ($candAppId > 0) $q->where('job_application_id', $candAppId);
                        if ($wiseId > 0) $q->orWhere('interview_job_wise_con_id', $wiseId);
                    })
                    ->where('interview_job_id', $jobIdInt)
                    ->where('stage', (string) $masterStatusId)
                    ->where(function ($q) {
                        $q->whereNull('status')->orWhere('status', '!=', 'canceled');
                    })
                    ->orderBy('interview_schedules.id', 'DESC')
                    ->first();
            }
            $hasStageInterview = ($stageIntRow !== null);
            $displayIntRow = $stageIntRow ?? $intRow;
            if ($displayIntRow && !empty($displayIntRow->schedule_date) && $displayIntRow->schedule_date !== '0000-00-00 00:00:00') {
                $schedule_date = date('d M, Y', strtotime($displayIntRow->schedule_date));
                $timezone_abbreviation = !empty($displayIntRow->timezone_abbreviation) ? ' (' . $displayIntRow->timezone_abbreviation . ')' : '';
                $schedule_time = trim(Carbon::parse($displayIntRow->schedule_date)->format('h:i A') . $timezone_abbreviation);
            }
            $feedbackGiven = false;
            $row3Text = '';
            $row3Class = '';
            $fbRow = $stageIntRow ?? ($intRowQ ?? $intRow);
            if ($fbRow) {
                if (isset($fbRow->feedback) && $fbRow->feedback >= 0) {
                    $feedbackGiven = true;
                    $row3Text = @$feedback_status[@$fbRow->feedback]['name'];
                    $row3Class = @$feedback_status[@$fbRow->feedback]['classname'];
                } else {
                    $interviewModeLabels = [1 => 'Telecon', 2 => 'Video Call', 3 => 'Face To Face', 4 => 'Other'];
                    $row3Text = $interviewModeLabels[(int) @$fbRow->interview_mode] ?? '';
                }
            }
            $printInterviewStr = !empty($schedule_date)
                ? trim($schedule_date . ' ' . $schedule_time . ($row3Text !== '' ? ' - ' . $row3Text : ''))
                : 'Not Scheduled';

            // Location & Qualification (uses ES fields + pre-fetched $citiesMap first)
            $row_country_name = trim((string) (@$row->country_name ?? ''));
            if ($row_country_name === '') {
                if (!empty(@$row->country) && is_numeric(@$row->country)) {
                    $cid = (int) $row->country;
                    if (!isset($countryNameCache[$cid])) {
                        $cArr = GeneralMethod::getCountryName($cid);
                        $resolvedC = '';
                        foreach ($cArr as $cval) {
                            $resolvedC = ucwords($cval['name'] ?? '');
                        }
                        $countryNameCache[$cid] = $resolvedC;
                    }
                    $row_country_name = $countryNameCache[$cid];
                }
            }

            $row_city_name = trim((string) (@$row->city_name ?? ''));
            if ($row_city_name === '') {
                if (@$row->country == 101 && !empty(@$row->city_id)) {
                    $row_city_name = $citiesMap[$row->city_id] ?? (DB::table('city_list')->where('city_id', @$row->city_id)->value('city') ?? '');
                } else {
                    $row_city_name = trim((string) (@$row->city ?? ''));
                }
            }

            $row_city_name = AppTrackerCardPresenter::cleanText($row_city_name);
            $row_country_name = AppTrackerCardPresenter::cleanText($row_country_name);
            $locShort = ($row_city_name !== '' || $row_country_name !== '')
                ? trim(($row_city_name !== '' ? ucwords(mb_substr($row_city_name, 0, 18)) : '') . ($row_country_name !== '' ? ', ' . strtoupper(mb_substr($row_country_name, 0, 2)) : ''), ', ')
                : '';
            $locFull = trim(($row_city_name !== '' ? ucwords($row_city_name) : '') . ($row_country_name !== '' ? ', ' . strtoupper($row_country_name) : ''), ', ');

            $cardMeta = AppTrackerCardPresenter::present($row, $row_country_name, true);
            $displayDesignation = $cardMeta['designation'];
            $displayCurrentCompany = $cardMeta['current_company'];
            $displayQualification = $cardMeta['qualification'];

            // Ownership (Agency recruiter & actual submitting company)
            $post_company_name = @$row->submitted_by_company_name;
            if (!$post_company_name) {
                $post_company_name = @$row->post_company_name;
            }
            if (!$post_company_name) {
                $post_company_name = @$row->company_name;
            }
            $rowpostedby = AppTrackerCardPresenter::cleanText(@$row->posted_by ?? '');
            $rowpostedby = $rowpostedby !== '' ? ucwords($rowpostedby) : '';
            $post_company_name = AppTrackerCardPresenter::cleanText($post_company_name);
            $printOwnerStr = trim($rowpostedby . ($post_company_name !== '' ? ' (' . $post_company_name . ')' : ''));

            // Stage & Sub-stage (InReview = 8 editable by agency; else client-managed locked notice)
            $masterStatusId = (int) ($row->job_master_status_id ?? 8);
            $stageAccentColor = $stageRailColors[$masterStatusId] ?? '#64748b';
            $stagePillTheme = $stageBadgeThemes[$masterStatusId] ?? 'background:#f1f5f9;color:#334155;border-color:#cbd5e1;';
            $subStageName = '';
            if ($masterStatusId === 8) {
                $subStageName = 'Private';
            }
            if (!empty(@$row->childStatus) && @$row->childStatus > 0) {
                if (isset($rowMaps['child_status_map'])) {
                    $subStageName = $rowMaps['child_status_map'][(int) $row->childStatus] ?? $subStageName;
                } else {
                    $childStatus = DB::table('candidate_status')->select('name')->where('id', $row->childStatus)->first();
                    if (!empty($childStatus)) {
                        $subStageName = @$childStatus->name;
                    }
                }
            }
            $subStageName = trim((string) $subStageName) !== '' ? $subStageName : '—';
            $masterStageLabel = trim((string) (@$row->name ?? @$row->master_status_name ?? 'InReview'));
            $isStageEditable = ($masterStatusId === 8);
            $printStageStr = ucwords($masterStageLabel) . ($subStageName !== '—' ? ' / ' . $subStageName : '');
        @endphp

        <!-- Master Primary Candidate Card (Rule 20 Modern Card + Sub-Card) -->
        <div class="caf-cand-card"
             data-entity="candidate"
             data-candidate-id="{{ $candAppId }}"
             data-wise-id="{{ $wiseId }}"
             data-job-id="{{ $jobIdInt }}"
             data-stage-id="{{ $masterStatusId }}"
             data-print-name="{{ e($displayFullName) }}"
             data-print-desig="{{ e($displayDesignation) }}"
             data-print-match="{{ $matchScore }}%"
             data-print-exp="{{ e($printExpStr) }}"
             data-print-salary="{{ e($printSalaryStr) }}"
             data-print-notice="{{ e($np) }}"
             data-print-company="{{ e($displayCurrentCompany) }}"
             data-print-interview="{{ e($printInterviewStr) }}"
             data-print-location="{{ e($locFull) }}"
             data-print-edu="{{ e(strtoupper($displayQualification)) }}"
             data-print-owner="{{ e($printOwnerStr) }}"
             data-print-stage="{{ e($printStageStr) }}"
             style="border-left: 3.5px solid {{ $stageAccentColor }} !important;">

            <!-- Upper Discrete 6 Sub-Cards Grid -->
            <div class="caf-cand-grid">

                <!-- SUB-CARD 1: CANDIDATE INFO -->
                <div class="caf-cand-subcard caf-subcard-identity">
                    <div class="caf-subcard-hdr">
                        <label class="caf-chk-wrap" title="Select candidate">
                            <input class="form-check-input chk_list_item"
                                   type="checkbox"
                                   value="{{ $candAppId }}"
                                   data-candidate-id="{{ $candAppId }}"
                                   data-wise-id="{{ $wiseId }}"
                                   data-jobId="{{ $jobIdInt }}"
                                   data-match="{{ $matchScore }}"
                                   data-skills="{{ e($rawSkillsStr) }}"
                                   data-cname="{{ e(@$row->full_name ?? '') }}" />
                            <span class="caf-subcard-label">Candidate Info</span>
                        </label>
                        <div class="caf-hdr-badges">
                            @if ($isBench)
                                <span class="caf-pill-bench" title="Bench Talent">Bench</span>
                            @endif
                            @if ($flag === 1)
                                <span class="caf-pill-dup" title="Duplicate candidate detected">Duplicate</span>
                            @endif
                        </div>
                    </div>
                    <div class="caf-subcard-body">
                        <div class="caf-identity-row">
                            <a href="javascript:void(0);"
                               data-row-id="{{ $wiseId }}"
                               data-wise-id="{{ $candAppId }}"
                               class="caf-avatar-box show-application-detail {{ $flag === 1 ? 'is-dup btn-light-danger pulse pulse-danger' : $bgTextDecore }}"
                               title="{{ $flag === 1 ? 'Duplicate — View Profile' : 'View Candidate Profile' }}">
                                @if (!empty($photo))
                                    <img src="{{ $photo }}" alt="{{ e($displayFullName) }}" />
                                @else
                                    <span>{{ strtoupper($firstChar) }}</span>
                                @endif
                                @if ($flag === 1)
                                    <span class="pulse-ring"></span>
                                @endif
                            </a>
                            <div class="caf-identity-meta">
                                <a href="javascript:void(0);"
                                   data-row-id="{{ $wiseId }}"
                                   data-wise-id="{{ $candAppId }}"
                                   class="caf-cand-name show-application-detail {{ $flag === 1 ? 'text-danger' : '' }}"
                                   title="{{ e($displayFullName) }}">
                                    {{ $displayFullName }}
                                </a>
                                <div class="caf-cand-desig {{ $flag === 1 ? 'text-danger' : '' }}" title="{{ $displayDesignation !== '' ? e($displayDesignation) : 'Designation: –' }}">
                                    {{ $displayDesignation !== '' ? $displayDesignation : '–' }}
                                </div>
                                <div class="caf-cand-company" title="{{ $displayCurrentCompany !== '' ? e($displayCurrentCompany) : 'Current Company: –' }}">
                                    <i class="fas fa-building me-1"></i><span>{{ $displayCurrentCompany !== '' ? $displayCurrentCompany : '–' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: COMPENSATION & EXP -->
                <div class="caf-cand-subcard caf-subcard-comp">
                    <div class="caf-subcard-hdr">
                        <span class="caf-subcard-label">Compensation &amp; Exp</span>
                    </div>
                    <div class="caf-subcard-body">
                        <div class="caf-val-salary" title="Expected / Current CTC">
                            <span class="caf-sal-main">{{ $current_ctc_cur }}{{ $ectc }}@if ($ectcModeSuffix !== '')<span class="caf-sal-mode">{{ $ectcModeSuffix }}</span>@endif</span>
                            @if (!$isBench)
                                <span class="caf-sal-sub">Cur: {{ $current_ctc_cur }}{{ $ctc }}{{ $ctcModeSuffix }}</span>
                            @else
                                <span class="caf-sal-sub text-primary">{{ $outsourcing_type }}</span>
                            @endif
                        </div>
                        <div class="caf-val-exp" title="Total: {{ $formattedExperience }} Yrs{{ $rel_formattedExperience !== '' ? ' | Relevant: ' . $rel_formattedExperience . ' Yrs' : '' }}">
                            <i class="far fa-clock me-1"></i>
                            <span><strong>{{ $formattedExperience }} Y</strong> Total</span>
                            @if ($rel_formattedExperience !== '')
                                <span class="caf-rel-bracket">(Rel <strong>{{ $rel_formattedExperience }} Y</strong>)</span>
                            @endif
                        </div>
                        <div class="caf-val-notice" title="Notice Period: {{ $np !== '' ? e($np) : '–' }}">
                            <i class="fas fa-hourglass-half me-1"></i>
                            @if ($np !== '')
                                <span class="caf-notice-txt {{ $npClass }}">{{ $np }}</span>
                            @else
                                <span class="caf-notice-txt text-muted">–</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 3: LOCATION & DEGREE -->
                <div class="caf-cand-subcard caf-subcard-loc">
                    <div class="caf-subcard-hdr">
                        <span class="caf-subcard-label">Location &amp; Degree</span>
                    </div>
                    <div class="caf-subcard-body">
                        <div class="caf-loc-city" title="{{ $locFull !== '' ? e($locFull) : 'Location: –' }}">
                            <i class="fas fa-map-marker-alt me-1"></i><span>{{ $locShort !== '' ? $locShort : '–' }}</span>
                        </div>
                        @php
                            $wmIcon = $cardMeta['work_mode_icon'] !== '' ? $cardMeta['work_mode_icon'] : 'fas fa-building';
                            $wmLabel = $cardMeta['work_mode_label'] !== '' ? $cardMeta['work_mode_label'] : '–';
                        @endphp
                        <div class="caf-loc-mode" title="{{ $cardMeta['work_mode_label'] !== '' ? e($cardMeta['work_mode_label']) : 'Work Mode: –' }}">
                            <i class="{{ $wmIcon }} me-1"></i><span>{{ $wmLabel }}</span>
                        </div>
                        <div class="caf-loc-edu" title="{{ $displayQualification !== '' ? e(strtoupper($displayQualification)) : 'Education / Degree: –' }}">
                            <i class="fas fa-graduation-cap me-1"></i><span class="{{ $displayQualification !== '' ? 'caf-edu-uppercase' : '' }}">{{ $displayQualification !== '' ? $displayQualification : '–' }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: INTERVIEW & FEEDBACK -->
                <div class="caf-cand-subcard caf-subcard-interview">
                    <div class="caf-subcard-hdr">
                        <span class="caf-subcard-label">Interview</span>
                    </div>
                    <div class="caf-subcard-body">
                        @if ($hasStageInterview && !empty($schedule_date))
                            <div class="caf-int-date">
                                <i class="far fa-calendar-alt me-1 text-primary"></i>
                                <span>{{ $schedule_date }}</span>
                            </div>
                            <div class="caf-int-time">
                                <i class="far fa-clock me-1"></i>
                                <span>{{ $schedule_time ?: 'Time TBD' }}</span>
                            </div>
                            @if ($row3Text !== '')
                                @if ($feedbackGiven)
                                    <span class="badge {{ $row3Class }} caf-int-fb-badge">
                                        <i class="fas fa-check me-1"></i>{{ $row3Text }}
                                    </span>
                                @else
                                    <span class="caf-int-mode-pill">
                                        <i class="fas fa-video me-1"></i>{{ $row3Text }}
                                    </span>
                                @endif
                            @endif
                        @else
                            @if (!empty($schedule_date))
                                <div class="caf-int-date" title="Previous stage interview: {{ e($printInterviewStr) }}">
                                    <i class="far fa-calendar-alt me-1 text-muted"></i>
                                    <span>{{ $schedule_date }}</span>
                                </div>
                            @else
                                <div class="caf-int-empty">
                                    <i class="far fa-calendar-times me-1"></i>Not Scheduled
                                </div>
                            @endif
                            @if ($canScheduleInterview)
                                <button type="button"
                                        class="caf-int-schedule-btn app_interview_schedule"
                                        rel="{{ $wiseId ?: $candAppId }}"
                                        data-app-id="{{ $candAppId }}"
                                        data-wise-id="{{ $wiseId }}"
                                        data-job-id="{{ $jobIdInt }}"
                                        title="Schedule interview for current stage">
                                    <i class="far fa-calendar-plus"></i>
                                    <span>Schedule Interview</span>
                                </button>
                            @elseif (empty($schedule_date))
                                <div class="caf-int-empty-sub">Pending interview</div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- SUB-CARD 5: OWNERSHIP & RECRUITER -->
                <div class="caf-cand-subcard caf-subcard-owner">
                    <div class="caf-subcard-hdr">
                        <span class="caf-subcard-label"><i class="fas fa-user-tie me-1"></i>Ownership &amp; Recruiter</span>
                    </div>
                    <div class="caf-subcard-body">
                        <div class="caf-owner-name" title="{{ $rowpostedby !== '' ? e($rowpostedby) : 'Recruiter: –' }}">
                            <i class="fas fa-user-circle text-primary me-1"></i><span>{{ $rowpostedby !== '' ? $rowpostedby : '–' }}</span>
                        </div>
                        <div class="caf-owner-company" title="{{ $post_company_name !== '' ? e($post_company_name) : 'Company: –' }}">
                            <i class="fas fa-building me-1"></i><span>{{ $post_company_name !== '' ? $post_company_name : '–' }}</span>
                        </div>
                        <div class="caf-owner-submitted" title="{{ $cardMeta['submitted_ago'] !== '' ? e('Submitted: ' . $cardMeta['submitted_ago']) : 'Submitted: –' }}">
                            <i class="far fa-calendar-alt me-1"></i><span>Submitted: {{ $cardMeta['submitted_ago'] !== '' ? $cardMeta['submitted_ago'] : '–' }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 6: PIPELINE & STAGE (Entire Card Clickable) -->
                <div class="caf-cand-subcard caf-subcard-stage {{ $isStageEditable ? 'client_stage_sub_stage' : 'client_stage_locked_notice' }}"
                     data-row-id="{{ $wiseId }}"
                     @if ($isStageEditable)
                         data-toggle="modal"
                         data-target="#client_stage_sub_stage"
                     @endif
                     role="button"
                     tabindex="0"
                     title="{{ $isStageEditable ? 'Click anywhere on this stage card to submit or update stage' : 'Managed directly by the client' }}">
                    <div class="caf-subcard-hdr">
                        <span class="caf-subcard-label">Pipeline &amp; Stage</span>
                    </div>
                    <div class="caf-subcard-body">
                        @if ($isStageEditable)
                            <div class="caf-stage-trigger">
                                <span class="caf-stage-pill {{ @$row->css_style }}" style="{{ $stagePillTheme }}">
                                    {{ ucwords(mb_substr($masterStageLabel, 0, 20)) }}
                                </span>
                                @if ($subStageName !== '' && $subStageName !== '—')
                                    <span class="caf-substage-txt" title="{{ e($subStageName) }}">
                                        Sub: <strong>{{ $subStageName }}</strong>
                                    </span>
                                @endif
                                <span class="caf-stage-cue">Update Stage <i class="fas fa-chevron-right ms-1"></i></span>
                            </div>
                        @else
                            <div class="caf-stage-trigger">
                                <span class="caf-stage-pill {{ @$row->css_style }}" style="{{ $stagePillTheme }}">
                                    {{ ucwords(mb_substr($masterStageLabel, 0, 20)) }}
                                </span>
                                @if ($subStageName !== '' && $subStageName !== '—')
                                    <span class="caf-substage-txt" title="{{ e($subStageName) }}">
                                        Sub: <strong>{{ $subStageName }}</strong>
                                    </span>
                                @endif
                                <span class="caf-stage-cue is-locked"><i class="fas fa-lock me-1"></i>Client Managed</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- SUB-CARD 7: FULL-WIDTH INTELLIGENCE & SKILLS STRIP -->
            <div class="caf-cand-bottom">
                <div class="caf-bottom-left">
                    <span class="jw-row-match-badge caf-match-pill"
                          data-candidate-id="{{ $candAppId }}"
                          data-wise-id="{{ $wiseId }}"
                          data-match-pct="{{ $matchScore }}"
                          style="background:{{ $matchBg }};color:{{ $matchColor }};">
                        Match {{ $percentage ?: '0%' }}
                    </span>
                    <div class="caf-skills-scroll">
                        @if (count($matchedSkillsArr) > 0 || count($unmatchedSkillsArr) > 0)
                            @foreach ($matchedSkillsArr as $sval)
                                <span class="caf-skill-pill is-matched" title="Matches a required skill">{{ ucfirst($sval) }}</span>
                            @endforeach
                            @foreach ($unmatchedSkillsArr as $sval)
                                <span class="caf-skill-pill">{{ ucfirst($sval) }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="caf-bottom-right">
                    @php
                        $hasResume = !empty($row->resume) || !empty($rowMaps['resume_map'][$candAppId] ?? null);
                    @endphp
                    @if ($hasResume)
                        <span class="caf-resume-pill jw-open-resume-tab cursor-pointer"
                              data-row-id="{{ $candAppId }}"
                              data-wise-id="{{ $wiseId }}"
                              data-open-tab="resume_cv"
                              title="Click to view Resume / CV">
                            <i class="fas fa-check-circle"></i><span>Resume</span>
                        </span>
                    @else
                        <span class="caf-nocv-pill" title="Resume missing">
                            <i class="fas fa-exclamation-circle"></i><span>No CV</span>
                        </span>
                    @endif
                    @if ($cardMeta['visa_display'] !== '')
                        <span class="caf-visa-pill" title="{{ e('Visa Status: ' . $cardMeta['visa_raw']) }}">
                            <i class="fas fa-passport"></i><span>{{ $cardMeta['visa_display'] }}</span>
                        </span>
                    @endif
                    @if ($cardMeta['linkedin_url'] !== '')
                        <a href="{{ e($cardMeta['linkedin_url']) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="caf-linkedin-pill"
                           title="LinkedIn Profile Verified">
                            <i class="fab fa-linkedin"></i><span>LinkedIn Verified</span>
                        </a>
                    @endif
                </div>
            </div>

        </div>
    @endforeach
@else
    <div class="caf-empty-card">
        <div class="caf-empty-icon">
            <i class="fas fa-users-slash"></i>
        </div>
        <div class="caf-empty-title">No Candidates Found</div>
        <div class="caf-empty-sub">No submitted candidates currently match your selected pipeline stage or search criteria.</div>
    </div>
@endif
