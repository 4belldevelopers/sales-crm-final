@php
    use App\Helper\GeneralMethod;
    use App\Services\AppTrackerCardPresenter;
    use App\Services\CurrencyService;
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
    $canViewSalary = in_array('salary_view', (array) ($userPermissions ?? []));
    $canScheduleInterview = in_array('add_schedule', (array) ($userPermissions ?? []));

    $stageRailColors = [
        8 => '#f59e0b', // InReview
        1 => '#0891b2', // Sourcing
        2 => '#a855f7', // Screening
        3 => '#009ef7', // Evaluation
        4 => '#ec4899', // Negotiation
        5 => '#f97316', // Offered
        6 => '#10b981', // Joined
        7 => '#ef4444', // Rejected
        9 => '#ef4444', // Rejected
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
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $row->thirdparty_company_id = $row->ClientComID ?? ($row->thirdparty_company_id ?? 0);
            $candAppId = (int) ($row->candidate_id ?? ($row->id ?? 0));
            $wiseId = (int) ($row->wiseId ?? ($row->add_job_wise_condidates_id ?? $candAppId));
            $rowJobId = (int) ($row->jobId ?? ($row->job_id ?? 0));
            $hasResume = !empty($row->resume);

            // Job Salary Mode & Currency
            if (isset($row->job_max_sal_mode) && isset($row->job_max_salary_currency)) {
                $jobMode = $row->job_max_sal_mode;
                $jobCurrency = $row->job_max_salary_currency;
            } elseif (isset($jobDetailsMap[$rowJobId])) {
                $jobMode = $jobDetailsMap[$rowJobId]->max_sal_mode ?? @$row->current_ctc_mode;
                $jobCurrency = $jobDetailsMap[$rowJobId]->max_salary_currency ?? @$row->current_ctc_cur;
            } else {
                $jobMode = @$row->current_ctc_mode ?? 0;
                $jobCurrency = @$row->current_ctc_cur ?? 0;
            }

            if (!empty($modeFilter)) {
                $jobMode = $modeFilter;
            }
            if (!empty($currencyFilter)) {
                $jobCurrency = $currencyFilter;
            }
            $symbols = $symbolsValue[$jobCurrency] ?? "INR";
            if (!empty($symbolsFilter)) {
                $symbols = $symbolsFilter;
            }

            // Skills & Skill Match %
            $rawSkillsStr = (string) (@$row->skills ?? '');
            $skillArr = array_values(array_filter(array_map('trim', explode(',', $rawSkillsStr))));
            $candidateSkillsLower = array_map(fn($s) => strtolower(trim($s)), $skillArr);
            $mandatoryBasis = $mandatorySkillsForDisplay ?? [];
            $optionalBasis = $optionalSkillsForDisplay ?? [];
            $hasFilterSkills = !empty($mandatoryBasis) || !empty($optionalBasis);
            if (!$hasFilterSkills) {
                $optionalBasis = ($jobSkillsByJobId[$rowJobId] ?? []);
            }
            $percentBasis = array_merge($mandatoryBasis, $optionalBasis);
            $percentage = '0%';
            if (!empty($rowJobId) && !empty($jobSkillsByJobId[$rowJobId])) {
                $percentage = GeneralMethod::skill_percentage_from_array($jobSkillsByJobId[$rowJobId], $skillArr);
            } elseif (!empty($percentBasis)) {
                $matches = 0;
                foreach ($percentBasis as $req) {
                    foreach ($candidateSkillsLower as $cand) {
                        if (GeneralMethod::skillMatchesPhrasePrefix($req, $cand)) { $matches++; break; }
                    }
                }
                $percentage = ((int) round(min(($matches / count($percentBasis)) * 100, 100))) . '%';
            }
            $matchScore = (int) round((float) rtrim(@$percentage ?: '0', '%'));
            $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
            $matchBg = $matchStyle['bg'] ?? '#eff6ff';
            $matchColor = $matchStyle['color'] ?? '#2563eb';

            $matchedSkillsArr = GeneralMethod::matchedSkillsList($skillArr, $percentBasis);
            $unmatchedSkillsArr = array_values(array_udiff($skillArr, $matchedSkillsArr, 'strcasecmp'));

            // Candidate Name & Avatar Decorator
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
            $displayDesignation = $cardMeta['designation'] !== '' ? $cardMeta['designation'] : (trim((string)(@$row->current_designation ?? '')) ?: '–');
            $displayCurrentCompany = $cardMeta['current_company'] !== '' ? $cardMeta['current_company'] : (trim((string)(@$row->current_company ?? '')) ?: '–');
            if ($isBench && !empty($row->apl_company_name)) {
                $displayCurrentCompany = $row->apl_company_name;
            }

            // Experience Formatting
            $fmtExp = function ($val) {
                $s = trim((string) $val);
                if ($s === '' || $s === '.' || $s === '0.') return '0';
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

            // Compensation Formatting
            $cur = $jobCurrency ?? 0;
            $mod = $jobMode ?? 0;
            $currencyCode = $currency[$cur] ?? CurrencyService::resolveCurrencyCode($cur);
            if (!empty($row->ectc_yearly)) {
                GeneralMethod::conversionRates($currencyCode, $row->ectc_yearly, 0);
                GeneralMethod::conversionRates($currencyCode, $row->ctc_yearly ?? 0, 0);
            }
            $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly ?? 0, $symbols, $mod);
            $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly ?? 0, $symbols, $mod);
            $current_ctc_cur = CurrencyService::resolvePlainSymbolFromLegacy(
                $cur,
                $currencySymbole,
                $billFormatViewSalary->symbol ?? null
            );
            $payAmountFormatted = explode('.', GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount))[0];
            $billAmountFormatted = explode('.', GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount))[0];
            $current_ctc_mode = $salaryMode[$mod] ?? '';
            $current_ectc_mode = $salaryMode[$mod] ?? '';
            $ectcModeSuffix = $current_ectc_mode !== '' ? '/' . $current_ectc_mode : '';
            $ctcModeSuffix = $current_ctc_mode !== '' ? '/' . $current_ctc_mode : '';
            $outsourcing_type = (@$row->outsourcing_type == 1) ? 'Fulltime' : 'Contract';

            $printSalaryStr = $canViewSalary
                ? ($current_ctc_cur . $billAmountFormatted . $ectcModeSuffix . ($isBench ? ' (' . $outsourcing_type . ')' : ' | Cur: ' . $current_ctc_cur . $payAmountFormatted . $ctcModeSuffix))
                : 'N/A';

            // Notice Period
            $np = $cardMeta['notice_display'];
            $npClass = 'aptk-notice-' . $cardMeta['notice_class'];

            // Location
            $row_city_name = AppTrackerCardPresenter::cleanText(@$row->city_name ?? '');
            $row_country_name = AppTrackerCardPresenter::cleanText(@$row->country_name ?? '');
            if ($row_city_name === '' && !empty($row->city_id) && isset($cityDataMap[$row->city_id])) {
                $row_city_name = $cityDataMap[$row->city_id];
            }
            $locShort = ($row_city_name !== '' || $row_country_name !== '')
                ? trim(($row_city_name !== '' ? ucwords(mb_substr($row_city_name, 0, 18)) : '') . ($row_country_name !== '' ? ', ' . strtoupper(mb_substr($row_country_name, 0, 2)) : ''), ', ')
                : '';
            $locFull = trim(($row_city_name !== '' ? ucwords($row_city_name) : '') . ($row_country_name !== '' ? ', ' . strtoupper($row_country_name) : ''), ', ');
            $displayQualification = $cardMeta['qualification'] !== '' ? $cardMeta['qualification'] : (trim((string)(@$row->qualification_name ?? '')) ?: '–');

            // Organization & Client Classification (Rule 25 canonical tokens)
            $isDirect = false;
            if (!empty($row->is_direct_client) || !empty($row->recommendation_id) || (!empty($row->ClientComID) && (int)$row->ClientComID !== (int)$company_id)) {
                $isDirect = true;
            }
            $isOwnOrg = false;
            if (!$isDirect && (!empty($row->client_self_company) || (isset($row->ClientComID) && (int)$row->ClientComID === 0) || !empty($row->self_company))) {
                $isOwnOrg = true;
            }

            if ($isDirect) {
                $orgTypeLabel = 'Direct Client';
                $orgTypeCls   = 'type-direct';
                $orgTypeIcon  = 'fa-shield-alt';
            } elseif ($isOwnOrg) {
                $orgTypeLabel = 'My Organization';
                $orgTypeCls   = 'type-my-org';
                $orgTypeIcon  = 'fa-id-badge';
            } else {
                $orgTypeLabel = 'Internal Client';
                $orgTypeCls   = 'type-internal';
                $orgTypeIcon  = 'fa-building';
            }

            // Recruiter / Ownership
            if (!empty($row->spokperson_name)) {
                $rowpostedby = $row->spokperson_name;
            } elseif ($company_id != ($row->ClientComID ?? 0) && !empty($row->ClientComID) && $row->ClientComID > 0) {
                $spokKey = $rowJobId . '_' . ($row->ClientComID ?? 0);
                $rowpostedby = $spokUserMap[$spokKey] ?? ($row->posted_by ?? '–');
            } else {
                $rowpostedby = $row->posted_by ?? '–';
            }
            $post_company_name = @$row->submitted_by_company_name ?: (@$row->post_company_name ?: (@$row->company_name ?: ''));

            // Job Title & Client Name
            $jobTitleDisplay = !empty($row->title) ? $row->title : 'General Pool Application';
            $client_title_name = $row->client_name ?? '';
            if (empty($client_title_name) && !empty($rowJobId)) {
                $client_title_name = GeneralMethod::companyDetailsByClient($rowJobId) ?: '';
            }

            // Stage, Substage & Accents
            $masterStatusId = (int) ($row->job_master_status_id ?? 8);
            $stageAccentColor = $stageRailColors[$masterStatusId] ?? '#009ef7';
            $stagePillTheme = $stageBadgeThemes[$masterStatusId] ?? 'background:#eff6ff;color:#009ef7;border-color:#bfdbfe;';
            $masterStageLabel = trim((string) (@$row->name ?? @$row->master_status_name ?? 'InReview'));

            $subStageName = '';
            if (!empty($row->child_status_name)) {
                $subStageName = $row->child_status_name;
            } elseif (!empty($row->childStatus) && isset($childStatusMap[$row->childStatus])) {
                $subStageName = $childStatusMap[$row->childStatus];
            }
            $subStageName = trim((string) $subStageName) !== '' ? $subStageName : '—';
            $printStageStr = ucwords($masterStageLabel) . ($subStageName !== '—' ? ' / ' . $subStageName : '');

            // Stage Modal Trigger Rules
            $stage_default_css = 'stage_sub_stage';
            if (!empty($row->thirdparty_company_id) && $row->thirdparty_company_id == $company_id) {
                $stage_default_css = 'client_stage_sub_stage';
            }
            if (!empty($row->thirdparty_company_id) && $row->thirdparty_company_id == $company_id && $masterStatusId != 8) {
                $stage_default_css = 'client_stage_locked_notice';
            }
            $stageModalAttrs = ($stage_default_css === 'client_stage_locked_notice')
                ? ''
                : ' data-toggle="modal" data-target="#' . $stage_default_css . '"';
        @endphp

        <!-- Master Primary Candidate Card (Rule 20 Modern Card + Sub-Card Enterprise Standard) -->
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
             data-print-location="{{ e($locFull) }}"
             data-print-edu="{{ e(strtoupper($displayQualification)) }}"
             data-print-owner="{{ e($rowpostedby) }}"
             data-print-job="{{ e($jobTitleDisplay) }}"
             data-print-stage="{{ e($printStageStr) }}"
             style="border-left: 3.5px solid {{ $stageAccentColor }} !important;">

            <!-- Upper Discrete 6 Sub-Cards Grid -->
            <div class="aptk-cand-grid">

                <!-- SUB-CARD 1: CANDIDATE INFO & IDENTITY -->
                <div class="aptk-cand-subcard aptk-subcard-identity">
                    <div class="aptk-subcard-hdr">
                        <label class="aptk-chk-wrap" title="Select candidate">
                            <input class="form-check-input chk_list_item"
                                   type="checkbox"
                                   name="chk_list_item[]"
                                   value="{{ $candAppId }}"
                                   data-wiseId="{{ $wiseId }}"
                                   data-Wiid="{{ $wiseId }}"
                                   data-candidate-id="{{ $candAppId }}"
                                   data-jobId="{{ $rowJobId }}"
                                   data-match="{{ $matchScore }}"
                                   data-has-resume="{{ $hasResume ? '1' : '0' }}"
                                   data-skills="{{ e($rawSkillsStr) }}"
                                   data-cname="{{ e($displayFullName) }}" />
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

                <!-- SUB-CARD 4: JOB & ORGANIZATION CONTEXT -->
                <div class="aptk-cand-subcard aptk-subcard-job">
                    <div class="aptk-subcard-hdr">
                        <span class="aptk-subcard-label">Job &amp; Mandate</span>
                        <span class="cv-org-badge {{ $orgTypeCls }}" title="{{ $orgTypeLabel }}">
                            <i class="fas {{ $orgTypeIcon }} me-1"></i>{{ $orgTypeLabel }}
                        </span>
                    </div>
                    <div class="aptk-subcard-body">
                        <div class="aptk-job-title" title="{{ e($jobTitleDisplay) }}">
                            <a href="javascript:void(0);" class="text-dark fw-bold text-hover-primary">
                                {{ mb_substr($jobTitleDisplay, 0, 24) }}{{ mb_strlen($jobTitleDisplay) > 24 ? '...' : '' }}
                            </a>
                        </div>
                        <div class="aptk-job-client" title="{{ $client_title_name !== '' ? e($client_title_name) : 'Company: –' }}">
                            <i class="fas fa-building me-1"></i><span>{{ $client_title_name !== '' ? mb_substr($client_title_name, 0, 22) : '–' }}</span>
                        </div>
                        <div class="aptk-job-recruiter" title="{{ $rowpostedby !== '' ? e('Recruiter: ' . $rowpostedby) : 'Recruiter: –' }}">
                            <i class="fas fa-user-circle me-1"></i><span>{{ $rowpostedby !== '' ? mb_substr($rowpostedby, 0, 18) : '–' }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 5: PIPELINE & STAGE (Interactive Trigger) -->
                <div class="aptk-cand-subcard aptk-subcard-stage {{ $stage_default_css }}"
                     data-row-id="{{ $wiseId }}"
                     {!! $stageModalAttrs !!}
                     role="button"
                     tabindex="0"
                     title="Click anywhere to update candidate stage">
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

                <!-- SUB-CARD 6: QUICK ACTIONS & RESUME -->
                <div class="aptk-cand-subcard aptk-subcard-actions">
                    <div class="aptk-subcard-hdr">
                        <span class="aptk-subcard-label">Actions</span>
                        @if ($hasResume)
                            <span class="badge badge-light-success fw-bold fs-9 py-1 px-2" title="Resume uploaded"><i class="fas fa-check-circle me-1 text-success"></i>Resume</span>
                        @else
                            <span class="badge badge-light-danger fw-bold fs-9 py-1 px-2" title="Resume missing"><i class="fas fa-exclamation-circle me-1 text-danger"></i>No CV</span>
                        @endif
                    </div>
                    <div class="aptk-subcard-body">
                        <div class="cv-quick-actions-grid">
                            <a href="javascript:void(0);"
                               data-row-id="{{ $wiseId }}"
                               data-wise-id="{{ $candAppId }}"
                               class="btn btn-icon btn-sm btn-light-primary show-application-detail"
                               title="View Full Profile">
                                <i class="fas fa-eye fs-7"></i>
                            </a>
                            <a href="javascript:void(0);"
                               class="btn btn-icon btn-sm btn-light-success test_fwd_resume"
                               data-row-id="{{ $wiseId }}"
                               data-toggle="modal"
                               data-target="#test_fwd_resume"
                               title="Forward Resume">
                                <i class="fas fa-share-alt fs-7"></i>
                            </a>
                            <a href="javascript:void(0);"
                               class="btn btn-icon btn-sm btn-light-info send_mail"
                               data-row-id="{{ $wiseId }}"
                               data-toggle="modal"
                               data-target="#send_mail"
                               title="Send Email">
                                <i class="fas fa-envelope fs-7"></i>
                            </a>
                            <a href="javascript:void(0);"
                               class="btn btn-icon btn-sm btn-light-warning application_copy"
                               data-row-id="{{ $wiseId }}"
                               data-toggle="modal"
                               data-target="#application_copy"
                               title="Copy Candidate">
                                <i class="fas fa-copy fs-7"></i>
                            </a>
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
                        @else
                            <span class="aptk-skills-empty">No skills recorded</span>
                        @endif
                    </div>
                </div>
                @if ($cardMeta['linkedin_url'] !== '' || $cardMeta['visa_display'] !== '' || !empty($row->created_at))
                    <div class="aptk-bottom-right">
                        @if ($cardMeta['linkedin_url'] !== '')
                            <a href="{{ e($cardMeta['linkedin_url']) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="aptk-linkedin-pill"
                               title="LinkedIn Profile Verified">
                                <i class="fab fa-linkedin"></i><span>LinkedIn Verified</span>
                            </a>
                        @endif
                        @if ($cardMeta['visa_display'] !== '')
                            <span class="aptk-visa-pill" title="{{ e('Visa Status: ' . $cardMeta['visa_raw']) }}">
                                <i class="fas fa-passport"></i><span>{{ $cardMeta['visa_display'] }}</span>
                            </span>
                        @endif
                        @if (!empty($row->created_at))
                            <span class="text-muted fs-8 ms-2" title="Application Added Date">
                                <i class="far fa-calendar-alt me-1"></i>{{ date('d M Y', strtotime($row->created_at)) }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    @endforeach
@else
    <div class="aptk-empty-card">
        <div class="aptk-empty-icon">
            <i class="fas fa-users-slash"></i>
        </div>
        <div class="aptk-empty-title">No Candidates Found</div>
        <div class="aptk-empty-sub">No candidates match your selected pipeline stage, company type, or search filters.</div>
    </div>
@endif
