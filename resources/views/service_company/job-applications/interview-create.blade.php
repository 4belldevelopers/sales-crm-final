<?php
use App\Helper\GeneralMethod;
// dd("jd");
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
    // $.noConflict();  

    setTimeout(() => {
          
          var defaultTimeFormat = '{{$defaultTimeFormat}}';
         
          if(defaultTimeFormat==1){

            flatpickr("#interviewTime", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                minuteIncrement: 1
            });

            // $("#interviewTime").flatpickr({
            //     enableTime: true,
            //     noCalendar: true,
            //     dateFormat: "h:i K",
            //     minuteIncrement: 1 ,  
            //     // defaultDate: new Date()    
            // });

          }else{
            
            flatpickr("#interviewTime", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1
            });

            // $("#interviewTime").flatpickr({
            //     enableTime: true,
            //     noCalendar: true,
            //     dateFormat: "H:i",       // 24-hour format
            //     time_24hr: true,         // Enable 24-hour dropdown
            //     minuteIncrement: 1,
            //     // defaultDate: new Date()
            // });

          }
  
            flatpickr("#interviewDate", {
                dateFormat: "d-m-Y",
                altInput: true,
                altFormat: "d-M-Y",
                minDate: "today",
                onReady: function (selectedDates, dateStr, instance) {
                    instance.altInput.style.width = "129px";
                }
            });

          
            // $("#interviewDate").flatpickr({
            //     dateFormat: "d-m-Y",    // Custom date format 
            //     altInput: true,                // Enable alternative input
            //     altFormat: "d-M-Y",  
            //     minDate: "today",       // Set minimum date to today
            //     onReady: function(selectedDates, dateStr, instance) {
            //             instance.altInput.style.width = "129px"; // Set the desired width
            //         }
            //                 // defaultDate: new Date() // Set maximum date
            // });

          
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
                complete: function(data) {
                    //$('#childStatus_'+current_application_id).html('');
                }
            });
        } else {
            $('#subject').val('');
            $('#editor').html('');
        }
    }
</script>
<style>
    .tox-statusbar__branding {
        display: none
    }
