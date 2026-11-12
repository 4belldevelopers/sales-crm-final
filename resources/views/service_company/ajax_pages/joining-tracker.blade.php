{{--
  JERO ATS — Service Company Joining Tracker Card Stream Partial (status_cus=6)
  Intent: Renders Elasticsearch joining-stage candidate projections as Rule 20 Modern
          Primary Cards with 5 discrete upper sub-cards (Candidate, Job & Client,
          Compensation, Joining Status, Joining Date) and a bottom metadata/action strip.
          Preserves all real ES status rules (of_joined, of_joined_date, of_offer_acceptance),
          confidentiality checks, duplicate indicators, bench badges, candidate profile drawer,
          and stage-change drawer triggers.
  Tenancy & Security: Strictly scoped by auth()->user()->company_id in controller and
                      pre-batched lookups; all outputs Blade-escaped.
  Screen Fit: Fractional minmax(0, ...fr) grid prevents horizontal overflow on 15.6" screens.
--}}
@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $company_id = auth()->user()->company_id;
    $today = strtotime(date('Y-m-d'));

    $job_status_arr   = GeneralMethod::job_status();
    $currencySymbols  = GeneralMethod::currency();
    $salaryModeOffer  = GeneralMethod::salaryModeOffer();

    $jobDetailsMap             = $jobDetailsMap ?? [];
    $allowedConfidentialJobIds = $allowedConfidentialJobIds ?? [];
    $spokUserMap               = $spokUserMap ?? [];
    $childStatusMap            = $childStatusMap ?? [];
    $cityDataMap               = $cityDataMap ?? [];
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;

            /* ── 1. Candidate Identity & Avatar ── */
            $fullNameRaw = trim((string) (@$row->full_name ?? 'Candidate'));
            $fullName    = ucwords(mb_substr($fullNameRaw ?: 'Candidate', 0, 32));
            $initials    = $fullNameRaw !== '' ? strtoupper(mb_substr($fullNameRaw, 0, 1)) : '?';
            $nameParts   = array_values(array_filter(explode(' ', $fullNameRaw)));
            if (count($nameParts) >= 2) {
                $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
            } elseif (count($nameParts) === 1 && mb_strlen($nameParts[0]) >= 2) {
                $initials = strtoupper(mb_substr($nameParts[0], 0, 2));
            }

            $candTone    = GeneralMethod::icoinText($fullNameRaw ?: '?');
            $isBench     = (@$row->condidate_type == 'bench_condidate');
            $designation = ucwords(mb_substr(trim((string) (@$row->current_designation ?? '')), 0, 30));

            $exp = trim((string) (@$row->total_exp ?? ''));
            if ($exp !== '' && strpos($exp, '.') !== false) {
                [$w, $d] = array_pad(explode('.', $exp), 2, '');
                $exp = ($d === '00') ? $w : $exp;
            }
            $expLabel = ($exp !== '' && $exp != '0') ? $exp . ' Yrs' : '';

            $candCity = trim((string) (@$row->city_name ?? ''));
            if ($candCity === '' && !empty($row->city_id) && isset($cityDataMap[$row->city_id])) {
                $candCity = (string) $cityDataMap[$row->city_id];
            }
            $candCountry  = trim((string) (@$row->country_name ?? ''));
            $candLocation = implode(', ', array_filter([ucwords($candCity), ucwords($candCountry)]));

            $duplicate     = $row->duplicate ?? 0;
            $duplicateFlag = ($duplicate == 1);

            /* ── 2. Job & Client Details ── */
            $jobId    = (int) (@$row->jobId ?? 0);
            $jobTitle = ucwords(mb_substr(trim((string) (@$row->title ?? 'Untitled Role')), 0, 36));
            $deptRaw  = $jobId > 0 ? (GeneralMethod::companyDetailsByClient($jobId) ?: (@$row->client_name ?? '')) : (@$row->client_name ?? '');
            $dept     = ucwords(mb_substr(trim((string) $deptRaw), 0, 38));

            $jobStatusRow = (isset($jobDetailsMap[$jobId]))
                ? $jobDetailsMap[$jobId]
                : ($jobId > 0 ? GeneralMethod::jobDetails($jobId) : null);
            $jobStatusRaw   = strtolower(trim((string) ($jobStatusRow->status ?? 'active')));
            $jobStatusLabel = match($jobStatusRaw) {
                'active'   => 'Hiring',
                'hold'     => 'Hold',
                'inactive' => 'Closed',
                default    => ucwords($jobStatusRaw ?: 'Hiring'),
            };
            $jobStatusPillClass = match($jobStatusRaw) {
                'active'   => 'jt-pill-hiring',
                'hold'     => 'jt-pill-hold',
                'inactive' => 'jt-pill-closed',
                default    => 'jt-pill-neutral',
            };

            $isInternalReq  = (((int) @$row->recommendation_id > 0 && @$row->client_comapny_id == $company_id) || @$row->client_comapny_id == $company_id);
            $scopeLabel     = $isInternalReq ? __('messages.internal_tag') : __('messages.clinet_tag');
            $scopePillClass = $isInternalReq ? 'jt-scope-internal' : 'jt-scope-direct';

            /* ── 3. Compensation (Honouring Confidentiality) ── */
            $employment_type = !empty($row->of_employment_type)
                ? ucwords((string) $row->of_employment_type)
                : ((($row->employment_type ?? 0) == 1) ? 'Contract' : 'Full-Time');
            $offer_currency = $currencySymbols[$row->of_offer_currency ? @$row->of_offer_currency : 0] ?? '₹';
            $offer_amount   = GeneralMethod::thousandsCurrencyFormat($row->of_offer_amount ? @$row->of_offer_amount : 0);
            $offer_mode_full = $salaryModeOffer[$row->of_offer_mode ? @$row->of_offer_mode : 0] ?? 'Yearly';
            $offer_mode_char = mb_substr($offer_mode_full, 0, 1);

            $isConfidentialMasked = false;
            if (@$row->confidential == 1) {
                $canShowConfidential = isset($allowedConfidentialJobIds[$jobId])
                    ? (!empty($allowedConfidentialJobIds[$jobId]))
                    : GeneralMethod::confidentialAmontShow($jobId);
                if ($canShowConfidential) {
                    $offerAmount = $offer_currency . ' ' . $offer_amount . ' / ' . $offer_mode_char;
                } else {
                    $offerAmount = 'Confidential';
                    $isConfidentialMasked = true;
                }
            } else {
                $offerAmount = $offer_currency . ' ' . $offer_amount . ' / ' . $offer_mode_char;
            }

            /* ── 4. Joining Status (Derived from Real ES Fields) ── */
            $joined     = (int) @$row->of_joined;
            $acceptance = (int) @$row->of_offer_acceptance;
            $jdRaw      = @$row->of_joined_date;
            $hasDate    = ($jdRaw && $jdRaw != '0000-00-00 00:00:00');
            $jdTs       = $hasDate ? strtotime($jdRaw) : null;
            $jdDay      = $hasDate ? strtotime(date('Y-m-d', $jdTs)) : null;
            $diffDays   = $hasDate ? (int) floor(($jdDay - $today) / 86400) : null;

            if ($joined === 1) {
                $stLabel = 'Joined';
                $stBg = '#dcfce7'; $stClr = '#16a34a'; $stBorder = '#bbf7d0'; $cardAccent = '#16a34a';
                $stSub = 'Joined Successfully';
                $stIcon = 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            } elseif ($acceptance === 3) {
                $stLabel = 'No Show';
                $stBg = '#fee2e2'; $stClr = '#dc2626'; $stBorder = '#fecaca'; $cardAccent = '#dc2626';
                $stSub = 'Candidate Unresponsive';
                $stIcon = 'm9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            } elseif ($hasDate && $diffDays === 0) {
                $stLabel = 'Joining Today';
                $stBg = '#ede9fe'; $stClr = '#7c3aed'; $stBorder = '#ddd6fe'; $cardAccent = '#7c3aed';
                $stSub = 'Confirmed';
                $stIcon = 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z';
            } elseif ($hasDate && $diffDays < 0) {
                $stLabel = 'Delayed';
                $stBg = '#ffedd5'; $stClr = '#ea580c'; $stBorder = '#fed7aa'; $cardAccent = '#ea580c';
                $stSub = 'Reschedule Pending';
                $stIcon = 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            } elseif ($acceptance === 1) {
                $stLabel = 'Pending Docs';
                $stBg = '#fef3c7'; $stClr = '#d97706'; $stBorder = '#fde68a'; $cardAccent = '#d97706';
                $stSub = 'Docs in Progress';
                $stIcon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z';
            } else {
                $stLabel = 'Pre-Joining';
                $stBg = '#d1fae5'; $stClr = '#059669'; $stBorder = '#a7f3d0'; $cardAccent = '#059669';
                $stSub = 'Docs in Progress';
                $stIcon = 'M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z';
            }

            /* ── 5. Joining Date Display & Relative Countdown ── */
            $dateMain = $timeMain = $relTxt = '';
            $relBg = '#f1f5f9'; $relClr = '#64748b';
            if ($hasDate) {
                $dateMain = date('d M Y', $jdTs);
                $timeMain = date('h:i A', $jdTs) . ' IST';
                if ($diffDays === 0)     { $relTxt = 'Today'; $relClr = '#6d28d9'; $relBg = '#ede9fe'; }
                elseif ($diffDays === 1) { $relTxt = 'Tomorrow'; $relClr = '#b45309'; $relBg = '#fef3c7'; }
                elseif ($diffDays > 1)   { $relTxt = 'In ' . $diffDays . ' Days'; $relClr = '#2563eb'; $relBg = '#eff6ff'; }
                else {
                    $ago = abs($diffDays);
                    if ($joined === 1) { $relTxt = $ago . ' Days Ago'; $relClr = '#15803d'; $relBg = '#dcfce7'; }
                }
            }

            /* ── 6. HR Ownership & Pipeline Stage ── */
            if ($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0) {
                $pairKey = $row->jobId . '_' . $row->ClientComID;
                if (isset($spokUserMap) && array_key_exists($pairKey, $spokUserMap)) {
                    $owner = $spokUserMap[$pairKey] ?: $row->posted_by;
                } else {
                    $spok = DB::table('job_assign')->select('users.name')
                        ->join('users', 'users.id', 'job_assign.spok_user_id')
                        ->where('job_assign.job_id', $row->jobId)
                        ->where('job_assign.company_id', $row->ClientComID)->first();
                    $owner = $spok ? $spok->name : $row->posted_by;
                }
                $owner = $row->rec_candidate_assign_id ? $row->posted_by : $owner;
            } else {
                $owner = $row->posted_by;
            }
            $owner        = trim((string) $owner);
            $ownerName    = ucwords(mb_substr($owner, 0, 22));
            $ownerCompany = ucwords(mb_substr(trim((string) (@$row->post_company_name ?: @$row->company_name)), 0, 24));

            $stageName  = ucwords(mb_substr((string) (@$row->name ?? 'Joined'), 0, 16));
            $stageChild = '';
            if (!empty(@$row->childStatus) && @$row->childStatus > 0) {
                if (isset($childStatusMap) && isset($childStatusMap[$row->childStatus])) {
                    $stageChild = ucwords(trim((string) $childStatusMap[$row->childStatus]));
                } else {
                    $childStatus = DB::table('candidate_status')->select('name')->where('id', $row->childStatus)->first();
                    if ($childStatus && !empty($childStatus->name)) {
                        $stageChild = ucwords(trim((string) $childStatus->name));
                    }
                }
            }

            $stageCss = 'stage_sub_stage';
            if (($row->thirdparty_company_id ?? 0) > 0 && $row->thirdparty_company_id == $company_id) {
                $stageCss = ((int) (@$row->job_master_status_id ?? 0) === 8) ? 'stage_sub_stage' : '';
            }
        @endphp

        <!-- Master Primary Joining Card (Rule 20 Card + Sub-Card System) -->
        <div class="jt-entity-card"
             data-wise-id="{{ @$row->wiseId }}"
             data-cand-name="{{ $fullName }}"
             data-cand-role="{{ $designation ?: '—' }}"
             data-job-title="{{ $jobTitle }}"
             data-job-id="{{ $jobId }}"
             data-client-name="{{ $dept ?: '—' }}"
             data-comp="{{ $offerAmount }}"
             data-joining-status="{{ $stLabel }}"
             data-joining-date="{{ $dateMain ?: 'Not scheduled' }}"
             data-owner="{{ $ownerName ?: '—' }}"
             data-stage="{{ $stageName }}{{ $stageChild ? ' (' . $stageChild . ')' : '' }}"
             style="border-left: 3.5px solid {{ $cardAccent }} !important;">

            <!-- Upper 5 Discrete Sub-Cards Grid -->
            <div class="jt-entity-grid">

                <!-- SUB-CARD 1: CANDIDATE IDENTITY -->
                <div class="jt-subcard jt-subcard-cand">
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
                        <a href="javascript:void(0);"
                           data-row-id="{{ @$row->wiseId }}"
                           class="show-application-detail {{ $duplicateFlag ? 'pulse pulse-danger' : '' }}"
                           style="text-decoration:none;flex-shrink:0;position:relative;">
                            @if(!empty($row->photo))
                                <img src="{{ $row->photo }}"
                                     class="jt-avatar-img"
                                     alt="{{ $initials }}"
                                     onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                                <div class="jt-avatar {{ $duplicateFlag ? 'bg-light-danger' : $candTone }}" style="display:none;">{{ $initials }}</div>
                            @else
                                <div class="jt-avatar {{ $duplicateFlag ? 'bg-light-danger' : $candTone }}">{{ $initials }}</div>
                            @endif
                            @if($duplicateFlag) <span class="pulse-ring"></span> @endif
                        </a>
                        <div class="jt-cand-text">
                            <a href="javascript:void(0);"
                               data-row-id="{{ @$row->wiseId }}"
                               class="jt-cand-name show-application-detail {{ $duplicateFlag ? 'text-danger' : '' }}"
                               title="{{ ucwords($fullNameRaw) }}">{{ $fullName }}</a>
                            <div class="jt-cand-role {{ $duplicateFlag ? 'text-danger' : '' }}" title="{{ ucwords((string) @$row->current_designation) }}">{{ $designation ?: '—' }}</div>
                            @if($candLocation)
                                <div class="jt-cand-loc" title="{{ $candLocation }}">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $candLocation }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 2: REQUISITION & CLIENT -->
                <div class="jt-subcard jt-subcard-job">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label">Requisition</span>
                        <div class="jt-subcard-badges">
                            <span class="jt-scope-pill {{ $scopePillClass }}">{{ $scopeLabel }}</span>
                            <span class="jt-job-status-pill {{ $jobStatusPillClass }}">{{ $jobStatusLabel }}</span>
                        </div>
                    </div>
                    <div class="jt-job-body">
                        <div class="jt-job-title" title="{{ @$row->title }}">{{ $jobTitle }}</div>
                        <div class="jt-job-meta">
                            @if($jobId > 0)
                                <span class="jt-job-id">#{{ $jobId }}</span>
                                <span class="jt-dot-sep">&bull;</span>
                            @endif
                            <span class="jt-job-client" title="{{ $deptRaw }}">{{ $dept ?: '—' }}</span>
                        </div>
                        <div class="jt-job-tags">
                            <span class="jt-tag-chip"><i class="fas fa-briefcase"></i> {{ $employment_type }}</span>
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 3: COMPENSATION -->
                <div class="jt-subcard jt-subcard-comp">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label">Compensation</span>
                        <span class="jt-micro-pill jt-pill-neutral">{{ $employment_type }}</span>
                    </div>
                    <div class="jt-comp-body">
                        <div class="jt-comp-amount" title="{{ $offerAmount }}">{{ $offerAmount }}</div>
                        <div class="jt-comp-sub">
                            @if($isConfidentialMasked)
                                <i class="fas fa-lock"></i>
                                <span>Restricted access</span>
                            @else
                                <i class="fas fa-coins"></i>
                                <span>{{ $offer_mode_full }} package</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SUB-CARD 4: JOINING STATUS -->
                <div class="jt-subcard jt-subcard-status">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label">Joining Status</span>
                        @if($joined === 1)
                            <span class="jt-micro-pill" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">Confirmed</span>
                        @endif
                    </div>
                    <div class="jt-status-body">
                        <span class="jt-joining-badge" style="background:{{ $stBg }};color:{{ $stClr }};border:1px solid {{ $stBorder }};">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $stIcon }}"/></svg>
                            <span>{{ $stLabel }}</span>
                        </span>
                        <span class="jt-joining-sub">{{ $stSub }}</span>
                    </div>
                </div>

                <!-- SUB-CARD 5: JOINING SCHEDULE -->
                <div class="jt-subcard jt-subcard-joining">
                    <div class="jt-subcard-head">
                        <span class="jt-subcard-label">Joining Date</span>
                        @if($relTxt)
                            <span class="jt-micro-pill" style="background:{{ $relBg }};color:{{ $relClr }};">{{ $relTxt }}</span>
                        @elseif(!$hasDate)
                            <span class="jt-micro-pill jt-pill-neutral">Pending</span>
                        @endif
                    </div>
                    <div class="jt-joining-body">
                        @if($hasDate)
                            <div class="jt-join-date">
                                <i class="far fa-calendar-check"></i>
                                <span>{{ $dateMain }}</span>
                            </div>
                            <div class="jt-join-time">{{ $timeMain }}</div>
                        @else
                            <div class="jt-join-empty">—</div>
                            <div class="jt-join-time">Not scheduled</div>
                        @endif
                    </div>
                </div>

            </div>
            <!-- End Upper 5 Sub-Cards Grid -->

            <!-- SUB-CARD 6: BOTTOM ACTIVITY, OWNERSHIP & QUICK ACTIONS STRIP -->
            <div class="jt-entity-bottom">
                <div class="jt-bottom-left">
                    <div class="jt-bottom-item">
                        <span class="jt-bottom-lbl">Owner:</span>
                        <strong class="jt-bottom-strong" title="{{ $owner }}">{{ $ownerName ?: '—' }}</strong>
                        @if($ownerCompany)
                            <span class="jt-bottom-muted">({{ $ownerCompany }})</span>
                        @endif
                    </div>
                    <span class="jt-dot-sep">&bull;</span>
                    <div class="jt-bottom-item">
                        <span class="jt-bottom-lbl">Stage:</span>
                        @if(!empty($stageCss))
                            <a href="javascript:void(0);"
                               data-row-id="{{ @$row->wiseId }}"
                               class="jt-stage-btn {{ $stageCss }}"
                               data-toggle="modal"
                               data-target="#{{ $stageCss }}"
                               title="Update candidate stage">{{ $stageName ?: 'Joined' }}</a>
                        @else
                            <span class="jt-stage-btn jt-stage-disabled">{{ $stageName ?: '—' }}</span>
                        @endif
                        @if($stageChild)
                            <span class="jt-substage-pill">{{ $stageChild }}</span>
                        @endif
                    </div>
                    @if(!empty($row->offer_id))
                        <span class="jt-dot-sep">&bull;</span>
                        <div class="jt-bottom-item">
                            <span class="jt-bottom-lbl">Ref:</span>
                            <span class="jt-bottom-muted">#OFF-{{ str_pad((string) $row->offer_id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    @endif
                </div>

                <div class="jt-bottom-right">
                    @if(!empty($stageCss))
                        <button type="button"
                                class="jt-action-ghost {{ $stageCss }}"
                                data-row-id="{{ @$row->wiseId }}"
                                data-toggle="modal"
                                data-target="#{{ $stageCss }}"
                                title="Update candidate stage">
                            <i class="fas fa-random"></i>
                            <span>Stage</span>
                        </button>
                    @endif
                    <button type="button"
                            class="jt-action-primary show-application-detail"
                            data-row-id="{{ @$row->wiseId }}"
                            title="Open Candidate Profile Drawer">
                        <span>Candidate Profile</span>
                        <i class="fas fa-external-link-alt"></i>
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
        <div class="jt-empty-sub">No candidates match your active filter, location, or search criteria.</div>
        <button type="button" class="jt-empty-reset-btn" onclick="window.jeroJoiningTracker && window.jeroJoiningTracker.resetFilters()">
            <i class="fas fa-undo-alt"></i> Reset All Filters
        </button>
    </div>
@endif
