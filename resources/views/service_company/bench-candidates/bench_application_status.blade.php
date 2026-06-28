@php  
    use App\Helper\GeneralMethod;
	use Illuminate\Support\Facades\DB;


		$bgTextDecore			=	$strVal	= $cicon	 = $prevMasterStage = $prevSubStage = 	'';
		$str						=	trim(@$benchApplication->full_name);
		$str	= $strVal			=	strtolower($str[0]);
		$str						=	ord($str);
		if($str >=97 && $str <=101){
				$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
		}else if($str >=102 && $str <=106){
				$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
		}else if($str >=107 && $str <=111){
				$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
		}else if($str >=112 && $str <=116){
				$bgTextDecore		=	'fs-3 bg-light-success text-success';	
		}else if($str >=117 && $str <=121){
				$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
		}else{
				$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
		}	
		$cicon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
						<a href="javascript:void(0);" class="">
							<div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
						</a>
				  </div>';  
		$prevMasterStage 			=	@$benchApplication->status; 
		$bgTextDecore				=	$strVal	=	'';
		$str						=	trim(@$benchApplication->submitted_by_name);
		$str	= $strVal			=	strtolower($str);
		$str						=	ord($str);
		if($str >=97 && $str <=101){
				$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
		}else if($str >=102 && $str <=106){
				$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
		}else if($str >=107 && $str <=111){
				$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
		}else if($str >=112 && $str <=116){
				$bgTextDecore		=	'fs-3 bg-light-success text-success';	
		}else if($str >=117 && $str <=121){
				$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
		}else{
				$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
		}	
		$company_id = auth()->user()->company_id;
		$id = $benchApplication->id;
		$team = DB::table('users')
			->leftjoin('bench_assign', function ($join) use ($id) {
				$join->on('bench_assign.user_id', '=', 'users.id');
				$join->on('bench_assign.bench_applications_id', '=', DB::raw("'" . $id . "'"));
			})
			->leftjoin('role_user', 'role_user.user_id', 'users.id')
			->leftjoin('roles', 'roles.id', 'role_user.role_id')
			->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
			->select(
				'users.id',
				'users.name',
				'users.email',
				'users.working_status',
				'bench_assign.id as bench_assign_id',
				'bench_assign.bench_applications_id',
				'users_profile.designation as display_name'
			)
			->where('users.company_id', $company_id)
			->where('bench_assign.bench_applications_id', $id)
			->where('users.status', '=', 'active')
			->orderByDesc('bench_assign.user_id')
			->orderBy('users.name', 'asc')
			->groupBy('users.id')
			->get();

                $countMnager = count($team);
			    $manager_name = $team[0]->name ?? '';
				$strVal = '';
				if($manager_name){
                    $str = trim(@$manager_name);
                    $strVal = ucwords(strtolower($str[0]));
				}
				if($countMnager > 1){
                   $countMnager = ($countMnager)-(1).'+';
				}
				// dd($countMnager,$team);

						$icon='<div class="d-flex flex-column">
									<div data-row-id="1255" class="symbol-group symbol-hover">
										<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="'.$manager_name.'">
											<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
												<a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="">
													<div class="symbol-label fs-8 fs-8 bg-dark text-secondary border-radius-100">'.$strVal.'</div>
												</a>
											</div>
										</div>
										<div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="'.$manager_name.'">
											<span class="symbol-label font-weight-bold">'.$countMnager.'</span>
										</div>
									</div>
						</div>';


		// $icon	= '<div class="" style="float:left">
		// 			<span href="javascript:void(0);" class="text-dark fw-bolder fs-6">
		// 				<div class="symbol-label  border-radius-5 fw-bolder" style="font-size:13px!important;" data-toggle="tooltip" title="'.@$benchApplication->posted_by.'">'.ucwords($strVal).'</div>
		// 			</span>
				//   </div>';
		
		// if(!empty($company->logo_logical_path)){
		// 	$icon	= '<div class="symbol symbol-circle symbol-25px overflow-hidden border-radius-0">
		// 					<a href="javascript:void(0);">
		// 						<div class="symbol-label border-radius-5">
		// 							<img src="'.url('public/'.@$company->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
		// 						</div>
		// 					</a>
		// 			 </div>';
		// }	
    			  
