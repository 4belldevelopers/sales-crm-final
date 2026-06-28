<?php
use App\Helper\GeneralMethod;
// dd("jd");
?>
@push('head-script')
<style>
.form-check.form-check-custom .form-check-label {margin-left: 2.55rem;}
</style>
@endpush
<form>
  <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
      <div class="card card-custom mx-2">
        <!--<div class="card-header cursor-pointer">-->
        <!--  <div class="card-title m-0">-->
        <!--    <h3 class="fw-bolder m-0">HM| CM</h3>-->
        <!--  </div>-->
        <!--</div>-->
        <div class="card-body py-7">
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table111">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="min-w-150px">Name</th>
				<th class="min-w-150px">Contact</th>
				<th class="min-w-150px">Jobs</th>
                <th class="min-w-150px">Role</th>
                <th class="min-w-150px">Working Status</th>
                <th class="min-w-150px">Action</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
<?php 
            $team_event  = '';
          

		foreach($team as $teamVal)
		{
			$activeJobs = DB::table('job_assign')->join('jobs','jobs.id','=','job_assign.job_id')->where('jobs.status','=','active')->where('job_assign.user_id', $teamVal->id)->count();

					$bgTextDecore = $strVal = '';
					$str 			= trim(@$teamVal->name);
					$str 			= $strVal = strtolower($str[0]);

					$bgTextDecore				=	GeneralMethod::icoinText(@$teamVal->name);
							
					$btn_text	=	$btn_class	=	'';
					$btn_assign	=	0;
					
					if(@$teamVal->job_assign_id > 0 && ($currentJobId == $teamVal->job_id)){
						$btn_text	=	'Remove';
						$btn_class	=	'btn-light-danger'; 
						$btn_assign	=	1;	
					}else{
						$btn_text	=	'Add';
						$btn_class	=	'btn-light-primary'; 
						$btn_assign	=	0;		
					}
					$working_status			=	$working_status_style	=	'';
					$working_status			=  @$teamVal->working_status ==1 ? 'Enable' : 'Disable';
					$working_status_style	=  @$teamVal->working_status ==1 ? 'text-success' : 'text-danger';

?>
              <tr>
               
                <td><div class="d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                      <div class="symbol-label <?php echo @$bgTextDecore; ?> border-radius-5"><?php echo strtoupper(@$strVal); ?></div>
                      </a> </div>
                    <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6"><?php echo ucwords(substr(@$teamVal->name, 0, 30));  ?></span> <span class="text-muted fw-bold d-block fs-7"><?php echo ucwords(@$teamVal->designation)  ?></span> 
					</div>
                  </div></td>
				  <td>
					<span class="text-dark fw-bolder fs-6"><?php echo @$teamVal->mobile;  ?></span>
					<span class="text-muted fw-bold d-block fs-7"><?php echo @$teamVal->email;  ?></span> 
                  </div>
				</td>

				  <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">
					<div class="d-flex flex-column">
						<span class="{{ ($activeJobs >=1) ? 'fw-bolder text-success' : 'text-muted fw-bold d-block fs-7' }} fs-6">{{ $activeJobs }} Active</span>
					  </div>
				  </div></div></span> </div></td>
                <td><div class="d-flex flex-column"> <span class="text-muted fw-bold d-block fs-7"><?php echo ucwords(substr(@$teamVal->display_name, 0, 15))  ?></span> </div></td>
                <td><div class="d-flex flex-column"> <span class=" <?php echo @$working_status_style; ?> fw-bolder fs-6"><?php echo @$working_status; ?></span> </div></td>
                <td><div class="d-flex flex-column"> <a id="btn_<?php echo @$teamVal->id ?>"  href="javascript:void(0);" class="<?= $team_event ?> {{ ($team_event=='') ? '' : '' }} btn btn-sm <?php echo $btn_class; ?> w-130"  
rel="<?php echo @$currentJobId ?>" data-assign="<?php echo $btn_assign; ?>" data-userid="<?php echo @$teamVal->id ?>"> <?php echo @$btn_text; ?> </a> </div></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</form>


<script>
	$(document).ready(function(){

						$('body').on('click', '.team_event', function(){
				
				
														var currentJobId    		=	$(this).attr('rel');
														var currentUserId    		=	$(this).data('userid');
														var assignStatus    		=	$(this).data('assign');
														var id    					=	currentUserId;
														if(assignStatus == 0){
																$('#btn_'+currentUserId).addClass("btn-light-danger");
																$('#btn_'+currentUserId).removeClass("btn-light-primary");
																$('#btn_'+currentUserId).text('Remove');
																$(this).data('assign',1);	
														}else if(assignStatus == 1){
																$('#btn_'+currentUserId).addClass("btn-light-primary");
																$('#btn_'+currentUserId).removeClass("btn-light-danger");
																$('#btn_'+currentUserId).text('Add');
																$(this).data('assign',0);			
														}
														var token 					=	"{{ csrf_token() }}";
														var url   					=	"{{ route('service_company.jobs.hmlistingupdate',':id') }}"; 
														url     					=	url.replace(':id', id);  		
														var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}";    		
														$.ajax({
																	type  		:	'GET',
																	url   		:	url,
																	data		:	{'_token': token, 'currentJobId' : currentJobId, 'currentUserId' : currentUserId, 'assignStatus' : assignStatus },
																	success 	:	function (response) {			
																						if(response.status=='success'){																							
																							if(assignStatus == 0){
																									$('#btn_'+currentUserId).addClass("btn-light-danger");
																									$('#btn_'+currentUserId).removeClass("btn-light-primary");
																									// $('#btn_'+currentUserId).text('Remove');	
																							}else if(assignStatus == 1){
																									$('#btn_'+currentUserId).addClass("btn-light-primary");
																									$('#btn_'+currentUserId).removeClass("btn-light-danger");
																									// $('#btn_'+currentUserId).text('Add');		
																							}		
																						}
																						
																						table._fnDraw();
																						load_jobs._fnDraw();
																						
																	}
															}); 
																									
					});
					
					
					
					
						});
</script>


