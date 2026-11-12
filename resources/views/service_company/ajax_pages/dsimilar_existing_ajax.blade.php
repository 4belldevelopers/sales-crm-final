@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();
    $company_id = auth()->user()->company_id;
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $row->id = $row->candidate_id;
            $row->thirdparty_company_id = $row->ClientComID;
            $jobMode = @$modeFilter;
            $jobCurrency = @$currencyFilter;
            $symbols = @$symbolsFilter;

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

            // Skill matching
            $skillArr = is_array($row->matched_skills ?? null) ? $row->matched_skills : [];
            $candidateAllSkills = is_array($row->all_skills ?? null) ? $row->all_skills : (explode(',', (string) @$row->skills) ?: $skillArr);

            $preSkillRows = $rowMaps['skills'][$row->job_id ?? $jobid] ?? ($rowMaps['job_skills'] ?? null);
            if ($preSkillRows !== null) {
                $jobSkillsForDisplay = collect($preSkillRows)->pluck('skill_name')->filter()->map(fn($s) => strtolower(trim($s)))->unique()->values()->toArray();
                if (empty($jobSkillsForDisplay) && is_array($preSkillRows)) {
                    $jobSkillsForDisplay = array_values(array_unique(array_filter(array_map(fn($s) => strtolower(trim((string)$s)), $preSkillRows))));
                }
            } else {
                try {
                    $jobSkillsForDisplay = DB::table('job_metronics_skills')
                        ->where('job_id', $row->job_id ?? $jobid)
                        ->pluck('skill_name')
                        ->map(fn($s) => strtolower(trim($s)))
                        ->unique()
                        ->values()
                        ->toArray();
                } catch (\Throwable $e) {
                    $jobSkillsForDisplay = [];
                }
            }

            $percentageString = GeneralMethod::skill_percentage($jobSkillsForDisplay, $candidateAllSkills);
            $percentage = (int) round((float) rtrim($percentageString ?: '0%', '%'));
            $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
            $matchBg = $matchStyle['bg'];
            $matchColor = $matchStyle['color'];

            $missingSkills = array_values(array_filter($jobSkillsForDisplay, function ($required) use ($candidateAllSkills) {
                foreach ($candidateAllSkills as $matched) {
                    if (GeneralMethod::skillMatchesPhrasePrefix($required, $matched)) {
                        return false;
                    }
                }
                return true;
            }));
            $matchTooltipParts = [];
            if (!empty($skillArr)) {
                $matchTooltipParts[] = 'Matched: ' . implode(', ', array_map('ucfirst', $skillArr));
            }
            if (!empty($missingSkills)) {
                $matchTooltipParts[] = 'Missing: ' . implode(', ', array_map('ucfirst', $missingSkills));
            }
            $matchTooltipText = !empty($matchTooltipParts) ? implode(' — ', $matchTooltipParts) : 'No required skills configured for this job';

            $mandatoryMatched = $row->matched_mandatory_skills ?? [];
            $optionalMatched = array_values(array_udiff($skillArr, $mandatoryMatched, 'strcasecmp'));
            $allSkillsList = $row->all_skills ?? $skillArr;
            $unmatchedSkills = array_values(array_udiff($allSkillsList, $skillArr, 'strcasecmp'));

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

            // Salary
            $current_ctc_cur = $currencySymbole[@$jobCurrency] ?? '';
            $current_ectc_cur = $currencySymbole[@$jobCurrency] ?? '';
            $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly, $symbols, $jobMode);
            $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $symbols, $jobMode);
            $ctc = explode('.', GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount))[0];
            $ectc = explode('.', GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount))[0];
            $current_ctc_mode = @$salaryMode[$jobMode];
            $current_ectc_mode = @$salaryMode[$jobMode];

            // Notice period
            $np = $row->np_id;
            if (empty($np)) {
                $np = 'N/A';
            } else {
                if ($row->serving_notice_period_mode == 0) {
                    $np = !empty($row->np_id) ? $row->np_id . ' Days' : 'N/A';
                } elseif ($row->serving_notice_period_mode == 1) {
                    $originalDate = $row->np_id;
                    $np = 'SNP LWD ' . date('d-M-y', strtotime($originalDate));
                    if (date('Y-m-d', strtotime($row->np_id)) < date('Y-m-d')) {
                        $np = 'Immediate';
                    }
                }
            }
            $npClass = 'dsf-notice-default';
            if ($np === 'Immediate') {
                $npClass = 'dsf-notice-immediate';
            } elseif (($row->serving_notice_period_mode ?? null) == 0 && !empty($row->np_id)) {
                $npClass = 'dsf-notice-days';
            } elseif (($row->serving_notice_period_mode ?? null) == 1) {
                $npClass = 'dsf-notice-snp';
            }

            $currentCompanyFull = ucwords((string) @$row->current_company);

            // Location
            $row_country_name = !empty(@$row->country_name) ? ucwords(@$row->country_name) : '----';
            if ($row_country_name === '----' && !empty(@$row->country) && is_numeric(@$row->country)) {
                try {
                    foreach (GeneralMethod::getCountryName(@$row->country) as $cval) {
                        $row_country_name = ucwords($cval['name']);
                    }
                } catch (\Throwable $e) {}
            }

            $row_state_name = @$row->state_name ?? '';
            if (empty($row_state_name) && !empty($row->state)) {
                if (@$row->country == 101) {
                    $row_state_name = @$row->state_name;
                } elseif (is_numeric(@$row->country) && is_numeric($row->state) && (int) $row->state > 0) {
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
                    try {
                        $row_city = DB::table('city_list')->where('city_id', @$row->city_id)->first();
                        $row_city_name = @$row_city->city;
                    } catch (\Throwable $e) {}
                } else {
                    $row_city_name = @$row->city;
                }
            }

            // Jobs + Status
            $job_title = ucwords((string) @$row->title);
            $client_name = ucwords((string) @$row->client_name);
            if (($row->client_comapny_id ?? $company_id) != $company_id) {
                $client_name = ucwords((string) @$row->company_name);
            }
            $created_at = !empty($row->created_at) ? date('d M , Y', strtotime($row->created_at)) : '—';

            $job_status_get = GeneralMethod::jobDetails($row->jobId);
            $rawStatus = strtolower((string) @$job_status_get->status);
            if ($rawStatus === 'hold') {
                $status = 'Hold';
                $statusBg = '#fef3c7';
                $statusColor = '#d97706';
            } elseif ($rawStatus === 'inactive') {
                $status = 'Closed';
                $statusBg = '#fee2e2';
                $statusColor = '#dc2626';
            } else {
                $status = 'Hiring';
                $statusBg = '#dcfce7';
                $statusColor = '#16a34a';
            }

            $recommendation_text = __('messages.internal_tag');
            if (($row->client_comapny_id ?? $company_id) != $company_id) {
                $recommendation_text = __('messages.clinet_tag');
            }

            // Ownership
            if ($company_id != ($row->ClientComID ?? 0) && ($row->ClientComID ?? 0) > 0) {
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
                $rowpostedby = @$row->rec_candidate_assign_id ? ($row->posted_by ?? '') : $rowpostedby;
            } else {
                $rowpostedby = $row->posted_by ?? '';
            }
            $post_company_name = @$row->post_company_name ?: @$row->company_name;

            // Stage
            $stage_default_css = 'stage_sub_stage';
            if (($row->thirdparty_company_id ?? 0) > 0 && $row->thirdparty_company_id == $company_id) {
                $stage_default_css = 'client_stage_sub_stage';
            }
            if (($row->thirdparty_company_id ?? 0) > 0 && $row->thirdparty_company_id == $company_id && (@$row->master_status_id ?? @$row->job_master_status_id ?? 0) != 8) {
                $stage_default_css = '';
            }

            $subStageName = '';
            if ((@$row->master_status_id ?? @$row->job_master_status_id ?? 0) != 8 && !empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                $childStatus = DB::table('candidate_status')->where('id', $row->child_status_id)->first();
                $subStageName = @$childStatus->name ?? '';
            }

            // Added status check
            $candId = (int) (@$row->candidate_id ?? @$row->id ?? 0);
            if (isset($rowMaps)) {
                $isRecAssigned = isset($rowMaps['rec_assigned'][$candId]) || isset($rowMaps['recAssignedSet'][$candId]);
                $source_from_type = $rowMaps['source_from_type'][$candId] ?? $rowMaps['sourceTypeMap'][$candId] ?? 0;
                $isParentAssigned = isset($rowMaps['parent_assigned'][$candId]) || isset($rowMaps['parentAssignedSet'][$candId]);
                $isExistingInJob = isset($rowMaps['existing_in_job'][$candId]) || isset($rowMaps['existingCandidatesSet'][$candId]);
                if (isset($assignedCandidateIds) && in_array($candId, $assignedCandidateIds, true)) {
                    $isExistingInJob = true;
                }
                $isAdded = ($isRecAssigned || $isParentAssigned || $isExistingInJob);
            } else {
                try {
                    $recCounter = DB::table('rec_candidate_assign')
                        ->where('source_jobid', @$jobid)
                        ->where(function ($q) use ($candId) {
                            $q->where('source_from_id', $candId)->orWhere('source_application_id', $candId);
                        })->count();
                    $source_from_type = 0;
                    if (DB::table('rec_candidate_assign')->where('source_company_id', '=', $company_id)->where('source_application_id', '=', $candId)->where('source_from_type', '=', 2)->count() > 0) {
                        $source_from_type = 2;
                    }
                    if (DB::table('rec_candidate_assign')->where('source_company_id', '=', $company_id)->where('source_application_id', '=', $candId)->where('source_from_type', '=', 1)->count() > 0) {
                        $source_from_type = 1;
                    }
                    $parent_conter = DB::table('rec_candidate_assign')->where('source_company_id', '=', $company_id)->where('source_jobid', '=', $jobid)->where('parent_id', '=', $candId)->count();
                    $existingCandidatesQuery = DB::table('add_job_wise_condidates')->where('job_id', '=', $jobid)->where('job_application_id', '=', $candId)->count();
                    $isAdded = ($recCounter > 0 || $parent_conter > 0 || $existingCandidatesQuery > 0);
                } catch (\Throwable $e) {
                    $isAdded = false;
                }
            }
        @endphp

        <article class="dsf-cand-card {{ $flag == 1 ? 'is-duplicate' : '' }}" data-candidate-id="{{ @$row->id }}">
            <div class="dsf-cand-grid">
                {{-- Sub-Card 1: Candidate Info --}}
                <div class="dsf-cand-subcard dsf-cand-subcard--info">
                    <div class="dsf-subcard-head">
                        <span class="dsf-subcard-label">
                            <span class="form-check form-check-sm form-check-custom form-check-solid me-1">
                                <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" value="{{ $row->id }}" />
                            </span>
                            Candidate Info
                        </span>
                        @if ($flag == 1)
                            <span class="dsf-dup-pill" title="Duplicate candidate detected">
                                <i class="fa-solid fa-clone"></i> Duplicate
                            </span>
                        @endif
                    </div>
                    <div class="dsf-cand-info-row">
                        <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}" class="{{ $profile_sidebar_css }} dsf-avatar {{ $bgTextDecore }}">
                            {{ strtoupper(@$strVal) }}
                        </a>
                        <div class="dsf-cand-info-meta">
                            <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}" class="{{ $profile_sidebar_css }} dsf-cand-name {{ $flag == 1 ? 'text-danger' : '' }}" title="{{ ucwords((string) @$row->full_name) }}">
                                {{ ucwords((string) @$row->full_name) }}
                            </a>
                            <div class="dsf-cand-role" title="{{ ucwords((string) @$row->current_designation) }}">
                                {{ ucwords((string) @$row->current_designation) ?: '—' }}
                            </div>
                            <div class="dsf-cand-company" title="{{ $currentCompanyFull }}">
                                <i class="fa-regular fa-building me-1"></i>{{ $currentCompanyFull ?: '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 2: Compensation & Exp --}}
                <div class="dsf-cand-subcard">
                    <div class="dsf-subcard-head">
                        <span class="dsf-subcard-label">Compensation &amp; Exp</span>
                    </div>
                    <div class="dsf-val-salary">
                        <div class="dsf-sal-main" title="Expected: {{ $current_ectc_cur . $ectc }} /{{ $current_ectc_mode }}">
                            {{ $current_ectc_cur }}{{ $ectc }}<span class="dsf-sal-mode">/{{ $current_ectc_mode }}</span>
                        </div>
                        <div class="dsf-sal-sub">Cur: {{ $current_ctc_cur }}{{ $ctc }}/{{ $current_ctc_mode }}</div>
                    </div>
                    <div class="dsf-val-exp" title="Total: {{ $formattedExperience }} Y | Relevant: {{ $rel_formattedExperience }} Y">
                        <i class="fa-solid fa-briefcase"></i>
                        <span>{{ $formattedExperience !== '' ? $formattedExperience : '0' }} Y Total</span>
                        <span class="dsf-dot">&bull;</span>
                        <span>{{ $rel_formattedExperience !== '' ? $rel_formattedExperience : '0' }} Y Rel</span>
                    </div>
                    <div class="dsf-val-notice">
                        <i class="fa-regular fa-clock"></i>
                        <span class="dsf-notice-txt {{ $npClass }}">NP: {{ $np }}</span>
                    </div>
                </div>

                {{-- Sub-Card 3: Location & Degree --}}
                <div class="dsf-cand-subcard">
                    <div class="dsf-subcard-head">
                        <span class="dsf-subcard-label">Location &amp; Degree</span>
                    </div>
                    <div>
                        <div class="dsf-loc-city" title="{{ ucwords((string) $row_city_name) }}, {{ strtoupper((string) $row_country_name) }}">
                            <i class="fa-solid fa-location-dot me-1"></i>{{ ucwords((string) $row_city_name) ?: '—' }}, {{ strtoupper(mb_substr((string) $row_country_name, 0, 2)) }}
                        </div>
                        @if (!empty($row_state_name) && $row_state_name !== '----')
                            <div class="dsf-loc-state" title="{{ ucwords((string) $row_state_name) }}">
                                {{ ucwords((string) $row_state_name) }}
                            </div>
                        @endif
                    </div>
                    <div class="dsf-loc-edu" title="{{ @$row->qualification_name }}">
                        <i class="fa-solid fa-graduation-cap me-1"></i>{{ ucwords((string) @$row->qualification_name) ?: '—' }}
                    </div>
                </div>

                {{-- Sub-Card 4: Matched Job & Status --}}
                <div class="dsf-cand-subcard">
                    <div class="dsf-subcard-head">
                        <span class="dsf-subcard-label">Matched Job &amp; Status</span>
                    </div>
                    <div>
                        <a href="javascript:void(0);" class="dsf-job-title d-block" title="{{ @$row->title }}">
                            {{ $job_title ?: '—' }}
                        </a>
                        <div class="dsf-job-company" title="{{ @$row->client_name }}">
                            {{ $client_name ?: '—' }}
                        </div>
                    </div>
                    <div class="dsf-job-meta-row">
                        <span class="badge dsf-job-status-pill" style="background:{{ $statusBg }};color:{{ $statusColor }};">{{ $status }}</span>
                        <span class="dsf-job-type-tag">#{{ @$row->jobId }} &middot; {{ $recommendation_text }}</span>
                    </div>
                </div>

                {{-- Sub-Card 5: Ownership & Submitted --}}
                <div class="dsf-cand-subcard">
                    <div class="dsf-subcard-head">
                        <span class="dsf-subcard-label">Ownership &amp; Added</span>
                    </div>
                    <div>
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="show-detail dsf-owner-name d-block" title="{{ @$rowpostedby }}">
                            <i class="fa-regular fa-circle-user me-1 text-primary"></i>{{ ucfirst((string) @$rowpostedby) ?: '—' }}
                        </a>
                        <div class="dsf-owner-company" title="{{ @$post_company_name }}">
                            <i class="fa-regular fa-building me-1"></i>{{ @$post_company_name ?: '—' }}
                        </div>
                    </div>
                    <div class="dsf-owner-submitted" title="Uploaded on {{ $created_at }}">
                        <i class="fa-regular fa-calendar me-1"></i>{{ $created_at }}
                    </div>
                </div>

                {{-- Sub-Card 6: Stage & Action --}}
                <div class="dsf-cand-subcard">
                    <div class="dsf-subcard-head">
                        <span class="dsf-subcard-label">Stage &amp; Action</span>
                    </div>
                    <div class="dsf-stage-action-wrap">
                        <a href="javascript:void(0);" data-row-id="{{ @$row->id }}"
                            class="dsf-stage-trigger {{ $stage_default_css }}"
                            @if (!empty($stage_default_css)) data-toggle="modal" data-target="#{{ $stage_default_css }}" @endif>
                            <span class="dsf-stage-pill {{ @$row->css_style }}">{{ ucwords((string) @$row->name) ?: '—' }}</span>
                            @if (!empty($subStageName))
                                <span class="dsf-substage-txt" title="{{ $subStageName }}">{{ ucwords((string) $subStageName) }}</span>
                            @endif
                        </a>
                        <div class="d-flex flex-column">
                            @if ($isAdded)
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
            <div class="dsf-cand-bottom">
                <div class="dsf-bottom-left">
                    <span class="dsf-match-pill" title="{{ $matchTooltipText }}"
                        style="background:{{ $matchBg }};color:{{ $matchColor }};cursor:help;">
                        Match {{ $percentage }}%
                    </span>
                    <div class="dsf-skills-scroll">
                        @foreach ($mandatoryMatched as $sval)
                            <span class="dsf-skill-pill is-mandatory" title="Matches a mandatory (starred) skill">
                                <span style="color:#eab308;">&#9733;</span> {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($optionalMatched as $sval)
                            <span class="dsf-skill-pill is-matched" title="Matches a required skill">
                                {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($unmatchedSkills as $sval)
                            <span class="dsf-skill-pill">{{ ucfirst($sval) }}</span>
                        @endforeach
                        @if (empty($mandatoryMatched) && empty($optionalMatched) && empty($unmatchedSkills))
                            <span class="dsf-skills-empty">No skills listed</span>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    @endforeach
@else
    <div class="dsf-empty-card">
        <div class="dsf-empty-icon">
            <i class="fa-solid fa-users-slash"></i>
        </div>
        @if (!empty($search ?? null))
            <div class="dsf-empty-title">No candidates match &quot;{{ $search }}&quot;</div>
            <div class="dsf-empty-sub">Clear or change the search text to see all matching candidates.</div>
        @elseif (!empty($hasActiveFilters ?? false))
            <div class="dsf-empty-title">No candidates match your current filters</div>
            <div class="dsf-empty-sub">Try widening or clearing a filter to see more results.</div>
        @else
            <div class="dsf-empty-title">No similar candidates found yet</div>
            <div class="dsf-empty-sub">Adjust filters or check back when more candidates enter your pipeline.</div>
        @endif
    </div>
@endif