</style>
<form class="form fv-plugins-bootstrap fv-plugins-framework" id="app_interview_create_frm" name="app_interview_create_frm"
    method="post" enctype="multipart/form-data">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="application_id" id="application_id" value="{{ @$application->id }}" />
    <input type="hidden" name="wiseId" id="wiseId" value="{{ @$application->wiseId }}" />
    <input type="hidden" name="jobId" id="jobId" value="{{ @$application->jobId }}" />
    <input type="hidden" name="master_status_id" id="master_status_id" value="{{ @$application->master_status_id }}" />
    <input type="hidden" name="child_status_id" id="child_status_id" value="{{ @$application->child_status_id }}" />
    <div id="div_interview_schedule_first">
        <div class="modal-header flex-stack">
            <h2>Interview Schedule</h2>
            <div class="btn btn-sm btn-icon btn-active-light-danger" id="data-dismiss" data-dismiss="modal"> <span
                    class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="currentColor">
                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                            transform="rotate(-45 6 17.3137)" />
                        <rect x="7.41422" y="6" width="16" height="2" rx="1"
                            transform="rotate(45 7.41422 6)" />
                    </svg>
                </span> </div>
        </div>
        <div class="modal-body scroll-y py-0 px-lg-7">
            <div data-kt-element="options">
                <div class="py-5">
                    <table class="table align-middle table-row-dashed fs-6" id="">
                        <!--begin::Table Head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="fw-bolder text-muted" role="row">
                                <th class="">Name</th>
                                {{-- <th class="">Contacts</th> --}}
                                <th class="">Exp.</th>
                                <th class="">Salary</th>
                                <th class="">Notice Period</th>
                                <th class="">Qualification</th>
                                <th class="">Location</th>
                                <th class="">Source</th>
                                <th class="">Job</th>
                                <th class="">Stage</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table Head-->
                        <tbody class="text-gray-600 fw-bold">
                            <tr>
                                @php

                                    $bgTextDecore = $strVal = '';
                                    $str = trim(@$application->full_name);
                                    $str = $strVal = strtolower($str[0]);
                                    $str = ord($str);
                                    if ($str >= 97 && $str <= 101) {
                                        $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                                    } elseif ($str >= 102 && $str <= 106) {
                                        $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                                    } elseif ($str >= 107 && $str <= 111) {
                                        $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                                    } elseif ($str >= 112 && $str <= 116) {
                                        $bgTextDecore = 'fs-3 bg-light-success text-success';
                                    } elseif ($str >= 117 && $str <= 121) {
                                        $bgTextDecore = 'fs-3 bg-dark text-secondary';
                                    } else {
                                        $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                                    }

                                @endphp
                                <td class="d-flex align-items-center border-0">
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label fs-3 {{ @$bgTextDecore }} border-radius-5">
                                                {{ strtoupper(@$strVal) }}</div>
                                        </a>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="javascript:void(0);"
                                            class="text-dark fw-bolder text-hover-primary fs-6">
                                            {{ ucwords(@$application->full_name) }} </a>
                                        <a href="javascript:void(0);"
                                            class="text-muted fw-bold d-block fs-7 text-hover-primary">
                                            {{ ucwords(@$application->current_designation) }}</a>
                                    </div>
                                </td>

                                {{-- <td>
                                    <div class="d-flex flex-column"> 
                                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"> {{@$application->phone}}</a> 
                                        <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="{{@$application->email}}"> 
                                            {{substr(ucwords(@$application->email), 0, 10)}}
                                        </a> 
                                    </div>
                                </td> --}}

                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">{{ @$application->total_exp }}+</span>
                                        <span
                                            class="text-muted fw-bold d-block fs-7">{{ @$application->rel_exp }}+</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">{!! @$expected_ctc !!}</span>
                                        @if (@$application->condidate_type == 'other')
                                            <span class="text-muted fw-bold d-block fs-7">{!! @$current_ctc !!}</span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">

                                            @php

                                                $np = $application->np_id;
                                                if (empty($np)) {
                                                    $np = 'N/A';
                                                }
                                                if ($application->serving_notice_period_mode == 0) {
                                                    if (!empty($application->np_id)) {
                                                        $np = $application->np_id . '  Days';
                                                    }
                                                } elseif ($application->serving_notice_period_mode == 1) {
                                                    $np = date('d M y', strtotime($application->np_id));
                                                } else {
                                                    $np = $application->np_id;
                                                }
                                                echo $np;

                                            @endphp

                                        </span>
                                        <span
                                            class="text-muted fw-bold d-block fs-7">{{ @$application->current_company }}</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <span
                                            class="text-dark fw-bolder fs-6">{{ @$application->qualification_name }}</span>
                                        <span
                                            class="text-muted fw-bold d-block fs-7">{{ substr(ucwords(@$application->skills), 0, 15) }}</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">{{ @$row_city_name }}</span>
                                        <span
                                            class="text-muted fw-bold d-block fs-7">{{ substr(@$row_state_name, 0, 6) }}
                                            | {{ @$row_country_name }}</span>
                                    </div>
                                </td>

                                <td>

                                    @php
                                        $posted_by_image = '';
                                        if (!empty($application->posted_by_image)) {
                                            $posted_by_image = @$application->posted_by_image;
                                        }


                                        if($application->company_id != $application->ClientComID && $application->ClientComID != 0 && $application->ClientComID > 0){
                                            $spokperson = DB::table('job_assign')->select('users.name')->leftjoin('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $application->jobId)->where('job_assign.company_id', $application->ClientComID)->first();
                                            $rowpostedby = @$spokperson->name;
                                        }else{
                                            $rowpostedby = $application->submitted_by_name;
                                        }

                                    @endphp

                                    <div class="d-flex flex-column">
                                        <div class="symbol-group symbol-hover">
                                            <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip"
                                                data-placement="top"
                                                title="{{ substr(@$rowpostedby, 0, 10) }}">
                                                <span
                                                    class="text-dark fw-bolder fs-6">{{ substr(@$rowpostedby, 0, 20) }}</span>

                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        class="text-muted fw-bold d-block fs-7">{{ substr(@$submitted_by_company, 0, 10) }}</span>
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <span
                                            class="text-dark fw-bolder fs-6">{{ substr(@$application->job_title, 0, 10) }}</span>
                                        <span
                                            class="text-muted fw-bold d-block fs-7">{{ substr(@$application->client_name, 0, 10) }}</span>
                                    </div>
                                </td>

                                <td>
                                    <a href="javascript:void(0);" class="d-flex flex-column">
                                        <span
                                            class="badge fw-bolder fs-6 {{ @$application->master_status_style }} px-2 py-1">
                                            {{ substr(trim(@$application->master_status_name), 0, 10) }}
                                        </span>
                                        <span
                                            class="text-muted fw-bold text-muted d-block fs-7">{{ substr(@$child_status_name, 0, 10) }}</span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                    @php
                        $fresh_check = $reschedule_check = '';
                        if ($reschedule == 1) {
                            $reschedule_check = 'checked="checked"';
                        } else {
                            $fresh_check = 'checked="checked"';
                        }
                    @endphp
                    <div class="row">
                        <div class="col-lg-3">
                            <label class="text-dark mb-0 mt-3 fw-bolder fs-5">Select Time Zone</label>
                            <div class="row py-3 mb-3 px-3">

                                <select name="timezone" id="timezone" aria-label="Select a Time Zone" data-placeholder="Select a time zone..." class="form-select form-select-solid" tabindex="-1" aria-hidden="true">
                                    <option value="" >Select a timezone...</option>
                                      @if(!empty($timezone))
                                       <!-- Popular Time Zones -->
                                            <optgroup label="Popular Time Zones">
                                              @foreach($timezone as $tval)
                                                  @if($tval->priority)
                                                      @php
                                                          $sel = '';
                                                          if ($defaultTimeZone === $tval->time_zone) {
                                                              $sel = 'selected="selected"';
                                                          }
                                                      @endphp
                                                      {!! '<option value="'.$tval->timezone_abbreviation.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' - '.$tval->timezone_abbreviation.'</option>' !!}
                                                  @endif
                                              @endforeach
                                          </optgroup>
                  
                                          <!-- Other Time Zones -->
                                          <optgroup label="All Time Zones">
                                              @foreach($timezone as $tval)
                                                  @if(!$tval->priority) {{-- Non-priority time zones --}}
                                                      @php
                                                          $sel = '';
                                                          if ($defaultTimeZone === $tval->time_zone) {
                                                              $sel = 'selected="selected"';
                                                          }
                                                      @endphp
                                                      {!! '<option value="'.$tval->timezone_abbreviation.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' - '.$tval->timezone_abbreviation.'</option>' !!}
                                                  @endif
                                              @endforeach
                                          </optgroup>
                                      @endif
                                  </select>
                                  <input type="hidden" class="btn-check" name="interview_type" value="1"
                                        id="fresh" {{ @$fresh_check }}>

                                {{-- <div class="col-lg-6 px-2">
                                    <input type="radio" class="btn-check" name="interview_type" value="1"
                                        id="fresh" {{ @$fresh_check }}>
                                    <label
                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                        for="fresh"> <span class="d-block fw-bold text-start"> <span
                                                class="text-dark-800 fw-bolder d-block fs-5">Fresh</span> </span>
                                    </label>
                                </div>
                                <div class="col-lg-6 px-2">
                                    <input type="radio" class="btn-check" name="interview_type" value="2"
                                        id="reschedule" {{ @$reschedule_check }}>
                                    <label
                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                        for="reschedule"> <span class="d-block fw-bold text-start"> <span
                                                class="text-dark-800 fw-bolder d-block fs-5">Reschedule</span> </span>
                                    </label>
                                </div> --}}
                                <span class="form-text text-muted">Please select Time Zone</span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                           
                             

                            <label class="text-dark mb-0 mt-3 fw-bolder fs-5 px-2">Date and Time</label>
                            <div class="row my-3 px-3">
                                <input class="form-control form-control-solid" placeholder="Select date" name="interviewDate" id="interviewDate" value="{{ (($datadate) ?  \Carbon\Carbon::parse(@$datadate)->format('d-m-Y') : '') }}" type="text" readonly="readonly" style="width: 50%;">

                                <input class="form-control form-control-solid" placeholder="Select Time" name="interviewTime" id="interviewTime" value="{{ (($datatime) ? \Carbon\Carbon::parse(@$datatime)->format('h:i A') : '') }}" type="text" readonly="readonly" style="width: 48%; margin-left: 4px;">

                                {{-- <input class="form-control form-control-solid" placeholder="Select a date & time"
                                    name="interviewDate" id="interviewDate" type="datetime-local"
                                    onclick="this.showPicker()"> --}}
                            </div>
                            <span class="form-text text-muted">Please select date & time</span>
                        </div>
                        <div class="col-lg-3">
                            <label class="text-dark mb-0 mt-3 fw-bolder fs-5 px-2">Interview Mode</label>
                            <div class="row py-3 mb-3 px-3">
                                <select name="interview_mode" id="interview_mode"
                                    onchange="getInterviewModeChild(this.value, {{ $job_id }});"
                                    class="form-select form-select-solid">
                                    <option value="">Select Interview Mode</option>

                                    @if (!empty($interview_mode))
                                        @foreach ($interview_mode as $int_mode_val)
                                            <option value="{{ @$int_mode_val->id }}" {{ @$datatmode == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif

                                </select>
                                <span class="form-text text-muted">Please select mode</span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="text-dark mb-0 mt-3 fw-bolder fs-5 px-2">Interview Sub Mode</label>
                            <div class="row py-3 mb-3 px-3"> <span id="childInterview">
                                    <select class="form-select form-select-solid" name="interview_mode_lavel"
                                        id="interview_mode_lavels" data-style="">
                                        <option value="">Select mode</option>
                                    </select>
                                </span> <span class="form-text text-muted">Please select mode</span> </div>
                        </div>
                    </div>
                    <div class="row" style="display:none" id="url_div">
                        <div class="col-lg-12">
                            <label
                                class="text-dark my-3 fw-bolder fs-5 d-flex justify-content-between align-items-center"><span
                                    id="lbl_call_mode">&nbsp;</span></label>
                            <input type="text" name="url" id="url"
                                class="form-control form-control-solid">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12" style="position: relative;">
                            <label
                                class="text-dark my-3 fw-bolder fs-5 d-flex justify-content-between align-items-center">
                                Select Interview Panel </label>
                            <input class="form-control form-control-solid in_tr_email_panel" data-panel="1" name="panellist" id="panellist"
                                placeholder="Enter panellist" value="">
                            <div class="" style="position: absolute; right: 10px; top: 35.3px;">

                                 <a href="javascript:;" class="btn btn-primary btn-sm in_tr_email_panel"
                                    data-bs-toggle="modal" data-panel="1" data-bs-target="#kt_modal_create_project" style="    padding: 10px;">Add Interview Panel</a>


                                {{-- <a href="javascript:;" class="btn btn-primary btn-sm add_panel"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_create_project" style="height: 43px; padding: 12px;">Add Interview Panel</a> --}}
                            </div>
                        </div>
                        <span class="form-text text-muted">Please select panel list</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer flex-center">
            <div class="div1">
                <button type="close" id="" class="btn btn-secondary me-3"
                    data-dismiss="modal">Cancel</button>
                <input type="hidden" name="mailstatus" id="mailstatus" value="0">
                <input type="hidden" name="email_template_set" id="email_template_set" value="0">
                <button type="button" id="submitbtn" class="btn btn-light-primary me-3" onclick="submitForm();">
                    <span class="indicator-label">Save</span> <span class="indicator-progress">Please wait...<span
                            class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
            </div>
            <div class="div1 position-absolute end-0 me-9">
                <!-- <button type="button" id="" class="btn btn-secondary">Rescheduled</button> -->
                <button type="button" class="btn btn-light-dark btn-hover-dark" id="send_email_invies">Send Email
                    Invites</button>
            </div>
        </div>
    </div>
    <div id="div_interview_schedule_second" style="display:none">
        <!--begin::Modal header-->
        <div class="modal-header flex-stack">
            <!--begin::Title-->
            <h2>Interview Schedule Email</h2>
            <!--end::Title-->
            <!--begin::Close-->
            <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
                <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none">
                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                            transform="rotate(-45 6 17.3137)" fill="black" />
                        <rect x="7.41422" y="6" width="16" height="2" rx="1"
                            transform="rotate(45 7.41422 6)" fill="black" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
            </div>
            <!--end::Close-->
        </div>
        <!--end::Modal header-->
        <!--begin::Modal body-->
        <div class="modal-body scroll-y py-0 px-lg-7">
            <div data-kt-element="options">
                <div class="py-5">
                    <!--begin::Template-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label>
                        <div class="col-lg-10 col-xl-10">
                            <select name="email_template" id="email_template" class="form-control form-control-solid"
                                onchange="accessEmailTemplate(this.value, {{ @$application->id }} );">
                                <option value="">Select Pre Define Email Template </option>
                                @if (!empty($email_template))
                                    @foreach ($email_template as $email_temp)
                                        {
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
                    <!--end::Template-->
                    <!--begin::From email-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From</label>
                        <div class="col-lg-10 col-xl-10">
                            <input type="text" name="from_email" id="from_email"
                                placeholder="Ex: example@test.com" class="form-control form-control-solid"
                                value="{{ @$from_email }}" readonly>
                            <span class="form-text text-muted">Please enter email</span>
                        </div>
                    </div>
                    <!--end::From email-->
                    <!--begin::To email-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
                        <div class="col-lg-10 col-xl-10">
                            <input type="text" name="to_email" id="to_email" placeholder="Ex: demo@test.com"
                                class="form-control form-control-solid" value="{{ @$application->email }}" readonly>
                            <span class="form-text text-muted">Please enter email</span>
                        </div>
                    </div>
                    <!--end::To email-->
                    <!--begin::CC email-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">CC</label>
                        <div class="col-lg-10 col-xl-10" style="position: relative;">
                            <input type="text" name="cc_email" id="cc_email" placeholder="Ex: example2@test.com"
                                class="form-control form-control-solid" value="">
                            <span class="form-text text-muted">Please enter email</span>

                            <div class="" style="position: absolute; right: 13px; top: 4px;">
                                <a href="javascript:;" class="btn btn-primary btn-sm in_tr_email_panel"
                                    data-bs-toggle="modal" data-panel="0" data-bs-target="#kt_modal_create_project">Add E-mail ID</a>
                            </div>
                        </div>
                    </div>
                    <!--end::CC email-->
                    <!--begin::Subject-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
                        <div class="col-lg-10 col-xl-10">
                            <input type="text" name="subject" id="subject"
                                placeholder="Ex: Re-Schedule Interview" class="form-control form-control-solid">
                            <span class="form-text text-muted">Please enter subject</span>
                        </div>
                    </div>
                    <!--end::Subject-->
                    <!--begin::Body-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                        <div class="col-lg-10 col-xl-10">
                            <textarea id="editor1" name="editor1" class="form-control form-control-solid" row="10" placeholder="">&nbsp;</textarea>
                            <span class="form-text text-muted">Please enter email content</span>
                        </div>
                    </div>
                    <!--end::Body-->
                    <!--begin::Upload Files-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Upload Files
                            (2MB)</label>
                        <div class="col-xl-10 col-lg-10">
                            <input type="file" name="attachment" id="attachment"
                                class="dropzone-select btn btn-sm btn-primary me-2" />
                        </div>
                    </div>
                    <!--end::Upload Files-->
                </div>
            </div>
        </div>
        <!--begin::Modal body-->
        <!--begin::Modal footer-->
        <div class="modal-footer flex-center">
            <div class="div1">
                <!--begin::Cancel-->
                <button type="close" id="" class="btn btn-light-dark me-3"
                    data-dismiss="modal">Cancel</button>
                <!--end::Cancel-->
                <!--begin::Back-->
                <button type="button" id="" class="btn btn-light-info me-3 divback">Back</button>
                <!--end::Back-->
                <!--begin::Send Email-->
                <button type="button" id="sendEmailBtn" class="btn btn-light-primary sendEmailBtn"> <span
                        class="indicator-label">Send Email</span> <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
                <!--end::Send Email-->
            </div>
            <!--<span style="position: absolute;right: 2%;"></span>-->
            <div class="div2 position-absolute end-0 me-5 d-none"> </div>
        </div>
        <!--end::Modal footer-->
    </div>
</form>
<!--begin::Form-->


           
   


 <!--begin::Add Contact Person Modal-->
    <div class="modal modal fade" tabindex="-1" aria-hidden="true" id="add_team_member">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header" id="add_team_member_header">
                    <h2 class="fw-bolder">Add Contact Person</h2>
                    <div class="btn btn-icon btn-sm modal-close-team">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>


                </div>
                <div class="modal-body scroll-y mx-5">
                    <form id="add_team_member_form" name="add_team_member_form" class="form" action="#"
                        method="post">
                        <meta name="csrf-token" content="{{ csrf_token() }}" />
                        @csrf
                        @method('POST')
                        <div class="row client-cperson-row">
                            <div class="row client-cperson-container" style="position: relative;">
                                <span class="form-text text-muted form-label fw-bolder countContact"
                                    style="position: absolute; top: 0;"></span>
                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <input type="text" name="cp_name" id="cp_name_0"
                                            class="form-control form-control-lg form-control-solid rounded me-5"
                                            value="" placeholder="Enter person name">
                                        <span class="form-text text-muted">Please enter contact person name</span>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <input type="email" name="cp_email" id="cp_email_0"
                                            class="form-control form-control-lg form-control-solid rounded me-5"
                                            value="" placeholder="Enter email">
                                        <span class="form-text text-muted">Please enter contact email</span>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <input type="number" name="cp_number" id="cp_number_0"
                                            class="form-control form-control-lg form-control-solid rounded me-5"
                                            value="" placeholder="Enter contact number">
                                            <input type="hidden" value="{{ @$country_code }}" id="phone_country_code" name="phone_country_code">
                                        <span class="form-text text-muted">Please enter contact number</span>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <input type="text" name="cp_desigination" id="cp_desigination_0"
                                            class="form-control form-control-lg form-control-solid rounded" value=""
                                            placeholder="Enter desigination" style="">
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
                                    <button class="btn btn-primary "
                                        id="hiring_contact_person_add"> <span class="indicator-label">Submit</span> <span
                                            class="indicator-progress">Please wait... <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
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


{{-- <script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script> --}}
{{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}
<script src="https://preview.keenthemes.com/html/metronic/docs/assets/plugins/global/plugins.bundle.js"></script>
<script>
    $(document).ready(function() {


        const primaryPhoneInput = window.intlTelInput(document.querySelector("#cp_number_0"), {
      separateDialCode: true,
      initialCountry: "{{ $country_code }}",
      preferredCountries: ["in", "us", "gb", "ca", "au"],
    });


      // Function to update hidden country code inputs
    function updateCountryCode(inputField, hiddenField) {
      const selectedCountryData = inputField.getSelectedCountryData();
      //$(hiddenField).val(selectedCountryData.dialCode); // Set the country code in the hidden field
      const countryCode = selectedCountryData.iso2; // e.g., 'us', 'in', etc.
      $(hiddenField).val(countryCode);

    }
    // Update country code on input or country flag selection
    $("#cp_number_0").on("input change", function () {
      updateCountryCode(primaryPhoneInput, "#phone_country_code");
    });

   
    // Update country code dynamically when the user selects a different country flag
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
            // $("#hiring_contact_person_add").on("click", function(e) {
                e.preventDefault(); // Prevent the default form submission
                   // $(document).on('click','#hiring_contact_person_add', function() {
                var client_id = $('#client_idMail').val();
                var cp_name_0 = $('#cp_name_0').val();
                var cp_email_0 = $('#cp_email_0').val();
                var cp_number_0 = $('#cp_number_0').val();
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
                        //type  : 'GET',
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
                          }else{
                           var hiring_namager = $('#cc_email').val();
                          if(hiring_namager==''){
                            $('#cc_email').val(response.contact_person_name);
                          }else{
                            $('#cc_email').val(hiring_namager+','+response.contact_person_name);
                          }
                        }
                          $('#add_team_member_form')[0].reset();
                          $('#add_team_member').modal('hide');
                        }
                    });


                } else {
                    alert(
                        "Please Enter the Conract Person Details after Add"
                        );
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
            var interviewmentionlink = "{{ @$interviewmentionlink }}";
            setTimeout(() => {
                $('#url').val(interviewmentionlink);
            }, 500);

        }

        setTimeout(() => {
            var currentDate = new Date();
            var minDate = currentDate.toISOString().slice(0, 16); //Format: "YYYY-MM-DDTHH:mm"
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
        });
        $('body').on('click', '.divback', function() {
            $('#div_interview_schedule_first').show();
            $('#div_interview_schedule_second').hide();
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

        $("#submitbtn").prop("disabled", true);
        $("#submitbtn").html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
        );

        $.ajax({
            url: '{{ route('service_company.job-applications.store-schedule') }}',
            container: '#app_interview_create_frm',
            type: "POST",
            data: $('#app_interview_create_frm').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire("Success", "Interview schedule successfully!", "success").then(function() {
                        $("#data-dismiss").click();
                        location.reload();
                    });
                }
                if (response.status == 'coinCheck') {
                    Swal.fire("Success", ` ${response.message}`, "warning").then(function() {
                        $("#data-dismiss").click();
                    });
                }
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
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                redirect: true,
                success: function(response) {
                    $('.indicator-label').show();
                    $('.indicator-progress').hide();
                    if (response.status == 'faild') {
                        Swal.fire("Warning", "The file must not be larger than 2 MB ",
                            "warning").then(function() {

                        });
                    }


                    if (response.status == 'success') {
                        $('#mailstatus').val(1);
                        var email_template = $('#email_template').val();
                        $('#email_template_set').val(email_template);
                        var savesend = "{{ @$savesend }}";
                        if (savesend) {
                            submitForm();
                        } else {
                            Swal.fire("Success", "Interview schedule successfully!",
                                "success").then(function() {
                                $("#data-dismiss").click();  
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
            },
            complete: function(data) {
                //$('#childStatus_'+current_application_id).html('');
            }
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
            if (interviewDate == '') {
                alert("Please select interview date");
                return false;
            }
            if (interviewTime == '') {
                alert("Please select interview Time");
                return false;
            }
            var url = "{{ route('service_company.job-applications.getGoogleVideoUrl', ':currentId') }}";
            url = url.replace(':currentId', currentId);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'currentId': currentId,
                    'ParentId': ParentId,
                    'job_id': job_id,
                    'interviewDate': interviewDate,
                    'interviewTime': interviewTime,
                },
                beforeSend: function() {
                    $('#url_div').show();
                    $('#url').show();
                    $('#url').attr('placeholder', 'Generating Link .............');
                },
                success: function(response) {
                    if (response.status == "success") {
                        $('#url_div').show();
                        $('#url').val(response.getHangoutLink);
                        // var newOrganizerEmail = 'neworganizer@example.com';
                        // $.ajax({
                        // 		url: 'http://localhost/MEECRUITUAT/index.php/service_company/change-event-organizer/' + response.eventId,
                        // 		type: 'POST',
                        // 		data: {
                        // 			_token: '{{ csrf_token() }}', // CSRF token for security
                        // 			newOrganizerEmail: newOrganizerEmail
                        // 		},
                        // 		success: function(response) {
                        // 			// Handle the response from the server
                        // 			console.log('Success:', response);
                        // 			$('#url').val(response.newLink);	
                        // 		},
                        // 		error: function(xhr) {
                        // 			// Handle errors
                        // 			console.error('Error:', xhr.responseText);
                        // 		}
                        // });

                    } else {
           
                        Swal.fire({
                            title: 'Not Available',
                            text: `${response.getHangoutLink ?? ''} is currently not available, kindly use other options to schedule the interview`,
                            icon: 'question',
                            showCancelButton: false, // Show the cancel button
                            confirmButtonText: 'Ok',
                            cancelButtonText: 'No, cancel',
                            reverseButtons: true // Optional: to reverse the order of the buttons
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // $.ajax({
                                //     url: '{{ route('redirectToGoogle') }}',
                                //     method: 'GET',
                                //     success: function(response) {
                                //         var newWindow = window.open(response.authUrl,
                                //             'popupWindow',
                                //             'width=600,height=400,scrollbars=no,resizable=no'
                                //             );
                                //         setTimeout(function() {
                                //             // newWindow.close();
                                //             var selectElement = document
                                //                 .getElementById(
                                //                     'interview_mode_lavels');
                                //             selectElement.selectedIndex =
                                //             0; // Selects the first option
                                //         }, 5000);
                                //         // window.location.href = response.authUrl;
                                //         // Handle the response
                                //         $('#responseContainer').html('<p>' + response
                                //             .message + '</p>');
                                //     },
                                //     error: function(xhr) {
                                //         // Handle errors
                                //         $('#responseContainer').html(
                                //             '<p>An error occurred: ' + xhr
                                //             .statusText + '</p>');
                                //     }
                                // });

                            } else if (result.isDismissed) {
                                // Handle the case when the user cancels
                                console.log(`User cancelled the ${response.getHangoutLink ?? ''} request.`);
                            }
                        });




                        $('.sub_status').hide();
                    }
                },
                // complete: function(data) {}
            });

        }
        if ((ParentId == 2) && (currentId == 5)) { // FOR GOOGLE

            $('#url_div').show();
            $('#url').show();
            $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Microsoft Teams Meeting Link');
            var interviewDate = $('#interviewDate').val();
            var interviewTime = $('#interviewTime').val();
            if (interviewDate == '') {
                alert("Please select interview date");
                return false;
            }
            if (interviewTime == '') {
                alert("Please select interview Time");
                return false;
            }
            var url = "{{ route('service_company.job-applications.getGoogleVideoUrl', ':currentId') }}";
            url = url.replace(':currentId', currentId);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'currentId': currentId,
                    'ParentId': ParentId,
                    'job_id': job_id,
                    'interviewDate': interviewDate,
                    'interviewTime': interviewTime,
                },
                beforeSend: function() {
                    $('#url_div').show();
                    $('#url').show();
                    $('#url').attr('placeholder', 'Generating Link .............');
                },
                success: function(response) {
                    if (response.status == "success") {
                        $('#url_div').show();
                        $('#url').val(response.getHangoutLink);

                    } else {
           
                        Swal.fire({
                            title: 'Not Available',
                            text: `${response.getHangoutLink ?? ''} is currently not available, kindly use other options to schedule the interview`,
                            icon: 'question',
                            showCancelButton: false, // Show the cancel button
                            confirmButtonText: 'Ok',
                            cancelButtonText: 'No, cancel',
                            reverseButtons: true // Optional: to reverse the order of the buttons
                        }).then((result) => {
                            if (result.isConfirmed) {
                                
                                

                            } else if (result.isDismissed) {
                                // Handle the case when the user cancels
                                console.log(`User cancelled the ${response.getHangoutLink ?? ''} request.`);
                            }
                        });




                        $('.sub_status').hide();
                    }
                },
                // complete: function(data) {}
            });

        }

        if ((ParentId == 2) && (currentId == 3)) { // FOR ZOOM

            var interviewDate = $('#interviewDate').val();
            var interviewTime = $('#interviewTime').val();
            if (interviewDate == '') {
                alert("Please select interview date");
                return false;
            }
            if (interviewTime == '') {
                alert("Please select interview Time");
                return false;
            }
            var url = "{{ route('service_company.job-applications.getGoogleVideoUrl', ':currentId') }}";
            url = url.replace(':currentId', currentId);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'currentId': currentId,
                    'ParentId': ParentId,
                    'job_id': job_id,
                    'interviewDate': interviewDate,
                    'interviewTime': interviewTime
                },
                beforeSend: function() {
                    $('#url_div').show();
                    $('#url').show();
                    $('#url').attr('placeholder', 'Generating Link .............');
                    //$('#url_div').html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "success") {
                        $('#url_div').show();
                        $('#url').show();
                        $('#lbl_call_mode').show();
                        $('#lbl_call_mode').html('Zoom Link');
                        if (response.mode == 'zoom') {
                            $('#url').val(response.getHangoutLink);
                        } else {
                            $('#url').val('');
                        }
                    } else {

                        	Swal.fire({
                        	title: 'Not Available',
                        	text: `${response.getHangoutLink ?? ''} is currently not available, kindly use other options to schedule the interview.`,
                        	icon: 'question',
                        	showCancelButton: false, // Show the cancel button
                        	confirmButtonText: 'Ok',
                        	cancelButtonText: 'No, cancel',
                        	reverseButtons: true // Optional: to reverse the order of the buttons
                        }).then((result) => {
                        	if (result.isConfirmed) {
                        	// $.ajax({
                            //     url: '{{ route('redirectToZoom') }}',
                            //     method: 'GET',
                            //     success: function(response) {
                            //         var newWindow = window.open(response.authUrl, 'popupWindow', 'width=600,height=400,scrollbars=no,resizable=no');
                            //         var selectElement = document.getElementById('interview_mode_lavels');
                            //             selectElement.selectedIndex = 0; // Selects the first option
                            //         setTimeout(function() {
                            //             newWindow.close();
                            //             var selectElement = document.getElementById('interview_mode_lavels');
                            //             selectElement.selectedIndex = 0; // Selects the first option
                            //         }, 5000);

                            //         $('#responseContainer').html('<p>' + response.message + '</p>');
                            //     },
                            //     error: function(xhr) {
                            //         // Handle errors
                            //         $('#responseContainer').html('<p>An error occurred: ' + xhr.statusText + '</p>');
                            //     }
                            // });

                        	} else if (result.isDismissed) {
                        		// Handle the case when the user cancels
                        		console.log(`User cancelled the ${response.getHangoutLink ?? ''} request.`);
                        	}
                        });


                        $('.sub_status').hide();
                    }
                },
                complete: function(data) {}
            });
        }

        if ((ParentId == 2) && currentId == 6) { // FOR OTHER
            $('#url_div').show();
            $('#url').show();
            $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Other Video Link');
            $('#url').attr('placeholder', 'Enter Link here');
        } else if ((ParentId == 1) && ((currentId == 1) || (currentId == 4))) { // PHONE CALL
            $('#url_div').show();
            $('#url').show();
            $('#url').attr('placeholder', 'Enter Link here');
            $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('');
            if (currentId == 1) {
                $('#lbl_call_mode').html('Phone Call');
                $('#url').val('Phone Call');
                $("#url").prop("readonly", true);
            }
            if (currentId == 4) {
                $('#lbl_call_mode').html('Skype Call');
                $('#url').val('Skype Call');
                $("#url").prop("readonly", true);
            }
        } else if ((ParentId == 3) && (currentId >= 0)) {
            $('#url_div').show();
            $('#url').show();
            $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Interview Address/Location');
            var location = $('#interview_mode_lavels :selected').text();
            if (currentId > 0) {
                $('#url').val(location);
            }

        }
    }
    setTimeout(() => {
        let mode = $('#interview_mode').val();
    
     let job_id = @json($job_id); // safe conversion
     if (mode) {     
            getInterviewModeChild(mode,job_id);
        }
    }, 1000);
     



</script>
