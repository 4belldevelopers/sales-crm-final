@extends('layouts.product_company_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<link href="{{ asset('assets/dist/css/adminlte.min.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<!-- <link href="{{ asset('css/custom.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" /> -->
<!-- <link href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" /> -->


<link rel="stylesheet" href="{{ asset('demo/dist/assets/plugins/custom/prismjs/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>">


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
   
   		if(flag == 1){
   			$("body").removeClass("control-sidebar-slide-open");	
   		}
   
   });
</script>
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<style>
.mb-20
{
  margin-bottom: 20px
}
body
{
  padding: 0px!important;
  margin: 0px!important;
  background: #f5f8fa;
}
/*Popup Margin and Padding End*/

/*begin::Add new css*/
.header-fixed.toolbar-fixed .wrapper
{
  margin: 1.3em 0em 0em 0em;
  background: #f1f2f5;
  /* height: 100vh; */
  padding-right: 0.7em!important;
}
.card .card-body
{
  padding: 1rem 1em;
}
.card-body > .table > thead > tr > td,
.card-body > .table > thead > tr > th,
.card-body > .table > thead > tr > th > a
{
  border-top-width: 0;
  color: #a1a5b7;
}
#myTable_length
{
  display:none;
}
/*end::Add new css*/

</style>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!--script>
   $( function()
   {   
		$( "#start_date" ).datepicker().on('change', function() {
		$(this).valid();  // triggers the validation test
		// '$(this)' refers to '$("#datepicker")'
		});

	   $( "#end_date" ).datepicker().on('change', function() {
			$(this).valid();  // triggers the validation test
			// '$(this)' refers to '$("#datepicker")'
		});   
   } );
   
</script-->
@endpush 
@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
   <!--begin::Container-->
   <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <!--begin::Page title-->
      <div class="page-title d-flex align-items-center flex-wrap ml-3 mb-5 mb-lg-0 position-absolute start-5">
         <!--begin::Date format-->
         <div class="d-flex flex-column" data-toggle="modal" data-target="#scheduleDetailModalCustomReportFilter">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-0">
               <a href="javascript:void(0);" class="text-gray-800 text-hover-primary mb-0">Report</a>
            </h1>
            <span class="text-gray-600 d-none">920</span>
            <div class="d-inline-flex custom_report_filter1 text-hover-primary">
              <span class="cursor-pointer" style="font-size:20px;" id="lbl_start_date"><?php echo date('D, d-M, Y', strtotime('-7 days'));  ?></span>
              <span class="cursor-pointer" style="font-size:20px;">&nbsp;-&nbsp;</span>
              <span class="cursor-pointer" style="font-size:20px;" id="lbl_end_date">
                <?php echo date("D, d-M, Y"); ?>&nbsp;<i class="fa fa-calendar-alt cursor-pointer fa-1x"></i>
              </span>
            </div>
         </div>
         <!--end::Date format-->
         <!--begin::Separator-->
         <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
         <!--end::Separator-->
      </div>
      <!--end::Page title-->
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1 position-absolute end-0 me-5">
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
            <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
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
                  <!--end::Svg Icon-->Filter
               </button>
               <!--end::Filter-->
            </div>
            <!--end::Toolbar-->
         </div>
      </div>
      <!--end::Actions-->
   </div>
   <!--end::Container-->
</div>
<!--end::Toolbar-->

<!--begin::Report Popup-->
<div class="modal fade bs-modal-exl in" id="scheduleDetailModalCustomReportFilter" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mw-900px" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingCustomReportFilter"></span> </div>
      <div class="modal-body">Please wait...</div>
      <div class="modal-footer"> </div>
    </div>
  </div>
</div>
<!--end::Report Popup-->

<div id="chart_teamwork"></div>

