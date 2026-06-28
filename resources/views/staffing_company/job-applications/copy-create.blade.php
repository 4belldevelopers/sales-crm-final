<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
<style>
.form-group label
{
	margin-top: 0em!important;
}
.custm-style
{
	border: 1px solid #dedcdc;
	padding: .5em .2em;
	border-radius: 4px;
}
.custom-label-color
{
	font-weight: 900;
	color: #444444;
}
.fresh-padding
{
	padding: 4px 38px;
	height: 30px;
	font-size: 15px;
}
.resechudule-padding
{
	padding: 4px 5px;
	height: 30px;
	font-size: 15px;
}
.candidate-name
{
	color: #060606!important;
	font-size: 14px!important;
}
.error
{
	color:#FF0000;
}
</style>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<?php 
	
	$scheduleDate				= $schedule_from_time =	$schedule_to_time =	$url_link	=	$full_name= $message = $master_status_name =  '';	
	$interview_type_fresh       = '';
	$interview_type_re_schedule = '';
	
	
	$interview_type_fresh 				= 'checked="checked"';	
	
	//APPLICATION DATA 
	$full_name			= $client_name	= $job_title	=  $city = $company_id = $master_status_id =  ''; 
	$full_name			=	@$application->full_name;
	$client_name		=	@$application->client_name;
	$job_title			=	@$application->job_title;
	$city				=	@$application->city;
	$company_id			=	@$application->company_id;
	$master_status_name	=	@$application->master_status_name;
	$master_status_id	=	@$application->master_status_id;
	$job_id				=	@$application->job_id;
	
?>
<div class="modal-header" id="debug" contenteditable data-name="custom-text">
  <h4 class="modal-title">Copy Application : <?php echo @$master_status_name; ?> | <?php echo @$client_name; ?> | <?php echo @$job_title; ?> | <?php echo @$city; ?>&nbsp; <i class="fa fa-pencil text-primary"></i>&nbsp; </h4>
  <button type="button" class="close btn-sm mr-0" data-dismiss="modal" aria-hidden="true"> <i class="fa fa-times"></i> </button>
</div>
<div class="modal-body pb-0">
  <form id="createSchedule" name="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="application_id" id="application_id" value="<?php echo @$application->id ?>" />
    <div class="form-body">
      <div class="row">
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <label class="d-block text-dark text-capitalize" style=""> <span class="">Applicant Name : </span> <span class="font-weight-400"><?php echo @$full_name ?></span> </label>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <label>Requisitions Title</label>
            <select name="job_id" id="job_id" class="form-control">
              <option value="">Select  Requisitions</option>
              <?php 
			
				if(!empty($jobs)){
				foreach($jobs as $jd){
				
				$id		=	$title =	$min_salary	= $max_salary	= $salary =	$created_at	=	$client_name	=	$location_city	=	'';
				
				$id		=	$jd->id; 
				$title	=	$jd->title; 
				
				$min_salary			=	$jd->min_salary;
				$max_salary			=	$jd->max_salary;
				$salary 			= 'Salary('.$min_salary.'-'.$max_salary.')';
				
				if(($min_salary == 0) && ($max_salary == 0) )
				{
					$salary 			= 'Salary(Not Disclosed)';	
				}
				
				
				$created_at			=	$jd->created_at;
				$client_name		=	$jd->client_name;
				$location_city		=	$jd->location_city;
				
				$applicantJobId		=	$sel	=	'';
				$applicantJobId		=	@$application->job_id;
				if($applicantJobId ==	$jd->id ){
					$sel	=	'selected="selected"';	
				}
				
?>
              <option value="<?php echo $jd->id ?>"		<?php echo @$sel; ?> > <?php echo $id; ?> &nbsp;|&nbsp; <?php echo $title; ?> &nbsp;|&nbsp; <?php echo $client_name; ?> &nbsp;|&nbsp; <?php echo $location_city; ?> &nbsp;|&nbsp; <?php echo $salary; ?> &nbsp;|&nbsp; <?php echo $created_at; ?> </option>
              <?php 
					}
				}	
