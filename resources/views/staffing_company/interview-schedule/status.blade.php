<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<style>
		.form-group label {margin-top: 0em!important;}
		.custm-style{border: 1px solid #dedcdc; padding: .5em .2em; border-radius: 4px;}
		.custom-label-color{font-weight: 500;color: #444444;}
		.fresh-padding{padding: 4px 38px;height: 30px;font-size: 15px;}
		.resechudule-padding{padding: 4px 5px;height: 30px;font-size: 15px;}
		.candidate-name{color: #060606!important;font-size: 14px!important;}
		/*Radio btn css*/
		.radio-tile-group{display: flex;flex-wrap: wrap;justify-content: left;}
		.radio-tile-group .input-container{position: relative;height: 1rem;width:4.3rem;margin: 0.3rem;}
		.radio-tile-group .input-container .radio-button{opacity: 0;position: absolute;top: 0;left: 0;height: 100%;width: 100%;margin: 0;
		cursor: pointer;height:5vh}
		.radio-tile-group .input-container .radio-tile{display: flex;flex-direction: column;align-items: center;justify-content: center;
		  width: 100%;height: 100%;border: 2px solid #079ad9;border-radius: 3px;padding: 1rem;transition: transform 300ms ease;}
		.radio-tile-group .input-container .radio-tile-label{text-align: center;text-transform: capitalize;color: #079ad9;
		font-size: 12px;margin:0;}
		.radio-tile-group .input-container .radio-button:checked + .radio-tile{background-color: #079ad9;border: 2px solid #079ad9;
		  color: white;transform: scale(1.1, 1.1);}
		.modal-header{outline: 0;padding: 15px 0px 15px 0px!important;}
		/*.radio-tile-group .input-container .radio-button:checked + .radio-tile .icon svg {fill: white;background-color: #079ad9;}*/
		.radio-tile-group .input-container .radio-button:checked + .radio-tile .radio-tile-label {color: white;background-color: #079ad9;margin:0;}
		.modal-header{outline: 0;padding:.5em 0em .5em 0em!important}
		/*.radio-tile-group .input-container .radio-button:checked + .radio-tile .icon svg {fill: white;background-color: #079ad9;}*/
		.radio-tile-group .input-container .radio-button:checked + .radio-tile .radio-tile-label {color: white;background-color: #079ad9;margin:0;}
		/*Radio btn css end*/
</style>
<?php 
	
	$scheduleDate				= $schedule_from_time =	$schedule_to_time =	$url_link	=	$full_name= $message = $master_status_name =  '';	
	$interview_type_fresh       = '';
	$interview_type_re_schedule = '';
	
	if($feedback_id == 4){
		$interview_type_re_schedule       	= 'checked="checked"';
	}else{
		$interview_type_fresh 				= 'checked="checked"';	
	}
	
	
	
	$time 			= strtotime(@$interview_detail->schedule_date);
	$scheduleDate 	= date('Y-m-d',$time);

	$schedule_from_time	=	@$interview_detail->schedule_from_time;
	$schedule_to_time	=	@$interview_detail->schedule_to_time;
	
	$url_link			=	@$interview_detail->url_link;
	$other_email		=	@$interview_detail->other_email;
	$message			=	@$interview_detail->message;
	
	$master_status_name	=	@$interview_detail->master_status_name;
	
	//APPLICATION DATA 
	$full_name			= $client_name	= $job_title	=  $city = $company_id =  ''; 
	$full_name			=	@$application->full_name;
	$client_name		=	@$application->client_name;
	$job_title			=	@$application->job_title;
	$city				=	@$application->city;
	$company_id			=	@$application->company_id;
	
	
?>
<div class="modal-header" id="debug" contenteditable data-name="custom-text">
  <h4 class="modal-title"> Subject : <?php echo @$master_status_name; ?> | <?php echo @$client_name; ?> | <?php echo @$job_title; ?> | <?php echo @$city; ?>&nbsp; <a href="javascript:void(0);"> <i class="fa fa-pencil fa-sm text-primary"></i> </a></h4>
  <button type="button" class="close btn-sm mr-0" data-dismiss="modal" aria-hidden="true"> <i class="ti-close text-dark"></i> </button>
</div>
<div class="modal-body pt-3 pb-0">
  <form id="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="application_id" id="application_id" value="<?php echo @$application->id ?>" />
    <input type="hidden" name="interview_schedule_id" id="interview_schedule_id" value="<?php echo @$interview_id; ?>" />
    <div class="form-body">
      <div class="row">
        <div class="col-xs-6 col-md-4">
          <div class="form-group">
            <label class="custom-label-color">Assessment Stage</label>
            <select name="masterStatus" id="masterStatus" class="form-control" onchange="getMasterChild(this.value, <?php echo @$application->id ?>, <?php echo $company_id; ?>);">
              <option value="" >Select Stage</option>
              <?php 
								if(!empty($interviewStatus))
                {
								  foreach($interviewStatus as $int_val )
                  {
									$sel	=	'';
									if($interview_detail->stage == $int_val->id)
                  {
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
        <div class="col-xs-6 col-md-4">
          <div class="form-group sub_status" style="display:none">
            <label class="custom-label-color">Sub Status</label>
            <select name="child_status" id="child_status" class="form-control">
              <option value="" >Select Sub-Status</option>
            </select>
          </div>
        </div>
        <div class="col-xs-5 col-md-4 col-form-label padding-0">
          <div class="form-group">
            <label class="custom-label-color mb-0 pl-1">Interview Type</label>
            <div class="container padding-0">
              <div class="radio-tile-group">
                <div class="input-container" style="width:10em;">
                  <input id="fresh" class="radio-button" name="interview_type" type="radio"   value="1" <?php echo @$interview_type_fresh; ?> />
                  <div class="radio-tile">
                    <label for="radio" class="radio-tile-label">Fresh</label>
                  </div>
                </div>
                <div class="input-container" style="width:10em;">
                  <input id="reschedule" class="radio-button" name="interview_type"  type="radio"  value="2" <?php echo @$interview_type_re_schedule; ?> />
                  <div class="radio-tile">
                    <label for="radio" class="radio-tile-label">Re-Schedule</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-xs-6 col-md-5">
          <div class="form-group">
            <label class="custom-label-color">@lang('modules.interviewSchedule.scheduleDate')</label>
            <!-- <label>Scheduled Date</label> -->
            <input type="text" name="schedule_date" id="schedule_date" class="form-control" placeholder="Enter Your Date" value="<?php echo @$scheduleDate; ?>">
          </div>
        </div>
        <div class="col-xs-6 col-md-7 pl-8">
          <div class="row">
            <div class="col-xs-5 col-md-5 padding-0">
              <div class="form-group chooseCandidate bootstrap-timepicker timepicker">
                <label class="custom-label-color">From Time</label>
                <input type="time" name="schedule_from_time" id="schedule_from_time" class="form-control" placeholder="Time" value="<?php echo @$schedule_from_time; ?>">
              </div>
            </div>
            <div class="col-xs-1 col-md-1 mt-2 padding-0 text-center"><br>
              <br>
              <span>To</span> </div>
            <div class="col-xs-5 col-md-5 padding-0">
              <div class="form-group chooseCandidate bootstrap-timepicker timepicker">
                <label class="custom-label-color">To</label>
                <input type="time" name="schedule_to_time" id="schedule_to_time" class="form-control" placeholder="Reschedule Time" value="<?php echo @$schedule_to_time; ?>">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-xs-6 col-md-5">
          <div class="form-group">
            <label class="checkbox checkbox-outline checkbox-success custom-label-color mb-0"> Interview Mode </label>
            <div class="container padding-0">
              <div class="radio-tile-group">
                <?php
			  			$i=0;
			  			if(!empty($interview_node)){
							foreach($interview_node as $key=>$val){
							
								$interview_mode			=	$chk = '';
								$interview_mode			=	@$interview_detail->interview_mode;
								if($interview_mode==$key){
									$chk = 'checked="checked"';
								}								
			       ?>
                <div class="input-container">
                  <input id="intmode<?php echo @$val; ?>" class="radio-button" name="interview_mode" type="radio" <?php echo $chk; ?> value="<?php echo @$key; ?>">
                  <div class="radio-tile">
                    <label for="<?php echo @$val; ?>" class="radio-tile-label"><?php echo @$val; ?></label>
                  </div>
                </div>
                <?php 
      					$i++;
      						}
      					}
        			?>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-md-7">
          <div class="form-group">
            <label class="custom-label-color"> Mention Link </label>
            <input type="text" name="url_link" id="url_link" placeholder="Ex: https://meet.google.com" class="form-control" value="<?php echo @$url_link; ?>">
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-xs-6 col-md-5">
          <div class="form-group">
            <label class="custom-label-color"> Interviewers </label>
            <select name="interviewers[]" id="Interviewers" class="select2 m-b-10 form-control select2-multiple" multiple="multiple" data-placeholder="Choose Interviewers" >
              <?php 
		   			if(!empty($client_contact_info)){
						foreach($client_contact_info as $cval){
						
							$sel			=	$interviewers = '';
							$interviewers	=	@$interview_detail->interviewers;
							if(!empty($interviewers)){
								if (strpos($interviewers, (string)@$cval->id) !== false) {
								$sel			=	'selected="selected"';
								}
							}
						?>
              <option value="<?php echo @$cval->id; ?>"
              <?php echo @$sel; ?> ><?php echo @$cval->contact_person_name;  ?> </option>
              <?php 
		   			}}
		        ?>
            </select>
          </div>
        </div>
        <div class="col-xs-6 col-md-7">
          <div class="form-group">
            <label class="custom-label-color"> Add Email Address </label>
            <input type="email" name="other_email" id="other_email" placeholder="Ex: test@example.com" class="form-control" value="<?php echo @$other_email; ?>">
          </div>
        </div>
      </div>
      <div class="row mt-1">
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <!-- <label class="d-block">Candidate Name</label> -->
            <label class="d-block text-dark text-capitalize" style=""> <span class="">Candidate :</span> <?php echo @$full_name ?> </label>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <label class="custom-label-color"> Message </label>
            <textarea type="text" name="message" id="message" placeholder="Write a Message..." rows="3" class="form-control"><?php echo @$message; ?></textarea>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="form-group row">
          <div class="col-12 col-form-label">
            <div class="checkbox-inline">
              <label class="checkbox checkbox-primary">
              <input type="checkbox" name="email_everyone" id="email_everyone" value="1" checked="">
              <span class="mr-1"></span> Send email notifaction to everyone. </label>
            </div>
          </div>
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
  <button type="button" class="btn btn-light-danger btn-sm mr-2 p-1" data-dismiss="modal"> @lang('app.close')&nbsp; <i class="fa fa-circle fa-sm"></i> </button>
  <button type="button" class="btn btn-light-success btn-sm save_schedule_reschedule p-1"> @lang('app.submit')&nbsp;<i class="fa fa-spinner fa-sm"></i> </button>
</div>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
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
	function getMasterChild(id){
		
		
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
												$('#child').show();
						 						$('#childStatus').html(response.data);
											}
											else
											{
												$('#child').hide();
						 						//$('#childStatus').html(response.data);
											}
                         											 					
											
										},
										complete: function (data) {
												//$('.clientStatusLoader').html('');
										 }
                    });
					
					
	}
		
	 // Save Interview Schedule
    $('.save_schedule_reschedule').click(function () {
        $.easyAjax({
            url: '{{route('staffing_company.job-applications.store-schedule')}}',
            container: '#createSchedule',
            type: "POST",
            data: $('#createSchedule').serialize(),
            success: function (response) {
               // if(response.status == 'success'){                 
				   table._fnDraw();
               // }
            }
        })
    })	
	
</script>
