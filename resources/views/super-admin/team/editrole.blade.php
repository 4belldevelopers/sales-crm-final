
<form id="kt_modal_add_role_form" name="kt_modal_add_role_form" class="form" action="#" method="post">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="cuser_id" id="cuser_id" value="<?php echo @$id; ?>" />
  <div class="d-flex flex-column scroll-y me-n7 pe-7" id="update_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" style="max-height: 350px;">
    <div class="mb-4">
      <label class="fw-bold fs-6 mb-5">Role</label>
      <?php 
	if(!empty($roleslist))
	{
		foreach($roleslist as $rlist)
		{
			$sel	=	'';
			if( $selrole->role_id == $rlist->id )
			{
				$sel	=	'checked="checked"';	
			} 
				
?>
      <div class="d-flex fv-row">
        <div class="form-check form-check-custom form-check-solid">
          <input class="form-check-input me-3" name="user_role" type="radio" value="<?php echo $rlist->id; ?>" id="<?php echo $rlist->id; ?>" <?php echo @$sel; ?> >
          <label class="form-check-label" for="one">
          <div class="fw-bolder text-gray-800"><?php echo ucwords($rlist->display_name); ?></div>
          <div class="text-gray-600"><?php echo ucwords($rlist->description); ?></div>
          </label>
        </div>
      </div>
      <div class="separator separator-dashed my-5"></div>
      <?php 
		}
	}	
?>
    </div>
  </div>
  <!--begin::Actions-->
  <div class="text-center pt-15">
    <button type="reset" class="btn btn-light me-3" data-dismiss="modal">Reset</button>
    <button type="button" class="btn btn-primary"> <span class="indicator-label" id="submit-frm-btn">Update</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
  </div>
  <!--end::Actions-->
  <div></div>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<script>
$('#submit-frm-btn').click(function(){
	
		var role_name	=	$('input[name="user_role"]:checked').val();
		//alert(role_name); return false; 
		if(role_name == ''){
				
				
									swal({
											title	: "Warning!",
											text	: "Rolename can not be null !",
											type	: "warning",
											timer	: 1000,
											showConfirmButton: true
									}, function(){
													//location.reload();
									});
																									
				
		}else{
		
					$.ajax({
								url				: '{{route('product_company.team.roleupdate')}}',
								container		: '#kt_modal_add_role_form',
								type			: "POST",
								data			: $('#kt_modal_add_role_form').serialize(),
								success: function (response) {
																if(response.status == 'success'){
																									//$('#role_name').val('');
																									swal({
																											title	: "Success!",
																											text	: "Update Roles & Permissions successfully	!",
																											type	: "success",
																											timer	: 1000,
																											showConfirmButton: true
																									}, function(){
																										table._fnDraw();
																										$(".update_role_close").click();
																													//tableLoad('load');
																													//location.reload();
																									});
								   								}
																
																if(response.status == 'warning'){
																									//$('#role_name').val('');
																									swal({
																											title	: "Warning!",
																											text	: response.message,
																											type	: "warning",
																											timer	: 1000,
																											showConfirmButton: true
																									}, function(){
																													//location.reload();
																									});
								   								}
																
																
																
																
																
								}
							})	
		}						
				
				

});

</script>
