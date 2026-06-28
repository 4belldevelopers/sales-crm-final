@extends('layouts.product_company_layout')
<style>
.border-radius-5
{
  border-radius: 5px!important;
}
.ml-2
{
   margin-left: 2em;
}

/*Media query*/
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0.75em 0rem!important;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important; 
  }
}

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesClient
  {
    width: 1025px;
  }
}

@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesClient
  {
    width: 1200px;
  }
}


@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesDirectClient
  {
    width: 1025px;
  }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesDirectClient
  {
    width: 1200px;
  }
}

/*Media query end*/
</style>

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

@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesInvoice {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesInvoice {width: 1200px;}
}


</style>

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::All Client-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-primary">Invoice</a> </h5>
        <span class="text-gray-600" id="span_clientAll"><?php echo @$invoice_counter; ?></span> </div>
      <!--end::All Client-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
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
        <input type="text" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>
      <!--end::Search-->
      
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
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
          <!--begin::Table-->
          <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5" >
            <!--begin::Table head-->
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#myTable .form-check-input" value="1" />
                  </div></th>
                <th class="">Invoice No</th>
                <th class="">Company</th>
                <th class="">Payment Mode</th>
                <th class="">Amount Paid</th>
                <th class="">Invoice Download</th>
				<th class="">Create</th>
              </tr>
            </thead>
            <!--end::Table head-->
          </table>
          <!--end::Table-->
          </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post--> 


         


</div>
<!--end::Content-->


<div id="kt_activitiesInvoice_toggle"></div>
<div id="kt_activitiesInvoice" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesInvoice_toggle" data-kt-drawer-close="#kt_activitiesInvoice_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Invoice Detail</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesInvoice_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesInvoice_body"></div>
  </div>
</div>



@endsection

@push('footer-script')
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>
<script>
	
	 var myPackageHistory 	= $('#myTable').dataTable({
				responsive     		: true, 
      			processing      	: true,
				ordering			: false, //disable ordering       			
      			destroy       		: true,   
      			stateSave     		: true,      
      			"bLengthChange"   	: false,   
				sDom				: 'lrtip',
				//responsive		: true,
            	// processing		: true,				
				searching			: false,
            	serverSide			: true,
            	ajax				: '{!! route('product_company.subscribe.historylisting') !!}',
            	language			: {
                						//"url": "<?php echo __("app.datatable") ?>" 
            						},
            	"fnDrawCallback"	: function( oSettings ) {
                												$("body").tooltip({
                    																selector: '[data-toggle="tooltip"]'
                													});
            						  },
            	columns: [
               							{ data: 'chkbox', name: 'chkbox', searchable : false, orderable : false},
										{ data: 'InvoiceNo', name: 'InvoiceNo', searchable : false, orderable : false},
										{ data: 'Company', name: 'Company', searchable : false, orderable : false},
										{ data: 'PaymentMode', name: 'PaymentMode', searchable : false, orderable : false},
										{ data: 'AmountPaid', name: 'AmountPaid', orderable : false},
										{ data: 'InvoiceDownload', name: 'InvoiceDownload', orderable : false},
										{ data: 'Create', name: 'Create', orderable : false}
            		     ]
        });
      new $.fn.dataTable.FixedHeader( myPackageHistory );

 	  myPackageHistory.on('click', '.show-detail', function ()
		{ 
			
			 
			$('#kt_activitiesInvoice_toggle').trigger('click');		
			var id    		=	$(this).data('row-id');
			var url   		=	"{{ route('product_company.subscribe.viewmore',':id') }}";
			url     		=	url.replace(':id', id);
			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
			$.ajax({
							type  		: 'GET',
							url   		: url,
							 beforeSend	: function() {
										 $('#kt_activitiesInvoice_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
							},
							success 	: function (response) 
							{
									setTimeout(
									()=>
									{
											if (response.status == "success")
											{
												$('#kt_activitiesInvoice_body').html(response.view);
											}
									}, 500);
									
							}
					});
		}); 
	
	
</script>




<script>

	function f1(){
		//
		console.log('F-1');
		
	 
	}
	
	console.log(this);
</script>
@endpush 