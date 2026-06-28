@extends('layouts.product_company_layout')
@push('head-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<style>

</style>
@endpush
@section('content')
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
@php 
        
        $jobtitle				=	@$job->title;
        $bgTextDecore			=	$strVal	=	'';
        $str					=	trim(@$jobtitle);
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
        
		$statusName				=	ucwords(substr(@$job->status, 0, 10)); 	
		$statusClass			=	'joined-bg';
		$final_status			=	$job->final_status;
		if($final_status == 2){
			 $statusName		=	"REMOVED";	
			 $statusClass		=	'closed-bg';
		}
		
		$req_path				=	url('/product_company/apptracker/'.@$job->id.'/all');
		$public_bench_path		=	url('/product_company/apptracker/public_bench/'.@$job->id);
		$internal_bench_path	=	url('/product_company/apptracker/internal_bench/'.@$job->id);
		$existing_path			=	url('/product_company/apptracker/similar_candidates/'.@$job->id);

@endphp 
     
      <td class="d-flex align-items-center border-0">
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="{{ @$req_path}}">
          <div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
          </a> </div>
        <div class="d-flex flex-column"> 
			<a href="{{ @$req_path}}" class="text-dark fw-bolder text-hover-primary fs-6">{{ strtoupper(substr(@$jobtitle, 0, 2)) }}</a> 
			<a href="{{ @$req_path}}" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">{{ucwords(substr(@$jobclient->name, 0, 10))}}</a> 
		</div>
      </td>
      <td>
	  	<div class="d-flex flex-column ml-2"> 
			<a href="{{ @$req_path}}" class="text-dark fw-bolder text-hover-primary fs-6"><span class="badge fw-bolder fs-6 {{ @$statusClass }} px-2 py-1">{{ $statusName }}</span> 
			<span class="text-muted fw-bold text-muted d-block fs-7">R-{{ @$job->id }}</span></a>
		</div>
	  </td>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ @$public_bench_path }}" class="text-dark text-hover-primary mb-1">Public Bench</a> </h5>
        <span class="text-gray-600">{{ @$public_bench_counter }}</span> 
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ @$internal_bench_path }}" class="text-dark text-hover-primary mb-1">Internal Bench</a> </h5>
        <span class="text-gray-600">{{ @$internal_bench_counter }}</span> 
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <div class="d-flex flex-column tabactive">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ @$existing_path }}" class="text-primary mb-1">Existing</a> </h5>
        <span class="text-gray-600">{{ @$existing_counter }}</span> 
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ route('product_company.apptracker.candidates_request') }}" class="text-dark text-hover-primary mb-1">Candidates Request</a> </h5>
        <span class="text-gray-600">{{ @$recomnded_counter }}</span> 
      </div>
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
				Filter 
			</button>
            
            <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none" data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
				<span class="svg-icon svg-icon-2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
					<path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black" />
					<path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
					</svg>
				</span>
				Export
			</button>
			
            @if(in_array("add_job_applications", $userPermissions))
            <a class="d-none" href="{!! url('product_company/job-applications/create/'.@$job->id.'/0') !!}">
				<button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" id="AddCandidate">
					<span class="svg-icon svg-icon-primary svg-icon-1x">
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
						<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<polygon points="0 0 24 0 24 24 0 24"/>
						<path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
						<path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
						</g>
						</svg>
					</span> 
				Add Candidate 
				</button>
            </a>
            @endif </div>
        </div>
      </div>
      <!--end::Actions-->
    </form>
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
@php 
        $sourcing_bg = $screening_bg = $interview_bg= $negotation_bg = $offer_bg = $joining_bg = $rejected_bg  = $All_candidate_bg = 'alert bg-gray text-center circle_boxes';   
        $sourcing_text = $screening_text = $interview_text = $negotation_text = $offer_text = $joining_text = $rejected_text = $All_candidate_text = 'text-center color-all'; 
        
        if(!empty(@$candStatusClass['Index'])){
		
				if(@$candStatusClass['Index']== 'Sourcing'){
					  $sourcing_bg    =   'alert text-center circle_boxes '.@$candStatusClass['bg_class']; 
					  $sourcing_text    =   'text-center  '.@$candStatusClass['nav_text'];    
				}else if(@$candStatusClass['Index']== 'Screening'){
					  $screening_bg     =   'alert text-center circle_boxes '.@$candStatusClass['bg_class']; 
					  $screening_text   =   'text-center  '.@$candStatusClass['nav_text'];    
				}else if(@$candStatusClass['Index']== 'Interview'){
					  $interview_bg     =   'alert text-center circle_boxes '.@$candStatusClass['bg_class']; 
					  $interview_text   =   'text-center  '.@$candStatusClass['nav_text'];    
				}else if(@$candStatusClass['Index']== 'Negotiation'){
					  $negotation_bg    =   'alert text-center circle_boxes '.@$candStatusClass['bg_class']; 
					  $negotation_text  =   'text-center  '.@$candStatusClass['nav_text'];    
				}else if(@$candStatusClass['Index']== 'Offered'){
					  $offer_bg       =   'alert text-center circle_boxes '.@$candStatusClass['bg_class']; 
					  $offer_text     =   'text-center  '.@$candStatusClass['nav_text'];    
				}else if(@$candStatusClass['Index']== 'Joined'){
					  $joining_bg     =   'alert text-center circle_boxes '.@$candStatusClass['bg_class']; 
					  $joining_text     =   'text-center  '.@$candStatusClass['nav_text'];    
				}else if(@$candStatusClass['Index']== 'Rejected'){
					  $rejected_bg    =   'alert text-center circle_boxes '.@$candStatusClass['bg_class']; 
					  $rejected_text    =   'text-center  '.@$candStatusClass['nav_text'];    
				}else{
					  $All_candidate_bg     =   'alert text-center circle_boxes '.@$candStatusClass['bg_class']; 
					  $All_candidate_text   =   'text-center  '.@$candStatusClass['nav_text'];    
				}    
        }  
        
        $tr_class  = 'bg-gray';
        if(!empty($mstatus_id)){
			if($mstatus_id == 'all'){
			  	$tr_class = 'bg-total-active';
			}else{
			  	$tr_class = 'bg-gray';
			}
        }         

