<?php 
		$default_image	= url('public/user-uploads/team_profile_img/avatar.png');				
?>

<div class="d-flex flex-column scroll-y mx-n7 px-7" id="add_team_member_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
  <div class="fv-row mb-7">
    <label class="d-block fw-bold fs-6 mb-5">Upload Image</label>
    <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url(<?php echo @$default_image; ?>)">
      <div class="image-input-wrapper w-125px h-125px" style="background-image: url(<?php echo @$default_image; ?>);"></div>
      <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar"> <i class="bi bi-pencil-fill fs-7"></i>
      <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
      <input type="hidden" name="avatar_remove" />
      </label>
      <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar"> <i class="bi bi-x fs-2"></i> </span> <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar"> <i class="bi bi-x fs-2"></i> </span> </div>
    <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
  </div>
  <div class="fv-row mb-7">
    <label class="required fw-bold fs-6 mb-2">Full Name</label>
    <input type="text" name="invitation_fullname" id="invitation_fullname" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Full name" value="" />
  </div>
  <div class="fv-row mb-7">
    <label class="required fw-bold fs-6 mb-2">Email</label>
    <input type="email" name="invitation_email" id="invitation_email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="example@domain.com" value="" />
  </div>
  <div class="mb-7">
    <label class="required fw-bold fs-6 mb-5">Role</label>
    <?php 
		if(!empty($roles)){
			foreach($roles as $rval){
?>
    <div class="d-flex fv-row">
      <div class="form-check form-check-custom form-check-solid">
        <input class="form-check-input me-3" name="invitation_roles" type="radio" value="<?php echo @$rval->id; ?>" id="<?php echo @$rval->id; ?>" />
        <label class="form-check-label" for="kt_modal_update_role_option_0">
        <div class="fw-bolder text-gray-800"><?php echo @$rval->display_name; ?></div>
        <div class="text-gray-600"><?php echo @$rval->description; ?></div>
        </label>
      </div>
    </div>
    <div class='separator separator-dashed my-5'></div>
    <?php 
			}
		}	
?>
  </div>
</div>
<div class="text-center pt-15">
  <button type="reset" class="btn btn-light me-3" data-dismiss="modal">Cancel</button>
  <button type="button" class="btn btn-primary" data-kt-users-modal-action="submit" id="invitation_submit"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
</div>
<script>
   var loadFile = function(event) {
   	var output = URL.createObjectURL(event.target.files[0]);
   	$('.image-input-wrapper').css('background-image', 'url(' + output + ')');
   };
   
</script>
