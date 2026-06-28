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
            var editorElement = CKEDITOR.document.getById('editorwhatsapp');

            if (isBBCodeBuiltIn) {
                editorElement.setHtml(
                    'Hello world!\n\n' +
                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                );
            }
            if (wysiwygareaAvailable) {
                CKEDITOR.replace('editorwhatsapp');
            } else {
                editorElement.setAttribute('contenteditable', 'true');
                CKEDITOR.inline('editorwhatsapp');
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
        

        $cc_email = '';
        
        
        $applicationDataID = '';
        $applicationDataID = '';
        if(($applicationData)){
            $applicationDataID = $applicationData->id;
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
                            <select name="templatewhatsapp" id="templatewhatsapp" class="form-select form-select-solid"
                                aria-label="Select template">
                                <option value="">Select Whatsapp Template...</option>
                                @if (!empty($templateData))
                                    @foreach ($templateData as $tdata)

                                            <option value="{{ $tdata->id }}">
                                                {{ str_replace(['[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{CLIENT_NAME}]'], [$companyTemp, $job_title, $clientTemp], $tdata->display_name) }}
                                            </option>
                                      
                                    @endforeach
                                @endif


                            </select>
                            <span class="form-text text-muted">Please select email template</span>
                        </div>
                    </div>
                   
                    <!--end::From email-->
                    <!--begin::To email-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From Mobile No.</label>
                        <div class="col-lg-10 col-xl-10" style="position: relative;">
                            <input class="form-control form-control-solid" name="f_phone"
                                id="f_phone_{{ auth()->user()->id }}" value="{{ $connectedNumberGet ?? auth()->user()->mobile }}" readonly>
                                <div>
                                <div class="connectBtn" id="{{ $connectBtn }}" style="position: absolute; right: {{ (!$connectBtn) ? 90 : 10 }}px; top: 4px;">

                                    <a href="javascript:;" data-userid="{{ auth()->user()->id }}" class="btn btn-sm btn-primary sendwhatsapp_panel sendwhatsapp_connect_{{ auth()->user()->id }} sendwhatsapp_text_{{ auth()->user()->id }} {{$sectionTwodnone}}"><i class="fa fa-whatsapp" style=""></i> <span id="">Click to Connect</span></a>

                                     <a href="javascript:;" class="btn btn-sm btn-success sendwhatsapp_connected_{{ auth()->user()->id }} {{$sectionOnednone}}"><i class="fa fa-whatsapp" style=""></i> <span id="">Whatsapp Connected</span></a>

                                    {{-- <a href="javascript:;" data-userid="{{auth()->user()->id }}" id="whatsappConnectBtn" class="btn {{$classname}} btn-sm sendwhatsapp_panel"
                                        ><i class="fa fa-whatsapp" style=" font-size: 22px;"></i><span id="whatsappBtnText">{{ $textvalue }}</span></a> --}}
                                        
                                </div>
                                <div class="" style="position: absolute; right: 13px; top: 4px;">
                                    <button data-userid="{{auth()->user()->id }}" class="{{$connectBtndnone}} deleteBtn sendwhatsapp_connected_{{ auth()->user()->id }}" id="" style="background: red; padding: 6px; color: white; border-radius: 4px; font-weight: 600;"><i class="fa fa-power-off" style=""></i> Disconnect</button>
                                </div>
                                </div>
                        </div>
                    </div>
                    <!--end::To email exists_number-->
                    <!--begin::To email-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Candidate Mobile No.</label>
                        <div class="col-lg-10 col-xl-10" style="position: relative;">
                            <input class="form-control form-control-solid" name="phone"
                                id="phone" value="{{ $applicationData->phone }}">
                                {{-- <div class="" style="position: absolute; right: 13px; top: 4px;">
                                    <a href="javascript:;" id="whatsappConnectNumber" class="btn {{$classnamewhatsapp}} btn-sm"
                                        ><i class="fa fa-whatsapp" style=" font-size: 22px;"></i><span id="whatsappBtnTextTo">{{ $textvaluewhatsapp }}</span></a>
                                </div> --}}
                            <span class="form-text text-muted">Please enter mobile</span>
                        </div>
                    </div>
                    <!--end::To email-->
                    
                   
                    <!--end::Subject-->
                    <!--begin::Body-->
                    <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                        <div class="col-lg-10 col-xl-10">
                            <textarea id="editorwhatsapp" name="editorwhatsapp" row="10">
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



<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
{{-- <script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script> --}}
<script>



    $('#ck-editor__editable_inline').html("testing");


    $('#templatewhatsapp').change(function() {


        var template = $('#templatewhatsapp').val();
       
        if (template > 0) {
            var id = $('#templatewhatsapp').val();
            var url = "{{ route('service_company.cvtracker.email_template', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var to_email = $('#to_email_mass_mail').val();
            var from_email = $('#from_email_mass_mail').val();
            var jobid = $('#jobid').val();
            var token = "{{ csrf_token() }}";
            var mailtype = 9
            var applicationDataId =  $('#applicationDataId').val();
             var selectedApplicantListing = [applicationDataId];

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


                        CKEDITOR.instances.editorwhatsapp.setData(response.template_description);
                        setTimeout(() => {
                            $('#editorwhatsapp').val(response.template_description);
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
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes,  Please Whatsapp!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {

                var id = 1;
                var url = "{{ url('/whatsapp/send') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var token = "{{ csrf_token() }}";

                var phone = $('#phone').val();
                var templatewhatsapp = $('#templatewhatsapp').val();
                var editorwhatsapp = CKEDITOR.instances.editorwhatsapp.getData(); //$('#editorwhatsapp').val();
                var userid = "{{ auth()->user()->id }}";
                var application_id = "{{ $applicationData->id }}";
                var application_email = "{{ $applicationData->email }}";
                var job_id = "{{ $applicationData->jobID }}";

                
                $('.indicator-label').hide();
                $('.indicator-progress').show();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'POST',
                        'phone': phone,
                        'userid': userid,
                        'application_id': application_id,
                        'application_email': application_email,
                        'job_id': job_id,
                        'templatewhatsapp': templatewhatsapp,
                        'message': editorwhatsapp
                    },
                    beforeSend: function() {
                        // $('#main_container').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    },
                    success: function(response) {
                        $('.indicator-label').show();
                        $('.indicator-progress').hide();

                        if (response.status == 'error') {

                            Swal.fire("Failed", ` ${response.message}`, "warning").then(
                                function() {
                                    // $(".btn-active-color-primary").click();
                                });
                            return false;
                        }

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
                                ` ${response.message}`,
                                'success'
                            )
                            $(".btn-active-light-danger").click();
                            // location.reload();

                        }


                    }
                });



            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Selected request is safe :)',
                    'error'
                )
            }
        })



    });


</script>

