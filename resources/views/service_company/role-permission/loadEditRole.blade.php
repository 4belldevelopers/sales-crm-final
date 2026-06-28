@php 
   use App\Helper\GeneralMethod;
   
@endphp

<form id="kt_modal_add_role_form" name="kt_modal_add_role_form" class="form" action="#" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="role_id" id="role_id" value="<?php echo @$id; ?>" />
    <!--begin::Scroll-->
    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_role_scroll" data-kt-scroll="true"
        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
        data-kt-scroll-dependencies="#kt_modal_add_role_header" data-kt-scroll-wrappers="#kt_modal_add_role_scroll"
        data-kt-scroll-offset="300px">
        <!--begin::Input group-->
        <div class="fv-row mb-10">
            <label class="fs-5 fw-bolder form-label mb-2"> <span class="required">Role name</span> </label>
            @if ($role_row->display_name === 'Sub Administrator')
                <input class="form-control form-control-solid" placeholder="Enter a role name"
                    value="<?php echo ucwords(@$role_row->display_name); ?>" readonly />
                <input type="hidden" placeholder="Enter a role name" name="role_name" id="role_name"
                    value="<?php echo ucwords(@$role_row->display_name); ?>" />
            @else
                <input class="form-control form-control-solid" placeholder="Enter a role name" name="role_name"
                    id="role_name" value="<?php echo ucwords(@$role_row->display_name); ?>" />
            @endif


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
                            <td class="text-gray-800">Administrator Access <i
                                    class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Allows a full access to the system"></i></td>
                            <td><!--begin::Checkbox-->
                                <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                                        data-kt-check-target="#kt_roles_edit .form-check-input-edit" value="1">
                                    <span class="form-check-label" for="kt_roles_select_all1">Select all</span> </label>
                                <!--end::Checkbox-->
                            </td>
                        </tr>
                        <?php

                        if(!empty($permissions))
                        {
                            // dd($roles);
                            foreach($permissions as $key=> $rlist)  
                                {
                                    
                                    if(!empty($rlist))
                                        {
                                             $roleTitle = GeneralMethod::permissionRole($key);
                                              
                                            
                                                $html	=	'';
                                                $html	.=	'<tr>';
                                                $html	.=	'<td class="text-gray-800">'.ucwords($roleTitle).'</td>';
                                                $html	.=	'<td><div class="d-flex">';	

                                            foreach($rlist as $key=> $pval)
                                            { 
                                                // dd( $pval);
                                                $pval = (object)$pval;

                                                 $checked	=	'';
                                                 $disabled	=	'';
                                                 if (in_array($pval->id, $permission_role)) {
                                                        $checked = 'checked="checked"';
                                                    }
                                                if($pval->status == 0){
                                                        $disabled	=	'disabled';
                                                        $checked	=	'';
                                                    }

                                                   
                                                    
                                                    $html	.='<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">';
                                                    $html	.= '<input class="form-check-input form-check-input-edit" type="checkbox"  '.@$checked.'  value="'.$pval->id.'" '.@$disabled.' name="role[]" />';
                                                    $html	.= '<span class="form-check-label">'.ucwords($pval->display_name).'</span> </label>';
                                                    

                                            }
                                            

                                                $html	.=	'</div></td>';
                                                $html	.=	'</tr>';
                                                echo $html;

                                        }
                                }
                        }

                        
                       
                        
                        ?>


                        <?php
                        
                        $all_requisition = 58;
                        $all_requisition_1 = '';
                        $all_requisition_0 = '';
                        if (in_array($all_requisition, $permission_role)) {
                            $all_requisition_1 = 'checked="checked"';
                        } else {
                            $all_requisition_0 = 'checked="checked"';
                        }
                        
                        $add_team_on_requisition = 59;
                        $add_team_on_requisition_1 = '';
                        $add_team_on_requisition_0 = '';
                        if (in_array($add_team_on_requisition, $permission_role)) {
                            $add_team_on_requisition_1 = 'checked="checked"';
                        } else {
                            $add_team_on_requisition_0 = 'checked="checked"';
                        }
                        
                        ?>
                        <tr>
                            <td class="text-gray-800">&nbsp;</td>
                            <td>
                                <div class="d-flex">
                                    <label
                                        class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <span class="form-check-label">
                                            <h4>Additional Permission</h4>
                                        </span>
                                    </label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-gray-800">Jobs</td>
                            <td>
                                <div class="d-flex">
                                    <label
                                        class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input form-check-input-edit" type="radio"
                                            value="1" <?php echo @$all_requisition_1; ?> name="all_requisition">
                                        <span class="form-check-label">All Jobs</span>
                                    </label>
                                    <label
                                        class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input form-check-input-edit" type="radio"
                                            value="0" <?php echo @$all_requisition_0; ?> name="all_requisition">
                                        <span class="form-check-label">Only Assign Job</span>
                                    </label>
                                </div>
                            </td>
                        </tr>


                        <tr>

                            <td class="text-gray-800">&nbsp;</td>
                            <td>
                                <div class="d-flex">
                                    <?php
                                    $html = '';
                                    $customArr = ['Add' => 14, 'View' => 15, 'Edit' => 16];
                                    foreach ($customArr as $key => $val) {
                                        $role_14_check = '';
                                        if (in_array($val, $permission_role)) {
                                            $role_14_check = 'checked="checked"';
                                        }
                                    
                                        $html .=
                                            '<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                    								<input class="form-check-input form-check-input-edit" type="checkbox" ' .
                                            $role_14_check .
                                            ' value="' .
                                            $val .
                                            '" name="role[]">
                                    								<span class="form-check-label">' .
                                            $key .
                                            '</span> 
                                    							</label>';
                                    }
                                    echo $html;
                                    ?>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-gray-800">Add Team Member On Job </td>
                            <td>
                                <div class="d-flex">
                                    <label
                                        class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input form-check-input-edit" type="radio"
                                            name="add_team_on_requisition" <?php echo @$add_team_on_requisition_1; ?> value="1">
                                        <span class="form-check-label">Yes</span>
                                    </label>
                                    <label
                                        class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input form-check-input-edit" type="radio"
                                            name="add_team_on_requisition" <?php echo @$add_team_on_requisition_0; ?> value="0">
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
        <button type="button" id="submit-frm-btn" class="btn btn-primary" data-kt-roles-modal-action="submit"> <span
                class="indicator-label">Update</span> <span class="indicator-progress">Please wait... <span
                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
    </div>
    <!--end::Actions-->
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<script>
    $('#submit-frm-btn').click(function() {

        var role_name = $('#role_name').val();
        if (role_name == '') {


            swal({
                title: "Warning!",
                text: "Rolename can not be null !",
                type: "warning",
                timer: 1000,
                showConfirmButton: true
            }, function() {
                //location.reload();
            });


        } else {

            $.ajax({
                url: '{{ route('service_company.settings.updateRole') }}',
                container: '#kt_modal_add_role_form',
                type: "POST",
                data: $('#kt_modal_add_role_form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        $('#role_name').val('');
                        swal({
                            title: "Success!",
                            text: "Update Roles & Permissions successfully	!",
                            type: "success",
                            timer: 1000,
                            showConfirmButton: false
                        }, function() {
                            location.reload();
                        });
                    }

                    if (response.status == 'warning') {
                        $('#role_name').val('');
                        swal({
                            title: "Warning!",
                            text: response.message,
                            type: "warning",
                            timer: 1000,
                            showConfirmButton: true
                        }, function() {
                            //location.reload();
                        });
                    }





                }
            })
        }


    });
</script>
