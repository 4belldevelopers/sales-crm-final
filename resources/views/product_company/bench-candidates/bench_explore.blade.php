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
@media only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesJob {width: 1025px;}}
@media only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesJob {width: 1200px;}}
@media only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesClientjob_toggle {width: 1025px;}}
@media only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesClientjob_toggle {width: 1200px;}}
</style>
@endpush

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

@section('content')

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">

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
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <a href="{{ asset('product_company/bench-candidates/recommend_req/') }}/{{ ucwords(@$bench_detail->id) }}" class="text-gray-800 text-hover-primary mb-1">Recommend Requisition</a>
        </h5>
      <span class="text-gray-600">&nbsp;</span>
    </div>
    </div>
    <form action="" id="filter-form" style="">
      @csrf
			<input type="hidden" name="bench_id" id="bench_id" value="{{ @$bench_id }}" />
			<input type="hidden" name="stage" id="stage" value="{{ @$stage }}" />			 
      <div class="d-flex align-items-center py-1">        
        <div class="d-flex align-items-center position-relative my-1 me-3">
          <span class="svg-icon svg-icon-1 position-absolute ms-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
              <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
            </svg>
          </span>
          <input type="text" id="inputSearch" name="inputSearch" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
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
      <div class="d-flex justify-content-space-around align-items-center custm-bg">
        <a class="d-block text-white f-17 font-weight-bolder application_sttus_nav application_sttus_nav" href="Javascript:void(0);" rel="all">
          <div class="total_jobs mr-2">
              <div class="bg-total-active text-center squre_box" role="alert" id="bg_all">
                <span class="total-content-view">
                    <i class="flaticon2-group text-white fa-sm" aria-hidden="true"></i> <span id="totalcnt"> 0 </span> 
                </span>
              </div>
              <h6 class="text-center color-all font-weight-600">&nbsp;Total</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20 application_sttus_nav" href="Javascript:void(0);" rel="1">
          <div class="sourcing mr-2">
              <div class="bg text-center circle_boxes SourcingCounter {{ $sourcing_bg }}" role="alert" id="bg_1"> 0 </div>
              <h6 class="tx text-center color-all font-weight-600 {{ $sourcing_text }}" id="tx_1">Sourcing</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="2">
          <div class="screening mr-2">
              <div class="bg text-center circle_boxes {{ $screening_bg }}" role="alert" id="bg_2"> 0 </div>
              <h6 class="tx text-center color-all font-weight-600 {{ $screening_text }}" id="tx_2">Screening</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="3">
          <div class="negitation mr-2">
              <div class="bg text-center circle_boxes {{ $interview_bg }}" role="alert" id="bg_3"> 0 </div>
              <h6 class="tx text-center color-all font-weight-600 {{ $interview_text }}" id="tx_3">Evaluation</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="4">
          <div class="negitation mr-2">
              <div class="bg text-center circle_boxes {{ $negotation_bg }}" role="alert" id="bg_4"> 0 </div>
              <h6 class="tx text-center color-all font-weight-600 {{ $negotation_text }}" id="tx_4">Negotation</h6>
          </div>
        </a>
        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="5">
            <div class="offer mr-2">
                <div class="bg text-center circle_boxes {{ $offer_bg }}" role="alert" id="bg_5"> 0 </div>
                <h6 class="tx text-center color-all font-weight-600 {{ $offer_text }}" id="tx_5">Offer</h6>
            </div>
          </a>
          <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="6">
            <div class="joining mr-2">
                <div class="bg text-center circle_boxes {{ $joining_bg }}" role="alert" id="bg_6"> 0 </div>
                <h6 class="tx text-center color-all font-weight-600 {{ $joining_text }}" id="tx_6">Joined</h6>
            </div>
          </a>
        <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="7">
            <div class="rejected mr-2">
                <div class="bg alert text-center circle_boxes   {{ $rejected_bg }}" role="alert" id="bg_7"> 0 </div>
                <h6 class="tx text-center   font-weight-600 {{ $rejected_text }}" id="tx_7">Rejected</h6>
            </div>
          </a>
      </div>
      <div class="card">
        <div class="card-body pt-3 px-5">
		  <table id="bench_explore_listing" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="fw-bolder text-muted">
                  <th class="w-10px pe-0">  <div class="form-check form-check-sm form-check-custom form-check-solid me-3"> <input class="form-check-input" type="checkbox" value="1"> </div> </th>
                  <th class="d-flex align-items-center border-0">Requisition</th>
                  <th class="" data-bs-toggle="tooltip" title="Experience">Exp.</th>
                  <th class="">Salary</th>
                  <th class="">Location</th>
                  <th class="">Status</th>
                  <th class="">Added Date</th>
                  <th class="">Contact Person</th>
                  <th class="">Interview</th>
                  <th class="">Stage</th>
                </tr>
            </thead>
          </table>		  
        </div>
      </div>
    </div>
  </div>
