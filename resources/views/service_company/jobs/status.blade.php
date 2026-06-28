<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<style>
.modal-footer {margin: 0 auto;border-top: 1px solid #ffffff!important;}
.modal-body {border-bottom: 1px solid #c1c1c1;}
.dropdown-content li.active, .dropdown-content li:hover {background-color: transparent!important;}
.inactive {background-color: #f64e60!important;color: #fff!important;}
.activee {background-color: #109600 !important;color: #fff!important;}
.hold {background-color: #ffa800!important;color: #fff!important;}
.withSet { width: unset !important;}
</style>
  
    <div class="d-flex flex-column text-dark-75 pt-5" style="padding-left: 25px;">
        <span class="fw-bold fs-5 text-muted">Job Title :: <?php echo @$job->title ?></span>
        <!--<span class="fw-bolder fs-2">Job Status</span>-->
    </div>

<div class="modal-body" style="padding: 0em 1.75rem 2em;">
  <form id="createSchedule" class="ajax-form" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="job_id" id="job_id" value="<?php echo @$job->id;?>" />
    <div class="form-body">
      <div class="row" style="">     
<?php 
	$statusVal		=	@$job->status;
	$inactive =	$active	=	$hold =	$selclass = $activebgclass = $inactivebgclass = $holdbgclass =	'';
	$activedisp = $inactivedisp = $holddisp = 'd-none';
	if($statusVal =='inactive'){
		$inactive		=	'selected="selected"';	  
		$selclass		=	'closed-bg'; 
		$inactivebgclass		=	'closed-bg'; 
		$inactivedisp = '';
	}else if($statusVal =='active'){
		$active			=	'selected="selected"';	
		$selclass		=	'active-bg';
		$activebgclass		=	'active-bg';
		$activedisp = '';
	}else if($statusVal =='hold'){
		$hold			=	'selected="selected"';
		$selclass		=	'hold-bg';	 
		$holdbgclass		=	'hold-bg';
		$holddisp = '';
	}						 
?>
      </div>
      
      <div class="row">
        <div class="row py-5">
           <div class="col-lg-4">
              <div class="joined-div withSet {{ @$activebgclass }}" data-master-status-id="6">
                 <input type="radio" class="btn-check master_status" name="status" value="active" id="joined">
                 <label class="lbl_common_master btn-joined  btn  btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center" for="joined" id="lbl_master_status_6" selected>
                 <span class="d-block fw-bold text-start">
                 <span class="text-dark-800 fw-bolder d-block fs-5">Hiring</span>
                 </span>
                 </label>
              </div>
           </div>
           <div class="col-lg-4">
              <div class="offer-div withSet {{@$holdbgclass }}" data-master-status-id="5">
                 <input type="radio" class="btn-check master_status" name="status" value="hold" id="offer">
                 <label class="lbl_common_master btn-offer  btn  btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center" for="offer" id="lbl_master_status_5">
                 <span class="d-block fw-bold text-start">
                 <span class="text-dark-800 fw-bolder d-block fs-5">Hold</span>
                 </span>
                 </label>
              </div>
           </div>
           <div class="col-lg-4">
              <div class="rejected-div withSet {{ @$inactivebgclass }}" data-master-status-id="7">
                 <input type="radio" class="btn-check master_status" name="status" value="inactive" id="rejected">
                 <label class="lbl_common_master btn-rejected  btn  btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center" for="rejected" id="lbl_master_status_7">
                 <span class="d-block fw-bold text-start">
                 <span class="text-dark-800 fw-bolder d-block fs-5">Closed</span>
                 </span>
                 </label>
              </div>
           </div>
        </div>
        <ul class="joined-content {{@$activedisp}}" style="padding-left: 2rem;">
            <li style="box-sizing: border-box;">The position is Hiring </li>
            <li style="box-sizing: border-box;">Vendors can upload the profile.</li>
        </ul>
        <ul class="rejected-content {{@$inactivedisp}}" style="padding-left: 2rem;">
            <li style="box-sizing: border-box;">The position is Close</li>
            <li style="box-sizing: border-box;">Vendors can not be upload the profile.</li>
        </ul>
        <ul class="offer-content {{@$holddisp}}" style="padding-left: 2rem;">
            <li style="box-sizing: border-box;">The position is Hold</li>
            <li style="box-sizing: border-box;">Vendors can not be upload the profile.</li>
        </ul>
      </div>
      
      <!--Start::previous code-->
      
      <!--<div class="row">-->
      <!--  <div class="col-xs-6 col-md-3">-->
      <!--    <div class="form-group">-->
      <!--      <label class="d-block"> <i class="fa fa-line-chart fa-sm"></i>&nbsp;Job Status </label>   -->
      <!--    </div>-->
      <!--  </div>-->
      <!--  <div class="col-xs-6 col-md-9">-->
      <!--    <div class="form-group">-->
      <!--      <label class="d-block">-->
      <!--      <select name="status" id="status" class="<?php echo $selclass; ?>"> -->
      <!--        <option value="active" <?php echo $active; ?>  style="background-color:#FFFFFF; color:#000000" >Active </option>  -->
      <!--        <option value="hold" <?php echo $hold; ?>  style="background-color:#FFFFFF; color:#000000" >Hold </option>-->
      <!--        <option value="inactive" <?php echo $inactive; ?>  style="background-color:#FFFFFF; color:#000000" >Closed </option>  -->
      <!--      </select>  -->
      <!--      </label>    -->
      <!--    </div> -->
      <!--  </div>-->
      <!--</div>-->
      
      <!--End::previous code-->
      
      <div class="row">
          
      </div>
      
      <div class="row">
        <div class="col-xs-12 col-md-12 ">
          <div class="form-group" id="status_msg" style="text-align: center;font-weight: 500;background-color: #fff!important;width: fit-content;color: #00a700;margin: 0 auto;"> </div>
        </div>
      </div>
    </div>
  </form>
</div>


<!--<div class="modal-footer" style="display: table;">-->
<!--  <button type="button" class="btn btn-light-danger btn-sm p-1" data-dismiss="modal">@lang('app.close') <i class="fa fa-circle fa-sm"></i> </button>-->
<!--  <button type="button" class="btn btn-light-success btn-sm p-1" id="" onclick="newUpdateJobstatus()">@lang('app.update') <i class="fa fa-spinner fa-sm"></i> </button>-->
<!--</div>-->

<div class="modal-footer flex-center">
    <div class="div1">
      <button type="button" id="" class="btn btn-secondary me-3" data-dismiss="modal">Cancel</button>
      <button type="button" id="changetextupdate" class="btn btn-primary" onclick="newUpdateJobstatus()"> 
      	<span class="indicator-label changetextupdate">Update</span></button>
    </div>
</div>



<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script type="text/javascript">	
	$( document ).ready(function(){    	
		$( "#status" ).change(function() {
				$('#status_msg').html("");
				$( "#status" ).removeClass('inactive');
				$( "#status" ).removeClass('activee');
				$( "#status" ).removeClass('hold');
				//  if(this.value == 'inactive'){
				//  		$( "#status" ).addClass('inactive'); 
				//  }else if(this.value == 'active'){
				//  		$( "#status" ).addClass('activee');
				//  }else{ 
				//  		$( "#status" ).addClass('hold');
				//  }
		});
		
		$('.withSet').click(function(){
		    var id = $(this).attr('data-master-status-id');
		    if(id==6){
		        $('.offer-content').addClass('d-none');
		        $('.rejected-content').addClass('d-none');
		        $('.joined-content').removeClass('d-none');
		        $('.withSet').removeClass('hold-bg');
		        $('.withSet').removeClass('closed-bg');
		    }else if(id==5){
		        $('.joined-content').addClass('d-none');
		        $('.rejected-content').addClass('d-none');
		        $('.offer-content').removeClass('d-none');
		        $('.withSet').removeClass('active-bg');
		        $('.withSet').removeClass('closed-bg');
		    }else if(id==7){
		        $('.joined-content').addClass('d-none');
		        $('.offer-content').addClass('d-none');
		        $('.rejected-content').removeClass('d-none');
		        $('.withSet').removeClass('active-bg');
		        $('.withSet').removeClass('hold-bg');
		    }
		    
		    
		});
		
		
	});
	function newUpdateJobstatus(){ 
	    $('.changetextupdate').prop('disabled',true);
	    $('#changetextupdate').text('Waiting...');
      var page = $('.custom-pagination .active_disabled').data('page');
		$.ajax({
					url: '{{route('service_company.jobs.newUpdateJobstatus')}}',
					container: '#createSchedule',
					type: "POST",
					data: $('#createSchedule').serialize(),
					success: function (response) {

            if (response.status == 'coinCheck') {
		
            Swal.fire("Failed",` ${response.message}` , "warning").then(function(){
              $(".btn-active-color-primary").click();
              });		
              
              return false;	
          }
					     
						if(response.status == 'success'){
							$('.activeJobs').html(response.activeJobs);
							$('.inactiveJobs').html(response.inactiveJobs);
							$('.holdJobs').html(response.holdJobs);
							$('#status_msg').html("Requisition status update successfully.");      
							
              // table._fnDraw();
							setTimeout(() => {
               $('#status_sub_stage_close').click();
              
               fetchCandidates(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    
                    $('.changetextupdate').prop('disabled',false);
                    $('#changetextupdate').text('Update');
                    $('.custom-pagination .page-link').removeClass('active_disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('.custom-pagination .page-link').removeClass('active_disabled');
                });
                // fetchCandidates();
                }, 500);
							
							
					   }
					   else{
					   		$('#status_msg').html("<div style='color:#FF0000'>Opps something worng. Please try again</div>"); 	
					   }
					}
				})	
	}	
</script>   
 