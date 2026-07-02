@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $currency       = GeneralMethod::currency();
    $salaryModeOffer = GeneralMethod::salaryModeOffer();
    $job_status_arr = GeneralMethod::job_status();

    $offerStatusMap = [
        0 => ['label'=>'Pending',  'color'=>'#d97706','bg'=>'#fef3c7','icon'=>'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        1 => ['label'=>'Released',  'color'=>'#2563eb','bg'=>'#dbeafe','icon'=>'M6 12L3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12zm0 0h7.5'],
        2 => ['label'=>'Accepted', 'color'=>'#16a34a','bg'=>'#dcfce7','icon'=>'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
        3 => ['label'=>'Rejected', 'color'=>'#dc2626','bg'=>'#fee2e2','icon'=>'m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
        4 => ['label'=>'Expired',  'color'=>'#dc2626','bg'=>'#fee2e2','icon'=>'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z'],
    ];
    $joinedIconPath = 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z';

    $now = \Carbon\Carbon::now();
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
        $row = (object) $rowArr;
        $company_id = auth()->user()->company_id;

        /* ── Avatar ── */
        $initials = strtoupper(substr(trim($row->full_name ?? ''), 0, 1));
        $nameChar = strtolower($initials);
        $charCode = ord($nameChar);
        if ($charCode >= 97 && $charCode <= 101)      { $avatarColor = '#ef4444'; $avatarBg = '#fee2e2'; }
        elseif ($charCode >= 102 && $charCode <= 106) { $avatarColor = '#f59e0b'; $avatarBg = '#fef3c7'; }
        elseif ($charCode >= 107 && $charCode <= 111) { $avatarColor = '#3b82f6'; $avatarBg = '#dbeafe'; }
        elseif ($charCode >= 112 && $charCode <= 116) { $avatarColor = '#22c55e'; $avatarBg = '#dcfce7'; }
        elseif ($charCode >= 117 && $charCode <= 121) { $avatarColor = '#6366f1'; $avatarBg = '#e0e7ff'; }
        else                                           { $avatarColor = '#64748b'; $avatarBg = '#f1f5f9'; }

        $photo = $row->photo ?? '';
        $profile_sidebar_css = 'show-application-detail';

        /* ── Bench tag ── */
        $isBench = ($row->condidate_type ?? '') === 'bench_condidate';

        /* ── Job details ── */
        $jobTitle  = ucwords(mb_substr($row->title ?? '', 0, 22));
        $clientName = '';
        $clientFull = GeneralMethod::companyDetailsByClient($row->jobId);
        if (!empty($clientFull)) { $clientName = ucwords(mb_substr($clientFull, 0, 20)); }
        else { $clientName = ucwords(mb_substr($row->client_name ?? '', 0, 20)); }

        /* ── Job status ── */
        $jobStatusRaw = 'active';
        if (!empty($row->jobId)) {
            try { $jobStatusRaw = GeneralMethod::jobDetails($row->jobId)->status ?? 'active'; } catch (\Throwable $e) {}
        }
        $jobStatusLabel = match($jobStatusRaw) {
            'active'   => 'Hiring',
            'hold'     => 'Hold',
            'inactive' => 'Closed',
            default    => ucfirst($jobStatusRaw),
        };
        $jobStatusStyle = match($jobStatusRaw) {
            'active'   => 'color:#16a34a;background:#dcfce7;',   // Hiring — green
            'hold'     => 'color:#d97706;background:#fef3c7;',   // Hold — orange
            'inactive' => 'color:#dc2626;background:#fee2e2;',   // Closed — red
            default    => 'color:#64748b;background:#f1f5f9;',
        };

        /* ── Client type (Direct vs Internal) — internal = the company's own job ── */
        $isOwnJob        = ($row->client_comapny_id ?? 0) == $company_id;
        $clientType      = $isOwnJob ? 'Internal Client' : 'Direct Client';
        $clientTypeColor = $isOwnJob ? '#2563eb' : '#16a34a'; // internal = blue, direct = green

        /* ── Compensation ── */
        $offerAmount   = $row->of_offer_amount ?? '';
        $offerJoinDate = $row->of_joined_date ?? '';
        $currencyLabel = $currency[$row->of_offer_currency ?? 0] ?? '₹';
        $modeLabel     = mb_substr($salaryModeOffer[$row->of_offer_mode ?? 0] ?? 'Yearly', 0, 1);
        $empType       = ($row->employment_type ?? 0) == 1 ? 'Contract' : 'Full-time';
        $empLabel      = $row->of_employment_type ?? $empType;
        $isConfidential = ($row->confidential ?? 0) == 1;
        if ($isConfidential) {
            $allow = GeneralMethod::confidentialAmontShow($row->jobId);
            $compDisplay = $allow ? ($currencyLabel.' '.number_format((float)$offerAmount).'/'.$modeLabel) : 'Confidential';
        } else {
            $compDisplay = !empty($offerAmount) ? ($currencyLabel.' '.number_format((float)$offerAmount).'/'.$modeLabel) : '—';
        }
        $hasPendingOffer = empty($offerAmount) || empty($offerJoinDate);

        /* ── Offer Status ── */
        $offerAcceptance = (int)($row->of_offer_acceptance ?? 0);
        $isJoined        = ($row->of_joined ?? 0) == 1;
        if ($isJoined) {
            $offerBadgeLabel = 'Joined';
            $offerBadgeColor = '#0891b2'; $offerBadgeBg = '#cffafe';
            $offerIconPath   = $joinedIconPath;
        } elseif ($hasPendingOffer) {
            $offerBadgeLabel = 'Pending';
            $offerBadgeColor = '#d97706'; $offerBadgeBg = '#fef3c7';
            $offerIconPath   = $offerStatusMap[0]['icon'];
        } else {
            $s = $offerStatusMap[$offerAcceptance] ?? $offerStatusMap[0];
            $offerBadgeLabel = $s['label'];
            $offerBadgeColor = $s['color'];
            $offerBadgeBg    = $s['bg'];
            $offerIconPath   = $s['icon'];
        }

        /* ── Offer Status: row 2 = status text, row 3 = date updated ── */
        $offerStatusText = '';
        $offerStatusDate = '';
        if ($isJoined && !empty($offerJoinDate)) {
            $offerStatusText = 'Joined';
            $offerStatusDate = \Carbon\Carbon::parse($offerJoinDate)->format('d M Y');
        } elseif ($offerAcceptance == 4) {
            $offerStatusText = 'Offer Expired';
            $offerStatusDate = !empty($row->of_expired_date) ? \Carbon\Carbon::parse($row->of_expired_date)->format('d M Y') : '';
        } elseif ($offerAcceptance == 3) {
            $offerStatusText = 'Offer Rejected';
            $offerStatusDate = !empty($row->of_rejected_date) ? \Carbon\Carbon::parse($row->of_rejected_date)->format('d M Y') : '';
        } elseif ($offerAcceptance == 2) {
            $offerStatusText = 'Offer Accepted';
            $offerStatusDate = !empty($row->of_accepted_date) ? \Carbon\Carbon::parse($row->of_accepted_date)->format('d M Y') : '';
        } elseif ($offerAcceptance == 1) {
            $offerStatusText = 'Offer Released';
            $offerStatusDate = !empty($row->of_released_date) ? \Carbon\Carbon::parse($row->of_released_date)->format('d M Y') : '';
        } elseif ($hasPendingOffer) {
            $offerStatusText = 'Offer Pending';
            $offerStatusDate = $now->format('d M Y');
        }

        /* ── Joining countdown ── */
        $joiningDate = $joiningTime = $joiningCountdown = $joiningCountdownStyle = '';
        if (!empty($offerJoinDate)) {
            $jDate = \Carbon\Carbon::parse($offerJoinDate);
            $check = date("d-m-Y", strtotime($offerJoinDate));
            if ($check !== '01-01-1970') {
                $joiningDate  = $jDate->format('d M Y');
                $joiningTime  = $jDate->format('g A (T)');
                $daysLeft = (int)$now->diffInDays($jDate, false);
                if ($daysLeft > 0) {
                    $joiningCountdown = 'in '.$daysLeft.' days';
                    $joiningCountdownStyle = $daysLeft <= 7 ? 'color:#ef4444' : ($daysLeft <= 14 ? 'color:#f59e0b' : 'color:#3b82f6');
                } elseif ($daysLeft < 0) {
                    $joiningCountdown = abs($daysLeft).' days ago';
                    $joiningCountdownStyle = 'color:#64748b';
                } else {
                    $joiningCountdown = 'Today';
                    $joiningCountdownStyle = 'color:#22c55e;font-weight:600';
                }
            }
        }

        /* ── Stage ── */
        $masterStatusName = ucwords(mb_substr($row->name ?? 'Offer', 0, 10));
        $stageClass = 'stage_sub_stage';
        if (($row->thirdparty_company_id ?? 0) > 0 && $row->thirdparty_company_id == $company_id) {
            $stageClass = ($row->master_status_id ?? 0) == 8 ? 'stage_sub_stage' : '';
        }
    @endphp

    <tr class="ot-row">
        {{-- Candidate --}}
        <td class="ot-td ot-td-candidate">
            <div class="ot-cand">
                @if(!empty($photo))
                    <img src="{{ $photo }}" class="ot-avatar-img" alt="">
                @else
                    <div class="ot-avatar" style="background:{{ $avatarBg }};color:{{ $avatarColor }};">{{ $initials }}</div>
                @endif
                <div class="ot-cand-info">
                    <a href="javascript:void(0);" data-row-id="{{ $row->wiseId }}"
                       class="ot-cand-name {{ $profile_sidebar_css }}">{{ ucwords(mb_substr($row->full_name ?? '', 0, 22)) }}@if($isBench) <sup class="ot-bench">Bench</sup>@endif</a>
                    <span class="ot-cand-role">{{ ucwords(mb_substr($row->current_designation ?? '', 0, 22)) }}</span>
                </div>
            </div>
        </td>

        {{-- Job: 1st row = position, 2nd row = company name --}}
        <td class="ot-td">
            <span class="ot-job-title">{{ $jobTitle }}</span>
            <span class="ot-job-type">{{ $clientName ?: '—' }}</span>
        </td>

        {{-- Job Status: 1st row = status, 2nd row = client type + Job ID (colored) --}}
        <td class="ot-td">
            <span class="ot-status-pill" style="{{ $jobStatusStyle }}">{{ $jobStatusLabel }}</span>
            <span class="ot-job-meta" style="color:{{ $clientTypeColor }};font-weight:600;">{{ $clientType }} &bull; {{ $row->jobId }}</span>
        </td>

        {{-- Compensation: 1st = amount, 2nd = type, 3rd = Change --}}
        <td class="ot-td">
            @if($hasPendingOffer)
                @if($isOwnJob)
                    <a href="javascript:void(0);" class="create_offer ot-create-btn" data-row-offerid="{{ $row->offer_id ?? '' }}">Create Offer</a>
                @else
                    <span class="ot-pending-badge">Offer Pending</span>
                @endif
            @else
                @if($isOwnJob)
                    <a href="javascript:void(0);" class="create_offer" style="text-decoration:none;color:inherit;" data-row-offerid="{{ $row->offer_id ?? '' }}">
                        <span class="ot-comp-amount">{{ $compDisplay }}</span>
                        <span class="ot-comp-type">{{ $empLabel }}</span>
                        <span class="ot-comp-change">Change</span>
                    </a>
                @else
                    <a href="javascript:void(0);" class="ot-client-offer-warn" style="text-decoration:none;color:inherit;">
                        <span class="ot-comp-amount">{{ $compDisplay }}</span>
                        <span class="ot-comp-type">{{ $empLabel }}</span>
                        <span class="ot-comp-change">Change</span>
                    </a>
                @endif
            @endif
        </td>

        {{-- Offer Status: 1st = badge, 2nd = status text, 3rd = date updated --}}
        <td class="ot-td">
            <span class="ot-offer-badge" style="background:{{ $offerBadgeBg }};color:{{ $offerBadgeColor }};">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $offerIconPath }}"/></svg>
                {{ $offerBadgeLabel }}
            </span>
            @if($offerStatusText)
                <span class="ot-offer-sub">{{ $offerStatusText }}</span>
            @endif
            @if($offerStatusDate)
                <span class="ot-offer-date">{{ $offerStatusDate }}</span>
            @endif
        </td>

        {{-- Joining --}}
        <td class="ot-td">
            @if($joiningDate)
                <span class="ot-join-date">{{ $joiningDate }}</span>
                <span class="ot-join-time">{{ $joiningTime }}</span>
                <span class="ot-join-countdown" style="{{ $joiningCountdownStyle }}">{{ $joiningCountdown }}</span>
            @else
                <span class="ot-na">—</span>
                @if(!$isJoined) <span class="ot-na-sub">Not joined</span> @endif
            @endif
        </td>

        {{-- Stage --}}
        <td class="ot-td ot-td-stage">
            @if(!empty($stageClass))
                <a href="javascript:void(0);" data-row-id="{{ $row->wiseId }}"
                   class="ot-stage-btn {{ $stageClass }}" data-toggle="modal" data-target="#{{ $stageClass }}">{{ $masterStatusName }}</a>
            @else
                <span class="ot-stage-btn ot-stage-disabled">{{ $masterStatusName }}</span>
            @endif
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="7" class="ot-empty">
            <div class="ot-empty-inner">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/></svg>
                <p>No offers found</p>
            </div>
        </td>
    </tr>
@endif
