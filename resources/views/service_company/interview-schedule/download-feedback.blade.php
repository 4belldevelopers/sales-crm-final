<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Interview Feedback Report</title>
<style>
    @font-face { font-family: 'Poppins'; font-weight: normal; font-style: normal; src: url('{{ str_replace('\\','/', public_path('fonts/Poppins-Regular.ttf')) }}') format('truetype'); }
    @font-face { font-family: 'Poppins'; font-weight: bold; font-style: normal; src: url('{{ str_replace('\\','/', public_path('fonts/Poppins-SemiBold.ttf')) }}') format('truetype'); }
    @page { margin: 26px 26px 40px 26px; }
    * { box-sizing: border-box; }
    body { font-family: "Poppins", "DejaVu Sans", sans-serif; font-size: 11px; color: #334155; margin: 0; }
    /* Stars use DejaVu Sans because Poppins has no star glyph (U+2605) */
    .stars, .star-on, .star-off { font-family: "DejaVu Sans", sans-serif; }

    /* ===== Header ===== */
    .report-head { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 8px; }
    .report-head td { padding: 16px 20px; vertical-align: middle; }
    .brand { font-size: 17px; font-weight: bold; color: #ffffff; letter-spacing: .5px; }
    .brand .dot { color: #f59e0b; }
    .brand-sub { font-size: 10px; color: #cbd5e1; margin-top: 3px; }
    .head-right { text-align: right; color: #cbd5e1; font-size: 9.5px; }
    .head-right .big { color: #ffffff; font-size: 12px; font-weight: bold; }

    /* ===== Candidate summary ===== */
    .panel { border: 1px solid #e2e8f0; border-radius: 8px; margin-top: 14px; }
    .panel-title { background: #f8fafc; color: #0f172a; font-weight: bold; font-size: 12px; padding: 9px 14px; border-bottom: 1px solid #e2e8f0; border-radius: 8px 8px 0 0; }
    .info-table { width: 100%; border-collapse: collapse; }
    .info-table td { border-bottom: 1px solid #f1f5f9; padding: 8px 12px; vertical-align: top; font-size: 10.5px; }
    .info-table .lbl { width: 14%; color: #64748b; font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: .4px; background: #fcfdfe; }
    .info-table .val { width: 19.3%; color: #0f172a; font-weight: bold; }
    .info-table .val small { display: block; color: #94a3b8; font-weight: normal; font-size: 9.5px; margin-top: 1px; }

    /* ===== Feedback card ===== */
    .fb-card { border: 1px solid #e2e8f0; border-radius: 8px; margin-top: 12px; }
    .fb-head { width: 100%; border-collapse: collapse; background: #f1f5f9; border-radius: 8px 8px 0 0; }
    .fb-head td { padding: 9px 14px; vertical-align: middle; }
    .av { display: inline-block; width: 26px; height: 26px; background: #2563eb; color: #fff; border-radius: 13px; text-align: center; line-height: 26px; font-size: 11px; font-weight: bold; }
    .fb-name { font-size: 12px; font-weight: bold; color: #0f172a; }
    .fb-role { font-size: 9.5px; color: #64748b; }
    .fb-date { font-size: 9px; color: #94a3b8; }
    .fb-body { padding: 2px 14px 10px 14px; }

    .sec { background: #eff6ff; color: #1d4ed8; font-weight: bold; font-size: 10.5px; padding: 5px 10px; margin: 9px 0 5px; border-left: 3px solid #2563eb; border-radius: 0 4px 4px 0; }
    table.grid tr, table.kv tr { page-break-inside: avoid; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 9.5px; font-weight: bold; }
    .b-green { background: #dcfce7; color: #16a34a; }
    .b-red { background: #fee2e2; color: #dc2626; }
    .b-amber { background: #fef3c7; color: #d97706; }
    .b-blue { background: #eff6ff; color: #2563eb; }
    .b-grey { background: #f1f5f9; color: #64748b; }

    .stars { font-size: 13px; letter-spacing: 1px; }
    .star-on { color: #f59e0b; }
    .star-off { color: #cbd5e1; }

    table.grid { width: 100%; border-collapse: collapse; }
    table.grid th { background: #f8fafc; text-align: left; padding: 6px 9px; font-size: 8.5px; color: #64748b; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; }
    table.grid td { padding: 7px 9px; font-size: 10.5px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    .skill-name { font-weight: bold; color: #0f172a; }
    .skill-exp { color: #94a3b8; font-size: 9px; }

    table.kv { width: 100%; border-collapse: collapse; }
    table.kv td { padding: 7px 9px; border: 1px solid #f1f5f9; font-size: 10.5px; vertical-align: top; }
    table.kv .k { color: #64748b; font-size: 8.5px; font-weight: bold; text-transform: uppercase; background: #fcfdfe; width: 16%; }
    table.kv .v { color: #0f172a; font-weight: bold; width: 17.3%; }

    .muted { color: #94a3b8; }
    .note { font-size: 10px; color: #475569; line-height: 1.5; }
    .chip { display: inline-block; background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; border-radius: 5px; padding: 2px 8px; font-size: 9px; margin: 0 4px 4px 0; }

    .empty { padding: 22px; text-align: center; color: #94a3b8; }
    .foot { margin-top: 10px; font-size: 8.5px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>

@php
    use Carbon\Carbon;

    $schedule        = @$interviewSchedule->schedule_date;
    $schedule_dt     = $schedule ? date('d M, Y h:i A', strtotime($schedule)) : '—';
    $candidateName   = ucwords(@$interviewSchedule->full_name ?: '—');
    $candidateInit   = strtoupper(substr(@$interviewSchedule->full_name ?: 'C', 0, 1));

    $attLabelMap   = [1=>'Attended', 2=>'No Show', 3=>'Rescheduled', 4=>'Cancelled by Company', 6=>'Other Reason', 7=>'Cancelled by Candidate'];
    $resultLabels  = ['selected'=>'Selected', 'hold'=>'Hold', 'rejected'=>'Rejected', 'fake_proxy'=>'Fake / Proxy', 'feedback_pending'=>'Feedback Pending'];
    $recLabels     = ['strong_hire'=>'Strong Hire', 'hire'=>'Hire', 'consider'=>'Consider', 'no_hire'=>'No Hire', 'strong_no_hire'=>'Strong No Hire'];
    $compFitLabels = ['within_budget'=>'Within Budget', 'slightly_above'=>'Slightly Above Budget', 'significantly_above'=>'Significantly Above Budget', 'negotiable'=>'Negotiable', 'not_discussed'=>'Not Discussed'];
    $joinProbLabels= ['high'=>'High (80 - 100%)', 'medium'=>'Medium (50 - 80%)', 'low'=>'Low (< 50%)'];
    $riskLabels    = ['multiple_offers'=>'Holding Multiple Offers', 'counter_offer'=>'Counter Offer Expected', 'relocation'=>'Relocation Concern', 'salary_gap'=>'Salary Gap', 'notice_period'=>'Long Notice Period', 'role_preference'=>'Role / Domain Preference', 'others'=>'Others'];
    $noticeLabels  = ['15'=>'15 Days', '30'=>'30 Days', '60'=>'60 Days', '90'=>'90 Days'];
    $behavioralNames = ['communication skills', 'problem solving', 'leadership', 'cultural fit'];

    // Render 5 stars: gold for filled, grey for empty
    $stars = function ($n) {
        $n = (int) $n; $h = '<span class="stars">';
        for ($i = 1; $i <= 5; $i++) { $h .= '<span class="' . ($i <= $n ? 'star-on' : 'star-off') . '">&#9733;</span>'; }
        return $h . '</span>';
    };
    $blank = function ($v) { $v = trim((string) $v); return $v === '' ? '<span class="muted">—</span>' : e($v); };
@endphp

<!-- ===== Header ===== -->
<table class="report-head">
    <tr>
        <td>
            <div class="brand">Jero<span class="dot">.</span>works</div>
            <div class="brand-sub">Interview Feedback Report</div>
        </td>
        <td class="head-right">
            <div class="big">{{ $candidateName }}</div>
            <div>Generated on {{ date('d M, Y h:i A') }}</div>
        </td>
    </tr>
</table>

<!-- ===== Candidate summary ===== -->
<div class="panel">
    <div class="panel-title">Candidate &amp; Interview Details</div>
    <table class="info-table">
        <tr>
            <td class="lbl">Candidate</td>
            <td class="val">{{ $candidateName }}<small>{{ @$interviewSchedule->current_designation ?: '—' }}</small></td>
            <td class="lbl">Position</td>
            <td class="val">{{ ucwords(@$interviewSchedule->job_title ?: '—') }}<small>Job ID: {{ @$interviewSchedule->job_id ?: '—' }}</small></td>
        </tr>
        <tr>
            <td class="lbl">Company</td>
            <td class="val">{{ ucwords(@$interviewSchedule->client_name ?: '—') }}</td>
            <td class="lbl">Stage</td>
            <td class="val">{{ ucwords(@$interviewSchedule->master_status_name ?: '—') }}<small>{{ ucwords(@$interviewSchedule->child_status_name) }}</small></td>
        </tr>
        <tr>
            <td class="lbl">Interview Type</td>
            <td class="val">{{ ucwords(@$interviewSchedule->mode_name ?: '—') }}<small>{{ ucwords(@$interviewSchedule->mode_lavel_name) }}</small></td>
            <td class="lbl">Date &amp; Time</td>
            <td class="val">{{ $schedule_dt }}</td>
        </tr>
        <tr>
            <td class="lbl">Interviewers</td>
            <td class="val">{{ ucwords(@$interviewSchedule->interviewers ?: '—') }}</td>
            <td class="lbl">Recruiter</td>
            <td class="val">{{ ucwords(@$interviewSchedule->upload_by_name ?: '—') }}<small>{{ @$interviewSchedule->company_name }}</small></td>
        </tr>
    </table>
</div>

<!-- ===== Per-interviewer feedback ===== -->
@forelse(($feedbackMapped ?? []) as $feedbackId => $fb)
    @php
        $fbInit = strtoupper(substr(trim(@$fb['by_name'] ?: 'I'), 0, 1));
        $fbDate = !empty($fb['created_at']) ? date('d M Y, h:i A', strtotime($fb['created_at'])) : '—';

        // Result: prefer new interview_result, else legacy feedback status name
        $resKey  = @$fb['interview_result'];
        $resText = (!empty($resKey) && isset($resultLabels[$resKey])) ? $resultLabels[$resKey] : '';
        $fbStatusId = intval(@$fb['interviewStatus']);
        if ($resText === '' && $fbStatusId > 0 && isset($feedback_status[$fbStatusId])) { $resText = ucwords($feedback_status[$fbStatusId]['name']); }
        $resClass = 'b-grey';
        if (in_array($resKey, ['selected']) || $fbStatusId == 1) $resClass = 'b-green';
        elseif (in_array($resKey, ['rejected','fake_proxy']) || in_array($fbStatusId, [3,4])) $resClass = 'b-red';
        elseif (in_array($resKey, ['hold','feedback_pending']) || in_array($fbStatusId, [2,5])) $resClass = 'b-amber';

        // Recommendation
        $recKey  = @$fb['overall_recommendation'];
        $recText = (!empty($recKey) && isset($recLabels[$recKey])) ? $recLabels[$recKey] : '';
        $recClass = 'b-grey';
        if (in_array($recKey, ['strong_hire','hire'])) $recClass = 'b-green';
        elseif (in_array($recKey, ['no_hire','strong_no_hire'])) $recClass = 'b-red';
        elseif ($recKey === 'consider') $recClass = 'b-amber';

        // Attendance
        $attId = intval(@$fb['candidateStatus']);
        $attText = $attLabelMap[$attId] ?? (isset($available_status[$attId]) ? ucwords($available_status[$attId]['name']) : '');

        // Split skills into technical vs behavioral
        $techSkills = []; $behSkills = [];
        foreach ((@$fb['feedbackdata'] ?: []) as $sk) {
            $nm = strtolower(trim(@$sk['skills']));
            if ($nm === '') continue;
            if (in_array($nm, $behavioralNames)) $behSkills[] = $sk; else $techSkills[] = $sk;
        }

        // Compensation
        $fbRisk = !empty($fb['risk_factors']) ? array_filter(array_map('trim', explode(',', $fb['risk_factors']))) : [];
    @endphp

    <div class="fb-card">
        <table class="fb-head">
            <tr>
                <td width="58%">
                    <table style="border-collapse:collapse;"><tr>
                        <td style="padding:0 8px 0 0;"><span class="av">{{ $fbInit }}</span></td>
                        <td>
                            <span class="fb-name">{{ ucwords(@$fb['by_name'] ?: 'Interviewer') }}</span><br>
                            <span class="fb-role">{{ @$fb['designation'] ?: 'Interviewer' }}</span><br>
                            <span class="fb-date">Submitted: {{ $fbDate }}</span>
                        </td>
                    </tr></table>
                </td>
                <td width="42%" align="right">
                    @if($resText)<span class="badge {{ $resClass }}">{{ $resText }}</span>@endif
                    @if($recText)<span class="badge {{ $recClass }}">{{ $recText }}</span>@endif
                    <br>
                    <span style="font-size:9px;color:#64748b;">Overall:</span> {!! $stars(@$fb['star_ratings']) !!}
                    <span style="font-size:9px;color:#64748b;">({{ intval(@$fb['star_ratings']) }}/5)</span>
                </td>
            </tr>
        </table>

        <div class="fb-body">

            <div class="sec">1 &nbsp; Interview Attendance</div>
            <div>
                @if($attText)
                    <span class="badge {{ $attId == 1 ? 'b-green' : 'b-amber' }}">{{ $attText }}</span>
                @else
                    <span class="muted">—</span>
                @endif
            </div>

            <div class="sec">2 &nbsp; Technical Assessment</div>
            @if(count($techSkills))
                <table class="grid">
                    <tr><th width="28%">Skill</th><th width="18%">Experience</th><th width="22%">Rating</th><th width="32%">Comment</th></tr>
                    @foreach($techSkills as $sk)
                        <tr>
                            <td><span class="skill-name">{{ ucfirst(@$sk['skills']) }}</span></td>
                            <td><span class="skill-exp">{{ @$sk['experience'] ?: '—' }}</span></td>
                            <td>{!! $stars(@$sk['ratings']) !!}</td>
                            <td>{!! $blank(@$sk['remarks']) !!}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <div class="muted note">No technical assessment provided.</div>
            @endif

            <div class="sec">3 &nbsp; Behavioral Assessment</div>
            @if(count($behSkills))
                <table class="grid">
                    <tr><th width="32%">Competency</th><th width="26%">Rating</th><th width="42%">Comment</th></tr>
                    @foreach($behSkills as $sk)
                        <tr>
                            <td><span class="skill-name">{{ ucwords(@$sk['skills']) }}</span></td>
                            <td>{!! $stars(@$sk['ratings']) !!}</td>
                            <td>{!! $blank(@$sk['remarks']) !!}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <div class="muted note">No behavioral assessment provided.</div>
            @endif

            @if(!empty($canViewSalary))
            <div class="sec">4 &nbsp; Compensation Assessment</div>
            <table class="kv">
                <tr>
                    <td class="k">Current CTC</td><td class="v">{!! $blank(@$fb['current_ctc']) !!}</td>
                    <td class="k">Expected CTC</td><td class="v">{!! $blank(@$fb['expected_ctc']) !!}</td>
                    <td class="k">Offered CTC</td><td class="v">{!! $blank(@$fb['offered_ctc']) !!}</td>
                </tr>
                <tr>
                    <td class="k">Maximum Budget</td><td class="v">{!! $blank(@$fb['max_budget']) !!}</td>
                    <td class="k">Notice Period</td><td class="v">{!! $blank($noticeLabels[@$fb['notice_period']] ?? @$fb['notice_period']) !!}</td>
                    <td class="k">Joining Date</td><td class="v">{!! $blank(@$fb['joining_date'] ? date('d M Y', strtotime($fb['joining_date'])) : '') !!}</td>
                </tr>
                <tr>
                    <td class="k">Compensation Fit</td><td class="v">{!! $blank($compFitLabels[@$fb['compensation_fit']] ?? '') !!}</td>
                    <td class="k">Joining Probability</td><td class="v">{!! $blank($joinProbLabels[@$fb['joining_probability']] ?? '') !!}</td>
                    <td class="k">Risk Factors</td>
                    <td class="v">
                        @forelse($fbRisk as $rf)
                            <span class="chip">{{ $riskLabels[$rf] ?? ucwords(str_replace('_',' ',$rf)) }}</span>
                        @empty
                            <span class="muted">—</span>
                        @endforelse
                    </td>
                </tr>
                <tr>
                    <td class="k">Comments</td>
                    <td class="v" colspan="5" style="font-weight:normal;">{!! $blank(@$fb['compensation_comments']) !!}</td>
                </tr>
            </table>
            @endif

            <div class="sec">5 &nbsp; Interview Result</div>
            <div>@if($resText)<span class="badge {{ $resClass }}">{{ $resText }}</span>@else<span class="muted">—</span>@endif</div>

            <div class="sec">6 &nbsp; Overall Recommendation</div>
            <div>@if($recText)<span class="badge {{ $recClass }}">{{ $recText }}</span>@else<span class="muted">—</span>@endif</div>

            <div class="sec">7 &nbsp; Final Comments</div>
            <div class="note">{!! $blank(@$fb['message']) !!}</div>

        </div>
    </div>
@empty
    <div class="panel"><div class="empty">No feedback has been submitted for this interview yet.</div></div>
@endforelse

<div class="foot">This report was generated from Jero.works — Interview Tracker. Confidential.</div>

</body>
</html>
