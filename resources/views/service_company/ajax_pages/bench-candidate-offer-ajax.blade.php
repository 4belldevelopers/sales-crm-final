@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $salaryModeOffer = GeneralMethod::salaryModeOffer();
    $currency = GeneralMethod::currency();
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $row->placedID = $row->wiseId;
            $row->id = $row->candidate_id;

            // ---- Status column (bench-level status + subline, opens the Inhouse Talent Status modal) ----
            if (isset($rowArr['_prehydrated_master_status_name'])) {
                $row->master_status_name = $rowArr['_prehydrated_master_status_name'];
            } else {
                $benchMasterStatus = DB::table('bench_master_status')->where('id', $row->status)->first();
                $row->master_status_name = $benchMasterStatus->name ?? '';
            }

            if (isset($rowArr['_prehydrated_status_subline'])) {
                $statusSubline = $rowArr['_prehydrated_status_subline'];
            } else {
                $statusSubline = GeneralMethod::bench_status_subline($row->status)->name ?? '';
            }

            // ---- Candidate avatar (first-letter colour bucket, same palette used site-wide) ----
            $name = trim((string) @$row->full_name);
            $initial = $name !== '' ? strtoupper($name[0]) : '?';
            $ord = $name !== '' ? ord(strtolower($name[0])) : 0;
            if ($ord >= 97 && $ord <= 101) {
                $avatarClass = 'bg-light-danger text-danger';
            } elseif ($ord >= 102 && $ord <= 106) {
                $avatarClass = 'bg-light-warning text-warning';
            } elseif ($ord >= 107 && $ord <= 111) {
                $avatarClass = 'bg-light-primary text-primary';
            } elseif ($ord >= 112 && $ord <= 116) {
                $avatarClass = 'bg-light-success text-success';
            } else {
                $avatarClass = 'bg-light-info text-info';
            }

            // ---- Duplicate detection (preserved from the previous template) ----
            $duplicateFlag = 0;
            if (isset($rowArr['_prehydrated_duplicate_flag'])) {
                $duplicateFlag = $rowArr['_prehydrated_duplicate_flag'];
            } else {
                $duplicate = $row->duplicate ?? 0;
                if ($duplicate == 1) {
                    $duplicateFlag = 1;
                }
            }

            $profileSidebarCss = 'show-application-detail';

            // ---- Job column ----
            $jobTitle = ucwords(mb_substr((string) $row->title, 0, 22));
            if (isset($rowArr['_prehydrated_client_name'])) {
                $clientNameCom = $rowArr['_prehydrated_client_name'];
            } else {
                $clientNameCom = GeneralMethod::companyDetailsByClient($row->jobId);
            }
            $clientTitleName = !empty($clientNameCom) ? $clientNameCom : $row->client_name;
            $clientName = ucwords(mb_substr((string) $clientTitleName, 0, 22));

            // ---- Job Status, ID & Client column ----
            if (isset($rowArr['_prehydrated_job_status'])) {
                $jobStatusGet = $rowArr['_prehydrated_job_status'];
            } else {
                $jobStatusGet = GeneralMethod::jobDetails($row->jobId);
            }
            $jobStatus = ucwords((string) $jobStatusGet->status);
            if ($jobStatus === 'Active') {
                $jobStatus = 'Hiring';
                $jobStatusPill = 'bo-pill-green';
            } elseif ($jobStatus === 'Inactive') {
                $jobStatus = 'Closed';
                $jobStatusPill = 'bo-pill-red';
            } else {
                $jobStatusPill = 'bo-pill-amber';
            }
            $isInternalClient = (($row->recommendation_id > 0 && @$row->client_comapny_id == auth()->user()->company_id) || @$row->client_comapny_id == auth()->user()->company_id);
            $parentCompName = $row->company_name ?? ($row->client_name ?? '');
            $clientTypeLabel = $isInternalClient ? 'Internal' : 'Direct Client';

            // ---- Compensation column ----
            $ofOfferAmount = $row->of_offer_amount;
            $ofJoinedDate = @$row->of_joined_date;
            $hasOffer = !empty($ofOfferAmount) && !empty($ofJoinedDate);
            $optionCurrency = $currency[$row->of_offer_currency] ?? '';
            $optionSalaryMode = $salaryModeOffer[$row->of_offer_mode ? $row->of_offer_mode : 0] ?? '';
            $employmentType = ($row->of_employment_type ?? $row->employment_type) == 1 ? 'Contract' : 'Full-time';
            $confidentialAllowed = $rowArr['_prehydrated_confidential_allow'] ?? GeneralMethod::confidentialAmontShow($row->jobId);
            if ($hasOffer && @$row->confidential == 1 && !$confidentialAllowed) {
                $compensationAmount = 'Confidential';
            } else {
                $compensationAmount = $optionCurrency . ' ' . number_format((float) $ofOfferAmount) . '/' . mb_substr($optionSalaryMode, 0, 1);
            }
            $createOfferUrl = route('service_company.job-applications.create_offer', $row->offer_id);

            // ---- Offer Status column ----
            $offerStatusLabel = 'Pending';
            $offerStatusPill = 'bo-pill-amber';
            $offerStatusDate = @$row->created_at;
            if ($hasOffer) {
                switch ((int) $row->of_offer_acceptance) {
                    case 1:
                        $offerStatusLabel = 'Offer Released';
                        $offerStatusPill = 'bo-pill-blue';
                        break;
                    case 2:
                        $offerStatusLabel = 'Offer Accepted';
                        $offerStatusPill = 'bo-pill-green';
                        break;
                    case 3:
                        $offerStatusLabel = 'Offer Rejected';
                        $offerStatusPill = 'bo-pill-red';
                        break;
                    case 4:
                        $offerStatusLabel = 'Offer Expired';
                        $offerStatusPill = 'bo-pill-gray';
                        $offerStatusDate = @$row->of_expired_date;
                        break;
                    default:
                        $offerStatusLabel = 'Offer Created';
                        $offerStatusPill = 'bo-pill-indigo';
                        $offerStatusDate = @$row->updated_at;
                        break;
                }
            }

            // ---- Joining column ----
            $joinedDateRaw = @$row->of_joined_date;
            $hasJoinDate = !empty($joinedDateRaw) && date('Y-m-d', strtotime($joinedDateRaw)) !== '1970-01-01';
            $joiningRelClass = 'none';
            $joiningRelText = 'Not joined';
            if ($hasJoinDate) {
                $daysDiff = (int) floor((strtotime(date('Y-m-d', strtotime($joinedDateRaw))) - strtotime(date('Y-m-d'))) / 86400);
                if ($daysDiff > 0) {
                    $joiningRelClass = 'future';
                    $joiningRelText = 'in ' . $daysDiff . ' day' . ($daysDiff > 1 ? 's' : '');
                } elseif ($daysDiff < 0) {
                    $joiningRelClass = 'past';
                    $joiningRelText = abs($daysDiff) . ' day' . (abs($daysDiff) > 1 ? 's' : '') . ' ago';
                } else {
                    $joiningRelClass = 'future';
                    $joiningRelText = 'Today';
                }
            }
        @endphp
        <tr>
            <td>
                <div class="bo-cell-flex">
                    <span class="bo-avatar {{ $avatarClass }} {{ $duplicateFlag ? 'dup' : '' }}"
                        @if ($duplicateFlag) data-toggle="tooltip" title="Duplicate" @endif>{{ $initial }}</span>
                    <div class="bo-cell-col">
                        <a href="javascript:void(0);" data-row-id="{{ $row->wiseId }}" class="t1 {{ $profileSidebarCss }}"
                            title="{{ ucwords((string) $row->full_name) }}">{{ ucwords(mb_substr((string) $row->full_name, 0, 24)) }}</a>
                        <span class="t2" title="{{ ucwords((string) $row->current_designation) }}">{{ ucwords(mb_substr((string) $row->current_designation, 0, 24)) }}</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="bo-cell-col">
                    <span class="t1" title="{{ $row->title }}">{{ $jobTitle }}</span>
                    <span class="t2" title="{{ $clientTitleName }}">{{ $clientName }}</span>
                </div>
            </td>
            <td>
                <div class="bo-cell-col">
                    <span class="bo-pill {{ $jobStatusPill }}" style="width:fit-content;">{{ $jobStatus }}</span>
                    <span class="t2"><span class="link" style="cursor:default;">{{ $clientTypeLabel }}</span> &bull; {{ $row->jobId }}</span>
                </div>
            </td>
            <td>
                <div class="bo-cell-col">
                    @if ($hasOffer)
                        <span class="t1">{{ $compensationAmount }}</span>
                        <span class="t2">{{ $employmentType }}</span>
                        <a href="{{ $createOfferUrl }}" class="link">Change</a>
                    @else
                        <a href="{{ $createOfferUrl }}" class="link accent">Create Offer</a>
                    @endif
                </div>
            </td>
            <td>
                <div class="bo-cell-col">
                    <span class="bo-pill {{ $offerStatusPill }}" style="width:fit-content;">{{ $offerStatusLabel }}</span>
                    <span class="t3">{{ !empty($offerStatusDate) ? date('d M Y', strtotime($offerStatusDate)) : '' }}</span>
                </div>
            </td>
            <td>
                <div class="bo-cell-col">
                    @if ($hasJoinDate)
                        <span class="t1">{{ date('d M Y', strtotime($joinedDateRaw)) }}</span>
                        <span class="t2">{{ date('g A (\G\M\TO)', strtotime($joinedDateRaw)) }}</span>
                    @else
                        <span class="t1">&mdash;</span>
                    @endif
                    <span class="bo-joining-rel {{ $joiningRelClass }}">{{ $joiningRelText }}</span>
                </div>
            </td>
            <td>
                <span class="bo-pill-outline">Offered</span>
            </td>
            <td>
                <div class="bo-cell-col">
                    <a href="javascript:void(0);" class="t1 link stage_sub_stage" data-row-id="{{ $row->id }}"
                        data-placedid="{{ $row->placedID }}" data-toggle="modal" data-target="#stage_sub_stage">{{ $row->master_status_name }}</a>
                    <span class="t3" data-toggle="tooltip" title="{{ $statusSubline }}">{{ $statusSubline }}</span>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" style="text-align: center; color: #8a93a8; padding: 40px 0;">
            No data available
        </td>
    </tr>
@endif