</div>
<!--end::Content-->

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


@endsection
@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>

<script>
   
   $( document ).ready(function() {
   	
        var current_nav_active  = $('#stage').val(); 
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
        
        var page  		= $(this).attr('rel');
        var bench_id 	= $('#bench_id').val();
         
         if(page == 1)
         {
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Asourcing'); 
            $('#tx_'+page).removeClass('color-all').addClass('text-Asourcing');
            $('.nav_status').html('Sourcing');  
            $('.nav_status').css('background','#efefef');
            $('.nav_status').css('color','#000000');
    
         }
         else if(page==2)
         {
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Ascreening');  
            $('#tx_'+page).removeClass('color-all').addClass('text-Ascreening');  
            $('.nav_status').html('Screening'); 
            $('.nav_status').css('background','#fff9ed');
            $('.nav_status').css('color','#ffab00');
         }
         else if(page==3)
         {  
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Ainterview');  
            $('#tx_'+page).removeClass('color-all').addClass('text-Ainterview');  
            $('.nav_status').html('Interview'); 
            $('.nav_status').css('background','#f0f8ff');
            $('.nav_status').css('color','#2896ff');
         }
         else if(page==4)
         {
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Anegotation'); 
            $('#tx_'+page).removeClass('color-all').addClass('text-Anegotation');
            $('.nav_status').html('Negotiation');
            $('.nav_status').css('background','#ffe6f2');
            $('.nav_status').css('color','#ff4098');    
         }
         else if(page==5)
         {
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Aoffer');
            $('#tx_'+page).removeClass('color-all').addClass('text-Aoffer');  
            $('.nav_status').html('Offered'); 
            $('.nav_status').css('background','#d4ffde');
            $('.nav_status').css('color','#06c732');  
         }
         else if(page==6)
         {
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Ajoiner');
            $('#tx_'+page).removeClass('color-all').addClass('text-Ajoiner'); 
            $('.nav_status').html('Joined');  
            $('.nav_status').css('background','#f7deff');
            $('.nav_status').css('color','#c000ff');
         }
        else if(page==7)
         {
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Arejected');
            $('#tx_'+page).removeClass('color-all').addClass('text-Arejected'); 
            $('.nav_status').html('Rejected');  
            $('.nav_status').css('background','#ffe2e5');
            $('.nav_status').css('color','#ff0202');
         }
         else if(page=='all')
         {
            $('.nav_status').html('All Candidate'); 
            $('.nav_status').css('background','linear-gradient(to left, #01e074, #0581d9)');
            $('.nav_status').css('color','#FFFFFF');
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-total-active');   
         }
        
        
        $('#stage').val(page);
        
        var url   		=	'<?php echo url('/product_company/bench-candidates/bench_explore/') ?>';
        url     		=	url+'/'+bench_id+'/'+page;
        window.history.pushState('page2', 'Title', url);
        tableLoad('load');
		
		
		 
     })
	
	});
	
</script>

<script>

   $(document).ready(function() { 
									  $('#inputSearch').keyup(function() {        
										  	tableLoad('load');   
									  });
   });
    
