@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<style>
   @media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr
  {width: max-content!important;}
  .table > :not(caption) > * > *
  { padding: 0rem; }
  td:nth-child(n+2),
  th:nth-child(n+2)
  { width: revert!important;}
}

.table-custom-class tbody tr {
    height: 82px; /* Adjust the height as per your requirement */
    position: relative;
}
</style>

    
<link href="{{ asset('demo/css/joblist.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

@endpush
@section('content')
@php	
     use App\Helper\GeneralMethod;
     
      $productAllowCompany = GeneralMethod::companiesType();
      $tabactive = [
         'allRequisition_active'=>'',
         'allRequisition_url'=> url('service_company/jobs'),
         'allRequisition_id'=>'',
         'customRequisition_active'=>'',
         'customRequisition_url'=> url('service_company/jobs/custom_requisition/0'),
         'customRequisition_id'=>'',
         'clientRequisition_active'=>'',
         'clientRequisition_url'=> url('service_company/jobs/client_requisition/0'),
         'clientRequisition_id'=>'',
         'jobRequest_active'=>'tabactive',
         'jobRequest_url'=>'javascript:void(0);',
         'jobRequest_id'=>'span_jobRequest',
         'allRequisition' =>@$dataCnt['allRequisition'],
         'customRequisition' =>@$dataCnt['customRequisition'],
         'clientRequisition' =>@$dataCnt['clientRequisition'],
         'jobRequest' =>@$dataCnt['jobRequest'],
         'productAllowCompany' =>@$productAllowCompany,
        ];

        
        $searchbar = [
         'userPermissions'=>$userPermissions
        ];

     
@endphp

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_job" id="kt_content"  style="margin-top: -90px;">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

         <!-- CONTENT -->
         <div class="content">        

               <!-- HERO -->
               
               @include('sections.service_company.jobHeader',['tabactive'=>$tabactive])

               <!-- TABLE -->
               <div class="table-card">
                  <!-- Header -->
                  <div class="table-header">
                  <div class="th">Job Details</div>
                  <div class="th">
                     Experience
                     <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                        <rect
                        x="2"
                        y="7"
                        width="20"
                        height="14"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                        />
                        <path
                        d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"
                        stroke="currentColor"
                        stroke-width="1.8"
                        />
                     </svg>
                  </div>
                  <div class="th">Hiring &amp; Location</div>
                  <div class="th">Hiring Manager</div>
                  <div class="th">Date</div>
                  <div class="th">Terms</div>
                  <div class="th">
                     Action
                     <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                        <circle
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="1.8"
                        />
                        <path
                        d="M12 8v4M12 16h.01"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        />
                     </svg>
                  </div>
                  </div>

                  <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                  <div class="" id="requestJobLists"></div>

                  <!-- PAGINATION -->
                  <div id="pagination"></div>
                  
               </div>
               <!-- /table-card -->
         </div>
         <!--/content -->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->



<div id="kt_activitiesClientjob_toggle"></div> 
<div id="kt_activitiesClientjob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClientjob_toggle" data-kt-drawer-close="#kt_activitiesClientjob_close">
  <div class="card shadow-none rounded-0 w-100">
   
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Direct Client Requisition</h3>
        
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        
      </div>
     
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesClientjob_close">
        
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span>
       
        </button>
      </div>
    </div>
    
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesclientjob_body"> </div>
   
  </div>
</div>




<div id="kt_activitiesHMClient_toggle"></div>
<div id="kt_activitiesHMClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesHMClient_toggle" data-kt-drawer-close="#kt_activitiesHMClient_close">
   <div class="card shadow-none rounded-0 w-100">
      <div class="card-header px-5 py-0" id="kt_activities_header">
         <div class="d-flex align-items-center py-1">
            <h3 class="card-title fw-bolder text-dark" id="company_or_client">Direct Client Contact Person</h3>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> 
         </div>
         <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesHMClient_close">
               <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                     <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                        <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
                        <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
                     </g>
                  </svg>
               </span>
            </button>
         </div>
      </div>
      <div class="card-body position-relative px-0 py-0" id="kt_activitiesHMClient_body"> </div>
   </div>
</div>
<!--end::Content-->


<!--start terms::Content-->


<!--end terms ::Content-->




@endsection

@push('footer-script') 
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
$(document).ready(function() {	
				$('#inputSearch').keyup(function() {
					   	//  tableLoad('load');
               fetchCandidates();      
		   		});
   });
   