<!--begin::Table-01-->
<div class="row px-4">
  <!-- margin-top: -2em; -->
  <div class="card" style="padding-bottom: 1em;margin-bottom: 1em!important;">
    <div class="card-body bg-white pt-3">        
      <!--begin::Toolbar-->        
      <div class="row pt-2">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack align-items-center" style="display: inline-flex!important;">
          <!--begin::Page title-->
          <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <!--begin::Title-->
            <div class="d-flex flex-column">
              <h2 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">All Requisitions</h2>
            </div>
              <span class="text-gray-600">
                <span class="text-capitalize"></span>
              </span>
            <!--end::Title-->
          </div>
          <!--end::Page title-->
          <!--begin::Actions-->
          <div class="d-flex align-items-center py-1">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1 me-3">
              <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
              <span class="svg-icon svg-icon-1 position-absolute ms-6">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <input type="text" data-kt-user-table-filter="search" name="inputSearch" id="inputSearch" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search...">
            </div>
            <!--end::Search-->
            <div class="card-toolbar">
              <!--begin::Toolbar-->
              <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <!--begin::Filter-->
                <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3">
                  <span class="svg-icon svg-icon-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  Filter
                </button>
                <!--end::Filter-->
                <!--begin::Export to Excel-->
                <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder">
                  <span class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                      <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                      <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                    </svg>
                  </span>
                  <!--end::Svg Icon-->
                  Export
                </button>
                <!--end::Export to Excel-->
              </div>
              <!--end::Toolbar-->
            </div>
          </div>
          <!--end::Actions-->
        </div>
      </div>
      <!--end::Toolbar-->
      <!--begin::Table All Requisitions-->
      <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
          <tr class="">
            <th>S.No</th>
            <th>Client</th>
            <th>Requisitions</th>
            <th>Source</th>
            <th>
              <a href="javascript:void(0);" class="font-weight-600" data-bs-toggle="tooltip" title="Total Application">TOT</a>
            </th>
            <th>
              <a href="javascript:void(0);" class="font-weight-600" data-bs-toggle="tooltip" title="Sourcing">SOU
              </a>
            </th>
            <th>
              <a href="javascript:void(0);" class="font-weight-600" data-bs-toggle="tooltip" title="Screening">SCR
              </a>
            </th>
            <th>
              <a href="javascript:void(0);" class="font-weight-600" data-bs-toggle="tooltip" title="Evaluation">EVA
              </a>
            </th>
            <th>
              <a href="javascript:void(0);" class="font-weight-600" data-bs-toggle="tooltip" title="Negotiation">NEO
              </a>
            </th>
            <th>
              <a href="javascript:void(0);" class="font-weight-600" data-bs-toggle="tooltip" title="Offer">OFF
              </a>
            </th>
            <th>
              <a href="javascript:void(0);" class="font-weight-600" data-bs-toggle="tooltip" title="Joined">JOI
              </a>
            </th>
            <th>
              <a href="javascript:void(0);" class="font-weight-600" data-bs-toggle="tooltip" title="Rejected">REJ
              </a>
            </th>
          </tr>
        </thead>
      </table>
      <!--end::Table All Requisitions-->
    </div>
  </div>
</div>
<!--end::Table-01-->

<!--begin::Table-02-->
<div class="row px-4">
  <div class="card pb-5 mb-0">
    <div class="card-body bg-white pt-3">         
      <!--begin::Toolbar-->        
      <div class="row pt-2">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack" style="display: inline-flex!important;align-items: center;">
          <!--begin::Page title-->
          <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <!--begin::Title-->
            <div class="d-flex flex-column">
              <h2 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Interview Tracker</h2>
            </div>
              <span class="text-gray-600">
                <span class="text-capitalize"></span>
              </span>
            <!--end::Title-->
          </div>
          <!--end::Page title-->
          <!--begin::Actions-->
          <div class="d-flex align-items-center py-1">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1 me-3">
              <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
              <span class="svg-icon svg-icon-1 position-absolute ms-6">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <input type="text" data-kt-user-table-filter="search" name="inputSearch" id="inputSearch" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search...">
            </div>
            <!--end::Search-->
            <div class="card-toolbar">
              <!--begin::Toolbar-->
              <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <!--begin::Filter-->
                <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3">
                  <span class="svg-icon svg-icon-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  Filter
                </button>
                <!--end::Filter-->
                <!--begin::Export to Excel-->
                <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder">
                  <span class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                      <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                      <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                    </svg>
                  </span>
                  <!--end::Svg Icon-->
                  Export
                </button>
                <!--end::Export to Excel-->
              </div>
              <!--end::Toolbar-->
            </div>
          </div>
          <!--end::Actions-->
        </div>
      </div>
      <!--end::Toolbar-->
      <!--begin::Table Interview Tracker-->
      <table id="myInterviewSchedule" class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
          <tr class="table-bg-custom">
            <th>S.No</th>
            <th>Schedule Date</th>
            <th>Application</th>
            <th>Client</th>
            <th>Requisitions</th>
            <th>Source</th>
            <th>Status</th>
            <th>Interview Mode</th>
            <th>Interview Stage</th>
            <th>Interviewer</th>
            <th>Feedback</th>
          </tr>
        </thead>
      </table>
      <!--end::Table Interview Tracker-->
    </div>
  </div>
