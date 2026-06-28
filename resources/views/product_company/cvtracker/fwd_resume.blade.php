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
	
								var editorElement = CKEDITOR.document.getById( 'editor1' );
						
								if ( isBBCodeBuiltIn ) {
									editorElement.setHtml(
										'Hello world!\n\n' +
										'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
									);
								}
						
								if ( wysiwygareaAvailable ) {
									CKEDITOR.replace( 'editor1' );
								} else {
									editorElement.setAttribute( 'contenteditable', 'true' );   
									CKEDITOR.inline( 'editor1' );
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


<div class="main_container" id="main_container">
@php  
$template_body 	=	'';
if($applicationDataCount > 0)
{

			$template_body				=	'<table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer dtr-inline" id="myTable" role="grid" aria-describedby="myTable_info" style="width: 1272px;">
            <thead>
              <tr class="fw-bolder text-muted" role="row">
					<th class="sorting_disabled align-items-center" rowspan="1" colspan="1" style="width: 151.5px;">S.No.</th>
					<th class="sorting_disabled align-items-center" rowspan="1" colspan="1" style="width: 151.5px;">Name</th>
					<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 112.5px;">Contacts</th>
					<th class="sorting_disabled"  rowspan="1" colspan="1" style="width: 40.5px;">Exp.</th>
					<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 58.5px;">Salary</th>
					<th class="sorting_disabled" data-toggle="tooltip" title="Notice Period" rowspan="1" colspan="1" style="width: 62.5px;">NP</th>
					<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 109.5px;">Qualifaction</th>
					<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 108.5px;">Requisitions</th>
            </thead>';
				
				$cnt	=	1;
				foreach($applicationData as $appData)
				{
					
						$bgTextDecore 	=	$strVal		=	$current_ctc_cur=	$current_ectc_cur	=	$ctc 	=	$ectc 	=	"";
						$str 			=	trim(@$appData->full_name);
						$str 			=	$strVal		=	strtolower($str[0]);
						$str 			=	ord($str);
						
						if ($str >= 97 && $str <= 101) {
							$bgTextDecore = 'fs-3 bg-light-danger text-danger';
						} else if ($str >= 102 && $str <= 106) {
							$bgTextDecore = 'fs-3 bg-light-warning text-warning';
						} else if ($str >= 107 && $str <= 111) {
							$bgTextDecore = 'fs-3 bg-light-primary text-primary';
						} else if ($str >= 112 && $str <= 116) {
							$bgTextDecore = 'fs-3 bg-light-success text-success';
						} else if ($str >= 117 && $str <= 121) {
							$bgTextDecore = 'fs-3 bg-dark text-secondary';
						} else {
							$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
						}
						
						$current_ctc_cur		=	$currencySymbole[@$appData->current_ctc_cur];
						$current_ectc_cur		=	$currencySymbole[@$appData->current_ectc_cur];
						$ctc 					=	GeneralMethod::thousandsCurrencyFormat($appData->ctc); 
						$ectc 					=	GeneralMethod::thousandsCurrencyFormat($appData->ectc); 
						$np 					= 	$appData->np_id;
						if (empty($np)) {
							$np = 'N/A';
						}
						if ($appData->serving_notice_period_mode == 0) {
							if (!empty($appData->np_id)) {
								$np = $appData->np_id . '  Days';
							}
						} else {
							$np = $appData->np_id;
						}
										
								
						$template_body			.= '<tr role="row" class="odd">
			<td tabindex="0">
					<div class="form-check form-check-sm form-check-custom form-check-solid">'.$cnt.'.</div>
			</td>
			
			<td class=" align-items-center">
					<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> 
						<a href="javascript:void(0);" data-row-id="1418" class="show-application-detail">
							<div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">'.strtoupper(@$strVal).'</div>
						</a> 
					</div>
					<div class="d-flex flex-column"> 
					<a href="javascript:void(0);" data-row-id="1418" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(@$appData->full_name).'</a><br>  
					<a href="javascript:void(0);" data-row-id="1418" class="text-muted fw-bold d-block fs-7 text-hover-primary" >'.ucwords(@$appData->current_designation).'</a> </div>
			</td>
			
			<td>
					<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$appData->phone.'</a><br>
						<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.@$appData->email.'</a> 
					</div>
			</td>
			
			<td>
					<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$appData->total_exp.'+</a><br>  
						<span class="text-muted fw-bold d-block fs-7">'.@$appData->rel_exp.'+</span> 
					</div>
			</td>
			
			<td>
					<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6"><i class="fa fa-inr" aria-hidden="true"></i>'.@$current_ctc_cur.@$ctc.'</a><br> 
						<span class="text-muted fw-bold d-block fs-7"><i class="fa fa-inr" aria-hidden="true"></i>'.$current_ectc_cur.@$ectc.'</span> 
					</div>
			</td>
			
			<td>
					<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$np.'</a><br> 
						<span class="text-muted fw-bold d-block fs-7">'.@$appData->current_company.'</span>  
					</div>
			</td>
			
			<td>
					<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6" >'.ucwords(substr(@$appData->qualification_name, 0, 10)).'</a><br> 
						<span class="text-muted fw-bold d-block fs-7">'.strtoupper(substr(@$appData->skills, 0, 15)).'</span> 
					</div>
				</td>
				
			<td>
					<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" >'.@$appData->job_title.'</a><br> 
						<span class="text-muted fw-bold text-muted d-block fs-7" >'.@$appData->client_name.'</span> 
					</div>
				</td>
					
		</tr>';
						
						
					$cnt	=	$cnt+1;		
				}
	
			$template_body				.= '<tbody class="text-gray-600 fw-bold">';
			$template_body				.= '</tbody></table>';
			
		
}else{
	$template_body = 'Choose email template';	
}



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
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label> 
          <div class="col-lg-10 col-xl-10">
            <select name="template" id="template" class="form-select form-select-solid" aria-label="Select template">
              <option value="">Select Email Template...</option>
				@if(!empty($templateData)) 
					@foreach($templateData as $tdata ){
							<option value="{{ $tdata->e_temp_id }}">{{ $tdata->display_name }}</option> 
					@endforeach
				@endif				
            </select>
            <span class="form-text text-muted">Please select email template</span> </div>
        </div>				 
		
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="from_email" id="from_email"  value="{{ $userdata->email }}" readonly="" data-role="from_email" >
            <span class="form-text text-muted">Please enter email</span> </div>
        </div>
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="to_email" id="to_email"  value="{{ @$to_email }}" >
            <span class="form-text text-muted">Please enter email</span> </div>
        </div>
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">CC</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="cc_email" id="cc_email"  value="{{ @$cc_email }}" >
            <span class="form-text text-muted">Please enter email</span> </div>
        </div>
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="subject" id="subject" placeholder="Enter subject" value="" >
            <span class="form-text text-muted">Please enter subject</span> </div>
        </div>
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
          <div class="col-lg-10 col-xl-10">
            <textarea  id="editor1" name="editor1" row="10" >
					{!! @$template_body !!}
			</textarea>
            <span class="form-text text-muted">
				Please enter email template body content
			</span> </div>
        </div>		
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Attach CV</label>
          <div class="col-xl-10 col-lg-10">
            <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
              <div class="dropzone-panel mb-lg-0 mb-2">
                <input class="" name="attached_cv" id="attached_cv" type="checkbox" value="1" checked="checked" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer flex-center">
    <div class="div1">
      <button type="close" id="" class="btn btn-light-dark me-3" data-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-light-primary" name="btnSubmit" id="btnSubmit" value="btnSubmit"> 
	  	<span class="indicator-label">Send</span> 
		<span class="indicator-progress">Please wait... 
			<span class="spinner-border spinner-border-sm align-middle ms-2"></span> 
		</span> 
	</button>
    </div>
    <div class="div2 position-absolute end-0 me-5 d-none"> </div>
  </div>
</div>
</form>


<script>

$('#ck-editor__editable_inline').html("testing");

$('#template').change(function(){

		var template	=	$('#template').val();
		if(template > 0){
							var id        	=	1;
							var url       	=	"{{ route('product_company.cvtracker.email_template',':id') }}";
							url         	=	url.replace(':id', id);
							var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
							var token       =	"{{ csrf_token() }}";
							
							var selectedApplicantListing	=	[];
							var jqueryarray 				=	{!! json_encode($applicationData) !!};
							jqueryarray.map((mval, mid, marr)=>{
								selectedApplicantListing.push( mval.id);	
							});
							$.ajax({
											type		: 'POST',
											url			: url,
											data		: {'_token': token, '_method': 'POST', 'template' : template, 'selectedApplicantListing': selectedApplicantListing},
											beforeSend	: function() {
																		//$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
																	},
											success 	: function (response) {
																					if (response.status == "success") {
																								CKEDITOR.instances.editor1.setData(response.template_description); 
																					}
												}
								}); 				
		}else{
			$('#editor').html('Choose email template');	
		} 		 

});


document.getElementById("btnSubmit").addEventListener("click", ()=>{
	
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
										  confirmButtonText	: 'Yes,  Forward Resume!',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true 
									}).then((result) => {
		  if (result.isConfirmed) {
		  
									var id        	=	1;
									var url       	=	"{{ route('product_company.cvtracker.fwd_resume_send',':id') }}";
									url         	=	url.replace(':id', id);
									var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
									var token       =	"{{ csrf_token() }}";
									
									var arr = [];
									$('.chk_list_item:checked').each(function () {
											arr.push($(this).val());
									});
									
									var template	=	$('#template').val();
									var from_email	=	$('#from_email').val();
									var to_email	=	$('#to_email').val();
									var cc_email	=	$('#cc_email').val();
									var subject		=	$('#subject').val();
									var attached_cv	=	0;
									var editor1		=	$('#editor1').val();
									
									$('#attached_cv:checkbox:checked').each(function(){
										attached_cv	=	1;
									});
						
									$.ajax({
													type		:	'POST',
													url			:	url,
													data		:	{'_token': token, '_method': 'POST', 'template' : template, 'from_email' : from_email, 'to_email' : to_email, 'cc_email' : cc_email, 'subject' : subject, 'attached_cv': attached_cv, 'editor1':editor1, 'arr':arr},
													beforeSend	:	function() {
																				$('#main_container').html('<img src="'+baseurl+'" height="35px" width="100%">');
																	},
													success 	:	function (response) {
																	if (response.status == "success") {
																		  
																		  $('#main_container').html(response.view);
																		  swalWithBootstrapButtons.fire('Success!', 'Selected Applicant Forworded.', 'success')
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
	
	$.noConflict();
	
	var from_email	=	document.querySelector('#from_email');
	new Tagify(from_email);
	
	var to_email	=	document.querySelector('#to_email');
	new Tagify(to_email);
    
	var cc_email	=	document.querySelector('#cc_email');
	new Tagify(cc_email);
		 
</script>




<script>
$(document).ready(function () {

									$('#form1').validate({ 
																	rules: {
																					to_email: {
																									required	: true,
																									email		: true
																							  },
																					subject: {
																									required	: true
																							  }		  
																							  
																			},
																	messages:{
																					to_email: {
																									required: "Please enter email address.",
																									email: true
																							  },
																					subject: {
																									required: "Please enter email subject."
																							  }		  
																	
																			  }		
														});

});
 	
</script>