@endphp 

<form class="form fv-plugins-bootstrap fv-plugins-framework" id="status_frm" name="status_frm" method="post" enctype="multipart/form-data">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="bench_application_id" id="bench_application_id" value="{{ @$benchApplication->id }}" />
  <input type="hidden" name="placedid" id="placedid" value="{{ @$placedid }}" />
  <div class="modal-body scroll-y py-0 px-lg-7">
    <div data-kt-element="options">
      <div class="pb-5">
        <table class="table align-middle table-row-dashed fs-6 mt-5" id="">
          <thead>
            <tr class="fw-bolder text-muted" role="row">
              <th class="">Name</th>
              <th class="">Contacts</th>
              <th class="">Exp.</th>
              <th class="">Salary</th>
              <th class="">Notice Period</th>
              <th class="">Qualification</th>
              <th class="">Location</th>
              <th class="">Manager</th>
              {{-- <th class="">Requisition</th> --}}
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-bold">
            <tr class="">
              <td class="d-flex align-items-center border-bottom-0">
			  		{!! @$cicon !!}
                <div class="d-flex flex-column"> 
					<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="{{ @$benchApplication->full_name }}"> 
						{{ ucwords(substr(@$benchApplication->full_name, 0, 20)) }} 
					</a> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$benchApplication->current_designation }}">
						{{ ucwords(substr(@$benchApplication->current_designation, 0, 20)) }}
					</span> 
				</div>
              </td>
			  <td class="">
			  	<div class="d-flex flex-column"> 
					<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">
						{{ @$benchApplication->phone }}
					</a> 
					<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="{{ @$benchApplication->email }}">
						{{ substr(@$benchApplication->email, 0, 20) }}
					</a> 
				</div>
			  </td>
			  <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ @$benchApplication->total_exp }}+</span> 
					<span class="text-muted fw-bold d-block fs-7">{{ @$benchApplication->rel_exp }}+</span> 
				</div>
			  </td>
			  <td class="">
			  	<div class="d-flex flex-column"> 
			  		<span class="text-dark fw-bolder fs-6">{!! @$bill_rate_amount !!}</span> 
					<span class="text-muted fw-bold d-block fs-7"> {!! @$pay_rate_amount !!}</span> 
				</div>
			  </td>
			  <td class="">
				@php
				$level = '';
				$npDate = $benchApplication->np_id;
				if ($benchApplication->serving_notice_period_mode == 0){
					$npDate = $benchApplication->np_id." Days";
					if($npDate==1){
						$npDate = $benchApplication->np_id." Day";
					}
				}elseif($benchApplication->serving_notice_period_mode == 1) {
					$originalDate = $benchApplication->np_id;
					$npDate = '' . date("d M y", strtotime($originalDate));
					$level = "Available from ";
				}else{
					$npDate=$benchApplication->np_id;
				}

				@endphp
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ @$level }}</span> 
					<span class="{{ ($level) ? 'text-muted fw-bold d-block fs-7' : 'text-dark fw-bolder fs-6'}}">{{ @$npDate }}</span> 
					<span class="text-muted fw-bold d-block fs-7">{{ @$benchApplication->current_company }}</span> 
				</div>
			  </td>
              <td class="">
				@php
				$employment_type = 'Full-time';
                if($benchApplication->outsourcing_type==2){
                    $employment_type = 'Contract';
                }
				@endphp
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ ucwords(@$qualification->name) }}</span> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$employment_type }}">{{ @$employment_type }}</span> 
				</div>
			  </td>
              <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ @$row_city_name }}</span> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$row_state_name }} | {{ strtoupper(@$row_country_name)}} ">
						{{ substr(@$row_state_name, 0, 6) }} | {{ strtoupper(substr(@$row_country_name, 0, 2)) }}
					</span> 
				</div> 
			  </td>
              <td class="">
			  	<div class="d-flex flex-column">
                  <div class="symbol-hover"> {!! @$icon !!} </div>
                  <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$company->company_name) }}</span> 
				 </div>
			  </td>
              {{-- <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{ @$benchApplication->title }}">{{ substr(ucwords(@$benchApplication->title), 0, 10) }}</span> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$benchApplication->client_name }}">{{ substr(ucwords(@$benchApplication->client_name), 0, 10) }}</span> 
				</div>
			  </td> --}}
            </tr>
          </tbody>
        </table>
   
        <label class="text-dark mb-0 mt-3 fw-bolder fs-5">Status</label>
        <div class="d-flex flex-column mb-8">
			 <div class="row py-5 mb-3">
						@php
							if(!empty($master_status)){
								foreach($master_status as $mstatus){
									// dd($benchApplication->status);
									$master_status_css = $chk	=	$text_name	= "";
									if($mstatus->id == @$benchApplication->status ){
										$master_status_css  = @$mstatus->css_style;	
										$chk				= "checked";
									} 
									$text_name	= @$mstatus->text_name;   
									$result = GeneralMethod::bench_status_subline($mstatus->id);
									$subLine = ($result->name) ?? '';
									$classname = ($result->classname) ?? '';
						@endphp

						
                                    <div class="col-lg-2 master_status_div app_process" data-master-status-id="{{ @$mstatus->id }}" style="width: 20% !important;">
                                        <input type="radio" class="btn-check app_int_activity master_status" name="bench_master_status" value="{{ @$mstatus->id }}" id="chk_{{ @$mstatus->id }}" {{ @$chk }}>
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="chk_{{ @$mstatus->id }}">
                                            <span class="d-block fw-bold text-center {{ $classname }}">
                                                <span class="text-dark-800 fw-bolder d-block fs-5">{{ @$mstatus->name }}</span>
												<span class="text-muted fw-bold d-block fs-7">{{ $subLine }}</span>
                                            </span>
                                        </label>
                                    </div>

								         
						@php
									}
								}	 
						@endphp 
				</div> 
        </div> 


        		
