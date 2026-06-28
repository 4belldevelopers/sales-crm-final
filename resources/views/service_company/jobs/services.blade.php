@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>

<style>
#send_email .modal-dialog{margin: .3rem auto!important;}
#fwd_resume .modal-dialog{margin: .3rem auto!important;}
@media  only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr{width: max-content!important;}
  .table > :not(caption) > * > *{padding: 0rem;}
  td:nth-child(n+2),
  th:nth-child(n+2){width: revert!important;}
}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesApplication{width: 1025px;}
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesApplication{width: 1200px;}
}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_client_activitiesApplication{width: 1025px;}
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_client_activitiesApplication{width: 1200px;}
}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesApplication{width: 1025px;}
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesApplication{width: 1200px;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesCopy{width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesCopy{width: 1200px;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesClient{width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesClient{width: 1200px;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesJob {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesJob {width: 1200px;}
}
</style> 
<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-primary-800">Team Condition</a> </h5>
        {{-- <span class="text-gray-600" id="total_record">{{@$applicationCounter}}</span> --}}
      </div>
    </div>
    <div class="d-flex align-items-center py-1">
    <!--  <a href="javascript:void(0);" class="me-3 daterange"><span class="svg-icon svg-icon-primary svg-icon-1x">-->
      <input type="hidden" class="form-control form-control-solid" placeholder="Pick date rage" id="kt_daterangepicker"/>
    <!--</span> </a>-->

  

      <div class="before-event downloadexcell d-flex align-items-center position-relative my-1 me-3 d-none">
        <span class="svg-icon svg-icon-1 position-absolute ms-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
          <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
        </svg>
        </span>
        
      </div>
      
      <div class="before-event d-flex align-items-center position-relative my-1 me-3 custom_hide">
        
        <input type="hidden" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>

   


      <div class="card-toolbar">
        <div class="before-event d-flex justify-content-end" data-kt-user-table-toolbar="base">
          
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
      </div>
    </div>
  </div>
</div>

<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        
        <div class="d-flex align-items-center flex-wrap custm-bg mb-0">
                  <div class="col-lg-4 d-flex align-items-center px-2" style="width:50%">
                     <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                        <option value="Show All" selected="">Sort</option>
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                        <option value="Show All" selected="">Location</option>
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
        <div class="card-body pt-3 px-5"> @if(in_array("view_job_applications", $userPermissions))
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input datatable_chk chk_list" type="checkbox" data-kt-check="true" data-kt-check-target="#myTable .form-check-input" value="1" />
                  </div></th>
                <th class="">Services</th>
                <th class="">Status</th>
                <th class="">Commericals</th>
                {{-- <th class="">Country</th> --}}
                <!--<th class="" data-toggle="tooltip" title="Notice Period">NP</th>-->
              </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
               <tr role="row" class="odd">
                  <td>
                     <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" data-jobid="2" value="1">
                     </div>
                  </td>
                  <td class=" align-items-center">
                     <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                        <a href="javascript:void(0);" data-row-id="1" class="">
                           <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">F</div>
                        </a>
                     </div>
                     <div class="d-flex flex-column ser_show-detail" data-type="2">
                        <a href="javascript:void(0);" data-row-id="1" class="text-dark fw-bolder text-hover-primary fs-6 " data-toggle="tooltip" title="Fulltime Hiring">Fulltime Hiring<sup style="color: #ef7c62 !important;
                           font-weight: bold;"></sup></a>
                        <a href="javascript:void(0);" data-row-id="1" class="text-muted fw-bold d-block fs-7 text-hover-primary  " data-toggle="tooltip" title="" data-bs-original-title=".Net Developer"></a>
                     </div>
                  </td>
                    <td>
                      <div class="d-flex flex-column">
                           <a href="javascript.void(0);" data-row-id="2" class="d-flex flex-column active-bg status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">Active</a>
                          <span class="fw-bold text-muted d-block fs-7"></span>
                        </div>
                    </td>
                  <td>
                     <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a>
                     </div>
                  </td>
                  {{-- <td>
                    <div class="d-flex flex-column">
                      <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">India</a>
                      <span class="text-muted fw-bold d-block fs-7"></span>
                   </div>
                  </td> --}}
               </tr>
               <tr role="row" class="odd">
                  <td>
                     <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" data-jobid="2" value="1">
                     </div>
                  </td>
                  <td class=" align-items-center">
                     <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                        <a href="javascript:void(0);" data-row-id="1" class="">
                           <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">C</div>
                        </a>
                     </div>
                     <div class="d-flex flex-column ser_show-detail" data-type="1">
                        <a href="javascript:void(0);" data-row-id="1" class="text-dark fw-bolder text-hover-primary fs-6  " data-toggle="tooltip" title="Contact Hiring">Contract Hiring<sup style="color: #ef7c62 !important;
                           font-weight: bold;"></sup></a>
                        <a href="javascript:void(0);" data-row-id="1" class="text-muted fw-bold d-block fs-7 text-hover-primary  " data-toggle="tooltip" title="" data-bs-original-title=".Net Developer"></a>
                     </div>
                  </td>
                    <td>
                      <div class="d-flex flex-column">
                           <a href="javascript.void(0);" data-row-id="2" class="d-flex flex-column active-bg status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">Active</a>
                          <span class="fw-bold text-muted d-block fs-7"></span>
                        </div>
                    </td>
                  <td>
                     <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a>
                     </div>
                  </td>
                  {{-- <td>
                    <div class="d-flex flex-column">
                      <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">USA</a>
                      <span class="text-muted fw-bold d-block fs-7"></span>
                   </div>
                  </td> --}}
               </tr>
            </tbody>
          </table>
          @endif </div>
      </div>
    </div>
  </div>
</div>


<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark"></h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>



@endsection
@push('footer-script')

<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>

  $(document).ready(function(){
      $(document).on('click','.rateTypeClass', function(){
      var rateType = $(this).val();
      if(rateType==2){
            $('.rate-section').addClass('d-none');
            $('.rate-perecentage').removeClass('d-none');
      }else{
            $('.rate-section').removeClass('d-none');
            $('.rate-perecentage').addClass('d-none');
      }
      });

       $(document).on('change','select[name="sal_mode"]', function(){
            var selectedValue = $(this).val();
            $('select[name="sal_mode"]').val(selectedValue);
        });
        
        $(document).on('change','select[name="salary_currency"]', function(){
            var selectedValue = $(this).val();
            $('select[name="salary_currency"]').val(selectedValue);
        });



        $(document).on('click','#form-submit', function(){					 
						 
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
                               confirmButtonText	: 'Yes, update it!',
                               cancelButtonText	: 'No, cancel!',
                               reverseButtons	: true 
                           }).then((result) => {
               if (result.isConfirmed) {
                     
                           var id        	=	1;
                           var url       	=	"{{ route('service_company.jobs.addUpdateJobServices',':id') }}";
                           url         	=	url.replace(':id', id);
                           var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
                           var token       =	"{{ csrf_token() }}";
                           var arrCountry 		=	[];
                           $('.countries:checked').each(function () {
                            arrCountry.push($(this).val());
                           });
                           var rate_val = $(".rate_val").map(function() {
                              return this.value;
                            }).get();
                            var rateTypeClass = $('.rateTypeClass:checked').val();
                            var main_status = $('.main_status:checked').val();
                            var sal_mode = $('select[name="sal_mode"]').val();
                            var salary_currency = $('select[name="salary_currency"]').val();
                            var updateId = $('.updateId').val();
                            var commType = $('.commType').val();
                            if(commType==2){
                              sal_mode =null;
                              salary_currency =null;

                              rate_val = $(".perecentage_val").map(function() {
                              return this.value;
                            }).get();


                            }
                         				
                           $.ajax({
                                   type		: 'POST',
                                   url			: url,
                                   data		: {'_token': token, '_method': 'POST', 'arrCountry' : arrCountry, 'rate_val': rate_val,'rateTypeClass':rateTypeClass,'sal_mode':sal_mode,'salary_currency':salary_currency,'updateId':updateId,'commType':commType,'main_status':main_status},
                                   success 	: function (response) {
                                           if (response.status == "success") {
                                               swalWithBootstrapButtons.fire(
                                                                 'Success!',
                                                                 'Updated Successfully.',
                                                                 'success'
                                                               )
                                           }
                                     }
                           });
                        
               } 
               else if (result.dismiss === Swal.DismissReason.cancel) {
                     swalWithBootstrapButtons.fire(
                                      'Cancelled',
                                     'Selected request is safe :)',
                                     'error'
                                     )
               }
             })	});





  });
  
// $('input[name="rateType"]').each(function() {
//         alert("d");
//         if ($(this).val() == sectionCheck) {
//             $(this).prop('checked', true);
//         } else {
//             $(this).prop('checked', false);
//         }
//     });

$('.ser_show-detail').click(function()
   	{
   		$('#kt_activitiesJob_toggle').trigger('click');		
   		var id    		=	$(this).attr('data-type');
   		var url   		=	"{{ route('service_company.jobs.serviceviewmore',':id') }}?viewmore=viewmore";
   		url     		=	url.replace(':id', id);
   		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
									$('#kt_activitiesJob_body').html(response.view);	
   						}
   	  			});
   	});
   	
</script>

@endpush