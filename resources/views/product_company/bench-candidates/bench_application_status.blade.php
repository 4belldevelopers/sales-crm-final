@php 

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
  
   $prevMasterStage 	=	@$benchApplication->status;
	
   $bgTextDecore			=	$strVal	=	'';
   $str						=	trim(@$benchApplication->submitted_by_name);
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
   $icon	= '<div class="symbol symbol-circle symbol-25px overflow-hidden border-radius-0" style="float:left">
      <a href="javascript:void(0);" class="">
         <div class="symbol-label '.$bgTextDecore.' border-radius-5 fw-bolder" style="font-size:13px!important;" data-toggle="tooltip" title="'.@$company->company_name.'">'.ucwords($strVal).'</div>
      </a>
    </div>';
    
    if(!empty($company->logo_logical_path)){
   	$icon	= '<div class="symbol symbol-circle symbol-25px overflow-hidden border-radius-0">
   					<a href="javascript:void(0);">
   						<div class="symbol-label border-radius-5">
   						<img src="'.url('public/'.@$company->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
   						</div>
   					</a>
   				</div>';
    }	     			  
@endphp 

<form class="form fv-plugins-bootstrap fv-plugins-framework" id="status_frm" name="status_frm" method="post" enctype="multipart/form-data">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="bench_application_id" id="bench_application_id" value="{{ @$benchApplication->id }}" />
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
              <th class="">Qualifaction</th>
              <th class="">Location</th>
              <th class="">Source</th>
              <th class="">Requisition</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-bold">
            <tr class="">
              <td class="d-flex align-items-center border-bottom-0">{!! @$cicon !!}
                <div class="d-flex flex-column"> 
					<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="{{ @$benchApplication->full_name }}"> 
						{{ ucwords(substr(@$benchApplication->full_name, 0, 10)) }}
					</a> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$benchApplication->current_designation }}">
						{{ ucwords(substr(@$benchApplication->current_designation, 0, 10)) }}
					</span> 
				</div>
              </td>
              
			  <td class="">
			  	<div class="d-flex flex-column"> 
					<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">
						{{ @$benchApplication->phone }}
					</a> 
					<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="{{ @$benchApplication->email }}">
						{{ substr(@$benchApplication->email, 0, 10) }}
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
			  		<span class="text-dark fw-bolder fs-6">{!! @$pay_rate_amount !!}</span> 
					<span class="text-muted fw-bold d-block fs-7">{!! @$bill_rate_amount !!}</span> 
				</div>
			  </td>
              
			  <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ @$benchApplication->np_id }}</span> 
					<span class="text-muted fw-bold d-block fs-7">{{ @$benchApplication->current_company }}</span> 
				</div>
			  </td>
              
              <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ ucwords(@$qualification->name) }}</span> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$benchApplication->skills }}">{{ @$benchApplication->skills }}</span> 
				</div>
			  </td>

              <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ @$row_city_name }}</span> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$row_state_name }} | {{ strtoupper(@$row_country_name) }}">
						{{ substr(@$row_state_name, 0, 6) }} | {{ strtoupper(substr(@$row_country_name, 0, 2)) }}
					</span> 
				</div>
			  </td>

              <td class="">
			  	<div class="d-flex flex-column">
                  <div class="symbol-group symbol-hover"> {!! @$icon !!} </div>
                  <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$company->company_name) }}</span> 
				 </div>
			  </td>

              <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{ @$benchApplication->title }}">{{ substr(ucwords(@$benchApplication->title), 0, 10) }}</span> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$benchApplication->client_name }}">{{ substr(ucwords(@$benchApplication->client_name), 0, 10) }}</span> 
				</div>
			  </td>
            </tr>
          </tbody>
        </table>
       	   
        <label class="text-dark mb-0 mt-3 fw-bolder fs-5">Status</label>
        <div class="row py-5 w-100 d-inline-block justify-content-between align-items-center">        
         @if(!empty($master_status))
                @foreach($master_status as $mstatus)
                          @php  $master_status_css = $chk	=	$text_name	= "";  @endphp 
                              @if($mstatus->id == @$benchApplication->status )
                              		@php
                                 			$master_status_css  = @$mstatus->css_style;	
                                 			$chk				= 'checked="checked"';
									@endphp		
                              @endif 
                              @php $text_name	= @$mstatus->text_name; @endphp     
         
         <div class="d-inline-block w-12 px-2 master_status_div app_process" data-master-status-id="{{ @$mstatus->id }}">
            <input type="radio" class=" master_status" name="bench_master_status" value="{{ @$mstatus->id }}" {!! @$chk !!} id="chk_{{ @$mstatus->id }}">
				<span class="d-block fw-bold text-start"> 
					<span	class="text-dark-800 fw-bolder d-block fs-5">
						{!! @$mstatus->name !!}
					</span> 
				</span> 
		 </div>	
         	@endforeach
         @endif
        </div> 
        
		
		
