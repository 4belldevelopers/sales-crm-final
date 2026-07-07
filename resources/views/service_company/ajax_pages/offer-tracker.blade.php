@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $currency       = GeneralMethod::currency();
    $salaryModeOffer = GeneralMethod::salaryModeOffer();
    $job_status_arr = GeneralMethod::job_status();

    $now = \Carbon\Carbon::now();

    /* ── Offer-lifecycle dates not present on the Elasticsearch candidate doc ──
       "Salary saved" and "sent for approval" dates only live in MySQL
       (offer_job_applications.updated_at / offer_create_main_tds.send_approved_date_time,
       offer_create_main_tds.accept_date_time for the candidate's accept/reject moment).
       Fetched once for the whole page (not per row) to avoid N+1 queries; tenancy is
       scoped explicitly since raw DB::table() is not auto-scoped by company_id. */
    $offerIds = collect($data)->pluck('offer_id')->filter()->unique()->values()->all();
    $offerMeta = [];
    if (!empty($offerIds)) {
        $metaRows = DB::table('offer_job_applications as o')
            ->leftJoin('offer_create_main_tds as t', function ($j) {
                $j->on('t.offer_id', '=', 'o.id')->whereIn('t.status', [1, 3]);
            })
            ->whereIn('o.id', $offerIds)
            ->where('o.company_id', auth()->user()->company_id)
            ->select('o.id as offer_id', 'o.updated_at as salary_saved_at', 't.send_approved_date_time', 't.accept_date_time')
            ->get();
        foreach ($metaRows as $m) { $offerMeta[$m->offer_id] = $m; }
    }
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
        /* Rule 1/2: "pending" (no offer yet) is driven purely by whether a salary
           has been entered — a saved salary means the offer exists ("Offer Created"). */
        $hasPendingOffer = empty($offerAmount);

        /* ── Offer Status — 7-state logic (row 1 = badge, row 2/3 = sub-status / date) ──
           1. No salary entered            → Pending           (date: offer-stage entry date)
           2. Salary entered & saved       → Offer Created     (date: salary saved date)
           3. Released via email           → Offer Released    (sub: "Offer Sent")
           4. Submitted for approval       → Approval Pending  (date: approval-email-sent date)
           5. Candidate accepted           → Accepted          (sub: date accepted)
           6. Candidate rejected           → Rejected          (sub: date rejected)
           7. No response / timed out      → Expired           (sub: date expired)
           Dates for 2 and 4 aren't on the Elasticsearch doc — pulled from the $offerMeta
           bulk lookup above (offer_job_applications.updated_at / offer_create_main_tds). */
        $offerAcceptance = (int)($row->of_offer_acceptance ?? 0); // 0 none · 1 released · 2 accepted · 3 rejected · 4 expired
        $subForApproval  = (int)($row->sub_for_approval ?? 0);    // 1 = sent for approval
        $isJoined        = ($row->of_joined ?? 0) == 1;           // used by the Joining column
        $meta            = $offerMeta[$row->offer_id ?? 0] ?? null;
        $fmtDate = fn($d) => (!empty($d) && date('d-m-Y', strtotime($d)) !== '01-01-1970')
                                ? \Carbon\Carbon::parse($d)->format('d M Y') : '';

        $offerStatusText = '';   // row 2 — sub-status text
        $offerStatusDate = '';   // row 3 — date

        if ($hasPendingOffer) {                       // 1 — no salary yet
            $offerBadgeLabel = 'Pending';          $offerBadgeColor = '#d97706'; $offerBadgeBg = '#fef3c7';
            $offerIconPath   = 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z';
            $offerStatusDate = $fmtDate($row->of_stage_date ?? $row->created_at ?? '');
        } elseif ($offerAcceptance == 4) {            // 7 — expired
            $offerBadgeLabel = 'Expired';          $offerBadgeColor = '#64748b'; $offerBadgeBg = '#f1f5f9';
            $offerIconPath   = 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z';
            $offerStatusDate = $fmtDate($row->of_expired_date ?? '');
        } elseif ($offerAcceptance == 3) {            // 6 — rejected
            $offerBadgeLabel = 'Rejected';         $offerBadgeColor = '#dc2626'; $offerBadgeBg = '#fee2e2';
            $offerIconPath   = 'm9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            $offerStatusDate = $fmtDate($meta->accept_date_time ?? '');
        } elseif ($offerAcceptance == 2) {            // 5 — accepted
            $offerBadgeLabel = 'Accepted';         $offerBadgeColor = '#16a34a'; $offerBadgeBg = '#dcfce7';
            $offerIconPath   = 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            $offerStatusDate = $fmtDate($meta->accept_date_time ?? '');
        } elseif ($offerAcceptance == 1) {            // 3 — released to candidate
            $offerBadgeLabel = 'Offer Released';   $offerBadgeColor = '#2563eb'; $offerBadgeBg = '#dbeafe';
            $offerIconPath   = 'M6 12L3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12zm0 0h7.5';
            $offerStatusText = 'Offer Sent';
        } elseif ($subForApproval == 1) {             // 4 — sent for approval
            $offerBadgeLabel = 'Approval Pending'; $offerBadgeColor = '#c2410c'; $offerBadgeBg = '#ffedd5';
            $offerIconPath   = 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z';
            $offerStatusDate = $fmtDate($meta->send_approved_date_time ?? '');
        } else {                                      // 2 — salary saved, offer created
            $offerBadgeLabel = 'Offer Created';    $offerBadgeColor = '#4f46e5'; $offerBadgeBg = '#e0e7ff';
            $offerIconPath   = 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z';
            $offerStatusDate = $fmtDate($meta->salary_saved_at ?? '');
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
        // "Offered" stage gets an orange box instead of the default purple, whether or not
        // the button is clickable in this row's context.
        $stageIsOffered = strtolower(trim($row->name ?? '')) === 'offered';
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
                   class="ot-stage-btn {{ $stageClass }} {{ $stageIsOffered ? 'ot-stage-offered' : '' }}" data-toggle="modal" data-target="#{{ $stageClass }}">{{ $masterStatusName }}</a>
            @else
                <span class="ot-stage-btn ot-stage-disabled {{ $stageIsOffered ? 'ot-stage-offered' : '' }}">{{ $masterStatusName }}</span>
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
