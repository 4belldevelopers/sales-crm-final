@extends('layouts.demo_admin_app')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<!-- <link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}"> -->
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<link href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).on('click', function (e)
{
	var flag	=	0;
	if ($('.control-sidebar-slide-open').is(':visible'))
	{
		if ($(e.target).closest("#right-sidebar-content").length === 0)
		{
				flag	=	1;
		}
	}			
	if ($('#scheduleDetailModal').is(':visible'))
	{
				flag = 0;
	}
	if(flag == 1)
	{
		$("body").removeClass("control-sidebar-slide-open");	
	}
});
</script>
<style>
body
{
   overflow-x:hidden;
}
/*begin::Media query*/
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0rem;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important;
  }
}
/*end::Media query*/

/*begin::On click div*/
.div_content
{
	width: 360px;
	background-color: #fff;
	width: 360px;
	height: 55px;
	position: absolute;
	padding: 0.5em;
	display: none;
}
/*end::On click div*/
.table td:nth-child(6), th:nth-child(6)
{
	text-align:center;
}

</style>
@endpush

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Staffing Partners-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
			<a href="javascript:void(0);" class="text-gray-800 text-hover-primary">Offer Tracker</a>
		</h5>
        <span class="text-gray-600">920</span> </div>
      <!--end::Staffing Partners-->
      <!--begin::Separator-->
      <!-- <span class="h-20px border-gray-200 border-start mx-4"></span> -->
      <!--end::Separator-->
    </div>
    <!--end::Page title-->
    <!--begin::Actions-->
    <div class="d-flex align-items-center py-1">
      <!--begin::Search-->
      <div class="d-flex align-items-center position-relative my-1 me-3">
        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
        <span class="svg-icon svg-icon-1 position-absolute ms-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
          <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
        </svg>
        </span>
        <!--end::Svg Icon-->
        <input type="text" name="inputSearch" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>
      <!--end::Search-->
      <div class="card-toolbar">
        <!--begin::Toolbar-->
        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
          <!--begin::Filter-->
          <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
          <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
          Filter </button>
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
        </div>
        <!--end::Toolbar-->
        <!--begin::Group actions-->
        <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
          <div class="fw-bolder me-5"> <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected </div>
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
<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
         
@if(in_array("view_offer", $userPermissions))			 
		  <!--begin::Table-->
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
				<th class="w-10px pe-0"> 
					<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
						<input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-joi" value="1" />
					</div>
				</th>
                <th class="">Name</th>
                <th class="">Contact</th>
                <th class="">Requisitions</th>
                <th class="">Source</th>
                <th class="" style="width:300px!important">Offer Amount</th>
                <th class="">Joining Date</th>
                <th class="">Stage</th>
              </tr>
              <!--end::Table row-->
            </thead>
            
          </table>
          <!--end::Table-->
@endif		  
		  
        </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
  </div>
</div>


<!--begin::Modal - Stage and sub stage-->
<div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack"> 
        <h2>Stage and sub stage</h2>
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
<!--end::Modal - Stage and sub stage-->
@endsection

@push('footer-script')


<script src="{{ asset('demo/js/daterangepicker.js') }}"></script>

<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.datetimepicker.min.css') }}<?php echo TM_VERSION; ?>"/>
<script src="{{ asset('js/jquery.datetimepicker.js') }}<?php echo TM_VERSION; ?>"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$('#datetimepicker').datetimepicker({
			format:'d-M-Y H:m:s'
				 
		});
	});
</script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	$('#inputSearch').keyup(function() {
		tableLoad('filter');     
	}); 
});


