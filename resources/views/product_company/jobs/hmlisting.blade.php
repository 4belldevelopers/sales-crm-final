<link href="{{ asset('demo/css/css-circular-prog-bar.css') }}" rel="stylesheet">
<style>
	/*begin::Prograss bar css*/
	.progressNew {
	width: 50px;
	height: 50px;
	line-height: 50px;
	background: none;
	margin: 0 auto;
	box-shadow: none;
	position: relative;
	}
	.progressNew:after {
	content: "";
	width: 100%;
	height: 100%;
	border-radius: 50%;
	border: 7px solid #eee;
	position: absolute;
	top: 0;
	left: 0;
	}
	.progressNew > span {
	width: 50%;
	height: 100%;
	overflow: hidden;
	position: absolute;
	top: 0;
	z-index: 1;
	}
	.progressNew .progressNew-left {
	left: 0;
	}
	.progressNew .progressNew-bar {
	width: 100%;
	height: 100%;
	background: none;
	border-width: 7px;
	border-style: solid;
	position: absolute;
	top: 0;
	border-color: #519ff7;
	}
	.progressNew .progressNew-left .progressNew-bar {
	left: 100%;
	border-top-right-radius: 75px;
	border-bottom-right-radius: 75px;
	border-left: 0;
	-webkit-transform-origin: center left;
	transform-origin: center left;
	}
	.progressNew .progressNew-right {
	right: 0;
	}
	.progressNew .progressNew-right .progressNew-bar {
	left: -100%;
	border-top-left-radius: 75px;
	border-bottom-left-radius: 75px;
	border-right: 0;
	-webkit-transform-origin: center right;
	transform-origin: center right;
	}
	.progressNew .progressNew-value {
	display: flex;
	border-radius: 50%;
	font-size: 0px;
	text-align: center;
	line-height: 20px;
	align-items: center;
	justify-content: center;
	height: 100%;
	font-weight: 300;
	}
	.progressNew .progressNew-value div {
	margin-top: 10px;
	}
	.progressNew .progressNew-value span {
	font-size: 13px!important;
	text-transform: uppercase;
	}

	/* new css*/
	.progressNew .progressNew-value .percent_value
	{
		font-size: 14px!important;
		font-weight: 500;
	}
	/*new css*/


	/* This for loop creates the 	necessary css animation names 
	Due to the split circle of progressNew-left and progressNew right, we must use the animations on each side. 
	*/
	.progressNew[data-percentage="10"] .progressNew-right .progressNew-bar {
	animation: loading-1 1.5s linear forwards;
	}
	.progressNew[data-percentage="10"] .progressNew-left .progressNew-bar {
	animation: 0;
	}

	.progressNew[data-percentage="20"] .progressNew-right .progressNew-bar {
	animation: loading-2 1.5s linear forwards;
	}
	.progressNew[data-percentage="20"] .progressNew-left .progressNew-bar {
	animation: 0;
	}

	.progressNew[data-percentage="30"] .progressNew-right .progressNew-bar {
	animation: loading-3 1.5s linear forwards;
	}
	.progressNew[data-percentage="30"] .progressNew-left .progressNew-bar {
	animation: 0;
	}

	.progressNew[data-percentage="40"] .progressNew-right .progressNew-bar {
	animation: loading-4 1.5s linear forwards;
	}
	.progressNew[data-percentage="40"] .progressNew-left .progressNew-bar {
	animation: 0;
	}

	.progressNew[data-percentage="50"] .progressNew-right .progressNew-bar {
	animation: loading-5 1.5s linear forwards;
	}
	.progressNew[data-percentage="50"] .progressNew-left .progressNew-bar {
	animation: 0;
	}

	.progressNew[data-percentage="60"] .progressNew-right .progressNew-bar {
	animation: loading-5 1.5s linear forwards;
	}
	.progressNew[data-percentage="60"] .progressNew-left .progressNew-bar {
	animation: loading-1 1.5s linear forwards 1.5s;
	}

	.progressNew[data-percentage="70"] .progressNew-right .progressNew-bar {
	animation: loading-5 1.5s linear forwards;
	}
	.progressNew[data-percentage="70"] .progressNew-left .progressNew-bar {
	animation: loading-2 1.5s linear forwards 1.5s;
	}

	.progressNew[data-percentage="80"] .progressNew-right .progressNew-bar {
	animation: loading-5 1.5s linear forwards;
	}
	.progressNew[data-percentage="80"] .progressNew-left .progressNew-bar {
	animation: loading-3 1.5s linear forwards 1.5s;
	}

	.progressNew[data-percentage="90"] .progressNew-right .progressNew-bar {
	animation: loading-5 1.5s linear forwards;
	}
	.progressNew[data-percentage="90"] .progressNew-left .progressNew-bar {
	animation: loading-4 1.5s linear forwards 1.5s;
	}

	.progressNew[data-percentage="100"] .progressNew-right .progressNew-bar {
	animation: loading-5 1.5s linear forwards;
	}
	.progressNew[data-percentage="100"] .progressNew-left .progressNew-bar {
	animation: loading-5 1.5s linear forwards 1.5s;
	}

	@keyframes loading-1 {
	0% {
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
	}
	100% {
		-webkit-transform: rotate(36);
		transform: rotate(36deg);
	}
	}
	@keyframes loading-2 {
	0% {
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
	}
	100% {
		-webkit-transform: rotate(72);
		transform: rotate(72deg);
	}
	}
	@keyframes loading-3 {
	0% {
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
	}
	100% {
		-webkit-transform: rotate(108);
		transform: rotate(108deg);
	}
	}
	@keyframes loading-4 {
	0% {
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
	}
	100% {
		-webkit-transform: rotate(144);
		transform: rotate(144deg);
	}
	}
	@keyframes loading-5 {
	0% {
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
	}
	100% {
		-webkit-transform: rotate(180);
		transform: rotate(180deg);
	}
	}
	.progress {
	margin-bottom: 1em;
	}
	/*end::Prograss bar css*/
	</style>

