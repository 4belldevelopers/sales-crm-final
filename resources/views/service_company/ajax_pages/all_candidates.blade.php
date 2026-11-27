@php
    if (!class_exists(\App\Services\AppTrackerCardPresenter::class, false)) {
        $presenterFile = app_path('Services/AppTrackerCardPresenter.php');
        if (file_exists($presenterFile)) {
            require_once $presenterFile;
        }
    }
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
    $empTypes = GeneralMethod::employmentType();
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
        8 => '#eab308', // InReview (standardized yellow)
        1 => '#0891b2', // Sourcing
        2 => '#a855f7', // Screening
        3 => '#009ef7', // Evaluation
        4 => '#ec4899', // Negotiation
        5 => '#f97316', // Offered (standardized orange)
        6 => '#10b981', // Joined
        7 => '#ef4444', // Rejected
        9 => '#ef4444', // Rejected
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
            $npClass = 'simf-notice-' . $cardMeta['notice_class'];

            // Location
            $row_city_name = AppTrackerCardPresenter::cleanText(@$row->city_name ?? '');
            $row_country_name = AppTrackerCardPresenter::cleanText(@$row->country_name ?? '');
            $locHier = \App\Helper\GeneralMethod::resolveCandidateLocationHierarchy($row);
            $row_city_name = $locHier['city'] !== '' ? $locHier['city'] : AppTrackerCardPresenter::cleanText(@$row->city_name ?? '');
            $row_state_name = $locHier['state'];
            $row_country_name = $locHier['country'] !== '' ? $locHier['country'] : AppTrackerCardPresenter::cleanText(@$row->country_name ?? '');
            $locShort = ($row_city_name !== '' || $row_state_name !== '')
                ? trim(($row_city_name !== '' ? ucwords(mb_substr($row_city_name, 0, 18)) : '') . ($row_state_name !== '' ? ', ' . ucwords(mb_substr($row_state_name, 0, 18)) : ($row_country_name !== '' ? ', ' . strtoupper(mb_substr($row_country_name, 0, 2)) : '')), ', ')
                : '';
            $locFull = $locHier['location_text'] !== '--' ? $locHier['location_text'] : trim(($row_city_name !== '' ? ucwords($row_city_name) : '') . ($row_country_name !== '' ? ', ' . strtoupper($row_country_name) : ''), ', ');
            $displayQualification = $cardMeta['qualification'] !== '' ? $cardMeta['qualification'] : (trim((string)(@$row->qualification_name ?? '')) ?: '–');

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

            // Job Title, Category, Client & Employment Type
            $jobObj = $jobDetailsMap[$rowJobId] ?? null;
            $jobTitleDisplay = !empty($jobObj->title) ? $jobObj->title : (!empty($row->title) ? $row->title : 'General Pool Application');
            $jobCategoryName = !empty($jobObj->category_name) ? $jobObj->category_name : (!empty($row->category_name) ? $row->category_name : '');
            $empTypeRaw = $jobObj->employment_type ?? (@$row->employment_type ?? 0);
            $empTypeLabel = $empTypes[$empTypeRaw] ?? ($empTypeRaw == 1 ? 'Contract' : 'Full-Time');

            $client_title_name = !empty($jobObj->client_name) ? $jobObj->client_name : ($row->client_name ?? '');
            if (empty($client_title_name) && !empty($rowJobId)) {
                $client_title_name = GeneralMethod::companyDetailsByClient($rowJobId) ?: '';
            }

            // Dynamic Organization-Type Classification (Rule 25 canonical tokens)
            // 1. Direct Client: Requirement received through marketplace
            $isDirect = false;
            if (!empty($row->is_direct_client) || !empty($row->is_job_request) || !empty($row->recommendation_id) || (!empty($row->ClientComID) && (int)$row->ClientComID !== (int)$company_id)) {
                $isDirect = true;
            }

            // 2. Client relationship from actual job & client entity
            $resolvedClientId = $jobObj->client_id ?? ($row->client_id ?? null);
            $resolvedSelfCompany = isset($jobObj->client_self_company)
                ? (int) $jobObj->client_self_company
                : (isset($row->client_self_company) ? (int) $row->client_self_company : null);

            $tenantCompanyName = trim((string) (auth()->user()->company->company_name ?? ''));

            if ($isDirect) {
                // Requirement received through the marketplace
                $targetTypeLabel = 'Direct Client';
                $targetTypeClass = 'type-direct';
                $targetTypeIcon  = 'fa-solid fa-shield-alt';
                $targetTypeStyle = 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;';
            } elseif ($resolvedSelfCompany === 0 || (!empty($resolvedClientId) && $resolvedSelfCompany !== 1 && !empty($client_title_name) && ($tenantCompanyName === '' || strcasecmp(trim($client_title_name), $tenantCompanyName) !== 0))) {
                // Hiring for an internal client (e.g. Tesla, Stripe)
                $targetTypeLabel = 'Internal Client';
                $targetTypeClass = 'type-internal';
                $targetTypeIcon  = 'fa-solid fa-building';
                $targetTypeStyle = 'background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;';
            } else {
                // Job created for own organization
                $targetTypeLabel = 'My Organization';
                $targetTypeClass = 'type-my-org';
                $targetTypeIcon  = 'fa-solid fa-id-badge';
                $targetTypeStyle = 'background:#fef3c7; color:#92400e; border:1px solid #fcd34d;';
            }

            // Stage, Substage & Accents
            $masterStatusId = (int) ($row->job_master_status_id ?? 8);
            $stageAccentColor = $stageRailColors[$masterStatusId] ?? '#009ef7';
            $stagePillTheme = $stageBadgeThemes[$masterStatusId] ?? 'background:#eff6ff;color:#009ef7;border-color:#bfdbfe;';
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
        <article class="simf-cand-card aptk-cand-card {{ $flag === 1 ? 'is-duplicate' : '' }}"
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
                 data-print-city="{{ e($row_city_name) }}"
                 data-print-state="{{ e($row_state_name) }}"
                 data-print-country="{{ e($row_country_name) }}"
                 data-print-edu="{{ e(strtoupper($displayQualification)) }}"
                 data-print-owner="{{ e($rowpostedby) }}"
                 data-print-job="{{ e($jobTitleDisplay) }}"
                 data-print-stage="{{ e($printStageStr) }}"
                 style="border-left: 3.5px solid {{ $stageAccentColor }} !important;">

            <!-- Upper Discrete 6 Sub-Cards Grid -->
            <div class="simf-cand-grid aptk-cand-grid">

                <!-- SUB-CARD 1: CANDIDATE INFO & IDENTITY -->
                <div class="simf-cand-subcard aptk-cand-subcard simf-cand-subcard--info aptk-subcard-identity">
                    <div class="simf-subcard-head aptk-subcard-hdr">
                        <label class="aptk-chk-wrap simf-chk-wrap" title="Select candidate" style="display:inline-flex; align-items:center; gap:6px; margin:0; cursor:pointer; min-width:0;">
                            <input class="form-check-input chk_list_item cursor-pointer"
                                   name="chk_list_item[]"
                                   type="checkbox"
                                   value="{{ $candAppId }}"
                                   data-wiseId="{{ $wiseId }}"
                                   data-Wiid="{{ $wiseId }}"
                                   data-candidate-id="{{ $candAppId }}"
                                   data-jobId="{{ $rowJobId }}"
                                   data-match="{{ $matchScore }}"
                                   data-has-resume="{{ $hasResume ? '1' : '0' }}"
                                   data-skills="{{ e($rawSkillsStr) }}"
                                   data-cname="{{ e($displayFullName) }}"
                                   style="width:15px; height:15px; margin:0; flex-shrink:0;" />
                            <span class="simf-subcard-label aptk-subcard-label">Lead Info</span>
                        </label>
                        <div class="d-inline-flex align-items-center gap-1">
                            @if ($isBench)
                                <span class="aptk-pill-bench" style="font-size:8.5px; font-weight:700; padding:1.5px 5px; border-radius:4px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; text-transform:uppercase;">Bench</span>
                            @endif
                            @if ($flag === 1)
                                <span class="simf-dup-pill aptk-pill-dup" title="Duplicate candidate detected" style="font-size:8.5px; font-weight:700; padding:1.5px 6px; border-radius:4px; background:#fef2f2; color:#dc2626; border:1px solid #fecaca; text-transform:uppercase; display:inline-flex; align-items:center; gap:3px;">
                                    <i class="fa-solid fa-clone me-1"></i>Duplicate
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="simf-cand-info-row aptk-identity-row" style="display:flex !important; align-items:flex-start !important; gap:9px !important; min-width:0 !important; padding-top:2px !important;">
                        <a href="javascript:void(0);"
                           data-row-id="{{ $wiseId }}"
                           data-wise-id="{{ $candAppId }}"
                           class="show-application-detail simf-avatar aptk-avatar-box {{ $flag === 1 ? 'is-dup btn-light-danger pulse pulse-danger' : $bgTextDecore }}"
                           title="{{ $flag === 1 ? 'Duplicate — View Profile' : 'View Candidate Profile' }}"
                           style="width:36px !important; height:36px !important; min-width:36px !important; border-radius:8px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; font-weight:700 !important; font-size:13.5px !important; text-decoration:none !important; overflow:hidden !important; flex-shrink:0 !important;">
                            @if (!empty($photo))
                                <img src="{{ $photo }}" alt="{{ e($displayFullName) }}" style="width:100% !important; height:100% !important; border-radius:8px !important; object-fit:cover !important;" />
                            @else
                                <span>{{ strtoupper($firstChar) }}</span>
                            @endif
                            @if ($flag === 1)
                                <span class="pulse-ring"></span>
                            @endif
                        </a>
                        <div class="simf-cand-info-meta aptk-identity-meta" style="flex:1 1 auto !important; min-width:0 !important; overflow:hidden !important;">
                            <a href="javascript:void(0);"
                               data-row-id="{{ $wiseId }}"
                               data-wise-id="{{ $candAppId }}"
                               class="show-application-detail simf-cand-name aptk-cand-name {{ $flag === 1 ? 'text-danger' : '' }}"
                               title="{{ e($displayFullName) }}"
                               style="font-size:12.5px !important; font-weight:700 !important; color:#0f172a !important; text-decoration:none !important; display:block !important; white-space:nowrap !important; overflow:hidden !important; text-overflow:ellipsis !important; line-height:1.25 !important;">
                                {{ $displayFullName }}
                            </a>
                            <div class="simf-cand-role aptk-cand-desig {{ $flag === 1 ? 'text-danger' : '' }}"
                                 title="{{ $displayDesignation !== '' ? e($displayDesignation) : 'Designation: –' }}"
                                 style="font-size:11px !important; font-weight:500 !important; color:#475569 !important; white-space:nowrap !important; overflow:hidden !important; text-overflow:ellipsis !important; line-height:1.3 !important; margin-top:2px !important;">
                                {{ $displayDesignation !== '' ? $displayDesignation : '—' }}
                            </div>
                            <div class="simf-cand-company aptk-cand-company"
                                 title="{{ $displayCurrentCompany !== '' ? e($displayCurrentCompany) : 'Current Company: –' }}"
                                 style="font-size:11px !important; font-weight:500 !important; color:#64748b !important; white-space:nowrap !important; overflow:hidden !important; text-overflow:ellipsis !important; line-height:1.3 !important; margin-top:2px !important;">
                                <i class="fa-regular fa-building me-1 text-muted" style="width:12px; text-align:center;"></i><span>{{ $displayCurrentCompany !== '' ? $displayCurrentCompany : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: COMPENSATION & EXP -->
                <div class="simf-cand-subcard">
                    <div class="simf-subcard-head">
                        <span class="simf-subcard-label">Compensation &amp; Exp</span>
                    </div>
                    <div class="simf-val-salary">
                        @if ($canViewSalary)
                            <div class="simf-sal-main" title="Expected: {{ $current_ctc_cur }}{{ $billAmountFormatted }}{{ $ectcModeSuffix }}">
                                {{ $current_ctc_cur }}{{ $billAmountFormatted }}@if ($ectcModeSuffix !== '')<span class="simf-sal-mode">{{ $ectcModeSuffix }}</span>@endif
                            </div>
                            <div class="simf-sal-sub" title="Current: {{ $current_ctc_cur }}{{ $payAmountFormatted }}{{ $ctcModeSuffix }}">
                                @if (!$isBench)
                                    Cur: {{ $current_ctc_cur }}{{ $payAmountFormatted }}{{ $ctcModeSuffix }}
                                @else
                                    <span class="text-primary">{{ $outsourcing_type }}</span>
                                @endif
                            </div>
                        @else
                            <div class="simf-sal-main text-muted">N/A</div>
                            <div class="simf-sal-sub text-muted">—</div>
                        @endif
                    </div>
                    <div class="simf-val-exp" title="Total: {{ $formattedExperience }} Yrs{{ $rel_formattedExperience !== '' ? ' | Relevant: ' . $rel_formattedExperience . ' Yrs' : '' }}">
                        <i class="fa-solid fa-briefcase me-1 text-muted"></i>
                        <span><strong>{{ $formattedExperience !== '' ? $formattedExperience : '0' }} Y</strong> Total</span>
                        @if ($rel_formattedExperience !== '')
                            <span class="ms-1" style="color:#64748b; font-size:10px;">(Rel <strong>{{ $rel_formattedExperience }} Y</strong>)</span>
                        @endif
                    </div>
                    <div class="simf-val-notice" title="Notice Period: {{ $np !== '' ? e($np) : '–' }}">
                        <i class="fa-regular fa-clock me-1 text-muted"></i>
                        @if ($np !== '')
                            @php
                                $displayNp = trim((string) $np);
                                if (!str_starts_with($displayNp, 'NP:') && !str_starts_with($displayNp, 'SNP')) {
                                    $displayNp = 'NP: ' . $displayNp;
                                }
                            @endphp
                            <span class="simf-notice-txt {{ $npClass }}">{{ $displayNp }}</span>
                        @else
                            <span class="simf-notice-txt text-muted">NP: –</span>
                        @endif
                    </div>
                </div>

                <!-- SUB-CARD 3: LOCATION & DEGREE -->
                <div class="simf-cand-subcard">
                    <div class="simf-subcard-head d-flex align-items-center justify-content-between mb-1" style="min-height:18px;">
                        <span class="simf-subcard-label text-uppercase text-truncate">Location &amp; Degree</span>
                    </div>
                    <div class="pt-1">
                        <div class="simf-loc-city text-truncate d-flex align-items-center" style="font-size:11.5px; font-weight:600; line-height:1.35; min-height:18px;" title="{{ $locFull !== '' ? e($locFull) : 'Location: –' }}">
                            <i class="fa-solid fa-location-dot me-1 text-muted" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ $locShort !== '' ? $locShort : '—' }}</span>
                        </div>
                        @php
                            $wmIcon = !empty($cardMeta['work_mode_icon']) ? $cardMeta['work_mode_icon'] : 'fa-solid fa-building';
                            $wmLabel = !empty($cardMeta['work_mode_label']) ? $cardMeta['work_mode_label'] : '–';
                        @endphp
                        <div class="simf-loc-mode text-truncate d-flex align-items-center mt-1" style="font-size:11px; font-weight:500; color:#475569; line-height:1.35; min-height:18px;" title="{{ $cardMeta['work_mode_label'] !== '' ? e($cardMeta['work_mode_label']) : 'Work Mode: –' }}">
                            <i class="{{ $wmIcon }} me-1 text-primary" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ $wmLabel }}</span>
                        </div>
                        <div class="simf-loc-edu simf-edu-uppercase text-truncate d-flex align-items-center mt-1" style="font-size:11px; font-weight:500; color:#64748b; line-height:1.35; min-height:18px;" title="{{ $displayQualification !== '' ? e(strtoupper($displayQualification)) : 'Education / Degree: –' }}">
                            <i class="fa-solid fa-graduation-cap me-1 text-muted" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ $displayQualification !== '' ? strtoupper($displayQualification) : '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: JOB DETAILS (Neutral Subcard, Dynamic Badge Highlight Only) -->
                <div class="simf-cand-subcard aptk-cand-subcard simf-cand-subcard--target">
                    <div class="simf-subcard-head aptk-subcard-hdr d-flex align-items-center justify-content-between mb-1" style="min-height:18px;">
                        <span class="simf-subcard-label aptk-subcard-label text-uppercase text-truncate" title="Service Details">Service Details</span>
                        <span class="simf-target-type-badge aptk-target-type-badge {{ $targetTypeClass }}" style="font-size:9.5px; font-weight:700; padding:1.5px 6px; border-radius:4px; text-transform:uppercase; white-space:nowrap !important; flex-shrink:0; {{ $targetTypeStyle }}">
                            <i class="{{ $targetTypeIcon }} me-1"></i>{{ $targetTypeLabel }}
                        </span>
                    </div>
                    <div class="simf-req-body pt-1">
                        <div class="simf-req-title text-truncate fw-bold text-dark" style="font-size:12.5px; line-height:1.35; color:#0f172a;" title="{{ e($jobTitleDisplay) }}">
                            {{ $jobTitleDisplay ?: '—' }}
                        </div>
                        <div class="simf-req-meta-line text-truncate mt-1" style="font-size:11px; line-height:1.35;">
                            <span class="fw-bold" style="color:#0284c7;">#{{ $rowJobId }}</span>
                            @if (!empty($jobCategoryName))
                                <span class="mx-1" style="color:#94a3b8;">&bull;</span>
                                <span style="color:#64748b;">{{ $jobCategoryName }}</span>
                            @endif
                        </div>
                        <div class="simf-req-sub-line text-truncate mt-1" style="font-size:11px; line-height:1.35; color:#475569;">
                            <span class="fw-medium text-dark">{{ $client_title_name ?: '—' }}</span>
                            @if (!empty($empTypeLabel))
                                <span class="mx-1" style="color:#94a3b8;">&bull;</span>
                                <i class="fa-solid fa-briefcase text-primary me-1" style="font-size:10px;"></i>
                                <span style="color:#64748b;">{{ $empTypeLabel }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 5: OWNERSHIP & ADDED -->
                <div class="simf-cand-subcard aptk-cand-subcard">
                    <div class="simf-subcard-head aptk-subcard-hdr d-flex align-items-center justify-content-between mb-1" style="min-height:18px;">
                        <span class="simf-subcard-label aptk-subcard-label text-uppercase text-truncate">Ownership &amp; Added</span>
                    </div>
                    <div class="pt-1">
                        <div class="simf-owner-name text-truncate d-flex align-items-center" style="font-size:11.5px; font-weight:600; line-height:1.35; min-height:18px;" title="{{ e($rowpostedby) }}">
                            <i class="fa-regular fa-circle-user me-1 text-primary" style="width:13px; text-align:center; font-size:10.5px;"></i>
                            <a href="javascript:void(0);" data-row-id="{{ $candAppId }}" class="show-application-detail text-dark text-hover-primary text-truncate">
                                {{ ucfirst((string) $rowpostedby) ?: '—' }}
                            </a>
                        </div>
                        <div class="simf-owner-company text-truncate d-flex align-items-center mt-1" style="font-size:11px; font-weight:500; color:#475569; line-height:1.35; min-height:18px;" title="{{ e($post_company_name) }}">
                            <i class="fa-regular fa-building me-1 text-muted" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ $post_company_name ?: '—' }}</span>
                        </div>
                        <div class="simf-owner-submitted text-truncate d-flex align-items-center mt-1" style="font-size:11px; font-weight:500; color:#64748b; line-height:1.35; min-height:18px;" title="Uploaded on {{ !empty($row->created_at) ? date('d M , Y', strtotime($row->created_at)) : '—' }}">
                            <i class="fa-regular fa-calendar me-1 text-muted" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ !empty($row->created_at) ? date('d M , Y', strtotime($row->created_at)) : '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 6: STAGE & ACTION ("Add to Other Job" Drawer Integration) -->
                <div class="simf-cand-subcard aptk-cand-subcard">
                    <div class="simf-subcard-head aptk-subcard-hdr d-flex align-items-center justify-content-between mb-1" style="min-height:18px;">
                        <span class="simf-subcard-label aptk-subcard-label text-uppercase text-truncate" title="Stage &amp; Action">Stage &amp; Action</span>
                    </div>
                    <div class="simf-stage-action-wrap pt-1">
                        <a href="javascript:void(0);"
                           data-row-id="{{ $wiseId }}"
                           class="simf-stage-trigger aptk-stage-trigger {{ $stage_default_css }}"
                           {!! $stageModalAttrs !!}
                           role="button"
                           tabindex="0"
                           title="Click to update candidate stage"
                           style="display:flex; flex-direction:column; gap:2px; text-decoration:none; cursor:pointer;">
                            <span class="simf-stage-pill aptk-stage-pill {{ @$row->css_style }}" style="{{ $stagePillTheme }} font-size:11px; font-weight:700; padding:2px 8px; border-radius:4px; display:inline-flex; align-items:center; width:fit-content; line-height:1.3;">
                                {{ ucwords((string) $masterStageLabel) ?: '—' }}
                            </span>
                            @if (!empty($subStageName) && $subStageName !== '—')
                                <span class="simf-substage-txt aptk-substage-txt text-truncate" title="{{ e($subStageName) }}" style="font-size:10px; color:#64748b; margin-top:1px; line-height:1.2;">
                                    Sub: <strong style="color:#334155;">{{ ucwords((string) $subStageName) }}</strong>
                                </span>
                            @endif
                            <span class="simf-stage-cue aptk-stage-cue" style="font-size:10px; font-weight:600; color:#009ef7; display:inline-flex; align-items:center; margin-top:1px;">
                                Update Stage <i class="fas fa-chevron-right ms-1" style="font-size:9px;"></i>
                            </span>
                        </a>
                        <div class="d-flex flex-column mt-1">
                            <button type="button"
                                    class="btn btn-sm btn-primary cv-add-to-other-job"
                                    style="font-size:11.5px; font-weight:600; padding:4px 10px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; gap:5px; width:100%; white-space:nowrap; height:28px;"
                                    data-candidate-id="{{ $candAppId }}"
                                    data-wise-id="{{ $wiseId }}"
                                    data-job-id="{{ $rowJobId }}"
                                    data-cname="{{ e($displayFullName) }}"
                                    data-skills="{{ e($rawSkillsStr) }}"
                                    title="Copy lead to another service">
                                Add to Other Service
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SUB-CARD 7: FULL-WIDTH INTELLIGENCE & SKILLS STRIP -->
            <div class="simf-cand-bottom aptk-cand-bottom" style="display:flex !important; align-items:center !important; justify-content:space-between !important; min-height:38px !important; width:100% !important; overflow:hidden !important; gap:12px !important; padding:6px 12px !important; background:#fafcff !important; border-top:1px solid #edf2f7 !important;">
                <div class="simf-bottom-left aptk-bottom-left" style="display:flex !important; align-items:center !important; gap:8px !important; min-width:0 !important; flex:1 1 auto !important; overflow:hidden !important;">
                    <span class="simf-match-pill aptk-match-pill jw-row-match-badge"
                          data-candidate-id="{{ $candAppId }}"
                          data-wise-id="{{ $wiseId }}"
                          data-match-pct="{{ $matchScore }}"
                          style="background:{{ $matchBg }} !important; color:{{ $matchColor }} !important; font-size:10.5px !important; font-weight:700 !important; padding:2.5px 8px !important; border-radius:4px !important; line-height:1.3 !important; white-space:nowrap !important; flex-shrink:0 !important; display:inline-flex !important; align-items:center !important;">
                        Match {{ $percentage ?: '0%' }}
                    </span>
                    <div class="simf-skills-scroll aptk-skills-scroll" style="display:flex !important; align-items:center !important; gap:4px !important; overflow-x:auto !important; overflow-y:hidden !important; white-space:nowrap !important; flex-wrap:nowrap !important; min-width:0 !important; flex:1 1 auto !important; scrollbar-width:none !important; -ms-overflow-style:none !important;">
                        @if (count($matchedSkillsArr) > 0 || count($unmatchedSkillsArr) > 0)
                            @foreach ($matchedSkillsArr as $sval)
                                <span class="simf-skill-pill aptk-skill-pill is-matched" title="Matches a required skill" style="font-size:10px !important; font-weight:600 !important; padding:2px 7px !important; border-radius:4px !important; background:#ecfdf5 !important; color:#059669 !important; border:1px solid #a7f3d0 !important; line-height:1.35 !important; white-space:nowrap !important; flex-shrink:0 !important; display:inline-block !important;">{{ ucfirst($sval) }}</span>
                            @endforeach
                            @foreach ($unmatchedSkillsArr as $sval)
                                <span class="simf-skill-pill aptk-skill-pill" style="font-size:10px !important; font-weight:500 !important; padding:2px 7px !important; border-radius:4px !important; background:#f1f5f9 !important; color:#475569 !important; border:1px solid #e2e8f0 !important; line-height:1.35 !important; white-space:nowrap !important; flex-shrink:0 !important; display:inline-block !important;">{{ ucfirst($sval) }}</span>
                            @endforeach
                        @else
                            <span class="simf-skills-empty aptk-skills-empty" style="font-size:10.5px !important; color:#94a3b8 !important; font-style:italic !important; white-space:nowrap !important;">No skills listed</span>
                        @endif
                    </div>
                </div>
                <div class="simf-bottom-right aptk-bottom-right" style="display:flex !important; align-items:center !important; gap:6px !important; flex-shrink:0 !important; white-space:nowrap !important;">
                    @if ($hasResume)
                        <span class="simf-resume-pill aptk-resume-pill jw-open-resume-tab cursor-pointer"
                              data-row-id="{{ $candAppId }}"
                              data-wise-id="{{ $wiseId }}"
                              data-open-tab="resume_cv"
                              title="Click to view Resume / CV"
                              style="display:inline-flex !important; align-items:center !important; gap:4px !important; padding:2px 8px !important; border-radius:4px !important; background:#f0fdf4 !important; color:#15803d !important; border:1px solid #bbf7d0 !important; font-size:10px !important; font-weight:600 !important; line-height:1.4 !important; white-space:nowrap !important; flex-shrink:0 !important; cursor:pointer !important;">
                            <i class="fas fa-check-circle me-1"></i><span>Resume</span>
                        </span>
                    @else
                        <span class="simf-nocv-pill aptk-nocv-pill" title="Resume missing" style="display:inline-flex !important; align-items:center !important; gap:4px !important; padding:2px 8px !important; border-radius:4px !important; background:#fef2f2 !important; color:#b91c1c !important; border:1px solid #fecaca !important; font-size:10px !important; font-weight:600 !important; line-height:1.4 !important; white-space:nowrap !important; flex-shrink:0 !important;">
                            <i class="fas fa-exclamation-circle me-1"></i><span>No CV</span>
                        </span>
                    @endif
                    @if (!empty($cardMeta['linkedin_url']))
                        <a href="{{ e($cardMeta['linkedin_url']) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="simf-linkedin-pill aptk-linkedin-pill"
                           title="LinkedIn Profile Verified"
                           style="display:inline-flex !important; align-items:center !important; gap:4px !important; padding:2px 8px !important; border-radius:4px !important; background:#ecfdf5 !important; color:#15803d !important; border:1px solid #86efac !important; font-size:10px !important; font-weight:600 !important; text-decoration:none !important; line-height:1.4 !important; white-space:nowrap !important; flex-shrink:0 !important;">
                            <i class="fab fa-linkedin me-1"></i><span>LinkedIn Verified</span>
                        </a>
                    @endif
                    @if (!empty($cardMeta['visa_display']))
                        <span class="simf-visa-pill aptk-visa-pill" title="{{ e('Visa Status: ' . $cardMeta['visa_raw']) }}" style="display:inline-flex !important; align-items:center !important; gap:4px !important; padding:2px 8px !important; border-radius:4px !important; background:#f8fafc !important; color:#334155 !important; border:1px solid #e2e8f0 !important; font-size:10px !important; font-weight:600 !important; line-height:1.4 !important; white-space:nowrap !important; flex-shrink:0 !important;">
                            <i class="fas fa-passport me-1"></i><span>{{ $cardMeta['visa_display'] }}</span>
                        </span>
                    @endif
                </div>
            </div>

        </article>
    @endforeach
@else
    <div class="simf-empty-card text-center p-8 bg-white rounded border border-dashed border-gray-300">
        <div class="simf-empty-icon mb-3" style="font-size:32px; color:#94a3b8;">
            <i class="fa-solid fa-users-slash"></i>
        </div>
        <div class="simf-empty-title fw-bold text-dark fs-5 mb-1">No Candidates Found</div>
        <div class="simf-empty-sub text-muted fs-7">No candidates match your selected pipeline stage, company type, or search filters.</div>
    </div>
@endif
