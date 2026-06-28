@php
    use App\Helper\GeneralMethod;
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();

@endphp
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
            var editorElement = CKEDITOR.document.getById('editor1mass');

            if (isBBCodeBuiltIn) {
                editorElement.setHtml(
                    'Hello world!\n\n' +
                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                );
            }
            if (wysiwygareaAvailable) {
                CKEDITOR.replace('editor1mass');
            } else {
                editorElement.setAttribute('contenteditable', 'true');
                CKEDITOR.inline('editor1mass');
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

<div class="main_container" id="main_container">
    @php
        $template_body = '';

        $to_email = '';
        if ($to_email_from_count > 0) {
            $to_email = implode(', ', $to_email_from_data);
        }

        $cc_email = '';
        if ($cc_email_count > 0) {
            $cc_email = implode(', ', $cc_email_data);
        }
        
        $applicationDataID = '';
        if(count($applicationData) > 0){
            $applicationDataID = $applicationData[0]->id;
        }

    @endphp

    <form id="form1">
        <!--begin::Modal body-->
        <div class="modal-body scroll-y py-0 px-lg-7">
            <div data-kt-element="options">
                <div class="py-5">
                    <!--begin::Template-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label>
                        <div class="col-lg-10 col-xl-10">
                            <input type="hidden" value="{{ $jobid }}" name="jobid" id="jobid">
                            <input type="hidden" value="{{$applicationDataID }}" name="applicationDataId" id="applicationDataId">
                             {{-- @dd($templateData) --}}
                            <select name="templatemassmail" id="templatemassmail" class="form-select form-select-solid"
                                aria-label="Select template">
                                <option value="">Select Email Template...</option>

                                @if (!empty($templateData))
                                    @foreach ($templateData as $tdata)
                                   
                                        @if (@$tdata->tempForInterview == 1 && $selfCompany == 1)
                                            <option value="{{ $tdata->id }}">
                                                {{ str_replace(['[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_LOCATION}]'], [$companyTemp, $job_title, $location], $tdata->display_name) }}
                                            </option>
                                        @endif
                                        @if ($selfCompany == '')
                                            <option value="{{ $tdata->id }}">
                                                {{ str_replace(['[{CLIENT_NAME}]', '[{COMPANY_NAME}]', '[{JOB_LOCATION}]', '[{JOB_TITLE}]'], [$clientTemp, $companyTemp, $location], $tdata->display_name, $job_title) }}
                                            </option>
                                        @endif

                                        @if (@$tdata->tempForInterview == 4 && $selfCompany == 1)
                                            <option value="{{ $tdata->id }}">
                                                {{ str_replace(['[{COMPANY_NAME}]'], [$companyTemp], $tdata->display_name) }}
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
                            <input class="form-control form-control-solid" name="from_email_mass_mail"
                                id="from_email_mass_mail" value="{{ $userdata->email }}" readonly=""
                                data-role="from_email">
                            <span class="form-text text-muted">Please enter email</span>
                        </div>
                    </div>
                    <!--end::From email-->
                    <!--begin::To email-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
                        <div class="col-lg-10 col-xl-10">
                            <input class="form-control form-control-solid" name="to_email_mass_mail"
                                id="to_email_mass_mail" value="{{ @$cc_email }}">
                            <span class="form-text text-muted">Please enter email</span>
                        </div>
                    </div>
                    <!--end::To email-->

                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">CC</label>
                        <div class="col-lg-10 col-xl-10" style="position: relative;">
                            <input class="form-control form-control-solid" name="sendmail_panel" id="sendmail_panel"
                                value="">
                            <span class="form-text text-muted">Please enter email</span>

                            <div class="" style="position: absolute; right: 13px; top: 4px;">
                                <a href="javascript:;" class="btn btn-primary btn-sm sendmail_panel"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_create_project">Add E-mail ID</a>
                            </div>

                        </div>

                    </div>


                    <!--begin::Subject-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
                        <div class="col-lg-10 col-xl-10">
                            <input class="form-control form-control-solid" name="subject" id="subject_mass_mail"
                                placeholder="Enter subject" value="">
                            <span class="form-text text-muted">Please enter subject</span>
                        </div>
                    </div>
                    <!--end::Subject-->
                    <!--begin::Body-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                        <div class="col-lg-10 col-xl-10">
                            <textarea id="editor1mass" name="editor1mass" row="10">
				{{ @$template_body }}
			</textarea>
                            <span class="form-text text-muted"> Please enter email template body content </span>
                        </div>
                    </div>
                    <!--begin::Files-->
                    <div class="form-group row mb-4 d-none">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Attach CV</label>
                        <div class="col-xl-10 col-lg-10">
                            <!--begin::Dropzone-->
                            <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
                                <!--begin::Controls-->
                                <div class="dropzone-panel mb-lg-0 mb-2">
                                    <input class="" name="attached_cv" id="attached_cv" type="checkbox"
                                        value="1" checked="checked" />
                                </div>
                                <!--end::Controls-->
                            </div>
                            <!--end::Dropzone-->
                        </div>
                    </div>
                    <!--end::Files-->
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
                <!--begin::Send-->
                <button type="button" class="btn btn-light-primary" name="btnSubmitSendMail" id="btnSubmitSendMail"
                    value="btnSubmitSendMail"> <span class="indicator-label">Send</span> <span
                        class="indicator-progress">Please wait... <span
                            class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
                <!--end::Send-->
            </div>
            <!--<span style="position: absolute;right: 2%;"></span>-->
            <div class="div2 position-absolute end-0 me-5 d-none"> </div>
        </div>
        <!--end::Modal footer-->
        <!--</form>-->
</div>
</form>


<div id="kt_activitiesIntTeam_toggle_sendmail"></div>
<div id="kt_activitiesIntTeam_sendmail" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
    data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesIntTeam_toggle_sendmail"
    data-kt-drawer-close="#kt_activitiesIntTeam_close_sendmail">
    <div class="card-body position-relative px-0 py-0" id="kt__to_body">
        <div id="kt_sendmail_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt__sendmail_body"
            data-kt-scroll-dependencies="#kt__sendmail_header, #kt__sendmail_footer" data-kt-scroll-offset="5px"
            style="">
            <!--begin::Team card-->
            <div class="card card-custom mx-2">
                <!--begin::Team header-->
                <div class="card-header px-5 py-0" id="kt_to_header">
                    <div class="d-flex align-items-center py-1">
                        <div class=" d-flex align-items-center">
                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                    class="text-dark fw-bolder fs-6" id="light_to_name">Assign Members</a> </div>
                        </div>
                        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                            id="kt_activitiesIntTeam_close_sendmail"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                        fill="currentColor">
                                        <rect x="0" y="7" width="16" height="2"
                                            rx="1" />
                                        <rect opacity="0.5"
                                            transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                            x="0" y="7" width="16" height="2" rx="1" />
                                    </g>
                                </svg>
                            </span> </button>
                    </div>
                </div>
                <!--end::Team header-->
                <!--begin::Team Content-->
                <div class="card-body py-7">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_to">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="fw-bolder text-muted">
                                <th class="min-w-250px">Name</th>
                                <th class="min-w-250px">Contact</th>
                                <th class="min-w-250px">Company</th>
                                <th class="min-w-100px">Action</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold">
                            @if (!empty($cc_email_assignData))

                                @foreach ($cc_email_assignData as $teamVal)
                                    @php
                                        $bgTextDecore = $strVal = '';
                                        $str = trim(@$teamVal->name);
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

                                        $working_status = $working_status_style = '';
                                        $working_status = @$teamVal->working_status == 1 ? 'Enable' : 'Disable';
                                        $working_status_style =
                                            @$teamVal->working_status == 1 ? 'text-success' : 'text-danger';
                                    @endphp

                                    <tr>
                                        <!--begin::Name-->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin::Avatar-->
                                                <div
                                                    class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                                                    <a href="javascript:void(0);" class="">
                                                        <div
                                                            class="symbol-label {{ @$bgTextDecore }} border-radius-5">
                                                            {{ strtoupper(@$strVal) }}</div>
                                                    </a> </div>
                                                <!--end::Avatar-->
                                                <!--begin::Name details-->
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 15)) }}</span>
                                                    <span
                                                        class="text-muted fw-bold d-block fs-7">{{ ucwords(substr(@$teamVal->display_name, 0, 15)) }}</span>
                                                </div>
                                                <!--end::Name details-->
                                            </div>
                                        </td>
                                        <!--end::Name-->
                                        <!--begin::Role-->
                                        <td>
                                            <div class="d-flex flex-column"> <span
                                                    class="text-dark fw-bolder fs-6"></span> {{ @$teamVal->email }}
                                            </div>
                                        </td>
                                        <!--begin::Role-->
                                        <td>
                                            <div class="d-flex flex-column"> <span
                                                    class="text-dark fw-bolder fs-6"></span>
                                                {{ @$teamVal->company_name }}</div>
                                        </td>
                                        <!--begin::Action-->
                                        <td>
                                            <div class="d-flex flex-column"> <a
                                                    id="sendmail_team_select_{{ @$teamVal->id }}"
                                                    href="javascript:void(0);"
                                                    class="sendmail_select btn btn-sm btn-light-primary w-130"
                                                    rel="{{ @$teamVal->id }}" data-assign="0"
                                                    data-title="{{ @$teamVal->name }}"
                                                    data-email="{{ @$teamVal->email }}">Add</a> </div>
                                        </td>
                                        <!--end::Action-->
                                    </tr>
                                @endforeach
                            @endif


                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Team Content-->
            </div>
            <!--end::Team card-->
        </div>
    </div>
