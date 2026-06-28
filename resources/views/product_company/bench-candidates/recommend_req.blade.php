@extends('layouts.product_company_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
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
		#kt_activitiesJob {width: 1025px;}
		}
		@media only screen and (min-width: 1440px) and (max-width: 2560px){
		#kt_activitiesJob {width: 1200px;}
		}
		@media only screen and (min-width: 1024px) and (max-width: 1440px){
		#kt_activitiesClientjob_toggle {width: 1025px;}
		}
		@media only screen and (min-width: 1440px) and (max-width: 2560px){
		#kt_activitiesClientjob_toggle {width: 1200px;}
		}
</style>
@endpush

@section('content')
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
@php 
        
        $jobtitle				=	@$job->title;
        $bgTextDecore			=	$strVal	=	'';
        $str					=	@$bench_detail->full_name;
        $str	= $strVal		=	strtolower($str[0]);
        $str					=	ord($str);
        if($str >=97 && $str <=101){
          $bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
        }else if($str >=102 && $str <=106){
          $bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
        }else if($str >=107 && $str <=111){
          $bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
        }else if($str >=112 && $str <=116){
          $bgTextDecore		=	'fs-3 bg-light-success text-success';	
        }else if($str >=117 && $str <=121){
          $bgTextDecore		=	'fs-3 bg-dark text-secondary';	
        }else {
          $bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
        }	
        
@endphp 

      <td class="d-flex align-items-center border-0">
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> 
			<a href="javascript:void(0);">
          		<div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
          	</a> 
		</div>
        <div class="d-flex flex-column"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$bench_detail->full_name) }}</a> 
			<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">{{ ucwords(@$bench_detail->current_designation) }}</a> 
		</div>
      </td>
      
	  <td>
	  	<div class="d-flex flex-column ml-2"> 
			<span class="badge fw-bolder fs-6 joined-bg px-2 py-1">{{ ucwords(@$bench_detail->name) }}</span> 
			<span class="text-muted fw-bold text-muted d-block fs-7">B-{{ ucwords(@$bench_detail->id) }}</span> 
		</div>
	</td>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-primary mb-1">Recommend Requisition</a> </h5>
        <span class="text-gray-600">&nbsp;</span> </div>
      </div>
    
	<form action="" id="filter-form" style="">
      @csrf
      <input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
      <input type="hidden" name="mstatus_id" id="mstatus_id" value="{{ @$mstatus_id }}" />
      <div class="d-flex align-items-center py-1">
        <div class="d-flex align-items-center position-relative my-1 me-3">
          <span class="svg-icon svg-icon-1 position-absolute ms-6">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
          </svg>
          </span>
          <input type="text" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
        </div>
        <div class="card-toolbar">
          <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
            <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
            <span class="svg-icon svg-icon-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
            </svg>
            </span>
            Filter </button>
            <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none" data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
            <span class="svg-icon svg-icon-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
              <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black" />
              <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
            </svg>
            </span>
            Export</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<!--end::Toolbar-->


<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="card">
        <div class="card-body pt-3 px-5">
		
          <table id="recommendedReq" class="table align-middle table-row-dashed fs-6 gy-5">
          <thead>
            <tr class="fw-bolder text-muted">
              <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3"><input class="form-check-input" type="checkbox" value="1" /></div></th>
              <th class="">Requisition</th>
              <th class="">Exp.</th>
              <th class="">Salary</th>
              <th class="">Location</th>
              <th class="">Status</th>
              <th class="">Date | OPN</th>
              <th class="">HM | CP</th>
              <th class="">Action</th>
            </tr>
          </thead>
          <tbody>
		  
            
          </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade bs-modal-sm in" id="scheduleDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingStatus"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn default" data-dismiss="modal">Close</button>
        <button type="button" class="btn blue">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalSizeLg" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> ... </div>
</div>

