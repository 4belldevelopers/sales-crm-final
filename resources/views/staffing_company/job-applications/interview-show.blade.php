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
body
{
	padding: 0!important;
	margin: 0!important;
}
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
font-weight: 500;
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
/*Radio btn css*/
.radio-tile-group
{
display: flex;
flex-wrap: wrap;
justify-content: left;
}
.radio-tile-group .input-container
{
position: relative;
height: 1rem;
/*width: 5rem;*/
margin: 0.3rem;}
.radio-tile-group .input-container1
{
position: relative;
height: 1rem;
width: 9rem!important;
margin: 0.3rem;
}
.radio-tile-group .input-container .radio-button
{
opacity: 0;
position: absolute;
top: 0;
left: 0;
height: 100%;
width: 100%;
margin: 0;
cursor: pointer;
height:5vh;
}
.modal .modal-header
{
outline:0;
}
/*Radio btn css end*/
.form-group label, label
{
font-weight: 500!important;
text-transform: capitalize;
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
  <h4 class="modal-title"> <span class="font-weight-600">Subject :</span> <?php echo @$master_status_name; ?> | <?php echo @$client_name; ?> | <?php echo @$job_title; ?> | <?php echo @$city; ?>&nbsp;
    <!-- <i class="fa fa-pencil text-primary"></i>&nbsp; -->
  </h4>
  <button type="button" class="close btn-sm mr-1" data-dismiss="modal" aria-hidden="true"> <i class="fa fa-times"></i> </button>
</div>
<!-- Applicant Name -->
<!-- 	<span class="">Applicant Name testing:</span>
	<span class="font-weight-400"><?php echo @$full_name ?></span> -->
<!-- Applicant Name End -->
<div class="modal-body pt-1 pb-1">
  <form id="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" id="token" value="OzrD7Td2eNIqlblQrbnkj2YxcCqSiGbFKVfH0LVk">
    <input type="hidden" name="_token" value="OzrD7Td2eNIqlblQrbnkj2YxcCqSiGbFKVfH0LVk">
    <div class="row">
      <div class="col-xs-6 col-md-6">
        <div class="form-group">
          <label class="d-block text-dark text-capitalize mt-0 font-weight-normal" style=""> <span class="font-weight-600">Scheduled Date :</span> 2021-06-02 22:15:00 </label>
        </div>
      </div>
      <div class="col-xs-6 col-md-6">
        <div class="form-group chooseCandidate bootstrap-timepicker timepicker">
          <label class="d-block text-dark text-capitalize mt-0 font-weight-normal" style=""> <span class="font-weight-600">Scheduled  Time : </span> 22:15 </label>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-5 col-md-12">
        <div class="form-group" id="child" style="display:none">
          <label class="d-block text-dark text-capitalize font-weight-normal" style=""> <span class="mt-0 font-weight-600">Interview Status Level :</span> Level - II </label>
        </div>
      </div>
    </div>
    <div class="row mt-0">
      <div class="col-xs-12 col-md-6">
        <div class="form-group">
          <label class="d-block text-dark text-capitalize font-weight-normal" style=""> <span class="mt-0 font-weight-600">Applicant Name : </span> <?php echo @$full_name ?></label>
        </div>
      </div>
      <div class="col-xs-12 col-md-6">
        <div class="form-group">
          <label class="d-block text-dark text-capitalize font-weight-normal" style=""> <span class="mt-0 font-weight-600">Interviewers : </span> </label>
        </div>
      </div>
    </div>
    <div class="row mt-0">
      <div class="col-xs-12 col-md-12">
        <div class="form-group">
          <label class="custom-label-color mt-0 font-weight-600 mb-0">Feedback</label>
          <div class="container pl-0 ml-0">
            <div class="radio-tile-group">
              <div class="input-container input-container1">
                <input id="pending" class="radio-button" type="radio" name="feedback_name"  value="Pending" 	<?php echo @$pending_chk; ?> />
                <div class="radio-tile radio-tile-pending <?php echo @$pending_first; ?>">
                  <label for="pending" class="mb-0 radio-tile-label radio-tile-label-pending <?php echo @$pending_second; ?>">Pending</label>
                </div>
              </div>
              <div class="input-container input-container1">
                <input id="shortlist" class="radio-button" type="radio" name="feedback_name" value="Shortlisted"	<?php echo @$shortlisted_chk; ?> />
                <div class="radio-tile radio-tile-shortlisted <?php echo @$shortlisted_first; ?>">
                  <label for="shortlist" class="mb-0 radio-tile-label radio-tile-label-shortlisted <?php echo @$shortlisted_second; ?>">Shortlisted</label>
                </div>
              </div>
              <div class="input-container input-container1">
                <input id="reschedule" class="radio-button" type="radio" name="feedback_name" value="Reschedule"	<?php echo @$reschedule_chk; ?> />
                <div class="radio-tile radio-tile-reschedule <?php echo @$reschedule_first; ?>">
                  <label for="reschedule" class="mb-0 radio-tile-label radio-tile-label-reschedule <?php echo @$reschedule_second; ?>">Reschedule</label>
                </div>
              </div>
              <div class="input-container input-container1">
                <input id="rejected" class="radio-button" type="radio" name="feedback_name" value="Rejected"	<?php echo @$rejected_chk; ?> />
                <div class="radio-tile radio-tile-rejected <?php echo @$rejected_first;  ?>">
                  <label for="rejected" class="mb-0 radio-tile-label radio-tile-label-rejected <?php echo @$rejected_second;  ?>">Rejected</label>
                </div>
              </div>
              <div class="input-container input-container1">
                <input id="other1" class="radio-button" type="radio" name="feedback_name" value="Other" 	<?php echo @$other_chk; ?> />
                <div class="radio-tile radio-tile-other <?php echo @$other_first; ?>">
                  <label for="other1" class="mb-0 radio-tile-label radio-tile-label-other <?php echo @$other_first; ?>">Other</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-4">
      <div class="col-xs-12 col-md-12">
        <div class="form-group">
          <label for="message-text" class="col-form-label pt-0 font-weight-600 mt-0">Feedback to Assessment Stage | Sub Stage</label>
          <textarea class="form-control" id="message" name="message" placeholder="Write a detail feedback for Interview" rows="6"></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="form-group row">
        <div class="col-12 col-form-label">
          <div class="checkbox-inline">
            <label class="checkbox checkbox-primary mt-0">
            <input type="checkbox" name="Checkboxes5" checked="checked">
            <span class="mr-1"></span> Add Feedback </label>
          </div>
        </div>
      </div>
    </div>
    <!-- For Interviewer -->
    <style>
          .rounded-pill {height: 18px;}
          .inside-img{font-size:9px;padding:0.3em;font-weight:bold;border: 1px solid #13a6ec;background:#13a6ec;color: #fff;}
        </style>
    <input type="hidden" name="application_id" id="application_id" value="620">
    <input type="hidden" name="interview_id" id="interview_id" value="20">
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
								url			: '{{route('staffing_company.job-applications.store-move')}}',
								container	: '#createSchedule',
								type		: "POST",
								data		: $('#createSchedule').serialize(),
								success		: function (response) 
								{
																if(response.status == 'success')
																{
																	table._fnDraw();
																	//$('#copyApplicationModal').modal('hide'); 
																	$('#moveApplicationModal').modal('hide'); 
																	
Swal.fire({
   icon: 'success',
  title: 'Application moved successfully',
  showConfirmButton: false,
  timer: 3000
})
$('#moveApplicationModal').modal('hide'); 	



																}
								}
				})
		
		}
		
    })
	
	
	
</script>
