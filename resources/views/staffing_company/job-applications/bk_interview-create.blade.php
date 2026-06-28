<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
<style>
body
{
   margin: 0!important;
   padding: 0!important;
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
/*Radio btn css*/
.radio-tile-group
{
   display: flex;
   flex-wrap: wrap;
   justify-content: left;
}
.radio-tile-group .input-container
{
   position:relative;
   height:2.3rem;
   width:5.5rem;
   margin:0em 0.3rem;
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
.radio-tile-group .input-container .radio-tile
{
   display: flex;
   flex-direction: column;
   align-items: center;
   justify-content: center;
   width: 100%;
   height: 100%;
   border: 2px solid #079ad9;
   border-radius: 3px;
   /*padding: .9rem;*/
   transition: transform 300ms ease;
   padding: 0px;
}
.radio-tile-group .input-container .radio-tile-label
{
   text-align: center;
   text-transform: capitalize;
   color: #079ad9;
   font-size: 12px;
   margin:0;
}
.radio-tile-group .input-container .radio-button:checked + .radio-tile
{
   background-color: #079ad9;
   border: 2px solid #079ad9;
   col`or: white;
   transform: scale(1.1, 1.1);
}
.modal-header
{
   outline: 0;
   padding: 15px 0px 15px 0px!important;
}
/*.radio-tile-group .input-container .radio-button:checked + .radio-tile .icon svg
{
   fill: white;
   background-color: #079ad9;
}*/
.radio-tile-group .input-container .radio-button:checked + .radio-tile .radio-tile-label
{
   color: white;
   background-color: #079ad9;
   margin:0;
   line-height: 0px;
}
.modal-header
{
   outline: 0;
   padding: 15px 0px 15px 0px!important;
}
.error
{
   color:#FF0000;
}
/*Checkbox Border*/
.checkbox.checkbox-primary > span
{
   border: 1px solid #b9acac;
}
/*Checkbox Border End*/

/*Dropdown spacing*/
.select2-container--default .select2-selection--multiple .select2-selection__rendered li
{
   margin: 1px 1px;
   padding: 0px 2px;
}
.select2-container--default .select2-selection--multiple
{
   border-color: #d2d6de;
   height: 38px!important;
   overflow-y: scroll;
}
.select2-container:focus .select2-search--inline:focus .select2-search__field:focus
{
   border: 0px solid transparent!important;
}
/*Dropdown spacing end*/
.input-group > .custom-select:not(:last-child), .input-group > .form-control:not(:last-child)
{
   border-radius: 0.25rem;
}

/*Modal default overflow-y*/
/*#scheduleDetailModal
{
   overflow-y: hidden;
}*/
#exampleModal .modal-xl
{
   transform: translate(0, 0);
   overflow-y: auto;
   height: -webkit-fill-available;
   height: 100vh!important;
   overflow-x: hidden;
   position: absolute!important;
   /*left: -11%!important;*/
   left: -2%!important;
}
.modal-content
{
   /*box-shadow: 0 0.5rem 1rem rgb(0 0 0 / 50%);*/
   border: none;
   /* border-radius: 0; */
}
@media (min-width: 992px)
{
   .modal-xl
   {
      /*max-width: 800px;*/
      max-width: 100vw;
      /*width: 1140px;*/
      width: 990px;
   }
}   
/*Modal default overflow-y End*/


/*Send Email Enquiry*/
#exampleModal
{
   padding-right: 0px!important;
   overflow: inherit!important;
}
.w-10
{
   width: 10%;
}
.w-90
{
   width: 90%;
}
.dropdown-custom-height
{
   height: calc(1.7rem + 2px)!important;
}

/*Editor*/
a{
  cursor:pointer;
}

#editor
{
   min-height: 300px;
   height: 300px;
   /*overflow-y: auto;*/
   overflow-y: scroll;
   padding: .6em;
   margin-top: 0px;
   resize: vertical;
   outline: none;
   border: 1px solid #e2e2e2;
   border-top: none;
}

.toolbar
{
   text-align: left;
   border: 1px solid #e2e2e2;
   border-bottom: none;
   background: #e2e2e2;
   padding: .9em;
}

.toolbar a,
.fore-wrapper,
.back-wrapper {
border: 1px solid #AAA;
background: #FFF;
font-family: "Poppins";
color: black;
padding: 5px;
box-shadow: 0px 1px 0px #ccc;
}

.toolbar a:hover, .fore-wrapper:hover, .back-wrapper:hover {
  background: #f2f2f2;
  border-color: #8c8c8c;
}

a[data-command='redo'],a[data-command='strikeThrough'],a[data-command='justifyFull'],a[data-command='insertOrderedList'],a[data-command='outdent'],a[data-command='p'],a[data-command='superscript']{
  margin-right:5px;
  border-radius:0 3px 3px 0;
}

a[data-command='undo'],.fore-wrapper,a[data-command='justifyLeft'],a[data-command='insertUnorderedList'],a[data-command='indent'],a[data-command='h1'],a[data-command='subscript']{
  border-radius:3px 0 0 3px;
}

a.palette-item {
  height: 1em;
  border-radius: 3px;
  margin: 2px 1px;
  width: 1em;
}

.fore-palette,
.back-palette {
  display: none;
}

.fore-wrapper,
.back-wrapper {
  display: inline-block;
  cursor: pointer;
}

.fore-wrapper:hover .fore-palette,
.back-wrapper:hover .back-palette {
  display: block;
  float: left;
  position: absolute;
  padding: 3px;
  width: 10em;
  background: #FFF;
  border: 1px solid #DDD;
  box-shadow: 0 0 5px #CCC;
  height: 4.4em;
}

.fore-wrapper:hover .fore-palette {
  left: 34%;
  top: 42px;
}

.back-wrapper:hover .back-palette {
  left: 36%;
  top: 42px;
}

.fore-palette a,
.back-palette a {
  background: #FFF;
  margin-bottom: 2px;
}
/*Editor End*/



/*Send Email Enquiry End*/
</style>
<?php 
   $scheduleDate				= $schedule_from_time =	$schedule_to_time =	$url_link	=	$full_name= $message = $master_status_name =  '';	
   $interview_type_fresh       = '';
   $interview_type_re_schedule = '';
   
   
   $interview_type_fresh 				= 'checked="checked"';	
   
   
   $time =	$scheduleDate = $schedule_from_time = $schedule_to_time = $url_link = $other_email = $message = $master_status_name = $interview_detail_stage =	'';
   if($feedback_id ==4)
   {
		$time 					   =	strtotime(@$interview_detail->schedule_date);
		$scheduleDate 			   =	date('Y-m-d',$time);
		$schedule_from_time		   =	@$interview_detail->schedule_from_time;
		$schedule_to_time		   =	@$interview_detail->schedule_to_time;	
		$url_link				   =	@$interview_detail->url_link;
		$other_email			   =	@$interview_detail->other_email;
		$message				   =	@$interview_detail->message;
		$master_status_name		   =	@$interview_detail->master_status_name;
		$interview_detail_stage    =	$interview_detail->stage;
   }
      //APPLICATION DATA 
      $full_name			=   $client_name	= $job_title	=  $city = $company_id = $master_status_id =  ''; 
      $full_name			=	@$application->full_name;
      $client_name		    =	@$application->client_name;
      $job_title			=	@$application->job_title;
      $city				    =	@$application->city;
      $company_id			=	@$application->company_id;
      $master_status_name	=	@$application->master_status_name;
      $master_status_id	    =	@$application->master_status_id;
      $job_id				=	@$application->job_id;
      
?>
<form id="createSchedule" name="createSchedule" class="ajax-form" method="post">
  <div class="modal-header" id="debug" contenteditable data-name="custom-text">
    <h4 class="modal-title"> <i class="fa fa-calendar"></i>&nbsp;Interview Schedule </h4>
    <button type="button" class="close btn-sm mr-1"data-dismiss="modal" aria-hidden="true"> <i class="ti-close text-dark"></i> </button>
  </div>
  <div class="modal-body pt-1">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="application_id" id="application_id" value="<?php echo @$application->id ?>" />
    <div class="form-body">
      <div class="row">
        <div class="col-xs-5 col-md-3 col-form-label padding-0">
          <div class="form-group">
            <label class="custom-label-color mb-0 pl-1">Interview Type</label>
            <div class="container padding-0 mt-1">
              <div class="radio-tile-group">
                <div class="input-container pl-1" style="">
                  <input id="interview_type" class="radio-button" name="interview_type" type="radio"  value="1"
                           <?php echo @$interview_type_fresh; ?> />
                  <div class="radio-tile">
                    <label for="radio" class="radio-tile-label">Fresh</label>
                  </div>
                </div>
                <div class="input-container" style="">
                  <input id="reschedule" class="radio-button" name="interview_type"  type="radio"  value="2"
                           <?php echo @$interview_type_re_schedule; ?> />
                  <div class="radio-tile">
                    <label for="radio" class="radio-tile-label">Re-Schedule</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-md-3">
          <div class="form-group">
            <label for="schedule_date">@lang('modules.interviewSchedule.scheduleDate') & Time</label>
            <div class='input-group date align-items-center' id='datetimepicker1'>
              <input type='datetime-local' name="schedule_date" id="schedule_date" class="form-control mb-0" >
              <span class="input-group-addon d-none" style="position: absolute;right: 3px;"> <span class="glyphicon glyphicon-calendar"></span> <span class="fa fa-calendar"></span> </span> </div>
          </div>
        </div>
        <div class="col-xs-6 col-md-3">
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
        <div class="col-xs-6 col-md-3">
          <div class="form-group sub_status" style="display:none">
            <label class="custom-label-color">Sub Status</label>
            <select name="child_status" id="child_status" class="form-control">
              <option value="" >Select Sub-Status</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-xs-6 col-md-3">
          <div class="form-group">
            <label class="custom-label-color">Interview Mode</label>
            <select name="interview_mode" id="interview_mode" onchange="getInterviewModeChild(this.value, <?php echo $job_id; ?>);" class="form-control">
              <option>Select Interview Mode</option>
              <?php 
					 		if(!empty($interview_mode)){
								foreach($interview_mode as $int_mode_val){
									echo	'<option value="'.@$int_mode_val->id.'">'.@$int_mode_val->name.'</option>';
								}
							}		 
					 ?>
            </select>
          </div>
        </div>
        <div class="col-xs-6 col-md-3">
          <div class="form-group">
            <label class="custom-label-color">Interview Sub Mode</label>
            <div style="width:150px" id="childInterview">&nbsp;</div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <label for="Interviewers" class="custom-label-color">Interviewers</label>
            <select name="interviewers[]" id="Interviewers" class="select2 m-b-10 form-control select2-multiple" multiple="multiple" data-placeholder="Choose Interviewers" >
              <?php 
						if(!empty($users)){
							foreach($users as $cval){					
							$sel			=	$interviewers = '';					
						?>
              <option value="<?php echo @$cval->id; ?>" <?php echo @$sel; ?> ><?php echo @$cval->name;  ?></option>
              <?php 
						}}
						?>
            </select>
          </div>
        </div>
      </div>
      <div class="row mt-1">
        <div class="col-xs-12 col-md-12 text-center">
          <div class="form-group" id="status_msg" style="color:#218838; text-align:center; margin:0px; font-weight:700;"> </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light-danger btn-sm mr-2 p-1" data-dismiss="modal"> <i class="fa fa-circle fa-sm"></i>&nbsp;@lang('app.cancel') </button>
    <button type="button" class="btn btn-light-success btn-sm mr-2 p-1 save-schedule"> <i class="fa fa-save fa-sm"></i>&nbsp;Save & Submit </button>
    <button type="button" class="btn btn-light-warning btn-sm p-1" data-toggle="modal" data-target="#exampleModal"> <i class="fa fa-envelope fa-sm"></i>&nbsp;Send Email Invites </button>
  </div>
  <!-- Send Mail Invites -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Availability</h5>
          <button type="button" class="close mr-0" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12 px-2">
              <div class="form-group">
                <div class="row mx-auto py-1">
                  <div class="title_section w-10">
                    <label class="custom-label-color">Template</label>
                  </div>
                  <div class="desc_section w-90">
                    <select name="interview_mode" class="form-control dropdown-custom-height">
                      <option>Default Candidate Availability Request Message</option>
                      <option>Demo-1</option>
                      <option>Demo-2</option>
                      <option>Demo-3</option>
                    </select>
                  </div>
                </div>
                <div class="row mx-auto py-1">
                  <div class="title_section w-10">
                    <label class="custom-label-color">From</label>
                  </div>
                  <div class="desc_section w-90">
                    <input type="text" name="message_from" id="Message_from" placeholder="Ex: example@test.com" class="form-control dropdown-custom-height" value="example@test.com">
                  </div>
                </div>
                <div class="row mx-auto py-1">
                  <div class="title_section w-10">
                    <label class="custom-label-color">To</label>
                  </div>
                  <div class="desc_section w-90">
                    <input type="text" name="message_to" id="Message_To" placeholder="Ex: demo@test.com" class="form-control dropdown-custom-height" value="demo@test.com">
                  </div>
                </div>
                <div class="row mx-auto py-1">
                  <div class="title_section w-10">
                    <label class="custom-label-color">CC</label>
                  </div>
                  <div class="desc_section w-90">
                    <input type="text" name="team_members" id="Team_Member" placeholder="Ex: example2@test.com" class="form-control dropdown-custom-height">
                  </div>
                </div>
                <div class="row mx-auto py-1">
                  <div class="title_section w-10">
                    <label class="custom-label-color">Subject</label>
                  </div>
                  <div class="desc_section w-90">
                    <input type="text" name="subject" id="Subject" placeholder="Ex: Re-Schedule Interview" class="form-control dropdown-custom-height">
                  </div>
                </div>
                <div class="row mx-auto py-1">
                  <div class="title_section w-10">
                    <label class="custom-label-color">Body</label>
                  </div>
                  <div class="desc_section w-90">
                    <div class="form-group">
                      <!-- <textarea class="form-control" id="job_description" name="job_description" rows="8" placeholder="Write a Message..." style="padding-bottom: .9em;"></textarea> -->
                      <link href='https://fonts.googleapis.com/css?family=Dosis|Candal' rel='stylesheet' type='text/css'>
                      <div class="toolbar"> <a href="javascript:void(0);" data-command='undo'><i class='fa fa-undo'></i></a> <a href="javascript:void(0);" data-command='redo'><i class='fa fa-repeat'></i></a> <a href="javascript:void(0);" data-command='bold'><i class='fa fa-bold'></i></a> <a href="javascript:void(0);" data-command='italic'><i class='fa fa-italic'></i></a> <a href="javascript:void(0);" data-command='underline'><i class='fa fa-underline'></i></a> <a href="javascript:void(0);" data-command='strikeThrough'><i class='fa fa-strikethrough'></i></a> <a href="javascript:void(0);" data-command='justifyLeft'><i class='fa fa-align-left'></i></a> <a href="javascript:void(0);" data-command='justifyCenter'><i class='fa fa-align-center'></i></a> <a href="javascript:void(0);" data-command='justifyRight'><i class='fa fa-align-right'></i></a> <a href="javascript:void(0);" data-command='justifyFull'><i class='fa fa-align-justify'></i></a> <a href="javascript:void(0);" data-command='indent'><i class='fa fa-indent'></i></a> <a href="javascript:void(0);" data-command='outdent'><i class='fa fa-outdent'></i></a> <a href="javascript:void(0);" data-command='insertUnorderedList'><i class='fa fa-list-ul'></i></a> <a href="javascript:void(0);" data-command='insertOrderedList'><i class='fa fa-list-ol'></i></a> <a href="javascript:void(0);" data-command='h1'>H1</a> <a href="javascript:void(0);" data-command='h2'>H2</a> <a href="javascript:void(0);" data-command='createlink'><i class='fa fa-link'></i></a> <a href="javascript:void(0);" data-command='unlink'><i class='fa fa-unlink'></i></a> <a href="javascript:void(0);" data-command='insertimage'><i class='fa fa-image'></i></a> <a href="javascript:void(0);" data-command='p'>P</a> <a href="javascript:void(0);" data-command='subscript'><i class='fa fa-subscript'></i></a> <a href="javascript:void(0);" data-command='superscript'><i class='fa fa-superscript'></i></a> </div>
                      <div id='editor' contenteditable>
                        <p>Write a Message...</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mx-auto py-1 d-none">
                  <div class="title_section w-10">
                    <label class="custom-label-color pt-0">Suggested times</label>
                  </div>
                  <div class="desc_section d-flex align-items-center"> <span class="bg-light-secondary font-weight-500 px-3 py-1">Suggest interview times for candidate</span> </div>
                </div>
                <div class="row mx-auto py-1">
                  <div class="title_section w-10">
                    <label class="custom-label-color">Attach File</label>
                  </div>
                  <div class="desc_section w-90">
                    <input type="file" name="attach_file" id="Attach-File" class="pl-1" multiple="multiple">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light-danger btn-sm p-1" data-dismiss="modal"> <i class="fa fa-circle"></i>&nbsp;Cancel </button>
          <button type="button" class="btn btn-light-secondary btn-sm p-1" data-toggle="modal" data-target="#createSchedule"> <i class="fa fa-chevron-left"></i>&nbsp;Back </button>
          <button type="button" class="btn btn-light-success btn-sm p-1"> <i class="fa fa-envelope"></i>&nbsp;Send Email </button>
        </div>
      </div>
    </div>
  </div>
  <!-- Send Mail Invites End -->
</form>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>
<!-- Editor -->
<script>
var colorPalette = ['000000', 'FF9966', '6699FF', '99FF66', 'CC0000', '00CC00', '0000CC', '333333', '0066FF', 'FFFFFF'];
var forePalette = $('.fore-palette');
var backPalette = $('.back-palette');

for (var i = 0; i < colorPalette.length; i++) {
  forePalette.append('<a href="#" data-command="forecolor" data-value="' + '#' + colorPalette[i] + '" style="background-color:' + '#' + colorPalette[i] + ';" class="palette-item"></a>');
  backPalette.append('<a href="#" data-command="backcolor" data-value="' + '#' + colorPalette[i] + '" style="background-color:' + '#' + colorPalette[i] + ';" class="palette-item"></a>');
}

$('.toolbar a').click(function(e) {
  var command = $(this).data('command');
  if (command == 'h1' || command == 'h2' || command == 'p') {
    document.execCommand('formatBlock', false, command);
  }
  if (command == 'forecolor' || command == 'backcolor') {
    document.execCommand($(this).data('command'), false, $(this).data('value'));
  }
    if (command == 'createlink' || command == 'insertimage') {
  url = prompt('Enter the link here: ','http:\/\/'); document.execCommand($(this).data('command'), false, url);
  }
  else document.execCommand($(this).data('command'), false, null);
});
</script>
<!-- Editor End -->
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
   
   function getMasterChild2(id)
   {
   
   
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
   
   // Save Interview Schedule
   
/*   $('.save-schedule-old').click(function () {
       $.easyAjax({
           url: '{{route('staffing_company.cvtracker.statusUpdate')}}',
           container: '#createSchedule',
           type: "POST",
           data: $('#createSchedule').serialize(),
           success: function (response) {
               if(response.status == 'success'){
                  	$('#status_msg').html("Application status update successfully.");
   	 table._fnDraw();
              }
           }
       })
   })
*/   

   $("#schedule_date").on('change', function() {
       $(this).valid();  
   });
   $("#createSchedule").validate({
   onfocusout: function(e) {
       		this.element(e);
   		},
   
   rules: {
   		masterStatus		: "required",
   		schedule_date		: "required",
   		schedule_from_time	: "required",
   		schedule_to_time	: "required",
   		Interviewers		: "required",						
   		
   	},
   messages: {
   		masterStatus		: "Please select Assessment Stage",
   		schedule_date		: "Please select Schedule Date",
   		schedule_from_time	: "Please select From Time",
   		schedule_to_time	: "Please select To",
   		Interviewers		: "Please select Interviewers",
   	 }
   });
   
   
   
   
   
   // Save Interview Schedule
   $('.save-schedule').click(function () {
      
	  		alert("call"); 
			return false;
			
  		  //if($("#createSchedule").valid())
   			//{
				
   
			   $.easyAjax({
					   			url			: '{{route('staffing_company.job-applications.store-schedule')}}',
					   			container	: '#createSchedule',
					   			type		: "POST",
					   			data		: $('#createSchedule').serialize(),
					   			success		: function (response) {
																		if(response.status == 'success')
																		{
																			table._fnDraw();
																			$('#scheduleDetailModal').modal('hide');
																		}	
					   												}
				   })
   
   
   			//}
   
   })
   
   
   
   
   
   
   
   
 
   
    function getInterviewModeChild(id, job_id)
      {
	  		//alert(id); 
		
          var id        	= id;
          var url       	= "{{ route('staffing_company.job-applications.getinterviewchild',':id') }}";
          url         		= url.replace(':id', id);
          var baseurl     	= "{{ asset('assets/images/Loading2.gif') }}"; 
          var token       	= "{{ csrf_token() }}";
          
           $.easyAjax({
                    	type	:	'POST',
                    	url		:	url,
                    	data	:	{'_token': token, '_method': 'POST', 'id' : id, 'job_id' : job_id},
                    	beforeSend	: function() {
                                  					$('#childInterview').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    	},
                    	success: function (response) 
                    	{
                      			$('#childInterview').html('');
                      //if(response.flag == 1)
                     // {
                        //$('#child').show();
                        		$('#childInterview').html(response.data);
                     // }
                     // else if(response.flag == 0)
                     // {
                        //alert(response.activeAppCounter.Sourcing); 
                        
                       // $('#bg_1').html(response.activeAppCounter.Sourcing);     // Sourcing
                      //  $('#bg_2').html(response.activeAppCounter.Screening);    // Screening
                       // $('#bg_3').html(response.activeAppCounter.Interview);    // Interview
                       // $('#bg_4').html(response.activeAppCounter.Negotiation);   // Negotiation
                       // $('#bg_5').html(response.activeAppCounter.Offered);      // Offered
                       // $('#bg_6').html(response.activeAppCounter.Joined);     // Joined
                       // $('#bg_7').html(response.activeAppCounter.Rejected);      // Rejected
                        
                         		table._fnDraw();
                     // }
                    },
                    complete: function (data) {
                        //$('#childStatus_'+current_application_id).html('');
                     }
          });
       }
	   
   
</script>
<!-- Date and Time Format -->
<!-- Date and Time Format End -->