<div class="modal fade right" id="sidebar-right" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg">
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Right Sidebar</h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-pills nav-stacked text-center lead">
          <li role="presentation" class="active"> <a href="#home">Home</a> </li>
          <li role="presentation" class="dropdown"> 
		  	<a href="#" class="dropdown-toggle" id="myTabDrop1" data-toggle="dropdown" aria-controls="myTabDrop1-contents" aria-expanded="false">
				Services <span class="fa fa-fw fa-chevron-down"></span> 
			</a>
            <ul class="dropdown-menu btn-block">
              <li> <a href="#design" role="tab">Design</a> </li>
              <li> <a href="#develop" role="tab">Develop</a> </li>
              <li> <a href="#support" role="tab">Support</a> </li>
            </ul>
          </li>
          <li role="presentation"> <a href="#about">About</a> </li>
        </ul>
        <hr>
        <div class="form-group hide">
          <div class="input-group">
            <input class="form-control" placeholder="Search" name="customSearch" id="customSearch" class="customSearch">
            <span class="input-group-btn">
            <button class="btn btn-default" type="button"> <i class="fa fa-fw fa-search"></i> </button>
            </span> </div>
        </div>
        <div class="form-group has-feedback">
          <input type="text" class="form-control" id="search-right" name="search-right" class="search-right" placeholder="Search">
          <span class="glyphicon glyphicon-search form-control-feedback" aria-hidden="true"></span> </div>
        <hr>
        <div class="btn-group-vertical center-block"> <a class="btn btn-instagram btn-lg" href="https://www.instagram.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-instagram"></i> </a> <a class="btn btn-twitter btn-lg" href="https://www.twitter.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-twitter"></i> </a> <a class="btn btn-github btn-lg" href="https://www.github.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-github"></i> </a> </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-lg in" id="JobDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="JobDetailModalStatusData"></span> </div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-md in" id="scheduleDetailModal_vis" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_vis"></span> </div>
    </div>
  </div>
</div>

<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Requisition</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close">
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span>
        </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>
<!--end::Content-->

@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>	
<!--<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>-->

<script>
   $(document).ready(function() {	

	   $('#inputSearch').keyup(function() {
			table._fnDraw();
	   });

     $(document).on("click",".request-send", function(){
      var jobId  = $(this).attr('data-jobId');
      var conId  = $(this).attr('data-conId');
      //       url     		=	url.replace(':id', jobId);
      // alert(url)
      var elm = $(this);
      swal({
          title: "Are you sure?",
          text: " Recommend Requisition!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: '#DD6B55',
          confirmButtonText: 'Yes, I am sure!',
          cancelButtonText: "No, cancel it!",
          closeOnConfirm: false,
          closeOnCancel: false
      },
      function(isConfirm){
        if (isConfirm){
        // jobs/job_request
            var url   		=	"{{ route('product_company.jobs.recommandedrequestcondidate',':id') }}";
            url     		=	url.replace(':id', jobId)+"?conId="+conId;
            var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		      $.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                elm.parent().html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
                swal("Recommend!", "Candidates are successfully Recommend!", "success");
                setTimeout(() => {
                    location.reload();
                  }, 1000);
   						}
              

   	  			});

           

          } else {

            swal("Cancelled", "", "error");

          }
      });
    });

   });
