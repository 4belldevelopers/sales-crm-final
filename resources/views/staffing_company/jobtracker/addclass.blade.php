<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<style>
    .form-group label {margin-top: 0em!important;}
</style>
<div class="modal-header">
  <!-- <h4 class="modal-title"><i class="icon-plus"></i>Add New Column </h4> -->
  <h4 class="modal-title"><i class="fa fa-briefcase fa-sm text-dark"></i> Job Title - <?php echo @$job->title ?> </h4>
  <button type="button" class="close mr-0" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<div class="modal-body">
  <?php 
		//echo '<pre>'; print_r($candidates); exit; 
?>
  <form id="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="job_id" id="job_id" value="<?php echo @$job->id;?>" />
    <div class="form-body">
      <div class="col-md-7">
        <div class="form-group">
          <label for="address">Column Name</label>
          <input type="text" name="class_name" id="class_name" class="form-control" placeholder="Enter Column Name">
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 col-md-12 text-center">
          <div class="form-group" id="status_msg" style="color:#218838; text-align:center; margin:10px; font-weight:bold;"> </div>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer">
<!--<button type="button" class="btn dark btn-danger btn-sm" data-dismiss="modal">@lang('app.close')</button>-->
<!--<button type="button" class="btn btn-success btn-sm save-schedule addNewClass" >@lang('app.submit')</button>-->
<div class="modal-footer">
  <button type="button" class="btn btn-light-danger btn-sm mr-2 btn-sm p-1" data-dismiss="modal">@lang('app.close')&nbsp;<i class="fa fa-circle fa-sm"></i></button>
  <button type="button" class="btn btn-light-success btn-sm save-schedule addNewClass btn-sm p-1">@lang('app.submit')&nbsp;<i class="fa fa-spinner fa-sm"></i></button>
</div>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>

	$( document ).ready(function() {
   
    	console.log( "ready!" );
	
	});
	
	$( ".addNewClass" ).click(function() {
	//function addNewClass()
	//{
				var class_name	=	$('#class_name').val();
				//alert(class_name); 
				if(class_name == '')
				{
					$( "#class_name" ).focus();
					$('#status_msg').html("please enter class name.");	
					return false;
				}
		
					$.easyAjax({
								url: '{{route('staffing_company.job.saveClass')}}',
								container: '#createSchedule',
								type: "POST",
								data: $('#createSchedule').serialize(),
								success: function (response) {
									if(response.status == 'success'){
									
										
										document.getElementById("class_name").value = "";
										$('#status_msg').html("Class added successfully.");
											table._fnDraw();
											//tableLoad('load');
								   }
								}
							})	
	});
	
	
	
	
	
	
	
   
</script>