</div>


<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
{{-- <script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script> --}}
<script>
    $('#ck-editor__editable_inline').html("testing");


    $('#templatemassmail').change(function() {


        var template = $('#templatemassmail').val();
        if (template > 0) {
            var id = $('#templatemassmail').val();
            var url = "{{ route('service_company.cvtracker.email_template', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var to_email = $('#phone').val();
            var from_email = $('#f_phone').val();
            var jobid = $('#jobid').val();
            var mailtype = 4

            var selectedApplicantListing = [];
            var jqueryarray = <?php echo json_encode($applicationData); ?>;
            jqueryarray.map((mval, mid, marr) => {
                selectedApplicantListing.push(mval.id);
            });

            var jqueryarrayjobs = <?php echo json_encode($applicationData); ?>;

            var jobID = jobid;//jqueryarrayjobs[0]['jobID'];


            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'template': template,
                    'selectedApplicantListing': selectedApplicantListing,
                    'to_email': to_email,
                    'from_email': from_email,
                    'mailtype': mailtype,
                    'jobID': jobID
                },
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {


                    if (response.status == "success") {


                        CKEDITOR.instances.editor1mass.setData(response.template_description);
                        setTimeout(() => {
                            $('#editor1mass').val(response.template_description);
                            $('#subject_mass_mail').val(response.subject);
                        }, 500);

                    }
                }
            });

        } else {
            $('#editor').html('Choose email template');
        }



    });


    document.getElementById("btnSubmitSendMail").addEventListener("click", () => {



        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        
           

                var id = 1;
                var url = "{{ route('service_company.cvtracker.send_mail_for_candidate', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";

                var jqueryarray = <?php echo json_encode($applicationData); ?>;

                var sid = jqueryarray[0]['id'];

                var arr = [sid];
                // 	$('.chk_list_item:checked').each(function () {
                // 			arr.push($(this).val());
                // 	});
                // 	alert(sid);
                var template = $('#templatemassmail').val();
                var from_email = $('#from_email_mass_mail').val();
                var to_email = $('#to_email_mass_mail').val();
                var cc_email = $('#sendmail_panel').val();
                var subject = $('#subject_mass_mail').val();
                var jobid = $('#jobid').val();
                var applicationDataId = $('#applicationDataId').val();
                var attached_cv = 0;
                var editor1mass = CKEDITOR.instances.editor1mass.getData(); //$('#editor1mass').val();

                // alert(editor1mass);

                // $('#attached_cv:checkbox:checked').each(function(){
                // 	attached_cv	=	1;
                // });

                $('.indicator-label').hide();
                $('.indicator-progress').show();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'POST',
                        'template': template,
                        'from_email': from_email,
                        'to_email': to_email,
                        'cc_email': cc_email,
                        'subject': subject,
                        'attached_cv': attached_cv,
                        'editor1': editor1mass,
                        'jobid': jobid,
                        'applicationDataId': applicationDataId,
                        'arr': arr
                    },
                    beforeSend: function() {
                        // $('#main_container').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    },
                    success: function(response) {
                        $('.indicator-label').show();
                        $('.indicator-progress').hide();

                        if (response.status == 'coinCheck') {

                            Swal.fire("Failed", ` ${response.message}`, "warning").then(
                                function() {
                                    $(".btn-active-light-danger").click();
                                });
                            return false;
                        }

                        if (response.status == "success") {

                            //   $('#main_container').html(response.view);
                            swalWithBootstrapButtons.fire(
                                'Success!',
                                'Email has been sent.',
                                'success'
                            )
                            $(".btn-active-light-danger").click();
                            // location.reload();

                        }


                    }
                });
        



    });

    $(document).on('click','.sendmail_select', function(e){

        var rel = $(this).attr('rel');
        var assign = $('#sendmail_team_select_' + rel).attr("data-assign");
        var title = $('#sendmail_team_select_' + rel).attr("data-title");
        var email = $('#sendmail_team_select_' + rel).attr("data-email");

        if (assign == 0) {
            $(this).addClass("btn-light-danger");
            $(this).removeClass("btn-light-primary");
            $(this).text('Remove');
            $(this).attr("data-assign", 1);
            AddEventSend(title, email);
        } else if (assign == 1) {
            $(this).addClass("btn-light-primary");
            $(this).removeClass("btn-light-danger");
            $(this).text('Add');
            $(this).attr("data-assign", 0);
            DeleteEventTo(title, email);
        }

    });

    function AddEventSend(name, email) {

        var to_email = $('#sendmail_panel').val();
        if (to_email != '') {
            var includeStr = to_email.includes(email);
            if (includeStr === false) {
                var assignNewEmail = to_email + ' , ' + email;
                $('#sendmail_panel').val(assignNewEmail);

            }
        } else {
            $('#sendmail_panel').val(email);

        }


    }

    function DeleteEventTo(contact_person_name, email) {

        var team_hiring_manager_email = '';
        var tempEmail = [];
        var to_email = $('#sendmail_panel').val();

        if (to_email != '') {

            var emailArr = to_email.split(',');
            for (var inc = 0; inc < emailArr.length; inc++) {
                if (email.trim() != emailArr[inc].trim()) {
                    tempEmail.push(emailArr[inc]);

                }
            }
            team_hiring_manager_email = tempEmail.toString();


        }

        $('#sendmail_panel').val(team_hiring_manager_email);

    }
</script>
<script>
    // $.noConflict();

    var from_email = document.querySelector('#from_email_mass_mail');
    new Tagify(from_email);

    var to_email = document.querySelector('#to_email_mass_mail');
    new Tagify(to_email);

    // var cc_email	=	document.querySelector('#cc_email');
    // new Tagify(cc_email);
</script>
