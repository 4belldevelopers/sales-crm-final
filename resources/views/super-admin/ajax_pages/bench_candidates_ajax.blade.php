@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
@endphp

@if (!empty($data) && count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $val_tem = (object) $rowArr;

            // Avatar letter styling
            $bgTextDecore = $strVal = '';
            $str = trim((string) @$val_tem->full_name);
            $firstChar = !empty($str) ? strtolower($str[0]) : 'c';
            $strVal = $firstChar;
            $ascii = ord($firstChar);

            if ($ascii >= 97 && $ascii <= 101) {
                $bgTextDecore = 'bg-light-danger text-danger';
            } elseif ($ascii >= 102 && $ascii <= 106) {
                $bgTextDecore = 'bg-light-warning text-warning';
            } elseif ($ascii >= 107 && $ascii <= 111) {
                $bgTextDecore = 'bg-light-primary text-primary';
            } elseif ($ascii >= 112 && $ascii <= 116) {
                $bgTextDecore = 'bg-light-success text-success';
            } elseif ($ascii >= 117 && $ascii <= 121) {
                $bgTextDecore = 'bg-dark text-secondary';
            } else {
                $bgTextDecore = 'bg-light-secondary text-dark';
            }

            // Skills & Stage Date badges
            $skillArr = array_values(array_filter(array_map('trim', explode(',', (string) (@$val_tem->skills ?? '')))));
            $stageMovedAt = $val_tem->updated_at ?? $val_tem->created_at ?? null;
            $stageDateLabel = $stageMovedAt ? Carbon::parse($stageMovedAt)->format('d M y') : '';
            $stageDateChip = $stageDateLabel
                ? '<span class="job-date-tag"><svg fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8"/></svg>' . e($stageDateLabel) . '</span>'
                : '';

            $skilltag = '';
            foreach ($skillArr as $sval) {
                if ($sval === '') { continue; }
                $skilltag .= '<span class="skill-tag">' . e(ucfirst($sval)) . '</span> ';
            }

            // Experience formatting
            $totalExp = trim((string) (@$val_tem->total_exp ?? '0'));
            $relExp = trim((string) (@$val_tem->rel_exp ?? ''));

            // Salary & Bill Rate
            $pay_rate_currency  = $currencySymbole[@$val_tem->pay_rate_currency] ?? '$';
            $bill_rate_currency = $currencySymbole[@$val_tem->bill_rate_currency] ?? '$';
            $pay_rate_mode      = $salaryMode[@$val_tem->pay_rate_mode] ?? 'Yr';
            $bill_rate_mode     = $salaryMode[@$val_tem->bill_rate_mode] ?? 'Yr';
            $pay_rate_amount    = GeneralMethod::thousandsCurrencyFormat(@$val_tem->pay_rate_amount ?? 0);
            $bill_rate_amount   = GeneralMethod::thousandsCurrencyFormat(@$val_tem->bill_rate_amount ?? 0);

            // Notice Period & Availability
            $npArr = ['Notice period', 'Available from', 'Immediate'];
            $servingMode = (int) (@$val_tem->serving_notice_period_mode ?? 2);
            $npId = (string) (@$val_tem->np_id ?? '');

            if ($servingMode === 0) {
                $days = (int) $npId;
                $availLine = ($days === 1) ? '1 Day Notice' : ($days > 0 ? $days . ' Days Notice' : 'Immediate');
                $availClass = 'text-warning';
                $npDate = '';
            } elseif ($servingMode === 1) {
                if (!empty($npId) && strtotime($npId) !== false) {
                    $availLine = 'Available from';
                    $availClass = 'text-primary';
                    $npDate = date('d M, Y', strtotime($npId));
                } else {
                    $availLine = 'Immediate';
                    $availClass = 'text-success';
                    $npDate = '';
                }
            } else {
                $availLine = 'Immediate';
                $availClass = 'text-success';
                $npDate = '';
            }

            // Location
            $row_country_name = !empty($val_tem->country_name) ? ucwords($val_tem->country_name) : '';
            $row_state_name   = !empty($val_tem->state_name) ? ucwords($val_tem->state_name) : '';
            $row_city_name    = !empty($val_tem->city_name) ? ucwords($val_tem->city_name) : (!empty($val_tem->city) ? ucwords($val_tem->city) : '');
            $locationParts = array_filter([$row_city_name, $row_state_name, $row_country_name]);
            $cityLocation = !empty($locationParts) ? implode(', ', $locationParts) : '—';

            // Bench & SuperAdmin Status
            $superStatus = (int) (@$val_tem->superadminstatus ?? 0);
            $masterStatus = (string) (@$val_tem->master_status_name ?? 'Bench');

            if ($superStatus === 0) {
                $statusBadgeClass = 'badge-light-warning text-warning';
                $statusBadgeText  = 'Pending Approval';
            } elseif ($superStatus === 1) {
                $statusBadgeClass = 'badge-light-success text-success';
                $statusBadgeText  = 'Approved (Live)';
            } elseif ($superStatus === 2) {
                $statusBadgeClass = 'badge-light-danger text-danger';
                $statusBadgeText  = 'Rejected';
            } else {
                $statusBadgeClass = 'badge-light-primary text-primary';
                $statusBadgeText  = 'Placed';
            }

            // Company & Upload Date
            $companyName = !empty($val_tem->company_name) ? ucwords($val_tem->company_name) : '—';
            $createdAt = !empty($val_tem->created_at) ? date('d M, Y', strtotime($val_tem->created_at)) : '—';
        @endphp
        <tr data-bench-row-id="{{ $val_tem->id }}" style="height: 84px; position: relative;">
            {{-- Col 1: Checkbox --}}
            <td class="col-select pe-0">
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox" data-jobid="{{ $val_tem->id }}" data-cname="{{ e($val_tem->full_name ?? '') }}" value="{{ $val_tem->id }}">
                </div>
            </td>

            {{-- Col 2: Talent Name & Role + Job Tracker Meta Strip --}}
            <td class="col-talent-name position-relative" style="padding-bottom: 24px !important;">
                <div class="d-flex align-items-start">
                    <div class="symbol symbol-circle symbol-40px me-2 flex-shrink-0" style="overflow:hidden; border-radius:50%;">
                        <a href="javascript:void(0);" data-id="{{ $val_tem->id }}" class="bench_show_application_detail d-flex w-100 h-100" title="Click to view details">
                            <div class="symbol-label {{ $bgTextDecore }} w-100 h-100 d-flex align-items-center justify-content-center fw-bolder fs-5" style="border-radius:50%;">{{ strtoupper($strVal) }}</div>
                        </a>
                    </div>
                    <div class="d-flex flex-column" style="min-width:0;">
                        <a href="javascript:void(0);" data-id="{{ $val_tem->id }}" class="text-dark fw-bolder text-hover-primary fs-6 bench_show_application_detail talent-name-main" data-toggle="tooltip" title="{{ ucwords($val_tem->full_name) }}">
                            {{ ucwords($val_tem->full_name) }}
                        </a>
                        <span class="text-muted fw-bold d-block fs-7 talent-name-role" data-toggle="tooltip" title="{{ ucwords($val_tem->current_designation) }}">
                            {{ !empty($val_tem->current_designation) ? ucwords($val_tem->current_designation) : 'Talent' }}
                        </span>
                    </div>
                </div>

                @if($stageDateChip || $skilltag)
                    <div class="job-meta-strip">
                        <div class="job-meta-line">{!! $stageDateChip !!}{!! $skilltag !!}</div>
                    </div>
                @endif
            </td>

            {{-- Col 3: Experience --}}
            <td class="col-experience">
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">{{ $totalExp }}+ Yrs</span>
                    <span class="text-muted fw-bold d-block fs-7">{{ !empty($relExp) ? $relExp . '+ Rel' : '—' }}</span>
                </div>
            </td>

            {{-- Col 4: Salary (Bill & Pay Rate) --}}
            <td class="col-salary">
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">{!! $bill_rate_currency !!}{{ $bill_rate_amount }} /{{ mb_substr($bill_rate_mode, 0, 1) }}</span>
                    <span class="text-muted fw-bold d-block fs-7">{!! $pay_rate_currency !!}{{ $pay_rate_amount }} /{{ mb_substr($pay_rate_mode, 0, 1) }}</span>
                </div>
            </td>

            {{-- Col 5: Availability --}}
            <td class="col-availability">
                <div class="d-flex flex-column">
                    <span class="fw-bolder fs-6 {{ $availClass }}">{{ $availLine }}</span>
                    @if(!empty($npDate))
                        <span class="text-muted fw-bold d-block fs-7">{{ $npDate }}</span>
                    @else
                        <span class="text-muted fw-bold d-block fs-7">{{ $val_tem->serving_notice_period_mode == 2 ? 'Available Now' : 'Serving NP' }}</span>
                    @endif
                </div>
            </td>

            {{-- Col 6: Location & Qualification --}}
            <td class="col-location">
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder text-hover-primary fs-6 wrap-2" data-toggle="tooltip" title="{{ $cityLocation }}">
                        {{ $cityLocation }}
                    </span>
                    <span class="text-muted fw-bold d-block fs-7 wrap-2" data-toggle="tooltip" title="{{ @$val_tem->qualification_name }}">
                        {{ !empty($val_tem->qualification_name) ? $val_tem->qualification_name : '—' }}
                    </span>
                </div>
            </td>

            {{-- Col 7: Bench Status --}}
            <td class="col-status">
                <div class="d-flex flex-column align-items-start">
                    <span class="fw-bold fs-6 {{ @$val_tem->css_style ?: 'text-dark' }}">{{ $masterStatus }}</span>
                    <span class="badge {{ $statusBadgeClass }} mt-1 px-2 py-1 fs-8 fw-bold">{{ $statusBadgeText }}</span>
                </div>
            </td>

            {{-- Col 8: Company & Upload Date --}}
            <td class="col-company">
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6 wrap-2" data-toggle="tooltip" title="{{ $companyName }}">
                        {{ $companyName }}
                    </span>
                    <span class="text-muted fw-bold d-block fs-7">{{ $createdAt }}</span>
                </div>
            </td>

            {{-- Col 9: Actions --}}
            <td class="col-action text-end">
                <div class="d-flex align-items-center justify-content-end gap-1">
                    @if($superStatus === 0 || $superStatus === 2)
                        <button type="button" class="btn btn-icon btn-light-success btn-sm row-quick-approve" data-id="{{ $val_tem->id }}" data-cname="{{ e($val_tem->full_name ?? '') }}" title="Approve Candidate">
                            <i class="fa fa-check fs-7"></i>
                        </button>
                    @endif
                    @if($superStatus === 0 || $superStatus === 1)
                        <button type="button" class="btn btn-icon btn-light-danger btn-sm row-quick-reject" data-id="{{ $val_tem->id }}" data-jobid="{{ $val_tem->id }}" data-cname="{{ e($val_tem->full_name ?? '') }}" title="Reject Candidate">
                            <i class="fa fa-times fs-7"></i>
                        </button>
                    @endif
                    <button type="button" class="btn btn-icon btn-light-primary btn-sm bench_show_application_detail" data-id="{{ $val_tem->id }}" title="View Full Profile Details">
                        <i class="fa fa-eye fs-7"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9" class="text-center text-muted py-10">
            <div class="d-flex flex-column align-items-center justify-content-center">
                <i class="fa fa-folder-open text-gray-400 mb-2" style="font-size: 28px !important;"></i>
                <span class="fs-6 fw-bold text-gray-600">No bench candidates found</span>
                <span class="fs-7 text-muted">Try adjusting your filters or search keywords.</span>
            </div>
        </td>
    </tr>
@endif