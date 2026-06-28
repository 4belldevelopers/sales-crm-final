@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<style>
body {overflow-x:hidden;}
</style>
@endpush
@section('content')
@php 

		$sp_url   		=	route('service_company.consultancy.suggestedPartners',':id');
		$sp_url 		=	str_replace(':id', 2, $sp_url);
		
		$rp_url   		=	route('service_company.consultancy.requestPartners',':id');
		$rp_url 		=	str_replace(':id', 2, $rp_url);
				
@endphp

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
   
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{route('service_company.consultancy.index')}}" class="text-gray-800 text-hover-primary mb-1">Staffing Partners</a> </h5>
        <span class="text-gray-600">{{ @$spartnerDataCnt['spartnerAll'] }}</span> </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      {{-- <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ @$sp_url }}" class="text-gray-800 text-hover-primary mb-1">Suggested Partners</a> </h5>
        <span class="text-gray-600" id="span_suggestedPartners">{{ @$spartnerDataCnt['sentRequest'] }}</span> 
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span> --}}
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-primary mb-1">Request</a> </h5>
        <span class="text-gray-600" id="span_sentRequest">{{ @$spartnerDataCnt['sentRequest'] }}</span> </div>
    </div>
    <!--end::Page title-->
	
    <!--begin::Actions-->
    <div class="d-flex align-items-center py-1">
      <!--begin::Search-->
      <div class="d-flex align-items-center position-relative my-1 me-3">
        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
      
        <!--end::Svg Icon-->
        <input type="hidden" name="inputSearch" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>
      <!--end::Search-->
      <div class="card-toolbar">
        <!--begin::Toolbar-->
        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
          <!--begin::Filter-->
         
          <!--begin::Menu 1-->
          <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
            <!--begin::Header-->
            <div class="px-7 py-5">
              <div class="fs-5 text-dark fw-bolder">Filter Options</div>
            </div>
            <!--end::Header-->
            <!--begin::Separator-->
            <div class="separator border-gray-200"></div>
            <!--end::Separator-->
            <!--begin::Content-->
            <div class="px-7 py-5" data-kt-user-table-filter="form">
              <!--begin::Input group-->
              <div class="mb-10">
                <label class="form-label fs-6 fw-bold">Role:</label>
                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="role" data-hide-search="true">
                  <option></option>
                  <option value="Administrator">Administrator</option>
                  <option value="Analyst">Analyst</option>
                  <option value="Developer">Developer</option>
                  <option value="Support">Support</option>
                  <option value="Trial">Trial</option>
                </select>
              </div>
              <!--end::Input group-->
              <!--begin::Input group-->
              <div class="mb-10">
                <label class="form-label fs-6 fw-bold">Two Step Verification:</label>
                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="two-step" data-hide-search="true">
                  <option></option>
                  <option value="Enabled">Enabled</option>
                </select>
              </div>
              <!--end::Input group-->
              <!--begin::Actions-->
              <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                <button type="submit" class="btn btn-primary fw-bold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
              </div>
              <!--end::Actions-->
            </div>
            <!--end::Content-->
          </div>
          <!--end::Menu 1-->
          <!--end::Filter-->
          <!--begin::Add  Staffing Partners<-->
		
          <!--end::Add  Staffing Partners<-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Group actions-->
        <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
          <div class="fw-bolder me-5"> <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
          <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
        </div>
      </div>
    </div>
    <!--end::Actions-->
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-xxl px-2">
        
        <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                  <div class="col-lg-4 d-flex align-items-center px-2" style="width:50%">
                     <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                        <option value="Show All" selected="">Sort</option>
                        <option value="1">Newest to Oldest (Upload Date)</option>
                        <option value="2">Oldest to Newest (Upload Date)</option>>
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                        <option value="" selected="">Company Type</option>
                        <option value="1">Staffing Company</option>
                        <option value="2">Service Company</option>
                        <option value="3">Product Company</option>
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
                  
                  <!--<div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">-->
                  <!--     @if(in_array("add_staffing_partners", $userPermissions)) -->
                		<!--	<a href="javascript:void(0);" type="button" class="btn btn-sm btn-primary add_staffing_partners_frm" data-toggle="modal" data-target="#add_staffing_partners"> <span class="svg-icon svg-icon-primary svg-icon-1x">-->
                		<!--		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">-->
                		<!--		<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">-->
                		<!--		  <polygon points="0 0 24 0 24 24 0 24"/>-->
                		<!--		  <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>-->
                		<!--		  <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>-->
                		<!--		</g>-->
                		<!--		</svg>-->
                		<!--		</span> Add Staffing Partners -->
                		<!--	</a> -->
                		<!--@endif-->
                  <!--</div>-->
   
        </div>
        
        
      <table id="myTable" class="" >
        <thead style="display:none">
          <tr class="fw-bolder text-muted">
            <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#myTable .form-check-input" value="1" />
              </div></th>
            <th class="">Name</th>
          </tr>
        </thead>
      </table>
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->


