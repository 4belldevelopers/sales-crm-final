@php
		use App\Helper\GeneralMethod;
		$salaryMode 					=	GeneralMethod::salaryMode();
		$currencySymbole 				=	GeneralMethod::currencySymbole();

@endphp
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>

if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )

CKEDITOR.tools.enableHtml5Elements( document );
CKEDITOR.config.height 		=	350;
CKEDITOR.config.width 		=	'auto';

var initSample 				= ( function()
{
		var wysiwygareaAvailable 	= isWysiwygareaAvailable(),
		isBBCodeBuiltIn 			= !!CKEDITOR.plugins.get( 'bbcode' );

	return function() {
								var editorElement = CKEDITOR.document.getById( 'editor1mass' );

								if ( isBBCodeBuiltIn ) {
									editorElement.setHtml(
										'Hello world!\n\n' +
										'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
									);
								}
								if ( wysiwygareaAvailable ) {
									CKEDITOR.replace( 'editor1mass' );
								} else {
									editorElement.setAttribute( 'contenteditable', 'true' );
									CKEDITOR.inline( 'editor1mass' );
								}
					};
	function isWysiwygareaAvailable()
	{
		if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
			return true;
		}
		return !!CKEDITOR.plugins.get( 'wysiwygarea' );
	}

} )();


initSample();


</script>

