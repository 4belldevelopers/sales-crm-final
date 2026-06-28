<style>
.form-check.form-check-custom .form-check-label { margin-left: 2.55rem; }
</style>
<form>
  <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
      <div class="card card-custom mx-2">
        <div class="card-header cursor-pointer">
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">HM| CM</h3>
          </div>
        </div>
        <div class="card-body py-7">
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table111">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-7">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-target="#Iteam_Table .form-check-input-Item" value="1">
                  </div></th>
                <th class="min-w-150px">Name</th>
                <th class="min-w-150px">Role</th>
                <th class="min-w-150px">Working Status</th>
                <th class="min-w-150px">Action</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
@php  
		
		foreach($team as $teamVal)
		{			
				$bgTextDecore = $strVal = '';
				$str 			= trim(@$teamVal->name);
				$str 			= $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} 
				else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} 
				else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} 
				else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} 
				else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}
										
				$btn_text			=	$btn_class			=	'';
				$btn_assign			=	0;
				if(@$teamVal->bench_assign_id > 0 && ($currentBenchId == $teamVal->bench_applications_id)){
					$btn_text		=	'Remove';
					$btn_class		=	'btn-light-danger'; 
					$btn_assign		=	1;	
				}else{
					$btn_text		=	'Add';
					$btn_class		=	'btn-light-primary'; 
					$btn_assign		=	0;
						
				}
				
				$working_status	= $working_status_style	=	'';
				$working_status			=  @$teamVal->working_status ==1 ? 'Enable' : 'Disable';
				$working_status_style	=  @$teamVal->working_status ==1 ? 'text-success' : 'text-danger';
				
@endphp 
              <tr>
                <td>
					<div class="form-check form-check-sm form-check-custom form-check-solid">
                    	<input class="form-check-input form-check-input-Item" type="checkbox" value="1">
                  	</div>
				</td>
                <td>
					<div class="d-flex align-items-center">
						<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> 
							<a href="javascript:void(0);" class="">
								<div class="symbol-label {{ @$bgTextDecore }} border-radius-5"> {{ strtoupper(@$strVal) }}</div>
							</a> 
						</div>
						<div class="d-flex flex-column"> 
							<span class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 15)) }}</span> 
							<span class="text-muted fw-bold d-block fs-7">&nbsp;</span> 
						</div>
                  </div>
				</td>
                <td>
					<div class="d-flex flex-column"> 
						<span class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->display_name, 0, 15)) }}</span> 
					</div>
				</td>
                <td>
					<div class="d-flex flex-column"> 
						<span class=" {{ @$working_status_style }} fw-bolder fs-6">{{ @$working_status }}</span> 
					</div>
				</td>
                <td>
					<div class="d-flex flex-column"> 
						<a id="btn_{{ @$teamVal->id }}"  href="javascript:void(0);" class="team_event cp_select btn btn-sm {{ $btn_class }} w-130"  
rel="{{ @$currentBenchId }}" data-assign="{{ $btn_assign }}" data-userid="{{ @$teamVal->id }}"> 
							{{ @$btn_text }} 
						</a> 
					</div>
				</td>
              </tr>
@php }  @endphp 
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
				
														var currentBenchId    		=	$(this).attr('rel');
														var currentUserId    		=	$(this).data('userid');
														var assignStatus    		=	$(this).data('assign');
														var id    					=	currentUserId;
														var token 					=	"{{ csrf_token() }}";
														var url   					=	"{{ route('product_company.bench-candidates.hmlistingupdate',':id') }}"; 
														url     					=	url.replace(':id', id);  		
														var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}";    		
														$.ajax({
																	type  		:	'GET',
																	url   		:	url,
																	data		:	{'_token': token, 'currentBenchId' : currentBenchId, 'currentUserId' : currentUserId, 'assignStatus' : assignStatus },
																	beforeSend	:	function() {
																				 		//$('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
																	},
																	success 	:	function (response) 
																	{			
																						if(response.status=='success'){
																							
																							if(assignStatus == 0){
																									$('#btn_'+currentUserId).addClass("btn-light-danger");
																									$('#btn_'+currentUserId).removeClass("btn-light-primary");
																									$('#btn_'+currentUserId).text('Remove');
																									
																									$('#btn_'+currentUserId).attr("assign", 1); 	
																							}else if(assignStatus == 1){
																									$('#btn_'+currentUserId).addClass("btn-light-primary");
																									$('#btn_'+currentUserId).removeClass("btn-light-danger");
																									$('#btn_'+currentUserId).text('Add');
																									
																									$('#btn_'+currentUserId).attr("assign", 0);		
																							}	
																						}
																						tableLoad('load');					
																	}
															}); 									
					});
					
					
});

</script>
