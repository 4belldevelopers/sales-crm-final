{{--
  JERO ATS — Service Company Offer Tracker Card Stream Partial
  Intent: Renders Elasticsearch offer-stage candidate projections as Rule 20 Modern
          Primary Cards with 5 discrete upper sub-cards and a bottom metadata/action strip.
  Tenancy & Security: Strictly scoped by auth()->user()->company_id in controller and
                      bulk MySQL metadata lookup; all outputs Blade-escaped.
  Screen Fit: Fractional minmax(0, ...fr) grid prevents horizontal overflow on 15.6" screens.
--}}
@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $currency        = GeneralMethod::currency();
    $salaryModeOffer = GeneralMethod::salaryModeOffer();
    $job_status_arr  = GeneralMethod::job_status();
    $now             = \Carbon\Carbon::now();
    $company_id      = auth()->user()->company_id;

    $jobDetailsMap             = $jobDetailsMap ?? [];
    $allowedConfidentialJobIds = $allowedConfidentialJobIds ?? [];
    $childStatusMap            = $childStatusMap ?? [];
    $cityDataMap               = $cityDataMap ?? [];

    /* ── Offer-lifecycle dates not present on the Elasticsearch candidate doc ──
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
            ->where('o.company_id', $company_id)
            ->select('o.id as offer_id', 'o.updated_at as salary_saved_at', 't.send_approved_date_time', 't.accept_date_time')
            ->get();
        foreach ($metaRows as $m) {
            $offerMeta[$m->offer_id] = $m;
        }
    }
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
        $row = (object) $rowArr;

        /* ── 1. Candidate Identity & Avatar ── */
        $fullNameRaw = trim((string) ($row->full_name ?? 'Candidate'));
        $fullName    = ucwords(mb_substr($fullNameRaw, 0, 32));
        $initials    = strtoupper(mb_substr($fullNameRaw, 0, 1) ?: 'C');
        $nameParts   = array_values(array_filter(explode(' ', $fullNameRaw)));
        if (count($nameParts) >= 2) {
            $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
        } elseif (count($nameParts) === 1 && mb_strlen($nameParts[0]) >= 2) {
            $initials = strtoupper(mb_substr($nameParts[0], 0, 2));
        }

        $nameChar = strtolower(mb_substr($fullNameRaw, 0, 1) ?: 'c');
        $charCode = ord($nameChar);
        if ($charCode >= 97 && $charCode <= 101)      { $avatarColor = '#dc2626'; $avatarBg = '#fee2e2'; }
        elseif ($charCode >= 102 && $charCode <= 106) { $avatarColor = '#d97706'; $avatarBg = '#fef3c7'; }
        elseif ($charCode >= 107 && $charCode <= 111) { $avatarColor = '#2563eb'; $avatarBg = '#dbeafe'; }
        elseif ($charCode >= 112 && $charCode <= 116) { $avatarColor = '#16a34a'; $avatarBg = '#dcfce7'; }
        elseif ($charCode >= 117 && $charCode <= 121) { $avatarColor = '#7c3aed'; $avatarBg = '#ede9fe'; }
        else                                           { $avatarColor = '#475569'; $avatarBg = '#f1f5f9'; }

        $photo = $row->photo ?? '';
        $profile_sidebar_css = 'show-application-detail';
        $isBench = ($row->condidate_type ?? '') === 'bench_condidate';
        $designation = ucwords(trim((string) ($row->current_designation ?? '')));

        $candCity = trim((string) ($row->city_name ?? ''));
        if ($candCity === '' && !empty($row->city_id) && isset($cityDataMap[$row->city_id])) {
            $candCity = (string) $cityDataMap[$row->city_id];
        }
        $candCountry  = trim((string) ($row->country_name ?? ''));
        $candLocation = implode(', ', array_filter([ucwords($candCity), ucwords($candCountry)]));

        $candCompanyRaw = trim((string) ($row->current_company ?? $row->cur_companyname ?? $row->cur_company_name ?? $row->apl_company_name ?? ''));
        if ($isBench && !empty($row->apl_company_name)) {
            $candCompanyRaw = trim((string) $row->apl_company_name);
        }
        $candCompany = ucwords($candCompanyRaw);

        $expRaw = trim((string) ($row->total_exp ?? ''));
        if ($expRaw !== '' && str_contains($expRaw, '.')) {
            [$expW, $expD] = array_pad(explode('.', $expRaw), 2, '');
            $expRaw = ($expD === '00') ? $expW : $expRaw;
        }
        $expLabel = ($expRaw !== '' && $expRaw != '0') ? ($expRaw . ' Yrs') : '';

        /* ── 2. Job & Client Details (Canonical Rule 25 Classification) ── */
        $jobId      = (int) ($row->jobId ?? 0);
        $jobTitle   = ucwords(mb_substr(trim((string) ($row->title ?? 'Untitled Role')), 0, 36));
        $clientFull = $jobId > 0 ? GeneralMethod::companyDetailsByClient($jobId) : '';
        $clientName = !empty($clientFull)
            ? ucwords(mb_substr(trim((string) $clientFull), 0, 40))
            : ucwords(mb_substr(trim((string) ($row->client_name ?? '')), 0, 40));

        $jobObj = $jobDetailsMap[$jobId] ?? null;

        $isDirect = false;
        if (!empty($row->is_direct_client) || !empty($row->is_job_request) || !empty($row->recommendation_id) || (!empty($row->ClientComID) && (int)$row->ClientComID !== (int)$company_id) || (!empty($row->client_comapny_id) && (int)$row->client_comapny_id !== (int)$company_id && (int)$row->client_comapny_id > 0)) {
            $isDirect = true;
        }

        $resolvedClientId = $jobObj->client_id ?? ($row->client_id ?? null);
        $resolvedSelfCompany = isset($jobObj->client_self_company)
            ? (int) $jobObj->client_self_company
            : (isset($row->client_self_company) ? (int) $row->client_self_company : null);

        $tenantCompanyName = trim((string) (auth()->user()->company->company_name ?? ''));
        $clientNameTrim = trim((string) ($clientName ?: ($jobObj->client_name ?? '')));

        if ($isDirect) {
            $targetTypeLabel = 'Direct Client';
            $targetTypeClass = 'type-direct';
            $targetTypeIcon  = 'fas fa-shield-alt';
            $targetTypeStyle = 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;';
        } elseif ($resolvedSelfCompany === 0 || (!empty($resolvedClientId) && $resolvedSelfCompany !== 1 && !empty($clientNameTrim) && ($tenantCompanyName === '' || strcasecmp($clientNameTrim, $tenantCompanyName) !== 0))) {
            $targetTypeLabel = 'Internal Client';
            $targetTypeClass = 'type-internal';
            $targetTypeIcon  = 'fas fa-building';
            $targetTypeStyle = 'background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;';
        } else {
            $targetTypeLabel = 'My Organization';
            $targetTypeClass = 'type-my-org';
            $targetTypeIcon  = 'fas fa-id-badge';
            $targetTypeStyle = 'background:#fef3c7; color:#92400e; border:1px solid #fcd34d;';
        }

        $isOwnJob = ($targetTypeLabel !== 'Direct Client');

        /* ── 3. Compensation & Employment Type ── */
        $offerAmount    = $row->of_offer_amount ?? '';
        $offerJoinDate  = $row->of_joined_date ?? '';
        $currencyLabel  = $currency[$row->of_offer_currency ?? 0] ?? '₹';
        $modeFull       = $salaryModeOffer[$row->of_offer_mode ?? 0] ?? 'Yearly';
        $modeLabel      = mb_substr($modeFull, 0, 1);

        $employmentTypesList = GeneralMethod::employmentType();
        $rawEmpTypeVal = $row->employment_type ?? 0;
        $empType = $employmentTypesList[(int)$rawEmpTypeVal] ?? ($rawEmpTypeVal == 1 ? 'Contract' : 'Full-time');
        if (!empty($row->of_employment_type)) {
            $empLabel = ucwords((string) $row->of_employment_type);
        } else {
            $empLabel = ucwords((string) $empType);
        }

        $resolvedEmpVal = 0;
        if (strtolower($empLabel) === 'contract' || (string)$rawEmpTypeVal === '1') {
            $resolvedEmpVal = 1;
        } elseif (str_contains(strtolower($empLabel), 'freelanc') || (string)$rawEmpTypeVal === '2') {
            $resolvedEmpVal = 2;
        } elseif (str_contains(strtolower($empLabel), 'intern') || (string)$rawEmpTypeVal === '4') {
            $resolvedEmpVal = 4;
        } else {
            $resolvedEmpVal = 0;
        }

        $isConfidential = ($row->confidential ?? 0) == 1;

        if ($isConfidential) {
            $allow = isset($allowedConfidentialJobIds[$jobId]) ? !empty($allowedConfidentialJobIds[$jobId]) : GeneralMethod::confidentialAmontShow($jobId);
            $compDisplay = $allow && !empty($offerAmount)
                ? ($currencyLabel . ' ' . number_format((float) $offerAmount) . '/' . $modeLabel)
                : 'Confidential';
        } else {
            $compDisplay = !empty($offerAmount)
                ? ($currencyLabel . ' ' . number_format((float) $offerAmount) . '/' . $modeLabel)
                : '—';
        }
        $hasPendingOffer = empty($offerAmount);

        /* ── 4. Offer Status — 7-state lifecycle ── */
        $offerAcceptance = (int) ($row->of_offer_acceptance ?? 0); // 0 none · 1 released · 2 accepted · 3 rejected · 4 expired
        $subForApproval  = (int) ($row->sub_for_approval ?? 0);    // 1 = sent for approval
        $isJoined        = ($row->of_joined ?? 0) == 1;
        $meta            = $offerMeta[$row->offer_id ?? 0] ?? null;
        $fmtDate = fn($d) => (!empty($d) && date('d-m-Y', strtotime($d)) !== '01-01-1970')
            ? \Carbon\Carbon::parse($d)->format('d M Y') : '';

        $offerStatusText = '';
        $offerStatusDate = '';
        $cardAccentColor = '#f59e0b';

        if ($hasPendingOffer) {                       // 1 — no salary yet
            $offerBadgeLabel = 'Pending';          $offerBadgeColor = '#b45309'; $offerBadgeBg = '#fef3c7'; $offerBadgeBorder = '#fde68a';
            $offerIconPath   = 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z';
            $offerStatusText = 'Awaiting offer creation';
            $offerStatusDate = $fmtDate($row->of_stage_date ?? $row->created_at ?? '');
            $cardAccentColor = '#f59e0b';
        } elseif ($offerAcceptance == 4) {            // 7 — expired
            $offerBadgeLabel = 'Expired';          $offerBadgeColor = '#475569'; $offerBadgeBg = '#f1f5f9'; $offerBadgeBorder = '#cbd5e1';
            $offerIconPath   = 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z';
            $offerStatusText = 'Renewal needed';
            $offerStatusDate = $fmtDate($row->of_expired_date ?? '');
            $cardAccentColor = '#94a3b8';
        } elseif ($offerAcceptance == 3) {            // 6 — rejected
            $offerBadgeLabel = 'Rejected';         $offerBadgeColor = '#b91c1c'; $offerBadgeBg = '#fee2e2'; $offerBadgeBorder = '#fecaca';
            $offerIconPath   = 'm9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            $offerStatusText = 'Candidate declined';
            $offerStatusDate = $fmtDate($meta->accept_date_time ?? '');
            $cardAccentColor = '#ef4444';
        } elseif ($offerAcceptance == 2) {            // 5 — accepted
            $offerBadgeLabel = 'Accepted';         $offerBadgeColor = '#15803d'; $offerBadgeBg = '#dcfce7'; $offerBadgeBorder = '#bbf7d0';
            $offerIconPath   = 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            $offerStatusText = $isJoined ? 'Joined' : 'Ready for onboarding';
            $offerStatusDate = $fmtDate($meta->accept_date_time ?? '');
            $cardAccentColor = '#10b981';
        } elseif ($offerAcceptance == 1) {            // 3 — released to candidate
            $offerBadgeLabel = 'Offer Released';   $offerBadgeColor = '#1d4ed8'; $offerBadgeBg = '#dbeafe'; $offerBadgeBorder = '#bfdbfe';
            $offerIconPath   = 'M6 12L3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12zm0 0h7.5';
            $offerStatusText = 'Offer emailed to candidate';
            $cardAccentColor = '#009ef7';
        } elseif ($subForApproval == 1) {             // 4 — sent for approval
            $offerBadgeLabel = 'Approval Pending'; $offerBadgeColor = '#c2410c'; $offerBadgeBg = '#ffedd5'; $offerBadgeBorder = '#fed7aa';
            $offerIconPath   = 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z';
            $offerStatusText = 'Awaiting sign-off';
            $offerStatusDate = $fmtDate($meta->send_approved_date_time ?? '');
            $cardAccentColor = '#ea580c';
        } else {                                      // 2 — salary saved, offer created
            $offerBadgeLabel = 'Offer Created';    $offerBadgeColor = '#4338ca'; $offerBadgeBg = '#e0e7ff'; $offerBadgeBorder = '#c7d2fe';
            $offerIconPath   = 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z';
            $offerStatusText = 'Compensation saved';
            $offerStatusDate = $fmtDate($meta->salary_saved_at ?? '');
            $cardAccentColor = '#6366f1';
        }

        /* ── 5. Joining Countdown ── */
        $joiningDate = $joiningTime = $joiningCountdown = $joiningCountdownBg = $joiningCountdownColor = '';
        if (!empty($offerJoinDate)) {
            $jDate = \Carbon\Carbon::parse($offerJoinDate);
            $check = date("d-m-Y", strtotime($offerJoinDate));
            if ($check !== '01-01-1970') {
                $joiningDate = $jDate->format('d M Y');
                $joiningTime = $jDate->format('g A (T)');
                $daysLeft    = (int) $now->diffInDays($jDate, false);
                if ($daysLeft > 0) {
                    $joiningCountdown = 'in ' . $daysLeft . ' ' . ($daysLeft === 1 ? 'day' : 'days');
                    if ($daysLeft <= 7) {
                        $joiningCountdownColor = '#dc2626'; $joiningCountdownBg = '#fee2e2';
                    } elseif ($daysLeft <= 14) {
                        $joiningCountdownColor = '#d97706'; $joiningCountdownBg = '#fef3c7';
                    } else {
                        $joiningCountdownColor = '#2563eb'; $joiningCountdownBg = '#eff6ff';
                    }
                } elseif ($daysLeft < 0) {
                    $joiningCountdown      = abs($daysLeft) . 'd ago';
                    $joiningCountdownColor = '#64748b';
                    $joiningCountdownBg    = '#f1f5f9';
                } else {
                    $joiningCountdown      = 'Today';
                    $joiningCountdownColor = '#15803d';
                    $joiningCountdownBg    = '#dcfce7';
                }
            }
        }

        /* ── 6. Ownership & Stage ── */
        $ownerRecruiter = ucwords(mb_substr(trim((string) ($row->posted_by ?? '')), 0, 24));
        $ownerCompany   = $row->submitted_by_company_name ?? $row->post_company_name ?? $row->company_name ?? '';
        $ownerCompany   = ucwords(mb_substr(trim((string) $ownerCompany), 0, 26));

        $masterStatusName = ucwords(mb_substr((string) ($row->name ?? 'Offered'), 0, 14));
        $childStageName   = !empty($row->childStatus) && isset($childStatusMap[$row->childStatus])
            ? ucwords(trim((string) $childStatusMap[$row->childStatus]))
            : '';

        $stageClass = 'stage_sub_stage';
        if (($row->thirdparty_company_id ?? 0) > 0 && $row->thirdparty_company_id == $company_id) {
            $stageClass = ($row->master_status_id ?? 0) == 8 ? 'stage_sub_stage' : '';
        }
        $stageIsOffered = strtolower(trim((string) ($row->name ?? ''))) === 'offered';

        /* Stage theme palette (Matches AppTracker & Reference Design) */
        $stageTheme = match(strtolower(trim((string) ($row->name ?? '')))) {
            'offered'             => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a'],
            'inreview', 'review'  => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '#bfdbfe'],
            'interview'           => ['bg' => '#faf5ff', 'color' => '#7c3aed', 'border' => '#ddd6fe'],
            'accepted', 'joined'  => ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'],
            'rejected', 'declined'=> ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'],
            default               => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a'],
        };
        $stagePillBg     = $stageTheme['bg'];
        $stagePillColor  = $stageTheme['color'];
        $stagePillBorder = $stageTheme['border'];

        /* Relative submission time (e.g. "22 days ago", "Today") */
        $rawCreated = $row->created_at ?? $row->of_stage_date ?? null;
        $submittedAgoText = '–';
        if (!empty($rawCreated) && date('d-m-Y', strtotime($rawCreated)) !== '01-01-1970') {
            try {
                $cDate = \Carbon\Carbon::parse($rawCreated);
                $days = (int) $cDate->diffInDays($now);
                if ($days === 0) {
                    $submittedAgoText = 'Today';
                } elseif ($days < 30) {
                    $submittedAgoText = $days . ' ' . ($days === 1 ? 'day' : 'days') . ' ago';
                } else {
                    $submittedAgoText = $cDate->diffForHumans();
                }
            } catch (\Throwable $e) {
                $submittedAgoText = '–';
            }
        }
    @endphp

    <!-- Master Primary Offer Card (Rule 20 Card + Sub-Card System) -->
    <div class="ot-entity-card"
         data-wise-id="{{ $row->wiseId }}"
         data-offer-id="{{ $row->offer_id ?? '' }}"
         data-job-id="{{ $jobId }}"
         data-cand-name="{{ $fullName }}"
         data-job-title="{{ $jobTitle }}"
         data-client-name="{{ $clientName }}"
         data-comp="{{ $compDisplay }}"
         data-offer-status="{{ $offerBadgeLabel }}"
         data-joining="{{ $joiningDate ?: 'Not joined' }}"
         data-owner="{{ $ownerRecruiter ?: '—' }}"
         style="border-left: 3.5px solid {{ $cardAccentColor }} !important;">

        <!-- Upper 6 Discrete Sub-Cards Grid (Rule 20 Card + Sub-Card System) -->
        <div class="ot-entity-grid">

            <!-- SUB-CARD 1: CANDIDATE IDENTITY (Clickable to open Candidate Profile Drawer) -->
            <div class="ot-subcard ot-subcard-cand {{ $profile_sidebar_css }} ot-clickable-subcard"
                 data-row-id="{{ $row->wiseId }}"
                 role="button"
                 tabindex="0"
                 title="Click anywhere to view candidate profile">
                <div class="ot-subcard-head">
                    <span class="ot-subcard-label">Candidate</span>
                    <div class="ot-subcard-badges">
                        @if($isBench)
                            <span class="ot-micro-pill ot-pill-bench" title="Bench Candidate">Bench</span>
                        @endif
                        @if($expLabel)
                            <span class="ot-micro-pill ot-pill-neutral" title="Total Experience">{{ $expLabel }}</span>
                        @endif
                    </div>
                </div>
                <div class="ot-cand-body">
                    @if(!empty($photo))
                        <img src="{{ $photo }}"
                             class="ot-avatar-img {{ $profile_sidebar_css }}"
                             data-row-id="{{ $row->wiseId }}"
                             alt="{{ $initials }}"
                             onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                        <div class="ot-avatar {{ $profile_sidebar_css }}" data-row-id="{{ $row->wiseId }}" style="display:none;background:{{ $avatarBg }};color:{{ $avatarColor }};">{{ $initials }}</div>
                    @else
                        <div class="ot-avatar {{ $profile_sidebar_css }}" data-row-id="{{ $row->wiseId }}" style="background:{{ $avatarBg }};color:{{ $avatarColor }};">{{ $initials }}</div>
                    @endif
                    <div class="ot-cand-text">
                        <a href="javascript:void(0);"
                           data-row-id="{{ $row->wiseId }}"
                           class="ot-cand-name {{ $profile_sidebar_css }}"
                           title="{{ $fullName }}">{{ $fullName }}</a>
                        <div class="ot-cand-role" title="{{ $designation ?: 'Role not specified' }}">{{ $designation ?: '—' }}</div>
                        <div class="ot-cand-company" title="{{ $candCompany !== '' ? ('Current Company: ' . $candCompany) : 'Current Company: Not specified' }}">
                            <i class="fas fa-building"></i>
                            <span>{{ $candCompany !== '' ? $candCompany : '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUB-CARD 2: JOB DETAILS -->
            <div class="ot-subcard ot-subcard-job">
                <div class="ot-subcard-head">
                    <span class="ot-subcard-label">Job Details</span>
                    <div class="ot-subcard-badges">
                        <span class="ot-scope-pill {{ $targetTypeClass }}" style="{{ $targetTypeStyle }}">
                            <i class="{{ $targetTypeIcon }} me-1"></i>{{ $targetTypeLabel }}
                        </span>
                    </div>
                </div>
                <div class="ot-job-body">
                    <div class="ot-job-title" title="{{ $row->title ?? $jobTitle }}">{{ $jobTitle }}</div>
                    <div class="ot-job-meta">
                        @if($jobId > 0)
                            <span class="ot-job-id">#{{ $jobId }}</span>
                            <span class="ot-dot-sep">&bull;</span>
                        @endif
                        <span class="ot-job-client" title="{{ $clientName ?: 'Internal Requisition' }}">{{ $clientName ?: '—' }}</span>
                    </div>
                    <div class="ot-job-tags">
                        <span class="ot-tag-chip"><i class="fas fa-briefcase"></i> {{ $empLabel }}</span>
                    </div>
                </div>
            </div>

            <!-- SUB-CARD 3: COMPENSATION (Mirrors Sub-Card 6 Pipeline & Stage Layout) -->
            <div class="ot-subcard ot-subcard-comp-status {{ $isOwnJob ? 'create_offer ot-clickable-subcard' : '' }}"
                 @if($isOwnJob)
                 data-row-offerid="{{ $row->offer_id ?? '' }}"
                 data-emp-type="{{ $resolvedEmpVal }}"
                 role="button"
                 tabindex="0"
                 title="Click anywhere on this card to {{ $hasPendingOffer ? 'create' : 'update' }} offer"
                 @else
                 title="Client Managed Offer"
                 @endif>
                <div class="ot-subcard-head">
                    <span class="ot-subcard-label">Compensation</span>
                    @if(!$hasPendingOffer && !empty($empLabel))
                        <span class="ot-micro-pill ot-pill-neutral" title="Employment Type: {{ $empLabel }}">{{ $empLabel }}</span>
                    @elseif($hasPendingOffer)
                        <span class="ot-micro-pill ot-pill-pending" title="Offer Pending">Pending</span>
                    @endif
                </div>
                <div class="ot-comp-status-body">
                    <!-- 1st Row: Amount -->
                    <div class="ot-cs-amount-row">
                        @if($hasPendingOffer)
                            <span class="ot-cs-amount is-pending" title="Salary Pending">
                                <i class="fas fa-coins me-1"></i>Salary Pending
                            </span>
                        @else
                            <span class="ot-cs-amount" title="Proposed Compensation: {{ $compDisplay }}">
                                {{ $compDisplay }}
                            </span>
                        @endif
                    </div>

                    <!-- 2nd Row: Employment Type Options (Pending) OR Offer Status Badge (Existing) -->
                    <div class="ot-cs-status-row">
                        @if($hasPendingOffer)
                            <div class="ot-cs-emp-group" role="group" aria-label="Employment Type" onclick="event.stopPropagation();">
                                <button type="button" class="ot-emp-btn {{ $resolvedEmpVal === 0 ? 'is-active' : '' }}" data-emp-type="0" data-row-offerid="{{ $row->offer_id ?? '' }}" title="Full-time">Full-time</button>
                                <button type="button" class="ot-emp-btn {{ $resolvedEmpVal === 1 ? 'is-active' : '' }}" data-emp-type="1" data-row-offerid="{{ $row->offer_id ?? '' }}" title="Contract">Contract</button>
                                <button type="button" class="ot-emp-btn {{ $resolvedEmpVal === 2 ? 'is-active' : '' }}" data-emp-type="2" data-row-offerid="{{ $row->offer_id ?? '' }}" title="Freelancer">Freelancer</button>
                                <button type="button" class="ot-emp-btn {{ $resolvedEmpVal === 4 ? 'is-active' : '' }}" data-emp-type="4" data-row-offerid="{{ $row->offer_id ?? '' }}" title="Internship">Internship</button>
                            </div>
                        @else
                            <span class="ot-offer-badge" style="background:{{ $offerBadgeBg }};color:{{ $offerBadgeColor }};border:1px solid {{ $offerBadgeBorder }};" title="{{ $offerBadgeLabel }} — {{ $offerStatusText }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $offerIconPath }}"/></svg>
                                <span>{{ $offerBadgeLabel }}</span>
                            </span>
                        @endif
                    </div>

                    <!-- 3rd Row: Change or Update Action Cue -->
                    <div class="ot-cs-cue-row">
                        @if($isOwnJob)
                            <span class="ot-cs-cue">
                                <span>{{ $hasPendingOffer ? 'Create Offer' : 'Update Offer' }}</span>
                                <i class="fas fa-chevron-right ms-1"></i>
                            </span>
                        @else
                            <span class="ot-cs-cue locked" title="Client Mandate Offer">
                                <i class="fas fa-lock me-1"></i>Client Managed
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SUB-CARD 4: JOINING -->
            <div class="ot-subcard ot-subcard-joining">
                <div class="ot-subcard-head">
                    <span class="ot-subcard-label">Joining</span>
                    @if($joiningCountdown)
                        <span class="ot-micro-pill" style="background:{{ $joiningCountdownBg }};color:{{ $joiningCountdownColor }};">{{ $joiningCountdown }}</span>
                    @elseif($isJoined)
                        <span class="ot-micro-pill" style="background:#dcfce7;color:#15803d;">Joined</span>
                    @else
                        <span class="ot-micro-pill ot-pill-neutral">Pending</span>
                    @endif
                </div>
                <div class="ot-joining-body">
                    @if($joiningDate)
                        <div class="ot-join-date">
                            <i class="far fa-calendar-check"></i>
                            <span>{{ $joiningDate }}</span>
                        </div>
                        <div class="ot-join-time">{{ $joiningTime }}</div>
                    @else
                        <div class="ot-join-empty">—</div>
                        <div class="ot-join-time">{{ $isJoined ? 'Joined' : 'Not joined yet' }}</div>
                    @endif
                </div>
            </div>

            <!-- SUB-CARD 5: OWNERSHIP & RECRUITER (Reference Design Match) -->
            <div class="ot-subcard ot-subcard-owner">
                <div class="ot-subcard-head">
                    <span class="ot-subcard-label"><i class="fas fa-user-tie me-1"></i>Ownership &amp; Recruiter</span>
                </div>
                <div class="ot-owner-body">
                    <div class="ot-owner-name" title="{{ $ownerRecruiter !== '' ? e($ownerRecruiter) : 'Recruiter: —' }}">
                        <i class="fas fa-user-circle text-primary me-1"></i><span>{{ $ownerRecruiter !== '' ? $ownerRecruiter : '—' }}</span>
                    </div>
                    <div class="ot-owner-company" title="{{ $ownerCompany !== '' ? e($ownerCompany) : 'Company: —' }}">
                        <i class="fas fa-building me-1"></i><span>{{ $ownerCompany !== '' ? $ownerCompany : '—' }}</span>
                    </div>
                    <div class="ot-owner-submitted" title="Submitted: {{ $submittedAgoText }}">
                        <i class="far fa-calendar-alt me-1"></i><span>Submitted: {{ $submittedAgoText }}</span>
                    </div>
                </div>
            </div>

            <!-- SUB-CARD 6: PIPELINE & STAGE (Reference Design Match) -->
            <div class="ot-subcard ot-subcard-stage {{ $stageClass }}"
                 data-row-id="{{ $row->wiseId }}"
                 data-toggle="modal"
                 data-target="#{{ $stageClass }}"
                 role="button"
                 tabindex="0"
                 title="Click anywhere on this stage card to update candidate stage">
                <div class="ot-subcard-head">
                    <span class="ot-subcard-label">Pipeline &amp; Stage</span>
                </div>
                <div class="ot-stage-body">
                    <div class="ot-stage-trigger">
                        <span class="ot-stage-pill" style="background:{{ $stagePillBg }};color:{{ $stagePillColor }};border:1px solid {{ $stagePillBorder }};">
                            {{ $masterStatusName }}
                        </span>
                        @if($childStageName)
                            <span class="ot-substage-txt" title="Sub-Stage: {{ $childStageName }}">
                                Sub: <strong>{{ $childStageName }}</strong>
                            </span>
                        @else
                            <span class="ot-substage-txt">Sub: <strong>Standard</strong></span>
                        @endif
                        <span class="ot-stage-cue">Update Stage <i class="fas fa-chevron-right ms-1"></i></span>
                    </div>
                </div>
            </div>

        </div>
        <!-- End Upper 6 Sub-Cards Grid -->

        <!-- SUB-CARD 7 / BOTTOM STRIP: REFERENCE & PRIMARY ACTIONS -->
        <div class="ot-entity-bottom">
            <div class="ot-bottom-left">
                @if($offerStatusDate)
                    <div class="ot-bottom-item">
                        <span class="ot-bottom-lbl">Status Date:</span>
                        <span class="ot-bottom-muted">{{ $offerStatusDate }}</span>
                    </div>
                @endif
            </div>

            <div class="ot-bottom-right">
                @if($isOwnJob)
                    <button type="button"
                            class="ot-action-ghost create_offer"
                            data-row-offerid="{{ $row->offer_id ?? '' }}"
                            title="{{ $hasPendingOffer ? 'Create Offer' : 'Edit Offer Details' }}">
                        <i class="fas {{ $hasPendingOffer ? 'fa-plus' : 'fa-file-signature' }}"></i>
                        <span>{{ $hasPendingOffer ? 'Create Offer' : 'Manage Offer' }}</span>
                    </button>
                @endif
                <button type="button"
                        class="ot-action-primary {{ $profile_sidebar_css }}"
                        data-row-id="{{ $row->wiseId }}"
                        title="Open Candidate Profile Drawer">
                    <span>Candidate Profile</span>
                    <i class="fas fa-external-link-alt"></i>
                </button>
            </div>
        </div>

    </div>
    @endforeach
@else
    <div class="ot-empty-card">
        <div class="ot-empty-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
            </svg>
        </div>
        <div class="ot-empty-title">No Offers Found</div>
        <div class="ot-empty-sub">No offer records match your active filter, location, or search criteria.</div>
        <button type="button" class="ot-empty-reset-btn" onclick="window.jeroOfferTracker && window.jeroOfferTracker.resetFilters()">
            <i class="fas fa-undo-alt"></i> Reset All Filters
        </button>
    </div>
@endif
