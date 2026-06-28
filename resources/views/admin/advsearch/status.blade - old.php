<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
<style>
    .form-group label {margin-top: 0em!important;}
</style>
<div class="modal-header">
<h4 class="modal-title"><i class="icon-plus"></i>Application Status Updates</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<div class="modal-body">
<?php 
		//echo '<pre>'; print_r($candidates); exit; 
?>
    <form id="createSchedule" class="ajax-form" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    	<input type="hidden" name="application_id" id="application_id" value="<?php echo @$application[0]->id ?>" />
	    <div class="form-body">
            <div class="row">
                <div class="col-xs-6 col-md-4">
                    <div class="form-group">
                        <label class="d-block">Candidate Name</label>
                        <label class="d-block" style="border: 1px solid #f3f0f0; padding: .5em .2em; border-radius: 7px;"><?php echo @$application[0]->full_name ?></label>
                        <!-- <?php echo @$application[0]->full_name ?> -->
                    </div>
                </div>
                <div class="col-xs-6 col-md-4">
                    <div class="form-group">
                        <label class="d-block">Team</label>
                        <select class="select2 m-b-10 form-control select2-multiple " multiple="multiple" data-placeholder="Choose Team" name="team[]">
                            @foreach($users as $emp)
                                <option value="{{ $emp->id }}">{{ ucwords($emp->name) }} @if($emp->id == $user->id)
                                        (@lang('app.you')) @endif</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xs-5 col-md-4">
                    <div class="form-group">
                        <label>Interview Status</label>
                        <select name="masterStatus" id="masterStatus" class="form-control" onchange="getMasterChild(this.value)">
                            <option value="" >Select Status</option>
                            <?php 
                                    $mstatus = '';
                                    $mstatus = @$application[0]->full_name; 
                                    if(!empty($interviewStatus)){
                                        foreach($interviewStatus as $intStatus){
                                            $sel    =   '';
                                            if($application[0]->master_status_id == $intStatus->id ){
                                                //$sel  =   'selected="selected"';  
                                            }
                                            echo '<option value="'.$intStatus->id.'"   '.$sel.'>'.$intStatus->name.'</option>';
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
                        <label>@lang('modules.interviewSchedule.scheduleDate')</label>
                        <input type="text" name="scheduleDate" id="scheduleDate" value="" class="form-control">
                    </div>
                </div>

                <div class="col-xs-5 col-md-6">
                    <div class="form-group chooseCandidate bootstrap-timepicker timepicker">
                        <label>@lang('modules.interviewSchedule.scheduleTime')</label>
                        <input type="text" name="scheduleTime" id="scheduleTime" class="form-control">
                    </div>
                </div>
            </div>
			
			<div class="row">	
				
                <div class="col-xs-5 col-md-6">
                    <div class="form-group" id="child" style="display:none">
                        <label>Interview Status Level</label>
                        	<select name="childStatus" id="childStatus" class="form-control">
								<option value=""></option>
							</select>
                    </div>
                </div>				
            </div>
			
			<div class="row">
                <div class="col-xs-12 col-md-12 ">
                    <div class="form-group">
                        <label>@lang('modules.interviewSchedule.comment')</label>
                        <textarea type="text" name="comment" id="comment" placeholder="@lang('modules.interviewSchedule.comment')" class="form-control"></textarea>
                    </div>
                </div>
            </div>
			<div class="row">
                <div class="col-xs-12 col-md-12 text-center">
                    <div class="form-group" id="status_msg" style="color:#218838; text-align:center; margin:10px; font-weight:bold;">
                    </div>
                </div>
            </div>
           
        </div>
    </form>

</div>
<div class="modal-footer">    
    <button type="button" class="btn btn-light-danger btn-sm mr-2" data-dismiss="modal">@lang('app.close')&nbsp;<i class="fa fa-circle fa-sm"></i></button>
    <button type="button" class="btn btn-light-success btn-sm save-schedule">@lang('app.submit')&nbsp;<i class="fa fa-spinner fa-sm"></i></button>
</div>

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
