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

<div class="main_container" id="main_container" >
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

<form id="form1">
  <!--begin::Modal body-->
  <div class="modal-body scroll-y py-0 px-lg-7">
    <div data-kt-element="options">
      <div class="py-5">
        <!--begin::Template-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label>
          <div class="col-lg-10 col-xl-10">
            <select name="templatemassmail" id="templatemassmail" class="form-select form-select-solid" aria-label="Select template">
              <option value="">Select Email Template...</option>
					@if(!empty($templateData)){ 
						@foreach($templateData as $tdata )
							<option value="{{ $tdata->e_temp_id }}">{{ str_replace(["[{COMPANY_NAME}]"],[$company_name],  $tdata->display_name) }}</option>
						@endforeach
					@endif				
			  
            </select>
            <span class="form-text text-muted">Please select email template</span> </div>
        </div>
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="from_email_mass_mail" id="from_email_mass_mail"  value="{{ $userdata->email }}" readonly="" data-role="from_email" >
            <span class="form-text text-muted">Please enter email</span> </div>
        </div>
        <!--end::From email-->
        <!--begin::To email-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="to_email_mass_mail" id="to_email_mass_mail"  value="{{ @$cc_email }}" >
            <span class="form-text text-muted">Please enter email</span> </div>
        </div>
        <!--end::To email-->
        
        <!--begin::Subject-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="subject" id="subject_mass_mail" placeholder="Enter subject" value="" >
            <span class="form-text text-muted">Please enter subject</span> </div>
        </div>
        <!--end::Subject-->
        <!--begin::Body-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
          <div class="col-lg-10 col-xl-10">
            <textarea  id="editor1mass" name="editor1mass" row="10" >
				{{ @$template_body }}
			</textarea>
            <span class="form-text text-muted"> Please enter email template body content </span> </div>
        </div>
        <!--begin::Files-->
        <div class="form-group row mb-4 d-none">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Attach CV</label>
          <div class="col-xl-10 col-lg-10">
            <!--begin::Dropzone-->
            <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
              <!--begin::Controls-->
              <div class="dropzone-panel mb-lg-0 mb-2">
                <input class="" name="attached_cv" id="attached_cv" type="checkbox" value="1" checked="checked" />
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
      <button type="close" id="" class="btn btn-light-dark me-3" data-dismiss="modal">Cancel</button>
      <!--begin::Send-->
      <button type="button" class="btn btn-light-primary" name="btnSubmitMassMail" id="btnSubmitMassMail" value="btnSubmitMassMail"> <span class="indicator-label">Send</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
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
<script>

$('#ck-editor__editable_inline').html("testing");


$('#templatemassmail').change(function(){

	
		var template	=	$('#templatemassmail').val();
		if(template > 0)
		{
						var id        	=	$('#templatemassmail').val();
						var url       	=	"{{ route('superadmin.company.email_template',':id') }}";
						url         	=	url.replace(':id', id);
						var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
						var token       =	"{{ csrf_token() }}";
						var to_email = $('#to_email_mass_mail').val();
						var mailtype = 2;
						
						// var selectedApplicantListing	=	[];
						var selectedApplicantListing = $(".chk_list_item:checked").map(function() {
                                                    return this.value;
                                                    }).get();
						// jqueryarray.map((mval, mid, marr)=>{
						// 	selectedApplicantListing.push( mval.id);	
						// });
	   
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


document.getElementById("btnSubmitMassMail").addEventListener("click", ()=>{
	
		

		const swalWithBootstrapButtons = Swal.mixin({
			  customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
		  	 buttonsStyling: false
		})
		swalWithBootstrapButtons.fire({
										  title				: 'Are you sure?',
										  text				: "You won't be able to revert this!",
										  icon				: 'warning',
										  showCancelButton	: true,
										  confirmButtonText	: 'Yes,  Do reject Mail!',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true 
									}).then((result) => {
		  if (result.isConfirmed) {
		  
									var id        	=	1;
									var url       	=	"{{ route('superadmin.company.reject_mail_send',':id') }}";
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
													data		: {'_token': token, '_method': 'POST', 'template' : template, 'from_email' : from_email, 'to_email' : to_email, 'cc_email' : cc_email, 'subject' : subject, 'attached_cv': attached_cv, 'editor1':editor1mass, 'arr':arr},
													beforeSend	: function() {
																				$('#main_container').html('<img src="'+baseurl+'" height="35px" width="100%">');
													},
													success 	: function (response) {
																	if (response.status == "success") {
																		  
																		//   $('#main_container').html(response.view);
																		  swalWithBootstrapButtons.fire(
																										  'Success!',
																										  'Selected Applicant Rejected.',
																										  'success'
																										)

																										location.reload();										
																		  
																	}
														}
										});						 
						 
						 
						 
			
		  } 
		  else if (result.dismiss === Swal.DismissReason.cancel) {
						swalWithBootstrapButtons.fire(
														 	'Cancelled',
															'Selected request is safe :)',
															'error'
														)
		  }
		})		
		
		

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

