<link href="{{ asset('demo/css/css-circular-prog-bar.css') }}" rel="stylesheet">
<style>
</style>


<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>

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
          <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#recommended"> <span class="nav-text font-weight-bold recommended_tab">Recommended</span> </a> </li>
          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#registered"> <span class="nav-text font-weight-bold register_tab">Registered</span> </a> </li>
          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#assigned"> <span class="nav-text font-weight-bold assigned_tab">Assigned</span> </a> </li>
        </ul>
      </div>
    </div>
    <!--end::Tabs Header-->
    <!--Begin::Tabs Body-->
    <div class="card-body py-0">
      <div class="tab-content pt-5">
        <!--begin::Recommended Content-->
        <div class="tab-pane active show" id="recommended" role="tabpanel">
          
          <div class="card-body px-0 py-2">
		  
		  <table class="table align-middle table-row-dashed fs-6 gy-5" id="boostlisting">
              <thead>
                <tr class="fw-bolder text-muted">
                  <th class="w-10px"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                      <input class="form-check-input" type="checkbox" data-kt-check="true" value="1" />
                    </div></th>
                  <th class="min-w-150px">Name</th>
                  <th class="min-w-80px">Match</th>
                  <th class="min-w-50px">Similar Req.</th>
                  <th class="min-w-50px">Profiles</th>
                  <th class="min-w-100px">Hiring Type</th>
                  <th class="min-w-100px">Status</th>
                  <th class="min-w-100px">Action</th>
                </tr>
				
              </thead>
              </tbody>
            </table>
			
			
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="recommendedlist">
              <thead>
                <tr class="fw-bolder text-muted d-none">
                  <th class="w-10px"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-reco" value="1" />
                    </div></th>
                  <th class="min-w-150px">Name</th>
                  <th class="min-w-80px">Match</th>
                  <th class="min-w-50px">Similar Req.</th>
                  <th class="min-w-50px">Profiles</th>
                  <th class="min-w-100px">Hiring Type</th>
                  <th class="min-w-100px">Status</th>
                  <th class="min-w-100px">Action</th>
                </tr>
				<span id="boostkeywords"></span>
              </thead>
              </tbody>
            </table>
          </div>
        </div>
        <!--end::Recommended Content-->
		
        <!--begin::Registered Content-->
        	<div class="tab-pane" id="registered" role="tabpanel">
          
          <!--begin::Registered Content-->
          <div class="card-body px-0 py-2">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="staffingPartnersRegister">
              <thead>
                <tr class="fw-bolder text-muted">
                  <th class="w-10px"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-regi" value="1" />
                    </div></th>
                  <th class="min-w-150px">Name</th>
                  <th class="min-w-80px">Company Type</th>
                  <th class="min-w-100px">Status</th>
                  <th class="min-w-100px">Action</th>
                </tr>
              </thead>
            </table>
          </div>
          <!--end::Requisitions Content-->
        </div>
        <!--end::Requisitions Content-->
		
        <!--begin::Assigned Content-->
        	<div class="tab-pane" id="assigned" role="tabpanel">
          <!--begin::Working header-->
          
          <div class="card-body px-0 py-2">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="recommendedAssignList">
              <thead>
                <tr class="fw-bolder text-muted">
                  <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-staff" value="1" />
                    </div></th>
                  <th class="">Name</th>
                  <th class="">Contact Person</th>
                  <th class="">Upload Profile</th>
                  <th class="">Interview</th>
                  <th class="">Joinings</th>
                  <th class="">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <!--end::Assigned Content-->
		
      </div>
    </div>
    <!--end::Tabs Body-->
  </div>
  <!--end::Tabs-->
</div>
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>