<style>
/* ── Send Email Drawer form (mm-*) ── */
.mm-wrap {
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    color: #374151;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.mm-form-body {
    flex: 1;
    overflow-y: auto;
}
.mm-row {
    display: flex;
    align-items: flex-start;
    padding: 16px 24px;
    border-bottom: 1px solid #F3F4F6;
    gap: 16px;
}
.mm-label-col {
    width: 150px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-top: 9px;
    font-size: 12.5px;
    font-weight: 600;
    color: #374151;
}
.mm-label-icon {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.mm-label-icon svg { width: 14px; height: 14px; }
.mm-ic-blue   { background: #EFF6FF; color: #2563EB; }
.mm-ic-green  { background: #DCFCE7; color: #16A34A; }
.mm-ic-purple { background: #F5F3FF; color: #7C3AED; }
.mm-ic-teal   { background: #F0FDF4; color: #0D9488; }
.mm-ic-orange { background: #FFF7ED; color: #EA580C; }
.mm-req { color: #EF4444; margin-left: 1px; }
.mm-field-col { flex: 1; min-width: 0; }
.mm-input {
    width: 100%;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 9px 14px;
    font-size: 13px;
    color: #374151;
    font-family: 'Poppins', sans-serif;
    background: #F9FAFB;
    outline: none;
    transition: border-color 0.15s;
}
.mm-input:focus { border-color: #93C5FD; background: #fff; }
.mm-input[readonly] { color: #6B7280; cursor: default; background: #F3F4F6; }
.mm-hint {
    font-size: 11.5px;
    color: #9CA3AF;
    margin-top: 5px;
    line-height: 1.4;
}
/* Tagify override to match mm-input style */
.mm-field-col .tagify {
    border: 1px solid #E5E7EB !important;
    border-radius: 8px !important;
    background: #F9FAFB !important;
    padding: 4px 10px !important;
    font-family: 'Poppins', sans-serif !important;
    min-height: 40px !important;
}
.mm-field-col .tagify:focus-within { border-color: #93C5FD !important; background: #fff !important; }
.mm-field-col .tagify--readonly { background: #F3F4F6 !important; }
.mm-select {
    width: 100%;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 9px 36px 9px 14px;
    font-size: 13px;
    color: #374151;
    font-family: 'Poppins', sans-serif;
    background: #F9FAFB url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center;
    appearance: none;
    -webkit-appearance: none;
    outline: none;
    cursor: pointer;
    transition: border-color 0.15s;
}
.mm-select:focus { border-color: #93C5FD; background-color: #fff; }
/* CKEditor wrapper */
.mm-editor-wrap { border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; }
.mm-row-body { align-items: flex-start; }
/* Footer */
.mm-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 24px;
    border-top: 1px solid #E5E7EB;
    background: #F9FAFB;
    flex-shrink: 0;
    gap: 10px;
    flex-wrap: wrap;
}
.mm-footer-left { display: flex; align-items: center; gap: 8px; }
.mm-footer-right { display: flex; align-items: center; gap: 8px; margin-left: auto; }
.mm-btn-cancel {
    padding: 8px 18px;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
    background: #fff;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: background 0.15s;
}
.mm-btn-cancel:hover { background: #F3F4F6; }
.mm-btn-send {
    padding: 8px 20px;
    border-radius: 8px;
    border: none;
    background: #2563EB;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: background 0.15s;
}
.mm-btn-send:hover { background: #1d4ed8; }
.mm-btn-delete {
    padding: 8px 16px;
    border-radius: 8px;
    border: 1px solid #FECACA;
    background: #FEF2F2;
    color: #DC2626;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: background 0.15s;
}
.mm-btn-delete:hover { background: #FEE2E2; }
</style>

@php
$template_body 	=	'';

$to_email	=	'';
if($to_email_from_count > 0){
		$to_email = implode(', ', $to_email_from_data);
}

$cc_email	=	'';
if($cc_email_count > 0){
		$cc_email = implode(', ', $cc_email_data);
}
@endphp

<div class="mm-wrap" id="main_container">
<form id="form1">

    <div class="mm-form-body">

        {{-- From Email ID --}}
        <div class="mm-row">
            <div class="mm-label-col">
                <span class="mm-label-icon mm-ic-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/>
                    </svg>
                </span>
                From Email ID
            </div>
            <div class="mm-field-col">
                <input class="mm-input" name="from_email_mass_mail" id="from_email_mass_mail"
                    value="{{ $userdata->email }}" readonly data-role="from_email">
                <div class="mm-hint">This will be the email sender address</div>
            </div>
        </div>

        {{-- To Email ID --}}
        <div class="mm-row">
            <div class="mm-label-col">
                <span class="mm-label-icon mm-ic-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </span>
                To Email ID <span class="mm-req">*</span>
            </div>
            <div class="mm-field-col">
                <input class="mm-input" name="to_email_mass_mail" id="to_email_mass_mail"
                    value="{{ @$cc_email }}">
                <div class="mm-hint">Please enter email</div>
            </div>
        </div>

        {{-- Template --}}
        <div class="mm-row">
            <div class="mm-label-col">
                <span class="mm-label-icon mm-ic-purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </span>
                Template
            </div>
            <div class="mm-field-col">
                <select name="templatemassmail" id="templatemassmail" class="mm-select">
                    <option value="">Select Email Template...</option>
                    @php
                        $specialTemplates = collect($templateData)->where('id', 41);
                        $defaultTemplates = collect($templateData)->whereNull('company_id')->where('id', '!=', 41);
                        $companyTemplates = collect($templateData)->whereNotNull('company_id')->where('id', '!=', 41);
                    @endphp
                    @if($defaultTemplates->count())
                        <optgroup label="Default Templates">
                            @foreach($defaultTemplates as $tdata)
                                <option value="{{ $tdata->id }}">
                                    {{ str_replace(['[{COMPANY_NAME}]'], [$company_name], $tdata->display_name) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                    @if($specialTemplates->count())
                        <optgroup label="Custom Template">
                            @foreach($specialTemplates as $tdata)
                                <option value="{{ $tdata->id }}">
                                    {{ str_replace(['[{COMPANY_NAME}]'], [$company_name], $tdata->display_name) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                    @if($companyTemplates->count())
                        <optgroup label="Custom Template Lists">
                            @foreach($companyTemplates as $tdata)
                                <option value="{{ $tdata->id }}">
                                    {{ str_replace(['[{COMPANY_NAME}]'], [$company_name], $tdata->display_name) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
                <div class="mm-hint">Choosing a template replaces the Subject and Body below</div>
            </div>
        </div>

        {{-- Subject --}}
        <div class="mm-row">
            <div class="mm-label-col">
                <span class="mm-label-icon mm-ic-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </span>
                Subject <span class="mm-req">*</span>
            </div>
            <div class="mm-field-col">
                <input class="mm-input" name="subject" id="subject_mass_mail"
                    placeholder="Enter email subject" value="">
            </div>
        </div>

        {{-- Email Body --}}
        <div class="mm-row mm-row-body">
            <div class="mm-label-col">
                <span class="mm-label-icon mm-ic-teal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </span>
                Email Body
            </div>
            <div class="mm-field-col">
                <div class="mm-editor-wrap">
                    <textarea id="editor1mass" name="editor1mass" rows="10">{{ @$template_body }}</textarea>
                </div>
            </div>
        </div>

        {{-- Hidden fields --}}
        <input type="hidden" value="0" name="checkForSave" id="checkForSave">

        {{-- Attach CV (hidden, keep for functionality) --}}
        <div class="d-none">
            <input name="attached_cv" id="attached_cv" type="checkbox" value="1" checked="checked"/>
        </div>

    </div>{{-- end mm-form-body --}}

    {{-- Footer --}}
    <div class="mm-footer">
        <div class="mm-footer-left">
            <button type="button" class="mm-btn-cancel" data-dismiss="modal"
                onclick="var d=document.getElementById('kt_mass_mail_drawer'); if(d&&typeof KTDrawer!=='undefined'){var dr=KTDrawer.getInstance(d);if(dr)dr.hide();}">
                Cancel
            </button>
        </div>
        <div class="mm-footer-right">
            <button type="button" data-id="0" class="mm-btn-send massMailBtn"
                name="btnSubmitMassMail" id="btnSubmitMassMail" value="btnSubmitMassMail">
                <span class="indicator-label">Send</span>
                <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
            <button type="button" data-id="1" class="mm-btn-send massMailBtn d-none"
                name="btnSubmitMassMailSave" id="btnSubmitMassMailSave" value="btnSubmitMassMailSave">
                <span class="indicator-label">Save &amp; Send</span>
                <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
            <button type="button" id="deleteTemplateBtn" class="mm-btn-delete">
                Delete Template
            </button>
        </div>
    </div>

</form>
</div>

<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script>

$('#ck-editor__editable_inline').html("testing");


$('#templatemassmail').change(function(){


		var template	=	$('#templatemassmail').val();
		if(template > 0)
		{
						var id        	=	$('#templatemassmail').val();
						if (id == 41) {
							$('#btnSubmitMassMailSave').removeClass('d-none'); // ✅ show
							$('#checkForSave').val(1);
						} else {
							$('#btnSubmitMassMailSave').addClass('d-none'); // ❌ hide
							$('#checkForSave').val(0);
						}
						var url       	=	"{{ route('service_company.cvtracker.email_template',':id') }}";
						url         	=	url.replace(':id', id);
						var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}";
						var token       =	"{{ csrf_token() }}";
						var to_email = $('#to_email_mass_mail').val();
						var mailtype = 2;

						var selectedApplicantListing	=	[];
						var jqueryarray 				=	<?php echo json_encode($applicationData); ?>;
						jqueryarray.map((mval, mid, marr)=>{
							selectedApplicantListing.push( mval.id);
						});

						$.ajax({
										type		: 'POST',
										url			: url,
										data		: {'_token': token, '_method': 'POST', 'template' : template, 'selectedApplicantListing': selectedApplicantListing,'to_email':to_email,'mailtype':mailtype},
										beforeSend	: function() {
																	//$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
										},
										success 	: function (response) {


														if (response.status == "success") {


																	CKEDITOR.instances.editor1mass.setData(response.template_description);
																	setTimeout(() => {
																		$('#editor1mass').val(response.template_description);
																		$('#subject_mass_mail').val(response.subject);
																	}, 500);

														}
											}
               			});

		}
		else
		{
			$('#editor').html('Choose email template');
		}



});

$(document).on('click', '#deleteTemplateBtn', function () {

    let templateId = $('#templatemassmail').val();
	var token       =	"{{ csrf_token() }}";

    if (!templateId) {
        Swal.fire("Warning", "Please select a template first", "warning");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You can delete only custom template!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: "{{ route('delete.template') }}",
                type: "POST",
                data: {
                    id: templateId,
                    _token: token
                },

                beforeSend: function () {
                    $('#deleteTemplateBtn').prop('disabled', true);
                },

                success: function (response) {

                    if (response.status) {

                        Swal.fire("Success", response.message, "success");

                        // ✅ Remove deleted option
                        $('#templatemassmail option:selected').remove();

                        // ✅ Reset dropdown
                        $('#templatemassmail').val('').trigger('change');

                    } else {
                        Swal.fire("Warning", response.message, "warning");
                    }
                },

                error: function (xhr) {

                    let res = xhr.responseJSON;

                    Swal.fire(
                        "Warning",
                        res?.message || "You are not allowed to delete this template",
                        "warning"
                    );
                },

                complete: function () {
                    $('#deleteTemplateBtn').prop('disabled', false);
                }
            });

        }
    });
});



$(document).on('click', '.massMailBtn', function () {


         let save_id = $(this).data('id');
		const swalWithBootstrapButtons = Swal.mixin({
			  customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
		  	 buttonsStyling: false
		})


									var id        	=	1;
									var url       	=	"{{ route('service_company.cvtracker.mass_mail_send',':id') }}";
									url         	=	url.replace(':id', id);
									var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}";
									var token       =	"{{ csrf_token() }}";

									var arr = [];
									$('.chk_list_item:checked').each(function () {
											arr.push($(this).val());
									});

									var template	=	$('#templatemassmail').val();
									var from_email	=	$('#from_email_mass_mail').val();
									var to_email	=	$('#to_email_mass_mail').val();
									var checkForSave	=	$('#checkForSave').val();
									var cc_email	=	'';
									var subject		=	$('#subject_mass_mail').val();
									var attached_cv	=	0;
									var editor1mass		= CKEDITOR.instances.editor1mass.getData(); //$('#editor1mass').val();

									// alert(editor1mass);

									// $('#attached_cv:checkbox:checked').each(function(){
									// 	attached_cv	=	1;
									// });



									$.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST','checkForSave':checkForSave,'save_id':save_id, 'template' : template, 'from_email' : from_email, 'to_email' : to_email, 'cc_email' : cc_email, 'subject' : subject, 'attached_cv': attached_cv, 'editor1':editor1mass, 'arr':arr},
													// beforeSend	: function() {
													// 							$('#main_container').html('<img src="'+baseurl+'" height="35px" width="100%">');
													// },
													success 	: function (response) {

														if (response.status == 'coinCheck') {

															Swal.fire("Failed",` ${response.message}` , "warning").then(function(){
																$(".btn-active-light-danger").click();
																});

																return false;
														}

																	if (response.status == "success") {

																		//   $('#main_container').html(response.view);
																		$(".btn-active-light-danger").click();
																		$('input[type="checkbox"]').prop('checked', false);
																		  swalWithBootstrapButtons.fire(
																										  'Success!',
																										  'Selected Applicant Forworded.',
																										  'success'
																										)

																										// location.reload();

																	}
														}
										});






});






</script>
<script>

	// $.noConflict();

	var from_email	=	document.querySelector('#from_email_mass_mail');
	new Tagify(from_email);

	var to_email	=	document.querySelector('#to_email_mass_mail');
	new Tagify(to_email);

	// var cc_email	=	document.querySelector('#cc_email');
	// new Tagify(cc_email);

</script>
