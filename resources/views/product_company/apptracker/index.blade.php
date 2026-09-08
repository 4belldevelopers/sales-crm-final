@extends('layouts.product_company_layout')
@push('head-script')
<style>
#send_email .modal-dialog {margin: .3rem auto!important;}
#fwd_resume .modal-dialog {margin: .3rem auto!important;}
@media  only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr {width: max-content!important;}
  .table > :not(caption) > * > *
  { padding: 0rem; }
  td:nth-child(n+2),
  th:nth-child(n+2) { width: revert!important; }
}
@media  only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesApplication{ width: 1025px; }}
@media  only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesApplication { width: 1200px; }}
@media only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesCopy { width: 1025px; }}
@media only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesCopy { width: 1200px; } }
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
	$rc_path				=	url('/product_company/apptracker/similar_candidates/'.@$job->id);


	    $req_path				=	url('/product_company/apptracker/'.@$job->id.'/all');
		$public_bench_path		=	url('/product_company/apptracker/public_bench/'.@$job->id);
		$internal_bench_path	=	url('/product_company/apptracker/internal_bench/'.@$job->id);
		$existing_path			=	url('/product_company/apptracker/similar_candidates/'.@$job->id);


@endphp 

      <td class="d-flex align-items-center border-0">
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> 
			<a href="javascript:void(0);">
          		<div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
            </a> 
		</div>
        <div class="d-flex flex-column"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ strtoupper(substr(@$jobtitle, 0, 2)) }}</a> 
			<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">{{ ucwords(substr(@$jobclient->name, 0, 10)) }}</a> 
		</div>
      </td>

      <td>
	  	<div class="d-flex flex-column ml-2"> 
			<span class="badge fw-bolder fs-6 joined-bg px-2 py-1">{{ ucwords(substr(@$job->status, 0, 10)) }}</span> 
			<span class="text-muted fw-bold text-muted d-block fs-7">R-{{ @$job->id }}</span> 
		</div>
	  </td>
    
      <span class="h-20px border-gray-200 border-start mx-4"></span>

	  <div class="d-flex flex-column custom_hide">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ @$public_bench_path }}" class="text-dark text-hover-primary mb-1">Public Bench</a> </h5>
        <span class="text-gray-600">{{ @$public_bench_counter }}</span> 
      </div>
      <span class="h-20px border-gray-200 border-start mx-4 custom_hide"></span>
      <div class="d-flex flex-column custom_hide">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ @$internal_bench_path }}" class="text-dark text-hover-primary mb-1">Internal Bench</a> </h5>
        <span class="text-gray-600">{{ @$internal_bench_counter }}</span> 
      </div>
      <span class="h-20px border-gray-200 border-start mx-4 custom_hide"></span>
      <div class="d-flex flex-column custom_hide">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ @$existing_path }}" class="text-dark text-hover-primary mb-1">Existing</a> </h5>
        <span class="text-gray-600">{{ @$existing_counter }}</span> 
      </div>
      <span class="h-20px border-gray-200 border-start mx-4 custom_hide"></span>
      <div class="d-flex flex-column custom_hide">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ route('product_company.apptracker.candidates_request') }}" class="text-dark text-hover-primary mb-1">Candidates Request</a> </h5>
        <span class="text-gray-600">{{ @$recomnded_counter }}</span> 
      </div>

     
	  <!-- <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> 
			<a href="{{ @$rc_path }}" class="text-gray-800 text-hover-primary mb-1">Similar Candidates</a> 
		</h1>
        <span class="text-gray-600">0</span> 
	  </div> -->
    </div>

    <form action="" id="filter-form" style="">
      @csrf
      <input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
      <input type="hidden" name="mstatus_id" id="mstatus_id" value="{{ @$mstatus_id }}" />
      <div class="d-flex align-items-center py-1">
	  
		<a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 d-none fwd_resume" data-toggle="modal" data-target="#fwd_resume"> 
			<span class="svg-icon svg-icon-primary svg-icon-1x">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
				<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				<rect x="0" y="0" width="24" height="24"/>
				<path d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z" fill="#000000" opacity="0.3"/>
				<path d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z" fill="#000000"/>
				</g>
				</svg>
			</span> 
			Fwd Resume 
		</a>

		<a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 d-none send_email" data-toggle="modal" data-target="#send_email"> 
			<span class="svg-icon svg-icon-primary svg-icon-1x">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
				<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				<rect x="0" y="0" width="24" height="24"/>
				<path d="M4,16 L5,16 C5.55228475,16 6,16.4477153 6,17 C6,17.5522847 5.55228475,18 5,18 L4,18 C3.44771525,18 3,17.5522847 3,17 C3,16.4477153 3.44771525,16 4,16 Z M1,11 L5,11 C5.55228475,11 6,11.4477153 6,12 C6,12.5522847 5.55228475,13 5,13 L1,13 C0.44771525,13 6.76353751e-17,12.5522847 0,12 C-6.76353751e-17,11.4477153 0.44771525,11 1,11 Z M3,6 L5,6 C5.55228475,6 6,6.44771525 6,7 C6,7.55228475 5.55228475,8 5,8 L3,8 C2.44771525,8 2,7.55228475 2,7 C2,6.44771525 2.44771525,6 3,6 Z" fill="#000000" opacity="0.3"/>
				<path d="M10,6 L22,6 C23.1045695,6 24,6.8954305 24,8 L24,16 C24,17.1045695 23.1045695,18 22,18 L10,18 C8.8954305,18 8,17.1045695 8,16 L8,8 C8,6.8954305 8.8954305,6 10,6 Z M21.0849395,8.0718316 L16,10.7185839 L10.9150605,8.0718316 C10.6132433,7.91473331 10.2368262,8.02389331 10.0743092,8.31564728 C9.91179228,8.60740125 10.0247174,8.9712679 10.3265346,9.12836619 L15.705737,11.9282847 C15.8894428,12.0239051 16.1105572,12.0239051 16.294263,11.9282847 L21.6734654,9.12836619 C21.9752826,8.9712679 22.0882077,8.60740125 21.9256908,8.31564728 C21.7631738,8.02389331 21.3867567,7.91473331 21.0849395,8.0718316 Z" fill="#000000"/>
				</g>
				</svg>
			</span> 
			Send Email 
		</a>

		<a href="javascript:void(0);"  type="button" class="btn btn-sm btn-flex btn-light-primary fw-bolder me-3 d-none application_copy" data-toggle="modal" data-target="#application_copy">
			<span class="svg-icon svg-icon-primary svg-icon-1x">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
				<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				<rect x="0" y="0" width="24" height="24"/>
				<path d="M6,9 L6,15 C6,16.6568542 7.34314575,18 9,18 L15,18 L15,18.8181818 C15,20.2324881 14.2324881,21 12.8181818,21 L5.18181818,21 C3.76751186,21 3,20.2324881 3,18.8181818 L3,11.1818182 C3,9.76751186 3.76751186,9 5.18181818,9 L6,9 Z" fill="#000000" fill-rule="nonzero"/>
				<path d="M10.1818182,4 L17.8181818,4 C19.2324881,4 20,4.76751186 20,6.18181818 L20,13.8181818 C20,15.2324881 19.2324881,16 17.8181818,16 L10.1818182,16 C8.76751186,16 8,15.2324881 8,13.8181818 L8,6.18181818 C8,4.76751186 8.76751186,4 10.1818182,4 Z" fill="#000000" opacity="0.3"/>
				</g>
				</svg>
			</span>
			Copy 
		</a>

        <div class="d-flex align-items-center position-relative my-1 me-3 custom_hide">
          <span class="svg-icon svg-icon-1 position-absolute ms-6">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
          </svg>
          </span>
          <input type="text" id="inputSearch" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
        </div>
        <div class="card-toolbar">
          <div class="d-flex justify-content-end custom_hide" data-kt-user-table-toolbar="base">
			
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

