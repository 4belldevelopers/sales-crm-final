<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<div class="modal-header">
  <h4 class="modal-title">Schedule</h4>
  <h4 class="modal-title ajaxmsg"></h4>
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> <i class="fa fa-times"></i> </button>
</div>
<div class="modal-body">
  <form id="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="application_id" id="application_id" value="<?php echo @$candidate->id; ?>" />
    <input type="hidden" name="process_id" id="process_id" value="<?php echo @$process_id; ?>" />
    <div class="form-body">
      <div class="row">
        <div class="col-xs-12 col-md-6">
          <div class="form-group">
            <label class="d-block">Application name</label>
            <input type="text" name="application_name" id="application_name" class="form-control" value="<?php echo ucwords(@$candidate->full_name); ?>" readonly="">
          </div>
        </div>
        <div class="col-xs-12 col-md-6">
          <div class="form-group">
            <label class="d-block">Team Member</label>
            <select class="select2 m-b-10 form-control select2-multiple " multiple="multiple"
                                data-placeholder="@lang('modules.interviewSchedule.chooseEmployee')" name="employee[]">
              
                            @foreach($users as $emp)
                                
              <option value="{{ $emp->id }}">{{ ucwords($emp->name) }} @if($emp->id == $user->id)
              (@lang('app.you')) @endif</option>
              
                            @endforeach
                        
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 col-md-6">
          <div class="form-group">
            <label>@lang('modules.interviewSchedule.scheduleDate')</label>
            <input type="text" name="scheduleDate" id="scheduleDate" value="{{$scheduleDate}}" class="form-control">
          </div>
        </div>
        <div class="col-xs-12 col-md-6">
          <div class="form-group chooseCandidate bootstrap-timepicker timepicker">
            <label>@lang('modules.interviewSchedule.scheduleTime')</label>
            <input type="text" name="scheduleTime" id="scheduleTime" class="form-control">
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
    </div>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-light-danger btn-sm ml-2" data-dismiss="modal">@lang('app.close')&nbsp;<i class="fa fa-circle fa-sm"></i></button>
  <button type="button" class="btn btn-light-success btn-sm save-schedule">@lang('app.submit')&nbsp; <i class="fa fa-spinner fa-sm"></i> </button>
  <div class="interviewProcessLoader"></div>
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

    // Save Interview Schedule
    $('.save-schedule').click(function () {
		
		var baseurl 		= "{{ asset('assets/images/AjaxLoader.gif') }}"; 
        $.easyAjax({
            url: '{{route('staffing_company.cvtracker.schedulestore')}}',
            container: '#createSchedule',
            type: "POST",
            data: $('#createSchedule').serialize(),
			beforeSend: function() {
									$('.interviewProcessLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
			},
            success: function (response) {
               
			   $('.ajaxmsg').html('<div style="font-weight:bold; text-align:center; margin:5px; background:#009900; color:#FFFFFF">Interview process saved successfully</div>');
			   	//if(response==1)
				//
					//$('.ajaxmsg').html('<div style="font-weight:bold; text-align:center; margin:5px; background:#009900; color:#FFFFFF">Interview process saved successfully</div>');	
				//}
				$('.interviewProcessLoader').html('');
				//$('.ajaxmsg').html('');	
			   // if(response.status == 'success'){
                //    window.location.reload();
               // }
            }
        })
    })
</script>
<style>
.modal-backdrop {
			position: fixed2 !important;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			z-index: 1040;
			background-color: #000;
		}
</style>
