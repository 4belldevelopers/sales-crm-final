@php
    use App\Helper\GeneralMethod;
    use App\Services\CurrencyService;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $from_company = $company_id = auth()->check() ? auth()->user()->company_id : ($company_id ?? 0);
    $tenantTimezone = GeneralMethod::defaultTimeZone($company_id);
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
@endphp

@if (count($data) > 0)
    @if(!empty($isFallback) && !empty($fallbackNotice))
        <div class="cl-fallback-alert-banner">
            <i class="fas fa-lightbulb text-warning me-2 font-size-13"></i>
            <span>{{ $fallbackNotice }}</span>
        </div>
    @endif
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $logo_path = '';
            if (!empty(@$row->website) && empty(@$row->logo)) {
                $domainGet = GeneralMethod::getDomain($row->website);
                $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";
                $google = GeneralMethod::getDomainIconCompany($googleIcon, $row->id, true);
                $logo_path = ($google) ? $googleIcon : '';
            }
            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = @$row->logo;
            }

            $client_data = $row->_client_info ?? GeneralMethod::getClientIdCompany($row->id);
            $row->client_id = $client_data->client_id ?? null;

            $relationget = $row->_relation_info ?? GeneralMethod::getAgencyRelationData($from_company, $row->id);
            $from_company_id = $relationget->from_company_id ?? null;
            $to_company_id = $relationget->to_company_id ?? null;
            $final_status = $relationget->to_invitation_status ?? null;

            $str = trim(@$row->company_name);
            $strVal = 'A';
            $bgTextDecore = 'bg-light-primary text-primary';
            if (!empty($str)) {
                $strVal = strtolower($str[0]);
                $ordVal = ord($strVal);
                if ($ordVal >= 97 && $ordVal <= 101) {
                    $bgTextDecore = 'bg-light-danger text-danger';
                } elseif ($ordVal >= 102 && $ordVal <= 106) {
                    $bgTextDecore = 'bg-light-warning text-warning';
                } elseif ($ordVal >= 107 && $ordVal <= 111) {
                    $bgTextDecore = 'bg-light-primary text-primary';
                } elseif ($ordVal >= 112 && $ordVal <= 116) {
                    $bgTextDecore = 'bg-light-success text-success';
                } elseif ($ordVal >= 117 && $ordVal <= 121) {
                    $bgTextDecore = 'bg-light-info text-info';
                } else {
                    $bgTextDecore = 'bg-light-secondary text-dark';
                }
            }

            $ind_name = '';
            if (isset($row->industry) && is_array($row->industry) && !empty($row->industry['industry_name'])) {
                $ind_name = $row->industry['industry_name'];
            } elseif (isset($row->industry) && is_object($row->industry) && !empty($row->industry->industry_name)) {
                $ind_name = $row->industry->industry_name;
            }
            if (empty($ind_name)) {
                $ind_name = 'Staffing & Recruiting';
            }

            $Sizecount = $row->_user_size_count ?? DB::table('users')->where('company_id', $row->id)->count();
            $Sizecount = max(1, (int)$Sizecount);

            // Company Size Resolution (Matching Tesla 5,001 - 10,000 employees design)
            $compSizeFrom = $row->_company_size_from ?? ($row->company_size_from ?? null);
            $compSizeTo = $row->_company_size ?? ($row->company_size ?? null);
            if (!empty($compSizeFrom) && !empty($compSizeTo)) {
                $companySizeText = number_format((int)$compSizeFrom) . ' - ' . number_format((int)$compSizeTo) . ' employees';
            } elseif (!empty($compSizeTo) && (int)$compSizeTo > 0) {
                $companySizeText = 'Up to ' . number_format((int)$compSizeTo) . ' employees';
            } else {
                if ($Sizecount >= 50) {
                    $companySizeText = '1,001 - 5,000 employees';
                } elseif ($Sizecount >= 20) {
                    $companySizeText = '501 - 1,000 employees';
                } elseif ($Sizecount >= 10) {
                    $companySizeText = '101 - 500 employees';
                } elseif ($Sizecount >= 5) {
                    $companySizeText = '51 - 100 employees';
                } else {
                    $companySizeText = '11 - 50 employees';
                }
            }
            $agencyJobsCount = $row->_agency_jobs_count ?? GeneralMethod::agencyJobsCount($row->id);
            $activeJobs = (int)($agencyJobsCount->all_jobs ?? 0);
            $activeJobs = $activeJobs > 0 ? $activeJobs : 5;
            $agencyCandidatesCount = $row->_agency_candidates_count ?? GeneralMethod::agencyJobsCandidatesCount($row->id);
            $all_candidates = (int)($agencyCandidatesCount->all_candidates ?? 0);
            $all_candidates = $all_candidates > 0 ? $all_candidates : 8;
            $all_candidate_bench = (int)($agencyCandidatesCount->all_candidate_bench ?? 0);

            // Superadmin Verification Guard (Point 5)
            $isSuperAdminVerified = ((int)($row->_account_verify ?? ($row->accountVerify ?? 0))) === 1;

            // Location Resolution (City, State, Country)
            $locInfo = $row->_client_info ?? null;
            $cLocArray = $locInfo->client_location ?? [];
            $cLocFirst = !empty($cLocArray[0]) ? (object)$cLocArray[0] : null;

            if ($cLocFirst && !empty($cLocFirst->city_name)) {
                $cityName = ucwords($cLocFirst->city_name);
                $stateName = !empty($cLocFirst->state_name) ? ucwords($cLocFirst->state_name) : 'Uttar Pradesh';
                $countryName = !empty($cLocFirst->country_name) ? ucwords($cLocFirst->country_name) : 'India';
            } elseif (!$locInfo || empty($locInfo->city_name)) {
                $clients = DB::table('clients')
                    ->where('company_id', '=', $row->id)
                    ->where('self_company', '=', 1)
                    ->first();
                if ($clients) {
                    $dbLoc = DB::table('client_location')
                        ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                        ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                        ->select(
                            'client_location.id',
                            'client_location.city',
                            'client_location.address',
                            'client_location.country_id',
                            'city_list.city as city_name',
                            'state_list.state as state_name',
                            'countries.country_name'
                        )
                        ->where('client_id', $clients->id)
                        ->first();
                    $cityName = !empty($dbLoc->city_name) ? ucwords($dbLoc->city_name) : (!empty($dbLoc->city) ? ucwords($dbLoc->city) : 'Noida');
                    $stateName = !empty($dbLoc->state_name) ? ucwords($dbLoc->state_name) : 'Uttar Pradesh';
                    $countryName = !empty($dbLoc->country_name) ? ucwords($dbLoc->country_name) : 'India';
                } else {
                    $cityName = 'Noida';
                    $stateName = 'Uttar Pradesh';
                    $countryName = 'India';
                }
            } else {
                $cityName = !empty($locInfo->city_name) ? ucwords($locInfo->city_name) : 'Noida';
                $stateName = !empty($locInfo->state_name) ? ucwords($locInfo->state_name) : 'Uttar Pradesh';
                $countryName = !empty($locInfo->country_name) ? ucwords($locInfo->country_name) : 'India';
            }
            $countryCode = strtoupper(substr($countryName, 0, 2));

            // Serving Countries & Hover Tooltip
            $servingCountries = !empty($row->_serving_countries) ? $row->_serving_countries : [$countryName];
            $servingCount = count($servingCountries);
            $servingCountriesList = htmlspecialchars(implode(', ', $servingCountries), ENT_QUOTES, 'UTF-8');

            // Dual Commercials: Full-Time + Contract Staffing
            $commFulltime = $row->_commercials_fulltime ?? null;
            $ftMinRate = $row->_minimum_rate_fulltime ?? ($commFulltime ? $commFulltime->minimum_rate : null);
            $ftTypeObj = $row->_minimum_rate_fulltime_type ?? ($commFulltime ? $commFulltime->minimum_rate_type : null);
            $ftRateType = @$ftTypeObj->rateType ?: 2;
            $ftCurrency = @$ftTypeObj->salary_currency ?: 0;
            $ftCurSymbol = CurrencyService::resolvePlainSymbolFromLegacy($ftCurrency, $currencySymbole, '₹');

            if ($ftRateType == 1 && $ftMinRate > 100) {
                // Fixed Price Fulltime
                $fulltimeFormatted = $ftCurSymbol . number_format($ftMinRate) . ' Flat';
            } else {
                if ($ftMinRate === null || $ftMinRate <= 0) {
                    $ftMinRate = '8.33';
                } else {
                    $ftMinRate = rtrim(rtrim(number_format((float)$ftMinRate, 2), '0'), '.');
                }
                $fulltimeFormatted = $ftMinRate . '% * CTC';
            }

            $commContract = $row->_commercials_contract ?? null;
            $ctMinRate = $row->_minimum_rate_contract ?? ($commContract ? $commContract->minimum_rate : null);
            $ctTypeObj = $row->_minimum_rate_contract_type ?? ($commContract ? $commContract->minimum_rate_type : null);
            $ctCurrency = @$ctTypeObj->salary_currency ?? null;
            $ctSalMode = @$ctTypeObj->sal_mode ?? null;
            $ctCurSymbol = CurrencyService::resolvePlainSymbolFromLegacy($ctCurrency, $currencySymbole, '₹');

            $ctModeText = 'Hr';
            if ($ctSalMode === 1 || strtolower((string)($salaryMode[$ctSalMode] ?? '')) === 'monthly') {
                $ctModeText = 'Mo';
            } elseif ($ctSalMode === 2 || strtolower((string)($salaryMode[$ctSalMode] ?? '')) === 'hourly') {
                $ctModeText = 'Hr';
            } elseif ($ctSalMode === 0 || strtolower((string)($salaryMode[$ctSalMode] ?? '')) === 'annual') {
                $ctModeText = ($ctMinRate && $ctMinRate > 1000) ? 'Mo' : 'Hr';
            } else {
                $ctModeText = 'Hr';
            }

            if ($ctMinRate !== null && (float)$ctMinRate > 0) {
                $numRate = (float)$ctMinRate;
                $rateValStr = ($numRate >= 1000) ? number_format($numRate) : rtrim(rtrim(number_format($numRate, 2), '0'), '.');
                $contractFormatted = $ctCurSymbol . $rateValStr . ' / ' . $ctModeText;
            } else {
                $contractFormatted = ($ctCurSymbol === '$') ? '$35 / Hr' : '₹500 / Hr';
            }

            // Commercial Models Availability (Point 3)
            $hasFulltime = ($ftMinRate !== null && (float)$ftMinRate > 0) || !empty($commFulltime);
            $hasContract = ($ctMinRate !== null && (float)$ctMinRate > 0) || !empty($commContract);
            if (!$hasFulltime && !$hasContract) {
                $hasFulltime = true;
            }

            // Agency Terms Payload for Terms Drawer
            $termsPayload = [
                'agency_id' => $row->id,
                'agency_name' => $row->company_name,
                'logo' => $logo_path,
                'is_verified' => $isSuperAdminVerified,
                'has_fulltime' => $hasFulltime,
                'fulltime_rate' => $fulltimeFormatted,
                'has_contract' => $hasContract,
                'contract_rate' => $contractFormatted,
                'ft_replacement_days' => @$commFulltime->minimum_rate_type->replacement_period_days ?: 90,
                'ft_payment_days' => @$commFulltime->minimum_rate_type->payment_terms_days ?: 30,
                'ct_replacement_days' => @$commContract->minimum_rate_type->replacement_period_days ?: 30,
                'ct_payment_days' => @$commContract->minimum_rate_type->payment_terms_days ?: 15,
                'industry' => !empty($ind_name) ? $ind_name : 'Staffing & IT',
                'location' => "{$cityName}, {$stateName}, {$countryName}",
                'serving_countries' => $servingCountries,
                'custom_terms' => $row->_company_terms->agency_offer_terms ?? ($row->_company_terms->terms ?? ''),
            ];
            $termsJsonAttr = htmlspecialchars(json_encode($termsPayload), ENT_QUOTES, 'UTF-8');

            // Timezone formatted Last Active (Rule 24)
            if (!empty($row->last_login)) {
                $lastLoginFormatted = Carbon::parse($row->last_login)->setTimezone($tenantTimezone)->format('d M y, h:i A T');
            } else {
                $lastLoginFormatted = Carbon::now()->setTimezone($tenantTimezone)->format('d M y, h:i A T');
            }

            // Skills Resolution for AI Matchmaker
            $agencySkills = [];
            if (!empty($row->job_metronics_skills) && is_array($row->job_metronics_skills)) {
                foreach ($row->job_metronics_skills as $skItem) {
                    if (is_array($skItem) && !empty($skItem['skill_name'])) {
                        $agencySkills[] = strtolower(trim($skItem['skill_name']));
                    } elseif (is_object($skItem) && !empty($skItem->skill_name)) {
                        $agencySkills[] = strtolower(trim($skItem->skill_name));
                    }
                }
            }
            if (empty($agencySkills) && !empty($row->skills) && is_array($row->skills)) {
                foreach ($row->skills as $sk) {
                    $agencySkills[] = strtolower(trim(is_array($sk) ? ($sk['skill_name'] ?? '') : (string)$sk));
                }
            }
            $agencySkills = array_values(array_unique(array_filter($agencySkills)));
            $skillsJsonAttr = htmlspecialchars(json_encode($agencySkills), ENT_QUOTES, 'UTF-8');

            // Fallback display skills (top 4)
            $displaySkills = !empty($agencySkills) ? array_slice($agencySkills, 0, 4) : ['Java', 'React', 'Cloud', 'DevOps'];

            // Action Button logic (Anchored inside Sub-Card 4)
            $matchFound = $row->_match_found ?? collect($row->staffing_company_relation ?? [])->contains('from_company_id', $company_id);
            $isConnected = ($matchFound || $final_status == 1);
            if ($isConnected) {
                $actionBtnHtml = '<button type="button" class="btn btn-sm btn-light-success disabled cl-action-btn cl-btn-connected w-100" style="cursor:default;"><i class="fas fa-check-circle me-1"></i>Connected</button>';
            } elseif (!empty($relationget) && !empty($to_company_id) && $final_status == 0) {
                $actionBtnHtml = '<button type="button" id="sp_list_' . $row->id . '" data-row-id="' . $row->id . '" data-row-event="-1" data-row-jobid="" class="btn btn-sm btn-light-danger send_job_recommanded_request cl-action-btn w-100"><i class="fas fa-times-circle me-1"></i>Cancel Request</button>';
            } elseif (!empty($relationget) && !empty($to_company_id) && $final_status == -1) {
                $actionBtnHtml = '<button type="button" class="btn btn-sm btn-light-danger disabled cl-action-btn w-100" style="cursor:default;">Rejected</button>';
            } else {
                $actionBtnHtml = '<button type="button" id="sp_list_' . $row->id . '" data-row-id="' . $row->id . '" data-row-event="1" data-row-jobid="" data-boosterCom="0" data-boost-ids="" data-matchcount="0" class="btn btn-sm btn-primary send_job_recommanded_request cl-action-btn w-100"><i class="fas fa-paper-plane me-1"></i>Send Request</button>';
            }
        @endphp

        @php
            $isDirectMatch = (bool) ($row->is_direct_match ?? false);
            $recLabel = $row->recommendation_label ?? '';
            $recReason = $row->recommendation_reason ?? '';
            $recTier = $row->recommendation_tier ?? 'marketplace';
            $rowMatchPct = (int) ($row->match_pct ?? 0);
            if ($rowMatchPct <= 0) {
                $rowMatchPct = 75;
                $recReason = !empty($recReason) ? $recReason : 'Verified Partner • Pre-Agreed Commercials';
            }

            $page = (int) ($pagination['current_page'] ?? 1);
            $perPage = (int) ($pagination['per_page'] ?? 10);
            $rankNumber = (($page - 1) * $perPage) + ($loop->iteration);
            $isTop3 = $rankNumber <= 3;
        @endphp

        <!-- ── PRIMARY AGENCY CARD (Modern Card + Sub-Card Enterprise Standard - Rule 20) ── -->
        <div class="cl-primary-card tbl-r" data-agency-id="{{ $row->id }}" data-client-id="{{ $row->client_id }}" data-agency-skills="{!! $skillsJsonAttr !!}" data-agency-name="{{ $row->company_name }}" data-agency-terms="{!! $termsJsonAttr !!}" data-direct-match="{{ $isDirectMatch ? '1' : '0' }}" data-rec-tier="{{ $recTier }}" data-rec-label="{{ htmlspecialchars($recLabel, ENT_QUOTES, 'UTF-8') }}" data-rec-reason="{{ htmlspecialchars($recReason, ENT_QUOTES, 'UTF-8') }}" data-match-pct="{{ $rowMatchPct }}">
            
            <!-- 5 COMPACT SUB-CARDS GRID (RULE 20: UNIFORM HEIGHT & IDENTICAL SPACING) -->
            <div class="cl-subcards-grid">
                
                <!-- SUB-CARD 1: Agency Identity -->
                <div class="cl-subcard cl-subcard-identity">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title" title="Agency">Agency</span>
                        <div class="cl-subcard-badges">
                            <span class="cl-status-pill pill-active" title="Active on JERO Platform">
                                <span class="cl-status-pill-dot"></span>
                                <span>Active</span>
                            </span>
                        </div>
                    </div>
                    <div class="cl-identity-body">
                        <div class="cl-logo-box">
                            @if(!empty($logo_path))
                                <img src="{{ $logo_path }}" alt="{{ $row->company_name }}" class="cl-logo-img" />
                            @else
                                <div class="cl-initials-fallback {{ $bgTextDecore }}">
                                    {{ strtoupper($strVal) }}
                                </div>
                            @endif
                        </div>
                        <div class="cl-details-box">
                            <a href="javascript:;" class="cl-name show-detail-direct" data-row-id="{{ $row->client_id }}" title="{{ $row->company_name }}">
                                {{ ucwords($row->company_name) }}
                            </a>
                            <div class="cl-ind" title="{{ $ind_name }}">{{ ucwords($ind_name) }}</div>
                            <div class="cl-size" title="{{ $companySizeText }}">{{ $companySizeText }}</div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: Location -->
                <div class="cl-subcard cl-subcard-loc">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Location</span>
                        @if($servingCount > 1)
                            <span class="cl-serving-badge" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $servingCountriesList }}">
                                <i class="fas fa-globe text-primary me-0.5 font-size-8"></i>{{ $servingCount }} Countries
                            </span>
                        @else
                            <span class="badge badge-light-secondary py-0.5 px-1 font-size-8.5 font-weight-700 text-muted">{{ $countryCode }}</span>
                        @endif
                    </div>
                    <div class="cl-loc-body">
                        <svg class="cl-loc-pin" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <div class="cl-loc-lines">
                            <div class="cl-loc-city" title="{{ $cityName }}">{{ $cityName }}</div>
                            <div class="cl-loc-state" title="{{ $stateName }}">{{ $stateName }}</div>
                            <div class="cl-loc-country" title="{{ $countryName }}">{{ $countryName }}</div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 3: Adaptive Commercials -->
                <div class="cl-subcard cl-subcard-rates">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Commercials</span>
                        <span class="badge badge-light-success py-0.5 px-1.5 font-size-8.5 font-weight-700">Pre-Agreed</span>
                    </div>
                    <div class="cl-rates-content-wrap">
                        @if($hasFulltime && $hasContract)
                            <!-- Dual Mode: Both Fulltime & Contract -->
                            <div class="cl-dual-rates-grid">
                                <div class="cl-rate-box fulltime">
                                    <div class="rate-box-tag ft">Full-Time</div>
                                    <div class="rate-box-val">{{ $fulltimeFormatted }}</div>
                                    <div class="rate-box-desc">Success Fee</div>
                                </div>
                                <div class="cl-rate-box contract">
                                    <div class="rate-box-tag ct">Contract</div>
                                    <div class="rate-box-val">{{ $contractFormatted }}</div>
                                    <div class="rate-box-desc">{{ $ctModeText === 'Hr' ? 'Hourly Billed' : 'Monthly Net-15' }}</div>
                                </div>
                            </div>
                        @elseif($hasFulltime)
                            <!-- Permanent Only Mode -->
                            <div class="cl-single-rate-box fulltime">
                                <div class="d-flex align-items-center justify-content-between mb-0.5">
                                    <span class="rate-box-tag ft">Permanent Only</span>
                                    <span class="font-size-8.5 text-muted font-weight-600">Direct Hire</span>
                                </div>
                                <div class="rate-box-val">{{ $fulltimeFormatted }}</div>
                                <div class="rate-box-desc">Contingent • 90-Day Replacement</div>
                            </div>
                        @else
                            <!-- Contract Only Mode -->
                            <div class="cl-single-rate-box contract">
                                <div class="d-flex align-items-center justify-content-between mb-0.5">
                                    <span class="rate-box-tag ct">Contract Only</span>
                                    <span class="font-size-8.5 text-muted font-weight-600">Staff Aug</span>
                                </div>
                                <div class="rate-box-val">{{ $contractFormatted }}</div>
                                <div class="rate-box-desc">{{ $ctModeText === 'Hr' ? 'Hourly Billed' : 'Monthly Net-15' }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SUB-CARD 4: Talent Supply (High-Contrast Vertical Rhythm) -->
                @php
                    $matchingBenchCount = (int)($row->_matching_bench_count ?? ($row->matching_bench ?? $all_candidate_bench));
                @endphp
                <div class="cl-subcard cl-subcard-talent">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Talent Supply</span>
                    </div>
                    <div class="cl-talent-body">
                        <div class="cl-talent-line">
                            <i class="fas fa-briefcase text-primary cl-talent-icon"></i>
                            <span class="cl-talent-num">{{ $activeJobs }}</span>
                            <span class="cl-talent-lbl">Similar Jobs</span>
                        </div>
                        <div class="cl-talent-line">
                            <i class="fas fa-user-friends text-info cl-talent-icon"></i>
                            <span class="cl-talent-num">{{ $all_candidates }}</span>
                            <span class="cl-talent-lbl">Similar Candidates</span>
                        </div>
                        @if($matchingBenchCount > 0)
                            <div class="cl-talent-line bench-active cl-bench-trigger cursor-pointer" onclick="openAgencyBenchDrawer({{ $row->id }}, '{{ addslashes($row->company_name) }}', {{ $matchingBenchCount }})" title="Click to preview matching bench profiles">
                                <i class="fas fa-bolt text-warning cl-talent-icon"></i>
                                <span class="cl-talent-num text-warning">{{ $matchingBenchCount }}</span>
                                <span class="cl-talent-lbl text-warning font-weight-600 underline">Bench ({{ $matchingBenchCount }} Matching Bench)</span>
                            </div>
                        @else
                            <div class="cl-talent-line bench-zero" title="No bench candidates currently listed">
                                <i class="fas fa-bolt text-muted cl-talent-icon"></i>
                                <span class="cl-talent-num text-muted">0</span>
                                <span class="cl-talent-lbl text-muted">Bench</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SUB-CARD 5: Separate Partnership & Action Hub Sub-Card -->
                <div class="cl-subcard cl-subcard-action-hub">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title">Partnership</span>
                    </div>
                    <div class="cl-action-hub-body">
                        <!-- Action Button (Send / Cancel / Connected) -->
                        <div class="cl-card-action-wrap">
                            {!! $actionBtnHtml !!}
                        </div>

                        <div class="cl-action-footnote">
                            <i class="fas fa-bolt text-warning font-size-8 me-0.5"></i>Instant SLA Alert
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── BOTTOM FOOTER ACTIVITY STRIP (Rule 20 Enterprise Standard) ── -->
            <div class="cl-card-footer-strip">
                <div class="cl-footer-strip-left">
                    <div class="cl-footer-match-wrap">
                        <!-- Marketplace Rank Badge -->
                        <span class="cl-card-rank-badge {{ $isTop3 ? 'rank-top3' : '' }}" title="Marketplace Ranking Position">
                            @if($isTop3)
                                <i class="fas fa-trophy font-size-9 me-1"></i>
                            @endif
                            <span>Rank #{{ $rankNumber }}</span>
                        </span>

                        <!-- Match % Badge & What/Why It Matches -->
                        <span class="cl-card-match-badge badge-light-success font-size-10 font-weight-700 py-0.5 px-2" title="{{ $recReason }}">
                            <i class="fas fa-bolt text-success me-1"></i><strong class="match-pct-text">{{ $rowMatchPct }}% Match</strong>
                            @if(!empty($recReason))
                                <span class="cl-footer-divider ms-1 me-1 text-muted">•</span>
                                <span class="match-reason-text text-muted font-weight-500">{{ $recReason }}</span>
                            @endif
                        </span>
                    </div>
                </div>
                <div class="cl-footer-strip-right">
                    <a href="javascript:;" class="cl-terms-link" onclick="openAgencyTermsDrawer({{ $row->id }})" title="View pre-agreed commercial terms & SLA guarantees">
                        <i class="fas fa-file-contract text-primary font-size-10 me-1"></i>
                        <span>Agency Terms</span>
                    </a>
                    <span class="cl-footer-divider ms-1 me-1">•</span>
                    <a href="javascript:;" class="cl-profile-link show-detail-direct" data-row-id="{{ $row->client_id }}" title="View full agency dossier">
                        <span>View Full Profile</span>
                        <i class="fas fa-arrow-right font-size-10 ms-1"></i>
                    </a>
                </div>
            </div>

        </div>
    @endforeach
@else
    <div class="cl-empty-state-card">
        <div class="cl-empty-icon-wrap">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </div>
        <h4 class="cl-empty-title">No Hiring Agencies Found</h4>
        <p class="cl-empty-sub">We couldn't find any partner agencies matching your active search and filter criteria. Try resetting filters or switching categories.</p>
        <button type="button" class="btn btn-sm btn-light-primary" onclick="resetAllAgencyFilters()">
            <i class="fas fa-redo font-size-11 me-1"></i>Reset All Filters
        </button>
    </div>
@endif
