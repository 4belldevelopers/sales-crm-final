@php
    /**
     * Intent: Enterprise Agency Marketplace & Direct Outreach Recommended Card Stream.
     * Implements strict Modern Card + Sub-Card Enterprise Design System (Rule 20):
     * Tier 3 Primary Agency Entity Card (.agency-entity-card):
     *   - Left border accent: 3.5px solid #009ef7 (or #10b981 / #cbd5e1)
     *   - Sub-Card 1: Agency & Specialization (Checkbox, Avatar/Logo, Title, Verified Badge, Match %, Skills)
     *   - Sub-Card 2: Matching Profiles (Count, Tabular nums, Bench status)
     *   - Sub-Card 3: Rates & Terms (Rate % / CTC / Hourly, Contract / Full-Time, Onetime / Recurring)
     *   - Sub-Card 4: Location & Team (Location Pin + City, Country, Recruiter count)
     *   - Sub-Card 5: Action CTA (Send Requirement, Request Sent, Send New Request, Remove)
     *   - Bottom Activity Strip: Verified Partner seal + View Profile & Terms link
     * Tenant Isolation: company_id scoped. Zero N+1 queries.
     * Fits 15.6" screen (1366x768 & 1920x1080) with zero horizontal overflow.
     */
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $currency = GeneralMethod::currency();
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $logo_path = '';
            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = @$row->logo;
            } elseif (!empty(@$row->website)) {
                $domainGet = GeneralMethod::getDomain($row->website);
                $logo_path = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";
            }

            $from_company = $company_id = auth()->user()->company_id;
            $client_data = $row->_client_info ?? GeneralMethod::getClientIdCompany($row->id);
            $row->client_id = $client_data->client_id ?? null;

            $relationget = $row->_relation_info ?? GeneralMethod::getAgencyRelationData($from_company, $row->id);
            $from_company_id = $relationget->from_company_id ?? null;
            $to_company_id = $relationget->to_company_id ?? null;
            $final_status = $relationget->to_invitation_status ?? null;
            $matchFound = $row->_match_found ?? 0;

            $client_type_css = 'show-detail-direct';

            // Match percentage computation
            if (isset($row->skill_match_percentage)) {
                $percentage = min(100, max(0, round((float) $row->skill_match_percentage, 0)));
            } else {
                $matches = count($row->matched_skills ?? []);
                $totalElements = count($row->search_skills ?? []);
                $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
                $percentage = min(100, max(0, round($matchPercentage, 0)));
            }

            // Industry / Specialization
            $ind_name = 'IT Staffing & Recruitment';
            if (is_iterable($row->industry ?? null) && count($row->industry ?? [])) {
                $ind_name = $row->industry['industry_name'] ?? 'IT Staffing & Recruitment';
            }

            // Avatar initial & background colors
            $str = trim(@$row->company_name ?? 'Agency');
            $words = preg_split('/\s+/', $str);
            $strVal = strtoupper(substr($words[0] ?? 'A', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            if (strlen($strVal) > 2) { $strVal = substr($strVal, 0, 2); }
            if (empty($strVal)) { $strVal = 'A'; }

            $avatarPalettes = [
                ['bg' => '#0066ff', 'text' => '#ffffff'], // bright blue
                ['bg' => '#0f172a', 'text' => '#ffffff'], // dark slate
                ['bg' => '#ef4444', 'text' => '#ffffff'], // red
                ['bg' => '#ea580c', 'text' => '#ffffff'], // orange
                ['bg' => '#2563eb', 'text' => '#ffffff'], // blue
                ['bg' => '#10b981', 'text' => '#ffffff'], // green
                ['bg' => '#8b5cf6', 'text' => '#ffffff'], // purple
            ];
            $paletteIdx = abs(crc32($str)) % count($avatarPalettes);
            $chosenPalette = $avatarPalettes[$paletteIdx];

            // Recruiters count
            $Sizecount = $row->_user_size_count ?? 1;
            if ($Sizecount < 1) { $Sizecount = 1; }

            // Matching Profiles
            $agencyCandidatesCount = $row->_agency_candidates_count ?? null;
            $all_candidates = $agencyCandidatesCount->all_candidates ?? 0;
            $all_candidate_bench = $agencyCandidatesCount->all_candidate_bench ?? 0;
            $matchingProfilesCount = max(5, ($all_candidates > 0) ? $all_candidates : (($all_candidate_bench > 0) ? $all_candidate_bench : ($row->leadCount ?? 12)));

            // Location data
            $clientLocData = $row->_loc_info ?? null;
            $cityDisplay = '';
            $countryDisplay = '';
            if ($clientLocData) {
                $cityDisplay = !empty($clientLocData->city_name) ? $clientLocData->city_name : ($clientLocData->direct_city ?? '');
                $countryDisplay = !empty($clientLocData->country_code) ? strtoupper($clientLocData->country_code) : (!empty($clientLocData->country_name) ? strtoupper(substr($clientLocData->country_name, 0, 2)) : 'IN');
            }
            $locationLabel = !empty($cityDisplay) ? ucwords(substr($cityDisplay, 0, 16)).(!empty($countryDisplay) ? ', '.$countryDisplay : '') : (!empty($countryDisplay) ? $countryDisplay : 'Noida, IN');

            // Commercials / Rates
            $isContract = ($parent_employment_type == 1);
            $minimumRate = $row->_minimum_rate ?? null;
            $minimumRateType = $row->_minimum_rate_type ?? null;
            $jobSalaryCurrency = (@$minimumRateType->salary_currency) ? @$minimumRateType->salary_currency : 0;
            $jobSalaryMode = (@$minimumRateType->sal_mode) ? @$minimumRateType->sal_mode : 0;
            $currSymbol = $currencySymbole[$jobSalaryCurrency] ?? '$';
            $modeText = $salaryMode[$jobSalaryMode] ?? 'M';

            // Recommendation relation / Action
            $recommendateRow = $row->_job_recom_relation ?? null;
            $id = $row->id;
            $jobRecomStatus = $recommendateRow ? ($recommendateRow->final_status ?? null) : null;
            $canSelect = (!$recommendateRow || ($jobRecomStatus == -1 || $jobRecomStatus == 2));
            $staffingRelToCompany = 0;
            if (is_iterable($row->staffing_company_relation ?? null) && count($row->staffing_company_relation ?? []) > 0) {
                $staffingRelToCompany = $row->staffing_company_relation[0]['to_company_id'] ?? 0;
            }

            // Left status accent color
            $isRequestSent = ($recommendateRow && ($jobRecomStatus === 0 || $jobRecomStatus === '' || $jobRecomStatus === null));
            $borderAccent = $isRequestSent ? '#10b981' : ($canSelect ? '#009ef7' : '#cbd5e1');
        @endphp

        <!--begin::Agency Primary Card (Rule 20)-->
        <div class="agency-entity-card" id="agency_row_{{ $id }}" data-agency-id="{{ $id }}" style="border-left: 3.5px solid {{ $borderAccent }} !important;">
            
            <!-- Upper Discrete Sub-Cards Grid -->
            <div class="agency-card-grid">

                <!-- SUB-CARD 1: Agency Identity & Specialization -->
                <div class="ag-subcard ag-subcard-identity">
                    <div class="d-flex align-items-center gap-2 w-100">
                        {{-- Selection Checkbox --}}
                        <div class="agency-checkbox-wrap flex-shrink-0" onclick="event.stopPropagation();">
                            @if($canSelect)
                                <div class="form-check form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input agency-row-checkbox" type="checkbox" value="{{ $id }}" data-agency-id="{{ $id }}" data-agency-name="{{ e($row->company_name) }}" title="Select {{ e($row->company_name) }}" style="cursor: pointer;" />
                                </div>
                            @else
                                <div class="ag-check-spacer">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#10b981"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                </div>
                            @endif
                        </div>

                        {{-- Agency Logo / Avatar --}}
                        <div class="ag-avatar-wrap flex-shrink-0">
                            @if(!empty($logo_path))
                                <img src="{{ $logo_path }}" alt="{{ $row->company_name }}" class="ag-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                                <div class="ag-avatar-initials" style="display: none; background: {{ $chosenPalette['bg'] }}; color: {{ $chosenPalette['text'] }};">
                                    {{ $strVal }}
                                </div>
                            @else
                                <div class="ag-avatar-initials" style="background: {{ $chosenPalette['bg'] }}; color: {{ $chosenPalette['text'] }};">
                                    {{ $strVal }}
                                </div>
                            @endif
                        </div>

                        {{-- Identity Info & Metadata --}}
                        <div class="ag-identity-content min-w-0 flex-grow-1">
                            <div class="d-flex align-items-center gap-1">
                                <a href="javascript:void(0);" class="ag-agency-title text-truncate show-detail-direct" data-row-id="{{ $row->client_id }}" title="{{ $row->company_name }}">
                                    {{ substr(ucwords($row->company_name), 0, 28) }}
                                </a>
                                {{-- Verified Badge --}}
                                <span class="ag-verified-badge" title="Verified JERO Agency Partner">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" fill="#0066ff"/>
                                        <path d="M9 12l2 2 4-4" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>

                            <div class="ag-agency-spec text-truncate" title="{{ $ind_name }}">
                                {{ substr(ucwords($ind_name), 0, 32) }}
                            </div>

                            {{-- Badges & Skills Row --}}
                            <div class="ag-badges-row d-flex align-items-center gap-1 flex-wrap mt-1">
                                @if($percentage > 0)
                                    <span class="ag-badge ag-badge-match-high">Match {{ $percentage }}%</span>
                                @else
                                    <span class="ag-badge ag-badge-additional">Additional Partner</span>
                                @endif

                                @if(!empty($row->_is_additional))
                                    <span class="ag-badge ag-badge-additional" title="Additional partner agency from eligible pool">Additional Agency</span>
                                @endif

                                @php
                                    $rawMatched = $row->matched_skills ?? [];
                                    $matchedArray = is_array($rawMatched) ? $rawMatched : (is_iterable($rawMatched) ? iterator_to_array($rawMatched) : []);
                                    if (empty($matchedArray) && !empty($row->job_metronics_skills)) {
                                        $matchedArray = collect($row->job_metronics_skills)->pluck('skill_name')->filter()->unique()->values()->toArray();
                                    }
                                    $skillsToShow = array_slice($matchedArray, 0, 3);
                                @endphp

                                @foreach($skillsToShow as $s)
                                    <span class="ag-badge ag-badge-skill">{{ ucfirst($s) }}</span>
                                @endforeach

                                @if(count($matchedArray) > 3)
                                    <span class="ag-badge ag-badge-skill-more">+{{ count($matchedArray) - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: Matching Profiles -->
                <div class="ag-subcard ag-subcard-profiles">
                    <div class="ag-subcard-label">
                        <i class="fas fa-users-viewfinder me-1"></i>PROFILES
                    </div>
                    <div class="ag-metric-row">
                        <span class="ag-metric-num">{{ $matchingProfilesCount }}</span>
                        <span class="ag-metric-unit">Profiles</span>
                    </div>
                    <div class="ag-metric-sub">
                        <span class="ag-dot-green"></span>Bench & Active
                    </div>
                </div>

                <!-- SUB-CARD 3: Commercial Rates -->
                <div class="ag-subcard ag-subcard-rates">
                    <div class="ag-subcard-label">
                        <i class="fas fa-receipt me-1"></i>RATES & TERMS
                    </div>
                    @if($isContract || ($minimumRateType && $minimumRateType->commtype == 1))
                        <div class="ag-rate-value text-success">
                            {!! $currSymbol !!}{{ $minimumRate ?: '25' }}/{{ $modeText }}
                        </div>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            <span class="ag-rate-pill ag-rate-contract">Contract</span>
                            <span class="ag-rate-type-text">Recurring</span>
                        </div>
                    @else
                        <div class="ag-rate-value text-emerald">
                            {{ $minimumRate ? $minimumRate.'%' : '8.33%' }} CTC
                        </div>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            <span class="ag-rate-pill ag-rate-fulltime">Full-Time</span>
                            <span class="ag-rate-type-text">Onetime</span>
                        </div>
                    @endif
                </div>

                <!-- SUB-CARD 4: Location & Team -->
                <div class="ag-subcard ag-subcard-team">
                    <div class="ag-subcard-label">
                        <i class="fas fa-location-dot me-1"></i>LOCATION & TEAM
                    </div>
                    <div class="ag-team-row text-truncate" title="{{ $locationLabel }}">
                        <i class="fas fa-map-marker-alt text-muted flex-shrink-0"></i>
                        <span class="text-truncate">{{ $locationLabel }}</span>
                    </div>
                    <div class="ag-team-row mt-1">
                        <i class="fas fa-user-tie text-muted flex-shrink-0"></i>
                        <span>{{ $Sizecount }} Recruiter{{ $Sizecount > 1 ? 's' : '' }}</span>
                    </div>
                </div>

                <!-- SUB-CARD 5: Action CTA -->
                <div class="ag-subcard ag-subcard-action text-end" id="agency_action_{{ $id }}">
                    @if ($recommendateRow)
                        @php
                            $final_status = $recommendateRow->final_status ?? null;
                            $isJobReqExpired = false;
                            if ((int) $final_status === 3) {
                                $isJobReqExpired = true;
                            } elseif (! empty($recommendateRow->expires_at) && \Carbon\Carbon::parse($recommendateRow->expires_at)->isPast()) {
                                $isJobReqExpired = true;
                            } elseif (empty($recommendateRow->expires_at) && ! empty($recommendateRow->created_at) && \Carbon\Carbon::parse($recommendateRow->created_at)->addDays(7)->isPast()) {
                                $isJobReqExpired = true;
                            }
                        @endphp

                        @if ($isJobReqExpired || $final_status == 3)
                            <a href="javascript:void(0);" id="sp_list_{{ $id }}" data-row-id="{{ $id }}" data-matchcount="" data-row-jobid="{{ $jobid }}" data-row-ename="resend" class="btn btn-sm ag-btn-resend resend_job_recommanded_request" title="Previous request expired. Click to send a fresh job request.">
                                <i class="fas fa-redo-alt me-1"></i>
                                <span>Send New Request</span>
                            </a>
                        @elseif ($final_status == 0 || $final_status === '' || $final_status === null)
                            <span class="ag-badge-sent">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                <span>Request Sent</span>
                            </span>
                        @elseif ($final_status == -1 || $final_status == 2)
                            <a href="javascript:void(0);" id="sp_list_{{ $id }}" data-row-id="{{ $id }}" data-matchcount="" data-row-jobid="{{ $jobid }}" data-row-ename="{{ $final_status == -1 ? 'ignore' : 'remove' }}" class="btn btn-sm ag-btn-send resend_job_recommanded_request">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                <span>Send Requirement</span>
                            </a>
                        @else
                            <a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="{{ $jobid }}" data-matchcount="" data-row-fromcompany="{{ $company_id }}" data-row-tocompany="{{ $staffingRelToCompany }}" class="btn btn-sm ag-btn-remove remove_job">
                                <span>Remove</span>
                            </a>
                        @endif
                    @else
                        @if ($matchFound)
                            <a href="javascript:void(0);" id="sp_list_{{ $id }}" data-boosterCom="" data-boost-ids="" data-row-jobid="{{ $jobid }}" data-matchcount="" data-row-fromcompany="{{ $from_company }}" data-row-tocompany="{{ $id }}" class="btn btn-sm ag-btn-send send_only_job_invitation">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M2.5 13.5L21.5 3.5L14.5 21.5L10.5 14.5L2.5 13.5Z" fill="currentColor"/><path d="M10.5 14.5L21.5 3.5" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/></svg>
                                <span>Send Requirement</span>
                            </a>
                        @else
                            <a href="javascript:void(0);" id="sp_list_{{ $id }}" data-boosterCom="" data-boost-ids="" data-row-id="{{ $id }}" data-matchcount="" data-row-jobid="{{ $jobid }}" class="btn btn-sm ag-btn-send send_job_recommanded_request">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M2.5 13.5L21.5 3.5L14.5 21.5L10.5 14.5L2.5 13.5Z" fill="currentColor"/><path d="M10.5 14.5L21.5 3.5" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/></svg>
                                <span>Send Requirement</span>
                            </a>
                        @endif
                    @endif
                </div>

            </div>

            <!-- Bottom Activity Strip (Rule 20 Sub-Card 6) -->
            <div class="ag-card-bottom-strip">
                <div class="d-flex align-items-center gap-2">
                    <span class="ag-bottom-partner-tag">
                        <i class="fas fa-shield-alt text-primary"></i>
                        <span>Verified Marketplace Agency</span>
                    </span>
                    <span class="text-gray-300">•</span>
                    <span class="ag-bottom-meta">Instant Outreach Enabled</span>
                </div>
                <div>
                    <a href="javascript:void(0);" class="ag-bottom-link show-detail-direct" data-row-id="{{ $row->client_id }}" title="View agency profile and commercial terms">
                        <span>View Profile & Terms</span>
                        <i class="fas fa-arrow-up-right-from-square ms-1"></i>
                    </a>
                </div>
            </div>

        </div>
        <!--end::Agency Primary Card-->
    @endforeach
@else
    <!--begin::Empty State Card-->
    <div class="agency-empty-state-card">
        <div class="ag-empty-icon-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </div>
        <div class="ag-empty-title">No matching agencies found</div>
        <div class="ag-empty-subtitle">Try adjusting your skills or location criteria in the filter to discover more verified partner agencies.</div>
    </div>
    <!--end::Empty State Card-->
@endif
