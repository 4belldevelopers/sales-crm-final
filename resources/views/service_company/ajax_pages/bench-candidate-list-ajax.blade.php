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

            // Status Badge & Border Theme Accent
            $statusId = (int) ($row->status ?? 0);
            $statusName = $row->master_status_name ?? 'Bench';
            if ($statusId == 1) { // Pool
                $statusPillClass = 'status-pool';
                $statusBorderAccent = '#7c3aed';
                $subS = 'Newly added';
            } elseif ($statusId == 2) { // Bench
                $statusPillClass = 'status-bench';
                $statusBorderAccent = '#10b981';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'Open For Work';
            } elseif ($statusId == 4) { // On Hold / Paused
                $statusPillClass = 'status-hold';
                $statusBorderAccent = '#f59e0b';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'On Hold';
            } elseif ($statusId == 3) { // Placed
                $statusPillClass = 'status-placed';
                $statusBorderAccent = '#009ef7';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'Placed';
            } elseif ($statusId == 5) { // Exited
                $statusPillClass = 'status-exited';
                $statusBorderAccent = '#94a3b8';
                $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? 'Exited';
            } else {
                $statusPillClass = 'status-bench';
                $statusBorderAccent = '#10b981';
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

        <!-- Master Primary Talent Card (Rule 20 Modern 5-Subcard Layout) -->
        <article class="cd-talent-card" data-bench-row-id="{{ $row->id }}" data-candidate-id="{{ $row->id }}" style="border-left-color: {{ $statusBorderAccent }} !important;">
            <!-- Upper Sub-Cards Grid (5 Spacious Discrete Sections) -->
            <div class="cd-talent-card-grid">

                <!-- Sub-Card 1: Talent Profile & Status (Rule 20 Standard) -->
                <div class="cd-talent-subcard cd-subcard-talent">
                    <div class="cd-subcard-header">
                        <div class="d-flex align-items-center gap-1.5 min-w-0">
                            <div class="form-check form-check-sm form-check-custom form-check-solid m-0">
                                <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox"
                                    data-skills="{{ e($row->skills ?? '') }}"
                                    data-cname="{{ e($row->full_name ?? '') }}"
                                    value="{{ $row->id }}"
                                    title="Select candidate" />
                            </div>
                            <!-- Hiring Category: Full-Time / Contract -->
                            <span class="badge badge-light-primary text-primary fs-9 py-0.5 px-1.5 fw-bold border border-primary-subtle text-truncate" title="Hiring Category">
                                <i class="fas fa-briefcase me-1" style="font-size: 8.5px;"></i>{{ $outsourcing_type }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            @if($isAds)
                                <span class="badge badge-light-danger fs-9 py-0 px-1">Ads</span>
                            @endif
                            <!-- Candidate Availability Status (Bench) -->
                            <span class="cd-status-badge {{ $statusPillClass }} stage_sub_stage" data-row-id="{{ $row->id }}" title="Click to change status" style="cursor: pointer;">
                                {{ $statusName }}
                            </span>
                        </div>
                    </div>
                    <div class="cd-subcard-body">
                        <div class="d-flex align-items-start gap-2.5">
                            <!-- Refined Visual Profile Avatar Icon -->
                            <div class="cd-talent-avatar symbol symbol-38px flex-shrink-0" style="overflow:hidden; border-radius:10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(15,23,42,0.04);">
                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="{{ $profile_sidebar_css }} d-flex w-100 h-100" title="{{ ucwords(@$row->full_name) }}">
                                    <div class="symbol-label {{ $bgTextDecore }} w-100 h-100 d-flex align-items-center justify-content-center fw-bolder" style="border-radius:10px; font-size: 13.5px;">{{ $strVal }}</div>
                                </a>
                            </div>
                            <div class="d-flex flex-column min-w-0 flex-grow-1">
                                <!-- 1. Candidate Name -->
                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                                    class="cd-talent-name text-dark fw-bolder text-hover-primary fs-6 {{ $profile_sidebar_css }} text-truncate"
                                    data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}">
                                    {{ $displayFullName }}
                                </a>
                                <!-- 2. Designation -->
                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                                    class="cd-talent-role text-muted fw-bold d-block fs-7 text-hover-primary {{ $profile_sidebar_css }} text-truncate"
                                    data-toggle="tooltip" title="{{ ucwords(@$row->current_designation) }}">
                                    {{ $displayDesignation }}
                                </a>
                                <!-- 3. Bench Company Name -->
                                <div class="cd-talent-company d-flex align-items-center gap-1 mt-0.5 text-truncate" title="Bench Company: {{ $benchCompanyName }}">
                                    <i class="fa fa-building text-primary" style="font-size: 9.5px;"></i>
                                    <span class="fs-8 fw-semibold text-gray-700 text-truncate">{{ $displayBenchCompany }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 2: Compensation & Experience -->
                <div class="cd-talent-subcard cd-subcard-comp-exp">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">COMP &amp; EXP</span>
                        <!-- Service Category: Paid / Free Services -->
                        <span class="badge {{ @$row->comission_type == 2 ? 'badge-light-success text-success border border-success-subtle' : 'badge-light-secondary text-muted border border-gray-300' }} fs-9 py-0.5 px-1.5 fw-bold" title="Service Category">
                            <i class="fa fa-circle-check me-1" style="font-size: 8.5px;"></i>{{ $serviceType }}
                        </span>
                    </div>
                    <div class="cd-subcard-body">
                        <!-- 1. Bill Rate -->
                        <div class="cd-val-salary d-flex align-items-center gap-1">
                            <span class="fs-8 text-muted fw-semibold">Bill Rate:</span>
                            <strong class="fs-6 text-dark">{!! $bill_rate_currency !!}{{ $billAmountClean }} /{{ mb_substr($bill_rate_mode, 0, 1) }}</strong>
                        </div>
                        <!-- 2. Total Experience & Relevant Experience (4 Y Rel format) -->
                        <div class="cd-val-exp text-dark fw-bold fs-7 d-flex align-items-center gap-1 mt-0.5">
                            <i class="far fa-clock text-muted" style="font-size: 11px;"></i>
                            <span>{{ $formattedExperience }} Y Total</span>
                            @if (!empty($formattedRelExperience) && $formattedRelExperience > 0)
                                <span class="badge badge-light-primary text-primary fs-9 py-0 px-1.5 fw-bolder" title="Relevant Experience">{{ $formattedRelExperience }} Y Rel</span>
                            @endif
                        </div>
                        <!-- 3. Notice Period -->
                        <div class="cd-val-avail {{ $availClass }} fw-bold fs-8 d-flex align-items-center gap-1 mt-0.5" title="Notice Period">
                            <i class="far fa-calendar-check" style="font-size: 11px;"></i>
                            <span>Notice: <strong>{{ $availLine }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 3: Location & Education -->
                <div class="cd-talent-subcard cd-subcard-avail-loc">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">LOCATION &amp; EDUCATION</span>
                    </div>
                    <div class="cd-subcard-body">
                        <!-- First Row: City, State, Country -->
                        <div class="cd-val-location text-dark fw-bold fs-8 text-truncate mb-1" data-toggle="tooltip" title="{{ $fullLocation }}">
                            <i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 11px;"></i>
                            <span>{{ $fullLocation }}</span>
                        </div>
                        <!-- Second Row: Work Mode & Education / Qualification -->
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="cd-val-wm {{ $wmClass }} fw-bold fs-8 d-inline-flex align-items-center">
                                <i class="fas fa-building me-1" style="font-size: 10px;"></i>{{ $wmLabel }}
                            </span>
                            @if (!empty($qualificationName))
                                <span class="badge badge-light-secondary text-gray-700 fs-9 py-0.5 px-1.5 border border-gray-200 text-truncate" title="Qualification: {{ $qualificationName }}" style="max-width: 130px;">
                                    <i class="fas fa-graduation-cap me-1 text-primary" style="font-size: 9px;"></i>{{ \Illuminate\Support\Str::limit($qualificationName, 15) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 4: Relationship Manager -->
                <div class="cd-talent-subcard cd-subcard-manager">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label"><i class="fa fa-user-tie me-1"></i>Relationship Mgr</span>
                    </div>
                    <div class="cd-subcard-body">
                        <div class="cd-manager-name text-dark fw-bolder wrap-2 fs-7" data-toggle="tooltip" title="{{ $managerName }}{{ $extra }}">
                            {{ $displayManagerName }}
                        </div>
                        <div class="mt-1">
                            <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="manager-change hmb-listing cd-rm-change-btn">
                                <i class="fa fa-pen me-1"></i>Change
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 5: Similar Clients (Direct & Internal Clients) -->
                <div class="cd-talent-subcard cd-subcard-similar-clients">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label"><i class="fa fa-handshake me-1 text-primary"></i>Similar Clients</span>
                    </div>
                    <div class="cd-subcard-body">
                        <!-- Direct Client Mandates -->
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <a href="javascript:void(0);" class="btn-similar-clients text-hover-primary d-inline-flex align-items-center gap-1"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="direct"
                                title="View Potential Direct Clients">
                                <span class="badge badge-light-success py-1 px-1.5 fs-9 fw-bold border border-success-subtle d-inline-flex align-items-center">
                                    <i class="fa fa-shield-alt text-success me-1" style="font-size: 8.5px;"></i>Direct Client
                                </span>
                            </a>
                            <a href="javascript:void(0);" class="btn-similar-clients fs-8 fw-bolder text-dark text-hover-primary"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="direct">
                                {{ $recomDirectCount }} <i class="fa fa-chevron-right text-muted fs-9 ms-0.5"></i>
                            </a>
                        </div>
                        <!-- Internal Client Mandates -->
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="javascript:void(0);" class="btn-similar-clients text-hover-primary d-inline-flex align-items-center gap-1"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="internal"
                                title="View Potential Internal Clients">
                                <span class="badge badge-light-primary py-1 px-1.5 fs-9 fw-bold border border-primary-subtle d-inline-flex align-items-center">
                                    <i class="fa fa-building text-primary me-1" style="font-size: 8.5px;"></i>Internal Client
                                </span>
                            </a>
                            <a href="javascript:void(0);" class="btn-similar-clients fs-8 fw-bolder text-dark text-hover-primary"
                                data-bench-id="{{ $row->id }}"
                                data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                                data-bench-role="{{ e($row->current_designation ?? '') }}"
                                data-client-type="internal">
                                {{ $recomInternalCount }} <i class="fa fa-chevron-right text-muted fs-9 ms-0.5"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 6: Clients Pipeline Activity -->
                <div class="cd-talent-subcard cd-subcard-pipeline">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Clients Pipeline Activity</span>
                    </div>
                    <div class="cd-subcard-body p-2">
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
                        <div class="cd-pipe-summary d-flex align-items-center justify-content-between mt-2 pt-1 border-top border-gray-200">
                            <span class="pipe-stat text-primary fw-bold" style="font-size: 10px;">Interviews: <strong data-bench-pipe-interview="{{ $row->id }}" class="ms-1">{{ $row->InReview }}</strong></span>
                            <span class="pipe-stat text-muted" style="font-size: 10px;">Views: <strong class="ms-1 text-dark">{{ $viewCount }}</strong></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sub-Card 7 / Bottom Activity Strip -->
            <div class="cd-talent-card-bottom">
                <div class="cd-bottom-left d-flex align-items-center gap-2 flex-wrap">
                    @if ($stageDateLabel)
                        <span class="cd-bottom-date text-dark fw-bold">
                            <i class="far fa-calendar-alt text-muted me-1"></i>Added: <strong>{{ $stageDateLabel }}</strong>
                        </span>
                        <span class="text-muted">&bull;</span>
                    @endif
                    @if ($benchDaysCount !== null)
                        <span class="badge badge-light-warning {{ $benchDaysClass }} fw-bold fs-8 py-1 px-2" title="Days on Bench">
                            <i class="far fa-hourglass me-1"></i>{{ $benchDaysCount }} Days on Bench
                        </span>
                        <span class="text-muted">&bull;</span>
                    @endif
                    <div class="cd-bottom-skills d-flex align-items-center gap-1 flex-wrap">
                        @if (!empty($skillArr))
                            @foreach ($skillArr as $sval)
                                @if (trim($sval) !== '')
                                    <span class="cd-skill-pill">{{ ucfirst(trim($sval)) }}</span>
                                @endif
                            @endforeach
                        @else
                            <span class="text-muted fs-8">No specific skills listed</span>
                        @endif
                    </div>
                </div>
                <div class="cd-bottom-actions d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-light-info py-1 px-2.5 fs-8 fw-bold btn-similar-clients"
                        data-bench-id="{{ $row->id }}"
                        data-bench-name="{{ e($row->full_name ?? 'Talent') }}"
                        data-bench-role="{{ e($row->current_designation ?? '') }}"
                        data-client-type="direct">
                        <i class="fa fa-handshake me-1"></i>Similar Clients
                    </button>
                    <button type="button" class="btn btn-sm btn-light-primary py-1 px-3 fs-8 fw-bold {{ $profile_sidebar_css }}" data-row-id="{{ $row->id }}">
                        <i class="fa fa-user me-1"></i>View Profile
                    </button>
                    <button type="button" class="btn btn-sm btn-light-success py-1 px-3 fs-8 fw-bold stage_sub_stage" data-row-id="{{ $row->id }}">
                        <i class="fa fa-pen-to-square me-1"></i>Change Status
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