<a href="javascript:void(0);">
<button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" id="addProductCandidate">
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

@endif 
			
			</div>
          <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
            <div class="fw-bolder me-5"> <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
            <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
          </div>
         <div class="modal fade" id="kt_modal_export_users" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered mw-650px">
              <div class="modal-content">
                <div class="modal-header">
                  <h2 class="fw-bolder">Export Users</h2>
                  <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                    <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                      <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                    </svg>
                    </span>
                  </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                  <div class="fv-row mb-10">
                    <label class="fs-6 fw-bold form-label mb-2">Select Roles:</label>
                    <select name="role" data-control="select2" data-placeholder="Select a role" data-hide-search="true" class="form-select form-select-solid fw-bolder">
                      <option></option>
                      <option value="Administrator">Administrator</option>
                      <option value="Analyst">Analyst</option>
                      <option value="Developer">Developer</option>
                      <option value="Support">Support</option>
                      <option value="Trial">Trial</option>
                    </select>
                  </div>
                  <div class="fv-row mb-10">
                    <label class="required fs-6 fw-bold form-label mb-2">Select Export Format:</label>
                    <select name="format" data-control="select2" data-placeholder="Select a format" data-hide-search="true" class="form-select form-select-solid fw-bolder">
                      <option></option>
                      <option value="excel">Excel</option>
                      <option value="pdf">PDF</option>
                      <option value="cvs">CVS</option>
                      <option value="zip">ZIP</option>
                    </select>
                  </div>
                  <div class="text-center">
                    <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                    <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  

