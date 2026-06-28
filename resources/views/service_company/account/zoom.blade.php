<meta name="csrf-token" content="{{ csrf_token() }}" />
@csrf
<input type="hidden" name="SER_NAME" id="SER_NAME" value="ZOOM" />
<div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
  <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required1">EMAIL</span> </label>
  <input type="text" class="form-control form-control-solid" id="zoom_email" name="zoom_email" value="{{ $company->zoom_email }}" placeholder="ZOOM EMAIL">
  <div class="fv-plugins-message-container invalid-feedback"> </div>
</div>
<div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
  <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">API KEY </span> </label>
  <input type="text" class="form-control" id="zoom_api_key" name="zoom_api_key" value="{{ $company->zoom_api_key }}" placeholder="ZOOM API KEY">
  <div class="fv-plugins-message-container invalid-feedback"> </div>
</div>
<div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
  <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">API SECRET </span> </label>
  <input type="text" class="form-control" id="zoom_api_secret" name="zoom_api_secret" value="{{ $company->zoom_api_secret }}" placeholder="ZOOM API SECRET">
  <div class="fv-plugins-message-container invalid-feedback"> </div>
</div>
<div class="text-center">
  <button type="button" class="btn btn-secondary me-3" data-bs-dismiss="modal">Cancel</button>
  <button type="button" class="btn btn-primary submit_zoom">Submit</button>
</div>
<script>

$(document).ready(function(){

	$('body').on('click', '.submit_zoom', function (){
				var data 			=	new FormData();
				var form_data 		=	$('#zoom_configfrm').serializeArray();
				$.each(form_data, function (key, input) {
					data.append(input.name, input.value);
				});			
				$.ajax({	
							url					: '{{route('service_company.account.videoMeetingSeetingUpdate')}}',
							headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
							method				: "POST",
							processData			: false,
							contentType			: false,
							data				: data,
							success				: function (data) {
													if(data.status == 'success'){ 
															 						$("#zoom_success_alert").fadeTo(2000, 500).slideUp(500, function() { $("#zoom_success_alert").slideUp(500); });	
													}													
							}
   					});
	});
	
});

	
</script>
