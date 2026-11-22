@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $salaryMode = $salaryMode ?? GeneralMethod::salaryMode();
    $currencySymbole = $currencySymbole ?? GeneralMethod::currencySymbole();
    $company_id = $company_id ?? auth()->user()->company_id;
    $tenantTimezone = $tenantTimezone ?? (GeneralMethod::defaultTimeZone($company_id) ?: config('app.timezone', 'UTC'));
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;

            /* ── Candidate Word Limit Helper ── */
            $limitWords = function ($text, $limit) {
                $words = preg_split('/\s+/', trim((string) $text), -1, PREG_SPLIT_NO_EMPTY);
                if (count($words) <= $limit) {
                    return trim((string) $text);
                }
                return implode(' ', array_slice($words, 0, $limit)) . '...';
            };

            /* ── Avatar Styling ── */
            $strVal = 'T';
            $bgTextDecore = 'fs-4 bg-light-primary text-primary';
            $rawFullName = trim((string) @$row->full_name);
            if (!empty($rawFullName)) {
                $firstChar = strtolower($rawFullName[0]);
                $strVal = strtoupper($firstChar);
                $ordVal = ord($firstChar);
                if ($ordVal >= 97 && $ordVal <= 101) {
                    $bgTextDecore = 'fs-4 bg-light-danger text-danger';
                } elseif ($ordVal >= 102 && $ordVal <= 106) {
                    $bgTextDecore = 'fs-4 bg-light-warning text-warning';
                } elseif ($ordVal >= 107 && $ordVal <= 111) {
                    $bgTextDecore = 'fs-4 bg-light-primary text-primary';
                } elseif ($ordVal >= 112 && $ordVal <= 116) {
                    $bgTextDecore = 'fs-4 bg-light-success text-success';
                } elseif ($ordVal >= 117 && $ordVal <= 121) {
                    $bgTextDecore = 'fs-4 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-4 bg-light-info text-info';
                }
            }
            $profile_sidebar_css = 'pbench_show_application_detail';

            /* ── Display Names ── */
            $displayFullName = $limitWords(ucwords(@$row->full_name ?: 'Unnamed Talent'), 25);
            $displayDesignation = $limitWords(ucwords(@$row->current_designation ?: 'Talent'), 25);

            /* ── Bench Company Name ── */
            $benchCompanyName = trim((string) (@$row->company_name ?: @$row->current_company ?: ''));
            if ($benchCompanyName === '') {
                $benchCompanyName = 'Bench Talent';
            }
            $displayBenchCompany = $limitWords($benchCompanyName, 25);

            /* ── Status Pill & Left Accent Border ── */
            $statusId = (int) ($row->status ?? 2);
            $statusName = $row->master_status_name ?? ($statusId === 1 ? 'Pool' : 'Bench');
            if ($statusId == 1) { // Pool
                $statusBorderAccent = '#7c3aed';
                $statusPillStyle = 'background: #faf5ff !important; color: #7c3aed !important; border: 1px solid #ddd6fe !important;';
            } elseif ($statusId == 2) { // Bench
                $statusBorderAccent = '#2563eb';
                $statusPillStyle = 'background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important;';
            } elseif ($statusId == 3) { // Placed
                $statusBorderAccent = '#059669';
                $statusPillStyle = 'background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important;';
            } elseif ($statusId == 4) { // Hold
                $statusBorderAccent = '#ea580c';
                $statusPillStyle = 'background: #fff7ed !important; color: #ea580c !important; border: 1px solid #fed7aa !important;';
            } else {
                $statusBorderAccent = '#2563eb';
                $statusPillStyle = 'background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important;';
            }

            /* ── Experience ── */
            $experience = trim((string) @$row->total_exp);
            if (strpos($experience, '.') !== false) {
                $parts = explode('.', $experience);
                $formattedExperience = (empty($parts[1]) || $parts[1] === '00' || $parts[1] === '0') ? $parts[0] : $experience;
            } else {
                $formattedExperience = $experience ?: '0';
            }

            $relExperience = trim((string) @$row->rel_exp);
            if (strpos($relExperience, '.') !== false) {
                $rParts = explode('.', $relExperience);
                $formattedRelExperience = (empty($rParts[1]) || $rParts[1] === '00' || $rParts[1] === '0') ? $rParts[0] : $relExperience;
            } else {
                $formattedRelExperience = $relExperience ?: '';
            }

            /* ── Bill Rate & Commercials ── */
            $billRateAmt = !empty($row->bill_rate_amount) ? (float) $row->bill_rate_amount : (!empty($row->ectc_yearly) ? (float) $row->ectc_yearly : 0);
            $billModeInt = (int) ($sal_mode ?? ($row->bill_rate_mode ?? 2)); // default hourly if not set
            $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
            $currency_code = $symbolsValue[$min_salary_currency ?? ($row->bill_rate_currency ?? 0)] ?? 'USD';
            $billFormatViewSalary = GeneralMethod::formatViewSalary($billRateAmt, $currency_code, $billModeInt);
            $plainCurrencySymbol = !empty($billFormatViewSalary->symbol) ? $billFormatViewSalary->symbol : (trim(strip_tags($currencySymbole[$billFormatViewSalary->currency] ?? '')) ?: '$');
            $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode ?? $billModeInt] ?? 'H';
            $billAmountClean = number_format((float) ($billFormatViewSalary->amount ?? $billRateAmt), 0);
            $isPaid = ((int) (@$row->comission_type ?? 2) === 2);

            /* ── Notice Period Resolution (Rule 6) ── */
            $noticeData = \App\Services\AppTrackerCardPresenter::resolveNoticePeriod($row);
            $npDisplay = $noticeData['display'] ?: 'NP: Immediate';
            if (($noticeData['classSuffix'] ?? '') === 'snp') {
                $npDisplay = str_replace('SNP LWD ', 'SNP LWD: ', $npDisplay);
            }
            $npClass = 'simf-notice-' . ($noticeData['classSuffix'] ?? 'immediate');

            /* ── Location Formatting ── */
            $locParts = array_filter([
                !empty($row->city_name) ? ucwords(trim($row->city_name)) : null,
                !empty($row->state_name) ? ucwords(trim($row->state_name)) : null,
                !empty($row->country_name) ? ucwords(trim($row->country_name)) : null,
            ]);
            $fullLocation = !empty($locParts) ? implode(', ', $locParts) : '—';
            $qualificationName = !empty($row->qualification_name) ? trim($row->qualification_name) : (!empty($row->qualification) ? 'DEGREE' : '—');

            /* ── Work Mode ── */
            $wm = strtolower((string) @$row->wm);
            $wmMap = ['remote' => 'Remote', 'hybrid' => 'Hybrid', 'office' => 'Work from office'];
            $wmClassMap = ['remote' => 'text-success', 'hybrid' => 'text-primary', 'office' => 'text-gray-700'];
            $wmIconMap = ['remote' => 'fa-solid fa-house-laptop text-success', 'hybrid' => 'fa-solid fa-building-user text-primary', 'office' => 'fa-solid fa-building text-muted'];
            $wmLabel = $wmMap[$wm] ?? 'Work from office';
            $wmClass = $wmClassMap[$wm] ?? 'text-gray-700';
            $wmIcon = $wmIconMap[$wm] ?? 'fa-solid fa-building text-muted';
            $outsourcing_type = (int) ($row->outsourcing_type ?? 1) === 2 ? 'Contract' : 'Full-time';

            /* ── Relationship Manager Resolution ── */
            $rmDesignation = '';
            if (!empty($row->hiring_hr)) {
                $hiringHr = (array) $row->hiring_hr;
                $hmCount = count($hiringHr);
                $firstHr = (object) ($hiringHr[0] ?? []);
                $managerName = !empty($firstHr->name) ? ucwords($firstHr->name) : 'Secondary Recruiter';
                $rmDesignation = !empty($firstHr->designation) ? ucwords($firstHr->designation) : '';
                $extra = $hmCount > 1 ? ' +' . ($hmCount - 1) : '';
            } else {
                $hmCount = 0;
                $managerName = 'Secondary Recruiter';
                $extra = '';
            }
            $displayManagerName = $limitWords($managerName . $extra, 20);
            $displayRmDesignation = !empty($rmDesignation) ? $limitWords($rmDesignation, 20) : 'Relationship Manager';

            /* ── Matching Jobs Counts (Direct, Internal, My Org) ── */
            $recomInfo = $benchRecomCounts[$row->id] ?? null;
            $recomDirectCount = (int) ($recomInfo['recommend_req_counter'] ?? 0);
            $recomInternalCount = (int) ($recomInfo['recommend_internal_req_counter'] ?? 0);
            $recomMyOrgCount = (int) ($recomInfo['recommend_my_org_req_counter'] ?? 0);

            /* ── Skills & Skill Tagging ── */
            $skillArr = array_values(array_filter(array_map('trim', explode(',', (string) ($row->skills ?? '')))));
            $candidateSkillStr = htmlspecialchars(implode(',', array_map('strtolower', $skillArr)));

            /* ── Date Added in Tenant Timezone (Rule 24) ── */
            $stageMovedAt = $row->updated_at ?? $row->created_at ?? null;
            $stageDateLabel = $stageMovedAt ? Carbon::parse($stageMovedAt)->setTimezone($tenantTimezone)->format('d M y, h:i A T') : '';

            /* ── Days on Bench ── */
            $benchDaysCount = null;
            if (!empty($row->created_at)) {
                $benchDaysCount = (int) Carbon::parse($row->created_at)->diffInDays(Carbon::now());
            }
            if ($benchDaysCount === null || $benchDaysCount < 0) {
                $benchDaysCount = 0;
            }
        @endphp

        <!-- Master Primary Talent Card (Rule 20 Modern Card + 6 Subcards Layout) -->
        <article class="cd-talent-card" data-bench-row-id="{{ $row->id }}" data-candidate-id="{{ $row->id }}" style="border-left: 3.5px solid {{ $statusBorderAccent }} !important;">
            <!-- Upper Sub-Cards Grid (6 Discrete Sections) -->
            <div class="cd-talent-card-grid">

                <!-- Sub-Card 1: CANDIDATE INFO -->
                <div class="cd-talent-subcard cd-subcard-talent js-open-cand-drawer cursor-pointer" data-row-id="{{ $row->id }}" title="Click to view Candidate Details">
                    <div class="cd-subcard-header">
                        <div class="d-flex align-items-center gap-1.5 min-w-0">
                            <label class="form-check form-check-sm form-check-custom m-0" style="cursor: pointer;" title="Select candidate" onclick="event.stopPropagation();">
                                <input class="form-check-input chk_list_item cursor-pointer" name="chk_list_item[]" type="checkbox"
                                    data-skills="{{ e($row->skills ?? '') }}"
                                    data-cname="{{ e($row->full_name ?? '') }}"
                                    value="{{ $row->id }}"
                                    onclick="event.stopPropagation();"
                                    style="width: 15px; height: 15px; margin: 0; flex-shrink: 0; border: 1.5px solid #94a3b8 !important; border-radius: 4px !important; background-color: #ffffff !important;"
                                    title="Select candidate" />
                            </label>
                            <span class="cd-subcard-label text-uppercase text-truncate" title="Candidate Info">CANDIDATE INFO</span>
                        </div>
                        <div class="d-flex align-items-center gap-1" onclick="event.stopPropagation();">
                            <span class="cd-status-badge {{ $profile_sidebar_css }} cursor-pointer" data-row-id="{{ $row->id }}" style="{{ $statusPillStyle }} font-size: 8.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; text-transform: uppercase !important; letter-spacing: 0.3px !important;">
                                {{ strtoupper($statusName) }}
                            </span>
                        </div>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <div class="d-flex align-items-start gap-2 min-w-0">
                            <!-- Candidate Avatar -->
                            <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="cd-talent-avatar {{ $profile_sidebar_css }} flex-shrink-0" title="{{ ucwords(@$row->full_name) }}" style="width: 36px !important; height: 36px !important; min-width: 36px !important; border-radius: 8px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; font-weight: 700 !important; font-size: 13.5px !important; text-decoration: none !important; overflow: hidden !important; flex-shrink: 0 !important; border: 1px solid rgba(15, 23, 42, 0.08) !important; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;">
                                @if (!empty($row->photo))
                                    <img src="{{ $row->photo }}" alt="{{ e($displayFullName) }}" style="width: 100% !important; height: 100% !important; border-radius: 8px !important; object-fit: cover !important;" />
                                @else
                                    <div class="symbol-label {{ $bgTextDecore }} w-100 h-100 d-flex align-items-center justify-content-center fw-bolder" style="border-radius: 8px; font-size: 13.5px;">{{ $strVal }}</div>
                                @endif
                            </a>
                            <div class="d-flex flex-column min-w-0 flex-grow-1 overflow-hidden">
                                <!-- Candidate Name -->
                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                                    class="cd-talent-name {{ $profile_sidebar_css }}"
                                    title="{{ ucwords(@$row->full_name) }}"
                                    style="font-size: 12.5px !important; font-weight: 700 !important; color: #0f172a !important; line-height: 1.25 !important; display: block !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; text-decoration: none !important;">
                                    {{ $displayFullName }}
                                </a>
                                <!-- Designation -->
                                <div class="cd-talent-role"
                                    title="{{ ucwords(@$row->current_designation) }}"
                                    style="font-size: 11px !important; font-weight: 500 !important; color: #475569 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; line-height: 1.25 !important; margin-top: 1px !important;">
                                    {{ $displayDesignation !== '' ? $displayDesignation : '—' }}
                                </div>
                                <!-- Bench Company Name -->
                                <div class="cd-talent-company text-truncate"
                                    title="Bench Company: {{ $benchCompanyName }}"
                                    style="font-size: 10px !important; font-weight: 500 !important; color: #64748b !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; line-height: 1.25 !important; margin-top: 2px !important;">
                                    <i class="fa-regular fa-building me-1 text-muted" style="width: 12px; text-align: center; font-size: 9px !important;"></i>
                                    <span style="font-size: 10px !important;">{{ $displayBenchCompany !== '' ? $displayBenchCompany : '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 2: BILL RATE -->
                <div class="cd-talent-subcard cd-subcard-comp-exp js-open-cand-drawer cursor-pointer" data-row-id="{{ $row->id }}" title="Click to view Commercial Details">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate">BILL RATE</span>
                        <!-- Service Category: Paid / Free -->
                        <span class="badge" title="Service Category: {{ $isPaid ? 'Paid Services' : 'Free Services' }}" style="font-size: 8.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; {{ $isPaid ? 'background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important;' : 'background: #f1f5f9 !important; color: #64748b !important; border: 1px solid #e2e8f0 !important;' }}">
                            <i class="fa-solid fa-circle-check me-1" style="font-size: 8px !important;"></i>{{ $isPaid ? 'Paid' : 'Free' }}
                        </span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <!-- Bill Rate Amount & Mode -->
                        <div class="cd-val-salary d-flex align-items-baseline gap-1" title="Bill Rate: {{ $plainCurrencySymbol }}{{ $billAmountClean }} / {{ $bill_rate_mode }}">
                            <strong style="font-size: 12px !important; font-weight: 700 !important; color: #0f172a !important; line-height: 1.25 !important;">{{ $plainCurrencySymbol }}{{ $billAmountClean }}</strong>
                            <span style="font-size: 10px !important; color: #64748b !important; font-weight: 500 !important;">/{{ mb_substr($bill_rate_mode, 0, 1) }}</span>
                        </div>
                        <!-- Experience Total & Relevant -->
                        <div class="cd-val-exp d-flex align-items-center gap-1 mt-0.5 text-truncate" title="Total Experience: {{ $formattedExperience }} Yrs{{ (!empty($formattedRelExperience) && $formattedRelExperience > 0) ? ' | Rel: ' . $formattedRelExperience . ' Yrs' : '' }}" style="font-size: 10px !important; font-weight: 500 !important; color: #475569 !important; line-height: 1.3 !important;">
                            <i class="fa-solid fa-briefcase me-1 text-muted" style="width: 12px; text-align: center; font-size: 9px !important;"></i>
                            <span style="font-size: 10px !important;"><strong style="color: #0f172a !important; font-size: 10px !important; font-weight: 700 !important;">{{ $formattedExperience !== '' ? $formattedExperience : '0' }} Y</strong> Total</span>
                            @if (!empty($formattedRelExperience) && (float)$formattedRelExperience > 0)
                                <span class="ms-1" style="color: #64748b !important; font-size: 9.5px !important;">(Rel <strong style="color: #334155 !important; font-size: 9.5px !important; font-weight: 600 !important;">{{ $formattedRelExperience }} Y</strong>)</span>
                            @endif
                        </div>
                        <!-- Notice Period -->
                        <div class="cd-val-avail d-flex align-items-center gap-1 mt-0.5 text-truncate" title="Notice Period: {{ $npDisplay }}" style="font-size: 10px !important; font-weight: 500 !important; color: #475569 !important; line-height: 1.3 !important;">
                            <i class="fa-regular fa-clock me-1 text-muted" style="width: 12px; text-align: center; font-size: 9.5px !important;"></i>
                            <span class="simf-notice-txt {{ $npClass }}" style="font-size: 10px !important; font-weight: 600 !important; color: #16a34a !important;">{{ $npDisplay }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 3: LOCATION & DEGREE -->
                <div class="cd-talent-subcard cd-subcard-avail-loc js-open-cand-drawer cursor-pointer" data-row-id="{{ $row->id }}" title="Click to view Location & Education">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate" style="font-size: 9.5px !important; font-weight: 700 !important;">LOCATION &amp; DEGREE</span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <!-- City, State, Country -->
                        <div class="cd-val-location text-truncate d-flex align-items-center" style="font-size: 11px !important; font-weight: 600 !important; line-height: 1.25 !important; color: #0f172a !important;" title="{{ $fullLocation }}">
                            <i class="fa-solid fa-location-dot me-1 text-muted" style="width: 13px; text-align: center; font-size: 9.5px !important;"></i>
                            <span class="text-truncate" style="font-size: 11px !important; font-weight: 600 !important; color: #0f172a !important;">{{ $fullLocation }}</span>
                        </div>
                        <!-- Work Mode -->
                        <div class="cd-val-wm text-truncate d-flex align-items-center mt-1" style="font-size: 10px !important; font-weight: 500 !important; color: #475569 !important; line-height: 1.25 !important;" title="Work Mode: {{ $wmLabel }}">
                            <i class="{{ $wmIcon }} me-1" style="width: 13px; text-align: center; font-size: 9px !important;"></i>
                            <span class="text-truncate {{ $wmClass }} fw-semibold" style="font-size: 10px !important; font-weight: 600 !important;">{{ $wmLabel }}</span>
                        </div>
                        <!-- Qualification / Degree -->
                        <div class="cd-val-edu text-truncate d-flex align-items-center mt-1" style="font-size: 10px !important; font-weight: 500 !important; color: #64748b !important; line-height: 1.25 !important; text-transform: uppercase !important;" title="Qualification: {{ strtoupper($qualificationName) }}">
                            <i class="fa-solid fa-graduation-cap me-1 text-muted" style="width: 13px; text-align: center; font-size: 9.5px !important;"></i>
                            <span class="text-truncate" style="font-size: 10px !important; font-weight: 500 !important; color: #64748b !important; text-transform: uppercase !important;">{{ strtoupper($qualificationName) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 4: RELATIONSHIP MGR -->
                <div class="cd-talent-subcard cd-subcard-manager" title="Relationship Manager">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate" style="font-size: 9.5px !important; font-weight: 700 !important;">
                            <i class="fa-solid fa-user-tie me-1 text-primary"></i>Relationship Mgr
                        </span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <div class="cd-manager-name text-truncate d-flex align-items-center" style="font-size: 11px !important; font-weight: 600 !important; line-height: 1.25 !important; min-height: 18px; color: #0f172a !important;" title="{{ $managerName }}{{ $extra }}">
                            <i class="fa-regular fa-circle-user me-1 text-primary" style="width: 13px; text-align: center; font-size: 9.5px !important;"></i>
                            <span class="text-dark text-truncate" style="font-size: 11px !important; color: #0f172a !important; font-weight: 600 !important;">{{ $displayManagerName }}</span>
                        </div>
                        <div class="text-truncate d-flex align-items-center mt-1" style="font-size: 10px !important; font-weight: 500 !important; color: #64748b !important; line-height: 1.25 !important;" title="Designation: {{ $displayRmDesignation }}">
                            <i class="fa-solid fa-briefcase me-1 text-muted" style="width: 13px; text-align: center; font-size: 9px !important;"></i>
                            <span class="text-truncate" style="font-size: 10px !important; font-weight: 500 !important; color: #64748b !important;">{{ $displayRmDesignation }}</span>
                        </div>
                        <div class="mt-1 d-flex align-items-center">
                            <span class="manager-change cd-rm-change-btn d-inline-flex align-items-center cursor-pointer" style="font-size: 10px !important; font-weight: 600 !important; color: #009ef7 !important; line-height: 1.25 !important;">
                                <i class="fa-solid fa-pen-to-square me-1" style="font-size: 9px !important;"></i>Change
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 5: MATCHING JOBS (Replaces Similar Clients per user request) -->
                <div class="cd-talent-subcard cd-subcard-matching-jobs" title="Matching Client Requisitions">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate">
                            <i class="fa-solid fa-briefcase me-1 text-primary"></i>Matching Jobs
                        </span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <!-- Direct Client Mandates (Emerald - Rule 25) -->
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge d-inline-flex align-items-center" style="background-color: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; font-size: 9.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; text-transform: uppercase !important;">
                                <i class="fa-solid fa-shield-alt me-1" style="color: #059669 !important; font-size: 8.5px;"></i>Direct Client
                            </span>
                            <span class="fw-bolder text-dark" style="font-size: 11px !important; font-weight: 700 !important; color: #0f172a !important;">
                                {{ $recomDirectCount }} <i class="fa-solid fa-chevron-right text-muted fs-9 ms-0.5"></i>
                            </span>
                        </div>
                        <!-- Internal Client Mandates (Blue - Rule 25) -->
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge d-inline-flex align-items-center" style="background-color: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; font-size: 9.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; text-transform: uppercase !important;">
                                <i class="fa-solid fa-building me-1" style="color: #2563eb !important; font-size: 8.5px;"></i>Internal Client
                            </span>
                            <span class="fw-bolder text-dark" style="font-size: 11px !important; font-weight: 700 !important; color: #0f172a !important;">
                                {{ $recomInternalCount }} <i class="fa-solid fa-chevron-right text-muted fs-9 ms-0.5"></i>
                            </span>
                        </div>
                        <!-- My Organization Mandates (Amber - Rule 25) -->
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge d-inline-flex align-items-center" style="background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fcd34d !important; font-size: 9.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; text-transform: uppercase !important;">
                                <i class="fa-solid fa-id-badge me-1" style="color: #d97706 !important; font-size: 8.5px;"></i>My Organization
                            </span>
                            <span class="fw-bolder text-dark" style="font-size: 11px !important; font-weight: 700 !important; color: #0f172a !important;">
                                {{ $recomMyOrgCount }} <i class="fa-solid fa-chevron-right text-muted fs-9 ms-0.5"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 6: ACTIONS (Replaces Pipeline Activity per user request) -->
                <div class="cd-talent-subcard cd-subcard-actions" title="Candidate Actions">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate">
                            <i class="fa-solid fa-bolt me-1 text-primary"></i>Action
                        </span>
                    </div>
                    <div class="cd-subcard-body pt-1 d-flex flex-column justify-content-center align-items-stretch gap-1.5">
                        <button type="button" class="btn btn-sm btn-primary openSideBar d-inline-flex align-items-center justify-content-center"
                            data-row-source_from_type="2"
                            data-row-id="{{ $row->id }}"
                            data-row-jobid="{{ @$jobid }}"
                            data-cname="{{ e(ucwords((string)@$row->full_name)) }}"
                            data-cdesig="{{ e(ucwords((string)@$row->current_designation)) }}"
                            data-cexp="{{ e($formattedExperience) }}"
                            data-cctc="{{ e(trim(strip_tags($plainCurrencySymbol) . ' ' . $billAmountClean)) }}"
                            data-cnp="{{ e($npDisplay) }}"
                            data-cloc="{{ e($fullLocation) }}"
                            data-ctype="{{ e($outsourcing_type) }}"
                            data-skills="{{ $candidateSkillStr }}"
                            style="height: 28px; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 6px; gap: 4px; background: #009ef7; border: 1px solid #009ef7; color: #ffffff;">
                            <i class="fa-solid fa-user-plus" style="font-size: 10px;"></i> Add to Job
                        </button>
                        <a href="javascript:void(0);" class="pbench_show_application_detail text-center text-hover-primary fw-semibold"
                            data-row-id="{{ $row->id }}"
                            style="font-size: 10.5px; color: #64748b; text-decoration: none; display: block; line-height: 1.3;">
                            <i class="fa-regular fa-eye me-1" style="font-size: 9.5px;"></i>View Profile
                        </a>
                    </div>
                </div>

            </div>

            <!-- Sub-Card 7 / Bottom Activity Strip: Skills on Left, Timestamp & Days on Right -->
            <div class="cd-talent-card-bottom" style="display:flex !important; align-items:center !important; justify-content:space-between !important; min-height:38px !important; width:100% !important; overflow:hidden !important; gap:12px !important; padding:6px 12px !important; background:#fafcff !important; border-top:1px solid #edf2f7 !important;">
                <!-- Left: Skills Keywords Tags -->
                <div class="cd-bottom-skills d-flex align-items-center gap-1 flex-wrap min-w-0 flex-grow-1 overflow-hidden" style="overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
                    @if (!empty($skillArr))
                        @foreach ($skillArr as $sval)
                            @if (trim($sval) !== '')
                                <span class="cd-skill-pill" style="font-size: 10px !important; font-weight: 500 !important; padding: 2px 7px !important; border-radius: 4px !important; background: #f1f5f9 !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; line-height: 1.35 !important; white-space: nowrap !important; flex-shrink: 0 !important; display: inline-block !important;">{{ ucfirst(trim($sval)) }}</span>
                            @endif
                        @endforeach
                    @else
                        <span class="text-muted" style="font-size: 10.5px !important; font-style: italic !important;">No specific skills listed</span>
                    @endif
                </div>

                <!-- Right: Added Date & Time (Rule 24), Days on Bench & Action Button -->
                <div class="cd-bottom-right d-flex align-items-center gap-2 flex-shrink-0" style="white-space: nowrap;">
                    @if ($stageDateLabel)
                        <span class="cd-bottom-date" style="font-size: 10.5px; font-weight: 500; color: #64748b; white-space: nowrap;">
                            <i class="fa-regular fa-calendar-alt text-muted me-1" style="font-size: 10px; color: #94a3b8;"></i>Added: <strong style="font-size: 10.5px; font-weight: 700; color: #0f172a;">{{ $stageDateLabel }}</strong>
                        </span>
                        <span class="text-muted" style="font-size: 9px;">&bull;</span>
                    @endif
                    @if ($benchDaysCount !== null)
                        <span class="badge" title="Days on Bench" style="font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 4px; background: #fffbeb; color: #d97706; border: 1px solid #fde68a; line-height: 1.35; white-space: nowrap;">
                            <i class="fa-regular fa-hourglass me-1" style="font-size: 9.5px;"></i>{{ $benchDaysCount }} Days on Bench
                        </span>
                    @endif
                    <button type="button" class="btn btn-sm openSideBar"
                        data-row-source_from_type="2"
                        data-row-id="{{ $row->id }}"
                        data-row-jobid="{{ @$jobid }}"
                        data-cname="{{ e(ucwords((string)@$row->full_name)) }}"
                        data-cdesig="{{ e(ucwords((string)@$row->current_designation)) }}"
                        data-cexp="{{ e($formattedExperience) }}"
                        data-cctc="{{ e(trim(strip_tags($plainCurrencySymbol) . ' ' . $billAmountClean)) }}"
                        data-cnp="{{ e($npDisplay) }}"
                        data-cloc="{{ e($fullLocation) }}"
                        data-ctype="{{ e($outsourcing_type) }}"
                        data-skills="{{ $candidateSkillStr }}"
                        style="height: 26px; font-size: 10.5px; font-weight: 600; padding: 3px 10px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                        <i class="fa-solid fa-user-plus me-1" style="font-size: 9.5px;"></i>Add to Job
                    </button>
                </div>
            </div>
        </article>
    @endforeach
@else
    <div class="cd-talent-empty" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.02);">
        <div class="cd-talent-empty-icon mb-3" style="width:56px;height:56px;border-radius:50%;background:#eff6ff;color:#2563eb;display:inline-flex;align-items:center;justify-content:center;margin:0 auto;">
            <i class="fa-solid fa-user-slash" style="font-size:22px;"></i>
        </div>
        <div class="fw-bold fs-5 text-gray-800 mb-1">No Bench Talents Found</div>
        <div class="text-muted fs-7 mb-4">No candidates match the active filters or selected talent category. Try adjusting your search or filters.</div>
        <button type="button" class="btn btn-sm btn-light-primary" onclick="window.resetFindTalentsFilters && window.resetFindTalentsFilters()" style="font-weight:600;font-size:12px;border-radius:6px;">
            <i class="fa-solid fa-arrows-rotate me-1"></i> Reset Filters
        </button>
    </div>
@endif
