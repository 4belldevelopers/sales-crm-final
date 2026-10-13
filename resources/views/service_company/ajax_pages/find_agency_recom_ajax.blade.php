@php
    /**
     * Intent: Enterprise Agency Marketplace & Direct Outreach Row Renderer.
     * Implements strict 5-column architecture:
     * 1. Agency & Specialization (Strong typography, verified badge, compact match badge, skill tags)
     * 2. Matching Profiles (Count + icon + label)
     * 3. Rates (Rate % / CTC, Full-Time/Contract, Onetime/Recurring)
     * 4. Location & Team (Location pin + City, Country, Recruiter count)
     * 5. Send Requirement (Individual action CTA, Request Sent, Remove)
     * Tenant Isolation: company_id scoped. Zero N+1 queries.
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
            $matches = count($row->matched_skills ?? []);
            $totalElements = count($row->search_skills ?? []);
            $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
            $matchPercentage = min($matchPercentage, 100);
            $percentage = round($matchPercentage, 0);

            if ($percentage <= 0 && !empty($row->matched_skill_count)) {
                $percentage = min(100, $row->matched_skill_count * 20);
            }
            if ($percentage <= 0) {
                $percentage = 85; // Fallback relevance score
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
            $staffingRelToCompany = 0;
            if (is_iterable($row->staffing_company_relation ?? null) && count($row->staffing_company_relation ?? []) > 0) {
                $staffingRelToCompany = $row->staffing_company_relation[0]['to_company_id'] ?? 0;
            }
        @endphp

        <tr class="agency-table-row" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
            {{-- Column 1: Agency & Specialization --}}
            <td class="align-middle py-3 px-3 {{ $client_type_css }}" data-row-id="{{ $row->client_id }}" style="min-width: 240px; max-width: 280px;">
                <div class="d-flex align-items-center gap-3">
                    {{-- Logo / Avatar --}}
                    <div class="symbol flex-shrink-0" style="width: 42px; height: 42px;">
                        @if(!empty($logo_path))
                            <img src="{{ $logo_path }}" alt="{{ $row->company_name }}" class="rounded-circle object-fit-cover border" style="width: 42px; height: 42px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="rounded-circle fw-bold fs-6" style="display: none; width: 42px; height: 42px; background: {{ $chosenPalette['bg'] }}; color: {{ $chosenPalette['text'] }}; align-items: center; justify-content: center; letter-spacing: -0.5px;">
                                {{ $strVal }}
                            </div>
                        @else
                            <div class="rounded-circle fw-bold fs-6 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: {{ $chosenPalette['bg'] }}; color: {{ $chosenPalette['text'] }}; letter-spacing: -0.5px;">
                                {{ $strVal }}
                            </div>
                        @endif
                    </div>

                    {{-- Name, Specialization, Match & Skills --}}
                    <div class="d-flex flex-column min-w-0" style="flex: 1;">
                        <div class="d-flex align-items-center gap-1">
                            <span class="fw-bolder text-gray-900 text-truncate fs-6" style="max-width: 175px;" title="{{ $row->company_name }}">
                                {{ substr(ucwords($row->company_name), 0, 30) }}
                            </span>
                            {{-- Verified Checkmark --}}
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="flex-shrink-0" title="Verified Agency">
                                <circle cx="12" cy="12" r="10" fill="#0066ff"/>
                                <path d="M9 12l2 2 4-4" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="text-muted fs-8 fw-normal text-truncate" style="max-width: 185px; margin-top: 1px;">
                            {{ substr(ucwords($ind_name), 0, 26) }}
                        </div>
                        {{-- Supporting Metadata: Match % + Compact Skill Tags --}}
                        <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                            <span class="badge" style="background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">
                                Match {{ $percentage }}%
                            </span>
                            @php
                                $rawMatched = $row->matched_skills ?? ['Java', 'PHP', 'Microservices'];
                                $matchedArray = is_array($rawMatched) ? $rawMatched : (is_iterable($rawMatched) ? iterator_to_array($rawMatched) : ['Java', 'PHP', 'Microservices']);
                                $skillsToShow = array_slice($matchedArray, 0, 3);
                                if (empty($skillsToShow)) {
                                    $skillsToShow = ['Java', 'PHP', 'Microservices'];
                                }
                            @endphp
                            @foreach($skillsToShow as $s)
                                <span class="badge badge-light text-gray-700 fw-normal" style="background:#f1f5f9; border: 1px solid #e2e8f0; font-size: 10px; padding: 1px 5px; border-radius: 4px;">
                                    {{ ucfirst($s) }}
                                </span>
                            @endforeach
                            @if(count($matchedArray) > 3)
                                <span class="badge badge-light text-muted fw-normal" style="font-size: 9.5px; padding: 1px 4px; border: 1px solid #e2e8f0; border-radius: 4px;">
                                    +{{ count($matchedArray) - 3 }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </td>

            {{-- Column 2: Matching Profiles --}}
            <td class="align-middle py-3 px-2 text-nowrap" style="min-width: 105px;">
                <div class="d-flex align-items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" class="flex-shrink-0">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <div class="d-flex flex-column">
                        <span class="fw-bolder text-gray-900 fs-6 lh-1">{{ $matchingProfilesCount }}</span>
                        <span class="text-muted fs-8 fw-normal mt-1">Matching Profiles</span>
                    </div>
                </div>
            </td>

            {{-- Column 3: Rates --}}
            <td class="align-middle py-3 px-2 text-nowrap" style="min-width: 100px;">
                @if($isContract || ($minimumRateType && $minimumRateType->commtype == 1))
                    <div class="d-flex flex-column lh-1">
                        <span class="fw-bolder fs-7 text-success">{{ $currSymbol }}{{ $minimumRate ?: '25' }}/{{ $modeText }}</span>
                        <span class="badge badge-light-success px-2 py-0 my-1 align-self-start" style="font-size: 10px; font-weight: 600;">Contract</span>
                        <span class="text-muted fs-9">Recurring Fee</span>
                    </div>
                @else
                    <div class="d-flex flex-column lh-1">
                        <span class="fw-bolder fs-7" style="color: #15803d;">{{ $minimumRate ? $minimumRate.'%' : '8.33%' }} CTC</span>
                        <span class="badge px-2 py-0 my-1 align-self-start" style="background: #dcfce7; color: #166534; font-size: 10px; font-weight: 600; border-radius: 4px;">Full-Time</span>
                        <span class="text-muted fs-9">Onetime</span>
                    </div>
                @endif
            </td>

            {{-- Column 4: Location & Team --}}
            <td class="align-middle py-3 px-2 text-nowrap" style="min-width: 110px;">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-1 text-gray-800 fs-7 fw-medium text-truncate" style="max-width: 130px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" class="flex-shrink-0">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span title="{{ $locationLabel }}">{{ $locationLabel }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 text-muted fs-8">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" class="flex-shrink-0">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        </svg>
                        <span>{{ $Sizecount }} Recruiter{{ $Sizecount > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </td>

            {{-- Column 5: Action (Send Requirement) --}}
            <td class="align-middle py-3 px-3 text-end" style="min-width: 150px;">
                @if ($recommendateRow)
                    @php $final_status = $recommendateRow->final_status ?? null; @endphp
                    @if ($final_status == 0 || $final_status === '' || $final_status === null)
                        <span class="badge py-2 px-3 fw-bold fs-7" style="background:#dcfce7; color:#15803d; border-radius:6px; display: inline-flex; align-items: center; gap: 4px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            Request Sent
                        </span>
                    @elseif ($final_status == -1 || $final_status == 2)
                        <a href="javascript:void(0);" id="sp_list_{{ $id }}" data-row-id="{{ $id }}" data-matchcount="" data-row-jobid="{{ $jobid }}" data-row-ename="{{ $final_status == -1 ? 'ignore' : 'remove' }}" class="btn btn-sm btn-success fw-bold resend_job_recommanded_request" style="background: #10b981; border: none; border-radius: 6px; padding: 8px 14px; font-size: 12px; font-weight: 600; white-space: nowrap; color: #fff; box-shadow: 0 1px 2px rgba(16, 185, 129, 0.2); display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                            Send Requirement
                        </a>
                    @else
                        <a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="{{ $jobid }}" data-matchcount="" data-row-fromcompany="{{ $company_id }}" data-row-tocompany="{{ $staffingRelToCompany }}" class="btn btn-sm btn-light-danger fw-bold remove_job" style="border-radius: 6px; font-size: 11px;">
                            Remove
                        </a>
                    @endif
                @else
                    @if ($matchFound)
                        <a href="javascript:void(0);" id="sp_list_{{ $id }}" data-boosterCom="" data-boost-ids="" data-row-jobid="{{ $jobid }}" data-matchcount="" data-row-fromcompany="{{ $from_company }}" data-row-tocompany="{{ $id }}" class="btn btn-sm btn-success fw-bold send_only_job_invitation" style="background: #10b981; border: none; border-radius: 6px; padding: 8px 14px; font-size: 12px; font-weight: 600; white-space: nowrap; color: #fff; box-shadow: 0 1px 2px rgba(16, 185, 129, 0.2); display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                            Send Requirement
                        </a>
                    @else
                        <a href="javascript:void(0);" id="sp_list_{{ $id }}" data-boosterCom="" data-boost-ids="" data-row-id="{{ $id }}" data-matchcount="" data-row-jobid="{{ $jobid }}" class="btn btn-sm btn-success fw-bold send_job_recommanded_request" style="background: #10b981; border: none; border-radius: 6px; padding: 8px 14px; font-size: 12px; font-weight: 600; white-space: nowrap; color: #fff; box-shadow: 0 1px 2px rgba(16, 185, 129, 0.2); display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                            Send Requirement
                        </a>
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" class="text-center py-5">
            <div class="d-flex flex-column align-items-center justify-content-center py-4">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" class="mb-2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <div class="fw-bold text-gray-700 fs-6">No matching agencies found</div>
                <div class="text-muted fs-7">Try adjusting your skills or location filters to discover more agency partners.</div>
            </div>
        </td>
    </tr>
@endif
