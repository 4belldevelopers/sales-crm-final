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
    use Carbon\Carbon;

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

            $profile_sidebar_css = 'bench_show_application_detail';

            // 3-tier skill matching
            $skillArr = array_values(array_filter(array_map('trim', explode(',', (string) @$row->skills))));
            $targetJobId = $row->job_id ?? ($jobid ?? 0);
            $jobSkillsArg = $rowMaps['skills'][$targetJobId] ?? ($rowMaps['job_skills'] ?? null);
            if ($targetJobId > 0 || !empty($jobSkillsArg)) {
                $percentageString = GeneralMethod::skill_percentage($jobSkillsArg ?: $targetJobId, $skillArr);
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

            // Compensation
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
                    $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                    $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
                } else {
                    $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($convertedAmounEctc[$jobMode] ?? 0);
                    $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($convertedAmounCtc[$jobMode] ?? 0);
                }
                $pay_rate_amount = explode('.', (string) $pay_rate_amount)[0];
                $bill_rate_amount = explode('.', (string) $bill_rate_amount)[0];
            }
            $pay_rate_mode = @$salaryMode[@$jobMode] ?? '';
            $bill_rate_mode = @$salaryMode[@$jobMode] ?? '';

            // Availability / Notice Period
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
                $npDate = date('d-M-y', strtotime($originalDate));
                $today = date('Y-m-d');
                if (date('Y-m-d', strtotime($row->np_id)) < $today) {
                    $npDate = 'Immediate';
                    $npColorStyle = '#16a34a';
                } else {
                    $npDate = 'SNP LWD ' . $npDate;
                    $npColorStyle = '#7c3aed';
                }
            } elseif ($npModeIdx == 2 || $npDate === 'Immediate') {
                $npDate = 'Immediate';
                $npColorStyle = '#16a34a';
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

            // Status & Added Check
            $isAdded = isset($assignedCandidateIds) && in_array((int) @$row->id, $assignedCandidateIds, true);
            if (!$isAdded && !isset($assignedCandidateIds)) {
                try {
                    $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                        ->select('job_application_id')
                        ->where('add_job_wise_condidates.job_id', '=', $jobid)
                        ->where('add_job_wise_condidates.job_application_id', '=', @$row->id)
                        ->count();
                    $recCounter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_id', @$row->id)
                        ->where('source_from_type', 1)
                        ->where('source_jobid', @$jobid)
                        ->where('ac_status', 1)
                        ->count();
                    $isAdded = $existingCandidatesQuery > 0 || $recCounter > 0;
                } catch (\Throwable $e) {
                    $isAdded = false;
                }
            }

            $candId = (int) (@$row->id ?? @$row->candidate_id ?? 0);
            $hasResume = !empty($row->resume) || !empty($row->candidate_resume) || !empty($rowMaps['resume_map'][$candId] ?? null);
            $cardMeta = AppTrackerCardPresenter::extractCardMeta($row);
        @endphp

        <article class="ibf-cand-card" data-candidate-id="{{ @$row->id }}">
            <div class="ibf-cand-grid">
                {{-- Sub-Card 1: Candidate Info --}}
                <div class="ibf-cand-subcard ibf-cand-subcard--info">
                    <div class="ibf-subcard-head">
                        <span class="ibf-subcard-label">
                            <span class="form-check form-check-sm form-check-custom form-check-solid me-1">
                                <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" value="{{ $row->id }}" />
                            </span>
                            Candidate Info
                        </span>
                        <span class="ibf-active-pill" style="padding:1px 6px;font-size:9.5px;">In-house Bench</span>
                    </div>
                    <div class="ibf-cand-info-row">
                        <a href="javascript:void(0);" data-row-id="{{ @$row->id }}" class="{{ $profile_sidebar_css }} ibf-avatar {{ $bgTextDecore }}">
                            {{ strtoupper(@$strVal) }}
                        </a>
                        <div class="ibf-cand-info-meta">
                            <a href="javascript:void(0);" data-row-id="{{ @$row->id }}" class="{{ $profile_sidebar_css }} ibf-cand-name" title="{{ ucwords((string) @$row->full_name) }}">
                                {{ ucwords((string) @$row->full_name) }}
                            </a>
                            <div class="ibf-cand-role" title="{{ ucwords((string) @$row->current_designation) }}">
                                {{ ucwords((string) @$row->current_designation) ?: '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 2: Compensation & Exp --}}
                <div class="ibf-cand-subcard">
                    <div class="ibf-subcard-head">
                        <span class="ibf-subcard-label">Compensation &amp; Exp</span>
                    </div>
                    <div>
                        <div class="ibf-sal-main" title="Expected: {{ $bill_rate_currency . $bill_rate_amount . '/' . $bill_rate_mode }}">
                            {{ $bill_rate_currency }}{{ $bill_rate_amount }}<span class="ibf-sal-mode">/{{ $bill_rate_mode }}</span>
                        </div>
                        <div class="ibf-sal-sub">Cur: {{ $pay_rate_currency }}{{ $pay_rate_amount }}/{{ $pay_rate_mode }}</div>
                    </div>
                    <div class="mt-1">
                        <div class="ibf-exp-main" title="Total: {{ $formattedExperience }} Y | Relevant: {{ $rel_formattedExperience }} Y">
                            <i class="fa-solid fa-briefcase me-1 text-muted" style="font-size:10px;"></i>{{ $formattedExperience }} Y Total
                        </div>
                        <div class="ibf-exp-sub">{{ $rel_formattedExperience }} Y Relevant</div>
                    </div>
                </div>

                {{-- Sub-Card 3: Availability --}}
                <div class="ibf-cand-subcard">
                    <div class="ibf-subcard-head">
                        <span class="ibf-subcard-label">Availability</span>
                    </div>
                    <div>
                        <div class="ibf-avail-main" style="color: {{ $npColorStyle }};">
                            {{ $npLabel }}
                        </div>
                        <div class="ibf-avail-sub" title="{{ @$row->np_id }}">
                            <i class="fa-regular fa-clock me-1"></i>{{ $npDate ?: '—' }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 4: Location & Degree (With Restored Work Mode) --}}
                <div class="ibf-cand-subcard">
                    <div class="ibf-subcard-head">
                        <span class="ibf-subcard-label">Location &amp; Degree</span>
                    </div>
                    <div>
                        <div class="ibf-loc-city text-truncate" title="{{ ucwords((string) $row_city_name) }}, {{ strtoupper((string) $row_country_name) }}">
                            <i class="fa-solid fa-location-dot me-1"></i>{{ ucwords((string) $row_city_name) ?: '—' }}@if(!empty($row_country_name) && $row_country_name !== '----'), {{ strtoupper(mb_substr((string) $row_country_name, 0, 2)) }}@endif
                        </div>
                        <div class="ibf-loc-mode text-truncate" style="font-size:11px; color:#475569; margin:2px 0;" title="{{ !empty($wmMeta['label']) ? e($wmMeta['label']) : 'Work Mode: –' }}">
                            <i class="{{ $wmIcon }} me-1 text-primary" style="font-size:10px;"></i><span>{{ $wmLabel }}</span>
                        </div>
                        <div class="ibf-loc-edu text-truncate" style="text-transform:uppercase; font-size:11px;" title="{{ @$row->qualification_name }}">
                            <i class="fa-solid fa-graduation-cap me-1"></i>{{ $qualFull ?: '—' }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 5: Last Active --}}
                <div class="ibf-cand-subcard">
                    <div class="ibf-subcard-head">
                        <span class="ibf-subcard-label">Last Active</span>
                    </div>
                    <div>
                        <div class="ibf-active-date" title="Updated on {{ @$updated_at }}">
                            <i class="fa-regular fa-calendar me-1"></i>{{ @$updated_at ?: '—' }}
                        </div>
                        <div class="ibf-avail-sub mt-1">
                            <i class="fa-solid fa-layer-group me-1"></i>Internal Bench
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 6: Status & Action --}}
                <div class="ibf-cand-subcard">
                    <div class="ibf-subcard-head">
                        <span class="ibf-subcard-label">Status &amp; Action</span>
                    </div>
                    <div class="ibf-action-wrap">
                        @if ((@$row->status ?? 0) == 2)
                            <div class="d-flex flex-column">
                                @if ($isAdded)
                                    <button type="button" class="btn btn-sm" disabled
                                        style="background-color:#059669;color:#fff;opacity:1;cursor:default;border:none;font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;gap:5px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Added
                                    </button>
                                @else
                                    <a href="javascript:void(0);"
                                        class="btn btn-sm btn-primary add_to_inreview"
                                        style="font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;gap:5px;"
                                        data-row-source_from_type="1"
                                        data-row-id="{{ @$row->id }}"
                                        data-row-jobid="{{ @$jobid }}">
                                        Add to Job
                                    </a>
                                @endif
                            </div>
                        @elseif ((@$row->status ?? 0) == 1)
                            <a href="javascript:void(0);" data-row-id="985"
                                class="ibf-status-pill stage_sub_stage interview text-hover-primary"
                                data-toggle="modal" data-target="#stage_sub_stage">Pool</a>
                        @elseif ((@$row->status ?? 0) == 4)
                            <a href="javascript:void(0);" data-row-id="985"
                                class="ibf-status-pill stage_sub_stage hold-bg text-hover-warning"
                                data-toggle="modal" data-target="#stage_sub_stage">Pause</a>
                        @elseif ((@$row->status ?? 0) == 3)
                            <a href="javascript:void(0);" data-row-id="985"
                                class="ibf-status-pill stage_sub_stage active-bg text-hover-success"
                                data-toggle="modal" data-target="#stage_sub_stage">Placed</a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bottom Intelligence & 3-Tier Skills Strip --}}
            <div class="ibf-cand-bottom">
                <div class="ibf-bottom-left">
                    <span class="ibf-match-pill" title="Matched {{ $percentage }}% of required job skills"
                        style="background:{{ $matchBg }};color:{{ $matchColor }};cursor:help;">
                        Match {{ $percentage }}%
                    </span>
                    <div class="ibf-skills-scroll">
                        @foreach ($mandatoryMatched as $sval)
                            <span class="ibf-skill-pill is-mandatory" title="Matches a mandatory (starred) skill">
                                <span style="color:#eab308;">&#9733;</span> {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($optionalMatched as $sval)
                            <span class="ibf-skill-pill is-matched" title="Matches a required skill">
                                {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($unmatchedSkillsArr as $sval)
                            <span class="ibf-skill-pill">{{ ucfirst($sval) }}</span>
                        @endforeach
                        @if (empty($mandatoryMatched) && empty($optionalMatched) && empty($unmatchedSkillsArr))
                            <span class="ibf-skills-empty">No skills listed</span>
                        @endif
                    </div>
                </div>
                <div class="ibf-bottom-right">
                    @if ($hasResume)
                        <span class="ibf-resume-pill jw-open-resume-tab cursor-pointer"
                              data-row-id="{{ $candId }}"
                              data-open-tab="resume_cv"
                              title="Click to view Resume / CV">
                            <i class="fas fa-check-circle"></i><span>Resume</span>
                        </span>
                    @else
                        <span class="ibf-nocv-pill" title="Resume missing">
                            <i class="fas fa-exclamation-circle"></i><span>No CV</span>
                        </span>
                    @endif
                    @if ($cardMeta['visa_display'] !== '')
                        <span class="ibf-visa-pill" title="{{ e('Visa Status: ' . $cardMeta['visa_raw']) }}">
                            <i class="fas fa-passport"></i><span>{{ $cardMeta['visa_display'] }}</span>
                        </span>
                    @endif
                    @if ($cardMeta['linkedin_url'] !== '')
                        <a href="{{ e($cardMeta['linkedin_url']) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="ibf-linkedin-pill"
                           title="LinkedIn Profile Verified">
                            <i class="fab fa-linkedin"></i><span>LinkedIn Verified</span>
                        </a>
                    @endif
                </div>
            </div>
        </article>
    @endforeach
@else
    <div class="ibf-empty-card">
        <div class="ibf-empty-icon">
            <i class="fa-solid fa-users-slash"></i>
        </div>
        @if (!empty($search ?? null))
            <div class="ibf-empty-title">No bench talent matches &quot;{{ $search }}&quot;</div>
            <div class="ibf-empty-sub">Clear or change the search text to see all matching bench candidates.</div>
        @elseif (!empty($hasActiveFilters ?? false))
            <div class="ibf-empty-title">No bench talent matches your current filters</div>
            <div class="ibf-empty-sub">Try widening or clearing a filter to see more results.</div>
        @else
            <div class="ibf-empty-title">No in-house bench talent found yet</div>
            <div class="ibf-empty-sub">Available internal bench candidates matching this role will appear here.</div>
        @endif
    </div>
@endif
