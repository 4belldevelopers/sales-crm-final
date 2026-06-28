@extends('layouts.product_company_layout') 
@push('head-script')
<style>
#send_email .modal-dialog{margin: .3rem auto!important;}
#fwd_resume .modal-dialog{margin: .3rem auto!important;}
@media  only screen and (min-width: 900px) and (max-width: 2560px){
.table td, .table th, .table tr{width: max-content!important;}
.table > :not(caption) > * > *{padding: 0rem;}
td:nth-child(n+2),
th:nth-child(n+2){width: revert!important;}
}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesBenchApplication{width: 1025px;}}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesBenchApplication{width: 1200px;}}
@media only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesCopy{width: 1025px;}}
@media only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesCopy{width: 1200px;}}
</style>
@endpush

@section('content')
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <a href="javascript:void(0);" class="text-primary">Bench list</a>
        </h5>
        <span class="text-gray-600" id="">000</span>
      </div>
    </div>
    <div class="d-flex align-items-center py-1">
      <div class="d-flex align-items-center position-relative my-1 me-3">
        <span class="svg-icon svg-icon-1 position-absolute ms-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
          <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
        </svg>
        </span>
        <input type="text" id="inputSearch" name="inputSearch"  class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>
      <div class="card-toolbar">
        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
          <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
            <span class="svg-icon svg-icon-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
              </svg>
            </span>
            Filter
          </button>
		  </div>
      </div>
    </div>
  </div>
</div>

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="card">
        <div class="card-body pt-3 px-5">	          
		<table class="table align-middle table-row-dashed fs-6 gy-5" id="benchTable">
          <thead>
              <tr class="fw-bolder text-muted" role="row">
                <th class="w-10px pe-0"><div class="form-check form-check-sm form-check-custom form-check-solid me-3"><input class="form-check-input" type="checkbox"  value="1"></div></th>
                <th class="">Name</th>
                <th class="">Exp</th>
                <th class="">Salary</th>
                <th class="">Qualifaction</th>
				<th class="">Location</th>
                <th class="">Status</th>				
                <th class="">Upload Date</th>
                <th class="">CP</th>
                <th class="">TOT</th>
				<th class="">SOU</th>
				<th class="">SCR</th>
                <th class="">EVA</th>
                <th class="">NEO</th>
				<th class="">OFF</th>
				<th class="">JOI</th>
				<th class="">REJ</th>
              </tr>
          </thead>
        </table
        ></div>
      </div>
    </div>
  </div>
</div>
<!--end::Content-->

<div id="kt_activitiesHM_toggle"></div>
<div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">HM | CP</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesHM_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesHM_body"> </div>
  </div>
</div>

<div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Bench Status</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"  id="stage_sub_stage_close">
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
        </div>
      </div>
      <div class="stage_body_content" id="stage_body_content"></div>
    </div>
  </div>
</div>
		  
@endsection


@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script>

 	$(document).ready(function() { 
        $('#inputSearch').keyup(function() {
            tableLoad('load');      
        });   
	});  
 	var table;
    tableLoad('load');        
    function tableLoad(type) 
   		{
			var		status 				=	'all';
			var		jobs 				=	'all';
			var		clients 			=	'all';
			var		source 				=	'all';
			var		search_passport 	=	'all';
			var		search_np_id 		=	'all'; 
			var		inputSearch 		=	$('#inputSearch').val();
              		table 				=	$('#benchTable').dataTable({
																			responsive     		:	true, 
																			processing      	:	true,
																			ordering			:	false, //disable ordering      			
																			destroy       		:	true,   
																			stateSave     		:	true,      
																			"bLengthChange"   	:	false,   
																			sDom				:	'lrtip',       
																			 ajax				:	'{!! route('product_company.bench-candidates.data') !!}?search='+inputSearch,
																		   "fnDrawCallback" 	:	function (oSettings) {
																														$("body").tooltip({
																																				selector: '[data-toggle="tooltip"]'
																																		  });
																									},
									   			             
																		   "columnDefs"			:	[
																											{"targets": 0 },
																											{"targets": 1, "class":"align-items-center" },
																											{"targets": 2},
																											{"targets": 3},
																											{"targets": 4},
																											{"targets": 5},
																											{"targets": 6},
																											{"targets": 7},
																											{"targets": 8},
																											{"targets": 9},
																											{"targets": 10},
																											{"targets": 11},
																											{"targets": 12},
																											{"targets": 13},
																											{"targets": 14},
																											{"targets": 15},
																											{"targets": 16}
																										],  		  		
																			columns				:	 [
																										{ data: 'chkbox', name:'chkbox',width: '1%', searchable : false, orderable: false },  
																										{ data: 'Name', name: 'Name', width: '10%', orderable: true },
																										{ data: 'Exp', name: 'Exp', width: '2%', orderable: true },
																										{ data: 'Salary', name: 'Salary', width: '5%', orderable: true },
																										
																										{ data: 'Qualifaction', name: 'Qualifaction', width: '2%', orderable: true },
																										{ data: 'Location', name: 'Location', width: '2%', orderable: true },
																										{ data: 'Status', name: 'Status', width: '2%', orderable: true },
																										{ data: 'Date', name: 'Date', width: '7%', orderable: true },
																										{ data: 'Assigned', name: 'Assigned', width: '2%', orderable: true },
																										{ data: 'TOT', name: 'TOT', width: '2%', orderable: true },
																										
																										{ data: 'SOU', name: 'SOU', width: '2%', orderable: true },	
																										{ data: 'SCR', name: 'SCR', width: '2%', orderable: true },	
																										
																										{ data: 'EVA', name: 'EVA', width: '2%', orderable: true },	
																										{ data: 'NEO', name: 'NEO', width: '2%', orderable: true },
																										{ data: 'OFF', name: 'OFF', width: '2%', orderable: true },
																										{ data: 'JOI', name: 'JOI', width: '2%', orderable: true },
																										{ data: 'REJ', name: 'REJ', width: '2%', orderable: true }																										
																								]
															   });			     
            		new $.fn.dataTable.FixedHeader(table);
	}
	
	table.on('click', '.stage_sub_stage', function ()   
      {
       			$('#stage_body_content').html('');
               var id       =	$(this).data('row-id');			   
               var url    	=	"{{ route('product_company.bench-candidates.bench_application_status',':id') }}";
               url      	=	url.replace(':id', id);
				   $.ajax({
								type  		: 'GET',
								url   		: url,
								success 	: function (response) {
														if (response.status == "success") {
															  $('#stage_body_content').html(response.view);
														}
												}
				   });
      });  
	table.on('click', '.hm-listing', function (){
   	
   		$('#kt_activitiesHM_toggle').trigger('click');		
   		var id    		=	$(this).data('row-id');
   		var url   		=	"{{ route('product_company.bench-candidates.hmlisting',':id') }}";
   		url     		=	url.replace(':id', id);
   	 	var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
															 $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
												},
   						success 	: function (response) {
																setTimeout(
																()=>{
																		if (response.status == "success")
																		{
																			$('#kt_activitiesHM_body').html(response.view);
																		}
																}, 500);		
   									}
   	  		});
   	});  	
</script>
@endpush