</div>
<!--end::Table-02-->

<div class="modal fade bs-modal-xl in" id="scheduleDetailModal" role="dialog" aria-labelledby="myModalLabel"
   aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-md in" id="scheduleDetailModal_joblisting" role="dialog" aria-labelledby="myModalLabel"
   aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_joblisting"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-exl in" id="scheduleDetailModalReportFilter" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingReportFilter"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"> </div>
    </div>
  </div>
</div>

<input type="hidden" name="start_date_hidden" id="start_date_hidden" value="<?php echo date('Y-m-d', strtotime('-7 days'));  ?>">
<input type="hidden" name="end_date_hidden" id="end_date_hidden" value="<?php echo date('Y-m-d');  ?>">
@endsection
@push('footer-script')

<!--begin::New JS-->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/node_modules/popper/popper.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- SlimScroll -->
<script src="{{ asset('assets/plugins/slimScroll/jquery.slimscroll.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- FastClick -->
<script src="{{ asset('assets/plugins/fastclick/fastclick.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src='https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.0/dist/js/bootstrap-select.min.js'></script>
<script src="{{ asset('assets/node_modules/sweetalert/sweetalert.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('froiden-helper/helper.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/toast-master/js/jquery.toast.js') }}<?php echo TM_VERSION; ?>"></script>
{{--sticky note script--}}
<script src="{{ asset('js/cbpFWTabs.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/icheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/icheck/icheck.init.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}<?php echo TM_VERSION; ?>"></script>
<!--end::New JS-->


<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.9.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.9.0/additional-methods.min.js"></script>


<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>

<script>
  $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>

<script type="text/javascript">
   $().ready(function() { 
   
   
   			// $('#custom_report_filter').click(function () {	
        $('.custom_report_filter1').click(function () {  			
        			url 				=  "{{ route('product_company.customreport.custom_report_filter') }}";	  				
        			$('#modelHeadingCustomReportFilter').html('Schedule');
        			$.ajaxModal('#scheduleDetailModalCustomReportFilter', url);		
            }); 



  
   			/*$("#report_frm").validate({
   
   											rules: {
   														start_date: "required",
   														end_date: "required"
   													},
   
   											messages: {
   															start_date: "Please enter Start date",
   															end_date: "Please enter End date"
   													}
   			});   */
   });   
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawChart);
   function drawChart() {
    
     var data = google.visualization.arrayToDataTable([  
       ['Clients', 'Requisitions'],  
       ['TCS',     11],
       ['HCL',      2],
       ['IBM',  2],
       ['Wipro', 2],
       ['ABC',    7]
     ]);		
     var options = {
	 					legend: 'none',
						 pieSliceText: 'label',
       					title: "",
   						pieSliceText: 'value'  
     };
     var chart = new google.visualization.PieChart(document.getElementById('client_piechart'));
     chart.draw(data, options);  
   
   }	    
