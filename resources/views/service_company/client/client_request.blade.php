@extends('layouts.service_company_layout')
@push('head-script')
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/clientList.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')

<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_client" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">

        <!-- HERO -->
        @include('sections.service_company.clientHero', ['activeTab' => 'request'])

        <div class="d-flex align-items-center flex-wrap custm-bg mb-0 d-none">
                  <div class="col-lg-4 d-flex align-items-center px-2" style="width:55%">
                     <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                        <option value="Show All" selected="">Sort</option>
                        <option value="1">Newest to Oldest (Upload Date)</option>
                        <option value="2">Oldest to Newest (Upload Date)</option>
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdata()">
                        <option value="110" selected="selected">Location</option>
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <span class="svg-icon svg-icon-2">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                              <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                           </svg>
                        </span>
                        Filter
                     </button>
                  </div>
        </div>
        
        <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
      <table id="myTabled" class="table align-middle table-row-dashed fs-6 gy-5">
       <thead>
              <tr class="fw-bolder text-muted">
                <th class="">Name</th>
                <th class="">Location</th>
                <th class="">Request Date</th>
                <th class="">Action</th>
              </tr>
        </thead>

            <tbody class="text-gray-600 fw-bold" id="requestJobLists">
            </tbody>

      </table>
	   <div id="pagination"></div>
    </div>
  </div>
   </div>
  </div>
</div>
<div id="kt_activitiesDirectClient_toggle"></div>
<div id="kt_activitiesDirectClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Direct Client</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesDirectClient_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesDirectClient_body"> &nbsp; </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script>
$(document).ready(function() {	
	$('#inputSearch').keyup(function() {
		// table._fnDraw(); 
		 fetchCandidates();
	}); 
});

    var table = $('#myTable').dataTable({
	   		responsive  	: true,
			processing  	: true,
			serverSide  	: true,
			destroy   		: true,
			ordering		: false, //disable ordering			
			"bLengthChange" : false, //thought this line could hide the LengthMenu 
			sDom			: 'lrtip',
			oLanguage 		: {sLengthMenu: "_MENU_",},
			ajax			: {'url' : '{!! route('service_company.clients.client_request_data') !!}', "data": function ( d ) {	d.search = $('#inputSearch').val() } },	
	   		"drawCallback": function( settings ) {
						$('[data-toggle="tooltip1"]').tooltip();
						$('[data-toggle="tooltip2"]').tooltip();				
				},				  
        	columns			: [
									{ data: 'chkbox', name: 'chkbox', searchable : false, orderable : false}     
        					  ],
			//timeout			: 500000,
           async			: false
    });
    // new $.fn.dataTable.FixedHeader( table );

 $(document).on('click', '.request_event', function (){
		var id    			= $(this).data('row-id');
		var event_id    	= $(this).data('row-event');
		var from_company    = $(this).data('row-from_company');		
		  	
if(event_id == -1)
{			
			
			
		const swalWithBootstrapButtons = Swal.mixin({
		  customClass: {
			confirmButton: 'btn btn-success',
			cancelButton: 'btn btn-danger'
		  },
		  buttonsStyling: false
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
		  if (result.isConfirmed) {
			
		  
										var url   			= "{{ route('service_company.clients.request_update') }}";
										$.ajax({
													type	: 'GET',
													url		: url,
													data	: {'invitation_id':id, 'event_id': event_id, 'from_company': from_company},
													success	: function (response) {
																									var clientAll		=	response.clientDataCnt.clientAll;
																									var clientDirect	=	response.clientDataCnt.clientDirect;
																									var clientCustom	=	response.clientDataCnt.clientCustom;
																									var clientRequest	=	response.clientDataCnt.clientRequest;
																									
																									$('#span_clientAll').html(clientAll);
																									$('#span_clientDirect').html(clientDirect);
																									$('#span_clientCustom').html(clientCustom);
																									$('#span_clientRequest').html(clientRequest);
																									
																						if(response.status == 'success') {
													
																									// table._fnDraw(); 
																									 fetchCandidates();
																									swalWithBootstrapButtons.fire(
																																	'Ignore!',
																																	response.message,
																																	'success'
																																);
																																location.reload();
																						}
													}
										});
		   
		  
		  } else if (
			result.dismiss === Swal.DismissReason.cancel
		  ) {
			swalWithBootstrapButtons.fire(
			  'Cancelled',
			  'Selected request is safe :)',
			  'error'
			)
		  }
		})
			
			
}
else
{

		const swalWithBootstrapButtons = Swal.mixin({
		  customClass: {
			confirmButton: 'btn btn-success',
			cancelButton: 'btn btn-danger'
		  },
		  buttonsStyling: false
		})
		swalWithBootstrapButtons.fire({
										  title				: 'Are you sure?',
										  text				: "You won't be able to revert this!",
										  icon				: 'warning',
										  showCancelButton	: true,
										  confirmButtonText	: 'Yes, Accept it!',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true
									}).then((result) => {
		  if (result.isConfirmed) {
		  
		  
		  
		  								var url   			= "{{ route('service_company.clients.request_update') }}";
										$.ajax({
													type	: 'GET',
													url		: url,
													data	: {'invitation_id':id, 'event_id': event_id, 'from_company': from_company},
													success	: function (response) {
																						fetchCandidates(); 
																						if(response.status == 'success') {
																				
																									var clientAll		=	response.clientDataCnt.clientAll;
																									var clientDirect	=	response.clientDataCnt.clientDirect;
																									var clientCustom	=	response.clientDataCnt.clientCustom;
																									var clientRequest	=	response.clientDataCnt.clientRequest;
																									
																									$('#span_clientAll').html(clientAll);
																									$('#span_clientDirect').html(clientDirect);
																									$('#span_clientCustom').html(clientCustom);
																									$('#span_clientRequest').html(clientRequest);
																									
																									// table._fnDraw(); 
																									 fetchCandidates();
																									swalWithBootstrapButtons.fire(
																																	  'Accept!',
																																	  response.message,
																																	  'success'
																																	);
																																	location.reload();
																						}
													}
										});
										
										
			
		  } else if (
			result.dismiss === Swal.DismissReason.cancel
		  ) {
			swalWithBootstrapButtons.fire(
			  'Cancelled',
			  'Selected request is safe :)',
			  'error'
			)
		  }
		})
}			
			
				  
}); 


$(document).on('click', '.show-detail-direct', function (){
	$('#kt_activitiesDirectClient_toggle').trigger('click');	
	var id    		=	$(this).data('row-id');
	var url   		=	"{{ route('service_company.clients.requestviewmore',':id') }}";
	url     		=	url.replace(':id', id);
	 var baseurl    =	"{{ asset('assets/images/Loading2.gif') }}"; 
	$.ajax({
					type  		: 'GET',
					url   		: url,
					beforeSend	: function() {
								$('#kt_activitiesDirectClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
					},
					success 	: function (response) {
								$('#kt_activitiesDirectClient_body').html(response.view);
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
                url: `{!! route('service_company.clients.client_request_data') !!}?page=${page}`,
                type: 'GET',
                data: {
                  search : $('#inputSearch').val()
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                  //   alert(response.total);
                    $('#requestJobLists').html(response.html);
                    $('#pagination').html(response.pagination);
                }
            });
        }



</script>
@endpush 