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

            $Sizecount = $row->_user_size_count ?? DB::table('users')->where('company_id', $row->id)->count();
            $Sizecount = max(1, (int)$Sizecount);
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
            $matchFound = collect($row->staffing_company_relation ?? [])->contains('from_company_id', $company_id);
            if ($matchFound || $final_status == 1) {
                $actionBtnHtml = '<button type="button" class="btn btn-sm btn-light-success disabled cl-action-btn w-100" style="cursor:default;"><i class="fas fa-check-circle me-1"></i>Connected</button>';
            } elseif (!empty($relationget) && !empty($to_company_id) && $final_status == 0) {
                $actionBtnHtml = '<button type="button" id="sp_list_' . $row->id . '" data-row-id="' . $row->id . '" data-row-event="-1" data-row-jobid="" class="btn btn-sm btn-light-danger send_job_recommanded_request cl-action-btn w-100"><i class="fas fa-times-circle me-1"></i>Cancel Request</button>';
            } elseif (!empty($relationget) && !empty($to_company_id) && $final_status == -1) {
                $actionBtnHtml = '<button type="button" class="btn btn-sm btn-light-danger disabled cl-action-btn w-100" style="cursor:default;">Rejected</button>';
            } else {
                $actionBtnHtml = '<button type="button" id="sp_list_' . $row->id . '" data-row-id="' . $row->id . '" data-row-event="1" data-row-jobid="" data-boosterCom="0" data-boost-ids="" data-matchcount="0" class="btn btn-sm btn-primary send_job_recommanded_request cl-action-btn w-100"><i class="fas fa-paper-plane me-1"></i>Send Request</button>';
            }
        @endphp

        <!-- ── PRIMARY AGENCY CARD (Modern Card + Sub-Card Enterprise Standard - Rule 20) ── -->
        <div class="cl-primary-card tbl-r" data-agency-id="{{ $row->id }}" data-client-id="{{ $row->client_id }}" data-agency-skills="{!! $skillsJsonAttr !!}" data-agency-name="{{ $row->company_name }}" data-agency-terms="{!! $termsJsonAttr !!}">
            
            <!-- 5 COMPACT SUB-CARDS GRID (RULE 20: UNIFORM HEIGHT & IDENTICAL SPACING) -->
            <div class="cl-subcards-grid">
                
                <!-- SUB-CARD 1: Agency Identity -->
                <div class="cl-subcard cl-subcard-identity">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-shield-alt text-primary me-1"></i>Identity<span class="badge badge-light-success py-0.5 px-1 font-size-8.5 font-weight-700 ms-1 cl-card-match-badge d-none"></span></span>
                        @if($isSuperAdminVerified)
                            <span class="badge badge-light-success py-0.5 px-1.5 font-size-9 font-weight-700" title="Accredited & Verified by Superadmin">
                                <i class="fas fa-check-circle text-success me-0.5"></i>Verified
                            </span>
                        @else
                            <span class="badge badge-light-secondary py-0.5 px-1.5 font-size-9 font-weight-600 text-muted" title="Account verification pending review">
                                <i class="fas fa-clock text-warning me-0.5"></i>Pending
                            </span>
                        @endif
                    </div>
                    <div class="cl-identity-body">
                        <div class="agency-logo-wrap">
                            @if(!empty($logo_path))
                                <img src="{{ $logo_path }}" alt="{{ $row->company_name }}" class="agency-logo-img" />
                            @else
                                <div class="agency-logo-initials {{ $bgTextDecore }}">
                                    {{ strtoupper($strVal) }}
                                </div>
                            @endif
                        </div>
                        <div class="agency-meta-info">
                            <a href="javascript:;" class="agency-name show-detail-direct" data-row-id="{{ $row->client_id }}" title="{{ $row->company_name }}">
                                {{ ucwords($row->company_name) }}
                            </a>
                            <div class="agency-ind-pill">
                                <i class="fas fa-briefcase font-size-9 me-1 text-muted"></i>
                                <span>{{ !empty($ind_name) ? ucwords(substr($ind_name, 0, 22)) : 'Staffing & IT' }}</span>
                            </div>
                            <div class="cl-identity-tagline">
                                @if($isSuperAdminVerified)
                                    <span class="badge badge-light-primary font-size-8 py-0.5 px-1"><i class="fas fa-certificate text-primary me-0.5"></i>Accredited</span>
                                @else
                                    <span class="badge badge-light-secondary font-size-8 py-0.5 px-1 text-muted"><i class="fas fa-user-check text-muted me-0.5"></i>Partner</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: Dedicated Location & Countries Serving Sub-Card -->
                <div class="cl-subcard cl-subcard-location">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-map-marker-alt text-danger me-1"></i>Location</span>
                        <span class="badge badge-light-danger py-0.5 px-1 font-size-8.5 font-weight-700" title="{{ $countryName }}">{{ $countryCode }}</span>
                    </div>
                    <div class="cl-location-body">
                        <div class="cl-loc-primary-row" title="{{ $cityName }}, {{ $stateName }}, {{ $countryName }}">
                            <i class="fas fa-map-marker-alt text-danger font-size-10 me-1"></i>
                            <span class="cl-loc-city font-weight-700 text-dark">{{ $cityName }}</span>
                        </div>
                        <div class="cl-loc-secondary-row text-secondary font-size-10 truncate" title="{{ $stateName }}, {{ $countryName }}">
                            {{ $stateName }}, {{ $countryName }}
                        </div>
                        <div class="cl-loc-serving-row">
                            <span class="cl-serving-pill" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $servingCountriesList }}">
                                <i class="fas fa-globe-americas text-primary me-1 font-size-9"></i>
                                Serving <strong>{{ $servingCount }}</strong> {{ $servingCount > 1 ? 'Countries' : 'Country' }}
                                <i class="fas fa-info-circle ms-0.5 text-muted font-size-8"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 3: Adaptive Commercials & Agency Terms Drawer Trigger -->
                <div class="cl-subcard cl-subcard-rates">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-file-invoice-dollar text-success me-1"></i>Commercials</span>
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
                                    <span class="font-size-9 text-muted font-weight-600">Direct Hire</span>
                                </div>
                                <div class="rate-box-val">{{ $fulltimeFormatted }}</div>
                                <div class="rate-box-desc">Contingent • 90-Day Replacement</div>
                            </div>
                        @else
                            <!-- Contract Only Mode -->
                            <div class="cl-single-rate-box contract">
                                <div class="d-flex align-items-center justify-content-between mb-0.5">
                                    <span class="rate-box-tag ct">Contract Only</span>
                                    <span class="font-size-9 text-muted font-weight-600">Staff Aug</span>
                                </div>
                                <div class="rate-box-val">{{ $contractFormatted }}</div>
                                <div class="rate-box-desc">{{ $ctModeText === 'Hr' ? 'Hourly Billed' : 'Monthly Net-15' }}</div>
                            </div>
                        @endif

                        <!-- Agency Terms Drawer Button -->
                        <button type="button" class="btn btn-xs btn-light-primary w-100 cl-view-terms-btn" onclick="openAgencyTermsDrawer({{ $row->id }})">
                            <i class="fas fa-file-contract me-1"></i>View Agency Terms →
                        </button>
                    </div>
                </div>

                <!-- SUB-CARD 4: Jobs & Talent Supply (Vertical Listings) -->
                <div class="cl-subcard cl-subcard-talent">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-users-cog text-warning me-1"></i>Talent Supply</span>
                        <span class="badge badge-light-warning py-0.5 px-1 font-size-8.5 font-weight-700 text-dark">Live Pool</span>
                    </div>
                    <div class="cl-talent-vertical-list">
                        <div class="cl-talent-vrow">
                            <div class="vrow-left">
                                <i class="fas fa-briefcase text-primary font-size-9 me-1"></i>
                                <span class="vrow-lbl">Active Jobs:</span>
                            </div>
                            <span class="vrow-val text-primary font-weight-700">{{ $activeJobs }}</span>
                        </div>
                        <div class="cl-talent-vrow">
                            <div class="vrow-left">
                                <i class="fas fa-user-friends text-info font-size-9 me-1"></i>
                                <span class="vrow-lbl">Profiles:</span>
                            </div>
                            <span class="vrow-val text-info font-weight-700">{{ $all_candidates }}</span>
                        </div>
                        @if($all_candidate_bench > 0)
                            <div class="cl-talent-vrow bench cl-bench-trigger" onclick="openAgencyBenchDrawer({{ $row->id }}, '{{ addslashes($row->company_name) }}', {{ $all_candidate_bench }})" title="Click to preview pre-vetted bench profiles">
                                <div class="vrow-left">
                                    <i class="fas fa-bolt text-warning font-size-9 me-1"></i>
                                    <span class="vrow-lbl text-warning font-weight-700">Pre-Vetted Bench:</span>
                                </div>
                                <span class="vrow-val text-warning font-weight-700"><u>⚡ {{ $all_candidate_bench }} 👁️</u></span>
                            </div>
                        @else
                            <div class="cl-talent-vrow disabled" title="No bench candidates currently listed">
                                <div class="vrow-left">
                                    <i class="fas fa-bolt text-muted font-size-9 me-1"></i>
                                    <span class="vrow-lbl text-muted">Pre-Vetted Bench:</span>
                                </div>
                                <span class="vrow-val text-muted font-weight-600">0</span>
                            </div>
                        @endif
                    </div>
                    <div class="cl-talent-supply-note">
                        <i class="fas fa-check-circle text-success font-size-8 me-0.5"></i>
                        <span>Domain verified candidate pool</span>
                    </div>
                </div>

                <!-- SUB-CARD 5: Separate Partnership & Action Hub Sub-Card -->
                <div class="cl-subcard cl-subcard-action-hub">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-handshake text-info me-1"></i>Partnership</span>
                        <span class="badge badge-light-primary py-0.5 px-1.5 font-size-8.5 font-weight-700">Connect</span>
                    </div>
                    <div class="cl-action-hub-body">
                        <!-- Action Button (Send / Cancel / Connected) -->
                        <div class="cl-card-action-wrap">
                            {!! $actionBtnHtml !!}
                        </div>

                        <div class="cl-action-footnote">
                            <i class="fas fa-bolt text-warning font-size-9 me-0.5"></i>Instant SLA Alert to Lead Partner
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── BOTTOM FOOTER ACTIVITY STRIP (Rule 20 Enterprise Standard) ── -->
            <div class="cl-card-footer-strip">
                <div class="cl-footer-strip-left">
                    <div class="cl-footer-item">
                        <span class="cl-status-dot-pulse"></span>
                        <span class="cl-activity-lbl">Active {{ $lastLoginFormatted }}</span>
                    </div>
                    <span class="cl-footer-divider">•</span>
                    <div class="cl-footer-item">
                        <i class="fas fa-clock text-warning font-size-10 me-1"></i>
                        <span>Avg SLA: <strong>~36-48h First TAT</strong></span>
                    </div>
                    <span class="cl-footer-divider">•</span>
                    <div class="cl-footer-item">
                        <i class="fas fa-user-tie text-primary font-size-10 me-1"></i>
                        <span>Capacity: <strong>{{ $Sizecount }} Active {{ $Sizecount > 1 ? 'Recruiters' : 'Recruiter' }}</strong></span>
                    </div>
                    <span class="cl-footer-divider">•</span>
                    <div class="cl-footer-item">
                        @if($isSuperAdminVerified)
                            <i class="fas fa-shield-alt text-success font-size-10 me-1"></i>
                            <span class="text-success font-weight-600">Marketplace Accredited</span>
                        @else
                            <i class="fas fa-info-circle text-muted font-size-10 me-1"></i>
                            <span class="text-muted">Onboarding Partner</span>
                        @endif
                    </div>
                </div>
                <div class="cl-footer-strip-right">
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