<table id="bench_explore_listing" class="table align-middle table-row-dashed fs-6 gy-5" style="display:none">
	<thead>
			<tr class="fw-bolder text-muted">
			<th class="w-10px pe-0">
			<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
			&nbsp;
			</div>
			</th>
			<th class="d-flex align-items-center border-0">Jobs</th>
			<th class="" data-bs-toggle="tooltip" title="Experience">Exp.</th>
			<th class="">Salary</th>
			<th class="">Location</th>
			<th class="">Job Status</th>
			<th class="">Offer Amount</th>
			<th class="">Join Date</th>
			<th class="">Added Date</th>
			<th class="">Stage</th>
			<th class="">Talent Status</th>
			</tr>
	</thead>            
</table>

   <label class="text-dark mb-0 mt-3 fw-bolder fs-5">Description</label>
   <p>&nbsp;</p>
	<div class="description"></div>
       
      </div>
    </div>
  </div>
  <div class="modal-footer flex-center"> 
    <div class="div1">
      <button type="button" id="" class="btn btn-secondary me-3" data-dismiss="modal">Cancel</button>      
	   <button type="button" id="custom_bench_stage_submit" class="btn btn-primary custom_stage_submit custom_bench_stage_submit">Submit </button>
    </div>   
  </div>
</form>

