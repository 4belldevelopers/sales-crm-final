@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $company_id = auth()->user()->company_id;
@endphp
@if (count($data) > 0)
    @php
        // Ensure bulk ES stage counter map is available even if rendered outside data_ajax
        if (!isset($benchExploreCounters) || empty($benchExploreCounters)) {
            $pageBenchIds = array_values(array_filter(array_map(fn($r) => is_object($r) ? ($r->id ?? 0) : ($r['id'] ?? 0), $data)));
            $benchExploreCounters = GeneralMethod::bench_explore_listing_counter_bulk($pageBenchIds, $company_id);
        }
    @endphp
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            /**
             * Intent: Read candidate pipeline activity counts from bulk-fetched $benchExploreCounters
             * (1 ES aggregation per page instead of 10 per-row ES queries) while guaranteeing 100%
             * count consistency between the bench candidates cards and the bench explore view.
             * Tenancy: Scoped by candidate ID and logged-in company.
             * Strict CQRS: Zero MySQL queries on read paths.
             */
            $bench_explore_listing_counter = $benchExploreCounters[$row->id] ?? GeneralMethod::bench_explore_listing_counter($row->id, $company_id);
            $applicationCounter = $bench_explore_listing_counter->applicationCounter;

            $row->Sourcing = $applicationCounter->Sourcing ?? 0;
            $row->Screening = $applicationCounter->Screening ?? 0;
            $row->Evaluation = $applicationCounter->Evaluation ?? 0;
            $row->Negotiation = $applicationCounter->Negotiation ?? 0;
            $row->Offered = $applicationCounter->Offered ?? 0;
            $row->Joined = $applicationCounter->Joined ?? 0;
            $row->Rejected = $applicationCounter->Rejected ?? 0;
            $row->InReview = $applicationCounter->Interview ?? 0;

            $exploreBase = url('/service_company/bench-candidates/bench_explore/' . $row->id);

            // Work preference (Remote/Hybrid/Work from office)
            $wm = strtolower((string) @$row->wm);
            $wmMap = ['remote' => 'Remote', 'hybrid' => 'Hybrid', 'office' => 'Work from office'];
            $wmClassMap = ['remote' => 'text-success', 'hybrid' => 'text-primary', 'office' => 'text-warning'];
            $wmLabel = $wmMap[$wm] ?? 'Work from office';
            $wmClass = $wmClassMap[$wm] ?? 'text-warning';

            // Employment type (Full-time/Contract)
            $outsourcing_type = $row->outsourcing_type == 2 ? 'Contract' : 'Full-time';

            // Word-aware labels
            $limitWords = function ($text, $limit) {
                $words = preg_split('/\s+/', trim((string) $text), -1, PREG_SPLIT_NO_EMPTY);
                if (count($words) <= $limit) {
                    return trim((string) $text);
                }
                return implode(' ', array_slice($words, 0, $limit)) . '...';
            };

            // Avatar initial styling
            $strVal = 'U';
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
            $profile_sidebar_css = 'bench_show_application_detail';

            // Skill chips & Stage date
            $skillArr = array_values(array_filter(array_map('trim', explode(',', (string) ($row->skills ?? '')))));
            $stageMovedAt = $row->updated_at ?? $row->created_at ?? null;
            $stageDateLabel = $stageMovedAt ? Carbon::parse($stageMovedAt)->format('d M y') : '';
            $isAds = (@$row->ads_switch == 'Yes');
            $displayFullName = $limitWords(ucwords(@$row->full_name ?: 'Unnamed Talent'), 25);
            $displayDesignation = $limitWords(ucwords(@$row->current_designation ?: 'Talent'), 25);

            // View / Download counts pre-fetched in bulk via controller
            $viewCount = $viewCounts[$row->id] ?? $row->viewCount ?? 0;
            $downloadCount = $downloadCounts[$row->id] ?? $row->downloadCount ?? 0;

            // Similar clients count pre-fetched via controller (0% MySQL reads)
            $recomInfo = $benchRecomCounts[$row->id] ?? null;
            $recomDirectCount = (int) ($recomInfo['recommend_req_counter'] ?? 0);
            $recomInternalCount = (int) ($recomInfo['recommend_internal_req_counter'] ?? 0);
            $recomMyOrgCount = (int) ($recomInfo['recommend_my_org_req_counter'] ?? 0);

            // Experience formatting
            $experience = trim((string) @$row->total_exp);
            if (strpos($experience, '.') !== false) {
                $parts = explode('.', $experience);
                $formattedExperience = (empty($parts[1]) || $parts[1] === '00') ? $parts[0] : $experience;
            } else {
                $formattedExperience = $experience ?: '0';
            }

            // Relevant Experience formatting (format: 4 Y Rel)
            $relExperience = trim((string) @$row->rel_exp);
            if (strpos($relExperience, '.') !== false) {
                $rParts = explode('.', $relExperience);
                $formattedRelExperience = (empty($rParts[1]) || $rParts[1] === '00') ? $rParts[0] : $relExperience;
            } else {
                $formattedRelExperience = $relExperience ?: '';
            }

            // Bench Company Name resolution
            $benchCompanyName = trim((string) (@$row->company_name ?: @$row->current_company ?: ''));
            if ($benchCompanyName === '') {
                $benchCompanyName = 'In-House Talent';
            }
            $displayBenchCompany = $limitWords($benchCompanyName, 25);

            // Notice / Availability line
            if ($row->serving_notice_period_mode == 2) {
                $availLine = 'Immediate';
                $availClass = 'text-success';
            } elseif ($row->serving_notice_period_mode == 0) {
                $days = (int) $row->np_id;
                $availLine = ($days > 0 ? $days : 0) . ' Days';
                $availClass = 'text-warning';
            } elseif ($row->serving_notice_period_mode == 1) {
                $lwdDate = $row->np_id ? date('d M y', strtotime($row->np_id)) : '';
                if ($row->np_id && strtotime($row->np_id) < strtotime('today')) {
                    $availLine = 'Immediate';
                    $availClass = 'text-success';
                } else {
                    $availLine = 'Date ' . $lwdDate;
                    $availClass = 'text-warning';
                }
            } else {
                $availLine = 'Immediate';
                $availClass = 'text-success';
            }

            // Location formatting (First Row: City, State, Country)
            $locParts = array_filter([
                !empty($row->city_name) ? ucwords(trim($row->city_name)) : null,
                !empty($row->state_name) ? ucwords(trim($row->state_name)) : null,
                !empty($row->country_name) ? ucwords(trim($row->country_name)) : null,
            ]);
            $fullLocation = !empty($locParts) ? implode(', ', $locParts) : '—';
            $qualificationName = !empty($row->qualification_name) ? trim($row->qualification_name) : '';
            $cityCountry = $fullLocation;

            // Salary & Service Type formatting
            $currencyBenchMode = $companyCurrencyBenchshowMode ?? GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);
            $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $currencyBenchMode->currency_code, $currencyBenchMode->show_mode);
            $bill_rate_currency = $currencySymbole[$billFormatViewSalary->currency] ?? '$';
            $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode] ?? 'Hourly';
            $serviceType = @$row->comission_type == 2 ? 'Paid Services' : (@$row->comission_type == 1 ? 'Free Services' : '—');
            $serviceClass = @$row->comission_type == 2 ? 'text-success' : 'text-muted';
            $billAmountClean = number_format((float) $billFormatViewSalary->amount, 0);

            // Status Badge & Border Theme Accent (Synchronized with CV Tracker & Status KPI grid)
            $statusId = (int) ($row->status ?? 0);
            $statusName = $row->master_status_name ?? 'Bench';
            if ($statusId == 1) { // Pool (Cyan)
                $statusPillClass = 'status-pool';
                $statusBorderAccent = '#0284c7';
                $statusPillStyle = 'background: #f0f9ff !important; color: #0284c7 !important; border: 1px solid #bae6fd !important;';
                $subS = 'Newly added';
            } elseif ($statusId == 2) { // Bench (Amber)
                $statusPillClass = 'status-bench';
                $statusBorderAccent = '#d97706';
                $statusPillStyle = 'background: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a !important;';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'Open For Work';
            } elseif ($statusId == 4) { // On Hold / Paused (Slate)
                $statusPillClass = 'status-hold';
                $statusBorderAccent = '#64748b';
                $statusPillStyle = 'background: #f8fafc !important; color: #64748b !important; border: 1px solid #cbd5e1 !important;';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'On Hold';
            } elseif ($statusId == 3) { // Placed (Green)
                $statusPillClass = 'status-placed';
                $statusBorderAccent = '#16a34a';
                $statusPillStyle = 'background: #ecfdf5 !important; color: #16a34a !important; border: 1px solid #a7f3d0 !important;';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'Placed';
            } elseif ($statusId == 5) { // Exited (Red)
                $statusPillClass = 'status-exited';
                $statusBorderAccent = '#dc2626';
                $statusPillStyle = 'background: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fecaca !important;';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'Exited';
            } else {
                $statusPillClass = 'status-bench';
                $statusBorderAccent = '#d97706';
                $statusPillStyle = 'background: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a !important;';
                $subS = 'Bench';
            }

            // Bench Aging (days on bench, only while status = Bench)
            $benchDaysCount = null;
            if ($statusId == 2 && !empty($row->created_at)) {
                $benchDaysCount = (int) Carbon::parse($row->created_at)->diffInDays(Carbon::now());
            }
            $benchDaysClass = ($benchDaysCount !== null && $benchDaysCount > 30) ? 'text-danger' : 'text-warning';

            // Relationship Manager resolution
            if (!empty($row->hiring_hr)) {
                $hiringHr = (array) $row->hiring_hr;
                $hmCount = count($hiringHr);
                $firstHr = (object) ($hiringHr[0] ?? []);
                $managerName = !empty($firstHr->name) ? ucwords($firstHr->name) : 'Unassigned';
                $extra = $hmCount > 1 ? ' +' . ($hmCount - 1) : '';
            } elseif (isset($benchManagersMap) && is_array($benchManagersMap)) {
                $hmEntry = $benchManagersMap[$row->id] ?? null;
                $hmCount = (int) ($hmEntry['count'] ?? 0);
                $managerName = !empty($hmEntry['first_name']) ? ucwords($hmEntry['first_name']) : 'Unassigned';
                $extra = $hmCount > 1 ? ' +' . ($hmCount - 1) : '';
            } else {
                $hmCount = 0;
                $managerName = 'Unassigned';
                $extra = '';
            }
            $displayManagerName = $limitWords($managerName . $extra, 20);
        @endphp

        <!-- Master Primary Talent Card (Rule 20 Modern 6-Subcard + Strip Layout) -->
        <article class="cd-talent-card" data-bench-row-id="{{ $row->id }}" data-candidate-id="{{ $row->id }}" style="border-left: 3.5px solid {{ $statusBorderAccent }} !important;">
            <!-- Upper Sub-Cards Grid (6 Spacious Discrete Sections) -->
            <div class="cd-talent-card-grid">

                <!-- Sub-Card 1: Talent Profile & Status (Rule 20 Standard) -->
                <div class="cd-talent-subcard cd-subcard-talent">
                    <div class="cd-subcard-header">
                        <div class="d-flex align-items-center gap-1.5 min-w-0">
                            <label class="form-check form-check-sm form-check-custom form-check-solid m-0" style="cursor: pointer;" title="Select candidate">
                                <input class="form-check-input chk_list_item cursor-pointer" name="chk_list_item[]" type="checkbox"
                                    data-skills="{{ e($row->skills ?? '') }}"
                                    data-cname="{{ e($row->full_name ?? '') }}"
                                    value="{{ $row->id }}"
                                    style="width: 15px; height: 15px; margin: 0; flex-shrink: 0;"
                                    title="Select candidate" />
                            </label>
                            <!-- Hiring Category: Full-Time / Contract -->
                            <span class="badge badge-light-primary text-primary fs-9 py-0.5 px-1.5 fw-bold border border-primary-subtle text-truncate" title="Hiring Category">
                                <i class="fa-solid fa-briefcase me-1" style="font-size: 8.5px;"></i>{{ $outsourcing_type }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            @if($isAds)
                                <span class="badge badge-light-danger fs-9 py-0.5 px-1.5 fw-bold border border-danger-subtle">Ads</span>
                            @endif
                            <!-- Candidate Availability Status (Bench) -->
                            <span class="cd-status-badge {{ $statusPillClass }} stage_sub_stage cursor-pointer" data-row-id="{{ $row->id }}" title="Click to change status" style="{{ $statusPillStyle }}">
                                {{ $statusName }}
                            </span>
                        </div>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <div class="d-flex align-items-start gap-2.5 min-w-0">
                            <!-- Refined Visual Profile Avatar Icon -->
                            <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="{{ $profile_sidebar_css }} cd-talent-avatar flex-shrink-0" title="{{ ucwords(@$row->full_name) }}" style="width: 36px !important; height: 36px !important; min-width: 36px !important; border-radius: 8px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; font-weight: 700 !important; font-size: 13.5px !important; text-decoration: none !important; overflow: hidden !important; flex-shrink: 0 !important; border: 1px solid rgba(15, 23, 42, 0.08) !important; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;">
                                @if (!empty($row->photo))
                                    <img src="{{ $row->photo }}" alt="{{ e($displayFullName) }}" style="width: 100% !important; height: 100% !important; border-radius: 8px !important; object-fit: cover !important;" />
                                @else
                                    <div class="symbol-label {{ $bgTextDecore }} w-100 h-100 d-flex align-items-center justify-content-center fw-bolder" style="border-radius: 8px; font-size: 13.5px;">{{ $strVal }}</div>
                                @endif
                            </a>
                            <div class="d-flex flex-column min-w-0 flex-grow-1 overflow-hidden">
                                <!-- 1. Candidate Name -->
                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                                    class="cd-talent-name {{ $profile_sidebar_css }}"
                                    title="{{ ucwords(@$row->full_name) }}"
                                    style="font-size: 12.5px !important; font-weight: 700 !important; color: #0f172a !important; line-height: 1.25 !important; display: block !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; text-decoration: none !important;">
                                    {{ $displayFullName }}
                                </a>
                                <!-- 2. Designation -->
                                <div class="cd-talent-role"
                                    title="{{ ucwords(@$row->current_designation) }}"
                                    style="font-size: 11px !important; font-weight: 500 !important; color: #475569 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; line-height: 1.3 !important; margin-top: 2px !important;">
                                    {{ $displayDesignation !== '' ? $displayDesignation : '—' }}
                                </div>
                                <!-- 3. Bench Company Name -->
                                <div class="cd-talent-company text-truncate"
                                    title="Bench Company: {{ $benchCompanyName }}"
                                    style="font-size: 11px !important; font-weight: 500 !important; color: #64748b !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; line-height: 1.3 !important; margin-top: 2px !important;">
                                    <i class="fa-regular fa-building me-1 text-muted" style="width: 12px; text-align: center;"></i>
                                    <span>{{ $displayBenchCompany !== '' ? $displayBenchCompany : '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 2: Compensation & Experience -->
                <div class="cd-talent-subcard cd-subcard-comp-exp">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate">COMP &amp; EXP</span>
                        <!-- Service Category: Paid / Free Services -->
                        <span class="badge {{ @$row->comission_type == 2 ? 'badge-light-success text-success border border-success-subtle' : 'badge-light-secondary text-muted border border-gray-300' }} fs-9 py-0.5 px-1.5 fw-bold" title="Service Category">
                            <i class="fa-solid fa-circle-check me-1" style="font-size: 8.5px;"></i>{{ $serviceType }}
                        </span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <!-- 1. Bill Rate -->
                        <div class="cd-val-salary d-flex align-items-baseline gap-1" title="Bill Rate: {!! $bill_rate_currency !!}{{ $billAmountClean }} / {{ $bill_rate_mode }}">
                            <span style="font-size: 10px; color: #64748b; font-weight: 500;">Bill Rate:</span>
                            <strong style="font-size: 13.5px; font-weight: 700; color: #0f172a; line-height: 1.2;">{!! $bill_rate_currency !!}{{ $billAmountClean }}</strong>
                            <span style="font-size: 10px; color: #64748b; font-weight: 500;">/{{ mb_substr($bill_rate_mode, 0, 1) }}</span>
                        </div>
                        <!-- 2. Total Experience & Relevant Experience -->
                        <div class="cd-val-exp d-flex align-items-center gap-1 mt-0.5 text-truncate" title="Total: {{ $formattedExperience }} Yrs{{ (!empty($formattedRelExperience) && $formattedRelExperience > 0) ? ' | Rel: ' . $formattedRelExperience . ' Yrs' : '' }}" style="font-size: 11px; line-height: 1.35;">
                            <i class="fa-solid fa-briefcase me-1 text-muted" style="width: 12px; text-align: center; font-size: 10px;"></i>
                            <span style="color: #0f172a; font-weight: 600;"><strong>{{ $formattedExperience !== '' ? $formattedExperience : '0' }} Y</strong> Total</span>
                            @if (!empty($formattedRelExperience) && $formattedRelExperience > 0)
                                <span class="ms-1" style="color: #64748b; font-size: 10px;">(Rel <strong>{{ $formattedRelExperience }} Y</strong>)</span>
                            @endif
                        </div>
                        <!-- 3. Notice Period -->
                        <div class="cd-val-avail {{ $availClass }} d-flex align-items-center gap-1 mt-0.5 text-truncate" title="Notice Period: {{ $availLine }}" style="font-size: 11px; font-weight: 600; line-height: 1.35;">
                            <i class="fa-regular fa-clock me-1 text-muted" style="width: 12px; text-align: center; font-size: 10px;"></i>
                            <span>Notice: <strong class="{{ $availClass }}">{{ $availLine }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 3: Location & Education -->
                <div class="cd-talent-subcard cd-subcard-avail-loc">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate">LOCATION &amp; DEGREE</span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <!-- First Row: City, State, Country -->
                        <div class="cd-val-location text-truncate d-flex align-items-center" style="font-size: 11.5px; font-weight: 600; line-height: 1.35; color: #0f172a;" title="{{ $fullLocation }}">
                            <i class="fa-solid fa-location-dot me-1 text-muted" style="width: 13px; text-align: center; font-size: 10px;"></i>
                            <span class="text-truncate">{{ $fullLocation }}</span>
                        </div>
                        <!-- Second Row: Work Mode -->
                        <div class="cd-val-wm text-truncate d-flex align-items-center mt-1" style="font-size: 11px; font-weight: 500; color: #475569; line-height: 1.35;" title="Work Mode: {{ $wmLabel }}">
                            <i class="fa-solid fa-building me-1 text-primary" style="width: 13px; text-align: center; font-size: 10px;"></i>
                            <span class="text-truncate {{ $wmClass }} fw-semibold">{{ $wmLabel }}</span>
                        </div>
                        <!-- Third Row: Qualification / Degree in Uppercase -->
                        <div class="cd-val-edu text-truncate d-flex align-items-center mt-1" style="font-size: 11px; font-weight: 500; color: #64748b; line-height: 1.35;" title="{{ !empty($qualificationName) ? strtoupper($qualificationName) : 'Education: —' }}">
                            <i class="fa-solid fa-graduation-cap me-1 text-muted" style="width: 13px; text-align: center; font-size: 10px;"></i>
                            <span class="text-truncate">{{ !empty($qualificationName) ? strtoupper($qualificationName) : '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 4: Relationship Manager -->
                <div class="cd-talent-subcard cd-subcard-manager">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate"><i class="fa-solid fa-user-tie me-1 text-primary"></i>Relationship Mgr</span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <div class="cd-manager-name text-truncate d-flex align-items-center" style="font-size: 11.5px; font-weight: 600; line-height: 1.35; min-height: 18px;" title="{{ $managerName }}{{ $extra }}">
                            <i class="fa-regular fa-circle-user me-1 text-primary" style="width: 13px; text-align: center; font-size: 10.5px;"></i>
                            <span class="text-dark text-truncate">{{ $displayManagerName }}</span>
                        </div>
                        <div class="text-truncate d-flex align-items-center mt-1" style="font-size: 11px; font-weight: 500; color: #64748b; line-height: 1.35;">
                            <i class="fa-regular fa-building me-1 text-muted" style="width: 13px; text-align: center; font-size: 10px;"></i>
                            <span class="text-truncate">In-House Talent</span>
                        </div>
                        <div class="mt-1 d-flex align-items-center">
                            <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="manager-change hmb-listing cd-rm-change-btn d-inline-flex align-items-center" style="font-size: 11px !important; font-weight: 600 !important; color: #009ef7 !important;">
                                <i class="fa-solid fa-pen-to-square me-1" style="font-size: 9.5px;"></i>Change
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 5: Similar Clients (Direct, Internal Clients & My Organization) -->
                <div class="cd-talent-subcard cd-subcard-similar-clients">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate"><i class="fa-solid fa-handshake me-1 text-primary"></i>Similar Clients</span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <!-- Direct Client Mandates (Emerald) -->
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <a href="javascript:void(0);" class="btn-similar-clients text-hover-primary d-inline-flex align-items-center"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="direct"
                                title="View Potential Direct Clients">
                                <span class="badge py-1 px-1.5 fs-9 fw-bold border d-inline-flex align-items-center" style="background-color: #ecfdf5 !important; color: #059669 !important; border-color: #a7f3d0 !important;">
                                    <i class="fa-solid fa-shield-alt me-1" style="color: #059669 !important; font-size: 8.5px;"></i>Direct Client
                                </span>
                            </a>
                            <a href="javascript:void(0);" class="btn-similar-clients fs-8 fw-bolder text-dark text-hover-primary"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="direct">
                                {{ $recomDirectCount }} <i class="fa-solid fa-chevron-right text-muted fs-9 ms-0.5"></i>
                            </a>
                        </div>
                        <!-- Internal Client Mandates (Blue) -->
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <a href="javascript:void(0);" class="btn-similar-clients text-hover-primary d-inline-flex align-items-center"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="internal"
                                title="View Potential Internal Clients">
                                <span class="badge py-1 px-1.5 fs-9 fw-bold border d-inline-flex align-items-center" style="background-color: #eff6ff !important; color: #2563eb !important; border-color: #bfdbfe !important;">
                                    <i class="fa-solid fa-building me-1" style="color: #2563eb !important; font-size: 8.5px;"></i>Internal Client
                                </span>
                            </a>
                            <a href="javascript:void(0);" class="btn-similar-clients fs-8 fw-bolder text-dark text-hover-primary"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="internal">
                                {{ $recomInternalCount }} <i class="fa-solid fa-chevron-right text-muted fs-9 ms-0.5"></i>
                            </a>
                        </div>
                        <!-- My Organization Mandates (Amber - Rule 25) -->
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="javascript:void(0);" class="btn-similar-clients text-hover-primary d-inline-flex align-items-center"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="my_org"
                                title="View Potential My Organization Mandates">
                                <span class="badge py-1 px-1.5 fs-9 fw-bold border d-inline-flex align-items-center" style="background-color: #fef3c7 !important; color: #92400e !important; border-color: #fcd34d !important;">
                                    <i class="fa-solid fa-id-badge me-1" style="color: #d97706 !important; font-size: 8.5px;"></i>My Organization
                                </span>
                            </a>
                            <a href="javascript:void(0);" class="btn-similar-clients fs-8 fw-bolder text-dark text-hover-primary"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="my_org">
                                {{ $recomMyOrgCount }} <i class="fa-solid fa-chevron-right text-muted fs-9 ms-0.5"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 6: Clients Pipeline Activity -->
                <div class="cd-talent-subcard cd-subcard-pipeline">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate">Clients Pipeline Activity</span>
                    </div>
                    <div class="cd-subcard-body p-1">
                        <div class="cd-pipe-grid">
                            <div class="cd-pipe-col"><span class="h">INW</span><span class="v"><a href="{{ $exploreBase }}/all" class="text-hover-primary" data-bench-pipe="inreview">{{ $row->InReview }}</a></span></div>
                            <div class="cd-pipe-col"><span class="h">SOU</span><span class="v"><a href="{{ $exploreBase }}/1" class="text-hover-primary" data-bench-pipe="sourcing">{{ $row->Sourcing }}</a></span></div>
                            <div class="cd-pipe-col"><span class="h">SCR</span><span class="v"><a href="{{ $exploreBase }}/2" class="text-hover-primary" data-bench-pipe="screening">{{ $row->Screening }}</a></span></div>
                            <div class="cd-pipe-col"><span class="h">EVA</span><span class="v"><a href="{{ $exploreBase }}/3" class="text-hover-primary" data-bench-pipe="evaluation">{{ $row->Evaluation }}</a></span></div>
                            <div class="cd-pipe-col"><span class="h">NEG</span><span class="v"><a href="{{ $exploreBase }}/4" class="text-hover-primary" data-bench-pipe="negotiation">{{ $row->Negotiation }}</a></span></div>
                            <div class="cd-pipe-col"><span class="h">OFF</span><span class="v"><a href="{{ $exploreBase }}/5" class="text-hover-primary" data-bench-pipe="offered">{{ $row->Offered }}</a></span></div>
                            <div class="cd-pipe-col"><span class="h">JOI</span><span class="v"><a href="{{ $exploreBase }}/6" class="text-hover-primary" data-bench-pipe="joined">{{ $row->Joined }}</a></span></div>
                            <div class="cd-pipe-col"><span class="h">REJ</span><span class="v"><a href="{{ $exploreBase }}/7" class="text-hover-primary" data-bench-pipe="rejected">{{ $row->Rejected }}</a></span></div>
                        </div>
                        <div class="cd-pipe-summary d-flex align-items-center justify-content-between mt-1.5 pt-1 border-top border-gray-200">
                            <span class="pipe-stat text-primary fw-bold" style="font-size: 10px;">Interviews: <strong data-bench-pipe-interview="{{ $row->id }}" class="ms-1">{{ $row->InReview }}</strong></span>
                            <span class="pipe-stat text-muted" style="font-size: 10px;">Views: <strong class="ms-1 text-dark">{{ $viewCount }}</strong></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sub-Card 7 / Bottom Activity Strip -->
            <div class="cd-talent-card-bottom" style="display:flex !important; align-items:center !important; justify-content:space-between !important; min-height:38px !important; width:100% !important; overflow:hidden !important; gap:12px !important; padding:6px 12px !important; background:#fafcff !important; border-top:1px solid #edf2f7 !important;">
                <div class="cd-bottom-left d-flex align-items-center gap-2 flex-wrap min-w-0 flex-grow-1 overflow-hidden">
                    @if ($stageDateLabel)
                        <span class="cd-bottom-date text-dark fw-bold" style="font-size: 11px; white-space: nowrap;">
                            <i class="fa-regular fa-calendar-alt text-muted me-1"></i>Added: <strong>{{ $stageDateLabel }}</strong>
                        </span>
                        <span class="text-muted">&bull;</span>
                    @endif
                    @if ($benchDaysCount !== null)
                        <span class="badge badge-light-warning {{ $benchDaysClass }} fw-bold fs-8 py-1 px-2" title="Days on Bench" style="white-space: nowrap;">
                            <i class="fa-regular fa-hourglass me-1"></i>{{ $benchDaysCount }} Days on Bench
                        </span>
                        <span class="text-muted">&bull;</span>
                    @endif
                    <div class="cd-bottom-skills d-flex align-items-center gap-1 flex-wrap min-w-0 flex-grow-1 overflow-hidden" style="overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
                        @if (!empty($skillArr))
                            @foreach ($skillArr as $sval)
                                @if (trim($sval) !== '')
                                    <span class="cd-skill-pill" style="font-size: 10px !important; font-weight: 500 !important; padding: 2px 7px !important; border-radius: 4px !important; background: #f1f5f9 !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; line-height: 1.35 !important; white-space: nowrap !important; flex-shrink: 0 !important; display: inline-block !important;">{{ ucfirst(trim($sval)) }}</span>
                                @endif
                            @endforeach
                        @else
                            <span class="text-muted fs-8" style="font-style: italic;">No specific skills listed</span>
                        @endif
                    </div>
                </div>
                <div class="cd-bottom-actions d-flex align-items-center gap-2 flex-shrink-0" style="white-space: nowrap;">
                    <button type="button" class="btn btn-sm btn-light-info py-1 px-2.5 fs-8 fw-bold btn-similar-clients"
                        data-bench-id="{{ $row->id }}"
                        data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                        data-bench-role="{{ e($row->current_designation ?? '') }}"
                        data-client-type="direct"
                        style="height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;">
                        <i class="fa-solid fa-handshake me-1"></i>Similar Clients
                    </button>
                    <button type="button" class="btn btn-sm btn-light-primary py-1 px-3 fs-8 fw-bold {{ $profile_sidebar_css }}" data-row-id="{{ $row->id }}"
                        style="height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;">
                        <i class="fa-solid fa-user me-1"></i>View Profile
                    </button>
                    <button type="button" class="btn btn-sm btn-light-success py-1 px-3 fs-8 fw-bold stage_sub_stage" data-row-id="{{ $row->id }}"
                        style="height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;">
                        <i class="fa-solid fa-pen-to-square me-1"></i>Change Status
                    </button>
                </div>
            </div>
        </article>
    @endforeach
@else
    <div class="cd-talent-empty">
        <div class="cd-talent-empty-icon mb-2">
            <i class="fa fa-users-slash fa-2x text-muted"></i>
        </div>
        <div class="cd-talent-empty-title fw-bold fs-6 text-gray-700 mb-1">No Inhouse Talents Found</div>
        <div class="cd-talent-empty-sub text-muted fs-7 mb-3">No candidates match the active filter or search criteria.</div>
        <button type="button" class="btn btn-sm btn-primary" id="AddInternalPoolEmpty">
            <i class="fa fa-user-plus me-1"></i>Add Inhouse Talent
        </button>
    </div>
@endif
