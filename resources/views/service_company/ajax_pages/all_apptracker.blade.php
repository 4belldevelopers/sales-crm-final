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

    $company_id = auth()->user()->company_id ?? (auth()->user()->roles[0]->company_id ?? 0);
    $app_duplicate = auth()->user()->company->app_duplicate ?? 0;
    $app_duplicate_days = auth()->user()->company->app_duplicate_days ?? 0;

    $stageRailColors = [
        8 => '#eab308', // InReview (standardized yellow)
        1 => '#0891b2', // Sourcing (Client Tracker cyan)
        2 => '#a855f7', // Screening
        3 => '#009ef7', // Evaluation / Interview
        4 => '#ec4899', // Negotiation
        5 => '#f97316', // Offered (standardized orange)
        6 => '#10b981', // Joined
        7 => '#ef4444', // Lost
        9 => '#ef4444', // Lost / Archived
        10 => '#64748b', // On Hold
    ];
    $stageBadgeThemes = [
        8 => 'background:#fefce8;color:#854d0e;border-color:#fef08a;',
        1 => 'background:#ecfeff;color:#0e7490;border-color:#a5f3fc;',
        2 => 'background:#faf5ff;color:#7e22ce;border-color:#e9d5ff;',
        3 => 'background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;',
        4 => 'background:#fdf2f8;color:#be185d;border-color:#fbcfe8;',
        5 => 'background:#fff7ed;color:#c2410c;border-color:#fed7aa;',
        6 => 'background:#dcfce7;color:#15803d;border-color:#bbf7d0;',
        7 => 'background:#fee2e2;color:#b91c1c;border-color:#fecaca;',
        9 => 'background:#fee2e2;color:#b91c1c;border-color:#fecaca;',
    ];
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $row->id = $row->candidate_id ?? ($row->id ?? 0);
            $row->add_job_wise_condidates_id = $row->wiseId ?? ($row->add_job_wise_condidates_id ?? 0);
            $candAppId = (int) $row->id;
            $wiseId = (int) $row->add_job_wise_condidates_id;
            $rowJobId = (int) (($row->job_id ?? 0) ?: ($row->jobId ?? 0));

            // Pre-fetched job salary mode & currency (eliminates N+1 query)
            $jobData = isset($rowMaps['jobs'][$rowJobId]) ? (object) $rowMaps['jobs'][$rowJobId] : null;
            $jobMode = $jobData->max_sal_mode ?? @$row->current_ctc_mode;
            $jobCurrency = $jobData->max_salary_currency ?? @$row->current_ctc_cur;
            $symbols = $symbolsValue[$jobCurrency] ?? \App\Services\CurrencyService::resolveCurrencyCode($jobCurrency);

            // Skills & Match %
            $rawSkillsStr = (string) (@$row->skills ?? '');
            $skillArr = array_values(array_filter(array_map('trim', explode(',', $rawSkillsStr))));
            $preSkillRows = $rowMaps['skills'][$rowJobId] ?? [];
            $jobRequiredSkills = collect($preSkillRows)->pluck('skill_name')->toArray();
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
            $cardMeta = AppTrackerCardPresenter::present($row, trim((string) (@$row->country_name ?? '')), false);
            $displayDesignation = $cardMeta['designation'];
            $displayCurrentCompany = $cardMeta['current_company'];

            // Experience formatting (preserving exact decimal formatting like 3.0, 5.6, 9.10; trimming dangling dots like "10.")
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
            $cur = $safeJobCurrency;
            $safeJobMode = $jobMode ?? 0;
            $mod = $safeJobMode;

            $currencymain = GeneralMethod::currency();
            $currencyCode = $currencymain[$cur] ?? \App\Services\CurrencyService::resolveCurrencyCode($cur);
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
            $payAmountFormatted = explode('.', GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount))[0];
            $billAmountFormatted = explode('.', GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount))[0];
            $current_ctc_mode = $salaryMode[$safeJobMode] ?? '';
            $current_ectc_mode = $salaryMode[$safeJobMode] ?? '';
            $ectcModeSuffix = $current_ectc_mode !== '' ? '/' . $current_ectc_mode : '';
            $ctcModeSuffix = $current_ctc_mode !== '' ? '/' . $current_ctc_mode : '';
            $outsourcing_type = (@$row->outsourcing_type == 1) ? 'Fulltime' : 'Contract';
            $canViewSalary = in_array('salary_view', (array) ($userPermissions ?? []));

            $printSalaryStr = $canViewSalary
                ? ($current_ctc_cur . $billAmountFormatted . $ectcModeSuffix . ($isBench ? ' (' . $outsourcing_type . ')' : ' | Cur: ' . $current_ctc_cur . $payAmountFormatted . $ctcModeSuffix))
                : 'N/A';

            // Notice period (only `NP: X Days` / `NP: Immediate` / `SNP LWD ...`; hidden if empty)
            $np = $cardMeta['notice_display'];
            $npClass = 'aptk-notice-' . $cardMeta['notice_class'];

            // Interview schedule (uses pre-fetched map; stage-aware so Schedule Interview re-activates on next stage)
            $masterStatusId = (int) ($row->job_master_status_id ?? 8);
            $canScheduleInterview = in_array('add_schedule', (array) ($userPermissions ?? []));
            $schedule_date = '';
            $schedule_time = '';
            $iKey = $candAppId . '_' . $rowJobId;
            $wKey = 'w_' . $wiseId . '_' . $rowJobId;
            $stageIntRow = null;
            if (isset($rowMaps['interviews'])) {
                $intRow = $rowMaps['interviews'][$iKey] ?? ($rowMaps['interviews'][$wKey] ?? null);
                $intRowCount = $intRow ? 1 : 0;
                if (isset($rowMaps['stage_interviews'])) {
                    $stageIntRow = $rowMaps['stage_interviews'][$iKey . '_' . $masterStatusId] ?? ($rowMaps['stage_interviews'][$wKey . '_' . $masterStatusId] ?? null);
                } elseif ($intRow && (int) ($intRow->stage ?? 0) === $masterStatusId && strtolower((string) ($intRow->status ?? '')) !== 'canceled') {
                    $stageIntRow = $intRow;
                }
            } else {
                try {
                    $intRow = DB::table('interview_schedules')
                        ->select('*')
                        ->where(function ($q) use ($candAppId, $wiseId) {
                            if ($candAppId > 0) $q->where('job_application_id', $candAppId);
                            if ($wiseId > 0) $q->orWhere('interview_job_wise_con_id', $wiseId);
                        })
                        ->where('interview_job_id', $rowJobId)
                        ->orderBy('interview_schedules.id', 'DESC')
                        ->first();
                    $intRowCount = $intRow ? 1 : 0;
                    $stageIntRow = DB::table('interview_schedules')
                        ->select('*')
                        ->where(function ($q) use ($candAppId, $wiseId) {
                            if ($candAppId > 0) $q->where('job_application_id', $candAppId);
                            if ($wiseId > 0) $q->orWhere('interview_job_wise_con_id', $wiseId);
                        })
                        ->where('interview_job_id', $rowJobId)
                        ->where('stage', (string) $masterStatusId)
                        ->where(function ($q) {
                            $q->whereNull('status')->orWhere('status', '!=', 'canceled');
                        })
                        ->orderBy('interview_schedules.id', 'DESC')
                        ->first();
                } catch (\Throwable $e) {
                    $intRow = null;
                    $stageIntRow = null;
                    $intRowCount = 0;
                }
            }
            $hasStageInterview = ($stageIntRow !== null);
            $displayIntRow = $stageIntRow ?? $intRow;
            if ($displayIntRow && !empty($displayIntRow->schedule_date) && $displayIntRow->schedule_date !== '0000-00-00 00:00:00') {
                $schedule_date = date('d M, Y', strtotime($displayIntRow->schedule_date));
                $timezone_abbreviation = !empty($displayIntRow->timezone_abbreviation) ? ' (' . $displayIntRow->timezone_abbreviation . ')' : '';
                $schedule_time = trim((string) (@$displayIntRow->schedule_to_time . ' ' . $timezone_abbreviation));
            }
            $feedbackGiven = false;
            $row3Text = '';
            $row3Class = '';
            if ($displayIntRow) {
                if (isset($displayIntRow->feedback) && $displayIntRow->feedback >= 0) {
                    $feedbackGiven = true;
                    $row3Text = @$feedback_status[@$displayIntRow->feedback]['name'];
                    $row3Class = @$feedback_status[@$displayIntRow->feedback]['classname'];
                } else {
                    $interviewModeLabels = [1 => 'Telecon', 2 => 'Video Call', 3 => 'Face To Face', 4 => 'Other'];
                    $row3Text = $interviewModeLabels[(int) @$displayIntRow->interview_mode] ?? '';
                }
            }
            $printInterviewStr = !empty($schedule_date)
                ? trim($schedule_date . ' ' . $schedule_time . ($row3Text !== '' ? ' - ' . $row3Text : ''))
                : 'Not Scheduled';

            // Location & Qualification
            $locHier = \App\Helper\GeneralMethod::resolveCandidateLocationHierarchy($row);
            $row_city_name = $locHier['city'] !== '' ? $locHier['city'] : AppTrackerCardPresenter::cleanText(@$row->city_name ?? '');
            $row_state_name = $locHier['state'];
            $row_country_name = $locHier['country'] !== '' ? $locHier['country'] : AppTrackerCardPresenter::cleanText(@$row->country_name ?? '');
            $locShort = ($row_city_name !== '' || $row_state_name !== '')
                ? trim(($row_city_name !== '' ? ucwords(mb_substr($row_city_name, 0, 18)) : '') . ($row_state_name !== '' ? ', ' . ucwords(mb_substr($row_state_name, 0, 18)) : ($row_country_name !== '' ? ', ' . strtoupper(mb_substr($row_country_name, 0, 2)) : '')), ', ')
                : '';
            $locFull = $locHier['location_text'] !== '--' ? $locHier['location_text'] : trim(($row_city_name !== '' ? ucwords($row_city_name) : '') . ($row_country_name !== '' ? ', ' . strtoupper($row_country_name) : ''), ', ');
            $displayQualification = $cardMeta['qualification'];

            // Ownership
            if ($company_id != ($row->ClientComID ?? 0) && !empty($row->ClientComID) && $row->ClientComID > 0) {
                $spokKey = $rowJobId . '_' . ($row->ClientComID ?? 0);
                if (isset($rowMaps['spokperson_map'])) {
                    $spokName = $rowMaps['spokperson_map'][$spokKey] ?? null;
                } else {
                    $spokperson = DB::table('job_assign')
                        ->select('users.name')
                        ->join('users', 'users.id', 'job_assign.spok_user_id')
                        ->where('job_assign.job_id', $rowJobId)
                        ->where('job_assign.company_id', $row->ClientComID)
                        ->first();
                    $spokName = @$spokperson->name;
                }
                $rowpostedby = (!empty($spokName)) ? ucwords($spokName) : ucwords((string) @$row->posted_by);
                $rowpostedby = !empty($row->rec_candidate_assign_id) ? ucwords((string) @$row->posted_by) : $rowpostedby;
            } else {
                $rowpostedby = ucwords((string) @$row->posted_by);
            }
            $rowpostedby = AppTrackerCardPresenter::cleanText($rowpostedby);
            $post_company_name = AppTrackerCardPresenter::cleanText(@$row->submitted_by_company_name ?: (@$row->post_company_name ?: (@$row->company_name ?? '')));
            $printOwnerStr = trim($rowpostedby . ($post_company_name !== '' ? ' (' . $post_company_name . ')' : ''));

            // Stage & Sub-stage
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
            $crmStageNameMap = [
                8 => 'Lead',
                1 => 'Qualified',
                2 => 'Engagement',
                3 => 'Engagement',
                4 => 'Negotiation',
                5 => 'Closing',
                6 => 'Won',
                7 => 'Lost',
                9 => 'Lost',
                10 => 'On Hold',
            ];
            $masterStageLabel = $crmStageNameMap[$masterStatusId] ?? trim((string) (@$row->name ?? @$row->master_status_name ?? 'Lead'));
            $printStageStr = ucwords($masterStageLabel) . ($subStageName !== '—' ? ' / ' . $subStageName : '');
        @endphp

        <!-- Master Primary Candidate Card (Rule 20 Modern Card + Sub-Card) -->
        <div class="aptk-cand-card"
             data-entity="candidate"
             data-candidate-id="{{ $candAppId }}"
             data-wise-id="{{ $wiseId }}"
             data-job-id="{{ $rowJobId }}"
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
             data-print-city="{{ e($row_city_name) }}"
             data-print-state="{{ e($row_state_name) }}"
             data-print-country="{{ e($row_country_name) }}"
             data-print-edu="{{ e(strtoupper($displayQualification)) }}"
             data-print-owner="{{ e($printOwnerStr) }}"
             data-print-stage="{{ e($printStageStr) }}"
             style="border-left: 3.5px solid {{ $stageAccentColor }} !important;">

            <!-- Upper Discrete 6 Sub-Cards Grid -->
            <div class="aptk-cand-grid">

                <!-- SUB-CARD 1: CANDIDATE INFO -->
                <div class="aptk-cand-subcard aptk-subcard-identity">
                    <div class="aptk-subcard-hdr">
                        <label class="aptk-chk-wrap" title="Select candidate">
                            <input class="form-check-input chk_list_item"
                                   type="checkbox"
                                   data-Wiid="{{ $wiseId }}"
                                   value="{{ $candAppId }}"
                                   data-wiseId="{{ $wiseId }}"
                                   data-candidate-id="{{ $candAppId }}"
                                   data-match="{{ $matchScore }}"
                                   data-skills="{{ e($rawSkillsStr) }}"
                                   data-cname="{{ e(@$row->full_name ?? '') }}" />
                            <span class="aptk-subcard-label">Candidate Info</span>
                        </label>
                        <div class="aptk-hdr-badges">
                            @if ($isBench)
                                <span class="aptk-pill-bench" title="Bench Talent">Bench</span>
                            @endif
                            @if ($flag === 1)
                                <span class="aptk-pill-dup" title="Duplicate candidate detected">Duplicate</span>
                            @endif
                        </div>
                    </div>
                    <div class="aptk-subcard-body">
                        <div class="aptk-identity-row">
                            <a href="javascript:void(0);"
                               data-row-id="{{ $wiseId }}"
                               data-wise-id="{{ $candAppId }}"
                               class="aptk-avatar-box show-application-detail {{ $flag === 1 ? 'is-dup btn-light-danger pulse pulse-danger' : $bgTextDecore }}"
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
                            <div class="aptk-identity-meta">
                                <a href="javascript:void(0);"
                                   data-row-id="{{ $wiseId }}"
                                   data-wise-id="{{ $candAppId }}"
                                   class="aptk-cand-name show-application-detail {{ $flag === 1 ? 'text-danger' : '' }}"
                                   title="{{ e($displayFullName) }}">
                                    {{ $displayFullName }}
                                </a>
                                <div class="aptk-cand-desig {{ $flag === 1 ? 'text-danger' : '' }}" title="{{ $displayDesignation !== '' ? e($displayDesignation) : 'Designation: –' }}">
                                    {{ $displayDesignation !== '' ? $displayDesignation : '–' }}
                                </div>
                                <div class="aptk-cand-company" title="{{ $displayCurrentCompany !== '' ? e($displayCurrentCompany) : 'Current Company: –' }}">
                                    <i class="fas fa-building me-1"></i><span>{{ $displayCurrentCompany !== '' ? $displayCurrentCompany : '–' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: COMPENSATION & EXP -->
                <div class="aptk-cand-subcard aptk-subcard-comp">
                    <div class="aptk-subcard-hdr">
                        <span class="aptk-subcard-label">Compensation &amp; Exp</span>
                    </div>
                    <div class="aptk-subcard-body">
                        @if ($canViewSalary)
                            <div class="aptk-val-salary" title="Expected / Current CTC">
                                <span class="aptk-sal-main">{{ $current_ctc_cur }}{{ $billAmountFormatted }}@if ($ectcModeSuffix !== '')<span class="aptk-sal-mode">{{ $ectcModeSuffix }}</span>@endif</span>
                                @if (!$isBench)
                                    <span class="aptk-sal-sub">Cur: {{ $current_ctc_cur }}{{ $payAmountFormatted }}{{ $ctcModeSuffix }}</span>
                                @else
                                    <span class="aptk-sal-sub text-primary">{{ $outsourcing_type }}</span>
                                @endif
                            </div>
                        @else
                            <div class="aptk-val-salary">
                                <span class="aptk-sal-main text-muted">N/A</span>
                            </div>
                        @endif
                        <div class="aptk-val-exp" title="Total: {{ $formattedExperience }} Yrs{{ $rel_formattedExperience !== '' ? ' | Relevant: ' . $rel_formattedExperience . ' Yrs' : '' }}">
                            <i class="far fa-clock me-1"></i>
                            <span><strong>{{ $formattedExperience }} Y</strong> Total</span>
                            @if ($rel_formattedExperience !== '')
                                <span class="aptk-rel-bracket">(Rel <strong>{{ $rel_formattedExperience }} Y</strong>)</span>
                            @endif
                        </div>
                        <div class="aptk-val-notice" title="Notice Period: {{ $np !== '' ? e($np) : '–' }}">
                            <i class="fas fa-hourglass-half me-1"></i>
                            @if ($np !== '')
                                <span class="aptk-notice-txt {{ $npClass }}">{{ $np }}</span>
                            @else
                                <span class="aptk-notice-txt text-muted">–</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 3: LOCATION & DEGREE -->
                <div class="aptk-cand-subcard aptk-subcard-loc">
                    <div class="aptk-subcard-hdr">
                        <span class="aptk-subcard-label">Location &amp; Degree</span>
                    </div>
                    <div class="aptk-subcard-body">
                        <div class="aptk-loc-city" title="{{ $locFull !== '' ? e($locFull) : 'Location: –' }}">
                            <i class="fas fa-map-marker-alt me-1"></i><span>{{ $locShort !== '' ? $locShort : '–' }}</span>
                        </div>
                        @php
                            $wmIcon = $cardMeta['work_mode_icon'] !== '' ? $cardMeta['work_mode_icon'] : 'fas fa-building';
                            $wmLabel = $cardMeta['work_mode_label'] !== '' ? $cardMeta['work_mode_label'] : '–';
                        @endphp
                        <div class="aptk-loc-mode" title="{{ $cardMeta['work_mode_label'] !== '' ? e($cardMeta['work_mode_label']) : 'Work Mode: –' }}">
                            <i class="{{ $wmIcon }} me-1"></i><span>{{ $wmLabel }}</span>
                        </div>
                        <div class="aptk-loc-edu" title="{{ $displayQualification !== '' ? e(strtoupper($displayQualification)) : 'Education / Degree: –' }}">
                            <i class="fas fa-graduation-cap me-1"></i><span class="{{ $displayQualification !== '' ? 'aptk-edu-uppercase' : '' }}">{{ $displayQualification !== '' ? $displayQualification : '–' }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: INTERVIEW & FEEDBACK -->
                <div class="aptk-cand-subcard aptk-subcard-interview">
                    <div class="aptk-subcard-hdr">
                        <span class="aptk-subcard-label">Interview</span>
                    </div>
                    <div class="aptk-subcard-body" id="interview_date">
                        @if ($hasStageInterview && !empty($schedule_date))
                            <div class="aptk-int-date">
                                <i class="far fa-calendar-alt me-1 text-primary"></i>
                                <span>{{ $schedule_date }}</span>
                            </div>
                            <div class="aptk-int-time">
                                <i class="far fa-clock me-1"></i>
                                <span>{{ $schedule_time ?: 'Time TBD' }}</span>
                            </div>
                            @if ($row3Text !== '')
                                @if ($feedbackGiven)
                                    <span class="badge {{ $row3Class }} aptk-int-fb-badge">
                                        <i class="fas fa-check me-1"></i>{{ $row3Text }}
                                    </span>
                                @else
                                    <span class="aptk-int-mode-pill">
                                        <i class="fas fa-video me-1"></i>{{ $row3Text }}
                                    </span>
                                @endif
                            @endif
                        @else
                            @if (!empty($schedule_date))
                                <div class="aptk-int-date" title="Previous stage interview: {{ e($printInterviewStr) }}">
                                    <i class="far fa-calendar-alt me-1 text-muted"></i>
                                    <span>{{ $schedule_date }}</span>
                                </div>
                            @else
                                <div class="aptk-int-empty">
                                    <i class="far fa-calendar-times me-1"></i>Not Scheduled
                                </div>
                            @endif
                            @if ($canScheduleInterview)
                                <button type="button"
                                        class="aptk-int-schedule-btn app_interview_schedule"
                                        rel="{{ $wiseId ?: $candAppId }}"
                                        data-app-id="{{ $candAppId }}"
                                        data-wise-id="{{ $wiseId }}"
                                        data-job-id="{{ $rowJobId }}"
                                        title="Schedule interview for current stage">
                                    <i class="far fa-calendar-plus"></i>
                                    <span>Schedule Interview</span>
                                </button>
                            @elseif (empty($schedule_date))
                                <div class="aptk-int-empty-sub">Pending interview</div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- SUB-CARD 5: OWNERSHIP & RECRUITER -->
                <div class="aptk-cand-subcard aptk-subcard-owner">
                    <div class="aptk-subcard-hdr">
                        <span class="aptk-subcard-label"><i class="fas fa-user-tie me-1"></i>Ownership &amp; Recruiter</span>
                    </div>
                    <div class="aptk-subcard-body">
                        <div class="aptk-owner-name" title="{{ $rowpostedby !== '' ? e($rowpostedby) : 'Recruiter: –' }}">
                            <i class="fas fa-user-circle text-primary me-1"></i><span>{{ $rowpostedby !== '' ? $rowpostedby : '–' }}</span>
                        </div>
                        <div class="aptk-owner-company" title="{{ $post_company_name !== '' ? e($post_company_name) : 'Company: –' }}">
                            <i class="fas fa-building me-1"></i><span>{{ $post_company_name !== '' ? $post_company_name : '–' }}</span>
                        </div>
                        <div class="aptk-owner-submitted" title="{{ $cardMeta['submitted_ago'] !== '' ? e('Submitted: ' . $cardMeta['submitted_ago']) : 'Submitted: –' }}">
                            <i class="far fa-calendar-alt me-1"></i><span>Submitted: {{ $cardMeta['submitted_ago'] !== '' ? $cardMeta['submitted_ago'] : '–' }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 6: PIPELINE & STAGE (Entire Card Clickable) -->
                <div class="aptk-cand-subcard aptk-subcard-stage stage_sub_stage"
                     data-row-id="{{ $wiseId }}"
                     data-toggle="modal"
                     data-target="#stage_sub_stage"
                     role="button"
                     tabindex="0"
                     title="Click anywhere on this stage card to update candidate stage">
                    <div class="aptk-subcard-hdr">
                        <span class="aptk-subcard-label">Pipeline &amp; Stage</span>
                    </div>
                    <div class="aptk-subcard-body">
                        <div class="aptk-stage-trigger">
                            <span class="aptk-stage-pill {{ @$row->css_style }}" style="{{ $stagePillTheme }}">
                                {{ ucwords(mb_substr($masterStageLabel, 0, 20)) }}
                            </span>
                            @if ($subStageName !== '' && $subStageName !== '—')
                                <span class="aptk-substage-txt" title="{{ e($subStageName) }}">
                                    Sub: <strong>{{ $subStageName }}</strong>
                                </span>
                            @endif
                            <span class="aptk-stage-cue">Update Stage <i class="fas fa-chevron-right ms-1"></i></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SUB-CARD 7: FULL-WIDTH INTELLIGENCE & SKILLS STRIP -->
            <div class="aptk-cand-bottom">
                <div class="aptk-bottom-left">
                    <span class="jw-row-match-badge aptk-match-pill"
                          data-candidate-id="{{ $candAppId }}"
                          data-wise-id="{{ $wiseId }}"
                          data-match-pct="{{ $matchScore }}"
                          style="background:{{ $matchBg }};color:{{ $matchColor }};">
                        Match {{ $percentage ?: '0%' }}
                    </span>
                    <div class="aptk-skills-scroll">
                        @if (count($matchedSkillsArr) > 0 || count($unmatchedSkillsArr) > 0)
                            @foreach ($matchedSkillsArr as $sval)
                                <span class="aptk-skill-pill is-matched" title="Matches a required skill">{{ ucfirst($sval) }}</span>
                            @endforeach
                            @foreach ($unmatchedSkillsArr as $sval)
                                <span class="aptk-skill-pill">{{ ucfirst($sval) }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="aptk-bottom-right">
                    @php
                        $hasResume = !empty($row->resume) || !empty($rowMaps['resume_map'][$candAppId] ?? null);
                    @endphp
                    @if ($hasResume)
                        <span class="aptk-resume-pill jw-open-resume-tab cursor-pointer"
                              data-row-id="{{ $candAppId }}"
                              data-wise-id="{{ $wiseId }}"
                              data-open-tab="resume_cv"
                              title="Click to view Resume / CV">
                            <i class="fas fa-check-circle"></i><span>Resume</span>
                        </span>
                    @else
                        <span class="aptk-nocv-pill" title="Resume missing">
                            <i class="fas fa-exclamation-circle"></i><span>No CV</span>
                        </span>
                    @endif
                    @if ($cardMeta['visa_display'] !== '')
                        <span class="aptk-visa-pill" title="{{ e('Visa Status: ' . $cardMeta['visa_raw']) }}">
                            <i class="fas fa-passport"></i><span>{{ $cardMeta['visa_display'] }}</span>
                        </span>
                    @endif
                    @if ($cardMeta['linkedin_url'] !== '')
                        <a href="{{ e($cardMeta['linkedin_url']) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="aptk-linkedin-pill"
                           title="LinkedIn Profile Verified">
                            <i class="fab fa-linkedin"></i><span>LinkedIn Verified</span>
                        </a>
                    @endif
                </div>
            </div>

        </div>
    @endforeach
@else
    <div class="aptk-empty-card">
        <div class="aptk-empty-icon">
            <i class="fas fa-users-slash"></i>
        </div>
        <div class="aptk-empty-title">No Leads Found</div>
        <div class="aptk-empty-sub">No leads currently match your selected pipeline stage or search criteria.</div>
    </div>
@endif
