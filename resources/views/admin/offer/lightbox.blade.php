
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<div class="modal-header">
  <h4 class="modal-title"><i class="icon-plus"></i> Client Add User Information </h4>
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<style>
.message{ color:#006600; margin:10px; text-align:center; font-weight:bold}
.error{ color:#FF0000}
</style>
<div class="modal-body">
  <form id="createSchedule" name="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="client_id" id="client_id" value="{{ $client->id}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="contact_person_name">Contact Name</label>
            <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" placeholder="Enter Name">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="contact_person_email">Contact Email</label>
            <input type="text" class="form-control" name="contact_person_email" id="contact_person_email" placeholder="Enter Email">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="contact_person_number">Contact Number</label>
            <input type="text" class="form-control" name="contact_person_number" id="contact_person_number" placeholder="Enter Contact Number">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="contact_person_designation">Designation</label>
            <input type="text" class="form-control" name="contact_person_designation" id="contact_person_designation" placeholder="Enter Designation">
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
  <button type="button" class="btn dark btn-danger btn-sm" data-dismiss="modal">@lang('app.close') <i class="fa fa-circle fa-sm"></i></button>
  <button type="button" class="btn btn-success save-schedule btn-sm">Save <i class="fa fa-save fa-sm"></i></button>
</div>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>

<script>

$(document).ready(function() {

$("#createSchedule").validate({
    rules: {
						contact_person_name : {
													required: true,
													minlength: 3
						},
						contact_person_email:{
													required: true,
													email: true
						},
						contact_person_number:{
													required: true,
													number: true,
													minlength: 8,
													maxlength: 15
													
						},
						contact_person_designation: {
													required: true
						}
    	},
    messages : {
						contact_person_name: {
													required: "Please enter name",
													minlength: "Name should be at least 3 characters"
						},
						contact_person_email: {
													required: "Please enter email",
													email: "The email should be in the format: abc@domain.tld"
						},
						
						contact_person_number: {
													required: "Please enter number",
													number: "Please enter number as a numerical value"
						},
						
						contact_person_designation: {
													required: "Please enter designation"
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
					url			: '{{route('admin.clients.usermore')}}',
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
																
																var newOption = response.option;
																$('#location_id').append(newOption);
																$('.message').html(response.message);
																
																var ddl_id = response.option.id;
																var ddl_val = response.option.value;
																var newOption = $('<option value="'+ddl_id+'">'+ddl_val+'</option>');
																$('#contact_person').append(newOption);
 
	
																
															}
							
								}
	})
})
</script>