?>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <label for="masterStatus" class="custom-label-color">Assessment Stage</label>
            <select name="masterStatus" id="masterStatus" class="form-control"  onchange="getMasterChild(this.value, <?php echo @$application->id ?>, <?php echo $job_id; ?>);">
              <option value="" >Select Stage</option>
              <?php 
						if(!empty($interviewStatus)){
							foreach($interviewStatus as $int_val ){
								$sel	=	'';
								if($master_status_id == $int_val->id){
									$sel	=	'selected="selected"';	
								}
				
				?>
              <option value="<?php echo $int_val->id ?>"  <?php echo @$sel;?> ><?php echo $int_val->name ?></option>
              <?php 
							}
						}	
				?>
            </select>
          </div>
        </div>
        <div class="col-xs-6 col-md-6">
          <div class="form-group sub_status" style="display:none">
            <label class="custom-label-color">Sub Status</label>
            <select name="child_status" id="child_status" class="form-control">
              <option value="" >Select Sub-Status</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-xs-12 col-md-12 text-center">
          <div class="form-group" id="status_msg" style="color:#218838; text-align:center; margin:10px; font-weight:bold;"> </div>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-light-danger btn-sm mr-2 p-1" data-dismiss="modal">@lang('app.close')&nbsp;<i class="fa fa-circle fa-sm"></i></button>
  <button type="button" class="btn btn-light-success btn-sm save-schedule p-1">@lang('app.submit')&nbsp;<i class="fa fa-spinner fa-sm"></i></button>
</div>
<style>
.error{ color:#FF0000}
</style>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>
<script>
    // Select 2 init.
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    // Datepicker set
    $('#schedule_date').bootstrapMaterialDatePicker
    ({
        time: false,
        clearButton: true,
        minDate : new Date()
    });

    // Timepicker Set
    $('#scheduleTime').bootstrapMaterialDatePicker
    ({
        date: false,
        shortTime: true,   // look it
        format: 'HH:mm',
        switchOnClick: true
    });
	
	function getMasterChild(current_status, current_application_id, job_id)
		{
				var id 				= id;
				var url 			= "{{ route('staffing_company.apptracker.getchild',':id') }}";
				url 				= url.replace(':id', current_application_id);
				var baseurl 		= "{{ asset('assets/images/Loading2.gif') }}"; 
				var token 			= "{{ csrf_token() }}";
				
				 $.easyAjax({
									type: 'POST',
									url: url,
									data: {'_token': token, '_method': 'POST', 'current_application_id' : current_application_id, 'current_status': current_status, 'job_id': job_id },
									beforeSend: function() {
																$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
									},
									success: function (response) 
									{
										if(response.flag == 1)
											{
												$('.sub_status').show();
						 						$('#child_status').html(response.data);
											}
											else
											{
												$('.sub_status').hide();
						 						//$('#childStatus').html(response.data);
											}
									},
									complete: function (data) {
											//$('#childStatus_'+current_application_id).html('');
									 }
				});
	   }
	   
	function getMasterChild2(id){
		
		
					var id 		= id;
					var url 			= "{{ route('staffing_company.cvtracker.getchild',':id') }}";
                    url 				= url.replace(':id', id);

					var baseurl 		= "{{ asset('assets/images/AjaxLoader.gif') }}"; 
				
                    var token 			= "{{ csrf_token() }}";
					//alert(clientId); 
					
					 $.easyAjax({
                        				type: 'POST',
                        				url: url,
                        				data: {'_token': token, '_method': 'POST', 'id' : id},
										beforeSend: function() {
																	//$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
										},
                        				success: function (response) 
										{
											if(response.flag == 1)
											{
												$('.sub_status').show();
						 						$('#child_status').html(response.data);
											}
											else
											{
												$('.sub_status').hide();
						 						//$('#childStatus').html(response.data);
											}
                         					
						 					
											
										},
										complete: function (data) {
												//$('.clientStatusLoader').html('');
										 }
                    });
					
					
	}
	
   
	
	$("#schedule_date").on('change', function() {
        $(this).valid();  
    });
	$("#createSchedule").validate({
			onfocusout: function(e) {
        		this.element(e);
    		},
	
			rules: {
						job_id		: "required"				
					},
			messages: {
						job_id				: "Please select Requisitions Title"
					 }
		});
		
		
	 // Save Interview Schedule
    $('.save-schedule').click(function () {
       
	    if($("#createSchedule").valid())
		{
		
				$.easyAjax({
								url			: '{{route('staffing_company.job-applications.store-copy')}}',
								container	: '#createSchedule',
								type		: "POST",
								data		: $('#createSchedule').serialize(),
								success		: function (response) 
								{
																if(response.status == 'success')
																{
																	table._fnDraw();
																	$('#copyApplicationModal').modal('hide'); 
																	
																	
																	
Swal.fire({
 
  icon: 'success',
  title: 'Application copy successfully',
  showConfirmButton: false,
  timer: 3000
})
$('#copyApplicationModal').modal('hide'); 	
																	
																	
																	
																	
																	
																	
																	
																	
																	
																	
																	
																	
																	
																	
																}
								}
				})
		
		}
		
    })
	
	
	
</script>