</div>
<!--end::Toolbar-->
@php 

               $sourcing_bg = $screening_bg = $interview_bg= $negotation_bg = $offer_bg = $joining_bg = $rejected_bg  = $All_candidate_bg = 'alert bg-gray text-center circle_boxes';   
               $sourcing_text = $screening_text = $interview_text = $negotation_text = $offer_text = $joining_text = $rejected_text = $All_candidate_text = 'text-center color-all'; 
               if(!empty(@$candStatusClass['Index']))
               {
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
			
			$allCandidate_value		=	$sourcing_value	=	$screening_value	=	$interview_value	=	$negotiation_value	=	$offered_value	=	$joined_value	=	$rejected_value	=	0;
			
			if(@$applicationCounter->allCandidate > 0){
				$allCandidate_value		=	@$applicationCounter->allCandidate;	
			}
			if(@$applicationCounter->Sourcing > 0 ){
				$sourcing_value	=	@$applicationCounter->Sourcing;	
			}
			if(@$applicationCounter->Screening > 0){
				$screening_value	=	@$applicationCounter->Screening;		
			}
			if(@$applicationCounter->Interview > 0){
				$interview_value	=	@$applicationCounter->Interview;	
			}
			if(@$applicationCounter->Negotiation > 0){
				$negotiation_value	=	@$applicationCounter->Negotiation;	
			}
			if(@$applicationCounter->Offered > 0){
				$offered_value	=	@$applicationCounter->Offered;	
			}
			if(@$applicationCounter->Joined > 0){
				$joined_value	=	@$applicationCounter->Joined;	
			}
			if(@$applicationCounter->Rejected > 0){
				$rejected_value	=	@$applicationCounter->Rejected;	
			}
		        
@endphp 

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="d-flex justify-content-space-around align-items-center custm-bg"> 
		<a class="d-block text-white f-17 font-weight-bolder application_sttus_nav application_sttus_nav" href="Javascript:void(0);" rel="all">
			<div class="total_jobs mr-2">
				<div class=" {{ @$tr_class }} text-center squre_box" role="alert" id="bg_all"> <span class="total-content-view">
				<i class="flaticon2-group text-white fa-sm" aria-hidden="true"></i> <span id="totalcnt"> 0 </span> </span> </div>
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
			  <h6 class="tx text-center color-all font-weight-600 {{ $negotation_text }}" id="tx_4">Negotiation</h6> 
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
        <div class="card-body pt-3 px-5"> @if(in_array("view_job_applications", $userPermissions))
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3"><input class="form-check-input chk_list" type="checkbox" value="1" /> </div></th>
                <th class="">Name</th>
                <th class="">Contacts</th>
                <th class="">Exp.</th>
                <th class="">Salary</th>
                <th class="" data-toggle="tooltip" title="Notice Period">NP</th>
                <th class="">Qualifaction</th>
                <th class="">Interview</th>
                <th class="">Location</th>
                <th class="">Source</th>
                <th class="">Stage</th>
              </tr>
            </thead>
          </table>
          @endif </div>
      </div>
    </div>
  </div>
</div>
<!--end::Content-->


<!--begin::Stage sub stage popup-->
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
<!--end::Stage sub stage popup-->

<div id="kt_activitiesCopy_toggle"></div>
<div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle" data-kt-drawer-close=".kt_activitiesCopy_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Copy Applicant </h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5 kt_activitiesCopy_close" id="kt_activitiesCopy_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="application_copy_body_content"></div>
  </div>
</div>

 <!--begin::Fwd Resume - Modal-->
<div class="modal fade" id="fwd_resume" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Forward Resume</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
        </div>
      </div>
      <div class="card-body position-relative px-0 py-0" id="application_fwd_resume_body_content"></div>
    </div>
  </div>
</div>
<!--end::Fwd Resume - Modal-->

<div id="kt_activitiesBA_toggle"></div>
<div id="kt_activitiesBA" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesBA_toggle" data-kt-drawer-close="#kt_activitiesBA_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Relationship Manager Listing</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesBA_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesBA_body"> </div>
  </div>
</div>


<!--begin::Send Email - Modal-->
	<div class="modal fade" id="send_email" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
        <!--begin::Title-->
        <h2>Send Email</h2>
        <!--end::Title-->
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Form-->
      <form class="form fv-plugins-bootstrap fv-plugins-framework" id="" method="post">
        <!--begin::Modal body-->
        <div class="modal-body scroll-y py-0 px-lg-7">
          <div data-kt-element="options">
            <div class="py-5">
              <!--begin::Template-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label>
                <div class="col-lg-10 col-xl-10">
                  <select name="template" class="form-select form-select-solid" aria-label="Select template">
                    <option value="">Select Email Template...</option>
                    <option value="1">Email Template - 1</option>
                    <option value="2">Email Template - 2</option>
                    <option value="3">Email Template - 3</option>
                    <option value="3">Email Template - 4</option>
                    <option value="3">Email Template - 5</option>
                  </select>
                  <span class="form-text text-muted">Please select email template</span> </div>
              </div>
              <!--end::Template-->
              <!--begin::From email-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="from_email" id="" placeholder="Enter email" value="sonika@test.com" >
                  <span class="form-text text-muted">Please enter email</span> </div>
              </div>
              <!--end::From email-->
              <!--begin::To email-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="to_email" id="" placeholder="Enter email" value="amit@test.com" >
                  <span class="form-text text-muted">Please enter email</span> </div>
              </div>
              <!--end::To email-->
              <!--begin::CC email-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">CC</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="cc_email" id="" placeholder="Enter email" value="abu@test.com" >
                  <span class="form-text text-muted">Please enter email</span> </div>
              </div>
              <!--end::CC email-->
              <!--begin::Subject-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
                <div class="col-lg-10 col-xl-10">
                  <input class="form-control form-control-solid" name="subject" id="" placeholder="Enter subject" value="" >
                  <span class="form-text text-muted">Please enter subject</span> </div>
              </div>
              <!--end::Subject-->
              <!--begin::Body-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                <div class="col-lg-10 col-xl-10">
                  <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target">
                      <h1>Quick and Simple TinyMCE 5 Integration</h1>
                      <p>Here goes the&nbsp;<a href="#">Minitial content of the editor</a>. Lorem Ipsum is simply dummy text of the&nbsp;<em>printing and typesetting</em>&nbsp;industry.</p>
                      <blockquote>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled. Lorem Ipsum is simply dummy text of the printing and typesetting industry.</blockquote>
                      <h3 style="text-align: right;">Flexible &amp; Powerful</h3>
                      <p style="text-align: right;"><strong>Lorem Ipsum has been the industry's</strong>&nbsp;standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</p>
                      <ul>
                          <li>List item 1</li>
                          <li>List item 2</li>
                          <li>List item 3</li>
                          <li>List item 4</li>
                      </ul>
                  </textarea>
                  <textarea class="form-control form-control-solid" row="10" placeholder="TinyMCE editor is not working...!"></textarea>
                  <span class="form-text text-muted">Please enter email content</span> </div>
              </div>
              <!--end::Body-->
              <!--begin::Upload Files-->
              <div class="form-group row mb-4">
                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Upload Files</label>
                <div class="col-xl-10 col-lg-10">
                  <!--begin::Dropzone-->
                  <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
                    <!--begin::Controls-->
                    <div class="dropzone-panel mb-lg-0 mb-2"> <a class="dropzone-select btn btn-sm btn-primary me-2">Attach files</a> <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload All</a> <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remove All</a> </div>
                    <!--end::Controls-->
                    <!--begin::Items-->
                    <div class="dropzone-items wm-200px">
                      <div class="dropzone-item" style="display:none">
                        <!--begin::File-->
                        <div class="dropzone-file">
                          <div class="dropzone-filename" title="some_image_file_name.jpg"> <span data-dz-name="">some_image_file_name.jpg</span> <strong>( <span data-dz-size="">340kb</span>)</strong> </div>
                          <div class="dropzone-error" data-dz-errormessage=""></div>
                        </div>
                        <!--end::File-->
                        <!--begin::Progress-->
                        <div class="dropzone-progress">
                          <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                          </div>
                        </div>
                        <!--end::Progress-->
                        <!--begin::Toolbar-->
                        <div class="dropzone-toolbar"> <span class="dropzone-start"> <i class="bi bi-play-fill fs-3"></i> </span> <span class="dropzone-cancel" data-dz-remove="" style="display: none;"> <i class="bi bi-x fs-3"></i> </span> <span class="dropzone-delete" data-dz-remove=""> <i class="bi bi-x fs-1"></i> </span> </div>
                        <!--end::Toolbar-->
                      </div>
                    </div>
                    <!--end::Items-->
                  </div>
                  <!--end::Dropzone-->
                  <!--begin::Hint-->
                  <span class="form-text text-muted">Max file size is 1MB and max number of files is 5.</span>
                  <!--end::Hint-->
                </div>
              </div>
              <!--end::Upload Files-->
            </div>
          </div>
        </div>
        <!--begin::Modal body-->
        <!--begin::Modal footer-->
        <div class="modal-footer flex-center">
          <div class="div1">
            <!--begin::Cancel-->
            <button type="close" id="" class="btn btn-light-dark me-3" data-dismiss="modal">Cancel</button>
            <!--begin::Send Email-->
            <button type="button" id="" class="btn btn-light-primary"> <span class="indicator-label">Send Email</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
            <!--end::Send Email-->
          </div>
          <!--<span style="position: absolute;right: 2%;"></span>-->
          <div class="div2 position-absolute end-0 me-5 d-none"> </div>
        </div>
        <!--end::Modal footer-->
      </form>
      <!--begin::Form-->
    </div>
    <!--end::Modal content-->
  </div>
</div>
<!--end::Send Email - Modal--> 




@endsection
@push('footer-script')

<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}{{ TM_VERSION }}"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}{{ TM_VERSION }}"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('js/schedule-calendar.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}{{ TM_VERSION }}" type="text/javascript"></script>
<script>

		$('body').on('click', '#AddCandidate', function(){
			var url					=	'{{ route('product_company.job-applications.create') }}';
			window.location.href 	=	url;			
		});
		  
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
        var url   	= "{!! url('/product_company/apptracker/') !!}";
        url     	= url+'/'+jobid+'/'+page;
        window.history.pushState('page2', 'Title', url);
        tableLoad('load');
     })});
