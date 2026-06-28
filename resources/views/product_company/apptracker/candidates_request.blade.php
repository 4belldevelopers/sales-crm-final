@extends('layouts.product_company_layout') 
@push('head-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
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

        $req_path				=	url('/product_company/apptracker/'.@$job->id.'/all');
		$public_bench_path		=	url('/product_company/apptracker/public_bench/'.@$job->id);
		$internal_bench_path	=	url('/product_company/apptracker/internal_bench/'.@$job->id);
		$existing_path			=	url('/product_company/apptracker/similar_candidates/'.@$job->id);
        
@endphp 
      <td class="d-flex align-items-center border-0">
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> 
			<a href="{{$req_path}}">
          		<div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
            </a> 
		</div>
        <div class="d-flex flex-column"> 
			<a href="{{$req_path}}" class="text-dark fw-bolder text-hover-primary fs-6">{{ strtoupper(substr(@$jobtitle, 0, 2)) }}</a> 
			<a href="{{$req_path}}" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">{{ ucwords(substr(@$jobclient->name, 0, 10)) }}</a> 
		</div>
      </td>

      <td><a href="{{ @$req_path}}" class="text-dark fw-bolder text-hover-primary fs-6">
	  	<div class="d-flex flex-column ml-2"> 
			<span class="badge fw-bolder fs-6 joined-bg px-2 py-1">{{ ucwords(substr(@$job->status, 0, 10)) }}</span> 
			<span class="text-muted fw-bold text-muted d-block fs-7">R-{{ @$job->id }}</span> </a>
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
	  <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ @$existing_path }}" class="text-dark text-primary mb-1">Existing</a> </h5>
        <span class="text-gray-600">{{ @$existing_counter }}</span> 
	  </div>  
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <div class="d-flex flex-column tabactive">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <a href="javascript:void(0);" class="text-primary mb-1">Candidates Request</a>
        </h5>
        <span class="text-gray-600">{{ @$recomnded_counter }}</span>  </div>
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
            </span> Add Candidate</button>
            </a>
            @endif </div>
        </div>
      </div>
    </form>
  </div>
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
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="candidates_request">
              <thead>
                  <tr class="fw-bolder text-muted" role="row">
                    <th class="w-10px pe-0 sorting_disabled" rowspan="1" colspan="1" style="width: 30px;">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#candidates_request .form-check-input" value="1">
                        </div>
                    </th>
                    <th class="">Name</th>
                    <th class="">Exp.</th>
                    <th class="">Salary</th>
                    <th class="">Notice Period</th>
                    <th class="">Qualifaction</th>
                    <th class="">Location</th>
                    <th class="">Type</th>
                    <th class="">Offer by</th>
                    <th class="">Last Active</th>
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
</div>
<!--end::Content--> 
@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{TM_VERSION}}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>	
<script>

