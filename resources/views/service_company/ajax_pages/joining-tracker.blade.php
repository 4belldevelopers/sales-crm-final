{{--
  JERO ATS — Service Company Joining Tracker Card Stream Partial (status_cus=6)
  Intent: Renders Elasticsearch joining-stage candidate projections strictly adhering
          to the Modern Card + 6 Sub-Card Enterprise Design System (Rule 20), matching
          the Offer Tracker aesthetic and the user's reference design.
          Discrete Upper Sub-Cards:
            1. CANDIDATE (Avatar, Name, Role, Current Company, Experience Badge)
            2. JOB DETAILS (Title, #ID • Client, Canonical Rule 25 Scope Badge, Emp Tag)
            3. COMPENSATION (Amount, Real Joining Status Badge, Action Cue)
            4. JOINING (Date, Time with Tenant Timezone per Rule 24, Countdown Badge)
            5. OWNERSHIP & RECRUITER (Recruiter Name, Agency/Company, Relative Submission Age)
            6. PIPELINE & STAGE (Master Stage Pill, Sub-Stage Text, Update Stage Drawer Cue)
          Bottom Activity Strip:
            - Left: LAST UPDATED in Tenant Timezone (Rule 24)
            - Right: Manage Offer (for own jobs) + Candidate Profile Drawer CTA
  Tenancy & Security: Strictly scoped by auth()->user()->company_id in controller and
                      pre-batched MySQL lookups; all user outputs Blade-escaped.
  Screen Fit: Fractional minmax(0, ...fr) grid prevents horizontal overflow on 15.6" screens.
--}}
@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $company_id     = auth()->user()->company_id;
    $now            = \Carbon\Carbon::now();
    $tenantTimezone = GeneralMethod::defaultTimeZone($company_id) ?: 'Asia/Kolkata';

    $currencySymbols     = GeneralMethod::currency();
    $salaryModeOffer     = GeneralMethod::salaryModeOffer();
    $job_status_arr      = GeneralMethod::job_status();
    $employmentTypesList = GeneralMethod::employmentType();

    $jobDetailsMap             = $jobDetailsMap ?? [];
    $allowedConfidentialJobIds = $allowedConfidentialJobIds ?? [];
    $spokUserMap               = $spokUserMap ?? [];
    $childStatusMap            = $childStatusMap ?? [];
    $cityDataMap               = $cityDataMap ?? [];

    /* ── Batched Offer-Lifecycle Meta ──
       Fetched once for all candidates on page (not per row) to eliminate N+1 queries. */
    $offerIds = collect($data)->pluck('offer_id')->filter()->unique()->values()->all();
    $offerMeta = [];
    if (!empty($offerIds)) {
        $metaRows = DB::table('offer_job_applications as o')
            ->leftJoin('offer_create_main_tds as t', function ($j) {
                $j->on('t.offer_id', '=', 'o.id')->whereIn('t.status', [1, 3]);
            })
            ->whereIn('o.id', $offerIds)
            ->where('o.company_id', $company_id)
            ->select(
                'o.id as offer_id',
                'o.updated_at as salary_saved_at',
                't.updated_at as doc_updated_at',
                't.created_at as doc_created_at',
                't.send_approved_date_time',
                't.accept_date_time',
                't.send_accept_date_time'
            )
            ->get();
        foreach ($metaRows as $m) {
            $offerMeta[$m->offer_id] = $m;
        }
    }

    /* Rule 24: Default User/Account-Setting Timezone with Date, Time, and Timezone Abbreviation */
    $fmtDateTimeTz = function ($d) use ($tenantTimezone) {
        if (empty($d)) return '';
        $ts = strtotime($d);
        if (!$ts || date('d-m-Y', $ts) === '01-01-1970') return '';
        try {
            return \Carbon\Carbon::parse($d)->setTimezone($tenantTimezone)->format('d M Y, h:i A T');
        } catch (\Throwable $e) {
            return date('d M Y, h:i A', $ts) . ' ' . $tenantTimezone;
        }
    };
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;

            /* ── 1. Candidate Identity & Avatar ── */
            $fullNameRaw = trim((string) ($row->full_name ?? 'Candidate'));
            $fullName    = ucwords(mb_substr($fullNameRaw ?: 'Candidate', 0, 32));
            $initials    = $fullNameRaw !== '' ? strtoupper(mb_substr($fullNameRaw, 0, 1)) : '?';
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

            $photo       = $row->photo ?? '';
            $isBench     = (($row->condidate_type ?? '') === 'bench_condidate');
            $designation = ucwords(mb_substr(trim((string) ($row->current_designation ?? '')), 0, 30));

            $candCompanyRaw = trim((string) ($row->current_company ?? $row->cur_companyname ?? $row->cur_company_name ?? $row->apl_company_name ?? ''));
            if ($isBench && !empty($row->apl_company_name)) {
                $candCompanyRaw = trim((string) $row->apl_company_name);
            }
            $candCompany = ucwords($candCompanyRaw);

            $candCity = trim((string) ($row->city_name ?? ''));
            if ($candCity === '' && !empty($row->city_id) && isset($cityDataMap[$row->city_id])) {
                $candCity = (string) $cityDataMap[$row->city_id];
            }
            $candCountry  = trim((string) ($row->country_name ?? ''));
            $candLocation = implode(', ', array_filter([ucwords($candCity), ucwords($candCountry)]));

            $expRaw = trim((string) ($row->total_exp ?? ''));
            if ($expRaw !== '' && str_contains($expRaw, '.')) {
                [$expW, $expD] = array_pad(explode('.', $expRaw), 2, '');
                $expRaw = ($expD === '00') ? $expW : $expRaw;
            }
            $expLabel = ($expRaw !== '' && $expRaw != '0') ? ($expRaw . ' Yrs') : '';

            $duplicate     = (int) ($row->duplicate ?? 0);
            $duplicateFlag = ($duplicate === 1);

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
            } elseif ($resolvedSelfCompany === 0 || (!empty($resolvedClientId) && $resolvedSelfCompany !== 1 && !empty($clientNameTrim) && ($tenantCompanyName === '' || strcasecmp($clientNameTrim, $tenantCompanyName) !== 0))) {
                $targetTypeLabel = 'Internal Client';
                $targetTypeClass = 'type-internal';
                $targetTypeIcon  = 'fas fa-building';
            } else {
                $targetTypeLabel = 'My Organization';
                $targetTypeClass = 'type-my-org';
                $targetTypeIcon  = 'fas fa-id-badge';
            }
            $isOwnJob = ($targetTypeLabel !== 'Direct Client');

            /* ── 3. Compensation & Employment Type ── */
            $offerAmount    = $row->of_offer_amount ?? '';
            $offerJoinDate  = $row->of_joined_date ?? '';
            $currencyLabel  = $currencySymbols[$row->of_offer_currency ?? 0] ?? '₹';
            $modeFull       = $salaryModeOffer[$row->of_offer_mode ?? 0] ?? 'Yearly';
            $modeLabel      = mb_substr($modeFull, 0, 1);

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

            /* ── 4. Joining Schedule & Status Badge ── */
            $joined          = (int) ($row->of_joined ?? 0);
            $offerAcceptance = (int) ($row->of_offer_acceptance ?? 0);
            $meta            = $offerMeta[$row->offer_id ?? 0] ?? null;

            $joiningDate = $joiningTime = $joiningCountdown = $joiningCountdownBg = $joiningCountdownColor = '';
            $diffDays = null;
            if (!empty($offerJoinDate)) {
                $check = date("d-m-Y", strtotime($offerJoinDate));
                if ($check !== '01-01-1970') {
                    $jDate = \Carbon\Carbon::parse($offerJoinDate)->setTimezone($tenantTimezone);
                    $joiningDate = $jDate->format('d M Y');
                    $joiningTime = $jDate->format('h:i A T');
                    $diffDays    = (int) $now->diffInDays($jDate, false);
                    if ($diffDays > 0) {
                        $joiningCountdown = 'in ' . $diffDays . ' ' . ($diffDays === 1 ? 'day' : 'days');
                        if ($diffDays <= 7) {
                            $joiningCountdownColor = '#dc2626'; $joiningCountdownBg = '#fee2e2';
                        } elseif ($diffDays <= 14) {
                            $joiningCountdownColor = '#d97706'; $joiningCountdownBg = '#fef3c7';
                        } else {
                            $joiningCountdownColor = '#2563eb'; $joiningCountdownBg = '#eff6ff';
                        }
                    } elseif ($diffDays < 0) {
                        $joiningCountdown      = abs($diffDays) . 'd ago';
                        $joiningCountdownColor = '#64748b';
                        $joiningCountdownBg    = '#f1f5f9';
                    } else {
                        $joiningCountdown      = 'Today';
                        $joiningCountdownColor = '#15803d';
                        $joiningCountdownBg    = '#dcfce7';
                    }
                }
            }

            $rawMasterStatus = strtolower(trim((string) ($row->name ?? '')));
            $isCandidateJoined = ($joined === 1) || in_array($rawMasterStatus, ['joined', 'onboarded', 'accepted']);
            $isCandidateBackedOut = ($offerAcceptance === 3) || in_array($rawMasterStatus, ['backed out', 'backedout', 'rejected', 'declined']);

            /* Real status badge & card left accent (No Delayed or Reschedule state) */
            if ($isCandidateJoined) {
                $statusBadgeLabel  = 'Joined';
                $statusBadgeBg     = '#dcfce7'; $statusBadgeColor = '#15803d'; $statusBadgeBorder = '#bbf7d0';
                $cardAccentColor   = '#10b981';
                $statusIconPath    = 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            } elseif ($isCandidateBackedOut) {
                $statusBadgeLabel  = 'Backed Out';
                $statusBadgeBg     = '#fee2e2'; $statusBadgeColor = '#dc2626'; $statusBadgeBorder = '#fecaca';
                $cardAccentColor   = '#ef4444';
                $statusIconPath    = 'm9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            } elseif (!is_null($diffDays) && $diffDays === 0) {
                $statusBadgeLabel  = 'Joining Today';
                $statusBadgeBg     = '#ede9fe'; $statusBadgeColor = '#7c3aed'; $statusBadgeBorder = '#ddd6fe';
                $cardAccentColor   = '#7c3aed';
                $statusIconPath    = 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z';
            } elseif ($offerAcceptance === 1) {
                $statusBadgeLabel  = 'Pending Docs';
                $statusBadgeBg     = '#fef3c7'; $statusBadgeColor = '#d97706'; $statusBadgeBorder = '#fde68a';
                $cardAccentColor   = '#d97706';
                $statusIconPath    = 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z';
            } elseif (!empty($row->offer_id)) {
                $statusBadgeLabel  = 'Offer Created';
                $statusBadgeBg     = '#e0e7ff'; $statusBadgeColor = '#4338ca'; $statusBadgeBorder = '#c7d2fe';
                $cardAccentColor   = '#6366f1';
                $statusIconPath    = 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z';
            } else {
                $statusBadgeLabel  = 'Pre-Joining';
                $statusBadgeBg     = '#ecfdf5'; $statusBadgeColor = '#059669'; $statusBadgeBorder = '#a7f3d0';
                $cardAccentColor   = '#009ef7';
                $statusIconPath    = 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            }

            /* ── 5. Ownership & Recruiter ── */
            if ($company_id != ($row->ClientComID ?? 0) && ($row->ClientComID ?? 0) > 0) {
                $pairKey = $row->jobId . '_' . $row->ClientComID;
                if (isset($spokUserMap) && array_key_exists($pairKey, $spokUserMap)) {
                    $owner = $spokUserMap[$pairKey] ?: ($row->posted_by ?? '');
                } else {
                    $spok = DB::table('job_assign')->select('users.name')
                        ->join('users', 'users.id', 'job_assign.spok_user_id')
                        ->where('job_assign.job_id', $row->jobId)
                        ->where('job_assign.company_id', $row->ClientComID)->first();
                    $owner = $spok ? $spok->name : ($row->posted_by ?? '');
                }
                $owner = !empty($row->rec_candidate_assign_id) ? ($row->posted_by ?? '') : $owner;
            } else {
                $owner = $row->posted_by ?? '';
            }
            $ownerRecruiter = ucwords(mb_substr(trim((string) ($owner ?: ($row->posted_by ?? ''))), 0, 24));
            $ownerCompany   = $row->submitted_by_company_name ?? $row->post_company_name ?? $row->company_name ?? '';
            $ownerCompany   = ucwords(mb_substr(trim((string) $ownerCompany), 0, 26));

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

            /* ── 6. Pipeline & Stage ── */
            $masterStatusName = ucwords(mb_substr((string) ($row->name ?? 'Joined'), 0, 14));
            $childStageName   = !empty($row->childStatus) && isset($childStatusMap[$row->childStatus])
                ? ucwords(trim((string) $childStatusMap[$row->childStatus]))
                : '';

            $stageClass = 'stage_sub_stage';
            if (($row->thirdparty_company_id ?? 0) > 0 && $row->thirdparty_company_id == $company_id) {
                $stageClass = ((int) ($row->job_master_status_id ?? 0) === 8) ? 'stage_sub_stage' : '';
            }

            $stageTheme = match(strtolower(trim((string) ($row->name ?? '')))) {
                'joined', 'accepted'   => ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'],
                'offered'              => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a'],
                'inreview', 'review'   => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '#bfdbfe'],
                'interview'            => ['bg' => '#faf5ff', 'color' => '#7c3aed', 'border' => '#ddd6fe'],
                'rejected', 'declined' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'],
                default                => ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'],
            };
            $stagePillBg     = $stageTheme['bg'];
            $stagePillColor  = $stageTheme['color'];
            $stagePillBorder = $stageTheme['border'];

            /* Last Updated in Tenant Timezone */
            $rawStatusDate = $meta->salary_saved_at ?? $meta->doc_updated_at ?? $row->updated_at ?? $row->of_stage_date ?? $row->created_at ?? null;
            $lastUpdatedFormatted = $fmtDateTimeTz($rawStatusDate);
        @endphp

        <!-- Master Primary Joining Card (Rule 20 Modern Card + 6 Sub-Card System) -->
        <div class="jt-entity-card"
             data-wise-id="{{ $row->wiseId }}"
             data-offer-id="{{ $row->offer_id ?? '' }}"
             data-job-id="{{ $jobId }}"
             data-cand-name="{{ $fullName }}"
             data-cand-role="{{ $designation ?: '—' }}"
             data-job-title="{{ $jobTitle }}"
             data-client-name="{{ $clientName ?: '—' }}"
             data-comp="{{ $compDisplay }}"
             data-joining-status="{{ $statusBadgeLabel }}"
             data-joining-date="{{ $joiningDate ?: 'Not scheduled' }}"
             data-owner="{{ $ownerRecruiter ?: '—' }}"
             data-stage="{{ $masterStatusName }}{{ $childStageName ? ' (' . $childStageName . ')' : '' }}"
             style="border-left: 3.5px solid {{ $cardAccentColor }} !important;">

            <!-- Upper 6 Discrete Sub-Cards Grid (Rule 20 Design Standard) -->
            <div class="jt-entity-grid">

                <!-- SUB-CARD 1: CANDIDATE IDENTITY -->
                <div class="jt-subcard jt-subcard-cand show-application-detail jt-clickable-subcard"
                     data-row-id="{{ $row->wiseId }}"
                     role="button"
                     tabindex="0"
                     title="Click anywhere to view candidate profile">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label">Candidate</span>
                        <div class="jt-subcard-badges">
                            @if($isBench)
                                <span class="jt-micro-pill jt-pill-bench" title="Bench Candidate">Bench</span>
                            @endif
                            @if($expLabel)
                                <span class="jt-micro-pill jt-pill-neutral" title="Total Experience">{{ $expLabel }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="jt-cand-body">
                        @if(!empty($photo))
                            <img src="{{ $photo }}"
                                 class="jt-avatar-img show-application-detail"
                                 data-row-id="{{ $row->wiseId }}"
                                 alt="{{ $initials }}"
                                 onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                            <div class="jt-avatar show-application-detail"
                                 data-row-id="{{ $row->wiseId }}"
                                 style="display:none;background:{{ $avatarBg }};color:{{ $avatarColor }};">{{ $initials }}</div>
                        @else
                            <div class="jt-avatar show-application-detail"
                                 data-row-id="{{ $row->wiseId }}"
                                 style="background:{{ $avatarBg }};color:{{ $avatarColor }};">{{ $initials }}</div>
                        @endif
                        <div class="jt-cand-text">
                            <a href="javascript:void(0);"
                               data-row-id="{{ $row->wiseId }}"
                               class="jt-cand-name show-application-detail {{ $duplicateFlag ? 'text-danger' : '' }}"
                               title="{{ $fullName }}">{{ $fullName }}</a>
                            <div class="jt-cand-role" title="{{ $designation ?: 'Role not specified' }}">{{ $designation ?: '—' }}</div>
                            <div class="jt-cand-company" title="{{ $candCompany !== '' ? ('Current Company: ' . $candCompany) : ($candLocation ? ('Location: ' . $candLocation) : 'Current Company: Not specified') }}">
                                <i class="fas {{ $candCompany !== '' ? 'fa-building' : 'fa-map-marker-alt' }}"></i>
                                <span>{{ $candCompany !== '' ? $candCompany : ($candLocation ?: '—') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: JOB DETAILS -->
                <div class="jt-subcard jt-subcard-job">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label">Job Details</span>
                        <div class="jt-subcard-badges">
                            <span class="jt-scope-pill {{ $targetTypeClass }}">
                                <i class="{{ $targetTypeIcon }} me-1"></i>{{ $targetTypeLabel }}
                            </span>
                        </div>
                    </div>
                    <div class="jt-job-body">
                        <div class="jt-job-title" title="{{ $row->title ?? $jobTitle }}">{{ $jobTitle }}</div>
                        <div class="jt-job-meta">
                            @if($jobId > 0)
                                <span class="jt-job-id">#{{ $jobId }}</span>
                                <span class="jt-dot-sep">&bull;</span>
                            @endif
                            <span class="jt-job-client" title="{{ $clientName ?: 'Internal Requisition' }}">{{ $clientName ?: '—' }}</span>
                        </div>
                        <div class="jt-job-tags">
                            <span class="jt-tag-chip"><i class="fas fa-briefcase"></i> {{ $empLabel }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 3: COMPENSATION (View-Only per ATS Stage Standard) -->
                <div class="jt-subcard jt-subcard-comp" title="Agreed Compensation (View-Only)">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label">Compensation</span>
                        <span class="jt-micro-pill jt-pill-neutral" title="Employment Type: {{ $empLabel }}">{{ $empLabel }}</span>
                    </div>
                    <div class="jt-comp-status-body">
                        <!-- 1st Row: Amount -->
                        <div class="jt-cs-amount-row">
                            @if($isConfidential && $compDisplay === 'Confidential')
                                <span class="jt-cs-amount is-pending" title="Restricted Compensation Access">
                                    <i class="fas fa-lock me-1"></i>Confidential
                                </span>
                            @else
                                <span class="jt-cs-amount" title="Agreed Compensation: {{ $compDisplay }}">
                                    {{ $compDisplay }}
                                </span>
                            @endif
                        </div>

                        <!-- 2nd Row: Compensation Status Badge (View-Only) -->
                        <div class="jt-cs-status-row">
                            <span class="jt-status-badge"
                                  style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;"
                                  title="Offer Accepted & Finalized">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                <span>Offer Accepted</span>
                            </span>
                        </div>

                        <!-- 3rd Row: Package Descriptor (View-Only, No Offer Edit Button) -->
                        <div class="jt-comp-sub">
                            <i class="fas fa-coins text-warning me-1"></i>
                            <span>{{ $modeFull }} Package</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: JOINING SCHEDULE -->
                <div class="jt-subcard jt-subcard-joining">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label">Joining</span>
                        @if($isCandidateJoined)
                            <span class="jt-micro-pill" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">Joined</span>
                        @elseif($isCandidateBackedOut)
                            <span class="jt-micro-pill" style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;">No Show</span>
                        @elseif(!is_null($diffDays) && $diffDays === 0)
                            <span class="jt-micro-pill" style="background:#ede9fe;color:#7c3aed;border:1px solid #ddd6fe;">Today</span>
                        @elseif($joiningCountdown)
                            <span class="jt-micro-pill" style="background:{{ $joiningCountdownBg }};color:{{ $joiningCountdownColor }};border:1px solid #e2e8f0;">{{ $joiningCountdown }}</span>
                        @else
                            <span class="jt-micro-pill jt-pill-neutral">Not Scheduled</span>
                        @endif
                    </div>
                    <div class="jt-joining-body">
                        @if($joiningDate)
                            <div class="jt-join-date">
                                <i class="far fa-calendar-check"></i>
                                <span>{{ $joiningDate }}</span>
                            </div>
                            <div class="jt-join-time">
                                {{ $joiningTime }}
                            </div>
                        @else
                            <div class="jt-join-empty">—</div>
                            <div class="jt-join-time">{{ $isCandidateJoined ? 'Joined' : 'Not scheduled' }}</div>
                        @endif
                    </div>
                </div>

                <!-- SUB-CARD 5: OWNERSHIP & RECRUITER (Reference Design Match) -->
                <div class="jt-subcard jt-subcard-owner">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label"><i class="fas fa-user-tie me-1"></i>Ownership &amp; Recruiter</span>
                    </div>
                    <div class="jt-owner-body">
                        <div class="jt-owner-name" title="{{ $ownerRecruiter !== '' ? e($ownerRecruiter) : 'Recruiter: —' }}">
                            <i class="fas fa-user-circle text-primary me-1"></i><span>{{ $ownerRecruiter !== '' ? $ownerRecruiter : '—' }}</span>
                        </div>
                        <div class="jt-owner-company" title="{{ $ownerCompany !== '' ? e($ownerCompany) : 'Company: —' }}">
                            <i class="fas fa-building me-1"></i><span>{{ $ownerCompany !== '' ? $ownerCompany : '—' }}</span>
                        </div>
                        <div class="jt-owner-submitted" title="Submitted: {{ $submittedAgoText }}">
                            <i class="far fa-calendar-alt me-1"></i><span>Submitted: {{ $submittedAgoText }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 6: PIPELINE & STAGE (Reference Design Match) -->
                <div class="jt-subcard jt-subcard-stage {{ $stageClass }} jt-clickable-subcard"
                     data-row-id="{{ $row->wiseId }}"
                     role="button"
                     tabindex="0"
                     title="Click anywhere on this stage card to update candidate stage">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label">Pipeline &amp; Stage</span>
                    </div>
                    <div class="jt-stage-body">
                        <div class="jt-stage-trigger">
                            <span class="jt-stage-pill" style="background:{{ $stagePillBg }};color:{{ $stagePillColor }};border:1px solid {{ $stagePillBorder }};">
                                {{ $masterStatusName }}
                            </span>
                            @if($childStageName)
                                <span class="jt-substage-txt" title="Sub-Stage: {{ $childStageName }}">
                                    Sub: <strong>{{ $childStageName }}</strong>
                                </span>
                            @else
                                <span class="jt-substage-txt">Sub: <strong>Standard</strong></span>
                            @endif
                            <span class="jt-stage-cue">Update Stage <i class="fas fa-chevron-right ms-1"></i></span>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Upper 6 Sub-Cards Grid -->

            <!-- SUB-CARD 7 / BOTTOM STRIP: REFERENCE & PRIMARY ACTIONS -->
            <div class="jt-entity-bottom">
                <div class="jt-bottom-left">
                    @if(!empty($lastUpdatedFormatted))
                        <div class="jt-bottom-item">
                            <span class="jt-bottom-lbl">Last Updated:</span>
                            <span class="jt-bottom-muted">{{ $lastUpdatedFormatted }}</span>
                        </div>
                    @endif
                </div>

                <div class="jt-bottom-right">
                    <button type="button"
                            class="jt-action-primary show-application-detail"
                            data-row-id="{{ $row->wiseId }}"
                            title="Open Candidate Profile Drawer">
                        <span>Candidate Profile</span>
                        <i class="fas fa-external-link-alt ms-1"></i>
                    </button>
                </div>
            </div>

        </div>
    @endforeach
@else
    <div class="jt-empty-card">
        <div class="jt-empty-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
        </div>
        <div class="jt-empty-title">No Joining Records Found</div>
        <div class="jt-empty-sub">No candidates match your active filter, organization, or search criteria.</div>
        <button type="button" class="jt-empty-reset-btn" onclick="window.jeroJoiningTracker && window.jeroJoiningTracker.resetFilters()">
            <i class="fas fa-undo-alt"></i> Reset All Filters
        </button>
    </div>
@endif
