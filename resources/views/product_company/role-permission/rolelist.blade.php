<form id="kt_modal_add_role_form" name="kt_modal_add_role_form" class="form" action="#" method="post">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <!--begin::Scroll-->
  <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_role_header" data-kt-scroll-wrappers="#kt_modal_add_role_scroll" data-kt-scroll-offset="300px">
    <!--begin::Input group-->
    <div class="fv-row mb-10">
      <label class="fs-5 fw-bolder form-label mb-2"> <span class="required">Role name</span> </label>
      <input class="form-control form-control-solid" placeholder="Enter a role name" name="role_name" id="role_name" value="" />
    </div>
    <!--end::Input group-->
    <!--begin::Permissions-->
    <div class="fv-row">
      <label class="fs-5 fw-bolder form-label mb-2">Role Permissions</label>
      <!--begin::Table-->
      <div class="table-responsive">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5">
          <!--begin::Table body-->
          <tbody class="text-gray-600 fw-bold" id="kt_roles_edit">
            <!--begin::Table Administrator Access row-->
            <tr>
              <td class="text-gray-800">Administrator Access <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Allows a full access to the system"></i></td>
              <td><!--begin::Checkbox-->
                <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
                <input class="form-check-input" type="checkbox" checked="checked" data-kt-check="true" data-kt-check-target="#kt_roles_edit .form-check-input-edit" value="1">
                <span class="form-check-label" for="kt_roles_select_all1">Select all</span> </label>
                <!--end::Checkbox-->
              </td>
            </tr>
            <?php 

	if(!empty($permissions_master))
	{
		foreach($permissions_master as $master_val)
		{
			$html	=	'';
			$html	.=	'<tr>';
			$html	.=	'<td class="text-gray-800">'.ucwords($master_val->master_display_name).'</td>';
			
						 $permissions 			= DB::table('permissions')
													->select('permissions.id', 'permissions.display_name', 'permissions.module_id', 'permissions.status')
													->where('permissions.module_id', $master_val->master_module_id)
													->orderBy('permissions.priority', 'ASC')
													->get();
						$html	.=	'<td><div class="d-flex">';	 
						if(!empty($permissions))
						{
							foreach($permissions as $permissions_val)
							{
								$checked	=	'checked="checked"';
								$disabled	=	'';
								if($permissions_val->status == 0){
									$disabled	=	'disabled';
									$checked	=	'';
								}
$html	.='<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">';
$html	.= '<input class="form-check-input form-check-input-edit" type="checkbox"  '.@$checked.'  value="'.$permissions_val->id.'" '.@$disabled.' name="role[]" />';
$html	.= '<span class="form-check-label">'.ucwords($permissions_val->display_name).'</span> </label>';
							}
						}
							
						$html	.=	'</div></td>';							
			
			$html	.=	'</tr>';
	
			echo $html;
		}
	}		
			
?>


<tr>
	<td class="text-gray-800">&nbsp;</td><td>
		<div class="d-flex">
			<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
				<span class="form-check-label"><h4>Additional Permission</h4></span>                
			</label>
		</div>
	</td>  
</tr>

<tr>
	<td class="text-gray-800">Requisition</td><td>
		<div class="d-flex">
			<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
				<input class="form-check-input form-check-input-edit" type="radio" checked="checked" value="1"  name="all_requisition"> 
				<span class="form-check-label">All Requisiton</span> 
			</label>
			<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
			<input class="form-check-input form-check-input-edit" type="radio"  value="0" name="all_requisition">
			<span class="form-check-label">Only Assign Requisition</span> 
			</label>
		</div>
	</td>
</tr>

<tr>

<td class="text-gray-800">&nbsp;</td>
	<td>
		<div class="d-flex">
<?php 
		$html 		=	'';
		$customArr 	=	array('Add' => 14, 'View' => 15, 'Edit' => 16, 'Delete' => 17);
		foreach($customArr as $key=>$val ){ 
			$html .= '<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
						<input class="form-check-input form-check-input-edit" type="checkbox" checked="checked" value="'.$val.'" name="role[]">
						<span class="form-check-label">'.$key.'</span> 
					</label>';
		} 
		echo $html; 		
?>		
		</div>
	</td>
</tr>
  
<tr>
	<td class="text-gray-800">Add Team Member On Requisition </td><td>
		<div class="d-flex">
			<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
				<input class="form-check-input form-check-input-edit" type="radio" name="add_team_on_requisition" checked="checked" value="1"  > 
				<span class="form-check-label">Yes</span> 
			</label>
			<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
				<input class="form-check-input form-check-input-edit" type="radio" name="add_team_on_requisition"  value="0" >
				<span class="form-check-label">No</span> 
			</label>
		</div>
	</td>
</tr> 


          </tbody>
          <!--end::Table body-->
        </table>
        <!--end::Table-->
      </div>
      <!--end::Table-->
    </div>
    <!--end::Permissions-->
  </div>
  <!--end::Scroll-->
  <!--begin::Actions-->
  <div class="text-center pt-15">
    <!-- data-kt-roles-modal-action="cancel" -->
    <button type="reset" class="btn btn-light me-3" data-dismiss="modal">Discard</button>
    <button type="button" id="submit-frm-btn" class="btn btn-primary" data-kt-roles-modal-action="submit"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
  </div>
  <!--end::Actions-->
</form>
<br />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<script>
$('#submit-frm-btn').click(function(){

		var role_name	=	$('#role_name').val();
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
									
				//Swal.fire("Warning!", "Rolename can not be null !", "warning");
				
		}else{
					$.ajax({
								url				: '{{route('product_company.settings.saveRole')}}',
								container		: '#kt_modal_add_role_form',
								type			: "POST",
								data			: $('#kt_modal_add_role_form').serialize(),
								success: function (response) {
																if(response.status == 'success'){
																		$('#role_name').val('');
																		swal({
																				title	: "Success!",
																				text	: "Roles & Permissions added successfully	!",
																				type	: "success",
																				timer	: 1000,
																				showConfirmButton: false
																		}, function(){
																						location.reload();
																		});

																	
																		//Swal.fire("Success", 'Application stage update successfully !', "success");	
																																			
								   }
								   								
																if(response.status == 'warning'){
																	
																		$('#role_name').val('');
																		swal({
																				title	: "Warning!",
																				text	: response.message,
																				type	: "warning",
																				timer	: 1000,
																				showConfirmButton: true
																		}, function(){
																						//location.reload();
																		});

																	
																		//Swal.fire("Success", 'Application stage update successfully !', "success");	
																																			
								   
								   								 }
								   								
								}
							})	
				
		}		
				
				
});

</script>

