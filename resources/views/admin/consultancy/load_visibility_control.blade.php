<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<div class="modal-header">
  <h4 class="modal-title">Select Column</h4>
  <button type="button" class="close btn-sm mr-0" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<style>
.modal-dialog
{
  position: relative;
  width: auto;
  margin: .5rem;
  pointer-events: none;
  float: right;
}
.modal-lg
{
  max-width: 350px;
}
.modal-content
{
  height: 100vh;
}
.message
{
  color:#006600;
  margin:10px;
  text-align:center;
  font-weight:bold;
}
.error
{
  color:#FF0000;
}
#scheduleDetailModal
{
  padding-right: 0px!important;
  display: block;
}
</style>
<div class="modal-body">
  <form id="createSchedule" name="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="contact_person_name">
              <input type="checkbox" name='hide_columns[]' value='0' class="column_visibility_control" checked="checked">
              ID
            </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name">
              <input type="checkbox" name='hide_columns[]' value='1' class="column_visibility_control" checked="checked">
              Consultancy
            </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name">
              <input type="checkbox" name='hide_columns[]' value='2' class="column_visibility_control" checked="checked">
              Contact Person
            </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name">
              <input type="checkbox" name='hide_columns[]' value='3' class="column_visibility_control" checked="checked">
              Mobile
            </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name">
              <input type="checkbox" name='hide_columns[]' value='4' class="column_visibility_control" checked="checked">
              Email
            </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name">
              <input type="checkbox" name='hide_columns[]' value='5' class="column_visibility_control" checked="checked">
              Commission(%)
            </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name">
              <input type="checkbox" name='hide_columns[]' value='6' class="column_visibility_control" checked="checked">
              Payout(Days)
            </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name">
              <input type="checkbox" name='hide_columns[]' value='7' class="column_visibility_control" checked="checked">
              Status
            </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name">
              <input type="checkbox" name='hide_columns[]' value='8' class="column_visibility_control" checked="checked">
              Action
            </label>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group message"></div>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer py-2 px-0 d-block w-100">
  <button type="button" class="btn btn-light-danger font-weight-500 d-block w-100 py-1 btn-sm" data-dismiss="modal">
    @lang('app.close')
  </button>
</div>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>

$(document).ready(function() {


	$('.column_visibility_control').click(function()
		{
				var checked_arr 	= [];
				var unchecked_arr 	= [];
				
				$.each($('input[type="checkbox"]:checked'), function (key, value) {
						var dt = $('#myTable').DataTable();
						dt.column(this.value).visible(true);
				});
				
				$.each($('input[type="checkbox"]:not(:checked)'), function (key, value) {
						var dt = $('#myTable').DataTable();
						dt.column(this.value).visible(false);
				});
				
		});
		
$("#createSchedule").validate({
    rules: {
						name : {
											required: true,
											minlength: 1
						}
    	},
    messages : {
						name: {
											required: "Please enter name",
											minlength: "Name should be at least 1 characters"
						}
    		}
  });
  
  
});

</script>