</script>
<script type="text/javascript">
   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawChart);  
   function drawChart() { 
     var data = google.visualization.arrayToDataTable([
       ['Requisitions', 'Application'],
       ['Sr PHP Developer',     35],
       ['Automation Testing',      12],
       ['Java Developer',  5],
       ['SAP Programmer', 51],
       ['ABC',    41]
     ]);		
   
   
   
     var options = {
  	      				title: "Application's on Requisitions",
   						pieSliceText: 'value'  
     				};
   
   
   
     var chart = new google.visualization.PieChart(document.getElementById('application_on_jobs_piechart'));   
     chart.draw(data, options);
   
   
   }	  
</script>
<script type="text/javascript">
   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawVisualization);
   function drawVisualization() {
   
   
     var data = google.visualization.arrayToDataTable([
		   ['Month', 'Testing', 'Sr PHP Developer', 'Automation Testing', 'Java Developer', 'SAP Programmer', 'ABC'], 
		   ['2021/01',  165,      938,         522,             998,           450,      614.6],
		   ['2021/02',  135,      120,        599,             268,          288,      682],
		   ['2021/03',  157,      167,        587,             407,           397,      623],
		   ['2021/04',  139,      110,        615,             368,           215,      609.4],
		   ['2021/05',  136,      691,         629,             626,          366,      569.6]
     ]);
   
     var options = {
       					title : "Application's on Requisitions",
       					vAxis: {title: 'Application'},
       					hAxis: {title: 'Date'}, 
       					seriesType: 'bars',
       					series: {5: {type: 'line'}}
     				};
   
     var chart = new google.visualization.ComboChart(document.getElementById('application_on_jobs_barchart'));
     chart.draw(data, options);
   }
</script>
<script type="text/javascript">
   google.charts.load('current', {'packages':['corechart']});
   google.charts.setOnLoadCallback(drawChart);
   function drawChart() {
    
     var data = google.visualization.arrayToDataTable([
														   ['Date', 'Abu', 'Amit', 'Sonika', 'Vibhanshu'],
														   ['jan-2021',  1000,      400, 150, 340],
														   ['feb-2021',  1170,      460 , 245, 125],
														   ['march-2021',  660,       1120, 850, 374],
														   ['april-2021',  1030,      540, 893, 1170]
													 ]);
    
     var options = { 
					   title: 'Team Work(Application submited by team member)',
					   hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
					   vAxis: {minValue: 0}
					 };  
     var chart = new google.visualization.AreaChart(document.getElementById('team_work'));
     chart.draw(data, options);
   }
