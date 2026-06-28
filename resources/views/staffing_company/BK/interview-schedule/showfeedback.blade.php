
<form class="ajax-form" method="POST" id="ajax-form" name="ajax-form" enctype="multipart/form-data">
@csrf
<input type="hidden" name="interview_id" id="interview_id" value="<?php echo @$interviewSchedule->interview_schedules_id ?>" />
<input type="hidden" name="application_id" id="application_id" value="<?php echo @$interviewSchedule->id ?>" />
<?php 	  		
			$schedule_date 		=	$schedule_time 		= "";
			$schedule 			=	@$interviewSchedule->schedule_date;
			$schedule_date 		=	date("d M, Y", strtotime($schedule));			
			$schedule_time 		=	date("H:i A", strtotime($schedule));
?> 
<div id="load_interview_feedback">
  <div class="modal-body scroll-y py-0 px-lg-7">
    <div data-kt-element="options">
      <div class="pb-5">
      <!--begin::Table-->
      <table class="table align-middle table-row-dashed fs-6 gy-5" id="">
        <!--begin::Table body-->
        <tbody class="text-gray-600 fw-bold">
          <!--begin::Table row-->
          <tr>
            <!--begin::Name=-->
            <td><div class="d-flex flex-column"> 
				<a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder fs-6">
					<?php echo ucwords(@$interviewSchedule->full_name); ?>
				</a> 
				<a href="javascript:void(0);" class="text-muted text-hover-primary fw-bold d-block fs-7">
					Frontend Developer</a> 
				</div>
			</td>
            <!--end::Name=-->
            <!--begin::Requisitions=-->
            <td>
				<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6"><?php echo ucwords(@$interviewSchedule->job_title); ?></span> 
					<span class="text-muted fw-bold d-block fs-7"><?php echo ucwords(@$interviewSchedule->client_name); ?></span> 
				</div>
			</td>
            <!--end::Requisitions=-->
            <!--begin::Interview Mode=-->
            <td>
				<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6"><?php echo ucwords(@$interviewSchedule->mode_name); ?></span> 
					<span class="text-muted fw-bold d-block fs-7"><?php echo ucwords(@$interviewSchedule->mode_lavel_name); ?></span> 
				</div>
			</td>
            <!--end::Interview Mode=-->
            <!--begin::Interviewers=-->
            <td><div class="d-flex flex-column">
                <div class="symbol-group symbol-hover">
<?php 
	$panellistArr	=	'';
	$panellistArr	=	@$interviewSchedule->interviewers;  
	if(!empty($panellistArr))
		{
			$panellistArr 	=	explode(',', $panellistArr);
			foreach($panellistArr as $pval)
			{
				$bgTextDecore = $strVal = $team_full_name	=	 '';
				$str 			= $team_full_name =  trim(@$pval);
				$str 			= $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-8 bg-light-danger text-danger';
				} 
				else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-8 bg-light-warning text-warning';
				} 
				else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-8 bg-light-primary text-primary';
				} 
				else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-8 bg-light-success text-success';
				} 
				else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-8 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-8 bg-light-secondary text-dark';
				}									
				echo '<div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" data-bs-original-title="'.ucwords(@$team_full_name).'">
                  <div class="symbol-label '.@$bgTextDecore.'">'.strtoupper(@$strVal).'</div>
                </div>';	
			}
		}							
?>
                
				  
                </div>
              </div></td>
           
<?php 
	$posted_by_image			=	'';	
	if(!empty($interviewSchedule->image)){	
		$posted_by_image		=	url('public/user-uploads/team_profile_img/'.@$interviewSchedule->image);
	}		
?>          
            <td>
				<div>
					<div class="d-flex flex-column">
						<div class="symbol-group symbol-hover">
							  <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="<?php echo @$interviewSchedule->upload_by_name; ?>"> 
								<img alt="Pic" src="<?php echo @$posted_by_image; ?>"> 
							  </div>
						</div>
					</div>
					<span class="text-muted fw-bold d-block fs-7"><?php echo @$interviewSchedule->upload_by_name; ?></span>
				</div>   
        	</td>
        
        <!--end::Source=-->
        <!--begin::Interview Date=-->
        <td>
			<div class="d-flex flex-column"> 
				<span class="text-dark fw-bolder fs-6"><?php echo @$schedule_date; ?></span> 
				<span class="text-muted fw-bold d-block fs-7"><?php echo @$schedule_time; ?></span> 
			</div>
		</td>
          <!--end::Interview Date=-->
          <!--begin::Stage-->
          <td>		  	
			<div class="d-flex flex-column"> 
				<span class="badge fw-bolder fs-6 <?php echo @$interviewSchedule->css_style; ?> px-2 py-1"><?php echo ucwords(@$interviewSchedule->master_status_name); ?></span> 
				<span class="text-muted fw-bold d-block fs-7"><?php echo ucwords(@$interviewSchedule->child_status_name); ?></span> 
			</div>
		 </td>
          <!--end::Stage-->
        </tr>
        <!--end::Table row-->
        </tbody>
        
        <!--end::Table body-->
      </table>
      <!--end::Table-->
      <!--begin::Candidate status-->
      <label class="text-dark fw-bolder fs-5">Candidate Status</label>
      <div class="row py-5 mb-3">
        <?php 	  		
	  		for($i=1; $i<=6; $i++)
			{ 
				$sel	=	'';
				if(@$interviewSchedule->available_status == $i ){
					$sel	=	'checked="checked"';	
				}
				
	  ?>
		<div class="col-lg-2"> 
			<input type="radio" class="btn-check app_int_status" name="app_int_status" value="<?php echo $i; ?>" id="app_int_status<?php echo $i; ?>"  <?php echo @$sel; ?> >
			<label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_status<?php echo $i; ?>"> 
				<span class="d-block fw-bold text-start"> 
					<span class="text-dark-800 fw-bolder d-block fs-5">
						<?php echo ucwords($available_status[$i]['name']); ?>
					</span> 
				</span>
			</label>
		</div> 
	  <?php 
	  		}
	  ?>		
	
      </div>
      <!--end::Candidate status-->
      <!--begin::Feedback status-->
      <label class="text-dark fw-bolder fs-5">Feedback Status</label>
      <div class="row py-5 mb-3">
 <?php 	  		
	for($j=0; $j<=4; $j++)
	{ 
		$sel	=	'';
		if(@$interviewSchedule->feedback == $j ){
			$sel	=	'checked="checked"';	
		}
?>
	  	<div class="col-lg-2">
			<input type="radio" class="btn-check" name="app_int_feedback_status" id="app_int_feedback_status_<?php echo $j; ?>" value="<?php echo $j; ?>" <?php echo @$sel; ?> >
			<label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_feedback_status_<?php echo $j; ?>"> 
				<span class="d-block fw-bold text-start"> 
					<span class="text-dark-800 fw-bolder d-block fs-5">
						<?php echo ucwords($feedback_status[$j]['name']); ?>
					</span> 
				</span> 
			</label>
		 </div>
<?php 
	 }
