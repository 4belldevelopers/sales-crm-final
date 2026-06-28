@extends('layouts.service_company_layout')

@push('head-script')
<style>
body {overflow-x:hidden;}
@media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr {width: max-content!important; }
  .table > :not(caption) > * > * {padding: 0rem; }
  td:nth-child(n+2),
  th:nth-child(n+2) {width: revert!important; }
}
.div_content {width: 360px; background-color: #fff; width: 360px; height: 55px; position: absolute; padding: 0.5em; display: none; }
.table td:nth-child(6), th:nth-child(6){text-align:center;}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesApplication {width: 1025px;}
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesApplication{ width: 1200px; }
}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_create_offer {width: 1025px;}
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_create_offer{ width: 1200px; }
}

@media  only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_client_activitiesApplication{ width: 1025px;}
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_client_activitiesApplication{ width: 1200px; }
}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_client_activitiesApplication_toggle{ width: 1025px;}
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_client_activitiesApplication_toggle{ width: 1200px; }
}
.joining_date::-webkit-inner-spin-button,
.joining_date::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
#myTable tbody tr {
    height: 100px; /* Adjust the height as per your requirement */
    position: relative;
}

</style>
@endpush

@section('content')
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-gray-800 text-hover-primary">Offer Tracker</a> </h5>
        <!-- <span class="text-gray-600">{{ @$appCounter }}</span> -->
        <span class="text-gray-600" id="total_record"></span> 
      </div>
    </div>
    <div class="d-flex align-items-center py-1">
      <div class="d-flex align-items-center position-relative my-1 me-3"> 
        <input type="hidden" name="inputSearch" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>
      <div class="card-toolbar">
        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
          
          <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
            <div class="px-7 py-5">
              <div class="fs-5 text-dark fw-bolder">Filter Options</div>
            </div>
            <div class="separator border-gray-200"></div>
            <div class="px-7 py-5" data-kt-user-table-filter="form">
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
              <div class="mb-10">
                <label class="form-label fs-6 fw-bold">Two Step Verification:</label>
                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="two-step" data-hide-search="true">
                  <option></option>
                  <option value="Enabled">Enabled</option>
                </select>
              </div>
              <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                <button type="submit" class="btn btn-primary fw-bold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
          <div class="fw-bolder me-5"> <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected </div>
          <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        
        <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                  <div class="col-lg-4 d-flex align-items-center px-2" style="width:60%;position: relative;">
                     <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="" placeholder="Search..." id="kt_tagify_2" />
                     <div style="position: absolute; top: 0px; right: 36px;">
                      <div id="loader_search" class="loader_search hidden_search" style="position: absolute; top: 8px;"></div>
                   </div>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 25%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter"  data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                        <option value="Show All" selected="">Sort</option>
                        <option value="10">Newest to Oldest (Offer Date)</option>
                        <option value="11">Oldest to Newest (Offer Date)</option>
                         <option value="12">Newest to Oldest (Join Date)</option>
                        <option value="13">Oldest to Newest (Join Date)</option>
                        <option value="14">Low to High (Offer Amount)</option>
                        <option value="15">High to Low (Offer Amount)</option>
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country"  data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                        <option value="" selected="selected">Location</option>
                        @if(@$countries)
                            @foreach($countries as $val_co)
                            <option value="{{ $val_co->id }}">{{ $val_co->country_name }}</option>
                            @endforeach
                        @endif
                        
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 15%;">
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
        <div class="card-body pt-3 px-5"> @if(in_array("view_offer", $userPermissions))
          <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-joi" value="1" />
                  </div></th>
                <th class="">Name</th>
                <th class="">Jobs</th>
                <th class="">Status</th>
                <th class="">Source</th>
                <th class="">Offer Amount</th>
                <th class="">Joining</th>
                <th class="">Offer Status</th>
                <th class="">Stage</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold" id="offer-tracker">
            </tbody>
          </table>
          <div id="pagination"></div>
          @else
          <table id="no-access" class="table align-middle table-row-dashed fs-6 gy-5" >
            <thead>
              <tr class="fw-bolder text-muted">
                <th class=""><div align="center">ACCESS DENIED CONTACT ADMINISTRATOR</div></th>
              </tr>
            </thead>
          </table>
          @endif </div>
      </div>
    </div>
  </div>