</script>

<script>
   		
$(document).ready(function() { 
      $('#inputSearch').keyup(function() {
          tableLoad('filter');   
      });
});
    
      
           $('#start-date').datepicker({format: 'yyyy-mm-dd'})
           $('#end-date').datepicker({
               format: 'yyyy-mm-dd'
           })
   
           var table;
           tableLoad('load');
         
   
       	   function tableLoad(type) {
           
										var jobid          		= $('#jobid').val();
										var mstatus_id        	= $('#mstatus_id').val();
										var inputSearch       	= $('#inputSearch').val();
        
     									table 					= $('#myTable').dataTable({
      
												responsive     	: true, 
												processing      : true,
												ordering		: false, //disable ordering      			
												"bLengthChange"   : false,   
												"lengthMenu"      : [[10, 25, 50, 100], [10, 25, 50, 100]],
												sDom			: 'lrtip',       		  
												ajax			: '{!! route('product_company.apptracker.data') !!}?jobid='+jobid+'&mstatus_id='+mstatus_id+'&search='+inputSearch,

               									"fnDrawCallback" : function (oSettings) {
                       										$("body").tooltip({	selector: '[data-toggle="tooltip"]' });
															
															var url       		= '{{ route('product_company.apptracker.loadcounter', [':jobid', ':mstatus_id', ':inputSearch']) }}';
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
						{data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '15%'},						
						{data: 'Exp', name: 'Exp', searchable : false, orderable : false,  width: '5%'},
						{data: 'Salary', name: 'Salary',  searchable : false, orderable : false,  width: '10%'},   

						{data: 'NoticePeriod', name: 'NoticePeriod', searchable : false, orderable : false},
						{data: 'Qualifaction', name: 'Qualifaction',  searchable : false, orderable : false},
												
						{data: 'Interview', name: 'Interview', orderable: false},
						{data: 'Location', name: 'Location', orderable : false},   
						{data: 'Source', name: 'Source', orderable: false},
						{data: 'Stage', name: 'Stage', orderable: false}
					]         
      
             }); 
           new $.fn.dataTable.FixedHeader(table);
		   } 
		   
	 		table.on('click', '.stage_sub_stage', function ()   
      		{
					   var id       = $(this).data('row-id');			   
					   var url    	= "{{ route('product_company.job-applications.stage_body_content',':id') }}";
					   url      	= url.replace(':id', id);
					   $.ajax({
									type  : 'GET',
									url   : url,
									success : function (response) {
													if (response.status == "success") 
													{
														  $('#stage_body_content').html(response.view);
													}
												}
					   });
       		}); 
		   	   
      	   
      
    
     
      		
	