var table;
// tableLoad('load');      
function tableLoad(type) 
	{
			table 						= $('#job_request').dataTable({
  
																			responsive     		: true, 
																			processing      	: true,
																			ordering			: false, //disable ordering      			
																			destroy       		: true,   
																			stateSave     		: true,      
																			"bLengthChange"   	: false,   
																			sDom				: 'lrtip',       
																			
			ajax			: 	{'url' : '{!! route('service_company.jobs.recommandedrequest') !!}', "data": function ( d ){ d.search = $('#inputSearch').val() } },           
		   "fnDrawCallback" : 	function (oSettings) {
														$("body").tooltip({selector: '[data-toggle="tooltip"]' });
								},				
		  "initComplete"	:	function( settings, json){ }, 		  
		   columns			: [             
									{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
									{data: 'Requisition', name: 'Requisition',  orderable: false, searchable: false, width: '15%'},
									{data: 'Exp', name: 'Exp', searchable : false, orderable : false, width: '15%'},
									{data: 'Salary', name: 'Salary', searchable : false, orderable : false,  width: '5%'},
									{data: 'Location', name: 'Location',  searchable : false, orderable : false,  width: '10%'},
									{data: 'Status', name: 'Status', searchable : false, orderable : false},
									{data: 'Date', name: 'Date',  searchable : false, orderable : false},
									{data: 'HM', name: 'HM', orderable: false},
                  {data: 'Terms', name: 'Terms', orderable: false},
									{data: 'Action', name: 'Action', orderable: false}
								
							 ]         
		 }); 
		 new $.fn.dataTable.FixedHeader(table);
	   
	   } 

     $(document).on('click', '.client-show-detail', function ()
   		{
			$('#kt_activitiesClientjob_toggle').trigger('click');	
			var id    		=	$(this).data('row-id');
      // var url   		=	"{{ route('service_company.jobs.clientviewmore',':id') }}";
			var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?bechshowtab=1";
			url     		=	url.replace(':id', id);
			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
			$.ajax({
							type  		: 'GET',
							url   		: url,
							 beforeSend	: function() {
										 $('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
							},
							success 	: function (response) {
									$('#kt_activitiesclientjob_body').html(response.view);									
							}
					});
   		});

       $(document).on('click', '.client-hm-listing', function (){
   		$('#kt_activitiesHMClient_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.jobs.clienthmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesHMClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
   				$('#kt_activitiesHMClient_body').html(response.view);
               $('#company_or_client').text(response.company_or_client);			
   						}
   	  			});
   	});

     

    $(document).on('click', '.request_reject', function ()
   	{
   		
   		var jobId    					=	$(this).data('row-id');
		var recommendation_relation_id  =	$(this).data('row-recommendation_relation_id');
		var status    					=	$(this).data('row-status');
		
   		var url   						=	"{{ route('service_company.jobs.requestapproval',':jobId') }}";
   		url     						=	url.replace(':jobId', jobId);	
		var baseurl    					=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		
		                     
													const swalWithBootstrapButtons = Swal.mixin({
																									customClass: {
																														confirmButton	: 'btn btn-danger',
																														cancelButton	: 'btn btn-info'
																												},
																									buttonsStyling	: false
													})
													swalWithBootstrapButtons.fire({
																					  title				: 'Are you sure?',
																					  text				: "You won't be able to revert this!",
																					  icon				: 'warning',
																					  showCancelButton	: true,
																					  confirmButtonText	: 'Yes, Reject',
																					  cancelButtonText	: 'No, cancel!',
																					  reverseButtons	: true
																				})
													.then((result) => {
																				
													  if (result.isConfirmed) {
															
																$.ajax({
																			type  		: 'GET',
																			url   		: url,
																			data		: {'jobId': jobId, 'rec_relation_id': recommendation_relation_id, 'status':status },
																			success 	: function (response) {
																				$('#span_allRequisition').html(response.dataCnt.allRequisition);
																				$('#span_clientRequisition').html(response.dataCnt.clientRequisition);
																				$('#span_customRequisition').html(response.dataCnt.customRequisition);
																				$('#span_jobRequest').html(response.dataCnt.jobRequest);
																																											
																							
																						if(status == 'success'){
																							swalWithBootstrapButtons.fire
																							('Accept!', 'Selected request has been accepted successfully.', 'success')	
																						}else if(status == 'ignore'){
																							swalWithBootstrapButtons.fire
																							('Ignore!', 'Selected request has been ignored successfully.', 'success')
																						}else if(status == 'ignoreClient'){
																							swalWithBootstrapButtons.fire
																							('Ignore!', 'Selected request has been ignored successfully.', 'success')
																						}			
                                            // table._fnDraw();
																						// tableLoad('load');	
                                            fetchCandidates();																							
																			}
																	});
														} 
														else if (result.dismiss === Swal.DismissReason.cancel) {
															swalWithBootstrapButtons.fire('Cancelled', 'Selected request is safe :)', 'error')
														}
													
													});
                        });  

  $(document).on('click', '.request_approval', function () {
        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        var jobId                    = $btn.data('row-id');
        var recommendation_relation_id = $btn.data('row-recommendation_relation_id');
        var status                   = $btn.data('row-status');
        var originalHtml             = $btn.html();

        var url = "{{ route('service_company.jobs.requestapproval',':jobId') }}";
        url = url.replace(':jobId', jobId);

        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Please wait...');

        $.ajax({
            type: 'GET',
            url: url,
            data: { 'jobId': jobId, 'rec_relation_id': recommendation_relation_id, 'status': status },
            success: function (response) {
                fetchCandidates();
            },
            error: function () {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
  });




      $(document).ready(function() {

            fetchCandidates();
            
            $(document).on('click', '#pagination .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchCandidates(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#pagination .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#pagination .page-link').removeClass('disabled');
                });
            });

      });

      function fetchCandidates(page = 1, search = '') {
            var clientid = '1';
            $('.loadingMessage').css({ display: 'block' });
            $.ajax({
                url: `{!! route('service_company.jobs.recommandedrequest_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  search : $('#inputSearch').val()
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                  //   alert(response.total);
                    $('#requestJobLists').html(response.html);
                    $('#span_jobRequest').html(response.total);
                    $('#pagination').html(response.pagination);
                }
            });
        }
																      
</script>
@endpush

