@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $company_id = auth()->user()->company_id;
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            /**
             * Intent: Fetch candidate pipeline activity counts via GeneralMethod::bench_explore_listing_counter
             * to guarantee 100% count consistency between the bench candidates table and the bench explore view.
             * Tenancy: Scoped by candidate ID and logged-in company.
             */
            $bench_explore_listing_counter = GeneralMethod::bench_explore_listing_counter($row->id);
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

            // Work preference (Remote/Hybrid/Work from office) — shared by the Experience column's 2nd row.
            $wm = strtolower((string) @$row->wm);
            $wmMap = ['remote' => 'Remote', 'hybrid' => 'Hybrid', 'office' => 'Work from office'];
            $wmClassMap = ['remote' => 'text-success', 'hybrid' => 'text-primary', 'office' => 'text-warning'];
            $wmLabel = $wmMap[$wm] ?? '—';
            $wmClass = $wmClassMap[$wm] ?? 'text-muted';

            // Employment type (Full-time/Contract) — shared by the Availability column's 2nd row.
            $outsourcing_type = $row->outsourcing_type == 2 ? 'Contract' : 'Full-time';

            // Word-aware labels let the widened Talent and RM columns show useful names without spilling across cells.
            $limitWords = function ($text, $limit) {
                $words = preg_split('/\s+/', trim((string) $text), -1, PREG_SPLIT_NO_EMPTY);
                if (count($words) <= $limit) {
                    return trim((string) $text);
                }

                return implode(' ', array_slice($words, 0, $limit)) . '...';
            };
        @endphp
        <tr style="height: 102px; position: relative;">
            {{-- Checkbox --}}
            <td class="col-select">
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" data-skills="{{ e($row->skills ?? '') }}" data-cname="{{ e($row->full_name ?? '') }}" value="{{ $row->id }}" />
                </div>
            </td>

            {{-- Talent (avatar + name + designation + skills) --}}
            <td class="col-talent-name">
                @php
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->full_name);
                    $str = $strVal = strtolower($str[0]);
                    $str = ord($str);
                    if ($str >= 97 && $str <= 101) {
                        $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                    } elseif ($str >= 102 && $str <= 106) {
                        $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                    } elseif ($str >= 107 && $str <= 111) {
                        $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                    } elseif ($str >= 112 && $str <= 116) {
                        $bgTextDecore = 'fs-3 bg-light-success text-success';
                    } elseif ($str >= 117 && $str <= 121) {
                        $bgTextDecore = 'fs-3 bg-dark text-secondary';
                    } else {
                        $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                    }
                    $profile_sidebar_css = 'bench_show_application_detail';

                    /* Jobs Tracker style meta row: stage date first, then skill chips in the same compact strip. */
                    $skillArr = array_values(array_filter(array_map('trim', explode(',', (string) ($row->skills ?? '')))));
                    $stageMovedAt = $row->updated_at ?? $row->created_at ?? null;
                    $stageDateLabel = $stageMovedAt ? Carbon::parse($stageMovedAt)->format('d M y') : '';
                    $stageDateChip = $stageDateLabel
                        ? '<span class="job-date-tag"><svg fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8"/></svg>' . e($stageDateLabel) . '</span>'
                        : '';

                    $skilltag = '';
                    foreach ($skillArr as $sval) {
                        if ($sval === '') { continue; }
                        $skilltag .= '<span class="skill-tag">' . e(ucfirst($sval)) . '</span> ';
                    }
                    $award = (@$row->ads_switch == 'Yes') ? '<span class="badge badge-light-danger my-0">Ads</span>' : '';
                    $displayFullName = $limitWords(ucwords(@$row->full_name), 25);
                    $displayDesignation = $limitWords(ucwords(@$row->current_designation), 25);

                    /* View / Download counts pre-fetched in bulk via controller */
                    $viewCount = $viewCounts[$row->id] ?? $row->viewCount ?? 0;
                    $downloadCount = $downloadCounts[$row->id] ?? $row->downloadCount ?? 0;
                @endphp
                <div class="d-flex align-items-start">
                    <div class="symbol symbol-circle symbol-40px me-2 flex-shrink-0" style="overflow:hidden; border-radius:50%;">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="{{ $profile_sidebar_css }} d-flex w-100 h-100">
                            <div class="symbol-label {{ $bgTextDecore }} w-100 h-100 d-flex align-items-center justify-content-center" style="border-radius:50%;">{{ strtoupper($strVal) }}</div>
                        </a>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                            class="text-dark fw-bolder text-hover-primary fs-6 {{ $profile_sidebar_css }} talent-name-main"
                            data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}">{{ $displayFullName }}</a>
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                            class="text-muted fw-bold d-block fs-7 text-hover-primary {{ $profile_sidebar_css }} talent-name-role"
                            data-toggle="tooltip" title="{{ ucwords(@$row->current_designation) }}">{{ $displayDesignation }}</a>
                    </div>
                </div>
                {{-- Stage date + Ads + Skills footer strip (bottom-left) --}}
                @if($stageDateChip || $award || $skilltag)
                <div class="job-meta-strip">
                    <div class="job-meta-line">{!! $stageDateChip !!}{!! $award !!}{!! $skilltag !!}</div>
                </div>
                @endif

            </td>

            {{-- Experience: Experience (1) · Work preference (2) --}}
            <td class="col-experience">
                @php
                    $experience = trim((string) @$row->total_exp);
                    if (strpos($experience, '.') !== false) {
                        $parts = explode('.', $experience);
                        // Drop the decimal entirely when it's ".00" OR simply empty (e.g. a stray
                        // trailing dot like "3." from older records) — both should just read "3".
                        $formattedExperience = (empty($parts[1]) || $parts[1] === '00') ? $parts[0] : $experience;
                    } else {
                        $formattedExperience = $experience;
                    }
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ $formattedExperience }} Yrs</a>
                    <span class="fw-bold d-block fs-7 {{ $wmClass }}">{{ $wmLabel }}</span>
                </div>
            </td>

            {{-- Availability: conditional notice text (1) · Employment type (2) --}}
            <td class="col-availability">
                @php
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
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="fw-bolder fs-6 {{ $availClass }}">{{ $availLine }}</a>
                    <span class="text-muted fw-bold d-block fs-7">{{ $outsourcing_type }}</span>
                </div>
            </td>

            {{-- Location: City & Country (1) · Education (2) --}}
            <td class="col-location">
                @php
                    $row_city_name = @$row->city_name;
                    $row_country_name = @$row->country_name;
                    $cityCountry = trim(ucwords($row_city_name) . ($row_country_name ? ', ' . ucwords($row_country_name) : ''), ', ');
                    if ($cityCountry === '') { $cityCountry = '—'; }
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 wrap-2"
                        data-toggle="tooltip" title="{{ $cityCountry }}">{{ $cityCountry }}</a>
                    <span class="text-muted fw-bold d-block fs-7 wrap-2" data-toggle="tooltip"
                        title="{{ @$row->qualification_name }}" style="text-transform:uppercase;">{{ @$row->qualification_name }}</span>
                </div>
            </td>

            {{-- Expected Salary: Salary (1) · Free/Paid service (2) --}}
            <td class="col-salary">
                @php
                    $currencyBenchMode = $companyCurrencyBenchshowMode ?? GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $currencyBenchMode->currency_code, $currencyBenchMode->show_mode);
                    $bill_rate_currency = $currencySymbole[$billFormatViewSalary->currency];
                    $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode];
                    $serviceType = @$row->comission_type == 2 ? 'Paid Services' : (@$row->comission_type == 1 ? 'Free Services' : '—');
                    $serviceClass = @$row->comission_type == 2 ? 'text-success' : 'text-muted';
                    // Whole-number display — no redundant ".00" decimals on the headline salary figure.
                    $billAmountClean = number_format((float) $billFormatViewSalary->amount, 0);
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{!! $bill_rate_currency !!}{{ $billAmountClean }} /{{ mb_substr($bill_rate_mode, 0, 1) }}</a>
                    <span class="fw-bold d-block fs-7 {{ $serviceClass }}"><i class="fa fa-circle-check" style="font-size:10px !important;"></i> {{ $serviceType }}</span>
                </div>
            </td>

            {{-- Bench Status --}}
            <td class="col-status">
                @php
                    // Pool (status 1) always shows purple + "Newly added", matching the Change Status
                    // drawer's Pool colour/description — the shared subline helper doesn't cover this case.
                    if ($row->status == 1) {
                        $statusClass = 'text-Apool';
                        $subS = 'Newly added';
                    } else {
                        $statusClass = $row->css_style;
                        $subS = optional(GeneralMethod::bench_status_subline($row->status))->name ?? '';
                    }
                    // 3rd row: how long the talent has been on bench (fixed whole days, only while status = Bench).
                    // Turns red once the talent has been sitting on bench for more than 30 days.
                    $benchDaysCount = null;
                    if ($row->status == 2 && !empty($row->created_at)) {
                        $benchDaysCount = (int) Carbon::parse($row->created_at)->diffInDays(Carbon::now());
                    }
                    $benchDaysClass = ($benchDaysCount !== null && $benchDaysCount > 30) ? 'text-danger' : 'text-warning';
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                        class="fw-bold fs-6 stage_sub_stage {{ $statusClass }}">{{ $row->master_status_name }}</a>
                    <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $subS }}">{{ $subS }}</span>
                    @if ($benchDaysCount !== null)
                        <span class="fw-bolder d-block fs-7 {{ $benchDaysClass }}">{{ $benchDaysCount }} Days</span>
                    @endif
                </div>
            </td>

            {{-- Relationship Manager (name + Change only — no filler subtitle, no avatar image) --}}
            <td class="col-manager-name">
                @php
                    if (!empty($row->hiring_hr)) {
                        $hiringHr = (array) $row->hiring_hr;
                        $hmCount = count($hiringHr);
                        $firstHr = (object) ($hiringHr[0] ?? []);
                        $managerName = !empty($firstHr->name) ? ucwords($firstHr->name) : 'Unassigned';
                        $extra = $hmCount > 1 ? ' +' . ($hmCount - 1) : '';
                    } else {
                        $hmCount = DB::table('bench_assign')
                            ->join('users', 'users.id', '=', 'bench_assign.user_id')
                            ->where('bench_assign.bench_applications_id', $row->id)
                            ->where('bench_assign.company_id', $company_id)
                            ->where('users.company_id', $company_id)
                            ->count();
                        $hmData = DB::table('bench_assign')
                            ->join('users', 'users.id', '=', 'bench_assign.user_id')
                            ->select('users.name')
                            ->where('bench_assign.bench_applications_id', $row->id)
                            ->where('bench_assign.company_id', $company_id)
                            ->where('users.company_id', $company_id)
                            ->orderBy('users.name', 'ASC')->take(1)->first();
                        $managerName = @$hmData->name ? ucwords($hmData->name) : 'Unassigned';
                        $extra = $hmCount > 1 ? ' +' . ($hmCount - 1) : '';
                    }
                    $displayManagerName = $limitWords($managerName . $extra, 20);
                @endphp
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex flex-column" style="min-width:0;">
                        <span class="text-dark fw-bolder fs-6 manager-name-main"
                              data-toggle="tooltip" title="{{ $managerName }}{{ $extra }}">{{ $displayManagerName }}</span>
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="manager-change hmb-listing" style="font-size:11px;">Change</a>
                    </div>
                </div>
            </td>

            {{-- Clients Pipeline Activity (mini stage grid) --}}
            <td class="col-pipeline-activity">
                <div class="pipe">
                    <div>
                        <div class="pipe-grid">
                            <div class="h">INW</div><div class="h">SOU</div><div class="h">SCR</div><div class="h">EVA</div><div class="h">NEG</div><div class="h">JOI</div><div class="h">REJ</div>
                            {{-- Intent: Clicking INW (InReview) opens the full bench explore list with stage = 'all' --}}
                            <div class="v"><a href="{{ $exploreBase }}/all" class="text-hover-primary">{{ $row->InReview }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/1" class="text-hover-primary">{{ $row->Sourcing }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/2" class="text-hover-primary">{{ $row->Screening }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/3" class="text-hover-primary">{{ $row->Evaluation }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/4" class="text-hover-primary">{{ $row->Negotiation }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/6" class="text-hover-primary">{{ $row->Joined }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/7" class="text-hover-primary">{{ $row->Rejected }}</a></div>
                        </div>
                        <div class="pipe-foot">
                            Interview: {{ $row->InReview }}
                            <span class="pipe-stat">Views: {{ $viewCount }}</span>
                            <span class="pipe-stat">Downloads: {{ $downloadCount }}</span>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9" style="text-align:center; color:#7e8299; padding:24px;">No data available in table</td>
    </tr>
@endif