?>	
	  
				
      </div>
      <!--end::Feedback status-->
      <!--begin::Comment & rating-->
      <div class="row">
        <div class="py-5 d-flex align-items-center justify-content-between">
          <label class="text-dark fw-bolder fs-5">Comment</label>
          <div class="rating">
            <!--begin::Star 1-->
            <label class="rating-label" for="kt_rating_2_input_1"> <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"/>
                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
              </g>
            </svg>
            </span> </label>
            <input class="rating-input" name="rating" value="1" type="radio" checked="checked" id="kt_rating_2_input_1"/>
            <!--end::Star 1-->
            <!--begin::Star 2-->
            <label class="rating-label" for="kt_rating_2_input_2"> <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"/>
                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
              </g>
            </svg>
            </span> </label>
            <input class="rating-input" name="rating" value="2" type="radio" id="kt_rating_2_input_2"/>
            <!--end::Star 2-->
            <!--begin::Star 3-->
            <label class="rating-label" for="kt_rating_2_input_3"> <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"/>
                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
              </g>
            </svg>
            </span> </label>
            <input class="rating-input" name="rating" value="3" type="radio" id="kt_rating_2_input_3"/>
            <!--end::Star 3-->
            <!--begin::Star 4-->
            <label class="rating-label" for="kt_rating_2_input_4"> <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"/>
                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
              </g>
            </svg>
            </span> </label>
            <input class="rating-input" name="rating" value="4" type="radio" id="kt_rating_2_input_4"/>
            <!--end::Star 4-->
            <!--begin::Star 5-->
            <label class="rating-label" for="kt_rating_2_input_5"> <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"/>
                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
              </g>
            </svg>
            </span> </label>
            <input class="rating-input" name="rating" value="5" type="radio" id="kt_rating_2_input_5"/>
            <!--end::Star 5-->
          </div>
        </div>
        <textarea name="feedback_comment" id="feedback_comment" class="form-control form-control-solid" rows="5" placeholder="Write comment here..."></textarea>
      </div>
      <!--end::Comment & rating-->
    </div>
  </div>
  <div class="modal-footer flex-center">
    <div class="div1">
      <!--begin::Cancel-->
      <button type="button" id="" class="btn btn-secondary me-3" data-bs-dismiss="modal">Cancel</button>
      <!--end::Cancel-->
      <!--begin::Submit-->
      <button type="button" id="submit-frm-btn" class="btn btn-primary"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
      <!--end::Submit-->
    </div>
    <div class="div2" style="position: absolute;right: 2%;">
      <!--begin::Rescheduled-->
      
	 <a href="javascript:void(0);" id="app_interview_schedule_reschedule" class="btn btn-secondary" data-toggle="modal" data-target="#interview_schedule" rel="<?php echo @$interviewSchedule->id; ?>" data-bs-dismiss="modal">
         
         Rescheduled 
      </a>
      <!--end::Rescheduled-->
    </div>
  </div>
</div>

</form>


















<script>
$('#submit-frm-btn').click(function(){
    					
				var  app_int_status				=	$('input[name="app_int_status"]:checked').val(); 
				var  app_int_feedback_status	=	$('input[name="app_int_feedback_status"]:checked').val();  
				//var  rating2					=	$('input[name="rating2"]:checked').val();   
				
				
				var feedback_comment			=	$('#feedback_comment').val();								
				var id    						=	$('#interview_id').val();	
				var application_id    			=	$('#application_id').val();			
					
					
				var rating						=	$("input[name='rating']:checked").val();	
						
				var url 						=	"{{ route('admin.interview-schedule.updateInterview',':id') }}";
                url 							=	url.replace(':id', id);
                var token 						=	"{{ csrf_token() }}";

				var baseurl     				=	"{{ asset('assets/images/Loading2.gif') }}"; 
				
                $.ajax({
								type		: 'POST',
								url			: url,
								beforeSend	: function() {
                                  							$('#modal-content-body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    									},
						
								data		: {'_token': token, '_method': 'POST', 'id':id, 'app_int_status':app_int_status, 'app_int_feedback_status':app_int_feedback_status, 'feedback_comment':feedback_comment, 'application_id':application_id, 'rating':rating },
								success		: function (response) {
																		 if (response.status == "success") {
																				table._fnDraw();
																				Swal.fire("Success", "Interview Feedback submit successfully!", "success");								
																		}
								}
                });					  										
});

		
</script>