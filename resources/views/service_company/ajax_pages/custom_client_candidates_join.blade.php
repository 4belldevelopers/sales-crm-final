@include('service_company.client.components.tab-offers-joinings-style')

@php
    use App\Helper\GeneralMethod;
    use App\Services\CurrencyService;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();

    $fmtDate = function($d) {
        if (empty($d) || date('d-m-Y', strtotime($d)) === '01-01-1970') return '';
        try {
            return Carbon::parse($d)->format('d M Y');
        } catch (\Throwable $e) {
            return '';
        }
    };
@endphp

@if (count($data) > 0)
    <div class="cd-oj-stream-list">
        @foreach ($data as $rowArr)
            @php
                $row = (object) $rowArr;
                $row->thirdparty_company_id = $row->ClientComID ?? 0;
                $wiseId = (int) ($row->wiseId ?? 0);

                // 1. Relational Offer Meta Lookup (Single Bulk Query from Controller)
                $meta = $offerMeta['offer_'.($row->offer_id ?? 0)] ?? ($offerMeta['app_'.($row->candidate_id ?? $row->id ?? 0)] ?? null);

                // 2. Stage Detection
                $masterStatusId = (int) (@$row->job_master_status_id ?? (@$row->master_status_id ?? 0));
                $isJoined = ($masterStatusId === 6 || in_array($masterStatusId, [6, '6'], true) || ($meta && $meta->joined == 1));

                // 3. Candidate Avatar & Name
                $fullName = trim((string) (@$row->full_name ?? 'Candidate'));
                $firstChar = !empty($fullName) ? strtolower($fullName[0]) : 'c';
                $charOrd = ord($firstChar);

                if ($charOrd >= 97 && $charOrd <= 101) { // a-e
                    $avatarClass = 'avatar-red';
                } elseif ($charOrd >= 102 && $charOrd <= 106) { // f-j
                    $avatarClass = 'avatar-orange';
                } elseif ($charOrd >= 107 && $charOrd <= 111) { // k-o
                    $avatarClass = 'avatar-blue';
                } elseif ($charOrd >= 112 && $charOrd <= 116) { // p-t
                    $avatarClass = 'avatar-green';
                } elseif ($charOrd >= 117 && $charOrd <= 122) { // u-z
                    $avatarClass = 'avatar-purple';
                } else {
                    $avatarClass = 'avatar-gray';
                }

                $initial = !empty($fullName) ? strtoupper($fullName[0]) : 'C';
                $designation = trim((string) (@$row->current_designation ?? '—'));
                $currentCompanyClean = trim((string) (@$row->current_company ?: (@$row->apl_company_name ?: '—')));
                if (strlen($currentCompanyClean) > 22) {
                    $currentCompanyClean = mb_substr($currentCompanyClean, 0, 22) . '...';
                }

                $profileSidebarCss = ($row->thirdparty_company_id > 0) ? 'client-show-application-detail' : 'show-application-detail';
                $duplicate = $row->duplicate ?? 0;

                // 4. Client Scope (Internal vs Direct)
                $isInternal = (@$row->client_comapny_id == ($company_id ?? auth()->user()->company_id) || empty($row->ClientComID) || $row->ClientComID == 0);
                $clientTypeLabel = $isInternal ? 'Internal Client' : 'Direct Client';
                $clientTypeCls   = $isInternal ? 'type-internal' : 'type-direct';

                // 5. Job Requisition Information
                $rawJobTitle = trim((string) (@$row->title ?? 'Untitled Job'));
                $jobId = @$row->jobId ?: (@$row->job_id ?: '—');
                $clientDisplayName = trim((string) (@$row->client_name ?: (@$row->clients_name ?: 'Client Account')));
                $jobStatus = !empty($row->job_status) ? ucwords(trim($row->job_status)) : 'Hiring';
                $employmentType = !empty($row->employmentType) ? ucwords($row->employmentType) : ((@$row->employment_type == 1) ? 'Contract' : 'Full-Time');

                $cityId = @$row->city_id;
                $locationCity = isset($citiesMap[$cityId]) ? $citiesMap[$cityId] : (!empty($row->city_name) ? $row->city_name : (!empty($row->city) ? $row->city : 'Work From Office'));
                if (empty($locationCity) || $locationCity === '----') {
                    $locationCity = 'Work From Office';
                }

                // 6. Notice Period
                $np = $row->np_id ?? '';
                if (empty($np)) {
                    $noticeText = 'Immediate';
                } elseif (@$row->serving_notice_period_mode == 0) {
                    $noticeText = $np . ' Days';
                } elseif (@$row->serving_notice_period_mode == 1) {
                    $noticeText = ($np != 'Immediate') ? GeneralMethod::ymdTodmyNotTime($np) : 'Immediate';
                } else {
                    $noticeText = $np;
                }

                // Experience
                $experience = trim((string) (@$row->total_exp ?? '0'));
                $formattedExperience = ($experience !== '' && $experience !== '0.') ? $experience : '0';

                // 7. Compensation Resolution (Rule 8: Global Compact K/M Formatting, strictly 0% Lakhs/Lacs/Crores)
                $offerAmount = !empty($meta->offer_amount) ? $meta->offer_amount : (!empty($meta->tds_salary_ctc) ? $meta->tds_salary_ctc : (!empty($row->of_offer_amount) ? $row->of_offer_amount : null));
                $hasOfferAmount = !empty($offerAmount) && (float)$offerAmount > 0;

                $currId = $meta->offer_currency ?? ($row->of_offer_currency ?? ($row->current_ctc_cur ?? 0));
                $currCode = CurrencyService::resolveCurrencyCode($currId);
                $currSym = CurrencyService::getCurrencySymbol($currCode);

                $modeKey = isset($meta->offer_mode) ? (int)$meta->offer_mode : (isset($row->of_salary_mode) ? (int)$row->of_salary_mode : (isset($row->current_ctc_mode) ? (int)$row->current_ctc_mode : 0));
                $salaryModeLabel = $salaryMode[$modeKey] ?? 'Annual';
                $modeSuffix = match($salaryModeLabel) {
                    'Monthly' => ' / mo',
                    'Hourly' => ' / hr',
                    'Weekly' => ' / wk',
                    'Daily' => ' / day',
                    default => ' / yr',
                };

                $fmtCompactComp = function($num) {
                    if (empty($num) || !is_numeric($num)) return null;
                    $val = (float) $num;
                    if ($val <= 0) return null;
                    if ($val < 100) {
                        $val = $val * 100000;
                    }
                    if ($val >= 1000000) {
                        $m = round($val / 1000000, 1);
                        return ($m == (int)$m ? (int)$m : $m) . 'M';
                    }
                    if ($val >= 1000) {
                        $k = round($val / 1000);
                        return $k . 'K';
                    }
                    return number_format($val);
                };

                if ($hasOfferAmount) {
                    $formattedComp = $fmtCompactComp($offerAmount);
                } else {
                    $formattedComp = null;
                }

                // Reference CTC when offer is pending (Global Compact Formatter)
                $curCtc = !empty($row->ctc) ? (float)$row->ctc : 0;
                $expCtc = !empty($row->ectc) ? (float)$row->ectc : 0;
                $refCtcParts = [];
                if ($expCtc > 0) {
                    $refCtcParts[] = 'Exp: ' . $currSym . $fmtCompactComp($expCtc);
                }
                if ($curCtc > 0) {
                    $refCtcParts[] = 'Cur: ' . $currSym . $fmtCompactComp($curCtc);
                }
                $refCtcStr = implode(' • ', $refCtcParts);

                // 8. Joining Schedule & Countdown
                $targetJoiningDate = !empty($meta->tds_joined_date) ? $meta->tds_joined_date : (!empty($meta->joined_date) ? $meta->joined_date : (!empty($row->of_joined_date) ? $row->of_joined_date : (!empty($row->joining_date) ? $row->joining_date : null)));
                $formattedJoinDate = 'Date Pending';
                $joinSubText = 'Notice: ' . $noticeText;
                $countdownHtml = '';

                if (!empty($targetJoiningDate) && date('d-m-Y', strtotime($targetJoiningDate)) !== '01-01-1970') {
                    try {
                        $joinCarbon = Carbon::parse($targetJoiningDate);
                        $formattedJoinDate = $joinCarbon->format('d M Y');
                        if (strlen($targetJoiningDate) > 10 && strpos($targetJoiningDate, ':') !== false) {
                            $joinSubText = $joinCarbon->format('g:i A (T)');
                        } else {
                            $joinSubText = '9:00 AM (IST)';
                        }
                        $today = Carbon::today();
                        $diffDays = $today->diffInDays($joinCarbon->copy()->startOfDay(), false);

                        if ($isJoined) {
                            $countdownHtml = '<span class="cd-oj-countdown-pill countdown-joined"><i class="fas fa-check-circle me-1"></i>Joined</span>';
                        } elseif ($diffDays > 1) {
                            $countdownHtml = '<span class="cd-oj-countdown-pill countdown-soon"><i class="far fa-clock me-1"></i>in ' . $diffDays . ' days</span>';
                        } elseif ($diffDays == 1) {
                            $countdownHtml = '<span class="cd-oj-countdown-pill countdown-tomorrow"><i class="far fa-clock me-1"></i>Tomorrow</span>';
                        } elseif ($diffDays == 0) {
                            $countdownHtml = '<span class="cd-oj-countdown-pill countdown-today"><i class="fas fa-bell me-1"></i>Joining Today</span>';
                        } else {
                            $countdownHtml = '<span class="cd-oj-countdown-pill countdown-overdue"><i class="fas fa-exclamation-triangle me-1"></i>Overdue (' . abs($diffDays) . 'd)</span>';
                        }
                    } catch (\Throwable $e) {
                        $formattedJoinDate = $targetJoiningDate;
                        $countdownHtml = '<span class="cd-oj-countdown-pill countdown-neutral"><i class="far fa-calendar me-1"></i>Date Set</span>';
                    }
                } else {
                    $formattedJoinDate = 'Date Pending';
                    $joinSubText = 'Notice: ' . $noticeText;
                    $countdownHtml = $isJoined 
                        ? '<span class="cd-oj-countdown-pill countdown-joined"><i class="fas fa-check-circle me-1"></i>Joined</span>'
                        : '<span class="cd-oj-countdown-pill countdown-neutral"><i class="far fa-calendar me-1"></i>Awaiting DOJ</span>';
                }

                // 9. Offer Lifecycle & Status Mapping (Direct Parity with Authoritative Offer Tracker)
                $stageDate = $fmtDate($row->of_stage_date ?? ($row->created_at ?? ''));
                if (empty($stageDate)) {
                    $stageDate = Carbon::now()->format('d M Y');
                }

                $offerAcceptance = (int)($meta->offer_acceptance ?? ($row->of_offer_acceptance ?? 0));
                $candStatus = isset($meta->candidate_status) ? (int)$meta->candidate_status : null;
                $offerRelease = (int)($meta->offer_release ?? ($row->of_offer_release ?? 0));
                $subForApproval = (int)($meta->sub_for_approval ?? ($row->sub_for_approval ?? 0));

                if ($isJoined) {
                    $statusPillClass = 'pill-joined';
                    $statusPillIcon = 'fas fa-check-circle';
                    $statusPillText = 'Joined &amp; Onboarded';
                    $statusSubtext = 'Joined: ' . $formattedJoinDate;
                    $substageBadgeText = 'Onboarded';
                    $substageBadgeClass = 'badge-joined';
                    $bottomDateLabel = 'Joined Date';
                    $bottomDateVal = $formattedJoinDate;
                } elseif ($offerAcceptance === 2 || $candStatus === 2) {
                    $statusPillClass = 'pill-accepted';
                    $statusPillIcon = 'fas fa-check';
                    $statusPillText = 'Offer Accepted';
                    $acceptedDate = $fmtDate($meta->accept_date_time ?? null) ?: $stageDate;
                    $statusSubtext = 'Accepted: ' . $acceptedDate;
                    $substageBadgeText = 'Candidate Accepted';
                    $substageBadgeClass = 'badge-accepted';
                    $bottomDateLabel = 'Accepted Date';
                    $bottomDateVal = $acceptedDate;
                } elseif ($offerAcceptance === 3 || $candStatus === 3) {
                    $statusPillClass = 'pill-rejected';
                    $statusPillIcon = 'fas fa-times-circle';
                    $statusPillText = 'Offer Declined';
                    $declinedDate = $fmtDate($meta->accept_date_time ?? null) ?: $stageDate;
                    $statusSubtext = 'Declined: ' . $declinedDate;
                    $substageBadgeText = 'Declined by Candidate';
                    $substageBadgeClass = 'badge-rejected';
                    $bottomDateLabel = 'Declined Date';
                    $bottomDateVal = $declinedDate;
                } elseif ($offerAcceptance === 4 || $candStatus === 4) {
                    $statusPillClass = 'pill-expired';
                    $statusPillIcon = 'fas fa-hourglass-end';
                    $statusPillText = 'Offer Expired';
                    $expiredDate = $fmtDate($meta->accept_date_time ?? null) ?: $stageDate;
                    $statusSubtext = 'Expired: ' . $expiredDate;
                    $substageBadgeText = 'Offer Expired';
                    $substageBadgeClass = 'badge-expired';
                    $bottomDateLabel = 'Expired Date';
                    $bottomDateVal = $expiredDate;
                } elseif ($offerRelease === 1 || $candStatus === 1) {
                    $statusPillClass = 'pill-offered';
                    $statusPillIcon = 'fas fa-paper-plane';
                    $statusPillText = 'Offer Released';
                    $releasedDate = $fmtDate($meta->send_accept_date_time ?? ($meta->send_approved_date_time ?? null)) ?: $stageDate;
                    $statusSubtext = 'Released: ' . $releasedDate;
                    $substageBadgeText = 'Offer Sent';
                    $substageBadgeClass = '';
                    $bottomDateLabel = 'Offer Released';
                    $bottomDateVal = $releasedDate;
                } elseif ($subForApproval === 1) {
                    $statusPillClass = 'pill-approval';
                    $statusPillIcon = 'far fa-clock';
                    $statusPillText = 'Approval Pending';
                    $approvalDate = $fmtDate($meta->send_approved_date_time ?? null) ?: $stageDate;
                    $statusSubtext = 'Submitted: ' . $approvalDate;
                    $substageBadgeText = 'Internal Approval';
                    $substageBadgeClass = '';
                    $bottomDateLabel = 'Approval Sent';
                    $bottomDateVal = $approvalDate;
                } elseif ($hasOfferAmount) {
                    $statusPillClass = 'pill-created';
                    $statusPillIcon = 'fas fa-file-alt';
                    $statusPillText = 'Offer Created';
                    $createdDate = $fmtDate($meta->salary_saved_at ?? null) ?: $stageDate;
                    $statusSubtext = 'Created: ' . $createdDate;
                    $substageBadgeText = 'Draft / Ready to Release';
                    $substageBadgeClass = '';
                    $bottomDateLabel = 'Draft Date';
                    $bottomDateVal = $createdDate;
                } else {
                    $statusPillClass = 'pill-pending';
                    $statusPillIcon = 'far fa-clock';
                    $statusPillText = 'Offer Pending';
                    $statusSubtext = 'Stage Date: ' . $stageDate;
                    $substageBadgeText = 'Offer Pending';
                    $substageBadgeClass = 'badge-pending';
                    $bottomDateLabel = 'Stage Date';
                    $bottomDateVal = $stageDate;
                }

                $stageDefaultCss = ($row->thirdparty_company_id > 0) ? 'client_stage_sub_stage' : 'stage_sub_stage';

                // 10. Ownership & Recruiter
                $recruiterName = !empty($row->posted_by) ? $row->posted_by : (!empty($row->submitted_by_name) ? $row->submitted_by_name : (!empty($row->spokperson_name) ? $row->spokperson_name : (!empty($row->upload_by_name) ? $row->upload_by_name : (@$row->source ?: 'Recruiter'))));
                $organizationName = @$row->submitted_by_company_name ?: (@$row->post_company_name ?: (@$row->uc_company_name ?: (@$row->from_company_name ?: (@$row->company_name ?: '—'))));
            @endphp

            <!-- Master Primary Card for Candidate Offer & Joining -->
            <div class="cd-oj-card" data-entity="candidate" data-candidate-id="{{ $wiseId }}">
                <!-- Upper Sub-Cards Grid (5 Discrete Enterprise Sub-Cards) -->
                <div class="cd-oj-card-grid">

                    <!-- Sub-Card 1: CANDIDATE DETAILS -->
                    <div class="cd-oj-subcard cd-subcard-candidate">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">Candidate Details</span>
                            <div class="cd-header-badges">
                                <span class="cd-badge-client-type {{ $clientTypeCls }}"><i class="fas fa-building me-1"></i>{{ $clientTypeLabel }}</span>
                                @if ($isJoined)
                                    <span class="cd-oj-stage-outline stage-joined"><i class="fas fa-check-circle me-1"></i>Joined</span>
                                @else
                                    <span class="cd-oj-stage-outline stage-offered"><i class="fas fa-tag me-1"></i>Offered</span>
                                @endif
                            </div>
                        </div>
                        <div class="cd-details-body">
                            <div class="cd-oj-avatar-box {{ $avatarClass }}">
                                {{ $initial }}
                            </div>
                            <div class="cd-details-text">
                                <a href="javascript:void(0);" 
                                   class="cd-oj-cand-name {{ $profileSidebarCss }} {{ $duplicate == 1 ? 'text-danger' : '' }}" 
                                   data-row-id="{{ $wiseId }}" 
                                   title="{{ ucwords($fullName) }}">
                                    {{ ucwords($fullName) }}
                                </a>
                                <div class="cd-oj-cand-role" title="{{ ucwords($designation) }}">
                                    {{ ucwords($designation) }}
                                </div>
                                <div class="cd-oj-cand-meta">
                                    <span class="cd-oj-cand-id">#{{ $wiseId }}</span>
                                    <span class="cd-bullet">&bull;</span>
                                    <span class="cd-oj-cand-employer" title="{{ $currentCompanyClean }}">{{ $currentCompanyClean }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 2: JOB REQUISITION -->
                    <div class="cd-oj-subcard cd-subcard-job">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">Job Requisition</span>
                            <span class="cd-status-badge status-hiring"><i class="fas fa-circle me-1" style="font-size:5px;"></i>{{ $jobStatus }}</span>
                        </div>
                        <div>
                            <div class="cd-oj-job-title" title="{{ ucwords($rawJobTitle) }}">
                                {{ ucwords($rawJobTitle) }}
                            </div>
                            <div class="cd-job-meta-row">
                                <span class="cd-job-id">#{{ $jobId }}</span>
                                <span class="cd-bullet">&bull;</span>
                                <span class="cd-job-client" title="{{ $clientDisplayName }}">{{ $clientDisplayName }}</span>
                            </div>
                            <div class="cd-job-emp-row">
                                <span class="cd-job-emp-type"><i class="fas fa-briefcase text-primary me-1"></i>{{ $employmentType }}</span>
                                <span class="cd-bullet">&bull;</span>
                                <span title="{{ $locationCity }}">{{ $locationCity }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 3: COMPENSATION -->
                    <div class="cd-oj-subcard cd-subcard-comp">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fas fa-money-bill-wave me-1 text-muted"></i>Compensation</span>
                            <a href="javascript:void(0);" data-row-id="{{ $wiseId }}" class="cd-oj-comp-change {{ $profileSidebarCss }}">
                                {{ $hasOfferAmount ? 'Change' : 'Set Offer' }}
                            </a>
                        </div>
                        <div>
                            @if ($hasOfferAmount)
                                <div class="cd-val-salary">
                                    <strong>{{ $currSym }}{{ $formattedComp }}{{ $modeSuffix }}</strong>
                                </div>
                                <div class="cd-val-sub">
                                    <i class="far fa-clock text-muted me-1"></i>{{ $employmentType }} &bull; {{ $salaryModeLabel }}
                                </div>
                            @else
                                <div class="cd-oj-comp-pending">
                                    <i class="far fa-clock me-1"></i>Offer Pending
                                </div>
                                @if (!empty($refCtcStr))
                                    <div class="cd-val-sub" title="Candidate Reference CTC">
                                        <i class="fas fa-wallet text-muted me-1"></i>{{ $refCtcStr }}
                                    </div>
                                @else
                                    <div class="cd-val-sub">
                                        <i class="far fa-clock text-muted me-1"></i>{{ $employmentType }}
                                    </div>
                                @endif
                            @endif
                            <div class="cd-val-sub">
                                <i class="fas fa-business-time text-muted me-1"></i>Notice: {{ $noticeText }}
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 4: OFFER STATUS -->
                    <div class="cd-oj-subcard cd-subcard-offer">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fas fa-file-contract me-1 text-muted"></i>Offer Status</span>
                        </div>
                        <div>
                            <div class="cd-oj-status-pill {{ $statusPillClass }}">
                                <i class="{{ $statusPillIcon }}"></i>
                                <span>{{ $statusPillText }}</span>
                            </div>
                            <div class="cd-oj-subtext">{{ $statusSubtext }}</div>
                            <span class="cd-oj-substage-badge {{ $substageBadgeClass }}">{{ $substageBadgeText }}</span>
                        </div>
                    </div>

                    <!-- Sub-Card 5: JOINING SCHEDULE -->
                    <div class="cd-oj-subcard cd-subcard-joining">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="far fa-calendar-check me-1 text-muted"></i>Joining Schedule</span>
                        </div>
                        <div>
                            <div class="cd-val-date">
                                <strong>{{ $formattedJoinDate }}</strong>
                            </div>
                            <div class="cd-val-sub">
                                <i class="far fa-clock text-muted me-1"></i>{{ $joinSubText }}
                            </div>
                            <div>
                                {!! $countdownHtml !!}
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sub-Card 6: Bottom Activity Strip (Recruiter, Offer Timeline & Stage Action) -->
                <div class="cd-oj-card-bottom">
                    <div class="cd-oj-bottom-left">
                        <span><i class="far fa-user text-muted me-1"></i>Recruiter: <strong>{{ $recruiterName }}</strong> ({{ $organizationName }})</span>
                        <span class="cd-bullet">&bull;</span>
                        <span><i class="far fa-clock text-muted me-1"></i>{{ $bottomDateLabel }}: <strong>{{ $bottomDateVal }}</strong></span>
                    </div>
                    <div class="cd-oj-bottom-right">
                        <a href="javascript:void(0);" 
                           data-row-id="{{ $wiseId }}" 
                           class="cd-oj-stage-action-btn {{ $stageDefaultCss }}" 
                           data-toggle="modal" 
                           data-target="#{{ $stageDefaultCss }}"
                           title="Update candidate stage">
                            <i class="fas fa-edit me-1"></i>Stage: <strong>{{ $isJoined ? 'Joined' : 'Offered' }}</strong>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="cd-oj-empty">
        <i class="fas fa-file-signature fa-2x mb-3 text-muted d-block"></i>
        <div class="fw-bold fs-6 text-gray-700 mb-1">No Candidate Offers or Joinings Found</div>
        <div class="text-muted fs-7">No candidates currently match the selected offer or joining stage for this client.</div>
    </div>
@endif
