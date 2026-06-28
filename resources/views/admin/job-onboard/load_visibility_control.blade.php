
<div class="modal-header">
  <h4 class="modal-title">Select Column</h4>
  <button type="button" class="close mr-0" data-dismiss="modal" aria-hidden="true">
  	<i class="fa fa-times"></i>
  </button>
</div>
<style>
body
{
	margin: 0!important;
	padding: 0!important;
}
#scheduleDetailModal_vis
{
	padding-right: 0!important;
}
#scheduleDetailModal_vis .modal-content
{
	height: 100vh;
}
.modal-dialog
{
    position: relative;
    width: auto;
    margin: .5rem;
    pointer-events: none;
    float: right;
}
.modal-lg {
    max-width: 350px;
}
.message{ color:#006600; margin:10px; text-align:center; font-weight:bold}
.error{ color:red;}
.form-group {margin-bottom: 0px!important;}
</style>
<div class="modal-body">
  <form id="createSchedule" name="createSchedule" class="ajax-form" method="post">
    
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='0' class="column_visibility_control" checked="checked"> ID  </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='1' class="column_visibility_control" checked="checked"> Applicant Name  </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='2' class="column_visibility_control" checked="checked"> All Jobs </label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"><input type="checkbox" name='hide_columns[]' value='3' class="column_visibility_control" checked="checked"> Current Designation </label>
          </div>
		   <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='4' class="column_visibility_control" checked="checked"> CTC </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='5' class="column_visibility_control" checked="checked">  ECTC</label>
          </div>
		  
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='6' class="column_visibility_control" checked="checked"> Status </label>
          </div>
		  <div class="form-group">
            <label for="contact_person_name"> <input type="checkbox" name='hide_columns[]' value='7' class="column_visibility_control" checked="checked"> Action </label>
          </div>
		
		  
		  
        </div>
       
        <div class="col-md-12">
          <div class="form-group message"></div>
        </div>
      </div>
    </div>
  </form>
</div>



<div class="modal-footer py-1 d-block w-100 px-0">
  <button type="button" class="btn btn-light-danger btn-sm d-block w-100" data-dismiss="modal">
  	<i class="fa fa-circle"></i>&nbsp;Cancel
  </button>
 </div>


<script>

$(document).ready(function() {


	$('.column_visibility_control').click(function(e)
		{
				  
				var dt 				= $('#myTable').DataTable();
				var checked_arr 	= [];
				var unchecked_arr 	= [];
				
				$.each($('input[type="checkbox"]:checked'), function (key, value) {
						
						var dt 		= $('#myTable').DataTable();
						dt.column(this.value).visible(true);
						checked_arr.push(this.value);
				});
				
				$.each($('input[type="checkbox"]:not(:checked)'), function (key, value) {
				
						var dt 		= $('#myTable').DataTable();
						dt.column(this.value).visible(false);
						unchecked_arr.push(this.value);
				});
				
				
				
		});
		

  
  
});

</script>

