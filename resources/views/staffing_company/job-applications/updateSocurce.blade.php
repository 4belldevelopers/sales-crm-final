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
	color:#444444;
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
	//APPLICATION DATA  id full_name master_status_id
	$full_name			= $client_name	= $job_title	=  $city = $company_id =   ''; 	
	//echo '<pre>'; print_r(@$jobApplication); 
?>
<div class="modal-header modal-htcp">
  <h4 class="modal-title">Update Source</h4>
  <button type="button" class="close btn-sm mr-0" data-dismiss="modal" aria-hidden="true">
  	<i class="fa fa-times"></i>
  </button>
</div>
<div class="modal-body">
  <div id="alt_msg"></div>
  <div class="row pt-2">
    <div class="col-md-3">
    	<span class="font-weight-500 text-black">Fullname
       	</span>
    </div>
    <div class="col-md-9">
    	<span class="font-weight-500 text-black">
    		<?php echo @$jobApplication->full_name; ?>
       </span>
    </div>
  </div>
  <form class="ajax-form" method="POST" id="createForm" name="createForm">
    @csrf
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <div class="row mt-2">
      <div class="col-xs-12 col-md-3">
        <div class="form-group">
          	<label for="resume" class="col-form-label pt-0 font-weight-500 mt-0"> Source</label>
        </div>
      </div>
      <div class="col-xs-12 col-md-9">
        <div class="form-group">
          <select name="candidate_source" id="candidate_source">
            <?php 			
				$submitted_by = @$jobApplication->submitted_by; 
				
				if(!empty($users)){
					foreach($users as $uArr ){
						
						$sel	=	'';
						if($submitted_by == $uArr->id){
							$sel	=	'selected="selected"';
						}							
			?>
            <option value="<?php echo $uArr->id ?>"  <?php echo @$sel; ?> ><?php echo $uArr->name ?></option>
            <?php 
					}
				}	
			?>
          </select>
        </div>
      </div>
    </div>
    <input type="hidden" name="application_id" id="application_id" value="<?php echo @$jobApplication->id; ?>" />
    <div class="row mt-9">
      <div class="col-xs-12 col-md-12">
        <div class="form-group"> </div>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-light-danger btn-sm mr-2 p-1" data-dismiss="modal">@lang('app.close')&nbsp;<i class="fa fa-circle fa-sm"></i></button>
  <button type="button" class="btn btn-light-success btn-sm save-schedule p-1">Update Source &nbsp;<i class="fa fa-spinner fa-sm"></i></button>
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
							candidate_source: 	{
													required: true
												}
												
					},
			messages: {
						
							candidate_source: {
												required: "Please select source"
											}
					 }
		});
		
		
    $('.save-schedule').click(function () {
			
			//$("#createForm").valid(); exit; 
			
			$.easyAjax({
							 url: '{{route('staffing_company.job-applications.source_update')}}',
							 container		: '#createForm',
							 type			: "POST",
							 file			: true,
							 data: $('#createForm').serialize(),
							 success: function (response) {
							  if(response.status == 'success')
							   {
							   		$('#can_source').html(response.data.name); 
								    table._fnDraw();
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
