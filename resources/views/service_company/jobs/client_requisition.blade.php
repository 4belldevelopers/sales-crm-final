@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<style>
   @media only screen and (min-width: 900px) and (max-width: 2560px){
   .table td, .table th, .table tr{width: max-content!important;}
   .table > :not(caption) > * > *{padding: 0rem;}
   td:nth-child(n+2),
   th:nth-child(n+2){width: revert!important;}
   }
   @media only screen and (min-width: 1024px) and (max-width: 1440px){
   #kt_activitiesJob{width: 1025px;}
   }
   @media only screen and (min-width: 1440px) and (max-width: 2560px){
   #kt_activitiesJob{width: 1200px;}
   }
   .select2-container--bootstrap5 .select2-dropdown .select2-results>.select2-results__options {background: #f5f8fa;,border: 1px solid #e1e1e1;}
   
   #myTable tbody tr {
    height: 82px; /* Adjust the height as per your requirement */
    position: relative;
}

.hidden-button {
  display: none; /* Initially hidden */
  position: absolute;
  bottom: 10px; /* Adjust position as needed */
  left: 50%;
  transform: translateX(-50%);
  padding: 10px 20px;
  background-color: #007BFF;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.show_hidden:hover .hidden-button {
  display: block; /* Show the button on hover */
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
         'customRequisition_url'=>url('service_company/jobs/custom_requisition/0'),
         'customRequisition_id'=>'',
         'clientRequisition_active'=>'tabactive',
         'clientRequisition_url'=> 'javascript:void(0);',
         'clientRequisition_id'=>'client_jobs_count',
         'jobRequest_active'=>'',
         'jobRequest_url'=> url('service_company/jobs/job_request/0'),
         'jobRequest_id'=>'',
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
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_job" id="kt_content" style="margin-top: -90px;">
   <!--begin::Post-->
   <div class="post d-flex flex-column-fluid" id="kt_post">
      <!--begin::Container-->
      <div id="kt_content_container" class="container-fluid px-3">

         <!-- CONTENT -->
         <div class="content">        

               <!-- HERO -->
               
               @include('sections.service_company.jobHeader',['tabactive'=>$tabactive])

               <!-- FILTER BAR -->
               @include('sections.service_company.jobFilterBar',['searchbar'=>$searchbar])

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
                  <div class="th">Location</div>
                  <div class="th">Hiring Manager</div>
                  <div class="th">Team</div>
                  <div class="th">Agency</div>
                  <div class="th">
                     Hiring Pipeline
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
                  <div class="" id="clientJobLists"></div>

                  <!-- PAGINATION -->
                  <div id="pagination"></div>

                  <!-- LEGEND -->
                  @include('sections.service_company.pipeline')
                  
               </div>
               <!-- /table-card -->
         </div>
         <!--/content -->
      </div>
      <!--end::Container-->
   </div>
   <!--end::Post-->
</div>
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
<div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesHMClient_toggle" data-kt-drawer-close="#kt_activitiesHMClient_close">
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
<div class="modal fade bs-modal-sm in" id="scheduleDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md" id="modal-data-application">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingStatus"></span> 
         </div>
         <div class="modal-body"> Loading... </div>
         <div class="modal-footer">
            <button type="button" class="btn default" data-dismiss="modal">Close</button>
            <button type="button" class="btn blue">Save changes</button>
         </div>
      </div>
   </div>
</div>
<div class="modal fade bs-modal-lg in" id="JobDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" id="modal-data-application" style="margin:.5rem auto;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <span class="caption-subject font-red-sunglo bold uppercase" id="JobDetailModalStatusData"></span> 
         </div>
      </div>
   </div>
</div>
<div class="modal fade bs-modal-md in" id="scheduleDetailModal_vis" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md" id="modal-data-application" style="margin:.5rem auto;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_vis"></span> 
         </div>
      </div>
   </div>
</div>
<div id="kt_activitiesTEAM_toggle"></div>
<div id="kt_activitiesTEAM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesTEAM_toggle" data-kt-drawer-close="#kt_activitiesTEAM_close">
   <div class="card shadow-none rounded-0 w-100">
      <div class="card-header px-5 py-0" id="kt_activities_header">
         <div class="d-flex align-items-center py-1">
            <h3 class="card-title fw-bolder text-dark">TEAM Listing</h3>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> 
         </div>
         <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesTEAM_close">
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
      <div class="card-body position-relative px-0 py-0" id="kt_activitiesTEAM_body"> </div>
   </div>
</div>
<!--end::Staffing Partners sidebar-->
@endsection
@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
   $(document).ready(function() {	
   	   $('#inputSearch').keyup(function() {
   			table._fnDraw();
   	   });
   });
</script>
<!--<link href="https://preview.keenthemes.com/html/keen/docs/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />-->
<!--<script src="https://preview.keenthemes.com/html/keen/docs/assets/plugins/global/plugins.bundle.js"></script>-->
<script>
//   var input1 = document.querySelector("#kt_tagify_1");
   // var input2 = document.querySelector("#kt_tagify_2");
   
//   new Tagify(input1);
   // new Tagify(input2);
</script>
<script>
   // var table = $('#myTables').dataTable({
   
   // 			responsive  	:	true,
   // 			processing  	:	true,
   // 			serverSide  	:	true,
   // 			destroy   		:	true,
   // 			ordering		:	false, 		//disable ordering
   // 			"bLengthChange" :	false, 		//thought this line could hide the LengthMenu
   // 			sDom			:	'lrtip',
   // 			oLanguage 		:	{sLengthMenu: "_MENU_",},   		
   //    		ajax			: {'url' : '{!! route('service_company.jobs.client_requisition_data') !!}',"data": function ( d ){d.search = $('#inputSearch').val(),d.custom_search = $('#kt_tagify_2').val(),d.custom_status = $('#status').val(),d.custom_hiringtype = $('#hiringtype').val()}},
   // 			"drawCallback"	: function( settings ) {   				
   // 							$('[data-toggle="tooltip1"]').tooltip();
   // 							$('[data-toggle="tooltip2"]').tooltip();
   // 							$('[data-toggle="tooltip3"]').tooltip();
   // 							$('[data-toggle="tooltip4"]').tooltip();   						
   // 					},
   // 			"columnDefs"	:
   // 				    [ 
   // 							{ "width": "50px", "targets": 0, "width": "4%", "class":""},
   // 							{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
   // 							{  "width": "50px"  , "targets": 2},		
   // 							{  "width": "100px", "targets": 3 },		
   // 							{  "width": "200px", "targets": 4 }, 
   							
   // 							{  "width": "50px" ,"targets": 5 },
   // 							{  "width": "200px" , "targets": 6 },              
   // 							{  "width": "150px" , "targets": 7 },	
   // 							{  "width": "150px", "targets": 8 },	
   // 							{ "width": "20px", "targets": 9 },
   								
   // 							{ "width": "20px", "targets": 10 }, 
   // 							{ "width": "20px", "targets": 11 },    
   // 							{ "width": "20px", "targets": 12 },             
   // 							{ "width": "20px", "targets": 13 },
   // 							{ "width": "20px", "targets": 14 }, 
   							  
   // 							{ "width": "20px", "targets": 15 },
   // 							{ "width": "20px", "targets": 16 }  	
   // 					 ], 
   // 				columns		:
   // 					[
   // 						{ data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
   // 						{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
   // 						{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
   // 						{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
   // 						{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
   						
   // 						{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
   // 						{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
   // 						{ data: 'HM', name: 'HM' , orderable: false, searchable: false},
   // 						{ data: 'Team', name: 'Team' , orderable: false, searchable: false},
   // 						{ data: 'TOT', name: 'TOT' , orderable: false, searchable: false},
   						
   // 						{ data: 'SOU', name: 'SOU' , orderable: false, searchable: false},					
   // 						{ data: 'SCR', name: 'SCR' , orderable: false, searchable: false},
   // 						{ data: 'EVA', name: 'EVA' , orderable: false, searchable: false},
   // 						{ data: 'NEO', name: 'NEO' , orderable: false, searchable: false},
   // 						{ data: 'OFF', name: 'OFF' , orderable: false, searchable: false},
   						
   // 						{ data: 'JOI', name: 'JOI' , orderable: false, searchable: false},
   // 						{ data: 'REJ', name: 'REJ' , orderable: false, searchable: false}	         	   
   // 					],
   // 	//timeout: 500000,
   //         async: false
   // 				}); 
   // new $.fn.dataTable.FixedHeader( table ); 
   $(document).on('keyup','.inputJobSearch',function(){
   	   // table._fnDraw();
         fetchCandidates();
   	});
   
   	$(document).on('change','#status,#hiringtype',function(){
   	   // table._fnDraw();
         fetchCandidates();
   	});
   
      $(document).on('click', '.client-show-detail', function (){
   	$('#kt_activitiesClientjob_toggle').trigger('click');	
   	var id    		=	$(this).data('row-id');
        // var url   		=	"{{ route('service_company.jobs.clientviewmore',':id') }}";
   	var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?viewmore=clientviewmore";
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
   
      $(document).on('click', '.team-listing', function (){
   		$('#kt_activitiesTEAM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.jobs.currentteamlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activitiesTEAM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) {
   					$('#kt_activitiesTEAM_body').html(response.view);     		  					
   						}
   	  			});
   	});

      $(document).ready(function() {
            // $.noConflict();

            fetchCandidates();
            
            $(document).on('click', '#pagination .pagi-page, #pagination .pagi-arrow', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;

                fetchCandidates(page, $('#search').val());
            });

            $(document).on('change', '#pagination .pagi-perpage', function() {
                var selectedPerPage = $(this).val();
                localStorage.setItem('candidatePerPage', selectedPerPage);
                localStorage.setItem('currentPageClientReq', 1);
                page_no = 1;
                fetchCandidates(1);
            });

      });

      var page_no = localStorage.getItem('currentPageClientReq') || 1;

      function fetchCandidates(page, search = '') {
            var targetPage = page || page_no || 1;
            page_no = targetPage;
            localStorage.setItem('currentPageClientReq', targetPage);

            var perPage = $('#pagination .pagi-perpage').val() || localStorage.getItem('candidatePerPage') || 10;
            var clientid = '1';

            $('.loadingMessage').css({ display: 'block' });
            $.ajax({
                url: `{!! route('service_company.jobs.client_requisition_data_ajax') !!}?page=${targetPage}`,
                type: 'GET',
                data: {
                  custom_status : $('#status').val(),
                  search : $('#inputSearch').val(),
                  custom_hiringtype : $('#hiringtype').val(),
                  employment_type : $('#employment_type').val(),
                  custom_search : $('#kt_tagify_2').val(),
                  size: perPage,
                  per_page: perPage
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    localStorage.setItem('currentPageClientReq', targetPage);
                    $('#clientJobLists').html(response.html);
                    $('#client_jobs_count').html(response.total);
                    $('#pagination').html(response.pagination);
                }
            });
        }

     
</script>
@endpush
