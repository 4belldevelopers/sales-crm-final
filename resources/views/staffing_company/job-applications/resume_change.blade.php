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
</style>
<?php 
	//APPLICATION DATA 
	$full_name			= $client_name	= $job_title	=  $city = $company_id =   ''; 	
	//echo '<pre>'; print_r(@$interviewers); 
?>
<div class="modal-header modal-htcp">
  <h4 class="modal-title">Change Resume</h4>
  <button type="button" class="close btn-sm mr-1" data-dismiss="modal" aria-hidden="true">
  	<i class="fa fa-times"></i>
  </button>
</div>
<div class="modal-body">
  <div id="alt_msg"></div>
  <form class="ajax-form" method="POST" id="createForm" name="createForm">
    @csrf
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <div class="row mt-9">
      <div class="col-xs-12 col-md-12">
        <div class="form-group">
          <input class="form-control" name="resume" id="resume" type="file" style="padding: 2em 1em;font-size: larger;">
        </div>
      </div>
    </div>
    <input type="hidden" name="application_id" id="application_id" value="<?php echo @$jobApplication->id; ?>" />
    <div class="row mt-9">
      <div class="col-xs-12 col-md-12">
        <div class="form-group"> &nbsp; </div>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-light-danger btn-sm mr-2 p-1" data-dismiss="modal">@lang('app.close')&nbsp;<i class="fa fa-circle fa-sm"></i></button>
  <button type="button" class="btn btn-light-success btn-sm save-schedule p-1">Update Resume &nbsp;<i class="fa fa-spinner fa-sm"></i></button>
  <div class="row mt-9">
    <div class="col-xs-12 col-md-12">
      <div class="form-group"> &nbsp; </div>
    </div>
  </div>
</div>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
   
	
	 $("#createForm").validate({
			rules: {						
							resume: {
											required: true
									}
												
					},
			messages: {
						
							resume: {
											required: "Please upload resume"
									}
					 }
		});
		
		
    $('.save-schedule').click(function () {
			
			//$("#createForm").valid(); exit; 
			
			$.easyAjax({
							 url: '{{route('staffing_company.job-applications.resume_update')}}',
							 container		: '#createForm',
							 type			: "POST",
							 file			: true,
							 data: $('#createForm').serialize(),
							 success: function (response) {
							  if(response.status == 'success')
							   {
								   $('#scheduleDetailModal').modal('hide');	
							   }
							   else
							   { 
									$('#alt_msg').html('<div class="alert alert-warning alert-dismissible"><a href="Javascript:void(0);" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Warning!</strong>'+response.message+'</div>');			
							   }
							}
				})
																				
    })
	
		
</script>
