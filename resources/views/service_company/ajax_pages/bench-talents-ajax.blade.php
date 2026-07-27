@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $jobs = @$row->jobs;
    $array = collect($jobs)->unique('job_id')->values()->all();
    $masterStatusIds = array_column($array, 'master_status_id');
    $bench_explore_listing_counter = GeneralMethod::bench_explore_listing_counter($row->id);
    $applicationCounter = $bench_explore_listing_counter->applicationCounter;
    $counts = array_count_values($masterStatusIds);
    $countForZero = $applicationCounter->allCandidate ?? 0;
    $row->Sourcing = $applicationCounter->Sourcing ?? 0;
    $row->Screening = $applicationCounter->Screening ?? 0;
    $row->Evaluation = $applicationCounter->Evaluation ?? 0;
    $row->Negotiation = $applicationCounter->Negotiation ?? 0;
    $row->Offered = $applicationCounter->Offered ?? 0;
    $row->Joined = $applicationCounter->Joined ?? 0;
    $row->Rejected = $applicationCounter->Rejected ?? 0;
    $row->InReview = $applicationCounter->Interview ?? 0;
    @endphp

        <tr style="height: 82px; position: relative;"
            data-match-pct="{{ (int)($row->skill_match_percent ?? 0) }}"
            data-np-mode="{{ (int)($row->serving_notice_period_mode ?? 0) }}">

            {{-- ① TALENT --}}
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

                    $skillArr = array_values(array_filter(array_map('trim', explode(',', (string) ($row->skills ?? '')))));
                    $matchedArr = array_map('strtolower', array_filter(array_map('trim', explode(',', (string) ($row->matched_skills ?? '')))));
                    $skilltag = '';
                    $matchedTags = '';
                    $normalTags  = '';
                    foreach ($skillArr as $sval) {
                        if ($sval === '') { continue; }
                        if (in_array(strtolower($sval), $matchedArr)) {
                            $matchedTags .= '<span class="badge my-0 skill" style="font-weight:500;font-size:9px;padding:2px 5px;background:#DCFCE7;color:#15803D;">' . e(ucfirst($sval)) . '</span> ';
                        } else {
                            $normalTags  .= '<span class="badge badge-light my-0 skill" style="font-weight:300;font-size:9px;padding:2px 5px;">' . e(ucfirst($sval)) . '</span> ';
                        }
                    }
                    $skilltag = $matchedTags . $normalTags;

                    $matchBadge = (!empty($row->skill_match_percent))
                        ? '<span class="badge my-0" style="background:#EFF6FF;color:#1D4ED8;font-weight:600;font-size:9px;padding:2px 5px;">Match ' . (int)$row->skill_match_percent . '%</span> '
                        : '';

                @endphp

                <div class="d-flex align-items-start">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 flex-shrink-0">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="d-flex w-100 h-100">
                            <div class="symbol-label {{ $bgTextDecore }} border-radius-5 d-flex align-items-center justify-content-center">{{ strtoupper($strVal) }}</div>
                        </a>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                            class="text-dark fw-bolder text-hover-primary fs-6"
                            data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}">{{ ucwords(mb_substr(@$row->full_name, 0, 20)) }}</a>
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                            class="text-muted fw-bold d-block fs-7 text-hover-primary"
                            data-toggle="tooltip" title="{{ ucwords(@$row->current_designation) }}">{{ ucwords(mb_substr(@$row->current_designation, 0, 20)) }}</a>
                    </div>
                </div>

                @if($matchBadge || $skilltag)
                <div style="position: absolute;bottom: 8px;left: 0;white-space: nowrap;width: 80%;overflow: hidden;">
                    {!! $matchBadge !!}{!! $skilltag !!}
                </div>
                @endif

            </td>

            {{-- ② EXPERIENCE --}}
            <td>
                @php
                    $total_exp = $row->total_exp;
                    if($total_exp == '0.'){ $total_exp = str_replace('.','',$total_exp); }
                    $rel_exp = $row->rel_exp;
                    if($total_exp == '0.'){ $rel_exp = str_replace('.','',$rel_exp); }
                    if($total_exp==0 && $rel_exp==0){ $rel_exp = ''; }

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

                    $wmRaw = strtolower(trim($row->wm ?? ''));
                    $wmLabel = match($wmRaw) {
                        'remote'  => 'Remote',
                        'hybrid'  => 'Hybrid',
                        'office'  => 'Work From Office',
                        default   => '—',
                    };
                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $formattedExperience . ' Yrs</a>
                        <span class="text-muted fw-bold d-block fs-7">' . $wmLabel . '</span>
                    </div>';
                @endphp
            </td>

            {{-- ③ AVAILABILITY --}}
            <td>
                @php
                    $npArr = ['Notice period', 'Available from', 'Immediate'];
                    $npDate = $row->np_id;
                    if ($row->serving_notice_period_mode == 0) {
                        $npDate = $row->np_id == 1 ? $row->np_id . ' Day' : $row->np_id . ' Days';
                    } elseif ($row->serving_notice_period_mode == 1) {
                        $originalDate = $row->np_id;
                        $npDate = date('d-M-y', strtotime($originalDate));
                    } elseif ($row->serving_notice_period_mode == 2) {
                        $originalDate = $row->np_id;
                        $npDate = date('d-M-y', strtotime($originalDate));
                        $today = date('Y-m-d');
                        if(date('Y-m-d', strtotime($row->np_id)) < $today){ $npDate = ""; }
                    } else {
                        $npDate = '';
                    }

                    $outsourcing_type = ($row->outsourcing_type == 2) ? 'Contract' : 'Full-time';

                    $npLabel = $npArr[$row->serving_notice_period_mode] ?? '';
                    $isImmediate = (strtolower($npLabel) === 'immediate');
                    $availColor = $isImmediate ? 'color:#17C653;' : 'color:#F6A723;';
                    $availText  = $isImmediate ? 'Immediate' : ($npDate ?: $npLabel);

                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="fw-bolder fs-6" style="' . $availColor . '">' . $availText . '</a>
                        <span class="text-muted fw-bold text-muted d-block fs-7">' . $outsourcing_type . '</span>
                    </div>';
                @endphp
            </td>

            {{-- ④ LOCATION (city + country + qualification below) --}}
            <td>
                @php
                    $row_city_name    = @$row->city_name;
                    $row_state_name   = @$row->state_name;
                    $row_country_name = @$row->country_name;

                    $locationLine1 = ucwords(mb_substr($row_city_name, 0, 12));
                    if ($row_country_name) {
                        $locationLine1 .= ', ' . ucwords(mb_substr($row_country_name, 0, 8));
                    }

                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"
                           data-toggle="tooltip" title="' . ucwords($row_city_name) . ', ' . ucwords($row_country_name) . '">'
                            . $locationLine1 . '</a>
                        <span class="text-muted fw-bold d-block fs-7">' . strtoupper(@$row->qualification_name ?? '') . '</span>
                    </div>';
                @endphp
            </td>

            {{-- ⑤ EXPECTED SALARY (bill rate + Paid badge) --}}
            <td>
                @php
                    $company_id = auth()->user()->company_id;
                    $currencyBenchshowMode = GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);
                    $currencyArr = [0=>"INR",1=>"EUR",2=>"JPY",3=>"GBP",4=>"USD",5=>"AED"];
                    $salCur  = $currencyArr[$sal_currency] ?? $currencyBenchshowMode->currency_code;
                    $salMode = $sal_mode ?? $currencyBenchshowMode->show_mode;

                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $salCur, $salMode);
                    $bill_rate_currency   = $currencySymbole[$billFormatViewSalary->currency] ?? '₹';
                    $bill_rate_mode       = $salaryMode[$billFormatViewSalary->mode] ?? '';
                    $billAmount           = (float) ($billFormatViewSalary->amount ?? 0);
                    $hasBill              = $billAmount > 0;

                    /* K format only */
                    $billFormatted = $hasBill ? round($billAmount / 1000, 1) . 'K' : '—';
                    $modeSuffix    = mb_substr($bill_rate_mode, 0, 1);

                    if ($hasBill) {
                        echo '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'
                                . $bill_rate_currency . $billFormatted . '/' . $modeSuffix .
                            '</a>
                            <span class="text-muted fw-bold d-block fs-7">Paid Service</span>
                        </div>';
                    } else {
                        echo '<span class="text-muted fs-7">—</span>';
                    }
                @endphp
            </td>

            {{-- ⑥ BENCH STATUS (status + sub-status + days counter) --}}
            <td>
                @php
                    $subS = '';
                    if ($row->master_status_name == 'Bench') {
                        $subS = 'Live';
                        if ($row->superadminstatus == 0) { $subS = 'Pending'; }
                    }
                    $result = GeneralMethod::bench_status_subline($row->status);
                    $subS = ($result->name) ?? '';

                    $daysOnBench = 0;
                    if (!empty($row->created_at)) {
                        try { $daysOnBench = (int) Carbon::parse($row->created_at)->diffInDays(now()); } catch(\Exception $e){}
                    }

                    echo '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" data-row-id="' . $row->id . '"
                           class="fw-bold fs-6 stage_sub_stage ' . $row->css_style . '"
                           data-toggle="modal" data-target="#stage_sub_stage"
                           style="color:#009ef7;text-decoration:none;">' . $row->master_status_name . '</a>
                        <span class="fw-bold text-muted d-block fs-7">' . $subS . '</span>
                    </div>';
                @endphp
            </td>

            {{-- ⑦ RELATIONSHIP MANAGER --}}
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
                    $managerFullName  = @$hmData->name ? ucwords($hmData->name) : 'Unassigned';
                    $managerFirstName = ucfirst(explode(' ', trim($managerFullName))[0]);
                    $extra            = $hmCount > 1 ? ' +' . ($hmCount - 1) : '';
                @endphp
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6 text-truncate" style="max-width:110px;"
                          data-toggle="tooltip" title="{{ $managerFullName }}{{ $extra }}">{{ $managerFirstName }}{{ $extra }}</span>
                </div>
            </td>

            {{-- ⑧ ACTION --}}
            <td style="vertical-align:top;padding-top:10px !important;">
                @php
                    $benchRequestCheck = GeneralMethod::benchRequestCheck($row->id, $currentJobId);
                    $btnStyle = 'white-space:nowrap;font-size:11px;padding:5px 10px;';

                    try {
                        $viewCount = DB::table('bench_count_view_download')
                            ->where('type', 1)->where('status', 0)->where('candidate_id', $row->id)->count();
                        $downloadCount = DB::table('bench_count_view_download')
                            ->where('type', 2)->where('status', 0)->where('candidate_id', $row->id)->count();
                    } catch (\Exception $e) {
                        $viewCount = 0;
                        $downloadCount = 0;
                    }
                @endphp

                @if($benchRequestCheck == null)
                    <a href="javascript:void(0);" data-jobId="{{ $currentJobId }}" data-conId="{{ $row->id }}"
                       class="btn btn-sm btn-primary request-send" style="{{ $btnStyle }}">Send Bench Request</a>
                @elseif($benchRequestCheck->ac_status == 0)
                    <a href="javascript:void(0);" class="btn btn-sm btn-success" style="{{ $btnStyle }}">Request sent</a>
                @elseif($benchRequestCheck->ac_status == 1)
                    <a href="javascript:void(0);" class="text-dark" style="{{ $btnStyle }}">Added</a>
                @elseif($benchRequestCheck->ac_status == 2)
                    <a href="javascript:void(0);" data-jobId="{{ $currentJobId }}" data-conId="{{ $row->id }}"
                       class="btn btn-sm btn-primary request-send" style="{{ $btnStyle }}">Send Bench Request</a>
                @elseif($benchRequestCheck->ac_status == 3)
                    <a href="javascript:void(0);" class="btn btn-sm btn-danger" style="{{ $btnStyle }}">Removed</a>
                @endif

            </td>

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">
        <td colspan="8" style="text-align:center; color:black;">
            <div class="" style="display:flex; justify-content:center; align-items:center; width:100%;">
                <a href="{{ url('/service_company/bench-candidates/create') }}" class="btn btn-sm btn-primary">Add Inhouse Talent</a>
            </div>
        </td>
    </tr>
@endif