</script>


<script>

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
			 
</script>	

<script>
	
	$('.application_copy').click(function(){
	
			$('#kt_activitiesCopy_toggle').trigger('click');
						var id        	=	1;
						var url       	=	"{{ route('product_company.cvtracker.application_copy',':id') }}";
						url         	=	url.replace(':id', id);
						var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
						var token       =	"{{ csrf_token() }}";
						
						var arr = [];
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


<script>

	$('.fwd_resume').click(function(){
	
						var id        	=	{{ $job->id }};
						var url       	=	"{{ route('product_company.cvtracker.fwd_resume',':id') }}";
						url         	=	url.replace(':id', id);
						var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
						var token       =	"{{ csrf_token() }}";
						
						var arr = [];
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

	$('body').on('click', '.master_status_div', function(e){
   
   		e.preventDefault(); 
		
   		var id 	= $(this).data('master-status-id');
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
                  var url    		= "{{ route('product_company.job-applications.child_stage',':id') }}";
                  url      		= url.replace(':id', id);
                  $.ajax({
                           	type  : 'GET',
                           	url   : url,
   							beforeSend	: function() {
                                     					$('#substage').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       		},
                           	success : function (response) {
                                               if (response.status == "success") 
                                     			{
   												  $('#substage').html('');
                                                     $('#substage').html(response.html);
                                               }
                                           }
                  });	
   		
   		}
   		e.run();	
   });
   
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>	
<script>
	$(document).ready(function(){
	
				$('body').on('click', '#addProductCandidate', function(){
																	
							var url 				=	"{{ route('product_company.auth-custom-package.validatePackage') }}";
							var token 				=	"{{ csrf_token() }}";							
							var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
							$.ajax({
										type		: 'POST',
										url			: url,
										beforeSend	: function(){ },
										data		: {'_token': token, 'event':'CVACCESS'},
										success		: function (response){
																					if (response.status == "success"){  
                                                                                            var url   		=	'<?php echo url('product_company/job-applications/create/'.@$job->id.'/0'); ?>';
																							window.location = url;
																					}else if (response.status == "info"){   																							
																							sweetAlert("Fail!", response.message, "error");
																						 }
																												 
																			}
									});
				});
	});
</script>


<script>

	$(document).ready(function(){
   
   		$('body').on('click', '#int_stage_submit', function(e){
   			
   				e.preventDefault(); 
				
   				var master_status	=	$('input[name="master_status"]:checked').val();
   				if(master_status == 8){
   										Swal.fire("Warning", 'Inreview can not be update, please select other option !', "info");	
   				}else{
   							$.ajax({
   									url				: '{{route('product_company.job-applications.stage_submit')}}',
   									container		: '#status_frm',
									

beforeSend: function() {
	swal.fire({
		html: '<h5>Stage updating Process...</h5>',
		showConfirmButton: false,
		onRender: function() {
			 $('.swal2-content').prepend(sweet_loader);
		}
	});
},
									
									
   									type			: "POST",
   									data			: $('#status_frm').serialize(),
   									success			: function (response) {
   																			if(response.status == 'success'){
   																				Swal.fire("Success", 'Application stage update successfully !', "success");	
   																				//table._fnDraw();
   																				 	var table;
              																			tableLoad('load');
   																			}

   									}
   							}); 		
   				} 				
   		});
   		e.run();
		
   })
     $(document).ready(function(){
   
   		$('body').on('click', '#custom_stage_submit', function(e){ 
   			
   				e.preventDefault(); 
				
   				var master_status	=	$('input[name="master_status"]:checked').val();
   				if(master_status == 8){
   										Swal.fire("Warning", 'Stage can not be update, please select other option !', "info");	
   				}else{
   							$.ajax({
   									url				: '{{route('product_company.job-applications.stage_submit')}}',
   									container		: '#status_frm',
   									type			: "POST",
   									data			: $('#status_frm').serialize(),
   									success			: function (response) {
																			//load_interviewSchedule();
   																			if(response.status == 'success'){
   																				Swal.fire("Success", 'Application stage update successfully !', "success").then(function(){ 
																					// $('#stage_sub_stage').modal('hide');
																					$("#stage_sub_stage_close").click();
																				});	
   																				//table._fnDraw();
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
   
</script>

@endpush 