$( document ).ready(function() {   
    $('body').on('click', '.ti-close', function () {
			$("body").removeClass("control-sidebar-slide-open");
		})		
});
		
		
		$('#column_visibility_control_btn').click(function(){
			var id        = 12;
			if(typeof scheduleDate === "undefined") {
				scheduleDate  = '';
			}
			var url      	 = "{{ route('admin.offer.load_visibility_control',':id') }}";
			url         	 = url.replace(':id', id);
			$('#modelHeading_cvc').html('Schedule');
			$.ajaxModal('#scheduleDetailModal_cvc', url);
		});
		
	  
	  
        var table;
        tableLoad('load');

        function tableLoad() 
		{
            table = $('#myTable').dataTable({
												responsive     	: true, 
												processing      : true,
												ordering		: false, //disable ordering      			
												destroy       	: true,   
												stateSave     	: true,      
												"bLengthChange"   : false,   
												sDom			: 'lrtip',   
												
												ajax			: {'url' : '{!! route('admin.offer.data') !!}',
												"data": function ( d )
													{
														d.search = $('#inputSearch').val()
													}
												},
		
				
				
												language: {
													"url": "<?php echo __("app.datatable") ?>"
												},
												"fnDrawCallback": function (oSettings) {
													$("body").tooltip({
														selector: '[data-toggle="tooltip"]'
													});
													$('.custom_calendar').datetimepicker({	format	:	'd-M-Y H:m:s' });
					
	
					
                },
                columns: [
							{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},
							{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},	
							{data: 'Contacts', name: 'Contacts',  orderable: false, searchable: false, width: '5%'},	
							{data: 'Requisitions', name: 'Requisitions',  orderable: false, searchable: false, width: '5%'},
							{data: 'Source', name: 'Source',  orderable: false, searchable: false, width: '10%'},
							{data: 'OfferAmount', name: 'OfferAmount',  orderable: false, searchable: false, width: '5%'},
							{data: 'JoiningDate', name: 'JoiningDate',  orderable: false, searchable: false, width: '10%'},
							{data: 'Stage', name: 'Stage',  orderable: false, searchable: false, width: '10%'}										
							
						]
            });
            new $.fn.dataTable.FixedHeader(table);
        }
		
		
		table.on('click', '.show-detail', function () 
		{			
			
				$(".right-sidebar").slideDown(50).addClass("shw-rside");
				var id 			= $(this).data('row-id');
								
				var url 		= "{{ route('admin.job-applications.show',':id') }}";
				url 			= url.replace(':id', id);
				$.easyAjax({
								type	: 'GET',
								url		: url,
								success	: function (response) {
																	if (response.status == "success") 
																	{
																			$('#right-sidebar-content').html(response.view);
																	}
															  }
				});
									
        }); 
		
		
		table.on('change', '.option_currency', function () 
		{						
				var option_case					=	'OFFER_CURRENCY';
				var option_currency_id 			= $(this).data('id');
				var option_currency_val 		= $(this).val();													
				var url 						= '{!! route('admin.applicationoffer.update_salary') !!}';				
				//url 							= 	url.replace(':id', id);
				var token 						= '{{ csrf_token() }}';
				$.ajax({
							type	: 'POST',
							url		: url,
							data	: {'_token': token, '_method': 'POST', 'option_currency_id' : option_currency_id, 'option_currency_val' : option_currency_val, 'option_case':option_case },
							success	: function (response) {
																if (response.status == "success") {
																		//$('#right-sidebar-content').html(response.view);
																}
														  }
				});									
        });
		table.on('change', '.option_salarymode', function () 
		{						
				var option_case					=	'OFFER_SALARY_MODE';
				var option_currency_id 			= $(this).data('id');
				var option_currency_val 		= $(this).val();													
				var url 						= '{!! route('admin.applicationoffer.update_salary') !!}';				
				//url 							= 	url.replace(':id', id);
				var token 						= '{{ csrf_token() }}';
				$.ajax({
							type	: 'POST',
							url		: url,
							data	: {'_token': token, '_method': 'POST', 'option_currency_id' : option_currency_id, 'option_currency_val' : option_currency_val, 'option_case':option_case },
							success	: function (response) {
																if (response.status == "success") {
																		//$('#right-sidebar-content').html(response.view);
																}
														  }
				});									
        }); 
		
		table.on('keyup', '.offer_amount', function () 
		{						
				var option_case					=	'OFFER_AMOUNT';
				var option_currency_id 			= $(this).data('id');
				var option_currency_val 		= $(this).val();													
				var url 						= '{!! route('admin.applicationoffer.update_salary') !!}';				
				//url 							= 	url.replace(':id', id);
				var token 						= '{{ csrf_token() }}';
				$.ajax({
							type	: 'POST',
							url		: url,
							data	: {'_token': token, '_method': 'POST', 'option_currency_id' : option_currency_id, 'option_currency_val' : option_currency_val, 'option_case':option_case },
							success	: function (response) {
																if (response.status == "success") {
																		//$('#right-sidebar-content').html(response.view);
																}
														  }
				});									
        }); 
		
		table.on('change', '.joining_date', function () 
		{						
				var option_case					=	'OFFER_JOINING_DATE';
				var option_currency_id 			=	$(this).data('id');
				var option_currency_val 		=	$(this).val();													
				var url 						=	'{!! route('admin.applicationoffer.update_salary') !!}';				
				//url 							= 	url.replace(':id', id);
				var token 						=	'{{ csrf_token() }}';
				$.ajax({
							type	: 'POST',
							url		: url,
							data	: {'_token': token, '_method': 'POST', 'option_currency_id' : option_currency_id, 'option_currency_val' : option_currency_val, 'option_case':option_case },
							success	: function (response) {
																if (response.status == "success") {
																		//$('#right-sidebar-content').html(response.view);
																}
														  }
				});									
        });
		
		
    </script>
