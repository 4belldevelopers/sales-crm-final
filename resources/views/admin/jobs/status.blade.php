<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<style>
.modal-footer
{
	margin: 0 auto;
	border-top: 1px solid #ffffff!important;
}
.modal-body
{
	border-bottom: 1px solid #c1c1c1;
}
.dropdown-content li.active, .dropdown-content li:hover
{
	background-color: transparent!important;
}
.inactive
{
	background-color: #f64e60!important;
	color: #fff!important;
}
.activee
{
	background-color: #109600 !important;
	color: #fff!important;
}
.hold
{
	background-color: #ffa800!important;
	color: #fff!important;
}
</style>
<div class="modal-header">
	<h4 class="modal-title">Update Job Status</h4>
	<button type="button" class="close btn-sm mr-1" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>

<div class="modal-body">
<?php 
		//echo '<pre>'; print_r($job);  exit; 
?>
    <form id="createSchedule" class="ajax-form" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    	<input type="hidden" name="job_id" id="job_id" value="<?php echo @$job->id;?>" />
	    <div class="form-body">
            <div class="row" style="">
                <div class="col-xs-6 col-md-3">
                    <div class="form-group">
                        <label class="d-block"><i class="fa fa-briefcase fa-sm"></i>&nbsp;Job Title</label>
                    </div>
                </div>
                <div class="col-xs-6 col-md-9">
                    <div class="form-group">
                        <label class="d-block" style="font-weight:500!important;"><?php echo @$job->title ?></label>
                    </div>
                </div>  
  <?php 
  			$statusVal		=	$job->status;
			
			$inactive		=	'';
			$active			=	'';
			$hold			=	'';
			if($statusVal =='inactive')
			{
				$inactive		=	'selected="selected"';	
			}
			if($statusVal =='active')
			{
				$active		=	'selected="selected"';	
			}
			if($statusVal =='hold')
			{
				$hold		=	'selected="selected"';	
			}			
  ?>       </div>
  			 <div class="row">  	     
				<div class="col-xs-6 col-md-3">
                    <div class="form-group">
                        <label class="d-block">
                        	<i class="fa fa-line-chart fa-sm"></i>&nbsp;Job Status
                        </label>
                    </div>
                </div> 	     
				<div class="col-xs-6 col-md-9">
                    <div class="form-group">
                    	<label class="d-block">
	                        <select name="status" id="status">
								<option value="active" class="activee text-white"
									<?php echo $active; ?>>Active
								</option>
								<option value="hold" class="bg-warning text-white"
									<?php echo $hold; ?>>Hold
								</option>
								<option value="inactive" class="bg-danger text-white"
									<?php echo $inactive; ?>>Closed
								</option>
							</select>
						</label>
                    </div>
                </div>
			</div> 	
			<div class="row">
                <div class="col-xs-12 col-md-12 ">
                    <div class="form-group" id="status_msg" style="text-align: center;font-weight: 500;background-color: #fff!important;width: fit-content;color: #00a700;margin: 0 auto;">
                    </div>
                </div>
            </div>           
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light-danger btn-sm p-1" data-dismiss="modal">@lang('app.close')
    	<i class="fa fa-circle fa-sm"></i>
    </button>
    <button type="button" class="btn btn-light-success btn-sm p-1" id="" onclick="newUpdateJobstatus()">@lang('app.update')
    	<i class="fa fa-spinner fa-sm"></i>
    </button>
</div>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script type="text/javascript">
	
	$( document ).ready(function()
	{    	
		$( "#status" ).change(function() {
				$( "#status" ).removeClass('inactive');
				$( "#status" ).removeClass('activee');
				$( "#status" ).removeClass('hold');
				// alert( this.value );
				 if(this.value == 'inactive'){
				 		$( "#status" ).addClass('inactive');

				 }else if(this.value == 'active'){
				 		$( "#status" ).addClass('activee');

				 }else{ 
				 		$( "#status" ).addClass('hold');
				 }
		});
	});
</script>
<script>
    // Select 2 init.
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    // Datepicker set
    $('#scheduleDate').bootstrapMaterialDatePicker
    ({
        time			: false,
        clearButton		: true,
        minDate 		: new Date()
    });

    // Timepicker Set
    $('#scheduleTime').bootstrapMaterialDatePicker
    ({
        date			: false,
        shortTime		: true,   // look it
        format			: 'HH:mm',
        switchOnClick	: true
    });	
	function newUpdateJobstatus()
	{
		//alert("call me");
		$.easyAjax({
					url: '{{route('admin.jobs.newUpdateJobstatus')}}',
					container: '#createSchedule',
					type: "POST",
					data: $('#createSchedule').serialize(),
					success: function (response) {
						if(response.status == 'success'){
						
							$('.activeJobs').html(response.activeJobs);
							$('.inactiveJobs').html(response.inactiveJobs);
							$('.holdJobs').html(response.holdJobs);
							
							$('#status_msg').html("Application status update successfully.");
							 table._fnDraw();
					   }
					}
				})	
	}	
</script>
