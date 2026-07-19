<?php
use App\Helper\GeneralMethod;
$defaultTimeZone = GeneralMethod::defaultTimeZone();
$defaultTimeFormat = GeneralMethod::defaultTimeFormat();
$currency_code =  GeneralMethod::defaultCurrency();
$country_code =  GeneralMethod::countryCodeFun();
?>
<style>
    #interviewDate::-webkit-inner-spin-button,
    #interviewDate::-webkit-calendar-picker-indicator {
        display: none;
        -webkit-appearance: none;
    }
    .hasTime{
        top: 309.344px;
        left: 483.25px !important;
        right: auto;
    }
    .flatpickr-calendar.open {
        position: absolute !important;
        z-index: 2147483647 !important
    }
    .swal2-container {
        z-index: 2247483647 !important;
    }
    #kt_activitiesIntTeamtra {
        z-index: 99999999999 !important;
    }
    .tox-statusbar__branding {
        display: none
    }

    /* ===================================================================
       Interview Schedule drawer content — enterprise right-drawer layout.
       Scoped under .isd-wrap so it never leaks into the rest of the app.
       Real inputs/selects keep their original ids/names so every existing
       script (submitForm, getInterviewModeChild, getVideoMeetingUrl,
       accessEmailTemplate, the panel/contact-person modal) works unchanged.
       =================================================================== */
    .isd-wrap { font-family: 'Poppins', sans-serif; color: #3f4254; }
    .isd-wrap * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

    .isd-info-strip { display: flex; align-items: center; gap: 26px; padding: 14px 20px; border-bottom: 1px solid #eef0f4; flex-wrap: wrap; }
    .isd-avatar { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0; }
    .isd-info-cand { display: flex; align-items: center; gap: 12px; min-width: 200px; }
    .isd-info-name { font-size: 13.5px; font-weight: 700; color: #181c32; }
    .isd-info-role { font-size: 11px; color: #7e8299; margin-top: 1px; }
    .isd-info-block { display: flex; flex-direction: column; gap: 2px; }
    .isd-info-label { font-size: 10.5px; color: #7e8299; font-weight: 500; }
    .isd-info-value { font-size: 12.5px; font-weight: 700; color: #181c32; }

    .isd-body { display: flex; align-items: flex-start; }
    .isd-main { flex: 1 1 66%; min-width: 0; padding: 16px 20px 30px; }
    .isd-sidebar { flex: 0 0 34%; max-width: 320px; padding: 16px 16px 30px; border-left: 1px solid #eef0f4; background: #fbfbfd; }

    .isd-sec { margin-bottom: 18px; }
    .isd-card { background: #fff; border: 1px solid #eef0f4; border-radius: 12px; padding: 16px 18px; margin-bottom: 14px; }
    .isd-sec-head { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .isd-sec-num { width: 22px; height: 22px; border-radius: 50%; color: #fff; font-size: 10.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .isd-sec-title { font-size: 13px; font-weight: 700; color: #181c32; }
    .isd-sec-sub { font-size: 10.5px; color: #7e8299; font-weight: 400; margin-top: 1px; }

    .isd-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
    .isd-grid + .isd-grid { margin-top: 12px; }
    .isd-field label { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #3f4254; margin-bottom: 5px; }
    .isd-field label .req { color: #f64e60; }
    .isd-field label i { color: #a1a5b7; font-size: 11px; }
    .isd-field input, .isd-field select, .isd-field textarea { width: 100%; border: 1.5px solid #e4e6ef; border-radius: 8px; padding: 7px 9px; font-size: 12px; color: #181c32; background: #f8f9fb; }
    .isd-field input:disabled, .isd-field select:disabled, .isd-field textarea:disabled { color: #b5b5c3; cursor: not-allowed; background: #f1f1f4; }
    .isd-soon { display: inline-flex; align-items: center; font-size: 9px; font-weight: 600; color: #a1a5b7; background: #f1f1f4; border-radius: 5px; padding: 2px 6px; margin-left: 4px; text-transform: uppercase; letter-spacing: .3px; }

    .isd-datetime-pill { display: flex; align-items: center; border: 1.5px solid #e4e6ef; border-radius: 8px; background: #f8f9fb; padding: 0 9px; }
    .isd-datetime-pill i { color: #7e8299; font-size: 12px; margin-right: 8px; }
    .isd-datetime-pill input { border: none !important; background: transparent !important; padding: 7px 4px !important; width: auto; flex: 1 1 auto; min-width: 0; }
    .isd-datetime-pill .sep { width: 1px; height: 18px; background: #e4e6ef; flex-shrink: 0; }

    .isd-cal-row { display: flex; gap: 10px; }
    .isd-cal-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1.5px solid #e4e6ef; border-radius: 8px; padding: 9px; font-size: 11.5px; font-weight: 600; color: #3f4254; background: #fff; cursor: pointer; transition: .15s; }
    .isd-cal-btn:hover { border-color: #3e97ff; color: #3e97ff; background: #f5f9ff; }
    .isd-cal-btn i { font-size: 13px; }

    .isd-tile-row { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
    .isd-tile { flex: 1 1 100px; max-width: 138px; border: 1.5px solid #eef0f4; border-radius: 10px; padding: 10px 8px; text-align: center; cursor: pointer; background: #fff; position: relative; transition: .15s; }
    .isd-tile:hover { border-color: #cdd4e0; }
    .isd-tile.is-selected { border-color: #3e97ff; background: #f5f9ff; box-shadow: 0 0 0 1px #3e97ff inset; }
    .isd-tile .ic { width: 28px; height: 28px; border-radius: 8px; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; font-size: 13px; background: #eef4ff; color: #3e97ff; }
    .isd-tile .nm { font-size: 10.5px; font-weight: 700; color: #181c32; }
    .isd-tile.is-disabled { cursor: not-allowed; opacity: .55; }
    .isd-tile .chk { position: absolute; top: 6px; right: 6px; width: 14px; height: 14px; border-radius: 50%; background: #3e97ff; color: #fff; display: none; align-items: center; justify-content: center; font-size: 8px; }
    .isd-tile.is-selected .chk { display: flex; }
    .isd-leaf-label { font-size: 10px; font-weight: 600; color: #a1a5b7; text-transform: uppercase; letter-spacing: .3px; margin: 2px 0 8px; }

    .isd-link-row { position: relative; }
    .isd-link-row input { padding-right: 88px; }
    .isd-link-actions { position: absolute; right: 5px; top: 5px; display: flex; gap: 4px; }
    .isd-icon-btn { width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e4e6ef; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #7e8299; font-size: 10px; }
    .isd-icon-btn:hover { background: #f8f9fb; color: #3e97ff; }

    .isd-panel-add { display: flex; gap: 8px; margin-bottom: 12px; }
    .isd-panel-add input { flex: 1; }
    .isd-btn-add-panel { border: 1px solid #3e97ff; color: #3e97ff; background: #fff; border-radius: 8px; padding: 0 14px; font-size: 11.5px; font-weight: 600; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; }
    .isd-btn-add-panel:hover { background: #f5f9ff; }
    .isd-panel-list { display: flex; flex-direction: column; gap: 10px; }
    .isd-panel-empty { font-size: 11.5px; color: #a1a5b7; text-align: center; padding: 10px 0; }
    .isd-panel-row { display: flex; align-items: center; gap: 12px; border: 1.5px solid #eef0f4; border-radius: 10px; padding: 10px 12px; }
    .isd-panel-row .isd-avatar { width: 38px; height: 38px; font-size: 13px; }
    .isd-panel-info { min-width: 0; flex: 1 1 auto; }
    .isd-panel-name { font-size: 12.5px; font-weight: 700; color: #181c32; word-break: break-word; }
    .isd-panel-email { font-size: 10.5px; color: #7e8299; margin-top: 2px; }
    .isd-panel-email i { margin-right: 5px; color: #a1a5b7; }
    .isd-panel-tags { display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0; }
    .isd-role-tag { font-size: 10px; font-weight: 600; color: #3e97ff; background: #eef4ff; border-radius: 6px; padding: 4px 9px; white-space: nowrap; }
    .isd-panel-del { width: 26px; height: 26px; border-radius: 7px; border: none; background: transparent; color: #f64e60; cursor: pointer; font-size: 12px; flex-shrink: 0; }
    .isd-panel-del:hover { background: #fff5f8; }
    .isd-check-avail { font-size: 10.5px; color: #a1a5b7; margin-top: 12px; display: inline-flex; align-items: center; gap: 6px; }

    .isd-notify-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .isd-notify { display: flex; align-items: flex-start; gap: 9px; border: 1.5px solid #eef0f4; border-radius: 9px; padding: 8px 10px; }
    .isd-notify i { color: #7e8299; font-size: 12px; margin-top: 1px; }
    .isd-notify input[type=checkbox] { margin-top: 2px; width: 14px; height: 14px; accent-color: #3e97ff; flex-shrink: 0; }
    .isd-notify .t { font-size: 11px; font-weight: 600; color: #181c32; }
    .isd-notify .d { font-size: 9.5px; color: #7e8299; }
    .isd-notify.is-disabled .t, .isd-notify.is-disabled .d { color: #b5b5c3; }
    .isd-notify.is-disabled i { color: #cdd4e0; }

    .isd-sidebar-card-title { font-size: 12px; font-weight: 700; color: #181c32; display: flex; align-items: center; gap: 7px; margin-bottom: 10px; }
    .isd-sidebar-card-title .dot { width: 6px; height: 6px; border-radius: 50%; background: #3e97ff; flex-shrink: 0; }
    .isd-sum-row { display: flex; align-items: flex-start; gap: 9px; padding: 6px 0; border-bottom: 1px dashed #f1f1f4; }
    .isd-sum-row:last-child { border-bottom: none; }
    .isd-sum-ico { width: 24px; height: 24px; border-radius: 7px; background: #f5f9ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 10px; flex-shrink: 0; }
    .isd-sum-label { font-size: 9.5px; color: #7e8299; font-weight: 500; }
    .isd-sum-value { font-size: 11.5px; font-weight: 700; color: #181c32; margin-top: 1px; word-break: break-word; }
    .isd-sum-value.muted { color: #b5b5c3; font-weight: 600; }
    .isd-status-chip { display: inline-block; padding: 3px 9px; border-radius: 6px; font-size: 10px; font-weight: 700; }
    .isd-status-chip.incomplete { background: #fff4de; color: #ffa800; }
    .isd-status-chip.ready { background: #e3fbe9; color: #06c732; }

    .isd-avail-row { display: flex; align-items: center; gap: 9px; padding: 6px 0; border-bottom: 1px dashed #f1f1f4; font-size: 11px; }
    .isd-avail-row:last-child { border-bottom: none; }
    .isd-avail-row i { color: #06c732; font-size: 11px; width: 16px; text-align: center; }
    .isd-avail-empty { font-size: 10.5px; color: #a1a5b7; text-align: center; padding: 8px 0; }

    .isd-quick-row { display: flex; justify-content: space-between; gap: 10px; padding: 5px 0; font-size: 11px; }
    .isd-quick-row .k { color: #7e8299; display: flex; align-items: center; gap: 6px; }
    .isd-quick-row .k i { color: #a1a5b7; font-size: 10px; width: 12px; }
    .isd-quick-row .v { color: #181c32; font-weight: 700; text-align: right; }

    .isd-hidden-native { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; overflow: hidden; }

    .isd-email-inline-head { font-size: 13px; font-weight: 700; color: #181c32; margin-bottom: 14px; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
    crossorigin="anonymous"></script>

<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
    if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)
        CKEDITOR.tools.enableHtml5Elements(document);
    CKEDITOR.config.height = 350;
    CKEDITOR.config.width = 'auto';

    var initSample = (function() {
        var wysiwygareaAvailable = isWysiwygareaAvailable(),
            isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');

        return function() {
            var editorElement = CKEDITOR.document.getById('editor1');

            if (isBBCodeBuiltIn) {
                editorElement.setHtml(
                    'Hello world!\n\n' +
                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                );
            }
            if (wysiwygareaAvailable) {
                CKEDITOR.replace('editor1');
            } else {
                editorElement.setAttribute('contenteditable', 'true');
                CKEDITOR.inline('editor1');
            }
        };

        function isWysiwygareaAvailable() {
            if (CKEDITOR.revision == ('%RE' + 'V%')) {
                return true;
            }
            return !!CKEDITOR.plugins.get('wysiwygarea');
        }

    })();

    initSample();
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    setTimeout(() => {
          var defaultTimeFormat = '{{$defaultTimeFormat}}';

          if(defaultTimeFormat==1){
            flatpickr("#interviewTime", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                minuteIncrement: 1
            });
          }else{
            flatpickr("#interviewTime", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1
            });
          }

            flatpickr("#interviewDate", {
                dateFormat: "d-m-Y",
                altInput: true,
                altFormat: "d-M-Y",
                minDate: "today",
                onReady: function (selectedDates, dateStr, instance) {
                    instance.altInput.style.width = "110px";
                }
            });
      }, 500);

    function accessEmailTemplate(template_id, application_id) {

        var interviewers = $('#panellist').val();
        var videoUrl = $('#url').val();
        var interview_mode = $('#interview_mode').val();
        var jobId = $('#jobId').val();
        var interviewDate = $('#interviewDate').val();
        var interviewTime = $('#interviewTime').val();
        var timezone = $('#timezone').val();
        if (template_id > 0) {

            var id = template_id;
            var url = "{{ route('service_company.job-applications.accessEmailTemplate', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'template_id': template_id,
                    'interviewDate': interviewDate,
                    'application_id': application_id,
                    'interviewers': interviewers,
                    'videoUrl': videoUrl,
                    'interview_mode': interview_mode,
                    'interviewTime': interviewTime,
                    'timezone': timezone,
                    'jobId': jobId
                },
                beforeSend: function() {
                    $('#childInterview').html('<img src="' + baseurl + '" height="35px" width="100%">');
                },
                success: function(response) {
                    $('#subject').val(response.template_subject);
                    $('#editor1').html(response.template_description);
                    CKEDITOR.instances.editor1.setData(response.template_description);
                    setTimeout(() => {
                        $('#editor1').val(response.template_description);
                    }, 500);
                },
                complete: function(data) {}
            });
        } else {
            $('#subject').val('');
            $('#editor').html('');
        }
    }
</script>

<form class="form fv-plugins-bootstrap fv-plugins-framework" id="app_interview_create_frm" name="app_interview_create_frm"
    method="post" enctype="multipart/form-data">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="application_id" id="application_id" value="{{ @$application->id }}" />
    <input type="hidden" name="wiseId" id="wiseId" value="{{ @$application->wiseId }}" />
    <input type="hidden" name="jobId" id="jobId" value="{{ @$application->jobId }}" />
    <input type="hidden" name="master_status_id" id="master_status_id" value="{{ @$application->master_status_id }}" />
    <input type="hidden" name="child_status_id" id="child_status_id" value="{{ @$application->child_status_id }}" />

    @php
        $isdFullName = trim(@$application->full_name);
        $isdInitial = $isdFullName !== '' ? strtoupper($isdFullName[0]) : '?';

        // Candidate-proposed interview slots, captured during the candidate's own
        // application submission (job_applications.interviewDateOne..Six /
        // interview_mode_one..six) — real data, not tracked anywhere else.
        $isdAvailabilitySlots = [];
        for ($isdI = 1; $isdI <= 6; $isdI++) {
            $isdDateField = 'interviewDate' . ['One', 'Two', 'Three', 'Four', 'Five', 'Six'][$isdI - 1];
            $isdModeField = 'interview_mode_' . ['one', 'two', 'three', 'four', 'five', 'six'][$isdI - 1];
            $isdSlotDate = @$application->{$isdDateField};
            if (!empty($isdSlotDate)) {
                $isdAvailabilitySlots[] = [
                    'date' => $isdSlotDate,
                    'mode' => @$application->{$isdModeField},
                ];
            }
        }
    @endphp

    <div id="div_interview_schedule_first" class="isd-wrap">
        <div class="isd-info-strip">
            <div class="isd-info-cand">
                <div class="isd-avatar bg-light-primary text-primary">{{ $isdInitial }}</div>
                <div>
                    <div class="isd-info-name">{{ ucwords($isdFullName) }}</div>
                    <div class="isd-info-role">{{ ucwords(@$application->current_designation) }}</div>
                </div>
            </div>
            <div class="isd-info-block">
                <span class="isd-info-label">Applied For</span>
                <span class="isd-info-value">{{ @$application->job_title }}</span>
            </div>
            <div class="isd-info-block">
                <span class="isd-info-label">Job ID</span>
                <span class="isd-info-value">{{ @$application->jobId }}</span>
            </div>
            <div class="isd-info-block">
                <span class="isd-info-label">Current Stage</span>
                <span class="badge fw-bolder {{ @$application->master_status_style }} px-2 py-1">{{ trim(@$application->master_status_name) }}</span>
            </div>
        </div>

        <div class="isd-body">
            <div class="isd-main">

                <!-- Section 1: Interview Details -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#3e97ff;">1</span>
                        <div><div class="isd-sec-title">Interview Details</div><div class="isd-sec-sub">When &amp; for how long</div></div>
                    </div>
                    <div class="isd-grid">
                        <div class="isd-field">
                            <label><i class="fa fa-calendar"></i> Date &amp; Time <span class="req">*</span></label>
                            <div class="isd-datetime-pill">
                                <i class="fa fa-calendar"></i>
                                <input class="form-control" placeholder="Select date" name="interviewDate" id="interviewDate"
                                    value="{{ (($datadate) ?  \Carbon\Carbon::parse(@$datadate)->format('d-m-Y') : '') }}" type="text" readonly="readonly">
                                <span class="sep"></span>
                                <input class="form-control" placeholder="Select time" name="interviewTime" id="interviewTime"
                                    value="{{ (($datatime) ? \Carbon\Carbon::parse(@$datatime)->format('h:i A') : '') }}" type="text" readonly="readonly">
                            </div>
                        </div>
                        <div class="isd-field">
                            <label><i class="fa fa-globe"></i> Timezone <span class="req">*</span></label>
                            <select name="timezone" id="timezone" aria-label="Select a Time Zone" data-placeholder="Select a time zone...">
                                <option value="">Select a timezone...</option>
                                @if(!empty($timezone))
                                    <optgroup label="Popular Time Zones">
                                        @foreach($timezone as $tval)
                                            @if($tval->priority)
                                                @php $sel = ($defaultTimeZone === $tval->time_zone) ? 'selected="selected"' : ''; @endphp
                                                {!! '<option value="'.$tval->timezone_abbreviation.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' - '.$tval->timezone_abbreviation.'</option>' !!}
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="All Time Zones">
                                        @foreach($timezone as $tval)
                                            @if(!$tval->priority)
                                                @php $sel = ($defaultTimeZone === $tval->time_zone) ? 'selected="selected"' : ''; @endphp
                                                {!! '<option value="'.$tval->timezone_abbreviation.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' - '.$tval->timezone_abbreviation.'</option>' !!}
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                        <div class="isd-field">
                            <label><i class="fa fa-clock-o"></i> Duration</label>
                            <select id="isdDuration">
                                <option value="15">15 Minutes</option>
                                <option value="30">30 Minutes</option>
                                <option value="45" selected>45 Minutes</option>
                                <option value="60">60 Minutes</option>
                                <option value="90">90 Minutes</option>
                            </select>
                        </div>
                    </div>
                    <div class="isd-grid">
                        <div class="isd-field">
                            <label><i class="fa fa-list"></i> Interview Round</label>
                            <select id="isdInterviewRound">
                                @if(!empty($interview_processes) && count($interview_processes))
                                    <option value="">Select round</option>
                                    @foreach($interview_processes as $ipRow)
                                        <option value="{{ $ipRow->id }}">{{ ucfirst($ipRow->name) }}</option>
                                    @endforeach
                                @else
                                    <option value="">No interview rounds configured</option>
                                @endif
                            </select>
                        </div>
                        <div class="isd-field">
                            <label><i class="fa fa-calendar-o"></i> Add to Calendar</label>
                            <div class="isd-cal-row">
                                <div class="isd-cal-btn" id="isdGoogleCalBtn"><i class="fa fa-google"></i> Google Calendar</div>
                                <div class="isd-cal-btn" id="isdOutlookCalBtn"><i class="fa fa-calendar"></i> Outlook Calendar</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="btn-check" name="interview_type" value="1" id="fresh" {{ (@$reschedule == 1) ? '' : 'checked="checked"' }}>
                </div>

                <!-- Section 2: Interview Mode -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#c000ff;">2</span>
                        <div><div class="isd-sec-title">Interview Mode</div><div class="isd-sec-sub">How will interview happen</div></div>
                    </div>

                    <div class="isd-field"><label>Select Interview Mode <span class="req">*</span></label></div>
                    <div class="isd-tile-row" id="isdFamilyTiles"></div>
                    <div id="isdLeafSection" style="display:none;">
                        <div class="isd-leaf-label">Choose an option</div>
                        <div class="isd-tile-row" id="isdLeafTiles"></div>
                    </div>

                    <select name="interview_mode" id="interview_mode" onchange="getInterviewModeChild(this.value, {{ $job_id }});" class="isd-hidden-native">
                        <option value="">Select Interview Mode</option>
                        @if (!empty($interview_mode))
                            @foreach ($interview_mode as $int_mode_val)
                                <option value="{{ @$int_mode_val->id }}" {{ @$datatmode == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span id="childInterview" class="isd-hidden-native">
                        <select name="interview_mode_lavel" id="interview_mode_lavels" data-style="">
                            <option value="">Select mode</option>
                        </select>
                    </span>

                    <div class="row" style="display:none" id="url_div">
                        <div class="col-lg-12">
                            <label class="text-dark my-3 fw-bolder fs-5 d-flex justify-content-between align-items-center"><span id="lbl_call_mode">&nbsp;</span></label>
                            <input type="text" name="url" id="url" class="form-control form-control-solid">
                        </div>
                    </div>
                    <div class="isd-field" id="isdMeetingLinkWrap" style="display:none;">
                        <label>Meeting Link <span class="req">*</span></label>
                        <div class="isd-link-row">
                            <input type="text" id="isdMeetingLinkDisplay" readonly>
                            <div class="isd-link-actions">
                                <span class="isd-icon-btn" title="Open" id="isdLinkOpen"><i class="fa fa-external-link"></i></span>
                                <span class="isd-icon-btn" title="Refresh" id="isdLinkRefresh"><i class="fa fa-refresh"></i></span>
                                <span class="isd-icon-btn" title="Copy" id="isdLinkCopy"><i class="fa fa-clipboard"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Interview Panel -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#06c732;">3</span>
                        <div><div class="isd-sec-title">Interview Panel</div><div class="isd-sec-sub">Who will interview</div></div>
                    </div>
                    <div class="isd-panel-add">
                        <input class="form-control form-control-solid" id="isdPanelInput" placeholder="Enter panellist name or email">
                        <a href="javascript:;" class="isd-btn-add-panel in_tr_email_panel" data-bs-toggle="modal" data-panel="1"
                            data-bs-target="#kt_modal_create_project"><i class="fa fa-plus"></i> Add Interview Panel</a>
                    </div>
                    <input class="isd-hidden-native" name="panellist" id="panellist" value="">
                    <div class="isd-panel-list" id="isdPanelList"></div>
                    <div class="isd-check-avail"><i class="fa fa-clock-o"></i> Panelist availability isn't tracked yet <span class="isd-soon">Coming soon</span></div>
                </div>

                <!-- Section 4: Notifications -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#ff7a00;">4</span>
                        <div><div class="isd-sec-title">Notifications</div><div class="isd-sec-sub">Who should be notified</div></div>
                    </div>
                    <div class="isd-notify-grid">
                        <label class="isd-notify"><i class="fa fa-envelope"></i><span><span class="t">Send Email Invite</span><br><span class="d">To candidate &amp; panelists</span></span><input type="checkbox" id="isdNotifyEmail" checked style="margin-left:auto;"></label>
                        <label class="isd-notify is-disabled"><i class="fa fa-whatsapp"></i><span><span class="t">Send WhatsApp <span class="isd-soon">Soon</span></span><br><span class="d">To candidate</span></span><input type="checkbox" disabled style="margin-left:auto;"></label>
                        <label class="isd-notify is-disabled"><i class="fa fa-window-maximize"></i><span><span class="t">Notify Candidate Portal <span class="isd-soon">Soon</span></span><br><span class="d">Update on candidate portal</span></span><input type="checkbox" disabled style="margin-left:auto;"></label>
                        <label class="isd-notify is-disabled"><i class="fa fa-calendar"></i><span><span class="t">Add to Panel Calendar <span class="isd-soon">Soon</span></span><br><span class="d">Block time for panelists</span></span><input type="checkbox" disabled style="margin-left:auto;"></label>
                    </div>
                    <div style="margin-top:12px;">
                        <a href="javascript:void(0);" id="send_email_invies" style="font-size:11.5px;font-weight:600;color:#3e97ff;"><i class="fa fa-pencil"></i> Customize &amp; send invite email</a>
                    </div>
                </div>

                <div class="isd-sec isd-card">
                    <div class="isd-field">
                        <label>Notes / Special Instructions <span class="isd-soon">Coming soon</span></label>
                        <textarea rows="3" disabled placeholder="Not available yet"></textarea>
                    </div>
                </div>

                <input type="hidden" name="mailstatus" id="mailstatus" value="0">
                <input type="hidden" name="email_template_set" id="email_template_set" value="0">
            </div>

            <div class="isd-sidebar">
                <div class="isd-card">
                    <div class="isd-sidebar-card-title"><span class="dot"></span>Interview Summary</div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="fa fa-calendar"></i></div><div><div class="isd-sum-label">Date &amp; Time</div><div class="isd-sum-value muted" id="isdSumDate">Not set</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="fa fa-globe"></i></div><div><div class="isd-sum-label">Timezone</div><div class="isd-sum-value muted" id="isdSumTz">Not set</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="fa fa-video-camera"></i></div><div><div class="isd-sum-label">Mode</div><div class="isd-sum-value muted" id="isdSumMode">Not set</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="fa fa-users"></i></div><div><div class="isd-sum-label">Panelists</div><div class="isd-sum-value" id="isdSumPanel">0 added</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="fa fa-envelope"></i></div><div><div class="isd-sum-label">Email Invite</div><div class="isd-sum-value" id="isdSumEmail">Ready to send</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="fa fa-check-circle"></i></div><div><div class="isd-sum-label">Status</div><div><span class="isd-status-chip incomplete" id="isdSumStatus">Incomplete</span></div></div></div>
                </div>

                <div class="isd-card">
                    <div class="isd-sidebar-card-title"><span class="dot" style="background:#06c732;"></span>Candidate Availability</div>
                    @if(count($isdAvailabilitySlots))
                        @foreach($isdAvailabilitySlots as $isdSlot)
                            <div class="isd-avail-row">
                                <i class="fa fa-check-circle"></i>
                                <span>{{ \Carbon\Carbon::parse($isdSlot['date'])->format('D, d M · h:i A') }}
                                    @if(!empty($isdSlot['mode'])) &middot; {{ $isdSlot['mode'] }} @endif
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="isd-avail-empty">No availability shared during application</div>
                    @endif
                </div>

                <div class="isd-card">
                    <div class="isd-sidebar-card-title"><span class="dot" style="background:#ffa800;"></span>Quick Info</div>
                    <div class="isd-quick-row"><span class="k"><i class="fa fa-briefcase"></i>Job ID</span><span class="v">{{ @$application->jobId }}</span></div>
                    <div class="isd-quick-row"><span class="k"><i class="fa fa-list-alt"></i>Applied For</span><span class="v">{{ @$application->job_title }}</span></div>
                    <div class="isd-quick-row"><span class="k"><i class="fa fa-star"></i>Current Stage</span><span class="v">{{ trim(@$application->master_status_name) }}</span></div>
                    <div class="isd-quick-row"><span class="k"><i class="fa fa-user"></i>Source</span><span class="v">{{ @$submitted_by_company }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div id="div_interview_schedule_second" class="isd-wrap" style="display:none">
        <div class="isd-main" style="padding:20px;">
            <div class="isd-email-inline-head">Interview Schedule Email</div>
            <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label>
                <div class="col-lg-10 col-xl-10">
                    <select name="email_template" id="email_template" class="form-control form-control-solid"
                        onchange="accessEmailTemplate(this.value, {{ @$application->id }} );">
                        <option value="">Select Pre Define Email Template </option>
                        @if (!empty($email_template))
                            @foreach ($email_template as $email_temp)
                                @if ($email_temp->tempForInterview == 1 && $selfCompany == 1)
                                    <option value="{{ $email_temp->e_temp_id }}">
                                        {{ str_replace(['[{CLIENT_NAME}]', '[{COMPANY_NAME}]'], [@$clientTemp, @$companyTemp], $email_temp->display_name) }}
                                    </option>
                                @endif
                                @if ($selfCompany == '')
                                    <option value="{{ $email_temp->e_temp_id }}">
                                        {{ str_replace(['[{CLIENT_NAME}]', '[{COMPANY_NAME}]'], [@$clientTemp, @$companyTemp], $email_temp->display_name) }}
                                    </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <span class="form-text text-muted">Please select email template</span>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From</label>
                <div class="col-lg-10 col-xl-10">
                    <input type="text" name="from_email" id="from_email" placeholder="Ex: example@test.com" class="form-control form-control-solid" value="{{ @$from_email }}" readonly>
                    <span class="form-text text-muted">Please enter email</span>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
                <div class="col-lg-10 col-xl-10">
                    <input type="text" name="to_email" id="to_email" placeholder="Ex: demo@test.com" class="form-control form-control-solid" value="{{ @$application->email }}" readonly>
                    <span class="form-text text-muted">Please enter email</span>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">CC</label>
                <div class="col-lg-10 col-xl-10" style="position: relative;">
                    <input type="text" name="cc_email" id="cc_email" placeholder="Ex: example2@test.com" class="form-control form-control-solid" value="">
                    <span class="form-text text-muted">Please enter email</span>
                    <div class="" style="position: absolute; right: 13px; top: 4px;">
                        <a href="javascript:;" class="btn btn-primary btn-sm in_tr_email_panel" data-bs-toggle="modal" data-panel="0" data-bs-target="#kt_modal_create_project">Add E-mail ID</a>
                    </div>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
                <div class="col-lg-10 col-xl-10">
                    <input type="text" name="subject" id="subject" placeholder="Ex: Re-Schedule Interview" class="form-control form-control-solid">
                    <span class="form-text text-muted">Please enter subject</span>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                <div class="col-lg-10 col-xl-10">
                    <textarea id="editor1" name="editor1" class="form-control form-control-solid" row="10" placeholder="">&nbsp;</textarea>
                    <span class="form-text text-muted">Please enter email content</span>
                </div>
            </div>
            <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Upload Files (2MB)</label>
                <div class="col-xl-10 col-lg-10">
                    <input type="file" name="attachment" id="attachment" class="dropzone-select btn btn-sm btn-primary me-2" />
                </div>
            </div>
        </div>
        <!-- Back/Send Email live in the shared drawer topbar now (window.isdShellSetStep);
             these stay as hidden proxies so the existing .divback / #sendEmailBtn click
             handlers below keep working untouched. -->
        <button type="button" class="divback isd-hidden-native"></button>
        <button type="button" id="sendEmailBtn" class="isd-hidden-native"></button>
    </div>
</form>

<!--begin::Add Contact Person Modal-->
<div class="modal modal fade" tabindex="-1" aria-hidden="true" id="add_team_member">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="add_team_member_header">
                <h2 class="fw-bolder">Add Contact Person</h2>
                <div class="btn btn-icon btn-sm modal-close-team">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5">
                <form id="add_team_member_form" name="add_team_member_form" class="form" action="#" method="post">
                    <meta name="csrf-token" content="{{ csrf_token() }}" />
                    @csrf
                    @method('POST')
                    <div class="row client-cperson-row">
                        <div class="row client-cperson-container" style="position: relative;">
                            <span class="form-text text-muted form-label fw-bolder countContact" style="position: absolute; top: 0;"></span>
                            <div class="col-xl-12">
                                <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                                    <input type="text" name="cp_name" id="cp_name_0" class="form-control form-control-lg form-control-solid rounded me-5" value="" placeholder="Enter person name">
                                    <span class="form-text text-muted">Please enter contact person name</span>
                                    <div class="fv-plugins-message-container"></div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                                    <input type="email" name="cp_email" id="cp_email_0" class="form-control form-control-lg form-control-solid rounded me-5" value="" placeholder="Enter email">
                                    <span class="form-text text-muted">Please enter contact email</span>
                                    <div class="fv-plugins-message-container"></div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                                    <input type="number" name="cp_number" id="cp_number_0" class="form-control form-control-lg form-control-solid rounded me-5" value="" placeholder="Enter contact number">
                                    <input type="hidden" value="{{ @$country_code }}" id="phone_country_code" name="phone_country_code">
                                    <span class="form-text text-muted">Please enter contact number</span>
                                    <div class="fv-plugins-message-container"></div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                                    <input type="text" name="cp_desigination" id="cp_desigination_0" class="form-control form-control-lg form-control-solid rounded" value="" placeholder="Enter desigination" style="">
                                    <span class="form-text text-muted">Please enter desigination</span>
                                    <div class="fv-plugins-message-container"></div>
                                </div>
                            </div>
                            <input type="hidden" name="client_idMail" id="client_idMail" value="{{$application->client_id}}">
                            <input type="hidden" name="jobIdMail" id="jobIdMail" value="{{$application->jobId}}">
                            <input type="hidden" name="self_company" id="self_company" value="{{$application->self_company}}">
                            <input type="hidden" name="client_name" id="client_name" value="{{$application->client_name}}">
                            <input type="hidden" name="panelValue" id="panelValue" value="1">

                            <div class="text-center col-xl-12">
                                <button type="reset" class="btn btn-light me-3 modal-close-team">Close</button>
                                <button class="btn btn-primary " id="hiring_contact_person_add"> <span class="indicator-label">Submit</span> <span
                                        class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Add  Contact Person popup-->

<script src="https://preview.keenthemes.com/html/metronic/docs/assets/plugins/global/plugins.bundle.js"></script>
<script>
    $(document).ready(function() {

        const primaryPhoneInput = window.intlTelInput(document.querySelector("#cp_number_0"), {
            separateDialCode: true,
            initialCountry: "{{ $country_code }}",
            preferredCountries: ["in", "us", "gb", "ca", "au"],
        });

        function updateCountryCode(inputField, hiddenField) {
            const selectedCountryData = inputField.getSelectedCountryData();
            const countryCode = selectedCountryData.iso2;
            $(hiddenField).val(countryCode);
        }
        $("#cp_number_0").on("input change", function () {
            updateCountryCode(primaryPhoneInput, "#phone_country_code");
        });

        $(".iti__flag-container").on("click", function (event) {
            const targetInput = $(event.target).closest(".iti").find("input");
            if (targetInput.is("#cp_number_0")) {
                updateCountryCode(primaryPhoneInput, "#phone_country_code");
            }
        });

        setTimeout(function () {
            $(".iti__flag-container").click();
        },500);

        $(document).on('click', '#hiring_contact_person_add', function (e) {
            e.preventDefault();
            var client_id = $('#client_idMail').val();
            var cp_name_0 = $('#cp_name_0').val();
            var cp_email_0 = $('#cp_email_0').val();
            var cp_number_0 = $('#cp_number_0').val();
            var panel = $('#panelValue').val();
            var cp_desigination_0 = $('#cp_desigination_0').val();
            var phone_country_code = $('#phone_country_code').val();

            if (client_id > 0 && cp_name_0 !='' && cp_email_0 !='' && cp_desigination_0 !='') {

                $('.indicator-label').hide();
                $('#hiring_contact_person_add').attr('disabled',true);
                $('.indicator-progress').show();

                var token = "{{ csrf_token() }}";
                var id = client_id;
                var url = "{{ route('service_company.jobs.mail_contact_person', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        'cp_name_0': cp_name_0,
                        'cp_email_0': cp_email_0,
                        'cp_number_0': cp_number_0,
                        'cp_desigination_0': cp_desigination_0,
                        'phone_country_code': phone_country_code,
                        'id': id
                    },
                    success: function(response) {
                        $('.indicator-label').show();
                        $('#hiring_contact_person_add').attr('disabled',false);
                        $('.indicator-progress').hide();
                        $('#Iteam_Table').prepend(response.view);

                        if(panel){
                            var hiring_namager = $('#cc_email').val();
                            if(hiring_namager==''){
                                $('#cc_email').val(cp_name_0);
                            }else{
                                $('#cc_email').val(hiring_namager+','+cp_name_0);
                            }
                            if (typeof isdRegisterPanelMeta === 'function') { isdRegisterPanelMeta(cp_name_0, cp_email_0, cp_desigination_0); }
                            isdSyncChipsFromField();
                        }else{
                            var hiring_namager2 = $('#cc_email').val();
                            if(hiring_namager2==''){
                                $('#cc_email').val(response.contact_person_name);
                            }else{
                                $('#cc_email').val(hiring_namager2+','+response.contact_person_name);
                            }
                        }
                        $('#add_team_member_form')[0].reset();
                        $('#add_team_member').modal('hide');
                    }
                });

            } else {
                alert("Please Enter the Conract Person Details after Add");
            }
        });

        $(document).on('click', '.modal-close-team', function () {
            $('#add_team_member').modal('hide');
        });

        var update = "{{ @$update }}";

        if (update) {
            var scheduledate = "{{ @$scheduledate }}";
            $('#interviewDate').val(scheduledate);
            var interviewmode = "{{ @$interviewmode }}";
            $('#interview_mode').val(interviewmode).change();
            var interviewmodelavel = "{{ @$interviewmodelavel }}";
            setTimeout(() => {
                if (interviewmode == 2) {
                    $('#interview_mode_lavels').val(6).change();
                } else {
                    $('#interview_mode_lavels').val(interviewmodelavel).change();
                }
            }, 500);

            var emailtemplateset = "{{ @$emailtemplateset }}";
            if (emailtemplateset != 0) {
                $('#email_template').val(emailtemplateset).change();
            }

            var interviewers = "{{ @$interviewers }}";
            $('#panellist').val(interviewers);
            isdSyncChipsFromField();
            var interviewmentionlink = "{{ @$interviewmentionlink }}";
            setTimeout(() => {
                $('#url').val(interviewmentionlink);
            }, 500);
        }

        setTimeout(() => {
            var currentDate = new Date();
            var minDate = currentDate.toISOString().slice(0, 16);
            document.getElementById('interviewDate').min = minDate;
        }, 500);

        $('body').on('click', '#send_email_invies', function() {
            var interviewDate = $('#interviewDate').val();
            var interview_mode = $('#interview_mode').val();
            var interviewTime = $('#interviewTime').val();
            var timezone = $('#timezone').val();
            var interview_mode_lavels = $('#interview_mode_lavels').val();
            if (interview_mode == '') {
                Swal.fire("Required", "Interview Mode is required", "warning")
                return false;
            }
            if (timezone == '') {
                Swal.fire("Required", "Interview Time Zone is required", "warning")
                return false;
            }
            if (interview_mode_lavels == '') {
                Swal.fire("Required", "Interview Sub Mode is required", "warning")
                return false;
            }
            if (interviewDate == '') {
                Swal.fire("Required", "Date is required", "warning")
                return false;
            }
            if (interviewTime == '') {
                Swal.fire("Required", "Time is required", "warning")
                return false;
            }

            $('#div_interview_schedule_first').hide();
            $('#div_interview_schedule_second').show();
            window.isdShellSetStep('email');
        });
        $('body').on('click', '.divback', function() {
            $('#div_interview_schedule_first').show();
            $('#div_interview_schedule_second').hide();
            window.isdShellSetStep('main');
        });
    });

    function submitForm() {

        var interviewDate = $('#interviewDate').val();
        var interview_mode = $('#interview_mode').val();
        var interviewTime = $('#interviewTime').val();
        var timezone = $('#timezone').val();
        var interview_mode_lavels = $('#interview_mode_lavels').val();
        if (interview_mode == '') {
            Swal.fire("Required", "Interview Mode is required", "warning")
            return false;
        }
        if(interview_mode_lavels == ''){
            Swal.fire("Required", "Interview Sub Mode is required", "warning")
            return false;
        }
        if(interviewDate == ''){
            Swal.fire("Required", "Date is required", "warning")
            return false;
        }
        if(interviewTime == ''){
            Swal.fire("Required", "Time is required", "warning")
            return false;
        }
        if (timezone == '') {
            Swal.fire("Required", "Interview Time Zone is required", "warning")
            return false;
        }

        $("#interview_schedule_drawer_primary").prop("disabled", true);
        $('#interview_schedule_drawer_primary .indicator-label').hide();
        $('#interview_schedule_drawer_primary .indicator-progress').show();

        $.ajax({
            url: '{{ route('service_company.job-applications.store-schedule') }}',
            container: '#app_interview_create_frm',
            type: "POST",
            data: $('#app_interview_create_frm').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire("Success", "Interview schedule successfully!", "success").then(function() {
                        $('#interview_schedule_drawer_close').trigger('click');
                        location.reload();
                    });
                }
                if (response.status == 'coinCheck') {
                    Swal.fire("Success", ` ${response.message}`, "warning").then(function() {
                        $('#interview_schedule_drawer_close').trigger('click');
                    });
                }
            },
            complete: function() {
                $("#interview_schedule_drawer_primary").prop("disabled", false);
                $('#interview_schedule_drawer_primary .indicator-label').show();
                $('#interview_schedule_drawer_primary .indicator-progress').hide();
            }
        })
    }

    $(document).ready(function() {
        $(document).on('click', '#sendEmailBtn', function(e) {
            e.preventDefault();
            $(this).prop('disabled', true);
            var formData = new FormData($('#app_interview_create_frm')[0]);
            $('.indicator-label').hide();
            $('.indicator-progress').show();
            $.ajax({
                url: '{{ route('service_company.job-applications.emailschedule') }}',
                container: '#app_interview_create_frm',
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                redirect: true,
                success: function(response) {
                    $('.indicator-label').show();
                    $('.indicator-progress').hide();
                    if (response.status == 'faild') {
                        Swal.fire("Warning", "The file must not be larger than 2 MB ", "warning").then(function() {});
                    }
                    if (response.status == 'success') {
                        $('#mailstatus').val(1);
                        var email_template = $('#email_template').val();
                        $('#email_template_set').val(email_template);
                        var savesend = "{{ @$savesend }}";
                        if (savesend) {
                            submitForm();
                        } else {
                            Swal.fire("Success", "Interview schedule successfully!", "success").then(function() {
                                $('#interview_schedule_drawer_close').trigger('click');
                            });
                        }
                    }
                }
            })
        });
    });

    function getInterviewModeChild(id, job_id) {
        $('#url_div').hide();
        $('#lbl_call_mode').html('');
        $('#url').hide('');
        $('#isdMeetingLinkWrap').hide();

        var url = "{{ route('service_company.job-applications.getinterviewchild', ':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        var token = "{{ csrf_token() }}";

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                '_method': 'POST',
                'id': id,
                'job_id': job_id
            },
            beforeSend: function() {
                $('#childInterview').html('<img src="' + baseurl + '" height="35px" width="100%">');
            },
            success: function(response) {
                $('#childInterview').html('');
                $('#childInterview').html(response.data);
                isdRenderLeafTiles();
                isdUpdateSummary();
            },
            complete: function(data) {}
        });
    }

    function getVideoMeetingUrl(currentId, ParentId, job_id) {
        $("#url").prop("readonly", false);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $('#url_div').hide();
        $('#url').val('');
        if ((ParentId == 2) && (currentId == 2)) { // FOR GOOGLE
            $('#url_div').show();
            $('#url').show();
            $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Google Meet Link');
            var interviewDate = $('#interviewDate').val();
            var interviewTime = $('#interviewTime').val();
            if (interviewDate == '') { alert("Please select interview date"); return false; }
            if (interviewTime == '') { alert("Please select interview Time"); return false; }
            var url = "{{ route('service_company.job-applications.getGoogleVideoUrl', ':currentId') }}";
            url = url.replace(':currentId', currentId);
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: { '_token': token, '_method': 'POST', 'currentId': currentId, 'ParentId': ParentId, 'job_id': job_id, 'interviewDate': interviewDate, 'interviewTime': interviewTime },
                beforeSend: function() { $('#url_div').show(); $('#url').show(); $('#url').attr('placeholder', 'Generating Link .............'); isdSetLinkLoading(); },
                success: function(response) {
                    if (response.status == "success") {
                        $('#url_div').show();
                        $('#url').val(response.getHangoutLink);
                    } else {
                        Swal.fire({ title: 'Not Available', text: `${response.getHangoutLink ?? ''} is currently not available, kindly use other options to schedule the interview`, icon: 'question', showCancelButton: false, confirmButtonText: 'Ok', cancelButtonText: 'No, cancel', reverseButtons: true });
                        $('.sub_status').hide();
                    }
                    isdSyncMeetingLinkDisplay();
                },
            });
        }
        if ((ParentId == 2) && (currentId == 5)) { // Microsoft Teams
            $('#url_div').show();
            $('#url').show();
            $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Microsoft Teams Meeting Link');
            var interviewDate = $('#interviewDate').val();
            var interviewTime = $('#interviewTime').val();
            if (interviewDate == '') { alert("Please select interview date"); return false; }
            if (interviewTime == '') { alert("Please select interview Time"); return false; }
            var url = "{{ route('service_company.job-applications.getGoogleVideoUrl', ':currentId') }}";
            url = url.replace(':currentId', currentId);
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: { '_token': token, '_method': 'POST', 'currentId': currentId, 'ParentId': ParentId, 'job_id': job_id, 'interviewDate': interviewDate, 'interviewTime': interviewTime },
                beforeSend: function() { $('#url_div').show(); $('#url').show(); $('#url').attr('placeholder', 'Generating Link .............'); isdSetLinkLoading(); },
                success: function(response) {
                    if (response.status == "success") {
                        $('#url_div').show();
                        $('#url').val(response.getHangoutLink);
                    } else {
                        Swal.fire({ title: 'Not Available', text: `${response.getHangoutLink ?? ''} is currently not available, kindly use other options to schedule the interview`, icon: 'question', showCancelButton: false, confirmButtonText: 'Ok', cancelButtonText: 'No, cancel', reverseButtons: true });
                        $('.sub_status').hide();
                    }
                    isdSyncMeetingLinkDisplay();
                },
            });
        }
        if ((ParentId == 2) && (currentId == 3)) { // Zoom
            var interviewDate = $('#interviewDate').val();
            var interviewTime = $('#interviewTime').val();
            if (interviewDate == '') { alert("Please select interview date"); return false; }
            if (interviewTime == '') { alert("Please select interview Time"); return false; }
            var url = "{{ route('service_company.job-applications.getGoogleVideoUrl', ':currentId') }}";
            url = url.replace(':currentId', currentId);
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: { '_token': token, '_method': 'POST', 'currentId': currentId, 'ParentId': ParentId, 'job_id': job_id, 'interviewDate': interviewDate, 'interviewTime': interviewTime },
                beforeSend: function() { $('#url_div').show(); $('#url').show(); $('#url').attr('placeholder', 'Generating Link .............'); isdSetLinkLoading(); },
                success: function(response) {
                    if (response.status == "success") {
                        $('#url_div').show();
                        $('#url').show();
                        $('#lbl_call_mode').show();
                        $('#lbl_call_mode').html('Zoom Link');
                        if (response.mode == 'zoom') { $('#url').val(response.getHangoutLink); } else { $('#url').val(''); }
                    } else {
                        Swal.fire({ title: 'Not Available', text: `${response.getHangoutLink ?? ''} is currently not available, kindly use other options to schedule the interview.`, icon: 'question', showCancelButton: false, confirmButtonText: 'Ok', cancelButtonText: 'No, cancel', reverseButtons: true });
                        $('.sub_status').hide();
                    }
                    isdSyncMeetingLinkDisplay();
                },
                complete: function(data) {}
            });
        }
        if ((ParentId == 2) && currentId == 6) { // Other video
            $('#url_div').show(); $('#url').show(); $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Other Video Link'); $('#url').attr('placeholder', 'Enter Link here');
            isdSyncMeetingLinkDisplay();
        } else if ((ParentId == 1) && ((currentId == 1) || (currentId == 4))) { // Phone / Skype
            $('#url_div').show(); $('#url').show(); $('#url').attr('placeholder', 'Enter Link here');
            $('#lbl_call_mode').show(); $('#lbl_call_mode').html('');
            if (currentId == 1) { $('#lbl_call_mode').html('Phone Call'); $('#url').val('Phone Call'); $("#url").prop("readonly", true); }
            if (currentId == 4) { $('#lbl_call_mode').html('Skype Call'); $('#url').val('Skype Call'); $("#url").prop("readonly", true); }
            isdSyncMeetingLinkDisplay();
        } else if ((ParentId == 3) && (currentId >= 0)) {
            $('#url_div').show(); $('#url').show(); $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Interview Address/Location');
            var location = $('#interview_mode_lavels :selected').text();
            if (currentId > 0) { $('#url').val(location); }
            isdSyncMeetingLinkDisplay();
        }
        isdUpdateSummary();
    }
    setTimeout(() => {
        let mode = $('#interview_mode').val();
        let job_id = @json($job_id);
        if (mode) {
            getInterviewModeChild(mode,job_id);
        }
    }, 1000);

    /* =====================================================================
       Presentation-only glue: renders the icon tile-grid on top of the real
       #interview_mode / #interview_mode_lavels selects (built above), keeps
       chips in sync with the real #panellist field, and drives the live
       "Interview Summary" sidebar. None of this changes what gets submitted
       or how — it only mirrors/sets the same real fields the legacy
       dropdowns always used.
       ===================================================================== */
    var isdFamilyIcons = { 1: 'fa-phone', 2: 'fa-video-camera', 3: 'fa-map-marker', 4: 'fa-ellipsis-h' };
    var isdLeafIcons = { 'google meet':'fa-google', 'zoom':'fa-video-camera', 'microsoft teams':'fa-windows', 'skype call':'fa-skype', 'phone call':'fa-phone' };

    function isdLeafIcon(name){
        var key = (name||'').toLowerCase();
        return isdLeafIcons[key] || 'fa-map-marker';
    }

    function isdRenderFamilyTiles(){
        var wrap = $('#isdFamilyTiles').empty();
        $('#interview_mode option').each(function(){
            var val = $(this).val();
            if (val === '') return;
            var tile = $('<div class="isd-tile" data-family-val="'+val+'">' +
                '<span class="chk"><i class="fa fa-check"></i></span>' +
                '<div class="ic"><i class="fa '+(isdFamilyIcons[val] || 'fa-calendar')+'"></i></div>' +
                '<div class="nm"></div></div>');
            tile.find('.nm').text($(this).text());
            if ($(this).is(':selected')) tile.addClass('is-selected');
            wrap.append(tile);
        });
    }

    function isdRenderLeafTiles(){
        var wrap = $('#isdLeafTiles').empty();
        var opts = $('#interview_mode_lavels option');
        var real = opts.filter(function(){ return $(this).val() !== ''; });
        if (real.length === 0) { $('#isdLeafSection').hide(); return; }
        $('#isdLeafSection').show();
        real.each(function(){
            var val = $(this).val();
            var tile = $('<div class="isd-tile" data-leaf-val="'+val+'">' +
                '<span class="chk"><i class="fa fa-check"></i></span>' +
                '<div class="ic"></div>' +
                '<div class="nm"></div></div>');
            tile.find('.ic').html('<i class="fa '+isdLeafIcon($(this).text())+'"></i>');
            tile.find('.nm').text($(this).text());
            if ($(this).is(':selected')) tile.addClass('is-selected');
            wrap.append(tile);
        });
        // Video mode also gets a disabled "Client Portal" tile — not a real
        // interview_mode_lavel row, shown for parity with the target design.
        if ($('#interview_mode').val() == '2') {
            wrap.append('<div class="isd-tile is-disabled" title="Coming soon"><div class="ic"><i class="fa fa-window-maximize"></i></div><div class="nm">Client Portal <span class="isd-soon">Soon</span></div></div>');
        }
    }

    $(document).on('click', '#isdFamilyTiles .isd-tile', function(){
        var val = $(this).data('family-val');
        $('#interview_mode').val(val).trigger('change');
    });
    $(document).on('click', '#isdLeafTiles .isd-tile[data-leaf-val]', function(){
        var val = $(this).data('leaf-val');
        $('#interview_mode_lavels').val(val).trigger('change');
    });
    $(document).on('change', '#interview_mode', function(){
        $('#isdFamilyTiles .isd-tile').removeClass('is-selected');
        $('#isdFamilyTiles .isd-tile[data-family-val="'+$(this).val()+'"]').addClass('is-selected');
        $('#isdLeafSection').hide();
        $('#isdMeetingLinkWrap').hide();
    });
    $(document).on('change', '#interview_mode_lavels', function(){
        $('#isdLeafTiles .isd-tile').removeClass('is-selected');
        $('#isdLeafTiles .isd-tile[data-leaf-val="'+$(this).val()+'"]').addClass('is-selected');
    });

    function isdSyncMeetingLinkDisplay(){
        var visible = $('#url_div').is(':visible');
        $('#isdMeetingLinkWrap').toggle(visible);
        $('#isdMeetingLinkDisplay').val($('#url').val());
        isdUpdateSummary();
    }
    function isdSetLinkLoading(){
        $('#isdMeetingLinkWrap').show();
        $('#isdMeetingLinkDisplay').val('Generating link...');
    }
    $(document).on('click', '#isdLinkRefresh', function(){
        var parentVal = $('#interview_mode').val();
        var childVal = $('#interview_mode_lavels').val();
        var jobId = $('#jobId').val();
        if (parentVal && childVal) { getVideoMeetingUrl(childVal, parentVal, jobId); }
    });
    $(document).on('click', '#isdLinkCopy', function(){
        var v = $('#isdMeetingLinkDisplay').val();
        if (v && navigator.clipboard) { navigator.clipboard.writeText(v); }
    });
    $(document).on('click', '#isdLinkOpen', function(){
        var v = $('#isdMeetingLinkDisplay').val();
        if (v && v.indexOf('http') === 0) { window.open(v, '_blank'); }
    });
    // Keep the plain #url input's own manual edits mirrored into the display copy too.
    $(document).on('input', '#url', function(){ $('#isdMeetingLinkDisplay').val($(this).val()); });
    $(document).on('input', '#isdMeetingLinkDisplay', function(){ $('#url').val($(this).val()); isdUpdateSummary(); });

    // ---- panel chips: presentation over the real, single #panellist field ----
    // isdPanelMeta caches {email, designation} for entries added via the real
    // team-picker (contact-persion-with-team.blade.php's "Add Interview Panel"
    // drawer) so the chip can show real designation/email instead of guessing.
    var isdPanelMeta = {};
    function isdRegisterPanelMeta(name, email, designation){
        if (!name) return;
        isdPanelMeta[name.trim()] = { email: (email||'').trim(), designation: (designation||'').trim() };
    }
    function isdPanelListArray(){
        var raw = ($('#panellist').val() || '').trim();
        if (!raw) return [];
        return raw.split(/[,;]/).map(function(s){ return s.trim(); }).filter(function(s){ return s !== ''; });
    }
    function isdRenderPanelChips(){
        var list = isdPanelListArray();
        var wrap = $('#isdPanelList').empty();
        if (list.length === 0) {
            wrap.append('<div class="isd-panel-empty">No panelists added yet</div>');
        }
        list.forEach(function(entry, idx){
            var meta = isdPanelMeta[entry];
            var initial = entry.trim()[0] ? entry.trim()[0].toUpperCase() : '?';
            var row = $('<div class="isd-panel-row"></div>');
            row.append('<div class="isd-avatar bg-light-success text-success">'+initial+'</div>');
            var info = $('<div class="isd-panel-info"></div>');
            info.append($('<div class="isd-panel-name"></div>').text(entry));
            if (meta && meta.email) {
                info.append($('<div class="isd-panel-email"><i class="fa fa-envelope"></i></div>').append(document.createTextNode(meta.email)));
            }
            row.append(info);
            var tags = $('<div class="isd-panel-tags"></div>');
            if (meta && meta.designation) {
                tags.append($('<span class="isd-role-tag"></span>').text(meta.designation));
            }
            var del = $('<button type="button" class="isd-panel-del"><i class="fa fa-trash"></i></button>');
            del.on('click', function(){
                delete isdPanelMeta[entry];
                list.splice(idx, 1);
                $('#panellist').val(list.join(','));
                isdRenderPanelChips();
                isdUpdateSummary();
            });
            tags.append(del);
            row.append(tags);
            wrap.append(row);
        });
        isdUpdateSummary();
    }
    function isdSyncChipsFromField(){ isdRenderPanelChips(); }
    function isdAddPanelFromInput(){
        var v = $('#isdPanelInput').val().trim();
        if (!v) return;
        var list = isdPanelListArray();
        list.push(v);
        $('#panellist').val(list.join(','));
        $('#isdPanelInput').val('');
        isdRenderPanelChips();
    }
    $('#isdPanelInput').on('keydown', function(e){
        if (e.key === 'Enter') { e.preventDefault(); isdAddPanelFromInput(); }
    });
    $(document).on('blur', '#isdPanelInput', function(){ isdAddPanelFromInput(); });

    // ---- live "Interview Summary" sidebar ----
    function isdUpdateSummary(){
        var d = $('#interviewDate').val();
        var t = $('#interviewTime').val();
        $('#isdSumDate').text((d && t) ? (d + ' ' + t) : 'Not set').toggleClass('muted', !(d && t));
        var tz = $('#timezone').val();
        $('#isdSumTz').text(tz || 'Not set').toggleClass('muted', !tz);
        var leafText = $('#interview_mode_lavels option:selected').text();
        var hasMode = $('#interview_mode_lavels').val() ? true : false;
        $('#isdSumMode').text(hasMode ? leafText : 'Not set').toggleClass('muted', !hasMode);
        var panelCount = isdPanelListArray().length;
        $('#isdSumPanel').text(panelCount + ' added');
        $('#isdSumEmail').text($('#isdNotifyEmail').is(':checked') ? 'Ready to send' : 'Off');
        var ready = d && t && tz && hasMode && panelCount > 0;
        $('#isdSumStatus').text(ready ? 'Ready to Schedule' : 'Incomplete')
            .removeClass('incomplete ready').addClass(ready ? 'ready' : 'incomplete');
    }
    $(document).on('change', '#interviewDate, #interviewTime, #timezone, #isdNotifyEmail', isdUpdateSummary);

    // ---- Add to Calendar: fully client-side, built from the real form fields
    // (date/time/timezone/duration/meeting link) — no backend/API needed. ----
    function isdBuildEventDetails(){
        var d = $('#interviewDate').val();
        var t = $('#interviewTime').val();
        if (!d || !t) return null;
        var dParts = d.split('-');
        if (dParts.length !== 3) return null;
        var dd = parseInt(dParts[0], 10), mm = parseInt(dParts[1], 10), yyyy = parseInt(dParts[2], 10);

        var hh, min;
        var ampmMatch = t.match(/(\d{1,2}):(\d{2})\s*([AaPp][Mm])/);
        if (ampmMatch) {
            hh = parseInt(ampmMatch[1], 10);
            min = parseInt(ampmMatch[2], 10);
            var ap = ampmMatch[3].toUpperCase();
            if (ap === 'PM' && hh !== 12) hh += 12;
            if (ap === 'AM' && hh === 12) hh = 0;
        } else {
            var parts24 = t.split(':');
            hh = parseInt(parts24[0], 10);
            min = parseInt(parts24[1], 10);
        }

        var durationMin = parseInt($('#isdDuration').val() || '45', 10);
        var start = new Date(yyyy, mm - 1, dd, hh, min);
        var end = new Date(start.getTime() + durationMin * 60000);

        function pad(n){ return (n < 10 ? '0' : '') + n; }
        function fmt(dt){
            return dt.getFullYear() + pad(dt.getMonth() + 1) + pad(dt.getDate()) + 'T' + pad(dt.getHours()) + pad(dt.getMinutes()) + '00';
        }

        var candidateName = $.trim($('.isd-info-name').first().clone().children().remove().end().text()) || 'Candidate';
        return {
            title: 'Interview: ' + candidateName,
            start: fmt(start),
            end: fmt(end),
            location: $('#isdMeetingLinkDisplay').val() || ''
        };
    }
    $(document).on('click', '#isdGoogleCalBtn', function(){
        var ev = isdBuildEventDetails();
        if (!ev) { Swal.fire('Required', 'Please select date and time first', 'warning'); return; }
        var url = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' + encodeURIComponent(ev.title) +
            '&dates=' + ev.start + '/' + ev.end + '&location=' + encodeURIComponent(ev.location);
        window.open(url, '_blank');
    });
    $(document).on('click', '#isdOutlookCalBtn', function(){
        var ev = isdBuildEventDetails();
        if (!ev) { Swal.fire('Required', 'Please select date and time first', 'warning'); return; }
        var ics = 'BEGIN:VCALENDAR\r\nVERSION:2.0\r\nBEGIN:VEVENT\r\nDTSTART:' + ev.start + '\r\nDTEND:' + ev.end +
            '\r\nSUMMARY:' + ev.title + '\r\nLOCATION:' + ev.location + '\r\nEND:VEVENT\r\nEND:VCALENDAR';
        var blob = new Blob([ics], { type: 'text/calendar' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'interview.ics';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    $(document).ready(function(){
        isdRenderFamilyTiles();
        isdRenderLeafTiles();
        isdRenderPanelChips();
        isdUpdateSummary();
        window.isdShellSetStep('main');
    });
</script>
