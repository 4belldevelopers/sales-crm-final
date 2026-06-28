@extends('layouts.superadmin_layout')
@push('head-script')
<style>
body{overflow-x:hidden;}
</style>
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
@php

$userPermissions = array('add_team','view_team');

@endphp

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <span class="text-gray-800 mb-1">Team</span> </h5>
        <span class="text-gray-600" id="">{{ $usersCount}}</span> </div>
      <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
    <div class="d-flex align-items-center py-1">
      <div class="d-flex align-items-center position-relative my-1 me-3"> <span class="svg-icon svg-icon-1 position-absolute ms-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
          <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
        </svg>
        </span>
        <input type="text" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>
      @if(in_array("add_team", $userPermissions)) <a href="javascript:void(0);" class="btn btn-primary align-self-center add_team_member me-3" data-toggle="modal" data-target="#add_team_member"> <span class="svg-icon svg-icon-primary svg-icon-1x">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <polygon points="0 0 24 0 24 24 0 24"></polygon>
          <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
          <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
        </g>
      </svg>
      </span>Add Member </a> @endif </div>
  </div>
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
<div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-3">
    <div class="card">
      <div class="card-body pt-3 px-5"> @if(in_array("view_team", $userPermissions))
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
          <thead>
            <tr class="fw-bolder text-muted">
              <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input1" value="1">
                </div></th>
              <th class="min-w-250px">Name</th>
			  <th class="min-w-150px">Designation</th>
              <th class="min-w-150px">Role</th>
              <th class="min-w-150px">Login Status</th>
              <th class="min-w-150px">Working Status</th>
              <th class="min-w-150px">Added Date</th>
            </tr>
          </thead>
        </table>
        @endif </div>
    </div>
  </div>
</div>
<!--end::Content-->

<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">User Profile</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesClient_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
  </div>
</div>

<!--begin::Add Team Member Modal-->
<div class="modal fade" id="add_team_member" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content">
      <div class="modal-header" id="add_team_member_header">
        <h2 class="fw-bolder">Add Team Member</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y mx-5">
        <form id="add_team_member_form" name="add_team_member_form" class="form" action="#" method="post">
          <meta name="csrf-token" content="{{ csrf_token() }}" />
          @csrf  
          @method('POST')
          <div id="load_add_team_invitation_content"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Add team member popup-->

<!--begin::Update role-->
<div class="modal fade show" id="update_role" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content">
      <div class="modal-header" id="update_role_header" data-dismiss="modal">
        <h2 class="fw-bolder">Update role</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary update_role_close"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y mx-5" id="update_role_body"> </div>
    </div>
  </div>
</div>
<!--end::Update role-->


<div class="modal fade" id="status_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Requisition Status</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"  id="status_sub_stage_close"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span> </div>
      </div>
      <div class="status_body_content" id="status_body_content"></div>
    </div>
  </div>
</div>

@endsection

@push('footer-script')

<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<div class="closeclass"></div>

<!--<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>-->

