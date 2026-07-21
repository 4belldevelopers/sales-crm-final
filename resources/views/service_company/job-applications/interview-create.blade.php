<?php
use App\Helper\GeneralMethod;
$defaultTimeZone = GeneralMethod::defaultTimeZone();
$currency_code =  GeneralMethod::defaultCurrency();
$country_code =  GeneralMethod::countryCodeFun();
?>
<style>
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

    .isd-avatar { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0; }

    .isd-body { display: flex; align-items: flex-start; }
    .isd-body--no-hero { padding-top: 8px; }
    .isd-main { flex: 1 1 66%; min-width: 0; padding: 16px 20px; }
    .isd-sidebar { flex: 0 0 34%; max-width: 320px; padding: 16px; border-left: 1px solid #eef0f4; background: #fbfbfd; }

    .isd-sec { margin-bottom: 18px; }
    .isd-card { background: #fff; border: 1px solid #eef0f4; border-radius: 12px; padding: 16px 18px; margin-bottom: 14px; }
    .isd-sec-head { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .isd-sec-head .isd-btn-add-panel { margin-left: auto; }
    .isd-sec-num { width: 22px; height: 22px; border-radius: 50%; color: #fff; font-size: 10.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .isd-sec-title { font-size: 13px; font-weight: 700; color: #181c32; }
    .isd-sec-sub { font-size: 10.5px; color: #7e8299; font-weight: 400; margin-top: 1px; }

    .isd-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
    .isd-grid + .isd-grid { margin-top: 12px; }
    .isd-grid-details { display: grid; grid-template-columns: 1.7fr 1fr 1fr; gap: 12px; }
    @media (max-width: 1500px) { .isd-grid-details { grid-template-columns: 1fr 1fr; } }
    .isd-field label { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #3f4254; margin-bottom: 5px; }
    .isd-field label .req { color: #f64e60; }
    .isd-field label i { color: #a1a5b7; font-size: 14px; }
    .isd-field input, .isd-field select, .isd-field textarea { width: 100%; border: 1.5px solid #e4e6ef; border-radius: 8px; padding: 7px 9px; font-size: 12px; color: #181c32; background: #f8f9fb; }
    .isd-field input:disabled, .isd-field select:disabled, .isd-field textarea:disabled { color: #b5b5c3; cursor: not-allowed; background: #f1f1f4; }
    .isd-soon { display: inline-flex; align-items: center; font-size: 9px; font-weight: 600; color: #a1a5b7; background: #f1f1f4; border-radius: 5px; padding: 2px 6px; margin-left: 4px; text-transform: uppercase; letter-spacing: .3px; }

    .isd-datetime-pill { display: flex; align-items: center; border: 1.5px solid #e4e6ef; border-radius: 8px; background: #f8f9fb; padding: 0 9px; }
    .isd-datetime-pill input { border: none !important; background: transparent !important; padding: 8px 4px !important; width: auto; flex: 1 1 auto; min-width: 0; color: #181c32; }
    .isd-datetime-pill .sep { width: 1px; height: 18px; background: #e4e6ef; flex-shrink: 0; }

    .isd-cal-row { display: flex; gap: 10px; }
    .isd-cal-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1.5px solid #e4e6ef; border-radius: 8px; padding: 9px; font-size: 11.5px; font-weight: 600; color: #3f4254; background: #fff; cursor: pointer; transition: .15s; }
    .isd-cal-btn:hover { border-color: #3e97ff; color: #3e97ff; background: #f5f9ff; }
    .isd-cal-btn i { font-size: 13px; }

    .isd-tile-row { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
    .isd-tile { flex: 1 1 100px; max-width: 138px; border: 1.5px solid #eef0f4; border-radius: 10px; padding: 10px 8px; text-align: center; cursor: pointer; background: #fff; position: relative; transition: .15s; }
    .isd-tile:hover { border-color: #cdd4e0; }
    .isd-tile.is-selected { border-color: #3e97ff; border-width: 2px; background: #f5f9ff; box-shadow: 0 0 0 1px #3e97ff inset, 0 2px 8px rgba(62,151,255,.18); }
    .isd-tile .ic { width: 44px; height: 44px; border-radius: 10px; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: #eef4ff; color: #3e97ff; overflow: hidden; }
    .isd-tile .ic svg { width: 30px; height: 30px; }
    .isd-tile .nm { font-size: 10.5px; font-weight: 700; color: #181c32; }
    .isd-tile.is-disabled { cursor: not-allowed; opacity: .55; }
    .isd-tile .chk { position: absolute; top: 6px; right: 6px; width: 18px; height: 18px; border-radius: 50%; background: #3e97ff; color: #fff; display: none; align-items: center; justify-content: center; font-size: 10px; }
    .isd-tile.is-selected .chk { display: flex; }
    .isd-leaf-label { font-size: 10px; font-weight: 600; color: #a1a5b7; text-transform: uppercase; letter-spacing: .3px; margin: 2px 0 8px; }

    .isd-link-row { position: relative; }
    .isd-link-row input { padding-right: 108px; }
    .isd-link-actions { position: absolute; right: 5px; top: 4px; display: flex; gap: 5px; }
    .isd-icon-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid #e4e6ef; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #7e8299; font-size: 14px; }
    .isd-icon-btn:hover { background: #f8f9fb; color: #3e97ff; }

    .isd-btn-add-panel { border: 1px solid #3e97ff; color: #3e97ff; background: #fff; border-radius: 8px; padding: 7px 14px; font-size: 11.5px; font-weight: 600; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; }
    .isd-btn-add-panel:hover { background: #f5f9ff; }
    .isd-panel-list { display: flex; flex-direction: column; gap: 10px; }
    .isd-panel-empty { font-size: 11.5px; color: #a1a5b7; text-align: center; padding: 10px 0; }
    .isd-panel-row { display: flex; align-items: center; gap: 14px; border: 1.5px solid #eef0f4; border-radius: 10px; padding: 12px 14px; }
    .isd-panel-row .isd-avatar { width: 38px; height: 38px; font-size: 13px; flex-shrink: 0; }
    .isd-panel-cols { display: flex; flex: 1 1 auto; gap: 20px; min-width: 0; flex-wrap: wrap; }
    .isd-panel-namecol { flex: 1 1 40%; min-width: 130px; }
    .isd-panel-emailcol { flex: 1 1 40%; min-width: 160px; display: flex; align-items: center; }
    .isd-panel-name { font-size: 12.5px; font-weight: 700; color: #181c32; word-break: break-word; }
    .isd-panel-designation { font-size: 10.5px; color: #7e8299; margin-top: 3px; }
    .isd-panel-email { font-size: 11px; color: #7e8299; }
    .isd-panel-email i { margin-right: 6px; color: #a1a5b7; }
    .isd-panel-del { width: 26px; height: 26px; border-radius: 7px; border: none; background: transparent; color: #f64e60; cursor: pointer; font-size: 12px; flex-shrink: 0; margin-left: auto; }
    .isd-panel-del:hover { background: #fff5f8; }
    .isd-check-avail { font-size: 10.5px; color: #a1a5b7; margin-top: 12px; display: inline-flex; align-items: center; gap: 6px; }

    .isd-notify-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .isd-notify { display: flex; align-items: flex-start; gap: 9px; border: 1.5px solid #eef0f4; border-radius: 9px; padding: 8px 10px; }
    .isd-notify i { color: #7e8299; font-size: 16px; margin-top: 1px; }
    .isd-notify input[type=checkbox] { margin-top: 2px; width: 14px; height: 14px; accent-color: #3e97ff; flex-shrink: 0; }
    .isd-notify .t { font-size: 11px; font-weight: 600; color: #181c32; }
    .isd-notify .d { font-size: 9.5px; color: #7e8299; }
    .isd-notify.is-disabled .t, .isd-notify.is-disabled .d { color: #b5b5c3; }
    .isd-notify.is-disabled i { color: #cdd4e0; }

    .isd-sidebar-card-title { font-size: 12px; font-weight: 700; color: #181c32; display: flex; align-items: center; gap: 7px; margin-bottom: 10px; }
    .isd-sidebar-card-title .dot { width: 6px; height: 6px; border-radius: 50%; background: #3e97ff; flex-shrink: 0; }
    .isd-sum-row { display: flex; align-items: flex-start; gap: 9px; padding: 6px 0; border-bottom: 1px dashed #f1f1f4; }
    .isd-sum-row:last-child { border-bottom: none; }
    .isd-sum-ico { width: 30px; height: 30px; border-radius: 8px; background: #f5f9ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
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
    .isd-quick-row .k i { color: #a1a5b7; font-size: 14px; width: 14px; }
    .isd-quick-row .v { color: #181c32; font-weight: 700; text-align: right; }
    .isd-quick-row--stacked { align-items: flex-start; }
    .isd-quick-v-stacked { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; }
    .isd-quick-v-line1 { font-weight: 700; color: #181c32; }
    .isd-quick-v-line2 { font-weight: 500; color: #7e8299; font-size: 10px; }

    .isd-hidden-native { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; overflow: hidden; }
    /* #url_div is the legacy address/link row — the real #url field it wraps still
       needs to exist and receive values from getVideoMeetingUrl(), but the new
       #isdMeetingLinkWrap box below is the only one that should ever be visible;
       otherwise the same address/link renders twice (legacy code calls .show()
       on this for every mode). */
    #url_div { display: none !important; }

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

<script>
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
        <div class="isd-body isd-body--no-hero">
            <div class="isd-main">

                <!-- Section 1: Interview Details -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#3e97ff;">1</span>
                        <div><div class="isd-sec-title">Interview Details</div><div class="isd-sec-sub">When &amp; for how long</div></div>
                    </div>
                    <div class="isd-grid-details">
                        <div class="isd-field">
                            <label><i class="ti ti-calendar-filled"></i> Date &amp; Time <span class="req">*</span></label>
                            <div class="isd-datetime-pill">
                                <input name="interviewDate" id="interviewDate" type="date"
                                    min="{{ date('Y-m-d') }}"
                                    value="{{ ($datadate) ? \Carbon\Carbon::parse(@$datadate)->format('Y-m-d') : '' }}">
                                <span class="sep"></span>
                                <input name="interviewTime" id="interviewTime" type="time"
                                    value="{{ ($datatime) ? \Carbon\Carbon::parse(@$datatime)->format('H:i') : '' }}">
                            </div>
                        </div>
                        <div class="isd-field">
                            <label><i class="ti ti-world"></i> Timezone <span class="req">*</span></label>
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
                            <label><i class="ti ti-clock-filled"></i> Duration</label>
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
                            <label><i class="ti ti-list"></i> Interview Round</label>
                            <select id="isdInterviewRound">
                                <option value="">Select round</option>
                                @if(!empty($interview_processes) && count($interview_processes))
                                    @foreach($interview_processes as $ipRow)
                                        <option value="{{ $ipRow->id }}">{{ ucfirst($ipRow->name) }}</option>
                                    @endforeach
                                @else
                                    @php
                                        $isdStaticRounds = [
                                            'Screening', 'Technical Round 1', 'Technical Round 2', 'Technical Round 3',
                                            'Technical Round 4', 'Technical Round 5', 'Technical Round 6', 'Technical Round 7',
                                            'Hiring Manager Round', 'HR Round', 'Final Round',
                                        ];
                                    @endphp
                                    @foreach($isdStaticRounds as $isdRoundName)
                                        <option value="{{ $isdRoundName }}">{{ $isdRoundName }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="isd-field">
                            <label><i class="ti ti-calendar-filled"></i> Add to Calendar</label>
                            <div class="isd-cal-row">
                                <div class="isd-cal-btn" id="isdGoogleCalBtn"><i class="ti ti-brand-google-filled"></i> Google Calendar</div>
                                <div class="isd-cal-btn" id="isdOutlookCalBtn"><i class="ti ti-calendar-filled"></i> Outlook Calendar</div>
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
                        <label><span id="isdMeetingLinkLabel">Meeting Link</span> <span class="req">*</span></label>
                        <div class="isd-link-row">
                            <input type="text" id="isdMeetingLinkDisplay" readonly>
                            <div class="isd-link-actions">
                                <span class="isd-icon-btn" title="Open" id="isdLinkOpen"><i class="ti ti-external-link"></i></span>
                                <span class="isd-icon-btn" title="Refresh" id="isdLinkRefresh"><i class="ti ti-refresh"></i></span>
                                <span class="isd-icon-btn" title="Copy" id="isdLinkCopy"><i class="ti ti-clipboard"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Interview Panel -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#06c732;">3</span>
                        <div><div class="isd-sec-title">Interview Panel</div><div class="isd-sec-sub">Who will interview</div></div>
                        <a href="javascript:;" class="isd-btn-add-panel in_tr_email_panel" data-bs-toggle="modal" data-panel="1"
                            data-bs-target="#kt_modal_create_project"><i class="ti ti-plus"></i> Add Interview Panel</a>
                    </div>
                    <input class="isd-hidden-native" name="panellist" id="panellist" value="">
                    <div class="isd-panel-list" id="isdPanelList"></div>
                    <div class="isd-check-avail"><i class="ti ti-clock-filled"></i> Panelist availability isn't tracked yet <span class="isd-soon">Coming soon</span></div>
                </div>

                <!-- Section 4: Notifications -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#ff7a00;">4</span>
                        <div><div class="isd-sec-title">Notifications</div><div class="isd-sec-sub">Who should be notified</div></div>
                    </div>
                    <div class="isd-notify-grid">
                        <label class="isd-notify"><i class="ti ti-mail-filled"></i><span><span class="t">Send Email Invite</span><br><span class="d">To candidate &amp; panelists</span></span><input type="checkbox" id="isdNotifyEmail" checked style="margin-left:auto;"></label>
                        <label class="isd-notify is-disabled"><i class="ti ti-brand-whatsapp"></i><span><span class="t">Send WhatsApp <span class="isd-soon">Soon</span></span><br><span class="d">To candidate</span></span><input type="checkbox" disabled style="margin-left:auto;"></label>
                        <label class="isd-notify is-disabled"><i class="ti ti-apps-filled"></i><span><span class="t">Notify Candidate Portal <span class="isd-soon">Soon</span></span><br><span class="d">Update on candidate portal</span></span><input type="checkbox" disabled style="margin-left:auto;"></label>
                        <label class="isd-notify is-disabled"><i class="ti ti-calendar-filled"></i><span><span class="t">Add to Panel Calendar <span class="isd-soon">Soon</span></span><br><span class="d">Block time for panelists</span></span><input type="checkbox" disabled style="margin-left:auto;"></label>
                    </div>
                    <div style="margin-top:12px;">
                        <a href="javascript:void(0);" id="send_email_invies" style="font-size: 11.5px;font-weight:600;color:#3e97ff;"><i class="ti ti-pencil"></i> Customize &amp; send invite email</a>
                    </div>
                </div>

                <input type="hidden" name="mailstatus" id="mailstatus" value="0">
                <input type="hidden" name="email_template_set" id="email_template_set" value="0">
            </div>

            <div class="isd-sidebar">
                <div class="isd-card">
                    <div class="isd-sidebar-card-title"><span class="dot" style="background:#ffa800;"></span>Quick Info</div>
                    <div class="isd-quick-row"><span class="k"><i class="ti ti-id-badge-2"></i>Candidate</span><span class="v" id="isdQuickCandidateName">{{ ucwords($isdFullName) }}</span></div>
                    <div class="isd-quick-row"><span class="k"><i class="ti ti-briefcase"></i>Job ID</span><span class="v">{{ @$application->jobId }}</span></div>
                    <div class="isd-quick-row"><span class="k"><i class="ti ti-list-details"></i>Applied For</span><span class="v">{{ @$application->job_title }}</span></div>
                    <div class="isd-quick-row"><span class="k"><i class="ti ti-star"></i>Current Stage</span><span class="v">{{ trim(@$application->master_status_name) }}</span></div>
                    <div class="isd-quick-row isd-quick-row--stacked">
                        <span class="k"><i class="ti ti-user"></i>Ownership</span>
                        <span class="v isd-quick-v-stacked">
                            <span class="isd-quick-v-line1">{{ @$application->submitted_by_name }}</span>
                            <span class="isd-quick-v-line2">{{ @$submitted_by_company }}</span>
                        </span>
                    </div>
                </div>

                <div class="isd-card">
                    <div class="isd-sidebar-card-title"><span class="dot"></span>Interview Summary</div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="ti ti-calendar-filled"></i></div><div><div class="isd-sum-label">Date &amp; Time</div><div class="isd-sum-value muted" id="isdSumDate">Not set</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="ti ti-world"></i></div><div><div class="isd-sum-label">Timezone</div><div class="isd-sum-value muted" id="isdSumTz">Not set</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="ti ti-video"></i></div><div><div class="isd-sum-label">Mode</div><div class="isd-sum-value muted" id="isdSumMode">Not set</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="ti ti-users"></i></div><div><div class="isd-sum-label">Panelists</div><div class="isd-sum-value" id="isdSumPanel">0 added</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="ti ti-mail-filled"></i></div><div><div class="isd-sum-label">Email Invite</div><div class="isd-sum-value" id="isdSumEmail">Ready to send</div></div></div>
                    <div class="isd-sum-row"><div class="isd-sum-ico"><i class="ti ti-circle-check-filled"></i></div><div><div class="isd-sum-label">Status</div><div><span class="isd-status-chip incomplete" id="isdSumStatus">Incomplete</span></div></div></div>
                </div>

                <div class="isd-card">
                    <div class="isd-sidebar-card-title"><span class="dot" style="background:#06c732;"></span>Candidate Availability</div>
                    @if(count($isdAvailabilitySlots))
                        @foreach($isdAvailabilitySlots as $isdSlot)
                            <div class="isd-avail-row">
                                <i class="ti ti-circle-check-filled"></i>
                                <span>{{ \Carbon\Carbon::parse($isdSlot['date'])->format('D, d M · h:i A') }}
                                    @if(!empty($isdSlot['mode'])) &middot; {{ $isdSlot['mode'] }} @endif
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="isd-avail-empty">No availability shared during application</div>
                    @endif
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
            if (scheduledate) {
                // scheduledate is "Y-m-d H:i:s" from the DB; native date/time
                // inputs need it split into their own ISO pieces.
                $('#interviewDate').val(scheduledate.substring(0, 10));
                $('#interviewTime').val(scheduledate.substring(11, 16));
            }
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

    // Called directly by this file's own Save button and, via a global
    // reference, by the shared drawer shell's "Schedule Interview" button in
    // service_company_layout.blade.php — assigning to window explicitly here
    // removes any doubt about scope now that it's invoked cross-file.
    window.submitForm = function submitForm() {

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
            $('#lbl_call_mode').html('Enter Your Meeting Link'); $('#url').attr('placeholder', 'Enter Link here');
            isdSyncMeetingLinkDisplay();
        } else if ((ParentId == 1) && ((currentId == 1) || (currentId == 4))) { // Phone / Skype
            $('#url_div').show(); $('#url').show(); $('#url').attr('placeholder', 'Enter Link here');
            $('#lbl_call_mode').show(); $('#lbl_call_mode').html('');
            if (currentId == 1) { $('#lbl_call_mode').html('Phone Call'); $('#url').val('Phone Call'); $("#url").prop("readonly", true); }
            if (currentId == 4) { $('#lbl_call_mode').html('Skype Call'); $('#url').val('Skype Call'); $("#url").prop("readonly", true); }
            isdSyncMeetingLinkDisplay();
        } else if ((ParentId == 3) && (currentId >= 0)) {
            $('#url_div').show(); $('#url').show(); $('#lbl_call_mode').show();
            var location = $('#interview_mode_lavels :selected').text();
            if (currentId > 0) {
                $('#lbl_call_mode').html('Interview Address');
                $('#url').val(location);
            } else {
                // "Other address" — manual entry, not a saved client location.
                $('#lbl_call_mode').html('Enter the Interview Address');
                $('#url').val('');
            }
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
    var isdFamilyIcons = { 1: 'ti-phone-filled', 2: 'ti-video', 3: 'ti-map-pin-filled', 4: 'ti-dots' };
    var isdLeafIcons = { 'google meet':'ti-brand-google-filled', 'zoom':'ti-brand-zoom', 'microsoft teams':'ti-brand-teams', 'skype call':'ti-brand-skype', 'phone call':'ti-phone-filled' };
    // Custom brand SVGs (full-color, self-contained) — used in place of the
    // Tabler icon font for the modes an icon was actually supplied for.
    // Keyed the same way isdFamilyIcons / isdLeafIcons are looked up.
    var isdCustomSvg = {
        'family-1': '<svg viewBox="0 0 122.88 122.27" xmlns="http://www.w3.org/2000/svg"><path fill="#1E0478" d="M33.84,50.25c4.13,7.45,8.89,14.6,15.07,21.12c6.2,6.56,13.91,12.53,23.89,17.63c0.74,0.36,1.44,0.36,2.07,0.11 c0.95-0.36,1.92-1.15,2.87-2.1c0.74-0.74,1.66-1.92,2.62-3.21c3.84-5.05,8.59-11.32,15.3-8.18c0.15,0.07,0.26,0.15,0.41,0.21 l22.38,12.87c0.07,0.04,0.15,0.11,0.21,0.15c2.95,2.03,4.17,5.16,4.2,8.71c0,3.61-1.33,7.67-3.28,11.1 c-2.58,4.53-6.38,7.53-10.76,9.51c-4.17,1.92-8.81,2.95-13.27,3.61c-7,1.03-13.56,0.37-20.27-1.69 c-6.56-2.03-13.17-5.38-20.39-9.84l-0.53-0.34c-3.31-2.07-6.89-4.28-10.4-6.89C31.12,93.32,18.03,79.31,9.5,63.89 C2.35,50.95-1.55,36.98,0.58,23.67c1.18-7.3,4.31-13.94,9.77-18.32c4.76-3.84,11.17-5.94,19.47-5.2c0.95,0.07,1.8,0.62,2.25,1.44 l14.35,24.26c2.1,2.72,2.36,5.42,1.21,8.12c-0.95,2.21-2.87,4.25-5.49,6.15c-0.77,0.66-1.69,1.33-2.66,2.03 c-3.21,2.33-6.86,5.02-5.61,8.18L33.84,50.25L33.84,50.25L33.84,50.25z"/></svg>',
        'family-2': '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path d="M170.666667 256h469.333333c46.933333 0 85.333333 38.4 85.333333 85.333333v341.333334c0 46.933333-38.4 85.333333-85.333333 85.333333H170.666667c-46.933333 0-85.333333-38.4-85.333334-85.333333V341.333333c0-46.933333 38.4-85.333333 85.333334-85.333333z" fill="#4CAF50"/><path d="M938.666667 746.666667l-213.333334-128V405.333333l213.333334-128z" fill="#388E3C"/></svg>',
        'family-3': '<svg viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg"><path fill="#d22f27" d="M52.5733,29.11c0-9.3149-7.1338-16.8923-15.9034-16.8923S20.7665,19.795,20.7665,29.114c.0027.4653.2238,11.6088,12.9612,31.2452a3.4626,3.4626,0,0,0,2.8174,1.9343c1.8394,0,3.0941-2.0266,3.2162-2.2327C52.58,40.4135,52.58,29.5534,52.5733,29.11ZM36.67,35.9135a7.0826,7.0826,0,1,1,7.0826-7.0826A7.0907,7.0907,0,0,1,36.67,35.9135Z"/><path fill="#ea5a47" d="M52.5733,29.11c0-9.3149-7.1338-16.8923-15.9034-16.8923a14.96,14.96,0,0,0-3.8645.5254c8.3948.4493,15.0989,7.8225,15.0989,16.849.0068.4436.0068,11.3036-12.812,30.9509a5.8543,5.8543,0,0,1-.5865.7972,2.8549,2.8549,0,0,0,2.0393.9534c1.8394,0,3.0941-2.0266,3.2162-2.2327C52.58,40.4135,52.58,29.5534,52.5733,29.11Z"/><path fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M36.5451,62.2935a3.4628,3.4628,0,0,1-2.8174-1.9343C20.99,40.7228,20.7692,29.5793,20.7665,29.114c0-9.3191,7.1337-16.8964,15.9034-16.8964S52.5733,19.7949,52.5733,29.11c.0068.4436.0068,11.3036-12.812,30.9509C39.6392,60.2669,38.3845,62.2935,36.5451,62.2935Z"/><path fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M36.67,35.9135a7.0826,7.0826,0,1,1,7.0827-7.0826A7.0906,7.0906,0,0,1,36.67,35.9135Z"/></svg>',
        'leaf-zoom': '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2"><g transform="matrix(6.4 0 0 6.4 -32 -32)"><clipPath id="zmclip1"><path d="M84.062 37.188v15.624c0 17.248-14.002 31.25-31.25 31.25H37.188c-17.248 0-31.25-14.002-31.25-31.25V37.188c0-17.248 14.002-31.25 31.25-31.25h15.624c17.248 0 31.25 14.002 31.25 31.25z"/></clipPath><g clip-path="url(#zmclip1)"><path d="M5 13a8 8 0 018-8h64a8 8 0 018 8v64a8 8 0 01-8 8H13a8 8 0 01-8-8V13z" fill="url(#zmgrad1)" fill-rule="nonzero"/></g></g><path d="M152.08 295.512H88.07c-4.489 0-8.501-2.677-10.223-6.825a11.048 11.048 0 012.4-12.063l44.335-44.335H92.806c-8.728 0-15.805-7.077-15.805-15.804h59.03c4.49 0 8.502 2.683 10.218 6.831a11.024 11.024 0 01-2.393 12.056l-44.335 44.336h36.75c8.733 0 15.81 7.076 15.81 15.804zM435 246.119c0-16.99-13.83-30.821-30.821-30.821-9.089 0-17.275 3.96-22.92 10.243-5.644-6.282-13.83-10.243-22.918-10.243-16.991 0-30.822 13.83-30.822 30.821v49.393c8.728 0 15.804-7.077 15.804-15.804v-33.589c0-8.276 6.741-15.01 15.018-15.01 8.282 0 15.017 6.734 15.017 15.01v33.589c0 8.727 7.076 15.804 15.804 15.804v-49.393c0-8.276 6.734-15.01 15.017-15.01 8.276 0 15.017 6.734 15.017 15.01v33.589c0 8.727 7.07 15.804 15.804 15.804v-49.393zm-115.383 9.882c0 22.475-18.223 40.698-40.697 40.698-22.48 0-40.704-18.223-40.704-40.698 0-22.48 18.223-40.703 40.704-40.703 22.474 0 40.697 18.223 40.697 40.703zm-15.804 0c0-13.752-11.147-24.893-24.893-24.893-13.753 0-24.893 11.14-24.893 24.893 0 13.747 11.14 24.894 24.893 24.894 13.746 0 24.893-11.147 24.893-24.894zm-71.918 0c0 22.475-18.217 40.698-40.697 40.698-22.481 0-40.698-18.223-40.698-40.698 0-22.48 18.217-40.703 40.698-40.703 22.48 0 40.697 18.223 40.697 40.703zm-15.804 0c0-13.752-11.147-24.893-24.893-24.893-13.747 0-24.894 11.14-24.894 24.893 0 13.747 11.147 24.894 24.894 24.894 13.746 0 24.893-11.147 24.893-24.894z" fill="#fff" fill-rule="nonzero"/><defs><linearGradient id="zmgrad1" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="rotate(-60 88.792 30.85) scale(109.282)"><stop offset="0" stop-color="#0845bf"/><stop offset=".6" stop-color="#0b5cff"/><stop offset="1" stop-color="#4f90ee"/></linearGradient></defs></svg>',
        'leaf-google meet': '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path fill="#F5BB41" d="M91.084,20.921L77.229,32.352V18.068c0-3.528-2.84-6.386-6.346-6.386H25.405L3.723,33.502v32.996v15.434c0,3.528,2.84,6.386,6.346,6.386h15.336h45.478c3.506,0,6.346-2.858,6.346-6.386V68.18l13.918,11.485c2.083,1.639,5.13,0.144,5.13-2.522V23.39C96.277,20.692,93.162,19.212,91.084,20.921z M56.076,66.498H25.405V33.502h30.671V50V66.498z"/><path fill="#2167D1" d="M3.723,66.498v15.434c0,3.528,2.84,6.386,6.346,6.386h15.336v-21.82H3.723z"/><rect fill="#3D84F3" height="32.996" width="21.682" x="3.723" y="33.502"/><path fill="#4CA853" d="M91.084,20.921L77.229,32.352V68.18l13.918,11.485c2.083,1.639,5.13,0.144,5.13-2.522V23.39C96.277,20.692,93.162,19.212,91.084,20.921z"/><path fill="#4CA853" d="M56.076,50v16.498H25.405v21.82h45.478c3.506,0,6.346-2.858,6.346-6.386V68.18L56.076,50z"/><polygon fill="#398039" points="77.229,68.18 77.229,32.363 56.076,50"/><polygon fill="#D74F3F" points="3.723,33.502 25.405,33.502 25.405,11.682"/><path fill="#F5BB41" d="M70.883,11.682H25.405v21.82h30.671V50l21.153-17.637v-0.011V18.068C77.229,14.54,74.389,11.682,70.883,11.682z"/></svg>',
        'leaf-microsoft teams': '<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill="#5059C9" d="M10.765 6.875h3.616c.342 0 .619.276.619.617v3.288a2.272 2.272 0 01-2.274 2.27h-.01a2.272 2.272 0 01-2.274-2.27V7.199c0-.179.145-.323.323-.323zM13.21 6.225c.808 0 1.464-.655 1.464-1.462 0-.808-.656-1.463-1.465-1.463s-1.465.655-1.465 1.463c0 .807.656 1.462 1.465 1.462z"/><path fill="#7B83EB" d="M8.651 6.225a2.114 2.114 0 002.117-2.112A2.114 2.114 0 008.65 2a2.114 2.114 0 00-2.116 2.112c0 1.167.947 2.113 2.116 2.113zM11.473 6.875h-5.97a.611.611 0 00-.596.625v3.75A3.669 3.669 0 008.488 15a3.669 3.669 0 003.582-3.75V7.5a.611.611 0 00-.597-.625z"/><path fill="#000000" d="M8.814 6.875v5.255a.598.598 0 01-.596.595H5.193a3.951 3.951 0 01-.287-1.476V7.5a.61.61 0 01.597-.624h3.31z" opacity=".1"/><path fill="#000000" d="M8.488 6.875v5.58a.6.6 0 01-.596.595H5.347a3.22 3.22 0 01-.267-.65 3.951 3.951 0 01-.172-1.15V7.498a.61.61 0 01.596-.624h2.985z" opacity=".2"/><path fill="#000000" d="M8.488 6.875v4.93a.6.6 0 01-.596.595H5.08a3.951 3.951 0 01-.172-1.15V7.498a.61.61 0 01.596-.624h2.985z" opacity=".2"/><path fill="#000000" d="M8.163 6.875v4.93a.6.6 0 01-.596.595H5.079a3.951 3.951 0 01-.172-1.15V7.498a.61.61 0 01.596-.624h2.66z" opacity=".2"/><path fill="#000000" d="M8.814 5.195v1.024c-.055.003-.107.006-.163.006-.055 0-.107-.003-.163-.006A2.115 2.115 0 016.593 4.6h1.625a.598.598 0 01.596.594z" opacity=".1"/><path fill="#000000" d="M8.488 5.52v.699a2.115 2.115 0 01-1.79-1.293h1.195a.598.598 0 01.595.594z" opacity=".2"/><path fill="#000000" d="M8.163 5.52v.647a2.115 2.115 0 01-1.465-1.242h.87a.598.598 0 01.595.595z" opacity=".2"/><path fill="url(#pgmt1)" d="M1.597 4.925h5.969c.33 0 .597.267.597.596v5.958a.596.596 0 01-.597.596h-5.97A.596.596 0 011 11.479V5.521c0-.33.267-.596.597-.596z"/><path fill="#ffffff" d="M6.152 7.193H4.959v3.243h-.76V7.193H3.01v-.63h3.141v.63z"/><defs><linearGradient id="pgmt1" x1="2.244" x2="6.906" y1="4.46" y2="12.548" gradientUnits="userSpaceOnUse"><stop stop-color="#5A62C3"/><stop offset=".5" stop-color="#4D55BD"/><stop offset="1" stop-color="#3940AB"/></linearGradient></defs></svg>',
        'leaf-phone call': '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><rect width="512" height="512" rx="15%" fill="#20df40"/><path fill="#ffffff" d="M395 338c-10-9-22-17-33-23-23-16-43-17-59 6-10 14-23 16-37 10-39-18-68-43-84-82-8-16-8-31 10-42 9-6 19-14 18-28-1-18-44-78-62-84-8-2-14-2-22 0-41 14-57 46-42 85a451 451 0 0 0 251 248l17 5c26 0 57-25 67-50 9-24-10-34-24-45z"/><path fill="none" stroke="#ffffff" stroke-linecap="round" stroke-width="17" d="M268 138s41-1 72 30c31 30 27 74 27 74m-87-63s18-2 33 14c15 15 13 34 13 34M255 98s64-3 111 44c47 48 43 114 43 114"/></svg>',
        'leaf-other': '<svg viewBox="0 0 512.176 512.176" xmlns="http://www.w3.org/2000/svg"><path fill="#1689FC" d="M253.422 172.886L346.8 79.507c11.341-11.342 26.288-17.016 41.229-17.016 14.94 0 29.887 5.674 41.229 17.016l.068.072c11.296 11.334 16.948 26.251 16.948 41.157 0 14.937-5.673 29.884-17.013 41.226l-95.985 95.987c-6.917 6.917-15.622 11.896-24.986 14.67-9.375 2.775-19.459 3.322-29.103 1.37-12.796-2.59-20.783-9.21-28.727-15.795-16.45-17.096-43.529-18.599-62.063-.065l-.075.075c4.42 13.375 13.19 26.417 24.152 37.514 11.64 11.781 25.634 21.27 39.45 26.58 19.788 7.605 41.42 9.238 62.029 4.903 19.889-4.186 38.83-13.981 54.226-29.377l97.982-97.981c21.144-21.144 31.719-49.029 31.719-76.914 0-27.868-10.561-55.732-31.676-76.872l-.044-.042c-21.145-21.145-49.028-31.719-76.913-31.719-27.886 0-55.769 10.574-76.913 31.717l-97.982 97.983c-5.604 5.604-10.447 11.639-14.522 17.981a108.1 108.1 0 00-5.37 9.341 124.273 124.273 0 0126.274-2.805c11.003 0 22.005 1.459 32.688 4.373z"/><path fill="#5B9FFC" d="M214.476 297.698l37.91-37.911c7.339 6.035 15.038 11.82 26.801 14.202 9.644 1.952 19.728 1.405 29.103-1.37 9.364-2.774 18.069-7.753 24.986-14.67l95.985-95.987c11.34-11.342 17.013-26.289 17.013-41.226 0-14.185-5.118-28.381-15.349-39.488l35.235-35.233.044.042c21.115 21.14 31.676 49.004 31.676 76.872 0 27.885-10.575 55.77-31.719 76.914l-97.982 97.981c-15.396 15.396-34.337 25.191-54.226 29.377-20.609 4.335-42.241 2.702-62.029-4.903-13.034-5.009-26.225-13.737-37.448-24.6z"/><path fill="#A3D4FF" d="M41.107 471.058h.001c45.123 45.124 118.96 45.124 164.083 0l96.531-96.531a114.972 114.972 0 0028.236-46.213c-29.88 4.82-60.152 5.081-76.034.476l-95.341 95.34c-20.391 20.392-53.763 20.394-74.158 0-20.393-20.393-20.392-53.763.001-74.157l94.298-94.297c20.393-20.394 53.763-20.394 74.156-.001 1.992 1.991 6.739 4.885 12.357 7.736 22.287 11.305 43.499 7.568 63.749-9.638a114.963 114.963 0 00-27.264-43.33c-45.123-45.123-118.961-45.123-164.084 0l-96.531 96.531c-45.123 45.123-45.123 118.961 0 164.084z"/><path fill="#BFE0FF" d="M84.045 428.128l-38.208 38.208.002.002c21.143 21.144 49.027 31.718 76.914 31.718 27.886 0 55.769-10.574 76.914-31.718l97.981-97.981c5.798-5.798 10.771-12.039 14.913-18.581a106.457 106.457 0 004.983-8.762l-.685.148a124.32 124.32 0 01-63.515-3.282l-88.581 88.58c-11.342 11.342-26.289 17.016-41.23 17.016-14.185 0-28.382-5.119-39.488-15.348zm213.601-213.601l-35.11 35.11c5.672 4.593 11.642 8.813 19.443 10.391 7.33 1.483 15.056 1.051 22.29-1.09 6.2-1.837 12.019-4.952 16.884-9.214a107.295 107.295 0 00-8.817-16.984c-4.083-6.39-8.98-12.503-14.688-18.212l-.002-.001z"/><path fill="#1A1A1A" fill-rule="nonzero" d="M176.52 176.649a121.866 121.866 0 0111.304-22.38 122.478 122.478 0 0116.419-20.382l97.981-97.981C326.16 11.97 357.709 0 389.247 0c68.144 0 122.929 54.782 122.929 122.929 0 31.54-11.971 63.087-35.906 87.023l-97.982 97.981a121.905 121.905 0 01-42.769 27.74 122.073 122.073 0 01-10.898 21.696 122.37 122.37 0 01-16.864 21.096l-97.981 97.981c-47.639 47.639-126.409 47.641-174.046 0-47.64-47.637-47.639-126.407 0-174.046l97.981-97.981a121.978 121.978 0 0142.809-27.77zm76.824 161.231l-88.581 88.58c-22.575 22.575-59.882 22.575-82.457 0-22.67-22.671-22.669-59.784-.002-82.458l95.984-95.982c8.853-8.852 20.073-14.579 32.443-16.521 25.13-3.947 36.941 5.84 48.833 15.695 6.602 5.474 13.243 10.978 22.415 12.834 7.33 1.483 15.056 1.051 22.29-1.09 6.2-1.837 12.019-4.952 16.884-9.214-16.645-40.465-56.234-66.915-100.419-66.915-27.887 0-55.771 10.574-76.914 31.719l-97.981 97.981c-42.087 42.087-42.088 111.744 0 153.829 42.085 42.088 111.742 42.086 153.828 0l97.981-97.981c5.798-5.798 10.771-12.039 14.913-18.581a106.457 106.457 0 004.983-8.762l-.685.148a124.32 124.32 0 01-63.515-3.282zm78.67-98.887l87.135-87.136c8.552-8.554 12.829-19.836 12.829-31.121a43.808 43.808 0 00-43.949-43.949c-11.285 0-22.57 4.278-31.121 12.829l-88.438 88.44c28.065 11.729 50.64 33.492 63.544 60.937zm-78.592-66.107L346.8 79.507c11.341-11.342 26.288-17.016 41.229-17.016 32.278 0 58.245 25.968 58.245 58.245 0 14.937-5.673 29.884-17.013 41.226l-95.985 95.987c-6.917 6.917-15.622 11.896-24.986 14.67-9.375 2.775-19.459 3.322-29.103 1.37-12.796-2.59-20.783-9.21-28.727-15.795-9.427-7.813-18.791-15.57-37.552-12.624-9.378 1.471-17.887 5.935-24.586 12.634 4.42 13.375 13.19 26.417 24.152 37.514 11.64 11.781 25.634 21.27 39.45 26.58 19.788 7.605 41.42 9.238 62.029 4.903 19.889-4.186 38.83-13.981 54.226-29.377l97.982-97.981c21.144-21.144 31.719-49.029 31.719-76.914A108.31 108.31 0 00389.247 14.296c-27.886 0-55.769 10.574-76.913 31.717l-97.982 97.983c-5.604 5.604-10.447 11.639-14.522 17.981a108.1 108.1 0 00-5.37 9.341 124.273 124.273 0 0126.274-2.805c11.003 0 22.005 1.459 32.688 4.373zm-76.169 96.387l-84.84 84.838c-17.12 17.123-17.121 45.117.002 62.24 17.022 17.023 45.216 17.023 62.239 0l84.217-84.215c-13.02-6.271-25.752-15.483-36.561-26.423-10.645-10.774-19.509-23.299-25.057-36.44z"/></svg>'
    };
    function isdSetIconContent($icEl, key, fallbackIconClass){
        var svg = isdCustomSvg[key];
        if (svg) {
            $icEl.html(svg).css({ background: 'transparent' });
            return true;
        }
        $icEl.html('<i class="ti ' + fallbackIconClass + '"></i>');
        return false;
    }
    // Brand-ish colors per icon, matching the reference design (each mode gets
    // its own recognizable color instead of one flat blue box).
    var isdIconColors = {
        'ti-brand-google-filled': { bg: '#e6f4ea', fg: '#1a7f37' },
        'ti-brand-zoom':   { bg: '#e8f0fe', fg: '#2d8cff' },
        'ti-brand-teams':  { bg: '#f1edfd', fg: '#5059c9' },
        'ti-brand-skype':  { bg: '#e6f7ff', fg: '#00aff0' },
        'ti-phone-filled': { bg: '#e6faf3', fg: '#03a678' },
        'ti-video':        { bg: '#eef4ff', fg: '#3e97ff' },
        'ti-map-pin-filled': { bg: '#f7edff', fg: '#a35ee8' },
        'ti-dots':         { bg: '#f1f1f4', fg: '#a1a5b7' },
        'ti-apps-filled':  { bg: '#f1f1f4', fg: '#a1a5b7' }
    };
    function isdColorIcon($icEl, iconClass){
        var c = isdIconColors[iconClass];
        if (c) { $icEl.css({ background: c.bg, color: c.fg }); }
    }

    function isdLeafIcon(name){
        var key = (name||'').toLowerCase();
        return isdLeafIcons[key] || 'ti-map-pin-filled';
    }
    function isdLeafCustomKey(name){
        var key = (name||'').toLowerCase();
        if (isdCustomSvg['leaf-' + key]) return 'leaf-' + key;
        if (key.indexOf('other') !== -1) return 'leaf-other';
        return null;
    }

    function isdRenderFamilyTiles(){
        var wrap = $('#isdFamilyTiles').empty();
        $('#interview_mode option').each(function(){
            var val = $(this).val();
            if (val === '') return;
            var iconClass = isdFamilyIcons[val] || 'ti-calendar';
            var tile = $('<div class="isd-tile" data-family-val="'+val+'">' +
                '<span class="chk"><i class="ti ti-check"></i></span>' +
                '<div class="ic"></div>' +
                '<div class="nm"></div></div>');
            tile.find('.nm').text($(this).text());
            var usedCustom = isdSetIconContent(tile.find('.ic'), 'family-' + val, iconClass);
            if (!usedCustom) { isdColorIcon(tile.find('.ic'), iconClass); }
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
            var label = $(this).text();
            var iconClass = isdLeafIcon(label);
            var tile = $('<div class="isd-tile" data-leaf-val="'+val+'">' +
                '<span class="chk"><i class="ti ti-check"></i></span>' +
                '<div class="ic"></div>' +
                '<div class="nm"></div></div>');
            tile.find('.nm').text(label);
            var customKey = isdLeafCustomKey(label);
            var usedCustom = customKey ? isdSetIconContent(tile.find('.ic'), customKey, iconClass) : false;
            if (!usedCustom) {
                tile.find('.ic').html('<i class="ti '+iconClass+'"></i>');
                isdColorIcon(tile.find('.ic'), iconClass);
            }
            if ($(this).is(':selected')) tile.addClass('is-selected');
            wrap.append(tile);
        });
        // Video mode also gets a disabled "Client Portal" tile — not a real
        // interview_mode_lavel row, shown for parity with the target design.
        if ($('#interview_mode').val() == '2') {
            var portalTile = $('<div class="isd-tile is-disabled" title="Coming soon"><div class="ic"><i class="ti ti-apps-filled"></i></div><div class="nm">Client Portal <span class="isd-soon">Soon</span></div></div>');
            isdColorIcon(portalTile.find('.ic'), 'ti-apps-filled');
            wrap.append(portalTile);
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
        // #url_div is force-hidden via CSS (kills the old duplicate address row),
        // so it can no longer be used as a "should we show a link?" signal —
        // this is only ever called from branches that DO have a link/address to
        // show, so just show the box unconditionally.
        $('#isdMeetingLinkWrap').show();
        var labelText = $.trim($('#lbl_call_mode').text()) || 'Meeting Link';
        $('#isdMeetingLinkLabel').text(labelText);
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
    // Keeps the "Add Interview Panel" team-picker drawer in sync: deleting a
    // panelist here must also drop them from #cc_email (the field that picker
    // reads to decide Add-vs-Remove state) and flip any already-rendered
    // picker button back to "Add", so the two lists never disagree.
    function isdRemoveFromCcEmail(email){
        var cc = ($('#cc_email').val() || '');
        var arr = cc.split(',').map(function(s){ return s.trim(); })
            .filter(function(s){ return s !== '' && s.toLowerCase() !== email.trim().toLowerCase(); });
        $('#cc_email').val(arr.join(','));
    }
    function isdResetPanelPickerButton(name){
        $('.cp_select_panel[data-title="'+name+'"]').each(function(){
            if ($(this).attr('data-assign') == '1') {
                $(this).removeClass('btn-light-danger').addClass('btn-light-primary').text('Add').attr('data-assign', '0');
            }
        });
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
            wrap.append('<div class="isd-panel-empty">No panelists added yet — use "Add Interview Panel" above</div>');
        }
        list.forEach(function(entry, idx){
            var meta = isdPanelMeta[entry];
            var initial = entry.trim()[0] ? entry.trim()[0].toUpperCase() : '?';
            var row = $('<div class="isd-panel-row"></div>');
            row.append('<div class="isd-avatar bg-light-success text-success">'+initial+'</div>');
            var cols = $('<div class="isd-panel-cols"></div>');
            var nameCol = $('<div class="isd-panel-namecol"></div>');
            nameCol.append($('<div class="isd-panel-name"></div>').text(entry));
            if (meta && meta.designation) {
                nameCol.append($('<div class="isd-panel-designation"></div>').text(meta.designation));
            }
            cols.append(nameCol);
            var emailCol = $('<div class="isd-panel-emailcol"></div>');
            if (meta && meta.email) {
                emailCol.append($('<span class="isd-panel-email"><i class="ti ti-mail-filled"></i></span>').append(document.createTextNode(meta.email)));
            }
            cols.append(emailCol);
            row.append(cols);
            var del = $('<button type="button" class="isd-panel-del"><i class="ti ti-trash-filled"></i></button>');
            del.on('click', function(){
                if (meta && meta.email) { isdRemoveFromCcEmail(meta.email); }
                isdResetPanelPickerButton(entry);
                delete isdPanelMeta[entry];
                list.splice(idx, 1);
                $('#panellist').val(list.join(','));
                isdRenderPanelChips();
                isdUpdateSummary();
            });
            row.append(del);
            wrap.append(row);
        });
        isdUpdateSummary();
    }
    function isdSyncChipsFromField(){ isdRenderPanelChips(); }

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
    // Native date/time inputs otherwise only open their picker when the tiny
    // built-in icon is clicked — showPicker() opens it from anywhere in the field.
    $(document).on('click', '#interviewDate, #interviewTime', function(){
        if (this.showPicker) { try { this.showPicker(); } catch (e) {} }
    });

    // ---- Add to Calendar: fully client-side, built from the real form fields
    // (date/time/timezone/duration/meeting link) — no backend/API needed. ----
    function isdBuildEventDetails(){
        // Native <input type="date"> / <input type="time"> already give clean
        // ISO pieces ("YYYY-MM-DD" / "HH:MM" 24hr) — no format-guessing needed.
        var d = $('#interviewDate').val();
        var t = $('#interviewTime').val();
        if (!d || !t) return null;
        var dParts = d.split('-');
        var tParts = t.split(':');
        if (dParts.length !== 3 || tParts.length < 2) return null;
        var yyyy = parseInt(dParts[0], 10), mm = parseInt(dParts[1], 10), dd = parseInt(dParts[2], 10);
        var hh = parseInt(tParts[0], 10), min = parseInt(tParts[1], 10);

        var durationMin = parseInt($('#isdDuration').val() || '45', 10);
        var start = new Date(yyyy, mm - 1, dd, hh, min);
        var end = new Date(start.getTime() + durationMin * 60000);

        function pad(n){ return (n < 10 ? '0' : '') + n; }
        function fmt(dt){
            return dt.getFullYear() + pad(dt.getMonth() + 1) + pad(dt.getDate()) + 'T' + pad(dt.getHours()) + pad(dt.getMinutes()) + '00';
        }

        var candidateName = $.trim($('#isdQuickCandidateName').first().text()) || 'Candidate';
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