</script>
<script src="{{ asset('js/jquery.validate.js') }}<?php echo TM_VERSION; ?>"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
// validate signup form on keyup and submit
$("#report_frm").validate({
			onfocusout: function(e)
      {
        this.element(e);
    	},	
			rules:
      {						
				start_date: "required",
				end_date: "required"										
			},
			messages:
      {
				start_date: "Please select start date",
				end_date: "Please select end date"															
			}		 
});		
</script>
<script>
   loadTable(); 
   
   $('#start_date').datepicker({
   // format: 'yyyy-mm-dd'
   // format: 'd-M-Y: H:I:s'
   format: 'd-m-yyyy'
   })
   
   $('#end_date').datepicker({
   // format: 'yyyy-mm-dd'
   //format: 'd-M-Y: H:I:s'
   format: 'd-m-yyyy'
   })		
    
   function getClientJob(clientId)
   {
   
   	var clientId 		= clientId;					
   	var url 			= "{{ route('product_company.cvtracker.get_client_job',':clientId') }}";					
   	url 				= url.replace(':clientId', clientId);					
   	var baseurl 		= "{{ asset('assets/images/Loading2.gif') }}"; 									
   	var token 			= "{{ csrf_token() }}";
   	$.easyAjax({

       				type: 'POST',
       				url: url,
       				data: {'_token': token, '_method': 'POST', 'clientId' : clientId},
   						beforeSend: function() {
   													$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
   						},
              success: function (response)
              {   
 		 					$('#jobs').html(response.data);
 							$('.clientStatusLoader').html('');
 							//tableLoad('filter');
 						 },
   
   						complete: function (data) {
   								$('.clientStatusLoader').html('');
   						 }
   
                   });		
   
   }
   
   // Apply Filter   
   $('#apply-filters').click(function () {
   loadTable();
   });
   
   function loadTable()
   {   	
	   var custom_text			=	$('#custom_text').val();
	   var start_date			=	$('#start_date').val();
	   var end_date				=	$('#end_date').val();
	   var clients				=	$('#clients').val();             					                // Clients Default value = all else other number
	   var jobs					=	$('#jobs').val();									                          // Jobs Default value = all else other number
	   var source				=	$('#source').val();									                        // Team Default value = all else other number
	   var master_status_id		=	$('#master_status_id').val();			                		// Jobs Default value = all else other number
	   var interview_process 	= $('input[name="interview_process"]:checked').val();   // 0 For Not intrested interview schedule , 1 For intrested interview schedule 
     
	 
	 
	 	if((start_date == '') && (end_date == '') )
		{
			var start_date		=	$('#start_date_hidden').val();
			var end_date		=	$('#end_date_hidden').val();
			
			var custom_text			=	$('#custom_text').val();
			
			var clients				=	'all';             	// Clients Default value = all else other number
			var jobs				=	'all';								// Jobs Default value = all else other number
			var source				=	'all';						  // Team Default value = all else other number
			var master_status_id	=	'all';					// Jobs Default value = all else other number
			var interview_process 	=	1;   					// 0 For Not intrested interview schedule , 1 For intrested interview schedule 
	   			
		}
		
		//  if($("#report_frm").valid())
		//  { 
   
   		teamwork();
   		$("body").mLoading();
			var table = $('#myTable').dataTable({
      responsive     : false, 
      processing      : true,
      "scrollY"     : 200,
      autoWidth     : false, 
      destroy       : true,   
      stateSave     : true,
      colReorder      : true,     
      scrollCollapse    : true,       
      "bFilter"     : false,
      "bInfo"      : false,

      "paging":   true,
      "ordering": true,
			"bLengthChange"	: false,        
      
      "fixedHeader":
       {
        "header": false,
        "footer": false  
       },		

			   ajax: '{!! route('product_company.customreport.data') !!}?custom_text='+custom_text+'&start_date='+start_date+'&end_date='+end_date+'&clients='+clients+'&jobs='+jobs+'&source='+source+'&master_status_id='+master_status_id+'&interview_process='+interview_process,
				   language: {
					   "url": "<?php echo __("app.datatable") ?>"
				   },
			   
				   "fnDrawCallback": function( oSettings ) {
						$("body").tooltip({  
						   selector: '[data-toggle="tooltip"]' 
					   });  
				   },  
			   
					"initComplete":function( settings, json){
							
							setTimeout(function(){   
													$('#scheduleDetailModalReportFilter').modal('hide');
													$('#scheduleDetailModalReportFilter').modal().hide();
													  
												}, 1000);
							setTimeout(function(){$("body").mLoading('hide');}, 1500);
							
					},
					
			   "columnDefs": [   
								  { "width": "3%", "targets": 0 },
								  { "width": "25%", "targets": 1 },
								  { "width": "45%", "targets": 2 },
								  { "width": "3%", "targets": 3 },
								  { "width": "3%", "targets": 4 },
								  { "width": "3%", "targets": 5 },
								  { "width": "3%", "targets": 6 },
								  { "width": "3%", "targets": 7 },
								  { "width": "3%", "targets": 8 },
								  { "width": "3%", "targets": 9 },
								  { "width": "3%", "targets": 10 },
								  { "width": "3%", "targets": 11 } 
					],
			 columns: [
						{ data: 'id', width: '3%'},
						{ data: 'client_name', name: 'client_name', searchable : false, orderable : false, width: '25%'},
						{ data: 'job_title', name: 'job_title' , searchable : false, orderable : false, width: '45%'},
						{ data: 'source', name: 'source', searchable : false, orderable : false },
						{ data: 'total_application', name: 'total_application', searchable : false, orderable : false },	
						{ data: 'sourcing', name: 'sourcing', searchable : false, orderable : false },
						{ data: 'screening', name: 'screening', searchable : false, orderable : false },
						{ data: 'interview', name: 'interview', searchable : false, orderable : false },
						{ data: 'negotiation', name: 'negotiation', searchable : false, orderable : false },
						{ data: 'offered', name: 'offered', searchable : false, orderable : false },
						{ data: 'joined', name: 'joined', searchable : false, orderable : false },
						{ data: 'rejected', name: 'rejected', searchable : false, orderable : false }
			   
					]
			   
				});
			   
						new $.fn.dataTable.FixedHeader( table );					
			   
						if(interview_process == 1)
							{
			   
				 var table = $('#myInterviewSchedule').dataTable({
			   
			   
			   
												 responsive		: true,
												 processing		: true,
												 serverSide		: true,
												 "bFilter"		: false,
												 stateSave		: true,
                         "bInfo": false,
												"bLengthChange"	: false,
												 oLanguage		: {sLengthMenu: "_MENU_",},
												 lengthMenu		: [[10, 25, 50, 100,150,200,250,300], [10, 25, 50, 100,150, 200, 250, 300]],
			   
			   
			   destroy: true,
				//aaData: response.data,			   
			   //ajax: '{!! route('product_company.customreport.myInterviewSchedule') !!}',
			   ajax: '{!! route('product_company.customreport.myInterviewSchedule') !!}?custom_text='+custom_text+'&start_date='+start_date+'&end_date='+end_date+'&clients='+clients+'&jobs='+jobs+'&source='+source+'&master_status_id='+master_status_id+'&interview_process='+interview_process,
			   	   
				   language		: {"url": "<?php echo __("app.datatable") ?>"},
				   "fnDrawCallback": function( oSettings ) {
						$("body").tooltip({
								selector: '[data-toggle="tooltip"]'
							});
				   },
			   
			   "columnDefs": [
					  { "width": "10px", "targets": 0 },
					  { "width": "150px", "targets": 1 },
					  { "width": "200px", "targets": 2 },
					  { "width": "200px", "targets": 3 },
					  { "width": "300px", "targets": 4 },
					  { "width": "70px", "targets": 5 },
					  { "width": "70px", "targets": 6 },
					  { "width": "70px", "targets": 7 },
					  { "width": "70px", "targets": 8 },
					  { "width": "70px", "targets": 9 },
					  { "width": "70px", "targets": 10 },
					  { "width": "70px", "targets": 11 }
					],
			   			   
	columns: [
				{ data: 'id'},
				{ data: 'schedule_date', name: 'schedule_date', searchable : false, orderable : false},
				{ data: 'full_name', name: 'full_name', searchable : false, orderable : false },
				{ data: 'client_name', name: 'client_name' , searchable : false, orderable : false},		
				{ data: 'requisitions_title', name: 'requisitions_title', searchable : false, orderable : false },	
				{ data: 'source_user', name: 'source_user', searchable : false, orderable : false },
			   
				{ data: 'interview_status_name', name: 'interview_status_name', searchable : false, orderable : false },
				{ data: 'interview_mode', name: 'interview_mode', searchable : false, orderable : false },
				{ data: 'status_name', name: 'status_name', searchable : false, orderable : false }, 
				{ data: 'child_status_name', name: 'child_status_name', searchable : false, orderable : false },
				{ data: 'interviewers', name: 'interviewers', searchable : false, orderable : false },											
				{ data: 'feedback', name: 'feedback', searchable : false, orderable : false }
						]
			   
			   
			   
			   });
			   
							 new $.fn.dataTable.FixedHeader( table );
			   
				
			   
			   }
			   
 
  		//	} 
   
   
   }
    
   
   $('#myTable').on('click', '.joblisting', function() {
   
   var id 				= $(this).data('row-id');
   var row_status 		= $(this).data('row-status');			
   //url 				=  '{!! route('product_company.customreport.show') !!}?id='+id+'&row_status='+row_status;					
   url 				= '{!! route('product_company.customreport.show') !!}?job_id='+id+'&application_status='+row_status				
   	
          	$('#modelHeading_joblisting').html('Schedule');
          	$.ajaxModal('#scheduleDetailModal_joblisting', url);					
   });
     
  
   $('.report_filter').click(function () {				
			url 				= '{!! route('product_company.customreport.report_filter') !!}';				
			$('#modelHeadingReportFilter').html('Schedule');
			$.ajaxModal('#scheduleDetailModalReportFilter', url);		
   });  
   
   var chart = document.getElementById("myChart").getContext('2d');
   var cData = JSON.parse(`<?php echo $chart_data; ?>`);
   var keys = [];
   var value = [];
   $.each(cData, function(k, v) {
   					value.push(v)
   					keys.push(k)
   				});
   
    
   var myChart = new Chart(chart, {
   
   	type: 'pie',
     data: {
   	labels: keys,
   	datasets: [{
   				  backgroundColor: [
   										"#95a5a6",
   										"#9b59b6",
   										"#28a745",
   										"#3D8EE8",
   									  ],
   				  data:value,
   			 }]
   	}
   
   });
