@extends('layouts.service_company_layout')
@push('head-script')
<style>
.border-radius-5 {border-radius: 5px!important; }
.ml-2 {margin-left: 2em;}
@media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr {width: max-content!important;}
  .table > :not(caption) > * > *{padding: 0.75em 0rem!important;}
  td:nth-child(n+2),
  th:nth-child(n+2){width: revert!important;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesClient{width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesClient{ width: 1200px;}
}
#kt_activitiesClient_close svg {
        color: red;
}
</style>
@endpush
@section('content')
@php	
    $tabactive = [
         'allClient_active'=>'',
         'allClient_url'=> route('service_company.clients.index'),
         'span_clientAll'=>'span_clientAll',
         'clientDirect_active'=>'',
         'clientDirect_url'=> url('service_company/clients/direct_client/0'),
         'span_clientDirect'=>'span_clientDirect',
         'customClient_active'=>'tabactive',
         'customClient_url'=> url('service_company/clients/custom_client/0'),
         'span_clientcustom'=>'span_clientCustom',
         'clientRequest_active'=>'',
         'clientRequest_url'=> url('service_company/clients/client_request/0'),
         'span_clientRequest'=>'span_clientRequest',
         'clientAll' =>@$clientDataCnt['clientAll'],
         'clientDirect' =>@$clientDataCnt['clientDirect'],
         'clientCustom' =>@$clientDataCnt['clientCustom'],
         'clientRequest' =>@$clientDataCnt['clientRequest'],
        ];
@endphp

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_client" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

      <div id="" class="container-fluid d-flex flex-stack second_header">
          @include('sections.service_company.clientHeader',['tabactive'=>$tabactive])
        </div>
        
        <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                  <div class="col-lg-4 d-flex align-items-center px-2" style="width:56%;position: relative;">
                     <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                     <div style="position: absolute; top: 0px; right: 36px;">
                      <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 8px;"></div>
                   </div>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 30%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                        <option value="Show All" selected="">Sort</option>
                        <option value="1">Newest to Oldest (Upload Date)</option>
                        <option value="2">Oldest to Newest (Upload Date)</option>
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 20%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdata()">
                        <option value="" selected="selected">Custom Client</option>
                     </select>
                  </div>
                  {{-- <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <span class="svg-icon svg-icon-2">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                              <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                           </svg>
                        </span>
                        Filter
                     </button>
                  </div> --}}
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 14%;">
                  @if(in_array("add_clients", $userPermissions))
                  <!--begin::Add Candidate-->
                  <a href="{{ url('service_company/clients/create') }}">
                  <button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" style="height: 43px;"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> Add Client </button>
                  </a> @endif
       
                    </div>
        </div>
        
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5"> @if(in_array("view_clients", $userPermissions))
          <!--begin::Table-->
          <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
          <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5" >
            <!--begin::Table head-->
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#myTable .form-check-input" value="1" />
                  </div></th>
                <th class="">Name</th>
                <th class="">Location</th>
                <th class="">Candidates</th>
                <th class="">Jobs</th>
                <th class="">Connected On</th>
                <th class="">Client</th>
                {{-- <th class="">Contact Person</th> --}}
              </tr>
            </thead>
            <!--end::Table head-->
            <tbody class="text-gray-600 fw-bold" id="allClients">
            </tbody>
          </table>
          <!--end::Table-->
          <div id="pagination"></div>
          @endif </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Internal Client</h3>
        <!--begin::seprator-->
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        <!--end::seprator-->
      </div>
      <!--end::Header-->
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesClient_close">
        <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
            <rect x="0" y="7" width="16" height="2" rx="1" />
            <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        </button>
      </div>
    </div>
    <!--end::Header-->
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
    <!--begin::Body-->
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script> 
<script>
    // var input2 = document.querySelector("#kt_tagify_2");
    // new Tagify(input2);
</script>
<script>

$(document).ready(function() {	
	$('#kt_tagify_2').keyup(function() {
		// table._fnDraw(); 
    fetchCandidates();
	});
});
 function filtersdataget(){
        		// table._fnDraw();
            fetchCandidates();
        	}

    // var table = $('#myTable').dataTable({
       
	  //  		responsive  	: true,
		// 	processing  	: true,
		// 	serverSide  	: true,
		// 	destroy   		: true,
		// 	ordering		: false, 		//disable ordering			
		// 	"bLengthChange" : false, 		//thought this line could hide the LengthMenu
		// 	sDom			: 'lrtip',
		// 	oLanguage 		: {sLengthMenu: "_MENU_",},	   
		// 	ajax			: {'url' : '{!! route('service_company.clients.custom_client_data') !!}', "data": function ( d ) { d.search = $('#inputSearch').val(),d.sortingfilter = $('#sortingfilter').val(),d.country = $('#country').val(),d.cus_search = $('#kt_tagify_2').val() } },			       
	  //  		"drawCallback"	: function( settings ) {
		// 							$('[data-toggle="tooltip1"]').tooltip();
		// 							$('[data-toggle="tooltip2"]').tooltip();					
		// 					},
		// 	"columnDefs"	:
		// 					  [ 
		// 								{ "width": "50px", "targets": 0, "width": "4%", "class":""},
		// 								{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" }																													
		// 					 ], 			  
    //     	columns			: [
		// 						{ data: 'chkbox', name: 'chkbox', searchable : false, orderable : false},
		// 						{ data: 'Name', name: 'Name', searchable : false, orderable : false},
		// 						{ data: 'Location', name: 'Location', searchable : false, orderable : false},
		// 						{ data: 'Joining', name: 'Joining', searchable : false, orderable : false},
		// 						{ data: 'Requisitions', name: 'Requisitions', Requisitions : false, orderable : false},
								
		// 						{ data: 'Connected', name: 'Connected', Requisitions : false, orderable : false},
		// 						{ data: 'Client', name: 'Client', Requisitions : false, orderable : false}
		// 						// { data: 'Contact', name: 'Contact', Requisitions : false, orderable : false}        		
		// 					 ],
		// 	//timeout			: 500000,
    //        	async			: false
    // });
    // new $.fn.dataTable.FixedHeader( table );




  $(document).on('click', '.show-detail', function ()
	{
	
		$('#kt_activitiesClient_toggle').trigger('click');
		var id    			=	$(this).data('row-id');
		var url   			=	"{{ route('service_company.clients.viewmore',':id') }}";
		url     			=	url.replace(':id', id);
		 var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
		$.ajax({
						type  		: 'GET',
						url   		: url,
						 beforeSend	: function() {
                                  $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    	},
						success 	: function (response) {
								$('#kt_activitiesClient_body').html(response.view);							
						}
	  			});
	}); 


  $(document).ready(function() {
            // $.noConflict();

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
            if($('#kt_tagify_2').val()!=''){
                // $('.loadingMessage').css({ display: 'block' });
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            $.ajax({
                url: `{!! route('service_company.clients.custom_client_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  custom_status : $('#status').val(),
                  search : $('#inputSearch').val(),
                  sortingfilter : $('#sortingfilter').val(),
                  cus_search : $('#kt_tagify_2').val(),
                  country:$('#country').val()
                },
                success: function(response) {
                  $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#allClients').html(response.html);
                    $('#custom_client_count').html(response.total);
                    $('#pagination').html(response.pagination);
                }
            });
        }



   
		
</script>
@endpush 