</div>
<!--begin::Modal - Stage and sub stage-->

    <!--begin::Fwd Resume - Modal-->
    <div class="modal fade" id="fwd_offer" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
          <div class="modal-header flex-stack">
            <h2 id="titleSet"></h2>
            <div class="btn btn-sm btn-icon btn-active-light-danger" data-dismiss="modal">
              <span class="svg-icon svg-icon-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" />
                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" />
              </svg>
              </span>
            </div>
          </div>
          <div class="card-body position-relative px-0 py-0" id="application_fwd_offer_body_content"></div>
        </div>
      </div>
    </div>
    <!--end::Fwd Resume - Modal-->

        <!--begin::Stage sub stage popup-->
    <div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Stage and sub stage</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                        id="stage_sub_stage_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="stage_body_content" id="stage_body_content"></div>
            </div>
        </div>
    </div>
    <!--end::Stage sub stage popup-->

@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script>
    // var input2 = document.querySelector("#kt_tagify_2");
    // new Tagify(input2);
</script>
<script type="text/javascript">

$(document).ready(function() {
	$('#kt_tagify_2').keyup(function() {
		// tableLoad('filter');    
    fetchCandidates(); 
	}); 
});

	
	  
        var table;
        // tableLoad('load');
 function filtersdataget(){
        		// tableLoad('load');	
            fetchCandidates(); 
        	}
        function tableLoad() 
			{
          var   inputSearch 		=	$('#inputSearch').val();
          var sortingfilter          			= $('#sortingfilter').val();
          var country          			= $('#country').val();
          var cus_search          			= $('#kt_tagify_2').val();
            	table = $('#myTable').dataTable({
                          serverSide: true, // Enable server-side processing
													responsive     	:	true, 
													processing      :	true,
													ordering		:	false, //disable ordering      			
													destroy       	:	true,   
													stateSave     	:	true,      
													"bLengthChange" :	false,   
													sDom			:	'lrtip',   
													// ajax			:	{'url' : '{!! route('service_company.offer.data') !!}', "data" : function ( d ) { d.search = $('#inputSearch').val() } },
                          ajax			:	{'url' : '{!! route('service_company.cvtracker.alldata') !!}', "data" : function ( d ) { d.search = $('#inputSearch').val(),d.status_cus = 5,d.sortingfilter = sortingfilter,d.country = country,d.cus_search = cus_search } },
													"fnDrawCallback": function (oSettings) {
												
																							var total_record	=	this.fnSettings().fnRecordsTotal();
																							$('#total_record').html(total_record);
																							$("body").tooltip({
																								selector: '[data-toggle="tooltip"]'
																							});
                													},
													columns			: [
																		{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},
																		{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},		
																		{data: 'Requisitions', name: 'Requisitions',  orderable: false, searchable: false, width: '5%'},
																		{data: 'Status', name: 'Status',width: '10%', searchable : false, orderable: false},
																		
																		
																		{data: 'Source', name: 'Source',  orderable: false, searchable: false, width: '10%'},
																		{data: 'OfferAmount', name: 'OfferAmount',  orderable: false, searchable: false, width: '5%'},
																		{data: 'JoiningDate', name: 'JoiningDate',  orderable: false, searchable: false, width: '10%'},
																		{data: 'OfferStatus', name: 'OfferStatus',  orderable: false, searchable: false, width: '5%'},
																		{data: 'Stage', name: 'Stage',  orderable: false, searchable: false, width: '10%'}										
																		
																]
            });
            // new $.fn.dataTable.FixedHeader(table);
        }


        $(document).on('click', '.master_status_div', function(e) {

            e.preventDefault();

            var id = $(this).data('master-status-id');
            $("#master_status_" + id).attr('checked', true);
            $('#main_substage').show();
            $('#comment_div').show();

            $("#inreview").attr('checked', false);
            $("#sourcing").attr('checked', false);
            $("#screening").attr('checked', false);
            $("#evaluation").attr('checked', false);
            $("#negotation").attr('checked', false);
            $("#offer").attr('checked', false);
            $("#joined").attr('checked', false);
            $("#rejected").attr('checked', false);

            if (id == 1) {
                $("#sourcing").attr('checked', true);
                $('lbl_master_status_' + id).addClass('sourcing-bg');
            } else if (id == 2) {
                $("#screening").attr('checked', true);
                $('lbl_master_status_' + id).addClass('screening-bg');
            } else if (id == 3) {
                $("#evaluation").attr('checked', true);
                $('lbl_master_status_' + id).addClass('interview');
            } else if (id == 4) {
                $("#negotation").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-negotation');
            } else if (id == 5) {
                $("#offer").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-offer');
            } else if (id == 6) {
                $("#joined").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-joined');
            } else if (id == 7) {
                $("#rejected").attr('checked', true);
                $('lbl_master_status_' + id).addClass('btn-rejected');
                // $('#main_substage').hide();	
                $('#comment_div').show();
            } else if (id == 8) {
                $("#inreview").attr('checked', true);
                $('lbl_master_status_' + id).addClass('bg-light-warning text-warning');
                $('#main_substage').hide();
            }

            if (id > 0 && (id != 8)) {

                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                var url = "{{ route('service_company.job-applications.child_stage', ':id') }}";
                url = url.replace(':id', id);
                $.ajax({
                    type: 'GET',
                    url: url,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('#substage').html('<img src="' + baseurl + '" height="35px" width="100%">');
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            if (response.html == '') {
                                $('#main_substage').hide();
                            }
                            $('#substage').html('');
                            $('#substage').html(response.html);
                        }
                    }
                });

            }

            e.run();

        });

		
    $(document).on('change', '.option_currency', function () 
			{						
				var option_case					=	'OFFER_CURRENCY';
				var option_currency_id 			= $(this).data('id');
        var offerId 			= $(this).data('offerid');
				var option_currency_val 		= $(this).val();													
				var url 						= '{!! route('service_company.applicationoffer.update_salary') !!}';				
				//url 							= 	url.replace(':id', id);
        var currency			= $("#option_currency_"+option_currency_id).val();
				var offer_amount		= $("#offer_amount_"+option_currency_id).val();
				var joining_date		= $("#joining_date_"+option_currency_id).val();
				var salarymode 			= $("#option_salarymode"+option_currency_id).val();
				var token 						= '{{ csrf_token() }}';
				var baseurl     				= "{{ asset('assets/images/Loading2.gif') }}"; 
				 
				$.ajax({
							type	: 'POST',
							 beforeSend: function() {
                                  						//$('#option_currency_'+option_currency_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                    		},
							url		: url,
							data	: {'_token': token, '_method': 'POST', 'option_currency_id' : option_currency_id, 'option_currency_val' : option_currency_val, 'option_case':option_case, 'offerId':offerId,'currency':currency,'offer_amount':offer_amount,'joining_date':joining_date,'salarymode':salarymode },
							success	: function (response) {
																if (response.status == "success") {
																		//$('#right-sidebar-content').html(response.view);
																}
														  }
				});									
        });
		
    $(document).on('change', '.option_salarymode', function () 
			{						
				var option_case					=	'OFFER_SALARY_MODE';
				var option_currency_id 			= $(this).data('id');
				var option_currency_val 		= $(this).val();		
        var offerId 			= $(this).data('offerid');											
				var url 						= '{!! route('service_company.applicationoffer.update_salary') !!}';				
				//url 							= 	url.replace(':id', id);
        var currency			= $("#option_currency_"+option_currency_id).val();
				var offer_amount		= $("#offer_amount_"+option_currency_id).val();
				var joining_date		= $("#joining_date_"+option_currency_id).val();
				var salarymode 			= $("#option_salarymode"+option_currency_id).val();
				var token 						= '{{ csrf_token() }}';
				$.ajax({
							type	: 'POST',
							url		: url,
							data	: {'_token': token, '_method': 'POST', 'option_currency_id' : option_currency_id, 'option_currency_val' : option_currency_val, 'option_case':option_case, 'offerId':offerId,'currency':currency,'offer_amount':offer_amount,'joining_date':joining_date,'salarymode':salarymode },
							success	: function (response) {
																if (response.status == "success") {
																		//$('#right-sidebar-content').html(response.view);
																}
														  }
				});									
        }); 
		
    $(document).on('change', '.offer_amount', function () 
			{						
				var option_case					=	'OFFER_AMOUNT';
				var option_currency_id 			= $(this).data('id');
        var offerId 			= $(this).data('offerid');
				var option_currency_val 		= $(this).val();													
				var url 						= '{!! route('service_company.applicationoffer.update_salary') !!}';				
				//url 							= 	url.replace(':id', id);
        var currency			= $("#option_currency_"+option_currency_id).val();
				var offer_amount		= $("#offer_amount_"+option_currency_id).val();
				var joining_date		= $("#joining_date_"+option_currency_id).val();
				var salarymode 			= $("#option_salarymode"+option_currency_id).val();
				var token 						= '{{ csrf_token() }}';
				$.ajax({
							type	: 'POST',
							url		: url,
							data	: {'_token': token, '_method': 'POST', 'option_currency_id' : option_currency_id, 'option_currency_val' : option_currency_val, 'option_case':option_case, 'offerId':offerId,'currency':currency,'offer_amount':offer_amount,'joining_date':joining_date,'salarymode':salarymode },
							success	: function (response) {
																if (response.status == "success") {
																		//$('#right-sidebar-content').html(response.view);
																}
														  }
				});									
        }); 


        $(document).on('click', '.stage_sub_stage', function() {

            $('#stage_body_content').html('');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.job-applications.stage_body_content', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html('');
                        $('#stage_body_content').html(response.view);
                    }
                }
            });

        });

        $(document).on('click', '#custom_stage_submit', function(e) {

                e.preventDefault();

                var master_status = $('input[name="master_status"]:checked').val();
                var comment = $('#comment').val();
                if (master_status == 8 && comment=='') {
                    Swal.fire("Warning", 'InReview Comment Required !', "info");
                } else {
                    $("#custom_stage_submit").prop("disabled", true);
                    $("#custom_stage_submit").html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                        );

                    $.ajax({
                        url: '{{ route('service_company.job-applications.stage_submit') }}',
                        container: '#status_frm',
                        type: "POST",
                        data: $('#status_frm').serialize(),
                        success: function(response) {
                            $("#custom_stage_submit").prop("disabled", false);
                            $("#custom_stage_submit").html(
                                `<span class="indicator-label">Submit</span>`);
                            if (response.status == 'success') {
                                Swal.fire("Success", 'Application stage update successfully !',
                                    "success").then(function() {
                                    // $('#stage_sub_stage').modal('hide');
                                    $("#stage_sub_stage_close").click();
                                });
                                var page = $('#paginationcandidate .active_disabled').data('page');
                                fetchCandidates(page);
                                //  	var table;
                                // 				tableLoad('load');
                                // table._fnDraw();
                                load_interviewSchedule();
                            } else if (response.status == 'cvpending') {
                                Swal.fire("Warning", 'Resume pending this job application.',
                                    "warning").then(function() {
                                    $("#stage_sub_stage_close").click();
                                });
                            } else if (response.status == 'candidatevalidation') {
                                Swal.fire("Warning", response.message, "warning").then(
                                function() {
                                    $("#stage_sub_stage_close").click();
                                });
                            }
                        }
                    })
                }


            });
		
    $(document).on('change', '.joining_date', function () 
			{						
				var option_case					=	'OFFER_JOINING_DATE';
				var option_currency_id 			=	$(this).data('id');
        var offerId 			= $(this).data('offerid');
				var option_currency_val 		=	$(this).val();													
				var url 						=	'{!! route('service_company.applicationoffer.update_salary') !!}';				
				//url 							= 	url.replace(':id', id);
        var currency			= $("#option_currency_"+option_currency_id).val();
				var offer_amount		= $("#offer_amount_"+option_currency_id).val();
				var joining_date		= $("#joining_date_"+option_currency_id).val();
				var salarymode 			= $("#option_salarymode"+option_currency_id).val();
				var token 						=	'{{ csrf_token() }}';
				$.ajax({
							type	: 'POST',
							url		: url,
							data	: {'_token': token, '_method': 'POST', 'option_currency_id' : option_currency_id, 'option_currency_val' : option_currency_val, 'option_case':option_case, 'offerId':offerId,'currency':currency,'offer_amount':offer_amount,'joining_date':joining_date,'salarymode':salarymode },
							success	: function (response) {
																if (response.status == "success") {
																		//$('#right-sidebar-content').html(response.view);
																}
														  }
				});									
      });



      $(document).on('click','.fwd_offer', function(e){
       // $('.fwd_resume').click(function(e){
						
            e.preventDefault();	

            localStorage.setItem('toggleOpen', 'true');
            $('#application_mass_mail_body_content').html('');
            $('#application_fwd_offer_body_content').html('');
            var jobId = $(this).attr('data-jobId');
            var candidate = $(this).attr('data-candidate');
            var mtype = $(this).attr('data-mtype');
            var offerId = $(this).attr('data-offerId');
            var arr 		=	[candidate];
            var jobIdArr = [jobId];
            // $('.chk_list_item:checked').each(function () {
                
            //     var jobId = $(this).attr('data-jobId');
            //     arr.push($(this).val());
            //     jobIdArr.push(jobId);
            // });

            // console.log(jobIdArr);

            var id        	=	jobIdArr[0];
            var url       	=	"{{ route('service_company.cvtracker.offer_send_mail',':id') }}";
            url         	=	url.replace(':id', id);
            var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
            var token       =	"{{ csrf_token() }}";
          
           
            $.ajax({
                    type		: 'POST',
                    url			: url,
                    data		: {'_token': token, '_method': 'POST', 'arr' : arr,'jobIdArr':jobIdArr,'mtype':mtype,'offerId':offerId},
                    cache		: false,
                    success 	: function (response) {
                                        if (response.status == "success") {
                                            $('#titleSet').text(response.title)
                                            $('#application_fwd_offer_body_content').html(response.view);
                                        }
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
          var   inputSearch 		=	$('#inputSearch').val();
          var sortingfilter          			= $('#sortingfilter').val();
          var country          			= $('#country').val();
          var cus_search          			= $('#kt_tagify_2').val();
          if($('#kt_tagify_2').val()!=''){
                // $('.loadingMessage').css({ display: 'block' });
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            // $('.page-loader').css('display', 'flex');
            $.ajax({
                url: `{!! route('service_company.cvtracker.allcandidates') !!}?page=${page}`,
                type: 'GET',
                data: {
                  status_cus : 5,
                  search : $('#inputSearch').val(),
                  sortingfilter : sortingfilter,
                  country : country,
                  cus_search:cus_search
                },
                success: function(response) {
                  $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#total_record').html(response.total);
                    $('#offer-tracker').html(response.html);
                    $('#pagination').html(response.pagination);
                }
            });
        }
        




       
		
</script>

@endpush 