<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<div class="modal-header">
  <h4 class="modal-title"><i class="icon-plus"></i> Client Add Office Address </h4>
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<style>
.message{ color:#006600; margin:10px; text-align:center; font-weight:bold}
.error{ color:#FF0000}
</style>
<?php 
		//echo '<pre>'; print_r($client);
?>
<div class="modal-body">
  <form id="createSchedule" name="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="client_id" id="client_id" value="{{ $client->id }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group font-weight-bold text-dark">
            <label for="website">Client</label>
            {{ ucfirst($client->name) }} </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="country_id">Country</label>
            <select name="country_id" id="country_id" class="form-control select2 custom-select">
              <?php 
						$option_list ='';
						foreach($countries as $country)
						{	
							echo $option 		=  '<option value="'.$country->id.'">'.ucfirst($country->country_name).'</option>';
							$option_list 	.= $option;
						} 
				 ?>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control" name="city" id="city" placeholder="City">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="address">Office address</label>
            <input type="text" class="form-control" name="address" id="address" placeholder="Office Address">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="postcode">Postcode</label>
            <input type="text" class="form-control" name="postcode" id="postcode" placeholder="Postcode">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group message"> </div>
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
						city : {
													required: true,
													minlength: 3
						},
						address:{
													required: true
						},
						postcode:{
													required: true,
													number: true,
													minlength: 5,
													maxlength: 6
													
						}
    	},
    messages : {
						city: {
													required: "Please enter city name",
													minlength: "City name should be at least 3 characters"
						},
						address: {
													required: "Please enter address"
						},
						postcode: {
													required: "Please enter postal code"
						}
    		}
  });
  
  
});

</script>
<script>
    
    // Save Interview Schedule
    $('.save-schedule').click(function () {
        $.easyAjax({
						url			: '{{route('admin.clients.addressmore')}}',
						container	: '#createSchedule',
						type		: "POST",
						data		: $('#createSchedule').serialize(),
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
																
																var ddl_id 				= response.option.id;
																var ddl_address 		= response.option.address;
																var ddl_city 			= response.option.city;
																var ddl_postcode 		= response.option.postcode;
																var ddl_country_name 	= response.option.country_name;
																
																var Html_value			=	ddl_address +'  '+ddl_city + '-' + ddl_postcode +' '+ddl_country_name ; 
																
																var newOption 	= $('<option value="'+ddl_id+'">'+Html_value+'</option>');
																$('#location_id').append(newOption);
																
																
								  
															}
															//$.unblockUI();
															//table._fnDraw();
								
									}
        })
    })
</script>