</script>
<script>

function exportExcel_application()
{
	
		
				var custom_text 				= '';
				var start_date 					= '';
				var end_date 					= '';
				var clients			 			= '';
				var jobs 						= '';
				var master_status_id 			= '';			
				var source           			= '';
				var interview_process           = '';
				//   var interview_process 	= $('input[name="interview_process"]:checked').val();  // 0 For Not intrested interview schedule , 1 For intrested interview schedule 
				
			 	custom_text				=	$('#custom_text').val();
			 	start_date				=	$('#start_date').val();
			 	end_date				=	$('#end_date').val();
			 	clients					=	$('#clients').val();
			 	jobs					=	$('#jobs').val();  
			 	master_status_id		=	$('#master_status_id').val();
				source					=	$('#source').val();
				interview_process		=	$('input[name="interview_process"]:checked').val();
			
				var table 				= $('#myTable').dataTable();
				var length 				= table.fnGetData().length;
						
				var oSettings 			= table.fnSettings();
				var start     			= oSettings._iDisplayStart;			
						
				start_date 				= start_date.replace('/', '-');
				start_date 				= start_date.replace('/', '-'); 
				
				end_date 				= end_date.replace('/', '-');
				end_date 				= end_date.replace('/', '-'); 
				
				if(custom_text == '')
				{
						custom_text = 'none';
				}
  
			//alert(end_date);  //04/01/2021
			// return false; 
			 
		//var url 				= '{{ route('product_company.cvtracker.export', [':clients', ':jobs', ':source', ':search_passport', ':search_np_id', ':status' , ':start' , ':length']) }}';			

var url 		= '{{ route('product_company.customreport.export', [':clients', ':jobs', ':master_status_id', ':source' ,  ':custom_text', ':start_date', ':end_date', ':start' , ':length']) }}';
												
			url 					= url.replace(':clients', clients);
			url 					= url.replace(':jobs', jobs);
			url 					= url.replace(':master_status_id', master_status_id);
			url 					= url.replace(':source', source);
			url 					= url.replace(':custom_text', custom_text);
			url 					= url.replace(':start_date', start_date);
			url 					= url.replace(':end_date', end_date);
			
			url 					= url.replace(':start', start);
			url 					= url.replace(':length', length);
		
           window.location.href = url;
						
			
        
}

