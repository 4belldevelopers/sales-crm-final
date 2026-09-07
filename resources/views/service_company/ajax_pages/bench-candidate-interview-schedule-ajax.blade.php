@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
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

            {{-- Candidate Cell: Avatar + Name + Designation --}}
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
                    </div>
                </div>
            </td>

            {{-- Job Requisitions: Title + Company + Type + JobID --}}
            <td>
                @php
                    $client_title_name = $row->client_title_name_enriched ?? $row->client_name ?? $row->company_name ?? '';
                    $client_name = ucwords(mb_substr($client_title_name, 0, 15));

                    $jobTypeLabel = 'Internal Client';
                    $jobTypeClass = 'it-job-type-internal';
                    if (($row->recommendation_id ?? 0) > 0 && (@$row->to_company_id == auth()->user()->company_id)) {
                        $jobTypeLabel = 'Direct Client';
                        $jobTypeClass = 'it-job-type-direct';
                    }
                @endphp
                <div class="it-job-title" data-toggle="tooltip" title="{{ ucwords($row->job_title) }}">
                    {{ ucwords(mb_substr($row->job_title, 0, 18)) }}
                </div>
                <div class="it-job-company" data-toggle="tooltip" title="{{ ucwords($client_title_name) }}">
                    {{ ucwords(mb_substr($client_name, 0, 14)) }}
                </div>
                <div class="it-job-tags">
                    <span class="it-job-type {{ $jobTypeClass }}">{{ $jobTypeLabel }}</span>
                    <span class="it-dot-separator"></span>
                    <a href="javascript:void(0);" class="it-job-id">{{ @$row->job_id }}</a>
                </div>
            </td>

            {{-- Interview Date & Time + Mode --}}
            <td>
                @php
                    $modeIcon = '';
                    if ($row->interview_mode == 1) {
                        $modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
                    } elseif ($row->interview_mode == 2) {
                        $modeIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>';
                    } else {
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
                        $mLink = trim((string) @$row->interview_mention_link);
                        $isUrl = (bool) preg_match('#^https?://#i', $mLink);
                        $ilType = null; $ilLabel = '';
                        if ($row->interview_mode == 3 && $mLink !== '') {
                            $ilType = 'address'; $ilLabel = 'View Address';
                        } elseif ($isUrl) {
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

            {{-- Interviewers --}}
            <td>
                @php
                    $interviewers = $row->interviewers;
                    $firstInterviewer = '';
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
                <div class="it-person-cell">
                    <div class="it-person-name" data-toggle="tooltip" title="{{ @$row->posted_by }}">
                        {{ ucwords(mb_substr(@$row->posted_by, 0, 12)) }}
                    </div>
                    <div class="it-person-company" data-toggle="tooltip" title="{{ @$row->company_name }}">
                        {{ ucwords(mb_substr(@$row->company_name, 0, 12)) }}
                    </div>
                </div>
            </td>

            {{-- Stage --}}
            <td>
                @php
                    $stageName = '';
                    if ($row->master_status_id == 8) {
                        $stageName = 'Private';
                    }
                    if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                        $stageName = @$row->child_status_name;
                    }

                    // Map stage name to pipeline badge colors (INW/SOU/SCR/EVA/NEG/OFF/JOI/REJ)
                    $stageNameLower = strtolower(@$row->master_status_name);
                    if (str_contains($stageNameLower, 'review') || str_contains($stageNameLower, 'inw')) {
                        $stageBadgeClass = 'it-badge-yellow';   // INW – In Review
                    } elseif (str_contains($stageNameLower, 'sourc') || str_contains($stageNameLower, 'sou')) {
                        $stageBadgeClass = 'it-badge-gray';     // SOU – Sourcing
                    } elseif (str_contains($stageNameLower, 'screen') || str_contains($stageNameLower, 'scr')) {
                        $stageBadgeClass = 'it-badge-purple';   // SCR – Screening
                    } elseif (str_contains($stageNameLower, 'evaluat') || str_contains($stageNameLower, 'eva')) {
                        $stageBadgeClass = 'it-badge-blue';     // EVA – Evaluation
                    } elseif (str_contains($stageNameLower, 'negot') || str_contains($stageNameLower, 'neg')) {
                        $stageBadgeClass = 'it-badge-pink';     // NEG – Negotiation
                    } elseif (str_contains($stageNameLower, 'offer') || str_contains($stageNameLower, 'off')) {
                        $stageBadgeClass = 'it-badge-orange';   // OFF – Offer
                    } elseif (str_contains($stageNameLower, 'join') || str_contains($stageNameLower, 'joi')) {
                        $stageBadgeClass = 'it-badge-green';    // JOI – Joining
                    } elseif (str_contains($stageNameLower, 'reject') || str_contains($stageNameLower, 'rej')) {
                        $stageBadgeClass = 'it-badge-red';      // REJ – Rejected
                    } else {
                        $stageBadgeClass = 'it-badge-blue';     // fallback
                    }

                @endphp
                <div class="it-stage-cell">
                    <span class="it-badge {{ $stageBadgeClass }}">{{ mb_substr(@$row->master_status_name, 0, 10) }}</span>
                    @if($stageName)
                        <span class="text-muted fw-bold d-block fs-7" style="font-size:10px;color:var(--it-text-muted);margin-top:2px;">{{ $stageName }}</span>
                    @endif
                </div>
            </td>

            {{-- Feedback (view-only — no Add Feedback for bench candidates) --}}
            <td>
                @php
                    $latestFb = $row->latest_feedback_enriched ?? null;
                    $hasAnyFeedback = !empty($latestFb);
                    $attId = (int) (@$latestFb->available_status ?? 0);

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
                @endphp
                <div class="it-feedback-cell">
                    @if($attended)
                        {{-- Attended: show result + recommendation + View Feedback button --}}
                        <span style="font-size:10px;font-weight:600;color:{{ $resultColor }};display:inline-flex;align-items:center;gap:3px;">
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
                        {{-- Not attended: show attendance reason + View button --}}
                        <span style="font-size:10px;font-weight:600;color:var(--it-warning);display:inline-flex;align-items:center;gap:3px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            {{ $attLabelMap[$attId] ?? 'Not Attended' }}
                        </span>
                        <button type="button" class="it-feedback-btn show-feedback" data-row-id="{{ $row->id }}" style="border:1px solid var(--it-border);color:var(--it-text-muted);background:#fff;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View
                        </button>
                    @else
                        {{-- No feedback recorded yet — view only, no add button --}}
                        <span style="font-size:10px;font-weight:600;color:var(--it-warning);display:inline-flex;align-items:center;gap:3px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Pending
                        </span>
                        <span style="font-size:10px;color:var(--it-text-muted);">Feedback Pending</span>
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
