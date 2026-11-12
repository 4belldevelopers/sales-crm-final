{{--
  JERO ATS — Service Company Interview Tracker Card Stream Partial
  Intent: Renders Elasticsearch interview projections as Rule 20 Modern Primary Cards
          with 6 discrete upper sub-cards and a bottom activity/stage strip.
  Tenancy & Security: Strictly scoped by company_id in controller; all outputs Blade-escaped.
  Screen Fit: Fractional minmax(0, ...fr) grid prevents horizontal overflow on 15.6" screens.
--}}
@php
    use App\Helper\GeneralMethod;
    use App\Services\AppTrackerCardPresenter;
    use Carbon\Carbon;

    $feedback_status   = $feedback_status ?? GeneralMethod::feedback_status();
    $followupMap       = $followupMap ?? collect();
    $feedbackMap       = $feedbackMap ?? collect();
    $spokpersonMap     = $spokpersonMap ?? collect();
    $clientCompanyMap  = $clientCompanyMap ?? [];
    $jobMetaMap        = $jobMetaMap ?? [];
    $candidateMetaMap  = $candidateMetaMap ?? [];
    $userCaptainJobIds = $userCaptainJobIds ?? [];

    $panelPalette = [
        ['bg' => '#eff6ff', 'color' => '#2563eb'],
        ['bg' => '#f3e8ff', 'color' => '#7c3aed'],
        ['bg' => '#ecfdf5', 'color' => '#059669'],
        ['bg' => '#fff7ed', 'color' => '#ea580c'],
        ['bg' => '#fef2f2', 'color' => '#dc2626'],
        ['bg' => '#f0fdfa', 'color' => '#0d9488'],
    ];
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;

            // ── 1. Candidate Identity & Avatar ─────────────────────────────
            $fullNameRaw = trim((string) (@$row->full_name ?: 'Candidate'));
            $fullName    = ucwords($fullNameRaw);
            $firstChar   = strtolower(mb_substr($fullNameRaw, 0, 1) ?: 'c');
            $strVal      = strtoupper($firstChar);
            $ord         = ord($firstChar);
            if ($ord >= 97 && $ord <= 101) {
                $bgTextDecore = 'it-avatar-red';
            } elseif ($ord >= 102 && $ord <= 106) {
                $bgTextDecore = 'it-avatar-orange';
            } elseif ($ord >= 107 && $ord <= 111) {
                $bgTextDecore = 'it-avatar-blue';
            } elseif ($ord >= 112 && $ord <= 116) {
                $bgTextDecore = 'it-avatar-green';
            } elseif ($ord >= 117 && $ord <= 121) {
                $bgTextDecore = 'it-avatar-purple';
            } else {
                $bgTextDecore = 'it-avatar-gray';
            }

            $designation = ucwords(trim((string) (@$row->current_designation ?? '')));
            $wiseId      = (int) (@$row->wiseID ?? 0);
            $jobAppId    = (int) (@$row->job_application_id ?? 0);
            $candMeta    = ($wiseId > 0 && isset($candidateMetaMap['by_wise'][$wiseId]))
                ? $candidateMetaMap['by_wise'][$wiseId]
                : (($jobAppId > 0 && isset($candidateMetaMap['by_app'][$jobAppId])) ? $candidateMetaMap['by_app'][$jobAppId] : []);
            $candCurrentCompany = AppTrackerCardPresenter::cleanText(
                $candMeta['current_company'] ?? (@$row->current_company ?? (@$row->cur_companyname ?? ''))
            );
            $candCurrentCompany = $candCurrentCompany !== '' ? ucwords($candCurrentCompany) : '';
            $candEmail   = trim((string) (@$row->email ?? ''));
            $profile_detail = 'show-application-detail';

            // ── 2. Follow-Up Status ────────────────────────────────────────
            $fuRow    = $followupMap->get($row->id);
            $fuStatus = is_object($fuRow) ? ($fuRow->followup_status ?? null) : null;
            $fuAt     = is_object($fuRow) ? ($fuRow->followup_status_at ?? null) : null;
            $fuLabels = ['confirmed' => 'Confirmed', 'no-response' => 'No Response', 'rescheduled' => 'Rescheduled'];
            $fuLabel  = $fuLabels[$fuStatus] ?? 'Follow Up';
            $fuClass  = $fuStatus ? ('is-' . $fuStatus) : '';

            // ── 3. Requisition & Standardized Org Classification ──────────
            $jobId = (int) (@$row->job_id ?? @$row->jobId ?? 0);
            $jobMeta = $jobMetaMap[$jobId] ?? [];
            $client_title_name = ($jobMeta['client_name'] ?? '') ?: ($clientCompanyMap[$jobId] ?? (@$row->client_name ?? ''));
            $jobCompanyId = (int) (@$row->job_company_id ?? 0);
            $isDirectClient = ((int) (@$row->recommendation_id ?? 0) > 0 && (int) (@$row->to_company_id ?? 0) === (int) $company_id)
                || ($jobCompanyId > 0 && (int) $company_id > 0 && $jobCompanyId !== (int) $company_id);
            $selfCompVal = (int) ($jobMeta['self_company'] ?? (@$row->self_company ?? 0));

            if ($isDirectClient) {
                $jobTypeLabel = 'Direct Client';
                $jobTypeClass = 'it-scope-direct';
                $jobTypeIcon  = 'fa-handshake';
            } elseif ($selfCompVal === 1) {
                $jobTypeLabel = 'My Organization';
                $jobTypeClass = 'it-scope-myorg';
                $jobTypeIcon  = 'fa-building';
            } else {
                $jobTypeLabel = 'Internal Client';
                $jobTypeClass = 'it-scope-internal';
                $jobTypeIcon  = 'fa-sitemap';
            }

            $displayCompName = $isDirectClient
                ? (!empty($row->company_name) ? $row->company_name : (!empty($row->uc_company_name) ? $row->uc_company_name : $client_title_name))
                : ($client_title_name ?: (@$row->company_name ?? ''));
            $displayCompName = ucwords(trim((string) $displayCompName));
            $jobTitle = ucwords(trim((string) (@$row->job_title ?: 'Untitled Role')));
            $empTypeCode = (int) ($jobMeta['employment_type'] ?? (@$row->employment_type ?? 0));
            $employmentTypeLabel = $empTypeCode === 1 ? 'Contract' : ($empTypeCode === 3 ? 'Part Time' : 'Full-time');
            $roundLevelClean = trim((string) (@$row->mode_lavel_name ?? ''));
            $isRescheduledType = ((int) (@$row->interview_type ?? 1) === 2);

            // ── 4. Schedule Date, Time, Mode, Platform & Meeting Link ─────
            $scheduleCarbon = null;
            $dateFormatted = '—';
            try {
                if (!empty($row->schedule_date)) {
                    $scheduleCarbon = Carbon::parse($row->schedule_date);
                    $dateFormatted  = $scheduleCarbon->format('d M, Y');
                }
            } catch (\Throwable $e) {
                $dateFormatted = (string) (@$row->schedule_date ?? '—');
            }

            $timeDisplay = trim((string) (@$row->schedule_to_time ?: (@$row->schedule_from_time ?? '')));
            $tzDisplay   = trim((string) (@$row->timezone_abbreviation ?? ''));
            $timeFormatted = $timeDisplay ? ($timeDisplay . ($tzDisplay ? ' (' . $tzDisplay . ')' : '')) : '—';

            $modeId = (int) (@$row->interview_mode ?? 2);
            $modeLabel = ucwords(trim((string) (@$row->mode_name ?: ([1 => 'Telecon', 2 => 'Video', 3 => 'F2F', 4 => 'Other'][$modeId] ?? 'Video'))));
            if ($modeId === 1) {
                $modePillClass = 'it-mode-telecon';
                $modeFaIcon = 'fas fa-phone-alt';
            } elseif ($modeId === 3) {
                $modePillClass = 'it-mode-f2f';
                $modeFaIcon = 'fas fa-map-marker-alt';
            } elseif ($modeId === 2) {
                $modePillClass = 'it-mode-video';
                $modeFaIcon = 'fas fa-video';
            } else {
                $modePillClass = 'it-mode-other';
                $modeFaIcon = 'fas fa-calendar-check';
            }

            // Mode: 1=Telecon, 2=Video, 3=Face to Face
            $mLink = trim((string) (@$row->interview_mention_link ?? ''));
            $isUrl = (bool) preg_match('#^https?://#i', $mLink);
            $ilType = null;
            $ilLabel = '';
            if ($modeId === 3 && $mLink !== '') {
                $ilType = 'address';
                $ilLabel = 'View Address';
            } elseif ($isUrl) {
                $ilType = 'link';
                $ilLabel = 'Interview Link';
            }

            // ── 5. Interviewers / Panel ────────────────────────────────────
            $panelists = [];
            if (!empty($row->interviewers)) {
                $rawNames = preg_split('/\s*,\s*/', trim((string) $row->interviewers), -1, PREG_SPLIT_NO_EMPTY);
                foreach ($rawNames as $pName) {
                    $cleanName = ucwords(trim((string) $pName));
                    if ($cleanName !== '' && strtoupper($cleanName) !== 'N/A') {
                        $hash = abs(crc32(strtolower($cleanName)));
                        $panelists[] = [
                            'name'    => $cleanName,
                            'initial' => strtoupper(mb_substr($cleanName, 0, 1)),
                            'palette' => $panelPalette[$hash % count($panelPalette)],
                        ];
                    }
                }
            }
            $panelCount    = count($panelists);
            $panelCompany  = ucwords(trim((string) (@$row->uc_company_name ?: (@$row->company_name ?? ''))));

            // ── 6. Source (Recruiter / SPOC) ───────────────────────────────
            if ((int) $company_id !== (int) (@$row->ClientComID ?? 0) && (int) (@$row->ClientComID ?? 0) > 0) {
                $spokKey = ($row->jobId ?? $row->job_id ?? 0) . '_' . $row->ClientComID;
                $spokName = (is_object($spokpersonMap)) ? ($spokpersonMap->get($spokKey) ?? null) : null;
                $rowpostedby = $spokName ?: (@$row->posted_by ?? '');
                $rowpostedby = !empty($row->rec_candidate_assign_id) ? (@$row->posted_by ?? $rowpostedby) : $rowpostedby;
            } else {
                $rowpostedby = @$row->posted_by ?? '';
            }
            if (empty($rowpostedby)) {
                $rowpostedby = !empty($row->submitted_by_name)
                    ? $row->submitted_by_name
                    : (!empty($row->spokperson_name) ? $row->spokperson_name : (@$row->source ?: ''));
            }
            $post_company_name = !empty($row->submitted_by_company_name)
                ? $row->submitted_by_company_name
                : (!empty($row->post_company_name)
                    ? $row->post_company_name
                    : (!empty($row->from_company_name)
                        ? $row->from_company_name
                        : (!empty($row->uc_company_name) ? $row->uc_company_name : (@$row->company_name ?? ''))));

            $ownerRecruiterName = AppTrackerCardPresenter::cleanText(ucwords((string) $rowpostedby));
            $ownerCompanyName   = AppTrackerCardPresenter::cleanText(ucwords((string) $post_company_name));
            $submittedRaw       = AppTrackerCardPresenter::cleanText($candMeta['submitted_at'] ?? (@$row->submitted_at ?? (@$row->created_at ?? '')));
            $submittedAgo       = AppTrackerCardPresenter::resolveSubmittedAgo((object) ['created_at' => $submittedRaw]);

            // ── 7. Stage & Substage ────────────────────────────────────────
            $childStageName = '';
            if ((int) (@$row->master_status_id ?? 0) === 8) {
                $childStageName = 'Private';
            }
            if (!empty($row->child_status_id) && (int) $row->child_status_id > 0) {
                $childStageName = (string) (@$row->child_status_name ?? '');
            }
            $masterStatusName = trim((string) (@$row->master_status_name ?: 'In Review'));
            $stageNameLower   = strtolower($masterStatusName);
            if (str_contains($stageNameLower, 'review') || str_contains($stageNameLower, 'inw')) {
                $stageBadgeClass = 'it-stage-inw';
            } elseif (str_contains($stageNameLower, 'sourc') || str_contains($stageNameLower, 'sou')) {
                $stageBadgeClass = 'it-stage-sou';
            } elseif (str_contains($stageNameLower, 'screen') || str_contains($stageNameLower, 'scr')) {
                $stageBadgeClass = 'it-stage-scr';
            } elseif (str_contains($stageNameLower, 'evaluat') || str_contains($stageNameLower, 'eva')) {
                $stageBadgeClass = 'it-stage-eva';
            } elseif (str_contains($stageNameLower, 'negot') || str_contains($stageNameLower, 'neg')) {
                $stageBadgeClass = 'it-stage-neg';
            } elseif (str_contains($stageNameLower, 'offer') || str_contains($stageNameLower, 'off')) {
                $stageBadgeClass = 'it-stage-off';
            } elseif (str_contains($stageNameLower, 'join') || str_contains($stageNameLower, 'joi')) {
                $stageBadgeClass = 'it-stage-joi';
            } elseif (str_contains($stageNameLower, 'reject') || str_contains($stageNameLower, 'rej')) {
                $stageBadgeClass = 'it-stage-rej';
            } else {
                $stageBadgeClass = 'it-stage-eva';
            }

            // ── 8. Feedback, Attendance & Permissions ──────────────────────
            $latestFb       = $feedbackMap->get($row->id) ?? null;
            $hasAnyFeedback = !empty($latestFb);
            $attId          = (int) (@$latestFb->available_status ?? 0); // 1=Attended

            $attLabelMap = [
                1 => 'Attended',
                2 => 'No Show',
                3 => 'Rescheduled',
                4 => 'Cancelled by Company',
                6 => 'Other Reason',
                7 => 'Cancelled by Candidate',
            ];

            $resultLabels = [
                'selected'         => 'Selected',
                'hold'             => 'Hold',
                'rejected'         => 'Rejected',
                'fake_proxy'       => 'Fake / Proxy',
                'feedback_pending' => 'Feedback Pending',
            ];
            $resultKey  = @$latestFb->interview_result;
            $resultText = '';
            if (!empty($resultKey) && isset($resultLabels[$resultKey])) {
                $resultText = $resultLabels[$resultKey];
            } elseif (!empty($row->feedback) && isset($feedback_status[$row->feedback])) {
                $resultText = ucwords((string) $feedback_status[$row->feedback]['name']);
            }
            $legacyFb = (int) (@$row->feedback ?? 0); // 1=Selected,2=Hold,3=Rejected,4=Fake,5=Feedback Pending
            $resultColor = '#10b981';
            if (in_array($resultKey, ['rejected', 'fake_proxy'], true) || in_array($legacyFb, [3, 4], true)) {
                $resultColor = '#ef4444';
            } elseif (in_array($resultKey, ['hold', 'feedback_pending'], true) || in_array($legacyFb, [2, 5], true)) {
                $resultColor = '#d97706';
            }

            $recLabels = [
                'strong_hire'    => 'Strong Hire',
                'hire'           => 'Hire',
                'consider'       => 'Consider',
                'no_hire'        => 'No Hire',
                'strong_no_hire' => 'Strong No Hire',
            ];
            $recKey  = @$latestFb->overall_recommendation;
            $recText = (!empty($recKey) && isset($recLabels[$recKey])) ? $recLabels[$recKey] : '';
            $recBg = '#eff6ff'; $recColor = '#2563eb';
            if (in_array($recKey, ['strong_hire', 'hire'], true)) {
                $recBg = '#dcfce7'; $recColor = '#15803d';
            } elseif (in_array($recKey, ['no_hire', 'strong_no_hire'], true)) {
                $recBg = '#fee2e2'; $recColor = '#b91c1c';
            } elseif ($recKey === 'consider') {
                $recBg = '#fef3c7'; $recColor = '#b45309';
            }

            $attended    = ($attId === 1);
            $notAttended = ($hasAnyFeedback && $attId > 0 && $attId !== 1);

            // Permissions (pre-hydrated check preserved 100%)
            $canAddFeedback = true;
            $customHtml     = 'show-feedback';
            if ((int) (@$row->thirdparty_company_id ?? 0) === (int) $company_id) {
                $customHtml = '';
                $canAddFeedback = false;
            }
            if ((int) (@$row->to_company_id ?? 0) === (int) $company_id) {
                $customHtml = '';
                $canAddFeedback = false;
            }
            $allowChk = isset($userCaptainJobIds[@$row->jobId]) ? 1 : 0;
            $isInterviewerOnRow = false;
            $myNameChk = strtolower(trim(auth()->user()->name ?? ''));
            if ($myNameChk !== '' && !empty($row->interviewers)) {
                foreach (preg_split('/\s*,\s*/', (string) $row->interviewers) as $intvName) {
                    if (strtolower(trim($intvName)) === $myNameChk) {
                        $isInterviewerOnRow = true;
                        break;
                    }
                }
            }
            if ((int) $chkAdminRole !== 1) {
                if ($allowChk === 0 && !$isInterviewerOnRow) {
                    $customHtml = 'notAllow-feedback';
                    $canAddFeedback = false;
                }
            }

            // Attendance pill for Evaluation sub-card header
            if ($attended) {
                $evalHeaderPillText  = 'Attended';
                $evalHeaderPillClass = 'it-eval-attended';
            } elseif ($notAttended) {
                $evalHeaderPillText  = $attId === 3 ? 'Rescheduled' : ($attId === 2 ? 'No Show' : 'Not Attended');
                $evalHeaderPillClass = ($attId === 3) ? 'it-eval-rescheduled' : 'it-eval-noshow';
            } else {
                $evalHeaderPillText  = ($scheduleCarbon && $scheduleCarbon->isFuture()) ? 'Scheduled' : 'Pending';
                $evalHeaderPillClass = ($scheduleCarbon && $scheduleCarbon->isFuture()) ? 'it-eval-scheduled' : 'it-eval-pending';
            }

            // Card Left Accent Border Color
            if ($attended && ($resultKey === 'selected' || $legacyFb === 1)) {
                $cardAccentColor = '#10b981'; // Green — Selected
            } elseif (in_array($resultKey, ['rejected', 'fake_proxy'], true) || in_array($legacyFb, [3, 4], true) || in_array($attId, [4, 7], true)) {
                $cardAccentColor = '#ef4444'; // Red — Rejected / Cancelled
            } elseif ($notAttended || in_array($resultKey, ['hold', 'feedback_pending'], true) || in_array($legacyFb, [2, 5], true) || (!$attended && $scheduleCarbon && $scheduleCarbon->isPast())) {
                $cardAccentColor = '#f59e0b'; // Amber — Pending / Hold / No Show
            } else {
                $cardAccentColor = '#009ef7'; // Primary Blue — Scheduled / Upcoming
            }

            $starRating = (float) (@$row->average_star_rating ?? 0);
        @endphp

        <!-- Master Primary Interview Card (Rule 20 Card + Sub-Card System) -->
        <div class="it-card" data-interview-id="{{ $row->id }}" data-wise-id="{{ @$row->wiseID }}" data-job-id="{{ $jobId }}" data-schedule-ts="{{ $scheduleCarbon ? $scheduleCarbon->timestamp : 0 }}" data-created-ts="{{ !empty($row->created_at) ? (int) strtotime((string) $row->created_at) : 0 }}" data-mode-id="{{ (int) $modeId }}" style="border-left: 3.5px solid {{ $cardAccentColor }} !important;">

            <!-- Upper Discrete Sub-Cards Grid (6 Responsive Enterprise Sub-Cards) -->
            <div class="it-card-grid">

                <!-- Sub-Card 1: CANDIDATE -->
                <div class="it-subcard it-subcard-candidate">
                    <div class="it-subcard-header">
                        <span class="it-subcard-label">Candidate</span>
                        <div class="it-followup-dropdown" data-fid="{{ $row->id }}">
                            <button class="it-followup-btn it-followup-trigger {{ $fuClass }}" onclick="toggleFollowUp(this)" type="button" title="Update candidate follow-up status">
                                @if(!$fuStatus)
                                    <i class="far fa-comment-dots fu-cta-icon"></i>
                                @endif
                                <span class="fu-label">{{ $fuLabel }}</span>
                                <i class="fas fa-chevron-down it-fu-caret"></i>
                            </button>
                            <div class="it-followup-menu">
                                <div class="it-followup-item" onclick="setFollowUpStatus(this, {{ $row->id }}, 'confirmed')">
                                    <i class="fas fa-check text-success"></i>
                                    <span>Confirmed</span>
                                </div>
                                <div class="it-followup-item" onclick="setFollowUpStatus(this, {{ $row->id }}, 'no-response')">
                                    <i class="fas fa-times text-danger"></i>
                                    <span>No Response</span>
                                </div>
                                <div class="it-followup-item" onclick="setFollowUpStatus(this, {{ $row->id }}, 'rescheduled')">
                                    <i class="fas fa-redo text-primary"></i>
                                    <span>Rescheduled</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="it-cand-body">
                        <div class="it-avatar {{ $bgTextDecore }} {{ $profile_detail }}" data-row-id="{{ @$row->wiseID }}" title="View candidate profile">
                            {{ $strVal }}
                        </div>
                        <div class="it-cand-text">
                            <a href="javascript:void(0);" data-row-id="{{ @$row->wiseID }}" class="it-cand-name {{ $profile_detail }}" title="{{ $fullName }}">
                                {{ $fullName }}
                            </a>
                            <div class="it-cand-role {{ $profile_detail }}" data-row-id="{{ @$row->wiseID }}" title="{{ $designation ?: '—' }}">
                                {{ $designation ?: '—' }}
                            </div>
                            <div class="it-cand-submeta it-cand-company" title="{{ $candCurrentCompany !== '' ? $candCurrentCompany : ($candEmail !== '' ? $candEmail : 'Company: –') }}">
                                @if($candCurrentCompany !== '')
                                    <i class="fas fa-building"></i>
                                    <span>{{ $candCurrentCompany }}</span>
                                @elseif($candEmail !== '')
                                    <i class="far fa-envelope"></i>
                                    <span>{{ $candEmail }}</span>
                                @else
                                    <i class="fas fa-building"></i>
                                    <span>–</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 2: REQUISITION -->
                <div class="it-subcard it-subcard-job">
                    <div class="it-subcard-header">
                        <span class="it-subcard-label">Requisition</span>
                        <span class="it-scope-badge {{ $jobTypeClass }}">
                            <i class="fas {{ $jobTypeIcon }}"></i>
                            {{ $jobTypeLabel }}
                        </span>
                    </div>
                    <div class="it-job-body">
                        <a href="javascript:void(0);"
                           class="it-job-title js-it-open-jd"
                           data-job-id="{{ $jobId }}"
                           data-direct="{{ $isDirectClient ? '1' : '0' }}"
                           title="{{ $jobTitle }}">
                            {{ $jobTitle }}
                        </a>
                        <div class="it-job-meta">
                            <a href="javascript:void(0);"
                               class="it-job-id js-it-open-jd"
                               data-job-id="{{ $jobId }}"
                               data-direct="{{ $isDirectClient ? '1' : '0' }}">#{{ $jobId }}</a>
                            <span class="it-dot-sep">&bull;</span>
                            <span class="it-job-client" title="{{ $displayCompName }}">{{ $displayCompName ?: '—' }}</span>
                        </div>
                        <div class="it-job-pills">
                            <span class="it-emp-pill" title="Employment Type">
                                <i class="fas fa-briefcase"></i>
                                <span>{{ $employmentTypeLabel }}</span>
                            </span>
                            @if($isRescheduledType)
                                <span class="it-resched-tag"><i class="fas fa-history"></i> Rescheduled</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 3: SCHEDULE & LINK -->
                <div class="it-subcard it-subcard-schedule">
                    <div class="it-subcard-header">
                        <span class="it-subcard-label">Schedule</span>
                        <span class="it-mode-pill {{ $modePillClass }}">
                            <i class="{{ $modeFaIcon }}"></i>
                            {{ $modeLabel }}
                        </span>
                    </div>
                    <div class="it-sched-body">
                        <div class="it-sched-date-row">
                            <span class="it-sched-date">
                                <i class="far fa-calendar-alt"></i>
                                <span>{{ $dateFormatted }}</span>
                            </span>
                        </div>
                        <div class="it-sched-time-row">
                            <i class="far fa-clock"></i>
                            <span>{{ $timeFormatted }}</span>
                        </div>
                        @if($ilType)
                            <a href="javascript:void(0);"
                               class="it-link-pill open-interview-link"
                               data-link="{{ $mLink }}"
                               data-id="{{ $row->id }}"
                               data-name="{{ $fullName }}"
                               data-type="{{ $ilType }}"
                               title="{{ $mLink }}">
                                <i class="fas {{ $ilType === 'address' ? 'fa-map-marked-alt' : 'fa-link' }}"></i>
                                <span>{{ $ilLabel }}</span>
                            </a>
                        @else
                            <div class="it-no-link-note">No link attached</div>
                        @endif
                    </div>
                </div>

                <!-- Sub-Card 4: INTERVIEW PANEL -->
                <div class="it-subcard it-subcard-panel">
                    <div class="it-subcard-header">
                        <span class="it-subcard-label">Panelists</span>
                        <span class="it-panel-count {{ $panelCount === 0 ? 'is-empty' : '' }}">
                            {{ $panelCount > 0 ? ($panelCount . ' ' . ($panelCount === 1 ? 'Panelist' : 'Panelists')) : '—' }}
                        </span>
                    </div>
                    @if($panelCount === 0)
                        <div class="it-panel-empty">
                            <span class="it-empty-dash">—</span>
                            <span class="it-empty-sub">Not Assigned</span>
                        </div>
                    @elseif($panelCount === 1)
                        @php $p = $panelists[0]; @endphp
                        <div class="it-panel-single" title="{{ $p['name'] }}{{ $panelCompany ? ' (' . $panelCompany . ')' : '' }}">
                            <div class="it-panel-avatar" style="background: {{ $p['palette']['bg'] }}; color: {{ $p['palette']['color'] }};">
                                {{ $p['initial'] }}
                            </div>
                            <div class="it-panel-meta">
                                <div class="it-panel-name">{{ $p['name'] }}</div>
                                <div class="it-panel-company">{{ $panelCompany ?: '—' }}</div>
                            </div>
                        </div>
                    @else
                        @php
                            $visiblePanelists = array_slice($panelists, 0, 3);
                            $remainingCount = $panelCount - 3;
                            $lead = $panelists[0];
                            $extraCount = $panelCount - 1;
                        @endphp
                        <div class="it-panel-multi">
                            <div class="it-panel-stack">
                                @foreach($visiblePanelists as $p)
                                    <div class="it-panel-avatar-sm" style="background: {{ $p['palette']['bg'] }}; color: {{ $p['palette']['color'] }};" title="{{ $p['name'] }}">
                                        {{ $p['initial'] }}
                                    </div>
                                @endforeach
                                @if($remainingCount > 0)
                                    <div class="it-panel-avatar-sm it-panel-avatar-extra" title="+{{ $remainingCount }} more">+{{ $remainingCount }}</div>
                                @endif
                            </div>
                            <div class="it-panel-meta">
                                <div class="it-panel-name" title="{{ $lead['name'] }} + {{ $extraCount }} more">
                                    {{ $lead['name'] }} <span class="it-panel-more">+{{ $extraCount }}</span>
                                </div>
                                <div class="it-panel-company">{{ $panelCompany ?: '—' }}</div>
                            </div>
                            <div class="it-panel-popover">
                                <div class="it-panel-popover-head">
                                    <span>Interview Panel</span>
                                    <span>{{ $panelCount }}</span>
                                </div>
                                @foreach($panelists as $idx => $p)
                                    <div class="it-panel-popover-row">
                                        <span class="it-panel-pop-dot" style="background: {{ $p['palette']['bg'] }}; color: {{ $p['palette']['color'] }};">{{ $p['initial'] }}</span>
                                        <span class="it-panel-pop-name">{{ $p['name'] }}</span>
                                        @if($idx === 0)
                                            <span class="it-panel-lead-tag">Lead</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sub-Card 5: OWNERSHIP & RECRUITER -->
                <div class="it-subcard it-subcard-owner">
                    <div class="it-subcard-header">
                        <span class="it-subcard-label"><i class="fas fa-user-tie"></i> Ownership &amp; Recruiter</span>
                    </div>
                    <div class="it-owner-body">
                        <div class="it-owner-name" title="{{ $ownerRecruiterName !== '' ? $ownerRecruiterName : 'Recruiter: –' }}">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ $ownerRecruiterName !== '' ? $ownerRecruiterName : '–' }}</span>
                        </div>
                        <div class="it-owner-company" title="{{ $ownerCompanyName !== '' ? $ownerCompanyName : 'Company: –' }}">
                            <i class="fas fa-building"></i>
                            <span>{{ $ownerCompanyName !== '' ? $ownerCompanyName : '–' }}</span>
                        </div>
                        <div class="it-owner-submitted" title="{{ $submittedAgo !== '' ? 'Submitted: ' . $submittedAgo : 'Submitted: –' }}">
                            <i class="far fa-calendar-alt"></i>
                            <span>Submitted: {{ $submittedAgo !== '' ? $submittedAgo : '–' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 6: EVALUATION & FEEDBACK -->
                <div class="it-subcard it-subcard-feedback">
                    <div class="it-subcard-header">
                        <span class="it-subcard-label">Evaluation</span>
                        <span class="it-eval-pill {{ $evalHeaderPillClass }}">{{ $evalHeaderPillText }}</span>
                    </div>
                    <div class="it-feedback-cell">
                        @if($attended)
                            <div class="it-fb-status-row" style="color: {{ $resultColor }};">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ $resultText ?: 'Feedback Pending' }}</span>
                            </div>
                            <div class="it-fb-badges-row">
                                @if($recText)
                                    <span class="it-rec-pill" style="background: {{ $recBg }}; color: {{ $recColor }};">{{ $recText }}</span>
                                @endif
                                @if($starRating > 0)
                                    <span class="it-star-pill"><i class="fas fa-star"></i> {{ number_format($starRating, 1) }}</span>
                                @endif
                            </div>
                            <button type="button" class="it-feedback-btn it-fb-btn-view show-feedback" data-row-id="{{ $row->id }}">
                                <i class="far fa-eye"></i> View Feedback
                            </button>
                        @elseif($notAttended)
                            <div class="it-fb-status-row" style="color: #d97706;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>{{ $attLabelMap[$attId] ?? 'Not Attended' }}</span>
                            </div>
                            <button type="button" class="it-feedback-btn it-fb-btn-neutral show-feedback" data-row-id="{{ $row->id }}">
                                <i class="far fa-file-alt"></i> No Feedback
                            </button>
                        @else
                            <div class="it-fb-status-row" style="color: #d97706;">
                                <i class="far fa-clock"></i>
                                <span>Pending</span>
                            </div>
                            @if($canAddFeedback)
                                <button type="button" class="it-feedback-btn it-fb-btn-add {{ $customHtml }}" data-row-id="{{ $row->id }}">
                                    <i class="fas fa-plus"></i> Add Feedback
                                </button>
                            @else
                                <span class="it-fb-locked-text">Pending Feedback</span>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
            <!-- End Upper Sub-Cards Grid -->

            <!-- Bottom Activity & Pipeline Strip -->
            <div class="it-card-bottom">
                <div class="it-bottom-left">
                    <div class="it-bottom-item it-stage-cell">
                        <span class="it-bottom-lbl">Stage:</span>
                        <span class="it-stage-badge {{ $stageBadgeClass }}">{{ $masterStatusName }}</span>
                        @if($childStageName)
                            <span class="it-substage-pill">{{ $childStageName }}</span>
                        @endif
                    </div>
                    @if($fuStatus && $fuAt)
                        <span class="it-dot-sep">&bull;</span>
                        <div class="it-bottom-item">
                            <span class="it-bottom-lbl">Follow-up:</span>
                            <span class="it-bottom-muted">{{ Carbon::parse($fuAt)->format('d M, h:i A') }}</span>
                        </div>
                    @endif
                </div>

                <div class="it-bottom-right">
                    @if(!$attended && $canAddFeedback && !empty($row->wiseID))
                        <button type="button"
                                class="it-action-ghost js-it-reschedule-btn"
                                data-wise-id="{{ $row->wiseID }}"
                                data-interview-id="{{ $row->id }}"
                                title="Reschedule this interview">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Reschedule</span>
                        </button>
                    @endif
                    <button type="button"
                            class="it-action-primary {{ $profile_detail }}"
                            data-row-id="{{ @$row->wiseID }}"
                            title="Open Candidate Drawer">
                        <span>Candidate Profile</span>
                        <i class="fas fa-external-link-alt"></i>
                    </button>
                </div>
            </div>

        </div>
    @endforeach
@else
    <div class="it-empty-card">
        <div class="it-empty-icon">
            <i class="far fa-calendar-times"></i>
        </div>
        <div class="it-empty-title">No Interviews Found</div>
        <div class="it-empty-sub">No scheduled interviews match your current search, tab, or mode filters.</div>
        <button type="button" class="btn btn-sm btn-light-primary fw-bold mt-3" onclick="resetFilters()">
            <i class="fas fa-sync-alt me-1"></i> Reset All Filters
        </button>
    </div>
@endif
