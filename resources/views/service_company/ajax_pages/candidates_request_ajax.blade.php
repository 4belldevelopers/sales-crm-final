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
    $job_status = GeneralMethod::job_status();

    $benchExpireDays = cache()->remember('feature:expire_days:4', 86400, function () {
        try {
            $benchExpireFeature = DB::table('features')->where('id', 4)->first();
            return (int) (@$benchExpireFeature->days ?: 30);
        } catch (\Throwable $e) {
            return 30;
        }
    });
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $row->id = $row->candidate_id;
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

            $duplicate = $row->duplicate ?? 0;
            $flag = ($duplicate == 1) ? 1 : 0;
            $profile_sidebar_css = 'pbench_show_application_detail';

            // 3-tier skill matching
            $skillArr = array_values(array_filter(array_map('trim', explode(',', (string) @$row->skills))));
            $targetJobId = (!empty($jobid) && (int) $jobid > 0)
                ? (int) $jobid
                : (int) (@$row->job_id ?: (@$row->source_jobid ?: session()->get('job_id')));
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
            $current_ctc_cur = $plainCurrencySymbol;
            $current_ectc_cur = $plainCurrencySymbol;
            $ctc = '0';
            $ectc = '0';
            if ((@$row->ctc_yearly ?? 0) > 0 || (@$row->ectc_yearly ?? 0) > 0) {
                $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly, $symbols, $jobMode);
                $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $symbols, $jobMode);
                $ctc = explode('.', (string) GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount))[0];
                $ectc = explode('.', (string) GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount))[0];
            }
            $current_ctc_mode = @$salaryMode[@$jobMode] ?? '';
            $current_ectc_mode = @$salaryMode[@$jobMode] ?? '';
            $outsourcing_type = ($row->outsourcing_type ?? 0) == 1 ? 'Fulltime' : 'Contract';

            // Notice period
            $np = $row->np_id ?? '';
            if (empty($np)) {
                $np = 'N/A';
            }
            $npColorStyle = '#181c32';
            if ($row->serving_notice_period_mode == 0) {
                if (!empty($row->np_id)) {
                    $np = $row->np_id . ' Days';
                }
                $npColorStyle = '#d97706';
            } elseif ($row->serving_notice_period_mode == 1) {
                if ($np !== 'Immediate') {
                    $np = date('d-M-Y', strtotime($row->np_id));
                    $today = date('Y-m-d');
                    if (date('Y-m-d', strtotime($row->np_id)) < $today) {
                        $np = 'Immediate';
                    }
                }
                if ($np === 'Immediate') {
                    $npColorStyle = '#16a34a';
                } else {
                    $np = 'SNP LWD ' . $np;
                    $npColorStyle = '#7c3aed';
                }
            } elseif ($np === 'Immediate') {
                $npColorStyle = '#16a34a';
            }

            // Location
            $row_country_name = !empty(@$row->country_name) ? ucwords(@$row->country_name) : '';
            if (empty($row_country_name)) {
                if (empty($row->country)) {
                    $row_country_name = '----';
                } elseif (is_numeric(@$row->country)) {
                    try {
                        $country_name_Arr = GeneralMethod::getCountryName(@$row->country);
                        foreach ($country_name_Arr as $cval) {
                            $row_country_name = ucwords($cval['name']);
                        }
                    } catch (\Throwable $e) {
                        $row_country_name = '----';
                    }
                } else {
                    $row_country_name = @$row->country;
                }
            }

            $row_state_name = @$row->state_name ?: '';
            if (empty($row_state_name)) {
                if (empty($row->state)) {
                    $row_state_name = '----';
                } elseif (@$row->country == 101) {
                    $row_state_name = @$row->state_name ?: '----';
                }
                $state = @$row->state;
                if (is_numeric(@$row->country) && is_numeric($state) && $state > 0) {
                    try {
                        $state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
                        foreach ($state_name_Arr as $sval) {
                            $row_state_name = ucwords($sval['name']);
                        }
                    } catch (\Throwable $e) {}
                }
            }

            $row_city_name = '';
            if (!empty($row->city_name)) {
                $row_city_name = $row->city_name;
            } elseif (@$row->country == 101 && !empty($row->city_id)) {
                $row_city_name = cache()->remember('city_name:' . (int) $row->city_id, 86400, function () use ($row) {
                    $row_city = DB::table('city_list')->select('city')->where('city_id', (int) $row->city_id)->first();
                    return $row_city ? $row_city->city : '';
                });
            } else {
                $row_city_name = @$row->city ?: '';
            }
            $qualFull = mb_strtoupper((string) @$row->qualification_name);
            $wmMeta = AppTrackerCardPresenter::resolveWorkMode($row);
            $wmIcon = !empty($wmMeta['icon']) ? $wmMeta['icon'] : 'fa-solid fa-building';
            $wmLabel = !empty($wmMeta['label']) ? $wmMeta['label'] : '–';

            // Last Active & Commission & Request Expiry
            $dateupdate = !empty($row->updated_at) ? GeneralMethod::ymdTodmyNotTime($row->updated_at) : '—';
            $isFree = ($row->comission_type ?? 0) == 1;
            $comission_type = $isFree ? 'Free' : 'Paid';

            $expireDate = '';
            $expireColor = '#d97706';
            if (!empty($row->created_at)) {
                $expireTs = strtotime("+{$benchExpireDays} days", strtotime($row->created_at));
                $expireDate = date('d M y', $expireTs);
                $expireColor = ($expireTs < time() || ($row->ac_status ?? 0) == 2) ? '#dc2626' : '#d97706';
            }

            $isAdded = (($row->ac_status ?? 0) == 1) || (isset($assignedCandidateIds) && in_array((int) @$row->candidate_id, $assignedCandidateIds, true));
        @endphp

        <article class="brf-cand-card {{ $flag == 1 ? 'is-duplicate' : '' }}" data-candidate-id="{{ @$row->id }}">
            <div class="brf-cand-grid">
                {{-- Sub-Card 1: Candidate Info --}}
                <div class="brf-cand-subcard brf-cand-subcard--info">
                    <div class="brf-subcard-head">
                        <span class="brf-subcard-label">
                            <span class="form-check form-check-sm form-check-custom form-check-solid me-1">
                                <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" value="{{ @$row->id }}" />
                            </span>
                            Candidate Info
                        </span>
                        @if ($flag == 1)
                            <span class="brf-dup-pill" title="Duplicate candidate detected">
                                <i class="fa-solid fa-clone"></i> Duplicate
                            </span>
                        @else
                            <span class="brf-comm-pill {{ $isFree ? 'is-free' : 'is-paid' }}">{{ $comission_type }}</span>
                        @endif
                    </div>
                    <div class="brf-cand-info-row">
                        <a href="javascript:void(0);" data-row-id="{{ @$row->id }}" class="{{ $profile_sidebar_css }} brf-avatar {{ $bgTextDecore }}">
                            {{ strtoupper(@$strVal) }}
                        </a>
                        <div class="brf-cand-info-meta">
                            <a href="javascript:void(0);" data-row-id="{{ @$row->id }}" class="{{ $profile_sidebar_css }} brf-cand-name {{ $flag == 1 ? 'text-danger' : '' }}" title="{{ ucwords((string) @$row->full_name) }}">
                                {{ ucwords((string) @$row->full_name) }}
                            </a>
                            <div class="brf-cand-role" title="{{ ucwords((string) @$row->current_designation) }}">
                                {{ ucwords((string) @$row->current_designation) ?: '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 2: Experience --}}
                <div class="brf-cand-subcard">
                    <div class="brf-subcard-head">
                        <span class="brf-subcard-label">Experience</span>
                    </div>
                    <div>
                        <div class="brf-exp-main" title="Total: {{ $formattedExperience }} Y | Relevant: {{ $rel_formattedExperience }} Y">
                            <i class="fa-solid fa-briefcase me-1 text-muted" style="font-size:10px;"></i>{{ $formattedExperience }} Y Total
                        </div>
                        <div class="brf-exp-sub">{{ $rel_formattedExperience }} Y Relevant</div>
                    </div>
                </div>

                {{-- Sub-Card 3: Compensation & Mode --}}
                <div class="brf-cand-subcard">
                    <div class="brf-subcard-head">
                        <span class="brf-subcard-label">Compensation</span>
                    </div>
                    <div>
                        <div class="brf-sal-main" title="Expected: {{ $current_ectc_cur . $ectc . '/' . $current_ectc_mode }}">
                            {{ $current_ectc_cur }}{{ $ectc }}<span class="brf-sal-mode">/{{ $current_ectc_mode }}</span>
                        </div>
                        <div class="brf-sal-sub">Cur: {{ $current_ctc_cur }}{{ $ctc }}/{{ $current_ctc_mode }}</div>
                        <span class="brf-outsource-pill">{{ $outsourcing_type }}</span>
                    </div>
                </div>

                {{-- Sub-Card 4: Notice & Employer --}}
                <div class="brf-cand-subcard">
                    <div class="brf-subcard-head">
                        <span class="brf-subcard-label">Notice &amp; Employer</span>
                    </div>
                    <div>
                        <div class="brf-avail-main" style="color: {{ $npColorStyle }};">
                            {{ $np }}
                        </div>
                        <div class="brf-avail-sub" title="{{ ucwords((string) @$row->current_company) }}">
                            <i class="fa-regular fa-building me-1"></i>{{ ucwords((string) @$row->current_company) ?: '—' }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 5: Location & Degree (With Restored Work Mode) --}}
                <div class="brf-cand-subcard">
                    <div class="brf-subcard-head">
                        <span class="brf-subcard-label">Location &amp; Degree</span>
                    </div>
                    <div>
                        <div class="brf-loc-city text-truncate" title="{{ ucwords((string) $row_city_name) }}, {{ strtoupper((string) $row_country_name) }}">
                            <i class="fa-solid fa-location-dot me-1"></i>{{ ucwords((string) $row_city_name) ?: '—' }}@if(!empty($row_country_name) && $row_country_name !== '----'), {{ strtoupper(mb_substr((string) $row_country_name, 0, 2)) }}@endif
                        </div>
                        <div class="brf-loc-mode text-truncate" style="font-size:11px; color:#475569; margin:2px 0;" title="{{ !empty($wmMeta['label']) ? e($wmMeta['label']) : 'Work Mode: –' }}">
                            <i class="{{ $wmIcon }} me-1 text-primary" style="font-size:10px;"></i><span>{{ $wmLabel }}</span>
                        </div>
                        <div class="brf-loc-edu brf-edu-uppercase text-truncate" style="font-size:11px;" title="{{ @$row->qualification_name }}">
                            <i class="fa-solid fa-graduation-cap me-1"></i>{{ $qualFull ?: '—' }}
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 6: Last Active & Action --}}
                <div class="brf-cand-subcard">
                    <div class="brf-subcard-head">
                        <span class="brf-subcard-label">Request &amp; Action</span>
                        <span class="brf-comm-pill {{ $isFree ? 'is-free' : 'is-paid' }}">{{ $comission_type }}</span>
                    </div>
                    <div class="brf-action-wrap">
                        <div class="brf-active-date" title="Updated on {{ @$dateupdate }}">
                            <i class="fa-regular fa-calendar me-1"></i>{{ @$dateupdate ?: '—' }}
                        </div>
                        <div class="d-flex flex-column">
                            @if (($row->ac_status ?? 0) == 2)
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger" style="font-size:12px;font-weight:600;padding:5px 12px;border-radius:6px;">Expired</a>
                            @elseif (($row->ac_status ?? 0) == 0)
                                <a href="javascript:void(0);"
                                    data-row-id="{{ $targetJobId }}"
                                    data-recomnded-id="{{ @$row->rec_candidate_assign_id }}"
                                    data-condidate-id="{{ @$row->id }}"
                                    class="btn btn-sm btn-primary add_to_list"
                                    style="font-size:12px;font-weight:600;padding:5px 12px;border-radius:6px;">Add to job</a>
                            @elseif ($isAdded)
                                <button type="button" class="btn btn-sm" disabled
                                    style="background-color:#059669;color:#fff;opacity:1;cursor:default;border:none;font-size:12px;font-weight:600;padding:5px 12px;border-radius:6px;">Added</button>
                            @else
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger" style="font-size:12px;font-weight:600;padding:5px 12px;border-radius:6px;">Removed</a>
                            @endif
                            @if (!empty($expireDate))
                                <span class="brf-expire-txt mt-1 text-center" style="color: {{ $expireColor }};"
                                    title="Request expires {{ $benchExpireDays }} days after being sent">
                                    Exp: {{ $expireDate }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Intelligence & 3-Tier Skills Strip --}}
            <div class="brf-cand-bottom">
                <div class="brf-bottom-left">
                    <span class="brf-match-pill" title="Matched {{ $percentage }}% of required job skills"
                        style="background:{{ $matchBg }};color:{{ $matchColor }};cursor:help;">
                        Match {{ $percentage }}%
                    </span>
                    <div class="brf-skills-scroll">
                        @foreach ($mandatoryMatched as $sval)
                            <span class="brf-skill-pill is-mandatory" title="Matches a mandatory (starred) skill">
                                <span style="color:#eab308;">&#9733;</span> {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($optionalMatched as $sval)
                            <span class="brf-skill-pill is-matched" title="Matches a required skill">
                                {{ ucfirst($sval) }}
                            </span>
                        @endforeach
                        @foreach ($unmatchedSkillsArr as $sval)
                            <span class="brf-skill-pill">{{ ucfirst($sval) }}</span>
                        @endforeach
                        @if (empty($mandatoryMatched) && empty($optionalMatched) && empty($unmatchedSkillsArr))
                            <span class="brf-skills-empty">No skills listed</span>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    @endforeach
@else
    <div class="brf-empty-card">
        <div class="brf-empty-icon">
            <i class="fa-solid fa-users-slash"></i>
        </div>
        @if (!empty($search ?? null))
            <div class="brf-empty-title">No talent requests match &quot;{{ $search }}&quot;</div>
            <div class="brf-empty-sub">Clear or change the search text to see all bench talent requests.</div>
        @elseif (!empty($hasActiveFilters ?? false))
            <div class="brf-empty-title">No talent requests match your current filters</div>
            <div class="brf-empty-sub">Try widening or clearing a filter to see more results.</div>
        @else
            <div class="brf-empty-title">No bench talent requests found yet</div>
            <div class="brf-empty-sub">Incoming partner bench talent requests for this role will appear here.</div>
        @endif
    </div>
@endif
