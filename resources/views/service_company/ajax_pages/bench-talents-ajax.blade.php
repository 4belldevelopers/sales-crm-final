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
    // dd($row);
    $jobs = @$row->jobs;
    $array = collect($jobs)
    ->unique('job_id')
    ->values()
    ->all();
    // dd(@$row->full_name,$array);

    // $jobIds = array_values(array_unique(array_column($jobs, 'job_id')));
    $masterStatusIds = array_column($array, 'master_status_id');
    $bench_explore_listing_counter = GeneralMethod::bench_explore_listing_counter($row->id);
    $applicationCounter = $bench_explore_listing_counter->applicationCounter;

    // dd($jobs,@$row->full_name);

    // Count how many times each value appears
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
        <tr style="height: 84px; position: relative;">
            {{-- Col 1: Checkbox --}}
            <td class="align-middle px-3">
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input select-bench-candidate" type="checkbox" value="{{ $row->id }}" />
                </div>
            </td>

            {{-- Col 2: TALENT --}}
            <td class="align-middle" style="position: relative; padding-bottom: 24px !important;">
                @php
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->full_name);
                    $str = $strVal = strtolower($str[0] ?? 'c');
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

                    /* Skills */
                    $skillArr = array_values(array_filter(array_map('trim', explode(',', (string) ($row->skills ?? '')))));
                    $skilltag = '';
                    foreach ($skillArr as $sval) {
                        if ($sval === '') { continue; }
                        $skilltag .= '<span class="badge badge-light my-1 skill" style="font-weight:400; font-size:10px;">' . e(ucfirst($sval)) . '</span> ';
                    }

                    /* Match % badge */
                    $matchBadge = (!empty($row->skill_match_percent))
                        ? '<span class="badge badge-light-info my-0" style="font-size:10px;">' . (int)$row->skill_match_percent . '%</span> '
                        : '';

                    /* Ads switch tag */
                    $adsBadge = (isset($row->ads_switch_order) && $row->ads_switch_order == 0)
                        ? '<span class="badge badge-light-danger my-0 fw-bold" style="font-size:10px; color:#EF4444;">Ads</span> '
                        : '';

                    /* View / Download counts */
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

                <div class="d-flex align-items-center me-2">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-3 border-radius-5 flex-shrink-0">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="d-flex w-100 h-100">
                            <div class="symbol-label {{ $bgTextDecore }} border-radius-5 d-flex align-items-center justify-content-center fw-bold">{{ strtoupper($strVal) }}</div>
                        </a>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                            class="text-dark fw-bolder text-hover-primary fs-6"
                            data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}">{{ ucwords(mb_substr(@$row->full_name, 0, 22)) }}</a>
                        <span class="text-muted fw-normal fs-7"
                            data-toggle="tooltip" title="{{ ucwords(@$row->current_designation) }}">{{ ucwords(mb_substr(@$row->current_designation ?: 'Developer', 0, 25)) }}</span>
                    </div>
                </div>

                {{-- Skills & Tags footer strip --}}
                @if($adsBadge || $matchBadge || $skilltag)
                <div class="mt-1" style="white-space: nowrap; max-width: 95%; overflow: hidden;">
                    {!! $adsBadge !!}{!! $matchBadge !!}{!! $skilltag !!}
                </div>
                @endif
            </td>

            {{-- Col 3: EXPERIENCE --}}
            <td class="align-middle">
                @php
                    $experience = trim((string) @$row->total_exp);
                    if (strpos($experience, '.') !== false) {
                        $parts = explode('.', $experience);
                        $formattedExperience = (isset($parts[1]) && $parts[1] === '00') ? $parts[0] : $experience;
                    } else {
                        $formattedExperience = $experience;
                    }

                    $rawWm = strtolower(trim((string)(
                        $row->work_mode ?? $row->work_type ?? $row->wm ?? $row->prefer_work_mode ?? $row->location_type ?? $row->work_location ?? ''
                    )));

                    if ($rawWm === 'office' || $rawWm === 'onsite' || $rawWm === 'no' || strpos($rawWm, 'onsite') !== false || strpos($rawWm, 'office') !== false) {
                        $modeBadge = '<span style="color: #D97706; font-weight: 600; font-size: 11px;">Onsite</span>';
                    } elseif ($rawWm === 'hybrid' || $rawWm === 'other' || strpos($rawWm, 'hybrid') !== false) {
                        $modeBadge = '<span style="color: #2563EB; font-weight: 600; font-size: 11px;">Hybrid</span>';
                    } elseif ($rawWm === 'remote' || $rawWm === 'yes' || strpos($rawWm, 'remote') !== false) {
                        $modeBadge = '<span style="color: #059669; font-weight: 600; font-size: 11px;">Remote</span>';
                    } else {
                        // If not set, display dash (-)
                        $modeBadge = '<span class="text-muted fs-7">—</span>';
                    }
                @endphp
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">{{ $formattedExperience ?: '0' }} Yrs</span>
                    {!! $modeBadge !!}
                </div>
            </td>

            {{-- Col 4: AVAILABILITY --}}
            <td class="align-middle">
                @php
                    $npMode = $row->serving_notice_period_mode ?? 0;
                    $npDate = $row->np_id;
                    if ($npMode == 0) {
                        $npDate = ($row->np_id == 1 || $row->np_id == '1') ? $row->np_id . ' Day' : ($row->np_id ? $row->np_id . ' Days' : 'Immediate');
                    } elseif ($npMode == 1 || $npMode == 2) {
                        $originalDate = $row->np_id;
                        $npDate = date('d-M-y', strtotime($originalDate));
                        if (date('Y-m-d', strtotime($originalDate)) < date('Y-m-d')) {
                            $npDate = "Immediate";
                        }
                    } else {
                        $npDate = 'Immediate';
                    }

                    $npVal = strtolower((string)$row->np_id);
                    $isImmediate = ($npMode == 2 || stripos($npVal, 'immediate') !== false || $npDate === 'Immediate' || $npVal === '0' || $npVal === '');

                    if ($isImmediate) {
                        $availHtml = '<span style="color: #10B981; font-weight: 700; font-size: 13px;">Immediate</span>';
                    } else {
                        $availHtml = '<span style="color: #D97706; font-weight: 700; font-size: 13px;">' . e($npDate) . '</span>';
                    }

                    $outsourcing_type = ($row->outsourcing_type == 2) ? 'Contract' : 'Full-time';
                @endphp
                <div class="d-flex flex-column">
                    {!! $availHtml !!}
                    <span class="text-muted fw-bold d-block fs-7">{{ $outsourcing_type }}</span>
                </div>
            </td>

            {{-- Col 5: LOCATION --}}
            <td class="align-middle">
                @php
                    $row_city_name = @$row->city_name ?: '';
                    $row_country_name = @$row->country_name ?: '';
                    $locStr = trim(ucwords($row_city_name) . ($row_country_name ? ', ' . ucwords($row_country_name) : ''), ', ');
                    if (empty($locStr)) { $locStr = '—'; }

                    $qualStr = !empty($row->qualification_name) ? strtoupper($row->qualification_name) : '—';
                @endphp
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{ $locStr }}">{{ mb_substr($locStr, 0, 18) }}</span>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ $qualStr }}">{{ mb_substr($qualStr, 0, 15) }}</span>
                </div>
            </td>

            {{-- Col 6: EXPECTED SALARY --}}
            <td class="align-middle">
                @php
                    $company_id = auth()->user()->company_id;
                    $currencyBenchshowMode = GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);
                    $currencyArr = array(0 => "INR", 1 => "EUR", 2 => "JPY", 3 => "GBP", 4 => "USD", 5 => "AED");
                    $currency = $currencyArr[$sal_currency] ?? $currencyBenchshowMode->currency_code;
                    $mode = $sal_mode ?? $currencyBenchshowMode->show_mode;

                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $currency, $mode);
                    $bill_rate_currency = $currencySymbole[$billFormatViewSalary->currency] ?? '₹';
                    $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode] ?? 'H';

                    $salAmountFormatted = $billFormatViewSalary->amount ? $bill_rate_currency . $billFormatViewSalary->amount : '—';
                    $salModeFormatted = !empty($bill_rate_mode) ? '/' . strtoupper(substr($bill_rate_mode, 0, 1)) : '';

                    $isPaid = isset($row->ads_switch_order) && $row->ads_switch_order == 0;
                @endphp
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">{!! $salAmountFormatted !!}{{ $salModeFormatted }}</span>
                    @if($isPaid)
                        <span style="color: #10B981; font-weight: 600; font-size: 11px; display: inline-flex; align-items: center; gap: 3px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Paid
                        </span>
                    @else
                        <span style="color: #6B7280; font-weight: 500; font-size: 11px; display: inline-flex; align-items: center; gap: 3px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg> Free
                        </span>
                    @endif
                </div>
            </td>

            {{-- Col 7: BENCH STATUS --}}
            <td class="align-middle">
                @php
                    $statusName = $row->master_status_name ?: 'Bench';
                    $statusColor = (strtolower($statusName) == 'pool') ? '#8B5CF6' : '#009EF7';
                    $result = GeneralMethod::bench_status_subline($row->status);
                    $subS = ($result->name) ?? 'Open For Work';

                    $createdDate = @$row->created_at ? Carbon::parse($row->created_at) : null;
                    $daysDiff = $createdDate ? (int) floor($createdDate->diffInDays(now())) : 0;
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="fw-bolder fs-6 stage_sub_stage" data-toggle="modal" data-target="#stage_sub_stage" style="color: {{ $statusColor }}; text-decoration: none;">
                        {{ $statusName }}
                    </a>
                    <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $subS }}">{{ $subS }}</span>
                    @if($daysDiff > 30)
                        <span style="color: #EF4444; font-weight: 600; font-size: 11px;">{{ $daysDiff }} Days</span>
                    @endif
                </div>
            </td>

            {{-- Col 8: RELATIONSHIP MANAGER --}}
            <td class="align-middle">
                @php
                    $hmCount = DB::table('bench_assign')
                        ->join('users', 'users.id', '=', 'bench_assign.user_id')
                        ->where('bench_assign.bench_applications_id', $row->id)
                        ->where('bench_assign.company_id', $company_id)
                        ->where('users.company_id', $company_id)
                        ->count();
                    $hmData = DB::table('bench_assign')
                        ->join('users', 'users.id', '=', 'bench_assign.user_id')
                        ->select('users.name', 'users.image')
                        ->where('bench_assign.bench_applications_id', $row->id)
                        ->where('bench_assign.company_id', $company_id)
                        ->where('users.company_id', $company_id)
                        ->orderBy('users.name', 'ASC')->take(1)->first();
                    $managerFullName = @$hmData->name ? ucwords($hmData->name) : 'Unassigned';
                    $managerFirstName = ucfirst(explode(' ', trim($managerFullName))[0]);
                    $extra = $hmCount > 1 ? ' +' . ($hmCount - 1) : '';
                @endphp
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6 text-truncate" style="max-width:140px;" data-toggle="tooltip" title="{{ $managerFullName }}{{ $extra }}">{{ $managerFirstName }}{{ $extra }}</span>
                </div>
            </td>

            {{-- Col 9: ACTION --}}
            <td class="align-middle text-end">
                @php
                    $benchRequestCheck = GeneralMethod::benchRequestCheck($row->id, $currentJobId);
                    $btnStyle = 'white-space:nowrap;font-size:12px;font-weight:600;padding:6px 14px;border-radius:6px;';
                    if ($benchRequestCheck == null) {
                        echo '<a href="javascript:void(0);" data-jobId="' . $currentJobId . '" data-conId="' . $row->id . '" class="btn btn-sm btn-primary request-send" style="' . $btnStyle . '">Send Bench Request</a>';
                    } else if ($benchRequestCheck->ac_status == 0) {
                        echo '<a href="javascript:void(0);" class="btn btn-sm btn-success" style="' . $btnStyle . 'background:#10B981;border-color:#10B981;color:#fff;">Request sent</a>';
                    } else if ($benchRequestCheck->ac_status == 1) {
                        echo '<span class="badge badge-light-primary fs-7 fw-bold" style="padding:6px 12px;">Added</span>';
                    } else if ($benchRequestCheck->ac_status == 2) {
                        echo '<span class="badge badge-light-danger fs-7 fw-bold" style="padding:6px 12px;">Expired</span>';
                    } else if ($benchRequestCheck->ac_status == 3) {
                        echo '<span class="badge badge-light-danger fs-7 fw-bold" style="padding:6px 12px;">Removed</span>';
                    }
                @endphp
            </td>
        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="10" style="text-align:center; color:black;">
            <div class="" style="display:flex; justify-content:center; align-items:center; width:100%;">
                <a href="{{ url('/service_company/bench-candidates/create') }}"  class="btn btn-sm btn-primary">Add Inhouse Talent</a>
            </div>
        </td>

    </tr>
@endif
