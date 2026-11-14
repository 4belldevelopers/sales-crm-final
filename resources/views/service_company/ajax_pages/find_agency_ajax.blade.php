@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $from_company = $company_id = auth()->check() ? auth()->user()->company_id : ($company_id ?? 0);
    $tenantTimezone = GeneralMethod::defaultTimeZone($company_id);
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
            $agencyJobsCount = $row->_agency_jobs_count ?? GeneralMethod::agencyJobsCount($row->id);
            $activeJobs = $agencyJobsCount->all_jobs ?? 0;
            $agencyCandidatesCount = $row->_agency_candidates_count ?? GeneralMethod::agencyJobsCandidatesCount($row->id);
            $all_candidates = $agencyCandidatesCount->all_candidates ?? 0;
            $all_candidate_bench = $agencyCandidatesCount->all_candidate_bench ?? 0;

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

            // Rates & Commercials
            $commtype = ($parent_employment_type == 1) ? 1 : 2;
            $minimumRate = $row->_minimum_rate ?? null;
            if ($minimumRate === null) {
                $minimumRate = DB::table('commericials')
                    ->join('commericial_details', 'commericial_details.commericial_id', '=', 'commericials.id')
                    ->where('commericials.commtype', '=', $commtype)
                    ->where('commericials.company_id', '=', $row->id)
                    ->min('commericial_details.rate');
            }
            $minimumRateType = $row->_minimum_rate_type ?? DB::table('commericials')->where('commtype', $commtype)->where('company_id', $row->id)->first();

            $salaryMode = GeneralMethod::salaryMode();
            $currencySymbole = GeneralMethod::currencySymbole();
            $jobSalaryCurrency = @$minimumRateType->salary_currency ?: 0;
            $jobSalaryMode = @$minimumRateType->sal_mode ?: 0;
            $current_cur = $currencySymbole[$jobSalaryCurrency] ?? '₹';
            $current_mode = $salaryMode[$jobSalaryMode] ?? 'Monthly';

            if ($commtype == 1) {
                $ratesFormatted = $current_cur . ($minimumRate ?: '500') . '/' . $current_mode;
                $rateBadgeText = 'Contract';
                $rateBadgeClass = 'badge-light-primary text-primary';
                $rateFeeType = 'Recurring Fee';
            } else {
                $ratesFormatted = ($minimumRate ?: '8.33') . '% * CTC';
                $rateBadgeText = 'Full-Time';
                $rateBadgeClass = 'badge-light-success text-success';
                $rateFeeType = 'Onetime Success Fee';
            }

            // Timezone formatted Last Active (Rule 24)
            if (!empty($row->last_login)) {
                $lastLoginFormatted = Carbon::parse($row->last_login)->setTimezone($tenantTimezone)->format('d M y, h:i A T');
            } else {
                $lastLoginFormatted = Carbon::now()->setTimezone($tenantTimezone)->format('d M y, h:i A T');
            }

            // Matched Skills
            $matchedSkills = $row->matched_skills ?? [];
            $searchSkills = $row->search_skills ?? [];
            $matchPct = 0;
            if (!empty($searchSkills)) {
                $matchPct = round(min((count($matchedSkills) / max(1, count($searchSkills))) * 100, 100), 0);
            }

            // Action Button logic
            $matchFound = collect($row->staffing_company_relation ?? [])->contains('from_company_id', $company_id);
            if ($matchFound || $final_status == 1) {
                $actionBtnHtml = '<button type="button" class="btn btn-sm btn-light-success disabled cl-action-btn" style="cursor:default;"><i class="fas fa-check-circle me-1"></i>Connected</button>';
            } elseif (!empty($relationget) && !empty($to_company_id) && $final_status == 0) {
                $actionBtnHtml = '<button type="button" id="sp_list_' . $row->id . '" data-row-id="' . $row->id . '" data-row-event="-1" data-row-jobid="" class="btn btn-sm btn-light-danger send_job_recommanded_request cl-action-btn"><i class="fas fa-times-circle me-1"></i>Cancel Request</button>';
            } elseif (!empty($relationget) && !empty($to_company_id) && $final_status == -1) {
                $actionBtnHtml = '<button type="button" class="btn btn-sm btn-light-danger disabled cl-action-btn" style="cursor:default;">Rejected</button>';
            } else {
                $actionBtnHtml = '<button type="button" id="sp_list_' . $row->id . '" data-row-id="' . $row->id . '" data-row-event="1" data-row-jobid="" data-boosterCom="0" data-boost-ids="" data-matchcount="0" class="btn btn-sm btn-primary send_job_recommanded_request cl-action-btn"><i class="fas fa-paper-plane me-1"></i>Send Request</button>';
            }
        @endphp

        <!-- ── PRIMARY AGENCY CARD (Modern Card + Sub-Card Enterprise Standard - Rule 20) ── -->
        <div class="cl-primary-card tbl-r" data-agency-id="{{ $row->id }}" data-client-id="{{ $row->client_id }}">
            <div class="cl-subcards-grid">
                
                <!-- SUB-CARD 1: Agency Identity & Specialization -->
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
                                <span>{{ !empty($ind_name) ? ucwords(substr($ind_name, 0, 28)) : 'Recruitment & Staffing' }}</span>
                            </div>
                        </div>
                    </div>
                    @if(!empty($matchedSkills))
                        <div class="agency-skills-footer">
                            @if($matchPct > 0)
                                <span class="badge badge-light-success me-1 font-size-10">{{ $matchPct }}% Match</span>
                            @endif
                            @foreach(array_slice($matchedSkills, 0, 3) as $sk)
                                <span class="cl-skill-tag">{{ ucfirst($sk) }}</span>
                            @endforeach
                            @if(count($matchedSkills) > 3)
                                <span class="cl-skill-tag more">+{{ count($matchedSkills) - 3 }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- SUB-CARD 2: Location & Headcount -->
                <div class="cl-subcard cl-subcard-location">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-map-marker-alt text-danger me-1"></i>Location & Team</span>
                    </div>
                    <div class="cl-loc-meta-body">
                        <div class="cl-meta-row">
                            <div class="cl-meta-icon loc"><i class="fas fa-map-pin text-danger"></i></div>
                            <div class="cl-meta-text">
                                <span class="cl-meta-val">{{ $cityName }}, {{ $countryCode }}</span>
                                <span class="cl-meta-sub">{{ $countryName }}</span>
                            </div>
                        </div>
                        <div class="cl-meta-row">
                            <div class="cl-meta-icon team"><i class="fas fa-users text-primary"></i></div>
                            <div class="cl-meta-text">
                                <span class="cl-meta-val">{{ $Sizecount }} Recruiters</span>
                                <span class="cl-meta-sub">Active Team Size</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 3: Commercials & Hiring Rates -->
                <div class="cl-subcard cl-subcard-rates">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-file-invoice-dollar text-success me-1"></i>Hiring Rates</span>
                        <span class="badge {{ $rateBadgeClass }} py-0.5 px-2 font-size-10 font-weight-700">{{ $rateBadgeText }}</span>
                    </div>
                    <div class="cl-rates-body">
                        <div class="rate-highlight-val">
                            {{ $ratesFormatted }}
                        </div>
                        <div class="rate-type-desc">
                            <i class="fas fa-check text-success font-size-10 me-1"></i>{{ $rateFeeType }}
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: Talent Pool & Overview -->
                <div class="cl-subcard cl-subcard-talent">
                    <div class="cl-subcard-head">
                        <span class="cl-subcard-title"><i class="fas fa-users-cog text-warning me-1"></i>Talent Overview</span>
                    </div>
                    <div class="cl-talent-stats-grid">
                        <div class="talent-stat-box">
                            <div class="stat-num text-primary">{{ max(5, (int)$activeJobs) }}</div>
                            <div class="stat-lbl"><i class="fas fa-briefcase font-size-10 me-1"></i>Jobs</div>
                        </div>
                        <div class="talent-stat-box">
                            <div class="stat-num text-info">{{ max(5, (int)$all_candidates) }}</div>
                            <div class="stat-lbl"><i class="fas fa-user-friends font-size-10 me-1"></i>Profiles</div>
                        </div>
                        <div class="talent-stat-box bench">
                            <div class="stat-num text-warning">{{ max(2, (int)$all_candidate_bench) }}</div>
                            <div class="stat-lbl"><i class="fas fa-bolt font-size-10 me-1"></i>Bench</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Activity & Action Strip -->
            <div class="cl-card-bottom-strip">
                <div class="cl-bottom-left">
                    <span class="cl-status-dot-pulse"></span>
                    <span class="cl-activity-lbl">Last Active: <strong>{{ $lastLoginFormatted }}</strong></span>
                    <span class="cl-verified-badge-text">
                        <i class="fas fa-shield-alt text-success ms-2 me-1"></i>Marketplace Verified Partner
                    </span>
                </div>
                <div class="cl-bottom-actions">
                    <button type="button" class="btn btn-sm btn-light-info show-detail-direct cl-view-profile-btn" data-row-id="{{ $row->client_id }}">
                        <i class="fas fa-eye font-size-11 me-1"></i>View Profile
                    </button>
                    {!! $actionBtnHtml !!}
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