<div class="modal fade show" id="add_staffing_partners" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content">
      <div class="modal-header" id="add_staffing_partners_header">
        <h2 class="fw-bolder">Invite Staffing partner or Recruitment consultant</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y mx-5 my-0">
        <form id="add_staffing_partners_form" name="add_staffing_partners_form" class="fv-row form fv-plugins-bootstrap5 fv-plugins-framework add_staffing_partners_form" action="#">
          <div class="d-flex flex-column scroll-y me-n7 pe-7" id="add_staffing_partners_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#add_staffing_partners_header" data-kt-scroll-wrappers="#add_staffing_partners_scroll" data-kt-scroll-offset="300px" style="max-height: 10px;">
            <div class="fv-row mb-7 fv-plugins-icon-container">
              <label class="fw-bold fs-6 mb-2" >Invite with email</label>
              <input type="email" name="staffing_partners_email" id="staffing_partners_email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Enter one or more email addresses" value="">
              <div class="fv-plugins-message-container invalid-feedback" for="staffing_partners_email"></div>
            </div>
          </div>
          <div class="text-center pt-5">
            <button type="button" class="btn btn-light me-3" data-kt-users-modal-action="close" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="submit_staffing_partners"> <span class="indicator-label">Invite</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Add staffing partners - popup-->
@endsection

@push('footer-script')

<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script>
    var input2 = document.querySelector("#kt_tagify_2");
    new Tagify(input2);
</script>
<script>

	$(document).ready(function() {
		$('#inputSearch').keyup(function() {
			table._fnDraw(); 
		});
	});
	
	 function filtersdataget(){
        	table._fnDraw(); 
        	}
        	
	var table = $('#myTable').dataTable({
       
											responsive  	:	true,
											processing  	:	true,
											serverSide  	:	true,
											destroy   		:	true,
											ordering		:	false,		//disable ordering			
											"bLengthChange" :	false,		//thought this line could hide the LengthMenu 
											sDom			:	'lrtip',
											oLanguage 		:	{sLengthMenu: "_MENU_",},
											ajax			:	{'url' : '{!! route('service_company.consultancy.uncleared_request_data') !!}', "data": function ( d ) {d.search = $('#inputSearch').val(),d.sortingfilter = $('#sortingfilter').val(),d.country = $('#country').val(),d.cus_search = $('#kt_tagify_2').val()  } },	
											"drawCallback"	:	function( settings ) {
																		$('[data-toggle="tooltip1"]').tooltip();
																		$('[data-toggle="tooltip2"]').tooltip();				
																},				  
											columns			:	[
																	{ data: 'chkbox', name: 'chkbox', searchable : false, orderable : false} 
															 	],
											//timeout		:	500000,
           									async			: false
   										});
    new $.fn.dataTable.FixedHeader( table );	
		
	$().ready(function() { 
		$("#add_staffing_partners_form").validate({
				rules: {
								email: { required: true, email: true }
						},
				messages: {
					
							email: "Please enter a valid email address"
						 }
		});
	 });

$('body').on('click', '.add_staffing_partners_frm', function(){ $('#staffing_partners_email').val('');	 });
$('body').on('click', '#submit_staffing_partners', function(){
	
			$("#add_staffing_partners_form").valid();
			
			var staffing_partners_email		=	$('#staffing_partners_email').val();
			var id							=	0; 
            var url 						=	"{{ route('service_company.consultancy.staffing_invitation', 'id') }}";
			url 							=	url.replace(':id', id);
            $.ajax({
									
							type		:	'GET',
							url			:	url,
							data		:	{'staffing_partners_email': staffing_partners_email },
							success		:	function (response) {
																	$('#staffing_partners_email').val('');
																	if (response.status == "warning") {
																		swal({
																					title				: "Warning!",
																					text				: response.message,
																					type				: "warning",
																					timer				: 1000,
																					showConfirmButton	: true
																			}, function(){
																							//location.reload();
																			});
									
																		}
																		
																		if (response.status == "success") {
																			
																				var spartnerAll  	=	response.spartnerDataCnt.spartnerAll;
																				var sentRequest  	=	response.spartnerDataCnt.sentRequest;
																				$('#span_spartnerAll').html(spartnerAll);
																				$('#span_sentRequest').html(sentRequest);
																				table._fnDraw(); 
																				
																				swal({
																						title				: "Success!",
																						text				: response.message,
																						type				: "success",
																						timer				: 500,
																						showConfirmButton	: true
																				}, function(){
																								//location.reload();
																				});
																			
																		}
											}
						
					});
			
	
	});
	
	
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" type="text/javascript"></script>
@endpush