<!-- <script src="{{ asset('js/lc_switch.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script> -->
<script type="text/javascript">
    const checkboxes 	=	'input[type=checkbox]',
          radios 		=	'input[type=radio]';    
    // initialization    
    lc_switch(checkboxes);
    lc_switch(radios, {
        					compact_mode : true,
        					on_color : 'teal',
    }); 
      	     
</script>
<script src="{{ asset('js/jquery.mloading.js') }}<?php echo TM_VERSION; ?>"></script>
<link rel="stylesheet" href="{{ asset('css/jquery.mloading.css') }}<?php echo TM_VERSION; ?>">
<script>    
    
        $('.custom_calendar').datepicker({
				format			: 'dd-mm-yyyy',
				showOn			: "button",
				buttonImage		: "{{ asset('img/calendar.png') }}", 
				buttonImageOnly	: true,
				buttonText		: "Select date",
				 onSelect: function(dateText, inst) {
														var date = $(this).val();
														var time = $('#time').val();
														alert('on select triggered');
				}
	 
        })
       
	   function updatecalendar()
	   {
	   		alert("call"); 
	   }
	   
	   
	   function update_offer(application_id, current_value, event_name)
	   {
				
	   			if( (event_name ==2) || (event_name ==3) || (event_name ==4) || (event_name ==5) || (event_name ==6) || (event_name ==7) || (event_name ==8)  )
				{
						$("body").mLoading();
						if(event_name == 2){
												if($("#any_offer"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 3){
												if($("#document"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 4){
												if($("#sub_for_approval"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 5){
												if($("#offer_approved"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 6){
												if($("#offer_release"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 7){
												if($("#offer_acceptance"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 8){
												if($("#joined"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						
				}	
	   			update_application(application_id, current_value, event_name);
	   }
	   
	  function update_application(application_id, current_value, event_name)
	   {
	   						
				var candidate_id 		= application_id;
				var process 			= event_name;
				var cvalue 				= current_value;
									
				var url 				= '{!! route('admin.applicationoffer.update_application') !!}';					
				//url 					= url.replace(':clientId', clientId);					
				var baseurl 			= '{{ asset('assets/images/AjaxLoader.gif') }}'; 									
				var token 				= '{{ csrf_token() }}';
				$.easyAjax({
   
                       				type	: 'POST',
                       				url		: url,
                       				data	: {'_token': token, '_method': 'POST', 'candidate_id' : candidate_id, 'process' : process, 'cvalue' : cvalue},
   									beforeSend: function() {
   													//$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
   									},
                       				success: function (response) {   
										//$('#jobs').html(response.data);
										//$('.clientStatusLoader').html('');
									},   
   									complete: function (data) {
																	if( (process ==2) || (process ==3) || (process ==4) || (process ==5) || (process ==6) || (process ==7) || (process ==8)  )
																	{
   																		setTimeout(function(){$("body").mLoading('hide');}, 500);
																	}
   						 			}
                		});
				  				   	
	   }
	   
	   
	   function exportExcel()
			{			

				var table 				= $('#myTable').dataTable();
				var length 				= table.fnGetData().length;
						
				var oSettings 			= table.fnSettings();
				var start     			= oSettings._iDisplayStart;
							
				var url 				= '{{ route('admin.applicationoffer.export', [':start' , ':length']) }}';
					
				url 					= url.replace(':start', start);
				url 					= url.replace(':length', length);
			
				window.location.href = url;
						
        }
		
		function updateApplicantStage(id)
		{
				//var id       	= $(this).data('row-id');			   
				var url    		= "{{ route('admin.job-applications.stage_body_content',':id') }}";
				url      		= url.replace(':id', id);
				$.ajax({
								type  			:	'GET',
								url   			:	url,
								success 		:	function (response) {
																	if (response.status == "success") 
																	{
																		$('#stage_body_content').html(response.view);
																	}
															  }
						}); 
		
		}	
	   
</script>
@endpush

<select name="">
	<option value=""></option>
</select>