var table;
tableLoad('load');			
function tableLoad(type)
	{
				
           
				var bench_id       		=	$('#bench_id').val();
				var stage  				=	$('#stage').val();
				var inputSearch 		=	$('#inputSearch').val();

				table 					=	$('#bench_explore_listing').dataTable({

							responsive     	: true, 
							processing      : true,
							ordering		: false, //disable ordering      			
							destroy       	: true,   
							stateSave     	: true,      
							"bLengthChange" : false,   
							sDom			: 'lrtip',         
							ajax			: '{!! route('product_company.bench-candidates.bench_explore_listing') !!}?bench_id='+bench_id+'&stage='+stage+'&search='+inputSearch,
						   "fnDrawCallback" : function (oSettings) {
																		$("body").tooltip({selector: '[data-toggle="tooltip"]' });
																																										
var url       		= '{{ route('product_company.bench-candidates.bench_explore_listing_counter', [':bench_id', ':stage', ':inputSearch']) }}';
url         		= url.replace(':bench_id', bench_id);
url         		= url.replace(':stage', stage);
url         		= url.replace(':inputSearch', inputSearch);
$.ajax({
			type  	: 'GET',
			url   	: url,
			success : function (response) {

													var allCandidate	=	0;
													var Sourcing		=	0;
													var Screening		=	0;
													var Rejected		=	0;
													var Offered			=	0;
													var Negotiation		=	0;
													var Joined			=	0;
													var Interview		=	0;
													if(response.allCounter > 0){
																					allCandidate	=	response.data.allCandidate;
																					Sourcing		=	response.data.Sourcing;
																					Screening		=	response.data.Screening;
																					Rejected		=	response.data.Rejected;
																					Offered			=	response.data.Offered;
																					
																					Negotiation		=	response.data.Negotiation;
																					Joined			=	response.data.Joined;
																					Interview		=	response.data.Interview;
													}
													setTimeout(()=>{
																		$('#bg_1').html(Sourcing);
																		$('#bg_2').html(Screening);
																		$('#bg_3').html(Interview);
																		$('#bg_4').html(Negotiation);
																		$('#bg_5').html(Offered);
																		
																		$('#bg_6').html(Joined);
																		$('#bg_7').html(Rejected);
																		$('#totalcnt').html(allCandidate);
													
													}, 100);
									}
});			   
			   				  
									},
											
												"drawCallback": function(settings) {															
								}, 
						
								"initComplete"		:	function( settings, json){ 			
																	$('.clientStatusLoader').html('');
																	$('.jobLoader').html('');
																	$('.sourceLoader').html('');
																	$('.search_np_idLoader').html('');
																	$('.search_passportLoader').html('');
																	$('.statusLoader').html(''); 
														}, 
		  						"columnDefs"		:
														  [ 
																	{ "width": "50px", "targets": 0, "width": "4%", "class":""},
																	{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
																	{  "width": "50px"  , "targets": 2},		
																	{  "width": "100px", "targets": 3 },		
																	{  "width": "200px", "targets": 4 }, 
																	
																	{  "width": "50px" ,"targets": 5 },
																	{  "width": "200px" , "targets": 6 },              
																	{  "width": "150px" , "targets": 7 },	
																	{  "width": "150px", "targets": 8 },	
																	{ "width": "20px", "targets": 9 } 	
														  ], 
								   columns			: [             
															{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
															{data: 'Requisition', name: 'Requisition',  orderable: false, searchable: false, width: '15%'},						
															{data: 'Exp', name: 'Exp', searchable : false, orderable : false, width: '15%'},						
															{data: 'Salary', name: 'Salary', searchable : false, orderable : false,  width: '5%'},
															{data: 'Location', name: 'Location',  searchable : false, orderable : false,  width: '10%'},   
															
															{data: 'Status', name: 'Status', searchable : false, orderable : false},
															{data: 'AddedDate', name: 'AddedDate',  searchable : false, orderable : false},
															{data: 'ContactPerson', name: 'ContactPerson', orderable: false},
															{data: 'Interview', name: 'Interview', orderable : false},   
															{data: 'Stage', name: 'Stage', orderable: false}
													  ]         
             }); 
           new $.fn.dataTable.FixedHeader(table); 	   
	} 
	
</script>

<script>

 table.on('click', '.show-detail', function ()
   	{
   		$('#kt_activitiesJob_toggle').trigger('click');				
   		var id    					=	$(this).data('row-id');
		var job_applications_id		=	$(this).data('row-job_applications_id');
   		var url   					=	"{{ route('product_company.apptracker.benchReqViewMore', ['id' => ':id', 'job_applications_id' => ':job_applications_id' ]) }}";
   		url     					=	url.replace(':id', id);
		url     					=	url.replace(':job_applications_id', job_applications_id);
   		var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 
					
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
													 $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
									   },
   						success 	: function (response) {
   								setTimeout(
   								()=>{
   										if (response.status == "success"){
   											$('#kt_activitiesJob_body').html(response.view);
   										}
   								}, 500);     		  					
   						}
   	  			});	
	}); 
	
</script>

@endpush