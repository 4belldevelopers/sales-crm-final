<div class="modal-header" style="padding: 8px 0px 8px 0px!important;">
  <h4 class="modal-title">Select Column</h4>
  <button type="button" class="close btn-sm mr-0 btn-outline-none" data-dismiss="modal" aria-hidden="true"> <i class="fa fa-times"></i></button>
</div>
<style>
.modal-dialog{position: relative;width: auto;margin: .5rem;pointer-events: none;float: right;}
.modal-lg{max-width: 350px;}
.message{ color:#006600; margin:10px; text-align:center; font-weight:bold}
.error{ color:#FF0000}

.font-weight-normal{font-weight: normal!important;}
.font-weight-500{font-weight: 500!important;}
.form-group label {margin-top: .2em;}
label {margin-bottom: .1rem;}

</style>
<div class="modal-body">
  <form id="createSchedule" name="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='0'   checked="checked">
            ID </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='1'   checked="checked">
            Date </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='2'   checked="checked">
            Applicant </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='3'   checked="checked">
            Client </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='4'   checked="checked">
            Jobs </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='5'   checked="checked">
            Source </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='6'   checked="checked">
            Email </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='7'   checked="checked">
            Phone </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='8'   checked="checked">
            Current CTC </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='9'   checked="checked">
            Excepted CTC </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='10'   checked="checked">
            Experience </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='11'   checked="checked">
            Relevant Experience </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='12'   checked="checked">
            Passport </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='13'   checked="checked">
            Notice Period </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='14'   checked="checked">
            Current Designation </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='15'   checked="checked">
            Current Company </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='16'   checked="checked">
            Current Location </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='17'   checked="checked">
            Qualification </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='18'   checked="checked">
            Status </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='19'   checked="checked">
            Interview Status Level </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='20'   checked="checked">
            Resume </label>
          </div>
          <div class="form-group">
            <label for="contact_person_name" class="mt-1 pt-0 pb-0">
            <input type="checkbox" name='hide_columns[]' value='21' class="" checked="checked">
            Action </label>
          </div>
          <div class="load text-center"> <a href="javascript:void(0);" id="reset-filters" class="btn btn-light-danger btn-sm font-weight-500 d-block px-1 py-1 mb-2"> <i class="fa fa-spinner fa-sm"></i>&nbsp;@lang('app.reset') </a> <a href="javascript:void();" id="load" class="btn btn-light-success font-weight-500 d-block w-100 column_visibility_control btn-sm px-1 py-1"><i class="fa fa-save"></i>&nbsp;Save & Submit </a> </div>
        </div>
        <div class="col-md-12">
          <div class="form-group message"></div>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer d-none">
  <!-- <button type="button" class="btn dark btn-outline" data-dismiss="modal">@lang('app.close')</button> -->
</div>
<script>

$(document).ready(function() {

	$('.column_visibility_control').click(function(e)
		{
				  
				var dt 				= $('#myTable').DataTable();
				var checked_arr 	= [];
				var unchecked_arr 	= [];
				var flag_1			=	0;
				var flag_2			=	0;
				
				$('#load').html('Loading...<i class="fa fa-spinner fa-spin fa-sm" aria-hidden="true"></i>'); 
				
				$.each($('input[type="checkbox"]:checked'), function (key, value)
        {
						var dt 		= $('#myTable').DataTable();
						dt.column(this.value).visible(true);
						checked_arr.push(this.value);
						flag_1			=	1;
				});
				
				$.each($('input[type="checkbox"]:not(:checked)'), function (key, value)
        {
				  var dt 		= $('#myTable').DataTable();
					dt.column(this.value).visible(false);
					unchecked_arr.push(this.value);
					flag_2			=	1;
				});
				
				//if((flag_1 ==1) && (flag_1 ==2)){
					$('#load').html('Loading'); 	
				//}
    });
});
</script>
