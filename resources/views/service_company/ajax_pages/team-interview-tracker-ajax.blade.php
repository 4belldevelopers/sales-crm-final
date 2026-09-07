@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $feedback_status = GeneralMethod::feedback_status();

    // Resolve scope vars defensively (controller passes them; keep resilient otherwise)
    $company_id   = $company_id   ?? (auth()->check() ? auth()->user()->company_id : 0);
    $chkAdminRole = $chkAdminRole ?? (auth()->check() ? auth()->user()->hasRole('admin') : 0);

    // Follow-up statuses for this page (one query; resilient if the column isn't migrated yet)
    $hasFollowupCol = \Illuminate\Support\Facades\Cache::remember('ats:has_followup_col', 86400, function() {
        return \Illuminate\Support\Facades\Schema::hasColumn('interview_schedules', 'followup_status');
    });
    $followupMap = collect();
    if (count($data) > 0 && $hasFollowupCol) {
        $followupMap = DB::table('interview_schedules')
            ->whereIn('id', collect($data)->pluck('id')->all())
            ->get(['id', 'followup_status', 'followup_status_at'])
            ->keyBy('id');
    }
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
        $row = (object) $rowArr;
    @endphp
        <tr>
            {{-- Checkbox --}}
            <td>
                <div class="it-checkbox" data-row-check onclick="toggleRowSelect(this, {{ $row->id }})">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            </td>

            {{-- Candidate: Avatar + Name + Role + Follow Up --}}
            <td>
                @php
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->full_name);
                    $str = $strVal = strtolower($str[0] ?? '');
                    $str = ord($str);
                    if ($str >= 97 && $str <= 101) {
                        $bgTextDecore = 'it-avatar-red';
                    } elseif ($str >= 102 && $str <= 106) {
                        $bgTextDecore = 'it-avatar-orange';
                    } elseif ($str >= 107 && $str <= 111) {
                        $bgTextDecore = 'it-avatar-blue';
                    } elseif ($str >= 112 && $str <= 116) {
                        $bgTextDecore = 'it-avatar-green';
                    } elseif ($str >= 117 && $str <= 121) {
                        $bgTextDecore = 'it-avatar-purple';
                    } else {
                        $bgTextDecore = 'it-avatar-gray';
                    }
                @endphp
                <div class="it-candidate-cell">
                    <div class="it-avatar {{ $bgTextDecore }} custom-show-application-detail" data-row-id="{{ $row->wiseID }}">
                        {{ ucwords($strVal) }}
                    </div>
                    <div class="it-candidate-info">
                        <div class="it-candidate-name">
                            <a href="javascript:void(0);" data-row-id="{{ $row->wiseID }}" class="custom-show-application-detail" data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}" style="text-decoration:none;color:inherit;">
                                {{ ucwords(mb_substr(@$row->full_name, 0, 20)) }}
                            </a>
                        </div>
                        <div class="it-candidate-role">
                            <a href="javascript:void(0);" data-row-id="{{ $row->wiseID }}" class="custom-show-application-detail" data-toggle="tooltip" title="{{ ucwords(@$row->current_designation) }}" style="text-decoration:none;color:inherit;">
                                {{ ucwords(mb_substr(@$row->current_designation, 0, 21)) }}
                            </a>
                        </div>
                        <div class="it-candidate-meta">
                            @php
                                $fuRow = $followupMap->get($row->id);
                                $fuStatus = is_object($fuRow) ? ($fuRow->followup_status ?? null) : null;
                                $fuAt = is_object($fuRow) ? ($fuRow->followup_status_at ?? null) : null;
                                $fuLabels = ['confirmed'=>'Confirmed','no-response'=>'No Response','rescheduled'=>'Rescheduled'];
                                $fuLabel = $fuLabels[$fuStatus] ?? 'Follow Up';
                                $fuStyle = '';
                                if ($fuStatus === 'confirmed') { $fuStyle = 'color:var(--it-success);border-color:var(--it-success);'; }
                                elseif ($fuStatus === 'no-response') { $fuStyle = 'color:var(--it-danger);border-color:var(--it-danger);'; }
                                elseif ($fuStatus === 'rescheduled') { $fuStyle = 'color:var(--it-primary);border-color:var(--it-primary);'; }
                            @endphp
                            <div class="it-followup-dropdown" data-fid="{{ $row->id }}">
                                <button class="it-followup-btn it-followup-trigger" onclick="toggleFollowUp(this)" type="button" style="{{ $fuStyle }}">
                                    @if(!$fuStatus)
                                        <svg class="fu-cta-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    @endif
                                    <span class="fu-label">{{ $fuLabel }}</span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                                <div class="it-followup-menu">
                                    <div class="it-followup-item" onclick="setFollowUpStatus(this, {{ $row->id }}, 'confirmed')">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        Confirmed
                                    </div>
                                    <div class="it-followup-item" onclick="setFollowUpStatus(this, {{ $row->id }}, 'no-response')">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                        No Response
                                    </div>
                                    <div class="it-followup-item" onclick="setFollowUpStatus(this, {{ $row->id }}, 'rescheduled')">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                        Rescheduled
                                    </div>
                                </div>
                            </div>
                            @if($fuStatus && $fuAt)
                                <div class="it-meta-tag info" style="font-size:10px;color:var(--it-text-muted);margin-top:2px;">
                                    {{ \Carbon\Carbon::parse($fuAt)->format('d M, h:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </td>

            {{-- Job Requisitions: Title + Company + Type + JobID --}}
            <td>
                @php
                    $client_name = '';
                    $client_title_name = '';
                    $targetJobId = $row->jobId ?? $row->job_id ?? 0;
                    $client_name_com = $targetJobId > 0 ? GeneralMethod::companyDetailsByClient($targetJobId) : null;
                    if (!empty($client_name_com)) {
                        $client_title_name = $client_name_com;
                    } else {
                        $client_title_name = $row->client_name ?? '';
                    }
                    $client_name = ucwords(mb_substr((string) $client_title_name, 0, 15));

                    // Job type / recommendation
                    $jobTypeLabel = 'Internal';
                    $recId = $row->recommendation_id ?? 0;
                    $toCompId = $row->to_company_id ?? 0;
                    if ($recId > 0 && $toCompId == $company_id) {
                        $jobTypeLabel = 'Client';
                    }
                    $jobTitleText = $row->job_title ?? '';
                @endphp
                <div class="it-job-title" data-toggle="tooltip" title="{{ ucwords($jobTitleText) }}">
                    {{ ucwords(mb_substr($jobTitleText, 0, 18)) }}
                </div>
                <div class="it-job-company" data-toggle="tooltip" title="{{ ucwords($client_title_name) }}">
                    {{ ucwords(mb_substr($client_name, 0, 14)) }}
                </div>
                <div class="it-job-tags">
                    <span class="it-job-type">{{ $jobTypeLabel }}</span>
                    <span class="it-dot-separator"></span>
                    <a href="javascript:void(0);" class="it-job-id">{{ $targetJobId }}</a>
                </div>
            </td>

            {{-- Interview Date & Time + Mode + Link --}}
            <td>
                @php
                    $intMode = $row->interview_mode ?? 0;
                    if ($intMode == 1) {
                        // Telecon / Phone
                        $modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
                    } elseif ($intMode == 2) {
                        // Video Call
                        $modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>';
                    } else {
                        // Face to Face
                        $modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
                    }

                    $mLink = trim((string) @$row->interview_mention_link);
                    $isUrl = (bool) preg_match('#^https?://#i', $mLink);
                    $ilType = null; $ilLabel = '';
                    if ($intMode == 3 && $mLink !== '') {
                        $ilType = 'address'; $ilLabel = 'View Address';
                    } elseif ($isUrl) {
                        $ilType = 'link'; $ilLabel = 'Interview Link';
                    }
                    $schedTime = $row->schedule_to_time ?? '';
                    $tzAbbr = $row->timezone_abbreviation ?? '';
                    $modeNameText = $row->mode_name ?? '';
                @endphp
                <div class="it-date-cell">
                    <div class="it-date-row">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        {{ Carbon::parse($row->schedule_date ?? 'now')->format('d M, Y') }}
                    </div>
                    <div class="it-date-row">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 16 14"/>
                        </svg>
                        {{ $schedTime }}{{ $tzAbbr ? ' (' . $tzAbbr . ')' : '' }}
                    </div>
                    <div class="it-date-row it-mode-row">
                        {!! $modeIcon !!}
                        <span class="it-mode">{{ ucwords(mb_substr((string) $modeNameText, 0, 13)) }}</span>
                    </div>
                    @if($ilType)
                        <a href="javascript:void(0);" class="it-date-row open-interview-link"
                           style="color:var(--it-primary);font-weight:500;cursor:pointer;margin-top:2px;"
                           data-link="{{ $mLink }}"
                           data-id="{{ $row->id }}"
                           data-name="{{ ucwords(@$row->full_name) }}"
                           data-type="{{ $ilType }}">
                            @if($ilType === 'address')
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            @else
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            @endif
                            {{ $ilLabel }}
                        </a>
                    @endif
                </div>
            </td>

            {{-- Interviewers (Panel) --}}
            <td>
                @php
                    $interviewers = $row->interviewers;
                    $firstInterviewer = '';
                    $extraInterviewers = 0;
                    if (!empty($interviewers)) {
                        $interviewersArr = preg_split('/\s*,\s*/', trim($interviewers));
                        if (!empty($interviewersArr)) {
                            $firstInterviewer = ucwords(trim($interviewersArr[0]));
                            $extraInterviewers = count($interviewersArr) - 1;
                        }
                    }
                @endphp
                <div class="it-person-cell">
                    <div class="it-person-name">{{ $firstInterviewer ?: 'N/A' }}</div>
                    <div class="it-person-company">{{ ucwords(mb_substr((string) @$row->uc_company_name, 0, 13)) }}</div>
                    @if($extraInterviewers > 0)
                        <div class="it-person-extra">
                            <span class="it-extra-badge">+{{ $extraInterviewers }}</span>
                            <span class="it-extra-text">{{ $extraInterviewers + 1 }} Interviewers</span>
                        </div>
                    @endif
                </div>
            </td>

            {{-- Source (Owner) --}}
            <td>
                @php
                    $clientComID = $row->ClientComID ?? 0;
                    $postedByText = $row->posted_by ?? '';
                    if($company_id != $clientComID && $clientComID != 0 && $clientComID > 0){
                        $spokperson = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $targetJobId)->where('job_assign.company_id', $clientComID)->first();
                        $rowpostedby = (@$spokperson) ? @$spokperson->name : $postedByText;
                        $rowpostedby = @$row->rec_candidate_assign_id ? $postedByText : $rowpostedby;
                    } else {
                        $rowpostedby = $postedByText;
                    }
                @endphp
                <div class="it-person-cell">
                    <div class="it-person-name" data-toggle="tooltip" title="{{ @$rowpostedby }}">
                        {{ ucwords(mb_substr((string) $rowpostedby, 0, 10)) }}
                    </div>
                    <div class="it-person-company" data-toggle="tooltip" title="{{ @$row->uc_company_name }}">
                        {{ ucwords(mb_substr((string) @$row->uc_company_name, 0, 10)) }}
                    </div>
                </div>
            </td>

            {{-- Stage --}}
            <td>
                @php
                    $name = '';
                    $masterStatusId = $row->master_status_id ?? 0;
                    if ($masterStatusId == 8) {
                        $name = 'Private';
                    }
                    if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                        $name = @$row->child_status_name;
                    }

                    $stageBadgeClass = 'it-badge-blue';
                    $masterStatusName = mb_substr((string) @$row->master_status_name, 0, 10);
                    $styleLower = strtolower((string) @$row->css_style);
                    if (strpos($styleLower, 'success') !== false || strpos($styleLower, 'green') !== false) {
                        $stageBadgeClass = 'it-badge-green';
                    } elseif (strpos($styleLower, 'danger') !== false || strpos($styleLower, 'red') !== false) {
                        $stageBadgeClass = 'it-badge-red';
                    } elseif (strpos($styleLower, 'warning') !== false || strpos($styleLower, 'yellow') !== false || strpos($styleLower, 'orange') !== false) {
                        $stageBadgeClass = 'it-badge-orange';
                    } elseif (strpos($styleLower, 'purple') !== false || strpos($styleLower, 'info') !== false) {
                        $stageBadgeClass = 'it-badge-purple';
                    }

                    $stageStatusClass = 'pending';
                    $stageStatusText = 'Pending';
                    $avgStarRating = $row->average_star_rating ?? 0;
                    if (!empty($row->feedback) || $avgStarRating > 0) {
                        $stageStatusClass = 'confirmed';
                        $stageStatusText = 'Feedback';
                    }
                @endphp
                <div class="it-stage-cell">
                    <span class="it-badge {{ $stageBadgeClass }}">{{ $masterStatusName }}</span>
                    @if($name)
                        <span class="it-stage-sub">{{ $name }}</span>
                    @endif
                    <div class="it-stage-status {{ $stageStatusClass }}">
                        @if($stageStatusClass == 'confirmed')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        @endif
                        {{ $stageStatusText }}
                    </div>
                </div>
            </td>

            {{-- Feedback --}}
            <td>
                @php
                    $latestFb = DB::table('interview_feedback')->where('interview_id', $row->id)->orderByDesc('id')->first();
                    $hasAnyFeedback = !empty($latestFb);
                    $attId = (int) (@$latestFb->available_status ?? 0); // 1=Attended

                    $attLabelMap = [1=>'Attended', 2=>'No Show', 3=>'Rescheduled', 4=>'Cancelled by Company', 6=>'Other Reason', 7=>'Cancelled by Candidate'];

                    $resultLabels = ['selected'=>'Selected','hold'=>'Hold','rejected'=>'Rejected','fake_proxy'=>'Fake / Proxy','feedback_pending'=>'Feedback Pending'];
                    $resultKey = @$latestFb->interview_result;
                    $resultText = '';
                    if (!empty($resultKey) && isset($resultLabels[$resultKey])) {
                        $resultText = $resultLabels[$resultKey];
                    } elseif (!empty($row->feedback) && isset($feedback_status[$row->feedback])) {
                        $resultText = ucwords(mb_substr($feedback_status[$row->feedback]['name'], 0, 22));
                    }
                    $legacyFb = (int) (@$row->feedback ?? 0);
                    $resultColor = 'var(--it-success)';
                    if (in_array($resultKey, ['rejected','fake_proxy']) || in_array($legacyFb, [3,4])) { $resultColor = 'var(--it-danger)'; }
                    elseif (in_array($resultKey, ['hold','feedback_pending']) || in_array($legacyFb, [2,5])) { $resultColor = 'var(--it-warning)'; }

                    $recLabels = ['strong_hire'=>'Strong Hire','hire'=>'Hire','consider'=>'Consider','no_hire'=>'No Hire','strong_no_hire'=>'Strong No Hire'];
                    $recKey = @$latestFb->overall_recommendation;
                    $recText = (!empty($recKey) && isset($recLabels[$recKey])) ? $recLabels[$recKey] : '';
                    $recBg = '#eef2ff'; $recColor = '#4f46e5';
                    if (in_array($recKey, ['strong_hire','hire'])) { $recBg = '#dcfce7'; $recColor = '#16a34a'; }
                    elseif (in_array($recKey, ['no_hire','strong_no_hire'])) { $recBg = '#fee2e2'; $recColor = '#dc2626'; }
                    elseif ($recKey === 'consider') { $recBg = '#fef3c7'; $recColor = '#d97706'; }

                    $attended = ($attId === 1);
                    $notAttended = ($hasAnyFeedback && $attId > 0 && $attId !== 1);

                    // Permissions
                    $canAddFeedback = true;
                    $customHtml = 'show-feedback';
                    $thirdpartyCompId = $row->thirdparty_company_id ?? $row->ClientComID ?? 0;
                    $toCompanyId = $row->to_company_id ?? 0;
                    if ($thirdpartyCompId == $company_id) { $customHtml = ''; $canAddFeedback = false; }
                    if ($toCompanyId == $company_id) { $customHtml = ''; $canAddFeedback = false; }
                    $allowChk = DB::table('job_assign')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $targetJobId)->where('job_assign.spok_user_id', auth()->user()->id)->count();
                    $isInterviewerOnRow = false;
                    $myNameChk = strtolower(trim(auth()->user()->name ?? ''));
                    if ($myNameChk !== '' && !empty($row->interviewers)) {
                        foreach (preg_split('/\s*,\s*/', $row->interviewers) as $intvName) {
                            if (strtolower(trim($intvName)) === $myNameChk) { $isInterviewerOnRow = true; break; }
                        }
                    }
                    if($chkAdminRole != 1){ if($allowChk == 0 && !$isInterviewerOnRow){ $customHtml = 'notAllow-feedback'; $canAddFeedback = false; } }
                @endphp
                <div class="it-feedback-cell">
                    @if($attended)
                        <span class="it-fb-status" style="color: {{ $resultColor }};">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ $resultText ?: 'Feedback Pending' }}
                        </span>
                        @if($recText)
                            <span class="it-fb-rec" style="background:{{ $recBg }};color:{{ $recColor }};">{{ $recText }}</span>
                        @endif
                        <button type="button" class="it-feedback-btn it-feedback-btn-outline-green show-feedback" data-row-id="{{ $row->id }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View Feedback
                        </button>
                    @elseif($notAttended)
                        <span class="it-fb-status" style="color: var(--it-warning);">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            {{ $attLabelMap[$attId] ?? 'Not Attended' }}
                        </span>
                        <button type="button" class="it-feedback-btn show-feedback" data-row-id="{{ $row->id }}" style="border:1px solid var(--it-border);color:var(--it-text-muted);background:#fff;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                            No Feedback
                        </button>
                    @else
                        <span class="it-fb-status" style="color: var(--it-warning);">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Pending
                        </span>
                        @if($canAddFeedback)
                            <button type="button" class="it-feedback-btn it-feedback-btn-solid-blue {{ $customHtml }}" data-row-id="{{ $row->id }}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Add Feedback
                            </button>
                        @else
                            <span class="it-fb-muted">Pending Feedback</span>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" style="text-align:center;padding:30px;color:var(--it-text-muted);font-size:12px;background:#fffcfc7a;">
            No data available in table
        </td>
    </tr>
@endif