function exportExcel_interview()
{
	
	
		var custom_text 				= '';
		var start_date 					= '';
		var end_date 					= '';
		var clients			 			= '';
		var jobs 						= '';
		var master_status_id 			= '';			
		var source           			= '';
		var interview_process           = '';
				
		custom_text						=	$('#custom_text').val();
		start_date						=	$('#start_date').val();
		end_date						=	$('#end_date').val();
		clients							=	$('#clients').val();
		jobs							=	$('#jobs').val();  
		master_status_id				=	$('#master_status_id').val();
		source							=	$('#source').val();
		interview_process				=	$('input[name="interview_process"]:checked').val();
			
		var table 						= $('#myTable').dataTable();
		var length 						= table.fnGetData().length;
						
		var oSettings 					= table.fnSettings();
		var start     					= oSettings._iDisplayStart;			
						
		start_date 						= start_date.replace('/', '-');
		start_date 						= start_date.replace('/', '-'); 
				
		end_date 						= end_date.replace('/', '-');
		end_date 						= end_date.replace('/', '-'); 
				
		if(custom_text == '')
		{
						custom_text = 'none';
		}
		
		var url 						= '{{ route('product_company.customreport.interviewexport', [':clients', ':jobs', ':master_status_id', ':source' ,  ':custom_text', ':start_date', ':end_date', ':start' , ':length']) }}';	
		
			url 					= url.replace(':clients', clients);
			url 					= url.replace(':jobs', jobs);
			url 					= url.replace(':master_status_id', master_status_id);
			url 					= url.replace(':source', source);
			url 					= url.replace(':custom_text', custom_text);
			url 					= url.replace(':start_date', start_date);
			url 					= url.replace(':end_date', end_date);
			
			url 					= url.replace(':start', start);
			url 					= url.replace(':length', length);
		
           window.location.href = url;
	
			
}
		
