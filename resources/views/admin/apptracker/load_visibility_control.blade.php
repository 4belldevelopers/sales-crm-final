<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<div class="modal-header">
  <h4 class="modal-title"><i class="icon-plus"></i>  Column Visibility Control </h4>
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<style>
.modal-dialog {
    position: relative;
    width: auto;
    margin: .5rem;
    pointer-events: none;
    float: right;
}
.modal-lg {
    max-width: 350px;
} 
.message{ color:#006600; margin:10px; text-align:center; font-weight:bold}
.error{ color:#FF0000;}
#scheduleDetailModal_cvc
{
	height: auto;
  padding-right: 0px!important;
}
</style>
<div class="modal-body">
  <form id="createSchedule" name="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
    <div class="form-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='0' class="column_visibility_control" checked="checked"> ID  </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='1' class="column_visibility_control" checked="checked"> Date </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='2' class="column_visibility_control" checked="checked"> Applicant </label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='3' class="column_visibility_control" checked="checked"> Client </label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='4' class="column_visibility_control" checked="checked"> Jobs </label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='5' class="column_visibility_control" checked="checked"> Source </label>
          </div>
		  
		  <?php 
		  
		  			$x					=	5; 
		  			if(!empty($jobSkill))
					{
						foreach($jobSkill as $sval)
						{
							$x = $x+1;
							echo ' <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name="hide_columns[]" value="'.$x.'" class="column_visibility_control" checked="checked"> '.$sval->name.' </label>
          </div>';	
						}
					}
					
					$x_email 			= $x+1;
					$x_phone 			= $x+2;
					$x_ctc 				= $x+3;
					$x_ectc 			= $x+4;
					$x_exp 				= $x+5;
					$x_relexp 			= $x+6;
					$x_passport 		= $x+7;
					$x_np 				= $x+8;
					$x_curr_desig 		= $x+9;
					$x_curr_co 			= $x+10;
					$x_curr_loc 		= $x+11;
					$x_qualification 	= $x+12;
					$x_status 			= $x+13;
					$x_interview_status_level = $x+14;
					$x_resume 			= $x+15;
					$x_action 			= $x+16;
					
		  ?>
		  
		   <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='<?php echo $x_email; ?>' class="column_visibility_control" checked="checked"> Email </label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_phone; ?>' class="column_visibility_control" checked="checked"> Phone </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_ctc; ?>' class="column_visibility_control" checked="checked"> CTC</label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_ectc; ?>' class="column_visibility_control" checked="checked"> ECTC</label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_exp; ?>' class="column_visibility_control" checked="checked"> EXP</label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_relexp; ?>' class="column_visibility_control" checked="checked"> REL EXP</label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_passport; ?>' class="column_visibility_control" checked="checked"> Passport</label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_np; ?>' class="column_visibility_control" checked="checked"> Notice Period</label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_curr_desig; ?>' class="column_visibility_control" checked="checked"> Current DSGN</label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_curr_co; ?>' class="column_visibility_control" checked="checked"> Current Company</label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_curr_loc; ?>' class="column_visibility_control" checked="checked"> Current Location</label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_qualification; ?>' class="column_visibility_control" checked="checked"> Qualification</label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_status; ?>' class="column_visibility_control" checked="checked"> Status</label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_interview_status_level; ?>' class="column_visibility_control" checked="checked"> Interview Status Level</label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_resume; ?>' class="column_visibility_control" checked="checked"> Resume</label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='<?php echo $x_action; ?>' class="column_visibility_control" checked="checked"> Action</label>
          </div>
		  
        </div>
        <div class="col-md-12">
          <div class="form-group message"></div>
        </div>
      </div>
    </div>
  </form>
</div>



<div class="modal-footer">
  <button type="button" class="btn dark btn-outline" data-dismiss="modal">@lang('app.close')</button>
 </div>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>

$(document).ready(function() {


	$('.column_visibility_control').click(function()
		{
				var checked_arr 	= [];
				var unchecked_arr 	= [];
				
				$.each($('input[type="checkbox"]:checked'), function (key, value) {
						var dt = $('#myTable').DataTable();
						dt.column(this.value).visible(true);
				});
				
				$.each($('input[type="checkbox"]:not(:checked)'), function (key, value) {
						var dt = $('#myTable').DataTable();
						dt.column(this.value).visible(false);
				});
				
		});
		
$("#createSchedule").validate({
    rules: {
						name : {
											required: true,
											minlength: 1
								}
    	   },
    messages : {
						name: {
											required: "Please enter name",
											minlength: "Name should be at least 1 characters"
							  }
    		   }
  });
  
  
});

</script>

<script>
// Save User Information alert( "Valid: " + $('#createSchedule').valid() );
$('.save-schedule').click(function () {

	
	
	if( $('#createSchedule').valid() == false)
	{
		return false;
	}
	$.easyAjax({
					url			: '{{route('admin.skills.saveMoreSkills')}}',
					container	: '#createSchedule',
					type		: "POST",
					data		: $('#createSchedule').serialize(),
					beforeSend: function() {
											
											//alert( "Valid: " + $('#createSchedule').valid() ); return false;
					},
					success		: function (response) {
															if(response.status =='error')
															{
																$('.message').html(response.message);	
															}
															
															if(response.status == 'success')
															{
																
																var newOption 	= response.option;
																$('#location_id').append(newOption);
																$('.message').html(response.message);
																
																var ddl_id 		= response.option.id;
																var ddl_val 	= response.option.name;
																var newOption 	= $('<option value="'+ddl_id+'">'+ddl_val+'</option>');
																$('#job_skills').append(newOption);
 
	
																
															}
							
								}
	})
})
</script>