@endphp 

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="card">
        <div class="card-body pt-3 px-5">
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3"><input class="form-check-input chk_list" type="checkbox" value="1" /></div></th>
                <th class="">Name</th>
                <th class="">Contacts</th>
                <th class="">Exp.</th>
                <th class="">Salary</th>
                <th class="">NP</th>
                <th class="">Qualification</th>
                <th class="">Location</th>
                <th class="">Source</th>
                <th class="">Date</th>
                <th class="">Requisitions</th>
                <th class="">Stage</th>
                <th class="">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->

@endsection
@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script>


   			var table;
            tableLoad('load');		   
   
			function tableLoad(type) 
			{
				
           
					var jobid       	=	{{ @$job->id }};
					var inputSearch 	=	$('#inputSearch').val();
        
					table 				=	$('#myTable').dataTable({
  
												responsive     	:	true, 
												processing      :	true,
												ordering		:	false, //disable ordering      			
												destroy       	:	true,   
												stateSave     	:	true,      
												"bLengthChange" :	false,   
												sDom			:	'lrtip',         		 
												ajax			:	'{!! route('product_company.apptracker.similar_existing_data') !!}?jobid='+jobid+'&search='+inputSearch,
											   "fnDrawCallback" :	function (oSettings) { },
												"drawCallback"	:	function(settings) {
																		alert("call"+settings.json);  
																	}, 
											  "initComplete"	:	function( settings, json){ 
																	  
																								$('.clientStatusLoader').html('');
																								$('.jobLoader').html('');
																								$('.sourceLoader').html('');
																								$('.search_np_idLoader').html('');
																								$('.search_passportLoader').html('');
																								$('.statusLoader').html(''); 
																	  }, 
  		  
											   columns			: [             
																		{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
																		{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '12%'},						
																		{data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '10%'},						
																		{data: 'Exp', name: 'Exp', searchable : false, orderable : false,  width: '5%'},
																		{data: 'Salary', name: 'Salary',  searchable : false, orderable : false,  width: '5%'},   
												
																		{data: 'NP', name: 'NP', searchable : false, orderable : false},
																		{data: 'Qualification', name: 'Qualification',  searchable : false, orderable : false},						
																		{data: 'Location', name: 'Location', orderable: false},
																		{data: 'Source', name: 'Source', orderable : false},   
																		{data: 'Date', name: 'Date', orderable: false},
																		
																		{data: 'Requisitions', name: 'Requisitions', orderable: false},
																		{data: 'Stage', name: 'Stage', orderable: false},
																		{data: 'Action', name: 'Action', orderable: false, width: '10%'} 
																]         
      
             }); 
           new $.fn.dataTable.FixedHeader(table);
		   
			} 
   
   
</script>
<script>
   
   $( document ).ready(function() {
      
         var current_nav_active  = $('#mstatus_id').val(); 

      	if(current_nav_active == 'all'){
				$('.nav_status').html('All Candidate'); 
				$('.nav_status').css('background', 'linear-gradient(to left, #01e074, #0581d9)');
				$('.nav_status').css('color','#fff'); 
		  } 
      
      	$('.application_sttus_nav').bind('click',function(){
          
        $(".bg").removeClass('bg-Asourcing').addClass('bg-gray');
        $(".bg").removeClass('bg-Ascreening').addClass('bg-gray');
        $(".bg").removeClass('bg-Ainterview').addClass('bg-gray');
        $(".bg").removeClass('bg-Anegotation').addClass('bg-gray');
        $(".bg").removeClass('bg-Aoffer').addClass('bg-gray');
        $(".bg").removeClass('bg-Ajoiner').addClass('bg-gray');
        $(".bg").removeClass('bg-Arejected').addClass('bg-gray');
        
        $("#tx_1").removeClass('text-Asourcing').addClass('color-all');
        $("#tx_2").removeClass('text-Ascreening').addClass('color-all');
        $("#tx_3").removeClass('text-Ainterview').addClass('color-all');
        $("#tx_4").removeClass('text-Anegotation').addClass('color-all');
        $("#tx_5").removeClass('text-Aoffer').addClass('color-all');
        $("#tx_6").removeClass('text-Ajoiner').addClass('color-all');
        $("#tx_7").removeClass('text-Arejected').addClass('color-all');
        
        $('#bg_all').removeClass('bg-total-active').addClass('bg-gray');
        
        
        var page  = $(this).attr('rel');
        var jobid = $('#jobid').val();
         
         if(page == 1){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Asourcing'); 
            $('#tx_'+page).removeClass('color-all').addClass('text-Asourcing');
            $('.nav_status').html('Sourcing');  
            $('.nav_status').css('background','#efefef');
            $('.nav_status').css('color','#000000');
         }else if(page==2){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Ascreening');  
            $('#tx_'+page).removeClass('color-all').addClass('text-Ascreening');  
            $('.nav_status').html('Screening'); 
            $('.nav_status').css('background','#fff9ed');
            $('.nav_status').css('color','#ffab00');
         }else if(page==3){  
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Ainterview');  
            $('#tx_'+page).removeClass('color-all').addClass('text-Ainterview');  
            $('.nav_status').html('Interview'); 
            $('.nav_status').css('background','#f0f8ff');
            $('.nav_status').css('color','#2896ff');
         }else if(page==4){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Anegotation'); 
            $('#tx_'+page).removeClass('color-all').addClass('text-Anegotation');
            $('.nav_status').html('Negotiation');
            $('.nav_status').css('background','#ffe6f2');
            $('.nav_status').css('color','#ff4098');    
         }else if(page==5){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Aoffer');
            $('#tx_'+page).removeClass('color-all').addClass('text-Aoffer');  
            $('.nav_status').html('Offered'); 
            $('.nav_status').css('background','#d4ffde');
            $('.nav_status').css('color','#06c732');  
         }else if(page==6){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Ajoiner');
            $('#tx_'+page).removeClass('color-all').addClass('text-Ajoiner'); 
            $('.nav_status').html('Joined');  
            $('.nav_status').css('background','#f7deff');
            $('.nav_status').css('color','#c000ff');
         }else if(page==7){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Arejected');
            $('#tx_'+page).removeClass('color-all').addClass('text-Arejected'); 
            $('.nav_status').html('Rejected');  
            $('.nav_status').css('background','#ffe2e5');
            $('.nav_status').css('color','#ff0202');
         }else if(page=='all'){
            $('.nav_status').html('All Candidate'); 
            $('.nav_status').css('background','linear-gradient(to left, #01e074, #0581d9)');
            $('.nav_status').css('color','#FFFFFF');
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-total-active');   
         }
        $('#mstatus_id').val(page);
        var url   	=	{!! url('/product_company/apptracker/') !!};
        url     	=	url+'/'+jobid+'/'+page;
        window.history.pushState('page2', 'Title', url);
        tableLoad('load');
     })
});
</script>
<script>

 $('body').on('click', '.add_to_inreview', function(){
 
               var id 						=	$(this).data('row-id');
			   var source_from_type 		=	$(this).data('row-source_from_type');
			   var source_jobid 			=	$(this).data('row-jobid');
			   
			    var url 					=	'{{ route('product_company.apptracker.rec_candidate_assign') }}';
              	var token 					= 	"{{ csrf_token() }}";
				var source_tab 				=	$(this).data('row-from');				
																
		const swalWithBootstrapButtons = Swal.mixin({
														   customClass		: {
																					confirmButton: 'btn btn-success',
																					cancelButton: 'btn btn-danger'
																		  		},
		  													buttonsStyling	: false
		})
		swalWithBootstrapButtons.fire({
										  title				: 'Are you sure?',
										  text				: "You won't be able to revert this!",
										  icon				: 'warning',
										  showCancelButton	: true,
										  confirmButtonText	: 'Yes, Add to In-Review!',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true
									}).then((result) => {
		  if (result.isConfirmed) {
		  
		  
		  
		  								
										$.ajax({
													type	: 'GET',
													url		: url,
													data	: {'id':id, 'source_from_type': source_from_type, 'source_jobid': source_jobid, 'source_tab': source_tab},
													success	: function (response) {
																						//alert(response); 
																						if(response.status == 'success') {
																				
																								//	var clientAll		=	response.clientDataCnt.clientAll;
																								//	var clientDirect	=	response.clientDataCnt.clientDirect;
																								//	var clientCustom	=	response.clientDataCnt.clientCustom;
																								//	var clientRequest	=	response.clientDataCnt.clientRequest;
																									
																								//	$('#span_clientAll').html(clientAll);
																								//	$('#span_clientDirect').html(clientDirect);
																								//	$('#span_clientCustom').html(clientCustom);
																								//	$('#span_clientRequest').html(clientRequest);
																									tableLoad('load');
																									//table._fnDraw(); 
																									swalWithBootstrapButtons.fire(
																																	  'In-Review!',
																																	  'Selected request has been In-Review.',
																																	  'success'
																																	)
																						}
													}
										});
										
										
			
		  } else if (
			result.dismiss === Swal.DismissReason.cancel
		  ) {
			swalWithBootstrapButtons.fire(
			  'Cancelled',
			  'Selected request is safe',
			  'error'
			)
		  }
		})
														

           });
		   
	
</script>

@endpush 