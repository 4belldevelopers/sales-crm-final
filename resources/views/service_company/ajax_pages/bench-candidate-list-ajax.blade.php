@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $company_id = auth()->user()->company_id;
    $tenantTimezone = GeneralMethod::defaultTimeZone($company_id) ?: config('app.timezone', 'UTC');
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
            $wmClassMap = [
                'remote' => 'text-success',
                'hybrid' => 'text-primary',
                'office' => 'text-gray-700',
            ];
            $wmIconMap = [
                'remote' => 'fa-solid fa-house-laptop text-success',
                'hybrid' => 'fa-solid fa-building-user text-primary',
                'office' => 'fa-solid fa-building text-primary',
            ];
            $wmLabel = $wmMap[$wm] ?? 'Work from office';
            $wmClass = $wmClassMap[$wm] ?? 'text-gray-700';
            $wmIcon = $wmIconMap[$wm] ?? 'fa-solid fa-building text-primary';

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
            $stageDateLabel = $stageMovedAt ? Carbon::parse($stageMovedAt)->setTimezone($tenantTimezone)->format('d M y, h:i A T') : '';
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
            $plainCurrencySymbol = !empty($billFormatViewSalary->symbol)
                ? $billFormatViewSalary->symbol
                : (trim(strip_tags($bill_rate_currency)) ?: '$');
            $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode] ?? 'Hourly';
            $serviceType = @$row->comission_type == 2 ? 'Paid Services' : (@$row->comission_type == 1 ? 'Free Services' : '—');
            $serviceClass = @$row->comission_type == 2 ? 'text-success' : 'text-muted';
            $billAmountClean = number_format((float) $billFormatViewSalary->amount, 0);

            // Status Badge & Border Theme Accent (Synchronized with Bench Talent Stage Palette)
            $statusId = (int) ($row->status ?? 0);
            $statusName = $row->master_status_name ?? 'Bench';
            if ($statusId == 1) { // Pool (Purple)
                $statusPillClass = 'status-pool';
                $statusBorderAccent = '#7c3aed';
                $statusPillStyle = 'background: #faf5ff !important; color: #7c3aed !important; border: 1px solid #ddd6fe !important;';
                $subS = 'Newly added';
            } elseif ($statusId == 2) { // Bench (Blue)
                $statusPillClass = 'status-bench';
                $statusBorderAccent = '#2563eb';
                $statusPillStyle = 'background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important;';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'Open For Work';
            } elseif ($statusId == 4) { // On Hold / Paused (Orange)
                $statusPillClass = 'status-hold';
                $statusBorderAccent = '#ea580c';
                $statusPillStyle = 'background: #fff7ed !important; color: #ea580c !important; border: 1px solid #fed7aa !important;';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'On Hold';
            } elseif ($statusId == 3) { // Placed (Green)
                $statusPillClass = 'status-placed';
                $statusBorderAccent = '#059669';
                $statusPillStyle = 'background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important;';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'Placed';
            } elseif ($statusId == 5) { // Exited (Red)
                $statusPillClass = 'status-exited';
                $statusBorderAccent = '#dc2626';
                $statusPillStyle = 'background: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fecaca !important;';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'Exited';
            } else {
                $statusPillClass = 'status-bench';
                $statusBorderAccent = '#2563eb';
                $statusPillStyle = 'background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important;';
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

                <!-- Sub-Card 1: Talent Profile & Status (All Candidates Tracker Standard) -->
                <div class="cd-talent-subcard cd-subcard-talent">
                    <div class="cd-subcard-header">
                        <div class="d-flex align-items-center gap-1.5 min-w-0">
                            <label class="form-check form-check-sm form-check-custom m-0" style="cursor: pointer;" title="Select candidate">
                                <input class="form-check-input chk_list_item cursor-pointer" name="chk_list_item[]" type="checkbox"
                                    data-skills="{{ e($row->skills ?? '') }}"
                                    data-cname="{{ e($row->full_name ?? '') }}"
                                    value="{{ $row->id }}"
                                    style="width: 15px; height: 15px; margin: 0; flex-shrink: 0; border: 1.5px solid #94a3b8 !important; border-radius: 4px !important; background-color: #ffffff !important;"
                                    title="Select candidate" />
                            </label>
                            <!-- Candidate Info (Replaced full-time per user request) -->
                            <span class="cd-subcard-label text-uppercase text-truncate" title="Candidate Info ({{ $outsourcing_type }})">Candidate Info</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            @if($isAds)
                                <span class="badge" style="font-size: 8.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; background: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fecaca !important; text-transform: uppercase !important;">Ads</span>
                            @endif
                            <!-- Candidate Availability Status (Bench Talent Canonical Palette) -->
                            <span class="cd-status-badge {{ $statusPillClass }} stage_sub_stage cursor-pointer" data-row-id="{{ $row->id }}" title="Click to change status" style="{{ $statusPillStyle }} font-size: 8.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; text-transform: uppercase !important; letter-spacing: 0.3px !important;">
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
                                <!-- 1. Candidate Name (All Candidates Tracker Typography) -->
                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                                    class="cd-talent-name {{ $profile_sidebar_css }}"
                                    title="{{ ucwords(@$row->full_name) }}"
                                    style="font-size: 12.5px !important; font-weight: 700 !important; color: #0f172a !important; line-height: 1.25 !important; display: block !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; text-decoration: none !important;">
                                    {{ $displayFullName }}
                                </a>
                                <!-- 2. Designation -->
                                <div class="cd-talent-role"
                                    title="{{ ucwords(@$row->current_designation) }}"
                                    style="font-size: 11px !important; font-weight: 500 !important; color: #475569 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; line-height: 1.25 !important; margin-top: 1px !important;">
                                    {{ $displayDesignation !== '' ? $displayDesignation : '—' }}
                                </div>
                                <!-- 3. Bench Company Name (Reduced per candidate tracker standard) -->
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

                <!-- Sub-Card 2: Bill Rate (Compensation & Experience) -->
                <div class="cd-talent-subcard cd-subcard-comp-exp">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate">BILL RATE</span>
                        <!-- Service Category: Paid / Free -->
                        <span class="badge" title="Service Category: {{ @$row->comission_type == 2 ? 'Paid Services' : 'Free Services' }}" style="font-size: 8.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; {{ @$row->comission_type == 2 ? 'background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important;' : 'background: #f1f5f9 !important; color: #64748b !important; border: 1px solid #e2e8f0 !important;' }}">
                            <i class="fa-solid fa-circle-check me-1" style="font-size: 8px !important;"></i>{{ @$row->comission_type == 2 ? 'Paid' : 'Free' }}
                        </span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <!-- 1. Bill Rate (Clean amount display without repeated "Bill Rate" words) -->
                        <div class="cd-val-salary d-flex align-items-baseline gap-1" title="Bill Rate: {{ $plainCurrencySymbol }}{{ $billAmountClean }} / {{ $bill_rate_mode }}">
                            <strong style="font-size: 12px !important; font-weight: 700 !important; color: #0f172a !important; line-height: 1.25 !important;">{{ $plainCurrencySymbol }}{{ $billAmountClean }}</strong>
                            <span style="font-size: 10px !important; color: #64748b !important; font-weight: 500 !important;">/{{ mb_substr($bill_rate_mode, 0, 1) }}</span>
                        </div>
                        <!-- 2. Total Experience & Relevant Experience (Reduced font size of numbers) -->
                        <div class="cd-val-exp d-flex align-items-center gap-1 mt-0.5 text-truncate" title="Total Experience: {{ $formattedExperience }} Yrs{{ (!empty($formattedRelExperience) && $formattedRelExperience > 0) ? ' | Rel: ' . $formattedRelExperience . ' Yrs' : '' }}" style="font-size: 10px !important; font-weight: 500 !important; color: #475569 !important; line-height: 1.3 !important;">
                            <i class="fa-solid fa-briefcase me-1 text-muted" style="width: 12px; text-align: center; font-size: 9px !important;"></i>
                            <span style="font-size: 10px !important;"><strong style="color: #0f172a !important; font-size: 10px !important; font-weight: 700 !important;">{{ $formattedExperience !== '' ? $formattedExperience : '0' }} Y</strong> Total</span>
                            @if (!empty($formattedRelExperience) && $formattedRelExperience > 0)
                                <span class="ms-1" style="color: #64748b !important; font-size: 9.5px !important;">(Rel <strong style="color: #334155 !important; font-size: 9.5px !important; font-weight: 600 !important;">{{ $formattedRelExperience }} Y</strong>)</span>
                            @endif
                        </div>
                        <!-- 3. Notice Period (Changed from Notice to NP) -->
                        <div class="cd-val-avail {{ $availClass }} d-flex align-items-center gap-1 mt-0.5 text-truncate" title="Notice Period: {{ $availLine }}" style="font-size: 10px !important; font-weight: 500 !important; color: #475569 !important; line-height: 1.3 !important;">
                            <i class="fa-regular fa-clock me-1 text-muted" style="width: 12px; text-align: center; font-size: 9.5px !important;"></i>
                            <span style="color: #64748b !important; font-weight: 500 !important; font-size: 10px !important;">NP:</span>
                            <strong class="{{ $availClass }}" style="font-size: 10px !important; font-weight: 600 !important;">{{ $availLine }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 3: Location & Education (Reduced text size per Candidate Tracker standard) -->
                <div class="cd-talent-subcard cd-subcard-avail-loc">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate" style="font-size: 9.5px !important; font-weight: 700 !important;">LOCATION &amp; DEGREE</span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <!-- First Row: City, State, Country -->
                        <div class="cd-val-location text-truncate d-flex align-items-center" style="font-size: 11px !important; font-weight: 600 !important; line-height: 1.25 !important; color: #0f172a !important;" title="{{ $fullLocation }}">
                            <i class="fa-solid fa-location-dot me-1 text-muted" style="width: 13px; text-align: center; font-size: 9.5px !important;"></i>
                            <span class="text-truncate" style="font-size: 11px !important; font-weight: 600 !important; color: #0f172a !important;">{{ $fullLocation }}</span>
                        </div>
                        <!-- Second Row: Work Mode (High contrast - WCAG AAA) -->
                        <div class="cd-val-wm text-truncate d-flex align-items-center mt-1" style="font-size: 10px !important; font-weight: 500 !important; color: #475569 !important; line-height: 1.25 !important;" title="Work Mode: {{ $wmLabel }}">
                            <i class="{{ $wmIcon }} me-1 text-primary" style="width: 13px; text-align: center; font-size: 9px !important;"></i>
                            <span class="text-truncate {{ $wmClass }} fw-semibold" style="font-size: 10px !important; font-weight: 600 !important;">{{ $wmLabel }}</span>
                        </div>
                        <!-- Third Row: Qualification / Degree in Uppercase -->
                        <div class="cd-val-edu text-truncate d-flex align-items-center mt-1" style="font-size: 10px !important; font-weight: 500 !important; color: #64748b !important; line-height: 1.25 !important; text-transform: uppercase !important;" title="{{ !empty($qualificationName) ? strtoupper($qualificationName) : 'Education: —' }}">
                            <i class="fa-solid fa-graduation-cap me-1 text-muted" style="width: 13px; text-align: center; font-size: 9.5px !important;"></i>
                            <span class="text-truncate" style="font-size: 10px !important; font-weight: 500 !important; color: #64748b !important; text-transform: uppercase !important;">{{ !empty($qualificationName) ? strtoupper($qualificationName) : '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 4: Relationship Manager (Reduced font size per user instruction) -->
                <div class="cd-talent-subcard cd-subcard-manager">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label text-uppercase text-truncate" style="font-size: 9.5px !important; font-weight: 700 !important;"><i class="fa-solid fa-user-tie me-1 text-primary"></i>Relationship Mgr</span>
                    </div>
                    <div class="cd-subcard-body pt-1">
                        <div class="cd-manager-name text-truncate d-flex align-items-center" style="font-size: 11px !important; font-weight: 600 !important; line-height: 1.25 !important; min-height: 18px; color: #0f172a !important;" title="{{ $managerName }}{{ $extra }}">
                            <i class="fa-regular fa-circle-user me-1 text-primary" style="width: 13px; text-align: center; font-size: 9.5px !important;"></i>
                            <span class="text-dark text-truncate" style="font-size: 11px !important; color: #0f172a !important; font-weight: 600 !important;">{{ $displayManagerName }}</span>
                        </div>
                        <div class="text-truncate d-flex align-items-center mt-1" style="font-size: 10px !important; font-weight: 500 !important; color: #64748b !important; line-height: 1.25 !important;">
                            <i class="fa-regular fa-building me-1 text-muted" style="width: 13px; text-align: center; font-size: 9px !important;"></i>
                            <span class="text-truncate" style="font-size: 10px !important; font-weight: 500 !important; color: #64748b !important;">In-House Talent</span>
                        </div>
                        <div class="mt-1 d-flex align-items-center">
                            <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="manager-change hmb-listing cd-rm-change-btn d-inline-flex align-items-center" style="font-size: 10px !important; font-weight: 600 !important; color: #009ef7 !important; line-height: 1.25 !important;">
                                <i class="fa-solid fa-pen-to-square me-1" style="font-size: 9px !important;"></i>Change
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
                                <span class="badge d-inline-flex align-items-center" style="background-color: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; font-size: 9.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; text-transform: uppercase !important;">
                                    <i class="fa-solid fa-shield-alt me-1" style="color: #059669 !important; font-size: 8.5px;"></i>Direct Client
                                </span>
                            </a>
                            <a href="javascript:void(0);" class="btn-similar-clients fw-bolder text-dark text-hover-primary"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="direct"
                                style="font-size: 11px !important; font-weight: 700 !important; color: #0f172a !important;">
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
                                <span class="badge d-inline-flex align-items-center" style="background-color: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; font-size: 9.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; text-transform: uppercase !important;">
                                    <i class="fa-solid fa-building me-1" style="color: #2563eb !important; font-size: 8.5px;"></i>Internal Client
                                </span>
                            </a>
                            <a href="javascript:void(0);" class="btn-similar-clients fw-bolder text-dark text-hover-primary"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="internal"
                                style="font-size: 11px !important; font-weight: 700 !important; color: #0f172a !important;">
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
                                <span class="badge d-inline-flex align-items-center" style="background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fcd34d !important; font-size: 9.5px !important; font-weight: 700 !important; padding: 1.5px 6px !important; border-radius: 4px !important; text-transform: uppercase !important;">
                                    <i class="fa-solid fa-id-badge me-1" style="color: #d97706 !important; font-size: 8.5px;"></i>My Organization
                                </span>
                            </a>
                            <a href="javascript:void(0);" class="btn-similar-clients fw-bolder text-dark text-hover-primary"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="my_org"
                                style="font-size: 11px !important; font-weight: 700 !important; color: #0f172a !important;">
                                {{ $recomMyOrgCount }} <i class="fa-solid fa-chevron-right text-muted fs-9 ms-0.5"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 6: Clients Pipeline Activity (All Candidates Tracker Standard) -->
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

            <!-- Sub-Card 7 / Bottom Activity Strip: Keywords on Left, Timestamp & Status on Right -->
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

                <!-- Right: Added Date & Time (Rule 24), Days on Bench & Change Status Button -->
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
                    <button type="button" class="btn btn-sm stage_sub_stage" data-row-id="{{ $row->id }}"
                        style="height: 26px; font-size: 10.5px; font-weight: 600; padding: 3px 10px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                        <i class="fa-solid fa-pen-to-square me-1" style="font-size: 9.5px;"></i>Change Status
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
