<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<div class="modal-header">
  <h4 class="modal-title">Select Column </h4>
  <button type="button" class="close mr-0" data-dismiss="modal" aria-hidden="true">
  	<i class="fa fa-times" style="font-size: 0.8rem;outline:0;"></i>
  </button>
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
.error{ color:#FF0000}
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
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='1' class="column_visibility_control" checked="checked"> Applicant Name  </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='2' class="column_visibility_control" checked="checked"> Requisition </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='3' class="column_visibility_control" checked="checked"> Client </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='4' class="column_visibility_control" checked="checked"> Loc </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='5' class="column_visibility_control" checked="checked">  REP </label>
          </div>
		  
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='6' class="column_visibility_control" checked="checked"> P.CTC </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='7' class="column_visibility_control" checked="checked"> AOF </label>
          </div>		
		  
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='8' class="column_visibility_control" checked="checked"> DOC </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='9' class="column_visibility_control" checked="checked"> SFA </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='10' class="column_visibility_control" checked="checked"> OAP </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='11' class="column_visibility_control" checked="checked"> ORE </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='12' class="column_visibility_control" checked="checked"> OAC </label>
          </div>
		  
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='13' class="column_visibility_control" checked="checked"> JOI </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='14' class="column_visibility_control" checked="checked"> J.Date </label> 
          </div> 
		  
		  
        </div>
        <div class="col-md-12">
          <div class="form-group message"></div>
        </div>
      </div>
    </div>
  </form>
</div>



<div class="modal-footer w-100 px-0 pt-0 pb-2">
  <button type="button" class="btn btn-light-danger font-weight-500 d-block w-100 py-1 btn-sm" data-dismiss="modal">@lang('app.close')</button>
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
