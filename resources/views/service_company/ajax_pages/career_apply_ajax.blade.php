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

            $profile_sidebar_css = 'website-show-application-detail';

            // 3-tier skill matching
            $skillArr = is_array($row->matched_skills ?? null) ? $row->matched_skills : [];
            $allCandidateSkills = array_values(array_filter(array_map('trim', explode(',', (string) @$row->skills))));
            $targetJobId = $row->job_id ?? ($jobid ?? 0);
            $jobSkillsArg = $rowMaps['skills'][$targetJobId] ?? ($rowMaps['job_skills'] ?? null);
            if ($targetJobId > 0 || !empty($jobSkillsArg)) {
                $percentageString = GeneralMethod::skill_percentage($jobSkillsArg ?: $targetJobId, $allCandidateSkills);
                $percentage = (int) round((float) rtrim($percentageString ?: '0%', '%'));
            } else {
                $matches = $row->matched_skill_count ?? 0;
                $totalElements = count($requiredSkillsForDisplay ?? []);
                $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
                $percentage = (int) round(min($matchPercentage, 100));
            }
            $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
            $matchBg = $matchStyle['bg'];
            $matchColor = $matchStyle['color'];

            $mandatoryMatched = $row->matched_mandatory_skills ?? [];
            $optionalMatched = array_values(array_udiff($skillArr, $mandatoryMatched, 'strcasecmp'));
            $unmatchedSkillsArr = array_values(array_udiff($allCandidateSkills, $skillArr, 'strcasecmp'));

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

            // Compensation (preserve exact career_apply_ajax logic)
            $plainCurrencySymbol = CurrencyService::resolvePlainSymbolFromLegacy(@$jobCurrency, $currencySymbole);
            $pay_rate_currency = $plainCurrencySymbol;
            $bill_rate_currency = $plainCurrencySymbol;
            $pay_rate_amount = '0';
            $bill_rate_amount = '0';
            if ((@$row->ectc_yearly ?? 0) > 0 || (@$row->ctc_yearly ?? 0) > 0) {
                $currencymain = GeneralMethod::currency();
                $currencyCode = $currencymain[@$jobCurrency] ?? 'INR';
                $convertedAmounEctc = GeneralMethod::conversionRates($currencyCode, $row->ectc_yearly, 0);
                $convertedAmounCtc = GeneralMethod::conversionRates($currencyCode, $row->ctc_yearly, 0);
                if (@$jobCurrency == 0) {
                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly, $symbols, $jobMode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $symbols, $jobMode);
                    $pay_rate_amount = explode('.', (string) GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount))[0];
                    $bill_rate_amount = explode('.', (string) GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount))[0];
                } else {
                    $effective_sal_mode = ($sal_mode !== '' && $sal_mode !== null) ? $sal_mode : 0;
                    $bill_rate_amount = explode('.', (string) GeneralMethod::thousandsCurrencyFormat($convertedAmounEctc[$effective_sal_mode] ?? 0))[0];
                    $pay_rate_amount = explode('.', (string) GeneralMethod::thousandsCurrencyFormat($convertedAmounCtc[$effective_sal_mode] ?? 0))[0];
                }
            }
            $pay_rate_mode = @$salaryMode[@$jobMode] ?? '';
            $bill_rate_mode = @$salaryMode[@$jobMode] ?? '';

            // Availability / Notice period
            $npArr = ['Notice period', 'Available from', 'Immediate'];
            $npModeIdx = (int) (@$row->serving_notice_period_mode ?? 0);
            $npLabel = $npArr[$npModeIdx] ?? 'Notice period';
            $npDate = $row->np_id ?? '';
            if ($npModeIdx == 0) {
                $npDate = ($row->np_id ?? '0') . ((int) ($row->np_id ?? 0) === 1 ? ' Day' : ' Days');
            } elseif ($npModeIdx == 1) {
                $npDate = date('d-M-y', strtotime($row->np_id));
                $today = date('Y-m-d');
                if (date('Y-m-d', strtotime($row->np_id)) < $today) {
                    $npDate = 'Immediate';
                    $npLabel = 'Immediate';
                }
            }

            if ($npLabel === 'Immediate') {
                $npColor = '#16a34a';
            } elseif ($npModeIdx == 0) {
                $npColor = '#d97706';
            } elseif ($npModeIdx == 1) {
                $npColor = '#7c3aed';
                $npDate = 'SNP LWD ' . $npDate;
            } else {
                $npColor = '#181c32';
            }

            // Location & Qualification
            $row_country_name = @$row->country_name;
            $row_city_name = @$row->city_name;
            $qualFull = mb_strtoupper((string) @$row->qualification_name);
            $wmMeta = AppTrackerCardPresenter::resolveWorkMode($row);
            $wmIcon = !empty($wmMeta['icon']) ? $wmMeta['icon'] : 'fa-solid fa-building';
            $wmLabel = !empty($wmMeta['label']) ? $wmMeta['label'] : '–';

            // Last Active
            $updated_at = !empty($row->updated_at) ? GeneralMethod::ymdTodmyNotTime($row->updated_at) : '—';

            // Added state
            $candId = (int) (@$row->id ?? 0);
            $isAdded = isset($assignedCandidateIds) && in_array($candId, $assignedCandidateIds, true);
            if (!$isAdded && !isset($assignedCandidateIds)) {
                try {
                    $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                        ->select('job_application_id')
                        ->where('add_job_wise_condidates.job_id', '=', $jobid)
                        ->where('add_job_wise_condidates.job_application_id', '=', $candId)
                        ->count();
                    $recCounter = DB::table('rec_candidate_assign')
                        ->where('source_jobid', '=', $jobid)
                        ->where(function ($q) use ($candId) {
                            $q->where('source_from_id', $candId)
                              ->orWhere('source_application_id', $candId);
                        })
                        ->where('ac_status', 1)
                        ->count();
                    $isAdded = $existingCandidatesQuery > 0 || $recCounter > 0;
                } catch (\Throwable $e) {
                    $isAdded = false;
                }
            }
        @endphp

        <article class="wcf-cand-card" data-candidate-id="{{ @$row->id }}">
            <div class="wcf-cand-grid">
                {{-- Sub-Card 1: Candidate Info --}}
                <div class="wcf-cand-subcard wcf-cand-subcard--info">
                    <div class="wcf-subcard-head">
                        <span class="wcf-subcard-label">
                            <span class="form-check form-check-sm form-check-custom form-check-solid me-1">
                                <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" value="{{ $row->id }}" />
                            </span>
                            Candidate Info
                        </span>
                        <span class="wcf-career-badge">Career Page</span>
                    </div>
                    <div class="wcf-cand-info-row">
                        <a href="javascript:void(0);" data-row-id="{{ @$row->id }}" class="{{ $profile_sidebar_css }} wcf-avatar {{ $bgTextDecore }}">
                            {{ strtoupper(@$strVal) }}
                        </a>
                        <div class="wcf-cand-info-meta">
                            <a href="javascript:void(0);" data-row-id="{{ @$row->id }}" class="{{ $profile_sidebar_css }} wcf-cand-name" title="{{ ucwords((string) @$row->full_name) }}">
                                {{ ucwords((string) @$row->full_name) }}
                            </a>
                            <div class="wcf-cand-role" title="{{ ucwords((string) @$row->current_designation) }}">
                                {{ ucwords((string) @$row->current_designation) ?: '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 2: Compensation & Exp --}}
                <div class="wcf-cand-subcard">
                    <div class="wcf-subcard-head">
                        <span class="wcf-subcard-label">Compensation &amp; Exp</span>
                    </div>
                    <div>
                        <div class="wcf-sal-main" title="Expected: {{ $bill_rate_currency . $bill_rate_amount . '/' . $bill_rate_mode }}">
                            {{ $bill_rate_currency }}{{ $bill_rate_amount }}<span class="wcf-sal-mode">/{{ $bill_rate_mode }}</span>
                        </div>
                        <div class="wcf-sal-sub">Cur: {{ $pay_rate_currency }}{{ $pay_rate_amount }}/{{ $pay_rate_mode }}</div>
                    </div>
                    <div>
                        <div class="wcf-exp-main" title="Total: {{ $formattedExperience }} Y | Relevant: {{ $rel_formattedExperience }} Y">
                            <i class="fa-solid fa-briefcase me-1 text-muted" style="font-size:10px;"></i>{{ $formattedExperience }} Y Total
                        </div>
                        <div class="wcf-exp-sub">{{ $rel_formattedExperience }} Y Relevant</div>
                    </div>
                </div>

                {{-- Sub-Card 3: Availability --}}
                <div class="wcf-cand-subcard">
                    <div class="wcf-subcard-head">
                        <span class="wcf-subcard-label">Availability</span>
                    </div>
                    <div>
                        <div class="wcf-avail-main" style="color: {{ $npColor }};">
                            {{ $npLabel }}
                        </div>
                        <div class="wcf-avail-sub" title="{{ @$row->np_id }}">
                            <i class="fa-regular fa-clock me-1"></i>{{ $npDate ?: '—' }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 4: Location & Degree (With Restored Work Mode) --}}
                <div class="wcf-cand-subcard">
                    <div class="wcf-subcard-head">
                        <span class="wcf-subcard-label">Location &amp; Degree</span>
                    </div>
                    <div>
                        <div class="wcf-loc-city text-truncate" title="{{ ucwords((string) $row_city_name) }}, {{ strtoupper((string) $row_country_name) }}">
                            <i class="fa-solid fa-location-dot me-1"></i>{{ ucwords((string) $row_city_name) ?: '—' }}@if(!empty($row_country_name) && $row_country_name !== '----'), {{ strtoupper(mb_substr((string) $row_country_name, 0, 2)) }}@endif
                        </div>
                        <div class="wcf-loc-mode text-truncate" style="font-size:11px; color:#475569; margin:2px 0;" title="{{ !empty($wmMeta['label']) ? e($wmMeta['label']) : 'Work Mode: –' }}">
                            <i class="{{ $wmIcon }} me-1 text-primary" style="font-size:10px;"></i><span>{{ $wmLabel }}</span>
                        </div>
                        <div class="wcf-loc-edu wcf-edu-uppercase text-truncate" style="font-size:11px;" title="{{ @$row->qualification_name }}">
                            <i class="fa-solid fa-graduation-cap me-1"></i>{{ $qualFull ?: '—' }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 5: Applied / Updated --}}
                <div class="wcf-cand-subcard">
                    <div class="wcf-subcard-head">
                        <span class="wcf-subcard-label">Applied On</span>
                    </div>
                    <div>
                        <div class="wcf-active-date" title="Updated on {{ @$updated_at }}">
                            <i class="fa-regular fa-calendar me-1"></i>{{ @$updated_at ?: '—' }}
                        </div>
                        <div class="wcf-avail-sub">
                            <i class="fa-solid fa-globe me-1"></i>Direct Applicant
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 6: Action --}}
                <div class="wcf-cand-subcard">
                    <div class="wcf-subcard-head">
                        <span class="wcf-subcard-label">Action</span>
                    </div>
                    <div class="wcf-action-wrap">
                        <div class="d-flex flex-column">
                            @if ($isAdded)
                                <button type="button" class="btn btn-sm" disabled
                                    style="background-color:#059669;color:#fff;opacity:1;cursor:default;border:none;font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;gap:5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Added
                                </button>
                                @if (!empty(@$row->posted_by))
                                    <span class="wcf-added-by" title="{{ @$row->posted_by }}">{{ @$row->posted_by }}</span>
                                @endif
                            @else
                                <a href="javascript:void(0);"
                                    class="btn btn-sm btn-primary add_to_inreview"
                                    style="font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;gap:5px;"
                                    data-row-source_from_type="3"
                                    data-row-id="{{ @$row->id }}"
                                    data-row-jobid="{{ @$jobid }}">
                                    Add to Job
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Intelligence & 3-Tier Skills Strip --}}
            <div class="wcf-cand-bottom">
                <div class="wcf-bottom-left">
                    <span class="wcf-match-pill" title="Matched {{ $percentage }}% of required job skills"
                        style="background:{{ $matchBg }};color:{{ $matchColor }};cursor:help;">
                        Match {{ $percentage }}%
                    </span>
                    <div class="wcf-skills-scroll">
                        @foreach ($mandatoryMatched as $sval)
                            <span class="wcf-skill-pill is-mandatory" title="Matches a mandatory (starred) skill">
                                <span style="color:#eab308;">&#9733;</span> {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($optionalMatched as $sval)
                            <span class="wcf-skill-pill is-matched" title="Matches a required skill">
                                {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($unmatchedSkillsArr as $sval)
                            <span class="wcf-skill-pill">{{ ucfirst($sval) }}</span>
                        @endforeach
                        @if (empty($mandatoryMatched) && empty($optionalMatched) && empty($unmatchedSkillsArr))
                            <span class="wcf-skills-empty">No skills listed</span>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    @endforeach
@else
    <div class="wcf-empty-card">
        <div class="wcf-empty-icon">
            <i class="fa-solid fa-users-slash"></i>
        </div>
        @if (!empty($search ?? null))
            <div class="wcf-empty-title">No career page applicants match &quot;{{ $search }}&quot;</div>
            <div class="wcf-empty-sub">Clear or change the search text to see all direct applicants.</div>
        @elseif (!empty($hasActiveFilters ?? false))
            <div class="wcf-empty-title">No career page applicants match your current filters</div>
            <div class="wcf-empty-sub">Try widening or clearing a filter to see more results.</div>
        @else
            <div class="wcf-empty-title">No career page applicants found yet</div>
            <div class="wcf-empty-sub">Candidates who apply via your career page will appear here.</div>
        @endif
    </div>
@endif