<script>	
	
	$(document).ready(function(){
	
			var currentStatus	=	{{ @$benchApplication->status }};
			description(currentStatus);
			if(currentStatus == 3){
					$('#bench_explore_listing').show();
					statusTableLoad('load');
			}
		
			$('.master_status_div').click(function(e){
   
					var id 			= $(this).data('master-status-id');
					description(id);
					$("#master_status_"+id).attr('checked', true);
					$('#lbl_master_status_'+id).removeClass('placed_bg text-hover-primary');	
					$('#lbl_master_status_'+id).removeClass('active-bg text-hover-success');	
					$('#lbl_master_status_'+id).removeClass('hold-bg text-hover-warning');
					$('#lbl_master_status_'+id).removeClass('benchleft-bg text-hover-benchleft');	
					
					if(id == 1){
						$('#lbl_master_status_'+id).addClass('placed_bg text-hover-primary');	
					}
					else if(id == 2){
						$('#lbl_master_status_'+id).addClass('placed_bg text-hover-primary');	
					}
					else if(id == 3){
						$('#lbl_master_status_'+id).addClass('active-bg text-hover-success');	
						
					}
					else if(id == 4){
						$('#lbl_master_status_'+id).addClass('hold-bg text-hover-warning');	
					}
					else if(id == 5){
						$('#lbl_master_status_'+id).addClass('benchleft-bg text-hover-benchleft');	
					}		
			 });	
	});	
</script> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>	
<script>
 
   $(document).ready(function(){
   
				$('#custom_bench_stage_submit').click(function(e){
					
								var prevMasterStage			=	{{ @$prevMasterStage }};
								var currentMasterStatus		=	$('input[name="bench_master_status"]:checked').val();
																				
								if((prevMasterStage == currentMasterStatus )){
										Swal.fire("Warning", 'Same status cannot be updated, please select other option !', "info");	 	
								} 
								else{
									$('#custom_bench_stage_submit').prop('disabled',true);
									$('#custom_bench_stage_submit').text('Waiting..');
											$.ajax({
															url				: '{{route('service_company.bench-candidates.status_submit')}}',
															container		: '#status_frm',
															beforeSend		: function() { 	},
															type			: "POST",
															data			: $('#status_frm').serialize(),
															success			: function (response) {
																$('#custom_bench_stage_submit').prop('disabled',false);
									                           $('#custom_bench_stage_submit').text('Submit');
																									
																			if(response.status == 'success'){
																									// sweetAlert("Success", 'Application stage update successfully !', "success");	
																									
																									//  tableLoad('load');
																									
																				swal({
																				title: "Success",
																				text: "Status changed successfully !",
																				type: "success",
																				showCancelButton: false,
																				confirmButtonColor: '#DD6B55',
																				confirmButtonText: 'Ok',
																				cancelButtonText: "No, cancel it!",
																				closeOnConfirm: true,
																				closeOnCancel: false
																				},
																				function(isConfirm){

																				if (isConfirm){
																					$("#stage_sub_stage_close").click();
																					fetchBenchBenchCandidatesCandidateList();
																					}
																				});

																								}
																								else if (response.status == "info"){   
																									sweetAlert("Fail!", response.message, "error");	
																								}else if(response.status == "candidatevalidation"){
																									sweetAlert("Fail!", response.message, "error");	
																								}else if(response.status == "cvpending"){
																									sweetAlert("Fail!", response.message, "error");	
																								}
																						 
																				}
													}) 		
								}			
				});		 
   })
      