</script>
<script>
   var table = $('#recommendedReq').dataTable({
   
   			responsive  	: true,
   			processing  	: true,
   			serverSide  	: true,
   			destroy   		: true,
   			ordering		: false, //disable ordering	
   			"bLengthChange" : false, //thought this line could hide the LengthMenu   			
   			sDom			: 'lrtip',   			
   			oLanguage 		: { sLengthMenu: "_MENU_", },
			ajax			: {'url' : '{!! route('product_company.recommend.all_data') !!}', "data": function ( d ){d.search = $('#inputSearch').val(),d.condidate_id = '{{$segment}}'}},
   		  "drawCallback"	: function( settings ) {
									$('[data-toggle="tooltip1"]').tooltip();
									$('[data-toggle="tooltip2"]').tooltip();
									$('[data-toggle="tooltip3"]').tooltip();
									$('[data-toggle="tooltip4"]').tooltip();		
							},
   		  "columnDefs"		:
   						  		[ 
   									{ "width": "50px", "targets": 0, "width": "4%", "class":""},
   									{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
   									{  "width": "50px"  , "targets": 2},		
   									{  "width": "100px", "targets": 3 },		
   									{  "width": "200px", "targets": 4 }, 
                    {  "width": "100px" ,"targets": 5 },
   									{  "width": "150px" ,"targets": 6 },
   									{  "width": "200px" , "targets": 7 },  
									  { "width": "200px", "targets": 8 }  	
   							  ], 
   				columns:
   				[
   				  { data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
   					{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
   					{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
   					{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
   					{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
   					
   					{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
   					{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
   					{ data: 'HM', name: 'HM' , orderable: false, searchable: false},
   					{ data: 'action', name: 'action' , orderable: false, searchable: false} 	   
   				],				
				//timeout: 500000,
           		async: false
   				
   				}); 
   
   	new $.fn.dataTable.FixedHeader( table );
   
   table.on('click', '.show-detail', function ()
   	{
   		$('#kt_activitiesJob_toggle').trigger('click');		
   		var id    		=	$(this).data('row-id');
   		var url   		=	"{{ route('product_company.jobs.viewmore',':id') }}";
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
   	
		
table.on('click', '.status_stage', function () { 
		$('#status_body_content').html('');
		var id        	=	$(this).data('row-id');	
		var url       	=	"{{ route('product_company.jobs.editJobStatus',':id') }}";
		url           	=	url.replace(':id', id);
		$.ajax({
					type  	: 'GET',
					url   	: url,
					success : function (response) {
						if (response.status == "success") {
							$('#status_body_content').html(response.view);      
						}
					}
		}); 			
});
	
 table.on('click', '.client-show-detail', function ()
{
	$('#kt_activitiesClientjob_toggle').trigger('click');	
	var id    		=	$(this).data('row-id');
	var url   		=	"{{ route('product_company.jobs.clientviewmore',':id') }}";
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
	
	
table.on('click', '.spartner-listing', function ()
{
	$('#kt_activitiesStaffing_toggle').trigger('click');		
	var id    		= $(this).data('row-id');
	var url   		= "{{ route('product_company.jobs.spartnerlisting',':id') }}";
	url     		= url.replace(':id', id);
	 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
	$.ajax({
					type  : 'GET',
					url   : url,
					 beforeSend: function() {
								 $('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
					},
					success : function (response) {
							$('#kt_activities_body').html(response.view);		
					}
			});
}); 
	
   
   function updateJobStatus(id, statusName)
   {
     var id        	= id;
     var statusName  = statusName;
     var url       	= "{{ route('product_company.jobs.updatestatus',':id') }}";
     url         	= url.replace(':id', id);
     var token       = "{{ csrf_token() }}";
     $.easyAjax({
					  type		: 'POST',
					  url		: url,
					  data		: {'_token': token, '_method': 'POST', 'id':id, 'statusName':statusName},
					  success	: function (response){
						  table._fnDraw();
					 }
				 });
   }
   
    
</script>
<script>

$(document).ready(function(){

		table.on('click', '.hmlisting', function(){
			
														$('#kt_activitiesRecruiter_toggle').trigger('click');  
														var id    		= $(this).data('row-id');
														var token 		=	"{{ csrf_token() }}";
										
											
														var url   		=	"{{ route('product_company.jobs.hmlisting',':id') }}"; 
														url     		=	url.replace(':id', id);  		
														var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
														$.ajax({
																	type  		: 'GET',
																	url   		: url,
																	data		: {'_token': token, 'id' : id},
																	 beforeSend	: function() {
																				 $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
																	},
																	success 	: function (response) {
																				$('#kt_activitiesRecruiter_body').html(response.view);
																	}
															}); 
												
		
		});
		 

});
</script>
<script>

table.on('click', '.client-hm-listing', function ()
   	{	
   		$('#kt_activitiesCHM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('product_company.jobs.clienthmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesCHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) {
								$('#kt_activitiesCHM_body').html(response.view);  								
   						}
   	  			});
   	});
	
table.on('click', '.team-listing', function ()
   	{
   	
   		$('#kt_activitiesTEAM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('product_company.jobs.currentteamlisting',':id') }}";
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
	
</script>
<script>

table.on('click', '.hm-listing', function ()
   	{
			
   		$('#kt_activitiesHM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('product_company.jobs.hmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) {		
								$('#kt_activitiesHM_body').html(response.view);		
   						}
   	  			});
   	});



</script>
@endpush 

