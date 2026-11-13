@php
    if (!class_exists(\App\Services\AppTrackerCardPresenter::class, false)) {
        $pPath = app_path('Services/AppTrackerCardPresenter.php');
        if (file_exists($pPath)) {
            require_once $pPath;
        }
    }
    use App\Helper\GeneralMethod;
    use App\Services\AppTrackerCardPresenter;
    use App\Services\CurrencyService;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();
    $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
    $symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
        0 => 'INR',
        1 => 'EUR',
        2 => 'JPY',
        3 => 'GBP',
        4 => 'USD',
        5 => 'AED',
    ];
    $company_id = auth()->check() ? auth()->user()->company_id : 1;
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $jobMode = @$modeFilter;
            $jobCurrency = @$currencyFilter;
            $symbols = @$symbolsFilter;

            // Candidate initials + avatar palette
            $str = trim((string) @$row->full_name);
            $strVal = strtolower($str[0] ?? 'c');
            $ord = ord($strVal);
            if ($ord >= 97 && $ord <= 101) {
                $bgTextDecore = 'bg-light-danger text-danger';
            } elseif ($ord >= 102 && $ord <= 106) {
                $bgTextDecore = 'bg-light-warning text-warning';
            } elseif ($ord >= 107 && $ord <= 111) {
                $bgTextDecore = 'bg-light-primary text-primary';
            } elseif ($ord >= 112 && $ord <= 116) {
                $bgTextDecore = 'bg-light-success text-success';
            } elseif ($ord >= 117 && $ord <= 121) {
                $bgTextDecore = 'bg-dark text-secondary';
            } else {
                $bgTextDecore = 'bg-light-secondary text-dark';
            }

            $flag = ($row->duplicate ?? 0) == 1 ? 1 : 0;

            $profile_sidebar_css = 'show-application-detail';
            if (($row->thirdparty_company_id ?? 0) > 0 && $row->thirdparty_company_id == $company_id) {
                $profile_sidebar_css = 'client-show-application-detail';
            }

            // Skill matching (same authoritative job_metronics_skills logic)
            $skillArr = is_array($row->matched_skills ?? null) ? $row->matched_skills : [];
            $candidateAllSkills = is_array($row->all_skills ?? null) ? $row->all_skills : $skillArr;

            $preSkillRows = $rowMaps['skills'][$row->job_id ?? $jobid] ?? ($rowMaps['job_skills'] ?? null);
            if ($preSkillRows !== null) {
                $jobRequiredSkills = collect($preSkillRows)->pluck('skill_name')->filter()->map(fn($s) => strtolower(trim($s)))->unique()->values()->all();
                if (empty($jobRequiredSkills) && is_array($preSkillRows)) {
                    $jobRequiredSkills = array_values(array_unique(array_filter(array_map(fn($s) => strtolower(trim((string)$s)), $preSkillRows))));
                }
            } else {
                try {
                    $jobRequiredSkills = DB::table('job_metronics_skills')
                        ->where('job_id', $row->job_id ?? $jobid)
                        ->pluck('skill_name')
                        ->map(fn($s) => strtolower(trim($s)))
                        ->unique()
                        ->values()
                        ->all();
                } catch (\Throwable $e) {
                    $jobRequiredSkills = [];
                }
            }

            $satisfiedRequired = [];
            $representativeByRequired = [];
            foreach ($jobRequiredSkills as $req) {
                foreach ($candidateAllSkills as $cand) {
                    if (GeneralMethod::skillMatchesPhrasePrefix($req, $cand)) {
                        $satisfiedRequired[$req] = true;
                        if (!isset($representativeByRequired[$req])) {
                            $representativeByRequired[$req] = $cand;
                        }
                        break;
                    }
                }
            }

            $mandatorySatisfied = [];
            $optionalSatisfied = [];
            $mandatoryCandidateKeys = array_map('strtolower', (array) ($row->matched_mandatory_skills ?? []));
            foreach (array_keys($satisfiedRequired) as $req) {
                $rep = $representativeByRequired[$req] ?? null;
                $isMandatory = $rep !== null && in_array(strtolower($rep), $mandatoryCandidateKeys, true);
                if ($isMandatory) {
                    $mandatorySatisfied[] = $req;
                } else {
                    $optionalSatisfied[] = $req;
                }
            }

            $percentageString = GeneralMethod::skill_percentage($jobRequiredSkills, $candidateAllSkills);
            $percentage = (int) round((float) rtrim($percentageString ?: '0%', '%'));
            $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
            $matchBg = $matchStyle['bg'];
            $matchColor = $matchStyle['color'];

            $missingSkills = array_values(array_filter($jobRequiredSkills, fn($required) => empty($satisfiedRequired[$required])));
            $matchTooltipParts = [];
            if (!empty($satisfiedRequired)) {
                $matchedLabels = array_map(fn($req) => ucfirst($representativeByRequired[$req]), array_keys($satisfiedRequired));
                $matchTooltipParts[] = 'Matched: ' . implode(', ', $matchedLabels);
            }
            if (!empty($missingSkills)) {
                $matchTooltipParts[] = 'Missing: ' . implode(', ', array_map('ucfirst', $missingSkills));
            }
            $matchTooltipText = !empty($matchTooltipParts) ? implode(' — ', $matchTooltipParts) : 'No required skills configured for this job';

            $matchedRepresentativeStrings = array_map(fn($req) => strtolower($representativeByRequired[$req]), array_keys($satisfiedRequired));
            $unmatchedSkills = array_values(array_udiff($candidateAllSkills, $matchedRepresentativeStrings, 'strcasecmp'));

            // Experience
            $experience = trim((string) @$row->total_exp);
            if ($experience === '0.') {
                $experience = '0';
            }
            $formattedExperience = $experience;
            if (strpos($experience, '.') !== false) {
                $parts = explode('.', $experience);
                if (isset($parts[1]) && $parts[1] === '00') {
                    $formattedExperience = $parts[0];
                }
            }

            $rel_experience = trim((string) @$row->rel_exp);
            if ($rel_experience === '0.') {
                $rel_experience = '0';
            }
            $rel_formattedExperience = $rel_experience;
            if (strpos($rel_experience, '.') !== false) {
                $parts = explode('.', $rel_experience);
                if (isset($parts[1]) && $parts[1] === '00') {
                    $rel_formattedExperience = $parts[0];
                }
            }

            // Compensation
            $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly, $symbols, $jobMode);
            $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $symbols, $jobMode);
            $plainCurrencySymbol = CurrencyService::resolvePlainSymbolFromLegacy(
                @$jobCurrency,
                $currencySymbole,
                $billFormatViewSalary->symbol ?? null
            );
            $current_ctc_cur = $plainCurrencySymbol;
            $current_ectc_cur = $plainCurrencySymbol;
            $ctc = explode('.', GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount))[0];
            $ectc = explode('.', GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount))[0];
            $current_ctc_mode = @$salaryMode[$jobMode] ?: 'Monthly';
            $current_ectc_mode = @$salaryMode[$jobMode] ?: 'Monthly';

            // Notice period
            $np = $row->np_id;
            if (empty($np)) {
                $np = 'N/A';
            } else {
                if ($row->serving_notice_period_mode == 0) {
                    $np = !empty($row->np_id) ? $row->np_id . ' Days' : 'N/A';
                } elseif ($row->serving_notice_period_mode == 1) {
                    if (!empty($row->np_id)) {
                        $np = GeneralMethod::ymdTodmyNotTime($row->np_id);
                        if (date('Y-m-d', strtotime($row->np_id)) < date('Y-m-d')) {
                            $np = 'Immediate';
                        }
                    } else {
                        $np = 'Immediate';
                    }
                }
            }
            $npClass = 'simf-notice-default';
            if ($np === 'Immediate') {
                $npClass = 'simf-notice-immediate';
            } elseif (($row->serving_notice_period_mode ?? null) == 0 && !empty($row->np_id)) {
                $npClass = 'simf-notice-days';
            } elseif (($row->serving_notice_period_mode ?? null) == 1) {
                $np = 'SNP LWD ' . $np;
                $npClass = 'simf-notice-snp';
            }

            $currentCompanyFull = ucwords((string) @$row->current_company);

            // Location
            $row_country_name = !empty(@$row->country_name) ? ucwords(@$row->country_name) : '----';
            if ($row_country_name === '----' && !empty(@$row->country)) {
                if (isset($rowMaps['country_map'][$row->country])) {
                    $row_country_name = ucwords($rowMaps['country_map'][$row->country]);
                } elseif (is_numeric(@$row->country)) {
                    try {
                        foreach (GeneralMethod::getCountryName(@$row->country) as $cval) {
                            $row_country_name = ucwords($cval['name']);
                        }
                    } catch (\Throwable $e) {}
                }
            }

            $row_state_name = @$row->state_name ?? '';
            if (empty($row_state_name) && !empty($row->state)) {
                if (@$row->country == 101) {
                    $row_state_name = @$row->state_name;
                } elseif (is_numeric(@$row->country) && is_numeric(@$row->state) && (int) $row->state > 0) {
                    try {
                        foreach (GeneralMethod::getStateName(@$row->country, $row->state) as $sval) {
                            $row_state_name = ucwords($sval['name']);
                        }
                    } catch (\Throwable $e) {}
                }
            }

            $row_city_name = @$row->city_name ?? '';
            if (empty($row_city_name)) {
                if (@$row->country == 101) {
                    if (isset($rowMaps['city_map'][$row->city_id])) {
                        $row_city_name = $rowMaps['city_map'][$row->city_id];
                    } else {
                        try {
                            $row_city = DB::table('city_list')->where('city_id', @$row->city_id)->first();
                            $row_city_name = @$row_city->city;
                        } catch (\Throwable $e) {}
                    }
                } else {
                    $row_city_name = @$row->city;
                }
            }
            $qualFull = mb_strtoupper((string) @$row->qualification_name);

            // Work mode resolution
            $candId = (int) ($row->candidate_id ?? (is_array($rowArr) ? ($rowArr['candidate_id'] ?? 0) : ($row->candidate_id ?? 0)) ?? $row->id ?? 0);
            $rawWiseId = (int) (is_array($rowArr) ? ($rowArr['id'] ?? 0) : ($row->id ?? $row->wiseId ?? $row->wise_id ?? 0));
            if (empty($row->wm) && isset($rowMaps['work_mode_map'][$candId])) {
                $row->wm = $rowMaps['work_mode_map'][$candId];
            }
            $wmMeta = AppTrackerCardPresenter::resolveWorkMode($row);
            $wmIcon = !empty($wmMeta['icon']) ? $wmMeta['icon'] : 'fa-solid fa-building';
            $wmLabel = !empty($wmMeta['label']) ? $wmMeta['label'] : '–';
            $hasResume = !empty($row->resume) || !empty($rowMaps['resume_map'][$candId] ?? null);
            $linkedinUrl = AppTrackerCardPresenter::resolveLinkedinUrl($row);
            $visaMeta = AppTrackerCardPresenter::resolveVisa($row, $row_country_name ?? null);
            $visaDisplay = $visaMeta['display'];
            $visaRaw = $visaMeta['raw'];

            // Matched Job & Status
            $candJobId = (int) (@$row->jobId ?? @$row->job_id ?? 0);
            $wiseInfo = $rowMaps['wise_job_info'][$rawWiseId] 
                ?? ($rowMaps['wise_job_info'][$candId] 
                ?? ($rowMaps['wise_job_info']['cand_' . $candId] 
                ?? ($rowMaps['wise_job_info']['job_' . $candJobId] 
                ?? null)));
            $matchedJobTitle = ucwords((string) ($wiseInfo->title ?? @$row->title ?? ''));
            $matchedJobId = (int) ($wiseInfo->matched_job_id ?? $candJobId);
            $matchedCategory = ucwords((string) ($wiseInfo->category_name ?? $rowMaps['job_category_map'][$matchedJobId] ?? ''));
            $client_name = ucwords((string) ($wiseInfo->client_name ?? @$row->client_name ?? ''));
            $companyName = ucwords((string) ($wiseInfo->company_name ?? @$row->company_name ?? ''));
            if ($wiseInfo && ($wiseInfo->companyIdCheck ?? 0) != $company_id && !empty($wiseInfo->company_name)) {
                $client_name = $companyName;
            }

            // Determine Target Req Classification per Rule 25
            $isSelfCompany = !empty($wiseInfo->self_company) || (($wiseInfo->companyIdCheck ?? $company_id) == $company_id && empty($wiseInfo->client_name));
            $isDirectClient = ($wiseInfo && ($wiseInfo->companyIdCheck ?? 0) != $company_id);
            if ($isSelfCompany) {
                $targetTypeLabel = 'My Organization';
                $targetTypeClass = 'type-my-org';
                $targetTypeIcon = 'fa-solid fa-id-badge';
                $targetTypeStyle = 'background:#ecfeff; color:#0891b2; border:1px solid #a5f3fc;';
                $targetSubcardClass = 'simf-cand-subcard--my-org';
            } elseif ($isDirectClient) {
                $targetTypeLabel = 'Direct Client';
                $targetTypeClass = 'type-direct';
                $targetTypeIcon = 'fa-solid fa-shield-alt';
                $targetTypeStyle = 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;';
                $targetSubcardClass = 'simf-cand-subcard--direct';
            } else {
                $targetTypeLabel = 'Internal Client';
                $targetTypeClass = 'type-internal';
                $targetTypeIcon = 'fa-solid fa-building';
                $targetTypeStyle = 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;';
                $targetSubcardClass = 'simf-cand-subcard--internal';
            }

            // Job Status Pill
            $jobStatusVal = strtolower((string) ($wiseInfo->jobStatus ?? 'active'));
            if ($jobStatusVal === 'active') {
                $targetStatusLabel = 'Hiring';
                $targetStatusStyle = 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;';
            } elseif ($jobStatusVal === 'hold') {
                $targetStatusLabel = 'On Hold';
                $targetStatusStyle = 'background:#fffbeb; color:#d97706; border:1px solid #fde68a;';
            } else {
                $targetStatusLabel = 'Closed';
                $targetStatusStyle = 'background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;';
            }

            // Employment Type
            $empTypeRaw = $wiseInfo->employment_type ?? 0;
            $empTypes = GeneralMethod::employmentType();
            $empTypeLabel = $empTypes[$empTypeRaw] ?? ($empTypeRaw == 1 ? 'Contract' : 'Full-Time');

            // Ownership
            if ($company_id != ($row->ClientComID ?? 0) && ($row->ClientComID ?? 0) > 0) {
                $spokKey = ($row->jobId ?? $row->job_id ?? 0) . '_' . $row->ClientComID;
                if (isset($rowMaps['spokperson_map'][$spokKey])) {
                    $rowpostedby = $rowMaps['spokperson_map'][$spokKey];
                } else {
                    try {
                        $spokperson = DB::table('job_assign')
                            ->select('users.name')
                            ->join('users', 'users.id', 'job_assign.spok_user_id')
                            ->where('job_assign.job_id', $row->jobId)
                            ->where('job_assign.company_id', $row->ClientComID)
                            ->first();
                        $rowpostedby = @$spokperson ? @$spokperson->name : ($row->posted_by ?? '');
                    } catch (\Throwable $e) {
                        $rowpostedby = $row->posted_by ?? '';
                    }
                }
                $rowpostedby = @$row->rec_candidate_assign_id ? ($row->posted_by ?? '') : $rowpostedby;
            } else {
                $rowpostedby = $row->posted_by ?? '';
            }
            $post_company_name = @$row->post_company_name ?: @$row->company_name;
            $created_at = !empty($row->created_at) ? GeneralMethod::ymdTodmyNotTime($row->created_at) : '—';

            // Stage
            $stage_default_css = 'stage_sub_stage';
            if (($row->thirdparty_company_id ?? 0) > 0 && $row->thirdparty_company_id == $company_id) {
                $stage_default_css = 'client_stage_sub_stage';
            }
            if (($row->thirdparty_company_id ?? 0) > 0 && $row->thirdparty_company_id == $company_id && (@$row->master_status_id ?? @$row->job_master_status_id ?? 0) != 8) {
                $stage_default_css = '';
            }

            $subStageName = '';
            if ((@$row->master_status_id ?? @$row->job_master_status_id ?? 0) != 8 && !empty(@$row->childStatusId) && @$row->childStatusId > 0) {
                if (isset($rowMaps['child_status_map'][$row->childStatusId])) {
                    $subStageName = $rowMaps['child_status_map'][$row->childStatusId];
                } else {
                    $childStatus = DB::table('candidate_status')->where('id', $row->childStatusId)->first();
                    $subStageName = @$childStatus->name ?? '';
                }
            }

            // Assignment state
            $candId = (int) (@$row->candidate_id ?? @$row->id ?? 0);
            $isAlreadyAdded = false;
            if (isset($rowMaps['rec_assigned'])) {
                $isRecAssigned = isset($rowMaps['rec_assigned'][$candId]);
                $source_from_type = $rowMaps['source_from_type'][$candId] ?? 0;
                $isParentAssigned = isset($rowMaps['parent_assigned'][$candId]);
                $isExistingInJob = isset($rowMaps['existing_in_job'][$candId]);
                if (isset($assignedCandidateIds) && in_array($candId, $assignedCandidateIds, true)) {
                    $isExistingInJob = true;
                }
                $isAlreadyAdded = $isRecAssigned || $isParentAssigned || $isExistingInJob;
            } else {
                try {
                    $recCounter = DB::table('rec_candidate_assign')
                        ->where('source_jobid', @$jobid)
                        ->where(function ($q) use ($row) {
                            $q->where('source_from_id', @$row->candidate_id)
                                ->orWhere('source_application_id', @$row->candidate_id);
                        })->count();
                    $source_from_type = 0;
                    if (DB::table('rec_candidate_assign')->where('source_company_id', '=', $company_id)->where('source_application_id', '=', @$row->candidate_id)->where('source_from_type', '=', 2)->count() > 0) {
                        $source_from_type = 2;
                    }
                    if (DB::table('rec_candidate_assign')->where('source_company_id', '=', $company_id)->where('source_application_id', '=', @$row->candidate_id)->where('source_from_type', '=', 1)->count() > 0) {
                        $source_from_type = 1;
                    }
                    $parent_conter = DB::table('rec_candidate_assign')->where('source_company_id', '=', $company_id)->where('source_jobid', '=', $jobid)->where('parent_id', '=', @$row->candidate_id)->count();
                    $existingCandidatesQuery = DB::table('add_job_wise_condidates')->where('job_id', '=', $jobid)->where('job_application_id', '=', $row->candidate_id)->count();
                    $isAlreadyAdded = ($recCounter > 0 || $parent_conter > 0 || $existingCandidatesQuery > 0);
                } catch (\Throwable $e) {
                    $isAlreadyAdded = false;
                }
            }
        @endphp

        <article class="simf-cand-card {{ $flag == 1 ? 'is-duplicate' : '' }}" data-candidate-id="{{ @$row->candidate_id }}">
            <div class="simf-cand-grid">
                {{-- Sub-Card 1: Candidate Info --}}
                <div class="simf-cand-subcard simf-cand-subcard--info">
                    <div class="simf-subcard-head">
                        <span class="simf-subcard-label">
                            <span class="form-check form-check-sm form-check-custom form-check-solid me-1">
                                <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" value="{{ @$row->candidate_id }}" />
                            </span>
                            Candidate Info
                        </span>
                        @if ($flag == 1)
                            <span class="simf-dup-pill" title="Duplicate candidate detected">
                                <i class="fa-solid fa-clone"></i> Duplicate
                            </span>
                        @endif
                    </div>
                    <div class="simf-cand-info-row">
                        <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}" class="{{ $profile_sidebar_css }} simf-avatar {{ $bgTextDecore }}">
                            {{ strtoupper(@$strVal) }}
                        </a>
                        <div class="simf-cand-info-meta">
                            <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}" class="{{ $profile_sidebar_css }} simf-cand-name {{ $flag == 1 ? 'text-danger' : '' }}" title="{{ ucwords((string) @$row->full_name) }}">
                                {{ ucwords((string) @$row->full_name) }}
                            </a>
                            <div class="simf-cand-role" title="{{ ucwords((string) @$row->current_designation) }}">
                                {{ ucwords((string) @$row->current_designation) ?: '—' }}
                            </div>
                            <div class="simf-cand-company" title="{{ $currentCompanyFull }}">
                                <i class="fa-regular fa-building me-1"></i>{{ $currentCompanyFull ?: '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 2: Compensation & Exp --}}
                <div class="simf-cand-subcard">
                    <div class="simf-subcard-head">
                        <span class="simf-subcard-label">Compensation &amp; Exp</span>
                    </div>
                    <div class="simf-val-salary">
                        <div class="simf-sal-main" title="Expected: {{ $current_ectc_cur . $ectc }} /{{ $current_ectc_mode }}">
                            {{ $current_ectc_cur }}{{ $ectc }}<span class="simf-sal-mode">/{{ $current_ectc_mode }}</span>
                        </div>
                        <div class="simf-sal-sub">Cur: {{ $current_ctc_cur }}{{ $ctc }}/{{ $current_ctc_mode }}</div>
                    </div>
                    <div class="simf-val-exp" title="Total: {{ $formattedExperience }} Y | Relevant: {{ $rel_formattedExperience }} Y">
                        <i class="fa-solid fa-briefcase me-1 text-muted"></i>
                        <span>{{ $formattedExperience !== '' ? $formattedExperience : '0' }} Y Total</span>
                        <span class="ms-1" style="color:#64748b; font-size:11px;">(Rel {{ $rel_formattedExperience !== '' ? $rel_formattedExperience : '0' }} Y)</span>
                    </div>
                    <div class="simf-val-notice">
                        <i class="fa-regular fa-clock"></i>
                        <span class="simf-notice-txt {{ $npClass }}">NP: {{ $np }}</span>
                    </div>
                </div>

                {{-- Sub-Card 3: Location & Degree (With Restored Work Mode) --}}
                <div class="simf-cand-subcard">
                    <div class="simf-subcard-head d-flex align-items-center justify-content-between mb-1" style="min-height:18px;">
                        <span class="simf-subcard-label text-uppercase text-truncate" style="font-size:10px; font-weight:700; color:#64748b; letter-spacing:0.4px;">Location &amp; Degree</span>
                    </div>
                    <div class="pt-1">
                        <div class="simf-loc-city text-truncate d-flex align-items-center" style="font-size:11.5px; font-weight:600; line-height:1.35; min-height:18px;" title="{{ ucwords((string) $row_city_name) }}, {{ strtoupper((string) $row_country_name) }}">
                            <i class="fa-solid fa-location-dot me-1 text-muted" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ ucwords((string) $row_city_name) ?: '—' }}@if(!empty($row_country_name) && $row_country_name !== '----'), {{ strtoupper(mb_substr((string) $row_country_name, 0, 2)) }}@endif</span>
                        </div>
                        <div class="simf-loc-mode text-truncate d-flex align-items-center mt-1" style="font-size:11px; font-weight:500; color:#475569; line-height:1.35; min-height:18px;" title="{{ !empty($wmMeta['label']) ? e($wmMeta['label']) : 'Work Mode: –' }}">
                            <i class="{{ $wmIcon }} me-1 text-primary" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ $wmLabel }}</span>
                        </div>
                        <div class="simf-loc-edu simf-edu-uppercase text-truncate d-flex align-items-center mt-1" style="font-size:11px; font-weight:500; color:#64748b; line-height:1.35; min-height:18px;" title="{{ $qualFull ?: '—' }}">
                            <i class="fa-solid fa-graduation-cap me-1 text-muted" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ $qualFull ?: '—' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 4: Matched Job & Status (Target Req Redesign) --}}
                <div class="simf-cand-subcard simf-cand-subcard--target {{ $targetSubcardClass }}">
                    <div class="simf-subcard-head d-flex align-items-center justify-content-between mb-1" style="min-height:18px;">
                        <span class="simf-subcard-label text-uppercase text-truncate" style="font-size:10px; font-weight:700; color:#64748b; letter-spacing:0.4px;" title="TARGET REQ">TARGET REQ</span>
                        <span class="simf-target-type-badge {{ $targetTypeClass }}" style="font-size:9.5px; font-weight:700; padding:1.5px 6px; border-radius:4px; text-transform:uppercase; white-space:nowrap !important; flex-shrink:0; {{ $targetTypeStyle }}">
                            <i class="{{ $targetTypeIcon }} me-1"></i>{{ $targetTypeLabel }}
                        </span>
                    </div>
                    <div class="simf-req-body pt-1">
                        <div class="simf-req-title text-truncate fw-bold text-dark" style="font-size:12.5px; line-height:1.35; color:#0f172a;" title="{{ $matchedJobTitle }}">
                            {{ $matchedJobTitle ?: '—' }}
                        </div>
                        <div class="simf-req-meta-line text-truncate mt-1" style="font-size:11px; line-height:1.35;">
                            <span class="fw-bold" style="color:#0284c7;">#{{ $matchedJobId }}</span>
                            @if(!empty($matchedCategory))
                                <span class="mx-1" style="color:#94a3b8;">&bull;</span>
                                <span style="color:#64748b;">{{ $matchedCategory }}</span>
                            @endif
                        </div>
                        <div class="simf-req-sub-line text-truncate mt-1" style="font-size:11px; line-height:1.35; color:#475569;">
                            <span class="fw-medium text-dark">{{ $client_name ?: '—' }}</span>
                            @if(!empty($empTypeLabel))
                                <span class="mx-1" style="color:#94a3b8;">&bull;</span>
                                <i class="fa-solid fa-briefcase text-primary me-1" style="font-size:10px;"></i>
                                <span style="color:#64748b;">{{ $empTypeLabel }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 5: Ownership & Submitted --}}
                <div class="simf-cand-subcard">
                    <div class="simf-subcard-head d-flex align-items-center justify-content-between mb-1" style="min-height:18px;">
                        <span class="simf-subcard-label text-uppercase text-truncate" style="font-size:10px; font-weight:700; color:#64748b; letter-spacing:0.4px;">Ownership &amp; Added</span>
                    </div>
                    <div class="pt-1">
                        <div class="simf-owner-name text-truncate d-flex align-items-center" style="font-size:11.5px; font-weight:600; line-height:1.35; min-height:18px;" title="{{ @$rowpostedby }}">
                            <i class="fa-regular fa-circle-user me-1 text-primary" style="width:13px; text-align:center; font-size:10.5px;"></i>
                            <a href="javascript:void(0);" data-row-id="{{ $row->candidate_id }}" class="show-detail text-dark text-hover-primary text-truncate">
                                {{ ucfirst((string) @$rowpostedby) ?: '—' }}
                            </a>
                        </div>
                        <div class="simf-owner-company text-truncate d-flex align-items-center mt-1" style="font-size:11px; font-weight:500; color:#475569; line-height:1.35; min-height:18px;" title="{{ @$post_company_name }}">
                            <i class="fa-regular fa-building me-1 text-muted" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ @$post_company_name ?: '—' }}</span>
                        </div>
                        <div class="simf-owner-submitted text-truncate d-flex align-items-center mt-1" style="font-size:11px; font-weight:500; color:#64748b; line-height:1.35; min-height:18px;" title="Uploaded on {{ @$created_at }}">
                            <i class="fa-regular fa-calendar me-1 text-muted" style="width:13px; text-align:center; font-size:10px;"></i>
                            <span class="text-truncate">{{ @$created_at ?: '—' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 6: Stage & Action --}}
                <div class="simf-cand-subcard">
                    <div class="simf-subcard-head d-flex align-items-center justify-content-between mb-1" style="min-height:18px;">
                        <span class="simf-subcard-label text-uppercase text-truncate" style="font-size:10px; font-weight:700; color:#64748b; letter-spacing:0.4px;" title="STAGE &amp; ACTION">Stage &amp; Action</span>
                    </div>
                    <div class="simf-stage-action-wrap pt-1">
                        <a href="javascript:void(0);" data-row-id="{{ @$row->candidate_id }}"
                            class="simf-stage-trigger {{ $stage_default_css }}"
                            @if (!empty($stage_default_css)) data-toggle="modal" data-target="#{{ $stage_default_css }}" @endif>
                            <span class="simf-stage-pill {{ @$row->css_style }}">{{ ucwords((string) @$row->name) ?: '—' }}</span>
                            @if (!empty($subStageName))
                                <span class="simf-substage-txt" title="{{ $subStageName }}">{{ ucwords((string) $subStageName) }}</span>
                            @endif
                        </a>
                        <div class="d-flex flex-column">
                            @if ($isAlreadyAdded)
                                <button type="button" class="btn btn-sm" disabled
                                    style="background:#16a34a;color:#fff;border-color:#16a34a;font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;cursor:default;display:inline-flex;align-items:center;justify-content:center;gap:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Added
                                </button>
                            @else
                                <a href="javascript:void(0);"
                                    class="btn btn-sm btn-primary add_to_inreview"
                                    style="font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;gap:5px;"
                                    data-row-from="existing"
                                    data-row-source_from_type="{{ @$source_from_type }}"
                                    data-row-id="{{ @$row->candidate_id }}"
                                    data-row-wiseId="{{ @$row->id }}"
                                    data-row-jobid="{{ @$jobid }}">
                                    Add to Job
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Intelligence & 3-Tier Skills Strip --}}
            <div class="simf-cand-bottom">
                <div class="simf-bottom-left">
                    <span class="simf-match-pill" title="{{ $matchTooltipText }}"
                        style="background:{{ $matchBg }};color:{{ $matchColor }};cursor:help;">
                        Match {{ $percentage }}%
                    </span>
                    <div class="simf-skills-scroll">
                        @foreach ($mandatorySatisfied as $req)
                            <span class="simf-skill-pill is-mandatory" title="Matches a mandatory (starred) skill">
                                <span style="color:#eab308;">&#9733;</span> {{ ucfirst($representativeByRequired[$req] ?? $req) }}
                            </span>
                        @endforeach
                        @foreach ($optionalSatisfied as $req)
                            <span class="simf-skill-pill is-matched" title="Matches a required skill">
                                {{ ucfirst($representativeByRequired[$req] ?? $req) }}
                            </span>
                        @endforeach
                        @foreach ($unmatchedSkills as $sval)
                            <span class="simf-skill-pill">{{ ucfirst($sval) }}</span>
                        @endforeach
                        @if (empty($mandatorySatisfied) && empty($optionalSatisfied) && empty($unmatchedSkills))
                            <span class="simf-skills-empty">No skills listed</span>
                        @endif
                    </div>
                </div>
                <div class="simf-bottom-right">
                    @if ($hasResume)
                        <span class="simf-resume-pill" title="Resume uploaded">
                            <i class="fas fa-check-circle"></i><span>Resume</span>
                        </span>
                    @else
                        <span class="simf-nocv-pill" title="Resume missing">
                            <i class="fas fa-exclamation-circle"></i><span>No CV</span>
                        </span>
                    @endif
                    @if (!empty($visaDisplay))
                        <span class="simf-visa-pill" title="{{ e('Visa Status: ' . $visaRaw) }}">
                            <i class="fas fa-passport"></i><span>{{ $visaDisplay }}</span>
                        </span>
                    @endif
                    @if (!empty($linkedinUrl))
                        <a href="{{ e($linkedinUrl) }}" target="_blank" rel="noopener noreferrer" class="simf-linkedin-pill" title="LinkedIn Profile Verified">
                            <i class="fab fa-linkedin"></i><span>LinkedIn Verified</span>
                        </a>
                    @endif
                </div>
            </div>
        </article>
    @endforeach
@else
    <div class="simf-empty-card">
        <div class="simf-empty-icon">
            <i class="fa-solid fa-users-slash"></i>
        </div>
        @if (!empty($search ?? null))
            <div class="simf-empty-title">No candidates match &quot;{{ $search }}&quot;</div>
            <div class="simf-empty-sub">Clear or change the search text to see all matching candidates.</div>
        @elseif (!empty($hasActiveFilters ?? false))
            <div class="simf-empty-title">No candidates match your current filters</div>
            <div class="simf-empty-sub">Try widening or clearing a filter to see more results.</div>
        @else
            <div class="simf-empty-title">No similar candidates found yet</div>
            <div class="simf-empty-sub">Adjust filters or check back when more candidates enter your pipeline.</div>
        @endif
    </div>
@endif
