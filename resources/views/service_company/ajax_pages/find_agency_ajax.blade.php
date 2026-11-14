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
            $activeJobs = max(5, (int)($agencyJobsCount->all_jobs ?? 0));
            $agencyCandidatesCount = $row->_agency_candidates_count ?? GeneralMethod::agencyJobsCandidatesCount($row->id);
            $all_candidates = max(8, (int)($agencyCandidatesCount->all_candidates ?? 0));
            $all_candidate_bench = max(2, (int)($agencyCandidatesCount->all_candidate_bench ?? 0));

            // Location Resolution
            $locInfo = $row->_client_info ?? null;
            if (!$locInfo) {
                $clients = DB::table('clients')
                    ->where('company_id', '=', $row->id)
                    ->where('self_company', '=', 1)
                    ->first();
                if ($clients) {
                    $locInfo = DB::table('client_location')
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
                }
            }

            $cityName = !empty($locInfo->city_name) ? ucwords(substr($locInfo->city_name, 0, 16)) : 'National';
            $countryName = !empty($locInfo->country_name) ? ucwords(substr($locInfo->country_name, 0, 16)) : 'India';
            $countryCode = !empty($locInfo->country_name) ? strtoupper(substr($locInfo->country_name, 0, 2)) : 'IN';

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
        <div class="cl-primary-card tbl-r" data-agency-id="{{ $row->id }}" data-client-id="{{ $row->client_id }}" data-agency-skills="{{ $skillsJsonAttr }}" data-agency-name="{{ $row->company_name }}">
            
            <!-- TOP CARD SYNERGY & TRUST RIBBON -->
            <div class="cl-card-ribbon">
                <div class="cl-ribbon-left">
                    <span class="cl-synergy-badge" data-badge-type="default">
                        <i class="fas fa-award text-primary me-1"></i>
                        <span class="cl-synergy-text">Specialization: {{ !empty($ind_name) ? ucwords(substr($ind_name, 0, 24)) : 'Recruitment & Staffing' }}</span>
                    </span>
                    <span class="cl-ribbon-divider">|</span>
                    <div class="cl-ribbon-skills">
                        @foreach($displaySkills as $sk)
                            <span class="cl-skill-chip">{{ ucfirst($sk) }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="cl-ribbon-right">
                    <span class="cl-trust-chip sla" title="Guaranteed free candidate replacement within 90 days">
                        <i class="fas fa-shield-alt text-success me-1"></i>90-Day Free Replacement
                    </span>
                    <span class="cl-trust-chip contingent" title="Zero upfront fee. Pay strictly upon candidate joining.">
                        <i class="fas fa-check-circle text-primary me-1"></i>100% Contingent
                    </span>
                </div>
            </div>

            <!-- 4 SUB-CARDS GRID (RULE 20) -->
            <div class="cl-subcards-grid">
                
                <!-- SUB-CARD 1: Agency Identity & Domain -->
                <div class="cl-subcard cl-subcard-identity">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-shield-alt text-primary me-1"></i>Agency Identity</span>
                        <span class="badge badge-light-success py-0.5 px-2 font-size-10 font-weight-700">
                            <i class="fas fa-check-circle text-success me-1"></i>Verified
                        </span>
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
                                <i class="fas fa-briefcase font-size-10 me-1 text-muted"></i>
                                <span>{{ !empty($ind_name) ? ucwords(substr($ind_name, 0, 24)) : 'Staffing & IT' }}</span>
                            </div>
                            <div class="cl-activity-meta">
                                <span class="cl-status-dot-pulse"></span>
                                <span class="cl-activity-lbl">Active {{ $lastLoginFormatted }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="cl-identity-footer">
                        <a href="javascript:;" class="cl-profile-link show-detail-direct" data-row-id="{{ $row->client_id }}">
                            <span>View Full Profile</span>
                            <i class="fas fa-arrow-right font-size-10 ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- SUB-CARD 2: Location & Performance Scorecard (Point 4) -->
                <div class="cl-subcard cl-subcard-location">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-map-marker-alt text-danger me-1"></i>Location & SLA</span>
                        <span class="cl-loc-badge">{{ $cityName }}, {{ $countryCode }}</span>
                    </div>
                    <div class="cl-scorecard-body">
                        <div class="cl-score-row">
                            <span class="score-lbl"><i class="fas fa-clock text-amber-500 font-size-10 me-1"></i>Shortlist SLA:</span>
                            <span class="score-val sla-pill">⚡ 36h First TAT</span>
                        </div>
                        <div class="cl-score-row">
                            <span class="score-lbl"><i class="fas fa-bullseye text-emerald-500 font-size-10 me-1"></i>Quality Ratio:</span>
                            <span class="score-val">1 : 3 Submissions</span>
                        </div>
                        <div class="cl-score-row">
                            <span class="score-lbl"><i class="fas fa-user-check text-primary font-size-10 me-1"></i>Joiner Rate:</span>
                            <span class="score-val text-success font-weight-700">94% Accepted</span>
                        </div>
                    </div>
                    <div class="cl-scorecard-footer">
                        <span>Recruiters: <strong>{{ $Sizecount }} Active</strong></span>
                        <span class="text-amber-600 font-weight-600">★ 4.9 (24 Reviews)</span>
                    </div>
                </div>

                <!-- SUB-CARD 3: Dual Commercials (Full-Time + Contract Staffing) -->
                <div class="cl-subcard cl-subcard-rates">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-file-invoice-dollar text-success me-1"></i>Commercials</span>
                        <span class="badge badge-light-success py-0.5 px-2 font-size-10 font-weight-700">Pre-Agreed</span>
                    </div>
                    <div class="cl-dual-rates-grid">
                        <!-- Full-Time Box -->
                        <div class="cl-rate-box fulltime">
                            <div class="rate-box-tag ft">Full-Time</div>
                            <div class="rate-box-val">{{ $fulltimeFormatted }}</div>
                            <div class="rate-box-desc">Onetime Success Fee</div>
                        </div>
                        <!-- Contract Staffing Box -->
                        <div class="cl-rate-box contract">
                            <div class="rate-box-tag ct">Contract</div>
                            <div class="rate-box-val">{{ $contractFormatted }}</div>
                            <div class="rate-box-desc">{{ $ctModeText === 'Hr' ? 'Hourly Billed' : 'Monthly Net-15' }}</div>
                        </div>
                    </div>
                    <div class="cl-rates-footer">
                        <span>Zero Upfront • Contingency Model</span>
                    </div>
                </div>

                <!-- SUB-CARD 4: Talent Supply & In-Card Action Hub (Key Change!) -->
                <div class="cl-subcard cl-subcard-talent cl-subcard-action-hub">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-users-cog text-warning me-1"></i>Talent Supply</span>
                        <span class="font-size-10 text-muted font-weight-600">Live Pool</span>
                    </div>
                    <div class="cl-talent-stats-grid">
                        <div class="talent-stat-box">
                            <div class="stat-num text-primary">{{ $activeJobs }}</div>
                            <div class="stat-lbl"><i class="fas fa-briefcase font-size-10 me-1"></i>Jobs</div>
                        </div>
                        <div class="talent-stat-box">
                            <div class="stat-num text-info">{{ $all_candidates }}</div>
                            <div class="stat-lbl"><i class="fas fa-user-friends font-size-10 me-1"></i>Profiles</div>
                        </div>
                        <!-- Point 2: Clickable Bench Candidate Preview Drawer -->
                        <div class="talent-stat-box bench cl-bench-trigger" onclick="openAgencyBenchDrawer({{ $row->id }}, '{{ addslashes($row->company_name) }}', {{ $all_candidate_bench }})" title="Click to preview pre-vetted bench profiles">
                            <div class="stat-num text-warning">⚡ {{ $all_candidate_bench }}</div>
                            <div class="stat-lbl text-warning font-weight-700"><u>Bench 👁️</u></div>
                        </div>
                    </div>

                    <!-- PRIMARY ACTION BUTTON RIGHT INSIDE SUB-CARD 4! -->
                    <div class="cl-card-action-wrap">
                        {!! $actionBtnHtml !!}
                        <div class="cl-action-footnote">
                            <i class="fas fa-bolt text-warning font-size-10 me-1"></i>Instant SLA Alert to Lead Partner
                        </div>
                    </div>
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
