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

            // Work preference (Remote/Onsite/Hybrid) — shared by the Experience column's 2nd row.
            $wm = strtolower((string) @$row->wm);
            $wmMap = ['remote' => 'Remote', 'hybrid' => 'Hybrid', 'office' => 'Onsite'];
            $wmClassMap = ['remote' => 'text-success', 'hybrid' => 'text-primary', 'office' => 'text-warning'];
            $wmLabel = $wmMap[$wm] ?? '—';
            $wmClass = $wmClassMap[$wm] ?? 'text-muted';

            // Employment type (Full-time/Contract) — shared by the Availability column's 2nd row.
            $outsourcing_type = $row->outsourcing_type == 2 ? 'Contract' : 'Full-time';
        @endphp
        <tr>
            {{-- Checkbox --}}
            <td>
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" value="{{ $row->id }}" />
                </div>
            </td>

            {{-- Talent (avatar + name + designation + skills) --}}
            <td>
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

                    $skilltag = '';
                    $skillArr = explode(',', $row->skills);
                    if (empty($limit)) {
                        foreach (array_slice($skillArr, 0, 3) as $sval) {
                            $sval = trim($sval);
                            if ($sval === '') { continue; }
                            $skilltag .= '<span class="badge badge-light my-0">' . ucfirst($sval) . '</span> ';
                        }
                    }
                    $award = (@$row->ads_switch == 'Yes') ? '<span class="badge badge-light-danger my-0">Ads</span>' : '';
                @endphp
                <div class="d-flex align-items-start">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 flex-shrink-0">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="{{ $profile_sidebar_css }} d-flex w-100 h-100">
                            <div class="symbol-label {{ $bgTextDecore }} border-radius-5 w-100 h-100 d-flex align-items-center justify-content-center">{{ strtoupper($strVal) }}</div>
                        </a>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                            class="text-dark fw-bolder text-hover-primary fs-6 {{ $profile_sidebar_css }}"
                            data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}">{{ ucwords(mb_substr(@$row->full_name, 0, 20)) }}</a>
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                            class="text-muted fw-bold d-block fs-7 text-hover-primary {{ $profile_sidebar_css }}"
                            data-toggle="tooltip" title="{{ ucwords(@$row->current_designation) }}">{{ ucwords(mb_substr(@$row->current_designation, 0, 20)) }}</a>
                        @if ($award !== '')
                            <div class="d-flex align-items-center gap-3 mt-1">{!! $award !!}</div>
                        @endif
                        <div class="mt-1">{!! $skilltag !!}</div>
                    </div>
                </div>
            </td>

            {{-- Experience: Experience (1) · Work preference (2) --}}
            <td>
                @php
                    $experience = trim((string) @$row->total_exp);
                    if (strpos($experience, '.') !== false) {
                        $parts = explode('.', $experience);
                        $formattedExperience = (isset($parts[1]) && $parts[1] === '00') ? $parts[0] : $experience;
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
            <td>
                @php
                    if ($row->serving_notice_period_mode == 2) {
                        $availLine = 'Immediate';
                        $availClass = 'text-success';
                    } elseif ($row->serving_notice_period_mode == 0) {
                        $days = (int) $row->np_id;
                        $availLine = ($days > 0 ? $days : 0) . ' Days Joining';
                        $availClass = 'text-warning';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $lwdDate = $row->np_id ? date('d M y', strtotime($row->np_id)) : '';
                        if ($row->np_id && strtotime($row->np_id) < strtotime('today')) {
                            $availLine = 'Immediate';
                            $availClass = 'text-success';
                        } else {
                            $availLine = 'SNP LWD ' . $lwdDate;
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
            <td>
                @php
                    $row_city_name = @$row->city_name;
                    $row_country_name = @$row->country_name;
                    $cityCountry = trim(ucwords($row_city_name) . ($row_country_name ? ', ' . ucwords($row_country_name) : ''), ', ');
                    if ($cityCountry === '') { $cityCountry = '—'; }
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"
                        data-toggle="tooltip" title="{{ $cityCountry }}">{{ mb_substr($cityCountry, 0, 20) }}</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip"
                        title="{{ @$row->qualification_name }}" style="text-transform:uppercase;">{{ mb_substr(@$row->qualification_name, 0, 16) }}</span>
                </div>
            </td>

            {{-- Expected Salary: Salary (1) · Free/Paid service (2) --}}
            <td>
                @php
                    $currencyBenchshowMode = GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $currencyBenchshowMode->currency_code, $currencyBenchshowMode->show_mode);
                    $bill_rate_currency = $currencySymbole[$billFormatViewSalary->currency];
                    $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode];
                    $serviceType = @$row->comission_type == 2 ? 'Paid' : (@$row->comission_type == 1 ? 'Free' : '—');
                    $serviceClass = @$row->comission_type == 2 ? 'text-success' : 'text-muted';
                    // Whole-number display — no redundant ".00" decimals on the headline salary figure.
                    $billAmountClean = number_format((float) $billFormatViewSalary->amount, 0);
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{!! $bill_rate_currency !!}{{ $billAmountClean }} /{{ mb_substr($bill_rate_mode, 0, 1) }}</a>
                    <span class="fw-bold d-block fs-7 {{ $serviceClass }}"><i class="fa fa-circle-check" style="font-size:9px !important;"></i> {{ $serviceType }}</span>
                </div>
            </td>

            {{-- Bench Status --}}
            <td>
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
            <td>
                @php
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
                @endphp
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{ $managerName }}">{{ mb_substr($managerName, 0, 14) }}{{ $extra }}</span>
                    <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="manager-change hmb-listing">Change</a>
                </div>
            </td>

            {{-- Clients Pipeline Activity (mini stage grid) --}}
            <td>
                <div class="pipe">
                    <div>
                        <div class="pipe-grid">
                            <div class="h">INW</div><div class="h">SOU</div><div class="h">SCR</div><div class="h">EVA</div><div class="h">NEG</div><div class="h">JOI</div><div class="h">REJ</div>
                            <div class="v"><a href="{{ $exploreBase }}/3" class="text-hover-primary">{{ $row->InReview }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/1" class="text-hover-primary">{{ $row->Sourcing }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/2" class="text-hover-primary">{{ $row->Screening }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/3" class="text-hover-primary">{{ $row->Evaluation }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/4" class="text-hover-primary">{{ $row->Negotiation }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/6" class="text-hover-primary">{{ $row->Joined }}</a></div>
                            <div class="v"><a href="{{ $exploreBase }}/7" class="text-hover-primary">{{ $row->Rejected }}</a></div>
                        </div>
                        <div class="pipe-foot">
                            Interview: {{ $row->InReview }}
                            <span class="pipe-ico"><i class="fa fa-eye"></i> {{ rand(5, 60) }}</span>
                            <span class="pipe-ico"><i class="fa fa-download"></i> {{ rand(1, 20) }}</span>
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