<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>


<form>
<?php 

	$current_job_id				=	''; 
	$current_job_id				=	@$job_id;	 
?>
<input type="hidden" name="current_job_id" id="current_job_id" value="<?php echo @$current_job_id; ?>" />
<div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
  <!--begin::Tabs-->
  <div class="card card-custom mx-2">
    <!--Begin::Tabs Header-->
    <div class="card-header card-header-tabs-line">
      <div class="card-toolbar">
        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
          <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#recommended"> <span class="nav-text font-weight-bold">HM Listing</span> </a> </li>
        </ul>
      </div>
    </div>
    <!--end::Tabs Header-->
    <!--Begin::Tabs Body-->
    <div class="card-body py-0">
      <div class="tab-content pt-5">
        <div class="tab-pane active show" id="recommended" role="tabpanel">
          <div class="card-header cursor-pointer px-0 d-flex align-items-center justify-content-between">
            <div class="card-title m-0 d-block">
              <h3 class="fw-bolder m-0">HM Listing</h3>
            </div>
          </div>
          <div class="card-body px-0 py-7">
        
		    
        	<table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table111">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="fw-bolder text-muted">
              <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-7">
                  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-target="#Iteam_Table .form-check-input-Item" value="1">
                </div></th>
              <th class="min-w-150px">Name</th>
              <th class="min-w-150px">Role</th>
			  <th class="min-w-150px">Working Status</th>			  
              <th class="min-w-150px">Action</th>
            </tr>
            <!--end::Table row-->
          </thead>
          <!--end::Table head-->
          <!--begin::Table body-->
          <tbody class="text-gray-600 fw-bold">

<?php 
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
					
					
				$btn_text	=	'';	
				$btn_class	=	'';
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
				$working_status			=	@$teamVal->working_status ==1 ? 'Enable' : 'Disable';
				$working_status_style	=	@$teamVal->working_status ==1 ? 'text-success' : 'text-danger';
?>
            <tr>
              <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                  <input class="form-check-input form-check-input-Item" type="checkbox" value="1">
                </div></td>
              <td><div class="d-flex align-items-center">
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                    <div class="symbol-label <?php echo @$bgTextDecore; ?> border-radius-5"><?php echo strtoupper(@$strVal); ?></div>
                    </a> </div>
                  <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6"><?php echo ucwords(substr(@$teamVal->name, 0, 15));  ?></span> <span class="text-muted fw-bold d-block fs-7">&nbsp;</span> </div>
                </div></td>
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6"><?php echo ucwords(substr(@$teamVal->display_name, 0, 15))  ?></span> </div></td>
              
			  <td><div class="d-flex flex-column"> <span class=" <?php echo @$working_status_style; ?> fw-bolder fs-6"><?php echo @$working_status; ?></span> </div></td>
			  
			   
              <td>
			  	<div class="d-flex flex-column"> 
					
<a id="btn_<?php echo @$teamVal->id ?>"  href="javascript:void(0);" class="team_event cp_select btn btn-sm <?php echo $btn_class; ?> w-130"  
rel="<?php echo @$currentJobId ?>" data-assign="<?php echo $btn_assign; ?>" data-userid="<?php echo @$teamVal->id ?>">
	<?php echo @$btn_text; ?>
</a>
					 
				</div>
			 </td>
           </tr>
<?php 
		}
		
		//echo '<pre>'; print_r($team); 
?>

          </tbody>
          <!--end::Table body-->
        </table>
		
		  </div>
        </div>
      </div>
    </div>
    <!--end::Tabs Body-->
  </div>
  <!--end::Tabs-->
</div>

</form>


<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>


<script>

$(document).ready(function(){

						$('body').on('click', '.team_event', function(){
				
														var currentJobId    		=	$(this).attr('rel');
														var currentUserId    		=	$(this).data('userid');
														var assignStatus    		=	$(this).data('assign');
								
														
														var id    					=	currentUserId;
														var token 					=	"{{ csrf_token() }}";
										
											
														var url   					=	"{{ route('product_company.jobs.hmlistingupdate',':id') }}"; 
														url     					=	url.replace(':id', id);  		
														var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}";    		
														$.ajax({
																	type  		:	'GET',
																	url   		:	url,
																	data		:	{'_token': token, 'currentJobId' : currentJobId, 'currentUserId' : currentUserId, 'assignStatus' : assignStatus },
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
																							}else if(assignStatus == 1){
																									$('#btn_'+currentUserId).addClass("btn-light-primary");
																									$('#btn_'+currentUserId).removeClass("btn-light-danger");
																									$('#btn_'+currentUserId).text('Add');		
																							}
																							
																								
																						}
																						
																						
																						table._fnDraw();
																						load_jobs._fnDraw();
																						
																						//$('#kt_activitiesRecruiter_body').html(response.view);
																	}
															}); 
															
															
					});
					
	
	
});



</script>



