@php
    use App\Helper\GeneralMethod;
    use App\Services\AppTrackerCardPresenter;
    use App\Services\CurrencyService;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
    $symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
        0 => 'INR',
        1 => 'EUR',
        2 => 'JPY',
        3 => 'GBP',
        4 => 'USD',
        5 => 'AED',
    ];
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;

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

            $profile_sidebar_css = 'pbench_show_application_detail';

            // 3-tier skill matching
            $skillArr = array_values(array_filter(array_map('trim', explode(',', (string) @$row->skills))));
            $targetJobId = $row->job_id ?? ($jobid ?? 0);
            if ($targetJobId > 0) {
                if (isset($rowMaps['skills'][$targetJobId])) {
                    $jobRequiredSkills = collect($rowMaps['skills'][$targetJobId])->pluck('skill_name')->toArray();
                    $percentageString = GeneralMethod::skill_percentage_from_array($jobRequiredSkills, $skillArr);
                } else {
                    $percentageString = GeneralMethod::skill_percentage($targetJobId, $skillArr);
                }
                $percentage = (int) round((float) rtrim($percentageString ?: '0%', '%'));
            } else {
                $matches = $row->matched_skill_count ?? 0;
                $totalElements = count($requiredSkills ?? []);
                $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
                $percentage = (int) round(min($matchPercentage, 100));
            }
            $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
            $matchBg = $matchStyle['bg'];
            $matchColor = $matchStyle['color'];

            $matchedSkillsArr = $row->matched_skills ?? [];
            $mandatoryMatched = $row->matched_mandatory_skills ?? [];
            $optionalMatched = array_values(array_udiff($matchedSkillsArr, $mandatoryMatched, 'strcasecmp'));
            $unmatchedSkillsArr = array_values(array_udiff($skillArr, $matchedSkillsArr, 'strcasecmp'));

            // Experience
            $experience = trim((string) @$row->total_exp);
            if ($experience === '0.') {
                $experience = '0';
            }
            $formattedExperience = $experience !== '' ? $experience : '0';

            $rel_experience = trim((string) @$row->rel_exp);
            if ($rel_experience === '0.') {
                $rel_experience = '0';
            }
            if ((float) ($row->total_exp ?? 0) == 0 && (float) ($row->rel_exp ?? 0) == 0) {
                $rel_formattedExperience = '0';
            } else {
                $rel_formattedExperience = $rel_experience !== '' ? $rel_experience : '0';
            }

            // Availability / Notice period
            $npArr = ['Notice period', 'Available from', 'Immediate'];
            $npModeIdx = (int) (@$row->serving_notice_period_mode ?? 0);
            $npLabel = $npArr[$npModeIdx] ?? 'Notice period';
            $npDate = $row->np_id ?? '';
            $npColorStyle = '#181c32';
            if ($npModeIdx == 0) {
                $npDate = ($row->np_id ?? '0') . ((int) ($row->np_id ?? 0) === 1 ? ' Day' : ' Days');
                $npColorStyle = '#d97706';
            } elseif ($npModeIdx == 1) {
                $originalDate = $row->np_id;
                $npDate = 'SNP LWD ' . date('d-M-y', strtotime($originalDate));
                $npColorStyle = '#7c3aed';
            } elseif ($npModeIdx == 2 || $npDate === 'Immediate') {
                $npDate = 'Immediate';
                $npColorStyle = '#16a34a';
            }

            // Compensation & Outsourcing type
            $safeCurrencyKey = (int) (@$min_salary_currency ?? 0);
            $safeSalMode = (int) (@$sal_mode ?? 0);
            $plainCurrencySymbol = CurrencyService::resolvePlainSymbolFromLegacy($safeCurrencyKey, $currencySymbole);
            $pay_rate_currency = $plainCurrencySymbol;
            $bill_rate_currency = $plainCurrencySymbol;
            $pay_rate_amount = '0';
            $bill_rate_amount = '0';
            if ((@$row->ectc_yearly ?? 0) > 0 || (@$row->ctc_yearly ?? 0) > 0) {
                $currencymain = GeneralMethod::currency();
                $currencyCode = $currencymain[$safeCurrencyKey] ?? 'INR';
                $convertedAmounEctc = GeneralMethod::conversionRates($currencyCode, $row->ectc_yearly, 0);
                $convertedAmounCtc = GeneralMethod::conversionRates($currencyCode, $row->ctc_yearly, 0);
                $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($convertedAmounEctc[$safeSalMode] ?? 0);
                $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($convertedAmounCtc[$safeSalMode] ?? 0);
                $pay_rate_amount = explode('.', (string) $pay_rate_amount)[0];
                $bill_rate_amount = explode('.', (string) $bill_rate_amount)[0];
            }
            $pay_rate_mode = @$salaryMode[$safeSalMode] ?? '';
            $bill_rate_mode = @$salaryMode[$safeSalMode] ?? '';
            $hideCurrentCtc = (($row->condidate_type ?? '') === 'bench_condidate');
            $outsourcing_type = ($row->outsourcing_type ?? 0) == 1 ? 'Fulltime' : 'Contract';

            // Location & Education
            $row_country_name = @$row->country_name;
            $row_city_name = @$row->city_name;
            $qualFull = mb_strtoupper((string) @$row->qualification_name);
            $wmMeta = AppTrackerCardPresenter::resolveWorkMode($row);
            $wmIcon = !empty($wmMeta['icon']) ? $wmMeta['icon'] : 'fa-solid fa-building';
            $wmLabel = !empty($wmMeta['label']) ? $wmMeta['label'] : '–';

            // Last Active & Status label
            $statusLabel = 'Bench';
            $statusColorClass = 'text-primary';
            if ($row->status == 1) {
                $statusLabel = 'Pool';
                $statusColorClass = 'text-primary';
            } elseif ($row->status == 4) {
                $statusLabel = 'Pause';
                $statusColorClass = 'text-warning';
            } elseif ($row->status == 3) {
                $statusLabel = 'Placed';
                $statusColorClass = 'text-success';
            } elseif ($row->status == 2) {
                $statusLabel = 'Bench';
                $statusColorClass = 'text-primary';
            }
            $updated_at = !empty($row->updated_at) ? GeneralMethod::ymdTodmyNotTime($row->updated_at) : '—';

            // Added / Requested / Expired check
            $isAdded = isset($assignedCandidateIds) && in_array((int) @$row->id, $assignedCandidateIds, true);
            if (!$isAdded && !isset($assignedCandidateIds)) {
                try {
                    $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                        ->select('job_application_id')
                        ->where('add_job_wise_condidates.job_id', '=', $jobid)
                        ->where('add_job_wise_condidates.job_application_id', '=', $row->id)
                        ->count();
                    $recCounter = DB::table('rec_candidate_assign')
                        ->where('source_jobid', '=', $jobid)
                        ->where(function ($q) use ($row) {
                            $q->where('source_from_id', @$row->id)
                              ->orWhere('source_application_id', @$row->id);
                        })
                        ->where('ac_status', 1)
                        ->count();
                    $isAdded = $existingCandidatesQuery > 0 || $recCounter > 0;
                } catch (\Throwable $e) {
                    $isAdded = false;
                }
            }
            $isRequested = isset($requestedCandidateIds) && in_array((int) @$row->id, $requestedCandidateIds, true);
        @endphp

        <article class="abf-cand-card" data-candidate-id="{{ @$row->id }}">
            <div class="abf-cand-grid">
                {{-- Sub-Card 1: Candidate Info --}}
                <div class="abf-cand-subcard abf-cand-subcard--info">
                    <div class="abf-subcard-head">
                        <span class="abf-subcard-label">
                            <span class="form-check form-check-sm form-check-custom form-check-solid me-1">
                                <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" value="{{ $row->id }}" />
                            </span>
                            Candidate Info
                        </span>
                        <span class="abf-type-badge">{{ $outsourcing_type }}</span>
                    </div>
                    <div class="abf-cand-info-row">
                        <a href="javascript:void(0);" data-row-id="{{ @$row->id }}" class="{{ $profile_sidebar_css }} abf-avatar {{ $bgTextDecore }}">
                            {{ strtoupper(@$strVal) }}
                        </a>
                        <div class="abf-cand-info-meta">
                            <a href="javascript:void(0);" data-row-id="{{ @$row->id }}" class="{{ $profile_sidebar_css }} abf-cand-name" title="{{ ucwords((string) @$row->full_name) }}">
                                {{ ucwords((string) @$row->full_name) }}
                            </a>
                            <div class="abf-cand-role" title="{{ ucwords((string) @$row->current_designation) }}">
                                {{ ucwords((string) @$row->current_designation) ?: '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 2: Experience --}}
                <div class="abf-cand-subcard">
                    <div class="abf-subcard-head">
                        <span class="abf-subcard-label">Experience</span>
                    </div>
                    <div>
                        <div class="abf-exp-main" title="Total: {{ $formattedExperience }} Y | Relevant: {{ $rel_formattedExperience }} Y">
                            <i class="fa-solid fa-briefcase me-1 text-muted" style="font-size:10px;"></i>{{ $formattedExperience }} Y Total
                        </div>
                        <div class="abf-exp-sub">{{ $rel_formattedExperience }} Y Relevant</div>
                    </div>
                </div>

                {{-- Sub-Card 3: Availability --}}
                <div class="abf-cand-subcard">
                    <div class="abf-subcard-head">
                        <span class="abf-subcard-label">Availability</span>
                    </div>
                    <div>
                        <div class="abf-avail-main" style="color: {{ $npColorStyle }};">
                            {{ $npLabel }}
                        </div>
                        <div class="abf-avail-sub" title="{{ @$row->np_id }}">
                            <i class="fa-regular fa-clock me-1"></i>{{ $npDate ?: '—' }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 4: Compensation --}}
                <div class="abf-cand-subcard">
                    <div class="abf-subcard-head">
                        <span class="abf-subcard-label">Compensation</span>
                    </div>
                    <div>
                        <div class="abf-sal-main" title="Expected: {{ $bill_rate_currency . $bill_rate_amount . '/' . $bill_rate_mode }}">
                            {{ $bill_rate_currency }}{{ $bill_rate_amount }}<span class="abf-sal-mode">/{{ $bill_rate_mode }}</span>
                        </div>
                        @if (!$hideCurrentCtc)
                            <div class="abf-sal-sub">Cur: {{ $pay_rate_currency }}{{ $pay_rate_amount }}/{{ $pay_rate_mode }}</div>
                        @endif
                        <span class="abf-outsource-pill">{{ $outsourcing_type }}</span>
                    </div>
                </div>

                {{-- Sub-Card 5: Location & Degree (With Restored Work Mode) --}}
                <div class="abf-cand-subcard">
                    <div class="abf-subcard-head">
                        <span class="abf-subcard-label">Location &amp; Degree</span>
                    </div>
                    <div>
                        <div class="abf-loc-city text-truncate" title="{{ ucwords((string) $row_city_name) }}, {{ strtoupper((string) $row_country_name) }}">
                            <i class="fa-solid fa-location-dot me-1"></i>{{ ucwords((string) $row_city_name) ?: '—' }}@if(!empty($row_country_name) && $row_country_name !== '----'), {{ strtoupper(mb_substr((string) $row_country_name, 0, 2)) }}@endif
                        </div>
                        <div class="abf-loc-mode text-truncate" style="font-size:11px; color:#475569; margin:2px 0;" title="{{ !empty($wmMeta['label']) ? e($wmMeta['label']) : 'Work Mode: –' }}">
                            <i class="{{ $wmIcon }} me-1 text-primary" style="font-size:10px;"></i><span>{{ $wmLabel }}</span>
                        </div>
                        <div class="abf-loc-edu abf-edu-uppercase text-truncate" style="font-size:11px;" title="{{ @$row->qualification_name }}">
                            <i class="fa-solid fa-graduation-cap me-1"></i>{{ $qualFull ?: '—' }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 6: Last Active & Action --}}
                <div class="abf-cand-subcard">
                    <div class="abf-subcard-head">
                        <span class="abf-subcard-label">Active &amp; Action</span>
                        <span class="fw-bold fs-8 {{ $statusColorClass }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="abf-action-wrap">
                        <div class="abf-active-date" title="Updated on {{ @$updated_at }}">
                            <i class="fa-regular fa-calendar me-1"></i>{{ @$updated_at ?: '—' }}
                        </div>
                        <div class="d-flex flex-column">
                            @if ($row->status == 2)
                                @if ($isAdded)
                                    <button type="button" class="btn btn-sm" disabled
                                        style="background-color:#059669;color:#fff;opacity:1;cursor:default;border:none;font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;gap:5px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Added
                                    </button>
                                @elseif (($row->countforadd ?? 0) >= 3)
                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger fs-7">Expired</a>
                                @else
                                    <a href="javascript:void(0);"
                                        class="btn btn-sm btn-primary add_to_inreview"
                                        style="font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;gap:5px;"
                                        data-row-source_from_type="2"
                                        data-row-requested="{{ $isRequested ? '1' : '0' }}"
                                        data-row-id="{{ @$row->id }}"
                                        data-row-jobid="{{ @$jobid }}">
                                        Add to Job
                                    </a>
                                @endif
                            @else
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger fs-7">Expired</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Intelligence & 3-Tier Skills Strip --}}
            <div class="abf-cand-bottom">
                <div class="abf-bottom-left">
                    <span class="abf-match-pill" title="Matched {{ $percentage }}% of required job skills"
                        style="background:{{ $matchBg }};color:{{ $matchColor }};cursor:help;">
                        Match {{ $percentage }}%
                    </span>
                    <div class="abf-skills-scroll">
                        @foreach ($mandatoryMatched as $sval)
                            <span class="abf-skill-pill is-mandatory" title="Matches a mandatory (starred) skill">
                                <span style="color:#eab308;">&#9733;</span> {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($optionalMatched as $sval)
                            <span class="abf-skill-pill is-matched" title="Matches a required skill">
                                {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($unmatchedSkillsArr as $sval)
                            <span class="abf-skill-pill">{{ ucfirst($sval) }}</span>
                        @endforeach
                        @if (empty($mandatoryMatched) && empty($optionalMatched) && empty($unmatchedSkillsArr))
                            <span class="abf-skills-empty">No skills listed</span>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    @endforeach
@else
    @if (count($boostbenchApplications ?? []) == 0 && count($data) == 0)
        <div class="abf-empty-card">
            <div class="abf-empty-icon">
                <i class="fa-solid fa-users-slash"></i>
            </div>
            @if (!empty($search ?? null))
                <div class="abf-empty-title">No agency bench talent matches &quot;{{ $search }}&quot;</div>
                <div class="abf-empty-sub">Clear or change the search text to see all partner agency bench candidates.</div>
            @elseif (!empty($hasActiveFilters ?? false))
                <div class="abf-empty-title">No agency bench talent matches your current filters</div>
                <div class="abf-empty-sub">Try widening or clearing a filter to see more results.</div>
            @else
                <div class="abf-empty-title">No agency bench talent found yet</div>
                <div class="abf-empty-sub">Partner agency bench candidates matching this role will appear here.</div>
            @endif
        </div>
    @endif
@endif
