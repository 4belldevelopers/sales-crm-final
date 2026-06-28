
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @csrf   
  <input type="hidden" name="SER_NAME" id="SER_NAME" value="GOOGLE" /> 
  <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
    <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required1">VERIFY APP</span> </label>
    <?php  	
						if($company->google_verify == 1)
						{
							echo '<a  class="form-control" href="javascript:void(0);" style="color:#009900; font-weight:bold">&nbsp;Verify</a>';
						}
						else if(!empty($company->google_client_id) && !empty($company->google_client_secret) && $company->google_verify == 0)
						{
						 	$CLIENT_REDIRECT_URL		=	route('admin.profile.verify-google-redirect').'/';  							
							$login_url 					= 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/calendar') . '&redirect_uri=' . urlencode($CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . @$company->google_client_id . '&access_type=offline&prompt=consent';
	?>
    <a id="logo" class="form-control" href="<?php echo $login_url ?>" style="color:#0000FF; font-weight:bold">Login with Google</a>
    <?php 
			   			}
						else 
						{
							echo '<a  class="form-control" href="javascript:void(0);" style="color:#990000; font-weight:bold">&nbsp;None</a>';
						}
	?> 
    <div class="fv-plugins-message-container invalid-feedback"> </div>
  </div>
  <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
    <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">CLIENT ID </span> </label>
    <input type="text" class="form-control form-control-solid" id="google_client_id" name="google_client_id" value="{{ $company->google_client_id }}" placeholder="GOOGLE API KEY">
    <div class="fv-plugins-message-container invalid-feedback"> </div>
  </div>
  <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
    <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">CLIENT SECRET </span> </label>
    <input type="text" class="form-control" id="google_client_secret" name="google_client_secret" value="{{ $company->google_client_secret }}" placeholder="GOOGLE API SECRET">
    <div class="fv-plugins-message-container invalid-feedback"> </div>
  </div>
  <div class="text-center">
    <button type="button" class="btn btn-secondary me-3" data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-primary submit_google">Submit</button>
  </div>


<script>

$(document).ready(function(){

	$('body').on('click', '.submit_google', function (){
	
			var data 			=	new FormData();
			var form_data 		=	$('#google_frm').serializeArray();
			$.each(form_data, function (key, input) {
				data.append(input.name, input.value);
			});			
				$.ajax({
						
							url					: '{{route('admin.account.videoMeetingSeetingUpdate')}}',
							headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
							method				: "POST",
							processData			: false,
							contentType			: false,
							data				: data,
							//redirect			: true,
							success				: function (data) {
													//alert(data); 
													if(data.status == 'success'){ 
															 $("#google_success_alert").fadeTo(2000, 500).slideUp(500, function() {
																																$("#google_success_alert").slideUp(500);
																															});	
													}
													
							}
   					});
			
			//alert("test");
	});
	
});

	
</script>