// Reset Filters
$('#reset-filters').click(function () {
           
		   $('#custom_text').val('');
		   $('#start_date').val('');
		   $('#end_date').val('');
		   $('#clients').val('all');
		   $('#jobs').val('all');
		   $('#master_status_id').val('all');
		   $('#source').val('all');
          	loadTable();
			
			//var table = $('#myTable').DataTable(); 
 
			$( "#myTable tbody tr" ).each( function(){
				this.parentNode.removeChild( this ); 
			});
			$( "#myInterviewSchedule tbody tr" ).each( function(){
				this.parentNode.removeChild( this ); 
			});
			
			 
	
			
        })
					
</script>
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/jquery.mloading.js') }}<?php echo TM_VERSION; ?>"></script>
<link rel="stylesheet" href="{{ asset('css/jquery.mloading.css') }}<?php echo TM_VERSION; ?>">
<script>

   	function teamwork()
			{
					var custom_text 				= '';
					var start_date 					= '';
					var end_date 					= '';
					var clients			 			= '';
					var jobs 						= '';
					var master_status_id 			= '';			
					var source           			= '';
					var interview_process           = '';
				
					custom_text						=	$('#custom_text').val();
					start_date						=	$('#start_date').val();
					end_date						=	$('#end_date').val();
					clients							=	$('#clients').val();
					jobs							=	$('#jobs').val();  
					master_status_id				=	$('#master_status_id').val();
					source							=	$('#source').val();
					interview_process				=	$('input[name="interview_process"]:checked').val();
									
					start_date 						= start_date.replace('/', '-');
					start_date 						= start_date.replace('/', '-'); 
							
					end_date 						= end_date.replace('/', '-');
					end_date 						= end_date.replace('/', '-'); 
				
					if(custom_text == '')
					{
									custom_text = 'none';
					}
		
				
			
					var clientId 		= clients;
					var url 			= "{{ route('product_company.customreport.chart_teamwork',':clientId') }}";
                    url 				= url.replace(':clientId', clientId);
					var baseurl 		= "{{ asset('assets/images/Loading2.gif') }}"; 				
                    var token 			= "{{ csrf_token() }}";
					//alert(clientId); 
					
					 $.easyAjax({
                        				type: 'POST',
                        				url: url,
                        				data: {'_token': token, '_method': 'POST', 'custom_text' : custom_text, 'start_date' : start_date,  'end_date' : end_date, 'clients' : clients, 'jobs' : jobs, 'master_status_id' : master_status_id, 'source' : source, 'interview_process' : interview_process},
										beforeSend: function() {
																	$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
										},
                        				success: function (response) {
                         
						 					$('#chart_teamwork').html(response.view);
											//$('.clientStatusLoader').html('');
											//tableLoad('filter');
											
										
											
										},
										complete: function (data) {
												$('.clientStatusLoader').html('');
										 }
                    });
	}	
</script>
@endpush