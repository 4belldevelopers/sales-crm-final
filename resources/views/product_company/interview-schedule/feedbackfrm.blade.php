<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
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
<?php 
//APPLICATION DATA 
$full_name			= $client_name	= $job_title	=  $city = $company_id =   ''; 

$full_name			=	@$application->full_name;
$client_name		=	@$application->client_name;
$job_title			=	@$application->job_title;
$city				=	@$application->city;
$company_id			=	@$application->company_id;


$schedule_date			=	$schedule_date =	$schedule_from_time	=	''; ;	
$schedule_date			=	$interview_detail->schedule_date;
$schedule_from_time		=	$interview_detail->schedule_from_time;


//$	=	Carbon::parse($interview_detail->schedule_date)->format('d M, Y H:i a');
//$interviewStatus	= '';

//echo '<pre>'; print_r(@$interviewers); 
?>
<div class="modal-header modal-htcp">
  <h4 class="modal-title"> <span class="font-weight-600">Subject :</span> <?php echo @$master_status_name; ?> | <?php echo @$client_name; ?> | <?php echo @$job_title; ?> | <?php echo @$city; ?>&nbsp; </h4>
  <button type="button" class="close btn-sm mr-1" data-dismiss="modal" aria-hidden="true"> <i class="fa fa-times"></i> </button>
</div>
<div class="modal-body">
  <div id="alt_msg"></div>
  <form id="createSchedule" id="createSchedule" class="ajax-form" method="post">
  <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
  @csrf
  <div class="row">
    <div class="col-xs-6 col-md-6">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize mt-0 font-weight-normal" style=""> <span class="font-weight-600">Scheduled Date :</span> <?php echo $schedule_date ?> </label>
      </div>
    </div>
    <div class="col-xs-6 col-md-6">
      <div class="form-group chooseCandidate bootstrap-timepicker timepicker">
        <label class="d-block text-dark text-capitalize mt-0 font-weight-normal" style=""> <span class="font-weight-600">Scheduled  Time : </span> <?php echo $schedule_from_time ?> </label>
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
        <label class="d-block text-dark text-capitalize font-weight-normal" style=""> <span class="mt-0 font-weight-600">Candidate : </span> <?php echo ucwords($full_name);  ?> </label>
      </div>
    </div>
    <div class="col-xs-12 col-md-6">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize font-weight-normal" style=""> <span class="mt-0 font-weight-600">Interviewers : </span>
        <?php 
			  		$i=0;
			  		if(!empty(@$interviewers)){
						foreach(@$interviewers as $intVal){
							
							echo $intVal->name;
							if($i>0){
								echo '&nbsp;|&nbsp;';
							}
							$i++;
						}
					}	
			  ?>
        </label>
      </div>
    </div>
  </div>
  <div class="row mt-0">
    <div class="col-xs-12 col-md-12">
      <div class="form-group">
        <label class="custom-label-color mt-0 font-weight-600 mb-0">Feedback</label>
        <div class="container pl-0 ml-0">
          <div class="radio-tile-group">
            <?php 

//echo $interview_detail->feedback.'abu'; 

$pending_first 		= '';
$pending_second 	= '';	
$pending_chk		=	'';

$shortlisted_first 	= '';
$shortlisted_second = '';		
$shortlisted_chk	=	'';

$reschedule_first 	= '';
$reschedule_second 	= '';	
$reschedule_chk		=	'';

$rejected_first 	= '';
$rejected_second 	= '';
$rejected_chk		=	'';

$other_first 		= '';
$other_second 		= '';	
$other_chk			=	'';
	
if($interview_detail->feedback ==0)
{
$pending_first 			=	'radio-tile-pending';
$pending_second 		=	'radio-tile-label-pending';
$pending_chk			=	'checked="checked"';
}			
else if($interview_detail->feedback ==1)
{
$shortlisted_first 		=	'radio-tile-shortlisted';
$shortlisted_second 	=	'radio-tile-label-shortlisted';
$shortlisted_chk		=	'checked="checked"';
}	
else if($interview_detail->feedback ==4)
{
$reschedule_first 		=	'radio-tile-reschedule';
$reschedule_second 		=	'radio-tile-label-reschedule';
$reschedule_chk			=	'checked="checked"';
}
else if($interview_detail->feedback ==2)
{
$rejected_first 		=	'radio-tile-rejected';
$rejected_second 		=	'radio-tile-label-rejected';
$rejected_chk			=	'checked="checked"';
}
else
{
$other_first 			=	'radio-tile-other';
$other_second 			=	'radio-tile-label-other';
$other_chk				=	'checked="checked"';
}

?>
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
          <input type="checkbox" name="Checkboxes5"/ checked="checked">
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
  <input type="hidden" name="application_id" id="application_id" value="<?php echo @$application_id; ?>" />
  <input type="hidden" name="interview_id" id="interview_id" value="<?php echo @$interview_id; ?>" />
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-light-danger btn-sm mr-2 p-1" data-dismiss="modal">@lang('app.close')&nbsp;<i class="fa fa-circle fa-sm"></i></button>
  <button type="button" class="btn btn-light-success btn-sm save-schedule p-1">Add Feedback &nbsp;<i class="fa fa-spinner fa-sm"></i></button>
</div>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>
 

 // Save Interview Schedule
  $('.save-schedule').click(function () {
		
		$.easyAjax({
						url: '{{route('product_company.interview-schedule.store_feedbackfrm')}}',
						container: '#createSchedule',
						type: "POST",
						data: $('#createSchedule').serialize(),
						success: function (response) {
						   if(response.status == 'success')
						   {
							   $('#scheduleDetailModal_feedback').modal('hide');	
							   //table._fnDraw();
						   }
						   else
						   { 
								$('#alt_msg').html('<div class="alert alert-warning alert-dismissible"><a href="Javascript:void(0);" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Warning!</strong>'+response.message+'</div>');			
						   }
						}
			})
																			
  })

	
</script>