</script>
<script>
			function statusTableLoad(type)
			{
				           
									var bench_id       	=	{{ @$currentBenchId }};
									var stage  			=	'all';
									table 				=	$('#bench_explore_listing').dataTable({
										        serverSide: true, // Enable server-side processing
												responsive     	: 	true, 
												processing      : 	true,
												ordering		: 	false,     			
												destroy       	: 	true,   
												stateSave     	: 	true,      
												"bLengthChange" : 	false,   
												sDom			: 	'lrtip', 
												"showNEntries" 	: 	false, 
												"bInfo" 		: 	false,       
												"bPaginate"		: 	false, 
												ajax			: 	'{!! route('service_company.bench-candidates.bench_assign_to_job_listing') !!}?bench_id='+bench_id+'&stage='+stage,
											   "fnDrawCallback" : 	function (oSettings) {
																							$("body").tooltip({selector: '[data-toggle="tooltip"]' });
																	},
												"drawCallback"	:	function(settings) {}, 
						        
											  	"initComplete"	:	function( settings, json){ 
																			$('.clientStatusLoader').html('');
																			$('.jobLoader').html('');
																			$('.sourceLoader').html('');
																			$('.search_np_idLoader').html('');
																			$('.search_passportLoader').html('');
																			$('.statusLoader').html('');            
																	  }, 
												  "columnDefs"	:
																  [ 
																			{ "width": "50px", "targets": 0, "width": "4%", "class":""},
																			{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
																			{  "width": "50px"  , "targets": 2},		
																			{  "width": "100px", "targets": 3 },		
																			{  "width": "200px", "targets": 4 },
																			{  "width": "200px", "targets": 5 }, 
																			{  "width": "200px", "targets": 6 },  																			
																			{  "width": "50px" ,"targets": 7 },
																			{  "width": "200px" , "targets": 8 },	
																			{ "width": "20px", "targets": 9 },
																			{ "width": "20px", "targets": 10 }  	
																	  ], 

												   columns		: 		[             
																				{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
																				{data: 'Requisition', name: 'Requisition',  orderable: false, searchable: false, width: '15%'},						
																				{data: 'Exp', name: 'Exp', searchable : false, orderable : false, width: '15%'},						
																				{data: 'Salary', name: 'Salary', searchable : false, orderable : false,  width: '5%'},
																				{data: 'Location', name: 'Location',  searchable : false, orderable : false,  width: '10%'},  
																				{data: 'Status', name: 'Status', searchable : false, orderable : false},
																				{data: 'offeramount', name: 'offeramount', orderable : false},
																				{data: 'joindate', name: 'joindate', orderable : false}, 
																				{data: 'AddedDate', name: 'AddedDate',  searchable : false, orderable : false},   
																				{data: 'Stage', name: 'Stage', orderable: false},
																				{data: 'placed', name: 'placed', orderable: false}
																		]         
      
             }); 
           new $.fn.dataTable.FixedHeader(table); 		   
			} 


			function description(id){
				    
					if(id == 1){	
						$('.description').html("<p>*In the pool status, the added candidate's status will only be visible to company executives. Please change the status to 'Bench' to make your candidate listings visible to other company hiring team.*</p>");
					}
					else if(id == 2){	
						$('.description').html("<p>*In Bench Status, the added candidate will be listed on our Marketplace for job opportunities in other companies, either for contract positions on your company payroll or for full-time hiring on the hiring company's payroll.</p>");
					}
					else if(id == 3){	
						$('.description').html("<p>*In Placed Status, if you change the status to Placed, the candidate will be removed from the Bench listing on our platform and will not be visible to any other companies.</p><p>*You have to select the company for which the candidate is hired, either on contract or full-time payroll.</p> <p>*Once the contract is over, you can change the status back to Bench so that the candidate will be listed again on our platform and available for hire by other companies.<p>");
					}
					else if(id == 4){
						$('.description').html("<p>*In Pause Status, if the status is changed automatically, it means the candidate has been removed from the Bench listing on our platform after being shortlisted by up to 3 companies for job opportunities.</p> <p>*You can change the status back to Bench if you want to make the candidate available to more clients.</p>");		
					}
					else if(id == 5){
						$('.description').html("<p>*Exited candidates are considered no longer associated with the bench talent pool and will not be available for hiring.</p>");		
					}
			}
</script>
<script>
$(document).ready(function(){	
				$('.master_status_div').click(function(e){
								var checkedValue = $('.master_status:checked').val();
								if(checkedValue == 3){
									$('#bench_explore_listing').show();
									statusTableLoad('load');
								}else{
									$('#bench_explore_listing').hide();
								}																											
				})									
});
</script>