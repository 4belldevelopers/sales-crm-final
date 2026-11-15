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

            // Experience formatting
            $experience = trim((string) @$row->total_exp);
            if (strpos($experience, '.') !== false) {
                $parts = explode('.', $experience);
                $formattedExperience = (empty($parts[1]) || $parts[1] === '00') ? $parts[0] : $experience;
            } else {
                $formattedExperience = $experience ?: '0';
            }

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

            // Location formatting
            $row_city_name = @$row->city_name;
            $row_country_name = @$row->country_name;
            $cityCountry = trim(ucwords($row_city_name) . ($row_country_name ? ', ' . ucwords($row_country_name) : ''), ', ');
            if ($cityCountry === '') { $cityCountry = '—'; }

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

        <!-- Master Primary Talent Card (Rule 20 Modern Card + Sub-Card Enterprise Standard) -->
        <article class="cd-talent-card" data-bench-row-id="{{ $row->id }}" data-candidate-id="{{ $row->id }}" style="border-left-color: {{ $statusBorderAccent }} !important;">
            <!-- Upper Sub-Cards Grid (6 Discrete Sections) -->
            <div class="cd-talent-card-grid">

                <!-- Sub-Card 1: Talent Profile & Status -->
                <div class="cd-talent-subcard cd-subcard-talent">
                    <div class="cd-subcard-header">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="form-check form-check-sm form-check-custom form-check-solid m-0">
                                <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox"
                                    data-skills="{{ e($row->skills ?? '') }}"
                                    data-cname="{{ e($row->full_name ?? '') }}"
                                    value="{{ $row->id }}"
                                    title="Select candidate" />
                            </div>
                            <span class="cd-subcard-label">Talent Profile</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            @if($isAds)
                                <span class="badge badge-light-danger fs-9 py-0 px-1">Ads</span>
                            @endif
                            <span class="cd-status-badge {{ $statusPillClass }} stage_sub_stage" data-row-id="{{ $row->id }}" title="Click to change status" style="cursor: pointer;">
                                {{ $statusName }}
                            </span>
                        </div>
                    </div>
                    <div class="cd-subcard-body">
                        <div class="d-flex align-items-start gap-2">
                            <div class="symbol symbol-circle symbol-36px flex-shrink-0" style="overflow:hidden; border-radius:50%;">
                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="{{ $profile_sidebar_css }} d-flex w-100 h-100" title="{{ ucwords(@$row->full_name) }}">
                                    <div class="symbol-label {{ $bgTextDecore }} w-100 h-100 d-flex align-items-center justify-content-center fw-bold" style="border-radius:50%;">{{ $strVal }}</div>
                                </a>
                            </div>
                            <div class="d-flex flex-column min-w-0 flex-grow-1">
                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                                    class="cd-talent-name text-dark fw-bolder text-hover-primary fs-6 {{ $profile_sidebar_css }}"
                                    data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}">
                                    {{ $displayFullName }}
                                </a>
                                <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                                    class="cd-talent-role text-muted fw-bold d-block fs-7 text-hover-primary {{ $profile_sidebar_css }}"
                                    data-toggle="tooltip" title="{{ ucwords(@$row->current_designation) }}">
                                    {{ $displayDesignation }}
                                </a>
                                <div class="cd-talent-type-line mt-1">
                                    <span class="badge badge-light-info fs-9 py-0 px-1"><i class="fas fa-briefcase me-1"></i>{{ $outsourcing_type }}</span>
                                    <span class="text-muted fs-8 ms-1">{{ $subS }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 2: Compensation & Experience -->
                <div class="cd-talent-subcard cd-subcard-comp-exp">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Comp &amp; Exp</span>
                    </div>
                    <div class="cd-subcard-body">
                        <div class="cd-val-salary">
                            <strong>{!! $bill_rate_currency !!}{{ $billAmountClean }} /{{ mb_substr($bill_rate_mode, 0, 1) }}</strong>
                        </div>
                        <div class="cd-val-service {{ $serviceClass }}">
                            <i class="fa fa-circle-check fs-8 me-1"></i>{{ $serviceType }}
                        </div>
                        <div class="cd-val-exp text-dark fw-bold">
                            <i class="far fa-clock text-muted me-1"></i>{{ $formattedExperience }} Yrs Total
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 3: Availability & Work Mode -->
                <div class="cd-talent-subcard cd-subcard-avail-mode">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Availability &amp; Mode</span>
                    </div>
                    <div class="cd-subcard-body">
                        <div class="cd-val-avail {{ $availClass }} fw-bolder">
                            <i class="far fa-calendar-check me-1"></i>{{ $availLine }}
                        </div>
                        <div class="cd-val-wm {{ $wmClass }} fw-bold">
                            <i class="fas fa-building me-1"></i>{{ $wmLabel }}
                        </div>
                        @if ($benchDaysCount !== null)
                            <div class="cd-val-aging {{ $benchDaysClass }} fw-bold fs-8">
                                <i class="far fa-hourglass me-1"></i>{{ $benchDaysCount }} Days on Bench
                            </div>
                        @else
                            <div class="text-muted fs-8">
                                <i class="far fa-user me-1"></i>In-House Active
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sub-Card 4: Location & Education -->
                <div class="cd-talent-subcard cd-subcard-location-edu">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Location &amp; Degree</span>
                    </div>
                    <div class="cd-subcard-body">
                        <div class="cd-val-location text-dark fw-bold wrap-2" data-toggle="tooltip" title="{{ $cityCountry }}">
                            <i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $cityCountry }}
                        </div>
                        <div class="cd-val-edu text-muted fw-bold wrap-2 fs-8" data-toggle="tooltip" title="{{ @$row->qualification_name }}" style="text-transform:uppercase;">
                            <i class="fas fa-graduation-cap text-muted me-1"></i>{{ @$row->qualification_name ?: '—' }}
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 5: Relationship Manager -->
                <div class="cd-talent-subcard cd-subcard-manager">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label"><i class="fa fa-user-tie me-1"></i>Relationship Mgr</span>
                    </div>
                    <div class="cd-subcard-body">
                        <div class="cd-manager-name text-dark fw-bolder wrap-2" data-toggle="tooltip" title="{{ $managerName }}{{ $extra }}">
                            {{ $displayManagerName }}
                        </div>
                        <div class="mt-1">
                            <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="manager-change hmb-listing cd-rm-change-btn">
                                <i class="fa fa-pen me-1"></i>Change
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 6: Clients Pipeline Activity -->
                <div class="cd-talent-subcard cd-subcard-pipeline col-pipeline-activity">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">Clients Pipeline Activity</span>
                    </div>
                    <div class="cd-subcard-body p-1">
                        <div class="pipe">
                            <div class="w-100">
                                <div class="pipe-grid">
                                    <div class="h">INW</div><div class="h">SOU</div><div class="h">SCR</div><div class="h">EVA</div><div class="h">NEG</div><div class="h">OFF</div><div class="h">JOI</div><div class="h">REJ</div>
                                    <div class="v"><a href="{{ $exploreBase }}/all" class="text-hover-primary" data-bench-pipe="inreview">{{ $row->InReview }}</a></div>
                                    <div class="v"><a href="{{ $exploreBase }}/1" class="text-hover-primary" data-bench-pipe="sourcing">{{ $row->Sourcing }}</a></div>
                                    <div class="v"><a href="{{ $exploreBase }}/2" class="text-hover-primary" data-bench-pipe="screening">{{ $row->Screening }}</a></div>
                                    <div class="v"><a href="{{ $exploreBase }}/3" class="text-hover-primary" data-bench-pipe="evaluation">{{ $row->Evaluation }}</a></div>
                                    <div class="v"><a href="{{ $exploreBase }}/4" class="text-hover-primary" data-bench-pipe="negotiation">{{ $row->Negotiation }}</a></div>
                                    <div class="v"><a href="{{ $exploreBase }}/5" class="text-hover-primary" data-bench-pipe="offered">{{ $row->Offered }}</a></div>
                                    <div class="v"><a href="{{ $exploreBase }}/6" class="text-hover-primary" data-bench-pipe="joined">{{ $row->Joined }}</a></div>
                                    <div class="v"><a href="{{ $exploreBase }}/7" class="text-hover-primary" data-bench-pipe="rejected">{{ $row->Rejected }}</a></div>
                                </div>
                                <div class="pipe-foot justify-content-between mt-2 pt-1 border-top border-gray-200">
                                    <span class="pipe-stat text-primary fw-bold">Interview: <strong data-bench-pipe-interview="{{ $row->id }}" class="ms-1">{{ $row->InReview }}</strong></span>
                                    <span class="pipe-stat">Views: <strong class="ms-1">{{ $viewCount }}</strong></span>
                                    <span class="pipe-stat">Downloads: <strong class="ms-1">{{ $downloadCount }}</strong></span>
                                </div>
                            </div>
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
