
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<div class="modal-header">
  <h4 class="modal-title">
  	Add more skills</h4>
  <button type="button" class="close mr-0" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<style>
.message
{
	color:#006600;
	margin:10px;
	text-align:center;
	font-weight:bold;
}
.error
{
	color:#FF0000;
}
</style>
<div class="modal-body">
  <form id="createSchedule" name="createSchedule" class="ajax-form" method="post">
    
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="contact_person_name">Skill Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Name">
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
  <button type="button" class="btn btn-light-danger btn-sm" data-dismiss="modal">
  	<i class="fa fa-circle"></i>&nbsp;@lang('app.close')
  </button>
  <button type="button" class="btn btn-light-success btn-sm">
  	<i class="fa fa-save"></i>&nbsp;Save
  </button>
</div>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>

<script>

$(document).ready(function() {

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