<script>

	$('#inputSearch').keyup(function() {
   		table._fnDraw();      
   }); 
   
   $(".update_role_close").click(function(){
	   $("#update_role").removeAttr("style");
   });
   
   $("#kt_activitiesClient_close").click(function(){
	$(".closeclass").removeAttr("style");
	$(".closeclass").removeClass("drawer-overlay");
	$("#kt_activitiesClient").removeAttr("style");
   });

   $(".closeclass").click(function(){
	
	$("#kt_activitiesClient_close").click();
   });
   
    
	var table = $('#myTable').dataTable({
   
											responsive      :	true,
											processing      :	true,
											serverSide      :	true,
											"bFilter"       :	false,
											stateSave       :	true,
											"bInfo"         :	true, // Showing table records
											"bLengthChange" :	true,
											oLanguage		:	{ sLengthMenu: "_MENU_", },   
   											"lengthMenu"	:	[[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
   											"pageLength"	:	25,   	
											ajax			: 	{'url' : '{!! route('superadmin.team.data') !!}', "data": function ( d ) {d.search = $('#inputSearch').val()} },
											"columnDefs"	: 	[
																	{ "width": "3%", "targets": 0 },
																	{ "width": "30%", "targets": 1 },
																	{ "width": "20%", "targets": 2 },
																	{ "width": "20%", "targets": 3 },
																	{ "width": "15%", "targets": 4 },
																	{ "width": "15%", "targets": 5 }
																],
											columns			:	[
																	{ data: 'DT_Row_Index', orderable : false, searchable : true},
																	{ data: 'Name', name: 'Name', orderable : false, searchable : true },
																	{ data: 'designation', name: 'designation', orderable : false, searchable : true },
																	{ data: 'Role', name: 'Role', orderable : false, searchable : true },
																	{ data: 'Status', name: 'Status', orderable : false, searchable : false },
																	{ data: 'WorkingStatus', name: 'WorkingStatus', orderable : false, searchable : false},
																	{ data: 'AddedDate', name: 'AddedDate', orderable : false, searchable : false}	
																]
         });
		 new $.fn.dataTable.FixedHeader( table );
	table.on('click', '.show-detail-team', function (){
					$('#kt_activitiesClient_toggle').trigger('click');
					var id    			=	$(this).data('row-id');
					var url   			=	"{{ route('superadmin.team.viewmore',':id') }}";
					url     			=	url.replace(':id', id);
					var baseurl    		=	"{{ asset('assets/images/Loading2.gif') }}"; 
					$.ajax({
									type  		:	'GET',
									url   		:	url,
									beforeSend	:	function() { $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">'); },
									success 	:	function (response) {
										console.log("Team data"+response);
										$("#kt_activitiesClient").removeAttr("style");
										$(".closeclass").addClass("drawer-overlay");
										$(".closeclass").attr("style","z-index: 109");
                $("#kt_activitiesClient").attr("style","width: 1200px !important; transform: none; box-shadow: 0 1px 9px -3px rgba(0, 0, 0, 0.05); transition: transform 0.3s ease-in-out;");
														$('#kt_activitiesClient_body').html(response.view);	
												 	}
							});

							setTimeout(() => {
								var userId		=	id;  

								var load_jobs 	=	$('#load_jobs').dataTable({   
														responsive  	:	true,
														processing  	:	true,
														serverSide  	:	true,
														destroy   		:	true,
														ordering		:	false, //disable ordering   			
														"bLengthChange" :	false, //thought this line could hide the LengthMenu
														sDom			:	'lrtip',
														oLanguage 		:	{sLengthMenu: "_MENU_",},   
														ajax			:	{'url' : '{!! route('superadmin.team.jobdata') !!}?userId='+userId, "data": function ( d ) { d.search = $('#inputSearch').val(),
														d.team = 'team' } }, 
														"drawCallback"	: 	function( settings ) {   				
																					$('[data-toggle="tooltip1"]').tooltip();
																					$('[data-toggle="tooltip2"]').tooltip();
																					$('[data-toggle="tooltip3"]').tooltip();
																					$('[data-toggle="tooltip4"]').tooltip();		
																			 },
														"columnDefs"	:
																	  		[ 
																				{ "width": "50px", "targets": 0, "width": "4%", "class":""},
																				{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
																				{  "width": "50px"  , "targets": 2},		
																				{  "width": "50px", "targets": 3 },		
																				{  "width": "200px", "targets": 4 }, 
																				{  "width": "50px" ,"targets": 5 },
																				{  "width": "200px" , "targets": 6 },              
																				{  "width": "150px" , "targets": 7 },	
																				{  "width": "150px", "targets": 8 },	
   										
																				{ "width": "20px", "targets": 9 },	
																				{ "width": "20px", "targets": 10 }, 
																				{ "width": "20px", "targets": 11 },    
																				{ "width": "20px", "targets": 12 },             
																				{ "width": "20px", "targets": 13 },
																				{ "width": "20px", "targets": 14 },   
																				{ "width": "20px", "targets": 15 }, 
																				{ "width": "20px", "targets": 16 }
																		  	], 
														columns			:
																			[
																				{ data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
																				{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
																				{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
																				{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
																				{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
																
																				{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
																				{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
																				{ data: 'HM', name: 'HM' , orderable: false, searchable: false},
																				{ data: 'Partners', name: 'Partners' , orderable: false, searchable: false},
																				{ data: 'TOT', name: 'TOT' , orderable: false, searchable: false},
																				{ data: 'SOU', name: 'SOU' , orderable: false, searchable: false},					
																				{ data: 'SCR', name: 'SCR' , orderable: false, searchable: false},
																				
																				{ data: 'EVA', name: 'EVA' , orderable: false, searchable: false},
																				{ data: 'NEO', name: 'NEO' , orderable: false, searchable: false},
																				{ data: 'OFF', name: 'OFF' , orderable: false, searchable: false},
																				{ data: 'JOI', name: 'JOI' , orderable: false, searchable: false},
																				{ data: 'REJ', name: 'REJ' , orderable: false, searchable: false}	   
																			]
   				
   				                });    
                             	new $.fn.dataTable.FixedHeader( load_jobs );


								///////////////////////

								application_table = $('#load_application').dataTable({
      								
									  responsive     		:	true, 
									  processing      		:	true,
									  ordering				:	false, //disable ordering
									  destroy       		:	true,   
									  stateSave     		:	true,
									  "bLengthChange"   	:	false,   
									  sDom					:	'lrtip',       
										ajax					:	'{!! route('superadmin.team.appdata') !!}?userId='+userId,             
									  "initComplete"		:	function( settings, json){ 
																							$('.clientStatusLoader').html('');
																							$('.jobLoader').html('');
																							$('.sourceLoader').html('');
																							$('.search_np_idLoader').html('');
																							$('.search_passportLoader').html('');
																							$('.statusLoader').html('');            
																 }, 	
									"columnDefs"			:
																	[ 
																		{ "width": "50px", "targets": 0, "width": "4%", "class":""},
																		{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
																		{  "width": "50px"  , "targets": 2},		
																		{  "width": "50px", "targets": 3 },		
																		{  "width": "200px", "targets": 4 }, 
																		{  "width": "50px" ,"targets": 5 },
																		{  "width": "200px" , "targets": 6 },              
																		{  "width": "150px" , "targets": 7 },	
																		{  "width": "150px", "targets": 8 },	
								
																		{ "width": "20px", "targets": 9 },	
																		{ "width": "20px", "targets": 10 }, 
																		{ "width": "20px", "targets": 11 }   
																		
																	], 
																						
									columns					: [
																	{ data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
																	{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},						
																	{data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '15%'},						
																	{data: 'Exp', name: 'Exp', searchable : false, orderable : false,  width: '5%'},
																	{data: 'Salary', name: 'Salary',  searchable : false, orderable : false,  width: '5%'},   
																	{data: 'NoticePeriod', name: 'NoticePeriod', searchable : false, orderable : false},
																	{data: 'Qualifaction', name: 'Qualifaction',  searchable : false, orderable : false},		
																	{data: 'Location', name: 'Location', orderable : false},  												 
																	{data: 'Source', name: 'Source', orderable: false},
																	{data: 'UploadDate', name: 'UploadDate', orderable: false},
																	{data: 'Requisitions', name: 'Requisitions', orderable: false},
																	{data: 'Stage', name: 'Stage', orderable: false}
																]         
							  
								}); 
								new $.fn.dataTable.FixedHeader(application_table);

                            /////////////////////////////////

							jointable = $('#myTable_joining').dataTable({
													responsive  	:	true,
													processing  	:	true,
													serverSide  	:	true,
													destroy   		:	true,
													ordering		:	false, //disable ordering
   													"bLengthChange" :	false, //thought this line could hide the LengthMenu
		   											sDom			:	'lrtip',
		   											oLanguage 		:	{sLengthMenu: "_MENU_",},
           											ajax			:	'{!! route('superadmin.team.joiningdata') !!}?userId='+userId,       
												    columns			:
																		[
																			{data: 'chkbox', name:'chkbox', width: '5%', orderable: false, searchable: false},
																			{data: 'Name', name:'Name', width: '20%', orderable: false, searchable: false},
																			{data: 'Contact', name:'Contact', width: '15%', orderable: false, searchable: false},
																			{data: 'Requisitions', name:'Requisitions', width: '15%', orderable: false, searchable: false},
																			{data: 'Source', name:'Source', width: '10%', orderable: false, searchable: false},
																			{data: 'Offer', name:'Offer', width: '10%', orderable: false, searchable: false},
																			{data: 'Joining', name:'Joining', width: '10%', orderable: false, searchable: false},
																			{data: 'Stage', name:'Stage', width: '10%', orderable: false, searchable: false} 
																		]
							});
							new $.fn.dataTable.FixedHeader(jointable);
							//////////////////////////////////
							inttable  =	$('#load_interviewSchedule').dataTable({
													responsive  	:	true,
													processing  	:	true,
													serverSide  	:	true,
													destroy   		:	true,
													ordering		:	false, //disable ordering
													"bLengthChange" :	false, //thought this line could hide the LengthMenu
													sDom			:	'lrtip',
													oLanguage 		:	{sLengthMenu: "_MENU_",},
													ajax    		:	'{!! route('superadmin.team.intdata') !!}?userId=' + userId, 

													"fnDrawCallback": 	function( settings, start, end, max, total, pre ) {
																			var total_record	=	this.fnSettings().fnRecordsTotal();
																			$('#total_record').html(total_record);					
																			$("body").tooltip({
																			selector: '[data-toggle="tooltip"]'
																			});
																		},
        
      
													"columnDefs"	: 	[
																			{"width": "50px", "targets": 0 },
																			{"width": "150px", "targets": 1, "class":"align-items-center" }, 
																			{"width": "50px", "targets": 2 },
																			{ "width": "50px", "targets": 3 },
																			{ "width": "50px", "targets": 4 },
																			{ "width": "50px", "targets": 5 },            
																			{ "width": "50px", "targets": 6 },
																			{ "width": "50px", "targets": 7 },
																			{ "width": "50px", "targets": 8 },
																			{ "width": "50px", "targets": 9 }					
																			],     

														columns		: 	  [
																				{ data: 'chkbox', name:'chkbox',width: '1%', searchable : false, orderable: false},  
																				{ data: 'Name', name: 'Name',width: '15%', searchable : false, orderable: false},
																				{ data: 'Requisitions', name: 'Requisitions',width: '10%', searchable : false, orderable: false},
																				{ data: 'InterviewMode', name: 'InterviewMode',width: '10%', searchable : false, orderable: false},
																				{ data: 'Interviewers', name: 'Interviewers',width: '10%', searchable : false, orderable: false},
																				{ data: 'Source', name: 'Source',width: '10%', searchable : false, orderable: false},
																				{ data: 'InterviewDate', name: 'InterviewDate',width: '10%', searchable : false, orderable: false},
																				{ data: 'Feedback', name: 'Feedback',width: '10%', searchable : false, orderable: false},
																				{ data: 'Rating', name: 'Rating',width: '10%', searchable : false, orderable: false},		  
																				{ data: 'Stage', name: 'Stage',width: '10%', searchable : false, orderable: false}
																			],
               
                            });
                            new $.fn.dataTable.FixedHeader(inttable);

								
							}, 1500);
	});		 
	
	function handleClick(currentValue, currentId) {
		
			var chkStatus	=	currentValue.checked;
			var userId		=	currentId;   	
			var url 				= "{{ route('superadmin.team.statusUpdate') }}";
			var token 				= "{{ csrf_token() }}";							
			var baseurl    			= "{{ asset('assets/images/Loading2.gif') }}"; 
			$.ajax({
							type		:	'POST',
							url			:	url,
							beforeSend	:	function(){ },
							data		:	{'_token': token, 'chkStatus':chkStatus, 'userId': userId },
							success		:	function (response){
																	if (response.status == "success"){                                                                    
																		$('#'+block).remove();																											
																	}
											}
				});
	}

	table.on('click', '.update-user-role', function (){

		$('.update_role').trigger('click');			

		var id    			= $(this).data('row-id');
		var url   			= "{{ route('superadmin.team.editrole',':id') }}";
		url     			= url.replace(':id', id);
		var baseurl    		= "{{ asset('assets/images/Loading2.gif') }}"; 
		// alert(url);
		$.ajax({
						type  		: 'GET',
						url   		: url,
						beforeSend	: function() {
													$('#update_role_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
						},
						success : function (response) 
						{
				$("#update_role").attr("style","display: block;");
								$('#update_role_body').html(response.view);

								/*setTimeout(
								()=>
								{
										if (response.status == "success")
										{
											$('#kt_activitiesClient_body').html(response.view);
										}
								}, 500);*/
						}
				});	

	}); 

	
	// table.on('click', '.update-user-role', function () { 
	// 				$('#status_stage').trigger('click');  
					
	// 				$('#status_body_content').html('');
	// 				var id        	=	$(this).data('row-id');	
	// 				var url       	=	"{{ route('superadmin.jobs.editJobStatus',':id') }}";
	// 				url           	=	url.replace(':id', id);
	// 				$.ajax({
	// 							type  	: 'GET',
	// 							url   	: url,
	// 							success : function (response) {
	// 								if (response.status == "success") 
	// 								{
	// 									$('#status_body_content').html(response.view);       
	// 								}
	// 							}
	// 				});			
	// });
	
	$('body').on('click', '.add_team_member', function() {	
        var id    					=	123;
        var url   					=	"{{ route('superadmin.company.add_team_member_form',':id') }}";
        url     					=	url.replace(':id', id);
        var token 					=	"{{ csrf_token() }}";								
        var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
        $.ajax({
						type		:	'GET',
						url			:	url,
						beforeSend	: 	function() { $('#load_add_team_invitation_content').html('<img src="'+baseurl+'" height="60%" width="100%">'); },
						success		: 	function (response){
															if (response.status == "success"){
																$('#load_add_team_invitation_content').html(response.view);	 	
															}						
										}
			});		    
	});
		
</script>

<script> 

function WorkingStatus(currentValue, currentId) 
{
				var chkStatus			=	currentValue.checked;
				var userId				=	currentId;   	
				var url 				=	"{{ route('superadmin.team.WorkingStatus') }}";
				var token 				=	"{{ csrf_token() }}";							
				var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
				
				const swalWithBootstrapButtons = Swal.mixin({
																customClass		: { confirmButton	: 'btn btn-success', cancelButton	: 'btn btn-danger' },
																buttonsStyling	: false
													})
													swalWithBootstrapButtons.fire({
																					  title				: 'Are you sure?',
																					  text				: "You want to update Working Status.!",
																					  icon				: 'warning',
																					  showCancelButton	: true,
																					  confirmButtonText	: 'Yes, Ignore it!',
																					  cancelButtonText	: 'No, cancel!',
																					  reverseButtons	: true
																				}).then((result) => {
													  if (result.isConfirmed) {
																$.ajax({
																			type		: 'POST',
																			url			: url,
																			beforeSend	: function(){ },
																			data		: {'_token': token, 'chkStatus':chkStatus, 'userId': userId },
																			success		: function (response){																																						
																								if (response.status == "success"){  															  
																										swalWithBootstrapButtons.fire ( 'Success!', 'Working status update successfully.', 'success' )
																								} else if (response.status == "info"){   
																										if(chkStatus == true){
																											$('#working_status_'+userId).prop('checked', false); 
																										}else{
																											$('#working_status_'+userId).prop('checked', true); 	
																										}																											     																									swalWithBootstrapButtons.fire ( 'Fail!', response.message, 'error')
																							    }
																			}
																});
														} else if (result.dismiss === Swal.DismissReason.cancel) {
																if(chkStatus == true){
																		$('#working_status_'+userId).prop('checked', false); 
																}else{
																		$('#working_status_'+userId).prop('checked', true); 	
																}															
																swalWithBootstrapButtons.fire( 'Cancelled', 'Working status can not update:)', 'error' )
														}
													
													})
													   
													
}												
</script>

@endpush