<script>


			var boostlisting;
            boostlisting('load');	   
		   function boostlisting(type) 
		   {
		              
				var jobid          			= $('#current_job_id').val();
        
     			table 						= $('#boostlisting').dataTable({
      																			pageLength: 3, 
																				iDisplayLength:3,
																				
																				
																				/*responsive     	: true, 
																				processing			: true,
        																		serverSide			: true,
																				ordering			: false,    			
																				destroy       		: true,   
																				stateSave     		: true,      
																				"bLengthChange"   	: false,   
																				sDom				: 'lrtip', */
																				
																				responsive     		: true, 
																				processing			: true,
																				serverSide			: false, 
																				ordering			: false,
																				destroy       		: true,
																				stateSave     		: true,
																				"bLengthChange"   	: false, 
																				sDom				: 'lrtip',
																				"bInfo" 			: false,
																				bPaginate			: false, 
          																		
																				ajax				: '{!! route('product_company.jobs.boostkeywords') !!}?jobid='+jobid,
                          
																				          
               "fnDrawCallback" : function (oSettings) { 
			   												
															
															
															
															
/*var url   			= "{{ route('product_company.jobs.boostkeywords') }}"; 
$.ajax({
			type		: 'GET',
			url			: url,
			data		: {'jobid':jobid},
			success		: function (response) {

													if(response.status == 'success') {
														$('#recommendedlist').prepend(response.view);
													}				
											  }
});*/
															
														
									
									
									},
                    
			  "initComplete":function( settings, json){ }, 					  		  
			   columns: [             
							{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
							{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},
							{data: 'Match', name: 'Match', searchable : false, orderable : false, width: '15%'},
							{data: 'SimilarReq', name: 'SimilarReq', searchable : false, orderable : false,  width: '5%'},
							{data: 'Profiles', name: 'Profiles',  searchable : false, orderable : false,  width: '10%'},
							{data: 'HiringType', name: 'HiringType', searchable : false, orderable : false},
							{data: 'Status', name: 'Status',  searchable : false, orderable : false},
							{data: 'Action', name: 'Action', orderable: false}
							
						]         
      
             }); 
             new $.fn.dataTable.FixedHeader(boostlisting);
		   
			 var tbodyrecommendedlist = document.getElementById("recommendedlist");
			 var tbodyboostlisting = document.getElementById("boostlisting");
			 if (tbodyrecommendedlist.innerHTML.trim() !== "" || tbodyboostlisting.innerHTML.trim() !== "") {
					console.log("Tbody is empty");
					setTimeout(() => {
						$(".dataTables_empty").attr("style","display: none !important;")
						$(".dataTables_empty").hide();
					}, 500);
					
				}

		   } 
</script>

<script>		   	
    
	 		var table;
            tableLoad('load');	   
		   function tableLoad(type) 
		   {
		              
				var jobid          			= $('#current_job_id').val();
        
     			table 						= $('#recommendedlist').dataTable({
      
																				responsive     		: true, 
																				processing: true,
        																		serverSide: true,
																				ordering			: false, //disable ordering      			
																				destroy       		: true,   
																				stateSave     		: true,      
																				"bLengthChange"   	: false,   
																				sDom				: 'lrtip', 
																				
																				
																				      
				          
          																		ajax				: '{!! route('product_company.jobs.recommendedData') !!}?jobid='+jobid,
                          
																				/*language			: {
																										  "url": "<?php echo __("app.datatable") ?>"
																								  	  },*/           
               "fnDrawCallback" : function (oSettings) {
			   												
															
															
															
															
/*var url   			= "{{ route('product_company.jobs.boostkeywords') }}";
$.ajax({
			type		: 'GET',
			url			: url,
			data		: {'jobid':jobid},
			success		: function (response) {

													if(response.status == 'success') {
														$('#recommendedlist').prepend(response.view);
													}				
											  }
});*/
															
															
															
															
															//alert('calling');
															
                       										$("body").tooltip({
                           															selector: '[data-toggle="tooltip"]'
                       														  });
                   					
									
									
									
									
									
									
									
									
									
									
									
									
									
									
									
									},
                    
			  "initComplete":function( settings, json){ 
															//$('.clientStatusLoader').html('');
															//$('.jobLoader').html('');
															//$('.sourceLoader').html('');
															//$('.search_np_idLoader').html('');
															//$('.search_passportLoader').html('');
															//$('.statusLoader').html(''); 
													  }, 



 
											  		  
			   columns: [             
							{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false,  width: '5%'},            
							{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},
							{data: 'Match', name: 'Match', searchable : false, orderable : false, width: '15%'},
							{data: 'SimilarReq', name: 'SimilarReq', searchable : false, orderable : false,  width: '10%'},
							{data: 'Profiles', name: 'Profiles',  searchable : false, orderable : false,  width: '10%'},
							
							{data: 'HiringType', name: 'HiringType', searchable : false, orderable : false},
							{data: 'Status', name: 'Status',  searchable : false, orderable : false},
							{data: 'Action', name: 'Action', orderable: false}
							
						]         
      
             }); 
             new $.fn.dataTable.FixedHeader(table);

			 var tbodyrecommendedlist = document.getElementById("recommendedlist");
			 var tbodyboostlisting = document.getElementById("boostlisting");
			 if (tbodyrecommendedlist.innerHTML.trim() !== "" || tbodyboostlisting.innerHTML.trim() !== "") {
					console.log("Tbody is empty");
					setTimeout(() => {
						$(".dataTables_empty").attr("style","display: none !important;")
						$(".dataTables_empty").hide();
					}, 1500);
					
				}
		   
		   
		   } 
	
		   recommendedAssignList('load');
		   function recommendedAssignList(type) 
		   {
		              
				var jobid          			= $('#current_job_id').val();
     			table 						= $('#recommendedAssignList').dataTable({
      
																				responsive     		: true, 
																				processing      	: true,
																				ordering			: false, //disable ordering      			
																				destroy       		: true,   
																				stateSave     		: true,      
																				"bLengthChange"   	: false,   
																				sDom				: 'lrtip',       
				          
          																		ajax				: '{!! route('product_company.jobs.recommendedAssignList') !!}?jobid='+jobid,
                          
																				/*language			: {
																										"url": "<?php echo __("app.datatable") ?>"
																								  	  },*/           
               "fnDrawCallback" : function (oSettings) {
                       										$("body").tooltip({
                           															selector: '[data-toggle="tooltip"]'
                       														  });
                   					},
                    
			  "initComplete":function( settings, json){ 
															//$('.clientStatusLoader').html('');
															//$('.jobLoader').html('');
															//$('.sourceLoader').html('');
															//$('.search_np_idLoader').html('');
															//$('.search_passportLoader').html('');
															//$('.statusLoader').html(''); 
													  }, 		  
			   columns: [             
							{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
							{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},
							{data: 'ContactPerson', name: 'ContactPerson', searchable : false, orderable : false, width: '15%'},
							{data: 'UploadProfile', name: 'UploadProfile', searchable : false, orderable : false,  width: '5%'},
							{data: 'Interview', name: 'Interview',  searchable : false, orderable : false,  width: '10%'},
							{data: 'Joinings', name: 'Joinings', searchable : false, orderable : false},
							{data: 'Action', name: 'Action',  searchable : false, orderable : false},							
						]         
      
             }); 
             new $.fn.dataTable.FixedHeader(table);		   } 

		   staffingPartnersRegister('load');	   
		   function staffingPartnersRegister(type) 
		   {
		              
				var jobid          			= $('#current_job_id').val();
        
     			table 						= $('#staffingPartnersRegister').dataTable({
      
																				responsive     		: true, 
																				processing      	: true,
																				ordering			: false, //disable ordering      			
																				destroy       		: true,   
																				stateSave     		: true,      
																				"bLengthChange"   	: false,   
																				sDom				: 'lrtip',       
				          
          																		ajax				: '{!! route('product_company.jobs.staffingPartnersRegister') !!}?jobid='+jobid,  
                          
																				/*language			: {
																										"url": "<?php echo __("app.datatable") ?>"
																								  	  },  */         
               "fnDrawCallback" : function (oSettings) {
                       										$("body").tooltip({
                           															selector: '[data-toggle="tooltip"]'
                       														  });
                   					},
                    
			  "initComplete":function( settings, json){  
															//$('.clientStatusLoader').html('');
															//$('.jobLoader').html('');
															//$('.sourceLoader').html('');
															//$('.search_np_idLoader').html('');
															//$('.search_passportLoader').html('');
															//$('.statusLoader').html(''); 
													  }, 		  
			   columns: [             
							{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
							{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},
							{data: 'CompanyType', name: 'CompanyType', searchable : false, orderable : false, width: '15%'},
							{data: 'Status', name: 'Status',  searchable : false, orderable : false},
							{data: 'Action', name: 'Action', orderable: false}
							
						]         
      
             }); 
             new $.fn.dataTable.FixedHeader(table);
		   
		   
		   } 
		   
$('body').on('click', '.send_only_job_invitation', function(event){

		event.preventDefault();

		
		var id    				= $(this).data('row-jobid');  
		var fromcompany    		= $(this).data('row-fromcompany'); 
		var tocompany    		= $(this).data('row-tocompany'); 
		
		var url   				= "{{ route('product_company.jobs.send_only_job_invitation',':id') }}";
   		url     				=   url.replace(':id', id);
   		var baseurl     		= "{{ asset('assets/images/Loading2.gif') }}"; 
   			$.ajax({
   						type  	: 'GET',
   						url   	: url,
						data	: {'jobid': id, 'fromcompany': fromcompany, 'tocompany': tocompany },
   						beforeSend: function() {
                                     //$('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) 
   						{
   								
										$('#sp_list_'+tocompany).removeClass('btn-light-primary');
										$('#sp_list_'+tocompany).removeClass('send_job_recommanded_request');
										$('#sp_list_'+tocompany).removeClass('send_only_job_invitation');
										
										$('#sp_list_'+tocompany).addClass('btn-light-success');
										$('#sp_list_'+tocompany).text('Request Sent');
											
   										if (response.status == "success") 
   										{
   											//staffingPartnersRegister('load');
											Swal.fire("Success", response.message, "success");	
   										}
										if (response.status == "warning") 
   										{
   											//staffingPartnersRegister('load');
											Swal.fire("Warning", response.message, "warning");	
   										}
     		  					
   						}
   	  			});
				
				
		});


$('body').on('click', '.send_request_from_rsp', function ()
   	{
 
   		var id    			= $(this).data('row-id');
		var jobid    		= $(this).data('row-jobid'); 
		
		var url   		= "{{ route('product_company.jobs.rsp_job_request_send',':id') }}";
   		url     		=   url.replace(':id', id);
   		var baseurl     = "{{ asset('assets/images/Loading2.gif') }}"; 

   			$.ajax({
   						type  : 'GET',
   						url   : url,
						data	: {'jobid': jobid},
   						 beforeSend: function() {
                                     //$('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) 
   						{
   								
   										if (response.status == "success") 
   										{
   											tableLoad('load');
											Swal.fire("Success", response.message, "success");	
   										}
										if (response.status == "warning") 
   										{
   											tableLoad('load');
											Swal.fire("Warning", response.message, "warning");	
   										}
     		  					
   						}
   	  			});
   	}); 


$('body').on('click', '.send_job_recommanded_request', function ()
   	{
 		
		var from_company_id    		= $(this).data('row-id');
		var current_jobid    		= $(this).data('row-jobid'); 
		var id						= from_company_id;
 		//alert("call"+current_jobid); return false; 	
		
		var url   		= "{{ route('product_company.jobs.send_job_recommanded_request',':id') }}";
   		url     		=   url.replace(':id', id);
   		var baseurl     = "{{ asset('assets/images/Loading2.gif') }}"; 
   			$.ajax({
   						type  : 'GET',
   						url   : url,
						data	: {'from_company_id': from_company_id, 'current_jobid':current_jobid},
   						 beforeSend: function() {
                                     //$('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); 
                       	},
   						success : function (response) 
   						{
   								//alert('calling =' + response.status);
								
   										if (response.status == "success") 
   										{
											
											$('#sp_list_'+id).removeClass('btn-light-primary');
											$('#sp_list_'+id).removeClass('send_job_recommanded_request');
											$('#sp_list_'+id).removeClass('send_only_job_invitation');
											
											$('#sp_list_'+id).addClass('btn-light-success');
											$('#sp_list_'+id).text('Request Sent');
											
											Swal.fire("Success", response.message, "success");	
   										}
										if (response.status == "warning") 
   										{
   											tableLoad('load');
											Swal.fire("Warning", response.message, "warning");	
   										}
     		  					
   						}
   	  			});
   	}); 
		
</script>

<script>

	$('body').on('click', '.remove_assign_job', function(){

		var id    			= $(this).data('row-id');   
		var jobid    		= $(this).data('row-jobid');
			
		const swalWithBootstrapButtons = Swal.mixin({
		  customClass: {
							confirmButton	: 'btn btn-success',
							cancelButton	: 'btn btn-danger'
		  				},
		  					buttonsStyling	: false
		})
		swalWithBootstrapButtons.fire({
										  title				: 'Are you sure?',
										  text				: "You won't be able to revert this!",
										  icon				: 'warning',
										  showCancelButton	: true,
										  confirmButtonText	: 'Yes, Ignore it!',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true
									}).then((result) => {
									
		  if (result.isConfirmed) 
		  	{
				
			
		  
										var url   			= "{{ route('product_company.jobs.assignRejected') }}";
										$.ajax({
													type	: 'GET',
													url		: url,
													data	: {'id':id, 'jobid': jobid},
													success	: function (response) {
																						
																						if(response.status == 'success') {
													
																									recommendedAssignList('load');  
																									swalWithBootstrapButtons.fire(
																																	'Ignore!',
																																	'Selected request has been removed successfully.',
																																	'success'
																																)
																						}
													}
										});
		  
		  
		  	} 
			else if (result.dismiss === Swal.DismissReason.cancel) 
			{
				swalWithBootstrapButtons.fire(
			  									'Cancelled',
			  									'Selected request is safe :)',
			  									'error'
											 )
		  	}
		
		})});

</script>

<script>

$('body').on('click', '.remove_job', function(){

	
		var jobid    		=	$(this).data('row-jobid'); 
		var fromcompany    	=	$(this).data('row-fromcompany'); 
		var tocompany    	=	$(this).data('row-tocompany'); 
		var tabname    		=	$(this).data('row-tabname'); 
		//alert(tabname); 
		//return false; Assigned 
			
		const swalWithBootstrapButtons = Swal.mixin({
		  customClass: {
							confirmButton	: 'btn btn-success',
							cancelButton	: 'btn btn-danger'
		  				},
		  					buttonsStyling	: false
		})
		swalWithBootstrapButtons.fire({
										  title				: 'Are you sure?',
										  text				: "You won't be able to remove this!",
										  icon				: 'warning',
										  showCancelButton	: true,
										  confirmButtonText	: 'Yes, Ignore it!',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true
									}).then((result) => {
									
		  if (result.isConfirmed) 
		  	{
		  
										var url   			= "{{ route('product_company.jobs.removeJob') }}";
										$.ajax({
													type	: 'GET',
													url		: url,
													data	: {'jobid':jobid, 'fromcompany': fromcompany, 'tocompany':tocompany},
													success	: function (response) {
																						
																						if(response.status == 'success') {
																									
																										tableLoad('load');
																									//if(tabname =='Assigned'){
																										recommendedAssignList('load');  
																									//}else{
																										staffingPartnersRegister('load');
																									//}
																									
																									
																									swalWithBootstrapButtons.fire(
																																	'Removed!',
																																	'Selected request has been removed successfully.',
																																	'success'
																																);
																						}else{
																								
																								swalWithBootstrapButtons.fire(
																																'Cancelled',
																																'Selected request is safe :)',
																																'error'
																															 );
											 
											 
																						}
													}
										});
		  
		  
		  	} 
			else if (result.dismiss === Swal.DismissReason.cancel) 
			{
				swalWithBootstrapButtons.fire(
			  									'Cancelled',
			  									'Selected request is safe :)',
			  									'error'
											 )}}) });

$('body').on('click', '.resend_job_recommanded_request', function ()
   	{
 		
		var ename    				= $(this).data('row-ename');
		
		var to_company_id    		= $(this).data('row-id');
		var current_jobid    		= $(this).data('row-jobid'); 
		var id						= to_company_id;
 		//alert("call"+current_jobid); return false; 	
		
		var url   		= "{{ route('product_company.jobs.resend_job_recommanded_request',':id') }}";
   		url     		=   url.replace(':id', id);
   		var baseurl     = "{{ asset('assets/images/Loading2.gif') }}"; 
   			$.ajax({
   						type  : 'GET',
   						url   : url,
						data	: {'to_company_id': to_company_id, 'current_jobid':current_jobid, 'ename':ename},
   						 beforeSend: function() {
                                     //$('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response)  
   						{
   										
										tableLoad('load');
										recommendedAssignList('load');  
										staffingPartnersRegister('load');
																									
   										if (response.status == "success") 
   										{
											Swal.fire("Success", response.message, "success");	
   										}
										if (response.status == "warning") 
   										{
											Swal.fire("Warning", response.message, "warning");	
   										}
     		  					
   						}
   	  			});
   	}); 

</script>

