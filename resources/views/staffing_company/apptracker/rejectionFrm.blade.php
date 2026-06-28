<style>
.form-group label {margin-top: 0em!important;}
.custm-style{border: 1px solid #dedcdc; padding: .5em .2em; border-radius: 4px;}
.custom-label-color{font-weight: 900;color: #444444;}
.fresh-padding{padding: 4px 38px;height: 30px;font-size: 15px;}
.resechudule-padding{padding: 4px 5px;height: 30px;font-size: 15px;}
.candidate-name{color: #060606!important;font-size: 14px!important;}
.font-weight-600{font-weight: 600;}
.text-danger{color:#ff0202!important}

/*Radio btn css*/
.radio-tile-group{display: flex;flex-wrap: wrap;justify-content: left;}
.radio-tile-group .input-container1{position: relative;height:2.5rem;width: 9em;margin: 0.8rem;}
.radio-tile-group .input-container .radio-button{opacity: 0;position: absolute;top: 0;left: 0;height: 100%;width: 100%;margin: 0;
cursor: pointer;height:5vh;}
.radio-tile-group .input-container .radio-tile{display: flex;flex-direction: column;align-items: center;justify-content: center;
  width: 100%;height: 100%;border: 2px solid #079ad9;border-radius: 3px;padding:.7rem;transition: transform 300ms ease;}
.radio-tile-group .input-container .radio-tile-label{text-align: center;text-transform: capitalize;color: #079ad9;font-size: 11px;font-weight:600;margin:0;}
.radio-tile-group .input-container .radio-button:checked + .radio-tile{background-color:#ffe2e5;border: 1px solid #ff0d0d;
  color: white;transform: scale(1.1, 1.1);}
.radio-tile-group .input-container .radio-button:checked + .radio-tile .radio-tile-label {color: #f10e0e;background-color:#ffe2e5;margin:0;}
.modal .modal-header{outline:0;}
.modal-footer{margin: 0 auto;border-top: 1px solid #ffffff!important;}
/*Radio btn css end*/
</style>
<?php 
	//APPLICATION DATA 
	$full_name	=	$client_name=	$job_title=	$city=	$company_id=	$master_status_name=	$job_id=	$child_status_name=	$application_id=	$process_id=	$process_child_name=	$process_child_id =   ''; 
	
	$full_name				=	@$application->full_name;
	$client_name			=	@$application->client_name;
	$job_title				=	@$application->job_title;
	$city					=	@$application->city;
	$company_id				=	@$application->company_id;
	$master_status_name		=	@$application->master_status_name;
	$job_id					=	@$application->job_id;
	$application_id			=	@$application->id;
	$process_id				=	@$application->master_status_id;
	$process_id				=	@$application->master_status_id;
	$process_child_id		=	@$application->child_status_id;
	
	$process_child_name		=	@$childStatus->name;
	//echo '<pre>'; print_r($childStatus); 
?>
<div class="modal-header">
  <h6 class="modal-title"><i class="icon-plus"></i> <?php echo @$job_id; ?> | <?php echo @$job_title; ?> | <?php echo @$client_name; ?> | <?php echo @$city; ?> | <?php echo @$master_status_name; ?> | <?php echo @$process_child_name; ?> | <span class="bg-light-danger text-danger font-weight-600 px-2">Rejected</span> &nbsp;<a href="javascript:void(0);">
    <!-- <i class="flaticon2-pen text-primary"></i> -->
    </a></h6>
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<div class="modal-body pt-0 pb-0">
  <div id="alt_msg"></div>
  <form id="frm_rejection_resion" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="application_id"  value="<?php echo @$application_id; ?>">
    <input type="hidden" name="job_id"  value="<?php echo @$job_id; ?>">
    <input type="hidden" name="process_id"  value="<?php echo @$process_id; ?>">
    <input type="hidden" name="master_status_name"  value="<?php echo @$master_status_name; ?>">
    <input type="hidden" name="process_child_name"  value="<?php echo @$process_child_name; ?>">
    <input type="hidden" name="process_child_id"  value="<?php echo @$current_child_id; ?>">
    <input type="hidden" name="child_status_id"  value="<?php echo @$current_child_id; ?>">
    <div class="form-body">
      <div class="row mt-2">
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <!-- <label class="d-block">Candidate Name</label> -->
            <label class="d-block text-dark text-capitalize" style=""> <span class="font-weight-bold candidate-name">Candidate : </span> <?php echo $full_name; ?> </label>
          </div>
        </div>
      </div>
      <div class="row mt-0">
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <label class="custom-label-color font-weight-bold mt-0 mb-0">Select Reason</label>
            <div class="container pl-0">
              <div class="radio-tile-group pt-0">
                <div class="input-container input-container1">
                  <input id="pending" class="radio-button" type="radio" name="rejected_resion_name" value="Round Rejected" checked="checked" />
                  <div class="radio-tile">
                    <label for="pending" class="radio-tile-label">Round Rejected</label>
                  </div>
                </div>
                <div class="input-container input-container1">
                  <input id="shortlist" class="radio-button" type="radio"  name="rejected_resion_name" value="HR Rejected" />
                  <div class="radio-tile">
                    <label for="shortlist" class="radio-tile-label">HR Rejected</label>
                  </div>
                </div>
                <div class="input-container input-container1">
                  <input id="reschedule" class="radio-button" type="radio" 	name="rejected_resion_name" value="Not Suitable" />
                  <div class="radio-tile">
                    <label for="reschedule" class="radio-tile-label">Not Suitable</label>
                  </div>
                </div>
                <div class="input-container input-container1">
                  <input id="rejected" class="radio-button" type="radio" 	name="rejected_resion_name" value="Not Intrested"  />
                  <div class="radio-tile">
                    <label for="rejected" class="radio-tile-label">Not Intrested</label>
                  </div>
                </div>
                <div class="input-container input-container1">
                  <input id="other1" class="radio-button" type="radio" 	name="rejected_resion_name" value="Other" />
                  <div class="radio-tile">
                    <label for="other1" class="radio-tile-label">Other</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-0">
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <label>Rejection Reason in Brief</label>
            <textarea type="text" name="rejected_resion_message"  placeholder="Write the reason..." rows="5" class="form-control"></textarea>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="form-group row">
          <div class="col-12 col-form-label ml-2 mt-1">
            <div class="checkbox-inline">
              <label class="checkbox checkbox-primary">
              <input type="checkbox" name="email_everyone" id="email_everyone" value="1" checked="checked">
              <span class="mr-1"></span> Send email notifaction to everyone. </label>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-xs-12 col-md-12 text-center">
          <div class="form-group" id="status_msg" style="color:#FF0000; text-align:center; margin:10px; font-weight:bold;"> </div>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-light-danger btn-sm mr-2 p-1" data-dismiss="modal">Close&nbsp;<i class="fa fa-circle fa-sm"></i></button>
  <button type="button" class="btn btn-light-success btn-sm save_rejected_resion p-1">Submit&nbsp;<i class="fa fa-spinner fa-sm"></i></button>
</div>
<script>
   
	
    $('.save_rejected_resion').click(function () {
        $.easyAjax({
						url			: '{{route('staffing_company.apptracker.save_rejected_resion')}}',
						//url			: 'http://127.0.0.1/ats2020New/public/staffing_company/cvtracker/statusUpdate',
						container	: '#createSchedule',
						type		: "POST",
						data		: $('#frm_rejection_resion').serialize(),
						success		: function (response) {
						
						if(response.status == 'success')
						{
							$('#scheduleDetailModal_reschedule').modal('hide'); 	
							//$('#status_msg').html("Application status update successfully.");
							table._fnDraw();
						}
						else
						{
							//$('#status_msg').html(response.message);
							$('#alt_msg').html('<div class="alert alert-warning alert-dismissible"><a href="Javascript:void(0);" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Warning!</strong>'+response.message+'</div>');
							
						}
						
//alert(response.status); return false;
/*if(response.status == 'error'){
$('#status_msg').html(response.message);
}else{
$('#scheduleDetailModal').modal('hide'); 	
}*/
//if(response.status == 'success'){
//$('#status_msg').html("Application status update successfully.");
// table._fnDraw();
//}
//$('#scheduleDetailModal').modal('hide'); 
// $('#scheduleDetailModal').hide();
						   
						}
        			});
    });
	
	
	
</script>
</div>
