<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<style>
.form-group label {margin-top: 0em!important;}
.custm-style{border: 1px solid #dedcdc; padding: .5em .2em; border-radius: 4px;}
.custom-label-color{font-weight: 900;color: #444444;}
.fresh-padding{padding: 4px 38px;height: 30px;font-size: 15px;}
.resechudule-padding{padding: 4px 5px;height: 30px;font-size: 15px;}
.candidate-name{color: #060606!important;font-size: 14px!important;}

/*Radio btn css*/
.radio-tile-group{display: flex;flex-wrap: wrap;justify-content: left;}
.radio-tile-group .input-container{position: relative;height: 1rem;margin: 0.3rem;width: 5.8em;}
.radio-tile-group .input-container .radio-button{opacity: 0;position: absolute;top: 0;left: 0;height: 100%;width: 100%;margin: 0;
cursor: pointer;height: 100vh;}
.radio-tile-group .input-container .radio-tile{display: flex;align-items: center;justify-content: center;
  width: 100%;height: 100%;border: 2px solid #079ad9;border-radius: 3px;padding: 1rem;transition: transform 300ms ease;}
.radio-tile-group .input-container .radio-tile-label{text-align: center;text-transform: capitalize;color: #079ad9;
font-size: 12px;margin:0;}
.radio-tile-group .input-container .radio-button:checked + .radio-tile{background-color: #079ad9;border: 2px solid #079ad9;
  color: white;transform: scale(1.1, 1.1);}
.radio-tile-group .input-container .radio-button:checked + .radio-tile .radio-tile-label {color: white;background-color: #079ad9;margin:0;}
.modal .modal-header{outline:0;}
/*Radio btn css end*/

</style>

<!-- Editable text -->
<script>
    
</script>
<!-- Editable text end -->

<div class="modal-header line-height-0 modal-htcp" id="debug" contenteditable data-name="custom-text">
<h4 class="modal-title">Subject : Interview stages | 4Bell Technology | Web Developer | Noida&nbsp;
    <a href="javascript:void(0);">
        <i class="fa fa-pencil text-primary"></i>
    </a>
</h4>
<button type="button" class="close btn-sm mr-0" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<div class="modal-body pb-0">
<?php 
		//echo '<pre>'; print_r($candidates); exit; 
?>
    <form id="createSchedule" class="ajax-form" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    	<input type="hidden" name="application_id" id="application_id" value="<?php echo @$application[0]->id ?>" />
	    <div class="form-body">
            <!-- Assessment Stage, Sub Status & Interview Type -->
            <div class="row">
                <div class="col-xs-6 col-md-4">
                    <div class="form-group">
                        <label class="custom-label-color">Assessment Stage</label>
                        <select name="masterStatus" id="masterStatus" class="form-control">
                            <option value="" >Select Stage</option>
                            <option value="" >Stage - I</option>
                            <option value="" >Stage - II</option>
                            <option value="" >Stage - III</option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-6 col-md-4">
                    <div class="form-group">
                        <label class="custom-label-color">Sub Status</label>
                        <select name="masterStatus" id="masterStatus" class="form-control">
                            <option value="" >Select Sub Stage</option>
                            <option value="" >Sourcing - I</option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-5 col-md-4 col-form-label padding-0">
                    <div class="form-group">
                        <label class="checkbox checkbox-outline checkbox-success custom-label-color mb-0">
                            Interview Type
                        </label>
                        <div class="container padding-0">
                          <div class="radio-tile-group">
                            <div class="input-container mt-1" style="width:10em;">
                              <input id="fresh" class="radio-button" type="radio" name="radio1" checked="checked" />
                              <div class="radio-tile">
                                <label for="fresh" class="radio-tile-label">Fresh</label>
                              </div>
                            </div>
                            <div class="input-container mt-1" style="width:10em;">
                              <input id="reschedule" class="radio-button" type="radio" name="radio1" />
                              <div class="radio-tile">
                                <label for="reschedule" class="radio-tile-label">Re-Schedule</label>
                              </div>
                            </div>                            
                          </div>
                        </div>
                    </div>
                </div>
            </div>
			
            <!-- Interview Status Level -->
            <div class="row mt-2">                  
                <div class="col-xs-5 col-md-12">
                    <div class="form-group" id="child" style="display:none">
                        <label class="custom-label-color">Interview Status Level</label>
                            <select name="childStatus" id="childStatus" class="form-control">
                                <option value=""></option>
                            </select>
                    </div>
                </div>              
            </div>

            <!-- Schedule Date & Start Time/End Time-->
			<div class="row mt-2">
                <div class="col-xs-6 col-md-5">
                    <div class="form-group">
                        <label class="custom-label-color">
                            @lang('modules.interviewSchedule.scheduleDate')
                        </label>
                        <!-- <label>Scheduled Date</label> -->
                        <input type="text" name="scheduleDate" id="scheduleDate" value="" class="form-control" placeholder="Enter Your Date">
                    </div>
                </div>
                <div class="col-xs-6 col-md-7 pl-8">
                    <div class="row">
                        <div class="col-xs-5 col-md-5 padding-0">
                            <div class="form-group chooseCandidate bootstrap-timepicker timepicker">
                                <label class="custom-label-color">Start Time</label>
                                <input type="time" name="scheduleTime" class="form-control" placeholder="Time">
                            </div>
                        </div>
                        <div class="col-xs-1 col-md-1 mt-3 padding-0 text-center">
                            <br><span class="font-weight-600">To</span>
                        </div>
                        <div class="col-xs-5 col-md-5 padding-0">
                            <div class="form-group chooseCandidate bootstrap-timepicker timepicker">
                                <label class="custom-label-color">End Time</label>
                                <input type="time" name="scheduleTime" class="form-control" placeholder="Reschedule Time">
                            </div>
                        </div>    
                    </div>
                </div>    
            </div>

            <!-- Interview Mode & Mention Link -->
            <div class="row mt-2">
                <div class="col-xs-6 col-md-5">
                    <div class="form-group">
                        <label class="checkbox checkbox-outline checkbox-success custom-label-color">Interview Mode</label>
                        <div class="container padding-0">
                          <div class="radio-tile-group">
                            <div class="input-container">
                              <input id="Telecon" class="radio-button" type="radio" name="radio" checked="checked" />
                              <div class="radio-tile">
                                <label for="Telecon" class="radio-tile-label">Telecon</label>
                              </div>
                            </div>

                            <div class="input-container">
                              <input id="video" class="radio-button" type="radio" name="radio" />
                              <div class="radio-tile">
                                <label for="video" class="radio-tile-label">Video</label>
                              </div>
                            </div>

                            <div class="input-container">
                              <input id="f2f" class="radio-button" type="radio" name="radio" />
                              <div class="radio-tile">
                                <label for="f2f" class="radio-tile-label">F2F</label>
                              </div>
                            </div>

                            <div class="input-container">
                              <input id="other" class="radio-button" type="radio" name="radio" />
                              <div class="radio-tile">
                                <label for="other" class="radio-tile-label">Other</label>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-md-7">
                    <div class="form-group">
                        <label class="custom-label-color">Mention Link</label>
                        <input type="text" name="link" placeholder="Ex: https://meet.google.com" class="form-control">
                    </div>
                    <!-- <div class="form-group">
                        <label class="custom-label-color">Mention Location</label>
                        <input type="text" name="link" placeholder="Ex: Pune/Delhi/Noida" class="form-control">
                    </div> -->
                </div>
            </div>		

            <!-- Interviewers & Add Email Address -->
            <div class="row mt-2">
                <div class="col-xs-6 col-md-5">
                    <div class="form-group">
                        <label class="custom-label-color">Interviewers</label>
                        <input type="text" name="interviewers" placeholder="Select Interviewers" class="form-control">
                    </div>
                </div>
                <div class="col-xs-6 col-md-7">
                    <div class="form-group">
                        <label class="custom-label-color">Add Email Address</label>
                        <input type="email" name="email" placeholder="Ex: example@gmail.com" class="form-control">
                    </div>
                </div>
            </div>  

            <!-- Candidate -->
            <div class="row mt-2">                
                <div class="col-xs-12 col-md-12">
                    <div class="form-group">
                        <!-- <label class="d-block">Candidate Name</label> -->
                        <label class="d-block text-dark text-capitalize custom-label-color">
                            <span class="">Candidate :</span> 
                            <span class="font-weight-400"><?php echo @$application[0]->full_name ?></span>
                        </label>
                    </div>
                </div>
            </div>
			
            <!-- Message -->
			<div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="form-group">
                        <label class="custom-label-color">Message</label>
                            <textarea type="text" name="message" id="message" placeholder="Write a Message..." rows="3" class="form-control"><?php echo @$message; ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Send email notifaction to everyone -->
            <div class="row">
                <div class="form-group row">
                    <div class="col-12 col-form-label">
                        <div class="checkbox-inline">
                            <label class="checkbox checkbox-primary mb-0 d-flex justify-content-space-around align-items-center">
                                <input type="checkbox" name="Checkboxes5"/ checked="checked">
                            <span class="mr-1"></span>
                                Send email notifaction to everyone.
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit successfully message -->
			<div class="row mt-2">
                <div class="col-xs-12 col-md-12 text-center">
                    <div class="form-group" id="status_msg" style="color:#218838; text-align:center; margin:10px; font-weight:bold;">
                    </div>
                </div>
            </div>           
        </div>
    </form>

</div>
<div class="modal-footer p-2">    
    <button type="button" class="btn btn-light-danger btn-sm mr-2 p-1" data-dismiss="modal">@lang('app.close')&nbsp;
        <i class="fa fa-circle fa-sm"></i>
    </button>
    <button type="button" class="btn btn-light-success btn-sm save-schedule p-1">@lang('app.submit')&nbsp;
        <i class="fa fa-spinner fa-sm"></i>
    </button>
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
    $('#scheduleDate').bootstrapMaterialDatePicker
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
					var url 			= "{{ route('admin.cvtracker.getchild',':id') }}";
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
    $('.save-schedule').click(function () {
        $.easyAjax({
            url: '{{route('admin.cvtracker.statusUpdate')}}',
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
</script>
