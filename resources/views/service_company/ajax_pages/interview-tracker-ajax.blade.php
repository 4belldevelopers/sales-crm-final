@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();

    // Follow-up statuses for this page (one query; resilient if the column isn't migrated yet)
    $followupMap = collect();
    if (\Illuminate\Support\Facades\Schema::hasColumn('interview_schedules', 'followup_status')) {
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

            {{-- Candidate Cell: Name + Role + Status --}}
            <td>
                @php
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->full_name);
                    $str = $strVal = strtolower($str[0]);
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

                    $profile_detail = 'show-application-detail';
                    if ($row->thirdparty_company_id == $company_id) {
                        $profile_detail = 'show-application-detail';
                    }

                    // Status for candidate meta
                    $candidateStatusClass = 'info';
                    $candidateStatusText = 'Scheduled';
                    $candidateStatusTime = '';

                    $schedule_date = $row->schedule_date;
                    $current_date = date('Y-m-d H:i:s');
                    $schedule_timestamp = strtotime($schedule_date);
                    $current_timestamp = strtotime($current_date);

                    if ($schedule_timestamp >= $current_timestamp) {
                        $candidateStatusClass = 'confirmed';
                        $candidateStatusText = 'Confirmed';
                    } else {
                        $candidateStatusClass = 'warning';
                        $candidateStatusText = 'Pending';
                    }

                    if (!empty($row->feedback)) {
                        $candidateStatusTime = ucwords(mb_substr(@$feedback_status[@$row->feedback]['name'], 0, 22));
                    }
                @endphp
                <div class="it-candidate-cell">
                    <div class="it-avatar {{ $bgTextDecore }} {{ $profile_detail }}" data-row-id="{{ $row->wiseID }}">
                        {{ ucwords($strVal) }}
                    </div>
                    <div class="it-candidate-info">
                        <div class="it-candidate-name">
                            <a href="javascript:void(0);" data-row-id="{{ $row->wiseID }}" class="text-dark fw-bolder text-hover-primary fs-6 {{ $profile_detail }}" data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}" style="text-decoration:none;color:inherit;">
                                {{ ucwords(mb_substr(@$row->full_name, 0, 20)) }}
                            </a>
                        </div>
                        <div class="it-candidate-role">
                            <a href="javascript:void(0);" data-row-id="{{ $row->wiseID }}" class="fw-bold text-muted d-block fs-7 {{ $profile_detail }} text-hover-primary" data-toggle="tooltip" title="{{ ucwords(@$row->current_designation) }}" style="text-decoration:none;color:inherit;">
                                {{ ucwords(mb_substr(@$row->current_designation, 0, 21)) }}
                            </a>
                        </div>
                        @php
                            $scheduleDate = Carbon::parse($row->schedule_date);
                            $now = Carbon::now();
                            $timeText = '';
                            if ($scheduleDate->isToday()) {
                                $timeText = 'Today, ' . $scheduleDate->format('h:i A');
                            } elseif ($scheduleDate->isYesterday()) {
                                $timeText = 'Yesterday, ' . $scheduleDate->format('h:i A');
                            } else {
                                $timeText = $scheduleDate->format('d M, h:i A');
                            }
                        @endphp
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
                            <div class="it-followup-dropdown" data-fid="{{ $row->id }}" style="position: relative; display: inline-block; margin-top: 2px;">
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
                                <div class="it-meta-tag info" style="font-size: 10px; color: var(--it-text-muted); margin-top: 2px;">
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
                    $client_name_com = GeneralMethod::companyDetailsByClient($row->job_id);
                    if (!empty($client_name_com)) {
                        $client_title_name = $client_name_com;
                        $client_name = ucwords(mb_substr($client_title_name, 0, 15));
                    } else {
                        $client_title_name = $row->client_name;
                        $client_name = ucwords(mb_substr($client_title_name, 0, 15));
                    }

                    // Job type / recommendation
                    $recommendation_text = __('messages.internal_tag');
                    $jobTypeLabel = 'Internal';
                    $jobTypeClass = '';
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $recommendation_text = __('messages.clinet_tag');
                        $jobTypeLabel = 'Client';
                    }
                @endphp
                <div class="it-job-title" data-toggle="tooltip" title="{{ ucwords($row->job_title) }}">
                    {{ ucwords(mb_substr($row->job_title, 0, 18)) }}
                </div>
                <div class="it-job-company" data-toggle="tooltip" title="{{ ucwords($client_title_name) }}">
                    {{ ucwords(mb_substr($client_name, 0, 14)) }}
                </div>
                <div class="it-job-tags">
                    <span class="it-job-type">{{ $jobTypeLabel }}</span>
                    <span class="it-dot-separator"></span>
                    <a href="javascript:void(0);" class="it-job-id">{{ @$row->job_id }}</a>
                </div>
            </td>

            {{-- Interview Date & Time + Mode --}}
            <td>
                @php
                    $modeIcon = '';
                    if ($row->interview_mode == 1) {
                        // Face to Face
                        $modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
                    } elseif ($row->interview_mode == 2) {
                        // Video Call
                        $modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>';
                    } else {
                        // Phone Call
                        $modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
                    }
                @endphp
                <div class="it-date-cell">
                    <div class="it-date-row">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        {{ Carbon::parse($row->schedule_date)->format('d M, Y') }}
                    </div>
                    <div class="it-date-row">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        {{ $row->schedule_to_time }}{{ $row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '' }}
                    </div>
                    <div class="it-date-row it-mode">
                        {!! $modeIcon !!}
                        {{ ucwords(mb_substr($row->mode_name, 0, 13)) }}
                    </div>
                    @php
                        // Mode: 1=Telecon, 2=Video, 3=Face to Face. interview_mention_link holds a URL (video/telecon link) or an address (F2F).
                        $mLink = trim((string) @$row->interview_mention_link);
                        $isUrl = (bool) preg_match('#^https?://#i', $mLink);
                        $ilType = null; $ilLabel = '';
                        if ($row->interview_mode == 3 && $mLink !== '') {      // Face to Face -> address
                            $ilType = 'address'; $ilLabel = 'View Address';
                        } elseif ($isUrl) {                                    // Video / Telecon with a meeting link
                            $ilType = 'link'; $ilLabel = 'Interview Link';
                        }
                    @endphp
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
                    $real_company_id = $row->real_company_id;
                    $mailstatus = $row->mailstatus;
                    $recommendation_id = $row->recommendation_id;
                    $mailsend = '';

                    $schedule_date = $row->schedule_date;
                    $current_date = date('Y-m-d H:i:s');
                    $schedule_timestamp = strtotime($schedule_date);
                    $current_timestamp = strtotime($current_date);
                    $current_timestamp_plus_4_hours = strtotime('+4 hours', $current_timestamp);

                    if ($row->interview_mode == 2) {
                        if ($schedule_timestamp >= $current_timestamp_plus_4_hours) {
                            if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                                $mailsend = '';
                            } else {
                                if ($mailstatus == 1) {
                                    $mailsend = '<a href="javascript:void(0);" class="it-job-id app_mail_schedule" rel="' . $row->wiseID . '" relint="' . $row->id . '" data-toggle="modal" data-target="#interview_schedule">Mail Resend</a>';
                                } else {
                                    $mailsend = '<a href="javascript:void(0);" class="it-job-id app_mail_schedule" rel="' . $row->wiseID . '" relint="' . $row->id . '" data-toggle="modal" data-target="#interview_schedule">Mail Send</a>';
                                }
                            }
                        }
                    }

                    $firstInterviewer = '';
                    $interviewerCompany = '';
                    $extraInterviewers = 0;

                    if (!empty($interviewers)) {
                        $interviewersArr = explode(' , ', trim($interviewers));
                        if (!empty($interviewersArr)) {
                            $firstInterviewer = ucwords(trim($interviewersArr[0]));
                            $extraInterviewers = count($interviewersArr) - 1;
                        }
                    }
                @endphp
                <div class="it-person-cell">
                    <div class="it-person-name">{{ $firstInterviewer ?: 'N/A' }}</div>
                    <div class="it-person-company">{{ ucwords(mb_substr(@$row->uc_company_name, 0, 13)) }}</div>
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
                    if($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0){
                        $spokperson = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $row->jobId)->where('job_assign.company_id', $row->ClientComID)->first();
                        $rowpostedby = (@$spokperson) ? @$spokperson->name : $row->posted_by;
                        $rowpostedby = @$row->rec_candidate_assign_id ? $row->posted_by : $rowpostedby;
                    } else {
                        $rowpostedby = $row->posted_by;
                    }
                @endphp
                <div class="it-person-cell">
                    <div class="it-person-name" data-toggle="tooltip" title="{{ @$rowpostedby }}">
                        {{ ucwords(mb_substr($rowpostedby, 0, 10)) }}
                    </div>
                    <div class="it-person-company" data-toggle="tooltip" title="{{ @$row->uc_company_name }}">
                        {{ mb_substr(@$row->uc_company_name, 0, 10) }}
                    </div>
                </div>
            </td>

            {{-- Stage --}}
            <td>
                @php
                    $name = '';
                    if ($row->master_status_id == 8) {
                        $name = 'Private';
                    }
                    if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                        $name = @$row->child_status_name;
                    }

                    // Map css_style to new badge classes
                    $stageBadgeClass = 'it-badge-blue';
                    $masterStatusName = mb_substr(@$row->master_status_name, 0, 10);
                    $styleLower = strtolower(@$row->css_style);
                    if (strpos($styleLower, 'success') !== false || strpos($styleLower, 'green') !== false) {
                        $stageBadgeClass = 'it-badge-green';
                    } elseif (strpos($styleLower, 'danger') !== false || strpos($styleLower, 'red') !== false) {
                        $stageBadgeClass = 'it-badge-red';
                    } elseif (strpos($styleLower, 'warning') !== false || strpos($styleLower, 'yellow') !== false || strpos($styleLower, 'orange') !== false) {
                        $stageBadgeClass = 'it-badge-orange';
                    } elseif (strpos($styleLower, 'purple') !== false || strpos($styleLower, 'info') !== false) {
                        $stageBadgeClass = 'it-badge-purple';
                    }

                    // Stage status
                    $stageStatusClass = 'pending';
                    $stageStatusText = 'Pending';
                    if (!empty($row->feedback) || $row->average_star_rating > 0) {
                        $stageStatusClass = 'confirmed';
                        $stageStatusText = 'Feedback';
                    }
                @endphp
                <div class="it-stage-cell">
                    <span class="it-badge {{ $stageBadgeClass }} {{ @$row->css_style }}">{{ $masterStatusName }}</span>
                    @if($name)
                        <span class="text-muted fw-bold text-muted d-block fs-7" style="font-size:10px;color:var(--it-text-muted);margin-top:2px;">{{ $name }}</span>
                    @endif
                    <div class="it-stage-status {{ $stageStatusClass }}" style="margin-top:2px;">
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
                    // Latest feedback row for this interview. A row is created on EVERY submit
                    // (attended or not), so its existence = "feedback recorded".
                    // @ keeps this resilient before the compensation migration is run.
                    $latestFb = DB::table('interview_feedback')->where('interview_id', $row->id)->orderByDesc('id')->first();
                    $hasAnyFeedback = !empty($latestFb);
                    $attId = (int) (@$latestFb->available_status ?? 0); // 1=Attended

                    $attLabelMap = [1=>'Attended', 2=>'No Show', 3=>'Rescheduled', 4=>'Cancelled by Company', 6=>'Other Reason', 7=>'Cancelled by Candidate'];

                    // Result (row 1) — prefer new interview_result, else fall back to legacy feedback status
                    $resultLabels = ['selected'=>'Selected','hold'=>'Hold','rejected'=>'Rejected','fake_proxy'=>'Fake / Proxy','feedback_pending'=>'Feedback Pending'];
                    $resultKey = @$latestFb->interview_result;
                    $resultText = '';
                    if (!empty($resultKey) && isset($resultLabels[$resultKey])) {
                        $resultText = $resultLabels[$resultKey];
                    } elseif (!empty($row->feedback) && isset($feedback_status[$row->feedback])) {
                        $resultText = ucwords(mb_substr($feedback_status[$row->feedback]['name'], 0, 22));
                    }
                    $legacyFb = (int) (@$row->feedback ?? 0); // 1=Selected,2=Hold,3=Rejected,4=Fake,5=Feedback Pending
                    $resultColor = 'var(--it-success)';
                    if (in_array($resultKey, ['rejected','fake_proxy']) || in_array($legacyFb, [3,4])) { $resultColor = 'var(--it-danger)'; }
                    elseif (in_array($resultKey, ['hold','feedback_pending']) || in_array($legacyFb, [2,5])) { $resultColor = 'var(--it-warning)'; }

                    // Recommendation (row 2) — replaces the star rating
                    $recLabels = ['strong_hire'=>'Strong Hire','hire'=>'Hire','consider'=>'Consider','no_hire'=>'No Hire','strong_no_hire'=>'Strong No Hire'];
                    $recKey = @$latestFb->overall_recommendation;
                    $recText = (!empty($recKey) && isset($recLabels[$recKey])) ? $recLabels[$recKey] : '';
                    $recBg = '#eef2ff'; $recColor = '#4f46e5';
                    if (in_array($recKey, ['strong_hire','hire'])) { $recBg = '#dcfce7'; $recColor = '#16a34a'; }
                    elseif (in_array($recKey, ['no_hire','strong_no_hire'])) { $recBg = '#fee2e2'; $recColor = '#dc2626'; }
                    elseif ($recKey === 'consider') { $recBg = '#fef3c7'; $recColor = '#d97706'; }

                    $attended = ($attId === 1);
                    $notAttended = ($hasAnyFeedback && $attId > 0 && $attId !== 1);

                    // Permissions (unchanged)
                    $canAddFeedback = true;
                    $customHtml = 'show-feedback';
                    if ($row->thirdparty_company_id == $company_id) { $customHtml = ''; $canAddFeedback = false; }
                    if ($row->to_company_id == $company_id) { $customHtml = ''; $canAddFeedback = false; }
                    $allowChk = DB::table('job_assign')->select('users.name')->join('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $row->jobId)->where('job_assign.spok_user_id', auth()->user()->id)->count();
                    // Anyone named as an interviewer on this schedule may add feedback too (not just the captain)
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
                        {{-- Condition 1: candidate attended -> result + recommendation + View Feedback --}}
                        <span style="font-size: 10px; font-weight: 600; color: {{ $resultColor }}; display: inline-flex; align-items: center; gap: 3px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ $resultText ?: 'Feedback Pending' }}
                        </span>
                        @if($recText)
                            <span style="display:inline-flex;align-items:center;width:fit-content;padding:2px 8px;border-radius:50px;font-size:10px;font-weight:600;background:{{ $recBg }};color:{{ $recColor }};">{{ $recText }}</span>
                        @endif
                        <button type="button" class="it-feedback-btn it-feedback-btn-outline-green show-feedback" data-row-id="{{ $row->id }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View Feedback
                        </button>
                    @elseif($notAttended)
                        {{-- Condition 2: candidate did not attend -> attendance reason + No Feedback (click to review/correct) --}}
                        <span style="font-size: 10px; font-weight: 600; color: var(--it-warning); display: inline-flex; align-items: center; gap: 3px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            {{ $attLabelMap[$attId] ?? 'Not Attended' }}
                        </span>
                        <button type="button" class="it-feedback-btn show-feedback" data-row-id="{{ $row->id }}" style="border:1px solid var(--it-border);color:var(--it-text-muted);background:#fff;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                            No Feedback
                        </button>
                    @else
                        {{-- Condition 3: nothing recorded yet -> Add Feedback --}}
                        <span style="font-size: 10px; font-weight: 600; color: var(--it-warning); display: inline-flex; align-items: center; gap: 3px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Pending
                        </span>
                        @if($canAddFeedback)
                            <button type="button" class="it-feedback-btn it-feedback-btn-solid-blue {{ $customHtml }}" data-row-id="{{ $row->id }}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Add Feedback
                            </button>
                        @else
                            <span style="font-size:10px;color:var(--it-text-muted);">Pending Feedback</span>
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
