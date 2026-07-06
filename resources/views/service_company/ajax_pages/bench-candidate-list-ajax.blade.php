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

            $countForZero = $applicationCounter->allCandidate ?? 0;
            $row->Sourcing = $applicationCounter->Sourcing ?? 0;
            $row->Screening = $applicationCounter->Screening ?? 0;
            $row->Evaluation = $applicationCounter->Evaluation ?? 0;
            $row->Negotiation = $applicationCounter->Negotiation ?? 0;
            $row->Offered = $applicationCounter->Offered ?? 0;
            $row->Joined = $applicationCounter->Joined ?? 0;
            $row->Rejected = $applicationCounter->Rejected ?? 0;
            $row->InReview = $applicationCounter->Interview ?? 0;

            $exploreBase = url('/service_company/bench-candidates/bench_explore/' . $row->id);
        @endphp
        <tr>
            {{-- Checkbox --}}
            <td>
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" value="{{ $row->id }}" />
                </div>
            </td>

            {{-- Talent (avatar + name + designation + view/download counts + skills) --}}
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
                            if (trim($sval) === '') { continue; }
                            $skilltag .= '<span class="badge badge-light my-0">' . ucfirst(trim($sval)) . '</span> ';
                        }
                    }
                    $viewCount = DB::table('bench_count_view_download')->where('type', 1)->where('status', 0)->where('candidate_id', $row->id)->count();
                    $downloadCount = DB::table('bench_count_view_download')->where('type', 2)->where('status', 0)->where('candidate_id', $row->id)->count();
                    $award = (@$row->ads_switch == 'Yes') ? '<span class="badge badge-light-danger my-0">Ads</span>' : '';
                @endphp
                <div class="d-flex align-items-start">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="{{ $profile_sidebar_css }}">
                            <div class="symbol-label {{ $bgTextDecore }} border-radius-5">{{ strtoupper($strVal) }}</div>
                        </a>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                            class="text-dark fw-bolder text-hover-primary fs-6 {{ $profile_sidebar_css }}"
                            data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}">{{ ucwords(mb_substr(@$row->full_name, 0, 20)) }}</a>
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                            class="text-muted fw-bold d-block fs-7 text-hover-primary {{ $profile_sidebar_css }}"
                            data-toggle="tooltip" title="{{ ucwords(@$row->current_designation) }}">{{ ucwords(mb_substr(@$row->current_designation, 0, 20)) }}</a>
                        <div class="d-flex align-items-center gap-3 mt-1">
                            <span class="text-muted fw-bold"><i class="fa fa-eye"></i> {{ $viewCount }}</span>
                            <span class="text-muted fw-bold"><i class="fa fa-download"></i> {{ $downloadCount }}</span>
                            {!! $award !!}
                        </div>
                        <div class="mt-1">{!! $skilltag !!}</div>
                    </div>
                </div>
            </td>

            {{-- Experience --}}
            <td>
                @php
                    $experience = trim((string) @$row->total_exp);
                    if (strpos($experience, '.') !== false) {
                        $parts = explode('.', $experience);
                        $formattedExperience = (isset($parts[1]) && $parts[1] === '00') ? $parts[0] : $experience;
                    } else {
                        $formattedExperience = $experience;
                    }
                    $rel_experience = trim((string) @$row->rel_exp);
                    if (strpos($rel_experience, '.') !== false) {
                        $parts = explode('.', $rel_experience);
                        $rel_formattedExperience = (isset($parts[1]) && $parts[1] === '00') ? $parts[0] : $rel_experience;
                    } else {
                        $rel_formattedExperience = $rel_experience;
                    }
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ $formattedExperience }} Yrs</a>
                    <span class="text-muted fw-bold d-block fs-7">{{ $rel_formattedExperience }} Yrs Rel.</span>
                </div>
            </td>

            {{-- Availability --}}
            <td>
                @php
                    $npArr = ['Notice period', 'Available from', 'Immediate'];
                    $npDate = $row->np_id;
                    $titleNotic = $npArr[$row->serving_notice_period_mode];
                    if ($row->serving_notice_period_mode == 0) {
                        $npDate = $row->np_id == 1 ? $row->np_id . ' Day' : $row->np_id . ' Days';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $npDate = date('d-M-y', strtotime($row->np_id));
                        if (date('Y-m-d', strtotime($npDate)) < date('Y-m-d')) { $titleNotic = 'Immediate'; $npDate = ''; }
                    } elseif ($row->serving_notice_period_mode == 2) {
                        $npDate = date('d-M-y', strtotime($row->np_id));
                        if (date('Y-m-d', strtotime($row->np_id)) < date('Y-m-d')) { $npDate = ''; }
                    } else {
                        $npDate = '';
                    }
                    $availClass = $titleNotic == 'Immediate' ? 'text-success' : 'text-warning';
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="fw-bolder fs-6 {{ $availClass }}">{{ $titleNotic }}</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ $row->np_id }}">{{ $npDate }}</span>
                </div>
            </td>

            {{-- Location: City & Country (1) · Work preference (2) · Education (3) --}}
            <td>
                @php
                    $row_city_name = @$row->city_name;
                    $row_country_name = @$row->country_name;
                    $cityCountry = trim(ucwords($row_city_name) . ($row_country_name ? ', ' . strtoupper(mb_substr($row_country_name, 0, 3)) : ''), ', ');
                    if ($cityCountry === '') { $cityCountry = '—'; }

                    $wm = strtolower((string) @$row->wm);
                    $wmMap = ['remote' => 'Remote', 'hybrid' => 'Hybrid', 'office' => 'Onsite'];
                    $wmClassMap = ['remote' => 'text-success', 'hybrid' => 'text-primary', 'office' => 'text-warning'];
                    $wmLabel = $wmMap[$wm] ?? '—';
                    $wmClass = $wmClassMap[$wm] ?? 'text-muted';
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"
                        data-toggle="tooltip" title="{{ ucwords($row_city_name) }} {{ strtoupper($row_country_name) }}">{{ mb_substr($cityCountry, 0, 16) }}</a>
                    <span class="fw-bold d-block fs-7 {{ $wmClass }}">{{ $wmLabel }}</span>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip"
                        title="{{ @$row->qualification_name }}" style="text-transform:uppercase;">{{ mb_substr(@$row->qualification_name, 0, 12) }}</span>
                </div>
            </td>

            {{-- Expected Salary --}}
            <td>
                @php
                    $currencyBenchshowMode = GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);
                    $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly, $currencyBenchshowMode->currency_code, $currencyBenchshowMode->show_mode);
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $currencyBenchshowMode->currency_code, $currencyBenchshowMode->show_mode);
                    $pay_rate_currency = $currencySymbole[$payFormatViewSalary->currency];
                    $pay_rate_mode = $salaryMode[$payFormatViewSalary->mode];
                    $bill_rate_currency = $currencySymbole[$billFormatViewSalary->currency];
                    $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode];
                    $outsourcing_type = $row->outsourcing_type == 2 ? 'Contract' : 'Full-time';
                    $serviceType = @$row->comission_type == 2 ? 'Paid' : (@$row->comission_type == 1 ? 'Free' : '—');
                    $serviceClass = @$row->comission_type == 2 ? 'text-success' : 'text-muted';
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{!! $bill_rate_currency !!}{{ $billFormatViewSalary->amount }} /{{ mb_substr($bill_rate_mode, 0, 1) }}</a>
                    <span class="text-muted fw-bold d-block fs-7"><i class="fa fa-briefcase" style="font-size:9px !important;"></i> {{ $outsourcing_type }}</span>
                    <span class="fw-bold d-block fs-7 {{ $serviceClass }}"><i class="fa fa-circle-check" style="font-size:9px !important;"></i> {{ $serviceType }}</span>
                </div>
            </td>

            {{-- Bench Status --}}
            <td>
                @php
                    $result = GeneralMethod::bench_status_subline($row->status);
                    $subS = ($result->name) ?? '';
                    // 3rd row: how long the talent has been on bench (only while status = Bench).
                    $benchDays = '';
                    if ($row->status == 2 && !empty($row->created_at)) {
                        $benchDays = Carbon::parse($row->created_at)->diffInDays(Carbon::now()) . ' Days on bench';
                    }
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                        class="fw-bold fs-6 stage_sub_stage {{ $row->css_style }}" data-toggle="modal" data-target="#stage_sub_stage">{{ $row->master_status_name }}</a>
                    <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $subS }}">{{ $subS }}</span>
                    @if ($benchDays !== '')
                        <span class="fw-bolder d-block fs-7 text-warning">{{ $benchDays }}</span>
                    @endif
                </div>
            </td>

            {{-- Manager (names only — no avatar image) --}}
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
                    <span class="text-muted fw-bold d-block fs-7">Relationship Mgr</span>
                    <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="manager-change hmb-listing mt-1"><i class="fa fa-pen"></i> Change</a>
                </div>
            </td>

            {{-- Hiring Pipeline Activity (mini stage grid) --}}
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
                        <div class="pipe-foot">Total: {{ $countForZero }} &nbsp;·&nbsp; Interview: {{ $row->InReview }}</div>
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