$( document ).ready(function() {



  $(document).on("click",".add_to_list", function(){
      var jobId  = $(this).attr('data-row-id');
      var recomndedId  = $(this).attr('data-recomnded-id');
      var condidateId  = $(this).attr('data-condidate-id');

      // var url   		=	"{{ route('service_company.jobs.job_request',':id') }}";
      //       url     		=	url.replace(':id', jobId);
      // alert(url)

      var elm = $(this);
      swal({
          title: "Are you sure?",
          text: "Add to list!",
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
            var url   		=	"{{ route('product_company.jobs.add_to_list',':id') }}";
            url     		=	url.replace(':id', jobId)+"?recomndedId="+recomndedId+"&condidateId="+condidateId;
            var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		      $.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                elm.parent().html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
                swal("Recommend!", "Add successfully public bench!", "success");
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
	 
	 var table;   
     tableLoad('load');
	 	function tableLoad(type) 
	 		{
           
				var jobid       = $('#jobid').val();
				var mstatus_id  = $('#mstatus_id').val();
				var inputSearch = $('#inputSearch').val();	
				table 			= $('#candidates_request').dataTable({
  
												responsive     	: true, 
												processing      : true,
												ordering		: false, //disable ordering      			
												destroy       	: true,   
												stateSave     	: true,      
												"bLengthChange" : false,   
												sDom			: 'lrtip',         		 
												ajax			: '{!! route('product_company.apptracker.candidates_request_data') !!}?jobid='+jobid+'&mstatus_id='+mstatus_id+'&search='+inputSearch,
											   "fnDrawCallback" : function (oSettings) {
																							$("body").tooltip({selector: '[data-toggle="tooltip"]' });
																								
																							
																						var url       		= '{{ route('service_company.apptracker.loadcounter', [':jobid', ':mstatus_id', ':inputSearch']) }}';
																						url         		= url.replace(':jobid', jobid);
																						url         		= url.replace(':mstatus_id', mstatus_id);
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
															alert("call"+settings.json);  
												}, 
						
                    
          "initComplete":function( settings, json){ 
          
            $('.clientStatusLoader').html('');
            $('.jobLoader').html('');
            $('.sourceLoader').html('');
            
            $('.search_np_idLoader').html('');
            $('.search_passportLoader').html('');
            $('.statusLoader').html(''); 
            
            
          }, 		  
           columns: [             
						{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
						{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},							
						{data: 'Exp', name: 'Exp', searchable : false, orderable : false,  width: '5%'},		
						{data: 'Salary', name: 'Salary',  searchable : false, orderable : false,  width: '10%'},   
						{data: 'NoticePeriod', name: 'NoticePeriod', searchable : false, orderable : false},	
						{data: 'Qualifaction', name: 'Qualifaction',  searchable : false, orderable : false},	
						{data: 'Location', name: 'Location', orderable : false},  
						{data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '15%'},	
						{data: 'Interview', name: 'Interview', orderable: false}, 
						{data: 'Source', name: 'Source', orderable: false},
						{data: 'Action', name: 'Action', orderable: false}
					]         
      
             }); 
           new $.fn.dataTable.FixedHeader(table);
		   } 
		   
	 	table.on('click', '.stage_sub_stage', function ()   
      		{

					$('#stage_body_content').html('');
					var id       	= $(this).data('row-id');			   
					var url    		= "{{ route('service_company.job-applications.stage_body_content',':id') }}";
					url      		= url.replace(':id', id);
					$.ajax({
								type  		: 'GET',
								url   		: url,
								success 	: function (response) {
																		if (response.status == "success") {
																			$('#stage_body_content').html('');
																			$('#stage_body_content').html(response.view);
																		} 
																  }
					});
                
           }); 		   
		   
	   $('body').on('click', '.master_status_div', function(e){
   
   		e.preventDefault(); 
   
   		
		
   		var id 			=	$(this).data('master-status-id');
   		$("#master_status_"+id).attr('checked', true);
   		$('#main_substage').show();
   		$('#comment_div').hide();
   		
		$("#inreview").attr('checked', false);
		$("#sourcing").attr('checked', false);
		$("#screening").attr('checked', false);
		$("#evaluation").attr('checked', false);
		$("#negotation").attr('checked', false);
		$("#offer").attr('checked', false);
		$("#joined").attr('checked', false);
		$("#rejected").attr('checked', false);
		
   		if(id == 1){
			$("#sourcing").attr('checked', true);
			$('lbl_master_status_'+id).addClass('sourcing-bg');	
   		}else if(id == 2){
			$("#screening").attr('checked', true);
   			$('lbl_master_status_'+id).addClass('screening-bg');	
   		}else if(id == 3){
			$("#evaluation").attr('checked', true);
   			$('lbl_master_status_'+id).addClass('interview');	
   		}else if(id == 4){
			$("#negotation").attr('checked', true);
   			$('lbl_master_status_'+id).addClass('btn-negotation');	
   		}else if(id == 5){
			$("#offer").attr('checked', true);
   			$('lbl_master_status_'+id).addClass('btn-offer');	
   		}else if(id == 6){
			$("#joined").attr('checked', true);
   			$('lbl_master_status_'+id).addClass('btn-joined');	
   		}else if(id == 7){
			$("#rejected").attr('checked', true);
   			$('lbl_master_status_'+id).addClass('btn-rejected');
   			$('#main_substage').hide();	
   			$('#comment_div').show();
   		}else if(id == 8){
			$("#inreview").attr('checked', true);
   			$('lbl_master_status_'+id).addClass('bg-light-warning text-warning'); 	
   			$('#main_substage').hide();   			
   		}	
   		
   		if(id > 0 && (id!=8) )
   		{
				
   			   var baseurl     	= "{{ asset('assets/images/Loading2.gif') }}"; 			   
                  var url    	= "{{ route('service_company.job-applications.child_stage',':id') }}";
                  url      		= url.replace(':id', id);
                  $.ajax({
                           	type  			: 'GET',
                           	url   			: url,
							contentType		: false,
							cache			: false,
							processData		: false,
   							beforeSend	: function() {
                                     					$('#substage').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       		},
                           	success : function (response) {
                                               if (response.status == "success") {
   												     $('#substage').html('');
                                                     $('#substage').html(response.html);
                                               }
                                           }
                  });	
   		
   		}
   		
		 e.run(); 
		 	
   });
   
    	$(document).ready(function(){   
   			$('body').on('click', '#custom_stage_submit', function(e){ 
   			
   				e.preventDefault(); 
				
   				var master_status	=	$('input[name="master_status"]:checked').val();
   				if(master_status == 8){
   										Swal.fire("Warning", 'Stage can not be update, please select other option !', "info");	
   				}else{
   							$.ajax({
   									url				: '{{route('service_company.job-applications.stage_submit')}}',
   									container		: '#status_frm',
   									type			: "POST",
   									data			: $('#status_frm').serialize(),
   									success			: function (response) {
   																			if(response.status == 'success'){
																					Swal.fire("Success", 'Application stage update successfully !', "success").then(function(){ 
																						// $('#stage_sub_stage').modal('hide');
                                            $("#stage_sub_stage_close").click();
																					});	
   																				 	var table;
              																		tableLoad('load');
																					table._fnDraw();
																					load_interviewSchedule();
   																			}
   									}
   							})  	
   				} 
   				
   						
   		});
   			e.run(); 
   		})
   
   		$(document).ready(function(){
			$('body').on('click', '#addServiceCandidate', function(){
			
							var url 				=	"{{ route('service_company.auth-custom-package.validatePackage') }}";
							var token 				=	"{{ csrf_token() }}";							
							var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
							$.ajax({
										type		: 'POST',
										url			: url,
										beforeSend	: function(){ },
										data		: {'_token': token, 'event':'CVACCESS'},
										success		: function (response){
																					if (response.status == "success"){  																					  																												                                                                                            	var url   			=	"{{url('service_company/job-applications/create/'.@$job->id.'/0')}}";
																								window.location 	=	url;
																						 }else if (response.status == "info"){   
																							sweetAlert("Fail!", response.message, "error");	
																						 }										 
																		}
									});
					
			});
				
	});
	
	
	
		$('.chk_list').click(function(){ 
				if($(".chk_list").prop('checked') == true){
					var chk_list 	=	$('.chk_list').val();
					$('.chk_list_item').attr('checked','checked');
				}else{
					$('.chk_list_item').removeAttr('checked');		
				}
		});	
		$('.chk_list').click(function(){
			if($(".chk_list").prop('checked') == true){
				var chk_list 	=	$('.chk_list').val();
				$('.fwd_resume').removeClass("d-none");
        $('.custom_hide').addClass("d-none");
				$('.send_email').removeClass("d-none");
				$('.app_copy').removeClass("d-none");
				$('.application_copy').removeClass("d-none");
			}else{
				$('.fwd_resume').addClass("d-none");
        $('.custom_hide').removeClass("d-none");
				$('.send_email').addClass("d-none");
				$('.app_copy').addClass("d-none");
				$('.application_copy').addClass("d-none");
			}
		 });	
		 table.on('click', '.chk_list_item', ()=>{
			   var i=0;	
			   var arr = [];
			   $('.chk_list_item:checked').each(function () {
				   arr.push($(this).val());
			   });
			   if(arr.length > 0){
					$('.fwd_resume').removeClass("d-none");
          $('.custom_hide').addClass("d-none");
					$('.send_email').removeClass("d-none");
					$('.app_copy').removeClass("d-none");
					$('.application_copy').removeClass("d-none");	
			   }else{
					$('.fwd_resume').addClass("d-none");
          $('.custom_hide').removeClass("d-none");
					$('.send_email').addClass("d-none");
					$('.app_copy').addClass("d-none");
					$('.application_copy').addClass("d-none"); 
			   }
		 });	
	
	
		$('.fwd_resume').click(function(){
	
						var id        	=	{{ $job->id }};
						var url       	=	"{{ route('service_company.cvtracker.fwd_resume',':id') }}";
						url         	=	url.replace(':id', id);
						var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
						var token       =	"{{ csrf_token() }}";
						var arr 		=	[];
						$('.chk_list_item:checked').each(function () {
								arr.push($(this).val());
						});
						$.ajax({
										type		: 'POST',
										url			: url,
										data		: {'_token': token, '_method': 'POST', 'arr' : arr},
										beforeSend	: function() {
																	//$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
										},
										success 	: function (response) {
														if (response.status == "success") {
															  $('#application_fwd_resume_body_content').html(response.view);
														}
											}
               				});
									
	});
	
</script>

<script>
	
	$('.application_copy').click(function(){	
			$('#kt_activitiesCopy_toggle').trigger('click');
						var id        	=	1;
						var url       	=	"{{ route('service_company.cvtracker.application_copy',':id') }}";
						url         	=	url.replace(':id', id);
						var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
						var token       =	"{{ csrf_token() }}";						
						var arr 		=	[];
						$('.chk_list_item:checked').each(function () {
								arr.push($(this).val());
						});	   
						$.ajax({
										type		: 'POST',
										url			: url,
										data		: {'_token': token, '_method': 'POST', 'arr' : arr},
										beforeSend	: function() {
																	//$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
										},
										success 	: function (response) {
														if (response.status == "success") {
															  $('#application_copy_body_content').html(response.view);
														}
											}
               				});									
	});	
	
</script>

@endpush 