<table id="bench_explore_listing" class="table align-middle table-row-dashed fs-6 gy-5" style="display:none">
	<thead>
			<tr class="fw-bolder text-muted">
			<th class="w-10px pe-0">
			<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
			&nbsp;
			</div>
			</th>
			<th class="d-flex align-items-center border-0">Requisition</th>
			<th class="" data-bs-toggle="tooltip" title="Experience">Exp.</th>
			<th class="">Salary</th>
			<th class="">Location</th>
			<th class="">Status</th>
			<th class="">Added Date</th>
			<th class="">Contact Person</th>
			<th class="">Interview</th>
			<th class="">Stage</th>
			</tr>
	</thead>            
</table>
       
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>	
<script>	
	
	$(document).ready(function(){
	
			var currentStatus	=	{{ @$benchApplication->status }};
			if(currentStatus == 3){
					$('#bench_explore_listing').show();
					statusTableLoad('load');
			}		
			$('.master_status_div').click(function(e){   
					var id 			= $(this).data('master-status-id');
					$("#master_status_"+id).attr('checked', true);					
					$('#lbl_master_status_'+id).removeClass('placed_bg text-hover-primary');	
					$('#lbl_master_status_'+id).removeClass('active-bg text-hover-success');	
					$('#lbl_master_status_'+id).removeClass('hold-bg text-hover-warning');						
					if(id == 1){
						$('#lbl_master_status_'+id).addClass('placed_bg text-hover-primary');	
					}else if(id == 2){
						$('#lbl_master_status_'+id).addClass('placed_bg text-hover-primary');	
					}else if(id == 3){
						$('#lbl_master_status_'+id).addClass('active-bg text-hover-success');	
					}else if(id == 4){
						$('#lbl_master_status_'+id).addClass('hold-bg text-hover-warning');							
					}		
			 });	
	});

   $(document).ready(function(){
   
				$('#custom_bench_stage_submit').click(function(e){
					
								var prevMasterStage			=	{{ @$prevMasterStage }};
								var currentMasterStatus		=	$('input[name="bench_master_status"]:checked').val();
																
								if((prevMasterStage == currentMasterStatus )){
										Swal.fire("Warning", 'Same status cannot be updated, please select other option !', "info");	 	
								} 
								else{
											$.ajax({
															url				: '{{route('product_company.bench-candidates.status_submit')}}',
															container		: '#status_frm',
															beforeSend		: function() { 	},
															type			: "POST",
															data			: $('#status_frm').serialize(),
															success			: function (response) {
																									
																								if(response.status == 'success'){
																									sweetAlert("Success", 'Application stage update successfully !', "success");	
																									tableLoad('load');	
																								}else if (response.status == "info"){   
																									sweetAlert("Fail!", response.message, "error");	
																								}
																						 
																				}
													}) 	
								}
				});		   
   })   
   
			function statusTableLoad(type)
			{           
									var bench_id       	=	{{ @$currentBenchId }};
									var stage  			=	'all';									        
									table 				=	$('#bench_explore_listing').dataTable({
  
												responsive     	:	true, 
												processing      :	true,
												ordering		:	false,     			
												destroy       	:	true,   
												stateSave     	:	true,      
												"bLengthChange" :	false,   
												sDom			:	'lrtip', 
												"showNEntries" 	:	false, 
												"bInfo" 		:	false,       
												"bPaginate"		:	false,
												ajax			:	'{!! route('product_company.bench-candidates.bench_assign_to_job_listing') !!}?bench_id='+bench_id+'&stage='+stage,
											   "fnDrawCallback" :	function (oSettings) { $("body").tooltip({selector: '[data-toggle="tooltip"]' });  },
											   "drawCallback"	:	function(settings) { }, 						
											  "initComplete"	:	function( settings, json){ 
																							$('.clientStatusLoader').html('');
																							$('.jobLoader').html('');
																							$('.sourceLoader').html('');
																							$('.search_np_idLoader').html('');
																							$('.search_passportLoader').html('');
																							$('.statusLoader').html('');            
											  						}, 
											  "columnDefs"		:
																	  [ 
																				{ "width": "50px", "targets": 0, "width": "4%", "class":""},
																				{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
																				{  "width": "50px"  , "targets": 2},		
																				{  "width": "100px", "targets": 3 },		
																				{  "width": "200px", "targets": 4 }, 
																				{  "width": "50px" ,"targets": 5 },
																				{  "width": "200px" , "targets": 6 },              
																				{  "width": "150px" , "targets": 7 },	
																				{  "width": "150px", "targets": 8 },	
																				{ "width": "20px", "targets": 9 } 	
																	], 
											   columns			: 	[             
																		{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
																		{data: 'Requisition', name: 'Requisition',  orderable: false, searchable: false, width: '15%'},						
																		{data: 'Exp', name: 'Exp', searchable : false, orderable : false, width: '15%'},						
																		{data: 'Salary', name: 'Salary', searchable : false, orderable : false,  width: '5%'},
																		{data: 'Location', name: 'Location',  searchable : false, orderable : false,  width: '10%'},   						
																		{data: 'Status', name: 'Status', searchable : false, orderable : false},
																		{data: 'AddedDate', name: 'AddedDate',  searchable : false, orderable : false},
																		{data: 'ContactPerson', name: 'ContactPerson', orderable: false},
																		{data: 'Interview', name: 'Interview', orderable : false},   
																		{data: 'Stage', name: 'Stage', orderable: false}
																	]         
      
             }); 
           new $.fn.dataTable.FixedHeader(table); 		   
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
